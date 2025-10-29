# 🌐 Perplexity AI - Setup Guide

## 📋 Panoramica

Perplexity AI fornisce ricerca web potenziata da AI con citazioni automatiche. Integrata in N.A.T.A.N. per arricchire le risposte con:

-   ✅ Normative recenti e aggiornamenti legislativi
-   ✅ Best practices internazionali PA
-   ✅ Opportunità di finanziamento (bandi PNRR, EU, ecc.)
-   ✅ Trend tecnologici e innovazioni settoriali
-   ✅ Case studies e benchmark da altre PA

---

## 🔑 Step 1: Ottenere API Key

### 1.1 Registrazione

1. Vai su: https://www.perplexity.ai/
2. Click su **"API"** nel footer o vai direttamente a: https://www.perplexity.ai/settings/api
3. Crea account (se non ce l'hai) con email aziendale
4. Verifica email

### 1.2 Acquisto Crediti

1. Accedi al dashboard: https://www.perplexity.ai/settings/api
2. Vai su **"API Keys"** → **"Billing"**
3. Aggiungi credito (minimo €5-10 per testing)
    - **Costo indicativo**: ~$0.001-0.005 per ricerca (dipende dal modello)
    - **Budget consigliato iniziale**: €10 → ~2000-10000 ricerche

### 1.3 Generare API Key

1. Click su **"Create New Secret Key"**
2. Dai un nome descrittivo: `EGI-NATAN-Production`
3. **COPIA SUBITO** la chiave (non sarà più visibile!)
4. Formato: `pplx-xxxxxxxxxxxxxxxxxxxxxxxxxxxxx`

---

## ⚙️ Step 2: Configurazione .env

### 2.1 Aprire il file

```bash
cd /home/fabio/EGI
nano .env
```

### 2.2 Trovare sezione WEB SEARCH

Cerca la sezione:

```env
# ===================================
# WEB SEARCH / PERPLEXITY AI
# ===================================
```

### 2.3 Inserire la tua API Key

Sostituisci `your-perplexity-api-key-here` con la chiave copiata:

```env
PERPLEXITY_API_KEY=pplx-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

### 2.4 Configurazione Completa

```env
# ===================================
# WEB SEARCH / PERPLEXITY AI
# ===================================
WEB_SEARCH_ENABLED=true
WEB_SEARCH_PROVIDER=perplexity
WEB_SEARCH_MAX_RESULTS=5          # Numero risultati per ricerca
WEB_SEARCH_TIMEOUT=15              # Timeout richiesta (secondi)
WEB_SEARCH_CACHE_TTL=3600          # Cache 1 ora (evita chiamate duplicate)

# Perplexity API
PERPLEXITY_API_KEY=pplx-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
PERPLEXITY_BASE_URL=https://api.perplexity.ai
PERPLEXITY_MODEL=sonar-pro         # Recommended (bilanciato qualità/costo)
# Alternative models:
# - sonar: più economico, veloce
# - sonar-pro: bilanciato (default)
# - sonar-reasoning: massima accuratezza (più costoso)
PERPLEXITY_TIMEOUT=30
```

### 2.5 Salvare

-   **nano**: `CTRL+O` → `Enter` → `CTRL+X`
-   **vim**: `ESC` → `:wq` → `Enter`

---

## 🔄 Step 3: Applicare Configurazione

### 3.1 Pulire cache config

```bash
cd /home/fabio/EGI
php artisan config:clear
php artisan config:cache
```

### 3.2 Riavviare servizi (se in produzione)

```bash
php artisan queue:restart
sudo systemctl restart nginx  # se necessario
```

---

## ✅ Step 4: Test Funzionamento

### 4.1 Test da Console Laravel

```bash
cd /home/fabio/EGI
php artisan tinker
```

Poi esegui:

```php
// Test connessione Perplexity
$service = app(\App\Services\WebSearchService::class);
$results = $service->search('PNRR digitalizzazione PA 2024');
dd($results);
```

**Output atteso:**

```php
array:1 [
  "results" => array:5 [
    0 => array:3 [
      "title" => "PNRR Digitalizzazione PA: Bandi 2024"
      "url" => "https://..."
      "snippet" => "Nuovi bandi per..."
    ]
    // ... altri 4 risultati
  ]
]
```

### 4.2 Test dall'interfaccia N.A.T.A.N.

1. Apri chat N.A.T.A.N.: `/pa/natan/chat`
2. **Abilita Web Search**: Toggle blu in alto
3. Fai una domanda che richiede info recenti:
    ```
    Quali sono i nuovi bandi PNRR per la PA nel 2024?
    ```
4. **Verifica presenza fonti esterne** nella risposta (box blu "🌐 Fonti Web")

---

## 📊 Modelli Disponibili

### Consigliati per N.A.T.A.N.

| Modello                             | Velocità | Qualità    | Costo | Use Case                                   |
| ----------------------------------- | -------- | ---------- | ----- | ------------------------------------------ |
| `llama-3.1-sonar-small-128k-online` | ⚡⚡⚡   | ⭐⭐⭐     | $     | Ricerche veloci, domande semplici          |
| `sonar-pro`                         | ⚡⚡     | ⭐⭐⭐⭐⭐ | $$    | **CONSIGLIATO** - Ricerche PA complesse    |
| `llama-3.1-sonar-huge-128k-online`  | ⚡       | ⭐⭐⭐⭐⭐ | $$$   | Analisi approfondite, ricerche accademiche |

**Configurazione attuale**: `sonar-pro` (best balance)

Per cambiare modello, modifica in `.env`:

```env
PERPLEXITY_MODEL=llama-3.1-sonar-small-128k-online  # più economico
```

---

## 💰 Costi e Budget

### Stima Costi

-   **Small model**: ~$0.0005/ricerca
-   **Large model** (default): ~$0.002/ricerca
-   **Huge model**: ~$0.008/ricerca

### Budget Suggerito

-   **Testing** (1-2 settimane): €5-10
-   **Produzione leggera** (100 ricerche/giorno): €20/mese
-   **Produzione standard** (500 ricerche/giorno): €80/mese
-   **Produzione intensiva** (2000 ricerche/giorno): €300/mese

### Ottimizzazione Costi

La cache è **già attiva**:

```env
WEB_SEARCH_CACHE_TTL=3600  # 1 ora
```

-   Ricerche identiche nelle 24h → **NO costo aggiuntivo**
-   Esempio: 10 utenti chiedono "PNRR bandi 2024" → **1 sola chiamata API**

---

## 🛡️ Privacy & GDPR

### Dati Sanitizzati

Il sistema **NON invia mai** a Perplexity:

-   ❌ Contenuto documenti interni
-   ❌ Numeri protocollo specifici
-   ❌ Nomi di persone
-   ❌ Riferimenti interni PA

**Invia solo**: Keywords generiche sanitizzate

-   ✅ "bandi PNRR digitalizzazione"
-   ✅ "normativa sicurezza informatica 2024"
-   ✅ "best practices gestione progetti PA"

### Configurazione Privacy (già attiva)

In `config/services.php`:

```php
'sanitization' => [
    'remove_protocols' => true,       // Rimuove "protocollo 1234/2024"
    'remove_internal_refs' => true,   // Rimuove "determina 847/2024"
    'remove_names' => true,           // Rimuove nomi persone
    'remove_locations' => true,       // Rimuove luoghi specifici
    'max_keyword_length' => 100,
],
```

---

## 🔧 Troubleshooting

### Errore: "Invalid API Key"

```bash
# Verifica chiave in .env
grep PERPLEXITY_API_KEY /home/fabio/EGI/.env

# Rigenera cache
php artisan config:clear && php artisan config:cache
```

### Errore: "Insufficient credits"

-   Vai su https://www.perplexity.ai/settings/api
-   Controlla balance
-   Aggiungi credito se necessario

### Errore: "Timeout"

Aumenta timeout in `.env`:

```env
PERPLEXITY_TIMEOUT=60  # da 30 a 60 secondi
WEB_SEARCH_TIMEOUT=30  # da 15 a 30 secondi
```

### Debug Avanzato

Abilita log dettagliati in `.env`:

```env
LOG_LEVEL=debug
```

Poi controlla:

```bash
tail -f storage/logs/laravel.log | grep -i perplexity
```

---

## 📚 Risorse

-   **Documentazione ufficiale**: https://docs.perplexity.ai/
-   **Dashboard API**: https://www.perplexity.ai/settings/api
-   **Pricing**: https://www.perplexity.ai/settings/api#pricing
-   **Status**: https://status.perplexity.ai/

---

## 🚀 Next Steps

Una volta configurato:

1. ✅ Testa con domande normative recenti
2. ✅ Verifica citazioni nelle risposte (box "Fonti Web")
3. ✅ Monitora costi nel dashboard Perplexity
4. ✅ Configura alert budget (raccomandato)
5. ✅ Forma utenti su quando attivare Web Search toggle

---

**Domande?** Contatta il team DevOps o consulta la documentazione N.A.T.A.N.
