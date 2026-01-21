FROM php:8.2-apache

WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git unzip libicu-dev libzip-dev libpq-dev zip \
    && docker-php-ext-install intl pdo pdo_pgsql zip

# Enable Apache modules
RUN a2enmod rewrite headers

# Configure PHP settings
RUN printf "upload_max_filesize=100M\npost_max_size=120M\nmemory_limit=256M\nmax_execution_time=300\nmax_input_time=300\n" > /usr/local/etc/php/conf.d/uploads.ini

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy application files
COPY . .

# Install dependencies
RUN composer install --optimize-autoloader --no-scripts
COPY railway_autoload_runtime.php vendor/autoload_runtime.php
RUN composer dump-autoload --optimize --classmap-authoritative --no-dev

ENV APP_ENV=prod
ENV TRUSTED_PROXIES=*

# Configure Apache DocumentRoot to /public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Configure Apache to listen on Railway's PORT (default 80 if not set)
RUN sed -i 's/80/${PORT:-80}/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

# Create upload directories with permissions
RUN mkdir -p public/uploads/annonces public/uploads/profiles public/uploads/products public/uploads/posts \
    && chown -R www-data:www-data public/uploads \
    && chmod -R 775 public/uploads

# Start Apache
CMD sh -c "php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration && apache2-foreground"
