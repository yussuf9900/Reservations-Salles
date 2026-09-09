#!/bin/bash
set -e

echo "Démarrage de l'environnement UnivSalles (App PHP 8.3 + MySQL 8.0)..."
docker compose up -d --build

if [ -f .env ]; then
    set -a
    source .env
    set +a
fi

APP_PORT="${APP_PORT:-8080}"
FORWARD_DB_PORT="${FORWARD_DB_PORT:-3306}"

echo "Attente de la disponibilité de l'application sur le port ${APP_PORT}..."
until curl -s "http://localhost:${APP_PORT}/" >/dev/null; do
    sleep 1
done

echo "Application UnivSalles prête et accessible sur : http://localhost:${APP_PORT}"
echo "Base de données MySQL accessible sur : 127.0.0.1:${FORWARD_DB_PORT}"

