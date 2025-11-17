<?php

declare(strict_types=1);

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Services\Payment\PaymentServiceFactory;
use App\Jobs\ProcessEgiMintingJob;
use App\Models\{EgiBlockchain, Reservation};
use App\Enums\Gdpr\GdprActivityCategory;
use App\Services\Gdpr\{AuditLogService, ConsentService};
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Illuminate\Http\{Request, JsonResponse, Response};
use Illuminate\Support\Facades\{Auth, DB, Queue};
use Illuminate\Validation\ValidationException;
use Exception;

/**
 * @Oracode Controller: PSP Webhook Handler with Security & Async Processing
 * 🎯 Purpose: Handle PSP webhooks (Stripe/PayPal) with signature verification and async job dispatching
 * 🧱 Core Logic: Security verification → Status update → Async minting job dispatch
 * 🛡️ MiCA-SAFE: Payment validation only, async blockchain operations
 *
 * @package App\Http\Controllers\Payment
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Blockchain Integration)
 * @date 2025-10-07
 * @purpose Secure PSP webhook processing with async job dispatching
 */
class PspWebhookController extends Controller {
    private UltraLogManager $logger;
    private ErrorManagerInterface $errorManager;
    private AuditLogService $auditService;
    private ConsentService $consentService;
    private PaymentServiceFactory $paymentFactory;

