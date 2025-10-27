# 🔧 N.A.T.A.N. Cost Tracking Fix + Provider Billing Comparison

**Data:** 2025-10-27  
**Problema:** Dashboard costi AI riportava $0 nonostante uso effettivo delle API  
**Soluzione:**

1. Implementato tracking tokens da Anthropic/OpenAI API
2. Creato servizio confronto billing interno vs provider API reale

---

## 📊 PROBLEMA IDENTIFICATO

### Sintomo

Dashboard `/pa/ai-costs` mostrava:

-   Spesa Totale: $0.00
-   0 richieste AI
-   Tutti i grafici vuoti

**Nonostante:**

-   ✅ Claude API funzionante
-   ✅ Chat N.A.T.A.N. operativa
-   ✅ Fatture Anthropic/OpenAI reali

### Root Cause Analysis

1. **Database OK**: Tabella `natan_chat_messages` ha campi `tokens_input` e `tokens_output` ✅
2. **Dashboard OK**: `AiCostCalculatorService` legge correttamente i campi tokens ✅
3. **API Anthropic ritorna usage**: Response JSON contiene `usage.input_tokens` e `usage.output_tokens` ✅
4. **❌ BUG TROVATO**: `AnthropicService::chat()` NON ritornava i tokens al chiamante!

**Flusso PRIMA della fix:**

```
Anthropic API Response
    ↓
{
  "content": [{"text": "Risposta..."}],
  "usage": {
    "input_tokens": 1234,    ← RICEVUTI DA API
    "output_tokens": 567     ← MA NON PASSATI AL CHIAMANTE!
  }
}
    ↓
AnthropicService::chat() ritorna solo:
"Risposta..."  ← SOLO STRINGA, NESSUN TOKEN!
    ↓
NatanChatService salva nel DB:
'tokens_input' => null   ← ❌ NULL!
'tokens_output' => null  ← ❌ NULL!
    ↓
AiCostCalculatorService legge:
$message->tokens_input ?? 0  → 0  ← SEMPRE 0!
    ↓
Dashboard mostra $0.00 ← ❌ SBAGLIATO!
```

---

## ✅ SOLUZIONE IMPLEMENTATA

### Files Modificati

1. **`app/Services/AnthropicService.php`**

    - Changed return type: `string` → `array`
    - Now returns: `['message' => string, 'usage' => array]`

2. **`app/Services/NatanChatService.php`**

    - Extract tokens from response
    - Save to database: `tokens_input`, `tokens_output`

3. **`app/Services/EgiPreMintManagementService.php`**

    - Updated to handle new array response

4. **`app/Services/Padmin/AiFixService.php`**
    - Updated to handle new array response

### Codice Chiave

**AnthropicService.php (PRIMA):**

```php
$data = $response->json();
$assistantMessage = $data['content'][0]['text'] ?? '';

$this->logger->info('[AnthropicService] Chat response received', [
    'usage' => $data['usage'] ?? null,  // ← Loggato ma NON ritornato!
]);

return $assistantMessage;  // ← Solo stringa!
```

**AnthropicService.php (DOPO):**

```php
$data = $response->json();
$assistantMessage = $data['content'][0]['text'] ?? '';
$usage = $data['usage'] ?? null;  // ← Estratto!

$this->logger->info('[AnthropicService] Chat response received', [
    'usage' => $usage,
]);

// Return both message and usage for cost tracking
return [
    'message' => $assistantMessage,  // ← Messaggio
    'usage' => $usage,               // ← TOKENS!
];
```

**NatanChatService.php (PRIMA):**

```php
$aiResponse = $this->anthropic->chat(...);

NatanChatMessage::create([
    'content' => $aiResponse,
    'tokens_input' => null,   // ← NULL!
    'tokens_output' => null,  // ← NULL!
]);
```

**NatanChatService.php (DOPO):**

```php
$aiResponseData = $this->anthropic->chat(...);
$aiResponse = $aiResponseData['message'] ?? $aiResponseData;
$usage = $aiResponseData['usage'] ?? null;

NatanChatMessage::create([
    'content' => $aiResponse,
    'tokens_input' => $usage['input_tokens'] ?? null,   // ← SALVATI!
    'tokens_output' => $usage['output_tokens'] ?? null, // ← SALVATI!
]);
```

---

## 🧪 TESTING

### Verifica Immediata

```bash
# 1. Fai una nuova query N.A.T.A.N.
curl -X POST http://localhost/pa/natan/chat \
  -H "Authorization: Bearer {token}" \
  -d '{"query": "Test tracking costi"}'

# 2. Controlla database
php artisan tinker
>>> $msg = \App\Models\NatanChatMessage::latest()->first();
>>> $msg->tokens_input  // Deve essere > 0
>>> $msg->tokens_output // Deve essere > 0

# 3. Controlla dashboard
# Vai su /pa/ai-costs
# Verifica che ora mostri:
#  - Spesa Totale > $0
#  - N richieste > 0
#  - Grafici popolati
```

### Test Completo

```bash
# 1. Pulisci dati vecchi (opzionale)
php artisan tinker
>>> \App\Models\NatanChatMessage::where('tokens_input', null)->delete();

# 2. Fai 5 query diverse
# 3. Refresh dashboard /pa/ai-costs
# 4. Verifica metriche:
#    - Total Cost ≈ $0.05-0.15 (5 query × ~$0.01-0.03)
#    - Total Messages = 5
#    - Avg Cost/Request ≈ $0.01-0.03
#    - Provider breakdown: Anthropic 100%
```

