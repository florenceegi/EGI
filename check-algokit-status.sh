#!/bin/bash

################################################################################
# CHECK ALGOKIT MICROSERVICE STATUS
################################################################################
# Purpose: Verifica se il microservice Algorand è in esecuzione
# Usage: bash check-algokit-status.sh
################################################################################

echo "🔍 Checking AlgoKit Microservice Status..."
echo ""

# Check process
if pgrep -f "node server.js" > /dev/null; then
    PID=$(pgrep -f "node server.js")
    echo "✅ Process RUNNING"
    echo "   PID: $PID"
    
    # Get process info
    ps -p $PID -o pid,vsz,rss,etime,cmd --no-headers
else
    echo "❌ Process NOT RUNNING"
fi

echo ""

# Check port
if lsof -Pi :3000 -sTCP:LISTEN -t >/dev/null 2>&1; then
    echo "✅ Port 3000 LISTENING"
    lsof -Pi :3000 -sTCP:LISTEN
else
    echo "❌ Port 3000 NOT LISTENING"
fi

echo ""

# Health check
echo "🏥 Health Check..."
if curl -s http://localhost:3000/health > /dev/null 2>&1; then
    echo "✅ Health endpoint OK"
    echo ""
    echo "📊 Response:"
    curl -s http://localhost:3000/health | jq . 2>/dev/null || curl -s http://localhost:3000/health
else
    echo "❌ Health endpoint FAILED (microservice not responding)"
fi

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "📄 Recent logs (last 20 lines):"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
if [ -f /tmp/algokit-local.log ]; then
    tail -20 /tmp/algokit-local.log
else
    echo "⚠️  No log file found at /tmp/algokit-local.log"
fi

echo ""
echo "💡 Commands:"
echo "   Start:  bash start-algokit-local.sh"
echo "   Stop:   bash stop-algokit-local.sh"
echo "   Logs:   tail -f /tmp/algokit-local.log"

