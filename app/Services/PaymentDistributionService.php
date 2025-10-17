<?php

namespace App\Services;

use App\Enums\Gdpr\GdprActivityCategory;
use App\Models\PaymentDistribution;
use App\Models\Reservation;
use App\Models\UserActivity;
use App\Models\Wallet;
use App\Enums\PaymentDistribution\UserTypeEnum;
use App\Enums\PaymentDistribution\DistributionStatusEnum;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

/**
 * @Oracode Service: Payment Distribution Service
 * 🎯 Purpose: Automated payment distribution system with GDPR compliance
 * 🛡️ Privacy: GDPR-compliant activity logging with UEM/ULM integration
 * 🧱 Core Logic: Calculate and create distributions from collection wallets
 *
 * @package App\Services
 * @author GitHub Copilot for Fabio Cherici
 * @version 1.0.0
 * @date 2025-08-20
 */
class PaymentDistributionService {
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;

    /**
     * Constructor with UEM/ULM dependency injection
     *
     * @param UltraLogManager $logger
     * @param ErrorManagerInterface $errorManager
     */
    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
    }

    /**
     * Create distributions for a reservation (treating reservations as virtual payments)
     * GDPR Compliance: All activities logged with user_activities for audit trail
     *
     * @param Reservation $reservation
     * @return array
     */
    public function createDistributionsForReservation(Reservation $reservation): array {
        try {
            // Start database transaction
            return DB::transaction(function () use ($reservation) {
                // Validate reservation
                $this->validateReservationForDistribution($reservation);

                // Get collection and validate wallets
                $collection = $reservation->egi->collection;

                // Log operation start
                if ($this->logger) {
                    $this->logger->info('[Payment Distribution] Starting distribution creation', [
                        'reservation_id' => $reservation->id,
                        'collection_id' => $collection->id,
                        'amount_eur' => $reservation->amount_eur,
                        'user_id' => auth()->id(),
                        'timestamp' => now()->toIso8601String()
                    ]);
                }

                // Get all wallets for the collection
                $wallets = $this->getCollectionWallets($collection);

                // Calculate distributions for all wallets
                $distributionsData = $this->calculateDistributions($reservation, $wallets);

                // Create distribution records in database
                $distributions = $this->createDistributionRecords($distributionsData);

                // Log GDPR-compliant user activities
                $this->logUserActivities($reservation, $distributions);

                // Log completion
                if ($this->logger) {
                    $this->logger->info('[Payment Distribution] Distribution creation completed', [
                        'reservation_id' => $reservation->id,
                        'total_distributions' => $distributions->count(),
                        'total_amount' => $distributions->sum('amount_eur')
                    ]);
                }

                return $distributions->toArray();
            });
        } catch (\Exception $e) {
            // Handle error with UEM
            $this->errorManager->handle('PAYMENT_DISTRIBUTION_ERROR', [
                'reservation_id' => $reservation->id,
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'operation' => 'createDistributionsForReservation',
                'timestamp' => now()->toIso8601String(),
                'error' => $e->getMessage()
            ], $e);

            throw $e;
        }
    }

    /**
     * Validate that reservation is eligible for distribution
     * Note: All reservations are treated as virtual payments, only 'highest' rank counts for stats
     *
     * @param Reservation $reservation
     * @throws \Exception
     */
    private function validateReservationForDistribution(Reservation $reservation): void {
        // Check if reservation is active (current system only uses 'active' status)
        if ($reservation->status !== 'active') {
            $this->errorManager->handle('RESERVATION_NOT_ACTIVE', [
                'reservation_id' => $reservation->id,
                'current_status' => $reservation->status,
                'user_id' => auth()->id(),
                'timestamp' => now()->toIso8601String()
            ]);
            throw new \Exception("Reservation {$reservation->id} is not active");
        }

        if (!$reservation->amount_eur || $reservation->amount_eur <= 0) {
            $this->errorManager->handle('INVALID_AMOUNT', [
                'reservation_id' => $reservation->id,
                'amount_eur' => $reservation->amount_eur,
                'user_id' => auth()->id(),
                'timestamp' => now()->toIso8601String()
            ]);
            throw new \Exception("Reservation {$reservation->id} has invalid amount_eur");
        }

        if (!$reservation->egi || !$reservation->egi->collection) {
            $this->errorManager->handle('COLLECTION_NOT_FOUND', [
                'reservation_id' => $reservation->id,
                'user_id' => auth()->id(),
                'timestamp' => now()->toIso8601String()
            ]);
            throw new \Exception("Reservation {$reservation->id} has no associated collection");
        }

        // Check if distributions already exist
        $existingDistributions = PaymentDistribution::where('reservation_id', $reservation->id)->count();
        if ($existingDistributions > 0) {
            if ($this->logger) {
                $this->logger->warning('[Payment Distribution] Distributions already exist', [
                    'reservation_id' => $reservation->id,
                    'existing_distributions' => $existingDistributions
                ]);
            }
            throw new \Exception("Distributions already exist for reservation {$reservation->id}");
        }
    }

    /**
     * Get collection wallets with validation
     *
     * @param \App\Models\Collection $collection
     * @return \Illuminate\Database\Eloquent\Collection<Wallet>
     * @throws \Exception
     */
    private function getCollectionWallets(\App\Models\Collection $collection) {
        $wallets = $collection->wallets()->with('user')->get();

        if ($wallets->isEmpty()) {
            $this->errorManager->handle('NO_WALLETS_FOUND', [
                'collection_id' => $collection->id,
                'user_id' => auth()->id(),
                'timestamp' => now()->toIso8601String()
            ]);
            throw new \Exception("No wallets found for collection {$collection->id}");
        }

        // Validate total mint percentages sum to 100%
        $totalMintPercentage = $wallets->sum('royalty_mint');
        if ($totalMintPercentage != 100) {
            $this->errorManager->handle('INVALID_MINT_PERCENTAGES', [
                'collection_id' => $collection->id,
                'current_percentage' => $totalMintPercentage,
                'expected_percentage' => 100,
                'user_id' => auth()->id(),
                'timestamp' => now()->toIso8601String()
            ]);
            throw new \Exception("Collection {$collection->id} wallet percentages don't sum to 100% (current: {$totalMintPercentage}%)");
        }

        return $wallets;
    }

    /**
     * Calculate distributions based on wallet percentages
     *
     * @param Reservation $reservation
     * @param \Illuminate\Database\Eloquent\Collection<Wallet> $wallets
     * @return array
     */
    private function calculateDistributions(Reservation $reservation, $wallets): array {
        $distributions = [];
        $totalAmount = $reservation->amount_eur;
        $exchangeRate = $reservation->payment_exchange_rate ?? 1.0;

        foreach ($wallets as $wallet) {
            $percentage = $wallet->royalty_mint;
            $amount = round(($totalAmount * $percentage) / 100, 2);

            // Determine user type from wallet platform_role and user data
            $userType = $this->determineUserType($wallet);

            $distributions[] = [
                'egi_id' => $reservation->egi_id, // Direct EGI reference
                'reservation_id' => $reservation->id,
                'collection_id' => $reservation->egi->collection_id,
                'user_id' => $wallet->user_id,
                'wallet_id' => $wallet->id, // 🎯 NEW: Reference to wallet (SOURCE OF TRUTH)
                'user_type' => $userType, // Backward compat
                'platform_role' => $wallet->platform_role, // 🎯 NEW: Wallet role (Natan, EPP, Frangette, Creator) - SOURCE OF TRUTH
                'percentage' => $percentage,
                'amount_eur' => $amount,
                'exchange_rate' => $exchangeRate,
                'is_epp' => $this->isEppWallet($wallet),
                'distribution_status' => DistributionStatusEnum::PENDING,
                'metadata' => [
                    'wallet_id' => $wallet->id, // Keep in metadata for backward compat
                    'wallet_address' => $wallet->wallet,
                    'platform_role' => $wallet->platform_role,
                    'calculation_timestamp' => now()->toISOString(),
                    'reservation_type' => $reservation->type,
                    'reservation_rank' => $reservation->rank,
                    'is_highest_rank' => $reservation->rank === 1,
                    'counts_for_stats' => $reservation->rank === 1, // Solo rank #1 conta per statistiche
                ]
            ];
        }

        return $distributions;
    }

    /**
     * Determine user type from wallet and user data
     *
     * @param Wallet $wallet
     * @return UserTypeEnum
     */
    private function determineUserType(Wallet $wallet): UserTypeEnum {
        // Priority 1: Platform role mapping
        if ($wallet->platform_role === 'EPP') {
            return UserTypeEnum::EPP;
        }

        if ($wallet->platform_role === 'Creator') {
            return UserTypeEnum::CREATOR;
        }

        // Priority 2: User type from user model
        if ($wallet->user && $wallet->user->usertype) {
            $usertype = $wallet->user->usertype;

            return match ($usertype) {
                'weak' => UserTypeEnum::WEAK,
                'creator' => UserTypeEnum::CREATOR,
                'collector' => UserTypeEnum::COLLECTOR,
                'commissioner' => UserTypeEnum::COMMISSIONER,
                'company' => UserTypeEnum::COMPANY,
                'epp' => UserTypeEnum::EPP,
                'trader-pro' => UserTypeEnum::TRADER_PRO,
                'vip' => UserTypeEnum::VIP,
                default => UserTypeEnum::COLLECTOR, // Default fallback
            };
        }

        // Default fallback
        return UserTypeEnum::COLLECTOR;
    }

    /**
     * Check if wallet is EPP-related
     *
     * @param Wallet $wallet
     * @return bool
     */
    private function isEppWallet(Wallet $wallet): bool {
        return $wallet->platform_role === 'EPP' ||
            ($wallet->user && $wallet->user->usertype === 'epp');
    }

    /**
     * Create distribution records in database
     *
     * @param array $distributions
     * @return \Illuminate\Database\Eloquent\Collection<PaymentDistribution>
     */
    private function createDistributionRecords(array $distributions) {
        $createdDistributions = collect();

        foreach ($distributions as $distributionData) {
            $distribution = PaymentDistribution::create($distributionData);
            $createdDistributions->push($distribution);
        }

        return $createdDistributions;
    }

    /**
     * Log GDPR-compliant user activities for each distribution
     *
     * @param Reservation $reservation
     * @param \Illuminate\Database\Eloquent\Collection<PaymentDistribution> $distributions
     */
    private function logUserActivities(Reservation $reservation, $distributions): void {
        foreach ($distributions as $distribution) {
            try {
                UserActivity::create([
                    'user_id' => $distribution->user_id,
                    'action' => 'payment_distribution_created',
                    'category' => GdprActivityCategory::BLOCKCHAIN_ACTIVITY,
                    'context' => [
                        'reservation_id' => $reservation->id,
                        'collection_id' => $distribution->collection_id,
                        'distribution_id' => $distribution->id,
                        'amount_eur' => $distribution->amount_eur,
                        'percentage' => $distribution->percentage,
                        'user_type' => $distribution->user_type->value,
                        'is_epp' => $distribution->is_epp,
                        'transaction_type' => 'payment_distribution'
                    ],
                    'metadata' => [
                        'egi_id' => $reservation->egi_id,
                        'original_amount' => $reservation->amount_eur,
                        'exchange_rate' => $distribution->exchange_rate,
                        'distribution_status' => $distribution->distribution_status->value,
                        'source' => 'PaymentDistributionService'
                    ],
                    'privacy_level' => GdprActivityCategory::BLOCKCHAIN_ACTIVITY->privacyLevel(), // Financial data requires high privacy
                    'ip_address' => request()->ip() ?? '127.0.0.1',
                    'user_agent' => request()->userAgent() ?? 'System/PaymentDistributionService',
                    'expires_at' => now()->addYears(7), // GDPR retention for financial records
                ]);
            } catch (\Exception $e) {
                // Log error but don't fail the whole process - use UEM with non-blocking error
                $this->errorManager->handle('USER_ACTIVITY_LOGGING_FAILED', [
                    'user_id' => $distribution->user_id,
                    'distribution_id' => $distribution->id,
                    'operation' => 'logUserActivities',
                    'error' => $e->getMessage(),
                    'timestamp' => now()->toIso8601String()
                ], $e);
            }
        }
    }

    /**
     * Get distribution statistics for analytics
     * Note: Only 'highest' rank reservations count for real statistics
     *
     * @param \App\Models\Collection $collection
     * @param string|null $startDate
     * @param string|null $endDate
     * @return array
     */
    public function getDistributionStats(\App\Models\Collection $collection, ?string $startDate = null, ?string $endDate = null): array {
        $query = PaymentDistribution::where('collection_id', $collection->id)
            ->whereHas('reservation', function ($q) {
                $q->where('rank', 1); // Only count #1 highest reservations for stats
            });

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        $distributions = $query->with(['user', 'reservation'])->get();

        return [
            'total_distributions' => $distributions->count(),
            'total_amount_eur' => $distributions->sum('amount_eur'),
            'epp_distributions' => $distributions->where('is_epp', true)->count(),
            'epp_amount_eur' => $distributions->where('is_epp', true)->sum('amount_eur'),
            'by_user_type' => $distributions->groupBy('user_type')->map(function ($group) {
                return [
                    'count' => $group->count(),
                    'amount_eur' => $group->sum('amount_eur'),
                ];
            }),
            'by_status' => $distributions->groupBy('distribution_status')->map(function ($group) {
                return [
                    'count' => $group->count(),
                    'amount_eur' => $group->sum('amount_eur'),
                ];
            }),
        ];
    }

    /**
     * Get ALL distribution tracking (including non-highest ranks)
     * Use this for complete audit trail, not for statistics
     *
     * @param \App\Models\Collection $collection
     * @param string|null $startDate
     * @param string|null $endDate
     * @return array
     */
    public function getAllDistributionTracking(\App\Models\Collection $collection, ?string $startDate = null, ?string $endDate = null): array {
        $query = PaymentDistribution::where('collection_id', $collection->id);

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        $distributions = $query->with(['user', 'reservation'])->get();

        return [
            'total_tracking_entries' => $distributions->count(),
            'highest_rank_count' => $distributions->whereHas('reservation', fn($q) => $q->where('rank', 1))->count(),
            'other_ranks_count' => $distributions->whereHas('reservation', fn($q) => $q->where('rank', '>', 1))->count(),
            'total_virtual_amount' => $distributions->sum('amount_eur'),
            'by_rank' => $distributions->groupBy('reservation.rank')->map(function ($group) {
                return [
                    'count' => $group->count(),
                    'amount_eur' => $group->sum('amount_eur'),
                ];
            }),
        ];
    }

    // ========================================================================
    // PHASE 2: MINT-BASED DISTRIBUTION METHODS (AREA 2.2.1)
    // ========================================================================

    /**
     * Create distributions for a mint transaction (Phase 2)
     * Splits payment according to collection wallet percentages
     * GDPR Compliance: Full audit trail with user_activities
     *
     * @param \App\Models\EgiBlockchain $egiBlockchain
     * @param array $paymentData ['paid_amount' => float, 'paid_currency' => string, 'payment_method' => string]
     * @return array
     * @throws \Exception
     */
    public function recordMintDistribution(\App\Models\EgiBlockchain $egiBlockchain, array $paymentData): array {
        try {
            // Start database transaction
            return DB::transaction(function () use ($egiBlockchain, $paymentData) {
                // Validate mint record
                $this->validateMintForDistribution($egiBlockchain, $paymentData);

                // Get collection and validate wallets
                $collection = $egiBlockchain->egi->collection;

                // Log operation start
                if ($this->logger) {
                    $this->logger->info('[Payment Distribution] Starting mint distribution creation', [
                        'egi_blockchain_id' => $egiBlockchain->id,
                        'egi_id' => $egiBlockchain->egi_id,
                        'collection_id' => $collection->id,
                        'paid_amount' => $paymentData['paid_amount'],
                        'paid_currency' => $paymentData['paid_currency'],
                        'buyer_user_id' => $egiBlockchain->buyer_user_id,
                        'timestamp' => now()->toIso8601String()
                    ]);
                }

                // Get all wallets for the collection
                $wallets = $this->getCollectionWallets($collection);

                // Calculate distributions for all wallets (mint-based)
                $distributionsData = $this->calculateMintDistributions($egiBlockchain, $paymentData, $wallets);

                // Create distribution records in database
                $distributions = $this->createDistributionRecords($distributionsData);

                // Log GDPR-compliant user activities (mint-based)
                $this->logMintUserActivities($egiBlockchain, $distributions);

                // Log completion
                if ($this->logger) {
                    $this->logger->info('[Payment Distribution] Mint distribution creation completed', [
                        'egi_blockchain_id' => $egiBlockchain->id,
                        'total_distributions' => $distributions->count(),
                        'total_amount' => $distributions->sum('amount_eur')
                    ]);
                }

                return $distributions->toArray();
            });
        } catch (\Exception $e) {
            // Handle error with UEM
            $this->errorManager->handle('MINT_DISTRIBUTION_ERROR', [
                'egi_blockchain_id' => $egiBlockchain->id,
                'egi_id' => $egiBlockchain->egi_id,
                'buyer_user_id' => $egiBlockchain->buyer_user_id,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'operation' => 'recordMintDistribution',
                'timestamp' => now()->toIso8601String(),
                'error' => $e->getMessage()
            ], $e);

            throw $e;
        }
    }

    /**
     * Validate that mint record is eligible for distribution
     *
     * @param \App\Models\EgiBlockchain $egiBlockchain
     * @param array $paymentData
     * @throws \Exception
     */
    private function validateMintForDistribution(\App\Models\EgiBlockchain $egiBlockchain, array $paymentData): void {
        // Check if mint is completed (use mint_status, NOT status - field doesn't exist)
        if ($egiBlockchain->mint_status !== 'minted') {
            $this->errorManager->handle('MINT_NOT_COMPLETED', [
                'egi_blockchain_id' => $egiBlockchain->id,
                'current_mint_status' => $egiBlockchain->mint_status,
                'buyer_user_id' => $egiBlockchain->buyer_user_id,
                'timestamp' => now()->toIso8601String()
            ]);
            throw new \Exception("Mint {$egiBlockchain->id} is not completed (mint_status: {$egiBlockchain->mint_status})");
        }

        // Validate payment amount
        if (!isset($paymentData['paid_amount']) || $paymentData['paid_amount'] <= 0) {
            $this->errorManager->handle('INVALID_PAYMENT_AMOUNT', [
                'egi_blockchain_id' => $egiBlockchain->id,
                'paid_amount' => $paymentData['paid_amount'] ?? null,
                'buyer_user_id' => $egiBlockchain->buyer_user_id,
                'timestamp' => now()->toIso8601String()
            ]);
            throw new \Exception("Invalid payment amount for mint {$egiBlockchain->id}");
        }

        // Validate paid_currency
        if (!isset($paymentData['paid_currency']) || empty($paymentData['paid_currency'])) {
            $paymentData['paid_currency'] = 'EUR'; // Default to EUR
        }

        // Check if EGI and collection exist
        if (!$egiBlockchain->egi || !$egiBlockchain->egi->collection) {
            $this->errorManager->handle('COLLECTION_NOT_FOUND', [
                'egi_blockchain_id' => $egiBlockchain->id,
                'egi_id' => $egiBlockchain->egi_id,
                'timestamp' => now()->toIso8601String()
            ]);
            throw new \Exception("Mint {$egiBlockchain->id} has no associated collection");
        }

        // Check if distributions already exist for this mint
        $existingDistributions = PaymentDistribution::where('egi_blockchain_id', $egiBlockchain->id)->count();
        if ($existingDistributions > 0) {
            if ($this->logger) {
                $this->logger->warning('[Payment Distribution] Distributions already exist for mint', [
                    'egi_blockchain_id' => $egiBlockchain->id,
                    'existing_distributions' => $existingDistributions
                ]);
            }
            throw new \Exception("Distributions already exist for mint {$egiBlockchain->id}");
        }
    }

    /**
     * Calculate distributions for mint based on wallet percentages
     *
     * @param \App\Models\EgiBlockchain $egiBlockchain
     * @param array $paymentData
     * @param \Illuminate\Database\Eloquent\Collection<Wallet> $wallets
     * @return array
     */
    private function calculateMintDistributions(\App\Models\EgiBlockchain $egiBlockchain, array $paymentData, $wallets): array {
        $distributions = [];
        $totalAmount = $paymentData['paid_amount'];
        $currency = $paymentData['paid_currency'] ?? 'EUR';

        foreach ($wallets as $wallet) {
            $percentage = $wallet->royalty_mint;
            $amount = round(($totalAmount * $percentage) / 100, 2);

            // Determine user type from wallet platform_role and user data
            $userType = $this->determineUserType($wallet);

            $distributions[] = [
                'source_type' => 'mint', // NEW: Phase 2 source type
                'egi_id' => $egiBlockchain->egi_id, // Direct EGI reference
                'reservation_id' => null, // NULL for mint-based distributions (minter ≠ last reserver)
                'egi_blockchain_id' => $egiBlockchain->id, // NEW: Link to blockchain record
                'blockchain_tx_id' => $egiBlockchain->algorand_tx_id, // NEW: Algorand TXID
                'collection_id' => $egiBlockchain->egi->collection_id,
                'user_id' => $wallet->user_id,
                'wallet_id' => $wallet->id, // 🎯 NEW: Reference to wallet (SOURCE OF TRUTH)
                'user_type' => $userType, // Backward compat
                'platform_role' => $wallet->platform_role, // 🎯 NEW: Wallet role (Natan, EPP, Frangette, Creator) - SOURCE OF TRUTH
                'percentage' => $percentage,
                'amount_eur' => $amount,
                'exchange_rate' => 1.0, // TODO: Implement multi-currency exchange rates
                'is_epp' => $this->isEppWallet($wallet),
                'distribution_status' => DistributionStatusEnum::CONFIRMED, // ✅ MINT = payment already confirmed on blockchain
                'metadata' => [
                    'wallet_id' => $wallet->id, // Keep in metadata for backward compat
                    'wallet_address' => $wallet->wallet,
                    'platform_role' => $wallet->platform_role, // Keep in metadata for backward compat
                    'calculation_timestamp' => now()->toISOString(),
                    'payment_method' => $paymentData['payment_method'] ?? 'unknown',
                    'paid_currency' => $currency,
                    'buyer_wallet' => $egiBlockchain->buyer_wallet,
                    'buyer_user_id' => $egiBlockchain->buyer_user_id,
                    'ownership_type' => $egiBlockchain->ownership_type,
                ]
            ];
        }

        return $distributions;
    }

    /**
     * Log GDPR-compliant user activities for mint distributions
     *
     * @param \App\Models\EgiBlockchain $egiBlockchain
     * @param \Illuminate\Database\Eloquent\Collection<PaymentDistribution> $distributions
     */
    private function logMintUserActivities(\App\Models\EgiBlockchain $egiBlockchain, $distributions): void {
        foreach ($distributions as $distribution) {
            try {
                UserActivity::create([
                    'user_id' => $distribution->user_id,
                    'action' => 'mint_payment_distribution_created',
                    'category' => GdprActivityCategory::BLOCKCHAIN_ACTIVITY,
                    'context' => [
                        'egi_blockchain_id' => $egiBlockchain->id,
                        'egi_id' => $egiBlockchain->egi_id,
                        'collection_id' => $distribution->collection_id,
                        'distribution_id' => $distribution->id,
                        'amount_eur' => $distribution->amount_eur,
                        'percentage' => $distribution->percentage,
                        'user_type' => $distribution->user_type->value,
                        'is_epp' => $distribution->is_epp,
                        'transaction_type' => 'mint_payment_distribution',
                        'source_type' => 'mint'
                    ],
                    'metadata' => [
                        'algorand_tx_id' => $egiBlockchain->algorand_tx_id,
                        'buyer_wallet' => $egiBlockchain->buyer_wallet,
                        'buyer_user_id' => $egiBlockchain->buyer_user_id,
                        'ownership_type' => $egiBlockchain->ownership_type,
                        'paid_amount' => $egiBlockchain->paid_amount,
                        'paid_currency' => $egiBlockchain->paid_currency,
                        'distribution_status' => $distribution->distribution_status->value,
                        'source' => 'PaymentDistributionService::recordMintDistribution'
                    ],
                    'privacy_level' => GdprActivityCategory::BLOCKCHAIN_ACTIVITY->privacyLevel(),
                    'ip_address' => request()->ip() ?? '127.0.0.1',
                    'user_agent' => request()->userAgent() ?? 'System/PaymentDistributionService',
                    'expires_at' => now()->addYears(7), // GDPR retention for financial records
                ]);
            } catch (\Exception $e) {
                // Log error but don't fail the whole process
                $this->errorManager->handle('USER_ACTIVITY_LOGGING_FAILED', [
                    'user_id' => $distribution->user_id,
                    'distribution_id' => $distribution->id,
                    'egi_blockchain_id' => $egiBlockchain->id,
                    'operation' => 'logMintUserActivities',
                    'error' => $e->getMessage(),
                    'timestamp' => now()->toIso8601String()
                ], $e);
            }
        }
    }

    /**
     * Get distributions for a specific mint (Phase 2)
     *
     * @param \App\Models\Egi $egi
     * @return \Illuminate\Database\Eloquent\Collection<PaymentDistribution>
     */
    public function getMintDistributions(\App\Models\Egi $egi) {
        return PaymentDistribution::where('source_type', 'mint')
            ->whereHas('egiBlockchain', function ($query) use ($egi) {
                $query->where('egi_id', $egi->id);
            })
            ->with(['user', 'egiBlockchain'])
            ->get();
    }

    /**
     * Compare DB distributions vs Blockchain records (Phase 2 utility)
     * Verifies consistency between database and blockchain
     *
     * @param \App\Models\Egi $egi
     * @return array
     */
    public function compareDbVsBlockchain(\App\Models\Egi $egi): array {
        // Get mint-based distributions from DB
        $dbDistributions = $this->getMintDistributions($egi);

        // Get blockchain record
        $blockchainRecord = $egi->egiBlockchain()
            ->where('status', 'minted')
            ->first();

        if (!$blockchainRecord) {
            return [
                'status' => 'no_blockchain_record',
                'message' => 'No minted blockchain record found for this EGI',
                'db_distributions_count' => $dbDistributions->count(),
                'blockchain_tx_id' => null,
            ];
        }

        // Compare totals
        $dbTotalAmount = $dbDistributions->sum('amount_eur');
        $blockchainAmount = $blockchainRecord->paid_amount;

        $isConsistent = abs($dbTotalAmount - $blockchainAmount) < 0.01; // 1 cent tolerance

        return [
            'status' => $isConsistent ? 'consistent' : 'inconsistent',
            'db_total_amount' => round($dbTotalAmount, 2),
            'blockchain_amount' => round($blockchainAmount, 2),
            'difference' => round($dbTotalAmount - $blockchainAmount, 2),
            'db_distributions_count' => $dbDistributions->count(),
            'blockchain_tx_id' => $blockchainRecord->algorand_tx_id,
            'distributions' => $dbDistributions->map(function ($dist) {
                return [
                    'user_id' => $dist->user_id,
                    'user_type' => $dist->user_type->value,
                    'amount_eur' => $dist->amount_eur,
                    'percentage' => $dist->percentage,
                    'is_epp' => $dist->is_epp,
                ];
            }),
        ];
    }
}
