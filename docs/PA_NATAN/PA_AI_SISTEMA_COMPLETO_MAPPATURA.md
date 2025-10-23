# 🏛️ SISTEMA PA CON INTEGRAZIONE AI - MAPPATURA COMPLETA

**Data creazione:** 2025-10-23  
**Scopo:** Mappatura completa dei file coinvolti nel sistema PA + N.A.T.A.N. AI  
**Prossima presentazione:** 4-5 giorni

---

## 📋 EXECUTIVE SUMMARY

### Sistema Implementato

**N.A.T.A.N.** (Nodo di Analisi e Tracciamento Atti Notarizzati)

-   Sistema per tokenizzazione atti amministrativi PA su blockchain
-   Integrazione AI per estrazione metadati (Anthropic Claude + Ollama)
-   Batch processing per caricamenti massivi
-   Dashboard PA dedicata
-   Sistema di verifica pubblica con QR code
-   Chat AI conversazionale (RAG) per interrogazione atti

### Stack Tecnologico

-   **Backend:** Laravel 11 + PHP 8.3
-   **AI Cloud:** Anthropic Claude 3.5 Sonnet API
-   **AI Locale:** Ollama (Llama 3.1)
-   **Blockchain:** Algorand (via AlgoKit microservice)
-   **Frontend:** Livewire + Vanilla JS/TS + Tailwind
-   **PDF Processing:** Smalot/PdfParser
-   **Firma Digitale:** SignatureValidationService (QES/PAdES)

---

## 🗂️ FILE COINVOLTI - ORGANIZZATI PER CATEGORIA

### 1️⃣ MODELLI (Database Layer)

**Core Models:**

```
app/Models/Egi.php
  - Modello principale per tutti gli asset (opere + atti PA)
  - Campi PA: pa_protocol_number, pa_protocol_date, pa_doc_type
  - Metadata JSON esteso per atti
  - Blockchain tracking (pa_anchored_at, pa_txid)

app/Models/Collection.php
  - Collections con type 'pa_documents'
  - Organizzazione fascicoli PA

app/Models/User.php
  - Utenti PA Entity (role: pa_entity)
  - Relazioni con Organization per API key

app/Models/PaBatchSource.php
  - Directory monitorate per batch processing
  - Statistiche cached (total, processed, failed)
  - Status: active, paused, error

app/Models/PaBatchJob.php
  - Singolo job di processamento file
  - Retry logic con max_attempts
  - Tracking: hash, status, duration
```

---

### 2️⃣ SERVIZI (Business Logic Layer)

**Servizi Core PA:**

```
app/Services/PaActs/PaActService.php
  - Orchestratore principale workflow PA
  - Gestione upload, validazione, creazione EGI
  - Integrazione con AlgorandService per anchoring

app/Services/PaActs/SignatureValidationService.php
  - Validazione firme digitali QES/PAdES
  - Estrazione certificati X.509
  - Verifica chain of trust

app/Services/PaActs/MerkleTreeService.php
  - Merkle tree per batch anchoring
  - Generazione proof per singoli documenti
  - Verifica proof on-chain

app/Services/PaActs/PaActStatisticsService.php
  - KPI dashboard PA
  - Statistiche tokenizzazione
  - Report aggregati
```

**Servizi AI:**

```
app/Services/AnthropicService.php
  - Client API Anthropic Claude
  - Chat conversazionale
  - Estrazione metadati da documenti
  - GDPR-compliant (solo metadati pubblici)

app/Services/OllamaService.php
  - Client Ollama locale (Llama 3.1)
  - Alternative on-premise per GDPR strict
  - Parsing documenti PA

app/Services/NatanChatService.php
  - Chat AI per PA officials
  - RAG (Retrieval Augmented Generation)
  - Context-aware conversation
  - Audit trail completo

app/Services/RagService.php
  - Recupero documenti rilevanti
  - Semantic search su atti
  - Context building per AI

app/Services/DataSanitizerService.php
  - Sanitizzazione dati prima invio AI
  - Rimozione PII, firme, path sensibili
  - Validazione safety data
```

**Servizi Blockchain:**

```
app/Services/AlgorandService.php
  - Integrazione blockchain Algorand
  - Anchoring hash documenti
  - Transaction management
  - Testnet/Mainnet switching
```

**Servizi Support:**

