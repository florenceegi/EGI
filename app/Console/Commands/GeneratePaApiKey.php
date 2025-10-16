<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * @Oracode Command: Generate PA API Key
 * 🎯 Purpose: Generate secure API key for PA entity NATAN agent authentication
 * 🛡️ Privacy: Keys are encrypted at rest, audit logged
 * 🧱 Core Logic: Generate cryptographically secure key for PA user
 *
 * @package App\Console\Commands
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - NATAN Batch Agent System)
 * @date 2025-10-16
 * @purpose Generate API authentication keys for PA entities
 */
class GeneratePaApiKey extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'natan:generate-api-key
                            {email : PA user email address}
                            {--force : Force regenerate even if key exists}
                            {--show-plaintext : Display plaintext key (use with caution)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate NATAN API key for PA entity user';

    /**
     * Ultra Log Manager instance.
     *
     * @var UltraLogManager
     */
    protected UltraLogManager $logger;

    /**
     * Constructor with dependency injection.
     *
     * @param UltraLogManager $logger
     */
    public function __construct(UltraLogManager $logger)
    {
        parent::__construct();
        $this->logger = $logger;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $email = $this->argument('email');
        $force = $this->option('force');
        $showPlaintext = $this->option('show-plaintext');

        $this->info('🔐 N.A.T.A.N. API Key Generator');
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        // 1. Find user
        $this->info("Looking up user: $email");

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("❌ User not found: $email");
            return self::FAILURE;
        }

        // 2. Check if user is PA entity
        if (!$user->hasRole('pa_entity')) {
            $this->error("❌ User is not a PA entity (role required: pa_entity)");
            $this->info("Current roles: " . $user->roles->pluck('name')->implode(', '));
            return self::FAILURE;
        }

        $this->info("✅ User found: {$user->name} (ID: {$user->id})");

        // 3. Check existing key
        if ($user->natan_api_key && !$force) {
            $this->warn("⚠️  API key already exists for this user!");
            $this->info("Generated at: " . $user->natan_api_key_generated_at);
            $this->info("Last used: " . ($user->natan_api_key_last_used_at ?? 'Never'));

            if (!$this->confirm('Do you want to regenerate? (existing key will be revoked)')) {
                $this->info("Operation cancelled.");
                return self::SUCCESS;
            }
        }

        // 4. Generate secure API key
        $this->info("Generating API key...");

        $plainKey = $this->generateSecureKey();
        $encryptedKey = Crypt::encryptString($plainKey);

        // 5. Save to database
        $user->update([
            'natan_api_key' => $encryptedKey,
            'natan_api_key_generated_at' => now(),
            'natan_api_key_last_used_at' => null, // Reset usage
        ]);

        // 6. Audit log
        $this->logger->info('NATAN_API_KEY_GENERATED', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'generated_by' => 'console_command',
            'force_regenerate' => $force,
        ]);

        // 7. Display result
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info("✅ API Key generated successfully!");
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        $this->table(
            ['Field', 'Value'],
            [
                ['User', $user->name],
                ['Email', $user->email],
                ['User ID', $user->id],
                ['Generated', now()->toDateTimeString()],
            ]
        );

        // 8. Show plaintext key (with warning)
        if ($showPlaintext) {
            $this->newLine();
            $this->warn("⚠️  SECURITY WARNING: Copy this key NOW! It won't be shown again.");
            $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
            $this->line("API Key (plaintext):");
            $this->line('');
            $this->info($plainKey);
            $this->line('');
            $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
            $this->newLine();
            $this->comment("Add this to agent config.json:");
            $this->line('"api_key": "' . $plainKey . '"');
        } else {
            $this->newLine();
            $this->comment("💡 Tip: Use --show-plaintext to display the key (store it securely!)");
            $this->newLine();
            $this->info("To display the key, run:");
            $this->line("php artisan natan:show-api-key $email");
        }

        $this->newLine();
        $this->info("Next steps:");
        $this->line("1. Copy the API key to agent config.json");
        $this->line("2. Test agent: php natan-agent.php");
        $this->line("3. Monitor usage: php artisan natan:api-key-status $email");

        return self::SUCCESS;
    }

    /**
     * Generate cryptographically secure API key.
     *
     * Format: sk_pa_{random_60_chars}
     *
     * @return string
     */
    private function generateSecureKey(): string
    {
        $randomBytes = random_bytes(30); // 30 bytes = 60 hex chars
        $randomHex = bin2hex($randomBytes);

        return 'sk_pa_' . $randomHex;
    }
}
