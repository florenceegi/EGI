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
        'portfolio' => [
            'title' => 'Company Portfolio - EGI Created & Owned',
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

                'epp_company' => [
                    'q' => 'Gli EPP sono obbligatori per le Company?',
                    'a' => 'NO. Per le Company gli EPP sono OPZIONALI. A differenza dei Creator (per cui EPP 20% è obbligatorio), le Company possono scegliere: (1) Supportare un EPP con percentuale variabile a loro scelta, oppure (2) Offrire abbonamenti ai loro contenuti, oppure (3) Nessuno dei due. Le Company hanno piena flessibilità nella scelta del modello di sostenibilità.',
                ],

                'epp_listino' => [
                    'q' => 'Quali percentuali EPP può scegliere una Company?',
                    'a' => 'Le Company possono scegliere liberamente la percentuale da destinare all\'EPP selezionato. Non esiste un minimo obbligatorio. Tipicamente si sceglie tra 5% e 20%, ma ogni Company decide autonomamente in base alla propria strategia di sostenibilità e posizionamento di brand.',
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
                'IMPORTANTE: Company ≠ Creator. Per le Company gli EPP sono OPZIONALI (non obbligatori). Creator devono usare EPP 20%, Company scelgono liberamente.',
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

        'collections' => [
            'title' => 'Company Collections - Brand Catalog',
            'description' => 'Pagina Collections della Company che mostra tutte le collections pubblicate con numero opere per collection.',

            'features' => [
                'collections_grid' => [
                    'name' => 'Griglia Collections Aziendali',
                    'description' => 'Visualizza tutte le collections pubblicate dalla company in formato grid.',
                    'source' => 'CompanyHomeController::collections() - lines 232-253',
                    'actions' => [
                        'Visualizza tutte le collections pubblicate',
                        'Click su collection card per vedere dettagli',
                        'Vedi numero EGI originali per collection',
                    ],
                    'ui_elements' => [
                        'Grid responsive (1-4 colonne)',
                        'Collection cards con immagine, titolo, conteggio EGI',
                        'Empty state se nessuna collection',
                    ],
                    'stats_shown' => [
                        'Total collections pubblicate',
                        'Total EGI in tutte le collections',
                        'Total supporters (placeholder)',
                    ],
                    'route' => '/company/{id}/collections',
                ],
            ],

            'common_questions' => [
                'cosa_sono_collections' => [
                    'q' => 'Cosa sono le Collections della Company?',
                    'a' => 'Le Collections sono cataloghi tematici che raggruppano prodotti/EGI aziendali. Ogni collection rappresenta una linea di prodotto, una campagna o una categoria specifica del brand. Le Company possono creare e gestire collections per organizzare il proprio catalogo.',
                ],

                'epp_collections_company' => [
                    'q' => 'Le Collections Company devono avere un EPP?',
                    'a' => 'NO, è OPZIONALE. Le Company possono scegliere liberamente se associare un EPP alle proprie collections (con percentuale variabile) oppure offrire abbonamenti, oppure nessuno dei due. La scelta è flessibile.',
                ],

                'come_creare_collection' => [
                    'q' => 'Come creo una nuova Collection come Company?',
                    'a' => 'Se sei owner della company, vai al menu di gestione (non visibile in questa pagina pubblica) e seleziona "Crea Collection". Potrai definire nome, descrizione, EPP opzionale e iniziare ad aggiungere EGI.',
                ],
            ],

            'warnings' => [
                'Questa pagina mostra SOLO collections pubblicate (is_published = true)',
                'Visitors pubblici vedono le collections ma non possono modificarle',
                'Owner può creare/modificare collections dal pannello di gestione (non da questa pagina)',
                'EPP è OPZIONALE per Company collections (diverso da Creator dove è obbligatorio 20%)',
            ],

            'technical_info' => [
                'controller' => 'App\\Http\\Controllers\\CompanyHomeController',
                'main_method' => 'collections()',
                'view' => 'company.partials.collections-content',
                'route_name' => 'company.collections',
                'route_pattern' => '/company/{id}/collections',
                'archetype_required' => 'company',
            ],
        ],

        // Altri view contexts (da aggiungere in futuro)
        // 'about' => [...],
        // etc.
    ],

    /*
    |--------------------------------------------------------------------------
    | CREATOR ARCHETYPE
    |--------------------------------------------------------------------------
    */

    'creator' => [
        'portfolio' => [
            'title' => 'Creator Portfolio - Works & Biography',
            'description' => 'Pagina home del Creator che mostra il portfolio di opere create e possedute, con accesso a collections, biography, impact ambientale e community.',

            'features' => [
                'portfolio_tabs' => [
                    'name' => 'Portfolio a Doppia Modalità',
                    'description' => 'Visualizza opere in due modalità separate: Created (create dal creator) e Owned (acquistate da altri).',
                    'source' => 'CreatorHomeController::portfolio() - lines 71-193',
                    'actions' => [
                        'Visualizza "Portfolio Created": tutte le opere mintate dal creator',
                        'Switch a "Portfolio Owned": opere acquistate sul mercato secondario',
                        'Filtra opere per collection specifica',
                        'Cerca opere per titolo (barra search)',
                        'Ordina per: Latest, Title, Price (High/Low)',
                        'Cambia vista: Grid o List',
                    ],
                    'ui_elements' => [
                        'Tab "Created" (default) - mostra opere create (exclude clones)',
                        'Tab "Owned" - mostra opere possedute ma non create',
                        'Barra search con placeholder',
                        'Dropdown "Sort by" (latest, title, price_high, price_low)',
                        'Toggle Grid/List view',
                        'Dropdown "Collection filter"',
                    ],
                    'stats_shown' => [
                        'Total Works (opere in modalità corrente)',
                        'Total Collections associate',
                        'Total Patrons (supporters)',
                    ],
                    'route' => '/creator/{id}/portfolio',
                ],

                'payment_settings' => [
                    'name' => 'Configurazione Pagamenti (Solo Owner)',
                    'description' => 'Bottone visibile solo all\'owner per configurare metodi pagamento (Stripe, Bank Transfer).',
                    'source' => 'creator/home-spa.blade.php - lines 8-74',
                    'visibility' => 'Solo se auth()->id() === creator->id',
                    'actions' => [
                        'Click bottone "Payment Settings" (desktop: bottom-right, mobile: FAB)',
                        'Apri modal configurazione pagamenti',
                        'Configura Stripe account',
                        'Configura Bank Transfer (IBAN)',
                    ],
                ],

                'onboarding_checklist' => [
                    'name' => 'Checklist Onboarding (Solo Owner)',
                    'description' => 'Sidebar con checklist passi onboarding per nuovi creator.',
                    'source' => 'CreatorHomeController::portfolio() - lines 170-173',
                    'visibility' => 'Solo se auth()->id() === creator->id',
                    'steps' => ['stripe', 'avatar', 'banner', 'bio', 'collection', 'first_egi', 'social_links'],
                ],
            ],

            'common_questions' => [
                'cosa_vedo_portfolio' => [
                    'q' => 'Cosa vedo nel Portfolio del Creator?',
                    'a' => 'Tab "Created" mostra tutte le opere che il creator ha creato e mintato. Tab "Owned" mostra opere che il creator ha acquistato da altri artisti. Cloni (PT) sono esclusi da "Created".',
                ],

                'differenza_created_owned' => [
                    'q' => 'Qual è la differenza tra Created e Owned per un Creator?',
                    'a' => 'Created = opere mintate dal creator (è l\'autore originale). Owned = opere acquistate da altri creator sul mercato secondario (il creator è owner ma non autore).',
                ],

                'epp_creator' => [
                    'q' => 'Gli EPP sono obbligatori per i Creator?',
                    'a' => 'SÌ. Per i Creator gli EPP sono OBBLIGATORI al 20%. Ogni collection deve destinare il 20% del ricavato a un progetto EPP selezionato. Questo è un requisito fondamentale per tutti i Creator sulla piattaforma FlorenceEGI.',
                ],

                'cosa_biography' => [
                    'q' => 'Cosa trovo nella Biography?',
                    'a' => 'La Biography è la sezione dove il creator racconta il proprio percorso artistico, background, stile creativo e vision. È il luogo dove scoprire la storia dietro le opere.',
                ],

                'dove_configuro_pagamenti' => [
                    'q' => 'Dove configuro i metodi di pagamento come Creator?',
                    'a' => 'Se sei owner, vedi bottone "Payment Settings" in basso a destra (desktop) o FAB arancione (mobile). Click per aprire modal configurazione Stripe o Bank Transfer.',
                ],
            ],

            'warnings' => [
                'IMPORTANTE: Creator ≠ Company. Per i Creator gli EPP sono OBBLIGATORI al 20%. Le Company invece possono scegliere liberamente (EPP opzionale o abbonamenti).',
                'Il bottone "Payment Settings" è visibile SOLO se sei l\'owner del profilo creator',
                'Stats mostrate variano in base a modalità Portfolio (Created vs Owned)',
                'EGI cloni (PT) NON appaiono in tab "Created" ma possono apparire in "Owned" se li possiedi',
            ],

            'technical_info' => [
                'controller' => 'App\\Http\\Controllers\\CreatorHomeController',
                'main_method' => 'portfolio()',
                'view' => 'creator.home-spa',
                'route_name' => 'creator.portfolio',
                'route_pattern' => '/creator/{id}/portfolio',
                'archetype_required' => 'creator',
            ],
        ],

        'collections' => [
            'title' => 'Creator Collections - Artistic Series',
            'description' => 'Pagina Collections del Creator che mostra tutte le collections artistiche pubblicate con numero opere per collection.',

            'features' => [
                'collections_grid' => [
                    'name' => 'Griglia Collections Artistiche',
                    'description' => 'Visualizza tutte le collections pubblicate dal creator in formato grid.',
                    'source' => 'CreatorHomeController::collectionsSection() - lines 301-326',
                    'actions' => [
                        'Visualizza tutte le collections pubblicate',
                        'Click su collection card per vedere dettagli',
                        'Vedi numero opere originali per collection',
                    ],
                    'ui_elements' => [
                        'Grid responsive (1-4 colonne)',
                        'Collection cards con immagine, titolo, conteggio opere',
                        'Empty state se nessuna collection',
                    ],
                    'stats_shown' => [
                        'Total collections pubblicate',
                        'Total opere in tutte le collections',
                        'Total supporters (placeholder)',
                    ],
                    'route' => '/creator/{id}/collections',
                ],
            ],

            'common_questions' => [
                'cosa_sono_collections' => [
                    'q' => 'Cosa sono le Collections del Creator?',
                    'a' => 'Le Collections sono serie artistiche che raggruppano opere tematicamente correlate. Ogni collection rappresenta un progetto artistico, una serie creativa o un tema specifico. I Creator possono creare e gestire collections per organizzare il proprio lavoro.',
                ],

                'epp_collections_creator' => [
                    'q' => 'Le Collections Creator devono avere un EPP?',
                    'a' => 'SÌ, è OBBLIGATORIO. Ogni collection del Creator deve destinare il 20% del ricavato a un progetto EPP selezionato. Questo è un requisito fondamentale per tutti i Creator sulla piattaforma FlorenceEGI.',
                ],

                'come_creare_collection' => [
                    'q' => 'Come creo una nuova Collection come Creator?',
                    'a' => 'Se sei owner del profilo creator, vai al menu di gestione e seleziona "Crea Collection". Dovrai definire nome, descrizione, selezionare un progetto EPP (20% obbligatorio) e iniziare ad aggiungere le tue opere.',
                ],

                'differenza_company_creator' => [
                    'q' => 'Differenza tra Collections Creator e Company?',
                    'a' => 'Creator: collections ARTISTICHE con EPP obbligatorio 20%, focus su serie creative e vision artistica. Company: collections BRAND/PRODOTTO con EPP opzionale, focus su cataloghi e linee di prodotto. Creator = arte, Company = business.',
                ],
            ],

            'warnings' => [
                'IMPORTANTE: EPP è OBBLIGATORIO 20% per TUTTE le collections Creator (diverso da Company dove è opzionale)',
                'Questa pagina mostra SOLO collections pubblicate (is_published = true)',
                'Visitors pubblici vedono le collections ma non possono modificarle',
                'Owner può creare/modificare collections dal pannello di gestione',
            ],

            'technical_info' => [
                'controller' => 'App\\Http\\Controllers\\CreatorHomeController',
                'main_method' => 'collectionsSection()',
                'view' => 'creator.partials.collections-content',
                'route_name' => 'creator.collections',
                'route_pattern' => '/creator/{id}/collections',
                'archetype_required' => 'creator',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | COLLECTOR ARCHETYPE
    |--------------------------------------------------------------------------
    */

    'collector' => [
        'portfolio' => [
            'title' => 'Collector Portfolio - Purchased Works',
            'description' => 'Pagina home del Collector che mostra il portfolio di opere acquistate/prenotate, con accesso a collections e statistiche personali.',

            'features' => [
                'portfolio_main' => [
                    'name' => 'Portfolio Acquisti',
                    'description' => 'Visualizza tutte le opere acquistate o prenotate dal collector tramite reservations.',
                    'source' => 'CollectorHomeController::portfolio() - lines 141-231',
                    'actions' => [
                        'Visualizza tutte le opere acquistate/prenotate',
                        'Filtra opere per collection specifica',
                        'Filtra opere per creator specifico',
                        'Cerca opere per titolo (barra search)',
                        'Ordina per: Latest, Title, Price (High/Low)',
                        'Cambia vista: Grid o List',
                    ],
                    'stats_shown' => [
                        'Total Owned EGIs (opere possedute)',
                        'Total Spent (spesa totale in EUR)',
                        'Collections Represented (collections da cui hai acquistato)',
                        'Active Reservations (prenotazioni attive)',
                    ],
                    'route' => '/collector/{id}/portfolio',
                ],

                'payment_settings' => [
                    'name' => 'Configurazione Pagamenti (Solo Owner)',
                    'description' => 'Bottone visibile solo all\'owner per configurare metodi pagamento. NUOVO: Collector può ora ricevere pagamenti da rivendite.',
                    'source' => 'collector/portfolio.blade.php - lines 26-91',
                    'visibility' => 'Solo se auth()->id() === collector->id',
                    'actions' => [
                        'Click bottone "Payment Settings" (desktop: bottom-right, mobile: FAB)',
                        'Configura Stripe account per ricevere pagamenti da rivendite',
                        'Configura Bank Transfer (IBAN)',
                    ],
                ],

                'onboarding_checklist' => [
                    'name' => 'Checklist Onboarding (Solo Owner)',
                    'description' => 'Sidebar con checklist passi onboarding per nuovi collector.',
                    'source' => 'CollectorHomeController::portfolio() - lines 213-216',
                    'visibility' => 'Solo se auth()->id() === collector->id',
                    'steps' => ['stripe', 'verify_email', 'avatar', 'banner', 'bio', 'social_links'],
                    'note' => 'NO collection/first_egi (collector non crea opere)',
                ],
            ],

            'common_questions' => [
                'cosa_vedo_portfolio' => [
                    'q' => 'Cosa vedo nel Portfolio del Collector?',
                    'a' => 'Il Portfolio mostra tutte le opere che hai acquistato o prenotato tramite reservations. Include opere da diversi creator e collections. Si concentra sugli ACQUISTI, non su opere create (i Collector non creano opere).',
                ],

                'epp_collector' => [
                    'q' => 'Gli EPP riguardano anche i Collector?',
                    'a' => 'NO. Gli EPP sono destinati SOLO a Creator e Company (venditori). I Collector sono ACQUIRENTI, quindi non hanno obblighi o opzioni EPP. Quando acquisti un\'opera, l\'EPP è già incluso nel prezzo dal creator/company.',
                ],

                'differenza_collector_creator' => [
                    'q' => 'Qual è la differenza tra Collector e Creator?',
                    'a' => 'Creator CREA opere originali e le minta sulla piattaforma (artisti). Collector ACQUISTA opere create da altri (appassionati, collezionisti). Creator ha EPP obbligatorio 20%, Collector no.',
                ],

                'posso_vendere_opere' => [
                    'q' => 'Posso rivendere opere come Collector?',
                    'a' => 'SÌ. Puoi rivendere opere sul mercato secondario. Per ricevere pagamenti, configura i metodi di pagamento tramite il bottone "Payment Settings" (disponibile per Collector owner).',
                ],

                'dove_configuro_pagamenti' => [
                    'q' => 'Dove configuro i metodi di pagamento come Collector?',
                    'a' => 'Se sei owner, vedi bottone "Payment Settings" in basso a destra (desktop) o FAB arancione (mobile). Configura Stripe o Bank Transfer per ricevere pagamenti da rivendite sul mercato secondario.',
                ],
            ],

            'warnings' => [
                'IMPORTANTE: Collector ≠ Creator. I Collector ACQUISTANO opere, non le creano. Non hanno EPP obbligatori.',
                'Il bottone "Payment Settings" è visibile SOLO se sei l\'owner del profilo collector',
                'Portfolio mostra SOLO opere acquistate/prenotate, non opere create',
                'Stats mostrano Total Spent (spesa totale), non ricavi',
                'Per rivendere opere sul mercato secondario, devi configurare metodi pagamento',
            ],

            'technical_info' => [
                'controller' => 'App\\Http\\Controllers\\CollectorHomeController',
                'main_method' => 'portfolio()',
                'view' => 'collector.portfolio',
                'route_name' => 'collector.home',
                'route_pattern' => '/collector/{id}',
                'archetype_required' => 'collector',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Collection Public Page
    |--------------------------------------------------------------------------
    */

    'collection' => [
        'show' => [
            'title' => 'Pagina Collection - Gallery EGI Pubblica',
            'description' => 'Pagina pubblica di dettaglio di una singola collection, visibile a tutti (visitor, collector, creator). Mostra gli EGI della collection, informazioni sul creator, eventuale progetto EPP collegato, statistiche.',

            'features' => [
                'collection_info' => [
                    'name' => 'Informazioni Collection',
                    'description' => 'Hero banner con titolo, descrizione, creator info, banner image personalizzabile (solo owner).',
                    'source' => 'CollectionsController::show() - lines 191-261, collections/show.blade.php',
                    'actions' => [
                        'Visualizza nome e descrizione collection',
                        'Vedi avatar e nome del creator (cliccabile per andare al suo profilo)',
                        'Badge "Official EPP Collection" se è una collection istituzionale EPP',
                        'Upload banner (solo se sei il creator/owner della collection)',
                        'Breadcrumb per tornare all\'indice collections',
                    ],
                ],

                'egi_grid' => [
                    'name' => 'Griglia EGI della Collection',
                    'description' => 'Grid di tutti gli EGI appartenenti alla collection, con preview immagine, titolo, ID.',
                    'source' => 'collections/show.blade.php - lines 484-571',
                    'actions' => [
                        'Visualizza tutti gli EGI in grid responsive (2 col mobile, 4 desktop, 5 widescreen)',
                        'Click su EGI per andare alla pagina dettaglio EGI',
                        'View mode switcher (grid/list/holders/traits) - solo in show normale',
                        'Sort EGI per: Position, Newest, Oldest, Price Low-High, Price High-Low',
                        'Filtra EGI se sei il creator (vedi anche unpublished), visitor vede solo published',
                    ],
                ],

                'epp_project' => [
                    'name' => 'Progetto Ambientale EPP',
                    'description' => 'Se la collection supporta un progetto EPP, mostra dettagli del progetto ambientale (ARF/APR/BPE).',
                    'source' => 'collections/show.blade.php - lines 376-493',
                    'actions' => [
                        'Vedi nome, descrizione, avatar del progetto EPP',
                        'Progress bar della completion percentage del progetto',
                        'Badge tipo progetto (ARF = Foreste, APR = Acqua, BPE = Biodiversità)',
                        'Info organizzazione EPP che gestisce il progetto',
                        'Percentuale di vendita che va al progetto (20% creator obbligatorio, company volontario)',
                    ],
                ],

                'payment_stats' => [
                    'name' => 'Statistiche Payment Distribution',
                    'description' => 'Mostra come vengono distribuiti i pagamenti delle vendite EGI (creator, EPP, platform fee).',
                    'source' => 'collections/show.blade.php - lines 45-62, x-hero-banner-stats component',
                    'actions' => [
                        'Visualizza split percentuali pagamenti (desktop & mobile)',
                        'Vedi quanto va al creator, quanto all\'EPP, quanto alla piattaforma',
                    ],
                ],

                'actions_owner' => [
                    'name' => 'Azioni Owner (Solo Creator)',
                    'description' => 'Se sei il creator della collection, hai accesso a funzionalità extra di gestione.',
                    'source' => 'collections/show.blade.php - lines 176-214',
                    'actions' => [
                        'Edit: Modifica nome, descrizione, visibility della collection (modale)',
                        'Dashboard: Apri dashboard monetization (EPP, subscription, stats)',
                        'Commerce Setup: Configura wizard commerce per vendite',
                        'Team Management: Gestisci team members con permission (create_team)',
                        'Share: Condividi link collection (copy to clipboard o native share API)',
                        'Like: Like/unlike collection (tutti possono farlo, non solo owner)',
                    ],
                ],

                'related_collections' => [
                    'name' => 'Collezioni Correlate',
                    'description' => 'Mostra altre collections dello stesso creator per discovery.',
                    'source' => 'collections/show.blade.php - lines 617-633',
                    'actions' => [
                        'Vedi fino a 3 altre collections dello stesso creator',
                        'Click per navigare ad altra collection',
                    ],
                ],
            ],

            'workflow_steps' => [
                [
                    'step' => 1,
                    'title' => 'Esplora la Collection',
                    'description' => 'Scrolla la gallery EGI, leggi descrizione collection e info sul creator.',
                ],
                [
                    'step' => 2,
                    'title' => 'Seleziona un EGI di Interesse',
                    'description' => 'Click su EGI card per aprire pagina dettaglio EGI e vedere prezzo, traits, disponibilità.',
                ],
                [
                    'step' => 3,
                    'title' => 'Acquista o Riserva EGI',
                    'description' => 'Dalla pagina EGI, acquista direttamente (se abilitato) o fai reservation se serve approval creator.',
                ],
                [
                    'step' => 4,
                    'title' => 'Supporta il Progetto EPP (Se Presente)',
                    'description' => 'Se la collection ha EPP, una percentuale della tua spesa va automaticamente al progetto ambientale.',
                ],
            ],

            'user_tasks' => [
                'first_time' => [
                    'Leggi descrizione collection per capire il tema e lo stile',
                    'Guarda chi è il creator (click sull\'avatar per vedere il suo profilo completo)',
                    'Se c\'è un EPP, scopri quale progetto ambientale stai supportando',
                    'Esplora la gallery EGI e familiarizza con i sorting/filtering',
                ],
                'experienced' => [
                    'Usa view selector per passare da grid a list a holders view',
                    'Usa sorting per trovare velocemente EGI per prezzo o data',
                    'Check dashboard monetization se sei owner (gestisci EPP, subscription)',
                    'Invita team members se sei owner (team management button)',
                ],
            ],

            'common_questions' => [
                'come_acquistare' => [
                    'q' => 'Come acquisto un EGI da questa collection?',
                    'a' => 'Click su qualsiasi EGI card nella gallery per aprire la pagina dettaglio. Lì troverai il pulsante "Acquista" o "Riserva" (se serve approvazione creator). Segui il checkout flow con pagamento Stripe o Egili (valuta interna).',
                ],
                'epp_cosa' => [
                    'q' => 'Cos\'è l\'EPP mostrato in questa collection?',
                    'a' => 'EPP = Enhanced Percentage Program (Ecological Goods Invent). È un progetto ambientale verificato (Riforestazione ARF, Protezione Acqua APR, Biodiversità BPE) che riceve una percentuale delle vendite. Per Creator è OBBLIGATORIO 20%. Per Company è VOLONTARIO (scelgono loro la %)',
                ],
                'creator_contact' => [
                    'q' => 'Come posso contattare il creator di questa collection?',
                    'a' => 'Click sull\'avatar o nome del creator nell\'hero banner per andare al suo profilo. Da lì troverai eventuali link social (Instagram, Twitter, Website) e potrai vedere tutte le sue altre collections.',
                ],
                'differenza_view' => [
                    'q' => 'Cosa cambia tra Grid, List e Holders view?',
                    'a' => 'Grid = card visuale con immagine (default). List = riga compatta con info tabellare. Holders = mostra i collector che possiedono EGI di questa collection. Traits = metadata attributi (coming soon).',
                ],
                'owner_dashboard' => [
                    'q' => 'A cosa serve il bottone Dashboard (solo owner)?',
                    'a' => 'Se sei il creator, Dashboard ti permette di: 1) Gestire monetizzazione (EPP selection, subscription tier), 2) Vedere statistiche vendite, 3) Configurare commerce setup wizard. È il centro controllo della collection.',
                ],
                'posso_rivendere' => [
                    'q' => 'Posso rivendere un EGI che ho comprato da questa collection?',
                    'a' => 'SÌ! Gli EGI sono NFT trasferibili. Vai al tuo portfolio Owned, click sull\'EGI e usa "Sell" o "Transfer". Il mercato secondario è fully supported con royalty al creator originale.',
                ],
            ],

            'tips' => [
                'Usa il sorting "Price Low to High" per trovare entry point economici nella collection',
                'Se vedi un EPP badge verde, significa che stai supportando progetti ambientali reali acquistando',
                'Controlla "Related Collections" in fondo per scoprire altri lavori dello stesso creator',
                'Il creator vede anche EGI non pubblicati (drafts), tu come visitor vedi solo published',
            ],

            'warnings' => [
                'Solo il creator può modificare nome/descrizione/banner della collection',
                'Solo il creator può configurare EPP e monetizzazione (Dashboard)',
                'Gli EGI in questa gallery possono avere diversi stati (published, reserved, sold)',
                'Se la collection ha subscription attiva, il creator può mintare unlimited EGI (no per-mint fee)',
            ],

            'technical_info' => [
                'controller' => 'App\Http\Controllers\CollectionsController',
                'methods' => ['show($id)'],
                'route_name' => 'collections.show',
                'route_pattern' => '/collections/{id}',
                'views' => [
                    'collections.show' => 'Vista normale con dashboard, monetization, view switcher',
                    'collections.show-epp' => 'Vista semplificata per EPP institutional collections',
                ],
                'models' => ['Collection', 'Egi', 'EppProject', 'User (creator)'],
                'permissions' => [
                    'create_collection' => 'Edit, Dashboard, Upload banner, Commerce setup',
                    'create_team' => 'Team Management button',
                ],
                'eager_loading' => [
                    'creator', 'epp', 'eppProject', 'egis', 'likes', 'reservations',
                ],
                'filtering' => 'Creator sees all EGIs (published + unpublished), visitors see only published',
            ],

            // AI Sidebar Context Messages (role-based)
            'sidebar_contexts' => [
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
                    'help_offer' => '🤝 <strong>Posso aiutarti con:</strong> Strategie di pricing, configurazione EPP, ottimizzazione SEO, gestione inventario, analytics. Chiedimi pure!',
                    'cta' => 'Vuoi che ti guidi passo dopo passo? Chiedimi "Come ottimizzare la mia collection?" oppure "Cosa devo fare per vendere di più?"',
                ],
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
                    'help_offer' => '🤝 <strong>Hai domande?</strong> Posso spiegarti come funzionano gli EGI, cosa sono i progetti EPP, come acquistare, rivendere e molto altro!',
                    'cta' => 'Chiedimi pure: "Come funziona un acquisto?", "Cos\'è l\'EPP collegato?", "Quali EGI sono disponibili?"',
                ],
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
                    'cta_register' => '<a href="/register" class="inline-block rounded-lg bg-gradient-to-r from-indigo-600 to-purple-600 px-4 py-2 font-semibold text-white hover:opacity-90">Registrati Gratis</a>',
                    'cta_browse' => 'oppure <a href="/explore" class="text-indigo-400 hover:text-indigo-300 underline">esplora altre collections</a>',
                    'help_offer' => '🤝 <strong>Hai domande?</strong> Posso spiegarti come funziona FlorenceEGI, cosa sono gli EGI e gli EPP, come acquistare e molto altro!',
                    'cta_questions' => 'Chiedimi pure: "Cos\'è un EGI?", "Come funziona un acquisto?", "Perché registrarmi?"',
                ],
                'creator_types' => [
                    'creator' => 'un artista/creatore',
                    'company' => 'un brand/azienda',
                    'collector' => 'un collezionista',
                ],
                'unknown_creator' => 'Creatore',
            ],
        ],
    ],

    // Altri archetipi (da aggiungere in futuro)
    // 'epp' => [...],
    // 'pa' => [...],
];
