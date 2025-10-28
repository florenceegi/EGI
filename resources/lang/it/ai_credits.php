<?php

/**
 * @package Resources\Lang\It
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - AI Credits Cost Tracking)
 * @date 2025-10-28
 * @purpose Traduzioni italiane sistema AI Credits per tracking costi, saldo e flussi pagamento
 */

return [
    // === BALANCE DISPLAY ===
    'balance' => [
        'title' => 'Saldo Crediti AI',
        'current' => 'Saldo Attuale',
        'credits' => 'crediti',
        'low_balance' => 'Saldo basso',
        'no_credits' => 'Nessun credito disponibile',
        'refresh' => 'Aggiorna saldo',
    ],

    // === COST PREVIEW MODAL ===
    'preview' => [
        'modal_title' => 'Anteprima Costo Analisi AI',
        'estimated_cost' => 'Costo Stimato',
        'your_balance' => 'Il Tuo Saldo',
        'balance_after' => 'Saldo Dopo Analisi',
        'acts_to_analyze' => 'Atti da Analizzare',
        'chunks_required' => 'Chunk di Elaborazione',
        'tokens_estimated' => 'Token Stimati',
        'cost_breakdown' => 'Dettaglio Costi',
        'input_tokens' => 'Token Input',
        'output_tokens' => 'Token Output (stimati)',
        'total_cost' => 'Costo Totale',
        'rate_info' => 'Tasso di Cambio',
        'rate_source' => 'Fonte: Banca Centrale Europea',
        'rate_updated' => 'Aggiornato',
        'insufficient_title' => 'Crediti Insufficienti',
        'insufficient_message' => 'Servono :required crediti ma ne hai solo :balance disponibili.',
        'purchase_credits' => 'Acquista Crediti',
        'cancel' => 'Annulla',
        'proceed' => 'Procedi con Analisi',
        'loading' => 'Calcolo costo in corso...',
    ],

    // === REAL-TIME COST DISPLAY ===
    'realtime' => [
        'panel_title' => 'Costo Elaborazione AI',
        'current_cost' => 'Costo Attuale',
        'estimated_final' => 'Stima Finale',
        'tokens_used' => 'Token Utilizzati',
        'input' => 'Input',
        'output' => 'Output',
        'chunk_cost' => 'Costo Chunk :number',
        'total_so_far' => 'Totale Parziale',
    ],

    // === FINAL COST SUMMARY ===
    'summary' => [
        'title' => 'Riepilogo Costo Analisi',
        'final_cost' => 'Costo Finale',
        'credits_deducted' => 'Crediti Scalati',
        'balance_before' => 'Saldo Precedente',
        'balance_after' => 'Saldo Attuale',
        'tokens_breakdown' => 'Dettaglio Token',
        'total_input_tokens' => 'Token Input Totali',
        'total_output_tokens' => 'Token Output Totali',
        'total_tokens' => 'Token Totali',
        'cost_per_chunk' => 'Costo per Chunk',
        'chunks_processed' => 'Chunk Elaborati',
        'transaction_id' => 'ID Transazione',
        'timestamp' => 'Completato Alle',
        'close' => 'Chiudi',
        'view_transaction' => 'Visualizza Dettagli Transazione',
    ],

    // === ERRORS ===
    'errors' => [
        'insufficient_credits' => 'Crediti AI insufficienti. Hai :balance crediti, ma ne servono :required.',
        'deduction_failed' => 'Impossibile scalare i crediti. Riprova o contatta il supporto.',
        'calculation_failed' => 'Calcolo costo fallito. Riprova.',
        'estimation_failed' => 'Impossibile stimare il costo. Procedere potrebbe consumare crediti.',
        'exchange_rate_unavailable' => 'Tasso di cambio non disponibile. Uso tasso di fallback.',
        'generic' => 'Errore durante elaborazione crediti.',
    ],

    // === SUCCESS MESSAGES ===
    'success' => [
        'credits_deducted' => 'Crediti scalati con successo.',
        'credits_refunded' => 'Crediti rimborsati: :amount crediti restituiti al tuo saldo.',
        'balance_updated' => 'Saldo aggiornato con successo.',
    ],

    // === TOOLTIPS ===
    'tooltips' => [
        'what_are_credits' => 'I Crediti AI alimentano le analisi intelligenti. Ogni query consuma crediti in base alla complessità dei documenti.',
        'how_calculated' => 'Il costo è calcolato dai token API: :input_price per 1M token input + :output_price per 1M token output.',
        'exchange_rate' => 'I prezzi sono in USD, convertiti in EUR usando il tasso ufficiale BCE aggiornato quotidianamente.',
        'chunks_explanation' => 'Set documentali ampi sono suddivisi in chunk per garantire analisi qualitative entro i limiti AI.',
        'refund_policy' => 'Se l\'analisi fallisce, i crediti sono automaticamente rimborsati al tuo saldo.',
    ],

    // === PURCHASE FLOW ===
    'purchase' => [
        'title' => 'Acquista Crediti AI',
        'select_package' => 'Seleziona Pacchetto',
        'package_basic' => 'Base',
        'package_standard' => 'Standard',
        'package_premium' => 'Premium',
        'package_enterprise' => 'Enterprise',
        'credits_amount' => ':amount crediti',
        'price' => ':price EUR',
        'best_value' => 'Miglior Valore',
        'most_popular' => 'Più Popolare',
        'proceed_to_payment' => 'Procedi al Pagamento',
        'payment_methods' => 'Accettati: Carta, PayPal, Bonifico',
    ],

    // === TRANSACTION HISTORY ===
    'transactions' => [
        'title' => 'Transazioni Crediti AI',
        'date' => 'Data',
        'type' => 'Tipo',
        'amount' => 'Importo',
        'balance' => 'Saldo',
        'description' => 'Descrizione',
        'status' => 'Stato',
        'type_deduction' => 'Analisi',
        'type_purchase' => 'Acquisto',
        'type_refund' => 'Rimborso',
        'type_bonus' => 'Bonus',
        'status_completed' => 'Completata',
        'status_pending' => 'In Attesa',
        'status_failed' => 'Fallita',
        'no_transactions' => 'Nessuna transazione ancora',
        'view_all' => 'Vedi Tutte le Transazioni',
    ],
];
