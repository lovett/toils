#!/bin/sh

cd /mnt/toils-storage
chown www-data:www-data .
chown -R www-data:www-data $(ls | grep -v lost)

umount /mnt/toils-app
mount -o rw -L toils-app /mnt/toils-app
cd /mnt/toils-app
chown www-data:www-data .
chown -R www-data:www-data $(ls | grep -v lost)
umount /mnt/toils-app
mount /mnt/toils-app

cd /mnt/toils-app

php artisan migrate --force --no-interaction
