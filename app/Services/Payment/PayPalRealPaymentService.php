<?php

namespace App\Services\Payment;

use App\Contracts\PaymentServiceInterface;
use App\DataTransferObjects\Payment\PaymentRequest;
use App\DataTransferObjects\Payment\PaymentResult;
use App\DataTransferObjects\Payment\RefundResult;
use App\DataTransferObjects\Payment\PaymentStatus;
use App\Enums\Gdpr\GdprActivityCategory;
use App\Services\Gdpr\AuditLogService;
use App\Services\Gdpr\ConsentService;
use Illuminate\Support\Arr;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\ProductionEnvironment;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use PayPalCheckoutSdk\Payments\CapturesRefundRequest;
use PayPalCheckoutSdk\Payments\CapturesGetRequest;
use PayPalCheckoutSdk\Webhooks\VerifyWebhookSignatureRequest;
use PayPalHttp\HttpException;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * @Oracode Service: PayPal Payment Service (Sandbox/Live)
 * 🎯 Purpose: Connect to PayPal Checkout for real FIAT payments
 * 🛡️ Compliance: MiCA-safe, GDPR logging, webhook signature verification
 */
class PayPalRealPaymentService implements PaymentServiceInterface
{
    private UltraLogManager $logger;
    private ErrorManagerInterface $errorManager;
    private AuditLogService $auditService;
    private ConsentService $consentService;
    private PayPalHttpClient $client;
    private array $config;
    private bool $sandbox;
    private array $supportedCurrencies;

    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        AuditLogService $auditService,
        ConsentService $consentService
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->auditService = $auditService;
        $this->consentService = $consentService;

        $this->config = config('algorand.payments.paypal', []);
        $clientId = Arr::get($this->config, 'client_id');
        $clientSecret = Arr::get($this->config, 'client_secret');

        if (empty($clientId) || empty($clientSecret)) {
            throw new \RuntimeException('PayPal credentials are not configured');
        }

        $this->sandbox = strtolower((string) Arr::get($this->config, 'mode', 'sandbox')) === 'sandbox';
        $environment = $this->sandbox
            ? new SandboxEnvironment($clientId, $clientSecret)
            : new ProductionEnvironment($clientId, $clientSecret);

