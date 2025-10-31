# NATAN_LOC - STARTING DOCUMENT

**Version:** 1.0.0  
**Date:** 2025-10-30  
**Author:** Padmin D. Curtis (CTO OS3) + Fabio Cherici  
**Purpose:** Analysis of existing NATAN_PA code in `/home/fabio/EGI` to build NATAN_LOC in `/home/fabio/NATAN_LOC`

---

## 🎯 EXECUTIVE SUMMARY

**NATAN_LOC** è l'evoluzione enterprise di NATAN_PA:
- **NATAN_PA** (attuale in `/home/fabio/EGI`): Demo single-tenant per Comune Firenze
- **NATAN_LOC** (nuovo in `/home/fabio/NATAN_LOC`): SaaS multi-tenant completo con MongoDB + Python FastAPI

**Deployment:**
- NATAN_LOC girerà su `https://natan.florenceegi.com` (sottodominio)
- NATAN_PA rimane in EGI come demo/showcase

---

## 📋 STACK TECNOLOGICO (DA README_STACK.md)

### **FRONTEND**
- TypeScript / JavaScript puro (NO React/Vue/Angular)
- Tailwind CSS
- Vite (build tool)
- WCAG 2.1 AA compliance

### **BACKEND CORE**
- PHP 8.3
- Laravel 12
- Multi-tenant: `stancl/tenancy`
- Auth: Laravel Sanctum + Spatie Permission
- Media: Spatie medialibrary

### **AI / SCRAPING**
- Python 3.12
- FastAPI (REST endpoints)
- Uvicorn (ASGI server)
- Libraries: `requests`, `beautifulsoup4`, `openai`, `anthropic`, `pymongo`, `numpy`, `celery`

### **DATABASE**
- **MariaDB**: Transazionale (users, pa_entities, pa_acts, permissions)
- **MongoDB**: Cognitivo (embeddings, chat_messages, ai_logs, analytics)
- **Redis**: Cache + Queue

### **BLOCKCHAIN**
- Algorand (AlgoKit / SDK JS)

### **COMPLIANCE**
- ULM (UltraLogManager)
- UEM (ErrorManagerInterface)
- AuditLogService (GDPR)
- ConsentService (GDPR)

---

## 📁 ANALISI CODICE ESISTENTE NATAN_PA

### **CONTROLLERS (1 file)**

```
app/Http/Controllers/PA/NatanChatController.php
├── index() - Show chat interface
├── sendMessage() - Process user message
├── getSuggestions() - Get suggested questions
├── getHistory() - Chat history (GDPR)
├── getSession() - Get specific session
├── deleteSession() - Delete session
├── searchPreview() - Preview search results
├── analyzeActs() - Analyze acts (standard)
├── analyzeActsStream() - SSE streaming (real-time)
├── getChunkingProgress() - Chunking progress
├── getChunkingFinal() - Chunking final result
├── estimateCost() - AI cost estimation
├── getUserMemories() - User memory list
├── getMemoryStats() - Memory statistics
├── getGreeting() - Personalized greeting
├── storeMemoryManual() - Store memory manually
├── deleteMemory() - Delete memory
└── toggleMemorySystem() - Enable/disable memory
```

**Dependencies:**
- NatanChatService
- NatanIntelligentChunkingService
- AiCreditsService
- AuditLogService
- ConsentService
- UltraLogManager
- ErrorManagerInterface

**Status**: ✅ COMPLETO - Da portare in NATAN_LOC con refactoring multi-tenant

---

### **SERVICES (7+ files)**

#### **Core Services**

```
app/Services/NatanChatService.php (v4.1.0)
├── processQuery() - Main orchestrator
│   ├── Persona selection (auto/manual)
│   ├── RAG retrieval (semantic + keyword fallback)
│   ├── Priority RAG (project docs > chat > acts)
│   ├── Web Search integration
│   ├── Context fusion (RAG + Web)
│   ├── Adaptive retry (rate limit handling)
│   └── GDPR audit logging
└── buildProjectContextSummary() - Helper for project context
```

**Dependencies:**
- AnthropicService
- RagService
- WebSearchService
- WebSearchAutoDetector
- DataSanitizerService
- PersonaSelector
- ConsentService
- AuditLogService

