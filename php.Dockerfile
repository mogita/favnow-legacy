FROM php:7.2-apache

COPY ./app /server
COPY ./.env /server
COPY ./vhost.conf /etc/apache2/sites-available/000-default.conf

WORKDIR /server

RUN apt-get update
RUN apt-get upgrade -y

RUN docker-php-ext-install pdo pdo_mysql mysqli
RUN a2enmod rewrite
