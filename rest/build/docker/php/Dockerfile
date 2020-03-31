FROM php:7.4-fpm
#RUN apt-get update

#RUN pecl install xdebug

RUN apt-get update && \
    pecl install xdebug && \
    docker-php-ext-install pdo pdo_mysql && \
    docker-php-ext-enable pdo && \
    docker-php-ext-enable pdo_mysql

#RUN docker-php-ext-install openssl
#RUN docker-php-ext-install mbstring

#RUN docker-php-ext-enable pdo pdo_mysql
#RUN docker-php-ext-enable
#RUN docker-php-ext-enable openssl
#RUN docker-php-ext-enable mbstring

#RUN touch /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
#    chown -R www-data:www-data /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

COPY ./build/docker/php/entrypoint /usr/local/bin/
RUN chmod +x /usr/local/bin/entrypoint


ENTRYPOINT ["entrypoint", "php-fpm"]

WORKDIR /var/www/html
#USER www-data/

