<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use App\Services\Gdpr\ConsentService;
use App\Services\Gdpr\AuditLogService;
use App\Enums\Gdpr\GdprActivityCategory;
use App\Enums\Gdpr\CookieConsentCategory;
use App\Models\ConsentType;

/**
 * @Oracode Controller: Cookie Consent Management
 * 🎯 Purpose: Manages cookie consent for all visitors (authenticated and anonymous)
 * 🛡️ Privacy: GDPR-compliant cookie consent with granular control
 * 🧱 Core Logic: Handles consent storage, retrieval, and audit trail
 *
 * @package App\Http\Controllers
 * @author Padmin D. Curtis (AI Partner) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Cookie Consent Implementation)
 * @date 2025-09-17
 */
class CookieConsentController extends Controller {
    /**
     * Logger instance for audit trail
     * @var UltraLogManager
     */
    protected UltraLogManager $logger;

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
     * Constructor with dependency injection
     *
     * @param UltraLogManager $logger
     * @param ErrorManagerInterface $errorManager
     * @param ConsentService $consentService
     * @param AuditLogService $auditService
     * @privacy-safe All injected services handle GDPR compliance
     */
    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        ConsentService $consentService,
        AuditLogService $auditService
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->consentService = $consentService;
        $this->auditService = $auditService;
    }

    /**
     * Get current cookie consent status for visitor
     *
     * @param Request $request
     * @return JsonResponse
     * @privacy-safe Returns consent status for current visitor
     */
    public function getConsentStatus(Request $request): JsonResponse {
        try {
            $this->logger->info('[Cookie Consent] Getting consent status', [
                'user_id' => Auth::id(),
                'session_id' => session()->getId(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'timestamp' => now()->toIso8601String(),
                'is_authenticated' => Auth::check(),
                'log_category' => 'COOKIE_CONSENT_ACCESS'
            ]);

            $consentData = [];

            if (Auth::check()) {
                // Get consent status from database for authenticated users
                $user = Auth::user();
                $userConsentStatus = $this->consentService->getUserConsentStatus($user);

                $consentData = [
                    'source' => 'database',
                    'user_id' => $user->id,
                    'consents' => $this->mapUserConsentsToCategories($userConsentStatus['userConsents']),
                    'last_updated' => $userConsentStatus['last_updated'] ?? null,
                ];

                $this->auditService->logUserAction(
                    $user,
                    'cookie_consent_status_retrieved',
                    [
                        'consent_source' => 'database',
                        'categories_count' => count($consentData['consents'])
                    ],
                    GdprActivityCategory::GDPR_ACTIONS
                );
            } else {
                // For anonymous users, return default structure
                $consentData = [
                    'source' => 'anonymous',
                    'user_id' => null,
                    'consents' => $this->getDefaultConsentCategories(),
                    'last_updated' => null,
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $consentData,
                'consent_required' => true,
                'available_categories' => $this->getAvailableConsentCategories()
            ]);
        } catch (\Exception $e) {
            $this->errorManager->handle('COOKIE_CONSENT_STATUS_ERROR', [
                'user_id' => Auth::id(),
                'session_id' => session()->getId(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'timestamp' => now()->toIso8601String(),
                'error' => $e->getMessage()
            ], $e);

            return response()->json([
                'success' => false,
                'error' => __('cookie.consent_status_error'),
                'data' => [
                    'source' => 'fallback',
                    'consents' => $this->getDefaultConsentCategories()
                ]
            ], 500);
        }
    }

    /**
     * Save cookie consent choices
     *
     * @param Request $request
     * @return JsonResponse
     * @privacy-safe Saves consent for authenticated users or returns success for anonymous
     */
    public function saveConsent(Request $request): JsonResponse {
        try {
            $this->logger->info('[Cookie Consent] Saving consent choices', [
                'user_id' => Auth::id(),
                'session_id' => session()->getId(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'timestamp' => now()->toIso8601String(),
                'is_authenticated' => Auth::check(),
                'request_data' => $request->except(['_token']),
                'log_category' => 'COOKIE_CONSENT_SAVE'
            ]);

            // Validate consent data
            $validationRules = [
                'consents' => 'required|array',
                'consent_version' => 'sometimes|string|max:20',
                'source' => 'sometimes|string|in:banner,preferences,api'
            ];

            // Add dynamic validation rules for each consent category from Enum
            foreach (CookieConsentCategory::cases() as $category) {
                $fieldName = "consents.{$category->value}";
                if ($category->isRequired()) {
                    $validationRules[$fieldName] = 'required|boolean';
                } else {
                    $validationRules[$fieldName] = 'sometimes|boolean';
                }
            }

            $validator = Validator::make($request->all(), $validationRules);

            if ($validator->fails()) {
                $this->logger->warning('[Cookie Consent] Validation failed', [
                    'user_id' => Auth::id(),
                    'validation_errors' => $validator->errors()->toArray(),
                    'request_data' => $request->except(['_token']),
                    'log_category' => 'COOKIE_CONSENT_VALIDATION_ERROR'
                ]);

                return response()->json([
                    'success' => false,
                    'error' => __('cookie.validation_error'),
                    'validation_errors' => $validator->errors()
                ], 422);
            }

            $validated = $validator->validated();
            $consentData = $validated['consents'];

            // Ensure essential cookies are always true using Enum
            $consentData[CookieConsentCategory::ESSENTIAL->value] = true;

            if (Auth::check()) {
                // Save to database for authenticated users
                $user = Auth::user();

                $result = $this->consentService->updateUserConsents($user, $consentData);

                $this->auditService->logUserAction(
                    $user,
                    'cookie_consent_updated',
                    [
                        'previous_consents' => $result['previous'] ?? [],
                        'new_consents' => $consentData,
                        'consent_source' => $validated['source'] ?? 'api',
                        'consent_version' => $validated['consent_version'] ?? '1.0'
                    ],
                    GdprActivityCategory::GDPR_ACTIONS
                );

                $this->logger->info('[Cookie Consent] Database consent updated successfully', [
                    'user_id' => $user->id,
                    'consents' => $consentData,
                    'log_category' => 'COOKIE_CONSENT_SAVE_SUCCESS'
                ]);

                return response()->json([
                    'success' => true,
                    'message' => __('cookie.consent_saved_successfully'),
                    'data' => [
                        'consents' => $consentData,
                        'saved_to' => 'database',
                        'user_id' => $user->id
                    ]
                ]);
            } else {
                // For anonymous users, just confirm receipt (they handle storage client-side)
                $this->logger->info('[Cookie Consent] Anonymous consent acknowledged', [
                    'session_id' => session()->getId(),
                    'consents' => $consentData,
                    'ip_address' => $request->ip(),
                    'log_category' => 'COOKIE_CONSENT_ANONYMOUS'
                ]);

                return response()->json([
                    'success' => true,
                    'message' => __('cookie.consent_acknowledged'),
                    'data' => [
                        'consents' => $consentData,
                        'saved_to' => 'local_storage',
                        'user_id' => null
                    ]
                ]);
            }
        } catch (\Exception $e) {
            // Safe error handling - ensure all values are strings
            $errorData = [
                'user_id' => Auth::id(),
                'session_id' => session()->getId(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'timestamp' => now()->toIso8601String(),
                'request_data' => $request->except(['_token']),
                'error_message' => (string) $e->getMessage(),
                'error_type' => get_class($e),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine()
            ];

            // Log directly to Laravel log to avoid UEM issues
            \Log::error('COOKIE_CONSENT_SAVE_ERROR', $errorData);

            return response()->json([
                'success' => false,
                'error' => __('cookie.consent_save_error')
            ], 500);
        }
    }

    /**
     * Get available consent categories configuration from database
     *
     * @return array
     */
    private function getAvailableConsentCategories(): array {
        try {
            $this->logger->info('[Cookie Consent] Loading consent categories using Enum', [
                'timestamp' => now()->toIso8601String(),
                'log_category' => 'COOKIE_CONSENT_CATEGORIES_LOAD'
            ]);

            // Get base categories from Enum
            $categories = CookieConsentCategory::toArray();

            // Enrich with database consent types
            $consentTypes = ConsentType::where('is_active', true)
                ->orderBy('priority_order')
                ->get();

            foreach ($consentTypes as $consentType) {
                $category = CookieConsentCategory::fromConsentTypeSlug($consentType->slug);

                if ($category) {
                    $categoryKey = $category->value;

                    // Initialize consent_types array if not exists
                    if (!isset($categories[$categoryKey]['consent_types_details'])) {
                        $categories[$categoryKey]['consent_types_details'] = [];
                    }

                    // Add detailed consent type information
                    $categories[$categoryKey]['consent_types_details'][] = [
                        'id' => $consentType->id,
                        'slug' => $consentType->slug,
                        'legal_basis' => $consentType->legal_basis,
                        'is_required' => $consentType->is_required,
                        'can_withdraw' => $consentType->can_withdraw,
                        'priority_order' => $consentType->priority_order
                    ];
                }
            }

            $this->logger->info('[Cookie Consent] Consent categories loaded successfully with Enum', [
                'categories_count' => count($categories),
                'total_consent_types' => $consentTypes->count(),
                'enum_categories' => array_keys($categories),
                'log_category' => 'COOKIE_CONSENT_CATEGORIES_SUCCESS'
            ]);

            return $categories;
        } catch (\Exception $e) {
            $this->logger->warning('[Cookie Consent] Failed to load categories, using Enum fallback', [
                'error' => $e->getMessage(),
                'log_category' => 'COOKIE_CONSENT_CATEGORIES_FALLBACK'
            ]);

            // Fallback to basic Enum data
            return CookieConsentCategory::toArray();
        }
    }

    /**
     * Get default consent categories for new visitors
     *
     * @return array
     */
    private function getDefaultConsentCategories(): array {
        $defaults = [];

        foreach (CookieConsentCategory::cases() as $category) {
            $defaults[$category->value] = $category->defaultValue();
        }

        return $defaults;
    }

    /**
     * Map user consents from ConsentService to cookie categories using Enum
     *
     * @param $userConsents
     * @return array
     */
    private function mapUserConsentsToCategories($userConsents): array {
        // Initialize with default values from Enum
        $categoryMap = [];
        foreach (CookieConsentCategory::cases() as $category) {
            $categoryMap[$category->value] = $category->defaultValue();
        }

        // Map user consents to categories
        foreach ($userConsents as $consent) {
            $category = CookieConsentCategory::fromConsentTypeSlug($consent->purpose);

            if ($category) {
                // For essential category, always true regardless of user consent
                if ($category === CookieConsentCategory::ESSENTIAL) {
                    $categoryMap[$category->value] = true;
                } else {
                    $categoryMap[$category->value] = $consent->granted;
                }
            }
        }

        return $categoryMap;
    }
}