<?php

use App\Http\Controllers\PA\PADashboardController;
use App\Http\Controllers\PA\PAHeritageController;
use App\Http\Controllers\PA\NatanChatController;
use App\Http\Controllers\PA\PaEmbeddingsController;
use App\Http\Controllers\PA\ProjectController;
use App\Http\Controllers\PaActs\PaActUploadController;
use App\Http\Controllers\PaActs\PaActController;
use App\Http\Controllers\PaActs\PaActPublicController;
use App\Http\Controllers\PaActs\PaBatchSourceController;
use Illuminate\Support\Facades\Route;

/**
 * PA/ENTERPRISE ROUTES
 *
 * @package FlorenceEGI\Routes
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - PA/Enterprise System MVP)
 * @date 2025-10-02
 * @purpose Routes for PA Entity dashboard, heritage management, and CoA display
 *
 * Access Control:
 * - Middleware: auth (user must be logged in)
 * - Middleware: role:pa_entity (Spatie permission check)
 *
 * Route Structure:
 * - /pa/dashboard         → PA Dashboard with KPIs and quick actions
 * - /pa/heritage          → Heritage items list (Collection-owned EGI)
 * - /pa/heritage/{egi}    → Heritage item detail + CoA display
 *
 * Notes:
 * - All routes prefixed with /pa
 * - Uses named routes for easy reference (pa.dashboard, pa.heritage, etc.)
 * - Authorization checks in controllers (user must own collection)
 * - GDPR: ULM logging for all access (read-only, no consent needed)
 */

