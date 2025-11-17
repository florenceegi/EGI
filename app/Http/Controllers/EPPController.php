<?php

namespace App\Http\Controllers;

use App\Models\Epp;
use App\Models\Collection;
use App\Models\EppMilestone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Controller for managing EPP (Environment Protection Programs) display and interaction.
 *
 * Handles the listing, viewing, and tracking of environmental projects
 * linked to collections and EGIs. Implements Oracode principles for readable,
 * maintainable code.
 *
 * --- Core Logic ---
 * 1. Retrieves EPP information for display
 * 2. Calculates and shows environmental impact metrics
 * 3. Lists collections associated with specific EPPs
 * 4. Provides transparency on fund allocation to environmental projects
 * 5. Tracks progress of environmental initiatives
 * --- End Core Logic ---
 *
 * @package App\Http\Controllers
 * @author Your Name <your.email@example.com>
 * @version 1.0.0
 * @since 1.0.0
 */
class EPPController extends Controller {
    /**
     * Display a listing of the EPPs.
     *
     * Shows all available Environment Protection Programs with
     * their impact statistics and associated collections.
     *
     * @param Request $request The HTTP request
     * @return \Illuminate\View\View The view with EPP data
     */
    public function index(Request $request) {
        // Get all EPPs with basic stats
        $epps = Epp::withCount(['collections', 'transactions'])
            ->withSum('transactions', 'amount')
            ->paginate(9);

        return view('epps.index', compact('epps'));
    }

    /**
     * Display the specified EPP.
     *
     * Shows detailed information about a single EPP, including
     * its progress, impact metrics, and associated collections.
     *
     * @param Epp $epp The EPP to display
     * @return \Illuminate\View\View The view with EPP details
     */
    public function show(Epp $epp) {
        // Load all necessary relationships
        $epp->load(['transactions', 'milestones']);

        // Get collections associated with this EPP
        $collections = Collection::where('epp_id', $epp->id)
            ->withCount(['egis', 'likes'])
            ->paginate(6);

        // Calculate impact metrics
        $totalFunds = $epp->transactions->sum('amount');
        $totalCollections = $collections->total();
        $impactMetrics = $this->calculateTypeMetrics($epp);

        return view('epps.show', compact('epp', 'collections', 'totalFunds', 'totalCollections', 'impactMetrics'));
    }

    /**
     * Display the dashboard for EPP progress.
     *
     * This page shows comprehensive statistics and visualizations
     * for all EPPs, intended for transparency and tracking. It includes
     * global metrics, type-specific impact data, and visualization of
     * funding distribution and environmental impact over time.
     *
     * @param Request $request The HTTP request
     * @return \Illuminate\View\View The view with dashboard data
     */
    public function dashboard(Request $request) {
        // Get summary statistics for all EPPs
        $totalFunds = DB::table('epp_transactions')->sum('amount');
        $totalEpps = Epp::count();
        $totalCollections = Collection::whereNotNull('epp_id')->count();

        // Get EPP types with their funding totals
        $eppTypeStats = Epp::select('type', DB::raw('COUNT(*) as count'), DB::raw('SUM(total_funds) as funds'))
            ->groupBy('type')
            ->get();

        // Get monthly funding data for charts
        $monthlyFunding = DB::table('epp_transactions')
            ->select(DB::raw('YEAR(created_at) as year'), DB::raw('MONTH(created_at) as month'), DB::raw('SUM(amount) as total'))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // Calculate global metrics for dashboard header
        $globalMetrics = [
            'totalContribution' => $totalFunds,
            'activeEpps' => Epp::where('status', 'active')->count(),
            'contributorsCount' => DB::table('epp_transactions')
                ->distinct('user_id')
                ->count('user_id')
        ];

        // Get type-specific metrics
        $typeMetrics = [
            'ARF' => $this->calculateTypeMetrics('ARF'),
            'APR' => $this->calculateTypeMetrics('APR'),
            'BPE' => $this->calculateTypeMetrics('BPE')
        ];

        // Get active projects by type
        $arfProjects = Epp::where('type', 'ARF')->where('status', 'active')
            ->withCount(['collections', 'transactions'])
            ->get()
            ->map(function ($project) {
                // Use the completion_percentage attribute from the model
                $project->metrics = $this->calculateTypeMetrics($project);
                return $project;
            });

        $aprProjects = Epp::where('type', 'APR')->where('status', 'active')
            ->withCount(['collections', 'transactions'])
            ->get()
            ->map(function ($project) {
                $project->metrics = $this->calculateTypeMetrics($project);
                return $project;
            });

        $bpeProjects = Epp::where('type', 'BPE')->where('status', 'active')
            ->withCount(['collections', 'transactions'])
            ->get()
            ->map(function ($project) {
                $project->metrics = $this->calculateTypeMetrics($project);
                return $project;
            });

        // Get recent milestones
        $recentMilestones = EppMilestone::whereNotNull('completion_date')
            ->with('epp')
            ->orderBy('completion_date', 'desc')
            ->take(5)
            ->get()
            ->map(function ($milestone) {
                // Add calculated metrics based on the milestone's EPP type
                $impactMetrics = [];
                switch ($milestone->epp->type) {
                    case 'ARF':
                        $impactMetrics['treesPlanted'] = floor($milestone->current_value / 10);
                        break;
                    case 'APR':
                        $impactMetrics['plasticRemoved'] = floor($milestone->current_value / 5);
                        break;
                    case 'BPE':
                        $impactMetrics['hivesCreated'] = floor($milestone->current_value / 100);
                        break;
                }
                $milestone->metrics = $impactMetrics;
                return $milestone;
            });

        // Prepare data for charts
        $chartData = $this->prepareChartData($eppTypeStats, $monthlyFunding);

        return view('epps.dashboard', compact(
            'totalFunds',
            'totalEpps',
            'totalCollections',
            'eppTypeStats',
            'monthlyFunding',
            'globalMetrics',
            'typeMetrics',
            'arfProjects',
            'aprProjects',
            'bpeProjects',
            'recentMilestones',
            'chartData'
        ));
    }

