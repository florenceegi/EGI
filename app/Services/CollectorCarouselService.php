<?php

namespace App\Services;

use App\Helpers\DatabaseHelper;
use App\Models\User;
use App\Services\CurrencyService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * @Oracode Service: Top Collectors Carousel for Guest Homepage (Multi-Currency)
 * 🎯 Purpose: Calculate and retrieve top collectors based on total spending
 * 🛡️ Strategy: Marketing visibility for top spenders to incentivize purchases
 * 🧱 Core Logic: Rank collectors by sum of their winning reservations in FIAT
 * 💱 Multi-Currency: Supports EUR, USD, GBP with "Think FIAT, Operate ALGO" architecture
 *
 * Business Logic:
 * - Collectors ranked by total spending (winning reservations in FIAT)
 * - Uses offer_amount_fiat instead of deprecated offer_amount_fiat
 * - Future evolution: Include completed purchases
 * - Top 10 collectors get homepage visibility
 * - Incentivizes higher spending for social recognition
 *
 * @package App\Services
 * @author Padmin D. Curtis + Fabio Cherici (Multi-Currency Enhancement)
 * @version 2.0.0 (Multi-Currency Architecture)
 * @date 2025-08-13
 */
class CollectorCarouselService {
    private CurrencyService $currencyService;

    public function __construct(CurrencyService $currencyService) {
        $this->currencyService = $currencyService;
    }

    /**
     * Get top collectors ranked by total spending in FIAT
     *
     * @param int $limit Number of top collectors to retrieve
     * @return Collection Collection of collectors with spending stats
     */
    public function getTopCollectors(int $limit = 10): Collection {
        // Build cross-database compatible boolean comparison
        $trueValue = DatabaseHelper::booleanValue(true);

        // Build the total spending subquery as a string for both select and where
        $totalSpendingSubquery = "
            COALESCE((
                SELECT SUM(r.offer_amount_fiat)
                FROM reservations r
                WHERE r.user_id = users.id
                AND r.is_current = {$trueValue}
                AND r.status = 'active'
                AND NOT EXISTS (
                    SELECT 1
                    FROM reservations r2
                    WHERE r2.egi_id = r.egi_id
                    AND r2.offer_amount_fiat > r.offer_amount_fiat
                    AND r2.is_current = {$trueValue}
                    AND r2.status = 'active'
                )
            ), 0)
        ";

        return User::select([
            'users.*',
            DB::raw("{$totalSpendingSubquery} as total_spending")
        ])
            ->whereExists(function ($query) use ($trueValue) {
                $query->select(DB::raw(1))
                    ->from('reservations')
                    ->whereColumn('reservations.user_id', 'users.id')
                    ->whereRaw("reservations.is_current = {$trueValue}")
                    ->where('reservations.status', 'active');
            })
            // PostgreSQL doesn't allow aliases in HAVING, use the raw expression
            ->whereRaw("{$totalSpendingSubquery} > 0")
            ->orderByRaw("{$totalSpendingSubquery} DESC")
            ->limit($limit)
            ->get()
            ->map(function ($collector) {
                // Add additional stats for each collector
                $collector->winning_reservations_count = $this->getWinningReservationsCount($collector->id);
                $collector->activated_egis_count = $this->getActivatedEgisCount($collector->id);
                $collector->owned_egis_count = $this->getOwnedEgisCount($collector->id);
                $collector->active_reservations_count = $this->getActiveReservationsCount($collector->id);
                $collector->average_spending = $collector->winning_reservations_count > 0
                    ? $collector->total_spending / $collector->winning_reservations_count
                    : 0;

                return $collector;
            });
    }

    /**
     * Get count of winning reservations for a collector (Multi-Currency)
     */
    private function getWinningReservationsCount(int $userId): int {
        $trueValue = DatabaseHelper::booleanValue(true);

        return DB::table('reservations')
            ->where('user_id', $userId)
            ->whereRaw("is_current = {$trueValue}")
            ->where('status', 'active')
            ->whereNotExists(function ($query) use ($trueValue) {
                $query->select(DB::raw(1))
                    ->from('reservations as r2')
                    ->whereColumn('r2.egi_id', 'reservations.egi_id')
                    ->whereColumn('r2.offer_amount_fiat', '>', 'reservations.offer_amount_fiat')
                    ->whereRaw("r2.is_current = {$trueValue}")
                    ->where('r2.status', 'active');
            })
            ->count();
    }

    /**
     * Get count of activated EGIs (EGIs where this collector has winning reservation)
     */
    private function getActivatedEgisCount(int $userId): int {
        $trueValue = DatabaseHelper::booleanValue(true);

        return DB::table('egis')
            ->whereExists(function ($query) use ($userId, $trueValue) {
                $query->select(DB::raw(1))
                    ->from('reservations')
                    ->whereColumn('reservations.egi_id', 'egis.id')
                    ->where('reservations.user_id', $userId)
                    ->whereRaw("reservations.is_current = {$trueValue}")
                    ->where('reservations.status', 'active')
                    ->whereNotExists(function ($subquery) use ($trueValue) {
                        $subquery->select(DB::raw(1))
                            ->from('reservations as r2')
                            ->whereColumn('r2.egi_id', 'reservations.egi_id')
                            ->whereColumn('r2.offer_amount_fiat', '>', 'reservations.offer_amount_fiat')
                            ->whereRaw("r2.is_current = {$trueValue}")
                            ->where('r2.status', 'active');
                    });
            })
            ->count();
    }

    /**
     * Get collector stats for display (Multi-Currency)
     */
    public function getCollectorStats(int $userId): array {
        $collector = User::find($userId);

        if (!$collector) {
            return [];
        }

        $trueValue = DatabaseHelper::booleanValue(true);

        $totalSpending = DB::table('reservations')
            ->where('user_id', $userId)
            ->whereRaw("is_current = {$trueValue}")
            ->where('status', 'active')
            ->whereNotExists(function ($query) use ($trueValue) {
                $query->select(DB::raw(1))
                    ->from('reservations as r2')
                    ->whereColumn('r2.egi_id', 'reservations.egi_id')
                    ->whereColumn('r2.offer_amount_fiat', '>', 'reservations.offer_amount_fiat')
                    ->whereRaw("r2.is_current = {$trueValue}")
                    ->where('r2.status', 'active');
            })
            ->sum('offer_amount_fiat');

        return [
            'total_spending' => $totalSpending ?? 0,
            'winning_reservations_count' => $this->getWinningReservationsCount($userId),
            'activated_egis_count' => $this->getActivatedEgisCount($userId),
            'owned_egis_count' => $this->getOwnedEgisCount($userId),
            'active_reservations_count' => $this->getActiveReservationsCount($userId),
        ];
    }

    /**
     * Get count of owned EGIs for a collector (simplified version for now)
     */
    private function getOwnedEgisCount(int $userId): int {
        // For now, count winning reservations as "owned"
        // In future, this could be based on actual ownership/purchase records
        return $this->getWinningReservationsCount($userId);
    }

    /**
     * Get count of active reservations for a collector
     */
    private function getActiveReservationsCount(int $userId): int {
        $trueValue = DatabaseHelper::booleanValue(true);

        return DB::table('reservations')
            ->where('user_id', $userId)
            ->whereRaw("is_current = {$trueValue}")
            ->where('status', 'active')
            ->count();
    }
}
