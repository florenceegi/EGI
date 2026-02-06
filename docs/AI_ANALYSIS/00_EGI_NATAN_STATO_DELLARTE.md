# 📊 EGI NATAN - Stato dell'Arte del Progetto

**Versione**: 0.1.0 (Alpha)
**Data**: 2026-02-06
**Ultimo Aggiornamento**: 2026-02-06 (Fase B - Schema PostgreSQL Design Completato)
**Autore**: Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
**Contesto**: FlorenceEGI - EGI Platform AI Assistant
**Progetto Base**: NATAN_LOC (adattamento per documenti strutturati)

---

## 🎯 Executive Summary

**EGI NATAN** è un sistema RAG (Retrieval-Augmented Generation) avanzato per assistenza AI integrata nella piattaforma FlorenceEGI, progettato per fornire risposte accurate e verificate basate su documentazione ufficiale strutturata.

**Status Attuale**: 🚧 **ALPHA - DESIGN FASE COMPLETATA** - Architettura RAG di NATAN_LOC analizzata, schema PostgreSQL progettato, pronto per implementazione Laravel.

**Obiettivo**: Integrare **Natan Assistant** nell'AI Sidebar di EGI con RAG-Fortress anti-hallucination per rispondere a domande su:
- Funzionalità piattaforma
- Guide creator/collector/company
- GDPR compliance
- Marketplace e NFT/ASA
- Sistemi di pagamento (Stripe)

**Deployment Target**:
- **Production**: `https://florenceegi.com` (existing Laravel app)
- **Database**: PostgreSQL (stesso DB di EGI)
- **AI Service**: Python FastAPI (da adattare da NATAN_LOC)

---

## 🏗️ Architettura Generale

### **Pattern Architetturale**

```
┌─────────────────────────────────────────────────────────────┐
│                 EGI FRONTEND (Blade + Livewire)              │
│  Porta: 80/443 | Laravel 11 | Tailwind CSS                   │
├─────────────────────────────────────────────────────────────┤
│  🤖 AI SIDEBAR (Existing Component)                          │
│     ├─ Chat Interface                                        │
│     ├─ Onboarding Checklist                                  │
│     └─ Natan Assistant (NEW - RAG Integration)               │
└────────────────────┬────────────────────────────────────────┘
                     │ HTTP/REST API
┌────────────────────▼────────────────────────────────────────┐
│              LARAVEL BACKEND (PHP 8.2+)                      │
│  Laravel 11 | Spatie Media | Ultra Packages                  │
│                                                              │
│  📄 NEW: RAG Services                                        │
│  ├─ DocumentIndexingService (MD → PostgreSQL)               │
│  ├─ NatanQueryService (User Question → RAG Pipeline)        │
│  └─ NatanResponseService (Format & Cache)                   │
└─────────┬─────────────────────┬──────────────────────────────┘
          │                     │
  ┌───────▼────────┐    ┌───────▼────────┐
  │  PostgreSQL    │    │  Python FastAPI │
  │  (EGI DB)      │    │  (AI Gateway)   │
  │                │    │  Porta: 8080    │
  │  NEW TABLES:   │    └───────┬────────┘
  │  rag_documents │            │
  │  rag_chunks    │    ┌───────▼────────┐
  │  rag_embeddings│    │  OpenAI API    │
  │  rag_queries   │    │  (Embeddings)  │
  │  rag_responses │    │                │
  └────────────────┘    │  Anthropic API │
                        │  (Claude)      │
                        └────────────────┘
```

### **Differenze Chiave vs NATAN_LOC**

| Aspetto | NATAN_LOC | EGI NATAN |
|---------|-----------|-----------|
| **Documenti** | PDF eterogenei PA | Markdown strutturati (docs/) |
| **Database** | MongoDB Atlas | PostgreSQL (stesso DB EGI) |
| **Vector Search** | MongoDB $vectorSearch | pgvector (IVFFlat/HNSW) |
| **Multi-tenant** | stancl/tenancy | Singolo tenant (FlorenceEGI) |
| **Use Case** | Albi Pretori, atti PA | Platform docs, guides, FAQ |
| **Utenti** | PA (comuni) | Creator, Collector, Company |
| **Integrazione** | Standalone app | Integrato in EGI esistente |

---

## 📦 Stack Tecnologico

### **Backend Laravel (Existing + NEW)**

| Componente | Versione | Scopo | Status |
|------------|----------|-------|--------|
| Laravel | 11.x | Framework core | ✅ Existing |
| PostgreSQL | 15+ | Database relazionale | ✅ Existing |
| **pgvector** | **0.5.1** | **Vector search** | 🚧 **To Install** |
| Spatie Media | 11.x | File management | ✅ Existing |
| Ultra Packages | dev-main | ULM, UEM, UTM | ✅ Existing |

