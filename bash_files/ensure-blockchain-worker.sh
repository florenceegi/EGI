#!/bin/bash

###############################################################################
# BLOCKCHAIN QUEUE WORKER MONITOR
# 🎯 Purpose: Ensure blockchain queue worker is always running
# 📋 Usage: ./ensure-blockchain-worker.sh [start|stop|restart|status]
# 🔄 Can be added to crontab: */5 * * * * /path/to/ensure-blockchain-worker.sh
###############################################################################

WORKER_COMMAND="php artisan queue:work redis --queue=blockchain --tries=3 --timeout=300 --sleep=3 --max-jobs=100"
LOG_FILE="storage/logs/queue-blockchain.log"
PID_FILE="storage/app/queue-blockchain.pid"
PROJECT_DIR="/home/fabio/EGI"

cd "$PROJECT_DIR" || exit 1

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Check if worker is running
is_running() {
    if pgrep -f "queue:work.*blockchain" > /dev/null; then
        return 0 # Running
    else
        return 1 # Not running
    fi
}

# Start worker
start_worker() {
    if is_running; then
        echo -e "${YELLOW}⚠️  Blockchain worker already running${NC}"
        return 1
    fi

    echo -e "${BLUE}🚀 Starting blockchain queue worker...${NC}"
    nohup $WORKER_COMMAND > "$LOG_FILE" 2>&1 &
    WORKER_PID=$!
    echo $WORKER_PID > "$PID_FILE"
    sleep 2

    if is_running; then
        echo -e "${GREEN}✅ Blockchain worker started successfully (PID: $WORKER_PID)${NC}"
        return 0
    else
        echo -e "${RED}❌ Failed to start blockchain worker${NC}"
        return 1
    fi
}

# Stop worker
stop_worker() {
    if ! is_running; then
        echo -e "${YELLOW}⚠️  No blockchain worker running${NC}"
        return 1
    fi

    echo -e "${BLUE}🛑 Stopping blockchain queue worker...${NC}"
    pkill -f "queue:work.*blockchain"
    sleep 2

    if is_running; then
        echo -e "${YELLOW}⚠️  Worker still running, forcing kill...${NC}"
        pkill -9 -f "queue:work.*blockchain"
        sleep 1
    fi

    if ! is_running; then
        echo -e "${GREEN}✅ Blockchain worker stopped${NC}"
        rm -f "$PID_FILE"
        return 0
    else
        echo -e "${RED}❌ Failed to stop blockchain worker${NC}"
        return 1
    fi
}

# Restart worker
restart_worker() {
    echo -e "${BLUE}🔄 Restarting blockchain queue worker...${NC}"
    stop_worker
    sleep 1
    start_worker
}

# Show status
show_status() {
    echo -e "${BLUE}📊 Blockchain Queue Worker Status${NC}"
    echo "=================================="
    
    if is_running; then
        WORKER_PID=$(pgrep -f "queue:work.*blockchain")
        WORKER_START=$(ps -p "$WORKER_PID" -o lstart= 2>/dev/null)
        echo -e "Status: ${GREEN}✅ RUNNING${NC}"
        echo "PID: $WORKER_PID"
        echo "Started: $WORKER_START"
        echo ""
        echo "Process details:"
        ps aux | grep "queue:work.*blockchain" | grep -v grep
    else
        echo -e "Status: ${RED}❌ STOPPED${NC}"
        echo ""
        echo "Recent failed jobs (last 5):"
        php artisan queue:failed | tail -5
    fi

    echo ""
    echo "Recent log entries:"
    tail -20 "$LOG_FILE" 2>/dev/null || echo "No log file found"
}

# Auto-start if not running (for cron)
auto_ensure() {
    if ! is_running; then
        echo "[$(date '+%Y-%m-%d %H:%M:%S')] Blockchain worker not running, starting..." >> storage/logs/worker-monitor.log
        start_worker >> storage/logs/worker-monitor.log 2>&1
    fi
}

# Main command handler
case "${1:-status}" in
    start)
        start_worker
        ;;
    stop)
        stop_worker
        ;;
    restart)
        restart_worker
        ;;
    status)
        show_status
        ;;
    auto)
        auto_ensure
        ;;
    *)
        echo "Usage: $0 {start|stop|restart|status|auto}"
        echo ""
        echo "Commands:"
        echo "  start   - Start blockchain queue worker"
        echo "  stop    - Stop blockchain queue worker"
        echo "  restart - Restart blockchain queue worker"
        echo "  status  - Show worker status and recent logs"
        echo "  auto    - Auto-start if not running (for cron)"
        echo ""
        echo "Example crontab entry (check every 5 minutes):"
        echo "  */5 * * * * $PROJECT_DIR/bash_files/ensure-blockchain-worker.sh auto"
        exit 1
        ;;
esac
