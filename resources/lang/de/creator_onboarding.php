<?php

return [
    'page' => [
        'title' => 'Onboarding-Übersicht für Creator',
        'description' => 'Überprüfe den Status deiner Zahlungen, des Algorand-Wallets und der FlorenceEGI-Compliance.',
        'heading' => 'Willkommen im Creator Control Center',
        'intro' => 'Danke, dass du die ersten Schritte abgeschlossen hast. Diese Seite zeigt eine operative Übersicht zu Zahlungen, Algorand-Verwahrung und nächsten Schritten.',
    ],
    'profile' => [
        'title' => 'Profil und Wallet',
        'user_name' => 'Vollständiger Name',
        'user_email' => 'E-Mail',
        'user_type' => 'Benutzerrolle',
        'wallet_address' => 'Algorand-Wallet',
        'iban_masked' => 'Registriertes IBAN',
        'iban_missing' => 'Kein IBAN hinterlegt. Füge eines hinzu, um Auszahlungen zu erhalten.',
    ],
    'stripe' => [
        'title' => 'Stripe-Connect-Konto',
        'account_id' => 'Konto-ID',
        'status' => 'Statusübersicht',
        'charges_enabled' => 'Kartenzahlungen aktiviert',
        'payouts_enabled' => 'Auszahlungen aktiviert',
        'details_submitted' => 'Verifizierung abgeschlossen',
        'status_ready' => 'Bereit, Zahlungen anzunehmen und Auszahlungen zu erhalten',
        'status_pending' => 'Weitere Aktionen erforderlich, bevor Auszahlungen freigeschaltet werden',
        'cta_onboarding' => 'Stripe-Onboarding abschließen',
        'cta_dashboard' => 'Stripe-Express-Dashboard öffnen',
        'onboarding_hint' => 'Stripe kann vor der Freigabe der Auszahlungen steuerliche oder Identitätsdokumente anfordern.',
    ],
    'actions' => [
        'title' => 'Nächste Schritte',
        'checklist' => [
            'onboarding' => 'Schließe den Stripe-Onboarding-Prozess ab, falls noch offen.',
            'documents' => 'Bereite Steuer- und Identitätsdokumente für die KYC-Prüfung vor.',
            'pricing' => 'Konfiguriere Preise und Egili-Optionen für deine EGIs.',
            'compliance' => 'Prüfe die MiCA-safe- und PA-Richtlinien für dein Ökosystem.',
        ],
    ],
    'pera' => [
        'title' => 'Algorand Pera Wallet & Verwahrung',
        'intro' => 'Das Algorand-Wallet bleibt in der Obhut von FlorenceEGI, bis du die zertifizierte Übergabe anforderst.',
        'request' => 'Um die Pera-Geheimphrase zu erhalten, öffne ein Ticket im Support Center und vereinbare einen Identitäts-Check mit unserem Team.',
        'note' => 'Bis zur Übergabe signiert FlorenceEGI On-Chain-Transaktionen in deinem Namen, während Fiat-Erlöse direkt auf dein Stripe-Konto gehen.',
    ],
    'badges' => [
        'ready' => 'Bereit',
        'pending' => 'Aktion erforderlich',
        'missing' => 'Konfiguration nötig',
    ],
    'buttons' => [
        'refresh' => 'Status aktualisieren',
        'support' => 'FlorenceEGI-Support kontaktieren',
    ],
];

