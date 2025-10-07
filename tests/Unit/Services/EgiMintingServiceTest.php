<?php

namespace Tests\Unit\Services;

use App\Models\Egi;
use App\Models\EgiBlockchain;
use App\Models\User;
use App\Services\EgiMintingService;
use App\Services\AlgorandService;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use App\Services\Gdpr\AuditLogService;
use App\Services\Gdpr\ConsentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;
use Mockery;

/**
 * @package Tests\Unit\Services
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Blockchain Integration)
 * @date 2025-10-07
 * @purpose Comprehensive tests for EgiMintingService - Unit & Integration testing
 */
class EgiMintingServiceTest extends TestCase {
    use RefreshDatabase;

    private EgiMintingService $mintingService;
    private $mockLogger;
    private $mockErrorManager;
    private $mockAuditService;
    private $mockConsentService;
    private $mockAlgorandService;

    protected function setUp(): void {
        parent::setUp();

        // Create mocks for all dependencies
        $this->mockLogger = Mockery::mock(UltraLogManager::class);
        $this->mockErrorManager = Mockery::mock(ErrorManagerInterface::class);
        $this->mockAuditService = Mockery::mock(AuditLogService::class);
        $this->mockConsentService = Mockery::mock(ConsentService::class);
        $this->mockAlgorandService = Mockery::mock(AlgorandService::class);

        // Instantiate service with mocked dependencies
        $this->mintingService = new EgiMintingService(
            $this->mockLogger,
            $this->mockErrorManager,
            $this->mockAuditService,
            $this->mockConsentService,
            $this->mockAlgorandService
        );
    }

