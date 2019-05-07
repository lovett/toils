#!/bin/bash

# Build a container image suitable for production use.
#
# This is where the source files for the application are brought onto
# the container's filesystem. It's also where composer packages get
# installed and where the application is optimzed for the production
# environment.

set -e -u

APP_NAME='Toils'
CONTAINER_NAME='toils'
BASE_IMAGE='nginx-php'
WEB_ROOT='/srv/www'

# Start from a clean slate by deleting any existing containers or images.
if $(buildah images | grep --quiet "localhost/$CONTAINER_NAME"); then
    echo "Deleting previous $CONTAINER_NAME image..."
    buildah rmi "$CONTAINER_NAME"
fi

if $(buildah containers -a | grep --quiet "$BASE_IMAGE-working-container"); then
    echo "Deleting existing work container..."
    buildah rm "$BASE_IMAGE-working-container"
fi

# Create a work container from the base image.
WORK_CONTAINER=$(buildah from "localhost/$BASE_IMAGE")

# Copy the application source files
#
# It's easier to do this in one shot with rsync than with buildah-copy.
MOUNT=$(buildah mount "$WORK_CONTAINER")

rsync -av --cvs-exclude \
      --delete-excluded \
      --exclude=.ackrc \
      --exclude=.env \
      --exclude=.env.example \
      --exclude=.gitignore \
      --exclude=node_modules \
      --exclude=vendor \
      --exclude=bootstrap/cache/* \
      --exclude=storage/app/public/* \
      --exclude=storage/debugbar \
      --exclude=storage/framework/cache \
      --exclude=storage/framework/sessions \
      --exclude=storage/framework/testing \
      --exclude=storage/framework/views/* \
      --exclude=tests \
      --exclude=phpcs.xml \
      --exclude=phpmd.xml \
      --exclude=phpunit.xml \
      --exclude=Makefile \
      --exclude=mkimage.sh \
      --exclude=server.php \
      --exclude=toils.sqlite \
      --exclude=webpack.mix.js \
      ./ "$MOUNT/$WEB_ROOT"

cd "$MOUNT/$WEB_ROOT"

# Install composer packages
#
# There is an implicit dependency on composer being available from the host.
composer install \
         --no-suggest \
         --optimize-autoloader \
         --no-dev \
         --no-interaction \
         --no-progress

# Create a Laravel env file
#
# This causes the Laravel app key to be regenerated ever time the
# image is built.
echo "APP_NAME=$APP_NAME" >> .env
echo "APP_ENV=production" >> .env
echo "APP_KEY=" >> .env
echo "APP_DEBUG=false" >> .env
php artisan key:generate

buildah unmount "$WORK_CONTAINER"

# Perform Laravel deployment optimizations
#
# This runs from inside the container so that the file paths remain
# relative to the continaer.  If it ran from a filesystem mount,
# artisan would see file paths relative to the host that would be
# invalid when the application runs.
buildah run "$WORK_CONTAINER" /bin/sh -c 'cd /srv/www; php artisan config:cache'

# Save the working container as an image and discard the working
# container.
buildah commit --rm "$WORK_CONTAINER" "$CONTAINER_NAME"
