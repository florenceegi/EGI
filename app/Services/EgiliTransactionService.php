<?php

namespace App\Services;

use App\Helpers\FegiAuth;
use App\Models\EgiliTransaction;
use App\Models\User;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use App\Services\Gdpr\AuditLogService;
use App\Enums\Gdpr\GdprActivityCategory;
use Illuminate\Support\Facades\DB;

/**
 * @package App\Services
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Egili Transactions)
 * @date 2025-11-10
 * @purpose Handle Egili debits for AI features and other modular services
 */
class EgiliTransactionService
{
    public function __construct(
        private UltraLogManager $logger,
        private ErrorManagerInterface $errorManager,
        private AuditLogService $auditService,
        private EgiliService $egiliService
    ) {}

    /**
     * Debit Egili for a specific feature usage.
     *
     * @param User $user
     * @param string $featureCode
     * @param int $amount
     * @param array $metadata
     * @return array{success:bool,message?:string,transaction_id?:int}
     */
    public function debitForFeature(User $user, string $featureCode, int $amount, array $metadata = []): array
    {
        if ($amount <= 0) {
            return [
                'success' => false,
                'message' => 'Invalid Egili amount for debit',
            ];
        }

        $currentBalance = $this->egiliService->getBalance($user);
        $availableEgili = is_array($currentBalance) ? ($currentBalance['egili'] ?? 0) : (int) $currentBalance;

        if ($availableEgili < $amount) {
            return [
                'success' => false,
                'message' => "Saldo Egili insufficiente. Disponibili: {$availableEgili}, richiesti: {$amount}",
            ];
        }

        try {
            $transaction = DB::transaction(function () use ($user, $amount, $featureCode, $metadata, $availableEgili) {
                $wallet = $user->wallet;
                $balanceBefore = $availableEgili;
                $balanceAfter = $balanceBefore - $amount;

                $wallet->update([
                    'egili_balance' => $balanceAfter,
                    'egili_spent_today' => ($wallet->egili_spent_today ?? 0) + $amount,
                    'last_spent_at' => now(),
                ]);

                $transaction = EgiliTransaction::create([
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'type' => 'debit',
                    'reason' => 'ai_feature',
                    'category' => 'ai_services',
                    'metadata' => array_merge($metadata, [
                        'feature_code' => $featureCode,
                    ]),
                    'balance_before' => $balanceBefore,
                    'balance_after' => $balanceAfter,
                ]);

                return $transaction;
            });

        $this->auditService->logUserAction(
            user: $user,
            action: 'egili_debited',
            context: [
                'feature_code' => $featureCode,
                'amount' => $amount,
                'transaction_id' => $transaction->id,
            ],
            category: GdprActivityCategory::AI_CREDITS_USAGE
        );

            $this->logger->info('[EgiliTransactionService] Egili debited', [
                'user_id' => $user->id,
                'feature_code' => $featureCode,
                'amount' => $amount,
                'transaction_id' => $transaction->id,
            ]);

            return [
                'success' => true,
                'transaction_id' => $transaction->id,
            ];
        } catch (\Exception $e) {
            $this->errorManager->handle('EGILI_DEBIT_FAILED', [
                'user_id' => $user->id,
                'amount' => $amount,
                'feature_code' => $featureCode,
            ], $e);

            return [
                'success' => false,
                'message' => 'Errore durante la detrazione degli Egili',
            ];
        }
    }
}

