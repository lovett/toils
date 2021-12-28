.PHONY: dummy

SQLITE_DB_NAME := toils.sqlite
PROJECT_NAME := toils
LOCAL_PORT := 8086
COMPOSER_COMMAND := composer install --no-dev --no-suggest --quiet --classmap-authoritative --no-progress

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
	phpstan analyze

# Install NPM packages quietly.
setup-js:
	DISABLE_OPENCOLLECTIVE=1 npm install

# Install Composer packages quietly based on composer.lock
setup-php: storage/toils.sqlite
	composer --no-interaction --no-ansi --no-suggest --no-progress install

# Install all packages quietly.
setup: setup-php setup-js .env

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


storage/toils.sqlite:
	touch storage/toils.sqlite

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
toils.tar.gz: export DISABLE_OPENCOLLECTIVE=1
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
ifeq "$(shell php -r 'echo PHP_MAJOR_VERSION;')" "7"
	cd build && $(COMPOSER_COMMAND)
else
	podman run \
	--rm \
	--interactive \
	--tty \
	--env COMPOSER_ALLOW_SUPERUSER=1 \
	-v $(PWD):/usr/src/toils:Z,rw \
	-w /usr/src/toils \
	php74-composer $(COMPOSER_COMMAND)
endif
	tar --create --gzip --file=toils.tar.gz --exclude=node_modules --exclude=storage --transform s/build/toils/ build

# Push the repository to GitHub.
mirror:
	git push --force git@github.com:lovett/toils.git master:master
