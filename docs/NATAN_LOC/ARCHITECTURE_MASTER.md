# NATAN_LOC - ARCHITETTURA MASTER (UNICA VERITÀ)

**Version:** 1.0.0  
**Date:** 2025-10-30  
**Authors:** Fabio Cherici + Padmin D. Curtis (CTO OS3)  
**Status:** ✅ APPROVED - Documento definitivo architettura NATAN_LOC

**Fonti:**
- `README_STACK.md` (2025-10-30) - Stack tecnologico
- `hybrid_database_implementation.md` (2025-10-30) - Database ibrido
- `natan_os_3_docs_batch_1` - Gateway AI specification
- `natan_os_3_docs_batch_2` - Security & embeddings

---

## 🎯 EXECUTIVE SUMMARY

**NATAN_LOC** è una piattaforma SaaS multi-tenant per analisi semantica documenti PA.

**Deployment:**
- URL: `https://natan.florenceegi.com`
- Modalità: Cloud, Hybrid, On-Premise (air-gapped)
- Target: Pubbliche Amministrazioni italiane

**Differenza da NATAN_PA:**
- NATAN_PA (in `/home/fabio/EGI`): Demo single-tenant Comune Firenze
- NATAN_LOC (in `/home/fabio/NATAN_LOC`): SaaS multi-tenant scalabile

---

## 🧩 STACK TECNOLOGICO

### **FRONTEND**
- **TypeScript / JavaScript puro** (NO React/Vue/Angular)
- **Build tool**: Vite
- **Styling**: Tailwind CSS
- **UI**: Componenti semantici modulari
- **Accessibilità**: WCAG 2.1 AA
- **SEO**: SEO-friendly
- **Autenticazione**: API Token Sanctum (Bearer) via fetch

**Rationale:** Policy OS3 - leggibilità, controllo totale, leggerezza.

---

### **BACKEND CORE**
- **PHP**: 8.3
- **Framework**: Laravel 12
- **Tenancy**: `stancl/tenancy` (single-DB con `tenant_id` + middleware)
- **Auth**: Laravel Sanctum + Spatie `laravel-permission`
- **Media**: Spatie `medialibrary`
- **API**: REST/JSON
- **Security**: CORS controllati, CSRF, header sicurezza

**Pattern:** Laravel espone API REST → Frontend TS/JS consuma → Python opera in parallelo.

---

### **AI / SCRAPING LAYER**
- **Language**: Python 3.12
- **Framework**: FastAPI
- **ASGI Server**: Uvicorn
- **Libraries**:
  - `requests` - HTTP client
  - `beautifulsoup4` - HTML parsing
  - `lxml` - XML parsing
  - `playwright` - Web scraping (opzionale)
  - `openai` - OpenAI API client
  - `anthropic` - Anthropic API client
  - `pymongo` - MongoDB client
  - `numpy` - Vector operations
  - `faiss` - Vector similarity (opzionale)
  - `celery` - Background jobs + Redis queue

**Endpoints Python FastAPI:**
- `POST /embed` - Generate embeddings
- `POST /chat` - LLM inference
- `POST /rag/search` - Vector similarity search
- `GET /healthz` - Health check

**Pipeline:** PDF → parsing → chunking → embedding → MongoDB → Laravel metadati → frontend.

---

### **DATABASES**

#### **MariaDB (Transazionale)**
**Ruolo:** Relazioni, permessi, dati strutturati tenant-scoped

**Tabelle:**
- `users` (con `tenant_id`)
- `pa_entities` (alias tenants)
- `pa_acts` (con `tenant_id`)
- `user_conversations` (con `tenant_id`)
- `pa_web_scrapers`
- `roles` / `permissions` (Spatie)

**Pattern:** Single-database con colonna `tenant_id` + Global Scope Laravel.

---

#### **MongoDB (Cognitivo)**
**Ruolo:** Embeddings, log AI, contenuti documentali ad alto volume

**Collections:**

```javascript
// pa_act_embeddings
{
    tenant_id: Number,      // OBBLIGATORIO
    act_id: Number,         // FK logica a MariaDB pa_acts.id
    embedding: [Float],     // 1536 dimensions
    model_name: String,     // "text-embedding-3-small"
    metadata: {
        title: String,
        protocol_number: String,
        act_type: String,
        date: ISODate,
        hash: String
    },
    created_at: ISODate
}

// Indici
db.pa_act_embeddings.createIndex({ tenant_id: 1, act_id: 1 }, { unique: true });
db.pa_act_embeddings.createIndex({ tenant_id: 1, "metadata.act_type": 1 });

// natan_chat_messages
{
    tenant_id: Number,
    user_id: Number,
    conversation_id: String,
    role: String,           // "user" | "assistant"
    content: String,
    tokens: Number,
    latency_ms: Number,
    sources: Array,
    created_at: ISODate
}

// ai_logs
{
    tenant_id: Number,
    event: String,
    context: Object,
    ts: ISODate
}

// analytics
{
    tenant_id: Number,
    date: ISODate,
    metrics: Object
}
```

**Pattern:** Namespace logico con `tenant_id` obbligatorio + indici composti.

---

#### **Redis**
**Ruolo:** Cache + Queue

- Cache Laravel
- Session storage
- Queue Laravel (Horizon)
- Queue Python (Celery)
- Rate limiting

---

### **BLOCKCHAIN**
- **Network**: Algorand
- **Tool**: AlgoKit (Node.js microservice)
- **SDK**: algosdk (JavaScript)
- **Ruolo**: Anchoring hash documenti, CoA immutability

---

### **COMPLIANCE**
- **ULM**: UltraLogManager (logging operativo)
- **UEM**: ErrorManagerInterface (errori strutturati)
- **Audit**: AuditLogService (GDPR activity tracking)
- **Consent**: ConsentService (gestione consensi)

---

### **LINGUE**
- **Italiano** (it) - Lingua principale
- **Inglese** (en) - Lingua secondaria

**Rationale:** Target PA italiana, internazionalizzazione futura europea.

---

## 🏗️ ARCHITETTURA LOGICA

### **Request Flow**

