# 📋 CHECKLIST DEPLOYMENT STAGING - ALGORAND CONFIGURATION

**Server:** https://app.13.48.57.194.sslip.io/  
**Data:** 12 Ottobre 2025  
**Commit:** b0bbee5

---

## ✅ PRE-DEPLOYMENT (DA FARE IN LOCALE)

- [x] Commit configurazione Algorand (`b0bbee5`)
- [ ] Git push su `main`
- [ ] Aspetta deploy automatico Forge (max 2 minuti)

---

## 🚀 DEPLOYMENT SU STAGING (VIA SSH)

### **OPZIONE A: Script Automatico (RACCOMANDATO)**

```bash
# SSH nel server
ssh forge@13.48.57.194

# Vai nella directory app
cd /home/forge/app.13.48.57.194.sslip.io

# Esegui script deployment
bash deploy-staging-algorand.sh
```

Lo script fa tutto automaticamente:
- ✅ Backup .env esistente
- ✅ Aggiunge variabili ALGORAND_API_URL
- ✅ Configura microservice .env
- ✅ Installa dipendenze npm
- ✅ Avvia microservice
- ✅ Clear config cache Laravel
- ✅ Restart queue workers
- ✅ Verifica configurazione

**Tempo stimato:** 2-3 minuti

---

### **OPZIONE B: Manuale (se script fallisce)**

#### **1. SSH nel server**
```bash
ssh forge@13.48.57.194
cd /home/forge/app.13.48.57.194.sslip.io
```

#### **2. Backup .env**
```bash
cp .env .env.backup.$(date +%Y%m%d_%H%M%S)
```

#### **3. Aggiungi variabili al .env principale**
```bash
nano .env
```

Trova la sezione `ALGORAND_NETWORK=` e aggiungi sotto:
```properties
# Algorand API URLs (AlgoNode - FREE)
ALGORAND_API_URL=https://testnet-api.algonode.cloud
ALGORAND_INDEXER_URL=https://testnet-idx.algonode.cloud
ALGORAND_API_KEY=
```

Salva con `CTRL+X`, `Y`, `ENTER`

#### **4. Configura microservice**
```bash
cd algokit-microservice

cat > .env << 'EOF'
PORT=3000
ALGORAND_NETWORK=testnet
ALGORAND_API_URL=https://testnet-api.algonode.cloud
ALGORAND_INDEXER_URL=https://testnet-idx.algonode.cloud
TREASURY_MNEMONIC=urge rotate level slush enjoy kick poem office explain jar credit exercise ensure crash phone cram vibrant settle proud patch disease universe indicate abandon ahead
LOG_LEVEL=info
EOF
```

#### **5. Installa dipendenze e avvia microservice**
```bash
npm install --production

# Kill vecchio processo se esiste
pkill -f "node server.js"

# Avvia nuovo processo
nohup npm start > ~/logs/algokit-microservice.log 2>&1 &
echo $! > ~/algokit-microservice.pid

# Verifica avvio
ps aux | grep "node server.js"
curl http://localhost:3000/health
```

#### **6. Clear config cache Laravel**
```bash
cd /home/forge/app.13.48.57.194.sslip.io

php artisan config:clear
php artisan config:cache
php artisan queue:restart
```

#### **7. Verifica configurazione**
```bash
php artisan tinker --execute="dump(config('algorand.algorand.api_url'));"
# Output atteso: "https://testnet-api.algonode.cloud"

php artisan tinker --execute="dump(config('algorand.algorand.indexer_url'));"
# Output atteso: "https://testnet-idx.algonode.cloud"
```

---

## ⚙️ CONFIGURAZIONE FORGE WORKERS (MANUALE)

**⚠️ IMPORTANTE:** Vai nel pannello Forge e modifica:

### **Worker "default"**
- [ ] Connection: `database` (NON redis)
- [ ] Queue: `default`
- [ ] Timeout: `60`
- [ ] Sleep: `3`
- [ ] Tries: `3`
- [ ] Max Jobs: `0` (unlimited)
- [ ] Max Time: `0` (unlimited)
- [ ] Memory: `128` MB
- [ ] Backoff: `0`

