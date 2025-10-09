# N.A.T.A.N. AI Implementation
## Technical Documentation for Claude Copilot

**Project:** FlorenceEGI - N.A.T.A.N. Module  
**Version:** 1.0  
**Date:** Ottobre 2025  
**Developer:** Fabio Cherici  
**AI Partner:** Claude Sonnet 4.5 (Copilot)

---

## 📋 EXECUTIVE SUMMARY

### Project Context
N.A.T.A.N. (Nodo di Analisi e Tracciamento Atti Notarizzati) è un modulo AI della piattaforma FlorenceEGI che trasforma documenti amministrativi firmati in metadati strutturati certificati blockchain.

### Current Status
**Completato (70%):**
- ✅ Firma digitale validation (PDF/P7M)
- ✅ Hash generation
- ✅ QR code generation  
- ✅ Blockchain Algorand integration (1 giorno per completare testnet)

**Da sviluppare (30%):**
- ❌ AI document parsing (Anthropic Claude API)
- ❌ Metadata extraction + JSON generation
- ❌ Database schema + API endpoints
- ❌ Dashboard PA (Vanilla JS/TypeScript)

### Strategic Goal
Pilot 8 settimane con Comune di Firenze (200-300 atti). Success = Firenze come ente promotore, scaling nazionale.

### Tech Stack
- **Backend:** Laravel 11 + PHP 8.3 (Ubuntu 24.04, MySQL 8)
- **Server:** DigitalOcean Stockholm (EU-compliant GDPR)
- **AI:** Anthropic Claude 3.5 Sonnet API
- **Blockchain:** Algorand (hash certification)
- **Frontend:** Vanilla JS/TypeScript + Tailwind CSS (no frameworks)
- **PDF:** Smalot/PdfParser
- **Architecture:** N.A.T.A.N. as module within FlorenceEGI Platform

### Key Constraints
- **GDPR-by-design:** EU data processing, zero retention testo estratto, audit completo
- **Performance:** <30 sec processing per documento
- **Accuracy:** >95% metadata extraction
- **Standards:** OOP puro, Ultra Eccellenza, AI-readable code, OS2.0 documentation
- **Frontend:** Vanilla JS/TS only (no Vue/React/Alpine), SEO + ARIA compliance

### Business Model
- Pilot Firenze: €300-400 (costi vivi)
- Production: €1k/anno (24k atti)
- Altri Comuni: €200-1000/mese (pay-per-use)
- Revenue share Firenze: 30% opzionale

---

## ✅ TODO LIST

### Phase 1: AI Integration Core (5-7 giorni)

#### Document Parser
- [ ] Install Smalot/PdfParser via Composer
- [ ] Create `App\Services\Natan\DocumentParserService`
  - [ ] Method: `extractText(string $filePath): string`
  - [ ] Handle P7M (signed PDF) extraction
  - [ ] Error handling (corrupt files, unsupported formats)
- [ ] Unit tests: 10 documenti reali Firenze

#### AI Analyzer  
- [ ] Setup Anthropic Claude API client
  - [ ] Composer package o Guzzle HTTP wrapper
  - [ ] `.env` config: `ANTHROPIC_API_KEY`, `ANTHROPIC_MODEL`
- [ ] Create `App\Services\Natan\AIAnalyzerService`
  - [ ] Method: `analyzeDocument(string $text): array`
  - [ ] System prompt per atti PA italiani
  - [ ] JSON schema validation output
  - [ ] Token usage tracking
  - [ ] Error handling (API failures, rate limits)
- [ ] Test accuracy su 20 documenti campione

#### Metadata Extractor
- [ ] Create `App\Services\Natan\MetadataExtractorService`
  - [ ] Method: `extract(string $filePath): array`
  - [ ] Orchestrate: Parser → AI → Validation
  - [ ] Return structured JSON EGI-Act
- [ ] Validation rules per campi obbligatori
- [ ] Test su 50 atti diverse tipologie

### Phase 2: Database & API (2-3 giorni)

#### Database Schema
- [ ] Migration: `create_egi_acts_table`
  ```sql
  - id, document_id (unique)
  - tipo_atto, numero_atto, data_atto
  - oggetto (TEXT), ente, direzione, responsabile
  - importo (DECIMAL)
  - metadata_json (JSON)
  - hash_firma, blockchain_tx, qr_code
  - created_at, updated_at
  - Indexes: tipo, data, ente, FULLTEXT oggetto
  ```
- [ ] Model: `App\Models\EgiAct` con relationships
- [ ] Seeders per test data

#### API Endpoints
- [ ] `POST /api/natan/analyze`
  - Upload documento, trigger AI processing
  - Queue job per async processing
  - Return: job_id per polling status
- [ ] `GET /api/natan/acts`
  - Filters: tipo, data_range, importo_range, ente, direzione
  - Pagination, sorting
  - Return: collection EgiActs
- [ ] `GET /api/natan/acts/{id}`
  - Single act detail con full metadata
- [ ] `GET /api/natan/search?q={query}`
  - Full-text search su oggetto
  - Semantic search preparation (future)
- [ ] `GET /api/natan/stats`
  - KPI: total acts, by tipo, by month, avg importo
- [ ] API authentication: Sanctum token PA users
- [ ] Rate limiting per organization
- [ ] API documentation (OpenAPI/Swagger)

### Phase 3: Dashboard PA (3-4 giorni)

#### Core Components (Vanilla JS/TS)
- [ ] `resources/js/natan/`
  - [ ] `UploadManager.ts` - Drag&drop + progress
  - [ ] `ActsTable.ts` - Sortable table con filters
  - [ ] `ActDetail.ts` - Modal JSON viewer
  - [ ] `SearchPanel.ts` - Advanced filters
  - [ ] `StatsPanel.ts` - KPI charts (Chart.js)
  - [ ] `ApiClient.ts` - Fetch wrapper con auth
  - [ ] `types.ts` - TypeScript interfaces

#### UI Pages
- [ ] `resources/views/natan/dashboard.blade.php`
  - Layout master con sidebar
  - Stats overview cards
  - Recent acts table
- [ ] `resources/views/natan/upload.blade.php`
  - Upload interface
  - Processing queue status
- [ ] `resources/views/natan/acts.blade.php`
  - Full acts table con advanced filters
  - Export CSV/JSON buttons
- [ ] `resources/views/natan/analytics.blade.php`
  - Charts e KPI visualizations

#### Styling (Tailwind + Custom)
- [ ] Responsive design mobile-first
- [ ] Dark mode toggle (optional)
- [ ] ARIA accessibility completa
- [ ] Loading states, error messages
- [ ] Toast notifications

### Phase 4: Integration & Testing (2-3 giorni)

#### End-to-End Flow
- [ ] Upload PDF → Parse → AI → JSON → DB → Blockchain → QR
- [ ] Test su 100 atti reali Firenze
- [ ] Performance profiling (<30sec target)
- [ ] Error scenarios handling

#### Quality Assurance
- [ ] Unit tests coverage >80%
- [ ] Feature tests API endpoints
- [ ] Browser tests dashboard (Dusk optional)
- [ ] Accuracy validation: 50 atti manual check vs AI

#### Security & GDPR
- [ ] Audit log ogni AI processing
- [ ] Encryption metadata sensibili
- [ ] Data retention policy enforcement
- [ ] DPIA documentation 1-pager

### Phase 5: Production Readiness (1-2 giorni)

#### Deployment
- [ ] Environment setup production
- [ ] Queue workers configuration (Horizon)
- [ ] Cron jobs per cleanup
- [ ] Monitoring e alerting (Laravel Telescope)
- [ ] Backup strategy database

#### Documentation
- [ ] User manual PA operatori (PDF)
- [ ] API documentation published
- [ ] Admin guide per Fabio
- [ ] Troubleshooting common issues

#### Pilot Preparation
- [ ] Training materials per operatori Firenze
- [ ] Demo script per Sparavigna
- [ ] Success metrics dashboard
- [ ] Feedback collection form

---

## 🏗️ ARCHITECTURE

### System Flow

