/**
 * @Oracode TypeScript: Personal Data Domain Logic (OS1-Compliant)
 * 🎯 Purpose: Client-side logic for personal data management with GDPR compliance
 * 🛡️ Privacy: Real-time validation, secure form handling, audit trail
 * 🧱 Core Logic: Country-specific validation, UEM integration, consent management
 * 🌍 Scale: 6 MVP countries support with enterprise-grade validation
 * ⏰ MVP: Critical client logic for 30 June deadline
 *
 * @package resources/js/domains
 * @author Padmin D. Curtis (AI Partner OS1-Compliant)
 * @version 1.0.0 (FlorenceEGI MVP - Personal Data Domain)
 * @deadline 2025-06-30
 */

// Global window interface extensions
// declare global {
//     interface Window {
//         personalDataConfig: PersonalDataConfig;
//         UEM: any; // UltraErrorManager client-side
//         showToast: (message: string, type: string) => void;
//     }
// }

// Configuration interface
// Configuration interface
interface PersonalDataManagerConfig {
    canEdit?: boolean;
    authType?: 'strong' | 'weak' | 'guest';
    userCountry?: string;
    availableCountries?: Record<string, string>;
    validationConfig?: ValidationConfig;
    csrfToken: string;
    updateUrl?: string;
    exportUrl?: string;
    translations: Record<string, string>;
}

// ...


// Validation configuration interface
interface ValidationConfig {
    country: string;
    validator_type: string;
    business_types: Record<string, any>;
}

// Form data interface
interface PersonalDataForm {
    birth_date?: string;
    birth_place?: string;
    gender?: string;
    street?: string;
    city?: string;
    zip?: string;
    province?: string;
    country?: string;
    home_phone?: string;
    cell_phone?: string;
    work_phone?: string;
    emergency_contact?: string;
    fiscal_code?: string;
    tax_id_number?: string;
    allow_personal_data_processing: boolean;
    processing_purposes: string[];
}

// Validation result interface
interface ValidationResult {
    isValid: boolean;
    field: string;
    message?: string;
    code?: string;
}

// Country-specific validation patterns
const VALIDATION_PATTERNS = {
    IT: {
        fiscal_code: /^[A-Z]{6}[0-9]{2}[ABCDEHLMPRST][0-9]{2}[A-Z][0-9]{3}[A-Z]$/i,
        postal_code: /^[0-9]{5}$/,
        phone: /^(\+39|0039|39)?[\s]?[0-9]{2,4}[\s]?[0-9]{6,8}$/
    },
    PT: {
        fiscal_code: /^[0-9]{9}$/,
        postal_code: /^[0-9]{4}-[0-9]{3}$/,
        phone: /^(\+351|00351|351)?[\s]?[0-9]{2,3}[\s]?[0-9]{3}[\s]?[0-9]{4}$/
    },
    FR: {
        fiscal_code: /^[0-9]{13}$/,
        postal_code: /^[0-9]{5}$/,
        phone: /^(\+33|0033|33)?[\s]?[0-9][\s]?[0-9]{2}[\s]?[0-9]{2}[\s]?[0-9]{2}[\s]?[0-9]{2}$/
    },
    ES: {
        fiscal_code: /^[0-9]{8}[A-Z]$/i,
        postal_code: /^[0-9]{5}$/,
        phone: /^(\+34|0034|34)?[\s]?[0-9]{3}[\s]?[0-9]{3}[\s]?[0-9]{3}$/
    },
    EN: {
        fiscal_code: /^[A-Z]{2}[0-9]{6}[A-Z]$/i,
        postal_code: /^[A-Z]{1,2}[0-9R][0-9A-Z]?\s?[0-9][A-Z]{2}$/i,
        phone: /^(\+44|0044|44)?[\s]?[0-9]{2,4}[\s]?[0-9]{4}[\s]?[0-9]{4}$/
    },
    DE: {
        fiscal_code: /^[0-9]{11}$/,
        postal_code: /^[0-9]{5}$/,
        phone: /^(\+49|0049|49)?[\s]?[0-9]{2,4}[\s]?[0-9]{6,8}$/
    }
} as const;

/**
 * @Oracode Class: Personal Data Manager (OS1-Compliant)
 * 🎯 Purpose: Main class for personal data domain client-side logic
 * 🛡️ Privacy: GDPR-compliant validation and form handling
 * 🧱 Core Logic: Real-time validation, country-specific rules, UEM integration
 */
class PersonalDataManager {
    private config: PersonalDataManagerConfig;
    private form: HTMLFormElement | null;
    private validationStatus: Record<string, boolean> = {};
    private originalData: any = {};
    private isDirty: boolean = false;
    private validationTimer: number | null = null;
    private isSubmitting: boolean = false;
    private changesMade: boolean = false;

    constructor(config: PersonalDataManagerConfig) {
        this.config = config;
        this.form = document.getElementById('personal-data-form') as HTMLFormElement;

        if (this.form) {
            this.initialize();
        } else {
            console.warn('[Personal Data] Form not found');
        }
    }

