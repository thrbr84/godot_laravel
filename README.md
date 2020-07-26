# Godot + Laravel

- [Dependencies](DEPENDENCIES.md)

# Explanation
- [My Youtube channel](https://youtube.com.br/thiagobruno)

# Setting
- Clone this repository
- cd godot_laravel/laravel
- Execute:
    - composer install
    - chmod -R 0777 storage
    - chmod -R 0777 bootstrap/cache
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
        - Access: http://127.0.0.1:8000

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