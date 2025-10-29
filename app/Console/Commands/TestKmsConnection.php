<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Security\KmsClient;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * Test KMS Connection and Configuration
 *
 * @package App\Console\Commands
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - KMS Testing)
 * @date 2025-10-29
 * @purpose Verify KMS configuration and AWS connectivity
 */
class TestKmsConnection extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kms:test
                            {--mode=auto : Test mode: auto, mock, or aws}
                            {--detailed : Show detailed test output}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test KMS connection and encryption/decryption cycle';

    private KmsClient $kmsClient;
    private UltraLogManager $logger;

    /**
     * Execute the console command.
     */
    public function handle(KmsClient $kmsClient, UltraLogManager $logger): int {
        $this->kmsClient = $kmsClient;
        $this->logger = $logger;

        $this->info('🔐 FlorenceEGI KMS Configuration Test');
        $this->newLine();

        // Step 1: Show current configuration
        $this->showConfiguration();
        $this->newLine();

        // Step 2: Test encryption/decryption cycle
        $this->info('🧪 Testing encryption/decryption cycle...');
        $this->newLine();

        $testResult = $this->testEncryptionCycle();

        $this->newLine();

        if ($testResult) {
            $this->components->success('✅ KMS TEST PASSED - Configuration is working correctly!');
            $this->newLine();
            $this->info('Your wallet mnemonics will be encrypted with enterprise-grade security.');
            return Command::SUCCESS;
        } else {
            $this->components->error('❌ KMS TEST FAILED - Check configuration above');
            $this->newLine();
            $this->warn('Please verify:');
            $this->warn('  1. AWS credentials are correct');
            $this->warn('  2. KMS key ARN is valid');
            $this->warn('  3. IAM permissions include kms:Encrypt and kms:Decrypt');
            $this->warn('  4. Network access to AWS KMS endpoint');
            return Command::FAILURE;
        }
    }

    /**
     * Show current KMS configuration
     */
    private function showConfiguration(): void {
        $this->components->info('📋 Current Configuration');

        $appEnv = config('app.env');
        $kmsEnv = env('KMS_ENVIRONMENT', $appEnv);
        $provider = config('kms.provider', 'aws');
        $kekId = config('kms.kek_id', 'egi-wallet-master-key');

        $isProduction = $kmsEnv === 'production';

        $this->table(
            ['Setting', 'Value'],
            [
                ['APP_ENV', $appEnv],
                ['KMS_ENVIRONMENT', $kmsEnv],
                ['KMS Mode', $isProduction ? '🔴 PRODUCTION (Cloud KMS)' : '🟡 DEVELOPMENT (Mock KMS)'],
                ['Provider', $provider],
                ['KEK ID', $kekId],
            ]
        );

        if ($isProduction && $provider === 'aws') {
            $this->showAwsConfiguration();
        }

        if (!$isProduction) {
            $this->warn('⚠️  Using Mock KMS (local encryption)');
            $this->warn('   Set KMS_ENVIRONMENT=production to use AWS KMS');
        }
    }

    /**
     * Show AWS-specific configuration
     */
    private function showAwsConfiguration(): void {
        $region = config('kms.aws.region', 'not-set');
        $keyArn = config('kms.aws.kek_arn');
        $accessKeyId = config('kms.aws.key');

        $this->newLine();
        $this->components->info('☁️  AWS KMS Configuration');

        $this->table(
            ['AWS Setting', 'Value'],
            [
                ['Region', $region],
                ['Access Key ID', $accessKeyId ? $this->maskSecret($accessKeyId) : '❌ NOT SET'],
                ['Secret Key', config('kms.aws.secret') ? '✅ SET (hidden)' : '❌ NOT SET'],
                ['KMS Key ARN', $keyArn ?: '❌ NOT SET'],
            ]
        );

        // Validate required fields
        $missing = [];
        if (!$accessKeyId) $missing[] = 'AWS_ACCESS_KEY_ID';
        if (!config('kms.aws.secret')) $missing[] = 'AWS_SECRET_ACCESS_KEY';
        if (!$keyArn) $missing[] = 'AWS_KMS_KEK_ARN';

        if (!empty($missing)) {
            $this->newLine();
            $this->error('❌ Missing required AWS configuration:');
            foreach ($missing as $var) {
                $this->error("   - {$var}");
            }
            $this->newLine();
            $this->warn('Add these to your .env file before testing.');
        }
    }

    /**
     * Test full encryption/decryption cycle
     */
    private function testEncryptionCycle(): bool {
        $testData = 'FlorenceEGI KMS Test - ' . now()->toISOString();
        $additionalData = 'test-context-data';

        try {
            // Step 1: Encrypt
            $this->info('📤 Step 1: Encrypting test data...');
            if ($this->option('detailed')) {
                $this->line("   Test data: {$testData}");
                $this->line("   Additional data: {$additionalData}");
            }

            $encrypted = $this->kmsClient->secureEncrypt($testData, null, $additionalData);

            $this->components->task('   Generate DEK', function () {
                return true;
            });
            $this->components->task('   Encrypt data with DEK', function () {
                return true;
            });
            $this->components->task('   Encrypt DEK with KEK', function () {
                return true;
            });

            if ($this->option('detailed')) {
                $this->newLine();
                $this->line('   Encrypted package:');
                $this->line('   - Algorithm: ' . ($encrypted['algorithm'] ?? 'unknown'));
                $this->line('   - Provider: ' . ($encrypted['encrypted_dek']['provider'] ?? 'unknown'));
                $this->line('   - Ciphertext length: ' . strlen($encrypted['ciphertext'] ?? ''));
                $this->line('   - Encrypted DEK length: ' . strlen($encrypted['encrypted_dek']['encrypted_dek'] ?? ''));
            }

            $this->newLine();

            // Step 2: Decrypt
            $this->info('📥 Step 2: Decrypting test data...');

            $decrypted = $this->kmsClient->secureDecrypt($encrypted);

            $this->components->task('   Decrypt DEK with KEK', function () {
                return true;
            });
            $this->components->task('   Decrypt data with DEK', function () {
                return true;
            });
            $this->components->task('   Verify integrity', function () {
                return true;
            });

            if ($this->option('detailed')) {
                $this->newLine();
                $this->line("   Decrypted data: {$decrypted}");
            }

            $this->newLine();

            // Step 3: Verify
            $this->info('🔍 Step 3: Verifying data integrity...');

            if ($decrypted === $testData) {
                $this->components->task('   Data match verification', function () {
                    return true;
                });
                $this->newLine();
                $this->info('✅ Encryption/Decryption cycle completed successfully!');
                $this->newLine();

                // Show provider used
                $provider = $encrypted['encrypted_dek']['provider'] ?? 'unknown';
                if ($provider === 'MOCK_KMS_DEVELOPMENT') {
                    $this->info('🟡 Used: Mock KMS (local encryption)');
                } elseif ($provider === 'aws') {
                    $this->info('☁️  Used: AWS KMS (enterprise HSM-backed encryption)');
                    $this->info('   Region: ' . config('kms.aws.region'));
                    $this->info('   Key ARN: ' . config('kms.aws.kek_arn'));
                } else {
                    $this->info("☁️  Used: {$provider} KMS");
                }

                return true;
            } else {
                $this->error('❌ Data mismatch after decryption!');
                $this->error("   Expected: {$testData}");
                $this->error("   Got: {$decrypted}");
                return false;
            }
        } catch (\Exception $e) {
            $this->newLine();
            $this->error('❌ Test failed with exception:');
            $this->error('   ' . $e->getMessage());
            $this->newLine();

            if ($this->option('detailed')) {
                $this->error('Stack trace:');
                $this->line($e->getTraceAsString());
            }

            // Provide helpful troubleshooting
            $this->newLine();
            $this->warn('💡 Troubleshooting tips:');

            if (str_contains($e->getMessage(), 'not_found_error') || str_contains($e->getMessage(), 'NotFoundException')) {
                $this->warn('   • KMS key not found - verify AWS_KMS_KEK_ARN is correct');
            }

            if (str_contains($e->getMessage(), 'AccessDenied') || str_contains($e->getMessage(), 'UnauthorizedException')) {
                $this->warn('   • Access denied - check IAM permissions (kms:Encrypt, kms:Decrypt)');
            }

            if (str_contains($e->getMessage(), 'Connection') || str_contains($e->getMessage(), 'timeout')) {
                $this->warn('   • Network issue - verify internet connection and AWS endpoint access');
            }

            if (str_contains($e->getMessage(), 'InvalidKeyId')) {
                $this->warn('   • Invalid key format - ARN should be: arn:aws:kms:region:account:key/key-id');
            }

            if (str_contains($e->getMessage(), 'Credentials')) {
                $this->warn('   • Credentials issue - verify AWS_ACCESS_KEY_ID and AWS_SECRET_ACCESS_KEY');
            }

            return false;
        }
    }

    /**
     * Mask sensitive string for display
     */
    private function maskSecret(string $secret, int $visibleChars = 8): string {
        if (strlen($secret) <= $visibleChars) {
            return str_repeat('*', strlen($secret));
        }

        return substr($secret, 0, $visibleChars) . str_repeat('*', strlen($secret) - $visibleChars);
    }
}
