<?php

namespace App\Services\Padmin;

use Illuminate\Support\Facades\Process;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use App\Services\Gdpr\AuditLogService;
use App\Enums\Gdpr\GdprActivityCategory;
use App\Models\User;

/**
 * @package App\Services\Padmin
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Padmin Analyzer MVP)
 * @date 2025-01-22
 * @purpose Laravel bridge to TypeScript Padmin Analyzer (OS3 Guardian) via Node.js
 *
 * ARCHITECTURE:
 * - PHP Service Layer: Questo file (dependency injection ULM/UEM/GDPR)
 * - Node.js Bridge: Executes TypeScript compiled code from tools/os3-guardian/dist/
 * - Redis Stack: Data persistence layer (port 6381)
 * - MiCA-safe: No crypto operations, only code analysis data
 *
 * SECURITY:
 * - UEM error handling for all Node.js execution failures
 * - ULM logging for all operations (trace + audit trail)
 * - GDPR audit for superadmin data access
 * - Input validation before Node.js execution
 *
 * INTEGRATION PATTERN:
 * PHP → executeNodeScript() → Symphony Process → node dist/cli.js <command> → JSON response → parse
 *
 * @dependencies
 * - Node.js 18+ installed
 * - tools/os3-guardian compiled (npm run build)
 * - Redis Stack running on port 6381
 * - .env PADMIN_* variables configured
 */
class PadminService {
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;
    protected AuditLogService $auditService;

    private string $nodeExecutable;
    private string $cliScriptPath;
    private int $nodeTimeout;
    private bool $redisStackEnabled;

    /**
     * PadminService constructor with GDPR/Ultra compliance.
     *
     * @param UltraLogManager $logger Ultra logging for trace/debug
     * @param ErrorManagerInterface $errorManager Ultra error manager
     * @param AuditLogService $auditService GDPR audit logging
     */
    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        AuditLogService $auditService
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->auditService = $auditService;

        // Node.js configuration
        $this->nodeExecutable = env('NODE_EXECUTABLE', 'node');
        $this->cliScriptPath = base_path('tools/os3-guardian/dist/cli.js');
        $this->nodeTimeout = (int) env('PADMIN_NODE_TIMEOUT', 30);
        $this->redisStackEnabled = env('PADMIN_REDIS_ENABLED', true);

