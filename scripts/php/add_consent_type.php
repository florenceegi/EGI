<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Inserisco il tipo di consenso mancante nella tabella consent_types
DB::table('consent_types')->insert([
    'slug' => 'allow-personal-data-processing',
    'legal_basis' => 'contract',
    'data_categories' => json_encode(['personal_data', 'contact_data']),
    'processing_purposes' => json_encode(['platform_operation', 'service_delivery', 'account_management']),
    'recipients' => json_encode(['internal_systems']),
    'international_transfers' => false,
    'is_required' => true,
    'is_granular' => false,
    'can_withdraw' => true,
    'withdrawal_effect_days' => 30,
    'retention_period' => 'account_lifetime',
    'retention_days' => null,
    'deletion_method' => 'secure_deletion',
    'priority_order' => 1,
    'is_active' => true,
    'requires_double_opt_in' => false,
    'requires_age_verification' => false,
    'minimum_age' => null,
    'icon' => 'shield-check',
    'color' => '#4F46E5',
    'form_fields' => json_encode([]),
    'created_by' => 1,
    'created_at' => now(),
    'updated_at' => now(),
]);

echo 'Tipo di consenso allow-personal-data-processing aggiunto alla tabella consent_types' . PHP_EOL;
