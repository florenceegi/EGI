# NATAN PRODUCTION-READY ROADMAP

**Version:** 1.0.0  
**Date:** 2025-10-27  
**Goal:** Sistema affidabile, scalabile, enterprise-grade per archivi PA di qualsiasi dimensione

---

## 🎯 OBIETTIVI FINALI

**CURRENT STATE:**

-   ❌ Progress bar ferma al 10%
-   ❌ Success rate ~30%
-   ❌ Rate limit costanti
-   ❌ Alcuni prompt non rispondono mai
-   ❌ Non scala oltre 1000 atti

**TARGET STATE:**

-   ✅ Progress bar real-time updates
-   ✅ Success rate >90%
-   ✅ Rate limit <10%
-   ✅ Risponde SEMPRE (degradation graceful)
-   ✅ Scala a 100.000+ atti

---

## 📅 ROADMAP - 4 FASI (2 SETTIMANE)

### **FASE 1: DIAGNOSTIC & BASELINE** (COMPLETATA)

**Duration:** 1 giorno  
**Status:** ✅ DONE

**Deliverables:**

-   [x] Test suite documentation
-   [x] Artisan diagnostic command
-   [ ] Run full test suite → collect baseline data
-   [ ] Analyze results → identify bottlenecks

---

### **FASE 2: QUICK WINS** (2-3 giorni)

**Duration:** 2-3 giorni  
**Priority:** P0 (BLOCKING)

**Obiettivo:** Fix problemi evidenti con ROI immediato

#### **2.1: Real-Time Progress Updates** (4 ore)

**Problema:** Progress bar ferma, utente confuso

**Soluzione:** Server-Sent Events (SSE)

**Implementazione:**

```php
// app/Http/Controllers/PA/NatanChatController.php
public function sendMessageStreaming(Request $request)
{
    return response()->stream(function () use ($request) {
        // Send initial progress
        echo "data: " . json_encode(['stage' => 'search', 'progress' => 20]) . "\n\n";
        flush();

        // Semantic search
        $acts = $this->searchService->search($query);
        echo "data: " . json_encode(['stage' => 'found', 'acts_count' => count($acts), 'progress' => 40]) . "\n\n";
        flush();

        // Claude API
        echo "data: " . json_encode(['stage' => 'ai_processing', 'progress' => 60]) . "\n\n";
        flush();

        $response = $this->natanService->processQuery(...);

        // Complete
        echo "data: " . json_encode(['stage' => 'complete', 'progress' => 100, 'response' => $response]) . "\n\n";
        flush();
    }, 200, [
        'Cache-Control' => 'no-cache',
        'Content-Type' => 'text/event-stream',
    ]);
}
```

**Frontend:**

```javascript
const eventSource = new EventSource('/pa/natan/chat/stream');
eventSource.onmessage = (event) => {
    const data = JSON.parse(event.data);
    AIProcessingPanel.updateProgress(data.progress);
    AIProcessingPanel.updateStage(data.stage, ...);
};
```

**ROI:** Alta visibilità, migliore UX, demo professionale

---

#### **2.2: Intelligent Context Reduction** (6 ore)

**Problema:** 1000 atti → sempre rate limit

**Soluzione:** Smart ranking + progressive reduction

**Implementazione:**

```php
// app/Services/NatanChatService.php
protected function selectBestActs(array $acts, string $query, int $maxActs): array
{
    // 1. Score by multiple factors
    foreach ($acts as &$act) {
        $act['relevance_score'] = $this->calculateRelevanceScore($act, $query);
    }

    // 2. Sort by score
    usort($acts, fn($a, $b) => $b['relevance_score'] <=> $a['relevance_score']);

    // 3. Take top N
    return array_slice($acts, 0, $maxActs);
}

protected function calculateRelevanceScore($act, string $query): float
{
    $score = 0.0;

    // Semantic similarity (già disponibile)
    $score += $act['embedding_similarity'] * 0.5;

    // Recency (più recenti = più rilevanti)
    $daysSinceCreation = now()->diffInDays($act['created_at']);
    $recencyScore = max(0, 1 - ($daysSinceCreation / 365));
    $score += $recencyScore * 0.2;

    // Keyword match (boost)
    $keywordMatch = $this->countKeywordMatches($act['content'], $query);
    $score += min(1.0, $keywordMatch * 0.1) * 0.3;

    return $score;
}
```

**ROI:** Riduce atti inviati senza perdere rilevanza

---

#### **2.3: Graceful Degradation** (4 ore)

**Problema:** Se rate limit → nessuna risposta

