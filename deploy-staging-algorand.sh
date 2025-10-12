#!/bin/bash

################################################################################
# FORGE STAGING DEPLOYMENT SCRIPT - ALGORAND CONFIGURATION
################################################################################
# Purpose: Configure Algorand API settings on staging server
# Author: Padmin D. Curtis (AI Partner OS3.0)
# Date: 2025-10-12
# Usage: bash deploy-staging-algorand.sh
################################################################################

set -e  # Exit on any error

echo "════════════════════════════════════════════════════════════════"
echo "  🚀 FORGE STAGING - ALGORAND CONFIGURATION DEPLOYMENT"
echo "════════════════════════════════════════════════════════════════"
echo ""

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
APP_DIR="/home/forge/app.13.48.57.194.sslip.io"
MICROSERVICE_DIR="$APP_DIR/algokit-microservice"
LOG_DIR="/home/forge/logs"
TREASURY_MNEMONIC="urge rotate level slush enjoy kick poem office explain jar credit exercise ensure crash phone cram vibrant settle proud patch disease universe indicate abandon ahead"

################################################################################
# STEP 1: VERIFY ENVIRONMENT
################################################################################

echo -e "${BLUE}[1/8]${NC} Verifico ambiente..."

if [ ! -d "$APP_DIR" ]; then
    echo -e "${RED}❌ Errore: Directory $APP_DIR non trovata${NC}"
    exit 1
fi

if [ ! -f "$APP_DIR/.env" ]; then
    echo -e "${RED}❌ Errore: File .env non trovato in $APP_DIR${NC}"
    exit 1
fi

echo -e "${GREEN}✅ Ambiente verificato${NC}"
echo ""

################################################################################
# STEP 2: BACKUP .ENV
################################################################################

echo -e "${BLUE}[2/8]${NC} Backup configurazione esistente..."

BACKUP_FILE="$APP_DIR/.env.backup.$(date +%Y%m%d_%H%M%S)"
cp "$APP_DIR/.env" "$BACKUP_FILE"
echo -e "${GREEN}✅ Backup creato: $BACKUP_FILE${NC}"
echo ""

################################################################################
# STEP 3: ADD ALGORAND API VARIABLES TO .ENV
################################################################################

echo -e "${BLUE}[3/8]${NC} Aggiungo variabili Algorand API al .env..."

# Check if variables already exist
if grep -q "ALGORAND_API_URL" "$APP_DIR/.env"; then
    echo -e "${YELLOW}⚠️  Variabili Algorand già presenti, aggiorno...${NC}"
    sed -i 's|ALGORAND_API_URL=.*|ALGORAND_API_URL=https://testnet-api.algonode.cloud|g' "$APP_DIR/.env"
    sed -i 's|ALGORAND_INDEXER_URL=.*|ALGORAND_INDEXER_URL=https://testnet-idx.algonode.cloud|g' "$APP_DIR/.env"
else
    echo -e "${YELLOW}➕ Aggiungo nuove variabili...${NC}"
    # Find ALGORAND_NETWORK line and add after it
    sed -i '/ALGORAND_NETWORK=/a\
\
# Algorand API URLs (AlgoNode - FREE)\
ALGORAND_API_URL=https://testnet-api.algonode.cloud\
ALGORAND_INDEXER_URL=https://testnet-idx.algonode.cloud\
ALGORAND_API_KEY=' "$APP_DIR/.env"
fi

echo -e "${GREEN}✅ Variabili Algorand configurate${NC}"
echo ""

################################################################################
# STEP 4: CONFIGURE ALGOKIT MICROSERVICE
################################################################################

echo -e "${BLUE}[4/8]${NC} Configuro AlgoKit Microservice..."

# Create microservice directory if not exists
if [ ! -d "$MICROSERVICE_DIR" ]; then
    echo -e "${YELLOW}⚠️  Directory microservice non trovata, la creo...${NC}"
    mkdir -p "$MICROSERVICE_DIR"
fi

# Create/update microservice .env
cat > "$MICROSERVICE_DIR/.env" << EOF
# AlgoKit Microservice - TestNet Configuration
# Generated: $(date)

PORT=3000
ALGORAND_NETWORK=testnet

# Algorand API URLs (AlgoNode - FREE)
ALGORAND_API_URL=https://testnet-api.algonode.cloud
ALGORAND_INDEXER_URL=https://testnet-idx.algonode.cloud

# Treasury Account Mnemonic (TestNet)
# Address: TF67P6XRLQJWBJSFIZFMTNW574VP5XWZMTH3ONP5JQLKHRWDG5IIZXLG7A
TREASURY_MNEMONIC=$TREASURY_MNEMONIC

LOG_LEVEL=info
EOF

echo -e "${GREEN}✅ Microservice .env creato${NC}"
echo ""

################################################################################
# STEP 5: INSTALL MICROSERVICE DEPENDENCIES
################################################################################

echo -e "${BLUE}[5/8]${NC} Installo dipendenze microservice..."

cd "$MICROSERVICE_DIR"

if [ ! -f "package.json" ]; then
    echo -e "${RED}❌ Errore: package.json non trovato${NC}"
    echo -e "${YELLOW}⚠️  Assicurati che il microservice sia stato committato nel repo${NC}"
    exit 1
fi

# Install dependencies if node_modules doesn't exist or is outdated
if [ ! -d "node_modules" ] || [ "package.json" -nt "node_modules" ]; then
    echo -e "${YELLOW}📦 Installazione dipendenze npm...${NC}"
    npm install --production
    echo -e "${GREEN}✅ Dipendenze installate${NC}"
