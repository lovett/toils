#!/bin/sh

# This script runs from inside the QEMU virtual machine the first time
# it is booted.
#
# It sets up PHP-FPM and Nginx by installing system
# packages. Configuration files were populated when the image was
# created.
#
# Application setup also happens here: installing composer pakcages
# installed and performing Laravel-specific optimizations.
#
# When this script runs, the OS is pristine. Deferring setup like this
# makes things a little easier for virt-builder and avoids some
# complexity around getting the network connection working.
#
# After this script runs the application should be viewable in a
# browser.

# Decrease boot time by skipping kernel selection.
sed -i 's/GRUB_TIMEOUT=5/GRUB_TIMEOUT=0/' /etc/default/grub
update-grub

# Package installation
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
rm -rf /var/www
ln -s /mnt/toils-app /var/www

systemctl restart php7.3-fpm nginx

cd /mnt/toils-storage
chown -R www-data:www-data $(ls | grep -v lost)

cd /mnt/toils-app
php artisan migrate --force --no-interaction
php artisan config:cache
chown -R www-data:www-data $(ls | grep -v lost)
