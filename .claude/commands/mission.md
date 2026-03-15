# /mission — Avvia Missione Strutturata (EGI)

Quando ricevi questo comando, applica il seguente protocollo prima di fare qualsiasi altra cosa.

## Fase 1 — Raccolta requisiti

Fai queste domande (tutte in una volta, non una alla volta):

1. **Cosa**: Descrivi il comportamento atteso del codice finale in una o due frasi
2. **Layer**: Quale layer è coinvolto? [Laravel / React+TS / Vanilla TS / Algorand / Python AI / tutti]
3. **File di riferimento**: Esiste un file simile che posso usare come pattern?
4. **Branch**: Su quale branch lavoriamo?
5. **Vincoli speciali**: GDPR? MiCA-SAFE? Multi-tenancy? Blockchain?

## Fase 2 — Analisi (Plan Mode automatico)

Dopo aver ricevuto le risposte:

1. Leggi i file rilevanti con Read tool
2. Esegui grep per verificare metodi e pattern esistenti (P0-4, P0-6)
3. Verifica enum constants (P0-7)
4. Mappa il flow completo: entry point → ogni step → output finale
5. Identifica tutti i file da toccare
6. Identifica i rischi (file [LEGACY] >500 righe, valori immutabili, blockchain, cross-layer)

## Fase 3 — Piano di implementazione

Prima di scrivere una riga di codice, presenta:

```
PIANO MISSIONE: [titolo]
Branch: [branch]
Layer: [layer/i]

FILE COINVOLTI:
- [file 1] — [tipo modifica: create/modify/read-only]
- [file 2] — [tipo modifica]

FILE [LEGACY] COINVOLTI (se presenti):
- [file] — [LOC] — motivo del tocco

SEQUENCE:
1. [step 1]
2. [step 2]
3. [step 3]

RISCHI:
- [rischio blockchain / GDPR / MiCA / tenant isolation]

AGENTE: [laravel-specialist / frontend-ts-specialist / blockchain-specialist / python-rag-specialist]
DOC-SYNC richiesto: [sì/no] — [quale .md aggiornare in EGI-DOC/docs/egi/]

Procedo? (sì/modifica piano)
```

## Fase 4 — Esecuzione

Solo dopo approvazione esplicita:
- Un file per volta
- Firma OS3.0 su ogni file
- Max 500 righe per file nuovo
- npm run build se tocca React/TS frontend
- GDPR audit su ogni operazione con dati personali (P0-EGI-2)
- TenantScope su ogni query tenant-specific (P0-EGI-3)

## Fase 5 — Chiusura

```
MISSIONE COMPLETATA: [titolo]

FILE CREATI/MODIFICATI:
- [lista]

NEXT STEPS:
- [ ] npm run build (se frontend React/TS)
- [ ] php artisan config:cache (se config Laravel)
- [ ] php artisan migrate (se migration)
- [ ] Verifica Algorand testnet (se blockchain)
- [ ] DOC-SYNC: aggiornare EGI-DOC/docs/egi/[file.md]
- [ ] Commit: [TAG] [descrizione]
```
