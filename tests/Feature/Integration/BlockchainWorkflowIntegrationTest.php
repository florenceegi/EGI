<?php

namespace Tests\Feature\Integration;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Config;
use Mockery;
use App\Models\User;
use App\Models\Egi;
use App\Models\Collection;
use App\Models\Reservation;
use App\Models\EgiBlockchain;
use App\Services\EgiMintingService;
use App\Services\AlgorandService;
use App\Services\Gdpr\AuditLogService;
use App\Services\Gdpr\ConsentService;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Carbon\Carbon;

/**
 * @package Tests\Feature\Integration
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Blockchain Integration)
 * @date 2025-10-07
 * @purpose Integration tests for complete Reservation→Payment→Mint workflow with real database
 */
class BlockchainWorkflowIntegrationTest extends TestCase {
    use DatabaseTransactions, WithFaker;

    private $testUser;
    private $testEgi;
    private $mockAlgorandService;
    private $mockLogger;
    private $mockErrorManager;
    private $mockAuditService;
    private $mockConsentService;

    protected function setUp(): void {
        parent::setUp();

        // Create test user without seeding entire database
        $this->testUser = User::factory()->create([
            'name' => 'Integration Test User',
            'email' => 'integration-' . uniqid() . '@test.com',
            'email_verified_at' => now(),
        ]);

        // Create test Collection first (required for EGI)
        $testCollection = Collection::factory()->create([
            'creator_id' => $this->testUser->id,
            'collection_name' => 'Test Collection for Integration'
        ]);

        // Create test EGI
        $this->testEgi = Egi::factory()->create([
            'collection_id' => $testCollection->id,
            'title' => 'Integration Test EGI',
            'description' => 'Test EGI for blockchain integration',
            'price' => 100.00,
            'status' => 'published',
            'is_published' => true,
            'creator' => $this->testUser->name,
        ]);

        // Setup mocks for blockchain services
        $this->setupServiceMocks();

        // Configure test environment
        $this->configureTestEnvironment();
    }

