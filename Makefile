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
setup-js: export NPM_CONFIG_PROGRESS=false
setup-js: export NO_UPDATE_NOTIFIER=1
setup-js:
	npm install

# Install Composer packages quietly based on composer.lock
setup-php:
	composer --no-interaction --no-ansi --no-suggest install

# Install all packages quietly.
setup: setup-php setup-js .env

# Check for out-of-date npm packages
outdated-js: export NO_UPDATE_NOTIFIER=1
outdated-js:
	npm outdated || true

# Check for out-of-date composer packages
outdated-php:
	composer show --outdated --direct

# Check for all out-of-date packages
outdated: outdated-php outdated-js

# Install newly updated npm packages.
update-js: export NO_UPDATE_NOTIFIER=1
update-js: export DISABLE_OPENCOLLECTIVE=true
update-js:
	npm install

# Install newly updated composer packages and update composer.lock
update-php:
	composer update

# Update all packages quietly
update: update-php update-js

# Create a package upgrade commit.
#
# "puc" stands for Package Upgrade Commit
puc: dummy
	git checkout master
	git add package.json package-lock.json composer.json composer.lock
	git commit -m "Upgrade npm and composer packages"

workspace:
# 0: Editor
	tmux new-session -d -s "$(PROJECT_NAME)" bash
	tmux send-keys -t "$(PROJECT_NAME)" "$(EDITOR) ." C-m

# 1: Shell
	tmux new-window -a -t "$(PROJECT_NAME)" bash

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
install:
	ansible-playbook ansible/install.yml


# Build the application in preparation for a production deployment
build: export DISABLE_OPENCOLLECTIVE=true
build: export COMPOSER_NO_INTERACTION=1
build: dummy
	rm -f toils.tar.gz
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
	--exclude=.tar \
	./ build
	cd build && mkdir -p storage/framework/views
	cd build && touch storage/$(SQLITE_DB_NAME)
	cd build && npm ci && npm run production
	cd build && composer install --no-dev --no-suggest --quiet --classmap-authoritative
	cd build && rm composer.lock composer.json package.json package-lock.json webpack.mix.js
	tar --create --gzip --file=toils.tar.gz --exclude=node_modules --exclude=storage --transform s/build/toils/ build
