<?php

/**
 * @package FlorenceEGI\Lang\fr
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - ToS v3.1.0 — Système Egili Français)
 * @date 2026-02-25
 * @purpose Traduction française du système Egili et Paquets de Services IA
 */

return [
    'buy_more' => 'Paquets de Services IA',
    'transaction_types' => [
        'earned'        => 'Gagné',
        'spent'         => 'Dépensé',
        'admin_grant'   => 'Bonus Admin',
        'admin_deduct'  => 'Déduction Admin',
        'purchase'      => 'Crédité (Pack IA)',
        'refund'        => 'Remboursé',
        'expiration'    => 'Expiré',
        'initial_bonus' => 'Bonus Initial',
    ],

    'wallet' => [
        'title'               => 'Solde Egili',
        'current_balance'     => 'Solde Actuel',
        'buy_more'            => 'Paquets IA',
        'recent_transactions' => 'Dernières Transactions',
        'view_all'            => 'Voir Tout',
        'no_transactions'     => 'Aucune transaction',
    ],

    // Purchase System (ToS v3.1.0 — produit = Paquets de Services IA en FIAT)
    'purchase' => [
        'title'                 => 'Acheter un Paquet de Services IA',
        'subtitle'              => 'Sélectionnez votre paquet — paiement en EUR uniquement',
        'how_many_label'        => 'Combien d\'Egili souhaitez-vous acheter ?',
        'amount_placeholder'    => 'ex : 10000',
        'min_purchase'          => 'Minimum : :min Egili (:eur)',
        'max_purchase'          => 'Maximum : :max Egili (:eur)',
        'unit_price'            => 'Prix unitaire',
        'total_cost'            => 'Total à payer',
        'select_payment_method' => 'Sélectionnez le mode de paiement',
        'payment_method_fiat'   => 'Carte/PayPal (EUR)',
        'select_provider'       => 'Sélectionnez le fournisseur',
        'fiat_provider_stripe'  => 'Stripe (Carte)',
        'fiat_provider_paypal'  => 'PayPal',
        'purchase_now'          => 'Confirmer l\'Achat',
        'processing'            => 'Traitement en cours...',
        'payment_success'       => 'Paiement effectué avec succès !',
        'process_error'         => 'Une erreur est survenue lors du traitement du paiement.',
        'order_not_found'       => 'Commande introuvable.',
        'unauthorized'          => 'Vous n\'êtes pas autorisé à consulter cette commande.',
        'invalid_amount'        => 'Montant invalide.',
        'pricing_error'         => 'Erreur lors du calcul du prix.',
        'amount_below_min'      => 'Le montant doit être au moins :min Egili.',
        'amount_above_max'      => 'Le montant ne peut pas dépasser :max Egili.',
        'calculating'           => 'Calcul en cours...',
        // Nouvelles clés ToS v3.1.0
        'legal_note'            => 'Les Egili sont crédités automatiquement lors de l\'achat d\'un Paquet de Services IA en EUR.',
        'select_package'        => 'Sélectionnez votre paquet',
        'egili_credited'        => 'Egili crédités',
        'you_get'               => 'Vous recevez',
        'egili_model_note'      => 'Vous payez en EUR — les Egili sont crédités automatiquement',
    ],

    'email' => [
        'purchase_confirmation_subject' => 'Confirmation d\'Achat IA — Commande :order_ref',
        'greeting'          => 'Bonjour :name,',
        'purchase_success'  => 'Votre paquet de services IA a été complété avec succès ! 🎉',
        'order_reference'   => '**Numéro de Commande** : :reference',
        'purchase_details'  => '**Détails de l\'Achat :**',
        'view_order'        => 'Voir la Commande',
        'invoice_info'      => 'Vous recevrez la facture aggregate par email avant la fin du mois.',
        'thank_you'         => 'Merci pour votre achat !',
        'signature'         => 'L\'Équipe FlorenceEGI',
    ],

    'confirmation' => [
        'title'                => 'Achat Finalisé !',
        'thank_you'            => 'Merci pour votre achat',
        'order_reference'      => 'Numéro de Commande',
        'order_summary'        => 'Récapitulatif de la Commande',
        'egili_purchased'      => 'Egili Crédités',
        'unit_price'           => 'Prix Unitaire',
        'total_paid'           => 'Total Payé',
        'payment_method'       => 'Mode de Paiement',
        'payment_provider'     => 'Fournisseur',
        'payment_id'           => 'ID de Transaction',
        'purchased_at'         => 'Date d\'Achat',
        'status'               => 'Statut',
        'status_completed'     => 'Complété',
        'status_pending'       => 'En Attente',
        'status_failed'        => 'Échoué',
        'wallet_info'          => 'Solde Egili',
        'new_balance'          => 'Nouveau Solde',
        'invoice'              => 'Facture',
        'invoice_will_be_sent' => 'Vous recevrez la facture aggregate par email avant la fin du mois.',
        'download_receipt'     => 'Télécharger le Reçu',
        'back_to_dashboard'    => 'Retour au Tableau de Bord',
        'email_sent'           => 'Un email de confirmation a été envoyé à :email',
    ],
];
