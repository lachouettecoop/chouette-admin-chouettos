FROM bitnami/symfony:1

WORKDIR /app/adminchouettos
COPY . .
RUN composer install