```
1. PA Operator uploads PDF firmato
   ↓
2. DocumentParserService extracts text
   ↓
3. AIAnalyzerService → Anthropic Claude API
   ↓
4. MetadataExtractorService validates JSON
   ↓
5. EgiAct saved to database
   ↓
6. Blockchain service certifies hash + metadata
   ↓
7. QR code generated with verification URL
   ↓
8. Dashboard displays EGI-Act card
```

### Services Architecture

```
App\Services\Natan\
├── DocumentParserService.php
│   └── extractText(filePath): string
├── AIAnalyzerService.php
│   ├── analyzeDocument(text): array
│   ├── buildPrompt(text): string
│   └── validateResponse(json): bool
├── MetadataExtractorService.php
│   ├── extract(filePath): array
│   └── validate(metadata): array
├── BlockchainService.php (già esistente)
│   └── certify(hash, metadata): tx_id
└── QRService.php (già esistente)
    └── generate(data): qr_code
```

### Database Schema

```sql
egi_acts
├── id (BIGINT PK)
├── document_id (VARCHAR UNIQUE) -- hash-based
├── tipo_atto (VARCHAR 50) -- Determina, Delibera, etc
├── numero_atto (VARCHAR 50)
├── data_atto (DATE)
├── oggetto (TEXT)
├── ente (VARCHAR 255)
├── direzione (VARCHAR 255)
├── responsabile (VARCHAR 255)
├── importo (DECIMAL 15,2)
├── metadata_json (JSON) -- full AI output
├── hash_firma (VARCHAR 255)
├── blockchain_tx (VARCHAR 255)
├── qr_code (TEXT)
├── processing_status (ENUM: pending, completed, failed)
├── ai_tokens_used (INT)
├── ai_cost (DECIMAL 10,4)
├── created_at (TIMESTAMP)
└── updated_at (TIMESTAMP)

Indexes:
- idx_tipo (tipo_atto)
- idx_data (data_atto)
- idx_ente (ente)
- idx_status (processing_status)
- fulltext_oggetto (oggetto)
```

### API Contracts

#### POST /api/natan/analyze
**Request:**
```json
{
  "file": "file upload (multipart/form-data)"
}
```

**Response:**
```json
{
  "job_id": "uuid",
  "status": "processing",
  "estimated_time": 30
}
```

#### GET /api/natan/acts
**Query params:**
- `tipo`: string (Determina, Delibera, etc)
- `data_from`: date (YYYY-MM-DD)
- `data_to`: date
- `importo_min`: decimal
- `importo_max`: decimal
- `ente`: string
- `page`: int
- `per_page`: int (default 20)

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "document_id": "abc123",
      "tipo_atto": "Determina Dirigenziale",
      "numero_atto": "DD-2025-0847",
      "data_atto": "2025-03-21",
      "oggetto": "Restauro Fontana del Nettuno",
      "direzione": "Lavori Pubblici",
      "responsabile": "Ing. Mario Rossi",
      "importo": 45000.00,
      "categoria": ["beni culturali", "restauro"],
      "blockchain_tx": "ALGO-7XYZ...",
      "qr_code": "https://florenceegi.org/verify/abc123",
      "created_at": "2025-10-08T10:30:00Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "total": 150,
    "per_page": 20
  }
}
```

#### GET /api/natan/stats
**Response:**
```json
{
  "total_acts": 1234,
  "by_tipo": {
    "Determina": 800,
    "Delibera": 300,
    "Ordinanza": 134
  },
  "by_month": [
    {"month": "2025-01", "count": 150},
    {"month": "2025-02", "count": 180}
  ],
  "avg_importo": 25000.50,
  "total_ai_cost": 45.30
}
```

---

## 🤖 AI PROMPT ENGINEERING

### System Prompt Base

```
Sei un assistente specializzato nell'analisi di atti amministrativi della Pubblica Amministrazione italiana.

Il tuo compito è estrarre metadati strutturati da documenti come:
- Determine Dirigenziali
- Delibere di Giunta
- Delibere di Consiglio
- Ordinanze Sindacali
- Decreti

Estrai SOLO informazioni presenti nel testo. Se un campo non è trovato, usa null.

Output DEVE essere JSON valido con questa struttura:
{
  "tipo_atto": string,
  "numero_atto": string,
  "data_atto": "YYYY-MM-DD",
  "oggetto": string,
  "ente": string,
  "direzione": string,
  "responsabile": string,
  "importo": number,
  "categoria": [string],
  "urgenza": "ordinaria" | "urgente",
  "scadenza": "YYYY-MM-DD" | null,
  "firmatari": [string]
}

Regole:
- tipo_atto: identifica da intestazione (Determina/Delibera/Ordinanza/Decreto)
- numero_atto: formato esatto come nel documento
- data_atto: data emissione/firma
- oggetto: sintesi max 200 caratteri
- importo: estrai da "€" o "euro", converti in numero decimale
- categoria: max 5 tag descrittivi (es: beni culturali, manutenzione, lavori pubblici)
- firmatari: lista nomi completi chi ha firmato

Rispondi SOLO con JSON, niente testo aggiuntivo.
```

### User Prompt Template

```
Analizza questo atto amministrativo:

---
{DOCUMENT_TEXT}
---

Estrai i metadati secondo lo schema richiesto.
```

### Optimization Notes
- **Token management:** Limita document_text a prime 4000 parole (Claude context)
- **Retry logic:** Se JSON invalido, retry con prompt "Fix JSON syntax"
- **Fallback:** Se AI fallisce 3 volte, flag document per review manuale

---

## 💾 CODE STRUCTURE

### DocumentParserService.php

```php
<?php

namespace App\Services\Natan;

use Smalot\PdfParser\Parser;
use Illuminate\Support\Facades\Log;

/**
 * Extracts text from PDF and P7M signed documents
 * 
 * @package App\Services\Natan
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - N.A.T.A.N.)
 * @date 2025-10-08
 */
class DocumentParserService
{
    private Parser $pdfParser;
    
    public function __construct()
    {
        $this->pdfParser = new Parser();
    }
    
