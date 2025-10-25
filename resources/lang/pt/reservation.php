<?php

/**
 * Mensagens de Reserva
 * @package FlorenceEGI
 * @subpackage Traduções
 * @language pt
 * @version 2.0.0
 */

return [
    // Mensagens de sucesso
    'success' => 'Sua reserva foi feita com sucesso! O certificado foi gerado.',
    'cancel_success' => 'Sua reserva foi cancelada com sucesso.',
    'success_title' => 'Reserva realizada!',
    'view_certificate' => 'Ver Certificado',
    'close' => 'Fechar',

    // Mensagens de erro
    'unauthorized' => 'Você deve conectar sua carteira ou fazer login para fazer uma reserva.',
    'validation_failed' => 'Verifique os dados inseridos e tente novamente.',
    'auth_required' => 'É necessária autenticação para visualizar suas reservas.',
    'list_failed' => 'Não foi possível recuperar suas reservas. Tente novamente mais tarde.',
    'status_failed' => 'Não foi possível recuperar o status da reserva. Tente novamente mais tarde.',
    'unauthorized_cancel' => 'Você não tem permissão para cancelar esta reserva.',
    'cancel_failed' => 'Não foi possível cancelar a reserva. Tente novamente mais tarde.',

    // Botões UI
    'button' => [
        'reserve' => 'Reservar',
        'reserved' => 'Reservado',
        'make_offer' => 'Fazer uma oferta'
    ],

    // Badges
    'badge' => [
        'highest' => 'Prioridade Máxima',
        'superseded' => 'Prioridade Inferior',
        'has_offers' => 'Reservado'
    ],

    // Detalhes da reserva
    'already_reserved' => [
        'title' => 'Já Reservado',
        'text' => 'Você já tem uma reserva para este EGI.',
        'details' => 'Detalhes da sua reserva:',
        'type' => 'Tipo',
        'amount' => 'Valor',
        'status' => 'Status',
        'view_certificate' => 'Ver Certificado',
        'ok' => 'OK',
        'new_reservation' => 'Nova Reserva',
        'confirm_new' => 'Você quer fazer uma nova reserva?'
    ],

    // Histórico de reservas
    'history' => [
        'title' => 'Histórico de Reservas',
        'entries' => 'Entradas de Reserva',
        'view_certificate' => 'Ver Certificado',
        'no_entries' => 'Nenhuma reserva encontrada.',
        'be_first' => 'Seja o primeiro a reservar este EGI!',
        'purchases_offers_title' => 'Histórico de Compras / Ofertas'
    ],

    // Mensagens de erro
    'errors' => [
        'button_click_error' => 'Ocorreu um erro ao processar sua solicitação.',
        'form_validation' => 'Verifique os dados inseridos e tente novamente.',
        'api_error' => 'Ocorreu um erro de comunicação com o servidor.',
        'unauthorized' => 'Você deve conectar sua carteira ou fazer login para fazer uma reserva.'
    ],

    // Formulário
    'form' => [
        'title' => 'Reservar este EGI',
        'offer_amount_label' => 'Sua Oferta (EUR)',
        'offer_amount_placeholder' => 'Digite o valor em EUR',
        'algo_equivalent' => 'Aproximadamente :amount ALGO',
        'terms_accepted' => 'Aceito os termos e condições para reservas de EGI',
        'contact_info' => 'Informações de Contato Adicionais (Opcional)',
        'submit_button' => 'Fazer Reserva',
        'cancel_button' => 'Cancelar'
    ],

    // Tipo de reserva
    'type' => [
        'strong' => 'Reserva Forte',
        'weak' => 'Reserva Fraca'
    ],

    // Níveis de prioridade
    'priority' => [
        'highest' => 'Reserva Ativa',
        'superseded' => 'Superada',
    ],

    // Status da reserva
    'status' => [
        'active' => 'Ativa',
        'pending' => 'Pendente',
        'cancelled' => 'Cancelada',
        'expired' => 'Expirada'
    ],

    // === NOVA SEÇÃO: NOTIFICAÇÕES ===
    'notifications' => [
        'reservation_expired' => 'Sua reserva de €:amount para :egi_title expirou.',
        'superseded' => 'Sua oferta para :egi_title foi superada. Nova oferta mais alta: €:new_highest_amount',
        'highest' => 'Parabéns! Sua oferta de €:amount para :egi_title é agora a mais alta!',
        'rank_changed' => 'Sua posição para :egi_title mudou: você está agora na posição #:new_rank',
        'competitor_withdrew' => 'Um competidor se retirou. Você subiu para a posição #:new_rank para :egi_title',
        'pre_launch_reminder' => 'O mint on-chain começará em breve! Confirme sua reserva para :egi_title.',
        'mint_window_open' => 'É a sua vez! Você tem 48 horas para completar o mint de :egi_title.',
        'mint_window_closing' => 'Atenção! Restam apenas :hours_remaining horas para completar o mint de :egi_title.',
        'default' => 'Atualização sobre sua reserva para :egi_title',
        'archived_success' => 'Notificação arquivada com sucesso.'
    ],
];
