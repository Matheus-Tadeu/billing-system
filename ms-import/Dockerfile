FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    libssl-dev \
    netcat-openbsd \
    supervisor && \
    docker-php-ext-install sockets && \
    pecl install redis mongodb && \
    docker-php-ext-enable redis mongodb

WORKDIR /var/www

COPY ms-import /var/www

COPY ./php.ini /usr/local/etc/php/

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN chmod +x /var/www/docker-entrypoint.sh

EXPOSE 80
