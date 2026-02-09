<?php

return [
    /*
    |--------------------------------------------------------------------------
    | AI View Contexts - Italian
    |--------------------------------------------------------------------------
    |
    | Detailed, user-friendly explanations of each view.
    | Based on codebase analysis (controllers, models, routes, blade templates).
    | Injected into AI prompts for context-aware, accurate responses.
    |
    | Structure per ogni view:
    | - title: Nome pagina
    | - description: Descrizione breve
    | - features: Lista dettagliata features con azioni
    | - workflow_steps: Step-by-step guide (se applicabile)
    | - user_tasks: Task comuni per utenti first-time vs experienced
    | - common_questions: FAQ inline con risposte dirette
    | - tips: Best practices
    | - warnings: Attenzioni importanti
    |
    | @author FlorenceEGI Dev Team (analisi automatica codebase)
    | @version 1.0.0
    | @date 2026-02-09
    | @source CompanyHomeController.php, company/home-spa.blade.php, routes/web.php
    */

    'company' => [
        'dashboard' => [
            'title' => 'Company Home - Portfolio Aziendale',
            'description' => 'Pagina home della Company che mostra il portfolio di EGI creati e posseduti, con accesso a gestione collections, about aziendale e impact ambientale.',

            /*
            |--------------------------------------------------------------------------
            | Features della Pagina (analizzate da controller + view)
            |--------------------------------------------------------------------------
            */

            'features' => [
                'portfolio_tabs' => [
                    'name' => 'Portfolio a Doppia Modalità',
                    'description' => 'Visualizza EGI in due modalità separate: Created (creati dalla company) e Owned (acquistati da altri).',
                    'source' => 'CompanyHomeController::portfolio() - lines 69-180',
                    'actions' => [
                        'Visualizza "Portfolio Created": tutti gli EGI mintati dalla company',
                        'Switch a "Portfolio Owned": EGI acquistati sul mercato secondario',
                        'Filtra EGI per collection specifica',
                        'Cerca EGI per titolo (barra search)',
                        'Ordina per: Latest, Title, Price (High/Low)',
                        'Cambia vista: Grid o List',
                    ],
                    'ui_elements' => [
                        'Tab "Created" (default) - mostra EGI creati (exclude clones)',
                        'Tab "Owned" - mostra EGI posseduti ma non creati',
                        'Barra search con placeholder',
                        'Dropdown "Sort by" (latest, title, price_high, price_low)',
                        'Toggle Grid/List view',
                        'Dropdown "Collection filter"',
                    ],
                    'stats_shown' => [
                        'Total EGI in modalità corrente',
                        'Total Collections associate',
                        'Total Value EUR (somma prezzi EGI)',
                        'Total Reservations (prenotazioni attive)',
                        'Highest Offer (offerta più alta ricevuta)',
                        'Available EGI (senza prenotazioni)',
                        'Reserved EGI (con prenotazioni)',
                    ],
                    'route' => '/company/{id}',
                ],

                'collections_tab' => [
                    'name' => 'Collections Aziendali',
                    'description' => 'Mostra tutte le collections pubblicate dalla company con conteggio EGI per collection.',
                    'source' => 'CompanyHomeController::collections() - lines 232-253',
                    'actions' => [
                        'Visualizza lista collections pubblicate',
                        'Vedi numero EGI originali per collection',
                        'Click su collection per dettagli',
                    ],
                    'stats_shown' => [
                        'Total collections pubblicate',
                        'Total EGI in tutte le collections',
                        'Total supporters (placeholder, future)',
                    ],
                    'route' => '/company/{id}/collections',
                ],

                'about_tab' => [
                    'name' => 'About Aziendale',
                    'description' => 'Sezione informativa sulla company con bio, dati organizzazione e possibilità di edit (solo owner).',
                    'source' => 'CompanyHomeController::about() - lines 258-273',
                    'actions' => [
                        'Visualizza about/bio della company',
                        'Edit about field (solo se sei owner della company)',
                        'Visualizza dati organizzazione',
                    ],
                    'edit_capability' => [
                        'Solo owner della company può editare about',
                        'Validazione: max 5000 caratteri',
                        'Salvataggio in organizationData table',
                    ],
                    'route' => '/company/{id}/about',
                ],

                'impact_tab' => [
                    'name' => 'Environmental Impact (EPP)',
                    'description' => 'Mostra impatto ambientale della company tramite progetti EPP collegati.',
                    'source' => 'CompanyHomeController::impact() - lines 308-324',
                    'actions' => [
                        'Visualizza impact score della company',
                        'Vedi progetti EPP supportati tramite collections',
                        'Monitora contributo ambientale (future)',
                    ],
                    'stats_shown' => [
                        'Total collections con EPP',
                        'Total EGI creati',
                        'Impact score (placeholder, future development)',
                    ],
                    'route' => '/company/{id}/impact',
                ],

                'payment_settings' => [
                    'name' => 'Configurazione Pagamenti (Solo Owner)',
                    'description' => 'Bottone visibile solo all\'owner per configurare metodi pagamento (Stripe, Bank Transfer).',
                    'source' => 'company/home-spa.blade.php - lines 32-98',
                    'visibility' => 'Solo se auth()->id() === company->id',
                    'actions' => [
                        'Click bottone "Payment Settings" (desktop: bottom-right, mobile: FAB)',
                        'Apri modal configurazione pagamenti',
                        'Configura Stripe account',
                        'Configura Bank Transfer (IBAN)',
                    ],
                    'ui_design' => [
                        'Desktop: Bottone stilizzato carta credito in basso a destra',
                        'Mobile: FAB (Floating Action Button) arancione con icona carta',
                        'Gradient amber/yellow/orange con animazioni',
                    ],
                ],

                'onboarding_checklist' => [
                    'name' => 'Checklist Onboarding (Solo Owner)',
                    'description' => 'Sidebar con checklist passi onboarding per nuove company.',
                    'source' => 'CompanyHomeController::portfolio() - lines 176-178',
                    'visibility' => 'Solo se auth()->id() === company->id',
                    'provides' => 'OnboardingChecklistService genera checklist per archetype=company',
                ],
            ],

            /*
            |--------------------------------------------------------------------------
            | Workflow Utente Step-by-Step
            |--------------------------------------------------------------------------
            */

            'workflow_steps' => [
                'first_visit' => [
                    'title' => 'Prima Visita alla Company Home',
                    'steps' => [
                        'Arrivi sulla pagina home della company',
                        'Default: vedi Portfolio tab in modalità "Created"',
                        'Se sei owner: vedi bottone "Payment Settings" (bottom-right) e checklist onboarding (sidebar)',
                        'Esplora tab: Portfolio, Collections, About, Impact',
                    ],
                ],

                'view_portfolio' => [
                    'title' => 'Visualizzare Portfolio EGI',
                    'steps' => [
                        'Tab Portfolio (default)',
                        'Modalità "Created" mostra EGI mintati dalla company',
                        'Switch a "Owned" per vedere EGI acquistati',
                        'Usa search bar per cercare per titolo',
                        'Filtra per collection dal dropdown',
                        'Ordina: Latest, Title, Price High/Low',
                        'Cambia vista Grid <-> List',
                    ],
                ],

                'configure_payments' => [
                    'title' => 'Configurare Metodi Pagamento (Owner Only)',
                    'steps' => [
                        'Click bottone "Payment Settings" (desktop: bottom-right corner)',
                        'Su mobile: tap FAB arancione (bottom-right)',
                        'Modal si apre con opzioni: Stripe, Bank Transfer',
                        'Segui wizard per configurare metodo scelto',
                        'Salva configurazione',
                    ],
                ],

                'edit_about' => [
                    'title' => 'Editare About Aziendale (Owner Only)',
                    'steps' => [
                        'Vai a tab "About"',
                        'Click bottone "Edit About" (visibile solo a owner)',
                        'Scrivi bio/descrizione aziendale (max 5000 caratteri)',
                        'Salva modifiche',
                    ],
                ],
            ],

            /*
            |--------------------------------------------------------------------------
            | Task Comuni per Tipologia Utente
            |--------------------------------------------------------------------------
            */

            'user_tasks' => [
                'owner_first_time' => [
                    'title' => 'Owner - Prima Volta',
                    'description' => 'Task per company owner che visita per la prima volta la propria home page.',
                    'tasks' => [
                        'Completa onboarding checklist (sidebar)',
                        'Configura almeno un metodo pagamento (Stripe o Bank Transfer)',
                        'Compila sezione About aziendale',
                        'Crea almeno una collection per iniziare',
                        'Carica primi EGI di test',
                    ],
                ],

                'owner_experienced' => [
                    'title' => 'Owner - Utente Esperto',
                    'description' => 'Task per company owner che usa regolarmente la piattaforma.',
                    'tasks' => [
                        'Monitora stats portfolio (EGI venduti, reservations, offers)',
                        'Gestisci collections (crea nuove, aggiorna esistenti)',
                        'Controlla impact ambientale (tab Impact)',
                        'Ottimizza pricing basandosi su analytics',
                        'Rispondi a reservations/offers ricevute',
                    ],
                ],

                'visitor_public' => [
                    'title' => 'Visitatore Pubblico',
                    'description' => 'Utente che visita la home page pubblica di una company.',
                    'tasks' => [
                        'Esplora portfolio EGI della company',
                        'Naviga tra collections',
                        'Leggi About aziendale per capire brand values',
                        'Visualizza impact ambientale della company',
                        'Acquista EGI interessanti (redirect a marketplace)',
                    ],
                ],
            ],

            /*
            |--------------------------------------------------------------------------
            | Domande Comuni con Risposte Immediate
            |--------------------------------------------------------------------------
            */

            'common_questions' => [
                'cosa_vedo_portfolio' => [
                    'q' => 'Cosa vedo nel Portfolio?',
                    'a' => 'Tab "Created" mostra tutti gli EGI che hai mintato come company. Tab "Owned" mostra EGI che hai acquistato da altri creators. Cloni (PT) sono esclusi da "Created".',
                ],

                'come_filtro_egi' => [
                    'q' => 'Come filtro gli EGI visualizzati?',
                    'a' => 'Usa la barra search per cercare per titolo, dropdown "Collection filter" per filtrare per collection specifica, "Sort by" per ordinare (latest/title/price), e toggle Grid/List per cambiare vista.',
                ],

                'dove_configuro_pagamenti' => [
                    'q' => 'Dove configuro i metodi di pagamento?',
                    'a' => 'Se sei owner, vedi bottone "Payment Settings" in basso a destra (desktop) o FAB arancione (mobile). Click per aprire modal configurazione Stripe o Bank Transfer.',
                ],

                'come_edito_about' => [
                    'q' => 'Come edito la sezione About?',
                    'a' => 'Solo owner: vai a tab "About", click "Edit About", scrivi bio aziendale (max 5000 caratteri), salva. Visitatori pubblici vedono About ma non possono editare.',
                ],

                'cosa_sono_stats' => [
                    'q' => 'Cosa significano le statistiche mostrate?',
                    'a' => 'Total EGI = numero EGI in modalità corrente; Total Collections = collections associate; Total Value EUR = somma prezzi EGI; Reservations = prenotazioni pre-launch attive; Highest Offer = offerta massima ricevuta; Available/Reserved = EGI liberi o prenotati.',
                ],

                'differenza_created_owned' => [
                    'q' => 'Qual è la differenza tra Created e Owned?',
                    'a' => 'Created = EGI mintati da te come company (sei il creator originale). Owned = EGI che hai acquistato da altri creators sul mercato secondario (sei owner ma non creator). Il tuo nome appare come creator negli EGI Created, come owner negli Owned.',
                ],
            ],

            /*
            |--------------------------------------------------------------------------
            | Tips & Best Practices
            |--------------------------------------------------------------------------
            */

            'tips' => [
                'Completa onboarding checklist per sbloccare tutte le funzionalità',
                'Configura metodi pagamento PRIMA di abilitare commerce sulle collections',
                'Usa nomi descrittivi per EGI e collections per migliorare ricercabilità',
                'Monitora tab Impact per comunicare valore ambientale ai clienti',
                'Compila About dettagliato per raccontare brand story e values aziendali',
                'Usa search e filtri per navigare velocemente portfolio con molti EGI',
                'Controlla regolarmente Reservations (prenotazioni) per non perdere opportunità vendita',
                'Se hai sia Created che Owned, usa tab switching per tenere separati portfolio personale e collections',
            ],

            /*
            |--------------------------------------------------------------------------
            | Warnings & Attenzioni
            |--------------------------------------------------------------------------
            */

            'warnings' => [
                'Il bottone "Payment Settings" è visibile SOLO se sei l\'owner della company',
                'Non puoi editare About di altre company, solo della tua',
                'Stats mostrate variano in base a modalità Portfolio (Created vs Owned)',
                'EGI cloni (PT) NON appaiono in tab "Created" ma possono apparire in "Owned" se li possiedi',
                'Se elimini una collection, tutti gli EGI associati potrebbero essere influenzati (verifica dipendenze prima)',
                'Visitors pubblici vedono solo EGI published (is_published = true)',
                'Owner vede anche EGI unpublished nel proprio portfolio',
            ],

            /*
            |--------------------------------------------------------------------------
            | Technical Details (opzionale, per AI context)
            |--------------------------------------------------------------------------
            */

            'technical_info' => [
                'controller' => 'App\Http\Controllers\CompanyHomeController',
                'main_method' => 'portfolio()',
                'view' => 'company.home-spa',
                'route_name' => 'company.portfolio',
                'route_pattern' => '/company/{id}',
                'archetype_required' => 'company',
                'uses_spa' => true,
                'ajax_enabled' => true,
                'eager_loads' => ['collection', 'user', 'owner', 'blockchain.buyer', 'traits.category', 'reservations'],
                'filters' => ['query', 'collection', 'sort', 'view', 'mode'],
                'sort_options' => ['latest', 'title', 'price_high', 'price_low'],
                'view_modes' => ['grid', 'list'],
                'portfolio_modes' => ['created', 'owned'],
            ],
        ],

        // Altri view contexts (da aggiungere in futuro)
        // 'collections' => [...],
        // 'about' => [...],
        // etc.
    ],

    // Altri archetipi (da aggiungere in futuro)
    // 'creator' => [...],
    // 'collector' => [...],
    // 'epp' => [...],
    // 'pa' => [...],
];
