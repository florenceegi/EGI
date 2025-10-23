# Padmin Analyzer - User Guide

**Quick Start Guide for FlorenceEGI Developers**

---

## 🎯 What is Padmin Analyzer?

Padmin Analyzer è il tuo **quality guardian** che:
- ✅ Controlla il codice per violazioni OS3.0
- ⚡ Suggerisce fix tramite AI
- 📊 Traccia violations nel tempo
- 🛡️ Previene errori prima del deployment

**Think of it as:** Your personal code reviewer that knows FlorenceEGI rules.

---

## 🚀 Quick Start (5 minuti)

### 1. Accedi alla Dashboard

```
URL: http://localhost:8004/superadmin/padmin
Richiede: SuperAdmin login
```

### 2. Prima Scansione

1. Click **"Run Scan"** (pulsante oro top-right)
2. Inserisci path: `app/Services/Test`
3. Seleziona regole (default: tutte ✓)
4. Click **"Avvia Scansione"**
5. Aspetta 2-3 secondi...
6. **Risultati** in tabella!

### 3. Gestisci Violations

**Hai trovato violations? Due opzioni:**

#### Opzione A: Fix with AI ⚡
1. Click **"⚡ AI"** sulla violation
2. Modal mostra prompt AI
3. Click **"Copia"**
4. Apri GitHub Copilot Chat
5. Incolla prompt
6. Applica fix suggerito
7. Torna e click **"✓ Mark as Fixed"**

#### Opzione B: Fix Manuale
1. Leggi violation message
2. Apri file indicato
3. Vai a linea indicata
4. Correggi problema
5. Click **"✓ Mark as Fixed"**

---

## 📖 Understanding Violations

### Priority Levels

| Priority | Significato | Azione |
|----------|-------------|--------|
| **P0** 🔴 | **BLOCKING** - Non può andare in production | Fix SUBITO |
| **P1** 🟡 | **HIGH** - Rischio medio, fix presto | Fix entro 1 settimana |
| **P2** 🔵 | **MEDIUM** - Migliora qualità | Fix quando possibile |
| **P3** ⚪ | **LOW** - Nice to have | Opzionale |

### Severity Types

- **critical:** Errore grave, sistema a rischio
- **error:** Errore significativo, funzionalità broken
- **warning:** Possibile problema, richiede attenzione
- **info:** Informazione, best practice suggestion

---

## 🎯 Common Violations & How to Fix

### REGOLA_ZERO: Method Doesn't Exist

**Violation:**
```
Using blacklisted method hasConsentFor()
File: UserController.php, Line: 42
```

**Problema:**
```php
// ❌ SBAGLIATO
if ($this->consentService->hasConsentFor('profile-update')) {
    // hasConsentFor() non esiste!
}
```

**Fix:**
```php
// ✅ CORRETTO
use App\Models\User;

if ($this->consentService->hasConsent($user, 'allow-personal-data-processing')) {
    // hasConsent() esiste ed è il metodo corretto
}
```

**Come verificare metodi corretti:**
1. Cerca nel repo: `grep -r "public function hasConsent" app/`
2. Leggi classe: `app/Services/Gdpr/ConsentService.php`
3. Usa AI Fix per suggerimento automatico

---

### UEM_FIRST: Missing Error Handling

**Violation:**
```
Catch block without errorManager->handle()
File: ProfileController.php, Line: 89
```

**Problema:**
```php
// ❌ SBAGLIATO
try {
    $user->update($data);
} catch (\Exception $e) {
    Log::error('Update failed'); // Solo log generico!
}
```

**Fix:**
```php
// ✅ CORRETTO
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

protected ErrorManagerInterface $errorManager;

public function __construct(ErrorManagerInterface $errorManager) {
    $this->errorManager = $errorManager;
}

try {
    $user->update($data);
} catch (\Exception $e) {
    $this->errorManager->handle('USER_UPDATE_FAILED', [
        'user_id' => $user->id,
        'context' => $data
    ], $e);
    
    return redirect()->back()->withErrors(['error' => 'Update failed']);
}
```

**Perché importante:**
- Team riceve notifica dell'errore
- Utente vede messaggio user-friendly
- Errore tracciato per monitoring

---

### STATISTICS: Hidden Data Limits

**Violation:**
```
Using ->take() without explicit parameter in StatisticsService
File: EgiStatisticsService.php, Line: 54
```

**Problema:**
```php
// ❌ SBAGLIATO
public function getTopEgis(): Collection
{
    return Egi::orderBy('likes')->take(10)->get();
    // Utente vede 10 EGI ma pensa siano tutti!
}
```

**Fix:**
```php
// ✅ CORRETTO
public function getTopEgis(?int $limit = null): Collection
{
    $query = Egi::orderBy('likes');
    
    // Limit SOLO se esplicitamente richiesto
    if ($limit !== null) {
        $query->limit($limit);
    }
    
    return $query->get(); // Default: tutti i record
}
```

**Rationale PA:**
Un dirigente PA che vede dati incompleti perde fiducia. Per clienti istituzionali, dati parziali = sistema inaffidabile.

---

