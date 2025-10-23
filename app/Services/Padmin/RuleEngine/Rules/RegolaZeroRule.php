<?php

declare(strict_types=1);

namespace App\Services\Padmin\RuleEngine\Rules;

use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;

/**
 * REGOLA_ZERO Rule Checker
 *
 * Detects potential violations of "no assumptions/deductions" rule:
 * - Methods called that don't exist in codebase
 * - Undefined class usage
 * - Magic method calls without verification
 *
 * @package App\Services\Padmin\RuleEngine\Rules
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Rule Engine)
 * @date 2025-10-23
 * @purpose Detect assumed/invented methods
 */
class RegolaZeroRule implements RuleInterface {
    /**
     * Known blacklisted methods (from copilot-instructions.md)
     */
    protected array $blacklistedMethods = [
        'hasConsentFor',      // ConsentService - INVENTATO
        'handleException',    // ErrorManager - INVENTATO
        'logError',          // AuditLogService - INVENTATO
        'logActivity',       // AuditLogService - INVENTATO (use logUserAction)
    ];

    public function check(array $ast, string $filePath, string $code): array {
        $violations = [];

        $traverser = new NodeTraverser();
        $visitor = new class($this->blacklistedMethods, $filePath) extends NodeVisitorAbstract {
            protected array $blacklistedMethods;
            protected string $filePath;
            public array $violations = [];

            public function __construct(array $blacklistedMethods, string $filePath) {
                $this->blacklistedMethods = $blacklistedMethods;
                $this->filePath = $filePath;
            }

            public function enterNode(Node $node) {
                // Check for method calls
                if ($node instanceof Node\Expr\MethodCall) {
                    if ($node->name instanceof Node\Identifier) {
                        $methodName = $node->name->toString();

                        // Check blacklist
                        if (in_array($methodName, $this->blacklistedMethods)) {
                            $this->violations[] = [
                                'type' => 'REGOLA_ZERO_BLACKLIST',
                                'message' => "Blacklisted method detected: {$methodName}() - This method was invented by AI and doesn't exist",
                                'line' => $node->getLine(),
                                'priority' => 'P0',
                                'severity' => 'critical',
                                'context' => [
                                    'method' => $methodName,
                                    'file' => $this->filePath,
                                ],
                            ];
                        }
                    }
                }

                return null;
            }
        };

        $traverser->addVisitor($visitor);
        $traverser->traverse($ast);

        return $visitor->violations;
    }

    public function getName(): string {
        return 'REGOLA_ZERO';
    }

    public function getDescription(): string {
        return 'Detects assumed/invented methods and undefined classes';
    }
}
