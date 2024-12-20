FROM php:8.2-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libicu-dev \
    libpq-dev \
    && docker-php-ext-install intl pdo pdo_pgsql \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set the working directory
WORKDIR /app

# Copy project files
COPY . /app

# Install PHP dependencies
RUN composer install --no-interaction --no-scripts --no-progress --prefer-dist

# Expose port 8000 for the built-in PHP server
EXPOSE 8000

# Start the built-in PHP server by default
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