```
app/Services/Egi/EgiService.php
  - CRUD EGI con role-based filtering
  - Isolation PA vs Creator

app/Services/Auth/AuthRedirectService.php
  - Redirect post-login per PA Entity
  - Dashboard routing dinamico

app/Services/View/ViewService.php
  - Switching viste PA vs Creator
  - Terminologia context-aware
```

---

### 3️⃣ CONTROLLERS

**Controllers PA:**

```
app/Http/Controllers/PA/PADashboardController.php
  - Dashboard PA con KPI
  - Quick stats API
  - Recent acts list

app/Http/Controllers/PA/PAHeritageController.php
  - Gestione patrimonio culturale PA
  - Lista beni + filtri
  - Detail view con CoA

app/Http/Controllers/PA/NatanChatController.php
  - Chat interface AI
  - Message handling
  - Suggestions provider
```

**Controllers PaActs:**

```
app/Http/Controllers/PaActs/PaActController.php
  - Index atti PA
  - Show singolo atto
  - Force tokenize
  - Statistics dashboard

app/Http/Controllers/PaActs/PaActUploadController.php
  - Form upload atto
  - Handle upload con validazione firma
  - Dispatch TokenizePaActJob

app/Http/Controllers/PaActs/PaActPublicController.php
  - Verifica pubblica atto
  - Route: /verify/{public_code}
  - No auth required
  - Trust-minimized verification

app/Http/Controllers/PaActs/PaBatchSourceController.php
  - CRUD batch sources
  - Toggle status (active/paused)
  - Job monitoring
```

**Controllers API:**

```
app/Http/Controllers/Api/PaActApiController.php
  - API per N.A.T.A.N. agent esterno
  - Metadata submission (no file upload)
  - JWT authentication
  - Batch job creation
```

---

### 4️⃣ MIDDLEWARE & HANDLERS

```
app/Http/Middleware/AuthenticateNatanAgent.php
  - Autenticazione API agent via API key
  - Verifica Organization + User PA
  - Rate limiting

app/Handlers/PaActs/PaActUploadHandler.php
  - Upload handler per atti PA
  - Validazione file
  - Signature extraction
  - Hash calculation
```

---

### 5️⃣ JOBS (Queue Processing)

```
app/Jobs/TokenizePaActJob.php
  - Job asincrono tokenizzazione
  - Chiamata AlgorandService
  - Update DB con TXID
  - Error handling + retry logic
  - Max 3 attempts
```

---

### 6️⃣ MENU & NAVIGATION

```
app/Services/Menu/ContextMenus.php
  - Menu dinamico PA vs Creator

app/Services/Menu/Items/PADashboardMenu.php
  - Voice Dashboard PA

app/Services/Menu/Items/PAActsMenu.php (implicito)
  - Gestione atti amministrativi

app/Services/Menu/Items/PABatchProcessorMenu.php
  - Batch processor navigation

app/Services/Menu/Items/PAStatisticsMenu.php
  - Statistiche PA

app/Services/Menu/Items/PAHeritageMenu.php
  - Patrimonio culturale

app/Services/Menu/Items/PACoAMenu.php
  - Certificati autenticità

app/Services/Menu/Items/PAInspectorsMenu.php
  - Gestione ispettori
```

---

### 7️⃣ VIEWS (Frontend)

**Dashboard & Layout:**

```
resources/views/pa/dashboard.blade.php
  - Dashboard principale PA
  - KPI cards
  - Quick actions

resources/views/components/pa-layout.blade.php
  - Layout dedicato PA
  - Sidebar PA-specific
  - Colors: blu #1B365D, oro #D4A574
```

**Atti Amministrativi:**

```
resources/views/pa/acts/index.blade.php
  - Lista atti con filtri
  - Badge tokenizzazione
  - Statistiche header

resources/views/pa/acts/show.blade.php
  - Dettaglio atto
  - Metadata display
  - Blockchain verification badge
  - QR code

resources/views/pa/acts/upload.blade.php
  - Form upload PDF firmato
  - Validazione client-side
  - Checkbox auto-tokenize

resources/views/pa/acts/verify.blade.php
  - Pagina verifica pubblica
  - No auth required
  - Blockchain proof display
```

**Batch Processing:**

```
resources/views/pa/batch/index.blade.php
  - Lista sources monitorate

resources/views/pa/batch/show.blade.php
  - Detail source + jobs list

resources/views/pa/batch/create.blade.php
resources/views/pa/batch/edit.blade.php
  - CRUD forms
```

