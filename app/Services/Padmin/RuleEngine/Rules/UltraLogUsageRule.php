<?php

declare(strict_types=1);

namespace App\Services\Padmin\RuleEngine\Rules;

use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;

/**
 * UltraLog Usage Rule
 *
 * Scopo: Segnalare uso diretto del Facade Illuminate\\Support\\Facades\\Log
 * in favore di UltraLogManager in dependency injection ($this->logger).
 *
 * Stato: P1 (non blocking) - severità warning
 */
class UltraLogUsageRule implements RuleInterface {
    public function check(array $ast, string $filePath, string $code): array {
        $violations = [];

        $traverser = new NodeTraverser();
        $visitor = new class($filePath, $code) extends NodeVisitorAbstract {
            public string $filePath;
            public string $code;
            public array $lines;
            public array $violations = [];

            public function __construct(string $filePath, string $code) {
                $this->filePath = $filePath;
                $this->code = $code;
                $this->lines = explode("\n", $code);
            }

            public function enterNode(Node $node) {
                // Rileva chiamate statiche a Log::*
                if ($node instanceof Node\Expr\StaticCall) {
                    // class può essere Name (Log) o FullyQualified (Illuminate\\Support\\Facades\\Log)
                    if ($node->class instanceof Node\Name) {
                        $className = $node->class->toString();
                        if ($className === 'Log' || str_ends_with($className, '\\Log')) {
                            $this->violations[] = $this->buildViolation(
                                $node->getStartLine(),
                                'Use UltraLogManager via dependency injection instead of Facade Log::*'
                            );
                        }
                    }
                }

                // Rileva import/uso del Facade Log
                if ($node instanceof Node\Stmt\Use_) {
                    foreach ($node->uses as $use) {
                        $name = $use->name->toString();
                        if ($name === 'Illuminate\\Support\\Facades\\Log') {
                            $this->violations[] = $this->buildViolation(
                                $node->getStartLine(),
                                'Facade Log imported; prefer UltraLogManager ($this->logger)'
                            );
                        }
                    }
                }

                return null;
            }

            private function buildViolation(int $line, string $message): array {
                return [
                    'rule' => 'ULTRA_LOG_USAGE',
                    'type' => 'LOG_FACADE_USED',
                    'priority' => 'P1',
                    'severity' => 'warning',
                    'file' => basename($this->filePath),
                    'line' => $line,
                    'message' => $message,
                    'code_snippet' => $this->getCodeSnippet($line),
                ];
            }

            private function getCodeSnippet(int $line): string {
                $idx = $line - 1;
                return $this->lines[$idx] ?? '';
            }
        };

        $traverser->addVisitor($visitor);
        $traverser->traverse($ast);

        return $visitor->violations;
    }

    public function getName(): string {
        return 'ULTRA_LOG_USAGE';
    }

    public function getDescription(): string {
        return 'Prefer UltraLogManager via DI over Illuminate\\Support\\Facades\\Log';
    }
}
