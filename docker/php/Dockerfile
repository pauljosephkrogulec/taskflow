FROM php:8.4-fpm-alpine

# System dependencies
RUN apk add --no-cache \
    git \
    curl \
    unzip \
    icu-dev \
    libpq-dev \
    linux-headers \
    $PHPIZE_DEPS

# PHP extensions
RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    intl \
    opcache

# APCu for caching
RUN pecl install apcu && docker-php-ext-enable apcu

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# PHP config
COPY php.ini /usr/local/etc/php/conf.d/app.ini

WORKDIR /app

# Non-root user
RUN addgroup -g 1000 app && adduser -u 1000 -G app -s /bin/sh -D app
USER app
