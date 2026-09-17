#!/bin/sh
set -e

cd /var/www/html

# php-fpm runs as www-data, which is typically neither the owner nor the
# group of a bind-mounted host directory (dev mode) — grant "other" write too.
chmod -R a+rwX storage bootstrap/cache

if [ -f .env ] && grep -q '^APP_KEY=$' .env; then
    php artisan key:generate --ansi
fi

if [ ! -L public/storage ]; then
    php artisan storage:link
fi

exec "$@"
