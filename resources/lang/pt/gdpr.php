<?php
// resources/lang/pt/gdpr.php

return [
    /*
    |--------------------------------------------------------------------------
    | Linhas de Idioma GDPR
    |--------------------------------------------------------------------------
    |
    | As seguintes linhas de idioma são usadas para funcionalidades relacionadas ao GDPR.
    |
    */

    // Geral
    'gdpr' => 'GDPR',
    'gdpr_center' => 'Centro de Controle de Dados GDPR',
    'dashboard' => 'Painel de Controle',
    'back_to_dashboard' => 'Voltar ao Painel de Controle',
    'save' => 'Salvar',
    'submit' => 'Enviar',
    'cancel' => 'Cancelar',
    'continue' => 'Continuar',
    'loading' => 'Carregando...',
    'success' => 'Sucesso',
    'error' => 'Erro',
    'warning' => 'Aviso',
    'info' => 'Informação',
    'enabled' => 'Habilitado',
    'disabled' => 'Desabilitado',
    'active' => 'Ativo',
    'inactive' => 'Inativo',
    'pending' => 'Pendente',
    'completed' => 'Concluído',
    'failed' => 'Falhou',
    'processing' => 'Processando',
    'retry' => 'Tentar novamente',
    'required_field' => 'Campo obrigatório',
    'required_consent' => 'Consentimento obrigatório',
    'select_all_categories' => 'Selecionar todas as categorias',
    'no_categories_selected' => 'Nenhuma categoria selecionada',
    'compliance_badge' => 'Selo de conformidade',

    'consent_types' => [
        'terms-of-service' => [
            'name' => 'Termos de Serviço',
            'description' => 'Aceitação dos termos para uso da plataforma.',
        ],
        'privacy-policy' => [
            'name' => 'Política de Privacidade',
            'description' => 'Reconhecimento de como os dados pessoais são processados.',
        ],
        'age-confirmation' => [
            'name' => 'Confirmação de Idade',
            'description' => 'Confirmação de ter pelo menos 18 anos.',
        ],
        'analytics' => [
            'name' => 'Análise e melhoria da plataforma',
            'description' => 'Ajude-nos a melhorar a FlorenceEGI compartilhando dados de uso anônimos.',
        ],
        'marketing' => [
            'name' => 'Comunicações promocionais',
            'description' => 'Receba atualizações sobre novas funcionalidades, eventos e oportunidades.',
        ],
        'personalization' => [
            'name' => 'Personalização de conteúdos',
            'description' => 'Permite a personalização de conteúdos e recomendações.',
        ],
        'collaboration_participation' => [
            'name' => 'Participação em Colaborações',
            'description' => 'Consentimento para participar de colaborações de coleções, compartilhamento de dados e atividades colaborativas.',
        ],
        'purposes' => [
            'account_management' => 'Gestão da Conta do Usuário',
            'service_delivery'   => 'Prestação dos Serviços Solicitados',
            'legal_compliance'   => 'Conformidade Legal e Regulatória',
            'customer_support'   => 'Suporte ao Cliente e Assistência',
        ],
    ],

    // Migalhas de Pão (Breadcrumb)
    'breadcrumb' => [
        'dashboard' => 'Painel de Controle',
        'gdpr' => 'Privacidade e GDPR',
    ],

    // Mensagens de Alerta
    'alerts' => [
        'success' => 'Operação concluída!',
        'error' => 'Erro:',
        'warning' => 'Aviso:',
        'info' => 'Informação:',
    ],

    // Itens do Menu
    'menu' => [
        'gdpr_center' => 'Centro de Controle de Dados GDPR',
        'consent_management' => 'Gestão de Consentimentos',
        'data_export' => 'Exportar Meus Dados',
        'processing_restrictions' => 'Restringir o Processamento de Dados',
        'delete_account' => 'Excluir Minha Conta',
        'breach_report' => 'Reportar uma Violação de Dados',
        'activity_log' => 'Registro das Minhas Atividades GDPR',
        'privacy_policy' => 'Política de Privacidade',
    ],

    // Gestão de Consentimentos
    'consent' => [
        'title' => 'Gerencie Suas Preferências de Consentimento',
        'description' => 'Controle como seus dados são usados em nossa plataforma. Você pode atualizar suas preferências a qualquer momento.',
        'update_success' => 'Suas preferências de consentimento foram atualizadas.',
        'update_error' => 'Ocorreu um erro ao atualizar suas preferências de consentimento. Tente novamente.',
        'save_all' => 'Salvar Todas as Preferências',
        'last_updated' => 'Última atualização:',
        'never_updated' => 'Nunca atualizado',
        'privacy_notice' => 'Aviso de Privacidade',
        'not_given' => 'Não Fornecido',
        'given_at' => 'Fornecido em',
        'your_consents' => 'Seus Consentimentos',
        'subtitle' => 'Gerencie suas preferências de privacidade e veja o status dos seus consentimentos.',
        'breadcrumb' => 'Consentimentos',
        'history_title' => 'Histórico de Consentimentos',
        'back_to_consents' => 'Voltar aos Consentimentos',
        'preferences_title' => 'Gestão de Preferências de Consentimento',
        'preferences_subtitle' => 'Configure suas preferências de privacidade detalhadas',
        'preferences_breadcrumb' => 'Preferências',
        'preferences_info_title' => 'Gestão Granular de Consentimentos',
        'preferences_info_description' => 'Aqui você pode configurar cada tipo de consentimento em detalhes...',
        'required' => 'Obrigatório',
        'optional' => 'Opcional',
        'toggle_label' => 'Ativar/Desativar',
        'always_enabled' => 'Sempre Ativo',
        'benefits_title' => 'Benefícios para Você',
        'consequences_title' => 'Se Desativar',
        'third_parties_title' => 'Serviços de Terceiros',
        'save_preferences' => 'Salvar Preferências',
        'back_to_overview' => 'Voltar à Visão Geral',
        'never_updated' => 'Nunca atualizado',

        // Detalhes do Consentimento
        'given_at' => 'Fornecido em',
        'withdrawn_at' => 'Retirado em',
        'not_given' => 'Não fornecido',
        'method' => 'Método',
        'version' => 'Versão',
        'unknown_version' => 'Versão desconhecida',

        // Ações
        'withdraw' => 'Retirar o Consentimento',
        'withdraw_confirm' => 'Tem certeza de que deseja retirar este consentimento? Esta ação pode limitar algumas funcionalidades.',
        'renew' => 'Renovar o Consentimento',
        'view_history' => 'Ver Histórico',

        // Estados Vazios
        'no_consents' => 'Nenhum Consentimento Presente',
        'no_consents_description' => 'Você ainda não forneceu nenhum consentimento para o processamento de dados. Você pode gerenciar suas preferências usando o botão abaixo.',

        // Gestão de Preferências
        'manage_preferences' => 'Gerenciar Suas Preferências',
        'update_preferences' => 'Atualizar as Preferências de Privacidade',

        // Status do Consentimento
        'status' => [
            'granted' => 'Concedido',
            'denied' => 'Negado',
            'active' => 'Ativo',
            'withdrawn' => 'Retirado',
            'expired' => 'Expirado',
            'pending' => 'Pendente',
            'in_progress' => 'Em andamento',
            'completed' => 'Concluído',
            'failed' => 'Falhou',
            'rejected' => 'Rejeitado',
            'verification_required' => 'Verificação necessária',
            'cancelled' => 'Cancelado',
        ],

        // Resumo do Painel
        'summary' => [
            'active' => 'Consentimentos Ativos',
            'total' => 'Consentimentos Totais',
            'compliance' => 'Pontuação de Conformidade',
        ],

        // Métodos de Consentimento
        'methods' => [
            'web' => 'Interface Web',
            'api' => 'API',
            'import' => 'Importação',
            'admin' => 'Administrador',
        ],

        // Propósitos do Consentimento
        'purposes' => [
            'functional' => 'Consentimentos Funcionais',
            'analytics' => 'Consentimentos Analíticos',
            'marketing' => 'Consentimentos de Marketing',
            'profiling' => 'Consentimentos de Perfilação',
            'platform-services' => 'Serviços da Plataforma',
            'terms-of-service' => 'Termos de Serviço',
            'privacy-policy' => 'Política de Privacidade',
            'age-confirmation' => 'Confirmação de Idade',
            'personalization' => 'Personalização de Conteúdos',
            'allow-personal-data-processing' => 'Permitir Processamento de Dados Pessoais',
            'collaboration_participation' => 'Participação em Colaborações',
        ],

        // Descrições dos Consentimentos
        'descriptions' => [
            'functional' => 'Necessários para o funcionamento básico da plataforma e para fornecer os serviços solicitados.',
            'analytics' => 'Usados para analisar o uso do site e melhorar a experiência do usuário.',
            'marketing' => 'Usados para enviar comunicações promocionais e ofertas personalizadas.',
            'profiling' => 'Usados para criar perfis personalizados e sugerir conteúdos relevantes.',
            'platform-services' => 'Consentimentos necessários para a gestão da conta, segurança e suporte ao cliente.',
            'terms-of-service' => 'Aceitação dos Termos de Serviço para o uso da plataforma.',
            'privacy-policy' => 'Aceitação da nossa Política de Privacidade e do processamento de dados pessoais.',
            'age-confirmation' => 'Confirmação de ter idade legal para o uso da plataforma.',
            'personalization' => 'Permite a personalização de conteúdos e recomendações com base nas suas preferências.',
            'allow-personal-data-processing' => 'Permite o processamento dos seus dados pessoais para melhorar nossos serviços e fornecer uma experiência personalizada.',
            'collaboration_participation' => 'Permite a participação em projetos colaborativos e atividades compartilhadas com outros usuários da plataforma.',
        ],

        'essential' => [
            'label' => 'Cookies Essenciais',
            'description' => 'Esses cookies são necessários para o funcionamento do site e não podem ser desativados em nossos sistemas.',
        ],
        'functional' => [
            'label' => 'Cookies Funcionais',
            'description' => 'Esses cookies permitem que o site forneça funcionalidades avançadas e personalização.',
        ],
        'analytics' => [
            'label' => 'Cookies Analíticos',
            'description' => 'Esses cookies nos permitem contar visitas e fontes de tráfego para medir e melhorar o desempenho do nosso site.',
        ],
        'marketing' => [
            'label' => 'Cookies de Marketing',
            'description' => 'Esses cookies podem ser configurados em nosso site por nossos parceiros de publicidade para criar um perfil dos seus interesses.',
        ],
        'profiling' => [
            'label' => 'Perfilação',
            'description' => 'Usamos a perfilação para entender melhor suas preferências e personalizar nossos serviços de acordo com suas necessidades.',
        ],

        'allow_personal_data_processing' => [
            'label' => 'Consentimento para Processamento de Dados Pessoais',
            'description' => 'Permite o processamento dos seus dados pessoais para melhorar nossos serviços e fornecer uma experiência personalizada.',
        ],

        'saving_consent' => 'Salvando...',
        'consent_saved' => 'Salvo',
        'saving_all_consents' => 'Salvando todas as preferências...',
        'all_consents_saved' => 'Todas as preferências de consentimento foram salvas com sucesso.',
        'all_consents_save_error' => 'Ocorreu um erro ao salvar todas as preferências de consentimento.',
        'consent_save_error' => 'Ocorreu um erro ao salvar esta preferência de consentimento.',

        // Propósitos do Processamento
        'processing_purposes' => [
            'functional' => 'Operações essenciais da plataforma: autenticação, segurança, prestação de serviços, armazenamento de preferências do usuário',
            'analytics' => 'Melhoria da plataforma: análise de uso, monitoramento de desempenho, otimização da experiência do usuário',
            'marketing' => 'Comunicação: newsletters, atualizações de produtos, ofertas promocionais, notificações de eventos',
            'profiling' => 'Personalização: recomendações de conteúdo, análise de comportamento do usuário, sugestões direcionadas',
        ],

        // Períodos de Retenção
        'retention_periods' => [
            'functional' => 'Duração da conta + 1 ano para conformidade legal',
            'analytics' => '2 anos desde a última atividade',
            'marketing' => '3 anos desde a última interação ou retirada do consentimento',
            'profiling' => '1 ano desde a última atividade ou retirada do consentimento',
        ],

        // Benefícios para o Usuário
        'user_benefits' => [
            'functional' => [
                'Acesso seguro à sua conta',
                'Configurações de usuário personalizadas',
                'Desempenho confiável da plataforma',
                'Proteção contra fraudes e abusos',
            ],
            'analytics' => [
                'Desempenho aprimorado da plataforma',
                'Design otimizado da experiência do usuário',
                'Tempos de carregamento mais rápidos',
                'Desenvolvimento de funcionalidades aprimoradas',
            ],
            'marketing' => [
                'Atualizações de produtos relevantes',
                'Ofertas e promoções exclusivas',
                'Convites para eventos e anúncios',
                'Conteúdos educativos e sugestões',
            ],
            'profiling' => [
                'Recomendações de conteúdo personalizadas',
                'Experiência de usuário personalizada',
                'Sugestões de projetos relevantes',
                'Painel de controle e funcionalidades personalizadas',
            ],
        ],

        // Serviços de Terceiros
        'third_parties' => [
            'functional' => [
                'Fornecedores de CDN (distribuição de conteúdo estático)',
                'Serviços de segurança (prevenção de fraudes)',
                'Fornecedores de infraestrutura (hospedagem)',
            ],
            'analytics' => [
                'Plataformas de análise (dados de uso anonimizados)',
                'Serviços de monitoramento de desempenho',
                'Serviços de rastreamento de erros',
            ],
            'marketing' => [
                'Fornecedores de serviços de e-mail',
                'Plataformas de automação de marketing',
                'Plataformas de redes sociais (para publicidade)',
            ],
            'profiling' => [
                'Motores de recomendação',
                'Serviços de análise comportamental',
                'Plataformas de personalização de conteúdo',
            ],
        ],

        // Consequências da Retirada
        'withdrawal_consequences' => [
            'functional' => [
                'Não pode ser retirado - essencial para o funcionamento da plataforma',
                'O acesso à conta seria comprometido',
                'As funcionalidades de segurança seriam desativadas',
            ],
            'analytics' => [
                'As melhorias da plataforma podem não refletir seus padrões de uso',
                'Experiência genérica em vez de desempenho otimizado',
                'Nenhum impacto nas funcionalidades principais',
            ],
            'marketing' => [
                'Nenhum e-mail promocional ou atualização',
                'Você pode perder anúncios importantes',
                'Nenhum impacto na funcionalidade da plataforma',
                'Pode ser reativado a qualquer momento',
            ],
            'profiling' => [
                'Conteúdos genéricos em vez de recomendações personalizadas',
                'Layout de painel padrão',
                'Sugestões de projetos menos relevantes',
                'Nenhum impacto nas funcionalidades principais da plataforma',
            ],
        ],
    ],

    // Exportação de Dados
    'export' => [
        'title' => 'Exportar Seus Dados',
        'subtitle' => 'Solicite uma cópia completa de seus dados pessoais em formato portátil',
        'description' => 'Solicite uma cópia de seus dados pessoais. O processamento pode levar alguns minutos.',

        // Categorias de Dados
        'select_data_categories' => 'Selecione as Categorias de Dados para Exportar',
        'categories' => [
            'profile' => 'Informações do Perfil',
            'account' => 'Detalhes da Conta',
            'preferences' => 'Preferências e Configurações',
            'activity' => 'Histórico de Atividades',
            'consents' => 'Histórico de Consentimentos',
            'collections' => 'Coleções e NFTs',
            'purchases' => 'Compras e Transações',
            'comments' => 'Comentários e Revisões',
            'messages' => 'Mensagens e Comunicações',
            'biography' => 'Biografias e Conteúdos',
        ],

        // Descrições das Categorias
        'category_descriptions' => [
            'profile' => 'Dados pessoais, informações de contato, foto de perfil e descrições pessoais',
            'account' => 'Detalhes da conta, configurações de segurança, histórico de login e alterações',
            'preferences' => 'Preferências do usuário, configurações de privacidade, configurações personalizadas',
            'activity' => 'Histórico de navegação, interações, visualizações e uso da plataforma',
            'consents' => 'Histórico de consentimentos de privacidade, alterações de preferências, trilha de auditoria GDPR',
            'collections' => 'Coleções de NFT criadas, metadados, propriedade intelectual e ativos',
            'purchases' => 'Transações, compras, faturas, métodos de pagamento e histórico de pedidos',
            'comments' => 'Comentários, revisões, avaliações e feedback deixados na plataforma',
            'messages' => 'Mensagens privadas, comunicações, notificações e conversas',
            'biography' => 'Biografias criadas, capítulos, linhas de tempo, mídia e conteúdos narrativos',
        ],

        // Formatos de Exportação
        'select_format' => 'Selecione o Formato de Exportação',
        'formats' => [
            'json' => 'JSON - Formato de Dados Estruturado',
            'csv' => 'CSV - Compatível com Planilhas',
            'pdf' => 'PDF - Documento Legível',
        ],

        // Descrições dos Formatos
        'format_descriptions' => [
            'json' => 'Formato de dados estruturado ideal para desenvolvedores e integrações. Mantém a estrutura completa dos dados.',
            'csv' => 'Formato compatível com Excel e Google Sheets. Perfeito para análise e manipulação de dados.',
            'pdf' => 'Documento legível e imprimível. Ideal para arquivamento e compartilhamento.',
        ],

        // Opções Adicionais
        'additional_options' => 'Opções Adicionais',
        'include_metadata' => 'Incluir Metadados Técnicos',
        'metadata_description' => 'Inclui informações técnicas como timestamps, endereços IP, versões e trilha de auditoria.',
        'include_audit_trail' => 'Incluir Registro Completo de Atividades',
        'audit_trail_description' => 'Inclui histórico completo de todas as alterações e atividades GDPR.',

        // Ações
        'request_export' => 'Solicitar Exportação de Dados',
        'request_success' => 'Solicitação de exportação enviada com sucesso. Você receberá uma notificação ao concluir.',
        'request_error' => 'Ocorreu um erro ao enviar a solicitação. Tente novamente.',

        // Histórico de Exportações
        'history_title' => 'Histórico de Exportações',
        'no_exports' => 'Nenhuma Exportação Presente',
        'no_exports_description' => 'Você ainda não solicitou nenhuma exportação de seus dados. Use o formulário acima para solicitar uma.',

        // Detalhes dos Itens de Exportação
        'export_format' => 'Exportação {format}',
        'requested_on' => 'Solicitado em',
        'completed_on' => 'Concluído em',
        'expires_on' => 'Expira em',
        'file_size' => 'Tamanho',
        'download' => 'Baixar',
        'download_export' => 'Baixar Exportação',

        // Status
        'status' => [
            'pending' => 'Pendente',
            'processing' => 'Processando',
            'completed' => 'Concluído',
            'failed' => 'Falhou',
            'expired' => 'Expirado',
        ],

        // Limitação de Frequência
        'rate_limit_title' => 'Limite de Exportações Atingido',
        'rate_limit_message' => 'Você atingiu o limite máximo de {max} exportações por hoje. Tente novamente amanhã.',
        'last_export_date' => 'Última exportação: {date}',

        // Validação
        'select_at_least_one_category' => 'Selecione pelo menos uma categoria de dados para exportar.',

        // Suporte Legado
        'request_button' => 'Solicitar Exportação de Dados',
        'format' => 'Formato de Exportação',
        'format_json' => 'JSON (recomendado para desenvolvedores)',
        'format_csv' => 'CSV (compatível com planilhas)',
        'format_pdf' => 'PDF (documento legível)',
        'include_timestamps' => 'Incluir timestamps',
        'password_protection' => 'Proteger a exportação com senha',
        'password' => 'Senha de exportação',
        'confirm_password' => 'Confirmar senha',
        'data_categories' => 'Categorias de dados para exportar',
        'recent_exports' => 'Exportações Recentes',
        'no_recent_exports' => 'Você não tem exportações recentes.',
        'export_status' => 'Status de Exportação',
        'export_date' => 'Data de Exportação',
        'export_size' => 'Tamanho de Exportação',
        'export_id' => 'ID de Exportação',
        'export_preparing' => 'Preparando sua exportação de dados...',
        'export_queued' => 'Sua exportação está na fila e começará em breve...',
        'export_processing' => 'Processando sua exportação de dados...',
        'export_ready' => 'Sua exportação de dados está pronta para download.',
        'export_failed' => 'Sua exportação de dados falhou.',
        'export_failed_details' => 'Ocorreu um erro ao processar sua exportação de dados. Tente novamente ou entre em contato com o suporte.',
        'export_unknown_status' => 'Status de exportação desconhecido.',
        'check_status' => 'Verificar Status',
        'retry_export' => 'Tentar Exportação Novamente',
        'export_download_error' => 'Ocorreu um erro ao baixar sua exportação.',
        'export_status_error' => 'Erro ao verificar o status da exportação.',
        'limit_reached' => 'Você atingiu o número máximo de exportações permitidas por dia.',
        'existing_in_progress' => 'Você já tem uma exportação em andamento. Aguarde até que ela seja concluída.',
    ],

    // Restrições de Processamento
    'restriction' => [
        'title' => 'Restringir o Processamento de Dados',
        'description' => 'Você pode solicitar a restrição de como processamos seus dados em certas circunstâncias.',
        'active_restrictions' => 'Restrições Ativas',
        'no_active_restrictions' => 'Você não tem restrições de processamento ativas.',
        'request_new' => 'Solicitar Nova Restrição',
        'restriction_type' => 'Tipo de Restrição',
        'restriction_reason' => 'Motivo da Restrição',
        'data_categories' => 'Categorias de Dados',
        'notes' => 'Notas Adicionais',
        'notes_placeholder' => 'Forneça quaisquer detalhes adicionais para nos ajudar a entender sua solicitação...',
        'submit_button' => 'Enviar Solicitação de Restrição',
        'remove_button' => 'Remover Restrição',
        'processing_restriction_success' => 'Sua solicitação de restrição de processamento foi enviada.',
        'processing_restriction_failed' => 'Ocorreu um erro ao enviar sua solicitação de restrição de processamento.',
        'processing_restriction_system_error' => 'Ocorreu um erro de sistema ao processar sua solicitação.',
        'processing_restriction_removed' => 'A restrição de processamento foi removida.',
        'processing_restriction_removal_failed' => 'Ocorreu um erro ao remover a restrição de processamento.',
        'unauthorized_action' => 'Você não está autorizado a realizar esta ação.',
        'date_submitted' => 'Data de Envio',
        'expiry_date' => 'Expira em',
        'never_expires' => 'Nunca Expira',
        'status' => 'Status',
        'limit_reached' => 'Você atingiu o número máximo de restrições ativas permitidas.',
        'categories' => [
            'profile' => 'Informações do Perfil',
            'activity' => 'Rastreamento de Atividades',
            'preferences' => 'Preferências e Configurações',
            'collections' => 'Coleções e Conteúdos',
            'purchases' => 'Compras e Transações',
            'comments' => 'Comentários e Revisões',
            'messages' => 'Mensagens e Comunicações',
        ],
        'types' => [
            'processing' => 'Restringir Todo o Processamento',
            'automated_decisions' => 'Restringir Decisões Automatizadas',
            'marketing' => 'Restringir o Processamento de Marketing',
            'analytics' => 'Restringir o Processamento Analítico',
            'third_party' => 'Restringir o Compartilhamento com Terceiros',
            'profiling' => 'Restringir a Perfilação',
            'data_sharing' => 'Restringir o Compartilhamento de Dados',
            'removed' => 'Remover Restrição',
            'all' => 'Restringir Todo o Processamento',
        ],
        'reasons' => [
            'accuracy_dispute' => 'Contesto a precisão dos meus dados',
            'processing_unlawful' => 'O processamento é ilícito',
            'no_longer_needed' => 'Você não precisa mais dos meus dados, mas eu preciso deles para reivindicações legais',
            'objection_pending' => 'Objetei ao processamento e estou aguardando verificação',
            'legitimate_interest' => 'Motivos legítimos prementes',
            'legal_claims' => 'Para a defesa de reivindicações legais',
            'other' => 'Outro motivo (especificar nas notas)',
        ],
        'descriptions' => [
            'processing' => 'Restringe o processamento dos seus dados pessoais enquanto sua solicitação é verificada.',
            'automated_decisions' => 'Restringe decisões automatizadas que podem afetar seus direitos.',
            'marketing' => 'Restringe o processamento dos seus dados para fins de marketing direto.',
            'analytics' => 'Restringe o processamento dos seus dados para fins analíticos e de monitoramento.',
            'third_party' => 'Restringe o compartilhamento dos seus dados com terceiros.',
            'profiling' => 'Restringe a perfilação dos seus dados pessoais.',
            'data_sharing' => 'Restringe o compartilhamento dos seus dados com outros serviços ou plataformas.',
            'all' => 'Restringe todas as formas de processamento dos seus dados pessoais.',
        ],
    ],

    // Exclusão de Conta
    'deletion' => [
        'title' => 'Excluir Minha Conta',
        'description' => 'Isso iniciará o processo para excluir sua conta e todos os dados associados.',
        'warning' => 'Aviso: A exclusão da conta é permanente e não pode ser desfeita.',
        'processing_delay' => 'Sua conta está programada para exclusão em :days dias.',
        'confirm_deletion' => 'Entendo que esta ação é permanente e não pode ser desfeita.',
        'password_confirmation' => 'Insira sua senha para confirmar',
        'reason' => 'Motivo da exclusão (opcional)',
        'additional_comments' => 'Comentários adicionais (opcional)',
        'submit_button' => 'Solicitar Exclusão de Conta',
        'request_submitted' => 'Sua solicitação de exclusão de conta foi enviada.',
        'request_error' => 'Ocorreu um erro ao enviar sua solicitação de exclusão de conta.',
        'pending_deletion' => 'Sua conta está programada para exclusão em :date.',
        'cancel_deletion' => 'Cancelar Solicitação de Exclusão',
        'cancellation_success' => 'Sua solicitação de exclusão de conta foi cancelada.',
        'cancellation_error' => 'Ocorreu um erro ao cancelar sua solicitação de exclusão de conta.',
        'reasons' => [
            'no_longer_needed' => 'Não preciso mais deste serviço',
            'privacy_concerns' => 'Preocupações com privacidade',
            'moving_to_competitor' => 'Mudança para outro serviço',
            'unhappy_with_service' => 'Insatisfeito com o serviço',
            'other' => 'Outro motivo',
        ],
        'confirmation_email' => [
            'subject' => 'Confirmação de Solicitação de Exclusão de Conta',
            'line1' => 'Recebemos sua solicitação para excluir sua conta.',
            'line2' => 'Sua conta está programada para exclusão em :date.',
            'line3' => 'Se você não solicitou esta ação, entre em contato conosco imediatamente.',
        ],
        'data_retention_notice' => 'Observe que alguns dados anonimizados podem ser retidos para fins legais e analíticos.',
        'blockchain_data_notice' => 'Os dados armazenados em blockchain não podem ser completamente excluídos devido à natureza imutável da tecnologia.',
    ],

    // Relatório de Violação
    'breach' => [
        'title' => 'Reportar uma Violação de Dados',
        'description' => 'Se você acredita que houve uma violação dos seus dados pessoais, reporte aqui.',
        'reporter_name' => 'Seu Nome',
        'reporter_email' => 'Seu E-mail',
        'incident_date' => 'Quando ocorreu o incidente?',
        'breach_description' => 'Descreva a possível violação',
        'breach_description_placeholder' => 'Forneça o máximo de detalhes possível sobre a possível violação de dados...',
        'affected_data' => 'Quais dados você acredita que foram comprometidos?',
        'affected_data_placeholder' => 'Por exemplo, informações pessoais, dados financeiros, etc.',
        'discovery_method' => 'Como você descobriu essa possível violação?',
        'supporting_evidence' => 'Evidência de Suporte (opcional)',
        'upload_evidence' => 'Carregar Evidência',
        'file_types' => 'Tipos de arquivo aceitos: PDF, JPG, JPEG, PNG, TXT, DOC, DOCX',
        'max_file_size' => 'Tamanho máximo do arquivo: 10MB',
        'consent_to_contact' => 'Consinto ser contatado em relação a este relatório',
        'submit_button' => 'Enviar Relatório de Violação',
        'report_submitted' => 'Seu relatório de violação foi enviado.',
        'report_error' => 'Ocorreu um erro ao enviar seu relatório de violação.',
        'thank_you' => 'Obrigado pelo seu relatório',
        'thank_you_message' => 'Obrigado por relatar essa possível violação. Nossa equipe de proteção de dados investigará e poderá entrar em contato para mais informações.',
        'breach_description_min' => 'Forneça pelo menos 20 caracteres para descrever a possível violação.',
    ],

    // Registro de Atividades
    'activity' => [
        'title' => 'Registro das Minhas Atividades GDPR',
        'description' => 'Veja um registro de todas as suas atividades e solicitações relacionadas ao GDPR.',
        'no_activities' => 'Nenhuma atividade encontrada.',
        'date' => 'Data',
        'activity' => 'Atividade',
        'details' => 'Detalhes',
        'ip_address' => 'Endereço IP',
        'user_agent' => 'Agente do Usuário',
        'download_log' => 'Baixar Registro de Atividades',
        'filter' => 'Filtrar Atividades',
        'filter_all' => 'Todas as Atividades',
        'filter_consent' => 'Atividades de Consentimento',
        'filter_export' => 'Atividades de Exportação de Dados',
        'filter_restriction' => 'Atividades de Restrição de Processamento',
        'filter_deletion' => 'Atividades de Exclusão de Conta',
        'types' => [
            'consent_updated' => 'Preferências de Consentimento Atualizadas',
            'data_export_requested' => 'Exportação de Dados Solicitada',
            'data_export_completed' => 'Exportação de Dados Concluída',
            'data_export_downloaded' => 'Exportação de Dados Baixada',
            'processing_restricted' => 'Restrição de Processamento Solicitada',
            'processing_restriction_removed' => 'Restrição de Processamento Removida',
            'account_deletion_requested' => 'Exclusão de Conta Solicitada',
            'account_deletion_cancelled' => 'Exclusão de Conta Cancelada',
            'account_deletion_completed' => 'Exclusão de Conta Concluída',
            'breach_reported' => 'Violação de Dados Reportada',
        ],
    ],

    // Validação
    'validation' => [
        'consents_required' => 'As preferências de consentimento são obrigatórias.',
        'consents_format' => 'O formato das preferências de consentimento não é válido.',
        'consent_value_required' => 'O valor do consentimento é obrigatório.',
        'consent_value_boolean' => 'O valor do consentimento deve ser um booleano.',
        'format_required' => 'O formato de exportação é obrigatório.',
        'data_categories_required' => 'É necessário selecionar pelo menos uma categoria de dados.',
        'data_categories_format' => 'O formato das categorias de dados não é válido.',
        'data_categories_min' => 'É necessário selecionar pelo menos uma categoria de dados.',
        'data_categories_distinct' => 'As categorias de dados devem ser distintas.',
        'export_password_required' => 'A senha é obrigatória quando a proteção por senha está habilitada.',
        'export_password_min' => 'A senha deve ter pelo menos 8 caracteres.',
        'restriction_type_required' => 'O tipo de restrição é obrigatório.',
        'restriction_reason_required' => 'O motivo da restrição é obrigatório.',
        'notes_max' => 'As notas não podem exceder 500 caracteres.',
        'reporter_name_required' => 'Seu nome é obrigatório.',
        'reporter_email_required' => 'Seu e-mail é obrigatório.',
        'reporter_email_format' => 'Insira um endereço de e-mail válido.',
        'incident_date_required' => 'A data do incidente é obrigatória.',
        'incident_date_format' => 'A data do incidente deve ser uma data válida.',
        'incident_date_past' => 'A data do incidente deve ser no passado ou hoje.',
        'breach_description_required' => 'A descrição da violação é obrigatória.',
        'breach_description_min' => 'A descrição da violação deve ter pelo menos 20 caracteres.',
        'affected_data_required' => 'As informações sobre os dados comprometidos são obrigatórias.',
        'discovery_method_required' => 'O método de descoberta é obrigatório.',
        'supporting_evidence_format' => 'A evidência deve ser um arquivo PDF, JPG, JPEG, PNG, TXT, DOC ou DOCX.',
        'supporting_evidence_max' => 'O arquivo de evidência não pode exceder 10MB.',
        'consent_to_contact_required' => 'O consentimento para contato é obrigatório.',
        'consent_to_contact_accepted' => 'O consentimento para contato deve ser aceito.',
        'required_consent_message' => 'Este consentimento é necessário para usar a plataforma.',
        'confirm_deletion_required' => 'Você deve confirmar que entende as consequências da exclusão da conta.',
        'form_error_title' => 'Corrija os erros abaixo',
        'form_error_message' => 'Há um ou mais erros no formulário que precisam ser corrigidos.',
    ],

    // Mensagens de Erro
    'errors' => [
        'general' => 'Ocorreu um erro inesperado.',
        'unauthorized' => 'Você não está autorizado a realizar esta ação.',
        'forbidden' => 'Esta ação é proibida.',
        'not_found' => 'O recurso solicitado não foi encontrado.',
        'validation_failed' => 'Os dados enviados não são válidos.',
        'rate_limited' => 'Muitas solicitações. Tente novamente mais tarde.',
        'service_unavailable' => 'O serviço não está disponível no momento. Tente novamente mais tarde.',
    ],

    'requests' => [
        'types' => [
            'consent_update' => 'Solicitação de atualização de consentimento enviada.',
            'data_export' => 'Solicitação de exportação de dados enviada.',
            'processing_restriction' => 'Solicitação de restrição de processamento enviada.',
            'account_deletion' => 'Solicitação de exclusão de conta enviada.',
            'breach_report' => 'Relatório de violação de dados enviado.',
            'erasure' => 'Solicitação de exclusão de dados enviada.',
            'access' => 'Solicitação de acesso a dados enviada.',
            'rectification' => 'Solicitação de retificação de dados enviada.',
            'objection' => 'Solicitação de objeção ao processamento enviada.',
            'restriction' => 'Solicitação de limitação de processamento enviada.',
            'portability' => 'Solicitação de portabilidade de dados enviada.',
        ],
    ],

    // Version Information
    'current_version' => 'Versão Atual',
    'version' => 'Versão: 1.0',
    'effective_date' => 'Data de Vigência: 30 de Setembro de 2025',
    'last_updated' => 'Última Atualização: 30 de Setembro de 2025, 17:41',

    // Actions
    'download_pdf' => 'Baixar PDF',
    'print' => 'Imprimir',

    'modal' => [
        'clarification' => [
            'title' => 'Esclarecimento Necessário',
            'explanation' => 'Para garantir sua segurança, precisamos entender o motivo da sua ação:',
        ],
        'revoke_button_text' => 'Mudei de ideia',
        'revoke_description' => 'Você deseja simplesmente retirar o consentimento previamente dado.',
        'disavow_button_text' => 'Não reconheço esta ação',
        'disavow_description' => 'Você nunca deu este consentimento (possível problema de segurança).',

        'confirmation' => [
            'title' => 'Confirmar Protocolo de Segurança',
            'warning' => 'Esta ação ativará um protocolo de segurança que inclui:',
        ],
        'confirm_disavow' => 'Sim, ativar protocolo de segurança',
        'final_warning' => 'Prossiga apenas se tiver certeza de que nunca autorizou esta ação.',

        'consequences' => [
            'consent_revocation' => 'Retirada imediata do consentimento',
            'security_notification' => 'Notificação à equipe de segurança',
            'account_review' => 'Possíveis verificações adicionais na conta',
            'email_confirmation' => 'E-mail de confirmação com instruções',
        ],

        'security' => [
            'title' => 'Protocolo de Segurança Ativado',
            'understood' => 'Entendido',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Seção de Notificações GDPR
    |--------------------------------------------------------------------------
    | Movido de `notification.php` para centralização.
    */
    'notifications' => [
        'acknowledged' => 'Confirmação registrada.',
        'consent_updated' => [
            'title' => 'Preferências de Privacidade Atualizadas',
            'content' => 'Suas preferências de consentimento foram atualizadas com sucesso.',
        ],
        'data_exported' => [
            'title' => 'Sua Exportação de Dados está Pronta',
            'content' => 'Sua solicitação de exportação de dados foi processada. Você pode baixar o arquivo pelo link fornecido.',
        ],
        'processing_restricted' => [
            'title' => 'Restrição de Processamento Aplicada',
            'content' => 'Aplicamos com sucesso sua solicitação para restringir o processamento de dados para a categoria: :type.',
        ],
        'account_deletion_requested' => [
            'title' => 'Solicitação de Exclusão de Conta Recebida',
            'content' => 'Recebemos sua solicitação para excluir sua conta. O processo será concluído em :days dias. Durante esse período, você ainda pode cancelar a solicitação fazendo login novamente.',
        ],
        'account_deletion_processed' => [
            'title' => 'Conta Excluída com Sucesso',
            'content' => 'Conforme solicitado, sua conta e os dados associados foram excluídos permanentemente da nossa plataforma. Lamentamos vê-lo partir.',
        ],
        'breach_report_received' => [
            'title' => 'Relatório de Violação Recebido',
            'content' => 'Obrigado pelo seu relatório. Ele foi recebido com o ID #:report_id e nossa equipe de segurança está revisando.',
        ],
        'status' => [
            'pending_user_confirmation' => 'Pendente de confirmação do usuário',
            'user_confirmed_action' => 'Ação do usuário confirmada',
            'user_revoked_consent' => 'Ação do usuário retirada',
            'user_disavowed_suspicious' => 'Ação do usuário não reconhecida',
        ],
    ],

    'consent_management' => [
        'title' => 'Gestão de Consentimentos',
        'subtitle' => 'Controle como seus dados pessoais são usados',
        'description' => 'Aqui você pode gerenciar suas preferências de consentimento para diferentes propósitos e serviços.',
        'update_preferences' => 'Atualizar suas preferências de consentimento',
        'preferences_updated' => 'Suas preferências de consentimento foram atualizadas com sucesso.',
        'preferences_update_error' => 'Ocorreu um erro ao atualizar suas preferências de consentimento. Tente novamente.',
    ],

    // Rodapé
    'privacy_policy' => 'Política de Privacidade',
    'terms_of_service' => 'Termos de Serviço',
    'all_rights_reserved' => 'Todos os direitos reservados.',
    'navigation_label' => 'Navegação GDPR',
    'main_content_label' => 'Conteúdo principal GDPR',
];
