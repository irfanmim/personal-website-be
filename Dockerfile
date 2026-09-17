# syntax=docker/dockerfile:1

##
## Stage 1 — install Composer dependencies and build the optimized autoloader.
##
FROM php:8.3-cli-alpine AS vendor

RUN apk add --no-cache icu-dev libzip-dev $PHPIZE_DEPS \
    && docker-php-ext-install pdo_mysql bcmath intl pcntl zip \
    && apk del $PHPIZE_DEPS

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

COPY . .
RUN composer dump-autoload --optimize --no-dev

##
## Stage 2 — runtime image: php-fpm + only the shared libs the extensions need.
##
FROM php:8.3-fpm-alpine AS runtime

RUN apk add --no-cache icu-libs libzip icu-dev libzip-dev $PHPIZE_DEPS \
    && docker-php-ext-install pdo_mysql bcmath intl pcntl \
    && apk del icu-dev libzip-dev $PHPIZE_DEPS

COPY docker/php/uploads.ini /usr/local/etc/php/conf.d/uploads.ini

WORKDIR /var/www/html

COPY --from=vendor /app .

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh \
    && chown -R www-data:www-data storage bootstrap/cache

EXPOSE 9000

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm"]
