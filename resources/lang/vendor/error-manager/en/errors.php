<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Error Messages - English
    |--------------------------------------------------------------------------
    */

    'dev' => [
        // == Existing Entries ==
        'authentication_error' => 'Unauthenticated access attempt.',
        'ucm_delete_failed' => 'Failed to delete configuration with key :key: :message',
        'undefined_error_code' => 'Undefined error code encountered: :errorCode. Original code was [:_original_code].',
        'invalid_input' => 'Invalid input provided for parameter :param.',
        'invalid_image_structure' => 'The structure of the image file is invalid.',
        'mime_type_not_allowed' => 'The MIME type of the file (:mime) is not allowed.',
        'max_file_size' => 'The file size (:size) exceeds the maximum allowed size (:max_size).',
        'invalid_file_extension' => 'The file has an invalid extension (:extension).',
        'invalid_file_name' => 'Invalid file name received during upload process: :filename.',
        'invalid_file_pdf' => 'The PDF file provided is invalid or corrupted.',
        'virus_found' => 'A virus was detected in the file: :filename.',
        'scan_error' => 'An error occurred during the virus scan for file: :filename.',
        'temp_file_not_found' => 'Temporary file not found at path: :path.',
        'file_not_found' => 'The requested file was not found: :path.',
        'error_getting_presigned_url' => 'An error occurred while retrieving the presigned URL for :object.',
        'error_during_file_upload' => 'An error occurred during the file upload process for :filename.',
        'error_deleting_local_temp_file' => 'Failed to delete the local temporary file: :path.',
        'error_deleting_ext_temp_file' => 'Failed to delete the external temporary file: :path.',
        'unable_to_save_bot_file' => 'Unable to save the file for the bot: :filename.',
        'unable_to_create_directory' => 'Failed to create directory for file upload: :directory.',
        'unable_to_change_permissions' => 'Failed to change permissions for file/directory: :path.',
        'impossible_save_file' => 'It was impossible to save the file: :filename to disk :disk.',
        'error_during_create_egi_record' => 'An error occurred while creating the EGI record in the database.',
        'error_during_file_name_encryption' => 'An error occurred during the file name encryption process.',
        'acl_setting_error' => 'An error occurred while setting the ACL (:acl) for object :object.',
        'imagick_not_available' => 'The Imagick PHP extension is not available or configured correctly.',
        'unexpected_error' => 'An unexpected error occurred in the system. Check logs for details.',
        'generic_server_error' => 'A generic server error occurred. Details: :details',
        'json_error' => 'JSON processing error. Type: :type, Message: :message',
        'fallback_error' => 'An error occurred but no specific error configuration was found for code [:_original_code].',
        'fatal_fallback_failure' => 'FATAL: Fallback configuration missing or invalid. System cannot respond.',
        'ucm_audit_not_found' => 'No audit records found for the given configuration ID: :id.',
        'ucm_duplicate_key' => 'Attempted to create a configuration with a duplicate key: :key.',
        'ucm_create_failed' => 'Failed to create configuration entry: :key. Reason: :reason',
        'ucm_update_failed' => 'Failed to update configuration entry: :key. Reason: :reason',
        'ucm_not_found' => 'Configuration key not found: :key.',
        'invalid_file' => 'Invalid file provided: :reason',
        'invalid_file_validation' => 'File validation failed for field :field. Reason: :reason',
        'error_saving_file_metadata' => 'Failed to save metadata for file ID :file_id. Reason: :reason',
        'server_limits_restrictive' => 'Server limits might be too restrictive. Check :limit_name (:limit_value).',
        'egi_auth_required' => 'Authentication required for EGI upload.',
        'egi_file_input_error' => "Invalid or missing 'file' input. Upload error code: :code",
        'egi_validation_failed' => 'EGI metadata validation failed. Check validation errors in context.',
        'egi_collection_init_error' => 'Critical error initializing default collection for user :user_id.',
        'egi_crypto_error' => 'Failed to encrypt filename: :filename',
        'egi_db_error' => 'Database error processing EGI :egi_id for collection :collection_id.',
        'egi_storage_critical_failure' => 'Critical failure saving EGI :egi_id file to disk(s): :disks',
        'egi_storage_config_error' => "'local' storage disk required for fallback is not configured.",
        'egi_unexpected_error' => 'Unexpected error during EGI processing for file :original_filename. Error: :error.',

        // Image Optimization Developer Error Messages
        'image_optimization_invalid_file' => 'Invalid image file provided for optimization. File: :file_path.',
        'image_optimization_unsupported_format' => 'Unsupported image format: :format. Supported formats: :supported_formats.',
        'image_optimization_processing_failed' => 'Imagick processing failed for file :file_path. Error: :error.',
        'image_optimization_storage_failed' => 'Failed to store optimized image variants. Original file: :original_file, Error: :error.',
        'image_optimization_imagick_unavailable' => 'Imagick extension is not available or properly configured.',
        'image_optimization_conversion_failed' => 'Failed to convert image from :source_format to :target_format. File: :file_path, Error: :error.',
        'image_optimization_variant_creation_failed' => 'Failed to create image variant :variant_type. Dimensions: :dimensions, Error: :error.',
        'image_optimization_file_too_large' => 'Image file exceeds maximum size limit. File size: :file_size, Limit: :max_size.',
        'image_optimization_invalid_dimensions' => 'Invalid image dimensions. Width: :width, Height: :height, Minimum: :min_dimensions.',
        'egi_unauthorized_access' => 'Unauthenticated attempt to access the EGI upload page.',
        'record_not_found_egi_in_reservation_controller' => 'EGI with ID :egi_id not found in the reservation controller.',
        // UI Related Errors (developer messages)
        'egi_page_access_notice' => 'EGI upload page accessed successfully by administrator with ID :user_id.',
        'egi_page_rendering_error' => 'Exception during EGI upload page rendering: :exception_message',
        'egi_update_failed' => 'EGI update failed: :error for user :user_id on EGI :egi_id',
        'egi_delete_failed' => 'EGI deletion failed: :error for user :user_id on EGI :egi_id',
        'egi_trait_delete_failed' => 'EGI trait deletion failed: :error for user :user_id on EGI :egi_id trait :trait_id',
        'egi_not_found' => 'EGI not found with ID :egi_id for user :user_id',
        'trait_not_found' => 'Trait not found with ID :trait_id for EGI :egi_id user :user_id',

        // Traits API errors (developer messages)
        'traits_categories_load_failed' => 'Traits categories loading failed: :error for collection :collection_id user :user_id',
        'traits_unauthorized_access' => 'Unauthorized traits access: user :user_id attempted access to EGI :egi_id owner :owner_id',
        'traits_egi_published' => 'Attempted traits modification on published EGI: user :user_id on EGI :egi_id',
        'traits_no_data_provided' => 'No traits data provided: user :user_id for EGI :egi_id',
        'traits_save_failed' => 'Traits save failed: :error for user :user_id on EGI :egi_id',
        'traits_add_failed' => 'Traits addition failed: :error for user :user_id on EGI :egi_id',

        // Collection CRUD Errors (developer messages)
        'collection_update_failed' => 'Collection update failed: :error for user :user_id on collection :collection_id',
        'collection_delete_failed' => 'Collection deletion failed: :error for user :user_id on collection :collection_id',

        // Validation Related Errors (developer messages)
        'invalid_egi_file' => 'EGI file validation failed with errors: :validation_errors',

        // Processing Related Errors (developer messages)
        'error_during_egi_processing' => 'Error during EGI file processing at stage ":processing_stage": :exception_message',

        // Wallet Related Errors (user messages)
        'wallet_creation_failed' => 'Failed to create wallet for collection :collection_id, user :user_id: :error_message',
        'wallet_quota_check_error' => 'Error checking wallet quota for user :user_id, collection :collection_id: :error_message',
        'wallet_insufficient_quota' => 'User :user_id has insufficient quota for collection :collection_id. Required: mint=:required_mint_quota, rebind=:required_rebind_quota. Available: mint=:current_mint_quota, rebind=:current_rebind_quota.',
        'wallet_address_invalid' => 'Invalid wallet address format provided for user :user_id: :wallet_address',
        'wallet_not_found' => 'Wallet not found for user :user_id and collection :collection_id',
        'wallet_already_exists' => 'Wallet already exists for user :user_id and collection :collection_id with ID :wallet_id',
        'wallet_invalid_secret' => 'Invalid secret key provided for wallet :wallet from IP :ip',
        'wallet_validation_failed' => 'Wallet validation failed. Errors: :errors',
        'wallet_connection_failed' => 'Failed to establish wallet connection. Error: :message',
        'wallet_disconnect_failed' => 'Failed to disconnect wallet. Error: :error',

        // COLLECTION_CREATION_FAILED
        'collection_creation_failed' => 'Failed to create default collection for user :user_id. Error details: :error_details',

        // COLLECTION_FIND_CREATE_FAILED
        'collection_find_create_failed' => 'Failed to find or create collection for user :user_id. Error details: :error_details',

        // User Current Collection Update Errors
        'user_current_collection_update_failed' => 'Critical failure updating current_collection_id for user :user_id to collection :collection_id. Database operation failed: :error_message. This prevents proper user-collection association in FlorenceEGI workflow.',
        'user_current_collection_validation_failed' => 'Validation failed during current collection update for user :user_id and collection :collection_id. Validation type: :validation_type. Error: :validation_error. This indicates data integrity issues that must be resolved.',

        // == New Entries ==
        'authorization_error' => 'Authorization denied for the requested action: :action.',
        'csrf_token_mismatch' => 'CSRF token mismatch detected.',
        'route_not_found' => 'The requested route or resource was not found: :url.',
        'method_not_allowed' => 'HTTP method :method not allowed for this route: :url.',
        'too_many_requests' => 'Too many requests hitting the rate limiter.',
        'database_error' => 'A database query or connection error occurred. Details: :details',
        'record_not_found' => 'The requested database record was not found (Model: :model, ID: :id).',
        'validation_error' => 'Input validation failed. Check context for specific errors.', // Generic dev message
        'utm_load_failed' => 'Failed to load translation file: :file for locale :locale.',
        'utm_invalid_locale' => 'Attempted to use an invalid or unsupported locale: :locale.',
        'uem_email_send_failed' => 'EmailNotificationHandler failed to send notification for :errorCode. Reason: :reason',
        'uem_slack_send_failed' => 'SlackNotificationHandler failed to send notification for :errorCode. Reason: :reason',
        'uem_recovery_action_failed' => 'Recovery action :action failed for error :errorCode. Reason: :reason',
        'user_unauthenticated_access' => 'User unauthenticated: Attempt to access a protected resource without valid authentication. Target Collection ID (if applicable): :target_collection_id. IP: :ip_address.',
        'set_current_collection_forbidden' => 'Forbidden: User ID :user_id attempted to set Collection ID :collection_id as current without authorization. IP: :ip_address.',
        'set_current_collection_failed' => 'Database Error: Failed to update current collection for User ID :user_id to Collection ID :collection_id. Details: :exception_message.',
        'auth_required' => 'Authentication required to perform this action. User not logged in.',
        'auth_required_for_like' => 'User must be authenticated to like items. Current auth status: :status',
        'like_toggle_failed' => 'Failed to toggle like for :resource_type :resource_id. Error: :error',

        // Dev message for reservations sistem
        'reservation_egi_not_available' => 'The EGI with ID :egi_id is not available for reservation. It may be already minted or not published.',
        'reservation_amount_too_low' => 'The offer amount of :amount EUR is below the minimum required for this EGI.',
        'reservation_relaunch_amount_too_low' => 'Invalid relaunch attempt with amount :new_amount EUR. User :user_id already has a reservation of :previous_amount EUR for EGI :egi_id.',
        'reservation_unauthorized' => 'Unauthorized attempt to reserve EGI :egi_id. User must be authenticated or have a connected wallet.',
        'reservation_certificate_generation_failed' => 'Failed to generate certificate for reservation :reservation_id. Error: :error',
        'reservation_certificate_not_found' => 'Certificate with UUID :uuid not found.',
        'reservation_already_exists' => 'User already has an active reservation for EGI :egi_id.',
        'reservation_cancel_failed' => 'Failed to cancel reservation :id. Error: :error',
        'reservation_unauthorized_cancel' => 'Unauthorized attempt to cancel reservation :id. Only the owner can cancel.',
        'reservation_status_failed' => 'Failed to retrieve reservation status for EGI :egi_id. Error: :error',
        'reservation_unknown_error' => 'An unknown error occurred during the reservation process. Error: :error',

        // Dev message for statistics
        'statistics_calculation_failed' => 'Statistics calculation failed for user :user_id. Context: :error_context. Error: :error_message',
        'icon_not_found' => 'Icon :icon_name with style :style not found in database. Using fallback icon.',
        'icon_retrieval_failed' => 'Failed to retrieve icon :icon_name. Error: :error_message. Using fallback icon.',
        'statistics_cache_clear_failed' => 'Failed to clear statistics cache for user :user_id. Error: :error_message',
        'statistics_summary_failed' => 'Failed to calculate statistics summary for user :user_id. Error: :error_message',

        // EGI Upload Handler Service-Based Architecture
        'egi_collection_service_error' => 'Collection service error during EGI upload: :error_details. Operation: :operation_id',
        'egi_wallet_service_error' => 'Wallet service error during collection setup: :error_details. Collection ID: :collection_id',
        'egi_role_service_error' => 'UserRole service error during role assignment: :error_details. User ID: :user_id',
        'egi_service_integration_error' => 'EGI services integration error: :error_details. Services: :services_involved',
        'egi_enhanced_authentication_error' => 'Enhanced EGI authentication error: :auth_type failed. Session: :session_data',
        'egi_file_input_validation_error' => 'EGI file input validation error: :validation_error. File: :original_filename',
        'egi_metadata_validation_error' => 'EGI metadata validation error: :validation_errors. Request data: :request_data',
        'egi_data_preparation_error' => 'EGI data preparation error: :error_details. File: :original_filename',
        'egi_record_creation_error' => 'EGI database record creation error: :error_details. Collection: :collection_id',
        'egi_file_storage_error' => 'EGI file storage error: :error_details. Storage disks: :failed_disks',
        'egi_cache_invalidation_error' => 'EGI cache invalidation error: :error_details. Cache keys: :cache_keys',

        // Collection Service Enhanced
        'collection_creation_enhanced_error' => 'Enhanced collection creation error: :error_details. User: :user_id, Name: :collection_name',
        'collection_validation_error' => 'Collection validation error: :validation_error. User: :user_id',
        'collection_limit_exceeded_error' => 'Collection limit exceeded for user :user_id. Current: :current_count, Max: :max_limit',
        'collection_wallet_attachment_failed' => 'Failed to attach wallets to collection :collection_id: :error_details',
        'collection_role_assignment_failed' => 'Failed to assign creator role to user :user_id: :error_details',
        'collection_ownership_mismatch_error' => 'Collection :collection_id ownership mismatch. Owner: :actual_owner, Expected: :expected_owner',
        'collection_current_update_error' => 'Failed to update current_collection_id for user :user_id: :error_details',

        // Enhanced Storage
        'egi_storage_disk_config_error' => 'Storage disk :disk_name configuration error: :error_details',
        'egi_storage_emergency_fallback_failed' => 'Emergency storage fallback failed: :error_details. All disks failed: :failed_disks',
        'egi_temp_file_read_error' => 'Temporary file :temp_path read error: :error_details',

        // Enhanced Authentication & Session
        'egi_session_auth_invalid' => 'Invalid EGI session authentication. Session status: :session_status, User ID: :user_id',
        'egi_wallet_auth_mismatch' => 'Wallet authentication mismatch. Session wallet: :session_wallet, User wallet: :user_wallet',

        // Enhanced Registration Errors
        'enhanced_registration_failed' => 'Enhanced registration with ecosystem setup failed: :error. User ID: :user_id, Collection ID: :collection_id, Components: :partial_creation',
        'registration_user_creation_failed' => 'User creation failed during registration: :error. Email: :email, User type: :user_type',
        'registration_collection_creation_failed' => 'Default collection creation failed during registration: :error. User ID: :user_id, Collection name: :collection_name',
        'registration_wallet_setup_failed' => 'Wallet setup failed during registration: :error. User: :user_id, Collection: :collection_id',
        'registration_role_assignment_failed' => 'Role assignment failed during registration: :error. User: :user_id, User type: :user_type',
        'registration_gdpr_consent_failed' => 'GDPR consent processing failed during registration: :error. User: :user_id, Consents: :consents',
        'registration_ecosystem_setup_incomplete' => 'Ecosystem setup incomplete during registration: :error. User: :user_id, Completed steps: :completed_steps',
        'registration_validation_enhanced_failed' => 'Enhanced registration validation failed: :validation_errors. Request data: :request_data',
        'registration_user_type_invalid' => 'Invalid user type during registration: :user_type. Valid types: creator,mecenate,acquirente,azienda',
        'registration_rate_limit_exceeded' => 'Registration rate limit exceeded. IP: :ip_address, Attempts: :attempts, Time window: :time_window',
        'registration_page_load_error' => 'Registration page load error: :error. IP: :ip_address',
        'permission_based_registration_failed' => 'Error during permission-based registration. Details: :error',
        'algorand_wallet_generation_failed' => 'Unable to generate valid Algorand wallet address. Error: :error',
        'ecosystem_setup_failed' => 'Error during user ecosystem creation (collection, wallets, relationships). Details: :error',
        'user_domain_initialization_failed' => 'Error during user domain initialization (profile, personal_data, etc.). Details: :error',
        'gdpr_consent_processing_failed' => 'Error during GDPR consent processing. Details: :error',
        'role_assignment_failed' => 'Error during role assignment for user :user_id. Details: :error',
        'personal_data_view_failed' => 'Error loading Personal Data view. Check PersonalDataController::index(), UserPersonalData model, and user.domains.personal-data.index view.',
        'personal_data_update_failed' => 'Error updating personal data. Check UpdatePersonalDataRequest validation, UserPersonalData database, and fiscal validator for specific country.',
        'personal_data_export_failed' => 'Error generating GDPR personal data export. Check PersonalDataController::export(), format handler, and GDPR permissions.',
        'personal_data_deletion_failed' => 'Critical error in GDPR personal data deletion request. Check PersonalDataController::destroy(), audit trail, and strong authentication.',
        'gdpr_export_rate_limit' => 'GDPR export rate limit exceeded. Check canRequestDataExport() logic, last export timestamp, and 30-day limit configuration.',
        'gdpr_violation_attempt' => 'GDPR violation attempt detected. Check consent logic in PersonalDataController, user consent status, and UpdatePersonalDataRequest validation.',
        'gdpr_notification_send_failed' => 'Critical error while sending a GDPR notification. Check notification service configuration and logs for details.',
        'gdpr_notification_dispatch_failed' => 'Critical error during GDPR notification dispatch. Check handler configuration and input data validity.',
        'gdpr_notification_persistence_failed' => 'Critical error during GDPR notification persistence to database. Transaction failed, possible data integrity issue.',
        'gdpr_service_unavailable' => 'GDPR ConsentService or related dependencies unavailable. Check database, DTOs, and translations.',

        // GDPR Consent Errors - Developer Messages EN
        'gdpr_consent_required' => 'GDPR consent required but not provided. Check ConsentService::hasConsent() and consent middleware.',
        'gdpr_consent_update_error' => 'Error updating user consents. Check ConsentService::updateUserConsents() and form validation.',
        'gdpr_consent_save_error' => 'Failed to save consents to database. Check DB transaction and UserConsent model constraints.',
        'gdpr_consent_load_error' => 'Error loading user consent status. Check ConsentService::getUserConsentStatus() and model relationships.',

        // Cookie Consent Errors - Developer Messages EN
        'cookie_consent_status_error' => 'Error loading cookie consent status. Check CookieConsentController::getConsentStatus() and storage accessibility.',
        'cookie_consent_save_error' => 'Failed to save cookie preferences. Check CookieConsentController::saveConsent() and data validation.',

        // GDPR Export Errors - Developer Messages EN
        'gdpr_export_request_error' => 'Error requesting GDPR data export. Check DataExportService and request validation.',
        'gdpr_export_limit_reached' => 'GDPR export limit reached. Check rate limiting and export policies.',
        'gdpr_export_create_error' => 'Failed to create export file. Check DataExportService::processExport() and storage permissions.',
        'gdpr_export_download_error' => 'Error downloading export file. Check file existence, permissions, and URL generation.',
        'gdpr_export_status_error' => 'Error verifying export status. Check DataExport model and status tracking.',
        'gdpr_export_processing_failed' => 'Failed to process export data. Check background jobs and data serialization.',

        // GDPR Processing Restriction Errors - Developer Messages EN
        'gdpr_processing_restricted' => 'Operation blocked by GDPR processing restriction. Check ProcessingRestrictionService.',
        'gdpr_processing_limit_view_error' => 'Error loading processing limitations view. Check middleware and view data.',
        'gdpr_processing_restriction_create_error' => 'Failed to create processing restriction. Check ProcessingRestrictionService::createRestriction().',
        'gdpr_processing_restriction_remove_error' => 'Error removing processing restriction. Check permissions and validation logic.',
        'gdpr_processing_restriction_limit_reached' => 'Processing restrictions limit reached. Check business rules and rate limiting.',

        // GDPR Deletion Errors - Developer Messages EN
        'gdpr_deletion_request_error' => 'Error requesting GDPR account deletion. Check AccountDeletionService and validation.',
        'gdpr_deletion_cancellation_error' => 'Failed to cancel deletion request. Check status transitions and business logic.',
        'gdpr_deletion_processing_error' => 'Error processing account deletion. Check background jobs and data cleanup.',

        // GDPR Breach Report Errors - Developer Messages EN
        'gdpr_breach_report_error' => 'Error submitting GDPR breach report. Check BreachReportService and form validation.',
        'gdpr_breach_evidence_upload_error' => 'Failed to upload breach evidence. Check file upload service and storage.',

        // GDPR Activity Log Errors - Developer Messages EN
        'gdpr_activity_log_error' => 'Error logging GDPR activity. Check ActivityLogService and database logging.',

        // GDPR Security Errors - Developer Messages EN
        'gdpr_enhanced_security_required' => 'Enhanced authentication required for GDPR operation. Check security middleware.',
        'gdpr_critical_security_required' => 'Password confirmation required for critical GDPR operation. Check auth verification.',

        // My Added Errors - Developer Messages EN
        'gdpr_consent_page_failed' => 'Error loading GDPR consent page. Check ConsentService, DTO integration, and view data structure.',
        'gdpr_service_unavailable' => 'GDPR ConsentService or dependencies unavailable. Check database connection, DTO files, and translations.',

        'legal_content_load_failed' => 'Legal content loading failed. Check file permissions in resources/legal/ and the validity of the "current" symlink.',
        'terms_acceptance_check_failed' => 'Failed to verify current terms acceptance for user. Check logic in ConsentService and reachability of LegalContentService.',

        // Registration validation errors - Developer messages
        'registration_email_already_exists' => 'Registration validation failed: Email :email already exists in database',
        'registration_password_too_weak' => 'Registration validation failed: Password does not meet security requirements',
        'registration_password_confirmation_mismatch' => 'Registration validation failed: Password confirmation mismatch',
        'registration_invalid_email_format' => 'Registration validation failed: Invalid email format provided: :email',
        'registration_required_field_missing' => 'Registration validation failed: Required fields missing: :fields',
        'registration_validation_comprehensive_failed' => 'Registration validation failed: Multiple validation errors detected',

        // Biography Core Errors - Developer Messages
        'biography_index_failed' => 'Error retrieving user biographies. Check pagination query and applied filters. Context: user_id, filters_applied.',
        'biography_validation_failed' => 'Validation failed for biography creation/update. Check Laravel validation rules and database constraints. Context: validation_errors, user_id.',
        'biography_create_failed' => 'Critical failure in biography creation. Possible causes: DB constraint violation, filesystem error for slug generation, or model boot exception. Context: user_id, attempted_data.',
        'biography_access_denied' => 'Biography access denied. User ID does not match owner_id and biography is not public. Check privacy logic. Context: user_id, biography_id, owner_id.',
        'biography_show_failed' => 'Error loading biography details. Possible causes: missing relationship, eager loading failure, or data corruption. Context: user_id, biography_id.',
        'biography_update_denied' => 'Biography update attempt without ownership. User ID does not match owner. Check authorization logic. Context: user_id, biography_id, owner_id.',
        'biography_update_failed' => 'Biography update failure. Possible causes: DB lock, constraint violation, or model event failure. Context: user_id, biography_id, updated_fields.',
        'biography_type_change_invalid' => 'Invalid biography type change from "chapters" to "single" with existing chapters. Business rule violation. Context: biography_id, from_type, to_type, chapters_count.',
        'biography_delete_denied' => 'Biography deletion attempt without ownership. User ID does not match owner. Check authorization logic. Context: user_id, biography_id, owner_id.',
        'biography_delete_failed' => 'Biography deletion failure. Possible causes: DB constraint violation, cascade delete failure, or filesystem cleanup error. Context: user_id, biography_id.',
        'biography_chapter_validation_failed' => 'Biography chapter operation validation failed. Check date range validation and parent biography constraints. Context: biography_id, validation_errors.',
        'biography_chapter_create_failed' => 'Biography chapter creation failure. Possible causes: parent biography type mismatch, sort_order conflict, or date range violation. Context: biography_id, chapter_data.',
        'biography_chapter_access_denied' => 'Biography chapter access denied. Check ownership via parent biography and publication status. Context: user_id, biography_id, chapter_id.',
        'biography_chapter_update_failed' => 'Biography chapter update failure. Possible causes: date constraint violation, sort_order conflict, or model event failure. Context: biography_id, chapter_id.',
        'biography_chapter_delete_failed' => 'Biography chapter deletion failure. Possible causes: DB constraint, cascade delete error, or media cleanup failure. Context: biography_id, chapter_id.',
        'biography_chapter_reorder_failed' => 'Biography chapters reorder failure. Possible causes: transaction rollback, invalid sort_order values, or DB lock. Context: biography_id, chapter_ids.',
        'biography_media_upload_failed' => 'Biography media upload failure. Possible causes: Spatie media library error, filesystem permission, or image processing failure. Context: biography_id, file_info.',
        'biography_media_validation_failed' => 'Biography media validation failed. Check file type validation, size limits, and Spatie media collection rules. Context: biography_id, file_validation_errors.',
        'biography_media_delete_failed' => 'Biography media deletion failure. Possible causes: Spatie media library error, filesystem permission, or missing media record. Context: biography_id, media_id.',
        'biography_chapter_index_failed' => 'Error retrieving biography chapters. Check ordering query and publication filters. Context: biography_id, user_id, order_by.',
        'biography_chapter_show_failed' => 'Error loading biography chapter details. Possible causes: missing relationship, eager loading failure, or chapter data corruption. Context: biography_id, chapter_id, user_id.',

        // Multi-Currency System Error Messages - Dev Messages
        'currency_exchange_service_failed' => 'Currency exchange service unavailable. Fallback used for code :code.',
        'currency_unsupported' => 'Unsupported currency requested: :currency. System configured to support: :supported.',
        'currency_rate_expired' => 'Exchange rate expired for :currency. Last update: :timestamp.',
        'currency_conversion_failed' => 'Currency conversion failed from :from to :to for amount :amount. Error: :error.',
        'user_currency_update_failed' => 'User currency preference update failed for user_id :user_id. Attempted currency: :currency.',
        'currency_exchange_service_unavailable' => 'CoinGecko service unreachable. HTTP Status: :status. Endpoint: :endpoint.',
        'currency_rate_cache_error' => 'Redis cache error for currency rate key :key. Error: :error.',
        'currency_invalid_rate_data' => 'Invalid currency rate data received from API. Response: :response.',
        'currency_conversion_error' => 'Conversion error :amount :from_currency to :to_currency. Rate: :rate.',
        'currency_unsupported_currency' => 'Currency :currency not supported by system. Supported: :supported_currencies.',
        'user_preference_update_failed' => 'User preference update failed for user_id :user_id. DB Error: :error.',
        'currency_conversion_validation_error' => 'Currency conversion validation failed. Parameters: :params. Errors: :errors.',
        'user_preference_fetch_error' => 'Unable to fetch preferences for user_id :user_id. Error: :error.',
        'currency_preference_validation_error' => 'Currency preference validation failed for :currency. Error: :validation_error.',

        // Payment Distribution Service Error Messages
        'payment_distribution_error' => 'Payment distribution failed for reservation :reservation_id. Error: :error.',
        'reservation_not_active' => 'Reservation :reservation_id is not active and cannot have distributions created.',
        'payment_not_executed' => 'Reservation :reservation_id has no payment execution timestamp.',
        'invalid_amount' => 'Reservation :reservation_id has invalid amount_eur.',
        'collection_not_found' => 'Reservation :reservation_id has no associated collection.',
        'distributions_already_exist' => 'Distributions already exist for reservation :reservation_id.',
        'no_wallets_found' => 'No wallets found for collection :collection_id.',
        'invalid_mint_percentages' => 'Collection :collection_id wallet percentages don\'t sum to 100% (current: :current_percentage%).',
        'user_activity_logging_failed' => 'Failed to log user activity for user :user_id, distribution :distribution_id. Error: :error.',
    ],
    'user' => [
        // == Existing Entries ==
        'authentication_error' => 'You are not authorized to perform this operation.',
        'scan_error' => 'Unable to verify the file\'s security at this time. Please try again later.',
        'virus_found' => 'The file ":fileName" contains potential threats and has been blocked for your safety.',
        'invalid_file_extension' => 'The file extension is not supported. Allowed extensions are: :allowed_extensions.',
        'max_file_size' => 'The file is too large. The maximum allowed size is :max_size.',
        'invalid_file_pdf' => 'The uploaded PDF is invalid or may be corrupted. Please try again.',
        'mime_type_not_allowed' => 'The uploaded file type is not supported. Allowed types are: :allowed_types.',
        'invalid_image_structure' => 'The uploaded image does not appear to be valid. Try another image.',
        'invalid_file_name' => 'The file name contains invalid characters. Use only letters, numbers, spaces, hyphens, and underscores.',
        'error_getting_presigned_url' => 'A temporary issue occurred while preparing the upload. Please try again.',
        'error_during_file_upload' => 'An error occurred during file upload. Please try again or contact support if the issue persists.',
        'unable_to_save_bot_file' => 'Unable to save the generated file at this time. Please try again later.',
        'unable_to_create_directory' => 'Internal system error while preparing to save. Please contact support.',
        'unable_to_change_permissions' => 'Internal system error while saving the file. Please contact support.',
        'impossible_save_file' => 'Unable to save your file due to a system error. Please try again or contact support.',
        'error_during_create_egi_record' => 'An error occurred while saving the information. Our technical team has been notified.',
        'error_during_file_name_encryption' => 'A security error occurred while processing the file. Please try again.',
        'imagick_not_available' => 'The system is temporarily unable to process images. Contact the administrator if the issue persists.',
        'json_error' => 'An error occurred while processing the data. Check the input data or try again later. [Ref: JSON]',
        'generic_server_error' => 'A server error occurred. Please try again later or contact support if the issue persists. [Ref: SERVER]',
        'file_not_found' => 'The requested file was not found.',
        'unexpected_error' => 'An unexpected error occurred. Our technical team has been notified. Please try again later. [Ref: UNEXPECTED]',
        'error_deleting_local_temp_file' => 'Internal error while cleaning up temporary files. Please contact support.',
        'acl_setting_error' => 'Unable to set the correct permissions for the file. Please try again or contact support.',
        'invalid_input' => 'The provided value for :param is invalid. Please check the input and try again.',
        'temp_file_not_found' => 'A temporary issue occurred with the file :file. Please try again.',
        'error_deleting_ext_temp_file' => 'Internal error while cleaning up external temporary files. Please contact support.',
        'ucm_delete_failed' => 'An error occurred while deleting the configuration. Please try again later.',
        'undefined_error_code' => 'An unexpected error occurred. Please contact support if the issue persists. [Ref: UNDEFINED]',
        'fallback_error' => 'An unexpected system issue occurred. Please try again later or contact support. [Ref: FALLBACK]',
        'fatal_fallback_failure' => 'A critical system error occurred. Please contact support immediately. [Ref: FATAL]',
        'ucm_audit_not_found' => 'No historical information is available for this item.',
        'ucm_duplicate_key' => 'This configuration setting already exists.',
        'ucm_create_failed' => 'Unable to save the new configuration setting. Please try again.',
        'ucm_update_failed' => 'Unable to update the configuration setting. Please try again.',
        'ucm_not_found' => 'The requested configuration setting was not found.',
        'invalid_file' => 'The provided file is invalid. Please check the file and try again.',
        'invalid_file_validation' => 'Please check the file in the :field field. Validation failed.',
        'error_saving_file_metadata' => 'An error occurred while saving the file details. Please try uploading again.',
        'server_limits_restrictive' => 'Server configuration may be preventing this operation. Contact support if the issue persists.',
        'generic_internal_error' => 'An internal error occurred. Our technical team has been notified and is working to resolve it.',
        'egi_auth_required' => 'Please log in to upload an EGI.',
        'egi_file_input_error' => 'Please select a valid file to upload.',
        'egi_validation_failed' => 'Please correct the highlighted fields in the form.',
        'egi_collection_init_error' => 'Unable to prepare your collection. Contact support if the issue persists.',
        'egi_storage_failure' => 'Failed to securely save the EGI file. Please try again or contact support.',
        'egi_unexpected_error' => 'An unexpected error occurred while processing your EGI. Please try again later.',

        // Image Optimization Error Messages
        'image_optimization_invalid_file' => 'The uploaded file is not a valid image. Please select a valid image file.',
        'image_optimization_unsupported_format' => 'The image format is not supported. Please use JPEG, PNG, WebP, or GIF files.',
        'image_optimization_processing_failed' => 'Failed to process the image. Please try again or contact support if the issue persists.',
        'image_optimization_storage_failed' => 'Failed to save the optimized image. Please try again or contact support.',
        'image_optimization_imagick_unavailable' => 'Image processing is temporarily unavailable. Contact the administrator if the issue persists.',
        'image_optimization_conversion_failed' => 'Failed to convert the image to the required format. Please try with a different image.',
        'image_optimization_variant_creation_failed' => 'Failed to create image variants. Please try again or contact support.',
        'image_optimization_file_too_large' => 'The image file is too large. Please reduce the file size and try again.',
        'image_optimization_invalid_dimensions' => 'The image dimensions are not valid. Please check the image size requirements.',
        'egi_unauthorized_access' => 'Unauthorized access. Please log in.',
        'egi_page_rendering_error' => 'An issue occurred while loading the page. Please try again later or contact support.',
        'egi_update_failed' => 'Unable to update the EGI. Please try again.',
        'egi_delete_failed' => 'Unable to delete the EGI. Please try again.',
        'egi_trait_delete_failed' => 'Unable to delete the trait from the EGI. Please try again.',
        'egi_not_found' => 'EGI not found. Please verify the ID is correct.',
        'trait_not_found' => 'Trait not found. Please verify the trait exists.',

        // Traits API errors (user messages)
        'traits_categories_load_failed' => 'Unable to load trait categories. Please try again later.',
        'traits_unauthorized_access' => 'You are not authorized to modify these traits.',
        'traits_egi_published' => 'Cannot modify traits of a published EGI.',
        'traits_no_data_provided' => 'No trait data provided. Please verify the data entered.',
        'traits_save_failed' => 'Unable to save traits. Please try again.',
        'traits_add_failed' => 'Unable to add new traits. Please try again.',

        // Collection CRUD Errors (user messages)
        'collection_update_failed' => 'Unable to update the collection. Please try again.',
        'collection_delete_failed' => 'Unable to delete the collection. Please try again.',

        'invalid_egi_file' => 'The EGI file cannot be processed due to validation errors. Please verify the file format and content.',
        'error_during_egi_processing' => 'An error occurred while processing the EGI file. Our team has been notified and will investigate the issue.',

        // Wallet Creation Errors (user messages)
        'wallet_creation_failed' => 'We encountered a problem setting up the wallet for this collection. Our team has been notified and will resolve this issue.',
        'wallet_insufficient_quota' => 'You do not have sufficient royalty quota available for this operation. Please adjust your royalty values and try again.',
        'wallet_address_invalid' => 'The wallet address provided is not valid. Please check the format and try again.',
        'wallet_not_found' => 'The requested wallet could not be found. Please verify your information and try again.',
        'wallet_already_exists' => 'A wallet is already configured for this collection. Please use the existing wallet or contact support for assistance.',
        'wallet_invalid_secret' => 'The secret key you entered is incorrect. Please try again.',
        'wallet_validation_failed' => 'The wallet address format is invalid. Please check and try again.',
        'wallet_connection_failed' => 'Unable to connect your wallet at this time. Please try again later.',
        'wallet_disconnect_failed' => 'There was a problem disconnecting your wallet. Please refresh the page.',

        // COLLECTION
        'collection_creation_failed' => 'Unable to create your collection. Please try again later or contact support.',
        'collection_find_create_failed' => 'Unable to access your collections. Please try again later.',

        // == New Entries ==
        'authorization_error' => 'You do not have permission to perform this action.',
        'csrf_token_mismatch' => 'Your session has expired or is invalid. Please refresh the page and try again.',
        'route_not_found' => 'The page or resource you requested could not be found.',
        'method_not_allowed' => 'The action you tried to perform is not allowed on this resource.',
        'too_many_requests' => 'You are performing actions too quickly. Please wait a moment and try again.',
        'database_error' => 'A database error occurred. Please try again later or contact support. [Ref: DB]',
        'record_not_found' => 'The item you requested could not be found.',
        'validation_error' => 'Please correct the errors highlighted in the form and try again.', // Generic user message
        'utm_load_failed' => 'The system encountered an issue loading language settings. Functionality may be limited.', // Generic internal error for user
        'utm_invalid_locale' => 'The requested language setting is not available.', // Slightly more specific internal issue
        // Internal UEM failures below generally shouldn't have specific user messages, map to generic ones if needed.
        'uem_email_send_failed' => null, // Use generic_internal_error
        'uem_slack_send_failed' => null, // Use generic_internal_error
        'uem_recovery_action_failed' => null, // Use generic_internal_error
        'user_unauthenticated_access' => 'Authentication required. Please log in to continue.',
        'set_current_collection_forbidden' => 'You do not have permission to access or set this collection as your current one.',
        'set_current_collection_failed' => 'An unexpected error occurred while updating your preferences. Our team has been notified. Please try again later.',
        'auth_required' => 'You must be logged in to perform this action.',
        'auth_required_for_like' => 'You must be connected to like items.',
        'like_toggle_failed' => 'Sorry, we could not process your like request. Please try again.',

        // user messages for reservations system
        'reservation_egi_not_available' => 'This EGI is not available for reservation at the moment.',
        'reservation_amount_too_low' => 'Your offer amount is too low. Please enter a higher amount.',
        'reservation_relaunch_amount_too_low' => 'For a relaunch, the amount must be higher than your previous reservation of €:previous_amount.',
        'reservation_unauthorized' => 'You need to connect your wallet or log in to make a reservation.',
        'reservation_certificate_generation_failed' => 'We couldn\'t generate your reservation certificate. Our team has been notified.',
        'reservation_certificate_not_found' => 'The requested certificate could not be found.',
        'reservation_already_exists' => 'You already have an active reservation for this EGI.',
        'reservation_cancel_failed' => 'We couldn\'t cancel your reservation. Please try again later.',
        'reservation_unauthorized_cancel' => 'You don\'t have permission to cancel this reservation.',
        'reservation_status_failed' => 'Could not retrieve the reservation status. Please try again later.',
        'reservation_unknown_error' => 'Something went wrong with your reservation. Our team has been notified.',

        // user messages for statistics
        'statistics_calculation_failed' => 'Unable to load your statistics at the moment. Our team has been notified. Please try again later.',
        'icon_not_found' => 'Icon temporarily unavailable. Using default icon.',
        'icon_retrieval_failed' => 'Icon temporarily unavailable. Using default icon.',
        'statistics_cache_clear_failed' => 'Unable to refresh statistics cache. Please try again.',
        'statistics_summary_failed' => 'Unable to load statistics summary. Please try again.',

        // EGI Upload Handler Service-Based Architecture
        'egi_collection_service_error' => 'An error occurred while managing your collection. Our technical team has been notified.',
        'egi_wallet_service_error' => 'Error during wallet setup for this collection. Please try again in a few minutes.',
        'egi_role_service_error' => 'Error assigning creator permissions. Contact support if the problem persists.',
        'egi_service_integration_error' => 'An internal system error occurred. Our technicians are already investigating.',
        'egi_enhanced_authentication_error' => 'Your session is not valid. Please log in again to your Renaissance.',
        'egi_file_input_validation_error' => 'The file you uploaded is invalid or corrupted. Check the format and try again.',
        'egi_metadata_validation_error' => 'Some entered data is incorrect. Check the highlighted fields and try again.',
        'egi_data_preparation_error' => 'Error processing your file. Verify it\'s a valid image.',
        'egi_record_creation_error' => 'Error creating your EGI. The technical team has been automatically notified.',
        'egi_file_storage_error' => 'Error during secure file storage. Please retry the upload.',
        'egi_cache_invalidation_error' => 'Your EGI has been uploaded, but it may take a few minutes to appear everywhere.',

        // Collection Service Enhanced
        'collection_creation_enhanced_error' => 'We couldn\'t create your collection. Please try again or contact support.',
        'collection_validation_error' => 'Collection data is invalid. Please verify and try again.',
        'collection_limit_exceeded_error' => 'You\'ve reached the maximum collection limit. Contact support to increase it.',
        'collection_wallet_attachment_failed' => 'Collection created, but with wallet configuration issues. Contact support.',
        'collection_role_assignment_failed' => 'Collection created, but with permission issues. Contact support.',
        'collection_ownership_mismatch_error' => 'You don\'t have permission to access this collection.',
        'collection_current_update_error' => 'Error updating your active collection. Please try again.',

        // User Current Collection Update Errors
        'user_current_collection_update_failed' => 'We encountered a critical issue while setting up your collection. Our technical team has been notified and will resolve this immediately. Please try again in a few moments or contact support if the problem persists.',
        'user_current_collection_validation_failed' => 'There was an issue with your collection selection. Please ensure you have the proper permissions for this collection and try again. If you continue to experience problems, please contact our support team.',

        // Enhanced Storage
        'egi_storage_disk_config_error' => 'Storage system configuration problem. The technical team has been notified.',
        'egi_storage_emergency_fallback_failed' => 'Critical storage system error. Technicians are investigating.',
        'egi_temp_file_read_error' => 'We can\'t read the file you uploaded. Try again with a different file.',

        // Enhanced Authentication & Session
        'egi_session_auth_invalid' => 'Your session has expired. Reconnect your wallet to continue.',
        'egi_wallet_auth_mismatch' => 'The connected wallet doesn\'t match your account. Verify the connection.',

        // Enhanced Registration Errors
        'enhanced_registration_failed' => 'An error occurred while setting up your account in the Digital Renaissance. Our team has been notified.',
        'registration_user_creation_failed' => 'We couldn\'t create your account. Please verify the entered data and try again.',
        'registration_collection_creation_failed' => 'Your account was created, but we couldn\'t set up your collection. Please contact support.',
        'registration_wallet_setup_failed' => 'Registration is almost complete, but there are issues with wallet configuration. Support will contact you soon.',
        'registration_role_assignment_failed' => 'Registration is almost complete, but there are issues with your account permissions. Support will help you.',
        'registration_gdpr_consent_failed' => 'Error saving your privacy preferences. Please try again or contact support.',
        'registration_ecosystem_setup_incomplete' => 'Registration was not completed fully. Our team is checking and will contact you.',
        'registration_validation_enhanced_failed' => 'Some entered data is incorrect. Check the highlighted fields and try again.',
        'registration_user_type_invalid' => 'The selected role is not valid. Choose between Creator, Mecenate, Purchaser, or Business.',
        'registration_rate_limit_exceeded' => 'Too many registration requests. Please try again in a few minutes.',
        'registration_page_load_error' => 'Error loading the registration page. Please reload the page.',
        'permission_based_registration_failed_user' => 'An error occurred during registration. Please try again or contact support if the problem persists.',
        'algorand_wallet_generation_failed_user' => 'Error creating digital wallet. Please try registration again.',
        'ecosystem_setup_failed_user' => 'Registration completed, but there was an error in initial setup. You can complete setup from your profile.',
        'user_domain_initialization_failed_user' => 'Registration completed successfully! Some profile sections may require additional configuration.',
        'gdpr_consent_processing_failed_user' => 'Error processing privacy consents. Please verify your choices and try again.',
        'role_assignment_failed_user' => 'Error in account type configuration. Please contact support.',
        'personal_data_view_failed' => 'An error occurred while loading your personal data. Please try again in a few minutes or contact support if the problem persists.',
        'personal_data_update_failed' => 'Unable to save changes to your personal data. Please verify that all fields are filled correctly and try again.',
        'personal_data_export_failed' => 'An error occurred while exporting your data. Please try again later or contact support for assistance.',
        'personal_data_deletion_failed' => 'Unable to process your data deletion request. Please contact our support team for immediate assistance.',
        'gdpr_export_rate_limit' => 'You can request a data export once every 30 days. Your next export will be available in a few days.',
        'gdpr_violation_attempt' => 'You cannot update your personal data without providing appropriate consent. Please accept the data processing terms to continue.',
        'gdpr_notification_send_failed_user' => 'We\'re sorry, a technical issue occurred and an important notification could not be sent. Our team has been notified.',
        'gdpr_notification_dispatch_failed' => 'A problem occurred while processing your privacy-related request. Our team has been notified and will resolve the issue as soon as possible.',
        'gdpr_notification_persistence_failed' => 'We were unable to complete the requested operation for your data management. Please try again later or contact support.',
        'gdpr_service_unavailable' => 'The consent management service is currently unavailable. Please try again later.',

        'legal_content_load_failed' => 'Unable to load legal documents at this time. Please try again later.',
        // Note: for TERMS_ACCEPTANCE_CHECK_FAILED, use the existing translation for 'generic_error'

        // Registration validation errors - User-friendly messages
        'registration_email_already_exists' => 'This email address is already registered. Try logging in instead.',
        'registration_password_too_weak' => 'Password must be at least 8 characters long and include letters and numbers.',
        'registration_password_confirmation_mismatch' => 'Password confirmation does not match. Please try again.',
        'registration_invalid_email_format' => 'Please enter a valid email address.',
        'registration_required_field_missing' => 'Please fill in all required fields.',
        'registration_validation_comprehensive_failed' => 'Please check the form and correct any errors.',

        // Biography Core Errors - User Messages
        'biography_index_failed' => 'Unable to load your biographies. Please try again in a moment or refresh the page.',
        'biography_validation_failed' => 'Some fields were not filled correctly. Please check the entered data and try again.',
        'biography_create_failed' => 'Unable to create the biography. Please try again in a moment or contact support if the problem persists.',
        'biography_access_denied' => 'You do not have permission to view this biography. You may not be the owner or the biography may be private.',
        'biography_show_failed' => 'Unable to load biography details. Please try again in a moment.',
        'biography_update_denied' => 'You do not have permission to edit this biography. Only the owner can make changes.',
        'biography_update_failed' => 'Unable to save changes to the biography. Please try again in a moment.',
        'biography_type_change_invalid' => 'Cannot change biography type from "chapters" to "single text" because chapters exist. Please delete all chapters first.',
        'biography_delete_denied' => 'You do not have permission to delete this biography. Only the owner can delete it.',
        'biography_delete_failed' => 'Unable to delete the biography. Please try again in a moment or contact support.',
        'biography_chapter_validation_failed' => 'Some chapter fields were not filled correctly. Please check the dates and content entered.',
        'biography_chapter_create_failed' => 'Unable to create the chapter. Check that the biography is set to "chapters" and try again.',
        'biography_chapter_access_denied' => 'You do not have permission to view this chapter. You may not be the owner or the chapter may not be published.',
        'biography_chapter_update_failed' => 'Unable to save changes to the chapter. Check the entered dates and try again.',
        'biography_chapter_delete_failed' => 'Unable to delete the chapter. Please try again in a moment.',
        'biography_chapter_reorder_failed' => 'Unable to reorder chapters. Try again or refresh the page to see the correct order.',
        'biography_media_upload_failed' => 'Unable to upload the image. Check that the file is a valid image (JPG, PNG) and does not exceed 5MB.',
        'biography_media_validation_failed' => 'The selected file is not valid. Make sure to upload an image in JPG, PNG or WebP format with a maximum size of 5MB.',
        'biography_media_delete_failed' => 'Unable to delete the image. Please try again in a moment.',
        'biography_chapter_index_failed' => 'Unable to load biography chapters. Please try again in a moment or refresh the page.',
        'biography_chapter_show_failed' => 'Unable to load chapter details. Please try again in a moment.',

        // Multi-Currency System Error Messages - User Messages (User-Friendly)
        'currency_exchange_service_failed' => 'The currency conversion service is temporarily unavailable. Please try again in a few minutes.',
        'currency_unsupported' => 'The selected currency is not currently supported. Please choose from the available currencies.',
        'currency_rate_expired' => 'Exchange rates have expired. We are updating the data, please try again in a few minutes.',
        'currency_conversion_failed' => 'An error occurred during currency conversion. Please try again or contact support.',
        'user_currency_update_failed' => 'Unable to save your currency preference. Please try again later.',
        'currency_exchange_service_unavailable' => 'The currency exchange service is temporarily unavailable. Please try again in a few minutes.',
        'currency_invalid_rate_data' => 'Exchange rate data is not available. Please try again later.',
        'currency_conversion_error' => 'Error during currency conversion. Please check the amount and try again.',
        'currency_unsupported_currency' => 'The selected currency is not supported. Please choose a different currency from the list.',
        'user_preference_update_failed' => 'Unable to save your preferences. Please try again later.',
        'currency_conversion_validation_error' => 'The data entered for conversion is not valid. Please check and try again.',
        'user_preference_fetch_error' => 'Unable to load your preferences. Please try again or contact support.',
        'currency_preference_validation_error' => 'The selected currency is not valid. Please choose a currency from the available list.',

        // Payment Distribution Service Error Messages - User-Friendly
        'payment_distribution_error' => 'Unable to process payment distribution. Our technical team has been notified. Please try again later.',
        'reservation_not_active' => 'This reservation is not active yet. Payment distributions will be processed automatically once the reservation becomes active.',
        'payment_not_executed' => 'Payment for this reservation has not been processed yet. Please contact support if you believe this is an error.',
        'invalid_amount' => 'There is an issue with the payment amount. Please contact support for assistance.',
        'collection_not_found' => 'Unable to find the associated collection. Please contact support for assistance.',
        'distributions_already_exist' => 'Payment distributions have already been processed for this reservation.',
        'no_wallets_found' => 'No payment wallets are configured for this collection. Please contact the collection owner.',
        'invalid_mint_percentages' => 'The collection wallet configuration is incorrect. Please contact the collection owner to fix the percentage settings.',
        'user_activity_logging_failed' => 'Unable to record activity log. This does not affect your transaction.',

        // Cookie Consent Errors - User Messages EN
        'cookie_consent_status_error' => 'Unable to load your cookie preferences. Please try refreshing the page.',
        'cookie_consent_save_error' => 'Could not save your cookie preferences. Please try again in a moment.',
    ],

    // Generic message (used by UserInterfaceHandler if no specific message found)
    'generic_error' => 'An error has occurred. Please try again later or contact support.',
];
