#!/bin/bash
# ============================================
# Script Deploy EGI Staging - N.A.T.A.N.
# ============================================
# Esegui questi comandi SUL SERVER STAGING

set -e  # Exit on error

echo "🚀 Starting EGI Staging Deploy..."

# ============================================
# 1. BACKUP DATABASE
# ============================================
echo "📦 Creating database backup..."
BACKUP_FILE="backup_$(date +%Y%m%d_%H%M%S).sql"
# mysqldump -u username -p database_name > $BACKUP_FILE
# echo "✅ Backup saved: $BACKUP_FILE"

# ============================================
# 2. GIT PULL
# ============================================
echo "📥 Pulling latest code from main branch..."
git pull origin main
echo "✅ Code updated"

# ============================================
# 3. COMPOSER & NPM
# ============================================
echo "📦 Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader

echo "📦 Installing Node dependencies..."
npm install

echo "🏗️  Building assets..."
npm run build
echo "✅ Dependencies installed"

# ============================================
# 4. MIGRATIONS
# ============================================
echo "🗄️  Running database migrations..."
php artisan migrate --force
echo "✅ Migrations completed"

# ============================================
# 5. CLEAR CACHE
# ============================================
echo "🧹 Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan optimize
echo "✅ Cache cleared"

# ============================================
# 6. PERMISSIONS
# ============================================
echo "🔐 Setting permissions..."
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
echo "✅ Permissions set"

# ============================================
# 7. RESTART SERVICES
# ============================================
echo "🔄 Restarting services..."
# sudo systemctl restart php8.2-fpm
# sudo systemctl restart nginx
echo "✅ Services restarted"

echo ""
echo "✅ ✅ ✅ DEPLOY COMPLETATO! ✅ ✅ ✅"
echo ""
echo "📋 PROSSIMI STEP:"
echo "1. Genera embeddings: php artisan pa:generate-embeddings --limit=100"
echo "2. Test N.A.T.A.N.: https://staging.egi.it/pa/natan/chat"
echo "3. Verifica logs: tail -f storage/logs/laravel.log"
echo ""

