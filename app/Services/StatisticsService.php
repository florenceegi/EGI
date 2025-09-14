<?php

namespace App\Services;

use App\Models\Collection; // Assumendo il namespace corretto
use App\Models\Egi;        // Assumendo il namespace corretto
use App\Models\Like;       // Assumendo il namespace corretto
use App\Models\User;       // O il tuo modello User specifico
use App\Models\Wallet;     // Assumendo il namespace corretto
use Illuminate\Support\Collection as SupportCollection; // Alias per chiarezza
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Ultra\UltraLogManager\UltraLogManager;
use Throwable; // Per catturare eccezioni generiche

/**
 * @Oracode Service: Statistics Calculation
 * 🎯 Purpose: Provides comprehensive statistics for a user's EGI collections.
 *              Calculates metrics related to likes, reservations, financial amounts,
 *              and EPP (Environmental Project Partner) potential.
 *              Implements intelligent caching to optimize performance.
 * 🧱 Core Logic: Fetches data via Eloquent, performs aggregations, applies business rules
 *               (e.g., reservation priority), and structures data for a dashboard.
 * 📡 Dependencies: User model, Collection model, Egi model, Like model, Reservation model,
 *                 Wallet model, UltraLogManager, Illuminate\Support\Facades\Cache,
 *                 Illuminate\Support\Facades\DB.
 * 🛡️ GDPR Scope: Processes user ID to fetch user-specific data. Aggregated statistics
 *                do not directly expose sensitive PII beyond what's necessary for the user
 *                to view their own data. Care is taken not to log sensitive data directly.
 * 🧪 Testing Strategy: Oracular unit tests for each public method, especially
 *                     getComprehensiveStats and individual statistic calculation methods.
 *                     Focus on data accuracy, edge cases (no data, single item),
 *                     correct application of business logic (reservation priority, EPP calculation),
 *                     and cache behavior (hit, miss, clear, force refresh).
 *
 * @version 2.0.0
 * @author Padmin D. Curtis & Fabio Cherici
 */
class StatisticsService {
    protected User $user;
    protected UltraLogManager $logger;
    protected array $userCollectionIds = []; // Cache per gli ID delle collection dell'utente

    private const DEFAULT_EPP_PERCENTAGE = 20.0;
    private const CACHE_TTL_MINUTES = 30;

    /**
     * 🎯 Constructor: Injects dependencies and initializes user context.
     * @param User $user The authenticated user for whom to calculate statistics.
     * @param UltraLogManager $logger For structured, contextual logging.
     *
     * @signature: __construct(User $user, UltraLogManager $logger)
     * @context: Instantiated by StatisticsController, receiving the current user.
     * @log: STATS_SERVICE_INIT - User ID for whom service is initialized.
     * @privacy-safe: Stores user object internally; operations are scoped to this user.
     */
    public function __construct(User $user, UltraLogManager $logger) {
        $this->user = $user;
        $this->logger = $logger;

        // Pre-carica e metti in cache gli ID delle collection dell'utente
        // Questo evita query multiple per gli ID delle collection.
        $this->userCollectionIds = $this->user->ownedCollections()->pluck('id')->all();

        $this->logger->info('StatisticsService initialized', [
            'user_id' => $this->user->id,
            'collection_ids_count' => count($this->userCollectionIds),
            'log_category' => 'STATS_SERVICE_INIT'
        ]);
    }

