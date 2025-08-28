# Base image: PHP 8.4 with FPM
FROM php:8.4-fpm

# Install system dependencies and PHP extensions required for Laravel
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && rm -rf /var/lib/apt/lists/*

# Install Composer from the official image
COPY --from=composer:2.8.11 /usr/bin/composer /usr/bin/composer

# Set working directory inside the container
WORKDIR /var/www/html

# Copy Laravel files into the container (adjust if located in "src")
COPY ./src /var/www/html

# Install Laravel PHP dependencies (without running scripts yet)
RUN composer install --no-scripts --no-interaction --no-progress --prefer-dist && \
    composer dump-autoload --optimize

# Give write permissions to storage and bootstrap/cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose the port for php artisan serve
EXPOSE 8000

# Default command: start Laravel server
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