**Status**: ✅ COMPLETO - Core logic pronto per NATAN_LOC

---

```
app/Services/NatanMemoryService.php
├── detectMemoryCommand() - Detect "ricorda", "memorizza"
├── storeMemory() - Save user memory
├── getRelevantMemories() - Retrieve relevant memories
├── formatMemoriesForPrompt() - Format for Claude
├── generateGreeting() - Personalized greeting
└── shouldUseMemory() - Memory relevance detection
```

**Status**: ✅ COMPLETO - Sistema memoria persistente

---

```
app/Services/Natan/NatanIntelligentChunkingService.php
├── Chunking strategy for large datasets
├── Token-based chunking
├── Progressive context reduction
└── Cost estimation
```

**Status**: ✅ COMPLETO - Gestione dataset grandi (migliaia di atti)

---

#### **Web Search Services**

```
app/Services/WebSearch/
├── WebSearchService.php - Multi-provider (Perplexity + Google)
├── KeywordSanitizerService.php - GDPR-safe sanitization
├── WebSearchAutoDetector.php - Auto-enable detection
├── NormativeMonitoringService.php - Regulatory alerts
├── FundingOpportunitiesService.php - Funding search
├── CompetitorIntelligenceService.php - Benchmark comuni
└── WebSearchAnalyticsService.php - Usage metrics
```

**Status**: ✅ COMPLETO - Sistema web search integrato

---

### **MODELS (3 files)**

```
app/Models/NatanChatMessage.php
├── Fields: user_id, session_id, role, content, persona_*, rag_*, web_search_*, ai_model, tokens_*
├── Relations: user(), referenceMessage(), elaborations()
├── Scopes: forSession(), forUser(), userMessages(), assistantMessages(), byPersona(), recent()
└── Helpers: isAssistant(), isUser(), getPersonaInfo(), getRagInfo(), getApiStats()
```

**Status**: ✅ COMPLETO - Schema chat message completo

---

```
app/Models/NatanUserMemory.php
├── Fields: user_id, memory_content, memory_type, keywords, usage_count, last_used_at, is_active
├── Scopes: active(), forUser(), ofType()
└── Methods: markAsUsed(), searchRelevant()
```

**Status**: ✅ COMPLETO - Sistema memoria utente

---

```
app/Models/NatanUnifiedContext.php
├── Unified context management
└── Cross-source context aggregation
```

**Status**: ⚠️ DA VERIFICARE - Ruolo non chiaro

---

### **MIGRATIONS (6+ files)**

```
2025_10_23_201039_create_natan_chat_messages_table.php
├── Schema base: user_id, session_id, role, content
├── Persona fields: persona_id, persona_name, persona_confidence, etc.
├── RAG fields: rag_sources, rag_acts_count, rag_method
└── AI fields: ai_model, tokens_input, tokens_output, response_time_ms
```

```
2025_10_23_213900_add_reference_message_id_to_natan_chat_messages_table.php
└── Support per elaborazioni iterative
```

```
2025_10_26_230403_add_web_search_fields_to_natan_chat_messages_table.php
└── web_search_enabled, web_search_provider, web_search_results, web_search_count
```

```
2025_10_27_085632_add_project_id_to_natan_chat_messages.php
└── Link to Projects system (priority RAG)
```

```
2025_10_27_121310_add_priority_rag_to_natan_chat_messages_rag_method_enum.php
└── rag_method ENUM extension (semantic, keyword, priority_rag)
```

```
2024_10_29_create_natan_user_memories_table.php
└── Persistent memory system
```

```
2025_10_30_081142_create_natan_unified_context_table.php
└── Unified context (da verificare)
```

**Status**: ✅ SCHEMA COMPLETO - Da adattare per multi-tenant (aggiungere tenant_id)

---

### **CONFIG (1 file)**

```
config/natan.php
├── claude_context_limit: 100 (max acts per request)
├── claude_context_limit_minimum: 5 (adaptive retry min)
├── max_tokens_per_call: 180000
├── reserved_tokens_system: 2000
├── reserved_tokens_output: 8000
├── avg_tokens_per_char: 0.25
├── slider_min_acts: 50
├── slider_max_acts: 5000
├── slider_default_acts: 500
├── cost_per_chunk: 0.09 EUR
├── time_per_chunk_seconds: 10
├── min_relevance_score: 0.3
├── chunking_strategy: 'token-based'
├── enable_progress_tracking: true
├── rate_limit_max_retries: 3
└── rate_limit_initial_delay_seconds: 2
```

