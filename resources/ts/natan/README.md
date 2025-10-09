# N.A.T.A.N. TypeScript Components

**N.A.T.A.N.** (Nodo di Analisi e Tracciamento Atti Notarizzati)  
AI Document Intelligence System for Public Administration

**Version:** 1.0.0  
**Author:** Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici  
**Date:** 2025-10-09

---

## 📦 Components

### 1. **ApiClient.ts**

HTTP client for N.A.T.A.N. API endpoints with CSRF protection.

**Features:**

-   Document upload with multipart/form-data
-   Acts retrieval with filters and pagination
-   Full-text search
-   Statistics and filter options
-   Job status polling

**Usage:**

```typescript
import { getNatanApiClient } from "@/natan";

const api = getNatanApiClient();

// Upload document
const result = await api.uploadDocument(file);

// Get acts with filters
const acts = await api.getActs({ tipo: "Determina" }, 1, 20);

// Search
const results = await api.searchActs("restauro");

// Get stats
const stats = await api.getStats();
```

---

### 2. **UploadManager.ts**

Drag & drop upload interface with progress tracking.

**Features:**

-   Drag & drop support
-   File validation (type, size)
-   Multiple file upload
-   Real-time progress
-   Job status polling
-   Custom events

**Usage:**

```typescript
import { UploadManager } from "@/natan";
import { getNatanApiClient } from "@/natan";

const api = getNatanApiClient();
const manager = new UploadManager("#uploadDropzone", "#fileInput", api);

// Callbacks
manager.onComplete((jobId) => {
    console.log("Upload completed:", jobId);
});

manager.onError((error) => {
    console.error("Upload error:", error);
});
```

---

### 3. **ActsTable.ts**

Sortable, filterable table for acts with pagination.

**Features:**

-   Sortable columns
-   Advanced filters
-   Pagination
-   Search integration
-   Auto-refresh on new acts

**Usage:**

```typescript
import { initActsTable } from "@/natan";
import { getNatanApiClient } from "@/natan";

const api = getNatanApiClient();
const table = initActsTable("#tableContainer", api);

// Apply filters
table.applyFilters({
    tipo: "Determina",
    data_from: "2025-01-01",
    importo_min: 10000,
});

// Refresh
table.refresh();

// Reset filters
table.resetFilters();
```

---

### 4. **StatsPanel.ts**

Statistics panel with Chart.js visualizations.

**Features:**

-   KPI cards auto-update
-   Doughnut chart (act types)
-   Line chart (monthly trends)
-   Chart.js integration
-   Responsive design

**Usage:**

```typescript
import { initStatsPanel } from "@/natan";
import { getNatanApiClient } from "@/natan";

const api = getNatanApiClient();
const panel = initStatsPanel(api);

// Reload stats
await panel.load();

// Cleanup
panel.destroy();
```

---

## 🚀 Quick Start

### Auto-initialization

The module auto-initializes all components on DOM ready:

```html
<script src="/js/natan.js" type="module"></script>
```

Components are automatically detected and initialized based on DOM elements:

-   `#uploadDropzone` + `#fileInput` → UploadManager
-   `#tableContainer` → ActsTable
-   `#actTypeChart` or `#monthlyTrendChart` → StatsPanel

### Manual initialization

```typescript
import { initNatan } from "@/natan";

// Initialize all components
initNatan();
```

---

## 📡 Events

### Custom Events Dispatched

-   `natan:upload-started` - Upload initiated
-   `natan:upload-completed` - Upload completed, processing started
-   `natan:upload-failed` - Upload failed
-   `natan:act-processed` - Act processing completed
-   `natan:show-detail` - Show act detail modal
-   `natan:show-qr` - Show QR code modal

### Listen to Events

```typescript
window.addEventListener("natan:act-processed", (e: CustomEvent) => {
    console.log("New act processed:", e.detail.act);
    // Refresh table, update stats, etc.
});
```

---

## 🎨 Brand Guidelines Compliance

**Colors used (FlorenceEGI Brand):**

-   `#8E44AD` - Viola Innovazione (AI/Innovation)
-   `#1B365D` - Blu Algoritmo (Technology/Trust)
-   `#2D5016` - Verde Rinascita (Success/Environment)
-   `#D4A574` - Oro Fiorentino (Premium/Highlights)
-   `#6B6B6B` - Grigio Pietra (Secondary text)
-   `#C13120` - Rosso Urgenza (Errors/Critical)

---

## 🔒 Security

**CSRF Protection:**

-   All requests include X-CSRF-TOKEN header
-   Token auto-detected from `<meta name="csrf-token">`

**Authentication:**

-   Uses Laravel Sanctum session auth
-   Requires `auth:sanctum` middleware on API routes
-   Permission check: `access_pa_dashboard`

**Rate Limiting:**

-   Upload: 20 requests/hour
-   Job polling: 120 requests/minute
-   Standard endpoints: 60 requests/minute

---

## 🧪 Testing

TypeScript components are designed for testability:

```typescript
import { NatanApiClient } from "@/natan/ApiClient";

// Mock API for testing
const mockApi = {
    uploadDocument: jest.fn(),
    getActs: jest.fn(),
    // ...
};

const manager = new UploadManager("#zone", "#input", mockApi as any);
```

---

## 📚 Dependencies

**Required:**

-   Chart.js (loaded via CDN in views)
-   Modern browser with Fetch API
-   CSS classes defined in blade views

**TypeScript:**

-   ES2020+ target
-   Strict mode enabled
-   Module resolution: node

---

## 🔧 Build

TypeScript files are compiled via Laravel Mix or Vite:

```bash
# Development
npm run dev

# Production
npm run build
```

Compiled output: `public/js/natan.js`

---

## 📖 Documentation

**Full API Documentation:** See `docs/ai/context/NATAN_PER_ENTI_IMPLEMENTATION_GUIDE.md`

**Architecture:** See implementation guide for:

-   System flow
-   Database schema
-   API contracts
-   Testing strategy
-   Deployment checklist

---

## 🤝 Contributing

**Standards:**

-   OS3.0 Documentation signatures
-   Type safety (no `any` except where necessary)
-   Error handling with try/catch
-   Console.error for errors, console.log for debug
-   Descriptive variable names (AI-readable)

**Before committing:**

-   Run `npm run lint`
-   Test in browser console
-   Check TypeScript compilation
-   Verify no console errors

---

**Ship it! 🚀**
