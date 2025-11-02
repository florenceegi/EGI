<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\FeaturePromotionService;
use App\Models\FeaturePromotion;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * @package App\Http\Controllers\Admin
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Admin Promotions Manager)
 * @date 2025-11-02
 * @purpose Admin panel for managing feature promotions (Black Friday, bundles, etc)
 */
class FeaturePromotionController extends Controller
{
    private UltraLogManager $logger;
    private ErrorManagerInterface $errorManager;
    private FeaturePromotionService $promotionService;
    
    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        FeaturePromotionService $promotionService
    ) {
        $this->middleware(['auth', 'role:admin|superadmin']);
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->promotionService = $promotionService;
    }
    
    /**
     * Display promotions list
     */
    public function index(Request $request): View
    {
        $status = $request->get('status', 'active');
        
        $query = FeaturePromotion::with('createdByAdmin')
            ->orderBy('created_at', 'desc');
        
        if ($status === 'active') {
            $query->valid();
        } elseif ($status === 'expired') {
            $query->expired();
        } elseif ($status === 'upcoming') {
            $query->upcoming();
        }
        
        $promotions = $query->paginate(15);
        
        $this->logger->info('Admin promotions accessed', [
            'admin_id' => Auth::id(),
            'status_filter' => $status,
            'log_category' => 'ADMIN_PROMO_ACCESS'
        ]);
        
        return view('admin.promotions.index', compact('promotions'));
    }
    
    /**
     * Store new promotion
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'promo_code' => 'required|string|max:50|unique:feature_promotions,promo_code',
                'promo_name' => 'required|string|max:255',
                'promo_description' => 'nullable|string',
                'is_global' => 'boolean',
                'feature_code' => 'nullable|exists:ai_feature_pricing,feature_code',
                'discount_type' => 'required|in:percentage,fixed_amount',
                'discount_value' => 'required|numeric|min:0',
                'start_at' => 'required|date',
                'end_at' => 'required|date|after:start_at',
                'max_uses' => 'nullable|integer|min:1',
                'max_uses_per_user' => 'nullable|integer|min:1',
                'is_active' => 'boolean',
                'is_featured' => 'boolean',
                'badge_text' => 'nullable|string|max:50',
                'admin_notes' => 'nullable|string',
            ]);
            
            $promo = $this->promotionService->createPromotion($validated, Auth::user());
            
            return redirect()->route('admin.promotions.index')
                ->with('success', __('admin.promotions.created_successfully'));
                
        } catch (\Exception $e) {
            $this->errorManager->handle('ADMIN_PROMO_CREATE_FAILED', [
                'admin_id' => Auth::id(),
            ], $e);
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }
    
    /**
     * Activate promotion
     */
    public function activate(int $id): RedirectResponse
    {
        try {
            $this->promotionService->activatePromotion($id, Auth::user());
            
            return redirect()->route('admin.promotions.index')
                ->with('success', __('admin.promotions.activated_successfully'));
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }
    
    /**
     * Deactivate promotion
     */
    public function deactivate(int $id): RedirectResponse
    {
        try {
            $this->promotionService->deactivatePromotion($id, Auth::user());
            
            return redirect()->route('admin.promotions.index')
                ->with('success', __('admin.promotions.deactivated_successfully'));
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }
}
