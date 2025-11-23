# ---------- 1) BUILD STAGE: install deps with Composer ----------
FROM composer:2 AS vendor

WORKDIR /app

COPY composer.json composer.lock symfony.* ./
RUN composer install --no-dev --no-scripts --no-progress --prefer-dist

FROM php:8.3-fpm-alpine

RUN apk add --no-cache \
    bash \
    icu-dev \
    libzip-dev \
    oniguruma-dev \
    git \
    unzip \
    curl \
    $PHPIZE_DEPS

RUN docker-php-ext-install \
    intl \
    pdo_mysql \
    opcache

RUN pecl install redis \
    && docker-php-ext-enable redis

WORKDIR /var/www/html

COPY . .

COPY --from=vendor /app/vendor ./vendor

RUN chown -R www-data:www-data var

EXPOSE 9000

CMD ["php-fpm"]
