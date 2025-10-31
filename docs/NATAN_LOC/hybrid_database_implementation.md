# HYBRID DATABASE IMPLEMENTATION (MariaDB + MongoDB)

**Project:** N.A.T.A.N. / FlorenceEGI — OS3 Integrated  
**Version:** 1.0.0  
**Author:** Padmin D. Curtis (CTO OS3)  
**Date:** 2025‑10‑30

---

## 1) Executive Summary

Obiettivo: definire un’architettura **ibrida** che combini **MariaDB** (layer transazionale, relazionale, tenant‑scoped) con **MongoDB** (layer cognitivo/documentale condiviso) per supportare **N.A.T.A.N. Loc** e i moduli PA/Enterprise.  

**Principi guida OS3:**
- **Isolamento forte per tenant** (security by default)  
- **Configurazione stratificata**: `.env` centrale + override per tenant  
- **Repository anti‑lock‑in**: interfacce + adapter Maria/Mongo  
- **GDPR + ULM + UEM** integrati in ogni punto di mutazione dati

**In una frase:** MariaDB custodisce le **relazioni e i permessi**, MongoDB custodisce **embeddings, log AI e contenuti documentali** ad alto volume — entrambi legati da `tenant_id` e orchestrati via middleware di tenancy.

---

## 2) Scope

**Incluso:**
- Pattern multi‑tenant (single DB con `tenant_id` per MariaDB; namespace logico in Mongo).
- Modelli dati chiave (PA atti, embeddings, chat, analytics).
- Configurazione `.env` e `config/database.php`.
- Repository + Services (RAG, Chat, Scraping).
- Sicurezza/Compliance (GDPR, Audit, ULM/UEM).
- Rollout, migrazioni, test, monitoring.

**Escluso:** provisioning infra (Docker/K8s), backup/DR runbook dettagliato (rimando a documento infra dedicato).

---

## 3) Glossario rapido

- **Tenant:** PA/azienda/area pubblica (ART) con isolamento dei dati.
- **N.A.T.A.N. Loc:** istanza locale/cognitiva che usa Mongo centrale per embeddings.
- **Embedding:** rappresentazione vettoriale di testo per RAG/ricerca semantica.

---

## 4) Architettura logica

```
Request → InitializeTenancyMiddleware → set tenant (from subdomain/user/API)
        ├─ Layer transazionale: MariaDB (relazionale, tenant_id scope)
        └─ Layer cognitivo: MongoDB (centrale, collections con tenant_id)
                           ├─ pa_act_embeddings
                           ├─ natan_chat_messages
                           └─ ai_logs / analytics
Response ← Aggregazione servizi (RAG/Chat/Dashboard) ← Policy + Audit
```

**Tenancy Model**
- **MariaDB**: strategia *single‑database* con colonna `tenant_id` e Global Scope (via middleware).
- **MongoDB**: **namespace logico**; ogni documento contiene `tenant_id` (obbligatorio) + indici composti.

---

## 5) Modelli dati (essenziali)

### 5.1 MariaDB (relazionale)

- `users` (`id`, `tenant_id`, …)
- `pa_entities` (alias *tenants*)
- `pa_acts` (`id`, `tenant_id`, `protocol_number`, `title`, `doc_type`, `hash`, …)
- `user_conversations` (`id`, `tenant_id`, `type`, …)
- `permissions` / `roles` (Spatie) con `tenant_id` quando pertinente

**Regole:** tutte le tabelle con dati di dominio includono `tenant_id` (FK → `pa_entities.id`) e indice su `tenant_id`.

### 5.2 MongoDB (documentale/cognitivo)

- Collection `pa_act_embeddings`
  - `tenant_id` (Number/String) — **obbligatorio**
  - `act_id` (Number, FK logica a MariaDB)
  - `embedding` (Array<Float>)
  - `metadata` (Object: titolo, tipo, hash, date)
  - `created_at` (Date)

