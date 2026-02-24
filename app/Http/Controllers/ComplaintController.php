<?php

namespace App\Http\Controllers;

use App\Http\Requests\ComplaintRequest;
use App\Models\Complaint;
use App\Notifications\Dsa\ComplaintReceivedNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * DSA Complaint Controller
 *
 * Handles the Digital Services Act (Reg. UE 2022/2065) complaint system:
 * - Art. 16: Notice-and-Action mechanism
 * - Art. 20: Internal complaint handling
 *
 * Phase 1: User-facing complaint form + tracking.
 */
class ComplaintController extends Controller
{
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;

    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
    }

    /**
     * Show complaint form + user's past complaints.
     */
    public function index(): View
    {
        try {
            $user = Auth::user();

            $this->logger->info('DSA: Accessing complaints page', [
                'user_id' => $user->id,
                'log_category' => 'DSA_COMPLAINT',
            ]);

            $previousComplaints = $user->complaints()
                ->orderBy('created_at', 'desc')
                ->get();

            return view('complaints.index', [
                'user' => $user,
                'previousComplaints' => $previousComplaints,
                'complaintTypes' => Complaint::TYPES,
                'contentTypes' => Complaint::CONTENT_TYPES,
                'pageTitle' => __('complaints.title'),
                'pageDescription' => __('complaints.subtitle'),
            ]);
        } catch (\Exception $e) {
            return $this->errorManager->handle('DSA_COMPLAINTS_PAGE_LOAD_ERROR', [
                'user_id' => Auth::id(),
                'error_message' => $e->getMessage(),
            ], $e);
        }
    }

    /**
     * Store a new complaint.
     */
    public function store(ComplaintRequest $request): RedirectResponse
    {
        try {
            $user = Auth::user();
            $validated = $request->validated();

            $complaint = Complaint::create([
                'type' => $validated['type'],
                'status' => 'received',
                'reporter_user_id' => $user->id,
                'reported_content_type' => $validated['reported_content_type'] ?? null,
                'reported_content_id' => $validated['reported_content_id'] ?? null,
                'reported_user_id' => $validated['reported_user_id'] ?? null,
                'description' => $validated['description'],
                'evidence_urls' => !empty($validated['evidence_urls']) ? $validated['evidence_urls'] : null,
            ]);

            $this->logger->info('DSA: Complaint submitted', [
                'complaint_id' => $complaint->id,
                'user_id' => $user->id,
                'type' => $complaint->type,
                'reference' => $complaint->complaint_reference,
                'log_category' => 'DSA_COMPLAINT',
            ]);

            $user->notify(new ComplaintReceivedNotification($complaint));

            return redirect()->route('complaints.index')
                ->with('success', __('complaints.submitted_successfully', [
                    'reference' => $complaint->complaint_reference,
                ]));
        } catch (\Exception $e) {
            return $this->errorManager->handle('DSA_COMPLAINT_SUBMIT_ERROR', [
                'user_id' => Auth::id(),
                'error_message' => $e->getMessage(),
            ], $e);
        }
    }

    /**
     * Show detail of a specific complaint.
     */
    public function show(Complaint $complaint): View
    {
        try {
            $user = Auth::user();

            if ($complaint->reporter_user_id !== $user->id) {
                abort(403);
            }

            return view('complaints.show', [
                'user' => $user,
                'complaint' => $complaint,
                'pageTitle' => __('complaints.detail_title') . ' - ' . $complaint->complaint_reference,
                'pageDescription' => __('complaints.detail_title'),
            ]);
        } catch (\Exception $e) {
            return $this->errorManager->handle('DSA_COMPLAINT_SHOW_ERROR', [
                'user_id' => Auth::id(),
                'complaint_id' => $complaint->id,
                'error_message' => $e->getMessage(),
            ], $e);
        }
    }
}
