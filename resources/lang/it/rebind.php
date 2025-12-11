<?php

/**
 * Rebind (Secondary Market) Translations - Italian
 */

return [
    'title' => 'Rebind - Mercato Secondario',
    'subtitle' => 'Acquista questo EGI dal proprietario attuale',

    'checkout' => [
        'title' => 'Checkout Rebind',
        'current_owner' => 'Venditore Attuale',
        'price_label' => 'Prezzo di Vendita',
        'platform_fee' => 'Commissione Piattaforma',
        'total' => 'Totale',
    ],

    'success' => [
        'purchase_initiated' => 'Acquisto avviato con successo!',
        'purchase_completed' => 'Rebind completato! Ora sei il nuovo proprietario.',
        'ownership_transferred' => 'Proprietà trasferita con successo.',
    ],

    'errors' => [
        'not_available' => 'Questo EGI non è disponibile per il Rebind.',
        'checkout_error' => 'Errore durante il checkout. Riprova.',
        'process_error' => 'Errore durante l\'elaborazione del Rebind.',
        'owner_cannot_buy' => 'Non puoi acquistare un EGI di cui sei già proprietario.',
        'not_minted' => 'Questo EGI non è ancora stato mintato.',
        'not_for_sale' => 'Questo EGI non è in vendita.',
        'invalid_price' => 'Prezzo non valido per questo EGI.',
        'payment_failed' => 'Pagamento non riuscito. Riprova.',
        'insufficient_egili' => 'Saldo EGILI insufficiente per questo acquisto.',
        'egili_disabled' => 'Il pagamento in EGILI non è disponibile per questo EGI.',
        'unauthorized' => 'Non sei autorizzato a completare questo acquisto.',
        'merchant_not_configured' => 'Il metodo di pagamento selezionato non è disponibile per questo venditore.',
        'validation_failed' => 'Dati di pagamento non validi. Riprova.',
    ],

    'process' => [
        'initiated' => 'Processo di Rebind avviato.',
        'processing' => 'Elaborazione del pagamento in corso...',
        'transferring' => 'Trasferimento proprietà in corso...',
    ],

    'info' => [
        'secondary_market' => 'Mercato Secondario',
        'secondary_market_desc' => 'Stai acquistando questo EGI dal suo proprietario attuale, non dal creatore originale.',
        'blockchain_transfer' => 'Trasferimento Blockchain',
        'blockchain_transfer_desc' => 'La proprietà verrà trasferita sulla blockchain dopo il pagamento.',
    ],
];
