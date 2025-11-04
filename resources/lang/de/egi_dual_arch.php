<?php

/**
 * Dual Architecture EGI - UI Messages
 *
 * German translations for user interface messages related
 * to Auto-Mint and Pre-Mint EGI management.
 *
 * @package FlorenceEGI\Translations
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 2.0.0 (FlorenceEGI - Dual Architecture + AI Traits Modal)
 * @date 2025-11-04
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Auto-Mint Messages
    |--------------------------------------------------------------------------
    */
    'auto_mint_enabled' => 'Auto-Mint erfolgreich aktiviert. Sie können Ihr EGI jetzt jederzeit minten.',
    'auto_mint_disabled' => 'Auto-Mint deaktiviert. Das EGI ist jetzt auf dem Marktplatz zum Verkauf verfügbar.',

    /*
    |--------------------------------------------------------------------------
    | AI Analysis Messages
    |--------------------------------------------------------------------------
    */
    'ai_analysis_requested' => 'KI-Analyseanfrage erfolgreich gesendet. N.A.T.A.N wird Ihre Daten in Kürze verarbeiten.',
    'description_generated' => 'Beschreibung erfolgreich von N.A.T.A.N KI generiert und in Ihrem EGI gespeichert.',
    'description_improved' => 'Beschreibung erfolgreich von N.A.T.A.N KI verbessert und in Ihrem EGI gespeichert.',

    /*
    |--------------------------------------------------------------------------
    | Promotion Messages
    |--------------------------------------------------------------------------
    */
    'promotion_initiated' => 'Blockchain-Promotion gestartet. Die Transaktion wird im Algorand-Netzwerk verarbeitet.',

    /*
    |--------------------------------------------------------------------------
    | EGI Type Labels
    |--------------------------------------------------------------------------
    */
    'type' => [
        'ASA' => 'Klassisches EGI',
        'SmartContract' => 'Lebendiges EGI',
        'PreMint' => 'Pre-Mint',
    ],

    /*
    |--------------------------------------------------------------------------
    | Status Messages
    |--------------------------------------------------------------------------
    */
    'status' => [
        'pre_mint_active' => 'Pre-Mint Aktiv',
        'auto_mint_enabled' => 'Auto-Mint Aktiviert',
        'minting_in_progress' => 'Minting läuft',
        'minted' => 'Gemintet',
    ],

    /*
    |--------------------------------------------------------------------------
    | AI Traits Generation Messages
    |--------------------------------------------------------------------------
    */
    'ai' => [
        'unauthorized' => 'Sie müssen authentifiziert sein, um diese Funktion zu nutzen.',
        'forbidden' => 'Sie haben keine Berechtigung, auf diese Ressource zuzugreifen.',
        'generation_started' => 'KI-Traits-Generierung erfolgreich gestartet! N.A.T.A.N analysiert das Bild.',
        'generation_failed' => 'Bei der Traits-Generierung ist ein Fehler aufgetreten. Bitte versuchen Sie es später erneut.',
        'generation_not_found' => 'Generierungssitzung nicht gefunden.',
        'review_completed' => 'Vorschlagsprüfung erfolgreich abgeschlossen.',
        'review_failed' => 'Bei der Prüfung ist ein Fehler aufgetreten. Bitte versuchen Sie es später erneut.',
        'traits_applied' => 'Traits erfolgreich auf Ihr EGI angewendet!',
        'apply_failed' => 'Beim Anwenden der Traits ist ein Fehler aufgetreten. Bitte versuchen Sie es später erneut.',

        // UI Labels
        'generate_traits' => 'Traits mit KI generieren',
        'requested_count' => 'Anzahl der zu generierenden Traits',
        'trait_proposals' => 'Trait-Vorschläge',
        'confidence' => 'Konfidenz',
        'match_type' => 'Match-Typ',
        'exact_match' => 'Exakte Übereinstimmung',
        'fuzzy_match' => 'Unscharfe Übereinstimmung',
        'new_value' => 'Neuer Wert',
        'new_type' => 'Neuer Typ',
        'new_category' => 'Neue Kategorie',
        'approve' => 'Genehmigen',
        'reject' => 'Ablehnen',
        'modify' => 'Ändern',
        'apply_traits' => 'Genehmigte Traits anwenden',
        'analyzing' => 'N.A.T.A.N analysiert...',
        'pending_review' => 'Prüfung ausstehend',
        'approved' => 'Genehmigt',
        'rejected' => 'Abgelehnt',
        'applied' => 'Angewendet',
        
        // Modal UI Labels (v2.0)
        'review_proposals_modal' => 'KI-Vorschläge prüfen',
        'proposals_modal_title' => 'KI-Trait-Vorschläge',
        'close_modal' => 'Schließen',
        'approve_all' => 'Alle genehmigen',
        'reject_all' => 'Alle ablehnen',
        'apply_selected' => 'Anwenden',
    ],
];


