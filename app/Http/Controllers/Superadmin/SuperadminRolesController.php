<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Services\Gdpr\AuditLogService;
use App\Enums\Gdpr\GdprActivityCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * @Oracode Controller: SuperAdmin Roles & Permissions Management
 * 🎯 Purpose: Enterprise-grade role management with permission assignment
 * 🛡️ Privacy: Full GDPR compliance with audit logging
 * 🧱 Core Logic: UEM error handling + ULM logging + GDPR audit
 *
 * @package App\Http\Controllers\Superadmin
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 2.0.0 (FlorenceEGI - SuperAdmin RBAC Management)
 * @date 2025-10-22
 * @purpose SuperAdmin enterprise role management center
 */
class SuperadminRolesController extends Controller
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
     * Display roles management center
     */
    public function index(): View | RedirectResponse
    {
        try {
            // 1. ULM: Log access
            $this->logger->info('SuperAdmin Roles center accessed', [
                'admin_id' => auth()->id(),
                'log_category' => 'SUPERADMIN_ROLES_ACCESS'
            ]);

            // 2. Get all roles with permissions and users count
            $roles = Role::withCount('permissions', 'users')
                ->with('permissions')
                ->orderBy('name')
                ->get();

            // 3. GDPR: Log administrative access
            $this->auditService->logUserAction(
                auth()->user(),
                'superadmin_roles_access',
                [
                    'page' => 'Roles Management Center',
                    'total_roles' => $roles->count(),
                ],
                GdprActivityCategory::SYSTEM_INTERACTION
            );

            return view('superadmin.roles.index', [
                'roles' => $roles,
                'pageTitle' => 'Gestione Ruoli & Permessi',
            ]);
        } catch (\Exception $e) {
            $this->logger->error('SuperAdmin Roles index failed', [
                'error' => $e->getMessage(),
                'log_category' => 'SUPERADMIN_ROLES_ERROR'
            ]);

            return $this->errorManager->handle('SUPERADMIN_ROLES_INDEX_ERROR', [
                'admin_id' => auth()->id(),
            ], $e);
        }
    }

    /**
     * Show create role form
     */
    public function create(): View | RedirectResponse
    {
        try {
            // Get all permissions grouped by prefix
            $permissions = Permission::orderBy('name')->get();
            $groupedPermissions = $this->groupPermissionsByModule($permissions);

            return view('superadmin.roles.create', [
                'groupedPermissions' => $groupedPermissions,
                'pageTitle' => 'Crea Nuovo Ruolo',
            ]);
        } catch (\Exception $e) {
            $this->logger->error('SuperAdmin Roles create form failed', [
                'error' => $e->getMessage(),
                'log_category' => 'SUPERADMIN_ROLES_CREATE_ERROR'
            ]);

            return back()->with('error', 'Errore nel caricamento del form');
        }
    }

    /**
     * Store new role
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:roles,name',
                'description' => 'nullable|string|max:500',
                'permissions' => 'required|array|min:1',
                'permissions.*' => 'exists:permissions,id',
            ]);

            // Create role
            $role = Role::create([
                'name' => $validated['name'],
                'guard_name' => 'web',
            ]);

            // Assign permissions
            $permissionNames = Permission::whereIn('id', $validated['permissions'])
                ->pluck('name')
                ->toArray();
            $role->syncPermissions($permissionNames);

            // GDPR: Log role creation
            $this->auditService->logUserAction(
                auth()->user(),
                'role_created',
                [
                    'role_id' => $role->id,
                    'role_name' => $role->name,
                    'permissions_count' => count($permissionNames),
                ],
                GdprActivityCategory::SYSTEM_INTERACTION
            );

            $this->logger->info('Role created successfully', [
                'role_id' => $role->id,
                'role_name' => $role->name,
                'admin_id' => auth()->id(),
                'log_category' => 'SUPERADMIN_ROLE_CREATED'
            ]);

            return redirect()->route('superadmin.roles.index')
                ->with('success', "Ruolo '{$role->name}' creato con successo");
        } catch (\Exception $e) {
            $this->logger->error('Role creation failed', [
                'error' => $e->getMessage(),
                'log_category' => 'SUPERADMIN_ROLES_STORE_ERROR'
            ]);

            return back()->withInput()->with('error', 'Errore nella creazione del ruolo');
        }
    }

    /**
     * Show edit role form
     */
    public function edit(Role $role): View | RedirectResponse
    {
        try {
            $permissions = Permission::orderBy('name')->get();
            $groupedPermissions = $this->groupPermissionsByModule($permissions);
            $rolePermissions = $role->permissions->pluck('id')->toArray();

            return view('superadmin.roles.edit', [
                'role' => $role,
                'groupedPermissions' => $groupedPermissions,
                'rolePermissions' => $rolePermissions,
                'pageTitle' => "Modifica Ruolo: {$role->name}",
            ]);
        } catch (\Exception $e) {
            $this->logger->error('SuperAdmin Roles edit form failed', [
                'error' => $e->getMessage(),
                'role_id' => $role->id ?? null,
                'log_category' => 'SUPERADMIN_ROLES_EDIT_ERROR'
            ]);

            return back()->with('error', 'Errore nel caricamento del form');
        }
    }

    /**
     * Update role
     */
    public function update(Request $request, Role $role): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
                'description' => 'nullable|string|max:500',
                'permissions' => 'required|array|min:1',
                'permissions.*' => 'exists:permissions,id',
            ]);

            $oldData = [
                'name' => $role->name,
                'permissions_count' => $role->permissions->count(),
            ];

            // Update role
            $role->update(['name' => $validated['name']]);

            // Sync permissions
            $permissionNames = Permission::whereIn('id', $validated['permissions'])
                ->pluck('name')
                ->toArray();
            $role->syncPermissions($permissionNames);

            // GDPR: Log role update
            $this->auditService->logUserAction(
                auth()->user(),
                'role_updated',
                [
                    'role_id' => $role->id,
                    'old_data' => $oldData,
                    'new_data' => [
                        'name' => $role->name,
                        'permissions_count' => count($permissionNames),
                    ],
                ],
                GdprActivityCategory::SYSTEM_INTERACTION
            );

            return redirect()->route('superadmin.roles.index')
                ->with('success', "Ruolo '{$role->name}' aggiornato con successo");
        } catch (\Exception $e) {
            $this->logger->error('Role update failed', [
                'error' => $e->getMessage(),
                'role_id' => $role->id,
                'log_category' => 'SUPERADMIN_ROLES_UPDATE_ERROR'
            ]);

            return back()->withInput()->with('error', 'Errore nell\'aggiornamento del ruolo');
        }
    }

    /**
     * Delete role
     */
    public function destroy(Role $role): RedirectResponse
    {
        try {
            // Prevent deletion of critical roles
            if (in_array($role->name, ['superadmin', 'admin'])) {
                return back()->with('error', 'Impossibile eliminare ruoli di sistema');
            }

            $roleName = $role->name;
            $usersCount = $role->users()->count();

            if ($usersCount > 0) {
                return back()->with('error', "Impossibile eliminare: {$usersCount} utenti hanno questo ruolo");
            }

            $role->delete();

            // GDPR: Log role deletion
            $this->auditService->logUserAction(
                auth()->user(),
                'role_deleted',
                ['role_name' => $roleName],
                GdprActivityCategory::SYSTEM_INTERACTION
            );

            return redirect()->route('superadmin.roles.index')
                ->with('success', "Ruolo '{$roleName}' eliminato con successo");
        } catch (\Exception $e) {
            $this->logger->error('Role deletion failed', [
                'error' => $e->getMessage(),
                'role_id' => $role->id,
                'log_category' => 'SUPERADMIN_ROLES_DELETE_ERROR'
            ]);

            return back()->with('error', 'Errore nell\'eliminazione del ruolo');
        }
    }

    /**
     * Group permissions by module/prefix for better UX
     */
    protected function groupPermissionsByModule($permissions): array
    {
        $grouped = [];

        foreach ($permissions as $permission) {
            // Extract prefix (es: "manage_roles" -> "manage")
            $parts = explode('_', $permission->name);
            $prefix = $parts[0] ?? 'other';

            if (!isset($grouped[$prefix])) {
                $grouped[$prefix] = [];
            }

            $grouped[$prefix][] = $permission;
        }

        return $grouped;
    }
}





