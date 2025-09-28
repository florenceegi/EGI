<?php

namespace App\Http\Controllers;

use App\Enums\Gdpr\GdprActivityCategory;
use App\Http\Requests\Gdpr\SaveLegalContentRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use App\Services\Gdpr\LegalContentService;
use App\Services\Fiscal\FiscalValidatorFactory;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use App\Services\Gdpr\ConsentService;
use App\Services\Gdpr\AuditLogService;
use App\Models\User;

/**
 * @package App\Http\Controllers
 * @author Padmin D. Curtis (AI Partner OS2.0-Compliant) for Fabio Cherici
 * @version 2.3.0 (FlorenceEGI MVP - Legal Domain Validation Refactoring)
 * @date 2025-06-24
 *
 * @Oracode Controller: Legal Document Management
 * 🎯 Purpose: Dedicated controller for legal terms. Acts as an orchestrator for services.
 * 🛡️ Security: Permission-based access is handled per-method with centralized validation.
 * 🧱 Core Logic: Delegates logic to services and validates against central configs (SSoT).
 * ✨ Refactoring: Centralized user type and locale validation for DRY principle.
 */
class GdprLegalController extends Controller
{
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;
    protected ConsentService $consentService;
    protected AuditLogService $auditService;
    protected LegalContentService $legalContentService;