    /**
     * @Oracode Method: Initialize Personal Data Manager
     * 🎯 Purpose: Set up event listeners and initial validation
     * 📥 Input: No parameters (uses instance config)
     * 📤 Output: Void (initializes manager state)
     */
    private initialize(): void {
        this.form = document.getElementById('personal-data-form') as HTMLFormElement;

        if (!this.form) {
            this.handleError('PERSONAL_DATA_FORM_NOT_FOUND', new Error('Form element not found'));
            return;
        }

        // Store original form data for change detection
        this.captureOriginalData();

        // Initialize event listeners
        this.initializeEventListeners();

        // Initialize real-time validation
        this.initializeValidation();

        console.log('[Personal Data] Manager initialized with config:', this.config);

        // Initialize GDPR consent handling
        this.initializeConsentHandling();

        // Initialize export functionality
        this.initializeExportHandling();

        console.log('[Personal Data] Manager initialized successfully');
    }

    /**
     * @Oracode Method: Capture Original Form Data
     * 🎯 Purpose: Store initial form state for change detection
     * 📥 Input: No parameters (reads from form)
     * 📤 Output: Void (stores in instance variable)
     */
    private captureOriginalData(): void {
        if (!this.form) return;

        const formData = new FormData(this.form);
        this.originalData = {
            birth_date: formData.get('birth_date') as string || '',
            birth_place: formData.get('birth_place') as string || '',
            gender: formData.get('gender') as string || '',
            street: formData.get('street') as string || '',
            city: formData.get('city') as string || '',
            zip: formData.get('zip') as string || '',
            province: formData.get('province') as string || '',
            country: formData.get('country') as string || '',
            home_phone: formData.get('home_phone') as string || '',
            cell_phone: formData.get('cell_phone') as string || '',
            work_phone: formData.get('work_phone') as string || '',
            emergency_contact: formData.get('emergency_contact') as string || '',
            fiscal_code: formData.get('fiscal_code') as string || '',
            tax_id_number: formData.get('tax_id_number') as string || '',
            allow_personal_data_processing: (formData.get('allow_personal_data_processing') as string) === '1',
            processing_purposes: formData.getAll('processing_purposes[]') as string[]
        };
    }

    /**
     * @Oracode Method: Initialize Event Listeners
     * 🎯 Purpose: Set up all form interaction handlers
     * 📥 Input: No parameters (attaches to form elements)
     * 📤 Output: Void (attaches event listeners)
     */
    private initializeEventListeners(): void {
        if (!this.form) return;

        // Form submission
        this.form.addEventListener('submit', this.handleFormSubmit.bind(this));

        // Input change detection
        this.form.addEventListener('input', this.handleInputChange.bind(this));
        this.form.addEventListener('change', this.handleInputChange.bind(this));

        // Country change for dynamic validation
        const countrySelect = this.form.querySelector('#country') as HTMLSelectElement;
        if (countrySelect) {
            countrySelect.addEventListener('change', this.handleCountryChange.bind(this));
        }

        // Reset button
        const resetButton = this.form.querySelector('[data-action="reset-form"]') as HTMLButtonElement;
        if (resetButton) {
            resetButton.addEventListener('click', this.handleFormReset.bind(this));
        }

        // Save button (if visible)
        const saveButton = document.getElementById('save-button') as HTMLButtonElement;
        if (saveButton) {
            saveButton.addEventListener('click', this.handleFormSubmit.bind(this));
        }

        // Prevent accidental navigation when changes are made
        window.addEventListener('beforeunload', this.handleBeforeUnload.bind(this));
    }

    /**
     * @Oracode Method: Initialize Real-time Validation
     * 🎯 Purpose: Set up field-by-field validation as user types
     * 📥 Input: No parameters (attaches to validation fields)
     * 📤 Output: Void (enables real-time validation)
     */
    private initializeValidation(): void {
        if (!this.form) return;

        const validationFields = this.form.querySelectorAll('[data-validation]');

        validationFields.forEach(field => {
            const input = field as HTMLInputElement | HTMLSelectElement;
            const validationType = input.getAttribute('data-validation');

            if (!validationType) return;

            // Real-time validation on input
            input.addEventListener('input', () => {
                if (this.validationTimer) {
                    clearTimeout(this.validationTimer);
                }

                this.validationTimer = window.setTimeout(() => {
                    this.validateField(input, validationType);
                }, 300); // Debounce validation
            });

            // Immediate validation on blur
            input.addEventListener('blur', () => {
                this.validateField(input, validationType);
            });
        });
    }

    /**
     * @Oracode Method: Initialize GDPR Consent Handling
     * 🎯 Purpose: Set up consent checkbox logic and processing purposes
     * 📥 Input: No parameters (attaches to consent elements)
     * 📤 Output: Void (enables consent management)
     */
    private initializeConsentHandling(): void {
        const consentCheckbox = document.getElementById('allow_personal_data_processing') as HTMLInputElement;
        const purposesContainer = document.getElementById('processing-purposes') as HTMLElement;

        if (consentCheckbox && purposesContainer) {
            consentCheckbox.addEventListener('change', () => {
                const isChecked = consentCheckbox.checked;
                purposesContainer.style.display = isChecked ? 'block' : 'none';

                // Clear purposes if consent is withdrawn
                if (!isChecked) {
                    const purposeCheckboxes = purposesContainer.querySelectorAll('input[type="checkbox"]');
                    purposeCheckboxes.forEach(checkbox => {
                        (checkbox as HTMLInputElement).checked = false;
                    });
                }
            });
        }
    }

