#!/bin/bash

cd /var/www

composer install

if [ ! -f .env ]; then
  cp .env.example .env
fi

chmod 777 .env

php artisan key:generate

#echo "Aguardando o banco de dados..."
#while ! nc -z mongodb 27017; do
#  sleep 1
#done

#php artisan migrate

php artisan serve --host=0.0.0.0 --port=80
