# 🚀 N.A.T.A.N. 2.0 - Deployment Staging Guide

**Target Environment**: `https://app.13.48.57.194.sslip.io/`  
**Data**: 2025-10-12  
**Versione**: N.A.T.A.N. 2.0 con Anthropic Claude 3.5 Sonnet

---

## ✅ **PRE-REQUISITI (CRITICI)**

### **1️⃣ API KEY ANTHROPIC**

**Dove configurare:**
```bash
# Sul server staging
nano /path/to/.env
```

**Variabile richiesta:**
```env
ANTHROPIC_API_KEY=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
ANTHROPIC_MODEL=claude-3-5-sonnet-20241022
ANTHROPIC_BASE_URL=https://api.anthropic.com
ANTHROPIC_TIMEOUT=60
```

⚠️ **NOTA SICUREZZA**: 
- NON committare la API KEY nel repository
- Configurare tramite `.env` sul server
- La key è già nel tuo `.env` locale

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

### **3️⃣ DIPENDENZE PHP**

**Nessuna nuova dipendenza richiesta!** ✅

Tutto usa:
- Laravel HTTP Client (già presente)
- Illuminate Collections (core Laravel)
- Ultra packages (già installati)

---

## 🔧 **DEPLOYMENT STEPS**

### **OPZIONE A: Deploy via Git Pull (Forge)**

```bash
# 1. SSH nel server
ssh forge@13.48.57.194

# 2. Vai nella directory app
cd /home/forge/app.13.48.57.194.sslip.io

# 3. Pull dei nuovi commit
git pull origin main

# 4. Config cache refresh
php artisan config:clear
php artisan cache:clear

# 5. Aggiungi ANTHROPIC_API_KEY al .env
nano .env
# Aggiungi:
# ANTHROPIC_API_KEY=sk-ant-api03-...
# ANTHROPIC_MODEL=claude-3-5-sonnet-20241022

# 6. Test connessione Anthropic
php artisan tinker
>>> $anthropic = app(\App\Services\AnthropicService::class);
>>> $anthropic->isAvailable();
>>> exit

# 7. Restart PHP-FPM (se necessario)
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
- `digital_signature`
- `file_path`
- `ip_address`
- `user_id` (come campo dati, solo come context)

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
- Input: ~1.000 token
- Output: ~500 token
- **Costo per query**: ~$0.011 (1 centesimo)

**Esempio mensile:**
- 100 query/giorno × 30 giorni = 3.000 query/mese
- **Costo stimato**: ~€30/mese

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

- [ ] API key configurata in `.env`
- [ ] Git pull effettuato (ultimi 3 commit)
- [ ] Config cache cleared
- [ ] Test Anthropic availability: ✅ OK
- [ ] Chat UI accessibile su `/pa/natan/chat`
- [ ] Query di test funziona
- [ ] GDPR logging attivo
- [ ] Nessun campo privato nei log

---

**Ready to deploy!** 🚀

