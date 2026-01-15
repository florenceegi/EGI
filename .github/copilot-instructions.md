# FlorenceEGI - AI Agent Instructions

> **Piattaforma ENTERPRISE per Pubbliche Amministrazioni (PA) - GDPR compliant, MiCA-safe**

---

## 🏗️ Architettura (Big Picture)

```
Laravel 11 :8000 → Algorand Blockchain (ASA/NFT)
    ↓
MariaDB (users, egis, collections, consents)
    ↓
AWS S3 (media storage) + IPFS (post-MVP)
```

| Componente | Path | Responsabilità |
|------------|------|----------------|
| **Controllers** | `app/Http/Controllers/` | API REST, auth Sanctum/Jetstream, GDPR |
| **Services** | `app/Services/` | Business logic, domain services |
| **Models** | `app/Models/` | Eloquent ORM, relazioni, soft deletes |
| **Enums** | `app/Enums/` | Type-safe enums (Payment, Gdpr, User) |
| **Ultra Packages** | `packages/ultra/` | ULM/UEM, Upload Manager, Config Manager |

**Domain Core**: `Egi` (NFT/digital asset) → `Collection` → `User` (Creator/Collector/PA)

---

## 🚫 REGOLA ZERO - FONDAMENTALE

**MAI DEDURRE | MAI INVENTARE METODI | SE NON SAI, CHIEDI**

```
Mancano info? → semantic_search / grep_search / read_file
Tutto fallito? → 🛑 STOP e CHIEDI
```

### Prima di usare QUALSIASI metodo:
```bash
# Verifica esistenza reale
grep_search "methodName" -includePattern="NomeService.php"
read_file app/Services/NomeService.php
```

---

## 🚨 P0 - REGOLE BLOCCANTI

### P0-1: Translation Keys Only (i18n)
```php
❌ 'message' => 'Success'                    // HARDCODED!
✅ 'message' => __('profile.updated')        // Chiave traduzione
```
File traduzioni: `resources/lang/{en,it,de,es,fr,pt}/`

### P0-2: Statistics Rule (No Hidden Limits)
```php
❌ ->take(10)->get()                         // NASCOSTO! PA perde fiducia
✅ ->limit($limit ?? null)->get()            // ESPLICITO nel signature
```

### P0-3: UEM-First (Error Handling)
```php
// UEM = errori strutturati (alert team) → $this->errorManager->handle()
// ULM = logging generico (trace) → $this->logger->info()
// MAI sostituire UEM con ULM!

try {
    $this->logger->info('Operation started');  // ULM trace
    // business logic...
} catch (\Exception $e) {
    $this->errorManager->handle('ERROR_CODE', [...], $e);  // UEM alert
}
```

### P0-4: MiCA-Safe (Compliance Europea)
- ✅ Emettere NFT/ASA per utenti
- ✅ Pagamenti FIAT via Stripe/PayPal
- ❌ MAI custodire crypto per utenti
- ❌ MAI fare exchange crypto/fiat

---

## 🔌 Pattern Standard ULM/UEM/GDPR

```php
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use App\Services\Gdpr\AuditLogService;
use App\Enums\Gdpr\GdprActivityCategory;

public function __construct(
    UltraLogManager $logger,
    ErrorManagerInterface $errorManager,
    AuditLogService $auditService
) { ... }

public function update(Request $request): RedirectResponse {
    try {
        $this->logger->info('Operation started', ['user_id' => $user->id]);
        $user->update($validated);
        $this->auditService->logUserAction($user, 'data_updated', $context,
            GdprActivityCategory::PERSONAL_DATA_UPDATE);
        return redirect()->with('success', __('messages.updated'));
    } catch (\Exception $e) {
        return $this->errorManager->handle('OP_FAILED', [...], $e);
    }
}
```

---

## 🛠️ Comandi Sviluppo

```bash
# Laravel server
php artisan serve --port=8000

# Frontend (Vite)
npm run dev

# Test
php artisan test
./vendor/bin/phpunit

# Algorand local (se attivo)
./start-algokit-local.sh
```

---

## 📁 File Chiave

| Scopo | File |
|-------|------|
| Routes Web | `routes/web.php` |
| Routes API | `routes/api.php` |
| Error Config | `config/error-manager.php` |
| GDPR Service | `app/Services/Gdpr/` |
| Consent Enums | `app/Enums/Gdpr/GdprActivityCategory.php` |
| Controller Pattern | `app/Http/Controllers/GdprController.php` |

---

## 🚫 Frontend: Librerie Bannate

- ❌ **Alpine.js** → Vanilla JS
- ❌ **Livewire per nuove feature** → REST + fetch()
- ❌ **jQuery** → querySelector, fetch, addEventListener
- ✅ **Vanilla JS / TypeScript** preferiti

---

## 📝 Commit Format

```
[TAG] Descrizione breve

- Dettaglio 1
- Dettaglio 2

Tags: [FEAT] [FIX] [REFACTOR] [DOC] [TEST] [CHORE]
```

---

## 🎯 Checklist Pre-Codice

1. ❓ Ho TUTTE le info? → NO = CHIEDI
2. ❓ Metodi VERIFICATI? → NO = grep/read_file PRIMA
3. ❓ Scrivo TESTO visibile? → USA `__('chiave')`
4. ❓ Aggiungo limiti? → DEVE essere parametrizzato
5. ❓ Faccio assunzioni? → DICHIARA e CHIEDI conferma

**Frasi corrette:** "Non trovo [X]. Dove si trova?" | "Sto assumendo [X]. Confermi?"
