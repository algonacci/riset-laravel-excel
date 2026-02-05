FROM serversideup/php:8.5-frankenphp

USER root

# Install intl extension
RUN install-php-extensions intl

# Copy composer files first for better layer caching
COPY composer.json composer.lock* ./

# Install composer dependencies including Sanctum
RUN composer install --no-dev --optimize-autoloader --no-interaction || true

# Copy application code (this will be overridden by volume mount at runtime for development)
COPY . .

# Ensure vendor directory exists and has correct permissions
RUN chmod -R 755 /var/www/html/vendor || true

# Run Laravel migrations and optimizations
RUN php artisan migrate --force || true
RUN php artisan optimize || true

USER www-data
