<?php

namespace App\Services\Payment;

use App\Contracts\PaymentServiceInterface;
use App\DataTransferObjects\Payment\PaymentRequest;
use App\DataTransferObjects\Payment\PaymentResult;
use App\DataTransferObjects\Payment\RefundResult;
use App\DataTransferObjects\Payment\PaymentStatus;
use App\Enums\Gdpr\GdprActivityCategory;
use App\Models\Collection;
use App\Services\Gdpr\AuditLogService;
use App\Services\Gdpr\ConsentService;
use App\Services\Payment\StripePaymentSplitService;
use Illuminate\Support\Arr;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;
use Stripe\StripeClient;
use Stripe\Webhook;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * @Oracode Service: Stripe Payment Service (Sandbox/Live)
 * 🎯 Purpose: Integrate Stripe PSP for real payments (sandbox-ready)
 * 🛡️ Compliance: MiCA-safe (FIAT only), GDPR logging, webhook verification
 */
class StripeRealPaymentService implements PaymentServiceInterface {
    private UltraLogManager $logger;
    private ErrorManagerInterface $errorManager;
    private AuditLogService $auditService;
    private ConsentService $consentService;
    private StripeClient $client;
    private StripePaymentSplitService $splitService;
    private array $config;
    private bool $autoConfirm;
    private ?string $sandboxPaymentMethod;
    private array $supportedCurrencies;

    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        AuditLogService $auditService,
        ConsentService $consentService,
        StripePaymentSplitService $splitService
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->auditService = $auditService;
        $this->consentService = $consentService;
        $this->splitService = $splitService;

        $this->config = config('algorand.payments.stripe', []);
        $secretKey = Arr::get($this->config, 'secret_key');

        if (empty($secretKey)) {
            throw new \RuntimeException('Stripe secret key is not configured');
        }

        Stripe::setAppInfo(
            'FlorenceEGI',
            '1.0.0',
            'https://florenceegi.it'
        );

        $this->client = new StripeClient([
            'api_key' => $secretKey,
        ]);