**Status**: ✅ COMPLETO - Configurazione production-ready

---

### **VIEWS (Frontend)**

```
resources/views/pa/natan/
├── chat.blade.php - Main chat interface
├── _ai-processing-panel.blade.php - Real-time progress
├── _ai-cost-preview-modal.blade.php - Cost estimation
├── _chat-settings-modal.blade.php - Settings panel
└── partials/ - UI components
```

**Status**: ✅ COMPLETO - UI pronta (da convertire in TypeScript/JS puro per NATAN_LOC)

---

### **ROUTES (pa-enterprise.php)**

```php
Route::prefix('/natan')->name('natan.')->group(function () {
    // Chat
    Route::get('/chat', 'index');
    Route::post('/chat/message', 'sendMessage');
    Route::get('/chat/suggestions', 'getSuggestions');
    
    // History (GDPR)
    Route::get('/chat/history', 'getHistory');
    Route::get('/chat/session/{sessionId}', 'getSession');
    Route::delete('/chat/session/{sessionId}', 'deleteSession');
    
    // Chunking
    Route::post('/search-preview', 'searchPreview');
    Route::post('/analyze', 'analyzeActs');
    Route::get('/chunking-progress/{sessionId}', 'getChunkingProgress');
    Route::get('/chunking-final/{sessionId}', 'getChunkingFinal');
    
    // SSE Streaming
    Route::post('/analyze-stream', 'analyzeActsStream');
    
    // Cost Tracking
    Route::post('/estimate-cost', 'estimateCost');
    
    // Memory System
    Route::prefix('/memory')->name('memory.')->group(function () {
        Route::get('/', 'getUserMemories');
        Route::get('/stats', 'getMemoryStats');
        Route::get('/greeting', 'getGreeting');
        Route::post('/store', 'storeMemoryManual');
        Route::delete('/{memoryId}', 'deleteMemory');
        Route::patch('/toggle', 'toggleMemorySystem');
    });
});
```

**Status**: ✅ COMPLETO - API REST completa

---

### **TRANSLATIONS (2 lingue - IT + EN)**

```
resources/lang/{it,en}/natan.php
├── Chat UI strings
├── Error messages
├── Persona names
├── Memory system
└── Settings labels
```

**Status**: ✅ COMPLETO - i18n IT/EN (sufficiente per mercato PA italiano)

---

### **TESTS**

```
tests/Feature/NatanWebSearchTest.php
└── Web search integration tests
```

**Status**: ⚠️ PARZIALE - Serve test coverage completa

---

### **MIDDLEWARE**

```
app/Http/Middleware/AuthenticateNatanAgent.php
└── Authentication for NATAN agents
```

**Status**: ✅ PRESENTE - Da verificare utilizzo

---

### **CONFIG CLASSES**

```
app/Config/NatanPersonas.php
└── 6 personas configuration (Strategic, Financial, Legal, Technical, Urban, Communication)
```

**Status**: ✅ COMPLETO - Sistema multi-persona

---

## 🔄 COSA CAMBIA IN NATAN_LOC

### **ARCHITETTURA**

| Componente | NATAN_PA (EGI) | NATAN_LOC (nuovo) |
|------------|----------------|-------------------|
| **Database** | MariaDB (single tenant) | MariaDB + MongoDB (multi-tenant) |
| **Embeddings** | MariaDB JSON column | MongoDB collection con vector search |
| **AI Backend** | PHP (AnthropicService) | Python FastAPI + AI Gateway |
| **Anti-Allucinazione** | Prompt engineering only | **USE (Ultra Semantic Engine)** |
| **Tenancy** | Implicito (user-based) | Esplicito (`tenant_id` ovunque) |
| **Frontend** | Blade + Alpine.js | TypeScript/JS puro + Vite |
| **Deployment** | Monolite Laravel | Laravel + Python microservices |
| **Affidabilità** | Response-level (trust AI) | **Claim-level (URS verification)** |

