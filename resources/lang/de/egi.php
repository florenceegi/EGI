<?php

return [

    /*
    |--------------------------------------------------------------------------
    | NFT Card System - NFT Karten System
    |--------------------------------------------------------------------------
    */

    // Badges und Status
    'badge' => [
        'owned' => 'BESESSEN',
        'media_content' => 'Medieninhalt',
    ],

    // Titel
    'title' => [
        'untitled' => '✨ Unbenanntes EGI',
    ],

    // Plattform
    'platform' => [
        'powered_by' => 'Powered by :platform',
    ],

    // Ersteller
    'creator' => [
        'created_by' => '👨‍🎨 Erstellt von:',
    ],

    // Preise
    'price' => [
        'purchased_for' => '💳 Gekauft für',
        'price' => '💰 Preis',
        'floor' => '📊 Mindestpreis',
    ],

    // Status
    'status' => [
        'not_for_sale' => '🚫 Nicht zu verkaufen',
        'draft' => '⏳ Entwurf',
    ],

    // Aktionen
    'actions' => [
        'view' => 'Anzeigen',
        'view_details' => 'EGI-Details anzeigen',
        'reserve' => 'Aktivieren',
        'outbid' => 'Höher bieten um zu aktivieren',
    ],

    // Reservierungsdetails
    'reservation' => [
        'highest_bid' => 'Höchstgebot',
        'fegi_reservation' => 'FEGI Reservierung',
        'strong_bidder' => 'Bester Bieter',
        'weak_bidder' => 'FEGI Code',
        'activator' => 'Mit-Ersteller',
        'activated_by' => 'Aktiviert von',
    ],

    'carousel' => [
        // Dynamic Headers
        'headers' => [
            'egi_list' => 'EGI',
            'egi_card' => 'EGI',
            'creators' => 'Künstler',
            'collections' => 'Sammlungen',
            'collectors' => 'Aktivatoren'
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Hero Coverflow - Hero-Bereich mit 3D-Coverflow-Effekt
    |--------------------------------------------------------------------------
    */

    'hero_coverflow' => [
        'title' => 'Ein EGI zu aktivieren bedeutet, Spuren zu hinterlassen.',
        'subtitle' => 'Ihr Name bleibt für immer neben dem des Schöpfers: ohne Sie würde das Kunstwerk nicht existieren.',
        'carousel_mode' => 'Karussell-Ansicht',
        'list_mode' => 'Raster-Ansicht',
        'carousel_label' => 'Karussell mit ausgewählten Kunstwerken',
        'no_egis' => 'Momentan sind keine ausgewählten Kunstwerke verfügbar.',
        'navigation' => [
            'previous' => 'Vorheriges Kunstwerk',
            'next' => 'Nächstes Kunstwerk',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Dossier System - Dossier System
    |--------------------------------------------------------------------------
    */
    'dossier' => [
        'title' => 'Bilderdossier',
        'loading' => 'Dossier wird geladen...',
        'view_complete' => 'Vollständiges Bilderdossier ansehen',
        'close' => 'Dossier schließen',

        // Artwork Info
        'artwork_info' => 'Kunstwerk-Informationen',
        'author' => 'Autor',
        'year' => 'Jahr',
        'internal_id' => 'Interne ID',

        // Dossier Info  
        'dossier_info' => 'Dossier-Informationen',
        'images_count' => 'Bilder',
        'type' => 'Typ',
        'utility_gallery' => 'Hilfsgalerie',

        // Gallery
        'gallery_title' => 'Bildergalerie',
        'image_number' => 'Bild :number',
        'image_of_total' => 'Bild :current von :total',

        // States
        'no_utility_title' => 'Dossier nicht verfügbar',
        'no_utility_message' => 'Für dieses Kunstwerk sind keine zusätzlichen Bilder verfügbar.',
        'no_utility_description' => 'Das Dossier für zusätzliche Bilder wurde für dieses Kunstwerk noch nicht konfiguriert.',

        'no_images_title' => 'Keine Bilder verfügbar',
        'no_images_message' => 'Das Dossier existiert, enthält aber noch keine Bilder.',
        'no_images_description' => 'Zusätzliche Bilder werden in Zukunft vom Ersteller des Kunstwerks hinzugefügt.',

        'error_title' => 'Fehler',
        'error_loading' => 'Fehler beim Laden des Dossiers',

        // Navigation
        'previous_image' => 'Vorheriges Bild',
        'next_image' => 'Nächstes Bild',
        'close_viewer' => 'Betrachter schließen',
        'of' => 'von',

        // Zoom Controls
        'zoom_help' => 'Verwenden Sie das Mausrad oder Touch zum Zoomen • Ziehen zum Bewegen',
        'zoom_in' => 'Vergrößern',
        'zoom_out' => 'Verkleinern',
        'zoom_reset' => 'Zoom zurücksetzen',
        'zoom_fit' => 'An Bildschirm anpassen',
    ],

];
