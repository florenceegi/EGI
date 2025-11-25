<?php

/**
 * @package Resources\Lang\It
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Natan Tutor)
 * @date 2025-01-XX
 * @purpose Traduzioni sistema Natan Tutor - Assistente operativo
 */

return [
    // === GENERAL ===
    'title' => 'Natan Tutor',
    'subtitle' => 'Il tuo assistente personale per la piattaforma',
    'tagline' => 'Ti guido passo passo nelle operazioni di FlorenceEGI',

    // === MODES ===
    'modes' => [
        'tutoring' => [
            'name' => 'Modalità Tutorial',
            'description' => 'Spiegazioni dettagliate e guida passo passo',
            'hint' => 'Ideale per imparare come funziona la piattaforma',
        ],
        'expert' => [
            'name' => 'Modalità Esperto',
            'description' => 'Scorciatoie rapide per azioni dirette',
            'hint' => 'Per utenti esperti che vogliono risparmiare tempo',
        ],
    ],

    // === ACTIONS ===
    'actions' => [
        'navigate' => [
            'name' => 'Navigazione',
            'description' => 'Ti porto alla pagina che cerchi',
        ],
        'explain' => [
            'name' => 'Spiegazione',
            'description' => 'Ti spiego come funziona questa funzionalità',
        ],
        'mint' => [
            'name' => 'Assistenza Mint',
            'description' => 'Ti guido nella creazione del tuo EGI',
        ],
        'reserve' => [
            'name' => 'Assistenza Prenotazione',
            'description' => 'Ti guido nella prenotazione di un EGI',
        ],
        'purchase' => [
            'name' => 'Assistenza Acquisto',
            'description' => 'Ti aiuto ad acquistare Egili',
        ],
        'collection_create' => [
            'name' => 'Crea Collezione',
            'description' => 'Ti guido nella creazione di una collezione',
        ],
    ],

    // === NAVIGATION DESTINATIONS ===
    'navigation' => [
        'home' => [
            'name' => 'Home',
            'explanation' => 'La pagina principale di FlorenceEGI, dove puoi vedere le novità e le collezioni in evidenza.',
        ],
        'dashboard' => [
            'name' => 'Dashboard',
            'explanation' => 'Il tuo pannello di controllo personale con statistiche e attività recenti.',
        ],
        'collections' => [
            'name' => 'Collezioni',
            'explanation' => 'Visualizza e gestisci le tue collezioni di EGI.',
        ],
        'explore' => [
            'name' => 'Esplora',
            'explanation' => 'Scopri collezioni ed EGI di altri creatori sulla piattaforma.',
        ],
        'mint' => [
            'name' => 'Mint',
            'explanation' => 'Crea un nuovo EGI caricando la tua opera digitale.',
        ],
        'profile' => [
            'name' => 'Profilo',
            'explanation' => 'Visualizza e modifica il tuo profilo pubblico.',
        ],
        'wallet' => [
            'name' => 'Wallet',
            'explanation' => 'Gestisci il tuo portafoglio Algorand e le transazioni.',
        ],
        'egili' => [
            'name' => 'Egili',
            'explanation' => 'Gestisci il tuo saldo Egili, acquista crediti o visualizza lo storico.',
        ],
        'default_explanation' => 'Questa pagina ti permette di accedere a funzionalità avanzate della piattaforma.',
    ],

    // === FEATURES EXPLANATIONS ===
    'features' => [
        'mint' => [
            'explanation' => 'Il Mint è il processo di creazione di un EGI (Entità Generatrice di Identità). Carichi la tua opera digitale (immagine, video, audio) e la registri sulla blockchain Algorand.',
            'tips' => [
                'Scegli un titolo accattivante e descrittivo',
                'Carica media di alta qualità (min 1000px per lato)',
                'Scrivi una descrizione che racconti la storia dell\'opera',
            ],
        ],
        'collection' => [
            'explanation' => 'Una Collezione è un contenitore per raggruppare i tuoi EGI. Puoi avere più collezioni tematiche.',
            'tips' => [
                'Dai un nome che rappresenti il tema della collezione',
                'Aggiungi una cover attraente',
                'Scrivi una descrizione per i collezionisti',
            ],
        ],
        'reservation' => [
            'explanation' => 'La Prenotazione ti permette di riservare un EGI prima che venga messo in vendita ufficialmente.',
            'tips' => [
                'Controlla il prezzo e le condizioni',
                'La prenotazione ha una scadenza',
                'Puoi annullare entro i termini previsti',
            ],
        ],
        'egili' => [
            'explanation' => 'Gli Egili sono i crediti della piattaforma. Li usi per accedere a funzionalità premium come Natan Tutor, generazione AI di tratti, e altro.',
            'tips' => [
                '1 Egili = €0.01',
                'Acquisti minimi di 5000 Egili',
                'I crediti regalo scadono dopo 90 giorni',
            ],
        ],
        'default_explanation' => 'Questa funzionalità ti aiuta a interagire con la piattaforma in modo più efficace.',
    ],

    // === MINT STEPS ===
    'mint' => [
        'step1_title' => 'Scegli il titolo',
        'step1_desc' => 'Dai un nome alla tua opera. Il titolo sarà visibile a tutti.',
        'step2_title' => 'Carica il media',
        'step2_desc' => 'Carica l\'immagine, video o audio che rappresenta la tua opera.',
        'step3_title' => 'Aggiungi descrizione',
        'step3_desc' => 'Racconta la storia della tua opera, cosa l\'ha ispirata.',
        'step4_title' => 'Conferma e Mint',
        'step4_desc' => 'Verifica i dati e conferma la creazione sulla blockchain.',
        'tip_title' => 'Usa un titolo che catturi l\'attenzione e sia memorabile.',
        'tip_media' => 'File supportati: JPG, PNG, GIF, MP4, MP3. Max 50MB.',
        'tip_description' => 'Una buona descrizione aumenta le possibilità di vendita.',
    ],

    // === RESERVATION STEPS ===
    'reserve' => [
        'step1_title' => 'Seleziona l\'EGI',
        'step1_desc' => 'Scegli l\'EGI che vuoi prenotare dalla collezione.',
        'step2_title' => 'Verifica disponibilità',
        'step2_desc' => 'Controlla che l\'EGI sia disponibile per la prenotazione.',
        'step3_title' => 'Conferma prenotazione',
        'step3_desc' => 'Conferma la prenotazione e ricevi la notifica.',
        'explanation' => 'Stai prenotando ":title" al prezzo di :price€.',
        'what_happens_next' => 'Dopo la prenotazione, il creatore riceverà una notifica. Avrai un tempo limitato per completare l\'acquisto.',
    ],

    // === COLLECTION STEPS ===
    'collection' => [
        'step1_title' => 'Nome della collezione',
        'step1_desc' => 'Scegli un nome che rappresenti il tema dei tuoi EGI.',
        'step2_title' => 'Descrizione',
        'step2_desc' => 'Spiega cosa conterrà questa collezione.',
        'step3_title' => 'Immagine di copertina',
        'step3_desc' => 'Carica un\'immagine che rappresenti la collezione.',
        'tip_name' => 'Un nome breve e memorabile funziona meglio.',
        'tip_description' => 'Racconta la visione dietro questa collezione.',
        'tip_cover' => 'Usa un\'immagine quadrata ad alta risoluzione.',
        'suggestion_personal' => 'Collezione Personale',
        'suggestion_art' => 'Le Mie Opere',
        'suggestion_memories' => 'Ricordi Digitali',
    ],

    // === PURCHASE SECTION ===
    'purchase' => [
        'egili_explanation' => 'Gli Egili sono la valuta della piattaforma. Con gli Egili puoi accedere a funzionalità premium come l\'assistenza di Natan Tutor.',
        'value_proposition' => 'Più Egili acquisti, più bonus ricevi! I pacchetti più grandi includono crediti extra gratuiti.',
    ],

    // === RECOMMENDATIONS ===
    'recommendations' => [
        'no_egis' => 'Non hai ancora creato nessun EGI. Vuoi che ti guidi nella creazione del tuo primo?',
        'explore_collections' => 'Hai esplorato poche collezioni. Scopri cosa hanno creato gli altri!',
        'low_balance' => 'Il tuo saldo Egili è basso. Considera un acquisto per continuare a usare Natan Tutor.',
    ],

    // === ERRORS ===
    'errors' => [
        'insufficient_egili' => 'Non hai abbastanza Egili per questa azione. Acquista altri crediti per continuare.',
        'egi_not_found' => 'L\'EGI richiesto non esiste o non è disponibile.',
        'action_not_available' => 'Questa azione non è disponibile al momento.',
        'user_not_authenticated' => 'Devi effettuare l\'accesso per usare Natan Tutor.',
    ],

    // === WELCOME GIFT ===
    'welcome_gift' => [
        'title' => 'Benvenuto su FlorenceEGI!',
        'message' => 'Hai ricevuto :amount Egili di benvenuto per esplorare la piattaforma con l\'aiuto di Natan Tutor!',
        'expires_notice' => 'I crediti regalo scadono tra :days giorni.',
        'start_exploring' => 'Inizia ad esplorare',
    ],

    // === UI ELEMENTS ===
    'ui' => [
        'cost_label' => 'Costo: :cost Egili',
        'balance_label' => 'Saldo: :balance Egili',
        'mode_switch' => 'Cambia modalità',
        'help_button' => 'Aiuto',
        'close_button' => 'Chiudi',
        'confirm_button' => 'Conferma',
        'cancel_button' => 'Annulla',
        'next_step' => 'Passo successivo',
        'previous_step' => 'Passo precedente',
        'complete' => 'Completa',
    ],

    // === TOOLTIPS ===
    'tooltips' => [
        'tutoring_mode' => 'In modalità Tutorial, Natan ti spiega ogni passaggio in dettaglio.',
        'expert_mode' => 'In modalità Esperto, Natan esegue le azioni rapidamente senza spiegazioni.',
        'cost_varies' => 'Il costo varia in base alla complessità dell\'azione e alla modalità scelta.',
    ],
];
