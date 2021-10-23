FROM bitnami/symfony:1

WORKDIR /app
COPY . .
RUN composer install
