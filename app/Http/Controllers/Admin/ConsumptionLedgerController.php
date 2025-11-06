<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeatureConsumptionLedger;
use App\Models\User;
use App\Models\AiFeaturePricing;
use App\Services\FeatureConsumptionService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * @package App\Http\Controllers\Admin
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Consumption Ledger Dashboard)
 * @date 2025-11-03
 * @purpose Granular dashboard for feature consumption tracking with intelligent filters
 * 
 * Views:
 * - summary(): High-level overview (total users, features, pending debt)
 * - byFeature(): Drill-down by feature (which features consume most)
 * - byUser(): Drill-down by user (which users consume most)
 * - details(): Single entry details (individual consumption record)
 */
class ConsumptionLedgerController extends Controller
{
    private UltraLogManager $logger;
    private FeatureConsumptionService $consumptionService;
    
    public function __construct(
        UltraLogManager $logger,
        FeatureConsumptionService $consumptionService
    ) {
        $this->middleware(['auth', 'role:admin|superadmin']);
        $this->logger = $logger;
        $this->consumptionService = $consumptionService;
    }
    
    /**
     * Summary dashboard - High-level overview
     */
    public function summary(Request $request): View
    {
        // Filters
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $billingStatus = $request->get('billing_status', 'all');
        
        // Build base query
        $query = FeatureConsumptionLedger::query();
        
        if ($dateFrom) {
            $query->where('consumed_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->where('consumed_at', '<=', $dateTo . ' 23:59:59');
        }
        if ($billingStatus && $billingStatus !== 'all') {
            $query->where('billing_status', $billingStatus);
        }
        
        // Summary statistics
        $stats = [
            'total_entries' => $query->count(),
            'total_cost' => $query->sum('total_cost_egili'),
            'pending_cost' => FeatureConsumptionLedger::pending()->sum('total_cost_egili'),
            'charged_cost' => FeatureConsumptionLedger::whereIn('billing_status', ['batched', 'charged'])->sum('total_cost_egili'),
            'unique_users' => $query->distinct('user_id')->count('user_id'),
            'unique_features' => $query->distinct('feature_code')->count('feature_code'),
        ];
        
        // Top features by cost
        $topFeatures = (clone $query)
            ->select('feature_code', DB::raw('SUM(total_cost_egili) as total_cost'), DB::raw('COUNT(*) as uses'))
            ->groupBy('feature_code')
            ->orderByDesc('total_cost')
            ->limit(10)
            ->get();
        
        // Top users by cost
        $topUsers = (clone $query)
            ->select('user_id', DB::raw('SUM(total_cost_egili) as total_cost'), DB::raw('COUNT(*) as uses'))
            ->groupBy('user_id')
            ->orderByDesc('total_cost')
            ->with('user:id,name,email')
            ->limit(10)
            ->get();
        
        // Daily consumption trend (last 30 days)
        $dailyTrend = (clone $query)
            ->selectRaw('DATE(consumed_at) as date, SUM(total_cost_egili) as cost, COUNT(*) as uses')
            ->where('consumed_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();
        
        $this->logger->info('Admin consumption ledger summary accessed', [
            'admin_id' => Auth::id(),
            'filters' => compact('dateFrom', 'dateTo', 'billingStatus'),
            'log_category' => 'ADMIN_CONSUMPTION_SUMMARY'
        ]);
        
        return view('admin.consumption.summary', compact(
            'stats',
            'topFeatures',
            'topUsers',
            'dailyTrend',
            'dateFrom',
            'dateTo',
            'billingStatus'
        ));
    }
    
    /**
     * Drill-down by feature
     */
    public function byFeature(Request $request, ?string $featureCode = null): View
    {
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $billingStatus = $request->get('billing_status', 'all');
        
        // If no feature selected, show feature list
        if (!$featureCode) {
            $features = FeatureConsumptionLedger::select('feature_code')
                ->selectRaw('COUNT(*) as total_uses')
                ->selectRaw('SUM(total_cost_egili) as total_cost')
                ->selectRaw('SUM(CASE WHEN billing_status = "pending" THEN total_cost_egili ELSE 0 END) as pending_cost')
                ->selectRaw('COUNT(DISTINCT user_id) as unique_users')
                ->selectRaw('AVG(total_cost_egili) as avg_cost_per_use')
                ->groupBy('feature_code')
                ->orderByDesc('total_cost')
                ->get();
            
            return view('admin.consumption.features-list', compact('features', 'dateFrom', 'dateTo', 'billingStatus'));
        }
        
        // Feature selected - show detailed breakdown
        $query = FeatureConsumptionLedger::where('feature_code', $featureCode);
        
        if ($dateFrom) {
            $query->where('consumed_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->where('consumed_at', '<=', $dateTo . ' 23:59:59');
        }
        if ($billingStatus && $billingStatus !== 'all') {
            $query->where('billing_status', $billingStatus);
        }
        
        // Feature stats
        $featureStats = [
            'total_uses' => $query->count(),
            'total_cost' => $query->sum('total_cost_egili'),
            'pending_cost' => (clone $query)->where('billing_status', 'pending')->sum('total_cost_egili'),
            'avg_cost' => $query->avg('total_cost_egili'),
            'unique_users' => $query->distinct('user_id')->count('user_id'),
        ];
        
        // Users for this feature
        $userBreakdown = (clone $query)
            ->select('user_id', DB::raw('COUNT(*) as uses'), DB::raw('SUM(total_cost_egili) as cost'))
            ->groupBy('user_id')
            ->orderByDesc('cost')
            ->with('user:id,name,email')
            ->get();
        
        // Recent entries
        $recentEntries = (clone $query)
            ->with('user:id,name,email')
            ->orderBy('consumed_at', 'desc')
            ->limit(50)
            ->get();
        
        return view('admin.consumption.feature-detail', compact(
            'featureCode',
            'featureStats',
            'userBreakdown',
            'recentEntries',
            'dateFrom',
            'dateTo',
            'billingStatus'
        ));
    }
    
    /**
     * Drill-down by user
     */
    public function byUser(Request $request, ?int $userId = null): View
    {
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $billingStatus = $request->get('billing_status', 'all');
        
        // If no user selected, show user list
        if (!$userId) {
            $users = FeatureConsumptionLedger::select('user_id')
                ->selectRaw('COUNT(*) as total_uses')
                ->selectRaw('SUM(total_cost_egili) as total_cost')
                ->selectRaw('SUM(CASE WHEN billing_status = "pending" THEN total_cost_egili ELSE 0 END) as pending_cost')
                ->selectRaw('COUNT(DISTINCT feature_code) as features_used')
                ->selectRaw('MAX(consumed_at) as last_consumption')
                ->groupBy('user_id')
                ->orderByDesc('total_cost')
                ->with('user:id,name,email')
                ->get();
            
            return view('admin.consumption.users-list', compact('users', 'dateFrom', 'dateTo', 'billingStatus'));
        }
        
        // User selected - show detailed breakdown
        $user = User::findOrFail($userId);
        $query = FeatureConsumptionLedger::where('user_id', $userId);
        
        if ($dateFrom) {
            $query->where('consumed_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->where('consumed_at', '<=', $dateTo . ' 23:59:59');
        }
        if ($billingStatus && $billingStatus !== 'all') {
            $query->where('billing_status', $billingStatus);
        }
        
        // User stats
        $userStats = [
            'total_uses' => $query->count(),
            'total_cost' => $query->sum('total_cost_egili'),
            'pending_cost' => (clone $query)->where('billing_status', 'pending')->sum('total_cost_egili'),
            'avg_cost_per_use' => $query->avg('total_cost_egili'),
            'features_used' => $query->distinct('feature_code')->count('feature_code'),
        ];
        
        // Features breakdown for this user
        $featureBreakdown = (clone $query)
            ->select('feature_code', DB::raw('COUNT(*) as uses'), DB::raw('SUM(total_cost_egili) as cost'))
            ->groupBy('feature_code')
            ->orderByDesc('cost')
            ->get();
        
        // Recent entries
        $recentEntries = (clone $query)
            ->orderBy('consumed_at', 'desc')
            ->limit(50)
            ->get();
        
        return view('admin.consumption.user-detail', compact(
            'user',
            'userStats',
            'featureBreakdown',
            'recentEntries',
            'dateFrom',
            'dateTo',
            'billingStatus'
        ));
    }
    
    /**
     * Single entry details
     */
    public function details(int $id): View
    {
        $entry = FeatureConsumptionLedger::with(['user', 'batchTransaction'])
            ->findOrFail($id);
        
        $this->logger->info('Admin viewing consumption entry details', [
            'admin_id' => Auth::id(),
            'entry_id' => $id,
            'log_category' => 'ADMIN_CONSUMPTION_DETAILS'
        ]);
        
        return view('admin.consumption.entry-detail', compact('entry'));
    }
}





