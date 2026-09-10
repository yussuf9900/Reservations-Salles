#!/bin/bash
set -euo pipefail

cd /var/www/html
if [ "$#" -gt 0 ] && [ "$1" != "apache2-foreground" ]; then
    exec "$@"
fi

composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader
php docker/wait-database.php
php youssou:migrate
php youssou:seed
exec apache2-foreground