### **FEATURES DA PORTARE**

✅ **Mantenere:**
- Multi-persona system (6 esperti)
- Memory system (persistent)
- Cost tracking
- SSE streaming
- GDPR audit trail
- Web search integration

🔄 **Evolvere con USE (Ultra Semantic Engine):**
- Single-tenant → Multi-tenant vero
- MariaDB embeddings → MongoDB embeddings
- AnthropicService PHP → Python FastAPI + AI Gateway
- RAG simple → **USE Pipeline (7 fasi)**:
  - Question Classifier
  - Execution Router
  - Retriever (chunks citabili)
  - Neurale Strict (claims atomici)
  - Logical Verifier (URS)
  - Renderer (colori + badge)
  - Audit granulare
- Response generica → **Claims verificati con URS**
- Fonti generiche → **Fonti atomiche (source_ref precisi)**
- Trust AI → **Verify AI (post-generation check)**
- Blade views → TypeScript components
- Laravel-only → Laravel + Python microservices

❌ **Rimuovere:**
- Alpine.js (vietato da regole)
- Livewire (se presente)
- jQuery (deprecato)
- Adaptive retry semplice → sostituito con Router logic USE

---

## 📦 COMPONENTI DA MIGRARE

### **PRIORITY 1 - CORE (Blocking)**

1. **Database Schema**
   - Tabelle MariaDB con `tenant_id`
   - MongoDB collections (embeddings, chat, logs)
   - Migration script per multi-tenant

2. **Multi-Tenant System**
   - `stancl/tenancy` setup
   - Middleware tenancy detection
   - Global scopes per tenant isolation

3. **AI Gateway (Python FastAPI)**
   - `/embed` endpoint (OpenAI/Ollama)
   - `/chat` endpoint (Claude/Ollama)
   - `/rag/search` endpoint (vector similarity)
   - Policy engine (YAML-based routing)

4. **Repository Pattern**
   - `PaActRepositoryInterface` → MariaDB adapter
   - `EmbeddingRepositoryInterface` → MongoDB adapter
   - Anti-lock-in design

---

### **PRIORITY 2 - USE PIPELINE (Ultra Semantic Engine)**

5. **Question Classifier**
   - AI leggero per intent detection
   - Confidence scoring
   - Constraint extraction

6. **Execution Router**
   - Logica deterministica
   - Decision: direct_query | RAG_strict | block
   - Può rispondere senza AI (query semplici)

7. **Retriever**
   - Estrae chunks citabili con source_ref precisi
   - URL#page=X (link esatti)
   - MongoDB vector search

8. **Neurale Strict**
   - LLM genera claims atomici (1 frase = 1 claim)
   - Ogni claim DEVE avere source_ids
   - Struttura JSON validata

9. **Logical Verifier**
   - Verifica post-generazione OGNI claim
   - Calcola URS (Ultra Reliability Score)
   - Blocca claims con URS < 0.5
   - Assegna label A/B/C/X

10. **Renderer**
    - HTML con colori per affidabilità
    - Badge URS score
    - Link clickable su claim → fonte esatta
    - Badge "[Deduzione]" per inferenze

11. **Memory System**
    - NatanUserMemory model
    - NatanMemoryService
    - Tenant-scoped memories

---

### **PRIORITY 3 - UI/UX**

8. **Frontend TypeScript**
   - Convertire Blade → TS/JS puro
   - Vite build pipeline
   - Tailwind CSS mantenuto
   - WCAG 2.1 AA compliance

9. **SSE Streaming**
   - Real-time progress panels
   - Event-driven UI updates
   - Cost tracking live

---

### **PRIORITY 4 - COMPLIANCE**

10. **GDPR System**
    - Tenant-scoped audit logs
    - Data sanitization
    - Consent management per tenant
    - Right to erasure (cascade per tenant)

11. **ULM/UEM Integration**
    - Logging per tenant
    - Error tracking per tenant
    - Alerting configurabile

---

## 🗄️ DATABASE SCHEMA NATAN_LOC

### **MariaDB (Transazionale)**

