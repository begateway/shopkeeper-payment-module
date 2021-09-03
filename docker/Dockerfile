FROM php:7.2-apache

USER root

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    wget libzip-dev unzip vim less libxml2 libxml2-dev libgd-dev \
    libpng-dev libwebp-dev libjpeg62-turbo-dev \
    libxpm-dev libfreetype6-dev && \
    docker-php-ext-configure gd \
    --with-gd \
    --with-webp-dir \
    --with-jpeg-dir \
    --with-png-dir \
    --with-zlib-dir \
    --with-xpm-dir \
    --with-freetype-dir && \
    docker-php-ext-install mysqli zip xml gd pdo pdo_mysql && \
    apt-get remove -y libzip-dev libgd-dev \
    libpng-dev libwebp-dev libjpeg62-turbo-dev \
    libxpm-dev libfreetype6-dev libxml2-dev && \
    rm -rf /var/lib/apt/lists/* && \
    /usr/sbin/a2enmod rewrite

RUN wget "https://modx.com/download/direct?id=modx-2.8.3-pl.zip&0=abs" \
    -O modx-2.8.3-pl.zip && \
    unzip modx-2.8.3-pl.zip && \
    mv modx-2.8.3-pl/* . && \
    rmdir modx-2.8.3-pl && \
    rm modx-2.8.3-pl.zip && \
    chown -R www-data.www-data /var/www/html

VOLUME ["/var/www/html"]
