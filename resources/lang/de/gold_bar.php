<?php

return [
    // Kategoriename für Badge
    'category_name' => 'Goldbarren',

    // Komponentenbeschriftungen
    'title' => 'Goldbarren Informationen',
    'subtitle' => 'Richtwert Basierend auf dem Aktuellen Goldpreis',

    // Gold-Eigenschaften
    'weight' => 'Gewicht',
    'weight_unit' => 'Einheit',
    'purity' => 'Reinheit',
    'pure_gold' => 'Reingoldgehalt',

    // Wertbeschriftungen
    'gold_price' => 'Gold Spotpreis',
    'creator_margin' => 'Ersteller-Marge',
    'per_gram' => 'pro Gramm',
    'per_oz' => 'pro Feinunze',
    'base_value' => 'Gold Basiswert',
    'margin' => 'Ersteller-Marge',
    'indicative_value' => 'Richtwert',

    // Haftungsausschlüsse
    'disclaimer' => 'Dies ist ein Richtwert basierend auf dem aktuellen Gold-Spotpreis. Der tatsächliche Verkaufspreis wird vom Ersteller festgelegt.',
    'price_updated_at' => 'Preis zuletzt aktualisiert',
    'price_source' => 'Quelle',

    // Reinheitsbeschreibungen
    'purity_999' => '24k - 99.9% Rein',
    'purity_995' => '99.5% Rein',
    'purity_990' => '99.0% Rein',
    'purity_916' => '22k - 91.6% Rein',
    'purity_750' => '18k - 75.0% Rein',

    // Gewichtseinheiten
    'unit_grams' => 'Gramm',
    'unit_ounces' => 'Unzen',
    'unit_troy_ounces' => 'Feinunzen',

    // Statusmeldungen
    'loading' => 'Goldpreis wird geladen...',
    'error' => 'Goldpreis konnte nicht abgerufen werden. Bitte versuchen Sie es später erneut.',
    'not_gold_bar' => 'Dieses EGI ist kein Goldbarren.',

    // Aktualisierungsfunktion
    'refresh_button' => 'Preis Aktualisieren',
    'refresh_cost' => 'Kosten: :cost Egili',
    'refresh_available_now' => 'Jetzt verfügbar',
    'next_refresh' => 'Nächste automatische Aktualisierung in :time',
    'refresh_success' => 'Goldpreis erfolgreich aktualisiert!',
    'insufficient_egili' => 'Nicht genügend Egili. Sie benötigen :required aber haben :current.',
    'refresh_confirm_title' => 'Goldpreis aktualisieren?',
    'refresh_confirm_message' => 'Dies kostet :cost Egili von Ihrem Guthaben. Der Preis wird mit Echtzeitdaten aktualisiert.',
    'refresh_confirm_button' => 'Aktualisieren für :cost Egili',
    'refresh_cancel' => 'Abbrechen',

    // CRUD Margen-Bereich
    'margin' => [
        'title' => 'Goldbarren-Marge',
        'description' => 'Legen Sie Ihre Marge auf den Goldbarrenwert fest. Sie können einen Prozentsatz, einen Festbetrag oder beides verwenden.',
        'percent_label' => 'Prozentuale Marge',
        'percent_hint' => 'Prozentsatz zum Goldwert hinzufügen (z.B. 5%)',
        'fixed_label' => 'Feste Marge',
        'fixed_hint' => 'Fester EUR-Betrag zum Goldwert hinzufügen',
        'current_value' => 'Aktueller Richtwert',
        'value_note' => 'Berechnet aus Gold-Spotpreis plus Margen',
    ],
];
