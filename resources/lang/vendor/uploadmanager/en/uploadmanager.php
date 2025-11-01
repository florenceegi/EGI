<?php

return [
    // Generale
    'dev' => [
        'invalid_file' => 'Invalid or missing file: :fileName',
        'invalid_file_validation' => 'Validation failed for file :fileName: :error',
        'error_saving_file_metadata' => 'Unable to save metadata for file :fileName',
        'server_limits_restrictive' => 'Server upload limits are more restrictive than application settings',
        // ... altri messaggi
    ],
    'user' => [
        'invalid_file' => 'The uploaded file is invalid. Please try again with another file.',
        'invalid_file_validation' => 'The file does not meet the requirements. Check format and size.',
        'error_saving_file_metadata' => 'An error occurred while saving the file information.',
        'server_limits_restrictive' => '',
        // ... altri messaggi
    ],

    'upload' => [
        'max_files' => 'Max :count file',
        'max_file_size' => 'Max :size per file',
        'max_total_size' => 'Max :size total',
        'max_files_error' => 'You can upload a maximum of :count files at a time.',
        'max_file_size_error' => 'The file ":name" exceeds the maximum allowed size (:size).',
        'max_total_size_error' => 'The total size of the files (:size) exceeds the allowed limit (:limit).',
    ],

    // Enterprise feature badges (Point 4)
    'storage_space_unit' => 'GB',
    'secure_storage' => 'Secure Storage',
    'secure_storage_tooltip' => 'Your files are saved with redundancy to protect your assets',
    'virus_scan_feature' => 'Virus Scan',
    'virus_scan_tooltip' => 'Each file is scanned for potential threats before storage',
    'advanced_validation' => 'Advanced Validation',
    'advanced_validation_tooltip' => 'Format validation and file integrity checks',
    'storage_space' => 'Space: :used/:total GB',
    'storage_space_tooltip' => 'Available storage space for your EGI assets',
    'toggle_virus_scan' => 'Toggle virus scanning on/off',

    // EGI Metadata (Point 3)
    'quick_egi_metadata' => 'Quick EGI Metadata',
    'egi_title' => 'EGI Title',
    'egi_title_placeholder' => 'E.g. Pixel Dragon #123',
    'egi_title_info' => 'The title of your EGI. It will be displayed in the marketplace and galleries.',
    'egi_collection' => 'Collection',
    'select_collection' => 'Select collection',
    'existing_collections' => 'Existing collections',
    'create_new_collection' => 'Create new collection',
    'egi_description' => 'Description',
    'egi_description_placeholder' => 'Brief description of your work...',
    'metadata_notice' => 'These metadata will be associated with your EGI, but you can edit them later.',
    'floor_price' => 'Floor Price',
    'floor_price_placeholder' => 'E.g. 1.000 ALGO',
    'floor_price_info' => 'The minimum initial price to purchase this EGI. It establishes the base value of the asset in the marketplace.',
    'creation_date' => 'Creation Date',
    'creation_date_placeholder' => 'E.g. 2023-10-01',
    'creation_date_info' => 'The official creation date of the EGI. It will be recorded on the blockchain and used to verify the authenticity and history of the asset.',
    'position' => 'Position',
    'position_placeholder' => 'E.g. 1/1000',
    'position_info' => 'Indicates the position of the EGI within the collection (e.g., 1/1000 means "first of a thousand"). It determines rarity and placement in galleries.',
    'publish_egi' => 'Publish EGI',
    'publish_egi_tooltip' => 'Publish your EGI to the selected collection',

    // Accessibility (Point 5)
    'select_files_aria' => 'Select files for upload',
    'select_files_tooltip' => 'Select one or more files from your device',
    'save_aria' => 'Save selected files',
    'save_tooltip' => 'Upload selected files to the server',
    'cancel_aria' => 'Cancel current upload',
    'cancel_tooltip' => 'Cancel the operation and remove the selected files',
    'return_aria' => 'Return to collection',
    'return_tooltip' => 'Return to the collection view without saving',

    // Generale
    'file_saved_successfully' => 'File :fileCaricato saved successfully',
    'file_deleted_successfully' => 'File deleted successfully',
    'first_template_title' => 'Egi Manager',
    'file_upload' => 'File Upload',
    'max_file_size_reminder' => 'Maximum file size: 10MB',
    'upload_your_files' => 'Upload your files',
    'save_the_files' => 'Save files',
    'cancel' => 'Cancel',
    'return_to_collection' => 'Return to collection',
    'mint_your_masterpiece' => 'Make your own masterpiece',
    'preparing_to_mint' => 'I\'m waiting for your files, dear...',
    'cancel_confirmation' => 'Do you want to cancel?',
    'waiting_for_upload' => 'Upload Status: Waiting...',
    'server_unexpected_response' => 'The server returned an invalid or unexpected response.',
    'unable_to_save_after_recreate' => 'Unable to save the file after recreating the directory.',
    'config_not_loaded' => 'Global configuration not loaded. Make sure JSON has been fetched.',
    'drag_files_here' => 'Drag files here',
    'select_files' => 'Select files',
    'or' => 'or',

    // Validation messages
    'allowedExtensionsMessage' => 'File extension not allowed. The allowed extensions are: :allowedExtensions',
    'allowedMimeTypesMessage' => 'File type not allowed. The allowed file types are: :allowedMimeTypes',
    'maxFileSizeMessage' => 'File size too large. The maximum allowed size is :maxFileSize',
    'minFileSizeMessage' => 'File size too small. The minimum allowed size is :minFileSize',
    'maxNumberOfFilesMessage' => 'Maximum number of files exceeded. The maximum allowed number is :maxNumberOfFiles',
    'acceptFileTypesMessage' => 'File type not allowed. The accepted file types are: :acceptFileTypes',
    'invalidFileNameMessage' => 'Invalid file name. The file name cannot contain the following characters: / \ ? % * : | " < >',

    // Virus scanning
    'virus_scan_disabled' => 'Virus scanning disabled',
    'virus_scan_enabled' => 'Virus scanning enabled',
    'antivirus_scan_in_progress' => 'Antivirus scan in progress',
    'scan_skipped_but_upload_continues' => 'Scan skipped, but upload continues',
    'scanning_stopped' => 'Scanning stopped',
    'file_scanned_successfully' => 'File :fileCaricato scanned successfully',
    'one_or_more_files_were_found_infected' => 'One or more files were found infected',
    'all_files_were_scanned_no_infected_files' => 'All files were scanned and no infected files were found',
    'the_uploaded_file_was_detected_as_infected' => 'The uploaded file was detected as infected',
    'possible_scanning_issues' => 'Warning: possible issues during virus scan',
    'unable_to_complete_scan_continuing' => 'Warning: Unable to complete virus scan, but continuing anyway',

    // Status messages
    'im_checking_the_validity_of_the_file' => 'Checking file validity',
    'im_recording_the_information_in_the_database' => 'Recording information in the database',
    'all_files_are_saved' => 'All files have been saved',
    'upload_failed' => 'Upload failed',
    'some_errors' => 'Some errors occurred',
    'no_file_uploaded' => 'No file uploaded',

    // Error messages
    'unauthenticated' => 'Devi effettuare il login per caricare un file.',
    'email_not_verified' => 'Devi verificare il tuo indirizzo email prima di caricare un file.',
    'create_egi' => 'Create an EGI',
    'error_title' => 'Access Denied',


    // JavaScript translations
    // JavaScript translations - camelCase for TypeScript compatibility
    'js' => [
        // Upload processing
        'uploadProcessingError' => 'Error processing the upload',
        'invalidServerResponse' => 'The server returned an invalid or unexpected response.',
        'unexpectedUploadError' => 'Unexpected error during upload.',
        'criticalUploadError' => 'Critical upload error',
        'errorDuringUpload' => 'Error during upload',
        'errorDuringUploadRequest' => 'Error during upload request',
        
        // Virus scanning
        'fileNotFoundForScan' => 'File not found for antivirus scan',
        'scanError' => 'Error during antivirus scan',
        'enableVirusScanning' => 'Virus scanning enabled',
        'disableVirusScanning' => 'Virus scanning disabled',
        'virusScanAdvise' => 'Virus scanning may slow down the upload process',
        'Scanning_stopped' => 'Scanning stopped',
        
        // Upload states
        'noFileSpecified' => 'No file specified',
        'confirmCancel' => 'Do you want to cancel?',
        'uploadWaiting' => 'Upload Status: Waiting...',
        'startingUpload' => 'Starting upload',
        'startingSaving' => 'Starting save',
        'startingScan' => 'Starting scan',
        'loading' => 'Loading',
        'uploadFinished' => 'Upload finished',
        'uploadAndScan' => 'Upload and scan completed',
        'scanningComplete' => 'Scanning complete',
        'scanningSuccess' => 'Scanning completed successfully',
        
        // Errors
        'serverError' => 'Server error',
        'saveError' => 'Error during save',
        'configError' => 'Configuration error',
        'someError' => 'Some errors occurred',
        'completeFailure' => 'Complete failure',
        'unknownError' => 'Unknown error',
        'unspecifiedError' => 'Unspecified error',
        
        // File operations
        'deleteButton' => 'Delete',
        'deleteFileError' => 'Error deleting file',
        'errorDeleteTempLocal' => 'Error deleting local temporary file',
        'errorDeleteTempExt' => 'Error deleting external temporary file',
        
        // UI elements
        'of' => 'of',
        'okButton' => 'OK',
        'checkFilesGuide' => 'Check the files guide',
        
        // Validation
        'invalidFilesTitle' => 'Invalid Files',
        'invalidFilesMessage' => 'Some files are invalid',
        
        // Emoji
        'emojiHappy' => '😊',
        'emojiSad' => '😢',
        'emojiAngry' => '😠',
    ],

    // EGI Type Selection (Dual Architecture)
    'egi_type_label' => 'EGI Type',
    'egi_type_help' => 'Choose how your EGI will be minted',
    'egi_type_asa' => 'Classic EGI (ASA)',
    'egi_type_asa_desc' => 'Static asset on Algorand blockchain. Permanent certificate, guaranteed authenticity.',
    'egi_type_smart_contract' => 'Living EGI (SmartContract)',
    'egi_type_smart_contract_desc' => 'Intelligent asset with integrated AI. Automatic analysis, promotion and evolutionary memory.',
    'free' => 'Free',
    'egi_type_notice' => 'The chosen type will determine the features available for your EGI. You can always mint it later.',
];
