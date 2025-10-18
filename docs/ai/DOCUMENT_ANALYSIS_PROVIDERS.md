# Document Analysis Providers - Integration Guide

**Version:** 1.0.0  
**Author:** Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici  
**Date:** 2025-10-18  
**Project:** FlorenceEGI - N.A.T.A.N. Module

---

## 🎯 Overview

N.A.T.A.N. uses a **provider-agnostic architecture** for document analysis, enabling easy switching between AI providers without code changes.

### Supported Providers

| Provider   | Status        | Use Case                     | Cost |
| ---------- | ------------- | ---------------------------- | ---- |
| **Regex**  | ✅ Active     | Basic extraction, fallback   | Free |
| **AISURU** | 🔄 Stub Ready | Italian AI, AI Act compliant | TBD  |
| **Claude** | 📋 Planned    | High accuracy, expensive     | $$$  |
| **OpenAI** | 📋 Planned    | Balanced quality/cost        | $$   |
| **Ollama** | 📋 Planned    | Self-hosted, on-premise      | Free |

---

## 🏗️ Architecture

### Interface Contract

All providers implement `App\Contracts\DocumentAnalysisInterface`:

```php
interface DocumentAnalysisInterface
{
    public function analyzeDocument(string $text, string $documentType = 'pa_act'): array;
    public function healthCheck(): bool;
    public function getProviderName(): string;
    public function getProviderVersion(): string;
    public function supportsDocumentType(string $documentType): bool;
}
```

### Factory Pattern

Provider selection via `DocumentAnalyzerFactory`:

```php
// Get default provider from config
$analyzer = DocumentAnalyzerFactory::make();

// Get specific provider
$analyzer = DocumentAnalyzerFactory::make('aisuru');

// Get with automatic fallback
$analyzer = DocumentAnalyzerFactory::makeWithFallback();

// Analyze document
$metadata = $analyzer->analyzeDocument($text, 'pa_act');
```

---

## ⚙️ Configuration

### File: `config/document_analysis.php`

```php
return [
    // Default provider
    'default_provider' => env('DOCUMENT_ANALYZER_PROVIDER', 'regex'),

    // Fallback configuration
    'fallback_enabled' => env('DOCUMENT_ANALYSIS_FALLBACK', false),
    'fallback_provider' => env('DOCUMENT_ANALYSIS_FALLBACK_PROVIDER', 'regex'),

    'providers' => [
        'regex' => [...],
        'aisuru' => [...],
        // ...
    ],
];
```

### Environment Variables

`.env` configuration:

```bash
# Primary provider
DOCUMENT_ANALYZER_PROVIDER=regex

# Fallback (optional)
DOCUMENT_ANALYSIS_FALLBACK=false
DOCUMENT_ANALYSIS_FALLBACK_PROVIDER=regex

# AISURU Configuration (when ready)
AISURU_ENABLED=false
AISURU_API_URL=https://backend.memori.ai
AISURU_API_KEY=your_api_key_here
AISURU_MEMORI_ID=your_memori_id_here
AISURU_TIMEOUT=30
```

---

## 🔌 Provider Integration Guides

### 1. Regex Provider (Default)

**Status:** ✅ Active  
**No configuration needed** - works out of the box.

```bash
# .env
DOCUMENT_ANALYZER_PROVIDER=regex
```

**Pros:**

-   ✅ No API costs
-   ✅ Fast (< 1ms)
-   ✅ No external dependencies

**Cons:**

-   ❌ Limited accuracy (~60-80%)
-   ❌ Only basic pattern matching

---

### 2. AISURU Provider (Sparavigna)

**Status:** 🔄 Stub Ready - Awaiting API Documentation

#### Prerequisites

1. AISURU account from Sparavigna/memori.ai
2. API key and Memori ID
3. Access to API documentation

#### Configuration

```bash
# .env
DOCUMENT_ANALYZER_PROVIDER=aisuru
AISURU_ENABLED=true
AISURU_API_URL=https://backend.memori.ai
AISURU_API_KEY=sk_aisuru_xxxxxxxxxxxxx
AISURU_MEMORI_ID=your_memori_id_here
AISURU_TIMEOUT=30
```

#### Implementation Status

File: `app/Services/DocumentAnalysis/Providers/AisuruDocumentAnalyzer.php`

```php
// STUB READY - Awaiting:
// 1. Actual API endpoint for document analysis
// 2. Request/response format
// 3. Authentication method confirmation
// 4. Error handling specifics
```

#### What We Need from Sparavigna

1. **Endpoint:** Full URL for document analysis API
2. **Authentication:** Confirm Bearer token format
3. **Request Format:**
    ```json
    {
        "memori_id": "...",
        "text": "...",
        "document_type": "pa_act",
        "extract_fields": ["act_type", "protocol", "title"]
    }
    ```
4. **Response Format:** Expected JSON structure
5. **Rate Limits:** Requests per minute/day
6. **Pricing:** Cost per request or subscription model
7. **Sandbox:** Test environment with dummy credentials

#### Activation

Once API docs received:

