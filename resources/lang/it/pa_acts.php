<?php

/**
 * Italian translations for N.A.T.A.N. Intelligence System
 *
 * @package Resources\Lang\IT
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - N.A.T.A.N. AI Document Intelligence)
 * @date 2025-10-04
 * @purpose Localizzazione italiana per sistema N.A.T.A.N. - Nodo di Analisi e Tracciamento Atti Notarizzati
 */

return [
    // Page titles
    'title' => 'N.A.T.A.N. Intelligence Center',
    'page_title' => 'N.A.T.A.N. - Nodo di Analisi e Tracciamento Atti Notarizzati',
    'dashboard_title' => 'N.A.T.A.N. AI Dashboard',

    // Actions
    'upload_act' => 'Carica Atto',
    'view_act' => 'Visualizza Atto',
    'download_original' => 'Scarica Originale',
    'verify_act' => 'Verifica Atto',
    'copy_verification_link' => 'Copia Link Verifica',

    // Document types (from config)
    'doc_types' => [
        'delibera' => [
            'label' => 'Delibera',
            'description' => 'Deliberazione di Giunta o Consiglio'
        ],
        'determina' => [
            'label' => 'Determina',
            'description' => 'Determinazione dirigenziale'
        ],
        'ordinanza' => [
            'label' => 'Ordinanza',
            'description' => 'Ordinanza sindacale o dirigenziale'
        ],
        'decreto' => [
            'label' => 'Decreto',
            'description' => 'Decreto amministrativo'
        ],
        'atto' => [
            'label' => 'Atto Generico',
            'description' => 'Atto amministrativo generico'
        ]
    ],

    // Form fields
    'protocol_number' => 'Numero Protocollo',
    'protocol_date' => 'Data Protocollo',
    'doc_type' => 'Tipo Documento',
    'doc_title' => 'Titolo Documento',
    'doc_description' => 'Descrizione/Oggetto',
    'select_file' => 'Seleziona File PDF',
    'select_fascicolo' => 'Seleziona Fascicolo',

    // Upload instructions
    'upload_instructions' => 'Trascina qui il PDF firmato digitalmente o clicca per selezionare',
    'upload_instructions_short' => 'PDF firmato digitalmente',
    'drop_file_here' => 'Rilascia il file qui',

    // Tokenization status
    'tokenization_status' => 'Stato Tokenizzazione',
    'status_pending' => 'In Attesa',
    'status_validating' => 'Validazione in Corso',
    'status_anchoring' => 'Ancoraggio su Blockchain',
    'status_completed' => 'Completato',
    'status_failed' => 'Fallito',

    // Blockchain info
    'blockchain_txid' => 'Transaction ID Blockchain',
    'blockchain_anchor_date' => 'Data Ancoraggio',
    'merkle_root' => 'Merkle Root',
    'merkle_proof' => 'Prova Merkle',
    'batch_id' => 'ID Batch',

    // Signature validation
    'signature_validation' => 'Validazione Firma Digitale',
    'signature_valid' => 'Firma Valida',
    'signature_invalid' => 'Firma Non Valida',
    'signature_not_found' => 'Firma Non Presente',
    'signer' => 'Firmatario',
    'signer_cn' => 'Nome Firmatario',
    'signer_email' => 'Email Firmatario',
    'cert_serial' => 'Seriale Certificato',
    'cert_issuer' => 'Emittente Certificato',
    'signature_timestamp' => 'Timestamp Firma',
    'validation_date' => 'Data Validazione',

    // Document hash
    'doc_hash' => 'Hash Documento',
    'doc_hash_short' => 'Hash',
    'hash_algorithm' => 'Algoritmo Hash',
    'hash_calculated' => 'Hash Calcolato',

    // Public verification
    'public_verification' => 'Verifica Pubblica',
    'public_code' => 'Codice Verifica',
    'public_url' => 'URL Verifica Pubblica',
    'qr_code' => 'QR Code',
    'scan_qr' => 'Scansiona per Verificare',
    'verification_page_title' => 'Verifica Autenticità Atto',

    // Stats & KPI
    'view_count' => 'Visualizzazioni',
    'download_count' => 'Download',
    'last_viewed_at' => 'Ultima Visualizzazione',
    'last_downloaded_at' => 'Ultimo Download',

    // Table headers
    'table' => [
        'title' => 'Titolo',
        'protocol' => 'Protocollo',
        'date' => 'Data',
        'type' => 'Tipo',
        'status' => 'Stato',
        'txid' => 'TXID',
        'actions' => 'Azioni'
    ],

    // Validation messages
    'validation' => [
        'pdf_only' => 'Solo file PDF sono ammessi',
        'max_size' => 'Dimensione massima: :size MB',
        'signature_required' => 'Il PDF deve essere firmato digitalmente',
        'protocol_required' => 'Il numero di protocollo è obbligatorio',
        'protocol_format' => 'Il numero di protocollo deve essere nel formato XXXXX/YYYY (es: 12345/2025)',
        'protocol_date_required' => 'La data di protocollo è obbligatoria',
        'date_required' => 'La data del protocollo è obbligatoria',
        'date_future' => 'La data del protocollo non può essere futura',
        'doc_type_required' => 'Il tipo di documento è obbligatorio',
        'type_required' => 'Il tipo di documento è obbligatorio',
        'type_invalid' => 'Il tipo di documento selezionato non è valido',
        'title_required' => 'Il titolo è obbligatorio',
        'title_max' => 'Il titolo non può superare i 255 caratteri',
        'description_max' => 'La descrizione non può superare i 5000 caratteri',
        'min_file_size' => 'Il file è troppo piccolo (minimo 1KB)',
        'invalid_pdf' => 'File PDF non valido',
        'upload_failed' => 'Caricamento fallito',
        'validation_failed' => 'Validazione fallita'
    ],

    // Success messages
    'success' => [
        'upload_completed' => 'Atto caricato con successo',
        'tokenization_started' => 'Tokenizzazione avviata',
        'tokenization_completed' => 'Tokenizzazione completata',
        'verification_link_copied' => 'Link di verifica copiato negli appunti'
    ],

    // Error messages
    'errors' => [
        'upload_failed' => 'Errore durante il caricamento',
        'tokenization_failed' => 'Errore durante la tokenizzazione',
        'document_not_found' => 'Documento non trovato',
        'invalid_verification_code' => 'Codice di verifica non valido',
        'signature_validation_failed' => 'Errore durante la validazione della firma',
        'blockchain_error' => 'Errore durante l\'ancoraggio su blockchain'
    ],

    // Info messages
    'info' => [
        'batch_pending' => 'L\'atto sarà incluso nel prossimo batch giornaliero',
        'signature_mock' => 'Validazione firma in modalità mock (sviluppo)',
        'blockchain_mock' => 'Ancoraggio blockchain in modalità mock (sviluppo)',
        'no_documents' => 'Nessun atto presente',
        'verification_instructions' => 'Condividi questo link o QR code per permettere la verifica pubblica dell\'autenticità del documento'
    ],

    // Buttons
    'buttons' => [
        'upload' => 'Carica',
        'cancel' => 'Annulla',
        'verify' => 'Verifica',
        'download' => 'Scarica',
        'view_details' => 'Visualizza Dettagli',
        'copy_link' => 'Copia Link',
        'generate_qr' => 'Genera QR Code'
    ],

    // Breadcrumbs
    'breadcrumbs' => [
        'dashboard' => 'Dashboard',
        'acts' => 'Atti',
        'upload' => 'Carica Atto',
        'view' => 'Visualizza Atto',
        'verify' => 'Verifica'
    ],

    // Index page
    'index' => [
        'page_title' => 'N.A.T.A.N. Intelligence Center',
        'title' => 'N.A.T.A.N. - Nodo di Analisi e Tracciamento Atti Notarizzati',
        'subtitle' => 'Intelligenza Artificiale per l\'analisi e certificazione blockchain degli atti amministrativi',
        'upload_new_act' => 'Carica Nuovo Atto',

        // Stats
        'stats' => [
            'total' => 'Totale Atti',
            'anchored' => 'Ancorati su Blockchain',
            'pending' => 'In Attesa di Ancoraggio',
            'failed' => 'Tokenizzazione Fallita',
            'success_rate' => 'Tasso di Successo',
            'avg_time' => 'Tempo Medio Ancoraggio',
            'this_month' => 'Questo Mese'
        ],

        // Filters
        'filters' => [
            'search' => 'Cerca',
            'search_placeholder' => 'Protocollo o titolo...',
            'doc_type' => 'Tipo Atto',
            'all_types' => 'Tutti i tipi',
            'date_from' => 'Data Da',
            'date_to' => 'Data A',
            'status' => 'Stato',
            'status_all' => 'Tutti',
            'status_anchored' => 'Ancorati',
            'status_pending' => 'In Attesa',
            'apply' => 'Applica Filtri',
            'reset' => 'Resetta'
        ],

        // Table
        'table' => [
            'protocol' => 'Protocollo',
            'title' => 'Titolo',
            'type' => 'Tipo',
            'status' => 'Stato',
            'actions' => 'Azioni'
        ],

        // Status
        'status' => [
            'anchored' => 'Ancorato',
            'pending' => 'In Attesa'
        ],

        // Actions
        'actions' => [
            'view' => 'Visualizza',
            'view_detail' => 'Visualizza Dettaglio'
        ],

        // Empty state
        'empty' => [
            'title' => 'Nessun Atto Trovato',
            'description' => 'Non hai ancora caricato atti tokenizzati. Inizia caricando il tuo primo documento firmato digitalmente.',
            'cta' => 'Carica Primo Atto'
        ]
    ],

    // Show page
    'show' => [
        'page_title' => 'N.A.T.A.N. Analysis - :protocol',
        'back_to_list' => 'Torna alla Lista',
        'protocol_date' => 'Data Protocollo',

        // Status
        'status' => [
            'anchored' => 'Ancorato su Blockchain',
            'pending' => 'In Attesa di Ancoraggio'
        ],

        // Metadata section
        'metadata' => [
            'title' => 'Metadati Documento',
            'act_title' => 'Titolo Atto',
            'description' => 'Descrizione',
            'upload_date' => 'Data Caricamento',
            'entity' => 'Ente'
        ],

        // Signature section
        'signature' => [
            'title' => 'Firma Digitale',
            'valid' => 'Firma Valida',
            'qes_pades' => 'Firma Qualificata QES/PAdES',
            'signer_name' => 'Firmatario',
            'signer_role' => 'Ruolo',
            'organization' => 'Organizzazione',
            'certificate_issuer' => 'Emittente Certificato',
            'timestamp' => 'Data e Ora Firma'
        ],

        // Blockchain section
        'blockchain' => [
            'title' => 'Dati Blockchain',
            'txid' => 'ID Transazione (TXID)',
            'merkle_root' => 'Merkle Root',
            'document_hash' => 'Hash Documento',
            'anchored_at' => 'Ancorato il',
            'explorer' => 'Explorer',
            'view_explorer' => 'Visualizza su Explorer',
            'pending_title' => 'Ancoraggio in Corso',
            'pending_description' => 'Il documento sarà ancorato su blockchain nel prossimo batch (entro 24 ore). Una volta completato, riceverai una notifica e potrai visualizzare i dati blockchain.'
        ],

        // Verification section
        'verification' => [
            'title' => 'Verifica Pubblica',
            'qr_description' => 'Scansiona per verificare l\'autenticità',
            'public_code' => 'Codice Verifica',
            'public_url' => 'URL Verifica Pubblica',
            'copy_code' => 'Copia Codice',
            'copy_url' => 'Copia URL',
            'copied' => 'Copiato negli appunti!',
            'open_public_page' => 'Apri Pagina Pubblica'
        ],

        // Tokenization section (error tracking)
        'tokenization' => [
            // Status titles
            'pending_title' => 'Ancoraggio Blockchain',
            'processing_title' => 'Tokenizzazione in Corso',
            'failed_title' => 'Tokenizzazione Fallita',

            // Status messages
            'pending_message' => 'In attesa di ancoraggio su blockchain Algorand',
            'processing_message' => 'Ancoraggio su blockchain in corso...',

            // Help texts
            'pending_help' => 'L\'atto è stato caricato correttamente e la firma digitale è stata validata. L\'ancoraggio su blockchain avverrà nei prossimi minuti. Se l\'ancoraggio non avviene automaticamente, puoi forzarlo manualmente con il pulsante sopra.',
            'processing_help' => 'Il sistema sta ancorandoil documento su blockchain Algorand. Questa operazione richiede solitamente 30-60 secondi. La pagina si aggiornerà automaticamente al completamento.',
            'failed_help' => 'Clicca "Riprova" per tentare nuovamente l\'ancoraggio',

            // Buttons
            'force_button' => 'Forza Tokenizzazione',
            'retry_button' => 'Riprova Tokenizzazione',

            // Error display
            'error_label' => 'Dettaglio Errore',
            'attempts_label' => 'Tentativi effettuati',

            // Status labels (for badges/pills)
            'status_pending' => 'In Attesa',
            'status_processing' => 'In Elaborazione',
            'status_completed' => 'Completato',
            'status_failed' => 'Fallito'
        ]
    ],

    // Public verification page
    'verify' => [
        'page_title' => 'Verifica Atto PA',
        'title' => 'Verifica Atto Pubblico',
        'subtitle' => 'Verifica l\'autenticità di un atto della Pubblica Amministrazione tramite blockchain',
        'protocol_date' => 'Data Protocollo',
        'document_title' => 'Titolo Documento',
        'entity' => 'Ente Emittente',

        // Verification results
        'verified' => [
            'title' => 'DOCUMENTO VERIFICATO SU BLOCKCHAIN',
        ],
        'pending' => [
            'title' => 'DOCUMENTO IN ATTESA DI TOKENIZZAZIONE',
            'info' => 'Il documento è in attesa di ancoraggio su blockchain. L\'operazione verrà completata entro 24 ore.'
        ],
        'failed' => [
            'title' => 'VERIFICA FALLITA'
        ],
        'not_found' => [
            'title' => 'CODICE VERIFICA NON VALIDO',
            'message' => 'Il codice di verifica inserito non corrisponde a nessun atto tokenizzato. Verifica di aver copiato correttamente il codice.',
            'code_shown' => 'Codice inserito'
        ],

        // Digital signature
        'digital_signature' => 'Firma Digitale',
        'signer_name' => 'Firmatario',
        'signer_role' => 'Ruolo',
        'certificate_issuer' => 'Emittente Certificato',
        'signature_timestamp' => 'Data e Ora Firma',

        // Blockchain data
        'blockchain_data' => 'Dati Blockchain',
        'transaction_id' => 'ID Transazione',
        'document_hash' => 'Hash Documento (SHA-256)',
        'anchored_at' => 'Ancorato il',
        'explorer' => 'Explorer',
        'view_explorer' => 'Visualizza su Algorand Explorer',

        // Trust indicators
        'signature_valid' => 'Firma Valida',
        'qes_certificate' => 'Certificato QES',
        'blockchain_confirmed' => 'Confermato su Blockchain',
        'algorand_network' => 'Rete Algorand',

        // How to verify
        'how_to' => [
            'title' => 'Come Verificare l\'Autenticità',
            'step1' => 'Trova il codice di verifica sul documento cartaceo o digitale (formato: VER-XXXXXXXXXX)',
            'step2' => 'Inserisci il codice nella barra di ricerca o scansiona il QR code con il tuo smartphone',
            'step3' => 'Verifica che i dati mostrati (protocollo, titolo, ente) corrispondano al documento',
            'step4' => 'Controlla lo stato della firma digitale e dell\'ancoraggio blockchain'
        ]
    ]
];
