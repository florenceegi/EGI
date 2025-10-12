#!/bin/bash

# Script per switchare tra Sandbox e TestNet
# Usage: ./switch-network.sh [sandbox|testnet]

NETWORK=${1:-sandbox}

case $NETWORK in
    sandbox)
        echo "🔄 Switching to SANDBOX mode..."
        cp .env.example .env
        echo "✅ Microservice configurato per SANDBOX (localhost)"
        echo "📍 Treasury: Wallet locale Sandbox"
        echo ""
        echo "🚀 Avvia microservice con:"
        echo "   npm start"
        ;;
    
    testnet)
        echo "🔄 Switching to TESTNET mode..."
        
        if [ ! -f .env.testnet ]; then
            echo "❌ ERRORE: .env.testnet non trovato!"
            echo ""
            echo "📝 Crea .env.testnet con:"
            echo "   1. Copia .env.testnet.example"
            echo "   2. Genera wallet TestNet: https://testnet.algoexplorer.io/"
            echo "   3. Richiedi fondi: https://bank.testnet.algorand.network/"
            echo "   4. Inserisci mnemonic in TREASURY_MNEMONIC"
            exit 1
        fi
        
        cp .env.testnet .env
        echo "✅ Microservice configurato per TESTNET"
        echo "📍 Network: Algorand TestNet (Public API)"
        echo ""
        echo "⚠️  IMPORTANTE:"
        echo "   - Verifica che il wallet abbia fondi (min 1 ALGO)"
        echo "   - TestNet faucet: https://bank.testnet.algorand.network/"
        echo ""
        echo "🚀 Avvia microservice con:"
        echo "   npm start"
        ;;
    
    *)
        echo "❌ Network non valido: $NETWORK"
        echo ""
        echo "Usage: ./switch-network.sh [sandbox|testnet]"
        exit 1
        ;;
esac

echo ""
echo "📋 Verifica configurazione:"
echo "   cat .env | grep ALGORAND_NETWORK"
