<?php

namespace App\Http\Controllers\User;

use App\Events\UserWelcomeUpdated;
use App\Http\Requests\User\UpdatePersonalDataRequest;
use App\Models\User;
use App\Models\UserPersonalData;
use App\Helpers\FegiAuth;
use App\Services\Gdpr\GdprNotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Ultra\UltraLogManager\UltraLogManager;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * @Oracode Controller: Personal Data Management (OS1-Compliant)
 * 🎯 Purpose: Manage user personal data with GDPR compliance and fiscal validation
 * 🛡️ Privacy: Complete audit trail, consent management, data subject rights
 * 🧱 Core Logic: CRUD operations with UEM+ULM integration and fiscal validation
 * 🌍 Scale: MVP countries support with enterprise-grade validation
 * ⏰ MVP: Critical Personal Data Domain for 30 June deadline
 *
 * @package App\Http\Controllers\User
 * @author Padmin D. Curtis (AI Partner OS1-Compliant)
 * @version 1.0.0 (FlorenceEGI MVP - Personal Data Domain)
 * @deadline 2025-06-30
 */
class PersonalDataController extends BaseUserDomainController {

    protected GdprNotificationService $gdprNotificationService;

    protected UltraLogManager $logger;

    /**
     * @Oracode Constructor: Initialize Personal Data Controller with GDPR Notification Integration
     * 🎯 Purpose: Add GDPR notification triggering capability to personal data management
     * 📥 Input: UEM error manager, ULM logger, and GDPR notification service via DI
     * 🛡️ Privacy: Initialize GDPR-compliant notification system for consent changes
     * 🧱 Core Logic: Extends existing controller with notification orchestration capability
     *
     * @param ErrorManagerInterface $errorManager UEM error manager for robust error handling
     * @param UltraLogManager $logger ULM logger for audit trail and compliance
     * @param GdprNotificationService $gdprNotificationService GDPR notification orchestrator
     */
    public function __construct(
        ErrorManagerInterface $errorManager,
        UltraLogManager $logger,
        GdprNotificationService $gdprNotificationService
    ) {
        parent::__construct($errorManager, $logger);
        $this->gdprNotificationService = $gdprNotificationService;
    }

    // In app/Http/Controllers/User/PersonalDataController.php

    // In app/Http/Controllers/User/PersonalDataController.php

