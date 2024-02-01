FROM php:8.3-fpm

RUN apt-get update && apt-get install git libzip4 libicu-dev -y
RUN pecl install xdebug-3.3.1

RUN docker-php-ext-enable xdebug.so
RUN docker-php-ext-install bcmath intl pdo_mysql

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer