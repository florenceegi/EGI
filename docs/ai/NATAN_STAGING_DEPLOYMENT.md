# 🚀 N.A.T.A.N. 2.0 - Deployment Staging Guide

**Target Environment**: `https://app.13.48.57.194.sslip.io/`  
**Data**: 2025-10-12  
**Versione**: N.A.T.A.N. 2.0 con Anthropic Claude 3.5 Sonnet

---

## ✅ **PRE-REQUISITI (CRITICI)**

### **1️⃣ ALGOKIT MICROSERVICE (BLOCKCHAIN)**

**N.A.T.A.N. dipende da AlgoKit per la tokenizzazione blockchain!**

**Requisiti server:**

-   Node.js >= 18.x
-   npm >= 9.x
-   Porta 3000 disponibile
-   AlgoKit microservice in esecuzione

**Files del microservice:**

```
algokit-microservice/
├── server.js           (Node.js Express server)
├── package.json        (dependencies)
├── .env               (configurazione)
└── README.md
```

**Environment variables necessarie:**

```env
# AlgoKit Microservice
PORT=3000
ALGORAND_NETWORK=testnet  # o mainnet per produzione
TREASURY_MNEMONIC=<YOUR_TREASURY_MNEMONIC_HERE>
```

**Deploy AlgoKit su staging:**

```bash
# 1. SSH nel server
ssh forge@13.48.57.194

# 2. Vai nella directory app
cd /home/forge/app.13.48.57.194.sslip.io

# 3. Installa dipendenze Node.js
cd algokit-microservice
npm install

# 4. Configura .env
nano .env
# Aggiungi:
# PORT=3000
# ALGORAND_NETWORK=testnet
# TREASURY_MNEMONIC=<YOUR_25_WORD_MNEMONIC>

# 5. Test avvio manuale
node server.js
# Dovresti vedere: "🚀 REAL BLOCKCHAIN MICROSERVICE STARTING..."
# Ctrl+C per fermare

# 6. Avvio con PM2 (process manager)
npm install -g pm2
pm2 start server.js --name algokit-egi
pm2 save
pm2 startup  # segui istruzioni per auto-start

# 7. Verifica status
pm2 status
pm2 logs algokit-egi

# 8. Test endpoint
curl http://localhost:3000/health
# Risposta attesa: {"status":"ok","network":"testnet","treasury":"..."}
```

**Verifiche post-deploy AlgoKit:**

```bash
# Test health check
curl http://localhost:3000/health

# Test mint asset (opzionale)
curl -X POST http://localhost:3000/mint \
  -H "Content-Type: application/json" \
  -d '{"test": true}'

# Test anchor document
curl -X POST http://localhost:3000/anchor-document \
  -H "Content-Type: application/json" \
  -d '{"document_hash": "test123", "note": "Test", "metadata": {}}'
```

---

### **2️⃣ API KEY ANTHROPIC**

**Dove configurare:**

```bash
# Sul server staging
nano /path/to/.env
```

**Variabile richiesta:**

```env
ANTHROPIC_API_KEY=<YOUR_ANTHROPIC_API_KEY_HERE>
ANTHROPIC_MODEL=claude-3-5-sonnet-20241022
ANTHROPIC_BASE_URL=https://api.anthropic.com
ANTHROPIC_TIMEOUT=60
```

**⚠️ IMPORTANTE**: Sostituisci `<YOUR_ANTHROPIC_API_KEY_HERE>` con la tua chiave reale ottenuta da https://console.anthropic.com/

⚠️ **NOTA SICUREZZA**:

-   NON committare la API KEY nel repository
-   Configurare tramite `.env` sul server
-   La key è già nel tuo `.env` locale

---

### **2️⃣ FILES DA DEPLOYARE**

**Nuovi servizi creati:**

```
app/Services/AnthropicService.php      (9 KB)
app/Services/DataSanitizerService.php  (7 KB)
app/Services/RagService.php            (9 KB)
app/Services/NatanChatService.php      (6 KB - refactored)
```

**Configurazione:**

```
config/services.php                    (aggiunta sezione anthropic)
.env.example                           (aggiunte variabili ANTHROPIC_*)
```

**Views:**

```
resources/views/pa/natan/chat.blade.php (fix contrasto colori)
```

**Controller:**

```
app/Http/Controllers/PA/NatanChatController.php (già esistente)
```

**Routes:**

```
routes/pa-enterprise.php (già esistente)
```

---

### **3️⃣ DIPENDENZE**

**PHP:**

