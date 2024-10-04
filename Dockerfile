FROM alpine:latest

WORKDIR /var/www/html/

# Essentials
RUN echo "UTC" > /etc/timezone
RUN apk add --no-cache zip unzip curl sqlite nginx supervisor

# Installing bash
RUN apk add bash
RUN sed -i 's/bin\/ash/bin\/bash/g' /etc/passwd

# Installing PHP 7.2
RUN apk add --no-cache php7 \
    php7-fpm \
    php7-opcache \
    php7-pdo \
    php7-pdo_mysql \
    php7-pdo_sqlite \
    php7-curl \
    php7-mbstring \
    php7-json \
    php7-xml \
    php7-iconv \
    php7-zip \
    php7-phar \
    php7-tokenizer \
    php7-fileinfo \
    php7-simplexml \
    php7-dom \
    php7-pecl-redis

RUN ln -s /usr/bin/php7 /usr/bin/php

# Installing composer
RUN curl -sS https://getcomposer.org/installer -o composer-setup.php
RUN php composer-setup.php --install-dir=/usr/local/bin --filename=composer
RUN rm -rf composer-setup.php

# Configure supervisor
RUN mkdir -p /etc/supervisor.d/
COPY .docker/supervisord.ini /etc/supervisor.d/supervisord.ini

# Configure PHP
RUN mkdir -p /run/php/
RUN touch /run/php/php7-fpm.pid

COPY .docker/php-fpm.conf /etc/php7/php-fpm.conf
COPY .docker/php.ini-production /etc/php7/php.ini

# Configure nginx
COPY .docker/nginx.conf /etc/nginx/

RUN mkdir -p /run/nginx/
RUN touch /run/nginx/nginx.pid

RUN ln -sf /dev/stdout /var/log/nginx/access.log
RUN ln -sf /dev/stderr /var/log/nginx/error.log

# Building process
COPY . .
RUN composer install --no-dev
RUN chown -R nobody:nobody /var/www/report/web-ntbs/storage

EXPOSE 80
CMD ["supervisord", "-c", "/etc/supervisor.d/supervisord.ini"]
