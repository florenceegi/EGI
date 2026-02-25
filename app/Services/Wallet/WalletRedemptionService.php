<?php

namespace App\Services\Wallet;

use App\Models\User;
use App\Models\Wallet;
use App\Models\Egi;
use App\Models\EgiBlockchain;
use App\Services\Blockchain\AlgorandClient;
use App\Services\Gdpr\AuditLogService;
use App\Enums\Gdpr\GdprActivityCategory;
use Illuminate\Support\Facades\DB;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * ═══════════════════════════════════════════════════════════════════════════════
 * 📜 Oracode WalletRedemptionService: Full Wallet Ownership Transfer
 * ═══════════════════════════════════════════════════════════════════════════════
 *
 * @Oracode WalletRedemptionService: Complete custodial-to-noncustodial wallet transfer
 *
 * 🎯 Purpose:
 *    Allows users to redeem (take full ownership of) their Algorand wallet,
 *    transferring all EGI assets (ASAs) from platform custody to user control.
 *    After redemption, users have their mnemonic and full asset ownership.
 *
 * 🔄 Redemption Flow:
 *    1. Calculate cost based on number of EGIs
 *    2. Verify user has sufficient EGILI balance
 *    3. Deduct EGILI from user
 *    4. Fund wallet with ALGO (for opt-ins + min balance)
 *    5. Batch opt-in for all user's ASAs
 *    6. Batch transfer all ASAs from Treasury to user wallet
 *    7. Deliver mnemonic to user
 *    8. Delete mnemonic from database (irreversible)
 *
 * 📌 ToS v3.0.0 — NOTA: WalletRedemptionService (ALGO→Egili) non è elencato nelle fonti
 *    standard di ottenimento Egili (AI Package + merit reward). **DECISIONE FABIO 2026-02-25:
 *    MANTENERE** — funzionalità presente e attiva. Ref: debiti_tecnici.md §8 — A3
 *
 * 💰 Cost Formula:
 *    Base Cost: 0.1 ALGO (minimum account balance)
 *    Per-EGI: 0.1 ALGO (each ASA opt-in requires 0.1 ALGO)
 *    Fees: ~0.001 ALGO per transaction
 *    Total: (N × 0.1) + 0.1 + fees → converted to EGILI at current rate
 *
 * 🛡️ Security Considerations:
 *    - GDPR audit logging for all sensitive operations
 *    - Mnemonic never logged
 *    - Atomic batch transactions (max 16 per group, Algorand limit)
 *    - Multiple batches for users with many EGIs
 *
 * @package App\Services\Wallet
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Wallet Redemption Module)
 * @date 2025-01-20
 * @purpose Enable custodial-to-noncustodial wallet transfer with ASA migration
 * @source docs/ai/blockchain/nuova_logica_wallet.md
 */
class WalletRedemptionService {
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;
    protected AuditLogService $auditService;
    protected AlgorandClient $algorandClient;
    protected WalletProvisioningService $provisioningService;

    /**
     * Cost per ASA in microAlgos (0.1 ALGO = 100,000 microAlgos)
     */
    public const COST_PER_ASA_MICRO_ALGO = 100000;

    /**
     * Base wallet cost in microAlgos (0.1 ALGO minimum balance)
     */
    public const BASE_WALLET_COST_MICRO_ALGO = 100000;

    /**
     * Estimated transaction fee in microAlgos (0.001 ALGO)
     */
    public const TX_FEE_MICRO_ALGO = 1000;

    /**
     * Maximum ASAs per batch (Algorand atomic group limit)
     */
    public const MAX_BATCH_SIZE = 16;

    /**
     * EGILI to ALGO conversion rate (how many EGILI per 1 ALGO)
     * TODO: Make this configurable or fetch from oracle
     */
    public const EGILI_PER_ALGO = 100; // 100 EGILI = 1 ALGO

    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        AuditLogService $auditService,
        AlgorandClient $algorandClient,
        WalletProvisioningService $provisioningService
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->auditService = $auditService;
        $this->algorandClient = $algorandClient;
        $this->provisioningService = $provisioningService;
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // 💰 COST CALCULATION
    // ═══════════════════════════════════════════════════════════════════════════

