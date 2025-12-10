<?php

return [
    // Nome categoria per badge
    'category_name' => 'Lingotto d\'Oro',

    // Etichette componente
    'title' => 'Informazioni Lingotto d\'Oro',
    'subtitle' => 'Valore Indicativo Basato sul Prezzo Corrente dell\'Oro',

    // Proprietà oro
    'weight' => 'Peso',
    'weight_unit' => 'Unità',
    'purity' => 'Purezza',
    'pure_gold' => 'Contenuto Oro Puro',

    // Etichette valore
    'gold_price' => 'Prezzo Spot Oro',
    'creator_margin' => 'Margine Creatore',
    'per_gram' => 'al grammo',
    'per_oz' => 'per oncia troy',
    'base_value' => 'Valore Base Oro',
    'margin' => 'Margine Creatore',
    'indicative_value' => 'Valore Indicativo',
    'final_value' => 'Valore Totale',

    // Disclaimer
    'disclaimer' => 'Questo è un valore indicativo basato sul prezzo spot corrente dell\'oro. Il prezzo di vendita effettivo è determinato dal Creatore.',
    'price_updated_at' => 'Prezzo aggiornato alle',
    'price_source' => 'Fonte',

    // Descrizioni purezza
    'purity_999' => '24k - 99.9% Puro',
    'purity_995' => '99.5% Puro',
    'purity_990' => '99.0% Puro',
    'purity_916' => '22k - 91.6% Puro',
    'purity_750' => '18k - 75.0% Puro',

    // Unità peso
    'unit_grams' => 'Grammi',
    'unit_ounces' => 'Once',
    'unit_troy_ounces' => 'Once Troy',

    // Messaggi stato
    'loading' => 'Caricamento prezzo oro...',
    'error' => 'Impossibile recuperare il prezzo dell\'oro. Riprova più tardi.',
    'not_gold_bar' => 'Questo EGI non è un Lingotto d\'Oro.',

    // Messaggi mint
    'mint_price_expired' => 'Il tempo per completare l\'operazione di Mint è scaduto (10 minuti). Il prezzo dell\'oro potrebbe essere cambiato. Riprova per ottenere un prezzo aggiornato.',
    'mint_price_warning' => 'Il prezzo del lingotto potrebbe essere aggiornato all\'ultima quotazione.',

    // Funzionalità aggiornamento
    'refresh_button' => 'Aggiorna Prezzo',
    'refresh_cost' => 'Costo: :cost Egili',
    'refresh_available_now' => 'Disponibile ora',
    'next_refresh' => 'Prossimo aggiornamento automatico tra :time',
    'refresh_success' => 'Prezzo dell\'oro aggiornato con successo!',
    'refresh_success_title' => 'Prezzo Aggiornato',
    'refresh_error' => 'Impossibile aggiornare il prezzo dell\'oro.',
    'refresh_error_title' => 'Errore',
    'refresh_network_error' => 'Errore di connessione. Riprova.',
    'insufficient_egili' => 'Egili insufficienti. Servono :required ma ne hai :current.',
    'insufficient_egili_title' => 'Crediti Insufficienti',
    'insufficient_egili_message' => 'Non hai abbastanza Egili per questa operazione.',
    'required' => 'Richiesti',
    'available' => 'Disponibili',
    'missing' => 'Mancanti',
    'buy_egili_hint' => 'Acquista Egili per continuare.',
    'buy_egili_button' => 'Acquista Egili',
    'refresh_confirm_title' => 'Aggiornare il prezzo dell\'oro?',
    'refresh_confirm_message' => 'Questo costerà :cost Egili dal tuo saldo. Il prezzo sarà aggiornato con dati in tempo reale.',
    'refresh_confirm_button' => 'Aggiorna per :cost Egili',
    'refresh_cancel' => 'Annulla',
    'operation_cost' => 'Costo Operazione',
    'your_balance' => 'Il tuo saldo',
    'after_operation' => 'Dopo',
    'egili_charged_on_success' => 'Gli Egili verranno scalati solo se l\'operazione ha successo.',
    'confirm_and_charge' => 'Conferma e Scala',
    'refreshing_title' => 'Aggiornamento in corso...',
    'refreshing_message' => 'Stiamo recuperando il prezzo aggiornato dell\'oro...',

    // Throttle
    'throttle_exceeded' => 'Hai raggiunto il limite di aggiornamenti manuali. Riprova tra qualche ora.',
    'throttle_exceeded_title' => 'Limite Raggiunto',
    'throttle_info' => 'Puoi effettuare max :max aggiornamenti ogni :hours ore.',
    'throttle_remaining' => ':remaining aggiornamenti rimasti',
    'throttle_reset_at' => 'Reset tra :time',

    // Sezione margine CRUD
    'margin' => [
        'title' => 'Margine Lingotto d\'Oro',
        'description' => 'Imposta il tuo margine sul valore del lingotto. Puoi usare una percentuale, un importo fisso o entrambi.',
        'percent_label' => 'Margine Percentuale',
        'percent_hint' => 'Percentuale da aggiungere al valore dell\'oro (es. 5%)',
        'fixed_label' => 'Margine Fisso',
        'fixed_hint' => 'Importo fisso in EUR da aggiungere al valore dell\'oro',
        'current_value' => 'Valore indicativo attuale',
        'value_note' => 'Calcolato dal prezzo spot dell\'oro più i margini',
    ],
];
