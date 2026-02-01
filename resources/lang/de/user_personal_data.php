<?php

/**
 * @Oracode Translation File: Personal Data Management - German
 * 🎯 Purpose: Complete German translations for GDPR-compliant personal data management
 * 🛡️ Privacy: GDPR-compliant notices, consent language, data subject rights
 * 🌐 i18n: Base language file for FlorenceEGI personal data domain
 * 🧱 Core Logic: Supports all personal data CRUD operations with privacy notices
 * ⏰ MVP: Critical for German market compliance and user trust
 *
 * @package Lang\De
 * @author Padmin D. Curtis (AI Partner OS1-Compliant)
 * @version 1.0.0 (FlorenceEGI MVP - GDPR Native)
 * @deadline 2025-06-30
 */

return [
    // SEITENTITEL UND KOPFZEILEN
    'management_title' => 'Verwaltung Persönlicher Daten',
    'management_subtitle' => 'Verwalte deine persönlichen Daten gemäß DSGVO',
    'edit_title' => 'Persönliche Daten Bearbeiten',
    'edit_subtitle' => 'Aktualisiere deine persönlichen Daten sicher',
    'export_title' => 'Persönliche Daten Exportieren',
    'export_subtitle' => 'Lade eine vollständige Kopie deiner persönlichen Daten herunter',
    'deletion_title' => 'Anfrage Datenlöschung',
    'deletion_subtitle' => 'Fordere die dauerhafte Löschung deiner persönlichen Daten an',

    // FORMULARABSCHNITTE
    'basic_information' => 'Grundlegende Informationen',
    'basic_description' => 'Wesentliche Identifikationsdaten',
    'fiscal_information' => 'Steuerinformationen',
    'fiscal_description' => 'Steuernummer und Angaben zur Erfüllung gesetzlicher Pflichten',
    'address_information' => 'Adressinformationen',
    'address_description' => 'Wohnsitz- und Meldeadresse',
    'contact_information' => 'Kontaktinformationen',
    'contact_description' => 'Telefon und weitere Kontaktdaten',
    'identity_verification' => 'Identitätsprüfung',
    'identity_description' => 'Verifiziere deine Identität für sensible Änderungen',

    // ALLGEMEIN
    'anonymous_user' => 'Anonymer Benutzer',

    // FORMULARFELDER
    'first_name' => 'Vorname',
    'first_name_placeholder' => 'Gib deinen Vornamen ein',
    'last_name' => 'Nachname',
    'last_name_placeholder' => 'Gib deinen Nachnamen ein',
    'birth_date' => 'Geburtsdatum',
    'birth_date_placeholder' => 'Wähle dein Geburtsdatum',
    'birth_place' => 'Geburtsort',
    'birth_place_placeholder' => 'Stadt und Bundesland des Geburtsorts',
    'gender' => 'Geschlecht',
    'gender_male' => 'Männlich',
    'gender_female' => 'Weiblich',
    'gender_other' => 'Anderes',
    'gender_prefer_not_say' => 'Keine Angabe',

    // Steuerfelder
    'tax_code' => 'Steuernummer',
    'tax_code_placeholder' => 'RSSMRA80A01H501X',
    'tax_code_help' => 'Deine italienische Steuernummer (16 Zeichen)',
    'id_card_number' => 'Personalausweisnummer',
    'id_card_number_placeholder' => 'Nummer des Ausweisdokuments',
    'passport_number' => 'Reisepassnummer',
    'passport_number_placeholder' => 'Reisepassnummer (falls vorhanden)',
    'driving_license' => 'Führerschein',
    'driving_license_placeholder' => 'Führerscheinnummer',

    // Adressfelder
    'street_address' => 'Adresse',
    'street_address_placeholder' => 'Straße, Hausnummer',
    'city' => 'Stadt',
    'city_placeholder' => 'Name der Stadt',
    'postal_code' => 'Postleitzahl',
    'postal_code_placeholder' => '00100',
    'province' => 'Bundesland',
    'province_placeholder' => 'Bundeslandkürzel (z.B. BY)',
    'region' => 'Region',
    'region_placeholder' => 'Name der Region',
    'country' => 'Land',
    'country_placeholder' => 'Land auswählen',

    // Kontaktfelder
    'phone' => 'Telefon',
    'phone_placeholder' => '+49 1512 3456789',
    'mobile' => 'Mobil',
    'mobile_placeholder' => '+49 1512 3456789',
    'emergency_contact' => 'Notfallkontakt',
    'emergency_contact_placeholder' => 'Name und Telefonnummer',

    // DATENSCHUTZ UND EINWILLIGUNG
    'consent_management' => 'Einwilligungsverwaltung',
    'consent_description' => 'Verwalte deine Einwilligungen zur Datenverarbeitung',
    'consent_required' => 'Erforderliche Einwilligung',
    'consent_optional' => 'Optionale Einwilligung',
    'consent_marketing' => 'Marketing und Kommunikation',
    'consent_marketing_desc' => 'Einwilligung für den Erhalt von Marketingnachrichten',
    'consent_profiling' => 'Profiling',
    'consent_profiling_desc' => 'Einwilligung für Profiling und Analysen',
    'consent_analytics' => 'Analysen',
    'consent_analytics_desc' => 'Einwilligung für anonyme statistische Analysen',
    'consent_third_party' => 'Dritte Parteien',
    'consent_third_party_desc' => 'Einwilligung zur Weitergabe an ausgewählte Partner',

    // AKTIONEN UND BUTTONS
    'update_data' => 'Daten Aktualisieren',
    'save_changes' => 'Änderungen Speichern',
    'cancel_changes' => 'Abbrechen',
    'export_data' => 'Daten Exportieren',
    'request_deletion' => 'Löschung Beantragen',
    'verify_identity' => 'Identität Verifizieren',
    'confirm_changes' => 'Änderungen Bestätigen',
    'back_to_profile' => 'Zurück zum Profil',

    // ERFOLGS- UND FEHLERMELDUNGEN
    'update_success' => 'Persönliche Daten erfolgreich aktualisiert',
    'update_error' => 'Fehler bei der Aktualisierung der persönlichen Daten',
    'validation_error' => 'Einige Felder enthalten Fehler. Bitte überprüfe sie und versuche es erneut.',
    'identity_verification_required' => 'Identitätsprüfung für diese Aktion erforderlich',
    'identity_verification_failed' => 'Identitätsprüfung fehlgeschlagen. Bitte erneut versuchen.',
    'export_started' => 'Datenexport gestartet. Du erhältst eine E-Mail, sobald er bereit ist.',
    'export_ready' => 'Dein Datenexport ist bereit zum Download',
    'deletion_requested' => 'Löschanfrage eingereicht. Wird innerhalb von 30 Tagen bearbeitet.',

    // VALIDIERUNGSMELDUNGEN
    'validation' => [
        'first_name_required' => 'Vorname ist erforderlich',
        'last_name_required' => 'Nachname ist erforderlich',
        'birth_date_required' => 'Geburtsdatum ist erforderlich',
        'birth_date_valid' => 'Geburtsdatum muss gültig sein',
        'birth_date_age' => 'Du musst mindestens 13 Jahre alt sein, um dich zu registrieren',
        'tax_code_invalid' => 'Steuernummer ist ungültig',
        'tax_code_format' => 'Steuernummer muss 16 Zeichen haben',
        'phone_invalid' => 'Telefonnummer ist ungültig',
        'postal_code_invalid' => 'Postleitzahl ist für das ausgewählte Land ungültig',
        'country_required' => 'Land ist erforderlich',
    ],

    // DSGVO-HINWEISE
    'gdpr_notices' => [
        'data_processing_info' => 'Deine persönlichen Daten werden gemäß DSGVO (EU) 2016/679 verarbeitet',
        'data_controller' => 'Verantwortlicher: FlorenceEGI S.r.l.',
        'data_purpose' => 'Zweck: Verwaltung des Benutzerkontos und Plattformdienste',
        'data_retention' => 'Aufbewahrung: Die Daten werden so lange gespeichert, wie für die angeforderten Dienste erforderlich',
        'data_rights' => 'Rechte: Du kannst auf deine Daten zugreifen, sie berichtigen, löschen oder die Verarbeitung einschränken',
        'data_contact' => 'Um deine Rechte auszuüben, kontaktiere: privacy@florenceegi.com',
        'sensitive_data_warning' => 'Achtung: Du bearbeitest sensible Daten. Identitätsprüfung ist erforderlich.',
        'audit_notice' => 'Alle Änderungen an persönlichen Daten werden aus Sicherheitsgründen protokolliert',
    ],

    // EXPORTFUNKTION
    'export' => [
        'formats' => [
            'json' => 'JSON (Maschinenlesbar)',
            'pdf' => 'PDF (Für Menschen lesbar)',
            'csv' => 'CSV (Tabellenkalkulation)',
        ],
        'categories' => [
            'basic' => 'Grundlegende Informationen',
            'fiscal' => 'Steuerdaten',
            'address' => 'Adressdaten',
            'contact' => 'Kontaktdaten',
            'consents' => 'Einwilligungen und Präferenzen',
            'audit' => 'Änderungsprotokoll',
        ],
        'select_format' => 'Exportformat auswählen',
        'select_categories' => 'Zu exportierende Kategorien auswählen',
        'generate_export' => 'Export Generieren',
        'download_ready' => 'Download Bereit',
        'download_expires' => 'Download-Link läuft in 7 Tagen ab',
    ],

    // LÖSCHABLAUF
    'deletion' => [
        'confirm_title' => 'Löschung der Daten Bestätigen',
        'warning_irreversible' => 'ACHTUNG: Dieser Vorgang ist unwiderruflich',
        'warning_account' => 'Die Löschung der Daten führt zur dauerhaften Schließung deines Kontos',
        'warning_backup' => 'Daten können bis zu 90 Tage in Backups verbleiben',
        'reason_required' => 'Grund der Anfrage (optional)',
        'reason_placeholder' => 'Du kannst den Grund für die Löschung angeben...',
        'final_confirmation' => 'Ich bestätige, dass ich meine persönlichen Daten dauerhaft löschen möchte',
        'type_delete' => 'Gib "LÖSCHEN" ein, um zu bestätigen',
        'submit_request' => 'Löschanfrage Senden',
        'request_submitted' => 'Löschanfrage erfolgreich gesendet',
        'processing_time' => 'Die Anfrage wird innerhalb von 30 Werktagen bearbeitet',
    ],
    // ===================================================================
    // IBAN VERWALTUNG
    // ===================================================================
    'iban_management' => 'IBAN Verwaltung',
    'iban_description' => 'Konfigurieren Sie Ihre IBAN, um Zahlungen in Euro zu erhalten',
    'manage_iban' => 'IBAN Verwalten',

    // ===================================================================
    // LIEFERADRESSEN
    // ===================================================================
    'shipping' => [
        'title' => 'Lieferadressen',
        'add_new' => 'Neue Adresse hinzufügen',
        'add_address' => 'Adresse hinzufügen',
        'edit_address' => 'Adresse bearbeiten',
        'select_address' => 'Wählen Sie eine Lieferadresse:',
        'no_address' => 'Keine gespeicherte Lieferadresse gefunden.',
    ],
    'address_created_success' => 'Lieferadresse erfolgreich hinzugefügt',
    'address_updated_success' => 'Lieferadresse erfolgreich aktualisiert',
    'address_deleted_success' => 'Lieferadresse gelöscht',
    'address_default_success' => 'Standardadresse festgelegt',
];
