<?php

namespace App\Services\Payment;

use App\Enums\Gdpr\GdprActivityCategory;
use App\Models\Collection;
use App\Models\User;
use App\Models\Wallet;
use App\Services\Gdpr\AuditLogService;
use App\Services\Gdpr\ConsentService;
use Illuminate\Support\Collection as LaravelCollection;
use Illuminate\Support\Facades\DB;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * @Oracode Service: Stripe Payment Split Manager (GDPR Compliant)
 * 🎯 Purpose: Gestisce split payment multi-wallet per mint EGI
 * 🛡️ Security: Transazioni atomiche con rollback completo
 * 💰 Business: Distribuisce fondi collector tra tutti i wallet della collection
 * 🔒 GDPR: Full audit trail, consent verification, PII protection
 *
 * @package App\Services\Payment
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 2.0.0 (FlorenceEGI - GDPR Compliant Split Payment)
 * @date 2025-11-17
 * @purpose Split payment automatico per mint EGI con distribuzione multi-wallet GDPR-compliant
 */
class StripePaymentSplitService {
    protected StripeClient $stripeClient;
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;
    protected AuditLogService $auditService;
    protected ConsentService $consentService;
    protected \App\Services\PaymentDistributionService $distributionService;
    // protected \App\Contracts\GoldPriceServiceInterface $goldPriceService; // REMOVED FOR STRATEGY PATTERN

    /**
     * @param UltraLogManager $logger
     * @param ErrorManagerInterface $errorManager
     * @param AuditLogService $auditService
     * @param ConsentService $consentService
     * @param \App\Services\PaymentDistributionService $distributionService
     */
    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        AuditLogService $auditService,
        ConsentService $consentService,
        \App\Services\PaymentDistributionService $distributionService
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->auditService = $auditService;
        $this->consentService = $consentService;
        $this->distributionService = $distributionService;

        $secretKey = config('algorand.payments.stripe.secret_key');

        if (empty($secretKey)) {
            throw new \Exception('Stripe secret key not configured');
        }

