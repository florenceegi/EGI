#!/bin/bash

################################################################################
# INSTALL WEB TERMINAL ON STAGING SERVER
################################################################################
# Purpose: Install WeTTY (Web-based terminal) accessible via browser
# Author: Padmin D. Curtis (AI Partner OS3.0)
# Date: 2025-10-14
# Usage: Copia questo script sul server e eseguilo
################################################################################

set -e

echo "════════════════════════════════════════════════════════════════"
echo "  🖥️  INSTALLAZIONE WEB TERMINAL (WeTTY)"
echo "════════════════════════════════════════════════════════════════"
echo ""

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m'

# Configuration
WETTY_PORT=3030
WETTY_USER="forge"

################################################################################
# STEP 1: Install Node.js (if not present)
################################################################################

echo -e "${BLUE}[1/5]${NC} Verifico Node.js..."

if ! command -v node &> /dev/null; then
    echo -e "${YELLOW}⚠️  Node.js non trovato, installo...${NC}"
    curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
    sudo apt-get install -y nodejs
else
    echo -e "${GREEN}✅ Node.js già installato: $(node -v)${NC}"
fi

echo ""

################################################################################
# STEP 2: Install WeTTY globally
################################################################################

echo -e "${BLUE}[2/5]${NC} Installo WeTTY..."

sudo npm install -g wetty

echo -e "${GREEN}✅ WeTTY installato${NC}"
echo ""

################################################################################
# STEP 3: Create systemd service for WeTTY
################################################################################

echo -e "${BLUE}[3/5]${NC} Creo servizio systemd..."

sudo tee /etc/systemd/system/wetty.service > /dev/null <<EOF
[Unit]
Description=WeTTY Web Terminal
After=network.target

[Service]
Type=simple
User=$WETTY_USER
ExecStart=/usr/bin/wetty --port $WETTY_PORT --host 0.0.0.0 --base /terminal
Restart=always
RestartSec=10
StandardOutput=journal
StandardError=journal

[Install]
WantedBy=multi-user.target
EOF

echo -e "${GREEN}✅ Servizio systemd creato${NC}"
echo ""

################################################################################
# STEP 4: Start and enable WeTTY service
################################################################################

echo -e "${BLUE}[4/5]${NC} Avvio servizio WeTTY..."

sudo systemctl daemon-reload
sudo systemctl enable wetty
sudo systemctl start wetty

echo -e "${GREEN}✅ Servizio WeTTY avviato${NC}"
echo ""

################################################################################
# STEP 5: Configure Nginx reverse proxy (optional)
################################################################################

echo -e "${BLUE}[5/5]${NC} Configurazione Nginx..."

NGINX_CONF="/etc/nginx/sites-available/staging.florenceegi.com"

if [ -f "$NGINX_CONF" ]; then
    echo -e "${YELLOW}⚠️  Aggiungi questa configurazione al tuo Nginx:${NC}"
    echo ""
    echo "location /terminal {"
    echo "    proxy_pass http://127.0.0.1:$WETTY_PORT;"
    echo "    proxy_http_version 1.1;"
    echo "    proxy_set_header Upgrade \$http_upgrade;"
    echo "    proxy_set_header Connection \"upgrade\";"
    echo "    proxy_set_header Host \$host;"
    echo "    proxy_set_header X-Real-IP \$remote_addr;"
    echo "    proxy_set_header X-Forwarded-For \$proxy_add_x_forwarded_for;"
    echo "    proxy_set_header X-Forwarded-Proto \$scheme;"
    echo "}"
    echo ""
    echo "Poi esegui: sudo systemctl reload nginx"
else
    echo -e "${YELLOW}⚠️  Nginx config non trovata, accedi direttamente via: http://[SERVER-IP]:$WETTY_PORT${NC}"
fi

echo ""

################################################################################
# DONE
################################################################################

echo -e "${GREEN}════════════════════════════════════════════════════════════════${NC}"
echo -e "${GREEN}  ✅ INSTALLAZIONE COMPLETATA!${NC}"
echo -e "${GREEN}════════════════════════════════════════════════════════════════${NC}"
echo ""
echo -e "${BLUE}📋 COME ACCEDERE:${NC}"
echo ""
echo -e "1. ${YELLOW}Via browser diretto:${NC}"
echo -e "   http://13.48.57.194:$WETTY_PORT"
echo ""
echo -e "2. ${YELLOW}Via dominio (dopo config Nginx):${NC}"
echo -e "   https://staging.florenceegi.com/terminal"
echo ""
echo -e "${BLUE}🔒 SICUREZZA:${NC}"
echo -e "   Usa credenziali SSH del server per il login"
echo ""
echo -e "${BLUE}🛠️  COMANDI UTILI:${NC}"
echo -e "   ${YELLOW}sudo systemctl status wetty${NC}  - Verifica stato"
echo -e "   ${YELLOW}sudo systemctl stop wetty${NC}    - Ferma servizio"
echo -e "   ${YELLOW}sudo systemctl start wetty${NC}   - Avvia servizio"
echo -e "   ${YELLOW}sudo journalctl -u wetty -f${NC}  - Log in tempo reale"
echo ""

