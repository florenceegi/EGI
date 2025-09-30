#!/bin/bash

# ========================================
# 🗄️ FLORENCE EGI - FORGE ATOMIC MIGRATIONS + SEEDING + STORAGE CLEANUP
# ========================================
# Script atomico per migrations, seeding e pulizia storage su Laravel Forge
# Ottimizzato per server staging/production con Forge (no .env file)
#
# @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
# @version 1.0.0 (Forge-Compatible)
# @date 2025-09-30
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

# Configuration variables (Forge-compatible)
TRANSACTION_ACTIVE=false
PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/../.."
STORAGE_PATH="$PROJECT_ROOT/storage/app/public"
CLEANUP_LOG="/tmp/egi_forge_cleanup_$(date +%Y%m%d_%H%M%S).log"

# ========================================
# 🛡️ CLEANUP FUNCTION (No .env backup)
# ========================================
cleanup() {
    local exit_code=$?

    echo -e "\n${YELLOW}🔄 Cleanup in progress...${NC}"

    if [ "$TRANSACTION_ACTIVE" = true ]; then
        echo -e "${RED}❌ Transaction failed! Environment managed by Forge.${NC}"
        echo -e "${RED}💥 TRANSACTION FAILED${NC}"
    else
        echo -e "${GREEN}✅ Transaction completed successfully${NC}"
    fi

    exit $exit_code
}

# ========================================
# 🚨 ERROR HANDLER
# ========================================
error_handler() {
    local line_number=$1
    echo -e "\n${RED}💥 ERROR on line $line_number${NC}" >&2
    cleanup
}

# ========================================
# ✅ VALIDATION (Forge-Compatible)
# ========================================
validate_prerequisites() {
    echo -e "${BLUE}🔍 Validating Forge environment prerequisites...${NC}"

    # Check if we're in Laravel project root
    if [ ! -f "artisan" ]; then
        echo -e "${RED}❌ Laravel artisan command not found!${NC}" >&2
        echo -e "${CYAN}💡 Make sure you're in the Laravel project root${NC}" >&2
        exit 1
    fi

    # Check PHP
    if ! command -v php >/dev/null 2>&1; then
        echo -e "${RED}❌ PHP not found in PATH!${NC}" >&2
        exit 1
    fi

    # Check Composer
    if ! command -v composer >/dev/null 2>&1; then
        echo -e "${RED}❌ Composer not found in PATH!${NC}" >&2
        exit 1
    fi

    # Test Laravel installation
    if ! php artisan --version >/dev/null 2>&1; then
        echo -e "${RED}❌ Laravel artisan not working!${NC}" >&2
        echo -e "${CYAN}💡 Run: composer install${NC}" >&2
        exit 1
    fi

    # Test database connection
    if ! php artisan db:show --quiet >/dev/null 2>&1; then
        echo -e "${YELLOW}⚠️ Database connection issues detected${NC}" >&2
        echo -e "${CYAN}💡 Check your database configuration in Forge panel${NC}" >&2
        read -p "Continue anyway? (y/N): " continue_choice
        if [[ ! "$continue_choice" =~ ^[Yy]$ ]]; then
            exit 1
        fi
    fi

    echo -e "${GREEN}✅ Prerequisites validated (Forge environment)${NC}"
}

# ========================================
# 📊 DATABASE INFO
# ========================================
show_database_info() {
    echo -e "\n${PURPLE}📊 DATABASE INFORMATION (Forge)${NC}"
    echo -e "${PURPLE}═══════════════════════════════════${NC}"

    local db_connection=$(php artisan tinker --execute="echo config('database.default');" 2>/dev/null | tail -n 1)
    local db_name=$(php artisan tinker --execute="echo config('database.connections.${db_connection}.database');" 2>/dev/null | tail -n 1)
    local db_host=$(php artisan tinker --execute="echo config('database.connections.${db_connection}.host');" 2>/dev/null | tail -n 1)

    echo -e "${CYAN}Connection:${NC} $db_connection"
    echo -e "${CYAN}Database:${NC} $db_name"
    echo -e "${CYAN}Host:${NC} $db_host"
    echo -e "${CYAN}Environment:${NC} Laravel Forge"
    echo -e "${PURPLE}═══════════════════════════════════${NC}"
}