### **Worker "blockchain"** (già configurato, verifica)
- [x] Connection: `redis`
- [x] Queue: `blockchain`
- [x] Timeout: `300`
- [x] Sleep: `3`
- [x] Tries: `3`

---

## 🧪 TESTING POST-DEPLOYMENT

### **1. Verifica microservice**
```bash
curl http://localhost:3000/health | jq
```

Output atteso:
```json
{
  "status": "healthy",
  "network": "testnet",
  "treasury": {
    "address": "TF67P6...",
    "balance": 1
  }
}
```

### **2. Verifica Laravel config**
```bash
php artisan tinker --execute="
echo 'API URL: ' . config('algorand.algorand.api_url') . PHP_EOL;
echo 'Indexer: ' . config('algorand.algorand.indexer_url') . PHP_EOL;
echo 'Network: ' . config('algorand.algorand.network') . PHP_EOL;
"
```

### **3. Test mint dalla UI**
1. Vai su: https://app.13.48.57.194.sslip.io/
2. Login con user test
3. Vai su EGI da mintare
4. Click "Mint on Blockchain"
5. Attendi completamento (max 30 secondi)

### **4. Verifica su TestNet Explorer**
Dopo mint riuscito, copia ASA ID e vai su:
```
https://testnet.algoexplorer.io/asset/[ASA_ID]
```

### **5. Monitor logs in real-time**
```bash
# Laravel logs
tail -f /home/forge/app.13.48.57.194.sslip.io/storage/logs/laravel.log

# Microservice logs
tail -f /home/forge/logs/algokit-microservice.log

# Error manager logs
tail -f /home/forge/app.13.48.57.194.sslip.io/storage/logs/error_manager.log
```

---

## 🚨 TROUBLESHOOTING

### **Problema: Microservice non risponde**

**Diagnosi:**
```bash
ps aux | grep "node server.js"
curl http://localhost:3000/health
cat /home/forge/logs/algokit-microservice.log
```

**Fix:**
```bash
cd /home/forge/app.13.48.57.194.sslip.io/algokit-microservice
pkill -f "node server.js"
nohup npm start > ~/logs/algokit-microservice.log 2>&1 &
```

### **Problema: Config non caricata**

**Fix:**
```bash
php artisan config:clear
php artisan config:cache
php artisan cache:clear
php artisan queue:restart
```

### **Problema: Worker non processa jobs**

**Diagnosi:**
```bash
php artisan queue:monitor
ps aux | grep "queue:work"
```

**Fix via Forge:**
1. Vai su "Queues" nel pannello
2. Click "Restart" su worker "default"
3. Verifica con `php artisan queue:monitor`

### **Problema: Mint fallisce con "API URL not configured"**

**Fix:**
```bash
# Verifica variabili ENV
grep "ALGORAND_API_URL" .env

# Se mancano, aggiungile manualmente
nano .env

# Poi clear cache
php artisan config:clear && php artisan config:cache
```

---

## 📊 SUCCESS CRITERIA

- [ ] Script deployment completato senza errori
- [ ] Microservice healthy (curl localhost:3000/health)
- [ ] Laravel config caricata (api_url presente)
- [ ] Worker "default" usa connection "database"
- [ ] Test mint completato con successo
- [ ] ASA visibile su testnet.algoexplorer.io
- [ ] Logs puliti (no errori critici)

---

## 📞 SUPPORT

**Se problemi persistono:**

1. Copia ultimi 50 righe log Laravel:
   ```bash
   tail -50 storage/logs/laravel.log
   ```

2. Copia ultimi 50 righe log microservice:
   ```bash
   tail -50 ~/logs/algokit-microservice.log
   ```

3. Copia config dump:
   ```bash
   php artisan tinker --execute="dump(config('algorand'));"
   ```

4. Invia a Fabio per debug.

---

## 🎯 ROLLBACK (se necessario)

```bash
# Restore backup .env
cp .env.backup.YYYYMMDD_HHMMSS .env

# Clear cache
php artisan config:clear
php artisan config:cache
php artisan queue:restart

# Stop microservice
pkill -f "node server.js"
```

---

**✅ Checklist completata quando tutti i checkbox sono spuntati.**
