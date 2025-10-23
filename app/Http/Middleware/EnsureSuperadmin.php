<?php

namespace App\Http\Middleware;

use App\Helpers\FegiAuth;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * EnsureSuperadmin Middleware
 * 
 * Protects routes that should only be accessible by SuperAdmin users.
 * Uses FegiAuth helper for dual-architecture authentication support.
 * 
 * @package App\Http\Middleware
 */
class EnsureSuperadmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!FegiAuth::check()) {
            abort(403, 'Accesso non autorizzato. Autenticazione richiesta.');
        }

        // Check if user has superadmin role
        if (!FegiAuth::user()->hasRole('superadmin')) {
            abort(403, 'Accesso riservato al SuperAdmin.');
        }

        return $next($request);
    }
}








