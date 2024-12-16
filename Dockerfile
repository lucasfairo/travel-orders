FROM php:8.2-apache

RUN pecl install xdebug && docker-php-ext-enable xdebug

RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libbz2-dev \
    libicu-dev \
    libldap2-dev \
    libpq-dev \
    libzip-dev \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_mysql zip

# Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# DocumentRoot  /var/www/html/public
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# mod_rewrite
RUN a2enmod rewrite

# .htaccess
RUN sed -i '/<Directory \/var\/www\/html\/public>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/g' /etc/apache2/sites-available/000-default.conf

COPY . /var/www/html

#RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
