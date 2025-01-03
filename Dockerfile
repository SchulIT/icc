# Use the official PHP image with FPM as the base image
FROM php:8.2-fpm AS base

# Install dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    unzip \
    libxml2-dev \
    libssl-dev \
    libzip-dev \
    libpng-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libonig-dev \
    libxslt1-dev \
    libmcrypt-dev \
    libsodium-dev \
    nginx \
    openssl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    ctype \
    dom \
    filter \
    iconv \
    intl \
    mbstring \
    pdo_mysql \
    phar \
    simplexml \
    sodium \
    xml \
    xmlwriter \
    zip \
    gd \
    xsl \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Set memory limit for PHP
RUN echo "memory_limit=512M" > /usr/local/etc/php/conf.d/memory-limit.ini
ENV PHP_MEMORY_LIMIT=512M

FROM base AS composer

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set COMPOSER_ALLOW_SUPERUSER environment variable
ENV COMPOSER_ALLOW_SUPERUSER=1

# Set working directory
WORKDIR /var/www/html

# Copy the composer.json and composer.lock files into the container
COPY . .

# Install PHP dependencies including symfony/runtime
RUN composer install --no-dev --classmap-authoritative --no-scripts

FROM base AS runner

# Set working directory
WORKDIR /var/www/html

COPY --from=composer /var/www/html/vendor /var/www/html/vendor

# Copy the package.json and package-lock.json files into the container
COPY . .

# Remove unnecessary files
RUN rm -rf ./docs
RUN rm -rf ./.github
RUN rm -rf ./docker-compose.yml
RUN rm -rf ./Dockerfile
RUN rm -rf ./.gitignore

# Install Node.js and npm
RUN apt-get update && apt-get install -y nodejs npm

RUN npm install

# Copy build files from the previous stages
COPY --from=composer /var/www/html/vendor /var/www/html/vendor

# Remove the .htaccess file because we are using Nginx
RUN rm -rf ./public/.htaccess

# Copy the Nginx configuration file into the container
COPY nginx.conf /etc/nginx/sites-enabled/default

# Copy the startup script into the container
COPY startup.sh /usr/local/bin/startup.sh

# Ensure the startup script is executable
RUN chmod +x /usr/local/bin/startup.sh

# Expose port 80
EXPOSE 80

# Use the startup script as the entrypoint
CMD ["/usr/local/bin/startup.sh"]
