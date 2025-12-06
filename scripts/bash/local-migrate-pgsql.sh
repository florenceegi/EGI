#!/bin/bash

# ========================================
# 🗄️ FLORENCE EGI - POSTGRESQL LOCAL MIGRATIONS + SEEDING + STORAGE CLEANUP
# ========================================
# Script atomico per migrations, seeding e pulizia storage con PostgreSQL
# Wrapper che configura l'ambiente per PostgreSQL e usa lo script originale
#
# @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
# @version 1.0.0 (PostgreSQL Support)
# @date 2025-12-06
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

# Configuration
ORIGINAL_ENV="$PROJECT_ROOT/.env"
BACKUP_ENV="$PROJECT_ROOT/.env.mariadb.backup"
PGSQL_CONFIG_FILE="$PROJECT_ROOT/.env.pgsql.config"

# PostgreSQL defaults (can be overridden)
PGSQL_HOST="${PGSQL_HOST:-127.0.0.1}"
PGSQL_PORT="${PGSQL_PORT:-5433}"
PGSQL_DATABASE="${PGSQL_DATABASE:-florenceegi_dev}"
PGSQL_USERNAME="${PGSQL_USERNAME:-florenceegi}"
PGSQL_PASSWORD="${PGSQL_PASSWORD:-florenceegi_dev_2025}"

# ========================================
# 🛡️ CLEANUP FUNCTION
# ========================================
cleanup_pgsql() {
    local exit_code=$?
    
    echo -e "\n${YELLOW}🔄 PostgreSQL cleanup in progress...${NC}"
    
    # If backup exists, restore MariaDB config
    if [ -f "$BACKUP_ENV" ]; then
        echo -e "${CYAN}📦 Restoring original MariaDB .env...${NC}"
        mv "$BACKUP_ENV" "$ORIGINAL_ENV"
        php artisan config:clear 2>/dev/null || true
        echo -e "${GREEN}✅ Original .env restored${NC}"
    fi
    
    exit $exit_code
}

# ========================================
# 🐘 POSTGRESQL CONFIG FUNCTIONS
# ========================================
switch_to_postgresql() {
    echo -e "\n${PURPLE}🐘 SWITCHING TO POSTGRESQL${NC}"
    echo -e "${PURPLE}═══════════════════════════${NC}"
    
    # Backup current .env (MariaDB)
    if [ -f "$ORIGINAL_ENV" ]; then
        cp "$ORIGINAL_ENV" "$BACKUP_ENV"
        echo -e "${BLUE}📦 MariaDB config backed up${NC}"
    fi
    
    # Modify .env for PostgreSQL
    sed -i "s/^DB_CONNECTION=.*/DB_CONNECTION=pgsql/" "$ORIGINAL_ENV"
    sed -i "s/^DB_HOST=.*/DB_HOST=$PGSQL_HOST/" "$ORIGINAL_ENV"
    sed -i "s/^DB_PORT=.*/DB_PORT=$PGSQL_PORT/" "$ORIGINAL_ENV"
    sed -i "s/^DB_DATABASE=.*/DB_DATABASE=$PGSQL_DATABASE/" "$ORIGINAL_ENV"
    sed -i "s/^DB_USERNAME=.*/DB_USERNAME=$PGSQL_USERNAME/" "$ORIGINAL_ENV"
    sed -i "s/^DB_PASSWORD=.*/DB_PASSWORD='$PGSQL_PASSWORD'/" "$ORIGINAL_ENV"
    
    # Clear config cache
    php artisan config:clear 2>/dev/null || true
    
    echo -e "${GREEN}✅ Switched to PostgreSQL${NC}"
    echo -e "${CYAN}   Host: $PGSQL_HOST:$PGSQL_PORT${NC}"
    echo -e "${CYAN}   Database: $PGSQL_DATABASE${NC}"
    echo -e "${CYAN}   User: $PGSQL_USERNAME${NC}"
}

verify_postgresql_connection() {
    echo -e "\n${BLUE}🔍 Verifying PostgreSQL connection...${NC}"
    
    # Test connection using Laravel
    if php artisan tinker --execute="DB::connection()->getPdo(); echo 'OK';" 2>/dev/null | grep -q "OK"; then
        local db_version=$(php artisan tinker --execute="echo DB::select('SELECT version()')[0]->version;" 2>/dev/null | tail -1 | head -c 50)
        echo -e "${GREEN}✅ PostgreSQL connection successful${NC}"
        echo -e "${CYAN}   Version: $db_version...${NC}"
        return 0
    else
        echo -e "${RED}❌ PostgreSQL connection FAILED!${NC}"
        echo -e "${YELLOW}💡 Check that PostgreSQL is running on port $PGSQL_PORT${NC}"
        echo -e "${YELLOW}💡 Verify credentials: $PGSQL_USERNAME@$PGSQL_HOST${NC}"
        return 1
    fi
}

