#php-fpm
FROM php:7.1-fpm
MAINTAINER Abraham Bosch <abrahambosch@gmail.com>

RUN apt-get update && apt-get install -y libmcrypt-dev \
    mysql-client libmagickwand-dev --no-install-recommends \
    && pecl install imagick \
    && docker-php-ext-enable imagick \
    && docker-php-ext-install mcrypt pdo_mysql

RUN curl -sS https://getcomposer.org/installer | php -- --filename=composer --install-dir=/bin
ENV PATH /root/.composer/vendor/bin:$PATH

RUN apt-get update && apt-get install -y git zip 


