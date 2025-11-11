#!/bin/bash

set -euo pipefail

# -------------------------------------------------------------
# FlorenceEGI - DB Import utility
# -------------------------------------------------------------
# Importa un dump SQL (anche gz) nel database configurato in .env.
# ATTENZIONE: sovrascrive i dati esistenti!
# -------------------------------------------------------------

if [[ $# -lt 1 ]]; then
  echo "Uso: $0 /percorso/al/dump.sql[.gz]"
  exit 1
fi

DUMP_PATH="$1"

if [[ ! -f "${DUMP_PATH}" ]]; then
  echo "❌ File '${DUMP_PATH}' non trovato."
  exit 1
fi

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "${SCRIPT_DIR}/../.." && pwd)"
ENV_FILE="${PROJECT_ROOT}/.env"

if [[ ! -f "${ENV_FILE}" ]]; then
  echo "❌ File .env non trovato in ${PROJECT_ROOT}. Impossibile leggere le credenziali DB."
  exit 1
fi

read_env() {
  local key="$1"
  local line
  line=$(grep -E "^[[:space:]]*${key}=" "${ENV_FILE}" | tail -n 1 | cut -d= -f2-)
  line="${line#"${line%%[![:space:]]*}"}"
  line="${line%"${line##*[![:space:]]}"}"
  line="${line%\"}"
  line="${line#\"}"
  line="${line%\'}"
  line="${line#\'}"
  echo "${line}"
}

DB_CONNECTION="$(read_env "DB_CONNECTION")"
DB_HOST="$(read_env "DB_HOST")"
DB_PORT="$(read_env "DB_PORT")"
DB_DATABASE="$(read_env "DB_DATABASE")"
DB_USERNAME="$(read_env "DB_USERNAME")"
DB_PASSWORD="$(read_env "DB_PASSWORD")"
DB_SOCKET="$(read_env "DB_SOCKET")"

if [[ "${DB_CONNECTION}" != "mysql" && "${DB_CONNECTION}" != "mariadb" ]]; then
  echo "❌ Connessione DB non supportata (${DB_CONNECTION:-non impostata}). Supportato solo mysql/mariadb."
  exit 1
fi

if [[ -n "${DB_SOCKET}" ]]; then
  CONNECTION_DESC="socket ${DB_SOCKET}"
else
  CONNECTION_DESC="${DB_HOST}:${DB_PORT}"
fi

echo "⚠️  IMPORTAZIONE DISTRUTTIVA"
echo "Database: ${DB_DATABASE} su ${CONNECTION_DESC}"
read -rp "Procedere? (scrivi YES per confermare): " CONFIRM

if [[ "${CONFIRM}" != "YES" ]]; then
  echo "Operazione annullata."
  exit 0
fi

echo "⬇️  Import da ${DUMP_PATH}..."

if [[ "${DUMP_PATH}" == *.gz ]]; then
  MYSQL_CMD=(mysql -u "${DB_USERNAME}")
  if [[ -n "${DB_SOCKET}" ]]; then
    MYSQL_CMD+=(--socket="${DB_SOCKET}")
  else
    MYSQL_CMD+=(-h "${DB_HOST}" -P "${DB_PORT}")
  fi
  if [[ -n "${DB_PASSWORD}" ]]; then
    MYSQL_CMD+=(--password="${DB_PASSWORD}")
  fi
  gunzip -c "${DUMP_PATH}" | "${MYSQL_CMD[@]}" "${DB_DATABASE}"
else
  MYSQL_CMD=(mysql -u "${DB_USERNAME}")
  if [[ -n "${DB_SOCKET}" ]]; then
    MYSQL_CMD+=(--socket="${DB_SOCKET}")
  else
    MYSQL_CMD+=(-h "${DB_HOST}" -P "${DB_PORT}")
  fi
  if [[ -n "${DB_PASSWORD}" ]]; then
    MYSQL_CMD+=(--password="${DB_PASSWORD}")
  fi
  "${MYSQL_CMD[@]}" "${DB_DATABASE}" < "${DUMP_PATH}"
fi

echo "✅ Import completato."