    /**
     * Extract text from PDF or P7M file
     * 
     * @param string $filePath Absolute path to document
     * @return string Extracted text content
     * @throws \Exception If file cannot be parsed
     */
    public function extractText(string $filePath): string
    {
        // Validate file exists
        if (!file_exists($filePath)) {
            throw new \Exception("File not found: {$filePath}");
        }
        
        // Handle P7M (signed PDF) - extract inner PDF first
        if (str_ends_with($filePath, '.p7m')) {
            $filePath = $this->extractP7M($filePath);
        }
        
        try {
            $pdf = $this->pdfParser->parseFile($filePath);
            $text = $pdf->getText();
            
            Log::info('N.A.T.A.N. DocumentParser: Text extracted', [
                'file' => basename($filePath),
                'length' => strlen($text)
            ]);
            
            return $this->cleanText($text);
            
        } catch (\Exception $e) {
            Log::error('N.A.T.A.N. DocumentParser: Failed', [
                'file' => basename($filePath),
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
    
    /**
     * Extract PDF from P7M signed container
     * 
     * @param string $p7mPath Path to .p7m file
     * @return string Path to extracted PDF
     */
    private function extractP7M(string $p7mPath): string
    {
        // Implementation depends on P7M library used
        // Placeholder: assume inner PDF extraction logic
        // Return path to temporary extracted PDF
        
        // TODO: Implement P7M extraction
        throw new \Exception("P7M extraction not yet implemented");
    }
    
    /**
     * Clean extracted text (remove excess whitespace, normalize)
     * 
     * @param string $text Raw extracted text
     * @return string Cleaned text
     */
    private function cleanText(string $text): string
    {
        // Remove multiple spaces
        $text = preg_replace('/\s+/', ' ', $text);
        
        // Remove multiple newlines
        $text = preg_replace('/\n+/', "\n", $text);
        
        // Trim
        return trim($text);
    }
}
```

### AIAnalyzerService.php

```php
<?php

namespace App\Services\Natan;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * AI-powered document analysis using Anthropic Claude API
 * 
 * @package App\Services\Natan
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - N.A.T.A.N.)
 * @date 2025-10-08
 */
class AIAnalyzerService
{
    private string $apiKey;
    private string $model;
    private string $apiUrl = 'https://api.anthropic.com/v1/messages';
    
    public function __construct()
    {
        $this->apiKey = config('services.anthropic.api_key');
        $this->model = config('services.anthropic.model', 'claude-3-5-sonnet-20241022');
    }
    
    /**
     * Analyze document text and extract structured metadata
     * 
     * @param string $text Document text content
     * @return array Structured metadata
     * @throws \Exception If API call fails or response invalid
     */
    public function analyzeDocument(string $text): array
    {
        // Truncate text if too long (Claude context limit)
        $text = $this->truncateText($text, 4000);
        
        $prompt = $this->buildPrompt($text);
        
        $response = $this->callAnthropicAPI($prompt);
        
        $metadata = $this->parseResponse($response);
        
        if (!$this->validateMetadata($metadata)) {
            throw new \Exception("AI response validation failed");
        }
        
        Log::info('N.A.T.A.N. AIAnalyzer: Document analyzed', [
            'tipo_atto' => $metadata['tipo_atto'] ?? 'unknown',
            'tokens_used' => $response['usage']['input_tokens'] + $response['usage']['output_tokens']
        ]);
        
        return $metadata;
    }
    
    /**
     * Build prompt for AI analysis
     * 
     * @param string $text Document text
     * @return string Complete prompt
     */
    private function buildPrompt(string $text): string
    {
        $systemPrompt = $this->getSystemPrompt();
        
        return $systemPrompt . "\n\nAnalizza questo atto amministrativo:\n\n---\n{$text}\n---\n\nEstrai i metadati secondo lo schema richiesto.";
    }
    
    /**
     * Get system prompt for PA document analysis
     * 
     * @return string System prompt
     */
    private function getSystemPrompt(): string
    {
        return <<<PROMPT
Sei un assistente specializzato nell'analisi di atti amministrativi della Pubblica Amministrazione italiana.

Il tuo compito è estrarre metadati strutturati da documenti come:
- Determine Dirigenziali
- Delibere di Giunta
- Delibere di Consiglio
- Ordinanze Sindacali
- Decreti

Estrai SOLO informazioni presenti nel testo. Se un campo non è trovato, usa null.

Output DEVE essere JSON valido con questa struttura:
{
  "tipo_atto": string,
  "numero_atto": string,
  "data_atto": "YYYY-MM-DD",
  "oggetto": string,
  "ente": string,
  "direzione": string,
  "responsabile": string,
  "importo": number,
  "categoria": [string],
  "urgenza": "ordinaria" | "urgente",
  "scadenza": "YYYY-MM-DD" | null,
  "firmatari": [string]
}

Rispondi SOLO con JSON, niente testo aggiuntivo.
PROMPT;
    }
    
    /**
     * Call Anthropic Claude API
     * 
     * @param string $prompt User prompt
     * @return array API response
     * @throws \Exception If API call fails
     */
    private function callAnthropicAPI(string $prompt): array
    {
        $response = Http::withHeaders([
            'x-api-key' => $this->apiKey,
            'anthropic-version' => '2023-06-01',
            'content-type' => 'application/json',
        ])->post($this->apiUrl, [
            'model' => $this->model,
            'max_tokens' => 4096,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ]
        ]);
        
        if (!$response->successful()) {
            Log::error('N.A.T.A.N. AIAnalyzer: API call failed', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            throw new \Exception("Anthropic API error: " . $response->status());
        }
        
        return $response->json();
    }
    
    /**
     * Parse AI response and extract JSON
     * 
     * @param array $response API response
     * @return array Parsed metadata
     * @throws \Exception If JSON parsing fails
     */
    private function parseResponse(array $response): array
    {
        $content = $response['content'][0]['text'] ?? '';
        
        // Try to extract JSON from response
        // Claude might wrap in ```json ... ```
        if (preg_match('/```json\n(.*?)\n```/s', $content, $matches)) {
            $jsonString = $matches[1];
        } else {
            $jsonString = $content;
        }
        
        $metadata = json_decode($jsonString, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("Invalid JSON from AI: " . json_last_error_msg());
        }
        
        // Add AI usage metrics
        $metadata['_ai_usage'] = [
            'model' => $this->model,
            'input_tokens' => $response['usage']['input_tokens'],
            'output_tokens' => $response['usage']['output_tokens'],
            'cost_estimate' => $this->estimateCost($response['usage'])
        ];
        
        return $metadata;
    }
    
    /**
     * Validate metadata structure
     * 
     * @param array $metadata Extracted metadata
     * @return bool True if valid
     */
    private function validateMetadata(array $metadata): bool
    {
        $required = ['tipo_atto', 'data_atto', 'oggetto'];
        
        foreach ($required as $field) {
            if (!isset($metadata[$field]) || empty($metadata[$field])) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Truncate text to max words
     * 
     * @param string $text Full text
     * @param int $maxWords Maximum words
     * @return string Truncated text
     */
    private function truncateText(string $text, int $maxWords): string
    {
        $words = explode(' ', $text);
        
        if (count($words) <= $maxWords) {
            return $text;
        }
        
        return implode(' ', array_slice($words, 0, $maxWords)) . '...';
    }
    
    /**
     * Estimate API cost based on token usage
     * 
     * @param array $usage Token usage from API
     * @return float Estimated cost in EUR
     */
    private function estimateCost(array $usage): float
    {
        // Claude 3.5 Sonnet pricing (approximate)
        $inputCostPer1M = 3.00; // EUR
        $outputCostPer1M = 15.00; // EUR
        
        $inputCost = ($usage['input_tokens'] / 1000000) * $inputCostPer1M;
        $outputCost = ($usage['output_tokens'] / 1000000) * $outputCostPer1M;
        
        return round($inputCost + $outputCost, 4);
    }
}
```

### MetadataExtractorService.php

```php
<?php

namespace App\Services\Natan;

use App\Services\Natan\DocumentParserService;
use App\Services\Natan\AIAnalyzerService;
use Illuminate\Support\Facades\Log;

/**
 * Orchestrates document processing: parse → AI → validate
 * 
 * @package App\Services\Natan
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - N.A.T.A.N.)
 * @date 2025-10-08
 */
class MetadataExtractorService
{
    private DocumentParserService $parser;
    private AIAnalyzerService $analyzer;
    
    public function __construct(
        DocumentParserService $parser,
        AIAnalyzerService $analyzer
    ) {
        $this->parser = $parser;
        $this->analyzer = $analyzer;
    }
    
    /**
     * Extract metadata from document file
     * 
     * @param string $filePath Path to document
     * @return array Complete EGI-Act metadata
     * @throws \Exception If processing fails
     */
    public function extract(string $filePath): array
    {
        Log::info('N.A.T.A.N. MetadataExtractor: Starting', [
            'file' => basename($filePath)
        ]);
        
        // Step 1: Extract text
        $text = $this->parser->extractText($filePath);
        
        // Step 2: AI analysis
        $metadata = $this->analyzer->analyzeDocument($text);
        
        // Step 3: Post-process and enrich
        $enrichedMetadata = $this->enrichMetadata($metadata, $filePath);
        
        Log::info('N.A.T.A.N. MetadataExtractor: Completed', [
            'file' => basename($filePath),
            'tipo_atto' => $enrichedMetadata['tipo_atto']
        ]);
        
        return $enrichedMetadata;
    }
    
    /**
     * Enrich metadata with additional fields
     * 
     * @param array $metadata Base metadata from AI
     * @param string $filePath Original file path
     * @return array Enriched metadata
     */
    private function enrichMetadata(array $metadata, string $filePath): array
    {
        // Generate document_id from hash
        $metadata['document_id'] = hash_file('sha256', $filePath);
        
        // Add processing timestamp
        $metadata['processed_at'] = now()->toIso8601String();
        
        // Add file info
        $metadata['file_info'] = [
            'name' => basename($filePath),
            'size' => filesize($filePath),
            'mime' => mime_content_type($filePath)
        ];
        
        // Normalize dates to ISO format
        if (isset($metadata['data_atto'])) {
            $metadata['data_atto'] = $this->normalizeDate($metadata['data_atto']);
        }
        
        if (isset($metadata['scadenza']) && $metadata['scadenza']) {
            $metadata['scadenza'] = $this->normalizeDate($metadata['scadenza']);
        }
        
        return $metadata;
    }
    
    /**
     * Normalize date to YYYY-MM-DD format
     * 
     * @param string $date Date in various formats
     * @return string Normalized date
     */
    private function normalizeDate(string $date): string
    {
        try {
            return \Carbon\Carbon::parse($date)->format('Y-m-d');
        } catch (\Exception $e) {
            Log::warning('N.A.T.A.N. MetadataExtractor: Date normalization failed', [
                'input' => $date,
                'error' => $e->getMessage()
            ]);
            return $date;
        }
    }
}
```

---

## 🎨 FRONTEND STRUCTURE

### TypeScript Interfaces

```typescript
// resources/js/natan/types.ts

export interface EgiAct {
  id: number;
  document_id: string;
  tipo_atto: string;
  numero_atto: string;
  data_atto: string;
  oggetto: string;
  ente: string;
  direzione: string;
  responsabile: string;
  importo: number;
  categoria: string[];
  metadata_json: Record<string, any>;
  hash_firma: string;
  blockchain_tx: string;
  qr_code: string;
  processing_status: 'pending' | 'completed' | 'failed';
  created_at: string;
  updated_at: string;
}

export interface ActsFilter {
  tipo_atto?: string;
  data_from?: string;
  data_to?: string;
  importo_min?: number;
  importo_max?: number;
  ente?: string;
  direzione?: string;
  search?: string;
}

export interface ApiResponse<T> {
  data: T;
  meta?: PaginationMeta;
  message?: string;
}

export interface PaginationMeta {
  current_page: number;
  total: number;
  per_page: number;
  last_page: number;
}

export interface Stats {
  total_acts: number;
  by_tipo: Record<string, number>;
  by_month: Array<{month: string; count: number}>;
  avg_importo: number;
  total_ai_cost: number;
}
```

### API Client

```typescript
// resources/js/natan/ApiClient.ts

/**
 * API Client for N.A.T.A.N. endpoints
 * 
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - N.A.T.A.N.)
 * @date 2025-10-08
 */

import type { EgiAct, ActsFilter, ApiResponse, Stats } from './types';

export class NatanApiClient {
  private baseUrl: string;
  private token: string;

  constructor(baseUrl: string = '/api/natan', token: string = '') {
    this.baseUrl = baseUrl;
    this.token = token || this.getTokenFromMeta();
  }

  /**
   * Get CSRF token from meta tag
   */
  private getTokenFromMeta(): string {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') || '' : '';
  }

  /**
   * Generic fetch wrapper with auth
   */
  private async fetch<T>(
    endpoint: string,
    options: RequestInit = {}
  ): Promise<ApiResponse<T>> {
    const headers = new Headers(options.headers);
    
    if (this.token) {
      headers.set('X-CSRF-TOKEN', this.token);
    }
    
    headers.set('Accept', 'application/json');
    headers.set('Content-Type', 'application/json');

    const response = await fetch(`${this.baseUrl}${endpoint}`, {
      ...options,
      headers,
    });

    if (!response.ok) {
      const error = await response.json().catch(() => ({ message: 'Network error' }));
      throw new Error(error.message || `HTTP ${response.status}`);
    }

    return response.json();
  }

  /**
   * Upload document for analysis
   */
  async uploadDocument(file: File): Promise<{ job_id: string; status: string }> {
    const formData = new FormData();
    formData.append('file', file);

    const response = await fetch(`${this.baseUrl}/analyze`, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': this.token,
        'Accept': 'application/json',
      },
      body: formData,
    });

    if (!response.ok) {
      throw new Error(`Upload failed: ${response.status}`);
    }

    return response.json();
  }

  /**
   * Get list of acts with filters
   */
  async getActs(filters: ActsFilter = {}, page: number = 1): Promise<ApiResponse<EgiAct[]>> {
    const params = new URLSearchParams({
      page: String(page),
      ...Object.fromEntries(
        Object.entries(filters)
          .filter(([_, v]) => v !== undefined && v !== '')
          .map(([k, v]) => [k, String(v)])
      ),
    });

    return this.fetch<EgiAct[]>(`/acts?${params}`);
  }

  /**
   * Get single act by ID
   */
  async getAct(id: number): Promise<ApiResponse<EgiAct>> {
    return this.fetch<EgiAct>(`/acts/${id}`);
  }

  /**
   * Search acts by query
   */
  async searchActs(query: string): Promise<ApiResponse<EgiAct[]>> {
    const params = new URLSearchParams({ q: query });
    return this.fetch<EgiAct[]>(`/search?${params}`);
  }

  /**
   * Get statistics
   */
  async getStats(): Promise<ApiResponse<Stats>> {
    return this.fetch<Stats>('/stats');
  }

  /**
   * Poll job status
   */
  async getJobStatus(jobId: string): Promise<{ status: string; act?: EgiAct }> {
    return this.fetch(`/jobs/${jobId}`).then(r => r.data);
  }
}
```

### Upload Manager

```typescript
// resources/js/natan/UploadManager.ts

/**
 * Handles document upload with drag&drop and progress
 * 
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - N.A.T.A.N.)
 * @date 2025-10-08
 */

import { NatanApiClient } from './ApiClient';

export class UploadManager {
  private dropzone: HTMLElement;
  private fileInput: HTMLInputElement;
  private api: NatanApiClient;
  private onUploadComplete?: (jobId: string) => void;

  constructor(
    dropzoneSelector: string,
    fileInputSelector: string,
    api: NatanApiClient
  ) {
    const dropzone = document.querySelector(dropzoneSelector);
    const fileInput = document.querySelector(fileInputSelector);

    if (!dropzone || !fileInput) {
      throw new Error('Dropzone or file input not found');
    }

    this.dropzone = dropzone as HTMLElement;
    this.fileInput = fileInput as HTMLInputElement;
    this.api = api;

    this.init();
  }

  /**
   * Initialize drag&drop listeners
   */
  private init(): void {
    // Prevent default drag behaviors
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
      this.dropzone.addEventListener(eventName, this.preventDefaults, false);
      document.body.addEventListener(eventName, this.preventDefaults, false);
    });

    // Highlight drop zone when item is dragged over it
    ['dragenter', 'dragover'].forEach(eventName => {
      this.dropzone.addEventListener(eventName, () => this.highlight(), false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
      this.dropzone.addEventListener(eventName, () => this.unhighlight(), false);
    });

    // Handle dropped files
    this.dropzone.addEventListener('drop', (e) => this.handleDrop(e), false);

    // Handle file input change
    this.fileInput.addEventListener('change', () => this.handleFiles(this.fileInput.files));

    // Click dropzone to trigger file input
    this.dropzone.addEventListener('click', () => this.fileInput.click());
  }

  /**
   * Prevent default drag behaviors
   */
  private preventDefaults(e: Event): void {
    e.preventDefault();
    e.stopPropagation();
  }

  /**
   * Highlight dropzone
   */
  private highlight(): void {
    this.dropzone.classList.add('border-blue-500', 'bg-blue-50');
  }

  /**
   * Remove highlight
   */
  private unhighlight(): void {
    this.dropzone.classList.remove('border-blue-500', 'bg-blue-50');
  }

  /**
   * Handle drop event
   */
  private handleDrop(e: DragEvent): void {
    const dt = e.dataTransfer;
    const files = dt?.files;
    if (files) {
      this.handleFiles(files);
    }
  }

  /**
   * Handle file selection/drop
   */
  private async handleFiles(files: FileList | null): Promise<void> {
    if (!files || files.length === 0) return;

    const file = files[0];

    // Validate file type
    if (!this.isValidFile(file)) {
      this.showError('Tipo file non valido. Solo PDF o P7M.');
      return;
    }

    // Validate file size (max 10MB)
    if (file.size > 10 * 1024 * 1024) {
      this.showError('File troppo grande. Max 10MB.');
      return;
    }

    await this.uploadFile(file);
  }

  /**
   * Validate file type
   */
  private isValidFile(file: File): boolean {
    const validTypes = ['application/pdf', 'application/pkcs7-mime'];
    const validExtensions = ['.pdf', '.p7m'];
    
    return validTypes.includes(file.type) || 
           validExtensions.some(ext => file.name.toLowerCase().endsWith(ext));
  }

  /**
   * Upload file to server
   */
  private async uploadFile(file: File): Promise<void> {
    this.showProgress(file.name);

    try {
      const result = await this.api.uploadDocument(file);
      
      this.showSuccess(`Upload completato. Processing...`);
      
      if (this.onUploadComplete) {
        this.onUploadComplete(result.job_id);
      }

      // Poll job status
      this.pollJobStatus(result.job_id);

    } catch (error) {
      this.showError(error instanceof Error ? error.message : 'Upload fallito');
    }
  }

  /**
   * Poll job status until completed
   */
  private async pollJobStatus(jobId: string): Promise<void> {
    const maxAttempts = 60; // 60 * 2s = 2 min max
    let attempts = 0;

    const poll = async () => {
      try {
        const result = await this.api.getJobStatus(jobId);

        if (result.status === 'completed') {
          this.showSuccess('Documento processato con successo!');
          // Trigger refresh of acts table
          window.dispatchEvent(new CustomEvent('natan:act-processed', { detail: result.act }));
          return;
        }

        if (result.status === 'failed') {
          this.showError('Processing fallito. Riprova.');
          return;
        }

        // Still processing, poll again
        attempts++;
        if (attempts < maxAttempts) {
          setTimeout(poll, 2000);
        } else {
          this.showError('Timeout: processing troppo lungo.');
        }

      } catch (error) {
        this.showError('Errore durante il polling dello stato.');
      }
    };

    poll();
  }

  /**
   * Show progress indicator
   */
  private showProgress(filename: string): void {
    this.setMessage(`Uploading ${filename}...`, 'info');
  }

  /**
   * Show success message
   */
  private showSuccess(message: string): void {
    this.setMessage(message, 'success');
  }

  /**
   * Show error message
   */
  private showError(message: string): void {
    this.setMessage(message, 'error');
  }

  /**
   * Set message in UI
   */
  private setMessage(message: string, type: 'info' | 'success' | 'error'): void {
    // Find or create message container
    let messageEl = this.dropzone.querySelector('.upload-message');
    
    if (!messageEl) {
      messageEl = document.createElement('div');
      messageEl.className = 'upload-message mt-4 p-3 rounded text-sm';
      this.dropzone.appendChild(messageEl);
    }

    // Set styling based on type
    messageEl.className = 'upload-message mt-4 p-3 rounded text-sm ';
    
    switch (type) {
      case 'info':
        messageEl.className += 'bg-blue-100 text-blue-800';
        break;
      case 'success':
        messageEl.className += 'bg-green-100 text-green-800';
        break;
      case 'error':
        messageEl.className += 'bg-red-100 text-red-800';
        break;
    }

    messageEl.textContent = message;

    // Auto-hide success/error after 5s
    if (type !== 'info') {
      setTimeout(() => {
        messageEl?.remove();
      }, 5000);
    }
  }

  /**
   * Set upload complete callback
   */
  public onComplete(callback: (jobId: string) => void): void {
    this.onUploadComplete = callback;
  }
}
```

### Acts Table

```typescript
// resources/js/natan/ActsTable.ts

/**
 * Sortable, filterable table for EGI Acts
 * 
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - N.A.T.A.N.)
 * @date 2025-10-08
 */

import type { EgiAct, ActsFilter } from './types';
import { NatanApiClient } from './ApiClient';

export class ActsTable {
  private container: HTMLElement;
  private api: NatanApiClient;
  private currentPage: number = 1;
  private filters: ActsFilter = {};

  constructor(containerSelector: string, api: NatanApiClient) {
    const container = document.querySelector(containerSelector);
    
    if (!container) {
      throw new Error('Table container not found');
    }

    this.container = container as HTMLElement;
    this.api = api;

    this.init();
  }

  /**
   * Initialize table
   */
  private async init(): Promise<void> {
    await this.loadActs();
    
    // Listen for new acts
    window.addEventListener('natan:act-processed', () => {
      this.refresh();
    });
  }

  /**
   * Load acts from API
   */
  private async loadActs(): Promise<void> {
    try {
      this.showLoading();

      const response = await this.api.getActs(this.filters, this.currentPage);
      
      this.render(response.data, response.meta);

    } catch (error) {
      this.showError(error instanceof Error ? error.message : 'Errore caricamento atti');
    }
  }

  /**
   * Render table
   */
  private render(acts: EgiAct[], meta?: any): void {
    const tableHtml = `
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" data-sort="tipo_atto">
                Tipo Atto
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" data-sort="numero_atto">
                Numero
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" data-sort="data_atto">
                Data
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Oggetto
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Direzione
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" data-sort="importo">
                Importo
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Azioni
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            ${acts.map(act => this.renderRow(act)).join('')}
          </tbody>
        </table>
      </div>
      ${meta ? this.renderPagination(meta) : ''}
    `;

    this.container.innerHTML = tableHtml;
    this.attachEventListeners();
  }

  /**
   * Render single row
   */
  private renderRow(act: EgiAct): string {
    return `
      <tr class="hover:bg-gray-50 cursor-pointer" data-act-id="${act.id}">
        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
          ${this.escapeHtml(act.tipo_atto)}
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
          ${this.escapeHtml(act.numero_atto)}
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
          ${this.formatDate(act.data_atto)}
        </td>
        <td class="px-6 py-4 text-sm text-gray-900 max-w-md truncate">
          ${this.escapeHtml(act.oggetto)}
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
          ${this.escapeHtml(act.direzione || '-')}
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
          ${act.importo ? this.formatCurrency(act.importo) : '-'}
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
          <button class="text-blue-600 hover:text-blue-900 mr-3" data-action="view" data-act-id="${act.id}">
            Dettagli
          </button>
          <button class="text-green-600 hover:text-green-900" data-action="qr" data-qr="${act.qr_code}">
            QR
          </button>
        </td>
      </tr>
    `;
  }

  /**
   * Render pagination
   */
  private renderPagination(meta: any): string {
    const { current_page, last_page } = meta;
    
    return `
      <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6 mt-4">
        <div class="flex-1 flex justify-between sm:hidden">
          <button 
            class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
            data-page="${current_page - 1}"
            ${current_page === 1 ? 'disabled' : ''}
          >
            Precedente
          </button>
          <button 
            class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
            data-page="${current_page + 1}"
            ${current_page === last_page ? 'disabled' : ''}
          >
            Successiva
          </button>
        </div>
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
          <div>
            <p class="text-sm text-gray-700">
              Pagina <span class="font-medium">${current_page}</span> di <span class="font-medium">${last_page}</span>
            </p>
          </div>
          <div>
            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
              ${this.renderPaginationButtons(current_page, last_page)}
            </nav>
          </div>
        </div>
      </div>
    `;
  }

  /**
   * Render pagination buttons
   */
  private renderPaginationButtons(current: number, last: number): string {
    const buttons: string[] = [];
    const range = 2; // Show 2 pages before and after current

    // Previous button
    buttons.push(`
      <button 
        class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
        data-page="${current - 1}"
        ${current === 1 ? 'disabled' : ''}
      >
        ‹
      </button>
    `);

    // Page numbers
    for (let i = Math.max(1, current - range); i <= Math.min(last, current + range); i++) {
      const isActive = i === current;
      buttons.push(`
        <button 
          class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium ${
            isActive 
              ? 'z-10 bg-blue-50 border-blue-500 text-blue-600' 
              : 'bg-white text-gray-700 hover:bg-gray-50'
          }"
          data-page="${i}"
          ${isActive ? 'disabled' : ''}
        >
          ${i}
        </button>
      `);
    }

    // Next button
    buttons.push(`
      <button 
        class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
        data-page="${current + 1}"
        ${current === last ? 'disabled' : ''}
      >
        ›
      </button>
    `);

    return buttons.join('');
  }

  /**
   * Attach event listeners
   */
  private attachEventListeners(): void {
    // Row click - show detail
    this.container.querySelectorAll('tr[data-act-id]').forEach(row => {
      row.addEventListener('click', (e) => {
        const target = e.target as HTMLElement;
        if (target.tagName !== 'BUTTON') {
          const actId = (row as HTMLElement).dataset.actId;
          if (actId) this.showDetail(parseInt(actId));
        }
      });
    });

    // Action buttons
    this.container.querySelectorAll('button[data-action]').forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.stopPropagation();
        const action = (btn as HTMLElement).dataset.action;
        
        if (action === 'view') {
          const actId = (btn as HTMLElement).dataset.actId;
          if (actId) this.showDetail(parseInt(actId));
        }
        
        if (action === 'qr') {
          const qrCode = (btn as HTMLElement).dataset.qr;
          if (qrCode) this.showQR(qrCode);
        }
      });
    });

    // Pagination
    this.container.querySelectorAll('button[data-page]').forEach(btn => {
      btn.addEventListener('click', () => {
        const page = (btn as HTMLElement).dataset.page;
        if (page) {
          this.currentPage = parseInt(page);
          this.loadActs();
        }
      });
    });

    // Sort headers
    this.container.querySelectorAll('th[data-sort]').forEach(th => {
      th.addEventListener('click', () => {
        const sortField = (th as HTMLElement).dataset.sort;
        // TODO: Implement sorting
        console.log('Sort by:', sortField);
      });
    });
  }

  /**
   * Show act detail modal
   */
  private async showDetail(actId: number): Promise<void> {
    try {
      const response = await this.api.getAct(actId);
      const act = response.data;

      // Dispatch event for modal to handle
      window.dispatchEvent(new CustomEvent('natan:show-detail', { detail: act }));

    } catch (error) {
      console.error('Failed to load act detail:', error);
    }
  }

  /**
   * Show QR code modal
   */
  private showQR(qrCode: string): void {
    window.dispatchEvent(new CustomEvent('natan:show-qr', { detail: qrCode }));
  }

  /**
   * Apply filters
   */
  public applyFilters(filters: ActsFilter): void {
    this.filters = filters;
    this.currentPage = 1;
    this.loadActs();
  }

  /**
   * Refresh table
   */
  public refresh(): void {
    this.loadActs();
  }

  /**
   * Show loading state
   */
  private showLoading(): void {
    this.container.innerHTML = `
      <div class="flex justify-center items-center py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
      </div>
    `;
  }

  /**
   * Show error message
   */
  private showError(message: string): void {
    this.container.innerHTML = `
      <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-red-800">
        <p class="font-medium">Errore</p>
        <p class="text-sm">${this.escapeHtml(message)}</p>
      </div>
    `;
  }

  /**
   * Format date for display
   */
  private formatDate(dateString: string): string {
    const date = new Date(dateString);
    return date.toLocaleDateString('it-IT', {
      year: 'numeric',
      month: '2-digit',
      day: '2-digit'
    });
  }

  /**
   * Format currency
   */
  private formatCurrency(amount: number): string {
    return new Intl.NumberFormat('it-IT', {
      style: 'currency',
      currency: 'EUR'
    }).format(amount);
  }

  /**
   * Escape HTML
   */
  private escapeHtml(text: string): string {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
  }
}
```

---

## 🧪 TESTING STRATEGY

### Unit Tests

**DocumentParserService:**
```php
// tests/Unit/Services/Natan/DocumentParserServiceTest.php

