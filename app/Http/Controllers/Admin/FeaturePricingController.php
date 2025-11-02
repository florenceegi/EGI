<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Enums\Gdpr\GdprActivityCategory;
use App\Services\Gdpr\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * @package App\Http\Controllers\Admin
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Admin Pricing Manager)
 * @date 2025-11-02
 * @purpose Admin panel for managing feature pricing (CRUD)
 * 
 * Access: Admin + SuperAdmin only
 * 
 * Features:
 * - List all features with pricing
 * - Create new feature
 * - Edit pricing (cost, tier, duration)
 * - Activate/deactivate feature
 * - GDPR audit trail for all changes
 */
class FeaturePricingController extends Controller
{
    private UltraLogManager $logger;
    private ErrorManagerInterface $errorManager;
    private AuditLogService $auditService;
    
    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        AuditLogService $auditService
    ) {
        $this->middleware(['auth', 'role:admin|superadmin']);
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->auditService = $auditService;
    }
    
    /**
     * Display feature pricing list
     * 
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        // Get filters
        $category = $request->get('category');
        $featureType = $request->get('feature_type');
        $status = $request->get('status', 'all');
        $search = $request->get('search');
        
        // Query features
        $query = DB::table('ai_feature_pricing')
            ->orderBy('created_at', 'desc');
        
        if ($category) {
            $query->where('category', $category);
        }
        
        if ($featureType) {
            $query->where('feature_type', $featureType);
        }
        
        if ($status === 'active') {
            $query->where('is_active', true);
        } elseif ($status === 'inactive') {
            $query->where('is_active', false);
        }
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('feature_code', 'like', "%{$search}%")
                  ->orWhere('name_key', 'like', "%{$search}%")
                  ->orWhere('description_key', 'like', "%{$search}%");
            });
        }
        
        $features = $query->paginate(20);
        
        // Get categories for filter
        $categories = DB::table('ai_feature_pricing')
            ->distinct()
            ->pluck('category')
            ->filter()
            ->sort();
        
        // ULM: Log access
        $this->logger->info('Admin pricing manager accessed', [
            'admin_id' => Auth::id(),
            'filters' => compact('category', 'featureType', 'status', 'search'),
            'log_category' => 'ADMIN_PRICING_ACCESS'
        ]);
        
        return view('admin.pricing.index', compact('features', 'categories'));
    }
    
    /**
     * Store new feature
     * 
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'feature_code' => 'required|string|max:100|unique:ai_feature_pricing,feature_code',
                'name_key' => 'required|string|max:255',
                'description_key' => 'nullable|string|max:500',
                'category' => 'required|string|max:100',
                'feature_type' => 'required|in:lifetime,consumable,temporal',
                'cost_egili' => 'required|integer|min:0',
                'cost_per_use' => 'nullable|integer|min:0',
                'lifetime_cost' => 'nullable|integer|min:0',
                'tier_pricing' => 'nullable|json',
                'discount_percentage' => 'nullable|numeric|min:0|max:100',
                'duration_hours' => 'nullable|integer|min:1',
                'max_uses_per_purchase' => 'nullable|integer|min:1',
                'requires_admin_approval' => 'boolean',
                'max_concurrent_slots' => 'nullable|integer|min:1',
                'is_active' => 'boolean',
            ]);
            
            // ULM: Log creation start
            $this->logger->info('Admin creating new feature pricing', [
                'admin_id' => Auth::id(),
                'feature_code' => $validated['feature_code'],
                'log_category' => 'ADMIN_PRICING_CREATE_START'
            ]);
            
            $featureId = DB::table('ai_feature_pricing')->insertGetId($validated);
            
            // GDPR: Audit trail
            $this->auditService->logUserAction(
                Auth::user(),
                'feature_pricing_created',
                [
                    'feature_id' => $featureId,
                    'feature_code' => $validated['feature_code'],
                    'cost_egili' => $validated['cost_egili'],
                ],
                GdprActivityCategory::PERSONAL_DATA_UPDATE
            );
            
            // ULM: Log success
            $this->logger->info('Feature pricing created', [
                'admin_id' => Auth::id(),
                'feature_id' => $featureId,
                'log_category' => 'ADMIN_PRICING_CREATE_SUCCESS'
            ]);
            
            return redirect()->route('admin.pricing.index')
                ->with('success', __('admin.pricing.created_successfully'));
                
        } catch (\Exception $e) {
            $this->errorManager->handle('ADMIN_PRICING_CREATE_FAILED', [
                'admin_id' => Auth::id(),
                'data' => $request->all(),
            ], $e);
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => __('errors.feature_create_failed')]);
        }
    }
    
    /**
     * Update feature pricing
     * 
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'name_key' => 'sometimes|string|max:255',
                'description_key' => 'nullable|string|max:500',
                'category' => 'sometimes|string|max:100',
                'feature_type' => 'sometimes|in:lifetime,consumable,temporal',
                'cost_egili' => 'sometimes|integer|min:0',
                'cost_per_use' => 'nullable|integer|min:0',
                'lifetime_cost' => 'nullable|integer|min:0',
                'tier_pricing' => 'nullable|json',
                'discount_percentage' => 'nullable|numeric|min:0|max:100',
                'duration_hours' => 'nullable|integer|min:1',
                'max_uses_per_purchase' => 'nullable|integer|min:1',
                'requires_admin_approval' => 'boolean',
                'max_concurrent_slots' => 'nullable|integer|min:1',
                'is_active' => 'boolean',
            ]);
            
            $feature = DB::table('ai_feature_pricing')->where('id', $id)->first();
            
            if (!$feature) {
                throw new \Exception("Feature not found");
            }
            
            // ULM: Log update start
            $this->logger->info('Admin updating feature pricing', [
                'admin_id' => Auth::id(),
                'feature_id' => $id,
                'feature_code' => $feature->feature_code,
                'log_category' => 'ADMIN_PRICING_UPDATE_START'
            ]);
            
            DB::table('ai_feature_pricing')
                ->where('id', $id)
                ->update(array_merge($validated, ['updated_at' => now()]));
            
            // GDPR: Audit trail
            $this->auditService->logUserAction(
                Auth::user(),
                'feature_pricing_updated',
                [
                    'feature_id' => $id,
                    'feature_code' => $feature->feature_code,
                    'changes' => $validated,
                ],
                GdprActivityCategory::PERSONAL_DATA_UPDATE
            );
            
            // ULM: Log success
            $this->logger->info('Feature pricing updated', [
                'admin_id' => Auth::id(),
                'feature_id' => $id,
                'log_category' => 'ADMIN_PRICING_UPDATE_SUCCESS'
            ]);
            
            return redirect()->route('admin.pricing.index')
                ->with('success', __('admin.pricing.updated_successfully'));
                
        } catch (\Exception $e) {
            $this->errorManager->handle('ADMIN_PRICING_UPDATE_FAILED', [
                'admin_id' => Auth::id(),
                'feature_id' => $id,
            ], $e);
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => __('errors.feature_update_failed')]);
        }
    }
    
    /**
     * Toggle feature active status
     * 
     * @param int $id
     * @return RedirectResponse
     */
    public function toggleActive(int $id): RedirectResponse
    {
        try {
            $feature = DB::table('ai_feature_pricing')->where('id', $id)->first();
            
            if (!$feature) {
                throw new \Exception("Feature not found");
            }
            
            $newStatus = !$feature->is_active;
            
            DB::table('ai_feature_pricing')
                ->where('id', $id)
                ->update([
                    'is_active' => $newStatus,
                    'updated_at' => now(),
                ]);
            
            // GDPR: Audit trail
            $this->auditService->logUserAction(
                Auth::user(),
                'feature_pricing_toggled',
                [
                    'feature_id' => $id,
                    'feature_code' => $feature->feature_code,
                    'new_status' => $newStatus ? 'active' : 'inactive',
                ],
                GdprActivityCategory::PERSONAL_DATA_UPDATE
            );
            
            // ULM: Log
            $this->logger->info('Feature status toggled', [
                'admin_id' => Auth::id(),
                'feature_id' => $id,
                'new_status' => $newStatus,
                'log_category' => 'ADMIN_PRICING_TOGGLE'
            ]);
            
            $message = $newStatus 
                ? __('admin.pricing.activated_successfully')
                : __('admin.pricing.deactivated_successfully');
            
            return redirect()->route('admin.pricing.index')
                ->with('success', $message);
                
        } catch (\Exception $e) {
            $this->errorManager->handle('ADMIN_PRICING_TOGGLE_FAILED', [
                'admin_id' => Auth::id(),
                'feature_id' => $id,
            ], $e);
            
            return redirect()->back()
                ->withErrors(['error' => __('errors.feature_toggle_failed')]);
        }
    }
}
