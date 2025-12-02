<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Oracode Middleware: CORS Handler
 * 🎯 Purpose: Gestisce CORS per le richieste dall'EGI-HUB React frontend
 */
class HandleCors
{
    /**
     * Allowed origins for CORS
     */
    protected array $allowedOrigins = [
        'http://localhost:5173',
        'http://localhost:5174',
        'http://localhost:5175',
        'http://127.0.0.1:5173',
        'http://127.0.0.1:5174',
        'http://127.0.0.1:5175',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Handle preflight OPTIONS request
        if ($request->isMethod('OPTIONS')) {
            return $this->addCorsHeaders(response('', 204), $request);
        }

        $response = $next($request);

        return $this->addCorsHeaders($response, $request);
    }

    /**
     * Add CORS headers to the response
     */
    protected function addCorsHeaders(Response $response, Request $request): Response
    {
        $origin = $request->header('Origin');
        
        // Check if origin is allowed
        if ($origin && in_array($origin, $this->allowedOrigins)) {
            $response->headers->set('Access-Control-Allow-Origin', $origin);
            $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, Accept, X-XSRF-TOKEN');
            $response->headers->set('Access-Control-Allow-Credentials', 'true');
            $response->headers->set('Access-Control-Max-Age', '86400');
        }

        return $response;
    }
}
