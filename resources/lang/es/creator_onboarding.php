<?php

return [
    'page' => [
        'title' => 'Resumen de onboarding para creadores',
        'description' => 'Revisa el estado de pagos, wallet de Algorand y cumplimiento FlorenceEGI.',
        'heading' => 'Bienvenido a tu centro de control de creador',
        'intro' => 'Gracias por completar los primeros pasos. Aquí tienes el resumen operativo de pagos, custodia Algorand y próximos pasos.',
    ],
    'profile' => [
        'title' => 'Perfil y wallet',
        'user_name' => 'Nombre completo',
        'user_email' => 'Correo electrónico',
        'user_type' => 'Rol de usuario',
        'wallet_address' => 'Wallet Algorand',
        'iban_masked' => 'IBAN registrado',
        'iban_missing' => 'IBAN no configurado. Añádelo para recibir abonos.',
    ],
    'stripe' => [
        'title' => 'Cuenta Stripe Connect',
        'account_id' => 'ID de cuenta',
        'status' => 'Estado general',
        'charges_enabled' => 'Pagos con tarjeta habilitados',
        'payouts_enabled' => 'Payouts habilitados',
        'details_submitted' => 'Verificación completada',
        'status_ready' => 'Listo para aceptar pagos y recibir payouts',
        'status_pending' => 'Se requieren acciones adicionales antes de activar los payouts',
        'cta_onboarding' => 'Completar onboarding de Stripe',
        'cta_dashboard' => 'Abrir panel Stripe Express',
        'onboarding_hint' => 'Stripe puede solicitar documentos fiscales o de identidad antes de liberar los payouts.',
    ],
    'actions' => [
        'title' => 'Próximos pasos',
        'checklist' => [
            'onboarding' => 'Completa el flujo de onboarding de Stripe si aún está pendiente.',
            'documents' => 'Prepara documentos fiscales y de identidad para la verificación KYC.',
            'pricing' => 'Configura precios y opciones Egili para tus EGI.',
            'compliance' => 'Revisa las directrices MiCA-safe y PA para tu ecosistema.',
        ],
    ],
    'pera' => [
        'title' => 'Wallet Pera Algorand y custodia',
        'intro' => 'FlorenceEGI custodia tu wallet Algorand hasta que solicites la entrega certificada.',
        'request' => 'Para recibir la frase secreta de Pera, abre un ticket en el Centro de Soporte y programa una sesión de verificación de identidad con nuestro equipo.',
        'note' => 'Hasta completar la entrega, FlorenceEGI firma las transacciones on-chain en tu nombre mientras los ingresos en fiat van directamente a tu cuenta Stripe.',
    ],
    'badges' => [
        'ready' => 'Listo',
        'pending' => 'Acción requerida',
        'missing' => 'Configuración necesaria',
    ],
    'buttons' => [
        'refresh' => 'Actualizar estado',
        'support' => 'Contactar con soporte FlorenceEGI',
    ],
];

