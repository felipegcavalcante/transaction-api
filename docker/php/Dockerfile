FROM php:8.3-fpm

RUN apt-get update \
    && apt-get install -y \
    libpq-dev \
    libxml2-dev \
    libssl-dev \
    libicu-dev \
    libxslt1-dev \
    libzip-dev \
    libbz2-dev \
    libonig-dev \
    && docker-php-ext-install \
    bcmath \
    dom \
    intl \
    mbstring \
    pdo \
    pdo_mysql \
    xml \
    xsl \
    zip

COPY ../.. /var/www/html

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

ENV TZ='America/Sao_Paulo'

COPY --from=composer/composer:latest-bin /composer /usr/bin/composer

COPY php.ini /etc/php83/conf.d/99_php.ini

RUN mkdir -p /var/www/html/var/cache \
    && mkdir -p /var/www/html/var/logs \
    && chown -R www-data:www-data /var/www/html/var/cache \
    && chown -R www-data:www-data /var/www/html/var/logs

USER www-data

EXPOSE 9000
