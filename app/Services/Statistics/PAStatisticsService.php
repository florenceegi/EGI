<?php

namespace App\Services\Statistics;

use App\Models\User;
use Carbon\Carbon;

/**
 * PA Statistics Service - MVP MOCK Version
 *
 * @package App\Services\Statistics
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - PA Statistics MVP MOCK)
 * @date 2025-10-02
 * @purpose Dashboard KPI e statistiche per PA Entity - MOCK per MVP
 *
 * IMPORTANT - FASE 2 UPGRADE:
 * Questo service usa MOCK data per MVP demo.
 * In FASE 2 (TASK 6.3) sostituire con query reali da database.
 *
 * REGOLA STATISTICS COMPLIANCE:
 * ✅ NO hidden ->take() or ->limit() in methods
 * ✅ All data limits must be explicit in method signatures
 * ✅ Full dataset returned by default
 *
 * Real Implementation Pattern (FASE 2):
 * - Query collection_user pivot per ownership
 * - Query coa table per certificate status
 * - Query egi table per heritage items count
 * - Aggregate real blockchain verification data
 */
class PAStatisticsService {
    /**
     * Get dashboard KPI statistics
     *
     * MVP: Returns MOCK data
     * FASE 2: Replace with real database queries
     *
     * @param User $paEntity PA Entity user
     * @return array Dashboard statistics
     */
    public function getDashboardStats(User $paEntity): array {
        // MVP: MOCK DATA per demo assessori
        // POST-MVP: Real queries from collections, coa, egi tables

        return [
            'total_heritage' => 127,
            'coa_issued' => 89,
            'coa_pending' => 12,
            'coa_draft' => 8,
            'inspections_active' => 5,
            'inspections_completed' => 82,
            'blockchain_verifications' => 89,
            'public_visibility' => 76,
            'last_update' => Carbon::now()->subHours(2),
            'monthly_growth' => [
                'heritage' => 12, // +12 new items this month
                'coa' => 8,       // +8 new CoA this month
                'verifications' => 156, // Total verifications this month
            ],
        ];
    }

    /**
     * Get pending actions requiring PA attention
     *
     * MVP: Returns MOCK data
     * FASE 2: Real queries from coa, collection_user, notifications
     *
     * @param User $paEntity PA Entity user
     * @return array Pending actions with counts
     */
    public function getPendingActions(User $paEntity): array {
        // MVP: MOCK DATA

        return [
            'coa_to_approve' => 3,
            'inspections_to_assign' => 2,
            'inspector_reports_pending' => 1,
            'blockchain_sync_pending' => 0,
            'public_visibility_requests' => 5,
        ];
    }

    /**
     * Get recent heritage items (real query for demo)
     *
     * This method uses REAL data even in MVP
     * Query collections owned by PA entity
     *
     * @param User $paEntity PA Entity user
     * @param int|null $limit Optional limit (explicit, not hidden)
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRecentHeritage(User $paEntity, ?int $limit = 5): \Illuminate\Database\Eloquent\Collection {
        // Real query - get collections owned by PA entity
        $query = \App\Models\Egi::whereHas('collection', function ($q) use ($paEntity) {
            $q->where('owner_id', $paEntity->id);
        })
            ->with(['coa', 'collection'])
            ->latest();

        // Explicit limit - compliant with REGOLA STATISTICS
        if ($limit !== null) {
            $query->limit($limit);
        }

        return $query->get();
    }

    /**
     * Get CoA status distribution
     *
     * MVP: Returns MOCK data
     * FASE 2: Real COUNT queries grouped by status
     *
     * @param User $paEntity PA Entity user
     * @return array Status distribution with counts and percentages
     */
    public function getCoAStatusDistribution(User $paEntity): array {
        // MVP: MOCK DATA

        $total = 109; // Total CoA (issued + pending + draft)

        return [
            'issued' => [
                'count' => 89,
                'percentage' => round((89 / $total) * 100, 1),
                'color' => 'success', // For UI badge
            ],
            'pending' => [
                'count' => 12,
                'percentage' => round((12 / $total) * 100, 1),
                'color' => 'warning',
            ],
            'draft' => [
                'count' => 8,
                'percentage' => round((8 / $total) * 100, 1),
                'color' => 'info',
            ],
            'revoked' => [
                'count' => 0,
                'percentage' => 0,
                'color' => 'error',
            ],
        ];
    }

    /**
     * Get monthly trend data for charts
     *
     * MVP: Returns MOCK data
     * FASE 2: Real aggregate queries with date grouping
     *
     * @param User $paEntity PA Entity user
     * @param int $months Number of months to retrieve (default 6)
     * @return array Monthly trend data
     */
    public function getMonthlyTrends(User $paEntity, int $months = 6): array {
        // MVP: MOCK DATA for chart display

        // MVP: MOCK trend data (static values for demo)
        $mockData = [12, 10, 14, 9, 13, 15]; // Heritage added per month
        $mockCoa = [8, 7, 10, 6, 9, 11];     // CoA issued per month
        $mockVerif = [142, 128, 165, 134, 158, 178]; // Verifications per month

        $trends = [];
        $now = Carbon::now();

        for ($i = $months - 1; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i);
            $index = $months - 1 - $i;

            $trends[] = [
                'month' => $month->format('M Y'),
                'heritage_added' => $mockData[$index] ?? 10,
                'coa_issued' => $mockCoa[$index] ?? 7,
                'verifications' => $mockVerif[$index] ?? 150,
            ];
        }

        return $trends;
    }

    /**
     * FASE 2 TODO - Real Implementation Examples
     *
     * Future methods when implementing real queries:
     *
     * private function queryTotalHeritage(User $paEntity): int
     * {
     *     return Egi::whereHas('collection', function($q) use ($paEntity) {
     *         $q->where('owner_id', $paEntity->id);
     *     })->count();
     * }
     *
     * private function queryCoAByStatus(User $paEntity, string $status): int
     * {
     *     return Coa::whereHas('egi.collection', function($q) use ($paEntity) {
     *         $q->where('owner_id', $paEntity->id);
     *     })->where('status', $status)->count();
     * }
     *
     * private function queryBlockchainVerifications(User $paEntity): int
     * {
     *     return Coa::whereHas('egi.collection', function($q) use ($paEntity) {
     *         $q->where('owner_id', $paEntity->id);
     *     })->whereNotNull('verification_hash')->count();
     * }
     */
}
