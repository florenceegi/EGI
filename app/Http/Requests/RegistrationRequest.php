<?php

namespace App\Http\Requests;

use App\Rules\ValidIban;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * @Oracode Request: Registration Form Validation with UEM Orchestration
 * 🎯 Purpose: Unified validation for user registration that bridges Laravel validation
 * with Ultra Error Manager for consistent error handling and audit trail
 * 🧱 Core Logic: Intercepts failed validation and maps Laravel errors to UEM codes
 * 📡 API: Standard Laravel FormRequest with failedValidation() override
 * 🛡️ Security: Sanitizes sensitive data, logs all validation attempts with IP tracking
 * 🌍 I18n: Full translation support for both Laravel validation and UEM messages
 *
 * @package App\Http\Requests
 * @author Padmin D. Curtis (AI Partner OS1.5.1-Compliant) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI MVP - Registration Domain)
 * @date 2025-06-24
 * @context registration
 */
class RegistrationRequest extends FormRequest {
    /**
     * Ultra Error Manager instance for unified error handling
     * @var ErrorManagerInterface
     */
    protected ErrorManagerInterface $errorManager;

    /**
     * @Oracode Constructor: DI-First Error Manager Integration
     * 🎯 Purpose: Initialize UEM dependency for validation error orchestration
     * 🛡️ Security: Ensures error manager is available before validation runs
     */
    public function __construct() {
        parent::__construct();
        $this->errorManager = app(ErrorManagerInterface::class);
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool Always true for registration (public endpoint)
     * @oracode-dimension access-control
     */
    public function authorize(): bool {
        return true; // Registration is public
    }

    /**
     * @Oracode Method: Validation Rules Definition
     * 🎯 Purpose: Define comprehensive validation rules for user registration
     * 📥 Input: None (uses request data automatically)
     * 📤 Output: Array of Laravel validation rules
     * 🧱 Core Logic: Covers all required fields with security-focused constraints
     *
     * @return array<string, string> Laravel validation rules array
     * @oracode-dimension data-integrity
     * @value-flow Validates user input before account creation
     * @community-impact Ensures data quality for platform ecosystem
     * @transparency-level High - all rules are explicit and auditable
     */
    public function rules(): array {
        // 🎯 Dynamic user types from config with fallback
        $allowedUserTypes = config('app.fegi_user_type', []);

        // 🛡️ Fallback to default user types if config is empty or missing
        if (empty($allowedUserTypes)) {
            $allowedUserTypes = [
                'commissioner',
                'collector',
                'creator',
                'patron',
                'epp',
                'company',
                'trader_pro',
                'pa_entity'
            ];
        }

        $userTypeRule = 'required|in:' . implode(',', $allowedUserTypes);

        return [
            'name' => 'required|string|max:255',
            'nick_name' => 'nullable|string|max:50|unique:users,nick_name',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'user_type' => $userTypeRule,
            'terms_accepted' => 'required|accepted',

            // ═══ GDPR REQUIRED ═══
            'privacy_policy_accepted' => 'required|accepted',
            'age_confirmation' => 'required|accepted',

            // ═══ GDPR OPTIONAL ═══
            'consents' => 'sometimes|array',
            'consents.analytics' => 'sometimes|string|in:1,0',
            'consents.marketing' => 'sometimes|string|in:1,0',
            'consents.profiling' => 'sometimes|string|in:1,0',

            // ═══ ORGANIZATION (Company/PA) ═══
            'org_name' => 'nullable|string|max:255',

            // ═══ WALLET & IBAN (OPTIONAL) ═══
            'iban' => ['nullable', 'string', 'min:15', 'max:34', new ValidIban()],
            'wallet_passphrase' => 'nullable|string|min:12|max:255',
            'accept_custody_seed' => 'sometimes|accepted',
        ];
    }

    /**
     * @Oracode Method: Localized Validation Messages
     * 🎯 Purpose: Provide user-friendly, translated validation messages
     * 📤 Output: Array mapping validation keys to translated messages
     * 🌍 I18n: Uses Laravel translation system for multi-language support
     * 🛡️ Privacy: No sensitive data exposed in error messages
     *
     * @return array<string, string> Localized validation messages
     * @oracode-dimension user-experience
     * @narrative-coherence Maintains consistent voice across all form errors
     */
    public function messages(): array {
        return [
            'email.unique' => __('validation.custom.email.unique'),
            'email.email' => __('validation.custom.email.email'),
            'email.required' => __('validation.custom.email.required'),
            'password.min' => __('validation.custom.password.min'),
            'password.confirmed' => __('validation.custom.password.confirmed'),
            'password.required' => __('validation.custom.password.required'),
            'password_confirmation.required' => __('validation.custom.password_confirmation.required'),
            'name.required' => __('validation.custom.name.required'),
            'name.string' => __('validation.custom.name.string'),
            'name.max' => __('validation.custom.name.max'),
            'nick_name.unique' => __('validation.custom.nick_name.unique'),
            'nick_name.max' => __('validation.custom.nick_name.max'),
            'user_type.required' => __('validation.custom.usertype.required'),
            'user_type.in' => __('validation.custom.usertype.in'),
            'terms_accepted.accepted' => __('validation.custom.terms_accepted.accepted'),
            'terms_accepted.required' => __('validation.custom.terms_accepted.required'),
        ];
    }

    /**
     * @Oracode Method: Field Attribute Names
     * 🎯 Purpose: Provide human-readable field names for error messages
     * 📤 Output: Array mapping field names to translated labels
     * 🌍 I18n: Localized field names for better user experience
     *
     * @return array<string, string> Localized field attributes
     * @oracode-dimension user-experience
     */
    public function attributes(): array {
        return [
            'email' => __('validation.attributes.email'),
            'password' => __('validation.attributes.password'),
            'password_confirmation' => __('validation.attributes.password_confirmation'),
            'name' => __('validation.attributes.name'),
            'usertype' => __('validation.attributes.usertype'),
            'terms_accepted' => __('validation.attributes.terms_accepted'),
            'iban' => __('validation.attributes.iban'),
            'wallet_passphrase' => __('validation.attributes.wallet_passphrase'),
            'accept_custody_seed' => __('validation.attributes.accept_custody_seed'),
        ];
    }

    /**
     * @Oracode Method: Failed Validation Orchestrator
     * 🎯 Purpose: Bridge Laravel validation failures to UEM error handling system
     * 📥 Input: Validator instance with failed validation results
     * 📤 Output: Delegates to UEM (method does not return)
     * 🧱 Core Logic:
     *   1. Extract validation errors from Laravel validator
     *   2. Map specific errors to UEM error codes
     *   3. Delegate to UEM with comprehensive context
     *   4. UEM handles response generation and logging
     * 🛡️ Security: Sanitizes sensitive data before logging
     * 🔄 Audit: Creates complete audit trail through UEM
     *
     * @param Validator $validator Laravel validator with failed rules
     * @return void (UEM handles all response logic)
     * @throws never (UEM manages all error responses)
     *
     * @oracode-dimension error-orchestration
     * @value-flow Converts framework errors to business domain errors
     * @community-impact Provides consistent error experience across platform
     * @transparency-level Medium - logs validation attempts without exposing sensitive data
     * @narrative-coherence Maintains FlorenceEGI's professional error handling standards
     */
    protected function failedValidation(Validator $validator): void {
        $errors = $validator->errors()->toArray();

        // Map Laravel validation errors to UEM domain codes
        $errorCode = $this->mapValidationToUemCode($errors);

        // Delegate to UEM with comprehensive context
        $this->errorManager->handle($errorCode, [
            'validation_errors' => $errors,
            'input_data' => $this->safe()->except(['password', 'password_confirmation']),
            'ip_address' => $this->ip(),
            'user_agent' => $this->userAgent(),
            'route' => $this->route()->getName(),
            'failed_fields' => array_keys($errors),
            'locale' => app()->getLocale(),
            'timestamp' => now()->toIso8601String()
        ]);

        // UEM handles all response logic - this point is never reached
    }

    /**
     * @Oracode Method: Validation Error to UEM Code Mapper
     * 🎯 Purpose: Intelligent mapping of Laravel validation errors to specific UEM codes
     * 📥 Input: Array of Laravel validation errors
     * 📤 Output: UEM error code string
     * 🧱 Core Logic: Priority-based mapping for most specific error identification
     * 🔄 Resilience: Falls back to generic code if no specific match found
     *
     * @param array<string, array<string>> $errors Laravel validation errors array
     * @return string UEM error code for consistent handling
     *
     * @oracode-dimension error-classification
     * @value-flow Translates technical validation to business domain language
     * @transparency-level High - mapping logic is explicit and auditable
     */
    private function mapValidationToUemCode(array $errors): string {
        // Priority to most specific email errors
        if (isset($errors['email'])) {
            foreach ($errors['email'] as $error) {
                if (str_contains($error, __('validation.custom.email.unique'))) {
                    return 'REGISTRATION_EMAIL_ALREADY_EXISTS';
                }
                if (str_contains($error, __('validation.custom.email.email'))) {
                    return 'REGISTRATION_INVALID_EMAIL_FORMAT';
                }
            }
        }

        // Password-specific errors
        if (isset($errors['password'])) {
            foreach ($errors['password'] as $error) {
                if (str_contains($error, __('validation.custom.password.confirmed'))) {
                    return 'REGISTRATION_PASSWORD_CONFIRMATION_MISMATCH';
                }
                if (str_contains($error, __('validation.custom.password.min'))) {
                    return 'REGISTRATION_PASSWORD_TOO_WEAK';
                }
            }
        }

        // Required field errors
        if (isset($errors['name']) || isset($errors['usertype']) || isset($errors['terms_accepted'])) {
            return 'REGISTRATION_REQUIRED_FIELD_MISSING';
        }

        // Generic fallback for comprehensive error handling
        return 'REGISTRATION_VALIDATION_COMPREHENSIVE_FAILED';
    }
}
