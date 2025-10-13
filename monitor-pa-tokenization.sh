#!/bin/bash
# PA Tokenization Real-Time Monitor
# Usage: bash monitor-pa-tokenization.sh

echo "🔍 PA TOKENIZATION MONITOR - Real-Time Logging"
echo "=============================================="
echo ""
echo "📊 Monitoring: storage/logs/laravel.log"
echo "🔍 Filter: [PA-TOKENIZATION]"
echo ""
echo "💡 TIP: In another terminal, go to http://localhost:8004/pa/acts/upload"
echo "        and upload a PDF to see the flow in real-time!"
echo ""
echo "Press Ctrl+C to stop"
echo "=============================================="
echo ""

# Tail log file and filter for PA-TOKENIZATION
tail -f storage/logs/laravel.log | grep --line-buffered --color=always -E "PA-TOKENIZATION|SignatureValidationService|FALLBACK|TokenizePaActJob"

