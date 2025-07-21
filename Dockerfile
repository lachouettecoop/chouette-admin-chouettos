FROM php:8.3-fpm-alpine3.21

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN apk add --no-cache \
    git \
    openldap-dev \
    && docker-php-ext-configure ldap \
    && docker-php-ext-install ldap pdo pdo_mysql

WORKDIR /var/www/html
COPY . .

RUN composer install
