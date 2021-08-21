FROM php:8.0-cli

RUN docker-php-ext-install opcache mysqli pcntl sockets > /dev/null

RUN pecl install redis > /dev/null && \
    docker-php-ext-enable redis

RUN pecl install event-3.0.5 > /dev/null && \
    docker-php-ext-enable event

RUN apt -yqq update > /dev/null && \
    apt -yqq install git unzip > /dev/null

COPY . /imi
COPY php.ini /usr/local/etc/php/

RUN chmod -R ug+rwx /imi/.runtime

RUN curl -sSL https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-dev --classmap-authoritative --quiet > /dev/null
RUN composer require imiphp/imi-workerman:~2.0.0 -W
RUN composer dumpautoload -o

RUN apt -yqq install redis-server > /dev/null

EXPOSE 8080

CMD ./run-workerman.sh
