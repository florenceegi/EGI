# FlorenceEGI Art (art.florenceegi.com) — AI Agent Instructions (OS3.0)

> **Marketplace Arte & Certificazione EGI su Blockchain Algorand**
> **"L'AI non pensa. Predice. Non deduce logicamente. Completa statisticamente."**

---

<!-- ══════════════════════════════════════════════════════════════
     CORE CONDIVISO — Questa sezione è IDENTICA in tutti i progetti
     dell'ecosistema FlorenceEGI (EGI, EGI-HUB-HOME-REACT, NATAN_LOC).
     Qualsiasi modifica va replicata in tutti e 3 i file.
     ══════════════════════════════════════════════════════════════ -->

## 🛑 REGOLE P0 - BLOCCANTI (Violazione = STOP immediato)

| #        | Regola                 | Cosa Fare                                                           |
| -------- | ---------------------- | ------------------------------------------------------------------- |
| **P0-0** | **NO ALPINE/LIVEWIRE** | **VIETATO SCRIVERE NUOVO CODICE ALPINE/LIVEWIRE. Solo Vanilla/TS.** |
| **P0-1** | REGOLA ZERO            | MAI dedurre. Se non sai → 🛑 CHIEDI                                 |
| **P0-2** | Translation Keys       | `__('key')` mai stringhe hardcoded                                  |
| **P0-3** | Statistics Rule        | No `->take(10)` nascosti, sempre param espliciti                    |
| **P0-4** | Anti-Method-Invention  | Verifica metodo esiste PRIMA di usarlo                              |
| **P0-5** | UEM-First              | Errori → `$errorManager->handle()`, mai solo ULM                    |
| **P0-6** | Anti-Service-Method    | `read_file` + `grep` prima di usare service                         |
| **P0-7** | Anti-Enum-Constant     | Verifica costanti enum esistono                                     |
| **P0-8** | Complete Flow Analysis | Map ENTIRE flow BEFORE any fix (15-35 min)                          |
| **P0-9** | i18n 6 Lingue          | Traduzioni in TUTTE: `it`, `en`, `de`, `es`, `fr`, `pt`             |

### 🔍 Prima di Ogni Risposta

```
1. Ho TUTTE le info? → NO = 🛑 CHIEDI
2. Metodi VERIFICATI? → NO = 🛑 semantic_search/grep/read_file
3. Pattern simile esiste? → Non so = 🛑 CHIEDI esempio
4. Sto ASSUMENDO? → SÌ = 🛑 DICHIARA e CHIEDI
5. Limiti impliciti? → SÌ = 🛑 RENDI ESPLICITO
```

### 🔄 Prima di Ogni FIX/DEBUG (P0-8)

```
1. Flow MAPPATO? (user action → response) → NO = 🛑 MAP FIRST
2. Types TRACCIATI? (ogni variabile/step) → NO = 🛑 TRACE FIRST
3. ALL occurrences TROVATE? (grep/search) → NO = 🛑 FIND ALL
4. Context VERIFICATO? (dependencies/patterns) → NO = 🛑 VERIFY

TEMPO: 15-35 min | RISPARMIO: 2+ ore debugging
```

---

## ♿ ACCESSIBILITY (A11Y) - Incrementale

**FILOSOFIA**: Non stop totale, ma **miglioramento incrementale**. Ogni fix/refactor su una pagina = occasione per sistemare A11Y.

### 📋 Checklist per OGNI pagina modificata

```
✅ 1. SEMANTIC HTML (P2)
   <main>, <nav>, <header>/<footer>, <section>/<article>, <aside>

✅ 2. ARIA LABELS (P2)
   aria-label per icon-only buttons, aria-label per nav multiple
   alt SEMPRE su <img>, aria-hidden="true" su icone decorative

✅ 3. KEYBOARD NAVIGATION (P2)
   focus:ring-2 focus:ring-oro-fiorentino, tabindex="0" per custom elements

✅ 4. SCREEN READER TEXT (P2)
   <span class="sr-only">, aria-live="polite"/"assertive", role="status"

✅ 5. FORM ACCESSIBILITY (P1)
   <label for="id"> SEMPRE, aria-describedby per help text, aria-invalid per errori
```

