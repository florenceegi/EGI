<?php

return [

    // Page
    'title' => 'Signalements et réclamations DSA',
    'subtitle' => 'Signalez des contenus illicites ou présentez une réclamation en vertu de la loi sur les services numériques (Rég. UE 2022/2065)',
    'dsa_info_title' => 'Vos droits selon la DSA',
    'dsa_info_text' => 'En vertu du Règlement (UE) 2022/2065 (Loi sur les services numériques), vous avez le droit de signaler les contenus que vous considérez comme illicites (Art. 16) et de présenter une réclamation contre les décisions de modération de la plateforme (Art. 20). Chaque signalement est examiné par du personnel qualifié dans les délais raisonnables.',
    'legal_contact' => 'Pour les signalements urgents, vous pouvez également écrire à',

    // Types
    'types' => [
        'content_report' => 'Signalement de contenu illicite',
        'ip_violation' => 'Violation de propriété intellectuelle',
        'fraud' => 'Fraude ou activité frauduleuse',
        'moderation_appeal' => 'Réclamation contre une décision de modération',
        'general' => 'Signalement général',
    ],

    // Type descriptions (for form helper text)
    'type_descriptions' => [
        'content_report' => 'Contenus illégaux, offensants ou violant nos conditions d\'utilisation',
        'ip_violation' => 'Œuvres contrefaites, plagiat, violation de droits d\'auteur ou de marques déposées',
        'fraud' => 'Arnaques, fraudes aux paiements ou comportements trompeurs',
        'moderation_appeal' => 'Contester une décision prise par la plateforme concernant vos contenus',
        'general' => 'Tout autre signalement ne relevant pas des catégories précédentes',
    ],

    // Content types
    'content_types' => [
        'egi' => 'Œuvre (EGI)',
        'collection' => 'Collection',
        'user_profile' => 'Profil utilisateur',
        'comment' => 'Commentaire',
    ],

    // Statuses
    'statuses' => [
        'received' => 'Reçu',
        'under_review' => 'En examen',
        'action_taken' => 'Mesure prise',
        'dismissed' => 'Archivé',
        'appealed' => 'Réclamation présentée',
        'resolved' => 'Résolu',
    ],

    // Form labels
    'form_title' => 'Nouveau signalement',
    'select_type' => 'Sélectionnez le type de signalement',
    'complaint_type' => 'Type de signalement',
    'reported_content_type' => 'Type de contenu signalé',
    'select_content_type' => 'Sélectionnez le type de contenu',
    'reported_content_id' => 'ID du contenu',
    'reported_content_id_help' => 'Entrez l\'ID du contenu que vous souhaitez signaler (visible sur la page du contenu)',
    'description' => 'Description détaillée',
    'description_placeholder' => 'Décrivez en détail la raison du signalement, en incluant tous les éléments utiles à l\'évaluation. Minimum 20 caractères.',
    'description_chars' => ':count / :max caractères',
    'evidence_urls' => 'URL de preuve (optionnel)',
    'evidence_urls_help' => 'Entrez des liens vers des captures d\'écran, des pages web ou d\'autres éléments à l\'appui du signalement. Maximum 5 URL.',
    'add_evidence_url' => 'Ajouter une URL',
    'remove_evidence_url' => 'Supprimer',
    'evidence_url_placeholder' => 'https://...',
    'consent_label' => 'Consentement au traitement',
    'consent_text' => 'J\'accepte le traitement de mes données personnelles nécessaires à la gestion de ce signalement, en vertu du Rég. UE 2016/679 (RGPD) et du Rég. UE 2022/2065 (DSA). Je déclare que les informations fournies sont véridiques et de bonne foi.',

    // Actions
    'submit' => 'Envoyer le signalement',
    'submitting' => 'Envoi en cours...',
    'cancel' => 'Annuler',
    'back_to_list' => 'Retour aux signalements',
    'view_details' => 'Détails',

    // Messages
    'submitted_successfully' => 'Votre signalement a été envoyé avec succès. Numéro de référence : :reference. Vous recevrez une confirmation par courrier électronique.',
    'no_complaints' => 'Vous n\'avez pas encore soumis de signalements ou de réclamations.',

    // Table headers
    'date' => 'Date',
    'reference' => 'Référence',
    'type' => 'Type',
    'status' => 'État',
    'actions' => 'Actions',

    // Previous complaints section
    'your_complaints' => 'Vos signalements',
    'your_complaints_description' => 'Historique des signalements et réclamations que vous avez soumis',

    // Validation
    'validation' => [
        'type_required' => 'Sélectionnez le type de signalement.',
        'type_invalid' => 'Le type de signalement sélectionné n\'est pas valide.',
        'description_required' => 'La description est obligatoire.',
        'description_min' => 'La description doit contenir au moins 20 caractères.',
        'description_max' => 'La description ne peut pas dépasser 5000 caractères.',
        'content_id_required' => 'L\'ID du contenu est obligatoire lorsqu\'un type de contenu est sélectionné.',
        'evidence_urls_max' => 'Vous pouvez entrer un maximum de 5 URL de preuve.',
        'evidence_url_format' => 'Chaque URL de preuve doit être une adresse web valide.',
        'consent_required' => 'Vous devez accepter le traitement des données pour continuer.',
    ],

    // Detail page
    'detail_title' => 'Détail du signalement',
    'submitted_on' => 'Envoyé le',
    'current_status' => 'État actuel',
    'complaint_type_label' => 'Type',
    'reported_content' => 'Contenu signalé',
    'description_label' => 'Description',
    'evidence_label' => 'Preuves jointes',
    'decision' => 'Décision',
    'decision_date' => 'Date de la décision',
    'decided_by_label' => 'Décidée par',
    'no_decision_yet' => 'En attente de révision par l\'équipe.',
    'appeal_section' => 'Réclamation / Appel',
    'no_appeal' => 'Aucune réclamation présentée.',
    'content_id_label' => 'ID du contenu',
    'content_type_label' => 'Type de contenu',
    'reported_user_label' => 'Utilisateur signalé',

    // Timeline
    'timeline' => [
        'received' => 'Signalement reçu',
        'under_review' => 'Pris en charge',
        'action_taken' => 'Mesure prise',
        'dismissed' => 'Signalement archivé',
        'appealed' => 'Réclamation présentée',
        'resolved' => 'Cas résolu',
    ],

    // Notification email
    'notification' => [
        'subject' => 'Confirmation de réception du signalement DSA - :reference',
        'greeting' => 'Madame, Monsieur,',
        'body' => 'Votre signalement a été reçu et enregistré avec le numéro de référence **:reference**.',
        'body_2' => 'Nous examinerons votre signalement et vous contacterons dans les délais prévus par la loi sur les services numériques (Rég. UE 2022/2065).',
        'reference_label' => 'Numéro de référence',
        'type_label' => 'Type de signalement',
        'date_label' => 'Date d\'envoi',
        'closing' => 'L\'équipe FlorenceEGI',
    ],

];
