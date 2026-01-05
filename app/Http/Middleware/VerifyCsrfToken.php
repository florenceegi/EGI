<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware {
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'cookie-consent/*', // Exclude cookie consent API from CSRF (needs to be accessible to anonymous users)
        'mint/*/certificate/pdf/check', // Public certificate PDF check endpoint (blockchain transparency)
        'stripe/*', // Allow ANY stripe route (safety net)
        '/stripe/webhook', // Explicit absolute path
        'stripe/webhook', // Fallback Stripe Webhook (if configured incorrectly in dashboard)
        'webhooks/*', // Allow all webhooks prefixes
        'api/webhooks/*', // Allow API webhooks (redundant but safe)
    ];
}
