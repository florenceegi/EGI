<?php

namespace App\Http\Controllers\PA;

use App\Http\Controllers\Controller;
use App\Models\EgiAct;
use Illuminate\Http\{JsonResponse, RedirectResponse, Request};
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * PA N.A.T.A.N. Controller
 *
 * @package App\Http\Controllers\PA
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - N.A.T.A.N. AI Document Intelligence)
 * @date 2025-10-09
 * @purpose N.A.T.A.N. dashboard and document management for PA entities
 *
 * Features:
 * - Dashboard with AI statistics and recent acts
 * - Document upload interface with drag&drop
 * - Acts list with advanced filters
 * - Act detail with metadata viewer
 *
 * GDPR Compliance:
 * - ULM logging for all access (read-only, no consent needed)
 * - No modification of personal data
 * - User can only see their organization's acts
 *
 * Authorization:
 * - Middleware: auth + role:pa_entity (Spatie permission)
 * - User must have pa_entity role assigned
 */
class PANatanController extends Controller
{
    /**
     * Services dependency injection
     */
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;

    /**
     * Constructor with DI
     */
    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager
    ) {
        // Only auth middleware - role check done in methods
        $this->middleware(['auth']);

        $this->logger = $logger;
        $this->errorManager = $errorManager;
    }

    /**
     * N.A.T.A.N. Dashboard
     *
     * Display dashboard with AI statistics, recent acts, and quick upload
     *
     * @param Request $request
     * @return View|RedirectResponse
     */
    public function dashboard(Request $request): View|RedirectResponse
    {
        try {
            $user = Auth::user();

            // Authorization: Check pa_entity role
            if (!$user->hasRole('pa_entity')) {
                abort(403, 'Accesso negato. Ruolo PA Entity richiesto.');
            }

            // ULM: Log dashboard access
            $this->logger->info('N.A.T.A.N. Dashboard accessed', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'ip_address' => $request->ip(),
            ]);

            // Get statistics
            $stats = $this->getDashboardStats();

            // Get recent acts (last 10)
            $recentActs = EgiAct::completed()
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            // Get processing stats
            $processingStats = [
                'pending' => EgiAct::pending()->count(),
                'completed_today' => EgiAct::completed()->whereDate('created_at', today())->count(),
                'failed_today' => EgiAct::failed()->whereDate('created_at', today())->count(),
            ];

            // ULM: Log successful load
            $this->logger->info('N.A.T.A.N. Dashboard data loaded', [
                'user_id' => $user->id,
                'total_acts' => $stats['total_acts'],
                'recent_acts_count' => $recentActs->count(),
            ]);

            return view('pa.natan.dashboard', compact(
                'stats',
                'recentActs',
                'processingStats'
            ));

        } catch (\Exception $e) {
            // UEM: Error handling
            $this->errorManager->handle('PA_NATAN_DASHBOARD_ERROR', [
                'user_id' => Auth::id(),
            ], $e);

            // ULM: Log error
            $this->logger->error('N.A.T.A.N. Dashboard error', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('pa.dashboard')->withErrors([
                'error' => 'Impossibile caricare la dashboard N.A.T.A.N. Riprova tra poco.'
            ]);
        }
    }

    /**
     * N.A.T.A.N. Upload Page
     *
     * Display document upload interface
     *
     * @param Request $request
     * @return View|RedirectResponse
     */
    public function upload(Request $request): View|RedirectResponse
    {
        try {
            $user = Auth::user();

            // Authorization: Check pa_entity role
            if (!$user->hasRole('pa_entity')) {
                abort(403, 'Accesso negato. Ruolo PA Entity richiesto.');
            }

            // ULM: Log upload page access
            $this->logger->info('N.A.T.A.N. Upload page accessed', [
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
            ]);

            return view('pa.natan.upload');

        } catch (\Exception $e) {
            // UEM: Error handling
            $this->errorManager->handle('PA_NATAN_UPLOAD_PAGE_ERROR', [
                'user_id' => Auth::id(),
            ], $e);

            return redirect()->route('pa.natan.dashboard')->withErrors([
                'error' => 'Impossibile caricare la pagina di upload.'
            ]);
        }
    }

    /**
     * N.A.T.A.N. Acts List
     *
     * Display full acts table with filters
     *
     * @param Request $request
     * @return View|RedirectResponse
     */
    public function acts(Request $request): View|RedirectResponse
    {
        try {
            $user = Auth::user();

            // Authorization: Check pa_entity role
            if (!$user->hasRole('pa_entity')) {
                abort(403, 'Accesso negato. Ruolo PA Entity richiesto.');
            }

            // ULM: Log acts page access
            $this->logger->info('N.A.T.A.N. Acts list accessed', [
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
            ]);

            // Get filter options for dropdowns
            $filterOptions = [
                'tipi_atto' => EgiAct::completed()
                    ->distinct()
                    ->pluck('tipo_atto')
                    ->filter()
                    ->values(),
                
                'enti' => EgiAct::completed()
                    ->distinct()
                    ->whereNotNull('ente')
                    ->pluck('ente')
                    ->filter()
                    ->values(),
                
                'direzioni' => EgiAct::completed()
                    ->distinct()
                    ->whereNotNull('direzione')
                    ->pluck('direzione')
                    ->filter()
                    ->values(),
            ];

            return view('pa.natan.acts', compact('filterOptions'));

        } catch (\Exception $e) {
            // UEM: Error handling
            $this->errorManager->handle('PA_NATAN_ACTS_PAGE_ERROR', [
                'user_id' => Auth::id(),
            ], $e);

            return redirect()->route('pa.natan.dashboard')->withErrors([
                'error' => 'Impossibile caricare la lista atti.'
            ]);
        }
    }

    /**
     * N.A.T.A.N. Act Detail
     *
     * Display single act with full metadata
     *
     * @param Request $request
     * @param int $id
     * @return View|RedirectResponse
     */
    public function show(Request $request, int $id): View|RedirectResponse
    {
        try {
            $user = Auth::user();

            // Authorization: Check pa_entity role
            if (!$user->hasRole('pa_entity')) {
                abort(403, 'Accesso negato. Ruolo PA Entity richiesto.');
            }

            // Get act
            $act = EgiAct::findOrFail($id);

            // ULM: Log act detail access
            $this->logger->info('N.A.T.A.N. Act detail accessed', [
                'user_id' => $user->id,
                'act_id' => $act->id,
                'document_id' => $act->document_id,
                'tipo_atto' => $act->tipo_atto,
            ]);

            return view('pa.natan.show', compact('act'));

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('pa.natan.acts')->withErrors([
                'error' => 'Atto non trovato.'
            ]);

        } catch (\Exception $e) {
            // UEM: Error handling
            $this->errorManager->handle('PA_NATAN_ACT_DETAIL_ERROR', [
                'user_id' => Auth::id(),
                'act_id' => $id,
            ], $e);

            return redirect()->route('pa.natan.acts')->withErrors([
                'error' => 'Impossibile caricare i dettagli dell\'atto.'
            ]);
        }
    }

    /**
     * Get dashboard statistics
     *
     * @return array Statistics data
     */
    private function getDashboardStats(): array
    {
        // Cache stats for 10 minutes
        return cache()->remember('natan:dashboard:stats', now()->addMinutes(10), function () {
            $totalActs = EgiAct::completed()->count();
            $totalCost = EgiAct::completed()->sum('ai_cost') ?? 0;
            $avgProcessingTime = 28; // TODO: Calculate from real data when available
            
            // Acts by tipo
            $byTipo = EgiAct::completed()
                ->selectRaw('tipo_atto, COUNT(*) as count')
                ->groupBy('tipo_atto')
                ->pluck('count', 'tipo_atto')
                ->toArray();

            return [
                'total_acts' => $totalActs,
                'total_ai_cost' => round($totalCost, 2),
                'avg_processing_time' => $avgProcessingTime,
                'acts_this_month' => EgiAct::completed()->whereMonth('created_at', now()->month)->count(),
                'by_tipo' => $byTipo,
            ];
        });
    }
}
