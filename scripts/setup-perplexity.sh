#!/bin/bash
# 🔍 Perplexity AI - Quick Setup Script
# Questo script ti guida nella configurazione rapida di Perplexity

set -e

echo "╔════════════════════════════════════════════════════════════╗"
echo "║     🌐 Perplexity AI - Quick Setup for N.A.T.A.N.        ║"
echo "╚════════════════════════════════════════════════════════════╝"
echo ""

# Check if .env exists
if [ ! -f .env ]; then
    echo "❌ Errore: File .env non trovato!"
    exit 1
fi

echo "📋 Step 1: Ottieni API Key da Perplexity"
echo "   → Vai su: https://www.perplexity.ai/settings/api"
echo "   → Crea account e acquista crediti (minimo €10)"
echo "   → Genera API Key (formato: pplx-xxxxxxxxxxxxx)"
echo ""
read -p "✅ Hai ottenuto la API Key? (y/n): " has_key

if [ "$has_key" != "y" ]; then
    echo "⏸️  Setup interrotto. Ottieni prima la API Key."
    exit 0
fi

echo ""
read -p "🔑 Incolla la tua API Key: " api_key

if [ -z "$api_key" ]; then
    echo "❌ API Key vuota! Riprova."
    exit 1
fi

echo ""
echo "⚙️  Step 2: Configurazione .env"

# Check if already configured
if grep -q "PERPLEXITY_API_KEY=" .env; then
    echo "⚠️  Configurazione esistente trovata. Sovrascrivo..."
    
    # Update existing
    sed -i "s/PERPLEXITY_API_KEY=.*/PERPLEXITY_API_KEY=$api_key/" .env
    
    # Ensure other settings exist
    if ! grep -q "WEB_SEARCH_ENABLED=" .env; then
        echo "WEB_SEARCH_ENABLED=true" >> .env
    fi
    if ! grep -q "WEB_SEARCH_PROVIDER=" .env; then
        echo "WEB_SEARCH_PROVIDER=perplexity" >> .env
    fi
else
    echo "📝 Aggiungo configurazione al .env..."
    cat >> .env << EOF

# ===================================
# WEB SEARCH / PERPLEXITY AI
# ===================================
WEB_SEARCH_ENABLED=true
WEB_SEARCH_PROVIDER=perplexity
WEB_SEARCH_MAX_RESULTS=5
WEB_SEARCH_TIMEOUT=15
WEB_SEARCH_CACHE_TTL=3600

PERPLEXITY_API_KEY=$api_key
PERPLEXITY_BASE_URL=https://api.perplexity.ai
PERPLEXITY_MODEL=llama-3.1-sonar-large-128k-online
PERPLEXITY_TIMEOUT=30
EOF
fi

echo "✅ Configurazione salvata!"
echo ""

echo "🔄 Step 3: Applicazione configurazione"
php artisan config:clear
php artisan config:cache
echo "✅ Cache aggiornata!"
echo ""

echo "🧪 Step 4: Test connessione"
echo "Eseguo test di connessione a Perplexity..."
echo ""

# Create test script
cat > /tmp/perplexity_test.php << 'PHPCODE'
<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    $service = app(\App\Services\WebSearchService::class);
    echo "🔍 Testing search: 'PNRR digitalizzazione PA'\n";
    
    $results = $service->search('PNRR digitalizzazione PA 2024');
    
    if (!empty($results['results'])) {
        echo "✅ SUCCESS! Perplexity funziona correttamente!\n\n";
        echo "📊 Risultati trovati: " . count($results['results']) . "\n";
        echo "📄 Primo risultato:\n";
        echo "   Titolo: " . ($results['results'][0]['title'] ?? 'N/A') . "\n";
        echo "   URL: " . ($results['results'][0]['url'] ?? 'N/A') . "\n";
        echo "\n";
        exit(0);
    } else {
        echo "⚠️  Nessun risultato trovato (ma connessione OK)\n";
        exit(0);
    }
} catch (\Exception $e) {
    echo "❌ ERRORE: " . $e->getMessage() . "\n";
    echo "\n💡 Verifica:\n";
    echo "   1. API Key corretta nel .env\n";
    echo "   2. Crediti disponibili su Perplexity\n";
    echo "   3. Connessione internet attiva\n";
    exit(1);
}
PHPCODE

php /tmp/perplexity_test.php
test_result=$?
rm /tmp/perplexity_test.php

echo ""
if [ $test_result -eq 0 ]; then
    echo "╔════════════════════════════════════════════════════════════╗"
    echo "║                 ✅ SETUP COMPLETATO!                       ║"
    echo "╚════════════════════════════════════════════════════════════╝"
    echo ""
    echo "🚀 Prossimi passi:"
    echo "   1. Apri N.A.T.A.N. chat: /pa/natan/chat"
    echo "   2. Attiva toggle 'Web Search' (blu in alto)"
    echo "   3. Chiedi: 'Quali sono i nuovi bandi PNRR per la PA?'"
    echo "   4. Verifica box 'Fonti Web' nella risposta"
    echo ""
    echo "📚 Documentazione completa: docs/perplexity-setup.md"
    echo "💰 Dashboard API: https://www.perplexity.ai/settings/api"
    echo ""
else
    echo "╔════════════════════════════════════════════════════════════╗"
    echo "║              ⚠️  SETUP PARZIALMENTE COMPLETATO            ║"
    echo "╚════════════════════════════════════════════════════════════╝"
    echo ""
    echo "⚠️  Configurazione salvata ma test fallito."
    echo "📚 Consulta troubleshooting: docs/perplexity-setup.md"
    echo ""
fi
