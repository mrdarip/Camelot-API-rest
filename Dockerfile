FROM php:8.2-apache

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Install PHP extensions
RUN docker-php-ext-install fileinfo

# Set working directory
WORKDIR /var/www/html

# Create uploads directory with proper permissions
RUN mkdir -p /var/www/html/src/uploads && \
    chown -R www-data:www-data /var/www/html && \
    chmod -R 775 /var/www/html

# Copy application files
COPY src/ /var/www/html/

# Configure Apache
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Set the Apache user and group
RUN sed -i 's/www-data:x:33:33:/www-data:x:1000:1000:/' /etc/passwd

# Apply PHP upload limits using environment variables
RUN echo "upload_max_filesize=${UPLOAD_MAX_FILESIZE}" > /usr/local/etc/php/conf.d/uploads.ini && \
    echo "post_max_size=${POST_MAX_SIZE}" >> /usr/local/etc/php/conf.d/uploads.ini