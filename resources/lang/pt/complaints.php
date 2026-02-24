<?php

return [

    // Page
    'title' => 'Denúncias e Reclamações DSA',
    'subtitle' => 'Denuncie conteúdos ilícitos ou apresente uma reclamação sob a Lei dos Serviços Digitais (Reg. UE 2022/2065)',
    'dsa_info_title' => 'Seus direitos sob a DSA',
    'dsa_info_text' => 'De acordo com o Regulamento (UE) 2022/2065 (Lei dos Serviços Digitais), você tem o direito de denunciar conteúdos que considere ilícitos (Art. 16) e de apresentar reclamações contra as decisões de moderação da plataforma (Art. 20). Cada denúncia é examinada por pessoal qualificado dentro de prazos razoáveis.',
    'legal_contact' => 'Para denúncias urgentes, você também pode escrever para',

    // Types
    'types' => [
        'content_report' => 'Denúncia de conteúdo ilícito',
        'ip_violation' => 'Violação de propriedade intelectual',
        'fraud' => 'Fraude ou atividade fraudulenta',
        'moderation_appeal' => 'Reclamação contra decisão de moderação',
        'general' => 'Denúncia genérica',
    ],

    // Type descriptions (for form helper text)
    'type_descriptions' => [
        'content_report' => 'Conteúdos ilegais, ofensivos ou que violam nossos termos de uso',
        'ip_violation' => 'Obras falsificadas, plágio, violação de direitos autorais ou marcas registradas',
        'fraud' => 'Fraudes, fraudes em pagamentos ou comportamentos enganosos',
        'moderation_appeal' => 'Conteste uma decisão tomada pela plataforma sobre seu conteúdo',
        'general' => 'Qualquer outra denúncia que não se enquadre nas categorias anteriores',
    ],

    // Content types
    'content_types' => [
        'egi' => 'Obra (EGI)',
        'collection' => 'Coleção',
        'user_profile' => 'Perfil de usuário',
        'comment' => 'Comentário',
    ],

    // Statuses
    'statuses' => [
        'received' => 'Recebida',
        'under_review' => 'Em revisão',
        'action_taken' => 'Medida tomada',
        'dismissed' => 'Arquivada',
        'appealed' => 'Reclamação apresentada',
        'resolved' => 'Resolvida',
    ],

    // Form labels
    'form_title' => 'Nova denúncia',
    'select_type' => 'Selecione o tipo de denúncia',
    'complaint_type' => 'Tipo de denúncia',
    'reported_content_type' => 'Tipo de conteúdo denunciado',
    'select_content_type' => 'Selecione o tipo de conteúdo',
    'reported_content_id' => 'ID do conteúdo',
    'reported_content_id_help' => 'Digite o ID do conteúdo que deseja denunciar (visível na página do conteúdo)',
    'description' => 'Descrição detalhada',
    'description_placeholder' => 'Descreva em detalhes o motivo da denúncia, incluindo todos os elementos úteis para a avaliação. Mínimo de 20 caracteres.',
    'description_chars' => ':count / :max caracteres',
    'evidence_urls' => 'URLs de prova (opcional)',
    'evidence_urls_help' => 'Digite links para capturas de tela, páginas da web ou outros elementos para apoiar sua denúncia. Máximo de 5 URLs.',
    'add_evidence_url' => 'Adicionar URL',
    'remove_evidence_url' => 'Remover',
    'evidence_url_placeholder' => 'https://...',
    'consent_label' => 'Consentimento para processamento',
    'consent_text' => 'Consinto com o processamento de dados pessoais necessários para gerenciar esta denúncia, de acordo com o Reg. UE 2016/679 (RGPD) e Reg. UE 2022/2065 (DSA). Declaro que as informações fornecidas são verdadeiras e de boa fé.',

    // Actions
    'submit' => 'Enviar denúncia',
    'submitting' => 'Enviando...',
    'cancel' => 'Cancelar',
    'back_to_list' => 'Voltar às denúncias',
    'view_details' => 'Detalhes',

    // Messages
    'submitted_successfully' => 'Sua denúncia foi enviada com sucesso. Número de referência: :reference. Você receberá uma confirmação por email.',
    'no_complaints' => 'Você ainda não apresentou denúncias ou reclamações.',

    // Table headers
    'date' => 'Data',
    'reference' => 'Referência',
    'type' => 'Tipo',
    'status' => 'Estado',
    'actions' => 'Ações',

    // Previous complaints section
    'your_complaints' => 'Suas denúncias',
    'your_complaints_description' => 'Histórico das denúncias e reclamações que você apresentou',

    // Validation
    'validation' => [
        'type_required' => 'Selecione o tipo de denúncia.',
        'type_invalid' => 'O tipo de denúncia selecionado não é válido.',
        'description_required' => 'A descrição é obrigatória.',
        'description_min' => 'A descrição deve ter pelo menos 20 caracteres.',
        'description_max' => 'A descrição não pode exceder 5000 caracteres.',
        'content_id_required' => 'O ID do conteúdo é obrigatório ao selecionar um tipo de conteúdo.',
        'evidence_urls_max' => 'Você pode inserir no máximo 5 URLs de prova.',
        'evidence_url_format' => 'Cada URL de prova deve ser um endereço web válido.',
        'consent_required' => 'Você deve consentir com o processamento de dados para continuar.',
    ],

    // Detail page
    'detail_title' => 'Detalhes da denúncia',
    'submitted_on' => 'Enviada em',
    'current_status' => 'Estado atual',
    'complaint_type_label' => 'Tipo',
    'reported_content' => 'Conteúdo denunciado',
    'description_label' => 'Descrição',
    'evidence_label' => 'Provas anexadas',
    'decision' => 'Decisão',
    'decision_date' => 'Data da decisão',
    'decided_by_label' => 'Decidido por',
    'no_decision_yet' => 'Aguardando revisão pela equipe.',
    'appeal_section' => 'Reclamação / Apelo',
    'no_appeal' => 'Nenhuma reclamação apresentada.',
    'content_id_label' => 'ID do conteúdo',
    'content_type_label' => 'Tipo de conteúdo',
    'reported_user_label' => 'Usuário denunciado',

    // Timeline
    'timeline' => [
        'received' => 'Denúncia recebida',
        'under_review' => 'Sendo processada',
        'action_taken' => 'Medida tomada',
        'dismissed' => 'Denúncia arquivada',
        'appealed' => 'Reclamação apresentada',
        'resolved' => 'Caso resolvido',
    ],

    // Notification email
    'notification' => [
        'subject' => 'Confirmação de recebimento de denúncia DSA - :reference',
        'greeting' => 'Prezado(a) :name,',
        'body' => 'Sua denúncia foi recebida e registrada com o número de referência **:reference**.',
        'body_2' => 'Analisaremos sua denúncia e entraremos em contato dentro dos prazos previstos pela Lei dos Serviços Digitais (Reg. UE 2022/2065).',
        'reference_label' => 'Número de referência',
        'type_label' => 'Tipo de denúncia',
        'date_label' => 'Data de envio',
        'closing' => 'O time FlorenceEGI',
    ],

];
