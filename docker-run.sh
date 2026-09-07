#!/bin/bash
set -e

IMAGE_NAME="reservations-salles:latest"
CONTAINER_NAME="reservations_all_in_one"
PORT="${1:-8080}"

docker build -t "$IMAGE_NAME" .

if [ "$(docker ps -aq -f name=$CONTAINER_NAME)" ]; then
    docker rm -f "$CONTAINER_NAME"
fi

docker run -d -p "$PORT:80" --name "$CONTAINER_NAME" "$IMAGE_NAME"

echo "Application accessible sur http://localhost:$PORT"
