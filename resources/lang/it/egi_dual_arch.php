<?php

/**
 * Dual Architecture EGI - UI Messages
 *
 * Traduzioni per i messaggi dell'interfaccia utente relativi
 * alla gestione Auto-Mint e Pre-Mint degli EGI.
 *
 * @package FlorenceEGI\Translations
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Dual Architecture)
 * @date 2025-10-21
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Auto-Mint Messages
    |--------------------------------------------------------------------------
    */
    'auto_mint_enabled' => 'Auto-Mint abilitato con successo. Puoi ora mintare il tuo EGI quando vuoi.',
    'auto_mint_disabled' => 'Auto-Mint disabilitato. L\'EGI è ora disponibile per la vendita sul marketplace.',

    /*
    |--------------------------------------------------------------------------
    | AI Analysis Messages
    |--------------------------------------------------------------------------
    */
    'ai_analysis_requested' => 'Richiesta di analisi AI inviata con successo. N.A.T.A.N elaborerà i tuoi dati a breve.',
    'description_generated' => 'Descrizione generata con successo da N.A.T.A.N AI e salvata nel tuo EGI.',
    'description_improved' => 'Descrizione migliorata con successo da N.A.T.A.N AI e salvata nel tuo EGI.',

    /*
    |--------------------------------------------------------------------------
    | Promotion Messages
    |--------------------------------------------------------------------------
    */
    'promotion_initiated' => 'Promozione su blockchain avviata. La transazione è in elaborazione sulla rete Algorand.',

    /*
    |--------------------------------------------------------------------------
    | EGI Type Labels
    |--------------------------------------------------------------------------
    */
    'type' => [
        'ASA' => 'EGI Classico',
        'SmartContract' => 'EGI Vivente',
        'PreMint' => 'Pre-Mint',
    ],

    /*
    |--------------------------------------------------------------------------
    | Status Messages
    |--------------------------------------------------------------------------
    */
    'status' => [
        'pre_mint_active' => 'Pre-Mint Attivo',
        'auto_mint_enabled' => 'Auto-Mint Abilitato',
        'minting_in_progress' => 'Minting in Corso',
        'minted' => 'Mintato',
    ],

    /*
    |--------------------------------------------------------------------------
    | AI Traits Generation Messages
    |--------------------------------------------------------------------------
    */
    'ai' => [
        'unauthorized' => 'Devi essere autenticato per utilizzare questa funzionalità.',
        'forbidden' => 'Non hai i permessi per accedere a questa risorsa.',
        'generation_started' => 'Generazione traits AI avviata con successo! N.A.T.A.N sta analizzando l\'immagine.',
        'generation_failed' => 'Si è verificato un errore durante la generazione dei traits. Riprova più tardi.',
        'generation_not_found' => 'Sessione di generazione non trovata.',
        'review_completed' => 'Revisione delle proposte completata con successo.',
        'review_failed' => 'Si è verificato un errore durante la revisione. Riprova più tardi.',
        'traits_applied' => 'Traits applicati con successo al tuo EGI!',
        'apply_failed' => 'Si è verificato un errore durante l\'applicazione dei traits. Riprova più tardi.',

        // UI Labels
        'generate_traits' => 'Genera Traits con AI',
        'requested_count' => 'Numero di traits da generare',
        'trait_proposals' => 'Proposte Traits',
        'confidence' => 'Confidenza',
        'match_type' => 'Tipo Match',
        'exact_match' => 'Match Esatto',
        'fuzzy_match' => 'Match Fuzzy',
        'new_value' => 'Valore Nuovo',
        'new_type' => 'Tipo Nuovo',
        'new_category' => 'Categoria Nuova',
        'approve' => 'Approva',
        'reject' => 'Rifiuta',
        'modify' => 'Modifica',
        'apply_traits' => 'Applica Traits Approvati',
        'analyzing' => 'N.A.T.A.N sta analizzando...',
        'pending_review' => 'In attesa di revisione',
        'approved' => 'Approvato',
        'rejected' => 'Rifiutato',
        'applied' => 'Applicato',
    ],
];
