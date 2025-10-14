#!/bin/bash

################################################################################
# CONNECT TO STAGING WITH TMUX (FROM LOCAL)
################################################################################
# Purpose: Connect to staging and automatically attach to persistent tmux session
# Usage: bash connect-staging.sh (from your local machine)
################################################################################

echo "🔌 Connecting to staging with persistent tmux session..."
echo ""

# SSH and attach to tmux session (create if doesn't exist)
ssh forge@13.48.57.194 -t "tmux attach -t staging || tmux new-session -s staging"

echo ""
echo "ℹ️  SSH connection closed. Your tmux session is still running on server!"
echo "   Run this script again to reconnect: bash connect-staging.sh"
