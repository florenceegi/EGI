<?php

return [
    // Modal Header
    'title' => '¡Bienvenido a FlorenceEGI!',
    'subtitle' => 'Tu Cartera Digital está lista',
    
    // Intro
    'intro' => 'Durante el registro, generamos automáticamente una <strong>cartera digital Algorand</strong> asociada a tu cuenta. Esta cartera es necesaria para recibir tus <strong>Certificados Digitales de Autenticidad (EGI)</strong> cuando compres obras de arte en la plataforma.',
    
    // Section 1: Security
    'security_title' => '🔒 Seguridad y Privacidad GDPR',
    'security_items' => [
        'Tu cartera está protegida con <strong>cifrado XChaCha20-Poly1305</strong>',
        'Las claves privadas se cifran mediante <strong>AWS Key Management Service (KMS)</strong> con cifrado de sobre (DEK + KEK)',
        'Almacenamiento seguro en base de datos conforme con GDPR',
        'Puedes <strong>solicitar en cualquier momento</strong> las credenciales de tu cartera (frase secreta de 25 palabras)',
        'Puedes importar la cartera en <strong>Pera Wallet</strong> u otros clientes compatibles con Algorand',
        'Puedes <strong>solicitar la eliminación definitiva</strong> de la cartera de nuestros sistemas',
    ],
    'security_note' => '<strong>Nota:</strong> Una vez exportada la frase secreta y eliminada la cartera de nuestros servidores, la gestión se vuelve completamente <strong>no custodiada</strong> y será tu exclusiva responsabilidad.',
    
    // Section 2: Content
    'content_title' => '💎 Qué contiene tu cartera',
    'content_has_title' => '✅ Contiene:',
    'content_has' => [
        'Tus <strong>Certificados EGI</strong> (NFT únicos de obras)',
        'Metadatos de obras certificadas',
        'Historial de autenticidad on-chain',
    ],
    'content_not_has_title' => '❌ NO contiene:',
    'content_not_has' => [
        'ALGO (criptomoneda Algorand)',
        'Stablecoins u otros tokens fungibles',
        'Fondos o activos financieros',
    ],
    'content_note' => 'La cartera está dedicada <strong>exclusivamente</strong> a certificados digitales. No puede utilizarse para operaciones financieras.',
    
    // Section 3: Payments
    'payments_title' => '💶 Pagos y Recibos FIAT',
    'payments_how_title' => 'Cómo funcionan los pagos:',
    'payments_how' => [
        'Todas tus compras se realizan en <strong>euros (€)</strong> mediante tarjeta de crédito, transferencia bancaria u otros métodos tradicionales',
        'La cartera se usa <strong>solo</strong> para recibir el certificado digital de la obra, no para gestionar pagos',
        'Las transacciones de pago son gestionadas por nuestro PSP (Proveedor de Servicios de Pago) certificado',
    ],
    'payments_iban_title' => '💳 ¿Quieres recibir pagos en FIAT?',
    'payments_iban_intro' => 'Si eres un <strong>Creator</strong> y deseas recibir los ingresos de tus ventas directamente en tu cuenta bancaria, puedes agregar tu <strong>IBAN</strong> en la configuración del perfil.',
    'payments_iban_security_title' => 'Tu IBAN será:',
    'payments_iban_security' => [
        'Cifrado con estándares de seguridad bancaria (AES-256)',
        'Protegido con hash SHA-256 + pepper para unicidad',
        'Utilizado solo para pagos hacia ti',
        'Gestionado en total cumplimiento con GDPR',
        'Solo se almacenan los últimos 4 caracteres para UI',
    ],
    
    // Section 4: Compliance
    'compliance_title' => '🔐 Cumplimiento Normativo (MiCA-safe)',
    'compliance_intro' => 'Esta modalidad constituye <strong>"custodia técnica limitada de activos digitales no financieros"</strong> y:',
    'compliance_items' => [
        '<strong>No constituye actividad CASP</strong> (Proveedor de Servicios de Criptoactivos)',
        'Opera <strong>fuera del perímetro MiCA</strong> (Reglamento de Mercados de Criptoactivos)',
        'Está sujeta exclusivamente a obligaciones GDPR para protección de datos personales',
    ],
    'compliance_platform_title' => 'FlorenceEGI:',
    'compliance_platform' => [
        '✅ Emite certificados digitales (NFT únicos)',
        '✅ Proporciona custodia técnica temporal de claves',
        '❌ NO realiza operaciones de cambio',
        '❌ NO custodia fondos ni criptomonedas',
        '❌ NO intermedia transacciones financieras',
    ],
    
    // Section 5: Options
    'options_title' => '📱 Qué puedes hacer',
    'option1_title' => '✨ Opción 1 - Gestión Automática',
    'option1_subtitle' => '(Recomendada para principiantes)',
    'option1_items' => [
        'La cartera permanece "invisible" y gestionada automáticamente',
        'Recibes tus certificados sin preocuparte de blockchain',
        'Ideal si no estás familiarizado con criptomonedas',
        'Máxima simplicidad de uso',
    ],
    'option2_title' => '🔓 Opción 2 - Control Total',
    'option2_subtitle' => '(Para usuarios expertos)',
    'option2_items' => [
        'Descarga la frase secreta (25 palabras) desde <strong>Configuración → Seguridad</strong>',
        'Impórtala en Pera Wallet u otro cliente Algorand',
        'Gestiona tus certificados de forma independiente',
        'Solicita la eliminación de la cartera de nuestros servidores',
    ],
    
    // Section 6: Glossary
    'glossary_title' => '📖 Glosario de Términos Técnicos',
    'glossary' => [
        'wallet_algorand' => [
            'term' => 'Cartera Algorand:',
            'definition' => 'Cartera digital en blockchain Algorand. Contiene tus certificados EGI (NFT únicos).',
        ],
        'egi' => [
            'term' => 'EGI (Certificado Digital):',
            'definition' => 'NFT único que certifica autenticidad de obra de arte. Contiene metadatos inmutables y rastreables.',
        ],
        'envelope_encryption' => [
            'term' => 'Cifrado de Sobre (DEK+KEK):',
            'definition' => 'Sistema de cifrado de doble nivel. Una clave (DEK) cifra datos, una segunda clave (KEK) cifra la primera. AWS KMS gestiona la KEK.',
        ],
        'seed_phrase' => [
            'term' => 'Frase Secreta (Seed Phrase):',
            'definition' => 'Secuencia de 25 palabras que permite recuperar acceso a la cartera. <strong>¡Nunca compartir con nadie!</strong>',
        ],
        'non_custodial' => [
            'term' => 'Cartera No Custodiada:',
            'definition' => 'Cartera donde solo tú posees las claves privadas. La plataforma no puede acceder a tus activos.',
        ],
        'gdpr' => [
            'term' => 'GDPR:',
            'definition' => 'Reglamento General de Protección de Datos. Garantiza tus derechos de privacidad y seguridad de datos personales en UE.',
        ],
        'mica' => [
            'term' => 'MiCA (Mercados de Criptoactivos):',
            'definition' => 'Reglamento UE sobre mercados de criptoactivos. FlorenceEGI opera fuera del perímetro MiCA porque no gestiona activos financieros.',
        ],
        'casp' => [
            'term' => 'CASP:',
            'definition' => 'Proveedor de Servicios de Criptoactivos. Entidad que ofrece servicios de cambio, custodia o transferencia de criptomonedas. FlorenceEGI no es CASP.',
        ],
    ],
    
    // Section 7: Help
    'help_title' => '🆘 ¿Tienes preguntas?',
    'help_whitepaper' => 'White Paper',
    'help_whitepaper_desc' => 'Guía completa',
    'help_support' => 'Soporte',
    'help_support_desc' => 'Asistencia 24/7',
    'help_faq' => 'FAQ',
    'help_faq_desc' => 'Respuestas rápidas',
    
    // Footer
    'dont_show_again' => 'No mostrar este mensaje de nuevo',
    'btn_add_iban' => 'Agregar IBAN',
    'btn_continue' => 'Entiendo, continuar',
];

