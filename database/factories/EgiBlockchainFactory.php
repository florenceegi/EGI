<?php

/**
 * @package Database\Factories
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Blockchain Integration)
 * @date 2025-10-07
 * @purpose EgiBlockchain factory for testing blockchain data with existing EGIs
 */

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\EgiBlockchain;
use App\Models\Egi;
use App\Models\User;
use App\Models\Reservation;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EgiBlockchain>
 */
class EgiBlockchainFactory extends Factory {
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = EgiBlockchain::class;

    /**
     * Define the model's default state.
     * Creates blockchain data for EXISTING EGIs only.
     *
     * @return array<string, mixed>
     */
    public function definition(): array {
        $mintStatus = $this->faker->randomElement([
            'unminted',
            'minting_queued',
            'minting',
            'minted',
            'failed'
        ]);

        $isMinted = $mintStatus === 'minted';
        $hasFailed = $mintStatus === 'failed';

        return [
            // Core relationship - MUST link to existing EGI
            'egi_id' => Egi::factory(), // Will use existing EGIs in seeder

            // Blockchain data (only if minted)
            'asa_id' => $isMinted ? $this->faker->numberBetween(100000, 999999) : null,
            'anchor_hash' => $isMinted ? $this->faker->sha256 : null,
            'blockchain_tx_id' => $isMinted ? $this->faker->sha256 : null,
            'platform_wallet' => config('algorand.algorand.treasury_address', 'TREASURY_WALLET_ADDRESS'),

            // Payment data (FIAT only - MiCA-SAFE)
            'payment_method' => $this->faker->randomElement(['stripe', 'paypal', 'bank_transfer', 'mock']),
            'psp_provider' => $this->faker->randomElement(['stripe_test', 'paypal_sandbox', 'bank_test', 'mock_provider']),
            'payment_reference' => $this->faker->unique()->bothify('PAY-####-????'),
            'paid_amount' => $this->faker->randomFloat(2, 10, 1000),
            'paid_currency' => 'EUR',

            // Ownership data
            'ownership_type' => $this->faker->randomElement(['treasury', 'wallet']),
            'buyer_wallet' => $this->faker->boolean(30) ? $this->generateAlgorandAddress() : null,
            'buyer_user_id' => $this->faker->boolean(70) ? User::factory() : null,

            // Certificate data
            'certificate_uuid' => Str::uuid()->toString(),
            'certificate_path' => $isMinted ? 'certificates/' . $this->faker->uuid . '.pdf' : null,
            'verification_url' => $isMinted ? url('/verify/' . Str::uuid()) : null,

            // Reservation link (optional)
            'reservation_id' => $this->faker->boolean(40) ? Reservation::factory() : null,

            // Minting status
            'mint_status' => $mintStatus,
            'minted_at' => $isMinted ? $this->faker->dateTimeBetween('-1 month', 'now') : null,
            'mint_error' => $hasFailed ? $this->faker->sentence : null,

            // V2 crypto payments (future - mostly null)
            'merchant_psp_config' => null,
            'crypto_payment_reference' => null,
            'supports_crypto_payments' => false,
        ];
    }

    /**
     * State for unminted EGI blockchain records
     */
    public function unminted(): Factory {
        return $this->state(function (array $attributes) {
            return [
                'mint_status' => 'unminted',
                'asa_id' => null,
                'anchor_hash' => null,
                'blockchain_tx_id' => null,
                'minted_at' => null,
                'mint_error' => null,
                'certificate_path' => null,
                'verification_url' => null,
            ];
        });
    }

    /**
     * State for minted EGI blockchain records
     */
    public function minted(): Factory {
        return $this->state(function (array $attributes) {
            return [
                'mint_status' => 'minted',
                'asa_id' => $this->faker->numberBetween(100000, 999999),
                'anchor_hash' => $this->faker->sha256,
                'blockchain_tx_id' => $this->faker->sha256,
                'minted_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
                'mint_error' => null,
                'certificate_path' => 'certificates/' . $this->faker->uuid . '.pdf',
                'verification_url' => url('/verify/' . Str::uuid()),
            ];
        });
    }

    /**
     * State for failed minting attempts
     */
    public function failed(): Factory {
        return $this->state(function (array $attributes) {
            return [
                'mint_status' => 'failed',
                'asa_id' => null,
                'anchor_hash' => null,
                'blockchain_tx_id' => null,
                'minted_at' => null,
                'mint_error' => $this->faker->sentence,
                'certificate_path' => null,
                'verification_url' => null,
            ];
        });
    }

    /**
     * State for EGIs in treasury (not transferred to user)
     */
    public function inTreasury(): Factory {
        return $this->state(function (array $attributes) {
            return [
                'ownership_type' => 'treasury',
                'buyer_wallet' => null,
            ];
        });
    }

    /**
     * State for EGIs transferred to user wallet
     */
    public function inUserWallet(): Factory {
        return $this->state(function (array $attributes) {
            return [
                'ownership_type' => 'wallet',
                'buyer_wallet' => $this->generateAlgorandAddress(),
            ];
        });
    }

    /**
     * Generate a fake but valid-looking Algorand address
     */
    private function generateAlgorandAddress(): string {
        // Algorand addresses are 58 characters, base32 encoded
        return strtoupper($this->faker->bothify('??????????????????????????????????????????????????'))
            . strtoupper($this->faker->bothify('??????????????????'));
    }
}