<?php

return [


    // Etiquetas UI genéricas
    'label_default'                   => 'Padrão',
    'label_toggle'                    => 'Ativar/Desativar',
    'label_make_default'              => 'Definir como padrão',
    // Transferência bancária
    'bank_account_holder'             => 'Titular da conta',
    'bank_holder_placeholder'         => 'Nome completo conforme indicado na conta bancária',
    'bank_config_title'               => 'Configuração da Conta Bancária',
    'bank_save_details'               => 'Guardar dados bancários',
    // Stripe
    'stripe_connected'                => 'Conta Stripe ligada',
    'stripe_connected_creator'        => 'Conta Stripe ligada (nível Creator)',
    'stripe_collection_inherits'      => 'A coleção herda a sua conta Stripe Connect principal.',
    'stripe_connect_first'            => 'Por favor, ligue primeiro a sua conta Stripe nas definições principais.',
    'stripe_connect_cta'             => 'Ligar conta Stripe',
    // Definições de coleção
    'collection_settings_title'       => 'Definições de Pagamento da Coleção',
    'collection_settings_description' => 'Personalize os métodos de pagamento para esta coleção específica',


    // Stripe popup return page
    'popup_return_title'   => 'Verificação concluída',
    'popup_return_heading' => 'Verificação concluída',
    'popup_return_closing' => 'Esta janela fechará automaticamente',

    'wizard' => [
        'chip_label'  => 'Ativar pagamentos',
        'intro_title' => 'Ative o sistema de pagamentos',
        'intro_text'  => 'Para começar a vender as suas obras, precisa de ativar o :psp_name. É um processo guiado que leva apenas alguns minutos.',
        'intro_note'  => 'Os pagamentos vão diretamente para a sua conta. A FlorenceEGI não retém o seu dinheiro.',
        'cta'         => 'Ativar :psp_name',
        'processing'  => 'A iniciar…',
        'link_failed' => 'Não foi possível gerar o link. Por favor, tente novamente.',
        'no_wallet'   => 'Nenhuma carteira configurada. Contacte o suporte.',
        'success'     => 'Pagamentos ativados! Já pode vender as suas obras.',
        'refresh'     => 'O link expirou. Clique em "Ativar pagamentos" novamente.',
        // Wizard 4-step popup
        'back'                => 'Voltar',
        'step1_next'          => 'Do que preciso?',
        'step2_title'         => 'O que precisa para ativar os pagamentos:',
        'step2_item1'         => 'Documento de identidade válido',
        'step2_item2'         => 'IBAN ou cartão bancário (para pagamentos)',
        'step2_item3'         => 'Cerca de 5 minutos do seu tempo',
        'step2_next'          => 'Continuar',
        'step3_note'          => 'Abrirá uma pequena janela segura. Conclua a verificação e volte aqui — esta página ficará aberta.',
        'step3_cta'           => 'Abrir verificação :psp_name',
        'popup_blocked'       => 'O seu browser bloqueou a janela. Permita popups para FlorenceEGI e tente novamente.',
        'step4_checking'      => 'A verificar estado…',
        'step4_complete'      => 'Pagamentos ativados!',
        'step4_complete_hint' => 'Já pode vender as suas obras. O modal será atualizado em breve.',
        'step4_pending'       => 'Verificação pendente',
        'step4_pending_hint'  => 'Estamos a processar os seus documentos. Receberá uma notificação quando estiver pronto.',
        'step4_error'         => 'Algo correu mal',
        'step4_retry'         => 'Tentar novamente',
    ],

];
