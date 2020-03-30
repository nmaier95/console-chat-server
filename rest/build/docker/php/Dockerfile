FROM php:7.4-fpm
RUN apt-get update

RUN pecl install xdebug

#RUN docker-php-ext-install pdo
#RUN docker-php-ext-install openssl
#RUN docker-php-ext-install mbstring

#RUN docker-php-ext-enable pdo
#RUN docker-php-ext-enable pdo_mysql
#RUN docker-php-ext-enable openssl
#RUN docker-php-ext-enable mbstring


COPY ./build/docker/php/entrypoint /usr/local/bin/
RUN chmod +x /usr/local/bin/entrypoint


ENTRYPOINT ["entrypoint", "php-fpm"]

WORKDIR /var/www/html
#USER www-data

