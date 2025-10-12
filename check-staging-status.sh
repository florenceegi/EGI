#!/bin/bash

################################################################################
# STAGING STATUS CHECK - ALGORAND CONFIGURATION
################################################################################
# Purpose: Verify Algorand configuration status on staging
# Usage: bash check-staging-status.sh
################################################################################

set -e

echo "════════════════════════════════════════════════════════════════"
echo "  🔍 STAGING STATUS CHECK - ALGORAND CONFIGURATION"
echo "════════════════════════════════════════════════════════════════"
echo ""

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m'

APP_DIR="/home/forge/app.13.48.57.194.sslip.io"
MICROSERVICE_DIR="$APP_DIR/algokit-microservice"

# Status counters
PASSED=0
FAILED=0
WARNINGS=0

################################################################################
# CHECK 1: LARAVEL .ENV
################################################################################

echo -e "${BLUE}[1/10]${NC} Checking Laravel .env configuration..."

cd "$APP_DIR"

if grep -q "ALGORAND_API_URL=https://testnet-api.algonode.cloud" .env; then
    echo -e "${GREEN}✅ ALGORAND_API_URL configured${NC}"
    ((PASSED++))
else
    echo -e "${RED}❌ ALGORAND_API_URL missing or incorrect${NC}"
    ((FAILED++))
fi

if grep -q "ALGORAND_INDEXER_URL=https://testnet-idx.algonode.cloud" .env; then
    echo -e "${GREEN}✅ ALGORAND_INDEXER_URL configured${NC}"
    ((PASSED++))
else
    echo -e "${RED}❌ ALGORAND_INDEXER_URL missing or incorrect${NC}"
    ((FAILED++))
fi

if grep -q "ALGORAND_NETWORK=testnet" .env; then
    echo -e "${GREEN}✅ ALGORAND_NETWORK=testnet${NC}"
    ((PASSED++))
else
    echo -e "${YELLOW}⚠️  ALGORAND_NETWORK not set to testnet${NC}"
    ((WARNINGS++))
fi

echo ""

################################################################################
# CHECK 2: LARAVEL CONFIG CACHE
################################################################################

echo -e "${BLUE}[2/10]${NC} Checking Laravel config cache..."

API_URL=$(php artisan tinker --execute="echo config('algorand.algorand.api_url');" 2>/dev/null | tail -1)
if [[ "$API_URL" == *"testnet-api.algonode.cloud"* ]]; then
    echo -e "${GREEN}✅ Laravel config loaded: $API_URL${NC}"
    ((PASSED++))
else
    echo -e "${RED}❌ Laravel config not loaded correctly${NC}"
    echo -e "${YELLOW}   Run: php artisan config:clear && config:cache${NC}"
    ((FAILED++))
fi

echo ""

################################################################################
# CHECK 3: MICROSERVICE .ENV
################################################################################

echo -e "${BLUE}[3/10]${NC} Checking microservice .env..."

if [ -f "$MICROSERVICE_DIR/.env" ]; then
    echo -e "${GREEN}✅ Microservice .env exists${NC}"
    ((PASSED++))
    
    if grep -q "ALGORAND_API_URL" "$MICROSERVICE_DIR/.env"; then
        echo -e "${GREEN}✅ Microservice API URL configured${NC}"
        ((PASSED++))
    else
        echo -e "${RED}❌ Microservice API URL missing${NC}"
        ((FAILED++))
    fi
else
    echo -e "${RED}❌ Microservice .env not found${NC}"
    ((FAILED++))
fi

echo ""

################################################################################
# CHECK 4: MICROSERVICE PROCESS
################################################################################

echo -e "${BLUE}[4/10]${NC} Checking microservice process..."

if pgrep -f "node server.js" > /dev/null; then
    PID=$(pgrep -f "node server.js")
    echo -e "${GREEN}✅ Microservice running (PID: $PID)${NC}"
    ((PASSED++))
else
    echo -e "${RED}❌ Microservice NOT running${NC}"
    echo -e "${YELLOW}   Start with: cd $MICROSERVICE_DIR && nohup npm start > ~/logs/algokit-microservice.log 2>&1 &${NC}"
    ((FAILED++))
fi

echo ""

################################################################################
# CHECK 5: MICROSERVICE HEALTH
################################################################################

echo -e "${BLUE}[5/10]${NC} Checking microservice health endpoint..."

