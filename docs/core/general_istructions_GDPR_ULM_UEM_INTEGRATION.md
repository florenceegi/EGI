# **GDPR, ULM, UEM - Integrazione nelle Classi che Modificano Dati**

## **Pattern di Integrazione Ultra Ecosystem**

### **Dependency Injection Obbligatoria**
```php
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use App\Services\Gdpr\AuditLogService;
use App\Services\Gdpr\ConsentService;
use App\Enums\Gdpr\GdprActivityCategory;
```

---

## **1. CONTROLLER PATTERN**

### **Constructor e Firma Completa**
```php
<?php

namespace App\Http\Controllers;

use App\Enums\Gdpr\GdprActivityCategory;
use App\Services\Gdpr\AuditLogService;
use App\Services\Gdpr\ConsentService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * @Oracode Controller: User Profile Data Management
 * 🎯 Purpose: Handles user profile modifications with full GDPR compliance
 * 🛡️ Privacy: Manages personal data updates with complete audit trail
 * 🧱 Core Logic: Updates profile data, logs actions, handles consent changes
 *
 * @package App\Http\Controllers\Profile
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Profile Data Management)
 * @date 2025-09-21
 * @purpose Handle user profile data modifications with GDPR compliance
 */
class ProfileController extends Controller
{
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;
    protected AuditLogService $auditService;
    protected ConsentService $consentService;

    /**
     * Constructor with dependency injection
     *
     * @param UltraLogManager $logger Ultra logging manager for audit trails
     * @param ErrorManagerInterface $errorManager Ultra error manager for error handling
     * @param AuditLogService $auditService GDPR audit logging service
     * @param ConsentService $consentService GDPR consent management service
     * @privacy-safe All injected services handle GDPR compliance
     */
    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        AuditLogService $auditService,
        ConsentService $consentService
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->auditService = $auditService;
        $this->consentService = $consentService;
        $this->middleware('auth');
    }
}
```

### **Method Pattern - Modifica Dati Personali**
```php
/**
 * Update user personal data with full GDPR compliance
 *
 * @param Request $request HTTP request with user data updates
 * @return RedirectResponse Redirect response with success/error message
 * @throws \Exception When data update fails or validation errors occur
 * @privacy-safe Only updates authenticated user's own personal data
 */
public function updatePersonalData(Request $request): RedirectResponse
{
    try {
        $user = Auth::user();
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date|before:today'
        ]);

        // 1. ULM: Log operation start with context
        $this->logger->info('Personal data update initiated', [
            'user_id' => $user->id,
            'fields_to_update' => array_keys($validated),
            'ip_address' => $request->ip(),
            'user_agent' => substr($request->userAgent(), 0, 255),
            'log_category' => 'PERSONAL_DATA_UPDATE_START'
        ]);

        // 2. Store previous values for audit
        $previousData = [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'phone' => $user->phone,
            'birth_date' => $user->birth_date
        ];

        // 3. Perform data modification
        $user->update($validated);

        // 4. GDPR: Log user action with AuditLogService - PERSONAL_DATA_UPDATE category
        $this->auditService->logUserAction($user, 'personal_data_updated', [
            'fields_updated' => array_keys($validated),
            'previous_values' => $previousData,
            'new_values' => $validated,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ], GdprActivityCategory::PERSONAL_DATA_UPDATE);

        // 5. ULM: Log successful completion
        $this->logger->info('Personal data update completed successfully', [
            'user_id' => $user->id,
            'fields_updated' => array_keys($validated),
            'log_category' => 'PERSONAL_DATA_UPDATE_SUCCESS'
        ]);

        return redirect()->route('profile.edit')
            ->with('success', __('profile.personal_data_updated_successfully'));

    } catch (\Illuminate\Validation\ValidationException $e) {
        // 6. ULM: Log validation errors
        $this->logger->warning('Personal data update validation failed', [
            'user_id' => Auth::id(),
            'validation_errors' => $e->errors(),
            'log_category' => 'PERSONAL_DATA_UPDATE_VALIDATION'
        ]);

        return redirect()->back()
            ->withErrors($e->errors())
            ->withInput();

    } catch (\Exception $e) {
        // 7. UEM: Handle unexpected errors with full context
        return $this->errorManager->handle('PERSONAL_DATA_UPDATE_FAILED', [
            'user_id' => Auth::id(),
            'error_message' => $e->getMessage(),
            'fields_attempted' => array_keys($request->all()),
            'ip_address' => $request->ip(),
            'trace' => $e->getTraceAsString()
        ], $e);
    }
}
```

