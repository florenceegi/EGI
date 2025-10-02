# FlorenceEGI PA/Enterprise - Architettura Tecnica e Business Logic

## Mapping Sistema Esistente → Contesto PA/Enterprise

_Versione 1.0 - 2 Ottobre 2025_  
_Complementare a: PA_ENTERPRISE_BRAND_GUIDELINES.md_

---

## 🎯 **PREMESSA: Riuso Intelligente**

**PRINCIPIO CORE:**

> "Non reinventiamo la ruota. L'architettura Collection→EGI è già enterprise-grade. Adattiamola al contesto PA/Enterprise con estensioni puntuali, non riscritture."

**Fabio ha ragione:** Abbiamo un patrimonio immenso di codice, architettura, e infrastruttura. Il sistema Collection→EGI è:

-   ✅ Già testato in produzione
-   ✅ GDPR compliant by design
-   ✅ ULTRA integrated (ULM, UEM, AuditLog)
-   ✅ Wallet system (crypto + IBAN fiat)
-   ✅ Collaborators management
-   ✅ CoA system già implementato

**Obiettivo:** Mappare use cases PA/Enterprise su architettura esistente + estensioni minime.

---

# PARTE I - MAPPATURA ARCHITETTURALE

## 📦 **1. COLLECTION → COLLECTION PA/ENTERPRISE**

### 1.1 Concept Mapping

**Collection Artwork (esistente):**

```
Creator → crea Collection → aggiunge EGI (opere d'arte)
→ Collaboratori → Wallet (crypto/IBAN)
→ GDPR consent → Audit trail
```

**Collection PA/Enterprise (nuovo context):**

```
PA Entity → crea Collection → aggiunge EGI (patrimonio/prodotti)
→ Inspector (collaboratore speciale) → Wallet IBAN (pagamenti istituzionali)
→ GDPR consent → Audit trail (mandatory per PA)
```

### 1.2 Schema Database - NESSUNA MODIFICA RICHIESTA

**Tabella `collections` - già compatibile:**

```sql
id BIGINT PK
user_id BIGINT FK (owner - sarà PA Entity o Company)
name VARCHAR(255)
description TEXT
status ENUM(...)
created_at, updated_at
```

**✅ FUNZIONA SUBITO PER PA/ENTERPRISE**

### 1.3 Estensioni Necessarie

**CAMPO `type` - ESISTE GIÀ ma con valori diversi:**

```sql
-- Migration esistente: 2024_11_14_090414_create_collections_table.php
-- Campo: type VARCHAR(25) NULL

-- CURRENT VALUES (da verificare in produzione):
-- 'standard', 'single_egi', etc.

-- PROPOSTA MIGRATION: Espandere valori senza breaking changes
ALTER TABLE collections
MODIFY COLUMN type VARCHAR(50);

-- Poi aggiungere check constraint o validazione Laravel per:
-- Valori esistenti: 'standard', 'single_egi', ...
-- Valori PA/Enterprise: 'pa_heritage', 'pa_documents', 'company_products', 'company_catalog'
```

**CAMPO `metadata` - NON ESISTE nella tabella collections**

**OPZIONI:**

**Opzione A (Raccomandato): Usare campo esistente per metadata generici**

```sql
-- Collections ha già campo 'description' TEXT
-- Possiamo aggiungere nuovo campo JSON senza breaking
ALTER TABLE collections
ADD COLUMN metadata JSON NULL AFTER url_collection_site
COMMENT 'Metadati flessibili: PA institutional data, company info, etc';
```

**Opzione B: Campo specifico PA-only**

```sql
ALTER TABLE collections
ADD COLUMN institutional_metadata JSON NULL AFTER metadata
COMMENT 'Dati specifici PA: codice ente, P.IVA, referente, etc';
```

**Raccomandazione: Opzione A (metadata generico) per flessibilità futura**

**Esempio collections.metadata per PA:**

```json
{
    "context": "pa_entity",
    "entity_type": "comune",
    "entity_name": "Comune di Firenze",
    "entity_code": "C_D612",
    "vat_number": "IT01234567890",
    "referent": {
        "name": "Dott. Mario Rossi",
        "email": "m.rossi@comune.fi.it",
        "phone": "+39 055 1234567",
        "role": "Assessore Cultura"
    },
    "department": "Assessorato Cultura",
    "catalogation_system": "SIGECweb",
    "approval_required": true
}
```

**Esempio collections.metadata per Company:**

