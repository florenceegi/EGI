<?php

/**
 * @Oracode Translation File: Organization Data Management - Portuguese
 * 🎯 Purpose: Complete Portuguese translations for business/organization data management
 * 🛡️ Privacy: Corporate data protection, business information security
 * 🌐 i18n: Multi-country business data support with Portuguese base
 * 🧱 Core Logic: Supports creator/enterprise/epp_entity organization management
 * ⏰ MVP: Critical for business users and EPP entity onboarding
 *
 * @package Lang\Pt
 * @author Padmin D. Curtis (AI Partner OS1-Compliant)
 * @version 1.0.0 (FlorenceEGI MVP - Business Ready)
 * @deadline 2025-06-30
 */

return [
    // TÍTULOS E CABEÇALHOS DA PÁGINA
    'management_title' => 'Dados da Organização',
    'management_subtitle' => 'Gerencie os dados da sua empresa ou organização',
    'company_title' => 'Informações da Empresa',
    'company_subtitle' => 'Detalhes legais e operacionais',
    'contacts_title' => 'Contatos Empresariais',
    'contacts_subtitle' => 'Contatos e referências',
    'certifications_title' => 'Certificações',
    'certifications_subtitle' => 'Certificações ambientais e de qualidade',

    // TIPOS DE ORGANIZAÇÃO
    'organization_types' => [
        'corporation' => 'Sociedade Empresária',
        'partnership' => 'Sociedade de Pessoas',
        'sole_proprietorship' => 'Empresário Individual',
        'cooperative' => 'Cooperativa',
        'non_profit' => 'Organização Sem Fins Lucrativos',
        'foundation' => 'Fundação',
        'association' => 'Associação',
        'government' => 'Entidade Pública',
        'educational' => 'Instituição de Ensino',
        'research' => 'Instituto de Pesquisa',
        'startup' => 'Startup Inovadora',
        'other' => 'Outro',
    ],

    'legal_forms' => [
        'srl' => 'LTDA - Sociedade Limitada',
        'spa' => 'S.A. - Sociedade Anônima',
        'srls' => 'LTDA Simples',
        'snc' => 'SNC - Sociedade em Nome Coletivo',
        'sas' => 'Sociedade em Comandita Simples',
        'ditta_individuale' => 'Empresário Individual',
        'cooperativa' => 'Cooperativa',
        'onlus' => 'Organização Sem Fins Lucrativos (OSFL)',
        'aps' => 'Associação de Promoção Social',
        'ets' => 'Entidade do Terceiro Setor',
        'fondazione' => 'Fundação',
        'ente_pubblico' => 'Entidade Pública',
    ],

    // SEÇÕES DO FORMULÁRIO
    'legal_information' => 'Informações Legais',
    'legal_information_desc' => 'Dados legais e de registro da organização',
    'operational_information' => 'Informações Operacionais',
    'operational_information_desc' => 'Dados de atividade e operações',
    'contact_information' => 'Informações de Contato',
    'contact_information_desc' => 'Contatos e referências da empresa',
    'sustainability_info' => 'Informações de Sustentabilidade',
    'sustainability_info_desc' => 'Certificações ambientais e práticas sustentáveis',
    'epp_information' => 'Informações EPP',
    'epp_information_desc' => 'Dados específicos para entidades EPP (Pontos de Proteção Ambiental)',

    // CAMPOS DO FORMULÁRIO - INFORMAÇÕES LEGAIS
    'legal_name' => 'Razão Social',
    'legal_name_placeholder' => 'Nome completo da organização conforme registro',
    'trade_name' => 'Nome Fantasia',
    'trade_name_placeholder' => 'Nome fantasia ou marca (se diferente)',
    'legal_form' => 'Natureza Jurídica',
    'legal_form_placeholder' => 'Selecione a natureza jurídica',
    'vat_number' => 'CNPJ / NIF',
    'vat_number_placeholder' => 'BR12345678000101',
    'tax_code' => 'Código Fiscal',
    'tax_code_placeholder' => 'Código fiscal da organização',
    'registration_number' => 'Número de Registro',
    'registration_number_placeholder' => 'Registro na Junta Comercial',
    'chamber_of_commerce' => 'Junta Comercial',
    'chamber_of_commerce_placeholder' => 'Junta Comercial de registro',
    'incorporation_date' => 'Data de Constituição',
    'incorporation_date_placeholder' => 'Data de abertura da empresa',
    'share_capital' => 'Capital Social',
    'share_capital_placeholder' => 'Capital social em reais',

    // CAMPOS DO FORMULÁRIO - OPERACIONAL
    'business_sector' => 'Setor de Atuação',
    'business_sectors' => [
        'technology' => 'Tecnologia & TI',
        'manufacturing' => 'Indústria',
        'services' => 'Serviços',
        'retail' => 'Varejo',
        'wholesale' => 'Atacado',
        'construction' => 'Construção',
        'agriculture' => 'Agricultura',
        'food_beverage' => 'Alimentação & Bebidas',
        'fashion' => 'Moda & Vestuário',
        'tourism' => 'Turismo & Hotelaria',
        'healthcare' => 'Saúde',
        'education' => 'Educação',
        'finance' => 'Finanças & Seguros',
        'transport' => 'Transporte & Logística',
        'energy' => 'Energia & Serviços',
        'creative' => 'Indústrias Criativas',
        'environmental' => 'Meio Ambiente & Sustentabilidade',
        'research' => 'Pesquisa & Desenvolvimento',
        'other' => 'Outro',
    ],
    'primary_activity' => 'Atividade Principal',
    'primary_activity_placeholder' => 'Descreva a atividade principal da organização',
    'employee_count' => 'Número de Funcionários',
    'employee_ranges' => [
        '1' => '1 funcionário',
        '2-9' => '2-9 funcionários',
        '10-49' => '10-49 funcionários',
        '50-249' => '50-249 funcionários',
        '250-999' => '250-999 funcionários',
        '1000+' => 'Mais de 1000 funcionários',
    ],
    'annual_revenue' => 'Faturamento Anual',
    'revenue_ranges' => [
        'under_100k' => 'Menos de R$100.000',
        '100k_500k' => 'R$100.000 - R$500.000',
        '500k_2m' => 'R$500.000 - R$2.000.000',
        '2m_10m' => 'R$2.000.000 - R$10.000.000',
        '10m_50m' => 'R$10.000.000 - R$50.000.000',
        'over_50m' => 'Mais de R$50.000.000',
    ],

    // CAMPOS DO FORMULÁRIO - CONTATO
    'headquarters_address' => 'Sede Social',
    'headquarters_street' => 'Endereço da Sede',
    'headquarters_street_placeholder' => 'Rua, número',
    'headquarters_city' => 'Cidade',
    'headquarters_postal_code' => 'CEP',
    'headquarters_province' => 'Estado',
    'headquarters_country' => 'País',
    'operational_address' => 'Sede Operacional',
    'same_as_headquarters' => 'Igual à sede social',
    'operational_street' => 'Endereço Operacional',
    'phone_main' => 'Telefone Principal',
    'phone_main_placeholder' => '+55 11 1234-5678',
    'phone_secondary' => 'Telefone Secundário',
    'fax' => 'Fax',
    'email_general' => 'E-mail Geral',
    'email_general_placeholder' => 'info@empresa.com.br',
    'email_admin' => 'E-mail Administrativo',
    'email_admin_placeholder' => 'admin@empresa.com.br',
    'pec' => 'PEC (E-mail Certificado)',
    'pec_placeholder' => 'empresa@pec.com.br',
    'website' => 'Website',
    'website_placeholder' => 'https://www.empresa.com.br',

    // CAMPOS - SUSTENTABILIDADE & EPP
    'sustainability_commitment' => 'Compromisso com Sustentabilidade',
    'sustainability_commitment_desc' => 'Descreva o compromisso ambiental da organização',
    'environmental_certifications' => 'Certificações Ambientais',
    'certifications' => [
        'iso_14001' => 'ISO 14001 - Sistema de Gestão Ambiental',
        'emas' => 'EMAS - Sistema Europeu de Gestão e Auditoria Ambiental',
        'carbon_neutral' => 'Certificação Carbono Neutro',
        'leed' => 'LEED - Liderança em Energia e Design Ambiental',
        'ecolabel' => 'Selo Ecológico Europeu',
        'fsc' => 'FSC - Conselho de Manejo Florestal',
        'cradle_to_cradle' => 'Cradle to Cradle Certified',
        'b_corp' => 'Certificação B-Corp',
        'organic' => 'Certificação Orgânica',
        'fair_trade' => 'Certificação Fair Trade',
        'other' => 'Outras Certificações',
    ],
    'epp_entity_type' => 'Tipo de Entidade EPP',
    'epp_entity_types' => [
        'environmental_ngo' => 'ONG Ambiental',
        'research_institute' => 'Instituto de Pesquisa',
        'green_tech_company' => 'Empresa Green Tech',
        'renewable_energy' => 'Energia Renovável',
        'waste_management' => 'Gestão de Resíduos',
        'conservation_org' => 'Organização de Conservação',
        'sustainable_agriculture' => 'Agricultura Sustentável',
        'environmental_consulting' => 'Consultoria Ambiental',
        'carbon_offset' => 'Compensação de Carbono',
        'biodiversity_protection' => 'Proteção da Biodiversidade',
    ],
    'epp_certification_level' => 'Nível de Certificação EPP',
    'epp_levels' => [
        'bronze' => 'Bronze - Compromisso básico',
        'silver' => 'Prata - Compromisso médio',
        'gold' => 'Ouro - Compromisso avançado',
        'platinum' => 'Platina - Compromisso excelente',
    ],
    'sustainability_projects' => 'Projetos de Sustentabilidade',
    'sustainability_projects_placeholder' => 'Descreva os principais projetos ambientais',

    // AÇÕES E BOTÕES
    'save_organization' => 'Salvar Dados da Organização',
    'verify_legal_data' => 'Verificar Dados Legais',
    'upload_certificate' => 'Enviar Certificado',
    'request_epp_verification' => 'Solicitar Verificação EPP',
    'export_organization_data' => 'Exportar Dados da Organização',
    'validate_vat' => 'Validar CNPJ / NIF',
    'check_chamber_registration' => 'Checar Registro na Junta Comercial',

    // MENSAGENS DE SUCESSO E ERRO
    'update_success' => 'Dados da organização atualizados com sucesso',
    'verification_reset_warning' => 'Dados críticos foram modificados, a verificação foi reiniciada.',
    'data_not_found' => 'Dados da organização não encontrados. Por favor, entre em contato com o suporte.',
    'role_not_allowed' => 'Você não tem permissão para acessar os dados da organização.',
    'organization_saved' => 'Dados da organização salvos com sucesso',
    'organization_error' => 'Erro ao salvar os dados da organização',
    'legal_verification_success' => 'Dados legais verificados com sucesso',
    'legal_verification_error' => 'Erro na verificação dos dados legais',
    'vat_verified' => 'CNPJ / NIF verificado com sucesso',
    'chamber_verified' => 'Registro na junta comercial verificado',
    'epp_verification_requested' => 'Solicitação de verificação EPP enviada com sucesso',
    'certificate_uploaded' => 'Certificado enviado com sucesso',

    // MENSAGENS DE VALIDAÇÃO
    'validation' => [
        'legal_name_required' => 'A razão social é obrigatória',
        'legal_form_required' => 'A natureza jurídica é obrigatória',
        'vat_number_invalid' => 'O CNPJ / NIF não é válido',
        'tax_code_invalid' => 'O código fiscal não é válido',
        'incorporation_date_valid' => 'A data de constituição deve ser válida',
        'share_capital_numeric' => 'O capital social deve ser numérico',
        'employee_count_required' => 'O número de funcionários é obrigatório',
        'business_sector_required' => 'O setor de atuação é obrigatório',
        'headquarters_address_required' => 'O endereço da sede social é obrigatório',
        'phone_main_required' => 'O telefone principal é obrigatório',
        'email_general_required' => 'O e-mail geral é obrigatório',
        'email_valid' => 'O e-mail deve ser válido',
        'website_url' => 'O site deve ser uma URL válida',
        'pec_email' => 'O PEC deve ser um e-mail válido',
    ],

    // AJUDA E DESCRIÇÕES
    'help' => [
        'legal_name' => 'Nome da organização conforme registro legal',
        'trade_name' => 'Nome fantasia ou marca utilizada nas operações',
        'vat_number' => 'CNPJ / NIF para transações e faturamento',
        'rea_number' => 'Registro na Junta Comercial',
        'share_capital' => 'Capital social integralizado',
        'epp_entity' => 'Entidades EPP podem atribuir pontos ambientais na plataforma',
        'sustainability_projects' => 'Projetos que demonstrem o compromisso ambiental',
        'certifications' => 'Certificações que atestam práticas sustentáveis',
    ],

    // PRIVACIDADE E CONFORMIDADE
    'privacy' => [
        'data_usage' => 'Os dados da organização são utilizados para:',
        'usage_verification' => 'Verificação de identidade empresarial',
        'usage_compliance' => 'Conformidade fiscal e legal',
        'usage_epp' => 'Gestão EPP e atribuição de pontos',
        'usage_marketplace' => 'Operações no marketplace FlorenceEGI',
        'data_sharing' => 'Os dados podem ser compartilhados com:',
        'sharing_authorities' => 'Órgãos fiscais e de controle',
        'sharing_partners' => 'Parceiros tecnológicos autorizados',
        'sharing_verification' => 'Organismos de certificação',
        'retention_period' => 'Dados retidos por 10 anos após o término da relação',
        'gdpr_rights' => 'A organização tem direito de acessar, corrigir ou excluir os dados',
    ],
];
