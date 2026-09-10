#!/bin/bash
set -euo pipefail
cd "$(dirname "$0")"
docker compose up -d --build --wait --wait-timeout 240
docker compose ps
