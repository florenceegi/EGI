<?php

namespace App\Http\Middleware;

use App\Helpers\FegiAuth;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * @Oracode Middleware: Ensure Superadmin Access
 * 🎯 Purpose: Restrict access to superadmin-only routes
 * 🔐 Security: Blocks non-superadmin users with UEM error handling
 *
 * @package App\Http\Middleware
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0
 */
class EnsureSuperadmin
{
    protected ErrorManagerInterface $errorManager;

    public function __construct(ErrorManagerInterface $errorManager)
    {
        $this->errorManager = $errorManager;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!FegiAuth::check()) {
            return $this->errorManager->handle('AUTHENTICATION_REQUIRED', [
                'ip_address' => $request->ip(),
                'attempted_route' => $request->path(),
                'message' => 'SuperAdmin access requires authentication',
            ]);
        }

        $user = FegiAuth::user();

        // Check if user has superadmin role
        if (!$user->hasRole('superadmin')) {
            return $this->errorManager->handle('INSUFFICIENT_PERMISSIONS', [
                'user_id' => $user->id,
                'user_role' => $user->getRoleNames()->first() ?? 'unknown',
                'required_role' => 'superadmin',
                'attempted_route' => $request->path(),
                'ip_address' => $request->ip(),
            ]);
        }

        return $next($request);
    }
}


