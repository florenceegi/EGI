#!/bin/bash

################################################################################
# START ALGOKIT MICROSERVICE ON LOCAL (DEVELOPMENT)
################################################################################
# Purpose: Avvia il microservice Algorand sulla porta 3000 locale
# Usage: bash start-algokit-local.sh
################################################################################

set -e

echo "🚀 Starting AlgoKit Microservice (LOCAL)..."

# Go to microservice directory
cd /home/fabio/EGI/algokit-microservice

# Kill any existing process on port 3000
echo "🔍 Checking for existing process on port 3000..."
if lsof -Pi :3000 -sTCP:LISTEN -t >/dev/null 2>&1; then
    echo "⚠️  Port 3000 occupied, killing existing process..."
    lsof -ti:3000 | xargs kill -9 2>/dev/null || true
    sleep 1
fi

# Install dependencies if needed
if [ ! -d "node_modules" ]; then
    echo "📦 Installing dependencies..."
    npm install
fi

# Check if .env exists
if [ ! -f ".env" ]; then
    echo "⚠️  .env not found, creating from .env.example..."
    if [ -f ".env.example" ]; then
        cp .env.example .env
    else
        echo "❌ ERROR: No .env.example found!"
        echo "   Creating minimal .env..."
        cat > .env << EOF
# Algorand Microservice Configuration
PORT=3000
ALGORAND_NETWORK=sandbox
ALGOD_SERVER=http://localhost:4001
ALGOD_TOKEN=aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa
TREASURY_ADDRESS=
TREASURY_MNEMONIC=
EOF
        echo "✅ Created minimal .env - CONFIGURE IT BEFORE USE!"
    fi
fi

# Start in background
echo "▶️  Starting server on port 3000..."
nohup node server.js > /tmp/algokit-local.log 2>&1 &
PID=$!

echo "✅ Started with PID: $PID"

# Wait and test
sleep 3

echo "🔍 Testing health endpoint..."
if curl -s http://localhost:3000/health > /dev/null 2>&1; then
    echo "✅ Health check OK!"
    echo ""
    echo "📋 STATUS:"
    echo "   PID: $PID"
    echo "   URL: http://localhost:3000"
    echo "   Log: tail -f /tmp/algokit-local.log"
    echo "   Test: curl http://localhost:3000/health"
    echo ""
    echo "🛑 To stop: pkill -f 'node server.js'"
else
    echo "❌ Health check failed!"
    echo ""
    echo "📄 Last 30 lines of log:"
    tail -30 /tmp/algokit-local.log
    echo ""
    echo "💡 Possible issues:"
    echo "   - AlgoD not running (start with: bash start-sandbox.sh)"
    echo "   - Wrong .env configuration"
    echo "   - Port 3000 still occupied"
    exit 1
fi

