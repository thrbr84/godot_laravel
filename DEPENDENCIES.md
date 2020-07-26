# Godot_Laravel - Dependencies

- Ubuntu 18.04 LTS
- PHP ^7.2.5
- MySQL ^5.7
- Composer
- NodeJs
- NPM


# How to install PHP ^7.2.5
- apt-get update && apt-get upgrade
- apt-get install software-properties-common
- add-apt-repository ppa:ondrej/php
- apt-get update
- apt-get install php7.2
- apt-get install php-pear php7.2-curl php7.2-dev php7.2-gd php7.2-mbstring php7.2-zip php7.2-mysql php7.2-xml
- php -v

# How to install MySQL ^5.7
- sudo apt update
- sudo apt install mysql-server
- sudo mysql_secure_installation

# How to install Composer
- sudo apt update
- sudo apt install curl php7.2-cli php7.2-mbstring git unzip
- cd ~
- curl -sS https://getcomposer.org/installer -o composer-setup.php
- sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer
- composer --version

# How to install NodeJS e NPM
- sudo apt install nodejs
- sudo apt install npm
- nodejs -v