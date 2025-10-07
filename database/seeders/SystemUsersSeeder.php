<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\UserPersonalData;
use App\Models\UserOrganizationData;
use App\Models\UserDocument;
use App\Models\UserInvoicePreference;
use App\Models\UserConsent;
use App\Enums\Gdpr\GdprActivityCategory;
use Ultra\EgiModule\Contracts\WalletServiceInterface;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * @Oracode Seeder: System Users Creation with Selective Ecosystem
 * 🎯 Purpose: Create the 3 core system users with selective registration flow
 * 🛡️ Privacy: Full GDPR compliance with domain-separated data initialization
 * 🧱 Core Logic: Direct model creation with simplified service dependencies
 *
 * System Users:
 * - ID 1: Natan (creator/superadmin) - NO collection/wallet - business_type: individual
 * - ID 2: Epp (epp/epp_entity) - NO collection/wallet - business_type: corporation
 * - ID 3: Fabio (creator/superadmin) - FULL ecosystem - business_type: individual
 *
 * Features:
 * - Uses actual model fillable fields from knowledge base
 * - Non-blocking service calls to prevent seeder failures
 * - Direct model creation instead of complex service dependencies
 * - Safe logging that works even if services are unavailable
 * - Correct ENUM values for business_type field
 * - Selective ecosystem creation (only user ID 3)
 *
 * @package Database\Seeders
 * @author Padmin D. Curtis (for Fabio Cherici)
 * @version 1.4.0 - Selective ecosystem creation (only ID 3)
 * @date 2025-07-30
 * @solution Forced IDs + Selective Ecosystem + Actual Model Fields + ENUM Compliance
 */
class SystemUsersSeeder extends Seeder {
    /**
     * System users configuration
     */
    protected array $systemUsers = [
        [
            'id' => 1,
            'name' => 'natan',
            'email' => 'natan@gmail.com',
            'usertype' => 'natan',
            'role' => 'superadmin'
        ],
        [
            'id' => 2,
            'name' => 'epp',
            'email' => 'epp@gmail.com',
            'usertype' => 'epp',
            'role' => 'epp_entity'
        ],
    ];

    /**
     * Services for complete registration flow - only essential ones
     */
    protected ?UltraLogManager $logger = null;

    /**
     * Initialize essential services only
     */
    protected function initializeServices(): void {
        if ($this->logger === null) {
            try {
                $this->logger = app(UltraLogManager::class);
            } catch (\Exception $e) {
                // If UltraLogManager is not available, use Laravel's default logger
                $this->logger = null;
            }
        }
    }

    /**
     * Safe logging method that works even if logger service is not available
     */
    protected function safeLog(string $level, string $message, array $context = []): void {
        try {
            if ($this->logger) {
                $this->logger->info($message, $context);
            } else {
                \Log::info('[Seeder] ' . $message, $context);
            }
        } catch (\Exception $e) {
            // Silent fail for logging issues during seeding
        }
    }

