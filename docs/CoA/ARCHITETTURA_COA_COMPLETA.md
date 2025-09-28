# ARCHITETTURA COMPLETA SISTEMA CoA (Certificate of Authenticity) - FlorenceEGI

**Documento Tecnico Architetturale**  
**Versione**: 2.0.0 Enterprise  
**Data**: 19 Settembre 2025  
**Autore**: Sistema AI per FlorenceEGI

---

## INDICE

1. [Panoramica Generale](#1-panoramica-generale)
2. [Architettura Database](#2-architettura-database)
3. [Layer Modelli](#3-layer-modelli)
4. [Layer Servizi](#4-layer-servizi)
5. [Layer Controller](#5-layer-controller)
6. [Layer View e Componenti](#6-layer-view-e-componenti)
7. [Sistemi di Sicurezza](#7-sistemi-di-sicurezza)
8. [Routing e API](#8-routing-e-api)
9. [Trait e Helper](#9-trait-e-helper)
10. [Integrazione con Sistema EGI](#10-integrazione-con-sistema-egi)
11. [Funzionalità Implementate](#11-funzionalita-implementate)
12. [Funzionalità Mancanti](#12-funzionalita-mancanti)
13. [Roadmap Implementazione](#13-roadmap-implementazione)

---

## 1. PANORAMICA GENERALE

### 1.1 Architettura del Sistema

Il sistema CoA di FlorenceEGI è un'architettura enterprise multi-layer che gestisce l'emissione, verifica e gestione di certificati di autenticità per artwork digitali. Il sistema implementa un approccio event-sourcing con snapshot immutabili per garantire la tracciabilità completa.

**Stack Tecnologico:**

-   **Framework**: Laravel 11.31
-   **Database**: MariaDB con relazioni complesse
-   **Sicurezza**: SHA-256 hashing, wallet signature, QES integration ready
-   **UI**: Blade Templates + TailwindCSS + Alpine.js
-   **Logging**: UltraLogManager + Ultra Error Manager
-   **GDPR**: Sistema audit completo

### 1.2 Principi Architetturali

-   **Immutabilità**: Snapshot immutabili dei traits al momento dell'emissione
-   **Event Sourcing**: Ogni azione sul certificato genera eventi tracciabili
-   **Role Distinction**: Distinzione tra Author, Creator, Issuer, Activator
-   **Cryptographic Integrity**: Hash SHA-256 per integrità dei dati
-   **Public Verification**: Verifica pubblica senza autenticazione
-   **Privacy by Design**: Compliance GDPR nativa

---

## 2. ARCHITETTURA DATABASE

### 2.1 Tabelle Principali

#### **Tabella `coas`** (Certificati Core)

```sql
- id (Primary Key)
- egi_id (Foreign Key -> egis.id)
- serial (Unique, formato: TEST-{egi_id}-{timestamp})
- status (enum: valid, revoked, suspended)
- issuer_type (enum: platform, gallery, institution)
- issuer_name (VARCHAR)
- issuer_location (VARCHAR, default: 'Firenze, Italia')
- issued_at (TIMESTAMP)
- notes (TEXT, nullable)
- verification_hash (SHA-256)
- qes_signature (BOOLEAN, default: false)
- wallet_signature (BOOLEAN, default: true)
- wallet_public_key (VARCHAR)
- blockchain_asset_id (VARCHAR, nullable)
- creator_info (JSON, per role distinction)
- version (INTEGER, default: 1)
- created_at, updated_at, deleted_at
```

#### **Tabella `coa_snapshots`** (Snapshot Immutabili)

```sql
- id (Primary Key)
- coa_id (Foreign Key -> coas.id)
- egi_traits_version_id (Foreign Key)
- snapshot_hash (SHA-256)
- snapshot_data (JSON, dati completi al momento emissione)
- created_at (TIMESTAMP immutabile)
```

#### **Tabella `coa_events`** (Event Sourcing)

```sql
- id (Primary Key)
- coa_id (Foreign Key)
- event_type (enum: issued, verified, revoked, etc.)
- event_data (JSON)
- user_id (Foreign Key, nullable)
- ip_address (VARCHAR)
- user_agent (TEXT)
- created_at (TIMESTAMP)
```

#### **Tabella `coa_annexes`** (Annessi Pro)

```sql
- id (Primary Key)
- coa_id (Foreign Key)
- type (enum: A_PROVENANCE, B_CONDITION, C_EXHIBITIONS, D_PHOTOS, E_AUTHORIZATION)
- title (VARCHAR)
- content (TEXT)
- file_data (JSON)
- metadata (JSON)
- created_at, updated_at
```

#### **Altre Tabelle di Supporto**

-   `coa_files`: Gestione file allegati
-   `coa_signatures`: Firme digitali multiple
-   `egi_traits_versions`: Versioning traits (relazione con sistema EGI)

### 2.2 Relazioni Database

-   **1:1** Coa ↔ Egi (un certificato per artwork)
-   **1:N** Coa → CoaEvents (event sourcing)
-   **1:N** Coa → CoaAnnexes (annessi multipli)
-   **1:1** Coa → CoaSnapshot (snapshot immutabile)
-   **N:1** Coa → User (issuer)

---

## 3. LAYER MODELLI

### 3.1 Modello Principale: `Coa`

**File**: `app/Models/Coa.php`

**Funzionalità Implementate:**

-   ✅ Relazioni complete con EGI, User, Events, Annexes
-   ✅ Scopes per stati (valid, revoked)
-   ✅ Cast automatici (dates, JSON)
-   ✅ Soft deletes per audit trail
-   ✅ Accessor per hash verification
-   ✅ Methods per status management

**Metodi Chiave:**

```php
- activeCoa() // Scope per certificati attivi
- generateVerificationHash() // Hash SHA-256
- isValid() // Check stato validità
- revoke() // Revoca con audit
- getAnnexes() // Recupero annessi
- toPublicArray() // Dati per verifica pubblica
```

### 3.2 Modelli di Supporto

#### **CoaSnapshot**

-   Gestisce snapshot immutabili al momento emissione
-   Collega con EgiTraitsVersion per versioning
-   Hash verification automatico

#### **CoaEvent**

-   Event sourcing completo
-   Tracciabilità azioni utente
-   Metadata IP/UserAgent per audit

#### **CoaAnnex**

-   Gestione annessi A-E
-   Upload file multipli
-   Metadata strutturati

#### **CoaFile & CoaSignature**

-   Gestione allegati
-   Sistema firme multiple
-   Verifica integrità file

---

## 4. LAYER SERVIZI

### 4.1 Servizio Core: `CoaIssueService`

**File**: `app/Services/Coa/CoaIssueService.php`

**Funzionalità Implementate:**

-   ✅ Emissione certificati con validazione ownership
-   ✅ Controllo duplicati (un CoA per EGI)
-   ✅ Generazione serial number unico
-   ✅ Creazione snapshot immutabile traits
-   ✅ Role distinction (Author vs Creator vs Issuer)
-   ✅ Event logging completo
-   ✅ Transazioni atomiche database
-   ✅ Integrazione EgiTraitsExtraction

**Workflow Emissione:**

1. Validazione ownership utente
2. Check duplicati esistenti
3. Generazione serial univoco
4. Snapshot traits versioning
5. Creazione record CoA
6. Event logging
7. Audit trail GDPR

### 4.2 Altri Servizi Critici

#### **TraitsSnapshotService**

-   ✅ Snapshot immutabili traits al momento emissione
-   ✅ Versioning con hash SHA-256
-   ✅ Collegamento con EgiTraitsVersion
-   ✅ Integrità dati garantita

#### **SerialGenerator**

-   ✅ Generazione serial number format: `{PREFIX}-{EGI_ID}-{TIMESTAMP}`
-   ✅ Unicità garantita con controlli database
-   ✅ Configurabile per ambienti (TEST/PROD)

#### **HashingService**

-   ✅ Hashing SHA-256 multi-algorithm
-   ✅ Verifica integrità dati
-   ✅ Hash traits, metadata, file
-   ✅ Verifica hash esistenti

#### **SignatureService**

-   ✅ Integrazione wallet signature
-   ✅ Preparazione QES (Qualified Electronic Signature)
-   ✅ Multi-signature support
-   ✅ Verifica firme

#### **CoaRevocationService**

-   ✅ Revoca certificati con motivo
-   ✅ Event logging revoca
-   ✅ Notifica stakeholder
-   ✅ Audit trail completo

#### **CoaPdfService**

-   ✅ Generazione PDF certificati
-   ✅ Template professional
-   ✅ QR Code embedded
-   ✅ Watermark digitale

#### **AnnexService**

-   ✅ Gestione annessi A-E completi
-   ✅ Upload file multipli
-   ✅ Metadata structured
-   ✅ Access control

#### **VerifyPageService**

-   ✅ Pagine verifica pubblica
-   ✅ Rate limiting
-   ✅ Analytics verification
-   ✅ SEO optimized

### 4.3 Servizi di Integrazione

#### **CoaAddendumService**

-   ✅ Addendum policy versionate
-   ✅ Updates non-breaking
-   ✅ Compatibility management

#### **BundleService**

-   ✅ Bundling multiple artworks
-   ✅ Collection certificates
-   ✅ Batch operations

---

## 5. LAYER CONTROLLER

### 5.1 Controller Principale: `VerifyController`

**File**: `app/Http/Controllers/VerifyController.php`

**Endpoints Implementati:**

-   ✅ `GET /coa/verify/certificate/{serial}` - Verifica JSON/HTML
-   ✅ `GET /coa/verify/certificate/{serial}/view` - Vista certificato
-   ✅ `GET /coa/verify/hash/{hash}` - Verifica per hash
-   ✅ `GET /coa/verify/qr/{hash}` - Verifica QR code
-   ✅ `GET /coa/verify/certificate/{serial}/annexes` - Verifica annessi

**Funzionalità Implementate:**

-   ✅ Rate limiting (30 req/5min per IP)
-   ✅ Response JSON/HTML auto-detect
-   ✅ Estrazione traits completa con EgiTraitsExtraction
-   ✅ Role distinction Author/Creator/Issuer
-   ✅ Public verification senza auth
-   ✅ Error handling robusto
-   ✅ Logging completo UEM
-   ✅ GDPR compliance

**Dati Passati al Template:**

```php
'certificate' => [
    'serial', 'status', 'issued_at', 'issuer_name',
    'verification_hash', 'qes_signature', 'wallet_signature'
],
'artwork' => [
    'name', 'internal_id', 'description', 'author',
    'year', 'technique', 'support', 'dimensions', 'edition',
    'traits' => [...], // ARRAY COMPLETO TRAITS
    'thumbnail', 'dossier_link'
],
'verification' => ['is_valid', 'verified_at', 'verification_url'],
'creator' => [...], // Role distinction info
'annexes' => [...] // Annessi A-E
```

### 5.2 Controller Gestionale: `CoaController`

**File**: `app/Http/Controllers/CoaController.php`

**Endpoints Implementati:**

-   ✅ `GET /coa` - Dashboard CoA utente
-   ✅ `POST /coa/issue/{egi}` - Emissione nuovo certificato
-   ✅ `GET /coa/{coa}` - Dettaglio certificato
-   ✅ `POST /coa/{coa}/revoke` - Revoca certificato
-   ✅ `GET /coa/{coa}/pdf` - Download PDF
-   ✅ `GET /coa/{coa}/annexes` - Gestione annessi

**Funzionalità:**

-   ✅ CRUD completo certificati
-   ✅ Authorization middleware
-   ✅ Validation rules
-   ✅ Event dispatch
-   ✅ Error handling UEM

### 5.3 Controller Annessi: `AnnexController`

**File**: `app/Http/Controllers/AnnexController.php`

**Gestione Annessi A-E:**

-   ✅ A_PROVENANCE - Storia provenance
-   ✅ B_CONDITION - Stato conservazione
-   ✅ C_EXHIBITIONS - Mostre e esposizioni
-   ✅ D_PHOTOS - Galleria foto HD
-   ✅ E_AUTHORIZATION - Documenti autorizzazione

### 5.4 Controller Addendum: `CoaAddendumController`

-   ✅ Gestione policy versionate
-   ✅ Addendum Pro features
-   ✅ Compatibility updates

---

## 6. LAYER VIEW E COMPONENTI

### 6.1 Template Pubblici

**Directory**: `resources/views/coa/public/`

#### **certificate.blade.php** - Template Certificato Principale

**Implementato Completamente:**

-   ✅ Design responsive enterprise-grade
-   ✅ Header con status verification
-   ✅ Due colonne: Info Opera + Dettagli Certificato
-   ✅ Sezione traits "Metadati e Caratteristiche dell'Opera"
-   ✅ Role distinction Author/Creator/Issuer display
-   ✅ QR Code verification integrato
-   ✅ Hash SHA-256 con copy button
-   ✅ Action buttons (PDF, JSON, Stampa, API, Condividi)
-   ✅ Thumbnail artwork con link dossier
-   ✅ Creator info quando Creator ≠ Author
-   ✅ Annexes links (quando disponibili)

**Struttura Sections:**

```blade
1. Header Status (Valid/Revoked)
2. Main Grid (2 columns)
   ├── Informazioni Opera
   │   ├── Titolo + ID interno
   │   ├── Anno, Tecnica, Supporto, Dimensioni, Edizione
   │   ├── Autore
   │   ├── SEZIONE TRAITS (IMPLEMENTATA)
   │   ├── Creator Block (role distinction)
   │   └── Thumbnail + Dossier link
   └── Dettagli Certificato
       ├── Data emissione, Emesso da, Luogo
       ├── Firma (QES/Wallet)
       ├── Hash SHA-256
       ├── QR Code
       └── Stato validità
3. Action Buttons
4. Footer
```

#### **not-found.blade.php** - Certificato Non Trovato

-   ✅ Design coerente con main template
-   ✅ Messaggi utente-friendly
-   ✅ Navigation hints

#### **error.blade.php** - Gestione Errori

-   ✅ Error handling robusto
-   ✅ Fallback graceful

### 6.2 Template PDF

**Directory**: `resources/views/coa/pdf/`

#### **certificate.blade.php** - Template PDF

-   ✅ Layout print-optimized
-   ✅ QR Code embedded
-   ✅ Watermark digitale
-   ✅ Professional typography
-   ✅ Include tutti i traits

### 6.3 Componenti Blade

**Directory**: `resources/views/components/coa/`

#### **coa-section.blade.php**

-   ✅ Sezione CoA nelle pagine EGI
-   ✅ Status display
-   ✅ Quick actions

#### **sidebar-section.blade.php**

-   ✅ Sidebar info CoA
-   ✅ Link verifiche rapide

#### **annex-modal.blade.php**

-   ✅ Modal per visualizzazione annessi
-   ✅ File download
-   ✅ Metadata display

---

## 7. SISTEMI DI SICUREZZA

### 7.1 Sicurezza Cryptografica

-   ✅ Hash SHA-256 per integrità dati
-   ✅ Verifica hash esistenti
-   ✅ Multi-algorithm hashing ready
-   ✅ Immutable snapshots

### 7.2 Sicurezza Access Control

-   ✅ Rate limiting per verification
-   ✅ Authorization middleware
-   ✅ Ownership validation
-   ✅ Public endpoints sicuri

### 7.3 Audit e Logging

-   ✅ UltraLogManager integration
-   ✅ Ultra Error Manager handling
-   ✅ Event sourcing completo
-   ✅ GDPR audit trail
-   ✅ IP tracking e User-Agent

### 7.4 Validation

-   ✅ Input validation robusto
-   ✅ File upload security
-   ✅ XSS protection
-   ✅ SQL injection prevention

---

## 8. ROUTING E API

### 8.1 Route Files

**File**: `routes/coa.php`

**Struttura Route Groups:**

```php
// PUBLIC VERIFICATION (No Auth)
Route::prefix('coa/verify')->group([
    'certificate/{serial}' => VerifyController@verify,
    'certificate/{serial}/view' => VerifyController@viewCertificateBySerial,
    'hash/{hash}' => VerifyController@verifyByHash,
    'qr/{hash}' => VerifyController@verifyQr,
    'certificate/{serial}/annexes' => VerifyController@verifyAnnexes
]);

// AUTHENTICATED MANAGEMENT
Route::middleware('auth')->prefix('coa')->group([
    '/' => CoaController@index,
    'issue/{egi}' => CoaController@issue,
    '{coa}' => CoaController@show,
    '{coa}/revoke' => CoaController@revoke,
    '{coa}/pdf' => CoaController@downloadPdf,
    '{coa}/annexes/*' => AnnexController@*
]);

// ADDENDUM MANAGEMENT
Route::prefix('coa/addendum')->group([...]);
```

### 8.2 API Endpoints

-   ✅ RESTful design principles
-   ✅ JSON/HTML content negotiation
-   ✅ Consistent error responses
-   ✅ Rate limiting integrato

---

## 9. TRAIT E HELPER

### 9.1 EgiTraitsExtraction Trait

**File**: `app/Traits/EgiTraitsExtraction.php`

**Funzionalità Implementate:**

-   ✅ Estrazione author da tabella `egi_traits`
-   ✅ Estrazione metadati (year, technique, support, dimensions, edition)
-   ✅ Relazioni con `TraitType` model
-   ✅ Fallback graceful quando traits mancanti
-   ✅ Array completo tutti i traits

**Metodi Implementati:**

```php
- extractAuthorFromTraits(Egi $egi): string
- extractYearFromTraits(Egi $egi): ?string
- extractTechniqueFromTraits(Egi $egi): ?string
- extractSupportFromTraits(Egi $egi): ?string
- extractDimensionsFromTraits(Egi $egi): ?string
- extractEditionFromTraits(Egi $egi): ?string
- extractRelationshipFromTraits(Egi $egi): ?string
- hasAuthorizationInTraits(Egi $egi): bool
- extractAllArtworkMetadata(Egi $egi): array
- extractTraitValue(Egi $egi, array $traitNames): ?string
```

**Integrato In:**

-   ✅ VerifyController (entrambi i metodi)
-   ✅ CoaIssueService
-   ✅ Template certificate.blade.php

---

## 10. INTEGRAZIONE CON SISTEMA EGI

### 10.1 Relazioni Database

-   ✅ Foreign key `coas.egi_id -> egis.id`
-   ✅ Relazione `Egi::hasOne(Coa)` / `Coa::belongsTo(Egi)`
-   ✅ Eager loading `egi.traits.traitType`
-   ✅ Soft delete cascade handling

### 10.2 Integrazione Traits System

-   ✅ Lettura da tabella `egi_traits`
-   ✅ Relazione con `trait_types`
-   ✅ Snapshot versioning con `egi_traits_versions`
-   ✅ Immutabilità snapshot al momento emissione

### 10.3 UI Integration

-   ✅ Sezioni CoA in pages EGI
-   ✅ Link bidirezionali EGI ↔ CoA
-   ✅ Dossier integration per thumbnail

---

## 11. FUNZIONALITÀ IMPLEMENTATE

### 11.1 Core Features ✅ COMPLETO

-   [x] **Emissione Certificati**: Sistema completo con validazioni
-   [x] **Verifica Pubblica**: Multi-format (serial, hash, QR)
-   [x] **Role Distinction**: Author/Creator/Issuer/Activator
-   [x] **Traits Integration**: Estrazione completa da egi_traits
-   [x] **Event Sourcing**: Tracciabilità completa azioni
-   [x] **Snapshot System**: Immutabilità garantita
-   [x] **Cryptographic Integrity**: SHA-256 hashing
-   [x] **PDF Generation**: Professional templates
-   [x] **Rate Limiting**: Protezione abuse

### 11.2 Pro Features ✅ COMPLETO

-   [x] **Annessi A-E**: Sistema completo 5 annessi
-   [x] **File Management**: Upload e gestione file
-   [x] **Addendum System**: Policy versionate
-   [x] **Bundle Certificates**: Certificati collezione
-   [x] **Advanced Signatures**: QES ready + Wallet

### 11.3 Enterprise Features ✅ COMPLETO

-   [x] **GDPR Compliance**: Audit trail completo
-   [x] **Error Management**: UEM integration
-   [x] **Logging**: Comprehensive logging
-   [x] **Security**: Access control completo
-   [x] **API Design**: RESTful + content negotiation

### 11.4 UI/UX Features ✅ COMPLETO

-   [x] **Responsive Design**: Mobile-first
-   [x] **Professional Templates**: Enterprise-grade
-   [x] **Interactive Elements**: QR, copy buttons, modals
-   [x] **Accessibility**: WCAG compliance ready
-   [x] **Performance**: Optimized queries

---

## 12. FUNZIONALITÀ MANCANTI

### 12.1 Roadmap Features - Riferimento QES Checklist

**NOTA**: Per il dettaglio completo delle funzionalità future, consultare:  
`docs/ai/checklists/qes_implementation_checklist.md`

Le prossime implementazioni includono:

-   **Admin Panel & System Management** (Fase 9)
-   **Analytics & Reporting** (Fase 10)
-   **Notification System** (Fase 11)
-   **API & Integration Avanzate** (Fase 12)
-   **Blockchain Integration** (Roadmap estesa)
-   **Mobile e PWA** (Roadmap estesa)
-   **AI/ML Features** (Roadmap estesa)

---

## 13. ROADMAP IMPLEMENTAZIONE

### 13.1 FASE 1 - Stabilizzazione (COMPLETATA) ✅

-   [x] Correzione bug traits extraction
-   [x] Template certificato completo
-   [x] Testing sistema completo
-   [x] Documentation completa

### 13.2 Roadmap Implementazione - Riferimento QES Checklist

**IMPORTANTE**: La roadmap dettagliata e aggiornata si trova in:  
`docs/ai/checklists/qes_implementation_checklist.md`

**Prossime Priority:**

-   **Fasi 1-8**: QES Core Implementation (in corso)
-   **Fase 9**: Admin Panel & System Management
-   **Fase 10**: Analytics & Reporting
-   **Fase 11**: Notification System
-   **Fase 12**: API & Integration Avanzate

**Target Timeline**: Q4 2025 - Q1 2026  
**Status**: Riferimento unico via QES checklist per evitare duplicazioni

---

## 14. CONCLUSIONI TECNICHE

### 14.1 Stato Attuale Sistema

Il sistema CoA di FlorenceEGI è **completamente funzionale e production-ready** per le funzionalità core. L'architettura enterprise implementata garantisce:

-   **Scalabilità**: Design multi-layer scalabile
-   **Sicurezza**: Cryptographic integrity + audit completo
-   **Usabilità**: UX enterprise-grade responsive
-   **Manutenibilità**: Codice strutturato e documentato
-   **Extensibilità**: Architettura modulare per future features

### 14.2 Punti di Forza

1. **Architettura Solida**: Event sourcing + immutable snapshots
2. **Integration EGI**: Perfetta integrazione con sistema traits esistente
3. **Security First**: Approccio security-by-design
4. **GDPR Compliant**: Audit trail e privacy nativa
5. **Developer Experience**: Codice pulito e ben documentato

### 14.3 Aree di Miglioramento

1. **Analytics**: Dashboard e reporting mancanti
2. **Blockchain**: Integration reale vs placeholder
3. **Mobile**: Esperienza mobile dedicata
4. **AI/ML**: Features intelligenti per anti-frode

### 14.4 Raccomandazioni Immediate

1. **Deploy in Production**: Sistema pronto per go-live
2. **User Testing**: Test con utenti reali
3. **Performance Monitoring**: Setup monitoring produzione
4. **Documentation User**: Guide utente finali

---

**Fine Documento Architetturale**  
**Status**: Sistema CoA FlorenceEGI - **PRODUCTION READY** ✅  
**Next Steps**: Analytics Dashboard + Admin Panel (Fase 2)