    /**
     * Calculate redemption cost for a user
     *
     * Formula: (N × 0.1 ALGO) + 0.1 ALGO base + estimated fees
     * Returns both microAlgos and EGILI equivalent
     *
     * @param User $user The user requesting redemption
     * @return array ['micro_algos' => int, 'algo' => float, 'egili' => int, 'breakdown' => array]
     */
    public function calculateRedemptionCost(User $user): array {
        // 1. Get user's ASA count
        $asaCount = $this->getUserAsaCount($user);

        // 2. Calculate costs
        $baseCost = self::BASE_WALLET_COST_MICRO_ALGO;
        $asaCost = $asaCount * self::COST_PER_ASA_MICRO_ALGO;

        // Estimate fees: opt-in txs + transfer txs + some margin
        $optInBatches = ceil($asaCount / self::MAX_BATCH_SIZE) ?: 1;
        $transferBatches = ceil($asaCount / self::MAX_BATCH_SIZE) ?: 1;
        $estimatedFees = ($optInBatches + $transferBatches + 2) * self::TX_FEE_MICRO_ALGO;

        $totalMicroAlgos = $baseCost + $asaCost + $estimatedFees;
        $totalAlgo = $totalMicroAlgos / 1_000_000;
        $totalEgili = (int) ceil($totalAlgo * self::EGILI_PER_ALGO);

        // 3. ULM: Log calculation
        $this->logger->info('WalletRedemption: Cost calculated', [
            'user_id' => $user->id,
            'asa_count' => $asaCount,
            'total_micro_algos' => $totalMicroAlgos,
            'total_algo' => $totalAlgo,
            'total_egili' => $totalEgili,
            'log_category' => 'WALLET_REDEMPTION_COST_CALC'
        ]);

        return [
            'micro_algos' => $totalMicroAlgos,
            'algo' => round($totalAlgo, 6),
            'egili' => $totalEgili,
            'breakdown' => [
                'asa_count' => $asaCount,
                'base_cost_algo' => 0.1,
                'asa_cost_algo' => $asaCount * 0.1,
                'estimated_fees_algo' => round($estimatedFees / 1_000_000, 6),
                'egili_rate' => self::EGILI_PER_ALGO . ' EGILI = 1 ALGO',
            ],
        ];
    }

    /**
     * Get count of ASAs owned by user (for cost calculation)
     *
     * @param User $user
     * @return int
     */
    public function getUserAsaCount(User $user): int {
        return EgiBlockchain::where('buyer_user_id', $user->id)
            ->whereNotNull('asa_id')
            ->where('mint_status', 'minted')
            ->count();
    }

    /**
     * Get all ASA IDs owned by a user
     *
     * @param User $user
     * @return array Array of ASA IDs (integers)
     */
    public function getUserAsaIds(User $user): array {
        return EgiBlockchain::where('buyer_user_id', $user->id)
            ->whereNotNull('asa_id')
            ->where('mint_status', 'minted')
            ->pluck('asa_id')
            ->map(fn($id) => (int) $id)
            ->toArray();
    }

