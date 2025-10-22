# 🧠 OS3 Guardian / Padmin Analyzer

**Database Cognitivo Semantico per FlorenceEGI**

---

## 📋 Overview

**Padmin Analyzer** è il sistema cognitivo interno di FlorenceEGI che gestisce:

- 🔍 Indicizzazione semantica del codice sorgente
- ⚠️ Rilevamento violazioni OS3/ULTRA/GDPR
- 🔎 Ricerca vettoriale concettuale
- 📊 Statistiche e KPI di qualità del codice
- 🤖 Integrazione con AI Partner per analisi e fix automatici

---

## 🏗️ Architettura

```
tools/os3-guardian/
├── src/
│   ├── core/
│   │   └── db.ts           # PadminDB - Database cognitivo principale
│   ├── types/
│   │   └── index.ts        # TypeScript type definitions
│   └── index.ts            # Entry point
├── package.json
├── tsconfig.json
└── README.md
```

---

## 🚀 Quick Start

### Installazione

```bash
cd tools/os3-guardian
npm install
```

### Build

```bash
npm run build
```

### Sviluppo

```bash
npm run dev
```

---

## 📖 Usage

### Connessione al Database

```typescript
import { padminDB } from '@florenceegi/os3-guardian';

// Connetti a Redis Stack
await padminDB.connect();

// Verifica connessione
console.log(padminDB.isConnected()); // true
```

### Salvare un Simbolo

```typescript
await padminDB.saveSymbol('App\\Services\\ConsentService', {
    fqcn: 'App\\Services\\ConsentService',
    type: 'class',
    summary: 'Gestisce il consenso GDPR degli utenti',
    file: 'app/Services/Gdpr/ConsentService.php',
    line: 15,
    inputs: JSON.stringify(['User', 'string']),
    outputs: 'bool',
    deps: 'App\\Models\\User,App\\Models\\Consent',
    updated_at: Date.now()
});
```

### Recuperare un Simbolo

```typescript
const symbol = await padminDB.getSymbol('App\\Services\\ConsentService');

if (symbol) {
    console.log(symbol.summary);
    console.log(symbol.type);
}
```

### Registrare una Violazione

```typescript
await padminDB.recordViolation(
    'App\\Services\\StatisticsService',
    'STATISTICS',
    'P0',
    'Uso di ->take() senza parametrizzazione esplicita',
    {
        file: 'app/Services/StatisticsService.php',
        line: 42,
        fix_suggestion: 'Aggiungi parametro $limit al method signature'
    }
);
```

### Recuperare Violazioni Recenti

```typescript
const violations = await padminDB.getRecentViolations(50, 'P0');

violations.forEach(v => {
    console.log(`[${v.severity}] ${v.symbol}: ${v.message}`);
});
```

### Ricerca Semantica (Mock)

```typescript
// Genera embedding del tuo query (in produzione, usare OpenAI API)
const queryVector = JSON.stringify([0.1, 0.2, 0.3, /* ... 1536 dims */]);

const results = await padminDB.findSimilarSymbols(queryVector, {
    limit: 10,
    minSimilarity: 0.8,
    filters: {
        type: 'class',
        file: 'app/Services/Gdpr'
    }
});

results.forEach(r => {
    console.log(`${r.symbol.fqcn} (${r.similarity.toFixed(3)})`);
});
```

### Statistiche

```typescript
const stats = await padminDB.getStats();

console.log(`Total symbols: ${stats.totalSymbols}`);
console.log(`Total violations: ${stats.totalViolations}`);
console.log(`Classes: ${stats.symbolsByType.class}`);
console.log(`P0 violations: ${stats.violationsBySeverity.P0}`);
```

### Disconnessione

```typescript
await padminDB.disconnect();
```

---

## 🔧 Configurazione

Crea un file `.env` nella root del progetto:

```env
REDIS_HOST=localhost
REDIS_PORT=6379
REDIS_PASSWORD=
REDIS_DB=0
```

Oppure passa la configurazione al costruttore:

```typescript
import { PadminDB } from '@florenceegi/os3-guardian';

const db = new PadminDB({
    host: 'redis.example.com',
    port: 6380,
    password: 'secret',
    keyPrefix: 'padmin:'
});

await db.connect();
```

---

## 📊 Schema Redis

### Simboli

