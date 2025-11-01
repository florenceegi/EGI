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
 * - Conversion rate: 1 Egilo ≈ €0.10 (perceived value)
 * 
 * @package App\Services
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Egili Token System)
 * @date 2025-11-01
 * @purpose Egili token operations with GDPR compliance
 */
class EgiliService
{
    private UltraLogManager $logger;
    private ErrorManagerInterface $errorManager;
    private AuditLogService $auditService;
    
    /**
     * Conversion rate Egili → EUR (perceived value)
     * NOT a real exchange rate (Egili are NOT currency)
     */
    const EGILI_TO_EUR_RATE = 0.10;
    
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
    public function getBalance(User $user): int
    {
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
    public function canSpend(User $user, int $amount): bool
    {
        return $this->getBalance($user) >= $amount;
    }
    
    /**
     * Calculate EUR equivalent of Egili amount
     * 
     * @param int $egiliAmount
     * @return float EUR equivalent (perceived value)
     */
    public function toEur(int $egiliAmount): float
    {
        return round($egiliAmount * self::EGILI_TO_EUR_RATE, 2);
    }
    
    /**
     * Calculate Egili equivalent of EUR amount
     * 
     * @param float $eurAmount
     * @return int Egili equivalent (rounded)
     */
    public function fromEur(float $eurAmount): int
    {
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
    public function getTransactionHistory(User $user, ?int $limit = null): \Illuminate\Support\Collection
    {
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
}

