# 🗄️ EGI POSTGRESQL RAG SCHEMA - DESIGN OTTIMIZZATO

**Data Design**: 2026-02-06
**Designer**: Padmin D. Curtis (AI Partner OS3.0)
**Database**: PostgreSQL 15+ with pgvector extension
**Purpose**: Super-optimized RAG schema for structured EGI documents
**Foundation**: NATAN_LOC architecture adapted for relational DB

---

## 🎯 DESIGN PHILOSOPHY

### **NATAN_LOC (MongoDB) vs EGI (PostgreSQL)**

| Aspect | NATAN_LOC | EGI/Natan |
|--------|-----------|-----------|
| **Documents** | PDF eterogenei non strutturati | Markdown strutturati e ottimizzabili |
| **Database** | MongoDB (schema flessibile) | PostgreSQL (schema relazionale) |
| **Vector Search** | Atlas vector_index | pgvector (ivfflat/hnsw) |
| **Metadata** | JSONB flessibile | Colonne tipizzate + JSONB extra |
| **Chunks** | Separate collection (sync) | FK relationship (ACID) |
| **Embeddings** | Embedded in document | Separate table (normalization) |
| **Full-text** | MongoDB text search | PostgreSQL tsvector (GIN index) |
| **Transactions** | Limited | Full ACID support |

### **Obiettivi Schema Design:**

1. ✅ **Performance**: Indici ottimizzati per vector + keyword + metadata search
2. ✅ **Scalabilità**: Milioni di documenti/chunks gestibili
3. ✅ **Manutenibilità**: Schema chiaro, FK relationships, migrations
4. ✅ **Flessibilità**: JSONB per metadata custom senza schema changes
5. ✅ **Multi-tenant**: Isolamento dati per tenant_id
6. ✅ **Audit Trail**: created_at, updated_at, version tracking
7. ✅ **i18n**: Support 6 lingue (it, en, de, es, fr, pt)

---

## 📊 SCHEMA OVERVIEW

```
┌────────────────────────────────────────────────────────────┐
│                  EGI RAG DATABASE SCHEMA                    │
├────────────────────────────────────────────────────────────┤
│                                                             │
│  ┌─────────────────┐         ┌─────────────────┐          │
│  │ rag_categories  │◄───────┤ rag_documents   │          │
│  └─────────────────┘         └────────┬────────┘          │
│                                        │                    │
│                                        │ 1:N                │
│                                        ▼                    │
│                               ┌────────────────┐           │
│                               │  rag_chunks    │           │
│                               └────────┬───────┘           │
│                                        │                    │
│                                        │ 1:1                │
│                                        ▼                    │
│                               ┌────────────────┐           │
│                               │ rag_embeddings │           │
│                               └────────────────┘           │
│                                                             │
│  ┌─────────────────┐         ┌─────────────────┐          │
│  │  rag_queries    │         │ rag_responses   │          │
│  └────────┬────────┘         └────────┬────────┘          │
│           │                            │                    │
│           │ N:M                        │ 1:N                │
│           ▼                            ▼                    │
│  ┌─────────────────┐         ┌─────────────────┐          │
│  │ rag_query_cache │         │  rag_sources    │          │
│  └─────────────────┘         └─────────────────┘          │
│                                                             │
└────────────────────────────────────────────────────────────┘
```

---

## 📋 TABLE DEFINITIONS

### **1. `rag_categories`** - Document Categories

```sql
CREATE TABLE rag_categories (
    id BIGSERIAL PRIMARY KEY,
    slug VARCHAR(100) NOT NULL UNIQUE,  -- 'platform-features', 'gdpr-compliance'
    name_key VARCHAR(255) NOT NULL,      -- 'rag.categories.platform_features'
    description_key VARCHAR(255),        -- Translation key
    icon VARCHAR(50),                    -- 'book', 'shield', 'chart'
    color VARCHAR(7),                    -- '#D4A574' (Oro Fiorentino)
    parent_id BIGINT REFERENCES rag_categories(id) ON DELETE SET NULL,
    sort_order INTEGER DEFAULT 0,
    is_active BOOLEAN DEFAULT true,
    metadata JSONB DEFAULT '{}'::jsonb,  -- Custom fields
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW()
);

-- Indexes
CREATE INDEX idx_rag_categories_slug ON rag_categories(slug);
CREATE INDEX idx_rag_categories_parent ON rag_categories(parent_id) WHERE parent_id IS NOT NULL;
CREATE INDEX idx_rag_categories_active ON rag_categories(is_active) WHERE is_active = true;
CREATE INDEX idx_rag_categories_metadata ON rag_categories USING gin(metadata);
```

