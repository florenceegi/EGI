# NATAN_LOC - AI Agent Instructions (OS3.0)

> **Sistema AI Cognitivo Multi-Tenant per Pubbliche Amministrazioni**
> **"L'AI non pensa. Predice. Non deduce logicamente. Completa statisticamente."**

---

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

### 🌍 Lingue Obbligatorie (P0-8)

Ogni traduzione DEVE essere in **tutte e 6** le lingue:

| Codice | Lingua    | Path                                 |
| ------ | --------- | ------------------------------------ |
| `it`   | Italiano  | `laravel_backend/resources/lang/it/` |
| `en`   | English   | `laravel_backend/resources/lang/en/` |
| `de`   | Deutsch   | `laravel_backend/resources/lang/de/` |
| `es`   | Español   | `laravel_backend/resources/lang/es/` |
| `fr`   | Français  | `laravel_backend/resources/lang/fr/` |
| `pt`   | Português | `laravel_backend/resources/lang/pt/` |

❌ **VIETATO** tradurre solo in `it` + `en` → 🛑 BLOCCA

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

### 🔧 Processo Verifica Metodi

```bash
semantic_search "NatanChatService class methods"
grep_search "public function" -includePattern="laravel_backend/app/Services/NatanChatService.php"
read_file laravel_backend/app/Services/NatanChatService.php
# SE non trovo → 🛑 STOP e CHIEDI
```

---

## ♿ ACCESSIBILITY (A11Y) - Incrementale

**FILOSOFIA**: Non stop totale, ma **miglioramento incrementale**. Ogni fix/refactor su una pagina = occasione per sistemare A11Y.

### 📋 Checklist per OGNI pagina modificata

**SEMPRE applicare quando tocchi una view:**

```blade
✅ 1. SEMANTIC HTML (P2 - SHOULD)
   <main>           per contenuto principale
   <nav>            per navigation (non <div class="nav">)
   <header>/<footer> per intestazione/piè di pagina
   <section>/<article> per blocchi logici
   <aside>          per sidebar/contenuti secondari

✅ 2. ARIA LABELS (P2 - SHOULD)
   <button aria-label="{{ __('key.action') }}">        per icon-only buttons
   <nav aria-label="{{ __('key.nav_type') }}">         per distinguere multiple nav
   <img alt="{{ $description }}">                      SEMPRE (mai alt="")
   <svg aria-hidden="true">                            per icone decorative

✅ 3. KEYBOARD NAVIGATION (P2 - SHOULD)
   focus:ring-2 focus:ring-oro-fiorentino              visual focus indicator
   focus:outline-none focus:border-[color]             custom focus per form
   tabindex="0"                                        per custom interactive elements
   tabindex="-1"                                       per skip tab su decorative

✅ 4. SCREEN READER TEXT (P2 - SHOULD)
   <span class="sr-only">{{ __('key.sr_text') }}</span>  testo nascosto ma leggibile
   aria-live="polite"                                     per notifiche non urgenti
   aria-live="assertive"                                  per errori critici
   role="status"                                          per aggiornamenti di stato

✅ 5. FORM ACCESSIBILITY (P1 - MUST)
   <label for="input-id">                             SEMPRE associare label
   <input id="input-id" aria-describedby="help-id">   per text di aiuto
   @error('field')                                     mostrare errori con aria-invalid
```

### 🎯 Pattern Blade A11Y-Ready

