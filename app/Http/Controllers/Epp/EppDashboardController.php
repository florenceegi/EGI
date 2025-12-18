<?php

namespace App\Http\Controllers\Epp;

use App\Http\Controllers\Controller;
use App\Models\Collection;
use App\Models\Egi;
use App\Models\EppProject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * @package App\Http\Controllers\Epp
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - EPP User Registration Flow)
 * @date 2025-11-19
 * @purpose EPP user private dashboard - project & collection management
 */
class EppDashboardController extends Controller {
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;

    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;

        // Middleware: only EPP users
        $this->middleware(['auth', 'check.user.type:EPP']);
    }

    /**
     * EPP user dashboard home
     */
    public function index() {
        try {
            $user = Auth::user();

            // ULM: Log dashboard access
            $this->logger->info('EPP Dashboard accessed', [
                'user_id' => $user->id,
                'user_type' => 'EPP',
                'log_category' => 'EPP_DASHBOARD_ACCESS'
            ]);

            // Get user's EPP projects
            $projects = EppProject::where('epp_user_id', $user->id)
                ->withCount('collections')
                ->latest()
                ->get();

            // Get user's collections
            $collections = Collection::where('owner_id', $user->id)
                ->where('epp_project_id', '!=', null)
                ->withCount('egis')
                ->latest()
                ->take(6)
                ->get();

            // Stats
            $stats = [
                'total_projects' => $projects->count(),
                'active_projects' => $projects->where('status', 'in_progress')->count(),
                'total_collections' => Collection::where('owner_id', $user->id)
                    ->where('epp_project_id', '!=', null)
                    ->count(),
                'total_egis' => Egi::whereHas('collection', function ($query) use ($user) {
                    $query->where('user_id', $user->id)
                        ->where('epp_project_id', '!=', null);
                })->count(),
            ];

            // Recent activity (last 5 collections created)
            $recentActivity = Collection::where('owner_id', $user->id)
                ->where('epp_project_id', '!=', null)
                ->with('eppProject')
                ->latest()
                ->take(5)
                ->get();

            return view('epp.dashboard.index', compact(
                'projects',
                'collections',
                'stats',
                'recentActivity'
            ));
        } catch (\Throwable $e) {
            // UEM: Handle error
            return $this->errorManager->handle('EPP_DASHBOARD_LOAD_FAILED', [
                'user_id' => Auth::id(),
                'error_message' => $e->getMessage()
            ], $e);
        }
    }

    /**
     * EPP user projects management
     */
    public function projects() {
        try {
            $user = Auth::user();

            // ULM: Log projects page access
            $this->logger->info('EPP Projects page accessed', [
                'user_id' => $user->id,
                'log_category' => 'EPP_PROJECTS_ACCESS'
            ]);

            // Get all user's EPP projects
            $projects = EppProject::where('epp_user_id', $user->id)
                ->withCount('collections')
                ->latest()
                ->paginate(12);

            // Calculate equilibrium for each project (TODO: implement proper calculation)
            foreach ($projects as $project) {
                $project->equilibrium = 0; // Placeholder
            }

            return view('epp.dashboard.projects.index', compact('projects'));
        } catch (\Exception $e) {
            // UEM: Handle error
            return $this->errorManager->handle('EPP_PROJECTS_LOAD_FAILED', [
                'user_id' => Auth::id(),
                'error_message' => $e->getMessage()
            ], $e);
        }
    }

    /**
     * EPP user collections management
     */
    public function collections() {
        try {
            $user = Auth::user();

            // ULM: Log collections page access
            $this->logger->info('EPP Collections page accessed', [
                'user_id' => $user->id,
                'log_category' => 'EPP_COLLECTIONS_ACCESS'
            ]);

            // Get all user's EPP collections
            $collections = Collection::where('owner_id', $user->id)
                ->where('epp_project_id', '!=', null)
                ->with('eppProject')
                ->withCount('egis')
                ->latest()
                ->paginate(12);

            // Get user's EPP projects for filters
            $projects = EppProject::where('epp_user_id', $user->id)
                ->get();

            return view('epp.dashboard.collections.index', compact('collections', 'projects'));
        } catch (\Exception $e) {
            // UEM: Handle error
            return $this->errorManager->handle('EPP_COLLECTIONS_LOAD_FAILED', [
                'user_id' => Auth::id(),
                'error_message' => $e->getMessage()
            ], $e);
        }
    }

    /**
     * Show form to create new EPP project
     */
    public function createProject() {
        try {
            $user = Auth::user();

            // ULM: Log create form access
            $this->logger->info('EPP Project create form accessed', [
                'user_id' => $user->id,
                'log_category' => 'EPP_PROJECT_CREATE_FORM'
            ]);

            return view('epp.dashboard.projects.create');
        } catch (\Exception $e) {
            return $this->errorManager->handle('EPP_PROJECT_CREATE_FORM_FAILED', [
                'user_id' => Auth::id(),
                'error_message' => $e->getMessage()
            ], $e);
        }
    }

    /**
     * Store new EPP project
     */
    public function storeProject(Request $request) {
        try {
            $user = Auth::user();

            // Validation
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'project_type' => 'required|in:ARF,APR,BPE',
                'description' => 'required|string',
                'target_value' => 'nullable|numeric|min:0',
                'target_date' => 'nullable|date|after:today',
                'status' => 'required|in:planned,in_progress,completed,cancelled',
                'evidence_url' => 'nullable|url',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            ]);

            // ULM: Log project creation start
            $this->logger->info('EPP Project creation initiated', [
                'user_id' => $user->id,
                'project_name' => $validated['name'],
                'log_category' => 'EPP_PROJECT_CREATE_START'
            ]);

            // Create project
            $project = EppProject::create([
                'epp_user_id' => $user->id,
                'name' => $validated['name'],
                'project_type' => $validated['project_type'],
                'description' => $validated['description'],
                'target_value' => $validated['target_value'],
                'current_value' => 0,
                'target_date' => $validated['target_date'] ?? null,
                'status' => $validated['status'],
                'evidence_url' => $validated['evidence_url'] ?? null,
                'current_funds' => 0,
            ]);

            // Handle Banner Image Upload
            if ($request->hasFile('image')) {
                $project->addMediaFromRequest('image')
                    ->toMediaCollection('project_images');
            }
            
            // Handle Avatar Image Upload
            if ($request->hasFile('avatar')) {
                $project->addMediaFromRequest('avatar')
                    ->toMediaCollection('project_avatar');
            }

            // ULM: Log success
            $this->logger->info('EPP Project created successfully', [
                'user_id' => $user->id,
                'project_id' => $project->id,
                'project_name' => $project->name,
                'log_category' => 'EPP_PROJECT_CREATED'
            ]);

            return redirect()->route('epp.dashboard.projects')
                ->with('success', __('epp_dashboard.projects.created_successfully'));
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            return $this->errorManager->handle('EPP_PROJECT_CREATE_FAILED', [
                'user_id' => Auth::id(),
                'error_message' => $e->getMessage(),
                'input_data' => $request->except(['_token', 'image', 'avatar'])
            ], $e);
        }
    }

    /**
     * Show form to edit EPP project
     */
    public function editProject(EppProject $project) {
        try {
            $user = Auth::user();

            // Authorization: only project owner
            if ($project->epp_user_id !== $user->id) {
                abort(403, 'Unauthorized');
            }

            // ULM: Log edit form access
            $this->logger->info('EPP Project edit form accessed', [
                'user_id' => $user->id,
                'project_id' => $project->id,
                'log_category' => 'EPP_PROJECT_EDIT_FORM'
            ]);

            return view('epp.dashboard.projects.edit', compact('project'));
        } catch (\Exception $e) {
            return $this->errorManager->handle('EPP_PROJECT_EDIT_FORM_FAILED', [
                'user_id' => Auth::id(),
                'project_id' => $project->id ?? null,
                'error_message' => $e->getMessage()
            ], $e);
        }
    }

    /**
     * Update EPP project
     */
    public function updateProject(Request $request, EppProject $project) {
        try {
            $user = Auth::user();

            // Authorization: only project owner
            if ($project->epp_user_id !== $user->id) {
                abort(403, 'Unauthorized');
            }

            // Validation
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'project_type' => 'required|in:ARF,APR,BPE',
                'description' => 'required|string',
                'target_value' => 'nullable|numeric|min:0',
                'current_value' => 'nullable|numeric|min:0',
                'target_date' => 'nullable|date',
                'status' => 'required|in:planned,in_progress,completed,cancelled',
                'evidence_url' => 'nullable|url',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            ]);

            // ULM: Log update start
            $this->logger->info('EPP Project update initiated', [
                'user_id' => $user->id,
                'project_id' => $project->id,
                'log_category' => 'EPP_PROJECT_UPDATE_START'
            ]);

            // Update project
            $project->update([
                'name' => $validated['name'],
                'project_type' => $validated['project_type'],
                'description' => $validated['description'],
                'target_value' => $validated['target_value'],
                'current_value' => $validated['current_value'] ?? $project->current_value,
                'target_date' => $validated['target_date'] ?? null,
                'status' => $validated['status'],
                'evidence_url' => $validated['evidence_url'] ?? null,
            ]);

            // Handle Banner Image Upload
            if ($request->hasFile('image')) {
                $project->clearMediaCollection('project_images'); // Replace existing image
                $project->addMediaFromRequest('image')
                    ->toMediaCollection('project_images');
            }
            
            // Handle Avatar Image Upload
            if ($request->hasFile('avatar')) {
                $project->clearMediaCollection('project_avatar'); // Replace existing avatar
                $project->addMediaFromRequest('avatar')
                    ->toMediaCollection('project_avatar');
            }

            // ULM: Log success
            $this->logger->info('EPP Project updated successfully', [
                'user_id' => $user->id,
                'project_id' => $project->id,
                'log_category' => 'EPP_PROJECT_UPDATED'
            ]);

            return redirect()->route('epp.dashboard.projects')
                ->with('success', __('epp_dashboard.projects.updated_successfully'));
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            return $this->errorManager->handle('EPP_PROJECT_UPDATE_FAILED', [
                'user_id' => Auth::id(),
                'project_id' => $project->id,
                'error_message' => $e->getMessage()
            ], $e);
        }
    }

    /**
     * Delete EPP project
     */
    public function destroyProject(EppProject $project) {
        try {
            $user = Auth::user();

            // Authorization: only project owner
            if ($project->epp_user_id !== $user->id) {
                abort(403, 'Unauthorized');
            }

            // Check if project has collections
            if ($project->collections()->count() > 0) {
                return redirect()->back()
                    ->withErrors(['error' => __('epp_dashboard.projects.cannot_delete_has_collections')]);
            }

            // ULM: Log deletion
            $this->logger->info('EPP Project deletion initiated', [
                'user_id' => $user->id,
                'project_id' => $project->id,
                'project_name' => $project->name,
                'log_category' => 'EPP_PROJECT_DELETE'
            ]);

            $projectName = $project->name;
            $project->delete();

            // ULM: Log success
            $this->logger->info('EPP Project deleted successfully', [
                'user_id' => $user->id,
                'project_name' => $projectName,
                'log_category' => 'EPP_PROJECT_DELETED'
            ]);

            return redirect()->route('epp.dashboard.projects')
                ->with('success', __('epp_dashboard.projects.deleted_successfully'));
        } catch (\Exception $e) {
            return $this->errorManager->handle('EPP_PROJECT_DELETE_FAILED', [
                'user_id' => Auth::id(),
                'project_id' => $project->id ?? null,
                'error_message' => $e->getMessage()
            ], $e);
        }
    }
}
