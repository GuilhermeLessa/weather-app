FROM php:8.3.12-fpm 

WORKDIR /var/www

RUN rm -rf /var/www/html

RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    libzip-dev \
    apt-utils \
    libonig-dev

RUN docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/
RUN docker-php-ext-install zip pdo_mysql mbstring exif gd

RUN pecl install xdebug && docker-php-ext-enable xdebug

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN groupadd -g 1000 laravel
RUN useradd -u 1000 -ms /bin/bash -g laravel user

RUN rm -rf /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
COPY --chown=root:root docker-php-ext-xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

USER user

EXPOSE 9000

CMD ["php-fpm"]