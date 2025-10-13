<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Route;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * Auth Redirect Service - Post-Login Redirect Logic
 *
 * @package App\Services\Auth
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Enterprise Architecture Refactor)
 * @date 2025-10-04
 * @purpose Service Layer per gestione redirect post-login basato su usertype
 *
 * Features:
 * - Redirect routing per usertype (Creator, PA, Inspector, Company, etc.)
 * - Fallback automatico a route default ('home')
 * - Route existence check per sicurezza
 * - ULM logging per audit trail
 * - Registry pattern (consistente con ViewService)
 *
 * Architecture:
 * - Service Layer Pattern (SOLID principles)
 * - Usertype-based route resolution
 * - Fallback chain (usertype → default)
 * - Route registry pattern
 *
 * Redirect Structure:
 * - creator → 'home' (public homepage)
 * - pa_entity → 'pa.acts.index' (PA N.A.T.A.N. Intelligence Center)
 * - inspector → 'inspector.dashboard' (Inspector dashboard) [FUTURE]
 * - company → 'company.dashboard' (Company dashboard) [FUTURE]
 * - collector → 'collector.dashboard' (Collector dashboard) [FUTURE]
 * - patron → 'patron.dashboard' (Patron dashboard) [FUTURE]
 * - fallback → 'home' (default)
 *
 * Usage Example:
 * ```php
 * // In AuthenticatedSessionController:
 * public function store(Request $request) {
 *     // ... authentication logic ...
 *     $user = Auth::user();
 *     $redirectRoute = $this->authRedirectService->getRedirectRoute($user);
 *     return redirect()->route($redirectRoute);
 * }
 * ```
 */
class AuthRedirectService
{
    /**
     * Ultra Log Manager instance
     */
    protected UltraLogManager $logger;

    /**
     * Redirect registry mapping (usertype → route name)
     *
     * IMPORTANT: Routes must exist in routes/*.php files
     * Add new usertypes here as they are implemented
     */
    protected array $redirectRegistry = [
        'pa_entity' => 'pa.acts.index',     // PA N.A.T.A.N. Intelligence Center
        'pa_identity' => 'pa.acts.index',   // PA Identity (same as pa_entity)
        'inspector' => 'inspector.dashboard', // Inspector dashboard [FUTURE]
        'company' => 'company.dashboard',    // Company dashboard [FUTURE]
        'collector' => 'collector.dashboard', // Collector dashboard [FUTURE]
        'patron' => 'patron.dashboard',      // Patron dashboard [FUTURE]
        'creator' => 'home',                 // Creator → Public homepage (default)
    ];

    /**
     * Default fallback route if usertype not in registry or route not found
     */
    protected string $defaultRoute = 'home';

    /**
     * Constructor - Dependency Injection
     *
     * @param UltraLogManager $logger
     */
    public function __construct(UltraLogManager $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Get redirect route for user based on usertype
     *
     * @param User $user Authenticated user
     * @return string Route name (e.g., 'pa.dashboard', 'home')
     *
     * Features:
     * - Usertype detection from $user->usertype
     * - Registry lookup (redirectRegistry)
     * - Route existence check (safety)
     * - Fallback to default route if not found
     * - ULM logging per audit trail
     *
     * Process:
     * 1. Get user usertype
     * 2. Lookup registry for route name
     * 3. Check if route exists
     * 4. Return route name or fallback to default
     * 5. Log decision for audit
     *
     * Example:
     * - PA entity user → 'pa.acts.index'
     * - Creator user → 'home'
     * - Unknown usertype → 'home' (fallback)
     */
    public function getRedirectRoute(User $user): string
    {
        // Get user usertype
        $usertype = $user->usertype ?? 'creator'; // Default to creator if null

        // ULM: Log redirect resolution start
        $this->logger->info('AUTH_REDIRECT_SERVICE: Resolving post-login redirect', [
            'user_id' => $user->id,
            'usertype' => $usertype,
            'operation' => 'post_login_redirect',
        ]);

        // Lookup registry for route name
        $routeName = $this->redirectRegistry[$usertype] ?? $this->defaultRoute;

        // Check if route exists (safety check)
        if (!Route::has($routeName)) {
            // ULM: Log route not found - fallback to default
            $this->logger->warning('AUTH_REDIRECT_SERVICE: Route not found, using fallback', [
                'user_id' => $user->id,
                'usertype' => $usertype,
                'attempted_route' => $routeName,
                'fallback_route' => $this->defaultRoute,
            ]);

            $routeName = $this->defaultRoute;
        }

        // ULM: Log successful redirect resolution
        $this->logger->info('AUTH_REDIRECT_SERVICE: Redirect route resolved', [
            'user_id' => $user->id,
            'usertype' => $usertype,
            'redirect_route' => $routeName,
        ]);

        return $routeName;
    }

    /**
     * Get redirect URL for user (alternative method)
     *
     * @param User $user Authenticated user
     * @return string Full redirect URL
     *
     * Note: Use getRedirectRoute() and redirect()->route() instead when possible
     */
    public function getRedirectUrl(User $user): string
    {
        $routeName = $this->getRedirectRoute($user);
        return route($routeName);
    }

    /**
     * Get redirect registry (for debugging/testing)
     *
     * @return array Redirect registry map
     */
    public function getRedirectRegistry(): array
    {
        return $this->redirectRegistry;
    }

    /**
     * Check if usertype has custom redirect configured
     *
     * @param string $usertype User type
     * @return bool True if custom redirect exists
     */
    public function hasCustomRedirect(string $usertype): bool
    {
        return isset($this->redirectRegistry[$usertype])
            && $this->redirectRegistry[$usertype] !== $this->defaultRoute;
    }
}
