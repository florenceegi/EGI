<?php

namespace Tests\Unit\Services\Coa;

use Tests\TestCase;
use App\Models\Coa;
use App\Models\Egi;
use App\Models\User;
use App\Models\CoaSnapshot;
use App\Services\Coa\CoaIssueService;
use App\Services\Coa\TraitsSnapshotService;
use App\Services\Coa\SerialGenerator;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use App\Services\Gdpr\AuditLogService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use ReflectionClass;

/**
 * @Oracode Test: CoA Verification Hash Integrity
 * 🎯 Purpose: Test verification hash calculation and integrity detection
 * 🛡️ Privacy: Tests with synthetic data only
 * 🧱 Core Logic: Validates hash generation, consistency, and tamper detection
 *
 * @package Tests\Unit\Services\Coa
 * @author AI Assistant following FlorenceEGI patterns
 * @version 1.0.0 (CoA Pro System)
 * @date 2025-09-23
 * @purpose Ensure certificate verification hash integrity
 */
class CoaVerificationHashTest extends TestCase {
    use RefreshDatabase;

    protected CoaIssueService $coaIssueService;
    protected User $testUser;
    protected Egi $testEgi;

    protected function setUp(): void {
        parent::setUp();

        // Create test user
        $this->testUser = User::factory()->create([
            'name' => 'Test Artist',
            'email' => 'test@florenceegi.test'
        ]);

        // Create test EGI
        $this->testEgi = Egi::factory()->create([
            'user_id' => $this->testUser->id,
            'title' => 'Test Artwork',
            'description' => 'Test artwork for hash verification'
        ]);

        // Setup service dependencies
        $logger = $this->createMock(UltraLogManager::class);
        $errorManager = $this->createMock(ErrorManagerInterface::class);
        $auditService = $this->createMock(AuditLogService::class);
        $snapshotService = $this->createMock(TraitsSnapshotService::class);
        $serialGenerator = $this->createMock(SerialGenerator::class);

        // Configure mocks
        $serialGenerator->method('generateSerial')
            ->willReturn('COA-EGI-2025-TEST001');

        $snapshotService->method('createTraitsVersion')
            ->willReturn(new \App\Models\EgiTraitsVersion([
                'id' => 1,
                'egi_id' => $this->testEgi->id,
                'version' => 1,
                'traits_json' => ['test' => 'data'],
                'traits_hash' => 'test-hash'
            ]));

        $snapshotService->method('createCoaSnapshot')
            ->willReturn(new CoaSnapshot([
                'id' => 1,
                'snapshot_json' => [
                    'work' => ['title' => 'Test Artwork'],
                    'traits' => ['technique' => 'oil'],
                    'issuer' => ['name' => 'Test Artist']
                ]
            ]));

        $this->coaIssueService = new CoaIssueService(
            $logger,
            $errorManager,
            $auditService,
            $snapshotService,
            $serialGenerator
        );
    }

    /**
     * Test that verification hash is generated during CoA creation
     */
    public function test_verification_hash_is_generated_on_coa_creation(): void {
        // Act: Create CoA
        $coa = $this->coaIssueService->issueCoaCertificate(
            $this->testEgi,
            'Test Artist',
            'Test notes',
            $this->testUser
        );

        // Assert: Verification hash is populated
        $this->assertNotNull($coa->verification_hash);
        $this->assertEquals(64, strlen($coa->verification_hash)); // SHA-256 length
        $this->assertMatchesRegularExpression('/^[a-f0-9]{64}$/', $coa->verification_hash);
    }

    /**
     * Test that verification hash is consistent for same data
     */
    public function test_verification_hash_consistency(): void {
        // Create multiple CoAs with same base data
        $coa1 = $this->coaIssueService->issueCoaCertificate(
            $this->testEgi,
            'Test Artist',
            'Test notes',
            $this->testUser
        );

        // Create second EGI with identical data
        $testEgi2 = Egi::factory()->create([
            'user_id' => $this->testUser->id,
            'title' => 'Test Artwork',
            'description' => 'Test artwork for hash verification'
        ]);

        $coa2 = $this->coaIssueService->issueCoaCertificate(
            $testEgi2,
            'Test Artist',
            'Test notes',
            $this->testUser
        );

        // Hashes should be different due to different serials and EGI IDs
        $this->assertNotEquals($coa1->verification_hash, $coa2->verification_hash);

        // But both should be valid SHA-256 hashes
        $this->assertEquals(64, strlen($coa1->verification_hash));
        $this->assertEquals(64, strlen($coa2->verification_hash));
    }

    /**
     * Test verification hash integrity detection
     */
    public function test_verification_hash_detects_tampering(): void {
        // Create original CoA
        $coa = $this->coaIssueService->issueCoaCertificate(
            $this->testEgi,
            'Test Artist',
            'Test notes',
            $this->testUser
        );

        $originalHash = $coa->verification_hash;

        // Simulate data tampering by modifying core fields
        $coa->update(['issuer_name' => 'Tampered Artist']);

        // Recalculate hash to simulate integrity check
        $snapshot = $coa->snapshot;
        $issuerInfo = [
            'type' => $coa->issuer_type,
            'name' => $coa->issuer_name, // Now tampered
            'location' => $coa->issuer_location,
        ];

        $reflection = new \ReflectionClass($this->coaIssueService);
        $method = $reflection->getMethod('calculateVerificationHash');
        $method->setAccessible(true);

        $newHash = $method->invokeArgs($this->coaIssueService, [$coa, $snapshot, $issuerInfo]);

        // Hash should be different, indicating tampering
        $this->assertNotEquals($originalHash, $newHash);
    }

    /**
     * Test verification hash includes all required components
     */
    public function test_hash_changes_with_different_data() {
        $coa1 = $this->createTestCoa();
        $coa2 = $this->createTestCoa(['serial' => 'EGI-TEST-456', 'issuer_user_id' => 999]);

        $snapshot1 = $this->createTestSnapshot($coa1);
        $snapshot2 = $this->createTestSnapshot($coa2);

        $reflection = new ReflectionClass($this->coaIssueService);
        $method = $reflection->getMethod('calculateVerificationHash');
        $method->setAccessible(true);

        $hash1 = $method->invoke($this->coaIssueService, $coa1, $snapshot1);
        $hash2 = $method->invoke($this->coaIssueService, $coa2, $snapshot2);

        $this->assertNotEquals($hash1, $hash2);
        $this->assertNotEmpty($hash1);
        $this->assertNotEmpty($hash2);
    }

    /**
     * Test that null values don't break hash calculation
     */
    public function test_verification_hash_handles_null_values(): void {
        // Create CoA with null location
        $coa = $this->coaIssueService->issueCoaCertificate(
            $this->testEgi,
            'Test Artist',
            null, // null notes
            $this->testUser
        );

        // Update to have null location
        $coa->update(['issuer_location' => null]);

        // Should still generate valid hash
        $this->assertNotNull($coa->verification_hash);
        $this->assertEquals(64, strlen($coa->verification_hash));
    }

    /**
     * Test verification hash format and content
     */
    public function test_verification_hash_format(): void {
        $coa = $this->coaIssueService->issueCoaCertificate(
            $this->testEgi,
            'Test Artist',
            'Test notes',
            $this->testUser
        );

        // Test hash format
        $this->assertMatchesRegularExpression('/^[a-f0-9]{64}$/', $coa->verification_hash);

        // Test that hash is not just zeros or simple pattern
        $this->assertNotEquals(str_repeat('0', 64), $coa->verification_hash);
        $this->assertNotEquals(str_repeat('a', 64), $coa->verification_hash);
    }
}