```json
{
    "context": "company",
    "company_type": "artigianato",
    "company_name": "Cantina Vini Toscani SRL",
    "vat_number": "IT09876543210",
    "sector": "food_wine",
    "certifications": ["DOCG", "Biologico EU"],
    "referent": {
        "name": "Laura Bianchi",
        "email": "l.bianchi@cantina.it",
        "phone": "+39 055 7654321",
        "role": "Responsabile Qualità"
    },
    "qr_code_enabled": true,
    "public_verification": true
}
```

---

## 🎨 **2. EGI → EGI PA/ENTERPRISE**

### 2.1 Concept Mapping

**EGI Artwork (esistente):**

```
Opera d'arte → Traits (tecnica, materiali, dimensioni)
→ Media (immagini) → CoA → Blockchain (Algorand)
```

**EGI PA Heritage:**

```
Bene culturale → Traits (epoca, materiali, stato conservazione)
→ Media (foto catalogazione) → CoA PA → Blockchain (audit trail)
```

**EGI Company Product:**

```
Prodotto certificato → Traits (origine, materiali, lotto)
→ Media (foto prodotto) → CoA Anti-contraffazione → QR Code
```

### 2.2 Schema Database - COMPATIBILE

**Tabella `egis` - già universale:**

```sql
id BIGINT PK
collection_id BIGINT FK
title VARCHAR(255)
description TEXT
media (relazione hasMany)
traits (relazione hasOne/hasMany)
status ENUM(...)
created_at, updated_at
```

**✅ FUNZIONA SUBITO - L'EGI È GIÀ UNIVERSALE**

### 2.3 Estensioni Traits per Contesti Specifici

**PA Heritage Traits (esempio: statua comunale):**

```json
{
    "category": "sculpture",
    "period": "Renaissance",
    "artist": "Unknown",
    "dating": "1480-1500",
    "materials": ["marble", "bronze"],
    "dimensions": { "height": 180, "width": 60, "depth": 50, "unit": "cm" },
    "conservation_state": "good",
    "location_historic": "Piazza della Signoria, Firenze",
    "catalogation_code": "INV-2024-0123",
    "unesco_protected": false
}
```

**Company Product Traits (esempio: bottiglia vino):**

```json
{
    "product_type": "wine",
    "denomination": "Chianti Classico DOCG",
    "vintage": "2021",
    "producer": "Azienda Agricola XYZ",
    "origin": "Greve in Chianti, Toscana",
    "batch_number": "BATCH-2021-A-042",
    "bottle_number": "1234/5000",
    "certifications": ["DOCG", "Organic EU", "Vegan OK"],
    "alcohol_content": "13.5%",
    "bottle_size": "750ml"
}
```

**✅ NESSUNA MODIFICA DB - Traits già JSON flessibile**

---

## 👥 **3. COLLABORATORI → INSPECTOR + STAKEHOLDERS**

### 3.1 Concept Mapping

**Collaboratore Artwork (esistente):**

```
Creator aggiunge collaboratori alla collection
→ Ruoli: co-creator, curator, gallery
→ Revenue share configurabile
```

**Collaboratore PA/Enterprise:**

```
PA Entity aggiunge Inspector alla collection
→ Ruolo speciale: verifica + firma CoA
→ Compenso fisso o per CoA emesso
```

### 3.2 Schema Database - REALE (Verificato)

**Tabella `collection_user` (pivot table esistente):**

```sql
-- Migration: 2024_12_27_102951_create_collection_user_table.php
id BIGINT PK
collection_id BIGINT FK (cascade delete)
user_id BIGINT FK (cascade delete)
role VARCHAR(255) NULL                    -- Ruolo Spatie (es. 'creator', 'editor', 'inspector')
is_owner BOOLEAN DEFAULT false
status VARCHAR(255) DEFAULT 'pending'
joined_at TIMESTAMP NULL
removed_at TIMESTAMP NULL
metadata JSON NULL                        -- Dati dinamici aggiuntivi
created_at, updated_at
UNIQUE(collection_id, user_id)
```

**✅ FUNZIONA SUBITO - Nessuna modifica strutturale necessaria**

### 3.3 Inspector come Ruolo Spatie

**Sistema esistente: Spatie Laravel Permission**

**File:** `database/seeders/RolesAndPermissionsSeeder.php`

**Inspector NON è ancora definito come ruolo - DA AGGIUNGERE:**

