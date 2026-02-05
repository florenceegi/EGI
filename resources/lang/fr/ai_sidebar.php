<?php

/**
 * AI Sidebar - Traductions Françaises
 * P0-2: Translation keys only
 * P0-9: All 6 languages required
 */

return [
    // General
    'title' => 'Assistant IA',
    'subtitle' => 'Je t\'aide à compléter ton profil',
    'toggle_title' => 'Ouvrir l\'assistant d\'intégration',
    'close' => 'Fermer',
    'send' => 'Envoyer',
    'chat_placeholder' => 'Pose une question...',
    'assistant_name' => 'Natan',
    'quick_actions_label' => 'Prochaines étapes suggérées :',

    // Messages (programmatic AI)
    'messages' => [
        'welcome' => 'Bienvenue ! Je t\'aide à configurer ton profil.',
        'progress_low' => 'Bon début ! Tu as complété :completed sur :total étapes. Suivant : :nextStep',
        'progress_high' => 'Presque fini ! Plus que :remaining étapes.',
        'complete' => 'Félicitations ! Ton profil est entièrement configuré. 🎉',
        'all_done' => 'Toutes les étapes terminées !',
    ],

    // Checklist
    'checklist' => [
        'progress' => 'Progression de la configuration',
        'title' => 'Liste d\'intégration',
    ],

    // Discourse messages (AI-like text)
    'discourse' => [
        'greeting' => 'Salut',
        'greeting_suffix' => ' ! Je suis là pour t\'aider à compléter ton profil.',
        'progress_intro' => 'Tu as complété',
        'progress_of' => 'sur',
        'progress_suffix' => ' étapes. Voyons ce qui manque pour rendre ton profil parfait.',
        'missing_title' => 'Voici ce qu\'il te reste à faire :',
        'suggestion_intro' => 'Je te conseille de commencer par',
        'click_hint' => 'Clique sur un élément de la liste ci-dessous pour le compléter.',
        'complete_title' => 'Fantastique !',
        'complete_text' => 'Ton profil est complet et prêt à être découvert. Tu peux maintenant te concentrer sur la création de tes œuvres et la croissance de ta communauté.',
    ],

    // Checklist Items - Creator
    'steps' => [
        'avatar' => [
            'title' => 'Télécharge ton avatar',
            'description' => 'Ajoute une photo de profil pour être reconnu',
        ],
        'banner' => [
            'title' => 'Personnalise ta bannière',
            'description' => 'Ajoute une image de couverture à ton profil',
        ],
        'bio' => [
            'title' => 'Écris ta bio',
            'description' => 'Dis-nous qui tu es et ce que tu crées',
        ],
        'stripe' => [
            'title' => 'Configure les paiements',
            'description' => 'Connecte Stripe pour recevoir des paiements',
        ],
        'collection' => [
            'title' => 'Crée ta première collection',
            'description' => 'Organise tes œuvres dans une collection',
        ],
        'first_egi' => [
            'title' => 'Crée ton premier EGI',
            'description' => 'Publie ta première œuvre numérique',
        ],
        'social_links' => [
            'title' => 'Ajoute des liens sociaux',
            'description' => 'Connecte tes profils sociaux',
        ],
        'verify_email' => [
            'title' => 'Vérifie ton email',
            'description' => 'Confirme ton adresse e-mail',
        ],
    ],

    // Errors
    'errors' => [
        'request_failed' => 'Requête échouée. Veuillez réessayer.',
        'connection_error' => 'Erreur de connexion. Veuillez vérifier votre connexion internet.',
        'invalid_user_type' => 'Type d\'utilisateur non valide.',
        'unauthorized' => 'Non autorisé à accéder à cette ressource.',
    ],
];
