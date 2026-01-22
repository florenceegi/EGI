# FlorenceEGI - AI Agent Instructions (OS3.0)

> **Piattaforma ENTERPRISE per PA - GDPR compliant, MiCA-safe**
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
| **P0-8** | MiCA-Safe | ❌ MAI custodire/exchange crypto |
| **P0-9** | i18n 6 Lingue | Traduzioni in TUTTE: `it`, `en`, `de`, `es`, `fr`, `pt` |

### 🌍 Lingue Obbligatorie (P0-9)

Ogni traduzione DEVE essere in **tutte e 6** le lingue:

| Codice | Lingua | Path |
|--------|--------|------|
| `it` | Italiano | `resources/lang/it/` |
| `en` | English | `resources/lang/en/` |
| `de` | Deutsch | `resources/lang/de/` |
| `es` | Español | `resources/lang/es/` |
| `fr` | Français | `resources/lang/fr/` |
| `pt` | Português | `resources/lang/pt/` |

❌ **VIETATO** tradurre solo in `it` + `en` → 🛑 BLOCCA

### 🔍 Prima di Ogni Risposta

```
1. Ho TUTTE le info? → NO = 🛑 CHIEDI
2. Metodi VERIFICATI? → NO = 🛑 semantic_search/grep/read_file
3. Pattern simile esiste? → Non so = 🛑 CHIEDI esempio
4. Sto ASSUMENDO? → SÌ = 🛑 DICHIARA e CHIEDI
5. Limiti impliciti? → SÌ = 🛑 RENDI ESPLICITO
```

### 🔧 Processo Verifica Metodi

```bash
semantic_search "UserService class methods"
grep_search "public function" -includePattern="app/Services/UserService.php"
read_file app/Services/UserService.php
# SE non trovo → 🛑 STOP e CHIEDI
```

---

## 🏗️ Architettura

```
Laravel 11 :8000 → Algorand Blockchain (ASA/NFT)
    ↓
MariaDB (users, egis, collections, consents) → AWS S3 (media)
```

| Componente | Path |
|------------|------|
| Controllers | `app/Http/Controllers/` |
| Services | `app/Services/` |
| Models | `app/Models/` |
| Enums | `app/Enums/` |
| Ultra Packages | `packages/ultra/` |

---

## 🔌 Pattern ULM/UEM/GDPR (Template Obbligatorio)

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

## 📁 File Chiave

| Scopo | Path |
|-------|------|
| Routes Web/API | `routes/web.php`, `routes/api.php` |
| GDPR Service | `app/Services/Gdpr/` |
| GDPR Enums | `app/Enums/Gdpr/GdprActivityCategory.php` |
| Error Config | `config/error-manager.php` |
| Oracode Docs | `docs/Oracode_Systems/` |

---

## 🧬 Oracode System

**3 Livelli**: OSZ (kernel) → OS3 (AI discipline) → OS4 (human education)

**6+1 Pilastri**: Intenzionalità, Semplicità, Coerenza, Circolarità, Evoluzione, Sicurezza + **REGOLA ZERO**

**Concetti OSZ**:
- **EGI**: `Wrapper<T> + Regole + Audit + Valore`
- **Interface**: Giunture stabili (API, contratti)
- **Instance**: Organi sostituibili
- **Nerve**: Sistema nervoso AI

---

## ⚡ Priorità

| P | Nome | Conseguenza |
|---|------|-------------|
| **P0** | BLOCKING | 🛑 STOP totale |
| **P1** | MUST | Non production-ready |
| **P2** | SHOULD | Debt tecnico |
| **P3** | REFERENCE | Info only |

---

## 🚫 Frontend BANNATO (P0-10)

**PRIMA di scrivere QUALSIASI codice frontend:**

```
🛑 CHECKPOINT FRONTEND OBBLIGATORIO:
□ Alpine.js? → VIETATO - usa addEventListener/onclick
□ Livewire? → VIETATO per nuove feature
□ jQuery? → VIETATO - usa querySelector/fetch
□ x-data, x-show, @click? → VIETATO (sono Alpine!)
✅ SOLO: Vanilla JS, TypeScript, addEventListener, onclick, fetch
```

**Se stai per scrivere `x-data`, `x-show`, `@click`, `$el`, `$dispatch` → 🛑 STOP! È Alpine!**

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
php artisan serve --port=8000  # Laravel
npm run dev                     # Frontend
php artisan test               # Test
```

---

**OS3.0 - "Less talk, more code. Ship it."**
