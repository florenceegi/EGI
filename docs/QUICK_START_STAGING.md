# ⚡ Quick Start - Deploy Staging per Presentazione

> **TL;DR** - Passi essenziali per far funzionare N.A.T.A.N. su staging

---

## 🎯 PREREQUISITI

**HAI GIÀ:**

-   ✅ Codice committato localmente
-   ✅ Migrations create
-   ✅ Features complete (multi-persona, elaborazioni, free chat)

**TI SERVONO:**

1. 🔑 **OpenAI API Key** (per embeddings)
2. 🔑 **Anthropic API Key** (per Claude/N.A.T.A.N.)
3. 🖥️ **Accesso SSH al server staging**

---

## 🚀 PROCEDURA RAPIDA (15 minuti)

### **Step 1: Connettiti a Staging** (1 min)

```bash
ssh user@staging.egi.it
cd /path/to/egi
```

### **Step 2: Aggiungi API Keys al .env** (2 min)

```bash
nano .env
```

**Aggiungi queste righe** (sostituisci con le tue API keys):

```env
OPENAI_API_KEY=sk-proj-XXXXX
OPENAI_BASE_URL=https://api.openai.com/v1
OPENAI_EMBEDDING_MODEL=text-embedding-ada-002

ANTHROPIC_API_KEY=sk-ant-XXXXX
ANTHROPIC_BASE_URL=https://api.anthropic.com
ANTHROPIC_MODEL=claude-3-5-sonnet-20241022
ANTHROPIC_MAX_TOKENS=4096
```

**Salva:** Ctrl+X → Y → Enter

### **Step 3: Deploy** (5 min)

```bash
# Pull codice
git pull origin main

# Installa dipendenze
composer install --no-dev --optimize-autoloader
npm install
npm run build

# Esegui migrations
php artisan migrate --force

# Clear cache
php artisan config:clear
php artisan cache:clear
php artisan optimize
```

### **Step 4: Genera Embeddings** (5 min)

**Opzione A - Prima 100 atti (test veloce):**

```bash
php artisan pa:generate-embeddings --limit=100
```

**Opzione B - Tutti gli atti:**

```bash
php artisan pa:generate-embeddings
```

Tempo: ~200ms per atto (100 atti = ~20 secondi)

### **Step 5: Test** (2 min)

**Apri browser:**

```
https://staging.egi.it/pa/natan/chat
```

**Scrivi una domanda:**

```
Suggerimenti strategie mobilità urbana sostenibile
```

**Expected:**

-   ✅ Risposta in 3-5 secondi
-   ✅ Mostra persona (es. 🎯 Consulente Strategico)
-   ✅ Mostra fonti (lista atti)
-   ✅ Quick actions (💡 Semplifica, 🔍 Approfondisci, etc.)

---

## 🐛 SE QUALCOSA NON FUNZIONA

### **Problema 1: "OPENAI_API_KEY not set"**

```bash
# Verifica .env
cat .env | grep OPENAI

# Se manca, aggiungi:
echo "OPENAI_API_KEY=sk-..." >> .env
php artisan config:clear
```

### **Problema 2: "No embeddings found"**

```bash
# Genera embeddings:
php artisan pa:generate-embeddings --limit=100

# Verifica nel database:
php artisan tinker
>>> \App\Models\PaActEmbedding::count()
```

### **Problema 3: "500 Error"**

```bash
# Guarda i logs:
tail -f storage/logs/laravel.log

# Permissions:
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R www-data:www-data storage bootstrap/cache
```

### **Problema 4: N.A.T.A.N. lentissimo (>10s)**

```bash
# Probabilmente mancano embeddings → Semantic search fallisce → Fallback keyword search
php artisan pa:generate-embeddings
```

---

## 📊 MIGRATIONS CRITICHE

Queste 3 migrations **DEVONO** essere eseguite su staging:

1. **`2025_10_23_182606_create_pa_act_embeddings_table.php`**
    - Crea tabella vector embeddings
