# Frontend assets
FROM node:22-alpine AS frontend
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
RUN npm run build

# Laravel + Apache
FROM php:8.3-apache-bookworm

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf \
    && sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/docker-php.conf

RUN a2enmod rewrite headers \
    && printf '\nServerName localhost\n' >> /etc/apache2/apache2.conf

RUN apt-get update && apt-get install -y --no-install-recommends \
    libicu-dev libpng-dev libzip-dev libonig-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install -j$(nproc) intl pdo_mysql mysqli mbstring exif pcntl bcmath gd zip opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .
RUN rm -rf vendor

COPY --from=frontend /app/public/build ./public/build

RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress --ansi

COPY docker/php/docker-entrypoint.sh /usr/local/bin/app-docker-entrypoint.sh
RUN chmod +x /usr/local/bin/app-docker-entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/app-docker-entrypoint.sh"]
CMD ["apache2-foreground"]
