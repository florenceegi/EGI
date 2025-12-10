<?php

return [
    // Category name for badge
    'category_name' => 'Gold Bar',

    // Component labels
    'title' => 'Gold Bar Information',
    'subtitle' => 'Indicative Value Based on Current Gold Price',

    // Gold properties
    'weight' => 'Weight',
    'weight_unit' => 'Unit',
    'purity' => 'Purity',
    'pure_gold' => 'Pure Gold Content',

    // Value labels
    'gold_price' => 'Gold Spot Price',
    'creator_margin' => 'Creator Margin',
    'per_gram' => 'per gram',
    'per_oz' => 'per troy ounce',
    'base_value' => 'Base Gold Value',
    'margin' => 'Creator Margin',
    'indicative_value' => 'Indicative Value',
    'final_value' => 'Total Value',

    // Disclaimers
    'disclaimer' => 'This is an indicative value based on the current gold spot price. The actual sale price is determined by the Creator.',
    'price_updated_at' => 'Price last updated',
    'price_source' => 'Source',

    // Purity descriptions
    'purity_999' => '24k - 99.9% Pure',
    'purity_995' => '99.5% Pure',
    'purity_990' => '99.0% Pure',
    'purity_916' => '22k - 91.6% Pure',
    'purity_750' => '18k - 75.0% Pure',

    // Weight units
    'unit_grams' => 'Grams',
    'unit_ounces' => 'Ounces',
    'unit_troy_ounces' => 'Troy Ounces',

    // Status messages
    'loading' => 'Loading gold price...',
    'error' => 'Unable to fetch gold price. Please try again later.',
    'not_gold_bar' => 'This EGI is not a Gold Bar.',

    // Mint messages
    'mint_price_expired' => 'The time to complete the Mint operation has expired (10 minutes). The gold price may have changed. Please try again to get an updated price.',
    'mint_price_warning' => 'The gold bar price may be updated to the latest quotation.',

    // Refresh feature
    'refresh_button' => 'Refresh Price',
    'refresh_cost' => 'Cost: :cost Egili',
    'refresh_available_now' => 'Available now',
    'next_refresh' => 'Next auto-refresh in :time',
    'refresh_success' => 'Gold price refreshed successfully!',
    'refresh_success_title' => 'Price Updated',
    'refresh_error' => 'Unable to update gold price.',
    'refresh_error_title' => 'Error',
    'refresh_network_error' => 'Connection error. Please try again.',
    'insufficient_egili' => 'Insufficient Egili. You need :required but have :current.',
    'insufficient_egili_title' => 'Insufficient Credits',
    'insufficient_egili_message' => 'You don\'t have enough Egili for this operation.',
    'required' => 'Required',
    'available' => 'Available',
    'missing' => 'Missing',
    'buy_egili_hint' => 'Purchase Egili to continue.',
    'buy_egili_button' => 'Buy Egili',
    'refresh_confirm_title' => 'Refresh Gold Price?',
    'refresh_confirm_message' => 'This will cost :cost Egili from your balance. The price will be updated with real-time data.',
    'refresh_confirm_button' => 'Refresh for :cost Egili',
    'refresh_cancel' => 'Cancel',
    'operation_cost' => 'Operation Cost',
    'your_balance' => 'Your balance',
    'after_operation' => 'After',
    'egili_charged_on_success' => 'Egili will only be charged if the operation succeeds.',
    'confirm_and_charge' => 'Confirm and Charge',
    'refreshing_title' => 'Refreshing...',
    'refreshing_message' => 'Retrieving the updated gold price...',

    // Throttle
    'throttle_exceeded' => 'You\'ve reached the manual refresh limit. Try again in a few hours.',
    'throttle_exceeded_title' => 'Limit Reached',
    'throttle_info' => 'You can perform max :max refreshes every :hours hours.',
    'throttle_remaining' => ':remaining refreshes remaining',
    'throttle_reset_at' => 'Reset in :time',

    // CRUD margin section
    'margin' => [
        'title' => 'Gold Bar Margin',
        'description' => 'Set your margin on the gold bar value. You can use a percentage, a fixed amount, or both.',
        'percent_label' => 'Percentage Margin',
        'percent_hint' => 'Percentage to add to gold value (e.g., 5%)',
        'fixed_label' => 'Fixed Margin',
        'fixed_hint' => 'Fixed EUR amount to add to gold value',
        'current_value' => 'Current indicative value',
        'value_note' => 'Calculated from gold spot price plus margins',
    ],
];
