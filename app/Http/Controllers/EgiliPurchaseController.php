<?php

namespace App\Http\Controllers;

use App\Services\EgiliPurchaseWorkflowService;
use App\Services\EgiliService;
use App\Services\Gdpr\AuditLogService;
use App\Enums\Gdpr\GdprActivityCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * @Oracode Controller: Egili Purchase Frontend Handler
 * 🎯 Purpose: Handle Egili purchase forms and payment processing
 * 🧱 Core Logic: Frontend interface for Egili purchases (FIAT + Crypto)
 * 🛡️ GDPR Compliance: Full audit trail for all purchase operations
 *
 * Routes:
 * - POST /egili/purchase/process → Process purchase request
 * - GET /egili/purchase/{orderReference}/confirmation → Show confirmation page
 *
 * @package App\Http\Controllers
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Egili Purchase System)
 * @date 2025-11-02
 * @purpose Frontend controller for Egili purchase workflow
 */
class EgiliPurchaseController extends Controller {
    private UltraLogManager $logger;
    private ErrorManagerInterface $errorManager;
    private AuditLogService $auditService;
    private EgiliPurchaseWorkflowService $purchaseWorkflow;
    private EgiliService $egiliService;

    /**
     * Constructor with dependency injection
     *
     * @param UltraLogManager $logger
     * @param ErrorManagerInterface $errorManager
     * @param AuditLogService $auditService
     * @param EgiliPurchaseWorkflowService $purchaseWorkflow
     * @param EgiliService $egiliService
     */
    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        AuditLogService $auditService,
        EgiliPurchaseWorkflowService $purchaseWorkflow,
        EgiliService $egiliService
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->auditService = $auditService;
        $this->purchaseWorkflow = $purchaseWorkflow;
        $this->egiliService = $egiliService;

