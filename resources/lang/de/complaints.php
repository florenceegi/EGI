<?php

return [

    // Page
    'title' => 'DSA-Meldungen und Beschwerden',
    'subtitle' => 'Melden Sie illegale Inhalte oder reichen Sie eine Beschwerde gemäß dem Digital Services Act (EU-Verordnung 2022/2065) ein',
    'dsa_info_title' => 'Ihre Rechte gemäß DSA',
    'dsa_info_text' => 'Gemäß der Verordnung (EU) 2022/2065 (Digital Services Act) haben Sie das Recht, Inhalte zu melden, die Sie für illegal halten (Art. 16), und Beschwerde gegen Moderationsentscheidungen der Plattform einzureichen (Art. 20). Jede Meldung wird von qualifiziertem Personal innerhalb angemessener Fristen überprüft.',
    'legal_contact' => 'Für dringende Meldungen können Sie auch schreiben an',

    // Types
    'types' => [
        'content_report' => 'Meldung illegaler Inhalte',
        'ip_violation' => 'Verletzung des Urheberrechts',
        'fraud' => 'Betrug oder betrügerische Aktivität',
        'moderation_appeal' => 'Beschwerde gegen Moderationsentscheidung',
        'general' => 'Allgemeine Meldung',
    ],

    // Type descriptions (for form helper text)
    'type_descriptions' => [
        'content_report' => 'Illegale, anstößige oder gegen unsere Nutzungsbedingungen verstoßende Inhalte',
        'ip_violation' => 'Gefälschte Werke, Plagiate, Urheber- oder Markenrechtsverletzungen',
        'fraud' => 'Betrügereien, Zahlungsbetrug oder betrügerisches Verhalten',
        'moderation_appeal' => 'Bestreiten Sie eine Entscheidung der Plattform bezüglich Ihrer Inhalte',
        'general' => 'Jede andere Meldung, die nicht in die obigen Kategorien fällt',
    ],

    // Content types
    'content_types' => [
        'egi' => 'Werk (EGI)',
        'collection' => 'Sammlung',
        'user_profile' => 'Benutzerprofil',
        'comment' => 'Kommentar',
    ],

    // Statuses
    'statuses' => [
        'received' => 'Empfangen',
        'under_review' => 'Zur Überprüfung',
        'action_taken' => 'Maßnahme ergriffen',
        'dismissed' => 'Archiviert',
        'appealed' => 'Beschwerde eingereicht',
        'resolved' => 'Gelöst',
    ],

    // Form labels
    'form_title' => 'Neue Meldung',
    'select_type' => 'Wählen Sie die Art der Meldung',
    'complaint_type' => 'Art der Meldung',
    'reported_content_type' => 'Art des gemeldeten Inhalts',
    'select_content_type' => 'Wählen Sie die Art des Inhalts',
    'reported_content_id' => 'ID des Inhalts',
    'reported_content_id_help' => 'Geben Sie die ID des Inhalts ein, den Sie melden möchten (sichtbar auf der Inhaltsseite)',
    'description' => 'Detaillierte Beschreibung',
    'description_placeholder' => 'Beschreiben Sie den Grund der Meldung im Detail, einschließlich aller Elemente, die zur Bewertung relevant sind. Mindestens 20 Zeichen erforderlich.',
    'description_chars' => ':count / :max Zeichen',
    'evidence_urls' => 'Nachweislinks (optional)',
    'evidence_urls_help' => 'Geben Sie Links zu Screenshots, Webseiten oder anderen Elementen ein, die Ihre Meldung unterstützen. Maximal 5 URLs.',
    'add_evidence_url' => 'URL hinzufügen',
    'remove_evidence_url' => 'Entfernen',
    'evidence_url_placeholder' => 'https://...',
    'consent_label' => 'Zustimmung zur Verarbeitung',
    'consent_text' => 'Ich stimme der Verarbeitung meiner persönlichen Daten zu, die für die Bearbeitung dieser Meldung erforderlich sind, gemäß der EU-Verordnung 2016/679 (DSGVO) und der EU-Verordnung 2022/2065 (DSA). Ich erkläre, dass die bereitgestellten Informationen wahrheitsgemäß und in gutem Glauben sind.',

    // Actions
    'submit' => 'Meldung einreichen',
    'submitting' => 'Wird eingereicht...',
    'cancel' => 'Abbrechen',
    'back_to_list' => 'Zurück zu den Meldungen',
    'view_details' => 'Details',

    // Messages
    'submitted_successfully' => 'Ihre Meldung wurde erfolgreich eingereicht. Referenznummer: :reference. Sie erhalten eine Bestätigung per E-Mail.',
    'no_complaints' => 'Sie haben noch keine Meldungen oder Beschwerden eingereicht.',

    // Table headers
    'date' => 'Datum',
    'reference' => 'Referenz',
    'type' => 'Typ',
    'status' => 'Status',
    'actions' => 'Aktionen',

    // Previous complaints section
    'your_complaints' => 'Ihre Meldungen',
    'your_complaints_description' => 'Verlauf der Meldungen und Beschwerden, die Sie eingereicht haben',

    // Validation
    'validation' => [
        'type_required' => 'Wählen Sie die Art der Meldung aus.',
        'type_invalid' => 'Die ausgewählte Art der Meldung ist ungültig.',
        'description_required' => 'Die Beschreibung ist erforderlich.',
        'description_min' => 'Die Beschreibung muss mindestens 20 Zeichen lang sein.',
        'description_max' => 'Die Beschreibung darf nicht mehr als 5000 Zeichen umfassen.',
        'content_id_required' => 'Die Inhalts-ID ist erforderlich, wenn Sie einen Inhaltstyp auswählen.',
        'evidence_urls_max' => 'Sie können maximal 5 Nachweislinks eingeben.',
        'evidence_url_format' => 'Jeder Nachweislink muss eine gültige Webadresse sein.',
        'consent_required' => 'Sie müssen der Datenverarbeitung zustimmen, um fortzufahren.',
    ],

    // Detail page
    'detail_title' => 'Meldungsdetails',
    'submitted_on' => 'Eingereicht am',
    'current_status' => 'Aktueller Status',
    'complaint_type_label' => 'Typ',
    'reported_content' => 'Gemeldeter Inhalt',
    'description_label' => 'Beschreibung',
    'evidence_label' => 'Beigefügte Nachweise',
    'decision' => 'Entscheidung',
    'decision_date' => 'Datum der Entscheidung',
    'decided_by_label' => 'Entschieden von',
    'no_decision_yet' => 'Wartet auf Überprüfung durch das Team.',
    'appeal_section' => 'Beschwerde / Berufung',
    'no_appeal' => 'Keine Beschwerde eingereicht.',
    'content_id_label' => 'Inhalts-ID',
    'content_type_label' => 'Inhaltstyp',
    'reported_user_label' => 'Gemeldeter Benutzer',

    // Timeline
    'timeline' => [
        'received' => 'Meldung empfangen',
        'under_review' => 'Übernahme zur Bearbeitung',
        'action_taken' => 'Maßnahme ergriffen',
        'dismissed' => 'Meldung archiviert',
        'appealed' => 'Beschwerde eingereicht',
        'resolved' => 'Fall gelöst',
    ],

    // Notification email
    'notification' => [
        'subject' => 'Bestätigung der DSA-Meldung - :reference',
        'greeting' => 'Sehr geehrte/r :name,',
        'body' => 'Ihre Meldung wurde empfangen und registriert mit der Referenznummer **:reference**.',
        'body_2' => 'Wir werden Ihre Meldung überprüfen und Sie innerhalb der im Digital Services Act (EU-Verordnung 2022/2065) vorgesehenen Fristen kontaktieren.',
        'reference_label' => 'Referenznummer',
        'type_label' => 'Meldungstyp',
        'date_label' => 'Sendedatum',
        'closing' => 'Das FlorenceEGI-Team',
    ],

];
