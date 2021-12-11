FROM bitnami/symfony:4.4

WORKDIR /app/
COPY . .
RUN composer install