    /**
     * 🎯 Retrieves or calculates comprehensive statistics for the user.
     * Uses caching with a configurable TTL and supports force refresh.
     *
     * @param bool $forceRefresh If true, bypasses cache and recalculates.
     * @return array Comprehensive statistics data structure.
     *
     * @signature: getComprehensiveStats(bool $forceRefresh = false): array
     * @context: Called by StatisticsController to get data for API/View.
     * @log: STATS_CACHE_CHECK - Cache key, force_refresh status.
     * @log: STATS_CACHE_HIT / STATS_CACHE_MISS / STATS_CACHE_FORCED_REFRESH
     * @log: STATS_CALCULATION_START / STATS_CALCULATION_END
     * @privacy-safe: Operations are on aggregated data or user's own data.
     * @data-output: Structured array of statistics as defined in documentation.
     * @error-boundary: Relies on the controller's try-catch for UEM handling of exceptions.
     *                  Internal exceptions are logged by this service.
     */
    public function getComprehensiveStats(bool $forceRefresh = false): array {
        $cacheKey = 'user_stats_' . $this->user->id;
        // Controlla lo stato della cache *prima* di un potenziale Cache::forget
        $loadedFromCache = !$forceRefresh && Cache::has($cacheKey);

        if ($forceRefresh) {
            Cache::forget($cacheKey);
            $this->logger->info('Forcing statistics refresh, cache cleared.', [
                'user_id' => $this->user->id,
                'cache_key' => $cacheKey,
                'log_category' => 'STATS_CACHE_FORCED_REFRESH'
            ]);
        } else {
            $this->logger->info('Statistics cache check.', [
                'user_id' => $this->user->id,
                'cache_key' => $cacheKey,
                'cache_hit' => $loadedFromCache,
                'log_category' => 'STATS_CACHE_CHECK'
            ]);
        }

        try {
            $stats = Cache::remember($cacheKey, now()->addMinutes(self::CACHE_TTL_MINUTES), function () {
                $this->logger->info('Calculating statistics (cache miss or refresh).', [
                    'user_id' => $this->user->id,
                    'log_category' => 'STATS_CALCULATION_START'
                ]);

                $calculatedStats = $this->calculateAllStatistics();
                $calculatedStats['generated_at'] = now()->toIso8601String();
                $calculatedStats['cache_expires_at'] = now()->addMinutes(self::CACHE_TTL_MINUTES)->toIso8601String();
                // 'loaded_from_cache' sarà impostato fuori dalla closure per riflettere lo stato iniziale.

                $this->logger->info('Statistics calculation inside cache closure finished.', [
                    'user_id' => $this->user->id,
                    'log_category' => 'STATS_CALCULATION_END'
                ]);
                return $calculatedStats;
            });
        } catch (Throwable $e) {
            // @log: Logga l'eccezione specifica del calcolo.
            $this->logger->error('Exception during statistics calculation or caching', [
                'user_id' => $this->user->id,
                'error_message' => $e->getMessage(),
                'exception_class' => get_class($e),
                'exception_file' => $e->getFile(),
                'exception_line' => $e->getLine(),
                'log_category' => 'STATS_CALCULATION_EXCEPTION'
            ]);
            throw $e; // Rilancia l'eccezione per essere gestita dal controller con UEM
        }

        // Aggiunge lo stato di caricamento dalla cache al risultato finale
        $stats['loaded_from_cache'] = $loadedFromCache;
        return $stats;
    }

    /**
     * 🎯 Orchestrates the calculation of all individual statistics components.
     * @return array Aggregated statistics.
     *
     * @signature: calculateAllStatistics(): array
     * @context: Called internally by getComprehensiveStats when cache is missed or refreshed.
     * @privacy-safe: All sub-methods operate on user's own data or aggregates.
     */
    private function calculateAllStatistics(): array {
        if (empty($this->userCollectionIds)) {
            $this->logger->info('User has no collections, returning empty stats.', [
                'user_id' => $this->user->id,
                'log_category' => 'STATS_NO_COLLECTIONS'
            ]);
            return $this->getEmptyStatsStructure();
        }

        $likesStats = $this->getLikesStatistics();
        $reservationsStats = $this->getReservationsStatistics(); // Contiene anche i dati per amounts
        $amountStats = $this->getAmountStatistics($reservationsStats['valid_reservations_for_amount']);
        $eppPotentialStats = $this->getEppPotentialStatistics($reservationsStats['valid_reservations_for_amount']);
        $portfolioStats = $this->getPortfolioStatistics(); // Nuove statistiche portfolio

        $summary = $this->buildSummaryKPIs(
            $likesStats,
            $reservationsStats,
            $amountStats,
            $eppPotentialStats
        );

        return [
            'likes' => $likesStats,
            'reservations' => $reservationsStats, // Rimuovi valid_reservations_for_amount se non serve all'esterno
            'amounts' => $amountStats,
            'epp_potential' => $eppPotentialStats,
            'portfolio' => $portfolioStats, // Aggiunte le statistiche del portfolio
            'summary' => $summary,
        ];
    }

    /**
     * 🎯 Retrieves the IDs of collections owned by the current user.
     * This is now pre-loaded in the constructor.
     *
     * @return array Array of collection IDs.
     * @signature: getUserCollectionIds(): array
     * @context: Helper method used internally.
     * @privacy-safe: Returns IDs related to the authenticated user.
     */
    private function getUserCollectionIds(): array {
        return $this->userCollectionIds;
    }