```sql
-- Tenants table
CREATE TABLE pa_entities (
    id BIGINT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    domain VARCHAR(255), -- natan-firenze.florenceegi.com
    settings JSON,
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    INDEX idx_slug (slug),
    INDEX idx_domain (domain)
);

-- Users (with tenant_id)
ALTER TABLE users ADD COLUMN tenant_id BIGINT;
ALTER TABLE users ADD FOREIGN KEY (tenant_id) REFERENCES pa_entities(id);
ALTER TABLE users ADD INDEX idx_users_tenant (tenant_id);

-- PA Acts (with tenant_id)
CREATE TABLE pa_acts (
    id BIGINT PRIMARY KEY,
    tenant_id BIGINT NOT NULL,
    user_id BIGINT NOT NULL,
    protocol_number VARCHAR(255),
    protocol_date DATE,
    act_type VARCHAR(100),
    title TEXT,
    description LONGTEXT,
    jsonMetadata JSON, -- financial_data, codes
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

-- Chat Messages (with tenant_id)
CREATE TABLE natan_chat_messages (
    id BIGINT PRIMARY KEY,
    tenant_id BIGINT NOT NULL,
    user_id BIGINT NOT NULL,
    session_id VARCHAR(100) NOT NULL,
    project_id BIGINT NULL,
    role ENUM('user', 'assistant') NOT NULL,
    content LONGTEXT NOT NULL,
    reference_message_id BIGINT NULL,
    persona_id VARCHAR(50),
    persona_name VARCHAR(100),
    persona_confidence FLOAT,
    rag_sources JSON,
    rag_acts_count INT DEFAULT 0,
    rag_method ENUM('semantic', 'keyword', 'priority_rag'),
    web_search_enabled BOOLEAN DEFAULT false,
    web_search_provider VARCHAR(50),
    web_search_results JSON,
    ai_model VARCHAR(100),
    tokens_input INT,
    tokens_output INT,
    response_time_ms INT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES pa_entities(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_tenant_user (tenant_id, user_id),
    INDEX idx_tenant_session (tenant_id, session_id),
    INDEX idx_tenant_project (tenant_id, project_id)
);

-- User Memories (with tenant_id)
CREATE TABLE natan_user_memories (
    id BIGINT PRIMARY KEY,
    tenant_id BIGINT NOT NULL,
    user_id BIGINT NOT NULL,
    memory_content TEXT NOT NULL,
    memory_type VARCHAR(50),
    keywords JSON,
    usage_count INT DEFAULT 0,
    last_used_at TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES pa_entities(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_tenant_user (tenant_id, user_id),
    INDEX idx_tenant_type (tenant_id, memory_type)
);
```

---

### **MongoDB (Cognitivo)**

```javascript
// Collection: pa_act_embeddings
{
    _id: ObjectId,
    tenant_id: Number,          // OBBLIGATORIO
    act_id: Number,             // FK logica a MariaDB pa_acts.id
    embedding: [Float],         // 1536 dimensions (text-embedding-3-small)
    model_name: String,         // "text-embedding-3-small"
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
// Optional: vector index se supportato
// db.pa_act_embeddings.createIndex({ embedding: "vector", dims: 1536, similarity: "cosine" });

// Collection: natan_ai_logs
{
    _id: ObjectId,
    tenant_id: Number,
    user_id: Number,
    event: String,              // "rag_search", "ai_inference", "embedding_generated"
    context: Object,
    latency_ms: Number,
    tokens_used: Number,
    cost_usd: Number,
    created_at: ISODate
}

// Indici
db.natan_ai_logs.createIndex({ tenant_id: 1, created_at: -1 });
db.natan_ai_logs.createIndex({ tenant_id: 1, event: 1 });

// Collection: natan_analytics
{
    _id: ObjectId,
    tenant_id: Number,
    date: ISODate,
    metrics: {
        queries_count: Number,
        avg_response_time_ms: Number,
        total_tokens: Number,
        total_cost_usd: Number,
        acts_analyzed: Number
    }
}

// Indici
db.natan_analytics.createIndex({ tenant_id: 1, date: -1 });
```

---

## 🐍 PYTHON FASTAPI MICROSERVICE (NUOVO)

### **Endpoints da Creare**

