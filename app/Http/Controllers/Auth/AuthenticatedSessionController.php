<?php

namespace App\Http\Controllers\Auth;

use App\Enums\Gdpr\GdprActivityCategory;
use App\Http\Controllers\Controller;
use App\Services\Gdpr\ConsentService;
use App\Services\Gdpr\AuditLogService;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Ultra\UltraLogManager\UltraLogManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\LoginResponse;

/**
 * @Oracode Controller: INDEPENDENT Authentication with GDPR Integration (FINAL SOLUTION)
 * 🎯 Purpose: Independent auth controller with GDPR consent checking & session enrichment
 * 🛡️ Privacy: Ensures consent compliance during login flow
 * 🧱 Core Logic: Uses Fortify's logic but with our signature
 *
 * @package App\Http\Controllers\Auth
 * @author Padmin D. Curtis (for Fabio Cherici)
 * @version 3.1.0 - Session Enrichment
 * @date 2025-06-24
 * @solution Independent controller that uses Fortify internally
 */
class AuthenticatedSessionController extends Controller {
    /**
     * Constructor with full DI
     */
    public function __construct(
        protected ErrorManagerInterface $errorManager,
        protected UltraLogManager $logger,
        protected ?ConsentService $consentService = null,
        protected ?AuditLogService $auditService = null
    ) {
        // NO middleware in constructor - handled by routes
    }

    public function boot(): void
    {
        Fortify::ignoreRoutes(); // <— disattiva le route predefinite
        
    }

    /**
     * Show login form
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function create(Request $request) {
        if (Auth::check()) {
            return redirect()->intended(route(config('app.redirect_to_url_after_login', 'home')));
        }

        return view('auth.login', [
            'brandColors' => [
                'oro_fiorentino' => '#D4A574',
                'verde_rinascita' => '#2D5016',
                'blu_algoritmo' => '#1B365D',
                'grigio_pietra' => '#6B6B6B',
                'rosso_urgenza' => '#C13120'
            ],
            'pageTitle' => __('auth.login_page_title'),
            'welcomeMessage' => __('auth.login_welcome_message')
        ]);
    }


    /**
     * Handle login with GDPR checks
     *
     * @param Request $request
     * @return Response|RedirectResponse|JsonResponse
     */
    public function store(Request $request) {
        $logContext = [
            'operation' => 'enhanced_login_attempt',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now()->toISOString()
        ];

        try {
            $this->logger->info('[Auth] Enhanced login attempt started', $logContext);

            // Validate login credentials
            $request->validate([
                Fortify::username() => 'required|string',
                'password' => 'required|string',
            ]);

            // Attempt authentication using Fortify's logic
            if ($this->attemptLogin($request)) {
                $user = Auth::user();
                $logContext['user_id'] = $user->id;
                $logContext['user_type'] = $user->usertype ?? 'unknown'; // Leggiamo subito lo usertype per il log

                $this->logger->info('[Auth] Authentication successful, proceeding with session setup', $logContext);

                // ===== INNESTO OS2.0: Scrittura UserType in Sessione =====
                // 🎯 Scriviamo in modo esplicito il tipo utente nella sessione per un accesso rapido
                // da parte di altri componenti dell'applicazione (es. Middleware, View Composers).
                $request->session()->put('user_type', $user->usertype);
                $this->logger->info('[Auth] UserType written to session', $logContext);
                // ===== FINE INNESTO OS2.0 =====

                // Log successful login
                $this->logSuccessfulLoginWithGdpr($user, $request);

                // Check if consent review is needed
                if ($this->userNeedsConsentReview($user)) {
                    session()->flash('login_success_pending_consent', true);
                    session()->flash('info', __('auth.gdpr_consent_required'));

                    $this->logger->info('[Auth] Redirecting to GDPR consent', $logContext);

                    return redirect()->route(config('app.redirect_to_url_after_login', 'home'));
                }

                // Update last login timestamp
                $user->update(['last_login_at' => now()]);

                // Success message
                session()->flash('success', __('auth.login_success'));

                $this->logger->info('[Auth] Login completed successfully, returning response', $logContext);

                // Return success response (use Fortify's response if available)
                return app(LoginResponse::class);
            } else {
                // Authentication failed
                $this->logger->info('[Auth] Authentication failed - invalid credentials', $logContext);

                throw ValidationException::withMessages([
                    Fortify::username() => [__('auth.failed')],
                ]);
            }
        } catch (ValidationException $e) {
            $this->logger->info('[Auth] Login validation failed', [
                ...$logContext,
                'validation_errors' => $e->errors()
            ]);
            throw $e;
        } catch (\Exception $e) {
            $this->logger->error('[Auth] Login process failed', [
                ...$logContext,
                'error' => $e->getMessage()
            ]);

            return $this->errorManager->handle('POST_LOGIN_GDPR_CHECK_ERROR', [
                'user_id' => Auth::id(),
                'ip_address' => $request->ip(),
                'error' => $e->getMessage()
            ], $e);
        }
    }