    /**
     * @Oracode Method: Display Personal Data Management Page (Refactored)
     * 🎯 Purpose: Show user's personal data, dynamically loading all consent definitions
     * and their current status from a central, database-driven source.
     * ... (il resto della documentazione rimane valido) ...
     */
    public function index(Request $request): View|RedirectResponse {
        // Check authentication and permissions
        $accessCheck = $this->checkWeakAuthAccess();

        if ($accessCheck !== true) {
            return $accessCheck;
        }

        try {
            $user = \App\Helpers\FegiAuth::user();

            // Audit access attempt
            $this->auditDataAccess('personal_data_view_requested', [
                'user_id' => $user->id,
                'auth_type' => $this->authType,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            // Load or create personal data record (non-consent data)
            $personalData = $this->getOrCreatePersonalData($user);

            // ===================================================================
            // === INIZIO BLOCCO MODIFICATO ===
            // ===================================================================
            $consentService = app(\App\Services\Gdpr\ConsentService::class);

            // 1. Recupera la DEFINIZIONE di tutti i tipi di consenso/accettazione
            //    dal nostro metodo refattorizzato che legge dal DB e usa la cache.
            //    Questo ci dà una collezione di DTO intelligenti.
            $allConsentDtos = $consentService->getConsentTypes();

            // 2. Costruiamo l'array con lo STATO (true/false) per l'utente corrente,
            //    mantenendo la struttura `$gdprConsents` che la vista si aspetta.
            $gdprConsents = [];
            foreach ($allConsentDtos as $consentDto) {
                $gdprConsents[$consentDto->key] = $consentService->hasConsent($user, $consentDto->key);
            }

            $this->logger->info('Loaded all consent definitions and user consent states', [
                'user_id' => $user->id,
                'consent_count' => count($allConsentDtos),
                'gdpr_consents' => $gdprConsents
            ]);

            // 3. Dalla collezione di DTO che abbiamo già, troviamo quello
            //    "contenitore" per i "processing purposes" da passare alla vista.
            $platformServicesDto = $allConsentDtos->firstWhere('key', 'platform-services');
            // ===================================================================
            // === FINE BLOCCO MODIFICATO ===
            // ===================================================================


            // Get user's country for form configuration (TUTTO IL RESTO È IDENTICO AL TUO CODICE)
            $userCountry = $this->getUserCountry();

            // Get available countries (MVP only)
            $availableCountries = $this->getMvpCountries();

            // Prepare view data with GDPR integration
            $viewData = [
                'user' => $user,
                'personalData' => $personalData,
                'gdprConsents' => $gdprConsents, // ✅ Ora è dinamico ma ha la stessa struttura di prima

                // ✅ Passiamo il DTO corretto alla vista
                'platformServicesConsent' => $platformServicesDto,

                'userCountry' => $userCountry,
                'availableCountries' => $availableCountries,
                'authType' => $this->authType,
                'canEdit' => $this->canEditPersonalData($user),
                'gdprSummary' => $this->getGdprSummary($user, $gdprConsents),
                'lastUpdate' => $personalData->updated_at,
                'validationConfig' => $this->getValidationConfig($userCountry),
                'consentHistory' => $consentService->getConsentHistory($user, 10),
                // Shipping Addresses
                'shippingAddresses' => $user->shippingAddresses()->orderBy('is_default', 'desc')->get(),
            ];

            // Log successful page load with consent integration info
            $this->logger->info('Personal data page displayed successfully with DYNAMIC GDPR integration', [
                'user_id' => $user->id,
                'auth_type' => $this->authType,
                'country' => $userCountry,
                // Aggiorniamo il log per usare la chiave corretta
                'has_data_processing_consent' => $gdprConsents['allow_personal_data_processing'] ?? false,
                'gdpr_consents_loaded' => count($gdprConsents),
                'platformServicesConsent' => $platformServicesDto
            ]);

            return view('users.domains.personal-data.index', $viewData);
        } catch (\Exception $e) {

            return $this->respondError('PERSONAL_DATA_VIEW_ERROR', $e, [
                'user_id' => \App\Helpers\FegiAuth::id(),
                'requested_page' => 'personal_data_index',
                'integration_point' => 'gdpr_consent_service'
            ]);
        }
    }

    /**
     * @Oracode Method: Update Personal Data
     * 🎯 Purpose: Process personal data updates with full validation and audit
     * 📥 Input: Validated update request
     * 📤 Output: Success response or error handling via UEM
     * 🛡️ Privacy: GDPR audit trail and consent management
     * 🧱 Core Logic: Transaction-based update with fiscal validation
     *
     * @param UpdatePersonalDataRequest $request Validated personal data update request
     * @return JsonResponse|RedirectResponse Update result
     */
    public function update(UpdatePersonalDataRequest $request): JsonResponse|RedirectResponse {
        // Additional access verification for updates
        $accessCheck = $this->checkWeakAuthAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }

        // For sensitive data updates, require identity verification
        $identityCheck = $this->requireIdentityVerification();
        if ($identityCheck !== true) {
            return $identityCheck;
        }

        try {
            $user = FegiAuth::user();
            $validatedData = $request->validated();

            $this->auditDataAccess('personal_data_update_requested', [
                'user_id' => $user->id,
                'fields_updated' => array_keys($validatedData),
                'auth_type' => $this->authType
            ]);

            // Process update in transaction
            $result = DB::transaction(function () use ($user, $validatedData, $request) {
                return $this->processPersonalDataUpdate($user, $validatedData, $request);
            });

            if ($result['success']) {
                $this->auditDataAccess('personal_data_updated_successfully', [
                    'user_id' => $user->id,
                    'changes_made' => $result['changes'],
                    'consent_updated' => $result['consent_updated']
                ]);

                $this->logger->info('Personal data updated successfully', [
                    'user_id' => $user->id,
                    'fields_changed' => count($result['changes']),
                    'auth_type' => $this->authType
                ]);

                return $this->respondSuccess(
                    __('user_personal_data.update_success'),
                    [
                        'updated_fields' => array_keys($result['changes']),
                        'consent_status' => $result['consent_updated']
                    ]
                );
            } else {
                throw new \Exception('Personal data update failed: ' . $result['error']);
            }
        } catch (\Exception $e) {
            return $this->respondError('PERSONAL_DATA_UPDATE_ERROR', $e, [
                'user_id' => FegiAuth::id(),
                'input_data_hash' => hash('sha256', json_encode($request->except(['password', 'password_confirmation'])))
            ]);
        }
    }

    /**
     * @Oracode Method: Export Personal Data (GDPR Right to Data Portability)
     * 🎯 Purpose: Generate GDPR-compliant data export for user
     * 📥 Input: HTTP request with export preferences
     * 📤 Output: Export response or redirect to processing page
     * 🛡️ Privacy: Full GDPR compliance with data minimization
     * 🧱 Core Logic: Generate comprehensive data export
     *
     * @param Request $request HTTP request with export parameters
     * @return JsonResponse|RedirectResponse Export result
     */
    public function export(Request $request): JsonResponse|RedirectResponse|StreamedResponse {
        $accessCheck = $this->checkWeakAuthAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }

        try {
            $user = FegiAuth::user();

            // Rate limiting check for exports
            if (!$this->canRequestDataExport($user)) {
                return $this->respondError('GDPR_EXPORT_RATE_LIMIT', new \Exception('Export rate limit exceeded'), [
                    'user_id' => $user->id,
                    'last_export' => $this->getLastExportDate($user)
                ]);
            }

            $this->auditDataAccess('personal_data_export_requested', [
                'user_id' => $user->id,
                'format' => $request->input('format', 'json'),
                'categories' => $request->input('categories', ['all'])
            ]);

            // Generate export data
            $exportData = $this->generatePersonalDataExport($user, $request->input('categories', ['all']));

            // Format based on request
            $format = $request->input('format', 'json');
            $formattedData = $this->formatExportData($exportData, $format);

            $this->logger->info('Personal data export generated', [
                'user_id' => $user->id,
                'format' => $format,
                'data_size' => strlen(json_encode($formattedData))
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $formattedData,
                    'export_date' => now()->toISOString(),
                    'format' => $format
                ]);
            }

            // For web requests, return download
            $filename = 'personal_data_' . $user->id . '_' . now()->format('Y-m-d_H-i-s') . '.' . $format;

            return response()->streamDownload(function () use ($formattedData, $format) {
                echo $format === 'json' ? json_encode($formattedData, JSON_PRETTY_PRINT) : $formattedData;
            }, $filename, $this->getExportHeaders($format));
        } catch (\Exception $e) {
            return $this->respondError('PERSONAL_DATA_EXPORT_ERROR', $e, [
                'user_id' => FegiAuth::id(),
                'export_format' => $request->input('format', 'json')
            ]);
        }
    }

    /**
     * @Oracode Method: Delete Personal Data (GDPR Right to Erasure)
     * 🎯 Purpose: Handle personal data deletion requests
     * 📥 Input: HTTP request with deletion confirmation
     * 📤 Output: Deletion confirmation or error
     * 🛡️ Privacy: GDPR-compliant data erasure with audit trail
     * 🧱 Core Logic: Secure deletion process with verification
     *
     * @param Request $request HTTP request with deletion parameters
     * @return JsonResponse|RedirectResponse Deletion result
     */
    public function destroy(Request $request): JsonResponse|RedirectResponse {
        $accessCheck = $this->checkWeakAuthAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }

        // Require strong authentication for deletion
        if (!FegiAuth::isStrongAuth()) {
            return $this->redirectToUpgrade();
        }

        // Require identity verification for deletion
        $identityCheck = $this->requireIdentityVerification();
        if ($identityCheck !== true) {
            return $identityCheck;
        }

        try {
            $user = FegiAuth::user();

            // Validate deletion request
            $request->validate([
                'confirm_deletion' => 'required|boolean|accepted',
                'deletion_reason' => 'nullable|string|max:500',
                'confirm_text' => 'required|string|in:DELETE'
            ]);

            $this->auditDataAccess('personal_data_deletion_requested', [
                'user_id' => $user->id,
                'reason' => $request->input('deletion_reason'),
                'ip_address' => $request->ip()
            ]);

            // Process deletion in transaction
            DB::transaction(function () use ($user, $request) {
                $this->processPersonalDataDeletion($user, $request->input('deletion_reason'));
            });

            $this->auditDataAccess('personal_data_deleted_successfully', [
                'user_id' => $user->id,
                'deletion_timestamp' => now()->toISOString()
            ]);

            $this->logger->info('Personal data deleted successfully', [
                'user_id' => $user->id,
                'auth_type' => $this->authType
            ]);

            return $this->respondSuccess(
                __('user_personal_data.deletion.request_submitted'),
                ['deletion_timestamp' => now()->toISOString()]
            );
        } catch (\Exception $e) {
            return $this->respondError('PERSONAL_DATA_DELETION_ERROR', $e, [
                'user_id' => FegiAuth::id()
            ]);
        }
    }

    /**
     * @Oracode Method: Get Required Domain Permission
     * 🎯 Purpose: Return permission required for personal data access
     * 📤 Output: Permission string for weak auth users
     * 🧱 Core Logic: Override from BaseUserDomainController
     *
     * @return string Permission required for personal data domain
     */
    protected function getRequiredDomainPermission(): string {
        return 'manage_profile';
    }

    /**
     * @Oracode Method: Get or Create Personal Data Record
     * 🎯 Purpose: Ensure user has personal data record, create if missing
     * 📥 Input: User instance
     * 📤 Output: UserPersonalData instance
     * 🧱 Core Logic: Lazy creation of personal data record
     *
     * @param User $user User instance
     * @return UserPersonalData Personal data record
     */
    private function getOrCreatePersonalData(User $user): UserPersonalData {
        return UserPersonalData::firstOrCreate(
            ['user_id' => $user->id],
            [
                'allow_personal_data_processing' => false,
                'processing_purposes' => [],
                'consent_updated_at' => now()
            ]
        );
    }

    /**
     * @Oracode Method: Check if User Can Edit Personal Data
     * 🎯 Purpose: Determine if user has edit permissions for personal data
     * 📥 Input: User instance
     * 📤 Output: Boolean indicating edit capability
     * 🧱 Core Logic: Permission and authentication level checks
     *
     * @param User $user User instance
     * @return bool True if user can edit personal data
     */
    private function canEditPersonalData(User $user): bool {
        // Users can always edit their own personal data if authenticated
        return FegiAuth::check() && FegiAuth::id() === $user->id;
    }

    /**
     * @Oracode Method: Get GDPR Summary for User
     * 🎯 Purpose: Generate GDPR compliance summary for display
     * 📥 Input: User instance
     * 📤 Output: Array with GDPR status information
     * 🛡️ Privacy: Privacy-focused summary generation
     *
     * @param User $user User instance
     * @return array<string, mixed> GDPR summary data
     */
    private function getGdprSummary(User $user): array {
        $personalData = $this->getOrCreatePersonalData($user);

        return [
            'consent_status' => $personalData->allow_personal_data_processing,
            'consent_date' => $personalData->consent_updated_at,
            'processing_purposes' => $personalData->processing_purposes ?: [],
            'data_retention_status' => 'active',
            'last_data_update' => $personalData->updated_at,
            'export_available' => $this->canRequestDataExport($user),
            'deletion_available' => FegiAuth::isStrongAuth()
        ];
    }

    /**
     * @Oracode Method: Get Validation Config for Frontend
     * 🎯 Purpose: Generate validation configuration for JavaScript
     * 📥 Input: User's country code
     * 📤 Output: Array with validation rules for frontend
     * 🧱 Core Logic: Country-specific validation config
     *
     * @param string $country User's country code
     * @return array<string, mixed> Validation configuration
     */
    private function getValidationConfig(string $country): array {
        try {
            $validator = \App\Services\Fiscal\FiscalValidatorFactory::create($country);
            $businessTypes = ['individual', 'business', 'corporation', 'partnership', 'non_profit'];

            $config = [
                'country' => $country,
                'validator_type' => $validator->getCountryCode(),
                'business_types' => []
            ];

            foreach ($businessTypes as $type) {
                $config['business_types'][$type] = $validator->getRequiredFields($type);
            }

            return $config;
        } catch (\Exception $e) {
            return [
                'country' => $country,
                'validator_type' => 'generic',
                'business_types' => []
            ];
        }
    }

    // In app/Http/Controllers/User/PersonalDataController.php
    // SOSTITUISCI L'INTERO METODO con questa versione finale.

    /**
     * @Oracode Method: Process Personal Data Update with Guarded Consent Logic
     * 🎯 Purpose: Atomically update personal data and related consents, but only
     * process consent changes if a user's choice has actually been modified.
     * 📥 Input: User, validated data with consents array, and request.
     * 📤 Output: Array with update results, including only actual changes.
     * 🛡️ Privacy: Prevents redundant GDPR consent records by comparing new and
     * old states before invoking the ConsentService.
     * 🧱 Core Logic: Implements an in-loop "Guard Clause" to ensure consent
     * processing is intentional and not a side-effect of other data updates.
     */
    private function processPersonalDataUpdate(User $user, array $validatedData, UpdatePersonalDataRequest $request): array {
        $personalData = $this->getOrCreatePersonalData($user);
        $changes = [];
        $consentUpdated = false;

        // ===================================================================
        // 1. GESTIONE DEI DATI UTENTE BASE (tabella users)
        // ===================================================================
        $userFields = ['name', 'last_name', 'email', 'nick_name'];
        foreach ($userFields as $field) {
            // ✅ FIX: Verifica se il campo è presente nei dati validati (anche se vuoto)
            // Questo permette di cancellare valori esistenti impostando il campo a stringa vuota o null
            if (array_key_exists($field, $validatedData)) {
                $oldValue = $user->getRawOriginal($field);
                $newValue = $validatedData[$field];

                // ✅ NORMALIZZAZIONE: Converte stringhe vuote in null per campi opzionali come nick_name
                if (in_array($field, ['nick_name']) && $newValue === '') {
                    $newValue = null;
                }

                if ($oldValue !== $newValue) {
                    $changes[$field] = ['old' => $oldValue, 'new' => $newValue];
                    $user->$field = $newValue;
                }
            }
        }
        if ($user->isDirty()) {
            $user->save();
        }

        // ===================================================================
        // 2. GESTIONE DEI DATI PERSONALI (tabella user_personal_data)
        // ===================================================================
        $personalDataFields = [
            // ❌ RIMOSSO: first_name non esiste in user_personal_data
            // ❌ RIMOSSO: last_name è nella tabella users, non user_personal_data
            'birth_date',
            'birth_place',
            'gender',
            'street',
            'city',
            'zip',
            'province',
            'country',
            'region',
            'home_phone',
            'cell_phone',
            'work_phone',
            'emergency_contact',
            'fiscal_code',
            'tax_id_number'
        ];

        foreach ($personalDataFields as $field) {
            // ✅ FIX: Usa array_key_exists invece di isset per permettere la cancellazione dei campi
            if (array_key_exists($field, $validatedData)) {
                $newValue = $validatedData[$field];

                // ✅ NORMALIZZAZIONE: Converte stringhe vuote in null per campi opzionali
                if ($newValue === '') {
                    $newValue = null;
                }

                if ($personalData->$field !== $newValue) {
                    $changes[$field] = ['old' => $personalData->$field, 'new' => $newValue];
                    $personalData->$field = $newValue;
                }
            }
        }
        if ($personalData->isDirty()) {
            $personalData->save();
        }

        // ===================================================================
        // 3. GESTIONE DEI CONSENSI (con Guardia Logica)
        // ===================================================================
        if (isset($validatedData['consents']) && is_array($validatedData['consents'])) {

            $consentService = app(\App\Services\Gdpr\ConsentService::class);
            $consentsToProcess = $validatedData['consents'];

            // ✅ OTTIMIZZAZIONE: Catturiamo lo stato dei consensi UNA SOLA VOLTA.
            $oldConsentState = $this->captureCurrentConsentState($user);
            $this->logger->info('Captured consent state before modifications.', [
                'user_id' => $user->id,
                'consent_state' => $oldConsentState
            ]);

            foreach ($consentsToProcess as $consentType => $consentValue) {
                $consentGranted = (bool) $consentValue;
                $currentValue = $oldConsentState[$consentType] ?? null;

                // --- GUARDIA LOGICA ---
                // Eseguiamo la logica solo se lo stato è effettivamente cambiato.
                if ($currentValue === null || $currentValue !== $consentGranted) {

                    $this->logger->info('Consent change detected. Processing update.', [
                        'user_id' => $user->id,
                        'consent_type' => $consentType,
                        'from' => $currentValue,
                        'to' => $consentGranted
                    ]);

                    $metadata = [
                        'updated_via' => 'personal_data_form',
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                        'timestamp' => now()->toISOString()
                    ];

                    // ✅ CORREZIONE: Gestione corretta dei metadati specifici
                    $consentMetadata = $validatedData['consent_metadata'] ?? [];
                    if ($consentType === 'allow_personal_data_processing' && isset($consentMetadata['processing_purposes'])) {
                        $metadata['processing_purposes'] = $consentMetadata['processing_purposes'];
                    }

                    if ($consentGranted) {
                        $result = $consentService->grantConsent($user, $consentType, $metadata);
                    } else {
                        $result = $consentService->withdrawConsent($user, $consentType, $metadata);
                    }

                    if ($result) {
                        $consentUpdated = true;
                        $changes["consent_{$consentType}"] = ['old' => !$consentGranted, 'new' => $consentGranted];

                        // ✅ OTTIMIZZAZIONE: Logghiamo il successo solo quando l'azione è avvenuta.
                        $this->logger->info('Consent processed successfully via ConsentService.', [
                            'user_id' => $user->id,
                            'consent_type' => $consentType,
                            'granted' => $consentGranted
                        ]);
                    }
                } else {
                    $this->logger->info('No change for consent type, skipping update.', [
                        'user_id' => $user->id,
                        'consent_type' => $consentType
                    ]);
                }
            }

            // La logica di notifica può rimanere, userà lo stato aggiornato
            $newConsentState = $this->captureCurrentConsentState($user);
            $this->handleConsentChangeNotifications($user, $oldConsentState, $newConsentState, $request);
        }

        // ===================================================================
        // 3. GESTIONE DEI DATI UTENTE (es. lingua)
        // ===================================================================
        // Evitiamo duplicazioni: qui gestiamo SOLO il campo 'language'.
        if (array_key_exists('language', $validatedData)) {
            $oldValue = $user->getRawOriginal('language');
            $newValue = $validatedData['language'];

            // Normalizza stringa vuota a null per consentire la cancellazione
            if ($newValue === '') {
                $newValue = null;
            }

            if ($oldValue !== $newValue) {
                $changes['language'] = ['old' => $oldValue, 'new' => $newValue];
                $user->language = $newValue;
                $user->save();
            }
        }

        // ===================================================================
        // 4. BROADCAST EVENT FOR REAL-TIME UI UPDATES
        // ===================================================================

        // Se ci sono stati cambiamenti ai dati che influenzano il messaggio di benvenuto
        $welcomeRelatedFields = ['nick_name', 'name', 'last_name', 'gender'];
        $welcomeDataChanged = array_intersect_key($changes, array_flip($welcomeRelatedFields));

        if (!empty($welcomeDataChanged)) {
            $this->logger->info('Broadcasting welcome message update', [
                'user_id' => $user->id,
                'changed_fields' => array_keys($welcomeDataChanged)
            ]);

            // Broadcast l'evento per aggiornare tutti i client connessi
            broadcast(new UserWelcomeUpdated($user->id));
        }

        // ===================================================================
        // 5. RISULTATO FINALE
        // ===================================================================
        return [
            'success' => true,
            'changes' => $changes,
            'consent_updated' => $consentUpdated,
            'personal_data' => $personalData->fresh(),
            'user' => $user->fresh()
        ];
    }

    /**
     * @Oracode Method: Capture Current Consent State (Robust & Efficient)
     * 🎯 Purpose: Snapshot complete consent state for all relevant types using
     * a single, efficient service call.
     * 📥 Input: User instance.
     * 📤 Output: A key-value array of all user consents, e.g., ['marketing' => true, 'analytics' => false].
     * 🛡️ Privacy: Read-only operation, leverages ConsentService for data access.
     * 🧱 Core Logic: Calls `ConsentService::getUserConsentStatus()` once and transforms
     * the result into a simple key-value array for easy comparison.
     */
    private function captureCurrentConsentState(User $user): array {
        try {
            $consentService = app(\App\Services\Gdpr\ConsentService::class);

            // ✅ SOLUZIONE: Chiamiamo il metodo più completo che recupera tutto in una volta.
            $fullConsentStatus = $consentService->getUserConsentStatus($user);

            $consentState = [];

            // Trasformiamo la collezione di oggetti in un semplice array [key => boolean]
            foreach ($fullConsentStatus['userConsents'] as $consentObject) {
                $consentState[$consentObject->purpose] = (bool)$consentObject->granted;
            }

            $this->logger->debug('Consent state captured successfully using getUserConsentStatus.', [
                'user_id' => $user->id,
                'captured_state' => $consentState,
            ]);

            return $consentState;
        } catch (\Throwable $e) {
            $this->logger->warning('Failed to capture consent state', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'operation' => 'consent_state_capture'
            ]);

            return []; // Ritorna uno stato vuoto in caso di errore
        }
    }

    /**
     * @Oracode Method: Generate Personal Data Export
     * 🎯 Purpose: Create comprehensive data export for GDPR compliance
     * 📥 Input: User and export categories
     * 📤 Output: Array with exported data
     * 🛡️ Privacy: Only include data user has consented to process
     *
     * @param User $user User instance
     * @param array<int, string> $categories Export categories
     * @return array<string, mixed> Exported data
     */
    private function generatePersonalDataExport(User $user, array $categories): array {
        $personalData = $this->getOrCreatePersonalData($user);
        $exportData = [
            'export_info' => [
                'user_id' => $user->id,
                'export_date' => now()->toISOString(),
                'categories' => $categories,
                'gdpr_basis' => 'Article 20 - Right to data portability'
            ]
        ];

        if (in_array('basic', $categories) || in_array('all', $categories)) {
            $exportData['basic_information'] = [
                'first_name' => $personalData->first_name ?? $user->name,
                'last_name' => $personalData->last_name ?? $user->last_name,
                'email' => $user->email,
                'birth_date' => $personalData->birth_date?->format('Y-m-d'),
                'gender' => $personalData->gender
            ];
        }

        if (in_array('address', $categories) || in_array('all', $categories)) {
            $exportData['address_information'] = [
                'street' => $personalData->street,
                'city' => $personalData->city,
                'zip' => $personalData->zip,
                'country' => $personalData->country,
                'region' => $personalData->region,
                'province' => $personalData->province
            ];
        }

        if (in_array('contact', $categories) || in_array('all', $categories)) {
            $exportData['contact_information'] = [
                'home_phone' => $personalData->home_phone,
                'cell_phone' => $personalData->cell_phone,
                'work_phone' => $personalData->work_phone
            ];
        }

        if (in_array('consent', $categories) || in_array('all', $categories)) {
            $exportData['consent_information'] = [
                'data_processing_consent' => $personalData->allow_personal_data_processing,
                'processing_purposes' => $personalData->processing_purposes,
                'consent_date' => $personalData->consent_updated_at?->toISOString()
            ];
        }

        return $exportData;
    }

    /**
     * @Oracode Method: Check if User Can Request Data Export
     * 🎯 Purpose: Rate limiting for data export requests
     * 📥 Input: User instance
     * 📤 Output: Boolean indicating export availability
     * 🧱 Core Logic: GDPR-compliant rate limiting
     *
     * @param User $user User instance
     * @return bool True if user can request export
     */
    private function canRequestDataExport(User $user): bool {
        // Allow one export per 30 days per GDPR guidelines
        // This would typically check a data_exports table
        return true; // Simplified for MVP
    }

    /**
     * @Oracode Method: Get Last Export Date
     * 🎯 Purpose: Get user's last data export date for rate limiting
     * 📥 Input: User instance
     * 📤 Output: Carbon date or null
     * 🧱 Core Logic: Export history tracking
     *
     * @param User $user User instance
     * @return Carbon|null Last export date
     */
    private function getLastExportDate(User $user): ?Carbon {
        // This would typically query a data_exports table
        return null; // Simplified for MVP
    }

    /**
     * @Oracode Method: Format Export Data
     * 🎯 Purpose: Format exported data in requested format
     * 📥 Input: Export data array and format
     * 📤 Output: Formatted data string
     * 🧱 Core Logic: Multi-format export support
     *
     * @param array<string, mixed> $data Export data
     * @param string $format Export format (json, csv, xml)
     * @return string Formatted export data
     */
    private function formatExportData(array $data, string $format): string {
        switch ($format) {
            case 'csv':
                return $this->convertToCSV($data);
            case 'xml':
                return $this->convertToXML($data);
            case 'json':
            default:
                return json_encode($data, JSON_PRETTY_PRINT);
        }
    }

    /**
     * @Oracode Method: Convert Data to CSV
     * 🎯 Purpose: Convert export data to CSV format
     * 📥 Input: Export data array
     * 📤 Output: CSV formatted string
     * 🧱 Core Logic: Flatten nested arrays for CSV
     *
     * @param array<string, mixed> $data Export data
     * @return string CSV formatted data
     */
    private function convertToCSV(array $data): string {
        // Simplified CSV conversion for MVP
        $csv = "Category,Field,Value\n";

        foreach ($data as $category => $fields) {
            if (is_array($fields)) {
                foreach ($fields as $field => $value) {
                    $csv .= sprintf(
                        "%s,%s,%s\n",
                        $category,
                        $field,
                        is_array($value) ? json_encode($value) : $value
                    );
                }
            }
        }

        return $csv;
    }

    /**
     * @Oracode Method: Convert Data to XML
     * 🎯 Purpose: Convert export data to XML format
     * 📥 Input: Export data array
     * 📤 Output: XML formatted string
     * 🧱 Core Logic: Simple XML structure
     *
     * @param array<string, mixed> $data Export data
     * @return string XML formatted data
     */
    private function convertToXML(array $data): string {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n<personal_data_export>\n";

        foreach ($data as $category => $fields) {
            $xml .= " <{$category}>\n";
            if (is_array($fields)) {
                foreach ($fields as $field => $value) {
                    $xml .= " <{$field}>" . htmlspecialchars((string)$value) . "</{$field}>\n";
                }
            }
            $xml .= " </{$category}>\n";
        }

        $xml .= "</personal_data_export>\n";

        return $xml;
    }

    /**
     * @Oracode Method: Get Export Headers
     * 🎯 Purpose: Return appropriate HTTP headers for export download
     * 📥 Input: Export format
     * 📤 Output: Array of HTTP headers
     * 🧱 Core Logic: Format-specific headers
     *
     * @param string $format Export format
     * @return array<string, string> HTTP headers
     */
    private function getExportHeaders(string $format): array {
        $headers = [
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ];

        switch ($format) {
            case 'csv':
                $headers['Content-Type'] = 'text/csv';
                break;
            case 'xml':
                $headers['Content-Type'] = 'application/xml';
                break;
            case 'json':
            default:
                $headers['Content-Type'] = 'application/json';
                break;
        }

        return $headers;
    }

    /**
     * @Oracode Method: Process Personal Data Deletion
     * 🎯 Purpose: Execute GDPR-compliant data deletion
     * 📥 Input: User and deletion reason
     * 📤 Output: Void (processes deletion)
     * 🛡️ Privacy: Secure deletion with audit trail
     *
     * @param User $user User instance
     * @param string|null $reason Deletion reason
     * @return void
     */
    private function processPersonalDataDeletion(User $user, ?string $reason): void {
        $personalData = UserPersonalData::where('user_id', $user->id)->first();

        if ($personalData) {
            // Create deletion audit record before deletion
            $this->auditDataAccess('personal_data_deletion_executed', [
                'user_id' => $user->id,
                'data_fields_deleted' => array_keys($personalData->toArray()),
                'reason' => $reason,
                'deletion_method' => 'user_requested'
            ]);

            // Perform deletion
            $personalData->delete();
        }

        // Clear related data processing consents
        $user->update([
            'consent' => false,
            'consent_summary' => null,
            'consents_updated_at' => now()
        ]);
    }

    /**
     * @Oracode Method: Handle Consent Change Notifications
     * 🎯 Purpose: Detect consent changes and trigger GDPR notifications for user awareness
     * 📥 Input: User, old consent state, new consent state, HTTP request context
     * 📤 Output: Void (side effect: notifications created)
     * 🛡️ Privacy: Only processes user's own consent changes with complete audit trail
     * 🧱 Core Logic: Compare states, detect significant changes, trigger notifications gracefully
     */
    private function handleConsentChangeNotifications(
        User $user,
        ?array $oldConsentState,
        array $newConsentState,
        UpdatePersonalDataRequest $request
    ): void {
        // ✅ OS1.5 PROACTIVE SECURITY: Guard against missing old state
        if (empty($oldConsentState) || empty($newConsentState)) {
            $this->logger->info('Skipping consent change notifications - insufficient state data', [
                'user_id' => $user->id,
                'old_state_available' => !empty($oldConsentState),
                'new_state_available' => !empty($newConsentState),
                'operation' => 'consent_change_detection'
            ]);
            return;
        }

        try {
            // ✅ OS1.5 EXPLICITLY INTENTIONAL: Detect all significant consent changes
            $detectedChanges = $this->detectSignificantConsentChanges($oldConsentState, $newConsentState);

            if (empty($detectedChanges)) {
                $this->logger->info('No significant consent changes detected - no notifications triggered', [
                    'user_id' => $user->id,
                    'old_state' => $oldConsentState,
                    'new_state' => $newConsentState
                ]);
                return;
            }

            // ✅ OS1.5 SIMPLICITY EMPOWERMENT: Build notification context once, reuse for all
            $notificationContext = $this->buildNotificationContext($request, $detectedChanges);

            $this->logger->info('Consent changes detected - triggering GDPR notifications', [
                'user_id' => $user->id,
                'changes_count' => count($detectedChanges),
                'change_types' => array_keys($detectedChanges),
                'operation' => 'gdpr_notification_trigger'
            ]);

            // ✅ OS1.5 CIRCOLARITÀ VIRTUOSA: Each notification improves user trust
            $notificationResults = [];
            foreach ($detectedChanges as $consentType => $change) {
                $notificationResults[$consentType] = $this->triggerSingleConsentNotification(
                    $user,
                    $consentType,
                    $change,
                    $notificationContext
                );
            }

            // ✅ OS1.5 INTERROGABILITÀ TOTALE: Complete audit trail of notification results
            $this->logger->info('GDPR notifications triggered successfully', [
                'user_id' => $user->id,
                'notifications_triggered' => count(array_filter($notificationResults)),
                'notifications_failed' => count(array_filter($notificationResults, fn($r) => !$r)),
                'results_detail' => $notificationResults,
                'operation' => 'gdpr_notification_completion'
            ]);
        } catch (\Throwable $e) {
            // ✅ OS1.5 RESILIENZA PROGRESSIVA: Notification failure doesn't break consent save
            $this->logger->warning('GDPR notification triggering failed - consent changes saved but notifications not sent', [
                'user_id' => $user->id,
                'error_message' => $e->getMessage(),
                'error_type' => get_class($e),
                'old_state' => $oldConsentState,
                'new_state' => $newConsentState,
                'operation' => 'gdpr_notification_error'
            ]);

            // Don't throw - consent save should succeed even if notifications fail
        }
    }

    /**
     * @Oracode Method: Detect Significant Consent Changes
     * 🎯 Purpose: Identify consent changes that require user notification per GDPR
     * 📥 Input: Old and new consent state arrays
     * 📤 Output: Array of significant changes with change metadata
     * 🧱 Core Logic: Compare boolean consent states, identify meaningful transitions
     */
    private function detectSignificantConsentChanges(array $oldState, array $newState): array {
        $significantChanges = [];

        // ✅ OS1.5 EXPLICITLY INTENTIONAL: Check each consent type for significant changes
        $consentTypesToCheck = [
            'allow_personal_data_processing',
            'marketing',
            'analytics'
        ];

        foreach ($consentTypesToCheck as $consentType) {
            $oldValue = $oldState[$consentType] ?? false;
            $newValue = $newState[$consentType] ?? false;

            // ✅ OS1.5 SEMANTIC CONSISTENCY: Only boolean state changes are significant
            if ($oldValue !== $newValue) {
                $significantChanges[$consentType] = [
                    'previous_value' => $oldValue,
                    'new_value' => $newValue,
                    'change_type' => $newValue ? 'granted' : 'withdrawn',
                    'timestamp' => now()->toISOString()
                ];

                $this->logger->debug('Significant consent change detected', [
                    'consent_type' => $consentType,
                    'change' => $significantChanges[$consentType],
                    'operation' => 'change_detection'
                ]);
            }
        }

        return $significantChanges;
    }

    /**
     * @Oracode Method: Build Notification Context
     * 🎯 Purpose: Create complete context for GDPR notification with audit trail data
     * 📥 Input: HTTP request and detected changes
     * 📤 Output: Context array for notification service
     * 🛡️ Privacy: Include only necessary context for GDPR compliance
     */
    private function buildNotificationContext(UpdatePersonalDataRequest $request, array $detectedChanges): array {
        return [
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'session_id' => session()->getId(),
            'timestamp' => now()->toISOString(),
            'changes_count' => count($detectedChanges),
            'change_source' => 'personal_data_form',
            'change_types' => array_keys($detectedChanges)
        ];
    }

    /**
     * @Oracode Method: Trigger Single Consent Notification
     * 🎯 Purpose: Trigger GDPR notification for specific consent change
     * 📥 Input: User, consent type, change details, notification context
     * 📤 Output: Boolean success/failure
     * 🛡️ Privacy: Graceful failure ensures consent save succeeds even if notification fails
     */
    private function triggerSingleConsentNotification(
        User $user,
        string $consentType,
        array $change,
        array $baseContext
    ): bool {
        try {
            // ✅ OS1.5 MODULARITÀ SEMANTICA: Build context specific to this change
            $changeContext = array_merge($baseContext, [
                'consent_type' => $consentType,
                'previous_value' => $change['previous_value'],
                'new_value' => $change['new_value'],
                'change_type' => $change['change_type']
            ]);

            // ✅ OS1.5 CIRCOLARITÀ VIRTUOSA: Each notification builds user trust
            $notificationResult = $this->gdprNotificationService->dispatchNotification(
                $user,
                'consent_updated',
                $changeContext
            );

            $this->logger->info('Single consent notification triggered successfully', [
                'user_id' => $user->id,
                'consent_type' => $consentType,
                'change_type' => $change['change_type'],
                'notification_result' => is_object($notificationResult) ? 'success' : 'failed'
            ]);

            return is_object($notificationResult);
        } catch (\Throwable $e) {
            $this->logger->warning('Single consent notification failed', [
                'user_id' => $user->id,
                'consent_type' => $consentType,
                'error' => $e->getMessage(),
                'operation' => 'single_notification_trigger'
            ]);

            return false;
        }
    }


    /**
     * Override Identity Verification for Personal Data Updates
     * @Oracode Method: Override Identity Verification for Personal Data Updates
     * 🎯 Purpose: Allow personal data updates without additional identity verification for MVP
     * 📤 Output: Always returns true for authenticated users
     * ⚠️ MVP: Simplified verification logic for personal data updates
     */
    protected function requireIdentityVerification(): bool|RedirectResponse {
        $user = FegiAuth::user();

        $this->logger->info('Identity verification check bypassed for personal data updates', [
            'user_id' => $user?->id,
            'auth_type' => FegiAuth::getAuthType(),
            'operation' => 'personal_data_update',
            'reason' => 'MVP_simplified_verification'
        ]);

        // ✅ MVP: For personal data updates, being authenticated is sufficient
        // Future: Implement proper re-verification flow
        if (FegiAuth::check()) {
            return true;
        }

        // If not authenticated at all, use parent logic
        return parent::requireIdentityVerification();
    }

    /**
     * Save IBAN for personal data
     */
    public function saveIban(Request $request): JsonResponse {
        try {
            // Validate IBAN
            $validated = $request->validate([
                'iban' => 'required|string|min:15|max:34|regex:/^[A-Z]{2}[0-9]{2}[A-Z0-9]+$/',
            ]);

            $user = FegiAuth::user();
            $personalData = $this->getOrCreatePersonalData($user);

            // Save IBAN
            $personalData->update([
                'iban' => strtoupper(str_replace(' ', '', $validated['iban']))
            ]);

            $this->logger->info('[Personal Data] IBAN saved', [
                'user_id' => $user->id,
                'auth_type' => FegiAuth::getAuthType(),
            ]);

            return response()->json([
                'success' => true,
                'message' => __('iban_modal.success')
            ]);
        } catch (\Exception $e) {
            $this->logger->error('[Personal Data] IBAN save failed', [
                'error' => $e->getMessage(),
                'user_id' => FegiAuth::id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => __('iban_modal.error_save')
            ], 400);
        }
    }
}
