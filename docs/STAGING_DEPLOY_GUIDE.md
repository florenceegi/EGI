# 🚀 Deploy su Staging - Checklist & Guide

> **Per Presentazione N.A.T.A.N.**  
> **Data:** 24 Ottobre 2025

---

## ✅ CHECKLIST PRE-DEPLOY

### **1. Configurazioni .env su Staging**

Le seguenti variabili d'ambiente **DEVONO** essere configurate su staging:

```bash
# ============================================
# OPENAI (per Embeddings)
# ============================================
OPENAI_API_KEY=sk-...your-key...
OPENAI_BASE_URL=https://api.openai.com/v1
OPENAI_EMBEDDING_MODEL=text-embedding-ada-002
OPENAI_TIMEOUT=30

# ============================================
# ANTHROPIC CLAUDE (per N.A.T.A.N.)
# ============================================
ANTHROPIC_API_KEY=sk-ant-...your-key...
ANTHROPIC_BASE_URL=https://api.anthropic.com
ANTHROPIC_MODEL=claude-3-5-sonnet-20241022
ANTHROPIC_MAX_TOKENS=4096
ANTHROPIC_TIMEOUT=60

# ============================================
# PUSHER (se serve per chat, opzionale per ora)
# ============================================
BROADCAST_DRIVER=log  # o 'pusher' se configurato
PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_APP_CLUSTER=eu
```

**IMPORTANTE:**

-   Le API keys sono **SENSIBILI** e **NON** devono essere committate nel repository
-   Su staging, aggiungi queste variabili nel file `.env` del server

---

## 📋 MIGRATIONS DA ESEGUIRE

Le seguenti migrations **devono** essere eseguite su staging (in ordine):

### **Migration 1: pa_act_embeddings**

```bash
# File: 2025_10_23_182606_create_pa_act_embeddings_table.php
```

**Crea:** Tabella per vector embeddings (semantic search)

### **Migration 2: natan_chat_messages**

```bash
# File: 2025_10_23_xxxxxx_create_natan_chat_messages_table.php
```

**Crea:** Tabella per storico conversazioni N.A.T.A.N.

### **Migration 3: reference_message_id**

```bash
# File: 2025_10_23_213900_add_reference_message_id_to_natan_chat_messages_table.php
```

**Aggiunge:** Colonna per elaborazioni iterative

---

## 🔧 COMANDI DA ESEGUIRE SU STAGING

### **Step 1: Backup Database**

```bash
# SEMPRE fare backup prima di migrations!
php artisan backup:run
# O manualmente:
mysqldump -u username -p database_name > backup_$(date +%Y%m%d_%H%M%S).sql
```

### **Step 2: Git Pull**

```bash
cd /path/to/egi/on/staging
git pull origin main
```

### **Step 3: Composer & NPM**

```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
```

### **Step 4: Esegui Migrations**

```bash
php artisan migrate
```

### **Step 5: Clear Cache**

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan optimize
```

### **Step 6: Verifica Permissions**

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

---

## 🤖 GENERAZIONE EMBEDDINGS

Dopo il deploy, **devi generare gli embeddings** per gli atti esistenti:

### **Opzione A: Da Command Line (raccomandato)**

```bash
# Genera embeddings per tutti gli atti
php artisan pa:generate-embeddings

# Con limite (per testare prima)
php artisan pa:generate-embeddings --limit=100