### **Method Pattern - Gestione Consensi**
```php
/**
 * Update user consent preferences with versioning and audit trail
 *
 * @param Request $request HTTP request with consent preferences
 * @return RedirectResponse Redirect response with success/error message
 * @throws \Exception When consent update fails
 * @privacy-safe Updates only authenticated user's consent preferences
 */
public function updateConsents(Request $request): RedirectResponse
{
    try {
        $validated = $request->validate([
            'consents' => 'required|array',
            'consents.functional' => 'boolean',
            'consents.analytics' => 'boolean',
            'consents.marketing' => 'boolean',
            'consents.profiling' => 'boolean',
        ]);

        $user = Auth::user();

        // 1. ULM: Log consent update initiation
        $this->logger->info('User consent update initiated', [
            'user_id' => $user->id,
            'consent_changes' => $validated['consents'],
            'ip_address' => $request->ip(),
            'log_category' => 'CONSENT_UPDATE_START'
        ]);

        // 2. ConsentService: Update consents with versioning
        $result = $this->consentService->updateUserConsents($user, $validated['consents']);

        // 3. GDPR: Log consent action - GDPR_ACTIONS category
        $this->auditService->logUserAction($user, 'consents_updated', [
            'previous_consents' => $result['previous'],
            'new_consents' => $result['current'],
            'changed_consents' => array_diff_assoc($result['current'], $result['previous']),
            'legal_basis' => 'user_request'
        ], GdprActivityCategory::GDPR_ACTIONS);

        // 4. GDPR: Specific GDPR audit log
        $this->auditService->logGdprAction($user, 'consent_updated', [
            'consent_changes' => $result,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ], 'user_request');

        return redirect()->route('gdpr.consent')
            ->with('success', __('gdpr.consents_updated_successfully'));

    } catch (\Exception $e) {
        // 5. UEM: Handle consent update errors
        return $this->errorManager->handle('CONSENT_UPDATE_FAILED', [
            'user_id' => Auth::id(),
            'error_message' => $e->getMessage(),
            'consents_attempted' => $validated['consents'] ?? [],
            'ip_address' => $request->ip()
        ], $e);
    }
}
```

---

## **2. SERVICE PATTERN - ConsentService**

### **Firma Completa ConsentService**
```php
<?php

namespace App\Services\Gdpr;

use App\Enums\Gdpr\GdprActivityCategory;
use App\Models\ConsentHistory;
use App\Models\User;
use App\Models\UserConsent;
use Illuminate\Support\Facades\DB;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Carbon\Carbon;

/**
 * @Oracode Service: Consent Management System
 * 🎯 Purpose: Manages user consents with versioning and complete audit trail
 * 🛡️ Privacy: Handles GDPR consent requirements with full legal compliance
 * 🧱 Core Logic: Tracks consent changes, versions, legal basis, and user preferences
 *
 * @package App\Services\Gdpr
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Consent Management Service)
 * @date 2025-09-21
 * @purpose Manage user consents with GDPR compliance and audit trail
 */
class ConsentService
{
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;

    /**
     * Constructor with dependency injection
     *
     * @param UltraLogManager $logger Ultra logging manager for consent operations
     * @param ErrorManagerInterface $errorManager Ultra error manager for error handling
     * @privacy-safe All injected dependencies handle GDPR compliance
     */
    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
    }
}
```

