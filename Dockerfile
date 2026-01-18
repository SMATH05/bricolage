FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    libicu-dev \
    zip \
    unzip \
    && docker-php-ext-configure intl \
    && docker-php-ext-install pdo pdo_pgsql pgsql intl mbstring xml ctype iconv opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Enable Apache mod_rewrite and ensure correct MPM (Nuclear cleanup)
RUN rm -f /etc/apache2/mods-enabled/mpm_* \
    && a2enmod mpm_prefork rewrite

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Install PHP dependencies (without scripts to avoid DB connection during build)
RUN composer install --no-dev --optimize-autoloader --no-scripts --ignore-platform-reqs

# Create required directories
RUN mkdir -p var/cache var/log public/uploads/annonces public/uploads/profiles \
    && chmod -R 777 var public/uploads

# Configure Apache
RUN echo '<VirtualHost *:80>\n\
    DocumentRoot /var/www/html/public\n\
    <Directory /var/www/html/public>\n\
    AllowOverride All\n\
    Require all granted\n\
    FallbackResource /index.php\n\
    </Directory>\n\
    </VirtualHost>' > /etc/apache2/sites-available/000-default.conf

# Set environment variables
ENV APP_ENV=prod
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

# Expose port 80
EXPOSE 80

# Create entrypoint script internally to avoid CRLF issues
RUN echo '#!/bin/bash' > /usr/local/bin/docker-entrypoint.sh && \
    echo 'set -e' >> /usr/local/bin/docker-entrypoint.sh && \
    echo 'echo "Detected PORT variable: ${PORT}"' >> /usr/local/bin/docker-entrypoint.sh && \
    echo 'sed -i "s/Listen 80/Listen ${PORT:-80}/g" /etc/apache2/ports.conf' >> /usr/local/bin/docker-entrypoint.sh && \
    echo 'sed -i "s/<VirtualHost \*:80>/<VirtualHost \*:${PORT:-80}>/g" /etc/apache2/sites-available/000-default.conf' >> /usr/local/bin/docker-entrypoint.sh && \
    echo 'echo "Warming up cache..."' >> /usr/local/bin/docker-entrypoint.sh && \
    echo 'php bin/console cache:warmup --env=prod --no-interaction || true' >> /usr/local/bin/docker-entrypoint.sh && \
    echo 'echo "Running migrations..."' >> /usr/local/bin/docker-entrypoint.sh && \
    echo 'php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration || true' >> /usr/local/bin/docker-entrypoint.sh && \
    echo 'echo "Starting Apache on port ${PORT:-80}..."' >> /usr/local/bin/docker-entrypoint.sh && \
    echo 'exec apache2-foreground' >> /usr/local/bin/docker-entrypoint.sh && \
    chmod +x /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT ["docker-entrypoint.sh"]
CMD []
