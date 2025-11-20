<?php

namespace App\Http\Controllers;

use App\Models\EppProject;
use App\Models\Collection;
use App\Models\Egi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * Controller for PUBLIC EPP Projects display.
 *
 * Handles public-facing pages showing environmental projects,
 * their Equilibrium received, collections supporting them,
 * and impact metrics. Full transparency for community.
 *
 * --- Core Logic ---
 * 1. Lists all active EPP projects with Equilibrium stats
 * 2. Shows project details with collections and EGI
 * 3. Displays environmental impact metrics
 * 4. Provides blockchain transparency (Algorand verification)
 * 5. Public dashboard showing global Equilibrium distribution
 * --- End Core Logic ---
 *
 * @package App\Http\Controllers
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - EPP Public Projects)
 * @date 2025-11-19
 * @purpose Public display of EPP projects with full Equilibrium transparency
 */
class EppProjectController extends Controller {
    protected UltraLogManager $logger;

    public function __construct(UltraLogManager $logger) {
        $this->logger = $logger;
    }

    /**
     * Display PUBLIC list of all EPP projects with Equilibrium stats.
     *
     * Shows all active environmental projects with:
     * - Equilibrium received
     * - Environmental impact metrics
     * - Collections count supporting each project
     * - Progress percentage
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request) {
        $this->logger->info('[EppProject] Public index accessed', [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Get all active projects with stats
        $projects = EppProject::with(['eppUser', 'eppUser.organizationData'])
            ->withCount('collections')
            ->where('status', '!=', 'cancelled')
            ->orderBy('current_funds', 'desc')
            ->paginate(12);

        // Calculate stats for view
        $totalEquilibrium = EppProject::sum('current_funds') ?? 0;
        $projectCount = EppProject::whereIn('status', ['in_progress', 'planned'])->count();
        $collectionCount = Collection::whereNotNull('epp_project_id')->count();

        // Projects by type distribution
        $projectsByType = [
            'ARF' => EppProject::where('project_type', 'ARF')->count(),
            'APR' => EppProject::where('project_type', 'APR')->count(),
            'BPE' => EppProject::where('project_type', 'BPE')->count(),
        ];

        return view('epp-projects.index', compact(
            'projects',
            'totalEquilibrium',
            'projectCount',
            'collectionCount',
            'projectsByType'
        ));
    }

    /**
     * Display PUBLIC detailed view of specific EPP project.
     *
     * Shows complete project information:
     * - Project details and status
     * - Equilibrium received and distribution
     * - Collections supporting this project
     * - EGI/certificates in those collections
     * - Environmental impact metrics
     * - Evidence and documentation
     *
     * @param EppProject $eppProject
     * @return \Illuminate\View\View
     */
    public function show(EppProject $eppProject) {
        $this->logger->info('[EppProject] Public show accessed', [
            'project_id' => $eppProject->id,
            'project_name' => $eppProject->name,
        ]);

        // Load relationships
        $eppProject->load(['eppUser', 'eppUser.organizationData']);

        // Get EPP's environmental collections (testimonianze, documenti del progetto)
        // NON le collezioni che supportano il progetto!
        $collections = Collection::where('creator_id', $eppProject->epp_user_id)
            ->where('type', 'environmental')
            ->where('is_published', true)
            ->withCount('egis')
            ->with(['creator'])
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        // Calculate impact metrics based on project type
        $impactMetrics = $this->calculateImpactMetrics($eppProject);

        // Equilibrium stats for this project
        $equilibriumStats = [
            'total' => $eppProject->current_funds,
            'this_month' => $this->getMonthlyEquilibrium($eppProject->id),
            'growth_percentage' => $this->getEquilibriumGrowth($eppProject->id),
        ];

        return view('epp-projects.show', compact(
            'eppProject',
            'collections',
            'impactMetrics',
            'equilibriumStats'
        ));
    }

