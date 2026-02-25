<?php

/**
 * @package FlorenceEGI\Lang\es
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - ToS v3.1.0 — Sistema Egili Español)
 * @date 2026-02-25
 * @purpose Traducción española del sistema Egili y Paquetes de Servicios IA
 */

return [
    'buy_more' => 'Paquetes de Servicios IA',

    'purchase_modal' => [
        'title'              => 'Comprar Paquete de Servicios IA',
        'subtitle'           => 'Próximamente',
        'coming_soon_badge'  => 'Sistema en Desarrollo',
        'coming_soon_text'   => 'El sistema de compra estará disponible pronto. ¡Te notificaremos por email!',
        'features_title'     => 'Lo que podrás hacer:',
        'payment_fiat'       => 'Pago FIAT',
        'payment_fiat_desc'  => 'Tarjeta de crédito, PayPal',
        'bulk_discounts'     => 'Paquetes con Descuento',
        'bulk_discounts_desc' => 'Ahorra con paquetes más grandes',
        'history'            => 'Historial Completo',
        'history_desc'       => 'Transacciones siempre rastreables',
        'what_is_egili_title' => '¿Qué son los Egili?',
        'what_is_egili_text' => '<strong>Egili</strong> son el contador interno de consumo de servicios IA de FlorenceEGI.',
        'value'              => 'Valor',
        'footer_note'        => 'Sistema en desarrollo • Recibirás notificación por email al lanzamiento',
    ],

    'transaction_types' => [
        'earned'        => 'Ganado',
        'spent'         => 'Gastado',
        'admin_grant'   => 'Bonus Admin',
        'admin_deduct'  => 'Deducción Admin',
        'purchase'      => 'Acreditado (Paquete IA)',
        'refund'        => 'Reembolsado',
        'expiration'    => 'Caducado',
        'initial_bonus' => 'Bonus Inicial',
    ],

    'wallet' => [
        'title'               => 'Saldo Egili',
        'current_balance'     => 'Saldo Actual',
        'buy_more'            => 'Paquetes IA',
        'recent_transactions' => 'Últimas Transacciones',
        'view_all'            => 'Ver Todo',
        'no_transactions'     => 'Sin transacciones',
    ],

    // Purchase System (ToS v3.1.0 — producto = Paquetes de Servicios IA en FIAT)
    'purchase' => [
        'title'                 => 'Comprar Paquete de Servicios IA',
        'subtitle'              => 'Selecciona tu paquete — pago solo en EUR',
        'how_many_label'        => '¿Cuántos Egili quieres comprar?',
        'amount_placeholder'    => 'ej: 10000',
        'min_purchase'          => 'Mínimo: :min Egili (:eur)',
        'max_purchase'          => 'Máximo: :max Egili (:eur)',
        'unit_price'            => 'Precio unitario',
        'total_cost'            => 'Total a pagar',
        'select_payment_method' => 'Selecciona el método de pago',
        'payment_method_fiat'   => 'Tarjeta/PayPal (EUR)',
        'select_provider'       => 'Selecciona proveedor',
        'fiat_provider_stripe'  => 'Stripe (Tarjeta)',
        'fiat_provider_paypal'  => 'PayPal',
        'purchase_now'          => 'Confirmar Compra',
        'processing'            => 'Procesando...',
        'payment_success'       => '¡Pago completado con éxito!',
        'process_error'         => 'Se ha producido un error al procesar el pago.',
        'order_not_found'       => 'Pedido no encontrado.',
        'unauthorized'          => 'No tienes permiso para ver este pedido.',
        'invalid_amount'        => 'Importe no válido.',
        'pricing_error'         => 'Error al calcular el precio.',
        'amount_below_min'      => 'El importe debe ser al menos :min Egili.',
        'amount_above_max'      => 'El importe no puede superar :max Egili.',
        'calculating'           => 'Calculando...',
        // Nuevas claves ToS v3.1.0
        'legal_note'            => 'Los Egili se acreditan automáticamente al comprar un Paquete de Servicios IA en EUR.',
        'select_package'        => 'Selecciona tu paquete',
        'egili_credited'        => 'Egili acreditados',
        'you_get'               => 'Recibes',
        'egili_model_note'      => 'Pagas en EUR — los Egili se acreditan automáticamente',
    ],

    'email' => [
        'purchase_confirmation_subject' => 'Confirmación de Compra IA — Pedido :order_ref',
        'greeting'          => 'Hola :name,',
        'purchase_success'  => '¡Tu paquete de servicios IA ha sido completado con éxito! 🎉',
        'order_reference'   => '**Número de Pedido**: :reference',
        'purchase_details'  => '**Detalles de la Compra:**',
        'view_order'        => 'Ver Pedido',
        'invoice_info'      => 'Recibirás la factura agregada por email antes de fin de mes.',
        'thank_you'         => '¡Gracias por tu compra!',
        'signature'         => 'El Equipo de FlorenceEGI',
    ],

    'confirmation' => [
        'title'                => '¡Compra Completada!',
        'thank_you'            => 'Gracias por tu compra',
        'order_reference'      => 'Número de Pedido',
        'order_summary'        => 'Resumen del Pedido',
        'egili_purchased'      => 'Egili Acreditados',
        'unit_price'           => 'Precio Unitario',
        'total_paid'           => 'Total Pagado',
        'payment_method'       => 'Método de Pago',
        'payment_provider'     => 'Proveedor',
        'payment_id'           => 'ID de Transacción',
        'purchased_at'         => 'Fecha de Compra',
        'status'               => 'Estado',
        'status_completed'     => 'Completado',
        'status_pending'       => 'Pendiente',
        'status_failed'        => 'Fallido',
        'wallet_info'          => 'Saldo Egili',
        'new_balance'          => 'Nuevo Saldo',
        'invoice'              => 'Factura',
        'invoice_will_be_sent' => 'Recibirás la factura agregada por email antes de fin de mes.',
        'download_receipt'     => 'Descargar Recibo',
        'back_to_dashboard'    => 'Volver al Panel',
        'email_sent'           => 'Te hemos enviado un email de confirmación a :email',
    ],
];
