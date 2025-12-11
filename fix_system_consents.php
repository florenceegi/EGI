<?php

/**
 * Script per aggiungere i consensi mancanti agli utenti di sistema
 * Eseguire con: php fix_system_consents.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\UserConsent;
use Illuminate\Support\Facades\DB;

echo "🔧 Fix System Users Consents\n";
echo "============================\n\n";

// 1. Creo una nuova version che include tutti i consensi system
$systemConsents = [
    'privacy-policy',
    'terms-of-service',
    'platform-services',
    'allow-personal-data-processing',
    'allow-payment-processing',
    'analytics',
];

// Controlla se esiste già
$existingVersion = DB::table('consent_versions')
    ->where('version', '1.1-system')
    ->first();

if ($existingVersion) {
    $versionId = $existingVersion->id;
    echo "✅ ConsentVersion già esistente: ID {$versionId}\n";
} else {
    $versionId = DB::table('consent_versions')->insertGetId([
        'version' => '1.1-system',
        'consent_types' => json_encode($systemConsents),
        'changes' => json_encode(['description' => 'System users consent version']),
        'configuration' => json_encode(['system_users' => true]),
        'effective_date' => now(),
        'is_active' => true,
        'created_by' => 1,
        'notes' => 'Created for system users (Natan, EPP, Frangette)',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    echo "✅ ConsentVersion creata: ID {$versionId}\n";
}

// 2. Creo i consensi per gli utenti di sistema
$systemUserIds = [1, 2, 3];
$created = 0;

foreach ($systemUserIds as $userId) {
    foreach ($systemConsents as $slug) {
        $exists = UserConsent::where('user_id', $userId)
            ->where('consent_type', $slug)
            ->exists();

        if (!$exists) {
            UserConsent::create([
                'user_id' => $userId,
                'consent_version_id' => $versionId,
                'consent_type' => $slug,
                'granted' => true,
                'legal_basis' => 'contract',
                'ip_address' => '127.0.0.1',
                'user_agent' => 'SystemFix/1.0',
                'metadata' => json_encode([
                    'source' => 'system_fix',
                    'system_user' => true,
                    'fixed_at' => now()->toIso8601String(),
                ]),
                'status' => 'active',
            ]);
            $created++;
            echo "   + User {$userId}: {$slug}\n";
        }
    }
}

echo "\n✅ Consensi creati: {$created}\n";

// 3. Verifica finale
echo "\n📋 Verifica allow-payment-processing:\n";
foreach ($systemUserIds as $uid) {
    $user = \App\Models\User::find($uid);
    $hasConsent = UserConsent::where('user_id', $uid)
        ->where('consent_type', 'allow-payment-processing')
        ->where('granted', true)
        ->exists();
    $status = $hasConsent ? '✅ OK' : '❌ MANCANTE';
    echo "   User {$uid} ({$user->name}): {$status}\n";
}

echo "\n🎉 Fatto! Ora puoi riprovare il mint.\n";
