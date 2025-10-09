#!/bin/bash

# ========================================
# 🚀 FLORENCE EGI - SANDBOX STARTUP SCRIPT
# ========================================
# Avvia tutti i servizi necessari per sviluppo/testing
#
# @author Padmin D. Curtis (AI Partner OS3.0)
# @version 1.0.0
# @date 2025-10-07
# ========================================

echo "🚀 Starting Florence EGI Sandbox Services..."
echo "=============================================="

# Vai nella directory del progetto
cd "$(dirname "$0")"

# Verifica che siamo nella directory corretta
if [ ! -f "artisan" ]; then
    echo "❌ Error: artisan file not found. Make sure you're in the Laravel project root."
    exit 1
fi

echo ""
echo "📋 1. VERIFYING ENVIRONMENT..."
echo "------------------------------"

# Verifica .env file
if [ ! -f ".env" ]; then
    echo "❌ .env file not found. Copying from .env.example..."
    cp .env.example .env
    echo "⚠️  Please configure your .env file before continuing."
    exit 1
fi

# Verifica APP_KEY
if ! grep -q "APP_KEY=base64:" .env; then
    echo "🔑 Generating APP_KEY..."
    php artisan key:generate
fi

echo "✅ Environment verified"

echo ""
echo "🐳 2. STARTING DOCKER SERVICES..."
echo "---------------------------------"

# Avvia servizi Docker (database, redis, app)
if command -v docker-compose &> /dev/null; then
    echo "Starting Docker Compose services..."
    docker-compose up -d

    # Attendi che i servizi siano pronti
    echo "⏳ Waiting for services to be ready..."
    sleep 10

    # Verifica stato servizi
    docker-compose ps
    echo "✅ Docker services started"
else
    echo "⚠️  Docker Compose not found. Skipping Docker services."
fi

echo ""
echo "🗄️ 3. DATABASE SETUP..."
echo "----------------------"

# Esegui migrazioni se necessario
echo "Running database migrations..."
php artisan migrate --force

echo "✅ Database ready"

echo ""
echo "⛓️  4. STARTING ALGOKIT MICROSERVICE..."
echo "--------------------------------------"

# Verifica se il microservice è già in esecuzione
if pgrep -f "algokit-microservice/server.js" > /dev/null; then
    echo "⚠️  AlgoKit microservice already running. Killing existing process..."
    pkill -f "algokit-microservice/server.js"
    sleep 2
fi

# Verifica se node_modules esistono
if [ ! -d "algokit-microservice/node_modules" ]; then
    echo "📦 Installing AlgoKit microservice dependencies..."
    cd algokit-microservice
    npm install
    cd ..
fi

# Avvia AlgoKit microservice in background
echo "Starting AlgoKit microservice on port 3000..."
cd algokit-microservice
nohup npm start > ../storage/logs/algokit-microservice.log 2>&1 &
MICROSERVICE_PID=$!
cd ..

# Attendi che il microservice sia pronto
echo "⏳ Waiting for microservice to be ready..."
for i in {1..10}; do
    if curl -s http://localhost:3000/health > /dev/null 2>&1; then
        echo "✅ AlgoKit microservice started (PID: $MICROSERVICE_PID)"
        break
    fi
    if [ $i -eq 10 ]; then
        echo "⚠️  Warning: AlgoKit microservice may not be ready"
    fi
    sleep 1
done

echo ""
echo "⚡ 5. STARTING QUEUE WORKER..."
echo "-----------------------------"

# Verifica se queue worker è già in esecuzione
if pgrep -f "artisan queue" > /dev/null; then
    echo "⚠️  Queue worker already running. Killing existing process..."
    pkill -f "artisan queue"
    sleep 2
fi

# Avvia queue worker in background
echo "Starting queue worker for blockchain operations..."
nohup php artisan queue:work --queue=blockchain,default --tries=3 --timeout=300 --sleep=3 --max-jobs=1000 --max-time=3600 > storage/logs/queue-worker.log 2>&1 &

QUEUE_PID=$!
echo "✅ Queue worker started (PID: $QUEUE_PID)"

echo ""
echo "🌐 6. STARTING DEVELOPMENT SERVER..."
echo "----------------------------------"

# Se non usa Docker, avvia server di sviluppo
if ! docker-compose ps | grep -q "app.*Up"; then
    echo "Docker not running, starting development server..."
    echo "🌍 Server will be available at: http://localhost:8000"
    echo "⛓️  AlgoKit Microservice PID: $MICROSERVICE_PID"
    echo "📋 Queue worker PID: $QUEUE_PID"
    echo ""
    echo "🛑 To stop services:"
    echo "   - Press Ctrl+C to stop development server"
    echo "   - Kill AlgoKit microservice: kill $MICROSERVICE_PID"
    echo "   - Kill queue worker: kill $QUEUE_PID"
    echo "   - Stop Docker: docker-compose down"
    echo ""

    # Avvia server in foreground
    php artisan serve --host=0.0.0.0 --port=8000
else
    echo "✅ Docker app container is running"
    echo ""
    echo "🎉 ALL SERVICES STARTED!"
    echo "======================="
    echo "🌍 Application: http://localhost:8000"
    echo "📊 Database: MariaDB via Docker"
    echo "⛓️  AlgoKit Microservice: http://localhost:3000 (PID: $MICROSERVICE_PID)"
    echo "🔄 Queue Worker: Running (PID: $QUEUE_PID)"
    echo "⛓️  Blockchain Queue: blockchain,default"
    echo ""
    echo "📋 MONITORING:"
    echo "   - Queue status: php artisan queue:monitor"
    echo "   - Queue failed: php artisan queue:failed"
    echo "   - Queue restart: php artisan queue:restart"
    echo "   - Microservice health: curl http://localhost:3000/health"
    echo "   - Microservice logs: tail -f storage/logs/algokit-microservice.log"
    echo ""
    echo "🛑 TO STOP:"
    echo "   - AlgoKit microservice: kill $MICROSERVICE_PID"
    echo "   - Queue worker: kill $QUEUE_PID"
    echo "   - Docker services: docker-compose down"
fi

echo ""
echo "🔍 HEALTH CHECKS:"
echo "   - AlgoKit microservice: curl http://localhost:3000/health"
echo "   - Webhook health: curl http://localhost:8000/api/webhooks/health"
echo "   - Application: curl http://localhost:8000/health"
echo ""
echo "✅ Sandbox environment ready for development!"