```php
'inspector' => [
    // Base permissions
    'access_dashboard',
    'view_dashboard',
    'view_collection',        // Vedere collections assegnate
    'view_EGI',              // Vedere EGI da ispezionare
    'view_statistics',
    'view_documentation',

    // Inspector specific (DA CREARE PERMESSI)
    'sign_coa',                           // ✅ Già esistente nel seeder
    'view_assigned_collections',          // NUOVO - vedere solo collections assegnate
    'upload_inspection_report',           // NUOVO - caricare report perizie
    'revoke_coa_if_issues',              // NUOVO - revocare CoA se problemi gravi
    'access_inspector_dashboard',         // NUOVO - area inspector

    // Profile & GDPR
    'manage_profile',
    'manage_account',
    'view_profile',
    'manage_consents',
    'manage_privacy',
    'export_personal_data',
    'view_activity_log',
    'view_privacy_policy',
    'edit_personal_data',
    'edit_own_profile_data',
    'edit_own_personal_data',
    'manage_own_documents',
    'upload_identity_documents',         // Certificazioni professionali
    'verify_document_status',
    'can_request_export',
    'can_request_deletion',
],
```

**Metadata JSON per Inspector in collection_user.metadata:**

```json
{
    "assignment_type": "coa_inspection",
    "assigned_at": "2025-10-15T10:30:00Z",
    "assignment_reason": "Request CoA #COA-EGI-2025-0123",
    "compensation_type": "per_coa",
    "compensation_amount": 150.0,
    "specialization": ["fine_art", "cultural_heritage"],
    "certifications": ["ICOM", "Restauro Certificato MiC"]
}
```

---

## 💰 **4. WALLET → IBAN ISTITUZIONALE**

### 4.1 Concept Mapping

**Wallet Artwork (esistente):**

```
Collection ha N wallet
→ Tipo: crypto (Algorand) + IBAN (fiat)
→ Revenue distribution automatica
```

**Wallet PA/Enterprise:**

```
Collection PA ha SOLO wallet IBAN
→ Nessuna crypto (compliance PA)
→ Pagamenti istituzionali (bonifici)
```

### 4.2 Schema Database - COMPATIBILE

**Tabella `wallets` (assumo esista):**

```sql
id BIGINT PK
collection_id BIGINT FK
type ENUM('algorand', 'ethereum', 'iban')
address VARCHAR(255)
is_primary BOOLEAN
status ENUM('active', 'inactive')
metadata JSON
created_at, updated_at
```

**✅ IBAN GIÀ SUPPORTATO - type='iban'**

### 4.3 Validazione PA-Specific

**Business Logic per PA Entity:**

```php
// app/Services/Wallet/WalletService.php

public function createWalletForPA(Collection $collection, array $ibanData): Wallet
{
    // Validazione: PA può avere SOLO IBAN
    // Verifico platform_role dell'owner usando Spatie
    if (!$collection->owner->hasRole('pa_entity')) {
        throw new \Exception('Only PA entities can create institutional wallets');
    }

    // Validazione IBAN europeo
    if (!$this->validateIBAN($ibanData['iban'])) {
        throw new \Exception('Invalid IBAN format');
    }

    return Wallet::create([
        'collection_id' => $collection->id,
        'type' => 'iban',
        'address' => $ibanData['iban'],
        'is_primary' => true,
        'metadata' => [
            'bank_name' => $ibanData['bank_name'],
            'account_holder' => $ibanData['account_holder'],
            'swift_bic' => $ibanData['swift_bic'] ?? null,
            'institution_type' => 'public_administration'
        ]
    ]);
}
```

---

## 📜 **5. CoA → CoA PA/ENTERPRISE**

### 5.1 Sistema Esistente - GIÀ COMPLETO

**Architettura CoA attuale:**

```
Tabella: coa
├─ coa_snapshot (immutabile)
├─ coa_files (PDFs, firme)
├─ coa_signatures (QES, wallet, autograph)
├─ coa_events (audit trail)
└─ coa_annexes (A_PROVENANCE, B_CONDITION, etc)
```

**✅ PERFETTO PER PA/ENTERPRISE - Nessuna modifica strutturale**

### 5.2 Workflow PA-Specific

**Flusso PA Heritage:**

```
1. PA Entity crea EGI patrimonio
2. Upload foto + traits catalogazione
3. Richiede CoA → status 'pending_inspection'
4. Inspector assegnato riceve notifica
5. Inspector verifica fisica + upload report
6. Inspector firma CoA → status 'valid'
7. CoA pubblicato + QR code generato
8. Audit trail completo in coa_events
```

**Flusso Company Product:**

```
1. Company crea EGI prodotto
2. Upload foto prodotto + traits origine
3. Richiede CoA + Inspector (opzionale)
4. Se Inspector: workflow simile PA
5. Se no Inspector: CoA auto-emesso (con disclaimer)
6. QR code su packaging per verifica
7. Cliente finale scansiona QR → verifica autenticità
```

### 5.3 Estensioni CoA per PA

**Campo `issuer_type` già presente - aggiungere valori:**

