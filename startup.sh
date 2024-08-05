#!/bin/sh

# Check if the FIRST_RUN environment variable is 1
if [ "$FIRST_RUN" = "1" ]; then
    # Check if the SAML certificate does not exist
    if [ ! -f /var/www/html/certs/saml.crt ] || [ ! -f /var/www/html/certs/saml.key ]; then
        # Create SAML certificate
        php bin/console app:create-certificate --type saml --no-interaction
    fi

    # Cache leeren und aufwärmen
    $ php bin/console cache:clear

    # Run database migrations
    php bin/console doctrine:migrations:migrate --no-interaction

    # Perform initial setup
    php bin/console app:setup

    # Register cron jobs
    php bin/console shapecode:cron:scan

    # Set FIRST_RUN environment variable to 0
    export FIRST_RUN=0
fi

# Start PHP-FPM
php-fpm &

# Start Nginx
nginx -g 'daemon off;'
