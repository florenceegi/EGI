# /docs/architecture/PA_AI_MULTIMODEL_GATEWAY_IMPLEMENTATION.md

> **OS3-Doc / Architecture Spec**  
> **Title:** N.A.T.A.N. – PA AI **Multimodel Gateway** (AI‑agnostic)  
> **Version:** 1.0.0  
> **Author:** Padmin D. Curtis (OS3)  
> **Status:** ✅ Approved for implementation  
> **Compliance:** OS3 P0–P1 (Regola Zero, ULM/UEM/GDPR), Security‑by‑Default  

---

## 1) PURPOSE
Implementare un **gateway AI‑agnostico** che consenta a N.A.T.A.N. di orchestrare più LLM/embeddings provider (Claude, OpenAI, Llama/Ollama, AISIRU, ecc.) in modalità **plug‑in**, con **policy di selezione dinamica** per persona/tenant/task, **circuit‑breaker**, **observability** ULM/UEM, e **GDPR** by‑design.

**Goals**
- Swap/route dei provider senza cambiare consumer code.
- Fallback e retry per resilienza.
- Policy per **persona**, **tenant**, **regione** e **classe di task** (RAG, summarization, extraction, reasoning, tool‑use).
- Log strutturato (ULM) + gestione errori (UEM) con codici.
- No PII verso provider esterni (DataSanitizer).

---

## 2) HIGH‑LEVEL DESIGN (OS3)

```
User → NatanChatService → AiMultimodelGateway → [Policy Engine]
                                           ↘ ProviderAdapter[*]
                                            ↘ EmbeddingAdapter[*]
                                            ↘ Observability (ULM/UEM)
                                            ↘ DataSanitizer
```

**Principi:**
- **Dependency Inversion**: i servizi applicativi dipendono da interfacce, non da SDK.
- **Single Responsibility**: routing/policy separati dall’esecuzione provider.
- **No implicit limits (Statistics Rule)**: i metodi espongono `$limit = null` opzionale.

---

## 3) INTERFACES (PHP – AI‑readable, production‑ready)

```php
<?php

namespace App\AI\Contracts;

use App\DTOs\AI\{AiMessage, AiTool, AiResponse, AiEmbeddingResult};

/**
 * @purpose Abstraction for chat/inference across providers
 */
interface ChatModel {
    /**
     * @param AiMessage[] $messages
     * @param AiTool[] $tools
     * @param array $options  // ['temperature'=>..., 'max_tokens'=>..., 'persona'=>..., 'tenant_id'=>...]
     */
    public function generate(array $messages, array $tools = [], array $options = []): AiResponse;
}

/**
 * @purpose Abstraction for text embeddings across providers
 */
interface EmbeddingModel {
    /** @return AiEmbeddingResult */
    public function embed(string $text, array $options = []);
}

/**
 * @purpose Routing/policy. Decides which provider to use per call.
 */
interface AiRouter {
    public function selectChatModel(array $context): ChatModel;     // persona, tenant, task_class
    public function selectEmbeddingModel(array $context): EmbeddingModel;
}
```

### DTOs (scheletri minimi)
```php
namespace App\DTOs\AI;

class AiMessage { public string $role; public string $content; }
class AiTool { public string $name; public array $schema; }
class AiResponse { public string $model; public string $text; public array $citations = []; public array $meta = []; }
class AiEmbeddingResult { public string $model; /** @var float[] */ public array $vector; public int $dimensions; }
```

---

## 4) PROVIDER ADAPTERS

**Namespace:** `App\AI\Providers\{OpenAI|Anthropic|Ollama|AISIRU}`

Contract: implementano `ChatModel`/`EmbeddingModel`.
- `OpenAIChatAdapter`, `OpenAIEmbeddingAdapter`
- `AnthropicChatAdapter`
- `OllamaChatAdapter` (local models)
- `AISIRUChatAdapter` (enterprise)

**Linee guida:**
- Niente SDK vendor‑lock diretto nei controller/services applicativi.
- Mapping **1:1** tra opzioni gateway e parametri provider.
- **UEM** su errori di rete/timeouts con codici dedicati.

