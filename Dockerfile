# Gunakan Alpine versi yang lebih spesifik
FROM alpine:3.18

# Set direktori kerja
WORKDIR /var/www/report/web-ntbs

# Set timezone
RUN echo "UTC" > /etc/timezone

# Install paket-paket esensial
RUN apk add --no-cache zip unzip curl sqlite nginx supervisor bash

# Install PHP dan ekstensi yang diperlukan
RUN apk add --no-cache php8 php8-fpm php8-opcache php8-pdo php8-pdo_mysql php8-pdo_sqlite \
    php8-curl php8-mbstring php8-json php8-xml php8-iconv php8-zip php8-phar php8-tokenizer \
    php8-fileinfo php8-simplexml php8-dom php8-pecl-redis

# Buat symlink ke PHP
RUN ln -s /usr/bin/php8 /usr/bin/php

# Install Composer
RUN curl -sS https://getcomposer.org/installer -o composer-setup.php
RUN php composer-setup.php --install-dir=/usr/local/bin --filename=composer
RUN rm -rf composer-setup.php

# Konfigurasi Supervisor
RUN mkdir -p /etc/supervisor.d/
COPY .docker/supervisord.ini /etc/supervisor.d/supervisord.ini

# Konfigurasi PHP
RUN mkdir -p /run/php/
RUN touch /run/php/php-fpm.pid

COPY .docker/php-fpm.conf /etc/php8/php-fpm.conf
COPY .docker/php.ini-production /etc/php8/php.ini

# Konfigurasi Nginx
COPY .docker/nginx.conf /etc/nginx/

RUN mkdir -p /run/nginx/
RUN touch /run/nginx/nginx.pid

RUN ln -sf /dev/stdout /var/log/nginx/access.log
RUN ln -sf /dev/stderr /var/log/nginx/error.log

# Proses pembangunan aplikasi
COPY . .
RUN composer install --no-dev
RUN chown -R nobody:nobody /var/www/report/web-ntbs/storage

# Expose port 80
EXPOSE 80

# Jalankan Supervisor
CMD ["supervisord", "-c", "/etc/supervisor.d/supervisord.ini"]
