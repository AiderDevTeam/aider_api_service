FROM php:8.2-apache

# Copy custom php.ini file
#ADD custom_php.ini /usr/local/etc/php/php.ini-development
#ADD custom_php.ini /usr/local/etc/php/php.ini-production
ADD custom_php.ini /usr/local/etc/php/conf.d/php.ini

# Install dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install zip pdo_mysql

RUN apt-get install nano

#install supervisor
#RUN apt-get install supervisor

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy the project files to the container
COPY . /var/www/html

#copy supervisor conf to container
# COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Set the working directory
WORKDIR /var/www/html

# Install composer packages
RUN composer install

RUN php artisan cache:clear
RUN php artisan config:clear
RUN php artisan route:clear

# Set up Apache's document root
RUN sed -i -e 's/html/html\/public/g' /etc/apache2/sites-available/000-default.conf

# Enable Apache's rewrite module
RUN a2enmod rewrite

# Expose the container's port
EXPOSE 80

# Run Apache in the foreground
CMD ["apache2-foreground"]

# Run supervisor in the foregroud
#CMD ["/usr/bin/supervisord"]