    /**
     * @Oracode Method: Initialize Export Handling
     * 🎯 Purpose: Set up data export functionality
     * 📥 Input: No parameters (attaches to export buttons)
     * 📤 Output: Void (enables data export)
     */
    private initializeExportHandling(): void {
        const exportButton = document.querySelector('[data-action="export-personal-data"]') as HTMLButtonElement;

        if (exportButton) {
            exportButton.addEventListener('click', this.handleDataExport.bind(this));
        }
    }

    /**
     * @Oracode Method: Validate Individual Field
     * 🎯 Purpose: Validate single form field with country-specific rules
     * 📥 Input: Field element and validation type
     * 📤 Output: ValidationResult with validation status
     */
    private validateField(field: HTMLInputElement | HTMLSelectElement, validationType: string): ValidationResult {
        const value = field.value.trim();
        const country = this.config.userCountry || 'IT';

        let result: ValidationResult = {
            isValid: true,
            field: validationType
        };

        // Skip validation for empty optional fields
        if (!value && !this.isRequiredField(validationType)) {
            this.clearFieldValidation(field);
            return result;
        }

        // Perform validation based on type
        switch (validationType) {
            case 'fiscal_code':
                result = this.validateFiscalCode(value, country);
                break;
            case 'zip':
                result = this.validatePostalCode(value, country);
                break;
            case 'home_phone':
            case 'cell_phone':
            case 'work_phone':
                result = this.validatePhone(value, country);
                break;
            case 'birth_date':
                result = this.validateBirthDate(value);
                break;
            case 'birth_place':
                result = this.validateBirthPlace(value);
                break;
            default:
                result = this.validateGeneric(value, validationType);
        }

        // Apply visual feedback
        this.applyFieldValidation(field, result);

        // Update validation status in sidebar
        this.updateValidationStatus(validationType, result);

        return result;
    }

    /**
     * @Oracode Method: Validate Fiscal Code
     * 🎯 Purpose: Country-specific fiscal code validation
     * 📥 Input: Fiscal code value and country
     * 📤 Output: ValidationResult with fiscal code validation
     */
    private validateFiscalCode(value: string, country: string): ValidationResult {
        if (!value) {
            return { isValid: true, field: 'fiscal_code' }; // Optional field
        }

        const patterns = VALIDATION_PATTERNS[country as keyof typeof VALIDATION_PATTERNS];
        if (!patterns) {
            return { isValid: true, field: 'fiscal_code' }; // No validation for unknown country
        }

        const isValid = patterns.fiscal_code.test(value);

        return {
            isValid,
            field: 'fiscal_code',
            message: isValid ? undefined : this.config.translations.fiscal_code_invalid || 'Invalid fiscal code format',
            code: isValid ? undefined : 'INVALID_FISCAL_CODE_FORMAT'
        };
    }

    /**
     * @Oracode Method: Validate Postal Code
     * 🎯 Purpose: Country-specific postal code validation
     * 📥 Input: Postal code value and country
     * 📤 Output: ValidationResult with postal code validation
     */
    private validatePostalCode(value: string, country: string): ValidationResult {
        if (!value) {
            return { isValid: true, field: 'zip' }; // Optional field
        }

        const patterns = VALIDATION_PATTERNS[country as keyof typeof VALIDATION_PATTERNS];
        if (!patterns) {
            return { isValid: true, field: 'zip' }; // No validation for unknown country
        }

        const isValid = patterns.postal_code.test(value);

        return {
            isValid,
            field: 'zip',
            message: isValid ? undefined : this.config.translations.postal_code_invalid || 'Invalid postal code format',
            code: isValid ? undefined : 'INVALID_POSTAL_CODE_FORMAT'
        };
    }

    /**
     * @Oracode Method: Validate Phone Number
     * 🎯 Purpose: Country-specific phone validation
     * 📥 Input: Phone value and country
     * 📤 Output: ValidationResult with phone validation
     */
    private validatePhone(value: string, country: string): ValidationResult {
        if (!value) {
            return { isValid: true, field: 'phone' }; // Optional field
        }

        const patterns = VALIDATION_PATTERNS[country as keyof typeof VALIDATION_PATTERNS];
        if (!patterns) {
            // Generic phone validation for unknown countries
            const genericPattern = /^[\+]?[\d\s\-\(\)\.]{7,20}$/;
            const isValid = genericPattern.test(value);
            return {
                isValid,
                field: 'phone',
                message: isValid ? undefined : 'Invalid phone format',
                code: isValid ? undefined : 'INVALID_PHONE_FORMAT'
            };
        }

        const isValid = patterns.phone.test(value);

        return {
            isValid,
            field: 'phone',
            message: isValid ? undefined : this.config.translations.phone_invalid || 'Invalid phone format',
            code: isValid ? undefined : 'INVALID_PHONE_FORMAT'
        };
    }

