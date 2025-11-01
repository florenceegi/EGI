<?php

/**
 * EGI Living (SmartContract) Translations - Italiano
 * 
 * @package Resources\Lang\IT
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Dual Architecture)
 * @date 2025-11-01
 */

return [
    'subscription' => [
        'one_time_name' => 'Attivazione EGI Vivente',
        'one_time_description' => 'Attivazione una tantum con feature premium lifetime',
    ],
    
    'payment' => [
        'title' => 'Attiva EGI Vivente',
        'subtitle' => 'Scegli il metodo di pagamento per attivare le feature premium',
        'description' => 'Pagamento subscription EGI Vivente #:egi_id',
        
        // Payment methods
        'method_fiat' => 'Pagamento FIAT (EUR)',
        'method_crypto' => 'Pagamento Crypto',
        'method_egili' => 'Pagamento Egili',
        
        // FIAT providers
        'fiat_stripe' => 'Carta di Credito (Stripe)',
        'fiat_paypal' => 'PayPal',
        
        // Crypto providers
        'crypto_coinbase' => 'Coinbase Commerce',
        'crypto_bitpay' => 'BitPay',
        'crypto_nowpayments' => 'NOWPayments',
        
        // Pricing
        'price_eur' => 'Prezzo',
        'price_egili' => 'Prezzo in Egili',
        'your_balance' => 'Il tuo saldo',
        'insufficient_egili' => 'Saldo Egili insufficiente',
        
        // Buttons
        'pay_now' => 'Paga Ora',
        'cancel' => 'Annulla',
        
        // Messages
        'egili_success' => 'EGI Vivente attivato con successo usando Egili!',
        'fiat_redirect' => 'Reindirizzamento al gateway di pagamento...',
        'crypto_redirect' => 'Reindirizzamento al gateway crypto...',
        'success' => 'EGI Vivente attivato con successo!',
        'cancelled' => 'Pagamento annullato',
        
        // Features
        'features_title' => 'Feature Incluse',
        'feature_curator' => 'AI Curator - Analisi automatica opera',
        'feature_promoter' => 'AI Promoter - Marketing intelligente',
        'feature_provenance' => 'Provenance Graph - Storia completa',
        'feature_passport' => 'Passaporto Espositivo - Registro eventi',
        'feature_anchoring' => 'Anchoring Automatico - Sicurezza blockchain',
    ],
    
    'already_active' => 'EGI Vivente è già attivo per questo EGI',
    
    'cta' => [
        'title' => '⚡ Attiva EGI Vivente',
        'subtitle' => 'Feature premium in attesa',
        'or_egili' => 'oppure',
        'activate_now' => '⚡ Attiva Ora',
        'lifetime' => 'Pagamento unico, feature lifetime',
    ],
    
    'features' => [
        'curator_short' => 'AI Curator automatico',
        'promoter_short' => 'Marketing intelligente',
        'provenance_short' => 'Provenance Graph completa',
    ],
    
    'curator' => [
        'title' => 'AI Curator',
        'description_short' => 'Analisi automatica opera',
        'run_now' => 'Esegui Curator',
    ],
    
    'promoter' => [
        'title' => 'AI Promoter',
        'description_short' => 'Marketing intelligente',
        'run_now' => 'Esegui Promoter',
    ],
    
    'provenance' => [
        'title' => 'Provenance Graph',
        'description_short' => 'Storia completa opera',
        'view' => 'Visualizza Provenance',
    ],
    
    'status' => [
        'active' => '✅ EGI Vivente Attivo',
        'active_since' => 'Attivo dal',
    ],
    
    'errors' => [
        'unauthorized' => 'Solo il creator può attivare EGI Vivente',
        'insufficient_balance' => 'Saldo Egili insufficiente',
        'payment_failed' => 'Pagamento fallito, riprova',
    ],
];

