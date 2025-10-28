<?php

/**
 * @package Resources\Lang\De
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 3.0.0 (FlorenceEGI - N.A.T.A.N. Web Search Integration)
 * @date 2025-10-26
 * @purpose N.A.T.A.N. AI chat system translations (PA/Enterprise) - German
 */

return [
    'chat.title' => 'N.A.T.A.N. - KI-Strategieberatung',
    'chat.subtitle' => 'Befragen Sie Ihre Verwaltungsdokumente mit künstlicher Intelligenz',
    'chat.input_placeholder' => 'Fragen Sie etwas über Ihre Dokumente... (Umschalt+Enter zum Senden)',
    'chat.send_button' => 'Senden',
    'web_search.toggle_label' => 'Auch im Web suchen',
    'web_search.toggle_hint' => 'Bereichern Sie Antworten mit globalen Best Practices, aktualisierten Vorschriften und Fördermöglichkeiten',
    'web_sources.title' => 'Externe Quellen (Web)',
    'web_sources.count' => '{count} Webergebnisse',
    'persona.select_title' => 'Berater auswählen',
    'persona.auto_mode' => 'Automatisch (empfohlen)',
    'sources.title' => 'Quellen',
    'copy.button' => 'Kopieren',
    'errors.generic' => 'Ein Fehler ist aufgetreten. Bitte versuchen Sie es später erneut.',
    'errors.ai_consent_required' => 'KI-Verarbeitungszustimmung erforderlich. Bitte aktualisieren Sie Ihre Datenschutzeinstellungen, um N.A.T.A.N. zu verwenden.',

    // === INTELLIGENT CHUNKING - Frontend Errors (Phase 3) ===
    'chunking.timeout_error' => 'Die Verarbeitung dauert länger als erwartet (>5 Minuten)',
    'chunking.session_not_found' => 'Verarbeitungssitzung nicht gefunden. Bitte versuchen Sie es erneut.',
    'chunking.unauthorized' => 'Sie haben keine Berechtigung, auf diese Verarbeitungssitzung zuzugreifen.',
    'chunking.polling_error' => 'Fehler beim Überprüfen des Verarbeitungsstatus',
    'chunking.final_error' => 'Fehler beim Abrufen des Endergebnisses',
    'chunking.retry_button' => 'Analyse wiederholen',
];
