#!/bin/bash
set -e

echo "🚀 Démarrage de l'environnement UnivSalles (App PHP 8.3 + MySQL 8.0)..."
docker compose up -d --build

echo "⏳ Attente de la disponibilité de l'application..."
until curl -s http://localhost:8080/ >/dev/null; do
    sleep 1
done

echo "✅ Application UnivSalles prête et accessible sur : http://localhost:8080"
echo "🗄️  Base de données MySQL accessible sur : 127.0.0.1:3306"