    /**
     * Calculate impact metrics for a specific EPP type.
     *
     * Processes transaction data to compute the environmental impact metrics
     * based on the specific conversion factors for each program type.
     * Reuses the same conversion logic from calculateImpactMetrics().
     *
     * @param string $type The EPP type (ARF, APR, or BPE)
     * @return array The calculated metrics specific to the program type
     */
    private function calculateTypeMetrics($type) {
        // Get total contribution for this type
        $totalTypeContribution = DB::table('epp_transactions')
            ->join('epps', 'epp_transactions.epp_id', '=', 'epps.id')
            ->where('epps.type', $type)
            ->sum('epp_transactions.amount');

        // Calculate metrics based on type-specific conversion factors
        switch ($type) {
            case 'ARF':
                // Using the same calculation logic as calculateImpactMetrics()
                $treesPlanted = floor($totalTypeContribution / 10);
                $hectaresRestored = round($treesPlanted / 1000, 2);
                $co2Sequestered = $treesPlanted * 22 / 1000; // Convert kg to tons

                return [
                    'treesPlanted' => $treesPlanted,
                    'hectaresRestored' => $hectaresRestored,
                    'co2Sequestered' => $co2Sequestered
                ];

            case 'APR':
                $plasticRemoved = floor($totalTypeContribution / 5);
                $oceanAreaCleaned = round($plasticRemoved * 100 / 1000000, 2); // Convert cubic meters to km²
                $marineLifeSaved = floor($plasticRemoved / 10);

                return [
                    'plasticRemoved' => $plasticRemoved,
                    'oceanAreaCleaned' => $oceanAreaCleaned,
                    'marineLifeSaved' => $marineLifeSaved
                ];

            case 'BPE':
                $hivesCreated = floor($totalTypeContribution / 100);
                $beesProtected = $hivesCreated * 30000;
                $pollinatedArea = round($beesProtected * 0.00001, 2); // Estimated km²

                return [
                    'hivesCreated' => $hivesCreated,
                    'beesProtected' => $beesProtected,
                    'pollinatedArea' => $pollinatedArea
                ];

            default:
                return [];
        }
    }

