#!/bin/bash

echo "==> Running migration:reset"
php artisan migrate:reset

echo "==> Running migration --seed"
php artisan migrate --seed

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
