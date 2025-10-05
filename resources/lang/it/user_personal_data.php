<?php

/**
 * @Oracode Translation File: Personal Data Management - Italian
 * 🎯 Purpose: Complete Italian translations for GDPR-compliant personal data management
 * 🛡️ Privacy: GDPR-compliant notices, consent language, data subject rights
 * 🌐 i18n: Base language file for FlorenceEGI personal data domain
 * 🧱 Core Logic: Supports all personal data CRUD operations with privacy notices
 * ⏰ MVP: Critical for Italian market compliance and user trust
 *
 * @package Lang\It
 * @author Padmin D. Curtis (AI Partner OS1-Compliant)
 * @version 1.0.0 (FlorenceEGI MVP - GDPR Native)
 * @deadline 2025-06-30
 */

return [
    // ===================================================================
    // PAGE TITLES AND HEADERS
    // ===================================================================
    'management_title' => 'Gestione Dati Personali',
    'management_subtitle' => 'Gestisci i tuoi dati personali in conformità GDPR',
    'edit_title' => 'Modifica Dati Personali',
    'edit_subtitle' => 'Aggiorna le tue informazioni personali in sicurezza',
    'export_title' => 'Esporta Dati Personali',
    'export_subtitle' => 'Scarica una copia completa dei tuoi dati personali',
    'deletion_title' => 'Richiesta Cancellazione Dati',
    'deletion_subtitle' => 'Richiedi la cancellazione permanente dei tuoi dati personali',

    // ===================================================================
    // FORM SECTIONS
    // ===================================================================
    'basic_information' => 'Informazioni Anagrafiche',
    'basic_description' => 'Dati anagrafici fondamentali per l\'identificazione',
    'fiscal_information' => 'Informazioni Fiscali',
    'fiscal_description' => 'Codice fiscale e dati per adempimenti fiscali',
    'address_information' => 'Informazioni di Residenza',
    'address_description' => 'Indirizzo di residenza e domicilio',
    'contact_information' => 'Informazioni di Contatto',
    'contact_description' => 'Telefono e altri recapiti di contatto',
    'identity_verification' => 'Verifica Identità',
    'identity_description' => 'Verifica la tua identità per modifiche sensibili',

    // ===================================================================
    // FORM FIELDS
    // ===================================================================
    'anonymous_user' => 'Utente Anonimo',
    'full_name' => 'Nome Completo',
    'full_name_help' => 'Il tuo nome legale completo (per uso interno)',
    'last_name' => 'Cognome',
    'email' => 'Indirizzo Email',
    'email_help' => 'Indirizzo email principale per comunicazioni',
    'nickname' => 'Nickname',
    'nickname_help' => 'Nome pubblico mostrato sui tuoi EGI (opzionale)',
    'first_name' => 'Nome',
    'first_name_placeholder' => 'Inserisci il tuo nome',
    'last_name' => 'Cognome',
    'last_name_placeholder' => 'Inserisci il tuo cognome',
    'birth_date' => 'Data di Nascita',
    'birth_date_placeholder' => 'Seleziona la tua data di nascita',
    'birth_place' => 'Luogo di Nascita',
    'birth_place_placeholder' => 'Città e provincia di nascita',
    'gender' => 'Genere',
    'gender_male' => 'Maschile',
    'gender_female' => 'Femminile',
    'gender_other' => 'Altro',
    'gender_prefer_not_say' => 'Preferisco non specificare',

    // Fiscal Fields
    'tax_code' => 'Codice Fiscale',
    'tax_code_placeholder' => 'RSSMRA80A01H501X',
    'tax_code_help' => 'Il tuo codice fiscale italiano (16 caratteri)',
    'id_card_number' => 'Numero Carta d\'Identità',
    'id_card_number_placeholder' => 'Numero documento di identità',
    'passport_number' => 'Numero Passaporto',
    'passport_number_placeholder' => 'Numero passaporto (se disponibile)',
    'driving_license' => 'Patente di Guida',
    'driving_license_placeholder' => 'Numero patente di guida',

    // Address Fields
    'street_address' => 'Indirizzo',
    'street_address_placeholder' => 'Via, numero civico',
    'city' => 'Città',
    'city_placeholder' => 'Nome della città',
    'postal_code' => 'Codice Postale',
    'postal_code_placeholder' => '00100',
    'province' => 'Provincia',
    'province_placeholder' => 'Sigla provincia (es. RM)',
    'region' => 'Regione',
    'region_placeholder' => 'Nome della regione',
    'country' => 'Paese',
    'country_placeholder' => 'Seleziona il paese',

    // Contact Fields
    'phone' => 'Telefono',
    'phone_placeholder' => '+39 123 456 7890',
    'mobile' => 'Cellulare',
    'mobile_placeholder' => '+39 123 456 7890',
    'emergency_contact' => 'Contatto di Emergenza',
    'emergency_contact_placeholder' => 'Nome e telefono',

    // ===================================================================
    // PRIVACY AND CONSENT
    // ===================================================================
    'consent_management' => 'Gestione Consensi',
    'consent_description' => 'Gestisci i tuoi consensi per il trattamento dei dati',
    'consent_required' => 'Consenso Obbligatorio',
    'consent_optional' => 'Consenso Facoltativo',
    'contract_data_processing' => 'Trattamento Dati Necessario al Servizio',
    'contractual_basis' => 'Base Contrattuale',
    'consent_marketing' => 'Marketing e Comunicazioni',
    'consent_marketing_desc' => 'Consenso per ricevere comunicazioni commerciali',
    'consent_profiling' => 'Profilazione',
    'consent_profiling_desc' => 'Consenso per attività di profilazione e analisi',
    'consent_analytics' => 'Analytics',
    'consent_analytics_desc' => 'Consenso per analisi statistiche anonimizzate',
    'consent_third_party' => 'Terze Parti',
    'consent_third_party_desc' => 'Consenso per condivisione con partner selezionati',

    // ===================================================================
    // ACTIONS AND BUTTONS
    // ===================================================================
    'update_data' => 'Aggiorna Dati',
    'save_changes' => 'Salva Modifiche',
    'cancel_changes' => 'Annulla',
    'export_data' => 'Esporta Dati',
    'request_deletion' => 'Richiedi Cancellazione',
    'verify_identity' => 'Verifica Identità',
    'confirm_changes' => 'Conferma Modifiche',
    'back_to_profile' => 'Torna al Profilo',

    // ===================================================================
    // SUCCESS AND ERROR MESSAGES
    // ===================================================================
    'update_success' => 'Dati personali aggiornati con successo',
    'update_error' => 'Errore durante l\'aggiornamento dei dati personali',
    'validation_error' => 'Alcuni campi contengono errori. Controlla e riprova.',
    'identity_verification_required' => 'È richiesta la verifica dell\'identità per questa operazione',
    'identity_verification_failed' => 'Verifica dell\'identità fallita. Riprova.',
    'export_started' => 'Esportazione dati avviata. Riceverai un\'email quando sarà pronta.',
    'export_ready' => 'La tua esportazione dati è pronta per il download',
    'deletion_requested' => 'Richiesta di cancellazione inviata. Sarà elaborata entro 30 giorni.',

    // ===================================================================
    // VALIDATION MESSAGES
    // ===================================================================
    'validation' => [
        'name_required' => 'Il nome completo è obbligatorio',
        'name_format' => 'Il nome può contenere solo lettere, spazi e apostrofi',
        'last_name_format' => 'Il cognome può contenere solo lettere, spazi e apostrofi',
        'email_required' => 'L\'indirizzo email è obbligatorio',
        'email_format' => 'Inserisci un indirizzo email valido',
        'email_unique' => 'Questo indirizzo email è già registrato',
        'nickname_unique' => 'Questo nickname è già in uso',
        'nickname_max' => 'Il nickname non può superare 50 caratteri',
        'first_name_required' => 'Il nome è obbligatorio',
        'last_name_required' => 'Il cognome è obbligatorio',
        'birth_date_required' => 'La data di nascita è obbligatoria',
        'birth_date_valid' => 'La data di nascita deve essere valida',
        'birth_date_age' => 'Devi avere almeno 13 anni per registrarti',
        'tax_code_invalid' => 'Il codice fiscale non è valido',
        'tax_code_format' => 'Il codice fiscale deve avere 16 caratteri',
        'phone_invalid' => 'Il numero di telefono non è valido',
        'postal_code_invalid' => 'Il codice postale non è valido per il paese selezionato',
        'country_required' => 'Il paese è obbligatorio',
    ],

    // ===================================================================
    // GDPR NOTICES
    // ===================================================================
    'gdpr_notices' => [
        'data_processing_info' => 'I tuoi dati personali sono trattati in conformità al GDPR (UE) 2016/679',
        'contractual_processing_info' => 'Questo trattamento è necessario per l\'esecuzione del contratto di servizio e non può essere revocato',
        'data_controller' => 'Titolare del trattamento: FlorenceEGI S.r.l.',
        'data_purpose' => 'Finalità: Gestione account utente e servizi della piattaforma',
        'data_retention' => 'Conservazione: I dati sono conservati per il tempo necessario ai servizi richiesti',
        'data_rights' => 'Diritti: Puoi accedere, rettificare, cancellare o limitare il trattamento dei tuoi dati',
        'data_contact' => 'Per esercitare i tuoi diritti contatta: privacy@florenceegi.com',
        'sensitive_data_warning' => 'Attenzione: stai modificando dati sensibili. È richiesta la verifica dell\'identità.',
        'audit_notice' => 'Tutte le modifiche ai dati personali sono registrate per sicurezza',
    ],

    // ===================================================================
    // EXPORT FUNCTIONALITY
    // ===================================================================
    'export' => [
        'formats' => [
            'json' => 'JSON (Machine Readable)',
            'pdf' => 'PDF (Human Readable)',
            'csv' => 'CSV (Spreadsheet)',
        ],
        'categories' => [
            'basic' => 'Informazioni Anagrafiche',
            'fiscal' => 'Dati Fiscali',
            'address' => 'Dati di Residenza',
            'contact' => 'Informazioni di Contatto',
            'consents' => 'Consensi e Preferenze',
            'audit' => 'Log delle Modifiche',
        ],
        'select_format' => 'Seleziona formato di esportazione',
        'select_categories' => 'Seleziona categorie da esportare',
        'generate_export' => 'Genera Esportazione',
        'download_ready' => 'Download Pronto',
        'download_expires' => 'Il link di download scade tra 7 giorni',
    ],

    // ===================================================================
    // DELETION WORKFLOW
    // ===================================================================
    'deletion' => [
        'confirm_title' => 'Conferma Cancellazione Dati',
        'warning_irreversible' => 'ATTENZIONE: Questa operazione è irreversibile',
        'warning_account' => 'La cancellazione dei dati comporterà la chiusura permanente dell\'account',
        'warning_backup' => 'I dati potrebbero essere conservati nei backup per massimo 90 giorni',
        'reason_required' => 'Motivo della richiesta (facoltativo)',
        'reason_placeholder' => 'Puoi specificare il motivo della cancellazione...',
        'final_confirmation' => 'Confermo di voler cancellare permanentemente i miei dati personali',
        'type_delete' => 'Digita "CANCELLA" per confermare',
        'submit_request' => 'Invia Richiesta di Cancellazione',
        'request_submitted' => 'Richiesta di cancellazione inviata con successo',
        'processing_time' => 'La richiesta sarà elaborata entro 30 giorni lavorativi',
    ],
];
