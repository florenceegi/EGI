# AI Provider Billing Comparison System

**Version**: 1.0.0  
**Date**: 2025-10-27  
**Author**: Padmin D. Curtis (AI Partner OS3.0)  
**Context**: FlorenceEGI - PA Enterprise AI Cost Monitoring

---

## 📋 OVERVIEW

Sistema che confronta il tracking interno dei costi AI (database) con i dati reali forniti dalle API dei provider:

-   **Anthropic**: Console billing (manuale per ora, API non disponibile)
-   **OpenAI**: Usage API (automatico)
-   **Perplexity**: Non implementato (API non disponibile)

**SCOPO:**

-   Verificare accuratezza tracking interno
-   Identificare discrepanze > 5%
-   Alert automatici su differenze significative
-   Audit trail per compliance PA/Enterprise

---

## 🚀 QUICK START

### **1. Configurazione .env**

```bash
# OpenAI API key (obbligatoria)
OPENAI_API_KEY=sk-proj-...

# Anthropic API key (già presente per chat)
ANTHROPIC_API_KEY=sk-ant-...
```

### **2. Test Sistema**

```bash
./bash_files/test-ai-billing-comparison.sh
```

Output atteso:

```
✓ OpenAI billing API working
  Total cost: $12.45
  Total requests: 342

✓ Comparison successful
  Internal cost: $12.30
  OpenAI API cost: $12.45
  Discrepancy: 1.2%
  Status: OK
```

### **3. Accesso Dashboard**

```
URL: http://localhost/pa/ai-costs
Pulsante: "Compare with Provider API"
```

---

## 🏗️ ARCHITETTURA

### **Componenti Principali**

```
┌─────────────────────────────────────────────────┐
│         AiProviderBillingService                │
│  (app/Services/AI/AiProviderBillingService.php) │
└───────────────┬─────────────────────────────────┘
                │
                ├─► getOpenAIBilling()      → Fetch OpenAI API
                ├─► getAnthropicBilling()   → Placeholder (manuale)
                ├─► compareBilling()        → Confronta per provider
                └─► getAllBillingComparison() → Confronta tutti

┌─────────────────────────────────────────────────┐
│      AiCostsDashboardController                 │
│ (app/Http/Controllers/PA/AiCostsDashboardController.php) │
└───────────────┬─────────────────────────────────┘
                │
                └─► compareBilling()        → API endpoint

┌─────────────────────────────────────────────────┐
│            Frontend Dashboard                   │
│  (resources/views/pa/ai-costs/dashboard.blade.php) │
└───────────────┬─────────────────────────────────┘
                │
                └─► Fetch /pa/ai-costs/api/compare-billing
```

### **Flusso Dati**

```
1. User clicks "Compare Billing" button
2. Frontend → GET /pa/ai-costs/api/compare-billing
3. Controller → AiProviderBillingService::getAllBillingComparison()
4. Service → OpenAI Usage API (https://api.openai.com/v1/usage)
5. Service → AiCostCalculatorService (internal tracking)
6. Service → Confronta costi
7. Service → Calcola discrepancy %
8. Service → Status: OK | WARNING (> 5%)
9. Controller → JSON response
10. Frontend → Mostra risultati
```

---

## 📊 API ENDPOINTS

### **GET /pa/ai-costs/api/compare-billing**

Confronta billing interno con API providers.

**Query Parameters:**

```
?provider=openai    → Confronta solo OpenAI
?provider=anthropic → Confronta solo Anthropic (manuale per ora)
(omesso)            → Confronta tutti i provider
```

**Response:**

