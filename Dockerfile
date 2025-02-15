### --- First Stage: Base Image --- ###

# Use the official PHP image with FPM as the base image
FROM php:8.3-fpm-alpine AS base

# Install dependencies and PHP extensions
RUN apk add icu-dev  \
    libzip-dev \
    imagemagick-dev \
    libxslt-dev \
    libgcrypt-dev \
    supervisor \
    nginx

RUN apk add --no-cache --virtual .build-deps \
    autoconf \
    g++ \
    make \
    && docker-php-ext-install -j$(nproc) pdo_mysql pcntl intl zip xsl \
    && pecl install imagick \
    && pecl install apcu \
    && pecl clear-cache \
    && apk del .build-deps \
    && docker-php-source delete \
    && docker-php-ext-enable pdo_mysql pcntl intl zip imagick apcu

# Copy php.ini
RUN cp /usr/local/etc/php/php.ini-production /usr/local/etc/php.ini

# Set memory limit
RUN echo "memory_limit=512M" > /usr/local/etc/php/conf.d/memory-limit.ini

# Set settings for uploading files
RUN echo "upload_max_filesize = 128M" >> /usr/local/etc/php/conf.d/uploads.ini
RUN echo "post_max_size = 128M" >> /usr/local/etc/php/conf.d/uploads.ini

# Set maximum execution time
RUN echo "max_execution_time = 90" > /usr/local/etc/php/conf.d/execution-time.ini

# Do not expose PHP
RUN echo "expose_php = Off" > /usr/local/etc/php/conf.d/expose-php.ini

# Set working directory
WORKDIR /var/www/html

# Copy whole project into image
COPY . .

### --- SECOND STAGE: Composer --- ###
FROM base AS composer

# Install composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1

# Run composer install
RUN composer install --no-dev --classmap-authoritative --no-scripts

# Export Translations to JS
RUN php bin/console bazinga:js-translation:dump assets/js/ --merge-domains

# --- Third stage: Install Assets --- #
FROM node:22-alpine AS assets

# Set workdir and copy files
WORKDIR /var/www/html
COPY --from=composer /var/www/html/vendor /var/www/html/vendor

# Install dependencies
RUN npm install \
    && npm run build \
    && rm -rf node_modules

# --- Fourth Stage: Runner --- #
FROM base AS runner

# Set workdir and copy files
WORKDIR /var/www/html
COPY --from=composer /var/www/html/vendor /var/www/html/vendor
COPY --from=assets /var/www/html/public/build /var/www/html/public/build

# Install assets (copy 3rd party stuff)
RUN php bin/console assets:install

# Install nginx configuration
COPY .docker/nginx.conf /etc/nginx/sites-enabled/default

# Install supervisor configuration
COPY .docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Export HTTP port
EXPOSE 80

# Install cronjob
RUN crontab -l | { cat; echo "*/2 * * * * php /var/www/html/bin/console shapecode:cron:run"; } | crontab -

# Copy startup.sh
COPY .docker/startup.sh startup.sh
RUN chmod +x startup.sh

CMD ["./startup.sh"]
