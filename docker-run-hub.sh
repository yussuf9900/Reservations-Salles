#!/bin/bash
set -euo pipefail
cd "$(dirname "$0")"
export DOCKER_IMAGE="${1:-devyussuf/reservations-salles:latest}"
docker compose -f docker-compose.hub.yml up -d --wait --wait-timeout 240
docker compose -f docker-compose.hub.yml ps
