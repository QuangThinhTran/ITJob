#!/bin/bash

if [ ! -f "vendor/autoload.php" ]; then
    composer install --no-ansi --no-dev --no-interaction --no-plugins --no-progress --no-scripts --optimize-autoloader
    composer update
    echo "Composer install completed!"
fi

if [ ! -f ".env" ]; then
    cp .env.example .env
    echo "Copying .env.example to .env"
fi

## Configure Laravel
php artisan key:generate --env=production --force
php artisan migrate --env=production --force
php artisan set-database
php artisan config

php-fpm -D
nginx -g "daemon off;"