**Purpose:**
- Hierarchical categorization of documents
- Supports i18n via translation keys
- Visual organization (icon, color)
- Enable category-based filtering in RAG

**Example Data:**
```sql
INSERT INTO rag_categories (slug, name_key, description_key, icon, color, sort_order) VALUES
('platform', 'rag.categories.platform', 'rag.categories.platform_desc', 'layers', '#1B365D', 1),
('creators', 'rag.categories.creators', 'rag.categories.creators_desc', 'users', '#D4A574', 2),
('collectors', 'rag.categories.collectors', 'rag.categories.collectors_desc', 'collection', '#2D5016', 3),
('gdpr', 'rag.categories.gdpr', 'rag.categories.gdpr_desc', 'shield', '#6B6B6B', 4),
('marketplace', 'rag.categories.marketplace', 'rag.categories.marketplace_desc', 'shopping-cart', '#E67E22', 5);
```

---

### **2. `rag_documents`** - Master Documents Table

```sql
CREATE TABLE rag_documents (
    id BIGSERIAL PRIMARY KEY,
    uuid UUID DEFAULT gen_random_uuid() UNIQUE NOT NULL,  -- Public identifier
    category_id BIGINT REFERENCES rag_categories(id) ON DELETE SET NULL,

    -- Core Fields
    title VARCHAR(500) NOT NULL,
    slug VARCHAR(255) NOT NULL,              -- URL-friendly: 'platform-features-overview'
    content TEXT NOT NULL,                   -- Full document content (Markdown)
    excerpt TEXT,                            -- Preview (first 200 chars)
    language VARCHAR(2) NOT NULL DEFAULT 'it',  -- ISO 639-1: it, en, de, es, fr, pt

    -- Structured Metadata (indexed columns for fast queries)
    document_type VARCHAR(50),               -- 'guide', 'faq', 'tutorial', 'reference'
    version VARCHAR(20) DEFAULT '1.0',       -- Semantic versioning
    status VARCHAR(20) DEFAULT 'published',  -- 'draft', 'published', 'archived'
    tags TEXT[],                             -- Array of tags: {'egi', 'nft', 'blockchain'}
    keywords TEXT[],                         -- SEO keywords

    -- Full-Text Search (auto-updated trigger)
    search_vector tsvector,                  -- GIN indexed for fast keyword search

    -- Flexible Metadata (JSONB for custom fields without schema changes)
    metadata JSONB DEFAULT '{}'::jsonb,
    -- Example metadata structure:
    -- {
    --   "author": "EGI Team",
    --   "difficulty": "beginner",
    --   "reading_time_minutes": 5,
    --   "target_audience": ["creator", "collector"],
    --   "related_docs": ["uuid1", "uuid2"],
    --   "external_links": [{"title": "...", "url": "..."}]
    -- }

    -- Stats & Analytics
    view_count INTEGER DEFAULT 0,
    helpful_count INTEGER DEFAULT 0,
    last_indexed_at TIMESTAMP,

    -- Audit Trail
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW(),
    published_at TIMESTAMP,
    archived_at TIMESTAMP,

    -- Constraints
    CONSTRAINT rag_documents_language_check CHECK (language IN ('it', 'en', 'de', 'es', 'fr', 'pt')),
    CONSTRAINT rag_documents_status_check CHECK (status IN ('draft', 'published', 'archived')),
    CONSTRAINT rag_documents_slug_language_unique UNIQUE (slug, language)
);

-- Indexes for Performance
CREATE INDEX idx_rag_documents_category ON rag_documents(category_id) WHERE category_id IS NOT NULL;
CREATE INDEX idx_rag_documents_language ON rag_documents(language);
CREATE INDEX idx_rag_documents_status ON rag_documents(status) WHERE status = 'published';
CREATE INDEX idx_rag_documents_type ON rag_documents(document_type) WHERE document_type IS NOT NULL;
CREATE INDEX idx_rag_documents_tags ON rag_documents USING gin(tags);
CREATE INDEX idx_rag_documents_keywords ON rag_documents USING gin(keywords);
CREATE INDEX idx_rag_documents_metadata ON rag_documents USING gin(metadata);
CREATE INDEX idx_rag_documents_created ON rag_documents(created_at DESC);

-- Full-Text Search Index (GIN for tsvector)
CREATE INDEX idx_rag_documents_search_vector ON rag_documents USING gin(search_vector);

-- Trigger to auto-update search_vector
CREATE OR REPLACE FUNCTION rag_documents_search_vector_trigger() RETURNS trigger AS $$
BEGIN
    NEW.search_vector :=
        setweight(to_tsvector('italian', coalesce(NEW.title, '')), 'A') ||
        setweight(to_tsvector('italian', coalesce(NEW.excerpt, '')), 'B') ||
        setweight(to_tsvector('italian', coalesce(NEW.content, '')), 'C') ||
        setweight(to_tsvector('italian', coalesce(array_to_string(NEW.tags, ' '), '')), 'B') ||
        setweight(to_tsvector('italian', coalesce(array_to_string(NEW.keywords, ' '), '')), 'B');
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER rag_documents_search_vector_update
    BEFORE INSERT OR UPDATE OF title, excerpt, content, tags, keywords
    ON rag_documents
    FOR EACH ROW
    EXECUTE FUNCTION rag_documents_search_vector_trigger();

-- Trigger to auto-update updated_at
CREATE OR REPLACE FUNCTION update_updated_at_column() RETURNS trigger AS $$
BEGIN
    NEW.updated_at = NOW();
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER rag_documents_updated_at
    BEFORE UPDATE ON rag_documents
    FOR EACH ROW
    EXECUTE FUNCTION update_updated_at_column();
```