---

## 5) POLICY ENGINE

```php
interface PolicyEngine {
    /**
     * @param array $ctx ['persona'=>'strategic', 'tenant_id'=>2, 'task_class'=>'RAG']
     * @return array { 'chat_model'=>'anthropic.sonnet-3.5', 'embedding_model'=>'openai.text-3-large' }
     */
    public function resolve(array $ctx): array;
}
```

**Esempio di regole (config YAML):**
```yaml
# config/ai_policies.yaml
version: 1
fallbacks:
  chat: ["anthropic.sonnet-3.5", "openai.gpt-4.1", "ollama.llama3-70b"]
  embed: ["openai.text-3-large", "ollama.nomic-embed", "aisiru.embed-v1"]
policies:
  - match: { persona: "strategic", task_class: "RAG" }
    use: { chat: "anthropic.sonnet-3.5", embed: "openai.text-3-large" }
  - match: { tenant_id: 0, locality: "onprem" }
    use: { chat: "ollama.llama3-8b", embed: "ollama.nomic-embed" }
```

---

## 6) OBSERVABILITY (ULM/UEM)

- **ULM**: log start/success/error con `tenant_id`, `persona`, `task_class`, `model`, `latency_ms`.
- **UEM**: codici errori standardizzati.

**config/error-manager.php (estratto)**
```php
return [
  'AI_PROVIDER_TIMEOUT' => [
    'type' => 'error', 'blocking' => 'semi-blocking',
    'dev_message_key' => 'error-manager::errors_2.dev.ai_provider_timeout',
    'user_message_key' => 'error-manager::errors_2.user.ai_provider_timeout',
    'http_status_code' => 504, 'msg_to' => 'toast'
  ],
  'AI_POLICY_NOT_FOUND' => [
    'type' => 'error', 'blocking' => 'blocking',
    'dev_message_key' => 'error-manager::errors_2.dev.ai_policy_not_found',
    'user_message_key' => 'error-manager::errors_2.user.ai_policy_not_found',
    'http_status_code' => 500, 'msg_to' => 'toast'
  ],
];
```

**i18n (vendor/error-manager/it/errors_2.php)**
```php
return [
  'dev' => [
    'ai_provider_timeout' => 'Timeout contacting :provider for :task_class (tenant=:tenant_id).',
    'ai_policy_not_found' => 'No AI routing policy matches ctx=:context.'
  ],
  'user' => [
    'ai_provider_timeout' => 'Il servizio AI è momentaneamente lento. Riprova tra poco.',
    'ai_policy_not_found' => 'Servizio non disponibile. Stiamo aggiornando le impostazioni.'
  ]
];
```

---

## 7) DATA SANITIZATION (GDPR)

```php
interface DataSanitizer { public function cleanse(string $text, array $rules = []): string; }

// Uso nel gateway prima della call esterna
$clean = $this->sanitizer->cleanse($prompt, [ 'pii' => true, 'hash_ids' => true, 'mask_emails' => true ]);
```

**Principio:** N.A.T.A.N. invia **solo metadati pubblici** e contenuti già pubblici; ogni PII viene rimossa o mascherata; audit trail via `AuditLogService` (GdprActivityCategory::DATA_ACCESS).

---

## 8) RAG INTEGRATION

- EmbeddingModel agganciato a `RagService`.
- Se presente **pgvector** → routing automatico su DB‑side similarity.
- **Statistics Rule:** i metodi `search($query, ?int $limit=null)` non impongono limiti nascosti.

---

## 9) CONFIGURAZIONE (.env)

```
AI_DEFAULT_PROVIDER=anthropic
AI_ALLOWED_CHAT_PROVIDERS=anthropic,openai,ollama,aisiru
AI_ALLOWED_EMBED_PROVIDERS=openai,ollama,aisiru
AI_POLICY_FILE=config/ai_policies.yaml
```

---