# Salta atti già processati
php artisan pa:generate-embeddings --skip-existing
```

**Tempo stimato:** ~200ms per atto

-   100 atti: ~20 secondi
-   1000 atti: ~3-4 minuti
-   10000 atti: ~30-40 minuti

### **Opzione B: Da Interfaccia Web**

```
1. Login come PA Admin
2. Vai su: /pa/embeddings
3. Click "Genera per 100 atti" (test)
4. Se OK, click "Genera per tutti"
```

**NOTA:** La generazione embeddings è **essenziale** per il semantic search di N.A.T.A.N.!

---

## 🧪 TEST POST-DEPLOY

### **Test 1: Homepage Caricamento**

```
URL: https://staging.egi.it
Expected: Homepage si carica senza errori
```

### **Test 2: Login PA**

```
URL: https://staging.egi.it/pa/login
Expected: Login funzionante
```

### **Test 3: N.A.T.A.N. Chat**

```
1. Vai su: /pa/natan/chat
2. Scrivi: "Suggerimenti strategie mobilità urbana"
3. Expected: Risposta AI entro 3-5 secondi
4. Expected: Mostra persona (es. 🎯 Consulente Strategico)
5. Expected: Mostra fonti (elenco atti)
```

### **Test 4: Elaborazioni**

```
1. Dopo risposta N.A.T.A.N., click "💡 Semplifica"
2. Expected: Nuova risposta semplificata
3. Expected: Badge "📄 Analisi originale elaborata"
```

### **Test 5: Free Chat**

```
1. Scroll giù a sezione "Free Chat with AI"
2. Scrivi: "Cos'è la strategia SWOT?"
3. Expected: Risposta Claude generale (senza fonti)
```

### **Test 6: Persona Selector**

```
1. Nel sidebar, seleziona "⚙️ Tecnico"
2. Scrivi: "Come digitalizzare l'ente?"
3. Expected: Risposta da persona Tecnico
```

---

## 🐛 TROUBLESHOOTING

### **Problema: "Class 'Anthropic' not found"**

**Soluzione:**

```bash
composer dump-autoload
php artisan config:clear
```

### **Problema: "OPENAI_API_KEY not set"**

**Soluzione:**

```bash
# Aggiungi al .env:
OPENAI_API_KEY=sk-...
php artisan config:clear
```

### **Problema: "No embeddings found"**

**Soluzione:**

```bash
# Genera embeddings:
php artisan pa:generate-embeddings --limit=100
```

### **Problema: "500 Internal Server Error"**

**Soluzione:**

```bash
# Controlla logs:
tail -f storage/logs/laravel.log

# Permissions:
chmod -R 775 storage
chown -R www-data:www-data storage
```

### **Problema: N.A.T.A.N. risponde lentamente (>10s)**

**Possibili cause:**

1. Troppi atti senza embeddings → Fallback a keyword search (lento)
2. API Anthropic slow → Verifica status API
3. Database non ottimizzato → Aggiungi indici

**Soluzione:**

```bash
# 1. Genera embeddings
php artisan pa:generate-embeddings

# 2. Ottimizza database
php artisan db:optimize

