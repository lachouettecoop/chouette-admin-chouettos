FROM php:8.3-fpm-alpine3.21

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN apk add --no-cache \
    git \
    bash \
    openldap-dev \
    && docker-php-ext-configure ldap \
    && docker-php-ext-install ldap pdo pdo_mysql

RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.alpine.sh' | bash && apk add symfony-cli



WORKDIR /var/www/html
COPY . .

RUN composer install
