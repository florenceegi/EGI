<?php

/**
 * Translation file for FlorenceEGI Registration Page - Portuguese
 *
 * @oracode OS1 Compliant
 * @semantic_coherence All translations align with FlorenceEGI brand and values
 * @intentionality Each message guides user toward successful registration
 */

return [
    // Form Fields
    'label_name' => 'Nome completo',
    'name_help' => 'Seu nome real para uso interno e identificação',

    'label_nick_name' => 'Apelido (público)',
    'nick_name_help' => 'Se inserir um apelido, será exibido publicamente no lugar do seu nome. Se deixar vazio, seu nome completo será exibido.',

    'label_email' => 'Endereço de email',
    'email_help' => 'Usaremos este endereço para comunicações importantes',

    'label_password' => 'Senha',
    'password_help' => 'Mínimo 8 caracteres, inclua maiúsculas, minúsculas e números',

    'label_password_confirmation' => 'Confirmar senha',
    'password_confirmation_help' => 'Repita a senha para confirmá-la',

    // Wallet Welcome Modal Messages
    'wallet_iban_added_success' => 'IBAN adicionado com sucesso à sua carteira.',
    'wallet_iban_add_failed' => 'Erro ao adicionar IBAN. Por favor, tente novamente.',
    'wallet_iban_duplicate' => 'Este IBAN já está vinculado a outra conta FlorenceEGI.',
    'wallet_welcome_completed' => 'Configuração da carteira concluída.',
    'wallet_not_found' => 'Carteira não encontrada para este usuário.',
    'invalid_iban' => 'IBAN inválido. Verifique o formato.',
    'unauthenticated' => 'Você deve estar autenticado para esta operação.',
];
