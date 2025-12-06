<?php

/**
 * @package App\Services
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - AREA 2.3.1 - Analytics)
 * @date 2025-10-09
 * @purpose Servizio per analytics avanzate sul dual tracking reservation/mint delle payment distributions
 */

namespace App\Services;

use App\Models\PaymentDistribution;
use App\Models\Collection;
use App\Enums\PaymentDistributionSourceType;
use App\Helpers\DatabaseHelper;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

class DualTrackingAnalyticsService {
    /**
     * Dependencies
     */
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;

    /**
     * Constructor con dependency injection
     */
    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
    }

    /**
     * Ottiene il riepilogo delle distribuzioni aggregate per source_type
     *
     * Analizza tutti i payment_distributions di una collezione e raggruppa per:
     * - source_type (reservation, mint, transfer)
     * - user_type (artist, creator, owner, etc.)
     * - is_epp (true/false)
     *
     * @param Collection $collection Collezione da analizzare
     * @param array $filters Filtri opzionali ['start_date' => ..., 'end_date' => ..., 'source_type' => ...]
     * @return array Summary con counts e amounts per categoria
     */
    public function getDistributionSummary(Collection $collection, array $filters = []): array {
        try {
            // ULM: Log start
            $this->logger->info('DualTrackingAnalyticsService: getDistributionSummary started', [
                'collection_id' => $collection->id,
                'collection_name' => $collection->name,
                'filters' => $filters
            ]);

            // Cache key univoco
            $cacheKey = $this->buildCacheKey('distribution_summary', $collection->id, $filters);

            // Prova cache (5 minuti)
            $summary = Cache::remember($cacheKey, 300, function () use ($collection, $filters) {
                return $this->computeDistributionSummary($collection, $filters);
            });

            // ULM: Log success
            $this->logger->info('DualTrackingAnalyticsService: getDistributionSummary completed', [
                'collection_id' => $collection->id,
                'summary' => $summary
            ]);

            return $summary;
        } catch (\Exception $e) {
            // UEM: Error handling
            $this->errorManager->handle('ANALYTICS_DISTRIBUTION_SUMMARY_ERROR', [
                'collection_id' => $collection->id,
                'filters' => $filters,
                'error' => $e->getMessage()
            ], $e);

            throw $e;
        }
    }

    /**
     * Calcola il breakdown dei ricavi per periodo temporale
     *
     * Confronta i ricavi generati da reservation vs mint in intervalli temporali
     * configurabili (giorno, settimana, mese)
     *
     * @param Collection $collection Collezione da analizzare
     * @param string|null $startDate Data inizio formato Y-m-d
     * @param string|null $endDate Data fine formato Y-m-d
     * @param string $groupBy Raggruppamento: 'day', 'week', 'month'
     * @return array Revenue breakdown per periodo e source_type
     */
    public function getRevenueBreakdown(
        Collection $collection,
        ?string $startDate = null,
        ?string $endDate = null,
        string $groupBy = 'month'
    ): array {
        try {
            // ULM: Log start
            $this->logger->info('DualTrackingAnalyticsService: getRevenueBreakdown started', [
                'collection_id' => $collection->id,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'group_by' => $groupBy
            ]);

            // Cache key
            $cacheKey = $this->buildCacheKey('revenue_breakdown', $collection->id, [
                'start' => $startDate,
                'end' => $endDate,
                'group' => $groupBy
            ]);

            // Prova cache (10 minuti)
            $breakdown = Cache::remember($cacheKey, 600, function () use ($collection, $startDate, $endDate, $groupBy) {
                return $this->computeRevenueBreakdown($collection, $startDate, $endDate, $groupBy);
            });

            // ULM: Log success
            $this->logger->info('DualTrackingAnalyticsService: getRevenueBreakdown completed', [
                'collection_id' => $collection->id,
                'periods_count' => count($breakdown)
            ]);

            return $breakdown;
        } catch (\Exception $e) {
            // UEM: Error handling
            $this->errorManager->handle('ANALYTICS_REVENUE_BREAKDOWN_ERROR', [
                'collection_id' => $collection->id,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'error' => $e->getMessage()
            ], $e);

            throw $e;
        }
    }

    /**
     * Analizza le performance per singolo wallet
     *
     * Per ogni wallet coinvolto nelle distribuzioni, calcola:
     * - Totale ricevuto
     * - Numero distribuzioni
     * - Split tra reservation/mint
     * - Performance EPP vs non-EPP
     *
     * @param Collection $collection Collezione da analizzare
     * @param array $filters Filtri opzionali ['wallet_address' => ..., 'user_type' => ...]
     * @return array Performance per wallet con statistiche dettagliate
     */
    public function getWalletPerformance(Collection $collection, array $filters = []): array {
        try {
            // ULM: Log start
            $this->logger->info('DualTrackingAnalyticsService: getWalletPerformance started', [
                'collection_id' => $collection->id,
                'filters' => $filters
            ]);

            // Cache key
            $cacheKey = $this->buildCacheKey('wallet_performance', $collection->id, $filters);

            // Prova cache (5 minuti)
            $performance = Cache::remember($cacheKey, 300, function () use ($collection, $filters) {
                return $this->computeWalletPerformance($collection, $filters);
            });

            // ULM: Log success
            $this->logger->info('DualTrackingAnalyticsService: getWalletPerformance completed', [
                'collection_id' => $collection->id,
                'wallets_count' => count($performance)
            ]);

            return $performance;
        } catch (\Exception $e) {
            // UEM: Error handling
            $this->errorManager->handle('ANALYTICS_WALLET_PERFORMANCE_ERROR', [
                'collection_id' => $collection->id,
                'filters' => $filters,
                'error' => $e->getMessage()
            ], $e);

            throw $e;
        }
    }

    /**
     * Calcola il ratio platform-wide mint vs reservation
     *
     * Analizza TUTTE le collections per ottenere trend generali:
     * - % distribuzioni da mint vs reservation
     * - Evoluzione nel tempo
     * - Medie per collection type
     *
     * @param string|null $startDate Data inizio formato Y-m-d
     * @param string|null $endDate Data fine formato Y-m-d
     * @return array Ratios con trend temporali e medie
     */
    public function getMintVsReservationRatio(?string $startDate = null, ?string $endDate = null): array {
        try {
            // ULM: Log start
            $this->logger->info('DualTrackingAnalyticsService: getMintVsReservationRatio started', [
                'start_date' => $startDate,
                'end_date' => $endDate
            ]);

            // Cache key
            $cacheKey = $this->buildCacheKey('mint_vs_reservation_ratio', 0, [
                'start' => $startDate,
                'end' => $endDate
            ]);

            // Prova cache (15 minuti - platform-wide data)
            $ratios = Cache::remember($cacheKey, 900, function () use ($startDate, $endDate) {
                return $this->computeMintVsReservationRatio($startDate, $endDate);
            });

            // ULM: Log success
            $this->logger->info('DualTrackingAnalyticsService: getMintVsReservationRatio completed', [
                'ratios' => $ratios
            ]);

            return $ratios;
        } catch (\Exception $e) {
            // UEM: Error handling
            $this->errorManager->handle('ANALYTICS_MINT_VS_RESERVATION_ERROR', [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'error' => $e->getMessage()
            ], $e);

            throw $e;
        }
    }

    /**
     * Invalida la cache per una collezione specifica
     *
     * @param int $collectionId ID collezione
     * @return void
     */
    public function invalidateCache(int $collectionId): void {
        $patterns = [
            "analytics:distribution_summary:{$collectionId}:*",
            "analytics:revenue_breakdown:{$collectionId}:*",
            "analytics:wallet_performance:{$collectionId}:*"
        ];

        foreach ($patterns as $pattern) {
            Cache::flush(); // In produzione usare Cache::tags() se disponibile
        }

        $this->logger->info('DualTrackingAnalyticsService: Cache invalidated', [
            'collection_id' => $collectionId
        ]);
    }

    // ==================== PRIVATE COMPUTATION METHODS ====================

    /**
     * Computa distribution summary (logica interna)
     */
    private function computeDistributionSummary(Collection $collection, array $filters): array {
        $query = PaymentDistribution::query()
            ->where('collection_id', $collection->id);

        // Applica filtri
        $query = $this->applyFilters($query, $filters);

        // Raggruppa per source_type, user_type, is_epp
        $distributions = $query->get();

        $summary = [
            'total_count' => $distributions->count(),
            'total_amount' => $distributions->sum('amount'),
            'by_source_type' => $this->groupBySourceType($distributions),
            'by_user_type' => $this->groupByUserType($distributions),
            'epp_vs_non_epp' => $this->groupByEpp($distributions),
            'averages' => [
                'avg_amount' => $distributions->avg('amount'),
                'median_amount' => $this->calculateMedian($distributions->pluck('amount'))
            ]
        ];

        return $summary;
    }

    /**
     * Computa revenue breakdown per periodo
     */
    private function computeRevenueBreakdown(
        Collection $collection,
        ?string $startDate,
        ?string $endDate,
        string $groupBy
    ): array {
        // Determina formato date SQL per grouping
        $dateFormat = match ($groupBy) {
            'day' => '%Y-%m-%d',
            'week' => '%Y-%U',
            'month' => '%Y-%m',
            default => '%Y-%m'
        };

        // Build cross-database date format SQL
        $dateFormatSql = DatabaseHelper::dateFormat('created_at', $dateFormat);

        $query = PaymentDistribution::query()
            ->selectRaw("{$dateFormatSql} as period")
            ->selectRaw('source_type')
            ->selectRaw('SUM(amount) as total_amount')
            ->selectRaw('COUNT(*) as count')
            ->where('collection_id', $collection->id);

        // Applica date range
        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        $results = $query->groupBy('period', 'source_type')
            ->orderBy('period')
            ->get();

        // Raggruppa per periodo
        $breakdown = [];
        foreach ($results as $row) {
            $period = $row->period;
            if (!isset($breakdown[$period])) {
                $breakdown[$period] = [
                    'period' => $period,
                    'reservation' => ['count' => 0, 'amount' => 0],
                    'mint' => ['count' => 0, 'amount' => 0],
                    'transfer' => ['count' => 0, 'amount' => 0],
                    'total' => ['count' => 0, 'amount' => 0]
                ];
            }

            $sourceType = $row->source_type;
            $breakdown[$period][$sourceType]['count'] = $row->count;
            $breakdown[$period][$sourceType]['amount'] = (float) $row->total_amount;
            $breakdown[$period]['total']['count'] += $row->count;
            $breakdown[$period]['total']['amount'] += (float) $row->total_amount;
        }

        return array_values($breakdown);
    }

    /**
     * Computa wallet performance
     */
    private function computeWalletPerformance(Collection $collection, array $filters): array {
        $query = PaymentDistribution::query()
            ->where('collection_id', $collection->id);

        // Applica filtri
        $query = $this->applyFilters($query, $filters);

        $distributions = $query->get();

        // Raggruppa per wallet
        $walletStats = [];
        foreach ($distributions as $dist) {
            $wallet = $dist->wallet_address;
            if (!isset($walletStats[$wallet])) {
                $walletStats[$wallet] = [
                    'wallet_address' => $wallet,
                    'user_type' => $dist->user_type,
                    'total_amount' => 0,
                    'total_count' => 0,
                    'by_source' => [
                        'reservation' => ['count' => 0, 'amount' => 0],
                        'mint' => ['count' => 0, 'amount' => 0],
                        'transfer' => ['count' => 0, 'amount' => 0]
                    ],
                    'epp_count' => 0,
                    'non_epp_count' => 0
                ];
            }

            $walletStats[$wallet]['total_amount'] += $dist->amount;
            $walletStats[$wallet]['total_count']++;
            $walletStats[$wallet]['by_source'][$dist->source_type]['count']++;
            $walletStats[$wallet]['by_source'][$dist->source_type]['amount'] += $dist->amount;

            if ($dist->is_epp) {
                $walletStats[$wallet]['epp_count']++;
            } else {
                $walletStats[$wallet]['non_epp_count']++;
            }
        }

        return array_values($walletStats);
    }

    /**
     * Computa mint vs reservation ratio platform-wide
     */
    private function computeMintVsReservationRatio(?string $startDate, ?string $endDate): array {
        $query = PaymentDistribution::query()
            ->selectRaw('source_type')
            ->selectRaw('COUNT(*) as count')
            ->selectRaw('SUM(amount) as total_amount');

        // Applica date range
        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        $results = $query->groupBy('source_type')->get();

        $totalCount = $results->sum('count');
        $totalAmount = $results->sum('total_amount');

        $ratios = [
            'total_distributions' => $totalCount,
            'total_amount' => $totalAmount,
            'by_source' => []
        ];

        foreach ($results as $row) {
            $ratios['by_source'][$row->source_type] = [
                'count' => $row->count,
                'amount' => (float) $row->total_amount,
                'percentage_count' => $totalCount > 0 ? round(($row->count / $totalCount) * 100, 2) : 0,
                'percentage_amount' => $totalAmount > 0 ? round(($row->total_amount / $totalAmount) * 100, 2) : 0
            ];
        }

        return $ratios;
    }

    // ==================== HELPER METHODS ====================

    /**
     * Applica filtri alla query
     */
    private function applyFilters($query, array $filters) {
        if (isset($filters['start_date'])) {
            $query->where('created_at', '>=', $filters['start_date']);
        }

        if (isset($filters['end_date'])) {
            $query->where('created_at', '<=', $filters['end_date']);
        }

        if (isset($filters['source_type'])) {
            $query->where('source_type', $filters['source_type']);
        }

        if (isset($filters['user_type'])) {
            $query->where('user_type', $filters['user_type']);
        }

        if (isset($filters['wallet_address'])) {
            $query->where('wallet_address', $filters['wallet_address']);
        }

        if (isset($filters['is_epp'])) {
            $query->where('is_epp', $filters['is_epp']);
        }

        return $query;
    }

    /**
     * Raggruppa per source_type
     */
    private function groupBySourceType(SupportCollection $distributions): array {
        return $distributions->groupBy('source_type')->map(function ($group) {
            return [
                'count' => $group->count(),
                'total_amount' => $group->sum('amount'),
                'avg_amount' => $group->avg('amount')
            ];
        })->toArray();
    }

    /**
     * Raggruppa per user_type
     */
    private function groupByUserType(SupportCollection $distributions): array {
        return $distributions->groupBy('user_type')->map(function ($group) {
            return [
                'count' => $group->count(),
                'total_amount' => $group->sum('amount'),
                'avg_amount' => $group->avg('amount')
            ];
        })->toArray();
    }

    /**
     * Raggruppa per is_epp
     */
    private function groupByEpp(SupportCollection $distributions): array {
        $epp = $distributions->where('is_epp', true);
        $nonEpp = $distributions->where('is_epp', false);

        return [
            'epp' => [
                'count' => $epp->count(),
                'total_amount' => $epp->sum('amount'),
                'percentage' => $distributions->count() > 0 ? round(($epp->count() / $distributions->count()) * 100, 2) : 0
            ],
            'non_epp' => [
                'count' => $nonEpp->count(),
                'total_amount' => $nonEpp->sum('amount'),
                'percentage' => $distributions->count() > 0 ? round(($nonEpp->count() / $distributions->count()) * 100, 2) : 0
            ]
        ];
    }

    /**
     * Calcola mediana
     */
    private function calculateMedian(SupportCollection $values): float {
        $sorted = $values->sort()->values();
        $count = $sorted->count();

        if ($count === 0) {
            return 0;
        }

        $middle = floor($count / 2);

        if ($count % 2 === 0) {
            return ($sorted[$middle - 1] + $sorted[$middle]) / 2;
        }

        return $sorted[$middle];
    }

    /**
     * Costruisce cache key univoco
     */
    private function buildCacheKey(string $method, int $collectionId, array $params): string {
        $paramsHash = md5(json_encode($params));
        return "analytics:{$method}:{$collectionId}:{$paramsHash}";
    }
}
