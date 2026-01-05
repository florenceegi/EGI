<?php

declare(strict_types=1);

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Services\Payment\PaymentServiceFactory;
use App\Jobs\ProcessEgiMintingJob;
use App\Models\{EgiBlockchain, Reservation, PspWebhookEvent};
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
        $payload = $request->all();

        try {
            // 1. Extract webhook ID for idempotency tracking
            $webhookId = $this->extractWebhookId($request, $provider);

            // 2. Ensure webhook idempotency (avoid duplicate processing)
            $webhookEvent = $this->ensureWebhookIdempotency($webhookId, $provider, $payload);

            if ($webhookEvent->status === 'processed') {
                return response()->json([
                    'success' => true,
                    'status' => 'already_processed',
                    'webhook_id' => $webhookId
                ], Response::HTTP_OK);
            }

            if ($webhookEvent->status === 'failed' && $webhookEvent->retry_count >= 5) {
                return response()->json([
                    'success' => true,
                    'status' => 'max_retries_exceeded',
                    'webhook_id' => $webhookId
                ], Response::HTTP_OK);
            }

            // 3. Get payment service and verify webhook
            $paymentService = $this->paymentFactory->create($provider);

            $payloadWrapper = [
                'body' => $request->getContent(),
                'json' => $payload,
                'headers' => $request->headers->all(),
            ];

            if ($provider === 'stripe') {
                $payloadWrapper['signature'] = $request->header('Stripe-Signature');
            }

            if (!$paymentService->verifyWebhook($payloadWrapper)) {
                $this->updateWebhookEventStatus($webhookEvent, 'failed', 'Webhook signature verification failed');

                return response()->json([
                    'success' => false,
                    'error' => 'Webhook verification failed',
                    'webhook_id' => $webhookId
                ], Response::HTTP_UNAUTHORIZED);
            }

            // 4. Process payment webhook via service (NO hardcoded logic here!)
            $result = $paymentService->processPaymentWebhook($payload, $payloadWrapper['headers'] ?? []);

            if (!$result || !$result['success']) {
                $this->updateWebhookEventStatus($webhookEvent, 'failed', 'Payment processing failed');

                return response()->json([
                    'success' => false,
                    'error' => 'Payment processing failed',
                    'webhook_id' => $webhookId
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            // 5. Mark webhook as processed
            $this->updateWebhookEventStatus($webhookEvent, 'processed');

            // 6. Success response
            return response()->json([
                'success' => true,
                'webhook_id' => $webhookId,
                'payment_id' => $result['payment_id'] ?? null
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            // Handle any processing errors
            $this->logger->error('Webhook processing error', [
                'provider' => $provider,
                'webhook_id' => $webhookId ?? 'unknown',
                'error' => $e->getMessage()
            ]);

            if (isset($webhookEvent)) {
                $this->updateWebhookEventStatus($webhookEvent, 'failed', $e->getMessage());
            }

            return response()->json([
                'success' => false,
                'error' => 'Webhook processing failed',
                'webhook_id' => $webhookId ?? 'unknown'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Extract webhook ID from request headers (PSP-agnostic)
     *
     * @param Request $request Webhook request
     * @param string $provider PSP provider
     * @return string Webhook ID for tracking
     */
    private function extractWebhookId(Request $request, string $provider): string {
        switch ($provider) {
            case 'stripe':
                $signature = $request->header('stripe-signature', '');
                if (preg_match('/wh_id=([^,]+)/', $signature, $matches)) {
                    return $matches[1];
                }
                return 'stripe_' . hash('sha256', $request->getContent() . time());

            case 'paypal':
                return $request->header('paypal-transmission-id', 'paypal_' . uniqid());

            default:
                return $provider . '_' . hash('sha256', $request->getContent() . time());
        }
    }

    /**
     * Ensure webhook idempotency using database
     *
     * @param string $webhookId Unique webhook identifier
     * @param string $psp PSP provider name
     * @param array $payload Webhook payload
     * @return PspWebhookEvent Webhook event record
     */
    private function ensureWebhookIdempotency(string $webhookId, string $psp, array $payload): PspWebhookEvent {
        $payloadHash = hash('sha256', json_encode($payload));
        $eventType = $this->determineEventType($payload);

        // Try to find existing webhook event
        $webhookEvent = PspWebhookEvent::where('event_id', $webhookId)->first();

        if ($webhookEvent) {
            // Existing event - check status
            return $webhookEvent;
        }

        // Create new webhook event record
        return PspWebhookEvent::create([
            'event_id' => $webhookId,
            'provider' => $psp,
            'event_type' => $eventType,
            'status' => 'processing',
            'payload' => $payload,
            'received_at' => now(),
            'retry_count' => 0
        ]);
    }

    /**
     * Determine event type from payload (PSP-agnostic)
     *
     * @param array $payload Webhook payload
     * @return string Event type for logging
     */
    private function determineEventType(array $payload): string {
        // Generic object-based detection (no PSP hardcoding!)
        if (isset($payload['object'])) {
            return $payload['object'];
        }

        if (isset($payload['type'])) {
            return explode('.', $payload['type'])[0] ?? 'unknown';
        }

        if (isset($payload['event_type'])) {
            return explode('.', $payload['event_type'])[0] ?? 'unknown';
        }

        return 'unknown';
    }

    /**
     * Update webhook event status in database
     *
     * @param PspWebhookEvent $webhookEvent Event to update
     * @param string $status New status
     * @param string|null $errorMessage Error message if failed
     */
    private function updateWebhookEventStatus(PspWebhookEvent $webhookEvent, string $status, ?string $errorMessage = null): void {
        $updateData = ['status' => $status];

        if ($status === 'processed') {
            $updateData['processed_at'] = now();
            $updateData['retry_count'] = 0; // Reset on success
            $updateData['error_message'] = null;
        } elseif ($status === 'failed') {
            $updateData['retry_count'] = $webhookEvent->retry_count + 1;
            $updateData['error_message'] = $errorMessage;
        }

        $webhookEvent->update($updateData);
    }

    /**
     * Process payment event and dispatch async jobs
     *
     * @param array $result Payment processing result from service
     * @param string $provider PSP provider
     * @param string $webhookId Webhook tracking ID
     * @throws Exception Processing failed
     * @gdpr-compliant Audit trail for all payment events
     * @blockchain-safe Async job dispatch for minting
     */
    private function processPaymentEvent(array $result, string $provider, string $webhookId): void {
        $paymentId = $result['payment_id'] ?? null;
        $metadata = $result['metadata'] ?? [];

        if (!$paymentId) {
            $this->logger->warning('No payment ID in webhook result', [
                'webhook_id' => $webhookId,
                'provider' => $provider
            ]);
            return;
        }

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
                    'amount' => $result['amount'] ?? null,
                    'currency' => $result['currency'] ?? 'EUR'
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
                    'amount' => $result['amount'] ?? null,
                    'currency' => $result['currency'] ?? 'EUR'
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
            'status' => $result['status'] ?? 'completed'
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