    /**
     * 🎯 Calculates statistics related to likes on collections and EGIs.
     * @return array Likes statistics.
     *
     * @signature: getLikesStatistics(): array
     * @context: Part of the comprehensive statistics calculation.
     * @log: STATS_LIKES_CALC - Details of like calculation if complex.
     * @privacy-safe: Aggregates like counts.
     * @data-output: Array with total_likes, collection_likes, egi_likes, by_collection, top_egis.
     */
    private function getLikesStatistics(): array {
        // 1. Like diretti alle collection dell'utente
        $collectionLikesCount = Like::where('likeable_type', Collection::class)
            ->whereIn('likeable_id', $this->userCollectionIds)
            ->count();

        // 2. Like agli EGI appartenenti alle collection dell'utente (totale)
        $totalEgiLikesAcrossAllCollections = Like::query()
            ->where('likeable_type', Egi::class)
            ->whereIn('likeable_id', function ($query) {
                $query->select('id')
                    ->from('egis')
                    ->whereIn('collection_id', $this->userCollectionIds);
            })
            ->count();

        $byCollectionStats = [];
        $allEgisForTopRanking = [];

        $collections = Collection::whereIn('id', $this->userCollectionIds)->get(['id', 'collection_name']);

        foreach ($collections as $collection) {
            // Like diretti per QUESTA collezione
            $directLikesToThisCollection = Like::where('likeable_type', Collection::class)
                ->where('likeable_id', $collection->id)
                ->count();

            // Like agli EGI di QUESTA collezione
            $likesOnEgisOfThisCollection = Like::query()
                ->where('likeable_type', Egi::class)
                ->whereIn('likeable_id', function ($query) use ($collection) {
                    $query->select('id')
                        ->from('egis')
                        ->where('collection_id', $collection->id);
                })
                ->count();

            $byCollectionStats[] = [
                'collection_id' => $collection->id,
                'collection_name' => $collection->collection_name,
                'collection_likes' => $directLikesToThisCollection,
                'egi_likes' => $likesOnEgisOfThisCollection,
                'total_likes' => $directLikesToThisCollection + $likesOnEgisOfThisCollection,
            ];

            // Raccogli EGI per il top ranking
            $egisInCollection = Egi::where('collection_id', $collection->id)
                ->withCount('likes') // Aggiunge 'likes_count' ad ogni EGI
                ->orderByDesc('likes_count')
                ->limit(5) // Prendi i top 5 per collezione per ottimizzare, poi faremo un ranking globale
                ->get(['id', 'title']);

            foreach ($egisInCollection as $egi) {
                if ($egi->likes_count > 0) {
                    $allEgisForTopRanking[] = [
                        'id' => $egi->id,
                        'title' => $egi->title,
                        'collection_name' => $collection->collection_name,
                        'likes_count' => $egi->likes_count,
                    ];
                }
            }
        }

        // Ordina globalmente gli EGI e prendi i top 3
        usort($allEgisForTopRanking, fn($a, $b) => $b['likes_count'] <=> $a['likes_count']);
        $topEgis = array_slice($allEgisForTopRanking, 0, 3);

        return [
            'total' => $collectionLikesCount + $totalEgiLikesAcrossAllCollections,
            'collections_total' => $collectionLikesCount,
            'egis_total' => $totalEgiLikesAcrossAllCollections,
            'by_collection' => $byCollectionStats,
            'top_egis' => $topEgis,
        ];
    }