```sql
ALTER TABLE coa
MODIFY COLUMN issuer_type ENUM(
  'author',
  'archive',
  'platform',
  'pa_entity',        -- NUOVO
  'inspector',        -- NUOVO
  'company',          -- NUOVO
  'third_party_cert'  -- NUOVO (enti certificatori esterni)
);
```

**Campo `metadata` JSON - esempi PA:**

```json
{
    "inspection_required": true,
    "inspector_user_id": 456,
    "inspection_date": "2025-10-15",
    "inspection_report_url": "storage/inspections/INS-2025-0123.pdf",
    "catalogation_system": "SIGECweb",
    "catalogation_code": "09-00001234",
    "unesco_reference": null,
    "ministry_approval": false
}
```

---

## 🔒 **6. GDPR/ULTRA → PA COMPLIANCE**

### 6.1 Sistema Esistente - GIÀ COMPLIANT

**GDPR by Design (esistente):**

```
✅ ConsentService → gestione consensi
✅ AuditLogService → audit trail GDPR
✅ UltraLogManager (ULM) → logging
✅ ErrorManager (UEM) → error handling
```

**✅ PERFETTO - Sistema già PA-ready**

### 6.2 Consensi PA-Specific

**Nuovi consent types per PA/Enterprise:**

```php
// config/gdpr.php

'consent_types' => [
    // ... existing consents

    // PA/Enterprise specific
    'allow-institutional-data-processing' => [
        'required' => true,
        'category' => 'institutional',
        'description' => 'Trattamento dati istituzionali per certificazione patrimonio',
        'roles' => ['pa_entity', 'inspector']
    ],

    'allow-inspector-access' => [
        'required' => true,
        'category' => 'collaboration',
        'description' => 'Accesso ispettore a dati collection per verifica',
        'roles' => ['pa_entity', 'company']
    ],

    'allow-public-coa-verification' => [
        'required' => false,
        'category' => 'transparency',
        'description' => 'Rendere CoA verificabili pubblicamente via QR code',
        'roles' => ['pa_entity', 'company']
    ]
];
```

### 6.3 Audit Trail PA - Estensioni

**GdprActivityCategory - nuove categorie:**

```php
// app/Enums/Gdpr/GdprActivityCategory.php

enum GdprActivityCategory: string
{
    // ... existing categories

    // PA/Enterprise specific
    case INSTITUTIONAL_DATA_ACCESS = 'institutional_data_access';
    case INSPECTOR_ASSIGNMENT = 'inspector_assignment';
    case COA_INSPECTION_PERFORMED = 'coa_inspection_performed';
    case COA_SIGNATURE_PA = 'coa_signature_pa';
    case PUBLIC_VERIFICATION_ACCESSED = 'public_verification_accessed';
}
```

**Logging automatico:**

```php
// Esempio: Inspector accede a collection PA
$this->auditService->logActivity(
    $inspector,
    GdprActivityCategory::INSTITUTIONAL_DATA_ACCESS,
    'Inspector accessed PA collection for verification',
    [
        'collection_id' => $collection->id,
        'pa_entity_id' => $collection->owner->id,
        'access_reason' => 'CoA inspection request #123',
        'ip_address' => request()->ip()
    ]
);
```

---

## 🎭 **7. USER ROLES → PA/ENTERPRISE ROLES**

### 7.1 PlatformRole Enum - GIÀ PRESENTE

**File: `app/Enums/PlatformRole.php`**

```php
enum PlatformRole: string
{
    case EPP = 'epp';
    case NATAN = 'natan';
    case CREATOR = 'creator';
    case COLLECTOR = 'collector';
    case COMMISSIONER = 'commissioner';
    case COMPANY = 'company';           // ✅ già presente
    case TRADER_PRO = 'trader_pro';
    case VIP = 'vip';
    case WEAK = 'weak';
    case PA_ENTITY = 'pa_entity';       // ✅ già presente
    case INSPECTOR = 'inspector';       // ✅ già presente
}
```

**✅ NESSUNA MODIFICA NECESSARIA**

### 7.2 Permissions Mapping (Sistema Spatie)

**RUOLO: pa_entity** (DA AGGIUNGERE a RolesAndPermissionsSeeder.php)

**Permessi necessari:**

