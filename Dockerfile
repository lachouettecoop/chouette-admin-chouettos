FROM bitnami/symfony:4.4.34

WORKDIR /app/
COPY . .
RUN composer install