    /**
     * @Oracode Method: Validate Birth Date
     * 🎯 Purpose: Birth date validation with age requirements
     * 📥 Input: Birth date value
     * 📤 Output: ValidationResult with birth date validation
     */
    private validateBirthDate(value: string): ValidationResult {
        if (!value) {
            return { isValid: true, field: 'birth_date' }; // Optional field
        }

        const birthDate = new Date(value);
        const today = new Date();
        const age = today.getFullYear() - birthDate.getFullYear();
        const monthDiff = today.getMonth() - birthDate.getMonth();

        // Check if date is valid
        if (isNaN(birthDate.getTime())) {
            return {
                isValid: false,
                field: 'birth_date',
                message: 'Invalid date format',
                code: 'INVALID_DATE_FORMAT'
            };
        }

        // Check minimum age (13 years)
        const actualAge = monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate()) ? age - 1 : age;

        if (actualAge < 13) {
            return {
                isValid: false,
                field: 'birth_date',
                message: this.config.translations.birth_date_age || 'Must be at least 13 years old',
                code: 'AGE_REQUIREMENT_NOT_MET'
            };
        }

        // Check maximum age (120 years)
        if (actualAge > 120) {
            return {
                isValid: false,
                field: 'birth_date',
                message: 'Invalid birth date',
                code: 'INVALID_BIRTH_DATE'
            };
        }

