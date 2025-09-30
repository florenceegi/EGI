<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\ConsentType;
use App\Models\UserConsent;
use App\Models\ConsentHistory;
use App\Models\GdprRequest;
use App\Models\DataExport;
use App\Models\UserActivityLog;
use App\Models\BreachReport;
use App\Models\PrivacyPolicy;
use App\Models\PrivacyPolicyAcceptance;
use App\Models\ProcessingRestriction;
use App\Models\DataRetentionPolicy;
use App\Enums\GdprRequestType;
use App\Enums\GdprRequestStatus;
use App\Enums\DataExportStatus;
use App\Enums\Gdpr\ConsentStatus;
use App\Models\UserActivity;
use Carbon\Carbon;

class GdprSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        $this->command->info('Starting GDPR data seeding...');

        // NOTE: ConsentType creation removed to avoid conflict with ConsentTypeSeeder
        // ConsentTypeSeeder handles all consent type definitions with complete schema

        // 1. Create Privacy Policy
        $this->command->info('Creating privacy policy...');

        // Get legal user ID or default to first user
        $legalUser = User::where('email', config('app.legal_default_user_email', 'legal@example.com'))->first()
            ?? User::first();

        if (!$legalUser) {
            $this->command->error('No users found in database. Privacy policy creation skipped.');
            return;
        }

        $privacyPolicy = PrivacyPolicy::create([
            'version' => '1.0',
            'title' => 'FlorenceEGI Privacy Policy',
            'summary' => json_encode([
                'key_points' => [
                    'Data collection for FlorenceEGI NFT marketplace',
                    'User rights under GDPR compliance',
                    'Personal data protection and processing'
                ],
                'scope' => 'General privacy policy for FlorenceEGI platform'
            ]),
            'content' => json_encode([
                'sections' => [
                    [
                        'title' => 'Introduction',
                        'content' => 'This privacy policy explains how FlorenceEGI collects, uses, and protects your personal data.'
                    ],
                    [
                        'title' => 'Data Collection',
                        'content' => 'We collect data that you provide directly to us, such as when you create an account.',
                        'subsections' => [
                            ['title' => 'Personal Information', 'content' => 'Name, email, phone number'],
                            ['title' => 'Usage Data', 'content' => 'How you interact with our platform']
                        ]
                    ],
                    [
                        'title' => 'Your Rights',
                        'content' => 'Under GDPR, you have the right to access, rectify, and delete your personal data.',
                        'list_items' => [
                            'Right to access your data',
                            'Right to rectification',
                            'Right to erasure',
                            'Right to data portability',
                            'Right to object'
                        ]
                    ]
                ]
            ]),
            'effective_date' => now()->subMonths(6),
            'change_summary' => 'Initial privacy policy version',
            'created_by' => $legalUser->id,
        ]);

        // 3. Create Data Retention Policies
        $this->command->info('Creating data retention policies...');
        DataRetentionPolicy::create([
            'name' => 'User Account Data Retention',
            'slug' => 'user-account-retention',
            'data_category' => 'user_accounts',
            'retention_trigger' => 'inactivity_based',
            'retention_days' => 2555, // 7 years
            'retention_period' => '7 years after last activity',
            'description' => 'User account data retained for 7 years after last activity',
            'legal_basis' => 'legitimate_interest',
            'legal_justification' => 'Legitimate interest and legal obligations for account security',
            'is_active' => true,
        ]);

        DataRetentionPolicy::create([
            'name' => 'Financial Transaction Logs Retention',
            'slug' => 'transaction-logs-retention',
            'data_category' => 'transaction_logs',
            'retention_trigger' => 'time_based',
            'retention_days' => 3650, // 10 years
            'retention_period' => '10 years from transaction date',
            'description' => 'Financial transaction logs for tax purposes',
            'legal_basis' => 'legal_obligation',
            'legal_justification' => 'Legal obligation - tax requirements',
            'is_active' => true,
        ]);

        // 4. Create test data for existing users
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->warn('No users found. Creating test users...');
            $users = User::factory(10)->create();
        }

        foreach ($users as $index => $user) {
            $this->command->info("Processing user {$user->email}...");

            // User Consents
            ConsentType::all()->each(function ($consentType) use ($user) {
                // Skip if already exists
                if ($user->consents()->where('consent_version_id', $consentType->id)->exists()) {
                    return;
                }

                // 80% chance of having consent for non-required types
                if ($consentType->is_required || rand(1, 100) <= 80) {
                    $consent = UserConsent::create([
                        'user_id' => $user->id,
                        'consent_version_id' => $consentType->id, // Usa consent_version_id invece di consent_type_id
                        'consent_type' => $consentType->type ?? 'functional', // Campo string richiesto
                        'granted' => true, // Boolean invece di status
                        'legal_basis' => 'consent', // Campo obbligatorio
                        'ip_address' => '127.0.0.1',
                        'user_agent' => 'Mozilla/5.0 (compatible; GdprSeeder)',
                    ]);

                    // Create consent history
                    ConsentHistory::create([
                        'user_consent_id' => $consent->id,
                        'user_id' => $user->id,
                        'action' => 'granted',
                        'previous_status' => null,
                        'new_status' => ConsentStatus::ACTIVE->value,
                        'ip_address' => $consent->ip_address,
                        'user_agent' => $consent->user_agent,
                    ]);
                }
            });

            // GDPR Requests (30% chance)
            if (rand(1, 100) <= 30) {
                GdprRequest::factory()
                    ->count(rand(1, 3))
                    ->create(['user_id' => $user->id]);
            }

            // Data Exports (20% chance)
            if (rand(1, 100) <= 20) {
                DataExport::factory()
                    ->count(rand(1, 2))
                    ->create(['user_id' => $user->id]);
            }

            // Activity Logs
            UserActivity::factory()
                ->count(rand(5, 20))
                ->create(['user_id' => $user->id]);

            // Privacy Policy Acceptance (90% chance)
            if (rand(1, 100) <= 90) {
                PrivacyPolicyAcceptance::create([
                    'user_id' => $user->id,
                    'privacy_policy_id' => $privacyPolicy->id,
                    'ip_address' => '127.0.0.1',
                    'user_agent' => 'Mozilla/5.0 (compatible; GdprSeeder)',
                ]);
            }

            // Processing Restrictions (30% chance)
            if (rand(1, 100) <= 30) {
                ProcessingRestriction::factory()
                    ->count(rand(1, 3))
                    ->create(['user_id' => $user->id]);

                // 20% chance of having a lifted restriction
                if (rand(1, 100) <= 20) {
                    ProcessingRestriction::factory()
                        ->lifted()
                        ->create(['user_id' => $user->id]);
                }
            }

            // Breach Reports (5% chance - rare)
            if (rand(1, 100) <= 5) {
                BreachReport::factory()->create(['user_id' => $user->id]);
            }
        }

        $this->command->info('GDPR seeding completed successfully!');
    }
}