    /**
     * Prepare data for the dashboard charts.
     *
     * Formats the database query results into the structure needed
     * for Chart.js visualization. Prepares distribution data, growth
     * trends, and program-specific metrics for visualization.
     *
     * @param \Illuminate\Support\Collection $eppTypeStats Statistics by EPP type
     * @param \Illuminate\Support\Collection $monthlyFunding Monthly funding data
     * @return array Structured data for charts
     */
    private function prepareChartData($eppTypeStats, $monthlyFunding) {
        // Calculate total funds for percentage calculations
        $totalAllFunds = DB::table('epp_transactions')->sum('amount');

        // Prepare distribution chart data
        $distributionLabels = $eppTypeStats->pluck('type')->map(function ($type) {
            switch ($type) {
                case 'ARF':
                    return 'ARF (Reforestation)';
                case 'APR':
                    return 'APR (Ocean Cleanup)';
                case 'BPE':
                    return 'BPE (Bee Protection)';
                default:
                    return $type;
            }
        })->toArray();

        $distributionValues = $eppTypeStats->pluck('funds')->toArray();

        // Prepare growth chart data
        $months = [];
        $arfGrowth = [];
        $aprGrowth = [];
        $bpeGrowth = [];
        $arfMetrics = [];
        $aprMetrics = [];
        $bpeMetrics = [];

        // Group all transaction data by month and EPP type
        $monthlyTypeData = [];

        // For each month in our data
        foreach ($monthlyFunding as $record) {
            $yearMonth = "{$record->year}-{$record->month}";
            $monthName = date('M', mktime(0, 0, 0, $record->month, 1));
            $months[] = $monthName . ' ' . $record->year;

            // If we don't already have data for this month and EPP types
            if (!isset($monthlyTypeData[$yearMonth])) {
                // Query database for this month's data per EPP type
                $typeData = DB::table('epp_transactions')
                    ->join('epps', 'epp_transactions.epp_id', '=', 'epps.id')
                    ->select('epps.type', DB::raw('SUM(epp_transactions.amount) as total'))
                    ->whereYear('epp_transactions.created_at', $record->year)
                    ->whereMonth('epp_transactions.created_at', $record->month)
                    ->groupBy('epps.type')
                    ->get()
                    ->keyBy('type');

                $monthlyTypeData[$yearMonth] = $typeData;
            }

            // Get totals for each EPP type this month, or 0 if none
            $arfTotal = isset($monthlyTypeData[$yearMonth]['ARF']) ? $monthlyTypeData[$yearMonth]['ARF']->total : 0;
            $aprTotal = isset($monthlyTypeData[$yearMonth]['APR']) ? $monthlyTypeData[$yearMonth]['APR']->total : 0;
            $bpeTotal = isset($monthlyTypeData[$yearMonth]['BPE']) ? $monthlyTypeData[$yearMonth]['BPE']->total : 0;

            // Calculate growth percentages (this is simplified - in production would calculate actual growth)
            // For demonstration, we'll use these values directly as "percent growth"
            $arfGrowth[] = min(100, round(($arfTotal / max(1, $totalAllFunds)) * 100));
            $aprGrowth[] = min(100, round(($aprTotal / max(1, $totalAllFunds)) * 100));
            $bpeGrowth[] = min(100, round(($bpeTotal / max(1, $totalAllFunds)) * 100));

            // Calculate impact metrics for each EPP type by month for specific charts
            $arfMetric = floor($arfTotal / 10); // Trees planted
            $aprMetric = floor($aprTotal / 5);  // Plastic removed (kg)
            $bpeMetric = floor($bpeTotal / 100); // Hives created

            $arfMetrics[] = $arfMetric;
            $aprMetrics[] = $aprMetric;
            $bpeMetrics[] = $bpeMetric;
        }

        return [
            'distribution' => [
                'labels' => $distributionLabels,
                'values' => $distributionValues
            ],
            'growth' => [
                'labels' => $months,
                'datasets' => [
                    ['data' => $arfGrowth],
                    ['data' => $aprGrowth],
                    ['data' => $bpeGrowth]
                ]
            ],
            // Add type-specific chart data
            'arf' => [
                'labels' => $months,
                'datasets' => [
                    ['data' => $arfMetrics]
                ]
            ],
            'apr' => [
                'labels' => $months,
                'datasets' => [
                    ['data' => $aprMetrics]
                ]
            ],
            'bpe' => [
                'labels' => $months,
                'datasets' => [
                    ['data' => $bpeMetrics]
                ]
            ]
        ];
    }

    /**
     * Get all active EPP projects as JSON for API consumption.
     *
     * Returns a list of active environmental projects with EPP User details
     * for use in project selection interfaces. Uses correct EppProject model
     * and UserOrganizationData for organization information.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getActiveEppProjects() {
        try {
            $projects = \App\Models\EppProject::where('status', 'in_progress')
                ->orWhere('status', 'planned')
                ->with([
                    'eppUser:id,name,email,usertype',
                    'eppUser.organizationData' // UserOrganizationData relationship
                ])
                ->withCount('collections')
                ->get()
                ->map(function ($project) {
                    $eppUser = $project->eppUser;
                    $orgData = $eppUser ? $eppUser->organizationData : null;

                    return [
                        'id' => $project->id,
                        'name' => $project->name,
                        'description' => $project->description,
                        'project_type' => $project->project_type,
                        'project_type_name' => $project->project_type_name,
                        'status' => $project->status,
                        'completion_percentage' => $project->completion_percentage,
                        'funds_completion_percentage' => $project->funds_completion_percentage,
                        'target_value' => $project->target_value,
                        'current_value' => $project->current_value,
                        'target_funds' => $project->target_funds,
                        'current_funds' => $project->current_funds,
                        'collections_count' => $project->collections_count ?? 0,
                        'epp_user' => [
                            'id' => $eppUser->id ?? null,
                            'name' => $eppUser->name ?? 'Unknown',
                            'organization_name' => $orgData->organization_name ?? null,
                            'fiscal_code' => $orgData->fiscal_code ?? null,
                            'email' => $eppUser->email ?? null,
                        ]
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $projects
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve EPP projects',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
