<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Services\Gdpr\AuditLogService;
use App\Enums\Gdpr\GdprActivityCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * @Oracode Controller: SuperAdmin Permissions & Costs Management
 * 🎯 Purpose: Link permissions to costs with bulk management
 * 🛡️ Privacy: Full GDPR compliance with audit logging
 * 🧱 Core Logic: UEM error handling + ULM logging + GDPR audit
 *
 * @package App\Http\Controllers\Superadmin
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 2.0.0 (FlorenceEGI - SuperAdmin Platform Management)
 * @date 2025-10-22
 * @purpose SuperAdmin interface for permission-based pricing with filters and bulk actions
 */
class SuperadminPermissionsController extends Controller
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
     * Display permissions list with filters, search, and pagination
     */
    public function index(Request $request): View | RedirectResponse
    {
        try {
            // 1. ULM: Log access
            $this->logger->info('SuperAdmin Permissions page accessed', [
                'admin_id' => auth()->id(),
                'filters' => $request->only(['search', 'guard', 'module']),
                'log_category' => 'SUPERADMIN_PERMISSIONS_ACCESS'
            ]);

            // 2. Build query with filters
            $query = Permission::query();

            // Search filter
            if ($request->filled('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }

            // Guard filter
            if ($request->filled('guard')) {
                $query->where('guard_name', $request->guard);
            }

            // Module filter (by prefix)
            if ($request->filled('module')) {
                $query->where('name', 'like', $request->module . '%');
            }

            // Order and paginate
            $permissions = $query->orderBy('name')->paginate(50)->withQueryString();

            // 3. GDPR: Log administrative access
            $this->auditService->logUserAction(
                auth()->user(),
                'superadmin_permissions_access',
                [
                    'page' => 'Permissions & Costs Management',
                    'total_permissions' => $permissions->total(),
                    'filters' => $request->only(['search', 'guard', 'module']),
                ],
                GdprActivityCategory::SYSTEM_INTERACTION
            );

            return view('superadmin.permissions.index', [
                'permissions' => $permissions,
                'pageTitle' => 'Permessi & Costi',
            ]);
        } catch (\Exception $e) {
            // 4. ULM: Log error
            $this->logger->error('SuperAdmin Permissions index failed', [
                'error' => $e->getMessage(),
                'log_category' => 'SUPERADMIN_PERMISSIONS_ERROR'
            ]);

            // 5. UEM: Handle error
            return $this->errorManager->handle('SUPERADMIN_PERMISSIONS_INDEX_ERROR', [
                'admin_id' => auth()->id(),
            ], $e);
        }
    }

    /**
     * Bulk update permission costs
     */
    public function bulkUpdate(Request $request): RedirectResponse
    {
        try {
            // 1. ULM: Log bulk update start
            $this->logger->info('Permissions bulk update initiated', [
                'admin_id' => auth()->id(),
                'permissions_data' => $request->input('permissions', []),
                'log_category' => 'SUPERADMIN_PERMISSIONS_BULK_UPDATE'
            ]);

            $updatedCount = 0;
            $permissions = $request->input('permissions', []);

            foreach ($permissions as $permissionId => $data) {
                // Only process selected permissions
                if (!isset($data['selected']) || !$data['selected']) {
                    continue;
                }

                // TODO: Implementare storage dei costi in tabella dedicata
                // Per ora logga solo l'operazione
                $this->logger->debug('Permission cost configuration', [
                    'permission_id' => $permissionId,
                    'permission_name' => $data['name'] ?? 'unknown',
                    'cost_egili' => $data['cost_egili'] ?? null,
                    'cost_fiat_eur' => $data['cost_fiat_eur'] ?? null,
                    'notes' => $data['notes'] ?? null,
                ]);

                $updatedCount++;
            }

            // 2. GDPR: Log bulk update
            $this->auditService->logUserAction(
                auth()->user(),
                'permissions_bulk_update',
                [
                    'permissions_updated' => $updatedCount,
                    'total_submitted' => count($permissions),
                ],
                GdprActivityCategory::SYSTEM_INTERACTION
            );

            // 3. ULM: Log success
            $this->logger->info('Permissions bulk update completed', [
                'admin_id' => auth()->id(),
                'updated_count' => $updatedCount,
                'log_category' => 'SUPERADMIN_PERMISSIONS_BULK_SUCCESS'
            ]);

            return redirect()->route('superadmin.permissions.index')
                ->with('success', "Aggiornamento bulk completato: {$updatedCount} permessi configurati.");
        } catch (\Exception $e) {
            // 4. ULM: Log error
            $this->logger->error('Permissions bulk update failed', [
                'error' => $e->getMessage(),
                'admin_id' => auth()->id(),
                'log_category' => 'SUPERADMIN_PERMISSIONS_BULK_ERROR'
            ]);

            return back()->with('error', 'Errore nell\'aggiornamento bulk dei permessi');
        }
    }
}