it('extracts text from valid PDF', function () {
    $service = app(DocumentParserService::class);
    $testPdf = storage_path('tests/documents/determina_sample.pdf');
    
    $text = $service->extractText($testPdf);
    
    expect($text)->toBeString();
    expect(strlen($text))->toBeGreaterThan(100);
});

it('throws exception on invalid file', function () {
    $service = app(DocumentParserService::class);
    
    expect(fn() => $service->extractText('nonexistent.pdf'))
        ->toThrow(Exception::class);
});
```

**AIAnalyzerService:**
```php
// tests/Unit/Services/Natan/AIAnalyzerServiceTest.php

it('analyzes document and returns valid JSON', function () {
    $service = app(AIAnalyzerService::class);
    $sampleText = file_get_contents(storage_path('tests/documents/determina_sample.txt'));
    
    $metadata = $service->analyzeDocument($sampleText);
    
    expect($metadata)->toBeArray();
    expect($metadata)->toHaveKeys(['tipo_atto', 'data_atto', 'oggetto']);
});

it('validates required fields', function () {
    $service = app(AIAnalyzerService::class);
    $invalidText = "Lorem ipsum dolor sit amet";
    
    expect(fn() => $service->analyzeDocument($invalidText))
        ->toThrow(Exception::class, 'validation failed');
});
```

### Feature Tests

**API Endpoints:**
```php
// tests/Feature/Natan/ActsApiTest.php