**Design Decisions:**

1. **UUID for Public ID**: Prevent ID enumeration attacks, stable external references
2. **slug + language UNIQUE**: Enable multi-language same-slug documents
3. **Typed Columns vs JSONB**: Frequently queried fields → typed columns, flexible extras → JSONB
4. **TEXT[] for tags/keywords**: Native array support, GIN indexable
5. **tsvector**: Native full-text search (faster than LIKE '%keyword%')
6. **Triggers**: Auto-update search_vector, updated_at (less manual code)

---

### **3. `rag_chunks`** - Document Chunks (Paragraphs/Sections)

```sql
CREATE TABLE rag_chunks (
    id BIGSERIAL PRIMARY KEY,
    uuid UUID DEFAULT gen_random_uuid() UNIQUE NOT NULL,
    document_id BIGINT NOT NULL REFERENCES rag_documents(id) ON DELETE CASCADE,

    -- Chunk Content
    text TEXT NOT NULL,                     -- Chunk content (paragraph/section)
    section_title VARCHAR(500),             -- Section heading (if applicable)
    chunk_order INTEGER NOT NULL,           -- 0-based position in document
    char_start INTEGER,                     -- Start position in original content
    char_end INTEGER,                       -- End position in original content
    token_count INTEGER,                    -- Precomputed token count (OpenAI tiktoken)

    -- Chunk Metadata (inherited from parent + chunk-specific)
    chunk_type VARCHAR(50) DEFAULT 'paragraph',  -- 'paragraph', 'heading', 'list', 'code', 'table'
    language VARCHAR(2) NOT NULL,           -- Inherited from document
    metadata JSONB DEFAULT '{}'::jsonb,     -- Custom chunk metadata

    -- Full-Text Search
    search_vector tsvector,

    -- Audit
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW(),

    -- Constraints
    CONSTRAINT rag_chunks_language_check CHECK (language IN ('it', 'en', 'de', 'es', 'fr', 'pt')),
    CONSTRAINT rag_chunks_order_unique UNIQUE (document_id, chunk_order)
);

-- Indexes
CREATE INDEX idx_rag_chunks_document ON rag_chunks(document_id);
CREATE INDEX idx_rag_chunks_language ON rag_chunks(language);
CREATE INDEX idx_rag_chunks_type ON rag_chunks(chunk_type);
CREATE INDEX idx_rag_chunks_order ON rag_chunks(document_id, chunk_order);
CREATE INDEX idx_rag_chunks_metadata ON rag_chunks USING gin(metadata);
CREATE INDEX idx_rag_chunks_search_vector ON rag_chunks USING gin(search_vector);

-- Trigger: Auto-update search_vector
CREATE TRIGGER rag_chunks_search_vector_update
    BEFORE INSERT OR UPDATE OF text, section_title
    ON rag_chunks
    FOR EACH ROW
    EXECUTE FUNCTION rag_documents_search_vector_trigger();

-- Trigger: Auto-update updated_at
CREATE TRIGGER rag_chunks_updated_at
    BEFORE UPDATE ON rag_chunks
    FOR EACH ROW
    EXECUTE FUNCTION update_updated_at_column();
```

**Design Decisions:**

