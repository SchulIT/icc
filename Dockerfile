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
    # && pecl install imagick \
    && pecl install apcu \
    && pecl clear-cache \
    && apk del .build-deps \
    && docker-php-source delete \
    && docker-php-ext-enable pdo_mysql pcntl intl zip apcu

RUN curl -L -o /tmp/imagick.tar.gz https://github.com/Imagick/imagick/archive/7088edc353f53c4bc644573a79cdcd67a726ae16.tar.gz \
    && tar --strip-components=1 -xf /tmp/imagick.tar.gz \
    && phpize \
    && ./configure \
    && make \
    && make install \
    && echo "extension=imagick.so" > /usr/local/etc/php/conf.d/ext-imagick.ini \
    && rm -rf /tmp/*

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

# # Install MariaDB and necessary dependencies
# RUN apk add --no-cache mariadb mariadb-client su-exec

# # ENV DATABASE_URL=mysql://myuser:mysecurepassword@127.0.0.1:3306/icc?serverVersion=11.4.4-MariaDB

# # Export Translations to JS with fake DB
# RUN mkdir -p /run/mysqld /var/lib/mysql \
#     && chown -R mysql:mysql /run/mysqld /var/lib/mysql \
#     && mariadb-install-db --user=mysql --datadir=/var/lib/mysql

# RUN su-exec mysql mariadbd  \
#     --user=mysql \
#     --datadir=/var/lib/mysql \
#     --socket=/var/run/mysqld/mysqld.sock \ 
#     --pid-file=/var/run/mysqld/mysqld.pid \ 
#     --bind-address=0.0.0.0 \
#     --skip-networking=0 \
#     & \
#     sleep 5 \
#     && su-exec mysql mysql -e "CREATE USER 'myuser'@'%' IDENTIFIED BY 'mysecurepassword';" \
#     && su-exec mysql mysql -e "GRANT ALL PRIVILEGES ON *.* TO 'myuser'@'%' WITH GRANT OPTION;" \
#     && su-exec mysql mysql -e "FLUSH PRIVILEGES;" \
#     && mysql -u myuser --socket=/var/run/mysqld/mysqld.sock -e "SHOW DATABASES;" \
#    # && php bin/console doctrine:database:create --if-not-exists \

RUN php bin/console bazinga:js-translation:dump assets/js/ --merge-domains
    # && mysqladmin -u root shutdown

# --- Third stage: Install Assets --- #
FROM node:22-alpine AS assets

# Set workdir and copy files
WORKDIR /var/www/html
COPY . .
COPY --from=composer /var/www/html/assets/js /var/www/html/assets/js
COPY --from=composer /var/www/html/vendor /var/www/html/vendor

# Install dependencies
RUN npm install 

RUN npm run build

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
