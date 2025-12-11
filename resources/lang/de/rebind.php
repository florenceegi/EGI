<?php

/**
 * Rebind (Secondary Market) Translations - German
 */

return [
    'title' => 'Rebind - Sekundärmarkt',
    'subtitle' => 'Kaufe dieses EGI vom aktuellen Besitzer',

    'checkout' => [
        'title' => 'Rebind Checkout',
        'current_owner' => 'Aktueller Verkäufer',
        'price_label' => 'Verkaufspreis',
        'platform_fee' => 'Plattformgebühr',
        'total' => 'Gesamt',
    ],

    'success' => [
        'purchase_initiated' => 'Kauf erfolgreich gestartet!',
        'purchase_completed' => 'Rebind abgeschlossen! Du bist jetzt der neue Besitzer.',
        'ownership_transferred' => 'Eigentum erfolgreich übertragen.',
    ],

    'errors' => [
        'not_available' => 'Dieses EGI ist nicht für Rebind verfügbar.',
        'checkout_error' => 'Fehler beim Checkout. Bitte versuche es erneut.',
        'process_error' => 'Fehler bei der Verarbeitung des Rebind.',
        'owner_cannot_buy' => 'Du kannst kein EGI kaufen, das dir bereits gehört.',
        'not_minted' => 'Dieses EGI wurde noch nicht gemintet.',
        'not_for_sale' => 'Dieses EGI steht nicht zum Verkauf.',
        'invalid_price' => 'Ungültiger Preis für dieses EGI.',
        'payment_failed' => 'Zahlung fehlgeschlagen. Bitte versuche es erneut.',
        'insufficient_egili' => 'Unzureichendes EGILI-Guthaben für diesen Kauf.',
        'egili_disabled' => 'EGILI-Zahlung ist für dieses EGI nicht verfügbar.',
        'unauthorized' => 'Du bist nicht berechtigt, diesen Kauf abzuschließen.',
        'merchant_not_configured' => 'Die gewählte Zahlungsmethode ist für diesen Verkäufer nicht verfügbar.',
        'validation_failed' => 'Ungültige Zahlungsdaten. Bitte versuche es erneut.',
    ],

    'process' => [
        'initiated' => 'Rebind-Prozess gestartet.',
        'processing' => 'Zahlung wird verarbeitet...',
        'transferring' => 'Eigentum wird übertragen...',
    ],

    'info' => [
        'secondary_market' => 'Sekundärmarkt',
        'secondary_market_desc' => 'Du kaufst dieses EGI vom aktuellen Besitzer, nicht vom ursprünglichen Ersteller.',
        'blockchain_transfer' => 'Blockchain-Transfer',
        'blockchain_transfer_desc' => 'Das Eigentum wird nach der Zahlung auf der Blockchain übertragen.',
    ],
];