HEALTH_STATUS=$(curl -s http://localhost:3000/health | jq -r '.status' 2>/dev/null || echo "error")
if [ "$HEALTH_STATUS" = "healthy" ]; then
    NETWORK=$(curl -s http://localhost:3000/health | jq -r '.network' 2>/dev/null)
    BALANCE=$(curl -s http://localhost:3000/health | jq -r '.treasury.balance' 2>/dev/null)
    echo -e "${GREEN}✅ Microservice healthy${NC}"
    echo -e "${GREEN}   Network: $NETWORK${NC}"
    echo -e "${GREEN}   Treasury balance: $BALANCE ALGO${NC}"
    ((PASSED++))
else
    echo -e "${RED}❌ Microservice unhealthy or not responding${NC}"
    echo -e "${YELLOW}   Check logs: tail -50 ~/logs/algokit-microservice.log${NC}"
    ((FAILED++))
fi

echo ""

################################################################################
# CHECK 6: QUEUE WORKERS
################################################################################

echo -e "${BLUE}[6/10]${NC} Checking queue workers..."

if pgrep -f "queue:work" > /dev/null; then
    WORKER_COUNT=$(pgrep -f "queue:work" | wc -l)
    echo -e "${GREEN}✅ Queue workers running ($WORKER_COUNT workers)${NC}"
    ((PASSED++))
else
    echo -e "${YELLOW}⚠️  No queue workers running${NC}"
    echo -e "${YELLOW}   Check Forge queue configuration${NC}"
    ((WARNINGS++))
fi

echo ""

################################################################################
# CHECK 7: DATABASE CONNECTION
################################################################################

echo -e "${BLUE}[7/10]${NC} Checking database connection..."

DB_CHECK=$(php artisan tinker --execute="try { \DB::connection()->getPdo(); echo 'OK'; } catch (\Exception \$e) { echo 'FAIL'; }" 2>/dev/null | tail -1)
if [ "$DB_CHECK" = "OK" ]; then
    echo -e "${GREEN}✅ Database connection OK${NC}"
    ((PASSED++))
else
    echo -e "${RED}❌ Database connection failed${NC}"
    ((FAILED++))
fi

echo ""

################################################################################
# CHECK 8: EGI_BLOCKCHAIN TABLE
################################################################################

echo -e "${BLUE}[8/10]${NC} Checking egi_blockchain table..."

MINT_COUNT=$(php artisan tinker --execute="echo \App\Models\EgiBlockchain::where('mint_status', 'minted')->count();" 2>/dev/null | tail -1)
if [[ "$MINT_COUNT" =~ ^[0-9]+$ ]]; then
    echo -e "${GREEN}✅ egi_blockchain table accessible${NC}"
    echo -e "${GREEN}   Total minted EGIs: $MINT_COUNT${NC}"
    ((PASSED++))
else
    echo -e "${RED}❌ egi_blockchain table query failed${NC}"
    ((FAILED++))
fi

echo ""

################################################################################
# CHECK 9: LOGS
################################################################################

echo -e "${BLUE}[9/10]${NC} Checking recent errors in logs..."

RECENT_ERRORS=$(tail -100 storage/logs/laravel.log | grep -c "ERROR" || echo "0")
if [ "$RECENT_ERRORS" -eq 0 ]; then
    echo -e "${GREEN}✅ No recent errors in laravel.log${NC}"
    ((PASSED++))
else
    echo -e "${YELLOW}⚠️  Found $RECENT_ERRORS errors in last 100 lines${NC}"
    echo -e "${YELLOW}   Check: tail -50 storage/logs/laravel.log${NC}"
    ((WARNINGS++))
fi

echo ""

################################################################################
# CHECK 10: FORGE WORKER CONFIGURATION
################################################################################

echo -e "${BLUE}[10/10]${NC} Reminders for manual Forge configuration..."

echo -e "${YELLOW}⚠️  MANUAL CHECK REQUIRED in Forge UI:${NC}"
echo -e "${YELLOW}   Worker 'default':${NC}"
echo -e "${YELLOW}   - Connection: database (NOT redis)${NC}"
echo -e "${YELLOW}   - Queue: default${NC}"
echo -e "${YELLOW}   - Timeout: 60${NC}"

echo ""

################################################################################
# SUMMARY
################################################################################

TOTAL=$((PASSED + FAILED + WARNINGS))

echo "════════════════════════════════════════════════════════════════"
echo -e "  📊 STATUS SUMMARY"
echo "════════════════════════════════════════════════════════════════"
echo ""
echo -e "  ${GREEN}✅ Passed: $PASSED${NC}"
echo -e "  ${RED}❌ Failed: $FAILED${NC}"
echo -e "  ${YELLOW}⚠️  Warnings: $WARNINGS${NC}"
echo ""

if [ $FAILED -eq 0 ]; then
    echo -e "${GREEN}🎉 ALL CRITICAL CHECKS PASSED!${NC}"
    echo ""
    echo "✅ System ready for testing"
    echo ""
    echo "Next steps:"
    echo "  1. Test mint: https://app.13.48.57.194.sslip.io/egis/{id}/mint"
    echo "  2. Monitor logs: tail -f storage/logs/laravel.log"
    echo "  3. Check TestNet explorer after mint"
    echo ""
    exit 0
else
    echo -e "${RED}⚠️  CRITICAL ISSUES FOUND ($FAILED)${NC}"
    echo ""
    echo "❌ Fix required before testing"
    echo ""
    echo "Actions:"
    echo "  1. Review failed checks above"
    echo "  2. Run deployment script if needed"
    echo "  3. Check logs for errors"
    echo "  4. Re-run this status check"
    echo ""
    exit 1
fi
