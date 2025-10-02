# PA/ENTERPRISE SYSTEM - MASTER TODO LIST

**Status:** 📋 ACTIVE TRACKING  
**Target MVP:** Demo Assessori (2 settimane)  
**Target Release:** Production PA/Enterprise (8 settimane)  
**Approach:** A (Minimal) → B (Balanced) → Full Release

---

## 🎯 MILESTONE STRUCTURE

```
FASE 1: MVP QUICK WIN (Demo Assessori) ⏱️ 2 settimane
├─ Database minimal changes
├─ PA Dashboard basic
├─ CoA display istituzionale
├─ Statistics PA (mock data OK)
└─ Visual demo screenshot-ready

FASE 2: POST-MVP EXPANSION ⏱️ 2 settimane
├─ Vocabulary expansion (+25 termini)
├─ Database metadata complete
├─ Statistics services real data
├─ Menu system dynamic
└─ GDPR consent PA types

FASE 3: RELEASE FINALE ⏱️ 4 settimane
├─ Inspector workflow complete
├─ Company QR system
├─ API pubblica verification
├─ Testing completo
└─ Deployment production
```

---

## 📊 PROGRESS TRACKING

**Overall Progress:** 15% (brainstorming + design docs complete)

| Fase                  | Progress | Status         | ETA      |
| --------------------- | -------- | -------------- | -------- |
| **FASE 1: MVP**       | 10%      | 🟡 IN PROGRESS | 2 weeks  |
| **FASE 2: Expansion** | 0%       | ⚪ NOT STARTED | +2 weeks |
| **FASE 3: Release**   | 0%       | ⚪ NOT STARTED | +4 weeks |

---

## 🚀 FASE 1: MVP QUICK WIN (Demo Assessori)

**OBIETTIVO:** Dashboard PA funzionale con CoA display, screenshot per demo assessori  
**APPROCCIO:** A (Minimal) - usa sistema esistente + custom terms  
**EFFORT TOTALE:** ~40 ore (2 settimane)

### ✅ COMPLETATO (Brainstorming & Design)

-   [x] **TASK 1.0: Strategic Planning** ✅ COMPLETATO

    -   Brainstorming PA/Enterprise system
    -   Target PA-first strategy
    -   3-5 comuni toscani pilot identificati
    -   **Files:** PA_ENTERPRISE_BRAND_GUIDELINES.md, PA_ENTERPRISE_ARCHITECTURE.md

