FROM php:8.2-apache

# Enable mod_rewrite
RUN a2enmod rewrite

# Copy all files to the Apache web root
COPY . /var/www/html/

# Set permissions
RUN chown -R www-data:www-data /var/www/html
