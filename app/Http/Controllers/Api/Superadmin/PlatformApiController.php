<?php

namespace App\Http\Controllers\Api\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * @Oracode API Controller: SuperAdmin Platform Management
 * 🎯 Purpose: API per gestione Roles, Pricing, Promotions, Featured Calendar, Consumption Ledger
 */
class PlatformApiController extends Controller
{
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;

    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
    }
    /**
     * Get roles list
     */
    public function roles(Request $request): JsonResponse
    {
        $roles = Role::withCount(['users', 'permissions'])
            ->get()
            ->map(fn($role) => [
                'id' => $role->id,
                'name' => $role->name,
                'slug' => \Str::slug($role->name),
                'description' => $role->description ?? '',
                'users_count' => $role->users_count,
                'permissions_count' => $role->permissions_count,
                'created_at' => $role->created_at,
            ]);

        return response()->json([
            'roles' => $roles,
        ]);
    }

    /**
     * Get feature pricing
     */
    public function pricing(Request $request): JsonResponse
    {
        $features = DB::table('feature_pricing')
            ->get()
            ->map(fn($f) => [
                'id' => $f->id,
                'feature_key' => $f->feature_key,
                'feature_name' => $f->name ?? $f->feature_key,
                'price_egili' => $f->price_egili ?? 0,
                'price_eur' => $f->price_eur ?? 0,
                'is_active' => (bool) ($f->is_active ?? true),
                'description' => $f->description ?? '',
            ]);

        return response()->json([
            'features' => $features,
        ]);
    }

    /**
     * Get promotions
     */
    public function promotions(Request $request): JsonResponse
    {
        $promotions = DB::table('promotions')
            ->get()
            ->map(fn($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'code' => $p->code,
                'discount_percent' => $p->discount_percent ?? 0,
                'discount_egili' => $p->discount_egili ?? 0,
                'valid_from' => $p->valid_from,
                'valid_until' => $p->valid_until,
                'usage_count' => $p->usage_count ?? 0,
                'max_usage' => $p->max_usage,
                'is_active' => (bool) ($p->is_active ?? true),
            ]);

        return response()->json([
            'promotions' => $promotions,
        ]);
    }

    /**
     * Get featured calendar
     */
    public function featuredCalendar(Request $request): JsonResponse
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        $featured = DB::table('featured_egis')
            ->join('egis', 'featured_egis.egi_id', '=', 'egis.id')
            ->whereMonth('featured_egis.date', $month)
            ->whereYear('featured_egis.date', $year)
            ->select('featured_egis.*', 'egis.name as egi_name')
            ->get()
            ->map(fn($f) => [
                'id' => $f->id,
                'egi_id' => $f->egi_id,
                'egi_name' => $f->egi_name,
                'date' => $f->date,
                'slot' => $f->slot ?? 'morning',
                'priority' => $f->priority ?? 1,
            ]);

        return response()->json([
            'featured' => $featured,
            'month' => $month,
            'year' => $year,
        ]);
    }

    /**
     * Get consumption ledger
     */
    public function consumptionLedger(Request $request): JsonResponse
    {
        $period = 'Last 30 days';
        
        $entries = DB::table('consumption_ledger')
            ->join('users', 'consumption_ledger.user_id', '=', 'users.id')
            ->where('consumption_ledger.created_at', '>=', now()->subDays(30))
            ->select('consumption_ledger.*', 'users.name as user_name')
            ->latest('consumption_ledger.created_at')
            ->take(50)
            ->get()
            ->map(fn($e) => [
                'id' => $e->id,
                'user_id' => $e->user_id,
                'user_name' => $e->user_name,
                'feature' => $e->feature,
                'amount_egili' => $e->amount_egili ?? 0,
                'amount_eur' => $e->amount_eur ?? 0,
                'description' => $e->description ?? '',
                'created_at' => $e->created_at,
            ]);

        $stats = [
            'total_egili' => $entries->sum('amount_egili'),
            'total_eur' => $entries->sum('amount_eur'),
            'entries_count' => $entries->count(),
            'period' => $period,
        ];

        return response()->json([
            'stats' => $stats,
            'entries' => $entries,
        ]);
    }
}
