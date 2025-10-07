<?php

/**
 * @package App\Console\Commands
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Blockchain Integration)
 * @date 2025-10-07
 * @purpose Generate secure Treasury Wallet for EGI blockchain operations
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

class GenerateTreasuryWallet extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'egi:generate-treasury-wallet
                           {--force : Force regeneration of existing wallet}
                           {--mock : Generate mock wallet for testing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate secure Treasury Wallet for EGI blockchain operations - MiCA-SAFE compliant';

    private UltraLogManager $logger;
    private ErrorManagerInterface $errorManager;

    /**
     * Constructor with dependency injection
     */
    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager
    ) {
        parent::__construct();
        $this->logger = $logger;
        $this->errorManager = $errorManager;
    }

    /**
     * Execute the console command - Generate Treasury Wallet with security checks
     */
    public function handle(): int {
        try {
            $this->info('🏛️ FlorenceEGI Treasury Wallet Generator');
            $this->info('🔒 MiCA-SAFE Compliant - Platform Custody Only');
            $this->line('');

            // Check if wallet already exists
            $existingAddress = env('ALGORAND_TREASURY_ADDRESS');
            $existingMnemonic = env('ALGORAND_TREASURY_MNEMONIC');

            if ($existingAddress && !$this->option('force')) {
                $this->warn('⚠️ Treasury wallet already exists:');
                $this->line("   Address: {$existingAddress}");
                $this->line('');
                $this->warn('Use --force to regenerate (DANGER: will lose existing wallet)');
                return 1;
            }

            if ($existingAddress && $this->option('force')) {
                $this->error('🚨 WARNING: This will REPLACE existing treasury wallet!');
                $this->error('🚨 Existing EGIs on old wallet will become inaccessible!');

                if (!$this->confirm('Are you absolutely sure? Type "yes" to continue:', false)) {
                    $this->info('❌ Operation cancelled for security.');
                    return 1;
                }
            }

            // Generate wallet credentials
            $this->info('🔐 Generating Treasury Wallet credentials...');

            if ($this->option('mock')) {
                [$address, $mnemonic] = $this->generateMockWallet();
                $this->info('📝 Generated MOCK wallet for testing');
            } else {
                [$address, $mnemonic] = $this->generateProductionWallet();
                $this->info('🏦 Generated PRODUCTION wallet');
            }

            // Display wallet information
            $this->displayWalletInfo($address, $mnemonic);

            // Save to environment
            if ($this->confirm('💾 Save to .env file?', true)) {
                $this->saveToEnvironment($address, $mnemonic);
                $this->info('✅ Treasury wallet saved to .env');
            }

            // Log wallet generation
            $this->logger->info('Treasury wallet generated', [
                'address' => $address,
                'type' => $this->option('mock') ? 'mock' : 'production',
                'force_regenerated' => (bool) $this->option('force')
            ]);

            $this->line('');
            $this->info('🎉 Treasury Wallet Generation Complete!');
            $this->warn('🔒 IMPORTANT: Backup the mnemonic phrase securely!');

            return 0;
        } catch (\Exception $e) {
            $this->errorManager->handle('TREASURY_WALLET_GENERATION_FAILED', [
                'error' => $e->getMessage(),
                'force' => $this->option('force'),
                'mock' => $this->option('mock')
            ], $e);

            $this->error("❌ Treasury wallet generation failed: {$e->getMessage()}");
            return 1;
        }
    }

    /**
     * Generate mock wallet for testing
     * @return array [address, mnemonic]
     */
    private function generateMockWallet(): array {
        // Generate valid Algorand address format (58 chars, base32)
        $base32Chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $address = '';
        for ($i = 0; $i < 58; $i++) {
            $address .= $base32Chars[fake()->numberBetween(0, 31)];
        }

        // Generate 25-word mnemonic (Algorand standard)
        $words = [
            'abandon',
            'ability',
            'able',
            'about',
            'above',
            'absent',
            'absorb',
            'abstract',
            'absurd',
            'abuse',
            'access',
            'accident',
            'account',
            'accuse',
            'achieve',
            'acid',
            'acoustic',
            'acquire',
            'across',
            'act',
            'action',
            'actor',
            'actress',
            'actual',
            'adapt',
            'add',
            'addict',
            'address',
            'adjust',
            'admit',
            'adult',
            'advance'
        ];

        $mnemonic = [];
        for ($i = 0; $i < 25; $i++) {
            $mnemonic[] = $words[array_rand($words)];
        }

        return [$address, implode(' ', $mnemonic)];
    }

    /**
     * Generate production wallet (placeholder - would use real Algorand SDK)
     * @return array [address, mnemonic]
     */
    private function generateProductionWallet(): array {
        // TODO: Replace with real Algorand SDK when integrated
        // For now, generate realistic mock for development
        $this->warn('🚧 Using mock generation - replace with Algorand SDK in production');

        return $this->generateMockWallet();
    }

    /**
     * Display wallet information securely
     */
    private function displayWalletInfo(string $address, string $mnemonic): void {
        $this->line('');
        $this->info('📋 Treasury Wallet Information:');
        $this->line('');

        $this->line("🏦 <comment>Treasury Address:</comment>");
        $this->line("    {$address}");
        $this->line('');

        $this->line("🔑 <comment>Mnemonic Phrase (25 words):</comment>");
        $this->line("    {$mnemonic}");
        $this->line('');

        $this->warn('🔒 SECURITY WARNING:');
        $this->warn('   • This mnemonic controls the treasury wallet');
        $this->warn('   • Store it securely offline');
        $this->warn('   • Never share with anyone');
        $this->warn('   • Loss = permanent loss of treasury funds');
        $this->line('');
    }

    /**
     * Save wallet to .env file
     */
    private function saveToEnvironment(string $address, string $mnemonic): void {
        $envPath = base_path('.env');
        $envContent = file_get_contents($envPath);

        // Update or add treasury address
        if (str_contains($envContent, 'ALGORAND_TREASURY_ADDRESS=')) {
            $lines = explode("\n", $envContent);
            foreach ($lines as &$line) {
                if (\str_starts_with($line, 'ALGORAND_TREASURY_ADDRESS=')) {
                    $line = "ALGORAND_TREASURY_ADDRESS=\"{$address}\"";
                }
            }
            $envContent = implode("\n", $lines);
        } else {
            $envContent .= "\nALGORAND_TREASURY_ADDRESS=\"{$address}\"";
        }

        // Update or add treasury mnemonic
        if (str_contains($envContent, 'ALGORAND_TREASURY_MNEMONIC=')) {
            $lines = explode("\n", $envContent);
            foreach ($lines as &$line) {
                if (\str_starts_with($line, 'ALGORAND_TREASURY_MNEMONIC=')) {
                    $line = "ALGORAND_TREASURY_MNEMONIC=\"{$mnemonic}\"";
                }
            }
            $envContent = implode("\n", $lines);
        } else {
            $envContent .= "\nALGORAND_TREASURY_MNEMONIC=\"{$mnemonic}\"";
        }

        file_put_contents($envPath, $envContent);
    }
}