## 10) TEST PLAN (excerpt)
- Unit: `PolicyEngineTest` (match/fallback).
- Contract: `ChatModelContractTest` (uniformità risposta).
- Resilience: inietta timeouts → verifica **UEM** `AI_PROVIDER_TIMEOUT`.
- GDPR: `DataSanitizerTest` su PII.

---

## 11) DELIVERABLES
- `App/AI/Contracts/*` interfaces
- `App/AI/Providers/*` adapters
- `App/AI/Policy/*` PolicyEngine (YAML backed)
- `App/AI/Gateway/AiMultimodelGateway.php`
- Docs + examples + tests.

---


# /docs/whitepaper/NATAN_WHITE_PAPER_EXEC_SUMMARY.md

> **Executive Summary (Institutional Edition)**  
> **Project:** **N.A.T.A.N.** – Cognitive Trust Layer for PA & Enterprises  
> **Claim:** “Non immagina. **Dimostra**.”  

## Problema
PA e aziende soffrono di **disordine documentale**, tempi lenti, rischi di **errore umano** e **contesto disperso**. Gli strumenti AI generalisti non sono **verificabili** né **certificabili** in ambienti ad alta compliance.

## Soluzione – N.A.T.A.N.
Una piattaforma **cognitiva** che **custodisce, comprende e certifica** i documenti:
- **Affidabilità**: risposte basate **solo** su fonti documentali interne, citate e verificabili (RAG certificato).
- **Sicurezza totale**: architettura **multi‑tenant isolata**, embeddings **non reversibili**, log/audit completi (GDPR, ULM, UEM).
- **Intelligenza cognitiva**: interfaccia naturale, personas istituzionali, azioni guidate e “quick elaborations”.

## Come funziona (sintesi tecnica)
1) **Ingest** documenti → **embeddings** anonimi (non reversibili) → indicizzazione vettoriale.  
2) Query utente → **RAG** recupera passaggi pertinenti → **LLM** selezionato dal **Multimodel Gateway**.  
3) Output con **citazioni** puntuali e **verifica pubblica** (hash notarizzati su blockchain quando richiesto).

## Perché ora
- Maturità di RAG + LLM + pgvector + stack OS3 (ULM/UEM/GDPR).  
- Pressione su **trasparenza** e **accountability** nella PA (D.Lgs. 33/2013).  

## Benefici chiave
- **−70%** tempi di ricerca interna / risposta ai cittadini.  
- **>95%** accuratezza su metadati e riassunti di atti ripetitivi (benchmark interni).  
- **Zero‑leak** perimetrale: dati segregati e **mai** inviati in chiaro a terzi.  

## Differenziali competitivi
- **AI‑agnostic** (gateway multimodello) + **Local Mode** (on‑prem/air‑gap).  
- **Compliance by design** (GDPR/UEM/ULM) + **audit trail** notarizzabile.  
- Verticale **PA** (albo, determine, delibere) con **personas** specializzate.

## Go‑to‑Market (PA → Enterprise)
- **Pilot** con ente singolo (tenant isolato) → **scaling** per cluster territoriale.  
- Modello **SaaS ibrido**: cloud, ibrido, full on‑prem.

## KPI iniziali
- TTK (Time‑to‑Knowledge) < **5s** a query  
- Copertura embeddings > **90%** degli atti caricati  
- Tempo on‑boarding ente < **48h**  

## Roadmap
- **Q1:** Multimodel Gateway + pgvector  
- **Q2:** Verifica pubblica wide + toolkit migrazione  
- **Q3:** App mobile ispettori + automations task‑based

**Tagline ufficiale**:  
**“N.A.T.A.N. – L’intelligenza cognitiva che custodisce, comprende e certifica i tuoi documenti. Affidabile come un notaio, sicura come un caveau, intelligente come un esperto.”**


# /docs/architecture/NATAN_LOCAL_MODE_ADDENDUM.md

> **Addendum Tecnico – NATAN Local/On‑Prem Mode**  
> **Scopo:** definire il profilo **air‑gapped/edge** conforme a OS3

