<?php

return [

    /*
    |--------------------------------------------------------------------------
    | EGI (Ecological Goods Invent) - Traduzioni Italiane
    |--------------------------------------------------------------------------
    |
    | Traduzioni per il sistema CRUD EGI in FlorenceEGI
    | Versione: 1.0.0 - Conforme al sistema Oracode 2.0
    |
    */

    // Meta e SEO
    'meta_description_default' => 'Dettagli per EGI: :title',
    'image_alt_default' => 'Immagine EGI',
    'view_full' => 'Visualizzazione Completa',
    'artwork_loading' => 'Caricamento Opera...',

    // Informazioni di Base
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
    'contact_owner_availability' => 'Contatta il proprietario per la disponibilità',
    'not_for_sale' => 'Non in Vendita',
    'not_for_sale_description' => 'Questo EGI non è attualmente disponibile per l\'acquisto',
    'liked' => 'Apprezzato',
    'add_to_favorites' => 'Aggiungi ai Preferiti',
    'reserve_this_piece' => 'Attivalo',

    /*
    |--------------------------------------------------------------------------
    | Sistema di Carte NFT - Sistema di Carte NFT
    |--------------------------------------------------------------------------
    */

    // Badge e Stati
    'badge' => [
        'owned' => 'POSSEDUTO',
        'media_content' => 'Contenuto Multimediale',
        'winning_bid' => 'OFFERTA VINCENTE',
        'outbid' => 'SUPERATO',
        'not_owned' => 'NON POSSEDUTO',
        'to_activate' => 'DA ATTIVARE',
        'activated' => 'ATTIVATO',
        'reserved' => 'PRENOTATO',
        'minted' => 'MINTATO',
    ],

    // Titoli
    'title' => [
        'untitled' => '✨ EGI Senza Titolo',
    ],

    // Piattaforma
    'platform' => [
        'powered_by' => 'Offerto da :platform',
    ],

    // Creatore
    'creator' => [
        'created_by' => '👨‍🎨 Creato da:',
        'co_creator' => '🤝 Co-Creatore:',
    ],

    // Prezzi
    'price' => [
        'purchased_for' => '💳 Acquistato per',
        'price' => '💰 Prezzo',
        'floor' => '📊 Base',
        'highest_bid' => '🏆 Offerta Più Alta',
    ],

    // Prenotazioni
    'reservation' => [
        'count' => 'Prenotazioni',
        'highest_bidder' => 'Miglior Offerente',
        'by' => 'di',
        'highest_bid' => 'Offerta Più Alta',
        'fegi_reservation' => 'Prenotazione FEGI',
        'strong_bidder' => 'Miglior Offerente',
        'weak_bidder' => 'Codice FEGI',
        'activator' => 'Co-Creatore',
        'activated_by' => 'Attivato da',
        'reserved_by' => '📝 Prenotato da:',
    ],

    // Nota sulla Valuta Originale
    'originally_reserved_in' => 'Originariamente prenotato in :currency per :amount',
    'originally_reserved_in_short' => 'Pren. :currency :amount',

    // Stato
    'status' => [
        'not_for_sale' => '🚫 Non in Vendita',
        'draft' => '⏳ Bozza',
        // Phase 2: Availability status
        'login_required' => '🔐 Login Richiesto',
        'already_minted' => '✅ Già Mintato',
        'not_available' => '⚠️ Non Disponibile',
        'view_mint_details' => 'Visualizza Dettagli Mint',
    ],

    // Azioni
    'actions' => [
        'view' => 'Visualizza',
        'view_details' => 'Visualizza Dettagli EGI',
        'reserve' => 'Attivalo',
        'reserved' => 'Prenotato',
        'outbid' => 'Supera per Attivare',
        'view_history' => 'Cronologia',
        'reserve_egi' => 'Prenota :title',
        // Phase 2: Dual path actions
        'mint_now' => 'Minta Ora',
        'mint_direct' => 'Minta Subito',
    ],

    // Sistema di Cronologia delle Prenotazioni
    'history' => [
        'title' => 'Cronologia delle Prenotazioni',
        'no_reservations' => 'Nessuna prenotazione trovata',
        'total_reservations' => '{1} :count prenotazione|[2,*] :count prenotazioni',
        'current_highest' => 'Priorità Più Alta Attuale',
        'superseded' => 'Priorità Inferiore',
        'created_at' => 'Creato il',
        'amount' => 'Importo',
        'type_strong' => 'Prenotazione Forte',
        'type_weak' => 'Prenotazione Debole',
        'loading' => 'Caricamento cronologia...',
        'error' => 'Errore nel caricamento della cronologia',
    ],

    // Sezioni Informative
    'properties' => 'Proprietà',
    'supports_epp' => 'Supporta EPP',
    'asset_type' => 'Tipo di Risorsa',
    'format' => 'Formato',
    'about_this_piece' => 'Informazioni su Quest\'Opera',
    'default_description' => 'Quest\'opera digitale unica rappresenta un momento di espressione creativa, catturando l\'essenza dell\'arte digitale nell\'era della blockchain.',
    'provenance' => 'Provenienza',
    'view_full_collection' => 'Visualizza Collezione Completa',

    /*
    |--------------------------------------------------------------------------
    | Sistema CRUD - Sistema di Modifica
    |--------------------------------------------------------------------------
    */

    'crud' => [

        // Intestazione e Navigazione
        'edit_egi' => 'Modifica EGI',
        'toggle_edit_mode' => 'Attiva/Disattiva Modalità Modifica',
        'start_editing' => 'Inizia Modifica',
        'save_changes' => 'Salva Modifiche',
        'cancel' => 'Annulla',

        // Campo Titolo
        'title' => 'Titolo',
        'title_placeholder' => 'Inserisci il titolo dell\'opera...',
        'title_hint' => 'Massimo 60 caratteri',
        'characters_remaining' => 'caratteri rimanenti',

        // Campo Descrizione
        'description' => 'Descrizione',
        'description_placeholder' => 'Descrivi la tua opera, la sua storia e il suo significato...',
        'description_hint' => 'Racconta la storia dietro la tua creazione',

        // Campo Prezzo
        'price' => 'Prezzo',
        'price_placeholder' => '0.00',
        'price_hint' => 'Prezzo in ALGO (lascia vuoto se non in vendita)',
        'price_locked_message' => 'Prezzo bloccato - EGI già prenotato',

        // Campo Data di Creazione
        'creation_date' => 'Data di Creazione',
        'creation_date_hint' => 'Quando hai creato quest\'opera?',

        // Campo Pubblicato
        'is_published' => 'Pubblicato',
        'is_published_hint' => 'Rendi l\'opera visibile pubblicamente',

        // Modalità Visualizzazione - Stato Attuale
        'current_title' => 'Titolo Attuale',
        'no_title' => 'Nessun titolo impostato',
        'current_price' => 'Prezzo Attuale',
        'price_not_set' => 'Prezzo non impostato',
        'current_status' => 'Stato di Pubblicazione',
        'status_published' => 'Pubblicato',
        'status_draft' => 'Bozza',

        // Sistema di Eliminazione
        'delete_egi' => 'Elimina EGI',
        'delete_confirmation_title' => 'Conferma Eliminazione',
        'delete_confirmation_message' => 'Sei sicuro di voler eliminare questo EGI? Questa azione non può essere annullata.',
        'delete_confirm' => 'Elimina Permanentemente',

        // Messaggi di Validazione
        'title_required' => 'Il titolo è obbligatorio',
        'title_max_length' => 'Il titolo non può superare i 60 caratteri',
        'price_numeric' => 'Il prezzo deve essere un numero valido',
        'price_min' => 'Il prezzo non può essere negativo',
        'creation_date_format' => 'Formato data non valido',

        // Messaggi di Successo
        'update_success' => 'EGI aggiornato con successo!',
        'delete_success' => 'EGI eliminato con successo.',

        // Messaggi di Errore
        'update_error' => 'Errore durante l\'aggiornamento dell\'EGI.',
        'delete_error' => 'Errore durante l\'eliminazione dell\'EGI.',
        'permission_denied' => 'Non hai i permessi necessari per questa azione.',
        'not_found' => 'EGI non trovato.',

        // Messaggi Generali
        'no_changes_detected' => 'Nessuna modifica rilevata.',
        'unsaved_changes_warning' => 'Hai modifiche non salvate. Sei sicuro di voler uscire?',

        // Blockchain Immutability Messages
        'blockchain_warning_title' => 'EGI Certificato su Blockchain',
        'blockchain_warning_message' => 'Questo EGI è stato mintato (ASA #:asa_id). Puoi modificare solo il prezzo per dinamiche di mercato. Titolo, descrizione e metadati sono immutabili.',
        'blockchain_verify_link' => 'Verifica su Blockchain',
        'field_immutable_hint' => 'Immutabile (certificato blockchain)',

        // Vendita/Asta
        'sale_mode' => 'Modalità di Vendita',
        'sale_mode_hint' => 'Scegli come vuoi vendere questo EGI',
        'sale_mode_not_for_sale' => 'Non in Vendita',
        'sale_mode_fixed_price' => 'Prezzo Fisso',
        'sale_mode_auction' => 'Asta',
        'auction_section_title' => 'Configurazione Asta',
        'auction_minimum_price' => 'Prezzo Minimo',
        'auction_minimum_price_hint' => 'Prezzo di partenza dell’asta in EUR',
        'auction_start' => 'Inizio Asta',
        'auction_start_hint' => 'Data e ora di avvio',
        'auction_end' => 'Fine Asta',
        'auction_end_hint' => 'Data e ora di chiusura',
        'auto_mint_highest' => 'Auto-mint al miglior offerente',
        'auto_mint_highest_hint' => 'Alla chiusura dell’asta, minta automaticamente all’offerente vincente'
    ],

    // Validazione (usata da EgiController)
    'validation' => [
        'title_required' => 'Il titolo è obbligatorio',
        'title_max' => 'Il titolo non può superare i 60 caratteri',
        'description_max' => 'La descrizione è troppo lunga',
        'price_numeric' => 'Il prezzo deve essere un numero valido',
        'price_min' => 'Il prezzo non può essere negativo',
        'creation_date_invalid' => 'La data di creazione non è valida',

        'sale_mode_invalid' => 'Modalità di vendita non valida',
        'auction_minimum_price_required' => 'Il prezzo minimo è obbligatorio per l’asta',
        'auction_minimum_price_numeric' => 'Il prezzo minimo deve essere un numero',
        'auction_minimum_price_min' => 'Il prezzo minimo deve essere maggiore di zero',
        'auction_start_required' => 'La data di inizio asta è obbligatoria',
        'auction_start_date' => 'La data di inizio asta non è valida',
        'auction_end_required' => 'La data di fine asta è obbligatoria',
        'auction_end_date' => 'La data di fine asta non è valida',
        'auction_end_after_start' => 'La fine dell’asta deve essere successiva all’inizio',
    ],

    /*
    |--------------------------------------------------------------------------
    | Etichette Responsive - Mobile/Tablet
    |--------------------------------------------------------------------------
    */

    'mobile' => [
        'edit_egi_short' => 'Modifica',
        'save_short' => 'Salva',
        'delete_short' => 'Elimina',
        'cancel_short' => 'Annulla',
        'published_short' => 'Pub.',
        'draft_short' => 'Bozza',
    ],

    /*
    |--------------------------------------------------------------------------
    | Carosello EGI - EGI in Evidenza sulla Homepage
    |--------------------------------------------------------------------------
    */

    'carousel' => [
        'two_columns' => 'Vista Elenco',
        'three_columns' => 'Vista Scheda',
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

        // Pulsanti Tipo di Contenuto
        'content_types' => [
            'egi_list' => 'Vista Elenco EGI',
            'egi_card' => 'Vista Scheda EGI',
            'creators' => 'Artisti in Evidenza',
            'collections' => 'Collezioni d\'Arte',
            'collectors' => 'Collezionisti Top'
        ],

        // Pulsanti Modalità di Visualizzazione
        'view_modes' => [
            'carousel' => 'Vista Carosello',
            'list' => 'Vista Elenco'
        ],

        // Etichette Modalità
        'carousel_mode' => 'Carosello',
        'list_mode' => 'Elenco',

        // Etichette Contenuto
        'creators' => 'Artisti',
        'collections' => 'Collezioni',
        'collectors' => 'Collezionisti',

        // Intestazioni Dinamiche
        'headers' => [
            'egi_list' => 'EGI',
            'egi_card' => 'EGI',
            'creators' => 'Artisti',
            'collections' => 'Collezioni',
            'collectors' => 'Attivatori'
        ],

        // Sezioni Carosello
        'sections' => [
            'egis' => 'EGI in Evidenza',
            'creators' => 'Artisti Emergenti',
            'collections' => 'Collezioni Esclusive',
            'collectors' => 'Collezionisti Top'
        ],
        'view_all' => 'Visualizza Tutto',
        'items' => 'elementi',

        // Titolo e Sottotitolo per Carosello Multi-Contenuto
        'title' => 'Attiva un EGI!',
        'subtitle' => 'Attivare un\'opera significa unirti ad essa ed essere riconosciuto per sempre come parte della sua storia.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Vista Elenco - Modalità Elenco Homepage
    |--------------------------------------------------------------------------
    */

    'list' => [
        'title' => 'Esplora per Categoria',
        'subtitle' => 'Sfoglia diverse categorie per trovare ciò che cerchi',

        'content_types' => [
            'egi_list' => 'Elenco EGI',
            'creators' => 'Elenco Artisti',
            'collections' => 'Elenco Collezioni',
            'collectors' => 'Elenco Collezionisti'
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
    | Carosello Desktop - Carosello EGI Solo Desktop
    |--------------------------------------------------------------------------
    */

    'desktop_carousel' => [
        'title' => 'Opere Digitali in Evidenza',
        'subtitle' => 'Le migliori creazioni EGI dalla nostra comunità',
        'navigation' => [
            'previous' => 'Precedente',
            'next' => 'Successivo',
            'slide' => 'Vai alla diapositiva :number',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Toggle Mobile - Toggle Vista Mobile
    |--------------------------------------------------------------------------
    */

    'mobile_toggle' => [
        'title' => 'Esplora FlorenceEGI',
        'subtitle' => 'Scegli come vuoi sfogliare i contenuti',
        'carousel_mode' => 'Vista Carosello',
        'list_mode' => 'Vista Elenco',
    ],

    /*
    |--------------------------------------------------------------------------
    | Hero Coverflow - Sezione Hero con Effetto Coverflow 3D
    |--------------------------------------------------------------------------
    */

    'hero_coverflow' => [
        'title' => 'Attivare un EGI significa lasciare un segno.',
        'subtitle' => 'Il tuo nome rimane per sempre accanto a quello del Creatore: senza di te, l\'opera non esisterebbe.',
        'carousel_mode' => 'Vista Carosello',
        'list_mode' => 'Vista Griglia',
        'carousel_label' => 'Carosello opere in evidenza',
        'no_egis' => 'Nessuna opera in evidenza disponibile al momento.',
        'navigation' => [
            'previous' => 'Opera Precedente',
            'next' => 'Opera Successiva',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Etichette di Accessibilità - Lettori di Schermo
    |--------------------------------------------------------------------------
    */

    'a11y' => [
        'edit_form' => 'Modulo di Modifica EGI',
        'delete_button' => 'Pulsante Elimina EGI',
        'toggle_edit' => 'Attiva/Disattiva Modalità Modifica',
        'save_form' => 'Salva Modifiche EGI',
        'close_modal' => 'Chiudi Finestra di Conferma',
        'required_field' => 'Campo Obbligatorio',
        'optional_field' => 'Campo Opzionale',
    ],

    'collection' => [
        'part_of' => 'Parte di',
    ],

    // Collaboratori della Collezione
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
        'verification' => 'ID di Verifica',
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
        'provenance' => 'Documentazione di Provenienza',
        'pdf_bundle' => 'Pacchetto PDF Professionale',
        'issue_description' => 'Emetti un certificato per fornire prova di autenticità e sbloccare funzionalità Pro',
        'creator_only' => 'Solo il creatore può emettere certificati',
        'active' => 'Attivo',
        'revoked' => 'Revocato',
        'expired' => 'Scaduto',
        'manage_coa' => 'Gestisci CoA',
        'no_certificate' => 'Nessun certificato emesso finora',

        // Messaggi JavaScript
        'confirm_issue' => 'Emettere un Certificato di Autenticità per questo EGI?',
        'issued_success' => 'Certificato emesso con successo!',
        'confirm_reissue' => 'Riemettere questo certificato? Verrà creata una nuova versione.',
        'reissued_success' => 'Certificato riemesso con successo!',
        'reissue_certificate_confirm' => 'Sei sicuro di voler riemettere questo certificato?',
        'certificate_reissued_successfully' => 'Certificato riemesso con successo!',
        'error_reissuing_certificate' => 'Errore durante la riemissione del certificato',
        'revocation_reason' => 'Motivo della Revoca:',
        'confirm_revoke' => 'Revocare questo certificato? Questa azione non può essere annullata.',
        'revoked_success' => 'Certificato revocato con successo!',
        'error_issuing' => 'Errore durante l\'emissione del certificato',
        'error_reissuing' => 'Errore durante la riemissione del certificato',
        'error_revoking' => 'Errore durante la revoca del certificato',
        'unknown_error' => 'Errore Sconosciuto',
        'verify_any_certificate' => 'Verifica Qualsiasi Certificato',

        // Modale Allegati
        'manage_annexes_title' => 'Gestisci Allegati CoA Pro',
        'annexes_description' => 'Aggiungi documentazione professionale per arricchire il tuo certificato',
        'provenance_tab' => 'Provenienza',
        'condition_tab' => 'Condizione',
        'exhibitions_tab' => 'Esposizioni',
        'photos_tab' => 'Foto',
        'provenance_title' => 'Documentazione di Provenienza',
        'provenance_description' => 'Documenta la storia di proprietà e la catena di autenticità',
        'condition_title' => 'Rapporto di Condizione',
        'condition_description' => 'Valutazione professionale dello stato fisico dell\'opera',
        'exhibitions_title' => 'Storia delle Esposizioni',
        'exhibitions_description' => 'Registro delle esposizioni pubbliche e della storia di esposizione',
        'photos_title' => 'Fotografia Professionale',
        'photos_description' => 'Documentazione ad alta risoluzione e fotografia di dettaglio',
        'save_annex' => 'Salva Allegato',
        'cancel' => 'Annulla',
        'upload_files' => 'Carica File',
        'drag_drop_files' => 'Trascina e rilascia i file qui, o clicca per selezionare',
        'max_file_size' => 'Dimensione massima file: 10MB per file',
        'supported_formats' => 'Formati supportati: PDF, JPG, PNG, DOCX',

        // Modulo Provenienza
        'ownership_history_description' => 'Documenta la storia di proprietà e la catena di autenticità di quest\'opera',
        'previous_owners' => 'Proprietari Precedenti',
        'previous_owners_placeholder' => 'Elenca i proprietari precedenti e le date di possesso...',
        'acquisition_details' => 'Dettagli di Acquisizione',
        'acquisition_details_placeholder' => 'Come è stata acquisita quest\'opera? Includi date, prezzi, case d\'asta...',
        'authenticity_sources' => 'Fonti di Autenticità',
        'authenticity_sources_placeholder' => 'Pareri di esperti, cataloghi ragionati, archivi istituzionali...',
        'save_provenance_data' => 'Salva Dati di Provenienza',

        // Modulo Condizione
        'condition_assessment_description' => 'Valutazione professionale dello stato fisico dell\'opera e delle esigenze di conservazione',
        'overall_condition' => 'Condizione Generale',
        'condition_excellent' => 'Eccellente',
        'condition_very_good' => 'Molto Buono',
        'condition_good' => 'Buono',
        'condition_fair' => 'Discreto',
        'condition_poor' => 'Scarso',
        'condition_notes' => 'Note sulla Condizione',
        'condition_notes_placeholder' => 'Descrizione dettagliata di eventuali danni, restauri o problemi di conservazione...',
        'conservation_history' => 'Storia di Conservazione',
        'conservation_history_placeholder' => 'Restauri precedenti, trattamenti o interventi di conservazione...',
        'save_condition_data' => 'Salva Dati di Condizione',

        // Modulo Esposizioni
        'exhibition_history_description' => 'Registro di musei, gallerie ed esposizioni pubbliche dove l\'opera è stata esposta',
        'exhibition_title' => 'Titolo Esposizione',
        'exhibition_title_placeholder' => 'Nome dell\'esposizione...',
        'venue' => 'Luogo',
        'venue_placeholder' => 'Nome del museo, galleria o istituzione...',
        'exhibition_dates' => 'Date dell\'Esposizione',
        'exhibition_notes' => 'Note',
        'exhibition_notes_placeholder' => 'Numero di catalogo, menzioni speciali, recensioni...',
        'add_exhibition' => 'Aggiungi Esposizione',
        'save_exhibitions_data' => 'Salva Dati delle Esposizioni',

        // Modulo Foto
        'photo_documentation_description' => 'Immagini di alta qualità per documentazione e scopi archivistici',
        'photo_type' => 'Tipo di Foto',
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
        'save_photos_data' => 'Salva Dati delle Foto',

        // Campi Aggiuntivi per il Modulo Condizione
        'select_condition' => 'Seleziona condizione...',
        'detailed_assessment' => 'Valutazione Dettagliata',
        'detailed_assessment_placeholder' => 'Descrizione dettagliata della condizione, inclusi eventuali danni, restauri o caratteristiche speciali...',
        'assessor_information' => 'Informazioni sul Valutatore',
        'assessor_placeholder' => 'Nome e credenziali del valutatore della condizione...',
        'save_condition_report' => 'Salva Rapporto di Condizione',

        // Campi del Modulo Esposizioni
        'major_exhibitions' => 'Esposizioni Principali',
        'major_exhibitions_placeholder' => 'Elenca esposizioni principali, musei, gallerie, date...',
        'publications_catalogues' => 'Pubblicazioni e Cataloghi',
        'publications_placeholder' => 'Libri, cataloghi, articoli dove quest\'opera è stata pubblicata...',
        'awards_recognition' => 'Premi e Riconoscimenti',
        'awards_placeholder' => 'Premi, riconoscimenti, critiche ricevute...',
        'save_exhibition_history' => 'Salva Storia delle Esposizioni',

        // Campi del Modulo Foto
        'click_upload_images' => 'Clicca per caricare immagini',
        'png_jpg_webp' => 'PNG, JPG, WEBP fino a 10MB ciascuno',
        'photo_descriptions' => 'Descrizioni delle Foto',
        'photo_descriptions_placeholder' => 'Descrivi le immagini: condizioni di illuminazione, dettagli catturati, scopo...',
        'photographer_credits' => 'Crediti del Fotografo',
        'photographer_placeholder' => 'Nome del fotografo e data...',
        'save_photo_documentation' => 'Salva Documentazione Fotografica',

        // Azioni Modali
        'close' => 'Chiudi',
        'error_no_certificate' => 'Errore: Nessun certificato selezionato',
        'saving' => 'Salvataggio...',
        'annex_saved_success' => 'Dati dell\'allegato salvati con successo!',
        'error_saving_annex' => 'Errore nel salvataggio dei dati dell\'allegato',

        // Traduzioni Mancanti per Sidebar e Componenti CoA
        'certificate' => 'Certificato CoA',
        'certificate_active' => 'Certificato Attivo',
        'serial_number' => 'Numero di Serie',
        'issue_date' => 'Data di Emissione',
        'expires' => 'Scade',
        'no_certificate_issued' => 'Questo EGI non ha un Certificato di Autenticità',
        'issue_certificate' => 'Emetti Certificato',
        'certificate_issued_successfully' => 'Certificato emesso con successo!',
        'pdf_generated_automatically' => 'PDF generato automaticamente!',
        'download_pdf_now' => 'Vuoi scaricare il PDF ora?',
        'digital_signatures' => 'Firme Digitali',
        'signature_by' => 'Firmato da',
        'signature_role' => 'Ruolo',
        'signature_provider' => 'Fornitore',
        'signature_date' => 'Data della Firma',
        'unknown_signer' => 'Firmatario Sconosciuto',
        'step_creating_certificate' => 'Creazione del certificato...',
        'step_generating_snapshot' => 'Generazione dello snapshot...',
        'step_generating_pdf' => 'Generazione del PDF...',
        'step_finalizing' => 'Finalizzazione...',
        'generating' => 'Generazione...',
        'generating_pdf' => 'Generazione PDF...',
        'error_issuing_certificate' => 'Errore durante l\'emissione del certificato: ',
        'issuing' => 'Emissione...',
        'unlock_with_coa_pro' => 'Sblocca con CoA Pro',
        'provenance_documentation' => 'Documentazione di Provenienza',
        'condition_reports' => 'Rapporti di Condizione',
        'exhibition_history' => 'Storia delle Esposizioni',
        'professional_pdf' => 'PDF Professionale',
        'only_creator_can_issue' => 'Solo il creatore può emettere certificati',

        // Sistema di Vocabolario dei Tratti CoA
        'traits_management_title' => 'Gestisci Tratti CoA',
        'traits_management_description' => 'Configura le caratteristiche tecniche dell\'opera per il Certificato di Autenticità',
        'status_configured' => 'Configurato',
        'status_not_configured' => 'Non Configurato',
        'edit_traits' => 'Modifica Tratti',
        'no_technique_selected' => 'Nessuna tecnica selezionata',
        'no_materials_selected' => 'Nessun materiale selezionato',
        'no_support_selected' => 'Nessun supporto selezionato',
        'custom' => 'personalizzato',
        'last_updated' => 'Ultimo Aggiornamento',
        'never_configured' => 'Mai Configurato',
        'clear_all' => 'Cancella Tutto',
        'saved' => 'Salvato',

        // Modale Vocabolario
        'modal_title' => 'Seleziona Tratti CoA',
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
        'items_selected' => 'elementi selezionati',
        'confirm' => 'Conferma',

        // Componenti Vocabolario
        'terms_available' => 'termini disponibili',
        'no_categories_available' => 'Nessuna categoria disponibile',
        'no_categories_found' => 'Nessuna categoria di vocabolario trovata.',
        'search_results' => 'Risultati della Ricerca',
        'results_for' => 'Per',
        'terms_found' => 'termini trovati',
        'results_found' => 'risultati trovati',
        'no_results_found' => 'Nessun risultato trovato',
        'no_terms_match_search' => 'Nessun termine corrisponde alla ricerca',
        'in_category' => 'nella categoria',
        'clear_search' => 'Cancella Ricerca',
        'no_terms_available' => 'Nessun termine disponibile',
        'no_terms_found_category' => 'Nessun termine trovato per la categoria',
        'categories' => 'Categorie',
        'back_to_start' => 'Torna all\'Inizio',
        'retry' => 'Riprova',
        'error' => 'Errore',
        'unexpected_error' => 'Si è verificato un errore imprevisto.',
        'professional_pdf_bundle' => 'Pacchetto PDF Professionale',
        'public_verification' => 'Verifica Pubblica',
        'verification_description' => 'Verifica l\'autenticità di un Certificato di Autenticità EGI',
        'verification_instructions' => 'Inserisci il numero di serie del certificato per verificarne l\'autenticità',
        'enter_serial' => 'Inserisci Numero di Serie',
        'serial_help' => 'Formato: ABC-123-DEF (lettere, numeri e trattini)',
        'certificate_of_authenticity' => 'Certificato di Autenticità',
        'public_verification_display' => 'Visualizzazione Verifica Pubblica',
        'verified_authentic' => 'Certificato Verificato e Autentico',
        'verified_at' => 'Verificato il',
        'artwork_information' => 'Informazioni sull\'Opera',
        'artwork_title' => 'Titolo dell\'Opera',
        'creator' => 'Creatore',
        'description' => 'Descrizione',
        'certificate_details' => 'Dettagli del Certificato',
        'cryptographic_verification' => 'Verifica Crittografica',
        'verify_again' => 'Verifica di Nuovo',
        'print_certificate' => 'Stampa Certificato',
        'share_verification' => 'Condividi Verifica',
        'powered_by_florenceegi' => 'Offerto da FlorenceEGI',
        'verification_timestamp' => 'Timestamp di Verifica',
        'link_copied' => 'Link copiato negli appunti',
        'revoke_certificate_confirm' => 'Revocare questo certificato? Questa azione non può essere annullata.',
        'reason_for_revocation' => 'Motivo della Revoca:',
        'certificate_revoked_successfully' => 'Certificato revocato con successo!',
        'error_revoking_certificate' => 'Errore durante la revoca del certificato: ',
        'manage_certificate' => 'Gestisci Certificato',
        'annex_management_coming_soon' => 'Gestione allegati disponibile a breve!',
        'issue_certificate_description' => 'Emetti un certificato per fornire prova di autenticità e sbloccare funzioni Pro',
        'serial' => 'Numero di Serie',
        'pro_features' => 'Funzionalità Pro',
        'provenance_docs' => 'Documentazione di Provenienza',
        'unlock_pro_features' => 'Sblocca Funzionalità Pro',
        'reason_for' => 'Motivo per',

        // Badge Firme QES
        'badge_author_signed' => 'Firmato dall\'Autore (QES)',
        'badge_inspector_signed' => 'Firmato dall\'Ispettore (QES)',
        'badge_integrity_ok' => 'Integrità Verificata',

        // Interfaccia Utente per la Località (CoA)
        'issue_place' => 'Luogo di Emissione',
        'location_placeholder' => 'Es., Firenze, Toscana, Italia',
        'save' => 'Salva',
        'location_hint' => 'Usa il formato "Città, Regione/Provincia, Paese" (o equivalente).',
        'location_required' => 'La località è obbligatoria',
        'location_saved' => 'Località salvata',
        'location_save_failed' => 'Salvataggio della località fallito',
        'location_updated' => 'Località aggiornata con successo',

        // Controfirma dell'Ispettore (QES)
        'inspector_countersign' => 'Controfirma dell\'Ispettore (QES)',
        'confirm_inspector_countersign' => 'Procedere con la controfirma dell\'ispettore?',
        'inspector_countersign_applied' => 'Controfirma dell\'ispettore applicata',
        'operation_failed' => 'Operazione fallita',
        'author_countersign' => 'Firma dell\'Autore (QES)',
        'confirm_author_countersign' => 'Procedere con la firma dell\'autore?',
        'author_countersign_applied' => 'Firma dell\'autore applicata',
        'regenerate_pdf' => 'Rigenera PDF',
        'pdf_regenerated' => 'PDF rigenerato',
        'pdf_regenerate_failed' => 'Rigenerazione del PDF fallita',
        'regenerating_pdf' => 'rigenerando PDF con firma',

        // Pagina di Verifica Pubblica
        'public_verify' => [
            'signature' => 'Firma',
            'author_signed' => 'Firmato dall\'Autore',
            'inspector_countersigned' => 'Controfirmato dall\'Ispettore',
            'timestamp_tsa' => 'Timestamp TSA',
            'qes' => 'QES',
            'wallet_signature' => 'Firma del Portafoglio',
            'verify_signature' => 'verifica firma',
            'certificate_hash' => 'Hash del Certificato (SHA-256)',
            'pdf_hash' => 'Hash del PDF (SHA-256)',
            'copy_hash' => 'Copia Hash',
            'copy_pdf_hash' => 'Copia Hash PDF',
            'hash_copied' => 'Hash copiato negli appunti!',
            'pdf_hash_copied' => 'Hash PDF copiato negli appunti!',
            'qr_code_verify' => 'Verifica tramite Codice QR',
            'qr_code' => 'Codice QR',
            'scan_to_verify' => 'Scansiona per Verificare',
            'status' => 'Stato',
            'valid' => 'Valido',
            'incomplete' => 'Incompleto',
            'revoked' => 'Revocato',

            // Intestazioni e Titoli
            'certificate_title' => 'Certificato di Autenticità',
            'public_verification_display' => 'Visualizzazione Verifica Pubblica',
            'verified_authentic' => 'Certificato Verificato e Autentico',
            'verified_at' => 'Verificato il',
            'serial_number' => 'Numero di Serie',
            'certificate_not_ready' => 'Certificato Non Pronto',
            'certificate_revoked' => 'Certificato Revocato',
            'certificate_not_valid' => 'Questo certificato non è più valido',
            'requires_coa_traits' => 'Richiede Tratti CoA',
            'certificate_not_ready_generic' => 'Certificato Non Pronto - Tratti Generici',

            // Informazioni sull'Opera
            'artwork_title' => 'Titolo',
            'year' => 'Anno',
            'dimensions' => 'Dimensioni',
            'edition' => 'Edizione',
            'author' => 'Autore',
            'technique' => 'Tecnica',
            'material' => 'Materiale',
            'support' => 'Supporto',
            'platform' => 'Piattaforma',
            'published_by' => 'Pubblicato da',
            'image' => 'Immagine',

            // Informazioni sul Certificato
            'issue_date' => 'Data di Emissione',
            'issued_by' => 'Emesso da',
            'issue_location' => 'Luogo di Emissione',
            'notes' => 'Note',

            // Allegati Professionali
            'professional_annexes' => 'Allegati Professionali',
            'provenance' => 'Provenienza',
            'condition_report' => 'Rapporto di Condizione',
            'exhibitions_publications' => 'Esposizioni/Pubblicazioni',
            'additional_photos' => 'Foto Aggiuntive',

            // Informazioni On-chain
            'on_chain_info' => 'Informazioni On-chain',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Sistema Dossier - Sistema Dossier
    |--------------------------------------------------------------------------
    */
    'dossier' => [
        'title' => 'Dossier di Immagini',
        'loading' => 'Caricamento dossier...',
        'view_complete' => 'Visualizza dossier di immagini completo',
        'close' => 'Chiudi Dossier',

        // Informazioni sull'Opera
        'artwork_info' => 'Informazioni sull\'Opera',
        'author' => 'Autore',
        'year' => 'Anno',
        'internal_id' => 'ID Interno',

        // Informazioni sul Dossier
        'dossier_info' => 'Informazioni sul Dossier',
        'images_count' => 'Immagini',
        'type' => 'Tipo',
        'utility_gallery' => 'Galleria Utilità',

        // Galleria
        'gallery_title' => 'Galleria di Immagini',
        'image_number' => 'Immagine :number',
        'image_of_total' => 'Immagine :current di :total',

        // Stati
        'no_utility_title' => 'Dossier non disponibile',
        'no_utility_message' => 'Nessuna immagine aggiuntiva disponibile per quest\'opera.',
        'no_utility_description' => 'Il dossier di immagini aggiuntive non è stato ancora configurato per quest\'opera.',

        'no_images_title' => 'Nessuna immagine disponibile',
        'no_images_message' => 'Il dossier esiste ma non contiene ancora immagini.',
        'no_images_description' => 'Immagini aggiuntive saranno aggiunte in futuro dal creatore dell\'opera.',

        'error_title' => 'Errore',
        'error_loading' => 'Errore nel caricamento del dossier',

        // Navigazione
        'previous_image' => 'Immagine Precedente',
        'next_image' => 'Immagine Successiva',
        'close_viewer' => 'Chiudi Visualizzatore',
        'of' => 'di',

        // Controlli Zoom
        'zoom_help' => 'Usa la rotellina del mouse o il tocco per lo zoom • Trascina per muoverti',
        'zoom_in' => 'Ingrandisci',
        'zoom_out' => 'Rimpicciolisci',
        'zoom_reset' => 'Reimposta Zoom',
        'zoom_fit' => 'Adatta allo Schermo',
    ],

];
