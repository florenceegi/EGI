<?php

return [
    'coa' => [
        'inspector_countersign' => 'Co-assinatura Perito (QES)',
        'confirm_inspector_countersign' => 'Prosseguir com a co-assinatura do perito?',
        'inspector_countersign_applied' => 'Co-assinatura do perito aplicada',
        'operation_failed' => 'Operação falhou',
    ],

    /*
    |--------------------------------------------------------------------------
    | NFT Card System - Sistema de Cartões NFT
    |--------------------------------------------------------------------------
    */

    // Badges e Status
    'badge' => [
        'owned' => 'POSSUÍDO',
        'media_content' => 'Conteúdo Mídia',
    ],

    // Títulos
    'title' => [
        'untitled' => '✨ EGI Sem Título',
    ],

    // Plataforma
    'platform' => [
        'powered_by' => 'Powered by :platform',
    ],

    // Criador
    'creator' => [
        'created_by' => '👨‍🎨 Criado por:',
    ],

    // Preços
    'price' => [
        'purchased_for' => '💳 Comprado por',
        'price' => '💰 Preço',
        'floor' => '📊 Preço Mínimo',
    ],

    // Status
    'status' => [
        'not_for_sale' => '🚫 Não está à venda',
        'draft' => '⏳ Rascunho',
    ],

    // Ações
    'actions' => [
        'view' => 'Ver',
        'view_details' => 'Ver detalhes do EGI',
        'reserve' => 'Ativá-lo',
        'outbid' => 'Dar lance maior para ativar',
    ],

    // Detalhes da reserva
    'reservation' => [
        'highest_bid' => 'Lance Mais Alto',
        'fegi_reservation' => 'Reserva FEGI',
        'strong_bidder' => 'Melhor Licitante',
        'weak_bidder' => 'Código FEGI',
        'activator' => 'Co Criador',
        'activated_by' => 'Ativado por',
    ],

    'carousel' => [
        // Dynamic Headers
        'headers' => [
            'egi_list' => 'EGI',
            'egi_card' => 'EGI',
            'creators' => 'Artistas',
            'collections' => 'Coleções',
            'collectors' => 'Ativadores'
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Hero Coverflow - Seção Hero com Efeito Coverflow 3D
    |--------------------------------------------------------------------------
    */

    'hero_coverflow' => [
        'title' => 'Ativar um EGI é deixar sua marca.',
        'subtitle' => 'Seu nome permanece para sempre ao lado do Criador: sem você, a obra não existiria.',
        'carousel_mode' => 'Vista Carrossel',
        'list_mode' => 'Vista Grade',
        'carousel_label' => 'Carrossel de obras em destaque',
        'no_egis' => 'Nenhuma obra em destaque disponível no momento.',
        'navigation' => [
            'previous' => 'Obra anterior',
            'next' => 'Próxima obra',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Sistema Dossier - Dossier System
    |--------------------------------------------------------------------------
    */
    'dossier' => [
        'title' => 'Dossiê de Imagens',
        'loading' => 'Carregando dossiê...',
        'view_complete' => 'Ver dossiê completo de imagens',
        'close' => 'Fechar dossiê',

        // Artwork Info
        'artwork_info' => 'Informações da Obra',
        'author' => 'Autor',
        'year' => 'Ano',
        'internal_id' => 'ID Interno',

        // Dossier Info
        'dossier_info' => 'Informações do Dossiê',
        'images_count' => 'Imagens',
        'type' => 'Tipo',
        'utility_gallery' => 'Galeria Utilitária',

        // Gallery
        'gallery_title' => 'Galeria de Imagens',
        'image_number' => 'Imagem :number',
        'image_of_total' => 'Imagem :current de :total',

        // States
        'no_utility_title' => 'Dossiê não disponível',
        'no_utility_message' => 'Não há imagens adicionais disponíveis para esta obra.',
        'no_utility_description' => 'O dossiê de imagens adicionais ainda não foi configurado para esta obra.',

        'no_images_title' => 'Nenhuma imagem disponível',
        'no_images_message' => 'O dossiê existe mas ainda não contém imagens.',
        'no_images_description' => 'Imagens adicionais serão adicionadas no futuro pelo criador da obra.',

        'error_title' => 'Erro',
        'error_loading' => 'Erro ao carregar o dossiê',

        // Navigation
        'previous_image' => 'Imagem anterior',
        'next_image' => 'Próxima imagem',
        'close_viewer' => 'Fechar visualizador',
        'of' => 'de',

        // Zoom Controls
        'zoom_help' => 'Use a roda do mouse ou toque para zoom • Arraste para mover',
        'zoom_in' => 'Ampliar',
        'zoom_out' => 'Reduzir',
        'zoom_reset' => 'Redefinir zoom',
        'zoom_fit' => 'Ajustar à tela',
    ],

    /*
    |--------------------------------------------------------------------------
    | Certificado de Autenticidade (CoA)
    |--------------------------------------------------------------------------
    */

    'coa' => [
        // Badge Sistema QES
        'badge_author_signed' => 'Assinado Autor (QES)',
        'badge_inspector_signed' => 'Assinado Inspetor (QES)',
        'badge_integrity_ok' => 'Integridade Verificada',

        // Contrafirma Inspetor (QES)
        'inspector_countersign' => 'Contrafirma Inspetor (QES)',
        'confirm_inspector_countersign' => 'Prosseguir com a contrafirma do inspetor?',
        'inspector_countersign_applied' => 'Contrafirma do inspetor aplicada',
        'operation_failed' => 'Operação falhou',
        'author_countersign' => 'Assinatura Autor (QES)',
        'confirm_author_countersign' => 'Prosseguir com a assinatura do autor?',
        'author_countersign_applied' => 'Assinatura do autor aplicada',
        'regenerate_pdf' => 'Regenerar PDF',
        'pdf_regenerated' => 'PDF regenerado',
        'pdf_regenerate_failed' => 'Regeneração PDF falhou',

        // Página pública de verificação
        'public_verify' => [
            'signature' => 'Assinatura',
            'author_signed' => 'Autor assinado',
            'inspector_countersigned' => 'Contrafirma inspetor',
            'timestamp_tsa' => 'Timestamp TSA',
            'qes' => 'QES',
            'wallet_signature' => 'Assinatura wallet',
            'verify_signature' => 'verificar assinatura',
            'certificate_hash' => 'Hash do certificado (SHA-256)',
            'pdf_hash' => 'Hash do PDF (SHA-256)',
            'copy_hash' => 'Copiar hash',
            'copy_pdf_hash' => 'Copiar hash PDF',
            'hash_copied' => 'Hash copiado para a área de transferência!',
            'pdf_hash_copied' => 'Hash PDF copiado para a área de transferência!',
            'qr_code_verify' => 'Verificação Código QR',
            'qr_code' => 'Código QR',
            'scan_to_verify' => 'Escanear para verificar',
            'status' => 'Status',
            'valid' => 'Válido',
            'incomplete' => 'Incompleto',
            'revoked' => 'Revogado',

            // Cabeçalho e títulos
            'certificate_title' => 'Certificado de Autenticidade',
            'public_verification_display' => 'Exibição Pública de Verificação',
            'verified_authentic' => 'Certificado Verificado e Autêntico',
            'verified_at' => 'Verificado em',
            'serial_number' => 'Número de Série',
            'certificate_not_ready' => 'Certificado Não Pronto',
            'certificate_revoked' => 'Certificado Revogado',
            'certificate_not_valid' => 'Este certificado não é mais válido',
            'requires_coa_traits' => 'Requer Traits CoA',
            'certificate_not_ready_generic' => 'Certificado Não Pronto - Traits Genéricos',

            // Informações da obra
            'artwork_title' => 'Título',
            'year' => 'Ano',
            'dimensions' => 'Dimensões',
            'edition' => 'Edição',
            'author' => 'Autor',
            'technique' => 'Técnica',
            'material' => 'Material',
            'support' => 'Suporte',
            'platform' => 'Plataforma',
            'published_by' => 'Publicado por',
            'image' => 'Imagem',

            // Informações do certificado
            'issue_date' => 'Data de Emissão',
            'issued_by' => 'Emitido por',
            'issue_location' => 'Local de emissão',
            'notes' => 'Notas',

            // Anexos profissionais
            'professional_annexes' => 'Anexos Profissionais',
            'provenance' => 'Procedência',
            'condition_report' => 'Relatório de Condições',
            'exhibitions_publications' => 'Exposições/Publicações',
            'additional_photos' => 'Fotos Adicionais',

            // Informações on-chain
            'on_chain_info' => 'Informações On-chain',
        ],
    ],

];