- **Key**: `symbol:{fqcn}`
- **Type**: Hash
- **TTL**: 24 ore
- **Fields**: `fqcn`, `type`, `summary`, `file`, `line`, `inputs`, `outputs`, `deps`, `vector`, `hash`, `updated_at`, `metadata`

### Violazioni

- **Key**: `violation:{timestamp}:{symbol}`
- **Type**: Hash
- **TTL**: 7 giorni
- **Fields**: `symbol`, `rule`, `severity`, `message`, `file`, `line`, `timestamp`, `status`, `fix_suggestion`

### Indici

- `symbols:all` (sorted set, ordinato per timestamp)
- `symbols:by_type:{type}` (set)
- `violations:recent` (sorted set, ordinato per timestamp)
- `violations:by_severity:{P0|P1|P2|P3}` (set)
- `violations:by_rule:{rule}` (set)
- `queue:update` (list, FIFO)

---

## ⚙️ API Reference

### `PadminDB`

#### Constructor

```typescript
new PadminDB(config?: Partial<RedisConfig>)
```

#### Methods

- `async connect(): Promise<void>` - Connessione a Redis
- `async disconnect(): Promise<void>` - Disconnessione da Redis
- `isConnected(): boolean` - Verifica stato connessione
- `async saveSymbol(fqcn: string, data: SymbolData): Promise<void>` - Salva simbolo
- `async getSymbol(fqcn: string): Promise<SymbolData | null>` - Recupera simbolo
- `async deleteSymbol(fqcn: string): Promise<boolean>` - Elimina simbolo
- `async findSimilarSymbols(vector: string, options?: SearchOptions): Promise<SearchResult[]>` - Ricerca vettoriale
- `async recordViolation(symbol, rule, severity, message, metadata?): Promise<void>` - Registra violazione
- `async getRecentViolations(limit?: number, severity?: string): Promise<Violation[]>` - Recupera violazioni
- `async queueUpdate(symbol: string): Promise<void>` - Accoda aggiornamento
- `async dequeueUpdate(): Promise<string | null>` - Recupera da coda
- `async getStats(): Promise<DBStats>` - Statistiche database
- `async flushAll(): Promise<void>` - ⚠️ Flush completo database

---

## 🧪 Testing

```bash
npm test
```

---

## 🔒 Sicurezza

- ✅ Validazione input su ogni operazione
- ✅ Try/catch e error handling UEM-style
- ✅ Logging ULM-style per audit trail
- ✅ TTL automatico su dati sensibili
- ✅ Sanitizzazione config per logging

---

## 📝 Logging

Tutti i log seguono il pattern ULM (UltraLogManager):

```json
{
    "timestamp": "2025-10-22T10:30:00.000Z",
    "level": "INFO",
    "service": "PadminDB",
    "message": "Symbol saved successfully",
    "fqcn": "App\\Services\\ConsentService",
    "key": "symbol:App\\Services\\ConsentService"
}
```

Livelli: `INFO`, `WARN`, `ERROR`, `DEBUG`

---

## 🚧 Roadmap

### Fase 1 - MVP (Completata ✅)

- [x] Modulo `db.ts` con Redis Stack
- [x] Salvataggio e recupero simboli
- [x] Registrazione violazioni
- [x] Ricerca vettoriale (mock)
- [x] Statistiche database

### Fase 2 - Backend Laravel

- [ ] `PadminService` in Laravel
- [ ] Route `/admin/padmin/*`
- [ ] Middleware `EnsurePadminSuperadmin`
- [ ] Integration con ULM/UEM/GDPR

### Fase 3 - Dashboard

- [ ] React + Inertia UI
- [ ] KPI Cards
- [ ] Violations Table
- [ ] Semantic Search
- [ ] Graph Map

### Fase 4 - Advanced

- [ ] Migrazione a Postgres + pgvector
- [ ] RediSearch integration
- [ ] OpenAI embeddings reali
- [ ] Auto-patch generation
- [ ] CI/CD integration

---

## 👥 Team

**Autore**: Padmin D. Curtis (AI Partner OS3.0)  
**Supervisor**: Fabio Cherici (Superadmin FlorenceEGI)  
**Framework**: FlorenceEGI OS3 Infrastructure Workflow

---

## 📜 License

UNLICENSED - FlorenceEGI Internal Use Only

---

## 📞 Support

Per domande o supporto, contattare il Superadmin FlorenceEGI.

---

**Documento OS3.0 - Build Date: 2025-10-22**
