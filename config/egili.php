<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Egili Purchase Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Egili utility token purchasing system.
    |
    */

    'purchase' => [

        /**
         * Unit price per Egili in EUR
         *
         * Default: €0.01/Egili
         */
        'unit_price_eur' => env('EGILI_UNIT_PRICE_EUR', 0.01),

        /**
         * Minimum purchase amount (Egili)
         *
         * Default: 5000 Egili = €50.00
         */
        'min_amount' => env('EGILI_MIN_PURCHASE', 5000),

        /**
         * Maximum purchase amount (Egili)
         *
         * Default: 1,000,000 Egili = €10,000.00
         */
        'max_amount' => env('EGILI_MAX_PURCHASE', 1000000),

        /**
         * Bulk discount tiers (FASE 2 - Not yet implemented)
         *
         * Example:
         * [
         *     50000 => 0.05,   // 5% discount for 50k+ Egili
         *     100000 => 0.10,  // 10% discount for 100k+ Egili
         * ]
         */
        'bulk_discounts' => [],
    ],

    /**
     * Payment providers configuration
     */
    'payment_providers' => [

        /**
         * FIAT providers
         */
        'fiat' => [
            'stripe' => [
                'enabled' => env('EGILI_FIAT_STRIPE_ENABLED', true),
                'display_name' => 'Stripe (Card)',
            ],
            'paypal' => [
                'enabled' => env('EGILI_FIAT_PAYPAL_ENABLED', true),
                'display_name' => 'PayPal',
            ],
        ],

        /**
         * Crypto providers
         */
        'crypto' => [
            'coinbase_commerce' => [
                'enabled' => env('EGILI_CRYPTO_COINBASE_ENABLED', true),
                'display_name' => 'Coinbase Commerce',
            ],
            'bitpay' => [
                'enabled' => env('EGILI_CRYPTO_BITPAY_ENABLED', false),
                'display_name' => 'BitPay',
            ],
            'nowpayments' => [
                'enabled' => env('EGILI_CRYPTO_NOWPAYMENTS_ENABLED', false),
                'display_name' => 'NOWPayments',
            ],
        ],
    ],

    /**
     * Invoice configuration (FASE 2)
     */
    'invoices' => [

        /**
         * Enable automatic invoice generation
         */
        'auto_generate' => env('EGILI_AUTO_INVOICE', false),

        /**
         * Invoice generation frequency
         *
         * Options: 'instant', 'monthly'
         * Default: 'monthly' (aggregate by month)
         */
        'frequency' => env('EGILI_INVOICE_FREQUENCY', 'monthly'),

        /**
         * Invoice issuer details (for electronic invoices)
         */
        'issuer' => [
            'company_name' => env('EGILI_INVOICE_COMPANY_NAME', 'FlorenceEGI S.r.l.'),
            'vat_number' => env('EGILI_INVOICE_VAT_NUMBER', ''),
            'address' => env('EGILI_INVOICE_ADDRESS', ''),
            'city' => env('EGILI_INVOICE_CITY', ''),
            'postal_code' => env('EGILI_INVOICE_POSTAL_CODE', ''),
            'country' => env('EGILI_INVOICE_COUNTRY', 'IT'),
        ],

        /**
         * Storage path for invoice PDFs (relative to storage/app)
         */
        'storage_path' => 'invoices/egili',
    ],

    /**
     * Email notifications
     */
    'notifications' => [

        /**
         * Send email after successful purchase
         */
        'send_purchase_confirmation' => env('EGILI_SEND_PURCHASE_EMAIL', true),

        /**
         * Send email when invoice is generated (FASE 2)
         */
        'send_invoice_email' => env('EGILI_SEND_INVOICE_EMAIL', false),
    ],

];