1. **ON DELETE CASCADE**: Delete document → delete all chunks (referential integrity)
2. **chunk_order UNIQUE per document**: Preserve document structure
3. **char_start/char_end**: Enable context reconstruction
4. **token_count**: Precomputed for LLM context management
5. **chunk_type**: Enable type-based retrieval (e.g., "find code examples")

---

### **4. `rag_embeddings`** - Vector Embeddings (1:1 with chunks)

```sql
-- Install pgvector extension first
CREATE EXTENSION IF NOT EXISTS vector;

CREATE TABLE rag_embeddings (
    id BIGSERIAL PRIMARY KEY,
    chunk_id BIGINT NOT NULL UNIQUE REFERENCES rag_chunks(id) ON DELETE CASCADE,

    -- Vector Data
    embedding vector(1536) NOT NULL,       -- OpenAI text-embedding-3-small (1536 dims)
    model VARCHAR(100) NOT NULL DEFAULT 'text-embedding-3-small',
    model_version VARCHAR(50),             -- e.g., 'v3', for future model upgrades

    -- Embedding Metadata
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW()
);

-- Vector Search Index (IVFFlat for large datasets, HNSW for smaller/faster)
-- IVFFlat: Good for datasets > 10k vectors, requires VACUUM ANALYZE
CREATE INDEX idx_rag_embeddings_vector_ivfflat
    ON rag_embeddings
    USING ivfflat (embedding vector_cosine_ops)
    WITH (lists = 100);  -- Adjust based on dataset size: lists = sqrt(row_count)

-- Alternative: HNSW (Hierarchical Navigable Small World) - better for < 1M vectors
-- CREATE INDEX idx_rag_embeddings_vector_hnsw
--     ON rag_embeddings
--     USING hnsw (embedding vector_cosine_ops)
--     WITH (m = 16, ef_construction = 64);

-- Other indexes
CREATE INDEX idx_rag_embeddings_chunk ON rag_embeddings(chunk_id);
CREATE INDEX idx_rag_embeddings_model ON rag_embeddings(model);
CREATE INDEX idx_rag_embeddings_created ON rag_embeddings(created_at DESC);

-- Trigger: Auto-update updated_at
CREATE TRIGGER rag_embeddings_updated_at
    BEFORE UPDATE ON rag_embeddings
    FOR EACH ROW
    EXECUTE FUNCTION update_updated_at_column();
```

**Design Decisions:**

1. **1:1 Relationship**: One embedding per chunk (normalization, easy updates)
2. **vector(1536)**: OpenAI text-embedding-3-small dimensions
3. **IVFFlat vs HNSW**: IVFFlat for EGI scale (10k-1M vectors), HNSW if < 100k
4. **model + model_version**: Track embedding model for future re-indexing
5. **ON DELETE CASCADE**: Delete chunk → delete embedding

**Vector Search Query Example:**

```sql
-- Find top 20 most similar chunks
SELECT
    c.id AS chunk_id,
    c.text,
    c.section_title,
    d.title AS document_title,
    d.uuid AS document_uuid,
    (e.embedding <-> $1::vector) AS distance,
    1 - (e.embedding <-> $1::vector) AS similarity
FROM rag_embeddings e
JOIN rag_chunks c ON c.id = e.chunk_id
JOIN rag_documents d ON d.id = c.document_id
WHERE d.status = 'published'
    AND d.language = 'it'
ORDER BY e.embedding <-> $1::vector
LIMIT 20;
```

**Index Selection Guide:**

| Dataset Size | Index Type | lists/m param | Build Time | Query Speed |
|--------------|------------|---------------|------------|-------------|
| < 10k vectors | HNSW | m=16, ef=64 | Fast (~1 min) | Very Fast |
| 10k-100k | HNSW | m=24, ef=100 | Medium (~10 min) | Fast |
| 100k-1M | IVFFlat | lists=100-1000 | Fast (~5 min) | Fast |
| > 1M | IVFFlat | lists=1000-5000 | Medium (~30 min) | Medium |

---

### **5. `rag_queries`** - User Query Log & Analytics

