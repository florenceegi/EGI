<?php
// resources/lang/en/gdpr.php

return [
    /*
    |--------------------------------------------------------------------------
    | GDPR Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used for GDPR-related functionality.
    |
    */

    // General
    'gdpr' => 'GDPR',
    'gdpr_center' => 'GDPR Data Control Center',
    'dashboard' => 'Dashboard',
    'back_to_dashboard' => 'Back to Dashboard',
    'save' => 'Save',
    'submit' => 'Submit',
    'cancel' => 'Cancel',
    'continue' => 'Continue',
    'loading' => 'Loading...',
    'success' => 'Success',
    'error' => 'Error',
    'warning' => 'Warning',
    'info' => 'Information',
    'enabled' => 'Enabled',
    'disabled' => 'Disabled',
    'active' => 'Active',
    'inactive' => 'Inactive',
    'pending' => 'Pending',
    'completed' => 'Completed',
    'failed' => 'Failed',
    'processing' => 'Processing',
    'retry' => 'Retry',
    'required_field' => 'Required field',
    'required_consent' => 'Required consent',
    'select_all_categories' => 'Select all categories',
    'no_categories_selected' => 'No categories selected',
    'compliance_badge' => 'Compliance badge',

    // Breadcrumb
    'breadcrumb' => [
        'dashboard' => 'Dashboard',
        'gdpr' => 'Privacy and GDPR',
    ],

    // Alerts
    'alerts' => [
        'success' => 'Operation completed!',
        'error' => 'Error:',
        'warning' => 'Warning:',
        'info' => 'Information:',
    ],

    // Menu Items
    'menu' => [
        'gdpr_center' => 'GDPR Data Control Center',
        'consent_management' => 'Consent Management',
        'data_export' => 'Export My Data',
        'processing_restrictions' => 'Restrict Data Processing',
        'delete_account' => 'Delete My Account',
        'breach_report' => 'Report a Data Breach',
        'activity_log' => 'My GDPR Activity Log',
        'privacy_policy' => 'Privacy Policy',
    ],

    // Consent Status
    'status' => [
        'granted' => 'Granted',
        'denied' => 'Denied',
        'active' => 'Active',
        'withdrawn' => 'Withdrawn',
        'expired' => 'Expired',
        'pending' => 'Pending',
        'in_progress' => 'In progress',
        'completed' => 'Completed',
        'failed' => 'Failed',
        'rejected' => 'Rejected',
        'verification_required' => 'Verification required',
        'cancelled' => 'Cancelled',
    ],

    // Consent Management
    'consent' => [
        'title' => 'Manage Your Consent Preferences',
        'description' => 'Control how your data is used within our platform. You can update your preferences at any time.',
        'update_success' => 'Your consent preferences have been updated.',
        'update_error' => 'An error occurred while updating your consent preferences. Please try again.',
        'save_all' => 'Save All Preferences',
        'last_updated' => 'Last updated:',
        'never_updated' => 'Never updated',
        'privacy_notice' => 'Privacy Notice',
        'not_given' => 'Not Provided',
        'given_at' => 'Provided on',
        'your_consents' => 'Your Consents',
        'subtitle' => 'Manage your privacy preferences and view the status of your consents.',
        'breadcrumb' => 'Consents',
        'history_title' => 'Consent History',
        'back_to_consents' => 'Back to Consents',
        'preferences_title' => 'Consent Preferences Management',
        'preferences_subtitle' => 'Configure your detailed privacy preferences',
        'preferences_breadcrumb' => 'Preferences',
        'preferences_info_title' => 'Granular Consent Management',
        'preferences_info_description' => 'Here you can configure each type of consent in detail...',
        'required' => 'Required',
        'optional' => 'Optional',
        'toggle_label' => 'Enable/Disable',
        'always_enabled' => 'Always Enabled',
        'benefits_title' => 'Benefits for You',
        'consequences_title' => 'If You Disable',
        'third_parties_title' => 'Third-Party Services',
        'save_preferences' => 'Save Preferences',
        'back_to_overview' => 'Back to Overview',
        'never_updated' => 'Never updated',

        // Consent Details
        'given_at' => 'Provided on',
        'withdrawn_at' => 'Withdrawn on',
        'not_given' => 'Not provided',
        'method' => 'Method',
        'version' => 'Version',
        'unknown_version' => 'Unknown version',

        // Actions
        'withdraw' => 'Withdraw Consent',
        'withdraw_confirm' => 'Are you sure you want to withdraw this consent? This action may limit some functionalities.',
        'renew' => 'Renew Consent',
        'view_history' => 'View History',

        // Empty States
        'no_consents' => 'No Consents Present',
        'no_consents_description' => 'You have not yet provided any consent for data processing. You can manage your preferences using the button below.',

        // Preference Management
        'manage_preferences' => 'Manage Your Preferences',
        'update_preferences' => 'Update Privacy Preferences',

        // Summary Dashboard
        'summary' => [
            'active' => 'Active Consents',
            'total' => 'Total Consents',
            'compliance' => 'Compliance Score',
        ],

        // Consent Methods
        'methods' => [
            'web' => 'Web Interface',
            'api' => 'API',
            'import' => 'Import',
            'admin' => 'Administrator',
        ],

        // Consent Purposes
        'purposes' => [
            'functional' => 'Functional Consents',
            'analytics' => 'Analytics Consents',
            'marketing' => 'Marketing Consents',
            'profiling' => 'Profiling Consents',
            'collaboration_participation' => 'Collaboration Participation',
        ],

        // Consent Descriptions
        'descriptions' => [
            'functional' => 'Necessary for the basic functioning of the platform and to provide requested services.',
            'analytics' => 'Used to analyze site usage and improve user experience.',
            'marketing' => 'Used to send you promotional communications and personalized offers.',
            'profiling' => 'Used to create personalized profiles and suggest relevant content.',
            'collaboration_participation' => 'Allow participation in collaborative projects and shared activities with other platform users.',
        ],

        'essential' => [
            'label' => 'Essential Cookies',
            'description' => 'These cookies are necessary for the website to function and cannot be disabled in our systems.',
        ],
        'functional' => [
            'label' => 'Functional Cookies',
            'description' => 'These cookies enable the website to provide enhanced functionality and personalization.',
        ],
        'analytics' => [
            'label' => 'Analytics Cookies',
            'description' => 'These cookies allow us to count visits and traffic sources to measure and improve our site’s performance.',
        ],
        'marketing' => [
            'label' => 'Marketing Cookies',
            'description' => 'These cookies may be set through our site by our advertising partners to build a profile of your interests.',
        ],
        'profiling' => [
            'label' => 'Profiling',
            'description' => 'We use profiling to better understand your preferences and personalize our services to your needs.',
        ],
        'collaboration_participation' => [
            'label' => 'Collaboration Participation',
            'description' => 'Consent to participate in collections collaboration, data sharing, and collaborative activities.',
        ],
        'saving_consent' => 'Saving...',
        'consent_saved' => 'Saved',
        'saving_all_consents' => 'Saving all preferences...',
        'all_consents_saved' => 'All consent preferences have been successfully saved.',
        'all_consents_save_error' => 'An error occurred while saving all consent preferences.',
        'consent_save_error' => 'An error occurred while saving this consent preference.',

        // Processing Purposes
        'processing_purposes' => [
            'functional' => 'Essential platform operations: authentication, security, service delivery, user preference storage',
            'analytics' => 'Platform improvement: usage analysis, performance monitoring, user experience optimization',
            'marketing' => 'Communication: newsletters, product updates, promotional offers, event notifications',
            'profiling' => 'Personalization: content recommendations, user behavior analysis, targeted suggestions',
            'collaboration_participation' => 'Collaboration: data sharing within collections, collaborative activities, notifications',
        ],

        // Retention Periods
        'retention_periods' => [
            'functional' => 'Account duration + 1 year for legal compliance',
            'analytics' => '2 years from last activity',
            'marketing' => '3 years from last interaction or consent withdrawal',
            'profiling' => '1 year from last activity or consent withdrawal',
            'collaboration_participation' => 'Duration of collaboration + 30 days',
        ],

        // User Benefits
        'user_benefits' => [
            'functional' => [
                'Secure access to your account',
                'Customized user settings',
                'Reliable platform performance',
                'Protection against fraud and abuse',
            ],
            'analytics' => [
                'Improved platform performance',
                'Optimized user experience design',
                'Faster loading times',
                'Enhanced feature development',
            ],
            'marketing' => [
                'Relevant product updates',
                'Exclusive offers and promotions',
                'Event invitations and announcements',
                'Educational content and tips',
            ],
            'profiling' => [
                'Personalized content recommendations',
                'Tailored user experience',
                'Relevant project suggestions',
                'Customized dashboard and features',
            ],
        ],

        // Third Parties
        'third_parties' => [
            'functional' => [
                'CDN providers (static content delivery)',
                'Security services (fraud prevention)',
                'Infrastructure providers (hosting)',
            ],
            'analytics' => [
                'Analytics platforms (anonymized usage data)',
                'Performance monitoring services',
                'Error tracking services',
            ],
            'marketing' => [
                'Email service providers',
                'Marketing automation platforms',
                'Social media platforms (for advertising)',
            ],
            'profiling' => [
                'Recommendation engines',
                'Behavioral analysis services',
                'Content personalization platforms',
            ],
        ],

        // Withdrawal Consequences
        'withdrawal_consequences' => [
            'functional' => [
                'Cannot be withdrawn - essential for platform operation',
                'Account access would be compromised',
                'Security features would be disabled',
            ],
            'analytics' => [
                'Platform improvements may not reflect your usage patterns',
                'Generic experience instead of optimized performance',
                'No impact on core functionalities',
            ],
            'marketing' => [
                'No promotional emails or updates',
                'You may miss important announcements',
                'No impact on platform functionality',
                'Can be reactivated at any time',
            ],
            'profiling' => [
                'Generic content instead of personalized recommendations',
                'Standard dashboard layout',
                'Less relevant project suggestions',
                'No impact on core platform functionalities',
            ],
        ],
    ],

    // Data Export
    'export' => [
        'title' => 'Export Your Data',
        'description' => 'Request a copy of your personal data. Processing may take a few minutes.',
        'request_button' => 'Request Data Export',
        'format' => 'Export Format',
        'format_json' => 'JSON (recommended for developers)',
        'format_csv' => 'CSV (spreadsheet compatible)',
        'format_pdf' => 'PDF (readable document)',
        'include_metadata' => 'Include metadata',
        'include_timestamps' => 'Include timestamps',
        'password_protection' => 'Protect export with password',
        'password' => 'Export password',
        'confirm_password' => 'Confirm password',
        'data_categories' => 'Data categories to export',
        'request_success' => 'Your data export request has been submitted.',
        'request_error' => 'An error occurred while requesting data export. Please try again.',
        'recent_exports' => 'Recent Exports',
        'no_recent_exports' => 'You have no recent exports.',
        'export_status' => 'Export Status',
        'export_date' => 'Export Date',
        'export_size' => 'Export Size',
        'export_id' => 'Export ID',
        'download' => 'Download',
        'download_export' => 'Download Export',
        'export_preparing' => 'Preparing your data export...',
        'export_queued' => 'Your export is queued and will start soon...',
        'export_processing' => 'Processing your data export...',
        'export_ready' => 'Your data export is ready for download.',
        'export_failed' => 'Your data export failed.',
        'export_failed_details' => 'An error occurred while processing your data export. Please try again or contact support.',
        'export_unknown_status' => 'Export status unknown.',
        'check_status' => 'Check Status',
        'retry_export' => 'Retry Export',
        'export_download_error' => 'An error occurred while downloading your export.',
        'export_status_error' => 'Error checking export status.',
        'categories' => [
            'profile' => 'Profile Information',
            'account' => 'Account Details',
            'preferences' => 'Preferences and Settings',
            'activity' => 'Activity History',
            'consents' => 'Consent History',
            'collections' => 'Collections and Content',
            'purchases' => 'Purchases and Transactions',
            'comments' => 'Comments and Reviews',
            'messages' => 'Messages and Communications',
        ],
        'limit_reached' => 'You have reached the maximum number of exports allowed per day.',
        'existing_in_progress' => 'You already have an export in progress. Please wait until it is completed.',
    ],

    // Processing Restrictions
    'restriction' => [
        'title' => 'Restrict Data Processing',
        'description' => 'You can request to restrict how we process your data under certain circumstances.',
        'active_restrictions' => 'Active Restrictions',
        'no_active_restrictions' => 'You have no active processing restrictions.',
        'request_new' => 'Request New Restriction',
        'restriction_type' => 'Restriction Type',
        'restriction_reason' => 'Restriction Reason',
        'data_categories' => 'Data Categories',
        'notes' => 'Additional Notes',
        'notes_placeholder' => 'Provide any additional details to help us understand your request...',
        'submit_button' => 'Submit Restriction Request',
        'remove_button' => 'Remove Restriction',
        'processing_restriction_success' => 'Your processing restriction request has been submitted.',
        'processing_restriction_failed' => 'An error occurred while submitting your processing restriction request.',
        'processing_restriction_system_error' => 'A system error occurred while processing your request.',
        'processing_restriction_removed' => 'The processing restriction has been removed.',
        'processing_restriction_removal_failed' => 'An error occurred while removing the processing restriction.',
        'unauthorized_action' => 'You are not authorized to perform this action.',
        'date_submitted' => 'Submission Date',
        'expiry_date' => 'Expires on',
        'never_expires' => 'Never Expires',
        'status' => 'Status',
        'limit_reached' => 'You have reached the maximum number of active restrictions allowed.',
        'categories' => [
            'profile' => 'Profile Information',
            'activity' => 'Activity Tracking',
            'preferences' => 'Preferences and Settings',
            'collections' => 'Collections and Content',
            'purchases' => 'Purchases and Transactions',
            'comments' => 'Comments and Reviews',
            'messages' => 'Messages and Communications',
        ],
        'types' => [
            'processing' => 'Restrict All Processing',
            'automated_decisions' => 'Restrict Automated Decisions',
            'marketing' => 'Restrict Marketing Processing',
            'analytics' => 'Restrict Analytics Processing',
            'third_party' => 'Restrict Third-Party Sharing',
            'profiling' => 'Restrict Profiling',
            'data_sharing' => 'Restrict Data Sharing',
            'removed' => 'Remove Restriction',
            'all' => 'Restrict All Processing',
        ],
        'reasons' => [
            'accuracy_dispute' => 'I dispute the accuracy of my data',
            'processing_unlawful' => 'The processing is unlawful',
            'no_longer_needed' => 'You no longer need my data, but I need it for legal claims',
            'objection_pending' => 'I have objected to processing and am awaiting verification',
            'legitimate_interest' => 'Compelling legitimate reasons',
            'legal_claims' => 'For the defense of legal claims',
            'other' => 'Other reason (specify in notes)',
        ],
        'descriptions' => [
            'processing' => 'Restrict the processing of your personal data pending verification of your request.',
            'automated_decisions' => 'Restrict automated decisions that may affect your rights.',
            'marketing' => 'Restrict the processing of your data for direct marketing purposes.',
            'analytics' => 'Restrict the processing of your data for analytics and tracking purposes.',
            'third_party' => 'Restrict the sharing of your data with third parties.',
            'profiling' => 'Restrict the profiling of your personal data.',
            'data_sharing' => 'Restrict the sharing of your data with other services or platforms.',
            'all' => 'Restrict all forms of processing of your personal data.',
        ],
    ],

    // Account Deletion
    'deletion' => [
        'title' => 'Delete My Account',
        'description' => 'This will initiate the process to delete your account and all associated data.',
        'warning' => 'Warning: Account deletion is permanent and cannot be undone.',
        'processing_delay' => 'Your account will be scheduled for deletion in :days days.',
        'confirm_deletion' => 'I understand that this action is permanent and cannot be undone.',
        'password_confirmation' => 'Enter your password to confirm',
        'reason' => 'Reason for deletion (optional)',
        'additional_comments' => 'Additional comments (optional)',
        'submit_button' => 'Request Account Deletion',
        'request_submitted' => 'Your account deletion request has been submitted.',
        'request_error' => 'An error occurred while submitting your account deletion request.',
        'pending_deletion' => 'Your account is scheduled for deletion on :date.',
        'cancel_deletion' => 'Cancel Deletion Request',
        'cancellation_success' => 'Your account deletion request has been cancelled.',
        'cancellation_error' => 'An error occurred while cancelling your account deletion request.',
        'reasons' => [
            'no_longer_needed' => 'I no longer need this service',
            'privacy_concerns' => 'Privacy concerns',
            'moving_to_competitor' => 'Moving to another service',
            'unhappy_with_service' => 'Unsatisfied with the service',
            'other' => 'Other reason',
        ],
        'confirmation_email' => [
            'subject' => 'Confirm Account Deletion Request',
            'line1' => 'We have received your request to delete your account.',
            'line2' => 'Your account is scheduled for deletion on :date.',
            'line3' => 'If you did not request this action, please contact us immediately.',
        ],
        'data_retention_notice' => 'Note that some anonymized data may be retained for legal and analytical purposes.',
        'blockchain_data_notice' => 'Data stored on blockchain cannot be fully deleted due to the immutable nature of the technology.',
    ],

    // Breach Reporting
    'breach' => [
        'title' => 'Report a Data Breach',
        'description' => 'If you believe there has been a breach of your personal data, report it here.',
        'reporter_name' => 'Your Name',
        'reporter_email' => 'Your Email',
        'incident_date' => 'When did the incident occur?',
        'breach_description' => 'Describe the potential breach',
        'breach_description_placeholder' => 'Provide as much detail as possible about the potential data breach...',
        'affected_data' => 'What data do you believe was compromised?',
        'affected_data_placeholder' => 'E.g., personal information, financial data, etc.',
        'discovery_method' => 'How did you discover this potential breach?',
        'supporting_evidence' => 'Supporting Evidence (optional)',
        'upload_evidence' => 'Upload Evidence',
        'file_types' => 'Accepted file types: PDF, JPG, JPEG, PNG, TXT, DOC, DOCX',
        'max_file_size' => 'Maximum file size: 10MB',
        'consent_to_contact' => 'I consent to being contacted regarding this report',
        'submit_button' => 'Submit Breach Report',
        'report_submitted' => 'Your breach report has been submitted.',
        'report_error' => 'An error occurred while submitting your breach report.',
        'thank_you' => 'Thank you for your report',
        'thank_you_message' => 'Thank you for reporting this potential breach. Our data protection team will investigate and may contact you for further information.',
        'breach_description_min' => 'Provide at least 20 characters to describe the potential breach.',
    ],

    // Activity Log
    'activity' => [
        'title' => 'My GDPR Activity Log',
        'description' => 'View a log of all your GDPR-related activities and requests.',
        'no_activities' => 'No activities found.',
        'date' => 'Date',
        'activity' => 'Activity',
        'details' => 'Details',
        'ip_address' => 'IP Address',
        'user_agent' => 'User Agent',
        'download_log' => 'Download Activity Log',
        'filter' => 'Filter Activities',
        'filter_all' => 'All Activities',
        'filter_consent' => 'Consent Activities',
        'filter_export' => 'Data Export Activities',
        'filter_restriction' => 'Processing Restriction Activities',
        'filter_deletion' => 'Account Deletion Activities',
        'types' => [
            'consent_updated' => 'Consent Preferences Updated',
            'data_export_requested' => 'Data Export Requested',
            'data_export_completed' => 'Data Export Completed',
            'data_export_downloaded' => 'Data Export Downloaded',
            'processing_restricted' => 'Processing Restriction Requested',
            'processing_restriction_removed' => 'Processing Restriction Removed',
            'account_deletion_requested' => 'Account Deletion Requested',
            'account_deletion_cancelled' => 'Account Deletion Cancelled',
            'account_deletion_completed' => 'Account Deletion Completed',
            'breach_reported' => 'Data Breach Reported',
        ],
    ],

    // Validation
    'validation' => [
        'consents_required' => 'Consent preferences are required.',
        'consents_format' => 'The format of consent preferences is invalid.',
        'consent_value_required' => 'The consent value is required.',
        'consent_value_boolean' => 'The consent value must be a boolean.',
        'format_required' => 'The export format is required.',
        'data_categories_required' => 'At least one data category must be selected.',
        'data_categories_format' => 'The format of data categories is invalid.',
        'data_categories_min' => 'At least one data category must be selected.',
        'data_categories_distinct' => 'Data categories must be distinct.',
        'export_password_required' => 'A password is required when password protection is enabled.',
        'export_password_min' => 'The password must be at least 8 characters long.',
        'restriction_type_required' => 'The restriction type is required.',
        'restriction_reason_required' => 'The restriction reason is required.',
        'notes_max' => 'Notes cannot exceed 500 characters.',
        'reporter_name_required' => 'Your name is required.',
        'reporter_email_required' => 'Your email is required.',
        'reporter_email_format' => 'Please enter a valid email address.',
        'incident_date_required' => 'The incident date is required.',
        'incident_date_format' => 'The incident date must be a valid date.',
        'incident_date_past' => 'The incident date must be in the past or today.',
        'breach_description_required' => 'The breach description is required.',
        'breach_description_min' => 'The breach description must be at least 20 characters long.',
        'affected_data_required' => 'Information about compromised data is required.',
        'discovery_method_required' => 'The discovery method is required.',
        'supporting_evidence_format' => 'Evidence must be a PDF, JPG, JPEG, PNG, TXT, DOC, or DOCX file.',
        'supporting_evidence_max' => 'The evidence file cannot exceed 10MB.',
        'consent_to_contact_required' => 'Consent to contact is required.',
        'consent_to_contact_accepted' => 'Consent to contact must be accepted.',
        'required_consent_message' => 'This consent is necessary to use the platform.',
        'confirm_deletion_required' => 'You must confirm that you understand the consequences of account deletion.',
        'form_error_title' => 'Correct the errors below',
        'form_error_message' => 'There are one or more errors in the form that need to be corrected.',
    ],

    // Error Messages
    'errors' => [
        'general' => 'An unexpected error occurred.',
        'unauthorized' => 'You are not authorized to perform this action.',
        'forbidden' => 'This action is forbidden.',
        'not_found' => 'The requested resource was not found.',
        'validation_failed' => 'The submitted data is invalid.',
        'rate_limited' => 'Too many requests. Please try again later.',
        'service_unavailable' => 'The service is currently unavailable. Please try again later.',
    ],

    'requests' => [
        'types' => [
            'consent_update' => 'Consent update request submitted.',
            'data_export' => 'Data export request submitted.',
            'processing_restriction' => 'Processing restriction request submitted.',
            'account_deletion' => 'Account deletion request submitted.',
            'breach_report' => 'Data breach report submitted.',
            'erasure' => 'Data erasure request submitted.',
            'access' => 'Data access request submitted.',
            'rectification' => 'Data rectification request submitted.',
            'objection' => 'Objection to processing request submitted.',
            'restriction' => 'Processing restriction request submitted.',
            'portability' => 'Data portability request submitted.',
        ],
    ],

    'modal' => [
        'clarification' => [
            'title' => 'Clarification Required',
            'explanation' => 'To ensure your security, we need to understand the reason for your action:',
        ],
        'revoke_button_text' => 'I changed my mind',
        'revoke_description' => 'You simply want to revoke the previously given consent.',
        'disavow_button_text' => 'I do not recognize this action',
        'disavow_description' => 'You never gave this consent (potential security issue).',

        'confirmation' => [
            'title' => 'Security Protocol Confirmation',
            'warning' => 'This action will trigger a security protocol which includes:',
        ],
        'confirm_disavow' => 'Yes, activate security protocol',
        'final_warning' => 'Proceed only if you are certain you never authorized this action.',

        'consequences' => [
            'consent_revocation' => 'Immediate revocation of consent',
            'security_notification' => 'Notification to the security team',
            'account_review' => 'Possible additional account review',
            'email_confirmation' => 'Confirmation email with instructions',
        ],

        'security' => [
            'title' => 'Security Protocol Activated',
            'understood' => 'I understand',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | GDPR Notifications Section
    |--------------------------------------------------------------------------
    | Moved from `notification.php` for centralization.
    */
    'notifications' => [
        'acknowledged' => 'Acknowledgment recorded.',
        'consent_updated' => [
            'title' => 'Privacy Preferences Updated',
            'content' => 'Your consent preferences have been successfully updated.',
        ],
        'data_exported' => [
            'title' => 'Your Data Export is Ready',
            'content' => 'Your data export request has been processed. You can download the file from the provided link.',
        ],
        'processing_restricted' => [
            'title' => 'Data Processing Restriction Applied',
            'content' => 'We have successfully applied your request to restrict data processing for the category: :type.',
        ],
        'account_deletion_requested' => [
            'title' => 'Account Deletion Request Received',
            'content' => 'We have received your request to delete your account. The process will be completed within :days days. During this period, you can still cancel the request by logging in again.',
        ],
        'account_deletion_processed' => [
            'title' => 'Account Successfully Deleted',
            'content' => 'As per your request, your account and associated data have been permanently deleted from our platform. We’re sorry to see you go.',
        ],
        'breach_report_received' => [
            'title' => 'Breach Report Received',
            'content' => 'Thank you for your report. We have received it with ID #:report_id and our security team is reviewing it.',
        ],
        'status' => [
            'pending_user_confirmation' => 'Waiting for user confirmation',
            'user_confirmed_action' => 'User action confirmed',
            'user_revoked_consent' => 'User revoked action',
            'user_disavowed_suspicious' => 'User disavowed action',
        ],
    ],

    'consent_management' => [
        'title' => 'Consent Management',
        'subtitle' => 'Control how your personal data is used',
        'description' => 'Here you can manage your consent preferences for various purposes and services.',
        'update_preferences' => 'Update your consent preferences',
        'preferences_updated' => 'Your consent preferences have been updated successfully.',
        'preferences_update_error' => 'An error occurred while updating your consent preferences. Please try again.',
    ],

    // Cookie Banner
    'cookie' => [
        'banner' => [
            'title' => 'Cookie Management',
            'description' => 'We use cookies to enhance your browsing experience, provide personalized features, and analyze our traffic. Choose your consent preferences.',
            'privacy_policy_link' => 'Privacy Policy',
            'accept_all' => 'Accept All',
            'reject_optional' => 'Essential Only',
            'customize' => 'Customize',
            'save_preferences' => 'Save Preferences',
            'close_preferences' => 'Close',
            'close' => 'Close',
            'saving' => 'Saving...',
            'required' => 'Required',
        ],
        'categories' => [
            'essential' => [
                'label' => 'Essential Cookies',
                'description' => 'Necessary for basic website functionality. Cannot be disabled.',
            ],
            'functional' => [
                'label' => 'Functional Cookies',
                'description' => 'Enhance user experience with advanced features and personalization.',
            ],
            'analytics' => [
                'label' => 'Analytics Cookies',
                'description' => 'Help us understand how you use the site to improve performance.',
            ],
            'marketing' => [
                'label' => 'Marketing Cookies',
                'description' => 'Used to show you relevant and personalized advertisements.',
            ],
            'profiling' => [
                'label' => 'Profiling Cookies',
                'description' => 'Create a profile of your preferences to personalize content and services.',
            ],
        ],
        'consent_saved_successfully' => 'Your cookie preferences have been saved successfully.',
        'consent_acknowledged' => 'Your cookie preferences have been recorded.',
        'consent_status_error' => 'Unable to load your cookie preferences.',
        'consent_save_error' => 'Error saving cookie preferences.',
        'validation_error' => 'Invalid consent data. Please check your choices.',
    ],
];