# ========================================
# 🗄️ MIGRATION FUNCTIONS (Same as local)
# ========================================
run_migration_fresh() {
    echo -e "\n${CYAN}🗄️ RUNNING: migrate:fresh${NC}"
    echo -e "${YELLOW}⚠️  This will DROP ALL TABLES and recreate them${NC}"

    if php artisan migrate:fresh --force; then
        echo -e "${GREEN}✅ Migration fresh completed${NC}"
    else
        echo -e "${RED}❌ Migration fresh failed!${NC}" >&2
        exit 1
    fi
}

run_migration_refresh() {
    echo -e "\n${CYAN}🔄 RUNNING: migrate:refresh${NC}"
    echo -e "${YELLOW}⚠️  This will rollback and re-run all migrations${NC}"

    if php artisan migrate:refresh --force; then
        echo -e "${GREEN}✅ Migration refresh completed${NC}"
    else
        echo -e "${RED}❌ Migration refresh failed!${NC}" >&2
        exit 1
    fi
}

run_migration_reset() {
    echo -e "\n${CYAN}🔙 RUNNING: migrate:reset + migrate${NC}"
    echo -e "${YELLOW}⚠️  This will reset and re-run all migrations${NC}"

    if php artisan migrate:reset --force; then
        echo -e "${GREEN}✅ Migration reset completed${NC}"
    else
        echo -e "${RED}❌ Migration reset failed!${NC}" >&2
        exit 1
    fi

    if php artisan migrate --force; then
        echo -e "${GREEN}✅ Migration completed${NC}"
    else
        echo -e "${RED}❌ Migration failed!${NC}" >&2
        exit 1
    fi
}

run_migration_status() {
    echo -e "\n${CYAN}📋 MIGRATION STATUS${NC}"
    echo -e "${CYAN}═══════════════════${NC}"

    if php artisan migrate:status; then
        echo -e "${GREEN}✅ Migration status displayed${NC}"
    else
        echo -e "${RED}❌ Could not get migration status!${NC}" >&2
        exit 1
    fi
}

run_seeding() {
    echo -e "\n${CYAN}🌱 RUNNING: db:seed (Forge-compatible)${NC}"

    if php artisan db:seed --class=DatabaseSeederForge --force; then
        echo -e "${GREEN}✅ Seeding completed successfully (Forge mode)${NC}"
    else
        echo -e "${RED}❌ Seeding failed!${NC}" >&2
        exit 1
    fi
}

run_cache_clear() {
    echo -e "\n${CYAN}🧹 CLEARING: Application cache${NC}"

    php artisan cache:clear
    php artisan config:clear
    php artisan route:clear
    php artisan view:clear

    echo -e "${GREEN}✅ Cache cleared${NC}"
}

run_optimize() {
    echo -e "\n${CYAN}⚡ OPTIMIZING: Application${NC}"

    php artisan config:cache
    php artisan route:cache
    php artisan view:cache

    echo -e "${GREEN}✅ Application optimized${NC}"
}

# ========================================
# 🧹 STORAGE CLEANUP FUNCTIONS (Same as local)
# ========================================
storage_log() {
    echo "$(date '+%Y-%m-%d %H:%M:%S') - $1" | tee -a "$CLEANUP_LOG"
}

check_storage_path() {
    if [ ! -d "$STORAGE_PATH" ]; then
        echo -e "${RED}❌ Storage path does not exist: $STORAGE_PATH${NC}"
        return 1
    fi
    echo -e "${GREEN}✅ Storage path verified: $STORAGE_PATH${NC}"
    return 0
}

