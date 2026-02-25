<?php

/**
 * @package FlorenceEGI\Lang\de
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - ToS v3.1.0 — Egili-System Deutsch)
 * @date 2026-02-25
 * @purpose Traduzione tedesca del sistema Egili e Pacchetti Servizi AI
 */

return [
    'buy_more' => 'KI-Servicepakete',
    'transaction_types' => [
        'earned'        => 'Verdient',
        'spent'         => 'Ausgegeben',
        'admin_grant'   => 'Admin-Bonus',
        'admin_deduct'  => 'Admin-Abzug',
        'purchase'      => 'Gutgeschrieben (KI-Paket)',
        'refund'        => 'Erstattet',
        'expiration'    => 'Abgelaufen',
        'initial_bonus' => 'Startbonus',
    ],

    'wallet' => [
        'title'               => 'Egili-Guthaben',
        'current_balance'     => 'Aktuelles Guthaben',
        'buy_more'            => 'KI-Pakete',
        'recent_transactions' => 'Letzte Transaktionen',
        'view_all'            => 'Alle anzeigen',
        'no_transactions'     => 'Keine Transaktionen',
    ],

    // Purchase System (ToS v3.1.0 — Produkt = KI-Servicepakete in FIAT)
    'purchase' => [
        'title'                  => 'KI-Servicepaket kaufen',
        'subtitle'               => 'Wähle dein Paket — Zahlung nur in EUR',
        'how_many_label'         => 'Wie viele Egili möchtest du kaufen?',
        'amount_placeholder'     => 'z. B. 10000',
        'min_purchase'           => 'Minimum: :min Egili (:eur)',
        'max_purchase'           => 'Maximum: :max Egili (:eur)',
        'unit_price'             => 'Einzelpreis',
        'total_cost'             => 'Gesamtbetrag',
        'select_payment_method'  => 'Zahlungsmethode wählen',
        'payment_method_fiat'    => 'Karte/PayPal (EUR)',
        'select_provider'        => 'Zahlungsanbieter wählen',
        'fiat_provider_stripe'   => 'Stripe (Karte)',
        'fiat_provider_paypal'   => 'PayPal',
        'purchase_now'           => 'Kauf bestätigen',
        'processing'             => 'Verarbeitung...',
        'payment_success'        => 'Zahlung erfolgreich abgeschlossen!',
        'process_error'          => 'Fehler bei der Zahlungsverarbeitung.',
        'order_not_found'        => 'Bestellung nicht gefunden.',
        'unauthorized'           => 'Du bist nicht berechtigt, diese Bestellung anzuzeigen.',
        'invalid_amount'         => 'Ungültiger Betrag.',
        'pricing_error'          => 'Fehler bei der Preisberechnung.',
        'amount_below_min'       => 'Mindestbetrag: :min Egili.',
        'amount_above_max'       => 'Höchstbetrag: :max Egili.',
        'calculating'            => 'Berechnung...',
        // Neue Schlüssel ToS v3.1.0
        'legal_note'             => 'Egili werden automatisch beim Kauf eines KI-Servicepakets in EUR gutgeschrieben.',
        'select_package'         => 'Paket wählen',
        'egili_credited'         => 'Gutgeschriebene Egili',
        'you_get'                => 'Du erhältst',
        'egili_model_note'       => 'Zahlung in EUR — Egili werden automatisch gutgeschrieben',
    ],

    'email' => [
        'purchase_confirmation_subject' => 'KI-Paket Kaufbestätigung — Bestellung :order_ref',
        'greeting'          => 'Hallo :name,',
        'purchase_success'  => 'Dein KI-Servicepaket wurde erfolgreich gekauft! 🎉',
        'order_reference'   => '**Bestellnummer**: :reference',
        'purchase_details'  => '**Kaufdetails:**',
        'view_order'        => 'Bestellung anzeigen',
        'invoice_info'      => 'Die Sammelrechnung erhältst du bis Monatsende per E-Mail.',
        'thank_you'         => 'Vielen Dank für deinen Kauf!',
        'signature'         => 'Das FlorenceEGI-Team',
    ],

    'confirmation' => [
        'title'                => 'Kauf abgeschlossen!',
        'thank_you'            => 'Danke für deinen Kauf',
        'order_reference'      => 'Bestellnummer',
        'order_summary'        => 'Bestellübersicht',
        'egili_purchased'      => 'Gutgeschriebene Egili',
        'unit_price'           => 'Einzelpreis',
        'total_paid'           => 'Bezahlter Betrag',
        'payment_method'       => 'Zahlungsmethode',
        'payment_provider'     => 'Anbieter',
        'payment_id'           => 'Transaktions-ID',
        'purchased_at'         => 'Kaufdatum',
        'status'               => 'Status',
        'status_completed'     => 'Abgeschlossen',
        'status_pending'       => 'Ausstehend',
        'status_failed'        => 'Fehlgeschlagen',
        'wallet_info'          => 'Egili-Guthaben',
        'new_balance'          => 'Neues Guthaben',
        'invoice'              => 'Rechnung',
        'invoice_will_be_sent' => 'Die Sammelrechnung erhältst du bis Monatsende per E-Mail.',
        'download_receipt'     => 'Quittung herunterladen',
        'back_to_dashboard'    => 'Zurück zum Dashboard',
        'email_sent'           => 'Eine Bestätigungs-E-Mail wurde an :email gesendet',
    ],
];
