#!/bin/bash
set -e

DOCKER_USER="${1:-${DOCKER_USERNAME:-devyussuf}}"
REPO_NAME="reservations-salles"
TARGET_REPO="$DOCKER_USER/$REPO_NAME"
SOURCE_IMAGE="reservations-salles:latest"

echo "=================================================="
echo " Synchronisation des tags Git vers Docker Hub"
echo " Dépôt cible : $TARGET_REPO"
echo "=================================================="

# Vérifier si l'image source existe, sinon la construire
if ! docker image inspect "$SOURCE_IMAGE" >/dev/null 2>&1; then
    echo "🔨 Construction de l'image source $SOURCE_IMAGE..."
    docker build -t "$SOURCE_IMAGE" .
fi

# Récupérer la liste des tags Git
TAGS=$(git tag -l)

if [ -z "$TAGS" ]; then
    echo "⚠️  Aucun tag Git trouvé dans ce dépôt."
    exit 1
fi

echo "📋 Tags Git trouvés :"
echo "$TAGS"
echo "--------------------------------------------------"

for TAG in $TAGS; do
    echo "🏷️  Tagging : $TARGET_REPO:$TAG"
    docker tag "$SOURCE_IMAGE" "$TARGET_REPO:$TAG"
    
    echo "🚀 Pushing : $TARGET_REPO:$TAG"
    docker push "$TARGET_REPO:$TAG"

done

# Pousser également le tag latest
echo "🏷️  Tagging : $TARGET_REPO:latest"
docker tag "$SOURCE_IMAGE" "$TARGET_REPO:latest"
echo "🚀 Pushing : $TARGET_REPO:latest"
docker push "$TARGET_REPO:latest"

echo "=================================================="
echo "✅ Tous les tags Git ont été synchronisés avec succès sur Docker Hub !"
echo "👉 Consultez vos tags : https://hub.docker.com/repository/docker/$TARGET_REPO/tags"
echo "=================================================="
