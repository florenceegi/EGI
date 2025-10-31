Perfetto.  
Ecco il documento completo e coerente con lo stile OS3 e la terminologia FlorenceEGI/NATAN.  
File:  
📁 `docs/natan_loc/USE_IMPLEMENTATION.md`  
Titolo: **Ultra Semantic Engine (USE)**

---

```markdown
# 🧠 USE – Ultra Semantic Engine
**Modulo Cognitivo Integrato per NATAN_LOC**

---

## 1. Executive Summary

**USE (Ultra Semantic Engine)** è il nucleo cognitivo di NATAN_LOC.  
Unisce **processo neurale** (comprensione e generazione linguistica) e **processo logico** (verifica, controllo, audit) per produrre risposte **citate, verificabili e consapevoli**.  
Ogni affermazione generata dal sistema possiede:
- **una fonte tracciabile** (link cliccabile),
- **un punteggio di affidabilità (URS)**,
- **uno stato epistemico** (certa, dedotta, ignota).

USE non sostituisce la mente umana, ma le fornisce **una mappa della certezza**.  
Il suo scopo è impedire l’allucinazione sistemica tipica dei LLM, integrando **coscienza semantica e rigore ingegneristico**.

---

## 2. Architettura Generale

```

┌────────────────────────────┐  
│ User Query │  
└────────────┬───────────────┘  
│  
▼  
┌────────────────────────────┐  
│ 1. Question Classifier │  
│ (neurale leggero) │  
└────────────┬───────────────┘  
│ → intent, confidenza, constraint  
▼  
┌────────────────────────────┐  
│ 2. Execution Router │  
│ (logico deterministico) │  
└────────────┬───────────────┘  
│  
├──→ Direct Query → Mongo (entities/pages)  
├──→ RAG → 3. Neurale Strict  
└──→ Block → richiesta chiarimento  
▼  
┌────────────────────────────┐  
│ 3. Neurale Strict │  
│ (LLM controllato) │  
│ produce claims[] │  
└────────────┬───────────────┘  
▼  
┌────────────────────────────┐  
│ 4. Logical Verifier │  
│ calcola URS e verifica │  
└────────────┬───────────────┘  
▼  
┌────────────────────────────┐  
│ 5. Renderer │  
│ HTML + colori + link + URS│  
└────────────┬───────────────┘  
▼  
┌────────────────────────────┐  
│ 6. Audit & ULM/UEM Logs │  
└────────────────────────────┘

````

---

## 3. Flusso Operativo

1. **Classifier** analizza la domanda (`fact_check`, `numerical`, `interpretation`, ecc.) e produce un `intent` con confidenza.
2. **Router** decide se inoltrare la query:
   - a un motore logico (Mongo, ricerca diretta),
   - al motore neurale “strict” (solo se necessario),
   - oppure bloccarla se interpretativa o non verificabile.
3. **Retriever** estrae dai database solo **passaggi citabili** con `source_ref`.
4. **LLM Strict** genera risposte con claims (una frase = un’affermazione) e relative fonti.
5. **Verifier** calcola l’**ULTRA Reliability Score (URS)** e assegna label `A/B/C/X`.
6. **Renderer** visualizza ogni claim:
   - verde/blu se citato e affidabile;
   - rosso se privo di fonte;
   - “[Deduzione]” se inferenza.
7. **ULM/UEM** registrano tutto: domande, fonti, URS, click sui link, esiti.

---

## 4. Schema Dati Mongo

