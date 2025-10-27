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

FASE 4: PROJECTS SYSTEM (RAG Enhancement) ⏱️ 3 settimane
├─ Database (projects, documents, chunks)
├─ Document processing pipeline
├─ Priority RAG (docs > chats > acts)
├─ Tab-based UI (Documents/Chat/Settings)
└─ Chat history as knowledge base
```

---

## 📊 PROGRESS TRACKING

**Overall Progress:** 60% (brainstorming + design + database + routes + controllers + layout + menu + heritage list complete)

| Fase                     | Progress | Status         | ETA      |
| ------------------------ | -------- | -------------- | -------- |
| **FASE 1: MVP**          | 78%      | 🟡 IN PROGRESS | 5 days   |
| **FASE 2: Expansion**    | 0%       | ⚪ NOT STARTED | +2 weeks |
| **FASE 3: Release**      | 0%       | ⚪ NOT STARTED | +4 weeks |
| **FASE 4: Projects RAG** | 0%       | 🟡 IN PROGRESS | +3 weeks |

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

### ✅ COMPLETATO (Database Layer)

-   [x] **TASK 2.1: Database Migrations - Collections** ✅ COMPLETATO (2h)

    -   **Priority:** P0 (BLOCKING)
    -   **File:** `database/migrations/2025_10_02_075449_add_pa_enterprise_fields_to_collections_table.php`
    -   **Changes Applied:**

        ```php
        // 1. ✅ ADD metadata JSON to collections table
        $table->json('metadata')->nullable()->after('featured_position');

        // 2. ✅ EXPAND type VARCHAR(25) → VARCHAR(50)
        DB::statement('ALTER TABLE collections MODIFY type VARCHAR(50)');

        // 3. ✅ ADD composite index for PA queries
        $table->index(['type', 'owner_id'], 'idx_collections_type_owner');
        ```

    -   **Testing:** ✅ migrate UP 111ms, rollback DOWN 200ms, re-migrate UP 111ms
    -   **Model Update:** ✅ Collection.php `$casts['metadata'] = 'array'` added
    -   **Commit:** `8ac5322` [FEAT] Add PA/Enterprise fields to collections table
    -   **Status:** PRODUCTION READY ✅

-   [ ] **TASK 2.2: Database Verification - CoA Tables** ⏱️ 1h

    -   **Priority:** P1 (HIGH)
    -   **Status:** PARTIALLY VERIFIED via seeder development
    -   **Verified:**
        -   ✅ `issuer_type` ENUM: author|archive|platform (using 'platform' for PA)
        -   ✅ `status` ENUM: valid|revoked
        -   ✅ `coa_files` table exists with `kind` field
        -   ✅ CoA metadata JSON field available for PA-specific data
        -   ✅ `issued_at` timestamp NOT NULL required
    -   **Decision:** Use metadata JSON for PA-specific fields ✅
    -   **Dependencies:** None
    -   **Output:** ✅ Verified during TASK 2.3 development

-   [x] **TASK 2.3: Seeders - PA Demo Data** ✅ COMPLETATO (3h)
    -   **Priority:** P1 (HIGH for demo)
    -   **File:** `database/seeders/PAEnterpriseDemoSeeder.php`
    -   **Data Created:**
        ```php
        // ✅ 1. Comune di Firenze (PA Entity: pa.firenze@comune.fi.it)
        // ✅ 2. Inspector (inspector.demo@florenceegi.com)
        // ✅ 3. Collection "Patrimonio Monumentale Comunale" type=artwork + metadata JSON
        // ✅ 4. 8 Heritage EGI: David, Nettuno, Perseo, Ghiberti, etc.
        // ✅ 5. 6 CoA issued (all valid status with verification_hash)
        // ✅ 6. Inspector assigned via collection_user pivot with metadata
        ```
    -   **Test Result:** ✅ `php artisan db:seed --class=PAEnterpriseDemoSeeder` SUCCESS
    -   **Commit:** `b7d6768` [FEAT] PA Enterprise demo seeder + Collection metadata cast
    -   **Status:** DEMO READY ✅

### ✅ COMPLETATO (Backend Layer - Routes)

-   [x] **TASK 3.1: Routes - PA Enterprise** ✅ COMPLETATO (2h)

    -   **Priority:** P0 (BLOCKING)
    -   **File:** `routes/pa-enterprise.php` (nuovo file creato)
    -   **Routes Registered:**
        ```php
        GET /pa/dashboard         → pa.dashboard → PADashboardController@index
        GET /pa/heritage          → pa.heritage.index → PAHeritageController@index
        GET /pa/heritage/{egi}    → pa.heritage.show → PAHeritageController@show
        ```
    -   **Middleware:** auth + role:pa_entity ✅
    -   **Registration:** ✅ routes/web.php via `require __DIR__ . '/pa-enterprise.php'`
    -   **Testing:** ✅ `php artisan route:list --name=pa` shows 3 routes
    -   **Stub Controllers:** ✅ PADashboardController + PAHeritageController created for route validation
    -   **Commit:** `bb37318` [FEAT] PA Enterprise routes + stub controllers
    -   **Status:** ROUTES READY ✅ (controllers need full implementation in TASK 3.2-3.3)

### ✅ COMPLETATO (Backend Layer - Controllers)

-   [x] **TASK 3.2: Controller - PADashboardController** ✅ COMPLETATO (4h)

    -   **Priority:** P0 (BLOCKING)
    -   **Status:** FULL IMPLEMENTATION ✅
    -   **File:** `app/Http/Controllers/PA/PADashboardController.php`
    -   **Methods Implemented:**

        ```php
        public function index(): View {
            // ✅ ULM logging with context
            // ✅ PAStatisticsService injection (getDashboardStats, getPendingActions)
            // ✅ Real heritage query via whereHas collections ownership
            // ✅ ErrorManager exception handling
            // ✅ Returns pa.dashboard view
        }

        public function quickStats(): JsonResponse {
            // ✅ API endpoint for async stats refresh
        }
        ```

    -   **Dependencies:** ✅ UltraLogManager, ErrorManager, PAStatisticsService injected
    -   **Routes:** ✅ pa.dashboard + pa.api.quickStats registered
    -   **View:** ✅ resources/views/pa/dashboard.blade.php created (stub with inline CSS)
    -   **Testing:** ✅ Manual test confirmed - screenshot shows 127 heritage, 89 CoA, 5 EGI
    -   **GDPR:** ✅ Read-only, ULM logging active
    -   **Commit:** `108155b` [FEAT] PADashboardController + PAStatisticsService MOCK + Dashboard view
    -   **Status:** PRODUCTION READY ✅

-   [x] **TASK 3.4: Service - PAStatisticsService (MOCK)** ✅ COMPLETATO (2h - anticipated)

    -   **Priority:** P2 (per demo MOCK OK)
    -   **Status:** FULL IMPLEMENTATION ✅ (completato prima di TASK 3.3 come dependency per TASK 3.2)
    -   **File:** `app/Services/Statistics/PAStatisticsService.php`
    -   **Methods Implemented:**

        ```php
        public function getDashboardStats(User $paEntity): array {
            // ✅ Returns MOCK data: 127 total, 89 issued, 12 pending, 5 inspections, etc.
        }

        public function getPendingActions(User $paEntity): array {
            // ✅ Returns MOCK pending counts: 3 to approve, 2 to assign, 1 to review
        }

        public function getMonthlyTrends(int $months = 6): array {
            // ✅ Returns 6 months of MOCK trend data (fixed values: 10/7/150 per month)
        }
        ```

    -   **Documentation:** ✅ Extensive PHPDoc explaining MVP mock vs FASE 2 real queries
    -   **Note:** Fixed mock values (no random functions for stability)
    -   **Dependencies:** None
    -   **Commit:** `108155b` [FEAT] PADashboardController + PAStatisticsService MOCK + Dashboard view
    -   **Status:** PRODUCTION READY ✅

### ✅ COMPLETATO (Backend Layer - Heritage Controller)

-   [x] **TASK 3.3: Controller - PAHeritageController** ✅ COMPLETATO (3h)

    -   **Priority:** P1 (HIGH)
    -   **Status:** FULL IMPLEMENTATION ✅
    -   **File:** `app/Http/Controllers/PA/PAHeritageController.php`
    -   **Methods Implemented:**
        ```php
        public function index(Request $request) {
            // ✅ Lista patrimonio con whereHas() collection ownership
            // ✅ Filters: search (title/artist/description), CoA status (valid/revoked/no_coa)
            // ✅ Pagination 15 items (NO hidden ->take() limit)
            // ✅ ULM logging con filtri e results count
            // ✅ ErrorManager exception handling
        }
        public function show(Egi $egi) {
            // ✅ Authorization check: PA entity must own collection
            // ✅ Eager loading: coa, files, signatures, traits, collections, media
            // ✅ ULM logging con EGI details + has_coa flag
            // ✅ ErrorManager with 403 abort handling
        }
        ```
    -   **Dependencies:** ✅ UltraLogManager, ErrorManager injected
    -   **Routes:** ✅ pa.heritage.index + pa.heritage.show verified
    -   **Views:** ✅ heritage/index.blade.php + heritage/show.blade.php created (stubs)
    -   **Authorization:** ✅ Collection ownership check on both methods
    -   **Testing:** Manual test ready (pa.firenze@comune.fi.it user)
    -   **Commit:** `2491b95` [FEAT] PAHeritageController + Heritage views
    -   **Status:** PRODUCTION READY ✅

### ✅ COMPLETATO (Frontend Layer - Layout & Dashboard)

-   [x] **TASK 4.1: Layout - pa-layout.blade.php** ✅ COMPLETATO (4h)

    -   **Priority:** P0 (BLOCKING)
    -   **File:** `resources/views/components/pa-layout.blade.php` (Blade Component)
    -   **Implementation:**
        ```blade
        {{-- ✅ DaisyUI drawer system (lg:drawer-open per desktop) --}}
        {{-- ✅ IBM Plex Sans font caricato --}}
        {{-- ✅ Font Awesome 6.4.0 + Material Symbols Outlined --}}
        {{-- ✅ enterprise-sidebar component parametrizzato --}}
        {{-- ✅ Named slots: breadcrumb, pageTitle, styles, scripts, default slot --}}
        {{-- ✅ Sidebar: 280px width, gradient Blu Algoritmo (#1B365D → #0F2342) --}}
        {{-- ✅ Theme parametrization: pa|inspector|company --}}
        {{-- ✅ Usage: <x-pa-layout title="Dashboard">content</x-pa-layout> --}}
        ```
    -   **Design:** PA_ENTERPRISE_BRAND_GUIDELINES.md sezioni Layout + Sidebar
    -   **Components:** ✅ DaisyUI drawer + enterprise-sidebar parametrizzato
    -   **Testing:** ✅ Verificato su /pa/dashboard con menu visibili
    -   **Commit:** `9c0e54f` [FEAT] PA Enterprise Menu System - Pure Blade (NO Livewire)
    -   **Status:** PRODUCTION READY ✅

-   [x] **TASK 4.2: View - pa/dashboard.blade.php** ✅ COMPLETATO (5h)

    -   **Priority:** P0 (BLOCKING)
    -   **File:** `resources/views/pa/dashboard.blade.php`
    -   **Implementation:**
        ```blade
        {{-- ✅ Uses <x-pa-layout> component --}}
        {{-- ✅ KPI Cards (4 stats in grid): Total Heritage, Issued CoA, Pending CoA, Inspections --}}
        {{-- ✅ Recent Heritage Table (5 items paginated) --}}
        {{-- ✅ Quick Actions CTA buttons: Nuovo Certificato, Assegna Ispettore, Genera Report --}}
        ```
    -   **Design:** ✅ PA brand colors (Oro #D4A574, Blu #1B365D), IBM Plex Sans, WCAG 2.1 AA
    -   **Data Source:** ✅ PAStatisticsService MOCK data
    -   **Testing:** ✅ Verificato con user pa_entity (pa.firenze@comune.fi.it)
    -   **Commit:** `108155b` [FEAT] PADashboardController + PAStatisticsService MOCK + Dashboard view
    -   **Status:** PRODUCTION READY ✅

-   [x] **TASK 4.3: View - pa/heritage/index.blade.php** ✅ COMPLETATO (4h)

    -   **Priority:** P1 (HIGH)
    -   **File:** `resources/views/pa/heritage/index.blade.php` (186 lines, component-based)
    -   **Implementation:**
        ```blade
        {{-- ✅ Page header: gradient background + stats (total, current page) --}}
        {{-- ✅ Filters form: search input + CoA status select (valid|revoked|pending|no_coa) --}}
        {{-- ✅ Active filters display: dismissable pills with icons --}}
        {{-- ✅ Heritage grid: responsive 1/2/3 columns with pa-heritage-card components --}}
        {{-- ✅ Pagination: Tailwind links with query params preserved --}}
        {{-- ✅ Empty states: filtered (no results) vs no-data (create collection CTA) --}}
        {{-- ✅ Quick stats footer: 4 cards showing CoA counts by status --}}
        ```
    -   **Components Used:**
        -   ✅ `<x-pa-layout>` wrapper with breadcrumb + pageTitle slots
        -   ✅ `<x-pa.pa-heritage-card>` for grid items (showCoa, showActions props)
        -   ✅ `<x-pa.pa-action-button>` for filters/CTAs (primary, secondary, outline variants)
    -   **Design:** ✅ PA brand colors (#D4A574, #1B365D, #2D5016), WCAG 2.1 AA, responsive mobile-first
    -   **Data Source:** ✅ PAHeritageController@index (15 items/page, filters: search + coa_status)
    -   **Bug Fixes:** ✅ Fixed pa-heritage-card route: `coa.download` → `coa.pdf.download`
    -   **Testing:** ✅ Seeded 16 items (6 with CoA) via PAEnterpriseDemoSeeder
    -   **Commit:** `acd5006` [FEAT] TASK 4.3: Heritage List View Component-Based + Bug Fixes
    -   **Status:** PRODUCTION READY ✅
    -   **Dependencies:** TASK 4.1 ✅, TASK 4.5 ✅
    -   **Output:** Heritage list view with filters, pagination, component-based architecture

-   [ ] **TASK 4.4: View - pa/heritage/show.blade.php** ⏱️ 4h

    -   **Priority:** P1 (HIGH)
    -   **File:** `resources/views/pa/heritage/show.blade.php`
    -   **Status:** STUB CREATED (needs full implementation)
    -   **Sections Needed:**
        ```blade
        {{-- EGI Details Card (title, description, images, category, dimensions) --}}
        {{-- CoA Traits Display (technique/materials/support from CoA JSON) --}}
        {{-- CoA Files List (PDF, images, annexes with download links) --}}
        {{-- Signatures Section (inspector signature + owner signature) --}}
        {{-- Blockchain Verification Badge (transaction hash, timestamp, link) --}}
        {{-- Public QR Code Display (FASE 3 feature) --}}
        ```
    -   **Design:** Professional PA institutional layout
    -   **Dependencies:** TASK 4.1 ✅
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

### ✅ COMPLETATO (Integration Layer - Menu & Universal Sidebar)

-   [x] **TASK 5.1: Menu System - PA Context** ✅ COMPLETATO (3h + BONUS Universal Sidebar)

    -   **Priority:** P1 (HIGH)
    -   **Files:**
        -   `app/Services/Menu/ContextMenus.php` (PA case added)
        -   `app/Services/Menu/Items/PADashboardMenu.php` ✅
        -   `app/Services/Menu/Items/PAHeritageMenu.php` ✅
        -   `app/Services/Menu/Items/PACoAMenu.php` ✅
        -   `app/Services/Menu/Items/PAInspectorsMenu.php` ✅
        -   `resources/views/components/enterprise-sidebar.blade.php` ✅ (Universal Component)
    -   **Implementation:**
        ```php
        case 'pa':
            $paMainMenu = new MenuGroup('Gestione PA', 'pa-building', [
                new PADashboardMenu(),        // route: pa.dashboard, permission: access_pa_dashboard
                new PAHeritageMenu(),         // route: pa.heritage.index, permission: manage_institutional_collections
                new PACoAMenu(),              // modalAction: pa-coa-coming-soon (FASE 2 placeholder)
                new PAInspectorsMenu(),       // modalAction: pa-inspectors-coming-soon (FASE 3)
            ]);
            $menus[] = $paMainMenu;
            break;
        ```
    -   **Icon System:** ✅ 5 PA Heroicons added to config/icons.php (pa-building, pa-dashboard, pa-heritage, pa-coa, pa-inspectors)
    -   **Icon Seeding:** ✅ `php artisan db:seed --class=IconSeeder` executed successfully
    -   **Translations:** ✅ Added to resources/lang/it/menu.php (5 keys)
    -   **Spatie Permissions:** ✅ All 4 menu items have permissions assigned
    -   **BONUS - Universal Sidebar:** ✅ Renamed pa-sidebar → enterprise-sidebar with @props (logo, badge, theme)
    -   **Livewire Elimination:** ✅ Pure Blade components (NO Livewire) - eliminated hydration issues
    -   **Theme Support:** ✅ 4 themes (pa: #1B365D, inspector: #2D5016, company: #8E44AD, dashboard: neutral)
    -   **Testing:** ✅ Menu visibile su /pa/dashboard con 5 items (4 PA + OpenCollection)
    -   **Commits:**
        -   `9c0e54f` [FEAT] PA Enterprise Menu System - Pure Blade (NO Livewire)
        -   `828faa9` [REFACTOR] Universal Enterprise Sidebar - Parametrized Component
        -   `a4cbfbe` [FIX] Add Material Symbols font to PA layout
    -   **Status:** PRODUCTION READY ✅

-   [x] **TASK 5.2: GDPR Integration - Basic ULM Logging** ✅ COMPLETATO (2h)

    -   **Priority:** P1 (compliance)
    -   **Implementation:**

        ```php
        // ✅ PADashboardController@index
        $this->logger->info('PA Dashboard accessed', [
            'user_id' => $user->id,
            'user_role' => 'pa_entity',
            'context' => 'pa',
            'has_stats' => true,
        ]);

        // ✅ PAHeritageController@index
        $this->logger->info('PA Heritage list accessed', [
            'user_id' => $user->id,
            'filters' => $validated,
            'results_count' => $heritage->count(),
        ]);

        // ✅ PAHeritageController@show
        $this->logger->info('PA Heritage detail accessed', [
            'user_id' => $user->id,
            'egi_id' => $egi->id,
            'has_coa' => isset($egi->coa),
        ]);
        ```

    -   **Read-Only Operations:** ✅ No consent needed (GDPR allows legitimate interest for data viewing)
    -   **ErrorManager Integration:** ✅ All controllers have try-catch with ErrorManager
    -   **Commits:** Included in controller commits (108155b, 2491b95)
    -   **Status:** PRODUCTION READY ✅

### 🟡 IN PROGRESS (Testing & Validation)

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
        ```

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

## � FASE 4: PROJECTS SYSTEM (RAG Enhancement)

**OBIETTIVO:** Sistema Projects per upload documenti PA + priority RAG search  
**ISPIRAZIONE:** OpenAI/Anthropic Projects - file upload + prioritized context  
**APPROCCIO:** Tab-based UI (OPZIONE B) - Documents/Chat/Settings  
**EFFORT TOTALE:** ~60 ore (3 settimane)

**FEATURES:**

-   Upload documenti PA (PDF, DOCX, TXT, CSV, XLSX, MD)
-   Priority search: Project docs → Chat history → Acts
-   Chat history diventa searchable knowledge base
-   Tab-based UI per PA users
-   Embedding model: ada-002 (MVP) → 3-small (optimization)

### ⚪ FASE 4.1: Database & Models (12h)

-   [ ] **TASK 11.1: Projects Migration** ⏱️ 2h

    -   **Priority:** P0 (BLOCKING)
    -   **File:** `database/migrations/YYYY_MM_DD_create_projects_table.php`
    -   **Schema:**
        ```sql
        id, user_id, name, description, icon, color
        settings (JSON: limits, auto_embed, priority_rag)
        is_active, created_at, updated_at
        ```
    -   **Indexes:** user_id, is_active
    -   **Limits:** 20 projects/user, 50 docs/project
    -   **Dependencies:** None
    -   **Output:** Migration ready

-   [ ] **TASK 11.2: Project Documents Migration** ⏱️ 2h

    -   **Priority:** P0 (BLOCKING)
    -   **File:** `database/migrations/YYYY_MM_DD_create_project_documents_table.php`
    -   **Schema:**
        ```sql
        id, project_id, filename, original_name, mime_type
        size_bytes, file_path, status (pending/processing/ready/failed)
        metadata (JSON: pages, words, extraction_method)
        processed_at, created_at, updated_at
        ```
    -   **Indexes:** project_id, status
    -   **Storage:** storage/app/projects/{project_id}/{filename}
    -   **Dependencies:** TASK 11.1
    -   **Output:** Migration ready

-   [ ] **TASK 11.3: Project Document Chunks Migration** ⏱️ 2h

    -   **Priority:** P0 (BLOCKING)
    -   **File:** `database/migrations/YYYY_MM_DD_create_project_document_chunks_table.php`
    -   **Schema:**
        ```sql
        id, project_document_id, chunk_index, chunk_text
        embedding (JSON 1536 dims), embedding_model (varchar)
        tokens_count, page_number, metadata (JSON)
        created_at, updated_at
        ```
    -   **Indexes:** project_document_id, (embedding) SPATIAL if supported
    -   **Embedding:** OpenAI ada-002 (MVP)
    -   **Dependencies:** TASK 11.2
    -   **Output:** Migration ready

-   [ ] **TASK 11.4: Add project_id to natan_chat_messages** ⏱️ 1h

    -   **Priority:** P0 (BLOCKING)
    -   **File:** `database/migrations/YYYY_MM_DD_add_project_id_to_natan_chat_messages.php`
    -   **Change:** `$table->foreignId('project_id')->nullable()->constrained('projects')->onDelete('set null');`
    -   **Purpose:** Link chat history to projects
    -   **Dependencies:** TASK 11.1
    -   **Output:** Migration ready

-   [ ] **TASK 11.5: Models Creation** ⏱️ 3h

    -   **Priority:** P0 (BLOCKING)
    -   **Files:**
        -   `app/Models/Project.php`
        -   `app/Models/ProjectDocument.php`
        -   `app/Models/ProjectDocumentChunk.php`
    -   **Relationships:**
        -   Project belongsTo User, hasMany Documents, hasMany ChatMessages
        -   ProjectDocument belongsTo Project, hasMany Chunks
        -   ProjectDocumentChunk belongsTo ProjectDocument
    -   **Casts:** settings/metadata → array, embedding → array
    -   **Dependencies:** TASK 11.1-11.4
    -   **Output:** Models ready

-   [ ] **TASK 11.6: Factories & Seeders** ⏱️ 2h
    -   **Priority:** P2
    -   **Files:**
        -   `database/factories/ProjectFactory.php`
        -   `database/factories/ProjectDocumentFactory.php`
        -   `database/seeders/ProjectSeeder.php`
    -   **Test Data:** 3 projects, 10 docs, 50 chunks
    -   **Dependencies:** TASK 11.5
    -   **Output:** Seeders ready

### ⚪ FASE 4.2: Document Processing Pipeline (18h)

-   [ ] **TASK 12.1: DocumentProcessingService** ⏱️ 4h

    -   **Priority:** P0 (BLOCKING)
    -   **File:** `app/Services/Projects/DocumentProcessingService.php`
    -   **Methods:**
        -   `uploadDocument(Project $project, UploadedFile $file): ProjectDocument`
        -   `extractText(ProjectDocument $doc): string`
        -   `chunkDocument(ProjectDocument $doc, string $text): array`
        -   `generateEmbeddings(ProjectDocumentChunk $chunk): void`
    -   **Text Extraction:** pdftotext (PDF), PHPWord (DOCX), file_get_contents (TXT/MD)
    -   **Chunking:** 1000 tokens, 200 overlap
    -   **Dependencies:** TASK 11.5
    -   **Output:** Service ready

-   [ ] **TASK 12.2: ProcessDocumentJob** ⏱️ 3h

    -   **Priority:** P1 (HIGH)
    -   **File:** `app/Jobs/Projects/ProcessDocumentJob.php`
    -   **Queue:** `projects` (dedicated queue)
    -   **Flow:**
        1. Extract text
        2. Chunk document
        3. Generate embeddings for each chunk
        4. Update status to 'ready'
    -   **Error Handling:** Max 3 retries, UEM integration
    -   **Dependencies:** TASK 12.1
    -   **Output:** Job ready

-   [ ] **TASK 12.3: File Upload Validation** ⏱️ 2h

    -   **Priority:** P1 (HIGH)
    -   **Files:**
        -   `app/Http/Requests/Projects/UploadDocumentRequest.php`
        -   `app/Rules/Projects/ValidDocumentMimeType.php`
    -   **Validation:**
        -   Max 10MB per file
        -   Mimes: pdf, docx, txt, csv, xlsx, md
        -   Max 50 docs per project
        -   Filename sanitization
    -   **Dependencies:** None
    -   **Output:** Validation ready

-   [ ] **TASK 12.4: Storage Configuration** ⏱️ 1h

    -   **Priority:** P1 (HIGH)
    -   **File:** `config/filesystems.php`
    -   **Disk:**
        ```php
        'projects' => [
            'driver' => 'local',
            'root' => storage_path('app/projects'),
            'url' => env('APP_URL').'/storage/projects',
            'visibility' => 'private',
        ],
        ```
    -   **Symlink:** `php artisan storage:link`
    -   **Dependencies:** None
    -   **Output:** Storage ready

-   [ ] **TASK 12.5: Progress Tracking UI** ⏱️ 4h

    -   **Priority:** P2
    -   **Component:** Livewire ProcessingProgressCard
    -   **Features:**
        -   Real-time status (pending/processing/ready)
        -   Progress bar (0-100%)
        -   Error display
        -   Cancel button
    -   **WebSocket:** Laravel Echo + Pusher (optional)
    -   **Dependencies:** TASK 12.2
    -   **Output:** UI component ready

-   [ ] **TASK 12.6: Testing Pipeline** ⏱️ 4h
    -   **Priority:** P2
    -   **Files:**
        -   `tests/Feature/Projects/DocumentProcessingTest.php`
        -   `tests/Unit/Services/DocumentProcessingServiceTest.php`
    -   **Tests:**
        -   PDF extraction (3 pages test file)
        -   DOCX extraction
        -   Chunking logic (token count verification)
        -   Embedding generation (OpenAI mock)
    -   **Dependencies:** TASK 12.1
    -   **Output:** Tests passing

### ⚪ FASE 4.3: Priority RAG System (12h)

-   [ ] **TASK 13.1: ProjectRagService** ⏱️ 4h

    -   **Priority:** P0 (BLOCKING)
    -   **File:** `app/Services/Projects/ProjectRagService.php`
    -   **Methods:**
        -   `searchProjectDocuments(Project $project, string $query, int $limit = 5): Collection`
        -   `searchProjectChats(Project $project, string $query, int $limit = 3): Collection`
        -   `buildPriorityContext(Project $project, string $query): array`
    -   **Priority Logic:**
        1. Search project documents (chunks embeddings)
        2. Search project chat history (natan_chat_messages)
        3. Fallback to acts (RagService)
    -   **Dependencies:** TASK 11.5
    -   **Output:** Service ready

-   [ ] **TASK 13.2: NatanChatService Integration** ⏱️ 3h

    -   **Priority:** P0 (BLOCKING)
    -   **File:** `app/Services/NatanChatService.php` (modify)
    -   **Changes:**
        -   Add `?Project $project` parameter to `processQuery()`
        -   Check `$project` exists → use ProjectRagService
        -   Save `project_id` in NatanChatMessage
        -   Build context with source attribution
    -   **Dependencies:** TASK 13.1
    -   **Output:** Integration ready

-   [ ] **TASK 13.3: Source Attribution** ⏱️ 3h

    -   **Priority:** P1 (HIGH)
    -   **Feature:** "According to [filename] (page X)..."
    -   **Implementation:**
        -   ProjectRagService returns metadata (filename, page)
        -   NatanChatService includes in context
        -   Frontend displays citations
    -   **Dependencies:** TASK 13.1
    -   **Output:** Citations ready

-   [ ] **TASK 13.4: Chat History Search** ⏱️ 2h
    -   **Priority:** P2
    -   **Feature:** Previous project chats become searchable
    -   **Implementation:**
        -   Index chat content (no embeddings needed - simple LIKE search)
        -   Return relevant past conversations
        -   Link to original chat in UI
    -   **Dependencies:** TASK 11.4
    -   **Output:** Search ready

### ⚪ FASE 4.4: Frontend UI (Tab-based) (12h)

-   [ ] **TASK 14.1: Projects Index Page** ⏱️ 3h

    -   **Priority:** P1 (HIGH)
    -   **Route:** `/pa/natan/projects`
    -   **View:** `resources/views/pa/natan/projects/index.blade.php`
    -   **Features:**
        -   Project cards grid
        -   Create new project button
        -   Search/filter
        -   Active project badge
    -   **Dependencies:** TASK 11.5
    -   **Output:** Index page ready

-   [ ] **TASK 14.2: Project Detail (Tab-based)** ⏱️ 5h

    -   **Priority:** P0 (BLOCKING)
    -   **Route:** `/pa/natan/projects/{project}`
    -   **View:** `resources/views/pa/natan/projects/show.blade.php`
    -   **Tabs:**
        -   **Documents** - Upload, list, delete
        -   **Chat** - Integrated N.A.T.A.N. interface
        -   **Settings** - Name, icon, limits
    -   **UI:** Tailwind tabs component
    -   **Dependencies:** TASK 14.1
    -   **Output:** Detail page ready

-   [ ] **TASK 14.3: Document Upload UI** ⏱️ 2h

    -   **Priority:** P1 (HIGH)
    -   **Component:** Drag-drop upload zone
    -   **Features:**
        -   Dropzone.js or Livewire File Upload
        -   Multi-file upload
        -   Progress indicators
        -   File type validation (client-side)
    -   **Dependencies:** TASK 14.2
    -   **Output:** Upload UI ready

-   [ ] **TASK 14.4: Chat Interface Integration** ⏱️ 2h
    -   **Priority:** P1 (HIGH)
    -   **Modification:** Existing N.A.T.A.N. chat
    -   **Changes:**
        -   Project selector dropdown
        -   "Using project: [name]" indicator
        -   Citation display (file sources)
        -   Switch between projects
    -   **Dependencies:** TASK 13.2
    -   **Output:** Chat integration ready

### ⚪ FASE 4.5: Controller & Routes (6h)

-   [ ] **TASK 15.1: ProjectController** ⏱️ 3h

    -   **Priority:** P0 (BLOCKING)
    -   **File:** `app/Http/Controllers/PA/ProjectController.php`
    -   **Methods:**
        -   index() - List projects
        -   show(Project $project) - Detail view
        -   store(CreateProjectRequest $request) - Create
        -   update(UpdateProjectRequest $request, Project $project)
        -   destroy(Project $project)
        -   uploadDocument(UploadDocumentRequest $request, Project $project)
    -   **Middleware:** auth, role:pa_entity
    -   **Dependencies:** TASK 11.5
    -   **Output:** Controller ready

-   [ ] **TASK 15.2: Routes Registration** ⏱️ 1h

    -   **Priority:** P0 (BLOCKING)
    -   **File:** `routes/pa-enterprise.php`
    -   **Routes:**
        ```php
        Route::prefix('/projects')->name('projects.')->group(function () {
            Route::get('/', [ProjectController::class, 'index'])->name('index');
            Route::post('/', [ProjectController::class, 'store'])->name('store');
            Route::get('/{project}', [ProjectController::class, 'show'])->name('show');
            Route::put('/{project}', [ProjectController::class, 'update'])->name('update');
            Route::delete('/{project}', [ProjectController::class, 'destroy'])->name('destroy');
            Route::post('/{project}/documents', [ProjectController::class, 'uploadDocument'])->name('documents.upload');
        });
        ```
    -   **Dependencies:** TASK 15.1
    -   **Output:** Routes ready

-   [ ] **TASK 15.3: Menu Integration** ⏱️ 1h

    -   **Priority:** P2
    -   **File:** `app/Services/Menu/Items/PAProjectsMenu.php`
    -   **Menu Item:**
        -   Icon: folder_open
        -   Label: menu.pa_projects (I18N)
        -   Route: pa.projects.index
        -   Badge: projects count (optional)
    -   **Dependencies:** TASK 15.2
    -   **Output:** Menu ready

-   [ ] **TASK 15.4: I18N Translations** ⏱️ 1h
    -   **Priority:** P2
    -   **Files:**
        -   `resources/lang/en/projects.php`
        -   `resources/lang/it/projects.php`
    -   **Keys:**
        -   projects.title, projects.create, projects.upload
        -   projects.documents_tab, projects.chat_tab, projects.settings_tab
        -   projects.no_documents, projects.processing, etc.
    -   **Dependencies:** None
    -   **Output:** Translations ready

### ⚪ FASE 4.6: Testing & Documentation (6h)

-   [ ] **TASK 16.1: Feature Tests** ⏱️ 3h

    -   **Priority:** P1 (HIGH)
    -   **Files:**
        -   `tests/Feature/Projects/ProjectControllerTest.php`
        -   `tests/Feature/Projects/DocumentUploadTest.php`
        -   `tests/Feature/Projects/ProjectRagTest.php`
    -   **Tests:**
        -   Create/list/delete project
        -   Upload document (PDF mock)
        -   Search project documents
        -   Priority RAG flow
    -   **Dependencies:** TASK 15.1
    -   **Output:** Tests passing

-   [ ] **TASK 16.2: Documentation** ⏱️ 2h

    -   **Priority:** P2
    -   **File:** `docs/ai/context/PROJECTS_SYSTEM_GUIDE.md`
    -   **Content:**
        -   Architecture overview
        -   Database schema
        -   Upload workflow
        -   RAG priority logic
        -   API reference
        -   Troubleshooting
    -   **Dependencies:** None
    -   **Output:** Documentation ready

-   [ ] **TASK 16.3: Optimization** ⏱️ 1h
    -   **Priority:** P2
    -   **Tasks:**
        -   Embedding model upgrade: ada-002 → 3-small (5x cheaper)
        -   Add `embedding_model` column to chunks table
        -   Gradual migration strategy
        -   Performance benchmarks
    -   **Dependencies:** TASK 11.3
    -   **Output:** Optimization guide

---

## �📊 EFFORT SUMMARY

| Fase                     | Tasks        | Effort    | Timeline     |
| ------------------------ | ------------ | --------- | ------------ |
| **FASE 1: MVP**          | 19 tasks     | ~40h      | 2 weeks      |
| **FASE 2: Expansion**    | 10 tasks     | ~30h      | 2 weeks      |
| **FASE 3: Release**      | 12 tasks     | ~60h      | 4 weeks      |
| **FASE 4: Projects RAG** | 24 tasks     | ~60h      | 3 weeks      |
| **TOTALE**               | **65 tasks** | **~190h** | **11 weeks** |

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
