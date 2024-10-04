# Gunakan Alpine versi yang lebih spesifik
FROM alpine:3.16

# Set direktori kerja
WORKDIR /var/www/report/web-ntbs

# Set timezone
RUN echo "UTC" > /etc/timezone

# Install paket-paket esensial
RUN apk add --no-cache zip unzip curl sqlite nginx supervisor bash

# Install PHP 7.4 dan ekstensi yang diperlukan
RUN apk add --no-cache php74 php74-fpm php74-opcache php74-pdo php74-pdo_mysql php74-pdo_sqlite \
    php74-curl php74-mbstring php74-json php74-xml php74-iconv php74-zip php74-phar php74-tokenizer \
    php74-fileinfo php74-simplexml php74-dom php74-pecl-redis

# Buat symlink ke PHP jika tidak ada
RUN if [ ! -e /usr/bin/php ]; then ln -s /usr/bin/php74 /usr/bin/php; fi

# Install Composer
RUN curl -sS https://getcomposer.org/installer -o composer-setup.php \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && rm -rf composer-setup.php

# Konfigurasi Supervisor
RUN mkdir -p /etc/supervisor.d/
COPY .docker/supervisord.ini /etc/supervisor.d/supervisord.ini

# Konfigurasi PHP
RUN mkdir -p /run/php/
RUN touch /run/php/php-fpm.pid

COPY .docker/php-fpm.conf /etc/php74/php-fpm.conf
COPY .docker/php.ini-production /etc/php74/php.ini

# Konfigurasi Nginx
COPY .docker/nginx.conf /etc/nginx/

RUN mkdir -p /run/nginx/
RUN touch /run/nginx/nginx.pid

RUN ln -sf /dev/stdout /var/log/nginx/access.log \
    && ln -sf /dev/stderr /var/log/nginx/error.log

# Proses pembangunan aplikasi
COPY . .
RUN composer install --no-dev \
    && chown -R nobody:nobody /var/www/report/web-ntbs/storage

# Expose port 80
EXPOSE 80

# Jalankan Supervisor
CMD ["supervisord", "-c", "/etc/supervisor.d/supervisord.ini"]