    /**
     * Get detailed EGI information for user's owned assets
     *
     * @param User $user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUserEgis(User $user) {
        return Egi::whereHas('blockchain', function ($query) use ($user) {
            $query->where('buyer_user_id', $user->id)
                ->whereNotNull('asa_id')
                ->where('mint_status', 'minted');
        })
            ->with(['blockchain:id,egi_id,asa_id,buyer_wallet,minted_at', 'collection:id,name'])
            ->get(['id', 'title', 'collection_id', 'token_EGI', 'created_at']);
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // ✅ PRE-REDEMPTION VALIDATION
    // ═══════════════════════════════════════════════════════════════════════════

    /**
     * Validate that redemption is possible for user
     *
     * Checks:
     * - User has a wallet
     * - Wallet has encrypted mnemonic (not already redeemed)
     * - Wallet has sufficient EGILI balance
     *
     * @param User $user
     * @return array ['valid' => bool, 'errors' => array, 'cost' => array|null]
     */
    public function validateRedemption(User $user): array {
        $errors = [];

        // 1. Check wallet exists
        $wallet = $user->wallet()->first();
        if (!$wallet) {
            $errors[] = 'L\'utente non ha un wallet associato.';
            return ['valid' => false, 'errors' => $errors, 'cost' => null];
        }

        // 2. Check wallet has mnemonic (not already redeemed)
        if (!$wallet->hasMnemonic()) {
            $errors[] = 'Il wallet è già stato riscattato o non ha mnemonic.';
            return ['valid' => false, 'errors' => $errors, 'cost' => null];
        }

        // 3. Calculate cost
        $cost = $this->calculateRedemptionCost($user);

        // 4. Check EGILI balance (on wallet, not user)
        $egiliBalance = $wallet->egili_balance ?? 0;
        if ($egiliBalance < $cost['egili']) {
            $errors[] = "EGILI insufficienti. Richiesti: {$cost['egili']} EGILI, disponibili: {$egiliBalance} EGILI.";
        }

        // 5. Log validation
        $this->logger->info('WalletRedemption: Validation completed', [
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'valid' => empty($errors),
            'errors' => $errors,
            'egili_required' => $cost['egili'],
            'egili_balance' => $egiliBalance,
            'log_category' => 'WALLET_REDEMPTION_VALIDATION'
        ]);

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'cost' => $cost,
            'wallet_address' => $wallet->wallet,
            'egili_balance' => $egiliBalance,
        ];
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // 🚀 REDEMPTION EXECUTION
    // ═══════════════════════════════════════════════════════════════════════════

