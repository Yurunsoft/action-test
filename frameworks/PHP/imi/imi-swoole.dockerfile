FROM php:8.0-cli

RUN docker-php-ext-install opcache > /dev/null

RUN pecl install swoole > /dev/null && \
    docker-php-ext-enable swoole

RUN pecl install redis > /dev/null && \
    docker-php-ext-enable redis

RUN apt -yqq update > /dev/null && \
    apt -yqq install git unzip > /dev/null

COPY . /imi
COPY php.ini /usr/local/etc/php/

RUN chmod -R ug+rwx /imi/.runtime

RUN curl -sSL https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-dev --classmap-authoritative --quiet
RUN composer require imiphp/imi-swoole:~2.0.0 -W
RUN composer dumpautoload -o

RUN apt -yqq install redis-server > /dev/null

EXPOSE 8080

CMD ./run-swoole.sh
