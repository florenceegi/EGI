<?php

/**
 * @Oracode Translation File: Personal Data Management - English
 * 🎯 Purpose: Complete English translations for GDPR-compliant personal data management
 * 🛡️ Privacy: GDPR-compliant notices, consent language, data subject rights
 * 🌐 i18n: Base language file for FlorenceEGI personal data domain
 * 🧱 Core Logic: Supports all personal data CRUD operations with privacy notices
 * ⏰ MVP: Critical for English market compliance and user trust
 *
 * @package Lang\En
 * @author Padmin D. Curtis (AI Partner OS1-Compliant)
 * @version 1.0.0 (FlorenceEGI MVP - GDPR Native)
 * @deadline 2025-06-30
 */

return [
    // PAGE TITLES AND HEADERS
    'management_title' => 'Personal Data Management',
    'management_subtitle' => 'Manage your personal data in GDPR compliance',
    'edit_title' => 'Edit Personal Data',
    'edit_subtitle' => 'Securely update your personal information',
    'export_title' => 'Export Personal Data',
    'export_subtitle' => 'Download a complete copy of your personal data',
    'deletion_title' => 'Data Deletion Request',
    'deletion_subtitle' => 'Request permanent deletion of your personal data',

    // --- General & Utility ---
    'read_only_notice' => 'This data is read-only. Upgrade your account to edit.',
    'weak_auth_notice' => 'You have limited permissions. Upgrade to unlock all features.',
    'processing_update' => 'Processing update, please wait...',
    'validation_checking' => 'Checking your data, please wait...',
    'validation_status' => 'Validation Status',
    'validation_info_title' => 'Data Validation & Help',
    'validation_country_detected' => 'Detected country based on your profile',
    'help_resources' => 'Help & Resources',
    'documentation' => 'Documentation',
    'privacy_policy' => 'Privacy Policy',
    'contact_support' => 'Contact DPO or Support',

    // --- General ---
    'anonymous_user' => 'Anonymous User',

    // --- Deletion ---
    'delete_account' => 'Delete Account',
    'delete_account_description' => 'Permanently delete your account and all personal data.',
    'deletion_not_available' => 'Account deletion is currently unavailable.',
    'request_deletion' => 'Request Deletion',

    // --- Upgrade Auth ---
    'upgrade_for_full_access' => 'Upgrade for Full Access',
    'upgrade_description' => 'Upgrade your authentication to manage all personal data features.',
    'upgrade_account' => 'Upgrade Account',

    // --- GDPR Notices/Sections ---
    'gdpr_actions_title' => 'GDPR Quick Actions',
    'last_updated' => 'Last updated',
    'gdpr_requirements' => 'GDPR Requirements',
    'gdpr_req_consent' => 'Consent required for data processing',
    'gdpr_req_purposes' => 'Must specify processing purposes',
    'gdpr_req_accuracy' => 'Data must be accurate and up-to-date',

    // --- Section Headers ---
    'format' => 'Format',
    'length' => 'Length',
    'characters' => 'characters',

    // --- GDPR Consent Status ---
    'consent_given' => 'Consent Given',
    'consent_review_required' => 'Consent Review Required',
    'processing_purposes' => 'Processing Purposes',

    // --- Purposes (checkboxes) ---
    'purpose_account_management' => 'Account Management',
    'purpose_service_delivery' => 'Service Delivery',
    'purpose_legal_compliance' => 'Legal Compliance',
    'purpose_marketing' => 'Marketing',
    'purpose_analytics' => 'Analytics',
    'purpose_customer_support' => 'Customer Support',

    // --- Real-time Validation ---
    'validation_checking' => 'Validating your data…',

    // --- Other UI bits ---
    'cancel_changes' => 'Cancel Changes',
    'read_only_notice' => 'Your data is read-only. Please upgrade your account for editing.',
    'processing_update' => 'Processing, please wait...',

    // --- Data Summary Section ---
    'data_summary_title' => 'Personal Data Summary',
    'data_completeness' => 'Data Completeness',
    'profile_complete' => 'Your profile is complete!',
    'profile_partial' => 'Your profile is partially complete.',
    'profile_incomplete' => 'Your profile is incomplete. Fill all required fields to improve your security and access.',
    'complete' => 'Complete',
    'missing' => 'Missing',
    'partial' => 'Partial',
    'available' => 'Available',
    'not_provided' => 'Not Provided',
    'validated' => 'Validated',
    'pending' => 'Pending Validation',
    'gdpr_consent' => 'GDPR Consent',


    // FORM SECTIONS
    'basic_information' => 'Basic Information',
    'basic_description' => 'Key identification data',
    'fiscal_information' => 'Fiscal Information',
    'fiscal_description' => 'Tax code and data for compliance',
    'address_information' => 'Address Information',
    'address_description' => 'Residence and domicile address',
    'contact_information' => 'Contact Information',
    'contact_description' => 'Phone and other contact details',
    'identity_verification' => 'Identity Verification',
    'identity_description' => 'Verify your identity for sensitive changes',
    'manage_consent' => 'Manage Consent',
    'manage_consent_action' => 'Manage your consent',
    'activity_log' => 'Activity Log',
    'activity_log_description' => 'View all changes and access to your personal data',
    'view_activity_log' => 'View Activity Log',

    // FORM FIELDS
    'first_name' => 'First Name',
    'first_name_placeholder' => 'Enter your first name',
    'last_name' => 'Last Name',
    'last_name_placeholder' => 'Enter your last name',
    'birth_date' => 'Date of Birth',
    'birth_date_placeholder' => 'Select your date of birth',
    'birth_place' => 'Place of Birth',
    'birth_place_placeholder' => 'City and province of birth',
    'gender' => 'Gender',
    'gender_male' => 'Male',
    'gender_female' => 'Female',
    'gender_other' => 'Other',
    'gender_prefer_not_say' => 'Prefer not to say',

    // Fiscal Fields
    'tax_code' => 'Tax Code',
    'tax_code_placeholder' => 'RSSMRA80A01H501X',
    'tax_code_help' => 'Your Italian tax code (16 characters)',
    'id_card_number' => 'Identity Card Number',
    'id_card_number_placeholder' => 'Identity document number',
    'passport_number' => 'Passport Number',
    'passport_number_placeholder' => 'Passport number (if available)',
    'driving_license' => 'Driving License',
    'driving_license_placeholder' => 'Driving license number',

    // Address Fields
    'street_address' => 'Street Address',
    'street_address_placeholder' => 'Street, house number',
    'city' => 'City',
    'city_placeholder' => 'City name',
    'postal_code' => 'Postal Code',
    'postal_code_placeholder' => '00100',
    'province' => 'Province',
    'province_placeholder' => 'Province code (e.g. RM)',
    'region' => 'Region',
    'region_placeholder' => 'Region name',
    'country' => 'Country',
    'country_placeholder' => 'Select country',

    // Contact Fields
    'phone' => 'Phone',
    'phone_placeholder' => '+39 123 456 7890',
    'mobile' => 'Mobile',
    'mobile_placeholder' => '+39 123 456 7890',
    'emergency_contact' => 'Emergency Contact',
    'emergency_contact_placeholder' => 'Name and phone',

    // PRIVACY AND CONSENT
    'consent_management' => 'Consent Management',
    'consent_description' => 'Manage your consent for data processing',
    'consent_required' => 'Required Consent',
    'consent_optional' => 'Optional Consent',
    'consent_marketing' => 'Marketing and Communications',
    'consent_marketing_desc' => 'Consent to receive marketing communications',
    'consent_profiling' => 'Profiling',
    'consent_profiling_desc' => 'Consent for profiling and analytics',
    'consent_analytics' => 'Analytics',
    'consent_analytics_desc' => 'Consent for anonymized statistical analysis',
    'consent_third_party' => 'Third Parties',
    'consent_third_party_desc' => 'Consent for sharing with selected partners',

    // ACTIONS AND BUTTONS
    'update_data' => 'Update Data',
    'save_changes' => 'Save Changes',
    'cancel_changes' => 'Cancel',
    'export_data' => 'Export Data',
    'request_deletion' => 'Request Deletion',
    'verify_identity' => 'Verify Identity',
    'confirm_changes' => 'Confirm Changes',
    'back_to_profile' => 'Back to Profile',

    // SUCCESS AND ERROR MESSAGES
    'update_success' => 'Personal data updated successfully',
    'update_error' => 'Error updating personal data',
    'validation_error' => 'Some fields contain errors. Please check and try again.',
    'identity_verification_required' => 'Identity verification required for this action',
    'identity_verification_failed' => 'Identity verification failed. Please try again.',
    'export_started' => 'Data export started. You will receive an email when it is ready.',
    'export_ready' => 'Your data export is ready for download',
    'deletion_requested' => 'Deletion request submitted. It will be processed within 30 days.',

    // --- Export & Rate Limits ---
    'export_description' => 'Download a complete copy of your personal data.',
    'export_now' => 'Export Now',
    'export_rate_limit' => 'Please wait before requesting another export.',
    'export_ready' => 'Your data export is ready for download',

    // VALIDATION MESSAGES
    'validation' => [
        'first_name_required' => 'First name is required',
        'last_name_required' => 'Last name is required',
        'birth_date_required' => 'Date of birth is required',
        'birth_date_valid' => 'Date of birth must be valid',
        'birth_date_age' => 'You must be at least 13 years old to register',
        'tax_code_invalid' => 'Tax code is not valid',
        'tax_code_format' => 'Tax code must be 16 characters',
        'phone_invalid' => 'Phone number is not valid',
        'postal_code_invalid' => 'Postal code is not valid for the selected country',
        'country_required' => 'Country is required',
    ],

    // GDPR NOTICES
    'gdpr_notices' => [
        'data_processing_info' => 'Your personal data is processed in accordance with GDPR (EU) 2016/679',
        'data_controller' => 'Data controller: FlorenceEGI S.r.l.',
        'data_purpose' => 'Purpose: User account management and platform services',
        'data_retention' => 'Retention: Data is kept for as long as necessary for requested services',
        'data_rights' => 'Rights: You can access, rectify, delete or restrict the processing of your data',
        'data_contact' => 'To exercise your rights contact: privacy@florenceegi.com',
        'sensitive_data_warning' => 'Warning: You are editing sensitive data. Identity verification is required.',
        'audit_notice' => 'All changes to personal data are logged for security',
    ],

    // EXPORT FUNCTIONALITY
    'export' => [
        'formats' => [
            'json' => 'JSON (Machine Readable)',
            'pdf' => 'PDF (Human Readable)',
            'csv' => 'CSV (Spreadsheet)',
        ],
        'categories' => [
            'basic' => 'Basic Information',
            'fiscal' => 'Fiscal Data',
            'address' => 'Address Data',
            'contact' => 'Contact Information',
            'consents' => 'Consents and Preferences',
            'audit' => 'Change Log',
        ],
        'select_format' => 'Select export format',
        'select_categories' => 'Select categories to export',
        'generate_export' => 'Generate Export',
        'download_ready' => 'Download Ready',
        'download_expires' => 'Download link expires in 7 days',
    ],

    // DELETION WORKFLOW
    'deletion' => [
        'confirm_title' => 'Confirm Data Deletion',
        'warning_irreversible' => 'WARNING: This action is irreversible',
        'warning_account' => 'Deleting data will permanently close your account',
        'warning_backup' => 'Data may be retained in backups for up to 90 days',
        'reason_required' => 'Reason for request (optional)',
        'reason_placeholder' => 'You can specify the reason for deletion...',
        'final_confirmation' => 'I confirm I want to permanently delete my personal data',
        'type_delete' => 'Type "DELETE" to confirm',
        'submit_request' => 'Submit Deletion Request',
        'request_submitted' => 'Deletion request submitted successfully',
        'processing_time' => 'Request will be processed within 30 business days',
    ],

    // NAVIGATION
    'quick_navigation' => 'Quick Navigation',
    'go_to_organization_data' => 'Go to Organization Data',

    // ===================================================================
    // IBAN MANAGEMENT
    // ===================================================================
    'iban_management' => 'IBAN Management',
    'iban_description' => 'Configure your IBAN to receive payments in Euro',
    'manage_iban' => 'Manage IBAN',

    // ===================================================================
    // SHIPPING ADDRESSES
    // ===================================================================
    'shipping' => [
        'title' => 'Shipping Addresses',
        'add_new' => 'Add New Address',
        'add_address' => 'Add Address',
        'edit_address' => 'Edit Address',
        'select_address' => 'Select an address for delivery:',
        'no_address' => 'No saved shipping address found.',
    ],
    'address_created_success' => 'Shipping address added successfully',
    'address_updated_success' => 'Shipping address updated successfully',
    'address_deleted_success' => 'Shipping address deleted',
    'address_default_success' => 'Default address set',
    // ===================================================================
    // SHIPPING ADDRESS SECTION
    // ===================================================================
    'shipping_address' => [
        'main' => 'Shipping Details',
        'select_for_delivery' => 'Select an address for physical goods delivery:',
        'add_another' => 'Add another address',
        'no_addresses_warning' => 'You haven\'t saved a shipping address yet.',
        'add_first' => 'Add Address',
        'reload_after_add' => 'After adding the address, reload this page.',
    ],
];
