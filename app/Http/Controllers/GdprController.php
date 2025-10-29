<?php

namespace App\Http\Controllers;

use App\Enums\Gdpr\GdprActivityCategory;
use App\Enums\Gdpr\ProcessingRestrictionReason;
use App\Enums\Gdpr\ProcessingRestrictionType;
use App\Helpers\FegiAuth;
use App\Http\Requests\Gdpr\ProcessingRestrictionRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use App\Services\Gdpr\GdprService;
use App\Services\Gdpr\ConsentService;
use App\Services\Gdpr\DataExportService;
use App\Services\Gdpr\AuditLogService;
use App\Models\User;
use App\Models\GdprRequest;
use App\Models\BreachReport;
use App\Models\PrivacyPolicy;
use App\Models\ProcessingRestriction;
use App\Services\Fiscal\FiscalValidatorFactory;
use App\Services\Gdpr\ProcessingRestrictionService;
use Carbon\Carbon;
use TCPDF;
use Illuminate\Support\Facades\Log;

/**
 * @Oracode Controller: GDPR Compliance Management
 * 🎯 Purpose: Complete GDPR rights implementation for FlorenceEGI users
 * 🛡️ Privacy: Handles all GDPR data subject rights with full audit trail
 * 🧱 Core Logic: Manages consent, data portability, rectification, erasure, limitation
 *
 * @package App\Http\Controllers
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 2.1.0 (FlorenceEGI - GDPR Export Data Fix)
 * @date 2025-08-05
 * @purpose Fixed export data method with correct variable naming and complete logic
 */
class GdprController extends Controller {
    /**
     * Logger instance for audit trail
     * @var UltraLogManager
     */
    protected UltraLogManager $logger;

    /**
     * Error manager for robust error handling
     * @var UltraErrorManager
     */
    protected ErrorManagerInterface $errorManager;

    /**
     * GDPR business logic service
     * @var GdprService
     */
    protected GdprService $gdprService;

    /**
     * Consent management service
     * @var ConsentService
     */
    protected ConsentService $consentService;

    /**
     * Data export service
     * @var DataExportService
     */
    protected DataExportService $exportService;

    /**
     * Audit logging service
     * @var AuditLogService
     */
    protected AuditLogService $auditService;

    protected ProcessingRestrictionService $processingRestrictionService;


