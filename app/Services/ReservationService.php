<?php

namespace App\Services;

use App\Models\Egi;
use App\Models\Reservation;
use App\Models\User;
use App\Services\CurrencyService;
use App\Services\CertificateGeneratorService;
use App\Services\Notifications\ReservationNotificationService;
use App\Services\PaymentDistributionService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Ultra\UltraLogManager\UltraLogManager;
use Exception;
use Illuminate\Http\RedirectResponse;

/**
 * @package App\Services
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 2.0.0 (FlorenceEGI - Integrated Pre-Launch & Legacy System)
 * @date 2025-08-15
 * @purpose Unified service managing both legacy priority system and new pre-launch ranking
 *
 * Combines:
 * - Legacy priority-based reservation system
 * - New pre-launch public ranking system
 * - Multi-currency support with EUR canonical
 * - Certificate generation
 * - Notification integration
 */
class ReservationService {
    /**
     * Service dependencies
     */
    protected UltraLogManager $logger;
    protected CertificateGeneratorService $certificateGenerator;
    protected CurrencyService $currencyService;
    protected ReservationNotificationService $notificationService;
    protected ErrorManagerInterface $errorManager;
    protected PaymentDistributionService $paymentDistributionService;

    /**
     * Constructor with dependency injection
     */
    public function __construct(
        UltraLogManager $logger,
        CertificateGeneratorService $certificateGenerator,
        CurrencyService $currencyService,
        ReservationNotificationService $notificationService,
        ErrorManagerInterface $errorManager,
        PaymentDistributionService $paymentDistributionService
    ) {
        $this->logger = $logger;
        $this->certificateGenerator = $certificateGenerator;
        $this->currencyService = $currencyService;
        $this->notificationService = $notificationService;
        $this->errorManager = $errorManager;
        $this->paymentDistributionService = $paymentDistributionService;
    }

