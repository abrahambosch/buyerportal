#!/bin/bash


composer install

php artisan migrate --seed


