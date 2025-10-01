<?php
// resources/lang/it/gdpr.php

return [
    /*
    |--------------------------------------------------------------------------
    | Linee di Lingua GDPR
    |--------------------------------------------------------------------------
    |
    | Le seguenti linee di lingua sono utilizzate per funzionalità relative al GDPR.
    |
    */

    // Generale
    'gdpr' => 'GDPR',
    'gdpr_center' => 'Centro di Controllo Dati GDPR',
    'dashboard' => 'Bacheca',
    'back_to_dashboard' => 'Torna alla Bacheca',
    'save' => 'Salva',
    'submit' => 'Invia',
    'cancel' => 'Annulla',
    'continue' => 'Continua',
    'loading' => 'Caricamento...',
    'success' => 'Successo',
    'error' => 'Errore',
    'warning' => 'Avviso',
    'info' => 'Informazioni',
    'enabled' => 'Abilitato',
    'disabled' => 'Disabilitato',
    'active' => 'Attivo',
    'inactive' => 'Inattivo',
    'pending' => 'In attesa',
    'completed' => 'Completato',
    'failed' => 'Fallito',
    'processing' => 'In elaborazione',
    'retry' => 'Riprova',
    'required_field' => 'Campo obbligatorio',
    'required_consent' => 'Consenso obbligatorio',
    'select_all_categories' => 'Seleziona tutte le categorie',
    'no_categories_selected' => 'Nessuna categoria selezionata',
    'compliance_badge' => 'Distintivo di conformità',
    'consent_required_profile_images' => 'È necessario il consenso per il trattamento dei dati personali per caricare immagini del profilo.',

    'consent_types' => [
        'terms-of-service' => [
            'name' => 'Termini di Servizio',
            'description' => 'Accettazione delle condizioni per l\'utilizzo della piattaforma.',
        ],
        'privacy-policy' => [
            'name' => 'Informativa sulla Privacy',
            'description' => 'Presa visione di come vengono trattati i dati personali.',
        ],
        'age-confirmation' => [
            'name' => 'Conferma Età',
            'description' => 'Conferma di avere almeno 18 anni.',
        ],
        'analytics' => [
            'name' => 'Analisi e miglioramento piattaforma',
            'description' => 'Aiutaci a migliorare FlorenceEGI condividendo dati anonimi di utilizzo.',
        ],
        'marketing' => [
            'name' => 'Comunicazioni promozionali',
            'description' => 'Ricevi aggiornamenti su nuove funzionalità, eventi e opportunità.',
        ],
        'personalization' => [
            'name' => 'Personalizzazione contenuti',
            'description' => 'Consenti la personalizzazione di contenuti e raccomandazioni.',
        ],
        'collaboration_participation' => [
            'name' => 'Partecipazione alla Collaborazione',
            'description' => 'Consenso a partecipare alla collaborazione di collezioni, condivisione dati e attività collaborative.',
        ],
        'purposes' => [
            'account_management' => 'Gestione dell\'Account Utente',
            'service_delivery'   => 'Erogazione dei Servizi Richiesti',
            'legal_compliance'   => 'Conformità Legale e Normativa',
            'customer_support'   => 'Supporto Clienti e Assistenza',
        ],

    ],

    // Breadcrumb
    'breadcrumb' => [
        'dashboard' => 'Bacheca',
        'gdpr' => 'Privacy e GDPR',
    ],

    // Messaggi di Allerta
    'alerts' => [
        'success' => 'Operazione completata!',
        'error' => 'Errore:',
        'warning' => 'Avviso:',
        'info' => 'Informazioni:',
    ],

    // Elementi del Menu
    'menu' => [
        'gdpr_center' => 'Centro di Controllo Dati GDPR',
        'consent_management' => 'Gestione dei Consensi',
        'data_export' => 'Esporta i Miei Dati',
        'processing_restrictions' => 'Limita l\'Elaborazione dei Dati',
        'delete_account' => 'Elimina il Mio Account',
        'breach_report' => 'Segnala una Violazione dei Dati',
        'activity_log' => 'Registro delle Mie Attività GDPR',
        'privacy_policy' => 'Informativa sulla Privacy',
    ],

    // Gestione dei Consensi
    'consent' => [
        'title' => 'Gestisci le Tue Preferenze di Consenso',
        'description' => 'Controlla come vengono utilizzati i tuoi dati all\'interno della nostra piattaforma. Puoi aggiornare le tue preferenze in qualsiasi momento.',
        'update_success' => 'Le tue preferenze di consenso sono state aggiornate.',
        'update_error' => 'Si è verificato un errore durante l\'aggiornamento delle tue preferenze di consenso. Riprova.',
        'save_all' => 'Salva Tutte le Preferenze',
        'last_updated' => 'Ultimo aggiornamento:',
        'never_updated' => 'Mai aggiornato',
        'privacy_notice' => 'Informativa sulla Privacy',
        'not_given' => 'Non Fornito',
        'given_at' => 'Fornito il',
        'your_consents' => 'I Tuoi Consensi',
        'subtitle' => 'Gestisci le tue preferenze sulla privacy e visualizza lo stato dei tuoi consensi.',
        'breadcrumb' => 'Consensi',
        'history_title' => 'Cronologia dei Consensi',
        'back_to_consents' => 'Torna ai Consensi',
        'preferences_title' => 'Gestione Preferenze Consensi',
        'preferences_subtitle' => 'Configura le tue preferenze di privacy dettagliate',
        'preferences_breadcrumb' => 'Preferenze',
        'preferences_info_title' => 'Gestione Granulare dei Consensi',
        'preferences_info_description' => 'Qui puoi configurare in dettaglio ogni tipo di consenso...',
        'required' => 'Obbligatorio',
        'optional' => 'Opzionale',
        'toggle_label' => 'Attiva/Disattiva',
        'always_enabled' => 'Sempre Attivo',
        'benefits_title' => 'Vantaggi per Te',
        'consequences_title' => 'Se Disattivi',
        'third_parties_title' => 'Servizi Terzi',
        'save_preferences' => 'Salva Preferenze',
        'back_to_overview' => 'Torna alla Panoramica',
        'never_updated' => 'Mai aggiornato',

        // Dettagli del Consenso
        'given_at' => 'Fornito il',
        'withdrawn_at' => 'Revocato il',
        'not_given' => 'Non fornito',
        'method' => 'Metodo',
        'version' => 'Versione',
        'unknown_version' => 'Versione sconosciuta',

        // Azioni
        'withdraw' => 'Revoca il Consenso',
        'withdraw_confirm' => 'Sei sicuro di voler revocare questo consenso? Questa azione potrebbe limitare alcune funzionalità.',
        'renew' => 'Rinnova il Consenso',
        'view_history' => 'Visualizza Cronologia',

        // Stati Vuoti
        'no_consents' => 'Nessun Consenso Presente',
        'no_consents_description' => 'Non hai ancora fornito alcun consenso per l\'elaborazione dei dati. Puoi gestire le tue preferenze utilizzando il pulsante qui sotto.',

        // Gestione Preferenze
        'manage_preferences' => 'Gestisci le Tue Preferenze',
        'update_preferences' => 'Aggiorna le Preferenze sulla Privacy',

        // Stato del Consenso
        'status' => [
            'granted' => 'Concesso',
            'denied' => 'Negato',
            'active' => 'Attivo',
            'withdrawn' => 'Revocato',
            'expired' => 'Scaduto',
            'pending' => 'In attesa',
            'in_progress' => 'In corso',
            'completed' => 'Completato',
            'failed' => 'Fallito',
            'rejected' => 'Respinto',
            'verification_required' => 'Verifica richiesta',
            'cancelled' => 'Annullato',
        ],

        // Dashboard di Riepilogo
        'summary' => [
            'active' => 'Consensi Attivi',
            'total' => 'Consensi Totali',
            'compliance' => 'Punteggio di Conformità',
        ],

        // Metodi di Consenso
        'methods' => [
            'web' => 'Interfaccia Web',
            'api' => 'API',
            'import' => 'Importazione',
            'admin' => 'Amministratore',
        ],

        // Scopi del Consenso
        'purposes' => [
            'functional' => 'Consensi Funzionali',
            'analytics' => 'Consensi Analitici',
            'marketing' => 'Consensi di Marketing',
            'profiling' => 'Consensi di Profilazione',
            'platform-services' => 'Servizi della Piattaforma',
            'terms-of-service' => 'Termini di Servizio',
            'privacy-policy' => 'Informativa sulla Privacy',
            'age-confirmation' => 'Conferma Età',
            'personalization' => 'Personalizzazione dei Contenuti',
            'allow-personal-data-processing' => 'Consenti Trattamento Dati Personali',
            'collaboration_participation' => 'Partecipazione alle Collaborazioni',
        ],

        // Descrizioni dei Consensi
        'descriptions' => [
            'functional' => 'Necessari per il funzionamento di base della piattaforma e per fornire i servizi richiesti.',
            'analytics' => 'Utilizzati per analizzare l\'uso del sito e migliorare l\'esperienza utente.',
            'marketing' => 'Utilizzati per inviarti comunicazioni promozionali e offerte personalizzate.',
            'profiling' => 'Utilizzati per creare profili personalizzati e suggerire contenuti pertinenti.',
            'platform-services' => 'Consensi necessari per la gestione dell\'account, la sicurezza e il supporto clienti.',
            'terms-of-service' => 'Accettazione dei Termini di Servizio per l\'utilizzo della piattaforma.',
            'privacy-policy' => 'Accettazione della nostra Informativa sulla Privacy e del trattamento dei dati personali.',
            'age-confirmation' => 'Conferma di avere la maggiore età per l\'utilizzo della piattaforma.',
            'personalization' => 'Consenti la personalizzazione dei contenuti e delle raccomandazioni in base alle tue preferenze.',
            'allow-personal-data-processing' => 'Consenti l\'elaborazione dei tuoi dati personali per migliorare i nostri servizi e fornirti un\'esperienza personalizzata.',
            'collaboration_participation' => 'Consenti la partecipazione a progetti collaborativi e attività condivise con altri utenti della piattaforma.',

        ],

        'essential' => [
            'label' => 'Cookie Essenziali',
            'description' => 'Questi cookie sono necessari per il funzionamento del sito web e non possono essere disattivati nei nostri sistemi.',
        ],
        'functional' => [
            'label' => 'Cookie Funzionali',
            'description' => 'Questi cookie consentono al sito web di fornire funzionalità avanzate e personalizzazione.',
        ],
        'analytics' => [
            'label' => 'Cookie Analitici',
            'description' => 'Questi cookie ci permettono di contare le visite e le fonti di traffico per misurare e migliorare le prestazioni del nostro sito.',
        ],
        'marketing' => [
            'label' => 'Cookie di Marketing',
            'description' => 'Questi cookie possono essere impostati tramite il nostro sito dai nostri partner pubblicitari per creare un profilo dei tuoi interessi.',
        ],
        'profiling' => [
            'label' => 'Profilazione',
            'description' => 'Utilizziamo la profilazione per comprendere meglio le tue preferenze e personalizzare i nostri servizi in base alle tue esigenze.',
        ],

        'allow_personal_data_processing' => [
            'label' => 'Consenso Trattamento Dati Personali',
            'description' => 'Consenti l\'elaborazione dei tuoi dati personali per migliorare i nostri servizi e fornirti un\'esperienza personalizzata.',
        ],

        'saving_consent' => 'Salvataggio...',
        'consent_saved' => 'Salvato',
        'saving_all_consents' => 'Salvataggio di tutte le preferenze...',
        'all_consents_saved' => 'Tutte le preferenze di consenso sono state salvate con successo.',
        'all_consents_save_error' => 'Si è verificato un errore nel salvare tutte le preferenze di consenso.',
        'consent_save_error' => 'Si è verificato un errore nel salvare questa preferenza di consenso.',

        // Processing Purposes (Scopi del Trattamento)
        'processing_purposes' => [
            'functional' => 'Operazioni essenziali della piattaforma: autenticazione, sicurezza, erogazione servizi, memorizzazione preferenze utente',
            'analytics' => 'Miglioramento della piattaforma: analisi utilizzo, monitoraggio performance, ottimizzazione esperienza utente',
            'marketing' => 'Comunicazione: newsletter, aggiornamenti prodotto, offerte promozionali, notifiche eventi',
            'profiling' => 'Personalizzazione: raccomandazioni contenuti, analisi comportamento utente, suggerimenti mirati',
        ],

        // Retention Periods (Periodi di Conservazione)
        'retention_periods' => [
            'functional' => 'Durata dell\'account + 1 anno per conformità legale',
            'analytics' => '2 anni dall\'ultima attività',
            'marketing' => '3 anni dall\'ultima interazione o revoca consenso',
            'profiling' => '1 anno dall\'ultima attività o revoca consenso',
        ],

        // User Benefits (Vantaggi Utente)
        'user_benefits' => [
            'functional' => [
                'Accesso sicuro al tuo account',
                'Impostazioni utente personalizzate',
                'Performance affidabile della piattaforma',
                'Protezione contro frodi e abusi',
            ],
            'analytics' => [
                'Performance migliorata della piattaforma',
                'Design esperienza utente ottimizzato',
                'Tempi di caricamento più veloci',
                'Sviluppo funzionalità potenziate',
            ],
            'marketing' => [
                'Aggiornamenti prodotto pertinenti',
                'Offerte e promozioni esclusive',
                'Inviti eventi e annunci',
                'Contenuti educativi e suggerimenti',
            ],
            'profiling' => [
                'Raccomandazioni contenuti personalizzate',
                'Esperienza utente su misura',
                'Suggerimenti progetti rilevanti',
                'Dashboard e funzionalità personalizzate',
            ],
        ],

        // Third Parties (Servizi Terzi)
        'third_parties' => [
            'functional' => [
                'Fornitori CDN (distribuzione contenuti statici)',
                'Servizi sicurezza (prevenzione frodi)',
                'Fornitori infrastruttura (hosting)',
            ],
            'analytics' => [
                'Piattaforme analytics (dati utilizzo anonimizzati)',
                'Servizi monitoraggio performance',
                'Servizi tracking errori',
            ],
            'marketing' => [
                'Fornitori servizi email',
                'Piattaforme automazione marketing',
                'Piattaforme social media (per pubblicità)',
            ],
            'profiling' => [
                'Motori raccomandazione',
                'Servizi analisi comportamentale',
                'Piattaforme personalizzazione contenuti',
            ],
        ],

        // Withdrawal Consequences (Conseguenze Revoca)
        'withdrawal_consequences' => [
            'functional' => [
                'Non può essere revocato - essenziale per funzionamento piattaforma',
                'L\'accesso all\'account verrebbe compromesso',
                'Le funzionalità di sicurezza verrebbero disabilitate',
            ],
            'analytics' => [
                'I miglioramenti della piattaforma potrebbero non riflettere i tuoi pattern di utilizzo',
                'Esperienza generica invece di performance ottimizzata',
                'Nessun impatto sulle funzionalità core',
            ],
            'marketing' => [
                'Nessuna email promozionale o aggiornamento',
                'Potresti perdere annunci importanti',
                'Nessun impatto sulla funzionalità della piattaforma',
                'Può essere riattivato in qualsiasi momento',
            ],
            'profiling' => [
                'Contenuti generici invece di raccomandazioni personalizzate',
                'Layout dashboard standard',
                'Suggerimenti progetti meno rilevanti',
                'Nessun impatto sulle funzionalità core della piattaforma',
            ],
        ],
    ],

    // Esportazione dei Dati
    'export' => [
        'title' => 'Esporta i Tuoi Dati',
        'subtitle' => 'Richiedi una copia completa dei tuoi dati personali in formato portabile',
        'description' => 'Richiedi una copia dei tuoi dati personali. L\'elaborazione potrebbe richiedere alcuni minuti.',

        // Data Categories
        'select_data_categories' => 'Seleziona le Categorie di Dati da Esportare',
        'categories' => [
            'profile' => 'Informazioni sul Profilo',
            'account' => 'Dettagli Account',
            'preferences' => 'Preferenze e Impostazioni',
            'activity' => 'Cronologia Attività',
            'consents' => 'Cronologia Consensi',
            'collections' => 'Collezioni e NFT',
            'purchases' => 'Acquisti e Transazioni',
            'comments' => 'Commenti e Recensioni',
            'messages' => 'Messaggi e Comunicazioni',
            'biography' => 'Biografie e Contenuti',
        ],

        // Category Descriptions
        'category_descriptions' => [
            'profile' => 'Dati anagrafici, informazioni di contatto, foto profilo e descrizioni personali',
            'account' => 'Dettagli account, impostazioni sicurezza, cronologia login e modifiche',
            'preferences' => 'Preferenze utente, impostazioni privacy, configurazioni personalizzate',
            'activity' => 'Cronologia navigazione, interazioni, visualizzazioni e utilizzo piattaforma',
            'consents' => 'Storico consensi privacy, modifiche preferenze, audit trail GDPR',
            'collections' => 'Collezioni NFT create, metadati, proprietà intellettuale e assets',
            'purchases' => 'Transazioni, acquisti, fatture, metodi pagamento e cronologia ordini',
            'comments' => 'Commenti, recensioni, valutazioni e feedback lasciati sulla piattaforma',
            'messages' => 'Messaggi privati, comunicazioni, notifiche e conversazioni',
            'biography' => 'Biografie create, capitoli, timeline, media e contenuti narrativi',
        ],

        // Export Formats
        'select_format' => 'Seleziona il Formato di Esportazione',
        'formats' => [
            'json' => 'JSON - Formato Dati Strutturato',
            'csv' => 'CSV - Compatibile con Fogli di Calcolo',
            'pdf' => 'PDF - Documento Leggibile',
        ],

        // Format Descriptions
        'format_descriptions' => [
            'json' => 'Formato dati strutturato ideale per sviluppatori e integrazioni. Mantiene la struttura completa dei dati.',
            'csv' => 'Formato compatibile con Excel e Google Sheets. Perfetto per analisi e manipolazione dati.',
            'pdf' => 'Documento leggibile e stampabile. Ideale per archiviazione e condivisione.',
        ],

        // Additional Options
        'additional_options' => 'Opzioni Aggiuntive',
        'include_metadata' => 'Includi Metadati Tecnici',
        'metadata_description' => 'Include informazioni tecniche come timestamp, IP address, versioni e audit trail.',
        'include_audit_trail' => 'Includi Registro Completo Attività',
        'audit_trail_description' => 'Include cronologia completa di tutte le modifiche e attività GDPR.',

        // Actions
        'request_export' => 'Richiedi Esportazione Dati',
        'request_success' => 'Richiesta di esportazione inviata con successo. Riceverai una notifica al completamento.',
        'request_error' => 'Si è verificato un errore nell\'invio della richiesta. Riprova.',

        // Export History
        'history_title' => 'Cronologia Esportazioni',
        'no_exports' => 'Nessuna Esportazione Presente',
        'no_exports_description' => 'Non hai ancora richiesto alcuna esportazione dei tuoi dati. Utilizza il modulo sopra per richiederne una.',

        // Export Item Details
        'export_format' => 'Esportazione {format}',
        'requested_on' => 'Richiesta il',
        'completed_on' => 'Completata il',
        'expires_on' => 'Scade il',
        'file_size' => 'Dimensione',
        'download' => 'Scarica',
        'download_export' => 'Scarica Esportazione',

        // Status
        'status' => [
            'pending' => 'In Attesa',
            'processing' => 'In Elaborazione',
            'completed' => 'Completata',
            'failed' => 'Fallita',
            'expired' => 'Scaduta',
        ],

        // Rate Limiting
        'rate_limit_title' => 'Limite Esportazioni Raggiunto',
        'rate_limit_message' => 'Hai raggiunto il limite massimo di {max} esportazioni per oggi. Riprova domani.',
        'last_export_date' => 'Ultima esportazione: {date}',

        // Validation
        'select_at_least_one_category' => 'Seleziona almeno una categoria di dati da esportare.',

        // Legacy Support
        'request_button' => 'Richiedi Esportazione Dati',
        'format' => 'Formato di Esportazione',
        'format_json' => 'JSON (consigliato per sviluppatori)',
        'format_csv' => 'CSV (compatibile con fogli di calcolo)',
        'format_pdf' => 'PDF (documento leggibile)',
        'include_timestamps' => 'Includi timestamp',
        'password_protection' => 'Proteggi l\'esportazione con password',
        'password' => 'Password di esportazione',
        'confirm_password' => 'Conferma password',
        'data_categories' => 'Categorie di dati da esportare',
        'recent_exports' => 'Esportazioni Recenti',
        'no_recent_exports' => 'Non hai esportazioni recenti.',
        'export_status' => 'Stato Esportazione',
        'export_date' => 'Data Esportazione',
        'export_size' => 'Dimensione Esportazione',
        'export_id' => 'ID Esportazione',
        'export_preparing' => 'Preparazione della tua esportazione dati...',
        'export_queued' => 'La tua esportazione è in coda e inizierà presto...',
        'export_processing' => 'Elaborazione della tua esportazione dati...',
        'export_ready' => 'La tua esportazione dati è pronta per il download.',
        'export_failed' => 'La tua esportazione dati è fallita.',
        'export_failed_details' => 'Si è verificato un errore durante l\'elaborazione della tua esportazione dati. Riprova o contatta il supporto.',
        'export_unknown_status' => 'Stato dell\'esportazione sconosciuto.',
        'check_status' => 'Controlla Stato',
        'retry_export' => 'Riprova Esportazione',
        'export_download_error' => 'Si è verificato un errore durante il download della tua esportazione.',
        'export_status_error' => 'Errore durante il controllo dello stato dell\'esportazione.',
        'limit_reached' => 'Hai raggiunto il numero massimo di esportazioni consentite al giorno.',
        'existing_in_progress' => 'Hai già un\'esportazione in corso. Attendi che sia completata.',
    ],

    // Restrizioni di Elaborazione
    'restriction' => [
        'title' => 'Limita l\'Elaborazione dei Dati',
        'description' => 'Puoi richiedere di limitare il modo in cui elaboriamo i tuoi dati in determinate circostanze.',
        'active_restrictions' => 'Restrizioni Attive',
        'no_active_restrictions' => 'Non hai restrizioni di elaborazione attive.',
        'request_new' => 'Richiedi Nuova Restrizione',
        'restriction_type' => 'Tipo di Restrizione',
        'restriction_reason' => 'Motivo della Restrizione',
        'data_categories' => 'Categorie di Dati',
        'notes' => 'Note Aggiuntive',
        'notes_placeholder' => 'Fornisci eventuali dettagli aggiuntivi per aiutarci a comprendere la tua richiesta...',
        'submit_button' => 'Invia Richiesta di Restrizione',
        'remove_button' => 'Rimuovi Restrizione',
        'processing_restriction_success' => 'La tua richiesta di restrizione di elaborazione è stata inviata.',
        'processing_restriction_failed' => 'Si è verificato un errore nell\'invio della tua richiesta di restrizione di elaborazione.',
        'processing_restriction_system_error' => 'Si è verificato un errore di sistema durante l\'elaborazione della tua richiesta.',
        'processing_restriction_removed' => 'La restrizione di elaborazione è stata rimossa.',
        'processing_restriction_removal_failed' => 'Si è verificato un errore nella rimozione della restrizione di elaborazione.',
        'unauthorized_action' => 'Non sei autorizzato a eseguire questa azione.',
        'date_submitted' => 'Data di Invio',
        'expiry_date' => 'Scade il',
        'never_expires' => 'Mai Scade',
        'status' => 'Stato',
        'limit_reached' => 'Hai raggiunto il numero massimo di restrizioni attive consentite.',
        'categories' => [
            'profile' => 'Informazioni sul Profilo',
            'activity' => 'Tracciamento Attività',
            'preferences' => 'Preferenze e Impostazioni',
            'collections' => 'Collezioni e Contenuti',
            'purchases' => 'Acquisti e Transazioni',
            'comments' => 'Commenti e Recensioni',
            'messages' => 'Messaggi e Comunicazioni',
        ],
        'types' => [
            'processing' => 'Limita Tutta l\'Elaborazione',
            'automated_decisions' => 'Limita le Decisioni Automatizzate',
            'marketing' => 'Limita l\'Elaborazione di Marketing',
            'analytics' => 'Limita l\'Elaborazione Analitica',
            'third_party' => 'Limita la Condivisione con Terze Parti',
            'profiling' => 'Limita la Profilazione',
            'data_sharing' => 'Limita la Condivisione dei Dati',
            'removed' => 'Rimuovi Restrizione',
            'all' => 'Limita Tutte le Elaborazioni',
        ],
        'reasons' => [
            'accuracy_dispute' => 'Contesto l\'accuratezza dei miei dati',
            'processing_unlawful' => 'L\'elaborazione è illecita',
            'no_longer_needed' => 'Non hai più bisogno dei miei dati, ma io ne ho bisogno per rivendicazioni legali',
            'objection_pending' => 'Ho obiettato all\'elaborazione e sono in attesa di verifica',
            'legitimate_interest' => 'Motivi legittimi impellenti',
            'legal_claims' => 'Per la difesa di rivendicazioni legali',
            'other' => 'Altro motivo (specificare nelle note)',
        ],
        'descriptions' => [
            'processing' => 'Limita l\'elaborazione dei tuoi dati personali in attesa di verifica della tua richiesta.',
            'automated_decisions' => 'Limita le decisioni automatizzate che possono influenzare i tuoi diritti.',
            'marketing' => 'Limita l\'elaborazione dei tuoi dati per scopi di marketing diretto.',
            'analytics' => 'Limita l\'elaborazione dei tuoi dati per scopi analitici e di monitoraggio.',
            'third_party' => 'Limita la condivisione dei tuoi dati con terze parti.',
            'profiling' => 'Limita la profilazione dei tuoi dati personali.',
            'data_sharing' => 'Limita la condivisione dei tuoi dati con altri servizi o piattaforme.',
            'all' => 'Limita tutte le forme di elaborazione dei tuoi dati personali.',
        ],
    ],

    // Eliminazione Account
    'deletion' => [
        'title' => 'Elimina il Mio Account',
        'description' => 'Questo avvierà il processo per eliminare il tuo account e tutti i dati associati.',
        'warning' => 'Attenzione: L\'eliminazione dell\'account è permanente e non può essere annullata.',
        'processing_delay' => 'Il tuo account sarà programmato per l\'eliminazione tra :days giorni.',
        'confirm_deletion' => 'Comprendo che questa azione è permanente e non può essere annullata.',
        'password_confirmation' => 'Inserisci la tua password per confermare',
        'reason' => 'Motivo dell\'eliminazione (facoltativo)',
        'additional_comments' => 'Commenti aggiuntivi (facoltativi)',
        'submit_button' => 'Richiedi Eliminazione Account',
        'request_submitted' => 'La tua richiesta di eliminazione account è stata inviata.',
        'request_error' => 'Si è verificato un errore nell\'invio della tua richiesta di eliminazione account.',
        'pending_deletion' => 'Il tuo account è programmato per l\'eliminazione il :date.',
        'cancel_deletion' => 'Annulla Richiesta di Eliminazione',
        'cancellation_success' => 'La tua richiesta di eliminazione account è stata annullata.',
        'cancellation_error' => 'Si è verificato un errore nell\'annullamento della tua richiesta di eliminazione account.',
        'reasons' => [
            'no_longer_needed' => 'Non ho più bisogno di questo servizio',
            'privacy_concerns' => 'Preoccupazioni sulla privacy',
            'moving_to_competitor' => 'Passaggio a un altro servizio',
            'unhappy_with_service' => 'Insoddisfatto del servizio',
            'other' => 'Altro motivo',
        ],
        'confirmation_email' => [
            'subject' => 'Conferma Richiesta di Eliminazione Account',
            'line1' => 'Abbiamo ricevuto la tua richiesta di eliminazione del tuo account.',
            'line2' => 'Il tuo account è programmato per l\'eliminazione il :date.',
            'line3' => 'Se non hai richiesto questa azione, contattaci immediatamente.',
        ],
        'data_retention_notice' => 'Nota che alcuni dati anonimizzati potrebbero essere conservati per scopi legali e analitici.',
        'blockchain_data_notice' => 'I dati memorizzati su blockchain non possono essere completamente eliminati a causa della natura immutabile della tecnologia.',
    ],

    // Segnalazione Violazione
    'breach' => [
        'title' => 'Segnala una Violazione dei Dati',
        'description' => 'Se ritieni che ci sia stata una violazione dei tuoi dati personali, segnalala qui.',
        'reporter_name' => 'Il Tuo Nome',
        'reporter_email' => 'La Tua Email',
        'incident_date' => 'Quando si è verificato l\'incidente?',
        'breach_description' => 'Descrivi la potenziale violazione',
        'breach_description_placeholder' => 'Fornisci il maggior numero di dettagli possibile sulla potenziale violazione dei dati...',
        'affected_data' => 'Quali dati ritieni siano stati compromessi?',
        'affected_data_placeholder' => 'Ad esempio, informazioni personali, dati finanziari, ecc.',
        'discovery_method' => 'Come hai scoperto questa potenziale violazione?',
        'supporting_evidence' => 'Prove a Supporto (facoltativo)',
        'upload_evidence' => 'Carica Prove',
        'file_types' => 'Tipi di file accettati: PDF, JPG, JPEG, PNG, TXT, DOC, DOCX',
        'max_file_size' => 'Dimensione massima del file: 10MB',
        'consent_to_contact' => 'Acconsento a essere contattato in merito a questa segnalazione',
        'submit_button' => 'Invia Segnalazione di Violazione',
        'report_submitted' => 'La tua segnalazione di violazione è stata inviata.',
        'report_error' => 'Si è verificato un errore nell\'invio della tua segnalazione di violazione.',
        'thank_you' => 'Grazie per la tua segnalazione',
        'thank_you_message' => 'Grazie per aver segnalato questa potenziale violazione. Il nostro team di protezione dei dati indagherà e potrebbe contattarti per ulteriori informazioni.',
        'breach_description_min' => 'Fornisci almeno 20 caratteri per descrivere la potenziale violazione.',
    ],

    // Registro Attività
    'activity' => [
        'title' => 'Registro delle Mie Attività GDPR',
        'description' => 'Visualizza un registro di tutte le tue attività e richieste relative al GDPR.',
        'no_activities' => 'Nessuna attività trovata.',
        'date' => 'Data',
        'activity' => 'Attività',
        'details' => 'Dettagli',
        'ip_address' => 'Indirizzo IP',
        'user_agent' => 'User Agent',
        'download_log' => 'Scarica Registro Attività',
        'filter' => 'Filtra Attività',
        'filter_all' => 'Tutte le Attività',
        'filter_consent' => 'Attività di Consenso',
        'filter_export' => 'Attività di Esportazione Dati',
        'filter_restriction' => 'Attività di Restrizione Elaborazione',
        'filter_deletion' => 'Attività di Eliminazione Account',
        'types' => [
            'consent_updated' => 'Preferenze di Consenso Aggiornate',
            'data_export_requested' => 'Esportazione Dati Richiesta',
            'data_export_completed' => 'Esportazione Dati Completata',
            'data_export_downloaded' => 'Esportazione Dati Scaricata',
            'processing_restricted' => 'Restrizione di Elaborazione Richiesta',
            'processing_restriction_removed' => 'Restrizione di Elaborazione Rimossa',
            'account_deletion_requested' => 'Eliminazione Account Richiesta',
            'account_deletion_cancelled' => 'Eliminazione Account Annullata',
            'account_deletion_completed' => 'Eliminazione Account Completata',
            'breach_reported' => 'Violazione Dati Segnalata',
        ],
    ],

    // Validazione
    'validation' => [
        'consents_required' => 'Le preferenze di consenso sono obbligatorie.',
        'consents_format' => 'Il formato delle preferenze di consenso non è valido.',
        'consent_value_required' => 'Il valore del consenso è obbligatorio.',
        'consent_value_boolean' => 'Il valore del consenso deve essere un booleano.',
        'format_required' => 'Il formato di esportazione è obbligatorio.',
        'data_categories_required' => 'È necessario selezionare almeno una categoria di dati.',
        'data_categories_format' => 'Il formato delle categorie di dati non è valido.',
        'data_categories_min' => 'È necessario selezionare almeno una categoria di dati.',
        'data_categories_distinct' => 'Le categorie di dati devono essere distinte.',
        'export_password_required' => 'La password è obbligatoria quando la protezione con password è abilitata.',
        'export_password_min' => 'La password deve essere lunga almeno 8 caratteri.',
        'restriction_type_required' => 'Il tipo di restrizione è obbligatorio.',
        'restriction_reason_required' => 'Il motivo della restrizione è obbligatorio.',
        'notes_max' => 'Le note non possono superare i 500 caratteri.',
        'reporter_name_required' => 'Il tuo nome è obbligatorio.',
        'reporter_email_required' => 'La tua email è obbligatoria.',
        'reporter_email_format' => 'Inserisci un indirizzo email valido.',
        'incident_date_required' => 'La data dell\'incidente è obbligatoria.',
        'incident_date_format' => 'La data dell\'incidente deve essere una data valida.',
        'incident_date_past' => 'La data dell\'incidente deve essere nel passato o oggi.',
        'breach_description_required' => 'La descrizione della violazione è obbligatoria.',
        'breach_description_min' => 'La descrizione della violazione deve essere lunga almeno 20 caratteri.',
        'affected_data_required' => 'Le informazioni sui dati compromessi sono obbligatorie.',
        'discovery_method_required' => 'Il metodo di scoperta è obbligatorio.',
        'supporting_evidence_format' => 'Le prove devono essere un file PDF, JPG, JPEG, PNG, TXT, DOC o DOCX.',
        'supporting_evidence_max' => 'Il file di prove non può superare i 10MB.',
        'consent_to_contact_required' => 'Il consenso al contatto è obbligatorio.',
        'consent_to_contact_accepted' => 'Il consenso al contatto deve essere accettato.',
        'required_consent_message' => 'Questo consenso è necessario per utilizzare la piattaforma.',
        'confirm_deletion_required' => 'Devi confermare di comprendere le conseguenze dell\'eliminazione dell\'account.',
        'form_error_title' => 'Correggi gli errori qui sotto',
        'form_error_message' => 'Ci sono uno o più errori nel modulo che devono essere corretti.',
    ],

    // Messaggi di Errore
    'errors' => [
        'general' => 'Si è verificato un errore imprevisto.',
        'unauthorized' => 'Non sei autorizzato a eseguire questa azione.',
        'forbidden' => 'Questa azione è vietata.',
        'not_found' => 'La risorsa richiesta non è stata trovata.',
        'validation_failed' => 'I dati inviati non sono validi.',
        'rate_limited' => 'Troppe richieste. Riprova più tardi.',
        'service_unavailable' => 'Il servizio non è attualmente disponibile. Riprova più tardi.',
    ],

    'requests' => [
        'types' => [
            'consent_update' => 'Richiesta di aggiornamento del consenso inviata.',
            'data_export' => 'Richiesta di esportazione dei dati inviata.',
            'processing_restriction' => 'Richiesta di restrizione dell\'elaborazione inviata.',
            'account_deletion' => 'Richiesta di eliminazione dell\'account inviata.',
            'breach_report' => 'Segnalazione di violazione dei dati inviata.',
            'erasure' => 'Richiesta di cancellazione dei dati inviata.',
            'access' => 'Richiesta di accesso ai dati inviata.',
            'rectification' => 'Richiesta di rettifica dei dati inviata.',
            'objection' => 'Richiesta di opposizione all\'elaborazione inviata.',
            'restriction' => 'Richiesta di limitazione dell\'elaborazione inviata.',
            'portability' => 'Richiesta di portabilità dei dati inviata.',
        ],
    ],

    'modal' => [
        'clarification' => [
            'title' => 'Chiarificazione Necessaria',
            'explanation' => 'Per garantire la tua sicurezza, dobbiamo capire il motivo della tua azione:',
        ],
        'revoke_button_text' => 'Ho cambiato idea',
        'revoke_description' => 'Vuoi semplicemente revocare il consenso precedentemente dato.',
        'disavow_button_text' => 'Non riconosco questa azione',
        'disavow_description' => 'Non hai mai dato questo consenso (potenziale problema di sicurezza).',

        'confirmation' => [
            'title' => 'Conferma Protocollo di Sicurezza',
            'warning' => 'Questa azione attiverà un protocollo di sicurezza che include:',
        ],
        'confirm_disavow' => 'Sì, attiva protocollo di sicurezza',
        'final_warning' => 'Procedi solo se sei certo che non hai mai autorizzato questa azione.',

        'consequences' => [
            'consent_revocation' => 'Revoca immediata del consenso',
            'security_notification' => 'Notifica al team di sicurezza',
            'account_review' => 'Possibili controlli aggiuntivi sull\'account',
            'email_confirmation' => 'Email di conferma con istruzioni',
        ],

        'security' => [
            'title' => 'Protocollo di Sicurezza Attivato',
            'understood' => 'Ho capito',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Sezione Notifiche GDPR
    |--------------------------------------------------------------------------
    | Spostato da `notification.php` per centralizzazione.
    */
    'notifications' => [
        'acknowledged' => 'Presa visione registrata.',
        'consent_updated' => [
            'title' => 'Preferenze Privacy Aggiornate',
            'content' => 'Le tue preferenze relative al consenso sono state aggiornate con successo.',
        ],
        'data_exported' => [
            'title' => 'Il Tuo Export di Dati è Pronto',
            'content' => 'La tua richiesta di esportazione dati è stata elaborata. Puoi scaricare il file dal link fornito.',
        ],
        'processing_restricted' => [
            'title' => 'Restrizione del Trattamento Applicata',
            'content' => 'Abbiamo applicato con successo la tua richiesta di limitare il trattamento dei dati per la categoria: :type.',
        ],
        'account_deletion_requested' => [
            'title' => 'Richiesta di Cancellazione Account Ricevuta',
            'content' => 'Abbiamo ricevuto la tua richiesta di cancellare l\'account. Il processo sarà completato entro :days giorni. Durante questo periodo, puoi ancora annullare la richiesta accedendo nuovamente.',
        ],
        'account_deletion_processed' => [
            'title' => 'Account Cancellato con Successo',
            'content' => 'Come da tua richiesta, il tuo account e i dati associati sono stati eliminati definitivamente dalla nostra piattaforma. Ci dispiace vederti andare.',
        ],
        'breach_report_received' => [
            'title' => 'Segnalazione di Violazione Ricevuta',
            'content' => 'Grazie per la tua segnalazione. L\'abbiamo ricevuta con ID #:report_id e il nostro team di sicurezza la sta analizzando.',
        ],
        'status' => [
            'pending_user_confirmation' => 'In attesa di conferma da parte dell\'utente',
            'user_confirmed_action' => 'Azione dell\'utente confermata',
            'user_revoked_consent' => 'Azione dell\'utente revocata',
            'user_disavowed_suspicious' => 'Azione dell\'utente disconosciuta',
        ],
    ],

    'consent_management' => [
        'title' => 'Gestione dei Consensi',
        'subtitle' => 'Controlla come vengono utilizzati i tuoi dati personali',
        'description' => 'Qui puoi gestire le tue preferenze di consenso per diversi scopi e servizi.',
        'update_preferences' => 'Aggiorna le tue preferenze di consenso',
        'preferences_updated' => 'Le tue preferenze di consenso sono state aggiornate con successo.',
        'preferences_update_error' => 'Si è verificato un errore durante l\'aggiornamento delle tue preferenze di consenso. Riprova.',
    ],

    // Cookie Banner
    'cookie' => [
        'banner' => [
            'title' => 'Gestione dei Cookie',
            'description' => 'Utilizziamo i cookie per migliorare la tua esperienza di navigazione, fornire funzionalità personalizzate e analizzare il nostro traffico. Scegli le tue preferenze di consenso.',
            'privacy_policy_link' => 'Informativa Privacy',
            'accept_all' => 'Accetta Tutti',
            'reject_optional' => 'Solo Essenziali',
            'customize' => 'Personalizza',
            'preferences_title' => 'Preferenze Cookie',
            'preferences_short' => 'Cookie',
            'save_preferences' => 'Salva Preferenze',
            'close_preferences' => 'Chiudi',
            'close' => 'Chiudi',
            'saving' => 'Salvataggio in corso...',
            'required' => 'Obbligatorio',
        ],
        'categories' => [
            'essential' => [
                'label' => 'Cookie Essenziali',
                'description' => 'Necessari per il funzionamento base del sito web. Non possono essere disattivati.',
            ],
            'functional' => [
                'label' => 'Cookie Funzionali',
                'description' => 'Migliorano l\'esperienza utente con funzionalità avanzate e personalizzazione.',
            ],
            'analytics' => [
                'label' => 'Cookie Analitici',
                'description' => 'Ci aiutano a comprendere come utilizzi il sito per migliorare le prestazioni.',
            ],
            'marketing' => [
                'label' => 'Cookie di Marketing',
                'description' => 'Utilizzati per mostrarti annunci pubblicitari pertinenti e personalizzati.',
            ],
            'profiling' => [
                'label' => 'Cookie di Profilazione',
                'description' => 'Creano un profilo delle tue preferenze per personalizzare contenuti e servizi.',
            ],
        ],
        'consent_saved_successfully' => 'Le tue preferenze sui cookie sono state salvate con successo.',
        'consent_acknowledged' => 'Le tue preferenze sui cookie sono state registrate.',
        'consent_status_error' => 'Impossibile caricare le tue preferenze sui cookie.',
        'consent_save_error' => 'Errore nel salvataggio delle preferenze sui cookie.',
        'validation_error' => 'Dati di consenso non validi. Verifica le tue scelte.',
    ],

    // Footer
    'privacy_policy' => 'Informativa sulla Privacy',
    'terms_of_service' => 'Termini di Servizio',
    'all_rights_reserved' => 'Tutti i diritti riservati.',
    'navigation_label' => 'Navigazione GDPR',
    'main_content_label' => 'Contenuto principale GDPR',

    // Version Information
    'current_version' => 'Versione Corrente',
    'version' => 'Versione: 1.0',
    'effective_date' => 'Data di Entrata in Vigore: 30 Set 2025',
    'last_updated' => 'Ultimo Aggiornamento: 30 Set 2025, 17:41',

    // Actions
    'download_pdf' => 'Scarica PDF',
    'print' => 'Stampa',
];