        $this->logger->debug('[PadminService] Service initialized', [
            'node' => $this->nodeExecutable,
            'cli_path' => $this->cliScriptPath,
            'timeout' => $this->nodeTimeout,
            'redis_enabled' => $this->redisStackEnabled,
        ]);
    }

    /**
     * Execute Node.js script via Symphony Process component.
     *
     * @param string $command CLI command (e.g., "violations:list", "symbols:search")
     * @param array $args Command arguments as key-value pairs
     * @param User|null $user User for audit logging (optional)
     * @return array Parsed JSON response from Node.js
     * @throws \RuntimeException If Node.js execution fails
     *
     * @example
     * $result = $service->executeNodeScript('violations:list', ['priority' => 'P0']);
     * // Returns: ['success' => true, 'data' => [...violations...]]
     */
    protected function executeNodeScript(string $command, array $args = [], ?User $user = null): array {
        // ULM: Trace Node.js call start
        $this->logger->debug('[PadminService] Executing Node.js script', [
            'command' => $command,
            'args' => $args,
            'user_id' => $user?->id,
        ]);

        // Validate CLI script exists
        if (!file_exists($this->cliScriptPath)) {
            $this->errorManager->handle('PADMIN_CLI_NOT_FOUND', [
                'cli_path' => $this->cliScriptPath,
                'command' => $command,
            ]);

            throw new \RuntimeException('Padmin CLI script not found. Run: npm run build in tools/os3-guardian');
        }

        // Build command with arguments
        $cmdArgs = [$command];
        foreach ($args as $key => $value) {
            if (is_bool($value)) {
                if ($value) {
                    $cmdArgs[] = "--{$key}";
                }
            } else {
                $cmdArgs[] = "--{$key}=" . escapeshellarg((string)$value);
            }
        }

        // Execute Node.js process
        try {
            $result = Process::timeout($this->nodeTimeout)
                ->path(base_path('tools/os3-guardian'))
                ->run([$this->nodeExecutable, 'dist/cli.js', ...$cmdArgs]);

            // ULM: Log execution result
            $this->logger->info('[PadminService] Node.js execution completed', [
                'command' => $command,
                'exit_code' => $result->exitCode(),
                'duration_ms' => $result->runningTime() * 1000,
            ]);

            // Check exit code
            if (!$result->successful()) {
                $this->logger->error('[PadminService] Node.js execution failed', [
                    'command' => $command,
                    'exit_code' => $result->exitCode(),
                    'stderr' => $result->errorOutput(),
                    'stdout' => $result->output(),
                ]);

                // UEM: Handle execution error
                $this->errorManager->handle('PADMIN_NODE_EXECUTION_FAILED', [
                    'command' => $command,
                    'exit_code' => $result->exitCode(),
                    'error' => $result->errorOutput(),
                ]);

                throw new \RuntimeException('Node.js execution failed: ' . $result->errorOutput());
            }

            // Parse JSON response
            $output = trim($result->output());
            $data = json_decode($output, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->logger->error('[PadminService] JSON parse error', [
                    'command' => $command,
                    'output' => substr($output, 0, 500),
                    'json_error' => json_last_error_msg(),
                ]);

                $this->errorManager->handle('PADMIN_INVALID_JSON_RESPONSE', [
                    'command' => $command,
                    'json_error' => json_last_error_msg(),
                ]);

                throw new \RuntimeException('Invalid JSON response from Node.js');
            }

            // GDPR: Audit log if user provided
            if ($user) {
                $this->auditService->logUserAction(
                    $user,
                    "Padmin Analyzer: {$command}",
                    ['command' => $command, 'args' => $args],
                    GdprActivityCategory::ADMIN_ACTION
                );
            }

            return $data;
        } catch (\Symfony\Component\Process\Exception\ProcessTimedOutException $e) {
            $this->logger->error('[PadminService] Node.js timeout', [
                'command' => $command,
                'timeout' => $this->nodeTimeout,
            ]);

            $this->errorManager->handle('PADMIN_NODE_TIMEOUT', [
                'command' => $command,
                'timeout' => $this->nodeTimeout,
            ], $e);

            throw new \RuntimeException('Node.js execution timeout');
        }
    }

    /**
     * Get violations statistics aggregated by priority and severity.
     *
     * @param User|null $user User for audit logging
     * @return array Violation stats with counts by priority (P0-P3) and severity
     *
     * @example
     * $stats = $service->getViolationStats($user);
     * // Returns: [
     * //   'total' => 42,
     * //   'fixed' => 10,
     * //   'unfixed' => 32,
     * //   'byPriority' => ['P0' => 5, 'P1' => 12, 'P2' => 15, 'P3' => 10],
     * //   'bySeverity' => ['critical' => 5, 'error' => 20, 'warning' => 15, 'info' => 2]
     * // ]
     */
    public function getViolationStats(?User $user = null): array {
        $this->logger->debug('[PadminService] Getting violation statistics');

        try {
            $result = $this->executeNodeScript('violations:stats', [], $user);

            if (!isset($result['success']) || !$result['success']) {
                throw new \RuntimeException($result['error'] ?? 'Unknown error');
            }

            return $result['data'] ?? [];
        } catch (\Exception $e) {
            $this->logger->error('[PadminService] Failed to get violation stats', [
                'error' => $e->getMessage(),
            ]);

            // Return empty stats on error
            return [
                'total' => 0,
                'fixed' => 0,
                'unfixed' => 0,
                'byPriority' => ['P0' => 0, 'P1' => 0, 'P2' => 0, 'P3' => 0],
                'bySeverity' => ['critical' => 0, 'error' => 0, 'warning' => 0, 'info' => 0],
            ];
        }
    }

    /**
     * Get violations list with optional filtering.
     *
     * @param array $filters Filters: priority, severity, type, isFixed, limit
     * @param User|null $user User for audit logging
     * @return array Array of violations
     *
     * @example
     * $violations = $service->getViolations(['priority' => 'P0', 'isFixed' => false], $user);
     * // Returns: [
     * //   ['id' => 'v1', 'type' => 'REGOLA_ZERO', 'priority' => 'P0', ...],
     * //   ...
     * // ]
     */
    public function getViolations(array $filters = [], ?User $user = null): array {
        $this->logger->debug('[PadminService] Getting violations list', [
            'filters' => $filters,
        ]);

        try {
            $result = $this->executeNodeScript('violations:list', $filters, $user);

            if (!isset($result['success']) || !$result['success']) {
                throw new \RuntimeException($result['error'] ?? 'Unknown error');
            }

            return $result['data'] ?? [];
        } catch (\Exception $e) {
            $this->logger->error('[PadminService] Failed to get violations', [
                'filters' => $filters,
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Get single violation by ID.
     *
     * @param string $violationId Violation ID
     * @param User|null $user User for audit logging
     * @return array|null Violation data or null if not found
     */
    public function getViolation(string $violationId, ?User $user = null): ?array {
        $this->logger->debug('[PadminService] Getting violation by ID', [
            'violation_id' => $violationId,
        ]);

        try {
            $result = $this->executeNodeScript('violations:get', ['id' => $violationId], $user);

            if (!isset($result['success']) || !$result['success']) {
                return null;
            }

            return $result['data'] ?? null;
        } catch (\Exception $e) {
            $this->logger->error('[PadminService] Failed to get violation', [
                'violation_id' => $violationId,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Mark violation as fixed.
     *
     * @param string $violationId Violation ID
     * @param User $user User performing the action (required for GDPR audit)
     * @return bool True if marked successfully
     */
    public function markViolationFixed(string $violationId, User $user): bool {
        $this->logger->info('[PadminService] Marking violation as fixed', [
            'violation_id' => $violationId,
            'user_id' => $user->id,
        ]);

        try {
            $result = $this->executeNodeScript('violations:fix', ['id' => $violationId], $user);

            if (!isset($result['success']) || !$result['success']) {
                throw new \RuntimeException($result['error'] ?? 'Unknown error');
            }

            $this->logger->info('[PadminService] Violation marked as fixed', [
                'violation_id' => $violationId,
            ]);

            return true;
        } catch (\Exception $e) {
            $this->logger->error('[PadminService] Failed to mark violation as fixed', [
                'violation_id' => $violationId,
                'error' => $e->getMessage(),
            ]);

            $this->errorManager->handle('PADMIN_MARK_FIXED_FAILED', [
                'violation_id' => $violationId,
                'user_id' => $user->id,
            ], $e);

            return false;
        }
    }

    /**
     * Search code symbols with full-text search.
     *
     * @param array $query Search query: text, type, filePath, limit
     * @param User|null $user User for audit logging
     * @return array Array of matching symbols
     *
     * @example
     * $symbols = $service->searchSymbols(['text' => 'ConsentService', 'type' => 'class'], $user);
     * // Returns: [
     * //   ['id' => 's1', 'name' => 'ConsentService', 'type' => 'class', ...],
     * //   ...
     * // ]
     */
    public function searchSymbols(array $query = [], ?User $user = null): array {
        $this->logger->debug('[PadminService] Searching symbols', [
            'query' => $query,
        ]);

        try {
            $result = $this->executeNodeScript('symbols:search', $query, $user);

            if (!isset($result['success']) || !$result['success']) {
                throw new \RuntimeException($result['error'] ?? 'Unknown error');
            }

            return $result['data'] ?? [];
        } catch (\Exception $e) {
            $this->logger->error('[PadminService] Failed to search symbols', [
                'query' => $query,
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Get symbol by ID.
     *
     * @param string $symbolId Symbol ID
     * @param User|null $user User for audit logging
     * @return array|null Symbol data or null if not found
     */
    public function getSymbol(string $symbolId, ?User $user = null): ?array {
        $this->logger->debug('[PadminService] Getting symbol by ID', [
            'symbol_id' => $symbolId,
        ]);

        try {
            $result = $this->executeNodeScript('symbols:get', ['id' => $symbolId], $user);

            if (!isset($result['success']) || !$result['success']) {
                return null;
            }

            return $result['data'] ?? null;
        } catch (\Exception $e) {
            $this->logger->error('[PadminService] Failed to get symbol', [
                'symbol_id' => $symbolId,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Get symbol count.
     *
     * @param User|null $user User for audit logging
     * @return int Total symbol count
     */
    public function getSymbolCount(?User $user = null): int {
        $this->logger->debug('[PadminService] Getting symbol count');

        try {
            $result = $this->executeNodeScript('symbols:count', [], $user);

            if (!isset($result['success']) || !$result['success']) {
                return 0;
            }

            return (int) ($result['data']['count'] ?? 0);
        } catch (\Exception $e) {
            $this->logger->error('[PadminService] Failed to get symbol count', [
                'error' => $e->getMessage(),
            ]);

            return 0;
        }
    }

    /**
     * Ping Redis Stack connection health check.
     *
     * @return bool True if Redis Stack is reachable
     */
    public function pingRedisStack(): bool {
        if (!$this->redisStackEnabled) {
            return false;
        }

        try {
            $result = $this->executeNodeScript('health:ping', []);

            return isset($result['success']) && $result['success'];
        } catch (\Exception $e) {
            $this->logger->error('[PadminService] Redis Stack ping failed', [
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Get Padmin Analyzer health status.
     *
     * @return array Health status with Redis, Node.js, CLI availability
     *
     * @example
     * $health = $service->getHealthStatus();
     * // Returns: [
     * //   'redis_stack' => true,
     * //   'node_cli' => true,
     * //   'cli_path' => '/path/to/cli.js',
     * //   'version' => '1.0.0'
     * // ]
     */
    public function getHealthStatus(): array {
        return [
            'redis_stack' => $this->pingRedisStack(),
            'node_cli' => file_exists($this->cliScriptPath),
            'cli_path' => $this->cliScriptPath,
            'redis_enabled' => $this->redisStackEnabled,
            'node_executable' => $this->nodeExecutable,
            'timeout' => $this->nodeTimeout,
        ];
    }
}
