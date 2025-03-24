### --- First Stage: Base Image --- ###

# Use the official PHP image with FPM as the base image
FROM php:8.3-fpm-alpine AS base

LABEL maintainer="SchulIT" \ 
      description="ICC - Webbasiertes Schulinformationssystem mit Vertretungs-, Klausur-, Stundenplan, Mitteilungen, Abwesenheitsmeldungen, digitale Klassenbücher uvm."

# Install dependencies and PHP extensions
RUN apk add --no-cache icu-dev  \
    libzip-dev \
    imagemagick-dev \
    libxslt-dev \
    libgcrypt-dev \
    supervisor \
    nginx \
    curl \
    tzdata

RUN apk add --no-cache --virtual .build-deps \
    autoconf \
    g++ \
    make \
    && docker-php-ext-install -j$(nproc) pdo_mysql pcntl intl zip xsl sysvsem \
    # Installing Imagic from PECL fails, so we need to install it manually
    # && pecl install imagick \
    && curl -L -o /tmp/imagick.tar.gz https://github.com/Imagick/imagick/archive/7088edc353f53c4bc644573a79cdcd67a726ae16.tar.gz \
    && tar --strip-components=1 -xf /tmp/imagick.tar.gz \
    && phpize \
    && ./configure \
    && make \
    && make install \
    && echo "extension=imagick.so" > /usr/local/etc/php/conf.d/ext-imagick.ini \
    && rm -rf /tmp/* \
    # End of manual installation of Imagick
    && pecl install apcu \
    && pecl clear-cache \
    && apk del .build-deps \
    && docker-php-source delete \
    && docker-php-ext-enable pdo_mysql pcntl intl zip imagick apcu sysvsem

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

# Set Timezone (default: Europe/Berlin)
ENV TZ=Europe/Berlin
RUN echo 'date.timezone="${TZ}"' > /usr/local/etc/php/conf.d/timezone.ini

# Configure PHP-FPM
COPY .docker/fpm-pool.conf /usr/local/etc/php-fpm.d/www.conf

# Set DB version so that symfony does not try to connect to a real DB
ENV DATABASE_SERVER_VERSION=10.11.0-MariaDB

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

RUN php bin/console bazinga:js-translation:dump assets/js/ --merge-domains


### --- Third stage: Install Assets --- ###
FROM node:22-alpine AS assets

# Set workdir and copy files
WORKDIR /var/www/html
COPY . .
COPY --from=composer /var/www/html/assets/js /var/www/html/assets/js
COPY --from=composer /var/www/html/vendor /var/www/html/vendor

# Install dependencies
RUN npm install 

RUN npm run build


### --- Fourth Stage: Runner --- ###
FROM base AS runner

# Set workdir and copy files
WORKDIR /var/www/html
COPY --from=composer /var/www/html/vendor /var/www/html/vendor
COPY --from=assets /var/www/html/public/build /var/www/html/public/build

# Install assets (copy 3rd party stuff)
RUN php bin/console assets:install

# Install nginx configuration
# Configure nginx - http
COPY .docker/nginx/nginx.conf /etc/nginx/nginx.conf
# Configure nginx - default server
COPY .docker/nginx/default.conf /etc/nginx/conf.d/icc.conf
# RUN mkdir -p /etc/nginx/sites-enabled/ && ln -s /etc/nginx/sites-available/default /etc/nginx/sites-enabled/

# Install supervisor configuration
COPY .docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Export HTTP port
EXPOSE 8080

# Install cronjob
RUN crontab -l | { cat; echo "*/2 * * * * php /var/www/html/bin/console shapecode:cron:run"; } | crontab -

# Copy startup.sh
COPY .docker/startup.sh startup.sh
RUN chmod +x startup.sh

RUN chown -R nobody:nobody /var/www/html /run /var/lib/nginx /var/log/nginx
USER nobody

CMD ["./startup.sh"]

# Configure a healthcheck to validate that everything is up&running
HEALTHCHECK --timeout=10s CMD curl --silent --fail http://127.0.0.1:8080/fpm-ping || exit 1