### **Method Pattern - Update Consents**
```php
/**
 * Update user consents with versioning and audit trail
 *
 * @param User $user User whose consents are being updated
 * @param array $consents Array of consent preferences (type => boolean)
 * @return array Previous and current consent states with change count
 * @throws \Exception When consent update fails or database error occurs
 * @privacy-safe Handles only the specified user's consent data
 */
public function updateUserConsents(User $user, array $consents): array
{
    try {
        // 1. ULM: Log service operation start
        $this->logger->info('ConsentService: Processing consent update', [
            'user_id' => $user->id,
            'consent_types' => array_keys($consents),
            'consent_values' => $consents,
            'log_category' => 'CONSENT_SERVICE_UPDATE_START'
        ]);

        // 2. Get current consents for comparison
        $previousConsents = $this->getCurrentConsents($user);

        // 3. Start database transaction for data integrity
        return DB::transaction(function () use ($user, $consents, $previousConsents) {
            $currentTime = now();
            
            // Update each consent type
            foreach ($consents as $type => $granted) {
                UserConsent::updateOrCreate(
                    [
                        'user_id' => $user->id, 
                        'consent_type' => $type
                    ],
                    [
                        'granted' => $granted,
                        'granted_at' => $granted ? $currentTime : null,
                        'withdrawn_at' => !$granted ? $currentTime : null,
                        'version' => $this->getLatestConsentVersion($type),
                        'ip_address' => request()->ip(),
                        'user_agent' => request()->userAgent()
                    ]
                );
            }

            // Create consent history record
            ConsentHistory::create([
                'user_id' => $user->id,
                'previous_consents' => json_encode($previousConsents),
                'new_consents' => json_encode($consents),
                'changed_at' => $currentTime,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'legal_basis' => 'user_request'
            ]);

            // 4. ULM: Log successful consent update
            $changedConsents = array_diff_assoc($consents, $previousConsents);
            $this->logger->info('ConsentService: Consent update completed', [
                'user_id' => $user->id,
                'changed_consents' => $changedConsents,
                'total_changes' => count($changedConsents),
                'log_category' => 'CONSENT_SERVICE_UPDATE_SUCCESS'
            ]);

            return [
                'previous' => $previousConsents,
                'current' => $consents,
                'changed' => $changedConsents,
                'changed_count' => count($changedConsents)
            ];
        });

    } catch (\Exception $e) {
        // 5. ULM: Log service-level error
        $this->logger->error('ConsentService: Consent update failed', [
            'user_id' => $user->id,
            'error_message' => $e->getMessage(),
            'error_trace' => $e->getTraceAsString(),
            'consents_attempted' => $consents,
            'log_category' => 'CONSENT_SERVICE_ERROR'
        ]);

        // 6. Re-throw for controller UEM handling
        throw new \Exception("Consent update failed: " . $e->getMessage(), 0, $e);
    }
}

/**
 * Get current user consents
 *
 * @param User $user User to get consents for
 * @return array Current consent preferences
 * @privacy-safe Returns only the specified user's consent data
 */
protected function getCurrentConsents(User $user): array
{
    $consents = UserConsent::where('user_id', $user->id)
        ->pluck('granted', 'consent_type')
        ->toArray();

    // Default values for missing consent types
    return array_merge([
        'functional' => false,
        'analytics' => false,
        'marketing' => false,
        'profiling' => false
    ], $consents);
}
```

---

## **3. SERVICE PATTERN - AuditLogService**

### **Firma Completa AuditLogService**
```php
<?php

namespace App\Services\Gdpr;

use App\Enums\Gdpr\GdprActivityCategory;
use App\Models\User;
use App\Models\UserActivity;
use App\Models\GdprAuditLog;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Carbon\Carbon;

/**
 * @Oracode Service: GDPR Audit Trail Management
 * 🎯 Purpose: Comprehensive audit logging for GDPR compliance and user activity tracking
 * 🛡️ Privacy: Tracks all user actions with privacy-safe storage and data retention
 * 🧱 Core Logic: Records, categorizes, and manages audit trails for legal compliance
 *
 * @package App\Services\Gdpr
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - GDPR Audit Service)
 * @date 2025-09-21
 * @purpose Manage GDPR audit trails and user activity logging
 */
class AuditLogService
{
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;
    protected int $retentionDays = 2555; // 7 years for legal compliance

    /**
     * Constructor with dependency injection
     *
     * @param UltraLogManager $logger Ultra logging manager for audit operations
     * @param ErrorManagerInterface $errorManager Ultra error manager for error handling
     * @privacy-safe All injected dependencies handle GDPR compliance
     */
    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
    }
}
```

