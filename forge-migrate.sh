#!/bin/bash

# ========================================
# 🗄️ FLORENCE EGI - FORGE MIGRATIONS WRAPPER
# ========================================
# Wrapper semplificato per esecuzione da root progetto su Forge
#
# @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
# @version 1.0.0 (Forge-Root-Compatible)
# @date 2025-10-31
# ========================================

set -euo pipefail

# ANSI Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${BLUE}🚀 FlorenceEGI Forge Migration Wrapper${NC}"
echo -e "${BLUE}═══════════════════════════════════════${NC}"

# Verify we're in Laravel project root
if [ ! -f "artisan" ]; then
    echo -e "${RED}❌ Not in Laravel project root!${NC}" >&2
    echo -e "${CYAN}💡 Please run this script from the project root directory${NC}" >&2
    echo -e "${CYAN}   Example: bash forge-migrate.sh 1${NC}" >&2
    exit 1
fi

# Check if script file exists
if [ ! -f "scripts/bash/forge-migrate-atomic.sh" ]; then
    echo -e "${RED}❌ Main script not found: scripts/bash/forge-migrate-atomic.sh${NC}" >&2
    exit 1
fi

# Make sure the script is executable
chmod +x scripts/bash/forge-migrate-atomic.sh

echo -e "${GREEN}✅ Prerequisites OK${NC}"
echo -e "${BLUE}📂 Project root: $(pwd)${NC}"
echo -e "${BLUE}🔧 Executing main migration script...${NC}"
echo ""

# Execute the main script with all passed arguments
bash scripts/bash/forge-migrate-atomic.sh "$@"

