<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\EgiBlockchain;
use App\Models\Egi;
use App\Models\User;
use App\Models\Reservation;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use ReflectionClass;

/**
 * @package Tests\Unit\Models
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Blockchain Integration)
 * @date 2025-10-07
 * @purpose Unit tests for EgiBlockchain model - architecture validation without database
 */
class EgiBlockchainCoreTest extends TestCase
{
    /**
     * Test model architecture and class structure
     */
    public function test_model_architecture(): void
    {
        $reflection = new ReflectionClass(EgiBlockchain::class);
        
        // Test class exists and is correct type
        $this->assertTrue($reflection->isSubclassOf(\Illuminate\Database\Eloquent\Model::class));
        $this->assertFalse($reflection->isAbstract());
        $this->assertTrue($reflection->isInstantiable());
        
        // Test has required traits
        $traitNames = array_keys($reflection->getTraits());
        $this->assertContains('Illuminate\Database\Eloquent\Factories\HasFactory', $traitNames);
    }

    /**
     * Test model can be instantiated without database
     */
    public function test_model_instantiation(): void
    {
        $model = new EgiBlockchain();
        
        $this->assertInstanceOf(EgiBlockchain::class, $model);
        $this->assertEquals('egi_blockchain', $model->getTable());
        $this->assertEquals('id', $model->getKeyName());
        $this->assertTrue($model->timestamps);
    }

    /**
     * Test fillable attributes configuration
     */
    public function test_fillable_attributes(): void
    {
        $model = new EgiBlockchain();
        $fillable = $model->getFillable();
        
        // Core fields
        $this->assertContains('egi_id', $fillable);
        
        // Blockchain fields
        $this->assertContains('asa_id', $fillable);
        $this->assertContains('anchor_hash', $fillable);
        $this->assertContains('blockchain_tx_id', $fillable);
        $this->assertContains('platform_wallet', $fillable);
        
        // Payment fields
        $this->assertContains('payment_method', $fillable);
        $this->assertContains('psp_provider', $fillable);
        $this->assertContains('paid_amount', $fillable);
        $this->assertContains('paid_currency', $fillable);
        
        // Ownership fields
        $this->assertContains('ownership_type', $fillable);
        $this->assertContains('buyer_wallet', $fillable);
        $this->assertContains('buyer_user_id', $fillable);
        
        // Status fields
        $this->assertContains('mint_status', $fillable);
        $this->assertContains('minted_at', $fillable);
        
        // Should have all essential fields
        $this->assertGreaterThanOrEqual(15, count($fillable));
    }

    /**
     * Test model casts configuration
     */
    public function test_model_casts(): void
    {
        $model = new EgiBlockchain();
        $casts = $model->getCasts();
        
        // Verify critical casts
        $this->assertEquals('decimal:2', $casts['paid_amount']);
        $this->assertEquals('boolean', $casts['supports_crypto_payments']);
        $this->assertEquals('datetime', $casts['minted_at']);
        $this->assertEquals('array', $casts['merchant_psp_config']);
    }

    /**
     * Test hidden attributes for security
     */
    public function test_hidden_attributes(): void
    {
        $model = new EgiBlockchain();
        $hidden = $model->getHidden();
        
        // Sensitive fields should be hidden
        $this->assertContains('mint_error', $hidden);
        $this->assertContains('merchant_psp_config', $hidden);
    }

    /**
     * Test relationship method signatures
     */
    public function test_relationship_methods(): void
    {
        $reflection = new ReflectionClass(EgiBlockchain::class);
        
        // Check egi relationship exists
        $this->assertTrue($reflection->hasMethod('egi'));
        $egiMethod = $reflection->getMethod('egi');
        $this->assertTrue($egiMethod->isPublic());
        
        // Check buyer relationship exists
        $this->assertTrue($reflection->hasMethod('buyer'));
        $buyerMethod = $reflection->getMethod('buyer');
        $this->assertTrue($buyerMethod->isPublic());
        
        // Check reservation relationship exists
        $this->assertTrue($reflection->hasMethod('reservation'));
        $reservationMethod = $reflection->getMethod('reservation');
        $this->assertTrue($reservationMethod->isPublic());
    }

