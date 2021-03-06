FROM php:7.4-apache

COPY ./app /server
COPY ./.env /server
COPY ./vhost.conf /etc/apache2/sites-available/000-default.conf

WORKDIR /server

RUN apt-get update
RUN apt-get upgrade -y
RUN apt-get install -y git libmcrypt-dev libmagickwand-dev libpng-dev sendmail libfreetype6-dev libonig-dev
RUN pecl install imagick

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
RUN composer install

RUN docker-php-ext-configure gd --with-freetype
RUN docker-php-ext-install -j$(nproc) gd
RUN docker-php-ext-install pdo pdo_mysql mysqli mbstring
RUN docker-php-ext-enable imagick
RUN echo "ServerName favnow.mogita.rocks" | tee /etc/apache2/conf-available/fqdn.conf && a2enconf fqdn
RUN a2enmod rewrite
RUN service apache2 restart
