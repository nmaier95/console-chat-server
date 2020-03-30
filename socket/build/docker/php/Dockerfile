FROM php:7.4-fpm
RUN apt-get update

RUN pecl install xdebug \
    && docker-php-ext-install sockets

#RUN echo "xdebug.remote_host=host.docker.internal" >> /usr/local/etc/php/conf.d/zz_xdebug.ini && \
#    echo "xdebug.remote_enable=1" >>  /usr/local/etc/php/conf.d/zz_xdebug.ini && \
#    echo "xdebug.remote_autostart=1" >>  /usr/local/etc/php/conf.d/zz_xdebug.ini && \
#    echo "xdebug.max_nesting_level=400" >>  /usr/local/etc/php/conf.d/zz_xdebug.ini

COPY ./build/docker/php/entrypoint /usr/local/bin/
RUN chmod +x /usr/local/bin/entrypoint


ENTRYPOINT ["entrypoint"]

WORKDIR /var/www
#USER www-data

