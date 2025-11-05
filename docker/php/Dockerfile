FROM php:8.3-fpm

# Install system dependencies and PHP extensions needed for Laravel
RUN apt-get update && apt-get install -y --no-install-recommends \
        git \
        curl \
        unzip \
        libpq-dev \
        libzip-dev \
        libpng-dev \
        libjpeg-dev \
        libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        pdo \
        pdo_pgsql \
        bcmath \
        exif \
        pcntl \
        gd \
        zip \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && rm -rf /var/lib/apt/lists/* /tmp/pear

# Copy Composer from the official image
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# PHP runtime tweaks
COPY docker/php/docker-fpm.ini "$PHP_INI_DIR/conf.d/docker-fpm.ini"

# www-data user owns project (helps for cache/storage permissions)
RUN chown -R www-data:www-data /var/www/html || true

# Default command
CMD ["php-fpm"]