    protected function tearDown(): void {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Helper: Create test user with proper attributes
     */
    protected function createTestUser(): User {
        return User::create([
            'name' => fake()->name(),
            'username' => fake()->unique()->userName(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'wallet' => $this->generateAlgorandAddress(),
            'wallet_balance' => fake()->randomFloat(2, 0, 1000),
        ]);
    }

    /**
     * Helper: Create test EGI
     */
    protected function createTestEgi(User $creator = null): Egi {
        if (!$creator) {
            $creator = $this->createTestUser();
        }

        return Egi::create([
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'creator_id' => $creator->id,
            'collection_id' => null, // Simplified for testing
            'price' => fake()->randomFloat(2, 10, 1000),
            'currency' => 'EUR',
            'status' => 'active',
        ]);
    }

    /**
     * Helper: Generate valid Algorand address format
     */
    protected function generateAlgorandAddress(): string {
        $base32Chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $address = '';
        for ($i = 0; $i < 58; $i++) {
            $address .= $base32Chars[fake()->numberBetween(0, 31)];
        }
        return $address;
    }

    /**
     * Test: Successful EGI minting with all requirements met
     */
    public function test_successful_egi_minting(): void {
        // Arrange
        $user = $this->createTestUser();
        $egi = $this->createTestEgi();

        $mockAsaId = '123456';
        $mockTxId = 'ALGO-TX-' . time() . '-TEST';
        $mockTreasuryAddress = $this->generateAlgorandAddress();

        // Mock successful consent check
        $this->mockConsentService
            ->shouldReceive('hasConsent')
            ->with($user, 'allow-blockchain-operations')
            ->andReturn(true);

        // Mock successful AlgorandService minting
        $this->mockAlgorandService
            ->shouldReceive('mintEgi')
            ->with($egi->id, Mockery::any(), $user)
            ->andReturn([
                'asaId' => $mockAsaId,
                'txId' => $mockTxId,
                'treasury_address' => $mockTreasuryAddress,
                'certificate_number' => 'CERT-' . $egi->id
            ]);

        // Mock logging calls
        $this->mockLogger->shouldReceive('info')->atLeast()->once();

        // Mock audit logging
        $this->mockAuditService
            ->shouldReceive('logActivity')
            ->with($user, Mockery::any(), Mockery::any(), Mockery::any())
            ->once();

        // Act
        $result = $this->mintingService->mintEgi($egi, $user);

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('egi_blockchain', $result);
        $this->assertArrayHasKey('minting_result', $result);
        $this->assertEquals($mockAsaId, $result['minting_result']['asaId']);
        $this->assertEquals($mockTxId, $result['minting_result']['txId']);

        // Verify database record was created
        $this->assertDatabaseHas('egi_blockchain', [
            'egi_id' => $egi->id,
            'asa_id' => $mockAsaId,
            'blockchain_tx_id' => $mockTxId,
            'mint_status' => 'minted',
            'ownership_type' => 'treasury'
        ]);
    }

    /**
     * Test: Minting fails when user lacks blockchain consent
     */
    public function test_minting_fails_without_consent(): void {
        // Arrange
        $user = $this->createTestUser();
        $egi = $this->createTestEgi();

        // Mock consent check failure
        $this->mockConsentService
            ->shouldReceive('hasConsent')
            ->with($user, 'allow-blockchain-operations')
            ->andReturn(false);

        // Mock logging
        $this->mockLogger->shouldReceive('info')->atLeast()->once();

        // Mock error handling
        $this->mockErrorManager
            ->shouldReceive('handle')
            ->with('EGI_MINTING_CONSENT_FAILED', Mockery::any(), Mockery::any())
            ->once();

        // Act & Assert
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('User consent required for blockchain operations');

        $this->mintingService->mintEgi($egi, $user);
    }

    /**
     * Test: Minting fails when EGI already has blockchain record
     */
    public function test_minting_fails_when_egi_already_minted(): void {
        // Arrange
        $user = $this->createTestUser();
        $egi = $this->createTestEgi();

        // Create existing blockchain record
        EgiBlockchain::create([
            'egi_id' => $egi->id,
            'asa_id' => '999999',
            'mint_status' => 'minted',
            'ownership_type' => 'treasury',
            'platform_wallet' => $this->generateAlgorandAddress()
        ]);

        // Mock consent check (should pass)
        $this->mockConsentService
            ->shouldReceive('hasConsent')
            ->with($user, 'allow-blockchain-operations')
            ->andReturn(true);

        // Mock logging
        $this->mockLogger->shouldReceive('info')->atLeast()->once();

        // Mock error handling
        $this->mockErrorManager
            ->shouldReceive('handle')
            ->with('EGI_ALREADY_MINTED', Mockery::any(), Mockery::any())
            ->once();

        // Act & Assert
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('EGI already has blockchain record');

        $this->mintingService->mintEgi($egi, $user);
    }

    /**
     * Test: Minting handles AlgorandService failures gracefully
     */
    public function test_minting_handles_algorand_service_failure(): void {
        // Arrange
        $user = $this->createTestUser();
        $egi = $this->createTestEgi();

        // Mock successful consent check
        $this->mockConsentService
            ->shouldReceive('hasConsent')
            ->with($user, 'allow-blockchain-operations')
            ->andReturn(true);

        // Mock AlgorandService failure
        $this->mockAlgorandService
            ->shouldReceive('mintEgi')
            ->with($egi->id, Mockery::any(), $user)
            ->andThrow(new \Exception('Blockchain network error'));

        // Mock logging
        $this->mockLogger->shouldReceive('info')->atLeast()->once();

        // Mock error handling
        $this->mockErrorManager
            ->shouldReceive('handle')
            ->with('EGI_MINTING_FAILED', Mockery::any(), Mockery::any())
            ->once();

        // Act & Assert
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('EGI minting failed: Blockchain network error');

        $this->mintingService->mintEgi($egi, $user);

        // Verify database record was created with failed status
        $this->assertDatabaseHas('egi_blockchain', [
            'egi_id' => $egi->id,
            'mint_status' => 'failed',
            'mint_error' => 'Blockchain network error'
        ]);
    }

    /**
     * Test: Minting creates proper metadata for AlgorandService
     */
    public function test_minting_creates_proper_metadata(): void {
        // Arrange
        $user = $this->createTestUser();
        $egi = $this->createTestEgi();

        // Mock consent check
        $this->mockConsentService
            ->shouldReceive('hasConsent')
            ->with($user, 'allow-blockchain-operations')
            ->andReturn(true);

        // Mock AlgorandService with metadata validation
        $this->mockAlgorandService
            ->shouldReceive('mintEgi')
            ->with($egi->id, Mockery::on(function ($metadata) use ($egi) {
                // Validate metadata structure within the mock
                $this->assertIsArray($metadata);
                $this->assertArrayHasKey('title', $metadata);
                $this->assertArrayHasKey('description', $metadata);
                $this->assertArrayHasKey('creator_id', $metadata);
                $this->assertArrayHasKey('price', $metadata);
                $this->assertArrayHasKey('currency', $metadata);
                $this->assertEquals($egi->title, $metadata['title']);
                $this->assertEquals($egi->description, $metadata['description']);
                return true;
            }), $user)
            ->andReturn([
                'asaId' => '123456',
                'txId' => 'TEST-TX-ID',
                'treasury_address' => $this->generateAlgorandAddress()
            ]);

        // Mock other dependencies
        $this->mockLogger->shouldReceive('info')->atLeast()->once();
        $this->mockAuditService->shouldReceive('logActivity')->once();

        // Act
        $result = $this->mintingService->mintEgi($egi, $user);

        // Assert successful result
        $this->assertIsArray($result);
        $this->assertArrayHasKey('egi_blockchain', $result);
    }

    /**
     * Test: Multiple EGI minting in sequence
     */
    public function test_multiple_egi_minting(): void {
        // Arrange
        $user = $this->createTestUser();
        $egi1 = $this->createTestEgi();
        $egi2 = $this->createTestEgi();

        // Mock consent checks
        $this->mockConsentService
            ->shouldReceive('hasConsent')
            ->with($user, 'allow-blockchain-operations')
            ->andReturn(true)
            ->twice();

        // Mock AlgorandService for both EGIs
        $this->mockAlgorandService
            ->shouldReceive('mintEgi')
            ->andReturn([
                'asaId' => '123456',
                'txId' => 'TEST-TX-1',
                'treasury_address' => $this->generateAlgorandAddress()
            ], [
                'asaId' => '789012',
                'txId' => 'TEST-TX-2',
                'treasury_address' => $this->generateAlgorandAddress()
            ]);

        // Mock logging and auditing
        $this->mockLogger->shouldReceive('info')->atLeast()->twice();
        $this->mockAuditService->shouldReceive('logActivity')->twice();

        // Act
        $result1 = $this->mintingService->mintEgi($egi1, $user);
        $result2 = $this->mintingService->mintEgi($egi2, $user);

        // Assert both succeeded
        $this->assertIsArray($result1);
        $this->assertIsArray($result2);

        // Verify both database records
        $this->assertDatabaseHas('egi_blockchain', [
            'egi_id' => $egi1->id,
            'mint_status' => 'minted'
        ]);

        $this->assertDatabaseHas('egi_blockchain', [
            'egi_id' => $egi2->id,
            'mint_status' => 'minted'
        ]);
    }

    /**
     * Test: EGI blockchain record contains all required fields
     */
    public function test_egi_blockchain_record_completeness(): void {
        // Arrange
        $user = $this->createTestUser();
        $egi = $this->createTestEgi();

        $mockResponse = [
            'asaId' => '123456',
            'txId' => 'ALGO-TX-12345',
            'treasury_address' => $this->generateAlgorandAddress(),
            'certificate_number' => 'CERT-' . $egi->id
        ];

        // Mock all dependencies
        $this->mockConsentService
            ->shouldReceive('hasConsent')
            ->andReturn(true);

        $this->mockAlgorandService
            ->shouldReceive('mintEgi')
            ->andReturn($mockResponse);

        $this->mockLogger->shouldReceive('info')->atLeast()->once();
        $this->mockAuditService->shouldReceive('logActivity')->once();

        // Act
        $this->mintingService->mintEgi($egi, $user);

        // Assert database record completeness
        $blockchainRecord = EgiBlockchain::where('egi_id', $egi->id)->first();

        $this->assertNotNull($blockchainRecord);
        $this->assertEquals($egi->id, $blockchainRecord->egi_id);
        $this->assertEquals('123456', $blockchainRecord->asa_id);
        $this->assertEquals('ALGO-TX-12345', $blockchainRecord->blockchain_tx_id);
        $this->assertEquals('minted', $blockchainRecord->mint_status);
        $this->assertEquals('treasury', $blockchainRecord->ownership_type);
        $this->assertNotNull($blockchainRecord->minted_at);
        $this->assertNull($blockchainRecord->mint_error);
        $this->assertEquals($mockResponse['treasury_address'], $blockchainRecord->platform_wallet);
    }
}
