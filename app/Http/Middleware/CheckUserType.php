<?php

namespace App\Http\Middleware;

use App\Helpers\FegiAuth;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * CheckUserType Middleware
 * 
 * Protects routes that should only be accessible by specific user types.
 * Supports multiple user types separated by comma (e.g., 'EPP,company').
 * 
 * @package App\Http\Middleware
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - EPP User Type Protection)
 * @date 2025-11-19
 * @purpose Restrict access to routes based on user type (EPP, company, etc.)
 */
class CheckUserType
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $allowedTypes Comma-separated list of allowed user types
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string $allowedTypes): Response
    {
        // Check if user is authenticated
        if (!FegiAuth::check()) {
            abort(403, 'Accesso non autorizzato. Autenticazione richiesta.');
        }

        // Get current user
        $user = FegiAuth::user();

        // Parse allowed types (support comma-separated list)
        $allowedTypesArray = array_map('trim', explode(',', strtolower($allowedTypes)));

        // Check if user's type is in allowed types
        if (!in_array(strtolower($user->usertype), $allowedTypesArray)) {
            abort(403, 'Accesso riservato agli utenti ' . strtoupper($allowedTypes) . '.');
        }

        return $next($request);
    }
}