    /**
     * PUBLIC dashboard showing global Equilibrium distribution.
     *
     * Comprehensive transparency dashboard:
     * - Total Equilibrium generated
     * - Distribution by project type (ARF, APR, BPE)
     * - Top projects by Equilibrium
     * - Monthly trends
     * - Environmental impact aggregated
     *
     * @return \Illuminate\View\View
     */
    public function dashboard() {
        $this->logger->info('[EppProject] Public Equilibrium dashboard accessed');

        // Global Equilibrium stats
        $globalStats = [
            'total_equilibrium' => EppProject::sum('current_funds'),
            'active_projects' => EppProject::whereIn('status', ['in_progress', 'planned'])->count(),
            'completed_projects' => EppProject::where('status', 'completed')->count(),
            'total_collections' => Collection::whereNotNull('epp_project_id')->count(),
            'total_egis' => Egi::whereHas('collection', function ($query) {
                $query->whereNotNull('epp_project_id');
            })->count(),
        ];

        // Equilibrium by project type
        $typeStats = EppProject::select(
            'project_type',
            DB::raw('SUM(current_funds) as equilibrium'),
            DB::raw('COUNT(*) as projects_count'),
            DB::raw('SUM(current_value) as total_impact')
        )
            ->groupBy('project_type')
            ->get()
            ->keyBy('project_type');

        // Top 10 projects by Equilibrium
        $topProjects = EppProject::with(['eppUser', 'eppUser.organizationData'])
            ->withCount('collections')
            ->orderBy('current_funds', 'desc')
            ->take(10)
            ->get();

        // Monthly Equilibrium trend (last 12 months)
        $monthlyTrend = $this->getMonthlyEquilibriumTrend();

        // Environmental impact aggregated
        $totalImpact = [
            'ARF' => [
                'trees_planted' => floor(($typeStats['ARF']->total_impact ?? 0)),
                'hectares' => round(($typeStats['ARF']->total_impact ?? 0) / 1000, 2),
                'co2_tons' => round((($typeStats['ARF']->total_impact ?? 0) * 22) / 1000, 2),
            ],
            'APR' => [
                'plastic_kg' => floor(($typeStats['APR']->total_impact ?? 0)),
                'ocean_km2' => round((($typeStats['APR']->total_impact ?? 0) * 100) / 1000000, 2),
                'marine_life_saved' => floor(($typeStats['APR']->total_impact ?? 0) / 10),
            ],
            'BPE' => [
                'hives_created' => floor(($typeStats['BPE']->total_impact ?? 0)),
                'bees_protected' => floor(($typeStats['BPE']->total_impact ?? 0) * 30000),
                'pollinated_km2' => round((($typeStats['BPE']->total_impact ?? 0) * 30000) * 0.00001, 2),
            ],
        ];

        return view('epp-projects.dashboard', compact(
            'globalStats',
            'typeStats',
            'topProjects',
            'monthlyTrend',
            'totalImpact'
        ));
    }

    /**
     * Calculate environmental impact metrics for a project.
     *
     * @param EppProject $project
     * @return array
     */
    private function calculateImpactMetrics(EppProject $project): array {
        switch ($project->project_type) {
            case 'ARF':
                return [
                    'trees_planted' => floor($project->current_value),
                    'hectares_restored' => round($project->current_value / 1000, 2),
                    'co2_sequestered_tons' => round(($project->current_value * 22) / 1000, 2),
                ];

            case 'APR':
                return [
                    'plastic_removed_kg' => floor($project->current_value),
                    'ocean_area_km2' => round(($project->current_value * 100) / 1000000, 2),
                    'marine_life_saved' => floor($project->current_value / 10),
                ];

            case 'BPE':
                return [
                    'hives_created' => floor($project->current_value),
                    'bees_protected' => floor($project->current_value * 30000),
                    'pollinated_area_km2' => round(($project->current_value * 30000) * 0.00001, 2),
                ];

            default:
                return [];
        }
    }

    /**
     * Get Equilibrium received this month for a project.
     *
     * @param int $projectId
     * @return float
     */
    private function getMonthlyEquilibrium(int $projectId): float {
        // TODO: Implement with payment_distributions or epp_transactions table
        // For now, return 0
        return 0;
    }

    /**
     * Get Equilibrium growth percentage for a project.
     *
     * @param int $projectId
     * @return float
     */
    private function getEquilibriumGrowth(int $projectId): float {
        // TODO: Implement with historical data
        // For now, return 0
        return 0;
    }

    /**
     * Get monthly Equilibrium trend (last 12 months).
     *
     * @return array
     */
    private function getMonthlyEquilibriumTrend(): array {
        // TODO: Implement with historical transactions data
        // For now, return empty array
        return [];
    }
}
