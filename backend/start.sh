#!/bin/sh
php artisan config:clear
php artisan migrate --force
php artisan schedule:work &
php-fpm