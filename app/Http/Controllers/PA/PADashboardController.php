<?php

namespace App\Http\Controllers\PA;

use App\Http\Controllers\Controller;
use App\Services\Statistics\PAStatisticsService;
use Illuminate\Http\{JsonResponse, RedirectResponse, Request};
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * PA Dashboard Controller
 *
 * @package App\Http\Controllers\PA
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - PA Dashboard MVP)
 * @date 2025-10-02
 * @purpose Dashboard PA Entity con KPI, recent heritage, pending actions
 *
 * Features:
 * - Dashboard KPI statistiche (MOCK per MVP, real data in FASE 2)
 * - Recent heritage items (real query from collections)
 * - Pending actions (CoA to approve, inspections to assign)
 * - Quick stats API endpoint for async refresh
 *
 * GDPR Compliance:
 * - ULM logging for all access (read-only, no consent needed)
 * - No data modification operations
 * - User can only see their own PA entity data
 *
 * Authorization:
 * - Middleware: auth + role:pa_entity (Spatie permission)
 * - User must have pa_entity role assigned
 */
class PADashboardController extends Controller {
    /**
     * Services dependency injection
     */
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;
    protected PAStatisticsService $statsService;

    /**
     * Constructor with DI
     */
    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        PAStatisticsService $statsService
    ) {
        // Middleware: auth + role:pa_entity
        $this->middleware(['auth', 'role:pa_entity']);

        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->statsService = $statsService;
    }

    /**
     * PA Dashboard Index
     *
     * Display dashboard with KPI cards, recent heritage, and pending actions
     *
     * @param Request $request
     * @return View|RedirectResponse
     */
    public function index(Request $request): View|RedirectResponse {
        try {
            $user = Auth::user();

            // ULM: Log dashboard access
            $this->logger->info('PA Dashboard accessed', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'ip_address' => $request->ip(),
            ]);

            // Get dashboard statistics (MOCK per MVP)
            $stats = $this->statsService->getDashboardStats($user);

            // Get recent heritage items (REAL query)
            $recentHeritage = $this->statsService->getRecentHeritage($user, 5);

            // Get pending actions (MOCK per MVP)
            $pendingActions = $this->statsService->getPendingActions($user);

            // Get CoA status distribution for chart (MOCK per MVP)
            $coaStatusDistribution = $this->statsService->getCoAStatusDistribution($user);

            // Get monthly trends for chart (MOCK per MVP)
            $monthlyTrends = $this->statsService->getMonthlyTrends($user, 6);

            // ULM: Log successful data load
            $this->logger->info('PA Dashboard data loaded successfully', [
                'user_id' => $user->id,
                'stats_total_heritage' => $stats['total_heritage'],
                'recent_heritage_count' => $recentHeritage->count(),
                'pending_actions_count' => array_sum($pendingActions),
            ]);

            return view('pa.dashboard', compact(
                'stats',
                'recentHeritage',
                'pendingActions',
                'coaStatusDistribution',
                'monthlyTrends'
            ));
        } catch (\Exception $e) {
            // UEM: Error handling
            $this->errorManager->handle('PA_DASHBOARD_ERROR', [
                'user_id' => Auth::id(),
                'context' => 'Dashboard access failed',
                'error_message' => $e->getMessage(),
            ], $e);

            // ULM: Log error
            $this->logger->error('PA Dashboard error', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('home')->withErrors([
                'error' => 'Impossibile caricare la dashboard PA. Riprova tra poco.'
            ]);
        }
    }

    /**
     * Quick Stats API Endpoint
     *
     * Return statistics as JSON for async refresh (e.g., via AJAX)
     * Useful for real-time dashboard updates without page reload
     *
     * @return JsonResponse
     */
    public function quickStats(): JsonResponse {
        try {
            $user = Auth::user();

            // Get fresh statistics
            $stats = $this->statsService->getDashboardStats($user);

            // ULM: Log API access
            $this->logger->info('PA Dashboard quick stats API called', [
                'user_id' => $user->id,
            ]);

            return response()->json([
                'success' => true,
                'data' => $stats,
                'timestamp' => now()->toIso8601String(),
            ]);
        } catch (\Exception $e) {
            // UEM: Error handling
            $this->errorManager->handle('PA_DASHBOARD_QUICKSTATS_ERROR', [
                'user_id' => Auth::id(),
                'context' => 'Quick stats API failed',
            ], $e);

            return response()->json([
                'success' => false,
                'error' => 'Unable to load statistics',
            ], 500);
        }
    }
}
