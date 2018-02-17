FROM caronae/php

ENV LOG_STREAM="/tmp/ufrj-authentication.log"
RUN mkfifo $LOG_STREAM && chmod 777 $LOG_STREAM
CMD ["/bin/sh", "-c", "php-fpm -D | tail -f $LOG_STREAM"]

WORKDIR /var/www/ufrj-authentication

COPY composer.json ./
COPY composer.lock ./

COPY vendor ./vendor
RUN composer install --no-ansi --no-interaction --no-dev

COPY src ./src

VOLUME /var/www/ufrj-authentication