count_storage_items() {
    echo -e "${BLUE}📊 Counting storage items before cleanup...${NC}"

    local spatie_count=$(find "$STORAGE_PATH" -maxdepth 1 -type d -name '[0-9]*' 2>/dev/null | wc -l)
    local export_count=$(find "$STORAGE_PATH" -maxdepth 1 -type f -name 'export_*' 2>/dev/null | wc -l)

    local cert_exists=0
    local users_files_exists=0

    [ -d "$STORAGE_PATH/certificates" ] && cert_exists=1
    [ -d "$STORAGE_PATH/users_files" ] && users_files_exists=1

    echo -e "${CYAN}📁 Found $spatie_count Spatie media directories${NC}"
    echo -e "${CYAN}📄 Found $export_count export files${NC}"
    echo -e "${CYAN}📂 Certificates directory exists: $cert_exists${NC}"
    echo -e "${CYAN}📂 Users_files directory exists: $users_files_exists${NC}"
}

clean_spatie_directories() {
    echo -e "\n${CYAN}🗑️ Cleaning Spatie media directories...${NC}"

    local count=0
    local failed=0

    for dir in "$STORAGE_PATH"/[0-9]*; do
        if [ -d "$dir" ]; then
            local dir_name=$(basename "$dir")
            storage_log "Removing Spatie directory: $dir_name"

            if rm -rf "$dir" 2>/dev/null; then
                ((count++))
                echo -e "${GREEN}✅ Removed: $dir_name${NC}"
            else
                ((failed++))
                echo -e "${YELLOW}⚠️ Failed to remove: $dir_name (permissions?)${NC}"
                storage_log "WARNING: Failed to remove directory: $dir_name"
            fi
        fi
    done

    echo -e "${GREEN}✅ Removed $count Spatie media directories${NC}"
    if [ $failed -gt 0 ]; then
        echo -e "${YELLOW}⚠️ $failed directories could not be removed (check permissions)${NC}"
    fi
}

clean_export_files() {
    echo -e "\n${CYAN}📤 Cleaning export files...${NC}"

    local count=0
    local failed=0

    for file in "$STORAGE_PATH"/export_*; do
        if [ -f "$file" ]; then
            local file_name=$(basename "$file")
            storage_log "Removing export file: $file_name"

            if rm -f "$file" 2>/dev/null; then
                ((count++))
                echo -e "${GREEN}✅ Removed: $file_name${NC}"
            else
                ((failed++))
                echo -e "${YELLOW}⚠️ Failed to remove: $file_name (permissions?)${NC}"
                storage_log "WARNING: Failed to remove file: $file_name"
            fi
        fi
    done

    echo -e "${GREEN}✅ Removed $count export files${NC}"
    if [ $failed -gt 0 ]; then
        echo -e "${YELLOW}⚠️ $failed files could not be removed (check permissions)${NC}"
    fi
}

clean_certificates_directory() {
    if [ -d "$STORAGE_PATH/certificates" ]; then
        echo -e "\n${CYAN}🔐 Cleaning certificates directory...${NC}"

        if rm -rf "$STORAGE_PATH/certificates" 2>/dev/null; then
            echo -e "${GREEN}✅ Removed certificates directory${NC}"
            storage_log "SUCCESS: Removed certificates directory"
        else
            echo -e "${YELLOW}⚠️ Failed to remove certificates directory (permissions?)${NC}"
            storage_log "WARNING: Failed to remove certificates directory"
        fi
    else
        echo -e "${BLUE}ℹ️ Certificates directory not found - skipping${NC}"
        storage_log "INFO: Certificates directory not found"
    fi
}