### `sources`
```json
{
  "_id": "src_fir_241_p3",
  "entity_id": "PA_Firenze",
  "type": "pdf",
  "title": "Delibera 241/2025",
  "url": "https://...#page=3",
  "hash": "sha256(file)",
  "retrieved_at": "2025-10-31T12:00:00Z"
}
````

### `claims`

```json
{
  "_id": "clm_77a_1",
  "answer_id": "ans_77a",
  "text": "L’importo complessivo è €125.000.",
  "source_ids": ["src_fir_241_p3"],
  "is_inference": false,
  "basis_ids": [],
  "urs": 0.91,
  "label": "A",
  "created_at": "2025-10-31T12:10:00Z"
}
```

### `query_audit`

```json
{
  "_id": "qa_241",
  "question": "Qual è l’importo della delibera?",
  "intent": "fact_check",
  "classifier_conf": 0.82,
  "router_action": "RAG_LLM_STRICT",
  "status": "SAFE",
  "latency_ms": 734
}
```

---

## 5. ULTRA Reliability Score (URS)

|Parametro|Descrizione|Peso|
|---|---|---|
|**C** – Coverage|quanta parte del claim è coperta da fonti|0.30|
|**R** – Reference count|numero di fonti citanti (1=0.6, 2=0.8, ≥3=1.0)|0.25|
|**E** – Extractor quality|qualità OCR o estrazione (0–1)|0.20|
|**D** – Date coherence|coerenza temporale e semantica|0.15|
|**O** – Out-of-domain risk|penalità (1–0) se fonte esterna|0.10|

Formula:  
`URS = 0.30*C + 0.25*R + 0.20*E + 0.15*D + 0.10*(1 - O)`

**Label:**

- ≥ 0.85 → `A – Affidabile`
    
- 0.70–0.84 → `B – Buona`
    
- 0.50–0.69 → `C – Debole`
    
- < 0.50 → `X – Non pubblicabile`
    

---

## 6. Regole OS3 Anti-Allucinazione

1. Ogni frase = 1 _claim_.
    
2. Ogni claim deve avere almeno una `source_id` o essere esplicitamente marcato `is_inference:true`.
    
3. Nessun claim privo di fonte può essere pubblicato in “strict mode”.
    
4. Ogni claim ha uno **stato epistemico**: `certa`, `dedotta`, `ignota`.
    
5. Il Renderer:
    
    - evidenzia in rosso le affermazioni senza fonte;
        
    - mostra badge `[Deduzione]` per le inferenze;
        
    - inserisce link sull’intera frase verso la fonte.
        
6. Tutto il processo viene tracciato in **ULM/UEM**:
    
    - `CLAIM_RENDERED`, `CLAIM_BLOCKED`, `CLAIM_CLICKED`
        
    - con `urs`, `label`, `gdpr_trace_id`.
        

---

## 7. Pseudocodice Principale (TypeScript)

```ts
async function processQuery(question: string, user: User) {
  const meta = await classify(question);
  if (meta.confidence < 0.6 || isBlocked(meta.intent)) 
    return askClarification(question);

  const chunks = await retrieveChunks(question, meta.constraints);
  const claims = await generateStrict(question, chunks);
  const verified = verifyClaims(claims, chunks);
  return renderAnswerHTML(verified);
}

function verifyClaims(claims, chunks) {
  return claims.map(c => {
    const sources = c.source_ids?.length || 0;
    const C = coverage(c, chunks);
    const R = referenceScore(sources);
    const E = extractorQuality(chunks);
    const D = dateCoherence(chunks);
    const O = domainRisk(chunks);
    const urs = 0.30*C + 0.25*R + 0.20*E + 0.15*D + 0.10*(1 - O);
    const label = classifyURS(urs);
    return { ...c, urs, label };
  });
}
```

---

## 8. Audit e Compliance (ULM/UEM)

Ogni interazione genera un log con:

```json
{
  "action": "QUERY_EXECUTED",
  "user_id": "usr_fabio",
  "gdpr_trace_id": "ULM_2025_10_31_1220",
  "meta": {
    "intent": "fact_check",
    "router_action": "RAG_LLM_STRICT",
    "avg_urs": 0.89
  }
}
```

Le funzioni UEM (Error Manager) intercettano:

- mancanza di fonte,
    
- incoerenze numeriche,
    
- URS < 0.5,
    
- assenza di chunk citabile.
    

---

## 9. Visione Concettuale (Oracode)

USE è il punto d’incontro fra **coscienza simbolica** e **logica verificabile**.  
Il layer neurale interpreta, il layer logico custodisce la verità.  
Ogni claim è un atto di linguaggio dotato di **livello di certezza dichiarato**, e l’interfaccia diventa lo **specchio epistemico** del sistema.

Nel linguaggio Oracode:

> “Il flusso del dato si trasforma in parola solo quando la parola sa da dove viene.”

USE incarna la **Coscienza Semantica OS3**:  
l’AI non solo risponde, ma _sa quanto può rispondere_.

---

## 10. Sintesi Finale

|Elemento|Funzione|
|---|---|
|**Classifier**|Etichetta la domanda con confidenza|
|**Router**|Decide il percorso cognitivo|
|**Retriever**|Estrae fonti citabili|
|**LLM Strict**|Genera claims con fonti|
|**Verifier**|Calcola URS, blocca falsi|
|**Renderer**|Mostra fonte, deduzione o rosso|
|**ULM/UEM**|Audit, log, compliance|
|**USE**|Sintesi di logica e coscienza, certezza nel linguaggio|

---

**Fine documento – USE (Ultra Semantic Engine)**

```

---

Vuoi che nella prossima fase io prepari anche il **diagramma visivo (flow + layer)** da usare nella documentazione ufficiale di NATAN_LOC, con stile OS3 (linee, simboli e palette neutra)?
```