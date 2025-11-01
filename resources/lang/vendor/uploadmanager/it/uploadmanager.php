<?php

return [

    'dev' => [
        'invalid_file' => 'File non valido o mancante: :fileName',
        'invalid_file_validation' => 'Validazione fallita per il file :fileName: :error',
        'error_saving_file_metadata' => 'Impossibile salvare i metadati del file :fileName',
        'server_limits_restrictive' => 'I limiti di upload del server sono più restrittivi rispetto alle impostazioni dell\'applicazione',
        // ... altri messaggi
    ],
    'user' => [
        'invalid_file' => 'Il file caricato non è valido. Riprova con un altro file.',
        'invalid_file_validation' => 'Il file non soddisfa i requisiti. Verifica formato e dimensione.',
        'error_saving_file_metadata' => 'Si è verificato un errore durante il salvataggio delle informazioni sul file.',
        'server_limits_restrictive' => '',
        // ... altri messaggi
    ],

    'upload' => [
        'max_files' => 'Massimo :count file',
        'max_file_size' => 'Massimo :size per file',
        'max_total_size' => 'Massimo :size totale',
        'max_files_error' => 'Puoi caricare un massimo di :count file alla volta.',
        'max_file_size_error' => 'Il file ":name" supera la dimensione massima consentita (:size).',
        'max_total_size_error' => 'La dimensione totale dei file (:size) supera il limite consentito (:limit).',
    ],

    // Badge funzionalità enterprise (Punto 4)
    'storage_space_unit' => 'GB',
    'secure_storage' => 'Archiviazione Sicura',
    'secure_storage_tooltip' => 'I tuoi file vengono salvati con ridondanza per proteggere i tuoi asset',
    'virus_scan_feature' => 'Scansione Virus',
    'virus_scan_tooltip' => 'Ogni file viene scansionato per rilevare potenziali minacce prima dell\'archiviazione',
    'advanced_validation' => 'Validazione Avanzata',
    'advanced_validation_tooltip' => 'Validazione del formato e integrità dei file',
    'storage_space' => 'Spazio: :used/:total GB',
    'storage_space_tooltip' => 'Spazio di archiviazione disponibile per i tuoi EGI',
    'toggle_virus_scan' => 'Attiva/disattiva la scansione virus',

    // Metadata EGI (Punto 3)
    'quick_egi_metadata' => 'Metadata EGI rapidi',
    'egi_title' => 'Titolo EGI',
    'egi_title_placeholder' => 'Es. Pixel Dragon #123',
    'egi_title_info' => 'Il titolo dell\'opera. Sarà visibile nel marketplace e nelle gallerie.',
    'egi_collection' => 'Collezione',
    'select_collection' => 'Seleziona collezione',
    'existing_collections' => 'Collezioni esistenti',
    'create_new_collection' => 'Crea nuova collezione',
    'egi_description' => 'Descrizione',
    'egi_description_placeholder' => 'Breve descrizione dell\'opera...',
    'metadata_notice' => 'Questi metadata saranno associati al tuo EGI, ma potrai modificarli in seguito.',
    'floor_price' => 'Prezzo Base',
    'floor_price_placeholder' => 'Es. 1.000 ALGO',
    'floor_price_info' => 'Il prezzo minimo iniziale per acquistare questo EGI. Stabilisce il valore base dell\'asset nel marketplace.',
    'creation_date' => 'Data di Creazione',
    'creation_date_placeholder' => 'Es. 2023-10-01',
    'creation_date_info' => 'La data ufficiale di creazione dell\'EGI. Verrà registrata sulla blockchain e utilizzata per verificare l\'autenticità e la cronologia dell\'asset.',
    'position' => 'Posizione',
    'position_placeholder' => 'Es. 1/1000',
    'position_info' => 'Indica la posizione dell\'EGI all\'interno della collezione (es. 1/1000 significa "primo di mille"). Determina rarità e posizionamento nelle gallery.',
    'publish_egi' => 'Pubblica EGI',
    'publish_egi_tooltip' => 'Pubblica il tuo EGI nella collezione selezionata',

    // Accessibilità (Punto 5)
    'select_files_aria' => 'Seleziona file per l\'upload',
    'select_files_tooltip' => 'Seleziona uno o più file dal tuo dispositivo',
    'save_aria' => 'Salva i file selezionati',
    'save_tooltip' => 'Carica i file selezionati sul server',
    'cancel_aria' => 'Annulla l\'upload corrente',
    'cancel_tooltip' => 'Annulla l\'operazione e rimuovi i file selezionati',
    'return_aria' => 'Torna alla collezione',
    'return_tooltip' => 'Torna alla vista della collezione senza salvare',

    // Generale
    'file_saved_successfully' => 'File :fileCaricato salvato con successo',
    'file_deleted_successfully' => 'File eliminato con successo',
    'first_template_title' => 'EGI Manager',
    'file_upload' => 'Caricamento File',
    'max_file_size_reminder' => 'Dimensione massima del file: 10MB',
    'upload_your_files' => 'Carica i tuoi file',
    'save_the_files' => 'Salva file',
    'cancel' => 'Annulla',
    'return_to_collection' => 'Torna alla collezione',
    'mint_your_masterpiece' => 'Crea il Tuo Capolavoro',
    'preparing_to_mint' => 'Sto aspettando i tuoi file, caro...',
    'cancel_confirmation' => 'Vuoi cancellare?',
    'waiting_for_upload' => 'Stato Upload: In attesa...',
    'server_unexpected_response' => 'Il server ha restituito una risposta non valida o inaspettata.',
    'unable_to_save_after_recreate' => 'Impossibile salvare il file dopo aver ricreato la directory.',
    'config_not_loaded' => 'Configurazione globale non caricata. Assicurati che i dati siano stati recuperati.',
    'drag_files_here' => 'Trascina i file qui',
    'select_files' => 'Seleziona i file',
    'or' => 'o',

    // Validation messages
    'allowedExtensionsMessage' => 'Estensione del file non consentita. Le estensioni consentite sono: :allowedExtensions',
    'allowedMimeTypesMessage' => 'Tipo di file non consentito. I tipi di file consentiti sono: :allowedMimeTypes',
    'maxFileSizeMessage' => 'Dimensione del file troppo grande. La dimensione massima consentita è :maxFileSize',
    'minFileSizeMessage' => 'Dimensione del file troppo piccola. La dimensione minima consentita è :minFileSize',
    'maxNumberOfFilesMessage' => 'Numero massimo di file superato. Il numero massimo consentito è :maxNumberOfFiles',
    'acceptFileTypesMessage' => 'Tipo di file non consentito. I tipi di file consentiti sono: :acceptFileTypes',
    'invalidFileNameMessage' => 'Nome del file non valido. Il nome del file non può contenere i seguenti caratteri: / \ ? % * : | " < >',

    // Scansione virus
    'virus_scan_disabled' => 'Scansione virus disabilitata',
    'virus_scan_enabled' => 'Scansione virus abilitata',
    'antivirus_scan_in_progress' => 'Scansione antivirus in corso',
    'scan_skipped_but_upload_continues' => 'Scansione saltata, ma il caricamento continua',
    'scanning_stopped' => 'Scansione interrotta',
    'file_scanned_successfully' => 'File :fileCaricato scansionato con successo',
    'one_or_more_files_were_found_infected' => 'Uno o più file sono stati rilevati come infetti',
    'all_files_were_scanned_no_infected_files' => 'Tutti i file sono stati scansionati e nessun file infetto è stato trovato',
    'the_uploaded_file_was_detected_as_infected' => 'Il file caricato è stato rilevato come infetto',
    'possible_scanning_issues' => 'Avviso: possibili problemi durante la scansione virus',
    'unable_to_complete_scan_continuing' => 'Avviso: Impossibile completare la scansione virus, ma procediamo comunque',

    // Messaggi di stato
    'im_checking_the_validity_of_the_file' => 'Controllo la validità del file',
    'im_recording_the_information_in_the_database' => 'Registrazione delle informazioni nel database',
    'all_files_are_saved' => 'Tutti i file sono stati salvati',
    'upload_failed' => 'Caricamento fallito',
    'some_errors' => 'Si sono verificati alcuni errori',
    'no_file_uploaded' => 'Nessun file caricato',

    // Traduzioni JavaScript
    // JavaScript translations - camelCase per compatibilità TypeScript
    'js' => [
        // Upload processing
        'uploadProcessingError' => 'Errore durante l\'elaborazione dell\'upload',
        'invalidServerResponse' => 'Il server ha restituito una risposta non valida o inaspettata.',
        'unexpectedUploadError' => 'Errore imprevisto durante il caricamento.',
        'criticalUploadError' => 'Errore critico durante l\'upload',
        'errorDuringUpload' => 'Errore durante il caricamento',
        'errorDuringUploadRequest' => 'Errore durante la richiesta di upload',

        // Virus scanning
        'fileNotFoundForScan' => 'File non trovato per la scansione antivirus',
        'scanError' => 'Errore durante la scansione antivirus',
        'enableVirusScanning' => 'Scansione virus abilitata',
        'disableVirusScanning' => 'Scansione virus disabilitata',
        'virusScanAdvise' => 'La scansione virus potrebbe rallentare il processo di caricamento',
        'Scanning_stopped' => 'Scansione interrotta',

        // Upload states
        'noFileSpecified' => 'Nessun file specificato',
        'confirmCancel' => 'Vuoi cancellare?',
        'uploadWaiting' => 'Stato Upload: In attesa...',
        'startingUpload' => 'Inizio caricamento',
        'startingSaving' => 'Inizio salvataggio',
        'startingScan' => 'Inizio scansione',
        'loading' => 'Caricamento in corso',
        'uploadFinished' => 'Caricamento completato',
        'uploadAndScan' => 'Caricamento e scansione completati',
        'scanningComplete' => 'Scansione completata',
        'scanningSuccess' => 'Scansione completata con successo',

        // Errors
        'serverError' => 'Errore del server',
        'saveError' => 'Errore durante il salvataggio',
        'configError' => 'Errore di configurazione',
        'someError' => 'Si sono verificati alcuni errori',
        'completeFailure' => 'Fallimento completo',
        'unknownError' => 'Errore sconosciuto',
        'unspecifiedError' => 'Errore non specificato',

        // File operations
        'deleteButton' => 'Elimina',
        'deleteFileError' => 'Errore durante l\'eliminazione del file',
        'errorDeleteTempLocal' => 'Errore durante l\'eliminazione del file temporaneo locale',
        'errorDeleteTempExt' => 'Errore durante l\'eliminazione del file temporaneo esterno',

        // UI elements
        'of' => 'di',
        'okButton' => 'OK',
        'checkFilesGuide' => 'Controlla la guida sui file',

        // Validation
        'invalidFilesTitle' => 'File non validi',
        'invalidFilesMessage' => 'Alcuni file non sono validi',

        // Emoji
        'emojiHappy' => '😊',
        'emojiSad' => '😢',
        'emojiAngry' => '😠',
    ],

    // EGI Type Selection (Dual Architecture)
    'egi_type_label' => 'Tipo di EGI',
    'egi_type_help' => 'Scegli come verrà mintato il tuo EGI',
    'egi_type_asa' => 'EGI Classico (ASA)',
    'egi_type_asa_desc' => 'Asset statico su blockchain Algorand. Certificato permanente, autenticità garantita.',
    'egi_type_smart_contract' => 'EGI Vivente (SmartContract)',
    'egi_type_smart_contract_desc' => 'Asset intelligente con AI integrata. Analisi automatica, promozione e memoria evolutiva.',
    'free' => 'Gratuito',
    'egi_type_notice' => 'Il tipo scelto determinerà le funzionalità disponibili per il tuo EGI. Potrai sempre mintarlo in seguito.',
];