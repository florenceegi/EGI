<?php

/**
 * Italian translations for PA Acts Tokenization System
 * 
 * @package Resources\Lang\IT
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - PA Acts Tokenization)
 * @date 2025-10-04
 * @purpose Localizzazione italiana per sistema tokenizzazione atti PA
 */

return [
    // Page titles
    'title' => 'Atti Tokenizzati',
    'page_title' => 'Gestione Atti Amministrativi',
    'dashboard_title' => 'Dashboard Atti',

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
        'protocol_date_required' => 'La data di protocollo è obbligatoria',
        'doc_type_required' => 'Il tipo di documento è obbligatorio',
        'title_required' => 'Il titolo è obbligatorio',
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
    ]
];
