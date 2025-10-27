<?php

namespace App\Http\Controllers\PA;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Services\Projects\ProjectService;
use App\Services\Gdpr\AuditLogService;
use App\Enums\Gdpr\GdprActivityCategory;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * ProjectController
 *
 * PA Projects management - Document upload and priority RAG
 *
 * @package App\Http\Controllers\PA
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Projects System)
 * @date 2025-10-27
 * @purpose Manage PA user projects with document upload and RAG capabilities
 *
 * GDPR Compliance:
 * - AuditLogService logs all project operations (create/update/delete)
 * - Projects contain PA documents (potentially sensitive data)
 * - Audit trail mandatory for PA compliance
 *
 * Authorization:
 * - Middleware: auth (Laravel default)
 * - Role check: pa_entity (done in methods via hasRole)
 * - Ownership check: user_id === Auth::id() per ogni operazione
 */
class ProjectController extends Controller {
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;
    protected ProjectService $projectService;
    protected AuditLogService $auditService;

    /**
     * Constructor with dependency injection
     *
     * @param UltraLogManager $logger Ultra logging manager for audit trails
     * @param ErrorManagerInterface $errorManager Ultra error manager for error handling
     * @param ProjectService $projectService Business logic for projects
     * @param AuditLogService $auditService GDPR audit logging service
     */
    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        ProjectService $projectService,
        AuditLogService $auditService
    ) {
        $this->middleware(['auth']);

        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->projectService = $projectService;
        $this->auditService = $auditService;
    }

    /**
     * Display projects list
     *
     * GET /pa/projects
     *
     * Supports:
     * - Search (name, description)
     * - Status filter (active/inactive)
     * - Pagination (15 items/page)
     */
    public function index(Request $request): View|RedirectResponse {
        try {
            $user = Auth::user();

            // Authorization: Check pa_entity role OR superadmin
            if (!$user->hasRole('pa_entity') && !$user->hasRole('superadmin')) {
                $this->logger->warning('[ProjectController] Unauthorized access attempt to projects index', [
                    'user_id' => $user->id,
                    'roles' => $user->roles->pluck('name')->toArray(),
                ]);
                abort(403, __('projects.unauthorized'));
            }

            // Validate filters
            $validated = $request->validate([
                'search' => 'nullable|string|max:255',
                'status' => 'nullable|in:active,inactive',
            ]);

            // Build query
            $query = $user->projects()->with(['documents', 'chatMessages']);

            // Apply search filter
            if (!empty($validated['search'])) {
                $search = $validated['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Apply status filter
            if (isset($validated['status'])) {
                $isActive = $validated['status'] === 'active';
                $query->where('is_active', $isActive);
            }

            // Order and paginate
            $query->orderBy('updated_at', 'desc');
            $projects = $query->paginate(15)->withQueryString();

            // ULM: Log access
            $this->logger->info('[ProjectController] Projects index accessed', [
                'user_id' => $user->id,
                'filters' => $validated,
                'results_count' => $projects->total(),
            ]);

            return view('pa.natan.projects.index', compact('projects'));
        } catch (\Exception $e) {
            // UEM: Error handling
            $this->errorManager->handle('PROJECT_INDEX_ERROR', [
                'user_id' => Auth::id(),
            ], $e);

            return redirect()->route('pa.dashboard')->withErrors([
                'error' => __('errors.generic_error')
            ]);
        }
    }

    /**
     * Show create project form
     *
     * GET /pa/projects/create
     */
    public function create(): View|RedirectResponse {
        try {
            $user = Auth::user();

            if (!$user->hasRole('pa_entity') && !$user->hasRole('superadmin')) {
                abort(403, __('projects.unauthorized'));
            }

            return view('pa.projects.create');
        } catch (\Exception $e) {
            $this->errorManager->handle('PROJECT_CREATE_PAGE_ERROR', [
                'user_id' => Auth::id(),
            ], $e);

            return redirect()->route('pa.projects.index')->withErrors([
                'error' => __('errors.generic_error')
            ]);
        }
    }

    /**
     * Store new project
     *
     * POST /pa/projects
     */
    public function store(Request $request): RedirectResponse|JsonResponse {
        try {
            $user = Auth::user();

            if (!$user->hasRole('pa_entity') && !$user->hasRole('superadmin')) {
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => __('projects.unauthorized')], 403);
                }
                abort(403, __('projects.unauthorized'));
            }

            // 1. ULM: Log operation start
            $this->logger->info('[ProjectController] Creating project', [
                'user_id' => $user->id,
                'name' => $request->input('name'),
            ]);

            // 2. Validation
            $validated = $request->validate([
                'name' => 'required|string|max:100',
                'description' => 'nullable|string|max:1000',
                'icon' => 'nullable|string|max:50',
                'color' => 'nullable|string|size:7',
            ]);

            // 3. Create project (service handles limit check)
            $project = $this->projectService->createProject($user, $validated);

            // 4. GDPR: Audit trail
            $this->auditService->logUserAction(
                $user,
                'project_created',
                [
                    'project_id' => $project->id,
                    'project_name' => $project->name,
                ],
                GdprActivityCategory::PLATFORM_USAGE
            );

            // 5. ULM: Log success
            $this->logger->info('[ProjectController] Project created successfully', [
                'project_id' => $project->id,
                'user_id' => $user->id,
            ]);

            // ✨ NEW v4.0 - Support AJAX requests from chat modal
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('projects.created_successfully', ['name' => $project->name]),
                    'project' => [
                        'id' => $project->id,
                        'name' => $project->name,
                        'description' => $project->description,
                        'icon' => $project->icon,
                        'color' => $project->color,
                        'documents_count' => 0,
                        'chats_count' => 0,
                    ]
                ], 201);
            }

            return redirect()
                ->route('pa.projects.show', $project)
                ->with('success', __('projects.created_successfully', ['name' => $project->name]));
        } catch (\Exception $e) {
            // 6. UEM: Error handling
            $this->errorManager->handle('PROJECT_CREATE_FAILED', [
                'user_id' => Auth::id(),
                'request_data' => $request->except(['_token']),
            ], $e);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 422);
            }

            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Show project detail (tab-based UI)
     *
     * GET /pa/projects/{project}
     */
    public function show(Project $project): View|RedirectResponse {
        try {
            $user = Auth::user();

            // Authorization: ownership check
            if ($project->user_id !== $user->id) {
                abort(403, __('projects.unauthorized'));
            }

            // ULM: Log access
            $this->logger->info('[ProjectController] Project detail accessed', [
                'project_id' => $project->id,
                'user_id' => $user->id,
            ]);

            $project->load(['documents', 'chatMessages']);
            $statistics = $this->projectService->getProjectStatistics($project);

            return view('pa.projects.show', compact('project', 'statistics'));
        } catch (\Exception $e) {
            $this->errorManager->handle('PROJECT_SHOW_ERROR', [
                'project_id' => $project->id ?? null,
                'user_id' => Auth::id(),
            ], $e);

            return redirect()->route('pa.projects.index')->withErrors([
                'error' => __('errors.generic_error')
            ]);
        }
    }

    /**
     * Show edit project form
     *
     * GET /pa/projects/{project}/edit
     */
    public function edit(Project $project): View|RedirectResponse {
        try {
            $user = Auth::user();

            if ($project->user_id !== $user->id) {
                abort(403, __('projects.unauthorized'));
            }

            return view('pa.projects.edit', compact('project'));
        } catch (\Exception $e) {
            $this->errorManager->handle('PROJECT_EDIT_PAGE_ERROR', [
                'project_id' => $project->id ?? null,
                'user_id' => Auth::id(),
            ], $e);

            return redirect()->route('pa.projects.index')->withErrors([
                'error' => __('errors.generic_error')
            ]);
        }
    }

    /**
     * Update project
     *
     * PUT /pa/projects/{project}
     */
    public function update(Request $request, Project $project): RedirectResponse {
        try {
            $user = Auth::user();

            if ($project->user_id !== $user->id) {
                abort(403, __('projects.unauthorized'));
            }

            // 1. ULM: Log operation start
            $this->logger->info('[ProjectController] Updating project', [
                'project_id' => $project->id,
                'user_id' => $user->id,
            ]);

            // 2. Validation
            $validated = $request->validate([
                'name' => 'required|string|max:100',
                'description' => 'nullable|string|max:1000',
                'icon' => 'nullable|string|max:50',
                'color' => 'nullable|string|size:7',
            ]);

            // 3. Update project
            $this->projectService->updateProject($project, $validated);

            // 4. GDPR: Audit trail
            $this->auditService->logUserAction(
                $user,
                'project_updated',
                [
                    'project_id' => $project->id,
                    'changes' => array_keys($validated),
                ],
                GdprActivityCategory::PLATFORM_USAGE
            );

            // 5. ULM: Log success
            $this->logger->info('[ProjectController] Project updated successfully', [
                'project_id' => $project->id,
            ]);

            return redirect()
                ->route('pa.projects.show', $project)
                ->with('success', __('projects.updated_successfully'));
        } catch (\Exception $e) {
            // 6. UEM: Error handling
            $this->errorManager->handle('PROJECT_UPDATE_FAILED', [
                'project_id' => $project->id ?? null,
                'user_id' => Auth::id(),
            ], $e);

            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Delete project (soft delete)
     *
     * DELETE /pa/projects/{project}
     */
    public function destroy(Project $project): RedirectResponse {
        try {
            $user = Auth::user();

            if ($project->user_id !== $user->id) {
                abort(403, __('projects.unauthorized'));
            }

            // Store project name before deletion
            $projectName = $project->name;

            // 1. ULM: Log operation start
            $this->logger->info('[ProjectController] Deleting project', [
                'project_id' => $project->id,
                'user_id' => $user->id,
            ]);

            // 2. Delete project (soft delete)
            $this->projectService->deleteProject($project);

            // 3. GDPR: Audit trail
            $this->auditService->logUserAction(
                $user,
                'project_deleted',
                [
                    'project_id' => $project->id,
                    'project_name' => $projectName,
                ],
                GdprActivityCategory::PLATFORM_USAGE
            );

            // 4. ULM: Log success
            $this->logger->info('[ProjectController] Project deleted successfully', [
                'project_id' => $project->id,
            ]);

            return redirect()
                ->route('pa.projects.index')
                ->with('success', __('projects.deleted_successfully'));
        } catch (\Exception $e) {
            // 5. UEM: Error handling
            $this->errorManager->handle('PROJECT_DELETE_FAILED', [
                'project_id' => $project->id ?? null,
                'user_id' => Auth::id(),
            ], $e);

            return redirect()
                ->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Upload document to project
     *
     * POST /pa/projects/{project}/documents/upload
     *
     * ✨ NEW v4.0 - Document upload for Priority RAG
     *
     * @param Request $request
     * @param Project $project
     * @return JsonResponse
     */
    public function uploadDocument(Request $request, Project $project): JsonResponse {
        try {
            $user = Auth::user();

            // 1. Authorization: Owner check
            if ($project->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => __('projects.unauthorized')
                ], 403);
            }

            // 2. ULM: Log operation start
            $this->logger->info('[ProjectController] Uploading document to project', [
                'user_id' => $user->id,
                'project_id' => $project->id,
                'filename' => $request->file('document')?->getClientOriginalName(),
            ]);

            // 3. Validation
            $validated = $request->validate([
                'document' => 'required|file|mimes:pdf,docx,txt,md|max:10240', // 10MB max
            ]);

            // 4. Upload and process document via service
            $document = $this->projectService->uploadDocument($project, $validated['document'], $user);

            // 5. GDPR: Audit trail
            $this->auditService->logUserAction(
                $user,
                'project_document_uploaded',
                [
                    'project_id' => $project->id,
                    'document_id' => $document->id,
                    'filename' => $document->filename,
                ],
                GdprActivityCategory::PLATFORM_USAGE
            );

            // 6. ULM: Log success
            $this->logger->info('[ProjectController] Document uploaded successfully', [
                'document_id' => $document->id,
                'project_id' => $project->id,
                'user_id' => $user->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => __('projects.document_uploaded_successfully', ['filename' => $document->filename]),
                'document' => [
                    'id' => $document->id,
                    'filename' => $document->filename,
                    'status' => $document->status,
                    'created_at' => $document->created_at->format('d/m/Y H:i'),
                ]
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validation errors
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            // 7. UEM: Error handling
            $this->errorManager->handle('PROJECT_DOCUMENT_UPLOAD_FAILED', [
                'project_id' => $project->id ?? null,
                'user_id' => Auth::id(),
                'filename' => $request->file('document')?->getClientOriginalName(),
            ], $e);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ✨ NEW v4.0 - Set active project in session (for chat context)
     *
     * POST /pa/projects/set-active
     */
    public function setActive(Request $request): JsonResponse {
        try {
            $user = Auth::user();

            $validated = $request->validate([
                'project_id' => 'required|integer|exists:projects,id',
            ]);

            $project = Project::findOrFail($validated['project_id']);

            // Authorization: Owner check
            if ($project->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => __('projects.unauthorized')
                ], 403);
            }

            // Set active project in session
            session(['active_project_id' => $project->id]);

            // GDPR: Audit trail
            $this->auditService->logUserAction(
                $user,
                'project_set_active',
                [
                    'project_id' => $project->id,
                    'project_name' => $project->name,
                ],
                GdprActivityCategory::PLATFORM_USAGE
            );

            $this->logger->info('[ProjectController] Project set as active', [
                'project_id' => $project->id,
                'user_id' => $user->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => __('projects.set_active_success', ['name' => $project->name]),
                'project' => [
                    'id' => $project->id,
                    'name' => $project->name,
                ]
            ]);
        } catch (\Exception $e) {
            $this->errorManager->handle('PROJECT_SET_ACTIVE_FAILED', [
                'user_id' => Auth::id(),
                'project_id' => $request->input('project_id'),
            ], $e);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ✨ NEW v4.0 - Remove active project from session (return to generic chat)
     *
     * POST /pa/projects/remove-active
     */
    public function removeActive(): JsonResponse {
        try {
            $user = Auth::user();

            $activeProjectId = session('active_project_id');

            // Remove from session
            session()->forget('active_project_id');

            // GDPR: Audit trail
            $this->auditService->logUserAction(
                $user,
                'project_removed_active',
                [
                    'previous_project_id' => $activeProjectId,
                ],
                GdprActivityCategory::PLATFORM_USAGE
            );

            $this->logger->info('[ProjectController] Active project removed', [
                'user_id' => $user->id,
                'previous_project_id' => $activeProjectId,
            ]);

            return response()->json([
                'success' => true,
                'message' => __('projects.remove_active_success'),
            ]);
        } catch (\Exception $e) {
            $this->errorManager->handle('PROJECT_REMOVE_ACTIVE_FAILED', [
                'user_id' => Auth::id(),
            ], $e);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
