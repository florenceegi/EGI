<?php

return [

    // Page
    'title' => 'Segnalazioni e Reclami DSA',
    'subtitle' => 'Segnala contenuti illeciti o presenta un reclamo ai sensi del Digital Services Act (Reg. UE 2022/2065)',
    'dsa_info_title' => 'I tuoi diritti ai sensi del DSA',
    'dsa_info_text' => 'Ai sensi del Regolamento (UE) 2022/2065 (Digital Services Act), hai il diritto di segnalare contenuti che ritieni illeciti (Art. 16) e di presentare reclami contro le decisioni di moderazione della piattaforma (Art. 20). Ogni segnalazione viene esaminata da personale qualificato entro tempi ragionevoli.',
    'legal_contact' => 'Per segnalazioni urgenti puoi anche scrivere a',

    // Types
    'types' => [
        'content_report' => 'Segnalazione contenuto illecito',
        'ip_violation' => 'Violazione proprietà intellettuale',
        'fraud' => 'Frode o attività fraudolenta',
        'moderation_appeal' => 'Reclamo contro decisione di moderazione',
        'general' => 'Segnalazione generica',
    ],

    // Type descriptions (for form helper text)
    'type_descriptions' => [
        'content_report' => 'Contenuti illegali, offensivi, o che violano le nostre condizioni d\'uso',
        'ip_violation' => 'Opere contraffatte, plagio, violazione di copyright o marchi registrati',
        'fraud' => 'Truffe, frodi nei pagamenti, o comportamenti ingannevoli',
        'moderation_appeal' => 'Contesti una decisione presa dalla piattaforma riguardo ai tuoi contenuti',
        'general' => 'Qualsiasi altra segnalazione non rientrante nelle categorie precedenti',
    ],

    // Content types
    'content_types' => [
        'egi' => 'Opera (EGI)',
        'collection' => 'Collezione',
        'user_profile' => 'Profilo utente',
        'comment' => 'Commento',
    ],

    // Statuses
    'statuses' => [
        'received' => 'Ricevuta',
        'under_review' => 'In revisione',
        'action_taken' => 'Provvedimento adottato',
        'dismissed' => 'Archiviata',
        'appealed' => 'Reclamo presentato',
        'resolved' => 'Risolta',
    ],

    // Form labels
    'form_title' => 'Nuova segnalazione',
    'select_type' => 'Seleziona il tipo di segnalazione',
    'complaint_type' => 'Tipo di segnalazione',
    'reported_content_type' => 'Tipo di contenuto segnalato',
    'select_content_type' => 'Seleziona il tipo di contenuto',
    'reported_content_id' => 'ID del contenuto',
    'reported_content_id_help' => 'Inserisci l\'ID del contenuto che desideri segnalare (visibile nella pagina del contenuto)',
    'description' => 'Descrizione dettagliata',
    'description_placeholder' => 'Descrivi nel dettaglio il motivo della segnalazione, includendo tutti gli elementi utili alla valutazione. Minimo 20 caratteri.',
    'description_chars' => ':count / :max caratteri',
    'evidence_urls' => 'URL di prova (opzionale)',
    'evidence_urls_help' => 'Inserisci link a screenshot, pagine web o altri elementi a supporto della segnalazione. Massimo 5 URL.',
    'add_evidence_url' => 'Aggiungi URL',
    'remove_evidence_url' => 'Rimuovi',
    'evidence_url_placeholder' => 'https://...',
    'consent_label' => 'Consenso al trattamento',
    'consent_text' => 'Acconsento al trattamento dei dati personali necessari per la gestione della presente segnalazione, ai sensi del Reg. UE 2016/679 (GDPR) e del Reg. UE 2022/2065 (DSA). Dichiaro che le informazioni fornite sono veritiere e in buona fede.',

    // Actions
    'submit' => 'Invia segnalazione',
    'submitting' => 'Invio in corso...',
    'cancel' => 'Annulla',
    'back_to_list' => 'Torna alle segnalazioni',
    'view_details' => 'Dettagli',

    // Messages
    'submitted_successfully' => 'La tua segnalazione è stata inviata con successo. Numero di riferimento: :reference. Riceverai una conferma via email.',
    'no_complaints' => 'Non hai ancora presentato segnalazioni o reclami.',

    // Table headers
    'date' => 'Data',
    'reference' => 'Riferimento',
    'type' => 'Tipo',
    'status' => 'Stato',
    'actions' => 'Azioni',

    // Previous complaints section
    'your_complaints' => 'Le tue segnalazioni',
    'your_complaints_description' => 'Storico delle segnalazioni e dei reclami che hai presentato',

    // Validation
    'validation' => [
        'type_required' => 'Seleziona il tipo di segnalazione.',
        'type_invalid' => 'Il tipo di segnalazione selezionato non è valido.',
        'description_required' => 'La descrizione è obbligatoria.',
        'description_min' => 'La descrizione deve contenere almeno 20 caratteri.',
        'description_max' => 'La descrizione non può superare 5000 caratteri.',
        'content_id_required' => 'L\'ID del contenuto è obbligatorio quando si seleziona un tipo di contenuto.',
        'evidence_urls_max' => 'Puoi inserire un massimo di 5 URL di prova.',
        'evidence_url_format' => 'Ogni URL di prova deve essere un indirizzo web valido.',
        'consent_required' => 'Devi acconsentire al trattamento dei dati per procedere.',
    ],

    // Detail page
    'detail_title' => 'Dettaglio segnalazione',
    'submitted_on' => 'Inviata il',
    'current_status' => 'Stato attuale',
    'complaint_type_label' => 'Tipo',
    'reported_content' => 'Contenuto segnalato',
    'description_label' => 'Descrizione',
    'evidence_label' => 'Prove allegate',
    'decision' => 'Decisione',
    'decision_date' => 'Data della decisione',
    'decided_by_label' => 'Decisa da',
    'no_decision_yet' => 'In attesa di revisione da parte del team.',
    'appeal_section' => 'Reclamo / Appello',
    'no_appeal' => 'Nessun reclamo presentato.',
    'content_id_label' => 'ID contenuto',
    'content_type_label' => 'Tipo contenuto',
    'reported_user_label' => 'Utente segnalato',

    // Timeline
    'timeline' => [
        'received' => 'Segnalazione ricevuta',
        'under_review' => 'Presa in carico',
        'action_taken' => 'Provvedimento adottato',
        'dismissed' => 'Segnalazione archiviata',
        'appealed' => 'Reclamo presentato',
        'resolved' => 'Caso risolto',
    ],

    // Notification email
    'notification' => [
        'subject' => 'Conferma ricezione segnalazione DSA - :reference',
        'greeting' => 'Gentile :name,',
        'body' => 'La tua segnalazione è stata ricevuta e registrata con il numero di riferimento **:reference**.',
        'body_2' => 'Esamineremo la tua segnalazione e ti contatteremo entro i termini previsti dal Digital Services Act (Reg. UE 2022/2065).',
        'reference_label' => 'Numero riferimento',
        'type_label' => 'Tipo segnalazione',
        'date_label' => 'Data invio',
        'closing' => 'Il team FlorenceEGI',
    ],

];