**NEW Services (To Implement)**:
- ✅ `DocumentIndexingService` - Ingest MD docs → PostgreSQL
- ✅ `NatanQueryService` - Query handling → RAG pipeline
- ✅ `NatanResponseService` - Format & cache responses
- ✅ `EmbeddingService` - Generate embeddings (OpenAI API)

---

### **Python AI Service (Adapted from NATAN_LOC)**

| Componente | Versione | Scopo | Status |
|------------|----------|-------|--------|
| FastAPI | 0.104.1 | Web framework | 🚧 To Adapt |
| asyncpg | 0.29.0 | PostgreSQL async driver | 🚧 To Add |
| pgvector | 0.2.0 | Vector ops Python | 🚧 To Add |
| openai | 1.3.0 | OpenAI API | ✅ Ready |
| anthropic | 0.7.0 | Claude API | ✅ Ready |
| numpy | 1.26.2 | Vector operations | ✅ Ready |

**Services to Adapt**:
- ✅ `HybridRetriever` - MongoDB → PostgreSQL pgvector
- ✅ `EvidenceVerifier` - Keep as-is
- ✅ `ClaimExtractor` - Keep as-is
- ✅ `ConstrainedSynthesizer` - Adapt prompts (PA → Platform)
- ✅ `HostileFactChecker` - Keep as-is
- ✅ `URSCalculator` - Keep as-is

---

### **Database Schema (PostgreSQL)**

**NEW Tables** (8 total):

```sql
-- Document Management
rag_categories       -- Hierarchical categorization
rag_documents        -- Master documents (title, content, metadata)
rag_chunks           -- Document chunks (paragraphs/sections)

-- Vector Search
rag_embeddings       -- Vector embeddings (1:1 with chunks)

-- Query & Response Management
rag_queries          -- User queries log & analytics
rag_responses        -- Generated responses + URS scores
rag_sources          -- Response citations (N:M)
rag_query_cache      -- Query result caching
```

**Indexes**:
- ✅ pgvector IVFFlat/HNSW for similarity search
- ✅ GIN indexes for JSONB, TEXT[], tsvector
- ✅ B-tree indexes for foreign keys
- ✅ Full-text search (tsvector + GIN)