```sql
CREATE TABLE rag_queries (
    id BIGSERIAL PRIMARY KEY,
    uuid UUID DEFAULT gen_random_uuid() UNIQUE NOT NULL,
    user_id BIGINT REFERENCES users(id) ON DELETE SET NULL,  -- Nullable for anonymous

    -- Query Data
    question TEXT NOT NULL,
    question_hash VARCHAR(64) NOT NULL,     -- SHA256 for deduplication
    language VARCHAR(2) NOT NULL,
    context JSONB DEFAULT '{}'::jsonb,      -- { user_type, mode, filters, etc. }

    -- Response Summary
    response_id BIGINT REFERENCES rag_responses(id) ON DELETE SET NULL,
    urs_score DECIMAL(5,2),                 -- 0-100 reliability score
    answer_length INTEGER,                  -- Character count
    chunks_used INTEGER,                    -- How many chunks retrieved
    response_time_ms INTEGER,               -- Latency in milliseconds

    -- Analytics
    was_helpful BOOLEAN,                    -- User feedback (thumbs up/down)
    feedback_text TEXT,                     -- Optional user comment
    view_count INTEGER DEFAULT 0,           -- How many times viewed (if cached)

    -- Metadata
    ip_address INET,                        -- For rate limiting
    user_agent TEXT,                        -- Browser/client info
    session_id VARCHAR(255),                -- Group queries by session

    -- Audit
    created_at TIMESTAMP DEFAULT NOW(),
    responded_at TIMESTAMP,

    -- Constraints
    CONSTRAINT rag_queries_language_check CHECK (language IN ('it', 'en', 'de', 'es', 'fr', 'pt'))
);

-- Indexes
CREATE INDEX idx_rag_queries_user ON rag_queries(user_id) WHERE user_id IS NOT NULL;
CREATE INDEX idx_rag_queries_hash ON rag_queries(question_hash);
CREATE INDEX idx_rag_queries_language ON rag_queries(language);
CREATE INDEX idx_rag_queries_created ON rag_queries(created_at DESC);
CREATE INDEX idx_rag_queries_context ON rag_queries USING gin(context);
CREATE INDEX idx_rag_queries_helpful ON rag_queries(was_helpful) WHERE was_helpful IS NOT NULL;
CREATE INDEX idx_rag_queries_urs ON rag_queries(urs_score) WHERE urs_score IS NOT NULL;

-- Function: Generate question hash
CREATE OR REPLACE FUNCTION rag_generate_question_hash(question TEXT) RETURNS VARCHAR(64) AS $$
BEGIN
    RETURN encode(digest(lower(trim(question)), 'sha256'), 'hex');
END;
$$ LANGUAGE plpgsql IMMUTABLE;
```

**Purpose:**
- Query analytics (popular questions, low URS scores)
- User feedback collection
- Rate limiting (ip_address)
- Query caching opportunities (question_hash)

---

### **6. `rag_responses`** - Generated Responses

```sql
CREATE TABLE rag_responses (
    id BIGSERIAL PRIMARY KEY,
    uuid UUID DEFAULT gen_random_uuid() UNIQUE NOT NULL,
    query_id BIGINT NOT NULL REFERENCES rag_queries(id) ON DELETE CASCADE,

    -- Response Content
    answer TEXT NOT NULL,                   -- Synthesized answer (Markdown)
    answer_html TEXT,                       -- Rendered HTML (optional)

    -- Quality Metrics
    urs_score DECIMAL(5,2) NOT NULL,        -- Unified Reliability Score (0-100)
    urs_explanation TEXT,                   -- "2 gaps, 0 hallucinations, score: 70"

    -- RAG Pipeline Metadata
    claims_used JSONB DEFAULT '[]'::jsonb,  -- Array of claim strings
    gaps_detected JSONB DEFAULT '[]'::jsonb,
    hallucinations JSONB DEFAULT '[]'::jsonb,
    sources_used JSONB DEFAULT '[]'::jsonb, -- Array of {chunk_id, document_uuid, title}

    -- Performance Metrics
    processing_time_ms INTEGER NOT NULL,
    tokens_input INTEGER,
    tokens_output INTEGER,
    cost_usd DECIMAL(10,6),
    model_used VARCHAR(100),                -- Primary LLM model

    -- Pipeline Stages Timing (for optimization)
    stage_timings JSONB DEFAULT '{}'::jsonb,
    -- Example: {"retrieval_ms": 1200, "extraction_ms": 8000, "synthesis_ms": 10000}

    -- Cache Strategy
    is_cached BOOLEAN DEFAULT false,
    cache_key VARCHAR(255),
    cache_expires_at TIMESTAMP,

    -- Audit
    created_at TIMESTAMP DEFAULT NOW(),
    served_from_cache_at TIMESTAMP,         -- Last time served from cache

    -- Constraints
    CONSTRAINT rag_responses_urs_check CHECK (urs_score BETWEEN 0 AND 100)
);

-- Indexes
CREATE INDEX idx_rag_responses_query ON rag_responses(query_id);
CREATE INDEX idx_rag_responses_urs ON rag_responses(urs_score);
CREATE INDEX idx_rag_responses_cache_key ON rag_responses(cache_key) WHERE is_cached = true;
CREATE INDEX idx_rag_responses_created ON rag_responses(created_at DESC);
CREATE INDEX idx_rag_responses_sources ON rag_responses USING gin(sources_used);
```

