FROM php:8.2-fpm

# Install system dependencies and build libraries
RUN DEBIAN_FRONTEND=noninteractive apt-get update && \
    DEBIAN_FRONTEND=noninteractive apt-get install -y --no-install-recommends \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    nginx && \
    rm -rf /var/lib/apt/lists/*

# Install required PHP extensions (parallel compilation added with -j$(nproc))
RUN docker-php-ext-install -j$(nproc) pdo_mysql mbstring exif bcmath gd zip

# Copy Composer binary from official Composer image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copy application files
COPY . /var/www

# Install Laravel PHP dependencies via Composer
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Set appropriate directory permissions
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

EXPOSE 80

# Start Laravel development server
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=80"]
