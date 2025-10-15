<?php

declare(strict_types=1);

namespace App\Services\PaActs;

use App\Models\User;
use App\Models\Egi;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * @Oracode PA Act Statistics Service
 * 🎯 Purpose: Advanced statistics and KPIs for N.A.T.A.N. PA acts
 * 🧱 Core Logic: Real-time queries on egis table for PA acts analytics
 * 🛡️ Security: User-scoped queries, GDPR-compliant aggregations
 *
 * STATISTICS COMPLIANCE:
 * ✅ NO hidden ->take() or ->limit() in methods
 * ✅ All data limits must be explicit in method signatures
 * ✅ Full dataset returned by default
 *
 * @package App\Services\PaActs
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - N.A.T.A.N. Statistics)
 * @date 2025-10-15
 * @purpose Real-time analytics for PA administrative acts
 */
class PaActStatisticsService
{
    protected UltraLogManager $logger;

    public function __construct(UltraLogManager $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Get comprehensive dashboard statistics for PA user
     *
     * @param User $user PA Entity user
     * @return array Complete statistics array
     */
    public function getDashboardStats(User $user): array
    {
        $this->logger->debug('[PaActStatisticsService] Fetching dashboard stats', [
            'user_id' => $user->id
        ]);

        return [
            // Basic counts
            'total' => $this->getTotalActs($user),
            'anchored' => $this->getAnchoredActs($user),
            'pending' => $this->getPendingActs($user),
            'failed' => $this->getFailedActs($user),

            // Tokenization metrics
            'tokenization' => $this->getTokenizationMetrics($user),

            // Document type distribution
            'by_doc_type' => $this->getDocumentTypeDistribution($user),

            // Temporal metrics
            'this_month' => $this->getMonthlyStats($user, Carbon::now()),
            'last_month' => $this->getMonthlyStats($user, Carbon::now()->subMonth()),

            // Performance metrics
            'avg_tokenization_time' => $this->getAverageTokenizationTime($user),
            'success_rate' => $this->getSuccessRate($user),
        ];
    }

    /**
     * Get total PA acts count
     *
     * @param User $user
     * @return int
     */
    public function getTotalActs(User $user): int
    {
        return Egi::whereHas('collection', fn($q) => $q->where('creator_id', $user->id))
            ->where('type', 'pa_act')
            ->whereNotNull('pa_protocol_number')
            ->count();
    }

    /**
     * Get successfully anchored acts count
     *
     * @param User $user
     * @return int
     */
    public function getAnchoredActs(User $user): int
    {
        return Egi::whereHas('collection', fn($q) => $q->where('creator_id', $user->id))
            ->where('type', 'pa_act')
            ->where('pa_anchored', true)
            ->where('pa_tokenization_status', 'completed')
            ->count();
    }

    /**
     * Get pending tokenization acts count
     *
     * @param User $user
     * @return int
     */
    public function getPendingActs(User $user): int
    {
        return Egi::whereHas('collection', fn($q) => $q->where('creator_id', $user->id))
            ->where('type', 'pa_act')
            ->whereIn('pa_tokenization_status', ['pending', 'processing'])
            ->count();
    }

    /**
     * Get failed tokenization acts count
     *
     * @param User $user
     * @return int
     */
    public function getFailedActs(User $user): int
    {
        return Egi::whereHas('collection', fn($q) => $q->where('creator_id', $user->id))
            ->where('type', 'pa_act')
            ->where('pa_tokenization_status', 'failed')
            ->count();
    }

    /**
     * Get tokenization performance metrics
     *
     * @param User $user
     * @return array
     */
    public function getTokenizationMetrics(User $user): array
    {
        $total = $this->getTotalActs($user);
        $completed = $this->getAnchoredActs($user);
        $failed = $this->getFailedActs($user);
        $pending = $this->getPendingActs($user);

        $successRate = $total > 0 ? round(($completed / $total) * 100, 1) : 0;
        $failureRate = $total > 0 ? round(($failed / $total) * 100, 1) : 0;

        return [
            'total_attempts' => $total,
            'completed' => $completed,
            'failed' => $failed,
            'pending' => $pending,
            'success_rate' => $successRate,
            'failure_rate' => $failureRate,
            'retry_needed' => $failed, // Acts that need manual retry
        ];
    }

    /**
     * Get document type distribution
     *
     * @param User $user
     * @return array
     */
    public function getDocumentTypeDistribution(User $user): array
    {
        $distribution = Egi::whereHas('collection', fn($q) => $q->where('creator_id', $user->id))
            ->where('type', 'pa_act')
            ->whereNotNull('pa_act_type')
            ->select('pa_act_type', DB::raw('count(*) as count'))
            ->groupBy('pa_act_type')
            ->orderBy('count', 'desc')
            ->get()
            ->mapWithKeys(fn($item) => [$item->pa_act_type => $item->count])
            ->toArray();

        return $distribution;
    }

    /**
     * Get monthly statistics for a specific month
     *
     * @param User $user
     * @param Carbon $month
     * @return array
     */
    public function getMonthlyStats(User $user, Carbon $month): array
    {
        $startOfMonth = $month->copy()->startOfMonth();
        $endOfMonth = $month->copy()->endOfMonth();

        $uploaded = Egi::whereHas('collection', fn($q) => $q->where('creator_id', $user->id))
            ->where('type', 'pa_act')
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->count();

        $anchored = Egi::whereHas('collection', fn($q) => $q->where('creator_id', $user->id))
            ->where('type', 'pa_act')
            ->where('pa_anchored', true)
            ->whereBetween('pa_anchored_at', [$startOfMonth, $endOfMonth])
            ->count();

        return [
            'month' => $month->format('Y-m'),
            'month_label' => $month->translatedFormat('F Y'),
            'uploaded' => $uploaded,
            'anchored' => $anchored,
        ];
    }

    /**
     * Get average tokenization time (in seconds)
     *
     * @param User $user
     * @return float|null Average time in seconds, null if no data
     */
    public function getAverageTokenizationTime(User $user): ?float
    {
        $acts = Egi::whereHas('collection', fn($q) => $q->where('creator_id', $user->id))
            ->where('type', 'pa_act')
            ->where('pa_anchored', true)
            ->whereNotNull('pa_anchored_at')
            ->select('created_at', 'pa_anchored_at')
            ->get();

        if ($acts->isEmpty()) {
            return null;
        }

        $totalSeconds = $acts->sum(function ($act) {
            return $act->created_at->diffInSeconds($act->pa_anchored_at);
        });

        return round($totalSeconds / $acts->count(), 1);
    }

    /**
     * Get success rate percentage
     *
     * @param User $user
     * @return float
     */
    public function getSuccessRate(User $user): float
    {
        $total = $this->getTotalActs($user);
        if ($total === 0) {
            return 0.0;
        }

        $completed = $this->getAnchoredActs($user);
        return round(($completed / $total) * 100, 1);
    }

    /**
     * Get monthly trend data for charts (last N months)
     *
     * @param User $user
     * @param int $months Number of months to retrieve
     * @return array
     */
    public function getMonthlyTrends(User $user, int $months = 6): array
    {
        $trends = [];
        $now = Carbon::now();

        for ($i = $months - 1; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i);
            $trends[] = $this->getMonthlyStats($user, $month);
        }

        return $trends;
    }

