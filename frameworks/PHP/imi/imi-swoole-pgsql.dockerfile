FROM php:8.0-cli

ENV SWOOLE_VERSION 4.7.1
ENV SWOOLE_POSTGRES 32c1281e892925fd5b2b7dac7070c40dfb54e729
ARG TFB_TEST_DATABASE
ENV TFB_TEST_DATABASE=${TFB_TEST_DATABASE}

RUN docker-php-ext-install -j$(nproc) opcache > /dev/null

RUN apt -yqq update > /dev/null && \
    apt -yqq install git unzip libpq-dev redis-server > /dev/null

RUN     cd /tmp && curl -sSL "https://github.com/swoole/swoole-src/archive/v${SWOOLE_VERSION}.tar.gz" | tar xzf - \
        && cd swoole-src-${SWOOLE_VERSION} \
        && phpize && ./configure > /dev/null && make -j > /dev/null && make install > /dev/null \
        && docker-php-ext-enable swoole

RUN     cd /tmp && curl -sSL "https://github.com/Yurunsoft/ext-postgresql/archive/${SWOOLE_POSTGRES}.tar.gz" | tar xzf - \
        && cd ext-postgresql-${SWOOLE_POSTGRES} \
        && phpize && ./configure > /dev/null && make -j > /dev/null && make install > /dev/null \
        && docker-php-ext-enable swoole_postgresql

RUN pecl update-channels

RUN pecl install redis > /dev/null && \
    docker-php-ext-enable redis

COPY . /imi
COPY php.ini /usr/local/etc/php/

RUN chmod -R ug+rwx /imi/.runtime

WORKDIR /imi

RUN curl -sSL https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-dev --classmap-authoritative --quiet > /dev/null
RUN composer require imiphp/imi-swoole:2.0.x-dev -W
RUN composer require imiphp/imi-pgsql:2.0.x-dev -W
RUN composer dumpautoload -o

EXPOSE 8080

CMD ./run-swoole.sh
