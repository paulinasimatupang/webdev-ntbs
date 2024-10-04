# Gunakan Alpine versi 3.12, yang mendukung PHP 7.2
FROM alpine:3.12

# Set direktori kerja
WORKDIR /var/www/report/web-ntbs

# Set timezone
RUN echo "UTC" > /etc/timezone

# Install paket-paket esensial dan PHP 7.2 dengan ekstensi yang diperlukan
RUN apk add --no-cache \
    zip \
    unzip \
    curl \
    sqlite \
    nginx \
    supervisor \
    bash \
    php7 \
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
    php7-pecl-redis \
    php7-gd \
    php7-xmlreader

# Buat symlink ke PHP jika tidak ada
RUN ln -sf /usr/bin/php7 /usr/bin/php

# Install Composer
RUN curl -sS https://getcomposer.org/installer -o composer-setup.php \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && rm -rf composer-setup.php

# Konfigurasi Supervisor
RUN mkdir -p /etc/supervisor.d/
COPY .docker/supervisord.ini /etc/supervisor.d/supervisord.ini

# Konfigurasi PHP
RUN mkdir -p /run/php/ \
    && touch /run/php/php-fpm.pid

COPY .docker/php-fpm.conf /etc/php7/php-fpm.conf
COPY .docker/php.ini-production /etc/php7/php.ini

# Konfigurasi Nginx
COPY .docker/nginx.conf /etc/nginx/

RUN mkdir -p /run/nginx/ \
    && touch /run/nginx/nginx.pid

RUN ln -sf /dev/stdout /var/log/nginx/access.log \
    && ln -sf /dev/stderr /var/log/nginx/error.log

# Proses pembangunan aplikasi
COPY . . 

# Instal dependensi PHP dengan Composer
RUN composer install --no-dev \
    && chown -R nobody:nobody /var/www/report/web-ntbs/storage

# Expose port 80
EXPOSE 80

# Jalankan Supervisor
CMD ["supervisord", "-c", "/etc/supervisor.d/supervisord.ini"]