        return { isValid: true, field: 'birth_date' };
    }

    /**
     * @Oracode Method: Validate Birth Place
     * 🎯 Purpose: Birth place format validation
     * 📥 Input: Birth place value
     * 📤 Output: ValidationResult with birth place validation
     */
    private validateBirthPlace(value: string): ValidationResult {
        if (!value) {
            return { isValid: true, field: 'birth_place' }; // Optional field
        }

        // Basic format validation (letters, spaces, apostrophes, dashes)
        const pattern = /^[\p{L}\s\-.,']+$/u;
        const isValid = pattern.test(value) && value.length >= 2 && value.length <= 255;

        return {
            isValid,
            field: 'birth_place',
            message: isValid ? undefined : 'Invalid birth place format',
            code: isValid ? undefined : 'INVALID_BIRTH_PLACE_FORMAT'
        };
    }

    /**
     * @Oracode Method: Validate Generic Field
     * 🎯 Purpose: Generic validation for other field types
     * 📥 Input: Field value and validation type
     * 📤 Output: ValidationResult with generic validation
     */
    private validateGeneric(value: string, validationType: string): ValidationResult {
        // Basic validation based on field type
        switch (validationType) {
            case 'street':
                const streetPattern = /^[\p{L}\p{N}\s\-\.,\/#]+$/u;
                return {
                    isValid: !value || (streetPattern.test(value) && value.length <= 255),
                    field: validationType,
                    message: 'Invalid street address format',
                    code: 'INVALID_STREET_FORMAT'
                };

            case 'city':
                const cityPattern = /^[-\p{L}\s.']+$/u;

                return {
                    isValid: !value || (cityPattern.test(value) && value.length <= 100),
                    field: validationType,
                    message: 'Invalid city format',
                    code: 'INVALID_CITY_FORMAT'
                };

            default:
                return { isValid: true, field: validationType };
        }
    }

    /**
     * @Oracode Method: Check if Field is Required
     * 🎯 Purpose: Determine if field is required for validation
     * 📥 Input: Validation type
     * 📤 Output: Boolean indicating if field is required
     */
    private isRequiredField(validationType: string): boolean {
        const requiredFields = ['allow_personal_data_processing'];
        return requiredFields.includes(validationType);
    }

    /**
     * @Oracode Method: Apply Field Validation Visual Feedback
     * 🎯 Purpose: Apply CSS classes and indicators based on validation result
     * 📥 Input: Field element and validation result
     * 📤 Output: Void (applies visual changes)
     */
    private applyFieldValidation(field: HTMLInputElement | HTMLSelectElement, result: ValidationResult): void {
        // Remove existing validation classes
        field.classList.remove('field-valid', 'field-invalid', 'field-pending');

        // Apply appropriate class
        if (result.isValid) {
            field.classList.add('field-valid');
        } else {
            field.classList.add('field-invalid');
        }

        // Update validation indicator
        this.updateValidationIndicator(field, result);
    }

    /**
     * @Oracode Method: Update Validation Indicator
     * 🎯 Purpose: Show/hide validation icons next to fields
     * 📥 Input: Field element and validation result
     * 📤 Output: Void (updates indicator)
     */
    private updateValidationIndicator(field: HTMLInputElement | HTMLSelectElement, result: ValidationResult): void {
        const container = field.parentElement;
        if (!container) return;

        // Remove existing indicator
        const existingIndicator = container.querySelector('.validation-indicator');
        if (existingIndicator) {
            existingIndicator.remove();
        }

        // Add new indicator if needed
        if (field.value.trim()) {
            const indicator = document.createElement('div');
            indicator.className = `validation-indicator ${result.isValid ? 'valid' : 'invalid'}`;

            if (result.isValid) {
                indicator.innerHTML = `
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                `;
            } else {
                indicator.innerHTML = `
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                `;
                indicator.title = result.message || 'Validation error';
            }

            container.style.position = 'relative';
            container.appendChild(indicator);
        }
    }

    /**
     * @Oracode Method: Clear Field Validation
     * 🎯 Purpose: Remove validation classes and indicators
     * 📥 Input: Field element
     * 📤 Output: Void (clears validation state)
     */
    private clearFieldValidation(field: HTMLInputElement | HTMLSelectElement): void {
        field.classList.remove('field-valid', 'field-invalid', 'field-pending');

        const container = field.parentElement;
        if (container) {
            const indicator = container.querySelector('.validation-indicator');
            if (indicator) {
                indicator.remove();
            }
        }
    }

    /**
     * @Oracode Method: Update Validation Status in Sidebar
     * 🎯 Purpose: Update real-time validation status display
     * 📥 Input: Field type and validation result
     * 📤 Output: Void (updates sidebar status)
     */
    private updateValidationStatus(fieldType: string, result: ValidationResult): void {
        const statusContainer = document.getElementById('validation-status');
        if (!statusContainer) return;

        const statusItem = statusContainer.querySelector(`[data-field="${fieldType}"]`) || this.createStatusItem(fieldType, statusContainer);

        if (statusItem) {
            const statusText = statusItem.querySelector('.status-text');
            const statusIcon = statusItem.querySelector('.status-icon');

            if (statusText && statusIcon) {
                if (result.isValid) {
                    statusText.textContent = 'Valid';
                    statusIcon.className = 'text-green-500 status-icon';
                    statusIcon.innerHTML = '✓';
                } else {
                    statusText.textContent = result.message || 'Invalid';
                    statusIcon.className = 'text-red-500 status-icon';
                    statusIcon.innerHTML = '✗';
                }
            }
        }
    }

    /**
     * @Oracode Method: Create Status Item
     * 🎯 Purpose: Create new validation status item in sidebar
     * 📥 Input: Field type and container element
     * 📤 Output: Created status element
     */
    private createStatusItem(fieldType: string, container: HTMLElement): HTMLElement {
        const item = document.createElement('div');
        item.className = 'flex items-center justify-between py-1 text-xs';
        item.setAttribute('data-field', fieldType);

        item.innerHTML = `
            <span class="capitalize">${fieldType.replace('_', ' ')}</span>
            <div class="flex items-center space-x-1">
                <span class="status-icon"></span>
                <span class="status-text"></span>
            </div>
        `;

        container.appendChild(item);
        return item;
    }

    /**
     * @Oracode Method: Handle Input Change
     * 🎯 Purpose: Track form changes for dirty state management
     * 📥 Input: Change event
     * 📤 Output: Void (updates change tracking)
     */
    private handleInputChange(event: Event): void {
        this.changesMade = true;
        this.updateSaveButtonVisibility();
    }

    /**
     * @Oracode Method: Handle Country Change
     * 🎯 Purpose: Update validation rules when country changes
     * 📥 Input: Change event from country select
     * 📤 Output: Void (updates validation context)
     */
    private handleCountryChange(event: Event): void {
        const select = event.target as HTMLSelectElement;
        const newCountry = select.value;

        if (newCountry && newCountry !== this.config.userCountry) {
            // Update config
            this.config.userCountry = newCountry;

            // Re-validate fiscal fields with new country rules
            const fiscalField = document.getElementById('fiscal_code') as HTMLInputElement;
            const zipField = document.getElementById('zip') as HTMLInputElement;

            if (fiscalField && fiscalField.value) {
                this.validateField(fiscalField, 'fiscal_code');
            }

            if (zipField && zipField.value) {
                this.validateField(zipField, 'zip');
            }

            // Update validation info in sidebar
            this.updateCountrySpecificInfo(newCountry);
        }
    }

    /**
     * @Oracode Method: Update Country-Specific Info
     * 🎯 Purpose: Update validation hints when country changes
     * 📥 Input: New country code
     * 📤 Output: Void (updates UI hints)
     */
    private updateCountrySpecificInfo(country: string): void {
        // Update fiscal section country indicator
        const fiscalSection = document.getElementById('fiscal-section');
        if (fiscalSection) {
            fiscalSection.setAttribute('data-country', country);
        }

        // Update zip field country context
        const zipField = document.getElementById('zip');
        if (zipField) {
            zipField.setAttribute('data-country', country);
        }
    }

    /**
     * @Oracode Method: Handle Form Submit
     * 🎯 Purpose: Process form submission with validation and UEM integration
     * 📥 Input: Submit event
     * 📤 Output: Void (submits form or shows errors)
     */
    private async handleFormSubmit(event: Event): Promise<void> {
        event.preventDefault();

        if (this.isSubmitting || !this.form) return;

        // Validate entire form
        const validationResults = await this.validateEntireForm();

        if (!validationResults.every(result => result.isValid)) {
            this.showValidationErrors(validationResults);
            return;
        }

        this.isSubmitting = true;
        this.showLoadingState();

        try {
            const formData = new FormData(this.form!);
            const data = Object.fromEntries(formData.entries());

            // Add unchecked checkboxes (as false)
            this.form!.querySelectorAll('input[type="checkbox"]').forEach((checkbox: Element) => {
                const input = checkbox as HTMLInputElement;
                if (!input.checked) {
                    data[input.name] = '0';
                } else {
                    data[input.name] = '1';
                }
            });

            // Add CSRF token
            const csrfToken = this.config.csrfToken;

            const response = await fetch(this.config.updateUrl!, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (response.ok && result.success) {
                this.handleSubmitSuccess(result);
            } else {
                this.handleSubmitError(result);
            }

        } catch (error) {
            this.handleError('PERSONAL_DATA_SUBMIT_ERROR', error as Error);
        } finally {
            this.isSubmitting = false;
            this.hideLoadingState();
        }
    }

    /**
     * @Oracode Method: Validate Entire Form
     * 🎯 Purpose: Run validation on all form fields
     * 📥 Input: No parameters (validates current form state)
     * 📤 Output: Promise<ValidationResult[]> with all field validations
     */
    private async validateEntireForm(): Promise<ValidationResult[]> {
        const results: ValidationResult[] = [];

        if (!this.form) return results;

        const validationFields = this.form.querySelectorAll('[data-validation]');

        for (const field of validationFields) {
            const input = field as HTMLInputElement | HTMLSelectElement;
            const validationType = input.getAttribute('data-validation');

            if (validationType) {
                const result = this.validateField(input, validationType);
                results.push(result);
            }
        }

        return results;
    }

    /**
     * @Oracode Method: Show Validation Errors
     * 🎯 Purpose: Display validation errors to user
     * 📥 Input: Array of validation results
     * 📤 Output: Void (shows error messages)
     */
    private showValidationErrors(results: ValidationResult[]): void {
        const errors = results.filter(result => !result.isValid);

        if (errors.length > 0) {
            const errorMessages = errors.map(error => error.message).join(', ');
            this.showMessage(errorMessages, 'error');

            // Focus first invalid field
            const firstErrorField = this.form?.querySelector(`.field-invalid`) as HTMLInputElement;
            if (firstErrorField) {
                firstErrorField.focus();
            }
        }
    }

    /**
     * @Oracode Method: Handle Submit Success
     * 🎯 Purpose: Handle successful form submission
     * 📥 Input: Success response data
     * 📤 Output: Void (shows success feedback)
     */
    private handleSubmitSuccess(result: any): void {
        this.showMessage(this.config.translations.updateSuccess || 'Personal data updated successfully', 'success');

        // Reset change tracking
        this.changesMade = false;
        this.captureOriginalData(); // Update baseline
        this.updateSaveButtonVisibility();

        // Update any displayed data
        if (result.data) {
            this.updateDisplayedData(result.data);
        }

        console.log('[Personal Data] Form submitted successfully');
    }

    /**
     * @Oracode Method: Handle Submit Error
     * 🎯 Purpose: Handle form submission errors using UEM
     * 📥 Input: Error response data
     * 📤 Output: Void (shows error feedback)
     */
    private handleSubmitError(result: any): void {
        if (window.UEM && result.error) {
            // Use UEM for structured error handling
            window.UEM.handle('PERSONAL_DATA_UPDATE_FAILED', result);
        } else {
            // Fallback error display
            const message = result.message || this.config.translations.updateError || 'Failed to update personal data';
            this.showMessage(message, 'error');
        }
    }

    /**
     * @Oracode Method: Handle Data Export
     * 🎯 Purpose: Process data export request
     * 📥 Input: Click event from export button
     * 📤 Output: Void (initiates export or shows error)
     */
    private async handleDataExport(event: Event): Promise<void> {
        event.preventDefault();

        const button = event.target as HTMLButtonElement;
        const originalText = button.textContent;

        button.disabled = true;
        button.textContent = 'Exporting...';

        try {
            const response = await fetch(this.config.exportUrl!, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': this.config.csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    format: 'json',
                    categories: ['all']
                })
            });

            if (response.ok) {
                const contentType = response.headers.get('content-type');

                if (contentType && contentType.includes('application/json')) {
                    const result = await response.json();
                    this.showMessage(this.config.translations.exportStarted || 'Data export started', 'success');
                } else {
                    // Direct download
                    const blob = await response.blob();
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `personal_data_${new Date().toISOString().split('T')[0]}.json`;
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    window.URL.revokeObjectURL(url);

                    this.showMessage('Data exported successfully', 'success');
                }
            } else {
                throw new Error(`Export failed: ${response.status}`);
            }

        } catch (error) {
            this.handleError('PERSONAL_DATA_EXPORT_ERROR', error as Error);
        } finally {
            button.disabled = false;
            button.textContent = originalText;
        }
    }

    /**
     * @Oracode Method: Handle Form Reset
     * 🎯 Purpose: Reset form to original state
     * 📥 Input: Click event from reset button
     * 📤 Output: Void (resets form)
     */
    private handleFormReset(event: Event): void {
        event.preventDefault();

        if (this.changesMade && !confirm('Are you sure you want to discard all changes?')) {
            return;
        }

        if (this.form && this.originalData) {
            // Reset form to original values
            Object.entries(this.originalData).forEach(([key, value]) => {
                const field = this.form!.querySelector(`[name="${key}"]`) as HTMLInputElement | HTMLSelectElement;
                if (field) {
                    if (field.type === 'checkbox') {
                        (field as HTMLInputElement).checked = Boolean(value);
                    } else {
                        field.value = String(value || '');
                    }
                }
            });

            // Reset processing purposes
            const purposeCheckboxes = this.form.querySelectorAll('input[name="processing_purposes[]"]');
            purposeCheckboxes.forEach(checkbox => {
                const input = checkbox as HTMLInputElement;
                input.checked = this.originalData!.processing_purposes.includes(input.value);
            });

            // Clear validation states
            const validationFields = this.form.querySelectorAll('[data-validation]');
            validationFields.forEach(field => {
                this.clearFieldValidation(field as HTMLInputElement);
            });

            // Reset change tracking
            this.changesMade = false;
            this.updateSaveButtonVisibility();

            this.showMessage('Form reset to original values', 'info');
        }
    }

    /**
     * @Oracode Method: Handle Before Unload
     * 🎯 Purpose: Warn user about unsaved changes
     * 📥 Input: Before unload event
     * 📤 Output: Event result (warning or allow navigation)
     */
    private handleBeforeUnload(event: BeforeUnloadEvent): void {
        if (this.changesMade && !this.isSubmitting) {
            event.preventDefault();
            event.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
        }
    }

    /**
     * @Oracode Method: Update Save Button Visibility
     * 🎯 Purpose: Show/hide save button based on changes
     * 📥 Input: No parameters (checks change state)
     * 📤 Output: Void (toggles button visibility)
     */
    private updateSaveButtonVisibility(): void {
        const saveButton = document.getElementById('save-button');
        if (saveButton) {
            if (this.changesMade && this.config.canEdit) {
                saveButton.classList.remove('hidden');
            } else {
                saveButton.classList.add('hidden');
            }
        }
    }

    /**
     * @Oracode Method: Update Displayed Data
     * 🎯 Purpose: Update any read-only displays with new data
     * 📥 Input: Updated data object
     * 📤 Output: Void (updates displays)
     */
    private updateDisplayedData(data: any): void {
        // Update sidebar summary if needed
        // Update last update timestamp
        // Update completeness indicators
        console.log('[Personal Data] Display data updated', data);
    }

    /**
     * @Oracode Method: Show Loading State
     * 🎯 Purpose: Show loading overlay during form submission
     * 📥 Input: No parameters
     * 📤 Output: Void (shows loading UI)
     */
    private showLoadingState(): void {
        const overlay = document.getElementById('loading-overlay');
        if (overlay) {
            overlay.classList.remove('hidden');
        }
    }

    /**
     * @Oracode Method: Hide Loading State
     * 🎯 Purpose: Hide loading overlay after form submission
     * 📥 Input: No parameters
     * 📤 Output: Void (hides loading UI)
     */
    private hideLoadingState(): void {
        const overlay = document.getElementById('loading-overlay');
        if (overlay) {
            overlay.classList.add('hidden');
        }
    }

    /**
     * @Oracode Method: Show Message
     * 🎯 Purpose: Display user feedback messages
     * 📥 Input: Message text and type
     * 📤 Output: Void (shows message)
     */
    private showMessage(message: string, type: 'success' | 'error' | 'info' = 'info'): void {
        if (window.showToast) {
            window.showToast(message, type);
        } else if (window.Swal) {
            // Custom SweetAlert toast for personal data form - positioned lower to avoid covering welcome message
            window.Swal.fire({
                toast: true,
                position: 'top-start',
                icon: type === 'success' ? 'success' : type === 'error' ? 'error' : 'info',
                title: message,
                showConfirmButton: false,
                timer: type === 'error' ? 8000 : 5000,
                timerProgressBar: false, // Disabilitato temporaneamente per evitare errore SVG
                customClass: {
                    popup: 'personal-data-toast' // Custom class for this specific toast
                },
                willOpen: () => {
                    // Applica il CSS prima che il toast diventi visibile per evitare l'effetto "salto"
                    const toastElement = document.querySelector('.personal-data-toast') as HTMLElement;
                    if (toastElement) {
                        toastElement.style.transform = 'translateY(120px)';
                        toastElement.style.zIndex = '9999';
                    }
                }
            });
        } else {
            // Fallback message display
            const container = document.getElementById('error-container');
            if (container) {
                const messageEl = document.createElement('div');
                messageEl.className = `${type}-message`;
                messageEl.textContent = message;

                container.appendChild(messageEl);

                setTimeout(() => {
                    messageEl.remove();
                }, 5000);
            } else {
                alert(message); // Ultimate fallback
            }
        }
    }

    /**
     * @Oracode Method: Handle Error with UEM Integration
     * 🎯 Purpose: Process errors through UEM system
     * 📥 Input: Error code and error object
     * 📤 Output: Void (handles error appropriately)
     */
    private handleError(errorCode: string, error: Error): void {
        console.error(`[Personal Data] ${errorCode}:`, error);

        if (window.UEM) {
            window.UEM.handleClientError(errorCode, {
                message: error.message,
                stack: error.stack,
                context: 'PersonalDataManager',
                userAgent: navigator.userAgent,
                timestamp: new Date().toISOString()
            });
        } else {
            // Fallback error handling
            this.showMessage('An unexpected error occurred. Please try again.', 'error');
        }
    }
}

