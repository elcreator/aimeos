#!/bin/sh
set -eu

cd /var/www/html
mkdir -p storage/app storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache

if [ ! -f .env ]; then
    cp .env.example .env
fi

if [ -z "${APP_KEY:-}" ]; then
    key_file=storage/app/aimeos-demo-app-key
    if [ -s "$key_file" ]; then
        APP_KEY=$(cat "$key_file")
    else
        APP_KEY="base64:$(php -r 'echo base64_encode(random_bytes(32));')"
        printf '%s' "$APP_KEY" > "$key_file"
    fi
    export APP_KEY
fi

attempt=0
until php -r '$dsn = sprintf("mysql:host=%s;port=%s;dbname=%s", getenv("DB_HOST"), getenv("DB_PORT") ?: "3306", getenv("DB_DATABASE")); new PDO($dsn, getenv("DB_USERNAME"), getenv("DB_PASSWORD"));' >/dev/null 2>&1; do
    attempt=$((attempt + 1))
    if [ "$attempt" -ge 60 ]; then
        echo "MySQL did not become ready in time" >&2
        exit 1
    fi
    sleep 2
done

php artisan aimeos:demo-seed --no-interaction
php artisan optimize
chown -R www-data:www-data storage bootstrap/cache

exec apache2-foreground
