FROM bitnami/symfony:1

WORKDIR /app
COPY . ./adminchouettos
RUN cd ./adminchouettos && composer install
