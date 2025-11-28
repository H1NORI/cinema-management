FROM php:8.3-fpm

# Устанавливаем зависимости
RUN apt-get update && apt-get install -y \
    curl \
    git \
    unzip \
    zip \
    libicu-dev \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libwebp-dev \
    && docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
        --with-webp \
    && docker-php-ext-install pdo pdo_mysql intl zip gd \
    && rm -rf /var/lib/apt/lists/*

# Устанавливаем composer
RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin --filename=composer


RUN usermod -u 1000 www-data && groupmod -g 1000 www-data

WORKDIR /var/www
USER 1000:1000


CMD ["php-fpm"]