# Clear cache
php bin/console cache:clear

# Migrate database
php bin/console doctrine:migrations:migrate --no-interaction -v

# Check if the SAML certificate does not exist
if [ ! -f /var/www/html/saml/sp.crt ] || [ ! -f /var/www/html/saml/sp.key ]; then
    echo "Creating SAML certificate..."

    # Create SAML certificate
    php bin/console app:create-certificate --type saml --no-interaction
fi

# Scan for new cronjobs
php bin/console shapecode:cron:scan

# Run app setup
php bin/console app:setup

# Start PHP FPM
php-fpm &

# Start nginx
nginx -g 'daemon off;'

# Start cron
cron