    /**
     * 🎯 Retrieves valid reservations based on priority logic.
     * @return array Reservations statistics, including a collection of valid reservations for other calcs.
     *
     * @signature: getReservationsStatistics(): array
     * @context: Part of the comprehensive statistics calculation.
     * @log: STATS_RESERVATIONS_CALC - Number of valid reservations found.
     * @privacy-safe: Processes reservation data for aggregation.
     * @data-output: Array with total, strong, weak counts, by_collection, by_egi, and 'valid_reservations_for_amount'.
     */
    private function getReservationsStatistics(): array {
        // La query con ROW_NUMBER() è definita qui.
        // Assicurati che la tua versione di MariaDB/MySQL la supporti.
        $sql = "
            WITH RankedReservations AS (
                SELECT
                    r.*,
                    e.collection_id AS egi_collection_id,
                    c.id AS actual_collection_id, c.collection_name, -- Aggiunto c.id per raggruppamento
                    ROW_NUMBER() OVER (
                        PARTITION BY r.egi_id
                        ORDER BY
                            CASE WHEN r.type = 'strong' THEN 1 ELSE 2 END ASC,
                            r.offer_amount_fiat DESC,
                            r.id DESC
                    ) as rn
                FROM reservations r
                INNER JOIN egis e ON e.id = r.egi_id
                INNER JOIN collections c ON c.id = e.collection_id
                WHERE c.creator_id = ?
                  AND r.status = 'active'
                  AND r.is_current = 1";

        $bindings = [$this->user->id];

        if (!empty($this->userCollectionIds)) {
            $placeholders = implode(',', array_fill(0, count($this->userCollectionIds), '?'));
            $sql .= " AND c.id IN ({$placeholders})";
            $bindings = array_merge($bindings, $this->userCollectionIds);
        }

        $sql .= "
            )
            SELECT *
            FROM RankedReservations
            WHERE rn = 1
        ";

        $validReservationsData = DB::select($sql, $bindings);
        $validReservations = collect($validReservationsData)->map(function ($data) {
            // Semplice mappatura per ora, può essere arricchita se StatisticsService restituisce oggetti modello
            $obj = (object)$data;
            $obj->egi = (object)['collection_id' => $data->egi_collection_id]; // Mantieni egi_collection_id per EPP
            $obj->collection = (object)['id' => $data->actual_collection_id, 'collection_name' => $data->collection_name];
            unset($obj->egi_collection_id, $obj->actual_collection_id, $obj->rn); // Pulisci colonne ausiliarie
            return $obj;
        });


        $totalReservations = $validReservations->count();
        $strongReservations = $validReservations->where('type', 'strong')->count();
        $weakReservations = $validReservations->where('type', 'weak')->count();

        $byCollection = $validReservations->groupBy('collection.id')->map(function (SupportCollection $reservationsInCollection, $collectionId) {
            return [
                'collection_id' => $collectionId,
                'collection_name' => $reservationsInCollection->first()->collection->collection_name,
                'total_reservations' => $reservationsInCollection->count(),
                'strong_reservations' => $reservationsInCollection->where('type', 'strong')->count(),
                'weak_reservations' => $reservationsInCollection->where('type', 'weak')->count(),
            ];
        })->values()->all();

        // by_egi non era nella tua documentazione JSON ma potrebbe essere utile
        // Per ora lo ometto per allinearmi alla struttura JSON precedente.

        return [
            'total' => $totalReservations,
            'strong' => $strongReservations,
            'weak' => $weakReservations,
            'by_collection' => $byCollection,
            // 'by_egi' => $byEgi, // Da implementare se necessario
            'valid_reservations_for_amount' => $validReservations // Passa per altri calcoli
        ];
    }


    /**
     * 🎯 Calculates financial statistics based on valid reservations.
     * @param SupportCollection $validReservations Collection of valid reservation objects.
     * @return array Amount statistics.
     *
     * @signature: getAmountStatistics(SupportCollection $validReservations): array
     * @context: Part of the comprehensive statistics calculation.
     * @privacy-safe: Aggregates financial data.
     * @data-input: Collection of pre-filtered valid reservation objects.
     * @data-output: Array with total_eur, by_collection, by_type.
     */
    private function getAmountStatistics(SupportCollection $validReservations): array {
        $totalEur = $validReservations->sum('offer_amount_fiat');
        $byType = [
            'strong' => $validReservations->where('type', 'strong')->sum('offer_amount_fiat'),
            'weak' => $validReservations->where('type', 'weak')->sum('offer_amount_fiat'),
        ];

        $byCollection = $validReservations->groupBy('collection.id')->map(function (SupportCollection $reservationsInCollection, $collectionId) {
            return [
                'collection_id' => $collectionId,
                'collection_name' => $reservationsInCollection->first()->collection->collection_name,
                'total_amount_eur' => $reservationsInCollection->sum('offer_amount_fiat'),
            ];
        })->values()->all();


        return [
            'total_eur' => (float) $totalEur,
            'by_collection' => $byCollection,
            'by_type' => [
                'strong' => (float) $byType['strong'],
                'weak' => (float) $byType['weak'],
            ],
        ];
    }