    /**
     * Execute full wallet redemption process
     *
     * IMPORTANT: This is an irreversible operation!
     *
     * Steps:
     * 1. Validate (balance, wallet exists, mnemonic exists)
     * 2. Deduct EGILI from user
     * 3. Fund wallet with ALGO
     * 4. Batch opt-in to all ASAs
     * 5. Batch transfer all ASAs to user
     * 6. Retrieve and return mnemonic
     * 7. Delete mnemonic from database
     *
     * @param User $user The user redeeming their wallet
     * @return array ['success' => bool, 'mnemonic' => string|null, 'error' => string|null, 'details' => array]
     * @throws \Exception if redemption fails at any critical step
     */
    public function executeRedemption(User $user): array {
        // 1. Pre-flight validation
        $validation = $this->validateRedemption($user);
        if (!$validation['valid']) {
            return [
                'success' => false,
                'mnemonic' => null,
                'error' => implode(' ', $validation['errors']),
                'details' => ['validation_errors' => $validation['errors']],
            ];
        }

        $wallet = $user->wallet()->first();
        $cost = $validation['cost'];
        $asaIds = $this->getUserAsaIds($user);

        // ULM: Log redemption start
        $this->logger->warning('WalletRedemption: Starting redemption (IRREVERSIBLE)', [
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'wallet_address' => $wallet->wallet,
            'asa_count' => count($asaIds),
            'cost_egili' => $cost['egili'],
            'cost_algo' => $cost['algo'],
            'log_category' => 'WALLET_REDEMPTION_START'
        ]);

        try {
            return DB::transaction(function () use ($user, $wallet, $cost, $asaIds) {
                $details = [];

                // ═══════════════════════════════════════════════════════════
                // STEP 2: Deduct EGILI from wallet (not user!)
                // ═══════════════════════════════════════════════════════════
                $wallet->decrement('egili_balance', $cost['egili']);
                $wallet->increment('egili_lifetime_spent', $cost['egili']);
                $details['egili_deducted'] = $cost['egili'];

                $this->logger->info('WalletRedemption: EGILI deducted', [
                    'user_id' => $user->id,
                    'wallet_id' => $wallet->id,
                    'amount' => $cost['egili'],
                    'new_balance' => $wallet->fresh()->egili_balance,
                    'log_category' => 'WALLET_REDEMPTION_EGILI_DEDUCTED'
                ]);

                // ═══════════════════════════════════════════════════════════
                // STEP 3: Fund wallet with ALGO
                // ═══════════════════════════════════════════════════════════
                $fundResult = $this->provisioningService->fundWallet($wallet, $cost['micro_algos']);
                $details['funding'] = [
                    'tx_id' => $fundResult['txId'],
                    'amount_algo' => $fundResult['amount_algo'],
                    'block' => $fundResult['block'],
                ];

                $this->logger->info('WalletRedemption: Wallet funded', [
                    'user_id' => $user->id,
                    'wallet_address' => $wallet->wallet,
                    'tx_id' => $fundResult['txId'],
                    'amount_micro_algos' => $cost['micro_algos'],
                    'log_category' => 'WALLET_REDEMPTION_FUNDED'
                ]);

                // ═══════════════════════════════════════════════════════════
                // STEP 4 & 5: Opt-in and Transfer ASAs (if any)
                // ═══════════════════════════════════════════════════════════
                if (!empty($asaIds)) {
                    // Retrieve mnemonic for opt-in operations (user needs to sign)
                    $mnemonic = $this->provisioningService->retrieveMnemonic($wallet, $user);

                    // Process in batches of 16 (Algorand limit)
                    $asaBatches = array_chunk($asaIds, self::MAX_BATCH_SIZE);
                    $optInResults = [];
                    $transferResults = [];

                    foreach ($asaBatches as $batchIndex => $batch) {
                        // STEP 4: Batch opt-in
                        $this->logger->info('WalletRedemption: Processing opt-in batch', [
                            'user_id' => $user->id,
                            'batch_index' => $batchIndex,
                            'batch_size' => count($batch),
                            'asa_ids' => $batch,
                            'log_category' => 'WALLET_REDEMPTION_OPTIN_BATCH'
                        ]);

                        if (count($batch) === 1) {
                            $optInResult = $this->algorandClient->optInToAsa($mnemonic, $batch[0]);
                            $optInResults[] = $optInResult;
                        } else {
                            $optInResult = $this->algorandClient->batchOptInToAsas($mnemonic, $batch);
                            $optInResults[] = $optInResult;
                        }

                        // STEP 5: Batch transfer
                        $this->logger->info('WalletRedemption: Processing transfer batch', [
                            'user_id' => $user->id,
                            'batch_index' => $batchIndex,
                            'batch_size' => count($batch),
                            'to_address' => $wallet->wallet,
                            'log_category' => 'WALLET_REDEMPTION_TRANSFER_BATCH'
                        ]);

                        if (count($batch) === 1) {
                            $transferResult = $this->algorandClient->transferAsa($wallet->wallet, $batch[0]);
                            $transferResults[] = $transferResult;
                        } else {
                            $transferResult = $this->algorandClient->batchTransferAsas($wallet->wallet, $batch);
                            $transferResults[] = $transferResult;
                        }
                    }

                    $details['opt_in_results'] = $optInResults;
                    $details['transfer_results'] = $transferResults;
                    $details['asa_count'] = count($asaIds);
                    $details['batch_count'] = count($asaBatches);

                    // Wipe mnemonic from this variable (we'll retrieve fresh for return)
                    if (function_exists('sodium_memzero')) {
                        sodium_memzero($mnemonic);
                    }
                }

                // ═══════════════════════════════════════════════════════════
                // STEP 6: Retrieve mnemonic for user (fresh retrieval)
                // ═══════════════════════════════════════════════════════════
                $mnemonic = $this->provisioningService->retrieveMnemonic($wallet, $user);

                // ═══════════════════════════════════════════════════════════
                // STEP 7: Delete mnemonic from database (IRREVERSIBLE!)
                // ═══════════════════════════════════════════════════════════
                $this->deleteMnemonicFromWallet($wallet, $user);
                $details['mnemonic_deleted'] = true;

                // ═══════════════════════════════════════════════════════════
                // GDPR Audit: Log complete redemption
                // ═══════════════════════════════════════════════════════════
                $this->auditService->logUserAction(
                    $user,
                    'wallet_redeemed',
                    [
                        'wallet_id' => $wallet->id,
                        'wallet_address' => $wallet->wallet,
                        'asa_count' => count($asaIds),
                        'cost_egili' => $cost['egili'],
                        'cost_algo' => $cost['algo'],
                    ],
                    GdprActivityCategory::WALLET_REDEEMED
                );

                // ULM: Log success
                $this->logger->warning('WalletRedemption: Redemption completed successfully', [
                    'user_id' => $user->id,
                    'wallet_id' => $wallet->id,
                    'wallet_address' => $wallet->wallet,
                    'asa_count' => count($asaIds),
                    'cost_egili' => $cost['egili'],
                    'log_category' => 'WALLET_REDEMPTION_COMPLETED'
                ]);

                return [
                    'success' => true,
                    'mnemonic' => $mnemonic,
                    'error' => null,
                    'details' => $details,
                ];
            });
        } catch (\Exception $e) {
            // ULM: Log failure
            $this->logger->error('WalletRedemption: Redemption failed', [
                'user_id' => $user->id,
                'wallet_id' => $wallet->id ?? null,
                'error' => $e->getMessage(),
                'log_category' => 'WALLET_REDEMPTION_FAILED'
            ]);

            // UEM: Handle error
            $this->errorManager->handle('WALLET_REDEMPTION_FAILED', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ], $e);

            return [
                'success' => false,
                'mnemonic' => null,
                'error' => 'Errore durante il riscatto: ' . $e->getMessage(),
                'details' => ['exception' => $e->getMessage()],
            ];
        }
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // 🗑️ MNEMONIC DELETION
    // ═══════════════════════════════════════════════════════════════════════════