**N.A.T.A.N. Chat:**

```
resources/views/pa/natan/chat.blade.php
  - Chat interface AI
  - Message history
  - Suggested questions
  - Markdown rendering
```

**Patrimonio Culturale:**

```
resources/views/pa/heritage/index.blade.php
resources/views/pa/heritage/show.blade.php
  - Heritage items (EGI in collections)
  - CoA display

resources/views/egis/pa/index.blade.php
resources/views/egis/pa/show.blade.php
resources/views/egis/pa/create.blade.php
resources/views/egis/pa/edit.blade.php
  - CRUD EGI context PA
```

**Components:**

```
resources/views/components/pa/pa-entity-header.blade.php
resources/views/components/pa/pa-heritage-card.blade.php
resources/views/components/pa/pa-coa-badge.blade.php
resources/views/components/pa/pa-stat-card.blade.php
resources/views/components/pa/pa-action-button.blade.php
  - Componenti riutilizzabili PA-styled

resources/views/components/natan-assistant.blade.php
  - Assistente AI embedding
```

---

### 8️⃣ ROUTES

```
routes/pa-enterprise.php
  - Tutte le route PA prefissate /pa
  - Middleware: auth + role:pa_entity
  - Groups: dashboard, heritage, acts, batch, natan

routes/api.php
  - API routes N.A.T.A.N. agent
  - /api/pa/acts/metadata
  - Middleware: natan.agent
```

---

### 9️⃣ MIGRATIONS

**Core PA:**

```
database/migrations/2025_10_02_075449_add_pa_enterprise_fields_to_collections_table.php
  - Campo type='pa_documents'

database/migrations/2025_10_04_190050_add_pa_acts_metadata_to_egis_table.php
  - Colonne pa_*: protocol, date, doc_type, title, description
  - Colonne blockchain: pa_anchored_at, pa_txid, pa_merkle_proof

database/migrations/2025_10_15_103605_add_blockchain_txid_to_egis_table.php
  - Tracking blockchain transactions

database/migrations/2025_10_15_000001_add_tokenization_error_tracking_to_egis_table.php
  - Error handling per tokenizzazione fallite

database/migrations/2025_10_16_120000_add_batch_fields_to_egis_table.php
  - Campi per batch processing
  - batch_source_id, batch_job_id
```

**Batch Processing:**

```
database/migrations/2025_10_16_100001_add_api_key_to_organizations_table.php
  - API key per autenticazione agent

database/migrations/2025_10_16_100002_create_pa_batch_sources_table.php
  - Directory monitorate

database/migrations/2025_10_16_100003_create_pa_batch_jobs_table.php
  - Jobs individuali
```

---

### 🔟 CONFIGURATION

```
config/services.php
  - Anthropic API config
  - Ollama config
  - Algorand config

config/document_analysis.php
  - Provider selection (Anthropic/Ollama)
  - Prompts AI
  - Timeout settings

config/AllowedFileType.php
  - PA document types allowed
  - PDF, P7M (firmato digitale)

config/egi.php
  - EGI system config
  - PA-specific settings
```

---

### 1️⃣1️⃣ TYPESCRIPT/JAVASCRIPT

```
resources/ts/components/natan-assistant.ts
  - Chat assistant frontend
  - WebSocket o polling per updates

resources/ts/components/assistant-actions.ts
resources/ts/components/butler-options.ts
  - Actions AI assistant
```

---

### 1️⃣2️⃣ CONSOLE COMMANDS

```
app/Console/Commands/GeneratePaApiKey.php
  - Genera API key per Organization PA

app/Console/Commands/ShowPaApiKey.php
  - Display API key esistente
```

---

### 1️⃣3️⃣ ENUMS

```
app/Enums/BusinessType.php
  - PA_ENTITY enum value
  - Display names

app/Enums/PlatformRole.php
  - PA entity role
```

---

### 1️⃣4️⃣ SEEDERS

```
database/seeders/PAEnterpriseDemoSeeder.php
  - Dati demo PA per testing

database/seeders/RolesAndPermissionsSeeder.php
  - Role 'pa_entity' con permessi
```

---

### 1️⃣5️⃣ LANGUAGE FILES

