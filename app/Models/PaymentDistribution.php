<?php

namespace App\Models;

use App\Enums\PaymentDistribution\UserTypeEnum;
use App\Enums\PaymentDistribution\DistributionStatusEnum;
use App\Helpers\DatabaseHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

/**
 * @Oracode Model: Payment Distribution
 * 🎯 Purpose: Core payment distribution tracking for FlorenceEGI
 * 🛡️ Privacy: Financial distribution with GDPR compliance integration
 * 🧱 Core Logic: Percentage-based automatic distribution system
 *
 * @package App\Models
 * @author GitHub Copilot for Fabio Cherici
 * @version 1.0.0
 * @date 2025-08-20
 *
 * @property int $id
 * @property int $reservation_id
 * @property int $collection_id
 * @property int $user_id
 * @property UserTypeEnum $user_type
 * @property DistributionStatusEnum $distribution_status
 * @property float $percentage
 * @property float $amount_eur
 * @property float $exchange_rate
 * @property bool $is_epp
 * @property array $metadata
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class PaymentDistribution extends Model {
    use HasFactory;

    /**
     * The table associated with the model
     * @var string
     */
    protected $table = 'payment_distributions';

    /**
     * The attributes that are mass assignable
     * @var array<string>
     */
    protected $fillable = [
        'source_type',           // Phase 2: mint/reservation/transfer/rebind tracking
        'egi_id',                // Direct EGI reference (always set)
        'reservation_id',
        'egi_blockchain_id',     // Phase 2: Link to blockchain record (mint tracking)
        'blockchain_tx_id',      // Phase 2: Algorand transaction ID (mint tracking)
        'collection_id',
        'payment_intent_id',     // NEW: Stripe Payment Intent ID for de-duplication
        'user_id',
        'seller_user_id',        // Rebind: User selling the EGI (NULL for primary mint)
        'buyer_user_id',         // Rebind: User purchasing the EGI
        'sale_price',            // Rebind: Total sale price for ownership history
        'wallet_id',             // Reference to wallet (SOURCE OF TRUTH for platform_role)
        'user_type',             // Account type (weak, creator, etc.) - backward compat
        'platform_role',         // Wallet role (Natan, EPP, Frangette, Creator) - NEW SOURCE OF TRUTH
        'percentage',
        'amount_eur',
        'amount_cents',          // Amount in cents
        'exchange_rate',
        'is_epp',
        'metadata',
        'distribution_status',
        'transfer_id',           // Stripe transfer ID
        'reversal_id',           // Stripe reversal ID
        'failure_reason',        // Failure reason for failed transfers
        'retry_count',           // Number of retry attempts
        'completed_at',          // When distribution was completed
        'reversed_at',           // When distribution was reversed
        'idempotency_key',       // Idempotency key for Stripe operations
    ];

    /**
     * The attributes that should be cast
     * @var array<string, string>
     */
    protected $casts = [
        'user_type' => UserTypeEnum::class,
        'distribution_status' => DistributionStatusEnum::class,
        'percentage' => 'decimal:2',
        'amount_eur' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'exchange_rate' => 'decimal:10',
        'is_epp' => 'boolean',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ===== RELATIONSHIPS =====

    /**
     * Get the EGI that owns this distribution (direct reference)
     * @return BelongsTo
     */
    public function egi(): BelongsTo {
        return $this->belongsTo(Egi::class);
    }

    /**
     * Get the reservation that owns this distribution
     * @return BelongsTo
     */
    public function reservation(): BelongsTo {
        return $this->belongsTo(Reservation::class);
    }

    /**
     * Get the collection that owns this distribution
     * @return BelongsTo
     */
    public function collection(): BelongsTo {
        return $this->belongsTo(Collection::class);
    }

    /**
     * Get the user that receives this distribution
     * @return BelongsTo
     */
    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the wallet associated with this distribution
     * @return BelongsTo
     */
    public function wallet(): BelongsTo {
        return $this->belongsTo(Wallet::class);
    }

    /**
     * Get the seller user for rebind transactions
     * @return BelongsTo
     */
    public function seller(): BelongsTo {
        return $this->belongsTo(User::class, 'seller_user_id');
    }

    /**
     * Get the buyer user for rebind transactions
     * @return BelongsTo
     */
    public function buyer(): BelongsTo {
        return $this->belongsTo(User::class, 'buyer_user_id');
    }

    // ===== SCOPES FOR ANALYTICS =====

    /**
     * Scope for reservation-based distributions
     * @param Builder $query
     * @return Builder
     */
    public function scopeReservationSource(Builder $query): Builder {
        return $query->where('source_type', 'reservation');
    }

    /**
     * Scope for mint-based distributions (Phase 2)
     * @param Builder $query
     * @return Builder
     */
    public function scopeMintSource(Builder $query): Builder {
        return $query->where('source_type', 'mint');
    }

    /**
     * Scope for rebind-based distributions (Secondary Market)
     * @param Builder $query
     * @return Builder
     */
    public function scopeRebindSource(Builder $query): Builder {
        return $query->where('source_type', 'rebind');
    }

    /**
     * Scope for confirmed distributions (payment completed)
     * @param Builder $query
     * @return Builder
     */
    public function scopeConfirmed(Builder $query): Builder {
        return $query->where('distribution_status', DistributionStatusEnum::CONFIRMED);
    }

    /**
     * Scope for specific user type
     * @param Builder $query
     * @param UserTypeEnum $userType
     * @return Builder
     */
    public function scopeByUserType(Builder $query, UserTypeEnum $userType): Builder {
        return $query->where('user_type', $userType);
    }

    /**
     * Scope for specific collection
     * @param Builder $query
     * @param int $collectionId
     * @return Builder
     */
    public function scopeByCollection(Builder $query, int $collectionId): Builder {
        return $query->where('collection_id', $collectionId);
    }

    /**
     * Scope for EPP distributions only
     * @param Builder $query
     * @return Builder
     */
    public function scopeIsEPP(Builder $query): Builder {
        return $query->where('is_epp', true);
    }

    /**
     * Scope for non-EPP distributions
     * @param Builder $query
     * @return Builder
     */
    public function scopeNotEPP(Builder $query): Builder {
        return $query->where('is_epp', false);
    }

    /**
     * Scope for specific status
     * @param Builder $query
     * @param DistributionStatusEnum $status
     * @return Builder
     */
    public function scopeByStatus(Builder $query, DistributionStatusEnum $status): Builder {
        return $query->where('distribution_status', $status);
    }

    /**
     * Scope for date range
     * @param Builder $query
     * @param string $startDate
     * @param string $endDate
     * @return Builder
     */
    public function scopeByDateRange(Builder $query, string $startDate, string $endDate): Builder {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    // ===== BUSINESS LOGIC METHODS =====

    /**
     * Calculate total distributions for a reservation
     * @param int $reservationId
     * @return float
     */
    public static function getTotalForReservation(int $reservationId): float {
        return static::where('reservation_id', $reservationId)->sum('amount_eur');
    }

    /**
     * Get percentage total for a reservation (should be 100%)
     * @param int $reservationId
     * @return float
     */
    public static function getPercentageTotalForReservation(int $reservationId): float {
        return static::where('reservation_id', $reservationId)->sum('percentage');
    }

    /**
     * Get EPP impact for a collection
     * @param int $collectionId
     * @return float
     */
    public static function getEppImpactForCollection(int $collectionId): float {
        return static::where('collection_id', $collectionId)
            ->where('is_epp', true)
            ->sum('amount_eur');
    }

    /**
     * Get user earnings by type
     * @param UserTypeEnum $userType
     * @return float
     */
    public static function getUserTypeEarnings(UserTypeEnum $userType): float {
        return static::where('user_type', $userType)->sum('amount_eur');
    }

    /**
     * Check if percentages are valid for reservation
     * @param int $reservationId
     * @return bool
     */
    public static function validatePercentagesForReservation(int $reservationId): bool {
        $total = static::getPercentageTotalForReservation($reservationId);
        return abs($total - 100.00) < 0.01; // Allow for floating point precision
    }

    // ===== ACCESSOR METHODS =====

    /**
     * Get formatted amount in EUR
     * @return string
     */
    public function getFormattedAmountAttribute(): string {
        return '€ ' . number_format($this->amount_eur, 2);
    }

    /**
     * Get formatted percentage
     * @return string
     */
    public function getFormattedPercentageAttribute(): string {
        return number_format($this->percentage, 2) . '%';
    }

    /**
     * Get user type display name
     * @return string
     */
    public function getUserTypeDisplayAttribute(): string {
        return $this->user_type->getDisplayName();
    }

    /**
     * Get status display name
     * @return string
     */
    public function getStatusDisplayAttribute(): string {
        return $this->distribution_status->getDisplayName();
    }

    // ================================
    // 📊 STATISTICS METHODS
    // ================================

    /**
     * Get total number of distributions created
     * @return int
     */
    public static function getTotalDistributionsCount(): int {
        return static::count();
    }

    /**
     * Get total amount distributed in EUR - Only from highest sub_status reservations
     * @return float
     */
    public static function getTotalAmountDistributed(): float {
        return static::join('reservations', 'payment_distributions.reservation_id', '=', 'reservations.id')
            ->where('reservations.sub_status', 'highest')
            ->sum('payment_distributions.amount_eur') ?? 0.0;
    }

    /**
     * Get average distribution amount
     * @return float
     */
    public static function getAverageDistributionAmount(): float {
        return static::avg('amount_eur') ?? 0.0;
    }

    /**
     * Get distributions grouped by period (day/week/month)
     * @param string $period ('day', 'week', 'month')
     * @param int $limit Number of periods to return
     * @return array
     */
    public static function getDistributionsByPeriod(string $period = 'day', int $limit = 30): array {
        $dateFormat = match ($period) {
            'day' => '%Y-%m-%d',
            'week' => '%Y-%u',
            'month' => '%Y-%m',
            default => '%Y-%m-%d'
        };

        $dateFormatSql = \App\Helpers\DatabaseHelper::dateFormat('created_at', $dateFormat);

        return static::selectRaw("
                {$dateFormatSql} as period,
                COUNT(*) as count,
                SUM(amount_eur) as total_amount,
                AVG(amount_eur) as avg_amount
            ")
            ->groupByRaw($dateFormatSql)
            ->orderByRaw("{$dateFormatSql} DESC")
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Get distributions totals grouped by user type - Only from highest sub_status reservations
     * @return array
     */
    public static function getTotalByUserType(): array {
        return static::join('reservations', 'payment_distributions.reservation_id', '=', 'reservations.id')
            ->where('reservations.sub_status', 'highest')
            ->selectRaw('
                payment_distributions.user_type,
                COUNT(*) as count,
                SUM(payment_distributions.amount_eur) as total_amount,
                AVG(payment_distributions.amount_eur) as avg_amount,
                AVG(payment_distributions.percentage) as avg_percentage,
                MIN(payment_distributions.percentage) as min_percentage,
                MAX(payment_distributions.percentage) as max_percentage
            ')
            ->groupBy('payment_distributions.user_type')
            ->orderBy('total_amount', 'DESC')
            ->get()
            ->map(function ($item) {
                return [
                    'user_type' => $item->user_type->value, // Convert enum to string
                    'count' => $item->count,
                    'total_amount' => round($item->total_amount, 2),
                    'avg_amount' => round($item->avg_amount, 2),
                    'avg_percentage' => round($item->avg_percentage, 2),
                    'min_percentage' => round($item->min_percentage, 2),
                    'max_percentage' => round($item->max_percentage, 2),
                ];
            })
            ->toArray();
    }

    /**
     * Get top users that generate most distributions for EPP type
     * @param int $limit
     * @return array
     */
    public static function getTopUsersForEPP(int $limit = 10): array {
        return static::join('reservations', 'payment_distributions.reservation_id', '=', 'reservations.id')
            ->join('users', 'reservations.user_id', '=', 'users.id')
            ->where('payment_distributions.user_type', UserTypeEnum::EPP)
            ->selectRaw('
                users.id as user_id,
                users.name as user_name,
                users.email as user_email,
                COUNT(payment_distributions.id) as distributions_count,
                SUM(payment_distributions.amount_eur) as total_epp_amount,
                AVG(payment_distributions.amount_eur) as avg_epp_amount,
                AVG(payment_distributions.percentage) as avg_epp_percentage
            ')
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderBy('total_epp_amount', 'DESC')
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                return [
                    'user_id' => $item->user_id,
                    'user_name' => $item->user_name,
                    'user_email' => $item->user_email,
                    'distributions_count' => $item->distributions_count,
                    'total_epp_amount' => round($item->total_epp_amount, 2),
                    'avg_epp_amount' => round($item->avg_epp_amount, 2),
                    'avg_epp_percentage' => round($item->avg_epp_percentage, 2),
                ];
            })
            ->toArray();
    }

    /**
     * Get total amount distributed per collection
     * @param int $limit
     * @return array
     */
    public static function getTotalByCollection(int $limit = 20): array {
        return static::join('reservations', 'payment_distributions.reservation_id', '=', 'reservations.id')
            ->join('egis', 'reservations.egi_id', '=', 'egis.id')
            ->join('collections', 'egis.collection_id', '=', 'collections.id')
            ->selectRaw("
                collections.id as collection_id,
                collections.collection_name as collection_name,
                COUNT(DISTINCT payment_distributions.reservation_id) as reservations_count,
                COUNT(payment_distributions.id) as distributions_count,
                SUM(payment_distributions.amount_eur) as total_distributed,
                AVG(payment_distributions.amount_eur) as avg_distribution,
                SUM(CASE WHEN payment_distributions.user_type = 'creator' THEN payment_distributions.amount_eur ELSE 0 END) as total_to_creators,
                SUM(CASE WHEN payment_distributions.user_type = 'epp' THEN payment_distributions.amount_eur ELSE 0 END) as total_to_epp,
                SUM(CASE WHEN payment_distributions.user_type = 'collector' THEN payment_distributions.amount_eur ELSE 0 END) as total_to_collectors
            ")
            ->groupBy('collections.id', 'collections.collection_name')
            ->orderBy('total_distributed', 'DESC')
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                return [
                    'collection_id' => $item->collection_id,
                    'collection_name' => $item->collection_name,
                    'reservations_count' => $item->reservations_count,
                    'distributions_count' => $item->distributions_count,
                    'total_distributed' => round($item->total_distributed, 2),
                    'avg_distribution' => round($item->avg_distribution, 2),
                    'total_to_creators' => round($item->total_to_creators, 2),
                    'total_to_epp' => round($item->total_to_epp, 2),
                    'total_to_collectors' => round($item->total_to_collectors, 2),
                ];
            })
            ->toArray();
    }

    /**
     * Get most profitable collections
     * @param int $limit
     * @return array
     */
    public static function getMostProfitableCollections(int $limit = 10): array {
        return static::getTotalByCollection($limit);
    }

    /**
     * Get ROI per collection (simplified calculation)
     * @param int $limit
     * @return array
     */
    public static function getCollectionROI(int $limit = 10): array {
        return static::join('reservations', 'payment_distributions.reservation_id', '=', 'reservations.id')
            ->join('egis', 'reservations.egi_id', '=', 'egis.id')
            ->join('collections', 'egis.collection_id', '=', 'collections.id')
            ->selectRaw('
                collections.id as collection_id,
                collections.collection_name as collection_name,
                collections.floor_price as floor_price,
                COUNT(DISTINCT payment_distributions.reservation_id) as reservations_count,
                SUM(payment_distributions.amount_eur) as total_distributed,
                SUM(reservations.amount_eur) as total_reservations_value,
                (SUM(payment_distributions.amount_eur) / NULLIF(collections.floor_price, 0)) * 100 as roi_percentage
            ')
            ->groupBy('collections.id', 'collections.collection_name', 'collections.floor_price')
            ->having('collections.floor_price', '>', 0)
            ->orderBy('roi_percentage', 'DESC')
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                return [
                    'collection_id' => $item->collection_id,
                    'collection_name' => $item->collection_name,
                    'floor_price' => round($item->floor_price, 2),
                    'reservations_count' => $item->reservations_count,
                    'total_distributed' => round($item->total_distributed, 2),
                    'total_reservations_value' => round($item->total_reservations_value, 2),
                    'roi_percentage' => round($item->roi_percentage, 2),
                ];
            })
            ->toArray();
    }

    /**
     * Get comprehensive statistics dashboard
     * @return array
     */
    public static function getDashboardStats(): array {
        return [
            'overview' => [
                'total_distributions' => static::getTotalDistributionsCount(),
                'total_amount_distributed' => static::getTotalAmountDistributed(),
                'average_distribution' => static::getAverageDistributionAmount(),
            ],
            'by_user_type' => static::getTotalByUserType(),
            'recent_activity' => static::getDistributionsByPeriod('day', 7),
            'top_collections' => static::getMostProfitableCollections(5),
            'top_epp_generators' => static::getTopUsersForEPP(5),
        ];
    }

    // ================================
    // 🎨 CREATOR-SPECIFIC STATISTICS
    // ================================

    /**
     * Get total earnings for a specific creator
     * @param int $creatorId
     * @return array
     */
    public static function getCreatorEarnings(int $creatorId): array {
        $earnings = static::join('reservations', 'payment_distributions.reservation_id', '=', 'reservations.id')
            ->join('egis', 'reservations.egi_id', '=', 'egis.id')
            ->join('collections', 'egis.collection_id', '=', 'collections.id')
            ->where('collections.creator_id', $creatorId)
            ->where('payment_distributions.user_type', UserTypeEnum::CREATOR)
            ->where('reservations.sub_status', 'highest')
            ->selectRaw('
                COUNT(DISTINCT payment_distributions.id) as total_distributions,
                SUM(payment_distributions.amount_eur) as total_earnings,
                AVG(payment_distributions.amount_eur) as avg_earnings_per_distribution,
                MIN(payment_distributions.amount_eur) as min_earnings,
                MAX(payment_distributions.amount_eur) as max_earnings,
                COUNT(DISTINCT reservations.id) as total_sales,
                COUNT(DISTINCT egis.collection_id) as collections_with_sales
            ')
            ->first();

        return [
            'total_earnings' => round($earnings->total_earnings ?? 0, 2),
            'total_distributions' => $earnings->total_distributions ?? 0,
            'total_sales' => $earnings->total_sales ?? 0,
            'avg_earnings_per_distribution' => round($earnings->avg_earnings_per_distribution ?? 0, 2),
            'avg_earnings_per_sale' => $earnings->total_sales > 0 ? round($earnings->total_earnings / $earnings->total_sales, 2) : 0,
            'min_earnings' => round($earnings->min_earnings ?? 0, 2),
            'max_earnings' => round($earnings->max_earnings ?? 0, 2),
            'collections_with_sales' => $earnings->collections_with_sales ?? 0,
        ];
    }

    /**
     * Get monthly earnings trend for a creator
     * @param int $creatorId
     * @param int $months Number of months to retrieve
     * @return array
     */
    public static function getCreatorMonthlyEarnings(int $creatorId, int $months = 12): array {
        $monthFormat = \App\Helpers\DatabaseHelper::dateFormat('payment_distributions.created_at', '%Y-%m');

        return static::join('reservations', 'payment_distributions.reservation_id', '=', 'reservations.id')
            ->join('egis', 'reservations.egi_id', '=', 'egis.id')
            ->join('collections', 'egis.collection_id', '=', 'collections.id')
            ->where('collections.creator_id', $creatorId)
            ->where('payment_distributions.user_type', UserTypeEnum::CREATOR)
            ->where('reservations.sub_status', 'highest')
            ->where('payment_distributions.created_at', '>=', now()->subMonths($months))
            ->selectRaw("
                {$monthFormat} as month,
                COUNT(payment_distributions.id) as distributions_count,
                SUM(payment_distributions.amount_eur) as monthly_earnings,
                AVG(payment_distributions.amount_eur) as avg_earnings,
                COUNT(DISTINCT reservations.id) as sales_count
            ")
            ->groupByRaw($monthFormat)
            ->orderByRaw("{$monthFormat} DESC")
            ->limit($months)
            ->get()
            ->map(function ($item) {
                return [
                    'month' => $item->month,
                    'distributions_count' => $item->distributions_count,
                    'monthly_earnings' => round($item->monthly_earnings, 2),
                    'avg_earnings' => round($item->avg_earnings, 2),
                    'sales_count' => $item->sales_count,
                ];
            })
            ->toArray();
    }

    /**
     * Get earnings performance by collection for a creator
     * @param int $creatorId
     * @param int $limit
     * @return array
     */
    public static function getCreatorCollectionPerformance(int $creatorId, int $limit = 10): array {
        return static::join('reservations', 'payment_distributions.reservation_id', '=', 'reservations.id')
            ->join('egis', 'reservations.egi_id', '=', 'egis.id')
            ->join('collections', 'egis.collection_id', '=', 'collections.id')
            ->where('collections.creator_id', $creatorId)
            ->where('payment_distributions.user_type', UserTypeEnum::CREATOR)
            ->where('reservations.sub_status', 'highest')
            ->selectRaw('
                collections.id as collection_id,
                collections.collection_name as collection_name,
                collections.floor_price as floor_price,
                COUNT(DISTINCT payment_distributions.id) as distributions_count,
                SUM(payment_distributions.amount_eur) as total_earnings,
                AVG(payment_distributions.amount_eur) as avg_earnings,
                COUNT(DISTINCT reservations.id) as sales_count,
                COUNT(DISTINCT egis.id) as egis_sold,
                MAX(payment_distributions.amount_eur) as best_sale
            ')
            ->groupBy('collections.id', 'collections.collection_name', 'collections.floor_price')
            ->orderBy('total_earnings', 'DESC')
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                return [
                    'collection_id' => $item->collection_id,
                    'collection_name' => $item->collection_name,
                    'floor_price' => round($item->floor_price, 2),
                    'distributions_count' => $item->distributions_count,
                    'total_earnings' => round($item->total_earnings, 2),
                    'avg_earnings' => round($item->avg_earnings, 2),
                    'sales_count' => $item->sales_count,
                    'egis_sold' => $item->egis_sold,
                    'best_sale' => round($item->best_sale, 2),
                    'conversion_rate' => $item->egis_sold > 0 ? round(($item->sales_count / $item->egis_sold) * 100, 2) : 0,
                ];
            })
            ->toArray();
    }

    /**
     * Get creator engagement and impact statistics
     * @param int $creatorId
     * @return array
     */
    public static function getCreatorEngagementStats(int $creatorId): array {
        // Total collectors reached (unique buyers)
        $collectorsReached = static::join('reservations', 'payment_distributions.reservation_id', '=', 'reservations.id')
            ->join('egis', 'reservations.egi_id', '=', 'egis.id')
            ->join('collections', 'egis.collection_id', '=', 'collections.id')
            ->where('collections.creator_id', $creatorId)
            ->where('reservations.sub_status', 'highest')
            ->distinct('reservations.user_id')
            ->count('reservations.user_id');

        // EPP impact generated through creator's works
        $eppImpact = static::join('reservations', 'payment_distributions.reservation_id', '=', 'reservations.id')
            ->join('egis', 'reservations.egi_id', '=', 'egis.id')
            ->join('collections', 'egis.collection_id', '=', 'collections.id')
            ->where('collections.creator_id', $creatorId)
            ->where('payment_distributions.user_type', UserTypeEnum::EPP)
            ->where('reservations.sub_status', 'highest')
            ->sum('payment_distributions.amount_eur');

        // Total volume generated (all distributions from creator's works)
        $totalVolumeGenerated = static::join('reservations', 'payment_distributions.reservation_id', '=', 'reservations.id')
            ->join('egis', 'reservations.egi_id', '=', 'egis.id')
            ->join('collections', 'egis.collection_id', '=', 'collections.id')
            ->where('collections.creator_id', $creatorId)
            ->where('reservations.sub_status', 'highest')
            ->sum('payment_distributions.amount_eur');

        return [
            'collectors_reached' => $collectorsReached,
            'epp_impact_generated' => round($eppImpact, 2),
            'total_volume_generated' => round($totalVolumeGenerated, 2),
            'avg_impact_per_collector' => $collectorsReached > 0 ? round($totalVolumeGenerated / $collectorsReached, 2) : 0,
            'epp_percentage' => $totalVolumeGenerated > 0 ? round(($eppImpact / $totalVolumeGenerated) * 100, 2) : 0,
        ];
    }

    /**
     * Get creator distribution status breakdown
     * @param int $creatorId
     * @return array
     */
    public static function getCreatorDistributionStatus(int $creatorId): array {
        return static::join('reservations', 'payment_distributions.reservation_id', '=', 'reservations.id')
            ->join('egis', 'reservations.egi_id', '=', 'egis.id')
            ->join('collections', 'egis.collection_id', '=', 'collections.id')
            ->where('collections.creator_id', $creatorId)
            ->where('payment_distributions.user_type', UserTypeEnum::CREATOR)
            ->selectRaw('
                payment_distributions.distribution_status,
                COUNT(*) as count,
                SUM(payment_distributions.amount_eur) as total_amount,
                AVG(payment_distributions.amount_eur) as avg_amount
            ')
            ->groupBy('payment_distributions.distribution_status')
            ->get()
            ->map(function ($item) {
                return [
                    'status' => $item->distribution_status->value,
                    'count' => $item->count,
                    'total_amount' => round($item->total_amount, 2),
                    'avg_amount' => round($item->avg_amount, 2),
                ];
            })
            ->toArray();
    }

    /**
     * Get comprehensive creator portfolio statistics
     * @param int $creatorId
     * @return array
     */
    public static function getCreatorPortfolioStats(int $creatorId): array {
        return [
            'earnings' => static::getCreatorEarnings($creatorId),
            'monthly_trend' => static::getCreatorMonthlyEarnings($creatorId, 6),
            'collection_performance' => static::getCreatorCollectionPerformance($creatorId, 5),
            'engagement' => static::getCreatorEngagementStats($creatorId),
            'distribution_status' => static::getCreatorDistributionStatus($creatorId),
        ];
    }

    // ================================
    // 🛒 COLLECTOR-SPECIFIC STATISTICS
    // ================================

    /**
     * Get total spending/investment for a specific collector
     * @param int $collectorId
     * @return array
     */
    public static function getCollectorSpending(int $collectorId): array {
        $spending = static::join('reservations', 'payment_distributions.reservation_id', '=', 'reservations.id')
            ->where('reservations.user_id', $collectorId)
            ->where('payment_distributions.user_type', UserTypeEnum::COLLECTOR)
            ->where('reservations.sub_status', 'highest')
            ->selectRaw('
                COUNT(DISTINCT payment_distributions.id) as total_distributions,
                SUM(payment_distributions.amount_eur) as total_spent,
                AVG(payment_distributions.amount_eur) as avg_spent_per_distribution,
                COUNT(DISTINCT reservations.id) as total_purchases,
                COUNT(DISTINCT payment_distributions.collection_id) as collections_purchased_from
            ')
            ->first();

        return [
            'total_spent' => round($spending->total_spent ?? 0, 2),
            'total_distributions' => $spending->total_distributions ?? 0,
            'total_purchases' => $spending->total_purchases ?? 0,
            'avg_spent_per_distribution' => round($spending->avg_spent_per_distribution ?? 0, 2),
            'avg_spent_per_purchase' => $spending->total_purchases > 0 ? round($spending->total_spent / $spending->total_purchases, 2) : 0,
            'collections_purchased_from' => $spending->collections_purchased_from ?? 0,
        ];
    }

    /**
     * Get EPP impact contributed by a specific collector
     * @param int $collectorId
     * @return array
     */
    public static function getCollectorEPPImpact(int $collectorId): array {
        $eppContributions = static::join('reservations', 'payment_distributions.reservation_id', '=', 'reservations.id')
            ->where('reservations.user_id', $collectorId)
            ->where('payment_distributions.user_type', UserTypeEnum::EPP)
            ->where('reservations.sub_status', 'highest')
            ->selectRaw('
                COUNT(*) as epp_contributions,
                SUM(payment_distributions.amount_eur) as total_epp_impact,
                AVG(payment_distributions.amount_eur) as avg_epp_per_purchase
            ')
            ->first();

        return [
            'epp_contributions' => $eppContributions->epp_contributions ?? 0,
            'total_epp_impact' => round($eppContributions->total_epp_impact ?? 0, 2),
            'avg_epp_per_purchase' => round($eppContributions->avg_epp_per_purchase ?? 0, 2),
        ];
    }

    // ================================
    // 📊 GENERAL USER STATISTICS
    // ================================

    /**
     * Get user statistics regardless of user type
     * @param int $userId
     * @param UserTypeEnum|null $filterUserType Optional filter by specific distribution type
     * @return array
     */
    public static function getUserDistributionStats(int $userId, ?UserTypeEnum $filterUserType = null): array {
        $query = static::join('reservations', 'payment_distributions.reservation_id', '=', 'reservations.id')
            ->where('reservations.user_id', $userId)
            ->where('reservations.sub_status', 'highest');

        if ($filterUserType) {
            $query->where('payment_distributions.user_type', $filterUserType);
        }

        $stats = $query->selectRaw('
                payment_distributions.user_type,
                COUNT(*) as distributions_count,
                SUM(payment_distributions.amount_eur) as total_amount,
                AVG(payment_distributions.amount_eur) as avg_amount,
                MIN(payment_distributions.amount_eur) as min_amount,
                MAX(payment_distributions.amount_eur) as max_amount
            ')
            ->groupBy('payment_distributions.user_type')
            ->get()
            ->map(function ($item) {
                return [
                    'user_type' => $item->user_type->value,
                    'distributions_count' => $item->distributions_count,
                    'total_amount' => round($item->total_amount, 2),
                    'avg_amount' => round($item->avg_amount, 2),
                    'min_amount' => round($item->min_amount, 2),
                    'max_amount' => round($item->max_amount, 2),
                ];
            })
            ->keyBy('user_type')
            ->toArray();

        return $stats;
    }

    /**
     * Get comprehensive user portfolio statistics
     * @param int $userId
     * @param string $userRole ('creator', 'collector', or 'any')
     * @return array
     */
    public static function getUserPortfolioStats(int $userId, string $userRole = 'any'): array {
        $baseStats = [
            'user_id' => $userId,
            'user_role' => $userRole,
            'general_stats' => static::getUserDistributionStats($userId),
            'total_earnings' => static::getUserTotalEarnings($userId),
            'non_creator_earnings' => static::getUserNonCreatorEarnings($userId),
        ];

        switch ($userRole) {
            case 'creator':
                return array_merge($baseStats, [
                    'creator_stats' => static::getCreatorPortfolioStats($userId),
                ]);

            case 'collector':
                return array_merge($baseStats, [
                    'collector_stats' => [
                        'spending' => static::getCollectorSpending($userId),
                        'epp_impact' => static::getCollectorEPPImpact($userId),
                    ],
                ]);

            default:
                // For users with multiple roles or undefined roles
                return array_merge($baseStats, [
                    'collector_stats' => [
                        'spending' => static::getCollectorSpending($userId),
                        'epp_impact' => static::getCollectorEPPImpact($userId),
                    ],
                    // Only add creator stats if user has creator distributions
                    'has_creator_activity' => static::join('reservations', 'payment_distributions.reservation_id', '=', 'reservations.id')
                        ->join('egis', 'reservations.egi_id', '=', 'egis.id')
                        ->join('collections', 'egis.collection_id', '=', 'collections.id')
                        ->where('collections.creator_id', $userId)
                        ->where('payment_distributions.user_type', UserTypeEnum::CREATOR)
                        ->exists(),
                ]);
        }
    }

    // ================================
    // 🔄 USER ROLE-BASED EARNINGS STATISTICS
    // ================================

    /**
     * Get total earnings for a user from all payment distributions (regardless of role)
     * @param int $userId
     * @return array
     */
    public static function getUserTotalEarnings(int $userId): array {
        $earnings = static::where('payment_distributions.user_id', $userId)
            ->join('reservations', 'payment_distributions.reservation_id', '=', 'reservations.id')
            ->where('reservations.sub_status', 'highest')
            ->selectRaw('
                COUNT(payment_distributions.id) as total_distributions,
                SUM(payment_distributions.amount_eur) as total_earnings,
                AVG(payment_distributions.amount_eur) as avg_earning_per_distribution,
                COUNT(DISTINCT payment_distributions.collection_id) as collections_count,
                COUNT(DISTINCT payment_distributions.reservation_id) as reservations_count
            ')
            ->first();

        return [
            'total_earnings' => round($earnings->total_earnings ?? 0, 2),
            'total_distributions' => $earnings->total_distributions ?? 0,
            'avg_earning_per_distribution' => round($earnings->avg_earning_per_distribution ?? 0, 2),
            'collections_involved' => $earnings->collections_count ?? 0,
            'reservations_involved' => $earnings->reservations_count ?? 0,
        ];
    }

    /**
     * Get earnings for a user ONLY from collections where they are NOT the creator
     * Uses collection_user table to find collections where user has a role but is_owner = false
     * @param int $userId
     * @return array
     */
    public static function getUserNonCreatorEarnings(int $userId): array {
        $earnings = static::where('payment_distributions.user_id', $userId)
            ->join('reservations', 'payment_distributions.reservation_id', '=', 'reservations.id')
            ->join('collection_user', function ($join) use ($userId) {
                $join->on('payment_distributions.collection_id', '=', 'collection_user.collection_id')
                    ->where('collection_user.user_id', '=', $userId)
                    ->where('collection_user.is_owner', '=', false);
            })
            ->join('collections', 'payment_distributions.collection_id', '=', 'collections.id')
            ->where('reservations.sub_status', 'highest')
            ->selectRaw('
                COUNT(payment_distributions.id) as total_distributions,
                SUM(payment_distributions.amount_eur) as total_earnings,
                AVG(payment_distributions.amount_eur) as avg_earning_per_distribution,
                COUNT(DISTINCT payment_distributions.collection_id) as collections_count,
                COUNT(DISTINCT payment_distributions.reservation_id) as reservations_count,
                ' . DatabaseHelper::groupConcat('collection_user.role', ',', true) . ' as roles_held
            ')
            ->first();

        // Get detailed breakdown by collection
        $collectionBreakdown = static::where('payment_distributions.user_id', $userId)
            ->join('reservations', 'payment_distributions.reservation_id', '=', 'reservations.id')
            ->join('collection_user', function ($join) use ($userId) {
                $join->on('payment_distributions.collection_id', '=', 'collection_user.collection_id')
                    ->where('collection_user.user_id', '=', $userId)
                    ->where('collection_user.is_owner', '=', false);
            })
            ->join('collections', 'payment_distributions.collection_id', '=', 'collections.id')
            ->where('reservations.sub_status', 'highest')
            ->selectRaw('
                collections.id as collection_id,
                collections.collection_name,
                collection_user.role,
                COUNT(payment_distributions.id) as distributions_count,
                SUM(payment_distributions.amount_eur) as earnings_from_collection,
                AVG(payment_distributions.amount_eur) as avg_earning
            ')
            ->groupBy('collections.id', 'collections.collection_name', 'collection_user.role')
            ->orderBy('earnings_from_collection', 'DESC')
            ->get()
            ->map(function ($item) {
                return [
                    'collection_id' => $item->collection_id,
                    'collection_name' => $item->collection_name,
                    'role' => $item->role,
                    'distributions_count' => $item->distributions_count,
                    'earnings_from_collection' => round($item->earnings_from_collection, 2),
                    'avg_earning' => round($item->avg_earning, 2),
                ];
            })
            ->toArray();

        return [
            'total_earnings' => round($earnings->total_earnings ?? 0, 2),
            'total_distributions' => $earnings->total_distributions ?? 0,
            'avg_earning_per_distribution' => round($earnings->avg_earning_per_distribution ?? 0, 2),
            'collections_involved' => $earnings->collections_count ?? 0,
            'reservations_involved' => $earnings->reservations_count ?? 0,
            'roles_held' => $earnings->roles_held ? explode(',', $earnings->roles_held) : [],
            'collection_breakdown' => $collectionBreakdown,
        ];
    }
}
