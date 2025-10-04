<?php

namespace App\Services\View;

use App\Models\User;
use Illuminate\Support\Facades\View as ViewFacade;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * View Service - View Routing Layer
 *
 * @package App\Services\View
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Enterprise Architecture Refactor)
 * @date 2025-10-04
 * @purpose Service Layer per routing view dinamico basato su ruolo utente
 *
 * Features:
 * - View routing per ruolo (Creator, PA, Inspector, Company)
 * - Fallback automatico a view default
 * - View existence check
 * - Role-specific data transformation
 * - ULM logging per debugging
 *
 * Architecture:
 * - Service Layer Pattern (SOLID principles)
 * - Role-based view resolution
 * - Fallback chain (role → default)
 * - View registry pattern
 *
 * View Structure:
 * - egis/index.blade.php (Creator default)
 * - egis/pa/index.blade.php (PA-specific)
 * - egis/inspector/index.blade.php (Inspector-specific)
 * - egis/company/index.blade.php (Company-specific)
 */
class ViewService {
    /**
     * Ultra Log Manager instance
     */
    protected UltraLogManager $logger;

    /**
     * View registry mapping (role → view prefix)
     */
    protected array $viewRegistry = [
        'pa_entity' => 'egis.pa',
        'inspector' => 'egis.inspector',
        'company' => 'egis.company',
        'creator' => 'egis', // Default
    ];

    /**
     * Constructor - Dependency Injection
     *
     * @param UltraLogManager $logger
     */
    public function __construct(UltraLogManager $logger) {
        $this->logger = $logger;
    }

    /**
     * Get view path for user role
     *
     * @param User $user Authenticated user
     * @param string $baseView Base view name (e.g., 'index', 'show', 'create', 'edit')
     * @return string Full view path (e.g., 'egis.pa.index' or 'egis.index')
     *
     * Features:
     * - Role detection via Spatie hasRole()
     * - View existence check
     * - Fallback to default view if role-specific not found
     * - ULM logging per debugging
     *
     * Example:
     * - PA user + 'index' → 'egis.pa.index' (if exists) or 'egis.index'
     * - Creator + 'show' → 'egis.show'
     * - Inspector + 'index' → 'egis.inspector.index' (if exists) or 'egis.index'
     */
    public function getViewForRole(User $user, string $baseView): string {
        // Get user primary role
        $role = $this->getUserPrimaryRole($user);

        // Get view prefix for role
        $viewPrefix = $this->viewRegistry[$role] ?? 'egis';

        // Construct role-specific view path
        if ($viewPrefix === 'egis') {
            // Default Creator view
            $roleView = "egis.{$baseView}";
        } else {
            // Role-specific view (e.g., egis.pa.index)
            $roleView = "{$viewPrefix}.{$baseView}";
        }

        // Check if role-specific view exists
        if (ViewFacade::exists($roleView)) {
            // ULM: Log view resolution
            $this->logger->info('VIEW_SERVICE_RESOLVED: Role-specific view found', [
                'user_id' => $user->id,
                'role' => $role,
                'base_view' => $baseView,
                'resolved_view' => $roleView,
            ]);

            return $roleView;
        }

        // Fallback to default Creator view
        $defaultView = "egis.{$baseView}";

        // ULM: Log fallback
        $this->logger->warning('VIEW_SERVICE_FALLBACK: Role-specific view not found, using default', [
            'user_id' => $user->id,
            'role' => $role,
            'attempted_view' => $roleView,
            'fallback_view' => $defaultView,
        ]);

        return $defaultView;
    }

    /**
     * Get view data with role-specific transformations
     *
     * @param User $user Authenticated user
     * @param string $action Action name (index, show, create, edit)
     * @param array $data Original data
     * @return array Transformed data for view
     *
     * Features:
     * - Role-specific data transformations
     * - Add role context variables
     * - Add permission flags
     *
     * Example:
     * - PA user → adds 'isPA' = true, 'viewMode' = 'institutional'
     * - Creator → adds 'isCreator' = true, 'viewMode' = 'artist'
     */
    public function getViewData(User $user, string $action, array $data): array {
        $role = $this->getUserPrimaryRole($user);

        // Add role context
        $data['userRole'] = $role;
        $data['viewMode'] = $this->getViewModeForRole($role);

        // Add role-specific flags
        $data['isPA'] = $role === 'pa_entity';
        $data['isInspector'] = $role === 'inspector';
        $data['isCompany'] = $role === 'company';
        $data['isCreator'] = $role === 'creator';

        // ULM: Log data transformation
        $this->logger->info('VIEW_SERVICE_DATA: View data transformed', [
            'user_id' => $user->id,
            'role' => $role,
            'action' => $action,
            'data_keys' => array_keys($data),
        ]);

        return $data;
    }

    /**
     * Get user primary role
     *
     * @param User $user
     * @return string Role name (pa_entity, inspector, company, creator)
     *
     * Priority order:
     * 1. pa_entity
     * 2. inspector
     * 3. company
     * 4. creator (default)
     */
    protected function getUserPrimaryRole(User $user): string {
        if ($user->hasRole('pa_entity')) {
            return 'pa_entity';
        }

        if ($user->hasRole('inspector')) {
            return 'inspector';
        }

        if ($user->hasRole('company')) {
            return 'company';
        }

        // Default: Creator
        return 'creator';
    }

    /**
     * Get view mode for role
     *
     * @param string $role
     * @return string View mode identifier
     *
     * Used for CSS classes, UI variants, terminology
     */
    protected function getViewModeForRole(string $role): string {
        return match($role) {
            'pa_entity' => 'institutional',
            'inspector' => 'technical',
            'company' => 'commercial',
            default => 'artist',
        };
    }

    /**
     * Check if view exists for role
     *
     * @param User $user
     * @param string $baseView
     * @return bool
     *
     * Utility method for conditional rendering
     */
    public function viewExistsForRole(User $user, string $baseView): bool {
        $role = $this->getUserPrimaryRole($user);
        $viewPrefix = $this->viewRegistry[$role] ?? 'egis';

        if ($viewPrefix === 'egis') {
            $roleView = "egis.{$baseView}";
        } else {
            $roleView = "{$viewPrefix}.{$baseView}";
        }

        return ViewFacade::exists($roleView);
    }

    /**
     * Register custom view mapping for role
     *
     * @param string $role Role name
     * @param string $viewPrefix View prefix path
     * @return void
     *
     * Allows runtime registration of new role views
     * Example: registerRoleView('partner', 'egis.partner')
     */
    public function registerRoleView(string $role, string $viewPrefix): void {
        $this->viewRegistry[$role] = $viewPrefix;

        $this->logger->info('VIEW_SERVICE_REGISTER: Custom view registered', [
            'role' => $role,
            'view_prefix' => $viewPrefix,
        ]);
    }
}