show_pgsql_info() {
    echo -e "\n${PURPLE}🐘 POSTGRESQL DATABASE INFO${NC}"
    echo -e "${PURPLE}═══════════════════════════════${NC}"
    
    local db_connection=$(php artisan tinker --execute="echo config('database.default');" 2>/dev/null | tail -n 1)
    local db_name=$(php artisan tinker --execute="echo config('database.connections.pgsql.database');" 2>/dev/null | tail -n 1)
    local db_host=$(php artisan tinker --execute="echo config('database.connections.pgsql.host');" 2>/dev/null | tail -n 1)
    local db_port=$(php artisan tinker --execute="echo config('database.connections.pgsql.port');" 2>/dev/null | tail -n 1)
    
    echo -e "${CYAN}Connection:${NC} $db_connection"
    echo -e "${CYAN}Database:${NC} $db_name"
    echo -e "${CYAN}Host:${NC} $db_host:$db_port"
    echo -e "${PURPLE}═══════════════════════════════${NC}"
}

# ========================================
# 🎯 MAIN MENU
# ========================================
show_menu() {
    echo -e "${GREEN}🐘 FLORENCE EGI - POSTGRESQL MIGRATIONS & SEEDING${NC}"
    echo -e "${GREEN}═══════════════════════════════════════════════════════════════════${NC}"
    show_pgsql_info
    echo -e "\n${CYAN}Select operation:${NC}"
    echo ""
    echo -e "${YELLOW}1)${NC} migrate:fresh + seed ${BLUE}(recommended for clean PostgreSQL state)${NC}"
    echo -e "   ${CYAN}→ Drops all tables, recreates + seeds${NC}"
    echo ""
    echo -e "${YELLOW}2)${NC} migrate:refresh + seed"
    echo -e "   ${CYAN}→ Rollback all, re-run + seeds${NC}"
    echo ""
    echo -e "${YELLOW}3)${NC} migrate:reset + migrate + seed"
    echo -e "   ${CYAN}→ Reset, migrate, then seed${NC}"
    echo ""
    echo -e "${YELLOW}4)${NC} Only seeding (preserve data)"
    echo -e "   ${CYAN}→ Only run seeders${NC}"
    echo ""
    echo -e "${YELLOW}5)${NC} Migration status"
    echo -e "   ${CYAN}→ Show current migration status${NC}"
    echo ""
    echo -e "${YELLOW}6)${NC} Clear cache + optimize"
    echo -e "   ${CYAN}→ Clear all cache and optimize${NC}"
    echo ""
    echo -e "${YELLOW}7)${NC} 🧹 Clean storage (Spatie media + exports + dirs)"
    echo -e "   ${CYAN}→ Remove Spatie media, export files, certificates, users_files${NC}"
    echo ""
    echo -e "${YELLOW}8)${NC} 🔄 Full reset (fresh + seed + clean storage) ${PURPLE}[ULTIMATE]${NC}"
    echo -e "   ${CYAN}→ Complete reset: DB + Storage cleanup${NC}"
    echo ""
    echo -e "${YELLOW}9)${NC} 🔙 Cancel and restore MariaDB config"
    echo ""
}