```
Request (https://natan.florenceegi.com)
    ↓
InitializeTenancyMiddleware (detect tenant da subdomain/user/API)
    ↓
┌─────────────────────────────────────┐
│  Laravel 12 (Backend Core)          │
│  ├─ Auth (Sanctum)                  │
│  ├─ Authorization (Spatie)          │
│  ├─ Tenant Context Set              │
│  └─ API Controllers                 │
└─────────────────────────────────────┘
    ↓
┌─────────────────────────────────────┐
│  Services Layer                     │
│  ├─ NatanChatService                │
│  ├─ RagService                      │
│  ├─ AiGatewayService (→ Python)     │
│  ├─ DataSanitizerService (GDPR)     │
│  └─ AuditLogService (GDPR)          │
└─────────────────────────────────────┘
    ↓
┌─────────────────────────────────────────┐
│  Data Layer                             │
│  ├─ MariaDB (WHERE tenant_id = X)       │
│  │   └─ Global Scope automatico         │
│  └─ MongoDB ({ tenant_id: X })          │
│      └─ Filter esplicito in query       │
└─────────────────────────────────────────┘
    ↓
┌─────────────────────────────────────────┐
│  Python FastAPI (AI Microservice)       │
│  ├─ POST /embed (embeddings)            │
│  ├─ POST /chat (LLM inference)          │
│  ├─ POST /rag/search (vector search)    │
│  └─ MongoDB direct access               │
└─────────────────────────────────────────┘
    ↓
Response ← JSON ← Laravel ← Frontend TypeScript
```

---

## 🤖 AI MULTIMODEL GATEWAY

### **Principio: AI-Agnostic**

NATAN_LOC NON è legato a un singolo provider AI.  
Usa un **gateway** che orchestra più provider tramite **policy configurabili**.

### **Provider Supportati**

```
Chat Models:
├─ anthropic.sonnet-3.5      (Claude 3.5 Sonnet)
├─ anthropic.opus-3          (Claude 3 Opus)
├─ openai.gpt-4.1            (GPT-4 Turbo)
└─ ollama.llama3-70b         (Llama 3 70B - local)
└─ ollama.llama3-8b          (Llama 3 8B - local)
└─ aisiru.enterprise-v1      (AISIRU - EU enterprise)

Embedding Models:
├─ openai.text-3-large       (text-embedding-3-large)
├─ openai.text-3-small       (text-embedding-3-small)
├─ ollama.nomic-embed        (nomic-embed-text - local)
└─ aisiru.embed-v1           (AISIRU embeddings)
```

### **Policy Engine**

**File:** `config/ai_policies.yaml`

```yaml
version: 1

fallbacks:
  chat: ["anthropic.sonnet-3.5", "openai.gpt-4.1", "ollama.llama3-70b"]
  embed: ["openai.text-3-large", "ollama.nomic-embed", "aisiru.embed-v1"]

policies:
  # Cloud mode (default)
  - match: { persona: "strategic", task_class: "RAG" }
    use: { chat: "anthropic.sonnet-3.5", embed: "openai.text-3-large" }
  
  # Local mode (on-premise PA)
  - match: { tenant_id: 0, locality: "onprem" }
    use: { chat: "ollama.llama3-8b", embed: "ollama.nomic-embed" }
```

### **Interfaces (Contract Pattern)**

```php
namespace App\AI\Contracts;

interface ChatModel {
    public function generate(array $messages, array $tools = [], array $options = []): AiResponse;
}

interface EmbeddingModel {
    public function embed(string $text, array $options = []): AiEmbeddingResult;
}

interface AiRouter {
    public function selectChatModel(array $context): ChatModel;
    public function selectEmbeddingModel(array $context): EmbeddingModel;
}
```

### **Provider Adapters**

**Namespace:** `App\AI\Providers\{OpenAI|Anthropic|Ollama|AISIRU}`

```
├─ OpenAIChatAdapter (implements ChatModel)
├─ OpenAIEmbeddingAdapter (implements EmbeddingModel)
├─ AnthropicChatAdapter (implements ChatModel)
├─ OllamaChatAdapter (implements ChatModel) - local models
├─ AISIRUChatAdapter (implements ChatModel) - enterprise
└─ ... (estensibile)
```

**Linee guida:**
- Nessun SDK vendor-lock nei controller/services applicativi
- Mapping 1:1 tra opzioni gateway e parametri provider
- UEM su errori di rete/timeouts con codici dedicati

---

## 🗄️ DATABASE SCHEMA COMPLETO

### **MariaDB Tables**

#### **pa_entities (Tenants)**
```sql
CREATE TABLE pa_entities (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    domain VARCHAR(255),               -- natan-firenze.florenceegi.com
    settings JSON,
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    INDEX idx_slug (slug),
    INDEX idx_domain (domain)
);
```

#### **users (con tenant_id)**
```sql
ALTER TABLE users ADD COLUMN tenant_id BIGINT NOT NULL;
ALTER TABLE users ADD FOREIGN KEY (tenant_id) REFERENCES pa_entities(id) ON DELETE CASCADE;
ALTER TABLE users ADD INDEX idx_users_tenant (tenant_id);
```

#### **pa_acts (Atti PA con tenant_id)**
```sql
CREATE TABLE pa_acts (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    tenant_id BIGINT NOT NULL,
    user_id BIGINT NOT NULL,
    protocol_number VARCHAR(255),
    protocol_date DATE,
    act_type VARCHAR(100),
    title TEXT,
    description LONGTEXT,
    jsonMetadata JSON,                 -- financial_data, codes, etc
    hash VARCHAR(64),                  -- SHA-256 document hash
    pa_anchored BOOLEAN DEFAULT false,
    pa_anchored_at TIMESTAMP NULL,
    pa_blockchain_txid VARCHAR(255) NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES pa_entities(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_tenant_id (tenant_id),
    INDEX idx_tenant_user (tenant_id, user_id),
    INDEX idx_protocol (tenant_id, protocol_number),
    INDEX idx_date (tenant_id, protocol_date)
);
```

#### **user_conversations (con tenant_id)**
```sql
CREATE TABLE user_conversations (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    tenant_id BIGINT NOT NULL,
    user_id BIGINT NOT NULL,
    type VARCHAR(50),
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES pa_entities(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_tenant_user (tenant_id, user_id)
);
```

