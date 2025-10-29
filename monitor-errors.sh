#!/bin/bash
# Monitor errors in real-time

echo "🔍 Monitoring errors in real-time..."
echo "Press CTRL+C to stop"
echo ""

tail -f storage/logs/florenceegi-*.log | grep --line-buffered -i "error\|exception\|fatal" &
TAIL_PID=$!

# Cleanup on exit
trap "kill $TAIL_PID 2>/dev/null" EXIT

wait $TAIL_PID