```php
'pa_entity' => [
    // Base dashboard
    'access_dashboard',
    'access_pa_dashboard',           // ✅ Già nel seeder
    'view_dashboard',

    // Collections PA
    'create_collection',
    'update_collection',
    'delete_collection',
    'manage_institutional_collections', // ✅ Già nel seeder
    'cultural_heritage_management',     // ✅ Già nel seeder

    // EGI Patrimonio
    'create_EGI',
    'update_EGI',
    'delete_EGI',
    'manage_EGI',
    'manage_cultural_assets',          // ✅ Già nel seeder

    // CoA
    'sign_coa',                        // ✅ Già nel seeder (può firmare propri CoA)
    'bulk_coa_operations',             // ✅ Già nel seeder
    'institutional_certification',     // ✅ Già nel seeder

    // Wallet IBAN
    'create_wallet',
    'update_wallet',
    'view_wallet',

    // Reporting & Compliance
    'pa_reporting_access',             // ✅ Già nel seeder
    'access_compliance_tools',         // ✅ Già nel seeder
    'institutional_audit_access',      // ✅ Già nel seeder

    // Profile & GDPR (standard)
    'manage_profile',
    'manage_institutional_profile',    // ✅ Già nel seeder
    'manage_account',
    'view_statistics',
    'edit_own_profile_data',
    'edit_own_organization_data',
    'manage_own_documents',
    'manage_consents',
    'manage_privacy',
    'export_personal_data',
    'view_activity_log',
    'can_request_export',
    'can_request_deletion',
],
```

**RUOLO: inspector** (DA CREARE - non presente nel seeder)

**Permessi necessari:**

```php
'inspector' => [
    // Base access
    'access_dashboard',
    'view_dashboard',
    'view_collection',              // Solo collections assegnate
    'view_EGI',                     // Solo EGI da ispezionare
    'view_statistics',

    // Inspector specific (CREARE NUOVI PERMESSI)
    'sign_coa',                     // ✅ Già esistente
    'view_assigned_collections',    // NUOVO
    'upload_inspection_report',     // NUOVO
    'revoke_coa_if_issues',        // NUOVO
    'access_inspector_dashboard',   // NUOVO
    'manage_inspection_assignments', // NUOVO

    // Profile & GDPR
    'manage_profile',
    'manage_account',
    'edit_own_profile_data',
    'edit_own_personal_data',
    'manage_own_documents',
    'upload_identity_documents',    // Per certificazioni professionali
    'verify_document_status',
    'manage_consents',
    'manage_privacy',
    'export_personal_data',
    'view_activity_log',
    'can_request_export',
    'can_request_deletion',
],
```

**RUOLO: company** (ESISTE già nel seeder - verificare permessi)

**Permessi company esistenti + DA AGGIUNGERE:**

```php
'company' => [
    // ... permessi esistenti dal seeder ...

    // DA AGGIUNGERE per sistema PA/Enterprise:
    'create_collection',              // Se non presente
    'bulk_coa_operations',            // Per certificazioni massive
    'generate_qr_codes',              // NUOVO - QR code prodotti
    'public_coa_verification',        // NUOVO - verifica pubblica
    'product_certification_access',   // NUOVO - area certificazioni
],
```

### 7.3 Middleware & Gates (Spatie Role-Based)

**Protezione routes con Spatie middleware:**

```php
// routes/web.php

Route::middleware(['auth', 'verified'])->group(function () {

    // PA Area - protected by Spatie role middleware
    Route::prefix('pa')->name('pa.')->group(function () {

        // Middleware Spatie: role:role_name
        Route::middleware('role:pa_entity')->group(function () {
            Route::get('/dashboard', [PADashboardController::class, 'index'])->name('dashboard');
            Route::resource('collections', PACollectionController::class);
            Route::resource('heritage', PAHeritageController::class);
            Route::post('/coa/request', [PACoAController::class, 'request'])->name('coa.request');
        });
    });

    // Inspector Area - protected
    Route::prefix('inspector')->name('inspector.')->group(function () {

        Route::middleware('role:inspector')->group(function () {
            Route::get('/dashboard', [InspectorDashboardController::class, 'index'])->name('dashboard');
            Route::get('/assignments', [InspectorController::class, 'assignments'])->name('assignments');
            Route::post('/coa/{coa}/sign', [InspectorController::class, 'signCoA'])->name('coa.sign');
            Route::post('/inspection/report', [InspectorController::class, 'uploadReport'])->name('inspection.report');
        });
    });

    // Company Area - protected
    Route::prefix('company')->name('company.')->group(function () {

        Route::middleware('role:company')->group(function () {
            Route::get('/dashboard', [CompanyDashboardController::class, 'index'])->name('dashboard');
            Route::resource('products', CompanyProductController::class);
            Route::get('/coa/qr-codes', [CompanyCoAController::class, 'qrCodes'])->name('coa.qrcodes');
        });
    });
});
```

**Gates per permission-specific checks:**

