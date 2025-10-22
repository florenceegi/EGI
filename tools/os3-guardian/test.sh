#!/bin/bash

# ========================================
# 🧠 PADMIN ANALYZER - QUICK TEST SCRIPT
# ========================================
# Script automatico NON-INTERATTIVO per test rapido
# @author Padmin D. Curtis (AI Partner OS3.0)
# @version 1.0.0
# @date 2025-10-22
# ========================================

set -e  # Exit on error

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

log_info() {
    echo -e "${BLUE}ℹ️  $1${NC}"
}

log_success() {
    echo -e "${GREEN}✅ $1${NC}"
}

log_warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

log_error() {
    echo -e "${RED}❌ $1${NC}"
}

echo ""
echo "🧠 =============================================="
echo "   PADMIN ANALYZER - QUICK TEST"
echo "   Automated setup and demo execution"
echo "============================================== 🧠"
echo ""

# Change to script directory
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
cd "$SCRIPT_DIR"

log_info "Working directory: $SCRIPT_DIR"

# Step 1: Check Node.js
log_info "Step 1/6: Checking Node.js version..."
if ! command -v node &> /dev/null; then
    log_error "Node.js not found. Install Node.js >= 18.0.0 first."
    exit 1
fi

NODE_VERSION=$(node --version | cut -d'v' -f2 | cut -d'.' -f1)
if [ "$NODE_VERSION" -lt 18 ]; then
    log_error "Node.js $NODE_VERSION.x too old. Required: >= 18.0.0"
    exit 1
fi

log_success "Node.js $(node --version) OK"

# Step 2: Setup .env
log_info "Step 2/6: Configuring .env file..."
if [ ! -f ".env" ]; then
    log_info "Creating .env from .env.example..."
    cp .env.example .env

    # Auto-detect Redis config from parent docker-compose
    if [ -f "../../docker-compose.yml" ]; then
        log_info "Detected docker-compose.yml, using Docker Redis settings..."
        # Use redis service inside Docker network
        cat > .env << EOF
# Redis Configuration (Docker)
REDIS_HOST=redis
REDIS_PORT=6379
REDIS_PASSWORD=florence_redis_2025
REDIS_DB=0

# Padmin Configuration
PADMIN_KEY_PREFIX=padmin:
PADMIN_TTL_SYMBOL=86400
PADMIN_TTL_VIOLATION=604800

# Logging
LOG_LEVEL=info
EOF
        log_success ".env configured for Docker Redis"
    else
        # Keep localhost default
        log_success ".env created (using localhost defaults)"
    fi
else
    log_success ".env already exists"
fi

# Step 3: Install dependencies
log_info "Step 3/6: Installing npm dependencies..."
if [ ! -d "node_modules" ]; then
    npm install --no-audit --no-fund
    log_success "Dependencies installed"
else
    log_success "Dependencies already installed (skip)"
fi

# Step 4: Build TypeScript
log_info "Step 4/6: Building TypeScript project..."
npm run build
log_success "TypeScript compiled successfully"

# Step 5: Check Redis connection
log_info "Step 5/6: Testing Redis connection..."

# Try to detect Redis (Docker or local)
REDIS_AVAILABLE=false

# Try Docker Redis first
if command -v docker-compose &> /dev/null; then
    if docker-compose -f ../../docker-compose.yml ps | grep -q redis; then
        log_info "Found Redis in docker-compose, testing connection..."
        if docker-compose -f ../../docker-compose.yml exec -T redis redis-cli -a "florence_redis_2025" ping 2>/dev/null | grep -q PONG; then
            REDIS_AVAILABLE=true
            log_success "Redis (Docker) is responding"
        fi
    fi
fi

# Fallback: try local Redis
if [ "$REDIS_AVAILABLE" = false ]; then
    if command -v redis-cli &> /dev/null; then
        if redis-cli -h 127.0.0.1 -p 6379 ping 2>/dev/null | grep -q PONG; then
            REDIS_AVAILABLE=true
            log_success "Redis (localhost) is responding"
        fi
    fi
fi

if [ "$REDIS_AVAILABLE" = false ]; then
    log_error "Redis is not running or not accessible!"
    log_info "Start Redis with: docker-compose up -d redis"
    log_info "Or install Redis locally"
    exit 1
fi

# Step 6: Run demo
log_info "Step 6/6: Running Padmin Analyzer demo..."
echo ""
log_info "========== DEMO OUTPUT =========="
echo ""

node dist/example.js

echo ""
log_info "========== END DEMO OUTPUT =========="
echo ""

# Final summary
echo ""
echo "🎉 =============================================="
echo "   TEST COMPLETED SUCCESSFULLY!"
echo "============================================== 🎉"
echo ""

log_success "Padmin Analyzer is working correctly"
echo ""

log_info "Next steps:"
echo "  • Inspect Redis keys: docker-compose exec redis redis-cli -a florence_redis_2025 --scan --pattern 'padmin:*'"
echo "  • View symbol: docker-compose exec redis redis-cli -a florence_redis_2025 hgetall 'symbol:App\\Services\\Gdpr\\ConsentService'"
echo "  • Check violations: docker-compose exec redis redis-cli -a florence_redis_2025 zrevrange violations:recent 0 10"
echo ""

log_info "Documentation:"
echo "  • README: tools/os3-guardian/README.md"
echo "  • Impact Analysis: docs/ai/Padmin analyzer/IMPACT_ANALYSIS.md"
echo ""

log_success "You can now integrate Padmin Analyzer with Laravel (Phase 2)"
echo ""