```python
# app/main.py (FastAPI)

from fastapi import FastAPI, HTTPException
from pydantic import BaseModel
import openai
import anthropic
from pymongo import MongoClient

app = FastAPI(title="NATAN AI Gateway")

# Models
class EmbedRequest(BaseModel):
    text: str
    tenant_id: int
    model: str = "text-embedding-3-small"

class ChatRequest(BaseModel):
    messages: list
    tenant_id: int
    persona: str
    model: str = "claude-3-5-sonnet"

class RagSearchRequest(BaseModel):
    query: str
    tenant_id: int
    limit: int = 10

# Endpoints
@app.post("/embed")
async def generate_embedding(req: EmbedRequest):
    """Generate text embedding"""
    # OpenAI or Ollama based on config
    pass

@app.post("/chat")
async def chat_inference(req: ChatRequest):
    """LLM inference with persona"""
    # Claude or Ollama based on policy
    pass

@app.post("/rag/search")
async def rag_search(req: RagSearchRequest):
    """Vector similarity search in MongoDB"""
    # Cosine similarity + tenant filtering
    pass

@app.get("/healthz")
async def health_check():
    return {"status": "ok", "version": "1.0.0"}
```

**Status**: 🆕 DA CREARE - Core AI microservice

---

## 🔌 INTEGRATION PATTERN (Laravel ↔ Python)

### **Laravel chiama Python FastAPI**

```php
// app/Services/AI/AiGatewayService.php (NUOVO in NATAN_LOC)

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
        
        if (!$response->successful()) {
            throw new \Exception('AI Gateway embed failed');
        }
        
        return $response->json();
    }
    
    public function chat(array $messages, int $tenantId, string $persona): array {
        $response = Http::timeout(120)->post($this->baseUrl . '/chat', [
            'messages' => $messages,
            'tenant_id' => $tenantId,
            'persona' => $persona,
            'model' => config('ai.chat_model')
        ]);
        
        if (!$response->successful()) {
            throw new \Exception('AI Gateway chat failed');
        }
        
        return $response->json();
    }
    
    public function ragSearch(string $query, int $tenantId, int $limit = 10): array {
        $response = Http::timeout(30)->post($this->baseUrl . '/rag/search', [
            'query' => $query,
            'tenant_id' => $tenantId,
            'limit' => $limit
        ]);
        
        if (!$response->successful()) {
            throw new \Exception('AI Gateway RAG search failed');
        }
        
        return $response->json();
    }
}
```

---

## 📋 ROADMAP IMPLEMENTAZIONE NATAN_LOC

### **FASE 1: Foundation (Week 1-2)**

**Database Setup**
- [ ] Setup MariaDB con `stancl/tenancy`
- [ ] Setup MongoDB connessione
- [ ] Creare tabelle multi-tenant (pa_entities, users, pa_acts, natan_chat_messages)
- [ ] Creare MongoDB collections (embeddings, logs, analytics)
- [ ] Migration script da NATAN_PA schema

**Python FastAPI Microservice**
- [ ] Setup progetto Python (`pyproject.toml`, `requirements.txt`)
- [ ] FastAPI app structure
- [ ] Endpoints: `/embed`, `/chat`, `/rag/search`, `/healthz`
- [ ] MongoDB connection (PyMongo)
- [ ] OpenAI client setup
- [ ] Anthropic client setup
- [ ] Ollama client setup (local mode)

**Tenancy Middleware**
- [ ] Tenancy detection (subdomain/header/user)
- [ ] Global scope MariaDB
- [ ] MongoDB tenant filtering
- [ ] Config override per tenant

---

### **FASE 2: Core Services (Week 3-4)**

**Repository Pattern**
- [ ] `PaActRepositoryInterface` + `MariaDBPaActRepository`
- [ ] `EmbeddingRepositoryInterface` + `MongoEmbeddingRepository`
- [ ] `ChatMessageRepositoryInterface` + implementation

**AI Services**
- [ ] `AiGatewayService` (Laravel → Python FastAPI)
- [ ] `RagService` (refactor per MongoDB)
- [ ] `NatanChatService` (refactor multi-tenant)
- [ ] `NatanMemoryService` (tenant-scoped)

**GDPR Services**
- [ ] `DataSanitizerService` (tenant-aware)
- [ ] `AuditLogService` (tenant-scoped)
- [ ] `ConsentService` (tenant-scoped)

