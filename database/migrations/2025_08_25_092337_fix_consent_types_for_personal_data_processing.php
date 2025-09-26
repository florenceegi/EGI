<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        // 1. Assicurati che il consent_type allow-personal-data-processing esista con i campi corretti
        DB::table('consent_types')->updateOrInsert(
            ['slug' => 'allow-personal-data-processing'],
            [
                'slug' => 'allow-personal-data-processing',
                'legal_basis' => 'contract',
                'data_categories' => json_encode([
                    'personal_information',
                    'contact_data',
                    'usage_data'
                ]),
                'processing_purposes' => json_encode([
                    'platform_operation',
                    'service_delivery',
                    'account_management',
                    'legal_compliance'
                ]),
                'recipients' => json_encode([
                    'internal_staff',
                    'service_providers'
                ]),
                'international_transfers' => false,
                'is_required' => true,
                'is_granular' => false,
                'can_withdraw' => true,
                'withdrawal_effect_days' => 30,
                'retention_period' => 'contract_duration',
                'deletion_method' => 'hard_delete',
                'priority_order' => 1,
                'is_active' => true,
                'requires_double_opt_in' => false,
                'requires_age_verification' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // 2. Verifica e sistema eventuali JSON malformattati in processing_purposes
        $records = DB::table('consent_types')->get();

        foreach ($records as $record) {
            $needsUpdate = false;
            $updates = ['updated_at' => now()];

            // Controlla processing_purposes
            if (!empty($record->processing_purposes)) {
                $purposes = json_decode($record->processing_purposes);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $updates['processing_purposes'] = json_encode(['platform_operation']);
                    $needsUpdate = true;
                }
            }

            // Controlla data_categories
            if (!empty($record->data_categories)) {
                $categories = json_decode($record->data_categories);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $updates['data_categories'] = json_encode(['personal_information']);
                    $needsUpdate = true;
                }
            }

            // Controlla recipients
            if (!empty($record->recipients)) {
                $recipients = json_decode($record->recipients);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $updates['recipients'] = json_encode(['internal_staff']);
                    $needsUpdate = true;
                }
            }

            if ($needsUpdate) {
                DB::table('consent_types')
                    ->where('id', $record->id)
                    ->update($updates);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        // Non rimuovere il record, potrebbe essere usato da altri dati
        // Semplicemente disattivalo
        DB::table('consent_types')
            ->where('slug', 'allow-personal-data-processing')
            ->update(['is_active' => false]);
    }
};