    /**
     * Get recent failed acts for retry dashboard widget
     *
     * @param User $user
     * @param int|null $limit Optional limit (explicit)
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRecentFailedActs(User $user, ?int $limit = 5): \Illuminate\Database\Eloquent\Collection
    {
        $query = Egi::whereHas('collection', fn($q) => $q->where('creator_id', $user->id))
            ->where('type', 'pa_act')
            ->where('pa_tokenization_status', 'failed')
            ->whereNotNull('pa_tokenization_error')
            ->orderBy('updated_at', 'desc');

        if ($limit !== null) {
            $query->limit($limit);
        }

        return $query->get();
    }

    /**
     * Get acts requiring attention (failed + high retry attempts)
     *
     * @param User $user
     * @return array
     */
    public function getActsRequiringAttention(User $user): array
    {
        $failed = $this->getFailedActs($user);

        $highRetryAttempts = Egi::whereHas('collection', fn($q) => $q->where('creator_id', $user->id))
            ->where('type', 'pa_act')
            ->where('pa_tokenization_attempts', '>=', 2)
            ->whereIn('pa_tokenization_status', ['pending', 'failed'])
            ->count();

        return [
            'failed_tokenization' => $failed,
            'high_retry_attempts' => $highRetryAttempts,
            'total_attention_needed' => $failed + $highRetryAttempts,
        ];
    }
}

