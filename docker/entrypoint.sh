#!/bin/bash
set -e

DB_HOST="${DB_HOST:-database}"
DB_PORT="${DB_PORT:-3306}"
DB_DATABASE="${DB_DATABASE:-reservation_salles}"
DB_USERNAME="${DB_USERNAME:-root}"
DB_PASSWORD="${DB_PASSWORD:-}"

echo "[DOCKER] En attente de la disponibilité de la base de données ($DB_HOST:$DB_PORT)..."

MAX_TRIES=60
COUNT=0
until php -r "
try {
    \$host = getenv('DB_HOST') ?: '$DB_HOST';
    \$port = getenv('DB_PORT') ?: '$DB_PORT';
    \$user = getenv('DB_USERNAME') ?: '$DB_USERNAME';
    \$pass = getenv('DB_PASSWORD') ?: '$DB_PASSWORD';
    \$pdo = new PDO(\"mysql:host=\$host;port=\$port\", \$user, \$pass);
    exit(0);
} catch (\Throwable \$e) {
    exit(1);
}
" > /dev/null 2>&1 || [ $COUNT -eq $MAX_TRIES ]; do
    sleep 1
    COUNT=$((COUNT + 1))
done

if [ $COUNT -eq $MAX_TRIES ]; then
    echo "[DOCKER ERREUR] Impossible de joindre MySQL sur $DB_HOST:$DB_PORT après ${MAX_TRIES}s."
    exit 1
fi

echo "[DOCKER] Base de données disponible !"

cd /var/www/html

echo "[DOCKER] Exécution des migrations..."
php youssou:migrate

echo "[DOCKER] Exécution du seeder..."
php youssou:seed

# Si une commande spécifique est passée au conteneur (ex: docker compose run app bash)
if [ "$#" -gt 0 ] && [ "$1" != "apache2-foreground" ]; then
    echo "[DOCKER] Exécution de la commande personnalisée : $@"
    exec "$@"
fi

echo "[DOCKER] Démarrage du serveur web Apache..."
exec apache2-foreground
