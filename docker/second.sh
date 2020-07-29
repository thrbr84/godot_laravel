#!/bin/bash

echo "==> Running migration"
php artisan migrate

echo "==> Running passport installation"
php artisan passport:install

echo "==> Config cache"
php artisan config:cache

echo "==> Generating key"
php artisan key:generate

echo "==> Config cache"
php artisan config:cache

echo "==> Starting apache"
apache2-foreground