    /**
     * Handle logout with GDPR logging
     *
     * @param Request $request
     * @return Response|RedirectResponse
     */
    public function destroy(Request $request) {
        $user = Auth::user();

        // Gestione per utenti "connected" ma non "logged-in"
        if (!$user) {
            // L'utente potrebbe essere in stato "connected" (sessione con wallet ma non autenticato Laravel)
            $connectedUserId = $request->session()->get('connected_user_id');
            $connectedWallet = $request->session()->get('connected_wallet');

            if ($connectedUserId && $connectedWallet) {
                // Log del disconnect per utente "connected"
                $this->logger->info('[Auth] Wallet disconnect for connected user', [
                    'operation' => 'wallet_disconnect_connected_user',
                    'connected_user_id' => $connectedUserId,
                    'connected_wallet' => $connectedWallet,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'timestamp' => now()->toISOString()
                ]);

                // Pulisce la sessione del wallet "connected"
                $request->session()->forget(['connected_user_id', 'connected_wallet', 'auth_status']);
                $request->session()->regenerateToken();

                return redirect()->route('home')
                    ->with('success', __('auth.wallet_disconnect_success'));
            }

            // Nessun utente autenticato e nessun wallet connesso
            return redirect()->route('home')
                ->with('info', __('auth.already_logged_out'));
        }

        // Gestione logout normale per utenti autenticati Laravel
        $logContext = [
            'operation' => 'enhanced_logout',
            'user_id' => $user?->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now()->toISOString()
        ];

        try {
            $this->logger->info('[Auth] Logout initiated', $logContext);

            // Log logout before destroying session
            if ($user) {
                $logContext['session_duration_minutes'] = $this->calculateSessionDuration($user);

                // GDPR audit trail
                if ($this->auditService) {
                    $this->auditService->logUserAction(
                        $user,
                        'user_logged_out_enhanced',
                        [
                            'ip_address' => $request->ip(),
                            'user_agent' => $request->userAgent(),
                            'logout_method' => 'manual',
                            'session_duration' => $logContext['session_duration_minutes']
                        ],
                        GdprActivityCategory::AUTHENTICATION_LOGOUT
                    );
                }

                $this->logger->info('[Auth] User session logged for audit', $logContext);
            }

            // Perform logout
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $this->logger->info('[Auth] Logout completed successfully', $logContext);

            // Return logout response with success message - redirect directly to home route
            return redirect()->route('home')
                ->with('success', __('auth.logout_success'));
        } catch (\Exception $e) {
            $this->logger->warning('[Auth] Failed to log user logout', [
                ...$logContext,
                'error' => $e->getMessage()
            ]);

            // Still logout even if logging fails
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('home')
                ->with('success', __('auth.logout_success'));
        }
    }

    // ===== PRIVATE HELPER METHODS =====

    /**
     * Attempt to authenticate using Fortify's logic
     *
     * @param Request $request
     * @return bool
     */
    private function attemptLogin(Request $request): bool {
        return Auth::attempt(
            $request->only(Fortify::username(), 'password'),
            $request->boolean('remember')
        );
    }

