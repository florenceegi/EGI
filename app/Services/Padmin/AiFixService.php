<?php

declare(strict_types=1);

namespace App\Services\Padmin;

use function preg_match;
use function preg_split;

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

            // Prepara snippet originale: usa codeSnippet, altrimenti estrai dal filePath
            $originalCode = $violation['codeSnippet'] ?? '';
            if ($originalCode === '' && !empty($violation['filePath'] ?? null) && !empty($violation['line'] ?? null)) {
                $originalCode = $this->extractContextSnippet(
                    (string)$violation['filePath'],
                    (int)$violation['line'],
                    14
                );
            }

            // Build user prompt con violation details e snippet
            $userPrompt = $this->buildUserPrompt($violation, $originalCode);

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
                'original_code' => $originalCode
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
    private function buildUserPrompt(array $violation, string $code): string {
        $file = basename($violation['filePath'] ?? ($violation['file'] ?? 'unknown'));
        $line = $violation['line'] ?? 'unknown';
        $rule = $violation['rule'] ?? 'unknown';
        $message = $violation['message'] ?? 'unknown';

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
        // Preferred: between ```php and ```
        $startTag = '```php';
        $endTag = '```';
        $start = stripos($response, $startTag);
        if ($start !== false) {
            $start += strlen($startTag);
            $end = stripos($response, $endTag, $start);
            if ($end !== false) {
                return trim(substr($response, $start, $end - $start));
            }
        }

        // Fallback: between FIXED CODE: and EXPLANATION:
        $fc = stripos($response, 'FIXED CODE:');
        $ex = stripos($response, 'EXPLANATION:');
        if ($fc !== false && $ex !== false && $ex > $fc) {
            $fc += strlen('FIXED CODE:');
            return trim(substr($response, $fc, $ex - $fc));
        }

        // Last resort: return full response
        return $response;
    }

    /**
     * Extract explanation from Claude response
     */
    private function extractExplanation(string $response): string {
        // Extract text after EXPLANATION:
        $pos = stripos($response, 'EXPLANATION:');
        if ($pos !== false) {
            return trim(substr($response, $pos + strlen('EXPLANATION:')));
        }

        // Fallback: extract text before code block
        $codePos = stripos($response, '```php');
        if ($codePos !== false) {
            return trim(substr($response, 0, $codePos));
        }

        return 'Fix applied by AI.';
    }

    /**
     * Estrae uno snippet di contesto attorno alla linea indicata.
     * Ritorna stringa vuota se il file non è leggibile.
     */
    private function extractContextSnippet(string $filePath, int $line, int $radius = 12): string {
        try {
            if (!is_file($filePath) || !is_readable($filePath)) {
                return '';
            }

            $contents = file_get_contents($filePath);
            if ($contents === false) {
                return '';
            }

            $normalized = str_replace(["\r\n", "\r"], "\n", $contents);
            $lines = explode("\n", $normalized);
            if (!$lines) {
                return '';
            }

            $idx = max(1, $line);
            $start = max(1, $idx - $radius);
            $end = min(count($lines), $idx + $radius);

            $slice = array_slice($lines, $start - 1, $end - $start + 1);
            return implode("\n", $slice);
        } catch (\Throwable $e) {
            $this->logger->warning('[AiFixService] Failed to extract context snippet', [
                'file' => $filePath,
                'line' => $line,
                'error' => $e->getMessage(),
            ]);
            return '';
        }
    }
}
