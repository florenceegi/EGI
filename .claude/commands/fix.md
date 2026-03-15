# /fix — Debug e Fix Strutturato (P0-8) — EGI

Protocollo obbligatorio per qualsiasi fix. Non saltare nessuna fase.

## Fase 1 — Descrizione del problema

Chiedi (tutto in una volta):

1. **Sintomo**: Cosa succede esattamente? (messaggio di errore, comportamento errato)
2. **Quando**: Quando si manifesta? (sempre / a volte / dopo X azione)
3. **Layer**: Dove appare l'errore? (browser / Laravel logs / Algorand / Python AI logs / DB)
4. **Log**: Incollami il log esatto se disponibile
5. **Branch**: Su quale branch?
6. **Ultimo cambiamento**: Cosa è stato modificato di recente?

## Fase 2 — Mappatura flusso completo (P0-8 — 15-35 min)

**Non skippare questa fase. Mai.**

```
FLOW MAP:
User action: [cosa fa l'utente]
    ↓
Frontend: [componente React/TSX / Blade / Livewire / Vanilla TS]
    ↓
API call: [endpoint routes/api.php o routes/web.php]
    ↓
Laravel: [Controller → Service → Model → ...]
    ↓
[Blockchain]: [AlgorandService → AlgoKit :3000 → Algorand testnet]
[AI Layer]:   [AnthropicService / NatanChatService → FastAPI :8001]
    ↓
Database: [query PostgreSQL / schema core o natan / search_path]
    ↓
Response: [formato atteso]
```

Leggi ogni file nel flow. Traccia il tipo di ogni variabile in ogni step.

## Fase 3 — Identificazione causa root

Dopo la mappatura:

```
CAUSA ROOT PROBABILE: [descrizione]
FILE COINVOLTO: [path]
RIGA APPROSSIMATIVA: [numero]
CONFIDENCE: [alta/media/bassa]

FILE [LEGACY]? [sì/no — se sì dichiara e proponi piano minimale]

CAUSE ALTERNATIVE DA ESCLUDERE:
- [causa alternativa 1]
- [causa alternativa 2]
```

Se confidence < alta → cerca tutte le occorrenze con grep prima di procedere.

## Fase 4 — Fix minimale

Il fix deve essere il più chirurgico possibile:
- Tocca solo i file strettamente necessari
- Non introduce nuove dipendenze
- Non cambia comportamento di feature non coinvolte
- Rispetta tutti i valori immutabili
- MAI bypass MiCA-SAFE o GDPR per "comodità"

## Fase 5 — Verifica

```bash
# Se Laravel
php artisan config:cache
php artisan view:clear

# Se Migration
php artisan migrate:status

# Se Frontend React
npm run build

# Se Algorand
# Verifica via SSM — NON direttamente
# Controlla EgiBlockchain records in DB

# Se AI layer
# Controlla AnthropicService/NatanChatService logs
```

## Fase 6 — Chiusura

```
FIX COMPLETATO: [titolo]

CAUSA ROOT: [descrizione]
FILE MODIFICATI: [lista]
TIPO FIX: [one-liner / patch / refactor]
FILE [LEGACY] TOCCATI: [se presenti — con dichiarazione esplicita]

COMMIT: [FIX] [descrizione causa + soluzione]

REGRESSIONI DA VERIFICARE:
- [area 1 potenzialmente impattata]
- [area 2]

DOC-SYNC: [sì/no — quale debito tecnico aggiornare in EGI-DOC/docs/egi/]
```