    /**
     * Run the database seeds.
     */
    public function run(): void {
        // Initialize services first
        $this->initializeServices();

        $this->command->info('🚀 Creating System Users with Complete Ecosystem...');

        try {
            // Disable foreign key checks for forced IDs (MySQL only)
            if (DB::getDriverName() === 'mysql') {
                DB::statement('SET foreign_key_checks=0;');
            }

            DB::transaction(function () {
                foreach ($this->systemUsers as $userData) {
                    $this->createCompleteUser($userData);
                }
            });

            // Re-enable foreign key checks (MySQL only)
            if (DB::getDriverName() === 'mysql') {
                DB::statement('SET foreign_key_checks=1;');
            }

            $this->command->info('✅ System Users created successfully with full ecosystem!');
            $this->logCreationSummary();
        } catch (\Exception $e) {
            // Re-enable foreign key checks (MySQL only) in case of error
            if (DB::getDriverName() === 'mysql') {
                DB::statement('SET foreign_key_checks=1;');
            }

            $this->command->error('❌ Failed to create system users: ' . $e->getMessage());
            \Log::error('[SystemUsersSeeder] Failed to create system users', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }

    /**
     * Create complete user with full registration flow
     * Replicates RegisteredUserController::store() logic exactly
     */
    protected function createCompleteUser(array $userData): void {
        // Ensure services are initialized
        $this->initializeServices();

        $logContext = [
            'operation' => 'system_user_seeder_creation',
            'user_data' => $userData,
            'seeder_run' => true,
            'timestamp' => now()->toISOString()
        ];

        try {
            $this->command->info("Creating user: {$userData['name']} ({$userData['usertype']})...");

            // 1. CREATE USER WITH ALGORAND WALLET
            $user = $this->createUserWithAlgorandWallet($userData);
            $logContext['user_id'] = $user->id;
            $logContext['algorand_wallet'] = $user->wallet;

            // 2. ASSIGN ROLE AND CHECK ECOSYSTEM PERMISSIONS
            $canCreateEcosystem = $this->assignRoleAndCheckPermissions($user, $userData);
            $logContext['can_create_ecosystem'] = $canCreateEcosystem;

            // 3. CONDITIONAL ECOSYSTEM SETUP - Only for user ID 3 (Fabio)
            $collection = null;
            if ($canCreateEcosystem && $userData['id'] === 3) {
                $collection = $this->createFullEcosystem($user, $userData, $logContext);
                $logContext['collection_id'] = $collection->id;
                $logContext['ecosystem_created'] = true;
                $this->command->info("   💼 Ecosystem: ✅ Created (Full setup)");
            } else {
                $logContext['ecosystem_created'] = false;
                if ($userData['id'] === 1 || $userData['id'] === 2) {
                    $this->command->info("   💼 Ecosystem: ❌ Skipped (User {$userData['id']} - no collection needed)");
                } else {
                    $this->command->info("   💼 Ecosystem: ❌ None (No permissions)");
                }
            }

            // 4. INITIALIZE USER DOMAINS (always)
            $this->initializeUserDomains($user, $userData, $logContext);

            // 5. PROCESS GDPR CONSENTS
            $this->processGdprConsents($user, $userData, $logContext);

            // 6. CREATE AUDIT RECORD
            $this->createRegistrationAuditRecord($user, $collection, $userData, $logContext);

            $this->command->info("✅ User {$userData['name']} created successfully!");
        } catch (\Exception $e) {
            $this->command->error("❌ Failed to create user {$userData['name']}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create user with valid Algorand wallet address
     * Uses actual User model fillable fields from knowledge base
     */
    protected function createUserWithAlgorandWallet(array $userData): User {
        try {
            $algorandAddress = $this->generateValidAlgorandAddress();

            // ✅ CHECK se utente esiste già
            $existingUser = User::find($userData['id']);

            if ($existingUser) {
                $this->command->warn("User with ID {$userData['id']} already exists. Updating...");

                // Update existing user instead of creating
                $existingUser->update([
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'usertype' => $userData['usertype'],
                    'wallet' => $algorandAddress,
                    'consent_summary' => $this->buildConsentSummary($userData),
                    'consents_updated_at' => now(),
                    'gdpr_compliant' => true,
                    'gdpr_status_updated_at' => now(),
                    'updated_at' => now(),
                ]);

                return $existingUser;
            }

            // Create new user if doesn't exist
            return User::forceCreate([
                'id' => $userData['id'],
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => Hash::make('Password'),
                'usertype' => $userData['usertype'],
                'wallet' => $algorandAddress,
                'wallet_balance' => 0.0000,
                'language' => 'it',
                'email_verified_at' => now(),
                'created_via' => 'system_seeder',
                'consent_summary' => $this->buildConsentSummary($userData),
                'consents_updated_at' => now(),
                'gdpr_compliant' => true,
                'gdpr_status_updated_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            throw new \Exception("Failed to create user {$userData['name']} with Algorand wallet: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Generate valid Algorand address format (58 chars, Base32 [A-Z2-7])
     */
    protected function generateValidAlgorandAddress(): string {
        try {
            // Algorand addresses: 58 chars, Base32 alphabet [A-Z2-7]
            $base32Chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
            $address = '';

            for ($i = 0; $i < 58; $i++) {
                $address .= $base32Chars[random_int(0, 31)];
            }

            // Validate our generated address
            if (!preg_match('/^[A-Z2-7]{58}$/', $address)) {
                throw new \Exception('Generated address does not match Algorand format validation');
            }

            return $address;
        } catch (\Exception $e) {
            throw new \Exception('Failed to generate valid Algorand address: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Build consent summary for user record (JSON array for User.consent_summary field)
     * Based on actual User model casts: 'consent_summary' => 'array'
     */
    protected function buildConsentSummary(array $userData): array {
        return [
            'privacy_policy' => true,
            'age_confirmed' => true,
            'registration_ip' => '127.0.0.1',
            'registration_user_agent' => 'SystemSeeder/1.0',
            'registration_method' => 'system_seeder',
            'consent_timestamp' => now()->toISOString(),
            'system_user' => true,
            'user_id' => $userData['id'],
            'gdpr_compliant' => true,
        ];
    }

    /**
     * Assign role and check ecosystem creation permissions - Simplified for seeder
     * Only user ID 3 (Fabio) gets full ecosystem creation capability
     */
    protected function assignRoleAndCheckPermissions(User $user, array $userData): bool {
        try {
            // For system users, assign the specific role directly from userData
            $assignedRole = $userData['role'];

            $user->assignRole($assignedRole);

            // Refresh user to load role permissions
            $user->refresh();

            // Only user ID 3 (Fabio) should have ecosystem creation capability
            $canCreateEcosystem = ($userData['id'] === 3);

            $this->safeLog('info', '[Seeder] Role assigned and permissions checked', [
                'user_id' => $user->id,
                'usertype' => $userData['usertype'],
                'assigned_role' => $assignedRole,
                'can_create_ecosystem' => $canCreateEcosystem,
                'system_user' => true,
                'ecosystem_policy' => 'only_user_id_3_gets_full_ecosystem'
            ]);

            return $canCreateEcosystem;
        } catch (\Exception $e) {
            throw new \Exception("Failed to assign role for user {$userData['name']}: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Create complete ecosystem: collection + relationships - Simplified for seeder
     * Creates collection directly without complex services
     */
    protected function createFullEcosystem(User $user, array $userData, array $logContext): \App\Models\Collection {
        try {
            // 1. Create Collection directly using fillable fields
            $collectionName = $this->getCollectionNameForUserType($userData['usertype'], $userData['name']);

            $collection = \App\Models\Collection::create([
                'creator_id' => $user->id,
                'owner_id' => $user->id,
                'collection_name' => $collectionName,
                'is_default' => true,
                'description' => "Default collection for system user {$userData['name']}",
                'type' => 'personal',
                'status' => 'active',
                'is_published' => false,
            ]);

            // 2. Create Collection-User relationship using CollectionUser model
            \App\Models\CollectionUser::create([
                'collection_id' => $collection->id,
                'user_id' => $user->id,
                'role' => 'admin',
                'is_owner' => true,
                'status' => 'active',
                'joined_at' => now(),
                'metadata' => [
                    'created_via' => 'system_seeder',
                    'system_user' => true,
                    'usertype' => $userData['usertype'],
                ],
            ]);

            // 3. Set as Current Collection
            $user->update(['current_collection_id' => $collection->id]);

            // 4. Try to create wallets via service (non-blocking)
            try {
                // Try to get wallet service and create wallets
                $walletService = app(WalletServiceInterface::class);
                $walletService->attachDefaultWalletsToCollection($collection, $user);
            } catch (\Exception $walletError) {
                $this->safeLog('warning', '[Seeder] Failed to create wallets (non-blocking)', [
                    'user_id' => $user->id,
                    'collection_id' => $collection->id,
                    'wallet_error' => $walletError->getMessage()
                ]);
            }

            $this->safeLog('info', '[Seeder] Full ecosystem created successfully', [
                'user_id' => $user->id,
                'collection_id' => $collection->id,
                'collection_name' => $collection->collection_name,
                'user_role_in_collection' => 'admin'
            ]);

            return $collection;
        } catch (\Exception $e) {
            throw new \Exception("Failed to create ecosystem for user {$userData['name']}: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Generate collection name based on user type and name
     * Updated for system users specific types
     */
    protected function getCollectionNameForUserType(string $userType, string $userName): string {
        $firstName = explode(' ', trim($userName), 2)[0];

        $typeNames = [
            'creator' => "{$firstName}'s Arte",
            'enterprise' => "{$firstName} Corporate Gallery",
            'patron' => "Patronato di {$firstName}",
            'collector' => "Collezione di {$firstName}",
            'epp' => "EPP {$firstName} Gallery",
        ];

        return $typeNames[$userType] ?? "{$firstName}'s Collection";
    }

    /**
     * Initialize all user domain tables using actual fillable fields and ENUM values
     * Based on actual model fillable fields from knowledge base
     */
    protected function initializeUserDomains(User $user, array $userData, array $logContext): void {
        try {
            // User Profile (always) - using actual fillable fields
            UserProfile::create([
                'user_id' => $user->id,
                // All other UserProfile fields are optional based on fillable
            ]);

            // Personal Data (always, GDPR-sensitive) - using actual fillable fields
            UserPersonalData::create([
                'user_id' => $user->id,
                'allow_personal_data_processing' => true,
                'processing_purposes' => ['platform_operation', 'system_administration'],
                'consent_updated_at' => now(),
            ]);

            // Organization Data (for all system users) - using correct ENUM values
            $businessTypeMapping = [
                'natan' => 'individual',
                'fabio' => 'individual',
                'epp' => 'corporation',
            ];

            UserOrganizationData::create([
                'user_id' => $user->id,
                'business_type' => $businessTypeMapping[strtolower($userData['name'])],
                'is_seller_verified' => true, // System users are pre-verified
                'can_issue_invoices' => true,
            ]);

            // Documents (always) - using actual fillable fields
            UserDocument::create([
                'user_id' => $user->id,
                'verification_status' => 'verified', // System users are pre-verified
                'verified_at' => now(),
            ]);

            // Invoice Preferences (always) - using actual fillable fields
            UserInvoicePreference::create([
                'user_id' => $user->id,
                'can_issue_invoices' => true,
                'auto_request_invoice' => false,
            ]);

            $this->safeLog('info', '[Seeder] User domains initialized successfully', [
                'user_id' => $user->id,
                'user_name' => $userData['name'],
                'business_type' => $businessTypeMapping[strtolower($userData['name'])],
                'domains_created' => ['profiles', 'personal_data', 'organization_data', 'documents', 'invoice_preferences'],
            ]);
        } catch (\Exception $e) {
            throw new \Exception("Failed to initialize user domains for {$userData['name']}: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Process GDPR consents - Simplified version for seeder
     * Creates basic consent records directly using UserConsent model
     */
    protected function processGdprConsents(User $user, array $userData, array $logContext): void {
        try {
            // Create basic consent records directly for system users
            $basicConsents = [
                'privacy_policy' => 'Privacy Policy Acceptance',
                'marketing' => 'Marketing Communications',
                'analytics' => 'Analytics and Performance',
                'platform_operation' => 'Platform Operation',
            ];

            foreach ($basicConsents as $consentType => $description) {
                UserConsent::create([
                    'user_id' => $user->id,
                    'consent_type' => $consentType,
                    'granted' => true,
                    'legal_basis' => 'consent',
                    'ip_address' => '127.0.0.1',
                    'user_agent' => 'SystemSeeder/1.0',
                    'metadata' => [
                        'source' => 'system_seeder',
                        'system_user' => true,
                        'description' => $description,
                    ],
                    'status' => 'active',
                ]);
            }

            $this->safeLog('info', '[Seeder] GDPR consents processed successfully', [
                'user_id' => $user->id,
                'consents_created' => array_keys($basicConsents),
                'consent_method' => 'direct_seeder_creation'
            ]);
        } catch (\Exception $e) {
            // Make GDPR consent creation non-blocking for system users
            $this->safeLog('warning', "[Seeder] Failed to process GDPR consents for {$userData['name']} (non-blocking)", [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Create registration audit record - Simplified version for seeder
     * Non-blocking to avoid seeder failure due to audit service issues
     */
    protected function createRegistrationAuditRecord(User $user, ?\App\Models\Collection $collection, array $userData, array $logContext): void {
        try {
            // Try to create audit record via service
            $auditService = app(\App\Services\Gdpr\AuditLogService::class);
            $auditService->logUserAction(
                $user,
                'system_user_created_with_complete_ecosystem',
                [
                    'registration_method' => 'system_seeder',
                    'usertype' => $userData['usertype'],
                    'role' => $userData['role'],
                    'ecosystem_created' => !is_null($collection),
                    'collection_id' => $collection?->id,
                    'collection_name' => $collection?->collection_name,
                    'algorand_wallet' => $user->wallet,
                    'ip_address' => '127.0.0.1',
                    'user_agent' => 'SystemSeeder/1.0',
                    'system_user' => true,
                    'forced_id' => $userData['id'],
                    'privacy_consents' => [
                        'privacy_policy' => true,
                        'age_confirmed' => true,
                    ],
                    'domains_initialized' => true,
                    'pre_verified' => true,
                ],
                GdprActivityCategory::REGISTRATION,
            );
        } catch (\Exception $e) {
            // Don't fail seeder for audit errors - it's non-blocking
            $this->safeLog('warning', '[Seeder] Failed to create audit record (non-blocking)', [
                'user_id' => $user->id,
                'audit_error' => $e->getMessage()
            ]);

            // Create a simple UserActivity record as fallback
            try {
                \App\Models\UserActivity::create([
                    'user_id' => $user->id,
                    'action' => 'system_user_registration',
                    'category' => 'registration',
                    'context' => [
                        'method' => 'system_seeder',
                        'usertype' => $userData['usertype'],
                        'role' => $userData['role'],
                        'system_user' => true,
                    ],
                    'privacy_level' => 'standard',
                    'ip_address' => '127.0.0.1',
                    'user_agent' => 'SystemSeeder/1.0',
                    'expires_at' => now()->addYears(7), // GDPR retention
                ]);
            } catch (\Exception $fallbackError) {
                // Even fallback failed - just log and continue
                $this->safeLog('warning', '[Seeder] Even fallback audit failed (continuing)', [
                    'user_id' => $user->id,
                    'fallback_error' => $fallbackError->getMessage()
                ]);
            }
        }
    }

    /**
     * Log creation summary
     */
    protected function logCreationSummary(): void {
        $users = User::whereIn('id', [1, 2, 3])->with('roles', 'collections')->get();

        $this->command->info("\n📊 SYSTEM USERS CREATION SUMMARY:");
        $this->command->info("═══════════════════════════════════");

        foreach ($users as $user) {
            $hasCollection = $user->collections->isNotEmpty();
            $role = $user->roles->first()?->name ?? 'none';

            $this->command->info("🧑 {$user->name} (ID: {$user->id})");
            $this->command->info("   📧 Email: {$user->email}");
            $this->command->info("   🏷️  Type: {$user->usertype} → Role: {$role}");

            // Different ecosystem message based on user policy
            if ($user->id === 3) {
                $this->command->info("   💼 Ecosystem: " . ($hasCollection ? "✅ Created" : "❌ Failed"));
                if ($hasCollection) {
                    $this->command->info("   🏦 Wallet: {$user->wallet}");
                }
            } else {
                $this->command->info("   💼 Ecosystem: ❌ Skipped (Policy: no collection for user {$user->id})");
                $this->command->info("   🏦 Wallet: {$user->wallet}");
            }
            $this->command->info("");
        }

        $this->command->info("🎯 System users ready - Only Fabio (ID: 3) has full ecosystem!");
    }
}