**Purpose:**
- Store generated responses for analytics
- Enable response caching (cache_key)
- Track RAG pipeline performance
- Quality metrics (URS, claims, gaps, hallucinations)

---

### **7. `rag_sources`** - Response Source Citations (N:M)

```sql
CREATE TABLE rag_sources (
    id BIGSERIAL PRIMARY KEY,
    response_id BIGINT NOT NULL REFERENCES rag_responses(id) ON DELETE CASCADE,
    chunk_id BIGINT NOT NULL REFERENCES rag_chunks(id) ON DELETE CASCADE,

    -- Citation Metadata
    claim_id VARCHAR(50),                   -- e.g., "CLAIM_001"
    exact_quote TEXT,                       -- Quoted text from chunk
    relevance_score DECIMAL(5,2),           -- 0-10 score
    citation_order INTEGER,                 -- Display order in answer

    -- Audit
    created_at TIMESTAMP DEFAULT NOW(),

    -- Constraints
    CONSTRAINT rag_sources_unique UNIQUE (response_id, chunk_id, claim_id)
);

-- Indexes
CREATE INDEX idx_rag_sources_response ON rag_sources(response_id);
CREATE INDEX idx_rag_sources_chunk ON rag_sources(chunk_id);
CREATE INDEX idx_rag_sources_relevance ON rag_sources(relevance_score DESC);
```

**Purpose:**
- Track which chunks were used in each response
- Enable "Show Sources" feature in UI
- Audit trail: response → chunks → documents

---

### **8. `rag_query_cache`** - Query Result Cache

```sql
CREATE TABLE rag_query_cache (
    id BIGSERIAL PRIMARY KEY,
    cache_key VARCHAR(255) NOT NULL UNIQUE,
    question_hash VARCHAR(64) NOT NULL,
    response_id BIGINT REFERENCES rag_responses(id) ON DELETE CASCADE,

    -- Cache Metadata
    language VARCHAR(2) NOT NULL,
    context_hash VARCHAR(64),               -- Hash of context JSONB
    hit_count INTEGER DEFAULT 0,            -- Cache hit counter
    last_hit_at TIMESTAMP,

    -- Cache Control
    expires_at TIMESTAMP NOT NULL,
    is_stale BOOLEAN DEFAULT false,

    -- Audit
    created_at TIMESTAMP DEFAULT NOW(),

    -- Constraints
    CONSTRAINT rag_query_cache_language_check CHECK (language IN ('it', 'en', 'de', 'es', 'fr', 'pt'))
);

-- Indexes
CREATE INDEX idx_rag_query_cache_key ON rag_query_cache(cache_key);
CREATE INDEX idx_rag_query_cache_hash ON rag_query_cache(question_hash);
CREATE INDEX idx_rag_query_cache_expires ON rag_query_cache(expires_at) WHERE is_stale = false;
CREATE INDEX idx_rag_query_cache_hits ON rag_query_cache(hit_count DESC);

-- Auto-cleanup expired cache
CREATE OR REPLACE FUNCTION rag_cleanup_expired_cache() RETURNS void AS $$
BEGIN
    DELETE FROM rag_query_cache WHERE expires_at < NOW();
END;
$$ LANGUAGE plpgsql;

-- Schedule cleanup (via pg_cron or Laravel scheduler)
-- SELECT cron.schedule('rag-cache-cleanup', '0 */6 * * *', 'SELECT rag_cleanup_expired_cache()');
```

**Purpose:**
- Cache frequent queries (reduce LLM costs)
- Track cache hit rate
- Auto-expiration

---

## 🔍 QUERY PATTERNS

### **1. Hybrid Search (Vector + Keyword + Metadata)**

