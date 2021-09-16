FROM php:8.0-cli

ENV SWOOLE_VERSION 86708d6ad31ce4374bca9c1bfdee0e6af892ed11
ENV SWOOLE_POSTGRES 6f603245d70dd4211ac1f5feadaac74f7e65fd97
ARG TFB_TEST_DATABASE
ENV TFB_TEST_DATABASE=${TFB_TEST_DATABASE}

RUN docker-php-ext-install -j$(nproc) opcache > /dev/null

RUN apt -yqq update > /dev/null && \
    apt -yqq install git unzip libpq-dev > /dev/null

RUN     cd /tmp && curl -sSL "https://github.com/swoole/swoole-src/archive/${SWOOLE_VERSION}.tar.gz" | tar xzf - \
        && cd swoole-src-${SWOOLE_VERSION} \
        && phpize && ./configure > /dev/null && make -j > /dev/null && make install > /dev/null \
        && docker-php-ext-enable swoole

RUN     cd /tmp && curl -sSL "https://github.com/swoole/ext-postgresql/archive/${SWOOLE_POSTGRES}.tar.gz" | tar xzf - \
        && cd ext-postgresql-${SWOOLE_POSTGRES} \
        && phpize && ./configure > /dev/null && make -j > /dev/null && make install > /dev/null \
        && docker-php-ext-enable swoole_postgresql

COPY . /imi
COPY php.ini /usr/local/etc/php/

RUN chmod -R ug+rwx /imi/.runtime

WORKDIR /imi

RUN curl -sSL https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-dev --classmap-authoritative --quiet > /dev/null
RUN composer require imiphp/imi-swoole:~2.0 -W
RUN composer dumpautoload -o

EXPOSE 8080

CMD ./run-swoole.sh
