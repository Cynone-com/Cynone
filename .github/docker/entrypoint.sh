#!/bin/ash
set -e

cd /app

mkdir -p \
    /app/storage/framework/cache \
    /app/storage/framework/sessions \
    /app/storage/framework/views \
    /app/storage/logs \
    /var/run/php \
    /var/run/nginx

chown -R nginx:nginx /app/storage /app/bootstrap/cache /var/run/php /var/run/nginx
chmod -R ug+rwX /app/storage /app/bootstrap/cache

if [ ! -f /app/.env ] && [ -f /app/.env.example ]; then
    cp /app/.env.example /app/.env
fi

if [ -f /app/artisan ]; then
    php /app/artisan package:discover --ansi
    php /app/artisan config:clear || true
    php /app/artisan route:clear || true
    php /app/artisan view:clear || true
fi

exec "$@"
