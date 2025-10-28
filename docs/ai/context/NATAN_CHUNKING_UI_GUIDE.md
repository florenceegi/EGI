# NATAN Intelligent Chunking - UI Progress Panel Guide

**Version:** 4.0  
**Date:** 2025-10-27  
**Author:** Padmin D. Curtis (AI Partner OS3.0)  
**Purpose:** Guida completa all'uso del pannello progress con supporto chunking

---

## 📋 PANORAMICA

Il sistema di progress AI di NATAN è stato esteso per supportare:

1. **Processing Normale** - Per dataset piccoli/medi (<200 atti)
2. **Processing con Chunking** - Per dataset grandi (>200 atti)

Il pannello mostra in tempo reale:

-   Progress globale dell'operazione
-   Visual grid dei chunk (completati/in corso/in attesa)
-   Progress chunk corrente
-   Statistiche live (atti analizzati, tempo, rilevanza)
-   Risultati parziali da ogni chunk

---

## 🎯 ARCHITETTURA

### **File Modificati:**

1. **`resources/views/pa/natan/_ai-processing-panel.blade.php`**

    - Aggiunta sezione `aiChunkingProgress` (hidden di default)
    - Visual grid chunks (max 10 visibili per riga)
    - Real-time chunk progress bar
    - Partial results preview

2. **`public/js/ai-processing-panel.js`**
    - Nuovi metodi chunking: `showChunking()`, `updateChunkProgress()`, `completeChunk()`
    - Auto-aggregation quando tutti i chunk completati
    - State management per chunking mode

---

## 🚀 USAGE - PROCESSING NORMALE

### **Backend (PHP):**

```php
// NatanChatController.php
public function analyzeActs(Request $request): JsonResponse
{
    $totalFound = 150; // Atti trovati dalla ricerca

    // Caso 1: Dataset piccolo (no chunking)
    return response()->json([
        'success' => true,
        'mode' => 'normal',
        'total_acts' => $totalFound,
        'message' => 'Elaborazione in corso...'
    ]);
}
```

### **Frontend (JavaScript):**

```javascript
// Nel controller AJAX che chiama /analyze
fetch("/pa/natan/analyze", {
    method: "POST",
    body: JSON.stringify({ query: userQuery }),
})
    .then((response) => response.json())
    .then((data) => {
        if (data.mode === "normal") {
            // Mostra pannello normale
            AIProcessingPanel.show(data.total_acts);

            // Simula progress durante processing
            AIProcessingPanel.updateProgress(50);
            AIProcessingPanel.updateStats({
                acts: data.total_acts,
                relevance: 85,
            });

            // Quando finito
            AIProcessingPanel.complete();
        }
    });
```

---

## ⚙️ USAGE - PROCESSING CON CHUNKING

### **Backend (PHP):**

```php
// NatanChatController.php
public function analyzeActs(Request $request): JsonResponse
{
    $totalFound = 900; // Atti trovati dalla ricerca
    $userLimit = $request->input('user_limit', 500);

    // Chunking necessario
    $estimation = $this->chunkingService->estimateProcessing($totalFound, $userLimit);

    return response()->json([
        'success' => true,
        'mode' => 'chunking',
        'total_acts' => $userLimit,
        'total_chunks' => $estimation['chunks_needed'],
        'estimated_time' => $estimation['estimated_time_seconds'],
        'estimated_cost' => $estimation['estimated_cost_euros'],
        'session_id' => $sessionId, // Per polling progress
    ]);
}
```

### **Frontend (JavaScript):**

