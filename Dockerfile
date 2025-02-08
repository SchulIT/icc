FROM php:8.3-fpm-alpine AS base

RUN apk add icu-dev  \
    libzip-dev \
    imagemagick-dev \
    libxslt-dev \
    libgcrypt-dev \
    supervisor \
    nginx \
    cron

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

# Set memory limit
RUN echo "memory_limit=512M" > /usr/local/etc/php/conf.d/memory-limit.ini

# Install composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1

# Set working directory
WORKDIR /var/www/html

# Copy whole project into image
COPY . .

# Run composer install
RUN composer install --no-dev --classmap-authoritative --no-scripts

# Export Translations to JS
RUN php bin/console bazinga:js-translation:dump assets/js/ --merge-domains

# Install Assets
FROM node:22-alpine as assets
WORKDIR /var/www/html
COPY --from=base /var/www/html /var/www/html

# Install dependencies
RUN npm install \
    && npm run build \
    && rm -rf node_modules

FROM base as runner
WORKDIR /var/www/html
COPY --from=assets /var/www/html/public/build /var/www/html/public/build

# Install assets (copy 3rd party stuff)
RUN php bin/console assets:install

# Install nginx configuration
COPY .docker/nginx.conf /etc/nginx/sites-enabled/default

# Remove the .htaccess file because we are using Nginx
RUN rm -rf ./public/.htaccess

# Install supervisor configuration
COPY .docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Export HTTP port
EXPOSE 80

# Install cronjob
RUN touch /var/log/cron.log
RUN (crontab -l ; echo "*/2 * * * * php /var/www/html/bin/console shapecode:cron:run" >> /var/log/cron.log) | crontab

# Copy startup.sh
COPY .docker/startup.sh startup.sh
RUN chmod +x startup.sh

CMD ["./startup.sh"]
