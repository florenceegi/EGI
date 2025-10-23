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

            // Get violations from session (temporary until Redis storage ready)
            // TODO: Use $this->padminService->getViolations($filters, auth()->user()) when Node.js CLI ready
            $allViolations = session('padmin_violations', []);
            
            // Apply filters
            $violations = $this->applyFilters($allViolations, $filters);

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
     * Apply filters to violations array
     */
    private function applyFilters(array $violations, array $filters): array
    {
        $filtered = $violations;
        
        if (isset($filters['priority'])) {
            $filtered = array_filter($filtered, fn($v) => ($v['severity'] ?? '') === $filters['priority']);
        }
        
        if (isset($filters['isFixed'])) {
            $filtered = array_filter($filtered, fn($v) => ($v['is_fixed'] ?? false) === $filters['isFixed']);
        }
        
        if (isset($filters['limit'])) {
            $filtered = array_slice($filtered, 0, $filters['limit']);
        }
        
        return array_values($filtered);
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

            // Get comprehensive stats
            $violationStats = $this->padminService->getViolationStats(auth()->user());
            $symbolCount = $this->padminService->getSymbolCount(auth()->user());

            // Get all violations for trend analysis
            $allViolations = $this->padminService->getViolations([], auth()->user());

            // Calculate additional metrics
            $totalViolations = $violationStats['total'];
            $fixedViolations = count(array_filter($allViolations, fn($v) => $v['isFixed'] ?? false));
            $fixRate = $totalViolations > 0 ? round(($fixedViolations / $totalViolations) * 100, 1) : 0;

            // Priority distribution percentages
            $priorityPercentages = [
                'P0' => $totalViolations > 0 ? round(($violationStats['byPriority']['P0'] / $totalViolations) * 100, 1) : 0,
                'P1' => $totalViolations > 0 ? round(($violationStats['byPriority']['P1'] / $totalViolations) * 100, 1) : 0,
                'P2' => $totalViolations > 0 ? round(($violationStats['byPriority']['P2'] / $totalViolations) * 100, 1) : 0,
                'P3' => $totalViolations > 0 ? round(($violationStats['byPriority']['P3'] / $totalViolations) * 100, 1) : 0,
            ];

            // Severity distribution percentages
            $severityPercentages = [
                'critical' => $totalViolations > 0 ? round(($violationStats['bySeverity']['critical'] / $totalViolations) * 100, 1) : 0,
                'error' => $totalViolations > 0 ? round(($violationStats['bySeverity']['error'] / $totalViolations) * 100, 1) : 0,
                'warning' => $totalViolations > 0 ? round(($violationStats['bySeverity']['warning'] / $totalViolations) * 100, 1) : 0,
                'info' => $totalViolations > 0 ? round(($violationStats['bySeverity']['info'] / $totalViolations) * 100, 1) : 0,
            ];

            // Group violations by type (top 5)
            $violationsByType = [];
            foreach ($allViolations as $violation) {
                $type = $violation['type'] ?? 'unknown';
                if (!isset($violationsByType[$type])) {
                    $violationsByType[$type] = 0;
                }
                $violationsByType[$type]++;
            }
            arsort($violationsByType);
            $topViolationTypes = array_slice($violationsByType, 0, 5, true);

            // Group violations by file (top 5 most problematic files)
            $violationsByFile = [];
            foreach ($allViolations as $violation) {
                $file = basename($violation['filePath'] ?? 'unknown');
                if (!isset($violationsByFile[$file])) {
                    $violationsByFile[$file] = 0;
                }
                $violationsByFile[$file]++;
            }
            arsort($violationsByFile);
            $topProblematicFiles = array_slice($violationsByFile, 0, 5, true);

            // Log admin access (middleware 'auth' guarantees user is authenticated)
            $this->auditLogService->logUserAction(
                user: auth()->user(),
                action: 'superadmin_padmin_statistics_access',
                context: [
                    'total_violations' => $totalViolations,
                    'symbol_count' => $symbolCount,
                    'fix_rate' => $fixRate,
                ],
                category: GdprActivityCategory::SYSTEM_INTERACTION
            );

            return view('superadmin.padmin.stats', [
                'pageTitle' => 'Statistiche Padmin',
                'violationStats' => $violationStats,
                'symbolCount' => $symbolCount,
                'totalViolations' => $totalViolations,
                'fixedViolations' => $fixedViolations,
                'fixRate' => $fixRate,
                'priorityPercentages' => $priorityPercentages,
                'severityPercentages' => $severityPercentages,
                'topViolationTypes' => $topViolationTypes,
                'topProblematicFiles' => $topProblematicFiles,
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

    /**
     * API: Run code quality scan
     */
    public function runScan(Request $request): JsonResponse {
        try {
            $validated = $request->validate([
                'path' => 'required|string',
                'rules' => 'nullable|array',
                'rules.*' => 'string',
                'store' => 'nullable|boolean',
            ]);

            $this->logger->info('[SuperAdmin] Running Padmin scan', [
                'admin_id' => auth()->id(),
                'path' => $validated['path'],
                'rules' => $validated['rules'] ?? [],
                'store' => $validated['store'] ?? false,
            ]);

            // Get rules array
            $rules = $validated['rules'] ?? [];

            // Inject RuleEngineService
            $ruleEngine = app(\App\Services\Padmin\RuleEngine\RuleEngineService::class);

            // Run scan
            $violations = $ruleEngine->scanDirectory(
                directory: base_path($validated['path']),
                ruleNames: $rules
            );

            $this->logger->info('[SuperAdmin] Scan completed', [
                'admin_id' => auth()->id(),
                'violations_found' => count($violations),
            ]);

            // Store violations in session if requested
            // TODO: Store in Redis via Node.js CLI when violations:create command is implemented
            if ($validated['store'] ?? false) {
                $existingViolations = session('padmin_violations', []);
                
                // Add scan metadata to each violation
                foreach ($violations as &$violation) {
                    $violation['id'] = uniqid('v_', true);
                    $violation['scanned_at'] = now()->toIso8601String();
                    $violation['scanned_by'] = auth()->id();
                    $violation['is_fixed'] = false;
                }
                
                // Merge with existing violations (keep unique by file+line+rule)
                $merged = $this->mergeViolations($existingViolations, $violations);
                session(['padmin_violations' => $merged]);
                
                $this->logger->info('[SuperAdmin] Violations stored in session', [
                    'admin_id' => auth()->id(),
                    'total_violations' => count($merged),
                    'new_violations' => count($violations),
                ]);
            }

            // Audit log
            $this->auditLogService->logUserAction(
                user: auth()->user(),
                action: 'padmin_scan_executed',
                context: [
                    'path' => $validated['path'],
                    'rules' => $validated['rules'] ?? 'all',
                    'violations_count' => count($violations),
                    'stored' => $validated['store'] ?? false,
                ],
                category: GdprActivityCategory::SYSTEM_INTERACTION
            );

            return response()->json([
                'success' => true,
                'message' => 'Scansione completata',
                'violations' => $violations,
                'count' => count($violations),
                'stored' => $validated['store'] ?? false,
            ]);
        } catch (\Exception $e) {
            $this->errorManager->handle('PADMIN_SCAN_FAILED', [
                'controller' => self::class,
                'method' => __METHOD__,
                'admin_id' => auth()->id(),
                'path' => $validated['path'] ?? 'unknown',
                'rules' => isset($validated['rules']) ? implode(', ', $validated['rules']) : 'none',
                'error' => $e->getMessage(),
            ], $e);

            return response()->json([
                'success' => false,
                'message' => 'Errore durante la scansione. Riprova più tardi.',
            ], 500);
        }
    }

    /**
     * Merge violations avoiding duplicates
     */
    private function mergeViolations(array $existing, array $new): array
    {
        $merged = $existing;
        
        foreach ($new as $newViolation) {
            $isDuplicate = false;
            
            foreach ($existing as $existingViolation) {
                if (
                    $existingViolation['file'] === $newViolation['file'] &&
                    $existingViolation['line'] === $newViolation['line'] &&
                    $existingViolation['rule'] === $newViolation['rule']
                ) {
                    $isDuplicate = true;
                    break;
                }
            }
            
            if (!$isDuplicate) {
                $merged[] = $newViolation;
            }
        }
        
        return $merged;
    }

    /**
     * API: Request AI-assisted fix for violation
     */
    public function requestAiFix(Request $request, string $violationId): JsonResponse {
        try {
            $this->logger->info('[SuperAdmin] Requesting AI fix for violation', [
                'admin_id' => auth()->id(),
                'violation_id' => $violationId,
            ]);

            // Get violation from session (temporary until Redis ready)
            // TODO: Use $this->padminService->getViolationById() when Node.js CLI ready
            $violations = session('padmin_violations', []);
            $violation = collect($violations)->firstWhere('id', $violationId);

            if (!$violation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Violazione non trovata.',
                ], 404);
            }

            // Build AI context prompt
            $aiPrompt = $this->buildAiFixPrompt($violation);

            // Audit log
            $this->auditLogService->logUserAction(
                user: auth()->user(),
                action: 'padmin_ai_fix_requested',
                context: [
                    'violation_id' => $violationId,
                    'violation_type' => $violation['rule'] ?? 'unknown',
                    'file' => $violation['file'] ?? 'unknown',
                ],
                category: GdprActivityCategory::SYSTEM_INTERACTION
            );

            return response()->json([
                'success' => true,
                'message' => 'Contesto AI generato. Copia e incolla in GitHub Copilot Chat.',
                'ai_prompt' => $aiPrompt,
                'violation' => $violation,
            ]);
        } catch (\Exception $e) {
            $this->errorManager->handle('PADMIN_AI_FIX_FAILED', [
                'controller' => self::class,
                'method' => __METHOD__,
                'admin_id' => auth()->id(),
                'violation_id' => $violationId,
                'error' => $e->getMessage(),
            ], $e);

            return response()->json([
                'success' => false,
                'message' => 'Errore durante la richiesta AI fix. Riprova più tardi.',
            ], 500);
        }
    }

    /**
     * Build AI-ready prompt for fixing violation
     */
    protected function buildAiFixPrompt(array $violation): string {
        $rule = $violation['rule'] ?? 'UNKNOWN';
        $type = $violation['type'] ?? 'UNKNOWN';
        $file = $violation['filePath'] ?? 'unknown file';
        $line = $violation['line'] ?? 0;
        $message = $violation['message'] ?? 'No message';
        $snippet = $violation['codeSnippet'] ?? '';

        return <<<PROMPT
🚨 VIOLATION DETECTED - FIX REQUIRED

Rule: {$rule}
Type: {$type}
File: {$file}
Line: {$line}

Message:
{$message}

Code Snippet:
```php
{$snippet}
```

TASK:
1. Read the file: {$file}
2. Locate line {$line}
3. Apply the appropriate fix according to rule {$rule}
4. Verify the fix follows FlorenceEGI Copilot Instructions
5. Test and commit changes

CONTEXT:
- Follow P0 rules strictly
- Maintain GDPR compliance
- Use ErrorManager (UEM) for error handling
- Document all changes with OS2.0 standards

Procedi con la correzione.
PROMPT;
    }
}
