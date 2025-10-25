<?php

/**
 * Reservierungsnachrichten
 * @package FlorenceEGI
 * @subpackage Übersetzungen
 * @language de
 * @version 2.0.0
 */

return [
    // Erfolgsmeldungen
    'success' => 'Ihre Reservierung wurde erfolgreich durchgeführt! Das Zertifikat wurde erstellt.',
    'cancel_success' => 'Ihre Reservierung wurde erfolgreich storniert.',
    'success_title' => 'Reservierung durchgeführt!',
    'view_certificate' => 'Zertifikat anzeigen',
    'close' => 'Schließen',

    // Fehlermeldungen
    'unauthorized' => 'Sie müssen Ihr Wallet verbinden oder sich anmelden, um eine Reservierung vorzunehmen.',
    'validation_failed' => 'Bitte überprüfen Sie die eingegebenen Daten und versuchen Sie es erneut.',
    'auth_required' => 'Eine Authentifizierung ist erforderlich, um Ihre Reservierungen anzuzeigen.',
    'list_failed' => 'Ihre Reservierungen konnten nicht abgerufen werden. Bitte versuchen Sie es später erneut.',
    'status_failed' => 'Der Reservierungsstatus konnte nicht abgerufen werden. Bitte versuchen Sie es später erneut.',
    'unauthorized_cancel' => 'Sie haben keine Berechtigung, diese Reservierung zu stornieren.',
    'cancel_failed' => 'Die Reservierung konnte nicht storniert werden. Bitte versuchen Sie es später erneut.',

    // UI-Schaltflächen
    'button' => [
        'reserve' => 'Reservieren',
        'reserved' => 'Reserviert',
        'make_offer' => 'Angebot machen'
    ],

    // Badges
    'badge' => [
        'highest' => 'Höchste Priorität',
        'superseded' => 'Niedrigere Priorität',
        'has_offers' => 'Reserviert'
    ],

    // Reservierungsdetails
    'already_reserved' => [
        'title' => 'Bereits Reserviert',
        'text' => 'Sie haben bereits eine Reservierung für dieses EGI.',
        'details' => 'Details Ihrer Reservierung:',
        'type' => 'Typ',
        'amount' => 'Betrag',
        'status' => 'Status',
        'view_certificate' => 'Zertifikat anzeigen',
        'ok' => 'OK',
        'new_reservation' => 'Neue Reservierung',
        'confirm_new' => 'Möchten Sie eine neue Reservierung vornehmen?'
    ],

    // Reservierungshistorie
    'history' => [
        'title' => 'Reservierungshistorie',
        'entries' => 'Reservierungseinträge',
        'view_certificate' => 'Zertifikat anzeigen',
        'no_entries' => 'Keine Reservierungen gefunden.',
        'be_first' => 'Seien Sie der Erste, der dieses EGI reserviert!',
        'purchases_offers_title' => 'Kaufhistorie / Angebote'
    ],

    // Fehlermeldungen
    'errors' => [
        'button_click_error' => 'Bei der Bearbeitung Ihrer Anfrage ist ein Fehler aufgetreten.',
        'form_validation' => 'Bitte überprüfen Sie die eingegebenen Daten und versuchen Sie es erneut.',
        'api_error' => 'Bei der Kommunikation mit dem Server ist ein Fehler aufgetreten.',
        'unauthorized' => 'Sie müssen Ihr Wallet verbinden oder sich anmelden, um eine Reservierung vorzunehmen.'
    ],

    // Formular
    'form' => [
        'title' => 'Dieses EGI reservieren',
        'offer_amount_label' => 'Ihr Angebot (EUR)',
        'offer_amount_placeholder' => 'Betrag in EUR eingeben',
        'algo_equivalent' => 'Etwa :amount ALGO',
        'terms_accepted' => 'Ich akzeptiere die Geschäftsbedingungen für EGI-Reservierungen',
        'contact_info' => 'Zusätzliche Kontaktinformationen (Optional)',
        'submit_button' => 'Reservierung durchführen',
        'cancel_button' => 'Abbrechen'
    ],

    // Reservierungstyp
    'type' => [
        'strong' => 'Starke Reservierung',
        'weak' => 'Schwache Reservierung'
    ],

    // Prioritätsstufen
    'priority' => [
        'highest' => 'Aktive Reservierung',
        'superseded' => 'Übertroffen',
    ],

    // Reservierungsstatus
    'status' => [
        'active' => 'Aktiv',
        'pending' => 'Ausstehend',
        'cancelled' => 'Storniert',
        'expired' => 'Abgelaufen'
    ],

    // === NEUE SEKTION: BENACHRICHTIGUNGEN ===
    'notifications' => [
        'reservation_expired' => 'Ihre Reservierung von €:amount für :egi_title ist abgelaufen.',
        'superseded' => 'Ihr Angebot für :egi_title wurde übertroffen. Neues höchstes Angebot: €:new_highest_amount',
        'highest' => 'Herzlichen Glückwunsch! Ihr Angebot von €:amount für :egi_title ist jetzt das höchste!',
        'rank_changed' => 'Ihre Position für :egi_title hat sich geändert: Sie sind jetzt auf Position #:new_rank',
        'competitor_withdrew' => 'Ein Konkurrent hat sich zurückgezogen. Sie sind auf Position #:new_rank für :egi_title aufgerückt',
        'pre_launch_reminder' => 'Das On-Chain-Mint beginnt bald! Bestätigen Sie Ihre Reservierung für :egi_title.',
        'mint_window_open' => 'Sie sind dran! Sie haben 48 Stunden Zeit, um das Mint von :egi_title abzuschließen.',
        'mint_window_closing' => 'Achtung! Nur noch :hours_remaining Stunden, um das Mint von :egi_title abzuschließen.',
        'default' => 'Update zu Ihrer Reservierung für :egi_title',
        'archived_success' => 'Benachrichtigung erfolgreich archiviert.'
    ],
];
