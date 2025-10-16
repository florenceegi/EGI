<?php

namespace App\Http\Controllers\PaActs;

use App\Http\Controllers\Controller;
use App\Models\PaBatchSource;
use App\Models\PaBatchJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * @Oracode Controller: PA Batch Source Management
 * 🎯 Purpose: CRUD operations for PA batch sources (monitored directories)
 * 🛡️ Privacy: PA-only access, user-scoped data
 * 🧱 Core Logic: Manage agent scan configurations and monitor processing status
 *
 * @package App\Http\Controllers\PaActs
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - NATAN Batch System)
 * @date 2025-10-16
 */
class PaBatchSourceController extends Controller
{
    /**
     * Display a listing of batch sources with stats
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        // Get all sources for this PA user with job counts
        $sources = PaBatchSource::where('user_id', $user->id)
            ->withCount([
                'jobs as total_jobs',
                'jobs as pending_jobs' => function ($query) {
                    $query->where('status', 'pending');
                },
                'jobs as processing_jobs' => function ($query) {
                    $query->where('status', 'processing');
                },
                'jobs as completed_jobs' => function ($query) {
                    $query->where('status', 'completed');
                },
                'jobs as failed_jobs' => function ($query) {
                    $query->where('status', 'failed');
                },
            ])
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate global stats
        $totalJobs = PaBatchJob::where('user_id', $user->id)->count();
        $completedJobs = PaBatchJob::where('user_id', $user->id)->where('status', 'completed')->count();
        $failedJobs = PaBatchJob::where('user_id', $user->id)->where('status', 'failed')->count();
        $pendingJobs = PaBatchJob::where('user_id', $user->id)->where('status', 'pending')->count();

        return view('pa.batch.index', compact('sources', 'totalJobs', 'completedJobs', 'failedJobs', 'pendingJobs'));
    }

    /**
     * Show the form for creating a new batch source
     */
    public function create(): View
    {
        return view('pa.batch.create');
    }

    /**
     * Store a newly created batch source
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'path' => 'required|string|max:500',
            'file_pattern' => 'nullable|string|max:100',
            'priority' => 'nullable|integer|min:1|max:10',
            'auto_process' => 'boolean',
        ]);

        $user = Auth::user();

        $source = PaBatchSource::create([
            'user_id' => $user->id,
            'created_by_user_id' => $user->id,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'path' => $validated['path'],
            'file_pattern' => $validated['file_pattern'] ?? '*',
            'priority' => $validated['priority'] ?? 5,
            'auto_process' => $validated['auto_process'] ?? true,
            'status' => 'active',
        ]);

        return redirect()
            ->route('pa.batch.show', $source->id)
            ->with('success', __('pa_batch.sources.created_successfully'));
    }

    /**
     * Display the specified batch source with recent jobs
     */
    public function show(Request $request, int $id): View
    {
        $user = Auth::user();

        $source = PaBatchSource::where('user_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

        // Get recent jobs for this source
        $jobs = PaBatchJob::where('source_id', $source->id)
            ->with('egi:id,title,pa_public_code,pa_act_type')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Stats for this source
        $stats = [
            'total' => PaBatchJob::where('source_id', $source->id)->count(),
            'completed' => PaBatchJob::where('source_id', $source->id)->where('status', 'completed')->count(),
            'failed' => PaBatchJob::where('source_id', $source->id)->where('status', 'failed')->count(),
            'pending' => PaBatchJob::where('source_id', $source->id)->where('status', 'pending')->count(),
            'duplicate' => PaBatchJob::where('source_id', $source->id)->where('status', 'duplicate')->count(),
        ];

        return view('pa.batch.show', compact('source', 'jobs', 'stats'));
    }

    /**
     * Show the form for editing the specified batch source
     */
    public function edit(int $id): View
    {
        $user = Auth::user();

        $source = PaBatchSource::where('user_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

        return view('pa.batch.edit', compact('source'));
    }

    /**
     * Update the specified batch source
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'path' => 'required|string|max:500',
            'file_pattern' => 'nullable|string|max:100',
            'priority' => 'nullable|integer|min:1|max:10',
            'auto_process' => 'boolean',
            'status' => 'required|in:active,paused,disabled',
        ]);

        $user = Auth::user();

        $source = PaBatchSource::where('user_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

        $source->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'path' => $validated['path'],
            'file_pattern' => $validated['file_pattern'] ?? '*',
            'priority' => $validated['priority'] ?? 5,
            'auto_process' => $validated['auto_process'] ?? true,
            'status' => $validated['status'],
        ]);

        return redirect()
            ->route('pa.batch.show', $source->id)
            ->with('success', __('pa_batch.sources.updated_successfully'));
    }

    /**
     * Remove the specified batch source
     */
    public function destroy(int $id): RedirectResponse
    {
        $user = Auth::user();

        $source = PaBatchSource::where('user_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

        // Check if there are active jobs
        $activeJobs = PaBatchJob::where('source_id', $source->id)
            ->whereIn('status', ['pending', 'processing'])
            ->count();

        if ($activeJobs > 0) {
            return redirect()
                ->route('pa.batch.show', $source->id)
                ->with('error', __('pa_batch.sources.cannot_delete_with_active_jobs'));
        }

        $source->delete();

        return redirect()
            ->route('pa.batch.index')
            ->with('success', __('pa_batch.sources.deleted_successfully'));
    }

    /**
     * Toggle source status (active/paused)
     */
    public function toggleStatus(int $id): RedirectResponse
    {
        $user = Auth::user();

        $source = PaBatchSource::where('user_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

        $newStatus = $source->status === 'active' ? 'paused' : 'active';
        $source->update(['status' => $newStatus]);

        return redirect()
            ->route('pa.batch.show', $source->id)
            ->with('success', __('pa_batch.sources.status_updated'));
    }
}