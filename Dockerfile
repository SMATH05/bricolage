FROM php:8.2-apache

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    git unzip libicu-dev libzip-dev libpq-dev zip \
    && docker-php-ext-install intl pdo pdo_pgsql zip \
    && a2enmod rewrite

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Enable Apache remote IP for proxies
RUN echo "RemoteIPHeader X-Forwarded-For" > /etc/apache2/conf-available/remoteip.conf \
    && a2enconf remoteip

COPY . .

RUN composer install --optimize-autoloader --no-scripts
COPY railway_autoload_runtime.php vendor/autoload_runtime.php

RUN mkdir -p public/uploads/annonces public/uploads/profiles public/uploads/products public/uploads/posts && \
    chmod -R 777 public/uploads

ENV APP_ENV=dev
ENV TRUSTED_PROXIES=*

# Apache port configuration for Railway
RUN sed -i 's/Listen 80/Listen ${PORT}/' /etc/apache2/ports.conf
RUN sed -i 's/:80/:${PORT}/' /etc/apache2/sites-available/000-default.conf

CMD ["sh", "-c", "php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration; apache2-foreground"]
