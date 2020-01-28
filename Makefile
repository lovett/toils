.PHONY: dummy

SQLITE_DB_NAME := toils.sqlite
PROJECT_NAME := toils
LOCAL_PORT := 8086

seed-user: dummy
	rm -f $(SQLITE_DB_NAME)
	touch $(SQLITE_DB_NAME)
	php artisan migrate
	php artisan db:seed --class=UserFakeSeeder

seed-full: dummy
	rm -f $(SQLITE_DB_NAME)
	touch $(SQLITE_DB_NAME)
	php artisan migrate
	php artisan db:seed --class=FullFakeSeeder

lint: dummy
	phpmd app text phpmd.xml
	phpcs -s app
	php artisan code:analyse

# Install NPM packages quietly.
#
# Warnings about fsevents are filtered because they are irrelevant.
#
# A warning about jquery being an unmet peer dependency of bootstrap
# is ignored because jQuery is deliberately not being used.
#
# Auditing is disabled because it isn't useful for the purposes of
# this project.
setup-js: export NPM_CONFIG_PROGRESS=false
setup-js:
	npm --no-update-notifier --no-audit --no-fund install 2>&1 \
	| grep -v fsevents \
	| grep -v "peer of jquery"

# Install Composer packages quietly based on composer.lock
setup-php:
	composer --no-interaction --no-ansi --no-suggest install

# Install all packages quietly.
setup: setup-php setup-js .env

# Check for out-of-date npm packages
outdated-js:
	npm --no-update-notifier outdated || true

# Check for out-of-date composer packages
outdated-php:
	composer show --outdated --direct

# Check for all out-of-date packages
outdated: outdated-php outdated-js

# Install newly updated composer packages and update composer.lock
update-php:
	composer update

# Update all packages quietly
update: update-php setup-js

# Create a package upgrade commit.
#
# "puc" stands for Package Upgrade Commit
puc: dummy
	git checkout master
	git add package.json package-lock.json composer.json composer.lock
	git commit -m "Upgrade npm and composer packages"

workspace:
# 0: Editor
	tmux new-session -d -s "$(PROJECT_NAME)" "$$SHELL"
	tmux send-keys -t "$(PROJECT_NAME)" "$(EDITOR) ." C-m

# 1: Shell
	tmux new-window -a -t "$(PROJECT_NAME)" "$$SHELL"

# 2: Webpack
	tmux new-window -a -t "$(PROJECT_NAME)" -n "webpack" "npm run watch"

# 3: Dev server
	tmux new-window -a -t "$(PROJECT_NAME)" -n "devserver" "php artisan serve --host 0.0.0.0 --port $(LOCAL_PORT)"
	tmux select-window -t "$(PROJECT_NAME)":0
	tmux attach-session -t "$(PROJECT_NAME)"

# Set up the application configuration file
#
# Most settings from the example file are usable as-is, but the
# APP_KEY in particular needs special handling.
.env:
	cp .env.example .env
	php artisan key:generate


# Install the application on the production host via Ansible
#
# The archive is only deleted if the installation succeeds so that
# the tar file isn't recreated unnecessarily.
install: toils.tar.gz
	ansible-playbook ansible/install.yml
	rm toils.tar.gz


# Build the application in preparation for a production deployment
#
# Normally invoked from the install target, not directly.
toils.tar.gz: export DISABLE_OPENCOLLECTIVE=true
toils.tar.gz: export COMPOSER_NO_INTERACTION=1
toils.tar.gz:
	rsync -a --cvs-exclude \
	--delete \
	--exclude=.ac-php-conf.json \
	--exclude=.ackrc \
	--exclude=.env \
	--exclude=.env.example \
	--exclude=.gitattributes \
	--exclude=.gitignore \
	--exclude=ansible \
	--exclude=node_modules \
	--exclude=vendor \
	--exclude=bootstrap/cache/* \
	--exclude=build \
	--exclude=storage \
	--exclude=tests \
	--exclude=phpcs.xml \
	--exclude=phpmd.xml \
	--exclude=phpunit.xml \
	--exclude=Makefile \
	--exclude=server.php \
	--exclude=*.sqlite \
	./ build
	cd build && mkdir -p storage/framework/views
	cd build && touch storage/$(SQLITE_DB_NAME)
	cd build && npm ci && npm run production
	cd build && composer install --no-dev --no-suggest --quiet --classmap-authoritative
	tar --create --gzip --file=toils.tar.gz --exclude=node_modules --exclude=storage --transform s/build/toils/ build

# Generate a favicon with multiple sizes.
favicon: dummy
	convert -density 900 -background none -geometry 180x180 public/toils.svg public/toils.png
	convert -density 900 -background none -geometry 48x48 public/toils.svg temp-48.png
	convert -density 900 -background none -geometry 32x32 public/toils.svg temp-32.png
	convert -density 900 -background none -geometry 16x16 public/toils.svg temp-16.png
	convert temp-16.png temp-32.png temp-48.png public/favicon.ico
	rm temp-48.png temp-32.png temp-16.png
	optipng -quiet -o 3 public/*.png
