# Padmin Analyzer - Architecture Deep Dive

**Enterprise-Grade Code Quality System for PA/Enterprise Applications**

---

## 📋 Table of Contents

1. [System Overview](#system-overview)
2. [Component Architecture](#component-architecture)
3. [Data Flow](#data-flow)
4. [Rule Engine Design](#rule-engine-design)
5. [Storage Strategy](#storage-strategy)
6. [Security & Compliance](#security--compliance)
7. [Performance Optimization](#performance-optimization)
8. [Future Architecture](#future-architecture)

---

## 🌐 System Overview

### High-Level Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                        PRESENTATION LAYER                        │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  ┌────────────┐  ┌────────────┐  ┌────────────┐  ┌──────────┐ │
│  │ Dashboard  │  │ Violations │  │  Symbols   │  │ Search   │ │
│  │   View     │  │    View    │  │    View    │  │   View   │ │
│  └─────┬──────┘  └─────┬──────┘  └─────┬──────┘  └─────┬────┘ │
│        │               │               │                │       │
│        └───────────────┴───────────────┴────────────────┘       │
│                              │                                   │
└──────────────────────────────┼───────────────────────────────────┘
                               │
                               ▼
┌─────────────────────────────────────────────────────────────────┐
│                      CONTROLLER LAYER                            │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │           PadminController (Laravel)                      │  │
│  ├──────────────────────────────────────────────────────────┤  │
│  │  + dashboard(): View                                      │  │
│  │  + violations(?filters): View                             │  │
│  │  + runScan(Request): JsonResponse                         │  │
│  │  + requestAiFix(string $id): JsonResponse                 │  │
│  │  + markFixed(string $id): JsonResponse                    │  │
│  │                                                            │  │
│  │  # mergeViolations(array $new, array $existing): array    │  │
│  │  # applyFilters(Collection $violations, array): Collection│  │
│  │  # buildAiFixPrompt(array $violation): string             │  │
│  └──────────────────┬───────────────────────────────────────┘  │
│                     │                                           │
└─────────────────────┼───────────────────────────────────────────┘
                      │
                      ▼
┌─────────────────────────────────────────────────────────────────┐
│                       SERVICE LAYER                              │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  ┌────────────────────────────────────────────────────────────┐ │
│  │              RuleEngineService                             │ │
│  ├────────────────────────────────────────────────────────────┤ │
│  │  + scanDirectory(string $path, array $rules): array       │ │
│  │  + scanFiles(array $files, array $rules): array           │ │
│  │  + scanFile(string $file, array $rules): array            │ │
│  │                                                            │ │
│  │  # parsePhpFile(string $content): array (AST nodes)       │ │
│  │  # applyRule(RuleInterface $rule, array $nodes): array    │ │
│  │  # formatViolation(array $raw): array                     │ │
│  └────────────┬───────────────────────────────────────────────┘ │
│               │                                                  │
│               │  ┌───────────────────────────────────────────┐  │
│               ├─→│    PadminService (Future)                 │  │
│               │  ├───────────────────────────────────────────┤  │
│               │  │  + getViolations(): Collection            │  │
│               │  │  + storeViolation(array $data): void      │  │
│               │  │  + getViolationById(string $id): ?array   │  │
│               │  │  + markAsFixed(string $id): bool          │  │
│               │  │                                            │  │
│               │  │  # executeNodeJsCli(string $cmd): array   │  │
│               │  └────────────┬──────────────────────────────┘  │
│               │               │                                  │
│               │  ┌────────────▼──────────────────────────────┐  │
│               └─→│    AuditLogService                        │  │
│                  ├───────────────────────────────────────────┤  │
│                  │  + logUserAction(...): UserActivity       │  │
│                  │  + logSecurityEvent(...): UserActivity    │  │
│                  │  + logGdprAction(...): UserActivity       │  │
│                  └───────────────────────────────────────────┘  │
│                                                                  │
└──────────────────────────────┬───────────────────────────────────┘
                               │
                               ▼
┌─────────────────────────────────────────────────────────────────┐
│                      RULE ENGINE LAYER                           │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │        nikic/php-parser v5.6.2 (AST Parser)              │  │
│  ├──────────────────────────────────────────────────────────┤  │
│  │  PhpParser\ParserFactory                                  │  │
│  │  └─→ create(PhpParser\ParserFactory::PREFER_PHP8)        │  │
│  │       └─→ parse(string $code): array<Node>               │  │
│  └──────────────────────┬───────────────────────────────────┘  │
│                         │                                        │
│        ┌────────────────┴────────────────┐                      │
│        ▼                                  ▼                      │
│  ┌─────────────┐                    ┌─────────────┐            │
│  │ RuleInterface                    │ Violation[] │            │
│  ├─────────────┤                    │ (DTO)       │            │
│  │ + check()   │                    └─────────────┘            │
│  │ + getName() │                                                │
│  └──────┬──────┘                                                │
│         │                                                        │
│         ├─→ RegolaZeroRule          (P0 - BLOCKING)            │
│         ├─→ UemFirstRule            (P0 - BLOCKING)            │
│         ├─→ StatisticsRule          (P0 - BLOCKING)            │
│         ├─→ MicaSafeRule            (P0 - BLOCKING)            │
│         └─→ GdprComplianceRule      (P0 - BLOCKING)            │
│                                                                  │
└──────────────────────────────┬───────────────────────────────────┘
                               │
                               ▼
┌─────────────────────────────────────────────────────────────────┐
│                       STORAGE LAYER                              │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  CURRENT IMPLEMENTATION:                                        │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │         Laravel Session Storage                           │  │
│  ├──────────────────────────────────────────────────────────┤  │
│  │  session('padmin_violations') => [                        │  │
│  │    {                                                      │  │
│  │      "id": "v_6720abc123",                                │  │
│  │      "rule": "REGOLA_ZERO",                               │  │
│  │      "severity": "P0",                                    │  │
│  │      "message": "...",                                    │  │
│  │      "file": "...",                                       │  │
│  │      "line": 42,                                          │  │
│  │      "codeSnippet": "...",                                │  │
│  │      "scanned_at": "2025-10-23T13:45:00Z",                │  │
│  │      "scanned_by": 1,                                     │  │
│  │      "is_fixed": false                                    │  │
│  │    }                                                      │  │
│  │  ]                                                        │  │
│  └──────────────────────────────────────────────────────────┘  │
│                                                                  │
│  FUTURE IMPLEMENTATION:                                         │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │           Redis Stack + Node.js CLI                       │  │
│  ├──────────────────────────────────────────────────────────┤  │
│  │  RediSearch Indexes:                                      │  │
│  │  - violations:by:rule                                     │  │
│  │  - violations:by:file                                     │  │
│  │  - violations:by:severity                                 │  │
│  │  - violations:by:user                                     │  │
│  │                                                            │  │
│  │  RedisJSON Documents:                                     │  │
│  │  - violation:{id}                                         │  │
│  │  - symbol:{class}:{method}                                │  │
│  │  - callgraph:{from}:{to}                                  │  │
│  └──────────────────────────────────────────────────────────┘  │
│                                                                  │
└──────────────────────────────────────────────────────────────────┘
```

---

## 🧩 Component Architecture

### 1. RuleEngineService

**Responsibility:** AST parsing e applicazione regole

**Key Methods:**

```php
class RuleEngineService
{
    private ParserFactory $parserFactory;
    private array $rules = [];

    public function __construct()
    {
        $this->parserFactory = new ParserFactory();

        // Carica regole disponibili
        $this->rules = [
            'REGOLA_ZERO' => new RegolaZeroRule(),
            'UEM_FIRST' => new UemFirstRule(),
            'STATISTICS' => new StatisticsRule(),
            'MICA_SAFE' => new MicaSafeRule(),
            'GDPR_COMPLIANCE' => new GdprComplianceRule(),
        ];
    }

    /**
     * Scansiona directory ricorsivamente
     *
     * @param string $path Path relativo da /home/fabio/EGI
     * @param array $rules Nomi regole da applicare (default: tutte)
     * @return array Violations trovate
     */
    public function scanDirectory(string $path, array $rules = []): array
    {
        $violations = [];
        $filesToScan = $this->getPhpFiles($path);

        foreach ($filesToScan as $file) {
            $violations = array_merge(
                $violations,
                $this->scanFile($file, $rules)
            );
        }

        return $violations;
    }

    /**
     * Scansiona singolo file
     *
     * @param string $filePath Path assoluto file
     * @param array $rules Regole da applicare
     * @return array Violations nel file
     */
    public function scanFile(string $filePath, array $rules = []): array
    {
        $content = file_get_contents($filePath);
        $parser = $this->parserFactory->create(ParserFactory::PREFER_PHP8);

        try {
            $ast = $parser->parse($content);
        } catch (Error $e) {
            // File ha syntax error
            return [[
                'rule' => 'SYNTAX_ERROR',
                'severity' => 'P0',
                'message' => 'Parse error: ' . $e->getMessage(),
                'file' => $filePath,
                'line' => $e->getStartLine(),
                'codeSnippet' => '',
            ]];
        }

        $violations = [];
        $rulesToApply = empty($rules)
            ? $this->rules
            : array_intersect_key($this->rules, array_flip($rules));

        foreach ($rulesToApply as $ruleName => $rule) {
            $ruleViolations = $rule->check($ast);

            foreach ($ruleViolations as $violation) {
                $violations[] = [
                    'rule' => $ruleName,
                    'severity' => $violation['severity'] ?? 'P2',
                    'message' => $violation['message'],
                    'file' => $filePath,
                    'line' => $violation['line'],
                    'codeSnippet' => $violation['codeSnippet'] ?? '',
                ];
            }
        }

        return $violations;
    }
}
```

**Dependencies:**

-   `nikic/php-parser ^5.6.2`
-   Rule classes implementing `RuleInterface`

**Performance:**

-   ~100-200ms per file (dipende da dimensione)
-   AST parsing è CPU-intensive
-   Cache AST planned per re-scans

---

### 2. Rule Classes

**Interface:**

```php
interface RuleInterface
{
    /**
     * Check AST nodes per violazioni
     *
     * @param array $nodes AST nodes da nikic/php-parser
     * @return array Violations trovate
     */
    public function check(array $nodes): array;

    /**
     * Nome regola
     */
    public function getName(): string;

    /**
     * Descrizione regola
     */
    public function getDescription(): string;

    /**
     * Severità default (P0-P3)
     */
    public function getDefaultSeverity(): string;
}
```

**Example Implementation - RegolaZeroRule:**

```php
class RegolaZeroRule implements RuleInterface
{
    private array $blacklistedMethods = [
        'hasConsentFor',      // ConsentService - inventato
        'handleException',    // ErrorManager - inventato
        'logError',           // AuditLogService - inventato
        'logActivity',        // AuditLogService - deprecato
    ];

    public function check(array $nodes): array
    {
        $violations = [];
        $traverser = new NodeTraverser();

        $visitor = new class($this->blacklistedMethods) extends NodeVisitorAbstract {
            private array $blacklist;
            public array $violations = [];

            public function __construct(array $blacklist) {
                $this->blacklist = $blacklist;
            }

            public function enterNode(Node $node) {
                // Cerca MethodCall nodes
                if ($node instanceof Node\Expr\MethodCall) {
                    $methodName = $node->name instanceof Node\Identifier
                        ? $node->name->toString()
                        : null;

                    if ($methodName && in_array($methodName, $this->blacklist)) {
                        $this->violations[] = [
                            'message' => "Using blacklisted method {$methodName}()",
                            'line' => $node->getStartLine(),
                            'severity' => 'P0',
                            'codeSnippet' => $this->getCodeSnippet($node),
                        ];
                    }
                }
            }

            private function getCodeSnippet(Node $node): string {
                // Extract 1-2 lines di codice intorno al node
                // ...implementazione...
            }
        };

        $traverser->addVisitor($visitor);
        $traverser->traverse($nodes);

        return $visitor->violations;
    }

    public function getName(): string {
        return 'REGOLA_ZERO';
    }

    public function getDescription(): string {
        return 'Prevents use of non-existent methods (anti-invention)';
    }

    public function getDefaultSeverity(): string {
        return 'P0';
    }
}
```

**Rule Performance:**

-   Visitor pattern = single AST traversal per rule
-   ~20-50ms per rule per file medio
-   Parallelizzazione planned (multi-threading)

---

### 3. PadminController

**Responsibility:** HTTP request handling + coordination services

**Key Workflows:**

#### Workflow 1: Run Scan

```
User → Click "Run Scan" button
  ↓
JS → POST /superadmin/padmin/scan/run {path, rules, store: true}
  ↓
PadminController::runScan()
  ↓
Validate request (rules array, path exists)
  ↓
RuleEngineService::scanDirectory($path, $rules)
  ↓
Get violations array
  ↓
IF store=true:
  ↓
  Generate unique IDs (v_uniqid())
  ↓
  Merge with existing session violations (dedupe)
  ↓
  session()->put('padmin_violations', $violations)
  ↓
AuditLogService::logUserAction('padmin_scan_executed')
  ↓
Return JSON {success: true, violations: [...], count: X, stored: true}
  ↓
JS → Close modal + reload page
  ↓
User → See violations in table
```

#### Workflow 2: AI Fix

```
User → Click "⚡ AI" button on violation
  ↓
JS → openAiFixModal(violationId)
  ↓
POST /superadmin/padmin/violations/{id}/ai-fix
  ↓
PadminController::requestAiFix($id)
  ↓
Get violation from session by ID
  ↓
buildAiFixPrompt($violation)
  ├─ Include violation details
  ├─ Include file context
  ├─ Include rule explanation
  └─ Include FlorenceEGI architecture context
  ↓
Return JSON {success: true, ai_prompt: "...", violation: {...}}
  ↓
JS → Display in modal
  ↓
User → Click "Copia"
  ↓
Clipboard copy success toast
  ↓
User → Paste in GitHub Copilot Chat
  ↓
Apply suggested fix
  ↓
User → Click "✓ Mark as Fixed"
  ↓
POST /superadmin/padmin/violations/{id}/fix
  ↓
PadminController::markFixed($id)
  ↓
Update violation in session: is_fixed = true
  ↓
AuditLogService::logUserAction('padmin_violation_fixed')
  ↓
Return JSON {success: true}
  ↓
JS → Reload page
  ↓
User → See violation badge green "Risolta"
```

---

### 4. Storage Layer

#### Current: Session Storage

**Pros:**

-   ✅ Zero setup (Laravel built-in)
-   ✅ Per-user isolation automatica
-   ✅ No external dependencies
-   ✅ Fast per single-user

**Cons:**

-   ❌ Non persistent (logout = perdi tutto)
-   ❌ No historical tracking
-   ❌ No multi-session sync
-   ❌ Limited to ~4KB data (cookie) o ~10MB (file/redis session)
-   ❌ No advanced queries

**Implementation:**

```php
// Store violations
session()->put('padmin_violations', $violations);

// Retrieve violations
$violations = session('padmin_violations', []);

// Update specific violation
$violations = collect(session('padmin_violations', []))
    ->map(function ($v) use ($id) {
        if ($v['id'] === $id) {
            $v['is_fixed'] = true;
        }
        return $v;
    })
    ->toArray();
session()->put('padmin_violations', $violations);
```

#### Future: Redis Stack + MySQL

**Architecture:**

```
┌──────────────────────────────────────────────────────┐
│                   APPLICATION LAYER                   │
│                  (PadminService)                      │
└───────────────────┬──────────────────────────────────┘
                    │
        ┌───────────┴───────────┐
        │                       │
        ▼                       ▼
┌──────────────┐      ┌─────────────────┐
│ Redis Stack  │      │     MySQL       │
├──────────────┤      ├─────────────────┤
│ RediSearch   │      │ violations      │
│ - Indexes    │      │ - id            │
│ - Full-text  │      │ - user_id       │
│              │      │ - rule          │
│ RedisJSON    │      │ - file          │
│ - Documents  │      │ - line          │
│ - Symbols    │      │ - created_at    │
│ - Call graph │      │ - fixed_at      │
└──────────────┘      └─────────────────┘
```

**Redis Keys Design:**

```
// Violations
violation:{id}                    → JSON document
violations:by:user:{user_id}      → Set of violation IDs
violations:by:file:{hash}         → Set of violation IDs
violations:by:rule:{rule_name}    → Set of violation IDs
violations:index                  → RediSearch index

// Symbols (FASE 2)
symbol:class:{class_name}         → JSON document
symbol:method:{class}:{method}    → JSON document
symbol:function:{function_name}   → JSON document
symbols:index                     → RediSearch index

// Call Graph (FASE 2)
callgraph:{from}:{to}             → JSON edge
callgraph:index                   → RediSearch index
```

**RediSearch Index:**

```redis
FT.CREATE violations:index ON JSON PREFIX 1 violation: SCHEMA
  $.rule AS rule TAG
  $.severity AS severity TAG
  $.file AS file TEXT
  $.message AS message TEXT
  $.line AS line NUMERIC SORTABLE
  $.scanned_at AS scanned_at NUMERIC SORTABLE
  $.is_fixed AS is_fixed TAG
```

**Query Examples:**

```redis
# Find all P0 violations not fixed
FT.SEARCH violations:index "@severity:{P0} @is_fixed:{false}"

# Find violations in specific file
FT.SEARCH violations:index "@file:UserController.php"

# Find violations by rule
FT.SEARCH violations:index "@rule:{REGOLA_ZERO}"

# Full-text search in messages
FT.SEARCH violations:index "@message:consent"
```

**Migration Plan:**

1. Week 1: Setup Redis Stack + RediSearch
2. Week 2: Implement PadminService con Node.js CLI
3. Week 3: Migrate session storage → Redis
4. Week 4: Implement MySQL backup + historical tracking

---

## 🔄 Data Flow

### Scan Flow Diagram

```
┌─────────┐
│  User   │
│ (Browser)
└────┬────┘
     │ 1. Click "Run Scan"
     ▼
┌──────────────────┐
│ Scan Modal (JS)  │
│ - Path input     │
│ - Rules checkboxes
└────┬─────────────┘
     │ 2. Submit form
     │ POST /superadmin/padmin/scan/run
     │ {path: "app/Services", rules: ["REGOLA_ZERO"], store: true}
     ▼
┌─────────────────────────────────────────────┐
│  PadminController::runScan()                │
│  ┌─────────────────────────────────────┐   │
│  │ 1. Validate request                 │   │
│  │ 2. Call RuleEngineService           │◄──┼──┐
│  │ 3. Get violations array             │   │  │
│  │ 4. Generate unique IDs              │   │  │
│  │ 5. Merge with existing (dedupe)     │   │  │
│  │ 6. Store in session                 │   │  │
│  │ 7. Log audit                        │   │  │
│  │ 8. Return JSON response             │   │  │
│  └─────────────────────────────────────┘   │  │
└─────────────────────────────────────────────┘  │
     │ 3. JSON response                          │
     ▼                                            │
┌──────────────────┐                             │
│ JavaScript       │                             │
│ - Close modal    │                             │
│ - Reload page    │                             │
└────┬─────────────┘                             │
     │ 4. GET /superadmin/padmin/violations      │
     ▼                                            │
┌─────────────────────────────────────────────┐  │
│  PadminController::violations()             │  │
│  ┌─────────────────────────────────────┐   │  │
│  │ 1. Read from session                │   │  │
│  │ 2. Apply filters (priority, fixed)  │   │  │
│  │ 3. Sort by severity                 │   │  │
│  │ 4. Return Blade view                │   │  │
│  └─────────────────────────────────────┘   │  │
└─────────────────────────────────────────────┘  │
     │ 5. Render view                            │
     ▼                                            │
┌──────────────────┐                             │
│ Violations Table │                             │
│ - Filters        │                             │
│ - Rows with      │                             │
│   action buttons │                             │
└──────────────────┘                             │
                                                  │
┌─────────────────────────────────────────────┐  │
│  RuleEngineService                          │◄─┘
│  ┌─────────────────────────────────────┐   │
│  │ scanDirectory()                     │   │
│  │  │                                  │   │
│  │  ├─→ getPhpFiles() (recursive)     │   │
│  │  │                                  │   │
│  │  └─→ foreach file:                 │   │
│  │       scanFile()                    │   │
│  │        │                            │   │
│  │        ├─→ file_get_contents()     │   │
│  │        ├─→ Parser::parse() → AST   │   │
│  │        │                            │   │
│  │        └─→ foreach rule:           │   │
│  │             rule->check(AST)        │   │
│  │              │                      │   │
│  │              └─→ NodeTraverser     │   │
│  │                   + Visitor         │   │
│  │                   │                 │   │
│  │                   └─→ violations[]  │   │
│  │                                     │   │
│  │  return merged violations           │   │
│  └─────────────────────────────────────┘   │
└─────────────────────────────────────────────┘
```

---

## 🔒 Security & Compliance

### Path Traversal Prevention

```php
public function runScan(Request $request)
{
    $validated = $request->validate([
        'path' => 'required|string',
        // ...
    ]);

    $basePath = base_path();
    $requestedPath = $basePath . '/' . ltrim($validated['path'], '/');

    // Resolve symlinks e '..' attacks
    $realPath = realpath($requestedPath);

    // Verifica che il path sia dentro base_path()
    if ($realPath === false || strpos($realPath, $basePath) !== 0) {
        throw new \InvalidArgumentException('Invalid path');
    }

    // Safe to scan
    $violations = $this->ruleEngine->scanDirectory($realPath, ...);
}
```

### Command Injection Prevention

**Node.js CLI executor (future):**

```php
class PadminService
{
    private function executeNodeJsCli(string $command, array $args): array
    {
        // Whitelist commands
        $allowedCommands = ['violations:create', 'symbols:index', 'symbols:search'];

        if (!in_array($command, $allowedCommands)) {
            throw new \InvalidArgumentException('Command not allowed');
        }

        // Escape arguments
        $escapedArgs = array_map('escapeshellarg', $args);

        // Execute with timeout
        $process = new Process(
            ['node', 'padmin-cli.js', $command, ...$escapedArgs],
            base_path('tools/padmin-cli'),
            null,
            null,
            30 // 30s timeout
        );

        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return json_decode($process->getOutput(), true);
    }
}
```

### GDPR Compliance

**Audit Trail per Scan Operations:**

```php
// Log quando user esegue scan
$this->auditService->logUserAction(
    Auth::user(),
    'padmin_scan_executed',
    [
        'path' => $validated['path'],
        'rules' => $validated['rules'],
        'violations_found' => count($violations)
    ],
    GdprActivityCategory::SYSTEM_ACTION
);

// Log quando user marca violation fixed
$this->auditService->logUserAction(
    Auth::user(),
    'padmin_violation_fixed',
    [
        'violation_id' => $id,
        'rule' => $violation['rule'],
        'file' => $violation['file']
    ],
    GdprActivityCategory::SYSTEM_ACTION
);
```

---

## ⚡ Performance Optimization

### Current Bottlenecks

1. **AST Parsing:** ~100-200ms per file
2. **Visitor Pattern:** ~20-50ms per rule per file
3. **File I/O:** ~10-20ms per file
4. **Session R/W:** ~5-10ms per operation

### Optimization Strategies

#### 1. AST Caching

```php
class RuleEngineService
{
    private CacheInterface $astCache;

    public function scanFile(string $filePath, array $rules = []): array
    {
        $fileHash = md5_file($filePath);
        $cacheKey = "ast:{$fileHash}";

        // Check cache
        $ast = $this->astCache->get($cacheKey);

        if ($ast === null) {
            // Parse e cache
            $content = file_get_contents($filePath);
            $parser = $this->parserFactory->create(ParserFactory::PREFER_PHP8);
            $ast = $parser->parse($content);

            // Cache for 1 hour
            $this->astCache->set($cacheKey, serialize($ast), 3600);
        } else {
            $ast = unserialize($ast);
        }

        // Apply rules...
    }
}
```

**Benefit:** 70% faster re-scans (no re-parsing)

#### 2. Parallel Rule Execution

```php
use Spatie\Fork\Fork;

public function scanFile(string $filePath, array $rules = []): array
{
    $ast = $this->parseFile($filePath);

    // Execute rules in parallel (separate processes)
    $results = Fork::new()
        ->before(fn() => $this->setupChildProcess())
        ->run(
            fn() => $this->rules['REGOLA_ZERO']->check($ast),
            fn() => $this->rules['UEM_FIRST']->check($ast),
            fn() => $this->rules['STATISTICS']->check($ast),
            fn() => $this->rules['MICA_SAFE']->check($ast),
            fn() => $this->rules['GDPR_COMPLIANCE']->check($ast),
        );

    // Merge results
    return array_merge(...$results);
}
```

**Benefit:** 50% faster per file (5 rules parallel vs sequential)

#### 3. Incremental Scanning

```php
class IncrementalScanner
{
    public function scanChangedFiles(string $basePath): array
    {
        // Get git status
        $changedFiles = $this->getGitChangedFiles($basePath);

        // Scan only changed files
        $violations = [];
        foreach ($changedFiles as $file) {
            if ($this->isPhpFile($file)) {
                $violations = array_merge(
                    $violations,
                    $this->ruleEngine->scanFile($file)
                );
            }
        }

        return $violations;
    }

    private function getGitChangedFiles(string $basePath): array
    {
        $process = new Process(['git', 'diff', '--name-only', 'HEAD'], $basePath);
        $process->run();

        return explode("\n", trim($process->getOutput()));
    }
}
```

**Benefit:** 90% faster (scan only changed files)

---

## 🚀 Future Architecture

### Phase 2: Symbol Registry

```
┌──────────────────────────────────────────────────┐
│           Indexing Pipeline                      │
├──────────────────────────────────────────────────┤
│                                                  │
│  php artisan padmin:index --full                 │
│         │                                        │
│         ▼                                        │
│  ┌────────────────────────────────┐             │
│  │ Scan all PHP files in repo    │             │
│  └────────────┬───────────────────┘             │
│               │                                  │
│               ▼                                  │
│  ┌────────────────────────────────┐             │
│  │ Parse AST for each file        │             │
│  │ Extract:                       │             │
│  │ - Classes                      │             │
│  │ - Methods + signatures         │             │
│  │ - Functions                    │             │
│  │ - Traits                       │             │
│  │ - Interfaces                   │             │
│  │ - Constants                    │             │
│  └────────────┬───────────────────┘             │
│               │                                  │
│               ▼                                  │
│  ┌────────────────────────────────┐             │
│  │ Build dependency graph         │             │
│  │ - Who extends who              │             │
│  │ - Who implements who           │             │
│  │ - Who uses who                 │             │
│  └────────────┬───────────────────┘             │
│               │                                  │
│               ▼                                  │
│  ┌────────────────────────────────┐             │
│  │ Store in Redis Stack           │             │
│  │ - symbol:{type}:{name}         │             │
│  │ - RediSearch index             │             │
│  │ - Call graph edges             │             │
│  └────────────────────────────────┘             │
│                                                  │
└──────────────────────────────────────────────────┘
```

**Example Symbol Document:**

```json
{
    "type": "method",
    "class": "App\\Services\\Gdpr\\ConsentService",
    "name": "hasConsent",
    "signature": "hasConsent(User $user, string $consentType): bool",
    "parameters": [
        { "name": "user", "type": "User", "nullable": false },
        { "name": "consentType", "type": "string", "nullable": false }
    ],
    "return_type": "bool",
    "file": "app/Services/Gdpr/ConsentService.php",
    "line": 42,
    "docblock": {
        "description": "Check if user has given specific consent",
        "params": ["@param User $user", "@param string $consentType"],
        "return": "@return bool"
    },
    "visibility": "public",
    "static": false,
    "used_by": [
        "App\\Http\\Controllers\\UserController::update",
        "App\\Http\\Controllers\\ProfileController::store",
        "App\\Services\\User\\UserService::updateProfile"
    ],
    "calls": [
        "App\\Models\\User::consents",
        "Illuminate\\Database\\Eloquent\\Collection::contains"
    ],
    "indexed_at": "2025-10-23T13:45:00Z"
}
```

### Phase 3: AI Copilot Integration

```
┌──────────────────────────────────────────────────┐
│              AI Context Builder                  │
├──────────────────────────────────────────────────┤
│                                                  │
│  User: "Create method to update user profile    │
│         with GDPR compliance"                    │
│         │                                        │
│         ▼                                        │
│  ┌────────────────────────────────┐             │
│  │ Query Symbol Registry          │             │
│  │ Search: "consent", "audit",    │             │
│  │         "user update"          │             │
│  └────────────┬───────────────────┘             │
│               │                                  │
│               ▼                                  │
│  ┌────────────────────────────────┐             │
│  │ Found relevant symbols:        │             │
│  │ - ConsentService::hasConsent() │             │
│  │ - AuditLogService::logUserAction() │         │
│  │ - UserController::update()     │             │
│  └────────────┬───────────────────┘             │
│               │                                  │
│               ▼                                  │
│  ┌────────────────────────────────┐             │
│  │ Build AI context:              │             │
│  │ - Method signatures            │             │
│  │ - Usage examples from code     │             │
│  │ - Pattern from similar methods │             │
│  │ - OS3.0 rules to follow        │             │
│  └────────────┬───────────────────┘             │
│               │                                  │
│               ▼                                  │
│  ┌────────────────────────────────┐             │
│  │ Send to OpenAI GPT-4:          │             │
│  │ {                              │             │
│  │   "model": "gpt-4",            │             │
│  │   "messages": [                │             │
│  │     {                          │             │
│  │       "role": "system",        │             │
│  │       "content": "OS3.0 inst..." │           │
│  │     },                         │             │
│  │     {                          │             │
│  │       "role": "user",          │             │
│  │       "content": "Create..."   │             │
│  │     }                          │             │
│  │   ],                           │             │
│  │   "context": {symbols...}      │             │
│  │ }                              │             │
│  └────────────┬───────────────────┘             │
│               │                                  │
│               ▼                                  │
│  ┌────────────────────────────────┐             │
│  │ Receive generated code         │             │
│  │ Validate against rules         │             │
│  │ Return to user                 │             │
│  └────────────────────────────────┘             │
│                                                  │
└──────────────────────────────────────────────────┘
```

---

## 📊 Metrics & Monitoring

### Key Metrics to Track

1. **Scan Performance**
    - Files scanned per second
    - Average scan time per file
    - Rule execution time breakdown
2. **Violation Metrics**
    - Violations per 1000 LOC
    - P0 violations ratio
    - Time to fix (detected → marked fixed)
3. **System Health**
    - AST parse success rate
    - Error rate per rule
    - Session storage usage
4. **User Engagement**
    - Scans per day
    - AI Fix usage rate
    - Violations marked fixed rate

### Logging Strategy

```php
// Performance logging
UltraLogManager::performance('padmin_scan', [
    'path' => $path,
    'files_scanned' => count($files),
    'duration_ms' => $duration,
    'violations_found' => count($violations)
]);

// Error logging
UltraLogManager::error('padmin_parse_error', [
    'file' => $filePath,
    'error' => $exception->getMessage()
]);

// Business metrics
UltraLogManager::info('padmin_ai_fix_used', [
    'violation_id' => $id,
    'rule' => $violation['rule'],
    'user_id' => Auth::id()
]);
```

---

**Last Updated:** 23 Ottobre 2025  
**Version:** 1.0.0  
**Status:** Architecture Stable (Fase 1)
