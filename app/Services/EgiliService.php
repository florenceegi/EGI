<?php

namespace App\Services;

use App\Models\User;
use App\Models\Wallet;
use App\Models\EgiliTransaction;
use App\Enums\Gdpr\GdprActivityCategory;
use App\Services\Gdpr\AuditLogService;
use Illuminate\Support\Facades\DB;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * @Oracode Service: Egili Token Management
 * 🎯 Purpose: Manage Egili token (platform utility) operations
 * 🧱 Core Logic: Earn, spend, balance tracking with atomic transactions
 * 🛡️ GDPR Compliance: Full audit trail for all operations
 *
 * Egili are platform utility tokens (NOT cryptocurrency):
 * - Account-bound (non-transferable between users)
 * - MiCA-safe (no crypto custody)
 * - Used for fee reduction, services, subscriptions
 * - Conversion rate: 1 Egilo = €0.01 (purchase price)
 *
 * NEW IN v1.1.0: Gift/Lifetime Egili Support
 * - Lifetime Egili: Purchased by user, never expire
 * - Gift Egili: Granted by platform/admin, expire after N days
 * - Priority Logic: Spend Gift first (expiring first), then Lifetime (FIFO)
 *
 * @package App\Services
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.1.0 (FlorenceEGI - Egili System Foundation)
 * @date 2025-11-02
 * @purpose Egili token operations with GDPR compliance and Gift/Lifetime support
 */
class EgiliService {
    private UltraLogManager $logger;
    private ErrorManagerInterface $errorManager;
    private AuditLogService $auditService;

