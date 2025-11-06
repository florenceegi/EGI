<?php

return [
    // Titoli e header
    'title' => 'Gestione Utility',
    'subtitle' => 'Aggiungi valore reale al tuo EGI',
    'status_configured' => 'Utility Configurata',
    'status_none' => 'Nessuna Utility',
    'available_images' => ':count immagini disponibili per ":title"',
    'view_details' => 'Visualizza Dettagli',
    'manage_utility' => 'Gestisci Utility',

    // Alert e messaggi
    'info_edit_before_publish' => 'La utility può essere aggiunta o modificata solo prima della pubblicazione della collection. Una volta pubblicata, non sarà più possibile modificarla.',
    'success_created' => 'Utility aggiunta con successo!',
    'success_updated' => 'Utility aggiornata con successo!',
    'confirm_reset' => 'Sei sicuro di voler annullare? Le modifiche non salvate andranno perse.',
    'confirm_remove_image' => 'Rimuovere questa immagine?',
    'note' => 'Nota',

    // Tipi di utility
    'types' => [
        'label' => 'Tipo di Utility',
        'physical' => [
            'label' => 'Bene Fisico',
            'description' => 'Oggetto fisico da spedire (quadro, scultura, etc.)'
        ],
        'service' => [
            'label' => 'Servizio',
            'description' => 'Servizio o esperienza (workshop, consulenza, etc.)'
        ],
        'hybrid' => [
            'label' => 'Ibrido',
            'description' => 'Combinazione fisico + servizio'
        ],
        'digital' => [
            'label' => 'Digitale',
            'description' => 'Contenuto o accesso digitale'
        ],
        'remove' => 'Rimuovi Utility'
    ],

    // Campi form base
    'fields' => [
        'title' => 'Titolo Utility',
        'title_placeholder' => 'Es: Quadro Originale 50x70cm',
        'description' => 'Descrizione Dettagliata',
        'description_placeholder' => 'Descrivi in dettaglio cosa riceverà l\'acquirente...',
    ],

    // Sezione spedizione
    'shipping' => [
        'title' => 'Dettagli Spedizione',
        'weight' => 'Peso (kg)',
        'weight_help' => 'Peso necessario per calcolare i costi di spedizione',
        'dimensions' => 'Dimensioni (cm)',
        'length' => 'Lunghezza',
        'width' => 'Larghezza',
        'height' => 'Altezza',
        'days' => 'Giorni preparazione/spedizione',
        'fragile' => 'Oggetto Fragile',
        'insurance' => 'Assicurazione Consigliata',
        'notes' => 'Note per la Spedizione',
        'notes_placeholder' => 'Istruzioni speciali per l\'imballaggio o la spedizione...'
    ],

    // Sezione servizio
    'service' => [
        'title' => 'Dettagli Servizio',
        'valid_from' => 'Valido Dal',
        'valid_until' => 'Valido Fino Al',
        'max_uses' => 'Numero Massimo Utilizzi',
        'max_uses_placeholder' => 'Lascia vuoto per illimitato',
        'instructions' => 'Istruzioni per l\'Attivazione',
        'instructions_placeholder' => 'Come l\'acquirente può usufruire del servizio...'
    ],

    // Escrow
    'escrow_tiers' => [
        'immediate' => 'Pagamento Immediato',
        'standard' => 'Escrow Standard',
        'premium' => 'Escrow Premium'
    ],

    'escrow' => [
        'immediate' => [
            'label' => 'Pagamento Immediato',
            'description' => 'Nessun escrow, pagamento diretto al creator'
        ],
        'standard' => [
            'label' => 'Escrow Standard',
            'description' => 'Fondi rilasciati dopo 14 giorni dalla consegna',
            'requirement_tracking' => 'Tracking obbligatorio'
        ],
        'premium' => [
            'label' => 'Escrow Premium',
            'description' => 'Fondi rilasciati dopo 21 giorni dalla consegna',
            'requirement_tracking' => 'Tracking obbligatorio',
            'requirement_signature' => 'Firma alla consegna',
            'requirement_insurance' => 'Assicurazione consigliata'
        ]
    ],

    // Media/Gallery
    'media' => [
        'title' => 'Galleria Immagini Dettagli',
        'description' => 'Aggiungi foto dell\'oggetto da vari angoli, dettagli importanti, certificati di autenticità, etc. (Max 10 immagini)',
        'upload_prompt' => 'Clicca per caricare o trascina le immagini qui',
        'current_images' => 'Immagini Attuali:',
        'remove_image' => 'Rimuovi',
        'images' => 'immagini',
        'no_images' => 'Nessuna immagine disponibile'
    ],

    // Validation errors
    'validation' => [
        'errors_found' => 'Si sono verificati alcuni errori:',
        'title_required' => 'Il titolo è obbligatorio',
        'type_required' => 'Seleziona un tipo di utility',
        'weight_required' => 'Il peso è obbligatorio per beni fisici',
        'valid_until_after' => 'La data di fine deve essere successiva alla data di inizio',
        'wait_upload_completion' => 'Attendere il completamento del caricamento delle immagini',
        'wait_before_save' => 'Attendere il completamento del caricamento delle immagini prima di salvare.',
        'upload_in_progress' => 'Upload già in corso. Attendere il completamento prima di aggiungere nuove immagini.',
        'select_images_only' => 'Per favore seleziona solo file immagine.',
        'images_too_large' => 'Alcune immagini superano i 10MB e non possono essere caricate.',
        'select_type' => 'Seleziona un tipo di utility',
        'title_required' => 'Il titolo è obbligatorio',
        'weight_required_physical' => 'Il peso è obbligatorio per beni fisici',
        'correct_errors' => 'Per favore correggi i seguenti errori:'
    ],

    // Azioni
    'actions' => [
        'delete' => 'Elimina Utility',
        'confirm_delete_title' => 'Conferma Eliminazione',
        'confirm_delete_message' => 'Sei sicuro di voler eliminare questa utility? Questa azione non può essere annullata.',
        'delete_success' => 'Utility eliminata con successo!',
        'delete_error' => 'Errore durante l\'eliminazione della utility.',
    ],

    // JavaScript messages
    'js' => [
        'drag_drop_text' => 'Trascina qui le immagini o clicca per selezionare',
        'processing_images' => 'Elaborazione immagini in corso...',
        'upload_in_progress' => 'Caricamento immagini in corso...',
        'uploading' => 'Caricamento in corso...',
        'upload_completed' => 'Immagini caricate con successo!'
    ],

    'upload_completed' => 'Immagini caricate con successo!'
];