# 3. Controlla indici:
# - pa_act_embeddings.egi_id (UNIQUE)
# - egis.pa_entity_id (INDEX)
# - natan_chat_messages.user_id (INDEX)
```

---

## 📊 DEMO DATA (Per Presentazione)

### **Domande Strategiche Pre-Preparate**

Per fare bella figura durante la presentazione, usa queste domande:

1. **Mobilità Sostenibile:**

    ```
    "Suggerimenti sulle strategie da adottare per migliorare la mobilità urbana sostenibile"
    ```

    Expected persona: 🏙️ Urbano/Sociale

2. **Governance Digitale:**

    ```
    "Come implementare una strategia di trasformazione digitale dell'ente pubblico?"
    ```

    Expected persona: ⚙️ Tecnico

3. **Ottimizzazione Budget:**

    ```
    "Analisi costi-benefici per un progetto di smart city. Quali KPI monitorare?"
    ```

    Expected persona: 💰 Finanziario

4. **Compliance Normativa:**

    ```
    "Verificare la conformità degli appalti pubblici alle normative europee 2024"
    ```

    Expected persona: ⚖️ Legale

5. **Change Management:**
    ```
    "Come comunicare efficacemente ai cittadini una riforma amministrativa?"
    ```
    Expected persona: 📢 Comunicazione

### **Elaborazioni da Dimostrare**

Dopo la prima risposta, clicca:

-   **💡 Semplifica** → "Wow, ora anche i cittadini capiscono!"
-   **🔍 Approfondisci** → "Dettaglio tecnico impressionante"
-   **✅ Azioni concrete** → "Piano operativo pronto"
-   **📊 Per presentazione** → "Slide executive-ready"

---

## 🎭 SCRIPT PRESENTAZIONE

### **Intro (1 min)**

```
"Oggi vi presento N.A.T.A.N., il sistema di consulenza strategica AI
per la Pubblica Amministrazione. Gestisce oltre 24.000 atti amministrativi
e fornisce analisi di livello McKinsey/BCG in tempo reale."
```

### **Demo 1: Semantic Search (2 min)**

```
1. "Facciamo una domanda complessa sulla mobilità urbana..."
2. [Scrivi query mobilità]
3. "In 3 secondi, N.A.T.A.N. ha analizzato 24.000 atti..."
4. "Mostra le fonti specifiche utilizzate (12 atti rilevanti)"
5. "E fornisce un'analisi strategica strutturata"
```

### **Demo 2: Multi-Persona (2 min)**

```
1. "N.A.T.A.N. non è un singolo AI, ma 6 consulenti specializzati"
2. [Mostra sidebar persona selector]
3. "Routing intelligente: per questa query ha scelto il Consulente Urbano"
4. "Confidence: 95%. Reasoning: keyword 'mobilità' + 'città'"
5. [Cambia manualmente a Tecnico]
6. "Stessa domanda, prospettiva tecnologica diversa"
```

### **Demo 3: Iterative Elaboration (2 min)** ⭐ **WOW FACTOR**

```
1. "Ma il vero game-changer è l'elaborazione iterativa"
2. "Questa risposta è molto tecnica..."
3. [Click "💡 Semplifica"]
4. "In 1 secondo, Claude rielabora per un pubblico non esperto"
5. [Click "📊 Per presentazione"]
6. "Ora ho le slide pronte per il Consiglio Comunale!"
```

### **Demo 4: Free Chat (1 min)**

```
1. "E se ho bisogno di consulenza generale?"
2. [Scroll a Free Chat]
3. "Chat libera con Claude, senza vincoli di atti"
4. "Perfetto per brainstorming e domande esplorative"
```

### **Conclusione (1 min)**

```
"N.A.T.A.N. è 2-3 anni avanti rispetto alle soluzioni RAG tradizionali.
- Vector embeddings scalabili
- Multi-persona con routing intelligente
- Elaborazioni iterative
- Qualità consulting firm

Pronto per essere un vero SaaS multi-tenant.
Grazie!"
```

---

## 📞 CONTATTI SUPPORTO

**Se qualcosa non funziona durante la presentazione:**

1. **Refresh page** (spesso risolve problemi di cache frontend)
2. **Check logs:** `tail -f storage/logs/laravel.log`
3. **Restart services:**
    ```bash
    sudo systemctl restart php8.2-fpm
    sudo systemctl restart nginx
    ```

---

## ✅ CHECKLIST FINALE PRE-PRESENTAZIONE

La sera prima della presentazione:

-   [ ] Staging accessible da browser
-   [ ] Login PA funzionante
-   [ ] Almeno 100 atti con embeddings generati
-   [ ] N.A.T.A.N. risponde in <5s
-   [ ] Tutte e 6 le personas funzionanti
-   [ ] Elaborazioni quick actions funzionano
-   [ ] Free Chat funziona
-   [ ] Domande demo testate e funzionanti
-   [ ] Browser cache cleared
-   [ ] API keys verificate e attive
-   [ ] Backup database recente disponibile
-   [ ] Contatto tecnico disponibile durante presentazione

---

**Good luck! 🍀 You got this! 💪**