- Collection `natan_chat_messages`
  - `tenant_id`, `user_id`, `conversation_id`, `role` (`user`/`assistant`), `content`, `tokens`, `latency_ms`, `sources`

- Collection `ai_logs` / `analytics`
  - `tenant_id`, `event`, `context`, `ts`

**Indici consigliati:**
```js
// pa_act_embeddings
{ tenant_id: 1, act_id: 1 }
{ tenant_id: 1, "metadata.doc_type": 1 }
// Vector index se disponibile (Atlas Vector / plugin):
// { embedding: "vector", dims: 1536, similarity: "cosine" }
```

> Nota: se l’ambiente non offre un indice vettoriale nativo, si usa KNN app‑level con cosine similarity + pruning.

---

## 6) Configurazione

### 6.1 `.env` (centrale)

```dotenv
# === MariaDB (transazionale)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=egi_main
DB_USERNAME=egi_user
DB_PASSWORD=********

# === MongoDB (cognitivo)
MONGO_DB_CONNECTION=mongodb
MONGO_DB_HOST=127.0.0.1
MONGO_DB_PORT=27017
MONGO_DB_DATABASE=natan_ai_core
MONGO_DB_USERNAME=natan_user
MONGO_DB_PASSWORD=********
```

### 6.2 `config/database.php`

```php
'mongodb' => [
    'driver'   => 'mongodb',
    'host'     => env('MONGO_DB_HOST'),
    'port'     => env('MONGO_DB_PORT'),
    'database' => env('MONGO_DB_DATABASE'),
    'username' => env('MONGO_DB_USERNAME'),
    'password' => env('MONGO_DB_PASSWORD'),
    'options'  => ['database' => 'admin'],
],
```

### 6.3 Override per tenant

- Chiavi override (per tenant) persistite in `tenant_settings` (MariaDB) o `pa_entities.settings` (JSON).
- Il middleware di tenancy applica `config([key => value])` a inizio richiesta.

---

## 7) Pattern di accesso dati

### 7.1 Repository Interfaces (anti‑lock‑in)

```php
interface PaActRepositoryInterface {
    public function findByIds(array $ids, int $tenantId): Collection;
    public function search(array $filters, int $tenantId, ?int $limit = null): Collection; // STATISTICS RULE
}

interface EmbeddingRepositoryInterface {
    public function upsertEmbedding(int $tenantId, int $actId, array $embedding, array $metadata = []): void;
    public function knn(int $tenantId, array $queryVector, int $k = 10): array; // returns [ [act_id, score], ... ]
}
```

### 7.2 Implementazioni

**MariaDBPaActRepository** (Eloquent + Global Scope `tenant_id`)
```php
class MariaDBPaActRepository implements PaActRepositoryInterface {
    public function findByIds(array $ids, int $tenantId): Collection {
        return PaAct::where('tenant_id', $tenantId)->whereIn('id', $ids)->get();
    }
}
```

**MongoEmbeddingRepository** (Jenssegers MongoDB)
```php
class MongoEmbeddingRepository implements EmbeddingRepositoryInterface {
    public function upsertEmbedding(int $tenantId, int $actId, array $embedding, array $metadata = []): void {
        PaActEmbedding::updateOrCreate(
            ['tenant_id' => $tenantId, 'act_id' => $actId],
            ['embedding' => $embedding, 'metadata' => $metadata, 'created_at' => now()]
        );
    }

    public function knn(int $tenantId, array $q, int $k = 10): array {
        // Fallback app-level KNN (se non c’è vector index nativo)
        return PaActEmbedding::where('tenant_id', $tenantId)
            ->get(['act_id', 'embedding'])
            ->map(fn($doc) => [
                'act_id' => $doc->act_id,
                'score'  => self::cosine($q, $doc->embedding),
            ])
            ->sortByDesc('score')
            ->take($k)
            ->values()
            ->all();
    }

    private static function cosine(array $a, array $b): float {
        $dot = 0; $na = 0; $nb = 0; $n = min(count($a), count($b));
        for ($i=0; $i<$n; $i++) { $dot += $a[$i]*$b[$i]; $na += $a[$i]**2; $nb += $b[$i]**2; }
        return $dot / (sqrt($na) * sqrt($nb) ?: 1e-9);
    }
}
```

