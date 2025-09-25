FROM php:8.4-fpm-alpine

# --- 1) Build deps + runtime libs ---
# $PHPIZE_DEPS: autoconf, make, g++, etc. (needed for PECL builds)
# postgresql-dev (Alpine) is required for pdo_pgsql
RUN set -eux; \
    apk add --no-cache \
        $PHPIZE_DEPS \
        icu-dev \
        libzip-dev \
        postgresql-dev \
        linux-headers \
        bash \
        git \
        unzip

# --- 2) Core PHP extensions (split steps for clearer errors) ---
RUN set -eux; docker-php-ext-configure intl
RUN set -eux; docker-php-ext-install -j"$(nproc)" intl opcache zip
# pdo_pgsql depends on postgresql-dev
RUN set -eux; docker-php-ext-install -j"$(nproc)" pdo_pgsql

# --- 3) Xdebug (pin a known-good version) ---
# Pinning helps avoid sudden upstream changes
RUN set -eux; \
    pecl install xdebug-3.4.5; \
    docker-php-ext-enable xdebug; \
    rm -rf /tmp/pear; \
    apk del --no-cache $PHPIZE_DEPS linux-headers

# --- 4) Composer (for Symfony) ---
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# --- 5) Xdebug INI (baked into the image) ---
# Build context is ./docker/php, so this path refers to docker/php/conf.d/xdebug.ini
COPY ./conf.d/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

WORKDIR /var/www

RUN chown -R www-data:www-data /var/www