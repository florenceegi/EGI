<?php

/**
 * Messages de Réservation
 * @package FlorenceEGI
 * @subpackage Traductions
 * @language fr
 * @version 2.0.0
 */

return [
    // Messages de succès
    'success' => 'Votre réservation a été effectuée avec succès ! Le certificat a été généré.',
    'cancel_success' => 'Votre réservation a été annulée avec succès.',
    'success_title' => 'Réservation effectuée !',
    'view_certificate' => 'Voir le Certificat',
    'close' => 'Fermer',

    // Messages d'erreur
    'unauthorized' => 'Vous devez connecter votre portefeuille ou vous connecter pour effectuer une réservation.',
    'validation_failed' => 'Veuillez vérifier les données saisies et réessayer.',
    'auth_required' => 'L\'authentification est requise pour voir vos réservations.',
    'list_failed' => 'Impossible de récupérer vos réservations. Veuillez réessayer plus tard.',
    'status_failed' => 'Impossible de récupérer le statut de la réservation. Veuillez réessayer plus tard.',
    'unauthorized_cancel' => 'Vous n\'avez pas l\'autorisation d\'annuler cette réservation.',
    'cancel_failed' => 'Impossible d\'annuler la réservation. Veuillez réessayer plus tard.',

    // Boutons UI
    'button' => [
        'reserve' => 'Réserver',
        'reserved' => 'Réservé',
        'make_offer' => 'Faire une offre'
    ],

    // Badges
    'badge' => [
        'highest' => 'Priorité Maximale',
        'superseded' => 'Priorité Inférieure',
        'has_offers' => 'Réservé'
    ],

    // Détails de réservation
    'already_reserved' => [
        'title' => 'Déjà Réservé',
        'text' => 'Vous avez déjà une réservation pour cet EGI.',
        'details' => 'Détails de votre réservation :',
        'type' => 'Type',
        'amount' => 'Montant',
        'status' => 'Statut',
        'view_certificate' => 'Voir le Certificat',
        'ok' => 'OK',
        'new_reservation' => 'Nouvelle Réservation',
        'confirm_new' => 'Voulez-vous effectuer une nouvelle réservation ?'
    ],

    // Historique des réservations
    'history' => [
        'title' => 'Historique des Réservations',
        'entries' => 'Entrées de Réservation',
        'view_certificate' => 'Voir le Certificat',
        'no_entries' => 'Aucune réservation trouvée.',
        'be_first' => 'Soyez le premier à réserver cet EGI !',
        'purchases_offers_title' => 'Historique des Achats / Offres'
    ],

    // Messages d'erreur
    'errors' => [
        'button_click_error' => 'Une erreur s\'est produite lors du traitement de votre demande.',
        'form_validation' => 'Veuillez vérifier les données saisies et réessayer.',
        'api_error' => 'Une erreur de communication s\'est produite avec le serveur.',
        'unauthorized' => 'Vous devez connecter votre portefeuille ou vous connecter pour effectuer une réservation.'
    ],

    // Formulaire
    'form' => [
        'title' => 'Réserver cet EGI',
        'offer_amount_label' => 'Votre Offre (EUR)',
        'offer_amount_placeholder' => 'Saisir le montant en EUR',
        'algo_equivalent' => 'Environ :amount ALGO',
        'terms_accepted' => 'J\'accepte les termes et conditions pour les réservations EGI',
        'contact_info' => 'Informations de Contact Supplémentaires (Optionnel)',
        'submit_button' => 'Effectuer la Réservation',
        'cancel_button' => 'Annuler'
    ],

    // Type de réservation
    'type' => [
        'strong' => 'Réservation Forte',
        'weak' => 'Réservation Faible'
    ],

    // Niveaux de priorité
    'priority' => [
        'highest' => 'Réservation Active',
        'superseded' => 'Dépassée',
    ],

    // Statut de la réservation
    'status' => [
        'active' => 'Active',
        'pending' => 'En attente',
        'cancelled' => 'Annulée',
        'expired' => 'Expirée'
    ],

    // === NOUVELLE SECTION : NOTIFICATIONS ===
    'notifications' => [
        'reservation_expired' => 'Votre réservation de €:amount pour :egi_title a expiré.',
        'superseded' => 'Votre offre pour :egi_title a été dépassée. Nouvelle offre la plus élevée : €:new_highest_amount',
        'highest' => 'Félicitations ! Votre offre de €:amount pour :egi_title est maintenant la plus élevée !',
        'rank_changed' => 'Votre position pour :egi_title a changé : vous êtes maintenant en position #:new_rank',
        'competitor_withdrew' => 'Un concurrent s\'est retiré. Vous êtes monté à la position #:new_rank pour :egi_title',
        'pre_launch_reminder' => 'Le mint on-chain commencera bientôt ! Confirmez votre réservation pour :egi_title.',
        'mint_window_open' => 'C\'est à votre tour ! Vous avez 48 heures pour compléter le mint de :egi_title.',
        'mint_window_closing' => 'Attention ! Il ne reste que :hours_remaining heures pour compléter le mint de :egi_title.',
        'default' => 'Mise à jour sur votre réservation pour :egi_title',
        'archived_success' => 'Notification archivée avec succès.'
    ],
];
