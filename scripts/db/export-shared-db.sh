#!/bin/bash

set -euo pipefail

# -------------------------------------------------------------
# FlorenceEGI - DB Export utility
# -------------------------------------------------------------
# Crea un dump del database configurato in .env nella cartella
# storage/db-sync utilizzando mysqldump.
# -------------------------------------------------------------

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "${SCRIPT_DIR}/../.." && pwd)"
ENV_FILE="${PROJECT_ROOT}/.env"
OUTPUT_DIR="${PROJECT_ROOT}/storage/db-sync"
TIMESTAMP="$(date +%Y%m%d_%H%M%S)"

if [[ ! -f "${ENV_FILE}" ]]; then
  echo "❌ File .env non trovato in ${PROJECT_ROOT}. Impossibile leggere le credenziali DB."
  exit 1
fi

read_env() {
  local key="$1"
  # Estrae il valore ignorando eventuali righe commentate
  sed -n "s/^${key}=//p" "${ENV_FILE}" | tail -n 1
}

DB_CONNECTION="$(read_env "DB_CONNECTION")"
DB_HOST="$(read_env "DB_HOST")"
DB_PORT="$(read_env "DB_PORT")"
DB_DATABASE="$(read_env "DB_DATABASE")"
DB_USERNAME="$(read_env "DB_USERNAME")"
DB_PASSWORD="$(read_env "DB_PASSWORD")"

if [[ "${DB_CONNECTION}" != "mysql" && "${DB_CONNECTION}" != "mariadb" ]]; then
  echo "❌ Connessione DB non supportata (${DB_CONNECTION:-non impostata}). Supportato solo mysql/mariadb."
  exit 1
fi

mkdir -p "${OUTPUT_DIR}"
DUMP_FILE="${OUTPUT_DIR}/${DB_DATABASE}_${TIMESTAMP}.sql.gz"

echo "📦 Avvio export database '${DB_DATABASE}' su ${DB_HOST}:${DB_PORT}..."

mysqldump \
  --single-transaction \
  --quick \
  --lock-tables=false \
  -h "${DB_HOST}" \
  -P "${DB_PORT}" \
  -u "${DB_USERNAME}" \
  --password="${DB_PASSWORD}" \
  "${DB_DATABASE}" | gzip > "${DUMP_FILE}"

echo "✅ Dump completato: ${DUMP_FILE}"
echo "Trasferisci questo file alle altre postazioni e importalo con import-shared-db.sh"

