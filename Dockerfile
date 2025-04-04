# Use PHP 8 with Apache
FROM php:8.0-apache

# Install required system packages and PHP extensions
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libldap2-dev \
    && docker-php-ext-configure ldap --with-ldap \
    && docker-php-ext-install pdo pdo_pgsql pgsql ldap

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Set permissions (not necessary if you're mounting, but harmless)
RUN chown -R www-data:www-data /var/www/html

# Allow .htaccess overrides
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Expose port 80
EXPOSE 80

# Start Apache server
CMD ["apache2-foreground"]