        $this->autoConfirm = (bool) Arr::get($this->config, 'auto_confirm', true);
        $this->sandboxPaymentMethod = Arr::get($this->config, 'sandbox_payment_method');
        $this->supportedCurrencies = array_map('strtoupper', Arr::get($this->config, 'supported_currencies', ['EUR']));
    }

    public function processPayment(PaymentRequest $request): PaymentResult {
        try {
            $merchantContext = $request->getMerchantContext();
            $stripeAccountId = $merchantContext['stripe_account_id'] ?? null;
            $collectionId = $merchantContext['collection_id'] ?? null;

            // Determine payment strategy: Split vs Single vs Platform Direct
            $requiresSplit = !empty($collectionId) && $request->egiId !== null;
            $isConnectPayment = !empty($stripeAccountId) && !$requiresSplit;

            $metadata = $request->getPspMetadata();
            $amountCents = $request->getAmountInCents();
            $currency = strtolower($request->currency);

            // MiCA-SAFE ARCHITECTURE:
            // SPLIT PAYMENT: Create DIRECT charges to Connected Accounts (NO platform transit)
            // SINGLE CONNECT: Use specific Connected Account
            // PLATFORM DIRECT: Use platform account (Egili, etc.)

            if ($requiresSplit) {
                // WEBHOOK-ONLY SPLIT: All split logic moved to webhook for consistency
                // This ensures split works for both auto-confirm (sandbox) and Checkout Session (production)

                // P0 FIX: Ensure metadata includes requires_split for Webhook
                $dataWithSplitMetadata = array_merge($metadata, [
                    'requires_split' => 'true',
                    'collection_id' => $collectionId
                ]);

                // Create single PaymentIntent - split happens ONLY in webhook
                return $this->createSinglePaymentIntent($request, $dataWithSplitMetadata, $isConnectPayment, $stripeAccountId);
            }

            // Use Stripe Checkout for real user interaction (card input)
            if (!$this->autoConfirm) {
                return $this->createCheckoutSession($request, $metadata, $isConnectPayment, $stripeAccountId);
            }

            // Auto-confirm mode (sandbox testing without user interaction)
            $params = [
                'amount' => $amountCents,
                'currency' => $currency,
                'description' => $requiresSplit
                    ? sprintf('EGI Mint #%s (Multi-wallet Distribution)', $request->egiId)
                    : sprintf('EGI Mint #%s', $request->egiId ?? 'unknown'),
                'metadata' => array_merge($metadata, [
                    'requires_split' => $requiresSplit ? 'true' : 'false',
                    'collection_id' => $collectionId,
                ]),
                'receipt_email' => $request->customerEmail,
                'confirmation_method' => 'automatic',
                'confirm' => true,
            ];

            if ($this->isSandbox() && $this->sandboxPaymentMethod) {
                $params['payment_method'] = $this->sandboxPaymentMethod;
            }

            // Single merchant or platform direct payment
            $createOptions = $isConnectPayment ? ['stripe_account' => $stripeAccountId] : [];
            $paymentIntent = $this->client->paymentIntents->create($params, $createOptions);

            $this->logger->info('Stripe payment intent created (auto-confirm)', [
                'payment_intent_id' => $paymentIntent->id,
                'status' => $paymentIntent->status,
                'amount' => $paymentIntent->amount,
                'currency' => $paymentIntent->currency,
                'auto_confirm' => $this->autoConfirm,
                'sandbox' => $this->isSandbox(),
                'is_connect_payment' => $isConnectPayment,
                'connected_account_id' => $stripeAccountId,
            ]);

            if ($paymentIntent->status !== 'succeeded') {
                throw new \RuntimeException(
                    sprintf('Stripe payment failed with status %s', $paymentIntent->status)
                );
            }

            // Split payment is now handled BEFORE this point via createDirectSplitPayments()
            // This code path is only for single merchant or platform direct payments

            $charge = $paymentIntent->charges->data[0] ?? null;
            $receiptUrl = $charge->receipt_url ?? null;

            $metadataResponse = array_merge($metadata, [
                'payment_intent_status' => $paymentIntent->status,
                'client_secret' => $paymentIntent->client_secret,
                'receipt_url' => $receiptUrl,
                'merchant_psp' => $isConnectPayment ? array_filter([
                    'provider' => 'stripe',
                    'stripe_account_id' => $stripeAccountId,
                ]) : [
                    'provider' => 'stripe',
                    'account_type' => 'platform_direct',
                ],
            ]);

            $result = new PaymentResult(
                success: true,
                paymentId: $paymentIntent->id,
                amount: $request->amount,
                currency: strtoupper($request->currency),
                status: 'completed',
                metadata: $metadataResponse,
                receiptUrl: $receiptUrl,
                processedAt: new \DateTimeImmutable()
            );

            $this->logger->info('Stripe payment completed successfully', [
                'payment_intent_id' => $paymentIntent->id,
                'charge_id' => $charge?->id,
                'receipt_url' => $receiptUrl,
            ]);

            return $result;
        } catch (ApiErrorException $exception) {
            // ULM: Log Stripe API error for debugging
            $this->logger->error('Stripe API error', [
                'error_type' => $exception->getError()?->type,
                'error_message' => $exception->getMessage(),
                'error_code' => $exception->getStripeCode(),
            ]);

            // Re-throw for Controller to handle with UEM
            throw $exception;
        } catch (\Throwable $exception) {
            $this->errorManager->handle('STRIPE_PAYMENT_UNEXPECTED_ERROR', [
                'error_message' => $exception->getMessage(),
            ], $exception);

            return PaymentResult::failure(
                paymentId: '',
                errorMessage: 'Stripe payment processing failed',
                errorCode: 'STRIPE_UNEXPECTED_ERROR'
            );
        }
    }

    public function processPaymentWebhook(array $payload, array $headers = []): array {
        $type = $payload['type'] ?? 'unknown';
        $object = $payload['data']['object'] ?? [];

        $this->logger->info('Processing Stripe webhook event', [
            'type' => $type,
            'payment_id' => $object['id'] ?? 'unknown',
        ]);

        // ULM DEBUG: Track Webhook Payload for Split Logic
        $this->logger->info('ULM DEBUG: Webhook Payload Received', [
            'type' => $type,
            'payment_id' => $object['id'] ?? 'unknown',
            'metadata_requires_split' => $object['metadata']['requires_split'] ?? 'MISSING',
            'metadata_collection_id' => $object['metadata']['collection_id'] ?? 'MISSING',
            'log_file' => 'upload.log' // Explicit marker
        ]);

        if ($type === 'payment_intent.succeeded') {
            $paymentId = $object['id'];
            $amount = ($object['amount'] ?? 0) / 100;
            $currency = strtoupper($object['currency'] ?? 'EUR');
            $metadata = $object['metadata'] ?? [];

            // 1. Fetch Charge to get Balance Transaction
            $chargeId = $object['charges']['data'][0]['id'] ?? null;
            $balanceTransactionId = $object['charges']['data'][0]['balance_transaction'] ?? null;
            $netAmount = $amount; // Fallback
            $stripeFee = 0.00;

            if ($balanceTransactionId) {
                try {
                    $bt = $this->client->balanceTransactions->retrieve($balanceTransactionId);
                    $netAmount = ($bt->net ?? 0) / 100;
                    $stripeFee = ($bt->fee ?? 0) / 100;
                } catch (\Exception $e) {
                    $this->logger->warning('Failed to fetch balance transaction for fees', [
                        'payment_id' => $paymentId,
                        'bt_id' => $balanceTransactionId
                    ]);
                    // Fallback estimate: 2.9% + 0.25 (approximate if API fails)
                    $stripeFee = ($amount * 0.029) + 0.25;
                    $netAmount = $amount - $stripeFee;
                }
            }

            // 2. Calculate Platform Fee (0.5% of GROSS)
            $platformFeeConfig = config('egi.fees.platform_fee_percentage', 0.5);
            $platformFee = $amount * ($platformFeeConfig / 100);

            // 3. Calculate Distributable Amount
            // "distribuzione solo del netto dalla fee di stripe" implies: Net from Stripe - Platform Fee
            $distributableAmount = max(0, $netAmount - $platformFee);

            // Check if this payment requires split distribution
            if (isset($metadata['requires_split']) && $metadata['requires_split'] === 'true') {
                $collectionId = $metadata['collection_id'] ?? null;

                if ($collectionId) {
                    try {
                        $collection = Collection::findOrFail($collectionId);

                        // Pass NET details to metadata for split service
                        $metadata['stripe_fee'] = $stripeFee;
                        $metadata['platform_fee'] = $platformFee;
                        $metadata['net_available_stripe'] = $netAmount;

                        // Execute split payment logic with DISTRIBUTABLE AMOUNT
                        $this->splitService->splitPaymentToWallets(
                            $paymentId,
                            $collection,
                            $distributableAmount, // <-- CRITICAL: Distributing only what remains
                            $metadata
                        );

                        $this->logger->info('Split payment distribution triggered via webhook (NET)', [
                            'payment_id' => $paymentId,
                            'gross' => $amount,
                            'stripe_fee' => $stripeFee,
                            'platform_fee' => $platformFee,
                            'distributable' => $distributableAmount
                        ]);
                    } catch (\Exception $e) {
                        // UEM: Critical error but non-blocking for the webhook response
                        // This notifies devs/slack according to configuration
                        $this->errorManager->handle('STRIPE_WEBHOOK_SPLIT_FAILED', [
                            'payment_id' => $paymentId,
                            'collection_id' => $collectionId,
                            'amount' => $amount,
                            'distributable' => $distributableAmount,
                            'stripe_fee' => $stripeFee,
                            'platform_fee' => $platformFee,
                            'error_message' => $e->getMessage()
                        ], $e);
                    }
                }
            }

            return [
                'success' => true,
                'payment_id' => $paymentId,
                'status' => 'succeeded',
                'amount' => $amount,
                'currency' => $currency,
                'metadata' => $metadata
            ];
        }

        return [
            'success' => true,
            'status' => 'ignored_event_type'
        ];
    }

    public function verifyWebhook(array $payload): bool {
        try {
            $secret = Arr::get($this->config, 'webhook_secret');
            $body = $payload['body'] ?? '';
            $signature = Arr::get($payload, 'signature')
                ?? Arr::get($payload, 'headers.stripe-signature.0');

            if (empty($secret) || empty($body) || empty($signature)) {
                $this->logger->warning('Stripe webhook verification skipped - missing data');
                return false;
            }

            Webhook::constructEvent(
                $body,
                $signature,
                $secret
            );

            return true;
        } catch (\UnexpectedValueException | \Stripe\Exception\SignatureVerificationException $exception) {
            $this->errorManager->handle('STRIPE_WEBHOOK_VERIFICATION_FAILED', [
                'error_message' => $exception->getMessage(),
            ], $exception);

            return false;
        }
    }

    public function refundPayment(string $paymentId): RefundResult {
        try {
            $refund = $this->client->refunds->create([
                'payment_intent' => $paymentId,
            ]);

            $amount = ($refund->amount ?? 0) / 100;
            $currency = strtoupper($refund->currency ?? 'EUR');

            // P0 FIX: Reverse split transfers if they exist for this payment
            try {
                $this->reverseTransfersForPayment($paymentId);
            } catch (\Exception $e) {
                $this->logger->warning('Failed to reverse transfers during refund', [
                    'payment_id' => $paymentId,
                    'error' => $e->getMessage()
                ]);
                // Continue with refund even if transfer reversal fails
            }

            return RefundResult::success(
                refundId: $refund->id,
                originalPaymentId: $paymentId,
                refundAmount: $amount,
                currency: $currency,
                reason: $refund->reason ?? 'requested_by_customer',
                metadata: [
                    'status' => $refund->status,
                ]
            );
        } catch (ApiErrorException $exception) {
            $this->errorManager->handle('STRIPE_REFUND_ERROR', [
                'payment_id' => $paymentId,
                'error_message' => $exception->getMessage(),
            ], $exception);

            return RefundResult::failure(
                refundId: '',
                originalPaymentId: $paymentId,
                errorMessage: $exception->getMessage(),
                errorCode: $exception->getStripeCode() ?? 'STRIPE_REFUND_ERROR'
            );
        }
    }

    public function getPaymentStatus(string $paymentId): PaymentStatus {
        try {
            $intent = $this->client->paymentIntents->retrieve($paymentId);
            $charge = $intent->charges->data[0] ?? null;

            $data = [
                'payment_id' => $intent->id,
                'status' => $intent->status,
                'amount' => ($intent->amount ?? 0) / 100,
                'currency' => strtoupper($intent->currency ?? 'EUR'),
                'is_paid' => $intent->status === 'succeeded',
                'metadata' => $intent->metadata ?? [],
                'created_at' => $intent->created ? date('c', $intent->created) : null,
                'paid_at' => $charge?->created ? date('c', $charge->created) : null,
                'receipt_url' => $charge?->receipt_url,
            ];

            if (!empty($intent->last_payment_error)) {
                $data['failure_reason'] = $intent->last_payment_error['message'] ?? null;
                $data['failure_code'] = $intent->last_payment_error['code'] ?? null;
            }

            if (!empty($charge?->refunds?->data)) {
                $data['refunds'] = collect($charge->refunds->data)->map(function ($refund) {
                    return [
                        'refund_id' => $refund->id,
                        'amount' => ($refund->amount ?? 0) / 100,
                        'currency' => strtoupper($refund->currency ?? 'EUR'),
                        'status' => $refund->status,
                        'created_at' => $refund->created ? date('c', $refund->created) : null,
                    ];
                })->toArray();
            }

            return PaymentStatus::fromArray($data);
        } catch (ApiErrorException $exception) {
            $this->errorManager->handle('STRIPE_STATUS_ERROR', [
                'payment_id' => $paymentId,
                'error_message' => $exception->getMessage(),
            ], $exception);

            return PaymentStatus::fromArray([
                'payment_id' => $paymentId,
                'status' => 'failed',
                'amount' => 0,
                'currency' => 'EUR',
                'is_paid' => false,
                'failure_reason' => $exception->getMessage(),
                'failure_code' => $exception->getStripeCode(),
            ]);
        }
    }

    public function getProviderName(): string {
        return 'stripe';
    }

    public function supportsCurrency(string $currency): bool {
        return in_array(strtoupper($currency), $this->supportedCurrencies, true);
    }

    public function getSupportedCurrencies(): array {
        return $this->supportedCurrencies;
    }

    private function isSandbox(): bool {
        return strtolower((string) Arr::get($this->config, 'mode', 'sandbox')) === 'sandbox';
    }

    /**
     * Create Stripe Checkout Session for real user interaction
     * Redirects user to Stripe-hosted payment page with card input
     *
     * @param PaymentRequest $request
     * @param array $metadata
     * @param bool $isConnectPayment
     * @param string|null $stripeAccountId
     * @return PaymentResult
     */
    private function createCheckoutSession(
        PaymentRequest $request,
        array $metadata,
        bool $isConnectPayment,
        ?string $stripeAccountId
    ): PaymentResult {
        $amountCents = $request->getAmountInCents();
        $currency = strtolower($request->currency);

        // P0 FIX: Ensure metadata includes requires_split (passed from caller)
        // If caller didn't merge it, we can't easily guess here, but processPayment logic should have handled it.
        // However, explicit safety check:
        // (No-op if already present)


        // Build line item description
        $description = $metadata['description'] ?? 'Acquisto Pacchetto AI';
        if (isset($metadata['egili_amount'])) {
            $description = sprintf('Acquisto Pacchetto AI (%s Egili accreditati)', number_format($metadata['egili_amount']));
        }

        $sessionParams = [
            'payment_method_types' => ['card'],
            'mode' => 'payment',
            'line_items' => [[
                'price_data' => [
                    'currency' => $currency,
                    'product_data' => [
                        'name' => $description,
                        'description' => 'FlorenceEGI - Piattaforma NFT',
                    ],
                    'unit_amount' => $amountCents,
                ],
                'quantity' => 1,
            ]],
            'customer_email' => $request->customerEmail,
            'metadata' => $metadata,
            'success_url' => $request->successUrl . (str_contains($request->successUrl, '?') ? '&' : '?') . 'session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => $request->cancelUrl ?? url()->previous(),
        ];

        // For Connect payments, add the connected account
        $createOptions = [];
        if ($isConnectPayment && $stripeAccountId) {
            $createOptions['stripe_account'] = $stripeAccountId;
        }

        $session = $this->client->checkout->sessions->create($sessionParams, $createOptions);

        $this->logger->info('Stripe Checkout session created', [
            'session_id' => $session->id,
            'amount' => $amountCents,
            'currency' => $currency,
            'checkout_url' => $session->url,
            'is_connect_payment' => $isConnectPayment,
            'connected_account_id' => $stripeAccountId,
        ]);

        // Return pending with redirect URL to Stripe Checkout
        return PaymentResult::pending(
            paymentId: $session->id,
            amount: $request->amount,
            currency: $request->currency,
            redirectUrl: $session->url,
            metadata: array_merge($metadata, [
                'checkout_session_id' => $session->id,
                'payment_status' => 'pending_checkout',
            ])
        );
    }

    /**
     * Create single PaymentIntent (extracted from processPayment for split payment fix)
     */
    private function createSinglePaymentIntent(
        PaymentRequest $request,
        array $metadata,
        bool $isConnectPayment,
        ?string $stripeAccountId
    ): PaymentResult {
        $amountCents = round($request->amount * 100);
        $currency = strtolower($request->currency);

        $params = [
            'amount' => $amountCents,
            'currency' => $currency,
            'description' => sprintf('EGI Mint #%s', $request->egiId ?? 'unknown'),
            'metadata' => $metadata,
            'receipt_email' => $request->customerEmail,
            'confirm' => true,
            'automatic_payment_methods' => [
                'enabled' => 'true',
                'allow_redirects' => 'never',
            ],
        ];

        // ULM DEBUG: Log Single Payment Intent Creation
        $this->logger->info('ULM DEBUG: Creating Single Payment Intent', [
            'egi_id' => $request->egiId,
            'amount_cents' => $amountCents,
            'metadata_requires_split' => $metadata['requires_split'] ?? 'N/A',
            'metadata_collection_id' => $metadata['collection_id'] ?? 'N/A'
        ]);

        if ($this->isSandbox() && $this->sandboxPaymentMethod) {
            $params['payment_method'] = $this->sandboxPaymentMethod;
        }

        $createOptions = $isConnectPayment ? ['stripe_account' => $stripeAccountId] : [];
        $paymentIntent = $this->client->paymentIntents->create($params, $createOptions);

        if ($paymentIntent->status !== 'succeeded') {
            throw new \RuntimeException(
                sprintf('Stripe payment failed with status %s', $paymentIntent->status)
            );
        }

        $charge = $paymentIntent->charges->data[0] ?? null;
        $receiptUrl = $charge->receipt_url ?? null;

        return new PaymentResult(
            success: true,
            paymentId: $paymentIntent->id,
            amount: $request->amount,
            currency: strtoupper($request->currency),
            status: 'succeeded',
            metadata: array_merge($metadata, [
                'payment_intent_status' => $paymentIntent->status,
                'client_secret' => $paymentIntent->client_secret,
                'receipt_url' => $receiptUrl,
            ]),
            receiptUrl: $receiptUrl,
            processedAt: new \DateTimeImmutable()
        );
    }

    /**
     * Reverse all transfers for a given payment (for refunds)
     */
    /**
     * P0.5 GAP FIX: Reverse transfers with proper state tracking
     */
    private function reverseTransfersForPayment(string $paymentId): void {
        // Query payment distributions to find transfers to reverse
        $distributions = \App\Models\PaymentDistribution::where('payment_intent_id', $paymentId)
            ->where('transfer_id', '!=', null)
            ->where('status', 'completed') // Only reverse completed transfers
            ->get();

        if ($distributions->isEmpty()) {
            $this->logger->info('No completed transfers found to reverse for payment', [
                'payment_id' => $paymentId
            ]);
            return;
        }

        $this->logger->info('Starting transfer reversal for refund', [
            'payment_id' => $paymentId,
            'transfers_to_reverse' => $distributions->count()
        ]);

        foreach ($distributions as $distribution) {
            try {
                // Create idempotency key for reversal
                $reversalIdempotencyKey = $paymentId . '_reversal_' . $distribution->transfer_id;

                $reversal = $this->client->transfers->createReversal(
                    $distribution->transfer_id,
                    [
                        'description' => 'Refund - automatic transfer reversal',
                        'metadata' => [
                            'payment_intent_id' => $paymentId,
                            'reason' => 'refund_initiated',
                            'original_distribution_id' => $distribution->id
                        ],
                    ],
                    [
                        'idempotency_key' => $reversalIdempotencyKey  // CRITICAL: Prevents duplicate reversals
                    ]
                );

                // P0.5 FIX: Update distribution record with reversal info
                $distribution->update([
                    'status' => 'reversed',
                    'reversal_id' => $reversal->id,
                    'reversed_at' => now(),
                ]);

                $this->logger->info('Transfer reversed successfully', [
                    'transfer_id' => $distribution->transfer_id,
                    'reversal_id' => $reversal->id,
                    'payment_id' => $paymentId,
                    'distribution_id' => $distribution->id,
                    'amount_eur' => $distribution->amount_eur
                ]);
            } catch (\Exception $e) {
                // P0.5 FIX: Track reversal failure but don't block entire refund
                $distribution->update([
                    'status' => 'reversal_failed',
                    'failure_reason' => $e->getMessage(),
                    'retry_count' => ($distribution->retry_count ?? 0) + 1
                ]);

                $this->logger->error('Failed to reverse transfer - REQUIRES MANUAL INTERVENTION', [
                    'transfer_id' => $distribution->transfer_id,
                    'payment_id' => $paymentId,
                    'distribution_id' => $distribution->id,
                    'wallet_id' => $distribution->wallet_id,
                    'amount_eur' => $distribution->amount_eur,
                    'error' => $e->getMessage(),
                    'action_required' => 'Manual debt collection or account adjustment needed'
                ]);

                // Continue with other reversals - don't fail entire refund
                // but this creates a debt that needs manual handling
            }
        }

        // Summary log
        $reversalSummary = [
            'payment_id' => $paymentId,
            'total_distributions' => $distributions->count(),
            'successful_reversals' => $distributions->where('status', 'reversed')->count(),
            'failed_reversals' => $distributions->where('status', 'reversal_failed')->count(),
        ];

        if ($reversalSummary['failed_reversals'] > 0) {
            $this->logger->warning('Partial reversal failure - manual intervention required', $reversalSummary);
        } else {
            $this->logger->info('All transfers reversed successfully', $reversalSummary);
        }
    }
}
