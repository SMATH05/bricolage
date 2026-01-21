FROM php:8.2-cli

WORKDIR /app

RUN apt-get update && apt-get install -y \
    git unzip libicu-dev libzip-dev libpq-dev zip \
    && docker-php-ext-install intl pdo pdo_pgsql zip

RUN printf "upload_max_filesize=100M\npost_max_size=120M\nmemory_limit=256M\nmax_execution_time=300\nmax_input_time=300\n" > /usr/local/etc/php/conf.d/uploads.ini

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY . .

RUN composer install --optimize-autoloader --no-scripts
COPY railway_autoload_runtime.php vendor/autoload_runtime.php
RUN composer dump-autoload --optimize --classmap-authoritative --no-dev

ENV APP_ENV=dev
ENV TRUSTED_PROXIES=*

# Create uploads directory structure
RUN mkdir -p public/uploads/annonces public/uploads/profiles public/uploads/products public/uploads/posts
RUN chmod -R 777 public/uploads

# Also create tmp storage as fallback
RUN mkdir -p /tmp/bricolage_uploads && chmod -R 777 /tmp/bricolage_uploads

CMD sh -c "mkdir -p public/uploads/annonces public/uploads/profiles public/uploads/products public/uploads/posts && mkdir -p /tmp/bricolage_uploads && chmod -R 777 public/uploads && chmod -R 777 /tmp/bricolage_uploads && php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration && php -S 0.0.0.0:${PORT:-80} -t public"