---

### **MongoDB Collections**

**IMPORTANTE:** MongoDB è **schema-less** (document-oriented).  
Ogni documento può avere struttura DIVERSA. Gli esempi sotto sono indicativi, non rigidi.

---

#### **documents** (Collezione Principale - FLESSIBILE)

**Natura MongoDB:** Ogni documento può contenere campi completamente diversi.  
NATAN_LOC gestisce **QUALSIASI tipo di file** che un'organizzazione archivia.

**Esempio 1 - Atto PA:**
```javascript
{
    _id: ObjectId("..."),
    tenant_id: 123,                     // OBBLIGATORIO
    document_id: "uuid-abc-123",        // UUID universale
    document_type: "pa_act",            // Tipo documento
    
    // CONTENUTO (estratto da PDF/DOCX/etc)
    content: {
        raw_text: "Delibera di Giunta Comunale n. 45...",  // Full text
        extracted_from: "delibera_GC_45_2024.pdf",
        file_path: "storage/tenants/123/documents/delibera_GC_45_2024.pdf",
        file_size_bytes: 245678,
        original_format: "pdf",
        extraction_method: "pdfparser",  // O "claude_vision" per immagini
        extraction_confidence: 0.98
    },
    
    // METADATA PA-SPECIFIC (campi liberi!)
    protocol_number: "DET-2024/123",
    protocol_date: ISODate("2024-10-15"),
    issuer: "Comune di Firenze",
    department: "Ufficio Tecnico",
    responsible: "Ing. Mario Rossi",
    act_category: "urbanistica",
    
    // EMBEDDINGS per semantic search
    embedding: [0.123, -0.456, 0.789, ...],  // 1536 floats
    embedding_model: "text-embedding-3-small",
    
    // CHUNKS (se documento grande)
    chunks: [
        {
            chunk_index: 0,
            chunk_text: "Delibera di Giunta...",
            embedding: [0.111, -0.222, ...],
            tokens: 245,
            page_number: 1
        },
        {
            chunk_index: 1,
            chunk_text: "Articolo 1 - Disposizioni...",
            embedding: [0.333, -0.444, ...],
            tokens: 312,
            page_number: 2
        }
    ],
    
    // BLOCKCHAIN (immutabilità)
    blockchain: {
        anchored: true,
        hash: "sha256_abc123...",
        txid: "ALGORAND_TX_123",
        network: "mainnet",
        anchored_at: ISODate("2024-10-15T10:30:00Z")
    },
    
    // AI INSIGHTS (documento VIVO)
    ai_insights: {
        summary: "Approvazione progetto...",
        entities_extracted: ["Comune Firenze", "Progetto Verde", "€125.000"],
        categories: ["urbanistica", "verde pubblico"],
        financial_data: {
            amounts: [125000.00],
            cig: "ABC123456789",
            cup: null
        },
        last_analyzed_at: ISODate("2024-10-15T11:00:00Z")
    },
    
    created_at: ISODate("2024-10-15T10:00:00Z"),
    updated_at: ISODate("2024-10-15T11:00:00Z")
}
```

**Esempio 2 - Contratto Aziendale (STRUTTURA COMPLETAMENTE DIVERSA!):**
```javascript
{
    _id: ObjectId("..."),
    tenant_id: 456,
    document_id: "uuid-def-456",
    document_type: "contract",
    
    content: {
        raw_text: "Contratto di fornitura tra...",
        extracted_from: "contratto_fornitura_2024.pdf",
        file_path: "storage/tenants/456/documents/contratto_fornitura_2024.pdf",
        original_format: "pdf"
    },
    
    // METADATA CONTRACT-SPECIFIC (campi DIVERSI da PA act!)
    contract_number: "CNT-2024/456",
    contract_date: ISODate("2024-09-20"),
    parties: ["Azienda XYZ Srl", "Fornitore ABC Spa"],
    contract_value: 50000.00,
    currency: "EUR",
    duration_months: 12,
    renewal_clause: true,
    
    embedding: [0.789, -0.123, ...],
    embedding_model: "text-embedding-3-small",
    
    chunks: [...],  // Se necessario
    
    blockchain: {
        anchored: true,
        hash: "sha256_def456...",
        txid: "ALGORAND_TX_456",
        network: "mainnet",
        anchored_at: ISODate("2024-09-20T15:00:00Z")
    },
    
    ai_insights: {
        summary: "Contratto fornitura materiali...",
        key_clauses: ["Pagamento 60gg", "Penale 10%"],
        risk_level: "low"
    },
    
    created_at: ISODate("2024-09-20T14:00:00Z")
}
```

**Esempio 3 - Immagine con OCR (da Claude Vision):**
```javascript
{
    _id: ObjectId("..."),
    tenant_id: 789,
    document_id: "uuid-ghi-789",
    document_type: "image_scan",
    
    content: {
        raw_text: "Testo estratto da immagine scansionata via Claude Vision...",
        extracted_from: "documento_scannerizzato.jpg",
        file_path: "storage/tenants/789/documents/documento_scannerizzato.jpg",
        original_format: "jpg",
        extraction_method: "claude_vision",  // Claude Vision API
        extraction_confidence: 0.94,
        image_metadata: {
            width: 2480,
            height: 3508,
            dpi: 300,
            color_space: "RGB"
        }
    },
    
    // METADATA estratti da AI Vision (campi VARIABILI!)
    document_title: "Certificato di Proprietà",
    issue_date: ISODate("2020-05-10"),
    issuer: "Agenzia delle Entrate",
    // ... altri campi estratti da AI
    
    embedding: [0.456, -0.789, ...],
    
    blockchain: {
        anchored: true,
        hash: "sha256_ghi789...",
        txid: "ALGORAND_TX_789"
    },
    
    created_at: ISODate("2024-10-20T09:00:00Z")
}
```

---

#### **CAMPI OBBLIGATORI (Minimi Comuni)**

