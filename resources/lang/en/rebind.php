<?php

/**
 * Rebind (Secondary Market) Translations - English
 */

return [
    'title' => 'Rebind - Secondary Market',
    'subtitle' => 'Purchase this EGI from the current owner',

    'checkout' => [
        'title' => 'Rebind Checkout',
        'current_owner' => 'Current Seller',
        'price_label' => 'Sale Price',
        'platform_fee' => 'Platform Fee',
        'total' => 'Total',
    ],

    'success' => [
        'purchase_initiated' => 'Purchase initiated successfully!',
        'purchase_completed' => 'Rebind completed! You are now the new owner.',
        'ownership_transferred' => 'Ownership transferred successfully.',
    ],

    'errors' => [
        'not_available' => 'This EGI is not available for Rebind.',
        'checkout_error' => 'Error during checkout. Please try again.',
        'process_error' => 'Error processing the Rebind.',
        'owner_cannot_buy' => 'You cannot buy an EGI you already own.',
        'not_minted' => 'This EGI has not been minted yet.',
        'not_for_sale' => 'This EGI is not for sale.',
        'invalid_price' => 'Invalid price for this EGI.',
        'payment_failed' => 'Payment failed. Please try again.',
        'insufficient_egili' => 'Insufficient EGILI balance for this purchase.',
        'egili_disabled' => 'EGILI payment is not available for this EGI.',
        'unauthorized' => 'You are not authorized to complete this purchase.',
        'merchant_not_configured' => 'The selected payment method is not available for this seller.',
        'validation_failed' => 'Invalid payment data. Please try again.',
    ],

    'process' => [
        'initiated' => 'Rebind process initiated.',
        'processing' => 'Processing payment...',
        'transferring' => 'Transferring ownership...',
    ],

    'info' => [
        'secondary_market' => 'Secondary Market',
        'secondary_market_desc' => 'You are purchasing this EGI from its current owner, not the original creator.',
        'blockchain_transfer' => 'Blockchain Transfer',
        'blockchain_transfer_desc' => 'Ownership will be transferred on the blockchain after payment.',
    ],
];
