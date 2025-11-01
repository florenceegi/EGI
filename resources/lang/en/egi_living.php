<?php

/**
 * EGI Living (SmartContract) Translations - English
 * 
 * @package Resources\Lang\EN
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Dual Architecture)
 * @date 2025-11-01
 */

return [
    'subscription' => [
        'one_time_name' => 'EGI Living Activation',
        'one_time_description' => 'One-time activation with lifetime premium features',
    ],
    
    'payment' => [
        'title' => 'Activate EGI Living',
        'subtitle' => 'Choose payment method to activate premium features',
        'description' => 'EGI Living subscription payment #:egi_id',
        
        // Payment methods
        'method_fiat' => 'FIAT Payment (EUR)',
        'method_crypto' => 'Crypto Payment',
        'method_egili' => 'Egili Payment',
        
        // FIAT providers
        'fiat_stripe' => 'Credit Card (Stripe)',
        'fiat_paypal' => 'PayPal',
        
        // Crypto providers
        'crypto_coinbase' => 'Coinbase Commerce',
        'crypto_bitpay' => 'BitPay',
        'crypto_nowpayments' => 'NOWPayments',
        
        // Pricing
        'price_eur' => 'Price',
        'price_egili' => 'Price in Egili',
        'your_balance' => 'Your balance',
        'insufficient_egili' => 'Insufficient Egili balance',
        
        // Buttons
        'pay_now' => 'Pay Now',
        'cancel' => 'Cancel',
        
        // Messages
        'egili_success' => 'EGI Living activated successfully using Egili!',
        'fiat_redirect' => 'Redirecting to payment gateway...',
        'crypto_redirect' => 'Redirecting to crypto gateway...',
        'success' => 'EGI Living activated successfully!',
        'cancelled' => 'Payment cancelled',
        
        // Features
        'features_title' => 'Included Features',
        'feature_curator' => 'AI Curator - Automatic artwork analysis',
        'feature_promoter' => 'AI Promoter - Smart marketing',
        'feature_provenance' => 'Provenance Graph - Complete history',
        'feature_passport' => 'Exhibition Passport - Event registry',
        'feature_anchoring' => 'Automatic Anchoring - Blockchain security',
    ],
    
    'already_active' => 'EGI Living is already active for this EGI',
    
    'errors' => [
        'unauthorized' => 'Only the creator can activate EGI Living',
        'insufficient_balance' => 'Insufficient Egili balance',
        'payment_failed' => 'Payment failed, please try again',
    ],
];

