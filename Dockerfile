FROM php:8
COPY . /app
WORKDIR /app

# php/composer/php-mysql setup
RUN apt update && apt install -y git libzip4 libzip-dev zip unzip
RUN docker-php-ext-install pdo pdo_mysql zip

# Install composer and install deps
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
RUN composer install 

CMD ["php", "-S", "0.0.0.0:8000", "src/index.php"]
