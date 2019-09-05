#!/bin/sh

# This script runs from inside the QEMU virtual machine the first time
# it is run.
#
# It configures the system to run PHP-FPM and Nginx, and finishes
# installation of the application.
#
# At the time this script runs, the virtual machine OS is mostly
# pristine. Packages haven't been installed before now to avoid
# networking complications when virt-builder runs, and to simplify the
# virt-builder command. Although the application files have been
# copied into the VM filesystem, composer packages haven't been
# installed yet.
#
# After this script runs the application should be viewable in a
# browser.

mv /var/www/build /var/www/toils

apt-get install -y \
        composer \
        nginx-light \
        php-bcmath \
        php-dom \
        php-fpm \
        php-json \
        php-mbstring \
        php-sqlite3 \
        php-tokenizer

rm -f /etc/php/7.3/fpm/pool.d/www.conf
rm -f /etc/nginx/sites-enabled/default
rm -rf /var/www/html

systemctl restart php7.3-fpm nginx

cd /var/www/toils
touch toils.sqlite

mkdir -p storage/app/public
mkdir -p storage/fonts
mkdir -p storage/framework/views
mkdir -p storage/logs

if [ ! -f storage/toils.sqlite ]; then
   touch storage/toils.sqlite
fi

export COMPOSER_HOME=/var/www/toils
composer install --no-dev --no-interaction --optimize-autoloader --no-suggest

cat <<EOF > .env
APP_ENV=production
APP_KEY=
APP_DEBUG=false
EOF

php artisan key:generate
php artisan migrate --force --no-interaction
php artisan config:cache

chown -R www-data:www-data .
