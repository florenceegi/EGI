<?php

/**
 * @package App\Http\Controllers
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Dual Architecture)
 * @date 2025-11-01
 * @purpose Controller for EGI Living subscription payment flow
 */

namespace App\Http\Controllers;

use App\Models\Egi;
use App\Services\EgiLivingPaymentService;
use App\Services\EgiliService;
use App\Services\Gdpr\AuditLogService;
use App\Enums\Gdpr\GdprActivityCategory;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * @Oracode Controller: EGI Living Subscription Payment
 * 🎯 Purpose: Handle EGI Living subscription purchases
 * 🧱 Core Logic: Unified payment flow (FIAT/Crypto/Egili)
 * 🛡️ GDPR Compliance: Full audit trail for payment operations
 */
class EgiLivingSubscriptionController extends Controller
{
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;
    protected AuditLogService $auditService;
    protected EgiLivingPaymentService $paymentService;
    protected EgiliService $egiliService;
    
    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        AuditLogService $auditService,
        EgiLivingPaymentService $paymentService,
        EgiliService $egiliService
    ) {
        $this->middleware('auth');
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->auditService = $auditService;
        $this->paymentService = $paymentService;
        $this->egiliService = $egiliService;
    }
    
    /**
     * Show payment form for EGI Living subscription
     * 
     * @param int $egiId
     * @return \Illuminate\View\View|RedirectResponse
     */
    public function showPaymentForm(int $egiId)
    {
        try {
            $user = Auth::user();
            $egi = Egi::with(['user', 'utility.media'])->findOrFail($egiId);
            
            // ULM: Log form access
            $this->logger->info('EGI Living payment form accessed', [
                'user_id' => $user->id,
                'egi_id' => $egi->id,
                'log_category' => 'LIVING_PAYMENT_FORM_ACCESS'
            ]);
            
            // Check if user is EGI creator
            if ($egi->user_id !== $user->id) {
                return $this->errorManager->handle('LIVING_PAYMENT_UNAUTHORIZED', [
                    'user_id' => $user->id,
                    'egi_id' => $egi->id,
                    'egi_creator_id' => $egi->user_id,
                ]);
            }
            
            // Check if already activated
            if ($egi->egi_living_enabled) {
                return redirect()->route('egis.show', $egi->id)
                    ->with('info', __('egi_living.already_active'));
            }
            
            // Get pricing and user balance
            $pricing = $this->paymentService->getPricing();
            $egiliBalance = $this->egiliService->getBalance($user);
            $canPayWithEgili = $this->paymentService->canPayWithEgili($user);
            
            // GDPR: Audit trail
            $this->auditService->logUserAction(
                $user,
                'egi_living_payment_form_viewed',
                [
                    'egi_id' => $egi->id,
                    'pricing' => $pricing,
                ],
                GdprActivityCategory::BLOCKCHAIN_ACTIVITY
            );
            
            return view('egi-living.payment-form', compact(
                'egi',
                'pricing',
                'egiliBalance',
                'canPayWithEgili'
            ));
            
        } catch (\Exception $e) {
            return $this->errorManager->handle('LIVING_PAYMENT_FORM_ERROR', [
                'user_id' => Auth::id(),
                'egi_id' => $egiId,
                'error' => $e->getMessage(),
            ], $e);
        }
    }
    
    /**
     * Process payment submission
     * 
     * @param Request $request
     * @return RedirectResponse
     */
    public function processPayment(Request $request): RedirectResponse
    {
        try {
            $user = Auth::user();
            
            $validated = $request->validate([
                'egi_id' => 'required|integer|exists:egis,id',
                'payment_method' => 'required|string|in:fiat,crypto,egili',
                'fiat_provider' => 'nullable|required_if:payment_method,fiat|in:stripe,paypal',
                'crypto_provider' => 'nullable|required_if:payment_method,crypto|in:coinbase_commerce,bitpay,nowpayments',
            ]);
            
            $egi = Egi::findOrFail($validated['egi_id']);
            
            // ULM: Log payment processing
            $this->logger->info('EGI Living payment processing', [
                'user_id' => $user->id,
                'egi_id' => $egi->id,
                'payment_method' => $validated['payment_method'],
                'log_category' => 'LIVING_PAYMENT_PROCESS'
            ]);
            
            // Authorization check
            if ($egi->user_id !== $user->id) {
                return $this->errorManager->handle('LIVING_PAYMENT_UNAUTHORIZED', [
                    'user_id' => $user->id,
                    'egi_id' => $egi->id,
                ]);
            }
            
            // Route to appropriate payment method
            $result = match($validated['payment_method']) {
                'egili' => $this->paymentService->payWithEgili($user, $egi),
                'fiat' => $this->paymentService->payWithFiat($user, $egi, $validated['fiat_provider']),
                'crypto' => $this->paymentService->payWithCrypto($user, $egi, $validated['crypto_provider'] ?? null),
            };
            
            // If Egili payment, redirect to success page
            if ($validated['payment_method'] === 'egili') {
                return redirect()->route('egis.show', $egi->id)
                    ->with('success', __('egi_living.payment.egili_success'));
            }
            
            // If FIAT/Crypto, redirect to payment gateway
            return redirect($result['redirect_url']);
            
        } catch (\Exception $e) {
            return $this->errorManager->handle('LIVING_PAYMENT_PROCESS_ERROR', [
                'user_id' => Auth::id(),
                'egi_id' => $request->input('egi_id'),
                'payment_method' => $request->input('payment_method'),
                'error' => $e->getMessage(),
            ], $e);
        }
    }
    
    /**
     * Handle payment success callback
     * 
     * @param int $egiId
     * @return RedirectResponse
     */
    public function paymentSuccess(int $egiId): RedirectResponse
    {
        try {
            $user = Auth::user();
            $egi = Egi::findOrFail($egiId);
            
            // ULM: Log success callback
            $this->logger->info('EGI Living payment success callback', [
                'user_id' => $user->id,
                'egi_id' => $egi->id,
                'log_category' => 'LIVING_PAYMENT_SUCCESS_CALLBACK'
            ]);
            
            return redirect()->route('egis.show', $egi->id)
                ->with('success', __('egi_living.payment.success'));
            
        } catch (\Exception $e) {
            return $this->errorManager->handle('LIVING_PAYMENT_SUCCESS_ERROR', [
                'user_id' => Auth::id(),
                'egi_id' => $egiId,
                'error' => $e->getMessage(),
            ], $e);
        }
    }
    
    /**
     * Handle payment cancel callback
     * 
     * @param int $egiId
     * @return RedirectResponse
     */
    public function paymentCancel(int $egiId): RedirectResponse
    {
        try {
            $user = Auth::user();
            $egi = Egi::findOrFail($egiId);
            
            // ULM: Log cancel callback
            $this->logger->info('EGI Living payment cancelled', [
                'user_id' => $user->id,
                'egi_id' => $egi->id,
                'log_category' => 'LIVING_PAYMENT_CANCEL'
            ]);
            
            return redirect()->route('egis.show', $egi->id)
                ->with('warning', __('egi_living.payment.cancelled'));
            
        } catch (\Exception $e) {
            return $this->errorManager->handle('LIVING_PAYMENT_CANCEL_ERROR', [
                'user_id' => Auth::id(),
                'egi_id' => $egiId,
                'error' => $e->getMessage(),
            ], $e);
        }
    }
}





