#!/bin/bash

# ========================================
# 🧠 PADMIN ANALYZER - SETUP SCRIPT
# ========================================
# Script automatico per configurazione Redis Stack
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

# Functions
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

# Banner
echo ""
echo "🧠 =============================================="
echo "   PADMIN ANALYZER - SETUP SCRIPT"
echo "   FlorenceEGI Cognitive Database Setup"
echo "============================================== 🧠"
echo ""

# Step 1: Check Node.js version
log_info "Checking Node.js version..."
if ! command -v node &> /dev/null; then
    log_error "Node.js not found. Please install Node.js >= 18.0.0"
    exit 1
fi

NODE_VERSION=$(node --version | cut -d'v' -f2 | cut -d'.' -f1)
if [ "$NODE_VERSION" -lt 18 ]; then
    log_error "Node.js version $NODE_VERSION.x is too old. Required: >= 18.0.0"
    exit 1
fi

log_success "Node.js $(node --version) detected"

# Step 2: Ask for Redis setup option
echo ""
log_info "Choose Redis Stack setup option:"
echo "  [A] Upgrade existing Redis to Redis Stack (2-3 min downtime)"
echo "  [B] Add separate Redis Stack instance (zero downtime)"
echo ""
read -p "Your choice [A/B]: " REDIS_OPTION

if [[ "$REDIS_OPTION" != "A" && "$REDIS_OPTION" != "B" && "$REDIS_OPTION" != "a" && "$REDIS_OPTION" != "b" ]]; then
    log_error "Invalid option. Exiting."
    exit 1
fi

# Convert to uppercase
REDIS_OPTION=$(echo "$REDIS_OPTION" | tr '[:lower:]' '[:upper:]')

# Step 3: Backup current docker-compose.yml
log_info "Creating backup of docker-compose.yml..."
cp docker-compose.yml docker-compose.yml.backup
log_success "Backup created: docker-compose.yml.backup"

# Step 4: Apply configuration
if [ "$REDIS_OPTION" == "A" ]; then
    log_warning "OPTION A: Upgrading existing Redis to Redis Stack"
    log_warning "This will cause 2-3 minutes downtime for Redis-dependent features"
    read -p "Continue? [y/N]: " CONFIRM

    if [[ "$CONFIRM" != "y" && "$CONFIRM" != "Y" ]]; then
        log_info "Setup cancelled by user"
        rm docker-compose.yml.backup
        exit 0
    fi

    log_info "Stopping current Redis container..."
    docker-compose stop redis

    log_info "Creating Redis backup..."
    docker-compose exec redis redis-cli -a "${REDIS_PASSWORD:-florence_redis_2025}" --rdb /data/backup.rdb 2>/dev/null || true

    log_info "Updating docker-compose.yml..."
    sed -i 's|image: redis:7-alpine|image: redis/redis-stack:latest|g' docker-compose.yml
    sed -i 's|container_name: florence_redis|container_name: florence_redis_stack|g' docker-compose.yml

    # Add RedisInsight port if not exists
    if ! grep -q "8001:8001" docker-compose.yml; then
        log_info "Adding RedisInsight UI port (8001)..."
        # This is complex, manual edit recommended
        log_warning "Please manually add port 8001:8001 to redis service in docker-compose.yml"
    fi

    log_info "Starting Redis Stack..."
    docker-compose up -d redis

    log_info "Waiting for Redis Stack to be ready..."
    sleep 10

    log_info "Verifying Redis Stack..."
    docker-compose exec redis redis-cli -a "${REDIS_PASSWORD:-florence_redis_2025}" ping

    log_success "Redis Stack upgrade completed!"
    log_info "RedisInsight UI available at: http://localhost:8001"

