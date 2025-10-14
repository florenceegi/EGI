#!/bin/bash

################################################################################
# SETUP TMUX PERSISTENT SESSION ON STAGING
################################################################################
# Purpose: Create a persistent tmux session that survives SSH disconnections
# Usage: Run this ONCE on staging server
################################################################################

echo "🔧 Setting up tmux persistent session..."

# Install tmux if not present (should already be there on Forge)
if ! command -v tmux &> /dev/null; then
    echo "Installing tmux..."
    sudo apt-get update && sudo apt-get install -y tmux
fi

# Create or attach to 'staging' session
tmux has-session -t staging 2>/dev/null

if [ $? != 0 ]; then
    echo "✅ Creating new tmux session 'staging'"
    tmux new-session -d -s staging
    
    # Set some useful tmux options
    tmux set-option -t staging -g history-limit 10000
    tmux set-option -t staging -g mouse on
    
    echo ""
    echo "✅ Tmux session 'staging' created!"
else
    echo "✅ Tmux session 'staging' already exists"
fi

echo ""
echo "════════════════════════════════════════════════════════════════"
echo "  📋 HOW TO USE TMUX PERSISTENT SESSION"
echo "════════════════════════════════════════════════════════════════"
echo ""
echo "1️⃣  ATTACH to session (from SSH):"
echo "   tmux attach -t staging"
echo ""
echo "2️⃣  DETACH from session (session keeps running):"
echo "   Press: Ctrl+B then D"
echo ""
echo "3️⃣  If SSH drops, just reconnect and run again:"
echo "   ssh forge@13.48.57.194"
echo "   tmux attach -t staging"
echo ""
echo "4️⃣  USEFUL COMMANDS inside tmux:"
echo "   Ctrl+B then %  - Split vertical"
echo "   Ctrl+B then \"  - Split horizontal"
echo "   Ctrl+B then Arrow - Navigate splits"
echo "   Ctrl+B then C  - New window"
echo "   Ctrl+B then N  - Next window"
echo ""
echo "5️⃣  KILL session when done:"
echo "   tmux kill-session -t staging"
echo ""
echo "════════════════════════════════════════════════════════════════"