### **Method Pattern - Log User Action**
```php
/**
 * Log user action with full context and GDPR compliance
 *
 * @param User $user User performing the action
 * @param string $action Action being performed (e.g., 'personal_data_updated')
 * @param array $context Additional context data for the action
 * @param GdprActivityCategory $category GDPR activity category for classification
 * @return UserActivity Created user activity record
 * @throws \Exception When audit logging fails
 * @privacy-safe Logs action with privacy-safe context and IP masking
 */
public function logUserAction(
    User $user, 
    string $action, 
    array $context, 
    GdprActivityCategory $category
): UserActivity {
    try {
        // 1. ULM: Log audit service operation
        $this->logger->info('AuditLogService: Logging user action', [
            'user_id' => $user->id,
            'action' => $action,
            'category' => $category->value,
            'context_keys' => array_keys($context),
            'log_category' => 'AUDIT_SERVICE_USER_ACTION'
        ]);

        // 2. Get request metadata safely
        $requestMetadata = $this->getRequestMetadata();
        
        // 3. Sanitize context for privacy compliance
        $sanitizedContext = $this->sanitizeContext($context);

        // 4. Create user activity record
        $userActivity = UserActivity::create([
            'user_id' => $user->id,
            'action' => $action,
            'category' => $category->value,
            'description' => $this->generateActionDescription($action, $category),
            'context_data' => $sanitizedContext,
            'ip_address' => $this->maskIpAddress($requestMetadata['ip_address']),
            'user_agent' => $requestMetadata['user_agent'],
            'session_id' => $requestMetadata['session_id'],
            'expires_at' => now()->addDays($this->retentionDays)
        ]);

        // 5. ULM: Log successful audit creation
        $this->logger->info('AuditLogService: User action logged successfully', [
            'user_id' => $user->id,
            'action' => $action,
            'activity_id' => $userActivity->id,
            'category' => $category->value,
            'log_category' => 'AUDIT_SERVICE_SUCCESS'
        ]);

        return $userActivity;

    } catch (\Exception $e) {
        // 6. ULM: Log audit service error
        $this->logger->error('AuditLogService: Failed to log user action', [
            'user_id' => $user->id,
            'action' => $action,
            'category' => $category->value,
            'error_message' => $e->getMessage(),
            'log_category' => 'AUDIT_SERVICE_ERROR'
        ]);

        // 7. Re-throw for higher-level handling
        throw new \Exception("Audit logging failed: " . $e->getMessage(), 0, $e);
    }
}
```

### **Method Pattern - Log GDPR Action**
```php
/**
 * Log GDPR-specific action with legal compliance tracking
 *
 * @param User $user User performing the GDPR action
 * @param string $gdprAction GDPR action type (e.g., 'consent_updated', 'data_exported')
 * @param array $details Detailed context for the GDPR action
 * @param string $legalBasis Legal basis for the action (default: 'user_request')
 * @return GdprAuditLog Created GDPR audit log record
 * @throws \Exception When GDPR audit logging fails
 * @privacy-safe Logs GDPR action with full compliance tracking
 */
public function logGdprAction(
    User $user,
    string $gdprAction,
    array $details = [],
    string $legalBasis = 'user_request'
): GdprAuditLog {
    try {
        // 1. ULM: Log GDPR audit operation
        $this->logger->info('AuditLogService: Logging GDPR action', [
            'user_id' => $user->id,
            'gdpr_action' => $gdprAction,
            'legal_basis' => $legalBasis,
            'details_keys' => array_keys($details),
            'log_category' => 'AUDIT_SERVICE_GDPR'
        ]);

        // 2. Get request metadata
        $requestMetadata = $this->getRequestMetadata();
        
        // 3. Sanitize details for GDPR compliance
        $sanitizedDetails = $this->sanitizeContext($details);

        // 4. Create GDPR audit log record
        $gdprLog = GdprAuditLog::create([
            'user_id' => $user->id,
            'action_type' => $gdprAction,
            'category' => 'gdpr_actions',
            'description' => "GDPR action: {$gdprAction}",
            'legal_basis' => $legalBasis,
            'context_data' => $sanitizedDetails,
            'ip_address' => $this->maskIpAddress($requestMetadata['ip_address']),
            'user_agent' => $requestMetadata['user_agent'],
            'session_id' => $requestMetadata['session_id'],
            'expires_at' => now()->addDays($this->retentionDays)
        ]);

        // 5. Also create corresponding user activity record
        $this->logUserAction($user, "gdpr_{$gdprAction}", $details, GdprActivityCategory::GDPR_ACTIONS);

        // 6. ULM: Log successful GDPR audit
        $this->logger->info('AuditLogService: GDPR action logged successfully', [
            'user_id' => $user->id,
            'gdpr_action' => $gdprAction,
            'gdpr_log_id' => $gdprLog->id,
            'legal_basis' => $legalBasis,
            'log_category' => 'AUDIT_SERVICE_GDPR_SUCCESS'
        ]);

        return $gdprLog;

    } catch (\Exception $e) {
        // 7. ULM: Log GDPR audit error
        $this->logger->error('AuditLogService: Failed to log GDPR action', [
            'user_id' => $user->id,
            'gdpr_action' => $gdprAction,
            'error_message' => $e->getMessage(),
            'log_category' => 'AUDIT_SERVICE_GDPR_ERROR'
        ]);

        // 8. Re-throw for higher-level handling
        throw new \Exception("GDPR audit logging failed: " . $e->getMessage(), 0, $e);
    }
}
```