2. **`2025_10_23_201039_create_natan_chat_messages_table.php`**
    - Crea tabella chat history
3. **`2025_10_23_213900_add_reference_message_id_to_natan_chat_messages_table.php`**
    - Aggiunge colonna per elaborazioni iterative

**Verifica:**

```bash
php artisan migrate:status | grep -E "(pa_act_embeddings|natan_chat_messages)"
```

---

## 🎭 DOMANDE DEMO PER PRESENTAZIONE

### **1. Mobilità Urbana** (Persona: 🏙️ Urbano/Sociale)

```
Suggerimenti sulle strategie da adottare per migliorare la mobilità urbana sostenibile
```

### **2. Trasformazione Digitale** (Persona: ⚙️ Tecnico)

```
Come implementare una strategia di trasformazione digitale dell'ente pubblico?
```

### **3. Analisi Finanziaria** (Persona: 💰 Finanziario)

```
Analisi costi-benefici per un progetto di smart city. Quali KPI monitorare?
```

### **4. Compliance** (Persona: ⚖️ Legale)

```
Verificare la conformità degli appalti pubblici alle normative europee 2024
```

### **5. Comunicazione** (Persona: 📢 Comunicazione)

```
Come comunicare efficacemente ai cittadini una riforma amministrativa?
```

**Poi dimostra le elaborazioni:**

-   Click **💡 Semplifica** → "Ora anche i cittadini capiscono!"
-   Click **📊 Per presentazione** → "Slide executive-ready!"

---

## ✅ CHECKLIST PRE-PRESENTAZIONE

**La sera prima:**

-   [ ] Staging accessible da browser (https://staging.egi.it)
-   [ ] Login PA funzionante
-   [ ] API keys configurate (OpenAI + Anthropic)
-   [ ] Migrations eseguite (3 migrations N.A.T.A.N.)
-   [ ] Embeddings generati (almeno 100 atti)
-   [ ] N.A.T.A.N. risponde in <5s
-   [ ] Tutte le 6 personas funzionano
-   [ ] Elaborazioni quick actions funzionano
-   [ ] Free Chat funziona
-   [ ] Domande demo testate
-   [ ] Browser cache cleared

**La mattina della presentazione:**

-   [ ] Test rapido: una domanda N.A.T.A.N. + una elaborazione
-   [ ] Backup database recente disponibile
-   [ ] Contatto tecnico disponibile (tu!)

---

## 📞 HELP!

Se durante la presentazione qualcosa non va:

1. **Refresh page** (F5)
2. **Check API keys:** `php artisan config:cache`
3. **Restart PHP-FPM:** `sudo systemctl restart php8.2-fpm`
4. **Logs:** `tail -f storage/logs/laravel.log`

---

## 🎬 SCRIPT PRESENTAZIONE (2 minuti WOW)

**Intro:**

```
"N.A.T.A.N. gestisce 24.000 atti amministrativi e fornisce
consulenza strategica AI di livello McKinsey in tempo reale."
```

**Demo 1 - Semantic Search:**

```
[Scrivi query mobilità]
"In 3 secondi ha analizzato 24k atti e trovato i 12 più rilevanti.
Analisi strategica strutturata con framework professionali."
```

**Demo 2 - Multi-Persona:**

```
[Mostra sidebar]
"6 consulenti specializzati. Routing automatico 95% confidence.
Stessa domanda, prospettiva tecnica vs strategica."
```

**Demo 3 - Elaborazioni ⭐ WOW:**

```
[Click "Semplifica"]
"1 secondo: rielaborato per cittadini non esperti."
[Click "Per presentazione"]
"Slide pronte per il Consiglio Comunale!"
```

**Chiusura:**

```
"2-3 anni avanti rispetto a RAG tradizionali.
Pronto per SaaS multi-tenant. Grazie!"
```

---

**🍀 Good luck! 💪 Spacca tutto!**