```json
{
    "success": true,
    "comparison": {
        "providers": {
            "openai": {
                "success": true,
                "provider": "openai",
                "internal": {
                    "cost": 12.3,
                    "requests": 340,
                    "tokens": 1250000
                },
                "provider_api": {
                    "cost": 12.45,
                    "requests": 342
                },
                "comparison": {
                    "discrepancy_usd": 0.15,
                    "discrepancy_percentage": 1.2,
                    "status": "OK",
                    "message": "Tracking accurate (discrepancy < 5%)"
                }
            },
            "anthropic": {
                "success": false,
                "error": "Provider billing not available"
            }
        },
        "summary": {
            "total_providers_checked": 2,
            "providers_with_alerts": 0
        }
    }
}
```

**Status Codes:**

-   `200` - Success
-   `500` - Internal error

---

## 🔍 METODI SERVIZIO

### **AiProviderBillingService**

#### **getOpenAIBilling(): array**

Fetcha billing data da OpenAI Usage API per il mese corrente.

**Return:**

```php
[
    'success' => true,
    'provider' => 'OpenAI',
    'period' => [
        'start' => '2025-10-01',
        'end' => '2025-10-27',
    ],
    'total_cost' => 12.45,      // USD
    'total_requests' => 342,
    'raw_data' => [...],        // Full API response
]
```

**Cache:** 1 ora (key: `openai_billing_YYYY-MM`)

**Error Handling:**

-   API key mancante → `'success' => false, 'error' => 'API key not configured'`
-   API error → `'success' => false, 'error' => 'API error: 401'`
-   Exception → `'success' => false, 'error' => 'Exception message'`

---

#### **getAnthropicBilling(): ?array**

**NOTA:** Anthropic NON fornisce API billing pubblica (as of 2025-10-27).

**Return:** `null`

**Manual Check:** https://console.anthropic.com/settings/billing

**Implementazione futura:**
Quando Anthropic rilascerà API billing, implementare come `getOpenAIBilling()`.

---

#### **compareBilling(string $provider): array**

Confronta tracking interno con API provider per un singolo provider.

**Parameters:**

-   `$provider` - `'openai'`, `'anthropic'`, `'perplexity'`

**Return:**

```php
[
    'success' => true,
    'provider' => 'openai',
    'internal' => [
        'cost' => 12.30,
        'requests' => 340,
        'tokens' => 1250000,
    ],
    'provider_api' => [
        'cost' => 12.45,
        'requests' => 342,
    ],
    'comparison' => [
        'discrepancy_usd' => 0.15,
        'discrepancy_percentage' => 1.2,
        'status' => 'OK',          // OK | WARNING
        'message' => 'Tracking accurate (discrepancy < 5%)',
    ],
]
```

**Status Logic:**

-   Discrepancy ≤ 5% → `'status' => 'OK'`
-   Discrepancy > 5% → `'status' => 'WARNING'`

---

#### **getAllBillingComparison(): array**

Confronta tutti i provider (OpenAI, Anthropic).

**Return:**

```php
[
    'success' => true,
    'period' => 'current_month',
    'providers' => [
        'openai' => [...],      // compareBilling('openai')
        'anthropic' => [...],   // compareBilling('anthropic')
    ],
    'summary' => [
        'total_providers_checked' => 2,
        'providers_with_alerts' => 0,
    ],
]
```

---

## ⚙️ CONFIGURAZIONE

### **OpenAI Usage API**

**Documentazione:** https://platform.openai.com/docs/api-reference/usage

**Endpoint:** `GET https://api.openai.com/v1/usage`

**Query Parameters:**

-   `date` - Data inizio periodo (YYYY-MM-DD), default: start of current month

**Response:**

```json
{
  "data": [
    {
      "date": "2025-10-01",
      "usage": [
        {
          "model": "text-embedding-ada-002",
          "n_requests": 120,
          "cost": 0.15
        }
      ]
    },
    ...
  ]
}
```

**Rate Limits:**

-   Tier 1: 500 requests/day
-   Tier 2: 3,500 requests/day

**Note:**

-   Dati disponibili con delay fino a 24 ore
-   Aggregazione giornaliera (non real-time)

---

### **Anthropic Billing (Manual)**

**Console:** https://console.anthropic.com/settings/billing

