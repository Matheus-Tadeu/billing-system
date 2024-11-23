#!/bin/bash

cd /var/www

composer install

if [ ! -f .env ]; then
  cp .env.example .env
fi

chmod 777 .env

php artisan key:generate

service supervisor start

php artisan serve --host=0.0.0.0 --port=80
