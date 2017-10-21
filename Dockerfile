FROM php:7.1-fpm-alpine

WORKDIR /var/www/ufrj-authentication

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

COPY composer.json ./
COPY composer.lock ./

COPY vendor ./vendor
RUN composer install --no-ansi --no-interaction --no-dev

COPY src ./src

VOLUME /var/www/ufrj-authentication