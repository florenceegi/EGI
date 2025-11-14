<?php

return [
    'page' => [
        'title' => 'Creator onboarding summary',
        'description' => 'Review your FlorenceEGI payout, wallet and compliance status.',
        'heading' => 'Welcome to your creator control center',
        'intro' => 'Thank you for completing the first setup steps. This page summarizes payments, Algorand custody and your immediate next actions.',
    ],
    'profile' => [
        'title' => 'Your profile and wallet',
        'user_name' => 'Full name',
        'user_email' => 'Email',
        'user_type' => 'User role',
        'wallet_address' => 'Algorand wallet',
        'iban_masked' => 'Registered IBAN',
        'iban_missing' => 'No IBAN configured. Add one to receive payouts.',
    ],
    'stripe' => [
        'title' => 'Stripe Connect account',
        'account_id' => 'Account ID',
        'status' => 'Status overview',
        'charges_enabled' => 'Card payments enabled',
        'payouts_enabled' => 'Payouts enabled',
        'details_submitted' => 'Verification completed',
        'status_ready' => 'Ready to accept payments and payouts',
        'status_pending' => 'Action required before payouts are enabled',
        'cta_onboarding' => 'Complete Stripe onboarding',
        'cta_dashboard' => 'Open Stripe Express dashboard',
        'onboarding_hint' => 'Stripe may ask you to upload identity or tax documents before payouts are released.',
    ],
    'actions' => [
        'title' => 'Next steps',
        'checklist' => [
            'onboarding' => 'Complete the Stripe onboarding flow if it is still pending.',
            'documents' => 'Gather identity and tax documents requested by Stripe for KYC.',
            'pricing' => 'Configure prices and Egili payment options for your EGIs.',
            'compliance' => 'Review MiCA-safe and PA compliance guidelines for your ecosystem.',
        ],
    ],
    'pera' => [
        'title' => 'Algorand Pera Wallet & custody',
        'intro' => 'Your Algorand wallet is kept in secure custody by FlorenceEGI until you request the certified handover.',
        'request' => 'To receive the Pera Wallet secret phrase, open a ticket in the Support Center and schedule an identity verification session with our team.',
        'note' => 'Until the handover is completed, FlorenceEGI signs on-chain transactions on your behalf while fiat proceeds go directly to your Stripe account.',
    ],
    'badges' => [
        'ready' => 'Ready',
        'pending' => 'Action required',
        'missing' => 'Configuration needed',
    ],
    'buttons' => [
        'refresh' => 'Refresh status',
        'support' => 'Contact FlorenceEGI support',
    ],
];

