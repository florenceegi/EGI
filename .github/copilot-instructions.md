# NATAN_LOC - AI Agent Instructions (OS3.0)

> **Sistema AI Cognitivo Multi-Tenant per Pubbliche Amministrazioni**
> **"L'AI non pensa. Predice. Non deduce logicamente. Completa statisticamente."**

---

## 🛑 REGOLE P0 - BLOCCANTI (Violazione = STOP immediato)

| # | Regola | Cosa Fare |
|---|--------|-----------|
| **P0-1** | REGOLA ZERO | MAI dedurre. Se non sai → 🛑 CHIEDI |
| **P0-2** | Translation Keys | `__('key')` mai stringhe hardcoded |
| **P0-3** | Statistics Rule | No `->take(10)` nascosti, sempre param espliciti |
| **P0-4** | Anti-Method-Invention | Verifica metodo esiste PRIMA di usarlo |
| **P0-5** | UEM-First | Errori → `$errorManager->handle()`, mai solo ULM |
| **P0-6** | Anti-Service-Method | `read_file` + `grep` prima di usare service |
| **P0-7** | Anti-Enum-Constant | Verifica costanti enum esistono |
| **P0-8** | Complete Flow Analysis | Map ENTIRE flow BEFORE any fix (15-35 min) |
| **P0-9** | i18n 6 Lingue | Traduzioni in TUTTE: `it`, `en`, `de`, `es`, `fr`, `pt` |

### 🌍 Lingue Obbligatorie (P0-8)

Ogni traduzione DEVE essere in **tutte e 6** le lingue:

| Codice | Lingua | Path |
|--------|--------|------|
| `it` | Italiano | `laravel_backend/resources/lang/it/` |
| `en` | English | `laravel_backend/resources/lang/en/` |
| `de` | Deutsch | `laravel_backend/resources/lang/de/` |
| `es` | Español | `laravel_backend/resources/lang/es/` |
| `fr` | Français | `laravel_backend/resources/lang/fr/` |
| `pt` | Português | `laravel_backend/resources/lang/pt/` |

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

## 🏗️ Architettura

```
Frontend → Laravel Backend :8000 → Python FastAPI :8080 → MongoDB Atlas
                  ↓
             MariaDB :3306 (users, tenants, consents)
```

| Componente | Path | Porta |
|------------|------|-------|
| Backend | `laravel_backend/` | 8000 |
| AI Gateway | `python_ai_service/` | 8080 |
| Database Cognitivo | Docker MongoDB 7 | 27017 |

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

## ⚡ Livewire & Translations (CRITICAL)

**PROBLEM**: Livewire's DOM diffing often fails to update translated strings with dynamic parameters (`__('key', ['param' => $val])`), causing STALE texts (caching).

**RULE**: Use **Atomic Translations**.
Split sentences into static translated parts and raw Blade variables.

❌ **BAD (Livewire Fails)**:
```php
{{ __('messages.sold', ['item' => $item->name]) }}
```

✅ **GOOD (Atomic & Robust)**:
```php
{{ __('messages.user') }} {{ $user->name }} {{ __('messages.bought') }} {{ $item->name }}
```

**NEVER** use array parameter injection inside a Livewire Component view.

---

## 🐍 Pattern Python AI Gateway

```python
# USE Pipeline: QueryAnalyzer → [PREBUILT|AGGREGATION|HYBRID] → RAG-Fortress → URS Score
```

**RAG-Fortress Components**:
- HybridRetriever → EvidenceVerifier → ClaimExtractor → ConstrainedSynthesizer → HostileFactChecker

---

## 📁 File Chiave

| Scopo | Path |
|-------|------|
| API Routes | `laravel_backend/routes/api.php` |
| Chat Service | `laravel_backend/app/Services/NatanChatService.php` |
| USE Pipeline | `python_ai_service/app/services/use_pipeline.py` |
| RAG-Fortress | `python_ai_service/app/services/rag_fortress/` |
| GDPR Enums | `laravel_backend/app/Enums/Gdpr/` |
| Stato progetto | `docs/Core/00_NATAN_LOC_STATO_DELLARTE.md` |
| Architettura | `docs/Core/01_PLATFORME_ARCHITECTURE.md` |
| Anti-hallucination | `docs/Core/03_ANTI_HALLUCINATION_TECH.md` |

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

| P | Nome | Conseguenza |
|---|------|-------------|
| **P0** | BLOCKING | 🛑 STOP totale |
| **P1** | MUST | Non production-ready |
| **P2** | SHOULD | Debt tecnico |
| **P3** | REFERENCE | Info only |

---

## 📝 TAG System v2.0

Formato: `[TAG] Descrizione breve`

| Tag | Peso | Tag | Peso | Tag | Peso | Tag | Peso |
|-----|------|-----|------|-----|------|-----|------|
| FEAT | 1.0 | FIX | 1.5 | REFACTOR | 2.0 | TEST | 1.2 |
| DEBUG | 1.3 | DOC | 0.8 | CONFIG | 0.7 | CHORE | 0.6 |
| I18N | 0.7 | PERF | 1.4 | SECURITY | 1.8 | WIP | 0.3 |
| REVERT | 0.5 | MERGE | 0.4 | DEPLOY | 0.8 | UPDATE | 0.6 |

Alias: `[FEAT]` = `feat:` = ✨

---

## 🔒 Git Hooks

| Regola | Trigger | Azione |
|--------|---------|--------|
| R1 | >100 righe rimosse/file | 🛑 BLOCCA |
| R2 | 50-100 righe rimosse | ⚠️ WARNING |
| R3 | >50% contenuto rimosso | 🛑 BLOCCA |
| R4 | >500 righe totali rimosse | 🛑 BLOCCA |

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

| Endpoint | Metodo | Descrizione |
|----------|--------|-------------|
| `/api/v1/use/query` | POST | Query USE Pipeline |
| `/api/v1/chat` | POST | Chat RAG-Fortress |
| `/api/v1/faro/facets/{tenantId}` | GET | Facets documento |

---

**OS3.0 - "Less talk, more code. Ship it."**
