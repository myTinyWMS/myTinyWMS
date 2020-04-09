#!/bin/bash

set -e -x

cd /data/www

mkdir -p /data/www/storage/framework/{sessions,views,cache}
chown www-data:www-data /data/www/storage/framework/{sessions,views,cache}

mkdir -p /data/www/storage/logs
chown www-data:www-data /data/www/storage /data/www/storage/logs
chown -R www-data:www-data /data/www/bootstrap/cache

# no vendor directory (this happens only in a dev environment where /data/www is fully mounted)
if [ ! -d "vendor" ]; then
    composer install
fi

php-fpm
