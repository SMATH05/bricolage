# Force rebuild - 2026-01-20 v2
FROM php:8.2-cli

WORKDIR /app

RUN apt-get update && apt-get install -y \
    git unzip libicu-dev libzip-dev libpq-dev zip \
    && docker-php-ext-install intl pdo pdo_pgsql zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY . .

RUN composer install --optimize-autoloader --no-scripts
COPY railway_autoload_runtime.php vendor/autoload_runtime.php
RUN mkdir -p public/uploads/annonces public/uploads/profiles public/uploads/products && \
    chmod -R 777 public/uploads

ENV APP_ENV=dev
ENV TRUSTED_PROXIES=*

CMD sh -c "php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration && php -S 0.0.0.0:${PORT:-8080} -t public"
