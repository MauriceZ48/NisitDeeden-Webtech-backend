#!/bin/sh
set -e

php artisan key:generate --force
php artisan migrate --force
php artisan storage:link

php-fpm -D
nginx -g "daemon off;"