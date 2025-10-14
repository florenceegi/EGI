#!/bin/bash

################################################################################
# START WEB TERMINAL WITH GOTTY (NO SUDO REQUIRED)
################################################################################
# Purpose: Download and start Gotty web terminal - single binary, no installation
# Author: Padmin D. Curtis (AI Partner OS3.0)
# Date: 2025-10-14
# Usage: bash start-web-terminal.sh
################################################################################

set -e

echo "════════════════════════════════════════════════════════════════"
echo "  🖥️  WEB TERMINAL STARTUP (GOTTY)"
echo "════════════════════════════════════════════════════════════════"
echo ""

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m'

# Configuration
GOTTY_VERSION="1.0.1"
GOTTY_PORT=9000
INSTALL_DIR="$HOME/bin"
GOTTY_BIN="$INSTALL_DIR/gotty"

################################################################################
# STEP 1: Create bin directory if not exists
################################################################################

echo -e "${BLUE}[1/4]${NC} Preparo directory..."

mkdir -p "$INSTALL_DIR"

echo -e "${GREEN}✅ Directory pronta: $INSTALL_DIR${NC}"
echo ""

################################################################################
# STEP 2: Download Gotty if not present
################################################################################

echo -e "${BLUE}[2/4]${NC} Verifico Gotty..."

if [ ! -f "$GOTTY_BIN" ]; then
    echo -e "${YELLOW}⚠️  Gotty non trovato, scarico...${NC}"
    
    cd "$INSTALL_DIR"
    
    # Download Gotty (Linux AMD64)
    wget -q https://github.com/yudai/gotty/releases/download/v${GOTTY_VERSION}/gotty_linux_amd64.tar.gz
    
    # Extract
    tar -xzf gotty_linux_amd64.tar.gz
    
    # Make executable
    chmod +x gotty
    
    # Cleanup
    rm gotty_linux_amd64.tar.gz
    
    echo -e "${GREEN}✅ Gotty scaricato e installato${NC}"
else
    echo -e "${GREEN}✅ Gotty già presente${NC}"
fi

echo ""

################################################################################
# STEP 3: Check if already running
################################################################################

echo -e "${BLUE}[3/4]${NC} Verifico se già in esecuzione..."

if ps aux | grep -v grep | grep "gotty" > /dev/null; then
    echo -e "${YELLOW}⚠️  Gotty già in esecuzione!${NC}"
    echo ""
    echo -e "${BLUE}Processi attivi:${NC}"
    ps aux | grep -v grep | grep "gotty"
    echo ""
    echo -e "${YELLOW}Per fermarlo: kill \$(pgrep gotty)${NC}"
    echo ""
    echo -e "${GREEN}Accedi a: http://13.48.57.194:${GOTTY_PORT}${NC}"
    exit 0
fi

echo -e "${GREEN}✅ Nessun processo in esecuzione${NC}"
echo ""

################################################################################
# STEP 4: Start Gotty in background
################################################################################

echo -e "${BLUE}[4/4]${NC} Avvio terminale web..."

# Start Gotty in background (with bash shell)
nohup "$GOTTY_BIN" --port "$GOTTY_PORT" --permit-write --reconnect bash > /tmp/gotty.log 2>&1 &
PID=$!

# Wait for startup
sleep 2

# Check if running
if ps -p $PID > /dev/null; then
    echo -e "${GREEN}✅ Terminale web avviato con successo!${NC}"
    echo ""
    echo -e "${GREEN}════════════════════════════════════════════════════════════════${NC}"
    echo -e "${GREEN}  ✅ TERMINALE WEB ATTIVO${NC}"
    echo -e "${GREEN}════════════════════════════════════════════════════════════════${NC}"
    echo ""
    echo -e "${BLUE}📋 ACCEDI DA BROWSER:${NC}"
    echo -e "   ${YELLOW}http://13.48.57.194:${GOTTY_PORT}${NC}"
    echo ""
    echo -e "${BLUE}ℹ️  INFO:${NC}"
    echo -e "   PID: $PID"
    echo -e "   Port: $GOTTY_PORT"
    echo -e "   Log: /tmp/gotty.log"
    echo -e "   Shell: bash"
    echo ""
    echo -e "${BLUE}🛠️  COMANDI UTILI:${NC}"
    echo -e "   ${YELLOW}ps aux | grep gotty${NC}           - Verifica processo"
    echo -e "   ${YELLOW}kill $PID${NC}                     - Ferma terminale"
    echo -e "   ${YELLOW}cat /tmp/gotty.log${NC}            - Vedi log"
    echo ""
    echo -e "${YELLOW}⚠️  NOTA: Il terminale sarà accessibile senza password!${NC}"
    echo -e "${YELLOW}   Configura firewall per limitare accesso se necessario.${NC}"
    echo ""
else
    echo -e "${RED}❌ Errore all'avvio${NC}"
    echo ""
    echo -e "${BLUE}Log:${NC}"
    cat /tmp/gotty.log
    exit 1
fi