**Target**: WCAG 2.1 Level AA — A11Y è **P2 (SHOULD)**, non P0.

---

## 🧬 Oracode System

**3 Livelli**: OSZ (kernel) → OS3 (AI discipline) → OS4 (human education)

**6+1 Pilastri**: Intenzionalità, Semplicità, Coerenza, Circolarità, Evoluzione, Sicurezza + **REGOLA ZERO**

**Concetti OSZ**:

- **EGI**: `Wrapper<T> + Regole + Audit + Valore`
- **USE**: Ultra Semantic Engine — pipeline query semantiche
- **URS**: Unified Reliability Score — metrica affidabilità risposta AI
- **Nerve**: Sistema nervoso AI (governatori, validatori)

---

## ⚡ Priorità

| P      | Nome      | Conseguenza          |
| ------ | --------- | -------------------- |
| **P0** | BLOCKING  | 🛑 STOP totale       |
| **P1** | MUST      | Non production-ready |
| **P2** | SHOULD    | Debt tecnico         |
| **P3** | REFERENCE | Info only            |

---

## 📝 TAG System v2.0

Formato: `[TAG] Descrizione breve`

| Tag    | Peso | Tag   | Peso | Tag      | Peso | Tag    | Peso |
| ------ | ---- | ----- | ---- | -------- | ---- | ------ | ---- |
| FEAT   | 1.0  | FIX   | 1.5  | REFACTOR | 2.0  | TEST   | 1.2  |
| DEBUG  | 1.3  | DOC   | 0.8  | CONFIG   | 0.7  | CHORE  | 0.6  |
| I18N   | 0.7  | PERF  | 1.4  | SECURITY | 1.8  | WIP    | 0.3  |
| REVERT | 0.5  | MERGE | 0.4  | DEPLOY   | 0.8  | UPDATE | 0.6  |

Alias: `[FEAT]` = `feat:` = ✨

---

## 🔒 Git Hooks

| Regola | Trigger                   | Azione     |
| ------ | ------------------------- | ---------- |
| R1     | >100 righe rimosse/file   | 🛑 BLOCCA  |
| R2     | 50-100 righe rimosse      | ⚠️ WARNING |
| R3     | >50% contenuto rimosso    | 🛑 BLOCCA  |
| R4     | >500 righe totali rimosse | 🛑 BLOCCA  |

Bypass: `git commit --no-verify` (solo se intenzionale)

---

## 💎 FIRMA STANDARD

```php
/**
 * @package App\Http\Controllers\[Area]
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - [Context])
 * @date 2025-10-28
 * @purpose [Clear, specific purpose]
 */
```

<!-- ══════════════════════════════════════════════════════════════
     FINE CORE CONDIVISO — Da qui in poi: specifico per EGI
     ══════════════════════════════════════════════════════════════ -->

---

## 🏗️ Architettura EGI

```
Browser → Laravel 11 (PHP 8.3) → PostgreSQL + pgvector (RAG)
                ↓                         ↓
         AlgoKit Microservice    Anthropic Claude API
         (Node.js :3001)         (AI Sidebar / NATAN)
                ↓
         Algorand Blockchain (ASA)
```

| Componente            | Tecnologia                                 | Porta |
| --------------------- | ------------------------------------------ | ----- |
| Backend               | Laravel 11.31 + PHP 8.2+                   | 8000  |
| Frontend              | Vite 5.4 + React 19 + TS + Tailwind + DaisyUI | 5174  |
| Database              | PostgreSQL + pgvector (`rag_natan` schema)  | 5432  |
| Blockchain            | AlgoKit Microservice (Node.js)              | 3001  |
| AI LLM                | Anthropic Claude API                        | —     |
| Payments              | Stripe + PayPal (split payment)             | —     |
| Storage               | AWS S3 + CloudFront (Spatie MediaLibrary)   | —     |

---

### 🌍 Lingue Obbligatorie (P0-9)

Ogni traduzione DEVE essere in **tutte e 6** le lingue:

| Codice | Lingua    | Path                     |
| ------ | --------- | ------------------------ |
| `it`   | Italiano  | `resources/lang/it/`     |
| `en`   | English   | `resources/lang/en/`     |
| `de`   | Deutsch   | `resources/lang/de/`     |
| `es`   | Español   | `resources/lang/es/`     |
| `fr`   | Français  | `resources/lang/fr/`     |
| `pt`   | Português | `resources/lang/pt/`     |

