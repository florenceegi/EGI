#!/bin/bash

################################################################################
# START ALGOKIT MICROSERVICE ON STAGING (SIMPLE VERSION)
################################################################################
# Purpose: Just start the damn microservice, nothing fancy
# Usage: bash start-algokit-staging.sh
################################################################################

set -e

echo "🚀 Starting AlgoKit Microservice..."

# Go to microservice directory
cd ~/app.13.48.57.194.sslip.io/algokit-microservice || cd /home/forge/app.13.48.57.194.sslip.io/algokit-microservice

# Kill any existing process
pkill -f "node server.js" || true

# Install dependencies if needed
if [ ! -d "node_modules" ]; then
    echo "📦 Installing dependencies..."
    npm install
fi

# Start in background
echo "▶️  Starting server..."
nohup node server.js > /tmp/algokit-staging.log 2>&1 &
PID=$!

echo "✅ Started with PID: $PID"

# Wait and test
sleep 3

if curl -s http://localhost:3000/health > /dev/null; then
    echo "✅ Health check OK!"
    echo ""
    echo "📋 STATUS:"
    echo "   PID: $PID"
    echo "   Log: tail -f /tmp/algokit-staging.log"
    echo "   Test: curl http://localhost:3000/health"
else
    echo "❌ Health check failed!"
    echo "Log:"
    tail -20 /tmp/algokit-staging.log
    exit 1
fi
