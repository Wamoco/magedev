FROM php:8.1-apache
RUN apt-get update

RUN apt-get install -y --no-install-recommends libxml2-dev
RUN docker-php-ext-install -j$(nproc) simplexml
RUN docker-php-ext-install -j$(nproc) soap
RUN docker-php-ext-enable soap
RUN docker-php-ext-enable simplexml

