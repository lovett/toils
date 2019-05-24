#!/bin/bash

# Build a container image suitable for production use.
#
# This is where the source files for the application are brought info
# the container's filesystem. It's also where composer packages get
# installed and where the application is optimized for the production
# environment.

set -e -u

APP_NAME='Toils'
CONTAINER_NAME='toils'
BASE_IMAGE='nginx-php'
WEB_ROOT='/srv/www'

if ! hash npm 2>/dev/null; then
    echo "Cannot continue. Npm is not installed."
    exit 1
fi

# Start from a clean slate by deleting any existing containers or images.
if $(buildah images | grep --quiet "localhost/$CONTAINER_NAME"); then
    echo "Deleting previous $CONTAINER_NAME image..."
    buildah rmi "$CONTAINER_NAME"
fi

if $(buildah containers -a | grep --quiet "$BASE_IMAGE-working-container"); then
    echo "Deleting previous work container..."
    buildah rm "$BASE_IMAGE-working-container"
fi

# Create a work container from the base image.
WORK_CONTAINER=$(buildah from "localhost/$BASE_IMAGE")

# Build frontend assets in production mode
#
# This requires node and npm to be available on the build host.
npm run production

# Copy the application source files
#
# It's easier to do this in one shot with rsync than with buildah-copy.
MOUNT=$(buildah mount "$WORK_CONTAINER")

rsync -av --cvs-exclude \
      --delete-excluded \
      --exclude=.ackrc \
      --exclude=.env \
      --exclude=.env.example \
      --exclude=.gitattributes \
      --exclude=.gitignore \
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
      ./ "$MOUNT/$WEB_ROOT"

cd "$MOUNT/$WEB_ROOT"

# Composer will throw an error if this directory doesn't exist.
mkdir -p storage/framework/views

# Create a placeholder for the database.
#
# Otherwise composer throws an error about it not existing
# during the "Generating optimized autoload files" step.
touch "storage/toils.sqlite"

# Install composer packages
#
# This runs from inside the container to avoid an implicit
# dependency on the host having composer installed.
COMPOSER_ARGS="--no-dev --no-interaction --optimize-autoloader --no-suggest"
buildah run "$WORK_CONTAINER" /bin/sh -c "cd /srv/www; composer install $COMPOSER_ARGS"

# Create a Laravel env file
#
# This causes the Laravel app key to be regenerated ever time the
# image is built.
cat <<EOF > .env
APP_NAME=$APP_NAME
APP_ENV=production
APP_KEY=
APP_DEBUG=false
EOF
php artisan key:generate

# Perform pre-start tasks.
#
# The entrypoint of the base image will check for the existince of
# this file and run it prior to starting PHP-FPM and Nginx.
#
# Database migrations happen here so that they can be automatic.
#
# The SQLite database is created if it does not already exist. This is
# different from the placeholder file used elsewhere in the build
# process. That file exists on the container filesytem and is
# temporary. This one exists in the data volume mounted on
# /srv/www/storage, and is permanent.
cat <<EOF > "$MOUNT/usr/local/sbin/pre-init.sh"
#!/bin/sh

cd "$WEB_ROOT"

mkdir -p storage/app/public
mkdir -p storage/fonts
mkdir -p storage/framework/views
mkdir -p storage/logs

if [ ! -f storage/toils.sqlite ]; then
   touch storage/toils.sqlite
fi

php artisan migrate --force --no-interaction
EOF

# Perform Laravel deployment optimizations.
#
# This runs from inside the container out of necessity. If it ran from
# the host, artisan would see file paths relative to the host.
buildah run "$WORK_CONTAINER" /bin/sh -c 'cd /srv/www; php artisan config:cache'

# The database placeholder is no longer needed.
#
# The storage directory is intended to be a mount point for a data
# volume to allow for persistence, so this part of the container's
# filesystem is otherwise unused.
rm "$MOUNT/$WEB_ROOT/storage/toils.sqlite"

# Finished with direct access to the container filesystem.
buildah unmount "$WORK_CONTAINER"

# Save the working container as an image and discard the working
# container.
buildah commit --rm "$WORK_CONTAINER" "$CONTAINER_NAME"
