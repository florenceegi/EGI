#!/bin/bash

################################################################################
# STOP ALGOKIT MICROSERVICE ON LOCAL
################################################################################
# Purpose: Ferma il microservice Algorand in esecuzione
# Usage: bash stop-algokit-local.sh
################################################################################

echo "🛑 Stopping AlgoKit Microservice..."

# Kill process by name
if pkill -f "node server.js"; then
    echo "✅ Process killed successfully"
else
    echo "⚠️  No process found (was it running?)"
fi

# Double check port 3000
sleep 1

if lsof -Pi :3000 -sTCP:LISTEN -t >/dev/null 2>&1; then
    echo "⚠️  Port 3000 still occupied, forcing kill..."
    lsof -ti:3000 | xargs kill -9 2>/dev/null || true
    echo "✅ Port 3000 freed"
else
    echo "✅ Port 3000 is free"
fi

echo ""
echo "📋 Microservice stopped successfully"

