<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add payment-processing consent type as REQUIRED
        DB::table('consent_types')->updateOrInsert(
            ['slug' => 'allow-payment-processing'],
            [
                'slug' => 'allow-payment-processing',
                'legal_basis' => 'contract', // Required for payment processing
                'data_categories' => json_encode([
                    'financial_data',
                    'payment_information',
                    'transaction_history'
                ]),
                'processing_purposes' => json_encode([
                    'payment_processing',
                    'payment_distribution',
                    'financial_compliance',
                    'anti_money_laundering'
                ]),
                'recipients' => json_encode([
                    'internal_staff',
                    'payment_service_providers',
                    'stripe',
                    'paypal'
                ]),
                'international_transfers' => true, // Stripe may transfer to US
                'is_required' => true, // MANDATORY for payment processing
                'is_granular' => false,
                'can_withdraw' => true,
                'withdrawal_effect_days' => 30,
                'retention_period' => 'legal_requirement', // 10 years for financial data
                'deletion_method' => 'anonymize',
                'priority_order' => 2,
                'is_active' => true,
                'requires_double_opt_in' => false,
                'requires_age_verification' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Add consent to all existing users who have consents_updated_at
        // (they already accepted platform terms, so we extend to payment processing)
        $existingUsers = DB::table('users')
            ->whereNotNull('consents_updated_at')
            ->get();

        $defaultConsentVersion = DB::table('consent_versions')
            ->where('is_active', true)
            ->orderBy('id', 'desc')
            ->first();

        if ($defaultConsentVersion) {
            foreach ($existingUsers as $user) {
                // Check if user already has this consent
                $existingConsent = DB::table('user_consents')
                    ->where('user_id', $user->id)
                    ->where('consent_type', 'allow-payment-processing')
                    ->first();

                if (!$existingConsent) {
                    DB::table('user_consents')->insert([
                        'user_id' => $user->id,
                        'consent_version_id' => $defaultConsentVersion->id,
                        'consent_type' => 'allow-payment-processing',
                        'granted' => true, // Auto-granted for existing users
                        'legal_basis' => 'contract',
                        'ip_address' => null, // Migration, not user action
                        'user_agent' => 'migration:add_payment_processing_consent',
                        'metadata' => json_encode([
                            'source' => 'migration',
                            'auto_granted' => true,
                            'reason' => 'Required for payment processing - retroactively applied'
                        ]),
                        'status' => 'active',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove payment-processing consents
        DB::table('user_consents')
            ->where('consent_type', 'allow-payment-processing')
            ->delete();

        // Remove consent type
        DB::table('consent_types')
            ->where('slug', 'allow-payment-processing')
            ->delete();
    }
};
