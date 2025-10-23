<?php

namespace App\Services\Padmin\RuleEngine\Rules;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

/**
 * GDPR Compliance Rule - P0 BLOCKING
 *
 * Verifica che ogni modifica a dati personali (User model) sia protetta da:
 * 1. Check consent con ConsentService->hasConsent()
 * 2. Audit trail con AuditLogService->logUserAction() o logGdprAction()
 * 3. Dependency injection di ConsentService e AuditLogService
 *
 * @package App\Services\Padmin\RuleEngine\Rules
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - GDPR Compliance)
 * @date 2025-10-23
 * @purpose Enforce GDPR compliance for personal data modifications
 */
class GdprComplianceRule implements RuleInterface {
    public function getName(): string {
        return 'GDPR_COMPLIANCE';
    }

    public function getDescription(): string {
        return 'Verifica GDPR compliance: consent check + audit trail per modifiche dati personali';
    }

    public function check(array $ast, string $filePath, string $code): array {
        $violations = [];

        // Visitor per analizzare l'AST
        $visitor = new class($violations, $filePath, $code) extends NodeVisitorAbstract {
            private array $violations;
            private string $filePath;
            private string $code;
            private array $lines;

            // Tracking per dependency injection
            private bool $hasConsentService = false;
            private bool $hasAuditService = false;

            // Tracking per metodo corrente
            private ?string $currentMethod = null;
            private bool $currentMethodHasConsentCheck = false;
            private bool $currentMethodHasAuditLog = false;
            private array $userModifications = [];

            public function __construct(&$violations, string $filePath, string $code) {
                $this->violations = &$violations;
                $this->filePath = $filePath;
                $this->code = $code;
                $this->lines = explode("\n", $code);
            }

            public function enterNode(Node $node) {
                // 1. CHECK DEPENDENCY INJECTION
                if ($node instanceof Node\Stmt\Property) {
                    $propertyType = $node->type;
                    if ($propertyType instanceof Node\Name || $propertyType instanceof Node\Identifier) {
                        $typeName = (string)$propertyType;

                        if (str_contains($typeName, 'ConsentService')) {
                            $this->hasConsentService = true;
                        }
                        if (str_contains($typeName, 'AuditLogService') || str_contains($typeName, 'AuditService')) {
                            $this->hasAuditService = true;
                        }
                    }
                }

                // 2. TRACK CURRENT METHOD
                if ($node instanceof Node\Stmt\ClassMethod) {
                    $this->currentMethod = $node->name->toString();
                    $this->currentMethodHasConsentCheck = false;
                    $this->currentMethodHasAuditLog = false;
                    $this->userModifications = [];
                }

                // 3. DETECT CONSENT CHECKS
                if ($node instanceof Node\Expr\MethodCall) {
                    if ($node->name instanceof Node\Identifier) {
                        $methodName = $node->name->toString();

                        // Check per hasConsent()
                        if ($methodName === 'hasConsent') {
                            $this->currentMethodHasConsentCheck = true;
                        }

                        // Check per audit logging
                        if (in_array($methodName, ['logUserAction', 'logGdprAction', 'logActivity'])) {
                            $this->currentMethodHasAuditLog = true;
                        }
                    }
                }

                // 4. DETECT USER MODEL MODIFICATIONS
                if ($node instanceof Node\Expr\MethodCall) {
                    $methodName = $node->name instanceof Node\Identifier
                        ? $node->name->toString()
                        : '';

                    // Operazioni di modifica su User model
                    if (in_array($methodName, ['update', 'save', 'fill', 'forceFill', 'saveQuietly'])) {
                        // Verifica se è su variabile $user o User::
                        $isUserOperation = false;

                        if ($node->var instanceof Node\Expr\Variable) {
                            $varName = $node->var->name;
                            if (is_string($varName) && str_contains(strtolower($varName), 'user')) {
                                $isUserOperation = true;
                            }
                        }

                        if ($isUserOperation) {
                            $this->userModifications[] = [
                                'method' => $methodName,
                                'line' => $node->getStartLine(),
                            ];
                        }
                    }
                }

                // 5. DETECT User::query()->update() pattern
                if ($node instanceof Node\Expr\StaticCall) {
                    if ($node->class instanceof Node\Name) {
                        $className = $node->class->toString();
                        if ($className === 'User' || str_ends_with($className, '\\User')) {
                            $methodName = $node->name instanceof Node\Identifier
                                ? $node->name->toString()
                                : '';

                            if (in_array($methodName, ['query', 'where', 'find'])) {
                                // Potenziale User::query()->update()
                                // Traccia per analisi successiva
                                $this->userModifications[] = [
                                    'method' => 'query-chain',
                                    'line' => $node->getStartLine(),
                                ];
                            }
                        }
                    }
                }

                return null;
            }

            public function leaveNode(Node $node) {
                // Quando usciamo da un metodo, verifichiamo le violazioni
                if ($node instanceof Node\Stmt\ClassMethod) {
                    // Se ci sono modifiche a User senza consent/audit
                    foreach ($this->userModifications as $modification) {
                        $line = $modification['line'];
                        $method = $modification['method'];

                        // P0: Modifica User senza consent check
                        if (!$this->currentMethodHasConsentCheck) {
                            $this->violations[] = [
                                'rule' => 'GDPR_COMPLIANCE',
                                'type' => 'MISSING_CONSENT_CHECK',
                                'priority' => 'P0',
                                'file' => basename($this->filePath),
                                'line' => $line,
                                'message' => "User model modification ({$method}) without ConsentService->hasConsent() check - GDPR P0 violation",
                                'code_snippet' => $this->getCodeSnippet($line),
                            ];
                        }

                        // P0: Modifica User senza audit trail
                        if (!$this->currentMethodHasAuditLog) {
                            $this->violations[] = [
                                'rule' => 'GDPR_COMPLIANCE',
                                'type' => 'MISSING_AUDIT_TRAIL',
                                'priority' => 'P0',
                                'file' => basename($this->filePath),
                                'line' => $line,
                                'message' => "User model modification ({$method}) without AuditLogService->log*() - GDPR P0 violation",
                                'code_snippet' => $this->getCodeSnippet($line),
                            ];
                        }
                    }

                    // Reset per prossimo metodo
                    $this->currentMethod = null;
                    $this->currentMethodHasConsentCheck = false;
                    $this->currentMethodHasAuditLog = false;
                    $this->userModifications = [];
                }

                return null;
            }

            public function afterTraverse(array $nodes) {
                // P1: Se il file ha modifiche User ma manca dependency injection
                if (!empty($this->userModifications)) {
                    if (!$this->hasConsentService) {
                        $this->violations[] = [
                            'rule' => 'GDPR_COMPLIANCE',
                            'type' => 'MISSING_CONSENT_SERVICE_INJECTION',
                            'priority' => 'P1',
                            'file' => basename($this->filePath),
                            'line' => 1,
                            'message' => 'File modifies User data but missing ConsentService dependency injection',
                            'code_snippet' => '// Add: protected ConsentService $consentService;',
                        ];
                    }

                    if (!$this->hasAuditService) {
                        $this->violations[] = [
                            'rule' => 'GDPR_COMPLIANCE',
                            'type' => 'MISSING_AUDIT_SERVICE_INJECTION',
                            'priority' => 'P1',
                            'file' => basename($this->filePath),
                            'line' => 1,
                            'message' => 'File modifies User data but missing AuditLogService dependency injection',
                            'code_snippet' => '// Add: protected AuditLogService $auditService;',
                        ];
                    }
                }

                return null;
            }

            private function getCodeSnippet(int $line): string {
                $index = $line - 1;
                if ($index >= 0 && $index < count($this->lines)) {
                    return trim($this->lines[$index]);
                }
                return '';
            }

            public function getViolations(): array {
                return $this->violations;
            }
        };

        // Esegui traversal AST
        $traverser = new \PhpParser\NodeTraverser();
        $traverser->addVisitor($visitor);
        $traverser->traverse($ast);

        return $visitor->getViolations();
    }
}
