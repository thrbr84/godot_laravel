FROM php:7.4-apache-buster

RUN docker-php-ext-install pdo_mysql

RUN apt-get update && apt-get install -y \
    build-essential \
    zip \
    unzip \
    git \
    curl

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

ADD apache/000-default.conf /etc/apache2/sites-available/000-default.conf

ADD first.sh /first.sh
ADD wait-for-it.sh /wait-for-it.sh
ADD second.sh /second.sh

RUN chmod 755 /first.sh
RUN chmod 755 /wait-for-it.sh
RUN chmod 755 /second.sh

RUN a2enmod rewrite

WORKDIR /var/www/html

CMD /first.sh && /wait-for-it.sh $DB_HOST:$DB_PORT && /second.sh