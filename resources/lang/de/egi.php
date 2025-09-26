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

    /*
    |--------------------------------------------------------------------------
    | Echtheitszertifikat (CoA)
    |--------------------------------------------------------------------------
    */

    'coa' => [
        // QES Badge System
        'badge_author_signed' => 'Autor Signiert (QES)',
        'badge_inspector_signed' => 'Inspektor Signiert (QES)',
        'badge_integrity_ok' => 'Integrität Bestätigt',
        'inspector_countersign' => 'Inspektor Gegenzeichnung (QES)',
        'confirm_inspector_countersign' => 'Mit der Gegenzeichnung des Inspektors fortfahren?',
        'inspector_countersign_applied' => 'Gegenzeichnung des Inspektors angewendet',
        'operation_failed' => 'Vorgang fehlgeschlagen',
        'author_countersign' => 'Autor Signatur (QES)',
        'confirm_author_countersign' => 'Mit der Signatur des Autors fortfahren?',
        'author_countersign_applied' => 'Signatur des Autors angewendet',
        'regenerate_pdf' => 'PDF Regenerieren',
        'pdf_regenerated' => 'PDF regeneriert',
        'pdf_regenerate_failed' => 'PDF-Regenerierung fehlgeschlagen',

        // Öffentliche Verifizierungsseite
        'public_verify' => [
            'signature' => 'Signatur',
            'author_signed' => 'Autor signiert',
            'inspector_countersigned' => 'Inspektor gegenzeichnet',
            'timestamp_tsa' => 'Timestamp TSA',
            'qes' => 'QES',
            'wallet_signature' => 'Wallet-Signatur',
            'verify_signature' => 'Signatur verifizieren',
            'certificate_hash' => 'Zertifikat-Hash (SHA-256)',
            'pdf_hash' => 'PDF-Hash (SHA-256)',
            'copy_hash' => 'Hash kopieren',
            'copy_pdf_hash' => 'PDF-Hash kopieren',
            'hash_copied' => 'Hash in die Zwischenablage kopiert!',
            'pdf_hash_copied' => 'PDF-Hash in die Zwischenablage kopiert!',
            'qr_code_verify' => 'QR-Code Verifizierung',
            'qr_code' => 'QR-Code',
            'scan_to_verify' => 'Zum Verifizieren scannen',
            'status' => 'Status',
            'valid' => 'Gültig',
            'incomplete' => 'Unvollständig',
            'revoked' => 'Widerrufen',

            // Header und Titel
            'certificate_title' => 'Echtheitszertifikat',
            'public_verification_display' => 'Öffentliche Verifizierungsanzeige',
            'verified_authentic' => 'Zertifikat Verifiziert und Authentisch',
            'verified_at' => 'Verifiziert am',
            'serial_number' => 'Seriennummer',
            'certificate_not_ready' => 'Zertifikat Nicht Bereit',
            'certificate_revoked' => 'Zertifikat Widerrufen',
            'certificate_not_valid' => 'Dieses Zertifikat ist nicht mehr gültig',
            'requires_coa_traits' => 'Erfordert CoA Traits',
            'certificate_not_ready_generic' => 'Zertifikat Nicht Bereit - Generische Traits',

            // Kunstwerk-Informationen
            'artwork_title' => 'Titel',
            'year' => 'Jahr',
            'dimensions' => 'Abmessungen',
            'edition' => 'Auflage',
            'author' => 'Autor',
            'technique' => 'Technik',
            'material' => 'Material',
            'support' => 'Träger',
            'platform' => 'Plattform',
            'published_by' => 'Veröffentlicht von',
            'image' => 'Bild',

            // Zertifikat-Informationen
            'issue_date' => 'Ausstellungsdatum',
            'issued_by' => 'Ausgestellt von',
            'issue_location' => 'Ausstellungsort',
            'notes' => 'Notizen',

            // Professionelle Anhänge
            'professional_annexes' => 'Professionelle Anhänge',
            'provenance' => 'Provenienz',
            'condition_report' => 'Zustandsbericht',
            'exhibitions_publications' => 'Ausstellungen/Publikationen',
            'additional_photos' => 'Zusätzliche Fotos',

            // On-chain Informationen
            'on_chain_info' => 'On-chain Informationen',
        ],
    ],

];