    /**
     * Conversion rate Egili → EUR (purchase price)
     * NOT a real exchange rate (Egili are NOT currency)
     */
    const EGILI_TO_EUR_RATE = 0.01;

    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        AuditLogService $auditService
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->auditService = $auditService;
    }

    /**
     * Get user's Egili balance
     *
     * @param User $user
     * @return int Current Egili balance
     */
    public function getBalance(User $user): int {
        if (!$user->wallet) {
            $this->logger->warning('User has no wallet - Egili balance is zero', [
                'user_id' => $user->id,
                'log_category' => 'EGILI_NO_WALLET'
            ]);
            return 0;
        }

        return $user->wallet->egili_balance ?? 0;
    }

    /**
     * Check if user can spend specified amount
     *
     * @param User $user
     * @param int $amount Amount to spend
     * @return bool True if sufficient balance
     */
    public function canSpend(User $user, int $amount): bool {
        return $this->getBalance($user) >= $amount;
    }

    /**
     * Calculate EUR equivalent of Egili amount
     *
     * @param int $egiliAmount
     * @return float EUR equivalent (perceived value)
     */
    public function toEur(int $egiliAmount): float {
        return round($egiliAmount * self::EGILI_TO_EUR_RATE, 2);
    }

    /**
     * Calculate Egili equivalent of EUR amount
     *
     * @param float $eurAmount
     * @return int Egili equivalent (rounded)
     */
    public function fromEur(float $eurAmount): int {
        return (int) round($eurAmount / self::EGILI_TO_EUR_RATE);
    }

    /**
     * Earn Egili (add to balance) - GDPR Compliant
     *
     * Creates atomic transaction:
     * 1. Update wallet balance
     * 2. Create transaction record
     * 3. Log GDPR activity
     * 4. Log ULM event
     *
     * @param User $user User earning Egili
     * @param int $amount Amount to earn (positive integer)
     * @param string $reason Machine-readable reason (e.g., 'egi_sale_cashback')
     * @param string|null $category Reporting category (trading, milestone, service, etc.)
     * @param array|null $metadata Additional context data
     * @param mixed|null $source Source entity (Egi, Reservation, etc.) for polymorphic tracking
     * @return EgiliTransaction Created transaction record
     * @throws \Exception If wallet not found or transaction fails
     */
    public function earn(
        User $user,
        int $amount,
        string $reason,
        ?string $category = null,
        ?array $metadata = null,
        $source = null
    ): EgiliTransaction {
        // Validation: Amount must be positive
        if ($amount <= 0) {
            throw new \InvalidArgumentException("Egili earn amount must be positive, got: {$amount}");
        }

        // Check wallet exists
        if (!$user->wallet) {
            $this->logger->error('Egili earn failed - user has no wallet', [
                'user_id' => $user->id,
                'amount' => $amount,
                'reason' => $reason,
                'log_category' => 'EGILI_EARN_NO_WALLET'
            ]);

            throw new \Exception("User has no wallet. Cannot earn Egili.");
        }

        return DB::transaction(function () use ($user, $amount, $reason, $category, $metadata, $source) {
            $wallet = $user->wallet;
            $balanceBefore = $wallet->egili_balance;
            $balanceAfter = $balanceBefore + $amount;

            // ULM: Log operation start
            $this->logger->info('Egili earn initiated', [
                'user_id' => $user->id,
                'wallet_id' => $wallet->id,
                'amount' => $amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'reason' => $reason,
                'category' => $category,
                'log_category' => 'EGILI_EARN_START'
            ]);

            // Update wallet balance (atomic)
            $wallet->update([
                'egili_balance' => $balanceAfter,
                'egili_lifetime_earned' => $wallet->egili_lifetime_earned + $amount,
            ]);

            // Create transaction record (audit trail)
            $transaction = EgiliTransaction::create([
                'wallet_id' => $wallet->id,
                'user_id' => $user->id,
                'transaction_type' => 'earned',
                'operation' => 'add',
                'amount' => $amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'source_type' => $source ? get_class($source) : null,
                'source_id' => $source?->id,
                'reason' => $reason,
                'category' => $category ?? 'other',
                'metadata' => $metadata,
                'status' => 'completed',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            // GDPR: Audit trail
            $this->auditService->logUserAction(
                $user,
                'egili_earned',
                [
                    'amount' => $amount,
                    'reason' => $reason,
                    'category' => $category,
                    'balance_after' => $balanceAfter,
                    'transaction_id' => $transaction->id,
                ],
                GdprActivityCategory::BLOCKCHAIN_ACTIVITY
            );

            // ULM: Log success
            $this->logger->info('Egili earned successfully', [
                'user_id' => $user->id,
                'wallet_id' => $wallet->id,
                'transaction_id' => $transaction->id,
                'amount' => $amount,
                'balance_after' => $balanceAfter,
                'log_category' => 'EGILI_EARN_SUCCESS'
            ]);

            return $transaction;
        });
    }

    /**
     * Spend Egili (subtract from balance) - GDPR Compliant
     *
     * Creates atomic transaction:
     * 1. Validate sufficient balance
     * 2. Update wallet balance
     * 3. Create transaction record
     * 4. Log GDPR activity
     * 5. Log ULM event
     *
     * @param User $user User spending Egili
     * @param int $amount Amount to spend (positive integer)
     * @param string $reason Machine-readable reason (e.g., 'living_subscription_payment')
     * @param string|null $category Reporting category (service, fee_discount, etc.)
     * @param array|null $metadata Additional context data
     * @param mixed|null $source Source entity (EgiLivingSubscription, etc.)
     * @return EgiliTransaction Created transaction record
     * @throws \Exception If insufficient balance or transaction fails
     */
    public function spend(
        User $user,
        int $amount,
        string $reason,
        ?string $category = null,
        ?array $metadata = null,
        $source = null
    ): EgiliTransaction {
        // Validation: Amount must be positive
        if ($amount <= 0) {
            throw new \InvalidArgumentException("Egili spend amount must be positive, got: {$amount}");
        }

        // Check wallet exists
        if (!$user->wallet) {
            $this->logger->error('Egili spend failed - user has no wallet', [
                'user_id' => $user->id,
                'amount' => $amount,
                'reason' => $reason,
                'log_category' => 'EGILI_SPEND_NO_WALLET'
            ]);

            throw new \Exception("User has no wallet. Cannot spend Egili.");
        }

        // Check sufficient balance
        if (!$this->canSpend($user, $amount)) {
            $currentBalance = $this->getBalance($user);

            $this->logger->warning('Egili spend failed - insufficient balance', [
                'user_id' => $user->id,
                'wallet_id' => $user->wallet->id,
                'amount_requested' => $amount,
                'balance_available' => $currentBalance,
                'deficit' => $amount - $currentBalance,
                'reason' => $reason,
                'log_category' => 'EGILI_SPEND_INSUFFICIENT'
            ]);

            throw new \Exception(
                "Saldo Egili insufficiente. Disponibili: {$currentBalance}, Richiesti: {$amount}"
            );
        }

        return DB::transaction(function () use ($user, $amount, $reason, $category, $metadata, $source) {
            $wallet = $user->wallet;
            $balanceBefore = $wallet->egili_balance;
            $balanceAfter = $balanceBefore - $amount;

            // ULM: Log operation start
            $this->logger->info('Egili spend initiated', [
                'user_id' => $user->id,
                'wallet_id' => $wallet->id,
                'amount' => $amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'reason' => $reason,
                'category' => $category,
                'log_category' => 'EGILI_SPEND_START'
            ]);

            // Update wallet balance (atomic)
            $wallet->update([
                'egili_balance' => $balanceAfter,
                'egili_lifetime_spent' => $wallet->egili_lifetime_spent + $amount,
            ]);

            // Create transaction record (audit trail)
            $transaction = EgiliTransaction::create([
                'wallet_id' => $wallet->id,
                'user_id' => $user->id,
                'transaction_type' => 'spent',
                'operation' => 'subtract',
                'amount' => $amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'source_type' => $source ? get_class($source) : null,
                'source_id' => $source?->id,
                'reason' => $reason,
                'category' => $category ?? 'other',
                'metadata' => $metadata,
                'status' => 'completed',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            // GDPR: Audit trail
            $this->auditService->logUserAction(
                $user,
                'egili_spent',
                [
                    'amount' => $amount,
                    'reason' => $reason,
                    'category' => $category,
                    'balance_after' => $balanceAfter,
                    'transaction_id' => $transaction->id,
                ],
                GdprActivityCategory::BLOCKCHAIN_ACTIVITY
            );

            // ULM: Log success
            $this->logger->info('Egili spent successfully', [
                'user_id' => $user->id,
                'wallet_id' => $wallet->id,
                'transaction_id' => $transaction->id,
                'amount' => $amount,
                'balance_after' => $balanceAfter,
                'log_category' => 'EGILI_SPEND_SUCCESS'
            ]);

            return $transaction;
        });
    }

    /**
     * Get user's Egili transaction history
     *
     * @param User $user
     * @param int|null $limit Optional limit (null = all)
     * @return \Illuminate\Support\Collection
     */
    public function getTransactionHistory(User $user, ?int $limit = null): \Illuminate\Support\Collection {
        if (!$user->wallet) {
            return collect([]);
        }

        $query = EgiliTransaction::where('wallet_id', $user->wallet->id)
            ->orderBy('created_at', 'desc');

        if ($limit !== null) {
            $query->limit($limit);
        }

        return $query->get();
    }

    /**
     * Grant Egili bonus (admin operation)
     *
     * @param User $user User receiving bonus
     * @param int $amount Amount to grant
     * @param string $reason Admin reason
     * @param User $admin Admin performing operation
     * @param string|null $notes Additional admin notes
     * @return EgiliTransaction
     */
    public function grantBonus(
        User $user,
        int $amount,
        string $reason,
        User $admin,
        ?string $notes = null
    ): EgiliTransaction {
        if (!$user->wallet) {
            throw new \Exception("User has no wallet. Cannot grant bonus.");
        }

        return DB::transaction(function () use ($user, $amount, $reason, $admin, $notes) {
            $wallet = $user->wallet;
            $balanceBefore = $wallet->egili_balance;
            $balanceAfter = $balanceBefore + $amount;

            $wallet->update([
                'egili_balance' => $balanceAfter,
                'egili_lifetime_earned' => $wallet->egili_lifetime_earned + $amount,
            ]);

            $transaction = EgiliTransaction::create([
                'wallet_id' => $wallet->id,
                'user_id' => $user->id,
                'transaction_type' => 'admin_grant',
                'operation' => 'add',
                'amount' => $amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'reason' => $reason,
                'category' => 'admin',
                'admin_user_id' => $admin->id,
                'admin_notes' => $notes,
                'status' => 'completed',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            $this->auditService->logUserAction(
                $admin,
                'egili_admin_grant',
                [
                    'target_user_id' => $user->id,
                    'amount' => $amount,
                    'reason' => $reason,
                    'notes' => $notes,
                ],
                GdprActivityCategory::PERSONAL_DATA_UPDATE
            );

            $this->logger->info('Egili bonus granted by admin', [
                'admin_id' => $admin->id,
                'user_id' => $user->id,
                'amount' => $amount,
                'reason' => $reason,
                'log_category' => 'EGILI_ADMIN_GRANT'
            ]);

            return $transaction;
        });
    }

    // =====================================
    // === GIFT/LIFETIME EGILI METHODS ===
    // =====================================

    /**
     * Grant Gift Egili with expiration
     *
     * Admin operation to grant temporary Egili that expire after N days.
     * Used for contests, rewards, platform incentives.
     *
     * @param User $user User receiving gift
     * @param int $amount Amount of Gift Egili to grant
     * @param int $expirationDays Days until expiration
     * @param string $reason Reason for grant (e.g., 'contest_winner', 'platform_reward')
     * @param User $admin Admin granting the Egili
     * @param string|null $notes Additional admin notes
     * @return EgiliTransaction Created transaction record
     * @throws \Exception If wallet not found or transaction fails
     */
    public function grantGift(
        User $user,
        int $amount,
        int $expirationDays,
        string $reason,
        User $admin,
        ?string $notes = null
    ): EgiliTransaction {
        if ($amount <= 0) {
            throw new \InvalidArgumentException("Gift Egili amount must be positive, got: {$amount}");
        }

        if ($expirationDays <= 0) {
            throw new \InvalidArgumentException("Expiration days must be positive, got: {$expirationDays}");
        }

        if (!$user->wallet) {
            throw new \Exception("User has no wallet. Cannot grant gift Egili.");
        }

        return DB::transaction(function () use ($user, $amount, $expirationDays, $reason, $admin, $notes) {
            $wallet = $user->wallet;
            $balanceBefore = $wallet->egili_balance;
            $balanceAfter = $balanceBefore + $amount;
            $expiresAt = now()->addDays($expirationDays);

            // ULM: Log operation start
            $this->logger->info('Gift Egili grant initiated', [
                'user_id' => $user->id,
                'admin_id' => $admin->id,
                'amount' => $amount,
                'expiration_days' => $expirationDays,
                'expires_at' => $expiresAt->toDateTimeString(),
                'reason' => $reason,
                'log_category' => 'EGILI_GIFT_GRANT_START'
            ]);

            // Update wallet balance
            $wallet->update([
                'egili_balance' => $balanceAfter,
                'egili_lifetime_earned' => $wallet->egili_lifetime_earned + $amount,
            ]);

            // Create transaction record with gift fields
            $transaction = EgiliTransaction::create([
                'wallet_id' => $wallet->id,
                'user_id' => $user->id,
                'transaction_type' => 'admin_grant',
                'operation' => 'add',
                'amount' => $amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'reason' => $reason,
                'category' => 'gift',
                'admin_user_id' => $admin->id,
                'admin_notes' => $notes,
                'status' => 'completed',
                // Gift-specific fields
                'egili_type' => 'gift',
                'expires_at' => $expiresAt,
                'is_expired' => false,
                'granted_by_admin_id' => $admin->id,
                'grant_reason' => $reason,
                'priority_order' => $expiresAt->timestamp, // Earlier expiration = higher priority
                // Tracking
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            // GDPR: Audit trail (TODO: Add EGILI_GIFT_GRANTED to GdprActivityCategory)
            $this->auditService->logUserAction(
                $admin,
                'egili_gift_granted',
                [
                    'target_user_id' => $user->id,
                    'amount' => $amount,
                    'expiration_days' => $expirationDays,
                    'expires_at' => $expiresAt->toDateTimeString(),
                    'reason' => $reason,
                    'notes' => $notes,
                    'transaction_id' => $transaction->id,
                ],
                GdprActivityCategory::PERSONAL_DATA_UPDATE // TODO: Use EGILI_GIFT_GRANTED when added
            );

            // ULM: Log success
            $this->logger->info('Gift Egili granted successfully', [
                'admin_id' => $admin->id,
                'user_id' => $user->id,
                'transaction_id' => $transaction->id,
                'amount' => $amount,
                'expires_at' => $expiresAt->toDateTimeString(),
                'balance_after' => $balanceAfter,
                'log_category' => 'EGILI_GIFT_GRANT_SUCCESS'
            ]);

            return $transaction;
        });
    }

    /**
     * Grant Welcome Gift Egili to new users (no admin required)
     *
     * Automated operation for user registration flow.
     * Grants temporary Egili that expire after N days (configurable).
     * Used for Natan Tutor welcome bonus.
     *
     * @param User $user New user receiving welcome gift
     * @param int $amount Amount of Gift Egili to grant (default from config)
     * @param int $expirationDays Days until expiration (default from config)
     * @param string $reason Reason for grant (default: 'welcome_gift')
     * @return EgiliTransaction Created transaction record
     * @throws \Exception If wallet not found or transaction fails
     */
    public function grantWelcomeGift(
        User $user,
        ?int $amount = null,
        ?int $expirationDays = null,
        string $reason = 'welcome_gift'
    ): EgiliTransaction {
        // Get defaults from config
        $amount = $amount ?? config('natan-tutor.welcome_gift.amount', 100);
        $expirationDays = $expirationDays ?? config('natan-tutor.welcome_gift.expires_days', 90);

        if ($amount <= 0) {
            throw new \InvalidArgumentException("Welcome gift amount must be positive, got: {$amount}");
        }

        if ($expirationDays <= 0) {
            throw new \InvalidArgumentException("Expiration days must be positive, got: {$expirationDays}");
        }

        if (!$user->wallet) {
            throw new \Exception("User has no wallet. Cannot grant welcome gift Egili.");
        }

        return DB::transaction(function () use ($user, $amount, $expirationDays, $reason) {
            $wallet = $user->wallet;
            $balanceBefore = $wallet->egili_balance;
            $balanceAfter = $balanceBefore + $amount;
            $expiresAt = now()->addDays($expirationDays);

            // ULM: Log operation start
            $this->logger->info('Welcome Gift Egili grant initiated', [
                'user_id' => $user->id,
                'amount' => $amount,
                'expiration_days' => $expirationDays,
                'expires_at' => $expiresAt->toDateTimeString(),
                'reason' => $reason,
                'log_category' => 'EGILI_WELCOME_GIFT_START'
            ]);

            // Update wallet balance
            $wallet->update([
                'egili_balance' => $balanceAfter,
                'egili_lifetime_earned' => $wallet->egili_lifetime_earned + $amount,
            ]);

            // Create transaction record with initial_bonus type
            $transaction = EgiliTransaction::create([
                'wallet_id' => $wallet->id,
                'user_id' => $user->id,
                'transaction_type' => 'initial_bonus',
                'operation' => 'add',
                'amount' => $amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'reason' => $reason,
                'category' => 'welcome',
                'status' => 'completed',
                // Gift-specific fields
                'egili_type' => 'gift',
                'expires_at' => $expiresAt,
                'is_expired' => false,
                'grant_reason' => 'New user welcome gift - Natan Tutor credits',
                'priority_order' => $expiresAt->timestamp,
                // Tracking
                'ip_address' => request()?->ip(),
                'user_agent' => request()?->userAgent(),
            ]);

            // GDPR: Audit trail
            $this->auditService->logUserAction(
                $user,
                'egili_welcome_gift_granted',
                [
                    'amount' => $amount,
                    'expiration_days' => $expirationDays,
                    'expires_at' => $expiresAt->toDateTimeString(),
                    'reason' => $reason,
                    'transaction_id' => $transaction->id,
                ],
                GdprActivityCategory::ACCOUNT_LIFECYCLE
            );

            // ULM: Log success
            $this->logger->info('Welcome Gift Egili granted successfully', [
                'user_id' => $user->id,
                'transaction_id' => $transaction->id,
                'amount' => $amount,
                'expires_at' => $expiresAt->toDateTimeString(),
                'balance_after' => $balanceAfter,
                'log_category' => 'EGILI_WELCOME_GIFT_SUCCESS'
            ]);

            return $transaction;
        });
    }

    /**
     * Spend Egili with Priority Logic (Gift first, Lifetime after)
     *
     * Priority Order:
     * 1. Gift Egili (expiring first - LIFO by expiration)
     * 2. Lifetime Egili (FIFO by creation date)
     *
     * This ensures:
     * - Gift Egili are used before they expire
     * - Lifetime Egili are preserved when possible
     *
     * @param User $user User spending Egili
     * @param int $amount Amount to spend
     * @param string $reason Spend reason
     * @param string|null $category Spend category
     * @param array|null $metadata Additional metadata
     * @param mixed|null $source Source entity
     * @return array Array of EgiliTransaction records (one per source consumed)
     * @throws \Exception If insufficient balance or transaction fails
     */
    public function spendWithPriority(
        User $user,
        int $amount,
        string $reason,
        ?string $category = null,
        ?array $metadata = null,
        $source = null
    ): array {
        if ($amount <= 0) {
            throw new \InvalidArgumentException("Spend amount must be positive, got: {$amount}");
        }

        if (!$user->wallet) {
            throw new \Exception("User has no wallet. Cannot spend Egili.");
        }

        if (!$this->canSpend($user, $amount)) {
            $currentBalance = $this->getBalance($user);
            throw new \Exception(
                "Saldo Egili insufficiente. Disponibili: {$currentBalance}, Richiesti: {$amount}"
            );
        }

        return DB::transaction(function () use ($user, $amount, $reason, $category, $metadata, $source) {
            $wallet = $user->wallet;
            $remaining = $amount;
            $transactions = [];

            // ULM: Log spend start
            $this->logger->info('Egili spend with priority initiated', [
                'user_id' => $user->id,
                'amount' => $amount,
                'reason' => $reason,
                'log_category' => 'EGILI_SPEND_PRIORITY_START'
            ]);

            // STEP 1: Get available Egili sources with priority order
            // Priority: Gift first (expiring first), then Lifetime (FIFO)
            $sources = EgiliTransaction::where('wallet_id', $wallet->id)
                ->where('operation', 'add')
                ->where('status', 'completed')
                ->nonExpired() // Use scope from model
                ->orderByRaw("
                    CASE WHEN egili_type = 'gift' THEN 0 ELSE 1 END ASC,
                    CASE WHEN egili_type = 'gift' THEN expires_at END ASC,
                    CASE WHEN egili_type = 'lifetime' THEN created_at END ASC
                ")
                ->get();

            $this->logger->debug('Available Egili sources found', [
                'user_id' => $user->id,
                'sources_count' => $sources->count(),
                'log_category' => 'EGILI_SOURCES_LOADED'
            ]);

            // STEP 2: Loop through sources and consume until amount satisfied
            $balanceBefore = $wallet->egili_balance;

            foreach ($sources as $sourceTransaction) {
                if ($remaining <= 0) {
                    break;
                }

                // Calculate how much we can consume from this source
                $available = $sourceTransaction->amount; // Simplification: assuming full amount available
                // TODO: Track remaining balance per source for partial consumption

                $toConsume = min($available, $remaining);

                // Create spend transaction
                $spendTransaction = EgiliTransaction::create([
                    'wallet_id' => $wallet->id,
                    'user_id' => $user->id,
                    'transaction_type' => 'spent',
                    'operation' => 'subtract',
                    'amount' => $toConsume,
                    'balance_before' => $wallet->egili_balance,
                    'balance_after' => $wallet->egili_balance - $toConsume,
                    'source_type' => $source ? get_class($source) : null,
                    'source_id' => $source?->id,
                    'reason' => $reason,
                    'category' => $category ?? 'other',
                    'metadata' => array_merge($metadata ?? [], [
                        'source_transaction_id' => $sourceTransaction->id,
                        'source_egili_type' => $sourceTransaction->egili_type,
                    ]),
                    'status' => 'completed',
                    'egili_type' => $sourceTransaction->egili_type, // Inherit type from source
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);

                // Update wallet balance
                $wallet->update([
                    'egili_balance' => $wallet->egili_balance - $toConsume,
                    'egili_lifetime_spent' => $wallet->egili_lifetime_spent + $toConsume,
                ]);

                $transactions[] = $spendTransaction;
                $remaining -= $toConsume;

                $this->logger->debug('Egili consumed from source', [
                    'user_id' => $user->id,
                    'source_transaction_id' => $sourceTransaction->id,
                    'source_type' => $sourceTransaction->egili_type,
                    'amount_consumed' => $toConsume,
                    'remaining' => $remaining,
                    'log_category' => 'EGILI_SOURCE_CONSUMED'
                ]);
            }

            if ($remaining > 0) {
                // This should never happen due to canSpend() check, but safety net
                throw new \Exception("Priority spend failed: insufficient balance after consumption");
            }

            $balanceAfter = $wallet->egili_balance;

            // GDPR: Audit trail for main spend operation
            $this->auditService->logUserAction(
                $user,
                'egili_spent',
                [
                    'amount' => $amount,
                    'reason' => $reason,
                    'category' => $category,
                    'balance_before' => $balanceBefore,
                    'balance_after' => $balanceAfter,
                    'sources_used' => count($transactions),
                    'transaction_ids' => array_map(fn($t) => $t->id, $transactions),
                ],
                GdprActivityCategory::BLOCKCHAIN_ACTIVITY
            );

            // ULM: Log success
            $this->logger->info('Egili spent with priority successfully', [
                'user_id' => $user->id,
                'amount' => $amount,
                'sources_used' => count($transactions),
                'balance_after' => $balanceAfter,
                'log_category' => 'EGILI_SPEND_PRIORITY_SUCCESS'
            ]);

            return $transactions;
        });
    }

    /**
     * Expire Gift Egili (Cron Job)
     *
     * Finds all gift Egili with expires_at <= now() and marks them as expired.
     * Subtracts from wallet balance.
     *
     * Should be run daily at 00:05 (see app/Console/Kernel.php)
     *
     * @return int Count of transactions expired
     */
    public function expireGiftEgili(): int {
        $this->logger->info('Gift Egili expiration job started', [
            'log_category' => 'EGILI_EXPIRATION_START'
        ]);

        // Find all gift Egili that should be expired
        $expiredTransactions = EgiliTransaction::where('egili_type', 'gift')
            ->where('is_expired', false)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->get();

        if ($expiredTransactions->isEmpty()) {
            $this->logger->info('No gift Egili to expire', [
                'log_category' => 'EGILI_EXPIRATION_NONE'
            ]);
            return 0;
        }

        $count = 0;

        foreach ($expiredTransactions as $transaction) {
            try {
                DB::transaction(function () use ($transaction) {
                    // Mark as expired
                    $transaction->update(['is_expired' => true]);

                    // Subtract from wallet balance
                    $wallet = $transaction->wallet;
                    $wallet->decrement('egili_balance', $transaction->amount);

                    // GDPR: Audit trail (TODO: Add EGILI_GIFT_EXPIRED to GdprActivityCategory)
                    $this->auditService->logUserAction(
                        $transaction->user,
                        'egili_gift_expired',
                        [
                            'amount' => $transaction->amount,
                            'transaction_id' => $transaction->id,
                            'expires_at' => $transaction->expires_at->toDateTimeString(),
                            'granted_by_admin_id' => $transaction->granted_by_admin_id,
                            'grant_reason' => $transaction->grant_reason,
                        ],
                        GdprActivityCategory::BLOCKCHAIN_ACTIVITY // TODO: Use EGILI_GIFT_EXPIRED when added
                    );

                    $this->logger->info('Gift Egili expired', [
                        'user_id' => $transaction->user_id,
                        'transaction_id' => $transaction->id,
                        'amount' => $transaction->amount,
                        'expires_at' => $transaction->expires_at->toDateTimeString(),
                        'log_category' => 'EGILI_GIFT_EXPIRED'
                    ]);
                });

                $count++;
            } catch (\Exception $e) {
                $this->logger->error('Failed to expire gift Egili', [
                    'transaction_id' => $transaction->id,
                    'error' => $e->getMessage(),
                    'log_category' => 'EGILI_EXPIRATION_ERROR'
                ]);
            }
        }

        $this->logger->info('Gift Egili expiration job completed', [
            'expired_count' => $count,
            'log_category' => 'EGILI_EXPIRATION_COMPLETE'
        ]);

        return $count;
    }

    /**
     * Get balance breakdown (Lifetime vs Gift)
     *
     * Returns detailed balance information:
     * - Total balance
     * - Lifetime Egili
     * - Gift Egili (active)
     * - Gift Egili expiring soon (next 7 days)
     *
     * @param User $user
     * @param int $expiringSoonDays Days to consider as "expiring soon" (default: 7)
     * @return array Balance breakdown
     */
    public function getBalanceBreakdown(User $user, int $expiringSoonDays = 7): array {
        if (!$user->wallet) {
            return [
                'total' => 0,
                'lifetime' => 0,
                'gift' => 0,
                'gift_expiring_soon' => 0,
            ];
        }

        $wallet = $user->wallet;

        // Calculate lifetime balance
        $lifetimeBalance = EgiliTransaction::where('wallet_id', $wallet->id)
            ->where('operation', 'add')
            ->where('status', 'completed')
            ->lifetime()
            ->sum('amount')
            - EgiliTransaction::where('wallet_id', $wallet->id)
            ->where('operation', 'subtract')
            ->where('status', 'completed')
            ->lifetime()
            ->sum('amount');

        // Calculate active gift balance
        $giftBalance = EgiliTransaction::where('wallet_id', $wallet->id)
            ->where('operation', 'add')
            ->where('status', 'completed')
            ->gift()
            ->nonExpired()
            ->sum('amount')
            - EgiliTransaction::where('wallet_id', $wallet->id)
            ->where('operation', 'subtract')
            ->where('status', 'completed')
            ->gift()
            ->sum('amount');

        // Calculate gift expiring soon
        $giftExpiringSoon = EgiliTransaction::where('wallet_id', $wallet->id)
            ->where('operation', 'add')
            ->where('status', 'completed')
            ->expiringSoon($expiringSoonDays)
            ->sum('amount');

        return [
            'total' => $wallet->egili_balance,
            'lifetime' => max(0, $lifetimeBalance),
            'gift' => max(0, $giftBalance),
            'gift_expiring_soon' => max(0, $giftExpiringSoon),
        ];
    }
}
