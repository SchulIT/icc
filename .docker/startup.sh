#!/bin/sh

CONTAINER_ALREADY_STARTED="ICC_CONTAINER_ALREADY_STARTED"
# Check if the container has already been started
if [ ! -e $CONTAINER_ALREADY_STARTED ]; then
    touch $CONTAINER_ALREADY_STARTED
    echo "-- First container startup --"

    # Clear cache
    php bin/console cache:clear

    # Migrate database
    php bin/console doctrine:migrations:migrate --no-interaction -v

    # Scan for new cronjobs
    php bin/console shapecode:cron:scan

    # Run app setup
    php bin/console app:setup
fi

# Check if the SAML certificate does not exist
if [ ! -f /var/www/html/saml/sp.crt ] || [ ! -f /var/www/html/saml/sp.key ]; then
    echo "Creating SAML certificate..."

    # Create SAML certificate
    php bin/console app:create-certificate --type saml --no-interaction
fi


# Start PHP FPM
php-fpm &

# Start cron
crond &

# Start nginx
nginx -g 'daemon off;'