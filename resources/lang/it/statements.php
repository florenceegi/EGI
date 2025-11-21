<?php

return [
    // Page titles
    'page_title' => 'Estratti Conto',
    'page_subtitle' => 'Consulta e scarica i tuoi estratti conto per Egili, fatture e altri documenti',

    // Tabs
    'tabs' => [
        'egili' => 'Estratto Conto Egili',
        'invoices' => 'Fatture', // Future
        'receipts' => 'Ricevute', // Future
    ],

    // Filters
    'filters' => [
        'title' => 'Filtri Periodo',
        'today' => 'Oggi',
        'week' => 'Settimana',
        'month' => 'Mese',
        'year' => 'Anno',
        'custom' => 'Personalizzato',
        'date_from' => 'Dal',
        'date_to' => 'Al',
        'apply' => 'Applica Filtri',
        'reset' => 'Resetta',
    ],

    // EGILI Statement
    'egili' => [
        'title' => 'Estratto Conto Egili',
        'subtitle' => 'Movimenti del tuo portafoglio Egili',
        'period' => 'Periodo: :from - :to',
        'download_pdf' => 'Scarica PDF',
        'no_transactions' => 'Nessun movimento nel periodo selezionato',
        
        // Summary
        'summary' => [
            'title' => 'Riepilogo Periodo',
            'starting_balance' => 'Saldo Iniziale',
            'total_income' => 'Totale Entrate',
            'total_expenses' => 'Totale Uscite',
            'ending_balance' => 'Saldo Finale',
            'transaction_count' => 'Numero Movimenti',
        ],
        
        // Table headers
        'table' => [
            'date' => 'Data',
            'time' => 'Ora',
            'description' => 'Descrizione',
            'type' => 'Tipo',
            'income' => 'Entrate',
            'expenses' => 'Uscite',
            'balance' => 'Saldo',
        ],
        
        // Transaction types
        'types' => [
            'earned' => 'Guadagnati',
            'spent' => 'Spesi',
            'purchase' => 'Acquisto Egili',
            'admin_grant' => 'Bonus Amministratore',
            'admin_deduct' => 'Deduzione Amministratore',
            'refund' => 'Rimborso',
            'expiration' => 'Scadenza',
            'initial_bonus' => 'Bonus Iniziale',
            'gift' => 'Regalo Egili',
            'ai_feature' => 'Feature AI',
            'collection_subscription' => 'Abbonamento Collection',
            'bonus' => 'Bonus',
            'other' => 'Altro',
        ],
    ],

    // PDF specific
    'pdf' => [
        'title' => 'ESTRATTO CONTO EGILI',
        'document_title' => 'Estratto Conto Portafoglio Egili',
        'generated_on' => 'Generato il',
        'account_holder' => 'Intestatario',
        'account_id' => 'ID Account',
        'wallet_id' => 'ID Wallet',
        'footer_disclaimer' => 'Questo documento è generato automaticamente dal sistema FlorenceEGI. Per assistenza: support@florenceegi.it',
        'page_number' => 'Pagina :current di :total',
    ],

    // Messages
    'messages' => [
        'filter_applied' => 'Filtro applicato correttamente',
        'pdf_generated' => 'PDF generato con successo',
        'error_generating_pdf' => 'Errore durante la generazione del PDF',
    ],
];