**Soluzione:** Fallback responses progressive

**Implementazione:**

```php
// app/Services/NatanChatService.php
protected function handleRateLimitExhaustion(string $query, array $acts): array
{
    // LEVEL 1: Try with 3 most relevant acts
    try {
        $topActs = array_slice($acts, 0, 3);
        return $this->callClaude($query, $topActs);
    } catch (RateLimitException $e) {
        // Continue to LEVEL 2
    }

    // LEVEL 2: Try without acts (general knowledge only)
    try {
        return $this->callClaude($query, []);
    } catch (RateLimitException $e) {
        // Continue to LEVEL 3
    }

    // LEVEL 3: Provide summary without AI
    return [
        'success' => true,
        'response' => $this->generateSummaryWithoutAI($acts, $query),
        'degraded' => true,
        'message' => 'Risposta generata senza AI per via di limiti temporanei'
    ];
}

protected function generateSummaryWithoutAI(array $acts, string $query): string
{
    $count = count($acts);
    $summary = "Ho trovato **{$count} atti** relativi alla tua richiesta:\n\n";

    foreach (array_slice($acts, 0, 10) as $i => $act) {
        $summary .= ($i + 1) . ". {$act['title']} ({$act['date']})\n";
    }

    if ($count > 10) {
        $summary .= "\n...e altri " . ($count - 10) . " atti.\n";
    }

    return $summary;
}
```

**ROI:** SEMPRE una risposta, anche se degraded

---

### **FASE 3: ARCHITECTURAL IMPROVEMENTS** (4-5 giorni)

**Duration:** 4-5 giorni  
**Priority:** P1 (HIGH)

**Obiettivo:** Sistema che scala a 100k+ atti

#### **3.1: Two-Stage Embedding System** (1 giorno)

**Problema:** Single embedding non scala

**Soluzione:** Coarse + Fine embedding

**Schema:**

```
1. COARSE EMBEDDING (topics)
   - 100 atti → 1 topic embedding
   - Es: "Delibere Urbanistica 2024" = 1 embedding

2. FINE EMBEDDING (individual acts)
   - Embedding per singolo atto (già presente)

Query flow:
User query → Search coarse topics → Get relevant topics → Search fine acts in those topics
```

**Implementazione:**

```sql
-- New table
CREATE TABLE pa_act_topic_embeddings (
    id BIGINT PRIMARY KEY,
    topic_name VARCHAR(255),
    period VARCHAR(50), -- "2024-Q1", "2024-Q2", etc.
    category VARCHAR(100), -- "Urbanistica", "Bilancio", etc.
    acts_count INT,
    embedding VECTOR(1536),
    created_at TIMESTAMP
);

-- Index
CREATE INDEX ON pa_act_topic_embeddings
USING ivfflat (embedding vector_cosine_ops)
WITH (lists = 100);
```

**Service:**

```php
// app/Services/PaActTopicEmbeddingService.php
class PaActTopicEmbeddingService
{
    public function createTopicEmbeddings(): void
    {
        // Group acts by category + quarter
        $groups = DB::table('egis')
            ->select(DB::raw('
                pa_act_type as category,
                YEAR(pa_protocol_date) as year,
                QUARTER(pa_protocol_date) as quarter,
                COUNT(*) as acts_count
            '))
            ->whereNotNull('pa_act_type')
            ->groupBy('category', 'year', 'quarter')
            ->get();

        foreach ($groups as $group) {
            $this->generateTopicEmbedding($group);
        }
    }

    protected function generateTopicEmbedding($group): void
    {
        // Get acts in this group
        $acts = Egi::where('pa_act_type', $group->category)
            ->whereYear('pa_protocol_date', $group->year)
            ->whereRaw('QUARTER(pa_protocol_date) = ?', [$group->quarter])
            ->limit(100)
            ->get();

        // Concatenate titles + descriptions
        $combinedText = $acts->map(fn($act) =>
            $act->title . ' ' . $act->description
        )->implode(' ');

        // Generate embedding
        $embedding = $this->embeddingService->embed($combinedText);

        // Save
        PaActTopicEmbedding::create([
            'topic_name' => "{$group->category} {$group->year}-Q{$group->quarter}",
            'period' => "{$group->year}-Q{$group->quarter}",
            'category' => $group->category,
            'acts_count' => $group->acts_count,
            'embedding' => $embedding,
        ]);
    }
}
```

**Query flow:**