---

## **4. GDPR ACTIVITY CATEGORIES - Esempi Pratici**

### **Utilizzo delle Enum Categories**
```php
// Import obbligatorio
use App\Enums\Gdpr\GdprActivityCategory;

// Esempi di utilizzo per diverse operazioni:

// 1. Aggiornamento dati personali
$this->auditService->logUserAction($user, 'personal_data_updated', [
    'fields_updated' => ['first_name', 'last_name', 'email'],
    'previous_email' => 'old@example.com',
    'new_email' => 'new@example.com'
], GdprActivityCategory::PERSONAL_DATA_UPDATE);

// 2. Azioni GDPR specifiche (consensi, export, cancellazione)
$this->auditService->logUserAction($user, 'consents_updated', [
    'consent_changes' => ['marketing' => true, 'analytics' => false]
], GdprActivityCategory::GDPR_ACTIONS);

// 3. Creazione di contenuti (biografie, post)
$this->auditService->logUserAction($user, 'biography_created', [
    'biography_id' => $biography->id,
    'character_name' => $biography->character_name
], GdprActivityCategory::CONTENT_CREATION);

// 4. Modifica contenuti
$this->auditService->logUserAction($user, 'biography_updated', [
    'biography_id' => $biography->id,
    'fields_changed' => ['biography_text', 'character_image']
], GdprActivityCategory::CONTENT_MODIFICATION);

// 5. Login/Logout
$this->auditService->logUserAction($user, 'user_logged_in', [
    'login_method' => 'email_password',
    'remember_me' => true
], GdprActivityCategory::AUTHENTICATION_LOGIN);

// 6. Gestione wallet e operazioni finanziarie
$this->auditService->logUserAction($user, 'wallet_connected', [
    'wallet_address' => $maskedAddress,
    'wallet_type' => 'metamask'
], GdprActivityCategory::WALLET_MANAGEMENT);

// 7. Gestione media e file
$this->auditService->logUserAction($user, 'profile_image_uploaded', [
    'file_type' => 'image/jpeg',
    'file_size' => $fileSize
], GdprActivityCategory::MEDIA_MANAGEMENT);

// 8. Eventi di sicurezza
$this->auditService->logUserAction($user, 'password_changed', [
    'password_strength' => 'strong',
    'force_logout_other_sessions' => true
], GdprActivityCategory::SECURITY_EVENTS);
```

---

## **5. HELPER PATTERN**

