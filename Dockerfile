ARG PHP_VERSION=8.1
FROM php:${PHP_VERSION}-cli
COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN pecl install xdebug && docker-php-ext-enable xdebug

RUN echo '\
zend_extension=xdebug\n\
\n\
[xdebug]\n\
xdebug.mode=develop,debug\n\
xdebug.client_host=host.docker.internal\n\
xdebug.start_with_request=yes\n\
' > /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

RUN echo "error_reporting=E_ALL" > /usr/local/etc/php/conf.d/error_reporting.ini

WORKDIR /code