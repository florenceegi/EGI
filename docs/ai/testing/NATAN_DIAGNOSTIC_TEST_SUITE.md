# NATAN Diagnostic Test Suite

**Version:** 1.0.0  
**Date:** 2025-10-27  
**Purpose:** Systematic testing to identify bottlenecks and failure points

---

## 🎯 TEST CATEGORIES

### **1. SEMANTIC SEARCH TESTS**

**Obiettivo:** Verificare qualità e performance della ricerca semantica

#### Test 1.1: Volume Scaling

```Analizza lo stato manutentivo delle infrastrutture pubbliche e proponi un piano di manutenzione predittiva basato su priorità
Query: "delibere approvate nel 2024"

Expected behavior:
- <100 atti trovati: ✅ Dovrebbe funzionare sempre
- 100-500 atti: ⚠️ Potrebbe avere rate limit
- 500-1000 atti: ❌ Quasi sempre rate limit
- >1000 atti: ❌ Sempre rate limit

Metric to track:
- Acts found
- Response time
- Success rate
```

#### Test 1.2: Query Specificity

```
Test queries (dal generico allo specifico):

A. "delibere"
   Expected: ~1539 atti (troppi)

B. "delibere 2024"
   Expected: ~500-700 atti

C. "delibere su manutenzione strade 2024"
   Expected: ~50-100 atti

D. "delibera numero 234 del 15 marzo 2024"
   Expected: 1-5 atti

Success rate expected:
A: 0% (troppi atti)
B: 30% (molti atti, dipende da rate limit)
C: 80% (numero gestibile)
D: 95% (molto specifico)
```

#### Test 1.3: Semantic vs Keyword

```
Test pairs:

1a. "progetti di sostenibilità ambientale" (semantico)
1b. "delibere che contengono la parola 'ambiente'" (keyword)

2a. "investimenti in infrastrutture pubbliche" (semantico)
2b. "delibere con importo > 100.000 euro" (structured)

3a. "iniziative per il sociale" (vago)
3b. "delibere assessorato servizi sociali" (specifico)

Compare:
- Relevance score
- Number of results
- Success rate
```

---

### **2. CONTEXT WINDOW TESTS**

**Obiettivo:** Trovare sweet spot per numero atti inviabili a Claude

#### Test 2.1: Fixed Context Sizes

```
Query fissa: "Riassumi le delibere più importanti"

Test con limiti progressivi:
- 5 atti
- 10 atti
- 25 atti
- 50 atti
- 100 atti
- 200 atti

For each size:
- Success rate (10 attempts)
- Avg response time
- Rate limit frequency
- Response quality (1-5 scale)
```

#### Test 2.2: Adaptive Retry Effectiveness

```
Start with 1000 atti trovati

Track retry sequence:
Attempt 1: 10 atti → Success/Fail?
Attempt 2: 5 atti → Success/Fail?
Attempt 3: 5 atti (10s wait) → Success/Fail?

Metrics:
- How many attempts before success?
- Total time spent
- Final acts count used
- Quality of response with reduced context
```

---

### **3. RATE LIMIT PATTERN ANALYSIS**

**Obiettivo:** Capire pattern temporali dei rate limit Anthropic

#### Test 3.1: Burst Testing

```
Send 10 queries in rapid succession (no delay):

Query: "Analizza delibere gennaio 2024"
Limit: 10 atti per query

Track:
- Which attempt fails first?
- After how many seconds?
- How long to recover?
```

#### Test 3.2: Gradual Ramp-Up

```
Start: 5 atti
Wait: 60 seconds
Next: 10 atti
Wait: 60 seconds
Next: 25 atti
...continue scaling...

Find: Maximum sustainable rate without hitting limits
```

---

### **4. EMBEDDING QUALITY TESTS**

**Obiettivo:** Verificare se embeddings catturano semantica correttamente

#### Test 4.1: Known Relevant Acts

```
Manually identify 5 delibere about "manutenzione strade"

Query: "progetti di manutenzione delle infrastrutture viarie"

Check:
- Are known relevant acts in top 10 results?
- What's their ranking position?
- What's their similarity score?
```

#### Test 4.2: False Positive Analysis

```
Query: "progetti ambientali sostenibili"

Manually check top 20 results:
- How many are ACTUALLY about environment?
- How many are false positives?
- What causes false positives? (similar words? topic overlap?)
```

---

### **5. TIMEOUT & PERFORMANCE TESTS**

#### Test 5.1: End-to-End Timing

```
Break down total time:

1. Semantic search: ____ seconds
2. Embedding similarity: ____ seconds
3. Context preparation: ____ seconds
4. Claude API call: ____ seconds
5. Response parsing: ____ seconds

Total: ____ seconds

Identify bottleneck.
```

#### Test 5.2: Database Query Performance

```sql
-- Test embedding search performance
EXPLAIN ANALYZE
SELECT * FROM pa_act_embeddings
ORDER BY embedding <-> '[test_vector]'
LIMIT 100;

-- Check if index is used
-- Measure query time with different LIMIT values
```

---

## 🔧 **TEST EXECUTION PLAN**

### **Day 1: Data Collection**

```bash
# Create test log directory
mkdir -p storage/logs/natan-tests

# Run automated test suite
php artisan natan:diagnostic-test --category=semantic-search
php artisan natan:diagnostic-test --category=context-window
php artisan natan:diagnostic-test --category=rate-limit
```

### **Day 2: Analysis**

```bash
# Generate test report
php artisan natan:generate-test-report

# Output: storage/logs/natan-tests/report-2025-10-27.md
```

### **Day 3: Optimization**

Based on test results, implement fixes:

-   Adjust semantic search parameters
-   Optimize context window size
-   Improve retry strategy
-   Add intelligent caching

---

## 📊 **SUCCESS METRICS**

**CURRENT STATE (baseline):**

-   Success rate: ~30% (estimate)
-   Avg response time: 30-120s (quando funziona)
-   Rate limit frequency: ~70%
-   User satisfaction: ⭐⭐ (2/5)

**TARGET STATE (after optimization):**

-   Success rate: >90%
-   Avg response time: <15s
-   Rate limit frequency: <10%
-   User satisfaction: ⭐⭐⭐⭐⭐ (5/5)

---

## 🚀 **NEXT STEPS**

1. **Implement test harness** (artisan commands)
2. **Run full test suite** (collect data)
3. **Analyze results** (identify patterns)
4. **Prioritize fixes** (ROI-based)
5. **Iterate** (test → fix → test)

---

## 📝 **TEST LOG TEMPLATE**

```json
{
    "test_id": "semantic-search-1.1-volume-100",
    "timestamp": "2025-10-27T20:00:00Z",
    "query": "delibere 2024",
    "acts_found": 687,
    "acts_sent_to_claude": 10,
    "retry_attempts": 2,
    "total_time_seconds": 45,
    "success": true,
    "rate_limit_hit": true,
    "response_quality": 4,
    "notes": "Reduced from 687 to 10 acts due to rate limit"
}
```
