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
        'activator' => 'Co Creatore',
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
    // 'creator' => 'Creatore',
    'no_other_collaborators' => 'Nessun altro collaboratore',

    /*
    |--------------------------------------------------------------------------
    | Certificato di Autenticità (CoA)
    |--------------------------------------------------------------------------
    */

    'coa' => [
        'none' => 'Nessun Certificato di Autenticità',
        'title' => 'Certificato di Autenticità',
        'status' => 'Stato',
        'issued' => 'Emesso il',
        'verification' => 'ID Verifica',
        'copy' => 'Copia',
        'copied' => 'Copiato!',
        'view' => 'Visualizza',
        'pdf' => 'PDF',
        'reissue' => 'Riemetti',
        'issue' => 'Emetti Certificato',
        'annexes' => 'Allegati',
        'add_annex' => 'Aggiungi Allegato',
        'annex_coming_soon' => 'Gestione allegati disponibile a breve!',
        'pro' => 'Pro',
        'unlock_pro' => 'Sblocca con CoA Pro',
        'provenance' => 'Documentazione Provenienza',
        'pdf_bundle' => 'Bundle PDF Professionale',
        'issue_description' => 'Emetti un certificato per fornire prova di autenticità e sbloccare le funzionalità Pro',
        'creator_only' => 'Solo il creatore può emettere certificati',
        'active' => 'Attivo',
        'revoked' => 'Revocato',
        'expired' => 'Scaduto',
        'manage_coa' => 'Gestisci CoA',
        'no_certificate' => 'Nessun certificato ancora emesso',

        // Messaggi JavaScript
        'confirm_issue' => 'Emettere un Certificato di Autenticità per questo EGI?',
        'issued_success' => 'Certificato emesso con successo!',
        'confirm_reissue' => 'Riemettere questo certificato? Questo creerà una nuova versione.',
        'reissued_success' => 'Certificato riemesso con successo!',
        'reissue_certificate_confirm' => 'Sei sicuro di voler riemettere questo certificato?',
        'certificate_reissued_successfully' => 'Certificato riemesso con successo!',
        'error_reissuing_certificate' => 'Errore durante la riemissione del certificato',
        'revocation_reason' => 'Motivo della revoca:',
        'confirm_revoke' => 'Revocare questo certificato? Questa azione non può essere annullata.',
        'revoked_success' => 'Certificato revocato con successo!',
        'error_issuing' => 'Errore durante l\'emissione del certificato',
        'error_reissuing' => 'Errore durante la riemissione del certificato',
        'error_revoking' => 'Errore durante la revoca del certificato',
        'unknown_error' => 'Errore sconosciuto',
        'verify_any_certificate' => 'Verifica qualsiasi certificato',

        // Modal Allegati
        'manage_annexes_title' => 'Gestisci Allegati CoA Pro',
        'annexes_description' => 'Aggiungi documentazione professionale per migliorare il tuo certificato',
        'provenance_tab' => 'Provenienza',
        'condition_tab' => 'Condizioni',
        'exhibitions_tab' => 'Mostre',
        'photos_tab' => 'Foto',
        'provenance_title' => 'Documentazione Provenienza',
        'provenance_description' => 'Documenta la storia di proprietà e la catena di autenticità',
        'condition_title' => 'Rapporto sulle Condizioni',
        'condition_description' => 'Valutazione professionale delle condizioni fisiche dell\'opera',
        'exhibitions_title' => 'Storia delle Esposizioni',
        'exhibitions_description' => 'Registro delle mostre pubbliche e storia espositiva',
        'photos_title' => 'Fotografia Professionale',
        'photos_description' => 'Documentazione ad alta risoluzione e fotografia di dettaglio',
        'save_annex' => 'Salva Allegato',
        'cancel' => 'Annulla',
        'upload_files' => 'Carica File',
        'drag_drop_files' => 'Trascina e rilascia i file qui, o clicca per selezionare',
        'max_file_size' => 'Dimensione massima file: 10MB per file',
        'supported_formats' => 'Formati supportati: PDF, JPG, PNG, DOCX',

        // Form Provenienza
        'ownership_history_description' => 'Documenta la storia di proprietà e la catena di autenticità di quest\'opera',
        'previous_owners' => 'Proprietari Precedenti',
        'previous_owners_placeholder' => 'Elenca i proprietari precedenti e le date di possesso...',
        'acquisition_details' => 'Dettagli Acquisizione',
        'acquisition_details_placeholder' => 'Come è stata acquisita quest\'opera? Includi date, prezzi, case d\'asta...',
        'authenticity_sources' => 'Fonti di Autenticità',
        'authenticity_sources_placeholder' => 'Pareri di esperti, catalogue raisonnés, archivi istituzionali...',
        'save_provenance_data' => 'Salva Dati Provenienza',

        // Form Condizioni
        'condition_assessment_description' => 'Valutazione professionale dello stato fisico dell\'opera e delle necessità conservative',
        'overall_condition' => 'Condizioni Generali',
        'condition_excellent' => 'Eccellenti',
        'condition_very_good' => 'Molto Buone',
        'condition_good' => 'Buone',
        'condition_fair' => 'Discrete',
        'condition_poor' => 'Scarse',
        'condition_notes' => 'Note sulle Condizioni',
        'condition_notes_placeholder' => 'Descrizione dettagliata di eventuali danni, restauri o problemi conservativi...',
        'conservation_history' => 'Storia Conservativa',
        'conservation_history_placeholder' => 'Restauri precedenti, trattamenti o interventi conservativi...',
        'save_condition_data' => 'Salva Dati Condizioni',

        // Form Mostre
        'exhibition_history_description' => 'Registro di musei, gallerie ed esposizioni pubbliche dove quest\'opera è stata esposta',
        'exhibition_title' => 'Titolo Mostra',
        'exhibition_title_placeholder' => 'Nome della mostra...',
        'venue' => 'Sede',
        'venue_placeholder' => 'Nome del museo, galleria o istituzione...',
        'exhibition_dates' => 'Date Mostra',
        'exhibition_notes' => 'Note',
        'exhibition_notes_placeholder' => 'Numero di catalogo, menzioni speciali, recensioni...',
        'add_exhibition' => 'Aggiungi Mostra',
        'save_exhibitions_data' => 'Salva Dati Mostre',

        // Form Foto
        'photo_documentation_description' => 'Immagini di alta qualità per documentazione e scopi archivistici',
        'photo_type' => 'Tipo Foto',
        'photo_overall' => 'Vista Generale',
        'photo_detail' => 'Dettaglio',
        'photo_raking' => 'Luce Radente',
        'photo_uv' => 'Fotografia UV',
        'photo_infrared' => 'Infrarosso',
        'photo_back' => 'Retro/Verso',
        'photo_signature' => 'Firma/Marchi',
        'photo_frame' => 'Cornice/Montaggio',
        'photo_description' => 'Descrizione',
        'photo_description_placeholder' => 'Descrivi cosa mostra questa foto...',
        'save_photos_data' => 'Salva Dati Foto',

        // Campi aggiuntivi form condizioni
        'select_condition' => 'Seleziona condizione...',
        'detailed_assessment' => 'Valutazione Dettagliata',
        'detailed_assessment_placeholder' => 'Descrizione dettagliata delle condizioni, inclusi eventuali danni, restauri o caratteristiche particolari...',
        'conservation_history_placeholder' => 'Trattamenti conservativi precedenti, date e conservatori...',
        'assessor_information' => 'Informazioni Valutatore',
        'assessor_placeholder' => 'Nome e credenziali del valutatore delle condizioni...',
        'save_condition_report' => 'Salva Rapporto Condizioni',

        // Campi form mostre
        'major_exhibitions' => 'Mostre Principali',
        'major_exhibitions_placeholder' => 'Elenca mostre principali, musei, gallerie, date...',
        'publications_catalogues' => 'Pubblicazioni e Cataloghi',
        'publications_placeholder' => 'Libri, cataloghi, articoli in cui quest\'opera è stata pubblicata...',
        'awards_recognition' => 'Premi e Riconoscimenti',
        'awards_placeholder' => 'Premi, riconoscimenti, critica ricevuta...',
        'save_exhibition_history' => 'Salva Storia Mostre',
        'exhibition_history_description' => 'Registro delle mostre dove quest\'opera è stata esposta',

        // Campi form foto
        'click_upload_images' => 'Clicca per caricare immagini',
        'png_jpg_webp' => 'PNG, JPG, WEBP fino a 10MB ciascuna',
        'photo_descriptions' => 'Descrizioni Foto',
        'photo_descriptions_placeholder' => 'Descrivi le immagini: condizioni di illuminazione, dettagli catturati, scopo...',
        'photographer_credits' => 'Crediti Fotografo',
        'photographer_placeholder' => 'Nome fotografo e data...',
        'save_photo_documentation' => 'Salva Documentazione Fotografica',
        'photo_documentation_description' => 'Immagini ad alta risoluzione per documentazione e scopi assicurativi',

        // Azioni modal
        'close' => 'Chiudi',
        'error_no_certificate' => 'Errore: Nessun certificato selezionato',
        'saving' => 'Salvataggio...',
        'annex_saved_success' => 'Dati allegato salvati con successo!',
        'error_saving_annex' => 'Errore nel salvare i dati dell\'allegato',

        // Traduzioni mancanti per sidebar e componenti CoA
        'certificate' => 'Certificato CoA',
        'no_certificate' => 'Nessun Certificato',
        'certificate_active' => 'Certificato Attivo',
        'serial_number' => 'Numero Seriale',
        'issue_date' => 'Data Emissione',
        'expires' => 'Scade',
        'no_certificate_issued' => 'Questo EGI non ha un Certificato di Autenticità',
        'issue_certificate' => 'Emetti Certificato',
        'unlock_with_coa_pro' => 'Sblocca con CoA Pro',
        'provenance_documentation' => 'Documentazione Provenienza',
        'condition_reports' => 'Report Stato Conservazione',
        'exhibition_history' => 'Storia Mostre',
        'professional_pdf' => 'PDF Professionale',
        'only_creator_can_issue' => 'Solo il creatore può emettere certificati',

        // Sistema Traits Vocabolario CoA
        'traits_management_title' => 'Gestione Traits CoA',
        'traits_management_description' => 'Configura le caratteristiche tecniche dell\'opera per il Certificato di Autenticità',
        'status_configured' => 'Configurato',
        'status_not_configured' => 'Non Configurato',
        'edit_traits' => 'Modifica Traits',
        'no_technique_selected' => 'Nessuna tecnica selezionata',
        'no_materials_selected' => 'Nessun materiale selezionato',
        'no_support_selected' => 'Nessun supporto selezionato',
        'custom' => 'personalizzato',
        'last_updated' => 'Ultimo aggiornamento',
        'never_configured' => 'Mai configurato',
        'clear_all' => 'Cancella Tutto',
        'saved' => 'Salvato',

        // Modal Vocabolario
        'modal_title' => 'Seleziona Traits CoA',
        'category_technique' => 'Tecnica',
        'category_materials' => 'Materiali',
        'category_support' => 'Supporto',
        'search_placeholder' => 'Cerca termini...',
        'loading' => 'Caricamento...',
        'selected_items' => 'Elementi Selezionati',
        'no_items_selected' => 'Nessun elemento selezionato',
        'add_custom' => 'Aggiungi Personalizzato',
        'custom_term_placeholder' => 'Inserisci termine personalizzato (max 60 caratteri)',
        'add' => 'Aggiungi',
        'cancel' => 'Annulla',
        'items_selected' => 'elementi selezionati',
        'confirm' => 'Conferma',

        // Componenti Vocabolario
        'terms_available' => 'termini disponibili',
        'no_categories_available' => 'Nessuna categoria disponibile',
        'no_categories_found' => 'Non sono state trovate categorie di vocabolario.',
        'search_results' => 'Risultati ricerca',
        'results_for' => 'Per',
        'terms_found' => 'termini trovati',
        'results_found' => 'risultati trovati',
        'no_results_found' => 'Nessun risultato trovato',
        'no_terms_match_search' => 'Nessun termine corrisponde alla ricerca',
        'in_category' => 'nella categoria',
        'clear_search' => 'Cancella ricerca',
        'no_terms_available' => 'Nessun termine disponibile',
        'no_terms_found_category' => 'Non sono stati trovati termini per la categoria',
        'categories' => 'Categorie',
        'back_to_start' => 'Torna all\'inizio',
        'retry' => 'Riprova',
        'error' => 'Errore',
        'unexpected_error' => 'Si è verificato un errore imprevisto.',
        'exhibition_history' => 'Storia Esposizioni',
        'professional_pdf_bundle' => 'Bundle PDF Professionale',
        'only_creator_can_issue' => 'Solo il creatore può emettere certificati',
        'public_verification' => 'Verifica Pubblica',
        'verification_description' => 'Verifica l\'autenticità di un Certificato di Autenticità EGI',
        'verification_instructions' => 'Inserisci il numero seriale del certificato per verificarne l\'autenticità',
        'enter_serial' => 'Inserisci numero seriale',
        'serial_help' => 'Formato: ABC-123-DEF (lettere, numeri e trattini)',
        'certificate_of_authenticity' => 'Certificato di Autenticità',
        'public_verification_display' => 'Visualizzazione Pubblica di Verifica',
        'verified_authentic' => 'Certificato Verificato e Autentico',
        'verified_at' => 'Verificato il',
        'artwork_information' => 'Informazioni Opera',
        'artwork_title' => 'Titolo Opera',
        'creator' => 'Creatore',
        'description' => 'Descrizione',
        'certificate_details' => 'Dettagli Certificato',
        'cryptographic_verification' => 'Verifica Crittografica',
        'verify_again' => 'Verifica di Nuovo',
        'print_certificate' => 'Stampa Certificato',
        'share_verification' => 'Condividi Verifica',
        'powered_by_florenceegi' => 'Powered by FlorenceEGI',
        'verification_timestamp' => 'Timestamp di Verifica',
        'link_copied' => 'Link copiato negli appunti',
        'issuing' => 'Emettendo...',
        'certificate_issued_successfully' => 'Certificato emesso con successo!',
        'error_issuing_certificate' => 'Errore nell\'emissione del certificato: ',
        'reissue_certificate_confirm' => 'Riemettere questo certificato? Verrà creata una nuova versione.',
        'certificate_reissued_successfully' => 'Certificato riemesso con successo!',
        'error_reissuing_certificate' => 'Errore nella riemissione del certificato: ',
        'revoke_certificate_confirm' => 'Revocare questo certificato? Questa azione non può essere annullata.',
        'reason_for_revocation' => 'Motivo della revoca:',
        'certificate_revoked_successfully' => 'Certificato revocato con successo!',
        'error_revoking_certificate' => 'Errore nella revoca del certificato: ',
        'manage_certificate' => 'Gestisci Certificato',
        'annex_management_coming_soon' => 'Gestione allegati in arrivo!',
        'issue_certificate_description' => 'Emetti un certificato per fornire prova di autenticità e sbloccare le funzioni Pro',
        'serial' => 'Seriale',
        'pro_features' => 'Funzioni Pro',
        'provenance_docs' => 'Documentazione Provenienza',
        'professional_pdf' => 'PDF Professionale',
        'unlock_pro_features' => 'Sblocca Funzioni Pro',
        'reason_for' => 'Motivo per',
    ],

    /*
    |--------------------------------------------------------------------------
    | Dossier System - Sistema Dossier
    |--------------------------------------------------------------------------
    */
    'dossier' => [
        'title' => 'Dossier Immagini',
        'loading' => 'Caricamento dossier...',
        'view_complete' => 'Visualizza dossier immagini completo',
        'close' => 'Chiudi dossier',

        // Artwork Info
        'artwork_info' => 'Informazioni Opera',
        'author' => 'Autore',
        'year' => 'Anno',
        'internal_id' => 'ID Interno',

        // Dossier Info  
        'dossier_info' => 'Informazioni Dossier',
        'images_count' => 'Immagini',
        'type' => 'Tipo',
        'utility_gallery' => 'Utility Gallery',

        // Gallery
        'gallery_title' => 'Galleria Immagini',
        'image_number' => 'Immagine :number',
        'image_of_total' => 'Immagine :current di :total',

        // States
        'no_utility_title' => 'Dossier non disponibile',
        'no_utility_message' => 'Non sono disponibili immagini aggiuntive per questa opera.',
        'no_utility_description' => 'Il dossier delle immagini aggiuntive non è stato ancora configurato per questo artwork.',

        'no_images_title' => 'Nessuna immagine disponibile',
        'no_images_message' => 'Il dossier esiste ma non contiene ancora immagini.',
        'no_images_description' => 'Le immagini aggiuntive saranno aggiunte in futuro dal creatore dell\'opera.',

        'error_title' => 'Errore',
        'error_loading' => 'Errore nel caricamento del dossier',

        // Navigation
        'previous_image' => 'Immagine precedente',
        'next_image' => 'Immagine successiva',
        'close_viewer' => 'Chiudi visualizzatore',
        'of' => 'di',

        // Zoom Controls
        'zoom_help' => 'Usa rotellina del mouse o touch per zoom • Trascina per muovere',
        'zoom_in' => 'Ingrandisci',
        'zoom_out' => 'Riduci',
        'zoom_reset' => 'Reimposta zoom',
        'zoom_fit' => 'Adatta allo schermo',
    ],

];
