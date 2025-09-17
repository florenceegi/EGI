# FlorenceEGI GDPR System - Technical Documentation

## Abstract

The FlorenceEGI GDPR System provides a comprehensive framework for compliance with the General Data Protection Regulation within the FlorenceEGI platform. Designed according to Oracode 3.0 principles, it delivers a complete set of data protection capabilities including consent management, data export, processing restrictions, account deletion, breach reporting, and activity logging. The system integrates deeply with the Ultra ecosystem (particularly UEM for error handling) and emphasizes accessibility, internationalization, security, and transparent data handling. This technical documentation serves as the definitive guide for developers working with the GDPR module, covering its architecture, components, workflows, and integration points.

## Table of Contents

1. [System Overview](#system-overview)
2. [Architecture & Flow](#architecture--flow)
3. [File Structure](#file-structure)
4. [Key Components](#key-components)
5. [Data Models & Database](#data-models--database)
6. [Frontend Components](#frontend-components)
7. [Security Mechanisms](#security-mechanisms)
8. [Integration Points](#integration-points)
9. [Configuration Options](#configuration-options)
10. [Testing Guidelines](#testing-guidelines)
11. [Extending the System](#extending-the-system)

## System Overview

The GDPR System provides FlorenceEGI users with comprehensive tools to exercise their data protection rights, while giving the platform the necessary infrastructure to maintain compliance with GDPR regulations. The system follows Oracode 3.0 principles, ensuring code that is not only functional but also communicative, transparent, and aligned with FlorenceEGI's ethical values.

### Core Capabilities

- **Consent Management**: Granular user consent for different processing activities
- **Data Export**: Complete data portability with multiple format options
- **Processing Restrictions**: User-initiated limitations on data processing
- **Account Deletion**: Managed account deletion with proper data handling
- **Breach Reporting**: Channel for users to report potential data breaches
- **Activity Logging**: Comprehensive audit trail of all GDPR-related activities

### Key Technologies

- Laravel 10.x framework
- Ultra ecosystem integration (UEM, ULM)
- AlpineJS for frontend interactivity
- Background job processing with Laravel queues
- Laravel's encryption and authentication systems

## Architecture & Flow

The GDPR System follows a standard Laravel architecture with some specific adaptations:

### Core Architecture Pattern

1. **Route → Controller → Service → Model**
   - Routes are grouped under `gdpr.php` route file
   - Controllers handle request validation and response coordination
   - Services contain business logic for each GDPR domain
   - Models represent the database entities with Eloquent relationships

### DI-First Approach

The system uses dependency injection throughout, particularly for services and error handling:

```php
public function __construct(
    protected ErrorManagerInterface $errorManager,
    protected ConsentService $consentService
) {
    $this->middleware('auth');
}
```

### Typical Request Flow

1. User initiates a GDPR action (e.g., requests data export)
2. Request passes through relevant middleware (auth, rate limiting)
3. Controller validates the request (using Form Request classes)
4. Controller delegates to the appropriate service
5. Service performs the business logic
6. For long-running operations, background jobs are dispatched
7. Activity is logged for audit trail
8. User receives appropriate response/notification

### Error Handling Flow

1. Errors are captured using UEM's `handle()` method
2. Error codes are defined in `config/error-manager.php`
3. Appropriate response is generated based on error type
4. Frontend errors are handled by UEM client-side integration

## File Structure

The GDPR system spans multiple directories in the Laravel application structure:

### Routes

- `routes/gdpr.php` - All GDPR-related routes

### Controllers

- `app/Http/Controllers/GdprController.php` - Main controller for GDPR actions
- `app/Http/Controllers/ConsentController.php` - Handles consent-specific actions
- `app/Http/Controllers/DataExportController.php` - Manages data export requests
- `app/Http/Controllers/BreachReportController.php` - Processes breach reports

### Services

- `app/Services/Gdpr/ActivityLogService.php` - Handles GDPR activity logging
- `app/Services/Gdpr/ConsentService.php` - Manages user consents
- `app/Services/Gdpr/DataExportService.php` - Processes data exports
- `app/Services/Gdpr/EncryptionService.php` - Provides GDPR-specific encryption
- `app/Services/Gdpr/ProcessingRestrictionService.php` - Manages processing restrictions

### Models

- `app/Models/UserConsent.php` - Stores user consent records
- `app/Models/DataExport.php` - Represents data export requests
- `app/Models/ProcessingRestriction.php` - Represents processing restriction requests
- `app/Models/GdprActivityLog.php` - Stores GDPR activity audit trail
- `app/Models/BreachReport.php` - Stores data breach reports
- `app/Models/AccountDeletionRequest.php` - Manages account deletion requests

### Enums

- `app/Enums/ConsentStatus.php` - Status values for consents
- `app/Enums/DataExportFormat.php` - Available export formats
- `app/Enums/DataExportStatus.php` - Status values for exports
- `app/Enums/ProcessingRestrictionType.php` - Types of processing restrictions
- `app/Enums/ProcessingRestrictionReason.php` - Reasons for restrictions

### Form Requests

- `app/Http/Requests/GdprConsentRequest.php` - Validates consent updates
- `app/Http/Requests/DataExportRequest.php` - Validates data export requests
- `app/Http/Requests/ProcessingRestrictionRequest.php` - Validates restriction requests
- `app/Http/Requests/BreachReportRequest.php` - Validates breach reports

### Middleware

- `app/Http/Middleware/GdprAuthMiddleware.php` - Enhanced authentication for GDPR
- `app/Http/Middleware/ProcessingRestrictionMiddleware.php` - Enforces restrictions
- `app/Http/Middleware/ConsentCheckMiddleware.php` - Checks for required consents

### Jobs

- `app/Jobs/DataExportQueueJob.php` - Background job for data exports
- `app/Jobs/AccountDeletionJob.php` - Processes account deletion
- `app/Jobs/GdprScheduledCleanupJob.php` - Cleanup of old data

### Notifications

- `app/Notifications/Gdpr/BaseGdprNotification.php` - Base class for notifications
- `app/Notifications/Gdpr/DataExportedNotification.php` - Export completion notification
- `app/Notifications/Gdpr/AccountDeletionRequestedNotification.php` - Deletion request notification

### Views

- `resources/views/gdpr/layouts/gdpr.blade.php` - Base layout for GDPR pages
- `resources/views/gdpr/consent.blade.php` - Consent management view
- `resources/views/gdpr/export-data.blade.php` - Data export view
- `resources/views/gdpr/limit-processing.blade.php` - Processing restriction view
- `resources/views/gdpr/delete-account.blade.php` - Account deletion view
- `resources/views/gdpr/breach-report.blade.php` - Breach reporting view
- `resources/views/gdpr/activity-log.blade.php` - Activity log view

### Components

- `app/View/Components/Gdpr/ConsentToggle.php` - Consent toggle component
- `app/View/Components/Gdpr/StatusBadge.php` - Status badge component
- `app/View/Components/Gdpr/DataCategorySelector.php` - Data category selector
- `app/View/Components/Gdpr/PrivacyNotice.php` - Privacy notice component
- `resources/views/components/gdpr/*.blade.php` - Component templates

### JavaScript/TypeScript

- `resources/js/gdpr/uem-integration.ts` - UEM client-side integration
- `resources/js/gdpr/form-validation.ts` - Form validation helpers
- `resources/js/gdpr/consent-manager.ts` - Real-time consent updates
- `resources/js/gdpr/export-progress.ts` - Export progress tracking

### Policies

- `app/Policies/GdprPolicy.php` - Authorization rules for GDPR actions

### Tests

- `tests/Feature/Gdpr/GdprTestCase.php` - Base test case for GDPR
- `tests/Feature/Gdpr/ConsentControllerTest.php` - Tests for consent features
- `tests/Feature/Gdpr/DataExportControllerTest.php` - Tests for export features
- `tests/Feature/Gdpr/ProcessingRestrictionTest.php` - Tests for restrictions

### Config

- `config/gdpr.php` - GDPR-specific configuration options

### Translations

- `resources/lang/en/gdpr.php` - English translations
- `resources/lang/it/gdpr.php` - Italian translations

## Key Components

### 1. GdprController

The `GdprController` is the central controller for GDPR-related actions. It follows DI-first principles and provides methods for each major GDPR function.

```php
class GdprController extends Controller
{
    public function __construct(
        protected ErrorManagerInterface $errorManager,
        protected ProcessingRestrictionService $processingRestrictionService
    ) {
        $this->middleware('auth');
    }
    
    // Methods for each GDPR function...
}
```

Key methods include:
- `limitProcessing()` - Show processing restriction form
- `limitProcessingStore()` - Store a new processing restriction
- `removeProcessingRestriction()` - Remove an existing restriction

### 2. ConsentService

The `ConsentService` manages user consent records with versioning support.

```php
class ConsentService
{
    public function getUserConsents(User $user): array { /* ... */ }
    public function updateConsents(User $user, array $consents): bool { /* ... */ }
    public function hasConsent(User $user, string $consentKey): bool { /* ... */ }
    public function getConsentHistory(User $user, ?string $consentKey = null) { /* ... */ }
}
```

### 3. DataExportService

The `DataExportService` handles the generation and processing of data exports.

```php
class DataExportService
{
    public function createExportRequest(User $user, array $data): DataExport { /* ... */ }
    public function processExport(DataExport $export, callable $progressCallback = null): void { /* ... */ }
    public function getUserExports(User $user, int $limit = 10) { /* ... */ }
}
```

### 4. ProcessingRestrictionService

The `ProcessingRestrictionService` manages processing restriction requests.

```php
class ProcessingRestrictionService
{
    public function createRestriction(
        User $user, 
        ProcessingRestrictionType $type, 
        ProcessingRestrictionReason $reason, 
        ?string $notes = null, 
        array $dataCategories = []
    ): ProcessingRestriction { /* ... */ }
    
    public function hasActiveRestriction(
        User $user, 
        string $processingType, 
        ?string $dataCategory = null
    ): bool { /* ... */ }
}
```

### 5. ActivityLogService

The `ActivityLogService` provides a consistent interface for logging GDPR activities.

```php
class ActivityLogService
{
    public function log(string $activity, array $details = [], ?int $userId = null): ?GdprActivityLog { /* ... */ }
    public function getUserLogs(?int $userId = null, ?string $activityType = null, int $limit = 50) { /* ... */ }
    
    // Specialized logging methods
    public function logConsentUpdated(array $consentData, ?int $userId = null): ?GdprActivityLog { /* ... */ }
    public function logDataExportRequested(int $exportId, array $exportData, ?int $userId = null): ?GdprActivityLog { /* ... */ }
    // Additional logging methods...
}
```

### 6. EncryptionService

The `EncryptionService` provides enhanced encryption for sensitive GDPR data.

```php
class EncryptionService
{
    public function encrypt($data, ?string $password = null): string { /* ... */ }
    public function decrypt(string $encryptedData, ?string $password = null) { /* ... */ }
    public function encryptFile(string $filePath, ?string $password = null): string { /* ... */ }
    public function decryptFile(string $encryptedFilePath, ?string $password = null): string { /* ... */ }
}
```

### 7. ConsentToggle Component

The `ConsentToggle` component provides a reusable UI element for managing consents.

```php
class ConsentToggle extends Component
{
    public function __construct(
        public string $consentKey,
        public string $label,
        public string $description = '',
        public bool $isRequired = false,
        public bool $isEnabled = false,
        public string $formId = 'consent-form',
        public bool $isDisabled = false
    ) {}
}
```

## Data Models & Database

### Database Schema

The GDPR system uses several tables to store its data:

1. **user_consents**
   - `id` (bigint)
   - `user_id` (bigint)
   - `consent_key` (string)
   - `consented` (boolean)
   - `ip_address` (string, nullable)
   - `user_agent` (string, nullable)
   - `expires_at` (timestamp, nullable)
   - `created_at`, `updated_at` (timestamps)

2. **data_exports**
   - `id` (bigint)
   - `user_id` (bigint)
   - `format` (string)
   - `status` (string)
   - `data_categories` (json)
   - `include_metadata` (boolean)
   - `include_timestamps` (boolean)
   - `is_password_protected` (boolean)
   - `file_path` (string, nullable)
   - `file_size` (integer, nullable)
   - `started_at`, `completed_at`, `expires_at` (timestamps, nullable)
   - `progress` (json, nullable)
   - `error` (text, nullable)
   - `created_at`, `updated_at` (timestamps)

3. **processing_restrictions**
   - `id` (bigint)
   - `user_id` (bigint)
   - `restriction_type` (string)
   - `restriction_reason` (string)
   - `status` (string)
   - `data_categories` (json, nullable)
   - `notes` (text, nullable)
   - `expires_at` (timestamp, nullable)
   - `created_at`, `updated_at` (timestamps)

4. **gdpr_activity_logs**
   - `id` (bigint)
   - `user_id` (bigint, nullable)
   - `activity` (string)
   - `details` (json, nullable)
   - `ip_address` (string, nullable)
   - `user_agent` (string, nullable)
   - `created_at`, `updated_at` (timestamps)

5. **breach_reports**
   - `id` (bigint)
   - `user_id` (bigint, nullable)
   - `reporter_name` (string)
   - `reporter_email` (string)
   - `incident_date` (date)
   - `breach_description` (text)
   - `affected_data` (text)
   - `discovery_method` (string)
   - `supporting_evidence` (string, nullable)
   - `status` (string)
   - `consent_to_contact` (boolean)
   - `created_at`, `updated_at` (timestamps)

6. **account_deletion_requests**
   - `id` (bigint)
   - `user_id` (bigint)
   - `reason` (string, nullable)
   - `additional_comments` (text, nullable)
   - `status` (string)
   - `scheduled_deletion_date` (timestamp)
   - `created_at`, `updated_at` (timestamps)

### Model Relationships

Key relationships between models include:

```php
// User model relationships
public function consents() {
    return $this->hasMany(UserConsent::class);
}

public function dataExports() {
    return $this->hasMany(DataExport::class);
}

public function processingRestrictions() {
    return $this->hasMany(ProcessingRestriction::class);
}

public function gdprActivities() {
    return $this->hasMany(GdprActivityLog::class);
}

public function breachReports() {
    return $this->hasMany(BreachReport::class);
}

public function deletionRequest() {
    return $this->hasOne(AccountDeletionRequest::class);
}
```

### Enums

The system uses PHP 8.1 native enums for type safety:

```php
enum DataExportStatus: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';
    case FAILED = 'failed';
    case EXPIRED = 'expired';
}
```

## Frontend Components

### Layout Structure

The GDPR module uses a dedicated layout with glassmorphism design:

```blade
{{-- resources/views/gdpr/layouts/gdpr.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <title>{{ config('app.name') }} - GDPR Data Control Center</title>
    <!-- Styles, Meta tags, etc. -->
</head>
<body>
    <div class="gdpr-container">
        <div class="gdpr-sidebar">
            <!-- GDPR navigation menu -->
        </div>
        <div class="gdpr-content">
            <div class="gdpr-header">
                <h1>@yield('title')</h1>
            </div>
            <div class="gdpr-main">
                @yield('content')
            </div>
        </div>
    </div>
</body>
</html>
```

### Reusable Components

The system includes several Blade components for consistent UI:

1. **ConsentToggle**: Toggle switch for consent management
2. **StatusBadge**: Visual indicator for status values
3. **DataCategorySelector**: Multi-select for data categories
4. **PrivacyNotice**: Collapsible privacy information panel

Example usage:

```blade
<x-gdpr.consent-toggle
    consent-key="marketing"
    label="{{ __('gdpr.consent.marketing.label') }}"
    description="{{ __('gdpr.consent.marketing.description') }}"
    :is-enabled="$userConsents['marketing'] ?? false"
/>
```

### JavaScript Integration

The frontend functionality is enhanced with TypeScript:

1. **UEM Integration**: Configures UEM for GDPR error handling
2. **Form Validation**: Client-side validation for forms
3. **Consent Manager**: Real-time consent updates via AJAX
4. **Export Progress**: Tracking and display of export progress

Example:

```typescript
// resources/js/gdpr/consent-manager.ts
export class ConsentManager {
    public static initialize(): void {
        this.setupConsentToggleListeners();
        this.setupRealTimeUpdates();
    }
    
    private static setupConsentToggleListeners(): void {
        document.addEventListener('consent-changed', (event: any) => {
            const detail = event.detail;
            if (detail && detail.key) {
                this.updateConsent(detail.key, detail.value);
            }
        });
    }
    
    // Additional methods...
}
```

## Security Mechanisms

### Authentication & Authorization

The GDPR system implements multiple security layers:

1. **Basic Authentication**: All routes require authentication via middleware
2. **Enhanced Security**: Sensitive operations require recent authentication
3. **Critical Security**: High-risk operations require password confirmation

```php
// app/Http/Middleware/GdprAuthMiddleware.php
public function handle(Request $request, Closure $next, $guardType = 'standard')
{
    // Basic auth check
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    // Security level checks based on guardType
    // ...
}
```

### Authorization Policies

The `GdprPolicy` class controls access to GDPR functionality:

```php
// app/Policies/GdprPolicy.php
class GdprPolicy
{
    public function exportData(User $user): bool {
        // Check export limits
        $todayExports = $user->dataExports()
            ->whereDate('created_at', today())
            ->count();
            
        return $todayExports < config('gdpr.export.max_exports_per_day', 3);
    }
    
    // Additional policy methods...
}
```

### Rate Limiting

The system implements rate limiting to prevent abuse:

```php
// app/Providers/RouteServiceProvider.php
RateLimiter::for('gdpr.sensitive', function (Request $request) {
    return Limit::perMinute(5)
        ->by($request->user()?->id ?: $request->ip());
});
```

### Encryption

Sensitive data is encrypted using the `EncryptionService`:

```php
// Encrypting with optional password
$encrypted = $encryptionService->encrypt($data, $password);

// Decrypting
$decrypted = $encryptionService->decrypt($encryptedData, $password);
```

## Integration Points

### Ultra Ecosystem Integration

The GDPR system integrates with the Ultra ecosystem:

1. **UEM (UltraErrorManager)**: For consistent error handling
   ```php
   $this->errorManager->handle('GDPR_PROCESSING_RESTRICTION_CREATE_ERROR', [
       'user_id' => Auth::id(),
       'request_data' => $request->safe()->except(['notes']),
       'error' => $e->getMessage()
   ], $e);
   ```

2. **ULM (UltraLogManager)**: For logging (via UEM)

### Queue System Integration

Long-running processes use Laravel's queue system:

```php
// Dispatching data export job
DataExportQueueJob::dispatch($export);
```

### Notification System Integration

The system sends notifications using Laravel's notification system:

```php
$user->notify(new DataExportedNotification($export));
```

## Configuration Options

The `config/gdpr.php` file provides extensive configuration options:

```php
return [
    'consent' => [
        'defaults' => [
            'marketing' => false,
            'analytics' => false,
            // ...
        ],
        'required_categories' => [
            'essential',
        ],
        // ...
    ],
    
    'export' => [
        'default_format' => 'json',
        'available_formats' => [
            'json' => [
                'extension' => 'json',
                'mime_type' => 'application/json',
            ],
            // ...
        ],
        'max_exports_per_day' => 3,
        // ...
    ],
    
    // Additional configuration sections...
];
```

### Key Configuration Areas

1. **Consent Settings**: Default values, required categories
2. **Export Settings**: Formats, limits, timeouts
3. **Processing Restriction Settings**: Limits, expiry, categories
4. **Account Deletion Settings**: Delay period, soft/hard delete
5. **Breach Report Settings**: File types, notifications
6. **Activity Log Settings**: Retention, IP logging
7. **UI Settings**: Colors, breadcrumbs
8. **Notification Settings**: Email classes, Slack integration
9. **Security Settings**: Rate limiting, encryption

### UEM Error Codes

The GDPR module defines specific error codes in the UEM configuration (`config/error-manager.php`). These codes are used throughout the system for consistent error handling.

```php
// config/error-manager.php
return [
    // ... other error codes
    
    // GDPR Consent errors
    'GDPR_CONSENT_REQUIRED' => [
        'msg' => 'gdpr.errors.consent_required',
        'msg_to' => ['inline', 'toast'],
        'type' => 'warning',
        'http_code' => 403,
        'blocking' => true,
        'recovery' => 'redirect',
        'recovery_url' => '/gdpr/consent',
        'report' => true,
        'log' => true,
        'notify' => false,
    ],
    'GDPR_CONSENT_UPDATE_ERROR' => [
        'msg' => 'gdpr.errors.consent_update_error',
        'msg_to' => ['toast'],
        'type' => 'error',
        'http_code' => 500,
        'blocking' => false,
        'recovery' => 'retry',
        'report' => true,
        'log' => true,
        'notify' => false,
    ],
    'GDPR_CONSENT_SAVE_ERROR' => [
        'msg' => 'gdpr.errors.consent_save_error',
        'msg_to' => ['toast', 'console'],
        'type' => 'error',
        'http_code' => 500,
        'blocking' => true,
        'recovery' => 'retry',
        'report' => true,
        'log' => true,
        'notify' => false,
    ],
    'GDPR_CONSENT_LOAD_ERROR' => [
        'msg' => 'gdpr.errors.consent_load_error',
        'msg_to' => ['toast', 'inline'],
        'type' => 'error',
        'http_code' => 500,
        'blocking' => false,
        'recovery' => 'reload',
        'report' => true,
        'log' => true,
        'notify' => false,
    ],
    
    // GDPR Data Export errors
    'GDPR_EXPORT_REQUEST_ERROR' => [
        'msg' => 'gdpr.errors.export_request_error',
        'msg_to' => ['toast'],
        'type' => 'error',
        'http_code' => 500,
        'blocking' => false,
        'recovery' => 'retry',
        'report' => true,
        'log' => true,
        'notify' => false,
    ],
    'GDPR_EXPORT_LIMIT_REACHED' => [
        'msg' => 'gdpr.export.limit_reached',
        'msg_to' => ['toast', 'inline'],
        'type' => 'warning',
        'http_code' => 429,
        'blocking' => true,
        'recovery' => 'wait',
        'report' => false,
        'log' => true,
        'notify' => false,
    ],
    'GDPR_EXPORT_CREATE_ERROR' => [
        'msg' => 'gdpr.errors.export_create_error',
        'msg_to' => ['toast', 'console'],
        'type' => 'error',
        'http_code' => 500,
        'blocking' => true,
        'recovery' => 'retry',
        'report' => true,
        'log' => true,
        'notify' => true,
    ],
    'GDPR_EXPORT_DOWNLOAD_ERROR' => [
        'msg' => 'gdpr.errors.export_download_error',
        'msg_to' => ['toast', 'inline'],
        'type' => 'error',
        'http_code' => 500,
        'blocking' => false,
        'recovery' => 'retry',
        'report' => true,
        'log' => true,
        'notify' => false,
    ],
    'GDPR_EXPORT_STATUS_ERROR' => [
        'msg' => 'gdpr.errors.export_status_error',
        'msg_to' => ['toast'],
        'type' => 'error',
        'http_code' => 500,
        'blocking' => false,
        'recovery' => 'retry',
        'report' => true,
        'log' => true,
        'notify' => false,
    ],
    'GDPR_EXPORT_PROCESSING_FAILED' => [
        'msg' => 'gdpr.errors.export_processing_failed',
        'msg_to' => ['toast', 'inline'],
        'type' => 'error',
        'http_code' => 500,
        'blocking' => false,
        'recovery' => 'retry',
        'report' => true,
        'log' => true,
        'notify' => true,
    ],
    
    // GDPR Processing Restriction errors
    'GDPR_PROCESSING_RESTRICTED' => [
        'msg' => 'gdpr.errors.processing_restricted',
        'msg_to' => ['toast', 'inline'],
        'type' => 'warning',
        'http_code' => 403,
        'blocking' => false,
        'recovery' => 'none',
        'report' => false,
        'log' => true,
        'notify' => false,
    ],
    'GDPR_PROCESSING_LIMIT_VIEW_ERROR' => [
        'msg' => 'gdpr.errors.processing_limit_view_error',
        'msg_to' => ['toast'],
        'type' => 'error',
        'http_code' => 500,
        'blocking' => false,
        'recovery' => 'reload',
        'report' => true,
        'log' => true,
        'notify' => false,
    ],
    'GDPR_PROCESSING_RESTRICTION_CREATE_ERROR' => [
        'msg' => 'gdpr.errors.processing_restriction_create_error',
        'msg_to' => ['toast'],
        'type' => 'error',
        'http_code' => 500,
        'blocking' => false,
        'recovery' => 'retry',
        'report' => true,
        'log' => true,
        'notify' => false,
    ],
    'GDPR_PROCESSING_RESTRICTION_REMOVE_ERROR' => [
        'msg' => 'gdpr.errors.processing_restriction_remove_error',
        'msg_to' => ['toast'],
        'type' => 'error',
        'http_code' => 500,
        'blocking' => false,
        'recovery' => 'retry',
        'report' => true,
        'log' => true,
        'notify' => false,
    ],
    'GDPR_PROCESSING_RESTRICTION_LIMIT_REACHED' => [
        'msg' => 'gdpr.restriction.limit_reached',
        'msg_to' => ['toast', 'inline'],
        'type' => 'warning',
        'http_code' => 429,
        'blocking' => true,
        'recovery' => 'none',
        'report' => false,
        'log' => true,
        'notify' => false,
    ],
    
    // GDPR Account Deletion errors
    'GDPR_DELETION_REQUEST_ERROR' => [
        'msg' => 'gdpr.errors.deletion_request_error',
        'msg_to' => ['toast', 'inline'],
        'type' => 'error',
        'http_code' => 500,
        'blocking' => true,
        'recovery' => 'contact',
        'report' => true,
        'log' => true,
        'notify' => true,
    ],
    'GDPR_DELETION_CANCELLATION_ERROR' => [
        'msg' => 'gdpr.errors.deletion_cancellation_error',
        'msg_to' => ['toast'],
        'type' => 'error',
        'http_code' => 500,
        'blocking' => false,
        'recovery' => 'retry',
        'report' => true,
        'log' => true,
        'notify' => false,
    ],
    'GDPR_DELETION_PROCESSING_ERROR' => [
        'msg' => 'gdpr.errors.deletion_processing_error',
        'msg_to' => ['toast', 'inline'],
        'type' => 'error',
        'http_code' => 500,
        'blocking' => true,
        'recovery' => 'contact',
        'report' => true,
        'log' => true,
        'notify' => true,
    ],
    
    // GDPR Breach Report errors
    'GDPR_BREACH_REPORT_ERROR' => [
        'msg' => 'gdpr.errors.breach_report_error',
        'msg_to' => ['toast', 'console'],
        'type' => 'error',
        'http_code' => 500,
        'blocking' => false,
        'recovery' => 'retry',
        'report' => true,
        'log' => true,
        'notify' => true,
    ],
    'GDPR_BREACH_EVIDENCE_UPLOAD_ERROR' => [
        'msg' => 'gdpr.errors.breach_evidence_upload_error',
        'msg_to' => ['toast', 'inline'],
        'type' => 'error',
        'http_code' => 500,
        'blocking' => false,
        'recovery' => 'retry',
        'report' => true,
        'log' => true,
        'notify' => false,
    ],
    
    // GDPR Activity Log errors
    'GDPR_ACTIVITY_LOG_ERROR' => [
        'msg' => 'gdpr.errors.activity_log_error',
        'msg_to' => ['toast'],
        'type' => 'error',
        'http_code' => 500,
        'blocking' => false,
        'recovery' => 'reload',
        'report' => true,
        'log' => true,
        'notify' => false,
    ],
    
    // GDPR Security errors
    'GDPR_ENHANCED_SECURITY_REQUIRED' => [
        'msg' => 'gdpr.errors.security_verification_required',
        'msg_to' => ['toast', 'redirect'],
        'type' => 'warning',
        'http_code' => 403,
        'blocking' => true,
        'recovery' => 'redirect',
        'recovery_url' => '/login',
        'report' => false,
        'log' => true,
        'notify' => false,
    ],
    'GDPR_CRITICAL_SECURITY_REQUIRED' => [
        'msg' => 'gdpr.errors.password_confirmation_required',
        'msg_to' => ['toast', 'redirect'],
        'type' => 'warning',
        'http_code' => 403,
        'blocking' => true,
        'recovery' => 'redirect',
        'recovery_url' => '/password/confirm',
        'report' => false,
        'log' => true,
        'notify' => false,
    ],
    
    // ... other error codes
];
```

### Using UEM in the GDPR System

These error codes are used throughout the GDPR system for consistent error handling:

```php
// Example in a controller method
try {
    // Attempt some operation
    $result = $this->consentService->updateConsents($user, $consents);
    
    if (!$result) {
        return redirect()->route('gdpr.consent')
            ->with('error', __('gdpr.consent.update_error'));
    }
    
    return redirect()->route('gdpr.consent')
        ->with('success', __('gdpr.consent.update_success'));
} catch (\Throwable $e) {
    // Handle error with UEM
    $this->errorManager->handle('GDPR_CONSENT_UPDATE_ERROR', [
        'user_id' => $user->id,
        'consents' => array_keys($consents),
        'error' => $e->getMessage()
    ], $e);
    
    return redirect()->route('gdpr.consent')
        ->with('error', __('gdpr.consent.update_error'));
}
```

The frontend JavaScript also integrates with UEM for client-side error handling:

```javascript
// Example in consent-manager.ts
saveConsent(key, value) {
    // API call to save consent
    UEM.safeFetch('/gdpr/consent/update', {
        method: 'POST',
        headers: { /* ... */ },
        body: JSON.stringify({
            consent_key: key,
            value: value ? 1 : 0
        })
    })
    .then(response => response.json())
    .then(data => {
        // Success handling
    })
    .catch(error => {
        // Client-side error handling with UEM
        UEM.handleClientError('GDPR_CONSENT_SAVE_ERROR', {
            message: window.translations.gdpr.consent_save_error,
            context: { key, error }
        });
    });
}
```

## Testing Guidelines

### Test Structure

The GDPR system includes comprehensive tests:

1. **GdprTestCase**: Base test case with common utilities
2. **Feature Tests**: Test each controller/feature
3. **Accessibility Tests**: Verify ARIA compliance
4. **Performance Tests**: Validate export performance

### Testing Example

```php
// tests/Feature/Gdpr/ConsentControllerTest.php
public function testUpdateConsentsSuccessfully()
{
    $this->loginAsUser();
    
    $consents = $this->createTestConsents();
    
    $response = $this->post(route('gdpr.consent.update'), [
        'consents' => $consents
    ]);
    
    $response->assertRedirect(route('gdpr.consent'));
    $response->assertSessionHas('success');
    
    // Verify the consents were saved in the database
    $this->assertDatabaseHas('user_consents', [
        'user_id' => $this->user->id,
        'consent_key' => 'essential',
        'consented' => true,
    ]);
}
```

### Test Coverage Requirements

Tests should cover:

1. Basic functionality (happy path)
2. Validation errors
3. Authorization checks
4. Error handling
5. ARIA accessibility compliance

## Extending the System

### Adding New Consent Types

1. Add to `gdpr.consent.definitions` in config
2. Add translations in `gdpr.php` language files
3. Update default values in `gdpr.consent.defaults`

### Adding New Export Formats

1. Add to `gdpr.export.available_formats` in config
2. Implement format handler in `DataExportService`

### Adding New Processing Restriction Types

1. Add to `ProcessingRestrictionType` enum
2. Add translations in `gdpr.php` language files
3. Update `ProcessingRestrictionService` as needed

### Custom Notifications

1. Create new notification class extending `BaseGdprNotification`
2. Update config to use the new class
3. Add relevant templates in `resources/views/emails/gdpr/`

---

This documentation provides a comprehensive overview of the FlorenceEGI GDPR System. Developers should refer to the specific file implementations for detailed code examples and functionality. The system is designed to be maintainable, extensible, and compliant with both GDPR requirements and Oracode 3.0 principles.
