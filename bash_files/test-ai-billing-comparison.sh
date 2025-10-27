#!/bin/bash

###############################################################################
# AI BILLING COMPARISON TEST SCRIPT
#
# Testa il nuovo servizio di confronto billing tra:
# - Tracking interno (database)
# - API reali Anthropic/OpenAI
#
# FEATURES:
# - Fetch billing da OpenAI API
# - Confronta con tracking interno
# - Identifica discrepanze
# - Alert se differenza > 5%
#
# USAGE:
#   ./bash_files/test-ai-billing-comparison.sh
#
# @author Padmin D. Curtis (AI Partner OS3.0)
# @version 1.0.0 (FlorenceEGI - AI Cost Monitor)
# @date 2025-10-27
###############################################################################

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "🧪 AI BILLING COMPARISON TEST"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

# Colori
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Step 1: Check .env configuration
echo -e "${BLUE}[1/5] Checking OpenAI API key...${NC}"
if grep -q "OPENAI_API_KEY=" .env && [ -n "$(grep OPENAI_API_KEY= .env | cut -d'=' -f2)" ]; then
    echo -e "${GREEN}✓ OpenAI API key configured${NC}"
else
    echo -e "${RED}✗ OpenAI API key NOT configured in .env${NC}"
    echo "Add: OPENAI_API_KEY=sk-..."
    exit 1
fi
echo ""

# Step 2: Check service exists
echo -e "${BLUE}[2/5] Checking AiProviderBillingService...${NC}"
if [ -f "app/Services/AI/AiProviderBillingService.php" ]; then
    echo -e "${GREEN}✓ Service file exists${NC}"
else
    echo -e "${RED}✗ Service NOT found${NC}"
    exit 1
fi
echo ""

# Step 3: Test OpenAI billing API (via Tinker)
echo -e "${BLUE}[3/5] Testing OpenAI billing API...${NC}"
php artisan tinker --execute="
\$service = app(\App\Services\AI\AiProviderBillingService::class);
\$billing = \$service->getOpenAIBilling();

if (\$billing['success'] ?? false) {
    echo \"✓ OpenAI billing API working\n\";
    echo \"  Total cost: $\" . (\$billing['total_cost'] ?? 0) . \"\n\";
    echo \"  Total requests: \" . (\$billing['total_requests'] ?? 0) . \"\n\";
} else {
    echo \"✗ OpenAI billing API failed\n\";
    echo \"  Error: \" . (\$billing['error'] ?? 'Unknown') . \"\n\";
}
" 2>&1 | grep -v "Psy Shell" | grep -v ">>>"
echo ""

# Step 4: Compare OpenAI billing with internal
echo -e "${BLUE}[4/5] Comparing OpenAI billing with internal tracking...${NC}"
php artisan tinker --execute="
\$service = app(\App\Services\AI\AiProviderBillingService::class);
\$comparison = \$service->compareBilling('openai');

if (\$comparison['success'] ?? false) {
    echo \"✓ Comparison successful\n\";
    echo \"  Internal cost: $\" . (\$comparison['internal']['cost'] ?? 0) . \"\n\";
    echo \"  OpenAI API cost: $\" . (\$comparison['provider_api']['cost'] ?? 0) . \"\n\";
    echo \"  Discrepancy: \" . (\$comparison['comparison']['discrepancy_percentage'] ?? 0) . \"%\n\";
    echo \"  Status: \" . (\$comparison['comparison']['status'] ?? 'UNKNOWN') . \"\n\";

    if ((\$comparison['comparison']['status'] ?? '') === 'WARNING') {
        echo \"  ⚠️  WARNING: Discrepancy > 5%\n\";
        echo \"  Message: \" . (\$comparison['comparison']['message'] ?? '') . \"\n\";
    }
} else {
    echo \"✗ Comparison failed\n\";
    echo \"  Error: \" . (\$comparison['error'] ?? 'Unknown') . \"\n\";
}
" 2>&1 | grep -v "Psy Shell" | grep -v ">>>"
echo ""

# Step 5: Test all providers comparison
echo -e "${BLUE}[5/5] Testing all providers comparison...${NC}"
php artisan tinker --execute="
\$service = app(\App\Services\AI\AiProviderBillingService::class);
\$comparison = \$service->getAllBillingComparison();

if (\$comparison['success'] ?? false) {
    echo \"✓ All providers comparison successful\n\";
    echo \"  Providers checked: \" . (\$comparison['summary']['total_providers_checked'] ?? 0) . \"\n\";
    echo \"  Providers with alerts: \" . (\$comparison['summary']['providers_with_alerts'] ?? 0) . \"\n\";

    foreach (\$comparison['providers'] ?? [] as \$provider => \$result) {
        if (\$result['success'] ?? false) {
            echo \"  - \" . ucfirst(\$provider) . \": \" . (\$result['comparison']['status'] ?? 'UNKNOWN') . \"\n\";
        } else {
            echo \"  - \" . ucfirst(\$provider) . \": NOT AVAILABLE\n\";
        }
    }
} else {
    echo \"✗ All providers comparison failed\n\";
}
" 2>&1 | grep -v "Psy Shell" | grep -v ">>>"
echo ""

# Summary
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo -e "${GREEN}✓ AI BILLING COMPARISON TEST COMPLETED${NC}"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "📊 NEXT STEPS:"
echo "1. Visit: http://localhost/pa/ai-costs"
echo "2. Click 'Compare with Provider API' button"
echo "3. Check discrepancies (if any)"
echo ""
echo "⚠️  NOTE:"
echo "- Anthropic does NOT provide billing API yet"
echo "- Check manually: https://console.anthropic.com/settings/billing"
echo "- OpenAI billing API may have delays (up to 24h)"
echo ""
