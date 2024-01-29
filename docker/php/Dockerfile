FROM php:8.2-fpm

COPY ./docker/php/php.ini /usr/local/etc/php/

RUN apt-get update && apt-get install -y git zip unzip vim \
    && docker-php-ext-install pdo_mysql

# composerのインストール
RUN curl -s http://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html
