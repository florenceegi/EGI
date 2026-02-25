<?php

/**
 * @package FlorenceEGI\Lang\pt
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - ToS v3.1.0 — Sistema Egili Português)
 * @date 2026-02-25
 * @purpose Tradução portuguesa do sistema Egili e Pacotes de Serviços IA
 */

return [
    'buy_more' => 'Pacotes de Serviços IA',

    'purchase_modal' => [
        'title'              => 'Comprar Pacote de Serviços IA',
        'subtitle'           => 'Em breve',
        'coming_soon_badge'  => 'Sistema em Desenvolvimento',
        'coming_soon_text'   => 'O sistema de compra estará disponível em breve. Enviaremos uma notificação por email!',
        'features_title'     => 'O que poderá fazer:',
        'payment_fiat'       => 'Pagamento FIAT',
        'payment_fiat_desc'  => 'Cartão de crédito, PayPal',
        'bulk_discounts'     => 'Pacotes com Desconto',
        'bulk_discounts_desc' => 'Poupe com pacotes maiores',
        'history'            => 'Histórico Completo',
        'history_desc'       => 'Transações sempre rastreáveis',
        'what_is_egili_title' => 'O que são Egili?',
        'what_is_egili_text' => '<strong>Egili</strong> são o contador interno de consumo de serviços IA da FlorenceEGI.',
        'value'              => 'Valor',
        'footer_note'        => 'Sistema em desenvolvimento • Notificação por email no lançamento',
    ],

    'transaction_types' => [
        'earned'        => 'Ganho',
        'spent'         => 'Gasto',
        'admin_grant'   => 'Bónus Admin',
        'admin_deduct'  => 'Dedução Admin',
        'purchase'      => 'Creditado (Pacote IA)',
        'refund'        => 'Reembolsado',
        'expiration'    => 'Expirado',
        'initial_bonus' => 'Bónus Inicial',
    ],

    'wallet' => [
        'title'               => 'Saldo Egili',
        'current_balance'     => 'Saldo Atual',
        'buy_more'            => 'Pacotes IA',
        'recent_transactions' => 'Últimas Transações',
        'view_all'            => 'Ver Tudo',
        'no_transactions'     => 'Sem transações',
    ],

    // Purchase System (ToS v3.1.0 — produto = Pacotes de Serviços IA em FIAT)
    'purchase' => [
        'title'                 => 'Comprar Pacote de Serviços IA',
        'subtitle'              => 'Selecione o seu pacote — pagamento apenas em EUR',
        'how_many_label'        => 'Quantos Egili deseja comprar?',
        'amount_placeholder'    => 'ex: 10000',
        'min_purchase'          => 'Mínimo: :min Egili (:eur)',
        'max_purchase'          => 'Máximo: :max Egili (:eur)',
        'unit_price'            => 'Preço unitário',
        'total_cost'            => 'Total a pagar',
        'select_payment_method' => 'Selecione o método de pagamento',
        'payment_method_fiat'   => 'Cartão/PayPal (EUR)',
        'select_provider'       => 'Selecione o fornecedor',
        'fiat_provider_stripe'  => 'Stripe (Cartão)',
        'fiat_provider_paypal'  => 'PayPal',
        'purchase_now'          => 'Confirmar Compra',
        'processing'            => 'A processar...',
        'payment_success'       => 'Pagamento concluído com sucesso!',
        'process_error'         => 'Ocorreu um erro ao processar o pagamento.',
        'order_not_found'       => 'Encomenda não encontrada.',
        'unauthorized'          => 'Não está autorizado a visualizar esta encomenda.',
        'invalid_amount'        => 'Valor inválido.',
        'pricing_error'         => 'Erro ao calcular o preço.',
        'amount_below_min'      => 'O valor deve ser no mínimo :min Egili.',
        'amount_above_max'      => 'O valor não pode exceder :max Egili.',
        'calculating'           => 'A calcular...',
        // Novas chaves ToS v3.1.0
        'legal_note'            => 'Os Egili são creditados automaticamente na compra de um Pacote de Serviços IA em EUR.',
        'select_package'        => 'Selecione o seu pacote',
        'egili_credited'        => 'Egili creditados',
        'you_get'               => 'Recebe',
        'egili_model_note'      => 'Paga em EUR — os Egili são creditados automaticamente',
    ],

    'email' => [
        'purchase_confirmation_subject' => 'Confirmação de Compra IA — Encomenda :order_ref',
        'greeting'          => 'Caro :name,',
        'purchase_success'  => 'O seu pacote de serviços IA foi concluído com sucesso! 🎉',
        'order_reference'   => '**Número de Encomenda**: :reference',
        'purchase_details'  => '**Detalhes da Compra:**',
        'view_order'        => 'Ver Encomenda',
        'invoice_info'      => 'Receberá a fatura agregada por email até ao final do mês.',
        'thank_you'         => 'Obrigado pela sua compra!',
        'signature'         => 'A Equipa FlorenceEGI',
    ],

    'confirmation' => [
        'title'                => 'Compra Concluída!',
        'thank_you'            => 'Obrigado pela sua compra',
        'order_reference'      => 'Número de Encomenda',
        'order_summary'        => 'Resumo da Encomenda',
        'egili_purchased'      => 'Egili Creditados',
        'unit_price'           => 'Preço Unitário',
        'total_paid'           => 'Total Pago',
        'payment_method'       => 'Método de Pagamento',
        'payment_provider'     => 'Fornecedor',
        'payment_id'           => 'ID da Transação',
        'purchased_at'         => 'Data de Compra',
        'status'               => 'Estado',
        'status_completed'     => 'Concluído',
        'status_pending'       => 'Pendente',
        'status_failed'        => 'Falhado',
        'wallet_info'          => 'Saldo Egili',
        'new_balance'          => 'Novo Saldo',
        'invoice'              => 'Fatura',
        'invoice_will_be_sent' => 'Receberá a fatura agregada por email até ao final do mês.',
        'download_receipt'     => 'Descarregar Recibo',
        'back_to_dashboard'    => 'Voltar ao Painel',
        'email_sent'           => 'Enviámos um email de confirmação para :email',
    ],
];
