#!/bin/bash
# Clear all Laravel caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Remove cached service provider files
rm -f bootstrap/cache/services.php
rm -f bootstrap/cache/packages.php

# Regenerate autoloader
composer dump-autoload --optimize

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Deployment fix completed!"