❌ **VIETATO** tradurre solo in `it` + `en` → 🛑 BLOCCA

---

## 🔌 Pattern Laravel (ULM/UEM/GDPR)

```php
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use App\Services\Gdpr\AuditLogService;
use App\Enums\Gdpr\GdprActivityCategory;

class ExampleController extends Controller
{
    public function __construct(
        private UltraLogManager $logger,
        private ErrorManagerInterface $errorManager,
        private AuditLogService $auditService
    ) {}

    public function update(Request $request): RedirectResponse
    {
        try {
            $this->logger->info('Operation started', ['user_id' => $user->id]);
            $user->update($validated);

            // GDPR Audit (P0-7: enum verificato)
            $this->auditService->logUserAction($user, 'data_updated', $context,
                GdprActivityCategory::PERSONAL_DATA_UPDATE);

            return redirect()->with('success', __('messages.updated')); // P0-2
        } catch (\Exception $e) {
            return $this->errorManager->handle('OP_FAILED', [...], $e); // P0-5
        }
    }
}
```

---

## ⚡ Ultra Translation Manager (CRITICAL)

**PROBLEM**: UTM caches translated strings aggressively. Dynamic parameters (`__('key', ['param' => $val])`) may cache with the FIRST user's data and serve to ALL users.

**RULE**: Use **Atomic Translations** (split static text + dynamic variables).

❌ **BAD (UTM caches with first user's data)**:

```php
{{ __('messages.greeting', ['name' => $user->name]) }}
```

✅ **GOOD (Atomic & Cache-Safe)**:

```php
{{ __('messages.greeting') }} {{ $user->name }}{{ __('messages.greeting_suffix') }}
```

**Migration Pattern**:

```php
// OLD: 'greeting' => 'Hello :name! Welcome back.',
// NEW:
'greeting' => 'Hello',
'greeting_suffix' => '! Welcome back.',
```

**IMPORTANT**: When migrating to atomic translations, update ALL 6 languages (P0-9).

**See**: `docs/FlorenceEGI/debiti_tecnici.md` — UTM Dynamic Parameter Caching Issue

---

## 🔧 Processo Verifica Metodi (EGI)

```bash
grep "public function" app/Services/ArtAdvisorService.php
grep "public function" app/Services/AnthropicService.php
grep "public function" app/Services/AlgorandService.php
# SE non trovo → 🛑 STOP e CHIEDI
```

---

## 📁 File Chiave EGI

| Scopo                   | Path                                            |
| ----------------------- | ----------------------------------------------- |
| Routes web              | `routes/web.php`                                |
| Routes API              | `routes/api.php`                                |
| Routes company          | `routes/company.php`                            |
| Routes creator          | `routes/creator.php`                            |
| Routes CoA              | `routes/coa.php`                                |
| Routes GDPR             | `routes/gdpr.php`                               |
| AI Sidebar Service      | `app/Services/ArtAdvisorService.php`            |
| Anthropic LLM           | `app/Services/AnthropicService.php`             |
| Algorand Service        | `app/Services/AlgorandService.php`              |
| Collection Service      | `app/Services/CollectionService.php`            |
| Certificate Generator   | `app/Services/CertificateGeneratorService.php`  |
| RAG Search              | `app/Services/AI/RAGSearchService.php`          |
| Fee Structure           | `app/Enums/Fees/FeeStructureEnum.php`           |
| GDPR Enums              | `app/Enums/Gdpr/`                               |
| AI View Contexts        | `config/ai_view_contexts.php`                   |
| EGI Config              | `config/egi.php`                                |
| Error Manager           | `config/error-manager.php`                      |
| Debiti Tecnici          | `docs/FlorenceEGI/debiti_tecnici.md`            |

---

## 🛠️ Comandi EGI

```bash
php artisan serve                                      # Laravel dev server
npm run dev                                            # Vite dev (port 5174)
cd algokit-microservice && node server.js               # Algorand microservice (:3001)
php artisan config:cache && php artisan cache:clear     # Dopo modifiche config
```

---

**OS3.0 — "Less talk, more code. Ship it."**
