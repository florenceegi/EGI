<?php

return [
    'title' => 'Tratti e Attributi',
    'locked_on_ipfs' => 'Bloccato su IPFS',
    'empty_state' => 'Nessun tratto aggiunto. I tratti rendono il tuo EGI unico e ricercabile.',
    'add_first_trait' => 'Aggiungi il primo tratto',
    'add_trait' => 'Aggiungi Tratto',
    'add_new_trait' => 'Aggiungi Nuovo Tratto',
    'remove_trait' => 'Rimuovi tratto',
    'have_this' => 'hanno questo',

    // Modal
    'modal_title' => 'Aggiungi Nuovo Tratto',
    'select_category' => 'Seleziona Categoria',
    'select_type' => 'Seleziona Tipo',
    'select_value' => 'Seleziona o Inserisci Valore',
    'choose_type' => 'Scegli un tipo...',
    'choose_value' => 'Scegli un valore...',
    'enter_value' => 'Inserisci valore',
    'preview' => 'Anteprima',
    'cancel' => 'Annulla',
    'add' => 'Aggiungi',
    'loading_categories' => 'Caricamento categorie...',
    'loading_types' => 'Caricamento tipi...',
    'insert_value' => 'Inserisci valore',
    'insert_numeric_value' => 'Inserisci valore numerico',

    // Counter
    'traits_counter' => ':count/30',
    'save_all_traits' => 'Salva Tutti i Tratti',

    // Messages
    'modal_error' => 'Errore nell\'apertura della modale. Riprova.',
    'save_success' => 'Tratti salvati con successo!',
    'save_error' => 'Errore nel salvataggio',
    'network_error' => 'Errore di rete nel salvataggio',
    'unknown_error' => 'Errore sconosciuto',

    // JavaScript Messages
    'remove_success' => 'Trait rimosso con successo',
    'remove_error' => 'Errore durante la rimozione',
    'network_error_js' => 'Errore di rete',
    'unauthorized' => 'Non autorizzato',
    'confirm_remove' => 'Sei sicuro di voler rimuovere questo trait?',
    'creator_only_modify' => 'Solo il creator può modificare i traits di questo EGI',
    'modal_open_error' => 'Errore apertura modal',
    'add_trait_error' => 'Errore aggiunta trait',
    'unknown_error_js' => 'Errore sconosciuto',
    'network_error_general' => 'Errore di rete',
    'add_success' => 'Trait aggiunto con successo',

    // SweetAlert2 Messages
    'confirm_delete_title' => 'Rimuovere questo trait?',
    'confirm_delete_text' => 'Questa azione non può essere annullata',
    'confirm_delete_button' => 'Sì, rimuovi',
    'cancel_button' => 'Annulla',
    'delete_success_title' => 'Rimosso!',
    'delete_success_text' => 'Il trait è stato rimosso con successo',
    'delete_error_title' => 'Errore!',
    'delete_error_text' => 'Si è verificato un errore durante la rimozione',

    // Categories
    'categories' => [
        'materials' => 'Materiali',
        'visual' => 'Visuale',
        'dimensions' => 'Dimensioni',
        'special' => 'Speciale',
        'sustainability' => 'Sostenibilità',
    ],

    // Trait Types
    'types' => [
        'primary_material' => 'Materiale Primario',
        'finish' => 'Finitura',
        'technique' => 'Tecnica',
        'primary_color' => 'Colore Primario',
        'style' => 'Stile',
        'mood' => 'Atmosfera',
        'size' => 'Dimensione',
        'weight' => 'Peso',
        'height' => 'Altezza',
        'width' => 'Larghezza',
        'edition' => 'Edizione',
        'signature' => 'Firma',
        'condition' => 'Condizione',
        'year_created' => 'Anno di Creazione',
        'recycled_content' => 'Contenuto Riciclato',
        'carbon_footprint' => 'Impronta Carbonica',
        'eco_certification' => 'Certificazione Eco',
        'sustainability_score' => 'Punteggio Sostenibilità',
    ],

    // Image Management
    'detail_title' => 'Dettagli Tratto',
    'image_section' => 'Immagine del Tratto',
    'no_image' => 'Nessuna immagine caricata',
    'upload_image' => 'Carica Immagine',
    'delete_image' => 'Elimina Immagine',
    'uploading' => 'Caricamento in corso',
    'information' => 'Informazioni',
    'category' => 'Categoria',
    'rarity' => 'Rarità',
    'value' => 'Valore',
    'description' => 'Descrizione',
    'image_alt_placeholder' => 'Testo alternativo per l\'immagine',
    'image_description_placeholder' => 'Descrizione dell\'immagine (opzionale)',
    'upload_success' => 'Immagine caricata con successo',
    'upload_error' => 'Errore nel caricamento dell\'immagine',
    'delete_success' => 'Immagine eliminata con successo',
    'delete_error' => 'Errore nell\'eliminazione dell\'immagine',
    'confirm_delete' => 'Sei sicuro di voler eliminare questa immagine?',
    'no_image_to_delete' => 'Nessuna immagine da eliminare',
    'info_error' => 'Errore nel recupero delle informazioni',
    'preview_selected' => 'Anteprima file selezionato',
    'file_too_large' => 'File troppo grande (max 5MB)',
    'invalid_file_type' => 'Tipo di file non valido',
    'click_to_view_details' => 'Clicca per visualizzare i dettagli',
    'has_image' => 'Ha immagine',
    'confirm_delete' => 'Sei sicuro di voler eliminare questa immagine?',

    // ===============================
    // CoA VOCABULARY (Vocabolario CoA)
    // ===============================
    
    'technique' => [
        'painting-oil' => 'pittura a olio',
        'painting-acrylic' => 'pittura acrilica', 
        'painting-watercolor' => 'acquerello',
        'painting-tempera' => 'tempera',
        'painting-gouache' => 'guazzo (gouache)',
        'printmaking-etching' => 'acquaforte',
        'printmaking-lithography' => 'litografia',
        'photography-inkjet' => 'stampa inkjet a pigmenti',
        'sculpture-carving' => 'scultura per sottrazione',
        'jewelry-fabrication' => 'fabbricazione (gioielleria)',
    ],
    
    'materials' => [
        'paint-oil' => 'colore a olio',
        'paint-acrylic' => 'colore acrilico',
        'metal-bronze' => 'bronzo',
        'metal-gold' => 'oro',
    ],
    
    'support' => [
        'support-canvas-stretched-cotton' => 'tela di cotone intelaiata',
        'support-canvas-stretched-linen' => 'tela di lino intelaiata',
        'support-wood-panel' => 'tavola di legno',
        'support-paper-rag' => 'carta cotone (rag)',
    ],
];
