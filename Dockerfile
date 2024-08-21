# Use official PHP image.
FROM php:8.1-fpm

# Set environment variables
ENV PHP_OPCACHE_ENABLE=1
ENV PHP_OPCACHE_ENABLE_CLI=1
ENV PHP_OPCACHE_VALIDATE_TIMESTAMPS=1
ENV PHP_OPCACHE_REVALIDATE_FREQ=1

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libzip-dev \
    zlib1g-dev \
    libicu-dev \
    g++ \
    && docker-php-ext-install -j$(nproc) pdo pdo_mysql \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

#  Install dependencies.
RUN apt-get update && apt-get install -y unzip libpq-dev libcurl4-gnutls-dev nginx libonig-dev

# Install PHP extensions.
RUN docker-php-ext-install mysqli pdo pdo_mysql bcmath curl opcache mbstring

# Copy existing application directory contents
COPY . /var/www/recruitment-job

# Set permissions in a separate step
RUN chown -R www-data:www-data /var/www/recruitment-job \
    && chmod -R 755 /var/www/recruitment-job

# Set working directory
WORKDIR /var/www/recruitment-job

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy configuration files.
COPY ./docker/php/php.ini /usr/local/etc/php/php.ini
COPY ./docker/php/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf
COPY ./docker/nginx/nginx.conf /etc/nginx/nginx.conf

# Run entrypoint script
RUN ["chmod", "+x", "docker/entrypoint.sh"]

ENTRYPOINT ["docker/entrypoint.sh" ]
