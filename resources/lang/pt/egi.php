<?php

return [

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

];
