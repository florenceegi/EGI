<?php

return [


    // Allgemeine UI-Labels
    'label_default'                   => 'Standard',
    'label_toggle'                    => 'Umschalten',
    'label_make_default'              => 'Als Standard setzen',
    // Banküberweisung
    'bank_account_holder'             => 'Kontoinhaber',
    'bank_holder_placeholder'         => 'Vollständiger Name wie auf dem Bankkonto',
    'bank_config_title'               => 'Bankkonto-Konfiguration',
    'bank_save_details'               => 'Bankdaten speichern',
    // Stripe
    'stripe_connected'                => 'Stripe-Konto verbunden',
    'stripe_connected_creator'        => 'Stripe-Konto verbunden (Creator-Ebene)',
    'stripe_collection_inherits'      => 'Die Sammlung übernimmt dein Stripe-Connect-Hauptkonto.',
    'stripe_connect_first'            => 'Bitte verbinde zunächst dein Stripe-Konto in den Haupteinstellungen.',
    'stripe_connect_cta'             => 'Stripe-Konto verbinden',
    // Sammlungseinstellungen
    'collection_settings_title'       => 'Zahlungseinstellungen der Sammlung',
    'collection_settings_description' => 'Standardzahlungsmethoden für diese Sammlung anpassen',


    // Stripe popup return page
    'popup_return_title'   => 'Verifizierung abgeschlossen',
    'popup_return_heading' => 'Verifizierung abgeschlossen',
    'popup_return_closing' => 'Dieses Fenster schließt sich automatisch',

    'wizard' => [
        'chip_label'  => 'Zahlungen aktivieren',
        'intro_title' => 'Zahlungssystem aktivieren',
        'intro_text'  => 'Um deine Werke zu verkaufen, musst du :psp_name aktivieren. Eine geführte Einrichtung, die nur wenige Minuten dauert.',
        'intro_note'  => 'Zahlungen gehen direkt auf dein Konto. FlorenceEGI hält kein Geld zurück.',
        'cta'         => ':psp_name aktivieren',
        'processing'  => 'Wird gestartet…',
        'link_failed' => 'Link konnte nicht erstellt werden. Bitte versuche es erneut.',
        'no_wallet'   => 'Kein Wallet konfiguriert. Bitte kontaktiere den Support.',
        'success'     => 'Zahlungen aktiviert! Du kannst jetzt deine Werke verkaufen.',
        'refresh'     => 'Der Link ist abgelaufen. Klicke erneut auf „Zahlungen aktivieren".',
        // Wizard 4-step popup
        'back'                => 'Zurück',
        'step1_next'          => 'Was brauche ich?',
        'step2_title'         => 'Was du für die Zahlungsaktivierung benötigst:',
        'step2_item1'         => 'Gültiges Ausweisdokument',
        'step2_item2'         => 'IBAN oder Bankkarte (für Auszahlungen)',
        'step2_item3'         => 'Ca. 5 Minuten deiner Zeit',
        'step2_next'          => 'Weiter',
        'step3_note'          => 'Es öffnet sich ein kleines sicheres Fenster. Schließe die Verifizierung ab und komm zurück — diese Seite bleibt geöffnet.',
        'step3_cta'           => ':psp_name Verifizierung öffnen',
        'popup_blocked'       => 'Dein Browser hat das Popup blockiert. Bitte erlaube Popups für FlorenceEGI und versuche es erneut.',
        'step4_checking'      => 'Status wird geprüft…',
        'step4_complete'      => 'Zahlungen aktiviert!',
        'step4_complete_hint' => 'Du kannst jetzt deine Werke verkaufen. Das Modal wird sich in Kürze aktualisieren.',
        'step4_pending'       => 'Verifizierung ausstehend',
        'step4_pending_hint'  => 'Deine Dokumente werden bearbeitet. Du erhältst eine Benachrichtigung, sobald alles fertig ist.',
        'step4_error'         => 'Etwas ist schiefgelaufen',
        'step4_retry'         => 'Erneut versuchen',
    ],

];
