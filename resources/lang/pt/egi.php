<?php

return [

    /*
    |--------------------------------------------------------------------------
    | EGI (Ecological Goods Invent) - Traduções para Português
    |--------------------------------------------------------------------------
    |
    | Traduções para o sistema CRUD de EGI no FlorenceEGI
    | Versão: 1.0.0 - Compatível com o Oracode System 2.0
    |
    */

    // Meta e SEO
    'meta_description_default' => 'Detalhes para EGI: :title',
    'image_alt_default' => 'Imagem EGI',
    'view_full' => 'Ver Completo',
    'artwork_loading' => 'Obra em Carregamento...',

    // Informações Básicas
    'by_author' => 'por :name',
    'unknown_creator' => 'Artista Desconhecido',

    // Ações Principais
    'like_button_title' => 'Adicionar aos Favoritos',
    'unlike_button_title' => 'Remover dos Favoritos',
    'like_button_aria' => 'Adicionar este EGI aos seus favoritos',
    'unlike_button_aria' => 'Remover este EGI dos seus favoritos',
    'share_button_title' => 'Compartilhar este EGI',

    'current_price' => 'Preço Atual',
    'not_currently_listed' => 'A Ativar',
    'contact_owner_availability' => 'Contacte o proprietário para disponibilidade',
    'not_for_sale' => 'Não à Venda',
    'not_for_sale_description' => 'Este EGI não está atualmente disponível para compra',
    'liked' => 'Curtido',
    'add_to_favorites' => 'Adicionar aos Favoritos',
    'reserve_this_piece' => 'Ative-o',

    /*
    |--------------------------------------------------------------------------
    | Sistema de Cartões NFT - Sistema de Cartões NFT
    |--------------------------------------------------------------------------
    */

    // Emblemas e Estados
    'badge' => [
        'owned' => 'POSUÍDO',
        'media_content' => 'Conteúdo Multimídia',
        'winning_bid' => 'LANCE VENCEDOR',
        'outbid' => 'SUPERADO',
        'not_owned' => 'NÃO POSUÍDO',
        'to_activate' => 'A ATIVAR',
        'activated' => 'ATIVADO',
        'reserved' => 'RESERVADO',
        'minted' => 'MINTADO',
        'auction_active' => 'A MINTAR',  // Badge para EGI em leilão
    ],

    'ownership' => [
        'badge_title' => 'Propriedade',
        'current_owner' => 'Proprietário atual',
        'creator_owner' => 'Criado e ainda pertencente ao creator',
        'roles' => [
            'creator' => 'Creator',
            'collector' => 'Colecionador',
        ],
        'creator_default' => 'O creator mantém a propriedade até que o mint na blockchain seja concluído.',
        'collector_since' => 'Em posse desde :date',
        'collector_default' => 'A propriedade está registrada on-chain.',
        'minted_on' => 'Mint realizado em :date',
        'minted_unknown' => 'Mint registrado na blockchain',
        'unminted_hint' => 'Mint pendente: a propriedade permanece com o creator.',
        'owner_avatar_alt' => 'Retrato de :name',
    ],

    // Títulos
    'title' => [
        'untitled' => '✨ EGI Sem Título',
    ],

    // Plataforma
    'platform' => [
        'powered_by' => 'Desenvolvido por :platform',
    ],

    // Artista
    'creator' => [
        'created_by' => '👨‍🎨 Criado por:',
    ],

    // Preços
    'price' => [
        'purchased_for' => '💳 Comprado por',
        'price' => '💰 Preço',
        'floor' => '📊 Preço Base',
        'highest_bid' => '🏆 Lance Mais Alto',
    ],

    // Reservas
    'reservation' => [
        'count' => 'Reservas',
        'highest_bidder' => 'Maior Licitante',
        'by' => 'por',
        'highest_bid' => 'Lance Mais Alto',
        'fegi_reservation' => 'Reserva FEGI',
        'strong_bidder' => 'Maior Licitante',
        'weak_bidder' => 'Código FEGI',
        'activator' => 'Co-Criador',
        'activated_by' => 'Ativado por',
    ],

    // Nota de Moeda Original
    'originally_reserved_in' => 'Reservado originalmente em :currency por :amount',
    'originally_reserved_in_short' => 'Res. :currency :amount',

    // Sistema de Leilão
    'auction' => [
        'auction_details' => 'Detalhes do Leilão',
        'minimum_price' => 'Lance Inicial',
        'starting_price' => 'Preço Inicial',
        'current_bid' => 'Lance Atual',
        'highest_bid' => 'Lance Mais Alto',
        'no_bids' => 'Sem Lances',
        'starts_at' => 'Início',
        'ends_at' => 'Término',
        'ended' => 'Leilão Encerrado',
        'not_started' => 'Leilão Não Iniciado',
        'time_remaining' => 'Tempo Restante',
        'days' => 'dias',
        'hours' => 'horas',
        'minutes' => 'minutos',
    ],

    // Estados
    'status' => [
        'not_for_sale' => '🚫 Não à Venda',
        'draft' => '⏳ Rascunho',
        // Phase 2: Availability status
        'login_required' => '🔐 Login Necessário',
        'already_minted' => '✅ Já Mintado',
        'not_available' => '⚠️ Não Disponível',
    ],

    // Ações
    'actions' => [
        'view' => 'Ver',
        'view_details' => 'Ver Detalhes do EGI',
        'reserve' => 'Ative-o',
        'reserved' => 'Reservado',
        'outbid' => 'Superar para Ativar',
        'view_history' => 'Histórico',
        'reserve_egi' => 'Reservar :title',
        'complete_purchase' => 'Concluir Compra',
        // Phase 2: Dual path actions
        'mint_now' => 'Mintar Agora',
        'mint_direct' => 'Mintar Instantaneamente',
        // Ações de leilão
        'make_offer' => 'Fazer uma Oferta',
        // Phase 3: Secondary market (Rebind)
        'rebind' => 'Rebind',
    ],

    // Sistema de Histórico de Reservas
    'history' => [
        'title' => 'Histórico de Reservas',
        'no_reservations' => 'Nenhuma reserva encontrada',
        'total_reservations' => '{1} :count reserva|[2,*] :count reservas',
        'current_highest' => 'Prioridade Máxima Atual',
        'superseded' => 'Prioridade Inferior',
        'created_at' => 'Criado em',
        'amount' => 'Montante',
        'type_strong' => 'Reserva Forte',
        'type_weak' => 'Reserva Fraca',
        'loading' => 'Carregando histórico...',
        'error' => 'Erro ao carregar o histórico',
    ],

    // Seções Informativas
    'properties' => 'Propriedades',
    'supports_epp' => 'Suporta EPP',
    'asset_type' => 'Tipo de Ativo',
    'format' => 'Formato',
    'about_this_piece' => 'Sobre Esta Obra',
    'default_description' => 'Esta obra digital única representa um momento de expressão criativa, capturando a essência da arte digital na era do blockchain.',
    'provenance' => 'Proveniência',
    'view_full_collection' => 'Ver Coleção Completa',

    /*
    |--------------------------------------------------------------------------
    | Sistema CRUD - Sistema de Edição
    |--------------------------------------------------------------------------
    */

    'crud' => [

        // Cabeçalho e Navegação
        'edit_egi' => 'Editar EGI',
        'toggle_edit_mode' => 'Ativar/Desativar Modo de Edição',
        'start_editing' => 'Iniciar Edição',
        'save_changes' => 'Salvar Alterações',
        'cancel' => 'Cancelar',

        // Campo Título
        'title' => 'Título',
        'title_placeholder' => 'Insira o título da obra...',
        'title_hint' => 'Máximo de 60 caracteres',
        'characters_remaining' => 'caracteres restantes',

        // Campo Descrição
        'description' => 'Descrição',
        'description_placeholder' => 'Descreva sua obra, sua história e seu significado...',
        'description_hint' => 'Conte a história por trás da sua criação',

        // Campo Preço
        'price' => 'Preço',
        'price_placeholder' => '0.00',
        'price_hint' => 'Preço em ALGO (deixe em branco se não estiver à venda)',
        'price_locked_message' => 'Preço bloqueado - EGI já reservado',

        // Campo Data de Criação
        'creation_date' => 'Data de Criação',
        'creation_date_hint' => 'Quando você criou esta obra?',

        // Campo Publicado
        'is_published' => 'Publicado',
        'is_published_hint' => 'Tornar a obra visível publicamente',
        'payment_by_egili' => 'Ativar pagamento com Egili',
        'payment_by_egili_hint' => 'Permita que os colecionadores concluam o mint usando o saldo de Egili (token utilitário da plataforma). Permanece desativado até que você ative manualmente.',
        'payment_by_egili_status' => 'Pagamento com Egili',
        'payment_by_egili_enabled' => 'Os colecionadores podem concluir o mint utilizando Egili.',
        'payment_by_egili_disabled' => 'O pagamento com Egili está desativado para este EGI.',
        'psp_required_title' => 'Configure uma conta PSP para vender',
        'psp_required_description' => 'Para colocar um EGI à venda você precisa conectar uma conta Stripe ou PayPal e receber os pagamentos diretamente. Conclua o onboarding PSP para receber em EUR.',
        'psp_open_modal' => 'Abrir processo IBAN + PSP',
        'psp_onboarding_link' => 'Ver resumo do onboarding',
        'psp_only_egili_hint' => 'Enquanto isso só é possível vender habilitando o mint em Egili.',

        // Modo de Visualização - Estado Atual
        'current_title' => 'Título Atual',
        'no_title' => 'Nenhum título definido',
        'current_price' => 'Preço Atual',
        'price_not_set' => 'Preço não definido',
        'current_status' => 'Estado de Publicação',
        'status_published' => 'Publicado',
        'status_draft' => 'Rascunho',

        // Sistema de Exclusão
        'delete_egi' => 'Excluir EGI',
        'delete_confirmation_title' => 'Confirmar Exclusão',
        'delete_confirmation_message' => 'Tem certeza de que deseja excluir este EGI? Esta ação não pode ser desfeita.',
        'delete_confirm' => 'Excluir Permanentemente',

        // Mensagens de Validação
        'title_required' => 'O título é obrigatório',
        'title_max_length' => 'O título não pode exceder 60 caracteres',
        'price_numeric' => 'O preço deve ser um número válido',
        'price_min' => 'O preço não pode ser negativo',
        'price_required_for_fixed_price' => 'O modo Preço Fixo requer um preço superior a zero',
        'creation_date_format' => 'Formato de data inválido',

        // Mensagens de Sucesso
        'update_success' => 'EGI atualizado com sucesso!',
        'delete_success' => 'EGI excluído com sucesso.',

        // Mensagens de Erro
        'update_error' => 'Erro ao atualizar o EGI.',
        'delete_error' => 'Erro ao excluir o EGI.',
        'permission_denied' => 'Você não tem as permissões necessárias para esta ação.',
        'not_found' => 'EGI não encontrado.',

        // Mensagens Gerais
        'no_changes_detected' => 'Nenhuma alteração detectada.',
        'unsaved_changes_warning' => 'Você tem alterações não salvas. Tem certeza de que deseja sair?',
    ],

    'validation' => [
        'psp_required_for_sale' => 'Conecte primeiro uma conta PSP (Stripe ou PayPal) para ativar o modo de venda ou leilão. Você pode reabrir o onboarding em “Gerenciar conta → Conta PSP”.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Etiquetas Responsivas - Mobile/Tablet
    |--------------------------------------------------------------------------
    */

    'mobile' => [
        'edit_egi_short' => 'Editar',
        'save_short' => 'Salvar',
        'delete_short' => 'Excluir',
        'cancel_short' => 'Cancelar',
        'published_short' => 'Pub.',
        'draft_short' => 'Rascunho',
    ],

    /*
    |--------------------------------------------------------------------------
    | Carrossel EGI - EGIs em Destaque na Página Inicial
    |--------------------------------------------------------------------------
    */

    'carousel' => [
        'two_columns' => 'Vista de Lista',
        'three_columns' => 'Vista de Cartão',
        'navigation' => [
            'previous' => 'Anterior',
            'next' => 'Próximo',
            'slide' => 'Ir para o slide :number',
        ],
        'empty_state' => [
            'title' => 'Nenhum Conteúdo Disponível',
            'subtitle' => 'Volte em breve para novos conteúdos!',
            'no_egis' => 'Nenhuma obra EGI disponível no momento.',
            'no_creators' => 'Nenhum artista disponível no momento.',
            'no_collections' => 'Nenhuma coleção disponível no momento.',
            'no_collectors' => 'Nenhum colecionador disponível no momento.'
        ],

        // Botões de Tipo de Conteúdo
        'content_types' => [
            'egi_list' => 'Vista de Lista EGI',
            'egi_card' => 'Vista de Cartão EGI',
            'creators' => 'Artistas em Destaque',
            'collections' => 'Coleções de Arte',
            'collectors' => 'Melhores Colecionadores'
        ],

        // Botões de Modo de Visualização
        'view_modes' => [
            'carousel' => 'Vista de Carrossel',
            'list' => 'Vista de Lista'
        ],

        // Etiquetas de Modo
        'carousel_mode' => 'Carrossel',
        'list_mode' => 'Lista',

        // Etiquetas de Conteúdo
        'creators' => 'Artistas',
        'collections' => 'Coleções',
        'collectors' => 'Colecionadores',

        // Cabeçalhos Dinâmicos
        'headers' => [
            'egi_list' => 'EGI',
            'egi_card' => 'EGI',
            'creators' => 'Artistas',
            'collections' => 'Coleções',
            'collectors' => 'Ativadores'
        ],

        // Seções do Carrossel
        'sections' => [
            'egis' => 'EGIs em Destaque',
            'creators' => 'Artistas Emergentes',
            'collections' => 'Coleções Exclusivas',
            'collectors' => 'Melhores Colecionadores'
        ],
        'view_all' => 'Ver Todos',
        'items' => 'itens',

        // Título e Subtítulo para o Carrossel Multi-conteúdo
        'title' => 'Ative um EGI!',
        'subtitle' => 'Ativar uma obra significa juntar-se a ela e ser reconhecido para sempre como parte de sua história.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Vista de Lista - Modo Lista na Página Inicial
    |--------------------------------------------------------------------------
    */

    'list' => [
        'title' => 'Explorar por Categoria',
        'subtitle' => 'Navegue pelas diferentes categorias para encontrar o que procura',

        'content_types' => [
            'egi_list' => 'Lista EGI',
            'creators' => 'Lista de Artistas',
            'collections' => 'Lista de Coleções',
            'collectors' => 'Lista de Colecionadores'
        ],

        'headers' => [
            'egi_list' => 'Obras EGI',
            'creators' => 'Artistas',
            'collections' => 'Coleções',
            'collectors' => 'Colecionadores'
        ],

        'empty_state' => [
            'title' => 'Nenhum Item Encontrado',
            'subtitle' => 'Tente selecionar uma categoria diferente',
            'no_egis' => 'Nenhuma obra EGI encontrada.',
            'no_creators' => 'Nenhum artista encontrado.',
            'no_collections' => 'Nenhuma coleção encontrada.',
            'no_collectors' => 'Nenhum colecionador encontrado.'
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Carrossel de Desktop - Carrossel EGI Apenas para Desktop
    |--------------------------------------------------------------------------
    */

    'desktop_carousel' => [
        'title' => 'Obras Digitais em Destaque',
        'subtitle' => 'As melhores criações EGI da nossa comunidade',
        'navigation' => [
            'previous' => 'Anterior',
            'next' => 'Próximo',
            'slide' => 'Ir para o slide :number',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Alternância Mobile - Alternância de Vista Mobile
    |--------------------------------------------------------------------------
    */

    'mobile_toggle' => [
        'title' => 'Explorar FlorenceEGI',
        'subtitle' => 'Escolha como deseja navegar pelo conteúdo',
        'carousel_mode' => 'Vista de Carrossel',
        'list_mode' => 'Vista de Lista',
    ],

    /*
    |--------------------------------------------------------------------------
    | Hero Coverflow - Seção Hero com Efeito Coverflow 3D
    |--------------------------------------------------------------------------
    */

    'hero_coverflow' => [
        'title' => 'Ativar um EGI é deixar uma marca.',
        'subtitle' => 'Seu nome permanece para sempre ao lado do Criador: sem você, a obra não existiria.',
        'carousel_mode' => 'Vista de Carrossel',
        'list_mode' => 'Vista de Grade',
        'carousel_label' => 'Carrossel de obras em destaque',
        'no_egis' => 'Nenhuma obra em destaque disponível no momento.',
        'navigation' => [
            'previous' => 'Obra Anterior',
            'next' => 'Obra Seguinte',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Etiquetas de Acessibilidade - Leitores de Tela
    |--------------------------------------------------------------------------
    */

    'a11y' => [
        'edit_form' => 'Formulário de Edição de EGI',
        'delete_button' => 'Botão Excluir EGI',
        'toggle_edit' => 'Ativar Modo de Edição',
        'save_form' => 'Salvar Alterações do EGI',
        'close_modal' => 'Fechar Janela de Confirmação',
        'required_field' => 'Campo Obrigatório',
        'optional_field' => 'Campo Opcional',
    ],

    'collection' => [
        'part_of' => 'Parte de',
    ],

    // Colaboradores da Coleção
    'collection_collaborators' => 'Colaboradores',
    'owner' => 'Proprietário',
    // 'creator' => 'Criador',
    'no_other_collaborators' => 'Nenhum outro colaborador',

    /*
    |--------------------------------------------------------------------------
    | Certificado de Autenticidade (CoA)
    |--------------------------------------------------------------------------
    */

    'coa' => [
        'none' => 'Nenhum Certificado de Autenticidade',
        'title' => 'Certificado de Autenticidade',
        'status' => 'Estado',
        'issued' => 'Emitido em',
        'verification' => 'ID de Verificação',
        'copy' => 'Copiar',
        'copied' => 'Copiado!',
        'view' => 'Ver',
        'pdf' => 'PDF',
        'reissue' => 'Reemitir',
        'issue' => 'Emitir Certificado',
        'annexes' => 'Anexos',
        'add_annex' => 'Adicionar Anexo',
        'annex_coming_soon' => 'Gerenciamento de anexos disponível em breve!',
        'pro' => 'Pro',
        'unlock_pro' => 'Desbloquear com CoA Pro',
        'provenance' => 'Documentação de Proveniência',
        'pdf_bundle' => 'Pacote PDF Profissional',
        'issue_description' => 'Emita um certificado para fornecer prova de autenticidade e desbloquear recursos Pro',
        'creator_only' => 'Apenas o criador pode emitir certificados',
        'active' => 'Ativo',
        'revoked' => 'Revogado',
        'expired' => 'Expirado',
        'manage_coa' => 'Gerenciar CoA',
        'no_certificate' => 'Nenhum certificado emitido ainda',

        // Mensagens JavaScript
        'confirm_issue' => 'Emitir um Certificado de Autenticidade para este EGI?',
        'issued_success' => 'Certificado emitido com sucesso!',
        'confirm_reissue' => 'Reemitir este certificado? Isso criará uma nova versão.',
        'reissued_success' => 'Certificado reemitido com sucesso!',
        'reissue_certificate_confirm' => 'Tem certeza de que deseja reemitir este certificado?',
        'certificate_reissued_successfully' => 'Certificado reemitido com sucesso!',
        'error_reissuing_certificate' => 'Erro ao reemitir o certificado',
        'revocation_reason' => 'Motivo da Revogação:',
        'confirm_revoke' => 'Revogar este certificado? Esta ação não pode ser desfeita.',
        'revoked_success' => 'Certificado revogado com sucesso!',
        'error_issuing' => 'Erro ao emitir o certificado',
        'error_reissuing' => 'Erro ao reemitir o certificado',
        'error_revoking' => 'Erro ao revogar o certificado',
        'unknown_error' => 'Erro Desconhecido',
        'verify_any_certificate' => 'Verificar Qualquer Certificado',

        // Modal de Anexos
        'manage_annexes_title' => 'Gerenciar Anexos CoA Pro',
        'annexes_description' => 'Adicione documentação profissional para aprimorar seu certificado',
        'provenance_tab' => 'Proveniência',
        'condition_tab' => 'Condição',
        'exhibitions_tab' => 'Exposições',
        'photos_tab' => 'Fotos',
        'provenance_title' => 'Documentação de Proveniência',
        'provenance_description' => 'Documente o histórico de propriedade e a cadeia de autenticidade',
        'condition_title' => 'Relatório de Condição',
        'condition_description' => 'Avaliação profissional da condição física da obra',
        'exhibitions_title' => 'Histórico de Exposições',
        'exhibitions_description' => 'Registro de exposições públicas e histórico de exibição',
        'photos_title' => 'Fotografia Profissional',
        'photos_description' => 'Documentação de alta resolução e fotografia detalhada',
        'save_annex' => 'Salvar Anexo',
        'cancel' => 'Cancelar',
        'upload_files' => 'Carregar Arquivos',
        'drag_drop_files' => 'Arraste e solte arquivos aqui, ou clique para selecionar',
        'max_file_size' => 'Tamanho máximo do arquivo: 10MB por arquivo',
        'supported_formats' => 'Formatos suportados: PDF, JPG, PNG, DOCX',

        // Formulário de Proveniência
        'ownership_history_description' => 'Documente o histórico de propriedade e a cadeia de autenticidade desta obra',
        'previous_owners' => 'Proprietários Anteriores',
        'previous_owners_placeholder' => 'Liste os proprietários anteriores e as datas de posse...',
        'acquisition_details' => 'Detalhes de Aquisição',
        'acquisition_details_placeholder' => 'Como esta obra foi adquirida? Inclua datas, preços, casas de leilão...',
        'authenticity_sources' => 'Fontes de Autenticidade',
        'authenticity_sources_placeholder' => 'Opiniões de especialistas, catálogos razonados, arquivos institucionais...',
        'save_provenance_data' => 'Salvar Dados de Proveniência',

        // Formulário de Condição
        'condition_assessment_description' => 'Avaliação profissional do estado físico da obra e necessidades de conservação',
        'overall_condition' => 'Condição Geral',
        'condition_excellent' => 'Excelente',
        'condition_very_good' => 'Muito Bom',
        'condition_good' => 'Bom',
        'condition_fair' => 'Razoável',
        'condition_poor' => 'Ruim',
        'condition_notes' => 'Notas de Condição',
        'condition_notes_placeholder' => 'Descrição detalhada de quaisquer danos, restaurações ou problemas de conservação...',
        'conservation_history' => 'Histórico de Conservação',
        'conservation_history_placeholder' => 'Restaurações anteriores, tratamentos ou intervenções de conservação...',
        'save_condition_data' => 'Salvar Dados de Condição',

        // Formulário de Exposições
        'exhibition_history_description' => 'Registro de museus, galerias e exposições públicas onde esta obra foi exibida',
        'exhibition_title' => 'Título da Exposição',
        'exhibition_title_placeholder' => 'Nome da exposição...',
        'venue' => 'Local',
        'venue_placeholder' => 'Nome do museu, galeria ou instituição...',
        'exhibition_dates' => 'Datas da Exposição',
        'exhibition_notes' => 'Notas',
        'exhibition_notes_placeholder' => 'Número de catálogo, menções especiais, críticas...',
        'add_exhibition' => 'Adicionar Exposição',
        'save_exhibitions_data' => 'Salvar Dados de Exposições',

        // Formulário de Fotos
        'photo_documentation_description' => 'Imagens de alta qualidade para documentação e fins de arquivo',
        'photo_type' => 'Tipo de Foto',
        'photo_overall' => 'Visão Geral',
        'photo_detail' => 'Detalhe',
        'photo_raking' => 'Luz Rasante',
        'photo_uv' => 'Fotografia UV',
        'photo_infrared' => 'Infravermelho',
        'photo_back' => 'Verso',
        'photo_signature' => 'Assinatura/Marcas',
        'photo_frame' => 'Moldura/Montagem',
        'photo_description' => 'Descrição',
        'photo_description_placeholder' => 'Descreva o que esta foto mostra...',
        'save_photos_data' => 'Salvar Dados de Fotos',

        // Campos adicionais para o formulário de condição
        'select_condition' => 'Selecionar condição...',
        'detailed_assessment' => 'Avaliação Detalhada',
        'detailed_assessment_placeholder' => 'Descrição detalhada da condição, incluindo quaisquer danos, restaurações ou características especiais...',
        'conservation_history_placeholder' => 'Tratamentos de conservação anteriores, datas e conservadores...',
        'assessor_information' => 'Informações do Avaliador',
        'assessor_placeholder' => 'Nome e credenciais do avaliador da condição...',
        'save_condition_report' => 'Salvar Relatório de Condição',

        // Campos do formulário de exposições
        'major_exhibitions' => 'Exposições Principais',
        'major_exhibitions_placeholder' => 'Liste exposições principais, museus, galerias, datas...',
        'publications_catalogues' => 'Publicações e Catálogos',
        'publications_placeholder' => 'Livros, catálogos, artigos onde esta obra foi publicada...',
        'awards_recognition' => 'Prêmios e Reconhecimentos',
        'awards_placeholder' => 'Prêmios, reconhecimentos, críticas recebidas...',
        'save_exhibition_history' => 'Salvar Histórico de Exposições',
        'exhibition_history_description' => 'Registro de exposições onde esta obra foi exibida',

        // Campos do formulário de fotos
        'click_upload_images' => 'Clique para carregar imagens',
        'png_jpg_webp' => 'PNG, JPG, WEBP até 10MB cada',
        'photo_descriptions' => 'Descrições de Fotos',
        'photo_descriptions_placeholder' => 'Descreva as imagens: condições de iluminação, detalhes capturados, finalidade...',
        'photographer_credits' => 'Créditos do Fotógrafo',
        'photographer_placeholder' => 'Nome do fotógrafo e data...',
        'save_photo_documentation' => 'Salvar Documentação Fotográfica',
        'photo_documentation_description' => 'Imagens de alta resolução para documentação e fins de seguro',

        // Ações do Modal
        'close' => 'Fechar',
        'error_no_certificate' => 'Erro: Nenhum certificado selecionado',
        'saving' => 'Salvando...',
        'annex_saved_success' => 'Dados do anexo salvos com sucesso!',
        'error_saving_annex' => 'Erro ao salvar os dados do anexo',

        // Traduções faltantes para barra lateral e componentes CoA
        'certificate' => 'Certificado CoA',
        'no_certificate' => 'Nenhum Certificado',
        'certificate_active' => 'Certificado Ativo',
        'serial_number' => 'Número de Série',
        'issue_date' => 'Data de Emissão',
        'expires' => 'Expira',
        'no_certificate_issued' => 'Este EGI não possui um Certificado de Autenticidade',
        'issue_certificate' => 'Emitir Certificado',
        'certificate_issued_successfully' => 'Certificado emitido com sucesso!',
        'pdf_generated_automatically' => 'PDF gerado automaticamente!',
        'download_pdf_now' => 'Deseja baixar o PDF agora?',
        'digital_signatures' => 'Assinaturas Digitais',
        'signature_by' => 'Assinado por',
        'signature_role' => 'Papel',
        'signature_provider' => 'Provedor',
        'signature_date' => 'Data da Assinatura',
        'unknown_signer' => 'Assinante Desconhecido',
        'step_creating_certificate' => 'Criando certificado...',
        'step_generating_snapshot' => 'Gerando instantâneo...',
        'step_generating_pdf' => 'Gerando PDF...',
        'step_finalizing' => 'Finalizando...',
        'generating' => 'Gerando...',
        'generating_pdf' => 'Gerando PDF...',
        'error_issuing_certificate' => 'Erro ao emitir o certificado: ',
        'issuing' => 'Emitindo...',
        'unlock_with_coa_pro' => 'Desbloquear com CoA Pro',
        'provenance_documentation' => 'Documentação de Proveniência',
        'condition_reports' => 'Relatórios de Condição',
        'exhibition_history' => 'Histórico de Exposições',
        'professional_pdf' => 'PDF Profissional',
        'only_creator_can_issue' => 'Apenas o criador pode emitir certificados',

        // Sistema de Vocabulário de Traits CoA
        'traits_management_title' => 'Gerenciar Traits CoA',
        'traits_management_description' => 'Configure as características técnicas da obra para o Certificado de Autenticidade',
        'status_configured' => 'Configurado',
        'status_not_configured' => 'Não Configurado',
        'edit_traits' => 'Editar Traits',
        'no_technique_selected' => 'Nenhuma técnica selecionada',
        'no_materials_selected' => 'Nenhum material selecionado',
        'no_support_selected' => 'Nenhum suporte selecionado',
        'custom' => 'personalizado',
        'last_updated' => 'Última Atualização',
        'never_configured' => 'Nunca Configurado',
        'clear_all' => 'Limpar Tudo',
        'saved' => 'Salvo',

        // Modal de Vocabulário
        'modal_title' => 'Selecionar Traits CoA',
        'category_technique' => 'Técnica',
        'category_materials' => 'Materiais',
        'category_support' => 'Suporte',
        'search_placeholder' => 'Pesquisar termos...',
        'loading' => 'Carregando...',
        'selected_items' => 'Itens Selecionados',
        'no_items_selected' => 'Nenhum item selecionado',
        'add_custom' => 'Adicionar Personalizado',
        'custom_term_placeholder' => 'Insira um termo personalizado (máx. 60 caracteres)',
        'add' => 'Adicionar',
        'cancel' => 'Cancelar',
        'items_selected' => 'itens selecionados',
        'confirm' => 'Confirmar',

        // Componentes de Vocabulário
        'terms_available' => 'termos disponíveis',
        'no_categories_available' => 'Nenhuma categoria disponível',
        'no_categories_found' => 'Nenhuma categoria de vocabulário encontrada.',
        'search_results' => 'Resultados da Pesquisa',
        'results_for' => 'Para',
        'terms_found' => 'termos encontrados',
        'results_found' => 'resultados encontrados',
        'no_results_found' => 'Nenhum resultado encontrado',
        'no_terms_match_search' => 'Nenhum termo corresponde à pesquisa',
        'in_category' => 'na categoria',
        'clear_search' => 'Limpar Pesquisa',
        'no_terms_available' => 'Nenhum termo disponível',
        'no_terms_found_category' => 'Nenhum termo encontrado para a categoria',
        'categories' => 'Categorias',
        'back_to_start' => 'Voltar ao Início',
        'retry' => 'Tentar Novamente',
        'error' => 'Erro',
        'unexpected_error' => 'Ocorreu um erro inesperado.',
        'exhibition_history' => 'Histórico de Exposições',
        'professional_pdf_bundle' => 'Pacote PDF Profissional',
        'only_creator_can_issue' => 'Apenas o criador pode emitir certificados',
        'public_verification' => 'Verificação Pública',
        'verification_description' => 'Verifique a autenticidade de um Certificado de Autenticidade EGI',
        'verification_instructions' => 'Insira o número de série do certificado para verificar sua autenticidade',
        'enter_serial' => 'Insira o Número de Série',
        'serial_help' => 'Formato: ABC-123-DEF (letras, números e hífens)',
        'certificate_of_authenticity' => 'Certificado de Autenticidade',
        'public_verification_display' => 'Exibição de Verificação Pública',
        'verified_authentic' => 'Certificado Verificado e Autêntico',
        'verified_at' => 'Verificado em',
        'artwork_information' => 'Informações da Obra',
        'artwork_title' => 'Título da Obra',
        'creator' => 'Criador',
        'description' => 'Descrição',
        'certificate_details' => 'Detalhes do Certificado',
        'cryptographic_verification' => 'Verificação Criptográfica',
        'verify_again' => 'Verificar Novamente',
        'print_certificate' => 'Imprimir Certificado',
        'share_verification' => 'Compartilhar Verificação',
        'powered_by_florenceegi' => 'Desenvolvido por FlorenceEGI',
        'verification_timestamp' => 'Carimbo de Verificação',
        'link_copied' => 'Link copiado para a área de transferência',
        'issuing' => 'Emitindo...',
        'certificate_issued_successfully' => 'Certificado emitido com sucesso!',
        'error_issuing_certificate' => 'Erro ao emitir o certificado: ',
        'reissue_certificate_confirm' => 'Reemitir este certificado? Uma nova versão será criada.',
        'certificate_reissued_successfully' => 'Certificado reemitido com sucesso!',
        'error_reissuing_certificate' => 'Erro ao reemitir o certificado: ',
        'revoke_certificate_confirm' => 'Revogar este certificado? Esta ação não pode ser desfeita.',
        'reason_for_revocation' => 'Motivo da Revogação:',
        'certificate_revoked_successfully' => 'Certificado revogado com sucesso!',
        'error_revoking_certificate' => 'Erro ao revogar o certificado: ',
        'manage_certificate' => 'Gerenciar Certificado',
        'annex_management_coming_soon' => 'Gerenciamento de anexos em breve!',
        'issue_certificate_description' => 'Emita um certificado para fornecer prova de autenticidade e desbloquear funções Pro',
        'serial' => 'Série',
        'pro_features' => 'Recursos Pro',
        'provenance_docs' => 'Documentação de Proveniência',
        'professional_pdf' => 'PDF Profissional',
        'unlock_pro_features' => 'Desbloquear Recursos Pro',
        'reason_for' => 'Motivo para',

        // Emblemas de Assinaturas QES
        'badge_author_signed' => 'Assinado pelo Autor (QES)',
        'badge_inspector_signed' => 'Assinado pelo Inspetor (QES)',
        'badge_integrity_ok' => 'Integridade Verificada',

        // Interface de Localização (CoA)
        'issue_place' => 'Local de Emissão',
        'location_placeholder' => 'Ex. Florença, Toscana, Itália',
        'save' => 'Salvar',
        'location_hint' => 'Use o formato "Cidade, Região/Província, País" (ou equivalente).',
        'location_required' => 'A localização é obrigatória',
        'location_saved' => 'Localização salva',
        'location_save_failed' => 'Falha ao salvar a localização',
        'location_updated' => 'Localização atualizada com sucesso',

        // Co-assinatura do Inspetor (QES)
        'inspector_countersign' => 'Co-assinatura do Inspetor (QES)',
        'confirm_inspector_countersign' => 'Prosseguir com a co-assinatura do inspetor?',
        'inspector_countersign_applied' => 'Co-assinatura do inspetor aplicada',
        'operation_failed' => 'Operação falhou',
        'author_countersign' => 'Assinatura do Autor (QES)',
        'confirm_author_countersign' => 'Prosseguir com a assinatura do autor?',
        'author_countersign_applied' => 'Assinatura do autor aplicada',
        'regenerate_pdf' => 'Regenerar PDF',
        'pdf_regenerated' => 'PDF regenerado',
        'pdf_regenerate_failed' => 'Falha ao regenerar o PDF',

        // Página de Verificação Pública
        'public_verify' => [
            'signature' => 'Assinatura',
            'author_signed' => 'Assinado pelo Autor',
            'inspector_countersigned' => 'Co-assinado pelo Inspetor',
            'timestamp_tsa' => 'Carimbo TSA',
            'qes' => 'QES',
            'wallet_signature' => 'Assinatura da Carteira',
            'verify_signature' => 'verificar assinatura',
            'certificate_hash' => 'Hash do Certificado (SHA-256)',
            'pdf_hash' => 'Hash do PDF (SHA-256)',
            'copy_hash' => 'Copiar Hash',
            'copy_pdf_hash' => 'Copiar Hash do PDF',
            'hash_copied' => 'Hash copiado para a área de transferência!',
            'pdf_hash_copied' => 'Hash do PDF copiado para a área de transferência!',
            'qr_code_verify' => 'Verificação de Código QR',
            'qr_code' => 'Código QR',
            'scan_to_verify' => 'Escanear para Verificar',
            'status' => 'Estado',
            'valid' => 'Válido',
            'incomplete' => 'Incompleto',
            'revoked' => 'Revogado',

            // Cabeçalhos e Títulos
            'certificate_title' => 'Certificado de Autenticidade',
            'public_verification_display' => 'Exibição de Verificação Pública',
            'verified_authentic' => 'Certificado Verificado e Autêntico',
            'verified_at' => 'Verificado em',
            'serial_number' => 'Número de Série',
            'certificate_not_ready' => 'Certificado Não Pronto',
            'certificate_revoked' => 'Certificado Revogado',
            'certificate_not_valid' => 'Este certificado não é mais válido',
            'requires_coa_traits' => 'Requer Traits CoA',
            'certificate_not_ready_generic' => 'Certificado Não Pronto - Traits Genéricos',

            // Informações da Obra
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

            // Informações do Certificado
            'issue_date' => 'Data de Emissão',
            'issued_by' => 'Emitido por',
            'issue_location' => 'Local de Emissão',
            'notes' => 'Notas',

            // Anexos Profissionais
            'professional_annexes' => 'Anexos Profissionais',
            'provenance' => 'Proveniência',
            'condition_report' => 'Relatório de Condição',
            'exhibitions_publications' => 'Exposições/Publicações',
            'additional_photos' => 'Fotos Adicionais',

            // Informações na Cadeia
            'on_chain_info' => 'Informações na Cadeia',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Sistema de Dossiê - Sistema de Dossiê
    |--------------------------------------------------------------------------
    */
    'dossier' => [
        'title' => 'Dossiê de Imagens',
        'loading' => 'Carregando dossiê...',
        'view_complete' => 'Ver dossiê de imagens completo',
        'close' => 'Fechar Dossiê',

        // Informações da Obra
        'artwork_info' => 'Informações da Obra',
        'author' => 'Autor',
        'year' => 'Ano',
        'internal_id' => 'ID Interno',

        // Informações do Dossiê
        'dossier_info' => 'Informações do Dossiê',
        'images_count' => 'Imagens',
        'type' => 'Tipo',
        'utility_gallery' => 'Galeria de Utilidade',

        // Galeria
        'gallery_title' => 'Galeria de Imagens',
        'image_number' => 'Imagem :number',
        'image_of_total' => 'Imagem :current de :total',

        // Estados
        'no_utility_title' => 'Dossiê não disponível',
        'no_utility_message' => 'Nenhuma imagem adicional disponível para esta obra.',
        'no_utility_description' => 'O dossiê de imagens adicionais ainda não foi configurado para esta obra.',

        'no_images_title' => 'Nenhuma imagem disponível',
        'no_images_message' => 'O dossiê existe, mas ainda não contém imagens.',
        'no_images_description' => 'Imagens adicionais serão adicionadas no futuro pelo criador da obra.',

        'error_title' => 'Erro',
        'error_loading' => 'Erro ao carregar o dossiê',

        // Navegação
        'previous_image' => 'Imagem Anterior',
        'next_image' => 'Imagem Seguinte',
        'close_viewer' => 'Fechar Visualizador',
        'of' => 'de',

        // Controles de Zoom
        'zoom_help' => 'Use a roda do mouse ou toque para zoom • Arraste para mover',
        'zoom_in' => 'Aumentar Zoom',
        'zoom_out' => 'Reduzir Zoom',
        'zoom_reset' => 'Redefinir Zoom',
        'zoom_fit' => 'Ajustar à Tela',
    ],

];