```
resources/lang/it/pa_acts.php
  - Traduzioni specifiche atti PA
  - Terminologia: protocollo, delibera, determina

resources/lang/it/pa_batch.php
  - Traduzioni batch processing

resources/lang/it/pa_heritage.php
  - Traduzioni patrimonio culturale

resources/lang/it/menu.php
  - Voci menu PA

resources/lang/it/errors.php
  - Messaggi errore PA-specific
```

---

### 1️⃣6️⃣ DOCUMENTAZIONE

**Implementation Guides:**

```
docs/ai/context/NATAN_PER_ENTI_IMPLEMENTATION_GUIDE.md
  - Guida implementazione completa
  - TODO list fasi
  - Architecture diagrams

docs/ai/context/PA_ENTERPRISE_ARCHITECTURE.md
  - Architettura PA/Enterprise
  - Mapping Collection→EGI
  - Vocabulary expansion

docs/ai/context/PA_ENTERPRISE_IMPLEMENTATION_GUIDE.md
  - Guida implementazione PA generale
```

**Strategy & Marketing:**

```
docs/ai/NATAN_STRATEGY_FIRENZE_BRIEF.md
  - Brief strategico Comune Firenze
  - Obiettivi, tech stack, timeline
  - Hardware requirements

docs/ai/checklists/pa_strategy_pillar.md
  - PA come pilastro strategico
  - Silent Growth strategy

docs/ai/marketing/PA_ENTERPRISE_BRAND_GUIDELINES.md
  - Brand guidelines PA
  - Colors, tone, terminology
```

**Testing & Checklists:**

```
docs/testing/GUIDA_PRATICA_TESTING_STEP_2_7.md
  - Guida testing pratica
  - Test manuali step-by-step

docs/testing/STEP_2_7_MANUAL_TESTING_CHECKLIST.md
  - Checklist completa testing
  - Authentication, views, services

docs/ai/QES_TESTING_PLAN.md
  - Piano testing firme digitali QES
```

**TODO & Status:**

```
docs/ai/NATAN_TODO_IMPLEMENTATION.md
  - TODO completo N.A.T.A.N.
  - Organizzato per categoria
  - Priorità e effort estimate

docs/ai/context/PA_ENTERPRISE_TODO_MASTER.md
  - TODO master PA/Enterprise
```

**Technical Docs:**

```
docs/ai/DOCUMENT_ANALYSIS_PROVIDERS.md
  - Provider AI: Anthropic vs Ollama
  - Comparazione features, costi, GDPR

docs/ai/blockchain/ALGORAND_CUSTODIAL_SCALING_PROBLEM.md
  - Problematiche scaling Algorand
  - Soluzioni architetturali

docs/ai/NATAN_STAGING_DEPLOYMENT.md
  - Deployment staging server
  - Environment setup
```

---

### 1️⃣7️⃣ SCRIPTS & UTILITIES

```
monitor-pa-tokenization.sh
  - Script monitoring tokenizzazione
  - Tail logs + status check

scripts/download_real_signed_pa_acts.php
  - Download atti firmati reali per testing

scripts/generate_mock_pa_acts.php
  - Genera mock atti per testing

storage/testing/real_signed_pa_acts/
  - Directory con atti test reali
```

---

## 📊 STATO IMPLEMENTAZIONE

### ✅ COMPLETATO (80%)

#### Core Features

-   ✅ Upload PDF firmato con validazione QES/PAdES
-   ✅ Hash SHA-256 documento
-   ✅ Salvataggio database (tabella egis + metadata JSON)
-   ✅ Blockchain anchoring Algorand (via job asincrono)
-   ✅ Dashboard PA con KPI
-   ✅ Lista atti con filtri e statistiche
-   ✅ QR code generation
-   ✅ Public verification code (VER-XXXXXXXXXX)

#### AI Integration

-   ✅ AnthropicService integrato
-   ✅ OllamaService alternativo
-   ✅ NatanChatService con RAG
-   ✅ DataSanitizerService GDPR-compliant
-   ✅ Chat interface frontend

#### Batch Processing

-   ✅ PaBatchSource model
-   ✅ PaBatchJob model
-   ✅ API endpoint per agent esterno
-   ✅ Middleware autenticazione agent
-   ✅ Dashboard batch monitoring

#### Views & UX

-   ✅ PA-specific colors e branding
-   ✅ Dashboard PA
-   ✅ Atti index/show
-   ✅ Upload form
-   ✅ Batch management UI
-   ✅ N.A.T.A.N. chat UI

