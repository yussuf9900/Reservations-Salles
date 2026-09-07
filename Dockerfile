FROM php:8.3-apache

ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get update && apt-get install -y \
    mariadb-server \
    mariadb-client \
    git \
    unzip \
    libicu-dev \
    libzip-dev \
    && docker-php-ext-install pdo_mysql intl \
    && a2enmod rewrite \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

COPY . .

RUN if [ ! -f .env ]; then cp .env.example .env; fi
RUN chown -R www-data:www-data /var/www/html \
    && chmod +x youssou:migrate youssou:seed youssou

EXPOSE 80 3306

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
