<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Illuminate\Support\Facades\Log;

class VerifyCsrfToken extends Middleware {
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'cookie-consent/*', 
        'mint/*/certificate/pdf/check',
        'stripe/*',
        '/stripe/webhook',
        'stripe/webhook',
        'webhooks/*',
        'api/webhooks/*',
    ];

    public function handle($request, \Closure $next)
    {
        if ($request->is('stripe/webhook') || str_contains($request->path(), 'stripe')) {
             Log::info('CSRF Check Debug: Path [' . $request->path() . '] Is match? ' . ($this->inExceptArray($request) ? 'YES' : 'NO'));
        }
        return parent::handle($request, $next);
    }
}
