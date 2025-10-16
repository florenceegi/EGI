<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;

/**
 * @Oracode Command: Show PA API Key
 * 🎯 Purpose: Display existing API key for PA entity (for recovery/setup)
 * 🛡️ Privacy: Requires confirmation, audit logged
 * 🧱 Core Logic: Decrypt and display existing API key
 *
 * @package App\Console\Commands
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - NATAN Batch Agent System)
 * @date 2025-10-16
 * @purpose Display existing API key for PA setup/recovery
 */
class ShowPaApiKey extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'natan:show-api-key
                            {email : PA user email address}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display existing NATAN API key for PA user';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $email = $this->argument('email');

        $this->info('🔐 N.A.T.A.N. API Key Viewer');
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        // 1. Find user
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("❌ User not found: $email");
            return self::FAILURE;
        }

        // 2. Check if key exists
        if (!$user->natan_api_key) {
            $this->warn("⚠️  No API key found for this user.");
            $this->info("Generate one with: php artisan natan:generate-api-key $email --show-plaintext");
            return self::FAILURE;
        }

        // 3. Display info
        $this->table(
            ['Field', 'Value'],
            [
                ['User', $user->name],
                ['Email', $user->email],
                ['Generated', $user->natan_api_key_generated_at?->toDateTimeString() ?? 'Unknown'],
                ['Last Used', $user->natan_api_key_last_used_at?->toDateTimeString() ?? 'Never'],
            ]
        );

        // 4. Confirmation
        $this->newLine();
        $this->warn("⚠️  You are about to display the API key in plaintext.");

        if (!$this->confirm('Continue?', false)) {
            $this->info("Operation cancelled.");
            return self::SUCCESS;
        }

        // 5. Decrypt and display
        try {
            $plainKey = Crypt::decryptString($user->natan_api_key);

            $this->newLine();
            $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
            $this->line("API Key (plaintext):");
            $this->line('');
            $this->info($plainKey);
            $this->line('');
            $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

            $this->newLine();
            $this->comment("Add this to agent config.json:");
            $this->line('"api_key": "' . $plainKey . '"');
        } catch (\Exception $e) {
            $this->error("❌ Failed to decrypt API key: " . $e->getMessage());
            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
