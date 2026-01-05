<?php

return [
    // Page Meta
    'page_title' => 'Mint :title - FlorenceEGI',
    'minted_title' => 'EGI Minted: :title',
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
        'payment_method_label' => 'Payment Method',
        'payment_method_card' => 'Credit/Debit Card',
        'payment_method_egili' => 'Pay with Egili tokens',
        'total_label' => 'Total to Pay',
        'credit_card' => 'Credit/Debit Card',
        'paypal' => 'Pay with PayPal',
        'winning_reservation' => 'Winning reservation price',
        'egili_balance_label' => 'Current balance: :balance EGL',
        'egili_required_label' => 'Required for this mint: :required EGL',
        'egili_summary_title' => 'Egili overview',
        'egili_summary' => 'You need :required EGL to finalize this mint.',
        'egili_insufficient' => 'Egili balance insufficient. Recharge your tokens or select another method.',
        'submit_button' => 'Complete Payment',
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
        'invalid_amount' => 'Unable to calculate the mint amount. Contact support if the problem persists.',
        'insufficient_egili' => 'You do not have enough Egili to complete this mint.',
        'egili_disabled' => 'Egili payments are not enabled for this EGI.',
        'unauthorized' => 'You are not authorized to complete this mint.',
        'merchant_not_configured' => 'The creator has not completed the payment configuration for this provider. Contact the creator or choose another payment method.',
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
        'thumbnail_error' => 'Could not load preview',
        // Page header
        'congratulations' => 'Congratulations!',
        'title_guest' => 'EGI Details',
        'success_message' => 'Your EGI has been successfully minted on the blockchain.',
        'message_guest' => 'This EGI has been successfully minted on the blockchain.',
        'current_owner' => 'Current owner',
        'sale_price' => 'Sale Price',
        'view_egi' => 'View My EGI',
        // Blockchain section
        'blockchain_info' => 'Blockchain Info',
        'asa_id' => 'ASA ID',
        'tx_id' => 'Transaction ID',
        'minted_at' => 'Minted At',
        'view_pera_explorer' => 'View on Pera Explorer',
        // Payment breakdown
        'payment_breakdown' => 'Payment Distribution',
        'recipient' => 'Recipient',
        'role' => 'Role',
        'amount' => 'Amount',
    ],

    // Loading State (Polling for splits + certificate)
    'loading' => [
        'title' => 'Preparing Your Data',
        'message' => 'We are finalizing your blockchain transaction and preparing the certificate...',
        'mint_complete' => 'Blockchain mint complete',
        'waiting_splits' => 'Waiting for payment distribution...',
        'waiting_certificate' => 'Generating certificate...',
        'splits_ready' => 'Payment distribution ready',
        'certificate_ready' => 'Certificate ready',
        'timeout_message' => 'This is taking longer than expected. You can refresh the page if the data does not appear.',
    ],

    // Commodity specific labels
    'commodity' => [
        'types' => [
            'gold-bar' => 'Gold Bar',
            'goldbar' => 'Gold Bar',
            'silver-bar' => 'Silver Bar',
            'silverbar' => 'Silver Bar',
            'platinum-bar' => 'Platinum Bar',
            'platinumbar' => 'Platinum Bar',
        ],
        'section_details' => 'Physical Specifications',
        'serial_number' => 'Serial Number',
        'weight' => 'Weight',
        'purity' => 'Purity',
        'value_at_mint' => 'Value at Mint',
    ],
];