it('uploads document and creates act', function () {
    $user = User::factory()->create();
    $file = UploadedFile::fake()->create('test.pdf', 1000, 'application/pdf');
    
    $response = $this->actingAs($user)
        ->post('/api/natan/analyze', ['file' => $file]);
    
    $response->assertStatus(200);
    $response->assertJsonStructure(['job_id', 'status']);
});

it('returns paginated acts list', function () {
    $user = User::factory()->create();
    EgiAct::factory(30)->create();
    
    $response = $this->actingAs($user)
        ->get('/api/natan/acts?page=2');
    
    $response->assertStatus(200);
    $response->assertJsonStructure([
        'data' => [['id', 'tipo_atto', 'oggetto']],
        'meta' => ['current_page', 'total']
    ]);
});

it('filters acts by tipo_atto', function () {
    $user = User::factory()->create();
    EgiAct::factory()->create(['tipo_atto' => 'Determina']);
    EgiAct::factory()->create(['tipo_atto' => 'Delibera']);
    
    $response = $this->actingAs($user)
        ->get('/api/natan/acts?tipo=Determina');
    
    $response->assertStatus(200);
    $response->assertJsonCount(1, 'data');
});
```

### Accuracy Validation

```php
// tests/Feature/Natan/AccuracyValidationTest.php

