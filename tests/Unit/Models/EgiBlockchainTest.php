<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\EgiBlockchain;
use App\Models\Egi;
use App\Models\User;
use App\Models\Reservation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @package Tests\Unit\Models
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Blockchain Integration)
 * @date 2025-10-07
 * @purpose Unit tests for EgiBlockchain model - relationships, scopes, accessors, validation
 */
class EgiBlockchainTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test model can be instantiated
     */
    public function test_model_instantiation(): void
    {
        $egiBlockchain = new EgiBlockchain();
        
        $this->assertInstanceOf(EgiBlockchain::class, $egiBlockchain);
        $this->assertEquals('egi_blockchain', $egiBlockchain->getTable());
    }

    /**
     * Test fillable attributes are correctly defined
     */
    public function test_fillable_attributes(): void
    {
        $fillable = [
            'egi_id', 'asa_id', 'anchor_hash', 'blockchain_tx_id', 'platform_wallet',
            'payment_method', 'psp_provider', 'payment_reference', 'paid_amount', 'paid_currency',
            'ownership_type', 'buyer_wallet', 'buyer_user_id',
            'certificate_uuid', 'certificate_path', 'verification_url',
            'reservation_id', 'mint_status', 'minted_at', 'mint_error',
            'merchant_psp_config', 'crypto_payment_reference', 'supports_crypto_payments'
        ];

        $egiBlockchain = new EgiBlockchain();
        
        $this->assertEquals($fillable, $egiBlockchain->getFillable());
    }

    /**
     * Test casts are correctly applied
     */
    public function test_casts(): void
    {
        $egiBlockchain = new EgiBlockchain([
            'paid_amount' => '99.99',
            'supports_crypto_payments' => '1',
            'minted_at' => '2025-10-07 10:00:00',
            'merchant_psp_config' => '{"key": "value"}'
        ]);

        $this->assertIsFloat($egiBlockchain->paid_amount);
        $this->assertEquals(99.99, $egiBlockchain->paid_amount);
        $this->assertIsBool($egiBlockchain->supports_crypto_payments);
        $this->assertTrue($egiBlockchain->supports_crypto_payments);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $egiBlockchain->minted_at);
        $this->assertIsArray($egiBlockchain->merchant_psp_config);
    }

    /**
     * Test hidden attributes
     */
    public function test_hidden_attributes(): void
    {
        $egiBlockchain = new EgiBlockchain();
        
        $this->assertEquals(['mint_error', 'merchant_psp_config'], $egiBlockchain->getHidden());
    }

    /**
     * Test certificate UUID auto-generation on creation
     */
    public function test_certificate_uuid_auto_generated(): void
    {
        $egi = Egi::factory()->create();
        
        $egiBlockchain = EgiBlockchain::create([
            'egi_id' => $egi->id,
            'mint_status' => 'unminted',
            'ownership_type' => 'treasury'
        ]);

        $this->assertNotNull($egiBlockchain->certificate_uuid);
        $this->assertMatchesRegularExpression('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/', $egiBlockchain->certificate_uuid);
    }

    /**
     * Test certificate UUID is not overwritten if provided
     */
    public function test_certificate_uuid_not_overwritten(): void
    {
        $egi = Egi::factory()->create();
        $customUuid = '12345678-1234-1234-1234-123456789012';
        
        $egiBlockchain = EgiBlockchain::create([
            'egi_id' => $egi->id,
            'certificate_uuid' => $customUuid,
            'mint_status' => 'unminted',
            'ownership_type' => 'treasury'
        ]);

        $this->assertEquals($customUuid, $egiBlockchain->certificate_uuid);
    }

    /**
     * Test egi relationship
     */
    public function test_egi_relationship(): void
    {
        $egiBlockchain = new EgiBlockchain();
        $relation = $egiBlockchain->egi();

        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertEquals('egi_id', $relation->getForeignKeyName());
        $this->assertEquals('id', $relation->getOwnerKeyName());
    }

    /**
     * Test buyer relationship
     */
    public function test_buyer_relationship(): void
    {
        $egiBlockchain = new EgiBlockchain();
        $relation = $egiBlockchain->buyer();

        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertEquals('buyer_user_id', $relation->getForeignKeyName());
        $this->assertEquals('id', $relation->getOwnerKeyName());
    }

    /**
     * Test reservation relationship
     */
    public function test_reservation_relationship(): void
    {
        $egiBlockchain = new EgiBlockchain();
        $relation = $egiBlockchain->reservation();

        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertEquals('reservation_id', $relation->getForeignKeyName());
        $this->assertEquals('id', $relation->getOwnerKeyName());
    }

    /**
     * Test minted scope
     */
    public function test_minted_scope(): void
    {
        $egi = Egi::factory()->create();
        
        // Create minted record
        EgiBlockchain::create([
            'egi_id' => $egi->id,
            'mint_status' => 'minted',
            'ownership_type' => 'treasury'
        ]);
        
        // Create unminted record
        EgiBlockchain::create([
            'egi_id' => $egi->id,
            'mint_status' => 'unminted',
            'ownership_type' => 'treasury'
        ]);

        $mintedRecords = EgiBlockchain::minted()->count();
        
        $this->assertEquals(1, $mintedRecords);
    }

    /**
     * Test pending scope
     */
    public function test_pending_scope(): void
    {
        $egi = Egi::factory()->create();
        
        // Create pending records
        EgiBlockchain::create(['egi_id' => $egi->id, 'mint_status' => 'unminted', 'ownership_type' => 'treasury']);
        EgiBlockchain::create(['egi_id' => $egi->id, 'mint_status' => 'minting_queued', 'ownership_type' => 'treasury']);
        EgiBlockchain::create(['egi_id' => $egi->id, 'mint_status' => 'minting', 'ownership_type' => 'treasury']);
        
        // Create non-pending record
        EgiBlockchain::create(['egi_id' => $egi->id, 'mint_status' => 'minted', 'ownership_type' => 'treasury']);

        $pendingRecords = EgiBlockchain::pending()->count();
        
        $this->assertEquals(3, $pendingRecords);
    }

    /**
     * Test failed scope
     */
    public function test_failed_scope(): void
    {
        $egi = Egi::factory()->create();
        
        // Create failed record
        EgiBlockchain::create([
            'egi_id' => $egi->id,
            'mint_status' => 'failed',
            'ownership_type' => 'treasury'
        ]);
        
        // Create non-failed record
        EgiBlockchain::create([
            'egi_id' => $egi->id,
            'mint_status' => 'minted',
            'ownership_type' => 'treasury'
        ]);

        $failedRecords = EgiBlockchain::failed()->count();
        
        $this->assertEquals(1, $failedRecords);
    }

    /**
     * Test byOwnership scope
     */
    public function test_by_ownership_scope(): void
    {
        $egi = Egi::factory()->create();
        
        // Create treasury record
        EgiBlockchain::create([
            'egi_id' => $egi->id,
            'ownership_type' => 'treasury',
            'mint_status' => 'unminted'
        ]);
        
        // Create wallet record
        EgiBlockchain::create([
            'egi_id' => $egi->id,
            'ownership_type' => 'wallet',
            'mint_status' => 'unminted'
        ]);

        $treasuryRecords = EgiBlockchain::byOwnership('treasury')->count();
        $walletRecords = EgiBlockchain::byOwnership('wallet')->count();
        
        $this->assertEquals(1, $treasuryRecords);
        $this->assertEquals(1, $walletRecords);
    }

    /**
     * Test byPaymentMethod scope
     */
    public function test_by_payment_method_scope(): void
    {
        $egi = Egi::factory()->create();
        
        // Create stripe payment record
        EgiBlockchain::create([
            'egi_id' => $egi->id,
            'payment_method' => 'stripe',
            'mint_status' => 'unminted',
            'ownership_type' => 'treasury'
        ]);
        
        // Create paypal payment record
        EgiBlockchain::create([
            'egi_id' => $egi->id,
            'payment_method' => 'paypal',
            'mint_status' => 'unminted',
            'ownership_type' => 'treasury'
        ]);

        $stripeRecords = EgiBlockchain::byPaymentMethod('stripe')->count();
        $paypalRecords = EgiBlockchain::byPaymentMethod('paypal')->count();
        
        $this->assertEquals(1, $stripeRecords);
        $this->assertEquals(1, $paypalRecords);
    }

    /**
     * Test formatted amount accessor
     */
    public function test_formatted_amount_accessor(): void
    {
        $egiBlockchain = new EgiBlockchain([
            'paid_amount' => 99.99,
            'paid_currency' => 'EUR'
        ]);

        $this->assertEquals('99.99 EUR', $egiBlockchain->formatted_amount);

        // Test null amount
        $egiBlockchain->paid_amount = null;
        $this->assertEquals('N/A', $egiBlockchain->formatted_amount);
    }

    /**
     * Test mint status label accessor
     */
    public function test_mint_status_label_accessor(): void
    {
        $testCases = [
            'unminted' => 'Non Mintato',
            'minting_queued' => 'In Coda',
            'minting' => 'Minting in Corso',
            'minted' => 'Mintato',
            'failed' => 'Fallito',
            'unknown' => 'Sconosciuto'
        ];

        foreach ($testCases as $status => $expectedLabel) {
            $egiBlockchain = new EgiBlockchain(['mint_status' => $status]);
            $this->assertEquals($expectedLabel, $egiBlockchain->mint_status_label);
        }
    }

    /**
     * Test ownership type label accessor
     */
    public function test_ownership_type_label_accessor(): void
    {
        $testCases = [
            'treasury' => 'Deposito Piattaforma',
            'wallet' => 'Wallet Utente',
            'unknown' => 'Sconosciuto'
        ];

        foreach ($testCases as $type => $expectedLabel) {
            $egiBlockchain = new EgiBlockchain(['ownership_type' => $type]);
            $this->assertEquals($expectedLabel, $egiBlockchain->ownership_type_label);
        }
    }

    /**
     * Test isMinted method
     */
    public function test_is_minted_method(): void
    {
        // Test minted with ASA ID
        $egiBlockchain = new EgiBlockchain([
            'mint_status' => 'minted',
            'asa_id' => '123456789'
        ]);
        $this->assertTrue($egiBlockchain->isMinted());

        // Test minted without ASA ID
        $egiBlockchain = new EgiBlockchain([
            'mint_status' => 'minted',
            'asa_id' => null
        ]);
        $this->assertFalse($egiBlockchain->isMinted());

        // Test not minted
        $egiBlockchain = new EgiBlockchain([
            'mint_status' => 'unminted',
            'asa_id' => '123456789'
        ]);
        $this->assertFalse($egiBlockchain->isMinted());
    }

    /**
     * Test isPending method
     */
    public function test_is_pending_method(): void
    {
        $pendingStatuses = ['unminted', 'minting_queued', 'minting'];
        
        foreach ($pendingStatuses as $status) {
            $egiBlockchain = new EgiBlockchain(['mint_status' => $status]);
            $this->assertTrue($egiBlockchain->isPending(), "Status {$status} should be pending");
        }

        $nonPendingStatuses = ['minted', 'failed'];
        
        foreach ($nonPendingStatuses as $status) {
            $egiBlockchain = new EgiBlockchain(['mint_status' => $status]);
            $this->assertFalse($egiBlockchain->isPending(), "Status {$status} should not be pending");
        }
    }

    /**
     * Test hasFailed method
     */
    public function test_has_failed_method(): void
    {
        $egiBlockchain = new EgiBlockchain(['mint_status' => 'failed']);
        $this->assertTrue($egiBlockchain->hasFailed());

        $egiBlockchain = new EgiBlockchain(['mint_status' => 'minted']);
        $this->assertFalse($egiBlockchain->hasFailed());
    }

    /**
     * Test hasCertificate method
     */
    public function test_has_certificate_method(): void
    {
        // Test with both path and URL
        $egiBlockchain = new EgiBlockchain([
            'certificate_path' => '/certificates/test.pdf',
            'verification_url' => 'https://example.com/verify'
        ]);
        $this->assertTrue($egiBlockchain->hasCertificate());

        // Test with missing path
        $egiBlockchain = new EgiBlockchain([
            'certificate_path' => null,
            'verification_url' => 'https://example.com/verify'
        ]);
        $this->assertFalse($egiBlockchain->hasCertificate());

        // Test with missing URL
        $egiBlockchain = new EgiBlockchain([
            'certificate_path' => '/certificates/test.pdf',
            'verification_url' => null
        ]);
        $this->assertFalse($egiBlockchain->hasCertificate());
    }

    /**
     * Test getVerificationUrl method
     */
    public function test_get_verification_url_method(): void
    {
        $url = 'https://example.com/verify';
        $egiBlockchain = new EgiBlockchain(['verification_url' => $url]);
        
        $this->assertEquals($url, $egiBlockchain->getVerificationUrl());

        $egiBlockchain = new EgiBlockchain(['verification_url' => null]);
        $this->assertNull($egiBlockchain->getVerificationUrl());
    }

    /**
     * Test isOwnedByUser method
     */
    public function test_is_owned_by_user_method(): void
    {
        // Test user ownership with wallet
        $egiBlockchain = new EgiBlockchain([
            'ownership_type' => 'wallet',
            'buyer_wallet' => 'ALGO123...XYZ'
        ]);
        $this->assertTrue($egiBlockchain->isOwnedByUser());

        // Test user ownership without wallet
        $egiBlockchain = new EgiBlockchain([
            'ownership_type' => 'wallet',
            'buyer_wallet' => null
        ]);
        $this->assertFalse($egiBlockchain->isOwnedByUser());

        // Test treasury ownership
        $egiBlockchain = new EgiBlockchain(['ownership_type' => 'treasury']);
        $this->assertFalse($egiBlockchain->isOwnedByUser());
    }

    /**
     * Test isInTreasury method
     */
    public function test_is_in_treasury_method(): void
    {
        $egiBlockchain = new EgiBlockchain(['ownership_type' => 'treasury']);
        $this->assertTrue($egiBlockchain->isInTreasury());

        $egiBlockchain = new EgiBlockchain(['ownership_type' => 'wallet']);
        $this->assertFalse($egiBlockchain->isInTreasury());
    }

    /**
     * Test blockchain explorer URL generation
     */
    public function test_blockchain_explorer_url(): void
    {
        // Test mainnet
        config(['algorand.network' => 'mainnet']);
        $egiBlockchain = new EgiBlockchain([
            'mint_status' => 'minted',
            'asa_id' => '123456789'
        ]);
        $this->assertEquals('https://explorer.perawallet.app/asset/123456789', $egiBlockchain->getBlockchainExplorerUrl());

        // Test testnet
        config(['algorand.network' => 'testnet']);
        $this->assertEquals('https://testnet.explorer.perawallet.app/asset/123456789', $egiBlockchain->getBlockchainExplorerUrl());

        // Test sandbox (no public explorer)
        config(['algorand.network' => 'sandbox']);
        $this->assertNull($egiBlockchain->getBlockchainExplorerUrl());

        // Test not minted
        $egiBlockchain = new EgiBlockchain([
            'mint_status' => 'unminted',
            'asa_id' => null
        ]);
        $this->assertNull($egiBlockchain->getBlockchainExplorerUrl());
    }

    /**
     * Test model relationships work with real data
     */
    public function test_model_relationships_with_data(): void
    {
        $user = User::factory()->create();
        $egi = Egi::factory()->create();
        
        $egiBlockchain = EgiBlockchain::create([
            'egi_id' => $egi->id,
            'buyer_user_id' => $user->id,
            'mint_status' => 'minted',
            'ownership_type' => 'wallet'
        ]);

        // Test relationships are loaded correctly
        $this->assertEquals($egi->id, $egiBlockchain->egi->id);
        $this->assertEquals($user->id, $egiBlockchain->buyer->id);
        $this->assertEquals($egi->title, $egiBlockchain->egi->title);
        $this->assertEquals($user->name, $egiBlockchain->buyer->name);
    }
}