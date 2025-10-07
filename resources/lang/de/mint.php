<?php

return [
    // Page Meta
    'page_title' => 'Mint :title - FlorenceEGI',
    'meta_description' => 'Präge dein EGI :title auf der Algorand Blockchain. Sicherer und transparenter Prozess.',

    // Header
    'header_title' => 'Präge dein EGI',
    'header_description' => 'Schließe deinen Kauf ab und präge dein EGI auf der Algorand Blockchain. Dieser Prozess ist unumkehrbar.',

    // Buttons
    'mint_button' => 'Prägen (:price)',
    'mint_button_processing' => 'Prägung läuft...',
    'cancel_button' => 'Abbrechen',
    'back_button' => 'Zurück',

    // EGI Preview Section
    'egi_preview' => [
        'title' => 'EGI Vorschau',
        'creator_by' => 'Erstellt von :name',
    ],

    // Blockchain Info Section
    'blockchain_info' => [
        'title' => 'Blockchain Informationen',
        'network' => 'Netzwerk',
        'network_value' => 'Algorand Mainnet',
        'token_type' => 'Token Typ',
        'token_type_value' => 'ASA (Algorand Standard Asset)',
        'supply' => 'Versorgung',
        'supply_value' => '1 (Einzigartig)',
        'royalty' => 'Lizenzgebühren',
        'royalty_value' => ':percentage% an den Ersteller',
    ],

    // Payment Section
    'payment' => [
        'title' => 'Zahlungsdetails',
        'price_label' => 'Endpreis',
        'currency' => 'Währung',
        'payment_method' => 'Zahlungsmethode',
        'payment_method_card' => 'Kredit-/Debitkarte',
        'total_label' => 'Zu zahlender Betrag',
    ],

    // Buyer Info Section
    'buyer_info' => [
        'title' => 'Käuferinformationen',
        'wallet_label' => 'Ziel-Algorand-Wallet',
        'wallet_placeholder' => 'Gib deine Algorand Wallet-Adresse ein',
        'wallet_help' => 'Das EGI wird nach der Prägung an diese Adresse übertragen.',
        'verify_wallet' => 'Stelle sicher, dass die Adresse korrekt ist - sie kann nach der Prägung nicht geändert werden.',
    ],

    // Confirmation
    'confirmation' => [
        'title' => 'Prägung bestätigen',
        'description' => 'Du bist dabei, dieses EGI zu prägen. Diese Operation ist unumkehrbar.',
        'agree_terms' => 'Ich stimme den Allgemeinen Geschäftsbedingungen zu',
        'final_warning' => 'Warnung: Die Prägung kann nach der Bestätigung nicht abgebrochen werden.',
    ],

    // Success Messages
    'success' => [
        'minted' => 'EGI erfolgreich geprägt!',
        'transaction_id' => 'Transaktions-ID: :id',
        'view_on_explorer' => 'Auf Algorand Explorer anzeigen',
        'certificate_ready' => 'Das Echtheitszertifikat ist zum Download bereit.',
    ],

    // Error Messages
    'errors' => [
        'missing_params' => 'Fehlende Parameter für die Prägung.',
        'invalid_reservation' => 'Ungültige oder abgelaufene Reservierung.',
        'already_minted' => 'Dieses EGI wurde bereits geprägt.',
        'payment_failed' => 'Zahlung fehlgeschlagen. Bitte versuche es erneut.',
        'mint_failed' => 'Prägung fehlgeschlagen. Bitte kontaktiere den Support.',
        'invalid_wallet' => 'Ungültige Wallet-Adresse.',
        'blockchain_error' => 'Blockchain-Fehler. Bitte versuche es später erneut.',
    ],

    // Validation
    'validation' => [
        'wallet_required' => 'Wallet-Adresse ist erforderlich.',
        'wallet_format' => 'Wallet-Adresse muss eine gültige Algorand-Adresse sein.',
        'terms_required' => 'Du musst den Allgemeinen Geschäftsbedingungen zustimmen.',
    ],
];
