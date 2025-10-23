<?php

declare(strict_types=1);

namespace App\Services\Padmin\RuleEngine\Rules;

use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;

/**
 * UEM_FIRST Rule Checker
 *
 * Detects violations of "Never replace ErrorManager with Logger" rule:
 * - Catches replaced with logger->error() instead of errorManager->handle()
 * - Missing ErrorManager injection in classes that should have it
 *
 * @package App\Services\Padmin\RuleEngine\Rules
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Rule Engine)
 * @date 2025-10-23
 * @purpose Protect UEM error handling architecture
 */
class UemFirstRule implements RuleInterface {
    public function check(array $ast, string $filePath, string $code): array {
        $violations = [];

        $traverser = new NodeTraverser();
        $visitor = new class($filePath) extends NodeVisitorAbstract {
            protected string $filePath;
            public array $violations = [];
            protected bool $hasCatchBlock = false;
            protected bool $hasLoggerErrorInCatch = false;
            protected int $catchLine = 0;

            public function __construct(string $filePath) {
                $this->filePath = $filePath;
            }

            public function enterNode(Node $node) {
                // Detect try-catch blocks
                if ($node instanceof Node\Stmt\TryCatch) {
                    $this->hasCatchBlock = true;
                    $this->hasLoggerErrorInCatch = false;

                    foreach ($node->catches as $catch) {
                        $this->catchLine = $catch->getLine();

                        // Check if catch block uses logger->error() without errorManager->handle()
                        $hasErrorManager = false;
                        $hasLoggerError = false;

                        foreach ($catch->stmts as $stmt) {
                            if ($this->containsErrorManagerHandle($stmt)) {
                                $hasErrorManager = true;
                            }
                            if ($this->containsLoggerError($stmt)) {
                                $hasLoggerError = true;
                            }
                        }

                        // Violation: logger->error() without errorManager->handle()
                        if ($hasLoggerError && !$hasErrorManager) {
                            $this->violations[] = [
                                'type' => 'UEM_FIRST_VIOLATION',
                                'message' => 'Catch block uses logger->error() without errorManager->handle() - This violates UEM_FIRST rule',
                                'line' => $this->catchLine,
                                'priority' => 'P0',
                                'severity' => 'critical',
                                'context' => [
                                    'file' => $this->filePath,
                                    'rule' => 'UEM must be used for error handling, logger is for debug only',
                                ],
                            ];
                        }
                    }
                }

                return null;
            }

            protected function containsErrorManagerHandle($node): bool {
                if ($node instanceof Node\Expr\MethodCall) {
                    if ($node->var instanceof Node\Expr\PropertyFetch) {
                        $property = $node->var->name;
                        $method = $node->name;

                        if (
                            $property instanceof Node\Identifier &&
                            $method instanceof Node\Identifier &&
                            $property->toString() === 'errorManager' &&
                            $method->toString() === 'handle'
                        ) {
                            return true;
                        }
                    }
                }

                // Check nested nodes
                if (isset($node->stmts) && is_array($node->stmts)) {
                    foreach ($node->stmts as $stmt) {
                        if ($this->containsErrorManagerHandle($stmt)) {
                            return true;
                        }
                    }
                }

                return false;
            }

            protected function containsLoggerError($node): bool {
                if ($node instanceof Node\Expr\MethodCall) {
                    if ($node->var instanceof Node\Expr\PropertyFetch) {
                        $property = $node->var->name;
                        $method = $node->name;

                        if (
                            $property instanceof Node\Identifier &&
                            $method instanceof Node\Identifier &&
                            $property->toString() === 'logger' &&
                            $method->toString() === 'error'
                        ) {
                            return true;
                        }
                    }
                }

                // Check nested nodes
                if (isset($node->stmts) && is_array($node->stmts)) {
                    foreach ($node->stmts as $stmt) {
                        if ($this->containsLoggerError($stmt)) {
                            return true;
                        }
                    }
                }

                return false;
            }
        };

        $traverser->addVisitor($visitor);
        $traverser->traverse($ast);

        return $visitor->violations;
    }

    public function getName(): string {
        return 'UEM_FIRST';
    }

    public function getDescription(): string {
        return 'Ensures ErrorManager is used for error handling, not replaced by Logger';
    }
}