```javascript
// Step 1: Avvia chunking mode
fetch("/pa/natan/analyze", {
    method: "POST",
    body: JSON.stringify({
        query: userQuery,
        user_limit: 500,
    }),
})
    .then((response) => response.json())
    .then((data) => {
        if (data.mode === "chunking") {
            // Mostra pannello chunking
            AIProcessingPanel.showChunking(
                data.total_chunks, // Es: 5
                data.total_acts // Es: 500
            );

            // Step 2: Avvia polling per progress
            startChunkingPoll(data.session_id);
        }
    });

// Step 3: Polling backend ogni 2 secondi
function startChunkingPoll(sessionId) {
    const pollInterval = setInterval(() => {
        fetch(`/pa/natan/chunking-progress/${sessionId}`)
            .then((res) => res.json())
            .then((progress) => {
                if (progress.current_chunk !== undefined) {
                    // Aggiorna progress chunk corrente
                    AIProcessingPanel.updateChunkProgress(
                        progress.current_chunk, // Es: 2 (terzo chunk)
                        progress.chunk_progress, // Es: 75 (75%)
                        progress.acts_in_chunk // Es: 180 atti
                    );
                }

                if (progress.chunk_completed) {
                    // Chunk completato
                    AIProcessingPanel.completeChunk(
                        progress.completed_chunk_index,
                        {
                            relevantActs: progress.relevant_acts_found,
                            summary: progress.chunk_summary,
                        }
                    );
                }

                if (progress.all_completed) {
                    // Tutti i chunk completati
                    clearInterval(pollInterval);

                    // Pannello chiama automaticamente startAggregation()
                    // Aspetta risultato finale aggregato
                    waitForFinalResponse(sessionId);
                }
            });
    }, 2000); // Poll ogni 2 secondi
}

// Step 4: Aspetta risposta finale aggregata
function waitForFinalResponse(sessionId) {
    fetch(`/pa/natan/chunking-final/${sessionId}`)
        .then((res) => res.json())
        .then((final) => {
            // Completa processo
            AIProcessingPanel.completeChunking();

            // Mostra risposta finale in chat
            displayFinalResponse(final.aggregated_response);
        });
}
```

---

## 📊 BACKEND IMPLEMENTATION - CHUNKING ENDPOINTS

### **1. Endpoint Analyze (Inizio Processing):**

```php
// routes/pa-enterprise.php
Route::post('/natan/analyze', [NatanChatController::class, 'analyzeActs'])
    ->name('pa.natan.analyze');

// NatanChatController.php
public function analyzeActs(Request $request): JsonResponse
{
    $validated = $request->validate([
        'query' => 'required|string',
        'user_limit' => 'nullable|integer|min:50|max:5000',
    ]);

    $user = Auth::user();
    $query = $validated['query'];
    $userLimit = $validated['user_limit'] ?? config('natan.slider_default_acts');

    // Phase 1: Keyword extraction
    $keywords = $this->extractKeywords($query);

    // Phase 2: Count total matching acts
    $totalFound = Egi::query()
        ->whereHas('collection', fn($q) => $q->where('creator_id', $user->id))
        ->whereNotNull('pa_act_type')
        ->where(function($q) use ($keywords) {
            foreach ($keywords as $keyword) {
                $q->orWhere('title', 'LIKE', "%{$keyword}%")
                  ->orWhere('description', 'LIKE', "%{$keyword}%");
            }
        })
        ->count();

    // Phase 3: Estimate chunking
    $estimation = $this->chunkingService->estimateProcessing($totalFound, $userLimit);

    if ($estimation['chunks_needed'] === 1) {
        // Caso normale (no chunking)
        return response()->json([
            'success' => true,
            'mode' => 'normal',
            'total_acts' => $totalFound,
        ]);
    }

    // Caso chunking
    $sessionId = Str::uuid();

    // Store session in cache for polling
    Cache::put("natan_chunking_{$sessionId}", [
        'user_id' => $user->id,
        'query' => $query,
        'user_limit' => $userLimit,
        'total_acts' => $totalFound,
        'total_chunks' => $estimation['chunks_needed'],
        'keywords' => $keywords,
        'current_chunk' => 0,
        'completed_chunks' => [],
        'status' => 'started',
    ], now()->addHours(2));

    // Start background job
    dispatch(new ProcessChunkedAnalysis($sessionId));

    return response()->json([
        'success' => true,
        'mode' => 'chunking',
        'session_id' => $sessionId,
        'total_chunks' => $estimation['chunks_needed'],
        'total_acts' => $userLimit,
        'estimated_time_seconds' => $estimation['estimated_time_seconds'],
        'estimated_cost_euros' => $estimation['estimated_cost_euros'],
    ]);
}
```

### **2. Endpoint Progress Polling:**

