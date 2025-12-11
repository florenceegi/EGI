<?php

/**
 * Rebind (Secondary Market) Translations - Spanish
 */

return [
    'title' => 'Rebind - Mercado Secundario',
    'subtitle' => 'Compra este EGI del propietario actual',

    'checkout' => [
        'title' => 'Checkout Rebind',
        'current_owner' => 'Vendedor Actual',
        'price_label' => 'Precio de Venta',
        'platform_fee' => 'Comisión de la Plataforma',
        'total' => 'Total',
    ],

    'success' => [
        'purchase_initiated' => '¡Compra iniciada con éxito!',
        'purchase_completed' => '¡Rebind completado! Ahora eres el nuevo propietario.',
        'ownership_transferred' => 'Propiedad transferida con éxito.',
    ],

    'errors' => [
        'not_available' => 'Este EGI no está disponible para Rebind.',
        'checkout_error' => 'Error durante el checkout. Por favor, inténtalo de nuevo.',
        'process_error' => 'Error al procesar el Rebind.',
        'owner_cannot_buy' => 'No puedes comprar un EGI del que ya eres propietario.',
        'not_minted' => 'Este EGI aún no ha sido minteado.',
        'not_for_sale' => 'Este EGI no está a la venta.',
        'invalid_price' => 'Precio inválido para este EGI.',
        'payment_failed' => 'Pago fallido. Por favor, inténtalo de nuevo.',
        'insufficient_egili' => 'Saldo EGILI insuficiente para esta compra.',
        'egili_disabled' => 'El pago en EGILI no está disponible para este EGI.',
        'unauthorized' => 'No estás autorizado para completar esta compra.',
        'merchant_not_configured' => 'El método de pago seleccionado no está disponible para este vendedor.',
        'validation_failed' => 'Datos de pago inválidos. Por favor, inténtalo de nuevo.',
    ],

    'process' => [
        'initiated' => 'Proceso de Rebind iniciado.',
        'processing' => 'Procesando pago...',
        'transferring' => 'Transfiriendo propiedad...',
    ],

    'info' => [
        'secondary_market' => 'Mercado Secundario',
        'secondary_market_desc' => 'Estás comprando este EGI de su propietario actual, no del creador original.',
        'blockchain_transfer' => 'Transferencia Blockchain',
        'blockchain_transfer_desc' => 'La propiedad se transferirá en la blockchain después del pago.',
    ],
];
