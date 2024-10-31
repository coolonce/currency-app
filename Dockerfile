# Используем образ PHP 8.3
FROM php:8.3-cli

# Установка необходимых расширений
RUN apt-get update && apt-get install -y \
    zip \
    unzip \
    curl \
    && docker-php-ext-install pdo pdo_mysql sockets \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Установка Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY . /var/www

WORKDIR /var/www

RUN chmod +x /var/www/wait-for-it.sh

RUN composer install

CMD ["php", "app/index.php"]