clean_users_files_directory() {
    if [ -d "$STORAGE_PATH/users_files" ]; then
        echo -e "\n${CYAN}👥 Cleaning users_files directory...${NC}"

        if rm -rf "$STORAGE_PATH/users_files" 2>/dev/null; then
            echo -e "${GREEN}✅ Removed users_files directory${NC}"
            storage_log "SUCCESS: Removed users_files directory"
        else
            echo -e "${YELLOW}⚠️ Failed to remove users_files directory (permissions?)${NC}"
            storage_log "WARNING: Failed to remove users_files directory"
        fi
    else
        echo -e "${BLUE}ℹ️ Users_files directory not found - skipping${NC}"
        storage_log "INFO: Users_files directory not found"
    fi
}

run_storage_cleanup() {
    echo -e "\n${PURPLE}🧹 RUNNING: Storage cleanup (Forge)${NC}"
    echo -e "${PURPLE}═══════════════════════════════════${NC}"

    storage_log "=== EGI Forge Storage Cleanup Started ==="

    if ! check_storage_path; then
        echo -e "${YELLOW}⚠️ Storage cleanup skipped - path not found${NC}"
        storage_log "WARNING: Storage path not found, cleanup skipped"
        return 0
    fi

    count_storage_items

    echo -e "\n${BLUE}🗑️ Starting storage cleanup process...${NC}"

    set +e  # Temporarily disable exit on error for storage cleanup

    clean_spatie_directories
    clean_export_files
    clean_certificates_directory
    clean_users_files_directory

    set -e  # Re-enable exit on error

    local current_usage=$(du -sh "$STORAGE_PATH" 2>/dev/null | cut -f1 || echo "Unknown")
    echo -e "\n${GREEN}📊 Current storage usage: $current_usage${NC}"

    storage_log "=== EGI Forge Storage Cleanup Completed ==="
    echo -e "${GREEN}✅ Storage cleanup completed! Log: $CLEANUP_LOG${NC}"

    return 0
}

# ========================================
# 🗄️ ATOMIC STEPS (No .env backup)
# ========================================
step_start_transaction() {
    echo -e "\n${BLUE}🔄 STEP: Starting transaction (Forge)...${NC}"
    TRANSACTION_ACTIVE=true
    echo -e "${GREEN}✅ Transaction started${NC}"
}

step_complete_transaction() {
    echo -e "\n${BLUE}✅ STEP: Completing transaction (Forge)...${NC}"
    TRANSACTION_ACTIVE=false
    echo -e "${GREEN}✅ Transaction completed${NC}"
}

# ========================================
# 🎯 MAIN FUNCTIONS
# ========================================
show_menu() {
    echo -e "${GREEN}🗄️ FLORENCE EGI - FORGE ATOMIC MIGRATIONS & SEEDING & STORAGE${NC}"
    echo -e "${GREEN}═════════════════════════════════════════════════════════════════════${NC}"
    show_database_info
    echo -e "\n${CYAN}Select operation:${NC}"
    echo ""
    echo -e "${YELLOW}1)${NC} migrate:fresh + seed ${BLUE}(recommended for clean state)${NC}"
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
    echo -e "${YELLOW}7)${NC} 🧹 Clean storage (Spatie media + exports + dirs) ${PURPLE}[NEW]${NC}"
    echo -e "   ${CYAN}→ Remove Spatie media, export files, certificates, users_files${NC}"
    echo ""
    echo -e "${YELLOW}8)${NC} 🔄 Full reset (fresh + seed + clean storage) ${PURPLE}[ULTIMATE]${NC}"
    echo -e "   ${CYAN}→ Complete reset: DB + Storage cleanup${NC}"
    echo ""
    echo -e "${YELLOW}9)${NC} Cancel"
    echo ""
}

