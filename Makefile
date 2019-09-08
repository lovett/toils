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
	rsync -a --cvs-exclude \
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
	--exclude=storage \
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
	mkdir -p build/storage/framework/views
	touch build/storage/$(SQLITE_DB_NAME)
	cd build && composer install \
		--no-dev \
		--no-suggest \
		--quiet \
		--no-interaction \
		--optimize-autoloader \
		--classmap-authoritative
	echo 'APP_KEY=' > build/.env
	cd build && php artisan key:generate
	cat $(HOME)/Documents/secrets/toils >> build/.env
	cd build && php artisan config:cache
	sed -i 's|$(PWD)/build|/mnt/toils-app|' build/bootstrap/cache/config.php
	rm -rf build/storage
	cd build && ln -sf /mnt/toils-storage storage


# Build a QEMU virtual machine to run the application in production mode
image: toils-app.img toils-storage.qcow2 toils-os.qcow2

# Build a QEMU image for the OS.
toils-os.qcow2:
	virt-builder debian-10 \
		--format qcow2 \
		-o toils-os.qcow2 \
		--size 6G \
		--hostname toils.local \
		--root-password password:toils \
		--append-line /etc/fstab:'LABEL=toils-app /mnt/toils-app ext4 defaults,noatime,noexec 0 0' \
		--append-line /etc/fstab:'LABEL=toils-storage /mnt/toils-storage ext4 defaults,noatime,noexec 0 0' \
		--run-command 'mkdir -p /etc/nginx/sites-enabled' \
		--run-command 'mkdir -p /etc/php/7.3/fpm/pool.d' \
		--run-command 'mkdir /mnt/toils-app' \
		--run-command 'mkdir /mnt/toils-storage' \
		--copy-in resources/qemu/toils-vhost.conf:/etc/nginx/sites-enabled \
		--copy-in resources/qemu/toils-pool.conf:/etc/php/7.3/fpm/pool.d \
		--copy-in resources/qemu/toils-setup.sh:/usr/local/sbin \
		--copy-in resources/qemu/toils-setup.service:/etc/systemd/system \
		--uninstall man-db \
		--run-command 'apt autoremove -y' \
		--firstboot resources/qemu/toils-firstboot.sh

# Build a QEMU image separate from the OS image for the application files.
# Without the extra 10M, the image runs out of space.
toils-app.img: build
	virt-make-fs --format=raw --size=+10M --type=ext4 --label=toils-app build toils-app.img

# Build a QEMU image separate from the OS image to store volatile data.
toils-storage.qcow2:
	qemu-img create -f qcow2 toils-storage.qcow2 50G
	guestfish add toils-storage.qcow2 : \
		run : \
		mkfs ext4 /dev/sda : \
		set-label /dev/sda toils-storage : \
		mount /dev/sda / : \
		mkdir-p /app/public : \
		mkdir-p /framework/views : \
		mkdir /fonts : \
		mkdir /logs : \
		touch /$(SQLITE_DB_NAME)

# Run the QEMU virtual machine locally
testrun:
	qemu-system-x86_64 -m 256M \
		-accel kvm \
		-nic user,hostfwd=tcp::$(LOCAL_PORT)-:80 \
		-nographic \
		-drive file=toils-os.qcow2,index=0,media=disk,format=qcow2 \
		-drive file=toils-app.img,index=1,media=disk,format=raw \
		-drive file=toils-storage.qcow2,index=2,media=disk,format=qcow2

shrink:
	qemu-img convert -O qcow2 toils-storage.qcow2 toils-storage-shrunk.qcow2
	mv toils-storage-shrunk.qcow2 toils-storage.qcow2

clean:
	rm -f toils-os.qcow2
	rm -f toils-app.img
	rm -f toils-storage.qcow2
	rm -rf build
