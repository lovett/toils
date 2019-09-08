#!/bin/sh

# This script runs as root from inside the QEMU virtual machine at
# every boot.
#
# It resets file ownership on the storage and app mounts, because
# handling that at build time would be complicated. Storeage files
# shouldn't ever change ownership, but app files will change when a
# new image is deployed.
#
# The Laravel database is also migrated here, in case the the app
# volume has changed. Likewise for clearing and populating the Laravel
# view cache.

cd /mnt/toils-storage
chown www-data:www-data .
chown -R www-data:www-data $(ls | grep -v lost)

cd /mnt/toils-app
chown www-data:www-data .
chown -R www-data:www-data $(ls | grep -v lost)

php artisan migrate --force --no-interaction

php artisan view:clear
php artisan view:cache
