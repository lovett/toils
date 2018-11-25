.PHONY: dummy

fakeseed: dummy
	php artisan migrate:fresh
	php artisan db:seed --class=FakeSeeder

lint: dummy
	phpmd app text phpmd.xml

# Install NPM packages quietly.
#setup-js: export NPM_CONFIG_PROGRESS = false
setup-js: dummy
	npm install

# Install Composer packages quietly.
setup-php: dummy
	composer --no-interaction --no-ansi update

# Install all packages quietly.
setup: setup-php setup-js

# Check for out-of-date npm packages
outdated-js:
	npm outdated || true

# Check for out-of-date composer packages
outdated-php:
	composer show --outdated --direct

# Check for all out-of-date packages
outdated: outdated-php outdated-js
