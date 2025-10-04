<?php

/**
 * English translations for PA Acts Tokenization System
 * 
 * @package Resources\Lang\EN
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - PA Acts Tokenization)
 * @date 2025-10-04
 * @purpose English localization for PA acts tokenization system
 */

return [
    // Page titles
    'title' => 'Tokenized Acts',
    'page_title' => 'Administrative Acts Management',
    'dashboard_title' => 'Acts Dashboard',

    // Actions
    'upload_act' => 'Upload Act',
    'view_act' => 'View Act',
    'download_original' => 'Download Original',
    'verify_act' => 'Verify Act',
    'copy_verification_link' => 'Copy Verification Link',

    // Document types (from config)
    'doc_types' => [
        'delibera' => [
            'label' => 'Resolution',
            'description' => 'Council or Board Resolution'
        ],
        'determina' => [
            'label' => 'Determination',
            'description' => 'Management Determination'
        ],
        'ordinanza' => [
            'label' => 'Ordinance',
            'description' => 'Mayoral or Executive Ordinance'
        ],
        'decreto' => [
            'label' => 'Decree',
            'description' => 'Administrative Decree'
        ],
        'atto' => [
            'label' => 'Generic Act',
            'description' => 'Generic Administrative Act'
        ]
    ],

    // Form fields
    'protocol_number' => 'Protocol Number',
    'protocol_date' => 'Protocol Date',
    'doc_type' => 'Document Type',
    'doc_title' => 'Document Title',
    'doc_description' => 'Description/Subject',
    'select_file' => 'Select PDF File',
    'select_fascicolo' => 'Select Folder',

    // Upload instructions
    'upload_instructions' => 'Drag and drop the digitally signed PDF here or click to select',
    'upload_instructions_short' => 'Digitally signed PDF',
    'drop_file_here' => 'Drop file here',

    // Tokenization status
    'tokenization_status' => 'Tokenization Status',
    'status_pending' => 'Pending',
    'status_validating' => 'Validating',
    'status_anchoring' => 'Anchoring on Blockchain',
    'status_completed' => 'Completed',
    'status_failed' => 'Failed',

    // Blockchain info
    'blockchain_txid' => 'Blockchain Transaction ID',
    'blockchain_anchor_date' => 'Anchor Date',
    'merkle_root' => 'Merkle Root',
    'merkle_proof' => 'Merkle Proof',
    'batch_id' => 'Batch ID',

    // Signature validation
    'signature_validation' => 'Digital Signature Validation',
    'signature_valid' => 'Valid Signature',
    'signature_invalid' => 'Invalid Signature',
    'signature_not_found' => 'Signature Not Found',
    'signer' => 'Signer',
    'signer_cn' => 'Signer Name',
    'signer_email' => 'Signer Email',
    'cert_serial' => 'Certificate Serial',
    'cert_issuer' => 'Certificate Issuer',
    'signature_timestamp' => 'Signature Timestamp',
    'validation_date' => 'Validation Date',

    // Document hash
    'doc_hash' => 'Document Hash',
    'doc_hash_short' => 'Hash',
    'hash_algorithm' => 'Hash Algorithm',
    'hash_calculated' => 'Calculated Hash',

    // Public verification
    'public_verification' => 'Public Verification',
    'public_code' => 'Verification Code',
    'public_url' => 'Public Verification URL',
    'qr_code' => 'QR Code',
    'scan_qr' => 'Scan to Verify',
    'verification_page_title' => 'Act Authenticity Verification',

    // Stats & KPI
    'view_count' => 'Views',
    'download_count' => 'Downloads',
    'last_viewed_at' => 'Last Viewed',
    'last_downloaded_at' => 'Last Downloaded',

    // Table headers
    'table' => [
        'title' => 'Title',
        'protocol' => 'Protocol',
        'date' => 'Date',
        'type' => 'Type',
        'status' => 'Status',
        'txid' => 'TXID',
        'actions' => 'Actions'
    ],

    // Validation messages
    'validation' => [
        'pdf_only' => 'Only PDF files are allowed',
        'max_size' => 'Maximum size: :size MB',
        'signature_required' => 'PDF must be digitally signed',
        'protocol_required' => 'Protocol number is required',
        'protocol_date_required' => 'Protocol date is required',
        'doc_type_required' => 'Document type is required',
        'title_required' => 'Title is required',
        'min_file_size' => 'File is too small (minimum 1KB)',
        'invalid_pdf' => 'Invalid PDF file',
        'upload_failed' => 'Upload failed',
        'validation_failed' => 'Validation failed'
    ],

    // Success messages
    'success' => [
        'upload_completed' => 'Act uploaded successfully',
        'tokenization_started' => 'Tokenization started',
        'tokenization_completed' => 'Tokenization completed',
        'verification_link_copied' => 'Verification link copied to clipboard'
    ],

    // Error messages
    'errors' => [
        'upload_failed' => 'Upload error',
        'tokenization_failed' => 'Tokenization error',
        'document_not_found' => 'Document not found',
        'invalid_verification_code' => 'Invalid verification code',
        'signature_validation_failed' => 'Signature validation error',
        'blockchain_error' => 'Blockchain anchoring error'
    ],

    // Info messages
    'info' => [
        'batch_pending' => 'The act will be included in the next daily batch',
        'signature_mock' => 'Signature validation in mock mode (development)',
        'blockchain_mock' => 'Blockchain anchoring in mock mode (development)',
        'no_documents' => 'No acts found',
        'verification_instructions' => 'Share this link or QR code to allow public verification of document authenticity'
    ],

    // Buttons
    'buttons' => [
        'upload' => 'Upload',
        'cancel' => 'Cancel',
        'verify' => 'Verify',
        'download' => 'Download',
        'view_details' => 'View Details',
        'copy_link' => 'Copy Link',
        'generate_qr' => 'Generate QR Code'
    ],

    // Breadcrumbs
    'breadcrumbs' => [
        'dashboard' => 'Dashboard',
        'acts' => 'Acts',
        'upload' => 'Upload Act',
        'view' => 'View Act',
        'verify' => 'Verify'
    ]
];