Route::prefix('pa')
    ->middleware(['auth', 'role:pa_entity'])
    ->name('pa.')
    ->group(function () {

        /**
         * PA DASHBOARD
         *
         * GET /pa/dashboard
         * Controller: PADashboardController@index
         *
         * Features:
         * - KPI cards (total heritage, CoA issued, inspections active)
         * - Recent CoA list
         * - Pending actions (CoA to approve, inspectors to assign)
         * - Quick actions (issue CoA, assign inspector)
         *
         * View: resources/views/pa/dashboard.blade.php
         * Layout: resources/views/layouts/pa-layout.blade.php
         */
        Route::get('/dashboard', [PADashboardController::class, 'index'])
            ->name('dashboard');

        /**
         * QUICK STATS API
         *
         * GET /pa/api/quick-stats
         * Controller: PADashboardController@quickStats
         *
         * JSON API endpoint for async stats refresh
         * Returns dashboard KPI without page reload
         * Useful for real-time updates via AJAX
         */
        Route::get('/api/quick-stats', [PADashboardController::class, 'quickStats'])
            ->name('api.quickStats');

        /**
         * HERITAGE MANAGEMENT
         *
         * GET /pa/heritage
         * Controller: PAHeritageController@index
         *
         * Features:
         * - Table of heritage items (EGI owned by PA entity)
         * - Filters: search, CoA status, creation date
         * - Sorting: title, creation_date, coa_status
         * - CoA badges (issued, pending, draft)
         * - Pagination: 20 per page
         *
         * View: resources/views/pa/heritage/index.blade.php
         */
        Route::get('/heritage', [PAHeritageController::class, 'index'])
            ->name('heritage.index');

        /**
         * HERITAGE DETAIL + CoA DISPLAY
         *
         * GET /pa/heritage/{egi}
         * Controller: PAHeritageController@show
         *
         * Features:
         * - EGI detail (title, description, creation_date, media)
         * - CoA display (serial, status, issued_at, verification_hash)
         * - CoA traits (technique, materials, support)
         * - Signatures (author, inspector)
         * - Blockchain verification badge
         * - Actions: download PDF, verify online, assign inspector
         *
         * Authorization: User must own collection containing this EGI
         *
         * View: resources/views/pa/heritage/show.blade.php
         */
        Route::get('/heritage/{egi}', [PAHeritageController::class, 'show'])
            ->name('heritage.show')
            ->where('egi', '[0-9]+'); // EGI ID must be numeric


        Route::prefix('/acts')->group(function () {
            Route::get('/', [PaActController::class, 'index'])->name('acts.index');
            Route::get('/statistics', [PaActController::class, 'statistics'])->name('acts.statistics'); // PA Statistics Dashboard
            Route::get('/egis', [App\Http\Controllers\EgiController::class, 'index'])->name('acts.egis.index');
            Route::get('/upload', [PaActUploadController::class, 'showUploadForm'])->name('acts.upload');
            Route::post('/upload', [PaActUploadController::class, 'handleUpload'])->name('acts.upload.post');
            Route::post('/{egi}/force-tokenize', [PaActController::class, 'forceTokenize'])->name('acts.force_tokenize');
            Route::get('/{egi}', [PaActController::class, 'show'])->name('acts.show');
        });

        /**
         * BATCH PROCESSING MANAGEMENT
         *
         * Routes for managing batch sources (monitored directories)
         * and viewing processing jobs status
         *
         * Features:
         * - CRUD operations for batch sources
         * - Job monitoring and status tracking
         * - Toggle source active/paused status
         */
        Route::prefix('/batch')->name('batch.')->group(function () {
            Route::get('/', [PaBatchSourceController::class, 'index'])->name('index');
            Route::get('/create', [PaBatchSourceController::class, 'create'])->name('create');
            Route::post('/', [PaBatchSourceController::class, 'store'])->name('store');
            Route::get('/{id}', [PaBatchSourceController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [PaBatchSourceController::class, 'edit'])->name('edit');
            Route::put('/{id}', [PaBatchSourceController::class, 'update'])->name('update');
            Route::delete('/{id}', [PaBatchSourceController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/toggle-status', [PaBatchSourceController::class, 'toggleStatus'])->name('toggle_status');
        });

        /**
         * N.A.T.A.N. CHAT AI
         *
         * GET  /pa/natan/chat         → Chat interface
         * POST /pa/natan/chat/message → Process message and return AI response
         * GET  /pa/natan/chat/suggestions → Get suggested questions
         *
         * Features:
         * - AI-powered conversational interface for PA acts
         * - RAG (Retrieval Augmented Generation) for relevant context
         * - Natural language queries about administrative acts
         * - Summarization, analysis, strategic insights
         *
         * Authorization: auth + role:pa_entity
         * GDPR: Local AI processing (Ollama), no external data transfer
         */
        Route::prefix('/natan')->name('natan.')->group(function () {
            Route::get('/chat', [NatanChatController::class, 'index'])->name('chat');
            Route::post('/chat/message', [NatanChatController::class, 'sendMessage'])->name('chat.message');
            Route::get('/chat/suggestions', [NatanChatController::class, 'getSuggestions'])->name('chat.suggestions');

            // Chat History (NEW v3.1 - GDPR-Compliant)
            Route::get('/chat/history', [NatanChatController::class, 'getHistory'])->name('chat.history');
            Route::get('/chat/session/{sessionId}', [NatanChatController::class, 'getSession'])->name('chat.session');
            Route::delete('/chat/session/{sessionId}', [NatanChatController::class, 'deleteSession'])->name('chat.session.delete');

            // Intelligent Chunking System (NEW v4.0)
            Route::post('/search-preview', [NatanChatController::class, 'searchPreview'])->name('search.preview');
            Route::post('/analyze', [NatanChatController::class, 'analyzeActs'])->name('analyze');
            Route::get('/chunking-progress/{sessionId}', [NatanChatController::class, 'getChunkingProgress'])->name('chunking.progress');
            Route::get('/chunking-final/{sessionId}', [NatanChatController::class, 'getChunkingFinal'])->name('chunking.final');

            // SSE Streaming for Real-Time Progress (NEW v6.0)
            Route::post('/analyze-stream', [NatanChatController::class, 'analyzeActsStream'])->name('analyze.stream');

            // AI Credits Cost Tracking (NEW v5.0)
            Route::post('/estimate-cost', [NatanChatController::class, 'estimateCost'])->name('estimate.cost');
        });

        /**
         * AI COSTS MONITORING DASHBOARD (NEW v3.2)
         *
         * GET  /pa/ai-costs              → Dashboard costi AI
         * GET  /pa/ai-costs/api/stats    → API statistiche spesa (JSON)
         * GET  /pa/ai-costs/api/trend    → API trend giornaliero (JSON)
         * POST /pa/ai-costs/api/budget   → Aggiorna budget settings
         *
         * Features:
         * - Monitoraggio costi AI real-time (Anthropic, OpenAI, Perplexity)
         * - Budget tracking per provider
         * - Alert automatici soglie budget
         * - Trend spesa ultimi 30 giorni
         * - Cost breakdown per model
         * - Monthly spending reports
         *
         * Authorization: auth + role:pa_entity
         * GDPR: Solo dati aggregati, no PII
         */
        Route::prefix('/ai-costs')->name('ai-costs.')->group(function () {
            Route::get('/', [\App\Http\Controllers\PA\AiCostsDashboardController::class, 'index'])->name('dashboard');
            Route::get('/api/stats', [\App\Http\Controllers\PA\AiCostsDashboardController::class, 'getStats'])->name('api.stats');
            Route::get('/api/trend', [\App\Http\Controllers\PA\AiCostsDashboardController::class, 'getTrend'])->name('api.trend');
            Route::post('/api/budget', [\App\Http\Controllers\PA\AiCostsDashboardController::class, 'updateBudget'])->name('api.budget');
            Route::get('/api/compare-billing', [\App\Http\Controllers\PA\AiCostsDashboardController::class, 'compareBilling'])->name('api.compare-billing');
        });

        /**
         * VECTOR EMBEDDINGS MANAGEMENT
         *
         * GET  /pa/embeddings             → Dashboard embeddings con statistiche
         * GET  /pa/embeddings/stats       → API statistiche (JSON)
         * POST /pa/embeddings/generate    → Genera embeddings batch
         * DELETE /pa/embeddings           → Elimina tutti gli embeddings
         *
         * Features:
         * - Generazione vector embeddings per semantic search
         * - Progress monitoring real-time
         * - Coverage statistics
         * - Cost estimation
         * - OpenAI integration
         *
         * Authorization: auth + role:pa_entity
         */
        Route::prefix('/embeddings')->name('embeddings.')->group(function () {
            Route::get('/', [PaEmbeddingsController::class, 'index'])->name('index');
            Route::get('/stats', [PaEmbeddingsController::class, 'stats'])->name('stats');
            Route::post('/generate', [PaEmbeddingsController::class, 'generate'])->name('generate');
            Route::delete('/', [PaEmbeddingsController::class, 'deleteAll'])->name('delete');
        });

        /**
         * WEB SCRAPERS - PA Acts Scraping Management
         *
         * GET  /pa/scrapers            → Lista scraper configurati
         * GET  /pa/scrapers/create     → Form nuovo scraper
         * POST /pa/scrapers            → Salva nuovo scraper
         * GET  /pa/scrapers/{scraper}  → Dettaglio scraper
         * GET  /pa/scrapers/{scraper}/edit → Form modifica
         * PUT  /pa/scrapers/{scraper}  → Aggiorna scraper
         * DELETE /pa/scrapers/{scraper} → Elimina scraper
         * POST /pa/scrapers/{scraper}/test → Test connessione
         * POST /pa/scrapers/{scraper}/run  → Esegui scraping manuale
         * POST /pa/scrapers/{scraper}/toggle → Attiva/Disattiva
         *
         * Features:
         * - Configurazione scraper via UI (URL, payload, headers, mapping)
         * - Test connessione API/HTML
         * - Esecuzione manuale o schedulata
         * - GDPR compliance: sanitizzazione PII, audit trail
         * - Template pre-configurati (Firenze, altri comuni)
         *
         * Authorization: auth + role:pa_entity + business scope
         */
        Route::prefix('web-scrapers')->name('web-scrapers.')->group(function () {
            Route::get('/', [\App\Http\Controllers\PaActs\PaWebScraperController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\PaActs\PaWebScraperController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\PaActs\PaWebScraperController::class, 'store'])->name('store');
            Route::get('/{scraper}', [\App\Http\Controllers\PaActs\PaWebScraperController::class, 'show'])->name('show');
            Route::get('/{scraper}/edit', [\App\Http\Controllers\PaActs\PaWebScraperController::class, 'edit'])->name('edit');
            Route::put('/{scraper}', [\App\Http\Controllers\PaActs\PaWebScraperController::class, 'update'])->name('update');
            Route::delete('/{scraper}', [\App\Http\Controllers\PaActs\PaWebScraperController::class, 'destroy'])->name('destroy');
            Route::post('/{scraper}/test', [\App\Http\Controllers\PaActs\PaWebScraperController::class, 'test'])->name('test');
            Route::post('/{scraper}/preview', [\App\Http\Controllers\PaActs\PaWebScraperController::class, 'preview'])->name('preview');
            Route::post('/{scraper}/run', [\App\Http\Controllers\PaActs\PaWebScraperController::class, 'run'])->name('run');
            Route::get('/{scraper}/progress', [\App\Http\Controllers\PaActs\PaWebScraperController::class, 'progress'])->name('progress');
            Route::post('/{scraper}/toggle', [\App\Http\Controllers\PaActs\PaWebScraperController::class, 'toggle'])->name('toggle');
        });

        // ALIAS: pa.scrapers.* → pa.web-scrapers.* (per compatibilità menu)
        Route::prefix('scrapers')->name('scrapers.')->group(function () {
            Route::get('/', [\App\Http\Controllers\PaActs\PaWebScraperController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\PaActs\PaWebScraperController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\PaActs\PaWebScraperController::class, 'store'])->name('store');
            Route::get('/{scraper}', [\App\Http\Controllers\PaActs\PaWebScraperController::class, 'show'])->name('show');
            Route::get('/{scraper}/edit', [\App\Http\Controllers\PaActs\PaWebScraperController::class, 'edit'])->name('edit');
            Route::put('/{scraper}', [\App\Http\Controllers\PaActs\PaWebScraperController::class, 'update'])->name('update');
            Route::delete('/{scraper}', [\App\Http\Controllers\PaActs\PaWebScraperController::class, 'destroy'])->name('destroy');
            Route::post('/{scraper}/test', [\App\Http\Controllers\PaActs\PaWebScraperController::class, 'test'])->name('test');
            Route::post('/{scraper}/preview', [\App\Http\Controllers\PaActs\PaWebScraperController::class, 'preview'])->name('preview');
            Route::post('/{scraper}/run', [\App\Http\Controllers\PaActs\PaWebScraperController::class, 'run'])->name('run');
            Route::get('/{scraper}/progress', [\App\Http\Controllers\PaActs\PaWebScraperController::class, 'progress'])->name('progress');
            Route::post('/{scraper}/toggle', [\App\Http\Controllers\PaActs\PaWebScraperController::class, 'toggle'])->name('toggle');
        });

        /**
         * PROJECTS SYSTEM (FASE 4)
         *
         * GET    /pa/projects           → Projects list
         * GET    /pa/projects/create    → Create project form
         * POST   /pa/projects           → Store new project
         * GET    /pa/projects/{project} → Project detail (tab-based UI)
         * GET    /pa/projects/{project}/edit → Edit project form
         * PUT    /pa/projects/{project} → Update project
         * DELETE /pa/projects/{project} → Delete project (soft)
         *
         * Features:
         * - Document upload for RAG
         * - Priority search (project docs > chat history > PA acts)
         * - Tab-based UI (Documents / Chat / Settings)
         * - GDPR audit trail for all operations
         */
        Route::resource('projects', ProjectController::class);

        // ✨ NEW v4.0 - Project Session Management (for chat context)
        Route::post('/projects/set-active', [ProjectController::class, 'setActive'])->name('projects.set-active');
        Route::post('/projects/remove-active', [ProjectController::class, 'removeActive'])->name('projects.remove-active');

        // ✨ NEW v4.0 - Document Upload (for Priority RAG)
        Route::post('/projects/{project}/documents/upload', [ProjectController::class, 'uploadDocument'])->name('projects.documents.upload');

        /**
         * FUTURE ROUTES (FASE 2-3)
         *
         * Commented out - implement in POST-MVP phases:
         *
         * // CoA Management
         * // Route::post('/heritage/{egi}/coa/issue', [PACoAController::class, 'issue'])->name('coa.issue');
         * // Route::get('/coa/{coa}', [PACoAController::class, 'show'])->name('coa.show');
         *
         * // Inspector Assignment
         * // Route::post('/heritage/{egi}/inspector/assign', [PAInspectorController::class, 'assign'])->name('inspector.assign');
         * // Route::get('/inspectors', [PAInspectorController::class, 'index'])->name('inspectors.index');
         *
         * // Statistics & Reports
         * // Route::get('/statistics', [PAStatisticsController::class, 'index'])->name('statistics');
         * // Route::get('/reports/heritage', [PAReportsController::class, 'heritage'])->name('reports.heritage');
         */
    });
