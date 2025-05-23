ARG NGINX_VERSION=1.24

# -------------
# generic php base image
# -------------
FROM php:8.4-fpm-alpine AS generic

RUN apk update && \
  apk add --no-cache \
  fcgi git

RUN apk add --no-cache icu-dev openssl acl
RUN docker-php-ext-install mysqli pdo pdo_mysql posix pcntl intl

RUN apk add --no-cache libzip-dev zip  \
    && docker-php-ext-install zip

#RUN docker-php-ext-enable apcu
COPY --from=composer:2.8.8 /usr/bin/composer /usr/local/bin/composer

VOLUME /var/run/php

COPY docker/php/zz-docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf

COPY docker/php/php-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

COPY docker/php/docker-healthcheck.sh /usr/local/bin/docker-healthcheck
RUN chmod +x /usr/local/bin/docker-healthcheck

HEALTHCHECK --interval=10s --timeout=3s --retries=3 CMD ["docker-healthcheck"]

ENTRYPOINT ["docker-entrypoint"]
CMD ["php-fpm"]

FROM generic AS base

#ARG CI_JOB_TOKEN=
ARG COMPOSER_AUTH=

# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_AUTH=${COMPOSER_AUTH}

WORKDIR /srv/app

# prevent the reinstallation of vendors at every changes in the source code
COPY composer.json composer.lock symfony.lock ./

# copy only specifically what we need
COPY bin bin/
COPY config config/
COPY public public/
COPY src src/
COPY migrations migrations/
RUN mkdir -p files

RUN mkdir -p var/cache var/log

VOLUME /srv/app/var

# ---------
# prod build
# ---------
FROM base AS build_prod

WORKDIR /srv/app

RUN composer install --prefer-dist --no-dev --no-scripts --no-progress -v && \
    composer clear-cache

COPY .env ./

ENV APP_ENV=prod

RUN composer dump-autoload --classmap-authoritative --no-dev && \
    chmod +x bin/console && \
    sync

RUN rm -rf src/DataFixtures

RUN php bin/console assets:install

RUN echo 'memory_limit = -1' >> $PHP_INI_DIR/conf.d/memory_limit_php.ini
RUN echo 'upload_max_filesize = 200 M' >> $PHP_INI_DIR/conf.d/memory_limit_php.ini

# ---------
# dev build
# ---------
FROM base AS build_dev

RUN apk add --no-cache bash

COPY tests tests/
COPY phpunit.dist.xml phpstan.neon ./
COPY .env .env.test ./

RUN composer install --prefer-dist --no-scripts --no-progress && \
    composer clear-cache

RUN composer dump-autoload && \
    composer run-script post-install-cmd || \
    chmod +x bin/console && \
    sync

RUN php bin/console assets:install

# --------------
# php prod image
# --------------
FROM base AS php

WORKDIR /srv/app

RUN ln -sf $PHP_INI_DIR/php.ini-production $PHP_INI_DIR/php.ini

# Modify memory limit
RUN echo 'memory_limit = -1' >> $PHP_INI_DIR/conf.d/memory_limit_php.ini
RUN echo 'upload_max_filesize = 200 M' >> $PHP_INI_DIR/conf.d/memory_limit_php.ini

COPY --from=build_prod /srv/app /srv/app
RUN chown -R www-data var

# -------------
# php dev image
# -------------
FROM base AS php_dev

WORKDIR /srv/app

RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS linux-headers \
    && cd /tmp && \
    git clone https://github.com/xdebug/xdebug.git && \
    cd xdebug && \
    git checkout 3.4.2 && \
    phpize && \
    ./configure --enable-xdebug && \
    make && \
    make install && \
    rm -rf /tmp/xdebug
RUN docker-php-ext-enable xdebug

ARG COMPOSER_AUTH=

COPY phpunit.dist.xml phpstan.neon ./
COPY --from=build_dev /srv/app /srv/app
RUN chown -R www-data var

RUN ln -sf $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini

# Modify memory limit
RUN echo 'memory_limit = -1' >> $PHP_INI_DIR/conf.d/memory_limit_php.ini
RUN echo 'upload_max_filesize = 200 M' >> $PHP_INI_DIR/conf.d/memory_limit_php.ini

ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_AUTH=${COMPOSER_AUTH}

# ----------------
# nginx prod image
# ----------------
FROM nginx:${NGINX_VERSION}-alpine AS nginx

COPY docker/php/nginx_prod.conf /etc/nginx/conf.d/default.conf

WORKDIR /srv/app

COPY --from=php /srv/app/public public/

# ---------------
# nginx dev image
# ---------------
FROM nginx:${NGINX_VERSION}-alpine AS nginx_dev

COPY docker/php/nginx_dev.conf /etc/nginx/conf.d/default.conf

WORKDIR /srv/app

COPY --from=php_dev /srv/app/public public/