**Ogni documento DEVE avere (per funzionare):**
- `tenant_id` - Isolation multi-tenant
- `document_id` - UUID universale
- `document_type` - Tipo documento (per routing AI/categorizzazione)
- `content.raw_text` - Testo estratto (per embeddings + search)
- `embedding` - Vector per semantic search
- `created_at` - Timestamp

**Tutto il resto è LIBERO!** Ogni tipo documento aggiunge campi che servono.

---

#### **Indici MongoDB (Performance)**

```javascript
// Indice univoco per tenant + document
db.documents.createIndex({ tenant_id: 1, document_id: 1 }, { unique: true });

// Indice per tipo documento
db.documents.createIndex({ tenant_id: 1, document_type: 1 });

// Indice per date (query temporali)
db.documents.createIndex({ tenant_id: 1, created_at: -1 });

// Indice text search (full-text)
db.documents.createIndex({ "content.raw_text": "text" });
```

**Nota:** Vector search = cosine similarity app-level in Python (FAISS o loop).

---

#### **natan_chat_messages** (Conversazioni)

```javascript
{
    _id: ObjectId,
    tenant_id: Number,              // OBBLIGATORIO
    user_id: Number,
    conversation_id: String,
    role: String,                   // "user" | "assistant"
    content: String,                // Messaggio
    tokens: Number,
    latency_ms: Number,
    sources: [String],              // document_ids citati
    created_at: ISODate
}

// Indici
db.natan_chat_messages.createIndex({ tenant_id: 1, user_id: 1 });
db.natan_chat_messages.createIndex({ tenant_id: 1, conversation_id: 1 });
```

---

#### **ai_logs** (Logging AI Operations)

```javascript
{
    _id: ObjectId,
    tenant_id: Number,
    event: String,                  // "embedding_generated", "rag_search", "ai_inference"
    context: Object,                // Dati variabili per evento
    ts: ISODate
}
```

---

#### **analytics** (Metriche Aggregate)

```javascript
{
    _id: ObjectId,
    tenant_id: Number,
    date: ISODate,
    metrics: Object                 // Metriche variabili per tenant
}
```

---

## 🔄 ARCHITETTURA MULTI-TENANT

### **Tenancy Model**

**MariaDB:**
- Strategia: Single-database con `tenant_id`
- Global Scope: Laravel applica `WHERE tenant_id = X` automaticamente
- Middleware: `InitializeTenancyMiddleware` detect tenant da subdomain/user/API

**MongoDB:**
- Namespace logico: ogni documento ha `tenant_id` (obbligatorio)
- Filter esplicito: ogni query include `{ tenant_id: X }`
- Indici composti: `{ tenant_id: 1, ... }` per performance

### **Tenant Detection**

```php
// Middleware: InitializeTenancyMiddleware

// 1. Da subdomain
if (preg_match('/^([a-z0-9-]+)\.natan\.florenceegi\.com$/', $host, $matches)) {
    $slug = $matches[1];
    $tenant = PaEntity::where('slug', $slug)->first();
}

// 2. Da user autenticato
elseif (Auth::check()) {
    $tenant = Auth::user()->tenant;
}

// 3. Da API header
elseif ($request->header('X-Tenant-ID')) {
    $tenantId = $request->header('X-Tenant-ID');
    $tenant = PaEntity::find($tenantId);
}

// Set tenant context
tenancy()->initialize($tenant);
```

### **Isolation Garantita**

```php
// MariaDB: Global Scope automatico
PaAct::all(); // Laravel aggiunge WHERE tenant_id = current_tenant automaticamente

// MongoDB: Filter esplicito
PaActEmbedding::where('tenant_id', $tenantId)->get();
```

---

## 🐍 PYTHON FASTAPI MICROSERVICE

### **Structure**

```
python_ai_service/
├── app/
│   ├── main.py                 # FastAPI app
│   ├── routers/
│   │   ├── embeddings.py       # POST /embed
│   │   ├── chat.py             # POST /chat
│   │   ├── rag.py              # POST /rag/search
│   │   └── health.py           # GET /healthz
│   ├── services/
│   │   ├── openai_service.py
│   │   ├── anthropic_service.py
│   │   ├── ollama_service.py
│   │   ├── mongo_service.py
│   │   └── policy_engine.py    # YAML policy resolver
│   ├── models/
│   │   ├── requests.py         # Pydantic request models
│   │   └── responses.py        # Pydantic response models
│   └── config.py               # Settings
├── requirements.txt
├── pyproject.toml
└── .env
```

### **Endpoints Specification**

#### **POST /embed**
```python
# Request
{
    "text": "Delibera di Giunta...",
    "tenant_id": 123,
    "model": "text-embedding-3-small"
}

# Response
{
    "embedding": [0.123, -0.456, ...],  # 1536 floats
    "model": "text-embedding-3-small",
    "dimensions": 1536,
    "tokens": 245
}
```

#### **POST /chat**
```python
# Request
{
    "messages": [
        {"role": "user", "content": "Analizza..."}
    ],
    "tenant_id": 123,
    "persona": "strategic",
    "model": "anthropic.sonnet-3.5"
}

# Response
{
    "message": "Analisi strategica...",
    "model": "claude-3-5-sonnet-20241022",
    "usage": {
        "input_tokens": 1234,
        "output_tokens": 567
    },
    "citations": [...]
}
```

#### **POST /rag/search**
```python
# Request
{
    "query": "appalti verdi 2024",
    "tenant_id": 123,
    "limit": 10
}

# Response
{
    "results": [
        {
            "act_id": 456,
            "score": 0.87,
            "metadata": {...}
        }
    ],
    "total_found": 487,
    "search_time_ms": 142
}
```

---

## 🔌 INTEGRATION LARAVEL ↔ PYTHON

### **Laravel chiama Python FastAPI**