```php
// routes/pa-enterprise.php
Route::get('/natan/chunking-progress/{sessionId}', [NatanChatController::class, 'getChunkingProgress'])
    ->name('pa.natan.chunking-progress');

// NatanChatController.php
public function getChunkingProgress(string $sessionId): JsonResponse
{
    $session = Cache::get("natan_chunking_{$sessionId}");

    if (!$session) {
        return response()->json(['error' => 'Session not found'], 404);
    }

    return response()->json([
        'current_chunk' => $session['current_chunk'],
        'chunk_progress' => $session['chunk_progress'] ?? 0,
        'acts_in_chunk' => $session['acts_in_current_chunk'] ?? 0,
        'completed_chunks' => count($session['completed_chunks']),
        'total_chunks' => $session['total_chunks'],
        'chunk_completed' => $session['last_completed'] ?? false,
        'completed_chunk_index' => $session['last_completed_index'] ?? null,
        'relevant_acts_found' => $session['last_chunk_relevant_acts'] ?? null,
        'chunk_summary' => $session['last_chunk_summary'] ?? null,
        'all_completed' => $session['status'] === 'aggregating',
    ]);
}
```

### **3. Endpoint Final Response:**

```php
// routes/pa-enterprise.php
Route::get('/natan/chunking-final/{sessionId}', [NatanChatController::class, 'getChunkingFinal'])
    ->name('pa.natan.chunking-final');

// NatanChatController.php
public function getChunkingFinal(string $sessionId): JsonResponse
{
    $session = Cache::get("natan_chunking_{$sessionId}");

    if (!$session || $session['status'] !== 'completed') {
        return response()->json(['error' => 'Not ready'], 425); // 425 Too Early
    }

    return response()->json([
        'success' => true,
        'aggregated_response' => $session['final_response'],
        'total_relevant_acts' => $session['total_relevant_acts'],
        'chunks_processed' => count($session['completed_chunks']),
        'total_time_seconds' => $session['total_time_seconds'],
    ]);
}
```

---

## 🎨 UI/UX FEATURES

### **Visual Chunks Grid:**

-   Max 10 chunks per riga (responsive: 5 su mobile)
-   Stati visivi:
    -   **Grigio** = In attesa (`border-gray-300 bg-gray-100`)
    -   **Blu pulsante** = In corso (`border-blue-500 bg-blue-100 animate-pulse`)
    -   **Verde con check** = Completato (`border-green-500 bg-green-100`)

### **Progress Bars:**

-   **Globale** (Header): Avanzamento complessivo 0-100%
-   **Chunk corrente**: Progress del chunk in elaborazione

### **Live Stats:**

-   Atti analizzati
-   Rilevanza % media
-   Tempo trascorso (auto-update ogni secondo)
-   Modello AI (Claude Sonnet 4.5)

### **Partial Results Preview:**

Mostra risultati parziali man mano che i chunk vengono completati:

```
✅ Risultati parziali:
Chunk 1: 23 atti rilevanti trovati
Chunk 2: 17 atti rilevanti trovati
Chunk 3: 31 atti rilevanti trovati
```

---

## 🔄 STATE FLOW

```
┌─────────────────────────┐
│ User submits query      │
└───────────┬─────────────┘
            │
            ▼
┌─────────────────────────┐
│ Backend: Search Preview │ (/search-preview)
│ Returns: total_found    │
└───────────┬─────────────┘
            │
            ▼
┌─────────────────────────┐     ┌──────────────────────┐
│ User adjusts slider     │────▶│ Show estimation      │
│ Confirms limit          │     │ (cost, time, chunks) │
└───────────┬─────────────┘     └──────────────────────┘
            │
            ▼
┌─────────────────────────┐
│ Backend: Analyze        │ (/analyze)
│ Decides: normal/chunking│
└───────────┬─────────────┘
            │
      ┌─────┴─────┐
      │           │
      ▼           ▼
┌──────────┐  ┌─────────────┐
│ Normal   │  │ Chunking    │
│ Mode     │  │ Mode        │
└────┬─────┘  └──────┬──────┘
     │               │
     │               ▼
     │        ┌──────────────────┐
     │        │ Background Job   │
     │        │ ProcessChunked   │
     │        └────────┬─────────┘
     │                 │
     │                 ▼
     │        ┌──────────────────┐
     │        │ Poll Progress    │◀───┐
     │        │ Every 2s         │    │
     │        └────────┬─────────┘    │
     │                 │               │
     │                 ├───────────────┘
     │                 │ (until all chunks done)
     │                 │
     │                 ▼
     │        ┌──────────────────┐
     │        │ Aggregation      │
     │        │ Phase            │
     │        └────────┬─────────┘
     │                 │
     └────────┬────────┘
              │
              ▼
      ┌──────────────────┐
      │ Final Response   │
      │ Display in Chat  │
      └──────────────────┘
```

---

## 📝 JAVASCRIPT API REFERENCE

