<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use App\Services\Gdpr\AuditLogService;
use App\Services\Padmin\PadminService;
use App\Enums\Gdpr\GdprActivityCategory;

/**
 * @package App\Http\Controllers\Superadmin
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 2.0.0 (FlorenceEGI - SuperAdmin Padmin Analyzer with Service Integration)
 * @date 2025-01-22
 * @purpose SuperAdmin interface for Padmin Analyzer (OS3 Guardian) management
 */
class PadminController extends Controller {
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;
    protected AuditLogService $auditLogService;
    protected PadminService $padminService;

    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        AuditLogService $auditLogService,
        PadminService $padminService
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->auditLogService = $auditLogService;
        $this->padminService = $padminService;
        $this->middleware(['auth', 'superadmin']);
    }
    /**
     * Padmin Analyzer - Dashboard with real-time stats
     */
    public function dashboard(Request $request): View|JsonResponse|RedirectResponse {
        try {
            $this->logger->info('[SuperAdmin] Padmin Dashboard accessed', [
                'admin_id' => auth()->id(),
                'ip' => $request->ip(),
                'ua' => substr((string)$request->userAgent(), 0, 255),
                'section' => 'dashboard',
            ]);

            // Get real-time stats from Padmin Analyzer
            $stats = $this->padminService->getViolationStats(auth()->user());
            $symbolCount = $this->padminService->getSymbolCount(auth()->user());
            $healthStatus = $this->padminService->getHealthStatus();

            // Log admin access
            $this->auditLogService->logUserAction(
                user: auth()->user(),
                action: 'superadmin_padmin_dashboard_access',
                context: [
                    'route' => 'superadmin.padmin.dashboard',
                    'stats_loaded' => true,
                    'redis_stack_status' => $healthStatus['redis_stack'] ? 'connected' : 'disconnected',
                ],
                category: GdprActivityCategory::SYSTEM_INTERACTION
            );

            return view('superadmin.padmin.dashboard', [
                'pageTitle' => 'Padmin Analyzer',
                'stats' => $stats,
                'symbolCount' => $symbolCount,
                'healthStatus' => $healthStatus,
            ]);
        } catch (\Exception $e) {
            $this->errorManager->handle('UNEXPECTED_ERROR', [
                'controller' => self::class,
                'method' => __METHOD__,
                'admin_id' => auth()->id(),
            ], $e);

            return redirect()->route('superadmin.dashboard');
        }
    }

    /**
     * Padmin Analyzer - Violations list with filters
     */
    public function violations(Request $request): View|JsonResponse|RedirectResponse {
        try {
            $this->logger->info('[SuperAdmin] Padmin Violations viewed', [
                'admin_id' => auth()->id(),
                'ip' => $request->ip(),
                'section' => 'violations',
                'filters' => $request->all(),
            ]);

            // Build filters from request
            $filters = [];
            if ($request->has('priority')) {
                $filters['priority'] = $request->input('priority');
            }
            if ($request->has('severity')) {
                $filters['severity'] = $request->input('severity');
            }
            if ($request->has('type')) {
                $filters['type'] = $request->input('type');
            }
            if ($request->has('isFixed')) {
                $filters['isFixed'] = (bool) $request->input('isFixed');
            }
            if ($request->has('limit')) {
                $filters['limit'] = (int) $request->input('limit');
            }

            // Get violations from Padmin Analyzer
            $violations = $this->padminService->getViolations($filters, auth()->user());

            // Log admin access
            $this->auditLogService->logUserAction(
                user: auth()->user(),
                action: 'superadmin_padmin_violations_access',
                context: ['filters' => $filters, 'result_count' => count($violations)],
                category: GdprActivityCategory::SYSTEM_INTERACTION
            );

            return view('superadmin.padmin.violations', [
                'pageTitle' => 'Violazioni Padmin',
                'violations' => $violations,
                'filters' => $filters,
            ]);
        } catch (\Exception $e) {
            $this->errorManager->handle('UNEXPECTED_ERROR', [
                'controller' => self::class,
                'method' => __METHOD__,
                'admin_id' => auth()->id(),
            ], $e);

            return redirect()->route('superadmin.dashboard');
        }
    }

    /**
     * Padmin Analyzer - Symbols registry
     */
    public function symbols(Request $request): View|JsonResponse|RedirectResponse {
        try {
            $this->logger->info('[SuperAdmin] Padmin Symbols viewed', [
                'admin_id' => auth()->id(),
                'ip' => $request->ip(),
                'section' => 'symbols',
            ]);

            // Build search query from request
            $query = [];
            if ($request->has('text')) {
                $query['text'] = $request->input('text');
            }
            if ($request->has('type')) {
                $query['type'] = $request->input('type');
            }
            if ($request->has('filePath')) {
                $query['filePath'] = $request->input('filePath');
            }
            if ($request->has('limit')) {
                $query['limit'] = (int) $request->input('limit');
            }

            // Get symbols from Padmin Analyzer
            $symbols = $this->padminService->searchSymbols($query, auth()->user());
            $symbolCount = $this->padminService->getSymbolCount(auth()->user());

            // Log admin access
            $this->auditLogService->logUserAction(
                user: auth()->user(),
                action: 'superadmin_padmin_symbols_access',
                context: ['query' => $query, 'result_count' => count($symbols)],
                category: GdprActivityCategory::SYSTEM_INTERACTION
            );

            return view('superadmin.padmin.symbols', [
                'pageTitle' => 'Simboli Padmin',
                'symbols' => $symbols,
                'symbolCount' => $symbolCount,
                'query' => $query,
            ]);
        } catch (\Exception $e) {
            $this->errorManager->handle('UNEXPECTED_ERROR', [
                'controller' => self::class,
                'method' => __METHOD__,
                'admin_id' => auth()->id(),
            ], $e);

            return redirect()->route('superadmin.dashboard');
        }
    }

    /**
     * Padmin Analyzer - Semantic search UI
     */
    public function search(Request $request): View|JsonResponse|RedirectResponse {
        try {
            $this->logger->info('[SuperAdmin] Padmin Search viewed', [
                'admin_id' => auth()->id(),
                'ip' => $request->ip(),
                'section' => 'search',
            ]);

            // Build advanced search query
            $query = [];
            $results = [];
            $searchPerformed = false;

            if ($request->has('q') && !empty($request->input('q'))) {
                $searchPerformed = true;

                // Multi-field search query
                if ($request->has('searchField')) {
                    $field = $request->input('searchField');
                    $query[$field] = $request->input('q');
                } else {
                    // Default: search in name
                    $query['text'] = $request->input('q');
                }

                // Additional filters
                if ($request->has('type') && !empty($request->input('type'))) {
                    $query['type'] = $request->input('type');
                }

                if ($request->has('filePath') && !empty($request->input('filePath'))) {
                    $query['filePath'] = $request->input('filePath');
                }

                if ($request->has('namespace') && !empty($request->input('namespace'))) {
                    $query['namespace'] = $request->input('namespace');
                }

                // Limit
                $query['limit'] = (int) $request->input('limit', 100);

                // Execute search
                $results = $this->padminService->searchSymbols($query, auth()->user());

                $this->logger->info('[SuperAdmin] Padmin semantic search executed', [
                    'admin_id' => auth()->id(),
                    'query' => $query,
                    'results_count' => count($results),
                ]);
            }

            // Log admin access (middleware 'auth' guarantees user is authenticated)
            $this->auditLogService->logUserAction(
                user: auth()->user(),
                action: 'superadmin_padmin_search_access',
                context: [
                    'query' => $query,
                    'results_count' => count($results),
                ],
                category: GdprActivityCategory::SYSTEM_INTERACTION
            );

            return view('superadmin.padmin.search', [
                'pageTitle' => 'Ricerca Semantica',
                'results' => $results,
                'query' => $query,
                'searchPerformed' => $searchPerformed,
            ]);
        } catch (\Exception $e) {
            $this->errorManager->handle('UNEXPECTED_ERROR', [
                'controller' => self::class,
                'method' => __METHOD__,
                'admin_id' => auth()->id(),
            ], $e);

            return redirect()->route('superadmin.dashboard');
        }
    }

    /**
     * Padmin Analyzer - Statistics UI
     */
    public function statistics(Request $request): View|JsonResponse|RedirectResponse {
        try {
            $this->logger->info('[SuperAdmin] Padmin Statistics viewed', [
                'admin_id' => auth()->id(),
                'ip' => $request->ip(),
                'section' => 'statistics',
            ]);

            // Log admin access (middleware 'auth' guarantees user is authenticated)
            $this->auditLogService->logUserAction(
                user: auth()->user(),
                action: 'superadmin_padmin_statistics_access',
                context: [],
                category: GdprActivityCategory::SYSTEM_INTERACTION
            );

            return view('superadmin.padmin.stats', [
                'pageTitle' => 'Statistiche Padmin',
            ]);
        } catch (\Exception $e) {
            $this->errorManager->handle('UNEXPECTED_ERROR', [
                'controller' => self::class,
                'method' => __METHOD__,
                'admin_id' => auth()->id(),
            ], $e);

            return redirect()->route('superadmin.dashboard');
        }
    }

    /**
     * API: Mark violation as fixed
     */
    public function markViolationFixed(Request $request, string $violationId): JsonResponse {
        try {
            $this->logger->info('[SuperAdmin] Marking violation as fixed', [
                'admin_id' => auth()->id(),
                'violation_id' => $violationId,
            ]);

            // Call Padmin service
            $success = $this->padminService->markViolationFixed($violationId, auth()->user());

            if (!$success) {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossibile marcare la violazione come risolta. Riprova.',
                ], 500);
            }

            $this->logger->info('[SuperAdmin] Violation marked as fixed successfully', [
                'admin_id' => auth()->id(),
                'violation_id' => $violationId,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Violazione marcata come risolta.',
            ]);
        } catch (\Exception $e) {
            $this->errorManager->handle('UNEXPECTED_ERROR', [
                'controller' => self::class,
                'method' => __METHOD__,
                'admin_id' => auth()->id(),
                'violation_id' => $violationId,
            ], $e);

            return response()->json([
                'success' => false,
                'message' => 'Errore durante l\'aggiornamento. Riprova più tardi.',
            ], 500);
        }
    }
}
