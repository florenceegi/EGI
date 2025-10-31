# README_STACK.md — N.A.T.A.N. / FlorenceEGI (OS3)

**Version:** 1.0.1 (corretto)  
**Maintainer:** Fabio Cherici / Padmin D. Curtis  
**Updated:** 2025-10-30

---

## 🎯 Executive Summary

Stack OS3 ufficiale per **N.A.T.A.N. / FlorenceEGI**: piattaforma SaaS ibrida multi-tenant con **Laravel (backend core)**, **TypeScript/JavaScript puro (frontend)** e **Python (AI/scraping)**.  
Dati transazionali in **MariaDB**, layer cognitivo e log AI in **MongoDB**.  
Sicurezza e compliance GDPR integrate tramite ULM, UEM e AuditTrail.

---

## 🧩 STRUTTURA GENERALE

| Layer | Tecnologia | Ruolo |
|-------|-------------|-------|
| **Frontend** | TypeScript / JavaScript + Tailwind CSS + Vite | UI applicativa, accessibilità, SEO |
| **Backend Core** | PHP 8.3 + Laravel 12 | SaaS multi-tenant, AuthN/Z, API REST, business logic |
| **AI / Scraping** | Python 3.12 + FastAPI / CLI | RAG, embeddings, scraping albi PA |
| **Database 1 (Transazionale)** | MariaDB | Tabelle relazionali per tenant |
| **Database 2 (Cognitivo)** | MongoDB | Embeddings, chat AI, analytics/logs AI |
| **Caching/Queue** | Redis | Code Laravel + job Python, rate-limits |
| **Blockchain** | Algorand (AlgoKit / SDK JS) | Notarizzazione hash, CoA, proof |
| **Logging & Errori** | ULM / UEM / Audit GDPR | Tracciamento, gestione errori, audit trail |

---

## ⚙️ BACKEND — LARAVEL 12

- **Tenancy:** `stancl/tenancy` (single-DB con `tenant_id` e middleware detection).  
- **Auth:** Laravel Sanctum + Spatie `laravel-permission`.  
- **Media:** Spatie `medialibrary` (allegati PA/EGI).  
- **API:** REST/JSON consumate da frontend JS o Python.  
- **Security:** CORS controllati, CSRF su rotte web, header di sicurezza.

**Pattern:** Laravel espone API → frontend TS/JS puro consuma → Python opera in parallelo per scraping/AI.

---

## 🐍 MICRO‑SERVIZI — PYTHON 3.12

- **Framework:** FastAPI (REST) + Uvicorn.  
- **Scraping:** `requests`, `beautifulsoup4`, `lxml`, opzionale `playwright`.  
- **AI / RAG:** `openai`, `anthropic`, `pymongo`, `numpy`, `faiss` (opzionale), `celery` + Redis per job batch.  
- **Endpoints:** `/embed`, `/rag/search`, `/scrape/run`, `/healthz`.  

**Pipeline tipica:** PDF → parsing → chunking → embedding → Mongo (`pa_act_embeddings`) → Laravel restituisce metadati → frontend visualizza.

---

## 🖥️ FRONTEND — JAVASCRIPT / TYPESCRIPT PURO

- **Build:** Vite.  
- **Styling:** Tailwind CSS.  
- **UI:** JS/TS modulare, componenti semantici, ARIA-ready.  
- **Accessibilità:** WCAG 2.1 AA, semantica chiara, SEO-friendly.  
- **Autenticazione:** API Token Sanctum (Bearer) via fetch.  

**Non usa framework (React/Vue/Next)** per policy OS3: leggibilità, controllo totale e leggerezza.

---

## 🗄️ DATABASES

### MariaDB (Transazionale)
- Tabelle: `users`, `pa_entities`, `pa_acts`, `user_conversations`, `pa_web_scrapers`, `roles`, `permissions`.
- Campo `tenant_id` obbligatorio + indici.

### MongoDB (Cognitivo)
- Collezioni: `pa_act_embeddings`, `natan_chat_messages`, `ai_logs`, `analytics`.  
- Indici: `{ tenant_id: 1, act_id: 1 }`, opzionale vector index.  

### Redis
- Caching, rate limiting e code (Laravel + Celery).

---

## 🤖 AI & RAG PIPELINE

1. **Ingestione:** Python (scraping + parsing + embedding).  
2. **Persistenza:** MongoDB (`tenant_id`, `act_id`, `metadata`).  
3. **Ricerca:** query → vector search → risultati → join con `PaAct` (MariaDB).  
4. **Output:** generazione Claude / OpenAI, citazioni e fonti.

---

## 🔗 BLOCKCHAIN

- **Algorand (AlgoKit)** per notarizzazione hash, TXID salvato in MariaDB.  
- Pagina pubblica `/verify` per controllo hash/CoA.  

---

## 🧱 LOGGING / ERROR HANDLING / GDPR

| Sistema | Componente | Scopo |
|----------|-------------|-------|
| **ULM** | UltraLogManager | log operativo (start / success / error) |
| **UEM** | ErrorManagerInterface | errori strutturati (codici, blocking level, alert) |
| **Audit** | AuditLogService | attività utente e GDPR category |
| **Consent** | ConsentService | gestione e versioning consensi |

---

## 🔒 SICUREZZA & COMPLIANCE

- `tenant_id` ovunque (Maria + Mongo + logs).  
- Sanitizzazione PII prima di invii a provider AI.  
- Rate limiting su endpoint AI/chat.  
- Cifratura segreti per tenant in MariaDB.  
- Policy e Gate Laravel per accessi granulari.  
- HTTPS + HSTS in staging/prod.  

---

## 🧰 DEVOPS / DEPLOY

**Ambienti:** Local → Staging → Production  
**Deploy Staging:**
```bash
ssh user@staging
cd /path/to/app
git pull origin main
composer install --no-dev --optimize-autoloader
npm install && npm run build
php artisan migrate --force
php artisan pa:generate-embeddings --limit=100
php artisan optimize
```

Checklist:
- [ ] API keys configurate  
- [ ] Migrations OK  
- [ ] Embeddings generati  
- [ ] Risposte NATAN <5s  
- [ ] Test UI completato  

---

## 🧪 TESTS
- **TenantIsolationTest** → no cross-tenant leaks.  
- **RAGAccuracyTest** → atti corretti in Top‑K.  
- **GDPRAuditTest** → audit log registrato.  
- **ULMIntegrityTest** → log presenti in ogni servizio.

---

## 🧭 MATRICE RESPONSABILITÀ

| Ruolo | Responsabilità |
|--------|----------------|
| **Fabio Cherici (Architect)** | Visione, standard OS3, requisiti e approvazioni |
| **Padmin D. Curtis (CTO)** | Implementazione tecnica, sicurezza, code quality |
| **N.A.T.A.N.** | Interfaccia cognitiva, orchestrazione documentale |
| **Padmin Analyzer** | QA OS3, GDPR, MICA, code linting |

---

## ✅ CHECKLIST OS3 READY
- [ ] Regola Zero rispettata (no deduzioni).  
- [ ] STATISTICS Rule (no limiti impliciti).  
- [ ] Nessun testo hardcoded.  
- [ ] Logging ULM/UEM completo.  
- [ ] Audit GDPR presente.  
- [ ] `tenant_id` in tutti i layer.  
- [ ] Test E2E completati.  

---

**Fine documento — Stack OS3 ufficiale (v1.0.1)**

