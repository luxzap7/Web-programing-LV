FROM php:8.2-apache

# Instaliraj PHP MySQL ekstenzije
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Postavi DocumentRoot na public/ folder
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Kopiraj projekt
COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
