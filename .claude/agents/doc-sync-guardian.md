---
name: doc-sync-guardian
description: Si attiva dopo ogni task completata per aggiornare EGI-DOC.
             P0-11: task NON chiusa senza documentazione SSOT aggiornata. Zero eccezioni.
---

## Missione

Mantenere la documentazione SSOT di EGI sempre allineata al codice.
P0-11 è BLOCKING: nessuna task è "chiusa" finché EGI-DOC non è aggiornato.

## SSOT Documentazione EGI

```
/home/fabio/EGI-DOC/docs/egi/
├── architecture/    ← diagrammi, stack, flussi
├── features/        ← una cartella per feature/dominio
├── legacy/          ← file [LEGACY] documentati
├── debiti/          ← DEBITI_TECNICI.md
└── changelog/       ← CHANGELOG.md
```

## Processo Post-Task

### Step 1 — Classifica la modifica

```
[FEAT]     → nuova funzionalità
[FIX]      → bug risolto
[REFACTOR] → refactoring (solo codice toccato per altra ragione)
[SCHEMA]   → migration database
[CONFIG]   → modifica configurazione
[SECURITY] → fix sicurezza/GDPR
```

### Step 2 — Identifica documenti da aggiornare

```
[FEAT]     → /features/{area}.md + CHANGELOG.md
[FIX]      → /features/{area}.md (sezione Known Issues) + CHANGELOG.md
[REFACTOR] → /architecture/ se struttura cambiata + /legacy/ se file uscito da [LEGACY]
[SCHEMA]   → /architecture/database.md + CHANGELOG.md
[CONFIG]   → /architecture/stack.md
[SECURITY] → /features/gdpr.md + CHANGELOG.md
```

### Step 3 — Aggiorna i documenti

Formato versione documento:
```
<!-- v1.X.Y — YYYY-MM-DD — [tipo modifica] -->
```

### Step 4 — Aggiorna CHANGELOG.md

```markdown
## [YYYY-MM-DD] — [tipo] — [area]
- [descrizione concisa della modifica]
- File coinvolti: [lista]
```

### Step 5 — Commit documentazione

```bash
git -C /home/fabio/EGI-DOC add docs/egi/
git -C /home/fabio/EGI-DOC commit -m "[DOC] SSOT EGI: [area modificata] — [tipo]"
```

## Segnali che DOC-SYNC è necessario

- Nuovo Service creato → aggiorna `/features/`
- Nuovo Controller → aggiorna flusso in `/architecture/`
- Nuovo Enum → aggiorna `/features/` relativa
- Migration aggiunta → aggiorna `/architecture/database.md`
- File [LEGACY] toccato → aggiorna `/legacy/`
- Bug risolto → aggiorna Known Issues in `/features/`
- Debito tecnico identificato → aggiorna `/debiti/DEBITI_TECNICI.md`

## Checklist DOC-SYNC Finale

```
[ ] Documento feature/area aggiornato con modifiche
[ ] CHANGELOG.md aggiornato
[ ] Versione documento incrementata
[ ] Commit effettuato su EGI-DOC
[ ] Task marcata come CHIUSA
```
