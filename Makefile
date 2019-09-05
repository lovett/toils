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
build:
	rsync -av --cvs-exclude \
	--delete \
	--delete-excluded \
	--exclude=.ackrc \
	--exclude=.env \
	--exclude=.env.example \
	--exclude=.gitattributes \
	--exclude=.gitignore \
	--exclude=ansible \
	--exclude=node_modules \
	--exclude=vendor \
	--exclude=bootstrap/cache/* \
	--exclude=storage/* \
	--exclude=tests \
	--exclude=package.json \
	--exclude=package-lock.json \
	--exclude=phpcs.xml \
	--exclude=phpmd.xml \
	--exclude=phpunit.xml \
	--exclude=Makefile \
	--exclude=mkimage.sh \
	--exclude=server.php \
	--exclude=*.sqlite \
	--exclude=webpack.mix.js \
	--exclude=*.qcow2 \
	./ build

# Build a QEMU virtual machine to run the application in production mode
build-image: build
	rm -f toils.qcow2
	virt-builder debian-10 \
		--format qcow2 \
		-o toils.qcow2 \
		--size 6G \
		--hostname toils.local \
		--root-password password:toils \
		--run-command 'sed -i "s/ens2/ens3/" /etc/network/interfaces' \
		--run-command 'mkdir -p /var/www' \
		--run-command 'mkdir -p /etc/nginx/sites-enabled' \
		--run-command 'mkdir -p /etc/php/7.3/fpm/pool.d' \
		--copy-in resources/qemu/toils-vhost.conf:/etc/nginx/sites-enabled \
		--copy-in resources/qemu/toils-pool.conf:/etc/php/7.3/fpm/pool.d \
		--uninstall 'man-db' \
		--firstboot resources/qemu/toils-firstboot.sh
		virt-copy-in -a toils.qcow2 build /var/www

# Run the QEMU virtual machine locally
run-image:
	qemu-system-x86_64 -m 512M \
		-accel kvm \
		-nic user,hostfwd=tcp::$(LOCAL_PORT)-:80,hostfwd=tcp::2222-:22
		-nographic \
		toils.qcow2
