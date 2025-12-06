<?php

namespace App\Factories;

use App\Models\User;
use App\Models\Collection;
use App\Models\Egi;
use App\Models\Reservation;
use App\Models\Like;
use App\Models\PaymentDistribution;
use App\Models\Wallet;
use App\Enums\PaymentDistribution\UserTypeEnum;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * @package App\Factories
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Statistics Testing)
 * @date 2025-10-01
 * @purpose Factory deterministico per testare statistiche con dati noti e risultati pre-calcolati
 */
class StatisticsTestFactory {
    /**
     * Target user per i test - User ID 3 (Natan)
     */
    private const TARGET_USER_ID = 3;

    /**
     * 🔧 Configurazione Factory - Deterministico per risultati ripetibili
     */
    private const CONFIG = [
        'likes_per_collection' => 3,
        'likes_per_egi' => 2,
        'reservations_percentage' => 0.8, // 80% degli EGI avrà reservations
        'strong_reservation_percentage' => 0.8, // 80% delle reservations sarà strong
        'months_back' => 24, // 24 mesi indietro da oggi (per presentazioni realistiche)
        'base_amounts' => [66.5, 133, 199.5, 266, 332.5, 399, 465.5, 532, 598.5, 665], // Importi base deterministici
        'epp_percentage' => 0.25, // 25% chance per EPP payments
    ];

    /**
     * Dati base User 3 (esistenti) - Include TUTTE le collaborazioni
     */
    private $user3Data = [
        'collections' => [], // Sarà popolato dinamicamente da tutte le collections con wallets
        'egis' => [], // Sarà popolato dinamicamente
    ];

    /**
     * Expected results pre-calcolati per validazione
     */
    private $expectedResults = [];

    /**
     * 🎯 Metodo principale: cancella e ricrea dati deterministici
     *
     * @return array Expected results per validazione
     */
    public function seedDeterministicData(): array {
        DB::transaction(function () {
            $this->loadUser3Data(); // Prima carica i dati
            $this->clearExistingData(); // Poi cancella usando i dati caricati
            $this->createAdditionalUsers();
            $this->createDeterministicData();
            $this->calculateExpectedResults();
        });

        return $this->expectedResults;
    }

    /**
     * 🧹 Cancella dati esistenti per User 3 mantenendo Collections/EGIs
     */
    private function clearExistingData(): void {
        echo "🧹 Clearing existing data for User ID " . self::TARGET_USER_ID . "...\n";

        // Get EGI IDs dalle collections di User 3
        $egiIds = Egi::whereIn('collection_id', $this->user3Data['collections'])->pluck('id');

        // Cancella likes su EGIs e Collections di User 3
        Like::where('likeable_type', 'App\Models\Collection')
            ->whereIn('likeable_id', $this->user3Data['collections'])
            ->delete();

        Like::where('likeable_type', 'App\Models\Egi')
            ->whereIn('likeable_id', $egiIds)
            ->delete();

        // Cancella payment distributions collegate alle reservations degli EGI di User 3
        $reservationIds = Reservation::whereIn('egi_id', $egiIds)->pluck('id');
        PaymentDistribution::whereIn('reservation_id', $reservationIds)->delete();

        // Cancella reservations degli EGI di User 3
        Reservation::whereIn('egi_id', $egiIds)->delete();

        echo "✅ Cleared likes, reservations, and payment distributions for User 3\n";
    }

    /**
     * 📊 Carica dati esistenti User 3 - Include TUTTE le collaborazioni (scenario reale)
     */
    private function loadUser3Data(): void {
        // Collections di cui User 3 è creator
        $creatorCollectionIds = Collection::where('creator_id', self::TARGET_USER_ID)->pluck('id');

        // Collections dove User 3 ha wallets (collaborazioni) - SCENARIO REALE
        $walletCollectionIds = DB::table('wallets')
            ->where('user_id', self::TARGET_USER_ID)
            ->pluck('collection_id');

        // Merge e deduplica tutte le collections (CORRETTO per scenario reale)
        $allCollectionIds = $creatorCollectionIds->merge($walletCollectionIds)->unique()->sort()->values()->toArray();

        $this->user3Data['collections'] = $allCollectionIds;
        $this->user3Data['creatorCollections'] = $creatorCollectionIds->toArray();
        $this->user3Data['walletCollections'] = $walletCollectionIds->toArray();

        // Carica EGI IDs di TUTTE le collections dove può ricevere payments
        $egiIds = Egi::whereIn('collection_id', $allCollectionIds)->pluck('id')->toArray();
        $this->user3Data['egis'] = $egiIds;

        $creatorCount = count($this->user3Data['creatorCollections']);
        $walletCount = count($this->user3Data['walletCollections']);
        $totalCollections = count($allCollectionIds);
        $totalEgis = count($egiIds);

        echo "📊 Loaded User 3 data: {$totalEgis} EGIs in {$totalCollections} collections (creator: {$creatorCount}, wallets: {$walletCount}) - REAL SCENARIO\n";
    }

