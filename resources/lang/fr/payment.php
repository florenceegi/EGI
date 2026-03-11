<?php

return [


    // Étiquettes UI génériques
    'label_default'                   => 'Par défaut',
    'label_toggle'                    => 'Activer/Désactiver',
    'label_make_default'              => 'Définir par défaut',
    // Virement bancaire
    'bank_account_holder'             => 'Titulaire du compte',
    'bank_holder_placeholder'         => 'Nom complet tel qu\'indiqué sur le compte bancaire',
    'bank_config_title'               => 'Configuration du Compte Bancaire',
    'bank_save_details'               => 'Enregistrer les coordonnées bancaires',
    // Stripe
    'stripe_connected'                => 'Compte Stripe connecté',
    'stripe_connected_creator'        => 'Compte Stripe connecté (niveau Creator)',
    'stripe_collection_inherits'      => 'La collection hérite de votre compte Stripe Connect principal.',
    'stripe_connect_first'            => 'Veuillez d\'abord connecter votre compte Stripe dans les paramètres principaux.',
    'stripe_connect_cta'             => 'Connecter le compte Stripe',
    // Paramètres de collection
    'collection_settings_title'       => 'Paramètres de Paiement de la Collection',
    'collection_settings_description' => 'Personnalisez les méthodes de paiement pour cette collection spécifique',

    'wizard' => [
        'chip_label'  => 'Activer les paiements',
        'intro_title' => 'Activez votre système de paiement',
        'intro_text'  => 'Pour commencer à vendre vos œuvres, vous devez activer :psp_name. C\'est un processus guidé qui ne prend que quelques minutes.',
        'intro_note'  => 'Les paiements arrivent directement sur votre compte. FlorenceEGI ne retient pas votre argent.',
        'cta'         => 'Activer :psp_name',
        'processing'  => 'Démarrage en cours…',
        'link_failed' => 'Impossible de générer le lien. Veuillez réessayer.',
        'no_wallet'   => 'Aucun portefeuille configuré. Contactez le support.',
        'success'     => 'Paiements activés ! Vous pouvez maintenant vendre vos œuvres.',
        'refresh'     => 'Le lien a expiré. Cliquez à nouveau sur « Activer les paiements ».',
        // Wizard 4-step popup
        'back'                => 'Retour',
        'step1_next'          => 'De quoi ai-je besoin ?',
        'step2_title'         => 'Ce dont vous avez besoin pour activer les paiements :',
        'step2_item1'         => 'Pièce d\'identité valide',
        'step2_item2'         => 'IBAN ou carte bancaire (pour les virements)',
        'step2_item3'         => 'Environ 5 minutes de votre temps',
        'step2_next'          => 'Continuer',
        'step3_note'          => 'Une petite fenêtre sécurisée s\'ouvrira. Complétez la vérification et revenez ici — cette page restera ouverte.',
        'step3_cta'           => 'Ouvrir la vérification :psp_name',
        'popup_blocked'       => 'Votre navigateur a bloqué la fenêtre contextuelle. Autorisez les popups pour FlorenceEGI et réessayez.',
        'step4_checking'      => 'Vérification du statut…',
        'step4_complete'      => 'Paiements activés !',
        'step4_complete_hint' => 'Vous pouvez maintenant vendre vos œuvres. Le modal se rafraîchira bientôt.',
        'step4_pending'       => 'Vérification en attente',
        'step4_pending_hint'  => 'Vos documents sont en cours de traitement. Vous recevrez une notification dès que ce sera prêt.',
        'step4_error'         => 'Une erreur est survenue',
        'step4_retry'         => 'Réessayer',
    ],

];
