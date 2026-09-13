FROM php:8.2-apache

# MySQL extension enable karein
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Apache mod_rewrite enable karein
RUN a2enmod rewrite

# Code copy karein
COPY . /var/www/html/

# Permissions set karein
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Render default port
EXPOSE 80
