# Use the official PHP image
FROM php:7.4-apache

# Install required extensions
RUN apt-get install -y libpq-dev \
  && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
  && docker-php-ext-install pdo pdo_pgsql pgsql

# Enable Apache modules
RUN a2enmod rewrite

# Set up working directory
WORKDIR /var/www/html

# Copy CodeIgniter project files to the container
COPY . .

# Install Composer
# RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install project dependencies
# RUN composer install --no-plugins --no-scripts

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
