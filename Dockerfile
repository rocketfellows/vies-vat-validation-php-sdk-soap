FROM php:7.4-fpm
RUN apt-get update && apt-get install -y \
    zlib1g-dev \
    libzip-dev \
    unzip \
    mc \
    libxml2-dev
RUN docker-php-ext-install zip
RUN docker-php-ext-install soap && docker-php-ext-enable soap
RUN pecl install xdebug-3.1.5
RUN docker-php-ext-enable xdebug
RUN mkdir -p /home/app
WORKDIR /home/app
COPY . /home/app
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer