FROM php:8.2-apache

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Install PHP extensions
RUN docker-php-ext-install fileinfo

# Install required tools for Composer
RUN apt-get update && apt-get install -y \
    zip \
    unzip \
    git \
    && rm -rf /var/lib/apt/lists/*

# Install Composer earlier to ensure it is available
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Create uploads directory with proper permissions
RUN mkdir -p /var/www/html/src/uploads && \
    chown -R www-data:www-data /var/www/html && \
    chmod -R 775 /var/www/html

# Copy application files
COPY src/ /var/www/html/

# Install Composer dependencies
RUN composer install --no-dev --optimize-autoloader

# Configure Apache
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Set the Apache user and group
RUN sed -i 's/www-data:x:33:33:/www-data:x:1000:1000:/' /etc/passwd

# Apply PHP upload limits using environment variables
RUN echo "upload_max_filesize=${UPLOAD_MAX_FILESIZE}" > /usr/local/etc/php/conf.d/uploads.ini && \
    echo "post_max_size=${POST_MAX_SIZE}" >> /usr/local/etc/php/conf.d/uploads.ini

# Install pip
RUN apt-get update && apt-get install -y python3-pip && \
    rm -rf /var/lib/apt/lists/*

# Install camelot-py[base] with --break-system-packages
RUN pip3 install "camelot-py[base]" --break-system-packages