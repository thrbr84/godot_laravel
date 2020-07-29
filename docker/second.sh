#!/bin/bash

echo "==> Running migration:restart"
php artisan migrate:restart

echo "==> Running migration --seed"
php artisan migrate --seed

echo "==> Config cache"
php artisan config:cache

echo "==> Generating key"
php artisan key:generate

echo "==> Config cache"
php artisan config:cache

echo "==> Starting apache"
apache2-foreground