it('achieves >95% accuracy on sample documents', function () {
    $service = app(MetadataExtractorService::class);
    $testDocs = glob(storage_path('tests/documents/validation/*.pdf'));
    
    $correct = 0;
    $total = 0;
    
    foreach ($testDocs as $doc) {
        $extracted = $service->extract($doc);
        $expected = json_decode(file_get_contents($doc . '.expected.json'), true);
        
        foreach ($expected as $field => $value) {
            $total++;
            if ($extracted[$field] === $value) {
                $correct++;
            }
        }
    }
    
    $accuracy = ($correct / $total) * 100;
    
    expect($accuracy)->toBeGreaterThan(95.0);
    
    echo "\nAccuracy validation: {$accuracy}% ({$correct}/{$total} fields correct)\n";
});
```

---

## 📊 PERFORMANCE OPTIMIZATION

### Queue Processing

**Setup Queue Worker:**
```bash
# config/queue.php
'connections' => [
    'natan' => [
        'driver' => 'database',
        'table' => 'jobs',
        'queue' => 'natan-processing',
        'retry_after' => 300,
    ],
],

# Supervisor config
[program:natan-worker]
command=php /path/to/artisan queue:work natan --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
user=forge
redirect_stderr=true
stdout_logfile=/path/to/worker.log
```

**Job Class:**
```php
// app/Jobs/ProcessDocument.php

namespace App\Jobs;

use App\Models\EgiAct;
use App\Services\Natan\MetadataExtractorService;
use App\Services\BlockchainService;
use App\Services\QRService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Process document asynchronously: extract metadata, certify blockchain, generate QR
 * 
 * @package App\Jobs
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - N.A.T.A.N.)
 * @date 2025-10-08
 */
