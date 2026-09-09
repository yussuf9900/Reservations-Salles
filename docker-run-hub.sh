#!/bin/bash
set -e

DOCKER_IMAGE="${1:-devyussuf/reservations-salles:latest}"
export DOCKER_IMAGE

echo "=================================================="
echo " Démarrage d'UnivSalles depuis Docker Hub"
echo " Image cible : $DOCKER_IMAGE"
echo "=================================================="

echo "Téléchargement de l'image si nécessaire..."
docker pull "$DOCKER_IMAGE"

echo "Démarrage de la stack (App + MySQL 8.0)..."
docker compose -f docker-compose.hub.yml up -d

if [ -f .env ]; then
    set -a
    source .env
    set +a
fi

APP_PORT="${APP_PORT:-8080}"
FORWARD_DB_PORT="${FORWARD_DB_PORT:-3306}"
PMA_PORT="${PMA_PORT:-8081}"

echo "Attente de la disponibilité de l'application sur le port ${APP_PORT}..."
until curl -s "http://localhost:${APP_PORT}/" >/dev/null; do
    sleep 1
done

echo "=================================================="
echo "Application UnivSalles prête et accessible sur : http://localhost:${APP_PORT}"
echo "Base de données MySQL accessible sur : 127.0.0.1:${FORWARD_DB_PORT}"
echo "Interface phpMyAdmin accessible sur : http://localhost:${PMA_PORT}"
echo "Pour arrêter : docker compose -f docker-compose.hub.yml down"
echo "=================================================="