```blade
{{-- ✅ BUTTON ICON-ONLY --}}
<button type="button" 
    aria-label="{{ __('creator.profile.edit_avatar') }}"
    class="focus:ring-2 focus:ring-oro-fiorentino focus:outline-none"
    onclick="openModal()">
    <svg aria-hidden="true">...</svg>
</button>

{{-- ✅ NAVIGATION TABS --}}
<nav aria-label="{{ __('creator.home.navigation_aria') }}">
    <div role="tablist">
        <button role="tab" 
            aria-selected="{{ $activeTab === 'portfolio' ? 'true' : 'false' }}"
            aria-controls="portfolio-panel"
            class="focus:ring-2">
            {{ __('creator.tabs.portfolio') }}
        </button>
    </div>
</nav>
<div id="portfolio-panel" role="tabpanel" aria-labelledby="portfolio-tab">...</div>

{{-- ✅ FORM FIELD --}}
<div>
    <label for="title" class="block text-sm font-medium">
        {{ __('form.title') }}
    </label>
    <input type="text" 
        id="title" 
        name="title"
        aria-describedby="title-help"
        @error('title') aria-invalid="true" aria-describedby="title-error" @enderror
        class="focus:ring-2 focus:border-oro-fiorentino"
    >
    <p id="title-help" class="text-sm text-gray-400">{{ __('form.title_help') }}</p>
    @error('title')
        <p id="title-error" class="text-sm text-red-500" role="alert">{{ $message }}</p>
    @enderror
</div>

{{-- ✅ LIVE REGION for dynamic updates --}}
<div aria-live="polite" aria-atomic="true" class="sr-only" id="status-announcements"></div>
<script>
    function announceToScreenReader(message) {
        const announcer = document.getElementById('status-announcements');
        announcer.textContent = message;
        setTimeout(() => announcer.textContent = '', 1000);
    }
</script>

{{-- ✅ SKIP LINK --}}
<a href="#main-content" 
   class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-50 focus:px-4 focus:py-2">
    {{ __('a11y.skip_to_content') }}
</a>
<main id="main-content">...</main>
```

### 🔍 Quick Check Prima di Commit

```bash
# 1. Cerca <div> che dovrebbero essere semantici
grep -n "<div class=\"nav" resources/views/path/to/file.blade.php

# 2. Verifica button senza aria-label (icon-only)
grep -n "<button[^>]*><svg" resources/views/path/to/file.blade.php

# 3. Cerca img senza alt
grep -n '<img[^>]*>' resources/views/path/to/file.blade.php | grep -v 'alt='

# 4. Verifica focus indicators
grep -n "focus:" resources/views/path/to/file.blade.php
```

### 📊 Priorità Interventi

| Priorità | Elemento               | Impatto            | Dove                     |
| -------- | ---------------------- | ------------------ | ------------------------ |
| **P1**   | Form labels            | 🔴 CRITICAL        | Tutti i form             |
| **P1**   | Alt text immagini      | 🔴 CRITICAL        | Tutte le `<img>`         |
| **P2**   | Semantic HTML          | 🟡 HIGH            | Main content areas       |
| **P2**   | Focus indicators       | 🟡 HIGH            | Tutti i controlli        |
| **P2**   | ARIA labels (buttons)  | 🟡 HIGH            | Icon-only buttons        |
| **P3**   | Tab roles              | 🟢 MEDIUM          | Navigation tabs          |
| **P3**   | Live regions           | 🟢 MEDIUM          | Notifiche/aggiornamenti  |
| **P3**   | Skip links             | 🟢 LOW (già fatto) | Wizard registrazione     |

### 🎓 Translation Keys A11Y

Aggiungi a `resources/lang/*/a11y.php` (6 lingue):

```php
return [
    // Skip links
    'skip_to_content' => 'Salta al contenuto principale',
    'skip_to_navigation' => 'Vai alla navigazione',
    
    // Screen reader descriptions
    'loading' => 'Caricamento in corso...',
    'success' => 'Operazione completata con successo',
    'error' => 'Si è verificato un errore',
    
    // Button actions
    'close' => 'Chiudi',
    'open_menu' => 'Apri menu',
    'edit' => 'Modifica',
    'delete' => 'Elimina',
    'save' => 'Salva',
];
```

### ⚠️ NON Bloccare Deploy

- A11Y è **P2 (SHOULD)**, non **P0 (BLOCKING)**
- Fix incrementali: ogni pagina toccata → migliora A11Y
- Test con: Chrome DevTools Lighthouse (Accessibility score)
- Target: **WCAG 2.1 Level AA** (non AAA)

---

## 🏗️ Architettura

```
Frontend → Laravel Backend :8000 → Python FastAPI :8080 → MongoDB Atlas
                  ↓
             MariaDB :3306 (users, tenants, consents)
```

| Componente         | Path                 | Porta |
| ------------------ | -------------------- | ----- |
| Backend            | `laravel_backend/`   | 8000  |
| AI Gateway         | `python_ai_service/` | 8080  |
| Database Cognitivo | Docker MongoDB 7     | 27017 |