```php
// app/Providers/AuthServiceProvider.php

public function boot(): void
{
    // PA-specific gates
    Gate::define('manage-institutional-collections', function (User $user) {
        return $user->hasPermissionTo('manage_institutional_collections');
    });

    Gate::define('sign-coa-as-inspector', function (User $user, Coa $coa) {
        // Inspector può firmare SOLO CoA dove è assegnato come collaboratore nella collection
        return $user->hasRole('inspector')
            && $user->hasPermissionTo('sign_coa')
            && $coa->egi->collection->users()
                ->wherePivot('user_id', $user->id)
                ->wherePivot('role', 'inspector')
                ->exists();
    });

    Gate::define('view-pa-collection', function (User $user, Collection $collection) {
        // PA Entity può vedere SOLO le proprie collections
        return $user->hasRole('pa_entity')
            && ($collection->owner_id === $user->id
                || $collection->creator_id === $user->id);
    });
}
```

**Uso nei Controllers:**

```php
// app/Http/Controllers/Inspector/InspectorController.php

public function signCoA(Request $request, Coa $coa)
{
    // Check permission con Gate
    if (!Gate::allows('sign-coa-as-inspector', $coa)) {
        abort(403, 'Non autorizzato a firmare questo CoA');
    }

    // ... logica firma
}
```

---

## 🗂️ **8. MENU SIDEBAR → CONTESTI PA/ENTERPRISE**

### 8.1 ContextMenus - Estensioni

**File: `app/Services/Menu/ContextMenus.php`**

**Nuovo context: 'pa_dashboard'**

```php
case 'pa_dashboard':
    $menus[] = new MenuGroup(
        __('menu.pa_overview'),
        'pa_dashboard_icon',
        [
            new MenuItem('menu.pa_dashboard', 'pa.dashboard', 'home_icon'),
            new MenuItem('menu.pa_statistics', 'pa.statistics', 'chart_icon')
        ]
    );

    $menus[] = new MenuGroup(
        __('menu.pa_heritage'),
        'heritage_icon',
        [
            new MenuItem('menu.heritage_list', 'pa.heritage.index', 'list_icon'),
            new MenuItem('menu.heritage_create', 'pa.heritage.create', 'plus_icon'),
            new MenuItem('menu.collections_pa', 'pa.collections.index', 'folder_icon')
        ]
    );

    $menus[] = new MenuGroup(
        __('menu.pa_certificates'),
        'certificate_icon',
        [
            new MenuItem('menu.coa_active', 'pa.coa.index', 'badge_icon'),
            new MenuItem('menu.coa_request', 'pa.coa.request', 'document_icon'),
            new MenuItem('menu.inspectors', 'pa.inspectors.index', 'inspector_icon')
        ]
    );

    $menus[] = new MenuGroup(
        __('menu.pa_settings'),
        'settings_icon',
        [
            new MenuItem('menu.entity_profile', 'pa.profile', 'building_icon'),
            new MenuItem('menu.wallet_iban', 'pa.wallet', 'credit_card_icon'),
            new MenuItem('menu.gdpr_consents', 'gdpr.consents', 'shield_icon')
        ]
    );
    break;
```

**Nuovo context: 'inspector_dashboard'**

```php
case 'inspector_dashboard':
    $menus[] = new MenuGroup(
        __('menu.inspector_overview'),
        'inspector_dashboard_icon',
        [
            new MenuItem('menu.inspector_dashboard', 'inspector.dashboard', 'home_icon'),
            new MenuItem('menu.my_assignments', 'inspector.assignments', 'clipboard_icon')
        ]
    );

    $menus[] = new MenuGroup(
        __('menu.inspections'),
        'inspection_icon',
        [
            new MenuItem('menu.pending_inspections', 'inspector.pending', 'clock_icon'),
            new MenuItem('menu.completed_inspections', 'inspector.completed', 'check_icon'),
            new MenuItem('menu.upload_report', 'inspector.report.create', 'upload_icon')
        ]
    );

    $menus[] = new MenuGroup(
        __('menu.coa_signatures'),
        'signature_icon',
        [
            new MenuItem('menu.coa_to_sign', 'inspector.coa.pending', 'pen_icon'),
            new MenuItem('menu.coa_signed', 'inspector.coa.signed', 'badge_check_icon')
        ]
    );

    $menus[] = new MenuGroup(
        __('menu.inspector_profile'),
        'user_icon',
        [
            new MenuItem('menu.certifications', 'inspector.certifications', 'certificate_icon'),
            new MenuItem('menu.professional_profile', 'inspector.profile', 'id_card_icon')
        ]
    );
    break;
```

**Nuovo context: 'company_dashboard'**

