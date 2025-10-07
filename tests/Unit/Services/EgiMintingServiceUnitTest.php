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
use Tests\TestCase;
use Mockery;

/**
 * @package Tests\Unit\Services
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Blockchain Integration)
 * @date 2025-10-07
 * @purpose Unit tests for EgiMintingService (Mocked dependencies - no database)
 */
class EgiMintingServiceUnitTest extends TestCase {

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
     * Helper: Create mock user with proper expectations
     */
    protected function createMockUser(): User {
        $user = Mockery::mock(User::class);
        $user->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $user->shouldReceive('getAttribute')->with('name')->andReturn('Test User');
        $user->shouldReceive('getAttribute')->with('email')->andReturn('test@example.com');
        $user->shouldReceive('__get')->with('id')->andReturn(1);
        $user->shouldReceive('__get')->with('name')->andReturn('Test User');
        $user->shouldReceive('__get')->with('email')->andReturn('test@example.com');
        return $user;
    }

    /**
     * Helper: Create mock EGI with proper expectations
     */
    protected function createMockEgi(): Egi {
        $egi = Mockery::mock(Egi::class);
        $egi->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $egi->shouldReceive('getAttribute')->with('title')->andReturn('Test EGI');
        $egi->shouldReceive('getAttribute')->with('description')->andReturn('Test Description');
        $egi->shouldReceive('getAttribute')->with('creator_id')->andReturn(1);
        $egi->shouldReceive('getAttribute')->with('price')->andReturn(100.00);
        $egi->shouldReceive('getAttribute')->with('currency')->andReturn('EUR');
        
        $egi->shouldReceive('__get')->with('id')->andReturn(1);
        $egi->shouldReceive('__get')->with('title')->andReturn('Test EGI');
        $egi->shouldReceive('__get')->with('description')->andReturn('Test Description');
        $egi->shouldReceive('__get')->with('creator_id')->andReturn(1);
        $egi->shouldReceive('__get')->with('price')->andReturn(100.00);
        $egi->shouldReceive('__get')->with('currency')->andReturn('EUR');
        
        // Mock blockchain relationship
        $egi->shouldReceive('blockchain')->andReturn(null);
        
        return $egi;
    }

    /**
     * Test: Service instantiation with all dependencies
     */
    public function test_service_instantiation(): void {
        // Assert service is properly instantiated
        $this->assertInstanceOf(EgiMintingService::class, $this->mintingService);
    }

    /**
     * Test: Consent check validation
     */
    public function test_consent_check_validation(): void {
        // Arrange
        $user = $this->createMockUser();
        $egi = $this->createMockEgi();

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
     * Test: Metadata generation structure
     */
    public function test_metadata_generation(): void {
        // Arrange
        $user = $this->createMockUser();
        $egi = $this->createMockEgi();

        // Mock consent check success
        $this->mockConsentService
            ->shouldReceive('hasConsent')
            ->with($user, 'allow-blockchain-operations')
            ->andReturn(true);

        // Mock AlgorandService with metadata validation
        $this->mockAlgorandService
            ->shouldReceive('mintEgi')
            ->with($egi->id, Mockery::on(function ($metadata) use ($egi) {
                // Validate metadata structure
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
                'treasury_address' => 'TREASURY_ADDRESS_MOCK'
            ]);

        // Mock other dependencies
        $this->mockLogger->shouldReceive('info')->atLeast()->once();
        $this->mockAuditService->shouldReceive('logActivity')->once();

        // Mock EgiBlockchain creation
        $mockBlockchain = Mockery::mock(EgiBlockchain::class);
        $mockBlockchain->shouldReceive('create')->andReturn($mockBlockchain);

        // Act
        $result = $this->mintingService->mintEgi($egi, $user);

        // Assert
        $this->assertIsArray($result);
    }

    /**
     * Test: AlgorandService failure handling
     */
    public function test_algorand_service_failure_handling(): void {
        // Arrange
        $user = $this->createMockUser();
        $egi = $this->createMockEgi();

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
    }

    /**
     * Test: Logging behavior validation
     */
    public function test_logging_behavior(): void {
        // Arrange
        $user = $this->createMockUser();
        $egi = $this->createMockEgi();

        // Mock consent check
        $this->mockConsentService
            ->shouldReceive('hasConsent')
            ->andReturn(true);

        // Mock successful AlgorandService
        $this->mockAlgorandService
            ->shouldReceive('mintEgi')
            ->andReturn([
                'asaId' => '123456',
                'txId' => 'TEST-TX-ID',
                'treasury_address' => 'TREASURY_ADDRESS_MOCK'
            ]);

        // Mock and verify logging calls
        $this->mockLogger
            ->shouldReceive('info')
            ->with('EGI minting initiated', Mockery::any())
            ->once();
            
        $this->mockLogger
            ->shouldReceive('info')
            ->with('EGI minting successful', Mockery::any())
            ->once();

        // Mock audit logging
        $this->mockAuditService
            ->shouldReceive('logActivity')
            ->once();

        // Act
        $result = $this->mintingService->mintEgi($egi, $user);

        // Assert logging was called
        $this->assertIsArray($result);
    }

    /**
     * Test: Audit trail compliance
     */
    public function test_audit_trail_compliance(): void {
        // Arrange
        $user = $this->createMockUser();
        $egi = $this->createMockEgi();

        // Mock dependencies
        $this->mockConsentService
            ->shouldReceive('hasConsent')
            ->andReturn(true);

        $this->mockAlgorandService
            ->shouldReceive('mintEgi')
            ->andReturn([
                'asaId' => '123456',
                'txId' => 'TEST-TX-ID',
                'treasury_address' => 'TREASURY_ADDRESS_MOCK'
            ]);

        $this->mockLogger->shouldReceive('info')->atLeast()->once();

        // Verify audit trail is properly called
        $this->mockAuditService
            ->shouldReceive('logActivity')
            ->with($user, Mockery::any(), 'EGI minted successfully on blockchain', Mockery::on(function ($data) {
                $this->assertIsArray($data);
                $this->assertArrayHasKey('egi_id', $data);
                $this->assertArrayHasKey('asa_id', $data);
                $this->assertArrayHasKey('tx_id', $data);
                return true;
            }))
            ->once();

        // Act
        $this->mintingService->mintEgi($egi, $user);

        // Assert audit trail was called (verified by mock expectations)
        $this->assertTrue(true);
    }

    /**
     * Test: Error handling for invalid EGI state
     */
    public function test_invalid_egi_state_handling(): void {
        // Arrange
        $user = $this->createMockUser();
        $egi = $this->createMockEgi();

        // Mock EGI that already has blockchain record
        $mockBlockchain = Mockery::mock(EgiBlockchain::class);
        $egi->shouldReceive('blockchain')->andReturn($mockBlockchain);

        // Mock consent check
        $this->mockConsentService
            ->shouldReceive('hasConsent')
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
}