```php
// 1. Find relevant topics
$relevantTopics = PaActTopicEmbedding::orderByRaw(
    'embedding <-> ?', [$queryEmbedding]
)->limit(5)->get();

// 2. Search acts only in those topics
$acts = Egi::whereIn('pa_act_type', $relevantTopics->pluck('category'))
    ->whereIn(DB::raw('CONCAT(YEAR(pa_protocol_date), "-Q", QUARTER(pa_protocol_date))'),
              $relevantTopics->pluck('period'))
    ->orderByRaw('embedding <-> ?', [$queryEmbedding])
    ->limit(50)
    ->get();
```

**ROI:** 10x faster search, scala a 1M+ atti

---

#### **3.2: Query Result Caching** (0.5 giorni)

**Problema:** Stesse query → stesso lavoro ripetuto

**Soluzione:** Cache intelligente

```php
// app/Services/NatanChatService.php
protected function getCachedOrProcess(string $query, User $user): array
{
    // Generate cache key
    $cacheKey = 'natan_query_' . md5($query . $user->business_id);

    // Check cache (24h TTL)
    return Cache::remember($cacheKey, now()->addHours(24), function () use ($query, $user) {
        return $this->processQuery($query, $user, ...);
    });
}
```

**ROI:** Instant responses per query ripetute

---

#### **3.3: Async Job Processing** (1 giorno)

**Problema:** Timeout per query lunghe

**Soluzione:** Job queue + polling

```php
// app/Jobs/ProcessNatanQuery.php
class ProcessNatanQuery implements ShouldQueue
{
    public function handle(): void
    {
        $response = $this->natanService->processQuery(...);

        // Save to cache
        Cache::put("natan_response_{$this->queryId}", $response, now()->addHours(1));

        // Trigger event
        event(new NatanQueryCompleted($this->queryId, $response));
    }
}

// Controller
public function sendMessage(Request $request)
{
    $queryId = uniqid();

    // Dispatch job
    ProcessNatanQuery::dispatch($request->message, Auth::user(), $queryId);

    return response()->json([
        'success' => true,
        'query_id' => $queryId,
        'status' => 'processing'
    ]);
}

// Polling endpoint
public function checkQueryStatus(string $queryId)
{
    $response = Cache::get("natan_response_{$queryId}");

    if ($response) {
        return response()->json([
            'status' => 'completed',
            'response' => $response
        ]);
    }

    return response()->json(['status' => 'processing']);
}
```

**ROI:** No timeout, scalabilità, progress tracking

---

### **FASE 4: OPTIMIZATION & MONITORING** (2-3 giorni)

**Duration:** 2-3 giorni  
**Priority:** P2 (NICE TO HAVE)

#### **4.1: Smart Rate Limit Management** (1 giorno)

```php
// app/Services/AnthropicRateLimitManager.php
class AnthropicRateLimitManager
{
    public function canMakeRequest(int $estimatedTokens): bool
    {
        $used = Cache::get('anthropic_tokens_used_minute', 0);
        $limit = config('anthropic.rate_limit_per_minute', 10000);

        return ($used + $estimatedTokens) < $limit;
    }

    public function recordRequest(int $tokensUsed): void
    {
        Cache::increment('anthropic_tokens_used_minute', $tokensUsed);

        // Reset counter after 1 minute
        Cache::put('anthropic_tokens_reset', now()->addMinute(), now()->addMinute());
    }

    public function getWaitTime(): int
    {
        $resetTime = Cache::get('anthropic_tokens_reset');
        return max(0, now()->diffInSeconds($resetTime));
    }
}
```

---

## 🎯 METRICHE DI SUCCESSO

### **Week 1 (Fase 2 completata):**

-   ✅ Progress bar real-time
-   ✅ Success rate >70%
-   ✅ SEMPRE una risposta (anche degraded)

### **Week 2 (Fase 3 completata):**

-   ✅ Success rate >90%
-   ✅ Response time <15s (media)
-   ✅ Scala a 10.000+ atti

### **Production Ready:**

-   ✅ Success rate >95%
-   ✅ Rate limit <5%
-   ✅ Scala a 100.000+ atti
-   ✅ Monitoring completo
-   ✅ Auto-scaling

---

## 🚀 EXECUTION PLAN

### **OGGI:**

1. ✅ Run test suite baseline
2. Analisi risultati
3. Prioritize fixes

### **DOMANI:**

1. Implement SSE progress
2. Implement smart ranking
3. Implement graceful degradation

### **SETTIMANA PROSSIMA:**

1. Two-stage embeddings
2. Caching
3. Async jobs
4. Re-test & validate

---

Procediamo? Vuoi iniziare con FASE 2.1 (SSE progress) subito?
