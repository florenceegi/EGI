<?php

return [
    // Page Meta
    'page_title' => 'Mint :title - FlorenceEGI',
    'meta_description' => 'Mint your EGI :title on the Algorand blockchain. Safe and transparent process.',

    // Header
    'header_title' => 'Mint Your EGI',
    'header_description' => 'Complete your purchase and mint your EGI on the Algorand blockchain. This process is irreversible.',

    // Buttons
    'mint_button' => 'Mint (:price)',
    'mint_button_processing' => 'Minting in progress...',
    'cancel_button' => 'Cancel',
    'back_button' => 'Go Back',

    // EGI Preview Section
    'egi_preview' => [
        'title' => 'EGI Preview',
        'creator_by' => 'Created by :name',
    ],

    // Blockchain Info Section
    'blockchain_info' => [
        'title' => 'Blockchain Information',
        'network' => 'Network',
        'network_value' => 'Algorand Mainnet',
        'token_type' => 'Token Type',
        'token_type_value' => 'ASA (Algorand Standard Asset)',
        'supply' => 'Supply',
        'supply_value' => '1 (Unique)',
        'royalty' => 'Royalty',
        'royalty_value' => ':percentage% to creator',
    ],

    // Payment Section
    'payment' => [
        'title' => 'Payment Details',
        'price_label' => 'Final Price',
        'currency' => 'Currency',
        'payment_method' => 'Payment Method',
        'payment_method_card' => 'Credit/Debit Card',
        'total_label' => 'Total to Pay',
    ],

    // Buyer Info Section
    'buyer_info' => [
        'title' => 'Buyer Information',
        'wallet_label' => 'Destination Algorand Wallet',
        'wallet_placeholder' => 'Enter your Algorand wallet address',
        'wallet_help' => 'The EGI will be transferred to this address after minting.',
        'verify_wallet' => 'Make sure the address is correct - it cannot be changed after minting.',
    ],

    // Confirmation
    'confirmation' => [
        'title' => 'Confirm Mint',
        'description' => 'You are about to mint this EGI. This operation is irreversible.',
        'agree_terms' => 'I agree to the terms and conditions',
        'final_warning' => 'Warning: Minting cannot be canceled after confirmation.',
    ],

    // Success Messages
    'success' => [
        'minted' => 'EGI minted successfully!',
        'transaction_id' => 'Transaction ID: :id',
        'view_on_explorer' => 'View on Algorand Explorer',
        'certificate_ready' => 'Certificate of authenticity is ready for download.',
    ],

    // Error Messages
    'errors' => [
        'missing_params' => 'Missing parameters for minting.',
        'invalid_reservation' => 'Invalid or expired reservation.',
        'already_minted' => 'This EGI has already been minted.',
        'payment_failed' => 'Payment failed. Please try again.',
        'mint_failed' => 'Minting failed. Please contact support.',
        'invalid_wallet' => 'Invalid wallet address.',
        'blockchain_error' => 'Blockchain error. Please try again later.',
    ],

    // Validation
    'validation' => [
        'wallet_required' => 'Wallet address is required.',
        'wallet_format' => 'Wallet address must be a valid Algorand address.',
        'terms_required' => 'You must accept the terms and conditions.',
    ],

    // MiCA Compliance
    'compliance' => [
        'mica_title' => '⚖️ MiCA Compliance',
        'mica_description' => 'This process is completely MiCA-SAFE. We pay in FIAT through authorized PSPs, mint the NFT on your behalf, and only handle temporary custody if necessary.',
    ],

    // Post-Mint Certificate
    'post_mint' => [
        'certificate_title' => 'Blockchain Ownership Certificate',
        'certificate_description' => 'Your official digital certificate with verified blockchain data.',
        'certificate_preview' => 'Certificate Preview',
        'loading_preview' => 'Loading preview...',
        'generating_pdf' => 'Generating PDF...',
        'download_certificate' => 'Download Certificate PDF',
        'regenerate_certificate' => 'Regenerate Certificate',
        'regenerate_success' => 'Certificate regenerated successfully',
        'generate_certificate' => 'Generate Certificate',
        'certificate_not_created_title' => 'Certificate Not Yet Created',
        'certificate_not_created_message' => 'The certificate was not automatically generated during mint. You can generate it manually by clicking the button below.',
        'certificate_owner_only' => 'Only the owner can generate the certificate.',
        'view_certificate' => 'View Certificate',
        'my_certificates' => 'My Certificates',
        'click_to_view' => 'Click to view full certificate',
    ],
];