### 7.3 Service RAG (ibrido)

```php
class RagService {
    public function __construct(
        private EmbeddingRepositoryInterface $emb,
        private PaActRepositoryInterface $acts,
        private EmbeddingGenerator $gen,
        private AuditLogService $audit,
        private UltraLogManager $ulm,
        private ErrorManagerInterface $errorManager,
    ) {}

    /** @return Collection<PaAct> */
    public function findRelevant(string $query, User $user, ?int $limit = 10): Collection {
        $this->ulm->info('RAG_START', ['user_id' => $user->id, 'tenant_id' => $user->tenant_id]);
        try {
            $vec     = $this->gen->forQuery($query);
            $topK    = $this->emb->knn($user->tenant_id, $vec, $limit ?? 10);
            $actIds  = array_column($topK, 'act_id');
            $results = $this->acts->findByIds($actIds, $user->tenant_id);

            $this->audit->logUserAction(
                $user,
                'rag.search',
                ['query' => $query, 'k' => count($actIds)],
                GdprActivityCategory::DATA_ACCESS
            );

            $this->ulm->info('RAG_OK', ['count' => $results->count()]);
            return $results;
        } catch (\Throwable $e) {
            return $this->errorManager->handle('RAG_SEARCH_FAILED', [
                'user_id' => $user->id,
                'tenant_id' => $user->tenant_id,
                'query_len' => strlen($query),
            ], $e);
        }
    }
}
```

> **OS3 Notes:** STATISTICS RULE rispettata (limite esplicito), ULM/UEM/GDPR integrati, niente testi hardcoded (chiavi di traduzione lato controller/UI).

---

## 8) Pipeline Embeddings

1. **Ingestione**: parsing PDF/HTML → estrazione testo → chunking → generazione embedding.
2. **Persistenza**: `EmbeddingRepository::upsertEmbedding(tenant_id, act_id, embedding, metadata)` → Mongo.
3. **Ricerca**: query → embedding → `knn(tenant_id, vec, k)` → `PaActRepository::findByIds` → merge metadati.
4. **Verifica**: hash documento in MariaDB comparato con metadati Mongo.

**Indice consigliato in Mongo:** `{ tenant_id: 1, act_id: 1 }` per join logica rapida; opzionale vector index.

---

## 9) Sicurezza & Compliance (OS3)

- **Isolation First**: `tenant_id` obbligatorio in OGNI scrittura/lettura Mongo e Maria.
- **Policy di accesso**: policy/controller verificano `user->tenant_id` prima di eseguire.
- **UEM First**: *mai* sostituire gestione errori strutturata con log generici.
- **GDPR**: AuditLogService registra azioni su dati (DATA_ACCESS, CONTENT_CREATION, etc.).
- **Sanitizzazione**: DataSanitizerService per rimuovere PII verso provider AI esterni.
- **Cifratura**: campi sensibili (token API per tenant) cifrati a riposo nel DB relazionale.

---

## 10) Migrazioni & Seeders

**MariaDB**
- Migrazione `add_tenant_id_*` per ogni tabella di dominio.
- Seeder `MigrateToMultiTenantSeeder` per retro‑compatibilità e assegnazione `tenant_id`.

**Mongo**
- Script inizializzazione indici:
```js
db.pa_act_embeddings.createIndex({ tenant_id: 1, act_id: 1 }, { unique: true });
db.pa_act_embeddings.createIndex({ tenant_id: 1, "metadata.doc_type": 1 });
// opzionale: vector index se supportato
```