### GDPR_COMPLIANCE: Missing Consent/Audit

**Violation:**
```
User model update without consent check
File: ProfileController.php, Line: 76
```

**Problema:**
```php
// ❌ SBAGLIATO
public function update(Request $request)
{
    $user = Auth::user();
    $user->update($request->validated());
    // Mancano: consent check + audit log!
}
```

**Fix:**
```php
// ✅ CORRETTO
use App\Services\Gdpr\ConsentService;
use App\Services\Gdpr\AuditLogService;
use App\Enums\Gdpr\GdprActivityCategory;

protected ConsentService $consentService;
protected AuditLogService $auditService;

public function __construct(
    ConsentService $consentService,
    AuditLogService $auditService
) {
    $this->consentService = $consentService;
    $this->auditService = $auditService;
}

public function update(Request $request)
{
    $user = Auth::user();
    
    // 1. Check consent
    if (!$this->consentService->hasConsent($user, 'allow-personal-data-processing')) {
        return redirect()->back()->withErrors(['consent' => 'Consent required']);
    }
    
    // 2. Update
    $validated = $request->validated();
    $user->update($validated);
    
    // 3. Audit trail
    $this->auditService->logUserAction(
        $user,
        'personal_data_updated',
        ['fields' => array_keys($validated)],
        GdprActivityCategory::PERSONAL_DATA_UPDATE
    );
    
    return redirect()->back()->with('success', 'Profile updated');
}
```

**GDPR Requirements:**
- Consent check obbligatorio prima di update dati personali
- Audit trail completo di ogni modifica
- Dependency injection corretto

---

## 🔍 Using Filters

### Filter by Priority

**Scenario:** "Voglio vedere solo P0 violations"

1. Dropdown **"Priorità"** → Seleziona **"P0"**
2. Tabella si aggiorna automaticamente
3. Solo P0 violations visibili

### Filter by Status

**Scenario:** "Voglio vedere solo violations attive"

1. Dropdown **"Stato"** → Seleziona **"Attive"**
2. Violations risolte nascoste
3. Focus su cosa va ancora fixato

### Combine Filters

**Esempio:** "P0 violations attive nei miei file"

1. Priorità: **P0**
2. Stato: **Attive**
3. *(FASE 2: Filter by user)*

---

## ⚡ AI Fix - Best Practices

### When to Use AI Fix

✅ **Use AI when:**
- Violation è chiara ma non sai come fixare
- Hai bisogno di codice boilerplate (dependency injection, etc.)
- Vuoi vedere pattern corretto da replicare
- Prima volta che fissi questo tipo di violation

❌ **Don't use AI when:**
- Fix è ovvio (typo, import mancante)
- Hai già fixato violations simili
- Problema richiede context business logic

### How to Use AI Prompts

**Step by Step:**

1. **Copy prompt** dalla modal Padmin
2. **Open GitHub Copilot Chat** (Ctrl+Shift+I in VSCode)
3. **Paste entire prompt** (contiene già tutto il context)
4. **Review suggestion** - don't blindly apply!
5. **Apply fix** to your code
6. **Test** che funzioni
7. **Mark as Fixed** in Padmin

**Pro Tip:** AI prompt include:
- Context file e linea
- Regola violata
- Architettura FlorenceEGI
- Pattern OS3.0 da seguire

---

## 📊 Dashboard Metrics

### Total Violations
Numero totale violations trovate (tutte le scansioni)

**What to track:**
- Trend: In calo? ✅ Good!
- Spike improvvisi? Investigate!

### P0 Blocking Issues
Numero violations P0 (BLOCKING)

**Goal:** Zero P0 prima di deployment

**Action:** Se >0 → Fix SUBITO

### Symbols Indexed
*(FASE 2)* Numero simboli (classi, metodi) indicizzati

**What it means:** Più simboli = più context per AI

### System Health
Status generale scanner

- **100%:** ✅ Tutto OK
- **<100%:** ⚠️ Check errori parsing

---

## 🛠️ CLI Usage (Advanced)

### Basic Scan

```bash
php artisan padmin:scan --path=app/Http/Controllers
```

### Scan with Specific Rules

```bash
php artisan padmin:scan \
  --path=app/Services \
  --rules=REGOLA_ZERO,UEM_FIRST
```

### Scan and Store

```bash
php artisan padmin:scan \
  --path=app/Livewire/Collections \
  --store
```

### Full Repository Scan

```bash
php artisan padmin:scan --path=app --store
```

**Warning:** Full scan può richiedere 10-30 secondi per repo grandi.

---

## 💡 Tips & Tricks

### Tip 1: Scan Before Commit

```bash
# Git hook suggestion
git diff --name-only | grep ".php$" | xargs php artisan padmin:scan --path
```

### Tip 2: Focus on P0 First

Always fix P0 violations first. P1-P3 possono aspettare.

### Tip 3: Use AI for Boilerplate

Dependency injection, error handling → perfetti per AI Fix.

### Tip 4: Mark Fixed as You Go

Non accumulare violations. Fix → Mark → Next.

### Tip 5: Learn Patterns

