<?php

return [

    /*
    |--------------------------------------------------------------------------
    | EGI (Ecological Goods Invent) - Übersetzungen ins Deutsche
    |--------------------------------------------------------------------------
    |
    | Übersetzungen für das CRUD-System von EGI in FlorenceEGI
    | Version: 1.0.0 - Kompatibel mit Oracode System 2.0
    |
    */

    // Meta und SEO
    'meta_description_default' => 'Details für EGI: :title',
    'image_alt_default' => 'EGI-Bild',
    'view_full' => 'Vollständig anzeigen',
    'artwork_loading' => 'Kunstwerk wird geladen...',

    // Grundlegende Informationen
    'by_author' => 'von :name',
    'unknown_creator' => 'Unbekannter Künstler',

    // Hauptaktionen
    'like_button_title' => 'Zu Favoriten hinzufügen',
    'unlike_button_title' => 'Aus Favoriten entfernen',
    'like_button_aria' => 'Diesen EGI zu Ihren Favoriten hinzufügen',
    'unlike_button_aria' => 'Diesen EGI aus Ihren Favoriten entfernen',
    'share_button_title' => 'Diesen EGI teilen',

    'current_price' => 'Aktueller Preis',
    'not_currently_listed' => 'Zu aktivieren',
    'contact_owner_availability' => 'Kontaktieren Sie den Eigentümer für Verfügbarkeit',
    'not_for_sale' => 'Nicht zu verkaufen',
    'not_for_sale_description' => 'Dieser EGI ist derzeit nicht zum Kauf verfügbar',
    'liked' => 'Gefällt',
    'add_to_favorites' => 'Zu Favoriten hinzufügen',
    'reserve_this_piece' => 'Aktivieren',

    /*
    |--------------------------------------------------------------------------
    | NFT-Kartensystem - NFT-Kartensystem
    |--------------------------------------------------------------------------
    */

    // Abzeichen und Status
    'badge' => [
        'owned' => 'BESITZT',
        'media_content' => 'Medieninhalt',
        'winning_bid' => 'GEWINNENDES GEBOT',
        'outbid' => 'ÜBERBOTEN',
        'not_owned' => 'NICHT BESITZT',
        'to_activate' => 'ZU AKTIVIEREN',
        'activated' => 'AKTIVIERT',
    ],

    // Titel
    'title' => [
        'untitled' => '✨ EGI ohne Titel',
    ],

    // Plattform
    'platform' => [
        'powered_by' => 'Betrieben von :platform',
    ],

    // Künstler
    'creator' => [
        'created_by' => '👨‍🎨 Erstellt von:',
    ],

    // Preise
    'price' => [
        'purchased_for' => '💳 Gekauft für',
        'price' => '💰 Preis',
        'floor' => '📊 Mindestpreis',
        'highest_bid' => '🏆 Höchstes Gebot',
    ],

    // Reservierungen
    'reservation' => [
        'count' => 'Reservierungen',
        'highest_bidder' => 'Höchstbietender',
        'by' => 'von',
        'highest_bid' => 'Höchstes Gebot',
        'fegi_reservation' => 'FEGI-Reservierung',
        'strong_bidder' => 'Höchstbietender',
        'weak_bidder' => 'FEGI-Code',
        'activator' => 'Co-Schöpfer',
        'activated_by' => 'Aktiviert von',
    ],

    // Hinweis zur Originalwährung
    'originally_reserved_in' => 'Ursprünglich reserviert in :currency für :amount',
    'originally_reserved_in_short' => 'Res. :currency :amount',

    // Status
    'status' => [
        'not_for_sale' => '🚫 Nicht zu verkaufen',
        'draft' => '⏳ Entwurf',
        // Phase 2: Availability status
        'login_required' => '🔐 Anmeldung Erforderlich',
        'already_minted' => '✅ Bereits Gemintet',
        'not_available' => '⚠️ Nicht Verfügbar',
    ],

    // Aktionen
    'actions' => [
        'view' => 'Ansehen',
        'view_details' => 'EGI-Details ansehen',
        'reserve' => 'Aktivieren',
        'reserved' => 'Reserviert',
        'outbid' => 'Überbieten zum Aktivieren',
        'view_history' => 'Verlauf',
        'reserve_egi' => ':title reservieren',
        // Phase 2: Dual path actions
        'mint_now' => 'Jetzt Minten',
        'mint_direct' => 'Sofort Minten',
    ],

    // System für Reservierungsverlauf
    'history' => [
        'title' => 'Reservierungsverlauf',
        'no_reservations' => 'Keine Reservierungen gefunden',
        'total_reservations' => '{1} :count Reservierung|[2,*] :count Reservierungen',
        'current_highest' => 'Aktuelle höchste Priorität',
        'superseded' => 'Niedrigere Priorität',
        'created_at' => 'Erstellt am',
        'amount' => 'Betrag',
        'type_strong' => 'Starke Reservierung',
        'type_weak' => 'Schwache Reservierung',
        'loading' => 'Verlauf wird geladen...',
        'error' => 'Fehler beim Laden des Verlaufs',
    ],

    // Informative Abschnitte
    'properties' => 'Eigenschaften',
    'supports_epp' => 'Unterstützt EPP',
    'asset_type' => 'Asset-Typ',
    'format' => 'Format',
    'about_this_piece' => 'Über dieses Werk',
    'default_description' => 'Dieses einzigartige digitale Werk repräsentiert einen Moment kreativen Ausdrucks und fängt die Essenz der digitalen Kunst in der Blockchain-Ära ein.',
    'provenance' => 'Provenienz',
    'view_full_collection' => 'Vollständige Sammlung ansehen',

    /*
    |--------------------------------------------------------------------------
    | CRUD-System - Bearbeitungssystem
    |--------------------------------------------------------------------------
    */

    'crud' => [

        // Kopfzeile und Navigation
        'edit_egi' => 'EGI bearbeiten',
        'toggle_edit_mode' => 'Bearbeitungsmodus ein-/ausschalten',
        'start_editing' => 'Bearbeitung starten',
        'save_changes' => 'Änderungen speichern',
        'cancel' => 'Abbrechen',

        // Titelfeld
        'title' => 'Titel',
        'title_placeholder' => 'Geben Sie den Titel des Werks ein...',
        'title_hint' => 'Maximal 60 Zeichen',
        'characters_remaining' => 'Zeichen verbleibend',

        // Beschreibungsfeld
        'description' => 'Beschreibung',
        'description_placeholder' => 'Beschreiben Sie Ihr Werk, seine Geschichte und Bedeutung...',
        'description_hint' => 'Erzählen Sie die Geschichte hinter Ihrer Kreation',

        // Preisfeld
        'price' => 'Preis',
        'price_placeholder' => '0.00',
        'price_hint' => 'Preis in ALGO (leer lassen, wenn nicht zu verkaufen)',
        'price_locked_message' => 'Preis gesperrt - EGI bereits reserviert',

        // Erstellungsdatumfeld
        'creation_date' => 'Erstellungsdatum',
        'creation_date_hint' => 'Wann haben Sie dieses Werk erstellt?',

        // Veröffentlicht-Feld
        'is_published' => 'Veröffentlicht',
        'is_published_hint' => 'Das Werk öffentlich sichtbar machen',

        // Anzeigemodus - Aktueller Status
        'current_title' => 'Aktueller Titel',
        'no_title' => 'Kein Titel festgelegt',
        'current_price' => 'Aktueller Preis',
        'price_not_set' => 'Preis nicht festgelegt',
        'current_status' => 'Veröffentlichungsstatus',
        'status_published' => 'Veröffentlicht',
        'status_draft' => 'Entwurf',

        // Löschsystem
        'delete_egi' => 'EGI löschen',
        'delete_confirmation_title' => 'Löschung bestätigen',
        'delete_confirmation_message' => 'Sind Sie sicher, dass Sie diesen EGI löschen möchten? Diese Aktion kann nicht rückgängig gemacht werden.',
        'delete_confirm' => 'Dauerhaft löschen',

        // Validierungsmeldungen
        'title_required' => 'Der Titel ist erforderlich',
        'title_max_length' => 'Der Titel darf 60 Zeichen nicht überschreiten',
        'price_numeric' => 'Der Preis muss eine gültige Zahl sein',
        'price_min' => 'Der Preis darf nicht negativ sein',
        'creation_date_format' => 'Ungültiges Datumsformat',

        // Erfolgsmeldungen
        'update_success' => 'EGI erfolgreich aktualisiert!',
        'delete_success' => 'EGI erfolgreich gelöscht.',

        // Fehlermeldungen
        'update_error' => 'Fehler beim Aktualisieren des EGI.',
        'delete_error' => 'Fehler beim Löschen des EGI.',
        'permission_denied' => 'Sie haben nicht die erforderlichen Berechtigungen für diese Aktion.',
        'not_found' => 'EGI nicht gefunden.',

        // Allgemeine Meldungen
        'no_changes_detected' => 'Keine Änderungen erkannt.',
        'unsaved_changes_warning' => 'Sie haben ungespeicherte Änderungen. Sind Sie sicher, dass Sie verlassen möchten?',
    ],

    /*
    |--------------------------------------------------------------------------
    | Responsive Etiketten - Mobil/Tablet
    |--------------------------------------------------------------------------
    */

    'mobile' => [
        'edit_egi_short' => 'Bearbeiten',
        'save_short' => 'Speichern',
        'delete_short' => 'Löschen',
        'cancel_short' => 'Abbrechen',
        'published_short' => 'Veröff.',
        'draft_short' => 'Entwurf',
    ],

    /*
    |--------------------------------------------------------------------------
    | EGI-Karussell - Hervorgehobene EGIs auf der Startseite
    |--------------------------------------------------------------------------
    */

    'carousel' => [
        'two_columns' => 'Listenansicht',
        'three_columns' => 'Kartenansicht',
        'navigation' => [
            'previous' => 'Vorherige',
            'next' => 'Nächste',
            'slide' => 'Zur Folie :number gehen',
        ],
        'empty_state' => [
            'title' => 'Kein Inhalt verfügbar',
            'subtitle' => 'Kommen Sie bald wieder für neue Inhalte!',
            'no_egis' => 'Derzeit sind keine EGI-Werke verfügbar.',
            'no_creators' => 'Derzeit sind keine Künstler verfügbar.',
            'no_collections' => 'Derzeit sind keine Sammlungen verfügbar.',
            'no_collectors' => 'Derzeit sind keine Sammler verfügbar.'
        ],

        // Schaltflächen für Inhaltstypen
        'content_types' => [
            'egi_list' => 'EGI-Listenansicht',
            'egi_card' => 'EGI-Kartenansicht',
            'creators' => 'Hervorgehobene Künstler',
            'collections' => 'Kunstsammlungen',
            'collectors' => 'Top-Sammler'
        ],

        // Schaltflächen für Anzeigemodi
        'view_modes' => [
            'carousel' => 'Karussellansicht',
            'list' => 'Listenansicht'
        ],

        // Modus-Etiketten
        'carousel_mode' => 'Karussell',
        'list_mode' => 'Liste',

        // Inhalts-Etiketten
        'creators' => 'Künstler',
        'collections' => 'Sammlungen',
        'collectors' => 'Sammler',

        // Dynamische Kopfzeilen
        'headers' => [
            'egi_list' => 'EGI',
            'egi_card' => 'EGI',
            'creators' => 'Künstler',
            'collections' => 'Sammlungen',
            'collectors' => 'Aktivatoren'
        ],

        // Karussellabschnitte
        'sections' => [
            'egis' => 'Hervorgehobene EGIs',
            'creators' => 'Aufstrebende Künstler',
            'collections' => 'Exklusive Sammlungen',
            'collectors' => 'Top-Sammler'
        ],
        'view_all' => 'Alle ansehen',
        'items' => 'Elemente',

        // Titel und Untertitel für das Mehrinhalt-Karussell
        'title' => 'Aktivieren Sie einen EGI!',
        'subtitle' => 'Ein Werk zu aktivieren bedeutet, sich ihm anzuschließen und für immer als Teil seiner Geschichte anerkannt zu werden.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Listenansicht - Listenmodus auf der Startseite
    |--------------------------------------------------------------------------
    */

    'list' => [
        'title' => 'Nach Kategorie erkunden',
        'subtitle' => 'Durchstöbern Sie die verschiedenen Kategorien, um zu finden, wonach Sie suchen',

        'content_types' => [
            'egi_list' => 'EGI-Liste',
            'creators' => 'Künstlerliste',
            'collections' => 'Sammlungsliste',
            'collectors' => 'Sammlerliste'
        ],

        'headers' => [
            'egi_list' => 'EGI-Werke',
            'creators' => 'Künstler',
            'collections' => 'Sammlungen',
            'collectors' => 'Sammler'
        ],

        'empty_state' => [
            'title' => 'Keine Elemente gefunden',
            'subtitle' => 'Versuchen Sie, eine andere Kategorie auszuwählen',
            'no_egis' => 'Keine EGI-Werke gefunden.',
            'no_creators' => 'Keine Künstler gefunden.',
            'no_collections' => 'Keine Sammlungen gefunden.',
            'no_collectors' => 'Keine Sammler gefunden.'
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Desktop-Karussell - EGI-Karussell nur für Desktop
    |--------------------------------------------------------------------------
    */

    'desktop_carousel' => [
        'title' => 'Hervorgehobene digitale Werke',
        'subtitle' => 'Die besten EGI-Kreationen unserer Community',
        'navigation' => [
            'previous' => 'Vorherige',
            'next' => 'Nächste',
            'slide' => 'Zur Folie :number gehen',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Mobile-Umschaltung - Umschaltung der mobilen Ansicht
    |--------------------------------------------------------------------------
    */

    'mobile_toggle' => [
        'title' => 'FlorenceEGI erkunden',
        'subtitle' => 'Wählen Sie, wie Sie den Inhalt durchstöbern möchten',
        'carousel_mode' => 'Karussellansicht',
        'list_mode' => 'Listenansicht',
    ],

    /*
    |--------------------------------------------------------------------------
    | Hero Coverflow - Hero-Bereich mit 3D-Coverflow-Effekt
    |--------------------------------------------------------------------------
    */

    'hero_coverflow' => [
        'title' => 'Einen EGI zu aktivieren bedeutet, eine Spur zu hinterlassen.',
        'subtitle' => 'Ihr Name bleibt für immer neben dem des Schöpfers: Ohne Sie würde das Werk nicht existieren.',
        'carousel_mode' => 'Karussellansicht',
        'list_mode' => 'Rasteransicht',
        'carousel_label' => 'Karussell der hervorgehobenen Werke',
        'no_egis' => 'Derzeit sind keine hervorgehobenen Werke verfügbar.',
        'navigation' => [
            'previous' => 'Vorheriges Werk',
            'next' => 'Nächstes Werk',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Barrierefreiheitsetiketten - Bildschirmleser
    |--------------------------------------------------------------------------
    */

    'a11y' => [
        'edit_form' => 'EGI-Bearbeitungsformular',
        'delete_button' => 'EGI-Löschknopf',
        'toggle_edit' => 'Bearbeitungsmodus umschalten',
        'save_form' => 'EGI-Änderungen speichern',
        'close_modal' => 'Bestätigungsfenster schließen',
        'required_field' => 'Pflichtfeld',
        'optional_field' => 'Optionales Feld',
    ],

    'collection' => [
        'part_of' => 'Teil von',
    ],

    // Sammlungskollaboratoren
    'collection_collaborators' => 'Kollaboratoren',
    'owner' => 'Eigentümer',
    // 'creator' => 'Schöpfer',
    'no_other_collaborators' => 'Keine weiteren Kollaboratoren',

    /*
    |--------------------------------------------------------------------------
    | Authentizitätszertifikat (CoA)
    |--------------------------------------------------------------------------
    */

    'coa' => [
        'none' => 'Kein Authentizitätszertifikat',
        'title' => 'Authentizitätszertifikat',
        'status' => 'Status',
        'issued' => 'Ausgestellt am',
        'verification' => 'Verifizierungs-ID',
        'copy' => 'Kopieren',
        'copied' => 'Kopiert!',
        'view' => 'Ansehen',
        'pdf' => 'PDF',
        'reissue' => 'Neu ausstellen',
        'issue' => 'Zertifikat ausstellen',
        'annexes' => 'Anlagen',
        'add_annex' => 'Anlage hinzufügen',
        'annex_coming_soon' => 'Anlagenverwaltung bald verfügbar!',
        'pro' => 'Pro',
        'unlock_pro' => 'Mit CoA Pro freischalten',
        'provenance' => 'Provenienzdokumentation',
        'pdf_bundle' => 'Professionelles PDF-Paket',
        'issue_description' => 'Stellen Sie ein Zertifikat aus, um einen Authentizitätsnachweis zu liefern und Pro-Funktionen freizuschalten',
        'creator_only' => 'Nur der Schöpfer kann Zertifikate ausstellen',
        'active' => 'Aktiv',
        'revoked' => 'Widerrufen',
        'expired' => 'Abgelaufen',
        'manage_coa' => 'CoA verwalten',
        'no_certificate' => 'Noch kein Zertifikat ausgestellt',

        // JavaScript-Meldungen
        'confirm_issue' => 'Ein Authentizitätszertifikat für diesen EGI ausstellen?',
        'issued_success' => 'Zertifikat erfolgreich ausgestellt!',
        'confirm_reissue' => 'Dieses Zertifikat neu ausstellen? Dies erstellt eine neue Version.',
        'reissued_success' => 'Zertifikat erfolgreich neu ausgestellt!',
        'reissue_certificate_confirm' => 'Sind Sie sicher, dass Sie dieses Zertifikat neu ausstellen möchten?',
        'certificate_reissued_successfully' => 'Zertifikat erfolgreich neu ausgestellt!',
        'error_reissuing_certificate' => 'Fehler beim Neu-Ausstellen des Zertifikats',
        'revocation_reason' => 'Grund für den Widerruf:',
        'confirm_revoke' => 'Dieses Zertifikat widerrufen? Diese Aktion kann nicht rückgängig gemacht werden.',
        'revoked_success' => 'Zertifikat erfolgreich widerrufen!',
        'error_issuing' => 'Fehler beim Ausstellen des Zertifikats',
        'error_reissuing' => 'Fehler beim Neu-Ausstellen des Zertifikats',
        'error_revoking' => 'Fehler beim Widerrufen des Zertifikats',
        'unknown_error' => 'Unbekannter Fehler',
        'verify_any_certificate' => 'Beliebiges Zertifikat verifizieren',

        // Modal für Anlagen
        'manage_annexes_title' => 'CoA Pro-Anlagen verwalten',
        'annexes_description' => 'Fügen Sie professionelle Dokumentation hinzu, um Ihr Zertifikat zu verbessern',
        'provenance_tab' => 'Provenienz',
        'condition_tab' => 'Zustand',
        'exhibitions_tab' => 'Ausstellungen',
        'photos_tab' => 'Fotos',
        'provenance_title' => 'Provenienzdokumentation',
        'provenance_description' => 'Dokumentieren Sie die Besitzgeschichte und die Authentizitätskette',
        'condition_title' => 'Zustandsbericht',
        'condition_description' => 'Professionelle Bewertung des physischen Zustands des Werks',
        'exhibitions_title' => 'Ausstellungsgeschichte',
        'exhibitions_description' => 'Register öffentlicher Ausstellungen und Anzeigeverlauf',
        'photos_title' => 'Professionelle Fotografie',
        'photos_description' => 'Hochauflösende Dokumentation und Detailfotografie',
        'save_annex' => 'Anlage speichern',
        'cancel' => 'Abbrechen',
        'upload_files' => 'Dateien hochladen',
        'drag_drop_files' => 'Dateien hierher ziehen und ablegen oder klicken, um auszuwählen',
        'max_file_size' => 'Maximale Dateigröße: 10 MB pro Datei',
        'supported_formats' => 'Unterstützte Formate: PDF, JPG, PNG, DOCX',

        // Provenienz-Formular
        'ownership_history_description' => 'Dokumentieren Sie die Besitzgeschichte und die Authentizitätskette dieses Werks',
        'previous_owners' => 'Vorherige Eigentümer',
        'previous_owners_placeholder' => 'Listen Sie vorherige Eigentümer und Besitzdaten auf...',
        'acquisition_details' => 'Erwerbsdetails',
        'acquisition_details_placeholder' => 'Wie wurde dieses Werk erworben? Geben Sie Daten, Preise, Auktionshäuser an...',
        'authenticity_sources' => 'Authentizitätsquellen',
        'authenticity_sources_placeholder' => 'Expertenmeinungen, katalogisierte Werke, institutionelle Archive...',
        'save_provenance_data' => 'Provenienzdaten speichern',

        // Zustandsformular
        'condition_assessment_description' => 'Professionelle Bewertung des physischen Zustands des Werks und der Konservierungsbedürfnisse',
        'overall_condition' => 'Gesamtzustand',
        'condition_excellent' => 'Ausgezeichnet',
        'condition_very_good' => 'Sehr gut',
        'condition_good' => 'Gut',
        'condition_fair' => 'Mäßig',
        'condition_poor' => 'Schlecht',
        'condition_notes' => 'Zustandsnotizen',
        'condition_notes_placeholder' => 'Detaillierte Beschreibung von Schäden, Restaurierungen oder Konservierungsproblemen...',
        'conservation_history' => 'Konservierungsgeschichte',
        'conservation_history_placeholder' => 'Frühere Restaurierungen, Behandlungen oder Konservierungseingriffe...',
        'save_condition_data' => 'Zustandsdaten speichern',

        // Ausstellungsformular
        'exhibition_history_description' => 'Register von Museen, Galerien und öffentlichen Ausstellungen, in denen dieses Werk ausgestellt wurde',
        'exhibition_title' => 'Ausstellungstitel',
        'exhibition_title_placeholder' => 'Name der Ausstellung...',
        'venue' => 'Veranstaltungsort',
        'venue_placeholder' => 'Name des Museums, der Galerie oder der Institution...',
        'exhibition_dates' => 'Ausstellungsdaten',
        'exhibition_notes' => 'Notizen',
        'exhibition_notes_placeholder' => 'Katalognummer, besondere Erwähnungen, Rezensionen...',
        'add_exhibition' => 'Ausstellung hinzufügen',
        'save_exhibitions_data' => 'Ausstellungsdaten speichern',

        // Fotodokumentationsformular
        'photo_documentation_description' => 'Hochwertige Bilder für Dokumentation und Archivierungszwecke',
        'photo_type' => 'Fototyp',
        'photo_overall' => 'Gesamtansicht',
        'photo_detail' => 'Detail',
        'photo_raking' => 'Streiflicht',
        'photo_uv' => 'UV-Fotografie',
        'photo_infrared' => 'Infrarot',
        'photo_back' => 'Rückseite',
        'photo_signature' => 'Signatur/Markierungen',
        'photo_frame' => 'Rahmen/Montage',
        'photo_description' => 'Beschreibung',
        'photo_description_placeholder' => 'Beschreiben Sie, was dieses Foto zeigt...',
        'save_photos_data' => 'Fotodaten speichern',

        // Zusätzliche Felder für das Zustandsformular
        'select_condition' => 'Zustand auswählen...',
        'detailed_assessment' => 'Detaillierte Bewertung',
        'detailed_assessment_placeholder' => 'Detaillierte Beschreibung des Zustands, einschließlich Schäden, Restaurierungen oder besonderer Merkmale...',
        'conservation_history_placeholder' => 'Frühere Konservierungsbehandlungen, Daten und Konservatoren...',
        'assessor_information' => 'Informationen des Gutachters',
        'assessor_placeholder' => 'Name und Qualifikationen des Zustandsgutachters...',
        'save_condition_report' => 'Zustandsbericht speichern',

        // Felder des Ausstellungsformulars
        'major_exhibitions' => 'Wichtige Ausstellungen',
        'major_exhibitions_placeholder' => 'Listen Sie wichtige Ausstellungen, Museen, Galerien, Daten auf...',
        'publications_catalogues' => 'Publikationen und Kataloge',
        'publications_placeholder' => 'Bücher, Kataloge, Artikel, in denen dieses Werk veröffentlicht wurde...',
        'awards_recognition' => 'Auszeichnungen und Anerkennungen',
        'awards_placeholder' => 'Auszeichnungen, Anerkennungen, Kritiken...',
        'save_exhibition_history' => 'Ausstellungsgeschichte speichern',
        'exhibition_history_description' => 'Register der Ausstellungen, in denen dieses Werk gezeigt wurde',

        // Felder des Fotodokumentationsformulars
        'click_upload_images' => 'Klicken, um Bilder hochzuladen',
        'png_jpg_webp' => 'PNG, JPG, WEBP bis zu 10 MB pro Datei',
        'photo_descriptions' => 'Fotobeschreibungen',
        'photo_descriptions_placeholder' => 'Beschreiben Sie die Bilder: Lichtverhältnisse, erfasste Details, Zweck...',
        'photographer_credits' => 'Fotografenkredite',
        'photographer_placeholder' => 'Name des Fotografen und Datum...',
        'save_photo_documentation' => 'Fotodokumentation speichern',
        'photo_documentation_description' => 'Hochauflösende Bilder für Dokumentation und Versicherungszwecke',

        // Aktionen des Modals
        'close' => 'Schließen',
        'error_no_certificate' => 'Fehler: Kein Zertifikat ausgewählt',
        'saving' => 'Speichern...',
        'annex_saved_success' => 'Anlagendaten erfolgreich gespeichert!',
        'error_saving_annex' => 'Fehler beim Speichern der Anlagendaten',

        // Fehlende Übersetzungen für Seitenleiste und CoA-Komponenten
        'certificate' => 'CoA-Zertifikat',
        'no_certificate' => 'Kein Zertifikat',
        'certificate_active' => 'Zertifikat aktiv',
        'serial_number' => 'Seriennummer',
        'issue_date' => 'Ausstellungsdatum',
        'expires' => 'Läuft ab',
        'no_certificate_issued' => 'Dieser EGI hat kein Authentizitätszertifikat',
        'issue_certificate' => 'Zertifikat ausstellen',
        'certificate_issued_successfully' => 'Zertifikat erfolgreich ausgestellt!',
        'pdf_generated_automatically' => 'PDF automatisch generiert!',
        'download_pdf_now' => 'Möchten Sie das PDF jetzt herunterladen?',
        'digital_signatures' => 'Digitale Signaturen',
        'signature_by' => 'Signiert von',
        'signature_role' => 'Rolle',
        'signature_provider' => 'Anbieter',
        'signature_date' => 'Signaturdatum',
        'unknown_signer' => 'Unbekannter Unterzeichner',
        'step_creating_certificate' => 'Zertifikat wird erstellt...',
        'step_generating_snapshot' => 'Schnappschuss wird generiert...',
        'step_generating_pdf' => 'PDF wird generiert...',
        'step_finalizing' => 'Abschluss...',
        'generating' => 'Generieren...',
        'generating_pdf' => 'PDF wird generiert...',
        'error_issuing_certificate' => 'Fehler beim Ausstellen des Zertifikats: ',
        'issuing' => 'Ausstellen...',
        'unlock_with_coa_pro' => 'Mit CoA Pro freischalten',
        'provenance_documentation' => 'Provenienzdokumentation',
        'condition_reports' => 'Zustandsberichte',
        'exhibition_history' => 'Ausstellungsgeschichte',
        'professional_pdf' => 'Professionelles PDF',
        'only_creator_can_issue' => 'Nur der Schöpfer kann Zertifikate ausstellen',

        // System für CoA-Traits-Vokabular
        'traits_management_title' => 'CoA-Traits verwalten',
        'traits_management_description' => 'Konfigurieren Sie die technischen Merkmale des Werks für das Authentizitätszertifikat',
        'status_configured' => 'Konfiguriert',
        'status_not_configured' => 'Nicht konfiguriert',
        'edit_traits' => 'Traits bearbeiten',
        'no_technique_selected' => 'Keine Technik ausgewählt',
        'no_materials_selected' => 'Keine Materialien ausgewählt',
        'no_support_selected' => 'Kein Träger ausgewählt',
        'custom' => 'benutzerdefiniert',
        'last_updated' => 'Zuletzt aktualisiert',
        'never_configured' => 'Nie konfiguriert',
        'clear_all' => 'Alles löschen',
        'saved' => 'Gespeichert',

        // Vokabular-Modal
        'modal_title' => 'CoA-Traits auswählen',
        'category_technique' => 'Technik',
        'category_materials' => 'Materialien',
        'category_support' => 'Träger',
        'search_placeholder' => 'Begriffe suchen...',
        'loading' => 'Laden...',
        'selected_items' => 'Ausgewählte Elemente',
        'no_items_selected' => 'Keine Elemente ausgewählt',
        'add_custom' => 'Benutzerdefiniert hinzufügen',
        'custom_term_placeholder' => 'Geben Sie einen benutzerdefinierten Begriff ein (max. 60 Zeichen)',
        'add' => 'Hinzufügen',
        'cancel' => 'Abbrechen',
        'items_selected' => 'Elemente ausgewählt',
        'confirm' => 'Bestätigen',

        // Vokabular-Komponenten
        'terms_available' => 'verfügbare Begriffe',
        'no_categories_available' => 'Keine Kategorien verfügbar',
        'no_categories_found' => 'Keine Vokabelkategorien gefunden.',
        'search_results' => 'Suchergebnisse',
        'results_for' => 'Für',
        'terms_found' => 'Begriffe gefunden',
        'results_found' => 'Ergebnisse gefunden',
        'no_results_found' => 'Keine Ergebnisse gefunden',
        'no_terms_match_search' => 'Keine Begriffe stimmen mit der Suche überein',
        'in_category' => 'in der Kategorie',
        'clear_search' => 'Suche löschen',
        'no_terms_available' => 'Keine Begriffe verfügbar',
        'no_terms_found_category' => 'Keine Begriffe für die Kategorie gefunden',
        'categories' => 'Kategorien',
        'back_to_start' => 'Zurück zum Start',
        'retry' => 'Erneut versuchen',
        'error' => 'Fehler',
        'unexpected_error' => 'Ein unerwarteter Fehler ist aufgetreten.',
        'exhibition_history' => 'Ausstellungsgeschichte',
        'professional_pdf_bundle' => 'Professionelles PDF-Paket',
        'only_creator_can_issue' => 'Nur der Schöpfer kann Zertifikate ausstellen',
        'public_verification' => 'Öffentliche Verifizierung',
        'verification_description' => 'Verifizieren Sie die Authentizität eines EGI-Authentizitätszertifikats',
        'verification_instructions' => 'Geben Sie die Seriennummer des Zertifikats ein, um dessen Authentizität zu überprüfen',
        'enter_serial' => 'Seriennummer eingeben',
        'serial_help' => 'Format: ABC-123-DEF (Buchstaben, Zahlen und Bindestriche)',
        'certificate_of_authenticity' => 'Authentizitätszertifikat',
        'public_verification_display' => 'Öffentliche Verifizierungsanzeige',
        'verified_authentic' => 'Zertifikat verifiziert und authentisch',
        'verified_at' => 'Verifiziert am',
        'artwork_information' => 'Informationen zum Kunstwerk',
        'artwork_title' => 'Titel des Kunstwerks',
        'creator' => 'Schöpfer',
        'description' => 'Beschreibung',
        'certificate_details' => 'Zertifikatdetails',
        'cryptographic_verification' => 'Kryptografische Verifizierung',
        'verify_again' => 'Erneut verifizieren',
        'print_certificate' => 'Zertifikat drucken',
        'share_verification' => 'Verifizierung teilen',
        'powered_by_florenceegi' => 'Betrieben von FlorenceEGI',
        'verification_timestamp' => 'Verifizierungszeitstempel',
        'link_copied' => 'Link in die Zwischenablage kopiert',
        'issuing' => 'Ausstellen...',
        'certificate_issued_successfully' => 'Zertifikat erfolgreich ausgestellt!',
        'error_issuing_certificate' => 'Fehler beim Ausstellen des Zertifikats: ',
        'reissue_certificate_confirm' => 'Dieses Zertifikat neu ausstellen? Eine neue Version wird erstellt.',
        'certificate_reissued_successfully' => 'Zertifikat erfolgreich neu ausgestellt!',
        'error_reissuing_certificate' => 'Fehler beim Neu-Ausstellen des Zertifikats: ',
        'revoke_certificate_confirm' => 'Dieses Zertifikat widerrufen? Diese Aktion kann nicht rückgängig gemacht werden.',
        'reason_for_revocation' => 'Grund für den Widerruf:',
        'certificate_revoked_successfully' => 'Zertifikat erfolgreich widerrufen!',
        'error_revoking_certificate' => 'Fehler beim Widerrufen des Zertifikats: ',
        'manage_certificate' => 'Zertifikat verwalten',
        'annex_management_coming_soon' => 'Anlagenverwaltung bald verfügbar!',
        'issue_certificate_description' => 'Stellen Sie ein Zertifikat aus, um einen Authentizitätsnachweis zu liefern und Pro-Funktionen freizuschalten',
        'serial' => 'Seriennummer',
        'pro_features' => 'Pro-Funktionen',
        'provenance_docs' => 'Provenienzdokumentation',
        'professional_pdf' => 'Professionelles PDF',
        'unlock_pro_features' => 'Pro-Funktionen freischalten',
        'reason_for' => 'Grund für',

        // QES-Signaturabzeichen
        'badge_author_signed' => 'Vom Autor signiert (QES)',
        'badge_inspector_signed' => 'Vom Inspektor signiert (QES)',
        'badge_integrity_ok' => 'Integrität verifiziert',

        // Standortschnittstelle (CoA)
        'issue_place' => 'Ausstellungsort',
        'location_placeholder' => 'Z. B. Florenz, Toskana, Italien',
        'save' => 'Speichern',
        'location_hint' => 'Verwenden Sie das Format „Stadt, Region/Provinz, Land“ (oder gleichwertig).',
        'location_required' => 'Der Standort ist erforderlich',
        'location_saved' => 'Standort gespeichert',
        'location_save_failed' => 'Fehler beim Speichern des Standorts',
        'location_updated' => 'Standort erfolgreich aktualisiert',

        // Gegensignatur des Inspektors (QES)
        'inspector_countersign' => 'Gegensignatur des Inspektors (QES)',
        'confirm_inspector_countersign' => 'Mit der Gegensignatur des Inspektors fortfahren?',
        'inspector_countersign_applied' => 'Gegensignatur des Inspektors angewendet',
        'operation_failed' => 'Operation fehlgeschlagen',
        'author_countersign' => 'Signatur des Autors (QES)',
        'confirm_author_countersign' => 'Mit der Signatur des Autors fortfahren?',
        'author_countersign_applied' => 'Signatur des Autors angewendet',
        'regenerate_pdf' => 'PDF regenerieren',
        'pdf_regenerated' => 'PDF regeneriert',
        'pdf_regenerate_failed' => 'Fehler beim Regenerieren des PDF',

        // Öffentliche Verifizierungsseite
        'public_verify' => [
            'signature' => 'Signatur',
            'author_signed' => 'Vom Autor signiert',
            'inspector_countersigned' => 'Vom Inspektor gegengezeichnet',
            'timestamp_tsa' => 'TSA-Zeitstempel',
            'qes' => 'QES',
            'wallet_signature' => 'Wallet-Signatur',
            'verify_signature' => 'Signatur verifizieren',
            'certificate_hash' => 'Zertifikat-Hash (SHA-256)',
            'pdf_hash' => 'PDF-Hash (SHA-256)',
            'copy_hash' => 'Hash kopieren',
            'copy_pdf_hash' => 'PDF-Hash kopieren',
            'hash_copied' => 'Hash in die Zwischenablage kopiert!',
            'pdf_hash_copied' => 'PDF-Hash in die Zwischenablage kopiert!',
            'qr_code_verify' => 'QR-Code-Verifizierung',
            'qr_code' => 'QR-Code',
            'scan_to_verify' => 'Zum Verifizieren scannen',
            'status' => 'Status',
            'valid' => 'Gültig',
            'incomplete' => 'Unvollständig',
            'revoked' => 'Widerrufen',

            // Kopfzeilen und Titel
            'certificate_title' => 'Authentizitätszertifikat',
            'public_verification_display' => 'Öffentliche Verifizierungsanzeige',
            'verified_authentic' => 'Zertifikat verifiziert und authentisch',
            'verified_at' => 'Verifiziert am',
            'serial_number' => 'Seriennummer',
            'certificate_not_ready' => 'Zertifikat nicht bereit',
            'certificate_revoked' => 'Zertifikat widerrufen',
            'certificate_not_valid' => 'Dieses Zertifikat ist nicht mehr gültig',
            'requires_coa_traits' => 'Erfordert CoA-Traits',
            'certificate_not_ready_generic' => 'Zertifikat nicht bereit - Generische Traits',

            // Informationen zum Kunstwerk
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

            // Zertifikatdetails
            'issue_date' => 'Ausstellungsdatum',
            'issued_by' => 'Ausgestellt von',
            'issue_location' => 'Ausstellungsort',
            'notes' => 'Notizen',

            // Professionelle Anlagen
            'professional_annexes' => 'Professionelle Anlagen',
            'provenance' => 'Provenienz',
            'condition_report' => 'Zustandsbericht',
            'exhibitions_publications' => 'Ausstellungen/Publikationen',
            'additional_photos' => 'Zusätzliche Fotos',

            // Informationen auf der Kette
            'on_chain_info' => 'Informationen auf der Kette',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Dossier-System - Dossier-System
    |--------------------------------------------------------------------------
    */
    'dossier' => [
        'title' => 'Bilddossier',
        'loading' => 'Dossier wird geladen...',
        'view_complete' => 'Vollständiges Bilddossier ansehen',
        'close' => 'Dossier schließen',

        // Informationen zum Kunstwerk
        'artwork_info' => 'Informationen zum Kunstwerk',
        'author' => 'Autor',
        'year' => 'Jahr',
        'internal_id' => 'Interne ID',

        // Informationen zum Dossier
        'dossier_info' => 'Dossier-Informationen',
        'images_count' => 'Bilder',
        'type' => 'Typ',
        'utility_gallery' => 'Nutzungsgalerie',

        // Galerie
        'gallery_title' => 'Bildergalerie',
        'image_number' => 'Bild :number',
        'image_of_total' => 'Bild :current von :total',

        // Status
        'no_utility_title' => 'Dossier nicht verfügbar',
        'no_utility_message' => 'Keine zusätzlichen Bilder für dieses Werk verfügbar.',
        'no_utility_description' => 'Das Dossier für zusätzliche Bilder wurde für dieses Werk noch nicht eingerichtet.',

        'no_images_title' => 'Keine Bilder verfügbar',
        'no_images_message' => 'Das Dossier existiert, enthält aber noch keine Bilder.',
        'no_images_description' => 'Zusätzliche Bilder werden in Zukunft vom Schöpfer des Werks hinzugefügt.',

        'error_title' => 'Fehler',
        'error_loading' => 'Fehler beim Laden des Dossiers',

        // Navigation
        'previous_image' => 'Vorheriges Bild',
        'next_image' => 'Nächstes Bild',
        'close_viewer' => 'Betrachter schließen',
        'of' => 'von',

        // Zoom-Steuerungen
        'zoom_help' => 'Verwenden Sie das Mausrad oder Touch, um zu zoomen • Ziehen, um zu bewegen',
        'zoom_in' => 'Heranzoomen',
        'zoom_out' => 'Herauszoomen',
        'zoom_reset' => 'Zoom zurücksetzen',
        'zoom_fit' => 'An Bildschirm anpassen',
    ],

];