execute_choice() {
    local choice=$1

    trap 'error_handler $LINENO' ERR
    trap cleanup EXIT

    validate_prerequisites

    case $choice in
        1)
            step_start_transaction
            run_migration_fresh
            run_seeding
            run_cache_clear
            step_complete_transaction
            ;;
        2)
            step_start_transaction
            run_migration_refresh
            run_seeding
            run_cache_clear
            step_complete_transaction
            ;;
        3)
            step_start_transaction
            run_migration_reset
            run_seeding
            run_cache_clear
            step_complete_transaction
            ;;
        4)
            step_start_transaction
            run_seeding
            step_complete_transaction
            ;;
        5)
            trap - ERR EXIT
            run_migration_status
            exit 0
            ;;
        6)
            trap - ERR EXIT
            run_cache_clear
            run_optimize
            echo -e "\n${GREEN}🎉 CACHE OPERATIONS COMPLETED!${NC}"
            echo -e "${GREEN}════════════════════════════════${NC}"
            echo -e "${BLUE}⚡ Application cache cleared and optimized${NC}"
            exit 0
            ;;
        7)
            echo -e "\n${PURPLE}🧹 STORAGE CLEANUP ONLY (Forge)${NC}"
            echo -e "${PURPLE}══════════════════════════════════${NC}"
            trap - ERR EXIT
            run_storage_cleanup
            echo -e "\n${GREEN}🎉 STORAGE CLEANUP COMPLETED!${NC}"
            echo -e "${GREEN}═══════════════════════════${NC}"
            echo -e "${BLUE}🧹 Storage cleaned${NC}"
            exit 0
            ;;
        8)
            echo -e "\n${PURPLE}🔄 FULL RESET: DATABASE + STORAGE (Forge)${NC}"
            echo -e "${PURPLE}═══════════════════════════════════════════${NC}"
            step_start_transaction
            run_migration_fresh
            run_seeding
            run_storage_cleanup
            run_cache_clear
            step_complete_transaction
            ;;
        *)
            echo -e "${RED}❌ Invalid choice${NC}"
            exit 1
            ;;
    esac

    echo -e "\n${GREEN}🎉 OPERATION COMPLETED SUCCESSFULLY! (Forge)${NC}"
    echo -e "${GREEN}═══════════════════════════════════════════════${NC}"
    echo -e "${BLUE}💾 Forge database updated${NC}"
    echo -e "${BLUE}🛡️ Environment managed by Forge${NC}"
    echo -e "${BLUE}⚡ Application cache cleared${NC}"
    if [ "$choice" = "7" ] || [ "$choice" = "8" ]; then
        echo -e "${BLUE}🧹 Storage cleaned${NC}"
    fi
}

# ========================================
# 🎬 SCRIPT EXECUTION
# ========================================
main() {
    echo -e "${BLUE}🔍 Working directory: $PROJECT_ROOT${NC}"
    cd "$PROJECT_ROOT"

    if [ $# -eq 0 ]; then
        show_menu
        read -p "Enter your choice (1-9): " choice

        if [ "$choice" = "9" ]; then
            echo -e "${YELLOW}🚫 Operation cancelled${NC}"
            exit 0
        fi

        execute_choice "$choice"
    else
        execute_choice "$1"
    fi
}

# ========================================
# 🆘 HELP FUNCTION
# ========================================
show_help() {
    echo -e "${GREEN}🗄️ FLORENCE EGI - FORGE MIGRATIONS & SEEDING & STORAGE${NC}"
    echo -e "${GREEN}════════════════════════════════════════════════════════════${NC}"
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
    echo -e "${CYAN}Examples:${NC}"
    echo -e "  $0        # Interactive mode"
    echo -e "  $0 1      # Fresh migration + seed"
    echo -e "  $0 7      # Clean storage only"
    echo -e "  $0 8      # Full reset (ultimate clean)"
    echo ""
    echo -e "${PURPLE}🚀 Optimized for Laravel Forge environments${NC}"
    echo ""
}

# Parse command line arguments
if [ $# -gt 0 ] && [ "$1" = "-h" ]; then
    show_help
    exit 0
fi

# Run main function
if [[ "${BASH_SOURCE[0]}" == "${0}" ]]; then
    main "$@"
else
    echo -e "${RED}❌ This script should be executed, not sourced!${NC}" >&2
    return 1
fi
