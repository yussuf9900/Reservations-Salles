#!/bin/bash
set -e

IMAGE_NAME="${1:-reservations-salles}"

docker build -t "$IMAGE_NAME:latest" .

TAGS=$(git tag -l)
for TAG in $TAGS; do
    docker tag "$IMAGE_NAME:latest" "$IMAGE_NAME:$TAG"
    echo "Image taggee : $IMAGE_NAME:$TAG"
done

echo "Toutes les images Docker ont ete taguees avec succes."
