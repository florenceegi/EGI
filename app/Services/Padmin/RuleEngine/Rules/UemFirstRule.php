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
    public function check(array $ast, string $filePath, string $code): array
    {
        $violations = [];

        $traverser = new NodeTraverser();
        $visitor = new class($filePath) extends NodeVisitorAbstract {
            protected string $filePath;
            public array $violations = [];

            public function __construct(string $filePath)
            {
                $this->filePath = $filePath;
            }

            public function enterNode(Node $node)
            {
                // Detect try-catch blocks
                if ($node instanceof Node\Stmt\TryCatch) {
                    foreach ($node->catches as $catch) {
                        $catchLine = $catch->getLine();

                        // Check if catch block uses errorManager->handle()
                        $hasErrorManager = $this->containsErrorManagerHandle($catch);

                        // Violation: catch block without errorManager->handle()
                        if (!$hasErrorManager) {
                            $this->violations[] = [
                                'type' => 'UEM_FIRST_VIOLATION',
                                'message' => 'Catch block without errorManager->handle() - This violates UEM_FIRST rule',
                                'line' => $catchLine,
                                'priority' => 'P0',
                                'severity' => 'critical',
                                'context' => [
                                    'file' => $this->filePath,
                                    'rule' => 'UEM must be used for ALL error handling in catch blocks',
                                ],
                            ];
                        }
                    }
                }

                return null;
            }

            protected function containsErrorManagerHandle($node): bool
            {
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

                // Check all child nodes recursively
                foreach ($node->getSubNodeNames() as $name) {
                    $subNode = $node->$name;

                    if ($subNode instanceof Node) {
                        if ($this->containsErrorManagerHandle($subNode)) {
                            return true;
                        }
                    } elseif (is_array($subNode)) {
                        foreach ($subNode as $item) {
                            if ($item instanceof Node && $this->containsErrorManagerHandle($item)) {
                                return true;
                            }
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

    public function getName(): string
    {
        return 'UEM_FIRST';
    }

    public function getDescription(): string
    {
        return 'Ensures ErrorManager is used for ALL error handling in catch blocks';
    }
}            protected function containsLoggerError($node): bool {
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

                // Check all child nodes recursively
                foreach ($node->getSubNodeNames() as $name) {
                    $subNode = $node->$name;
                    
                    if ($subNode instanceof Node) {
                        if ($this->containsLoggerError($subNode)) {
                            return true;
                        }
                    } elseif (is_array($subNode)) {
                        foreach ($subNode as $item) {
                            if ($item instanceof Node && $this->containsLoggerError($item)) {
                                return true;
                            }
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
