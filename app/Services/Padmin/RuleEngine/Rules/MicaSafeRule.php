<?php

declare(strict_types=1);

namespace App\Services\Padmin\RuleEngine\Rules;

use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;

/**
 * MiCA_SAFE Rule Checker
 *
 * Detects potential MiCA violations (crypto custody/exchange features):
 * - Crypto wallet custody for users
 * - Crypto exchange functionality
 * - Private key management
 * - Crypto payment processing
 *
 * @package App\Services\Padmin\RuleEngine\Rules
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Rule Engine)
 * @date 2025-10-23
 * @purpose Ensure MiCA compliance - no crypto custody/exchange
 */
class MicaSafeRule implements RuleInterface {
    /**
     * Dangerous keywords that suggest MiCA violations
     */
    protected array $dangerousKeywords = [
        'custodial_wallet',
        'custody',
        'private_key',
        'exchange_crypto',
        'crypto_payment',
        'algo_custody',
        'usdc_custody',
        'wallet_custody',
    ];

    /**
     * Dangerous method patterns
     */
    protected array $dangerousMethods = [
        'custodyUserCrypto',
        'exchangeCrypto',
        'processUserCryptoPayment',
        'storePrivateKey',
        'manageCryptoAssets',
    ];

    public function check(array $ast, string $filePath, string $code): array {
        $violations = [];

        // Check for dangerous keywords in code
        foreach ($this->dangerousKeywords as $keyword) {
            if (stripos($code, $keyword) !== false) {
                // Find line number
                $lines = explode("\n", $code);
                foreach ($lines as $lineNum => $line) {
                    if (stripos($line, $keyword) !== false) {
                        $violations[] = [
                            'type' => 'MICA_SAFE_KEYWORD',
                            'message' => "Dangerous keyword detected: '{$keyword}' - May violate MiCA compliance",
                            'line' => $lineNum + 1,
                            'priority' => 'P0',
                            'severity' => 'critical',
                            'context' => [
                                'keyword' => $keyword,
                                'file' => $filePath,
                                'rule' => 'MiCA-SAFE: No crypto custody or exchange features',
                            ],
                        ];
                        break; // Only report first occurrence per keyword
                    }
                }
            }
        }

        // Check AST for dangerous method names
        $traverser = new NodeTraverser();
        $visitor = new class($this->dangerousMethods, $filePath) extends NodeVisitorAbstract {
            protected array $dangerousMethods;
            protected string $filePath;
            public array $violations = [];

            public function __construct(array $dangerousMethods, string $filePath) {
                $this->dangerousMethods = $dangerousMethods;
                $this->filePath = $filePath;
            }

            public function enterNode(Node $node) {
                // Check method definitions
                if ($node instanceof Node\Stmt\ClassMethod) {
                    $methodName = $node->name->toString();

                    foreach ($this->dangerousMethods as $dangerous) {
                        if (stripos($methodName, $dangerous) !== false) {
                            $this->violations[] = [
                                'type' => 'MICA_SAFE_METHOD',
                                'message' => "Dangerous method name: {$methodName}() - May violate MiCA compliance",
                                'line' => $node->getLine(),
                                'priority' => 'P0',
                                'severity' => 'critical',
                                'context' => [
                                    'method' => $methodName,
                                    'file' => $this->filePath,
                                    'rule' => 'MiCA-SAFE: Platform must not custody crypto or act as exchange',
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

        $violations = array_merge($violations, $visitor->violations);

        return $violations;
    }

    public function getName(): string {
        return 'MICA_SAFE';
    }

    public function getDescription(): string {
        return 'Ensures platform remains MiCA-compliant (no crypto custody/exchange)';
    }
}