    /**
     * 🎯 Calculates EPP (Environmental Project Partner) potential quota.
     * @param SupportCollection $validReservations Collection of valid reservation objects.
     * @return array EPP potential statistics.
     *
     * @signature: getEppPotentialStatistics(SupportCollection $validReservations): array
     * @context: Part of the comprehensive statistics calculation.
     * @log: STATS_EPP_CALC - Details on EPP percentages used.
     * @privacy-safe: Aggregates financial data for EPP calculation.
     * @data-input: Collection of pre-filtered valid reservation objects.
     * @data-output: Array with total_quota_eur, by_collection.
     */
    private function getEppPotentialStatistics(SupportCollection $validReservations): array {
        if ($validReservations->isEmpty()) {
            return ['total_quota_eur' => 0.0, 'by_collection' => []];
        }

        // Ottieni le percentuali EPP per le collection dell'utente che hanno wallet EPP
        $eppWallets = Wallet::whereIn('collection_id', $this->userCollectionIds)
            ->where('platform_role', 'EPP') // Assumendo questo sia il ruolo
            ->pluck('royalty_mint', 'collection_id'); // royalty_mint è la percentuale EPP

        $this->logger->debug('EPP Wallet Percentages Fetched', [
            'user_id' => $this->user->id,
            'epp_wallets_data' => $eppWallets->toArray(),
            'log_category' => 'STATS_EPP_CALC_DETAIL'
        ]);

        $totalEppQuotaEur = 0;
        $eppByCollection = [];

        // Raggruppa le prenotazioni valide per collection_id per efficienza
        $reservationsByCollectionId = $validReservations->groupBy('collection.id');

        foreach ($reservationsByCollectionId as $collectionId => $reservationsInCollection) {
            $collectionName = $reservationsInCollection->first()->collection->collection_name;
            $eppPercentage = $eppWallets->get($collectionId, self::DEFAULT_EPP_PERCENTAGE);
            $collectionTotalAmount = $reservationsInCollection->sum('offer_amount_fiat');
            $collectionEppQuota = ($collectionTotalAmount * $eppPercentage) / 100.0;

            $totalEppQuotaEur += $collectionEppQuota;
            $eppByCollection[] = [
                'collection_id' => $collectionId,
                'collection_name' => $collectionName,
                'epp_percentage' => (float) $eppPercentage,
                'total_amount' => (float) $collectionTotalAmount,
                'epp_quota' => (float) $collectionEppQuota,
            ];
        }

        return [
            'total_quota_eur' => (float) $totalEppQuotaEur,
            'by_collection' => $eppByCollection,
        ];
    }

    /**
     * 🎯 Builds the summary KPIs section from individual statistics components.
     * @param array $likesStats
     * @param array $reservationsStats
     * @param array $amountStats
     * @param array $eppPotentialStats
     * @return array Summary KPIs.
     *
     * @signature: buildSummaryKPIs(array $likesStats, array $reservationsStats, array $amountStats, array $eppPotentialStats): array
     * @context: Helper method called internally.
     * @privacy-safe: Uses aggregated data.
     */
    private function buildSummaryKPIs(
        array $likesStats,
        array $reservationsStats,
        array $amountStats,
        array $eppPotentialStats
    ): array {
        return [
            'total_likes' => $likesStats['total'] ?? 0,
            'total_reservations' => $reservationsStats['total'] ?? 0,
            'total_amount' => $amountStats['total_eur'] ?? 0.0,
            'epp_quota' => $eppPotentialStats['total_quota_eur'] ?? 0.0,
            'strong_reservations' => $reservationsStats['strong'] ?? 0,
            'collections_count' => count($this->userCollectionIds),
        ];
    }

    /**
     * 🎯 Provides a default empty structure for statistics when user has no data.
     * @return array Empty statistics structure.
     *
     * @signature: getEmptyStatsStructure(): array
     * @context: Helper method for initialization or when no user collections exist.
     * @privacy-safe: Returns a generic empty structure.
     */
    private function getEmptyStatsStructure(): array {
        $emptyNumericArray = ['total' => 0, 'collections_total' => 0, 'egis_total' => 0, 'by_collection' => [], 'top_egis' => []];
        $emptyReservations = ['total' => 0, 'strong' => 0, 'weak' => 0, 'by_collection' => [], 'valid_reservations_for_amount' => collect([])];
        $emptyAmounts = ['total_eur' => 0.0, 'by_collection' => [], 'by_type' => ['strong' => 0.0, 'weak' => 0.0]];
        $emptyEpp = ['total_quota_eur' => 0.0, 'by_collection' => []];

        return [
            'likes' => $emptyNumericArray,
            'reservations' => $emptyReservations,
            'amounts' => $emptyAmounts,
            'epp_potential' => $emptyEpp,
            'portfolio' => [
                'total_egis' => 0,
                'total_collections' => 0,
                'reserved_egis' => 0,
                'available_egis' => 0,
                'highest_offer' => 0,
                'total_value_eur' => 0,
            ],
            'summary' => [
                'total_likes' => 0,
                'total_reservations' => 0,
                'total_amount' => 0.0,
                'epp_quota' => 0.0,
                'strong_reservations' => 0,
                'collections_count' => 0,
            ],
            // 'generated_at' and 'cache_expires_at' verranno aggiunti da getComprehensiveStats
        ];
    }


