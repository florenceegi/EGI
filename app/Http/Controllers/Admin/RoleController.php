<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

class RoleController extends Controller
{
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;

    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        // Sposta il middleware nella definizione delle rotte
    }

    public function index(): View
    {

        $roles = Role::with('permissions')->get();
        Log::info('Roles: ' . $roles);
        return view('admin.roles.index', compact('roles'));
    }

    public function create(): View
    {
        $permissions = Permission::all();
        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles'],
            'permissions' => ['required', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $role = Role::create(['name' => $validated['name']]);

        $permissionNames = $this->getPermissionNames($validated['permissions']);
        $role->syncPermissions($permissionNames);

        return Redirect::route('admin.roles.index')
            ->with('success', 'Role created successfully.');
    }

    public function edit(Role $role): View
    {
        $permissions = Permission::all();
        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name,' . $role->id],
            'permissions' => ['required', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $role->update(['name' => $validated['name']]);

        $permissionNames = $this->getPermissionNames($validated['permissions']);
        $role->syncPermissions($permissionNames);

        return Redirect::route('admin.roles.index')
            ->with('success', 'Role updated successfully.');
    }

    protected function getPermissionNames(array $ids): array
    {
        return Permission::whereIn('id', $ids)->pluck('name')->toArray();
    }

    public function showAssignRoleForm()
    {
        $roles = Role::all(); // Recupera tutti i ruoli dalla tabella `roles`
        return view('admin.assign-role', compact('roles'));
    }

    public function showAssignPermissionsForm()
    {
        $permissions = Permission::all(); // Recupera tutti i permessi
        return view('admin.assign-permissions', compact('permissions'));
    }

    public function assignRole(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'role' => 'required|exists:roles,name',
        ]);

        $user = User::where('email', $request->email)->firstOrFail();
        $role = $request->role;


        // Assegna il ruolo all'utente
        $user->assignRole($role);

        Log::info('Ruolo assegnato: ' . $role . ' a ' . $user->email);

        return redirect()->back()->with('success', 'Ruolo assegnato correttamente!');
    }

    public function assignPermissions(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $user = User::where('email', $request->email)->firstOrFail();
        $permissions = $request->permissions;

        // Assegna i permessi selezionati all'utente
        $user->syncPermissions($permissions);

        return redirect()->back()->with('success', 'Permessi assegnati correttamente!');
    }
}
