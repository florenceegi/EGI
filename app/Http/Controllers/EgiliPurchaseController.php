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
class EgiliPurchaseController extends Controller
{
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
    public function processPurchase(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();

            // 1. Validate request
            $validated = $request->validate([
                'egili_amount' => 'required|integer|min:5000|max:1000000',
                'payment_method' => 'required|string|in:fiat,crypto',
                'fiat_provider' => 'nullable|required_if:payment_method,fiat|string|in:stripe,paypal',
                'crypto_provider' => 'nullable|required_if:payment_method,crypto|string',
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
                    $request->all()
                );

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
                    $request->all()
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
     *
     * @param string $orderReference
     * @return View
     */
    public function showConfirmation(string $orderReference): View
    {
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

            // 3. Get current Egili balance
            $currentBalance = $this->egiliService->getBalance($user);

            // 4. ULM: Log confirmation page view
            $this->logger->info('Egili purchase confirmation viewed', [
                'user_id' => $user->id,
                'order_reference' => $orderReference,
                'purchase_status' => $purchase->payment_status,
                'log_category' => 'EGILI_PURCHASE_CONFIRMATION_VIEW'
            ]);

            // 5. Return confirmation view
            return view('egili.purchase-confirmation', compact(
                'purchase',
                'currentBalance'
            ));

        } catch (\Exception $e) {
            return $this->errorManager->handle('EGILI_PURCHASE_CONFIRMATION_ERROR', [
                'user_id' => Auth::id(),
                'order_reference' => $orderReference,
                'error' => $e->getMessage(),
            ], $e);
        }
    }

    /**
     * Get purchase pricing for frontend
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getPricing(Request $request): JsonResponse
    {
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
}

