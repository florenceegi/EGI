*# 🌐 Algorand TestNet Setup Guide

**Obiettivo:** Configurare AlgoKit microservice per usare Algorand TestNet invece di Sandbox locale.

---

## 📋 **STEP 1: Creare Treasury Wallet su TestNet**

### **Opzione A: Via Browser (Raccomandato)**

1. **Vai su Algorand TestNet Explorer:**
   ```
   https://testnet.algoexplorer.io/
   ```

2. **Crea nuovo wallet:**
   - Clicca su "Create Account" (in alto a destra)
   - Salva il **mnemonic** (25 parole) in un posto SICURO
   - Copia l'**address** del wallet

### **Opzione B: Via AlgoSDK**

```javascript
const algosdk = require("algosdk");

// Genera nuovo account
const account = algosdk.generateAccount();

console.log("Address:", account.addr);
console.log("Mnemonic:", algosdk.secretKeyToMnemonic(account.sk));

// SALVA QUESTI DATI IN MODO SICURO!
```

---

## 💰 **STEP 2: Richiedere Fondi dal TestNet Faucet**

1. **Vai al TestNet Faucet (Bank):**
   ```
   https://bank.testnet.algorand.network/
   ```

2. **Richiedi fondi:**
   - Inserisci l'address del wallet creato
   - Clicca "Dispense"
   - Riceverai **10 ALGO** (gratis per testing)

3. **Verifica fondi ricevuti:**
   ```
   https://testnet.algoexplorer.io/address/TUO_ADDRESS
   ```
   
   Dovresti vedere:
   - Balance: ~10 ALGO
   - Status: Active

---

## ⚙️ **STEP 3: Configurare Microservice**

### **A. Crea `.env.testnet`:**

```bash
cd /home/fabio/EGI/algokit-microservice

# Copia template
cp .env.testnet.example .env.testnet

# Edita con il TUO mnemonic
nano .env.testnet
```

### **B. Inserisci dati wallet:**

```env
PORT=3000
ALGORAND_NETWORK=testnet

# Inserisci QUI il mnemonic del wallet TestNet (25 parole)
TREASURY_MNEMONIC=word1 word2 word3 ... word25

LOG_LEVEL=info
```

### **C. Switch a TestNet:**

```bash
# Usa script helper
./switch-network.sh testnet

# Output:
# ✅ Microservice configurato per TESTNET
# 📍 Network: Algorand TestNet (Public API)
```

---

## 🚀 **STEP 4: Avviare Microservice su TestNet**

```bash
cd /home/fabio/EGI/algokit-microservice

# Stop microservice se running
pm2 stop egi-microservice 2>/dev/null || pkill -f "node server.js"

# Start con TestNet config
npm start

# Output atteso:
# 🚀 REAL BLOCKCHAIN MICROSERVICE STARTING...
# 🌐 MODE: TESTNET (Public API)
# 📍 Algod Server: https://testnet-api.algonode.cloud
# 📍 Treasury Address: VAQTX5...UFFV
# 🚀 AlgoKit Microservice running on port 3000
```

---

## ✅ **STEP 5: Verificare Connessione TestNet**

### **Test Health Endpoint:**

```bash
curl http://localhost:3000/health | jq

# Output atteso:
{
  "status": "healthy",
  "network": "testnet",
  "round": 12345678,
  "treasury": {
    "address": "VAQTX5...UFFV",
    "balance": 10.0,
    "assets": 0
  },
  "algod_server": "https://testnet-api.algonode.cloud",
  "mode": "REAL_BLOCKCHAIN"
}
```

### **Verifica Balance Treasury:**

```bash
# Via AlgoExplorer
open https://testnet.algoexplorer.io/address/TUO_TREASURY_ADDRESS

# Via curl
curl "https://testnet-api.algonode.cloud/v2/accounts/TUO_TREASURY_ADDRESS" | jq
```

---

## 🧪 **STEP 6: Test Mint su TestNet**

### **A. Configura Laravel .env:**

```env
# .env Laravel
ALGORAND_NETWORK=testnet
ALGOKIT_MICROSERVICE_URL=http://localhost:3000
```