```sql
-- Full hybrid search with all filters
WITH vector_results AS (
    SELECT
        c.id AS chunk_id,
        c.text,
        c.section_title,
        c.document_id,
        d.title AS document_title,
        d.uuid AS document_uuid,
        d.category_id,
        cat.name_key AS category_name,
        (e.embedding <-> $1::vector) AS distance,
        1 - (e.embedding <-> $1::vector) AS similarity
    FROM rag_embeddings e
    JOIN rag_chunks c ON c.id = e.chunk_id
    JOIN rag_documents d ON d.id = c.document_id
    LEFT JOIN rag_categories cat ON cat.id = d.category_id
    WHERE d.status = 'published'
        AND d.language = $2  -- Language filter
        AND ($3::bigint IS NULL OR d.category_id = $3)  -- Category filter
        AND ($4::text IS NULL OR $4 = ANY(d.tags))      -- Tag filter
    ORDER BY e.embedding <-> $1::vector
    LIMIT 200  -- Adaptive top_k
),
keyword_results AS (
    SELECT
        c.id AS chunk_id,
        ts_rank(c.search_vector, plainto_tsquery('italian', $5)) AS keyword_score
    FROM rag_chunks c
    JOIN rag_documents d ON d.id = c.document_id
    WHERE d.status = 'published'
        AND d.language = $2
        AND c.search_vector @@ plainto_tsquery('italian', $5)
    LIMIT 50
)
SELECT
    vr.*,
    COALESCE(kr.keyword_score, 0) AS keyword_score,
    (vr.similarity * 0.7 + COALESCE(kr.keyword_score, 0) * 0.3) AS hybrid_score
FROM vector_results vr
LEFT JOIN keyword_results kr ON kr.chunk_id = vr.chunk_id
WHERE vr.similarity >= 0.25  -- Adaptive threshold
ORDER BY hybrid_score DESC
LIMIT 50;
```

**Parameters:**
- `$1`: query_embedding (vector)
- `$2`: language ('it')
- `$3`: category_id (optional)
- `$4`: tag (optional)
- `$5`: keyword query (string)

---

### **2. Keyword-Only Search (Fallback)**

```sql
SELECT
    c.id,
    c.text,
    c.section_title,
    d.title,
    d.uuid,
    ts_rank(c.search_vector, plainto_tsquery('italian', $1)) AS rank,
    ts_headline('italian', c.text, plainto_tsquery('italian', $1),
        'MaxWords=50, MinWords=20, MaxFragments=3') AS headline
FROM rag_chunks c
JOIN rag_documents d ON d.id = c.document_id
WHERE d.status = 'published'
    AND d.language = $2
    AND c.search_vector @@ plainto_tsquery('italian', $1)
ORDER BY rank DESC
LIMIT 20;
```

---

### **3. Category Browse**

```sql
SELECT
    d.id,
    d.uuid,
    d.title,
    d.excerpt,
    d.document_type,
    d.tags,
    d.view_count,
    d.helpful_count,
    cat.name_key AS category_name,
    cat.icon AS category_icon
FROM rag_documents d
JOIN rag_categories cat ON cat.id = d.category_id
WHERE d.status = 'published'
    AND d.language = $1
    AND d.category_id = $2
ORDER BY d.created_at DESC
LIMIT 50;
```

---

### **4. Related Documents (Vector Similarity)**

```sql
-- Find documents similar to document $1
WITH doc_chunks AS (
    SELECT c.id AS chunk_id, e.embedding
    FROM rag_chunks c
    JOIN rag_embeddings e ON e.chunk_id = c.id
    WHERE c.document_id = $1
    LIMIT 1  -- Use first chunk as representative
),
similar_chunks AS (
    SELECT
        c.document_id,
        AVG(1 - (e.embedding <-> dc.embedding)) AS avg_similarity
    FROM rag_embeddings e
    JOIN rag_chunks c ON c.id = e.chunk_id
    CROSS JOIN doc_chunks dc
    WHERE c.document_id != $1  -- Exclude self
    GROUP BY c.document_id
)
SELECT
    d.id,
    d.uuid,
    d.title,
    d.excerpt,
    sc.avg_similarity
FROM similar_chunks sc
JOIN rag_documents d ON d.id = sc.document_id
WHERE d.status = 'published'
    AND d.language = $2
ORDER BY sc.avg_similarity DESC
LIMIT 5;
```

---

### **5. Popular/Helpful Documents**

```sql
SELECT
    d.id,
    d.uuid,
    d.title,
    d.excerpt,
    d.view_count,
    d.helpful_count,
    (d.helpful_count::float / NULLIF(d.view_count, 0)) AS helpfulness_ratio
FROM rag_documents d
WHERE d.status = 'published'
    AND d.language = $1
    AND d.view_count > 10  -- Minimum views
ORDER BY helpfulness_ratio DESC, d.view_count DESC
LIMIT 20;
```

---

### **6. Query Analytics (Admin Dashboard)**