    /**
     * 👥 Crea utenti aggiuntivi per likes e reservations
     */
    private function createAdditionalUsers(): void {
        echo "👥 Creating additional users for testing...\n";

        // NON CREO NESSUN UTENTE - uso User ID 8 e 9 esistenti

        echo "✅ Additional users ready\n";
    }

    /**
     * 🏗️ Crea tutti i dati deterministici
     */
    private function createDeterministicData(): void {
        echo "🏗️ Creating deterministic data...\n";

        $this->createDeterministicLikes();
        $this->createDeterministicReservations();
        $this->createDeterministicPaymentDistributions();

        echo "✅ Deterministic data creation completed\n";
    }

    /**
     * ❤️ Crea likes deterministici
     */
    private function createDeterministicLikes(): void {
        $likesCreated = 0;

        // 6 likes su collections (30% di 20)
        $collectionLikesCount = 6;
        for ($i = 0; $i < $collectionLikesCount; $i++) {
            $userId = ($i % 2 === 0) ? 8 : 9; // Alterna tra User ID 8 e 9 (esistenti)
            $collectionId = $this->user3Data['collections'][$i % count($this->user3Data['collections'])];
            $createdAt = $this->getRandomDateInYears();

            Like::create([
                'user_id' => $userId,
                'likeable_type' => 'App\Models\Collection',
                'likeable_id' => $collectionId,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
            $likesCreated++;
        }

        // 14 likes su EGI (70% di 20)
        $egiLikesCount = 14;
        for ($i = 0; $i < $egiLikesCount; $i++) {
            $userId = ($i % 2 === 0) ? 8 : 9; // Alterna tra User ID 8 e 9 (esistenti)
            $egiId = $this->user3Data['egis'][$i % count($this->user3Data['egis'])];
            $createdAt = $this->getRandomDateInYears();

            Like::create([
                'user_id' => $userId,
                'likeable_type' => 'App\Models\Egi',
                'likeable_id' => $egiId,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
            $likesCreated++;
        }

        echo "❤️ Created {$likesCreated} likes (6 collection, 14 EGI)\n";
    }

    /**
     * 📋 Crea reservations deterministiche per TUTTI gli EGI
     */
    private function createDeterministicReservations(): void {
        $reservationsCreated = 0;
        $strongCount = 0;
        $weakCount = 0;

        $targetReservations = 500; // Target minimo
        $egiCount = count($this->user3Data['egis']);
        $reservationsPerEgi = ceil($targetReservations / $egiCount); // ~15 reservations per EGI

        // Crea multiple reservations per ogni EGI SIMULANDO LOGICA REALE DI SUPERSEDING
        foreach ($this->user3Data['egis'] as $egiIndex => $egiId) {

            // STEP 1: Genera TUTTE le date per questo EGI e le ordina cronologicamente
            $reservationDates = [];
            for ($resIndex = 0; $resIndex < $reservationsPerEgi; $resIndex++) {
                $reservationDates[] = $this->getRandomDateInYears();
            }
            // Ordina le date: la più vecchia PRIMA, la più recente ULTIMA
            sort($reservationDates);

            $createdReservations = [];

            // STEP 2: Crea reservations in ordine cronologico (dalla più vecchia alla più nuova)
            for ($resIndex = 0; $resIndex < $reservationsPerEgi; $resIndex++) {
                $globalIndex = ($egiIndex * $reservationsPerEgi) + $resIndex;

                $userId = ($globalIndex % 2 === 0) ? 8 : 9; // Alterna tra User ID 8 e 9

                // 80% strong, 20% weak (deterministico)
                $isStrong = ($globalIndex % 5) !== 4; // 4 su 5 = 80% strong
                $type = $isStrong ? 'strong' : 'weak';

                // Importi deterministici con varietà
                $amountIndex = $globalIndex % count(self::CONFIG['base_amounts']);
                $baseAmount = self::CONFIG['base_amounts'][$amountIndex];

                $createdAt = $reservationDates[$resIndex]; // Usa data ordinata cronologicamente

                // LOGICA REALE: Solo l'ULTIMA reservation (più recente cronologicamente) è current/highest
                $isLastReservation = ($resIndex === ($reservationsPerEgi - 1));

                // DISABILITA TEMPORANEAMENTE TIMESTAMPS per impostare created_at personalizzato
                Reservation::unguard();
                $reservationId = DB::table('reservations')->insertGetId([
                    'user_id' => $userId,
                    'egi_id' => $egiId,
                    'type' => $type,
                    'offer_amount_fiat' => $baseAmount,
                    'amount_eur' => $baseAmount, // Per semplicità
                    'input_amount' => $baseAmount, // Campo required!
                    'status' => 'active',
                    'is_current' => $isLastReservation, // Solo l'ultima cronologicamente è current
                    'is_highest' => $isLastReservation, // Solo l'ultima cronologicamente è highest
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);

                // Recupera il modello creato per l'array
                $newReservation = Reservation::find($reservationId);
                Reservation::reguard();

                $createdReservations[] = $newReservation;
                $reservationsCreated++;
                if ($isStrong) $strongCount++;
                else $weakCount++;
            }

            // STEP 3: SUPERSEDING LOGIC - DISTRIBUZIONE INTELLIGENTE PER PERIODI CORRENTI
            if (count($createdReservations) > 1) {
                // STRATEGIA: Privilegia periodi correnti (oggi > settimana > mese > passato)
                $today = Carbon::now();
                $currentReservation = null;

                // 1. PRIORITY: EGI con indici bassi -> reservations di oggi (primi 8 EGI)
                if ($egiIndex < 8) {
                    // Cerca reservation di oggi o più recente possibile
                    $todayReservations = array_filter($createdReservations, function ($res) use ($today) {
                        return $res->created_at->isSameDay($today);
                    });
                    $currentReservation = !empty($todayReservations) ? array_values($todayReservations)[0] : end($createdReservations);
                }
                // 2. PRIORITY: EGI 8-15 -> reservations di questa settimana
                elseif ($egiIndex < 16) {
                    $weekReservations = array_filter($createdReservations, function ($res) use ($today) {
                        return $res->created_at->isCurrentWeek();
                    });
                    $currentReservation = !empty($weekReservations) ? array_values($weekReservations)[0] : end($createdReservations);
                }
                // 3. PRIORITY: EGI 16-25 -> reservations di questo mese
                elseif ($egiIndex < 26) {
                    $monthReservations = array_filter($createdReservations, function ($res) use ($today) {
                        return $res->created_at->isCurrentMonth();
                    });
                    $currentReservation = !empty($monthReservations) ? array_values($monthReservations)[0] : end($createdReservations);
                }
                // 4. RESTO: Distribuzione storica (come prima)
                else {
                    $currentIndex = $egiIndex % count($createdReservations);
                    $currentReservation = $createdReservations[$currentIndex];
                }

                $currentCreatedAt = $currentReservation->created_at;

                // Marca TUTTE le altre come superseded
                foreach ($createdReservations as $reservation) {
                    if ($reservation->id !== $currentReservation->id) {
                        // Usa DB::table per mantenere timestamps personalizzati
                        DB::table('reservations')
                            ->where('id', $reservation->id)
                            ->update([
                                'is_current' => false,
                                'is_highest' => false,
                                'superseded_by_id' => $currentReservation->id,
                                'superseded_at' => $currentCreatedAt,
                                'updated_at' => $currentCreatedAt, // Mantieni timestamp personalizzato
                            ]);
                    }
                }

                // ASSICURATI che la reservation selezionata sia is_current = true
                DB::table('reservations')
                    ->where('id', $currentReservation->id)
                    ->update([
                        'is_current' => true,
                        'is_highest' => true,
                    ]);
            }
        }

        echo "📋 Created {$reservationsCreated} reservations for " . count($this->user3Data['egis']) . " EGIs ({$strongCount} strong, {$weakCount} weak)\n";
    }

    /**
     * 💰 Crea payment distributions per le reservations
     */
    private function createDeterministicPaymentDistributions(): void {
        // SEGUE ESATTAMENTE ARCHITETTURA DB PROGETTATA: ogni reservation -> tutti wallets collegati

        // Get the same reservations that StatisticsService would use (ROW_NUMBER = 1)
        $userCollectionIds = $this->user3Data['collections']; // Already an array of IDs
        $collectionIdsStr = implode(',', $userCollectionIds);

        $trueValue = \App\Helpers\DatabaseHelper::booleanValue(true);
        $sql = "
            WITH RankedReservations AS (
                SELECT
                    r.id,
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
                WHERE c.id IN ({$collectionIdsStr})
                  AND r.status = 'active'
                  AND r.is_current = {$trueValue}
            )
            SELECT id
            FROM RankedReservations
            WHERE rn = 1
        ";

        $validReservationIds = collect(DB::select($sql))->pluck('id');
        $reservations = Reservation::whereIn('id', $validReservationIds)->get();
        $paymentsCreated = 0;

        // ARCHITETTURA DB: Per OGNI reservation -> TUTTI i wallets della collection ricevono distribuzione
        foreach ($reservations as $reservation) {
            $collectionId = Egi::find($reservation->egi_id)->collection_id;

            // Prendi TUTTI i wallets collegati alla collection (FONTE DI VERITÀ)
            $wallets = DB::table('wallets')
                ->where('collection_id', $collectionId)
                ->get();

            // Per OGNI wallet -> crea PaymentDistribution usando FONTE DI VERITÀ
            foreach ($wallets as $wallet) {
                // FONTE DI VERITÀ: wallets.platform_role (sempre presente e corretto)
                $userType = match ($wallet->platform_role) {
                    'Creator' => UserTypeEnum::CREATOR->value,
                    'EPP' => UserTypeEnum::EPP->value,
                    'Natan' => UserTypeEnum::NATAN->value,
                    default => UserTypeEnum::COLLECTOR->value, // Default sicuro
                };

                // USA DB::table per impostare timestamps personalizzati (stesso fix delle reservations)
                DB::table('payment_distributions')->insert([
                    'user_id' => $wallet->user_id,
                    'reservation_id' => $reservation->id,
                    'collection_id' => $collectionId,
                    'user_type' => $userType,
                    'percentage' => $wallet->royalty_mint,
                    'amount_eur' => $reservation->amount_eur * ($wallet->royalty_mint / 100),
                    'exchange_rate' => 1.0000000000, // EUR/EUR
                    'is_epp' => $wallet->platform_role === 'EPP',
                    'distribution_status' => 'confirmed',
                    'created_at' => $reservation->created_at,
                    'updated_at' => $reservation->created_at,
                ]);
                $paymentsCreated++;
            }
        }

        echo "💰 Created {$paymentsCreated} payment distributions (seguendo architettura DB wallets)\n";
    }

    /**
     * 🧮 Calcola expected results per tutte le metriche
     */
    private function calculateExpectedResults(): void {
        echo "🧮 Calculating expected results...\n";

        $this->expectedResults = [
            'likes_statistics' => $this->calculateExpectedLikesStats(),
            'reservations_statistics' => $this->calculateExpectedReservationsStats(),
            'amounts_statistics' => $this->calculateExpectedAmountsStats(),
            'epp_potential_statistics' => $this->calculateExpectedEppStats(),
            'portfolio_statistics' => $this->calculateExpectedPortfolioStats(),
            'creator_earnings' => $this->calculateExpectedCreatorEarnings(),
            'user_total_earnings' => $this->calculateExpectedUserTotalEarnings(),
        ];

        echo "✅ Expected results calculated for " . count($this->expectedResults) . " metric categories\n";
    }

    /**
     * Calcola expected results per likes statistics - SAME STRUCTURE AS StatisticsService
     */
    private function calculateExpectedLikesStats(): array {
        // Collection likes
        $collectionLikes = Like::where('likeable_type', 'App\Models\Collection')
            ->whereIn('likeable_id', $this->user3Data['collections'])
            ->count();

        // EGI likes
        $egiLikes = Like::where('likeable_type', 'App\Models\Egi')
            ->whereIn('likeable_id', $this->user3Data['egis'])
            ->count();

        return [
            'total' => $collectionLikes + $egiLikes,
            'collections_total' => $collectionLikes,
            'egis_total' => $egiLikes,
            'by_collection' => [], // Non serve per validation
            'top_egis' => [], // Non serve per validation
        ];
    }

    /**
     * Calcola expected results per reservations statistics - SAME STRUCTURE AS StatisticsService
     */
    private function calculateExpectedReservationsStats(): array {
        // Use same ROW_NUMBER logic as StatisticsService
        $userCollectionIds = $this->user3Data['collections'];
        $collectionIdsStr = implode(',', $userCollectionIds);

        $trueValue = \App\Helpers\DatabaseHelper::booleanValue(true);
        $sql = "
            WITH RankedReservations AS (
                SELECT
                    r.*,
                    e.collection_id AS egi_collection_id,
                    c.id AS actual_collection_id, c.collection_name,
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
                WHERE r.status = 'active'
                  AND r.is_current = {$trueValue}
                  AND c.id IN ({$collectionIdsStr})
            )
            SELECT *
            FROM RankedReservations
            WHERE rn = 1
        ";

        $validReservationsData = collect(DB::select($sql));

        $totalReservations = $validReservationsData->count();
        $strongReservations = $validReservationsData->where('type', 'strong')->count();
        $weakReservations = $validReservationsData->where('type', 'weak')->count();

        return [
            'total' => $totalReservations,
            'strong' => $strongReservations,
            'weak' => $weakReservations,
            'by_collection' => [], // Non serve per validation
        ];
    }

    /**
     * Calcola expected results per creator earnings - USA PAYMENT_DISTRIBUTIONS REALI!
     */
    private function calculateExpectedCreatorEarnings(): array {
        // I PaymentDistribution sono già stati creati con i filtri giusti!
        // Uso ESATTAMENTE gli stessi dati che StatisticsService troverà
        $payments = PaymentDistribution::join('reservations', 'payment_distributions.reservation_id', '=', 'reservations.id')
            ->where('payment_distributions.user_id', self::TARGET_USER_ID)
            ->where('payment_distributions.user_type', 'creator')
            ->where('reservations.is_highest', true)
            ->select('payment_distributions.*')
            ->get();

        return [
            'total_earnings' => (float) $payments->sum('amount_eur'),
            'total_distributions' => $payments->count(),
            'avg_earnings_per_distribution' => $payments->count() > 0 ? (float) ($payments->sum('amount_eur') / $payments->count()) : 0,
            'min_earnings' => (float) ($payments->min('amount_eur') ?? 0),
            'max_earnings' => (float) ($payments->max('amount_eur') ?? 0),
            'total_sales' => $payments->count(), // Same as total_distributions for creator
            'collections_with_sales' => $payments->pluck('collection_id')->unique()->count(),
        ];
    }

    /**
     * Calcola expected results per user total earnings - USA PAYMENT_DISTRIBUTIONS REALI!
     */
    private function calculateExpectedUserTotalEarnings(): array {
        // I PaymentDistribution contengono già tutto quello che serve!
        // StatisticsService leggerà ESATTAMENTE questi stessi dati
        $payments = PaymentDistribution::join('reservations', 'payment_distributions.reservation_id', '=', 'reservations.id')
            ->where('payment_distributions.user_id', self::TARGET_USER_ID)
            ->where('reservations.is_highest', true)
            ->select('payment_distributions.*')
            ->get();

        return [
            'total_earnings' => (float) $payments->sum('amount_eur'),
            'total_distributions' => $payments->count(),
            'avg_earning_per_distribution' => $payments->count() > 0 ? (float) ($payments->sum('amount_eur') / $payments->count()) : 0,
            'collections_involved' => $payments->pluck('collection_id')->unique()->count(),
            'reservations_involved' => $payments->pluck('reservation_id')->unique()->count(),
        ];
    }

    /**
     * Calcola expected results per amounts statistics - SAME LOGIC AS StatisticsService
     */
    private function calculateExpectedAmountsStats(): array {
        // Use same ROW_NUMBER logic as StatisticsService per valid reservations
        $userCollectionIds = $this->user3Data['collections'];
        $collectionIdsStr = implode(',', $userCollectionIds);
        $trueValue = \App\Helpers\DatabaseHelper::booleanValue(true);

        $sql = "
            WITH RankedReservations AS (
                SELECT
                    r.*,
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
                WHERE r.status = 'active'
                  AND r.is_current = {$trueValue}
                  AND c.id IN ({$collectionIdsStr})
            )
            SELECT *
            FROM RankedReservations
            WHERE rn = 1
        ";

        $validReservations = collect(DB::select($sql));

        $totalEur = $validReservations->sum('offer_amount_fiat');
        $strongAmount = $validReservations->where('type', 'strong')->sum('offer_amount_fiat');
        $weakAmount = $validReservations->where('type', 'weak')->sum('offer_amount_fiat');

        return [
            'total_eur' => (float) $totalEur,
            'by_type' => [
                'strong' => (float) $strongAmount,
                'weak' => (float) $weakAmount,
            ],
        ];
    }

    /**
     * Calcola expected results per EPP potential statistics - USA PAYMENT_DISTRIBUTIONS REALI!
     */
    private function calculateExpectedEppStats(): array {
        // ALLINEAMENTO CON StatisticsService: calcola EPP da percentuali reservations,
        // NON dai PaymentDistribution EPP (quelli sono per creator_earnings)

        $totalEppQuotaEur = 0.0;

        foreach ($this->user3Data['collections'] as $collectionId) {
            // Get EPP percentage from wallets
            $eppWallet = DB::table('wallets')
                ->where('collection_id', $collectionId)
                ->where('platform_role', 'EPP')
                ->first();

            $eppPercentage = $eppWallet ? $eppWallet->royalty_mint : 20; // Default 20%

            // Get total reservations for this collection
            $reservationsTotal = Reservation::join('egis', 'reservations.egi_id', '=', 'egis.id')
                ->where('egis.collection_id', $collectionId)
                ->where('reservations.status', 'active')
                ->where('reservations.is_current', true)
                ->sum('reservations.offer_amount_fiat');

            $collectionEppQuota = ($reservationsTotal * $eppPercentage) / 100.0;
            $totalEppQuotaEur += $collectionEppQuota;
        }

        return [
            'total_quota_eur' => (float) $totalEppQuotaEur,
            'by_collection' => [], // Non serve per validation
        ];
    }

    /**
     * Calcola expected results per portfolio statistics
     */
    private function calculateExpectedPortfolioStats(): array {
        $egis = Egi::whereIn('collection_id', $this->user3Data['collections'])->get();

        $reservations = Reservation::whereIn('egi_id', $egis->pluck('id'))
            ->where('status', 'active')
            ->where('is_current', true)
            ->get();

        $reservedEgis = $reservations->pluck('egi_id')->unique()->count();
        $availableEgis = $egis->count() - $reservedEgis;
        $highestOffer = $reservations->max('offer_amount_fiat') ?? 0;
        $totalValueEur = $reservations->sum('amount_eur');

        return [
            'total_egis' => $egis->count(),
            'total_collections' => count($this->user3Data['collections']),
            'reserved_egis' => $reservedEgis,
            'available_egis' => $availableEgis,
            'highest_offer' => (float) $highestOffer,
            'total_value_eur' => (float) $totalValueEur,
        ];
    }

    /**
     * 📅 Genera date deterministiche partendo da OGGI e andando indietro 24 mesi
     * Per presentazioni realistiche con dati attuali
     */
    private function getRandomDateInYears(): Carbon {
        static $counter = 0;
        $counter++;

        $today = Carbon::now(); // Oggi: 2025-10-01
        $monthsBack = self::CONFIG['months_back']; // 24 mesi

        // Distribuzione deterministica negli ultimi 24 mesi
        // Con focus maggiore sui mesi più recenti (80% negli ultimi 12 mesi)
        if (($counter % 10) < 8) {
            // 80% dei dati negli ultimi 12 mesi (più recenti)
            $monthsOffset = $counter % 12;
        } else {
            // 20% dei dati nei 12 mesi precedenti (12-24 mesi fa)
            $monthsOffset = 12 + ($counter % 12);
        }

        // Crea data andando indietro dal oggi
        $baseDate = $today->copy()->subMonths($monthsOffset);

        // Aggiunge variazione deterministica nel mese
        $dayOffset = ($counter % 28);
        $hourOffset = ($counter % 24);

        return $baseDate->addDays($dayOffset)->addHours($hourOffset);
    }

    /**
     * 🧼 Cleanup method per il comando --cleanup
     */
    public function cleanup(): void {
        $this->loadUser3Data();
        $this->clearExistingData();
    }
}