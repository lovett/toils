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
update-js:
	npm update

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

# Launch a single-use container to run the site.
#
# A webserver on the container host should define a vhost for the site
# and proxy requests into the container.
server: .env
	podman run \
	--rm \
	--name=$(PROJECT_NAME) \
	--publish=127.0.0.1:$(LOCAL_PORT):80 \
	--volume="$(PWD):/srv/www" \
	localhost/nginx-php

# Build a production image for the application
#
# The script is wrapped by a call to buildah unshare because it
# involves a mounting the filesystem of the container onto the
# host. See the manpage for buildah-unshare for details.
image: dummy
	buildah unshare ./mkimage.sh

imagetest: dummy
	podman run \
	--rm \
	--name=$(PROJECT_NAME) \
	--publish=127.0.0.1:$(LOCAL_PORT):80 \
	--volume="$(PWD)/storage:/srv/www/storage" \
	--conmon /usr/lib/podman/bin/conmon \
	localhost/$(PROJECT_NAME)

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
