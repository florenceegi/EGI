<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\EgiliService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * @package App\Http\Controllers\Admin
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Admin Egili Management)
 * @date 2025-11-02
 * @purpose SuperAdmin panel for Egili grants, refunds, corrections
 * 
 * Access: SuperAdmin ONLY
 */
class EgiliManagementController extends Controller
{
    private UltraLogManager $logger;
    private ErrorManagerInterface $errorManager;
    private EgiliService $egiliService;
    
    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        EgiliService $egiliService
    ) {
        $this->middleware(['auth', 'role:superadmin']);
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->egiliService = $egiliService;
    }
    
    /**
     * Display Egili management dashboard
     */
    public function index(Request $request): View
    {
        // Get recent transactions (last 50)
        $recentTransactions = \App\Models\EgiliTransaction::with(['user', 'admin', 'grantedByAdmin'])
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();
        
        $this->logger->info('SuperAdmin Egili management accessed', [
            'admin_id' => Auth::id(),
            'log_category' => 'ADMIN_EGILI_ACCESS'
        ]);
        
        return view('admin.egili.index', compact('recentTransactions'));
    }
    
    /**
     * Grant Lifetime Egili
     */
    public function grantLifetime(Request $request): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'amount' => 'required|integer|min:1',
                'reason' => 'required|string|max:255',
                'notes' => 'nullable|string',
            ]);
            
            $user = User::findOrFail($validated['user_id']);
            
            $transaction = $this->egiliService->grantBonus(
                $user,
                $validated['amount'],
                $validated['reason'],
                Auth::user(),
                $validated['notes'] ?? null
            );
            
            return redirect()->route('admin.egili.index')
                ->with('success', __('admin.egili.granted_successfully'));
                
        } catch (\Exception $e) {
            $this->errorManager->handle('ADMIN_EGILI_GRANT_FAILED', [
                'admin_id' => Auth::id(),
            ], $e);
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }
    
    /**
     * Grant Gift Egili (with expiration)
     */
    public function grantGift(Request $request): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'amount' => 'required|integer|min:1',
                'expiration_days' => 'required|integer|min:1|max:365',
                'reason' => 'required|string|max:255',
                'notes' => 'nullable|string',
            ]);
            
            $user = User::findOrFail($validated['user_id']);
            
            $transaction = $this->egiliService->grantGift(
                $user,
                $validated['amount'],
                $validated['expiration_days'],
                $validated['reason'],
                Auth::user(),
                $validated['notes'] ?? null
            );
            
            return redirect()->route('admin.egili.index')
                ->with('success', __('admin.egili.granted_successfully'));
                
        } catch (\Exception $e) {
            $this->errorManager->handle('ADMIN_EGILI_GRANT_GIFT_FAILED', [
                'admin_id' => Auth::id(),
            ], $e);
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }
}
