#!/bin/sh
php artisan migrate:fresh --seed
php artisan storage:link
php-fpm