    /**
     * Create a new reservation (LEGACY METHOD - mantiene compatibilità)
     *
     * @param array $data The reservation data
     * @param User|null $user The user making the reservation (null for wallet-only)
     * @param string|null $walletAddress The wallet address for weak auth users
     * @return Reservation|null
     * @throws \Exception If the reservation cannot be created
     */
    public function createReservation(array $data, ?User $user = null, ?string $walletAddress = null): ?Reservation {
        // Start a transaction to ensure data consistency
        return DB::transaction(function () use ($data, $user, $walletAddress) {
            $egi = Egi::findOrFail($data['egi_id']);

            // Check if EGI is available for reservation
            if (!$this->canReserveEgi($egi)) {
                $this->logger->warning('[RESERVATION] Attempted to reserve unavailable EGI', [
                    'egi_id' => $egi->id,
                    'user_id' => $user?->id,
                    'wallet' => $walletAddress ?? ($user?->wallet ?? 'unknown')
                ]);

                return $this->errorManager->handle('RESERVATION_EGI_NOT_AVAILABLE', [
                    'egi_id' => $egi->id
                ]);
            }

            // Convert FIAT amount to appropriate format
            $offerAmountFiat = (float) $data['offer_amount_fiat'];

            $this->logger->info('[RESERVATION] Creation check started', [
                'user_id' => $user?->id,
                'egi_id' => $data['egi_id'],
                'new_offer_amount_fiat' => $offerAmountFiat,
                'has_user' => $user !== null
            ]);

            // Check for relaunch (rilancio) - amount must be higher
            if ($user) {
                $previousReservation = Reservation::where('user_id', $user->id)
                    ->where('egi_id', $data['egi_id'])
                    ->where('status', 'active')
                    ->orderBy('created_at', 'desc')
                    ->first();

                $this->logger->info('[RESERVATION] Previous reservation check', [
                    'user_id' => $user->id,
                    'egi_id' => $data['egi_id'],
                    'previous_reservation_found' => $previousReservation !== null,
                    'previous_amount' => $previousReservation?->offer_amount_fiat,
                    'new_amount' => $offerAmountFiat,
                ]);

                if ($previousReservation && $offerAmountFiat <= $previousReservation->offer_amount_fiat) {
                    $this->logger->warning('[RESERVATION] Relaunch blocked - insufficient amount', [
                        'user_id' => $user->id,
                        'egi_id' => $data['egi_id'],
                        'previous_amount' => $previousReservation->offer_amount_fiat,
                        'new_amount' => $offerAmountFiat
                    ]);

                    return $this->errorManager->handle('RESERVATION_INSUFFICIENT_AMOUNT', [
                        'previous_amount' => $previousReservation->offer_amount_fiat,
                        'new_amount' => $offerAmountFiat,
                        'message' => 'Il tuo rilancio deve essere superiore alla tua prenotazione precedente di €' .
                            number_format($previousReservation->offer_amount_fiat, 2) .
                            '. Hai inserito €' . number_format($offerAmountFiat, 2) . '.'
                    ]);
                }

                // We'll handle superseding after creation to have the new reservation ID
            }

            // Determine reservation type
            $reservationType = $user && !$user->is_weak_auth ? Reservation::TYPE_STRONG : Reservation::TYPE_WEAK;

            // Ensure we have either a user or a wallet address
            if (!$user && !$walletAddress) {
                return $this->errorManager->handle('RESERVATION_UNAUTHORIZED', [
                    'message' => 'Either user or wallet address is required'
                ]);
            }

            // Get exchange rate data
            $fiatCurrency = $data['fiat_currency'] ?? 'EUR';
            $rateData = $this->currencyService->getAlgoToFiatRate($fiatCurrency);

            if (!$rateData) {
                return $this->errorManager->handle('CURRENCY_EXCHANGE_SERVICE_UNAVAILABLE', [
                    'operation' => 'create_reservation',
                    'fiat_currency' => $fiatCurrency,
                    'user_id' => $user?->id,
                    'egi_id' => $egi->id
                ]);
            }

            $exchangeRate = $rateData['rate'];

            // Convert FIAT amount to ALGO (microALGO)
            $offerAmountAlgo = $this->currencyService->convertFiatToMicroAlgo(
                $offerAmountFiat,
                $exchangeRate
            );

            // Calculate EUR amount for new system
            $amountEur = $this->convertToEur($offerAmountFiat, $fiatCurrency);

            // Create the reservation
            $reservation = new Reservation([
                'user_id' => $user?->id,
                'egi_id' => $egi->id,
                'type' => $reservationType,
                'status' => Reservation::STATUS_ACTIVE,
                // New pre-launch fields
                'amount_eur' => $amountEur,
                'display_currency' => $fiatCurrency,
                'display_amount' => $offerAmountFiat,
                'input_currency' => $fiatCurrency,
                'input_amount' => $offerAmountFiat,
                'input_timestamp' => now(),
                // Legacy fields for compatibility
                'original_currency' => $fiatCurrency,
                'original_price' => $offerAmountFiat,
                'algo_price' => $offerAmountAlgo / 1_000_000, // Convert microALGO to ALGO
                'offer_amount_fiat' => $offerAmountFiat,
                'fiat_currency' => $fiatCurrency,
                'offer_amount_algo' => $offerAmountAlgo,
                'exchange_rate' => $exchangeRate,
                'rate_timestamp' => $rateData['timestamp'],
                'exchange_timestamp' => $rateData['timestamp'],
                'is_current' => true,
                'contact_data' => $data['contact_data'] ?? null
            ]);

            $reservation->save();

            // Now supersede user's previous reservations for this EGI
            if ($user) {
                $this->supersedeUserPreviousReservations($user->id, $data['egi_id'], $reservation->id);
            }

            // Log financial operation for audit trail
            $this->logger->info('[FINANCIAL] Reservation created with currency conversion', [
                'reservation_id' => $reservation->id,
                'egi_id' => $egi->id,
                'user_id' => $user?->id,
                'wallet_address' => $walletAddress ?? ($user?->wallet ?? 'unknown'),
                'amount_eur' => $amountEur,
                'offer_amount_fiat' => $offerAmountFiat,
                'fiat_currency' => $fiatCurrency,
                'offer_amount_algo' => $offerAmountAlgo,
                'exchange_rate' => $exchangeRate,
                'exchange_timestamp' => $rateData['timestamp'],
                'conversion_source' => 'coingecko',
                'type' => $reservationType
            ]);

            // Process existing reservations to maintain priority (legacy)
            $this->processReservationPriorities($reservation);

            // Update rankings for new pre-launch system
            $this->updateEgiRankings($egi->id);

            // Refresh reservation to get updated ranking data
            $reservation->refresh();

            // Generate a certificate for the reservation
            $certificateData = [
                'wallet_address' => $walletAddress ?? $user->wallet ?? 'unknown'
            ];

            $this->certificateGenerator->generateCertificate($reservation, $certificateData);

            $this->logger->info('[RESERVATION] New reservation created successfully', [
                'reservation_id' => $reservation->id,
                'egi_id' => $egi->id,
                'type' => $reservationType,
                'amount_eur' => $amountEur,
                'offer_amount_fiat' => $offerAmountFiat,
                'fiat_currency' => $fiatCurrency,
                'rank_position' => $reservation->rank_position,
                'is_highest' => $reservation->is_highest
            ]);

            // Check if this reservation is now the highest and send notifications
            $this->checkAndNotifyIfHighest($reservation);

            // 🎯 PAYMENT DISTRIBUTIONS: Create distributions for this reservation
            try {
                $distributions = $this->paymentDistributionService->createDistributionsForReservation($reservation);

                $this->logger->info('[PAYMENT_DISTRIBUTIONS] Created distributions for reservation (createReservation)', [
                    'reservation_id' => $reservation->id,
                    'distributions_count' => count($distributions),
                    'total_amount' => array_sum(array_column($distributions, 'amount_eur')),
                    'method' => 'createReservation'
                ]);
            } catch (\Exception $e) {
                // Log error but don't fail the reservation process
                $this->logger->error('[PAYMENT_DISTRIBUTIONS] Failed to create distributions (createReservation)', [
                    'reservation_id' => $reservation->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }

            return $reservation;
        });
    }

    /**
     * Create reservation for pre-launch system (NEW METHOD)
     *
     * @param Egi $egi The EGI to reserve
     * @param User $user The user making the reservation
     * @param float $amountEur Amount in EUR (canonical)
     * @param string $displayCurrency Currency for display (EUR/USD/GBP)
     * @param float $inputAmount Original amount input by user
     * @param string $inputCurrency Original currency input by user
     * @param array $metadata Optional metadata
     * @return Reservation
     * @throws Exception
     */
    public function createPreLaunchReservation(
        Egi $egi,
        User $user,
        float $amountEur,
        string $displayCurrency = 'EUR',
        float $inputAmount = null,
        string $inputCurrency = null,
        array $metadata = []
    ): Reservation | RedirectResponse {
        try {
            $this->logger->info('[RESERVATION] Creating pre-launch reservation', [
                'egi_id' => $egi->id,
                'user_id' => $user->id,
                'amount_eur' => $amountEur,
                'display_currency' => $displayCurrency,
            ]);

            // Validate amount
            if ($amountEur <= 0) {
                return $this->errorManager->handle('RESERVATION_INVALID_AMOUNT', [
                    'amount_eur' => $amountEur
                ]);
            }

            // Check if EGI is available for reservations
            if (!$this->isEgiAvailable($egi)) {
                return $this->errorManager->handle('RESERVATION_EGI_NOT_AVAILABLE', [
                    'egi_id' => $egi->id
                ]);
            }

            // Calculate display amount if different from EUR
            $displayAmount = $amountEur;
            $displayRate = null;

            if ($displayCurrency !== 'EUR') {
                $rate = $this->currencyService->getExchangeRate($displayCurrency);
                if ($rate) {
                    $displayAmount = round($amountEur * $rate['rate'], 2);
                    $displayRate = $rate['rate'];
                }
            }

            // Create reservation
            $reservation = DB::transaction(function () use (
                $egi,
                $user,
                $amountEur,
                $displayCurrency,
                $displayAmount,
                $displayRate,
                $inputAmount,
                $inputCurrency,
                $metadata
            ) {
                // Check for existing active reservation by same user for same EGI
                $existing = Reservation::active()
                    ->forEgi($egi->id)
                    ->forUser($user->id)
                    ->first();

                if ($existing) {
                    // Update existing reservation instead of creating new
                    return $this->updateReservationAmount($existing, $amountEur, $displayCurrency);
                }

                // Create new reservation
                $reservation = Reservation::create([
                    'user_id' => $user->id,
                    'egi_id' => $egi->id,
                    'type' => Reservation::TYPE_WEAK,
                    'status' => Reservation::STATUS_ACTIVE,
                    'sub_status' => Reservation::SUB_STATUS_PENDING,
                    'amount_eur' => $amountEur,
                    'display_currency' => $displayCurrency,
                    'display_amount' => $displayAmount,
                    'display_exchange_rate' => $displayRate,
                    'input_currency' => $inputCurrency ?? $displayCurrency,
                    'input_amount' => $inputAmount ?? $amountEur,
                    'input_exchange_rate' => ($inputCurrency && $inputCurrency !== 'EUR') ? $displayRate : null,
                    'input_timestamp' => now(),
                    'is_current' => true,
                    'metadata' => $metadata,
                    // Legacy compatibility
                    'original_currency' => $displayCurrency,
                    'original_price' => $displayAmount,
                    'fiat_currency' => $displayCurrency,
                    'offer_amount_fiat' => $displayAmount,
                ]);

                // Update rankings for all reservations of this EGI
                $this->updateEgiRankings($egi->id);

                // Refresh reservation to get updated ranking data
                $reservation->refresh();

                // Check if this is now the highest offer
                $this->checkAndNotifyIfHighest($reservation);

                return $reservation;
            });

            $this->logger->info('[RESERVATION] Pre-launch reservation created successfully', [
                'reservation_id' => $reservation->id,
                'rank_position' => $reservation->rank_position,
                'is_highest' => $reservation->is_highest,
            ]);

            return $reservation;
        } catch (Exception $e) {
            $this->logger->error('[RESERVATION] Failed to create pre-launch reservation', [
                'egi_id' => $egi->id,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return $this->errorManager->handle('RESERVATION_CREATION_FAILED', [
                'egi_id' => $egi->id,
                'user_id' => $user->id,
                'amount_eur' => $amountEur,
                'error' => $e->getMessage(),
            ], $e);
        }
    }

    /**
     * Update reservation amount (rilancio)
     *
     * @param Reservation $reservation
     * @param float $newAmountEur
     * @param string|null $displayCurrency
     * @return Reservation
     * @throws Exception
     */
    public function updateReservationAmount(
        Reservation $reservation,
        float $newAmountEur,
        ?string $displayCurrency = null
    ): Reservation | RedirectResponse {
        try {
            $this->logger->info('[RESERVATION] Updating reservation amount', [
                'reservation_id' => $reservation->id,
                'old_amount' => $reservation->amount_eur,
                'new_amount' => $newAmountEur,
            ]);

            // Validate new amount
            if ($newAmountEur <= 0) {
                return $this->errorManager->handle('RESERVATION_INVALID_AMOUNT', [
                    'amount' => $newAmountEur
                ]);
            }

            DB::transaction(function () use ($reservation, $newAmountEur, $displayCurrency) {
                $oldAmount = $reservation->amount_eur;
                $oldRank = $reservation->rank_position;

                // Update amount
                $reservation->amount_eur = $newAmountEur;

                // Update display amount if currency provided
                if ($displayCurrency) {
                    $reservation->display_currency = $displayCurrency;

                    if ($displayCurrency !== 'EUR') {
                        $rate = $this->currencyService->getExchangeRate($displayCurrency);
                        if ($rate) {
                            $reservation->display_amount = round($newAmountEur * $rate['rate'], 2);
                            $reservation->display_exchange_rate = $rate['rate'];
                        }
                    } else {
                        $reservation->display_amount = $newAmountEur;
                        $reservation->display_exchange_rate = null;
                    }
                }

                // Update legacy fields
                $reservation->offer_amount_fiat = $reservation->display_amount;
                $reservation->original_price = $reservation->display_amount;

                $reservation->save();

                // Update rankings
                $this->updateEgiRankings($reservation->egi_id);

                // Reload to get new rank
                $reservation->refresh();

                // Notify if position changed significantly
                if ($oldRank && $reservation->rank_position) {
                    if ($reservation->rank_position < $oldRank) {
                        // Position improved - use sendRankChanged with appropriate threshold
                        $this->notificationService->sendRankChanged($reservation, $oldRank, 1); // Any improvement triggers notification for updates
                    }
                }

                // Check if now highest
                $this->checkAndNotifyIfHighest($reservation);
            });

            $this->logger->info('[RESERVATION] Reservation updated successfully', [
                'reservation_id' => $reservation->id,
                'new_rank' => $reservation->rank_position,
                'is_highest' => $reservation->is_highest,
            ]);

            return $reservation;
        } catch (Exception $e) {
            $this->logger->error('[RESERVATION] Failed to update reservation', [
                'reservation_id' => $reservation->id,
                'error' => $e->getMessage(),
            ]);

            return $this->errorManager->handle('RESERVATION_UPDATE_FAILED', [
                'reservation_id' => $reservation->id,
                'new_amount_eur' => $newAmountEur,
                'error' => $e->getMessage(),
            ], $e);
        }
    }

    /**
     * Check if an EGI can be reserved
     *
     * @param Egi $egi The EGI to check
     * @return bool Whether the EGI can be reserved
     */
    public function canReserveEgi(Egi $egi): bool {
        // Check if EGI is published or if it's already minted
        return ($egi->is_published || $egi->status === 'published') && !$egi->mint;
    }

    /**
     * Check if EGI is available (alias for new system)
     */
    protected function isEgiAvailable(Egi $egi): bool {
        return $this->canReserveEgi($egi);
    }

    /**
     * Process reservation priorities to maintain consistency (LEGACY)
     *
     * @param Reservation $newReservation The new reservation to process
     * @return void
     */
    public function processReservationPriorities(Reservation $newReservation): void {
        // Get all active reservations for the same EGI, excluding same user reservations
        $existingReservations = Reservation::where('egi_id', $newReservation->egi_id)
            ->where('id', '!=', $newReservation->id)
            ->where('is_current', true)
            ->where('status', 'active')
            ->where('user_id', '!=', $newReservation->user_id)
            ->get();

        foreach ($existingReservations as $existingReservation) {
            // If new reservation has higher priority, mark existing as superseded
            if ($this->hasHigherPriority($newReservation, $existingReservation)) {
                // Load relationships BEFORE marking as superseded
                $existingReservation->load(['user', 'egi']);
                $newReservation->load(['user', 'egi']);

                // Send superseded notification BEFORE marking as superseded
                $this->notificationService->sendSuperseded($existingReservation, $newReservation);

                $existingReservation->markAsSuperseded($newReservation);

                $this->logger->info('[RESERVATION] Reservation superseded', [
                    'new_reservation_id' => $newReservation->id,
                    'superseded_reservation_id' => $existingReservation->id
                ]);
            }
            // If existing has higher priority, mark the new one as superseded
            elseif ($this->hasHigherPriority($existingReservation, $newReservation)) {
                $newReservation->markAsSuperseded($existingReservation);

                $this->logger->info('[RESERVATION] New reservation superseded by existing', [
                    'new_reservation_id' => $newReservation->id,
                    'higher_priority_reservation_id' => $existingReservation->id
                ]);

                break;
            }
            // If equal priority, first come first served (existing wins)
            elseif ($existingReservation->created_at->lt($newReservation->created_at)) {
                $newReservation->markAsSuperseded($existingReservation);

                $this->logger->info('[RESERVATION] New reservation superseded by older with equal priority', [
                    'new_reservation_id' => $newReservation->id,
                    'older_reservation_id' => $existingReservation->id
                ]);

                break;
            }
        }
    }

    /**
     * Determine if one reservation has higher priority than another (LEGACY)
     *
     * @param Reservation $a First reservation
     * @param Reservation $b Second reservation
     * @return bool Whether a has higher priority than b
     */
    public function hasHigherPriority(Reservation $a, Reservation $b): bool {
        // Strong reservations always have priority over weak ones
        if ($a->type === Reservation::TYPE_STRONG && $b->type === Reservation::TYPE_WEAK) {
            return true;
        }

        if ($a->type === Reservation::TYPE_WEAK && $b->type === Reservation::TYPE_STRONG) {
            return false;
        }

        // If same type, higher offer amount wins
        if ($a->offer_amount_fiat > $b->offer_amount_fiat) {
            return true;
        }

        if ($a->offer_amount_fiat < $b->offer_amount_fiat) {
            return false;
        }

        // If same amount, older reservation wins (handled outside this method)
        return false;
    }

    /**
     * Get the highest priority reservation for an EGI (LEGACY - IMPORTANTE!)
     *
     * @param Egi $egi The EGI to check
     * @return Reservation|null The highest priority reservation or null if none
     */
    public function getHighestPriorityReservation(Egi $egi): ?Reservation {
        // First look for strong reservations
        $strongReservation = $egi->reservations()
            ->where('type', 'strong')
            ->where('is_current', true)
            ->orderBy('offer_amount_fiat', 'desc')
            ->orderBy('created_at', 'asc')
            ->first();

        if ($strongReservation) {
            return $strongReservation;
        }

        // If no strong reservations, look for weak ones
        return $egi->reservations()
            ->where('type', 'weak')
            ->where('is_current', true)
            ->orderBy('offer_amount_fiat', 'desc')
            ->orderBy('created_at', 'asc')
            ->first();
    }

    /**
     * Get all active reservations for a user
     *
     * @param User $user The user
     * @return Collection Collection of active reservations
     */
    public function getUserActiveReservations(User $user): Collection {
        return Reservation::where('user_id', $user->id)
            ->where('is_current', true)
            ->where('status', 'active')
            ->with(['egi', 'certificate'])
            ->get();
    }

    /**
     * Get user's reservations (alias for new system)
     */
    public function getUserReservations(int $userId): Collection {
        return Reservation::active()
            ->forUser($userId)
            ->with('egi')
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Get reservation history for an EGI
     *
     * @param Egi $egi The EGI
     * @return Collection Collection of all reservations for the EGI
     */
    public function getReservationHistory(Egi $egi): Collection {
        return Reservation::where('egi_id', $egi->id)
            ->with(['user', 'certificate'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get ranking for a specific EGI (NEW)
     *
     * @param int $egiId
     * @return Collection
     */
    public function getEgiRanking(int $egiId): Collection {
        return Reservation::active()
            ->forEgi($egiId)
            ->ranked()
            ->with('user')
            ->get()
            ->map(function ($reservation) {
                return $reservation->getDisplayData();
            });
    }

    /**
     * Get statistics for an EGI (NEW)
     *
     * @param int $egiId
     * @return array
     */
    public function getEgiStatistics(int $egiId): array {
        $reservations = Reservation::active()->forEgi($egiId)->get();

        return [
            'total_reservations' => $reservations->count(),
            'highest_offer' => $reservations->max('amount_eur'),
            'average_offer' => round($reservations->avg('amount_eur'), 2),
            'total_value' => $reservations->sum('amount_eur'),
            'unique_users' => $reservations->pluck('user_id')->unique()->count(),
        ];
    }

    /**
     * Cancel a reservation
     *
     * @param Reservation $reservation The reservation to cancel
     * @return bool Whether the cancellation was successful
     */
    public function cancelReservation(Reservation $reservation): bool {
        // Update the reservation status
        $reservation->status = Reservation::STATUS_CANCELLED;
        $reservation->is_current = false;
        $result = $reservation->save();

        if ($result) {
            $this->logger->info('[RESERVATION] Reservation cancelled', [
                'reservation_id' => $reservation->id,
                'egi_id' => $reservation->egi_id,
                'user_id' => $reservation->user_id
            ]);

            // If this reservation superseded others, we need to reprocess priorities
            $supersededReservations = Reservation::where('superseded_by_id', $reservation->id)
                ->get();

            if ($supersededReservations->isNotEmpty()) {
                // Reactivate superseded reservations
                foreach ($supersededReservations as $supersededReservation) {
                    $supersededReservation->is_current = true;
                    $supersededReservation->superseded_by_id = null;
                    $supersededReservation->save();

                    // Reactivate certificate if it exists
                    if ($supersededReservation->certificate) {
                        $supersededReservation->certificate->is_superseded = false;
                        $supersededReservation->certificate->is_current_highest = true;
                        $supersededReservation->certificate->save();
                    }
                }

                // Reprocess priorities among the reactivated reservations
                $this->reprocessPrioritiesAfterCancellation($reservation->egi_id);
            }

            // Update rankings for new system
            $this->updateEgiRankings($reservation->egi_id);
        }

        return $result;
    }

    /**
     * Withdraw a reservation (NEW)
     *
     * @param Reservation $reservation
     * @param string|null $reason
     * @return bool
     */
    public function withdrawReservation(Reservation $reservation, ?string $reason = null): bool {
        try {
            $this->logger->info('[RESERVATION] Withdrawing reservation', [
                'reservation_id' => $reservation->id,
                'reason' => $reason,
            ]);

            DB::transaction(function () use ($reservation, $reason) {
                // Store metadata about withdrawal
                $metadata = $reservation->metadata ?? [];
                $metadata['withdrawal'] = [
                    'timestamp' => now()->toIso8601String(),
                    'reason' => $reason,
                    'previous_rank' => $reservation->rank_position,
                    'previous_amount' => $reservation->amount_eur,
                ];
                $reservation->metadata = $metadata;

                // Withdraw the reservation
                $reservation->withdraw();

                // Update rankings for remaining reservations
                $this->updateEgiRankings($reservation->egi_id);

                // Notify users who moved up in ranking
                $this->notifyRankingChangesAfterWithdrawal($reservation->egi_id);
            });

            $this->logger->info('[RESERVATION] Reservation withdrawn successfully', [
                'reservation_id' => $reservation->id,
            ]);

            return true;
        } catch (Exception $e) {
            $this->logger->error('[RESERVATION] Failed to withdraw reservation', [
                'reservation_id' => $reservation->id,
                'error' => $e->getMessage(),
            ]);

            $this->errorManager->handle('RESERVATION_WITHDRAWAL_FAILED', [
                'reservation_id' => $reservation->id,
                'error' => $e->getMessage(),
            ], $e);

            return false;
        }
    }

    /**
     * Check if user can reserve an EGI (NEW)
     *
     * @param User $user
     * @param Egi $egi
     * @return bool
     */
    public function canUserReserve(User $user, Egi $egi): bool {
        // Creator cannot reserve their own EGI
        if ($egi->user_id === $user->id) {
            return false;
        }

        // User can always update their existing reservation
        return true;
    }

    /**
     * Reprocess priorities after a reservation cancellation (LEGACY)
     *
     * @param int $egiId The EGI ID
     * @return void
     */
    private function reprocessPrioritiesAfterCancellation(int $egiId): void {
        $activeReservations = Reservation::where('egi_id', $egiId)
            ->where('is_current', true)
            ->where('status', 'active')
            ->orderBy('type', 'desc') // 'strong' comes before 'weak'
            ->orderBy('offer_amount_fiat', 'desc')
            ->orderBy('created_at', 'asc')
            ->get();

        if ($activeReservations->isEmpty()) {
            return;
        }

        // The first reservation in the sorted list has the highest priority
        $highestPriority = $activeReservations->shift();

        // Mark all other reservations as superseded by the highest priority one
        foreach ($activeReservations as $reservation) {
            $reservation->markAsSuperseded($highestPriority);
        }

        $this->logger->info('[RESERVATION] Priorities reprocessed after cancellation', [
            'egi_id' => $egiId,
            'highest_priority_reservation_id' => $highestPriority->id
        ]);
    }

    /**
     * Deactivate user's previous reservations for an EGI
     *
     * @param int $userId
     * @param int $egiId
     * @return void
     */
    private function deactivateUserPreviousReservations(int $userId, int $egiId): void {
        $previousReservations = Reservation::where('user_id', $userId)
            ->where('egi_id', $egiId)
            ->where('is_current', true)
            ->get();

        foreach ($previousReservations as $prevReservation) {
            $prevReservation->is_current = false;
            $prevReservation->sub_status = Reservation::SUB_STATUS_SUPERSEDED;
            $prevReservation->superseded_at = now();
            $prevReservation->save();

            // Update certificate if present
            if ($prevReservation->certificate) {
                $prevReservation->certificate->is_current_highest = false;
                $prevReservation->certificate->is_superseded = true;
                $prevReservation->certificate->save();
            }

            $this->logger->info('[RESERVATION] Previous user reservation deactivated', [
                'deactivated_reservation_id' => $prevReservation->id,
                'user_id' => $userId,
                'egi_id' => $egiId
            ]);
        }
    }

    /**
     * Supersede user's previous reservations for an EGI with proper superseded_by_id
     *
     * @param int $userId
     * @param int $egiId
     * @param int $newReservationId
     * @return void
     */
    private function supersedeUserPreviousReservations(int $userId, int $egiId, int $newReservationId): void {
        $previousReservations = Reservation::where('user_id', $userId)
            ->where('egi_id', $egiId)
            ->where('is_current', true)
            ->where('id', '!=', $newReservationId) // Exclude the new reservation
            ->get();

        foreach ($previousReservations as $prevReservation) {
            $prevReservation->is_current = false;
            $prevReservation->sub_status = Reservation::SUB_STATUS_SUPERSEDED;
            $prevReservation->superseded_by_id = $newReservationId;
            $prevReservation->superseded_at = now();
            $prevReservation->save();

            // Update certificate if present
            if ($prevReservation->certificate) {
                $prevReservation->certificate->is_current_highest = false;
                $prevReservation->certificate->is_superseded = true;
                $prevReservation->certificate->save();
            }

            $this->logger->info('[RESERVATION] Previous user reservation superseded', [
                'superseded_reservation_id' => $prevReservation->id,
                'superseded_by_id' => $newReservationId,
                'user_id' => $userId,
                'egi_id' => $egiId
            ]);
        }
    }

    /**
     * Update rankings for all reservations of an EGI (NEW)
     *
     * @param int $egiId
     * @return void
     */
    protected function updateEgiRankings(int $egiId): void {
        // Get all active reservations for this EGI
        $reservations = Reservation::active()
            ->forEgi($egiId)
            ->orderByDesc('amount_eur')
            ->orderBy('created_at') // Earlier reservation wins ties
            ->get();

        $previousHighest = $reservations->where('is_highest', true)->first();

        foreach ($reservations as $index => $reservation) {
            $newRank = $index + 1;
            $isHighest = ($newRank === 1);

            // Store previous rank
            if ($reservation->rank_position !== $newRank) {
                $reservation->previous_rank = $reservation->rank_position;
            }

            // Update rank and status
            $reservation->rank_position = $newRank;
            $reservation->is_highest = $isHighest;

            if ($isHighest) {
                $reservation->sub_status = Reservation::SUB_STATUS_HIGHEST;

                // Handle supersession only if not already handled
                if ($previousHighest && $previousHighest->id !== $reservation->id && $previousHighest->is_current) {
                    $previousHighest->sub_status = Reservation::SUB_STATUS_SUPERSEDED;
                    $previousHighest->superseded_by_id = $reservation->id;
                    $previousHighest->superseded_at = now();
                    $previousHighest->save();

                    // Notify the superseded user (only if still current to avoid duplicates)
                    $this->notificationService->sendSuperseded($previousHighest, $reservation);
                }
            } elseif ($reservation->sub_status === Reservation::SUB_STATUS_HIGHEST) {
                $reservation->sub_status = Reservation::SUB_STATUS_SUPERSEDED;
            }

            $reservation->save();
        }
    }

    /**
     * Check and notify if reservation is now highest
     *
     * @param Reservation $reservation
     * @return void
     */
    protected function checkAndNotifyIfHighest(Reservation $reservation): void {
        // For new reservations that became highest (previous_rank is null/0)
        // OR for existing reservations that improved to highest (previous_rank !== 1)
        if ($reservation->is_highest && ($reservation->previous_rank === null || $reservation->previous_rank === 0 || $reservation->previous_rank !== 1)) {
            $this->logger->info('[RESERVATION_NOTIFICATION] Sending new highest notification', [
                'reservation_id' => $reservation->id,
                'user_id' => $reservation->user_id,
                'previous_rank' => $reservation->previous_rank,
                'current_rank' => $reservation->rank_position,
                'is_highest' => $reservation->is_highest
            ]);

            $this->notificationService->sendNewHighest($reservation);
        } else {
            $this->logger->debug('[RESERVATION_NOTIFICATION] Notification not sent - conditions not met', [
                'reservation_id' => $reservation->id,
                'is_highest' => $reservation->is_highest,
                'previous_rank' => $reservation->previous_rank,
                'current_rank' => $reservation->rank_position
            ]);
        }
    }

    /**
     * Notify ranking changes after a withdrawal
     *
     * @param int $egiId
     * @return void
     */
    protected function notifyRankingChangesAfterWithdrawal(int $egiId): void {
        $reservations = Reservation::active()
            ->forEgi($egiId)
            ->where('previous_rank', '>', 0)
            ->whereColumn('rank_position', '<', 'previous_rank')
            ->get();

        foreach ($reservations as $reservation) {
            // Use sendRankChanged for withdrawal improvements
            $this->notificationService->sendRankChanged(
                $reservation,
                $reservation->previous_rank,
                1 // Any improvement after withdrawal triggers notification
            );
        }
    }

    /**
     * Convert amount to EUR
     *
     * @param float $amount
     * @param string $currency
     * @return float
     */
    protected function convertToEur(float $amount, string $currency): float {
        if ($currency === 'EUR') {
            return $amount;
        }

        $rate = $this->currencyService->getExchangeRate($currency);
        if ($rate) {
            // Rate is an array: ['rate' => float, 'timestamp' => Carbon]
            return round($amount / $rate['rate'], 2);
        }

        // Fallback to approximate rates if service unavailable
        $fallbackRates = [
            'USD' => 0.92,
            'GBP' => 1.17,
        ];

        return round($amount * ($fallbackRates[$currency] ?? 1.0), 2);
    }

    /**
     * EXTENSION METHODS FOR ReservationService
     *
     * ADD these methods to the existing app/Services/ReservationService.php
     * DO NOT replace the existing file, just add these methods to it
     *
     * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
     * @version 1.0.0 (FlorenceEGI - Pre-Launch Extension)
     * @date 2025-08-15
     */

    // ============================================================================
    // ADD THESE METHODS TO YOUR EXISTING ReservationService CLASS
    // ============================================================================

    /**
     * Create or update a pre-launch reservation (PUBLIC RANKING SYSTEM)
     * This is for the new pre-launch system with public rankings
     *
     * @param int $egiId The EGI ID
     * @param int $userId The user ID
     * @param float $amountEur The amount in EUR
     * @return Reservation The created/updated reservation
     */
    public function createOrUpdatePreLaunchReservation(int $egiId, int $userId, float $amountEur): Reservation {
        $this->logger->info('[PRE_LAUNCH_RESERVATION] Creating/updating reservation', [
            'egi_id' => $egiId,
            'user_id' => $userId,
            'amount_eur' => $amountEur
        ]);

        return DB::transaction(function () use ($egiId, $userId, $amountEur) {
            $egi = Egi::lockForUpdate()->findOrFail($egiId);

            // Check if EGI is available using existing method
            if (!$this->canReserveEgi($egi)) {
                throw new \Exception('EGI is not available for reservations');
            }

            // Check if user is the creator
            if ($egi->user_id === $userId) {
                throw new \Exception('Cannot reserve your own EGI');
            }

            // Look for existing reservation
            $reservation = Reservation::where('egi_id', $egiId)
                ->where('user_id', $userId)
                ->where('status', 'active')
                ->where('is_current', true)
                ->first();

            if ($reservation) {
                // Update existing
                $oldRank = $reservation->rank_position;

                $reservation->update([
                    'amount_eur' => $amountEur,
                    'offer_amount_fiat' => $amountEur, // Legacy field
                ]);

                // Update rankings
                $this->updatePreLaunchRankings($egiId);
                $reservation->refresh();

                // Handle notifications for rank change
                if ($oldRank !== $reservation->rank_position) {
                    $this->handleRankChangeNotifications($reservation, $oldRank);
                }
            } else {
                // Create new reservation
                $reservation = Reservation::create([
                    'egi_id' => $egiId,
                    'user_id' => $userId,
                    'amount_eur' => $amountEur,
                    'status' => 'active',
                    'is_current' => true,
                    'type' => 'weak', // Default for pre-launch

                    // Legacy fields
                    'fiat_currency' => 'EUR',
                    'offer_amount_fiat' => $amountEur,
                    'offer_amount_algo' => 0,
                    'exchange_rate' => 0,
                    'exchange_timestamp' => now()
                ]);

                // Update rankings
                $this->updateEgiRankings($egiId);
                $reservation->refresh();

                // Send notifications if became highest
                if ($reservation->is_highest) {
                    $this->notificationService->sendNewHighest($reservation);
                    $this->notifyPreviousHighest($egiId, $reservation);
                }
            }

            $this->logger->info('[PRE_LAUNCH_RESERVATION] Reservation processed', [
                'reservation_id' => $reservation->id,
                'rank_position' => $reservation->rank_position,
                'is_highest' => $reservation->is_highest
            ]);

            // 🎯 FASE 3: CREATE PAYMENT DISTRIBUTIONS for every reservation
            try {
                $distributions = $this->paymentDistributionService->createDistributionsForReservation($reservation);

                $this->logger->info('[PAYMENT_DISTRIBUTIONS] Created distributions for reservation', [
                    'reservation_id' => $reservation->id,
                    'distributions_count' => count($distributions),
                    'total_amount' => array_sum(array_column($distributions, 'amount_eur')),
                    'is_highest_rank' => $reservation->rank_position === 1
                ]);
            } catch (\Exception $e) {
                // Log error but don't fail the reservation process
                $this->logger->error('[PAYMENT_DISTRIBUTIONS] Failed to create distributions', [
                    'reservation_id' => $reservation->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);

                // Using ErrorManager to handle non-blocking error
                $this->errorManager->handle('PAYMENT_DISTRIBUTION_ERROR', [
                    'reservation_id' => $reservation->id,
                    'operation' => 'createOrUpdatePreLaunchReservation',
                    'error' => $e->getMessage(),
                    'user_id' => $userId,
                    'timestamp' => now()->toIso8601String()
                ], $e);
            }

            return $reservation;
        });
    }

    /**
     * Update pre-launch rankings for all active reservations of an EGI
     * This is specifically for the new ranking system with public positions
     *
     * @param int $egiId The EGI ID
     * @return void
     */
    public function updatePreLaunchRankings(int $egiId): void {
        // FIXED: Proper Strong/Weak priority ranking
        // Strong users ALWAYS rank before Weak users regardless of amount

        // Get Strong reservations first (priority high)
        $strongReservations = Reservation::where('egi_id', $egiId)
            ->where('status', 'active')
            ->where('is_current', true)
            ->where('type', 'strong')
            ->orderBy('amount_eur', 'desc')
            ->orderBy('created_at', 'asc') // Tie-breaker
            ->get();

        // Get Weak reservations second (priority low)
        $weakReservations = Reservation::where('egi_id', $egiId)
            ->where('status', 'active')
            ->where('is_current', true)
            ->where('type', 'weak')
            ->orderBy('amount_eur', 'desc')
            ->orderBy('created_at', 'asc') // Tie-breaker
            ->get();

        // Combine: Strong first, then Weak
        $allReservations = $strongReservations->concat($weakReservations);

        $rank = 1;
        foreach ($allReservations as $reservation) {
            $reservation->update([
                'rank_position' => $rank,
                'is_highest' => ($rank === 1)
            ]);
            $rank++;
        }

        $this->logger->debug('[PRE_LAUNCH_RESERVATION] Rankings updated with Strong/Weak priority', [
            'egi_id' => $egiId,
            'total_reservations' => $allReservations->count(),
            'strong_reservations' => $strongReservations->count(),
            'weak_reservations' => $weakReservations->count()
        ]);
    }

    /**
     * Get EGI reservations with ranking
     *
     * @param int $egiId The EGI ID
     * @param bool $activeOnly Whether to get only active reservations
     * @return Collection
     */
    public function getEgiReservationsWithRanking(int $egiId, bool $activeOnly = true): Collection {
        $query = Reservation::where('egi_id', $egiId)
            ->with(['user:id,name,email']);

        if ($activeOnly) {
            $query->where('status', 'active')
                ->where('is_current', true);
        }

        return $query->orderBy('rank_position', 'asc')
            ->orderBy('amount_eur', 'desc')
            ->get();
    }

    /**
     * Withdraw a pre-launch reservation
     *
     * @param int $reservationId The reservation ID
     * @param int $userId The user ID for authorization
     * @return bool Success status
     */
    public function withdrawPreLaunchReservation(int $reservationId, int $userId): bool {
        $this->logger->info('[PRE_LAUNCH_RESERVATION] Withdrawing reservation', [
            'reservation_id' => $reservationId,
            'user_id' => $userId
        ]);

        return DB::transaction(function () use ($reservationId, $userId) {
            $reservation = Reservation::lockForUpdate()->findOrFail($reservationId);

            // Verify ownership
            if ($reservation->user_id !== $userId) {
                throw new \Exception('Unauthorized: You can only withdraw your own reservations');
            }

            if ($reservation->status !== 'active') {
                throw new \Exception('Reservation is not active');
            }

            $egiId = $reservation->egi_id;
            $wasHighest = $reservation->is_highest;

            // Mark as withdrawn
            $reservation->update([
                'status' => 'withdrawn',
                'is_current' => false,
                'withdrawn_at' => now()
            ]);

            // Get affected reservations before updating rankings
            $affectedReservations = Reservation::where('egi_id', $egiId)
                ->where('status', 'active')
                ->where('is_current', true)
                ->where('rank_position', '>', $reservation->rank_position)
                ->get();

            // Update rankings
            $this->updatePreLaunchRankings($egiId);

            // Send notifications
            if ($affectedReservations->isNotEmpty()) {
                $affectedReservations->each->refresh();
                $this->notificationService->sendCompetitorWithdrew($affectedReservations, $reservation);
            }

            // If was highest, notify new highest
            if ($wasHighest) {
                $newHighest = Reservation::where('egi_id', $egiId)
                    ->where('status', 'active')
                    ->where('is_current', true)
                    ->where('is_highest', true)
                    ->first();

                if ($newHighest) {
                    $this->notificationService->sendNewHighest($newHighest);
                }
            }

            $this->logger->info('[PRE_LAUNCH_RESERVATION] Reservation withdrawn', [
                'reservation_id' => $reservationId,
                'was_highest' => $wasHighest
            ]);

            return true;
        });
    }

    /**
     * Get ranking statistics for an EGI
     *
     * @param int $egiId The EGI ID
     * @return array Statistics
     */
    public function getEgiRankingStats(int $egiId): array {
        $reservations = $this->getEgiReservationsWithRanking($egiId, true);

        if ($reservations->isEmpty()) {
            return [
                'total_reservations' => 0,
                'highest_amount' => 0,
                'lowest_amount' => 0,
                'average_amount' => 0,
                'median_amount' => 0
            ];
        }

        $amounts = $reservations->pluck('amount_eur')->sort()->values();

        return [
            'total_reservations' => $reservations->count(),
            'highest_amount' => $amounts->max(),
            'lowest_amount' => $amounts->min(),
            'average_amount' => round($amounts->avg(), 2),
            'median_amount' => $amounts->median()
        ];
    }

    /**
     * Check if user can make a pre-launch reservation
     *
     * @param int $egiId The EGI ID
     * @param int $userId The user ID
     * @return array Status and message
     */
    public function canUserMakePreLaunchReservation(int $egiId, int $userId): array {
        $egi = Egi::find($egiId);

        if (!$egi) {
            return [
                'can_reserve' => false,
                'reason' => 'egi_not_found',
                'message' => 'EGI not found'
            ];
        }

        // Check if user is the creator
        if ($egi->user_id === $userId) {
            return [
                'can_reserve' => false,
                'reason' => 'own_egi',
                'message' => 'Cannot reserve your own EGI'
            ];
        }

        // Use existing method to check availability
        if (!$this->canReserveEgi($egi)) {
            return [
                'can_reserve' => false,
                'reason' => 'egi_unavailable',
                'message' => 'EGI is not available for reservations'
            ];
        }

        // Check for existing active reservation
        $existingReservation = Reservation::where('egi_id', $egiId)
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->where('is_current', true)
            ->first();

        return [
            'can_reserve' => true,
            'has_existing' => $existingReservation !== null,
            'existing_reservation' => $existingReservation,
            'message' => $existingReservation
                ? 'You can update your existing reservation'
                : 'You can place a reservation'
        ];
    }

    /**
     * Handle notifications for rank changes (PRIVATE HELPER)
     *
     * @param Reservation $reservation
     * @param int|null $oldRank
     * @return void
     */
    private function handleRankChangeNotifications(Reservation $reservation, ?int $oldRank): void {
        $newRank = $reservation->rank_position;

        if ($oldRank === $newRank) {
            return;
        }

        // Became highest
        if ($newRank === 1 && $oldRank !== 1) {
            $this->notificationService->sendNewHighest($reservation);
            $this->notifyPreviousHighest($reservation->egi_id, $reservation);
        }
        // Was highest, now superseded
        elseif ($oldRank === 1 && $newRank !== 1) {
            $newHighest = Reservation::where('egi_id', $reservation->egi_id)
                ->where('is_highest', true)
                ->first();

            if ($newHighest) {
                $this->notificationService->sendSuperseded($reservation, $newHighest);
            }
        }
        // Significant rank change (3+ positions)
        elseif ($oldRank && abs($newRank - $oldRank) >= 3) {
            $this->notificationService->sendRankChanged($reservation, $oldRank);
        }
    }

    /**
     * Notify the previous highest reservation (PRIVATE HELPER)
     *
     * @param int $egiId
     * @param Reservation $newHighest
     * @return void
     */
    private function notifyPreviousHighest(int $egiId, Reservation $newHighest): void {
        $previousHighest = Reservation::where('egi_id', $egiId)
            ->where('id', '!=', $newHighest->id)
            ->where('rank_position', 2)
            ->first();

        if ($previousHighest) {
            $this->notificationService->sendSuperseded($previousHighest, $newHighest);
            $previousHighest->update(['superseded_by_id' => $newHighest->id]);
        }
    }

    // ============================================================================
    // END OF EXTENSION METHODS
    // ============================================================================
}