        $this->middleware('auth');
    }

    /**
     * Process Egili purchase request
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function processPurchase(Request $request): JsonResponse {
        try {
            $user = Auth::user();

            // 1. Validate request
            $validated = $request->validate([
                'egili_amount' => 'required|integer|min:5000|max:1000000',
                'payment_method' => 'required|string|in:fiat,crypto',
                'fiat_provider' => 'nullable|required_if:payment_method,fiat|string|in:stripe,paypal',
                'crypto_provider' => 'nullable|required_if:payment_method,crypto|string',
                'return_url' => 'nullable|string|max:512',
            ]);

            // 2. ULM: Log purchase initiation
            $this->logger->info('Egili purchase process initiated', [
                'user_id' => $user->id,
                'egili_amount' => $validated['egili_amount'],
                'payment_method' => $validated['payment_method'],
                'log_category' => 'EGILI_PURCHASE_INITIATED'
            ]);

            // 3. GDPR: Log purchase attempt
            $this->auditService->logUserAction(
                $user,
                'egili_purchase_initiated',
                [
                    'egili_amount' => $validated['egili_amount'],
                    'payment_method' => $validated['payment_method'],
                    'provider' => $validated['payment_method'] === 'fiat'
                        ? $validated['fiat_provider']
                        : $validated['crypto_provider'],
                ],
                GdprActivityCategory::WALLET_MANAGEMENT
            );

            // 4. Route to appropriate payment method
            if ($validated['payment_method'] === 'fiat') {
                // FIAT payment flow
                $result = $this->purchaseWorkflow->purchaseWithFiat(
                    $user,
                    $validated['egili_amount'],
                    $validated['fiat_provider'],
                    array_merge($request->all(), [
                        'return_url' => $validated['return_url'] ?? url()->previous()
                    ])
                );

                // Handle Stripe Checkout redirect
                if (!empty($result['requires_redirect']) && !empty($result['redirect_url'])) {
                    return response()->json([
                        'success' => true,
                        'redirect_url' => $result['redirect_url'],
                        'order_reference' => $result['order_reference'],
                        'message' => __('egili.purchase.redirect_to_payment'),
                    ]);
                }

                // FIAT completes immediately - redirect to confirmation
                return response()->json([
                    'success' => true,
                    'redirect_url' => route('egili.purchase.confirmation', [
                        'orderReference' => $result['order_reference']
                    ]),
                    'order_reference' => $result['order_reference'],
                    'message' => __('egili.purchase.payment_success'),
                ]);
            } else {
                // Crypto payment flow
                $result = $this->purchaseWorkflow->purchaseWithCrypto(
                    $user,
                    $validated['egili_amount'],
                    $validated['crypto_provider'],
                    array_merge($request->all(), [
                        'return_url' => $validated['return_url'] ?? url()->previous()
                    ])
                );

                // Crypto requires redirect to gateway
                return response()->json([
                    'success' => true,
                    'redirect_url' => $result['payment_url'],
                    'order_reference' => $result['order_reference'],
                    'message' => __('egili.purchase.crypto_redirect'),
                ]);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validation errors
            $this->logger->warning('Egili purchase validation failed', [
                'user_id' => Auth::id(),
                'errors' => $e->errors(),
                'log_category' => 'EGILI_PURCHASE_VALIDATION'
            ]);

            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
                'message' => __('validation.failed'),
            ], 422);
        } catch (\Exception $e) {
            // UEM: Handle unexpected errors
            $this->errorManager->handle('EGILI_PURCHASE_PROCESS_FAILED', [
                'user_id' => Auth::id(),
                'egili_amount' => $request->input('egili_amount'),
                'payment_method' => $request->input('payment_method'),
                'error_message' => $e->getMessage(),
            ], $e);

            return response()->json([
                'success' => false,
                'message' => __('egili.purchase.process_error'),
            ], 500);
        }
    }

    /**
     * Show purchase confirmation page
     * Also handles Stripe Checkout callback when session_id is present
     *
     * @param Request $request
     * @param string $orderReference
     * @return View
     */
    public function showConfirmation(Request $request, string $orderReference): View {
        try {
            $user = Auth::user();

            // 1. Get purchase record
            $purchase = $this->purchaseWorkflow->getPurchaseByOrderReference($orderReference);

            if (!$purchase) {
                abort(404, __('egili.purchase.order_not_found'));
            }

            // 2. Authorization check
            if ($purchase->user_id !== $user->id) {
                abort(403, __('egili.purchase.unauthorized'));
            }

            // 3. Handle Stripe Checkout callback - verify and complete payment
            $sessionId = $request->query('session_id');

            $this->logger->info('showConfirmation called', [
                'order_reference' => $orderReference,
                'session_id_from_url' => $sessionId,
                'payment_status' => $purchase->payment_status,
                'user_id' => $user->id,
            ]);

            if ($sessionId && $purchase->payment_status === 'pending_checkout') {
                $this->logger->info('Calling completeCheckoutPayment...');
                $this->completeCheckoutPayment($purchase, $sessionId, $user);
                // Refresh purchase record
                $purchase->refresh();
                $this->logger->info('After refresh', ['new_status' => $purchase->payment_status]);
            }

            // 4. Get current Egili balance
            $currentBalance = $this->egiliService->getBalance($user);

            // 5. ULM: Log confirmation page view
            $this->logger->info('Egili purchase confirmation viewed', [
                'user_id' => $user->id,
                'order_reference' => $orderReference,
                'purchase_status' => $purchase->payment_status,
                'log_category' => 'EGILI_PURCHASE_CONFIRMATION_VIEW'
            ]);

            // 6. Return confirmation view
            return view('egili.purchase-confirmation', compact(
                'purchase',
                'currentBalance'
            ));
        } catch (\Exception $e) {
            // UEM: Handle error and redirect to safe page
            $this->errorManager->handle('EGILI_PURCHASE_CONFIRMATION_ERROR', [
                'user_id' => Auth::id(),
                'order_reference' => $orderReference,
                'error' => $e->getMessage(),
            ], $e);

            abort(500, __('egili.purchase.confirmation_error'));
        }
    }

    /**
     * Get purchase pricing for frontend
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getPricing(Request $request): JsonResponse {
        try {
            $egiliAmount = $request->input('egili_amount', 0);

            if ($egiliAmount < 1) {
                return response()->json([
                    'success' => false,
                    'message' => __('egili.purchase.invalid_amount'),
                ], 400);
            }

            $unitPrice = Config::get('egili.purchase.unit_price_eur', 0.01);
            $minPurchase = Config::get('egili.purchase.min_amount', 5000);
            $maxPurchase = Config::get('egili.purchase.max_amount', 1000000);

            $totalEur = round($egiliAmount * $unitPrice, 2);

            return response()->json([
                'success' => true,
                'egili_amount' => $egiliAmount,
                'unit_price_eur' => $unitPrice,
                'total_eur' => $totalEur,
                'min_purchase' => $minPurchase,
                'max_purchase' => $maxPurchase,
                'is_valid' => $egiliAmount >= $minPurchase && $egiliAmount <= $maxPurchase,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('egili.purchase.pricing_error'),
            ], 500);
        }
    }

    /**
     * Complete payment after Stripe Checkout success
     *
     * @param \App\Models\EgiliMerchantPurchase $purchase
     * @param string $sessionId
     * @param \App\Models\User $user
     * @return void
     */
    private function completeCheckoutPayment($purchase, string $sessionId, $user): void {
        $this->logger->info('completeCheckoutPayment CALLED', [
            'session_id' => $sessionId,
            'order_reference' => $purchase->order_reference,
            'current_status' => $purchase->payment_status,
            'user_id' => $user->id,
        ]);

        try {
            // Verify Stripe Checkout session
            $stripe = new \Stripe\StripeClient(config('algorand.payments.stripe.secret_key'));
            $session = $stripe->checkout->sessions->retrieve($sessionId);

            $this->logger->info('Stripe session retrieved', [
                'stripe_payment_status' => $session->payment_status,
                'stripe_status' => $session->status,
            ]);

            if ($session->payment_status !== 'paid') {
                $this->logger->warning('Stripe Checkout session not paid', [
                    'session_id' => $sessionId,
                    'payment_status' => $session->payment_status,
                    'order_reference' => $purchase->order_reference,
                ]);
                return;
            }

            // Update purchase record
            $purchase->update([
                'payment_status' => 'completed',
                'payment_external_id' => $session->payment_intent,
                'completed_at' => now(),
            ]);

            // Mint Egili to user wallet
            $this->egiliService->earn(
                $user,
                $purchase->egili_amount,
                'egili_purchase',
                'purchase',
                [
                    'order_reference' => $purchase->order_reference,
                    'payment_method' => $purchase->payment_method,
                    'payment_provider' => $purchase->payment_provider,
                    'total_paid_eur' => $purchase->total_price_eur,
                    'stripe_session_id' => $sessionId,
                ]
            );

            // GDPR: Audit trail
            $this->auditService->logUserAction(
                $user,
                'egili_purchase_completed_checkout',
                [
                    'order_reference' => $purchase->order_reference,
                    'egili_amount' => $purchase->egili_amount,
                    'total_eur' => $purchase->total_price_eur,
                    'stripe_session_id' => $sessionId,
                    'new_balance' => $this->egiliService->getBalance($user),
                ],
                \App\Enums\Gdpr\GdprActivityCategory::WALLET_MANAGEMENT
            );

            $this->logger->info('Egili purchase completed via Stripe Checkout', [
                'user_id' => $user->id,
                'order_reference' => $purchase->order_reference,
                'egili_amount' => $purchase->egili_amount,
                'stripe_session_id' => $sessionId,
                'log_category' => 'EGILI_PURCHASE_CHECKOUT_COMPLETED'
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Failed to complete Stripe Checkout payment', [
                'session_id' => $sessionId,
                'order_reference' => $purchase->order_reference,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