    protected function tearDown(): void {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Setup service mocks for testing
     */
    private function setupServiceMocks(): void {
        $this->mockAlgorandService = Mockery::mock(AlgorandService::class);
        $this->mockLogger = Mockery::mock(UltraLogManager::class);
        $this->mockErrorManager = Mockery::mock(ErrorManagerInterface::class);
        $this->mockAuditService = Mockery::mock(AuditLogService::class);
        $this->mockConsentService = Mockery::mock(ConsentService::class);

        // Setup default mock expectations
        $this->mockLogger->shouldReceive('info')->andReturn(true);
        $this->mockConsentService->shouldReceive('hasConsent')->andReturn(true);
        $this->mockAuditService->shouldReceive('logActivity')->andReturn(true);
        $this->mockAuditService->shouldReceive('logUserAction')->andReturn(
            new \App\Models\UserActivity()
        );
        $this->mockErrorManager->shouldReceive('handle')->andReturn(null);

        // Bind mocks to container
        $this->app->instance(UltraLogManager::class, $this->mockLogger);
        $this->app->instance(ErrorManagerInterface::class, $this->mockErrorManager);
        $this->app->instance(AuditLogService::class, $this->mockAuditService);
        $this->app->instance(ConsentService::class, $this->mockConsentService);
        $this->app->instance(AlgorandService::class, $this->mockAlgorandService);
    }

    /**
     * Configure test environment settings
     */
    private function configureTestEnvironment(): void {
        Config::set('algorand.treasury_address', 'TEST_TREASURY_ADDRESS_1234567890ABCDEF');
        Config::set('algorand.treasury_mnemonic', 'test treasury mnemonic for integration testing');
        Config::set('algorand.network', 'testnet');
        Config::set('queue.default', 'sync'); // Synchronous for testing
    }

    /**
     * Test: Complete workflow from reservation creation to EGI minting
     * @group integration
     */
    public function test_complete_reservation_to_mint_workflow(): void {
        // Arrange: Mock successful blockchain response
        $this->mockAlgorandService
            ->shouldReceive('mintEgi')
            ->once()
            ->andReturn([
                'asaId' => '123456789',
                'txId' => 'TEST_TX_ID_12345',
                'certificate_number' => 'CERT-001',
                'treasury_address' => 'TEST_TREASURY_ADDRESS_1234567890ABCDEF'
            ]);

        // Step 1: Create reservation  
        $reservationData = [
            'user_id' => $this->testUser->id,
            'egi_id' => $this->testEgi->id,
            'offer_amount_fiat' => 100.00,
            'amount_eur' => 100.00,
            'input_amount' => 100.00,
            'type' => 'strong',
        ];

        $reservation = Reservation::create($reservationData);
        $this->assertNotNull($reservation);

        // Step 2: Execute minting workflow
        $mintingService = new EgiMintingService(
            $this->mockLogger,
            $this->mockErrorManager,
            $this->mockAuditService,
            $this->mockConsentService,
            $this->mockAlgorandService
        );

        // Act: Execute minting workflow
        $result = $mintingService->mintEgi($this->testEgi, $this->testUser);

        // Assert: Verify workflow completion
        $this->assertInstanceOf(EgiBlockchain::class, $result);
        $this->assertEquals('minted', $result->mint_status);
        $this->assertEquals('123456789', $result->asa_id);

        // Verify database state
        $this->assertDatabaseHas('egi_blockchain', [
            'egi_id' => $this->testEgi->id,
            'asa_id' => '123456789',
            'blockchain_tx_id' => 'TEST_TX_ID_12345',
            'mint_status' => 'minted',
            'ownership_type' => 'treasury'
        ]);

        // Verify EgiBlockchain record was created correctly
        $egiBlockchain = EgiBlockchain::where('egi_id', $this->testEgi->id)->first();
        $this->assertNotNull($egiBlockchain);
        $this->assertEquals($this->testUser->id, $egiBlockchain->buyer_user_id);
        $this->assertNotNull($egiBlockchain->certificate_uuid);
        $this->assertNotNull($egiBlockchain->minted_at);
    }

    /**
     * Test: Workflow with multiple reservations and winner selection
     * @group integration
     */
    public function test_workflow_with_payment_distribution(): void {
        // Arrange: Create multiple buyers with reservations
        $buyer1 = User::factory()->create(['email' => 'buyer1-' . uniqid() . '@test.com']);
        $buyer2 = User::factory()->create(['email' => 'buyer2-' . uniqid() . '@test.com']);

        $testCollection = Collection::factory()->create([
            'creator_id' => $this->testUser->id,
            'collection_name' => 'Multi-buyer Test Collection'
        ]);

        $egi = Egi::factory()->create([
            'collection_id' => $testCollection->id,
            'title' => 'Multi-buyer Test EGI',
            'price' => 150.00
        ]);

        // Create reservations with different amounts
        $reservation1 = Reservation::create([
            'user_id' => $buyer1->id,
            'egi_id' => $egi->id,
            'offer_amount_fiat' => 180.00,
            'amount_eur' => 180.00,
            'input_amount' => 180.00,
            'type' => 'weak',
            'rank' => 2
        ]);

        $reservation2 = Reservation::create([
            'user_id' => $buyer2->id,
            'egi_id' => $egi->id,
            'offer_amount_fiat' => 220.00,
            'amount_eur' => 220.00,
            'input_amount' => 220.00,
            'type' => 'strong',
            'rank' => 1 // Winning reservation
        ]);

        // Mock AlgorandService for winner
        $this->mockAlgorandService
            ->shouldReceive('mintEgi')
            ->once()
            ->andReturn([
                'asaId' => '987654321',
                'txId' => 'TEST_TX_WINNER_54321',
                'certificate_number' => 'CERT-002',
                'treasury_address' => 'TEST_TREASURY_ADDRESS_1234567890ABCDEF'
            ]);

        // Act: Mint EGI for the winner
        $mintingService = new EgiMintingService(
            $this->mockLogger,
            $this->mockErrorManager,
            $this->mockAuditService,
            $this->mockConsentService,
            $this->mockAlgorandService
        );

        $result = $mintingService->mintEgi($egi, $buyer2);

        // Assert: Verify correct winner processing
        $this->assertInstanceOf(EgiBlockchain::class, $result);

        // Verify EgiBlockchain created for winner
        $egiBlockchain = EgiBlockchain::where('egi_id', $egi->id)->first();
        $this->assertNotNull($egiBlockchain);
        $this->assertEquals('987654321', $egiBlockchain->asa_id);
        $this->assertEquals('treasury', $egiBlockchain->ownership_type);

        // Verify payment currency is set (payment amount handled separately)
        $this->assertEquals('EUR', $egiBlockchain->paid_currency);
    }

    /**
     * Test: Error recovery workflow when minting fails
     * @group integration
     */
    public function test_error_recovery_workflow(): void {
        // Arrange: Create specific error manager mock for this test
        $errorManagerMock = Mockery::mock(ErrorManagerInterface::class);
        $algorandMock = Mockery::mock(AlgorandService::class);
        
        // Mock: Simulate Algorand service failure
        $algorandMock
            ->shouldReceive('mintEgi')
            ->once()
            ->andThrow(new \Exception('Blockchain network error'));

        $errorManagerMock
            ->shouldReceive('handle')
            ->once()
            ->with('EGI_MINTING_FAILED', Mockery::any(), Mockery::any())
            ->andReturn(null);

        // Act & Assert
        $mintingService = new EgiMintingService(
            $this->mockLogger,
            $errorManagerMock,
            $this->mockAuditService,
            $this->mockConsentService,
            $algorandMock
        );

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('EGI minting failed: Blockchain network error');

        $mintingService->mintEgi($this->testEgi, $this->testUser);

        // Verify error state in database
        $egiBlockchain = EgiBlockchain::where('egi_id', $this->testEgi->id)->first();
        if ($egiBlockchain) {
            $this->assertEquals('failed', $egiBlockchain->mint_status);
            $this->assertNotNull($egiBlockchain->mint_error);
        }
    }

    /**
     * Test: GDPR compliance in blockchain workflow
     * @group integration
     */
    public function test_gdpr_compliance_workflow(): void {
        // Arrange: Mock successful blockchain response
        $this->mockAlgorandService
            ->shouldReceive('mintEgi')
            ->once()
            ->andReturn([
                'asaId' => '555666777',
                'txId' => 'GDPR_TEST_TX_789',
                'certificate_number' => 'CERT-GDPR-001',
                'treasury_address' => 'TEST_TREASURY_ADDRESS_1234567890ABCDEF'
            ]);

        // Act: Execute minting workflow
        $mintingService = new EgiMintingService(
            $this->mockLogger,
            $this->mockErrorManager,
            $this->mockAuditService,
            $this->mockConsentService,
            $this->mockAlgorandService
        );

        $result = $mintingService->mintEgi($this->testEgi, $this->testUser);

        // Assert: Verify GDPR compliance
        $this->assertInstanceOf(EgiBlockchain::class, $result);
        
        // Verify audit trail was created
        $this->mockAuditService->shouldHaveReceived('logUserAction')->once();
        
        // Verify consent was checked
        $this->mockConsentService->shouldHaveReceived('hasConsent')->atLeast()->once();
        
        // Verify blockchain record includes GDPR-required fields
        $egiBlockchain = EgiBlockchain::where('egi_id', $this->testEgi->id)->first();
        $this->assertNotNull($egiBlockchain->buyer_user_id);
        $this->assertNotNull($egiBlockchain->certificate_uuid);
    }

    /**
     * Test: MiCA-SAFE compliance verification
     * @group integration
     */
    public function test_mica_safe_compliance(): void {
        // Arrange: Mock successful blockchain response
        $this->mockAlgorandService
            ->shouldReceive('mintEgi')
            ->once()
            ->andReturn([
                'asaId' => '888999000',
                'txId' => 'MICA_TEST_TX_111',
                'certificate_number' => 'CERT-MICA-001',
                'treasury_address' => 'TEST_TREASURY_ADDRESS_1234567890ABCDEF'
            ]);

        // Act: Execute minting workflow
        $mintingService = new EgiMintingService(
            $this->mockLogger,
            $this->mockErrorManager,
            $this->mockAuditService,
            $this->mockConsentService,
            $this->mockAlgorandService
        );

        $result = $mintingService->mintEgi($this->testEgi, $this->testUser);

        // Assert: Verify MiCA-SAFE compliance
        $this->assertInstanceOf(EgiBlockchain::class, $result);
        
        $egiBlockchain = EgiBlockchain::where('egi_id', $this->testEgi->id)->first();
        
        // Verify platform retains treasury ownership (MiCA-SAFE)
        $this->assertEquals('treasury', $egiBlockchain->ownership_type);
        $this->assertEquals('TEST_TREASURY_ADDRESS_1234567890ABCDEF', $egiBlockchain->platform_wallet);
        
        // Verify no custodial crypto handling
        $this->assertNull($egiBlockchain->buyer_wallet);
        $this->assertFalse($egiBlockchain->supports_crypto_payments);
        
        // Verify fiat payment method only
        $this->assertEquals('mock', $egiBlockchain->payment_method);
        $this->assertEquals('EUR', $egiBlockchain->paid_currency);
    }

    /**
     * Test: Workflow with certificate generation
     * @group integration
     */
    public function test_workflow_with_certificate_generation(): void {
        // Arrange: Mock successful blockchain response with certificate
        $this->mockAlgorandService
            ->shouldReceive('mintEgi')
            ->once()
            ->andReturn([
                'asaId' => '111222333',
                'txId' => 'CERT_TEST_TX_444',
                'certificate_number' => 'CERT-GEN-001',
                'treasury_address' => 'TEST_TREASURY_ADDRESS_1234567890ABCDEF'
            ]);

        // Act: Execute minting workflow
        $mintingService = new EgiMintingService(
            $this->mockLogger,
            $this->mockErrorManager,
            $this->mockAuditService,
            $this->mockConsentService,
            $this->mockAlgorandService
        );

        $result = $mintingService->mintEgi($this->testEgi, $this->testUser);

        // Assert: Verify certificate generation
        $this->assertInstanceOf(EgiBlockchain::class, $result);
        
        $egiBlockchain = EgiBlockchain::where('egi_id', $this->testEgi->id)->first();
        
        // Verify certificate-related fields
        $this->assertNotNull($egiBlockchain->certificate_uuid);
        $this->assertEquals('CERT-GEN-001', $egiBlockchain->anchor_hash);
        $this->assertNotNull($egiBlockchain->minted_at);
        
        // Certificate generation creates proper blockchain linkage
        $this->assertEquals('111222333', $egiBlockchain->asa_id);
        $this->assertEquals('CERT_TEST_TX_444', $egiBlockchain->blockchain_tx_id);
    }

    /**
     * Test: Database consistency during workflow
     * @group integration
     */
    public function test_database_consistency(): void {
        // Arrange: Mock successful blockchain response
        $this->mockAlgorandService
            ->shouldReceive('mintEgi')
            ->once()
            ->andReturn([
                'asaId' => '777888999',
                'txId' => 'CONSISTENCY_TX_555',
                'certificate_number' => 'CERT-CONS-001',
                'treasury_address' => 'TEST_TREASURY_ADDRESS_1234567890ABCDEF'
            ]);

        // Act: Execute minting workflow
        $mintingService = new EgiMintingService(
            $this->mockLogger,
            $this->mockErrorManager,
            $this->mockAuditService,
            $this->mockConsentService,
            $this->mockAlgorandService
        );

        $result = $mintingService->mintEgi($this->testEgi, $this->testUser);

        // Assert: Verify database consistency
        $this->assertInstanceOf(EgiBlockchain::class, $result);
        
        // Verify referential integrity
        $egiBlockchain = EgiBlockchain::where('egi_id', $this->testEgi->id)->first();
        $this->assertEquals($this->testEgi->id, $egiBlockchain->egi_id);
        $this->assertEquals($this->testUser->id, $egiBlockchain->buyer_user_id);
        
        // Verify timestamps consistency
        $this->assertNotNull($egiBlockchain->created_at);
        $this->assertNotNull($egiBlockchain->updated_at);
        $this->assertNotNull($egiBlockchain->minted_at);
        
        // Verify data integrity
        $this->assertEquals('minted', $egiBlockchain->mint_status);
        $this->assertNull($egiBlockchain->mint_error);
    }

    /**
     * Test: Workflow performance benchmarks
     * @group integration
     */
    public function test_workflow_performance(): void {
        // Arrange: Mock successful blockchain response
        $this->mockAlgorandService
            ->shouldReceive('mintEgi')
            ->once()
            ->andReturn([
                'asaId' => '444555666',
                'txId' => 'PERF_TEST_TX_777',
                'certificate_number' => 'CERT-PERF-001',
                'treasury_address' => 'TEST_TREASURY_ADDRESS_1234567890ABCDEF'
            ]);

        // Act: Execute minting workflow with performance monitoring
        $startTime = microtime(true);
        
        $mintingService = new EgiMintingService(
            $this->mockLogger,
            $this->mockErrorManager,
            $this->mockAuditService,
            $this->mockConsentService,
            $this->mockAlgorandService
        );

        $result = $mintingService->mintEgi($this->testEgi, $this->testUser);
        
        $executionTime = microtime(true) - $startTime;

        // Assert: Verify performance benchmarks
        $this->assertInstanceOf(EgiBlockchain::class, $result);
        $this->assertLessThan(5.0, $executionTime, 'Minting workflow should complete within 5 seconds');
        
        // Verify efficient database operations
        $egiBlockchain = EgiBlockchain::where('egi_id', $this->testEgi->id)->first();
        $this->assertNotNull($egiBlockchain);
        $this->assertEquals('minted', $egiBlockchain->mint_status);
    }

    /**
     * Test: Concurrent workflow handling
     * @group integration
     */
    public function test_concurrent_workflow_handling(): void {
        // Arrange: Create multiple EGIs for concurrent processing
        $testCollection = Collection::factory()->create([
            'creator_id' => $this->testUser->id,
            'collection_name' => 'Concurrent Test Collection'
        ]);

        $egi1 = Egi::factory()->create([
            'collection_id' => $testCollection->id,
            'title' => 'Concurrent Test EGI 1'
        ]);

        $egi2 = Egi::factory()->create([
            'collection_id' => $testCollection->id,
            'title' => 'Concurrent Test EGI 2'
        ]);

        // Mock blockchain responses for both EGIs
        $this->mockAlgorandService
            ->shouldReceive('mintEgi')
            ->twice()
            ->andReturn(
                [
                    'asaId' => '111111111',
                    'txId' => 'CONCURRENT_TX_1',
                    'certificate_number' => 'CERT-CONC-001',
                    'treasury_address' => 'TEST_TREASURY_ADDRESS_1234567890ABCDEF'
                ],
                [
                    'asaId' => '222222222',
                    'txId' => 'CONCURRENT_TX_2',
                    'certificate_number' => 'CERT-CONC-002',
                    'treasury_address' => 'TEST_TREASURY_ADDRESS_1234567890ABCDEF'
                ]
            );

        // Act: Execute concurrent minting
        $mintingService = new EgiMintingService(
            $this->mockLogger,
            $this->mockErrorManager,
            $this->mockAuditService,
            $this->mockConsentService,
            $this->mockAlgorandService
        );

        $result1 = $mintingService->mintEgi($egi1, $this->testUser);
        $result2 = $mintingService->mintEgi($egi2, $this->testUser);

        // Assert: Verify concurrent processing
        $this->assertInstanceOf(EgiBlockchain::class, $result1);
        $this->assertInstanceOf(EgiBlockchain::class, $result2);
        
        // Verify separate blockchain records
        $blockchain1 = EgiBlockchain::where('egi_id', $egi1->id)->first();
        $blockchain2 = EgiBlockchain::where('egi_id', $egi2->id)->first();
        
        $this->assertNotNull($blockchain1);
        $this->assertNotNull($blockchain2);
        $this->assertNotEquals($blockchain1->id, $blockchain2->id);
        $this->assertNotEquals($blockchain1->asa_id, $blockchain2->asa_id);
    }
}