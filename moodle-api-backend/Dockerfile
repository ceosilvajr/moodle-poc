# Dockerfile
# This Dockerfile builds the Docker image for your Laravel PHP-FPM service.
# It starts with an official PHP-FPM image, installs necessary extensions,
# and sets up the Nginx web server configuration.

# Use a specific PHP-FPM image for consistency. PHP 8.2 is required for Laravel 11.
FROM php:8.2-fpm-alpine

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apk add --no-cache \
    nginx \
    mysql-client \
    curl \
    libzip-dev \
    zip \
    unzip \
    git \
    nodejs \
    npm

# Install PHP extensions required by Laravel and Moodle API interactions
# pdo_mysql for database connection
# dom, xml, gd for various PHP functionalities
# curl for making HTTP requests to Moodle API
# zip for composer install
RUN docker-php-ext-install pdo_mysql bcmath dom xml gd curl zip

# Install Composer (PHP dependency manager)
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Copy the Nginx configuration file
# This assumes you have an `nginx` directory in your project root with `default.conf`
COPY nginx/default.conf /etc/nginx/conf.d/default.conf

# Expose port 9000 for PHP-FPM
EXPOSE 9000

# Start PHP-FPM
CMD ["php-fpm"] 