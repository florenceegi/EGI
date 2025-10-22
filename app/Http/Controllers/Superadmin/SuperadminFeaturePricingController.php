<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\AiFeaturePricing;
use App\Services\Gdpr\AuditLogService;
use App\Enums\Gdpr\GdprActivityCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * @Oracode Controller: SuperAdmin Feature Pricing Management
 * 🎯 Purpose: CRUD for AI feature pricing (Egili & FIAT costs)
 * 🛡️ Privacy: Full GDPR compliance with audit logging
 * 🧱 Core Logic: UEM error handling + ULM logging + GDPR audit
 *
 * @package App\Http\Controllers\Superadmin
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 2.0.0 (FlorenceEGI - SuperAdmin Platform Management)
 * @date 2025-10-22
 * @purpose SuperAdmin interface for dynamic feature pricing management
 */
class SuperadminFeaturePricingController extends Controller
{
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;
    protected AuditLogService $auditService;

    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        AuditLogService $auditService
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->auditService = $auditService;
    }

    /**
     * Display feature pricing list
     */
    public function index(): View | RedirectResponse
    {
        try {
            // 1. ULM: Log access
            $this->logger->info('SuperAdmin Feature Pricing index accessed', [
                'admin_id' => auth()->id(),
                'log_category' => 'SUPERADMIN_PRICING_ACCESS'
            ]);

            // 2. Get all feature pricing with ordering
            $pricing = AiFeaturePricing::orderBy('min_tier_required')
                ->orderBy('feature_name')
                ->get();

            // 3. GDPR: Log administrative access
            $this->auditService->logUserAction(
                auth()->user(),
                'superadmin_feature_pricing_access',
                ['page' => 'Feature Pricing Management', 'total_features' => $pricing->count()],
                GdprActivityCategory::SYSTEM_INTERACTION
            );

            return view('superadmin.pricing.index', [
                'pricing' => $pricing,
                'pageTitle' => 'Gestione Pricing Features',
            ]);
        } catch (\Exception $e) {
            // 4. ULM: Log error
            $this->logger->error('SuperAdmin Feature Pricing index failed', [
                'error' => $e->getMessage(),
                'log_category' => 'SUPERADMIN_PRICING_ERROR'
            ]);

            // 5. UEM: Handle error
            $this->errorManager->handle('SUPERADMIN_PRICING_INDEX_ERROR', [
                'admin_id' => auth()->id(),
            ], $e);

            // 6. Return redirect response
            return redirect()->route('superadmin.dashboard')
                ->with('error', 'Errore nel caricamento della pagina pricing');
        }
    }

    /**
     * Show create form
     */
    public function create(): View | RedirectResponse
    {
        try {
            return view('superadmin.pricing.create', [
                'pageTitle' => 'Aggiungi Feature Pricing',
            ]);
        } catch (\Exception $e) {
            $this->logger->error('SuperAdmin Feature Pricing create form failed', [
                'error' => $e->getMessage(),
                'log_category' => 'SUPERADMIN_PRICING_CREATE_ERROR'
            ]);

            return redirect()->route('superadmin.pricing.index')
                ->with('error', 'Errore nel caricamento del form');
        }
    }

    /**
     * Store new feature pricing
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'feature_code' => 'required|string|max:100|unique:ai_feature_pricing,feature_code',
                'feature_name' => 'required|string|max:255',
                'feature_description' => 'nullable|string',
                'feature_category' => 'required|in:ai_services,premium_visibility,premium_profile,premium_analytics,exclusive_access,platform_services',
                'cost_egili' => 'nullable|numeric|min:0',
                'cost_fiat_eur' => 'nullable|numeric|min:0',
                'min_tier_required' => 'required|in:free,starter,pro,business,enterprise',
                'is_active' => 'boolean',
            ]);

            $pricing = AiFeaturePricing::create($validated);

            $this->auditService->logUserAction(
                auth()->user(),
                'feature_pricing_created',
                ['pricing_id' => $pricing->id, 'feature_name' => $pricing->feature_name],
                GdprActivityCategory::SYSTEM_INTERACTION
            );

            return redirect()->route('superadmin.pricing.index')
                ->with('success', 'Feature pricing creato con successo');
        } catch (\Exception $e) {
            $this->logger->error('Feature pricing creation failed', [
                'error' => $e->getMessage(),
                'log_category' => 'SUPERADMIN_PRICING_CREATE_ERROR'
            ]);

            return back()->withInput()->with('error', 'Errore nella creazione del pricing');
        }
    }

    /**
     * Show edit form
     */
    public function edit(AiFeaturePricing $pricing): View | RedirectResponse
    {
        try {
            return view('superadmin.pricing.edit', [
                'pricing' => $pricing,
                'pageTitle' => "Modifica Pricing: {$pricing->feature_name}",
            ]);
        } catch (\Exception $e) {
            $this->logger->error('SuperAdmin Feature Pricing edit form failed', [
                'error' => $e->getMessage(),
                'pricing_id' => $pricing->id ?? null,
                'log_category' => 'SUPERADMIN_PRICING_EDIT_ERROR'
            ]);

            return redirect()->route('superadmin.pricing.index')
                ->with('error', 'Errore nel caricamento del form');
        }
    }

    /**
     * Update feature pricing
     */
    public function update(Request $request, AiFeaturePricing $pricing): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'feature_code' => 'required|string|max:100|unique:ai_feature_pricing,feature_code,' . $pricing->id,
                'feature_name' => 'required|string|max:255',
                'feature_description' => 'nullable|string',
                'feature_category' => 'required|in:ai_services,premium_visibility,premium_profile,premium_analytics,exclusive_access,platform_services',
                'cost_egili' => 'nullable|numeric|min:0',
                'cost_fiat_eur' => 'nullable|numeric|min:0',
                'min_tier_required' => 'required|in:free,starter,pro,business,enterprise',
                'is_active' => 'boolean',
            ]);

            $oldData = $pricing->toArray();
            $pricing->update($validated);

            $this->auditService->logUserAction(
                auth()->user(),
                'feature_pricing_updated',
                [
                    'pricing_id' => $pricing->id,
                    'feature_name' => $pricing->feature_name,
                    'old_data' => $oldData,
                    'new_data' => $validated,
                ],
                GdprActivityCategory::SYSTEM_INTERACTION
            );

            return redirect()->route('superadmin.pricing.index')
                ->with('success', 'Feature pricing aggiornato con successo');
        } catch (\Exception $e) {
            $this->logger->error('Feature pricing update failed', [
                'error' => $e->getMessage(),
                'pricing_id' => $pricing->id,
                'log_category' => 'SUPERADMIN_PRICING_UPDATE_ERROR'
            ]);

            return back()->withInput()->with('error', 'Errore nell\'aggiornamento del pricing');
        }
    }

    /**
     * Delete feature pricing
     */
    public function destroy(AiFeaturePricing $pricing): RedirectResponse
    {
        try {
            $featureName = $pricing->feature_name;
            $pricing->delete();

            $this->auditService->logUserAction(
                auth()->user(),
                'feature_pricing_deleted',
                ['feature_name' => $featureName],
                GdprActivityCategory::SYSTEM_INTERACTION
            );

            return redirect()->route('superadmin.pricing.index')
                ->with('success', 'Feature pricing eliminato con successo');
        } catch (\Exception $e) {
            $this->logger->error('Feature pricing deletion failed', [
                'error' => $e->getMessage(),
                'pricing_id' => $pricing->id,
                'log_category' => 'SUPERADMIN_PRICING_DELETE_ERROR'
            ]);

            return back()->with('error', 'Errore nell\'eliminazione del pricing');
        }
    }
}