```php
// app/Services/AI/AiGatewayService.php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;

class AiGatewayService {
    protected string $baseUrl;
    
    public function __construct() {
        $this->baseUrl = config('services.ai_gateway.base_url'); // http://localhost:8000
    }
    
    public function embed(string $text, int $tenantId): array {
        $response = Http::timeout(30)->post($this->baseUrl . '/embed', [
            'text' => $text,
            'tenant_id' => $tenantId,
            'model' => config('ai.embedding_model')
        ]);
        
        return $response->json();
    }
    
    public function chat(array $messages, int $tenantId, string $persona): array {
        $response = Http::timeout(120)->post($this->baseUrl . '/chat', [
            'messages' => $messages,
            'tenant_id' => $tenantId,
            'persona' => $persona,
        ]);
        
        return $response->json();
    }
    
    public function ragSearch(string $query, int $tenantId, ?int $limit = null): array {
        $response = Http::timeout(30)->post($this->baseUrl . '/rag/search', [
            'query' => $query,
            'tenant_id' => $tenantId,
            'limit' => $limit ?? 10
        ]);
        
        return $response->json();
    }
}
```

---

## 🔒 SECURITY & GDPR

### **Principi di Sicurezza (OS3 Fortino Digitale)**

1. **Isolation First**: `tenant_id` obbligatorio in OGNI scrittura/lettura (MariaDB + MongoDB)
2. **Policy di accesso**: Verificare `user->tenant_id` prima di eseguire
3. **UEM First**: Mai sostituire gestione errori strutturata con log generici
4. **GDPR**: AuditLogService registra azioni su dati (DATA_ACCESS, CONTENT_CREATION, etc.)
5. **Sanitizzazione**: DataSanitizerService rimuove PII verso provider AI esterni
6. **Cifratura**: Campi sensibili cifrati a riposo

### **Data Sanitization (GDPR)**

```php
interface DataSanitizer {
    public function cleanse(string $text, array $rules = []): string;
}

// Uso nel gateway prima della call esterna
$clean = $this->sanitizer->cleanse($prompt, [
    'pii' => true,
    'hash_ids' => true,
    'mask_emails' => true
]);
```

**Principio:** NATAN invia **solo metadati pubblici**. Ogni PII viene rimossa o mascherata.

### **Embeddings: Non Reversibili**

Gli embeddings sono **punti in spazio vettoriale ad alta dimensionalità** (1536 dim).

**Proprietà matematica:**
- Trasformazione `f: Text → R^1536` è **molti-a-uno**
- **Non invertibile**: impossibile ricostruire testo da vettore
- **GDPR-safe**: Non costituisce dato personale

**Implicazioni:**
- Leak database embeddings → Nessun rischio di ricostruzione contenuti
- Embeddings anonimi e non leggibili
- Sicurezza intrinseca

### **Threat Matrix**

| # | Minaccia | Rischio | Mitigazione |
|---|----------|---------|-------------|
| 1 | Model Inversion | Medio | Embeddings non reversibili + segregazione tenant |
| 2 | Membership Inference | Basso | No training persistente |
| 3 | Attribute Inference | Basso | Metadati separati fisicamente |
| 4 | Similarity Abuse | Medio | Rate-limit + audit ULM |
| 5 | Embedding Collision | Basso | Threshold adaptativo |
| 6 | Leak Indici Vettoriali | Basso | Dati non reversibili + cifratura |
| 7 | Prompt Injection | Medio | DataSanitizer + filtro sintattico |
| 8 | Abuso API | Alto | Rate-limit + blocco UEM |
| 9 | Cross-Tenant Leakage | **CRITICO** | Architettura isolata + token firmati |
| 10 | LLM Memorization | Basso | Context ephemeral |

**Audit Frequency:** Mensile (ULM report) + straordinario su evento UEM critico.

---

## 🔄 USE PIPELINE COMPLETA (Ultra Semantic Engine)

### **Phase 1: Ingestione Documenti**

```
PA Web Scraper / Upload manuale / API REST
    ↓
Antivirus + Firma Digitale + Validazione
    ↓
AuditLogService (GDPR::CONTENT_CREATION)
    ↓
Storage MariaDB (metadata base)
```

### **Phase 2: Indicizzazione & Parsing**

```
Parser PDF/HTML → Extract full text
    ↓
DataSanitizerService (rimuove PII)
    ↓
Chunk semantici (con source_ref precisi)
    ↓
ULM info('parse_success')
    ↓
Text chunks + source_ref ready
```

### **Phase 3: Generazione Embeddings**

```
Text chunks → Python FastAPI POST /embed
    ↓
EmbeddingModelInterface (OpenAI/Ollama)
    ↓
Vector [1536 floats]
    ↓
Storage MongoDB collection 'documents'
    ↓
ULM info('embedding_stored') + Audit GDPR::DATA_ACCESS
```

### **Phase 4: Query Utente (USE Pipeline)**

```
User Query
    ↓
1. Question Classifier (AI leggero)
    ↓ intent + confidence + constraints
2. Execution Router (logico deterministico)
    ↓ decide: direct_query | RAG_strict | block
    
Se direct_query:
    → MongoDB query diretta (es: count documenti)
    → Response immediata (no AI)
    
Se block:
    → Richiesta chiarimento utente
    → "Query non verificabile, puoi riformulare?"
    
Se RAG_strict:
    ↓
3. Retriever (estrae chunks citabili)
    ↓ chunks con source_ref precisi (URL#page=X)
```

### **Phase 5: Neurale Strict (LLM Controllato)**

```
Chunks citabili → Python FastAPI POST /chat/strict
    ↓
Gateway seleziona provider (policy)
    ↓
LLM genera CLAIMS atomici (1 frase = 1 claim)
    ↓
Output: claims[] con source_ids obbligatori
```

### **Phase 6: Logical Verifier (Verifica Post-Generazione)**

```
Claims[] → Verifier
    ↓
Per ogni claim:
    1. Controlla source_ids esistono in MongoDB
    2. Calcola Coverage (claim coperto da fonti?)
    3. Calcola Reference Score (numero fonti)
    4. Calcola Extractor Quality (OCR confidence)
    5. Calcola Date Coherence
    6. Calcola Out-of-domain Risk
    ↓
URS = 0.30*C + 0.25*R + 0.20*E + 0.15*D + 0.10*(1-O)
    ↓
Label: A (≥0.85) | B (0.70-0.84) | C (0.50-0.69) | X (<0.50)
    ↓
Decision: URS < 0.5 → BLOCCA claim (non pubblicare)
```

### **Phase 7: Renderer (UI con Affidabilità Visibile)**