### **Firma Completa Helper**
```php
<?php

namespace App\Helpers;

use App\Enums\Gdpr\GdprActivityCategory;
use App\Models\User;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * @Oracode Helper: GDPR Data Processing Utilities
 * 🎯 Purpose: Utility functions for GDPR-compliant data processing and validation
 * 🛡️ Privacy: Handles data sanitization, masking, and privacy protection
 * 🧱 Core Logic: Validates, sanitizes, and processes user data safely
 *
 * @package App\Helpers
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - GDPR Data Helper)
 * @date 2025-09-21
 * @purpose Provide GDPR-compliant data processing utilities
 */
class GdprDataHelper
{
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;

    /**
     * Constructor with dependency injection
     *
     * @param UltraLogManager $logger Ultra logging manager for helper operations
     * @param ErrorManagerInterface $errorManager Ultra error manager for error handling
     * @privacy-safe All injected dependencies handle GDPR compliance
     */
    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
    }

    /**
     * Sanitize user data for export with privacy protection
     *
     * @param array $userData Raw user data to sanitize
     * @param array $sensitiveFields Fields to mask or remove completely
     * @return array Sanitized data safe for export and GDPR compliance
     * @throws \Exception When sanitization fails
     * @privacy-safe Masks sensitive fields and removes PII where required
     */
    public function sanitizeUserDataForExport(array $userData, array $sensitiveFields = []): array
    {
        try {
            // 1. ULM: Log sanitization operation
            $this->logger->info('GdprDataHelper: Sanitizing user data for export', [
                'data_fields_count' => count($userData),
                'sensitive_fields_count' => count($sensitiveFields),
                'log_category' => 'GDPR_DATA_SANITIZATION'
            ]);

            $sanitized = $userData;
            $defaultSensitiveFields = ['password', 'token', 'secret', 'private_key'];
            $allSensitiveFields = array_merge($defaultSensitiveFields, $sensitiveFields);

            // 2. Process each field for privacy compliance
            foreach ($allSensitiveFields as $field) {
                if (isset($sanitized[$field])) {
                    if (in_array($field, ['password', 'token', 'secret', 'private_key'])) {
                        unset($sanitized[$field]); // Remove completely
                    } else {
                        $sanitized[$field] = $this->maskSensitiveValue($sanitized[$field]);
                    }
                }
            }

            // 3. Apply additional privacy filters
            $sanitized = $this->applyPrivacyFilters($sanitized);

            return $sanitized;

        } catch (\Exception $e) {
            // 4. ULM: Log helper error but return safe fallback
            $this->logger->error('GdprDataHelper: Data sanitization failed', [
                'error_message' => $e->getMessage(),
                'data_fields' => array_keys($userData),
                'log_category' => 'GDPR_HELPER_ERROR'
            ]);

            // 5. Return safe fallback instead of throwing
            return [
                'error' => 'Data sanitization failed - safe mode activated',
                'timestamp' => now()->toISOString(),
                'user_id' => $userData['id'] ?? 'unknown'
            ];
        }
    }
}
```

---

## **6. PATTERNS SUMMARY**

### **Controller Responsibilities:**
- **ULM**: Log user operations, validations, errors
- **UEM**: Handle and respond to all errors with context
- **GDPR**: Log user actions with appropriate `GdprActivityCategory`
- **Services**: Coordinate business logic through injected services

### **Service Responsibilities:**
- **ULM**: Log business operations, data changes, service errors
- **UEM**: Log errors and re-throw for controller handling
- **GDPR**: Process and validate data changes with audit trail
- **Data**: Manage data integrity and versioning

### **Helper Responsibilities:**
- **ULM**: Log utility operations and validation results
- **UEM**: Safe fallback handling (no error propagation)
- **GDPR**: Data sanitization and privacy protection
- **Utilities**: Provide reusable data processing functions

### **GdprActivityCategory Usage:**
- `PERSONAL_DATA_UPDATE` - Modifiche dati personali
- `GDPR_ACTIONS` - Azioni GDPR (consensi, export, cancellazione)
- `CONTENT_CREATION` - Creazione contenuti (biografie, post)
- `CONTENT_MODIFICATION` - Modifica contenuti esistenti
- `AUTHENTICATION_LOGIN` - Login utente
- `WALLET_MANAGEMENT` - Operazioni wallet e finanziarie
- `MEDIA_MANAGEMENT` - Upload e gestione file/media
- `SECURITY_EVENTS` - Eventi di sicurezza