```sql
-- Top questions by frequency
SELECT
    question,
    language,
    COUNT(*) AS query_count,
    AVG(urs_score) AS avg_urs,
    AVG(response_time_ms) AS avg_response_ms,
    COUNT(*) FILTER (WHERE was_helpful = true) AS helpful_count,
    COUNT(*) FILTER (WHERE was_helpful = false) AS not_helpful_count
FROM rag_queries
WHERE created_at >= NOW() - INTERVAL '30 days'
GROUP BY question, language
HAVING COUNT(*) >= 3  -- At least 3 occurrences
ORDER BY query_count DESC
LIMIT 50;
```

---

## 🚀 PERFORMANCE OPTIMIZATION

### **1. Index Strategy**

```sql
-- Analyze query patterns first
EXPLAIN (ANALYZE, BUFFERS) <your_query>;

-- Update statistics after bulk inserts
VACUUM ANALYZE rag_documents;
VACUUM ANALYZE rag_chunks;
VACUUM ANALYZE rag_embeddings;

-- Rebuild vector index periodically (for IVFFlat)
REINDEX INDEX CONCURRENTLY idx_rag_embeddings_vector_ivfflat;
```

### **2. Partitioning (for large datasets)**

```sql
-- Partition rag_queries by month (time-series data)
CREATE TABLE rag_queries_partitioned (
    LIKE rag_queries INCLUDING ALL
) PARTITION BY RANGE (created_at);

CREATE TABLE rag_queries_2026_02 PARTITION OF rag_queries_partitioned
    FOR VALUES FROM ('2026-02-01') TO ('2026-03-01');

-- Auto-create partitions via pg_partman extension
```

### **3. Materialized Views (for analytics)**

```sql
-- Popular documents cache
CREATE MATERIALIZED VIEW rag_popular_documents AS
SELECT
    d.id,
    d.uuid,
    d.title,
    d.category_id,
    d.language,
    d.view_count,
    d.helpful_count,
    COUNT(DISTINCT q.id) AS query_count
FROM rag_documents d
LEFT JOIN rag_chunks c ON c.document_id = d.id
LEFT JOIN rag_sources rs ON rs.chunk_id = c.id
LEFT JOIN rag_responses r ON r.id = rs.response_id
LEFT JOIN rag_queries q ON q.response_id = r.id
WHERE d.status = 'published'
GROUP BY d.id
ORDER BY d.view_count DESC;

CREATE UNIQUE INDEX ON rag_popular_documents (id);

-- Refresh hourly via scheduler
REFRESH MATERIALIZED VIEW CONCURRENTLY rag_popular_documents;
```

---

## 🔐 SECURITY & PRIVACY

### **1. Row-Level Security (RLS)** - OPTIONAL

```sql
-- Enable RLS on sensitive tables
ALTER TABLE rag_queries ENABLE ROW LEVEL SECURITY;

-- Policy: Users see only their own queries
CREATE POLICY rag_queries_user_isolation ON rag_queries
    FOR SELECT
    USING (user_id = current_setting('app.current_user_id')::bigint OR user_id IS NULL);
```

### **2. PII Sanitization**

```sql
-- Function to anonymize IP addresses
CREATE OR REPLACE FUNCTION anonymize_ip(ip INET) RETURNS INET AS $$
BEGIN
    -- Mask last octet: 192.168.1.123 → 192.168.1.0
    RETURN set_masklen(ip, 24);
END;
$$ LANGUAGE plpgsql IMMUTABLE;

-- Apply on insert
CREATE TRIGGER rag_queries_anonymize_ip
    BEFORE INSERT ON rag_queries
    FOR EACH ROW
    EXECUTE FUNCTION anonymize_ip_trigger();
```

---

## 📦 MIGRATION STRATEGY

### **Phase 1: Core Tables**
1. rag_categories
2. rag_documents
3. rag_chunks

### **Phase 2: Vector Search**
4. rag_embeddings (requires pgvector)
5. Create vector indexes

### **Phase 3: Query Tracking**
6. rag_queries
7. rag_responses
8. rag_sources

### **Phase 4: Optimization**
9. rag_query_cache
10. Materialized views
11. Partitioning (if needed)

---

## ✅ NEXT STEPS

1. ✅ Create Laravel migrations for all tables
2. ✅ Create Eloquent models with relationships
3. ✅ Implement document ingestion pipeline (MD → PostgreSQL)
4. ✅ Port RAG-Fortress components (Python → PHP/Service)
5. ✅ Integrate with AI Sidebar (/art-advisor/chat)

---

**Schema Design Complete** ✅
**Ready for Implementation** 🚀

---

**Padmin D. Curtis OS3.0**
*"Less talk, more code. Ship it."*