    /**
     * 🎯 Clears the statistics cache for the current user.
     * @return bool True if cache was cleared or did not exist, false on error (though Cache::forget usually returns bool).
     *
     * @signature: clearUserStatisticsCache(): bool
     * @context: Called by StatisticsController or console command to invalidate cache.
     * @log: STATS_CACHE_CLEARED_EXPLICIT - User ID for whom cache is cleared.
     * @privacy-safe: Operates on user-specific cache key.
     */
    public function clearUserStatisticsCache(): bool {
        $cacheKey = 'user_stats_' . $this->user->id;
        $this->logger->info('User statistics cache explicitly cleared.', [
            'user_id' => $this->user->id,
            'cache_key' => $cacheKey,
            'log_category' => 'STATS_CACHE_CLEARED_EXPLICIT'
        ]);
        return Cache::forget($cacheKey);
    }

    /**
     * 🎯 Calculates portfolio statistics (moved from public portfolio views)
     * @return array Portfolio statistics including EGI counts, values, and offers
     *
     * @signature: getPortfolioStatistics(): array
     * @context: Called internally by calculateAllStatistics to get portfolio data
     * @privacy-safe: Operates on user's own collections and EGIs
     */
    private function getPortfolioStatistics(): array {
        if (empty($this->userCollectionIds)) {
            return [
                'total_egis' => 0,
                'total_collections' => 0,
                'reserved_egis' => 0,
                'available_egis' => 0,
                'highest_offer' => 0,
                'total_value_eur' => 0,
            ];
        }

        // Get all EGIs from user's collections with their reservations
        $egis = Egi::whereIn('collection_id', $this->userCollectionIds)
            ->with(['reservations' => function ($query) {
                $query->where('is_current', true)->where('status', 'active');
            }])
            ->get();

        $totalEgis = $egis->count();
        $totalCollections = count($this->userCollectionIds);

        // Calculate reserved and available EGIs
        $reservedEgis = $egis->filter(function ($egi) {
            return $egi->reservations->isNotEmpty();
        })->count();

        $availableEgis = $totalEgis - $reservedEgis;

        // Get highest offer and total value
        $allActiveReservations = $egis->flatMap->reservations;
        $highestOffer = $allActiveReservations->max('offer_amount_fiat') ?? 0;
        $totalValueEur = $allActiveReservations->sum('amount_eur') ?? 0;

        return [
            'total_egis' => $totalEgis,
            'total_collections' => $totalCollections,
            'reserved_egis' => $reservedEgis,
            'available_egis' => $availableEgis,
            'highest_offer' => $highestOffer,
            'total_value_eur' => $totalValueEur,
        ];
    }