```
Verified Claims → Renderer
    ↓
HTML con:
    - Colore per label (verde=A, blu=B, giallo=C, rosso=X)
    - Badge URS score
    - Link clickable su ogni claim → fonte esatta
    - Badge "[Deduzione]" se is_inference=true
    ↓
Response → Frontend TypeScript
```

### **Phase 8: Audit & Logging (Granulare)**

```
Save to MongoDB:
    ↓
Collection 'sources' (fonti atomiche)
Collection 'claims' (affermazioni verificate)
Collection 'query_audit' (audit strutturato)
    ↓
ULM traccia tutto:
    - CLAIM_RENDERED (URS, label)
    - CLAIM_BLOCKED (URS < 0.5)
    - CLAIM_CLICKED (user verifica fonte)
    ↓
UEM gestisce anomalie:
    - SOURCE_NOT_FOUND
    - URS_TOO_LOW
    - CLAIM_VERIFICATION_FAILED
    ↓
Audit GDPR (DATA_ACCESS, AI_INFERENCE)
    ↓
[OPTIONAL] Hash → Algorand blockchain
```

---

## 🔌 REPOSITORY PATTERN (Anti-Lock-in)

### **Interfaces**

```php
interface PaActRepositoryInterface {
    public function findByIds(array $ids, int $tenantId): Collection;
    public function search(array $filters, int $tenantId, ?int $limit = null): Collection;
}

interface EmbeddingRepositoryInterface {
    public function upsertEmbedding(int $tenantId, int $actId, array $embedding, array $metadata = []): void;
    public function knn(int $tenantId, array $queryVector, int $k = 10): array;
}
```

### **Implementations**

```php
// MariaDB Implementation
class MariaDBPaActRepository implements PaActRepositoryInterface {
    public function findByIds(array $ids, int $tenantId): Collection {
        return PaAct::where('tenant_id', $tenantId)
            ->whereIn('id', $ids)
            ->get();
    }
    
    public function search(array $filters, int $tenantId, ?int $limit = null): Collection {
        $query = PaAct::where('tenant_id', $tenantId);
        
        // Apply filters...
        
        if ($limit !== null) {
            $query->limit($limit);
        }
        
        return $query->get();
    }
}

// MongoDB Implementation
class MongoEmbeddingRepository implements EmbeddingRepositoryInterface {
    public function upsertEmbedding(int $tenantId, int $actId, array $embedding, array $metadata = []): void {
        PaActEmbedding::updateOrCreate(
            ['tenant_id' => $tenantId, 'act_id' => $actId],
            [
                'embedding' => $embedding,
                'metadata' => $metadata,
                'created_at' => now()
            ]
        );
    }
    
    public function knn(int $tenantId, array $q, int $k = 10): array {
        // App-level KNN (cosine similarity)
        return PaActEmbedding::where('tenant_id', $tenantId)
            ->get(['act_id', 'embedding'])
            ->map(fn($doc) => [
                'act_id' => $doc->act_id,
                'score'  => self::cosine($q, $doc->embedding),
            ])
            ->sortByDesc('score')
            ->take($k)
            ->values()
            ->all();
    }
    
    private static function cosine(array $a, array $b): float {
        $dot = 0; $na = 0; $nb = 0; $n = min(count($a), count($b));
        for ($i=0; $i<$n; $i++) {
            $dot += $a[$i] * $b[$i];
            $na += $a[$i] ** 2;
            $nb += $b[$i] ** 2;
        }
        return $dot / (sqrt($na) * sqrt($nb) ?: 1e-9);
    }
}
```

---

## ⚙️ SERVICES ARCHITECTURE

### **Core Services (Laravel)**

```
NatanChatService
├── processQuery() - Main orchestrator
│   ├── Persona selection
│   ├─→ AiGatewayService→ragSearch() (Python FastAPI)
│   ├─→ AiGatewayService→chat() (Python FastAPI)
│   ├── GDPR audit
│   └── Response assembly

RagService
├── getContextForQuery() - RAG retrieval
│   ├─→ AiGatewayService→embed() (Python)
│   ├─→ AiGatewayService→ragSearch() (Python)
│   └── Fetch metadata from MariaDB

AuditLogService (GDPR)
├── logUserAction()
├── logGdprAction()
└── Tenant-scoped logs

ConsentService (GDPR)
├── hasConsent()
├── updateUserConsents()
└── Tenant-scoped consents
```

### **AI Services (Python FastAPI)**

```
EmbeddingService
├── generate_embedding() - OpenAI/Ollama
└── Store in MongoDB

ChatService
├── inference() - Claude/OpenAI/Ollama
└── Policy-based routing

RagSearchService
├── vector_search() - MongoDB KNN
└── Cosine similarity
```

---

## 🌐 DEPLOYMENT MODES

### **1. Cloud Mode (SaaS)**

```
Browser → https://natan.florenceegi.com
    ↓
Laravel 12 (Cloud)
    ↓
Python FastAPI (Cloud)
    ↓
MariaDB + MongoDB (Cloud)
    ↓
Anthropic/OpenAI API (External)
```

**Configurazione:**
```env
AI_DEFAULT_PROVIDER=anthropic
AI_ALLOWED_CHAT_PROVIDERS=anthropic,openai
AI_ALLOWED_EMBED_PROVIDERS=openai
RAG_BACKEND=mongodb
```

---

### **2. Local Mode (On-Premise Air-Gapped)**

```
Browser (LAN) → Natan UI
    ↓
Laravel 12 (Server comunale)
    ↓
Python FastAPI (Server comunale)
    ↓
MariaDB + MongoDB (Server comunale)
    ↓
Ollama (Server comunale - LAN only)
```

**Configurazione:**
```env
AI_DEFAULT_PROVIDER=ollama
AI_ALLOWED_CHAT_PROVIDERS=ollama
AI_ALLOWED_EMBED_PROVIDERS=ollama
RAG_BACKEND=mongodb
OLLAMA_HOST=localhost:11434
```

**Network Security:**
- Blocco egress per default
- Nessun traffico Internet
- mTLS per comunicazioni interne
- Policy di rete enforced

