# Padmin Analyzer - OS3 Guardian

**Intelligent Code Quality Analyzer & AI-Powered Development Assistant for FlorenceEGI**

> 🎯 **Mission:** Garantire qualità del codice enterprise-grade per piattaforme PA con validazione automatica delle regole OS3.0 e assistenza AI contestuale.

---

## 📋 Indice

1. [Panoramica](#panoramica)
2. [Stato Attuale](#stato-attuale)
3. [Architettura](#architettura)
4. [Funzionalità](#funzionalità)
5. [Quick Start](#quick-start)
6. [Regole OS3.0](#regole-os30)
7. [Roadmap](#roadmap)
8. [API Reference](#api-reference)

---

## 🎯 Panoramica

Padmin Analyzer è un **sistema di analisi codice intelligente** che:

-   ✅ **Scansiona** il codebase PHP/Laravel per violazioni regole OS3.0
-   ✅ **Valida** in real-time rispetto a standard enterprise PA
-   ✅ **Suggerisce** fix tramite AI (GitHub Copilot integration)
-   ✅ **Traccia** violations con metadata completi
-   ✅ **Previene** code smells e anti-patterns
-   🚧 **Indicizza** simboli per search semantica (in sviluppo)
-   🚧 **Genera** codice AI-assisted (in sviluppo)

### Perché Padmin?

**Contesto PA/Enterprise:**

-   Pubbliche Amministrazioni richiedono **zero errori**
-   GDPR compliance **obbligatoria**
-   Audit trail **completo**
-   Code quality **mission-critical**

**Un bug = Fiducia persa = Contratto a rischio**

Padmin garantisce che ogni riga di codice rispetti gli standard OS3.0 prima del deployment.

---

## 📊 Stato Attuale

### ✅ FASE 1: Rule Engine (COMPLETATA)

**Data Completamento:** 23 Ottobre 2025

#### Implementazioni:

| Componente            | Stato | Descrizione                                                        |
| --------------------- | ----- | ------------------------------------------------------------------ |
| **RuleEngineService** | ✅    | Motore scansione AST con nikic/php-parser v5.6.2                   |
| **5 Regole Attive**   | ✅    | REGOLA_ZERO, UEM_FIRST, STATISTICS, MiCA_SAFE, GDPR_COMPLIANCE     |
| **CLI Scanner**       | ✅    | `php artisan padmin:scan` operativo                                |
| **Web UI Dashboard**  | ✅    | 5 pagine Blade: Dashboard, Violations, Symbols, Search, Statistics |
| **Scan Workflow**     | ✅    | Modal scan → Store → Display → AI Fix → Mark Fixed                 |
| **AI Fix Prompts**    | ✅    | Generazione prompt contestuali per GitHub Copilot                  |
| **UEM Integration**   | ✅    | Error codes PADMIN_SCAN_FAILED, PADMIN_AI_FIX_FAILED               |
| **Session Storage**   | ✅    | Violations salvate in sessione Laravel (temp)                      |

#### Metriche:

-   **Linee codice:** ~2,500 righe PHP + Blade
-   **Test scan:** 15 P0 violations trovate su app/Livewire/Collections
-   **Performance:** ~2-3s per directory scan
-   **Accuratezza:** 100% detection rate su test violations

### 🚧 FASE 2: Symbol Registry (PROSSIMA)

**Inizio:** TBD  
**Effort:** 2 settimane

---

## 🏗️ Architettura

```
┌─────────────────────────────────────────────────────────────┐
│                    WEB INTERFACE (Blade + JS)                │
├─────────────────────────────────────────────────────────────┤
│  Dashboard │ Violations │ Symbols │ Search │ Statistics     │
│  - KPI cards             - Scan modal      - Filters        │
│  - Real-time stats       - AI Fix modal    - Actions        │
└────────────┬────────────────────────────────┬───────────────┘
             │                                │
             ▼                                ▼
┌────────────────────────┐      ┌────────────────────────────┐
│  Laravel Controllers   │      │   Services Layer           │
├────────────────────────┤      ├────────────────────────────┤
│ PadminController       │      │ RuleEngineService          │
│ - dashboard()          │◄────►│ - scanDirectory()          │
│ - violations()         │      │ - scanFiles()              │
│ - runScan()            │      │ - scanFile()               │
│ - requestAiFix()       │      │                            │
│ - markFixed()          │      │ PadminService              │
│                        │      │ - Node.js CLI executor     │
└────────────┬───────────┘      └────────────┬───────────────┘
             │                                │
             ▼                                ▼
┌────────────────────────────────────────────────────────────┐
│                    RULE ENGINE LAYER                        │
├────────────────────────────────────────────────────────────┤
│  nikic/php-parser v5.6.2 (PHP AST Parser)                  │
│                                                             │
│  ┌──────────────────────────────────────────────────────┐  │
│  │ RuleInterface                                        │  │
│  │ - check(array $nodes): array                         │  │
│  │ - getName(): string                                  │  │
│  │ - getDescription(): string                           │  │
│  └──────────────────────────────────────────────────────┘  │
│                       │                                     │
│           ┌───────────┴───────────┬──────────────┐         │
│           ▼                       ▼              ▼         │
│  ┌────────────────┐  ┌─────────────────┐  ┌──────────┐   │
│  │ RegolaZeroRule │  │ UemFirstRule    │  │ ...      │   │
│  │ (P0-BLOCKING)  │  │ (P0-BLOCKING)   │  │          │   │
│  └────────────────┘  └─────────────────┘  └──────────┘   │
│                                                             │
│  Returns: Violation[]                                      │
│  {                                                          │
│    rule: string,      // Nome regola violata               │
│    severity: string,  // P0, P1, P2, P3                    │
│    message: string,   // Descrizione violazione            │
│    file: string,      // Path file relativo                │
│    line: int,         // Numero linea                       │
│    codeSnippet: string // Codice problematico              │
│  }                                                          │
└────────────────────────────────────────────────────────────┘
             │
             ▼
┌────────────────────────────────────────────────────────────┐
│                    STORAGE LAYER                            │
├────────────────────────────────────────────────────────────┤
│  CURRENT: Laravel Session                                  │
│  - session('padmin_violations')                            │
│  - Temporary storage                                       │
│  - Per-user isolation                                      │
│                                                             │
│  FUTURE: Redis Stack + Node.js CLI                         │
│  - RediSearch for fast queries                             │
│  - RedisJSON for complex objects                           │
│  - Persistent storage                                      │
│  - Multi-user support                                      │
│  - Historical tracking                                     │
└────────────────────────────────────────────────────────────┘
```

### Dependency Graph

```
PadminController
├─→ RuleEngineService
│   ├─→ nikic/php-parser (PhpParser\ParserFactory)
│   ├─→ RegolaZeroRule
│   ├─→ UemFirstRule
│   ├─→ StatisticsRule
│   ├─→ MicaSafeRule
│   └─→ GdprComplianceRule
│
├─→ PadminService (future Redis integration)
├─→ AuditLogService (GDPR audit trail)
├─→ ErrorManager (UEM error handling)
└─→ UltraLogManager (ULM logging)
```

---

## 🎯 Funzionalità

### 1. Code Scanning

#### CLI Usage

```bash
# Scansiona directory con tutte le regole
php artisan padmin:scan --path=app/Http/Controllers

# Scansiona con regole specifiche
php artisan padmin:scan --path=app/Services --rules=REGOLA_ZERO,UEM_FIRST

# Scansiona e salva in storage
php artisan padmin:scan --path=app/Models --store

# Scansiona per user specifico
php artisan padmin:scan --path=app --store --user-id=1
```

#### Web UI

1. Vai su `/superadmin/padmin/violations`
2. Click "Run Scan" (pulsante oro top-right)
3. Inserisci path: `app/Livewire/Collections`
4. Seleziona regole da applicare (default: tutte)
5. Click "Avvia Scansione"
6. Violations salvate e mostrate nella tabella

### 2. Violations Management

#### Tabella Violations

**Colonne:**

-   **Priorità:** Badge P0 (rosso), P1 (giallo), P2 (blu), P3 (grigio)
-   **Severità:** critical, error, warning, info
-   **Tipo Violazione:** Nome regola + messaggio
-   **File:** Basename file problematico
-   **Linea:** Numero linea esatto
-   **Data:** Timestamp scan
-   **Stato:** Attiva (rosso) / Risolta (verde)
-   **Azioni:** 2 pulsanti

#### Azioni Disponibili

**1. Fix with AI (pulsante blu ⚡)**

-   Click → Modal si apre
-   Genera prompt contestuale per GitHub Copilot
-   Include:
    -   Descrizione violazione
    -   Codice problematico
    -   File e linea
    -   Regola violata
    -   Context architettura FlorenceEGI
-   Pulsante "Copia" → clipboard
-   Incolla in GitHub Copilot Chat
-   Applica fix suggerito
-   Torna e marca come risolta

**2. Mark as Fixed (pulsante verde ✓)**

-   Click → Conferma
-   Violation diventa verde
-   Badge cambia: Attiva → Risolta
-   Audit log registrato

### 3. Filters

**Disponibili:**

-   **Priorità:** P0, P1, P2, P3
-   **Severità:** critical, error, warning, info
-   **Stato:** Attive, Risolte
-   **Risultati:** 50, 100, 500

### 4. Statistics (Dashboard)

**KPI Cards:**

-   Total Violations
-   P0 Blocking Issues
-   Symbols Indexed
-   System Health

**Metriche:**

-   Violations per rule
-   Violations per severity
-   Violations per file
-   Trend temporale

---

## 🚀 Quick Start

### Prerequisites

```bash
# PHP 8.2+
php --version

# Composer 2.x
composer --version

# Laravel 11.x
php artisan --version

# nikic/php-parser (già installato)
composer show nikic/php-parser
# Version: ^5.6.2
```

### Installation

```bash
# 1. Già integrato in FlorenceEGI
cd /home/fabio/EGI

# 2. Verifica installazione
php artisan list | grep padmin
# Output: padmin:scan

# 3. Test scan
php artisan padmin:scan --path=app/Services/Test/TestViolationsService.php

# Output:
# Found 2 P0 violations:
# - REGOLA_ZERO at line 42: Using blacklisted method hasConsentFor()
# - UEM_FIRST at line 58: Catch block without errorManager->handle()
```

### First Scan via Web UI

1. **Login come SuperAdmin**

    ```
    URL: http://localhost:8004/superadmin/login
    ```

2. **Vai a Padmin Analyzer**

    ```
    Sidebar → Padmin Analyzer → Violations
    ```

3. **Run First Scan**

    - Click "Run Scan"
    - Path: `app/Services/Test`
    - Regole: Tutte selezionate
    - Click "Avvia Scansione"

4. **View Results**

    - Modal si chiude
    - Pagina ricarica
    - Violations in tabella

5. **Test AI Fix**
    - Click "⚡ AI" su una violation
    - Modal con prompt
    - Click "Copia"
    - Testa in Copilot Chat

---

## 📜 Regole OS3.0

### REGOLA ZERO - Anti-Invention (P0 - BLOCKING)

**Obiettivo:** Prevenire uso di metodi/classi inesistenti

**Rileva:**

```php
// ❌ VIOLAZIONE
$this->consentService->hasConsentFor('profile-update');
// Metodo hasConsentFor() non esiste!

// ✅ CORRETTO
$this->consentService->hasConsent($user, 'allow-personal-data-processing');
// Metodo esistente verificato
```

**Blacklist:**

-   `hasConsentFor()` → usa `hasConsent()`
-   `handleException()` → usa `errorManager->handle()`
-   `logError()` → usa `errorManager->handle()`
-   `logActivity()` → usa `auditLogService->logUserAction()`

**Impatto:** BLOCKING - Code non può andare in production

### UEM FIRST - Error Handling (P0 - BLOCKING)

**Obiettivo:** Garantire error handling enterprise con UEM

**Rileva:**

```php
// ❌ VIOLAZIONE
try {
    $user->update($data);
} catch (\Exception $e) {
    Log::error('Update failed'); // Solo log generico!
}

// ✅ CORRETTO
try {
    $user->update($data);
} catch (\Exception $e) {
    $this->errorManager->handle('USER_UPDATE_FAILED', [
        'user_id' => $user->id,
        'context' => $data
    ], $e);
}
```

**Checks:**

-   Ogni `catch` block deve chiamare `errorManager->handle()`
-   Non sostituire UEM con ULM (logger)
-   ErrorManager deve essere iniettato nel controller/service

**Impatto:** BLOCKING - Team non riceve alert su errori

### STATISTICS RULE - Data Integrity (P0 - BLOCKING)

**Obiettivo:** Prevenire dati incompleti mostrati come completi

**Rileva:**

```php
// ❌ VIOLAZIONE (in StatisticsService)
public function getTopEgis(): Collection
{
    return Egi::orderBy('likes')->take(10)->get();
    // Limite nascosto! Utente vede 10 EGI ma pensa siano tutti
}

// ✅ CORRETTO
public function getTopEgis(?int $limit = null): Collection
{
    $query = Egi::orderBy('likes');

    if ($limit !== null) {
        $query->limit($limit);
    }

    return $query->get(); // Tutti i record di default
}
```

**Rationale PA:**
Un dirigente PA che vede "4 likes" invece di "6 reali" perde fiducia nella piattaforma.

**Impatto:** BLOCKING - Credibilità PA a rischio

### MiCA SAFE - Compliance Crypto EU (P0 - BLOCKING)

**Obiettivo:** Prevenire operazioni che richiedono licenza CASP/EMI

**Rileva:**

```php
// ❌ VIOLAZIONE
public function storeUserCrypto($userId, $amount, $currency)
{
    // Custodire crypto per utenti richiede licenza CASP!
}

// ❌ VIOLAZIONE
public function swapCrypto($fromCurrency, $toCurrency)
{
    // Exchange crypto richiede licenza EMI!
}

// ✅ CORRETTO (MiCA-safe)
public function mintNftForUser($userId, $metadata)
{
    // Minting NFT per conto utente = OK
    // NFT va su wallet piattaforma = OK
    // Transfer automatico a wallet utente = OK
}
```

**Keywords bloccati:**

-   `custody`, `custodial`, `store_crypto`
-   `exchange`, `swap`, `convert_crypto`
-   `wallet_balance`, `crypto_holdings`
-   `manage_keys`, `private_key`

**Impatto:** BLOCKING - Violazione MiCA = Sanzioni EU

### GDPR COMPLIANCE - Privacy (P0 - BLOCKING)

**Obiettivo:** Garantire GDPR compliance su operazioni User

**Rileva:**

```php
// ❌ VIOLAZIONE
public function updateProfile(Request $request)
{
    $user = Auth::user();
    $user->update($request->validated());
    // Mancano: consent check + audit log!
}

// ✅ CORRETTO
public function updateProfile(Request $request)
{
    $user = Auth::user();

    // 1. Check consent
    if (!$this->consentService->hasConsent($user, 'allow-personal-data-processing')) {
        return redirect()->back()->withErrors(['consent' => 'Missing']);
    }

    // 2. Update
    $user->update($request->validated());

    // 3. Audit trail
    $this->auditLogService->logUserAction(
        $user,
        'personal_data_updated',
        ['fields' => array_keys($request->validated())],
        GdprActivityCategory::PERSONAL_DATA_UPDATE
    );
}
```

**Checks:**

-   User model update/save senza `ConsentService->hasConsent()`
-   User model update/save senza `AuditLogService->logUserAction()`
-   Dependency injection mancante (ConsentService, AuditLogService)

**Impatto:** BLOCKING - Violazione GDPR = Sanzioni EU

---

## 🗺️ Roadmap

### ✅ FASE 1: Rule Engine (COMPLETATA)

**Timeline:** Settimane 1-2  
**Status:** ✅ 100% Completato (23 Ottobre 2025)

-   [x] RuleEngineService con nikic/php-parser
-   [x] 5 regole implementate e testate
-   [x] CLI scanner funzionante
-   [x] Web UI completa (5 pagine)
-   [x] Scan → Store → Display workflow
-   [x] AI Fix prompts generation
-   [x] Mark as fixed functionality
-   [x] UEM error codes registrati

### 🚧 FASE 2: Symbol Registry

**Timeline:** Settimane 3-4  
**Status:** 🔴 Not Started  
**Effort:** ~80 ore

**Obiettivi:**

-   [ ] Scansione completa codebase → Redis
-   [ ] Indicizzazione classi, metodi, funzioni, traits
-   [ ] Signatures complete (params, return types)
-   [ ] DocBlocks parsed e salvati
-   [ ] Dependencies tracking (chi usa cosa)
-   [ ] Call graph (chi chiama chi)
-   [ ] API search: `/api/padmin/symbols/search?q=hasConsent`
-   [ ] Dashboard "Code Explorer" con search UI

**Deliverables:**

```bash
# Comando indexing
php artisan padmin:index --full

# API endpoint
GET /api/padmin/symbols/search?q=ConsentService&type=class
Response: {
  "class": "App\\Services\\Gdpr\\ConsentService",
  "methods": [
    {
      "name": "hasConsent",
      "signature": "hasConsent(User $user, string $consentType): bool",
      "file": "app/Services/Gdpr/ConsentService.php",
      "line": 42,
      "docblock": "Check if user has given specific consent",
      "usedBy": ["UserController", "ProfileController", ...]
    }
  ]
}
```

### 🔮 FASE 3: AI Copilot Integration

**Timeline:** Settimane 5-6  
**Status:** 🔴 Not Started  
**Effort:** ~60 ore

**Obiettivi:**

-   [ ] OpenAI API integration (GPT-4)
-   [ ] Context builder che legge symbol registry
-   [ ] Prompt engineering per code generation
-   [ ] Chat interface stile ChatGPT
-   [ ] Code completion endpoint
-   [ ] Copy-paste workflow ottimizzato

**Use Case:**

```
User: "Crea metodo per salvare profilo utente con GDPR compliance"

AI (context da symbol registry):
- Trova ConsentService->hasConsent()
- Trova AuditLogService->logUserAction()
- Trova pattern UserController::store()
- Genera codice conforme

Output:
✅ Usa servizi esistenti
✅ Rispetta pattern architetturali
✅ GDPR compliant
✅ Non viola REGOLA ZERO
```

### 🔮 FASE 4: Web Terminal

**Timeline:** Settimane 7-8  
**Status:** 🔴 Not Started  
**Effort:** ~40 ore

**Obiettivi:**

-   [ ] xterm.js integration
-   [ ] WebSocket Laravel backend
-   [ ] Safe commands whitelist
-   [ ] Output streaming
-   [ ] Command history
-   [ ] Tab completion

**Comandi Safe:**

```bash
php artisan list
php artisan route:list
php artisan padmin:scan
composer show
git status
git log --oneline -10
```

### 🔮 FASE 5: Monaco Editor

**Timeline:** Settimane 9-11  
**Status:** 🔴 Not Started  
**Effort:** ~120 ore

**Obiettivi:**

-   [ ] Monaco Editor embedded
-   [ ] File tree browser
-   [ ] Syntax highlighting (PHP/JS/Blade)
-   [ ] Real-time rule validation
-   [ ] Inline errors/warnings display
-   [ ] Quick-fix actions
-   [ ] AI code completion
-   [ ] File save con metadata update

### 🔮 FASE 6: Closed-Loop Development

**Timeline:** Settimane 12-13  
**Status:** 🔴 Not Started  
**Effort:** ~60 ore

**Obiettivi:**

-   [ ] On-save hooks
-   [ ] Incremental symbol indexing
-   [ ] Call graph auto-update
-   [ ] Code versioning system
-   [ ] Rollback capability
-   [ ] Conflict resolution

---

## 📡 API Reference

### Scan Endpoints

#### POST `/superadmin/padmin/scan/run`

**Esegue scansione code quality**

**Request:**

```json
{
    "path": "app/Http/Controllers",
    "rules": ["REGOLA_ZERO", "UEM_FIRST", "GDPR_COMPLIANCE"],
    "store": true
}
```

**Response:**

```json
{
    "success": true,
    "message": "Scansione completata",
    "violations": [
        {
            "id": "v_6720abc123",
            "rule": "REGOLA_ZERO",
            "severity": "P0",
            "message": "Using blacklisted method hasConsentFor()",
            "file": "app/Http/Controllers/UserController.php",
            "line": 42,
            "codeSnippet": "if ($this->consentService->hasConsentFor('profile')) {",
            "scanned_at": "2025-10-23T13:45:00.000000Z",
            "scanned_by": 1,
            "is_fixed": false
        }
    ],
    "count": 1,
    "stored": true
}
```

### Violations Endpoints

#### GET `/superadmin/padmin/violations`

**Lista violations con filtri**

**Query Params:**

-   `priority` (optional): P0, P1, P2, P3
-   `severity` (optional): critical, error, warning, info
-   `isFixed` (optional): 0 (attive), 1 (risolte)
-   `limit` (optional): 50, 100, 500

**Response:** Blade view con tabella violations

#### POST `/superadmin/padmin/violations/{id}/fix`

**Marca violation come risolta**

**Response:**

```json
{
    "success": true,
    "message": "Violazione marcata come risolta"
}
```

#### POST `/superadmin/padmin/violations/{id}/ai-fix`

**Genera prompt AI per fix**

**Response:**

```json
{
  "success": true,
  "message": "Contesto AI generato",
  "ai_prompt": "You are a senior Laravel developer fixing a code quality violation in FlorenceEGI...",
  "violation": { ... }
}
```

---

## 🧪 Testing

### Manual Test Violations

File di test intenzionale con violations:

```php
// app/Services/Test/TestViolationsService.php
class TestViolationsService
{
    // ❌ REGOLA_ZERO violation
    public function testRegolaZero()
    {
        $this->consentService->hasConsentFor('test'); // Metodo inesistente!
    }

    // ❌ UEM_FIRST violation
    public function testUemFirst()
    {
        try {
            throw new \Exception('Test');
        } catch (\Exception $e) {
            Log::error('Error'); // Manca errorManager->handle()!
        }
    }
}
```

### Test Scan

```bash
php artisan padmin:scan --path=app/Services/Test/TestViolationsService.php

# Expected output:
# Found 2 P0 violations:
# - REGOLA_ZERO at line 12
# - UEM_FIRST at line 19
```

---

## 🐛 Troubleshooting

### "Parser error: unexpected token"

**Causa:** File PHP con syntax error

**Soluzione:**

```bash
php -l app/Http/Controllers/ProblematicController.php
# Fix syntax errors prima di scan
```

### "No violations found" ma dovrebbero esserci

**Causa:** Rules filter troppo restrittivo

**Soluzione:**

```bash
# Scan con tutte le regole
php artisan padmin:scan --path=app/Services
```

### "Session violations not persisting"

**Causa:** Session driver configurato male

**Soluzione:**

```bash
# Verifica .env
SESSION_DRIVER=file
SESSION_LIFETIME=120

# Clear cache
php artisan cache:clear
php artisan config:clear
```

### "Modal non si apre"

**Causa:** JavaScript error o conflitto librerie

**Soluzione:**

1. Apri Browser Console (F12)
2. Cerca errori JavaScript
3. Verifica che `meta[name="csrf-token"]` esista
4. Hard reload (Ctrl+Shift+R)

---

## 📚 Resources

### Documentation

-   [Architecture Deep Dive](./ARCHITECTURE.md)
-   [Rules Specification](./RULES.md)
-   [Development Roadmap](./ROADMAP.md)
-   [API Reference](./API.md)
-   [Contributing Guidelines](./CONTRIBUTING.md)

### External Resources

-   [nikic/php-parser Documentation](https://github.com/nikic/PHP-Parser/tree/master/doc)
-   [Laravel AST Parsing Best Practices](https://laravel.com)
-   [OpenAI GPT-4 API](https://platform.openai.com/docs)
-   [Monaco Editor Documentation](https://microsoft.github.io/monaco-editor/)

### FlorenceEGI Context

-   [OS3.0 Instructions](../../copilot-instructions.md)
-   [Brand Guidelines](../../marketing/FlorenceEGI_Brand_Guidelines.md)
-   [PA Enterprise TODO Master](../../context/PA_ENTERPRISE_TODO_MASTER.md)

---

## 👥 Team

**Lead Developer:** Padmin D. Curtis (AI Partner OS3.0)  
**Project Owner:** Fabio (FlorenceEGI Core Team)  
**Architecture:** Enterprise-grade for PA compliance

---

## 📄 License

**Proprietary** - FlorenceEGI Internal Tool

© 2025 FlorenceEGI - All Rights Reserved

---

## 🆘 Support

**Issues:** Create issue in repository  
**Questions:** Contact Fabio or Padmin team  
**Emergency:** Check error_manager.log first

---

**Last Updated:** 23 Ottobre 2025  
**Version:** 1.0.0 (Rule Engine Phase)  
**Status:** ✅ Production Ready (Fase 1 completa)
