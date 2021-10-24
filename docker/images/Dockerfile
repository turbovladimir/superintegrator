FROM php:7.2-fpm

#----------------------------------------------------------
# Common
#----------------------------------------------------------

RUN apt-get update && apt-get install -y \
    apt-utils \
    libcurl4-openssl-dev \
    pkg-config \
    libssl-dev \
    zlib1g-dev \
    unzip \
    build-essential \
    gnupg \
    libz-dev \
    libmemcached-dev \
    libtool \
    automake \
    python-dev \
    git \
    procps \
    sudo \
    libicu-dev \
    libpng-dev \
    libgmp-dev \
    g++ \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    telnet \
    nano \
    htop \
    wget

#----------------------------------------------------------
# PHP Libs
#----------------------------------------------------------
RUN docker-php-ext-configure intl \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install \
    sockets \
    pdo \
    pdo_mysql \
    mysqli \
    bcmath \
    zip \
    mbstring \
    gd \
    gmp \
    intl \
    && curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer \
    && pecl install xdebug-2.6.1 \
    && docker-php-ext-enable xdebug \
    && echo "xdebug.remote_enable=1" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_autostart=off" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.profiler_enable=0" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.profiler_enable_trigger=1" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.profiler_output_dir=/var/www/profiler" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "memory_limit=3072M" >> /usr/local/etc/php/conf.d/common.ini \
    && echo "upload_max_filesize=100M" >> /usr/local/etc/php/conf.d/common.ini \
    && echo "post_max_size=100M" >> /usr/local/etc/php/conf.d/common.ini \
    && echo "max_execution_time=300" >> /usr/local/etc/php/conf.d/common.ini \
    && echo 'date.timezone = "Europe/Moscow"\n' > /usr/local/etc/php/conf.d/timezone.ini \
    && mkdir /var/www/profiler

RUN useradd -ms /bin/bash app
USER app