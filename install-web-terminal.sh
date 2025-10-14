#!/bin/bash

################################################################################
# START ALGOKIT MICROSERVICE ON STAGING
################################################################################
# Purpose: Start AlgoKit microservice manually (no sudo required)
# Author: Padmin D. Curtis (AI Partner OS3.0)
# Date: 2025-10-14
# Usage: bash install-web-terminal.sh (from Forge Commands)
################################################################################

set -e

echo "════════════════════════════════════════════════════════════════"
echo "  � AVVIO ALGOKIT MICROSERVICE"
echo "════════════════════════════════════════════════════════════════"
echo ""

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m'

# Configuration
APP_DIR="/home/forge/app.13.48.57.194.sslip.io"
MICROSERVICE_DIR="$APP_DIR/algokit-microservice"
LOG_FILE="/tmp/algokit-staging.log"

################################################################################
# STEP 1: Check if microservice directory exists
################################################################################

echo -e "${BLUE}[1/4]${NC} Verifico directory microservice..."

if [ ! -d "$MICROSERVICE_DIR" ]; then
    echo -e "${RED}❌ Directory $MICROSERVICE_DIR non trovata${NC}"
    echo -e "${YELLOW}💡 Devi prima fare git pull per scaricare il microservice${NC}"
    exit 1
fi

cd "$MICROSERVICE_DIR"
echo -e "${GREEN}✅ Directory trovata: $MICROSERVICE_DIR${NC}"
echo ""

################################################################################
# STEP 2: Check if already running
################################################################################

echo -e "${BLUE}[2/4]${NC} Verifico se già in esecuzione..."

if ps aux | grep -v grep | grep "node server.js" > /dev/null; then
    echo -e "${YELLOW}⚠️  Microservice già in esecuzione!${NC}"
    echo ""
    echo -e "${BLUE}Processi attivi:${NC}"
    ps aux | grep -v grep | grep "node server.js"
    echo ""
    echo -e "${YELLOW}Per fermarlo: kill \$(pgrep -f 'node server.js')${NC}"
    exit 0
fi

echo -e "${GREEN}✅ Nessun processo in esecuzione${NC}"
echo ""

################################################################################
# STEP 3: Install dependencies if needed
################################################################################

echo -e "${BLUE}[3/4]${NC} Verifico dipendenze npm..."

if [ ! -d "node_modules" ]; then
    echo -e "${YELLOW}⚠️  node_modules mancante, installo...${NC}"
    npm install
    echo -e "${GREEN}✅ Dipendenze installate${NC}"
else
    echo -e "${GREEN}✅ Dipendenze già presenti${NC}"
fi

echo ""

################################################################################
# STEP 4: Start microservice in background
################################################################################

echo -e "${BLUE}[4/4]${NC} Avvio microservice..."

# Start in background
nohup node server.js > "$LOG_FILE" 2>&1 &
PID=$!

# Wait 3 seconds for startup
sleep 3

# Check if still running
if ps -p $PID > /dev/null; then
    echo -e "${GREEN}✅ Microservice avviato con successo!${NC}"
    echo ""
    echo -e "${BLUE}📋 INFO:${NC}"
    echo -e "   PID: $PID"
    echo -e "   Log: $LOG_FILE"
    echo -e "   Port: 3000"
    echo ""
    
    # Test health endpoint
    echo -e "${BLUE}[TEST]${NC} Verifico health endpoint..."
    sleep 2
    
    if curl -s http://localhost:3000/health > /dev/null; then
        echo -e "${GREEN}✅ Health check OK!${NC}"
        echo ""
        echo -e "${GREEN}════════════════════════════════════════════════════════════════${NC}"
        echo -e "${GREEN}  ✅ MICROSERVICE ATTIVO E FUNZIONANTE${NC}"
        echo -e "${GREEN}════════════════════════════════════════════════════════════════${NC}"
    else
        echo -e "${RED}❌ Health check fallito${NC}"
        echo -e "${YELLOW}Controlla il log: cat $LOG_FILE${NC}"
    fi
else
    echo -e "${RED}❌ Microservice crashato all'avvio${NC}"
    echo ""
    echo -e "${BLUE}Ultimi log:${NC}"
    tail -20 "$LOG_FILE"
    exit 1
fi

echo ""
echo -e "${BLUE}🛠️  COMANDI UTILI:${NC}"
echo -e "   ${YELLOW}ps aux | grep 'node server.js'${NC}     - Verifica processo"
echo -e "   ${YELLOW}kill $PID${NC}                          - Ferma microservice"
echo -e "   ${YELLOW}cat $LOG_FILE${NC}                      - Vedi log completo"
echo -e "   ${YELLOW}tail -f $LOG_FILE${NC}                  - Segui log in tempo reale"
echo -e "   ${YELLOW}curl http://localhost:3000/health${NC}  - Test health"
echo ""