class ProcessDocument implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300; // 5 minutes
    public $tries = 3;

    private string $filePath;
    private string $jobId;

    /**
     * Create a new job instance
     */
    public function __construct(string $filePath, string $jobId)
    {
        $this->filePath = $filePath;
        $this->jobId = $jobId;
        $this->onQueue('natan-processing');
    }

    /**
     * Execute the job
     */
    public function handle(
        MetadataExtractorService $extractor,
        BlockchainService $blockchain,
        QRService $qr
    ): void {
        Log::info('N.A.T.A.N. ProcessDocument: Starting', [
            'job_id' => $this->jobId,
            'file' => basename($this->filePath)
        ]);

        try {
            // Step 1: Extract metadata with AI
            $metadata = $extractor->extract($this->filePath);

            // Step 2: Create EgiAct record
            $act = EgiAct::create([
                'document_id' => $metadata['document_id'],
                'tipo_atto' => $metadata['tipo_atto'],
                'numero_atto' => $metadata['numero_atto'] ?? null,
                'data_atto' => $metadata['data_atto'],
                'oggetto' => $metadata['oggetto'],
                'ente' => $metadata['ente'] ?? null,
                'direzione' => $metadata['direzione'] ?? null,
                'responsabile' => $metadata['responsabile'] ?? null,
                'importo' => $metadata['importo'] ?? null,
                'metadata_json' => $metadata,
                'hash_firma' => hash_file('sha256', $this->filePath),
                'processing_status' => 'completed',
                'ai_tokens_used' => $metadata['_ai_usage']['input_tokens'] + $metadata['_ai_usage']['output_tokens'],
                'ai_cost' => $metadata['_ai_usage']['cost_estimate'],
            ]);

            // Step 3: Certify on blockchain
            $blockchainTx = $blockchain->certify(
                $act->hash_firma,
                $metadata
            );
            
            $act->update(['blockchain_tx' => $blockchainTx]);

            // Step 4: Generate QR code
            $qrCode = $qr->generate([
                'document_id' => $act->document_id,
                'verify_url' => route('natan.verify', $act->document_id)
            ]);
            
            $act->update(['qr_code' => $qrCode]);

            Log::info('N.A.T.A.N. ProcessDocument: Completed', [
                'job_id' => $this->jobId,
                'act_id' => $act->id
            ]);

            // Update job status cache
            cache()->put("natan:job:{$this->jobId}", [
                'status' => 'completed',
                'act' => $act
            ], now()->addHours(24));

        } catch (\Exception $e) {
            Log::error('N.A.T.A.N. ProcessDocument: Failed', [
                'job_id' => $this->jobId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Update job status
            cache()->put("natan:job:{$this->jobId}", [
                'status' => 'failed',
                'error' => $e->getMessage()
            ], now()->addHours(24));

            throw $e;
        }
    }

    /**
     * Handle job failure
     */
    public function failed(\Throwable $exception): void
    {
        Log::critical('N.A.T.A.N. ProcessDocument: Job failed permanently', [
            'job_id' => $this->jobId,
            'exception' => $exception->getMessage()
        ]);

        cache()->put("natan:job:{$this->jobId}", [
            'status' => 'failed',
            'error' => 'Processing failed after multiple retries'
        ], now()->addHours(24));
    }
}
```

### Caching Strategy

```php
// Cache frequently accessed stats
Cache::remember('natan:stats', now()->addMinutes(10), function () {
    return [
        'total_acts' => EgiAct::count(),
        'by_tipo' => EgiAct::groupBy('tipo_atto')->selectRaw('tipo_atto, count(*) as count')->pluck('count', 'tipo_atto'),
        'avg_importo' => EgiAct::avg('importo'),
        'total_ai_cost' => EgiAct::sum('ai_cost'),
    ];
});

// Cache individual acts for fast detail view
Cache::remember("natan:act:{$actId}", now()->addHour(), function () use ($actId) {
    return EgiAct::findOrFail($actId);
});
```

---

## 🔒 SECURITY & GDPR

### Data Retention Policy

**Auto-cleanup Job:**
```php
// app/Console/Commands/CleanupNatanLogs.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Cleanup N.A.T.A.N. audit logs per GDPR retention policy
 * 
 * @package App\Console\Commands
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - N.A.T.A.N.)
 * @date 2025-10-08
 */
class CleanupNatanLogs extends Command
{
    protected $signature = 'natan:cleanup-logs';
    protected $description = 'Delete audit logs older than 90 days (GDPR compliance)';

    public function handle(): int
    {
        $cutoffDate = Carbon::now()->subDays(90);
        
        // Delete old audit logs
        $deleted = DB::table('audit_logs')
            ->where('auditable_type', 'App\\Models\\EgiAct')
            ->where('created_at', '<', $cutoffDate)
            ->delete();
        
        $this->info("Deleted {$deleted} audit log entries older than 90 days.");
        
        return Command::SUCCESS;
    }
}

