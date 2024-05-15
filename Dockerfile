FROM php:8.2-apache
RUN apt-get update \
    && apt install libzip-dev unzip -y \
    && docker-php-ext-install mysqli pdo pdo_mysql zip

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

COPY ./app .

RUN alias composer='php /usr/bin/composer'