else
    echo -e "${GREEN}✅ Dipendenze già aggiornate${NC}"
fi

echo ""

################################################################################
# STEP 6: START MICROSERVICE
################################################################################

echo -e "${BLUE}[6/8]${NC} Avvio AlgoKit Microservice..."

# Create logs directory if not exists
mkdir -p "$LOG_DIR"

# Kill existing microservice process if running
if [ -f "/home/forge/algokit-microservice.pid" ]; then
    OLD_PID=$(cat /home/forge/algokit-microservice.pid)
    if ps -p $OLD_PID > /dev/null 2>&1; then
        echo -e "${YELLOW}⏹️  Stopping existing microservice (PID: $OLD_PID)...${NC}"
        kill $OLD_PID
        sleep 2
    fi
fi

# Start microservice in background
echo -e "${YELLOW}▶️  Starting microservice...${NC}"
cd "$MICROSERVICE_DIR"
nohup npm start > "$LOG_DIR/algokit-microservice.log" 2>&1 &
NEW_PID=$!
echo $NEW_PID > /home/forge/algokit-microservice.pid

# Wait for microservice to start
sleep 3

# Verify microservice is running
if ps -p $NEW_PID > /dev/null 2>&1; then
    echo -e "${GREEN}✅ Microservice avviato (PID: $NEW_PID)${NC}"
    
    # Test health endpoint
    if curl -s http://localhost:3000/health > /dev/null; then
        echo -e "${GREEN}✅ Health check passed${NC}"
    else
        echo -e "${YELLOW}⚠️  Health check failed, controlla i log${NC}"
    fi
else
    echo -e "${RED}❌ Errore: Microservice non avviato${NC}"
    echo -e "${YELLOW}📋 Ultimi log:${NC}"
    tail -20 "$LOG_DIR/algokit-microservice.log"
    exit 1
fi

echo ""

################################################################################
# STEP 7: CLEAR LARAVEL CONFIG CACHE
################################################################################

echo -e "${BLUE}[7/8]${NC} Clear Laravel config cache..."

cd "$APP_DIR"

php artisan config:clear
echo -e "${GREEN}✅ Config cache cleared${NC}"

php artisan config:cache
echo -e "${GREEN}✅ Config cache rebuilt${NC}"

php artisan queue:restart
echo -e "${GREEN}✅ Queue workers restarted${NC}"

echo ""

################################################################################
# STEP 8: VERIFY CONFIGURATION
################################################################################

echo -e "${BLUE}[8/8]${NC} Verifico configurazione finale..."

echo -e "${YELLOW}🔍 Testing configuration...${NC}"

# Test Algorand config
API_URL=$(php artisan tinker --execute="echo config('algorand.algorand.api_url');" 2>/dev/null | tail -1)
if [[ "$API_URL" == *"testnet-api.algonode.cloud"* ]]; then
    echo -e "${GREEN}✅ ALGORAND_API_URL: $API_URL${NC}"
else
    echo -e "${RED}❌ ALGORAND_API_URL non configurato correttamente${NC}"
    exit 1
fi

INDEXER_URL=$(php artisan tinker --execute="echo config('algorand.algorand.indexer_url');" 2>/dev/null | tail -1)
if [[ "$INDEXER_URL" == *"testnet-idx.algonode.cloud"* ]]; then
    echo -e "${GREEN}✅ ALGORAND_INDEXER_URL: $INDEXER_URL${NC}"
else
    echo -e "${RED}❌ ALGORAND_INDEXER_URL non configurato correttamente${NC}"
    exit 1
fi

# Test microservice
MICROSERVICE_HEALTH=$(curl -s http://localhost:3000/health | jq -r '.status' 2>/dev/null || echo "error")
if [ "$MICROSERVICE_HEALTH" = "healthy" ]; then
    echo -e "${GREEN}✅ Microservice: healthy${NC}"
else
    echo -e "${YELLOW}⚠️  Microservice health: $MICROSERVICE_HEALTH${NC}"
fi

echo ""

################################################################################
# SUMMARY
################################################################################

echo "════════════════════════════════════════════════════════════════"
echo -e "${GREEN}  ✅ DEPLOYMENT COMPLETATO CON SUCCESSO${NC}"
echo "════════════════════════════════════════════════════════════════"
echo ""
echo "📋 Summary:"
echo "  • Backup .env: $BACKUP_FILE"
echo "  • Algorand API: https://testnet-api.algonode.cloud"
echo "  • Indexer API: https://testnet-idx.algonode.cloud"
echo "  • Microservice PID: $NEW_PID"
echo "  • Microservice logs: $LOG_DIR/algokit-microservice.log"
echo ""
echo "🔍 Verifica manualmente:"
echo "  1. Test mint: https://app.13.48.57.194.sslip.io/egis/{id}/mint"
echo "  2. Worker status: php artisan queue:monitor"
echo "  3. Microservice logs: tail -f $LOG_DIR/algokit-microservice.log"
echo ""
echo "⚙️  Forge Worker Configuration (DA FARE MANUALMENTE):"
echo "  • Worker name: default"
echo "  • Connection: database (NON redis)"
echo "  • Queue: default"
echo "  • Timeout: 60"
echo "  • Sleep: 3"
echo "  • Tries: 3"
echo ""
echo "🌐 TestNet Explorer:"
echo "  https://testnet.algoexplorer.io/"
echo ""
echo "════════════════════════════════════════════════════════════════"
