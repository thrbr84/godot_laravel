# Godot + Laravel

# Explanation (PT_BR)
- [My Youtube channel](https://youtu.be/eSmhLndmim4)

[![Video explanation](https://img.youtube.com/vi/eSmhLndmim4/0.jpg)](https://www.youtube.com/watch?v=eSmhLndmim4)

# Setting with Docker
### [Contributed by @felippe-miguel](https://github.com/felippe-miguel)
- Install [docker](https://docs.docker.com/engine/install/ubuntu/)
- Install [docker-compose](https://docs.docker.com/compose/install/)
- execute: ```docker-compose up```
- after run the docker: http://godotlaravel.local

# Running without Docker
- [Dependencies](DEPENDENCIES.md)
- Clone this repository
- cd godot_laravel/laravel
- Execute:
    - composer install
    - chmod -R 0755 storage
    - chmod -R 0755 bootstrap/cache
    - cp .env.model .env
    - php artisan key:generate
    - create mysql database ```godot_laravel```
    - config the ```.env``` file with your parameters
    - php artisan migrate
    - php artisan passport:install

    ## Optimize
    - php artisan optimize:clear

    ## local serve
        - php artisan serve
        - Access: [http://127.0.0.1:8000](http://127.0.0.1:8000)

# Config local Apache
- ```sudo nano /etc/hosts```
    ```bash
    # add this line
    127.0.0.1   godotlaravel.local
    ```
- ```cd /etc/apache2/sites-available```
- ```sudo nano api-godotlaravel.conf```
    ```bash
    <VirtualHost *:80>
        ServerName godotlaravel.local
        ServerAlias godotlaravel.local

        ServerAdmin webmaster@localhost
        DocumentRoot "/home/SEU_USUARIO/Godot_Laravel/laravel/public"

        <Directory "/home/SEU_USUARIO/Godot_Laravel/laravel/public">
                AllowOverride all
                Require all granted
        </Directory>
    </VirtualHost>
    ```
- ```sudo a2ensite api-godotlaravel.conf```
- ```sudo systemctl restart apache2```
- Test: [http://godotlaravel.local](http://godotlaravel.local)

# Deploy production server
- Edit ```.env``` file
    ```bash
    APP_ENV=production
    APP_DEBUG=false
    ```
- composer install --optimize-autoload --no-dev

## Config cache
- php artisan config:cache
- php artisan route:cache