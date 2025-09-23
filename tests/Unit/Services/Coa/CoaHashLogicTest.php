<?php

namespace Tests\Unit\Services\Coa;

use PHPUnit\Framework\TestCase;
use App\Models\Coa;
use App\Models\CoaSnapshot;
use App\Models\User;
use App\Services\Coa\CoaIssueService;
use App\Services\Coa\TraitsSnapshotService;
use App\Services\Coa\SerialGenerator;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use App\Services\Gdpr\AuditLogService;
use ReflectionClass;
use Mockery;

/**
 * Test unitario per la logica di calcolo hash verificazione CoA
 * Test isolato senza dipendenze database per verificare solo l'algoritmo di hash
 */
class CoaHashLogicTest extends TestCase {
    private $coaIssueService;
    private $traitsSnapshotService;
    private $serialGenerator;
    private $ultraLogManager;
    private $errorManager;
    private $auditLogService;

    protected function setUp(): void {
        parent::setUp();

        // Mock delle dipendenze
        $this->traitsSnapshotService = Mockery::mock(TraitsSnapshotService::class);
        $this->serialGenerator = Mockery::mock(SerialGenerator::class);
        $this->ultraLogManager = Mockery::mock(UltraLogManager::class);
        $this->errorManager = Mockery::mock(ErrorManagerInterface::class);
        $this->auditLogService = Mockery::mock(AuditLogService::class);

        // Configura i mock per non fare operazioni reali
        $this->ultraLogManager->shouldReceive('logInfo')->andReturn(true);
        $this->ultraLogManager->shouldReceive('logError')->andReturn(true);
        $this->ultraLogManager->shouldReceive('error')->andReturn(true);
        $this->errorManager->shouldReceive('handleError')->andReturn(true);
        $this->auditLogService->shouldReceive('logAction')->andReturn(true);

        $this->coaIssueService = new CoaIssueService(
            $this->ultraLogManager,
            $this->errorManager,
            $this->auditLogService,
            $this->traitsSnapshotService,
            $this->serialGenerator
        );
    }

