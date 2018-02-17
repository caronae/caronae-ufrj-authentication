FROM caronae/php

WORKDIR /var/www/ufrj-authentication

COPY composer.json ./
COPY composer.lock ./

COPY vendor ./vendor
RUN composer install --no-ansi --no-interaction --no-dev

COPY src ./src

VOLUME /var/www/ufrj-authentication