    /**
     * Delete mnemonic encryption data from wallet
     *
     * IRREVERSIBLE: After this, the platform can no longer access the wallet.
     * User MUST have saved their mnemonic before calling this.
     *
     * @param Wallet $wallet
     * @param User $user For audit logging
     * @throws \Exception if deletion fails
     */
    protected function deleteMnemonicFromWallet(Wallet $wallet, User $user): void {
        try {
            // 1. Clear encryption fields
            $wallet->update([
                'secret_ciphertext' => null,
                'secret_nonce' => null,
                'dek_encrypted' => null,
                'cipher_algo' => null,
                'metadata' => array_merge($wallet->metadata ?? [], [
                    'redeemed_at' => now()->toISOString(),
                    'redeemed_by_user_id' => $user->id,
                    'mnemonic_deleted' => true,
                ]),
            ]);

            // 2. GDPR Audit: Log deletion
            $this->auditService->logUserAction(
                $user,
                'wallet_mnemonic_deleted',
                [
                    'wallet_id' => $wallet->id,
                    'wallet_address' => $wallet->wallet,
                    'reason' => 'wallet_redemption',
                ],
                GdprActivityCategory::WALLET_MNEMONIC_DELETED
            );

            // 3. ULM: Log deletion
            $this->logger->warning('WalletRedemption: Mnemonic deleted from database', [
                'user_id' => $user->id,
                'wallet_id' => $wallet->id,
                'wallet_address' => $wallet->wallet,
                'log_category' => 'WALLET_MNEMONIC_DELETED'
            ]);
        } catch (\Exception $e) {
            $this->logger->error('WalletRedemption: Mnemonic deletion failed', [
                'user_id' => $user->id,
                'wallet_id' => $wallet->id,
                'error' => $e->getMessage(),
                'log_category' => 'WALLET_MNEMONIC_DELETE_ERROR'
            ]);

            throw new \Exception("Failed to delete mnemonic: " . $e->getMessage(), 0, $e);
        }
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // 📊 STATUS CHECK
    // ═══════════════════════════════════════════════════════════════════════════

    /**
     * Check if a wallet has been redeemed
     *
     * @param Wallet $wallet
     * @return bool
     */
    public function isWalletRedeemed(Wallet $wallet): bool {
        return !$wallet->hasMnemonic();
    }

    /**
     * Get redemption status for a user
     *
     * @param User $user
     * @return array ['redeemed' => bool, 'redeemed_at' => string|null, 'wallet_address' => string|null]
     */
    public function getRedemptionStatus(User $user): array {
        $wallet = $user->wallet()->first();

        if (!$wallet) {
            return [
                'has_wallet' => false,
                'redeemed' => false,
                'redeemed_at' => null,
                'wallet_address' => null,
            ];
        }

        $redeemed = $this->isWalletRedeemed($wallet);
        $redeemedAt = null;

        if ($redeemed && isset($wallet->metadata['redeemed_at'])) {
            $redeemedAt = $wallet->metadata['redeemed_at'];
        }

        return [
            'has_wallet' => true,
            'redeemed' => $redeemed,
            'redeemed_at' => $redeemedAt,
            'wallet_address' => $wallet->wallet,
            'can_redeem' => !$redeemed && $wallet->hasMnemonic(),
        ];
    }
}
