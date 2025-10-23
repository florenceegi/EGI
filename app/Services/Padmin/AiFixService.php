<?php

declare(strict_types=1);

namespace App\Services\Padmin;

use App\Services\AnthropicService;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * Servizio per generare fix AI delle violations usando Claude
 *
 * @package App\Services\Padmin
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0
 * @date 2025-10-23
 */
class AiFixService {
    protected AnthropicService $anthropic;
    protected UltraLogManager $logger;

    public function __construct(
        AnthropicService $anthropic,
        UltraLogManager $logger
    ) {
        $this->anthropic = $anthropic;
        $this->logger = $logger;
    }

    /**
     * Genera fix per una violation usando Claude
     */
    public function generateFix(array $violation): array {
        $this->logger->info('[AiFixService] Generating fix', [
            'violation_id' => $violation['id'] ?? 'unknown',
            'rule' => $violation['rule'] ?? 'unknown'
        ]);

        try {
            // Build system prompt con regole OS3.0
            $systemPrompt = $this->buildSystemPrompt();

            // Build user prompt con violation details
            $userPrompt = $this->buildUserPrompt($violation);

            // Context per Claude
            $context = [
                'project' => 'FlorenceEGI',
                'task' => 'code_fix',
                'rule_violated' => $violation['rule'] ?? 'unknown'
            ];

            // Call Claude
            $response = $this->anthropic->chat($userPrompt, $context, []);

            // Parse response
            $fixedCode = $this->extractCodeFromResponse($response);

            return [
                'success' => true,
                'fixed_code' => $fixedCode,
                'explanation' => $this->extractExplanation($response),
                'original_code' => $violation['codeSnippet'] ?? ''
            ];
        } catch (\Exception $e) {
            $this->logger->error('[AiFixService] Fix generation failed', [
                'error' => $e->getMessage(),
                'violation_id' => $violation['id'] ?? 'unknown'
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Build system prompt con regole OS3.0
     */
    private function buildSystemPrompt(): string {
        return <<<PROMPT
You are a senior Laravel developer fixing code quality violations in FlorenceEGI platform.

CRITICAL RULES (OS3.0):

1. REGOLA_ZERO (P0-BLOCKING):
   - NEVER invent methods that don't exist
   - Use ONLY these verified methods:
     * ConsentService: hasConsent(User \$user, string \$consentType): bool
     * AuditLogService: logUserAction(User \$user, string \$action, array \$context, GdprActivityCategory \$category)
     * ErrorManager: handle(string \$errorCode, array \$context, \Exception \$e)

2. UEM_FIRST (P0-BLOCKING):
   - Every catch block MUST call errorManager->handle()
   - NEVER use Log::error() or logger->error() for exceptions
   - Error codes must be registered in config/error-manager.php

3. STATISTICS (P0-BLOCKING):
   - NEVER use ->take() or ->limit() without explicit parameter in method signature
   - Example: public function getResults(?int \$limit = null)
   - Default behavior: return ALL records

4. GDPR_COMPLIANCE (P0-BLOCKING):
   - User model updates MUST check consent: consentService->hasConsent()
   - User model updates MUST log: auditService->logUserAction()
   - Inject ConsentService and AuditLogService in constructor

5. MiCA_SAFE (P0-BLOCKING):
   - NO custody of user crypto
   - NO exchange operations
   - ONLY mint NFTs on platform wallet

YOUR TASK:
- Fix the violation
- Return ONLY the corrected code block
- Add brief comment explaining the fix
- Preserve existing code structure and style
- DO NOT change unrelated code
PROMPT;
    }

    /**
     * Build user prompt con violation details
     */
    private function buildUserPrompt(array $violation): string {
        $file = basename($violation['filePath'] ?? ($violation['file'] ?? 'unknown'));
        $line = $violation['line'] ?? 'unknown';
        $rule = $violation['rule'] ?? 'unknown';
        $message = $violation['message'] ?? 'unknown';
        $code = $violation['codeSnippet'] ?? '';

        return <<<PROMPT
Fix this code quality violation:

FILE: {$file}
LINE: {$line}
RULE VIOLATED: {$rule}
ISSUE: {$message}

PROBLEMATIC CODE:
```php
{$code}
```

Please provide:
1. The FIXED code block (complete and ready to use)
2. Brief explanation of what you changed

Format your response as:
FIXED CODE:
```php
[your fixed code here]
```

EXPLANATION:
[brief explanation]
PROMPT;
    }

    /**
     * Extract fixed code from Claude response
     */
    private function extractCodeFromResponse(string $response): string {
        // Extract code between ```php and ```
        if (preg_match('/```php\s*(.*?)\s*```/s', $response, $matches)) {
            return trim($matches[1]);
        }

        // Fallback: extract code between FIXED CODE: and EXPLANATION:
        if (preg_match('/FIXED CODE:\s*(.*?)\s*EXPLANATION:/s', $response, $matches)) {
            return trim($matches[1]);
        }

        // Last resort: return full response
        return $response;
    }

    /**
     * Extract explanation from Claude response
     */
    private function extractExplanation(string $response): string {
        // Extract text after EXPLANATION:
        if (preg_match('/EXPLANATION:\s*(.*)/s', $response, $matches)) {
            return trim($matches[1]);
        }

        // Fallback: extract text before code block
        if (preg_match('/(.*?)```php/s', $response, $matches)) {
            return trim($matches[1]);
        }

        return 'Fix applied by AI.';
    }
}