**API:** Non disponibile (as of 2025-10-27)

**Workaround attuale:**

1. Login console Anthropic
2. Vai a Settings → Billing
3. Leggi "Usage this month"
4. Confronta manualmente con dashboard FlorenceEGI

**Future Implementation:**
Quando Anthropic rilascerà API:

```php
$response = Http::withHeaders([
    'x-api-key' => config('services.anthropic.api_key'),
])
    ->get('https://api.anthropic.com/v1/billing/usage', [
        'start_date' => '2025-10-01',
        'end_date' => '2025-10-31',
    ]);
```

---

## 🧪 TESTING

### **Test Completo**

```bash
./bash_files/test-ai-billing-comparison.sh
```

### **Test Manuale OpenAI**

```bash
php artisan tinker
```

```php
$service = app(\App\Services\AI\AiProviderBillingService::class);

// Test OpenAI billing fetch
$billing = $service->getOpenAIBilling();
dd($billing);

// Test comparison
$comparison = $service->compareBilling('openai');
dd($comparison);

// Test all providers
$all = $service->getAllBillingComparison();
dd($all);
```

### **Test API Endpoint**

```bash
# Tutti i provider
curl -H "Cookie: laravel_session=..." \
  http://localhost/pa/ai-costs/api/compare-billing

# Solo OpenAI
curl -H "Cookie: laravel_session=..." \
  http://localhost/pa/ai-costs/api/compare-billing?provider=openai
```

---

## 🔒 SECURITY & GDPR

### **API Key Protection**

```php
// ✅ CORRETTO - API key in .env
$apiKey = config('services.openai.api_key');

// ❌ SBAGLIATO - API key hardcoded
$apiKey = 'sk-proj-hardcoded';
```

### **Rate Limiting**

OpenAI Usage API è limitato:

-   Cache response per 1 ora
-   Max 1 request/ora per dashboard
-   Prevent abuse con `Cache::remember()`

### **GDPR Compliance**

-   ✅ NO PII nei billing data (solo aggregati)
-   ✅ Solo admin PA accedono a dashboard
-   ✅ Middleware `auth` + `role:pa_entity`
-   ✅ Audit trail tramite UltraLogManager

---

## 🚨 ALERT SYSTEM

### **Discrepancy Alert (> 5%)**

Quando discrepanza > 5%, sistema logga WARNING:

```php
$this->logger->warning('[AiProviderBilling] High discrepancy detected', [
    'provider' => 'openai',
    'internal_cost' => 10.00,
    'provider_cost' => 12.00,
    'discrepancy_percentage' => 20.0,
]);
```

### **Future Enhancements**

-   Email alert a admin PA
-   Slack notification
-   Toast notification in dashboard
-   Monthly report PDF con discrepanze

---

## 📈 METRICS & MONITORING

### **Logs (UltraLogManager)**

```
[AiProviderBilling] Fetching OpenAI billing
[AiProviderBilling] OpenAI billing fetched: {total_cost: 12.45, total_requests: 342}
[AiProviderBilling] Comparing billing: {provider: openai}
[AiProviderBilling] Comparison completed: {discrepancy_percentage: 1.2, status: OK}
```

### **Database Tracking**

Tracking interno usa:

-   `natan_chat_messages` table (tokens_input, tokens_output, ai_model)
-   `AiCostCalculatorService` calcola costi da tokens

**Query esempio:**

```sql
SELECT
    ai_model,
    SUM(tokens_input) as total_input,
    SUM(tokens_output) as total_output,
    COUNT(*) as messages
FROM natan_chat_messages
WHERE created_at >= '2025-10-01'
GROUP BY ai_model;
```

---

## 🐛 TROUBLESHOOTING

### **OpenAI API returns 401**

**Causa:** API key invalida o scaduta

**Fix:**

```bash
# Check .env
grep OPENAI_API_KEY .env

# Test key manually
curl https://api.openai.com/v1/usage \
  -H "Authorization: Bearer YOUR_API_KEY"
```

