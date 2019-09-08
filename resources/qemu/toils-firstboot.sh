#!/bin/sh

# This script runs as root from inside the QEMU virtual machine the
# first time it is booted.
#
# It sets up PHP-FPM and Nginx by installing system
# packages. Configuration files will have already been populated when
# the image was created.
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

chown www-data:www-data /mnt/toils-app
chown www-data:www-data /mnt/toils-storage

systemctl restart php7.3-fpm nginx

systemctl enable toils-setup.service
systemctl start toils-setup.service