---

### ⚠️ PARZIALE / IN PROGRESS (15%)

#### AI Features

-   ⚠️ Parsing automatico metadati da PDF
    -   Service implementato ma da testare a fondo
    -   Accuracy validation necessaria
-   ⚠️ Prompt engineering ottimizzato
    -   Prompt base presenti
    -   Serve tuning su atti reali

#### Public Verification

-   ⚠️ Pagina verifica pubblica /verify/{code}
    -   Implementata ma da rifinire UI
    -   Merkle proof display da completare

#### Testing

-   ⚠️ Unit tests

    -   Pochi test esistenti
    -   Coverage bassa

-   ⚠️ Feature tests
    -   Test manuali fatti
    -   Automation tests mancanti

---

### ❌ TODO (5%)

#### High Priority

-   ❌ Merkle tree batch anchoring completo
    -   Service implementato, integrazione da completare
-   ❌ Dashboard widgets avanzati

    -   Grafici temporali
    -   Analytics approfondite

-   ❌ Notification system
    -   In-app notifications
    -   Email alerts

#### Medium Priority

-   ❌ Export FOIA compliance
    -   Export massivo atti per trasparenza
-   ❌ Multi-ente support

    -   Gestione sotto-enti (direzioni, uffici)

-   ❌ API documentation published
    -   Swagger/OpenAPI spec

#### Low Priority

-   ❌ Mobile app (futuro)
-   ❌ Advanced analytics ML

---

## 🎯 COMPONENTI CRITICI PER PRESENTAZIONE

### Must-Have (4-5 giorni)

1. **Upload + Tokenizzazione funzionante end-to-end**

    - Test con PDF firmato reale
    - Verifica blockchain anchoring
    - Display TXID + hash

2. **Dashboard PA con KPI live**

    - Total atti
    - Atti tokenizzati
    - Success rate
    - Grafici base

3. **Chat N.A.T.A.N. AI funzionante**

    - Query: "Riassumi l'atto X"
    - Query: "Quanti atti sul tema Y?"
    - Response time < 5 sec

4. **Pagina verifica pubblica bella**

    - UI professionale
    - QR code scannerizzabile
    - Link Algorand Explorer funzionante

5. **Batch processing demo**
    - Mostrare caricamento multiplo
    - Status tracking
    - Error handling

### Nice-to-Have (se c'è tempo)

1. Grafici temporali atti caricati
2. Export CSV atti
3. Filtri avanzati search
4. Mobile responsive perfetto
5. Accessibility WCAG 2.1 AA

---

## 🔍 FILE DA VERIFICARE PRIORITÀ ALTA

### Testing Immediato

1. `app/Services/PaActs/PaActService.php` - workflow completo
2. `app/Services/AnthropicService.php` - AI parsing
3. `app/Jobs/TokenizePaActJob.php` - tokenizzazione async
4. `app/Http/Controllers/PaActs/PaActController.php` - endpoints PA
5. `resources/views/pa/acts/index.blade.php` - UI principale

### Verification Priority

1. Blockchain anchoring effettivo (non solo log)
2. AI parsing accuracy (test con 10+ atti reali)
3. Error handling robusto (fail gracefully)
4. GDPR compliance (audit DataSanitizerService)
5. Performance (<30 sec per atto end-to-end)

---

## 📝 NOTE FINALI

### Punti di Forza

-   ✅ Architettura solida e modulare
-   ✅ GDPR-compliant by design
-   ✅ Integrazione AI state-of-the-art
-   ✅ Blockchain per trust-minimized verification
-   ✅ Batch processing per scalabilità
-   ✅ UX PA-specific ben curata

### Rischi da Mitigare

-   ⚠️ Accuracy AI parsing (bisogna dimostrare >95%)
-   ⚠️ Performance con volumi alti (stress test)
-   ⚠️ Costi API Anthropic (monitorare budget)
-   ⚠️ Algorand mainnet costs (batch vs single)

### Opportunità

-   🚀 Firenze come reference client
-   🚀 Scaling 50+ comuni Italia
-   🚀 Revenue model chiaro (€1k-5k/anno/comune)
-   🚀 Partnership ANCI, AgID possibili

---

**Fine Mappatura** - Documento creato per testing approfondito pre-presentazione