else
    log_info "OPTION B: Adding separate Redis Stack instance"

    log_info "Please manually add this service to docker-compose.yml:"
    echo ""
    echo "# ========================================"
    echo "# 🧠 REDIS STACK for Padmin Analyzer"
    echo "# ========================================"
    echo "redis_padmin:"
    echo "  image: redis/redis-stack:latest"
    echo "  container_name: florence_redis_padmin"
    echo "  restart: unless-stopped"
    echo "  ports:"
    echo "    - \"6381:6379\""
    echo "    - \"8001:8001\""
    echo "  environment:"
    echo "    - REDIS_ARGS=--requirepass padmin_redis_2025 --appendonly yes"
    echo "  volumes:"
    echo "    - redis_padmin_data:/data"
    echo "  networks:"
    echo "    - florence_network"
    echo "  healthcheck:"
    echo "    test: [\"CMD\", \"redis-cli\", \"-a\", \"padmin_redis_2025\", \"ping\"]"
    echo "    interval: 10s"
    echo "    timeout: 3s"
    echo "    retries: 5"
    echo ""
    echo "# Add to volumes section:"
    echo "redis_padmin_data:"
    echo "  driver: local"
    echo ""

    read -p "Press Enter after you've manually edited docker-compose.yml..."

    log_info "Starting Redis Padmin..."
    docker-compose up -d redis_padmin

    log_info "Waiting for Redis Padmin to be ready..."
    sleep 10

    log_info "Verifying Redis Padmin..."
    docker-compose exec redis_padmin redis-cli -a "padmin_redis_2025" ping

    log_success "Redis Padmin instance added!"
    log_info "RedisInsight UI available at: http://localhost:8001"
fi

# Step 5: Setup Padmin Analyzer TypeScript project
echo ""
log_info "Setting up Padmin Analyzer TypeScript project..."

cd tools/os3-guardian

if [ ! -f ".env" ]; then
    log_info "Creating .env file..."
    cp .env.example .env

    if [ "$REDIS_OPTION" == "B" ]; then
        log_info "Configuring for separate Redis instance..."
        sed -i 's|REDIS_HOST=localhost|REDIS_HOST=redis_padmin|g' .env
        sed -i 's|REDIS_PORT=6379|REDIS_PORT=6379|g' .env
        sed -i 's|REDIS_PASSWORD=|REDIS_PASSWORD=padmin_redis_2025|g' .env
    else
        log_info "Configuring for upgraded Redis Stack..."
        sed -i 's|REDIS_HOST=localhost|REDIS_HOST=redis|g' .env
        sed -i 's|REDIS_PASSWORD=|REDIS_PASSWORD=florence_redis_2025|g' .env
    fi

    log_success ".env configured"
fi

log_info "Installing npm dependencies..."
npm install

log_info "Building TypeScript project..."
npm run build

log_success "Padmin Analyzer build completed!"

# Step 6: Test connection
echo ""
log_info "Testing Padmin Analyzer connection..."
node dist/example.js

# Step 7: Final summary
echo ""
echo "🎉 =============================================="
echo "   SETUP COMPLETED SUCCESSFULLY!"
echo "============================================== 🎉"
echo ""
log_success "Redis Stack is running"
log_success "Padmin Analyzer is compiled and tested"
echo ""
log_info "Next steps:"
echo "  1. Access RedisInsight UI: http://localhost:8001"
echo "  2. Run example: cd tools/os3-guardian && node dist/example.js"
echo "  3. Integrate with Laravel (see IMPACT_ANALYSIS.md - Phase 2)"
echo ""
log_info "Documentation:"
echo "  - README: tools/os3-guardian/README.md"
echo "  - Impact Analysis: docs/ai/Padmin analyzer/IMPACT_ANALYSIS.md"
echo "  - Implementation Guide: docs/ai/Padmin analyzer/OS3_PADMIN_ANALYZER_IMPLEMENTATION_GUIDE.md"
echo ""
log_warning "Backup file preserved: docker-compose.yml.backup"
log_info "Remove backup when satisfied: rm docker-compose.yml.backup"
echo ""
