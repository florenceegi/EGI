<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

return new class extends Migration {
    /**
     * Ensure the Terms of Service consent type exists and is active.
     */
    public function up(): void
    {
        $timestamp = Carbon::now();

        DB::table('consent_types')->updateOrInsert(
            ['slug' => 'terms-of-service'],
            [
                'legal_basis' => 'contract',
                'data_categories' => json_encode([
                    'legal_acceptance_data',
                ]),
                'processing_purposes' => json_encode([
                    'legal_compliance',
                ]),
                'recipients' => json_encode([
                    'internal_staff',
                    'legal_team',
                ]),
                'international_transfers' => false,
                'is_required' => true,
                'is_granular' => false,
                'can_withdraw' => false,
                'withdrawal_effect_days' => 0,
                'retention_period' => 'contract_duration',
                'deletion_method' => 'hard_delete',
                'priority_order' => 10,
                'is_active' => true,
                'requires_double_opt_in' => false,
                'requires_age_verification' => false,
                'updated_at' => $timestamp,
                'created_at' => $timestamp,
            ]
        );
    }

    /**
     * Deactivate the Terms of Service consent type on rollback.
     */
    public function down(): void
    {
        DB::table('consent_types')
            ->where('slug', 'terms-of-service')
            ->update([
                'is_active' => false,
                'updated_at' => Carbon::now(),
            ]);
    }
};