-   ✅ Nessuna nuova dipendenza richiesta
-   Laravel HTTP Client (già presente)
-   Illuminate Collections (core Laravel)
-   Ultra packages (già installati)

**Node.js (AlgoKit Microservice):**

```json
{
    "express": "^4.18.2",
    "algosdk": "^2.7.0",
    "cors": "^2.8.5",
    "dotenv": "^16.3.1"
}
```

**Installazione:**

```bash
cd algokit-microservice
npm install
```

---

## 🔧 **DEPLOYMENT STEPS (COMPLETO)**

### **STEP 1: Deploy AlgoKit Microservice**

```bash
# 1. SSH nel server
ssh forge@13.48.57.194

# 2. Vai nella directory app
cd /home/forge/app.13.48.57.194.sslip.io

# 3. Pull codice (include algokit-microservice/)
git pull origin main

# 4. Installa Node.js dependencies
cd algokit-microservice
npm install

# 5. Configura AlgoKit .env
nano .env
# Aggiungi:
# PORT=3000
# ALGORAND_NETWORK=testnet
# TREASURY_MNEMONIC=<YOUR_25_WORD_MNEMONIC>

# 6. Avvia con PM2
npm install -g pm2
pm2 start server.js --name algokit-egi
pm2 save
pm2 startup

# 7. Verifica AlgoKit attivo
pm2 status
curl http://localhost:3000/health
# Risposta attesa: {"status":"ok","network":"testnet",...}
```

---

### **STEP 2: Deploy Laravel App**

```bash
# 1. Torna alla root app
cd /home/forge/app.13.48.57.194.sslip.io

# 2. Config cache refresh
php artisan config:clear
php artisan cache:clear

# 3. Aggiungi variabili N.A.T.A.N. al .env Laravel
nano .env
# Aggiungi:
# ANTHROPIC_API_KEY=sk-ant-api03-...
# ANTHROPIC_MODEL=claude-3-5-sonnet-20241022
# ALGOKIT_BASE_URL=http://localhost:3000

# 4. Test connessione Anthropic
php artisan tinker --execute="
\$anthropic = app(\App\Services\AnthropicService::class);
echo \$anthropic->isAvailable() ? '✅ Anthropic OK' : '❌ Anthropic FAIL';
"

# 5. Test connessione AlgoKit
php artisan tinker --execute="
\$response = \Illuminate\Support\Facades\Http::get('http://localhost:3000/health');
echo \$response->successful() ? '✅ AlgoKit OK' : '❌ AlgoKit FAIL';
"

# 6. Restart PHP-FPM (se necessario)
# sudo systemctl restart php8.3-fpm
```

### **OPZIONE B: Deploy via Forge Dashboard**

1. Vai su Laravel Forge dashboard
2. Seleziona il sito `app.13.48.57.194.sslip.io`
3. Clicca "Deploy Now"
4. Vai su "Environment" → aggiungi variabili `ANTHROPIC_*`
5. Clicca "Update"

---

## 🧪 **TESTING POST-DEPLOY**

### **Test 1: Verifica Connessione Anthropic**

```bash
ssh forge@13.48.57.194
cd /home/forge/app.13.48.57.194.sslip.io
php artisan tinker --execute="
\$anthropic = app(\App\Services\AnthropicService::class);
echo \$anthropic->isAvailable() ? '✅ OK' : '❌ FAIL';
"
```

**Output atteso:** `✅ OK`

---

### **Test 2: Verifica Chat UI**

1. Vai su: `https://app.13.48.57.194.sslip.io/pa/natan/chat`
2. Dovresti vedere l'interfaccia N.A.T.A.N.
3. Prova query: "Come funziona N.A.T.A.N.?"
4. Verifica risposta da Claude

---

### **Test 3: Verifica GDPR Data Sanitization**

```bash
# Sul server staging
tail -f storage/logs/laravel.log | grep "GDPR"
```

Poi fai una query dalla chat e verifica che vedi log come:

```
[NatanChatService][GDPR] Data sent to Anthropic AI
- user_id: X
- acts_count: Y
- fields_sent: [id, protocol_number, date, type, title, ...]
```

**✅ VERIFICA**: Nei log NON devono comparire:

-   `digital_signature`
-   `file_path`
-   `ip_address`
-   `user_id` (come campo dati, solo come context)

---

## ⚠️ **POTENTIAL ISSUES & FIXES**

### **Issue 1: "Anthropic API key not configured"**

**Causa**: Variabile `ANTHROPIC_API_KEY` mancante o vuota

**Fix:**

```bash
nano .env
# Aggiungi: ANTHROPIC_API_KEY=sk-ant-...
php artisan config:clear
```

