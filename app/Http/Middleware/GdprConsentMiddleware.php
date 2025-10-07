<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\Gdpr\ConsentService;
use App\Services\Gdpr\AuditLogService;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Oracode Middleware: GDPR Consent Verification
 * 🎯 Purpose: Ensures users have provided required GDPR consents
 * 🛡️ Privacy: Enforces consent requirements throughout the application
 * 🧱 Core Logic: Redirects to consent management if required consents missing
 *
 * @package App\Http\Middleware
 * @author Padmin D. Curtis (for Fabio Cherici)
 * @version 1.0.0
 * @date 2025-05-25
 * @accessibility-trait Preserves user journey with clear consent prompts
 */
class GdprConsentMiddleware
{
    /**
     * Error manager for robust error handling
     * @var ErrorManagerInterface
     */
    protected ErrorManagerInterface $errorManager;

    /**
     * GDPR consent service
     * @var ConsentService
     */
    protected ConsentService $consentService;

    /**
     * Audit logging service
     * @var AuditLogService
     */
    protected AuditLogService $auditService;

    /**
     * Constructor with DI
     *
     * @param ErrorManagerInterface $errorManager
     * @param ConsentService $consentService
     * @param AuditLogService $auditService
     * @privacy-safe All services handle GDPR compliance
     */
    public function __construct(
        ErrorManagerInterface $errorManager,
        ConsentService $consentService,
        AuditLogService $auditService
    ) {
        $this->errorManager = $errorManager;
        $this->consentService = $consentService;
        $this->auditService = $auditService;
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param string $requiredConsent Optional specific consent to check
     * @return Response
     * @privacy-safe Enforces user consent without exposing sensitive data
     */
    public function handle(Request $request, Closure $next, string $requiredConsent = null): Response
    {
        // Skip for guests
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        try {
            // Check if user needs consent review
            if ($this->needsConsentReview($user, $requiredConsent)) {
                return $this->redirectToConsentManagement($request, $requiredConsent);
            }

            // Log access for audit trail
            $this->auditService->logUserAction(
                $user,
                'resource_accessed_with_valid_consent',
                [
                    'url' => $request->url(),
                    'required_consent' => $requiredConsent,
                    'user_agent' => $request->userAgent()
                ],
                'data_access'
            );

            return $next($request);

        } catch (\Exception $e) {
            $this->errorManager->handle('GDPR_CONSENT_CHECK_ERROR', [
                'user_id' => $user->id,
                'url' => $request->url(),
                'required_consent' => $requiredConsent,
                'error' => $e->getMessage()
            ], $e);

            // Fail-safe: allow access but log the issue
            return $next($request);
        }
    }

    /**
     * Check if user needs consent review
     *
     * @param \App\Models\User $user
     * @param string|null $requiredConsent
     * @return bool
     * @privacy-safe Checks user's own consent status only
     */
    protected function needsConsentReview($user, ?string $requiredConsent): bool
    {
        // Check if user has given basic GDPR consents
        if (!$user->gdpr_consents_given_at) {
            return true;
        }

        // Check specific consent if required
        if ($requiredConsent && !$this->consentService->hasConsent($user, $requiredConsent)) {
            return true;
        }

        // Check for consent version updates
        $userConsentData = $this->consentService->getUserConsentStatus($user);
        $currentPolicyVersion = config('gdpr.current_policy_version', '1.0');

        if ($userConsentData['consent_version'] !== $currentPolicyVersion) {
            return true;
        }

        // Check for required re-consent (annually)
        if ($user->gdpr_consents_given_at < now()->subYear()) {
            return true;
        }

        return false;
    }

    /**
     * Redirect to consent management with context
     *
     * @param Request $request
     * @param string|null $requiredConsent
     * @return Response
     * @privacy-safe Redirects to consent page with context preservation
     */
    protected function redirectToConsentManagement(Request $request, ?string $requiredConsent): Response
    {
        $user = Auth::user();

        // Log consent redirect for audit
        $this->auditService->logUserAction(
            $user,
            'redirected_for_consent_review',
            [
                'intended_url' => $request->url(),
                'required_consent' => $requiredConsent,
                'reason' => $this->getRedirectReason($user, $requiredConsent)
            ],
            'gdpr_actions'
        );

        // Build redirect URL with context
        $redirectUrl = route('gdpr.consent');

        // Add query parameters for context
        $queryParams = [
            'return_to' => $request->fullUrl(),
        ];

        if ($requiredConsent) {
            $queryParams['required'] = $requiredConsent;
        }

        $redirectUrl .= '?' . http_build_query($queryParams);

        // Different handling for AJAX requests
        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'consent_required',
                'message' => __('gdpr.consent_required_message'),
                'consent_url' => $redirectUrl,
                'required_consent' => $requiredConsent
            ], 403);
        }

        // Flash message for better UX
        session()->flash('gdpr_consent_required', [
            'message' => $this->getConsentMessage($requiredConsent),
            'required_consent' => $requiredConsent
        ]);

        return redirect($redirectUrl);
    }

    /**
     * Get reason for consent redirect
     *
     * @param \App\Models\User $user
     * @param string|null $requiredConsent
     * @return string
     * @privacy-safe Returns general reason without exposing sensitive data
     */
    protected function getRedirectReason($user, ?string $requiredConsent): string
    {
        if (!$user->gdpr_consents_given_at) {
            return 'no_initial_consent';
        }

        if ($requiredConsent) {
            return 'specific_consent_required';
        }

        if ($user->gdpr_consents_given_at < now()->subYear()) {
            return 'annual_consent_renewal';
        }

        return 'policy_update';
    }

    /**
     * Get appropriate consent message for user
     *
     * @param string|null $requiredConsent
     * @return string
     * @privacy-safe Returns user-friendly message
     */
    protected function getConsentMessage(?string $requiredConsent): string
    {
        if ($requiredConsent) {
            return __('gdpr.specific_consent_required', ['consent' => $requiredConsent]);
        }

        return __('gdpr.consent_review_required');
    }
}
