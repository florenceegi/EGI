<?php

/**
 * @package Database\Seeders
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Blockchain Integration)
 * @date 2025-10-07
 * @purpose Seed blockchain data for existing EGIs with mixed states for testing
 */

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Egi;
use App\Models\EgiBlockchain;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class EgiBlockchainSeeder extends Seeder {
    /**
     * Run the database seeds.
     * Creates blockchain records for EXISTING EGIs only.
     * Mixed states for comprehensive testing.
     */
    public function run(): void {
        $this->command->info('🔗 Starting EGI Blockchain seeding...');

        // Get existing EGIs from database
        $existingEgis = Egi::whereDoesntHave('blockchain')->get();

        if ($existingEgis->isEmpty()) {
            $this->command->warn('⚠️ No EGIs found without blockchain data. Skipping seeding.');
            return;
        }

        $this->command->info("📊 Found {$existingEgis->count()} EGIs without blockchain data");

        // Get some users for buyer_user_id
        $users = User::limit(10)->get();
        if ($users->isEmpty()) {
            $this->command->warn('⚠️ No users found. Creating blockchain records without buyer_user_id.');
        }

        $stats = [
            'unminted' => 0,
            'minting_queued' => 0,
            'minting' => 0,
            'minted' => 0,
            'failed' => 0,
        ];

        DB::transaction(function () use ($existingEgis, $users, &$stats) {
            foreach ($existingEgis as $egi) {
                // Determine mint status with realistic distribution
                $mintStatus = $this->getMintStatus();
                $stats[$mintStatus]++;

                // Create blockchain record based on status
                $blockchainData = $this->generateBlockchainData($egi, $mintStatus, $users);

                EgiBlockchain::create($blockchainData);

                if ($stats['minted'] % 10 === 0) {
                    $this->command->info("✅ Processed {$stats['minted']} EGIs...");
                }
            }
        });

        $this->command->info('🎉 EGI Blockchain seeding completed!');
        $this->command->table(
            ['Status', 'Count', 'Percentage'],
            collect($stats)->map(function ($count, $status) use ($existingEgis) {
                $percentage = round(($count / $existingEgis->count()) * 100, 1);
                return [$status, $count, $percentage . '%'];
            })->toArray()
        );
    }

    /**
     * Get mint status with realistic distribution
     */
    private function getMintStatus(): string {
        $statuses = [
            'minted' => 50,        // 50% - Most EGIs are successfully minted
            'unminted' => 20,      // 20% - Not yet minted
            'minting_queued' => 10, // 10% - Queued for minting
            'failed' => 10,        // 10% - Failed minting attempts
            'minting' => 10,       // 10% - Currently minting
        ];

        return fake()->randomElement(
            array_merge(
                array_fill(0, $statuses['minted'], 'minted'),
                array_fill(0, $statuses['unminted'], 'unminted'),
                array_fill(0, $statuses['minting_queued'], 'minting_queued'),
                array_fill(0, $statuses['failed'], 'failed'),
                array_fill(0, $statuses['minting'], 'minting')
            )
        );
    }

    /**
     * Generate blockchain data based on EGI and mint status
     */
    private function generateBlockchainData(Egi $egi, string $mintStatus, $users): array {
        $isMinted = $mintStatus === 'minted';
        $hasFailed = $mintStatus === 'failed';
        $randomUser = $users->isNotEmpty() ? $users->random() : null;

        return [
            'egi_id' => $egi->id,

            // Blockchain data (only if minted)
            'asa_id' => $isMinted ? fake()->numberBetween(100000, 999999) : null,
            'anchor_hash' => $isMinted ? fake()->sha256 : null,
            'blockchain_tx_id' => $isMinted ? fake()->sha256 : null,
            'platform_wallet' => config('algorand.algorand.treasury_address', 'TREASURY_WALLET_ADDRESS'),

            // Payment data (FIAT only - MiCA-SAFE)
            'payment_method' => fake()->randomElement(['stripe', 'paypal', 'bank_transfer', 'mock']),
            'psp_provider' => fake()->randomElement(['stripe_test', 'paypal_sandbox', 'bank_test', 'mock_provider']),
            'payment_reference' => fake()->unique()->bothify('PAY-####-????'),
            'paid_amount' => fake()->randomFloat(2, 10, 1000),
            'paid_currency' => 'EUR',

            // Ownership data
            'ownership_type' => fake()->randomElement(['treasury', 'wallet']),
            'buyer_wallet' => fake()->boolean(30) ? $this->generateAlgorandAddress() : null,
            'buyer_user_id' => $randomUser?->id,

            // Certificate data
            'certificate_uuid' => \Illuminate\Support\Str::uuid()->toString(),
            'certificate_path' => $isMinted ? 'certificates/' . fake()->uuid . '.pdf' : null,
            'verification_url' => $isMinted ? url('/verify/' . \Illuminate\Support\Str::uuid()) : null,

            // Reservation link (optional - some EGIs come from reservations)
            'reservation_id' => null, // Could be enhanced to link to actual reservations

            // Minting status
            'mint_status' => $mintStatus,
            'minted_at' => $isMinted ? fake()->dateTimeBetween('-1 month', 'now') : null,
            'mint_error' => $hasFailed ? fake()->sentence : null,

            // V2 crypto payments (future - mostly null for now)
            'merchant_psp_config' => null,
            'crypto_payment_reference' => null,
            'supports_crypto_payments' => false,

            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Generate a fake but valid-looking Algorand address
     */
    private function generateAlgorandAddress(): string {
        // Algorand addresses are 58 characters, base32 encoded
        return strtoupper(fake()->bothify('??????????????????????????????????????????????????'))
            . strtoupper(fake()->bothify('??????????????????'));
    }
}
