FROM bitnami/symfony:4.4.34

WORKDIR /app/
COPY . .
RUN composer config --no-plugins allow-plugins.symfony/flex true
RUN COMPOSER_ALLOW_SUPERUSER=1 composer install