-   [x] **TASK 1.1: Design System** ✅ COMPLETATO

    -   Palette istituzionale (Blu #1B365D, Oro #B89968)
    -   Typography (IBM Plex Sans)
    -   Layout grid, sidebar 280px
    -   Componenti UI specs
    -   **File:** PA_ENTERPRISE_BRAND_GUIDELINES.md (70KB)

-   [x] **TASK 1.2: Architecture Verification** ✅ COMPLETATO
    -   Verificato Spatie roles: inspector (line 839), pa_entity (line 852)
    -   Verificato collection_user pivot (NOT collection_collaborators)
    -   Verificato Collection model relationships
    -   CoA traits system analysis (248 termini, 3 categorie sufficienti)
    -   **Files:** PA_ENTERPRISE_ARCHITECTURE.md, PA_ENTERPRISE_ARCHITECTURE_CHANGELOG.md

### 🟡 IN PROGRESS (Database Layer)

-   [ ] **TASK 2.1: Database Migrations - Collections** ⏱️ 2h

    -   **Priority:** P0 (BLOCKING)
    -   **File:** `database/migrations/YYYY_MM_DD_add_pa_enterprise_to_collections.php`
    -   **Changes:**

        ```php
        // 1. ADD metadata JSON to collections table
        $table->json('metadata')->nullable()->after('featured_position');

        // 2. EXPAND type VARCHAR(25) → VARCHAR(50)
        DB::statement('ALTER TABLE collections MODIFY type VARCHAR(50)');

        // 3. ADD new type values (optional, can use existing)
        // 'pa_heritage', 'pa_documents', 'company_products', 'company_catalog'
        ```

    -   **Testing:** `php artisan migrate` + rollback test
    -   **Dependencies:** None
    -   **Output:** Migration file + test success

-   [ ] **TASK 2.2: Database Verification - CoA Tables** ⏱️ 1h

    -   **Priority:** P1 (HIGH)
    -   **Command:** `php artisan tinker --execute="Schema::getColumnListing('coa');"`
    -   **Verify:**
        -   `issuer_type` ENUM has PA values OR can use JSON metadata
        -   `coa_files` table exists with `kind` field
        -   `coa_signatures` table ready for inspector signatures
    -   **Decision:** Use metadata JSON or extend ENUM?
    -   **Dependencies:** None
    -   **Output:** Schema verification report

-   [ ] **TASK 2.3: Seeders - PA Demo Data** ⏱️ 3h
    -   **Priority:** P1 (HIGH for demo)
    -   **File:** `database/seeders/PAEnterpriseDemoSeeder.php`
    -   **Data:**
        ```php
        // 1. Comune di Firenze (PA Entity user)
        // 2. Collection "Patrimonio Monumentale Comunale" type=pa_heritage
        // 3. 5-10 EGI: Statua David (replica), Palazzo Vecchio Affresco, etc.
        // 4. CoA emessi per alcuni EGI
        // 5. Inspector assigned via collection_user pivot
        ```
    -   **Dependencies:** TASK 2.1 complete
    -   **Output:** Seeder file + demo data testabile

### 🟡 IN PROGRESS (Backend Layer)

-   [ ] **TASK 3.1: Routes - PA Enterprise** ⏱️ 2h

    -   **Priority:** P0 (BLOCKING)
    -   **File:** `routes/pa-enterprise.php` (nuovo file)
    -   **Structure:**
        ```php
        Route::prefix('pa')->middleware(['auth', 'role:pa_entity'])->group(function () {
            Route::get('/dashboard', [PADashboardController::class, 'index'])->name('pa.dashboard');
            Route::get('/heritage', [PAHeritageController::class, 'index'])->name('pa.heritage');
            Route::get('/heritage/{egi}', [PAHeritageController::class, 'show'])->name('pa.heritage.show');
            Route::get('/coa/{coa}', [PACoAController::class, 'show'])->name('pa.coa.show');
        });
        ```
    -   **Register in:** `bootstrap/app.php` or `routes/web.php`
    -   **Dependencies:** None
    -   **Output:** Routes file + registration

-   [ ] **TASK 3.2: Controller - PADashboardController** ⏱️ 4h

    -   **Priority:** P0 (BLOCKING)
    -   **File:** `app/Http/Controllers/PA/PADashboardController.php`
    -   **Methods:**
        ```php
        public function index(): View
        {
            $user = Auth::user();

            // Statistics (MOCK per MVP OK)
            $stats = [
                'total_heritage' => 127,
                'coa_issued' => 89,
                'coa_pending' => 12,
                'inspections_active' => 5,
            ];

            // Recent heritage (real data)
            $recentHeritage = Egi::whereHas('collections', function($q) use ($user) {
                $q->where('collections.type', 'pa_heritage')
                  ->where('collections.owner_id', $user->id);
            })->latest()->take(5)->get();

            return view('pa.dashboard', compact('stats', 'recentHeritage'));
        }
        ```
    -   **GDPR:** ✅ No data modification, read-only
    -   **ULM:** Log dashboard access
    -   **Dependencies:** TASK 3.1
    -   **Output:** Controller + ULM integration

-   [ ] **TASK 3.3: Controller - PAHeritageController** ⏱️ 3h

    -   **Priority:** P1 (HIGH)
    -   **File:** `app/Http/Controllers/PA/PAHeritageController.php`
    -   **Methods:**
        ```php
        public function index(): View // Lista patrimonio
        public function show(Egi $egi): View // Dettaglio + CoA
        ```
    -   **Checks:** Verify user owns collection via collection_user
    -   **Dependencies:** TASK 3.2
    -   **Output:** Controller + authorization checks

-   [ ] **TASK 3.4: Service - PAStatisticsService (MOCK)** ⏱️ 2h
    -   **Priority:** P2 (per demo MOCK OK)
    -   **File:** `app/Services/Statistics/PAStatisticsService.php`
    -   **Methods:**
        ```php
        public function getDashboardStats(User $paEntity): array
        {
            // MVP: return MOCK data
            // POST-MVP: query real data from collection_user + coa tables
            return [
                'total_heritage' => 127,
                'coa_issued' => 89,
                'coa_pending' => 12,
                'inspections_active' => 5,
                'blockchain_verifications' => 89,
                'public_visibility' => 76,
            ];
        }
        ```
    -   **Note:** MOCK per MVP, real queries in FASE 2
    -   **Dependencies:** None
    -   **Output:** Service with MOCK data

### 🟡 IN PROGRESS (Frontend Layer)

-   [ ] **TASK 4.1: Layout - pa-layout.blade.php** ⏱️ 4h

    -   **Priority:** P0 (BLOCKING)
    -   **File:** `resources/views/layouts/pa-layout.blade.php`
    -   **Specs:**
        ```blade
        {{-- Sidebar istituzionale 280px --}}
        {{-- Background: #1B365D --}}
        {{-- Typography: IBM Plex Sans --}}
        {{-- Logo PA + menu items --}}
        {{-- Footer con info ente --}}
        ```
    -   **Reference:** PA_ENTERPRISE_BRAND_GUIDELINES.md sezioni Layout + Sidebar
    -   **Components:** Usa DaisyUI con custom palette
    -   **Dependencies:** None
    -   **Output:** Layout blade + CSS customizations

-   [ ] **TASK 4.2: View - pa/dashboard.blade.php** ⏱️ 5h

    -   **Priority:** P0 (BLOCKING)
    -   **File:** `resources/views/pa/dashboard.blade.php`
    -   **Sections:**
        ```blade
        {{-- KPI Cards (4 stats in grid) --}}
        {{-- Recent Heritage Table (5 items) --}}
        {{-- CoA Status Chart (mock chart) --}}
        {{-- Quick Actions (CTA buttons) --}}
        ```
    -   **Design:** Palette istituzionale, WCAG 2.1 AA compliance
    -   **Dependencies:** TASK 4.1
    -   **Output:** Dashboard view

-   [ ] **TASK 4.3: View - pa/heritage/index.blade.php** ⏱️ 3h

    -   **Priority:** P1 (HIGH)
    -   **File:** `resources/views/pa/heritage/index.blade.php`
    -   **Components:**
        -   Heritage table with filters
        -   CoA status badges
        -   Actions (view, download CoA PDF)
    -   **Dependencies:** TASK 4.1
    -   **Output:** Heritage list view

-   [ ] **TASK 4.4: View - pa/heritage/show.blade.php** ⏱️ 4h

    -   **Priority:** P1 (HIGH)
    -   **File:** `resources/views/pa/heritage/show.blade.php`
    -   **Sections:**
        ```blade
        {{-- EGI Details Card --}}
        {{-- CoA Traits Display (technique/materials/support) --}}
        {{-- CoA Files List (PDF, images, annexes) --}}
        {{-- Signatures Section (inspector + owner) --}}
        {{-- Blockchain Verification Badge --}}
        {{-- Public QR Code Display --}}
        ```
    -   **Design:** Professional PA layout
    -   **Dependencies:** TASK 4.1
    -   **Output:** Heritage detail view

-   [ ] **TASK 4.5: Components - PA UI Kit** ⏱️ 6h
    -   **Priority:** P1 (HIGH)
    -   **Files:** `resources/views/components/pa/*.blade.php`
    -   **Components:**
        ```
        pa-stat-card.blade.php         // KPI card component
        pa-heritage-card.blade.php     // Heritage preview card
        pa-coa-badge.blade.php         // CoA status badge
        pa-entity-header.blade.php     // PA entity info header
        pa-action-button.blade.php     // Institutional CTA button
        ```
    -   **Specs:** Follow PA_ENTERPRISE_BRAND_GUIDELINES.md
    -   **Dependencies:** None (parallel)
    -   **Output:** 5 blade components

### 🟡 IN PROGRESS (Integration & Testing)

-   [ ] **TASK 5.1: Menu System - PA Context** ⏱️ 3h

    -   **Priority:** P1 (HIGH)
    -   **File:** `app/Services/Menu/ContextMenus.php` (extend existing)
    -   **Add:**
        ```php
        'pa_dashboard' => [
            new MenuItem('Dashboard', 'pa.dashboard', 'dashboard-icon'),
            new MenuItem('Patrimonio', 'pa.heritage', 'heritage-icon'),
            new MenuItem('CoA Emessi', 'pa.coa.index', 'certificate-icon'),
            new MenuItem('Ispettori', 'pa.inspectors', 'inspector-icon'),
        ],
        ```
    -   **Dependencies:** TASK 3.1 (routes defined)
    -   **Output:** Menu context added

-   [ ] **TASK 5.2: GDPR Integration - Basic** ⏱️ 2h

    -   **Priority:** P1 (compliance)
    -   **Files:** Controllers (add ULM logging)
    -   **Integration:**
        ```php
        // In ogni controller PA
        $this->logger->info('PA Dashboard accessed', [
            'user_id' => Auth::id(),
            'user_role' => 'pa_entity',
        ]);
        ```
    -   **Dependencies:** TASK 3.2, 3.3
    -   **Output:** ULM logging integrated

-   [ ] **TASK 5.3: Testing - MVP Checklist** ⏱️ 4h

    -   **Priority:** P0 (pre-demo)
    -   **Tests:**
        ```
        ✓ PA user login → redirect to /pa/dashboard
        ✓ Dashboard renders with stats
        ✓ Heritage list displays EGI
        ✓ Heritage detail shows CoA traits
        ✓ CoA PDF download works
        ✓ Accessibility WCAG 2.1 AA (axe DevTools)
        ✓ Responsive mobile/tablet
        ✓ Statistics accuracy (anche se MOCK)
        ✓ No errors in browser console
        ✓ ULM logs written correctly
        ```
    -   **Tools:** Manual testing + axe DevTools
    -   **Dependencies:** All FASE 1 tasks
    -   **Output:** Testing checklist report

-   [ ] **TASK 5.4: Demo Preparation - Screenshots** ⏱️ 2h
    -   **Priority:** P0 (for assessori)
    -   **Deliverables:**
        ```
        1. PA Dashboard screenshot (desktop)
        2. Heritage list screenshot
        3. Heritage detail + CoA screenshot
        4. CoA PDF certificate example
        5. Mobile responsive screenshot
        ```
    -   **Quality:** High-res, professional, no placeholder text
    -   **Dependencies:** TASK 5.3 (testing passed)
    -   **Output:** 5 PNG screenshots + 1 PDF

---

## 📈 FASE 2: POST-MVP EXPANSION

**OBIETTIVO:** Vocabulary expansion, real data statistics, menu dynamic  
**APPROCCIO:** B (Balanced) - aggiungi 25 termini vocabulary  
**EFFORT TOTALE:** ~30 ore (2 settimane)

### Database & Services

-   [ ] **TASK 6.1: Vocabulary Expansion** ⏱️ 4h

    -   **Priority:** P1 (coverage PA complete)
    -   **File:** `resources/lang/it/coa_vocabulary.php`
    -   **Add:** 25 termini PRIORITÀ 1+2 (vedere PA_ENTERPRISE_VOCABULARY_EXPANSION.md)
    -   **Testing:** Modal vocabulary + search
    -   **Dependencies:** None
    -   **Output:** Updated vocabulary file

-   [ ] **TASK 6.2: Collections Metadata Usage** ⏱️ 3h

    -   **Priority:** P1
    -   **Implementation:**

        ```php
        // In Collection model, cast metadata
        protected $casts = ['metadata' => 'array'];

        // Usage in controllers
        $collection->metadata = [
            'pa_entity_code' => 'C_H501',
            'institution_name' => 'Comune di Firenze',
            'department' => 'Ufficio Cultura',
            'contact_email' => 'cultura@comune.fi.it',
            'heritage_type' => 'monumentale',
        ];
        ```

    -   **Dependencies:** TASK 2.1 (migration)
    -   **Output:** Metadata usage patterns documented

-   [ ] **TASK 6.3: PAStatisticsService - Real Data** ⏱️ 5h

    -   **Priority:** P1
    -   **Refactor:** Replace MOCK with real queries
    -   **Queries:**

        ```php
        // Total heritage owned by PA
        Collection::where('type', 'pa_heritage')
            ->where('owner_id', $paEntity->id)
            ->count();

        // CoA issued (via EGI relationship)
        Coa::whereHas('egi.collections', function($q) use ($paEntity) {
            $q->where('owner_id', $paEntity->id);
        })->count();
        ```

    -   **REMEMBER:** NO ->take() or ->limit() without params (REGOLA STATISTICS)
    -   **Dependencies:** TASK 2.3 (demo data)
    -   **Output:** Service with real queries

-   [ ] **TASK 6.4: InspectorStatisticsService** ⏱️ 4h

    -   **Priority:** P2
    -   **File:** `app/Services/Statistics/InspectorStatisticsService.php`
    -   **Methods:**
        ```php
        public function getAssignments(User $inspector): Collection
        public function getPendingInspections(User $inspector): Collection
        public function getCompletedInspections(User $inspector): int
        ```
    -   **Dependencies:** None (parallel)
    -   **Output:** Inspector statistics service

-   [ ] **TASK 6.5: CompanyStatisticsService** ⏱️ 4h
    -   **Priority:** P2
    -   **File:** `app/Services/Statistics/CompanyStatisticsService.php`
    -   **Methods:**
        ```php
        public function getProducts(User $company): Collection
        public function getCoAIssued(User $company): int
        public function getQRScans(User $company): int
        ```
    -   **Dependencies:** None (parallel)
    -   **Output:** Company statistics service

### Frontend & GDPR

-   [ ] **TASK 7.1: Dynamic Menu System** ⏱️ 4h

    -   **Priority:** P2
    -   **Implementation:** Menu builder checks user role dynamically
    -   **Dependencies:** TASK 5.1
    -   **Output:** Dynamic menu service

-   [ ] **TASK 7.2: GDPR Consent Types PA** ⏱️ 3h

    -   **Priority:** P1 (compliance)
    -   **File:** `config/gdpr.php`
    -   **Add:**
        ```php
        'consent_types' => [
            'allow-institutional-data-processing' => [...],
            'allow-inspector-access' => [...],
            'allow-public-coa-verification' => [...],
        ],
        ```
    -   **Dependencies:** None
    -   **Output:** GDPR config extended

-   [ ] **TASK 7.3: GdprActivityCategory Extensions** ⏱️ 2h
    -   **Priority:** P1
    -   **File:** `app/Enums/Gdpr/GdprActivityCategory.php`
    -   **Add:**
        ```php
        case PA_HERITAGE_ACCESS = 'pa_heritage_access';
        case INSPECTOR_ASSIGNMENT = 'inspector_assignment';
        case COA_PUBLIC_VERIFICATION = 'coa_public_verification';
        ```
    -   **Dependencies:** None
    -   **Output:** Enum extended

---

## 🚀 FASE 3: RELEASE FINALE

**OBIETTIVO:** Inspector workflow, Company QR, API pubblica, deployment  
**EFFORT TOTALE:** ~60 ore (4 settimane)

### Inspector Workflow

-   [ ] **TASK 8.1: Service - CoAInspectionService** ⏱️ 6h

    -   **Priority:** P0 (core feature)
    -   **File:** `app/Services/CoA/CoAInspectionService.php`
    -   **Methods:**
        ```php
        public function assignInspector(Collection $collection, User $inspector): void
        public function uploadReport(Coa $coa, UploadedFile $report): CoaFile
        public function signCoA(Coa $coa, User $inspector, string $signatureData): CoaSignature
        ```
    -   **GDPR:** Audit log + consent check
    -   **Dependencies:** None
    -   **Output:** Inspection service

-   [ ] **TASK 8.2: Controller - InspectorController** ⏱️ 5h

    -   **Priority:** P0
    -   **File:** `app/Http/Controllers/Inspector/InspectorController.php`
    -   **Routes:**
        ```php
        Route::prefix('inspector')->middleware(['auth', 'role:inspector'])->group([...]);
        ```
    -   **Dependencies:** TASK 8.1
    -   **Output:** Inspector controller

-   [ ] **TASK 8.3: Views - Inspector Dashboard** ⏱️ 6h
    -   **Priority:** P0
    -   **Files:** `resources/views/inspector/*.blade.php`
    -   **Views:** dashboard, assignments, coa-review, sign-coa
    -   **Dependencies:** TASK 8.2
    -   **Output:** Inspector views

### Company QR System

-   [ ] **TASK 9.1: Service - QRCodeGenerationService** ⏱️ 4h

    -   **Priority:** P1
    -   **File:** `app/Services/QR/QRCodeGenerationService.php`
    -   **Library:** `simplesoftwareio/simple-qrcode`
    -   **Method:**
        ```php
        public function generateCoAVerificationQR(Coa $coa): string
        {
            $url = route('public.coa.verify', ['uuid' => $coa->uuid]);
            return QrCode::size(300)->generate($url);
        }
        ```
    -   **Dependencies:** None
    -   **Output:** QR service

-   [ ] **TASK 9.2: Public API - CoA Verification** ⏱️ 5h

    -   **Priority:** P1
    -   **Route:** `GET /public/coa/verify/{uuid}` (NO auth)
    -   **Response:** JSON with CoA status, blockchain hash, issuer
    -   **Rate Limiting:** 60 requests/minute
    -   **Dependencies:** TASK 9.1
    -   **Output:** Public verification API

-   [ ] **TASK 9.3: Controller - CompanyDashboardController** ⏱️ 4h

    -   **Priority:** P1
    -   **File:** `app/Http/Controllers/Company/CompanyDashboardController.php`
    -   **Dependencies:** TASK 6.5 (CompanyStatisticsService)
    -   **Output:** Company controller

-   [ ] **TASK 9.4: Views - Company Dashboard** ⏱️ 5h
    -   **Priority:** P1
    -   **Files:** `resources/views/company/*.blade.php`
    -   **Dependencies:** TASK 9.3
    -   **Output:** Company views

### Testing & Deployment

-   [ ] **TASK 10.1: Feature Tests - PA/Inspector/Company** ⏱️ 8h

    -   **Priority:** P1
    -   **Files:** `tests/Feature/PA/*.php`
    -   **Tests:**
        ```
        PADashboardTest.php
        PAHeritageTest.php
        InspectorWorkflowTest.php
        CompanyQRTest.php
        ```
    -   **Coverage:** >80%
    -   **Dependencies:** All features complete
    -   **Output:** PHPUnit tests

-   [ ] **TASK 10.2: API Documentation** ⏱️ 4h

    -   **Priority:** P2
    -   **Tool:** Postman collection or OpenAPI spec
    -   **Endpoints:** Public verification API
    -   **Dependencies:** TASK 9.2
    -   **Output:** API docs

-   [ ] **TASK 10.3: Deployment Guide** ⏱️ 3h

    -   **Priority:** P1
    -   **File:** `docs/ai/context/PA_ENTERPRISE_DEPLOYMENT.md`
    -   **Sections:**
        -   Environment variables
        -   Database migrations sequence
        -   Seeder run order
        -   Queue configuration
        -   Cache setup
    -   **Dependencies:** None
    -   **Output:** Deployment guide

-   [ ] **TASK 10.4: User Documentation** ⏱️ 4h
    -   **Priority:** P2
    -   **Files:** PDF guides for PA/Inspector/Company users
    -   **Languages:** Italian (primary)
    -   **Dependencies:** None
    -   **Output:** User manuals

---

## 📊 EFFORT SUMMARY

| Fase                  | Tasks        | Effort    | Timeline    |
| --------------------- | ------------ | --------- | ----------- |
| **FASE 1: MVP**       | 19 tasks     | ~40h      | 2 weeks     |
| **FASE 2: Expansion** | 10 tasks     | ~30h      | 2 weeks     |
| **FASE 3: Release**   | 12 tasks     | ~60h      | 4 weeks     |
| **TOTALE**            | **41 tasks** | **~130h** | **8 weeks** |

---

## 🎯 PRIORITIES LEGEND

-   **P0:** BLOCKING - must complete before next tasks
-   **P1:** HIGH - critical for milestone
-   **P2:** MEDIUM - important but can defer
-   **P3:** LOW - nice to have

---

## 📝 TASK STATUS LEGEND

-   ✅ **COMPLETATO** - task finito e verificato
-   🟡 **IN PROGRESS** - task iniziato
-   ⚪ **NOT STARTED** - task da iniziare
-   🔴 **BLOCKED** - task bloccato da dependencies

---

## 🔄 USAGE INSTRUCTIONS

**Per nuove sessioni AI:**

1. Leggere questo file per status attuale
2. Identificare task 🟡 IN PROGRESS o ⚪ NOT STARTED
3. Verificare dependencies completate
4. Iniziare task con Priority P0 > P1 > P2
5. Aggiornare status dopo completion

**Per Fabio:**

1. Spuntare [ ] → [x] task completati
2. Aggiungere note sotto task se necessario
3. Aggiornare Progress % nelle milestone
4. Commit questo file ad ogni sessione

---

## 📚 DOCUMENTATION REFERENCES

| Documento            | Percorso                                                  | Contenuto                          |
| -------------------- | --------------------------------------------------------- | ---------------------------------- |
| Brand Guidelines     | `docs/ai/marketing/PA_ENTERPRISE_BRAND_GUIDELINES.md`     | Palette, typography, UI specs      |
| Architecture         | `docs/ai/context/PA_ENTERPRISE_ARCHITECTURE.md`           | System design, DB schema, patterns |
| Implementation Guide | `docs/ai/context/PA_ENTERPRISE_IMPLEMENTATION_GUIDE.md`   | Code examples, testing checklist   |
| Vocabulary Expansion | `docs/ai/context/PA_ENTERPRISE_VOCABULARY_EXPANSION.md`   | 25 termini PRIORITÀ 1+2            |
| Changelog            | `docs/ai/context/PA_ENTERPRISE_ARCHITECTURE_CHANGELOG.md` | Corrections tracking               |

---

## 🚦 READY TO START

**NEXT IMMEDIATE TASK:** TASK 2.1 (Database Migration - Collections)  
**ETA:** 2 hours  
**Command:** `php artisan make:migration add_pa_enterprise_to_collections`

**Ship it! 🚀**
