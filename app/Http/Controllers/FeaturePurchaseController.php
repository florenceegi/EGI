<?php

/**
 * @package App\Http\Controllers
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Feature Purchase System)
 * @date 2025-11-01
 * @purpose Controller for feature purchases (Hybrid approach)
 */

namespace App\Http\Controllers;

use App\Services\FeaturePurchaseService;
use App\Services\EgiliService;
use App\Services\Gdpr\AuditLogService;
use App\Enums\Gdpr\GdprActivityCategory;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * @Oracode Controller: Feature Purchase Flow
 * 🎯 Purpose: Handle feature purchases with FIAT/Crypto/Egili
 * 🧱 Core Logic: Show pricing → Process payment → Grant permission
 * 🛡️ GDPR Compliance: Full audit trail
 */
class FeaturePurchaseController extends Controller
{
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;
    protected AuditLogService $auditService;
    protected FeaturePurchaseService $purchaseService;
    protected EgiliService $egiliService;
    
    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        AuditLogService $auditService,
        FeaturePurchaseService $purchaseService,
        EgiliService $egiliService
    ) {
        $this->middleware('auth');
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->auditService = $auditService;
        $this->purchaseService = $purchaseService;
        $this->egiliService = $egiliService;
    }
    
    /**
     * Show feature purchase form
     * 
     * @param string $code Feature code
     * @return \Illuminate\View\View|RedirectResponse
     */
    public function showPurchaseForm(string $code)
    {
        try {
            $user = Auth::user();
            
            // ULM: Log form access
            $this->logger->info('Feature purchase form accessed', [
                'user_id' => $user->id,
                'feature_code' => $code,
                'log_category' => 'FEATURE_PURCHASE_FORM_ACCESS'
            ]);
            
            // Get pricing from catalog
            $pricing = $this->purchaseService->getFeaturePricing($code);
            
            if (!$pricing) {
                return redirect()->back()
                    ->with('error', __('features.not_found'));
            }
            
            // Check if already purchased
            if ($this->purchaseService->userHasFeature($user, $code)) {
                return redirect()->back()
                    ->with('info', __('features.already_owned'));
            }
            
            // Get user Egili balance
            $egiliBalance = $this->egiliService->getBalance($user);
            $canPayWithEgili = $pricing->cost_egili && $egiliBalance >= $pricing->cost_egili;
            
            // GDPR: Audit trail
            $this->auditService->logUserAction(
                $user,
                'feature_purchase_form_viewed',
                [
                    'feature_code' => $code,
                    'feature_name' => $pricing->feature_name,
                ],
                GdprActivityCategory::BLOCKCHAIN_ACTIVITY
            );
            
            return view('features.purchase-form', compact(
                'pricing',
                'egiliBalance',
                'canPayWithEgili'
            ));
            
        } catch (\Exception $e) {
            return $this->errorManager->handle('FEATURE_PURCHASE_FORM_ERROR', [
                'user_id' => Auth::id(),
                'feature_code' => $code,
                'error' => $e->getMessage(),
            ], $e);
        }
    }
    
    /**
     * Process feature purchase
     * 
     * @param Request $request
     * @return RedirectResponse
     */
    public function processPurchase(Request $request): RedirectResponse
    {
        try {
            $user = Auth::user();
            
            $validated = $request->validate([
                'feature_code' => 'required|string|exists:ai_feature_pricing,feature_code',
                'payment_method' => 'required|string|in:egili',  // SOLO Egili
                'source_type' => 'nullable|string',
                'source_id' => 'nullable|integer',
            ]);
            
            // ULM: Log purchase processing
            $this->logger->info('Feature purchase processing', [
                'user_id' => $user->id,
                'feature_code' => $validated['feature_code'],
                'payment_method' => $validated['payment_method'],
                'log_category' => 'FEATURE_PURCHASE_PROCESS'
            ]);
            
            $metadata = [
                'source_type' => $validated['source_type'] ?? null,
                'source_id' => $validated['source_id'] ?? null,
            ];
            
            // SIMPLIFIED: Solo Egili payment
            $purchase = $this->purchaseService->purchaseWithEgili(
                $user,
                $validated['feature_code'],
                $metadata
            );
            
            return redirect()->back()
                ->with('success', __('features.purchase.egili_success'));
            
        } catch (\Exception $e) {
            return $this->errorManager->handle('FEATURE_PURCHASE_PROCESS_ERROR', [
                'user_id' => Auth::id(),
                'feature_code' => $request->input('feature_code'),
                'payment_method' => $request->input('payment_method'),
                'error' => $e->getMessage(),
            ], $e);
        }
    }
}

