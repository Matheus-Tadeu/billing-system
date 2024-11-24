#!/bin/bash

cd /var/www

composer install

if [ ! -f .env ]; then
  cp .env.example .env
fi

if [ ! -f .env ]; then
  cp .env.example .env
fi

if [ ! -f ..env.testing ]; then
  cp .env.testing.example .env.testing
fi

chmod 777 .env .env.testing

php artisan key:generate
php artisan key:generate --env=testing

service supervisor start

php artisan serve --host=0.0.0.0 --port=80
