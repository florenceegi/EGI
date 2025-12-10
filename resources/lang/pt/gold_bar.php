<?php

return [
    // Nome da categoria para badge
    'category_name' => 'Barra de Ouro',

    // Rótulos do componente
    'title' => 'Informações da Barra de Ouro',
    'subtitle' => 'Valor Indicativo Baseado no Preço Atual do Ouro',

    // Propriedades do ouro
    'weight' => 'Peso',
    'weight_unit' => 'Unidade',
    'purity' => 'Pureza',
    'pure_gold' => 'Conteúdo de Ouro Puro',

    // Rótulos de valor
    'gold_price' => 'Preço Spot do Ouro',
    'creator_margin' => 'Margem do Criador',
    'per_gram' => 'por grama',
    'per_oz' => 'por onça troy',
    'base_value' => 'Valor Base do Ouro',
    'margin' => 'Margem do Criador',
    'indicative_value' => 'Valor Indicativo',

    // Avisos legais
    'disclaimer' => 'Este é um valor indicativo baseado no preço spot atual do ouro. O preço de venda real é determinado pelo Criador.',
    'price_updated_at' => 'Preço atualizado em',
    'price_source' => 'Fonte',

    // Descrições de pureza
    'purity_999' => '24k - 99.9% Puro',
    'purity_995' => '99.5% Puro',
    'purity_990' => '99.0% Puro',
    'purity_916' => '22k - 91.6% Puro',
    'purity_750' => '18k - 75.0% Puro',

    // Unidades de peso
    'unit_grams' => 'Gramas',
    'unit_ounces' => 'Onças',
    'unit_troy_ounces' => 'Onças Troy',

    // Mensagens de estado
    'loading' => 'Carregando preço do ouro...',
    'error' => 'Não foi possível obter o preço do ouro. Por favor, tente novamente mais tarde.',
    'not_gold_bar' => 'Este EGI não é uma Barra de Ouro.',

    // Função de atualização
    'refresh_button' => 'Atualizar Preço',
    'refresh_cost' => 'Custo: :cost Egili',
    'refresh_available_now' => 'Disponível agora',
    'next_refresh' => 'Próxima atualização automática em :time',
    'refresh_success' => 'Preço do ouro atualizado com sucesso!',
    'insufficient_egili' => 'Egili insuficientes. Você precisa de :required mas tem :current.',
    'refresh_confirm_title' => 'Atualizar preço do ouro?',
    'refresh_confirm_message' => 'Isso custará :cost Egili do seu saldo. O preço será atualizado com dados em tempo real.',
    'refresh_confirm_button' => 'Atualizar por :cost Egili',
    'refresh_cancel' => 'Cancelar',

    // Seção de margem CRUD
    'margin' => [
        'title' => 'Margem da Barra de Ouro',
        'description' => 'Defina sua margem sobre o valor da barra de ouro. Você pode usar uma porcentagem, um valor fixo ou ambos.',
        'percent_label' => 'Margem Percentual',
        'percent_hint' => 'Porcentagem a adicionar ao valor do ouro (ex. 5%)',
        'fixed_label' => 'Margem Fixa',
        'fixed_hint' => 'Valor fixo em EUR a adicionar ao valor do ouro',
        'current_value' => 'Valor indicativo atual',
        'value_note' => 'Calculado a partir do preço spot do ouro mais margens',
    ],
];
