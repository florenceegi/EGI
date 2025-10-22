# 🚨 REGOLE CRITICHE - DA RISPETTARE SEMPRE

## ⛔ DIVIETO ASSOLUTO GIT RESET

**REGOLA FONDAMENTALE**: È VIETATO usare comandi `git reset` (--soft, --hard, --mixed) senza esplicita approvazione dell'utente.

### Perché è vietato:

-   Il reset può causare **perdita irreversibile** di ore di lavoro
-   Può cancellare modifiche non committate
-   Può rompere la cronologia dei commit
-   Richiede recupero complesso dal reflog

### Cosa fare invece:

-   **SEMPRE** usare `git commit --amend` per correggere commit esistenti
-   **SEMPRE** chiedere approvazione prima di qualsiasi operazione di reset
-   **SEMPRE** verificare il reflog prima di operazioni distruttive

### Eccezioni:

-   Solo con **esplicita approvazione** dell'utente
-   Solo dopo aver spiegato esattamente cosa verrà perso
-   Solo come ultima risorsa

---

## 📝 REGOLE COMMIT

### Tag obbligatori:

-   `[FEAT]` - nuova feature o funzionalità
-   `[FIX]` - bug risolto
-   `[REFACTOR]` - refactoring del codice
-   `[DOC]` - documentazione aggiunta o aggiornata
-   `[TEST]` - aggiunta o modifica di test
-   `[CHORE]` - attività di manutenzione

### Formato obbligatorio:

```
[TAG] Descrizione breve e chiara

- Dettaglio 1 (cosa modificato)
- Dettaglio 2 (perché fatto)
- Dettaglio 3 (effetti/note)
- Max 4-5 punti
```

---

## 🛡️ REGOLE UEM/ULM

### Pattern obbligatorio:

-   **ULM**: `$this->logger->info/error/warning()`
-   **UEM**: `$this->errorManager->handle()`
-   **MAI**: `UEM::log()` (non esiste)

### Dependency injection obbligatoria:

```php
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use App\Services\Gdpr\AuditLogService;
```

---

## ⚠️ PENALITÀ PER VIOLAZIONI

-   **Reset non autorizzato**: ERRORE CRITICO - richiede recupero immediato
-   **Tag commit errati**: Correzione con amend obbligatoria
-   **Pattern UEM sbagliato**: Correzione immediata

---

**Data creazione**: 26 settembre 2025  
**Motivo**: Perdita lavoro per reset non autorizzato  
**Stato**: ATTIVO - DA RISPETTARE SEMPRE



