<?php

/**
 * AI Sidebar - Deutsche Übersetzungen
 * P0-2: Translation keys only
 * P0-9: All 6 languages required
 */

return [
    // General
    'title' => 'Natan',
    'subtitle' => 'Hilft dir, dein Profil zu vervollständigen',
    'toggle_title' => 'Onboarding-Assistent öffnen',
    'close' => 'Schließen',
    'send' => 'Senden',
    'chat_placeholder' => 'Frag etwas...',
    'assistant_name' => 'Natan',
    'quick_actions_label' => 'Empfohlene nächste Schritte:',

    // Messages (programmatic AI)
    'messages' => [
        'welcome' => 'Willkommen! Ich helfe dir, dein Profil einzurichten.',
        'progress_low' => 'Guter Start! Du hast :completed von :total Schritten abgeschlossen. Nächster: :nextStep',
        'progress_high' => 'Fast geschafft! Nur noch :remaining Schritte.',
        'complete' => 'Herzlichen Glückwunsch! Dein Profil ist vollständig eingerichtet. 🎉',
        'all_done' => 'Alle Einrichtungsschritte abgeschlossen!',
    ],

    // Checklist
    'checklist' => [
        'progress' => 'Einrichtungsfortschritt',
        'title' => 'Onboarding Checkliste',
    ],

    // Discourse messages (AI-like text)
    'discourse' => [
        'greeting' => 'Hallo',
        'greeting_suffix' => '! Ich bin hier, um dir bei der Vervollständigung deines Profils zu helfen.',
        'progress_intro' => 'Du hast',
        'progress_of' => 'von',
        'progress_suffix' => ' Schritten abgeschlossen. Schauen wir uns an, was noch fehlt, um dein Profil perfekt zu machen.',
        'missing_title' => 'Das musst du noch tun:',
        'suggestion_intro' => 'Ich empfehle, mit diesem zu beginnen',
        'click_hint' => 'Klicke auf einen Punkt in der Liste unten, um ihn abzuschließen.',
        'complete_title' => 'Fantastisch!',
        'complete_text' => 'Dein Profil ist vollständig und bereit, entdeckt zu werden. Jetzt kannst du dich auf die Erstellung deiner Werke und das Wachstum deiner Community konzentrieren.',
    ],

    // Checklist Items - Creator
    'steps' => [
        'avatar' => [
            'title' => 'Lade dein Avatar hoch',
            'description' => 'Füge ein Profilbild hinzu, um erkannt zu werden',
        ],
        'banner' => [
            'title' => 'Passe dein Banner an',
            'description' => 'Füge ein Titelbild zu deinem Profil hinzu',
        ],
        'bio' => [
            'title' => 'Schreibe deine Bio',
            'description' => 'Erzähle uns, wer du bist und was du erschaffst',
        ],
        'stripe' => [
            'title' => 'Zahlungen einrichten',
            'description' => 'Verbinde Stripe, um Zahlungen zu erhalten',
        ],
        'collection' => [
            'title' => 'Erstelle deine erste Sammlung',
            'description' => 'Organisiere deine Werke in einer Sammlung',
        ],
        'first_egi' => [
            'title' => 'Erstelle dein erstes EGI',
            'description' => 'Veröffentliche dein erstes digitales Kunstwerk',
        ],
        'social_links' => [
            'title' => 'Social Links hinzufügen',
            'description' => 'Verbinde deine Social-Media-Profile',
        ],
        'verify_email' => [
            'title' => 'E-Mail verifizieren',
            'description' => 'Bestätige deine E-Mail-Adresse',
        ],
    ],

    // Errors
    'errors' => [
        'request_failed' => 'Anfrage fehlgeschlagen. Bitte erneut versuchen.',
        'connection_error' => 'Verbindungsfehler. Bitte überprüfe deine Internetverbindung.',
        'invalid_user_type' => 'Ungültiger Benutzertyp.',
        'unauthorized' => 'Nicht berechtigt, auf diese Ressource zuzugreifen.',
    ],
];