---

### **FASE 3: API & Controllers (Week 5)**

**REST API**
- [ ] `NatanChatController` (tenant-aware)
- [ ] Authentication (Sanctum token-based)
- [ ] Rate limiting per tenant
- [ ] CORS configuration

**Endpoints**
- [ ] Chat message
- [ ] Chat history
- [ ] Memory system
- [ ] Cost estimation
- [ ] SSE streaming

---

### **FASE 4: Frontend (Week 6-7)**

**TypeScript/JS Setup**
- [ ] Vite configuration
- [ ] TypeScript setup
- [ ] Tailwind CSS
- [ ] Component structure

**UI Components**
- [ ] Chat interface
- [ ] Persona selector
- [ ] Cost preview
- [ ] Progress panels (SSE)
- [ ] Memory management
- [ ] Settings modal

---

### **FASE 5: Testing & Deploy (Week 8)**

**Testing**
- [ ] Tenant isolation tests
- [ ] RAG accuracy tests
- [ ] GDPR audit tests
- [ ] API integration tests
- [ ] Frontend E2E tests

**Deployment**
- [ ] Docker setup (Laravel + Python + MariaDB + MongoDB + Redis)
- [ ] CI/CD pipeline
- [ ] Staging environment
- [ ] Production deployment su `natan.florenceegi.com`

**Translations**
- [ ] Italian (it) - Lingua principale
- [ ] English (en) - Lingua secondaria (internazionalizzazione futura)

---

## 🔍 FILES DA ANALIZZARE DETTAGLIATAMENTE

**Prima di iniziare sviluppo NATAN_LOC, analizzare:**

1. ✅ `app/Services/NatanChatService.php` - Logic principale
2. ✅ `app/Services/RagService.php` - Semantic search
3. ✅ `app/Services/AnthropicService.php` - API integration pattern
4. ✅ `app/Models/NatanChatMessage.php` - Schema completo
5. ⚠️ `app/Services/Natan/NatanIntelligentChunkingService.php` - Chunking logic
6. ⚠️ `app/Services/NatanMemoryService.php` - Memory system
7. ⚠️ `app/Config/NatanPersonas.php` - Personas configuration
8. ⚠️ `resources/views/pa/natan/chat.blade.php` - UI structure
9. ⚠️ Database migrations complete list
10. ⚠️ Translation keys structure

---

## 📝 NEXT STEPS

1. **Fabio conferma:**
   - ✅ Stack corretto (da README_STACK.md)
   - ✅ Architettura ibrida MariaDB + MongoDB
   - ✅ Python FastAPI per AI layer
   - ✅ TypeScript/JS puro frontend
   
2. **Analisi approfondita codice esistente:**
   - Leggere tutti i service methods
   - Identificare dependencies
   - Mappare DB schema completo
   - Estrarre business logic riutilizzabile

3. **Creare documento tecnico dettagliato:**
   - Architecture design NATAN_LOC
   - Database schema completo
   - API specifications (Python FastAPI)
   - Migration plan da NATAN_PA
   - Testing strategy

4. **Setup nuovo progetto `/home/fabio/NATAN_LOC`:**
   - Laravel 12 installation
   - Python FastAPI project structure
   - Database setup (MariaDB + MongoDB + Redis)
   - Development environment

---

## ✅ CHECKLIST CONFORMITÀ OS3

Prima di iniziare NATAN_LOC:

- [ ] REGOLA ZERO: Nessuna assunzione, tutto verificato da codice esistente
- [ ] STATISTICS RULE: No limiti nascosti in query
- [ ] I18N: Zero testo hardcoded
- [ ] UEM/ULM: Integration completa
- [ ] GDPR: Audit trail per tenant
- [ ] Multi-tenant: `tenant_id` ovunque
- [ ] Repository pattern: Anti-lock-in
- [ ] Testing: Coverage >80%
- [ ] Documentation: Complete per ogni component

---

**Questo documento è la BASE per sviluppo NATAN_LOC.**

**Prossimo step:** Analisi dettagliata codice esistente o inizio setup nuovo progetto?

---

**Fine documento STARTING - v1.0.0**

