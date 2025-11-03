<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\FeaturedSchedulingService;
use App\Models\UserFeaturePurchase;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * @package App\Http\Controllers\Admin
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Admin Featured Calendar)
 * @date 2025-11-02
 * @purpose Admin panel for Featured/Hyper slot scheduling and approval
 */
class FeaturedCalendarController extends Controller
{
    private UltraLogManager $logger;
    private ErrorManagerInterface $errorManager;
    private FeaturedSchedulingService $schedulingService;
    
    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        FeaturedSchedulingService $schedulingService
    ) {
        $this->middleware(['auth', 'role:admin|superadmin']);
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->schedulingService = $schedulingService;
    }
    
    /**
     * Display calendar view (TODO: Full calendar implementation)
     */
    public function calendar(Request $request): View
    {
        // Placeholder - full calendar in next iteration
        return view('admin.featured.calendar');
    }
    
    /**
     * Display pending requests queue
     */
    public function pending(Request $request): View
    {
        $pendingRequests = $this->schedulingService->getPendingRequests();
        
        $this->logger->info('Admin featured pending queue accessed', [
            'admin_id' => Auth::id(),
            'pending_count' => $pendingRequests->count(),
            'log_category' => 'ADMIN_FEATURED_PENDING'
        ]);
        
        return view('admin.featured.pending', compact('pendingRequests'));
    }
    
    /**
     * Approve featured request
     */
    public function approve(Request $request, int $id): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'slot_start' => 'required|date|after:now',
                'slot_end' => 'required|date|after:slot_start',
                'notes' => 'nullable|string',
            ]);
            
            $featureRequest = UserFeaturePurchase::findOrFail($id);
            
            $this->schedulingService->scheduleSlot(
                $featureRequest,
                Auth::user(),
                Carbon::parse($validated['slot_start']),
                Carbon::parse($validated['slot_end']),
                $validated['notes'] ?? null
            );
            
            return redirect()->back()
                ->with('success', __('admin.featured.approved_successfully'));
                
        } catch (\Exception $e) {
            $this->errorManager->handle('ADMIN_FEATURED_APPROVE_FAILED', [
                'admin_id' => Auth::id(),
                'request_id' => $id,
            ], $e);
            
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }
    
    /**
     * Reject featured request
     */
    public function reject(Request $request, int $id): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'reason' => 'required|string|max:500',
            ]);
            
            $featureRequest = UserFeaturePurchase::findOrFail($id);
            
            $this->schedulingService->rejectSlot(
                $featureRequest,
                Auth::user(),
                $validated['reason']
            );
            
            return redirect()->back()
                ->with('success', __('admin.featured.rejected_successfully'));
                
        } catch (\Exception $e) {
            $this->errorManager->handle('ADMIN_FEATURED_REJECT_FAILED', [
                'admin_id' => Auth::id(),
                'request_id' => $id,
            ], $e);
            
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }
}
