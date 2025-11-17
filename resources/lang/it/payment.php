<?php

/**
 * Payment Common Translations - Italiano
 * 
 * @package Resources\Lang\IT
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Payment System)
 * @date 2025-11-01
 */

return [
    'select_method' => 'Seleziona metodo di pagamento',
    'method_fiat' => 'Pagamento FIAT (EUR)',
    'method_crypto' => 'Pagamento Crypto',
    'method_egili' => 'Pagamento Egili',

    'provider_stripe' => 'Carta di Credito',
    'provider_paypal' => 'PayPal',

    'select_provider' => 'Seleziona Provider',
    'select_crypto_provider' => 'Seleziona Gateway Crypto',

    'crypto_coinbase' => 'Coinbase Commerce',
    'crypto_bitpay' => 'BitPay',
    'crypto_nowpayments' => 'NOWPayments',

    'your_balance' => 'Il tuo saldo',
    'your_egili_balance' => 'Il tuo saldo Egili',
    'insufficient_egili' => 'Saldo Egili insufficiente',
    'insufficient_egili_title' => 'Saldo Egili insufficiente',
    'need_more_egili' => 'Ti servono altri :amount Egili',
    'lifetime' => 'Accesso lifetime',

    'pay_now' => 'Paga Ora',
    'cancel' => 'Annulla',

    'one_time_lifetime' => 'Pagamento unico, accesso lifetime',
    'recurring_monthly' => 'Abbonamento mensile',
    'recurring_yearly' => 'Abbonamento annuale',

    'crypto_dynamic' => 'Prezzo dinamico in crypto',

    // Error messages for payment processing
    'errors' => [
        'merchant_account_disabled' => 'Al momento non è possibile accettare pagamenti per questo contenuto. Il creator deve completare la configurazione del proprio account di pagamento.',
        'merchant_account_incomplete' => 'Il profilo del creator non è completo. Contatta il creator per completare la configurazione dell\'account.',
        'some_wallets_invalid' => 'Alcuni wallet associati alla collection non sono configurati correttamente. Contatta il creator per risolvere il problema.',
        'stripe_disabled' => 'I pagamenti con Stripe sono temporaneamente disabilitati. Usa un altro metodo di pagamento.',
        'paypal_disabled' => 'I pagamenti con PayPal sono temporaneamente disabilitati. Usa un altro metodo di pagamento.',
        'invalid_request' => 'Richiesta di pagamento non valida. Verifica i dati inseriti e riprova.',
        'api_error' => 'Si è verificato un errore temporaneo con il sistema di pagamento. Riprova tra qualche minuto.',
        'card_error' => 'La carta di credito è stata rifiutata. Verifica i dati della carta o usa un altro metodo di pagamento.',
        'authentication_error' => 'Errore di autenticazione con il sistema di pagamento. Contatta l\'assistenza.',
        'rate_limit' => 'Troppi tentativi di pagamento. Riprova tra qualche minuto.',
        'generic_error' => 'Si è verificato un errore durante l\'elaborazione del pagamento. Riprova più tardi.',
        'paypal_not_implemented' => 'PayPal non è ancora disponibile. Usa un altro metodo di pagamento.',
        'paypal_not_configured' => 'PayPal non è configurato per questo creator.',
    ],
];