```php
case 'company_dashboard':
    $menus[] = new MenuGroup(
        __('menu.company_overview'),
        'company_dashboard_icon',
        [
            new MenuItem('menu.company_dashboard', 'company.dashboard', 'home_icon'),
            new MenuItem('menu.analytics', 'company.analytics', 'chart_icon')
        ]
    );

    $menus[] = new MenuGroup(
        __('menu.products'),
        'product_icon',
        [
            new MenuItem('menu.products_list', 'company.products.index', 'list_icon'),
            new MenuItem('menu.product_create', 'company.products.create', 'plus_icon'),
            new MenuItem('menu.product_collections', 'company.collections.index', 'folder_icon')
        ]
    );

    $menus[] = new MenuGroup(
        __('menu.certifications'),
        'certificate_icon',
        [
            new MenuItem('menu.coa_active', 'company.coa.index', 'badge_icon'),
            new MenuItem('menu.qr_codes', 'company.coa.qrcodes', 'qrcode_icon'),
            new MenuItem('menu.request_verification', 'company.verification.request', 'shield_icon')
        ]
    );

    $menus[] = new MenuGroup(
        __('menu.company_settings'),
        'settings_icon',
        [
            new MenuItem('menu.company_profile', 'company.profile', 'building_icon'),
            new MenuItem('menu.wallet_iban', 'company.wallet', 'credit_card_icon'),
            new MenuItem('menu.branding', 'company.branding', 'palette_icon')
        ]
    );
    break;
```

---

## 📊 **9. DASHBOARD KPI - METRICHE PA/ENTERPRISE**

### 9.1 KPI PA Entity

**StatisticsService - nuovi metodi:**

```php
// app/Services/Statistics/PAStatisticsService.php

public function getPADashboardKPIs(User $paEntity): array
{
    $collections = Collection::where('user_id', $paEntity->id)
        ->where('type', 'pa_heritage')
        ->get();

    return [
        'total_heritage_items' => Egi::whereIn('collection_id', $collections->pluck('id'))->count(),

        'coa_active' => Coa::whereIn('egi_id',
            Egi::whereIn('collection_id', $collections->pluck('id'))->pluck('id')
        )->where('status', 'valid')->count(),

        'coa_pending_inspection' => Coa::whereIn('egi_id',
            Egi::whereIn('collection_id', $collections->pluck('id'))->pluck('id')
        )->where('status', 'pending_inspection')->count(),

        'inspectors_active' => \DB::table('collection_user')
            ->whereIn('collection_id', $collections->pluck('id'))
            ->where('role', 'inspector')
            ->where('status', 'active')      // Status nella pivot
            ->distinct('user_id')
            ->count(),

        'public_verifications_month' => CoaEvent::whereIn('coa_id',
            Coa::whereIn('egi_id', Egi::whereIn('collection_id', $collections->pluck('id'))->pluck('id'))->pluck('id')
        )->where('type', 'PUBLIC_VERIFICATION')
        ->whereMonth('created_at', now()->month)
        ->count(),

        'avg_inspection_days' => $this->calculateAvgInspectionTime($collections)
    ];
}
```

### 9.2 KPI Inspector

```php
// app/Services/Statistics/InspectorStatisticsService.php

public function getInspectorDashboardKPIs(User $inspector): array
{
    return [
        'assignments_pending' => $this->getPendingAssignments($inspector)->count(),

        'assignments_completed_month' => $this->getCompletedAssignmentsMonth($inspector)->count(),

        'coa_signed_total' => CoaSignature::where('signer_user_id', $inspector->id)
            ->where('kind', 'inspector')
            ->count(),

        'average_rating' => $this->getInspectorAverageRating($inspector),

        'response_time_avg_hours' => $this->calculateAvgResponseTime($inspector)
    ];
}
```

### 9.3 KPI Company

```php
// app/Services/Statistics/CompanyStatisticsService.php

public function getCompanyDashboardKPIs(User $company): array
{
    $collections = Collection::where('user_id', $company->id)
        ->where('type', 'company_products')
        ->get();

    return [
        'products_certified' => Egi::whereIn('collection_id', $collections->pluck('id'))
            ->whereHas('coa', fn($q) => $q->where('status', 'valid'))
            ->count(),

        'qr_scans_month' => CoaEvent::whereIn('coa_id',
            Coa::whereIn('egi_id', Egi::whereIn('collection_id', $collections->pluck('id'))->pluck('id'))->pluck('id')
        )->where('type', 'QR_SCANNED')
        ->whereMonth('created_at', now()->month)
        ->count(),

        'authenticity_verified' => $this->getVerificationCount($collections),

        'revenue_from_certified' => $this->calculateRevenueCertified($collections),

        'fake_detected' => $this->getFakeDetectionCount($collections)
    ];
}
```

---

## 🚀 **10. IMPLEMENTAZIONE ROADMAP**

### Fase 1: Foundation (Settimana 1-2)

**Database:**