/**
 * @Oracode Class: Shipping Modal Manager (OS1-Compliant)
 * 🎯 Purpose: Handle Shipping Address Modal via Vanilla JS (No Alpine)
 * 🛡️ Enterprise: Strict DOM manipulation, no eval, explicit state management
 */
class ShippingModalManager {
    private modal: HTMLElement | null;
    private backdrop: HTMLElement | null;
    private form: HTMLFormElement | null;
    private closeButtons: NodeListOf<Element>;
    private titleElement: HTMLElement | null;

    constructor() {
        this.modal = document.getElementById('shipping-address-modal');
        this.backdrop = document.getElementById('shipping-modal-backdrop');
        this.form = document.getElementById('shipping-address-form') as HTMLFormElement;
        this.titleElement = document.getElementById('shipping-modal-title');
        this.closeButtons = document.querySelectorAll('[data-action="close-shipping-modal"]');

        this.init();
    }

    private init(): void {
        if (!this.modal || !this.form) return;

        // Open Triggers (Delegation not needed if buttons exist, but safer to delegate if dynamic)
        document.addEventListener('click', (e) => {
            const target = (e.target as Element).closest('[data-action="open-shipping-modal"]');
            if (target) {
                const url = target.getAttribute('data-url');
                const method = target.getAttribute('data-method') || 'POST';
                const payload = target.getAttribute('data-payload');

                if (url) {
                    this.open(url, method, payload ? JSON.parse(payload) : null);
                }
            }
        });

        // Close Triggers
        if (this.backdrop) {
            this.backdrop.addEventListener('click', () => this.close());
        }

        this.closeButtons.forEach(btn => {
            btn.addEventListener('click', () => this.close());
        });
    }

