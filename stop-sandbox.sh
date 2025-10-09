#!/bin/bash

# ========================================
# 🛑 FLORENCE EGI - SANDBOX STOP SCRIPT
# ========================================
# Ferma tutti i servizi sandbox in modo pulito
#
# @author Padmin D. Curtis (AI Partner OS3.0)
# @version 1.0.0
# @date 2025-10-07
# ========================================

echo "🛑 Stopping Florence EGI Sandbox Services..."
echo "============================================="

# Vai nella directory del progetto
cd "$(dirname "$0")"

echo ""
echo "⛓️  1. STOPPING ALGOKIT MICROSERVICE..."
echo "--------------------------------------"

# Ferma AlgoKit microservice
if pgrep -f "algokit-microservice/server.js" > /dev/null; then
    echo "Stopping AlgoKit microservice..."
    pkill -f "algokit-microservice/server.js"
    sleep 2

    # Verifica se è stato fermato
    if pgrep -f "algokit-microservice/server.js" > /dev/null; then
        echo "⚠️  Force killing AlgoKit microservice..."
        pkill -9 -f "algokit-microservice/server.js"
    fi

    echo "✅ AlgoKit microservice stopped"
else
    echo "ℹ️  AlgoKit microservice not running"
fi

echo ""
echo "⚡ 2. STOPPING QUEUE WORKERS..."
echo "------------------------------"

# Ferma tutti i queue worker
if pgrep -f "artisan queue" > /dev/null; then
    echo "Stopping queue workers..."
    pkill -f "artisan queue"
    sleep 3

    # Verifica se sono stati fermati
    if pgrep -f "artisan queue" > /dev/null; then
        echo "⚠️  Force killing remaining queue workers..."
        pkill -9 -f "artisan queue"
    fi

    echo "✅ Queue workers stopped"
else
    echo "ℹ️  No queue workers running"
fi

echo ""
echo "🐳 3. STOPPING DOCKER SERVICES..."
echo "---------------------------------"

# Ferma servizi Docker
if command -v docker-compose &> /dev/null; then
    if docker-compose ps | grep -q "Up"; then
        echo "Stopping Docker Compose services..."
        docker-compose down
        echo "✅ Docker services stopped"
    else
        echo "ℹ️  Docker services not running"
    fi
else
    echo "ℹ️  Docker Compose not found"
fi

echo ""
echo "🧹 4. CLEANUP..."
echo "---------------"

# Cleanup processi Laravel serve
if pgrep -f "artisan serve" > /dev/null; then
    echo "Stopping Laravel development server..."
    pkill -f "artisan serve"
    echo "✅ Laravel server stopped"
fi

# Cleanup lock files se esistono
if [ -f "storage/logs/laravel.log.lock" ]; then
    rm -f storage/logs/laravel.log.lock
fi

echo ""
echo "📊 5. FINAL STATUS CHECK..."
echo "-------------------------"

# Verifica che tutto sia fermato
RUNNING_PROCESSES=$(pgrep -f "artisan|php.*serve|algokit-microservice" | wc -l)

if [ "$RUNNING_PROCESSES" -eq 0 ]; then
    echo "✅ All processes stopped"
else
    echo "⚠️  Warning: $RUNNING_PROCESSES processes still running"
    echo "Use 'ps aux | grep -E \"artisan|algokit\"' to check manually"
fi

# Verifica Docker
if command -v docker-compose &> /dev/null; then
    DOCKER_RUNNING=$(docker-compose ps -q | wc -l)
    if [ "$DOCKER_RUNNING" -eq 0 ]; then
        echo "✅ All Docker services stopped"
    else
        echo "⚠️  Warning: $DOCKER_RUNNING Docker services still running"
    fi
fi

echo ""
echo "🎉 SANDBOX SERVICES STOPPED!"
echo "============================"
echo ""
echo "🔄 To restart services:"
echo "   ./start-sandbox.sh"
echo ""
echo "🔍 To check for remaining processes:"
echo "   ps aux | grep -E 'artisan|php.*serve'"
echo "   docker-compose ps"
echo ""
echo "✅ Cleanup completed successfully!"
