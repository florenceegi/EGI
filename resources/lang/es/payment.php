<?php

return [


    // Etiquetas UI genéricas
    'label_default'                   => 'Predeterminado',
    'label_toggle'                    => 'Activar/Desactivar',
    'label_make_default'              => 'Establecer como predeterminado',
    // Transferencia bancaria
    'bank_account_holder'             => 'Titular de la cuenta',
    'bank_holder_placeholder'         => 'Nombre completo tal como aparece en la cuenta bancaria',
    'bank_config_title'               => 'Configuración de Cuenta Bancaria',
    'bank_save_details'               => 'Guardar datos bancarios',
    // Stripe
    'stripe_connected'                => 'Cuenta Stripe conectada',
    'stripe_connected_creator'        => 'Cuenta Stripe conectada (nivel Creator)',
    'stripe_collection_inherits'      => 'La colección hereda tu cuenta principal de Stripe Connect.',
    'stripe_connect_first'            => 'Por favor, conecta tu cuenta Stripe en la configuración principal primero.',
    'stripe_connect_cta'             => 'Conectar cuenta Stripe',
    // Configuración de colección
    'collection_settings_title'       => 'Configuración de Pago de la Colección',
    'collection_settings_description' => 'Personaliza los métodos de pago para esta colección específica',


    // Stripe popup return page
    'popup_return_title'   => 'Verificación completada',
    'popup_return_heading' => 'Verificación completada',
    'popup_return_closing' => 'Esta ventana se cerrará automáticamente',

    'wizard' => [
        'chip_label'  => 'Activar pagos',
        'intro_title' => 'Activa tu sistema de pagos',
        'intro_text'  => 'Para empezar a vender tus obras necesitas activar :psp_name. Es un proceso guiado que solo tarda unos minutos.',
        'intro_note'  => 'Los pagos van directamente a tu cuenta. FlorenceEGI no retiene tu dinero.',
        'cta'         => 'Activar :psp_name',
        'processing'  => 'Iniciando…',
        'link_failed' => 'No se pudo generar el enlace. Por favor, inténtalo de nuevo.',
        'no_wallet'   => 'No hay wallet configurada. Contacta con soporte.',
        'success'     => '¡Pagos activados! Ya puedes vender tus obras.',
        'refresh'     => 'El enlace ha caducado. Haz clic en "Activar pagos" de nuevo.',
        // Wizard 4-step popup
        'back'                => 'Atrás',
        'step1_next'          => '¿Qué necesito?',
        'step2_title'         => 'Lo que necesitas para activar los pagos:',
        'step2_item1'         => 'Documento de identidad válido',
        'step2_item2'         => 'IBAN o tarjeta bancaria (para cobros)',
        'step2_item3'         => 'Unos 5 minutos de tu tiempo',
        'step2_next'          => 'Continuar',
        'step3_note'          => 'Se abrirá una pequeña ventana segura. Completa la verificación y vuelve aquí — esta página permanecerá abierta.',
        'step3_cta'           => 'Abrir verificación :psp_name',
        'popup_blocked'       => 'Tu navegador bloqueó la ventana emergente. Permite las ventanas emergentes de FlorenceEGI e inténtalo de nuevo.',
        'step4_checking'      => 'Verificando estado…',
        'step4_complete'      => '¡Pagos activados!',
        'step4_complete_hint' => 'Ya puedes vender tus obras. El modal se actualizará en breve.',
        'step4_pending'       => 'Verificación pendiente',
        'step4_pending_hint'  => 'Estamos procesando tus documentos. Recibirás una notificación cuando esté listo.',
        'step4_error'         => 'Algo salió mal',
        'step4_retry'         => 'Reintentar',
    ],

];