    public open(url: string, method: string, data: any = null): void {
        if (!this.modal || !this.form) return;

        // 1. Reset Form
        this.form.reset();

        // 2. Set Action and Method
        this.form.action = url;

        // Handle Method Spoofing (Laravel _method)
        let methodInput = this.form.querySelector('input[name="_method"]') as HTMLInputElement;
        if (!methodInput) {
            methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            this.form.appendChild(methodInput);
        }
        methodInput.value = method;

        // 3. Update Title
        if (this.titleElement) {
            // We use generic texts or the one passed via data attribute could be better, 
            // but for now simple logic based on method
            // Ideally texts are passed via config.translations
            // We'll rely on current DOM state or config if available
            this.titleElement.textContent = method === 'PUT'
                ? ((window.personalDataConfig as any)?.translations['shipping_edit_address'] || 'Edit Address')
                : ((window.personalDataConfig as any)?.translations['shipping_add_new'] || 'Add New Address');
        }

        // 4. Fill Data if Edit
        if (data) {
            this.fillForm(data);
        }

        // 5. Show Modal
        this.modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden'); // Prevent background scroll
    }

    public close(): void {
        if (!this.modal) return;
        this.modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    private fillForm(data: any): void {
        if (!this.form) return;

        // Map data keys to input names
        // data keys: address_line_1, city, etc.
        // inputs: name="address_line_1", etc.

        Object.keys(data).forEach(key => {
            const input = this.form!.querySelector(`[name="${key}"]`) as HTMLInputElement | HTMLSelectElement;
            if (input) {
                if (input.type === 'checkbox') {
                    (input as HTMLInputElement).checked = !!data[key];
                } else {
                    input.value = data[key] || '';
                }
            }
        });
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    if (window.personalDataConfig) {
        // Initialize Personal Data Manager ONLY if form exists (allows reusing this script)
        if (document.getElementById('personal-data-form') || document.querySelector('[data-action="save-personal-data"]')) {
            new PersonalDataManager(window.personalDataConfig as unknown as PersonalDataManagerConfig);
            console.log('[Personal Data] Main Manager initialized');
        }

        // Initialize Shipping Modal Manager (Always init if config exists, for reuse in Mint/Checkout)
        new ShippingModalManager();
        console.log('[Personal Data] Shipping Modal Manager initialized');

    } else {
        // Graceful degradation or error
        console.warn('[Personal Data] Configuration not found - verify window.personalDataConfig');
    }
});
