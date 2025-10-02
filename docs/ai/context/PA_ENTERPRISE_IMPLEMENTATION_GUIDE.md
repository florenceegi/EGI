# PA/ENTERPRISE SYSTEM - IMPLEMENTATION GUIDE

**Version:** 1.0.0  
**Target:** MVP (Approccio A) + Release Finale  
**Purpose:** Code patterns, examples, testing checklist

---

## 📋 TABLE OF CONTENTS

1. [Database Schema](#database-schema)
2. [Controller Patterns](#controller-patterns)
3. [Service Patterns](#service-patterns)
4. [View Patterns](#view-patterns)
5. [Routes Structure](#routes-structure)
6. [Menu System](#menu-system)
7. [Component Library](#component-library)
8. [GDPR Integration](#gdpr-integration)
9. [Testing Checklist](#testing-checklist)
10. [Common Pitfalls](#common-pitfalls)

---

## 🗄️ DATABASE SCHEMA

### Collections Table Extensions

```php
// Migration: database/migrations/YYYY_MM_DD_add_pa_enterprise_to_collections.php

public function up(): void
{
    Schema::table('collections', function (Blueprint $table) {
        // 1. ADD metadata JSON for PA/Enterprise-specific data
        $table->json('metadata')->nullable()->after('featured_position');

        // Index for faster queries
        $table->index(['type', 'owner_id']);
    });

    // 2. EXPAND type field VARCHAR(25) → VARCHAR(50)
    DB::statement('ALTER TABLE collections MODIFY type VARCHAR(50)');
}

public function down(): void
{
    Schema::table('collections', function (Blueprint $table) {
        $table->dropColumn('metadata');
    });

    DB::statement('ALTER TABLE collections MODIFY type VARCHAR(25)');
}
```

### Collections.metadata JSON Structure

```json
{
    "pa_entity_code": "C_H501",
    "institution_name": "Comune di Firenze",
    "department": "Ufficio Cultura e Patrimonio",
    "contact_email": "cultura@comune.fi.it",
    "contact_phone": "+39 055 1234567",
    "heritage_type": "monumentale",
    "classification": "bene_culturale_immobile",
    "unesco_status": false,
    "public_access": true,
    "notes": "Patrimonio comunale gestito da assessorato cultura"
}
```

### Collections.type New Values

```php
// Valori esistenti: 'artwork', 'collection', 'nft_collection'
// Valori PA/Enterprise (usare esistenti + metadata per MVP):

'pa_heritage'       // Patrimonio culturale PA (statue, monumenti)
'pa_documents'      // Documenti storici/archivistici PA
'company_products'  // Prodotti aziendali certificati
'company_catalog'   // Catalogo prodotti azienda
```

**MVP Approach:** Usa `type='artwork'` + `metadata.heritage_type='monumentale'` per evitare migration complessa.

### collection_user Pivot Usage (GIÀ ESISTE)

```php
// Schema esistente - RIUSARE!
Schema::create('collection_user', function (Blueprint $table) {
    $table->foreignId('collection_id')->constrained()->cascadeOnDelete();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->string('role')->nullable(); // Spatie role name
    $table->boolean('is_owner')->default(false);
    $table->string('status')->default('pending'); // pending, active, removed
    $table->json('metadata')->nullable();
    $table->timestamp('joined_at')->nullable();
    $table->timestamp('removed_at')->nullable();
});

// Usage per Inspector assignment:
$collection->users()->attach($inspector->id, [
    'role' => 'inspector',
    'is_owner' => false,
    'status' => 'active',
    'metadata' => [
        'assigned_by' => Auth::id(),
        'assignment_date' => now(),
        'specialization' => 'scultura_monumentale',
        'compensation' => 500.00,
    ],
    'joined_at' => now(),
]);
```

### CoA Tables Verification

```bash
# Verificare schema esistente
php artisan tinker --execute="Schema::getColumnListing('coa');"
php artisan tinker --execute="Schema::getColumnListing('coa_files');"
php artisan tinker --execute="Schema::getColumnListing('coa_signatures');"
```

**Expected schema:**

-   `coa.issuer_type` ENUM o JSON metadata
-   `coa.serial` VARCHAR univoco
-   `coa.status` (draft, issued, verified, revoked)
-   `coa_files.kind` (certificate_pdf, inspection_report, annex_photo)
-   `coa_signatures.signer_id` FK users (per inspector)

---

## 🎮 CONTROLLER PATTERNS

### PADashboardController - Complete Example

```php
<?php

namespace App\Http\Controllers\PA;

use App\Http\Controllers\Controller;
use App\Services\Statistics\PAStatisticsService;
use App\Models\{Egi, Collection};
use Illuminate\Http\{Request, RedirectResponse};
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * @package App\Http\Controllers\PA
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - PA/Enterprise MVP)
 * @date 2025-10-02
 * @purpose Dashboard per Pubblica Amministrazione - statistiche e accesso rapido patrimonio
 */
class PADashboardController extends Controller
{
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;
    protected PAStatisticsService $statsService;

    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        PAStatisticsService $statsService
    ) {
        $this->middleware(['auth', 'role:pa_entity']);
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->statsService = $statsService;
    }

    /**
     * Dashboard principale PA - KPI e accessi rapidi
     */
    public function index(): View
    {
        try {
            $user = Auth::user();

            // ULM: Log accesso dashboard
            $this->logger->info('PA Dashboard accessed', [
                'user_id' => $user->id,
                'user_role' => 'pa_entity',
                'timestamp' => now(),
            ]);

            // Statistics (MOCK per MVP, real in FASE 2)
            $stats = $this->statsService->getDashboardStats($user);

            // Recent heritage (REAL data)
            $recentHeritage = Egi::whereHas('collections', function ($query) use ($user) {
                $query->where('collections.owner_id', $user->id)
                      ->where(function ($q) {
                          $q->where('collections.type', 'artwork') // MVP: usa artwork
                            ->orWhereJsonContains('collections.metadata->heritage_type', 'monumentale');
                      });
            })
            ->with(['coa', 'collections.owner'])
            ->latest()
            ->take(5)
            ->get();

            // Pending actions (CoA da approvare, inspector da assegnare)
            $pendingActions = $this->statsService->getPendingActions($user);

            return view('pa.dashboard', compact('stats', 'recentHeritage', 'pendingActions'));

        } catch (\Exception $e) {
            // UEM: Error handling
            $this->errorManager->handle('PA_DASHBOARD_ERROR', [
                'user_id' => Auth::id(),
                'context' => 'Dashboard access failed',
            ], $e);

            return redirect()->back()->withErrors([
                'error' => 'Impossibile caricare la dashboard. Riprova tra poco.'
            ]);
        }
    }

    /**
     * Quick stats API endpoint (per refresh async)
     */
    public function quickStats(): JsonResponse
    {
        $user = Auth::user();
        $stats = $this->statsService->getDashboardStats($user);

        return response()->json($stats);
    }
}
```

### PAHeritageController - Lista e Dettaglio

```php
<?php

namespace App\Http\Controllers\PA;

use App\Http\Controllers\Controller;
use App\Models\{Egi, Collection};
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\{Auth, Gate};
use Ultra\UltraLogManager\UltraLogManager;

/**
 * @package App\Http\Controllers\PA
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - PA Heritage Management)
 * @date 2025-10-02
 * @purpose Gestione patrimonio culturale PA - lista, dettaglio, CoA
 */
class PAHeritageController extends Controller
{
    protected UltraLogManager $logger;

    public function __construct(UltraLogManager $logger)
    {
        $this->middleware(['auth', 'role:pa_entity']);
        $this->logger = $logger;
    }

    /**
     * Lista patrimonio culturale dell'ente
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        // Query builder con filters
        $query = Egi::whereHas('collections', function ($q) use ($user) {
            $q->where('collections.owner_id', $user->id);
        })->with(['coa', 'collections', 'media']);

        // Filters (search, status, etc.)
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('artist', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('coa_status')) {
            $query->whereHas('coa', function ($q) use ($status) {
                $q->where('status', $status);
            });
        }

        // Pagination (NO ->take() hidden limit!)
        $heritage = $query->orderBy('created_at', 'desc')->paginate(15);

        // ULM: Log access
        $this->logger->info('PA Heritage list accessed', [
            'user_id' => $user->id,
            'filters' => $request->only(['search', 'coa_status']),
            'results_count' => $heritage->count(),
        ]);

        return view('pa.heritage.index', compact('heritage'));
    }

    /**
     * Dettaglio singolo bene patrimoniale + CoA
     */
    public function show(Egi $egi): View
    {
        $user = Auth::user();

        // Authorization: user must own collection containing this EGI
        $ownsCollection = $egi->collections()
            ->where('owner_id', $user->id)
            ->exists();

        if (!$ownsCollection) {
            abort(403, 'Non hai accesso a questo bene patrimoniale.');
        }

        // Eager load relationships
        $egi->load([
            'coa.files',
            'coa.signatures.signer',
            'coa.events',
            'coaTraits', // CoA traits (technique/materials/support)
            'collections.owner',
            'media',
        ]);

        // ULM: Log detail access
        $this->logger->info('PA Heritage detail accessed', [
            'user_id' => $user->id,
            'egi_id' => $egi->id,
            'egi_title' => $egi->title,
        ]);

        return view('pa.heritage.show', compact('egi'));
    }
}
```

---

## 🛠️ SERVICE PATTERNS

### PAStatisticsService - MOCK per MVP

```php
<?php

namespace App\Services\Statistics;

use App\Models\User;
use Illuminate\Support\Collection;

/**
 * @package App\Services\Statistics
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - PA Statistics MVP)
 * @date 2025-10-02
 * @purpose Statistiche dashboard PA - MOCK per MVP, real data in FASE 2
 */
class PAStatisticsService
{
    /**
     * Dashboard KPI - MOCK data per MVP
     *
     * FASE 2: sostituire con query reali
     */
    public function getDashboardStats(User $paEntity): array
    {
        // MVP: Return MOCK data
        // POST-MVP: Query real data from DB

        return [
            'total_heritage' => 127,
            'coa_issued' => 89,
            'coa_pending' => 12,
            'coa_draft' => 8,
            'inspections_active' => 5,
            'blockchain_verifications' => 89,
            'public_visibility' => 76,
            'last_update' => now()->subHours(2),
        ];
    }

    /**
     * Azioni pendenti (CoA da approvare, inspector da assegnare)
     */
    public function getPendingActions(User $paEntity): array
    {
        // MVP: MOCK
        return [
            'coa_to_approve' => 3,
            'inspections_to_assign' => 2,
            'reports_to_review' => 4,
        ];
    }

    /**
     * FASE 2: Real data implementation example
     */
    public function getDashboardStatsReal(User $paEntity): array
    {
        // IMPORTANTE: NO ->take() o ->limit() senza parametro esplicito!
        // Vedere REGOLA STATISTICS in copilot-instructions.md

        $totalHeritage = \DB::table('egis')
            ->join('collection_egi', 'egis.id', '=', 'collection_egi.egi_id')
            ->join('collections', 'collection_egi.collection_id', '=', 'collections.id')
            ->where('collections.owner_id', $paEntity->id)
            ->count();

        $coaIssued = \DB::table('coa')
            ->join('egis', 'coa.egi_id', '=', 'egis.id')
            ->join('collection_egi', 'egis.id', '=', 'collection_egi.egi_id')
            ->join('collections', 'collection_egi.collection_id', '=', 'collections.id')
            ->where('collections.owner_id', $paEntity->id)
            ->where('coa.status', 'issued')
            ->count();

        // ... altri stats reali

        return [
            'total_heritage' => $totalHeritage,
            'coa_issued' => $coaIssued,
            // ... altri campi
        ];
    }
}
```

### CoAInspectionService - FASE 3

```php
<?php

namespace App\Services\CoA;

use App\Models\{Collection, User, Coa, CoaFile, CoaSignature};
use App\Services\Gdpr\{AuditLogService, ConsentService};
use App\Enums\Gdpr\GdprActivityCategory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\{DB, Storage};

/**
 * @package App\Services\CoA
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Inspector Workflow)
 * @date 2025-10-02
 * @purpose Gestione workflow ispettori - assignment, report, firma CoA
 */
class CoAInspectionService
{
    protected AuditLogService $auditService;
    protected ConsentService $consentService;

    public function __construct(
        AuditLogService $auditService,
        ConsentService $consentService
    ) {
        $this->auditService = $auditService;
        $this->consentService = $consentService;
    }

    /**
     * Assegna inspector a una collection via pivot table
     */
    public function assignInspector(
        Collection $collection,
        User $inspector,
        User $assignedBy,
        array $metadata = []
    ): void {
        // Verify inspector has role
        if (!$inspector->hasRole('inspector')) {
            throw new \InvalidArgumentException('User is not an inspector');
        }

        // GDPR: Check consent for inspector access
        if (!$this->consentService->hasConsent($assignedBy, 'allow-inspector-access')) {
            throw new \Exception('Missing consent for inspector assignment');
        }

        // Attach inspector via collection_user pivot
        $collection->users()->attach($inspector->id, [
            'role' => 'inspector',
            'is_owner' => false,
            'status' => 'active',
            'metadata' => array_merge([
                'assigned_by' => $assignedBy->id,
                'assignment_date' => now(),
                'specialization' => $metadata['specialization'] ?? null,
                'compensation' => $metadata['compensation'] ?? null,
            ], $metadata),
            'joined_at' => now(),
        ]);

        // GDPR: Audit log
        $this->auditService->logActivity(
            $assignedBy,
            GdprActivityCategory::INSPECTOR_ASSIGNMENT,
            "Inspector {$inspector->name} assigned to collection {$collection->collection_name}",
            [
                'collection_id' => $collection->id,
                'inspector_id' => $inspector->id,
                'metadata' => $metadata,
            ]
        );
    }

    /**
     * Upload inspection report PDF
     */
    public function uploadReport(Coa $coa, UploadedFile $report, User $inspector): CoaFile
    {
        // Verify inspector is assigned
        $isAssigned = $coa->egi->collections()
            ->whereHas('users', function ($q) use ($inspector) {
                $q->where('users.id', $inspector->id)
                  ->where('collection_user.role', 'inspector');
            })->exists();

        if (!$isAssigned) {
            throw new \Exception('Inspector not assigned to this CoA');
        }

        // Store file
        $path = $report->store('coa/inspection-reports', 's3');

        // Create CoaFile record
        $coaFile = CoaFile::create([
            'coa_id' => $coa->id,
            'kind' => 'inspection_report',
            'file_path' => $path,
            'file_name' => $report->getClientOriginalName(),
            'file_size' => $report->getSize(),
            'mime_type' => $report->getMimeType(),
            'uploaded_by' => $inspector->id,
        ]);

        // GDPR: Audit log
        $this->auditService->logActivity(
            $inspector,
            GdprActivityCategory::COA_FILE_UPLOAD,
            "Inspection report uploaded for CoA {$coa->serial}",
            ['coa_id' => $coa->id, 'file_id' => $coaFile->id]
        );

        return $coaFile;
    }

    /**
     * Firma digitale CoA da parte inspector
     */
    public function signCoA(Coa $coa, User $inspector, string $signatureData): CoaSignature
    {
        // Verify inspector permission
        if (!$inspector->can('sign_coa')) {
            throw new \Exception('Inspector lacks sign_coa permission');
        }

        DB::beginTransaction();
        try {
            // Create signature record
            $signature = CoaSignature::create([
                'coa_id' => $coa->id,
                'signer_id' => $inspector->id,
                'signature_type' => 'inspector_digital',
                'signature_data' => $signatureData, // Hash o encrypted data
                'signed_at' => now(),
            ]);

            // Update CoA status
            $coa->update(['status' => 'verified']);

            // GDPR: Audit log
            $this->auditService->logActivity(
                $inspector,
                GdprActivityCategory::COA_SIGNATURE,
                "CoA {$coa->serial} signed by inspector {$inspector->name}",
                ['coa_id' => $coa->id, 'signature_id' => $signature->id]
            );

            DB::commit();
            return $signature;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
```

---

## 🎨 VIEW PATTERNS

### pa-layout.blade.php - Layout Master

```blade
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'FlorenceEGI') }} - PA Dashboard</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    {{-- Styles --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --pa-blue-primary: #1B365D;
            --pa-blue-dark: #0F1F3D;
            --pa-gold-accent: #B89968;
            --pa-gray-light: #F8F9FA;
            --pa-gray-border: #E5E7EB;
        }

        body {
            font-family: 'IBM Plex Sans', sans-serif;
        }

        .pa-sidebar {
            background: var(--pa-blue-primary);
            width: 280px;
            min-height: 100vh;
        }

        .pa-main {
            margin-left: 280px;
            background: var(--pa-gray-light);
            min-height: 100vh;
        }
    </style>
</head>
<body class="antialiased">
    <div class="flex">
        {{-- Sidebar Istituzionale --}}
        <aside class="pa-sidebar fixed top-0 left-0 h-screen overflow-y-auto">
            {{-- Logo PA Entity --}}
            <div class="p-6 border-b border-white/10">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-white/10 rounded-lg flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                            {{-- Icon PA --}}
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-white font-semibold text-lg">{{ Auth::user()->name }}</h2>
                        <p class="text-white/70 text-sm">Ente Pubblico</p>
                    </div>
                </div>
            </div>

            {{-- Menu Navigation --}}
            <nav class="p-4 space-y-2">
                @foreach($menuItems ?? [] as $item)
                    <a href="{{ route($item->route) }}"
                       class="flex items-center px-4 py-3 text-white/80 hover:bg-white/10 rounded-lg transition
                              {{ request()->routeIs($item->route) ? 'bg-white/10 text-white' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24">
                            {{-- Icon dinamico --}}
                        </svg>
                        {{ $item->label }}
                    </a>
                @endforeach
            </nav>

            {{-- Footer Sidebar --}}
            <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-white/10">
                <p class="text-white/50 text-xs text-center">
                    FlorenceEGI PA Portal<br>
                    v1.0.0 - {{ now()->format('Y') }}
                </p>
            </div>
        </aside>

        {{-- Main Content Area --}}
        <main class="pa-main">
            {{-- Header --}}
            <header class="bg-white border-b border-gray-200 px-8 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-800">
                            @yield('page-title', 'Dashboard')
                        </h1>
                        <p class="text-gray-500 text-sm mt-1">
                            @yield('page-description', 'Gestione patrimonio culturale')
                        </p>
                    </div>

                    {{-- User Actions --}}
                    <div class="flex items-center space-x-4">
                        <button class="p-2 text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        </button>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-gray-600 hover:text-gray-800 text-sm">
                                Esci
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            {{-- Content --}}
            <div class="p-8">
                @if(session('success'))
                    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-green-800">
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg text-red-800">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>
```

### pa/dashboard.blade.php - Dashboard View

```blade
@extends('layouts.pa-layout')

@section('page-title', 'Dashboard Ente Pubblico')
@section('page-description', 'Panoramica patrimonio culturale e CoA emessi')

@section('content')
    {{-- KPI Cards Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <x-pa-stat-card
            title="Patrimonio Totale"
            :value="$stats['total_heritage']"
            icon="collection"
            color="blue"
        />

        <x-pa-stat-card
            title="CoA Emessi"
            :value="$stats['coa_issued']"
            icon="certificate"
            color="green"
        />

        <x-pa-stat-card
            title="CoA Pendenti"
            :value="$stats['coa_pending']"
            icon="clock"
            color="yellow"
        />

        <x-pa-stat-card
            title="Ispettori Attivi"
            :value="$stats['inspections_active']"
            icon="user-check"
            color="purple"
        />
    </div>

    {{-- Recent Heritage Table --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Patrimonio Recente</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Opera
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Collezione
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Stato CoA
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Data
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Azioni
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($recentHeritage as $egi)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <img src="{{ $egi->getFirstMediaUrl('images') ?: asset('images/placeholder.png') }}"
                                         alt="{{ $egi->title }}"
                                         class="w-12 h-12 rounded object-cover">
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $egi->title }}</div>
                                        <div class="text-sm text-gray-500">{{ $egi->artist }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $egi->collections->first()->collection_name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <x-pa-coa-badge :status="$egi->coa->status ?? 'none'" />
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $egi->created_at->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('pa.heritage.show', $egi) }}"
                                   class="text-blue-600 hover:text-blue-900">
                                    Visualizza
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                Nessun patrimonio registrato
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <x-pa-action-button
            title="Nuovo Patrimonio"
            description="Aggiungi un nuovo bene culturale"
            icon="plus"
            :route="route('pa.heritage.create')"
        />

        <x-pa-action-button
            title="Gestisci Ispettori"
            description="Assegna ispettori alle collezioni"
            icon="users"
            :route="route('pa.inspectors.index')"
        />

        <x-pa-action-button
            title="Statistiche Avanzate"
            description="Report dettagliati e analytics"
            icon="chart"
            :route="route('pa.statistics')"
        />
    </div>
@endsection
```

---

## 🛣️ ROUTES STRUCTURE

```php
// routes/pa-enterprise.php

use App\Http\Controllers\PA\{PADashboardController, PAHeritageController, PACoAController};
use App\Http\Controllers\Inspector\InspectorController;
use App\Http\Controllers\Company\{CompanyDashboardController, CompanyProductController};

/*
|--------------------------------------------------------------------------
| PA/Enterprise Routes
|--------------------------------------------------------------------------
|
| Routes per Pubblica Amministrazione, Ispettori, Aziende.
| Middleware: auth + Spatie role checks
|
*/

// PA Entity Routes
Route::prefix('pa')
    ->middleware(['auth', 'role:pa_entity'])
    ->name('pa.')
    ->group(function () {
        Route::get('/dashboard', [PADashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/stats', [PADashboardController::class, 'quickStats'])->name('dashboard.stats');

        Route::resource('heritage', PAHeritageController::class)->only(['index', 'show']);
        Route::resource('coa', PACoAController::class)->only(['index', 'show']);

        Route::get('/inspectors', [PADashboardController::class, 'inspectors'])->name('inspectors.index');
        Route::post('/inspectors/assign', [PADashboardController::class, 'assignInspector'])->name('inspectors.assign');
    });

// Inspector Routes
Route::prefix('inspector')
    ->middleware(['auth', 'role:inspector'])
    ->name('inspector.')
    ->group(function () {
        Route::get('/dashboard', [InspectorController::class, 'dashboard'])->name('dashboard');
        Route::get('/assignments', [InspectorController::class, 'assignments'])->name('assignments');
        Route::get('/coa/{coa}/review', [InspectorController::class, 'reviewCoA'])->name('coa.review');
        Route::post('/coa/{coa}/report', [InspectorController::class, 'uploadReport'])->name('coa.report');
        Route::post('/coa/{coa}/sign', [InspectorController::class, 'signCoA'])->name('coa.sign');
    });

// Company Routes
Route::prefix('company')
    ->middleware(['auth', 'role:company'])
    ->name('company.')
    ->group(function () {
        Route::get('/dashboard', [CompanyDashboardController::class, 'index'])->name('dashboard');
        Route::resource('products', CompanyProductController::class);
        Route::get('/products/{egi}/qr', [CompanyProductController::class, 'generateQR'])->name('products.qr');
    });

// Public CoA Verification (NO auth)
Route::get('/public/coa/verify/{uuid}', [PACoAController::class, 'publicVerify'])
    ->name('public.coa.verify');
```

**Registrazione in `bootstrap/app.php` o `routes/web.php`:**

```php
// In routes/web.php
require __DIR__.'/pa-enterprise.php';
```

---

## 📱 MENU SYSTEM

### ContextMenus.php Extension

```php
// app/Services/Menu/ContextMenus.php

public static function getMenuForContext(string $context): array
{
    return match ($context) {
        'pa_dashboard' => [
            new MenuItem('Dashboard', 'pa.dashboard', 'dashboard', 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'),
            new MenuItem('Patrimonio', 'pa.heritage.index', 'collection', 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10'),
            new MenuItem('CoA Emessi', 'pa.coa.index', 'certificate', 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'),
            new MenuItem('Ispettori', 'pa.inspectors.index', 'users', 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'),
        ],

        'inspector_dashboard' => [
            new MenuItem('Dashboard', 'inspector.dashboard', 'dashboard', '...'),
            new MenuItem('Assegnazioni', 'inspector.assignments', 'clipboard-list', '...'),
            new MenuItem('CoA da Firmare', 'inspector.coa.pending', 'pen-tool', '...'),
        ],

        'company_dashboard' => [
            new MenuItem('Dashboard', 'company.dashboard', 'dashboard', '...'),
            new MenuItem('Prodotti', 'company.products.index', 'shopping-bag', '...'),
            new MenuItem('QR Codes', 'company.qr.index', 'qr-code', '...'),
        ],

        default => parent::getMenuForContext($context),
    };
}
```

**MenuItem Class:**

```php
// app/Services/Menu/MenuItem.php

namespace App\Services\Menu;

class MenuItem
{
    public function __construct(
        public string $label,
        public string $route,
        public string $icon,
        public string $iconPath = '',
        public array $permissions = []
    ) {}

    public function isActive(): bool
    {
        return request()->routeIs($this->route . '*');
    }

    public function canAccess(): bool
    {
        if (empty($this->permissions)) {
            return true;
        }

        return auth()->user()?->hasAnyPermission($this->permissions) ?? false;
    }
}
```

---

## 🧩 COMPONENT LIBRARY

### pa-stat-card.blade.php

```blade
@props(['title', 'value', 'icon', 'color' => 'blue'])

@php
$colorClasses = [
    'blue' => 'bg-blue-50 text-blue-600',
    'green' => 'bg-green-50 text-green-600',
    'yellow' => 'bg-yellow-50 text-yellow-600',
    'purple' => 'bg-purple-50 text-purple-600',
];
@endphp

<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-gray-600">{{ $title }}</p>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $value }}</p>
        </div>

        <div class="w-12 h-12 rounded-lg {{ $colorClasses[$color] }} flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                {{ $slot }}
            </svg>
        </div>
    </div>
</div>
```

### pa-coa-badge.blade.php

```blade
@props(['status'])

@php
$badges = [
    'issued' => ['text' => 'Emesso', 'class' => 'bg-green-100 text-green-800'],
    'verified' => ['text' => 'Verificato', 'class' => 'bg-blue-100 text-blue-800'],
    'pending' => ['text' => 'Pendente', 'class' => 'bg-yellow-100 text-yellow-800'],
    'draft' => ['text' => 'Bozza', 'class' => 'bg-gray-100 text-gray-800'],
    'revoked' => ['text' => 'Revocato', 'class' => 'bg-red-100 text-red-800'],
    'none' => ['text' => 'Nessun CoA', 'class' => 'bg-gray-100 text-gray-500'],
];

$badge = $badges[$status] ?? $badges['none'];
@endphp

<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badge['class'] }}">
    {{ $badge['text'] }}
</span>
```

### pa-action-button.blade.php

```blade
@props(['title', 'description', 'icon', 'route'])

<a href="{{ $route }}" class="block bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:border-blue-300 hover:shadow-md transition">
    <div class="flex items-start space-x-4">
        <div class="w-12 h-12 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                {{ $slot }}
            </svg>
        </div>

        <div class="flex-1">
            <h3 class="text-lg font-semibold text-gray-900">{{ $title }}</h3>
            <p class="text-sm text-gray-500 mt-1">{{ $description }}</p>
        </div>

        <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
    </div>
</a>
```

---

## 🔒 GDPR INTEGRATION

### Controller GDPR Pattern

```php
use Ultra\UltraLogManager\UltraLogManager;
use App\Services\Gdpr\{AuditLogService, ConsentService};
use App\Enums\Gdpr\GdprActivityCategory;

class PAHeritageController extends Controller
{
    protected UltraLogManager $logger;
    protected AuditLogService $auditService;
    protected ConsentService $consentService;

    public function __construct(/* dependency injection */)
    {
        $this->middleware('auth');
    }

    public function show(Egi $egi): View
    {
        $user = Auth::user();

        // ULM: Log access (read-only, no consent needed)
        $this->logger->info('PA Heritage detail accessed', [
            'user_id' => $user->id,
            'egi_id' => $egi->id,
        ]);

        // ... resto logica
    }

    public function update(Request $request, Egi $egi): RedirectResponse
    {
        try {
            $user = Auth::user();
            $validated = $request->validate([...]);

            // 1. ULM: Log start
            $this->logger->info('PA Heritage update initiated', [
                'user_id' => $user->id,
                'egi_id' => $egi->id,
            ]);

            // 2. GDPR: Check consent (se modifica dati sensibili)
            if (!$this->consentService->hasConsent($user, 'allow-institutional-data-processing')) {
                return redirect()->back()->withErrors([
                    'consent' => 'Consenso mancante per elaborazione dati istituzionali.'
                ]);
            }

            // 3. Update
            $egi->update($validated);

            // 4. GDPR: Audit trail
            $this->auditService->logActivity(
                $user,
                GdprActivityCategory::PA_HERITAGE_UPDATE,
                "Heritage {$egi->title} updated",
                ['egi_id' => $egi->id, 'fields' => array_keys($validated)]
            );

            // 5. ULM: Log success
            $this->logger->info('PA Heritage update completed', [
                'user_id' => $user->id,
                'egi_id' => $egi->id,
            ]);

            return redirect()->back()->with('success', 'Patrimonio aggiornato con successo.');

        } catch (\Exception $e) {
            // 6. UEM: Error handling
            $this->errorManager->handle('PA_HERITAGE_UPDATE_ERROR', [
                'user_id' => Auth::id(),
                'egi_id' => $egi->id,
            ], $e);

            return redirect()->back()->withErrors(['error' => 'Aggiornamento fallito.']);
        }
    }
}
```

### GDPR Config Extensions

```php
// config/gdpr.php

'consent_types' => [
    // ... existing consents

    // PA-specific consents
    'allow-institutional-data-processing' => [
        'title' => 'Elaborazione Dati Istituzionali',
        'description' => 'Consenti l\'elaborazione dei dati del patrimonio culturale per finalità istituzionali.',
        'required' => true,
        'category' => 'institutional',
    ],

    'allow-inspector-access' => [
        'title' => 'Accesso Ispettori',
        'description' => 'Consenti agli ispettori assegnati di accedere ai dati del patrimonio per verifiche CoA.',
        'required' => true,
        'category' => 'institutional',
    ],

    'allow-public-coa-verification' => [
        'title' => 'Verifica Pubblica CoA',
        'description' => 'Consenti la verifica pubblica dei Certificati di Autenticità tramite QR code.',
        'required' => false,
        'category' => 'public',
    ],
],
```

---

## ✅ TESTING CHECKLIST

### Pre-Demo MVP Checklist

```
FUNCTIONALITY:
☐ PA user login → redirect to /pa/dashboard
☐ Dashboard renders with KPI stats (anche MOCK)
☐ Heritage list displays all owned EGI
☐ Heritage detail shows complete info + CoA
☐ CoA traits display (technique/materials/support)
☐ CoA PDF download works (if CoA exists)
☐ CoA status badges correct colors
☐ Menu navigation works (all links active)
☐ Search/filter heritage by title/artist
☐ Pagination works correctly

DATABASE:
☐ collections.metadata JSON field exists
☐ collections.type can store 'artwork' (MVP uses this)
☐ collection_user pivot queries work
☐ CoA relationships load correctly
☐ No N+1 queries (check query log)

AUTHORIZATION:
☐ PA user can only see own collections
☐ Non-PA user redirected from /pa/* routes
☐ Inspector cannot access /pa/* routes
☐ Role middleware working correctly

GDPR/LOGGING:
☐ ULM logs dashboard access
☐ ULM logs heritage detail access
☐ No UEM errors in logs
☐ Consent checks pass (read-only OK senza consent)

UI/UX:
☐ Palette istituzionale applicata (Blu #1B365D, Oro #B89968)
☐ Typography IBM Plex Sans caricata
☐ Sidebar 280px width corretto
☐ Layout responsive mobile/tablet
☐ No testi placeholder "Lorem ipsum"
☐ No immagini broken/placeholder visibili
☐ Loading states appropriati
☐ Hover states su buttons/links

ACCESSIBILITY:
☐ WCAG 2.1 AA compliance (test con axe DevTools)
☐ Contrasto colori sufficiente (min 4.5:1)
☐ Focus states visibili
☐ ARIA labels presenti dove necessario
☐ Keyboard navigation funzionante
☐ Screen reader friendly (test con NVDA/JAWS)

PERFORMANCE:
☐ Dashboard load time < 2s
☐ Heritage list load time < 3s
☐ No browser console errors
☐ No console warnings critiche
☐ Images optimized (< 500KB)
☐ CSS/JS bundle size ragionevole

DEMO MATERIALS:
☐ 5 high-res screenshots (dashboard, list, detail, CoA, mobile)
☐ 1 CoA PDF certificate example
☐ Demo data seeded (10+ heritage items)
☐ PA user credentials documented
☐ Demo script prepared (5min walkthrough)
```

### Feature Tests Example

```php
// tests/Feature/PA/PADashboardTest.php

namespace Tests\Feature\PA;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

class PADashboardTest extends TestCase
{
    use RefreshDatabase;

    protected User $paUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Create PA entity user
        $this->paUser = User::factory()->create();
        $paRole = Role::findByName('pa_entity');
        $this->paUser->assignRole($paRole);
    }

    /** @test */
    public function pa_user_can_access_dashboard()
    {
        $response = $this->actingAs($this->paUser)
            ->get(route('pa.dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('pa.dashboard');
        $response->assertViewHas(['stats', 'recentHeritage']);
    }

    /** @test */
    public function non_pa_user_cannot_access_dashboard()
    {
        $regularUser = User::factory()->create();

        $response = $this->actingAs($regularUser)
            ->get(route('pa.dashboard'));

        $response->assertStatus(403);
    }

    /** @test */
    public function dashboard_displays_correct_statistics()
    {
        $response = $this->actingAs($this->paUser)
            ->get(route('pa.dashboard'));

        $stats = $response->viewData('stats');

        $this->assertArrayHasKey('total_heritage', $stats);
        $this->assertArrayHasKey('coa_issued', $stats);
        $this->assertIsInt($stats['total_heritage']);
    }
}
```

---

## ⚠️ COMMON PITFALLS

### 1. ❌ STATISTICS SERVICE - Hidden Limits

```php
// ❌ SBAGLIATO - limite nascosto
public function getTopHeritage(): Collection
{
    return Egi::orderBy('views')->take(10)->get(); // NASCOSTO!
}

// ✅ CORRETTO - limite esplicito
public function getTopHeritage(?int $limit = null): Collection
{
    $query = Egi::orderBy('views');

    if ($limit !== null) {
        $query->limit($limit);
    }

    return $query->get(); // Default: tutti i record
}
```

### 2. ❌ COLLECTION_USER Pivot - Nome Sbagliato

```php
// ❌ SBAGLIATO - tabella non esiste
CollectionCollaborator::where(...)->get();

// ✅ CORRETTO - usa pivot table
$collection->users()->wherePivot('role', 'inspector')->get();

// ✅ CORRETTO - query diretta
DB::table('collection_user')
    ->where('role', 'inspector')
    ->where('status', 'active')
    ->get();
```

### 3. ❌ GDPR - Consent Check Mancante

```php
// ❌ SBAGLIATO - modifica senza consent check
public function update(Request $request, Egi $egi)
{
    $egi->update($request->validated());
    return redirect()->back();
}

// ✅ CORRETTO - consent + audit
public function update(Request $request, Egi $egi)
{
    $user = Auth::user();

    if (!$this->consentService->hasConsent($user, 'allow-institutional-data-processing')) {
        return redirect()->back()->withErrors(['consent' => 'Missing consent']);
    }

    $egi->update($request->validated());

    $this->auditService->logActivity($user, GdprActivityCategory::PA_HERITAGE_UPDATE, ...);

    return redirect()->back()->with('success', 'Updated');
}
```

### 4. ❌ ULM - Log Mancante

```php
// ❌ SBAGLIATO - no logging
public function index(): View
{
    $data = Egi::all();
    return view('pa.heritage.index', compact('data'));
}

// ✅ CORRETTO - log access
public function index(): View
{
    $this->logger->info('PA Heritage list accessed', [
        'user_id' => Auth::id(),
        'timestamp' => now(),
    ]);

    $data = Egi::all();
    return view('pa.heritage.index', compact('data'));
}
```

### 5. ❌ Authorization - Missing Check

```php
// ❌ SBAGLIATO - no ownership check
public function show(Egi $egi): View
{
    return view('pa.heritage.show', compact('egi'));
}

// ✅ CORRETTO - verify ownership
public function show(Egi $egi): View
{
    $user = Auth::user();

    $ownsCollection = $egi->collections()
        ->where('owner_id', $user->id)
        ->exists();

    if (!$ownsCollection) {
        abort(403, 'Non autorizzato');
    }

    return view('pa.heritage.show', compact('egi'));
}
```

### 6. ❌ N+1 Query Problem

```php
// ❌ SBAGLIATO - N+1 queries
$heritage = Egi::all(); // 1 query
foreach ($heritage as $egi) {
    echo $egi->coa->serial; // N queries!
}

// ✅ CORRETTO - eager loading
$heritage = Egi::with(['coa', 'collections', 'media'])->get(); // 4 queries totali
foreach ($heritage as $egi) {
    echo $egi->coa->serial; // No extra query
}
```

### 7. ❌ Metadata JSON - Type Confusion

```php
// ❌ SBAGLIATO - array non salvato come JSON
$collection->metadata = ['key' => 'value'];
$collection->save(); // Errore!

// ✅ CORRETTO - cast in model
// In Collection model:
protected $casts = ['metadata' => 'array'];

// Poi usage normale:
$collection->metadata = ['key' => 'value'];
$collection->save(); // OK!
```

---

## 🚀 READY TO CODE

**NEXT STEPS:**

1. Crea migration collections (TASK 2.1)
2. Seed demo data PA (TASK 2.3)
3. Implementa PADashboardController (TASK 3.2)
4. Layout pa-layout.blade.php (TASK 4.1)
5. Dashboard view (TASK 4.2)

**Reference this guide for:**

-   ✅ Code patterns e examples
-   ✅ Common pitfalls da evitare
-   ✅ Testing checklist pre-demo
-   ✅ GDPR integration patterns

**Ship it! 🚀**
