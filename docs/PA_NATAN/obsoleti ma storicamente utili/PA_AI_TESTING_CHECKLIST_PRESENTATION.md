# 🧪 PA + AI SYSTEM - CHECKLIST TESTING APPROFONDITO

**Data:** 2025-10-23  
**Deadline Presentazione:** 4-5 giorni  
**Obiettivo:** Testing completo sistema PA + N.A.T.A.N. AI per presentazione

---

## 📋 INDICE RAPIDO

1. [Test Priorità CRITICA](#test-priorità-critica) (2 giorni)
2. [Test Priorità ALTA](#test-priorità-alta) (1 giorno)
3. [Test Priorità MEDIA](#test-priorità-media) (1 giorno)
4. [Fix Bugs Identificati](#fix-bugs-identificati)
5. [Preparazione Demo](#preparazione-demo)
6. [Checklist Pre-Presentazione](#checklist-pre-presentazione)

---

## 🔥 TEST PRIORITÀ CRITICA

**Tempo stimato:** 2 giorni  
**Blockers:** Se falliscono, presentazione a rischio

### ✅ TEST 1: Upload + Tokenizzazione End-to-End

**Obiettivo:** Verificare workflow completo da upload a blockchain

#### Setup

```bash
# Verifica servizi running
docker ps | grep algorand  # AlgoKit microservice
systemctl status mysql
php artisan queue:work --daemon  # Queue worker
```

#### Steps

1. **Login come PA Entity**

    - [ ] Vai su http://localhost/login
    - [ ] Credenziali PA: `pa@test.com` / password
    - [ ] Verifica redirect a `/pa/dashboard`

2. **Upload Atto Amministrativo**

    - [ ] Vai su `/pa/acts/upload`
    - [ ] Carica PDF firmato da `storage/testing/real_signed_pa_acts/`
    - [ ] Verifica validazione firma OK (badge verde)
    - [ ] Compila metadati:
        - [ ] Protocollo: "TEST-2023-001"
        - [ ] Data: oggi
        - [ ] Tipo: "Determina Dirigenziale"
        - [ ] Titolo: "Test Tokenizzazione Sistema"
        - [ ] Descrizione: "Atto di test per validazione sistema"
    - [ ] **Spunta checkbox "Tokenizza su blockchain"**
    - [ ] Clicca "Carica Atto"
    - [ ] ⏱️ **Tempo atteso: < 5 secondi**

3. **Verifica Database**

    ```bash
    php artisan tinker
    ```

    ```php
    $egi = \App\Models\Egi::latest()->first();
    $egi->pa_protocol_number; // "TEST-2023-001"
    $egi->pa_doc_type; // "Determina Dirigenziale"
    $egi->pa_document_hash; // Hash SHA-256
    $egi->public_code; // VER-XXXXXX
    ```

    - [ ] Verifica tutti i campi popolati correttamente

4. **Verifica Queue Job**

    ```bash
    # Controlla log queue
    tail -f storage/logs/laravel.log | grep TokenizePaActJob
    ```

    - [ ] Job dispatched correttamente
    - [ ] Job processato senza errori
    - [ ] ⏱️ **Tempo processing: < 30 secondi**

5. **Verifica Blockchain Anchoring**

    ```php
    $egi->pa_anchored_at; // DateTime presente
    $egi->pa_txid; // Transaction ID Algorand
    $egi->blockchain_url; // Link AlgoExplorer
    ```

    - [ ] `pa_anchored_at` NON null
    - [ ] `pa_txid` presente e valido
    - [ ] ⚠️ **Se TXID mancante: BUG CRITICO**

6. **Verifica AlgoExplorer**

    - [ ] Copia TXID
    - [ ] Vai su https://testnet.algoexplorer.io/tx/{TXID}
    - [ ] Verifica transazione visibile
    - [ ] Verifica note field contiene hash documento
    - [ ] ⚠️ **Se transazione non trovata: BUG CRITICO**

7. **Verifica UI Dashboard**

    - [ ] Vai su `/pa/acts`
    - [ ] Verifica atto appena caricato nella lista
    - [ ] Badge "Tokenizzato su Blockchain" presente e verde
    - [ ] Timestamp anchoring visibile
    - [ ] TXID cliccabile con link esterno

8. **Test Visualizzazione Dettaglio**
    - [ ] Clicca su atto appena creato
    - [ ] Vai su `/pa/acts/{id}`
    - [ ] Verifica tutte le sezioni:
        - [ ] Metadati protocollo
        - [ ] Hash documento (truncato con copy button)
        - [ ] Badge blockchain con checkmark verde
        - [ ] Link AlgoExplorer funzionante
        - [ ] QR code visibile e nitido

#### Risultato Atteso

-   ✅ Workflow completo senza errori
-   ✅ Atto tokenizzato su blockchain
-   ✅ TXID visibile su AlgoExplorer
-   ✅ UI mostra tutti i dati correttamente

#### Troubleshooting

Se fallisce, controlla:

```bash
# Log Laravel
tail -100 storage/logs/laravel.log

# Log Queue Jobs
php artisan queue:failed

# Log AlgoKit
docker logs algokit-microservice
```

---

### ✅ TEST 2: Chat N.A.T.A.N. AI - RAG Conversazionale

**Obiettivo:** Verificare AI chat funzionante con RAG

#### Setup

```bash
# Verifica API key Anthropic
php artisan tinker
config('services.anthropic.api_key'); // NON vuoto

# Oppure Ollama locale
curl http://localhost:11434/api/tags  # Lista modelli
```

#### Steps

1. **Accedi Chat N.A.T.A.N.**

    - [ ] Login come PA Entity
    - [ ] Vai su `/pa/natan/chat`
    - [ ] Verifica UI chat caricata
    - [ ] Input field funzionante

2. **Test Query Base**

    - [ ] Scrivi: "Ciao, puoi presentarti?"
    - [ ] Invia messaggio
    - [ ] ⏱️ **Tempo risposta: < 5 secondi**
    - [ ] Verifica risposta coerente

3. **Test RAG - Ricerca Atto Specifico**

    - [ ] Scrivi: "Cerca l'atto con protocollo TEST-2023-001"
    - [ ] Invia messaggio
    - [ ] Verifica risposta contiene:
        - [ ] Protocollo corretto
        - [ ] Tipo atto
        - [ ] Data
        - [ ] Titolo
    - [ ] ⚠️ **Se non trova: verificare RagService**

4. **Test RAG - Query Semantica**

    - [ ] Scrivi: "Quanti atti ho caricato oggi?"
    - [ ] Verifica risposta con numero corretto
    - [ ] Scrivi: "Mostrami gli ultimi 3 atti"
    - [ ] Verifica lista atti coerente

5. **Test RAG - Riassunto**

    - [ ] Scrivi: "Riassumi l'atto TEST-2023-001"
    - [ ] Verifica riassunto coerente con contenuto
    - [ ] ⏱️ **Tempo risposta: < 10 secondi**

6. **Test GDPR - Data Sanitization**

    - [ ] Controlla log:
        ```bash
        tail -100 storage/logs/laravel.log | grep DataSanitizerService
        ```
    - [ ] Verifica nessun PII inviato ad API
    - [ ] Verifica solo metadati pubblici

7. **Test Conversazione Multi-Turn**

    - [ ] Scrivi: "Quali sono i tipi di atto più comuni?"
    - [ ] Poi: "E qual è il più recente?"
    - [ ] Verifica context maintained tra messaggi

8. **Test Suggested Questions**
    - [ ] Vai su `/pa/natan/chat/suggestions`
    - [ ] Verifica API ritorna array di suggestions
    - [ ] Verifica suggestions rilevanti per PA

#### Risultato Atteso

-   ✅ Chat risponde in < 5 sec
-   ✅ RAG recupera atti corretti
-   ✅ Risposte contestualmente rilevanti
-   ✅ Nessun PII leakage (audit log pulito)

#### Troubleshooting

```bash
# Test Anthropic service
php artisan tinker
$service = app(\App\Services\AnthropicService::class);
$service->isAvailable(); // true

# Test RAG service
$rag = app(\App\Services\RagService::class);
$user = \App\Models\User::where('email', 'pa@test.com')->first();
$context = $rag->getContextForQuery('test', $user);
dd($context);
```

---

### ✅ TEST 3: Verifica Pubblica - Trust-Minimized

**Obiettivo:** Cittadini possono verificare autenticità atto

#### Steps

1. **Ottieni Public Code**

    - [ ] Vai su `/pa/acts/{id}`
    - [ ] Copia public code (es. `VER-ABC123XYZ`)
    - [ ] Oppure scannerizza QR code

2. **Logout (Importante!)**

    - [ ] Fai logout completo
    - [ ] Verifica non autenticato

3. **Accedi Pagina Verifica Pubblica**

    - [ ] Vai su `/verify/VER-ABC123XYZ` (sostituisci con code reale)
    - [ ] ⚠️ **NON deve chiedere login**
    - [ ] Pagina deve caricare senza auth

4. **Verifica UI Pubblica**

    - [ ] Header professionale
    - [ ] Logo FlorenceEGI + N.A.T.A.N.
    - [ ] Badge "Certificato Blockchain"
    - [ ] Metadati atto visibili:
        - [ ] Protocollo
        - [ ] Data
        - [ ] Tipo atto
        - [ ] Titolo
        - [ ] Ente emittente
    - [ ] Sezione Blockchain:
        - [ ] Hash documento (truncato + copy button)
        - [ ] TXID con link AlgoExplorer
        - [ ] Timestamp anchoring
        - [ ] Badge checkmark verde

5. **Test Copy-to-Clipboard**

    - [ ] Clicca su hash documento
    - [ ] Verifica clipboard contiene hash completo
    - [ ] Clicca su TXID
    - [ ] Verifica clipboard contiene TXID

6. **Test Link AlgoExplorer**

    - [ ] Clicca link TXID
    - [ ] Verifica apre nuova tab
    - [ ] Verifica transazione visibile su AlgoExplorer
    - [ ] ⚠️ **Se 404: link errato o TXID invalido**

7. **Test QR Code**

    - [ ] Scannerizza QR code con smartphone
    - [ ] Verifica redirect a pagina verifica
    - [ ] Verifica pagina mobile-responsive

8. **Test 404 - Code Inesistente**
    - [ ] Vai su `/verify/VER-FAKE12345`
    - [ ] Verifica 404 user-friendly
    - [ ] Messaggio: "Atto non trovato o codice non valido"

#### Risultato Atteso

-   ✅ Verifica funziona senza login
-   ✅ UI professionale e chiara
-   ✅ Tutti i dati pubblici visibili
-   ✅ Link blockchain funzionante
-   ✅ QR code scannerizzabile

---

### ✅ TEST 4: Dashboard PA - KPI & Statistics

**Obiettivo:** Dashboard mostra dati corretti in tempo reale

#### Steps

1. **Accedi Dashboard**

    - [ ] Login PA Entity
    - [ ] Vai su `/pa/dashboard`

2. **Verifica KPI Cards**

    - [ ] Card "Totale Atti": numero corretto
    - [ ] Card "Atti Tokenizzati": numero corretto
    - [ ] Card "In Attesa Tokenizzazione": numero corretto
    - [ ] Card "Success Rate": percentuale calcolata correttamente

3. **Test Real-Time Update**

    ```php
    // In tinker, simula tokenizzazione
    $egi = \App\Models\Egi::where('pa_anchored_at', null)->first();
    $egi->pa_anchored_at = now();
    $egi->pa_txid = 'TEST_TXID_' . uniqid();
    $egi->save();
    ```

    - [ ] Refresh dashboard
    - [ ] Verifica KPI aggiornati
    - [ ] Card "Tokenizzati" incrementata
    - [ ] Card "In Attesa" decrementata

4. **Verifica Lista Atti Recenti**

    - [ ] Sezione "Ultimi Atti" presente
    - [ ] Mostra almeno 5 atti più recenti
    - [ ] Ogni atto mostra:
        - [ ] Protocollo
        - [ ] Tipo
        - [ ] Data
        - [ ] Badge status tokenizzazione

5. **Test Quick Actions**

    - [ ] Button "Carica Nuovo Atto" presente
    - [ ] Clicca button
    - [ ] Verifica redirect a `/pa/acts/upload`

6. **Test Statistiche Dettagliate**
    - [ ] Vai su `/pa/acts/statistics`
    - [ ] Verifica grafici presenti (se implementati)
    - [ ] Tabella breakdown per tipo atto
    - [ ] Export CSV funzionante (se implementato)

#### Risultato Atteso

-   ✅ Dashboard carica in < 2 sec
-   ✅ KPI accurati
-   ✅ Quick actions funzionanti
-   ✅ UI pulita e professionale

---

### ✅ TEST 5: Batch Processing System

**Obiettivo:** Caricamento massivo via agent esterno

#### Setup

```bash
# Genera API key per PA
php artisan pa:generate-api-key --user=pa@test.com
# Copia API key output
```

#### Steps

1. **Crea Batch Source**

    - [ ] Login PA Entity
    - [ ] Vai su `/pa/batch`
    - [ ] Clicca "Nuova Source"
    - [ ] Compila form:
        - [ ] Nome: "Test Batch 2023"
        - [ ] Path: "/tmp/pa_docs_test"
        - [ ] Pattern: "\*.pdf.p7m"
        - [ ] Auto-process: ON
    - [ ] Salva

2. **Verifica Source Creata**

    - [ ] Verifica source nella lista
    - [ ] Status: "Active"
    - [ ] Stats: 0 total, 0 processed

3. **Test API Metadata Submission**

    ```bash
    # Test con curl
    curl -X POST http://localhost/api/pa/acts/metadata \
      -H "Authorization: Bearer YOUR_API_KEY" \
      -H "Content-Type: application/json" \
      -d '{
        "source_id": 1,
        "file_name": "atto_test_001.pdf.p7m",
        "file_hash": "abc123...def456",
        "file_size": 524288,
        "metadata": {
          "protocol_number": "BATCH-001",
          "protocol_date": "2023-10-23",
          "doc_type": "Determina Dirigenziale",
          "title": "Test Batch Upload",
          "description": "Test caricamento via API"
        }
      }'
    ```

    - [ ] Response 201 Created
    - [ ] Response contiene `job_id`

4. **Verifica Job Creato**

    ```php
    $job = \App\Models\PaBatchJob::latest()->first();
    $job->status; // 'pending' o 'processing'
    $job->file_name; // 'atto_test_001.pdf.p7m'
    $job->file_hash; // 'abc123...def456'
    ```

5. **Simula Processing Job**

    ```bash
    # Trigger manuale job processor
    php artisan queue:work --once
    ```

    - [ ] Job passa a 'processing'
    - [ ] EGI creato
    - [ ] Job passa a 'completed'

6. **Verifica Dashboard Batch**

    - [ ] Vai su `/pa/batch/{source_id}`
    - [ ] Verifica jobs list
    - [ ] Job con status 'completed' visibile
    - [ ] Link a EGI creato funzionante

7. **Test Error Handling**

    - [ ] Invia job con hash duplicato
    - [ ] Verifica job status 'duplicate'
    - [ ] Invia job con dati invalidi
    - [ ] Verifica job status 'failed'
    - [ ] Verifica error_code popolato

8. **Test Retry Logic**
    ```php
    $job = \App\Models\PaBatchJob::failed()->first();
    $job->canRetry(); // true se attempts < max_attempts
    ```
    - [ ] Marca job come failed
    - [ ] Verifica retry counter incrementato
    - [ ] Dopo max_attempts, status permanentemente 'failed'

#### Risultato Atteso

-   ✅ API autenticazione funzionante
-   ✅ Job creation via API
-   ✅ Processing automatico
-   ✅ Error handling robusto
-   ✅ Dashboard monitoring chiaro

---

## 🟡 TEST PRIORITÀ ALTA

**Tempo stimato:** 1 giorno

### TEST 6: AI Parsing Accuracy

**Obiettivo:** Validare accuracy estrazione metadati

#### Setup

```bash
# Prepara 10 atti reali diversi
ls storage/testing/real_signed_pa_acts/*.pdf
```

#### Steps

1. **Carica 10 Atti Reali**

    - [ ] Usa upload manuale o batch API
    - [ ] Ogni atto con tipo diverso:
        - [ ] Determina Dirigenziale
        - [ ] Delibera Giunta
        - [ ] Ordinanza
        - [ ] Decreto
        - [ ] Atto Generico

2. **Verifica Metadati Estratti**
   Per ciascun atto:

    ```php
    $egi = \App\Models\Egi::find($id);
    $metadata = $egi->metadata;

    // Verifica campi estratti da AI
    $metadata['ai_extracted_protocol'];
    $metadata['ai_extracted_date'];
    $metadata['ai_extracted_type'];
    $metadata['ai_extracted_object'];
    $metadata['ai_confidence_score'];
    ```

3. **Calcola Accuracy**

    ```
    Accuracy = (Campi Corretti / Totale Campi) * 100

    Target: > 95%
    Acceptable: > 90%
    Critical: < 85%
    ```

4. **Identifica Pattern Fallimenti**

    - [ ] Quali tipi atto danno errori?
    - [ ] Quali campi sono più problematici?
    - [ ] OCR quality impact?

5. **Test Confidence Score**
    - [ ] Verifica confidence_score presente
    - [ ] Se score < 0.8, flagga per review manuale
    - [ ] UI mostra warning per low confidence

#### Risultato Atteso

-   ✅ Accuracy > 95% su atti standard
-   ✅ Confidence score reliable
-   ✅ Pattern fallimenti identificati

---

### TEST 7: Performance & Scalability

**Obiettivo:** Sistema performante con carico

#### Steps

1. **Baseline Single Upload**

    - [ ] Tempo upload: \_\_ sec
    - [ ] Tempo tokenizzazione: \_\_ sec
    - [ ] Total end-to-end: \_\_ sec
    - [ ] Target: < 30 sec totali

2. **Test Batch 10 Atti**

    ```bash
    # Misura tempo
    time php artisan pa:batch-process --source=1
    ```

    - [ ] Tempo totale: \_\_ sec
    - [ ] Tempo medio per atto: \_\_ sec
    - [ ] Target: < 20 sec/atto in batch

3. **Test Concurrent Uploads**

    - [ ] Apri 3 browser tabs
    - [ ] Upload simultaneo 3 atti
    - [ ] Verifica nessun conflict
    - [ ] Queue gestisce correttamente

4. **Test Database Performance**

    ```bash
    # Genera 1000 atti mock
    php artisan db:seed --class=PAActsStressSeeder

    # Misura query dashboard
    php artisan tinker
    \DB::enableQueryLog();
    // Vai su /pa/dashboard
    \DB::getQueryLog();
    ```

    - [ ] Query count: < 15 per page load
    - [ ] Slow queries: 0 (> 100ms)

5. **Test Memory Usage**
    ```bash
    # Monitor durante batch
    php artisan queue:work --memory=256
    ```
    - [ ] Memory peak: < 256 MB
    - [ ] No memory leaks

#### Risultato Atteso

-   ✅ End-to-end < 30 sec
-   ✅ Batch processing efficiente
-   ✅ No bottleneck query
-   ✅ Memory usage stabile

---

### TEST 8: Security & GDPR Compliance

**Obiettivo:** Sistema sicuro e GDPR-compliant

#### Steps

1. **Test Authorization**

    - [ ] PA Entity A non vede atti di PA Entity B
    - [ ] Creator non può accedere `/pa/*` routes
    - [ ] Guest redirect a login su `/pa/*`

2. **Test API Authentication**

    ```bash
    # Senza API key
    curl -X POST http://localhost/api/pa/acts/metadata
    # Expect: 401 Unauthorized

    # Con API key sbagliata
    curl -X POST http://localhost/api/pa/acts/metadata \
      -H "Authorization: Bearer FAKE_KEY"
    # Expect: 401 Unauthorized
    ```

3. **Test GDPR - Data Sanitization**

    - [ ] Controlla log AI requests:
        ```bash
        grep "AnthropicService" storage/logs/laravel.log | grep "SENT"
        ```
    - [ ] Verifica nessun campo sensibile:
        - [ ] No file_path
        - [ ] No firme digitali
        - [ ] No IP utente
        - [ ] No certificati X.509

4. **Test GDPR - Audit Trail**

    ```php
    // Verifica ULM logging
    $logs = \Ultra\UltraLogManager\Models\UltraLog::where('action', 'PA_ACT_UPLOAD')->get();
    ```

    - [ ] Ogni upload loggato
    - [ ] Ogni AI request loggato
    - [ ] Ogni blockchain anchor loggato

5. **Test XSS Protection**

    - [ ] Carica atto con titolo: `<script>alert('XSS')</script>`
    - [ ] Verifica escaped in UI
    - [ ] No script execution

6. **Test SQL Injection**

    - [ ] Search con input: `' OR 1=1 --`
    - [ ] Verifica parametrizzazione query
    - [ ] No SQL error

7. **Test CSRF Protection**
    - [ ] Form upload senza CSRF token
    - [ ] Expect: 419 Page Expired

#### Risultato Atteso

-   ✅ Authorization robust
-   ✅ API authentication secure
-   ✅ GDPR audit trail completo
-   ✅ XSS/SQL injection protected

---

## 🟢 TEST PRIORITÀ MEDIA

**Tempo stimato:** 1 giorno

### TEST 9: UI/UX - User Experience

#### Steps

1. **Test Responsive Design**

    - [ ] Desktop 1920x1080: layout OK
    - [ ] Tablet 768x1024: layout OK
    - [ ] Mobile 375x667: layout OK
    - [ ] Sidebar collapsible su mobile

2. **Test Accessibility WCAG 2.1 AA**

    ```bash
    # Usa Chrome Lighthouse
    # Target score: > 90
    ```

    - [ ] Contrast ratio OK
    - [ ] Alt text images
    - [ ] Keyboard navigation
    - [ ] Screen reader friendly

3. **Test Loading States**

    - [ ] Upload: spinner durante processing
    - [ ] Chat: "Sto pensando..." durante AI
    - [ ] Dashboard: skeleton loaders

4. **Test Error Messages**

    - [ ] Upload file troppo grande: errore chiaro
    - [ ] Firma invalida: messaggio dettagliato
    - [ ] Blockchain fail: fallback graceful

5. **Test Tooltips & Help**
    - [ ] Tooltips su icone
    - [ ] Help text su campi complessi
    - [ ] Link documentazione contestuale

#### Risultato Atteso

-   ✅ UI responsive su tutti i device
-   ✅ Accessibility score > 90
-   ✅ UX fluida e intuitiva

---

### TEST 10: Integrazioni Esterne

#### Steps

1. **Test Algorand AlgoExplorer**

    - [ ] Link testnet funzionante
    - [ ] Preparazione switch mainnet
    - [ ] Verifica costi gas

2. **Test Anthropic API**

    - [ ] API quota check
    - [ ] Rate limiting handled
    - [ ] Fallback a Ollama se quota exceeded

3. **Test Email Notifications (se implementato)**
    - [ ] Email tokenizzazione completata
    - [ ] Email batch summary
    - [ ] Email error alerts

#### Risultato Atteso

-   ✅ Integrazioni stabili
-   ✅ Error handling robusto
-   ✅ Monitoring alert funzionanti

---

## 🐛 FIX BUGS IDENTIFICATI

**Da compilare durante testing**

### Bug Critici

-   [ ] Bug #1: ********\_********
-   [ ] Bug #2: ********\_********

### Bug Alta Priorità

-   [ ] Bug #3: ********\_********
-   [ ] Bug #4: ********\_********

### Bug Media Priorità

-   [ ] Bug #5: ********\_********

---

## 🎬 PREPARAZIONE DEMO

**Tempo:** 4 ore

### Demo Script (15 minuti)

**SLIDE 1: Overview (2 min)**

-   [ ] Problema: atti PA cartacei, scarsa trasparenza
-   [ ] Soluzione: N.A.T.A.N. AI + Blockchain
-   [ ] Value proposition: trust, efficiency, transparency

**SLIDE 2: Architecture (2 min)**

-   [ ] Diagramma stack tecnologico
-   [ ] AI: Anthropic Claude 3.5 Sonnet
-   [ ] Blockchain: Algorand
-   [ ] GDPR-compliant by design

**DEMO LIVE: Upload Atto (3 min)**

-   [ ] Login PA dashboard
-   [ ] Upload PDF firmato
-   [ ] Mostra validazione firma automatica
-   [ ] AI estrae metadati (real-time)
-   [ ] Tokenizzazione blockchain
-   [ ] Mostra TXID su AlgoExplorer

**DEMO LIVE: Chat N.A.T.A.N. (3 min)**

-   [ ] "Ciao N.A.T.A.N., mostrami gli ultimi atti"
-   [ ] "Riassumi la determina X"
-   [ ] "Quanti atti ho sul tema ambiente?"
-   [ ] Mostra context-aware responses

**DEMO LIVE: Verifica Pubblica (2 min)**

-   [ ] Logout dashboard
-   [ ] Scannerizza QR code con smartphone
-   [ ] Mostra pagina verifica pubblica
-   [ ] Clicca link AlgoExplorer
-   [ ] Mostra immutabilità blockchain

**DEMO LIVE: Batch Processing (2 min)**

-   [ ] Mostra dashboard batch
-   [ ] Simula caricamento massivo via API
-   [ ] Monitoring job real-time
-   [ ] Statistics processing

**SLIDE 3: Roadmap (1 min)**

-   [ ] Fase 1: Pilot Firenze (8 settimane)
-   [ ] Fase 2: Scaling 10 comuni
-   [ ] Fase 3: Network ANCI (50+ comuni)

### Dati Demo Preparati

-   [ ] 20+ atti caricati
-   [ ] Tipologie varie (determine, delibere, ordinanze)
-   [ ] Almeno 15 tokenizzati su blockchain
-   [ ] 5 atti in attesa (per mostrare batch)
-   [ ] Chat history con query interessanti

### Environment Setup

```bash
# Backup database prima demo
php artisan db:backup

# Clear cache
php artisan cache:clear
php artisan config:clear

# Restart queue workers
supervisorctl restart laravel-worker:*

# Check logs clean
> storage/logs/laravel.log
```

---

## ✅ CHECKLIST PRE-PRESENTAZIONE

**Da completare 1 giorno prima**

### Tecnico

-   [ ] Tutti i test critici passano
-   [ ] Zero bug critici aperti
-   [ ] Performance baseline documentata
-   [ ] Backup database fatto
-   [ ] Environment production-ready

### Demo

-   [ ] Script demo provato 3+ volte
-   [ ] Dati demo caricati e verificati
-   [ ] Fallback plan se live demo fail
-   [ ] Video backup registrato

### Documentazione

-   [ ] README aggiornato
-   [ ] API documentation pubblicata
-   [ ] User manual PA draft pronto
-   [ ] GDPR compliance 1-pager

### Business

-   [ ] Slide deck finalized
-   [ ] Pricing model chiaro
-   [ ] Case study Firenze preparato
-   [ ] ROI calculation presentabile

### Contingency

-   [ ] Piano B se internet fail
-   [ ] Piano B se AlgoExplorer down
-   [ ] Piano B se API Anthropic down
-   [ ] Demo video backup ready

---

## 📊 METRICHE SUCCESS

### Metriche Tecniche

-   ✅ Uptime: 99.9%
-   ✅ Response time upload: < 5 sec
-   ✅ Response time AI chat: < 5 sec
-   ✅ Blockchain anchoring: < 30 sec
-   ✅ AI accuracy: > 95%

### Metriche Business

-   ✅ Demo fluida senza errori
-   ✅ Wow factor chat AI
-   ✅ Trust factor blockchain verification
-   ✅ Scalability evidente (batch)
-   ✅ GDPR compliance chiara

---

## 📝 LOG TESTING

**Da compilare giorno per giorno**

### Giorno 1: **_/_**/2025

-   [ ] Test 1-5 completati
-   [ ] Bug identificati: \_\_\_
-   [ ] Fix completati: \_\_\_
-   [ ] Blocker: \_\_\_

### Giorno 2: **_/_**/2025

-   [ ] Test 6-8 completati
-   [ ] Bug identificati: \_\_\_
-   [ ] Fix completati: \_\_\_
-   [ ] Blocker: \_\_\_

### Giorno 3: **_/_**/2025

-   [ ] Test 9-10 completati
-   [ ] Demo preparazione
-   [ ] Rehearsal #1
-   [ ] Blocker: \_\_\_

### Giorno 4: **_/_**/2025

-   [ ] Final fixes
-   [ ] Rehearsal #2
-   [ ] Backup & deployment
-   [ ] Ready per presentazione: ✅

---

**Fine Checklist Testing** - Buon lavoro! 🚀
