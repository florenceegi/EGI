<?php

declare(strict_types=1);

namespace App\Services\Padmin\RuleEngine\Rules;

use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;

/**
 * STATISTICS Rule Checker
 *
 * Detects violations of "No hidden limits" rule in StatisticsService:
 * - Methods with ->take() or ->limit() without explicit parameter
 * - Hidden data truncation in stats/analytics services
 *
 * @package App\Services\Padmin\RuleEngine\Rules
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Rule Engine)
 * @date 2025-10-23
 * @purpose Ensure statistics show complete data by default
 */
class StatisticsRule implements RuleInterface {
    public function check(array $ast, string $filePath, string $code): array {
        $violations = [];

        // Only check files with "Statistics" or "Analytics" in name
        if (stripos($filePath, 'Statistics') === false && stripos($filePath, 'Analytics') === false) {
            return [];
        }

        $traverser = new NodeTraverser();
        $visitor = new class($filePath) extends NodeVisitorAbstract {
            protected string $filePath;
            public array $violations = [];
            protected ?string $currentMethodName = null;
            protected bool $currentMethodHasLimitParam = false;

            public function __construct(string $filePath) {
                $this->filePath = $filePath;
            }

            public function enterNode(Node $node) {
                // Track current method
                if ($node instanceof Node\Stmt\ClassMethod) {
                    $this->currentMethodName = $node->name->toString();
                    $this->currentMethodHasLimitParam = false;

                    // Check if method has $limit parameter
                    foreach ($node->params as $param) {
                        if (
                            $param->var instanceof Node\Expr\Variable &&
                            $param->var->name === 'limit'
                        ) {
                            $this->currentMethodHasLimitParam = true;
                            break;
                        }
                    }
                }

                // Detect ->take() or ->limit() calls
                if ($node instanceof Node\Expr\MethodCall) {
                    if ($node->name instanceof Node\Identifier) {
                        $methodName = $node->name->toString();

                        if (in_array($methodName, ['take', 'limit', 'first'])) {
                            // Check if this is a hardcoded limit (not using a variable)
                            $isHardcoded = false;

                            if (!empty($node->args)) {
                                $arg = $node->args[0]->value;
                                if ($arg instanceof Node\Scalar\LNumber) {
                                    $isHardcoded = true;
                                }
                            } elseif ($methodName === 'first') {
                                // first() is always limiting to 1
                                $isHardcoded = true;
                            }

                            if ($isHardcoded && !$this->currentMethodHasLimitParam) {
                                $this->violations[] = [
                                    'type' => 'STATISTICS_HIDDEN_LIMIT',
                                    'message' => "Hidden limit detected in {$this->currentMethodName}(): ->{$methodName}() without explicit \$limit parameter",
                                    'line' => $node->getLine(),
                                    'priority' => 'P0',
                                    'severity' => 'critical',
                                    'context' => [
                                        'method' => $this->currentMethodName,
                                        'limit_call' => $methodName,
                                        'file' => $this->filePath,
                                        'fix' => 'Add ?int $limit = null parameter and use conditional limit',
                                    ],
                                ];
                            }
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
        return 'STATISTICS';
    }

    public function getDescription(): string {
        return 'Ensures statistics services show complete data without hidden limits';
    }
}