---

### **Issue 2: "Class AnthropicService not found"**

**Causa**: Autoload non aggiornato

**Fix:**

```bash
composer dump-autoload
php artisan cache:clear
```

---

### **Issue 3: "Column 'pa_title' not found"**

**Causa**: Query usano colonne sbagliate (già fixato)

**Verifica fix applicato:**

```bash
grep "pa_title" app/Services/RagService.php
# Non deve restituire nulla
```

---

### **Issue 4: Chat non risponde / timeout**

**Causa possibile**: Firewall blocca chiamate a `api.anthropic.com`

**Fix:**

```bash
# Test connettività
curl -I https://api.anthropic.com
# Deve restituire 200 o 405 (non 403/timeout)
```

Se bloccato, contatta hosting provider per whitelist `api.anthropic.com`

---

## 📊 **MONITORING**

### **Logs da monitorare:**

```bash
# Errori generali
tail -f storage/logs/laravel.log

# Solo N.A.T.A.N.
tail -f storage/logs/laravel.log | grep -E "NatanChat|Anthropic|RAG|GDPR"

# Error Manager
tail -f storage/logs/error_manager.log
```

---

## 💰 **COSTI ANTHROPIC**

**Modello**: Claude 3.5 Sonnet  
**Input**: $3.00 per 1M tokens  
**Output**: $15.00 per 1M tokens

**Stima query media:**

-   Input: ~1.000 token
-   Output: ~500 token
-   **Costo per query**: ~$0.011 (1 centesimo)

**Esempio mensile:**

-   100 query/giorno × 30 giorni = 3.000 query/mese
-   **Costo stimato**: ~€30/mese

---

## 🔒 **GDPR COMPLIANCE CHECKLIST**

✅ **Data Minimization**: Solo metadati pubblici inviati  
✅ **Audit Trail**: Logging completo di ogni richiesta AI  
✅ **Data Isolation**: `DataSanitizerService` filtra PII  
✅ **Validation**: Check anti-GDPR violation  
✅ **Transparency**: Fonti citate in ogni risposta

**DPA Anthropic**: ✅ Disponibile su richiesta  
**Data Residency**: ⚠️ US (Anthropic servers)  
**SCC EU-US**: ✅ Standard Contractual Clauses applicabili

---

## 📞 **SUPPORT**

**Se qualcosa non funziona:**

1. Controlla logs: `storage/logs/laravel.log`
2. Verifica API key configurata: `php artisan tinker` → `config('services.anthropic.api_key')`
3. Test connessione: `curl https://api.anthropic.com`
4. Contatta Fabio con:
    - Messaggio errore esatto
    - Log relevanti (ultimi 50 righe)
    - Query che hai provato

---

## ✅ **DEPLOYMENT COMPLETE WHEN:**

### **AlgoKit Microservice:**

-   [ ] Node.js dependencies installate (`npm install`)
-   [ ] AlgoKit `.env` configurato (PORT, ALGORAND_NETWORK, TREASURY_MNEMONIC)
-   [ ] PM2 avviato: `pm2 status` mostra `algokit-egi` online
-   [ ] Health check OK: `curl http://localhost:3000/health`
-   [ ] Test mint funziona (opzionale)
-   [ ] Test anchor-document funziona

### **Laravel App (N.A.T.A.N.):**

-   [ ] Git pull effettuato (ultimi 4 commit)
-   [ ] Anthropic API key configurata in `.env`
-   [ ] ALGOKIT_BASE_URL configurato (http://localhost:3000)
-   [ ] Config cache cleared
-   [ ] Test Anthropic availability: ✅ OK
-   [ ] Test AlgoKit connectivity: ✅ OK
-   [ ] Chat UI accessibile su `/pa/natan/chat`
-   [ ] Query di test AI funziona
-   [ ] Upload atto PA + tokenizzazione funziona
-   [ ] GDPR logging attivo
-   [ ] Nessun campo privato nei log

### **Integration Test (End-to-End):**

-   [ ] Upload PDF atto PA
-   [ ] Abilita "Certifica con N.A.T.A.N."
-   [ ] Verifica job `TokenizePaActJob` in esecuzione
-   [ ] Verifica transazione Algorand su explorer
-   [ ] Chat N.A.T.A.N. risponde con atto caricato

---

**Ready to deploy!** 🚀

**Tempo stimato deployment:**

-   AlgoKit setup: ~10 minuti
-   Laravel app: ~5 minuti
-   Testing: ~10 minuti
-   **TOTALE: ~25 minuti**
