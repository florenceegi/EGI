<?php
// resources/lang/de/gdpr.php

return [
    /*
    |--------------------------------------------------------------------------
    | GDPR-Sprachzeilen
    |--------------------------------------------------------------------------
    |
    | Die folgenden Sprachzeilen werden für Funktionen im Zusammenhang mit der DSGVO verwendet.
    |
    */

    // Allgemein
    'gdpr' => 'DSGVO',
    'gdpr_center' => 'DSGVO-Datenkontrollzentrum',
    'dashboard' => 'Dashboard',
    'back_to_dashboard' => 'Zurück zum Dashboard',
    'save' => 'Speichern',
    'submit' => 'Absenden',
    'cancel' => 'Abbrechen',
    'continue' => 'Fortfahren',
    'loading' => 'Laden...',
    'success' => 'Erfolg',
    'error' => 'Fehler',
    'warning' => 'Warnung',
    'info' => 'Information',
    'enabled' => 'Aktiviert',
    'disabled' => 'Deaktiviert',
    'active' => 'Aktiv',
    'inactive' => 'Inaktiv',
    'pending' => 'Ausstehend',
    'completed' => 'Abgeschlossen',
    'failed' => 'Fehlgeschlagen',
    'processing' => 'In Bearbeitung',
    'retry' => 'Wiederholen',
    'required_field' => 'Pflichtfeld',
    'required_consent' => 'Erforderliche Zustimmung',
    'select_all_categories' => 'Alle Kategorien auswählen',
    'no_categories_selected' => 'Keine Kategorie ausgewählt',
    'compliance_badge' => 'Konformitätsabzeichen',

    'consent_types' => [
        'terms-of-service' => [
            'name' => 'Nutzungsbedingungen',
            'description' => 'Akzeptanz der Bedingungen für die Nutzung der Plattform.',
        ],
        'privacy-policy' => [
            'name' => 'Datenschutzrichtlinie',
            'description' => 'Anerkennung der Verarbeitung personenbezogener Daten.',
        ],
        'age-confirmation' => [
            'name' => 'Altersbestätigung',
            'description' => 'Bestätigung, mindestens 18 Jahre alt zu sein.',
        ],
        'analytics' => [
            'name' => 'Analyse und Plattformverbesserung',
            'description' => 'Helfen Sie uns, FlorenceEGI zu verbessern, indem Sie anonymisierte Nutzungsdaten teilen.',
        ],
        'marketing' => [
            'name' => 'Werbekommunikation',
            'description' => 'Erhalten Sie Updates zu neuen Funktionen, Veranstaltungen und Möglichkeiten.',
        ],
        'personalization' => [
            'name' => 'Personalisierung von Inhalten',
            'description' => 'Ermöglicht die Personalisierung von Inhalten und Empfehlungen.',
        ],
        'collaboration_participation' => [
            'name' => 'Teilnahme an Kollaborationen',
            'description' => 'Zustimmung zur Teilnahme an Sammlungskollaborationen, Datenaustausch und kooperativen Aktivitäten.',
        ],
        'purposes' => [
            'account_management' => 'Verwaltung des Benutzerkontos',
            'service_delivery'   => 'Bereitstellung der angeforderten Dienste',
            'legal_compliance'   => 'Rechtliche und regulatorische Konformität',
            'customer_support'   => 'Kundensupport und -hilfe',
        ],
    ],

    // Breadcrumbs
    'breadcrumb' => [
        'dashboard' => 'Dashboard',
        'gdpr' => 'Datenschutz und DSGVO',
    ],

    // Warnmeldungen
    'alerts' => [
        'success' => 'Operation erfolgreich abgeschlossen!',
        'error' => 'Fehler:',
        'warning' => 'Warnung:',
        'info' => 'Information:',
    ],

    // Menüelemente
    'menu' => [
        'gdpr_center' => 'DSGVO-Datenkontrollzentrum',
        'consent_management' => 'Zustimmungsverwaltung',
        'data_export' => 'Meine Daten exportieren',
        'processing_restrictions' => 'Datenverarbeitung einschränken',
        'delete_account' => 'Mein Konto löschen',
        'breach_report' => 'Datenschutzverletzung melden',
        'activity_log' => 'Protokoll meiner DSGVO-Aktivitäten',
        'privacy_policy' => 'Datenschutzrichtlinie',
    ],

    // Zustimmungsverwaltung
    'consent' => [
        'title' => 'Verwalten Sie Ihre Zustimmungspräferenzen',
        'description' => 'Kontrollieren Sie, wie Ihre Daten auf unserer Plattform verwendet werden. Sie können Ihre Präferenzen jederzeit aktualisieren.',
        'update_success' => 'Ihre Zustimmungspräferenzen wurden aktualisiert.',
        'update_error' => 'Beim Aktualisieren Ihrer Zustimmungspräferenzen ist ein Fehler aufgetreten. Bitte versuchen Sie es erneut.',
        'save_all' => 'Alle Präferenzen speichern',
        'last_updated' => 'Letzte Aktualisierung:',
        'never_updated' => 'Nie aktualisiert',
        'privacy_notice' => 'Datenschutzhinweis',
        'not_given' => 'Nicht erteilt',
        'given_at' => 'Erteilt am',
        'your_consents' => 'Ihre Zustimmungen',
        'subtitle' => 'Verwalten Sie Ihre Datenschutzpräferenzen und überprüfen Sie den Status Ihrer Zustimmungen.',
        'breadcrumb' => 'Zustimmungen',
        'history_title' => 'Zustimmungsverlauf',
        'back_to_consents' => 'Zurück zu Zustimmungen',
        'preferences_title' => 'Verwaltung der Zustimmungspräferenzen',
        'preferences_subtitle' => 'Konfigurieren Sie Ihre detaillierten Datenschutzpräferenzen',
        'preferences_breadcrumb' => 'Präferenzen',
        'preferences_info_title' => 'Granulare Zustimmungsverwaltung',
        'preferences_info_description' => 'Hier können Sie jeden Zustimmungstyp detailliert konfigurieren...',
        'required' => 'Erforderlich',
        'optional' => 'Optional',
        'toggle_label' => 'Aktivieren/Deaktivieren',
        'always_enabled' => 'Immer aktiv',
        'benefits_title' => 'Vorteile für Sie',
        'consequences_title' => 'Wenn Sie deaktivieren',
        'third_parties_title' => 'Drittanbieter-Dienste',
        'save_preferences' => 'Präferenzen speichern',
        'back_to_overview' => 'Zurück zur Übersicht',
        'never_updated' => 'Nie aktualisiert',

        // Zustimmungsdetails
        'given_at' => 'Erteilt am',
        'withdrawn_at' => 'Zurückgezogen am',
        'not_given' => 'Nicht erteilt',
        'method' => 'Methode',
        'version' => 'Version',
        'unknown_version' => 'Unbekannte Version',

        // Aktionen
        'withdraw' => 'Zustimmung zurückziehen',
        'withdraw_confirm' => 'Sind Sie sicher, dass Sie diese Zustimmung zurückziehen möchten? Diese Aktion kann einige Funktionen einschränken.',
        'renew' => 'Zustimmung erneuern',
        'view_history' => 'Verlauf anzeigen',

        // Leere Zustände
        'no_consents' => 'Keine Zustimmungen vorhanden',
        'no_consents_description' => 'Sie haben noch keine Zustimmungen zur Datenverarbeitung erteilt. Sie können Ihre Präferenzen mit dem Button unten verwalten.',

        // Präferenzverwaltung
        'manage_preferences' => 'Ihre Präferenzen verwalten',
        'update_preferences' => 'Datenschutzpräferenzen aktualisieren',

        // Zustimmungsstatus
        'status' => [
            'granted' => 'Erteilt',
            'denied' => 'Verweigert',
            'active' => 'Aktiv',
            'withdrawn' => 'Zurückgezogen',
            'expired' => 'Abgelaufen',
            'pending' => 'Ausstehend',
            'in_progress' => 'In Bearbeitung',
            'completed' => 'Abgeschlossen',
            'failed' => 'Fehlgeschlagen',
            'rejected' => 'Abgelehnt',
            'verification_required' => 'Verifizierung erforderlich',
            'cancelled' => 'Abgebrochen',
        ],

        // Dashboard-Zusammenfassung
        'summary' => [
            'active' => 'Aktive Zustimmungen',
            'total' => 'Gesamtzustimmungen',
            'compliance' => 'Konformitätsscore',
        ],

        // Zustimmungsmethoden
        'methods' => [
            'web' => 'Weboberfläche',
            'api' => 'API',
            'import' => 'Import',
            'admin' => 'Administrator',
        ],

        // Zustimmungszwecke
        'purposes' => [
            'functional' => 'Funktionale Zustimmungen',
            'analytics' => 'Analytische Zustimmungen',
            'marketing' => 'Marketing-Zustimmungen',
            'profiling' => 'Profiling-Zustimmungen',
            'platform-services' => 'Plattformdienste',
            'terms-of-service' => 'Nutzungsbedingungen',
            'privacy-policy' => 'Datenschutzrichtlinie',
            'age-confirmation' => 'Altersbestätigung',
            'personalization' => 'Personalisierung von Inhalten',
            'allow-personal-data-processing' => 'Verarbeitung personenbezogener Daten erlauben',
            'collaboration_participation' => 'Teilnahme an Kollaborationen',
        ],

        // Zustimmungsbeschreibungen
        'descriptions' => [
            'functional' => 'Notwendig für den grundlegenden Betrieb der Plattform und die Bereitstellung der angeforderten Dienste.',
            'analytics' => 'Wird verwendet, um die Nutzung der Website zu analysieren und die Benutzererfahrung zu verbessern.',
            'marketing' => 'Wird verwendet, um Ihnen Werbemitteilungen und personalisierte Angebote zu senden.',
            'profiling' => 'Wird verwendet, um personalisierte Profile zu erstellen und relevante Inhalte vorzuschlagen.',
            'platform-services' => 'Zustimmungen, die für die Kontoverwaltung, Sicherheit und den Kundensupport erforderlich sind.',
            'terms-of-service' => 'Akzeptanz der Nutzungsbedingungen für die Nutzung der Plattform.',
            'privacy-policy' => 'Akzeptanz unserer Datenschutzrichtlinie und der Verarbeitung personenbezogener Daten.',
            'age-confirmation' => 'Bestätigung, dass Sie das gesetzliche Mindestalter für die Nutzung der Plattform haben.',
            'personalization' => 'Ermöglicht die Personalisierung von Inhalten und Empfehlungen basierend auf Ihren Präferenzen.',
            'allow-personal-data-processing' => 'Ermöglicht die Verarbeitung Ihrer personenbezogenen Daten, um unsere Dienste zu verbessern und Ihnen eine personalisierte Erfahrung zu bieten.',
            'collaboration_participation' => 'Ermöglicht die Teilnahme an kollaborativen Projekten und gemeinsamen Aktivitäten mit anderen Plattformnutzern.',
        ],

        'essential' => [
            'label' => 'Essentielle Cookies',
            'description' => 'Diese Cookies sind für den Betrieb der Website erforderlich und können in unseren Systemen nicht deaktiviert werden.',
        ],
        'functional' => [
            'label' => 'Funktionale Cookies',
            'description' => 'Diese Cookies ermöglichen es der Website, erweiterte Funktionen und Personalisierung bereitzustellen.',
        ],
        'analytics' => [
            'label' => 'Analytische Cookies',
            'description' => 'Diese Cookies ermöglichen es uns, Besuche und Verkehrsquellen zu zählen, um die Leistung unserer Website zu messen und zu verbessern.',
        ],
        'marketing' => [
            'label' => 'Marketing-Cookies',
            'description' => 'Diese Cookies können über unsere Website von unseren Werbepartnern gesetzt werden, um ein Profil Ihrer Interessen zu erstellen.',
        ],
        'profiling' => [
            'label' => 'Profiling',
            'description' => 'Wir verwenden Profiling, um Ihre Präferenzen besser zu verstehen und unsere Dienste entsprechend Ihren Bedürfnissen zu personalisieren.',
        ],

        'allow_personal_data_processing' => [
            'label' => 'Zustimmung zur Verarbeitung personenbezogener Daten',
            'description' => 'Ermöglicht die Verarbeitung Ihrer personenbezogenen Daten, um unsere Dienste zu verbessern und Ihnen eine personalisierte Erfahrung zu bieten.',
        ],

        'saving_consent' => 'Speichern...',
        'consent_saved' => 'Gespeichert',
        'saving_all_consents' => 'Alle Präferenzen werden gespeichert...',
        'all_consents_saved' => 'Alle Zustimmungspräferenzen wurden erfolgreich gespeichert.',
        'all_consents_save_error' => 'Beim Speichern aller Zustimmungspräferenzen ist ein Fehler aufgetreten.',
        'consent_save_error' => 'Beim Speichern dieser Zustimmungspräferenz ist ein Fehler aufgetreten.',

        // Verarbeitungszwecke
        'processing_purposes' => [
            'functional' => 'Wesentliche Plattformoperationen: Authentifizierung, Sicherheit, Dienstleistungserbringung, Speicherung von Benutzerpräferenzen',
            'analytics' => 'Plattformverbesserung: Nutzungsanalyse, Leistungsüberwachung, Optimierung der Benutzererfahrung',
            'marketing' => 'Kommunikation: Newsletter, Produkt-Updates, Werbeangebote, Veranstaltungsbenachrichtigungen',
            'profiling' => 'Personalisierung: Inhaltsempfehlungen, Analyse des Benutzerverhaltens, gezielte Vorschläge',
        ],

        // Aufbewahrungsfristen
        'retention_periods' => [
            'functional' => 'Kontodauer + 1 Jahr für rechtliche Konformität',
            'analytics' => '2 Jahre seit der letzten Aktivität',
            'marketing' => '3 Jahre seit der letzten Interaktion oder dem Widerruf der Zustimmung',
            'profiling' => '1 Jahr seit der letzten Aktivität oder dem Widerruf der Zustimmung',
        ],

        // Vorteile für den Nutzer
        'user_benefits' => [
            'functional' => [
                'Sicherer Zugang zu Ihrem Konto',
                'Personalisierte Benutzereinstellungen',
                'Zuverlässige Plattformleistung',
                'Schutz vor Betrug und Missbrauch',
            ],
            'analytics' => [
                'Verbesserte Plattformleistung',
                'Optimiertes Benutzererlebnis-Design',
                'Schnellere Ladezeiten',
                'Entwicklung verbesserter Funktionen',
            ],
            'marketing' => [
                'Relevante Produkt-Updates',
                'Exklusive Angebote und Werbeaktionen',
                'Einladungen zu Veranstaltungen und Ankündigungen',
                'Bildungsinhalte und Vorschläge',
            ],
            'profiling' => [
                'Personalisierte Inhaltsempfehlungen',
                'Maßgeschneiderte Benutzererfahrung',
                'Relevante Projektvorschläge',
                'Personalisierte Dashboard- und Funktionsanpassungen',
            ],
        ],

        // Drittanbieter-Dienste
        'third_parties' => [
            'functional' => [
                'CDN-Anbieter (Verteilung statischer Inhalte)',
                'Sicherheitsdienste (Betrugsprävention)',
                'Infrastrukturanbieter (Hosting)',
            ],
            'analytics' => [
                'Analyseplattformen (anonymisierte Nutzungsdaten)',
                'Leistungsüberwachungsdienste',
                'Fehlerverfolgungsdienste',
            ],
            'marketing' => [
                'E-Mail-Dienstleister',
                'Marketing-Automatisierungsplattformen',
                'Social-Media-Plattformen (für Werbung)',
            ],
            'profiling' => [
                'Empfehlungsmaschinen',
                'Verhaltensanalysedienste',
                'Inhaltspersonalisierungsplattformen',
            ],
        ],

        // Konsequenzen des Widerrufs
        'withdrawal_consequences' => [
            'functional' => [
                'Kann nicht widerrufen werden - essenziell für den Plattformbetrieb',
                'Der Kontzugang wäre beeinträchtigt',
                'Sicherheitsfunktionen würden deaktiviert',
            ],
            'analytics' => [
                'Plattformverbesserungen könnten Ihre Nutzungsmuster nicht widerspiegeln',
                'Generische Erfahrung statt optimierter Leistung',
                'Kein Einfluss auf die Hauptfunktionen',
            ],
            'marketing' => [
                'Keine Werbe-E-Mails oder Updates',
                'Sie könnten wichtige Ankündigungen verpassen',
                'Kein Einfluss auf die Plattformfunktionalität',
                'Kann jederzeit reaktiviert werden',
            ],
            'profiling' => [
                'Generische Inhalte statt personalisierter Empfehlungen',
                'Standard-Dashboard-Layout',
                'Weniger relevante Projektvorschläge',
                'Kein Einfluss auf die Hauptfunktionen der Plattform',
            ],
        ],
    ],

    // Datenexport
    'export' => [
        'title' => 'Ihre Daten exportieren',
        'subtitle' => 'Fordern Sie eine vollständige Kopie Ihrer personenbezogenen Daten in einem portablen Format an',
        'description' => 'Fordern Sie eine Kopie Ihrer personenbezogenen Daten an. Die Verarbeitung kann einige Minuten dauern.',

        // Datenkategorien
        'select_data_categories' => 'Wählen Sie die zu exportierenden Datenkategorien aus',
        'categories' => [
            'profile' => 'Profilinformationen',
            'account' => 'Kontodetails',
            'preferences' => 'Präferenzen und Einstellungen',
            'activity' => 'Aktivitätsverlauf',
            'consents' => 'Zustimmungsverlauf',
            'collections' => 'Sammlungen und NFTs',
            'purchases' => 'Käufe und Transaktionen',
            'comments' => 'Kommentare und Bewertungen',
            'messages' => 'Nachrichten und Kommunikationen',
            'biography' => 'Biografien und Inhalte',
        ],

        // Beschreibungen der Kategorien
        'category_descriptions' => [
            'profile' => 'Persönliche Daten, Kontaktinformationen, Profilbild und persönliche Beschreibungen',
            'account' => 'Kontodetails, Sicherheitseinstellungen, Anmeldeverlauf und Änderungen',
            'preferences' => 'Benutzerpräferenzen, Datenschutzeinstellungen, personalisierte Konfigurationen',
            'activity' => 'Navigationsverlauf, Interaktionen, Aufrufe und Plattformnutzung',
            'consents' => 'Zustimmungsverlauf, Präferenzänderungen, DSGVO-Prüfprotokoll',
            'collections' => 'Erstellte NFT-Sammlungen, Metadaten, geistiges Eigentum und Vermögenswerte',
            'purchases' => 'Transaktionen, Käufe, Rechnungen, Zahlungsmethoden und Bestellverlauf',
            'comments' => 'Kommentare, Bewertungen, Feedback und Rezensionen auf der Plattform',
            'messages' => 'Private Nachrichten, Kommunikationen, Benachrichtigungen und Gespräche',
            'biography' => 'Erstellte Biografien, Kapitel, Zeitachsen, Medien und narrative Inhalte',
        ],

        // Exportformate
        'select_format' => 'Exportformat auswählen',
        'formats' => [
            'json' => 'JSON - Strukturiertes Datenformat',
            'csv' => 'CSV - Kompatibel mit Tabellenkalkulationen',
            'pdf' => 'PDF - Lesbares Dokument',
        ],

        // Beschreibungen der Formate
        'format_descriptions' => [
            'json' => 'Strukturiertes Datenformat, ideal für Entwickler und Integrationen. Bewahrt die vollständige Datenstruktur.',
            'csv' => 'Format kompatibel mit Excel und Google Sheets. Perfekt für Datenanalyse und -manipulation.',
            'pdf' => 'Lesbares und druckbares Dokument. Ideal für Archivierung und Weitergabe.',
        ],

        // Zusätzliche Optionen
        'additional_options' => 'Zusätzliche Optionen',
        'include_metadata' => 'Technische Metadaten einschließen',
        'metadata_description' => 'Enthält technische Informationen wie Zeitstempel, IP-Adressen, Versionen und Prüfprotokolle.',
        'include_audit_trail' => 'Vollständiges Aktivitätsprotokoll einschließen',
        'audit_trail_description' => 'Enthält den vollständigen Verlauf aller Änderungen und DSGVO-Aktivitäten.',

        // Aktionen
        'request_export' => 'Datenexport anfordern',
        'request_success' => 'Exportanfrage erfolgreich gesendet. Sie erhalten eine Benachrichtigung nach Abschluss.',
        'request_error' => 'Beim Senden der Anfrage ist ein Fehler aufgetreten. Bitte versuchen Sie es erneut.',

        // Exportverlauf
        'history_title' => 'Exportverlauf',
        'no_exports' => 'Keine Exporte vorhanden',
        'no_exports_description' => 'Sie haben noch keinen Export Ihrer Daten angefordert. Verwenden Sie das obige Formular, um einen anzufordern.',

        // Details der Exportelemente
        'export_format' => 'Export {format}',
        'requested_on' => 'Angefordert am',
        'completed_on' => 'Abgeschlossen am',
        'expires_on' => 'Läuft ab am',
        'file_size' => 'Größe',
        'download' => 'Herunterladen',
        'download_export' => 'Export herunterladen',

        // Status
        'status' => [
            'pending' => 'Ausstehend',
            'processing' => 'In Bearbeitung',
            'completed' => 'Abgeschlossen',
            'failed' => 'Fehlgeschlagen',
            'expired' => 'Abgelaufen',
        ],

        // Frequenzbegrenzung
        'rate_limit_title' => 'Exportlimit erreicht',
        'rate_limit_message' => 'Sie haben das maximale Limit von {max} Exporten für heute erreicht. Versuchen Sie es morgen erneut.',
        'last_export_date' => 'Letzter Export: {date}',

        // Validierung
        'select_at_least_one_category' => 'Wählen Sie mindestens eine Datenkategorie zum Exportieren aus.',

        // Legacy-Unterstützung
        'request_button' => 'Datenexport anfordern',
        'format' => 'Exportformat',
        'format_json' => 'JSON (empfohlen für Entwickler)',
        'format_csv' => 'CSV (kompatibel mit Tabellenkalkulationen)',
        'format_pdf' => 'PDF (lesbares Dokument)',
        'include_timestamps' => 'Zeitstempel einschließen',
        'password_protection' => 'Export mit Passwort schützen',
        'password' => 'Exportpasswort',
        'confirm_password' => 'Passwort bestätigen',
        'data_categories' => 'Zu exportierende Datenkategorien',
        'recent_exports' => 'Aktuelle Exporte',
        'no_recent_exports' => 'Sie haben keine aktuellen Exporte.',
        'export_status' => 'Exportstatus',
        'export_date' => 'Exportdatum',
        'export_size' => 'Exportgröße',
        'export_id' => 'Export-ID',
        'export_preparing' => 'Ihr Datenexport wird vorbereitet...',
        'export_queued' => 'Ihr Export steht in der Warteschlange und beginnt bald...',
        'export_processing' => 'Ihr Datenexport wird verarbeitet...',
        'export_ready' => 'Ihr Datenexport ist zum Download bereit.',
        'export_failed' => 'Ihr Datenexport ist fehlgeschlagen.',
        'export_failed_details' => 'Beim Verarbeiten Ihres Datenexports ist ein Fehler aufgetreten. Versuchen Sie es erneut oder kontaktieren Sie den Support.',
        'export_unknown_status' => 'Unbekannter Exportstatus.',
        'check_status' => 'Status prüfen',
        'retry_export' => 'Export erneut versuchen',
        'export_download_error' => 'Beim Herunterladen Ihres Exports ist ein Fehler aufgetreten.',
        'export_status_error' => 'Fehler beim Überprüfen des Exportstatus.',
        'limit_reached' => 'Sie haben die maximale Anzahl an erlaubten Exporten pro Tag erreicht.',
        'existing_in_progress' => 'Sie haben bereits einen Export in Bearbeitung. Warten Sie, bis dieser abgeschlossen ist.',
    ],

    // Verarbeitungseinschränkungen
    'restriction' => [
        'title' => 'Datenverarbeitung einschränken',
        'description' => 'Sie können unter bestimmten Umständen beantragen, wie wir Ihre Daten verarbeiten, einzuschränken.',
        'active_restrictions' => 'Aktive Einschränkungen',
        'no_active_restrictions' => 'Sie haben keine aktiven Verarbeitungseinschränkungen.',
        'request_new' => 'Neue Einschränkung anfordern',
        'restriction_type' => 'Art der Einschränkung',
        'restriction_reason' => 'Grund für die Einschränkung',
        'data_categories' => 'Datenkategorien',
        'notes' => 'Zusätzliche Notizen',
        'notes_placeholder' => 'Geben Sie zusätzliche Details an, um uns bei der Bearbeitung Ihrer Anfrage zu helfen...',
        'submit_button' => 'Einschränkungsanfrage senden',
        'remove_button' => 'Einschränkung entfernen',
        'processing_restriction_success' => 'Ihre Anfrage zur Einschränkung der Verarbeitung wurde gesendet.',
        'processing_restriction_failed' => 'Beim Senden Ihrer Anfrage zur Einschränkung der Verarbeitung ist ein Fehler aufgetreten.',
        'processing_restriction_system_error' => 'Beim Verarbeiten Ihrer Anfrage ist ein Systemfehler aufgetreten.',
        'processing_restriction_removed' => 'Die Verarbeitungseinschränkung wurde entfernt.',
        'processing_restriction_removal_failed' => 'Beim Entfernen der Verarbeitungseinschränkung ist ein Fehler aufgetreten.',
        'unauthorized_action' => 'Sie sind nicht berechtigt, diese Aktion durchzuführen.',
        'date_submitted' => 'Eingereicht am',
        'expiry_date' => 'Läuft ab am',
        'never_expires' => 'Läuft nie ab',
        'status' => 'Status',
        'limit_reached' => 'Sie haben die maximale Anzahl an erlaubten aktiven Einschränkungen erreicht.',
        'categories' => [
            'profile' => 'Profilinformationen',
            'activity' => 'Aktivitätsverfolgung',
            'preferences' => 'Präferenzen und Einstellungen',
            'collections' => 'Sammlungen und Inhalte',
            'purchases' => 'Käufe und Transaktionen',
            'comments' => 'Kommentare und Bewertungen',
            'messages' => 'Nachrichten und Kommunikationen',
        ],
        'types' => [
            'processing' => 'Gesamte Verarbeitung einschränken',
            'automated_decisions' => 'Automatisierte Entscheidungen einschränken',
            'marketing' => 'Marketing-Verarbeitung einschränken',
            'analytics' => 'Analytische Verarbeitung einschränken',
            'third_party' => 'Weitergabe an Dritte einschränken',
            'profiling' => 'Profiling einschränken',
            'data_sharing' => 'Datenaustausch einschränken',
            'removed' => 'Einschränkung entfernen',
            'all' => 'Gesamte Verarbeitung einschränken',
        ],
        'reasons' => [
            'accuracy_dispute' => 'Ich bestreite die Richtigkeit meiner Daten',
            'processing_unlawful' => 'Die Verarbeitung ist unrechtmäßig',
            'no_longer_needed' => 'Sie benötigen meine Daten nicht mehr, aber ich benötige sie für rechtliche Ansprüche',
            'objection_pending' => 'Ich habe der Verarbeitung widersprochen und warte auf eine Überprüfung',
            'legitimate_interest' => 'Zwingende legitime Gründe',
            'legal_claims' => 'Zur Verteidigung rechtlicher Ansprüche',
            'other' => 'Anderer Grund (bitte in den Notizen angeben)',
        ],
        'descriptions' => [
            'processing' => 'Einschränkt die Verarbeitung Ihrer personenbezogenen Daten, während Ihre Anfrage überprüft wird.',
            'automated_decisions' => 'Einschränkt automatisierte Entscheidungen, die Ihre Rechte beeinflussen können.',
            'marketing' => 'Einschränkt die Verarbeitung Ihrer Daten für Direktmarketing-Zwecke.',
            'analytics' => 'Einschränkt die Verarbeitung Ihrer Daten für analytische und Überwachungszwecke.',
            'third_party' => 'Einschränkt die Weitergabe Ihrer Daten an Dritte.',
            'profiling' => 'Einschränkt das Profiling Ihrer personenbezogenen Daten.',
            'data_sharing' => 'Einschränkt den Austausch Ihrer Daten mit anderen Diensten oder Plattformen.',
            'all' => 'Einschränkt alle Formen der Verarbeitung Ihrer personenbezogenen Daten.',
        ],
    ],

    // Kontolöschung
    'deletion' => [
        'title' => 'Mein Konto löschen',
        'description' => 'Dies startet den Prozess zur Löschung Ihres Kontos und aller zugehörigen Daten.',
        'warning' => 'Warnung: Die Kontolöschung ist dauerhaft und kann nicht rückgängig gemacht werden.',
        'processing_delay' => 'Ihr Konto ist für die Löschung in :days Tagen geplant.',
        'confirm_deletion' => 'Ich verstehe, dass diese Aktion dauerhaft ist und nicht rückgängig gemacht werden kann.',
        'password_confirmation' => 'Geben Sie Ihr Passwort zur Bestätigung ein',
        'reason' => 'Grund für die Löschung (optional)',
        'additional_comments' => 'Zusätzliche Kommentare (optional)',
        'submit_button' => 'Kontolöschung anfordern',
        'request_submitted' => 'Ihre Anfrage zur Kontolöschung wurde gesendet.',
        'request_error' => 'Beim Senden Ihrer Anfrage zur Kontolöschung ist ein Fehler aufgetreten.',
        'pending_deletion' => 'Ihr Konto ist für die Löschung am :date geplant.',
        'cancel_deletion' => 'Anfrage zur Kontolöschung abbrechen',
        'cancellation_success' => 'Ihre Anfrage zur Kontolöschung wurde abgebrochen.',
        'cancellation_error' => 'Beim Abbrechen Ihrer Anfrage zur Kontolöschung ist ein Fehler aufgetreten.',
        'reasons' => [
            'no_longer_needed' => 'Ich benötige diesen Dienst nicht mehr',
            'privacy_concerns' => 'Datenschutzbedenken',
            'moving_to_competitor' => 'Wechsel zu einem anderen Dienst',
            'unhappy_with_service' => 'Unzufrieden mit dem Dienst',
            'other' => 'Anderer Grund',
        ],
        'confirmation_email' => [
            'subject' => 'Bestätigung der Kontolöschungsanfrage',
            'line1' => 'Wir haben Ihre Anfrage zur Löschung Ihres Kontos erhalten.',
            'line2' => 'Ihr Konto ist für die Löschung am :date geplant.',
            'line3' => 'Wenn Sie diese Aktion nicht angefordert haben, kontaktieren Sie uns sofort.',
        ],
        'data_retention_notice' => 'Bitte beachten Sie, dass einige anonymisierte Daten zu rechtlichen und analytischen Zwecken aufbewahrt werden können.',
        'blockchain_data_notice' => 'Daten, die auf der Blockchain gespeichert sind, können aufgrund der unveränderlichen Natur der Technologie nicht vollständig gelöscht werden.',
    ],

    // Meldung von Datenschutzverletzungen
    'breach' => [
        'title' => 'Datenschutzverletzung melden',
        'description' => 'Wenn Sie glauben, dass es zu einer Verletzung Ihrer personenbezogenen Daten gekommen ist, melden Sie dies hier.',
        'reporter_name' => 'Ihr Name',
        'reporter_email' => 'Ihre E-Mail',
        'incident_date' => 'Wann ist der Vorfall passiert?',
        'breach_description' => 'Beschreiben Sie die mögliche Verletzung',
        'breach_description_placeholder' => 'Geben Sie so viele Details wie möglich über die mögliche Datenschutzverletzung an...',
        'affected_data' => 'Welche Daten wurden Ihrer Meinung nach kompromittiert?',
        'affected_data_placeholder' => 'Zum Beispiel persönliche Informationen, Finanzdaten usw.',
        'discovery_method' => 'Wie haben Sie diese mögliche Verletzung entdeckt?',
        'supporting_evidence' => 'Unterstützende Beweise (optional)',
        'upload_evidence' => 'Beweise hochladen',
        'file_types' => 'Akzeptierte Dateitypen: PDF, JPG, JPEG, PNG, TXT, DOC, DOCX',
        'max_file_size' => 'Maximale Dateigröße: 10 MB',
        'consent_to_contact' => 'Ich stimme zu, bezüglich dieses Berichts kontaktiert zu werden',
        'submit_button' => 'Verletzungsbericht senden',
        'report_submitted' => 'Ihr Verletzungsbericht wurde gesendet.',
        'report_error' => 'Beim Senden Ihres Verletzungsberichts ist ein Fehler aufgetreten.',
        'thank_you' => 'Vielen Dank für Ihren Bericht',
        'thank_you_message' => 'Vielen Dank, dass Sie diese mögliche Verletzung gemeldet haben. Unser Datenschutzteam wird die Angelegenheit untersuchen und könnte Sie für weitere Informationen kontaktieren.',
        'breach_description_min' => 'Geben Sie mindestens 20 Zeichen ein, um die mögliche Verletzung zu beschreiben.',
    ],

    // Aktivitätsprotokoll
    'activity' => [
        'title' => 'Protokoll meiner DSGVO-Aktivitäten',
        'description' => 'Sehen Sie ein Protokoll aller Ihrer Aktivitäten und Anfragen im Zusammenhang mit der DSGVO.',
        'no_activities' => 'Keine Aktivitäten gefunden.',
        'date' => 'Datum',
        'activity' => 'Aktivität',
        'details' => 'Details',
        'ip_address' => 'IP-Adresse',
        'user_agent' => 'Benutzeragent',
        'download_log' => 'Aktivitätsprotokoll herunterladen',
        'filter' => 'Aktivitäten filtern',
        'filter_all' => 'Alle Aktivitäten',
        'filter_consent' => 'Zustimmungsaktivitäten',
        'filter_export' => 'Datenexportaktivitäten',
        'filter_restriction' => 'Verarbeitungseinschränkungsaktivitäten',
        'filter_deletion' => 'Kontolöschungsaktivitäten',
        'types' => [
            'consent_updated' => 'Zustimmungspräferenzen aktualisiert',
            'data_export_requested' => 'Datenexport angefordert',
            'data_export_completed' => 'Datenexport abgeschlossen',
            'data_export_downloaded' => 'Datenexport heruntergeladen',
            'processing_restricted' => 'Verarbeitungseinschränkung angefordert',
            'processing_restriction_removed' => 'Verarbeitungseinschränkung entfernt',
            'account_deletion_requested' => 'Kontolöschung angefordert',
            'account_deletion_cancelled' => 'Kontolöschung abgebrochen',
            'account_deletion_completed' => 'Kontolöschung abgeschlossen',
            'breach_reported' => 'Datenschutzverletzung gemeldet',
        ],
    ],

    // Validierung
    'validation' => [
        'consents_required' => 'Zustimmungspräferenzen sind erforderlich.',
        'consents_format' => 'Das Format der Zustimmungspräferenzen ist ungültig.',
        'consent_value_required' => 'Der Zustimmungswert ist erforderlich.',
        'consent_value_boolean' => 'Der Zustimmungswert muss ein Boolean sein.',
        'format_required' => 'Das Exportformat ist erforderlich.',
        'data_categories_required' => 'Mindestens eine Datenkategorie muss ausgewählt werden.',
        'data_categories_format' => 'Das Format der Datenkategorien ist ungültig.',
        'data_categories_min' => 'Mindestens eine Datenkategorie muss ausgewählt werden.',
        'data_categories_distinct' => 'Die Datenkategorien müssen eindeutig sein.',
        'export_password_required' => 'Das Passwort ist erforderlich, wenn der Passwortschutz aktiviert ist.',
        'export_password_min' => 'Das Passwort muss mindestens 8 Zeichen lang sein.',
        'restriction_type_required' => 'Die Art der Einschränkung ist erforderlich.',
        'restriction_reason_required' => 'Der Grund für die Einschränkung ist erforderlich.',
        'notes_max' => 'Die Notizen dürfen 500 Zeichen nicht überschreiten.',
        'reporter_name_required' => 'Ihr Name ist erforderlich.',
        'reporter_email_required' => 'Ihre E-Mail ist erforderlich.',
        'reporter_email_format' => 'Geben Sie eine gültige E-Mail-Adresse ein.',
        'incident_date_required' => 'Das Datum des Vorfalls ist erforderlich.',
        'incident_date_format' => 'Das Datum des Vorfalls muss ein gültiges Datum sein.',
        'incident_date_past' => 'Das Datum des Vorfalls muss in der Vergangenheit oder heute liegen.',
        'breach_description_required' => 'Die Beschreibung der Verletzung ist erforderlich.',
        'breach_description_min' => 'Die Beschreibung der Verletzung muss mindestens 20 Zeichen lang sein.',
        'affected_data_required' => 'Informationen über die kompromittierten Daten sind erforderlich.',
        'discovery_method_required' => 'Die Entdeckungsmethode ist erforderlich.',
        'supporting_evidence_format' => 'Die Beweise müssen im Format PDF, JPG, JPEG, PNG, TXT, DOC oder DOCX vorliegen.',
        'supporting_evidence_max' => 'Die Beweisdatei darf 10 MB nicht überschreiten.',
        'consent_to_contact_required' => 'Die Zustimmung zur Kontaktaufnahme ist erforderlich.',
        'consent_to_contact_accepted' => 'Die Zustimmung zur Kontaktaufnahme muss akzeptiert werden.',
        'required_consent_message' => 'Diese Zustimmung ist für die Nutzung der Plattform erforderlich.',
        'confirm_deletion_required' => 'Sie müssen bestätigen, dass Sie die Konsequenzen der Kontolöschung verstehen.',
        'form_error_title' => 'Korrigieren Sie die folgenden Fehler',
        'form_error_message' => 'Es gibt einen oder mehrere Fehler im Formular, die korrigiert werden müssen.',
    ],

    // Fehlermeldungen
    'errors' => [
        'general' => 'Ein unerwarteter Fehler ist aufgetreten.',
        'unauthorized' => 'Sie sind nicht berechtigt, diese Aktion durchzuführen.',
        'forbidden' => 'Diese Aktion ist verboten.',
        'not_found' => 'Die angeforderte Ressource wurde nicht gefunden.',
        'validation_failed' => 'Die gesendeten Daten sind ungültig.',
        'rate_limited' => 'Zu viele Anfragen. Versuchen Sie es später erneut.',
        'service_unavailable' => 'Der Dienst ist derzeit nicht verfügbar. Versuchen Sie es später erneut.',
    ],

    'requests' => [
        'types' => [
            'consent_update' => 'Anfrage zur Zustimmungsaktualisierung gesendet.',
            'data_export' => 'Anfrage zum Datenexport gesendet.',
            'processing_restriction' => 'Anfrage zur Verarbeitungseinschränkung gesendet.',
            'account_deletion' => 'Anfrage zur Kontolöschung gesendet.',
            'breach_report' => 'Datenschutzverletzungsbericht gesendet.',
            'erasure' => 'Anfrage zur Datenlöschung gesendet.',
            'access' => 'Anfrage zum Datenzugriff gesendet.',
            'rectification' => 'Anfrage zur Datenberichtigung gesendet.',
            'objection' => 'Anfrage zur Einspruchsverarbeitung gesendet.',
            'restriction' => 'Anfrage zur Verarbeitungseinschränkung gesendet.',
            'portability' => 'Anfrage zur Datenportabilität gesendet.',
        ],
    ],

    // Version Information
    'current_version' => 'Aktuelle Version',
    'version' => 'Version: 1.0',
    'effective_date' => 'Gültig ab: 30. September 2025',
    'last_updated' => 'Zuletzt aktualisiert: 30. September 2025, 17:41',

    // Actions
    'download_pdf' => 'PDF herunterladen',
    'print' => 'Drucken',

    'modal' => [
        'clarification' => [
            'title' => 'Klärung erforderlich',
            'explanation' => 'Um Ihre Sicherheit zu gewährleisten, müssen wir den Grund für Ihre Aktion verstehen:',
        ],
        'revoke_button_text' => 'Ich habe meine Meinung geändert',
        'revoke_description' => 'Sie möchten lediglich die zuvor erteilte Zustimmung zurückziehen.',
        'disavow_button_text' => 'Ich erkenne diese Aktion nicht an',
        'disavow_description' => 'Sie haben diese Zustimmung niemals erteilt (mögliches Sicherheitsproblem).',

        'confirmation' => [
            'title' => 'Sicherheitsprotokoll bestätigen',
            'warning' => 'Diese Aktion aktiviert ein Sicherheitsprotokoll, das Folgendes umfasst:',
        ],
        'confirm_disavow' => 'Ja, Sicherheitsprotokoll aktivieren',
        'final_warning' => 'Fahren Sie nur fort, wenn Sie sicher sind, dass Sie diese Aktion niemals autorisiert haben.',

        'consequences' => [
            'consent_revocation' => 'Sofortiger Widerruf der Zustimmung',
            'security_notification' => 'Benachrichtigung des Sicherheitsteams',
            'account_review' => 'Mögliche zusätzliche Kontoüberprüfungen',
            'email_confirmation' => 'Bestätigungs-E-Mail mit Anweisungen',
        ],

        'security' => [
            'title' => 'Sicherheitsprotokoll aktiviert',
            'understood' => 'Verstanden',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | DSGVO-Benachrichtigungsabschnitt
    |--------------------------------------------------------------------------
    | Aus `notification.php` verschoben zur Zentralisierung.
    */
    'notifications' => [
        'acknowledged' => 'Bestätigung registriert.',
        'consent_updated' => [
            'title' => 'Datenschutzpräferenzen aktualisiert',
            'content' => 'Ihre Zustimmungspräferenzen wurden erfolgreich aktualisiert.',
        ],
        'data_exported' => [
            'title' => 'Ihr Datenexport ist bereit',
            'content' => 'Ihre Anfrage zum Datenexport wurde verarbeitet. Sie können die Datei über den bereitgestellten Link herunterladen.',
        ],
        'processing_restricted' => [
            'title' => 'Verarbeitungseinschränkung angewendet',
            'content' => 'Wir haben Ihre Anfrage zur Einschränkung der Datenverarbeitung für die Kategorie :type erfolgreich angewendet.',
        ],
        'account_deletion_requested' => [
            'title' => 'Anfrage zur Kontolöschung erhalten',
            'content' => 'Wir haben Ihre Anfrage zur Löschung Ihres Kontos erhalten. Der Prozess wird in :days Tagen abgeschlossen. In diesem Zeitraum können Sie die Anfrage durch erneutes Einloggen noch abbrechen.',
        ],
        'account_deletion_processed' => [
            'title' => 'Konto erfolgreich gelöscht',
            'content' => 'Wie angefordert, wurden Ihr Konto und die zugehörigen Daten dauerhaft von unserer Plattform gelöscht. Es tut uns leid, Sie gehen zu sehen.',
        ],
        'breach_report_received' => [
            'title' => 'Verletzungsbericht erhalten',
            'content' => 'Vielen Dank für Ihren Bericht. Er wurde mit der ID #:report_id erhalten, und unser Sicherheitsteam überprüft ihn.',
        ],
        'status' => [
            'pending_user_confirmation' => 'Ausstehende Benutzerbestätigung',
            'user_confirmed_action' => 'Benutzeraktion bestätigt',
            'user_revoked_consent' => 'Benutzeraktion zurückgezogen',
            'user_disavowed_suspicious' => 'Benutzeraktion nicht anerkannt',
        ],
    ],

    'consent_management' => [
        'title' => 'Zustimmungsverwaltung',
        'subtitle' => 'Kontrollieren Sie die Verwendung Ihrer personenbezogenen Daten',
        'description' => 'Hier können Sie Ihre Zustimmungspräferenzen für verschiedene Zwecke und Dienste verwalten.',
        'update_preferences' => 'Ihre Zustimmungspräferenzen aktualisieren',
        'preferences_updated' => 'Ihre Zustimmungspräferenzen wurden erfolgreich aktualisiert.',
        'preferences_update_error' => 'Beim Aktualisieren Ihrer Zustimmungspräferenzen ist ein Fehler aufgetreten. Bitte versuchen Sie es erneut.',
    ],

    // Fußzeile
    'privacy_policy' => 'Datenschutzrichtlinie',
    'terms_of_service' => 'Nutzungsbedingungen',
    'all_rights_reserved' => 'Alle Rechte vorbehalten.',
    'navigation_label' => 'DSGVO-Navigation',
    'main_content_label' => 'DSGVO-Hauptinhalt',
];
