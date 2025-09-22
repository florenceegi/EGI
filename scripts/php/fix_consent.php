<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$user = \App\Models\User::find(19);
if ($user) {
    $existing = $user->consents()->where('consent_type', 'allow-personal-data-processing')->first();
    if (!$existing) {
        \App\Models\UserConsent::create([
            'user_id' => $user->id,
            'consent_version_id' => 1, // Versione attiva trovata
            'consent_type' => 'allow-personal-data-processing',
            'granted' => true,
            'legal_basis' => 'contract',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'System Migration',
            'metadata' => json_encode(['source' => 'system_migration', 'purposes' => ['platform_operation']]),
            'status' => 'active',
        ]);
        echo 'Consenso creato per utente ID: ' . $user->id . PHP_EOL;
    } else {
        echo 'Consenso già esistente per utente ID: ' . $user->id . PHP_EOL;
    }
} else {
    echo 'Utente non trovato' . PHP_EOL;
}
