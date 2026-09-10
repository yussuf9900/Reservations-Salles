FROM php:8.3-apache

ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get update && apt-get install -y \
    curl \
    git \
    unzip \
    libicu-dev \
    libzip-dev \
    && docker-php-ext-install pdo_mysql intl zip \
    && a2enmod rewrite \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY docker/php-sessions.ini /usr/local/etc/php/conf.d/sessions.ini
RUN install -d -m 1733 /var/lib/php/sessions

COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

COPY . .

RUN composer dump-autoload --optimize --no-dev
RUN if [ ! -f .env ]; then cp .env.example .env; fi
RUN chown -R www-data:www-data /var/www/html \
    && chmod +x youssou:migrate youssou:seed youssou

EXPOSE 80

HEALTHCHECK --interval=30s --timeout=5s --start-period=10s --retries=3 \
    CMD curl -f http://localhost/login || exit 1

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
