FROM php:8.4-fpm

# 1. Install library sistem yang dibutuhkan
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    libicu-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    nodejs \
    npm

# 2. Install ekstensi PHP untuk Laravel & Filament
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql intl zip gd bcmath

# 3. Install Composer (Copy dari image resmi Composer)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 4. Set folder kerja
WORKDIR /var/www