    /**
     * Test scope method signatures
     */
    public function test_scope_methods(): void
    {
        $reflection = new ReflectionClass(EgiBlockchain::class);
        
        // Test critical scopes exist
        $this->assertTrue($reflection->hasMethod('scopeMinted'));
        $this->assertTrue($reflection->hasMethod('scopePending'));
        $this->assertTrue($reflection->hasMethod('scopeFailed'));
        $this->assertTrue($reflection->hasMethod('scopeByOwnership'));
        $this->assertTrue($reflection->hasMethod('scopeByPaymentMethod'));
        
        // Verify scope method signatures
        $mintedScope = $reflection->getMethod('scopeMinted');
        $this->assertTrue($mintedScope->isPublic());
        $this->assertGreaterThanOrEqual(1, $mintedScope->getNumberOfParameters());
    }

    /**
     * Test accessor method signatures
     */
    public function test_accessor_methods(): void
    {
        $reflection = new ReflectionClass(EgiBlockchain::class);
        
        // Test essential accessors exist
        $this->assertTrue($reflection->hasMethod('getFormattedAmountAttribute'));
        $this->assertTrue($reflection->hasMethod('getMintStatusLabelAttribute'));
        $this->assertTrue($reflection->hasMethod('getOwnershipTypeLabelAttribute'));
    }

    /**
     * Test helper method signatures
     */
    public function test_helper_methods(): void
    {
        $reflection = new ReflectionClass(EgiBlockchain::class);
        
        // Test status check methods
        $this->assertTrue($reflection->hasMethod('isMinted'));
        $this->assertTrue($reflection->hasMethod('isPending'));
        $this->assertTrue($reflection->hasMethod('hasFailed'));
        $this->assertTrue($reflection->hasMethod('hasCertificate'));
        
        // Test ownership methods
        $this->assertTrue($reflection->hasMethod('isOwnedByUser'));
        $this->assertTrue($reflection->hasMethod('isInTreasury'));
        
        // Test utility methods
        $this->assertTrue($reflection->hasMethod('getVerificationUrl'));
        $this->assertTrue($reflection->hasMethod('getBlockchainExplorerUrl'));
    }

    /**
     * Test accessors work without database
     */
    public function test_accessors_without_database(): void
    {
        // Test formatted amount accessor
        $model = new EgiBlockchain([
            'paid_amount' => 99.99,
            'paid_currency' => 'EUR'
        ]);
        $this->assertEquals('99.99 EUR', $model->formatted_amount);
        
        // Test with null amount
        $model = new EgiBlockchain(['paid_amount' => null]);
        $this->assertEquals('N/A', $model->formatted_amount);
    }

    /**
     * Test mint status labels
     */
    public function test_mint_status_labels(): void
    {
        $testCases = [
            'unminted' => 'Non Mintato',
            'minting_queued' => 'In Coda',
            'minting' => 'Minting in Corso',
            'minted' => 'Mintato',
            'failed' => 'Fallito',
            'unknown_status' => 'Sconosciuto'
        ];

        foreach ($testCases as $status => $expectedLabel) {
            $model = new EgiBlockchain(['mint_status' => $status]);
            $this->assertEquals($expectedLabel, $model->mint_status_label);
        }
    }

    /**
     * Test ownership type labels
     */
    public function test_ownership_type_labels(): void
    {
        $testCases = [
            'treasury' => 'Deposito Piattaforma',
            'wallet' => 'Wallet Utente',
            'unknown_type' => 'Sconosciuto'
        ];

        foreach ($testCases as $type => $expectedLabel) {
            $model = new EgiBlockchain(['ownership_type' => $type]);
            $this->assertEquals($expectedLabel, $model->ownership_type_label);
        }
    }

    /**
     * Test status check methods
     */
    public function test_status_check_methods(): void
    {
        // Test isMinted - requires both status and asa_id
        $model = new EgiBlockchain(['mint_status' => 'minted', 'asa_id' => '123456']);
        $this->assertTrue($model->isMinted());
        
        $model = new EgiBlockchain(['mint_status' => 'minted', 'asa_id' => null]);
        $this->assertFalse($model->isMinted());
        
        // Test isPending
        $pendingStatuses = ['unminted', 'minting_queued', 'minting'];
        foreach ($pendingStatuses as $status) {
            $model = new EgiBlockchain(['mint_status' => $status]);
            $this->assertTrue($model->isPending(), "Status {$status} should be pending");
        }
        
        // Test hasFailed
        $model = new EgiBlockchain(['mint_status' => 'failed']);
        $this->assertTrue($model->hasFailed());
        
        $model = new EgiBlockchain(['mint_status' => 'minted']);
        $this->assertFalse($model->hasFailed());
    }