        $this->stripeClient = new StripeClient($secretKey);
    }

    /**
     * 🚀 MiCA-SAFE: Create DIRECT split payments to Connected Accounts
     *
     * NO TRANSFER API - Funds go DIRECTLY to wallet owners' Stripe accounts
     * Platform NEVER holds/transits funds (MiCA compliance)
     *
     * @param Collection $collection
     * @param PaymentRequest $request
     * @param array $metadata
     * @return PaymentResult
     * @throws \Exception
     */
    public function createDirectSplitPayments(
        Collection $collection,
        \App\DataTransferObjects\Payment\PaymentRequest $request,
        array $metadata
    ): \App\DataTransferObjects\Payment\PaymentResult {
        $this->logger->info('MiCA-SAFE: Creating direct split payments', [
            'collection_id' => $collection->id,
            'total_amount' => $request->amount,
            'egi_id' => $request->egiId,
            'buyer_user_id' => $request->userId,
        ]);

        // 1. Get and validate all collection wallets
        $wallets = $this->getValidatedCollectionWallets($collection);

        // 2. GDPR: Validate payment processing consents for ALL wallet owners
        $this->validateWalletOwnersConsents($wallets);

        // 3. Calculate distribution amounts
        $distributions = $this->calculateDistributions($wallets, $request->amount, $request->egiId, $metadata);

        // 4. Validate all wallets have Stripe accounts configured
        $this->validateStripeAccounts($distributions);

        // 5. Create DIRECT PaymentIntents to each Connected Account (MiCA-SAFE)
        $paymentIntents = $this->createDirectPaymentIntents(
            $distributions,
            $request,
            $metadata
        );

        // 6. ACCOUNTING: Create PaymentDistribution records
        $distributionRecords = $this->createDistributionRecordsFromIntents(
            $collection,
            $distributions,
            $paymentIntents,
            $metadata
        );

        // 7. GDPR: Audit trail
        $this->logDirectPaymentsAuditTrail(
            $request->userId,
            $distributions,
            $paymentIntents,
            $metadata
        );

        $this->logger->info('MiCA-SAFE: Direct split payments created successfully', [
            'collection_id' => $collection->id,
            'payments_count' => count($paymentIntents),
            'total_distributed' => array_sum(array_column($distributions, 'amount_eur')),
        ]);

        // Return first PaymentIntent as primary (for frontend redirect)
        $primaryIntent = $paymentIntents[0];

        return new \App\DataTransferObjects\Payment\PaymentResult(
            success: true,
            paymentId: $primaryIntent->id,
            amount: $request->amount,
            currency: strtoupper($request->currency),
            status: 'pending', // Will be confirmed by frontend
            metadata: array_merge($metadata, [
                'split_payments' => array_map(fn($pi) => [
                    'payment_intent_id' => $pi->id,
                    'amount' => $pi->amount / 100,
                    'destination' => $pi->transfer_data['destination'] ?? null,
                ], $paymentIntents),
                'client_secret' => $primaryIntent->client_secret,
            ]),
            receiptUrl: null,
            processedAt: new \DateTimeImmutable()
        );
    }

    /**
     * DEPRECATED - OLD TRANSFER API METHOD (MiCA violation)
     *
     * @deprecated Use createDirectSplitPayments() instead
     * @param string $paymentIntentId Stripe PaymentIntent ID (already succeeded)
     * @param Collection $collection Collection with wallets to distribute to
     * @param float $totalAmountEur Total amount paid by collector
     * @param array $metadata Additional metadata for transfers (must include buyer_user_id)
     * @return array ['success' => bool, 'transfers' => array, 'distributions' => array]
     * @throws \Exception If split payment fails or GDPR validation fails
     *
     * @privacy-safe Full GDPR audit trail for all wallet owners
     * @security Validates payment processing consent for all recipients
     */
    public function splitPaymentToWallets(
        string $paymentIntentId,
        Collection $collection,
        float $totalAmountEur,
        array $metadata = []
    ): array {
        $buyerUserId = $metadata['buyer_user_id'] ?? null;

        if (!$buyerUserId) {
            throw new \Exception('buyer_user_id required in metadata for GDPR compliance');
        }

        $this->logger->info('Stripe split payment initiated', [
            'payment_intent_id' => $paymentIntentId,
            'collection_id' => $collection->id,
            'total_amount_eur' => $totalAmountEur,
            'buyer_user_id' => $buyerUserId,
            'log_category' => 'STRIPE_SPLIT_PAYMENT_START'
        ]);

        // 1. Get and validate all collection wallets
        $wallets = $this->getValidatedCollectionWallets($collection);

        // 2. GDPR: Validate payment processing consents for ALL wallet owners
        $this->validateWalletOwnersConsents($wallets);

        // 3. Calculate distribution amounts
        $egiId = isset($metadata['egi_id']) ? (int) $metadata['egi_id'] : null;
        $distributions = $this->calculateDistributions($wallets, $totalAmountEur, $egiId, $metadata);

        // 4. Validate all wallets have Stripe accounts configured
        $this->validateStripeAccounts($distributions);

        // 5. Execute transfers atomically with rollback capability
        $transferResults = $this->executeAtomicTransfers(
            $paymentIntentId,
            $distributions,
            $metadata
        );

        // 6. ACCOUNTING: Create PaymentDistribution records for financial tracking
        $distributionRecords = $this->createDistributionRecords(
            $paymentIntentId,
            $collection,
            $distributions,
            $transferResults['transfers'],
            $metadata
        );

        // 7. GDPR: Create audit trail for EACH wallet owner receiving payment
        $this->logPaymentDistributionAuditTrail(
            $buyerUserId,
            $distributions,
            $transferResults['transfers'],
            $metadata
        );

        $this->logger->info('Stripe split payment completed', [
            'payment_intent_id' => $paymentIntentId,
            'collection_id' => $collection->id,
            'transfers_count' => count($transferResults['transfers']),
            'total_distributed' => array_sum(array_column($distributions, 'amount_eur')),
            'recipients_count' => count($distributions),
            'distribution_records' => count($distributionRecords),
            'log_category' => 'STRIPE_SPLIT_PAYMENT_SUCCESS'
        ]);

        return [
            'success' => true,
            'transfers' => $transferResults['transfers'],
            'distributions' => $distributions,
            'distribution_records' => $distributionRecords,
            'payment_intent_id' => $paymentIntentId,
        ];
    }

    /**
     * Get and validate all wallets for collection
     *
     * @param Collection $collection
     * @return LaravelCollection<Wallet>
     * @throws \Exception If no wallets or invalid percentages
     */
    protected function getValidatedCollectionWallets(Collection $collection): LaravelCollection {
        $wallets = $collection->wallets()->with('user')->get();

        if ($wallets->isEmpty()) {
            $this->errorManager->handle('MINT_NO_WALLETS_CONFIGURED', [
                'collection_id' => $collection->id,
            ]);

            throw new \Exception("No wallets configured for collection {$collection->id}");
        }

        // Validate total mint percentages sum to 100%
        $totalPercentage = $wallets->sum('royalty_mint');

        if (abs($totalPercentage - 100) > 0.01) { // Allow 0.01% tolerance for floating point
            $this->errorManager->handle('MINT_INVALID_PERCENTAGES', [
                'collection_id' => $collection->id,
                'total_percentage' => $totalPercentage,
                'expected' => 100,
            ]);

            throw new \Exception(
                "Collection {$collection->id} wallet percentages don't sum to 100% (current: {$totalPercentage}%)"
            );
        }

        $this->logger->info('Collection wallets validated', [
            'collection_id' => $collection->id,
            'wallets_count' => $wallets->count(),
            'total_percentage' => $totalPercentage,
        ]);

        return $wallets;
    }

    /**
     * Calculate distribution amounts for each wallet
     *
     * @param LaravelCollection<Wallet> $wallets
     * @param float $totalAmountEur
     * @return array
     */
    protected function calculateDistributions(LaravelCollection $wallets, float $totalAmountEur, ?int $egiId = null, array $metadata = []): array {
        $distributions = [];
        $totalAmountCents = (int) round($totalAmountEur * 100);
        $distributedCents = 0;

        // Filter wallets with royalty > 0 (exclude 0% royalty wallets)
        $activeWallets = $wallets->filter(fn($w) => ($w->royalty_mint ?? 0) > 0)->values();

        if ($activeWallets->isEmpty()) {
            throw new \RuntimeException('No wallets with royalty > 0% found for distribution');
        }

        // Recupera la collection dal primo wallet (assumiamo appartengano alla stessa collection)
        $collection = $wallets->first()->collection;
        
        // --- COMMODITY LOGIC CHECK ---
        $isCommodity = false;
        $commodityBreakdown = null;

        if ($egiId) {
            // OS3 Robustness: Eager load traits
            $egi = \App\Models\Egi::with(['traits.traitType'])->find($egiId);
            
            // --- COST OVERRIDE LOGIC (PRIORITY) ---
            // If the controller passed a frozen 'commodity_base_value', USE IT immediately.
            // This bypasses any DB type detection issues or strategy failures.
            if (isset($metadata['commodity_base_value']) && is_numeric($metadata['commodity_base_value'])) {
                $baseValueOverride = (float) $metadata['commodity_base_value'];
                $marginOverride = max(0, $totalAmountEur - $baseValueOverride);
                
                $isCommodity = true;
                $commodityBreakdown = [
                    'base_value' => $baseValueOverride,
                    'margin_applied' => $marginOverride,
                    'final_value' => $totalAmountEur,
                    'currency' => 'EUR',
                    'source' => 'metadata_override'
                ];
                
                $this->logger->info('Commodity Split: Using FROZEN COST from Metadata (Global Override)', [
                    'egi_id' => $egiId,
                    'base_value_override' => $baseValueOverride,
                    'margin_override' => $marginOverride,
                    'total' => $totalAmountEur
                ]);
            } else {
                // STRATEGY PATTERN: Detect commodity type and resolve strategy
                $commodityType = $egi ? ($egi->commodity_type ?? ($egi->getTraitByTypeSlug('commodity-type')?->value)) : null;

                // Fallback for Backward Compatibility
                if (!$commodityType && $egi && $egi->isGoldBar()) {
                    $commodityType = 'goldbar';
                }

                if ($commodityType) {
                    try {
                        // RESOLVE STRATEGY
                        $commodityStrategy = \App\Egi\Commodity\CommodityFactory::make($commodityType);
                        
                        $isCommodity = true;
                        
                        // CALCULATE VALUE USING STRATEGY RECALCULATION
                        $commodityBreakdown = $commodityStrategy->calculateValue($egi, 'EUR');
                        
                        $this->logger->info('Commodity Split Logic Activated (DB Strategy Pattern)', [
                            'egi_id' => $egiId,
                            'commodity_type' => $commodityType,
                            'strategy' => get_class($commodityStrategy),
                            'total_price' => $totalAmountEur,
                            'breakdown' => $commodityBreakdown
                        ]);
                    } catch (\Exception $e) {
                         $this->logger->warning('Commodity Strategy Resolution Failed', [
                            'egi_id' => $egiId,
                            'type' => $commodityType,
                            'error' => $e->getMessage()
                        ]);
                        $isCommodity = false;
                    }
                }
            }
        }

        // Calcola la percentuale EPP effettiva (dinamica in base al profilo/collection)
        $effectiveEppPercentage = $collection->getEffectiveEppPercentage();

        foreach ($activeWallets as $index => $wallet) {
            $percentage = $wallet->royalty_mint;
            $amountCents = 0;
            $amountEur = 0;

            // Se il wallet è di tipo EPP, sovrascrivi la percentuale con quella calcolata dynamicamente
            if ($wallet->platform_role === 'epp') {
                $percentage = $effectiveEppPercentage;
                if ($percentage <= 0) {
                     continue;
                }
            }

            // --- COMMODITY CALCULATION ---
            if ($isCommodity && $commodityBreakdown) {
                // Logic:
                // Cost (Safe Capital) -> Company (Owner) -> 100% exempt from fees
                // Margin (Markup) -> Split: Platform 10%, Company 90% (or rest)
                
                $baseValue = $commodityBreakdown['base_value'] ?? 0; // Cost
                $marginApplied = $commodityBreakdown['margin_applied'] ?? 0; // Margin
                
                // Normalise role for comparison
                $role = strtolower($wallet->platform_role ?? '');
                
                // Platform Roles: Natan, Collector, App, Admin
                $isPlatform = in_array($role, ['natan', 'collector', 'epp', 'admin']);
                
                // Company Roles: Company, Creator (explicit ownership)
                $isCompany = in_array($role, ['company', 'creator']) || $wallet->user_id === $collection->collection_owner_id;

                if ($isPlatform) { 
                    // Platform takes configured percentage (default 10%) of MARGIN only
                    $platformFeeOnMargin = $marginApplied * (config('egi.fees.platform_margin_percentage', 10.0) / 100);
                    $amountEur = $platformFeeOnMargin;
                    $amountCents = (int) round($amountEur * 100);
                    // Recalculate generic percentage for logs/records
                    // Guard against division by zero if total is 0 (unlikely)
                    $percentage = $totalAmountEur > 0 ? ($amountEur / $totalAmountEur) * 100 : 0;
                    
                    $this->logger->info('Commodity Split: Calculated Platform Fee', [
                        'role' => $wallet->platform_role,
                        'margin' => $marginApplied,
                        'fee' => $amountEur
                    ]);
                } elseif ($isCompany) {
                    // Company takes Cost + Remainder of Margin (100% - Platform %)
                    $platformPercentage = config('egi.fees.platform_margin_percentage', 10.0) / 100;
                    $companyShareOnMargin = $marginApplied * (1.0 - $platformPercentage);
                    $amountEur = $baseValue + $companyShareOnMargin;
                    $amountCents = (int) round($amountEur * 100);
                    $percentage = $totalAmountEur > 0 ? ($amountEur / $totalAmountEur) * 100 : 0;

                    $this->logger->info('Commodity Split: Calculated Company Share', [
                        'role' => $wallet->platform_role,
                        'cost' => $baseValue,
                        'margin_share' => $companyShareOnMargin,
                        'total' => $amountEur
                    ]);
                } else {
                    // Other wallets (EPP, Frangette, etc.) - EXEMPT/ZERO for Commodities?
                    $amountEur = 0;
                    $amountCents = 0;
                    $percentage = 0;
                }
            } else {
                // --- STANDARD LOGIC ---
                // Calculate amount in cents
                if ($index === $activeWallets->count() - 1) {
                    // Last wallet gets remainder to ensure exact total
                    $amountCents = (int) round(($totalAmountCents * $percentage) / 100);
                    // Simplify: just calculate standard. Remainder logic is tricky with dynamic EPP.
                    // If we want perfection we sum up previous and subtract.
                } else {
                    $amountCents = (int) round(($totalAmountCents * $percentage) / 100);
                    $distributedCents += $amountCents;
                }
                
                $amountEur = $amountCents / 100;
            }

            $distributions[] = [
                'wallet_id' => $wallet->id,
                'wallet_address' => $wallet->wallet,
                'stripe_account_id' => $wallet->user?->stripe_account_id ?? $wallet->stripe_account_id,
                'user_id' => $wallet->user_id,
                'platform_role' => $wallet->platform_role,
                'percentage' => $percentage, // Store effective percentage
                'amount_eur' => $amountEur,
                'amount_cents' => $amountCents,
            ];
        }

        $this->logger->info('Distributions calculated', [
            'distributions_count' => count($distributions),
            'total_amount_eur' => $totalAmountEur,
            'total_distributed_eur' => array_sum(array_column($distributions, 'amount_eur')),
        ]);

        return $distributions;
    }

    /**
     * Validate all wallets have Stripe Connect accounts configured AND capabilities enabled
     *
     * Verifies:
     * - Stripe account ID exists
     * - Account has 'transfers' capability active (required for direct payments)
     * - Account has 'card_payments' capability active
     *
     * NOTE: Skips wallets with 0% royalty (amount_cents = 0) as they won't receive any payment
     *
     * @param array $distributions
     * @throws \Exception If any wallet missing Stripe account or capabilities
     */
    protected function validateStripeAccounts(array $distributions): void {
        $missingAccounts = [];
        $insufficientCapabilities = [];

        foreach ($distributions as $distribution) {
            // STRICT LOGIC: Skip wallets with 0% royalty or 0 amount - they won't receive any payment
            // User Feedback: "Se un Wallet ha 0 come royalty va saltato... nessun controllo sul id di stripe"
            $percentage = $distribution['percentage'] ?? 0;
            $amountCents = $distribution['amount_cents'] ?? 0;

            if ($percentage <= 0 || $amountCents <= 0) {
                // ... logging ...
                continue;
            }

            // PLATFORM WALLET EXEMPTION:
            // 'Natan' is the Platform itself. Funds allocated to Natan should REMAIN in the Platform Account.
            // We do NOT create a Transfer for Natan, so we do NOT need a Stripe Connect ID.
            if (($distribution['platform_role'] ?? '') === 'Natan') {
                 $this->logger->info('Skipping Stripe validation for Platform Wallet (Natan) - funds strictly retained', [
                    'wallet_id' => $distribution['wallet_id']
                ]);
                continue;
            }

            // Check if Stripe account ID exists
            if (empty($distribution['stripe_account_id'])) {
                $missingAccounts[] = [
                    'wallet_id' => $distribution['wallet_id'],
                    'platform_role' => $distribution['platform_role'],
                ];
                continue; // Skip capability check if no account
            }

            // Verify Stripe account capabilities (transfers + card_payments)
            try {
                $account = $this->stripeClient->accounts->retrieve($distribution['stripe_account_id']);
                $capabilities = $account->capabilities;

                $hasTransfers = isset($capabilities->transfers) && $capabilities->transfers === 'active';
                $hasCardPayments = isset($capabilities->card_payments) && $capabilities->card_payments === 'active';

                if (!$hasTransfers || !$hasCardPayments) {
                    $insufficientCapabilities[] = [
                        'wallet_id' => $distribution['wallet_id'],
                        'platform_role' => $distribution['platform_role'],
                        'stripe_account_id' => $distribution['stripe_account_id'],
                        'capabilities' => [
                            'transfers' => $capabilities->transfers ?? 'not_set',
                            'card_payments' => $capabilities->card_payments ?? 'not_set',
                        ],
                    ];
                }

                $this->logger->debug('Stripe account capabilities verified', [
                    'stripe_account_id' => $distribution['stripe_account_id'],
                    'wallet_id' => $distribution['wallet_id'],
                    'platform_role' => $distribution['platform_role'],
                    'transfers' => $capabilities->transfers ?? 'not_set',
                    'card_payments' => $capabilities->card_payments ?? 'not_set',
                ]);
            } catch (\Stripe\Exception\ApiErrorException $e) {
                $this->logger->error('Failed to verify Stripe account capabilities', [
                    'stripe_account_id' => $distribution['stripe_account_id'],
                    'wallet_id' => $distribution['wallet_id'],
                    'error' => $e->getMessage(),
                ]);

                // Treat API error as insufficient capability
                $insufficientCapabilities[] = [
                    'wallet_id' => $distribution['wallet_id'],
                    'platform_role' => $distribution['platform_role'],
                    'stripe_account_id' => $distribution['stripe_account_id'],
                    'error' => $e->getMessage(),
                ];
            }
        }

        // Error handling: Missing accounts
        if (!empty($missingAccounts)) {
            $this->errorManager->handle('MINT_MISSING_STRIPE_ACCOUNTS', [
                'missing_accounts' => json_encode($missingAccounts),
                'missing_count' => count($missingAccounts),
            ]);

            throw new \Exception(
                'Cannot process split payment: ' . count($missingAccounts) .
                    ' wallet(s) missing Stripe Connect account configuration'
            );
        }

        // Error handling: Insufficient capabilities
        if (!empty($insufficientCapabilities)) {
            $this->errorManager->handle('MINT_INSUFFICIENT_STRIPE_CAPABILITIES', [
                'insufficient_accounts' => json_encode($insufficientCapabilities),
                'insufficient_count' => count($insufficientCapabilities),
            ]);

            throw new \Exception(
                'Cannot process split payment: ' . count($insufficientCapabilities) .
                    ' Stripe account(s) missing required capabilities (transfers + card_payments). ' .
                    'Enable these capabilities in Stripe Dashboard → Connect → Accounts.'
            );
        }

        $this->logger->info('All Stripe accounts validated with required capabilities', [
            'accounts_validated' => count($distributions),
        ]);
    }

    /**
     * Execute transfers atomically with rollback on any failure
     *
     * @param string $paymentIntentId
     * @param array $distributions
     * @param array $metadata
     * @return array ['success' => bool, 'transfers' => array]
     * @throws \Exception If any transfer fails
     */
    protected function executeAtomicTransfers(
        string $paymentIntentId,
        array $distributions,
        array $metadata
    ): array {
        $executedTransfers = [];
        $success = false;

        DB::beginTransaction();

        try {
            foreach ($distributions as $distribution) {
                // PLATFORM WALLET EXEMPTION:
                // Skip transfer for Natan (Platform receives funds by NOT transferring them)
                if (($distribution['platform_role'] ?? '') === 'Natan') {
                    $this->logger->info('Skipping transfer creation for Natan (Platform Fee retained)', [
                        'wallet_id' => $distribution['wallet_id'],
                        'amount_eur' => $distribution['amount_eur']
                    ]);
                    
                    // Maintain array alignment with $distributions for createDistributionRecords
                    $executedTransfers[] = [
                        'transfer_id' => null, // No transfer ID (Retained)
                        'destination' => 'platform_retained',
                        'amount_cents' => $distribution['amount_cents'], // Correct amount
                        'amount_eur' => $distribution['amount_eur'],
                        'wallet_id' => $distribution['wallet_id'],
                        'platform_role' => $distribution['platform_role'],
                        'status' => 'succeeded', // Logically succeeded
                    ];
                    
                    continue; 
                }

                $transfer = $this->createStripeTransfer(
                    $paymentIntentId,
                    $distribution,
                    $metadata
                );

                $executedTransfers[] = [
                    'transfer_id' => $transfer->id,
                    'destination' => $transfer->destination,
                    'amount_cents' => $transfer->amount,
                    'amount_eur' => $distribution['amount_eur'],
                    'wallet_id' => $distribution['wallet_id'],
                    'platform_role' => $distribution['platform_role'],
                    'status' => 'succeeded',
                ];

                $this->logger->info('Transfer executed successfully', [
                    'transfer_id' => $transfer->id,
                    'destination_account' => $transfer->destination,
                    'amount_cents' => $transfer->amount,
                    'wallet_id' => $distribution['wallet_id'],
                    'platform_role' => $distribution['platform_role'],
                ]);
            }

            // All transfers succeeded
            DB::commit();
            $success = true;

            $this->logger->info('All transfers committed successfully', [
                'payment_intent_id' => $paymentIntentId,
                'transfers_count' => count($executedTransfers),
            ]);
        } catch (ApiErrorException $e) {
            DB::rollBack();

            $this->logger->error('Stripe transfer failed - rolling back', [
                'payment_intent_id' => $paymentIntentId,
                'executed_transfers_count' => count($executedTransfers),
                'error_type' => $e->getError()?->type,
                'error_message' => $e->getMessage(),
            ]);

            // Reverse all executed transfers
            $this->reverseExecutedTransfers($executedTransfers, $paymentIntentId);

            throw new \Exception(
                'Split payment failed: ' . $e->getMessage() .
                    '. All transfers have been reversed.',
                0,
                $e
            );
        } catch (\Throwable $e) {
            DB::rollBack();

            $this->logger->error('Unexpected error during transfers - rolling back', [
                'payment_intent_id' => $paymentIntentId,
                'executed_transfers_count' => count($executedTransfers),
                'error_message' => $e->getMessage(),
            ]);

            // Reverse all executed transfers
            $this->reverseExecutedTransfers($executedTransfers, $paymentIntentId);

            throw new \Exception(
                'Split payment failed unexpectedly: ' . $e->getMessage() .
                    '. All transfers have been reversed.',
                0,
                $e
            );
        }

        return [
            'success' => $success,
            'transfers' => $executedTransfers,
        ];
    }

    /**
     * Create a single Stripe Transfer to a Connected Account
     *
     * @param string $paymentIntentId
     * @param array $distribution
     * @param array $metadata
     * @return \Stripe\Transfer
     * @throws ApiErrorException
     */
    protected function createStripeTransfer(
        string $paymentIntentId,
        array $distribution,
        array $metadata
    ): \Stripe\Transfer {
        // Retrieve the charge ID from the PaymentIntent
        // Stripe Transfer requires charge ID (ch_xxx), not PaymentIntent ID (pi_xxx)
        $paymentIntent = $this->stripeClient->paymentIntents->retrieve($paymentIntentId);

        if (empty($paymentIntent->charges->data)) {
            throw new \Exception("No charge found for PaymentIntent {$paymentIntentId}");
        }

        $chargeId = $paymentIntent->charges->data[0]->id;

        $transferMetadata = array_merge($metadata, [
            'wallet_id' => $distribution['wallet_id'],
            'platform_role' => $distribution['platform_role'] ?? 'unknown',
            'percentage' => $distribution['percentage'],
            'payment_intent_id' => $paymentIntentId,
            'charge_id' => $chargeId,
        ]);

        return $this->stripeClient->transfers->create([
            'amount' => $distribution['amount_cents'],
            'currency' => 'eur',
            'destination' => $distribution['stripe_account_id'],
            'source_transaction' => $chargeId, // Use charge ID, not PaymentIntent ID
            'description' => sprintf(
                'EGI Mint - %s share (%s%%)',
                $distribution['platform_role'] ?? 'Creator',
                $distribution['percentage']
            ),
            'metadata' => $transferMetadata,
        ]);
    }

    /**
     * Reverse all executed transfers using Stripe Reversals API
     *
     * @param array $executedTransfers
     * @param string $paymentIntentId
     */
    protected function reverseExecutedTransfers(array $executedTransfers, string $paymentIntentId): void {
        if (empty($executedTransfers)) {
            return;
        }

        $this->logger->warning('Reversing executed transfers', [
            'payment_intent_id' => $paymentIntentId,
            'transfers_to_reverse' => count($executedTransfers),
        ]);

        foreach ($executedTransfers as $transfer) {
            try {
                $reversal = $this->stripeClient->transfers->createReversal(
                    $transfer['transfer_id'],
                    [
                        'description' => 'Split payment failed - automatic reversal',
                        'metadata' => [
                            'payment_intent_id' => $paymentIntentId,
                            'reason' => 'split_payment_failure',
                        ],
                    ]
                );

                $this->logger->info('Transfer reversed successfully', [
                    'transfer_id' => $transfer['transfer_id'],
                    'reversal_id' => $reversal->id,
                    'amount_cents' => $reversal->amount,
                ]);
            } catch (ApiErrorException $e) {
                $this->logger->error('Failed to reverse transfer', [
                    'transfer_id' => $transfer['transfer_id'],
                    'error_message' => $e->getMessage(),
                ]);

                // Continue reversing other transfers even if one fails
                $this->errorManager->handle('STRIPE_TRANSFER_REVERSAL_FAILED', [
                    'transfer_id' => $transfer['transfer_id'],
                    'payment_intent_id' => $paymentIntentId,
                    'error_message' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Validate payment processing consents for all wallet owners (GDPR)
     *
     * NOTE: Skips wallets with 0% royalty as they won't receive any payment
     *
     * @param LaravelCollection<Wallet> $wallets
     * @throws \Exception If any wallet owner lacks required consent
     *
     * @privacy-safe Verifies consent before processing financial transactions
     */
    protected function validateWalletOwnersConsents(LaravelCollection $wallets): void {
        $missingConsents = [];

        foreach ($wallets as $wallet) {
            // Skip wallets with 0% royalty - they won't receive any payment
            if (($wallet->royalty_mint ?? 0) <= 0) {
                $this->logger->debug('Skipping consent check for 0% royalty wallet', [
                    'wallet_id' => $wallet->id,
                    'platform_role' => $wallet->platform_role,
                    'royalty_mint' => $wallet->royalty_mint ?? 0,
                ]);
                continue;
            }

            // Skip wallets without associated users (anonymous/platform wallets)
            if (!$wallet->user_id) {
                $this->logger->info('Skipping consent check for anonymous wallet', [
                    'wallet_id' => $wallet->id,
                    'platform_role' => $wallet->platform_role,
                ]);
                continue;
            }

            $user = $wallet->user;

            if (!$user) {
                $this->logger->warning('Wallet has user_id but user not found', [
                    'wallet_id' => $wallet->id,
                    'user_id' => $wallet->user_id,
                ]);
                continue;
            }

            // Check payment processing consent
            if (!$this->consentService->hasConsent($user, 'allow-payment-processing')) {
                $missingConsents[] = [
                    'user_id' => $user->id,
                    'wallet_id' => $wallet->id,
                    'platform_role' => $wallet->platform_role,
                ];
            }
        }

        if (!empty($missingConsents)) {
            $this->errorManager->handle('SPLIT_PAYMENT_MISSING_CONSENTS', [
                'missing_consents' => json_encode($missingConsents), // Serialize array for UEM
                'missing_count' => count($missingConsents),
                'user_ids' => json_encode(array_column($missingConsents, 'user_id')), // Serialize array
                'wallet_ids' => json_encode(array_column($missingConsents, 'wallet_id')), // Serialize array
            ]);

            throw new \Exception(
                'Cannot process split payment: ' . count($missingConsents) .
                    ' wallet owner(s) missing payment processing consent (GDPR compliance)'
            );
        }

        $this->logger->info('All wallet owners consents validated', [
            'wallets_checked' => $wallets->count(),
            'consents_valid' => true,
        ]);
    }

    /**
     * Log GDPR audit trail for payment distribution to multiple wallets
     *
     * @param int $buyerUserId User who made the payment
     * @param array $distributions Calculated distributions
     * @param array $transfers Executed Stripe transfers
     * @param array $metadata Additional payment metadata
     *
     * @privacy-safe Logs payment reception for each wallet owner with full audit trail
     */
    protected function logPaymentDistributionAuditTrail(
        int $buyerUserId,
        array $distributions,
        array $transfers,
        array $metadata
    ): void {
        $egiId = $metadata['egi_id'] ?? null;

        // Log audit trail for EACH wallet owner receiving payment
        foreach ($distributions as $index => $distribution) {
            $userId = $distribution['user_id'];

            // Skip if no user associated (anonymous/platform wallets)
            if (!$userId) {
                $this->logger->info('Skipping audit log for anonymous wallet distribution', [
                    'wallet_id' => $distribution['wallet_id'],
                    'platform_role' => $distribution['platform_role'],
                    'amount_eur' => $distribution['amount_eur'],
                ]);
                continue;
            }

            $user = User::find($userId);

            if (!$user) {
                $this->logger->warning('User not found for audit trail', [
                    'user_id' => $userId,
                    'wallet_id' => $distribution['wallet_id'],
                ]);
                continue;
            }

            $transfer = $transfers[$index] ?? null;

            // GDPR: Log payment reception for wallet owner
            $this->auditService->logUserAction(
                $user,
                'payment_received_split',
                [
                    'egi_id' => $egiId,
                    'buyer_user_id' => $buyerUserId,
                    'amount_eur' => $distribution['amount_eur'],
                    'percentage' => $distribution['percentage'],
                    'platform_role' => $distribution['platform_role'],
                    'wallet_id' => $distribution['wallet_id'],
                    'transfer_id' => $transfer['transfer_id'] ?? null,
                    'stripe_destination' => $transfer['destination'] ?? null,
                    'payment_method' => 'stripe_split',
                ],
                GdprActivityCategory::WALLET_MANAGEMENT
            );

            $this->logger->info('GDPR audit trail logged for wallet owner', [
                'user_id' => $userId,
                'wallet_id' => $distribution['wallet_id'],
                'amount_eur' => $distribution['amount_eur'],
                'platform_role' => $distribution['platform_role'],
            ]);
        }
    }

    /**
     * Create PaymentDistribution records for accounting and financial tracking
     *
     * Integrates with PaymentDistributionService to create database records
     * for each wallet distribution, enabling financial reports and tracking.
     *
     * @param string $paymentIntentId Stripe PaymentIntent ID
     * @param Collection $collection Collection being paid for
     * @param array $distributions Calculated distributions
     * @param array $transfers Executed Stripe transfers
     * @param array $metadata Additional metadata (egi_id, buyer_user_id)
     * @return array Created distribution records
     */
    protected function createDistributionRecords(
        string $paymentIntentId,
        Collection $collection,
        array $distributions,
        array $transfers,
        array $metadata
    ): array {
        $egiId = $metadata['egi_id'] ?? null;
        $egiBlockchainId = $metadata['egi_blockchain_id'] ?? null; // <--- EXTRACT PASSED ID
        $buyerUserId = $metadata['buyer_user_id'] ?? null;

        $this->logger->info('Creating PaymentDistribution records', [
            'payment_intent_id' => $paymentIntentId,
            'collection_id' => $collection->id,
            'egi_id' => $egiId,
            'distributions_count' => count($distributions),
        ]);

        $records = [];

        try {
            foreach ($distributions as $index => $distribution) {
                $transfer = $transfers[$index] ?? null;
                $wallet = Wallet::find($distribution['wallet_id']);

                if (!$wallet) {
                    $this->logger->warning('Wallet not found for distribution record', [
                        'wallet_id' => $distribution['wallet_id'],
                    ]);
                    continue;
                }

                // Create distribution record via PaymentDistributionService
                // Note: This uses the existing PaymentDistributionService infrastructure
                // which creates payment_distributions records with proper GDPR tracking
                $distributionData = [
                    'wallet_id' => $wallet->id,
                    'user_id' => $wallet->user_id,
                    'collection_id' => $collection->id,
                    'egi_id' => $egiId, // Explicitly save EGI ID if present
                    'egi_blockchain_id' => $egiBlockchainId, // <--- SAVE LINK TO BLOCKCHAIN RECORD
                    'amount_eur' => $distribution['amount_eur'],
                    'percentage' => $distribution['percentage'],
                    'platform_role' => $distribution['platform_role'],
                    'payment_intent_id' => $paymentIntentId,
                    'stripe_transfer_id' => $transfer['transfer_id'] ?? null,
                    'stripe_destination' => $transfer['destination'] ?? null,
                    'transfer_status' => $transfer['status'] ?? 'unknown',
                    'buyer_user_id' => $buyerUserId,
                    'payment_method' => 'stripe_split',
                    'status' => ($transfer['status'] ?? '') === 'succeeded' ? 'completed' : 'failed',
                    'processed_at' => now(),
                ];

                // Create the record using raw PaymentDistribution model
                // (PaymentDistributionService methods expect Reservation, we have direct payment)
                $record = \App\Models\PaymentDistribution::create($distributionData);

                $records[] = $record->toArray();

                $this->logger->debug('PaymentDistribution record created', [
                    'id' => $record->id,
                    'wallet_id' => $wallet->id,
                    'amount_eur' => $distribution['amount_eur'],
                ]);
            }

            $this->logger->info('PaymentDistribution records created successfully', [
                'payment_intent_id' => $paymentIntentId,
                'records_count' => count($records),
            ]);

            return $records;
        } catch (\Exception $e) {
            // Log error but don't fail the split payment
            // (transfers already succeeded, distributions are for accounting only)
            $this->logger->error('Failed to create PaymentDistribution records', [
                'payment_intent_id' => $paymentIntentId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Return empty array to indicate failure without blocking payment
            return [];
        }
    }

    /**
     * 🚀 MiCA-SAFE: Create DIRECT PaymentIntents to Connected Accounts
     *
     * Uses Stripe `transfer_data[destination]` pattern
     * Funds go DIRECTLY to destination accounts, NO platform transit
     *
     * @param array $distributions
     * @param \App\Services\Payment\PaymentRequest $request
     * @param array $metadata
     * @return array Array of \Stripe\PaymentIntent objects
     * @throws \Exception
     */
    protected function createDirectPaymentIntents(
        array $distributions,
        \App\DataTransferObjects\Payment\PaymentRequest $request,
        array $metadata
    ): array {
        $paymentIntents = [];
        $currency = strtolower($request->currency);
        
        // Dynamically retrieve Platform Stripe Account ID to prevent "Source matches Destination" error
        // This handles cases where a wallet (e.g., Natan/Company) has the same Stripe ID as the Platform
        try {
            $platformAccount = $this->stripeClient->accounts->retrieve();
            $platformAccountId = $platformAccount->id;
        } catch(\Exception $e) {
            $this->logger->error('STRIPE_PLATFORM_ID_FETCH_FAILED', ['error' => $e->getMessage()]);
            // Fallback: Proceed without filter, relying on Stripe validation (risk of error remains but logged)
            $platformAccountId = null; 
        }

        foreach ($distributions as $distribution) {
            $amountCents = $distribution['amount_cents'];
            $destinationAccount = $distribution['stripe_account_id'];
            $isNatan = $distribution['platform_role'] === 'Natan';

            // CORRECT COMPLIANCE: NO Application Fee applied by default logic.
            // Stripe Fees are handled by Stripe based on platform contract.
            $applicationFeeCents = 0;
            
            // CRITICAL FIX: IF DESTINATION IS PLATFORM, DO NOT TRANSFER (Money stays in platform)
            $isPlatformSelfTransfer = ($platformAccountId && $destinationAccount === $platformAccountId);

            $intentParams = [
                'amount' => $amountCents,
                'currency' => $currency,
                'description' => sprintf(
                    'EGI Mint #%s - %s share (%s%%)',
                    $request->egiId ?? 'unknown',
                    $distribution['platform_role'] ?? 'Creator',
                    $distribution['percentage']
                ),
                'metadata' => array_merge($metadata, [
                    'wallet_id' => $distribution['wallet_id'],
                    'platform_role' => $distribution['platform_role'] ?? 'unknown',
                    'percentage' => $distribution['percentage'],
                    'egi_id' => $request->egiId,
                    'collection_id' => $request->getMerchantContext()['collection_id'] ?? null,
                    'split_payment' => 'true',
                ]),
                'receipt_email' => $request->customerEmail,
                'confirmation_method' => 'automatic',
            ];
            
            // Only add transfer_data if NOT transferring to self
            if (!$isPlatformSelfTransfer) {
                $intentParams['transfer_data'] = [
                    'destination' => $destinationAccount,
                ];
            } else {
                $this->logger->info('Skipping transfer_data for Platform Self-Transfer', [
                    'account_id' => $destinationAccount,
                    'role' => $distribution['platform_role']
                ]);
            }

            // Aggiungi platform fee SOLO per pagamento a Natan AND NOT Self-Transfer
            // (You can't take a fee from yourself effectively in this flow, or semantics differ)
            if ($applicationFeeCents > 0 && !$isPlatformSelfTransfer) {
                $intentParams['application_fee_amount'] = $applicationFeeCents;
                $intentParams['metadata']['application_fee_cents'] = $applicationFeeCents;
                $intentParams['metadata']['application_fee_percentage'] = '2';
            }

            // Auto-confirm in sandbox
            if (config('algorand.payments.stripe.auto_confirm', true)) {
                $intentParams['confirm'] = true;

                $sandboxPM = config('algorand.payments.stripe.sandbox_payment_method');
                if ($sandboxPM) {
                    $intentParams['payment_method'] = $sandboxPM;
                }

                // Return URL for redirect payment methods (only valid when confirm=true)
                if ($request->successUrl) {
                    $intentParams['return_url'] = $request->successUrl;
                }
            }

            // Create PaymentIntent with destination transfer
            $paymentIntent = $this->stripeClient->paymentIntents->create($intentParams);

            $paymentIntents[] = $paymentIntent;

            $this->logger->info('MiCA-SAFE: Direct payment created', [
                'payment_intent_id' => $paymentIntent->id,
                'destination_account' => $destinationAccount,
                'amount_cents' => $amountCents,
                'application_fee_cents' => $applicationFeeCents,
                'wallet_id' => $distribution['wallet_id'],
                'platform_role' => $distribution['platform_role'],
                'is_self_transfer' => $isPlatformSelfTransfer
            ]);
        }

        return $paymentIntents;
    }

    protected function createDistributionRecordsFromIntents(
        Collection $collection,
        array $distributions,
        array $paymentIntents,
        array $metadata
    ): array {
        $records = [];

        foreach ($distributions as $index => $distribution) {
            $intent = $paymentIntents[$index] ?? null;
            if (!$intent) continue;

            // Map platform_role → UserTypeEnum (platform_role è la SOURCE OF TRUTH per i ruoli wallet)
            $userType = $this->mapPlatformRoleToUserType($distribution['platform_role']);

            $records[] = \App\Models\PaymentDistribution::create([
                'wallet_id' => $distribution['wallet_id'],
                'user_id' => $distribution['user_id'],
                'user_type' => $userType, // Mapped from platform_role
                'collection_id' => $collection->id,
                'egi_id' => $metadata['egi_id'] ?? null,
                'amount_eur' => $distribution['amount_eur'],
                'percentage' => $distribution['percentage'],
                'platform_role' => $distribution['platform_role'],
                'payment_intent_id' => $intent->id,
                'stripe_transfer_id' => null,
                'stripe_destination' => $distribution['stripe_account_id'],
                'transfer_status' => $intent->status,
                'buyer_user_id' => $metadata['buyer_user_id'] ?? null,
                'payment_method' => 'stripe_direct',
                'status' => 'pending',
                'processed_at' => now(),
                'exchange_rate' => 1.0, // EUR → EUR (no conversion for split payments)
            ]);

            $this->logger->debug('PaymentDistribution created with mapped user_type', [
                'user_id' => $distribution['user_id'],
                'platform_role' => $distribution['platform_role'],
                'user_type' => $userType->value,
            ]);
        }

        return $records;
    }

    /**
     * Map platform_role (wallet role) to UserTypeEnum
     *
     * Platform roles are wallet-specific (Natan, Frangette, Creator, EPP)
     * UserTypeEnum are user account types
     *
     * This mapping allows PaymentDistribution to use the correct enum value
     *
     * @param string|null $platformRole
     * @return \App\Enums\PaymentDistribution\UserTypeEnum
     */
    protected function mapPlatformRoleToUserType(?string $platformRole): \App\Enums\PaymentDistribution\UserTypeEnum {
        return match ($platformRole) {
            'Natan' => \App\Enums\PaymentDistribution\UserTypeEnum::NATAN,
            'Frangette' => \App\Enums\PaymentDistribution\UserTypeEnum::FRANGETTE,
            'Creator' => \App\Enums\PaymentDistribution\UserTypeEnum::CREATOR,
            'EPP' => \App\Enums\PaymentDistribution\UserTypeEnum::EPP,
            'Commissioner' => \App\Enums\PaymentDistribution\UserTypeEnum::COMMISSIONER,
            'Collector' => \App\Enums\PaymentDistribution\UserTypeEnum::COLLECTOR,
            default => \App\Enums\PaymentDistribution\UserTypeEnum::COLLECTOR, // Safe fallback
        };
    }

    protected function logDirectPaymentsAuditTrail(
        int $buyerUserId,
        array $distributions,
        array $paymentIntents,
        array $metadata
    ): void {
        foreach ($distributions as $index => $distribution) {
            if (!$distribution['user_id']) continue;

            $user = User::find($distribution['user_id']);
            if (!$user) continue;

            $intent = $paymentIntents[$index] ?? null;

            $this->auditService->logUserAction(
                $user,
                'payment_received_direct',
                [
                    'egi_id' => $metadata['egi_id'] ?? null,
                    'buyer_user_id' => $buyerUserId,
                    'amount_eur' => $distribution['amount_eur'],
                    'percentage' => $distribution['percentage'],
                    'platform_role' => $distribution['platform_role'],
                    'wallet_id' => $distribution['wallet_id'],
                    'payment_intent_id' => $intent?->id,
                    'stripe_destination' => $distribution['stripe_account_id'],
                    'payment_method' => 'stripe_direct',
                ],
                GdprActivityCategory::WALLET_MANAGEMENT
            );
        }
    }
}