// Register in app/Console/Kernel.php
protected function schedule(Schedule $schedule): void
{
    $schedule->command('natan:cleanup-logs')->daily();
}
```

### Audit Logging

```php
// Automatic audit trail for all EgiAct operations
// Use spatie/laravel-activitylog

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class EgiAct extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['tipo_atto', 'numero_atto', 'oggetto', 'importo'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
```

### Encryption

```php
// Encrypt sensitive metadata fields
protected $casts = [
    'metadata_json' => 'encrypted:array',
];

// Or selective encryption
public function setResponsabileAttribute($value)
{
    $this->attributes['responsabile'] = encrypt($value);
}

public function getResponsabileAttribute($value)
{
    return decrypt($value);
}
```

---

## 📈 MONITORING & ALERTING

### Laravel Telescope

**Installation:**
```bash
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate
```

**Monitor:**
- API requests performance
- Queue jobs status
- Database queries optimization
- Exceptions and errors
- AI API calls (via HTTP client monitoring)

### Custom Metrics Dashboard

```php
// app/Http/Controllers/Natan/MetricsController.php

public function dashboard()
{
    return view('natan.metrics', [
        'metrics' => [
            'today' => [
                'acts_processed' => EgiAct::whereDate('created_at', today())->count(),
                'avg_processing_time' => $this->getAvgProcessingTime(today()),
                'ai_cost' => EgiAct::whereDate('created_at', today())->sum('ai_cost'),
                'failed_jobs' => FailedJob::whereDate('failed_at', today())->count(),
            ],
            'this_week' => [
                'acts_processed' => EgiAct::whereBetween('created_at', [now()->startOfWeek(), now()])->count(),
                'accuracy_rate' => $this->calculateAccuracy('week'),
            ],
            'this_month' => [
                'acts_processed' => EgiAct::whereMonth('created_at', now()->month)->count(),
                'total_cost' => EgiAct::whereMonth('created_at', now()->month)->sum('ai_cost'),
            ],
        ]
    ]);
}
```

### Budget Alerts

```php
// app/Console/Commands/CheckNatanBudget.php

class CheckNatanBudget extends Command
{
    protected $signature = 'natan:check-budget';

    public function handle(): int
    {
        $monthlyBudget = config('natan.monthly_budget', 100.00); // EUR
        $currentSpend = EgiAct::whereMonth('created_at', now()->month)->sum('ai_cost');
        
        $percentUsed = ($currentSpend / $monthlyBudget) * 100;
        
        if ($percentUsed > 80) {
            // Send alert to admin
            Mail::to(config('natan.admin_email'))
                ->send(new BudgetAlert($currentSpend, $monthlyBudget));
            
            $this->warn("Budget alert: {$percentUsed}% used ({$currentSpend}€ / {$monthlyBudget}€)");
        }
        
        return Command::SUCCESS;
    }
}
```

---

## 🚀 DEPLOYMENT CHECKLIST

### Pre-Production

- [ ] All tests passing (>80% coverage)
- [ ] Environment variables configured (.env.production)
  ```
  ANTHROPIC_API_KEY=sk-ant-...
  ANTHROPIC_MODEL=claude-3-5-sonnet-20241022
  NATAN_MONTHLY_BUDGET=100
  NATAN_ADMIN_EMAIL=fabio@florenceegi.org
  ```
- [ ] Database migrations run
- [ ] Queue workers configured (Supervisor)
- [ ] Cron jobs scheduled
- [ ] Laravel Telescope enabled (optional)
- [ ] SSL certificate valid
- [ ] Backup strategy in place
- [ ] Monitoring alerts configured

### Production Launch

- [ ] Smoke test: upload 1 documento, verify full flow
- [ ] Performance test: 10 concurrent uploads
- [ ] Error handling test: upload invalid file
- [ ] Accuracy validation: 20 documenti reali vs manual check
- [ ] User training completed (operatori Firenze)
- [ ] Documentation published
- [ ] Rollback plan ready

---

## 📚 DOCUMENTATION

### User Manual (1-pager per operatori PA)

```markdown
# N.A.T.A.N. - Guida Rapida Operatore

## Upload Documento

1. Accedi a dashboard N.A.T.A.N.
2. Trascina PDF firmato nell'area upload o clicca per selezionare
3. Attendi conferma processing (circa 30 secondi)
4. Documento appare automaticamente in tabella atti

## Ricerca Atti

**Ricerca veloce:** Usa barra ricerca in alto
**Filtri avanzati:** 
- Tipo atto (Determina, Delibera, etc)
- Data (da/a)
- Importo (min/max)
- Direzione

## Visualizza Dettagli

- Clicca su riga tabella per aprire scheda completa
- Visualizza metadati estratti
- Verifica blockchain via QR code
- Esporta JSON se necessario

## Risoluzione Problemi

**Upload fallito:** Verifica che file sia PDF firmato, max 10MB
**Metadati errati:** Segnala a [email supporto], revisione manuale
**QR non funziona:** Controlla connessione internet, riprova
```

### API Documentation (OpenAPI)

```yaml
openapi: 3.0.0
info:
  title: N.A.T.A.N. API
  version: 1.0.0
  description: Document Intelligence API for Public Administration

servers:
  - url: https://florenceegi.org/api/natan

paths:
  /analyze:
    post:
      summary: Upload document for AI analysis
      requestBody:
        content:
          multipart/form-data:
            schema:
              type: object
              properties:
                file:
                  type: string
                  format: binary
      responses:
        '200':
          description: Upload successful
          content:
            application/json:
              schema:
                type: object
                properties:
                  job_id:
                    type: string
                    format: uuid
                  status:
                    type: string
                    enum: [processing]

  /acts:
    get:
      summary: Get paginated list of acts
      parameters:
        - name: tipo
          in: query
          schema:
            type: string
        - name: data_from
          in: query
          schema:
            type: string
            format: date
        - name: page
          in: query
          schema:
            type: integer
      responses:
        '200':
          description: Acts list
          content:
            application/json:
              schema:
                type: object
                properties:
                  data:
                    type: array
                    items:
                      $ref: '#/components/schemas/EgiAct'
                  meta:
                    $ref: '#/components/schemas/PaginationMeta'

components:
  schemas:
    EgiAct:
      type: object
      properties:
        id:
          type: integer
        document_id:
          type: string
        tipo_atto:
          type: string
        numero_atto:
          type: string
        data_atto:
          type: string
          format: date
        oggetto:
          type: string
        importo:
          type: number
          format: float
        blockchain_tx:
          type: string
```

---

## 🎓 TRAINING MATERIALS

### Demo Script per Sparavigna (5 minuti)

**Slide 1: Upload**
- Mostra drag&drop documento reale Firenze
- Evidenzia velocità: "30 secondi vs 5 minuti manuale"

**Slide 2: Metadati AI**
- Mostra scheda JSON generata automaticamente
- Evidenzia accuracy: "Tutti i campi corretti"

**Slide 3: Blockchain**
- Mostra QR code verification
- Spiega: "Hash immutabile su Algorand, zero costi"

**Slide 4: Dashboard**
- Mostra ricerca semantica: "Trova tutte determine restauro >€30k"
- Mostra KPI real-time: "Trend categorie, tempi pubblicazione"

**Slide 5: Value**
- "20 ore/settimana risparmiate"
- "€1k/anno costi totali vs €15k vendor tradizionale"
- "Firenze first mover, reference per Italia"

### Video Tutorial (3 minuti)

**Script:**
```
0:00-0:30 - Introduzione N.A.T.A.N.
0:30-1:00 - Demo upload documento
1:00-1:30 - Visualizzazione metadati
1:30-2:00 - Ricerca e filtri
2:00-2:30 - Verifica blockchain QR
2:30-3:00 - Export e analytics
```

---

## 🐛 TROUBLESHOOTING

### Common Issues

**Issue: "AI API timeout"**
- Cause: Documento troppo lungo (>5000 parole)
- Solution: Implementa chunking o truncate strategico
- Prevention: Valida lunghezza pre-upload

**Issue: "JSON parsing failed"**
- Cause: Claude response non è JSON valido
- Solution: Retry con prompt "Fix JSON syntax"
- Prevention: Aggiungi examples nel system prompt

**Issue: "Accuracy <95%"**
- Cause: Formato atti Firenze diverso da training
- Solution: Fine-tune prompt con esempi specifici
- Prevention: Test su 50 atti prima pilot

**Issue: "Queue worker died"**
- Cause: Memory limit exceeded
- Solution: Aumenta `memory_limit` PHP o `--memory` flag
- Prevention: Monitor memory usage Horizon

**Issue: "Blockchain transaction failed"**
- Cause: Algorand node down o wallet insufficient funds
- Solution: Check node status, refill wallet
- Prevention: Alert su balance <100 ALGO

---

## 🔄 MAINTENANCE

### Weekly Tasks

- [ ] Review failed jobs queue
- [ ] Check AI cost vs budget
- [ ] Validate accuracy sample (10 random acts)
- [ ] Review error logs Telescope
- [ ] Backup database

### Monthly Tasks

- [ ] Accuracy report (100 acts validation)
- [ ] Performance review (avg processing time)
- [ ] Cost analysis (AI + infrastructure)
- [ ] User feedback collection
- [ ] Prompt optimization if needed

### Quarterly Tasks

- [ ] Security audit
- [ ] GDPR compliance review
- [ ] Infrastructure scaling assessment
- [ ] Feature roadmap update
- [ ] Documentation refresh

---

## 🎯 SUCCESS CRITERIA

### Pilot (8 weeks)

**Must-have:**
- ✅ >95% accuracy metadata extraction
- ✅ <30 sec processing time per documento
- ✅ Zero data breaches
- ✅ GDPR compliance verified DPO

**Nice-to-have:**
- >98% accuracy
- <20 sec processing time
- >8/10 operator satisfaction

### Production (Year 1)

**Target metrics:**
- 24.000 atti processed Firenze
- €1.000 total cost
- 3-5 altri Comuni onboarded
- Zero critical bugs
- 99.9% uptime

---

## 📞 SUPPORT

### Development Support

**Fabio Cherici**
- Email: [email]
- Phone: [phone]
- Hours: Mon-Fri 9-18

**Claude Copilot (AI Partner)**
- Via IDE integration
- 24/7 availability
- Context: Reference this document

### Escalation Path

**L1 - User Issues:** Operatori PA → Fabio
**L2 - Technical Issues:** Fabio → Claude Copilot
**L3 - Critical Bugs:** Fabio → Anthropic Support (AI API) / DigitalOcean (Infra)

---

## ✅ FINAL CHECKLIST

### Before Starting Development

- [ ] Executive Summary read and understood
- [ ] All dependencies available (Composer packages, API keys)
- [ ] Test documents collected (10-15 atti reali Firenze)
- [ ] Database backup before migrations
- [ ] Git branch created: `feature/natan-ai-integration`

### During Development (per phase)

- [ ] Code follows OS2.0 documentation standards
- [ ] AI-readable: clear names, explicit logic
- [ ] GDPR compliance: encryption, audit, retention
- [ ] Tests written for each component (>80% coverage)
- [ ] Performance profiled (<30sec target)

### Before Committing Each File

- [ ] DocBlock complete with OS3.0 signature
- [ ] Error handling comprehensive
- [ ] Logging appropriate (info/error levels)
- [ ] No hardcoded values (use config)
- [ ] TypeScript types defined (frontend)

### Before Demo Sparavigna

- [ ] End-to-end test on 20 atti reali passed
- [ ] Accuracy validation >95% documented
- [ ] Dashboard polished and responsive
- [ ] Demo script rehearsed (5 min)
- [ ] Fallback plan if live demo fails (video)

---

## 🚢 READY TO SHIP

**This document contains everything needed to implement N.A.T.A.N. AI integration.**

**Next steps:**
1. Review TODO list - mark what's done
2. Start Phase 1: AI Integration Core
3. Use this doc as reference in every Copilot session
4. Update checkboxes as you progress
5. When lost, re-read Executive Summary

**Remember:**
- One file at a time (OS3.0 principle)
- REGOLA ZERO: If unsure, ask (don't deduce)
- Test early, test often
- GDPR compliance always
- Ultra Eccellenza standard

**Buon lavoro, Fabio! 🚀**

---

**Document Version:** 1.0  
**Last Updated:** 2025-10-08  
**Status:** Ready for Implementation  
**Next Review:** After Phase 1 completion