### **AIProcessingPanel.show(actsCount)**

Mostra pannello in modalità normale.

**Params:**

-   `actsCount` (number): Numero atti totali da processare

**Example:**

```javascript
AIProcessingPanel.show(150);
```

---

### **AIProcessingPanel.showChunking(totalChunks, totalActs)**

Mostra pannello in modalità chunking.

**Params:**

-   `totalChunks` (number): Numero totale di chunk
-   `totalActs` (number): Numero totale atti da processare

**Example:**

```javascript
AIProcessingPanel.showChunking(5, 900);
```

---

### **AIProcessingPanel.updateChunkProgress(chunkIndex, progress, actsInChunk)**

Aggiorna progress del chunk corrente.

**Params:**

-   `chunkIndex` (number): Indice chunk (0-based)
-   `progress` (number): Percentuale 0-100
-   `actsInChunk` (number): Numero atti nel chunk

**Example:**

```javascript
AIProcessingPanel.updateChunkProgress(2, 75, 180);
// Chunk 3/5: 75% completato, 180 atti
```

---

### **AIProcessingPanel.completeChunk(chunkIndex, result)**

Marca chunk come completato con risultati.

**Params:**

-   `chunkIndex` (number): Indice chunk (0-based)
-   `result` (object):
    -   `relevantActs` (number): Atti rilevanti trovati
    -   `summary` (string, optional): Riepilogo risultati

**Example:**

```javascript
AIProcessingPanel.completeChunk(2, {
    relevantActs: 23,
    summary: "Trovati 23 atti su appalti pubblici",
});
```

---

### **AIProcessingPanel.updateStats({ acts, relevance })**

Aggiorna statistiche live.

**Params:**

-   `acts` (number, optional): Atti analizzati
-   `relevance` (number, optional): Rilevanza % (0-100)

**Example:**

```javascript
AIProcessingPanel.updateStats({
    acts: 450,
    relevance: 87,
});
```

---

### **AIProcessingPanel.completeChunking()**

Completa processo chunking (chiamare quando aggregazione finita).

**Example:**

```javascript
AIProcessingPanel.completeChunking();
```

---

### **AIProcessingPanel.hide()**

Nasconde pannello e resetta stato.

**Example:**

```javascript
AIProcessingPanel.hide();
```

---

## 🚨 ERROR HANDLING

### **Retry con Rate Limit:**

```javascript
AIProcessingPanel.showRetryInfo(
    2, // Tentativo corrente
    5, // Tentativi massimi
    50, // Riduzione atti
    10 // Attesa secondi
);
```

### **Timeout/Errors:**

Se polling fallisce o timeout, nascondere pannello e mostrare errore:

```javascript
AIProcessingPanel.hide();
showErrorMessage("Timeout durante elaborazione. Riprova.");
```

---

## 📊 BEST PRACTICES

1. **Polling Interval**: 2 secondi (balance tra real-time e server load)
2. **Cache TTL**: 2 ore per sessioni chunking
3. **Background Jobs**: Usare Queue Laravel per processing pesante
4. **Error Recovery**: Se job fallisce, session cache mantiene stato per retry
5. **UI Feedback**: Sempre mostrare cost/time estimates PRIMA di avviare chunking
6. **Mobile UX**: Grid chunks responsive (5 colonne su mobile, 10 su desktop)

---

## 🎯 TODO - PROSSIMI STEP

1. ✅ Backend `/search-preview` implementato
2. ✅ Backend `/analyze` implementato (estimation only)
3. ✅ Frontend modal progress implementato
4. ⏳ **Background Job `ProcessChunkedAnalysis`** (da creare)
5. ⏳ **Polling endpoints `/chunking-progress` e `/chunking-final`** (da implementare)
6. ⏳ **Frontend polling loop** (da integrare in chat.blade.php)
7. ⏳ **Aggregation logic** nel backend (combinare risultati chunk)
8. ⏳ **Testing con dataset 1000+ atti**

---

## 📚 RIFERIMENTI

-   **Config**: `config/natan.php`
-   **Service**: `app/Services/Natan/NatanIntelligentChunkingService.php`
-   **Controller**: `app/Http/Controllers/PA/NatanChatController.php`
-   **View**: `resources/views/pa/natan/_ai-processing-panel.blade.php`
-   **JS**: `public/js/ai-processing-panel.js`
-   **Translations**: `resources/lang/it/natan.php`

---

**Fine documentazione v4.0** 🚀