    /**
     * Constructor with dependency injection
     *
     * @param UltraLogManager $logger
     * @param ErrorManagerInterface $errorManager
     * @param GdprService $gdprService
     * @param ConsentService $consentService
     * @param DataExportService $exportService
     * @param AuditLogService $auditService
     * @param ProcessingRestrictionService $processingRestrictionService
     * @privacy-safe All injected services handle GDPR compliance
     */
    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        GdprService $gdprService,
        ConsentService $consentService,
        DataExportService $exportService,
        AuditLogService $auditService,
        ProcessingRestrictionService $processingRestrictionService
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->gdprService = $gdprService;
        $this->consentService = $consentService;
        $this->exportService = $exportService;
        $this->auditService = $auditService;
        $this->processingRestrictionService = $processingRestrictionService;
    }

    /**
     * Show GDPR-compliant profile management (replaces Jetstream profile)
     *
     * @return \Illuminate\View\View
     * @seo-purpose Provide comprehensive GDPR profile management interface
     * @accessibility-trait Full ARIA tablist navigation and landmark structure
     */
    public function showProfile() {
        try {
            $user = auth()->user();

            // Get user consent status for privacy settings tab
            $consentStatus = null;
            if ($this->consentService) {
                try {
                    $consentStatus = $this->consentService->getUserConsentStatus($user);
                } catch (\Exception $e) {
                    // Log but don't fail - privacy tab will show fallback message
                    if ($this->logger) {
                        $this->logger->warning('[GDPR Profile] Failed to load consent status', [
                            'user_id' => $user->id,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }

            return view('gdpr.profile', [
                'user' => $user,
                'consentStatus' => $consentStatus,
                'pageTitle' => __('gdpr.profile_management_title'),
                'brandColors' => [
                    'oro_fiorentino' => '#D4A574',
                    'verde_rinascita' => '#2D5016',
                    'blu_algoritmo' => '#1B365D',
                    'grigio_pietra' => '#6B6B6B',
                    'rosso_urgenza' => '#C13120'
                ],
                // Feature flags for conditional rendering
                'features' => [
                    'password_updates' => \Laravel\Fortify\Features::enabled(\Laravel\Fortify\Features::updatePasswords()),
                    'two_factor_auth' => \Laravel\Fortify\Features::canManageTwoFactorAuthentication(),
                    'browser_sessions' => true,
                    'account_deletion' => \Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures(),
                ]
            ]);
        } catch (\Exception $e) {
            $this->errorManager->handle('GDPR_PROFILE_PAGE_LOAD_ERROR', [
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'error' => $e->getMessage()
            ], $e);

            // Return error view on failure
            return view('error.generic', [
                'message' => __('gdpr.errors.general'),
                'return_url' => route('dashboard')
            ]);
        }
    }

    /**
     * Show GDPR Security Management Interface
     *
     * @return \Illuminate\View\View
     * @seo-purpose Provide dedicated security management interface for users
     * @accessibility-trait Full ARIA landmark structure for security controls
     */
    public function showSecurity() {
        try {
            $user = auth()->user();

            $this->auditService->logUserAction($user, 'security_page_viewed', ['route' => 'gdpr.security'], GdprActivityCategory::GDPR_ACTIONS);

            return view('gdpr.security', [
                'user' => $user,
                'pageTitle' => __('profile.security_management_title'),
                'features' => [
                    'password_updates' => \Laravel\Fortify\Features::enabled(\Laravel\Fortify\Features::updatePasswords()),
                    'two_factor_auth' => \Laravel\Fortify\Features::canManageTwoFactorAuthentication(),
                    'browser_sessions' => true,
                ]
            ]);
        } catch (\Exception $e) {
            $this->errorManager->handle('GDPR_SECURITY_PAGE_LOAD_ERROR', [
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'error' => $e->getMessage()
            ], $e);

            return view('error.generic', [
                'message' => __('gdpr.errors.general'),
                'return_url' => route('dashboard')
            ]);
        }
    }

    /**
     * Show GDPR Profile Images Management Interface
     *
     * @return \Illuminate\View\View
     * @seo-purpose Provide dedicated profile images management interface
     * @accessibility-trait Full ARIA landmark structure for image management
     */
    public function showProfileImages() {
        try {
            $user = auth()->user();

            $this->auditService->logUserAction($user, 'profile_images_page_viewed', ['route' => 'gdpr.profile-images'], GdprActivityCategory::GDPR_ACTIONS);

            return view('gdpr.profile-images', [
                'user' => $user,
                'pageTitle' => __('profile.profile_images_management_title'),
            ]);
        } catch (\Exception $e) {
            $this->errorManager->handle('GDPR_PROFILE_IMAGES_PAGE_LOAD_ERROR', [
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'error' => $e->getMessage()
            ], $e);

            return view('error.generic', [
                'message' => __('gdpr.errors.general'),
                'return_url' => route('dashboard')
            ]);
        }
    }


    /**
     * Store a new processing limitation request.
     *
     * @param ProcessingRestrictionRequest $request Validated restriction request
     * @return \Illuminate\Http\RedirectResponse Redirect with status message
     *
     * @oracode-dimension governance
     * @value-flow Creates new data processing limitation
     * @community-impact Enables user control over their data
     * @transparency-level High - clear feedback on restriction status
     * @narrative-coherence Supports user autonomy and data dignity
     */
    public function limitProcessingStore(ProcessingRestrictionRequest $request) {
        try {
            $user = Auth::user();
            $validated = $request->validated();

            $restriction = $this->processingRestrictionService->createRestriction(
                $user,
                ProcessingRestrictionType::from($validated['restriction_type']),
                ProcessingRestrictionReason::from($validated['restriction_reason']),
                $validated['notes'] ?? null,
                $validated['data_categories'] ?? []
            );

            if ($restriction) {
                return redirect()->route('gdpr.limit-processing')
                    ->with('success', __('gdpr.processing_restriction_success'));
            }

            return redirect()->route('gdpr.limit-processing')
                ->with('error', __('gdpr.processing_restriction_failed'));
        } catch (\Throwable $e) {
            $this->errorManager->handle('GDPR_PROCESSING_RESTRICTION_CREATE_ERROR', [
                'user_id' => Auth::id(),
                'request_data' => $request->safe()->except(['notes']),
                'error' => $e->getMessage()
            ], $e);

            return redirect()->route('gdpr.limit-processing')
                ->with('error', __('gdpr.processing_restriction_system_error'));
        }
    }

    /**
     * Remove an existing processing limitation.
     *
     * @param Request $request The HTTP request
     * @param ProcessingRestriction $restriction The restriction to remove
     * @return \Illuminate\Http\RedirectResponse Redirect with status message
     *
     * @oracode-dimension governance
     * @value-flow Removes data processing limitation
     * @community-impact Maintains user control with removal option
     * @transparency-level High - clear feedback on restriction removal
     * @narrative-coherence Completes the control cycle with removal rights
     */
    public function removeProcessingRestriction(Request $request, ProcessingRestriction $restriction) {
        try {
            $user = Auth::user();

            // Security check - only allow users to remove their own restrictions
            if ($restriction->user_id !== $user->id) {
                return redirect()->route('gdpr.limit-processing')
                    ->with('error', __('gdpr.unauthorized_action'));
            }

            $success = $this->processingRestrictionService->removeRestriction($restriction);

            if ($success) {
                return redirect()->route('gdpr.limit-processing')
                    ->with('success', __('gdpr.processing_restriction_removed'));
            }

            return redirect()->route('gdpr.limit-processing')
                ->with('error', __('gdpr.processing_restriction_removal_failed'));
        } catch (\Throwable $e) {
            $this->errorManager->handle('GDPR_PROCESSING_RESTRICTION_REMOVE_ERROR', [
                'user_id' => Auth::id(),
                'restriction_id' => $restriction->id,
                'error' => $e->getMessage()
            ], $e);

            return redirect()->route('gdpr.limit-processing')
                ->with('error', __('gdpr.processing_restriction_system_error'));
        }
    }

    // ===================================================================
    // CONSENT MANAGEMENT (ConsentMenu)
    // ===================================================================

    /**
     * Display consent management page
     *
     * @return View
     * @privacy-safe Shows user's own consent status only
     */
    public function consent(): View {
        try {
            $user = Auth::user();

            $this->logger->info('GDPR: Accessing consent management page', [
                'user_id' => $user->id,
                'log_category' => 'GDPR_ACCESS'
            ]);

            $consentData = $this->consentService->getUserConsentStatus($user);
            $consentHistory = $this->consentService->getConsentHistory($user);

            // ✅ OS1.5 FIX: Get consent types from ConsentService for view compatibility
            $availableConsentTypes = $this->consentService->getAvailableConsentTypes();

            $this->auditService->logUserAction(
                $user,
                'consent_page_viewed',
                [
                    'consent_count' => count($consentData['userConsents']),
                    'consent_summary' => $consentData['consentSummary']
                ],
                GdprActivityCategory::GDPR_ACTIONS
            );

            $this->logger->info('GDPR: Consent data retrieved', [
                'user_id' => $user->id,
                'consent_count' => count($consentData['userConsents']),
                'consent_summary' => $consentData['consentSummary'],
                'log_category' => 'GDPR_CONSENT_DATA'
            ]);

            return view('gdpr.consent', [
                'user' => $user,
                'consentStatus' => $consentData['userConsents'],
                'consentHistory' => $consentHistory,
                'lastUpdate' => $consentHistory->first()?->created_at,
                'userConsents' => $consentData['userConsents'],
                'consentSummary' => $consentData['consentSummary'],
                'consentTypes' => $availableConsentTypes, // ✅ MISSING! View needs this
            ]);
        } catch (\Exception $e) {
            $this->errorManager->handle('GDPR_CONSENT_PAGE_FAILED', [
                'user_id' => Auth::id(),
                'error_message' => $e->getMessage()
            ], $e);

            // Return error view on failure
            return view('error.generic', [
                'message' => __('gdpr.errors.general'),
                'return_url' => route('gdpr.consent')
            ]);
        }
    }

    /**
     * Display consent preferences management page
     *
     * @return View
     * @privacy-safe Shows user's consent preferences management interface
     */
    public function consentPreferences(): View {
        try {
            $user = Auth::user();

            $this->logger->info('GDPR: Accessing consent preferences page', [
                'user_id' => $user->id,
                'log_category' => 'GDPR_ACCESS'
            ]);

            // Ottieni i consensi attuali e le configurazioni disponibili
            $consentData = $this->consentService->getUserConsentStatus($user);
            $consentTypes = $this->consentService->getAvailableConsentTypes();

            $this->auditService->logUserAction($user, 'consent_preferences_page_viewed', [], GdprActivityCategory::GDPR_ACTIONS);

            // Use the main consent view with preferences mode
            return view('gdpr.consent.preferences', [
                'user' => $user,
                'userConsents' => $consentData['userConsents'],
                'consentTypes' => $consentTypes,
                'consentSummary' => $consentData['consentSummary'],
                'lastUpdate' => $consentData['last_updated'],
                'mode' => 'preferences', // Flag to show preferences interface
                'pageTitle' => __('gdpr.consent.preferences_title'),
                'pageSubtitle' => __('gdpr.consent.preferences_subtitle')
            ]);
        } catch (\Exception $e) {
            $this->errorManager->handle('GDPR_CONSENT_PREFERENCES_PAGE_FAILED', [
                'user_id' => Auth::id(),
                'error_message' => $e->getMessage()
            ], $e);

            // Return fallback view instead of null
            return view('gdpr.consent', [
                'user' => Auth::user(),
                'userConsents' => collect([]),
                'consentTypes' => [],
                'consentSummary' => [
                    'active_consents' => 0,
                    'total_consents' => 0,
                    'compliance_score' => 0
                ],
                'lastUpdate' => null,
                'mode' => 'error',
                'error' => true,
                'pageTitle' => __('gdpr.consent.preferences_title'),
                'pageSubtitle' => __('gdpr.consent.error_subtitle')
            ]);
        }
    }

    /**
     * Update user consent preferences
     *
     * @param Request $request
     * @return RedirectResponse
     * @privacy-safe Updates only authenticated user's consents
     */
    public function updateConsent(Request $request): RedirectResponse {
        try {
            $validated = $request->validate([
                'consents' => 'required|array',
                'consents.functional' => 'boolean',
                'consents.analytics' => 'boolean',
                'consents.marketing' => 'boolean',
                'consents.profiling' => 'boolean',
            ]);

            $user = Auth::user();

            $this->logger->info('GDPR: Updating user consents', [
                'user_id' => $user->id,
                'consents' => $validated['consents'],
                'log_category' => 'GDPR_CONSENT_UPDATE'
            ]);

            $result = $this->consentService->updateUserConsents($user, $validated['consents']);

            $this->auditService->logUserAction($user, 'consents_updated', [
                'previous_consents' => $result['previous'],
                'new_consents' => $result['current']
            ], GdprActivityCategory::GDPR_ACTIONS);

            return redirect()->route('gdpr.consent')
                ->with('success', __('gdpr.consents_updated_successfully'));
        } catch (\Exception $e) {
            $this->errorManager->handle('GDPR_CONSENT_UPDATE_FAILED', [
                'user_id' => Auth::id(),
                'error_message' => $e->getMessage()
            ], $e);

            return redirect()->route('gdpr.consent')
                ->with('error', __('gdpr.consent.update_error'));
        }
    }

    /**
     * Withdraw user consent for a specific purpose.
     *
     * @param Request $request
     * @return RedirectResponse
     * @privacy-safe Withdraws only the authenticated user's own consent
     *
     * @package App\Http\Controllers\User
     * @author Padmin D. Curtis (AI Partner OS1.5.1-Compliant) for Fabio Cherici
     * @version 1.1.0 (FlorenceEGI MVP - Personal Data Domain)
     * @deadline 2025-06-30
     */
    public function withdraw(Request $request): RedirectResponse {
        try {
            $validated = $request->validate([
                'consent_id' => 'required|integer|exists:user_consents,id'
            ]);

            $user = Auth::user();
            $consentId = $validated['consent_id'];

            // 1. Trova il consenso specifico
            $consent = \App\Models\UserConsent::findOrFail($consentId);

            // 2. 🛡️ CONTROLLO DI SICUREZZA FONDAMENTALE
            if ($consent->user_id !== $user->id) {
                throw new \Illuminate\Auth\Access\AuthorizationException('Unauthorized action.');
            }

            // 3. Chiama il service con il 'consent_type'
            $this->consentService->withdrawConsent($user, $consent->consent_type);

            $this->auditService->logUserAction($user, 'consent_withdrawn', [
                'consent_id' => $consentId,
                'consent_type' => $consent->consent_type
            ], GdprActivityCategory::GDPR_ACTIONS);

            return redirect()->route('gdpr.consent')
                ->with('success', __('gdpr.consent.withdrawn_successfully'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            $this->errorManager->handle('GDPR_CONSENT_WITHDRAW_AUTH_FAILED', [
                'user_id' => Auth::id(),
                'consent_id' => $request->input('consent_id'),
                'error_message' => 'User attempted to withdraw a consent not belonging to them.'
            ], $e);

            return redirect()->route('gdpr.consent')
                ->with('error', __('gdpr.errors.unauthorized'));
        } catch (\Exception $e) {
            $this->errorManager->handle('GDPR_CONSENT_WITHDRAW_FAILED', [
                'user_id' => Auth::id(),
                'consent_id' => $request->input('consent_id'),
                'error_message' => $e->getMessage()
            ], $e);

            return redirect()->route('gdpr.consent')
                ->with('error', __('gdpr.errors.general'));
        }
    }

    /**
     * Renew a previously withdrawn user consent.
     *
     * @param Request $request
     * @return RedirectResponse
     * @privacy-safe Renews only the authenticated user's own consent
     *
     * @package App\Http\Controllers\User
     * @author Padmin D. Curtis (AI Partner OS1.5.1-Compliant) for Fabio Cherici
     * @version 1.1.0 (FlorenceEGI MVP - Personal Data Domain)
     * @deadline 2025-06-30
     */
    public function renew(Request $request): RedirectResponse {
        try {
            $validated = $request->validate([
                'consent_id' => 'required|integer|exists:user_consents,id'
            ]);

            $user = Auth::user();
            $consentId = $validated['consent_id'];

            // 1. Trova il consenso specifico
            $consent = \App\Models\UserConsent::findOrFail($consentId);

            // 2. 🛡️ CONTROLLO DI SICUREZZA FONDAMENTALE
            if ($consent->user_id !== $user->id) {
                throw new \Illuminate\Auth\Access\AuthorizationException('Unauthorized action.');
            }

            // 3. Chiama il service con il 'consent_type'
            $this->consentService->renewConsent($user, $consent->consent_type);

            $this->auditService->logUserAction($user, 'consent_renewed', [
                'consent_id' => $consentId,
                'consent_type' => $consent->consent_type
            ], GdprActivityCategory::GDPR_ACTIONS);

            return redirect()->route('gdpr.consent')
                ->with('success', __('gdpr.consent.renewed_successfully'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            $this->errorManager->handle('GDPR_CONSENT_RENEW_AUTH_FAILED', [
                'user_id' => Auth::id(),
                'consent_id' => $request->input('consent_id'),
                'error_message' => 'User attempted to renew a consent not belonging to them.'
            ], $e);

            return redirect()->route('gdpr.consent')
                ->with('error', __('gdpr.errors.unauthorized'));
        } catch (\Exception $e) {
            $this->errorManager->handle('GDPR_CONSENT_RENEW_FAILED', [
                'user_id' => Auth::id(),
                'consent_id' => $request->input('consent_id'),
                'error_message' => $e->getMessage()
            ], $e);

            return redirect()->route('gdpr.consent')
                ->with('error', __('gdpr.errors.general'));
        }
    }

    /**
     * Get consent history for user
     *
     * @return RedirectResponse
     * @privacy-safe Shows only authenticated user's consent history
     */
    public function consentHistory(): RedirectResponse {
        try {
            $user = Auth::user();
            $history = $this->consentService->getDetailedConsentHistory($user);

            $this->auditService->logUserAction($user, 'consent_history_viewed', [], GdprActivityCategory::GDPR_ACTIONS);

            // Redirect to main consent page with history data in session
            return redirect()->route('gdpr.consent')
                ->with('show_history', true)
                ->with('consent_history', $history)
                ->with('success', __('gdpr.consent.history_loaded'));
        } catch (\Exception $e) {
            $this->errorManager->handle('GDPR_CONSENT_HISTORY_FAILED', [
                'user_id' => Auth::id(),
                'error_message' => $e->getMessage()
            ], $e);

            // Redirect back to consent page with error
            return redirect()->route('gdpr.consent')
                ->with('error', __('gdpr.consent.history_load_failed'));
        }
    }

    // ===================================================================
    // DATA EXPORT & PORTABILITY (ExportDataMenu)
    // ===================================================================

    /**
     * Display data export page
     *
     * @return View
     * @privacy-safe Shows export options and history for authenticated user
     */
    public function exportData(): View {
        try {
            $user = Auth::user();

            $this->logger->info('GDPR: Accessing data export page', [
                'user_id' => $user->id,
                'log_category' => 'GDPR_ACCESS'
            ]);

            // Get user's export history
            $exportHistory = $this->exportService->getUserExportHistory($user);

            // Debug log per vedere cosa viene restituito
            $this->logger->info('GDPR: Export history retrieved', [
                'user_id' => $user->id,
                'history_count' => $exportHistory->count(),
                'history_type' => get_class($exportHistory),
                'first_export_id' => $exportHistory->first()['id'] ?? 'no exports',
                'last_export_created' => $exportHistory->first()['created_at'] ?? 'no exports',
                'log_category' => 'GDPR_EXPORT_DEBUG'
            ]);

            // Get available data categories from config
            $availableCategories = $this->exportService->getAvailableDataCategories();

            // Check if user can request new export (rate limiting, permissions, etc.)
            $canRequestExport = $user->can('can_request_export') && $this->canUserRequestExport($user);

            // Log activity for audit trail
            $this->auditService->logUserAction(
                $user,
                'export_page_viewed',
                [
                    'export_count' => $exportHistory->count(),
                    'available_categories' => array_keys($availableCategories),
                    'can_request' => $canRequestExport
                ],
                GdprActivityCategory::DATA_ACCESS
            );

            return view('gdpr.export-data', [
                'user' => $user,
                'exportHistory' => $exportHistory,
                'availableCategories' => $availableCategories, // Full array for export-data view (needs $info)
                'canRequestExport' => $canRequestExport,
            ]);
        } catch (\Exception $e) {
            $this->errorManager->handle('GDPR_EXPORT_PAGE_FAILED', [
                'user_id' => Auth::id(),
                'error_message' => $e->getMessage()
            ], $e);

            // Return error view on failure
            return view('error.generic', [
                'message' => __('gdpr.errors.general'),
                'return_url' => route('gdpr.dashboard')
            ]);
        }
    }

    /**
     * Generate data export for user
     *
     * @param Request $request
     * @return RedirectResponse
     * @privacy-safe Generates export only for authenticated user's data
     */
    public function generateExport(Request $request): RedirectResponse {
        try {
            $this->logger->info('GDPR: Export generation started', [
                'user_id' => Auth::id(),
                'request_data' => $request->all(),
                'log_category' => 'GDPR_EXPORT_DEBUG'
            ]);

            $validated = $request->validate([
                'format' => 'required|in:json,csv,pdf',
                'categories' => 'required|array|min:1',
                'categories.*' => 'string|in:profile,account,preferences,activity,consents,collections,purchases,comments,messages,biography',
                'include_metadata' => 'sometimes|boolean',
                'include_audit_trail' => 'sometimes|boolean'
            ]);

            $this->logger->info('GDPR: Request validation passed', [
                'validated_data' => $validated,
                'log_category' => 'GDPR_EXPORT_DEBUG'
            ]);

            $user = Auth::user();

            // Check if user can request export
            $canRequestExport = $user->can('can_request_export');
            $canUserRequestExport = $this->canUserRequestExport($user);

            $this->logger->info('GDPR: Permission checks', [
                'user_can_request_export' => $canRequestExport,
                'user_can_request_export_rate_limit' => $canUserRequestExport,
                'log_category' => 'GDPR_EXPORT_DEBUG'
            ]);

            if (!$canRequestExport || !$canUserRequestExport) {
                $this->logger->warning('GDPR: Export request denied due to permissions', [
                    'user_id' => $user->id,
                    'can_request_export' => $canRequestExport,
                    'can_user_request_export' => $canUserRequestExport,
                    'log_category' => 'GDPR_EXPORT_DEBUG'
                ]);

                return redirect()->route('gdpr.export-data')
                    ->with('error', __('gdpr.export.limit_reached'));
            }

            $this->logger->info('GDPR: Generating data export', [
                'user_id' => $user->id,
                'format' => $validated['format'],
                'categories' => $validated['categories'],
                'log_category' => 'GDPR_EXPORT_GENERATE'
            ]);

            // Generate export via service
            $exportToken = $this->exportService->generateUserDataExport(
                $user,
                $validated['format'],
                $validated['categories']
            );

            // Check if export generation was successful
            if (empty($exportToken)) {
                $this->logger->error('GDPR: Export generation returned empty token', [
                    'user_id' => $user->id,
                    'format' => $validated['format'],
                    'categories' => $validated['categories'],
                    'log_category' => 'GDPR_EXPORT_GENERATE_FAILED'
                ]);

                return redirect()->route('gdpr.export-data')
                    ->with('error', __('gdpr.export.request_error'));
            }

            // Log activity
            $this->auditService->logUserAction($user, 'export_requested', [
                'format' => $validated['format'],
                'categories' => $validated['categories'],
                'export_token' => $exportToken,
                'include_metadata' => $validated['include_metadata'] ?? false,
                'include_audit_trail' => $validated['include_audit_trail'] ?? false
            ], GdprActivityCategory::GDPR_ACTIONS);

            return redirect()->route('gdpr.export-data')
                ->with('success', __('gdpr.export.request_success'))
                ->with('export_token', $exportToken);
        } catch (\Exception $e) {
            $this->errorManager->handle('GDPR_EXPORT_GENERATION_FAILED', [
                'user_id' => Auth::id(),
                'error_message' => $e->getMessage()
            ], $e);

            return redirect()->route('gdpr.export-data')
                ->with('error', __('gdpr.export.request_error'));
        }
    }

    /**
     * Download generated data export
     *
     * @param string $token
     * @return StreamedResponse
     * @privacy-safe Downloads only if token belongs to authenticated user
     */
    public function downloadExport(string $token): StreamedResponse {
        try {
            $user = Auth::user();

            $this->logger->info('GDPR: Downloading data export', [
                'user_id' => $user->id,
                'export_token' => $token,
                'log_category' => 'GDPR_EXPORT_DOWNLOAD'
            ]);

            $export = $this->exportService->getExportByToken($token, $user);

            if (!$export || $export->user_id !== $user->id) {
                $this->errorManager->handle('GDPR_EXPORT_NOT_FOUND', [
                    'user_id' => $user->id,
                    'token' => $token
                ]);

                abort(404, __('gdpr.export.export_not_found'));
            }

            // Log download activity
            $this->auditService->logUserAction($user, 'export_downloaded', [
                'export_token' => $token,
                'file_size' => $export->file_size
            ], GdprActivityCategory::DATA_ACCESS);

            return $this->exportService->streamExportFile($export);
        } catch (\Exception $e) {
            $this->errorManager->handle('GDPR_EXPORT_DOWNLOAD_FAILED', [
                'user_id' => Auth::id(),
                'token' => $token,
                'error_message' => $e->getMessage()
            ], $e);

            abort(500, __('gdpr.export.download_error'));
        }
    }

    /**
     * Check if user can request a new export (rate limiting)
     *
     * @param User $user
     * @return bool
     * @privacy-safe Checks only authenticated user's export limits
     */
    private function canUserRequestExport(User $user): bool {
        $maxExportsPerDay = config('gdpr.export.max_exports_per_day', 0);

        // Se $maxExportsPerDa == 0, allora non ci sono limiti
        if ($maxExportsPerDay <= 0) {
            return true;
        }

        $todayExports = $user->dataExports()
            ->whereDate('created_at', today())
            ->count();

        return $todayExports < $maxExportsPerDay;
    }

    // ===================================================================
    // PERSONAL DATA MANAGEMENT (EditPersonalDataMenu)
    // ===================================================================

    /**
     * Show the form for editing personal data
     *
     * @return \Illuminate\View\View
     * @seo-purpose Personal data editing form for authenticated users
     * @accessibility-trait Form validation, field descriptions, error handling
     */
    public function editPersonalData() {
        try {
            $user = auth()->user();

            // Get countries list for the country dropdown
            $countries = $this->getCountriesList();

            // Get editable fields based on user permissions
            $editableFields = $this->getEditableFieldsForUser($user);

            // Get on-chain data if user has wallet integrations
            $onChainData = $this->getOnChainData($user);

            $this->logger->info('[GDPR] Personal data edit form accessed', [
                'user_id' => $user->id,
                'user_type' => $user->user_type,
                'has_wallet' => !empty($user->wallet),
                'editable_fields_count' => count($editableFields)
            ]);

            return view('gdpr.edit-personal-data', [
                'user' => $user,
                'countries' => $countries,
                'editableFields' => $editableFields,
                'onChainData' => $onChainData,
                'pageTitle' => __('profile.edit_personal_data'),
                'pageDescription' => __('profile.edit_personal_data_description'),
            ]);
        } catch (\Exception $e) {
            $this->logger->error('[GDPR] Failed to load personal data edit form', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorManager->handle('GDPR_EDIT_DATA_PAGE_FAILED', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ], $e);
        }
    }

    /**
     * Update personal data
     *
     * @param \App\Http\Requests\UpdatePersonalDataRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePersonalData(\App\Http\Requests\UpdatePersonalDataRequest $request) {
        try {
            $user = auth()->user();
            $validated = $request->validated();

            // Log the update attempt
            $this->logger->info('[GDPR] Personal data update initiated', [
                'user_id' => $user->id,
                'fields_to_update' => array_keys($validated),
                'ip_address' => $request->ip()
            ]);

            // Filter out non-editable fields for this user
            $editableFields = $this->getEditableFieldsForUser($user);
            $filteredData = array_intersect_key($validated, array_flip($editableFields));

            // Update user data
            $user->update($filteredData);

            // Log the successful update via GDPR audit service
            if ($this->auditService) {
                $this->auditService->logUserAction(
                    $user,
                    'personal_data_updated',
                    [
                        'updated_fields' => array_keys($filteredData),
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                    ],
                    GdprActivityCategory::PERSONAL_DATA_UPDATE
                );
            }

            $this->logger->info('[GDPR] Personal data updated successfully', [
                'user_id' => $user->id,
                'updated_fields' => array_keys($filteredData)
            ]);

            return redirect()
                ->route('profile.show')
                ->with('success', __('profile.personal_data_updated_successfully'));
        } catch (\Exception $e) {
            $this->logger->error('[GDPR] Failed to update personal data', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'request_data' => $request->safe()->except(['password', 'password_confirmation'])
            ]);

            return $this->errorManager->handle('GDPR_UPDATE_PERSONAL_DATA_FAILED', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ], $e);
        }
    }

    /**
     * Get list of supported countries for dropdowns.
     * The method is now a simple wrapper that delegates the entire logic
     * to the FiscalValidatorFactory, which acts as the Single Source of Truth.
     *
     * @return array
     */
    private function getCountriesList(): array {
        return FiscalValidatorFactory::getSupportedCountriesTranslated();
    }

    /**
     * Get editable fields based on user type and permissions
     *
     * @param \App\Models\User $user
     * @return array
     */
    private function getEditableFieldsForUser(\App\Models\User $user): array {
        $baseFields = [
            'name',
            'email',
            'phone',
            'date_of_birth',
            'address',
            'city',
            'state',
            'postal_code',
            'country',
            'bio'
        ];

        // Add fields based on user type
        switch ($user->user_type) {
            case 'creator':
            case 'patron':
                $baseFields = array_merge($baseFields, [
                    'bio_title',
                    'bio_story',
                    'site_url',
                    'instagram',
                    'facebook',
                    'linkedin'
                ]);
                break;

            case 'enterprise':
                $baseFields = array_merge($baseFields, [
                    'org_name',
                    'org_email',
                    'org_street',
                    'org_city',
                    'org_region',
                    'org_state',
                    'org_zip',
                    'org_site_url',
                    'org_phone_1',
                    'rea',
                    'org_fiscal_code',
                    'org_vat_number'
                ]);
                break;

            case 'epp_entity':
                $baseFields = array_merge($baseFields, [
                    'org_name',
                    'org_email',
                    'org_street',
                    'org_city',
                    'org_region',
                    'org_state',
                    'org_zip',
                    'org_site_url',
                    'org_phone_1',
                    'rea',
                    'org_fiscal_code',
                    'org_vat_number'
                ]);
                break;
            case 'collector':
                $baseFields = array_merge($baseFields, [
                    'site_url',
                    'instagram',
                    'facebook',
                    'linkedin'
                ]);
                break;
            case 'trader_pro':
                $baseFields = array_merge($baseFields, [
                    'site_url',
                    'instagram',
                    'facebook',
                    'linkedin',
                    'org_name',
                    'org_email',
                    'org_street',
                    'org_city',
                    'org_region',
                    'org_state',
                    'org_zip',
                    'org_site_url',
                    'org_phone_1',
                    'rea',
                    'org_fiscal_code',
                    'org_vat_number'
                ]);
                break;
        }

        return $baseFields;
    }

    /**
     * Get on-chain data if available
     *
     * @param \App\Models\User $user
     * @return array|null
     */
    private function getOnChainData(\App\Models\User $user): ?array {
        if (empty($user->wallet)) {
            return null;
        }

        try {
            // If you have wallet integration, fetch on-chain data here
            return [
                'wallet_address' => $user->wallet,
                'wallet_balance' => $user->wallet_balance,
                // Add more on-chain data as needed
            ];
        } catch (\Exception $e) {
            $this->logger->warning('[GDPR] Failed to fetch on-chain data', [
                'user_id' => $user->id,
                'wallet' => $user->wallet,
                'error' => $e->getMessage()
            ]);

            return null;
        }
    }


    /**
     * Request data rectification for incorrect information
     *
     * @param Request $request
     * @return RedirectResponse
     * @privacy-safe Creates rectification request for authenticated user
     */
    public function requestRectification(Request $request): RedirectResponse {
        try {
            $validated = $request->validate([
                'field_name' => 'required|string|max:255',
                'current_value' => 'required|string|max:1000',
                'requested_value' => 'required|string|max:1000',
                'reason' => 'required|string|max:2000'
            ]);

            $user = Auth::user();

            $this->logger->info('GDPR: Rectification request submitted', [
                'user_id' => $user->id,
                'field_name' => $validated['field_name'],
                'log_category' => 'GDPR_RECTIFICATION_REQUEST'
            ]);

            $request = $this->gdprService->createRectificationRequest($user, $validated);

            $this->auditService->logUserAction($user, 'rectification_requested', [
                'request_id' => $request->id,
                'field_name' => $validated['field_name']
            ], GdprActivityCategory::PERSONAL_DATA_UPDATE);

            return redirect()->route('gdpr.edit-personal-data')
                ->with('success', __('gdpr.rectification_request_submitted'));
        } catch (\Exception $e) {
            return $this->errorManager->handle('GDPR_RECTIFICATION_REQUEST_FAILED', [
                'user_id' => Auth::id(),
                'error_message' => $e->getMessage()
            ], $e);
        }
    }

    // ===================================================================
    // PROCESSING LIMITATION (LimitProcessingMenu)
    // ===================================================================

    /**
     * Show the processing limitation request form.
     *
     * @param Request $request The HTTP request
     * @return View The processing limitation form view
     *
     * @oracode-dimension governance
     * @value-flow Enables users to control their data processing
     * @community-impact Empowers users with data control rights
     * @transparency-level High - all current restrictions displayed
     * @narrative-coherence Aligns with user dignity and control values
     */
    public function limitProcessing(Request $request): View {
        try {
            $user = Auth::user();
            $activeRestrictions = $this->processingRestrictionService->getUserActiveRestrictions($user);

            return view('gdpr.limit-processing', [
                'user' => $user,
                'activeRestrictions' => $activeRestrictions,
                'restrictionTypes' => ProcessingRestrictionType::cases(),
                'restrictionReasons' => ProcessingRestrictionReason::cases(),
            ]);
        } catch (\Throwable $e) {
            $this->errorManager->handle('GDPR_PROCESSING_LIMIT_VIEW_ERROR', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ], $e);

            return view('gdpr.limit-processing', [
                'user' => Auth::user(),
                'activeRestrictions' => [],
                'restrictionTypes' => ProcessingRestrictionType::cases(),
                'restrictionReasons' => ProcessingRestrictionReason::cases(),
                'error' => true
            ]);
        }
    }

    /**
     * Update processing limitations
     *
     * @param Request $request
     * @return RedirectResponse
     * @privacy-safe Updates limitations for authenticated user only
     */
    public function updateProcessingLimits(Request $request): RedirectResponse {
        try {
            $validated = $request->validate([
                'limitations' => 'required|array',
                'limitations.marketing' => 'boolean',
                'limitations.profiling' => 'boolean',
                'limitations.analytics' => 'boolean',
                'limitations.automated_decisions' => 'boolean',
            ]);

            $user = Auth::user();

            $this->logger->info('GDPR: Updating processing limitations', [
                'user_id' => $user->id,
                'limitations' => $validated['limitations'],
                'log_category' => 'GDPR_PROCESSING_LIMITS_UPDATE'
            ]);

            $result = $this->gdprService->updateProcessingLimitations($user, $validated['limitations']);

            $this->auditService->logUserAction($user, 'processing_limits_updated', [
                'previous_limits' => $result['previous'],
                'new_limits' => $result['current']
            ], GdprActivityCategory::GDPR_ACTIONS);

            return redirect()->route('gdpr.limit-processing')
                ->with('success', __('gdpr.processing_limits_updated_successfully'));
        } catch (\Exception $e) {
            return $this->errorManager->handle('GDPR_PROCESSING_LIMITS_UPDATE_FAILED', [
                'user_id' => Auth::id(),
                'error_message' => $e->getMessage()
            ], $e);
        }
    }

    // ===================================================================
    // ACCOUNT DELETION (DeleteAccountMenu)
    // ===================================================================

    /**
     * Display account deletion page
     *
     * @return View
     * @privacy-safe Shows deletion options for authenticated user
     */
    public function deleteAccount(): View {
        try {
            $user = Auth::user();

            $this->logger->info('GDPR: Accessing account deletion page', [
                'user_id' => $user->id,
                'log_category' => 'GDPR_ACCESS'
            ]);

            $deletionInfo = $this->gdprService->getDeletionInfo($user);
            $onChainDataSummary = $this->gdprService->getOnChainDataSummary($user);

            $this->auditService->logUserAction($user, 'delete_account_page_viewed', [], GdprActivityCategory::GDPR_ACTIONS);

            return view('gdpr.delete-account', [
                'user' => $user,
                'deletionInfo' => $deletionInfo,
                'onChainDataSummary' => $onChainDataSummary
            ]);
        } catch (\Exception $e) {
            return $this->errorManager->handle('GDPR_DELETE_ACCOUNT_PAGE_FAILED', [
                'user_id' => Auth::id(),
                'error_message' => $e->getMessage()
            ], $e);
        }
    }

    /**
     * Request account deletion
     *
     * @param Request $request
     * @return RedirectResponse
     * @privacy-safe Creates deletion request for authenticated user
     */
    public function requestAccountDeletion(Request $request): RedirectResponse {
        try {
            $validated = $request->validate([
                'reason' => 'sometimes|string|max:1000',
                'acknowledge_onchain' => 'required|accepted'
            ]);

            $user = Auth::user();

            $this->logger->warning('GDPR: Account deletion requested', [
                'user_id' => $user->id,
                'reason' => $validated['reason'] ?? 'No reason provided',
                'log_category' => 'GDPR_DELETION_REQUEST'
            ]);

            $deletionRequest = $this->gdprService->createDeletionRequest($user, $validated);

            $this->auditService->logUserAction($user, 'deletion_requested', [
                'request_id' => $deletionRequest->id,
                'reason' => $validated['reason'] ?? null
            ], GdprActivityCategory::GDPR_ACTIONS);

            return redirect()->route('gdpr.delete-account')
                ->with('warning', __('gdpr.deletion_request_submitted'));
        } catch (\Exception $e) {
            return $this->errorManager->handle('GDPR_DELETION_REQUEST_FAILED', [
                'user_id' => Auth::id(),
                'error_message' => $e->getMessage()
            ], $e);
        }
    }

    /**
     * Confirm and execute account deletion
     *
     * @param Request $request
     * @return RedirectResponse
     * @privacy-safe Deletes only authenticated user's account
     */
    public function confirmAccountDeletion(Request $request): RedirectResponse {
        try {
            $validated = $request->validate([
                'password' => 'required|current_password',
                'confirmation' => 'required|in:DELETE',
                'final_confirmation' => 'required|accepted'
            ]);

            $user = Auth::user();
            $userId = $user->id;
            $userEmail = $user->email;

            $this->logger->critical('GDPR: Account deletion confirmed and executing', [
                'user_id' => $userId,
                'user_email' => $userEmail,
                'log_category' => 'GDPR_DELETION_CONFIRMED'
            ]);

            // Execute deletion process
            $deletionResult = $this->gdprService->executeAccountDeletion($user);

            $this->auditService->logUserAction($user, 'account_deleted', [
                'deletion_result' => $deletionResult,
                'final_action' => true
            ], GdprActivityCategory::GDPR_ACTIONS);

            // Logout and invalidate session
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/')
                ->with('account_deleted', true)
                ->with('deletion_summary', $deletionResult);
        } catch (\Exception $e) {
            return $this->errorManager->handle('GDPR_ACCOUNT_DELETION_FAILED', [
                'user_id' => Auth::id(),
                'error_message' => $e->getMessage()
            ], $e);
        }
    }

    // ===================================================================
    // ACTIVITY LOG & AUDIT (ActivityLogMenu)
    // ===================================================================

    /**
     * Display user activity log
     *
     * @return View
     * @privacy-safe Shows only authenticated user's activity log
     */
    public function activityLog(): View {
        try {
            $user = Auth::user();

            $this->logger->info('GDPR: Accessing activity log page', [
                'user_id' => $user->id,
                'log_category' => 'GDPR_ACCESS'
            ]);

            $activities = $this->auditService->getUserActivityLog($user, 50);
            $activityStats = $this->auditService->getUserActivityStats($user);
            $availableCategories = array_keys(config('gdpr.activity_categories', []));
            
            // Extract unique action types from activities
            $availableActionTypes = $activities->pluck('action')->unique()->sort()->values()->toArray();

            $this->auditService->logUserAction($user, 'activity_log_viewed', [], GdprActivityCategory::GDPR_ACTIONS);

            return view('gdpr.activity-log', [
                'user' => $user,
                'activities' => $activities,
                'activityStats' => $activityStats,
                'availableCategories' => $availableCategories,
                'availableActionTypes' => $availableActionTypes
            ]);
        } catch (\Exception $e) {
            return $this->errorManager->handle('GDPR_ACTIVITY_LOG_PAGE_FAILED', [
                'user_id' => Auth::id(),
                'error_message' => $e->getMessage()
            ], $e);
        }
    }

    /**
     * Export user activity log
     *
     * @param Request $request
     * @return StreamedResponse
     * @privacy-safe Exports only authenticated user's activity log
     */
    public function exportActivityLog(Request $request): StreamedResponse {
        try {
            $validated = $request->validate([
                'format' => 'sometimes|in:csv,json',
                'date_from' => 'sometimes|date',
                'date_to' => 'sometimes|date|after_or_equal:date_from'
            ]);

            $user = Auth::user();

            $this->logger->info('GDPR: Exporting activity log', [
                'user_id' => $user->id,
                'format' => $validated['format'] ?? 'csv',
                'log_category' => 'GDPR_ACTIVITY_EXPORT'
            ]);

            $this->auditService->logUserAction($user, 'activity_log_exported', [
                'format' => $validated['format'] ?? 'csv',
                'date_range' => [
                    'from' => $validated['date_from'] ?? null,
                    'to' => $validated['date_to'] ?? null
                ]
            ], GdprActivityCategory::DATA_ACCESS);

            return $this->auditService->exportUserActivityLog($user, $validated);
        } catch (\Exception $e) {
            return $this->errorManager->handle('GDPR_ACTIVITY_LOG_EXPORT_FAILED', [
                'user_id' => Auth::id(),
                'error_message' => $e->getMessage()
            ], $e);
        }
    }

    // ===================================================================
    // BREACH REPORTING (BreachReportMenu)
    // ===================================================================

    /**
     * Display breach reporting page
     *
     * @return View
     * @privacy-safe Shows breach reporting form and user's reports
     */
    public function breachReport(): View {
        try {
            $user = Auth::user();

            $this->logger->info('GDPR: Accessing breach report page', [
                'user_id' => $user->id,
                'log_category' => 'GDPR_ACCESS'
            ]);

            $userReports = $this->gdprService->getUserBreachReports($user);
            $reportCategories = $this->gdprService->getBreachReportCategories();

            $this->auditService->logUserAction($user, 'breach_report_page_viewed', [], GdprActivityCategory::GDPR_ACTIONS);

            return view('gdpr.breach-report', [
                'user' => $user,
                'userReports' => $userReports,
                'reportCategories' => $reportCategories
            ]);
        } catch (\Exception $e) {
            return $this->errorManager->handle('GDPR_BREACH_REPORT_PAGE_FAILED', [
                'user_id' => Auth::id(),
                'error_message' => $e->getMessage()
            ], $e);
        }
    }

    /**
     * Submit breach report
     *
     * @param Request $request
     * @return RedirectResponse
     * @privacy-safe Creates breach report for authenticated user
     */
    public function submitBreachReport(Request $request): RedirectResponse {
        try {
            $validated = $request->validate([
                'category' => 'required|string|in:data_leak,unauthorized_access,system_breach,phishing,other',
                'description' => 'required|string|min:10|max:2000',
                'incident_date' => 'sometimes|date|before_or_equal:today',
                'affected_data' => 'sometimes|array',
                'severity' => 'required|in:low,medium,high,critical'
            ]);

            $user = Auth::user();

            $this->logger->warning('GDPR: Breach report submitted', [
                'user_id' => $user->id,
                'category' => $validated['category'],
                'severity' => $validated['severity'],
                'log_category' => 'GDPR_BREACH_REPORT'
            ]);

            $report = $this->gdprService->createBreachReport($user, $validated);

            $this->auditService->logUserAction($user, 'breach_report_submitted', [
                'report_id' => $report->id,
                'category' => $validated['category'],
                'severity' => $validated['severity']
            ], GdprActivityCategory::GDPR_ACTIONS);

            return redirect()->route('gdpr.breach-report')
                ->with('success', __('gdpr.breach_report_submitted_successfully'));
        } catch (\Exception $e) {
            return $this->errorManager->handle('GDPR_BREACH_REPORT_SUBMISSION_FAILED', [
                'user_id' => Auth::id(),
                'error_message' => $e->getMessage()
            ], $e);
        }
    }

    /**
     * View breach report status
     *
     * @param BreachReport $report
     * @return View
     * @privacy-safe Shows report status only if user owns the report
     */
    public function breachReportStatus(BreachReport $report): View {
        try {
            $user = Auth::user();

            if ($report->user_id !== $user->id) {
                return $this->errorManager->handle('GDPR_BREACH_REPORT_ACCESS_DENIED', [
                    'user_id' => $user->id,
                    'report_id' => $report->id
                ]);
            }

            $this->logger->info('GDPR: Viewing breach report status', [
                'user_id' => $user->id,
                'report_id' => $report->id,
                'log_category' => 'GDPR_ACCESS'
            ]);

            $this->auditService->logUserAction($user, 'breach_report_status_viewed', [
                'report_id' => $report->id
            ], GdprActivityCategory::GDPR_ACTIONS);

            return view('gdpr.breach-report-status', [
                'user' => $user,
                'report' => $report
            ]);
        } catch (\Exception $e) {
            return $this->errorManager->handle('GDPR_BREACH_REPORT_STATUS_FAILED', [
                'user_id' => Auth::id(),
                'report_id' => $report->id ?? null,
                'error_message' => $e->getMessage()
            ], $e);
        }
    }

    // ===================================================================
    // PRIVACY POLICY & TRANSPARENCY (PrivacyPolicyMenu)
    // ===================================================================

    /**
     * Display privacy policy page
     *
     * @return View
     * @privacy-safe Public information display with structured content
     */
    public function privacyPolicy(): View {
        try {
            $user = FegiAuth::user();

            $this->logger->info('GDPR: Accessing privacy policy page', [
                'user_id' => $user?->id,
                'log_category' => 'GDPR_ACCESS'
            ]);

            // Get current active policy
            $currentPolicy = PrivacyPolicy::active()
                ->documentType('privacy_policy')
                ->language(app()->getLocale())
                ->first();

            // If no policy is found for the current locale, fallback to a default
            if (!$currentPolicy) {
                $currentPolicy = PrivacyPolicy::active()
                    ->documentType('privacy_policy')
                    ->language(config('app.fallback_locale', 'it'))
                    ->firstOrFail(); // Fails if no policy is available at all
            }

            // Parse policy content for table of contents and sectioned display
            $policyContent = $this->parsePolicyContent($currentPolicy);

            if ($user) {
                $this->auditService->logUserAction($user, 'privacy_policy_viewed', [], GdprActivityCategory::GDPR_ACTIONS);
            }

            // Version history - all versions for the current document type and language
            $versionHistory = PrivacyPolicy::where('document_type', 'privacy_policy')
                ->where('language', app()->getLocale())
                ->orderBy('effective_date', 'desc')
                ->orderBy('version', 'desc')
                ->get();

            // User acceptance status for the specific policy type
            $userAcceptance = $user ? $user->consents()
                ->where('consent_type', 'privacy-policy')
                ->where('granted', true)
                ->latest('created_at')
                ->first() : null;

            $this->logger->info('GDPR: Privacy policy page loaded', [
                'user_id' => $user?->id,
                'current_policy_id' => $currentPolicy?->id,
                'version_count' => $versionHistory->count(),
                'user_acceptance' => $userAcceptance?->id ?? 'none',
            ]);

            return view('gdpr.privacy-policy', [
                'user' => $user,
                'currentPolicy' => $currentPolicy,
                'versionHistory' => $versionHistory,
                'userAcceptance' => $userAcceptance,
                'policyData' => $currentPolicy,
                'policyContent' => $policyContent
            ]);
        } catch (\Exception $e) {
            return $this->errorManager->handle('GDPR_PRIVACY_POLICY_PAGE_FAILED', [
                'user_id' => Auth::id(),
                'error_message' => $e->getMessage()
            ], $e);
        }
    }

    public function privacyPolicyVersion(PrivacyPolicy $policy): View {
        try {
            $user = FegiAuth::user();

            $this->logger->info('GDPR: Viewing privacy policy version', [
                'user_id' => $user?->id,
                'policy_id' => $policy->id,
                'log_category' => 'GDPR_ACCESS'
            ]);

            // Parse policy content for table of contents
            $policyContent = $this->parsePolicyContent($policy);

            if ($user) {
                $this->auditService->logUserAction($user, 'privacy_policy_version_viewed', [
                    'policy_id' => $policy->id
                ], GdprActivityCategory::GDPR_ACTIONS);
            }

            return view('gdpr.privacy-policy-version', [
                'user' => $user,
                'policy' => $policy,
                'policyContent' => $policyContent
            ]);
        } catch (\Exception $e) {
            return $this->errorManager->handle('GDPR_PRIVACY_POLICY_VERSION_FAILED', [
                'user_id' => Auth::id(),
                'policy_id' => $policy->id ?? null,
                'error_message' => $e->getMessage()
            ], $e);
        }
    }

    // ================================================
    // CONTENT PARSER HELPER METHODS (REFACTORED)
    // ================================================

    /**
     * Parse privacy policy content for structured display.
     * This is the main orchestrator for content parsing.
     *
     * @param PrivacyPolicy|null $policyData The policy model instance.
     * @return array Structured content with sections for TOC and display.
     */
    protected function parsePolicyContent(?PrivacyPolicy $policyData): array {
        $content = $policyData->content ?? '';

        if (empty($content)) {
            return $this->getFallbackPolicyContent();
        }

        // 🎯 The refactored method now correctly extracts full sections.
        $sections = $this->extractSectionsFromMarkdown($content);

        return [
            'sections' => $sections,
            'raw_content' => $content,
            'total_sections' => count($sections),
            'estimated_reading_time' => $this->calculateReadingTime($content)
        ];
    }


    /**
     * 🎯 REFACTORED LOGIC
     * Extract sections (title AND content) from markdown text.
     * This method now correctly parses the content belonging to each header.
     *
     * @param string $content Markdown content
     * @return array Array of sections, each with title, anchor, and its own content.
     */
    protected function extractSectionsFromMarkdown(string $content): array {
        $lines = \explode("\n", $content);
        $sections = [];
        $currentSection = null;
        $sectionIndex = 0;

        foreach ($lines as $lineNumber => $line) {
            // Check if the line is a header (H1, H2, H3)
            if (\preg_match('/^(#{1,3})\s+(.+)$/', \trim($line), $matches)) {
                // If there's a section being built, finalize it.
                if ($currentSection !== null) {
                    // Trim trailing newlines from the content
                    $currentSection['content'] = \trim($currentSection['content']);
                    $sections[] = $currentSection;
                }

                // Start a new section
                $headerLevel = \strlen($matches[1]);
                $rawTitle = \trim($matches[2]);
                $cleanTitle = $this->cleanHeaderTitle($rawTitle);

                // Aggiungi un controllo per i titoli vuoti
                if (empty($cleanTitle)) continue;

                $currentSection = [
                    'title' => $cleanTitle,
                    'anchor' => $this->generateAnchorId($cleanTitle, $sectionIndex),
                    'level' => $headerLevel,
                    'line_number' => $lineNumber + 1,
                    'index' => $sectionIndex + 1,
                    'raw_title' => $rawTitle,
                    'content' => '' // Initialize content for the new section
                ];
                $sectionIndex++;
            } elseif ($currentSection !== null) {
                // If it's not a header, append the line to the current section's content.
                // We add the newline back, as markdown() will need it.
                $currentSection['content'] .= $line . "\n";
            }
        }

        // Add the last section if it exists
        if ($currentSection !== null) {
            $currentSection['content'] = trim($currentSection['content']);
            $sections[] = $currentSection;
        }

        return $sections;
    }

    /**
     * Clean header title from markdown formatting
     *
     * @param string $title Raw header title
     * @return string Cleaned title
     */
    protected function cleanHeaderTitle(string $title): string {
        // Remove markdown formatting
        $cleaned = $title;

        // Remove bold **text** and __text__
        $cleaned = \preg_replace('/\*\*(.*?)\*\*/', '$1', $cleaned);
        $cleaned = \preg_replace('/__(.*?)__/', '$1', $cleaned);

        // Remove italic *text* and _text_
        $cleaned = \preg_replace('/(?<!\*)\*([^*]+)\*(?!\*)/', '$1', $cleaned);
        $cleaned = \preg_replace('/(?<!_)_([^_]+)_(?!_)/', '$1', $cleaned);

        // Remove code `text`
        $cleaned = \preg_replace('/`([^`]+)`/', '$1', $cleaned);

        // Remove links [text](url)
        $cleaned = \preg_replace('/\[([^\]]+)\]\([^)]+\)/', '$1', $cleaned);

        // Remove emojis and special characters for cleaner TOC
        $cleaned = \preg_replace('/[\x{1F600}-\x{1F64F}]/u', '', $cleaned); // Emoticons
        $cleaned = \preg_replace('/[\x{1F300}-\x{1F5FF}]/u', '', $cleaned); // Misc symbols
        $cleaned = \preg_replace('/[\x{1F680}-\x{1F6FF}]/u', '', $cleaned); // Transport
        $cleaned = \preg_replace('/[\x{1F1E0}-\x{1F1FF}]/u', '', $cleaned); // Flags

        return \trim($cleaned);
    }

    /**
     * Generate anchor ID for section
     *
     * @param string $title Section title
     * @param int $index Section index
     * @return string Anchor ID
     */
    protected function generateAnchorId(string $title, int $index): string {
        // Create slug from title
        $slug = Str::slug($title);

        // If slug is empty or too short, use section number
        if (empty($slug) || strlen($slug) < 3) {
            return "section-" . ($index + 1);
        }

        // Limit length and ensure uniqueness
        $baseSlug = Str::limit($slug, 50, '');
        return "section-" . ($index + 1) . "-" . $baseSlug;
    }

    /**
     * Calculate estimated reading time
     *
     * @param string $content Content to analyze
     * @return int Reading time in minutes
     */
    protected function calculateReadingTime(string $content): int {
        // Remove markdown syntax for accurate word count
        $plainText = strip_tags(\Illuminate\Mail\Markdown::parse($content));
        $wordCount = str_word_count($plainText);

        // Average reading speed: 200-250 words per minute
        // Use 200 for conservative estimate
        $readingTime = ceil($wordCount / 200);

        return max(1, $readingTime); // Minimum 1 minute
    }

    /**
     * Get fallback content structure when no policy is available
     *
     * @return array Fallback structure
     */
    protected function getFallbackPolicyContent(): array {
        return [
            'sections' => [
                [
                    'title' => __('gdpr.privacy_policy.introduction'),
                    'anchor' => 'section-1-introduction',
                    'level' => 1,
                    'index' => 1
                ],
                [
                    'title' => __('gdpr.privacy_policy.data_collection'),
                    'anchor' => 'section-2-data-collection',
                    'level' => 1,
                    'index' => 2
                ],
                [
                    'title' => __('gdpr.privacy_policy.data_usage'),
                    'anchor' => 'section-3-data-usage',
                    'level' => 1,
                    'index' => 3
                ],
                [
                    'title' => __('gdpr.privacy_policy.your_rights'),
                    'anchor' => 'section-4-your-rights',
                    'level' => 1,
                    'index' => 4
                ],
                [
                    'title' => __('gdpr.privacy_policy.contact'),
                    'anchor' => 'section-5-contact',
                    'level' => 1,
                    'index' => 5
                ]
            ],
            'content' => __('gdpr.privacy_policy.content_not_available'),
            'total_sections' => 5,
            'estimated_reading_time' => 5
        ];
    }


    /**
     * Display privacy policy changelog
     *
     * @return View
     * @privacy-safe Shows public policy version history
     */
    public function privacyPolicyChangelog(): View {
        try {
            $user = Auth::user();

            $this->logger->info('GDPR: Accessing privacy policy changelog', [
                'user_id' => $user?->id,
                'log_category' => 'GDPR_ACCESS'
            ]);

            $policyVersions = $this->gdprService->getPrivacyPolicyVersions();

            if ($user) {
                $this->auditService->logUserAction($user, 'privacy_policy_changelog_viewed', [], GdprActivityCategory::GDPR_ACTIONS);
            }

            return view('gdpr.privacy-policy-changelog', [
                'user' => $user,
                'policyVersions' => $policyVersions
            ]);
        } catch (\Exception $e) {
            return $this->errorManager->handle('GDPR_PRIVACY_POLICY_CHANGELOG_FAILED', [
                'user_id' => Auth::id(),
                'error_message' => $e->getMessage()
            ], $e);
        }
    }

    /**
     * Display data processing information
     *
     * @return View
     * @privacy-safe Shows transparency information about data processing
     */
    public function dataProcessingInfo(): View {
        try {
            $user = Auth::user();

            $this->logger->info('GDPR: Accessing data processing info page', [
                'user_id' => $user?->id,
                'log_category' => 'GDPR_ACCESS'
            ]);

            $processingInfo = $this->gdprService->getDataProcessingInformation();
            $thirdPartyServices = $this->gdprService->getThirdPartyServices();

            if ($user) {
                $this->auditService->logUserAction($user, 'data_processing_info_viewed', [], GdprActivityCategory::GDPR_ACTIONS);
            }

            return view('gdpr.data-processing-info', [
                'user' => $user,
                'processingInfo' => $processingInfo,
                'thirdPartyServices' => $thirdPartyServices
            ]);
        } catch (\Exception $e) {
            return $this->errorManager->handle('GDPR_DATA_PROCESSING_INFO_FAILED', [
                'user_id' => Auth::id(),
                'error_message' => $e->getMessage()
            ], $e);
        }
    }

    // ===================================================================
    // DPO CONTACT & SUPPORT
    // ===================================================================

    /**
     * Display DPO contact page
     *
     * @return View
     * @privacy-safe Shows DPO contact information and form
     */
    public function contactDpo(): View {
        try {
            $user = Auth::user();

            $this->logger->info('GDPR: Accessing DPO contact page', [
                'user_id' => $user->id,
                'log_category' => 'GDPR_ACCESS'
            ]);

            $dpoInfo = $this->gdprService->getDpoContactInformation();
            $userMessages = $this->gdprService->getUserDpoMessages($user);

            $this->auditService->logUserAction($user, 'dpo_contact_page_viewed', [], GdprActivityCategory::GDPR_ACTIONS);

            return view('gdpr.contact-dpo', [
                'user' => $user,
                'dpoInfo' => $dpoInfo,
                'userMessages' => $userMessages
            ]);
        } catch (\Exception $e) {
            return $this->errorManager->handle('GDPR_DPO_CONTACT_PAGE_FAILED', [
                'user_id' => Auth::id(),
                'error_message' => $e->getMessage()
            ], $e);
        }
    }

    /**
     * Send message to DPO
     *
     * @param Request $request
     * @return RedirectResponse
     * @privacy-safe Creates message from authenticated user to DPO
     */
    public function sendDpoMessage(Request $request): RedirectResponse {
        try {
            $validated = $request->validate([
                'subject' => 'required|string|max:255',
                'message' => 'required|string|min:10|max:2000',
                'priority' => 'required|in:low,normal,high,urgent',
                'request_type' => 'required|in:information,complaint,access_request,other'
            ]);

            $user = Auth::user();

            $this->logger->info('GDPR: Sending message to DPO', [
                'user_id' => $user->id,
                'subject' => $validated['subject'],
                'priority' => $validated['priority'],
                'log_category' => 'GDPR_DPO_MESSAGE'
            ]);

            $message = $this->gdprService->sendMessageToDpo($user, $validated);

            $this->auditService->logUserAction($user, 'dpo_message_sent', [
                'message_id' => $message->id,
                'subject' => $validated['subject'],
                'priority' => $validated['priority']
            ], GdprActivityCategory::GDPR_ACTIONS);

            return redirect()->route('gdpr.contact-dpo')
                ->with('success', __('gdpr.dpo_message_sent_successfully'));
        } catch (\Exception $e) {
            return $this->errorManager->handle('GDPR_DPO_MESSAGE_FAILED', [
                'user_id' => Auth::id(),
                'error_message' => $e->getMessage()
            ], $e);
        }
    }

    // ===================================================================
    // API METHODS (for dynamic frontend)
    // ===================================================================

    /**
     * Get current consent status (API)
     *
     * @return JsonResponse
     * @privacy-safe Returns authenticated user's consent status
     */
    public function getConsentStatus(): JsonResponse {
        try {
            $user = Auth::user();
            $consentStatus = $this->consentService->getUserConsentStatus($user);

            $this->logger->debug('GDPR API: Consent status requested', [
                'user_id' => $user->id,
                'log_category' => 'GDPR_API_ACCESS'
            ]);

            return response()->json([
                'success' => true,
                'data' => $consentStatus,
                'last_updated' => $consentStatus['last_updated'] ?? null
            ]);
        } catch (\Exception $e) {
            return $this->errorManager->handle('GDPR_API_CONSENT_STATUS_FAILED', [
                'user_id' => Auth::id(),
                'error_message' => $e->getMessage()
            ], $e);
        }
    }

    /**
     * Get current processing limitations (API)
     *
     * @return JsonResponse
     * @privacy-safe Returns authenticated user's processing limits
     */
    public function getProcessingLimits(): JsonResponse {
        try {
            $user = Auth::user();
            $processingLimits = $this->gdprService->getUserProcessingLimitations($user);

            $this->logger->debug('GDPR API: Processing limits requested', [
                'user_id' => $user->id,
                'log_category' => 'GDPR_API_ACCESS'
            ]);

            return response()->json([
                'success' => true,
                'data' => $processingLimits,
                'effective_date' => $processingLimits['effective_date'] ?? null
            ]);
        } catch (\Exception $e) {
            return $this->errorManager->handle('GDPR_API_PROCESSING_LIMITS_FAILED', [
                'user_id' => Auth::id(),
                'error_message' => $e->getMessage()
            ], $e);
        }
    }

    /**
     * Get export status by token (API)
     *
     * @param string $token
     * @return JsonResponse
     * @privacy-safe Returns export status only if token belongs to authenticated user
     */
    public function getExportStatus(string $token): JsonResponse {
        try {
            $user = Auth::user();
            $export = $this->exportService->getExportByToken($token, $user);

            if (!$export || $export->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'error' => 'Export not found or access denied'
                ], 404);
            }

            $this->logger->debug('GDPR API: Export status requested', [
                'user_id' => $user->id,
                'export_token' => $token,
                'log_category' => 'GDPR_API_ACCESS'
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'status' => $export->status,
                    'progress' => $export->progress,
                    'created_at' => $export->created_at,
                    'completed_at' => $export->completed_at,
                    'download_url' => $export->status === 'completed' ?
                        route('gdpr.export-data.download', $token) : null,
                    'file_size' => $export->file_size,
                    'format' => $export->format
                ]
            ]);
        } catch (\Exception $e) {
            return $this->errorManager->handle('GDPR_API_EXPORT_STATUS_FAILED', [
                'user_id' => Auth::id(),
                'token' => $token,
                'error_message' => $e->getMessage()
            ], $e);
        }
    }

    // ===================================================================
    // LEGACY METHODS (for backward compatibility)
    // ===================================================================

    /**
     * Legacy consent display method
     *
     * @return View
     * @deprecated Use consent() method instead
     * @privacy-safe Shows user's own consent status only
     */
    public function consents(): View {
        $this->logger->warning('GDPR: Legacy consents() method called', [
            'user_id' => Auth::id(),
            'log_category' => 'GDPR_LEGACY_ACCESS'
        ]);

        return $this->consent();
    }

    /**
     * Legacy consent update method
     *
     * @param Request $request
     * @return RedirectResponse
     * @deprecated Use updateConsent() method instead
     * @privacy-safe Updates only authenticated user's consents
     */
    public function updateConsents(Request $request): RedirectResponse {
        $this->logger->warning('GDPR: Legacy updateConsents() method called', [
            'user_id' => Auth::id(),
            'log_category' => 'GDPR_LEGACY_ACCESS'
        ]);

        return $this->updateConsent($request);
    }

    /**
     * Legacy data download method
     *
     * @param Request $request
     * @return StreamedResponse
     * @deprecated Use generateExport() and downloadExport() methods instead
     * @privacy-safe Downloads only authenticated user's data
     */
    public function downloadData(Request $request): StreamedResponse {
        try {
            $user = Auth::user();

            $this->logger->warning('GDPR: Legacy downloadData() method called', [
                'user_id' => $user->id,
                'log_category' => 'GDPR_LEGACY_ACCESS'
            ]);

            // Generate immediate export for backward compatibility
            $exportToken = $this->exportService->generateUserDataExport(
                $user,
                'json',
                ['profile', 'activities', 'consents']
            );

            $export = $this->exportService->getExportByToken($exportToken, $user);

            $this->auditService->logUserAction($user, 'legacy_data_downloaded', [
                'export_token' => $exportToken
            ], GdprActivityCategory::DATA_ACCESS);

            return $this->exportService->streamExportFile($export);
        } catch (\Exception $e) {
            return $this->errorManager->handle('GDPR_LEGACY_DATA_DOWNLOAD_FAILED', [
                'user_id' => Auth::id(),
                'error_message' => $e->getMessage()
            ], $e);
        }
    }

    /**
     * Legacy account destruction method
     *
     * @param Request $request
     * @return RedirectResponse
     * @deprecated Use confirmAccountDeletion() method instead
     * @privacy-safe Deletes only authenticated user's account
     */
    public function destroyAccount(Request $request): RedirectResponse {
        $this->logger->warning('GDPR: Legacy destroyAccount() method called', [
            'user_id' => Auth::id(),
            'log_category' => 'GDPR_LEGACY_ACCESS'
        ]);

        return $this->confirmAccountDeletion($request);
    }

    /**
     * Download active privacy policy as PDF using TCPDF
     *
     * @return \Illuminate\Http\Response
     * @privacy-safe Public document download with proper headers
     */
    public function privacyPolicyDownload(): Response {
        try {
            // Retrieve active privacy policy
            $policy = PrivacyPolicy::active()
                ->documentType('privacy_policy')
                ->language(app()->getLocale())
                ->first();

            if (!$policy) {
                // Fallback to default language if user language not available
                $policy = PrivacyPolicy::active()
                    ->documentType('privacy_policy')
                    ->language('it') // FlorenceEGI default
                    ->first();
            }

            if (!$policy) {
                abort(404, 'Privacy Policy non disponibile');
            }

            // Generate PDF using TCPDF
            $pdfContent = $this->generatePolicyPdfTcpdf($policy, 'privacy-policy');
            $filename = $this->getPolicyFilename($policy);

            // Return download response with proper headers
            return response($pdfContent)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
                ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');
        } catch (\Exception $e) {
            Log::error('Privacy Policy PDF Download Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_agent' => request()->userAgent(),
                'ip' => request()->ip()
            ]);

            abort(500, 'Errore nella generazione del PDF');
        }
    }

    /**
     * Download cookie policy as PDF
     *
     * @return \Illuminate\Http\Response
     */
    public function cookiePolicyDownload(): Response {
        return $this->downloadPolicyByType('cookie_policy');
    }

    /**
     * Generic policy download by type
     *
     * @param string $type Policy document type
     * @return \Illuminate\Http\Response
     */
    public function policyDownload(string $type): Response {
        // Map route params to document types
        $typeMapping = [
            'privacy-policy' => 'privacy_policy',
            'cookie-policy' => 'cookie_policy',
            'terms-of-service' => 'terms_of_service'
        ];

        $documentType = $typeMapping[$type] ?? null;

        if (!$documentType) {
            abort(404, 'Tipo di policy non supportato');
        }

        return $this->downloadPolicyByType($documentType);
    }

    /**
     * Download policy by document type
     *
     * @param string $documentType
     * @return \Illuminate\Http\Response
     * @privacy-safe Document type validation and secure retrieval
     */
    protected function downloadPolicyByType(string $documentType): Response {
        try {
            // Validate document type
            $validTypes = array_values(PrivacyPolicy::DOCUMENT_TYPES);
            if (!in_array($documentType, $validTypes)) {
                abort(400, 'Tipo documento non valido');
            }

            // Retrieve active policy
            $policy = PrivacyPolicy::active()
                ->documentType($documentType)
                ->language(app()->getLocale())
                ->first();

            if (!$policy) {
                // Fallback to Italian
                $policy = PrivacyPolicy::active()
                    ->documentType($documentType)
                    ->language('it')
                    ->first();
            }

            if (!$policy) {
                abort(404, 'Documento non disponibile');
            }

            // Generate PDF using TCPDF
            $pdfContent = $this->generatePolicyPdfTcpdf($policy, $documentType);
            $filename = $this->getPolicyFilename($policy);

            return response($pdfContent)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
                ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');
        } catch (\Exception $e) {
            Log::error('Policy PDF Download Error', [
                'document_type' => $documentType,
                'error' => $e->getMessage(),
                'user_agent' => request()->userAgent(),
                'ip' => request()->ip()
            ]);

            abort(500, 'Errore nella generazione del PDF');
        }
    }

    /**
     * Generate PDF from privacy policy using TCPDF
     *
     * @param PrivacyPolicy $policy
     * @param string $templateType
     * @return string PDF content as binary string
     * @privacy-safe Secure PDF generation with TCPDF
     */
    protected function generatePolicyPdfTcpdf(PrivacyPolicy $policy, string $templateType): string {
        // Create new TCPDF instance
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // ===== DOCUMENT INFORMATION =====
        $pdf->SetCreator('FlorenceEGI - Rinascimento Digitale');
        $pdf->SetAuthor('FlorenceEGI');
        $pdf->SetTitle($policy->title);
        $pdf->SetSubject('Privacy Policy - FlorenceEGI Marketplace EGI Sostenibile');
        $pdf->SetKeywords('Privacy, GDPR, NFT, Blockchain, Sostenibilità, FlorenceEGI');

        // ===== HEADER/FOOTER SETUP =====
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // ===== MARGINS =====
        $pdf->SetMargins(20, 20, 20);
        $pdf->SetAutoPageBreak(TRUE, 25);

        // ===== FONT SETUP =====
        $pdf->SetFont('helvetica', '', 10);

        // ===== ADD PAGE =====
        $pdf->AddPage();

        // ===== HEADER SECTION =====
        $this->addPdfHeader($pdf, $policy);

        // ===== META INFO SECTION =====
        $this->addPdfMetaInfo($pdf, $policy);

        // ===== CONTENT SECTION =====
        $this->addPdfContent($pdf, $policy);

        // ===== FOOTER SECTION =====
        $this->addPdfFooter($pdf, $policy);

        // Return PDF as string
        return $pdf->Output('', 'S');
    }

    /**
     * Add header section to PDF
     *
     * @param TCPDF $pdf
     * @param PrivacyPolicy $policy
     * @return void
     */
    protected function addPdfHeader(TCPDF $pdf, PrivacyPolicy $policy): void {
        // FlorenceEGI Brand Header
        $pdf->SetFillColor(102, 126, 234); // Brand color #667eea
        $pdf->Rect(0, 0, 210, 40, 'F');

        // White text for header
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('helvetica', 'B', 18);
        $pdf->SetXY(20, 10);
        $pdf->Cell(170, 10, 'FlorenceEGI', 0, 1, 'C');

        $pdf->SetFont('helvetica', '', 12);
        $pdf->SetXY(20, 22);
        $pdf->Cell(170, 8, 'Rinascimento Digitale Sostenibile 🌱', 0, 1, 'C');

        // Policy Title
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->SetXY(20, 50);
        $pdf->MultiCell(170, 8, $policy->title, 0, 'C');

        $pdf->Ln(10);
    }

    /**
     * Add meta information section to PDF
     *
     * @param TCPDF $pdf
     * @param PrivacyPolicy $policy
     * @return void
     */
    protected function addPdfMetaInfo(TCPDF $pdf, PrivacyPolicy $policy): void {
        $currentY = $pdf->GetY();

        // Background for meta info
        $pdf->SetFillColor(248, 249, 250);
        $pdf->Rect(20, $currentY, 170, 35, 'F');

        // Border
        $pdf->SetDrawColor(102, 126, 234);
        $pdf->SetLineWidth(0.5);
        $pdf->Line(20, $currentY, 25, $currentY + 35);

        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetTextColor(0, 0, 0);

        // Meta information table
        $metaY = $currentY + 5;
        $pdf->SetXY(30, $metaY);

        // Row 1
        $pdf->Cell(35, 6, 'Versione:', 0, 0, 'L');
        $pdf->Cell(40, 6, $policy->version, 0, 0, 'L');
        $pdf->Cell(35, 6, 'Tipo Documento:', 0, 0, 'L');
        $pdf->Cell(50, 6, ucfirst(str_replace('_', ' ', $policy->document_type)), 0, 1, 'L');

        // Row 2
        $pdf->SetX(30);
        $pdf->Cell(35, 6, 'Data Effettiva:', 0, 0, 'L');
        $pdf->Cell(40, 6, $policy->effective_date->format('d/m/Y'), 0, 0, 'L');
        $pdf->Cell(35, 6, 'Lingua:', 0, 0, 'L');
        $pdf->Cell(50, 6, strtoupper($policy->language), 0, 1, 'L');

        // Row 3
        $pdf->SetX(30);
        $pdf->Cell(35, 6, 'Stato:', 0, 0, 'L');
        $pdf->SetTextColor(102, 126, 234);
        $pdf->Cell(40, 6, ucfirst($policy->status), 0, 0, 'L');
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(35, 6, 'Generato il:', 0, 0, 'L');
        $pdf->Cell(50, 6, Carbon::now()->format('d/m/Y H:i'), 0, 1, 'L');

        // Row 4
        $pdf->SetX(30);
        $pdf->Cell(35, 6, 'Contatto Privacy:', 0, 0, 'L');
        $pdf->SetTextColor(102, 126, 234);
        $pdf->Cell(125, 6, 'privacy@florenceegi.com', 0, 1, 'L');

        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetY($currentY + 40);
    }

    /**
     * Add content section to PDF
     *
     * @param TCPDF $pdf
     * @param PrivacyPolicy $policy
     * @return void
     */
    protected function addPdfContent(TCPDF $pdf, PrivacyPolicy $policy): void {
        // Convert Markdown to HTML and then to TCPDF-compatible format
        $content = $this->formatContentForTcpdf($policy->content);

        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetTextColor(44, 62, 80);

        // Add content with HTML support
        $pdf->writeHTML($content, true, false, true, false, '');
    }

    /**
     * Add footer section to PDF
     *
     * @param TCPDF $pdf
     * @param PrivacyPolicy $policy
     * @return void
     */
    protected function addPdfFooter(TCPDF $pdf, PrivacyPolicy $policy): void {
        // Footer is handled by page event
        $pdf->setFooterData();
    }

    /**
     * Format content for TCPDF HTML rendering
     *
     * @param string $markdownContent
     * @return string
     */
    protected function formatContentForTcpdf(string $markdownContent): string {
        // Convert markdown to HTML
        $html = \Illuminate\Mail\Markdown::parse($markdownContent);

        // Clean up for TCPDF
        $html = str_replace(['<p>', '</p>'], ['<p style="margin-bottom: 10px; text-align: justify;">', '</p>'], $html);
        $html = str_replace(['<h1>', '</h1>'], ['<h1 style="color: #2c3e50; font-size: 16px; margin-top: 20px; margin-bottom: 10px; border-bottom: 2px solid #667eea; padding-bottom: 5px;">', '</h1>'], $html);
        $html = str_replace(['<h2>', '</h2>'], ['<h2 style="color: #495057; font-size: 14px; margin-top: 15px; margin-bottom: 8px;">', '</h2>'], $html);
        $html = str_replace(['<h3>', '</h3>'], ['<h3 style="color: #6c757d; font-size: 12px; margin-top: 12px; margin-bottom: 6px;">', '</h3>'], $html);
        $html = str_replace(['<strong>', '</strong>'], ['<strong style="color: #2c3e50; font-weight: bold;">', '</strong>'], $html);
        $html = str_replace(['<em>', '</em>'], ['<em style="color: #667eea; font-style: italic;">', '</em>'], $html);
        $html = str_replace(['<ul>', '</ul>'], ['<ul style="margin: 10px 0; padding-left: 20px;">', '</ul>'], $html);
        $html = str_replace(['<ol>', '</ol>'], ['<ol style="margin: 10px 0; padding-left: 20px;">', '</ol>'], $html);
        $html = str_replace(['<li>', '</li>'], ['<li style="margin-bottom: 5px;">', '</li>'], $html);

        return $html;
    }

    /**
     * Generate filename for policy PDF
     *
     * @param PrivacyPolicy $policy
     * @return string
     * @privacy-safe Sanitized filename generation
     */
    protected function getPolicyFilename(PrivacyPolicy $policy): string {
        $date = Carbon::now()->format('Y-m-d');
        $type = str_replace('_', '-', $policy->document_type);
        $version = str_replace('.', '-', $policy->version);

        return "florenceegi-{$type}-v{$version}-{$date}.pdf";
    }

    /**
     * Stream policy PDF (alternative to download)
     *
     * @param string $documentType
     * @return \Illuminate\Http\Response
     */
    public function streamPolicy(string $documentType): Response {
        $policy = PrivacyPolicy::active()
            ->documentType($documentType)
            ->language(app()->getLocale())
            ->firstOrFail();

        $pdfContent = $this->generatePolicyPdfTcpdf($policy, $documentType);
        $filename = $this->getPolicyFilename($policy);

        return response($pdfContent)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="' . $filename . '"')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate');
    }
}
