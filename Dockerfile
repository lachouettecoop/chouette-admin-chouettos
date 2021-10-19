FROM php:8.0-fpm

RUN apt update && apt install -y zlib1g-dev g++ git libicu-dev zip libzip-dev zip libldb-dev libldap2-dev \
    && docker-php-ext-install intl opcache pdo pdo_mysql \
    && pecl install apcu \
    && docker-php-ext-enable apcu \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip \
    && docker-php-ext-configure ldap \
    && docker-php-ext-install ldap

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/symfony_docker

COPY . .

RUN /usr/local/bin/composer install
