<?php

return [
    'section_title' => 'Precios de Funciones',
    'section_description' => 'Paquetes de acceso a servicios de IA para la plataforma FlorenceEGI',

    'packages' => [
        'starter' => [
            'name' => 'Paquete Iniciante',
            'description' => 'Paquete de acceso a servicios de IA — nivel básico. Ideal para usuarios individuales o primer contacto.',
            'description_short' => 'Nivel básico para inicio',
            'benefits' => [
                'Egili acreditados instantáneamente',
                'Pago seguro Stripe/PayPal',
                'Factura mensual agregada',
            ],
        ],
        'professional' => [
            'name' => 'Paquete Profesional',
            'description' => 'Paquete profesional para uso intensivo de servicios de IA.',
            'description_short' => 'Para uso regular y profesional',
            'benefits' => [
                'Egili acreditados instantáneamente',
                'Pago seguro Stripe/PayPal',
                'Factura mensual agregada',
                'Soporte técnico prioritario',
            ],
        ],
        'business' => [
            'name' => 'Paquete Empresarial',
            'description' => 'Paquete empresarial para equipos y agencias con uso continuo.',
            'description_short' => 'Para equipos y organizaciones',
            'benefits' => [
                'Egili acreditados instantáneamente',
                'Pago seguro Stripe/PayPal',
                'Factura mensual agregada',
                'Gerente de cuenta dedicado',
                'Acceso prioritario a API',
            ],
        ],
        'enterprise' => [
            'name' => 'Paquete Empresarial',
            'description' => 'Paquete enterprise para grandes organizaciones y Administraciones Públicas.',
            'description_short' => 'Para grandes organizaciones y AP',
            'benefits' => [
                'Egili acreditados instantáneamente',
                'Pago seguro Stripe/PayPal',
                'Factura mensual agregada',
                'Gerente de cuenta dedicado',
                'Soporte prioritario 24/7',
                'SLA garantizado',
                'API ilimitado',
            ],
        ],
    ],

    'columns' => [
        'feature' => 'Función',
        'category' => 'Categoría',
        'egili' => 'Egili',
        'eur' => 'EUR',
        'accessi' => 'Accesos',
        'acquisiti' => 'Adquiridos',
        'azioni' => 'Acciones',
    ],

    'categories' => [
        'ai_services' => 'Servicios de IA',
        'platform_services' => 'Servicios de Plataforma',
        'premium_visibility' => 'Visibilidad Premium',
        'governance' => 'Gobernanza',
    ],
];