-   [ ] Migration: Add `type` to `collections`
-   [ ] Migration: Add `institutional_metadata` to `collections`
-   [ ] Migration: Add PA-specific values to `coa.issuer_type`
-   [ ] Seeder: Test data PA/Company/Inspector

**Services:**

-   [ ] PAStatisticsService
-   [ ] InspectorStatisticsService
-   [ ] CompanyStatisticsService
-   [ ] CoAInspectionService (nuovo)

### Fase 2: Controllers & Routes (Settimana 3-4)

**Controllers:**

-   [ ] PADashboardController
-   [ ] PACollectionController
-   [ ] PAHeritageController (EGI context PA)
-   [ ] PACoAController
-   [ ] InspectorDashboardController
-   [ ] InspectorController (assignments, signing)
-   [ ] CompanyDashboardController
-   [ ] CompanyProductController

**Routes:**

-   [ ] PA routes group
-   [ ] Inspector routes group
-   [ ] Company routes group
-   [ ] Middleware role protection

### Fase 3: Views & Components (Settimana 5-6)

**Layouts:**

-   [ ] pa-layout.blade.php (sidebar PA)
-   [ ] inspector-layout.blade.php
-   [ ] company-layout.blade.php

**Components (seguendo PA_ENTERPRISE_BRAND_GUIDELINES.md):**

-   [ ] pa-entity-card.blade.php
-   [ ] coa-badge-institutional.blade.php
-   [ ] status-pill.blade.php
-   [ ] audit-timeline.blade.php
-   [ ] qr-verification-box.blade.php

**Pages:**

-   [ ] pa/dashboard.blade.php
-   [ ] pa/heritage/index.blade.php
-   [ ] pa/coa/request.blade.php
-   [ ] inspector/dashboard.blade.php
-   [ ] inspector/assignments.blade.php
-   [ ] company/dashboard.blade.php

### Fase 4: Menu System (Settimana 7)

**Menu Items:**

-   [ ] Creare MenuItem classes per PA context
-   [ ] Creare MenuItem classes per Inspector context
-   [ ] Creare MenuItem classes per Company context
-   [ ] Estendere ContextMenus.php con nuovi contexts
-   [ ] Icone SVG custom (certificate, inspector, etc)

### Fase 5: Testing & Refinement (Settimana 8)

**Unit Tests:**

-   [ ] PAStatisticsService tests
-   [ ] CoAInspectionService tests
-   [ ] Permissions gates tests

**Feature Tests:**

-   [ ] PA dashboard access
-   [ ] Inspector signing workflow
-   [ ] Company QR generation
-   [ ] GDPR audit trail PA-specific

**Manual Testing:**

-   [ ] Onboarding PA Entity
-   [ ] Inspector assignment flow
-   [ ] CoA request → inspection → sign → valid
-   [ ] QR code public verification

---

## ✅ **11. CHECKLIST ARCHITETTURALE**

**Prima di ogni sviluppo, verificare:**

-   [ ] Sto riusando architettura esistente?
-   [ ] Ho bisogno DAVVERO di nuova tabella o basta JSON metadata?
-   [ ] GDPR/ULTRA integration presente?
-   [ ] Audit trail configurato?
-   [ ] Permissions gates implementati?
-   [ ] Consensi GDPR mappati?
-   [ ] UEM error codes definiti?
-   [ ] ULM logging presente?
-   [ ] Menu sidebar configurato per contesto?
-   [ ] Brand guidelines PA rispettate?
-   [ ] Accessibilità WCAG 2.1 AA verificata?
-   [ ] Mobile responsive testato?

---

## 📞 **12. RIFERIMENTI TECNICI**

**Architettura Esistente:**

-   Collection model: `app/Models/Collection.php`
-   EGI model: `app/Models/Egi.php`
-   CoA architecture: `docs/CoA/ARCHITETTURA_COA_COMPLETA.md`
-   GDPR system: `app/Services/Gdpr/`
-   ULTRA integration: `docs/ai/` (ULM, UEM docs)

**Nuovi Documenti:**

-   Design system PA: `PA_ENTERPRISE_BRAND_GUIDELINES.md`
-   Questo documento: `PA_ENTERPRISE_ARCHITECTURE.md`

**Standard Compliance:**

-   Laravel 10+ best practices
-   SOLID principles
-   Repository pattern (dove appropriato)
-   Service layer mandatory per business logic

---

**Questo documento è la guida tecnica per implementare il sistema PA/Enterprise riusando intelligentemente l'architettura esistente.**

**Principio guida: Extend, don't rebuild. GDPR e ULTRA sempre attivi.**

_FlorenceEGI PA/Enterprise - Eccellenza tecnica riutilizzabile_

---

**#architecture #pa-enterprise #reusability #collection-egi #gdpr #ultra**