# ========================================
# 🎬 MAIN EXECUTION
# ========================================
main() {
    echo -e "${BLUE}🔍 Working directory: $PROJECT_ROOT${NC}"
    
    cd "$PROJECT_ROOT" || {
        echo -e "${RED}❌ Failed to change to project root: $PROJECT_ROOT${NC}" >&2
        exit 1
    }
    
    # Verify Laravel project
    if [ ! -f "artisan" ]; then
        echo -e "${RED}❌ Not in Laravel project root (artisan not found)${NC}" >&2
        exit 1
    fi
    
    # Set cleanup trap
    trap cleanup_pgsql EXIT
    
    # Switch to PostgreSQL
    switch_to_postgresql
    
    # Verify connection
    if ! verify_postgresql_connection; then
        echo -e "${RED}❌ Cannot proceed without PostgreSQL connection${NC}"
        exit 1
    fi
    
    if [ $# -eq 0 ]; then
        # Interactive mode
        show_menu
        read -p "Enter your choice (1-9): " choice
        
        if [ "$choice" = "9" ]; then
            echo -e "${YELLOW}🚫 Operation cancelled${NC}"
            exit 0
        fi
    else
        choice="$1"
    fi
    
    # Execute the chosen operation using the original script's logic
    echo -e "\n${PURPLE}🚀 EXECUTING OPERATION ON POSTGRESQL${NC}"
    echo -e "${PURPLE}═══════════════════════════════════════${NC}"
    
    case $choice in
        1)
            echo -e "${CYAN}🗄️ RUNNING: migrate:fresh + seed${NC}"
            php artisan migrate:fresh --force
            php artisan db:seed --force
            php artisan cache:clear
            php artisan config:clear
            ;;
        2)
            echo -e "${CYAN}🔄 RUNNING: migrate:refresh + seed${NC}"
            php artisan migrate:refresh --force
            php artisan db:seed --force
            php artisan cache:clear
            php artisan config:clear
            ;;
        3)
            echo -e "${CYAN}🔙 RUNNING: migrate:reset + migrate + seed${NC}"
            php artisan migrate:reset --force
            php artisan migrate --force
            php artisan db:seed --force
            php artisan cache:clear
            php artisan config:clear
            ;;
        4)
            echo -e "${CYAN}🌱 RUNNING: db:seed${NC}"
            php artisan db:seed --force
            ;;
        5)
            echo -e "${CYAN}📋 MIGRATION STATUS${NC}"
            php artisan migrate:status
            ;;
        6)
            echo -e "${CYAN}🧹 CLEARING CACHE${NC}"
            php artisan cache:clear
            php artisan config:clear
            php artisan route:clear
            php artisan view:clear
            echo -e "${CYAN}⚡ OPTIMIZING${NC}"
            php artisan config:cache
            php artisan route:cache
            ;;
        7)
            echo -e "${CYAN}🧹 CLEANING STORAGE${NC}"
            STORAGE_PATH="$PROJECT_ROOT/storage/app/public"
            if [ -d "$STORAGE_PATH" ]; then
                rm -rf "$STORAGE_PATH"/[0-9]* 2>/dev/null || true
                rm -f "$STORAGE_PATH"/export_* 2>/dev/null || true
                rm -rf "$STORAGE_PATH/certificates" 2>/dev/null || true
                rm -rf "$STORAGE_PATH/users_files" 2>/dev/null || true
                echo -e "${GREEN}✅ Storage cleaned${NC}"
            fi
            ;;
        8)
            echo -e "${CYAN}🔄 FULL RESET${NC}"
            php artisan migrate:fresh --force
            php artisan db:seed --force
            STORAGE_PATH="$PROJECT_ROOT/storage/app/public"
            if [ -d "$STORAGE_PATH" ]; then
                rm -rf "$STORAGE_PATH"/[0-9]* 2>/dev/null || true
                rm -f "$STORAGE_PATH"/export_* 2>/dev/null || true
                rm -rf "$STORAGE_PATH/certificates" 2>/dev/null || true
                rm -rf "$STORAGE_PATH/users_files" 2>/dev/null || true
            fi
            php artisan cache:clear
            php artisan config:clear
            ;;
        *)
            echo -e "${RED}❌ Invalid choice: $choice${NC}"
            exit 1
            ;;
    esac
    
    echo -e "\n${GREEN}🎉 POSTGRESQL OPERATION COMPLETED!${NC}"
    echo -e "${GREEN}══════════════════════════════════════${NC}"
    echo -e "${BLUE}🐘 PostgreSQL database: $PGSQL_DATABASE${NC}"
    echo -e "${BLUE}📦 MariaDB .env will be restored on exit${NC}"
}

# ========================================
# 🆘 HELP
# ========================================
show_help() {
    echo -e "${GREEN}🐘 FLORENCE EGI - POSTGRESQL MIGRATIONS${NC}"
    echo -e "${GREEN}═══════════════════════════════════════════════════════════════${NC}"
    echo ""
    echo -e "${CYAN}Usage:${NC}"
    echo -e "  $0 [option]"
    echo ""
    echo -e "${CYAN}Options:${NC}"
    echo -e "  ${YELLOW}1${NC}    migrate:fresh + seed"
    echo -e "  ${YELLOW}2${NC}    migrate:refresh + seed"
    echo -e "  ${YELLOW}3${NC}    migrate:reset + migrate + seed"
    echo -e "  ${YELLOW}4${NC}    only seeding"
    echo -e "  ${YELLOW}5${NC}    migration status"
    echo -e "  ${YELLOW}6${NC}    clear cache + optimize"
    echo -e "  ${YELLOW}7${NC}    clean storage only"
    echo -e "  ${YELLOW}8${NC}    full reset (fresh + seed + clean storage)"
    echo -e "  ${YELLOW}-h${NC}   show this help"
    echo ""
    echo -e "${CYAN}Environment Variables:${NC}"
    echo -e "  ${YELLOW}PGSQL_HOST${NC}      PostgreSQL host (default: 127.0.0.1)"
    echo -e "  ${YELLOW}PGSQL_PORT${NC}      PostgreSQL port (default: 5433)"
    echo -e "  ${YELLOW}PGSQL_DATABASE${NC}  Database name (default: florenceegi_dev)"
    echo -e "  ${YELLOW}PGSQL_USERNAME${NC}  Username (default: florenceegi)"
    echo -e "  ${YELLOW}PGSQL_PASSWORD${NC}  Password (default: florenceegi_dev_2025)"
    echo ""
    echo -e "${CYAN}Examples:${NC}"
    echo -e "  $0              # Interactive mode"
    echo -e "  $0 1            # Fresh migration + seed"
    echo -e "  $0 5            # Show migration status"
    echo -e "  PGSQL_PORT=5432 $0 1   # Use custom port"
    echo ""
    echo -e "${PURPLE}Note:${NC} After completion, MariaDB .env is automatically restored."
    echo ""
}

# Parse arguments
if [ $# -gt 0 ] && [ "$1" = "-h" ]; then
    show_help
    exit 0
fi

# Run main
if [[ "${BASH_SOURCE[0]}" == "${0}" ]]; then
    main "$@"
fi
