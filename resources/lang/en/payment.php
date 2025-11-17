<?php

/**
 * Payment Common Translations - English
 * 
 * @package Resources\Lang\EN
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Payment System)
 * @date 2025-11-01
 */

return [
    'select_method' => 'Select payment method',
    'method_fiat' => 'FIAT Payment (EUR)',
    'method_crypto' => 'Crypto Payment',
    'method_egili' => 'Egili Payment',
    
    'provider_stripe' => 'Credit Card',
    'provider_paypal' => 'PayPal',
    
    'select_provider' => 'Select Provider',
    'select_crypto_provider' => 'Select Crypto Gateway',
    
    'crypto_coinbase' => 'Coinbase Commerce',
    'crypto_bitpay' => 'BitPay',
    'crypto_nowpayments' => 'NOWPayments',
    
    'your_balance' => 'Your balance',
    'your_egili_balance' => 'Your Egili balance',
    'insufficient_egili' => 'Insufficient Egili balance',
    'insufficient_egili_title' => 'Insufficient Egili balance',
    'need_more_egili' => 'You need :amount more Egili',
    'lifetime' => 'Lifetime access',
    
    'pay_now' => 'Pay Now',
    'cancel' => 'Cancel',
    
    'one_time_lifetime' => 'One-time payment, lifetime access',
    'recurring_monthly' => 'Monthly subscription',
    'recurring_yearly' => 'Yearly subscription',
    
    'crypto_dynamic' => 'Dynamic crypto price',
    
    // Error messages for payment processing
    'errors' => [
        'merchant_account_disabled' => 'Payments cannot be accepted for this content at the moment. The creator needs to complete their payment account setup.',
        'merchant_account_incomplete' => 'The creator\'s profile is incomplete. Please contact the creator to complete their account setup.',
        'some_wallets_invalid' => 'Some wallets associated with the collection are not properly configured. Please contact the creator to resolve this issue.',
        'stripe_disabled' => 'Stripe payments are temporarily disabled. Please use another payment method.',
        'paypal_disabled' => 'PayPal payments are temporarily disabled. Please use another payment method.',
        'invalid_request' => 'Invalid payment request. Please check your information and try again.',
        'api_error' => 'A temporary error occurred with the payment system. Please try again in a few minutes.',
        'card_error' => 'Your credit card was declined. Please verify your card details or use another payment method.',
        'authentication_error' => 'Authentication error with the payment system. Please contact support.',
        'rate_limit' => 'Too many payment attempts. Please try again in a few minutes.',
        'generic_error' => 'An error occurred while processing your payment. Please try again later.',
        'paypal_not_implemented' => 'PayPal is not yet available. Please use another payment method.',
        'paypal_not_configured' => 'PayPal is not configured for this creator.',
    ],
];

