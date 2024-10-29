FROM php:8.1-apache

# Install dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install zip pdo_mysql

# Install the gRPC extension
# RUN pecl install grpc \
#     && docker-php-ext-enable grpc

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy the project files to the container
COPY . /var/www/html

# Set the working directory
WORKDIR /var/www/html

# Install composer packages
# RUN composer install

RUN composer update
RUN php artisan cache:clear
RUN php artisan config:clear
RUN php artisan route:clear
RUN php artisan config:cache

# Set up Apache's document root
RUN sed -i -e 's/html/html\/public/g' /etc/apache2/sites-available/000-default.conf

# Enable Apache's rewrite module
RUN a2enmod rewrite

# Expose the container's port
EXPOSE 80

# Run Apache in the foreground
CMD ["apache2-foreground"]