**Requisiti Hardware:**
- CPU: 8+ cores
- RAM: 32GB
- Storage: SSD NVMe 1TB
- Docker + Ollama + MariaDB + MongoDB

---

### **3. Hybrid Mode**

```
Browser → Cloud
    ↓
Laravel 12 (Cloud)
    ↓
MariaDB (Cloud) + MongoDB (Local)
    ↓
Python FastAPI (Local)
    ↓
Ollama (Local)
```

**Use Case:** Dati sensibili on-premise, orchestrazione cloud.

---

## 📋 CONFIGURATION FILES

### **Laravel .env**

```env
# App
APP_NAME="NATAN"
APP_ENV=production
APP_URL=https://natan.florenceegi.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=natan_main
DB_USERNAME=natan_user
DB_PASSWORD=********

# MongoDB
MONGO_DB_CONNECTION=mongodb
MONGO_DB_HOST=127.0.0.1
MONGO_DB_PORT=27017
MONGO_DB_DATABASE=natan_ai_core
MONGO_DB_USERNAME=natan_user
MONGO_DB_PASSWORD=********

# Redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# AI Gateway (Python FastAPI)
AI_GATEWAY_BASE_URL=http://localhost:8000
AI_GATEWAY_TIMEOUT=120

# Blockchain (AlgoKit)
ALGOKIT_BASE_URL=http://localhost:3000

# Tenancy
TENANCY_DETECTION=subdomain
CENTRAL_DOMAINS=natan.florenceegi.com
```

### **Python .env**

```env
# MongoDB
MONGO_URI=mongodb://natan_user:********@localhost:27017/natan_ai_core

# OpenAI
OPENAI_API_KEY=sk-...
OPENAI_ORGANIZATION=org-...

# Anthropic
ANTHROPIC_API_KEY=sk-ant-...

# Ollama (local mode)
OLLAMA_HOST=http://localhost:11434

# Policy
AI_POLICY_FILE=config/ai_policies.yaml
```

---

## 🧪 TESTING STRATEGY

### **Tenant Isolation Test**
```php
public function test_user_sees_only_own_tenant_acts_in_rag() {
    $tenant1 = Tenant::factory()->create();
    $tenant2 = Tenant::factory()->create();
    $user1 = User::factory()->create(['tenant_id' => $tenant1->id]);
    
    // Seed atti per ciascun tenant
    // ...
    
    $results = app(RagService::class)->findRelevant('mobilità', $user1, 5);
    
    $this->assertTrue($results->every(fn($a) => $a->tenant_id === $tenant1->id));
}
```

### **RAG Accuracy Test**
```php
public function test_rag_returns_correct_act_in_topk() {
    // Dato set di atti, la ricerca semantica deve restituire atto corretto
}
```

### **GDPR Audit Test**
```php
public function test_every_rag_search_generates_audit_log() {
    // Ogni ricerca deve generare audit log con categoria corretta
}
```

---

## 🚀 DEPLOYMENT ARCHITECTURE

### **Docker Compose (Production)**

```yaml
version: '3.8'

services:
  # Laravel Backend
  laravel:
    build: ./laravel
    ports:
      - "80:80"
    environment:
      - DB_HOST=mariadb
      - MONGO_DB_HOST=mongodb
      - REDIS_HOST=redis
      - AI_GATEWAY_BASE_URL=http://python_ai:8000
    depends_on:
      - mariadb
      - mongodb
      - redis
      - python_ai
  
  # Python AI Microservice
  python_ai:
    build: ./python_ai_service
    ports:
      - "8000:8000"
    environment:
      - MONGO_URI=mongodb://mongodb:27017/natan_ai_core
    depends_on:
      - mongodb
  
  # MariaDB
  mariadb:
    image: mariadb:11
    environment:
      MYSQL_ROOT_PASSWORD: ********
      MYSQL_DATABASE: natan_main
    volumes:
      - mariadb_data:/var/lib/mysql
  
  # MongoDB
  mongodb:
    image: mongo:7
    environment:
      MONGO_INITDB_ROOT_USERNAME: natan_user
      MONGO_INITDB_ROOT_PASSWORD: ********
    volumes:
      - mongodb_data:/data/db
  
  # Redis
  redis:
    image: redis:7-alpine
    volumes:
      - redis_data:/data
  
  # AlgoKit (Blockchain)
  algokit:
    build: ./algokit-microservice
    ports:
      - "3000:3000"
    environment:
      - ALGORAND_NETWORK=mainnet

volumes:
  mariadb_data:
  mongodb_data:
  redis_data:
```

---

## 📊 MONITORING & OBSERVABILITY

### **ULM Logging**

Ogni fase tracciata:
- `RAG_START`, `RAG_OK`, `RAG_SEARCH_FAILED`
- `EMBED_UPSERT_OK`, `EMBED_UPSERT_FAIL`
- `AI_CHAT_START`, `AI_CHAT_OK`, `AI_CHAT_FAILED`

**Context obbligatorio:**
- `tenant_id`
- `user_id`
- `persona`
- `task_class`
- `model`
- `latency_ms`

### **UEM Error Codes**

```php
// config/error-manager.php

'AI_PROVIDER_TIMEOUT' => [
    'type' => 'error',
    'blocking' => 'semi-blocking',
    'dev_message_key' => 'error-manager::errors_2.dev.ai_provider_timeout',
    'user_message_key' => 'error-manager::errors_2.user.ai_provider_timeout',
    'http_status_code' => 504,
    'msg_to' => 'toast'
],

'AI_POLICY_NOT_FOUND' => [
    'type' => 'error',
    'blocking' => 'blocking',
    'dev_message_key' => 'error-manager::errors_2.dev.ai_policy_not_found',
    'user_message_key' => 'error-manager::errors_2.user.ai_policy_not_found',
    'http_status_code' => 500,
    'msg_to' => 'toast'
],

'RAG_SEARCH_FAILED' => [
    'type' => 'error',
    'blocking' => 'semi-blocking',
    'http_status_code' => 500,
    'msg_to' => 'toast'
],

'EMBED_UPSERT_FAILED' => [
    'type' => 'error',
    'blocking' => 'not',
    'http_status_code' => 500,
    'msg_to' => 'toast'
],

'TENANT_CONTEXT_MISSING' => [
    'type' => 'critical',
    'blocking' => 'blocking',
    'http_status_code' => 500,
    'msg_to' => 'multiple'
],
```