        $this->client = new PayPalHttpClient($environment);
        $this->supportedCurrencies = array_map(
            'strtoupper',
            Arr::get($this->config, 'supported_currencies', ['EUR'])
        );
    }

    public function processPayment(PaymentRequest $request): PaymentResult
    {
        try {
            $merchantContext = $request->getMerchantContext();
            $paypalMerchantId = $merchantContext['paypal_merchant_id'] ?? null;

            if (!$paypalMerchantId) {
                throw new \RuntimeException('PayPal merchant ID is not configured for this merchant');
            }

            $amountValue = number_format($request->amount, 2, '.', '');
            $currencyCode = strtoupper($request->currency);
            $metadata = $request->getPspMetadata();

            $createRequest = new OrdersCreateRequest();
            $createRequest->prefer('return=representation');
            $createRequest->body = [
                'intent' => 'CAPTURE',
                'purchase_units' => [
                    [
                        'amount' => [
                            'currency_code' => $currencyCode,
                            'value' => $amountValue,
                        ],
                        'custom_id' => (string) ($request->egiId ?? $request->reservationId ?? 'EGI'),
                        'description' => sprintf('EGI Mint #%s', $request->egiId ?? 'N/A'),
                        'payee' => [
                            'merchant_id' => $paypalMerchantId,
                        ],
                    ],
                ],
                'payment_source' => $this->buildPaymentSource(),
            ];

            $orderResponse = $this->client->execute($createRequest);
            $orderId = $orderResponse->result?->id ?? null;

            if (!$orderId) {
                throw new \RuntimeException('Unable to create PayPal order');
            }

            $this->logger->info('PayPal order created', [
                'order_id' => $orderId,
                'status' => $orderResponse->result?->status,
                'sandbox' => $this->sandbox,
            ]);

            $captureRequest = new OrdersCaptureRequest($orderId);
            $captureRequest->prefer('return=representation');
            $captureRequest->body = array_filter([
                'payment_source' => $this->buildPaymentSource(),
            ]);

            $captureResponse = $this->client->execute($captureRequest);

            $capture = $captureResponse->result?->purchase_units[0]?->payments?->captures[0] ?? null;
            if (!$capture || strtoupper($capture->status) !== 'COMPLETED') {
                throw new \RuntimeException('PayPal capture failed');
            }

            $paymentId = $capture->id;
            $receiptUrl = $capture->links
                ? collect($capture->links)->firstWhere('rel', 'self')->href ?? null
                : null;

            $metadataResponse = array_merge($metadata, [
                'order_id' => $orderId,
                'capture_id' => $paymentId,
                'status' => $capture->status,
                'merchant_psp' => array_filter([
                    'provider' => 'paypal',
                    'paypal_merchant_id' => $paypalMerchantId,
                ]),
            ]);

            $result = new PaymentResult(
                success: true,
                paymentId: $paymentId,
                amount: $request->amount,
                currency: $currencyCode,
                status: 'completed',
                metadata: $metadataResponse,
                receiptUrl: $receiptUrl,
                processedAt: new \DateTimeImmutable()
            );

            $this->logger->info('PayPal payment captured', [
                'order_id' => $orderId,
                'capture_id' => $paymentId,
                'status' => $capture->status,
            ]);

            return $result;
        } catch (HttpException $exception) {
            $message = $exception->getMessage();
            $this->errorManager->handle('PAYPAL_PAYMENT_HTTP_ERROR', [
                'status_code' => $exception->statusCode,
                'error_message' => $message,
            ], $exception);

            return PaymentResult::failure(
                paymentId: '',
                errorMessage: $message,
                errorCode: 'PAYPAL_HTTP_ERROR'
            );
        } catch (\Throwable $exception) {
            $this->errorManager->handle('PAYPAL_PAYMENT_UNEXPECTED_ERROR', [
                'error_message' => $exception->getMessage(),
            ], $exception);

            return PaymentResult::failure(
                paymentId: '',
                errorMessage: 'PayPal payment processing failed',
                errorCode: 'PAYPAL_UNEXPECTED_ERROR'
            );
        }
    }

    public function verifyWebhook(array $payload): bool
    {
        try {
            $webhookId = Arr::get($this->config, 'webhook_id');
            if (empty($webhookId)) {
                $this->logger->warning('PayPal webhook verification skipped - webhook ID missing');
                return false;
            }

            $headers = Arr::get($payload, 'headers', []);
            $body = $payload['body'] ?? '';

            $verifyRequest = new VerifyWebhookSignatureRequest();
            $verifyRequest->body = [
                'transmission_id' => Arr::first($headers['paypal-transmission-id'] ?? []),
                'transmission_time' => Arr::first($headers['paypal-transmission-time'] ?? []),
                'transmission_sig' => Arr::first($headers['paypal-transmission-sig'] ?? []),
                'cert_url' => Arr::first($headers['paypal-cert-url'] ?? []),
                'auth_algo' => Arr::first($headers['paypal-auth-algo'] ?? []),
                'webhook_id' => $webhookId,
                'webhook_event' => $payload['json'] ?? [],
            ];

            $response = $this->client->execute($verifyRequest);
            return ($response->result?->verification_status ?? '') === 'SUCCESS';
        } catch (HttpException $exception) {
            $this->errorManager->handle('PAYPAL_WEBHOOK_HTTP_ERROR', [
                'status_code' => $exception->statusCode,
                'error_message' => $exception->getMessage(),
            ], $exception);
            return false;
        } catch (\Throwable $exception) {
            $this->errorManager->handle('PAYPAL_WEBHOOK_UNEXPECTED_ERROR', [
                'error_message' => $exception->getMessage(),
            ], $exception);
            return false;
        }
    }

    public function refundPayment(string $paymentId): RefundResult
    {
        try {
            $refundRequest = new CapturesRefundRequest($paymentId);
            $refundRequest->prefer('return=representation');
            $response = $this->client->execute($refundRequest);

            $refund = $response->result ?? null;
            if (!$refund) {
                throw new \RuntimeException('Unable to process PayPal refund');
            }

            $amount = (float) ($refund->amount->value ?? 0);
            $currency = $refund->amount->currency_code ?? 'EUR';

            return RefundResult::success(
                refundId: $refund->id ?? '',
                originalPaymentId: $paymentId,
                refundAmount: $amount,
                currency: $currency,
                reason: $refund->reason ?? 'Customer request',
                metadata: [
                    'status' => $refund->status ?? null,
                ]
            );
        } catch (HttpException $exception) {
            $this->errorManager->handle('PAYPAL_REFUND_HTTP_ERROR', [
                'status_code' => $exception->statusCode,
                'error_message' => $exception->getMessage(),
            ], $exception);

            return RefundResult::failure(
                refundId: '',
                originalPaymentId: $paymentId,
                errorMessage: $exception->getMessage(),
                errorCode: 'PAYPAL_HTTP_ERROR'
            );
        } catch (\Throwable $exception) {
            $this->errorManager->handle('PAYPAL_REFUND_UNEXPECTED_ERROR', [
                'error_message' => $exception->getMessage(),
            ], $exception);

            return RefundResult::failure(
                refundId: '',
                originalPaymentId: $paymentId,
                errorMessage: 'PayPal refund failed',
                errorCode: 'PAYPAL_REFUND_ERROR'
            );
        }
    }

    public function getPaymentStatus(string $paymentId): PaymentStatus
    {
        try {
            $captureRequest = new CapturesGetRequest($paymentId);
            $captureRequest->prefer('return=representation');
            $response = $this->client->execute($captureRequest);
            $capture = $response->result ?? null;

            $amount = (float) ($capture->amount->value ?? 0);
            $currency = $capture->amount->currency_code ?? 'EUR';
            $status = strtolower($capture->status ?? 'completed');

            return PaymentStatus::fromArray([
                'payment_id' => $paymentId,
                'status' => $status,
                'amount' => $amount,
                'currency' => $currency,
                'is_paid' => in_array($status, ['completed', 'succeeded']),
                'metadata' => [
                    'order_id' => $capture->supplementary_data->related_ids->order_id ?? null,
                ],
            ]);
        } catch (HttpException $exception) {
            $this->errorManager->handle('PAYPAL_STATUS_HTTP_ERROR', [
                'payment_id' => $paymentId,
                'status_code' => $exception->statusCode,
                'error_message' => $exception->getMessage(),
            ], $exception);

            return PaymentStatus::fromArray([
                'payment_id' => $paymentId,
                'status' => 'failed',
                'amount' => 0,
                'currency' => 'EUR',
                'is_paid' => false,
                'failure_reason' => $exception->getMessage(),
            ]);
        } catch (\Throwable $exception) {
            $this->errorManager->handle('PAYPAL_STATUS_ERROR', [
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
            ]);
        }
    }

    public function getProviderName(): string
    {
        return 'paypal';
    }

    public function supportsCurrency(string $currency): bool
    {
        return in_array(strtoupper($currency), $this->supportedCurrencies, true);
    }

    public function getSupportedCurrencies(): array
    {
        return $this->supportedCurrencies;
    }

    private function buildPaymentSource(): array
    {
        if (!$this->sandbox) {
            return [];
        }

        $card = Arr::get($this->config, 'sandbox_card', []);

        if (empty($card['number']) || empty($card['expiry']) || empty($card['cvv'])) {
            return [];
        }

        return [
            'card' => [
                'number' => $card['number'],
                'expiry' => $card['expiry'],
                'security_code' => $card['cvv'],
                'name' => $card['name'] ?? 'Sandbox User',
                'billing_address' => [
                    'country_code' => $card['billing_country'] ?? 'IT',
                    'postal_code' => $card['billing_postal_code'] ?? '50100',
                ],
            ],
        ];
    }
}

