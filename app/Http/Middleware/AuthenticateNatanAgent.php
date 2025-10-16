<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Symfony\Component\HttpFoundation\Response;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * @Oracode Middleware: NATAN Agent API Authentication
 * 🎯 Purpose: Authenticate PA agent API requests via Bearer token
 * 🛡️ Privacy: Validates encrypted API key, updates last_used timestamp
 * 🧱 Core Logic: Bearer token validation with rate limiting consideration
 *
 * @package App\Http\Middleware
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - NATAN Batch Agent System)
 * @date 2025-10-16
 * @purpose API authentication middleware for NATAN agent requests
 */
class AuthenticateNatanAgent
{
    /**
     * Ultra Log Manager instance.
     *
     * @var UltraLogManager
     */
    protected UltraLogManager $logger;

    /**
     * Constructor with dependency injection.
     *
     * @param UltraLogManager $logger
     */
    public function __construct(UltraLogManager $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Extract Bearer token
        $token = $request->bearerToken();

        if (!$token) {
            $this->logger->warning('NATAN_API_AUTH_FAILED: No bearer token provided', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'endpoint' => $request->path(),
            ]);

            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'Bearer token required',
            ], 401);
        }

        // 2. Validate token format (basic check)
        if (!str_starts_with($token, 'sk_pa_')) {
            $this->logger->warning('NATAN_API_AUTH_FAILED: Invalid token format', [
                'ip' => $request->ip(),
                'token_prefix' => substr($token, 0, 6),
            ]);

            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'Invalid API key format',
            ], 401);
        }

        // 3. Find user with matching API key
        $users = User::whereNotNull('natan_api_key')->get();
        $authenticatedUser = null;

        foreach ($users as $user) {
            try {
                $decryptedKey = Crypt::decryptString($user->natan_api_key);

                if (hash_equals($decryptedKey, $token)) {
                    $authenticatedUser = $user;
                    break;
                }
            } catch (\Exception $e) {
                // Decryption failed, skip this user
                $this->logger->error('NATAN_API_KEY_DECRYPT_FAILED', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
                continue;
            }
        }

        // 4. Check if user found
        if (!$authenticatedUser) {
            $this->logger->warning('NATAN_API_AUTH_FAILED: Invalid API key', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'Invalid API key',
            ], 401);
        }

        // 5. Check if user is PA entity
        if (!$authenticatedUser->hasRole('pa_entity')) {
            $this->logger->warning('NATAN_API_AUTH_FAILED: User not PA entity', [
                'user_id' => $authenticatedUser->id,
                'roles' => $authenticatedUser->roles->pluck('name')->toArray(),
            ]);

            return response()->json([
                'error' => 'Forbidden',
                'message' => 'User is not a PA entity',
            ], 403);
        }

        // 6. Update last_used timestamp (async to avoid slowing request)
        dispatch(function () use ($authenticatedUser) {
            $authenticatedUser->update([
                'natan_api_key_last_used_at' => now(),
            ]);
        })->afterResponse();

        // 7. Attach user to request
        $request->merge(['natan_authenticated_user' => $authenticatedUser]);
        auth()->setUser($authenticatedUser);

        // 8. Log successful authentication
        $this->logger->info('NATAN_API_AUTH_SUCCESS', [
            'user_id' => $authenticatedUser->id,
            'user_email' => $authenticatedUser->email,
            'endpoint' => $request->path(),
            'method' => $request->method(),
        ]);

        return $next($request);
    }
}
