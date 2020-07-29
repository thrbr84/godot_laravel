#!/bin/bash

echo "==> Creating .env file from .env.model file"
cp .env.model .env

echo "==> Running composer install"
composer install

echo "==> Setup permissions for container"
chown -Rf www-data:www-data /var/www/html
chmod -Rf 755 /var/www/html/storage
