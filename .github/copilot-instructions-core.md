# AI Agent Instructions — Core (OS3.0)

> "L'AI non pensa. Predice. Non deduce logicamente. Completa statisticamente."

## 🛑 P0 — BLOCCANTI

| #    | Regola                  | Cosa Fare                                          |
|------|-------------------------|----------------------------------------------------|
| P0-1 | REGOLA ZERO             | MAI dedurre → 🛑 CHIEDI                            |
| P0-2 | No Hardcoded            | Chiavi traduzione, mai testo letterale nel codice  |
| P0-3 | No Limiti Impliciti     | No `take(10)` nascosti, sempre params espliciti    |
| P0-4 | Anti-Method-Invention   | Verifica che il metodo esista PRIMA di usarlo      |
| P0-5 | Error Handling First    | Errori → gestore centralizzato, mai solo log       |
| P0-6 | Read Before Use         | grep/read file prima di usare qualsiasi service    |
| P0-7 | Anti-Enum-Constant      | Verifica costanti/enum esistano prima di usarle    |
| P0-8 | Complete Flow Analysis  | Mappa INTERO flow PRIMA di qualsiasi fix           |
| P0-9 | i18n Completo           | Traduzioni in TUTTE le lingue del progetto         |

### 🔍 Prima di Ogni Risposta
```
1. Ho TUTTE le info? → NO = 🛑 CHIEDI
2. Metodi VERIFICATI? → NO = 🛑 leggi sorgente
3. Sto ASSUMENDO? → SÌ = 🛑 DICHIARA e CHIEDI
4. Limiti impliciti? → SÌ = 🛑 RENDI ESPLICITO
```

### 🔄 Prima di FIX/DEBUG (P0-8)
```
1. Flow MAPPATO? → NO = 🛑 MAP FIRST
2. Types TRACCIATI? → NO = 🛑 TRACE FIRST
3. ALL occurrences TROVATE? → NO = 🛑 FIND ALL
4. Context VERIFICATO? → NO = 🛑 VERIFY
TEMPO: 15-35 min | RISPARMIO: 2+ ore debugging
```

## ♿ A11Y — Incrementale

Ogni fix su una pagina = occasione per migliorare A11Y.

```
P1-MUST:
  <label for="id"> su OGNI input | alt="..." su OGNI <img>

P2-SHOULD:
  HTML semantico: <main> <nav> <header> <section> <aside>
  aria-label su icon-only buttons e <nav> multipli
  aria-hidden="true" su SVG decorativi
  focus:ring-2 su tutti i controlli interattivi
  <span class="sr-only"> per testo screen reader
  aria-live="polite/assertive" per notifiche dinamiche
  aria-invalid="true" + aria-describedby su campi con errore

Quick check:
  grep "<button[^>]*><svg" file        # button senza aria-label
  grep '<img[^>]*>' file | grep -v alt=  # img senza alt
```

Target: **WCAG 2.1 Level AA** — A11Y è P2, non blocca deploy.

## ⚠️ Dynamic Translation Caching

Traduzioni con parametri dinamici rischiano di essere cachate con il primo valore e servite a tutti.

❌ `__('key', ['name' => $val])` → cacheato con primo utente
✅ `__('key.prefix') . $val . __('key.suffix')` → solo parti statiche cachate

## ⚡ Priorità

| P  | Nome      | Conseguenza          |
|----|-----------|----------------------|
| P0 | BLOCKING  | 🛑 STOP totale       |
| P1 | MUST      | Non production-ready |
| P2 | SHOULD    | Debito tecnico       |
| P3 | REFERENCE | Info only            |

## 📝 TAG System — `[TAG] Descrizione`

```
FEAT(1.0)  FIX(1.5)     REFACTOR(2.0)  TEST(1.2)
DEBUG(1.3) DOC(0.8)     CONFIG(0.7)    CHORE(0.6)
I18N(0.7)  PERF(1.4)    SECURITY(1.8)  WIP(0.3)
REVERT(0.5) MERGE(0.4)  DEPLOY(0.8)    UPDATE(0.6)
```

## 🔒 Git Hooks

| Regola | Trigger                   | Azione     |
|--------|---------------------------|------------|
| R1     | >100 righe rimosse/file   | 🛑 BLOCCA  |
| R2     | 50-100 righe rimosse      | ⚠️ WARNING |
| R3     | >50% contenuto rimosso    | 🛑 BLOCCA  |
| R4     | >500 righe totali rimosse | 🛑 BLOCCA  |

Bypass: `git commit --no-verify` (solo se intenzionale)

---
**OS3.0 — "Less talk, more code. Ship it."**