    /**
     * Constructor with GDPR/Ultra compliance
     *
     * @param UltraLogManager $logger Ultra logging manager
     * @param ErrorManagerInterface $errorManager Ultra error manager
     * @param AuditLogService $auditService GDPR audit service
     * @param ConsentService $consentService GDPR consent service
     * @param PaymentServiceFactory $paymentFactory PSP service factory
     * @privacy-safe All injected services handle GDPR compliance
     */
    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        AuditLogService $auditService,
        ConsentService $consentService,
        PaymentServiceFactory $paymentFactory
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->auditService = $auditService;
        $this->consentService = $consentService;
        $this->paymentFactory = $paymentFactory;
    }

    /**
     * Handle Stripe webhook events
     *
     * @param Request $request Stripe webhook payload
     * @return JsonResponse Webhook processing response
     * @throws ValidationException Invalid webhook payload
     * @throws Exception Webhook processing failed
     * @security Stripe signature verification mandatory
     * @gdpr-compliant Audit trail for payment events
     */
    public function handleStripeWebhook(Request $request): JsonResponse {
        return $this->processWebhook($request, 'stripe');
    }

    /**
     * Handle PayPal webhook events
     *
     * @param Request $request PayPal webhook payload
     * @return JsonResponse Webhook processing response
     * @throws ValidationException Invalid webhook payload
     * @throws Exception Webhook processing failed
     * @security PayPal signature verification mandatory
     * @gdpr-compliant Audit trail for payment events
     */
    public function handlePayPalWebhook(Request $request): JsonResponse {
        return $this->processWebhook($request, 'paypal');
    }

    /**
     * Process webhook with unified security and async handling
     *
     * @param Request $request Webhook request
     * @param string $provider PSP provider (stripe|paypal)
     * @return JsonResponse Processing result
     * @throws ValidationException Invalid webhook
     * @throws Exception Processing failed
     * @security Signature verification mandatory
     * @privacy-safe No sensitive data exposure in logs
     */
    private function processWebhook(Request $request, string $provider): JsonResponse {
        $webhookId = 'webhook_' . uniqid();

        try {
            // 1. ULM: Log webhook reception
            $this->logger->info("PSP webhook received", [
                'provider' => $provider,
                'webhook_id' => $webhookId,
                'payload_size' => strlen($request->getContent()),
                'headers' => $request->headers->all(),
                'ip' => $request->ip()
            ]);

            // 2. Get payment service for verification
            $paymentService = $this->paymentFactory->create($provider);

            // 3. SECURITY: Verify webhook authenticity
            $payloadWrapper = [
                'body' => $request->getContent(),
                'json' => $request->all(),
                'headers' => $request->headers->all(),
            ];

            if ($provider === 'stripe') {
                $payloadWrapper['signature'] = $request->header('Stripe-Signature');
            }

            if (!$paymentService->verifyWebhook($payloadWrapper)) {
                $this->errorManager->handle('PSP_WEBHOOK_VERIFICATION_FAILED', [
                    'provider' => $provider,
                    'webhook_id' => $webhookId,
                    'ip' => $request->ip()
                ]);

                return response()->json([
                    'error' => 'Webhook verification failed',
                    'webhook_id' => $webhookId
                ], Response::HTTP_UNAUTHORIZED);
            }

            // 4. Extract payment information
            $paymentData = $this->extractPaymentData($payloadWrapper, $provider);

            if (!$paymentData) {
                return response()->json([
                    'message' => 'Event not relevant for processing',
                    'webhook_id' => $webhookId
                ], Response::HTTP_OK);
            }

            // 5. Process within database transaction
            DB::transaction(function () use ($paymentData, $provider, $webhookId) {
                $this->processPaymentEvent($paymentData, $provider, $webhookId);
            });

            // 6. Success response
            return response()->json([
                'message' => 'Webhook processed successfully',
                'webhook_id' => $webhookId,
                'payment_id' => $paymentData['payment_id'] ?? null
            ], Response::HTTP_OK);
        } catch (ValidationException $e) {
            // 7. UEM: Validation error
            $this->errorManager->handle('PSP_WEBHOOK_VALIDATION_ERROR', [
                'provider' => $provider,
                'webhook_id' => $webhookId,
                'errors' => $e->errors()
            ], $e);

            return response()->json([
                'error' => 'Invalid webhook payload',
                'webhook_id' => $webhookId
            ], Response::HTTP_BAD_REQUEST);
        } catch (Exception $e) {
            // 8. UEM: Processing error
            $this->errorManager->handle('PSP_WEBHOOK_PROCESSING_ERROR', [
                'provider' => $provider,
                'webhook_id' => $webhookId,
                'error' => $e->getMessage()
            ], $e);

            return response()->json([
                'error' => 'Webhook processing failed',
                'webhook_id' => $webhookId
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Extract relevant payment data from webhook payload
     *
     * @param array $payload Webhook payload
     * @param string $provider PSP provider
     * @return array|null Payment data or null if not relevant
     * @privacy-safe Extract only necessary payment data
     */
    private function extractPaymentData(array $payload, string $provider): ?array {
        $event = $payload['json'] ?? $payload;

        switch ($provider) {
            case 'stripe':
                if (($event['type'] ?? '') === 'payment_intent.succeeded') {
                    return [
                        'payment_id' => $event['data']['object']['id'] ?? null,
                        'status' => 'succeeded',
                        'amount' => $event['data']['object']['amount'] ?? 0,
                        'currency' => $event['data']['object']['currency'] ?? 'EUR',
                        'metadata' => $event['data']['object']['metadata'] ?? []
                    ];
                }
                break;

            case 'paypal':
                if (($event['event_type'] ?? '') === 'PAYMENT.CAPTURE.COMPLETED') {
                    return [
                        'payment_id' => $event['resource']['id'] ?? null,
                        'status' => 'completed',
                        'amount' => $event['resource']['amount']['value'] ?? 0,
                        'currency' => $event['resource']['amount']['currency_code'] ?? 'EUR',
                        'metadata' => $event['resource']['custom_id'] ? ['custom_id' => $event['resource']['custom_id']] : []
                    ];
                }
                break;
        }

        return null;
    }

    /**
     * Process payment event and dispatch async jobs
     *
     * @param array $paymentData Payment event data
     * @param string $provider PSP provider
     * @param string $webhookId Webhook tracking ID
     * @throws Exception Processing failed
     * @gdpr-compliant Audit trail for all payment events
     * @blockchain-safe Async job dispatch for minting
     */
    private function processPaymentEvent(array $paymentData, string $provider, string $webhookId): void {
        $paymentId = $paymentData['payment_id'];
        $metadata = $paymentData['metadata'] ?? [];

        // 1. Find associated EGI or Reservation
        $egiBlockchain = null;
        $reservation = null;

        // Look for EGI by payment reference
        if (isset($metadata['egi_id'])) {
            $egiBlockchain = EgiBlockchain::where('id', $metadata['egi_id'])
                ->where('payment_id', $paymentId)
                ->first();
        }

        // Look for Reservation by payment reference
        if (!$egiBlockchain && isset($metadata['reservation_id'])) {
            $reservation = Reservation::where('id', $metadata['reservation_id'])
                ->where('payment_id', $paymentId)
                ->first();
        }

        if (!$egiBlockchain && !$reservation) {
            $this->logger->warning('Payment event without matching record', [
                'payment_id' => $paymentId,
                'provider' => $provider,
                'webhook_id' => $webhookId,
                'metadata' => $metadata
            ]);
            return;
        }

        // 2. Update payment status
        if ($egiBlockchain) {
            $egiBlockchain->update([
                'payment_status' => 'completed',
                'payment_completed_at' => now(),
                'webhook_received_at' => now()
            ]);

            // 3. GDPR: Audit trail
            $this->auditService->logActivity(
                $egiBlockchain->user,
                GdprActivityCategory::PAYMENT_PROCESSING,
                'Payment completed via PSP webhook',
                [
                    'payment_id' => $paymentId,
                    'provider' => $provider,
                    'egi_id' => $egiBlockchain->id,
                    'amount' => $paymentData['amount'],
                    'currency' => $paymentData['currency']
                ]
            );

            // 4. Dispatch async minting job
            if ($egiBlockchain->blockchain_status === 'pending') {
                ProcessEgiMintingJob::dispatch(
                    $egiBlockchain->id,
                    $webhookId
                )->onQueue('blockchain');

                $this->logger->info('Async minting job dispatched', [
                    'egi_id' => $egiBlockchain->id,
                    'payment_id' => $paymentId,
                    'webhook_id' => $webhookId,
                    'queue' => 'blockchain'
                ]);
            }
        }

        if ($reservation) {
            $reservation->update([
                'payment_status' => 'completed',
                'payment_completed_at' => now()
            ]);

            // GDPR: Audit trail for reservation payment
            $this->auditService->logActivity(
                $reservation->user,
                GdprActivityCategory::PAYMENT_PROCESSING,
                'Reservation payment completed via PSP webhook',
                [
                    'payment_id' => $paymentId,
                    'provider' => $provider,
                    'reservation_id' => $reservation->id,
                    'amount' => $paymentData['amount'],
                    'currency' => $paymentData['currency']
                ]
            );
        }

        // 5. ULM: Log successful processing
        $this->logger->info('Payment event processed successfully', [
            'payment_id' => $paymentId,
            'provider' => $provider,
            'webhook_id' => $webhookId,
            'egi_id' => $egiBlockchain?->id,
            'reservation_id' => $reservation?->id,
            'status' => $paymentData['status']
        ]);
    }

    /**
     * Health check endpoint for webhook endpoints
     *
     * @return JsonResponse Health status
     * @security Public endpoint for PSP health checks
     */
    public function health(): JsonResponse {
        return response()->json([
            'status' => 'healthy',
            'service' => 'psp-webhook-controller',
            'timestamp' => now()->toISOString(),
            'providers' => ['stripe', 'paypal'],
            'queue_status' => Queue::size('blockchain')
        ], Response::HTTP_OK);
    }
}