---

## 📈 IMPATTO

### Metriche Attese (esempio reale)

**Scenario: 100 query/mese**

| Metrica           | Valore                       |
| ----------------- | ---------------------------- |
| Query totali      | 100                          |
| Token input medi  | 1000/query                   |
| Token output medi | 500/query                    |
| Costo input       | (100k / 1M) × $3 = **$0.30** |
| Costo output      | (50k / 1M) × $15 = **$0.75** |
| **TOTALE MESE**   | **$1.05**                    |

**Dashboard NOW mostra:**

-   ✅ Spesa Totale: $1.05
-   ✅ 100 richieste AI
-   ✅ Avg Cost: $0.0105/request
-   ✅ Budget tracking: 1.05% di $100 budget

**Dashboard PRIMA mostrava:**

-   ❌ Spesa Totale: $0.00 (SBAGLIATO!)
-   ❌ 0 richieste (SBAGLIATO!)

---

## 🎯 BENEFICI

1. **Visibility finanziaria REALE**

    - Costi Anthropic/OpenAI tracciati accuratamente
    - Dashboard allineata a fatture effettive

2. **Budget Monitoring**

    - Alert automatici quando si supera threshold
    - Prevenzione overspending

3. **Cost Optimization**

    - Identificazione query costose
    - Trend analysis per ridurre costi

4. **Compliance Audit**
    - Log completo API usage
    - Reportistica per stakeholder PA

---

## 🚀 DEPLOYMENT

### Checklist

-   [x] Codice modificato
-   [x] Backward compatibility garantita (controllo `?? $aiResponseData`)
-   [x] Tutti i chiamanti di `chat()` aggiornati
-   [x] Testing locale completato
-   [ ] **Deploy su staging**
-   [ ] **Verifica dashboard staging**
-   [ ] **Deploy su production**

### Post-Deploy

1. Monitor logs per errori:

    ```bash
    tail -f storage/logs/laravel.log | grep -i "anthropic\|natan\|cost"
    ```

2. Verifica prime query salvano tokens:

    ```sql
    SELECT id, tokens_input, tokens_output, created_at
    FROM natan_chat_messages
    WHERE created_at > NOW() - INTERVAL 1 HOUR
    ORDER BY created_at DESC LIMIT 10;
    ```

3. Dashboard health check:
    - Accedi `/pa/ai-costs`
    - Verifica numeri realistici
    - Confronta con fatture Anthropic

---

## 📝 BACKWARD COMPATIBILITY

Il codice include fallback per compatibilità:

```php
$aiResponse = $aiResponseData['message'] ?? $aiResponseData;
```

**Se AnthropicService ritorna:**

-   `array` (NEW) → Estrae `message` e `usage` ✅
-   `string` (OLD, non dovrebbe più accadere) → Usa direttamente ✅

**Zero breaking changes!**

---

## � FASE 2: PROVIDER BILLING COMPARISON

**NUOVO (2025-10-27):** Confronto automatico tracking interno vs API provider reali.

### **Sistema implementato:**

1. **AiProviderBillingService** - Fetcha billing data da:

    - ✅ OpenAI Usage API (automatico)
    - ⚠️ Anthropic Console (manuale - API non disponibile)
    - ❌ Perplexity (non implementato)

2. **Confronto automatico:**

    - Legge tracking interno (DB)
    - Fetcha billing da provider API
    - Calcola discrepanza %
    - Alert se > 5%

3. **Endpoint API:**

    ```
    GET /pa/ai-costs/api/compare-billing?provider=openai
    ```

4. **Test script:**
    ```bash
    ./bash_files/test-ai-billing-comparison.sh
    ```

### **Documentazione completa:**

Vedi: `docs/ai/context/AI_PROVIDER_BILLING_COMPARISON.md`

**Features:**

-   Real-time billing sync con OpenAI
-   Discrepancy alerts (> 5%)
-   Cache 1 ora
-   GDPR compliant (solo aggregati)

---

## �🔗 FILES CORRELATI

**Core Services:**

-   `app/Services/AnthropicService.php` - API client Anthropic
-   `app/Services/NatanChatService.php` - Chat logic N.A.T.A.N.
-   `app/Services/EmbeddingService.php` - OpenAI embeddings (token tracking)
-   `app/Services/AI/AiCostCalculatorService.php` - Cost calculations
-   `app/Services/AI/AiProviderBillingService.php` - **NEW** Billing comparison

**Controllers:**

-   `app/Http/Controllers/PA/AiCostsDashboardController.php` - Dashboard + API

**Views:**

-   `resources/views/pa/ai-costs/dashboard.blade.php` - Dashboard UI

**Database:**

-   `database/migrations/2025_10_23_201039_create_natan_chat_messages_table.php` - Schema

**Documentation:**

-   `docs/ai/context/AI_PROVIDER_BILLING_COMPARISON.md` - Billing comparison system
-   `bash_files/test-natan-cost-tracking.sh` - Test tracking interno
-   `bash_files/test-ai-billing-comparison.sh` - Test billing comparison

---

**Autore**: Padmin D. Curtis (AI Partner OS3.0)  
**Reviewed by**: Fabio Cherici  
**Status**: ✅ IMPLEMENTED & TESTED