## 1) Profili di Deploy
- **Cloud** (multi‑tenant isolato)  
- **Hybrid** (indice locale + orchestrazione cloud)  
- **Local/On‑Prem** (**questo documento**):
  - LLM **Ollama** (Llama3, Mistral) su server locale.
  - Embeddings locali (e.g., `nomic-embed-text`) o **pgvector**.
  - Nessun traffico verso Internet (policy di rete enforced).

## 2) Architettura
```
Browser → Natan UI → NatanChatService → AiMultimodelGateway → Ollama (LAN)
                                               ↘ RagService → pgvector (LAN)
                                               ↘ DataSanitizer → AuditLog (GDPR)
```

## 3) Requisiti Minimi
- CPU 8c / RAM 32GB / SSD NVMe 1TB
- Docker + Ollama + Postgres 16 + pgvector
- Reverse proxy con mTLS per segmentazione interna

## 4) Sicurezza (OS3 Fortino Digitale)
- **Network**: blocco egress per default; allowlist solo CRL/OCSP se richiesto.
- **Secret management**: `.env.local` cifrato, rotating keys.
- **Audit**: `AuditLogService` con `GdprActivityCategory::SECURITY_EVENTS`.

## 5) Gateway Settings (esempio)
```env
AI_DEFAULT_PROVIDER=ollama
AI_ALLOWED_CHAT_PROVIDERS=ollama
AI_ALLOWED_EMBED_PROVIDERS=ollama
RAG_BACKEND=pgvector
```

## 6) Procedure
- **Cold start**: pre‑pull modelli (`ollama pull llama3:8b`), warm‑up embeddings.
- **Backup**: snapshot **solo** DB/indice; i file originali restano on‑prem.
- **DR**: runbook con RPO 15m / RTO 60m.

## 7) Test & Validation
- Latency chat < 1200ms (LAN).  
- Similarity top‑k coerente con baseline cloud.  
- Nessun DNS egress in audit.


# /docs/marketing/NATAN_MARKETING_GUIDELINES.md

> **Linea Guida Marketing – N.A.T.A.N.**  
> **Pilastri:** Affidabilità • Sicurezza Totale • Intelligenza Cognitiva  

## 1) Messaggi chiave
- **Affidabilità**: “N.A.T.A.N. **non immagina**: cita i documenti e **dimostra** la fonte.”
- **Sicurezza totale**: “Multi‑tenant isolato, embeddings non reversibili, **zero leak**.”
- **Intelligenza cognitiva**: “Parla il linguaggio della PA, collega concetti, **consiglia azioni**.”

## 2) Prova & Differenziali
- **Citazioni visibili** in ogni risposta.
- **Verifica pubblica** (hash → blockchain) quando richiesto.
- **Local Mode** per siti ad alta riservatezza.

## 3) Tone of Voice
- Istituzionale, chiaro, **anti‑hype**.  
- “Mostra, non promettere.”  

## 4) Claim & payoff
- **Claim:** “Non immagina. **Dimostra**.”  
- **Payoff:** “Affidabile come un notaio, sicura come un caveau, intelligente come un esperto.”

## 5) One‑pager Content Blocks
1. **Problema** (disordine documentale, tempi, rischio errori).  
2. **Soluzione** (cognitive trust layer).  
3. **Come funziona** (ingest → RAG → risposta con fonti).  
4. **Sicurezza & Compliance** (GDPR/ULM/UEM).  
5. **Modalità di adozione** (cloud/ibrido/on‑prem).  
6. **KPI** (TTK <5s; >95% accuracy metadati).  

## 6) Checklist materiali
- White paper (exec + tecnico) ✅  
- Slide 10‑12 pagine  
- Demo script 15′  
- Datasheet 2 pagine  

## 7) Frasi approvate (uso interno/esterno)
- “**AI‑agnostic** con gateway multimodello.”
- “**Zero accessi** ai dati: solo embeddings anonimi.”
- “**Verifica pubblica** su richiesta.”

---

**Nota OS3:** tutti i testi esterni devono evitare assoluti tecnici non verificabili; usare metriche con fonte interna; nessun riferimento a dati non pubblici.

