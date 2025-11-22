<?php

return [
    'title' => 'Fatture',
    'my_invoices' => 'Le Mie Fatture',
    'invoice' => 'Fattura',
    'invoices' => 'Fatture',
    'subtitle' => 'Gestisci le tue fatture, aggregazioni mensili e impostazioni di fatturazione',
    'items_title' => 'Dettaglio Righe Fattura',
    
    // Tabs
    'tabs' => [
        'sales' => 'Fatture Emesse',
        'purchases' => 'Fatture Ricevute',
        'aggregations' => 'Aggregazioni Mensili',
        'settings' => 'Impostazioni',
    ],
    
    // Status
    'status' => [
        'draft' => 'Bozza',
        'pending' => 'In Attesa',
        'sent' => 'Inviata',
        'delivered' => 'Consegnata',
        'paid' => 'Pagata',
        'cancelled' => 'Annullata',
        'rejected' => 'Rifiutata',
    ],
    
    // SDI Status
    'sdi_status' => [
        'not_sent' => 'Non Inviata a SDI',
        'pending' => 'In Attesa SDI',
        'sent' => 'Inviata a SDI',
        'delivered' => 'Consegnata via SDI',
        'rejected' => 'Rifiutata da SDI',
    ],
    
    // Types
    'types' => [
        'sales' => 'Vendita',
        'purchase' => 'Acquisto',
        'credit_note' => 'Nota di Credito',
    ],
    
    // Fields
    'fields' => [
        'invoice_number' => 'Numero Fattura',
        'invoice_code' => 'Codice Fattura',
        'invoice_type' => 'Tipo Fattura',
        'invoice_status' => 'Stato Fattura',
        'issue_date' => 'Data Emissione',
        'due_date' => 'Data Scadenza',
        'payment_date' => 'Data Pagamento',
        'seller' => 'Venditore',
        'buyer' => 'Acquirente',
        'subtotal' => 'Imponibile',
        'tax_amount' => 'IVA',
        'total' => 'Totale',
        'payment_method' => 'Metodo di Pagamento',
        'notes' => 'Note',
        'managed_by' => 'Gestita da',
        'item_description_default' => 'Vendita prodotto/servizio',
    ],
    
    // Management mode
    'managed_by' => [
        'platform' => 'Piattaforma',
        'user_external' => 'Sistema Esterno',
    ],
    
    // Aggregations
    'aggregations' => [
        'title' => 'Aggregazioni Mensili',
        'period' => 'Periodo',
        'total_sales' => 'Vendite Totali',
        'total_items' => 'Articoli Venduti',
        'total_buyers' => 'Acquirenti',
        'multiple_buyers' => 'Più Acquirenti',
        'no_buyers_data' => 'Nessun dato acquirente disponibile',
        'status' => [
            'pending' => 'In Attesa',
            'invoiced' => 'Fatturata',
            'exported' => 'Esportata',
            'cancelled' => 'Annullata',
        ],
    ],
    
    // Settings
    'settings' => [
        'title' => 'Impostazioni Fatturazione',
        'invoicing_mode' => 'Modalità Fatturazione',
        'platform_managed' => 'Gestita dalla Piattaforma',
        'user_managed' => 'Gestita da Sistema Esterno',
        'external_system_name' => 'Nome Sistema Esterno',
        'external_system_notes' => 'Note Sistema Esterno',
        'auto_generate_monthly' => 'Generazione Automatica Mensile',
        'invoice_frequency' => 'Frequenza Fatturazione',
        'frequency' => [
            'instant' => 'Istantanea (ad ogni vendita)',
            'monthly' => 'Mensile (aggregata)',
            'manual' => 'Manuale',
        ],
        'notify_on_invoice_generated' => 'Notifica Generazione Fattura',
        'notify_buyer_on_invoice' => 'Notifica Acquirente',
    ],
    
    // Actions
    'actions' => [
        'create' => 'Crea Fattura',
        'edit' => 'Modifica',
        'view' => 'Visualizza',
        'delete' => 'Elimina',
        'download_pdf' => 'Scarica PDF',
        'download_xml' => 'Scarica XML',
        'send_to_sdi' => 'Invia a SDI',
        'send_to_buyer' => 'Invia ad Acquirente',
        'mark_as_paid' => 'Segna come Pagata',
        'cancel' => 'Annulla',
        'generate_from_aggregation' => 'Genera Fattura',
        'export_aggregation' => 'Esporta Dati',
    ],
    
    // Messages
    'messages' => [
        'no_invoices' => 'Nessuna fattura trovata.',
        'no_aggregations' => 'Nessuna aggregazione trovata.',
        'invoice_created' => 'Fattura creata con successo.',
        'invoice_updated' => 'Fattura aggiornata con successo.',
        'invoice_deleted' => 'Fattura eliminata con successo.',
        'invoice_sent_to_sdi' => 'Fattura inviata a SDI con successo.',
        'invoice_sent_to_buyer' => 'Fattura inviata all\'acquirente con successo.',
        'aggregation_generated' => 'Fattura generata dall\'aggregazione con successo.',
        'aggregation_exported' => 'Aggregazione esportata con successo.',
        'settings_saved' => 'Impostazioni salvate con successo.',
    ],
    
    // Errors
    'errors' => [
        'invoice_not_found' => 'Fattura non trovata.',
        'aggregation_not_found' => 'Aggregazione non trovata.',
        'cannot_delete_paid_invoice' => 'Non è possibile eliminare una fattura pagata.',
        'cannot_edit_sent_invoice' => 'Non è possibile modificare una fattura già inviata.',
        'sdi_error' => 'Errore nell\'invio a SDI.',
        'export_error' => 'Errore nell\'esportazione dei dati.',
        'unauthorized' => 'Non autorizzato.',
        'already_invoiced' => 'Aggregazione già fatturata.',
        'pdf_not_found' => 'PDF della fattura non trovato.',
    ],
    
    // Info
    'info' => [
        'platform_managed_info' => 'La piattaforma genererà e gestirà automaticamente le fatture elettroniche.',
        'user_managed_info' => 'Gestirai le fatture tramite il tuo sistema esterno. La piattaforma ti fornirà i dati da importare.',
        'monthly_aggregation_info' => 'Le vendite del mese verranno aggregate e potrai generare una fattura unica o esportare i dati.',
        'instant_invoicing_info' => 'Verrà generata una fattura per ogni vendita.',
        'aggregation_buyers_info' => 'Questa fattura include vendite a più acquirenti nel periodo indicato.',
        'platform_managed_title' => 'Fattura Gestita dalla Piattaforma',
        'platform_managed_invoice_info' => 'Questa fattura è gestita automaticamente da FlorenceEGI in conformità alle normative fiscali vigenti.',
    ],
    
    // PDF Strings
    'pdf' => [
        'footer_line_1' => 'Documento generato elettronicamente - Validità ai sensi dell\'art. 21 D.P.R. 633/72',
        'footer_line_2' => 'FlorenceEGI S.r.l. - P.IVA IT12345678901 - info@florenceegi.com',
        'platform_description' => 'Piattaforma di Tokenizzazione Asset e Progetti Ambientali',
        'generated_at' => 'Documento generato il',
    ],
    
    // Misc
    'tagline' => 'Piattaforma di Tokenizzazione Asset',
    'invoice' => 'Fattura',
];