### **B. Test mint via UI:**

1. Vai su: `http://localhost:8004/egis/{id}/mint`
2. Completa pagamento (mock)
3. Monitora log:
   ```bash
   tail -f storage/logs/upload.log
   ```

4. Verifica su TestNet Explorer:
   ```
   https://testnet.algoexplorer.io/tx/TX_ID
   https://testnet.algoexplorer.io/asset/ASA_ID
   ```

---

## 🔄 **STEP 7: Switch tra Sandbox e TestNet**

### **Tornare a Sandbox:**

```bash
cd /home/fabio/EGI/algokit-microservice

# Switch a sandbox
./switch-network.sh sandbox

# Restart microservice
npm start
```

### **Switch a TestNet:**

```bash
# Switch a testnet
./switch-network.sh testnet

# Restart microservice
npm start
```

---

## 🐛 **TROUBLESHOOTING**

### **Problema: "insufficient balance"**

```bash
# Causa: Treasury wallet senza fondi
# Soluzione: Richiedi fondi dal faucet
open https://bank.testnet.algorand.network/
```

### **Problema: "connection timeout"**

```bash
# Causa: TestNet API down o firewall
# Verifica: Test connessione diretta
curl -I https://testnet-api.algonode.cloud

# Se fallisce, prova API alternativa:
# algodServer = "https://testnet-algorand.api.purestake.io/ps2";
```

### **Problema: "invalid mnemonic"**

```bash
# Causa: Mnemonic non valido o con spazi extra
# Soluzione: Verifica formato esatto (25 parole separate da spazio)

# Test mnemonic in Node:
node
> const algosdk = require("algosdk");
> const mnemonic = "word1 word2 ... word25";
> const account = algosdk.mnemonicToSecretKey(mnemonic);
> console.log(account.addr);
```

### **Problema: Microservice parte ma health fail**

```bash
# Verifica log dettagliati
npm start

# Output mostra errore esatto
# Possibili cause:
# - Mnemonic errato
# - Network API down
# - Firewall blocca HTTPS
```

---

## 📊 **COMPARAZIONE: Sandbox vs TestNet**

| Feature | Sandbox (Local) | TestNet (Public) |
|---------|----------------|------------------|
| **Network** | Privato (Docker) | Pubblico (Algorand) |
| **Costo** | Gratis | Gratis (faucet) |
| **Velocità** | Istantaneo | ~4 secondi/block |
| **Persistenza** | NO (reset Docker) | SÌ (blockchain pubblico) |
| **Explorer** | Locale | https://testnet.algoexplorer.io |
| **Fondi** | Illimitati | 10 ALGO dal faucet |
| **Setup** | Docker richiesto | Solo internet |
| **Production-like** | NO | SÌ (identico a MainNet) |

---

## 🎯 **CHECKLIST DEPLOYMENT STAGING**

Quando pronto per staging/production:

```
[ ] Treasury wallet TestNet creato
[ ] Fondi richiesti dal faucet (min 10 ALGO)
[ ] .env.testnet configurato con mnemonic corretto
[ ] Microservice testa OK in locale su TestNet
[ ] Mint test completo funzionante su TestNet
[ ] ASA visibile su TestNet Explorer
[ ] Laravel .env aggiornato (ALGORAND_NETWORK=testnet)
[ ] Queue worker configurato su server staging
[ ] PM2 config per microservice su staging
[ ] Test completo su staging (https://app.13.48.57.194.sslip.io)
```

---

## 🔐 **SICUREZZA - IMPORTANTE!**

### **⚠️ MAI committare mnemonic su Git!**

```bash
# Verifica .gitignore
cat algokit-microservice/.gitignore

# Deve contenere:
.env
.env.testnet
.env.production
```

### **Production: Usa Secret Manager**

Per production (MainNet), NON mettere mnemonic in `.env`!

**Opzioni sicure:**
- AWS Secrets Manager
- HashiCorp Vault
- Azure Key Vault
- Environment variables criptate su Forge

---

**Fine guida - Happy Testing! 🚀**
