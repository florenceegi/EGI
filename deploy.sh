#!/usr/bin/env bash
set -euo pipefail

SITE_DIR="/home/forge/default"
cd "$SITE_DIR"

echo "==> Check .env"
if [ ! -f ".env" ]; then
  echo "ERRORE: manca .env (Forge → Environment)."
  exit 1
fi

echo "==> Git fetch/reset ($FORGE_SITE_BRANCH)"
git fetch origin "$FORGE_SITE_BRANCH" --prune
git reset --hard "origin/$FORGE_SITE_BRANCH"

COMPOSER_CMD=${FORGE_COMPOSER:-composer}
PHP_CMD=${FORGE_PHP:-php}

echo "==> Composer install (no scripts)"
$COMPOSER_CMD install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts

echo "==> Cartelle/cache & permessi"
mkdir -p bootstrap/cache storage/logs
chown -R forge:forge storage bootstrap/cache
chmod -R ug+rw storage bootstrap/cache

echo "==> APP_KEY se manca"
if ! grep -q "^APP_KEY=base64:" .env; then
  $PHP_CMD artisan key:generate --force
fi

echo "==> Storage link (se già esiste, ok)"
$PHP_CMD artisan storage:link || true

ENV_SESSION_DRIVER=$(grep -E "^SESSION_DRIVER=" .env | sed -E 's/^[^=]+=//; s/"//g' || true)
ENV_QUEUE_CONNECTION=$(grep -E "^QUEUE_CONNECTION=" .env | sed -E 's/^[^=]+=//; s/"//g' || true)

have_table () {
  $PHP_CMD -r '
  use Illuminate\Support\Facades\Schema;
  require "vendor/autoload.php";
  $app = require "bootstrap/app.php";
  $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
  echo Schema::hasTable($argv[1]) ? "1" : "0";
  ' "$1"
}

echo "==> Preparo migrations on-demand"
if [ "${ENV_SESSION_DRIVER:-file}" = "database" ]; then
  if [ "$(have_table sessions || echo 0)" != "1" ]; then
    echo "   - Genero migration sessions"
    $PHP_CMD artisan session:table || true
  fi
fi
if [ "${ENV_QUEUE_CONNECTION:-sync}" = "database" ]; then
  if [ "$(have_table jobs || echo 0)" != "1" ]; then
    echo "   - Genero migration jobs"
    $PHP_CMD artisan queue:table || true
  fi
fi

echo "==> php artisan migrate --force"
$PHP_CMD artisan migrate --force || {
  echo "ATTENZIONE: migrate ha dato errore. Controlla storage/logs/laravel.log"
}

echo "==> Pulisci cache Laravel"
rm -f bootstrap/cache/*.php || true
$PHP_CMD artisan config:clear
$PHP_CMD artisan cache:clear
$PHP_CMD artisan route:clear
$PHP_CMD artisan view:clear

echo "==> Ricostruisci cache Laravel"
$PHP_CMD artisan config:cache
$PHP_CMD artisan route:cache
$PHP_CMD artisan view:cache || true

echo "==> DONE (default)"


































