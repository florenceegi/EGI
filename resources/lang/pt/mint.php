<?php

return [
    // Page Meta
    'page_title' => 'Mint :title - FlorenceEGI',
    // Validation
    'validation' => [
        'wallet_required' => 'Endereço da carteira é obrigatório.',
        'wallet_format' => 'Endereço da carteira deve ser um endereço Algorand válido.',
        'terms_required' => 'Deves aceitar os Termos e Condições.',
    ],

    // MiCA Compliance
    'compliance' => [
        'mica_title' => '⚖️ Conformidade MiCA',
        'mica_description' => 'Este processo é totalmente MiCA-SAFE. Pagamos em FIAT através de PSPs autorizados, criamos o NFT para ti, e gerimos apenas custódia temporária se necessário.',
    ],

    // Meta descriptions
    'meta_description' => 'Faça mint do seu EGI :title na blockchain Algorand. Processo seguro e transparente.',

    // Header
    'header_title' => 'Mint do seu EGI',
    'header_description' => 'Complete sua compra e faça mint do seu EGI na blockchain Algorand. Este processo é irreversível.',

    // Buttons
    'mint_button' => 'Mint (:price)',
    'mint_button_processing' => 'Mint em andamento...',
    'cancel_button' => 'Cancelar',
    'back_button' => 'Voltar',

    // EGI Preview Section
    'egi_preview' => [
        'title' => 'Visualização EGI',
        'creator_by' => 'Criado por :name',
    ],

    // Blockchain Info Section
    'blockchain_info' => [
        'title' => 'Informações Blockchain',
        'network' => 'Rede',
        'network_value' => 'Algorand Mainnet',
        'token_type' => 'Tipo de Token',
        'token_type_value' => 'ASA (Algorand Standard Asset)',
        'supply' => 'Suprimento',
        'supply_value' => '1 (Único)',
        'royalty' => 'Royalties',
        'royalty_value' => ':percentage% para o criador',
    ],

    // Payment Section
    'payment' => [
        'title' => 'Detalhes do Pagamento',
        'price_label' => 'Preço Final',
        'currency' => 'Moeda',
        'payment_method' => 'Método de Pagamento',
        'payment_method_label' => 'Método de Pagamento',
        'payment_method_card' => 'Cartão de Crédito/Débito',
        'payment_method_egili' => 'Pagar com Egili',
        'total_label' => 'Total a Pagar',
        'credit_card' => 'Cartão de Crédito/Débito',
        'paypal' => 'Pagar com PayPal',
        'winning_reservation' => 'Reserva vencedora',
        'egili_balance_label' => 'Saldo disponível: :balance EGL',
        'egili_required_label' => 'Necessários para este mint: :required EGL',
        'egili_summary_title' => 'Resumo Egili',
        'egili_summary' => 'Você precisa de :required EGL para finalizar o mint.',
        'egili_insufficient' => 'Saldo Egili insuficiente. Recarregue o saldo ou escolha outro método.',
        'submit_button' => 'Concluir pagamento',
    ],

    // Buyer Info Section
    'buyer_info' => [
        'title' => 'Informações do Comprador',
        'wallet_label' => 'Carteira Algorand de destino',
        'wallet_placeholder' => 'Digite seu endereço de carteira Algorand',
        'wallet_help' => 'O EGI será transferido para este endereço após o mint.',
        'verify_wallet' => 'Certifique-se de que o endereço esteja correto - não pode ser alterado após o mint.',
    ],

    // Confirmation
    'confirmation' => [
        'title' => 'Confirmar Mint',
        'description' => 'Você está prestes a fazer mint deste EGI. Esta operação é irreversível.',
        'agree_terms' => 'Concordo com os termos e condições',
        'final_warning' => 'Aviso: O mint não pode ser cancelado após a confirmação.',
    ],

    // Success Messages
    'success' => [
        'minted' => 'EGI mintado com sucesso!',
        'transaction_id' => 'ID da Transação: :id',
        'view_on_explorer' => 'Ver no Algorand Explorer',
        'certificate_ready' => 'O certificado de autenticidade está pronto para download.',
    ],

    // Error Messages
    'errors' => [
        'missing_params' => 'Parâmetros ausentes para o mint.',
        'invalid_reservation' => 'Reserva inválida ou expirada.',
        'already_minted' => 'Este EGI já foi mintado.',
        'payment_failed' => 'Pagamento falhou. Tente novamente.',
        'mint_failed' => 'Mint falhou. Entre em contato com o suporte.',
        'invalid_wallet' => 'Endereço de carteira inválido.',
        'blockchain_error' => 'Erro da blockchain. Tente novamente mais tarde.',
        'invalid_amount' => 'Não foi possível calcular o valor do mint. Entre em contato com o suporte.',
        'insufficient_egili' => 'Você não possui Egili suficientes para concluir este mint.',
        'egili_disabled' => 'O pagamento com Egili não está habilitado para este EGI.',
        'merchant_not_configured' => 'O creator não concluiu a configuração de pagamentos para este provedor. Entre em contato com o creator ou escolha outro método de pagamento.',
        'unauthorized' => 'Você não está autorizado a concluir este mint.',
    ],

    // Validation
    'validation' => [
        'wallet_required' => 'Endereço da carteira é obrigatório.',
        'wallet_format' => 'Endereço da carteira deve ser um endereço Algorand válido.',
        'terms_required' => 'Você deve aceitar os termos e condições.',
    ],
];
