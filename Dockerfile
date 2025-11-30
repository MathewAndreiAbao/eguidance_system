# Dockerfile for E-Guidance Platform on Render

FROM php:8.1-apache

# Install PHP extensions
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo pdo_mysql mysqli zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Node.js and npm
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# Copy application files
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Skip Node.js dependencies and Tailwind build since output.css is already compiled
# This avoids potential build errors during deployment
# Existing output.css file will be used

# Expose port
EXPOSE 80

# Apache configuration
RUN a2enmod rewrite
RUN sed -i 's/\/var\/www\/html/\/var\/www\/html/g' /etc/apache2/sites-available/000-default.conf
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Set permissions
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html

# Start Apache
CMD ["apache2-foreground"]