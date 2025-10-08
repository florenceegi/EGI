<?php

return [
    // Page Meta
    'page_title' => 'Mint :title - FlorenceEGI',
    'meta_description' => 'Acuña tu EGI :title en la blockchain de Algorand. Proceso seguro y transparente.',

    // Header
    'header_title' => 'Acuña tu EGI',
    'header_description' => 'Completa tu compra y acuña tu EGI en la blockchain de Algorand. Este proceso es irreversible.',

    // Buttons
    'mint_button' => 'Acuñar (:price)',
    'mint_button_processing' => 'Acuñando...',
    'cancel_button' => 'Cancelar',
    'back_button' => 'Volver',

    // EGI Preview Section
    'egi_preview' => [
        'title' => 'Vista previa EGI',
        'creator_by' => 'Creado por :name',
    ],

    // Blockchain Info Section
    'blockchain_info' => [
        'title' => 'Información Blockchain',
        'network' => 'Red',
        'network_value' => 'Algorand Mainnet',
        'token_type' => 'Tipo de Token',
        'token_type_value' => 'ASA (Algorand Standard Asset)',
        'supply' => 'Suministro',
        'supply_value' => '1 (Único)',
        'royalty' => 'Regalías',
        'royalty_value' => ':percentage% al creador',
    ],

    // Payment Section
    'payment' => [
        'title' => 'Detalles de Pago',
        'price_label' => 'Precio Final',
        'currency' => 'Moneda',
        'payment_method' => 'Método de Pago',
        'payment_method_card' => 'Tarjeta de Crédito/Débito',
        'total_label' => 'Total a Pagar',
    ],

    // Buyer Info Section
    'buyer_info' => [
        'title' => 'Información del Comprador',
        'wallet_label' => 'Wallet Algorand de destino',
        'wallet_placeholder' => 'Ingresa tu dirección de wallet Algorand',
        'wallet_help' => 'El EGI será transferido a esta dirección después de la acuñación.',
        'verify_wallet' => 'Asegúrate de que la dirección sea correcta - no se puede cambiar después de la acuñación.',
    ],

    // Confirmation
    'confirmation' => [
        'title' => 'Confirmar Acuñación',
        'description' => 'Estás a punto de acuñar este EGI. Esta operación es irreversible.',
        'agree_terms' => 'Acepto los términos y condiciones',
        'final_warning' => 'Advertencia: La acuñación no se puede cancelar después de la confirmación.',
    ],

    // Success Messages
    'success' => [
        'minted' => '¡EGI acuñado exitosamente!',
        'transaction_id' => 'ID de Transacción: :id',
        'view_on_explorer' => 'Ver en Algorand Explorer',
        'certificate_ready' => 'El certificado de autenticidad está listo para descargar.',
    ],

    // Error Messages
    'errors' => [
        'missing_params' => 'Faltan parámetros para la acuñación.',
        'invalid_reservation' => 'Reserva inválida o expirada.',
        'already_minted' => 'Este EGI ya ha sido acuñado.',
        'payment_failed' => 'Pago fallido. Inténtalo de nuevo.',
        'mint_failed' => 'Acuñación fallida. Contacta soporte.',
        'invalid_wallet' => 'Dirección de wallet inválida.',
        'blockchain_error' => 'Error de blockchain. Inténtalo más tarde.',
    ],

    // Validation
    'validation' => [
        'wallet_required' => 'La dirección de wallet es requerida.',
        'wallet_format' => 'La dirección de wallet debe ser una dirección Algorand válida.',
        'terms_required' => 'Debes aceptar los términos y condiciones.',
    ],

    // MiCA Compliance
    'compliance' => [
        'mica_title' => '⚖️ Cumplimiento MiCA',
        'mica_description' => 'Este proceso es completamente MiCA-SAFE. Pagamos en FIAT a través de PSPs autorizados, acuñamos el NFT por ti, y solo manejamos custodia temporal si es necesario.',
    ],
];
