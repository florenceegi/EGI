# /new-feature — Progettazione Feature Completa (EGI)

Protocollo per feature che toccano più layer o più file.
Usa Plan Mode (Shift+Tab x2) durante questa sessione.

## Fase 1 — Feature brief

Chiedi (tutto in una volta):

1. **Nome feature**: Come si chiama? (es. "Batch Minting ASA", "COA Export PDF multilingue")
2. **Problema che risolve**: Qual è il pain point?
3. **Layer coinvolti**: Laravel / React+TS / Algorand / Python AI / tutti
4. **Utente target**: Creator / Collector / Inspector / Admin / Sistema interno
5. **Vincoli**: GDPR? MiCA-SAFE? Multi-tenancy? Algorand testnet/mainnet?
6. **Priority**: P0/P1/P2 secondo standard OS3?

## Fase 2 — Spec tecnica

Dopo brief, genera spec strutturata:

```markdown
## SPEC: [Nome Feature]
**Priority**: P[n]
**Branch target**: [branch]
**Stima**: [n file, n righe]

### Comportamento atteso
[Descrizione precisa input → processo → output]

### File da creare
- [ ] `path/file.php` — [scopo] — Agente: [laravel-specialist]
- [ ] `path/file.tsx` — [scopo] — Agente: [frontend-ts-specialist]
- [ ] `path/file.php` — [scopo blockchain] — Agente: [blockchain-specialist]

### File da modificare
- [ ] `path/file.php` riga ~[n] — [modifica]

### File [LEGACY] coinvolti — piano approvazione richiesta
- [file LEGACY] — [motivo] — [piano minimo]

### File da NON toccare (senza piano approvato)
[lista file legacy > 500 LOC dal CLAUDE.md]

### Schema dati (se applicabile)
[Nuove colonne / tabelle / migration]
[Schema: core. o natan. ?]

### Blockchain (se applicabile)
[ASA minting? Certificate anchoring? Smart contract?]
[Testnet only — mai mainnet senza approvazione Fabio]

### API contract (se applicabile)
Request: { ... }
Response: { ... }

### GDPR (se dati personali coinvolti)
[GdprActivityCategory da usare]
[Audit trail richiesto]

### i18n (P0-9)
Stringhe nuove → tutte e 6 le lingue: it, en, de, es, fr, pt

### Test manuale
1. [Step 1]
2. [Step 2]
3. [Risultato atteso]

### DOC-SYNC richiesto
- [ ] `EGI-DOC/docs/egi/features/[area].md`
- [ ] `EGI-DOC/docs/egi/changelog/CHANGELOG.md`
```

## Fase 3 — Approval e suddivisione in task

Dopo approvazione della spec:

```
TASK BREAKDOWN: [Nome Feature]

TASK 1: [titolo] — Agent: [laravel-specialist / frontend-ts-specialist / blockchain-specialist / python-rag-specialist]
TASK 2: [titolo] — Agent: [...]
TASK N: DOC-SYNC — Agent: [doc-sync-guardian]

Procedo con Task 1? (sì/modifica)
```

## Fase 4 — Implementazione task-by-task

Per ogni task:
- Un file per volta
- Verifica grep preventivo (P0-4, P0-6)
- Firma OS3.0 obbligatoria
- Max 500 righe per file nuovo
- GDPR audit se dati personali (P0-EGI-2)
- TenantScope se tenant-specific (P0-EGI-3)

## Fase 5 — Integration test

Prima del merge:

```
INTEGRATION CHECKLIST:
□ Layer Laravel: php artisan config:cache OK
□ Layer React: npm run build 0 errors TypeScript
□ Layer Blockchain: AlgoKit microservice risponde :3000
□ Flusso end-to-end: [query/azione test specifica]
□ Multi-tenant: test con tenant_id diversi
□ i18n: tutte e 6 le lingue aggiornate
□ GDPR: audit trail su nuove operazioni dati personali
□ MiCA-SAFE: NO wallet custody introdotta
```

## Fase 6 — Chiusura e DOC-SYNC

```
FEATURE COMPLETATA: [Nome Feature]
Branch: [branch]
Commit: [hash o descrizione]

Attiva /doc-sync-guardian per aggiornare EGI-DOC/docs/egi/.
```