**Flow**: Frontend → Laravel API → Python FastAPI (USE Pipeline + RAG-Fortress) → MongoDB

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

## ⚡ Translations with Ultra Translation Manager (CRITICAL)

**PROBLEM**: Ultra Translation Manager (UTM) caches translated strings aggressively. When using dynamic parameters (`__('key', ['param' => $val])`), UTM may cache the translation with the FIRST user's data and serve the same cached string to ALL subsequent users, causing **STALE/WRONG data** (e.g., greeting "Ciao Mario" shown to user "Luigi").

**This affects ANY dynamic translation**, not just Livewire components:

- Blade views with user-specific data
- Controllers passing variables to translations
- Any context where parameters change per request

**RULE**: Use **Atomic Translations** (split static text + dynamic variables).

❌ **BAD (UTM caches with first user's data)**:

```php
{{ __('messages.greeting', ['name' => $user->name]) }}
// Translation: "Ciao :name!"
// UTM may cache "Ciao Mario!" and show it to ALL users
```

✅ **GOOD (Atomic & Cache-Safe)**:

```php
{{ __('messages.greeting') }} {{ $user->name }}{{ __('messages.greeting_suffix') }}
// Translations: "Ciao" + VARIABLE + "!"
// Static parts cached, dynamic parts always fresh
```

**Migration Pattern**:

```php
// OLD translation file:
'greeting' => 'Hello :name! Welcome back.',
'progress' => 'You completed :count of :total items.',

// NEW atomic translations:
'greeting' => 'Hello',
'greeting_suffix' => '! Welcome back.',
'progress_intro' => 'You completed',
'progress_of' => 'of',
'progress_suffix' => ' items.',
```

**IMPORTANT**: When migrating to atomic translations, update ALL 6 languages (P0-9).

**See**: `docs/FlorenceEGI/debiti_tecnici.md` - UTM Dynamic Parameter Caching Issue

---

## 🐍 Pattern Python AI Gateway

```python
# USE Pipeline: QueryAnalyzer → [PREBUILT|AGGREGATION|HYBRID] → RAG-Fortress → URS Score
```

**RAG-Fortress Components**:

- HybridRetriever → EvidenceVerifier → ClaimExtractor → ConstrainedSynthesizer → HostileFactChecker

---

## 📁 File Chiave

| Scopo              | Path                                                |
| ------------------ | --------------------------------------------------- |
| API Routes         | `laravel_backend/routes/api.php`                    |
| Chat Service       | `laravel_backend/app/Services/NatanChatService.php` |
| USE Pipeline       | `python_ai_service/app/services/use_pipeline.py`    |
| RAG-Fortress       | `python_ai_service/app/services/rag_fortress/`      |
| GDPR Enums         | `laravel_backend/app/Enums/Gdpr/`                   |
| Debiti Tecnici     | `docs/FlorenceEGI/debiti_tecnici.md`                |
| Stato progetto     | `docs/Core/00_NATAN_LOC_STATO_DELLARTE.md`          |
| Architettura       | `docs/Core/01_PLATFORME_ARCHITECTURE.md`            |
| Anti-hallucination | `docs/Core/03_ANTI_HALLUCINATION_TECH.md`           |

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

## 🛠️ Comandi

```bash
./start_services.sh              # Avvia MongoDB + FastAPI
./stop_services.sh               # Stop servizi
cd laravel_backend && php artisan serve --port=8000  # Laravel
cd python_ai_service && uvicorn app.main:app --reload --port=8080  # Python
```

---

## 📡 API Endpoints

| Endpoint                         | Metodo | Descrizione        |
| -------------------------------- | ------ | ------------------ |
| `/api/v1/use/query`              | POST   | Query USE Pipeline |
| `/api/v1/chat`                   | POST   | Chat RAG-Fortress  |
| `/api/v1/faro/facets/{tenantId}` | GET    | Facets documento   |

---

**OS3.0 - "Less talk, more code. Ship it."**

## **💎 FIRMA STANDARD**

```php
/**
 * @package App\Http\Controllers\[Area]
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - [Context])
 * @date 2025-10-28
 * @purpose [Clear, specific purpose]
 */
```
