#!/bin/sh

CONTAINER_ALREADY_STARTED="ICC_CONTAINER_ALREADY_STARTED"
# Check if the container has already been started
if [ ! -e $CONTAINER_ALREADY_STARTED ]; then
    touch $CONTAINER_ALREADY_STARTED
    echo "-- First container startup --"

    # Build and install the js files and translations as well as assets
    php bin/console bazinga:js-translation:dump assets/js/ --merge-domains
    npm run build
    php bin/console assets:install

    # Check if the SAML certificate does not exist
    if [ ! -f /var/www/html/saml/sp.crt ] || [ ! -f /var/www/html/saml/sp.key ]; then
        echo "Creating SAML certificate..."

        # Create SAML certificate
        php bin/console app:create-certificate --type saml --no-interaction
    fi

    # Cache leeren und aufwärmen
    php bin/console cache:clear

    # Run database migrations
    php bin/console doctrine:migrations:migrate --no-interaction

    # Perform initial setup
    php bin/console app:setup

    # Register cron jobs
    php bin/console shapecode:cron:scan

    # Grant write permissions to the storage and bootstrap/cache directories
    chown -R www-data:www-data /var/www/html/var
    chown -R www-data:www-data /var/www/html/files
fi

# Start PHP-FPM
php-fpm &

# Start Nginx
nginx -g 'daemon off;'
