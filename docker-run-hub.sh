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

echo "Attente de la disponibilité de l'application..."
until curl -s http://localhost:8080/ >/dev/null; do
    sleep 1
done

echo "=================================================="
echo "Application UnivSalles prête et accessible sur : http://localhost:8080"
echo "Base de données MySQL accessible sur : 127.0.0.1:3306"
echo "Pour arrêter : docker compose -f docker-compose.hub.yml down"
echo "=================================================="
