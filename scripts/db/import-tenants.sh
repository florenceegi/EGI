#!/usr/bin/env bash
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "${SCRIPT_DIR}/../.." && pwd)"

cd "$PROJECT_ROOT"

PHP_SCRIPT="$SCRIPT_DIR/import-tenants.php"
DATA_FILE="$PROJECT_ROOT/database/data/tenants.json"
IF_SQL="$PROJECT_ROOT/database/sql/tenants_import.sql"

if [[ ! -f "$DATA_FILE" ]]; then
    echo "[ERRORE] File JSON non trovato: $DATA_FILE" >&2
    exit 1
fi

if [[ ! -f "$IF_SQL" ]]; then
    cat <<INFO
[INFO] File SQL generato automaticamente: $IF_SQL
Per utilizzo manuale con mysql:
  mysql -h\$DB_HOST -P\$DB_PORT -u\$DB_USERNAME -p\$DB_PASSWORD \$DB_DATABASE < "$IF_SQL"
INFO
fi

php "$PHP_SCRIPT" "$DATA_FILE"