**Estimated Size** (Initial):
- Documents: ~100-200 (docs/FlorenceEGI/*)
- Chunks: ~500-1000 (avg 5-10 chunks per doc)
- Embeddings: 1536 dims × 4 bytes × 1000 = ~6 MB
- Total DB: < 50 MB initial

---

## 🤖 RAG-Fortress Pipeline (Adapted from NATAN_LOC)

### **Architecture**

```
User Question
      ↓
┌─────────────────────────────────────────────────────────────┐
│          STEP 0: Query Analysis (OPTIONAL in EGI)            │
│  - No prebuilt queries (not needed for platform docs)       │
│  - Simple query classification (FAQ vs deep search)         │
└──────────────────────┬──────────────────────────────────────┘
                       ↓
┌─────────────────────────────────────────────────────────────┐
│          STEP 1: HybridRetriever (PostgreSQL)                │
│  ├─ Generate embedding (OpenAI text-embedding-3-small)      │
│  ├─ Vector search (pgvector cosine similarity)              │
│  ├─ Keyword search (tsvector full-text)                     │
│  ├─ Metadata filters (category, language, tags)             │
│  └─ Adaptive top_k (50-200 based on query specificity)      │
│     → Output: List[Chunk] with scores                        │
└──────────────────────┬──────────────────────────────────────┘
                       ↓
┌─────────────────────────────────────────────────────────────┐
│          STEP 2: EvidenceVerifier                            │
│  ├─ Filter by similarity threshold (0.45)                   │
│  ├─ Normalize scores to 0-10 scale                          │
│  └─ Keep top 50 verified evidences                          │
│     → Output: List[VerifiedEvidence]                         │
└──────────────────────┬──────────────────────────────────────┘
                       ↓
┌─────────────────────────────────────────────────────────────┐
│          STEP 3: ClaimExtractor (Llama-3.1-70B)             │
│  ├─ Extract atomic claims from verified chunks              │
│  ├─ Format: [CLAIM_XXX:chunk_id] Claim text                 │
│  ├─ Token-based limiting (max 187k tokens)                  │
│  └─ Ultra-strict: NO deduction, NO external knowledge       │
│     → Output: List["[CLAIM_001] ..."]                        │
└──────────────────────┬──────────────────────────────────────┘
                       ↓
┌─────────────────────────────────────────────────────────────┐
│          STEP 4: GapDetector                                 │
│  ├─ Identify uncovered aspects of question                  │
│  └─ Compare question vs extracted claims                    │
│     → Output: List[Gap] or ["FULL_COVERAGE"]                │
└──────────────────────┬──────────────────────────────────────┘
                       ↓
┌─────────────────────────────────────────────────────────────┐
│          STEP 5: ConstrainedSynthesizer (Claude 3.5)        │
│  ├─ Synthesize using ONLY extracted claims                  │
│  ├─ Friendly platform style (not bureaucratic)              │
│  ├─ Citation format: (CLAIM_001)(CLAIM_003)                 │
│  ├─ Gap handling: "I don't have verified info about..."     │
│  └─ Max 450 words                                           │
│     → Output: Synthesized Answer with citations             │
└──────────────────────┬──────────────────────────────────────┘
                       ↓
┌─────────────────────────────────────────────────────────────┐
│          STEP 6: HostileFactChecker (Gemini-1.5-Flash)      │
│  ├─ Hostile verification with different model               │
│  ├─ Check factual statements against allowed claims         │
│  └─ Detect hallucinations                                   │
│     → Output: List[Hallucination] or ["NESSUNA"]            │
└──────────────────────┬──────────────────────────────────────┘
                       ↓
┌─────────────────────────────────────────────────────────────┐
│          STEP 7: URSCalculator                               │
│  ├─ Base 100 - gaps - hallucinations                        │
│  ├─ Penalties: -15 per gap, -15 per hallucination           │
│  ├─ Bonus: +5 if >8 claims used                             │
│  └─ Clamp to [0, 100]                                       │
│     → Output: URS Score (0-100) + explanation               │
└──────────────────────┬──────────────────────────────────────┘
                       ↓
               Final Response
         {answer, urs_score, claims, sources}
```

### **Adaptations for EGI**

1. **Prompts**:
   - Remove PA bureaucratic style
   - Use friendly, helpful platform tone
   - Adjust for creator/collector/company audiences

2. **Categories**:
   - Platform features
   - Creator guides
   - Collector guides
   - Company guides
   - GDPR compliance
   - Marketplace/NFT
   - Payment systems

3. **Multi-language**:
   - 6 languages (it, en, de, es, fr, pt)
   - Language detection from query
   - Filtered retrieval by language

4. **No Multi-tenant**:
   - Single tenant (FlorenceEGI platform)
   - Simplifies architecture vs NATAN_LOC

---

## 🗄️ Database Schema (PostgreSQL)

> **📌 SSOT**: Vedi `docs/AI_ANALYSIS/EGI_POSTGRESQL_RAG_SCHEMA_DESIGN.md` per schema completo.

### **Core Tables**

```sql
-- 1. Document Categories
rag_categories (
    id, slug, name_key, description_key,
    icon, color, parent_id, sort_order,
    metadata JSONB, created_at, updated_at
)

-- 2. Documents Master
rag_documents (
    id, uuid, category_id,
    title, slug, content, excerpt, language,
    document_type, version, status, tags[], keywords[],
    search_vector tsvector,  -- Full-text search
    metadata JSONB,
    created_at, updated_at, published_at
)

-- 3. Document Chunks
rag_chunks (
    id, uuid, document_id (FK),
    text, section_title, chunk_order,
    char_start, char_end, token_count,
    chunk_type, language,
    search_vector tsvector,
    metadata JSONB,
    created_at, updated_at
)

-- 4. Vector Embeddings (1:1 with chunks)
rag_embeddings (
    id, chunk_id (FK) UNIQUE,
    embedding vector(1536),  -- pgvector
    model, model_version,
    created_at, updated_at
)
-- Index: IVFFlat or HNSW for vector search

-- 5. User Queries Log
rag_queries (
    id, uuid, user_id (FK),
    question, question_hash, language,
    context JSONB,
    response_id (FK), urs_score,
    was_helpful, feedback_text,
    created_at
)

-- 6. Generated Responses
rag_responses (
    id, uuid, query_id (FK),
    answer, answer_html,
    urs_score, urs_explanation,
    claims_used JSONB, gaps_detected JSONB,
    hallucinations JSONB, sources_used JSONB,
    processing_time_ms, tokens_input, tokens_output,
    cost_usd, model_used,
    is_cached, cache_key,
    created_at
)

-- 7. Response Source Citations (N:M)
rag_sources (
    id, response_id (FK), chunk_id (FK),
    claim_id, exact_quote, relevance_score,
    citation_order,
    created_at
)

-- 8. Query Result Cache
rag_query_cache (
    id, cache_key UNIQUE, question_hash,
    response_id (FK), language, context_hash,
    hit_count, last_hit_at, expires_at,
    created_at
)
```

### **Indexes Strategy**

```sql
-- Vector Search (IVFFlat for 1k-1M vectors)
CREATE INDEX idx_rag_embeddings_vector_ivfflat
    ON rag_embeddings
    USING ivfflat (embedding vector_cosine_ops)
    WITH (lists = 100);

-- Full-Text Search (GIN for tsvector)
CREATE INDEX idx_rag_documents_search_vector
    ON rag_documents USING gin(search_vector);

CREATE INDEX idx_rag_chunks_search_vector
    ON rag_chunks USING gin(search_vector);

-- JSONB Search (GIN for metadata)
CREATE INDEX idx_rag_documents_metadata
    ON rag_documents USING gin(metadata);

CREATE INDEX idx_rag_chunks_metadata
    ON rag_chunks USING gin(metadata);

-- Array Search (GIN for tags/keywords)
CREATE INDEX idx_rag_documents_tags
    ON rag_documents USING gin(tags);

CREATE INDEX idx_rag_documents_keywords
    ON rag_documents USING gin(keywords);
```

### **Triggers**

```sql
-- Auto-update search_vector on insert/update
CREATE TRIGGER rag_documents_search_vector_update
    BEFORE INSERT OR UPDATE OF title, excerpt, content, tags, keywords
    ON rag_documents
    FOR EACH ROW
    EXECUTE FUNCTION rag_documents_search_vector_trigger();

-- Auto-update updated_at on update
CREATE TRIGGER rag_documents_updated_at
    BEFORE UPDATE ON rag_documents
    FOR EACH ROW
    EXECUTE FUNCTION update_updated_at_column();
```

---

## 📁 Struttura Progetto (EGI)

```
/home/fabio/EGI/
├── app/
│   ├── Http/Controllers/
│   │   └── NatanController.php         # AI Sidebar chat endpoint
│   ├── Services/
│   │   ├── Natan/
│   │   │   ├── DocumentIndexingService.php
│   │   │   ├── NatanQueryService.php
│   │   │   ├── NatanResponseService.php
│   │   │   └── EmbeddingService.php
│   │   └── OnboardingChecklistService.php  # Existing
│   ├── Models/
│   │   ├── Rag/
│   │   │   ├── RagCategory.php
│   │   │   ├── RagDocument.php
│   │   │   ├── RagChunk.php
│   │   │   ├── RagEmbedding.php
│   │   │   ├── RagQuery.php
│   │   │   ├── RagResponse.php
│   │   │   └── RagSource.php
│   │   └── User.php                     # Existing
│   └── ...
│
├── database/migrations/
│   ├── 2026_02_07_000001_create_rag_categories_table.php
│   ├── 2026_02_07_000002_create_rag_documents_table.php
│   ├── 2026_02_07_000003_create_rag_chunks_table.php
│   ├── 2026_02_07_000004_create_rag_embeddings_table.php
│   ├── 2026_02_07_000005_create_rag_queries_table.php
│   ├── 2026_02_07_000006_create_rag_responses_table.php
│   ├── 2026_02_07_000007_create_rag_sources_table.php
│   └── 2026_02_07_000008_create_rag_query_cache_table.php
│
├── docs/
│   ├── FlorenceEGI/              # Source documents (to index)
│   │   ├── Platform_Features.md
│   │   ├── Creator_Guide.md
│   │   ├── Collector_Guide.md
│   │   ├── GDPR_Compliance.md
│   │   ├── Payment_System_Map.md
│   │   └── ...
│   └── AI_ANALYSIS/              # RAG system documentation
│       ├── 00_EGI_NATAN_STATO_DELLARTE.md  # This file
│       ├── NATAN_LOC_RAG_ARCHITECTURE_ANALYSIS.md
│       └── EGI_POSTGRESQL_RAG_SCHEMA_DESIGN.md
│
├── resources/views/components/
│   └── ai-sidebar.blade.php      # Existing - to integrate Natan RAG
│
├── public/js/
│   └── ai-sidebar.js             # Existing - to enhance with RAG UI
│
├── python_ai_service/            # NEW - Adapted from NATAN_LOC
│   ├── app/
│   │   ├── main.py
│   │   ├── routers/
│   │   │   └── natan.py          # RAG endpoints
│   │   ├── services/
│   │   │   ├── rag_fortress/
│   │   │   │   ├── retriever_pg.py      # PostgreSQL adapter
│   │   │   │   ├── evidence_verifier.py
│   │   │   │   ├── claim_extractor.py
│   │   │   │   ├── gap_detector.py
│   │   │   │   ├── constrained_synthesizer.py
│   │   │   │   ├── hostile_factchecker.py
│   │   │   │   ├── urs_calculator.py
│   │   │   │   └── pipeline.py
│   │   │   └── embedding_service.py
│   │   └── config/
│   │       └── rag_config.py
│   ├── requirements.txt
│   └── .env
│
└── scripts/
    ├── index_documents.php       # Index docs/FlorenceEGI/* → PostgreSQL
    └── test_rag_pipeline.php     # Test RAG end-to-end
```

---

## ✅ Stato Implementazione

### **FASE A: Analisi NATAN_LOC** ✅ COMPLETATA (2026-02-06)

#### **Deliverable**
- [x] ✅ Documento analisi completa: `NATAN_LOC_RAG_ARCHITECTURE_ANALYSIS.md` (800+ righe)

#### **Key Insights Estratti**
- [x] ✅ USE Pipeline: Query routing intelligente (prebuilt/aggregation/hybrid)
- [x] ✅ RAG-Fortress: 7 stadi anti-hallucination
- [x] ✅ Claim-based architecture con traceability completa
- [x] ✅ Adaptive parameters (top_k, threshold) basati su filtri
- [x] ✅ Token-based evidence limiting (max 187k tokens)
- [x] ✅ Model diversity (Gemini per hostile check vs Claude per synthesis)
- [x] ✅ URS Score (0-100) per quantificare affidabilità
- [x] ✅ Dual-mode vector search (documents vs chunks)

#### **Componenti Mappati**
- [x] ✅ `use_pipeline.py` (1200 lines) - Query routing & orchestration
- [x] ✅ `rag_fortress/pipeline.py` (3800 lines) - Main RAG pipeline
- [x] ✅ `rag_fortress/retriever.py` (1200 lines) - Hybrid retrieval
- [x] ✅ `rag_fortress/evidence_verifier.py` (106 lines)
- [x] ✅ `rag_fortress/claim_extractor.py` (280 lines)
- [x] ✅ `rag_fortress/gap_detector.py` (150 lines)
- [x] ✅ `rag_fortress/constrained_synthesizer.py` (250 lines)
- [x] ✅ `rag_fortress/hostile_factchecker.py` (340 lines)
- [x] ✅ `rag_fortress/urs_calculator.py` (92 lines)
- [x] ✅ `rag_config.py` (182 lines) - Configuration

---

### **FASE B: Design Schema PostgreSQL** ✅ COMPLETATA (2026-02-06)

#### **Deliverable**
- [x] ✅ Documento schema completo: `EGI_POSTGRESQL_RAG_SCHEMA_DESIGN.md` (1100+ righe)

#### **Tabelle Progettate (8 total)**
- [x] ✅ `rag_categories` - Categorizzazione gerarchica documenti
- [x] ✅ `rag_documents` - Master table con full-text search (tsvector)
- [x] ✅ `rag_chunks` - Chunking intelligente con metadata
- [x] ✅ `rag_embeddings` - Vector embeddings (pgvector 1536 dims)
- [x] ✅ `rag_queries` - Query log & analytics
- [x] ✅ `rag_responses` - Generated responses + URS scores
- [x] ✅ `rag_sources` - Citation tracking (N:M)
- [x] ✅ `rag_query_cache` - Query result caching

#### **Features Schema**
- [x] ✅ pgvector integration (IVFFlat/HNSW indexes)
- [x] ✅ Full-text search (tsvector + GIN indexes)
- [x] ✅ JSONB metadata (flexible extra fields)
- [x] ✅ Auto-update triggers (search_vector, updated_at)
- [x] ✅ Foreign key relationships (referential integrity)
- [x] ✅ Multi-language support (6 languages)
- [x] ✅ Query patterns documented (hybrid search, keyword search, etc.)
- [x] ✅ Security features (RLS ready, IP anonymization)
- [x] ✅ Migration strategy (4 phases)

---

### **FASE C: Implementazione Laravel** 🚧 IN CORSO

#### **1. Database Setup** 🚧

**PostgreSQL Extension**
- [ ] 🚧 Install pgvector extension
  ```sql
  CREATE EXTENSION IF NOT EXISTS vector;
  ```

**Migrations**
- [ ] 🚧 Create migration: `rag_categories`
- [ ] 🚧 Create migration: `rag_documents`
- [ ] 🚧 Create migration: `rag_chunks`
- [ ] 🚧 Create migration: `rag_embeddings`
- [ ] 🚧 Create migration: `rag_queries`
- [ ] 🚧 Create migration: `rag_responses`
- [ ] 🚧 Create migration: `rag_sources`
- [ ] 🚧 Create migration: `rag_query_cache`
- [ ] 🚧 Run migrations: `php artisan migrate`

---

#### **2. Eloquent Models** 🚧

- [ ] 🚧 Create model: `RagCategory`
  - Relationships: `children()`, `parent()`, `documents()`
  - Scopes: `active()`, `root()`

- [ ] 🚧 Create model: `RagDocument`
  - Relationships: `category()`, `chunks()`, `queries()`
  - Scopes: `published()`, `byLanguage($lang)`
  - Accessors: `excerpt`, `reading_time_minutes`
  - Mutators: Auto-slug generation

- [ ] 🚧 Create model: `RagChunk`
  - Relationships: `document()`, `embedding()`
  - Scopes: `byOrder()`

- [ ] 🚧 Create model: `RagEmbedding`
  - Relationships: `chunk()`
  - Custom casts: `embedding` → array

- [ ] 🚧 Create model: `RagQuery`
  - Relationships: `user()`, `response()`
  - Scopes: `helpful()`, `recent()`

- [ ] 🚧 Create model: `RagResponse`
  - Relationships: `query()`, `sources()`
  - Accessors: `claims_array`, `sources_array`

- [ ] 🚧 Create model: `RagSource`
  - Relationships: `response()`, `chunk()`

---

#### **3. Services Layer** 🚧

**DocumentIndexingService**
- [ ] 🚧 Method: `indexDirectory($path, $category)`
  - Parse markdown files
  - Extract metadata (frontmatter)
  - Chunk content (smart splitting)
  - Generate embeddings (OpenAI API)
  - Save to PostgreSQL

**NatanQueryService**
- [ ] 🚧 Method: `processQuery($question, $userId, $language)`
  - Detect language
  - Check cache
  - Call Python RAG pipeline
  - Save query log
  - Return formatted response

**NatanResponseService**
- [ ] 🚧 Method: `formatResponse($ragOutput)`
  - Parse claims
  - Format citations
  - Generate HTML
  - Calculate reading time

**EmbeddingService**
- [ ] 🚧 Method: `generateEmbedding($text)`
  - Call OpenAI API
  - Return 1536-dim vector
  - Handle rate limits
  - Cache results

---

#### **4. Controllers** 🚧

**NatanController**
- [ ] 🚧 Endpoint: `POST /art-advisor/chat`
  - Receive question from AI Sidebar
  - Call `NatanQueryService`
  - Return SSE stream (Server-Sent Events)
  - Format: `data: {content: "...", done: false}`

**NatanDocumentController** (Admin)
- [ ] 🚧 Endpoint: `GET /admin/natan/documents`
  - List indexed documents
  - Filter by category/status
- [ ] 🚧 Endpoint: `POST /admin/natan/documents/index`
  - Trigger document indexing
  - Show progress

---

#### **5. Frontend Integration** 🚧

**AI Sidebar Enhancement**
- [ ] 🚧 Update `ai-sidebar.js`
  - Add RAG response handling
  - Display URS score badge
  - Show source citations
  - Handle gaps message

- [ ] 🚧 Update `ai-sidebar.blade.php`
  - Add URS score display
  - Add "Show Sources" toggle
  - Add claims highlighting

**Admin UI**
- [ ] 🚧 Create Livewire component: `DocumentIndexManager`
  - Upload/index new documents
  - View indexed documents
  - Trigger re-indexing
  - View indexing status

---

### **FASE D: Python AI Service** 🚧 TODO

#### **Setup Environment**
- [ ] 🚧 Create `python_ai_service/` directory
- [ ] 🚧 Copy base structure from NATAN_LOC
- [ ] 🚧 Install dependencies: `pip install -r requirements.txt`
- [ ] 🚧 Configure `.env` (OpenAI API key, PostgreSQL connection)

#### **Adapt RAG-Fortress Components**

**PostgreSQL Retriever** (`retriever_pg.py`)
- [ ] 🚧 Replace MongoDB queries with PostgreSQL
- [ ] 🚧 Implement pgvector similarity search
- [ ] 🚧 Implement tsvector keyword search
- [ ] 🚧 Hybrid scoring (vector + keyword)
- [ ] 🚧 Adaptive top_k/threshold

**Evidence Verifier**
- [ ] 🚧 Port from NATAN_LOC (no changes needed)

**Claim Extractor**
- [ ] 🚧 Port from NATAN_LOC
- [ ] 🚧 Adjust prompts for platform context

**Gap Detector**
- [ ] 🚧 Port from NATAN_LOC (no changes needed)

**Constrained Synthesizer**
- [ ] 🚧 Port from NATAN_LOC
- [ ] 🚧 Replace PA bureaucratic prompts with friendly platform style
- [ ] 🚧 Adjust for creator/collector audiences

**Hostile Fact Checker**
- [ ] 🚧 Port from NATAN_LOC (no changes needed)

**URS Calculator**
- [ ] 🚧 Port from NATAN_LOC (no changes needed)

**Pipeline Orchestrator**
- [ ] 🚧 Adapt `pipeline.py` for PostgreSQL
- [ ] 🚧 Remove multi-tenant logic (single tenant)
- [ ] 🚧 Add language filtering

---

### **FASE E: Testing & Optimization** 📋 PIANIFICATA

#### **Unit Tests**
- [ ] 📋 Test: DocumentIndexingService
- [ ] 📋 Test: NatanQueryService
- [ ] 📋 Test: EmbeddingService
- [ ] 📋 Test: PostgreSQL retriever
- [ ] 📋 Test: Claim extractor
- [ ] 📋 Test: URS calculator

#### **Integration Tests**
- [ ] 📋 Test: Full RAG pipeline end-to-end
- [ ] 📋 Test: AI Sidebar chat flow
- [ ] 📋 Test: Document indexing pipeline
- [ ] 📋 Test: Cache hit/miss scenarios

#### **Performance Tests**
- [ ] 📋 Benchmark: pgvector search (latency)
- [ ] 📋 Benchmark: Full RAG pipeline (end-to-end)
- [ ] 📋 Benchmark: Cache performance
- [ ] 📋 Load test: Concurrent queries

#### **Optimization**
- [ ] 📋 Optimize: pgvector index (tune lists parameter)
- [ ] 📋 Optimize: Chunk size (test 200/400/800 tokens)
- [ ] 📋 Optimize: top_k thresholds (test different values)
- [ ] 📋 Optimize: LLM prompts (reduce tokens)
- [ ] 📋 Optimize: Response caching (tune TTL)

---

## 📊 Document Sources (To Index)

### **docs/FlorenceEGI/** (Existing)

Current documents to index:

```
docs/FlorenceEGI/
├── Platform_Features.md          # Platform overview
├── Creator_Guide.md              # Creator onboarding
├── Collector_Guide.md            # Collector onboarding
├── Company_Guide.md              # Company onboarding
├── GDPR_Compliance.md            # Privacy & data protection
├── Payment_System_Map.md         # Stripe integration
├── NFT_ASA_Guide.md              # Digital assets
├── Marketplace_Rules.md          # Trading rules
├── API_Documentation.md          # Developer docs
└── FAQ.md                        # Frequently asked questions
```

**Status**: 📄 Documents exist, need to be indexed

**Categories** (to create in `rag_categories`):

| Slug | Name | Icon | Priority |
|------|------|------|----------|
| `platform` | Platform Features | layers | 1 |
| `creators` | Creator Guides | users | 2 |
| `collectors` | Collector Guides | collection | 3 |
| `companies` | Company Guides | briefcase | 4 |
| `gdpr` | GDPR & Privacy | shield | 5 |
| `marketplace` | Marketplace | shopping-cart | 6 |
| `nft-asa` | NFT & ASA | image | 7 |
| `payments` | Payment Systems | credit-card | 8 |
| `api` | API & Developers | code | 9 |
| `faq` | FAQ | help-circle | 10 |

---

## 🔐 Security & Compliance

### **GDPR Compliance**

- ✅ Audit trail for queries (`rag_queries` table)
- ✅ User consent for analytics (`was_helpful` feedback)
- ✅ Right to deletion (cascade delete user queries)
- ✅ Data minimization (only essential query data stored)
- ✅ Anonymization ready (IP address hashing)

### **Security Features**

- ✅ Input sanitization (XSS protection via DOMPurify)
- ✅ Rate limiting (Laravel throttle middleware)
- ✅ SQL injection protection (Eloquent ORM)
- ✅ API authentication (Sanctum tokens)
- ✅ Row-level security ready (PostgreSQL RLS)

### **Cost Control**

**OpenAI API Costs** (Estimates):

| Operation | Cost | Volume | Monthly |
|-----------|------|--------|---------|
| Embeddings (text-embedding-3-small) | $0.00002/1k tokens | 1k docs × 500 tokens | $0.01 |
| Claude 3.5 Sonnet (synthesis) | $3/MTok in, $15/MTok out | 1k queries × 3k tokens | $50 |
| Llama-3.1-70B (Groq free tier) | FREE | 1k queries | $0 |
| Gemini-1.5-Flash (free tier) | FREE | 1k queries | $0 |

**Total Estimated**: ~$50/month for 1k queries (with free-tier models)

---

## 📈 Performance Targets

### **Latency Goals**

| Metric | Target | Notes |
|--------|--------|-------|
| Vector search | < 500ms | pgvector IVFFlat |
| Full RAG pipeline | < 15s | All 7 stages |
| Cache hit response | < 100ms | PostgreSQL query |
| Document indexing | < 10s per doc | Including embeddings |

### **Throughput Goals**

| Metric | Target | Notes |
|--------|--------|-------|
| Concurrent queries | 10-20 req/s | Limited by OpenAI rate limits |
| Indexed documents | 1000+ | Initial target |
| Chunks | 10,000+ | Avg 10 chunks per doc |

---

## 🎯 Roadmap

### **Week 1-2: Foundation** (Current)

- [x] ✅ Analyze NATAN_LOC architecture
- [x] ✅ Design PostgreSQL schema
- [ ] 🚧 Create migrations
- [ ] 🚧 Create Eloquent models
- [ ] 🚧 Install pgvector extension

### **Week 3-4: Laravel Integration**

- [ ] 🚧 Implement DocumentIndexingService
- [ ] 🚧 Implement NatanQueryService
- [ ] 🚧 Implement EmbeddingService
- [ ] 🚧 Create NatanController
- [ ] 🚧 Index existing documents

### **Week 5-6: Python AI Service**

- [ ] 🚧 Setup Python FastAPI service
- [ ] 🚧 Adapt PostgreSQL retriever
- [ ] 🚧 Port RAG-Fortress components
- [ ] 🚧 Adjust prompts for platform context
- [ ] 🚧 Test full pipeline

### **Week 7-8: Frontend Integration**

- [ ] 🚧 Enhance AI Sidebar UI
- [ ] 🚧 Add URS score display
- [ ] 🚧 Add source citations
- [ ] 🚧 Add admin document manager
- [ ] 🚧 E2E testing

### **Week 9-10: Testing & Optimization**

- [ ] 📋 Unit tests
- [ ] 📋 Integration tests
- [ ] 📋 Performance testing
- [ ] 📋 Optimize pgvector indexes
- [ ] 📋 Optimize prompts

### **Week 11-12: Production Launch**

- [ ] 📋 Deploy to production
- [ ] 📋 Monitor performance
- [ ] 📋 Collect user feedback
- [ ] 📋 Iterate on prompts
- [ ] 📋 Add more documents

---

## 📝 Changelog

### **2026-02-06** - Fase A+B Completate

#### **FASE A: Analisi NATAN_LOC RAG Architecture**

- ✅ Creato documento analisi completa (800+ righe)
- ✅ Mappati 10 componenti core RAG-Fortress
- ✅ Estratte 7 key innovations (claim-based, adaptive params, token-based, etc.)
- ✅ Documentati pattern riusabili per EGI
- ✅ Identificate differenze MongoDB vs PostgreSQL

**File**: `docs/AI_ANALYSIS/NATAN_LOC_RAG_ARCHITECTURE_ANALYSIS.md`

#### **FASE B: Design Schema PostgreSQL**

- ✅ Progettate 8 tabelle relazionali ottimizzate
- ✅ Definiti 15+ indici (pgvector, GIN, B-tree)
- ✅ Creati 3 trigger automatici
- ✅ Documentati 6 query pattern
- ✅ Pianificata strategia migrazione (4 fasi)
- ✅ Aggiunte considerazioni security & privacy

**File**: `docs/AI_ANALYSIS/EGI_POSTGRESQL_RAG_SCHEMA_DESIGN.md`

#### **Documento Stato dell'Arte**

- ✅ Creato questo documento (analoga a NATAN_LOC)
- ✅ Struttura completa: architettura, stack, roadmap
- ✅ Stato implementazione dettagliato
- ✅ Prossimi passi chiari per Fase C

**File**: `docs/AI_ANALYSIS/00_EGI_NATAN_STATO_DELLARTE.md`

---

## 🔗 Riferimenti

### **Documentazione Correlata**

| Documento | Path | Descrizione |
|-----------|------|-------------|
| **NATAN_LOC Analysis** | `docs/AI_ANALYSIS/NATAN_LOC_RAG_ARCHITECTURE_ANALYSIS.md` | Analisi completa architettura NATAN_LOC |
| **PostgreSQL Schema** | `docs/AI_ANALYSIS/EGI_POSTGRESQL_RAG_SCHEMA_DESIGN.md` | Design schema database completo |
| **AI Sidebar Component** | `resources/views/components/ai-sidebar.blade.php` | Componente esistente da integrare |
| **Onboarding Checklist** | `app/Services/OnboardingChecklistService.php` | Service esistente (esempio pattern) |

### **Progetti Correlati**

| Progetto | Path | Relazione |
|----------|------|-----------|
| **NATAN_LOC** | `/home/fabio/NATAN_LOC` | Source architecture for RAG |
| **EGI Platform** | `/home/fabio/EGI` | Target integration |
| **EGI-HUB** | `/home/fabio/EGI-HUB` | Shared models & logic |

---

## 📞 Contatti & Support

**Progetto**: EGI NATAN (AI Assistant)
**Organizzazione**: FlorenceEGI
**Parent Project**: EGI Platform
**Repository**: `/home/fabio/EGI`
**Based On**: NATAN_LOC RAG Architecture

---

**Versione**: 0.1.0 (Alpha)
**Data**: 2026-02-06
**Ultimo Aggiornamento**: 2026-02-06 (Fase B Completata)
**Status**: 🚧 **ALPHA - DESIGN COMPLETATO, IMPLEMENTAZIONE NEXT**

---

**Padmin D. Curtis OS3.0**
*"Less talk, more code. Ship it."*