Dopo 3-4 AI fixes dello stesso tipo, impari il pattern. Poi fai manuale più veloce.

---

## ❓ FAQ

### Q: Quante violations sono "normali"?

**A:** Dipende da size repo:
- Small project (<50 files): 0-10 violations OK
- Medium project (50-200 files): 10-50 violations acceptable
- Large project (>200 files): <100 violations good

**Goal:** Zero P0 violations sempre.

### Q: Quanto tempo ci vuole per fixare una violation?

**A:** Dipende da tipo:
- **P0 method inesistente:** 2-5 minuti (find correct method, replace)
- **P0 missing UEM:** 5-10 minuti (add dependency injection + handle call)
- **P0 GDPR compliance:** 10-20 minuti (add consent + audit)
- **P1-P3:** Varia molto

### Q: AI Fix genera sempre codice corretto?

**A:** No, AI è tool non oracle. Sempre:
1. Review codice generato
2. Test che funzioni
3. Verifica rispetta OS3.0
4. Adatta al tuo context

### Q: Posso ignorare una violation?

**A:** No per P0 (BLOCKING). Sì per P1-P3 se hai ragione valida. Documenta il perché.

### Q: Scan rallenta sviluppo?

**A:** No se usi strategicamente:
- Scan incrementale (solo file modificati)
- Scan pre-commit (catch early)
- Dashboard per monitoring trend

### Q: Come faccio training per team?

**A:** 
1. Mostra questa User Guide
2. Live demo: Run Scan → Fix → AI → Mark Fixed
3. Let them try su test file
4. Pair programming per prime violations

---

## 🆘 Troubleshooting

### Scan Non Trova Violations (ma dovrebbero esserci)

**Cause:**
1. Path errato → Check che file esista
2. Rules filter troppo restrittivo → Usa "Tutte"
3. File ha syntax error → Fix syntax prima

**Solution:**
```bash
# Verifica file PHP valido
php -l app/Http/Controllers/ProblematicFile.php

# Scan con tutte le regole
php artisan padmin:scan --path=app/Services
```

### Modal Non Si Apre

**Cause:**
1. JavaScript error → Check browser console (F12)
2. CSRF token mancante → Hard reload (Ctrl+Shift+R)

**Solution:**
1. F12 → Console → Check errori
2. Verifica `<meta name="csrf-token">` in `<head>`
3. Hard reload pagina

### AI Fix Genera Codice Non Valido

**Cause:**
1. Context insufficiente → AI non ha tutti i dettagli
2. Prompt ambiguo → Specifica meglio nel prompt

**Solution:**
1. Aggiungi context manualmente al prompt:
   ```
   Context: Working on FlorenceEGI User profile update.
   Need to use ConsentService and AuditLogService.
   ```
2. Rigenera con più context

### Session Violations Persi

**Cause:**
1. Logout → Session cleared
2. Session expired → Timeout

**Solution:**
1. Re-run scan (2-3 secondi)
2. *(FASE 2: Redis storage)* → No more session loss

---

## 📚 Learn More

### Documentation
- [README.md](./README.md) - Project overview
- [ARCHITECTURE.md](./ARCHITECTURE.md) - System design
- [ROADMAP.md](./ROADMAP.md) - Future plans

### OS3.0 Rules
- Read: `docs/ai/copilot-instructions.md`
- Sections: P0, P1, P2 rules

### FlorenceEGI Context
- [Brand Guidelines](../../marketing/FlorenceEGI_Brand_Guidelines.md)
- [PA Enterprise TODO](../../context/PA_ENTERPRISE_TODO_MASTER.md)

---

## 🎓 Training Checklist

**New Developer Onboarding:**

- [ ] Leggi questa User Guide (15 min)
- [ ] Watch demo video *(coming soon)*
- [ ] Run first scan on test file
- [ ] Fix 1 violation manualmente
- [ ] Fix 1 violation con AI
- [ ] Mark both as Fixed
- [ ] Explore dashboard metrics
- [ ] Setup pre-commit hook *(optional)*

**After 1 Week:**
- [ ] Fixed 10+ violations
- [ ] Comfortable with AI Fix workflow
- [ ] Know when to use manual vs AI
- [ ] Understand all 5 P0 rules

**After 1 Month:**
- [ ] Zero P0 violations in your code
- [ ] Mentor new developer
- [ ] Suggest improvements to Padmin

---

## 📞 Support

**Questions?**
- Check this guide first
- Ask in team Slack: `#padmin-support`
- Create issue in repo

**Bug Report:**
- Include: screenshot, steps to reproduce, expected vs actual
- Tag: `bug`, `padmin-analyzer`

**Feature Request:**
- Include: use case, why needed, proposed solution
- Tag: `enhancement`, `padmin-analyzer`

---

**Last Updated:** 23 Ottobre 2025  
**Version:** 1.0.0 (Fase 1 - Rule Engine)  
**For:** FlorenceEGI Developers  
**Maintained by:** Padmin D. Curtis (AI Partner OS3.0)

---

**Remember:** Padmin is your friend, not your enemy. It helps you write better code faster. Embrace it! 🚀
