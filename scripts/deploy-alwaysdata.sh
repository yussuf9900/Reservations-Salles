#!/usr/bin/env bash
set -euo pipefail

echo "========================================================="
echo " Déploiement / Mise à jour Alwaysdata : Réservation Salles"
echo "========================================================="

# Répertoire racine du projet
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
ROOT_DIR="$(dirname "$SCRIPT_DIR")"
cd "$ROOT_DIR"

echo "-> Répertoire de travail : $ROOT_DIR"

# 1. Vérification du fichier .env
if [ ! -f .env ]; then
    if [ -f .env.alwaysdata.example ]; then
        echo "-> Aucun fichier .env trouvé. Création à partir de .env.alwaysdata.example..."
        cp .env.alwaysdata.example .env
        echo "=========================================================================="
        echo " ATTENTION : Veuillez éditer le fichier .env avec vos identifiants MySQL  "
        echo " puis relancez ce script : nano .env                                      "
        echo "=========================================================================="
        exit 1
    else
        echo "ERREUR : Aucun fichier .env ni modèle disponible."
        exit 1
    fi
fi

# 2. Récupération des dernières modifications si dépôt Git
if [ -d .git ]; then
    echo "-> Mise à jour du dépôt Git (git pull)..."
    git pull origin main || echo "-> Note : git pull ignoré ou branches divergentes, continuation..."
fi

# 3. Installation optimisée des dépendances Composer
echo "-> Installation des dépendances Composer (--no-dev)..."
composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader

# 4. Exécution des migrations
echo "-> Application des migrations de base de données..."
php youssou:migrate

# 5. Exécution des données de test / initiales
echo "-> Synchronisation des données initiales (seeds)..."
php youssou:seed

# 6. Droits d'exécution sur les scripts CLI
chmod +x youssou youssou:migrate youssou:seed 2>/dev/null || true

echo "========================================================="
echo " [SUCCÈS] Déploiement Alwaysdata terminé avec succès !"
echo "========================================================="