---

## 11) Test (Feature & Integration)

- **TenantIsolationTest**: un utente non vede atti di tenant diverso (MariaDB + knn Mongo).
- **RAGAccuracyTest**: dato un set di atti, la ricerca semantica restituisce l’atto corretto in Top‑K.
- **GDPRAuditTest**: ogni ricerca genera audit log con categoria corretta.

Esempio (pseudo‑PHPUnit):
```php
public function test_user_sees_only_own_tenant_acts_in_rag() {
    $tenant1 = Tenant::factory()->create();
    $tenant2 = Tenant::factory()->create();
    $user1   = User::factory()->create(['tenant_id' => $tenant1->id]);

    // Seed atti + embeddings per ciascun tenant
    // ...

    $results = app(RagService::class)->findRelevant('mobilità', $user1, 5);
    $this->assertTrue($results->every(fn($a) => $a->tenant_id === $tenant1->id));
}
```

---

## 12) Monitoring & Observability

- **ULM tracing**: `RAG_START`, `RAG_OK`, `RAG_SEARCH_FAILED`, `EMBED_UPSERT_OK/FAIL`.
- **Metriche**: latency media risposta chat, throughput KNN, dimensione media embedding per tenant.
- **Alerting UEM**: errori critici → Slack/email; log differenziati per tenant.

---

## 13) Rollout Plan

1. **Week 1** — Aggiunta `tenant_id` su tabelle mancanti, seeding di retro‑compatibilità.
2. **Week 2** — Introduzione Mongo (connessione, collections, indici), Repository + Service RAG.
3. **Week 3** — Spostamento embeddings da MariaDB → Mongo (script di migrazione).
4. **Week 4** — Hardening sicurezza, test end‑to‑end, monitoring e dashboard KPI.

Rollback sicuro: mantenere doppia scrittura (Maria+Mongo) per due rilasci e flag di feature toggle.

---

## 14) Troubleshooting (rapido)

- **RAG lentezza**: mancano indici Mongo o embeddings non generati → rigenerare + creare indici.
- **Mismatch atti**: `act_id` errato in Mongo → verificare seeder/mapping.
- **Leak cross‑tenant**: assenza `tenant_id` nel filtro Mongo → aggiungere guardia a livello repository e test dedicato.
- **500 AI Provider**: UEM con `RAG_SEARCH_FAILED`; fallback a keyword search + log dev.

---

## 15) Checklist OS3 di conformità

- [ ] `tenant_id` presente e indicizzato ovunque (Maria + Mongo)
- [ ] Nessun limite nascosto (STATISTICS RULE)
- [ ] ULM log start/success/error su servizi core
- [ ] UEM error handling con codici e messaggi tradotti
- [ ] Audit GDPR su azioni di accesso/creazione
- [ ] Nessun testo hardcoded (UI/controller → translation keys)
- [ ] Test di isolamento inter‑DB (Maria↔Mongo)
- [ ] Script indici Mongo eseguito
- [ ] Rollout con feature toggle e rollback piano definito

---

## 16) Appendix A — Error Codes (UEM)

Esempi (da definire in `config/error-manager.php` + lang vendor):

- `RAG_SEARCH_FAILED` → type:`error`, blocking:`semi-blocking`, msg_to:`toast`
- `EMBED_UPSERT_FAILED` → type:`error`, blocking:`not`, msg_to:`toast`
- `TENANT_CONTEXT_MISSING` → type:`critical`, blocking:`blocking`, msg_to:`multiple`

---

## 17) Appendix B — Security Notes

- Validare sempre `tenant_id` lato server (mai fidarsi dell’input client).
- Rate limiting per endpoint RAG/Chat.
- Separare ruoli: *super‑admin* con `tenant switcher` controllato e auditato.
- Non serializzare embeddings nei log; usare hash/truncate.

---

**Fine documento — versione 1.0.0**

