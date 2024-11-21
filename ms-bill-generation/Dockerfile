FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip && \
    pecl install redis  && \
    docker-php-ext-enable redis

WORKDIR /var/www

COPY ms-bill-generation /var/www

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN chmod +x /var/www/docker-entrypoint.sh

EXPOSE 80