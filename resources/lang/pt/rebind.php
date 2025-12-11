<?php

/**
 * Rebind (Secondary Market) Translations - Portuguese
 */

return [
    'title' => 'Rebind - Mercado Secundário',
    'subtitle' => 'Compre este EGI do proprietário atual',

    'checkout' => [
        'title' => 'Checkout Rebind',
        'current_owner' => 'Vendedor Atual',
        'price_label' => 'Preço de Venda',
        'platform_fee' => 'Taxa da Plataforma',
        'total' => 'Total',
    ],

    'success' => [
        'purchase_initiated' => 'Compra iniciada com sucesso!',
        'purchase_completed' => 'Rebind concluído! Você agora é o novo proprietário.',
        'ownership_transferred' => 'Propriedade transferida com sucesso.',
    ],

    'errors' => [
        'not_available' => 'Este EGI não está disponível para Rebind.',
        'checkout_error' => 'Erro durante o checkout. Por favor, tente novamente.',
        'process_error' => 'Erro ao processar o Rebind.',
        'owner_cannot_buy' => 'Você não pode comprar um EGI que já possui.',
        'not_minted' => 'Este EGI ainda não foi mintado.',
        'not_for_sale' => 'Este EGI não está à venda.',
        'invalid_price' => 'Preço inválido para este EGI.',
        'payment_failed' => 'Pagamento falhou. Por favor, tente novamente.',
        'insufficient_egili' => 'Saldo EGILI insuficiente para esta compra.',
        'egili_disabled' => 'O pagamento em EGILI não está disponível para este EGI.',
        'unauthorized' => 'Você não está autorizado a completar esta compra.',
        'merchant_not_configured' => 'O método de pagamento selecionado não está disponível para este vendedor.',
        'validation_failed' => 'Dados de pagamento inválidos. Por favor, tente novamente.',
    ],

    'process' => [
        'initiated' => 'Processo de Rebind iniciado.',
        'processing' => 'Processando pagamento...',
        'transferring' => 'Transferindo propriedade...',
    ],

    'info' => [
        'secondary_market' => 'Mercado Secundário',
        'secondary_market_desc' => 'Você está comprando este EGI do seu proprietário atual, não do criador original.',
        'blockchain_transfer' => 'Transferência Blockchain',
        'blockchain_transfer_desc' => 'A propriedade será transferida na blockchain após o pagamento.',
    ],
];
