<?php

declare(strict_types=1);

namespace App\Services\Padmin\RuleEngine;

use App\Services\Padmin\PadminService;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use PhpParser\ParserFactory;
use PhpParser\NodeTraverser;
use PhpParser\Node;

/**
 * Rule Engine Service - Automated Code Quality Analysis
 *
 * Scans PHP codebase for violations of Copilot Instructions rules:
 * - REGOLA_ZERO: No assumptions/deductions without verification
 * - UEM_FIRST: Never replace ErrorManager with Logger
 * - STATISTICS: No hidden limits in StatisticsService methods
 * - MiCA_SAFE: No crypto custody/exchange features
 *
 * @package App\Services\Padmin\RuleEngine
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Rule Engine)
 * @date 2025-10-23
 * @purpose Automated rule checking with AST parsing
 */
class RuleEngineService {
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;
    protected PadminService $padminService;
    protected array $rules = [];

    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        PadminService $padminService
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->padminService = $padminService;

        // Register rules
        $this->registerRules();
    }

    /**
     * Register all available rules
     */
    protected function registerRules(): void {
        $this->rules = [
            'REGOLA_ZERO' => new Rules\RegolaZeroRule(),
            'UEM_FIRST' => new Rules\UemFirstRule(),
            'STATISTICS' => new Rules\StatisticsRule(),
            'MICA_SAFE' => new Rules\MicaSafeRule(),
        ];
    }

    /**
     * Scan files for rule violations
     *
     * @param array $filePaths Array of file paths to scan
     * @param array $ruleNames Optional: specific rules to check (default: all)
     * @return array Violations found
     */
    public function scanFiles(array $filePaths, array $ruleNames = []): array {
        $this->logger->info('[RuleEngine] Scanning files', [
            'file_count' => count($filePaths),
            'rules' => $ruleNames ?: 'all',
        ]);

        $violations = [];
        $rulesToCheck = $this->getRulesToCheck($ruleNames);

        foreach ($filePaths as $filePath) {
            if (!file_exists($filePath)) {
                $this->logger->warning('[RuleEngine] File not found', ['path' => $filePath]);
                continue;
            }

            $fileViolations = $this->scanFile($filePath, $rulesToCheck);
            $violations = array_merge($violations, $fileViolations);
        }

        $this->logger->info('[RuleEngine] Scan completed', [
            'violations_found' => count($violations),
        ]);

        return $violations;
    }

    /**
     * Scan single file for violations
     *
     * @param string $filePath File to scan
     * @param array $rules Rules to apply
     * @return array Violations found in file
     */
    protected function scanFile(string $filePath, array $rules): array {
        $violations = [];
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);

        try {
            // PHP files only for now
            if ($extension !== 'php') {
                return [];
            }

            $code = file_get_contents($filePath);
            $ast = $this->parsePhpCode($code);

            foreach ($rules as $ruleName => $rule) {
                $ruleViolations = $rule->check($ast, $filePath, $code);

                foreach ($ruleViolations as $violation) {
                    $violations[] = [
                        'rule' => $ruleName,
                        'type' => $violation['type'],
                        'message' => $violation['message'],
                        'filePath' => $filePath,
                        'line' => $violation['line'] ?? null,
                        'priority' => $violation['priority'] ?? 'P2',
                        'severity' => $violation['severity'] ?? 'warning',
                        'context' => $violation['context'] ?? [],
                    ];
                }
            }
        } catch (\Exception $e) {
            $this->logger->error('[RuleEngine] Error scanning file', [
                'file' => $filePath,
                'error' => $e->getMessage(),
            ]);
        }

        return $violations;
    }

    /**
     * Parse PHP code into AST
     *
     * @param string $code PHP code to parse
     * @return array AST nodes
     */
    protected function parsePhpCode(string $code): array {
        $parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);

        try {
            $ast = $parser->parse($code);
            return $ast ?? [];
        } catch (\Exception $e) {
            $this->logger->error('[RuleEngine] Parse error', [
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    /**
     * Get rules to check based on names
     *
     * @param array $ruleNames Rule names to filter (empty = all)
     * @return array Filtered rules
     */
    protected function getRulesToCheck(array $ruleNames): array {
        if (empty($ruleNames)) {
            return $this->rules;
        }

        $filtered = [];
        foreach ($ruleNames as $name) {
            if (isset($this->rules[$name])) {
                $filtered[$name] = $this->rules[$name];
            }
        }

        return $filtered;
    }

    /**
     * Scan directory recursively
     *
     * @param string $directory Directory to scan
     * @param array $ruleNames Optional: specific rules to check
     * @param array $excludePatterns Patterns to exclude (default: vendor, node_modules, storage, tests)
     * @return array Violations found
     */
    public function scanDirectory(
        string $directory,
        array $ruleNames = [],
        array $excludePatterns = ['vendor', 'node_modules', 'storage', 'tests', 'database/migrations']
    ): array {
        $this->logger->info('[RuleEngine] Scanning directory', [
            'directory' => $directory,
        ]);

        $files = $this->getPhpFiles($directory, $excludePatterns);
        return $this->scanFiles($files, $ruleNames);
    }

    /**
     * Get all PHP files in directory recursively
     *
     * @param string $directory Directory to scan
     * @param array $excludePatterns Patterns to exclude
     * @return array PHP file paths
     */
    protected function getPhpFiles(string $directory, array $excludePatterns): array {
        $files = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $path = $file->getPathname();

                // Check exclude patterns
                $excluded = false;
                foreach ($excludePatterns as $pattern) {
                    if (strpos($path, $pattern) !== false) {
                        $excluded = true;
                        break;
                    }
                }

                if (!$excluded) {
                    $files[] = $path;
                }
            }
        }

        return $files;
    }

    /**
     * Store violations in Redis Stack via PadminService
     *
     * @param array $violations Violations to store
     * @param \App\Models\User $user User who triggered the scan
     * @return int Number of violations stored
     */
    public function storeViolations(array $violations, \App\Models\User $user): int {
        $stored = 0;

        foreach ($violations as $violation) {
            try {
                // Create violation via PadminService
                $this->padminService->createViolation([
                    'type' => $violation['type'],
                    'message' => $violation['message'],
                    'filePath' => $violation['filePath'],
                    'line' => $violation['line'],
                    'priority' => $violation['priority'],
                    'severity' => $violation['severity'],
                    'rule' => $violation['rule'],
                    'context' => $violation['context'],
                    'isFixed' => false,
                ], $user);

                $stored++;
            } catch (\Exception $e) {
                $this->logger->error('[RuleEngine] Failed to store violation', [
                    'violation' => $violation,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->logger->info('[RuleEngine] Violations stored', [
            'stored' => $stored,
            'total' => count($violations),
        ]);

        return $stored;
    }
}
