# FlorenceEGI GDPR & Legal Document System Documentation

**Data Documento**: 2025-01-26  
**Versione**: 1.0.0  
**Author**: Padmin D. Curtis (AI Partner OS3.0-Compliant)  
**Stato**: Documentazione di Sistema

---

## 📋 Indice

1. [Panoramica del Sistema](#1-panoramica-del-sistema)
2. [Architettura Dual-Storage](#2-architettura-dual-storage)
3. [Sistema di Versionamento File-Based (Terms of Service)](#3-sistema-di-versionamento-file-based-terms-of-service)
4. [Sistema di Versionamento Database (Privacy/Cookie Policy)](#4-sistema-di-versionamento-database-privacycookie-policy)
5. [Services e Controller](#5-services-e-controller)
6. [Tipi di Documenti Legali](#6-tipi-di-documenti-legali)
7. [Workflow di Aggiornamento](#7-workflow-di-aggiornamento)
8. [Reference API](#8-reference-api)

---

## 1. Panoramica del Sistema

FlorenceEGI implementa un sistema GDPR-compliant con **doppia architettura di storage**:

| Componente           | Storage          | Servizio              | Controller            |
| -------------------- | ---------------- | --------------------- | --------------------- |
| **Terms of Service** | File-based (PHP) | `LegalContentService` | `GdprLegalController` |
| **Privacy Policy**   | Database (MySQL) | `GdprService`         | `GdprController`      |
| **Cookie Policy**    | Database (MySQL) | `GdprService`         | `GdprController`      |

### Principi Architetturali (OS3.0)

-   **REGOLA ZERO**: Ogni componente ha documentazione di intenzionalità
-   **Separazione delle responsabilità**: Controller → Service → Model/Filesystem
-   **Audit Trail completo**: Ogni azione è tracciata via `AuditLogService`
-   **Multi-lingua**: Supporto IT, EN, ES, PT, FR, DE
-   **Multi-tenant**: Termini differenziati per tipo utente

---

## 2. Architettura Dual-Storage

### 2.1 Perché Due Sistemi?

**File-Based (Terms of Service)**:

-   Contenuto strutturato complesso (articoli, sezioni, clausole)
-   Versionamento semantico con Git-like history
-   Facile review da parte del team legale
-   Cache-friendly con symlink per `current`

**Database (Privacy/Cookie Policy)**:

-   Contenuto più semplice (markdown/HTML)
-   Integrazione con sistema consensi utente
-   Query per statistiche e compliance reporting
-   Integrazione con `PrivacyPolicyAcceptance`

### 2.2 Diagramma Architetturale

```
┌─────────────────────────────────────────────────────────────────┐
│                        GDPR LEGAL SYSTEM                         │
├─────────────────────────────┬───────────────────────────────────┤
│     FILE-BASED STORAGE      │       DATABASE STORAGE            │
│                             │                                   │
│  resources/legal/terms/     │   privacy_policies table          │
│  └── versions/              │   ├── id                          │
│      ├── current/ → symlink │   ├── document_type               │
│      │   └── it/            │   ├── version                     │
│      │       ├── creator.php│   ├── content                     │
│      │       ├── collector.php│ ├── language                    │
│      │       └── ...        │   ├── status                      │
│      └── 1.0.0/             │   ├── effective_date              │
│          ├── metadata.php   │   └── ...                         │
│          └── it/            │                                   │
├─────────────────────────────┼───────────────────────────────────┤
│   LegalContentService       │   GdprService / PrivacyPolicy     │
│   GdprLegalController       │   GdprController                  │
└─────────────────────────────┴───────────────────────────────────┘
```

---

## 3. Sistema di Versionamento File-Based (Terms of Service)

### 3.1 Struttura Directory

```
resources/legal/terms/versions/
├── current/                 # Symlink alla versione attiva
│   └── it/
│       ├── creator.php      # Termini per Creatori
│       ├── collector.php    # Termini per Collezionisti
│       ├── patron.php       # Termini per Patron
│       ├── epp.php          # Termini per EPP
│       ├── company.php      # Termini per Aziende
│       └── trader_pro.php   # Termini per Trader Pro
├── 1.0.0/
│   ├── metadata.php         # Metadati versione
│   └── it/
│       └── [user_type].php
└── backups/
    └── [date]/              # Backup automatici
```

### 3.2 Formato File PHP Termini

```php
<?php
// resources/legal/terms/versions/current/it/creator.php

return [
    'metadata' => [
        'title' => 'Termini e Condizioni per Utenti Creatori',
        'version' => '1.4.0',
        'effective_date' => '2025-06-30',
        'document_type' => 'Accordo Legale per Utenti Professionali',
        'target_audience' => 'Utenti qualificati come "Creatore"',
        'summary_of_changes' => 'Descrizione modifiche',
    ],

    'preambolo' => [
        'title' => 'Preambolo',
        'content' => '...'
    ],

    'articles' => [
        [
            'number' => 1,
            'category' => 'pact',      // pact|platform|rules|liability|general
            'title' => 'Definizioni',
            'subsections' => [
                [
                    'number' => '1.1',
                    'title' => 'Definizioni Chiave',
                    'content' => '...'
                ],
                // ...
            ]
        ],
        // ...
    ]
];
```

### 3.3 Formato metadata.php

```php
<?php
// resources/legal/terms/versions/1.0.0/metadata.php

return [
    'version' => '1.0.0',
    'release_date' => '2025-06-22',
    'effective_date' => '2025-06-30',
    'created_by' => 'legal@florenceegi.com',
    'approved_by' => 'fabio.cherici@florenceegi.com',
    'summary_of_changes' => 'Initial release',

    'available_user_types' => [
        'creator' => ['status' => 'ready', 'priority' => 'high'],
        'collector' => ['status' => 'in_development', 'priority' => 'high'],
        // ...
    ],

    'available_locales' => [
        'it' => ['status' => 'primary', 'completion' => '50%'],
        'en' => ['status' => 'pending_translation', 'completion' => '0%'],
        // ...
    ]
];
```

### 3.4 LegalContentService - Metodi Principali

```php
// Lettura
$service->getCurrentTermsContent($userType, $locale);  // array|null
$service->getCurrentVersionString();                    // string "1.0.0"
$service->getVersionHistory();                          // array di versioni
$service->getMetadataForVersion($version);              // array metadata
$service->getUserConsentStatus($userType, $locale);     // array stato consenso

// Scrittura
$service->createNewVersion($userType, $locale, $content, $summary, $effectiveDate, $autoPublish);
$service->isContentSecure($content);                    // bool - security check
$service->clearCache($userType, $locale);               // void
```

---

## 4. Sistema di Versionamento Database (Privacy/Cookie Policy)

### 4.1 Model PrivacyPolicy

**Tabella**: `privacy_policies`

| Campo                 | Tipo      | Descrizione                |
| --------------------- | --------- | -------------------------- |
| `id`                  | bigint    | Primary key                |
| `version`             | string    | Versione semantica (1.0.0) |
| `title`               | string    | Titolo documento           |
| `content`             | text      | Contenuto (Markdown)       |
| `summary`             | json      | Riassunto per lingua       |
| `document_type`       | enum      | Tipo documento             |
| `language`            | string    | Codice lingua (it, en)     |
| `status`              | enum      | Stato ciclo vita           |
| `effective_date`      | datetime  | Data efficacia             |
| `expiry_date`         | datetime  | Data scadenza              |
| `requires_consent`    | boolean   | Richiede nuovo consenso    |
| `created_by`          | foreignId | Autore                     |
| `approved_by`         | foreignId | Approvatore                |
| `legal_review_status` | enum      | Stato revisione legale     |

### 4.2 Costanti del Model

```php
// Tipi documento supportati
PrivacyPolicy::DOCUMENT_TYPES = [
    'privacy_policy',
    'terms_of_service',
    'cookie_policy',
    'data_processing_agreement',
    'consent_form',
    'gdpr_notice',
    'retention_policy',
    'security_policy',
];

// Stati ciclo vita
PrivacyPolicy::STATUS_VALUES = [
    'draft',
    'under_review',
    'approved',
    'active',
    'superseded',
    'archived',
    'rejected',
];

// Stati revisione legale
PrivacyPolicy::LEGAL_REVIEW_STATUS = [
    'pending',
    'in_progress',
    'approved',
    'requires_changes',
    'rejected',
];
```

### 4.3 Scopes Utili

```php
// Policy attive per tipo e lingua
PrivacyPolicy::active()
    ->documentType('privacy_policy')
    ->language('it')
    ->first();

// Policy che richiedono consenso
PrivacyPolicy::requiresConsent()->get();

// Policy approvate legalmente
PrivacyPolicy::legallyApproved()->get();
```

### 4.4 Metodi del Model

```php
$policy->isActive();           // bool
$policy->isEffective();        // bool
$policy->hasExpired();         // bool
$policy->isLegallyApproved();  // bool
$policy->activate();           // bool - attiva e supersede versioni precedenti
$policy->archive();            // bool
$policy->generateVersion();    // string - auto-increment
$policy->getExcerpt(200);      // string
```

---

## 5. Services e Controller

### 5.1 GdprLegalController (Terms of Service)

**Route File**: `routes/gdpr_legal.php`

| Route                                          | Metodo         | Descrizione                 |
| ---------------------------------------------- | -------------- | --------------------------- |
| `GET /legal/terms/{userType}/{locale}`         | `showTerms`    | Visualizza termini pubblici |
| `GET /legal/terms/edit/{userType}/{locale}`    | `editTerms`    | Editor admin                |
| `POST /legal/terms/save/{userType}/{locale}`   | `saveTerms`    | Salva nuova versione        |
| `POST /legal/terms/accept/{userType}`          | `acceptTerms`  | Accettazione utente         |
| `GET /legal/terms/history/{userType}/{locale}` | `termsHistory` | Storico versioni            |

### 5.2 GdprController (Privacy/Cookie Policy)

**Route File**: `routes/gdpr.php`

| Route                                   | Metodo                   | Descrizione             |
| --------------------------------------- | ------------------------ | ----------------------- |
| `GET /gdpr/privacy-policy`              | `privacyPolicy`          | Privacy Policy pubblica |
| `GET /gdpr/privacy-policy/version/{id}` | `privacyPolicyVersion`   | Versione specifica      |
| `GET /gdpr/privacy-policy/changelog`    | `privacyPolicyChangelog` | Storico modifiche       |
| `GET /gdpr/privacy-policy/download`     | `privacyPolicyDownload`  | Download PDF            |
| `GET /gdpr/cookie-policy/download`      | `cookiePolicyDownload`   | Download PDF            |
| `GET /gdpr/terms-of-service`            | `termsOfService`         | Termini (redirect)      |

---

## 6. Tipi di Documenti Legali

### 6.1 Privacy Policy

-   **Storage**: Database (`privacy_policies`)
-   **Seeder**: `FlorenceEgiPrivacyPolicySeeder`
-   **Lingue**: IT, EN
-   **Richiede Consenso**: Sì

### 6.2 Terms of Service

-   **Storage**: File-based
-   **Path**: `resources/legal/terms/versions/`
-   **User Types**: creator, collector, patron, epp, company, trader_pro
-   **Lingue**: IT (altre in sviluppo)
-   **Richiede Consenso**: Sì

### 6.3 Cookie Policy

-   **Storage**: Database (`privacy_policies` con `document_type = 'cookie_policy'`)
-   **Seeder**: `FlorenceEgiPrivacyPolicySeeder`
-   **Lingue**: IT, EN
-   **Richiede Consenso**: No (solo informativo)

---

## 7. Workflow di Aggiornamento

### 7.1 Aggiornare Terms of Service (File-Based)

**Opzione A - Via Dashboard Admin**:

1. Accedere a `/legal/terms/edit/{userType}/{locale}`
2. Modificare contenuto nell'editor
3. Inserire "Summary of Changes" e "Effective Date"
4. Opzionale: flag "Auto-publish"
5. Submit → nuova versione creata

**Opzione B - Via CLI/Manuale**:

1. Creare nuova directory: `resources/legal/terms/versions/X.Y.Z/`
2. Copiare struttura da versione precedente
3. Modificare contenuti e metadata.php
4. Aggiornare symlink `current` → nuova versione
5. Eseguire `php artisan cache:clear`

### 7.2 Aggiornare Privacy/Cookie Policy (Database)

**Opzione A - Via Seeder (Raccomandato per deploy)**:

1. Modificare `FlorenceEgiPrivacyPolicySeeder`
2. Incrementare versione nei metodi `create*Policy()`
3. Eseguire `php artisan db:seed --class=FlorenceEgiPrivacyPolicySeeder`

**Opzione B - Via Tinker/Code**:

```php
use App\Models\PrivacyPolicy;

$newPolicy = PrivacyPolicy::create([
    'title' => 'Informativa Privacy Aggiornata',
    'document_type' => 'privacy_policy',
    'content' => '...',
    'language' => 'it',
    'status' => 'approved',
    'effective_date' => now()->addDays(30),
    'requires_consent' => true,
    'change_description' => 'Aggiornamento per nuove funzionalità AI',
]);

$newPolicy->activate(); // Supersede versioni precedenti
```

### 7.3 Checklist Aggiornamento GDPR

-   [ ] Aggiornato contenuto Privacy Policy IT
-   [ ] Aggiornato contenuto Privacy Policy EN
-   [ ] Aggiornato contenuto Cookie Policy IT
-   [ ] Aggiornato contenuto Cookie Policy EN
-   [ ] Aggiornati Terms of Service per ogni user type
-   [ ] Verificate date di efficacia (minimo 30 giorni)
-   [ ] Configurato flag `requires_consent` appropriatamente
-   [ ] Testato rendering su tutti i browser
-   [ ] Testato download PDF
-   [ ] Verificato audit log
-   [ ] Notifica utenti esistenti (se necessario)

---

## 8. Reference API

### 8.1 ConsentService

```php
// Verificare consenso utente
$consentService->hasConsent($user, 'terms-of-service');
$consentService->hasAcceptedCurrentTerms($user);

// Registrare consenso
$consentService->recordTermsConsent($user, $version, [
    'locale' => 'it',
    'source' => 'legal_terms_page_acceptance',
    'user_type' => 'creator'
]);

// Ottenere contenuto termini
$consentService->getTermsContent($user, $locale);
```

### 8.2 AuditLogService

```php
// Registrare azione utente
$auditService->logUserAction($user, 'legal_terms_viewed', [
    'user_type' => $userType,
    'locale' => $locale,
    'version_viewed' => $version
], GdprActivityCategory::GDPR_ACTIONS);
```

### 8.3 GdprService

```php
// Privacy Policy corrente
$gdprService->getCurrentPrivacyPolicy();
$gdprService->getPrivacyPolicyVersions();
```

---

## Appendice A: Configurazioni Correlate

### config/app.php

```php
'fegi_user_type' => ['creator', 'collector', 'patron', 'epp', 'company', 'trader_pro'],
'fegi_countries' => ['it', 'en', 'es', 'pt', 'fr', 'de'],
```

### config/jetstream.php

```php
Features::termsAndPrivacyPolicy(), // Abilita feature Jetstream
```

---

## Appendice B: Views Correlate

```
resources/views/gdpr/
├── legal/
│   ├── editor.blade.php      # Editor admin termini
│   ├── terms.blade.php       # Visualizzazione pubblica
│   └── history.blade.php     # Storico versioni
├── privacy-policy.blade.php  # Privacy policy pubblica
├── cookie-policy.blade.php   # Cookie policy pubblica
└── dashboard/
    └── ...                   # Dashboard GDPR utente
```

---

**Fine Documentazione**

Per domande o chiarimenti: `docs@florenceegi.com`
