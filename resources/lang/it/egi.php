<?php

return [

    /*
    |--------------------------------------------------------------------------
    | EGI (Ecological Goods Invent) - Traduzioni Italiane
    |--------------------------------------------------------------------------
    |
    | Traduzioni per il sistema CRUD degli EGI in FlorenceEGI
    | Versione: 1.0.0 - Oracode System 2.0 Compliant
    |
    */

    // Meta e SEO
    'meta_description_default' => 'Dettagli per EGI: :title',
    'image_alt_default' => 'Immagine EGI',
    'view_full' => 'Visualizza Completa',
    'artwork_loading' => 'Opera in Caricamento...',

    // Informazioni Base
    'by_author' => 'di :name',
    'unknown_creator' => 'Artista Sconosciuto',

    // Azioni Principali
    'like_button_title' => 'Aggiungi ai Preferiti',
    'unlike_button_title' => 'Rimuovi dai Preferiti',
    'like_button_aria' => 'Aggiungi questo EGI ai tuoi preferiti',
    'unlike_button_aria' => 'Rimuovi questo EGI dai tuoi preferiti',
    'share_button_title' => 'Condividi questo EGI',
    'current_price' => 'Prezzo Attuale',
    'not_currently_listed' => 'Da Attivare',
    'contact_owner_availability' => 'Contatta il proprietario per disponibilità',
    'not_for_sale' => 'Non in vendita',
    'not_for_sale_description' => 'Questo EGI non è attualmente disponibile per l\'acquisto',
    'liked' => 'Piaciuto',
    'add_to_favorites' => 'Aggiungi ai Preferiti',
    'reserve_this_piece' => 'Attivalo',

    /*
    |--------------------------------------------------------------------------
    | NFT Card System - Sistema Carte NFT
    |--------------------------------------------------------------------------
    */

    // Badge e Stati
    'badge' => [
        'owned' => 'POSSEDUTO',
        'media_content' => 'Contenuto Media',
        'winning_bid' => 'OFFERTA VINCENTE',
        'outbid' => 'SUPERATO',
        'not_owned' => 'NON POSSEDUTO',
        'to_activate' => 'DA ATTIVARE',
        'activated' => 'ATTIVATO',
    ],

    // Titoli
    'title' => [
        'untitled' => '✨ EGI Senza Titolo',
    ],

    // Piattaforma
    'platform' => [
        'powered_by' => 'Powered by :platform',
    ],

    // Artista
    'creator' => [
        'created_by' => '👨‍🎨 Creato da:',
    ],

    // Prezzi
    'price' => [
        'purchased_for' => '💳 Acquistato per',
        'price' => '💰 Prezzo',
        'floor' => '📊 Floor',
        'highest_bid' => '🏆 Offerta Più Alta',
    ],

    // Prenotazioni
    'reservation' => [
        'count' => 'Prenotazioni',
        'highest_bidder' => 'Miglior Offerente',
        'by' => 'da',
        'highest_bid' => 'Offerta Più Alta',
        'fegi_reservation' => 'Prenotazione FEGI',
        'strong_bidder' => 'Miglior Offerente',
        'weak_bidder' => 'Codice FEGI',
        'activator' => 'Attivatore',
        'activated_by' => 'Attivato da',
    ],

    // Nota valuta originale
    'originally_reserved_in' => 'Originalmente prenotato in :currency per :amount',
    'originally_reserved_in_short' => 'Pren. :currency :amount',

    // Stati
    'status' => [
        'not_for_sale' => '🚫 Non in vendita',
        'draft' => '⏳ Bozza',
    ],

    // Azioni
    'actions' => [
        'view' => 'Visualizza',
        'view_details' => 'Visualizza dettagli EGI',
        'reserve' => 'Attivalo',
        'reserved' => 'Prenotato',
        'outbid' => 'Rilancia per attivare',
        'view_history' => 'Cronologia',
        'reserve_egi' => 'Prenota :title',
    ],

    // Sistema cronologia prenotazioni
    'history' => [
        'title' => 'Cronologia Prenotazioni',
        'no_reservations' => 'Nessuna prenotazione trovata',
        'total_reservations' => '{1} :count prenotazione|[2,*] :count prenotazioni',
        'current_highest' => 'Priorità massima attuale',
        'superseded' => 'Priorità inferiore',
        'created_at' => 'Creato il',
        'amount' => 'Importo',
        'type_strong' => 'Prenotazione forte',
        'type_weak' => 'Prenotazione debole',
        'loading' => 'Caricamento cronologia...',
        'error' => 'Errore nel caricamento della cronologia',
    ],

    // Sezioni Informative
    'properties' => 'Proprietà',
    'supports_epp' => 'Supporta EPP',
    'asset_type' => 'Tipo Asset',
    'format' => 'Formato',
    'about_this_piece' => 'Su Quest\'Opera',
    'default_description' => 'Questa opera digitale unica rappresenta un momento di espressione creativa, catturando l\'essenza dell\'arte digitale nell\'era blockchain.',
    'provenance' => 'Provenienza',
    'view_full_collection' => 'Visualizza Collezione Completa',

    /*
    |--------------------------------------------------------------------------
    | CRUD System - Sistema di Modifica
    |--------------------------------------------------------------------------
    */

    'crud' => [

        // Header e Navigation
        'edit_egi' => 'Modifica EGI',
        'toggle_edit_mode' => 'Attiva/Disattiva Modalità Modifica',
        'start_editing' => 'Inizia Modifica',
        'save_changes' => 'Salva Modifiche',
        'cancel' => 'Annulla',

        // Campo Title
        'title' => 'Titolo',
        'title_placeholder' => 'Inserisci il titolo dell\'opera...',
        'title_hint' => 'Massimo 60 caratteri',
        'characters_remaining' => 'caratteri rimanenti',

        // Campo Description
        'description' => 'Descrizione',
        'description_placeholder' => 'Descrivi la tua opera, la sua storia e il suo significato...',
        'description_hint' => 'Racconta la storia dietro la tua creazione',

        // Campo Price
        'price' => 'Prezzo',
        'price_placeholder' => '0.00',
        'price_hint' => 'Prezzo in ALGO (lascia vuoto se non in vendita)',
        'price_locked_message' => 'Prezzo bloccato - EGI già prenotato',

        // Campo Creation Date
        'creation_date' => 'Data Creazione',
        'creation_date_hint' => 'Quando hai creato quest\'opera?',

        // Campo Published
        'is_published' => 'Pubblicato',
        'is_published_hint' => 'Rendi l\'opera visibile pubblicamente',

        // View Mode - Stato Attuale
        'current_title' => 'Titolo Attuale',
        'no_title' => 'Nessun titolo impostato',
        'current_price' => 'Prezzo Attuale',
        'price_not_set' => 'Prezzo non impostato',
        'current_status' => 'Stato Pubblicazione',
        'status_published' => 'Pubblicato',
        'status_draft' => 'Bozza',

        // Delete System
        'delete_egi' => 'Elimina EGI',
        'delete_confirmation_title' => 'Conferma Eliminazione',
        'delete_confirmation_message' => 'Sei sicuro di voler eliminare quest\'EGI? Questa azione non può essere annullata.',
        'delete_confirm' => 'Elimina Definitivamente',

        // Validation Messages
        'title_required' => 'Il titolo è obbligatorio',
        'title_max_length' => 'Il titolo non può superare i 60 caratteri',
        'price_numeric' => 'Il prezzo deve essere un numero valido',
        'price_min' => 'Il prezzo non può essere negativo',
        'creation_date_format' => 'Formato data non valido',

        // Success Messages
        'update_success' => 'EGI aggiornato con successo!',
        'delete_success' => 'EGI eliminato con successo.',

        // Error Messages
        'update_error' => 'Errore durante l\'aggiornamento dell\'EGI.',
        'delete_error' => 'Errore durante l\'eliminazione dell\'EGI.',
        'permission_denied' => 'Non hai i permessi necessari per questa azione.',
        'not_found' => 'EGI non trovato.',

        // General Messages
        'no_changes_detected' => 'Nessuna modifica rilevata.',
        'unsaved_changes_warning' => 'Hai modifiche non salvate. Sei sicuro di voler uscire?',
    ],

    /*
    |--------------------------------------------------------------------------
    | Responsive Labels - Mobile/Tablet
    |--------------------------------------------------------------------------
    */

    'mobile' => [
        'edit_egi_short' => 'Modifica',
        'save_short' => 'Salva',
        'delete_short' => 'Elimina',
        'cancel_short' => 'Annulla',
        'published_short' => 'Pubbl.',
        'draft_short' => 'Bozza',
    ],

    /*
    |--------------------------------------------------------------------------
    | EGI Carousel - Homepage Featured EGIs
    |--------------------------------------------------------------------------
    */

    'carousel' => [
        'two_columns' => 'Vista Lista',
        'three_columns' => 'Vista Card',
        'navigation' => [
            'previous' => 'Precedente',
            'next' => 'Successivo',
            'slide' => 'Vai alla diapositiva :number',
        ],
        'empty_state' => [
            'title' => 'Nessun Contenuto Disponibile',
            'subtitle' => 'Torna presto per nuovi contenuti!',
            'no_egis' => 'Nessuna opera EGI disponibile al momento.',
            'no_creators' => 'Nessun artista disponibile al momento.',
            'no_collections' => 'Nessuna collezione disponibile al momento.',
            'no_collectors' => 'Nessun collezionista disponibile al momento.'
        ],

        // Content Type Buttons
        'content_types' => [
            'egi_list' => 'Vista Elenco EGI',
            'egi_card' => 'Vista Scheda EGI',
            'creators' => 'Artisti in Evidenza',
            'collections' => 'Collezioni d\'Arte',
            'collectors' => 'Top Collezionisti'
        ],

        // View Mode Buttons
        'view_modes' => [
            'carousel' => 'Vista Carousel',
            'list' => 'Vista Elenco'
        ],

        // Mode Labels
        'carousel_mode' => 'Carousel',
        'list_mode' => 'Elenco',

        // Content Labels
        'creators' => 'Artisti',
        'collections' => 'Collezioni',
        'collectors' => 'Collezionisti',

        // Dynamic Headers
        'headers' => [
            'egi_list' => 'EGI',
            'egi_card' => 'EGI',
            'creators' => 'Artisti',
            'collections' => 'Collezioni',
            'collectors' => 'Attivatori'
        ],

        // Carousel sections
        'sections' => [
            'egis' => 'EGI in Evidenza',
            'creators' => 'Artisti Emergenti',
            'collections' => 'Collezioni Esclusive',
            'collectors' => 'Top Collezionisti'
        ],
        'view_all' => 'Vedi Tutti',
        'items' => 'elementi',

        // Title and subtitle for multi-content carousel
        'title' => 'Attiva un EGI!',
        'subtitle' => 'Attivare un opera significa unirsi ad essa ed essere riconosciuti per sempre come parte della sua storia.',
    ],

    /*
    |--------------------------------------------------------------------------
    | List View - Homepage List Mode
    |--------------------------------------------------------------------------
    */

    'list' => [
        'title' => 'Esplora per Categoria',
        'subtitle' => 'Naviga tra le diverse categorie per trovare quello che cerchi',

        'content_types' => [
            'egi_list' => 'Lista EGI',
            'creators' => 'Lista Artisti',
            'collections' => 'Lista Collezioni',
            'collectors' => 'Lista Collezionisti'
        ],

        'headers' => [
            'egi_list' => 'Opere EGI',
            'creators' => 'Artisti',
            'collections' => 'Collezioni',
            'collectors' => 'Collezionisti'
        ],

        'empty_state' => [
            'title' => 'Nessun Elemento Trovato',
            'subtitle' => 'Prova a selezionare una categoria diversa',
            'no_egis' => 'Nessuna opera EGI trovata.',
            'no_creators' => 'Nessun artista trovato.',
            'no_collections' => 'Nessuna collezione trovata.',
            'no_collectors' => 'Nessun collezionista trovato.'
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Desktop Carousel - Desktop Only EGI Carousel
    |--------------------------------------------------------------------------
    */

    'desktop_carousel' => [
        'title' => 'Opere Digitali in Evidenza',
        'subtitle' => 'Le migliori creazioni NFT della nostra community',
        'navigation' => [
            'previous' => 'Precedente',
            'next' => 'Successivo',
            'slide' => 'Vai alla diapositiva :number',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Mobile Toggle - Mobile View Toggle
    |--------------------------------------------------------------------------
    */

    'mobile_toggle' => [
        'title' => 'Esplora FlorenceEGI',
        'subtitle' => 'Scegli come vuoi navigare i contenuti',
        'carousel_mode' => 'Vista Carousel',
        'list_mode' => 'Vista Lista',
    ],

    /*
    |--------------------------------------------------------------------------
    | Hero Coverflow - Sezione Hero con Effetto Coverflow 3D
    |--------------------------------------------------------------------------
    */

    'hero_coverflow' => [
        'title' => 'Attivare un EGI è lasciare il segno.',
        'subtitle' => 'Il tuo nome rimane per sempre accanto a quello del Creator: senza di te l’opera non esisterebbe.',
        'carousel_mode' => 'Vista Carousel',
        'list_mode' => 'Vista Griglia',
        'carousel_label' => 'Carousel opere in evidenza',
        'no_egis' => 'Nessuna opera in evidenza disponibile al momento.',
        'navigation' => [
            'previous' => 'Opera precedente',
            'next' => 'Opera successiva',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Accessibility Labels - Screen Readers
    |--------------------------------------------------------------------------
    */

    'a11y' => [
        'edit_form' => 'Modulo di modifica EGI',
        'delete_button' => 'Pulsante elimina EGI',
        'toggle_edit' => 'Attiva modalità modifica',
        'save_form' => 'Salva modifiche EGI',
        'close_modal' => 'Chiudi finestra di conferma',
        'required_field' => 'Campo obbligatorio',
        'optional_field' => 'Campo opzionale',
    ],

    'collection' => [
        'part_of' => 'Parte di',
    ],

    // Collaboratori della Collection
    'collection_collaborators' => 'Collaboratori',
    'owner' => 'Proprietario',
    'creator' => 'Creatore',
    'no_other_collaborators' => 'Nessun altro collaboratore',

];
