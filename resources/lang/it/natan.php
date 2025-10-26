<?php

/**
 * @package Resources\Lang\It
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 3.0.0 (FlorenceEGI - N.A.T.A.N. Web Search Integration)
 * @date 2025-10-26
 * @purpose Traduzioni sistema chat AI N.A.T.A.N. (PA/Enterprise)
 */

return [
    // === CHAT INTERFACE ===
    'chat.title' => 'N.A.T.A.N. - Consulenza Strategica AI',
    'chat.subtitle' => 'Interroga i tuoi documenti amministrativi con intelligenza artificiale',
    'chat.input_placeholder' => 'Chiedi qualcosa ai tuoi documenti... (Shift+Enter per inviare)',
    'chat.send_button' => 'Invia',
    'chat.send_aria' => 'Invia messaggio',
    'chat.clear_chat' => 'Nuova conversazione',
    'chat.clear_aria' => 'Cancella cronologia chat',

    // === WEB SEARCH TOGGLE (NEW v3.0) ===
    'web_search.toggle_label' => 'Cerca anche sul web',
    'web_search.toggle_hint' => 'Arricchisci le risposte con best practices globali, normative aggiornate e opportunità di finanziamento',
    'web_search.enabled' => 'Ricerca web attiva',
    'web_search.disabled' => 'Ricerca web disattivata',
    'web_search.tooltip' => 'Include fonti esterne da internet (best practices, normative, funding)',

    // === WEB SOURCES DISPLAY ===
    'web_sources.title' => 'Fonti Esterne (Web)',
    'web_sources.count' => '{count} risultati dal web',
    'web_sources.from_cache' => '(Risultati cache)',
    'web_sources.provider' => 'Provider:',
    'web_sources.relevance' => 'Rilevanza:',
    'web_sources.open_link' => 'Apri fonte',
    'web_sources.copy_url' => 'Copia URL',

    // === PERSONA SELECTOR ===
    'persona.select_title' => 'Seleziona Consulente',
    'persona.auto_mode' => 'Automatico (consigliato)',
    'persona.auto_hint' => 'N.A.T.A.N. sceglie il consulente più adatto per la tua domanda',
    'persona.manual_mode' => 'Manuale',
    'persona.confidence' => 'Confidenza',
    'persona.selected_by' => 'Selezionato',
    'persona.reasoning' => 'Motivazione',
    'persona.alternatives' => 'Alternative disponibili',

    // === PERSONAS NAMES ===
    'persona.strategic' => 'Consulente Strategico',
    'persona.technical' => 'Esperto Tecnico',
    'persona.legal' => 'Consulente Legale',
    'persona.financial' => 'Analista Finanziario',
    'persona.urban_social' => 'Urbanista/Social Impact',
    'persona.communication' => 'Esperto Comunicazione',

    // === QUICK ACTIONS ===
    'actions.title' => 'Elabora questa risposta',
    'actions.simplify' => 'Semplifica',
    'actions.deepen' => 'Approfondisci',
    'actions.actionable' => 'Azioni concrete',
    'actions.presentation' => 'Per presentazione',
    'actions.citizens' => 'Per cittadini',

    // === SOURCES ===
    'sources.title' => 'Fonti',
    'sources.internal' => 'Documenti Interni',
    'sources.external' => 'Fonti Web',
    'sources.count' => '{count} documenti analizzati',
    'sources.collapse' => 'Nascondi fonti',
    'sources.expand' => 'Mostra fonti',
    'sources.view_document' => 'Visualizza documento',
    'sources.blockchain_verified' => 'Verificato blockchain',

    // === FREE CHAT ===
    'free_chat.title' => 'Chat Libera con AI',
    'free_chat.subtitle' => 'Consulenza generale senza vincoli di documenti',
    'free_chat.placeholder' => 'Chiedi qualsiasi cosa...',

    // === SUGGESTIONS ===
    'suggestions.title' => 'Domande strategiche suggerite',
    'suggestions.refresh' => 'Altre domande',
    'suggestions.loading' => 'Caricamento suggerimenti...',

    // === MESSAGES ===
    'messages.thinking' => 'Sto analizzando...',
    'messages.searching_docs' => 'Ricerca tra i documenti in corso...',
    'messages.searching_web' => 'Ricerca sul web in corso...',
    'messages.generating' => 'Generazione risposta...',
    'messages.error' => 'Si è verificato un errore. Riprova.',
    'messages.empty_response' => 'Nessuna risposta generata.',
    'messages.no_sources' => 'Nessun documento trovato per questa query.',

    // === COPY FEATURE ===
    'copy.button' => 'Copia',
    'copy.success' => 'Copiato!',
    'copy.aria' => 'Copia risposta negli appunti',

    // === FEEDBACK ===
    'feedback.helpful' => 'Questa risposta è stata utile?',
    'feedback.yes' => 'Sì, utile',
    'feedback.no' => 'No, non utile',
    'feedback.thanks' => 'Grazie per il feedback!',

    // === ERRORS ===
    'errors.generic' => 'Si è verificato un errore. Riprova tra poco.',
    'errors.no_api_key' => 'Configurazione API mancante. Contatta l\'amministratore.',
    'errors.rate_limit' => 'Troppi richieste. Attendi qualche secondo.',
    'errors.invalid_query' => 'La domanda non è valida. Fornisci più dettagli.',
    'errors.no_results' => 'Nessun risultato trovato.',
    'errors.web_search_failed' => 'Ricerca web non riuscita. Continua solo con documenti interni.',

    // === GDPR & PRIVACY ===
    'gdpr.data_info' => 'I tuoi dati sono trattati in modo sicuro e conforme GDPR',
    'gdpr.no_pii_sent' => 'Nessun dato personale viene inviato a servizi esterni',
    'gdpr.audit_trail' => 'Tutte le interazioni sono registrate per audit',
];

