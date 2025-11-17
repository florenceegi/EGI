<?php

return [
    // Page Meta
    'page_title' => 'Mint :title - FlorenceEGI',
    'meta_description' => 'Mintez votre EGI :title sur la blockchain Algorand. Processus sûr et transparent.',

    // Header
    'header_title' => 'Mintez votre EGI',
    'header_description' => 'Complétez votre achat et mintez votre EGI sur la blockchain Algorand. Ce processus est irréversible.',

    // Buttons
    'mint_button' => 'Mint (:price)',
    'mint_button_processing' => 'Mint en cours...',
    'cancel_button' => 'Annuler',
    'back_button' => 'Retour',

    // EGI Preview Section
    'egi_preview' => [
        'title' => 'Aperçu EGI',
        'creator_by' => 'Créé par :name',
    ],

    // Blockchain Info Section
    'blockchain_info' => [
        'title' => 'Informations Blockchain',
        'network' => 'Réseau',
        'network_value' => 'Algorand Mainnet',
        'token_type' => 'Type de Token',
        'token_type_value' => 'ASA (Algorand Standard Asset)',
        'supply' => 'Approvisionnement',
        'supply_value' => '1 (Unique)',
        'royalty' => 'Redevances',
        'royalty_value' => ':percentage% au créateur',
    ],

    // Payment Section
    'payment' => [
        'title' => 'Détails du Paiement',
        'price_label' => 'Prix Final',
        'currency' => 'Devise',
        'payment_method' => 'Méthode de Paiement',
        'payment_method_label' => 'Méthode de Paiement',
        'payment_method_card' => 'Carte de Crédit/Débit',
        'payment_method_egili' => 'Payer avec les Egili',
        'total_label' => 'Total à Payer',
        'credit_card' => 'Carte de Crédit/Débit',
        'paypal' => 'Payer avec PayPal',
        'winning_reservation' => 'Offre gagnante',
        'egili_balance_label' => 'Solde disponible : :balance EGL',
        'egili_required_label' => 'Nécessaires pour ce mint : :required EGL',
        'egili_summary_title' => 'Résumé Egili',
        'egili_summary' => 'Vous avez besoin de :required EGL pour finaliser le mint.',
        'egili_insufficient' => 'Solde Egili insuffisant. Rechargez votre solde ou choisissez une autre méthode.',
        'submit_button' => 'Finaliser le paiement',
    ],

    // Buyer Info Section
    'buyer_info' => [
        'title' => 'Informations Acheteur',
        'wallet_label' => 'Wallet Algorand de destination',
        'wallet_placeholder' => 'Entrez votre adresse wallet Algorand',
        'wallet_help' => 'L\'EGI sera transféré à cette adresse après le mint.',
        'verify_wallet' => 'Assurez-vous que l\'adresse soit correcte - elle ne peut pas être modifiée après le mint.',
    ],

    // Confirmation
    'confirmation' => [
        'title' => 'Confirmer le Mint',
        'description' => 'Vous êtes sur le point de minter cet EGI. Cette opération est irréversible.',
        'agree_terms' => 'J\'accepte les termes et conditions',
        'final_warning' => 'Attention: Le mint ne peut pas être annulé après confirmation.',
    ],

    // Success Messages
    'success' => [
        'minted' => 'EGI minté avec succès !',
        'transaction_id' => 'ID de Transaction: :id',
        'view_on_explorer' => 'Voir sur Algorand Explorer',
        'certificate_ready' => 'Le certificat d\'authenticité est prêt au téléchargement.',
    ],

    // Error Messages
    'errors' => [
        'missing_params' => 'Paramètres manquants pour le mint.',
        'invalid_reservation' => 'Réservation invalide ou expirée.',
        'already_minted' => 'Cet EGI a déjà été minté.',
        'payment_failed' => 'Paiement échoué. Veuillez réessayer.',
        'mint_failed' => 'Mint échoué. Contactez le support.',
        'invalid_wallet' => 'Adresse wallet invalide.',
        'blockchain_error' => 'Erreur blockchain. Réessayez plus tard.',
        'invalid_amount' => 'Impossible de calculer le montant du mint. Contactez le support.',
        'insufficient_egili' => 'Vous n’avez pas assez d’Egili pour finaliser ce mint.',
        'egili_disabled' => 'Le paiement en Egili n’est pas activé pour cet EGI.',
        'merchant_not_configured' => 'Le créateur n’a pas terminé la configuration des paiements pour ce fournisseur. Contactez-le ou choisissez une autre méthode de paiement.',
        'unauthorized' => 'Vous n’êtes pas autorisé à finaliser ce mint.',
    ],

    // Validation
    'validation' => [
        'wallet_required' => 'L\'adresse wallet est requise.',
        'wallet_format' => 'L\'adresse wallet doit être une adresse Algorand valide.',
        'terms_required' => 'Vous devez accepter les termes et conditions.',
    ],

    // MiCA Compliance
    'compliance' => [
        'mica_title' => '⚖️ Conformité MiCA',
        'mica_description' => 'Ce processus est complètement MiCA-SAFE. Nous payons en FIAT via des PSP autorisés, mintons le NFT pour vous, et ne gérons qu\'une garde temporaire si nécessaire.',
    ],
];
