#!/bin/bash
# N.A.T.A.N. 2.0 - Quick Deploy Script for Staging
# Server: https://app.13.48.57.194.sslip.io/
# Run: bash NATAN_QUICK_DEPLOY.sh

set -e  # Exit on error

echo "🚀 N.A.T.A.N. 2.0 STAGING DEPLOYMENT"
echo "===================================="
echo ""

# Check we're in the right directory
if [ ! -f "artisan" ]; then
    echo "❌ ERROR: Run this script from the Laravel root directory"
    exit 1
fi

echo "📋 STEP 1: Deploy AlgoKit Microservice"
echo "---------------------------------------"

# Navigate to AlgoKit directory
cd algokit-microservice

echo "📦 Installing Node.js dependencies..."
npm install

# Check if .env exists
if [ ! -f ".env" ]; then
    echo "⚠️  Creating .env for AlgoKit..."
    cat > .env << 'EOF'
PORT=3000
ALGORAND_NETWORK=testnet
TREASURY_MNEMONIC=<YOUR_25_WORD_MNEMONIC_HERE>
EOF
    echo "⚠️  IMPORTANT: Edit .env and add your real TREASURY_MNEMONIC!"
    echo "✅ AlgoKit .env created"
else
    echo "✅ AlgoKit .env already exists"
fi

# Check if PM2 is installed
if ! command -v pm2 &> /dev/null; then
    echo "📦 Installing PM2 globally..."
    npm install -g pm2
fi

# Stop existing process if running
pm2 delete algokit-egi 2>/dev/null || true

echo "🔄 Starting AlgoKit with PM2..."
pm2 start server.js --name algokit-egi

# Save PM2 config
pm2 save

echo "✅ AlgoKit Microservice deployed!"
echo ""

# Test AlgoKit health
echo "🧪 Testing AlgoKit health..."
sleep 2
HEALTH_RESPONSE=$(curl -s http://localhost:3000/health || echo "ERROR")

if [[ $HEALTH_RESPONSE == *"status"* ]]; then
    echo "✅ AlgoKit is healthy!"
    echo "   Response: $HEALTH_RESPONSE"
else
    echo "❌ AlgoKit health check failed!"
    echo "   Check logs: pm2 logs algokit-egi"
    exit 1
fi

echo ""
echo "📋 STEP 2: Deploy Laravel App (N.A.T.A.N.)"
echo "------------------------------------------"

# Go back to Laravel root
cd ..

echo "🔄 Clearing Laravel caches..."
php artisan config:clear
php artisan cache:clear

echo ""
echo "⚙️  Environment Configuration Check"
echo "-----------------------------------"

# Check Anthropic API key
if grep -q "ANTHROPIC_API_KEY=" .env; then
    echo "✅ ANTHROPIC_API_KEY found in .env"
else
    echo "⚠️  ANTHROPIC_API_KEY not found in .env"
    echo "   Please add manually:"
    echo "   ANTHROPIC_API_KEY=<YOUR_KEY_HERE>"
    echo "   ANTHROPIC_MODEL=claude-3-5-sonnet-20241022"
    echo "   Get your key from: https://console.anthropic.com/"
fi

# Check AlgoKit URL
if grep -q "ALGOKIT_BASE_URL=" .env; then
    echo "✅ ALGOKIT_BASE_URL found in .env"
else
    echo "⚠️  ALGOKIT_BASE_URL not found in .env"
    echo "   Adding default value..."
    echo "ALGOKIT_BASE_URL=http://localhost:3000" >> .env
fi

echo ""
echo "🧪 Testing Integrations"
echo "-----------------------"

# Test Anthropic
echo "Testing Anthropic Claude..."
php artisan tinker --execute="
try {
    \$anthropic = app(\App\Services\AnthropicService::class);
    echo \$anthropic->isAvailable() ? '✅ Anthropic: CONNECTED' : '❌ Anthropic: FAILED';
} catch (Exception \$e) {
    echo '❌ Anthropic: ERROR - ' . \$e->getMessage();
}
echo PHP_EOL;
"

# Test AlgoKit connectivity
echo "Testing AlgoKit connectivity..."
php artisan tinker --execute="
try {
    \$response = \Illuminate\Support\Facades\Http::get('http://localhost:3000/health');
    echo \$response->successful() ? '✅ AlgoKit: CONNECTED' : '❌ AlgoKit: FAILED';
} catch (Exception \$e) {
    echo '❌ AlgoKit: ERROR - ' . \$e->getMessage();
}
echo PHP_EOL;
"

echo ""
echo "📊 PM2 Status"
echo "-------------"
pm2 status

echo ""
echo "✅ DEPLOYMENT COMPLETE!"
echo "======================"
echo ""
echo "🌐 Access N.A.T.A.N. Chat:"
echo "   https://app.13.48.57.194.sslip.io/pa/natan/chat"
echo ""
echo "📝 Test queries:"
echo "   - Come funziona N.A.T.A.N.?"
echo "   - Quanti atti ho caricato?"
echo "   - Mostrami gli atti certificati su blockchain"
echo ""
echo "📊 Monitoring:"
echo "   pm2 logs algokit-egi   (AlgoKit logs)"
echo "   tail -f storage/logs/laravel.log | grep NATAN   (Laravel logs)"
echo ""
echo "🎉 N.A.T.A.N. 2.0 is ready!"

