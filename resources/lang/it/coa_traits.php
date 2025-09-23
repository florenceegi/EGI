<?php

return [
    /*
    |--------------------------------------------------------------------------
    | CoA Traits Management - Traduzioni Italiane
    |--------------------------------------------------------------------------
    |
    | Traduzioni per il sistema di gestione traits del CoA in FlorenceEGI
    | Utilizzato dai componenti vocabulary modal e traits management
    |
    */

    // Gestione Traits
    'management_title' => 'Gestione Traits CoA',
    'management_description' => 'Configura le caratteristiche tecniche dell\'opera per il Certificato di Autenticità',
    'status_configured' => 'Configurato',
    'status_not_configured' => 'Non Configurato',
    'edit_traits' => 'Modifica Traits',
    'last_updated' => 'Ultimo aggiornamento',
    'never_configured' => 'Mai configurato',
    'clear_all' => 'Cancella Tutto',
    'saved' => 'Salvato',
    'custom' => 'personalizzato',
    'issue_certificate_confirm' => 'Sei sicuro di voler emettere il certificato? Non potrai più modificare i traits dopo l\'emissione.',
    'issue_certificate' => 'Emetti Certificato',

    // Categorie
    'technique' => 'Tecnica',
    'materials' => 'Materiali',
    'support' => 'Supporto',
    'category_technique' => 'Tecnica',
    'category_materials' => 'Materiali',
    'category_support' => 'Supporto',
    'group_description_support' => 'Supporto o superficie su cui è realizzata l\'opera (es. tela, carta, legno, ecc.)',

    // Selezioni per Categoria
    'no_technique_selected' => 'Nessuna tecnica selezionata',
    'no_materials_selected' => 'Nessun materiale selezionato',
    'no_support_selected' => 'Nessun supporto selezionato',
    'no_items_selected_for' => 'Nessun elemento selezionato per',
    'no_category' => 'Nessuna categoria',

    // Modal Vocabolario
    'terms' => 'termini',
    'term' => 'termine',
    'groups' => 'gruppi',
    'group' => 'gruppo',
    'modal_title' => 'Seleziona Traits CoA',
    'search_placeholder' => 'Cerca termini...',
    'loading' => 'Caricamento...',
    'selected_items' => 'Elementi Selezionati',
    'no_items_selected' => 'Nessun elemento selezionato',
    'no_groups_available' => 'Nessun gruppo disponibile',
    'no_groups_found_for_category' => 'Nessun gruppo trovato per questa categoria',
    'add_custom' => 'Aggiungi Personalizzato',
    'add_custom_technique' => 'Aggiungi Tecnica',
    'add_custom_materials' => 'Aggiungi Materiale',
    'add_custom_support' => 'Aggiungi Supporto',
    'custom_short' => 'custom',
    'custom_term_placeholder' => 'Inserisci termine personalizzato (max 60 caratteri)',
    'add' => 'Aggiungi',
    'cancel' => 'Annulla',
    'items_selected' => 'elementi selezionati',
    'confirm' => 'Conferma',

    // Componenti Vocabolario - Categorie
    'terms_available' => 'termini disponibili',
    'no_categories_available' => 'Nessuna categoria disponibile',
    'no_categories_found' => 'Non sono state trovate categorie di vocabolario.',

    // Componenti Vocabolario - Termini
    'categories' => 'Categorie',
    'terms_found' => 'termini trovati',
    'no_terms_available' => 'Nessun termine disponibile',
    'no_terms_found_category' => 'Non sono stati trovati termini per la categoria',

    // Componenti Vocabolario - Ricerca
    'search_results' => 'Risultati ricerca',
    'results_for' => 'Per',
    'results_found' => 'risultati trovati',
    'no_results_found' => 'Nessun risultato trovato',
    'no_terms_match_search' => 'Nessun termine corrisponde alla ricerca',
    'in_category' => 'nella categoria',
    'in_all_categories' => 'in tutte le categorie',
    'clear_search' => 'Cancella ricerca',

    // Componenti Vocabolario - Errori
    'error' => 'Errore',
    'unexpected_error' => 'Si è verificato un errore imprevisto.',
    'retry' => 'Riprova',
    'back_to_start' => 'Torna all\'inizio',

    // Errori specifici del modal
    'errors' => [
        'modal_not_ready' => 'Il sistema di selezione vocabolario non è ancora caricato. Riprova tra qualche secondo.',
        'modal_malfunction' => 'Errore nel sistema di selezione. Ricarica la pagina e riprova.',
    ],

    // PDF Certificate - Titoli e Sezioni
    'pdf_certificate_title' => 'Certificato di Autenticità',
    'pdf_public_verification' => 'Visualizzazione Pubblica di Verifica',
    'pdf_artwork_info' => 'Informazioni Opera',
    'pdf_certificate_details' => 'Dettagli Certificato',
    'pdf_technical_traits' => 'Caratteristiche Tecniche Complete',
    'pdf_technical_details' => 'Dettagli Tecnici',

    // PDF Certificate - Campi
    'pdf_title' => 'Titolo',
    'pdf_author' => 'Autore',
    'pdf_year' => 'Anno',
    'pdf_edition' => 'Edizione',
    'pdf_location' => 'Ubicazione',
    'pdf_provenance' => 'Provenienza',
    'pdf_conservation' => 'Stato Conservazione',
    'pdf_issue_date' => 'Data Emissione',
    'pdf_issued_by' => 'Emesso da',
    'pdf_issue_place' => 'Luogo di emissione',
    'pdf_status' => 'Stato',
    'pdf_status_valid' => 'Valido',
    'pdf_hash_title' => 'Hash del certificato (SHA-256)',
    'pdf_last_update' => 'Ultimo aggiornamento',

    // PDF Certificate - Banner e Footer
    'pdf_verified_banner' => 'Certificato Verificato e Autentico',
    'pdf_verified_on' => 'Verificato il',
    'pdf_powered_by' => 'Powered by FlorenceEGI',
    'pdf_verification_timestamp' => 'Timestamp di Verifica',

    // PDF Certificate - Luoghi
    // PDF Certificate - Luoghi
    'pdf_florence_italy' => 'Firenze, Italia',

    // PDF Certificate - Nuove chiavi professionali
    'pdf_company_name' => 'FlorenceEGI',
    'pdf_artwork_image' => 'Immagine Opera',
    'pdf_image_not_available' => 'Immagine non disponibile',
    'pdf_qr_verification' => 'Scansiona per verificare online',
    'pdf_technical_metadata' => 'Metadata Tecnici',
    'pdf_creation_date' => 'Data Creazione',
    'pdf_internal_id' => 'ID Interno',
    'pdf_certificate_type' => 'Tipo Certificato',
    'pdf_blockchain_status' => 'Stato Blockchain',
    'pdf_verified_on_chain' => 'Verificato su Blockchain',
    'pdf_description' => 'Descrizione',
    'pdf_collection' => 'Collezione',
    'pdf_no_collection' => 'Nessuna collezione',
    'pdf_validity' => 'Validità',
    'pdf_unlimited_validity' => 'Validità Illimitata',
    'pdf_no_data_available' => 'Nessun dato disponibile',
    'pdf_no_technical_traits' => 'Caratteristiche tecniche non configurate per questa opera',
    'pdf_signature_section' => 'Sezione Firme Autorizzate',
    'pdf_authorized_signature' => 'Firma Autorizzata',
    'pdf_date_and_stamp' => 'Data e Timbro',
    'pdf_enterprise_footer' => 'Certificato digitale conforme agli standard internazionali per l\'autenticazione di opere d\'arte',

    // PDF Certificate - Sezioni aggiuntive
    'pdf_certification' => 'Certificazione',
    'pdf_issuer_signature' => 'Firma Emittente',
    'pdf_date_stamp' => 'Timbro Data',
    'pdf_signatures' => 'Firme Autorizzate',
    'pdf_expert_signature' => 'Firma Esperto',
    'pdf_owner_signature' => 'Firma Proprietario',
    'pdf_validate_certificate' => 'Valida Certificato',
    'pdf_scan_qr_validation' => 'Scansiona il QR code per validare',

    // Sezioni del PDF professionale aggiuntive
    'no_title_available' => 'Titolo non disponibile',
    'no_artist_available' => 'Artista non disponibile',
    'no_date_available' => 'Data non disponibile',
    'no_description_available' => 'Descrizione non disponibile',
    'no_collection_assigned' => 'Nessuna collezione assegnata',
    'no_technique_selected' => 'Nessuna tecnica selezionata',
    'no_materials_selected' => 'Nessun materiale selezionato',
    'no_support_selected' => 'Nessun supporto selezionato',

    // QR Code
    'pdf_qr_code_title' => 'QR Code Verifica',
    'pdf_scan_to_verify' => 'Scansiona per verificare',
    'pdf_qr_not_available' => 'QR Code non disponibile',

    // PDF Metadata Labels
    'pdf_certificate_type' => 'Tipo Certificato',
    'pdf_blockchain_status' => 'Stato Blockchain',
    'pdf_internal_id' => 'ID Interno',
    'pdf_size' => 'Dimensioni',
    'pdf_weight' => 'Peso',
    'pdf_image_dimensions' => 'Dimensioni Immagine',
    'pdf_file_type' => 'Tipo File',
    'pdf_file_extension' => 'Estensione',
    'pdf_upload_date' => 'Data Upload',
    'pdf_publication_status' => 'Stato Pubblicazione',
    'pdf_file_size' => 'Dimensione File',
    'pdf_core_certificate' => 'Certificato Core',
    'pdf_verified_on_chain' => 'Verificato su Blockchain',
    'pdf_published' => 'Pubblicato',
    'pdf_not_published' => 'Non Pubblicato',

    // PDF Verification Section
    'pdf_verified_banner' => 'CERTIFICATO DIGITALE VERIFICATO',
    'pdf_verified_on' => 'Verificato il',
    'pdf_verification_timestamp' => 'Timestamp di verifica',
    'pdf_hash_title' => 'Hash di Sicurezza',
    'pdf_signature_section' => 'Sezione Firme',
    'pdf_authorized_signature' => 'Firma Autorizzata',
    'pdf_date_and_stamp' => 'Data e Timbro',
    'pdf_last_update' => 'Ultimo aggiornamento',

    // PDF Professional New - Additional Keys
    'pdf_certificate_id' => 'ID Certificato',
    'category_platform_metadata' => 'Metadata Piattaforma',
    'pdf_verification_title' => 'Verifica Certificato',
    'pdf_scan_prompt' => 'Scansiona il QR code per verificare l\'autenticità del certificato online',
    'pdf_additional_info_title' => 'Informazioni Aggiuntive',
    'pdf_stamp_area' => 'Area Timbro',
    'pdf_stamp_caption' => 'Timbro Autore',
    'pdf_author_signature' => 'Firma Autore',
    'pdf_core_certificate' => 'Certificato Base',

    // Common Fallbacks
    'not_available' => 'N/A',
];