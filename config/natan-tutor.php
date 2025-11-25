<?php

/**
 * @package Config
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Natan Tutor)
 * @date 2025-11-25
 * @purpose Configuration for Natan Tutor - Operational Assistant
 * @source docs/FlorenceEGI/Implementation/NatanTutor/NATAN_TUTOR_DESIGN.md
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Natan Tutor Feature Toggle
    |--------------------------------------------------------------------------
    */
    'enabled' => env('NATAN_TUTOR_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Pricing - Listino Azioni Natan Tutor
    |--------------------------------------------------------------------------
    |
    | Costi in Egili per ogni azione eseguibile da Natan.
    | Le azioni gratuite hanno cost_egili = 0
    |
    */
    'pricing' => [

        // ═══════════════════════════════════════════════════════════════════
        // AZIONI CREATOR
        // ═══════════════════════════════════════════════════════════════════

        'action_mint' => [
            'code' => 'natan_action_mint',
            'name' => 'Mint Opera via Natan',
            'description' => 'Natan esegue il mint della tua opera sulla blockchain Algorand',
            'cost_egili' => 10,
            'category' => 'creator_actions',
            'requires_confirmation' => true,
            'is_reversible' => false,
        ],

        'action_create_collection' => [
            'code' => 'natan_action_create_collection',
            'name' => 'Crea Collection via Natan',
            'description' => 'Natan crea una nuova collection per te',
            'cost_egili' => 5,
            'category' => 'creator_actions',
            'requires_confirmation' => true,
            'is_reversible' => true,
        ],

        'action_set_price' => [
            'code' => 'natan_action_set_price',
            'name' => 'Imposta Prezzo via Natan',
            'description' => 'Natan imposta o modifica il prezzo di un\'opera',
            'cost_egili' => 2,
            'category' => 'creator_actions',
            'requires_confirmation' => true,
            'is_reversible' => true,
        ],

        'action_publish' => [
            'code' => 'natan_action_publish',
            'name' => 'Pubblica Opera via Natan',
            'description' => 'Natan pubblica la tua opera rendendola visibile',
            'cost_egili' => 2,
            'category' => 'creator_actions',
            'requires_confirmation' => true,
            'is_reversible' => true,
        ],

        'action_unpublish' => [
            'code' => 'natan_action_unpublish',
            'name' => 'Nascondi Opera via Natan',
            'description' => 'Natan nasconde la tua opera dalla vista pubblica',
            'cost_egili' => 0, // Gratuito - non penalizziamo chi vuole nascondere
            'category' => 'creator_actions',
            'requires_confirmation' => true,
            'is_reversible' => true,
        ],

        // ═══════════════════════════════════════════════════════════════════
        // SERVIZI AI
        // ═══════════════════════════════════════════════════════════════════

        'action_ai_description' => [
            'code' => 'natan_action_ai_description',
            'name' => 'Genera Descrizione AI',
            'description' => 'Natan genera una descrizione professionale per la tua opera usando AI',
            'cost_egili' => 15,
            'category' => 'ai_services',
            'requires_confirmation' => true,
            'is_reversible' => true, // Può essere modificata
        ],

        'action_ai_tags' => [
            'code' => 'natan_action_ai_tags',
            'name' => 'Genera Tags AI',
            'description' => 'Natan suggerisce tag ottimizzati per la visibilità della tua opera',
            'cost_egili' => 5,
            'category' => 'ai_services',
            'requires_confirmation' => true,
            'is_reversible' => true,
        ],

        'action_ai_pricing_suggestion' => [
            'code' => 'natan_action_ai_pricing',
            'name' => 'Suggerimento Prezzo AI',
            'description' => 'Natan analizza il mercato e suggerisce un prezzo ottimale',
            'cost_egili' => 10,
            'category' => 'ai_services',
            'requires_confirmation' => false, // Solo suggerimento
            'is_reversible' => true,
        ],

        // ═══════════════════════════════════════════════════════════════════
        // AZIONI COLLECTOR
        // ═══════════════════════════════════════════════════════════════════

        'action_reserve' => [
            'code' => 'natan_action_reserve',
            'name' => 'Prenota EGI via Natan',
            'description' => 'Natan effettua la prenotazione di un opera per te',
            'cost_egili' => 5,
            'category' => 'collector_actions',
            'requires_confirmation' => true,
            'is_reversible' => true, // Può annullare
        ],

        'action_cancel_reservation' => [
            'code' => 'natan_action_cancel_reservation',
            'name' => 'Annulla Prenotazione via Natan',
            'description' => 'Natan annulla una tua prenotazione esistente',
            'cost_egili' => 0, // Gratuito
            'category' => 'collector_actions',
            'requires_confirmation' => true,
            'is_reversible' => false,
        ],

        // ═══════════════════════════════════════════════════════════════════
        // TUTORING - Sessioni Guidate
        // ═══════════════════════════════════════════════════════════════════

        'guided_tutorial_complete' => [
            'code' => 'natan_tutorial_complete',
            'name' => 'Tutorial Completo Piattaforma',
            'description' => 'Sessione guidata completa: dalla registrazione al primo mint',
            'cost_egili' => 20,
            'category' => 'tutoring',
            'requires_confirmation' => true,
            'estimated_duration_minutes' => 15,
        ],

        'guided_tutorial_creator' => [
            'code' => 'natan_tutorial_creator',
            'name' => 'Tutorial Creator',
            'description' => 'Impara a creare collection, caricare opere e mintare',
            'cost_egili' => 15,
            'category' => 'tutoring',
            'requires_confirmation' => true,
            'estimated_duration_minutes' => 10,
        ],

        'guided_tutorial_collector' => [
            'code' => 'natan_tutorial_collector',
            'name' => 'Tutorial Collector',
            'description' => 'Impara a cercare, prenotare e collezionare opere',
            'cost_egili' => 10,
            'category' => 'tutoring',
            'requires_confirmation' => true,
            'estimated_duration_minutes' => 8,
        ],

        'guided_tutorial_wallet' => [
            'code' => 'natan_tutorial_wallet',
            'name' => 'Tutorial Wallet & Blockchain',
            'description' => 'Capire wallet, Algorand e transazioni on-chain',
            'cost_egili' => 10,
            'category' => 'tutoring',
            'requires_confirmation' => true,
            'estimated_duration_minutes' => 8,
        ],

        // ═══════════════════════════════════════════════════════════════════
        // NAVIGAZIONE - Sempre Gratuita
        // ═══════════════════════════════════════════════════════════════════

        'navigation_goto' => [
            'code' => 'natan_navigation_goto',
            'name' => 'Navigazione Assistita',
            'description' => 'Natan ti porta dove vuoi andare',
            'cost_egili' => 0,
            'category' => 'navigation',
            'requires_confirmation' => false,
        ],

        'navigation_search' => [
            'code' => 'natan_navigation_search',
            'name' => 'Ricerca Assistita',
            'description' => 'Natan cerca opere, creator o collection per te',
            'cost_egili' => 0,
            'category' => 'navigation',
            'requires_confirmation' => false,
        ],

        // ═══════════════════════════════════════════════════════════════════
        // INFORMAZIONI - Sempre Gratuite
        // ═══════════════════════════════════════════════════════════════════

        'info_explain' => [
            'code' => 'natan_info_explain',
            'name' => 'Spiegazione Concetti',
            'description' => 'Natan spiega concetti della piattaforma',
            'cost_egili' => 0,
            'category' => 'info',
            'requires_confirmation' => false,
        ],

        'info_faq' => [
            'code' => 'natan_info_faq',
            'name' => 'FAQ e Aiuto',
            'description' => 'Risposte alle domande frequenti',
            'cost_egili' => 0,
            'category' => 'info',
            'requires_confirmation' => false,
        ],

        'info_status' => [
            'code' => 'natan_info_status',
            'name' => 'Stato Account',
            'description' => 'Informazioni sul tuo account, saldo, opere',
            'cost_egili' => 0,
            'category' => 'info',
            'requires_confirmation' => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Categories - Raggruppamento UI
    |--------------------------------------------------------------------------
    */
    'categories' => [
        'creator_actions' => [
            'name' => 'Azioni Creator',
            'description' => 'Operazioni per creator e artisti',
            'icon' => 'palette',
            'color' => 'emerald',
        ],
        'collector_actions' => [
            'name' => 'Azioni Collector',
            'description' => 'Operazioni per collezionisti',
            'icon' => 'shopping-bag',
            'color' => 'blue',
        ],
        'ai_services' => [
            'name' => 'Servizi AI',
            'description' => 'Generazione contenuti con intelligenza artificiale',
            'icon' => 'sparkles',
            'color' => 'purple',
        ],
        'tutoring' => [
            'name' => 'Tutorial Guidati',
            'description' => 'Sessioni di apprendimento interattive',
            'icon' => 'academic-cap',
            'color' => 'amber',
        ],
        'navigation' => [
            'name' => 'Navigazione',
            'description' => 'Assistenza nella navigazione (gratuita)',
            'icon' => 'map',
            'color' => 'gray',
        ],
        'info' => [
            'name' => 'Informazioni',
            'description' => 'Spiegazioni e FAQ (gratuite)',
            'icon' => 'information-circle',
            'color' => 'gray',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Welcome Gift - Egili Regalati alla Registrazione
    |--------------------------------------------------------------------------
    */
    'welcome_gift' => [
        'enabled' => env('NATAN_WELCOME_GIFT_ENABLED', true),
        'amount' => env('NATAN_WELCOME_GIFT_AMOUNT', 100),
        'type' => 'gift', // 'gift' = scade, 'lifetime' = non scade
        'expires_days' => env('NATAN_WELCOME_GIFT_EXPIRES_DAYS', 90),
        'reason' => 'Welcome to FlorenceEGI! 🎉',
        'message' => [
            'title' => 'Benvenuto su FlorenceEGI!',
            'body' => 'Ti abbiamo regalato :amount Egili per iniziare! Usali per esplorare la piattaforma con l\'aiuto di Natan Tutor.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Preferences
    |--------------------------------------------------------------------------
    */
    'user_modes' => [
        'guided' => [
            'name' => 'Modalità Guidata',
            'description' => 'Natan ti accompagna passo passo',
            'natan_proactive' => true,
            'show_tips' => true,
            'auto_suggest' => true,
        ],
        'expert' => [
            'name' => 'Modalità Esperto',
            'description' => 'Natan interviene solo su richiesta',
            'natan_proactive' => false,
            'show_tips' => false,
            'auto_suggest' => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Limits & Throttling
    |--------------------------------------------------------------------------
    */
    'limits' => [
        'actions_per_minute' => 10,
        'actions_per_hour' => 100,
        'tutorials_per_day' => 5,
    ],

    /*
    |--------------------------------------------------------------------------
    | Intent Keywords - Per parsing comandi naturali
    |--------------------------------------------------------------------------
    */
    'intent_keywords' => [
        'mint' => ['minta', 'mint', 'mintare', 'conia', 'coniare', 'pubblica on-chain'],
        'create_collection' => ['crea collection', 'nuova collection', 'crea collezione', 'nuova collezione'],
        'set_price' => ['prezzo', 'imposta prezzo', 'cambia prezzo', 'modifica prezzo'],
        'publish' => ['pubblica', 'rendi visibile', 'mostra'],
        'reserve' => ['prenota', 'prenotare', 'riserva', 'riservare'],
        'navigate' => ['vai', 'portami', 'mostrami', 'apri'],
        'search' => ['cerca', 'cercare', 'trova', 'trovare'],
        'explain' => ['spiega', 'spiegami', 'cos\'è', 'cosa sono', 'come funziona'],
        'help' => ['aiuto', 'aiutami', 'help', 'assistenza'],
        'tutorial' => ['tutorial', 'guida', 'guidami', 'insegnami', 'impara'],
    ],

];