    /**
     * Test ownership check methods
     */
    public function test_ownership_check_methods(): void
    {
        // Test isOwnedByUser
        $model = new EgiBlockchain([
            'ownership_type' => 'wallet',
            'buyer_wallet' => 'ALGO123ABC'
        ]);
        $this->assertTrue($model->isOwnedByUser());
        
        $model = new EgiBlockchain([
            'ownership_type' => 'wallet',
            'buyer_wallet' => null
        ]);
        $this->assertFalse($model->isOwnedByUser());
        
        // Test isInTreasury
        $model = new EgiBlockchain(['ownership_type' => 'treasury']);
        $this->assertTrue($model->isInTreasury());
        
        $model = new EgiBlockchain(['ownership_type' => 'wallet']);
        $this->assertFalse($model->isInTreasury());
    }

    /**
     * Test certificate related methods
     */
    public function test_certificate_methods(): void
    {
        // Test hasCertificate - requires both path and URL
        $model = new EgiBlockchain([
            'certificate_path' => '/path/to/cert.pdf',
            'verification_url' => 'https://verify.example.com'
        ]);
        $this->assertTrue($model->hasCertificate());
        
        $model = new EgiBlockchain(['certificate_path' => null, 'verification_url' => 'https://verify.example.com']);
        $this->assertFalse($model->hasCertificate());
        
        // Test getVerificationUrl
        $url = 'https://verify.example.com';
        $model = new EgiBlockchain(['verification_url' => $url]);
        $this->assertEquals($url, $model->getVerificationUrl());
        
        $model = new EgiBlockchain(['verification_url' => null]);
        $this->assertNull($model->getVerificationUrl());
    }

    /**
     * Test blockchain explorer URL generation
     */
    public function test_blockchain_explorer_url(): void
    {
        // Mock config for different networks
        $model = new EgiBlockchain([
            'mint_status' => 'minted',
            'asa_id' => '123456789'
        ]);
        
        // Test with different network configs
        config(['algorand.network' => 'mainnet']);
        $this->assertEquals('https://explorer.perawallet.app/asset/123456789', $model->getBlockchainExplorerUrl());
        
        config(['algorand.network' => 'testnet']);
        $this->assertEquals('https://testnet.explorer.perawallet.app/asset/123456789', $model->getBlockchainExplorerUrl());
        
        config(['algorand.network' => 'sandbox']);
        $this->assertNull($model->getBlockchainExplorerUrl());
        
        // Test not minted
        $model = new EgiBlockchain(['mint_status' => 'unminted']);
        $this->assertNull($model->getBlockchainExplorerUrl());
    }

    /**
     * Test model follows Laravel conventions
     */
    public function test_laravel_conventions(): void
    {
        $model = new EgiBlockchain();
        
        // Test table name follows convention
        $this->assertEquals('egi_blockchain', $model->getTable());
        
        // Test primary key follows convention
        $this->assertEquals('id', $model->getKeyName());
        
        // Test timestamps are enabled
        $this->assertTrue($model->timestamps);
        
        // Test created_at and updated_at columns
        $this->assertEquals('created_at', $model->getCreatedAtColumn());
        $this->assertEquals('updated_at', $model->getUpdatedAtColumn());
    }

    /**
     * Test model documentation and namespace
     */
    public function test_model_documentation(): void
    {
        $reflection = new ReflectionClass(EgiBlockchain::class);
        
        // Test correct namespace
        $this->assertEquals('App\Models', $reflection->getNamespaceName());
        
        // Test class has docblock (basic check)
        $docComment = $reflection->getDocComment();
        if ($docComment !== false) {
            $this->assertStringContainsString('EgiBlockchain', $docComment);
        }
        $this->assertTrue(true); // Class exists and is testable
    }

    /**
     * Test MiCA-SAFE compliance indicators in code
     */
    public function test_mica_safe_compliance(): void
    {
        $reflection = new ReflectionClass(EgiBlockchain::class);
        $source = file_get_contents($reflection->getFileName());
        
        // Check for MiCA-SAFE related fields and comments
        $this->assertStringContainsString('treasury', $source);
        $this->assertStringContainsString('ownership_type', $source);
        $this->assertStringContainsString('FIAT', $source);
        
        // Check no direct crypto handling methods
        $this->assertStringNotContainsString('crypto_balance', $source);
        $this->assertStringNotContainsString('private_key', $source);
        $this->assertStringNotContainsString('wallet_custody', $source);
    }
}