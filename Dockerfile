FROM php:8.3-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        libcurl4-openssl-dev libfreetype6-dev libicu-dev libjpeg62-turbo-dev \
        libonig-dev libpng-dev libzip-dev unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" bcmath curl exif gd intl mbstring opcache pdo_mysql zip \
    && a2enmod headers rewrite \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
COPY docker/apache-vhost.conf /etc/apache2/sites-available/000-default.conf
COPY docker/php/99-z-opcache-jit.ini /usr/local/etc/php/conf.d/99-z-opcache-jit.ini
COPY docker/entrypoint.sh /usr/local/bin/aimeos-demo-entrypoint

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --no-scripts --no-autoloader

COPY . .

RUN composer dump-autoload --no-dev --optimize --no-interaction \
    && php artisan vendor:publish --tag=public --force \
    && php artisan vendor:publish --tag=laravel-assets --force \
    && php docker/patch-aimeos-theme.php \
    && mkdir -p storage/app storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod +x /usr/local/bin/aimeos-demo-entrypoint

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/aimeos-demo-entrypoint"]
