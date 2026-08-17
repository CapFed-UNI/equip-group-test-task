#!/bin/sh
set -e

cd /var/www/html

if [ ! -f .env ]; then
    cp .env.example .env
fi

export COMPOSER_MAX_PARALLEL_HTTP=1
export COMPOSER_PROCESS_TIMEOUT=0

composer_ok=0
attempt=1
while [ "${attempt}" -le 20 ]; do
    if composer install --no-interaction --prefer-dist --no-ansi; then
        composer_ok=1
        break
    fi
    echo "composer install failed (attempt ${attempt}), retrying..."
    sleep $((15 + attempt * 5))
    attempt=$((attempt + 1))
done
[ "${composer_ok}" = "1" ] || exit 1

php artisan key:generate --ansi

chown -R www-data:www-data storage bootstrap/cache || true
chmod -R 775 storage bootstrap/cache || true

php artisan migrate --force --ansi
php artisan db:seed --force --ansi

exec docker-php-entrypoint php-fpm
