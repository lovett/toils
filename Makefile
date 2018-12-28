.PHONY: dummy

SQLITE_DB_NAME := toils.sqlite
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

# Create a package upgrade commit.
#
# "puc" stands for Package Upgrade Commit
puc: dummy
	git checkout master
	git add package.json package-lock.json composer.json composer.lock
	git commit -m "Upgrade npm and composer packages"
