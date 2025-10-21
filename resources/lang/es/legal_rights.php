<?php

/**
 * @package Resources\Lang\Es
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Legal Rights & Copyright)
 * @date 2025-10-21
 * @purpose Spanish translations for legal rights, copyright and resale rights
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Legal Rights - Traducciones Españolas
    |--------------------------------------------------------------------------
    |
    | Información legal sobre derechos de autor (Ley italiana 633/1941) y derecho de reventa
    | para documentos públicos y UI
    |
    */

    // Títulos de sección
    'section_title' => 'Derechos de Autor y Derecho de Reventa',
    'section_subtitle' => 'Legislación italiana y europea: qué pertenece al Creator, qué adquiere el Owner',

    // Aviso legal
    'disclaimer_title' => 'Aviso Importante',
    'disclaimer_text' => 'La siguiente información se proporciona solo con fines informativos y educativos. No constituye asesoramiento legal. Para asuntos específicos, consulte a un abogado especializado en derechos de autor.',
    'disclaimer_legal' => 'La información proporcionada es solo con fines informativos generales y no constituye asesoramiento legal profesional. La ley de derechos de autor es compleja y sujeta a interpretación. Para asuntos legales específicos, recomendamos consultar a un abogado especializado en propiedad intelectual y derecho del arte. FlorenceEGI no asume responsabilidad por decisiones tomadas en base a esta información.',

    // Derechos del Creator
    'creator_rights_title' => 'Derechos del Creator (Siempre)',
    'creator_rights_subtitle' => 'El Creator conserva estos derechos incluso después de vender la obra',

    'moral_rights_title' => 'Derechos Morales (Inalienables)',
    'moral_rights_subtitle' => 'Ley italiana 633/1941 Art. 20 - Nunca transferibles, incluso después de la venta',
    'moral_rights' => [
        'paternity' => 'Paternidad: Derecho a ser siempre reconocido como autor de la obra',
        'integrity' => 'Integridad: Derecho a oponerse a modificaciones, deformaciones o alteraciones que dañen la reputación',
        'attribution' => 'Atribución: El Owner debe siempre citar correctamente al artista',
        'owner_cannot' => 'El Owner NO PUEDE: eliminar firma, alterar la obra, atribuirla a otros',
    ],

    'economic_rights_title' => 'Derechos Económicos (Copyright)',
    'economic_rights_subtitle' => 'Ley italiana 633/1941 Art. 12-19 - Explotación económica',
    'economic_rights' => [
        'reproduction' => 'Reproducción: Solo el Creator puede hacer copias/impresiones de la obra',
        'public_communication' => 'Comunicación pública: Uso en publicidad/TV/online requiere licencia del Creator',
        'distribution' => 'Distribución: Vender copias/mercancía requiere autorización',
        'important_note' => 'IMPORTANTE: Comprar NFT ≠ Comprar copyright',
    ],

    // Derechos del Owner
    'owner_rights_title' => 'Derechos del Owner (Comprador)',
    'owner_can_title' => 'Qué PUEDE Hacer el Owner',
    'owner_can' => [
        'possess' => 'Poseer físicamente la obra',
        'display_private' => 'Exponer privadamente (casa/oficina)',
        'resell' => 'Revender la obra (con royalty del Creator)',
        'gift' => 'Donar o dejar en herencia',
        'photograph' => 'Fotografiar para documentación personal',
        'display_public' => 'Exponer públicamente sin fines comerciales (con atribución al Creator)',
        'restoration' => 'Restauración conservativa (sin alterar)',
    ],

    'owner_cannot_title' => 'Qué NO PUEDE Hacer (Sin Consentimiento del Creator)',
    'owner_cannot' => [
        'reproduce' => 'Reproducir comercialmente (impresiones, pósters, mercancía)',
        'modify' => 'Modificar/alterar la obra original',
        'advertise' => 'Usar en publicidad/marketing sin licencia',
        'publish' => 'Publicar online con fines comerciales',
        'derivative' => 'Crear obras derivadas (remixes, versiones)',
        'remove_credits' => 'Eliminar firma/créditos del artista',
        'mint_nft' => 'Emitir NFTs adicionales de la misma obra',
        'violation' => 'Violación = Art. 171 LDA: Multas hasta €15,493 + incautación + indemnización',
    ],

    // Comparación Royalty
    'comparison_title' => 'Derecho de Reventa vs Royalty Plataforma',
    'comparison_subtitle' => 'Dos mecanismos distintos y acumulables',

    'comparison_table' => [
        'aspect' => 'Aspecto',
        'platform_royalty' => 'Royalty Plataforma (FlorenceEGI)',
        'legal_droit' => 'Derecho de Reventa (Legal)',

        'legal_basis' => 'Base jurídica',
        'legal_basis_platform' => 'Contrato smart contract',
        'legal_basis_law' => 'Ley 633/1941 Art. 19bis',

        'min_threshold' => 'Umbral mínimo',
        'min_threshold_platform' => '€0 (todas las ventas)',
        'min_threshold_law' => '€3,000',

        'percentage' => 'Porcentaje',
        'percentage_platform' => '4.5% fijo',
        'percentage_law' => '4% → 0.25% (decreciente)',

        'sale_type' => 'Tipo de ventas',
        'sale_type_platform' => 'P2P directas (plataforma)',
        'sale_type_law' => 'A través de profesionales (galerías/subastas)',

        'management' => 'Gestión',
        'management_platform' => 'Smart contract automático',
        'management_law' => 'SIAE (manual)',

        'cumulative' => 'Acumulable',
        'cumulative_yes' => '¡SÍ! El Creator puede recibir AMBOS',
    ],

    // Escenarios de venta
    'scenarios_title' => 'Cómo Funciona en FlorenceEGI',

    'scenario_primary' => [
        'title' => 'Venta Primaria (Mint) - EGI €1,000',
        'distribution' => 'Distribución de ingresos:',
        'creator' => 'Creator: €650-680 (65-68%)',
        'epp' => 'EPP: €200 (20%)',
        'platform' => 'Plataforma: €100 (10%)',
        'association' => 'Asociación: €20 (2%)',
        'droit_not_applicable' => 'Derecho de reventa NO aplicable',
        'droit_reason' => 'Es la primera venta, no una reventa',
    ],

    'scenario_secondary_low' => [
        'title' => 'Reventa Secundaria - EGI €1,000 (P2P en FlorenceEGI)',
        'distribution' => 'Distribución:',
        'seller' => 'Seller recibe: €930 (93%)',
        'creator_royalty' => 'Creator royalty: €45 (4.5%)',
        'epp' => 'EPP: €10 (1%)',
        'platform' => 'Plataforma: €10 (1%)',
        'association' => 'Asociación: €5 (0.5%)',
        'droit_not_applicable' => 'Derecho de reventa legal NO aplicable',
        'droit_reason' => 'Por debajo del umbral de €3,000',
        'platform_royalty_note' => 'Pero Creator recibe igualmente 4.5% (nuestro contrato)',
    ],

    'scenario_secondary_high' => [
        'title' => 'Reventa Secundaria - EGI €50,000 (vía Galería/Subasta)',
        'fee_platform' => 'Fee FlorenceEGI:',
        'seller' => 'Seller: €46,500 (93%)',
        'creator' => 'Creator: €2,250 (4.5%)',
        'epp' => 'EPP: €500 (1%)',
        'platform' => 'Platform: €500 (1%)',
        'association' => 'Assoc: €250 (0.5%)',
        'droit_applicable' => 'Derecho de reventa legal APLICABLE',
        'droit_rate' => 'Tasa: 4% (tramo 0-€50k)',
        'droit_amount' => 'Importe: €2,000',
        'droit_recipient' => 'Recibido: Creator (vía SIAE)',
        'droit_separate' => 'Separado de las fees de plataforma',
        'total_creator' => 'TOTAL Creator: €4,250 (8.5%)',
        'example' => 'Ejemplo: Venta €50,000 vía galería → Creator recibe €2,250 (4.5% plataforma) + €2,000 (4% derecho reventa) = €4,250 total (8.5%)',
    ],

    // Normativa
    'legislation_title' => 'Marco Legislativo',

    'law_lda' => [
        'title' => 'Ley 633/1941 (Ley de Derechos de Autor - LDA)',
        'art_12_19' => 'Art. 12-19: Derechos patrimoniales (reproducción, comunicación, distribución)',
        'art_20' => 'Art. 20: Derechos morales (paternidad, integridad de la obra)',
        'art_19bis' => 'Art. 19bis: Derecho de reventa en reventas',
        'art_25' => 'Art. 25: Duración de protección (vida del autor + 70 años)',
        'art_171' => 'Art. 171: Sanciones por violaciones (multas €51-€15,493)',
    ],

    'law_dlgs' => [
        'title' => 'D.Lgs. 118/2006 (Implementación Directiva UE 2001/84/CE)',
        'art_3' => 'Art. 3: Tasas derecho de reventa (4% hasta €50k, luego decreciente)',
        'art_4' => 'Art. 4: Umbral mínimo €3,000 para aplicación',
        'art_5' => 'Art. 5: Máximo €12,500 por venta',
        'art_8' => 'Art. 8: Gestión vía SIAE (Sociedad Italiana Autores y Editores)',
    ],

    'law_cc' => [
        'title' => 'Código Civil - Art. 2575-2583',
        'description' => 'Distinción entre propiedad del objeto físico (Owner) y derechos sobre la obra intelectual (Creator). La compra de una obra de arte transfiere solo la posesión material, no el copyright.',
    ],

    // Contrato de venta
    'contract_title' => 'Qué Incluye el Contrato de Venta EGI',
    'owner_acquires' => 'El Owner ADQUIERE:',
    'owner_acquires_list' => [
        'physical' => 'Propiedad física de la obra (objeto material)',
        'nft' => 'NFT digital (certificado blockchain)',
        'enjoyment' => 'Derecho de disfrute privado',
        'resale' => 'Derecho de reventa (con royalty del Creator)',
        'possession' => 'Posesión exclusiva del original',
    ],

    'creator_retains' => 'El Creator CONSERVA:',
    'creator_retains_list' => [
        'moral_rights' => 'Todos los derechos morales (paternidad, integridad)',
        'droit_suite' => 'Derecho de reventa (4%-0.25% en reventas >€3k)',
        'platform_royalty' => 'Royalty plataforma (4.5% siempre)',
        'reproduction' => 'Derechos de reproducción (impresiones, copias)',
        'copyright' => 'Copyright sobre la imagen de la obra',
        'digital_rights' => 'Derechos digitales (uso online comercial)',
    ],

    // Compromiso FlorenceEGI
    'commitment_title' => 'Compromiso FlorenceEGI',
    'commitment_subtitle' => 'FlorenceEGI se compromete a respetar y proteger los derechos de los artistas previstos por la ley italiana y europea:',
    'commitment_list' => [
        'attribution' => 'Garantizamos atribución correcta en todos los EGIs (paternidad)',
        'immutability' => 'Bloqueamos modificaciones post-mint (integridad blockchain)',
        'royalties' => 'Royalties automáticas 4.5% en todas las reventas (incluso bajo €3k)',
        'siae' => 'Colaboramos con SIAE para gestión del derecho de reventa en ventas >€3k a través de profesionales',
        'enforcement' => 'Smart contract impide evasión de royalties (ejecución sin confianza)',
    ],
];