    /**
     * 🎯 Get likes received statistics for a user's EGIs
     * @param int $userId The user ID to get like statistics for
     * @return array Statistics about likes received on user's EGIs
     */
    public static function getLikesReceivedStats($userId) {
        try {
            $cacheKey = "likes_received_stats_{$userId}";

            return Cache::remember($cacheKey, self::CACHE_TTL_MINUTES * 60, function () use ($userId) {
                // Get user's EGIs with their likes and collections
                $userEgis = Egi::whereHas('collection', function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                })
                    ->whereHas('likes') // Solo EGI che hanno almeno un like
                    ->with(['likes.user', 'collection'])
                    ->get();

                // Calculate total likes received
                $totalReceived = $userEgis->sum(function ($egi) {
                    return $egi->likes->count();
                });

                // Get top EGIs by likes (solo quelli con like > 0)
                $topEgis = $userEgis->map(function ($egi) {
                    $likesCount = $egi->likes->count();

                    // Ottieni la lista degli utenti che hanno messo like
                    $likedByUsers = $egi->likes->map(function ($like) {
                        return [
                            'user_id' => $like->user->id,
                            'nickname' => $like->user->nickname ?? $like->user->name ?? 'Unknown User',
                            'avatar' => $like->user->avatar ?? null,
                        ];
                    })->toArray();

                    return [
                        'id' => $egi->id,
                        'title' => $egi->title,
                        'main_image_url' => $egi->getMainImageUrlAttribute(), // Chiama esplicitamente l'accessor
                        'thumbnail_image_url' => $egi->getThumbnailImageUrlAttribute(),
                        'avatar_image_url' => $egi->getAvatarImageUrlAttribute(),
                        'collection_name' => $egi->collection->name ?? 'Unknown Collection',
                        'likes_count' => $likesCount,
                        'liked_by_users' => $likedByUsers, // Lista utenti che hanno messo like
                    ];
                })
                    ->filter(function ($egi) {
                        return $egi['likes_count'] > 0; // Filtra EGI con 0 like
                    })
                    ->sortByDesc('likes_count')
                    ->take(10)
                    ->values()
                    ->toArray();

                return [
                    'total_received' => $totalReceived,
                    'top_egis' => $topEgis,
                ];
            });
        } catch (Throwable $e) {
            app(UltraLogManager::class)->log('error', 'Failed to get likes received stats', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'total_received' => 0,
                'top_egis' => [],
            ];
        }
    }

    /**
     * 🎯 Get likes given statistics for a user
     * @param int $userId The user ID to get like statistics for
     * @return array Statistics about likes given by the user
     */
    public static function getLikesGivenStats($userId) {
        try {
            $cacheKey = "likes_given_stats_{$userId}";

            return Cache::remember($cacheKey, self::CACHE_TTL_MINUTES * 60, function () use ($userId) {
                // Get all likes given TO this user's EGIs (chi ha dato like agli EGI dell'utente)
                $likesToUserEgis = Like::whereHas('likeable', function ($query) use ($userId) {
                    $query->where('likeable_type', 'App\Models\Egi')
                        ->whereHas('collection', function ($subQuery) use ($userId) {
                            $subQuery->where('creator_id', $userId);
                        });
                })
                    ->with(['user']) // Carica l'utente che ha dato il like
                    ->get();

                // Calculate total likes given TO this user
                $totalGiven = $likesToUserEgis->count();

                // Group by user who gave the like and count likes given by each
                $likesGroupedByGiver = $likesToUserEgis->groupBy('user_id')
                    ->filter(function ($likes, $giverId) use ($userId) {
                        return $giverId !== null && $giverId != $userId; // Escludi like a se stesso
                    });

                // Get top users that gave likes to this user's EGIs
                $topUsers = $likesGroupedByGiver->map(function ($likes, $giverId) {
                    $giverUser = $likes->first()->user; // Prendi l'utente dal primo like

                    if (!$giverUser) {
                        return null;
                    }

                    return [
                        'user_id' => $giverId,
                        'nickname' => $giverUser->nickname ?? $giverUser->name ?? 'Unknown User',
                        'avatar' => $giverUser->avatar ?? null,
                        'likes_given' => $likes->count(),
                    ];
                })
                    ->filter() // Rimuovi valori null
                    ->sortByDesc('likes_given')
                    ->take(20)
                    ->values()
                    ->toArray();

                return [
                    'total_given' => $totalGiven,
                    'top_users' => $topUsers,
                ];
            });
        } catch (Throwable $e) {
            app(UltraLogManager::class)->log('error', 'Failed to get likes given stats', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'total_given' => 0,
                'top_users' => [],
            ];
        }
    }

    /**
     * 🎯 Get likes given by user statistics (WHAT USER LIKED)
     * @param int $userId The user ID to get statistics for
     * @return array Statistics about what this user liked
     */
    public static function getLikesGivenByUserStats($userId) {
        try {
            $cacheKey = "likes_given_by_user_stats_{$userId}";

            return Cache::remember($cacheKey, self::CACHE_TTL_MINUTES * 60, function () use ($userId) {
                // Get all likes given BY this user to EGIs
                $userLikes = Like::where('user_id', $userId)
                    ->where('likeable_type', 'App\Models\Egi')
                    ->with(['likeable.collection.creator'])
                    ->get();

                // Calculate total likes given
                $totalGiven = $userLikes->count();

                // Get EGIs liked by this user
                $likedEgis = $userLikes->map(function ($like) {
                    $egi = $like->likeable;
                    if (!$egi) return null;

                    return [
                        'id' => $egi->id,
                        'title' => $egi->title,
                        'main_image_url' => $egi->getMainImageUrlAttribute(),
                        'thumbnail_image_url' => $egi->getThumbnailImageUrlAttribute(),
                        'avatar_image_url' => $egi->getAvatarImageUrlAttribute(),
                        'collection_name' => $egi->collection->collection_name ?? 'Unknown Collection',
                        'owner_id' => $egi->collection->creator_id ?? null,
                        'owner_name' => $egi->collection->creator->name ?? $egi->collection->creator->nickname ?? 'Unknown User',
                        'owner_nick_name' => $egi->collection->creator->nick_name ?? null,
                    ];
                })
                    ->filter() // Remove nulls
                    ->values()
                    ->toArray();

                // Group by owner and count likes given to each
                $likesGroupedByOwner = $userLikes->groupBy(function ($like) {
                    return $like->likeable->collection->creator_id ?? null;
                })->filter(function ($likes, $ownerId) use ($userId) {
                    return $ownerId !== null && $ownerId != $userId; // Exclude self-likes
                });

                // Get owners ranked by likes received from this user
                $owners = $likesGroupedByOwner->map(function ($likes, $ownerId) {
                    $owner = User::find($ownerId);

                    if (!$owner) {
                        return null;
                    }

                    return [
                        'user_id' => $ownerId,
                        'nickname' => $owner->nickname ?? $owner->name ?? 'Unknown User',
                        'nick_name' => $owner->nick_name, // Add nick_name for route
                        'user' => $owner, // Pass the full user object so accessor works
                        'likes_count' => $likes->count(),
                    ];
                })
                    ->filter() // Remove nulls
                    ->sortByDesc('likes_count')
                    ->take(20)
                    ->values()
                    ->toArray();

                return [
                    'total_given' => $totalGiven,
                    'liked_egis' => $likedEgis,
                    'owners' => $owners,
                ];
            });
        } catch (Throwable $e) {
            app(UltraLogManager::class)->log('error', 'Failed to get likes given by user stats', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'total_given' => 0,
                'liked_egis' => [],
                'owners' => [],
            ];
        }
    }

    /**
     * 🎯 Get statistics about who liked user's EGIs
     * @param int $userId The user ID to get statistics for
     * @return array Statistics about users who liked this user's EGIs
     */
    public static function getWhoLikedUserEgisStats($userId) {
        try {
            $cacheKey = "who_liked_user_egis_stats_{$userId}";

            return Cache::remember($cacheKey, self::CACHE_TTL_MINUTES * 60, function () use ($userId) {
                // Get all likes given TO this user's EGIs
                $likesToUserEgis = Like::whereHas('likeable', function ($query) use ($userId) {
                    $query->where('likeable_type', 'App\Models\Egi')
                        ->whereHas('collection', function ($subQuery) use ($userId) {
                            $subQuery->where('creator_id', $userId);
                        });
                })
                    ->with(['user']) // Load the user who gave the like
                    ->get();

                // Group by user who gave the like and count
                $usersWhoLiked = $likesToUserEgis->groupBy('user_id')
                    ->map(function ($likes) {
                        $user = $likes->first()->user;
                        if (!$user) return null;

                        return [
                            'user_id' => $user->id,
                            'nickname' => $user->nickname ?? $user->name ?? 'Unknown User',
                            'nick_name' => $user->nick_name,
                            'safe_nick_name' => $user->nick_name && \preg_match('/^[a-zA-Z0-9_-]+$/', $user->nick_name) ? $user->nick_name : null,
                            'user' => $user, // Pass the full user object so accessor works
                            'likes_given' => $likes->count(),
                        ];
                    })
                    ->filter() // Remove nulls
                    ->sortByDesc('likes_given')
                    ->take(20)
                    ->values()
                    ->toArray();

                return [
                    'total_given' => $likesToUserEgis->count(),
                    'top_users' => $usersWhoLiked,
                ];
            });
        } catch (Throwable $e) {
            app(UltraLogManager::class)->log('error', 'Failed to get who liked user egis stats', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'total_given' => 0,
                'top_users' => [],
            ];
        }
    }
}