1. Update `AisuruDocumentAnalyzer::analyzeDocument()` method
2. Update `AisuruDocumentAnalyzer::normalizeResponse()` mapping
3. Test with sample PA acts
4. Enable in `.env`: `AISURU_ENABLED=true`
5. Switch provider: `DOCUMENT_ANALYZER_PROVIDER=aisuru`

---

### 3. Claude Provider (Future)

**Status:** 📋 Planned

```bash
# .env
DOCUMENT_ANALYZER_PROVIDER=claude
CLAUDE_ENABLED=true
CLAUDE_API_KEY=sk-ant-xxxxxxxxxxxxx
CLAUDE_MODEL=claude-3-sonnet-20240229
CLAUDE_TIMEOUT=30
```

**Implementation:** `app/Services/DocumentAnalysis/Providers/ClaudeDocumentAnalyzer.php`

---

### 4. OpenAI Provider (Future)

**Status:** 📋 Planned

```bash
# .env
DOCUMENT_ANALYZER_PROVIDER=openai
OPENAI_ENABLED=true
OPENAI_API_KEY=sk-proj-xxxxxxxxxxxxx
OPENAI_MODEL=gpt-4-turbo
OPENAI_TIMEOUT=30
```

**Implementation:** `app/Services/DocumentAnalysis/Providers/OpenAIDocumentAnalyzer.php`

---

### 5. Ollama Provider (Future - Self-Hosted)

**Status:** 📋 Planned

```bash
# .env
DOCUMENT_ANALYZER_PROVIDER=ollama
OLLAMA_ENABLED=true
OLLAMA_API_URL=http://localhost:11434
OLLAMA_MODEL=llama3:70b
OLLAMA_TIMEOUT=60
```

**Requirements:**

-   Ollama installed on server
-   Llama 3 70B model downloaded (~40GB)
-   Sufficient GPU/CPU resources

**Implementation:** `app/Services/DocumentAnalysis/Providers/OllamaDocumentAnalyzer.php`

---

## 🧪 Testing

### Test Current Provider

```bash
php artisan tinker
```

```php
use App\Services\DocumentAnalysis\DocumentAnalyzerFactory;

// Get current provider
$analyzer = DocumentAnalyzerFactory::make();
echo $analyzer->getProviderName(); // e.g., "regex"

// Health check
var_dump($analyzer->healthCheck()); // true/false

// Test analysis
$text = "DELIBERA DI GIUNTA N. 123/2024\nOGGETTO: Test delibera";
$result = $analyzer->analyzeDocument($text);
dd($result);
```

### Switch Provider

```bash
# Edit .env
DOCUMENT_ANALYZER_PROVIDER=aisuru  # Change this

# Clear config cache
php artisan config:clear

# Test again
php artisan tinker
```

---

## 📊 Monitoring

### Provider Performance

Check logs for provider usage:

```bash
grep "DOCUMENT_ANALYSIS" storage/logs/laravel.log
```

### Dashboard Stats

PA Statistics dashboard shows:

-   Provider in use
-   Success rate
-   Average response time
-   Fallback activations

---

## 🚀 Quick Start: Adding New Provider

### Step 1: Create Provider Class

```php
// app/Services/DocumentAnalysis/Providers/MyProviderAnalyzer.php
namespace App\Services\DocumentAnalysis\Providers;

use App\Contracts\DocumentAnalysisInterface;

class MyProviderAnalyzer implements DocumentAnalysisInterface
{
    public function analyzeDocument(string $text, string $documentType = 'pa_act'): array
    {
        // Your implementation
    }

    // ... implement other interface methods
}
```

### Step 2: Add to Config

```php
// config/document_analysis.php
'providers' => [
    // ...
    'myprovider' => [
        'enabled' => env('MYPROVIDER_ENABLED', false),
        'api_key' => env('MYPROVIDER_API_KEY'),
        // ...
    ],
],
```

### Step 3: Register in Factory

```php
// app/Services/DocumentAnalysis/DocumentAnalyzerFactory.php
return match ($provider) {
    // ...
    'myprovider' => app(MyProviderAnalyzer::class),
    // ...
};
```

### Step 4: Configure & Test

```bash
# .env
DOCUMENT_ANALYZER_PROVIDER=myprovider
MYPROVIDER_ENABLED=true
MYPROVIDER_API_KEY=xxx

# Test
php artisan tinker
```

---

## 📞 Support

### For AISURU Integration

Contact: **Sparavigna Team**  
Website: https://www.aisuru.com / https://memori.ai  
Request: API documentation for document analysis endpoint

### For General Issues

Consult: `docs/ai/os3-rules.md`  
Architecture: Provider-agnostic by design (OS3 standard)

---

## ✅ Checklist: Production Deployment

-   [ ] Provider selected based on requirements (cost, accuracy, privacy)
-   [ ] API credentials configured in `.env`
-   [ ] Health check passing (`healthCheck()` returns true)
-   [ ] Test with 10+ sample PA acts
-   [ ] Compare accuracy vs. regex baseline
-   [ ] Fallback provider configured (if needed)
-   [ ] Monitoring alerts configured
-   [ ] Costs tracked (if using paid API)

---

**Document version:** 1.0.0  
**Last updated:** 2025-10-18  
**Maintained by:** FlorenceEGI Team
