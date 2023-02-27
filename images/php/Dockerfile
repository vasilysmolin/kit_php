FROM composer:2 AS composer
FROM php:8.1.1-fpm-alpine

ARG DOMAIN
ARG DOCKER_ENV
ARG ENV
ARG USER
ARG UID

COPY --from=composer /usr/bin/composer /usr/bin/composer

# Установка зависимостей
RUN apk update && apk add --no-cache \
        imagemagick \
        imagemagick-libs \
        imagemagick-dev \
        libjpeg-turbo-dev \
        libpng-dev \
        libwebp-dev \
        libzip-dev \
        libheif-dev \
        g++ \
        make \
        autoconf

# Установка расширения imagick с поддержкой формата heic
RUN pecl install imagick-3.7.0 \
    && docker-php-ext-enable imagick \
    && apk del imagemagick-dev \
    && rm -rf /tmp/* /var/cache/apk/*

RUN set -xe \
    && apk add --no-cache \
    $PHPIZE_DEPS \
    postgresql-client \
    ffmpeg \
    libwebp-tools \
    freetype-dev \
    postgresql-dev \
    gmp-dev \
    && docker-php-ext-configure zip \
    && docker-php-ext-configure gmp \
    && docker-php-ext-configure pdo_pgsql \
    && docker-php-ext-install -j$(nproc) zip \
    && docker-php-ext-install -j$(nproc) exif \
    && docker-php-ext-install -j$(nproc) pdo_pgsql \
    && docker-php-ext-install -j$(nproc) gmp \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl \
    && pear install pear/PHP_CodeSniffer \
    && pecl install pcov

RUN apk add --no-cache supervisor tzdata
ENV TZ Europe/Moscow

#RUN if [ "$ENV" = "development" ]; \
#	then pecl install xdebug && docker-php-ext-enable xdebug;  \
#	else echo "$ENV"; \
#	fi

COPY /php.ini /usr/local/etc/php/php.ini
COPY /fpm-pool.conf /usr/local/etc/php-fpm.d/www.conf
COPY /crontab /etc/crontabs/root

COPY /supervisord.conf /etc/supervisor/conf.d/supervisord.conf

RUN adduser -G root -u $UID -h /home/$USER -S $USER

USER $USER

CMD /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