---

## 🌍 INTERNAZIONALIZZAZIONE

### **Lingue Supportate**

- **Italiano (it)** - Lingua principale (PA italiana)
- **Inglese (en)** - Lingua secondaria (internazionalizzazione futura)

### **Translation Files**

```
resources/lang/it/natan.php
resources/lang/en/natan.php

Contenuto:
├── Chat UI strings
├── Error messages
├── Persona names
├── Memory system labels
└── Settings labels
```

**Rationale:** Target mercato PA italiano, inglese per export futuro EU.

---

## 🔐 COMPLIANCE GDPR

### **Legal Framework**

- **GDPR**: Regolamento UE 2016/679
- **D.Lgs 33/2013**: Trasparenza PA
- **CAD**: Codice Amministrazione Digitale

### **Measures Implemented**

1. **Data Minimization**: Solo metadati pubblici processati
2. **PII Exclusion**: DataSanitizerService automatico
3. **Audit Trail**: Ogni azione loggata (AuditLogService)
4. **Right to Erasure**: CASCADE delete per tenant
5. **Encryption**: 
   - At rest: MariaDB/MongoDB encrypted volumes (AES-256)
   - In transit: TLS 1.3 per tutte le API
   - Embeddings: Non reversibili (one-way)

### **GDPR Activity Categories**

```php
use App\Enums\Gdpr\GdprActivityCategory;

// Usage examples
$this->auditService->logUserAction($user, 'rag_search', $context, 
    GdprActivityCategory::DATA_ACCESS);

$this->auditService->logUserAction($user, 'document_uploaded', $context,
    GdprActivityCategory::CONTENT_CREATION);

$this->auditService->logUserAction($user, 'ai_inference', $context,
    GdprActivityCategory::AI_PROCESSING);
```

---

## 🎯 COMPLIANCE OS3

### **Regola Zero**
- Mai assumere informazioni mancanti
- Sempre chiedere se qualcosa non è chiaro
- Ricerca prima di presumere

### **Statistics Rule**
- No limiti nascosti in query
- `?int $limit = null` sempre esplicito
- Default = ALL records

### **I18N**
- Zero testo hardcoded
- Sempre `__('natan.key')`
- IT + EN supportate

### **UEM First**
- Mai sostituire UEM con logging generico
- UEM = alert team, ULM = trace operations
- Coesistono, non si sostituiscono

### **GDPR**
- Audit trail obbligatorio
- DataSanitizer prima di API esterne
- `tenant_id` in ogni log

---

## 📚 RIFERIMENTI TECNICI

### **Documenti Validi (DA USARE)**

1. `README_STACK.md` - Stack definitivo
2. `hybrid_database_implementation.md` - Database architecture
3. `natan_os_3_docs_batch_1` - Gateway AI spec
4. `natan_os_3_docs_batch_2` - Security spec
5. `NATAN_LOC_STARTING_DOCUMENT.md` - Starting guide
6. `ARCHITECTURE_INCONSISTENCIES_REPORT.md` - Questo report

### **Documenti Deprecated (NON USARE)**

- `NATAN_STATE_OF_THE_ART.md` → Riferisce a NATAN_PA demo
- `NATAN_STAGING_DEPLOYMENT.md` → Deploy NATAN_PA
- `NATAN_QUICK_DEPLOY.sh` → Script NATAN_PA
- Altri → Reference only per feature ideas

---

## ✅ CHECKLIST CONFORMITÀ

Prima di sviluppare NATAN_LOC:

- [ ] Stack da README_STACK.md verificato
- [ ] Database schema MariaDB + MongoDB (NO PostgreSQL)
- [ ] Python FastAPI structure definita
- [ ] Multi-tenant pattern `stancl/tenancy` compreso
- [ ] Repository interfaces per anti-lock-in
- [ ] AI Gateway policy-based (NO hardcoded provider)
- [ ] Frontend TypeScript puro (NO Vue 3)
- [ ] GDPR ULM/UEM/Audit integration chiara
- [ ] Tenant isolation in MariaDB + MongoDB
- [ ] I18N IT + EN only
- [ ] **USE Pipeline** (Ultra Semantic Engine) implementata:
  - [ ] Question Classifier
  - [ ] Execution Router
  - [ ] Retriever (chunk con source_ref)
  - [ ] Neurale Strict (claims atomici)
  - [ ] Logical Verifier (URS calculation)
  - [ ] Renderer (colori + badge + link)
- [ ] MongoDB Collections USE:
  - [ ] sources (fonti atomiche)
  - [ ] claims (affermazioni verificate)
  - [ ] query_audit (audit granulare)

---

## 🧠 USE (Ultra Semantic Engine) - ARCHITETTURA ADOTTATA

**DECISIONE:** NATAN_LOC userà **USE** come architettura anti-allucinazione.

**Rationale:** (da ARCHITECTURE_COMPARISON_USE_vs_STANDARD.md)
- Affidabilità critica per PA (score 10/10 vs 3/10)
- URS claim-level verification
- Trade-off accettabili: +50% costo, +60% tempo sviluppo
- Differenziale competitivo: "Non immagina, dimostra" (strutturale)

**Componenti USE da implementare:**
1. Question Classifier
2. Execution Router
3. Retriever (source_ref precisi)
4. Neurale Strict (claims atomici)
5. Logical Verifier (URS calculation)
6. Renderer (colori + badge + link)

**MongoDB Collections USE:**
- `sources` - Fonti atomiche con URL#page=X
- `claims` - Affermazioni singole con URS
- `query_audit` - Audit granulare
- `documents` - Documenti completi (base)

**Riferimento completo:** Vedi `USE_IMPLEMENTATION.md`

---

**Fine Documento Master - Unica Verità Architetturale NATAN_LOC**

**Architettura adottata:** USE (Ultra Semantic Engine)  
**Questo è il SINGOLO documento di riferimento per sviluppo NATAN_LOC.**  
**Tutti gli altri documenti sono deprecated o reference-only.**