---

### **OpenAI API returns empty data**

**Causa:** Nessun usage nel periodo richiesto

**Fix:** Genera embeddings per testare:

```php
$service = app(\App\Services\EmbeddingService::class);
$service->generateForAct($act);
```

Poi ri-testa billing dopo 24h (delay API).

---

### **Discrepancy > 5%**

**Possibili cause:**

1. **Delay API** - OpenAI ha ritardo fino a 24h
2. **Missing tracking** - Embeddings non tracciati (fixed in v1.0)
3. **Cache issue** - Cache interno non aggiornato

**Debug:**

```bash
# Check internal tracking
php artisan tinker
```

```php
$calc = app(\App\Services\AI\AiCostCalculatorService::class);
$stats = $calc->getCurrentMonthSpending();
dd($stats['by_provider']);
```

**Clear cache:**

```php
Cache::forget('openai_billing_' . now()->format('Y-m'));
```

---

### **Anthropic billing not available**

**Expected behavior:** Anthropic non fornisce API billing.

**Workaround:**

1. Login: https://console.anthropic.com/settings/billing
2. Leggi "Usage this month"
3. Confronta manualmente con dashboard

---

## 📚 RELATED DOCUMENTATION

-   [NATAN_COST_TRACKING_FIX.md](./NATAN_COST_TRACKING_FIX.md) - Fix tracking tokens Anthropic
-   [PA_ENTERPRISE_TODO_MASTER.md](./PA_ENTERPRISE_TODO_MASTER.md) - Project tracking
-   [OpenAI Usage API Docs](https://platform.openai.com/docs/api-reference/usage)
-   [Anthropic Console Billing](https://console.anthropic.com/settings/billing)

---

## 🎯 FUTURE ENHANCEMENTS

### **Phase 1 (Current)**

-   ✅ OpenAI billing fetch automatico
-   ✅ Confronto con tracking interno
-   ✅ Alert discrepanze > 5%
-   ⚠️ Anthropic billing manuale

### **Phase 2 (Future)**

-   [ ] Anthropic billing API (quando disponibile)
-   [ ] Perplexity AI billing API
-   [ ] Email alert automatici
-   [ ] Slack webhook notifications
-   [ ] Historical trend comparison
-   [ ] Cost optimization suggestions

### **Phase 3 (Advanced)**

-   [ ] Machine learning anomaly detection
-   [ ] Predictive cost forecasting
-   [ ] Budget auto-scaling
-   [ ] Multi-tenant cost allocation

---

## ✅ CHECKLIST IMPLEMENTAZIONE

**Backend:**

-   [x] `AiProviderBillingService` creato
-   [x] `compareBilling()` endpoint aggiunto
-   [x] Route `/pa/ai-costs/api/compare-billing` registrata
-   [x] OpenAI Usage API integrato
-   [x] Cache implementato (1 ora)
-   [x] Error handling completo
-   [x] Logging ULM integrato

**Testing:**

-   [x] Script `test-ai-billing-comparison.sh` creato
-   [x] Test OpenAI billing fetch
-   [x] Test comparison logic
-   [x] Test all providers

**Documentazione:**

-   [x] Questo file (AI_PROVIDER_BILLING_COMPARISON.md)
-   [x] Commenti docblock completi
-   [x] README references aggiornati

**Frontend (TODO):**

-   [ ] Pulsante "Compare Billing" in dashboard
-   [ ] Modal con risultati comparison
-   [ ] Warning badge per discrepanze
-   [ ] Link a console providers

---

## 📞 SUPPORT

**Per problemi con:**

-   OpenAI API → https://help.openai.com/
-   Anthropic API → https://support.anthropic.com/
-   FlorenceEGI billing → Controlla docs/ai/context/

---

**Version History:**

-   `1.0.0` (2025-10-27) - Initial release con OpenAI billing fetch

---

© 2025 FlorenceEGI - AI Cost Monitoring System
