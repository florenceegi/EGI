<?php

/**
 * Mensajes de Reserva
 * @package FlorenceEGI
 * @subpackage Traducciones
 * @language es
 * @version 2.0.0
 */

return [
    // Mensajes de éxito
    'success' => 'Tu reserva se ha realizado con éxito. El certificado ha sido generado.',
    'cancel_success' => 'Tu reserva se ha cancelado con éxito.',
    'success_title' => '¡Reserva realizada!',
    'view_certificate' => 'Ver Certificado',
    'close' => 'Cerrar',

    // Mensajes de error
    'unauthorized' => 'Debes conectar tu wallet o iniciar sesión para hacer una reserva.',
    'validation_failed' => 'Verifica los datos ingresados e inténtalo de nuevo.',
    'auth_required' => 'Se requiere autenticación para ver tus reservas.',
    'list_failed' => 'No se pudieron recuperar tus reservas. Inténtalo más tarde.',
    'status_failed' => 'No se pudo recuperar el estado de la reserva. Inténtalo más tarde.',
    'unauthorized_cancel' => 'No tienes permiso para cancelar esta reserva.',
    'cancel_failed' => 'No se pudo cancelar la reserva. Inténtalo más tarde.',

    // Botones UI
    'button' => [
        'reserve' => 'Reservar',
        'reserved' => 'Reservado',
        'make_offer' => 'Hacer una oferta'
    ],

    // Insignias
    'badge' => [
        'highest' => 'Máxima Prioridad',
        'superseded' => 'Prioridad Inferior',
        'has_offers' => 'Reservado'
    ],

    // Detalles de reserva
    'already_reserved' => [
        'title' => 'Ya Reservado',
        'text' => 'Ya tienes una reserva para este EGI.',
        'details' => 'Detalles de tu reserva:',
        'type' => 'Tipo',
        'amount' => 'Cantidad',
        'status' => 'Estado',
        'view_certificate' => 'Ver Certificado',
        'ok' => 'OK',
        'new_reservation' => 'Nueva Reserva',
        'confirm_new' => '¿Quieres hacer una nueva reserva?'
    ],

    // Historial de reservas
    'history' => [
        'title' => 'Historial de Reservas',
        'entries' => 'Entradas de Reserva',
        'view_certificate' => 'Ver Certificado',
        'no_entries' => 'No se encontraron reservas.',
        'be_first' => '¡Sé el primero en reservar este EGI!',
        'purchases_offers_title' => 'Historial de Compras / Ofertas'
    ],

    // Mensajes de error
    'errors' => [
        'button_click_error' => 'Ocurrió un error al procesar tu solicitud.',
        'form_validation' => 'Verifica los datos ingresados e inténtalo de nuevo.',
        'api_error' => 'Ocurrió un error de comunicación con el servidor.',
        'unauthorized' => 'Debes conectar tu wallet o iniciar sesión para hacer una reserva.'
    ],

    // Formulario
    'form' => [
        'title' => 'Reservar este EGI',
        'offer_amount_label' => 'Tu Oferta (EUR)',
        'offer_amount_placeholder' => 'Ingresa la cantidad en EUR',
        'algo_equivalent' => 'Aproximadamente :amount ALGO',
        'terms_accepted' => 'Acepto los términos y condiciones para las reservas de EGI',
        'contact_info' => 'Información de Contacto Adicional (Opcional)',
        'submit_button' => 'Realizar Reserva',
        'cancel_button' => 'Cancelar'
    ],

    // Tipo de reserva
    'type' => [
        'strong' => 'Reserva Fuerte',
        'weak' => 'Reserva Débil'
    ],

    // Niveles de prioridad
    'priority' => [
        'highest' => 'Reserva Activa',
        'superseded' => 'Superada',
    ],

    // Estado de la reserva
    'status' => [
        'active' => 'Activa',
        'pending' => 'Pendiente',
        'cancelled' => 'Cancelada',
        'expired' => 'Expirada'
    ],

    // === NUEVA SECCIÓN: NOTIFICACIONES ===
    'notifications' => [
        'reservation_expired' => 'Tu reserva de €:amount para :egi_title ha expirado.',
        'superseded' => 'Tu oferta para :egi_title ha sido superada. Nueva oferta más alta: €:new_highest_amount',
        'highest' => '¡Felicidades! Tu oferta de €:amount para :egi_title es ahora la más alta!',
        'rank_changed' => 'Tu posición para :egi_title ha cambiado: ahora estás en la posición #:new_rank',
        'competitor_withdrew' => 'Un competidor se ha retirado. Has subido a la posición #:new_rank para :egi_title',
        'pre_launch_reminder' => '¡El mint on-chain comenzará pronto! Confirma tu reserva para :egi_title.',
        'mint_window_open' => '¡Es tu turno! Tienes 48 horas para completar el mint de :egi_title.',
        'mint_window_closing' => '¡Atención! Solo quedan :hours_remaining horas para completar el mint de :egi_title.',
        'default' => 'Actualización sobre tu reserva para :egi_title',
        'archived_success' => 'Notificación archivada con éxito.'
    ],
];