<?php

return [
    // Page Meta
    'page_title' => 'Mint :title - FlorenceEGI',
    'meta_description' => 'Mint del tuo EGI :title sulla blockchain Algorand. Processo sicuro e trasparente.',

    // Header
    'header_title' => 'Mint del tuo EGI',
    'header_description' => 'Completa l\'acquisto e minta il tuo EGI sulla blockchain Algorand. Questo processo è irreversibile.',

    // Buttons
    'mint_button' => 'Mint (:price)',
    'mint_button_processing' => 'Minting in corso...',
    'cancel_button' => 'Annulla',
    'back_button' => 'Torna indietro',

    // EGI Preview Section
    'egi_preview' => [
        'title' => 'Anteprima EGI',
        'creator_by' => 'Creato da :name',
    ],

    // Blockchain Info Section
    'blockchain_info' => [
        'title' => 'Informazioni Blockchain',
        'network' => 'Rete',
        'network_value' => 'Algorand Mainnet',
        'token_type' => 'Tipo Token',
        'token_type_value' => 'ASA (Algorand Standard Asset)',
        'supply' => 'Supply',
        'supply_value' => '1 (Unico)',
        'royalty' => 'Royalty',
        'royalty_value' => ':percentage% al creatore',
    ],

    // Payment Section
    'payment' => [
        'title' => 'Dettagli Pagamento',
        'price_label' => 'Prezzo finale',
        'currency' => 'Valuta',
        'payment_method' => 'Metodo di pagamento',
        'payment_method_card' => 'Carta di Credito/Debito',
        'total_label' => 'Totale da pagare',
    ],

    // Buyer Info Section
    'buyer_info' => [
        'title' => 'Informazioni Acquirente',
        'wallet_label' => 'Wallet Algorand di destinazione',
        'wallet_placeholder' => 'Inserisci il tuo indirizzo wallet Algorand',
        'wallet_help' => 'L\'EGI verrà trasferito a questo indirizzo dopo il mint.',
        'verify_wallet' => 'Assicurati che l\'indirizzo sia corretto - non può essere modificato dopo il mint.',
    ],

    // Confirmation
    'confirmation' => [
        'title' => 'Conferma Mint',
        'description' => 'Stai per mintare questo EGI. Questa operazione è irreversibile.',
        'agree_terms' => 'Accetto i termini e condizioni',
        'final_warning' => 'Attenzione: Il mint non può essere annullato dopo la conferma.',
    ],

    // Success Messages
    'success' => [
        'minted' => 'EGI mintato con successo!',
        'transaction_id' => 'ID Transazione: :id',
        'view_on_explorer' => 'Visualizza su Algorand Explorer',
        'certificate_ready' => 'Il certificato di autenticità è pronto per il download.',
    ],

    // Error Messages
    'errors' => [
        'missing_params' => 'Parametri mancanti per il mint.',
        'invalid_reservation' => 'Prenotazione non valida o scaduta.',
        'already_minted' => 'Questo EGI è già stato mintato.',
        'payment_failed' => 'Pagamento fallito. Riprova.',
        'mint_failed' => 'Mint fallito. Contatta l\'assistenza.',
        'invalid_wallet' => 'Indirizzo wallet non valido.',
        'blockchain_error' => 'Errore della blockchain. Riprova più tardi.',
    ],

    // Validation
    'validation' => [
        'wallet_required' => 'L\'indirizzo wallet è obbligatorio.',
        'wallet_format' => 'L\'indirizzo wallet deve essere un indirizzo Algorand valido.',
        'terms_required' => 'Devi accettare i termini e condizioni.',
    ],
];
