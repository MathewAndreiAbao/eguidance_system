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
RUN curl -fsSL https://deb.nodesource.com/setup_16.x | bash - \
    && apt-get install -y nodejs

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . /var/www/html/

# Copy root files
COPY ../.env.example /var/www/html/
COPY ../mysql_schema.sql /var/www/html/
COPY ../package.json /var/www/html/
COPY ../tailwind.config.js /var/www/html/

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Install Node.js dependencies
RUN npm install

# Build Tailwind CSS
RUN npm run build-prod

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