    /**
     * Constructor with complete dependency injection.
     *
     * @Oracode Principle: Partnership Graduata - AI suggests, human directs, together we orchestrate
     */
    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        ConsentService $consentService,
        AuditLogService $auditService,
        LegalContentService $legalContentService
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->consentService = $consentService;
        $this->auditService = $auditService;
        $this->legalContentService = $legalContentService;
    }

    /**
     * @Oracode Method: Centralized Validation for User Type and Locale
     * 🎯 Purpose: Single source of truth for parameter validation across all methods
     * 🛡️ Security: Prevents injection of invalid parameters that could cause undefined behavior
     * 🧱 Core Logic: Uses centralized configuration to validate input parameters
     *
     * Implements Oracode Principles:
     * - Modularità Semantica: Validation logic isolated and reusable
     * - Semplicità Potenziante: DRY principle eliminates code duplication
     * - Interrogabilità Totale: Can be independently tested and verified
     * - Coerenza Semantica: Consistent validation behavior across all endpoints
     *
     * @param string $userType The user type to validate
     * @param string $locale The locale to validate
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException if validation fails
     * @return void
     */
    private function validateUserTypeAndLocale(string $userType, string $locale): void
    {
        $validUserTypes = config('app.fegi_user_type', []);
        $validLocales = config('app.fegi_countries', []);

        if (!in_array($userType, $validUserTypes) || !in_array($locale, $validLocales)) {
            $this->logger->warning('Legal Controller: Invalid user type or locale attempted', [
                'user_type' => $userType,
                'locale' => $locale,
                'valid_user_types' => $validUserTypes,
                'valid_locales' => $validLocales,
                'user_id' => auth()->id() ?? 'guest',
                'log_category' => 'LEGAL_VALIDATION_FAILED'
            ]);

            abort(404, 'Tipo utente o lingua non validi.');
        }

        $this->logger->debug('Legal Controller: User type and locale validated successfully', [
            'user_type' => $userType,
            'locale' => $locale,
            'log_category' => 'LEGAL_VALIDATION_SUCCESS'
        ]);
    }

    /**
     * Show legal terms editor for a specific user type and locale.
     *
     * @param string $userType
     * @param string $locale
     * @return View|RedirectResponse
     *
     * @Oracode Implementation: Resilienza Progressiva - validation errors are caught and handled gracefully
     */
    public function editTerms(string $userType, string $locale = 'it'): View|RedirectResponse
    {
        // $this->middleware(['auth', 'permission:legal.terms.edit']);

        try {
            // 🎯 Centralized validation using our new method
            $this->validateUserTypeAndLocale($userType, $locale);

            $this->logger->info('Legal Editor: Accessing terms editor', [
                'user_id' => auth()->id(),
                'user_type' => $userType,
                'locale' => $locale,
                'log_category' => 'LEGAL_EDITOR_ACCESS'
            ]);

            $currentContent = $this->legalContentService->getCurrentTermsContent($userType, $locale);
            $currentVersion = $this->legalContentService->getCurrentVersionString();
            $versions = $this->legalContentService->getVersionHistory();

            // 🔍 Audit log for compliance tracking
            if (auth()->check()) {
                $this->auditService->logUserAction(auth()->user(), 'legal_editor_accessed', [
                    'user_type' => $userType,
                    'locale' => $locale,
                    'content_loaded' => !empty($currentContent)
                ]);
            }

            return view('gdpr.legal.editor', compact(
                'userType',
                'locale',
                'currentContent',
                'currentVersion',
                'versions'
            ));

        } catch (\Exception $e) {
            return $this->errorManager->handle('LEGAL_EDITOR_LOAD_ERROR', [
                'user_type' => $userType,
                'locale' => $locale,
                'error' => $e->getMessage()
            ], $e);
        }
    }

    /**
     * Save a new version of legal terms.
     *
     * @param Request $request
     * @param string $userType
     * @param string $locale
     * @return RedirectResponse
     *
     * @Oracode Implementation: Intenzionalità Esplicita - every validation step is explicitly documented
     */
    public function saveTerms(SaveLegalContentRequest $request, string $userType, string $locale): RedirectResponse
    {

        try {
            // 🎯 Centralized validation using our new method
            $this->validateUserTypeAndLocale($userType, $locale);

            // Otteniamo i dati già validati e sicuri
            $validated = $request->validated();

            $this->logger->info('Legal Editor: Saving new terms version', [
                'user_id' => auth()->id(),
                'user_type' => $userType,
                'locale' => $locale,
                'content_length' => strlen($validated['content']),
                'auto_publish' => $validated['auto_publish'] ?? false,
                'log_category' => 'LEGAL_TERMS_SAVE'
            ]);

            $newVersion = $this->legalContentService->createNewVersion(
                $userType,
                $locale,
                $validated['content'],
                $validated['change_summary'],
                $validated['effective_date'] ?? null, // Usiamo il dato validato
                $validated['auto_publish'] ?? false
            );

            // 🔍 Complete audit trail for legal document changes
            $this->auditService->logUserAction(auth()->user(), 'legal_terms_saved', [
                'user_type' => $userType,
                'locale' => $locale,
                'new_version' => $newVersion,
                'change_summary' => $validated['change_summary'],
                'auto_published' => $validated['auto_publish'] ?? false
            ]);

            $successMessage = "Nuova versione {$newVersion} creata con successo per {$userType} ({$locale})";
            if ($validated['auto_publish'] ?? false) {
                $successMessage .= " e pubblicata automaticamente.";
            }

            return redirect()
                ->route('legal.edit', compact('userType', 'locale'))
                ->with('success', $successMessage);

        } catch (\Exception $e) {
            return $this->errorManager->handle('LEGAL_TERMS_SAVE_ERROR', [
                'user_type' => $userType,
                'locale' => $locale,
                'error' => $e->getMessage()
            ], $e);
        }
    }

    /**
     * Show public-facing legal terms.
     *
     * @param string $userType
     * @param string $locale
     * @return View|RedirectResponse
     *
     * @Oracode Implementation: Trasparenza Operativa - public access with full audit trail
     */
    public function showTerms(string $userType = "creator", string $locale = 'it'): View|RedirectResponse
    {
        try {
            // 🎯 Centralized validation using our new method
            $this->validateUserTypeAndLocale($userType, $locale);

            $this->logger->info('Legal Public: Accessing terms', [
                'user_id' => auth()->id() ?? 'guest',
                'user_type' => $userType,
                'locale' => $locale,
                'log_category' => 'LEGAL_TERMS_VIEW'
            ]);

            $termsContent = $this->legalContentService->getCurrentTermsContent($userType, $locale);
            $currentVersion = $this->legalContentService->getCurrentVersionString();

            if (!$termsContent) {
                return $this->errorManager->handle('LEGAL_TERMS_NOT_FOUND', [
                    'user_type' => $userType,
                    'locale' => $locale
                ]);
            }

            // ✅ Convert articles array to Collection for enhanced view manipulation
            if (isset($termsContent['articles'])) {
                $termsContent['articles'] = collect($termsContent['articles']);
            }

            $consentStatus = $this->legalContentService->getUserConsentStatus($userType, $locale);

            // 🔍 Check current user's consent status if authenticated
            $hasAcceptedCurrent = auth()->check()
                ? $this->consentService->hasAcceptedCurrentTerms(auth()->user())
                : false;

            // 🔍 Audit log for public access (important for compliance)
            if (auth()->check()) {
                $this->auditService->logUserAction(auth()->user(), 'legal_terms_viewed', [
                    'user_type' => $userType,
                    'locale' => $locale,
                    'has_accepted_current' => $hasAcceptedCurrent,
                    'version_viewed' => $currentVersion
                ], GdprActivityCategory::GDPR_ACTIONS);
            }

            return view('gdpr.legal.terms', compact(
                'userType',
                'locale',
                'termsContent',
                'currentVersion',
                'hasAcceptedCurrent',
                'consentStatus'
            ));

        } catch (\Exception $e) {
            return $this->errorManager->handle('LEGAL_TERMS_VIEW_ERROR', [
                'user_type' => $userType,
                'locale' => $locale,
                'error' => $e->getMessage()
            ], $e);
        }
    }

    /**
     * Handle user acceptance of terms.
     *
     * @param Request $request
     * @param string $userType
     * @return RedirectResponse
     *
     * @Oracode Implementation: Dignità Preservata - user consent is handled with full respect and transparency
     */
    public function acceptTerms(Request $request, string $userType): RedirectResponse
    {
        $this->middleware('auth');

        try {
            $validated = $request->validate([
                'version' => 'required|string',
                'locale' => 'required|string',
            ]);

            // 🎯 Centralized validation using our new method
            $this->validateUserTypeAndLocale($userType, $validated['locale']);

            $user = auth()->user();

            $this->logger->info('Legal Public: User accepting terms', [
                'user_id' => $user->id,
                'user_type' => $userType,
                'version' => $validated['version'],
                'locale' => $validated['locale'],
                'log_category' => 'LEGAL_TERMS_ACCEPT'
            ]);

            // 🛡️ Security check: verify user type matches their actual account type
            if ($user->usertype !== $userType) {
                return $this->errorManager->handle('USER_TYPE_MISMATCH_ON_ACCEPT', [
                    'user' => $user->id,
                    'expected' => $user->usertype,
                    'got' => $userType
                ]);
            }

            // 📝 Record consent through the established service
            $this->consentService->recordTermsConsent($user, $validated['version'], [
                'locale' => $validated['locale'],
                'source' => 'legal_terms_page_acceptance',
                'user_type' => $userType
            ]);

            // 🔍 Comprehensive audit trail for consent acceptance
            $this->auditService->logUserAction($user, 'legal_terms_accepted', [
                'user_type' => $userType,
                'version' => $validated['version'],
                'locale' => $validated['locale']
            ]);

            return back()->with('success', 'Termini accettati con successo!');

        } catch (\Exception $e) {
            return $this->errorManager->handle('LEGAL_TERMS_ACCEPT_ERROR', [
                'user_type' => $userType,
                'error' => $e->getMessage()
            ], $e);
        }
    }

    /**
     * Show the version history page for a document.
     *
     * @param string $userType
     * @param string $locale
     * @return View|RedirectResponse
     *
     * @Oracode Implementation: Evoluzione Ricorsiva - version history helps improve future versions
     */
    public function termsHistory(string $userType, string $locale = 'it'): View|RedirectResponse
    {
        $this->middleware(['auth', 'permission:legal.history.view']);

        try {
            // 🎯 Centralized validation using our new method
            $this->validateUserTypeAndLocale($userType, $locale);

            $this->logger->info('Legal History: Accessing terms history', [
                'user_id' => auth()->id(),
                'user_type' => $userType,
                'locale' => $locale,
                'log_category' => 'LEGAL_HISTORY_ACCESS'
            ]);

            $versions = $this->legalContentService->getVersionHistory();

            // 🔍 Audit access to version history
            $this->auditService->logUserAction(auth()->user(), 'legal_history_viewed', [
                'user_type' => $userType,
                'locale' => $locale,
                'versions_count' => count($versions)
            ]);

            return view('gdpr.legal.history', compact(
                'versions',
                'userType',
                'locale'
            ));

        } catch (\Exception $e) {
            return $this->errorManager->handle('LEGAL_HISTORY_VIEW_ERROR', [
                'user_type' => $userType,
                'locale' => $locale,
                'error' => $e->getMessage()
            ], $e);
        }
    }
}
