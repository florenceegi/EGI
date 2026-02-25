<?php

return [
    // Nombre de categoría para badge
    'category_name' => 'Lingote de Oro',

    // Etiquetas del componente
    'title' => 'Información del Lingote de Oro',
    'subtitle' => 'Valor Indicativo Basado en el Precio Actual del Oro',

    // Propiedades del oro
    'weight' => 'Peso',
    'weight_unit' => 'Unidad',
    'purity' => 'Pureza',
    'pure_gold' => 'Contenido de Oro Puro',

    // Etiquetas de valor
    'gold_price' => 'Precio Spot del Oro',
    'creator_margin' => 'Margen del Creador',
    'per_gram' => 'por gramo',
    'per_oz' => 'por onza troy',
    'base_value' => 'Valor Base del Oro',
    'margin' => 'Margen del Creador',
    'indicative_value' => 'Valor Indicativo',

    // Descargos de responsabilidad
    'disclaimer' => 'Este es un valor indicativo basado en el precio spot actual del oro. El precio de venta real es determinado por el Creador.',
    'price_updated_at' => 'Precio actualizado a las',
    'price_source' => 'Fuente',

    // Descripciones de pureza
    'purity_999' => '24k - 99.9% Puro',
    'purity_995' => '99.5% Puro',
    'purity_990' => '99.0% Puro',
    'purity_916' => '22k - 91.6% Puro',
    'purity_750' => '18k - 75.0% Puro',

    // Unidades de peso
    'unit_grams' => 'Gramos',
    'unit_ounces' => 'Onzas',
    'unit_troy_ounces' => 'Onzas Troy',

    // Mensajes de estado
    'loading' => 'Cargando precio del oro...',
    'error' => 'No se pudo obtener el precio del oro. Por favor, inténtelo más tarde.',
    'not_gold_bar' => 'Este EGI no es un Lingote de Oro.',

    // Función de actualización
    'refresh_button' => 'Actualizar Precio',
    'refresh_cost' => 'Costo: :cost Egili',
    'refresh_available_now' => 'Disponible ahora',
    'next_refresh' => 'Próxima actualización automática en :time',
    'refresh_success' => '¡Precio del oro actualizado correctamente!',
    'insufficient_egili' => 'Egili insuficientes. Necesitas :required pero tienes :current.',
    'refresh_confirm_title' => '¿Actualizar precio del oro?',
    'refresh_confirm_message' => 'Esto costará :cost Egili de tu saldo. El precio se actualizará con datos en tiempo real.',
    'refresh_confirm_button' => 'Actualizar por :cost Egili',
    'refresh_cancel' => 'Cancelar',

    // Sección de margen CRUD
    'margin' => [
        'title' => 'Margen del Lingote de Oro',
        'description' => 'Establece tu margen sobre el valor del lingote de oro. Puedes usar un porcentaje, una cantidad fija o ambos.',
        'percent_label' => 'Margen Porcentual',
        'percent_hint' => 'Porcentaje a añadir al valor del oro (ej. 5%)',
        'fixed_label' => 'Margen Fijo',
        'fixed_hint' => 'Cantidad fija en EUR a añadir al valor del oro',
        'current_value' => 'Valor indicativo actual',
        'value_note' => 'Calculado a partir del precio spot del oro más márgenes',
    ],
    'buy_egili_hint'   => 'Compra un Paquete IA para recargar tus Egili.',
    'buy_egili_button' => 'Comprar Paquete IA',
];