    protected function tearDown(): void {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Crea un mock del modello Coa
     */
    private function createMockCoa(array $attributes = []): Coa {
        $coa = Mockery::mock(Coa::class);
        $user = Mockery::mock(User::class);

        $defaultAttributes = [
            'id' => 1,
            'serial' => 'EGI-TEST-123',
            'issuer_user_id' => 1,
        ];

        $attributes = array_merge($defaultAttributes, $attributes);

        $user->shouldReceive('getAttribute')->with('name')->andReturn($attributes['user_name'] ?? 'Test User');
        $user->shouldReceive('getAttribute')->with('email')->andReturn($attributes['user_email'] ?? 'test@example.com');

        $coa->shouldReceive('getAttribute')->with('id')->andReturn($attributes['id']);
        $coa->shouldReceive('getAttribute')->with('serial')->andReturn($attributes['serial']);
        $coa->shouldReceive('getAttribute')->with('issuer_user_id')->andReturn($attributes['issuer_user_id']);
        $coa->shouldReceive('getAttribute')->with('user')->andReturn($user);

        return $coa;
    }

    /**
     * Crea un mock del modello CoaSnapshot
     */
    private function createMockSnapshot(array $data = []): CoaSnapshot {
        $snapshot = Mockery::mock(CoaSnapshot::class);

        $defaultData = [
            'traits' => [
                ['name' => 'Color', 'value' => 'Blue'],
                ['name' => 'Size', 'value' => 'Large']
            ],
            'metadata' => [
                'created_at' => '2025-01-01 12:00:00',
                'version' => '1.0'
            ]
        ];

        $snapshotData = array_merge($defaultData, $data);

        $snapshot->shouldReceive('getAttribute')->with('snapshot_data')->andReturn(json_encode($snapshotData));

        return $snapshot;
    }

    /**
     * Test che verifica la generazione corretta dell'hash di verifica
     */
    public function test_verification_hash_generation() {
        $coa = $this->createMockCoa();
        $snapshot = $this->createMockSnapshot();
        $issuerInfo = ['id' => 1, 'name' => 'Test User', 'email' => 'test@example.com'];

        // Usa reflection per accedere al metodo privato
        $reflection = new ReflectionClass($this->coaIssueService);
        $method = $reflection->getMethod('calculateVerificationHash');
        $method->setAccessible(true);

        // Esegui il calcolo hash
        $hash = $method->invoke($this->coaIssueService, $coa, $snapshot, $issuerInfo);

        // Verifica il risultato
        $this->assertNotEmpty($hash);
        $this->assertEquals(64, strlen($hash)); // SHA-256 produce 64 caratteri esadecimali
        $this->assertMatchesRegularExpression('/^[a-f0-9]{64}$/', $hash); // Solo caratteri esadecimali
    }

    /**
     * Test che verifica la consistenza dell'hash con dati identici
     */
    public function test_hash_consistency() {
        $coa = $this->createMockCoa([
            'serial' => 'EGI-TEST-456',
            'issuer_user_id' => 2,
            'user_name' => 'Another User',
            'user_email' => 'another@example.com'
        ]);

        $snapshot = $this->createMockSnapshot([
            'traits' => [
                ['name' => 'Material', 'value' => 'Gold'],
                ['name' => 'Weight', 'value' => '100g']
            ]
        ]);

        $issuerInfo = ['id' => 2, 'name' => 'Another User', 'email' => 'another@example.com'];

        $reflection = new ReflectionClass($this->coaIssueService);
        $method = $reflection->getMethod('calculateVerificationHash');
        $method->setAccessible(true);

        // Calcola hash multiple volte
        $hash1 = $method->invoke($this->coaIssueService, $coa, $snapshot, $issuerInfo);
        $hash2 = $method->invoke($this->coaIssueService, $coa, $snapshot, $issuerInfo);
        $hash3 = $method->invoke($this->coaIssueService, $coa, $snapshot, $issuerInfo);

        // Verifica consistenza
        $this->assertEquals($hash1, $hash2);
        $this->assertEquals($hash2, $hash3);
    }

    /**
     * Test che verifica che dati diversi producano hash diversi
     */
    public function test_hash_changes_with_different_data() {
        $reflection = new ReflectionClass($this->coaIssueService);
        $method = $reflection->getMethod('calculateVerificationHash');
        $method->setAccessible(true);

        // Primo set di dati
        $coa1 = $this->createMockCoa([
            'serial' => 'EGI-TEST-111',
            'issuer_user_id' => 1,
            'user_name' => 'User One',
            'user_email' => 'user1@example.com'
        ]);

        $snapshot1 = $this->createMockSnapshot([
            'traits' => [['name' => 'A', 'value' => 'B']]
        ]);

        $issuerInfo1 = ['id' => 1, 'name' => 'User One', 'email' => 'user1@example.com'];

        // Secondo set di dati (differente)
        $coa2 = $this->createMockCoa([
            'serial' => 'EGI-TEST-222',
            'issuer_user_id' => 2,
            'user_name' => 'User Two',
            'user_email' => 'user2@example.com'
        ]);

        $snapshot2 = $this->createMockSnapshot([
            'traits' => [['name' => 'C', 'value' => 'D']]
        ]);

        $issuerInfo2 = ['id' => 2, 'name' => 'User Two', 'email' => 'user2@example.com'];

        $hash1 = $method->invoke($this->coaIssueService, $coa1, $snapshot1, $issuerInfo1);
        $hash2 = $method->invoke($this->coaIssueService, $coa2, $snapshot2, $issuerInfo2);

        // Hash diversi per dati diversi
        $this->assertNotEquals($hash1, $hash2);
    }

    /**
     * Test rilevamento modifiche nei dati (tampering detection)
     */
    public function test_tampering_detection() {
        $reflection = new ReflectionClass($this->coaIssueService);
        $method = $reflection->getMethod('calculateVerificationHash');
        $method->setAccessible(true);

        // Dati originali
        $originalCoa = $this->createMockCoa([
            'serial' => 'EGI-TEST-TAMPER',
            'issuer_user_id' => 3,
            'user_name' => 'Original User',
            'user_email' => 'original@example.com'
        ]);

        $originalSnapshot = $this->createMockSnapshot([
            'traits' => [['name' => 'Original', 'value' => 'Value']]
        ]);

        $originalIssuerInfo = ['id' => 3, 'name' => 'Original User', 'email' => 'original@example.com'];

        // Dati modificati (simulando tampering)
        $tamperedCoa = $this->createMockCoa([
            'serial' => 'EGI-TEST-TAMPER', // Stesso serial
            'issuer_user_id' => 3, // Stesso issuer
            'user_name' => 'MODIFIED User', // Nome modificato
            'user_email' => 'original@example.com'
        ]);

        $tamperedIssuerInfo = ['id' => 3, 'name' => 'MODIFIED User', 'email' => 'original@example.com'];

        $originalHash = $method->invoke($this->coaIssueService, $originalCoa, $originalSnapshot, $originalIssuerInfo);
        $tamperedHash = $method->invoke($this->coaIssueService, $tamperedCoa, $originalSnapshot, $tamperedIssuerInfo);

        // Hash dovrebbe essere diverso se i dati sono stati modificati
        $this->assertNotEquals($originalHash, $tamperedHash);
    }

    /**
     * Test gestione valori null/vuoti
     */
    public function test_handles_null_values() {
        $reflection = new ReflectionClass($this->coaIssueService);
        $method = $reflection->getMethod('calculateVerificationHash');
        $method->setAccessible(true);

        // Dati con alcuni valori null
        $coa = $this->createMockCoa([
            'serial' => 'EGI-TEST-NULL',
            'issuer_user_id' => 1,
            'user_name' => null,
            'user_email' => 'test@example.com'
        ]);

        $snapshot = $this->createMockSnapshot([
            'traits' => []
        ]);

        $issuerInfo = ['id' => 1, 'name' => null, 'email' => 'test@example.com'];

        $hash = $method->invoke($this->coaIssueService, $coa, $snapshot, $issuerInfo);

        // Dovrebbe comunque generare un hash valido
        $this->assertNotEmpty($hash);
        $this->assertEquals(64, strlen($hash));
    }

    /**
     * Test formato hash SHA-256
     */
    public function test_hash_format_is_sha256() {
        $reflection = new ReflectionClass($this->coaIssueService);
        $method = $reflection->getMethod('calculateVerificationHash');
        $method->setAccessible(true);

        $coa = $this->createMockCoa([
            'serial' => 'EGI-FORMAT-TEST',
            'issuer_user_id' => 1,
            'user_name' => 'Format User',
            'user_email' => 'format@example.com'
        ]);

        $snapshot = $this->createMockSnapshot([
            'test' => 'data'
        ]);

        $issuerInfo = ['id' => 1, 'name' => 'Format User', 'email' => 'format@example.com'];

        $hash = $method->invoke($this->coaIssueService, $coa, $snapshot, $issuerInfo);

        // Verifica formato SHA-256
        $this->assertIsString($hash);
        $this->assertEquals(64, strlen($hash));
        $this->assertMatchesRegularExpression('/^[a-f0-9]{64}$/', $hash);

        // Verifica che sia diverso da hash vuoto o di default
        $this->assertNotEquals(hash('sha256', ''), $hash);
    }
}