    /**
     * Log successful login using existing GDPR services
     *
     * @param \App\Models\User $user
     * @param Request $request
     * @return void
     */
    private function logSuccessfulLoginWithGdpr($user, Request $request): void {
        $logContext = [
            'operation' => 'enhanced_login_with_gdpr_ecosystem',
            'user_id' => $user->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'login_method' => 'enhanced_credentials',
            'user_type' => $user->usertype ?? 'unknown',
            'ecosystem_status' => $user->ecosystem_setup_completed ? 'complete' : 'incomplete',
            'timestamp' => now()->toISOString()
        ];

        try {
            // Primary logging via ULM
            $this->logger->info('[Auth] Enhanced login successful with GDPR check', $logContext);

            // GDPR audit trail via AuditLogService
            if ($this->auditService) {
                $this->auditService->logUserAction(
                    $user,
                    'user_logged_in_with_gdpr_ecosystem',
                    [
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                        'login_method' => 'enhanced_credentials',
                        'user_type' => $user->user_type ?? 'unknown',
                        'ecosystem_status' => $user->ecosystem_setup_completed ? 'complete' : 'incomplete',
                        'timestamp' => now()->toISOString()
                    ],
                    GdprActivityCategory::AUTHENTICATION_LOGIN
                );
            }
        } catch (\Exception $e) {
            $this->logger->warning('[Auth] Failed to log GDPR-enhanced login', [
                ...$logContext,
                'logging_error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Check if user needs consent review post-login
     *
     * @param \App\Models\User $user
     * @return bool
     */
    private function userNeedsConsentReview($user): bool {
        $logContext = [
            'operation' => 'post_login_consent_check',
            'user_id' => $user->id,
            'gdpr_consents_given_at' => $user->gdpr_consents_given_at?->toISOString()
        ];

        try {
            // Skip GDPR check if services not available
            if (!$this->consentService) {
                $this->logger->info('[Auth] ConsentService unavailable - skipping consent check', $logContext);
                return false;
            }

            // No initial GDPR consent given
            if (!$user->gdpr_consents_given_at) {
                $this->logger->info('[Auth] User needs initial GDPR consent', $logContext);
                return true;
            }

            // Annual consent renewal required (if older than 1 year)
            if ($user->gdpr_consents_given_at < now()->subYear()) {
                $this->logger->info('[Auth] User needs consent renewal (>1 year)', [
                    ...$logContext,
                    'consent_age_days' => now()->diffInDays($user->gdpr_consents_given_at)
                ]);
                return true;
            }

            // Check for policy version updates using existing ConsentService
            $userConsentData = $this->consentService->getUserConsentStatus($user);
            $currentPolicyVersion = config('gdpr.current_policy_version', '1.0');

            if (
                isset($userConsentData['consent_version']) &&
                $userConsentData['consent_version'] !== $currentPolicyVersion
            ) {

                $this->logger->info('[Auth] User needs consent update (policy version changed)', [
                    ...$logContext,
                    'user_version' => $userConsentData['consent_version'],
                    'current_version' => $currentPolicyVersion
                ]);
                return true;
            }

            // All checks passed - no consent review needed
            $this->logger->info('[Auth] User consent status is current', $logContext);
            return false;
        } catch (\Exception $e) {
            $this->logger->warning('[Auth] Failed to check user consent status', [
                ...$logContext,
                'error' => $e->getMessage()
            ]);

            // Don't block login for consent check errors
            return false;
        }
    }

    /**
     * Calculate session duration for logout logging
     *
     * @param \App\Models\User $user
     * @return int|null Duration in minutes
     */
    private function calculateSessionDuration($user): ?int {
        try {
            if ($user->last_login_at) {
                return now()->diffInMinutes($user->last_login_at);
            }
        } catch (\Exception $e) {
            // Ignore errors in duration calculation
        }

        return null;
    }
}
