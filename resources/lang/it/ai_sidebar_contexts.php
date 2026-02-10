<?php

/**
 * AI Sidebar Context Messages - Italian
 *
 * Page-specific welcome messages for AI Sidebar based on user role and view.
 * These messages are injected into the sidebar's initial AI greeting.
 *
 * Structure: [view_name][role] where role can be: 'owner', 'visitor', 'guest'
 *
 * P0-2: Translation keys only (no hardcoded text in blade)
 * P0-9: Must be available in all 6 languages (it, en, de, es, fr, pt)
 *
 * @author FlorenceEGI Dev Team
 * @version 1.0.0
 * @date 2026-02-09
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Collection Show Page - Context Messages
    |--------------------------------------------------------------------------
    */

    'collection.show' => [
        /**
         * OWNER VIEW - Collection Management & Optimization
         *
         * Shown when the creator/company who owns the collection views their page.
         * Focus: Complete collection management, monetization, EPP configuration,
         * optimization checklist, completion strategies.
         */
        'owner' => [
            'greeting' => 'Ciao <strong>:name</strong>! 👋',
            'intro' => 'Questa è la tua <strong>Collection ":collection_name"</strong>. Ti aiuterò a gestirla, ottimizzarla e monetizzarla al meglio.',

            'status_title' => '📊 Stato Attuale',
            'status_egis' => '<strong>:egi_count EGI</strong> nella collection',
            'status_published' => '<strong>:published_count pubblicati</strong> e visibili',
            'status_drafts' => '<strong>:draft_count bozze</strong> da completare',
            'status_sold' => '<strong>:sold_count venduti</strong>',

            'actions_title' => '🎯 Cosa Puoi Fare',
            'actions' => [
                'monetize' => '<strong>Monetizzazione:</strong> Configura prezzi, disponibilità e modalità di vendita per ogni EGI',
                'epp' => '<strong>EPP Project:</strong> Collega un progetto ambientale alla collection per aumentare valore e appeal',
                'organize' => '<strong>Organizzazione:</strong> Riordina gli EGI, modifica descrizioni, aggiungi tag e categorie',
                'promote' => '<strong>Promozione:</strong> Condividi la collection sui social, newsletter, marketplace',
                'stats' => '<strong>Analytics:</strong> Monitora visualizzazioni, prenotazioni, vendite e guadagni',
            ],

            'suggestions_title' => '💡 Suggerimenti di Ottimizzazione',
            'suggestions' => [
                'incomplete_egis' => 'Completa gli EGI in bozza per renderli acquistabili',
                'no_prices' => 'Imposta prezzi competitivi per i tuoi EGI',
                'no_epp' => 'Collega un EPP Project per massimizzare l\'impatto ambientale e la profittabilità',
                'low_visibility' => 'Migliora le descrizioni e le immagini per aumentare le conversioni',
                'no_payment' => 'Configura i metodi di pagamento per ricevere i fondi delle vendite',
            ],

            'help_offer' => '🤝 <strong>Posso aiutarti con:</strong> Strategie di pricing, configurazione EPP, ottimizzazione SEO, gestione inventario, analytics. Chiedimi pure!',

            'cta' => 'Vuoi che ti guidi passo dopo passo? Chiedimi "Come ottimizzare la mia collection?" oppure "Cosa devo fare per vendere di più?"',
        ],

        /**
         * VISITOR VIEW - Marketing & Promotional
         *
         * Shown when a logged-in user (not owner) views the collection.
         * Focus: Marketing, value proposition, purchase process, creator benefits,
         * social proof, conversion optimization.
         */
        'visitor' => [
            'greeting' => 'Ciao <strong>:name</strong>! 👋',
            'intro' => 'Benvenuto nella <strong>Collection ":collection_name"</strong> di <strong>:creator_name</strong>.',

            'about_title' => '🎨 Cos\'è Questa Collection',
            'about_description' => 'Una <strong>collezione curata di :egi_count EGI</strong> (Ecological Goods Invent) – opere d\'arte digitali sostenibili che supportano progetti ambientali reali.',

            'value_title' => '💎 Perché Acquistare da Questa Collection',
            'value_props' => [
                'authenticity' => '<strong>Autenticità Garantita:</strong> Ogni EGI è certificato sulla blockchain e unico',
                'sustainability' => '<strong>Impatto Reale:</strong> Una % dei ricavi va direttamente a progetti EPP (Environmental Protection Projects)',
                'investment' => '<strong>Valore nel Tempo:</strong> Gli EGI possono aumentare di valore e diventare asset collezionabili',
                'support_artist' => '<strong>Supporta il Creator:</strong> Il tuo acquisto sostiene direttamente l\'artista/brand',
            ],

            'how_to_buy_title' => '🛒 Come Acquistare',
            'how_to_buy_steps' => [
                '1' => 'Esplora gli EGI disponibili nella collezione',
                '2' => 'Clicca su un EGI per vedere dettagli e prezzo',
                '3' => 'Prenota o acquista direttamente',
                '4' => 'Ricevi il certificato di proprietà digitale',
            ],

            'stats_title' => '📊 Numeri della Collection',
            'stats_egis' => '<strong>:egi_count EGI</strong> nella collection',
            'stats_available' => '<strong>:available_count disponibili</strong> per l\'acquisto',
            'stats_epp' => 'Supporta <strong>:epp_name</strong>' . (isset($epp_name) ? '' : ' <em>(nessun EPP collegato)</em>'),

            'creator_title' => '👤 Sul Creator',
            'creator_bio' => ':creator_name è :creator_type_label su FlorenceEGI. <a href=":creator_url" class="text-indigo-400 hover:text-indigo-300 underline">Visita il profilo</a> per scoprire altre opere.',

            'help_offer' => '🤝 <strong>Hai domande?</strong> Posso spiegarti come funzionano gli EGI, cosa sono i progetti EPP, come acquistare, rivendere e molto altro!',

            'cta' => 'Chiedimi pure: "Come funziona un acquisto?", "Cos\'è l\'EPP collegato?", "Quali EGI sono disponibili?"',
        ],

        /**
         * GUEST VIEW - Marketing & Call-to-Action
         *
         * Shown when a non-logged-in user views the collection.
         * Focus: Explain platform value, encourage registration, showcase collection,
         * simplified onboarding messaging.
         */
        'guest' => [
            'greeting' => 'Benvenuto! 👋',
            'intro' => 'Stai esplorando la <strong>Collection ":collection_name"</strong> di <strong>:creator_name</strong> su <strong>FlorenceEGI</strong>.',

            'what_is_title' => '🌟 Cos\'è FlorenceEGI',
            'what_is_description' => 'FlorenceEGI è il marketplace di <strong>arte digitale sostenibile</strong> dove ogni opera supporta progetti ambientali concreti. Gli <strong>EGI (Ecological Goods Invent)</strong> sono NFT con impatto reale.',

            'collection_title' => '🎨 Questa Collection',
            'collection_description' => 'Contiene <strong>:egi_count opere</strong> curate da :creator_name. Ogni EGI è un pezzo unico, certificato sulla blockchain.',

            'why_join_title' => '💡 Perché Registrarti',
            'why_join_reasons' => [
                'purchase' => '<strong>Acquista EGI:</strong> Diventa proprietario di arte digitale certificata',
                'support' => '<strong>Sostieni l\'Ambiente:</strong> Ogni acquisto finanzia progetti EPP',
                'collect' => '<strong>Colleziona:</strong> Crea il tuo portfolio personale di opere',
                'invest' => '<strong>Investi:</strong> Gli EGI possono aumentare di valore nel tempo',
            ],

            'how_it_works_title' => '🚀 Come Funziona',
            'how_it_works_steps' => [
                '1' => '<strong>Registrati gratuitamente</strong> su FlorenceEGI',
                '2' => '<strong>Esplora le collections</strong> e scopri gli EGI',
                '3' => '<strong>Acquista o prenota</strong> l\'EGI che ti piace',
                '4' => '<strong>Ricevi il certificato digitale</strong> e supporta l\'ambiente',
            ],

            'cta_title' => '🎯 Inizia Ora',
            'cta_register' => '<a href="/register" class="inline-block rounded-lg bg-gradient-to-r from-indigo-600 to-purple-600 px-4 py-2 font-semibold text-white hover:opacity-90">Registrati Gratis</a>',
            'cta_browse' => 'oppure <a href="/explore" class="text-indigo-400 hover:text-indigo-300 underline">esplora altre collections</a>',

            'help_offer' => '🤝 <strong>Hai domande?</strong> Posso spiegarti come funziona FlorenceEGI, cosa sono gli EGI e gli EPP, come acquistare e molto altro!',

            'cta_questions' => 'Chiedimi pure: "Cos\'è un EGI?", "Come funziona un acquisto?", "Perché registrarmi?"',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Future View Contexts
    |--------------------------------------------------------------------------
    |
    | Add more views following the same structure:
    |
    | 'epp.projects.show' => [
    |     'owner' => [...],
    |     'visitor' => [...],
    |     'guest' => [...],
    | ],
    */

    /*
    |--------------------------------------------------------------------------
    | Common Labels
    |--------------------------------------------------------------------------
    */

    'unknown_creator' => 'Creatore',

    'creator_types' => [
        'creator' => 'un artista/creatore',
        'company' => 'un brand/azienda',
        'collector' => 'un collezionista',
    ],
];
