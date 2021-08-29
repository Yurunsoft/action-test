FROM php:8.0-cli

ENV SWOOLE_VERSION 4.7.1
ENV SWOOLE_POSTGRES f5eda17f89d160d0a89ac7c5db4636bdaefd48e6

RUN docker-php-ext-install opcache > /dev/null

RUN apt -yqq update > /dev/null && \
    apt -yqq install git unzip libpq-dev redis-server > /dev/null

RUN pecl install swoole-${SWOOLE_VERSION} > /dev/null && \
    docker-php-ext-enable swoole

RUN curl -L -o ext-postgresql.tar.gz https://github.com/swoole/ext-postgresql/archive/${SWOOLE_POSTGRES}.tar.gz && tar -xvf ext-postgresql.tar.gz && cd ext-postgresql-${SWOOLE_POSTGRES} && phpize && ./configure && make -j && make install && docker-php-ext-enable swoole_postgresql && cd ../ && rm -rf ext-postgresql-${SWOOLE_POSTGRES}

RUN pecl install redis > /dev/null && \
    docker-php-ext-enable redis

COPY . /imi
COPY php.ini /usr/local/etc/php/

RUN chmod -R ug+rwx /imi/.runtime

WORKDIR /imi

RUN curl -sSL https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-dev --classmap-authoritative --quiet > /dev/null
RUN composer require imiphp/imi-swoole:~2.0.0 -W
RUN composer require imiphp/imi-pgsql:~2.0.0 -W
RUN composer dumpautoload -o

EXPOSE 8080

CMD ./run-swoole.sh
