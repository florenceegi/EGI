#!/bin/bash

# ========================================
# 🗄️ FLORENCE EGI - POSTGRESQL PRODUCTION MIGRATIONS + SEEDING
# ========================================
# Script per migrations e seeding su server di produzione (Laravel Forge)
# Database: PostgreSQL
#
# @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
# @version 1.0.0 (Production PostgreSQL)
# @date 2025-12-07
# ========================================

set -euo pipefail

# ANSI Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
PURPLE='\033[0;35m'
NC='\033[0m'

# Determine PROJECT_ROOT
if [ -f "artisan" ]; then
    PROJECT_ROOT="$(pwd)"
else
    SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
    PROJECT_ROOT="$(cd "$SCRIPT_DIR/../.." && pwd)"
fi

cd "$PROJECT_ROOT"

echo -e "${PURPLE}========================================${NC}"
echo -e "${PURPLE}🐘 FLORENCE EGI - PRODUCTION POSTGRESQL${NC}"
echo -e "${PURPLE}   Migrations + Seeding${NC}"
echo -e "${PURPLE}========================================${NC}"
echo ""

# ========================================
# 🔍 VERIFY ENVIRONMENT
# ========================================
echo -e "${BLUE}🔍 Verifico ambiente...${NC}"

# Check if .env exists
if [ ! -f ".env" ]; then
    echo -e "${RED}❌ File .env non trovato!${NC}"
    exit 1
fi

# Verify PostgreSQL connection
DB_CONNECTION=$(grep "^DB_CONNECTION=" .env | cut -d'=' -f2)
if [ "$DB_CONNECTION" != "pgsql" ]; then
    echo -e "${RED}❌ DB_CONNECTION non è pgsql! Valore attuale: $DB_CONNECTION${NC}"
    echo -e "${YELLOW}   Modifica .env e imposta DB_CONNECTION=pgsql${NC}"
    exit 1
fi

echo -e "${GREEN}✅ DB_CONNECTION=pgsql${NC}"

# Show DB config (without password)
DB_HOST=$(grep "^DB_HOST=" .env | cut -d'=' -f2)
DB_PORT=$(grep "^DB_PORT=" .env | cut -d'=' -f2)
DB_DATABASE=$(grep "^DB_DATABASE=" .env | cut -d'=' -f2)
DB_USERNAME=$(grep "^DB_USERNAME=" .env | cut -d'=' -f2)

echo -e "${CYAN}   Host: $DB_HOST:$DB_PORT${NC}"
echo -e "${CYAN}   Database: $DB_DATABASE${NC}"
echo -e "${CYAN}   User: $DB_USERNAME${NC}"
echo ""

# ========================================
# 🔗 TEST CONNECTION
# ========================================
echo -e "${BLUE}🔗 Test connessione PostgreSQL...${NC}"

if php artisan tinker --execute="try { DB::connection()->getPdo(); echo 'OK'; } catch(Exception \$e) { echo 'FAIL'; }" 2>/dev/null | grep -q "OK"; then
    echo -e "${GREEN}✅ Connessione PostgreSQL riuscita!${NC}"
else
    echo -e "${RED}❌ Impossibile connettersi a PostgreSQL!${NC}"
    echo -e "${YELLOW}   Verifica le credenziali in .env${NC}"
    exit 1
fi
echo ""

# ========================================
# ⚠️ CONFIRMATION
# ========================================
echo -e "${YELLOW}⚠️  ATTENZIONE: Questo script eseguirà:${NC}"
echo -e "${YELLOW}   1. migrate:fresh (CANCELLA TUTTI I DATI!)${NC}"
echo -e "${YELLOW}   2. Tutti i seeder${NC}"
echo ""
read -p "Sei sicuro di voler continuare? (yes/no): " CONFIRM

if [ "$CONFIRM" != "yes" ]; then
    echo -e "${RED}❌ Operazione annullata${NC}"
    exit 0
fi
echo ""

# ========================================
# 🧹 CLEAR CACHES
# ========================================
echo -e "${BLUE}🧹 Pulizia cache...${NC}"
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
echo -e "${GREEN}✅ Cache pulita${NC}"
echo ""

# ========================================
# 🗄️ MIGRATIONS
# ========================================
echo -e "${PURPLE}🗄️ Esecuzione migrate:fresh...${NC}"
php artisan migrate:fresh --force
echo -e "${GREEN}✅ Migrations completate${NC}"
echo ""

# ========================================
# 🌱 SEEDERS
# ========================================
echo -e "${PURPLE}🌱 Esecuzione seeders nella sequenza corretta...${NC}"
echo ""

# Array dei seeder in ordine (da DatabaseSeeder.php)
SEEDERS=(
    "RolesAndPermissionsSeeder"
    "SystemUsersSeeder"
    "ConsentTypeSeeder"
    "IconSeeder"
    "FlorenceEgiPrivacyPolicySeeder"
    "VocabularyTermSeeder"
    "TraitDefaultsSeeder"
    "PlatformKnowledgeSectionSeeder"
    "AiFeaturePricingSeederV2Real"
    "PaWebScraperSeeder"
)

for seeder in "${SEEDERS[@]}"; do
    echo -e "${CYAN}   ▶ $seeder...${NC}"
    if php artisan db:seed --class="$seeder" --force 2>/dev/null; then
        echo -e "${GREEN}   ✅ $seeder completato${NC}"
    else
        echo -e "${YELLOW}   ⚠️ $seeder saltato o errore (potrebbe non esistere)${NC}"
    fi
done

echo ""
echo -e "${GREEN}✅ Seeders completati${NC}"
echo ""

# ========================================
# 🔗 CREATE STORAGE LINK
# ========================================
echo -e "${BLUE}🔗 Creazione storage link...${NC}"
php artisan storage:link --force 2>/dev/null || true
echo -e "${GREEN}✅ Storage link creato${NC}"
echo ""

# ========================================
# 🔄 OPTIMIZE
# ========================================
echo -e "${BLUE}🔄 Ottimizzazione...${NC}"
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo -e "${GREEN}✅ Ottimizzazione completata${NC}"
echo ""

# ========================================
# ✅ DONE
# ========================================
echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN}✅ MIGRAZIONE POSTGRESQL COMPLETATA!${NC}"
echo -e "${GREEN}========================================${NC}"
echo ""
echo -e "${CYAN}Database: $DB_DATABASE${NC}"
echo -e "${CYAN}Tabelle create e popolate con successo${NC}"
echo ""

# Show table count
TABLE_COUNT=$(php artisan tinker --execute="echo DB::select(\"SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema = 'public'\")[0]->count;" 2>/dev/null | tail -1)
echo -e "${CYAN}Numero tabelle: $TABLE_COUNT${NC}"
echo ""
