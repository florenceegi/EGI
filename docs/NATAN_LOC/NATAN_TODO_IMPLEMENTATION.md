# N.A.T.A.N. - TODO Implementazione Completa

## Roadmap Funzionalità da Implementare

**Documento:** Analisi completa funzionalità N.A.T.A.N. da `pa-entity.blade.php`  
**Data:** 2025-10-09  
**Autore:** Padmin D. Curtis (AI Partner OS3.0) per Fabio Cherici  
**Versione:** 1.0.0

---

## 📊 STATO ATTUALE IMPLEMENTAZIONE

### ✅ COMPLETATO (Fase 1 - Core Tokenization)

-   [x] Upload PDF firmato con validazione firma digitale
-   [x] Calcolo hash SHA-256 documento
-   [x] Salvataggio in database (tabella `egis` con colonne `pa_*`)
-   [x] Blockchain anchoring su Algorand (via AlgoKit microservice)
-   [x] Job asincrono `TokenizePaActJob` per tokenizzazione
-   [x] Dashboard `/pa/acts` con lista atti
-   [x] Visualizzazione stato tokenizzazione (badge "Tokenizzato" / "In Attesa")
-   [x] Statistiche base (Total, Anchored, Pending)
-   [x] QR Code generation (base)
-   [x] Public verification code (VER-XXXXXXXXXX)

---

## 📋 TODO ORGANIZZATO PER CATEGORIA

---

## 🔒 CATEGORIA A: CERTEZZA E TRASPARENZA

### 1. Garantire l'Autenticità ✅ PARZIALE

**Descrizione:** QR Code permette verifica autenticità documento in 1 click

**Implementato:**

-   [x] Hash crittografico generato
-   [x] Registrazione blockchain Algorand
-   [x] QR Code base generato

**TODO:**

-   [ ] **Pagina pubblica verifica** (`/verify/{public_code}`)
    -   [ ] Mostra metadata atto (tipo, oggetto, data, ente)
    -   [ ] Mostra hash documento
    -   [ ] Mostra TXID blockchain con link AlgoExplorer
    -   [ ] Verifica hash: confronto con PDF scaricabile
    -   [ ] Design responsive + WCAG 2.1 AA
    -   [ ] SEO optimization per visibilità pubblica
-   [ ] **QR Code Enhancement**
    -   [ ] QR Code SVG (not base64 image) per migliore qualità stampa
    -   [ ] Download QR in formati: SVG, PNG (300dpi), PDF stampabile
    -   [ ] Watermark con logo Florence EGI + N.A.T.A.N.
-   [ ] **Link Algorand Explorer**
    -   [ ] Collegamento diretto da pagina verifica a AlgoExplorer
    -   [ ] Supporto testnet + mainnet (auto-detect da network)

**Priorità:** 🔥 ALTA  
**Effort:** 3 giorni  
**Dependencies:** Nessuna

---

### 2. Rendere la Trasparenza "Visibile" ⚠️ TODO

**Descrizione:** Scheda certificazione pubblica per ogni atto con QR Code

**TODO:**

-   [ ] **Public Act Card** (`/public/act/{public_code}`)
    -   [ ] Header: Logo Ente + Titolo "Atto Certificato N.A.T.A.N."
    -   [ ] Metadata Display:
        -   [ ] Tipo atto con badge colorato
        -   [ ] Numero protocollo + data
        -   [ ] Oggetto (descrizione completa)
        -   [ ] Ente emittente
        -   [ ] Direzione/Ufficio
        -   [ ] Responsabile procedimento
        -   [ ] Importo (se presente)
    -   [ ] Blockchain Verification Section:
        -   [ ] Hash documento (truncato + copy-to-clipboard)
        -   [ ] TXID Algorand con link explorer
        -   [ ] Timestamp anchoring
        -   [ ] Badge "Certificato Blockchain" con checkmark verde
    -   [ ] QR Code stampabile (download PDF)
    -   [ ] Download PDF originale (con watermark "Copia certificata N.A.T.A.N.")
    -   [ ] Footer: "Questo documento è certificato immutabile su blockchain Algorand"
-   [ ] **Embedding per Albo Pretorio**
    -   [ ] Widget iframe embeddable: `<iframe src="https://florenceegi.com/verify/VER-XXX" />`
    -   [ ] Badge "Certificato N.A.T.A.N." da inserire in pagine Albo Pretorio
    -   [ ] Shortcode per CMS comuni (WordPress, Drupal)

**Priorità:** 🔥 ALTA  
**Effort:** 4 giorni  
**Dependencies:** Pagina verifica pubblica (TODO #1)

---

### 3. Trovare l'Atto Giusto, Subito ✅ PARZIALE

**Descrizione:** Ricerca full-text e filtri avanzati per trovare atti in secondi

**Implementato:**

-   [x] Filtri base: tipo atto, date range, protocol search
-   [x] Ordinamento per data protocollo

**TODO:**

-   [ ] **Ricerca Semantica AI**
    -   [ ] Full-text search su `oggetto` (MySQL FULLTEXT index)
    -   [ ] Ricerca su responsabile, direzione, ente
    -   [ ] Ricerca per range importo (min/max)
    -   [ ] Ricerca per categoria tematica (urbanistica, bilancio, personale)
-   [ ] **Advanced Filters Panel**
    -   [ ] Multi-select tipo atto (Delibera + Determina simultanei)
    -   [ ] Slider range importo con labels dinamici
    -   [ ] Filtro per stato: Tokenizzato / Pending / Failed
    -   [ ] Filtro per periodo: Oggi / Settimana / Mese / Anno / Custom
    -   [ ] Filtro responsabile (dropdown con autocomplete)
    -   [ ] Filtro direzione/ufficio
-   [ ] **Search Performance**
    -   [ ] Implementare Algolia/Meilisearch per ricerca ultra-rapida (optional)
    -   [ ] Cache risultati ricerche frequenti (Redis)
    -   [ ] Infinite scroll o lazy loading (performance con migliaia di atti)
-   [ ] **Saved Searches**
    -   [ ] Salva combinazioni filtri preferite ("Delibere 2025 >50k€")
    -   [ ] Quick filters panel con ricerche salvate
    -   [ ] Export search results in CSV/Excel

**Priorità:** 🔥 ALTA  
**Effort:** 5 giorni  
**Dependencies:** AI Parsing per categorie tematiche

---

### 4. Gestire Rettifiche e Revoche ❌ TODO

**Descrizione:** Versioning automatico atti con grafo relazioni

**TODO:**

-   [ ] **Version Control System**
    -   [ ] Campo `parent_act_id` in tabella `egis` (self-referencing FK)
    -   [ ] Campo `version_number` (integer, auto-increment per stesso atto)
    -   [ ] Campo `version_type` ENUM: `original`, `correction`, `revoked`, `superseded`
    -   [ ] Timestamp `superseded_at` quando nuova versione pubblicata
-   [ ] **Upload Form Enhancement**
    -   [ ] Checkbox "Questo è una rettifica di atto precedente"
    -   [ ] Select dropdown per selezionare atto da rettificare (search protocol number)
    -   [ ] Campo "Motivo rettifica" (textarea obbligatorio)
    -   [ ] Auto-link parent → child relationship
-   [ ] **Version History Display**
    -   [ ] Timeline verticale con tutte le versioni
    -   [ ] Mostra diff tra versioni (metadata changes)
    -   [ ] Ogni versione ha proprio QR + blockchain TXID
    -   [ ] Badge "CORRETTA DA V2" su versioni superate
    -   [ ] Badge "REVOCATA" su atti annullati
-   [ ] **Public Verification Page Update**
    -   [ ] Se atto revocato → mostra ALERT ROSSO "ATTO REVOCATO - Non più valido"
    -   [ ] Link a versione corrente (se esiste)
    -   [ ] Mostra motivo revoca e data
    -   [ ] Storico completo versioni accessibile

**Priorità:** 🟡 MEDIA  
**Effort:** 6 giorni  
**Dependencies:** Database schema migration, Public verification page

---

### 5. Tracciare le Copie Ufficiali ❌ TODO

**Descrizione:** Sistema di copie certificate numerate con tracking stato

**TODO:**

-   [ ] **Certified Copies System**
    -   [ ] Tabella `pa_act_certified_copies`:
        -   [ ] `id`, `egi_id`, `copy_number`, `issued_to_name`, `issued_to_cf`
        -   [ ] `issued_at`, `issued_by_user_id`, `purpose`, `status` (valid/revoked)
        -   [ ] `revoked_at`, `revoked_by_user_id`, `revoke_reason`
        -   [ ] `public_code` (univoco per ogni copia: COPY-XXXXXXXXXX)
-   [ ] **Issue Copy Flow**
    -   [ ] Pulsante "Rilascia Copia Certificata" in act detail
    -   [ ] Form: destinatario (nome, CF), motivo richiesta
    -   [ ] Generazione PDF con watermark: "COPIA CERTIFICATA N. XXX rilasciata a [Nome] il [Data]"
    -   [ ] QR Code specifico della copia (link a `/verify/copy/COPY-XXX`)
    -   [ ] Numerazione progressiva automatica per atto
-   [ ] **Copy Verification Page**
    -   [ ] Mostra: atto originale + info copia (numero, destinatario, data rilascio)
    -   [ ] Badge "COPIA VALIDA" / "COPIA REVOCATA"
    -   [ ] Se revocata: motivo e data revoca in rosso
    -   [ ] Link a atto madre blockchain
-   [ ] **Revoke Copy Flow**
    -   [ ] Dashboard "Copie Rilasciate" con lista tutte copie
    -   [ ] Pulsante "Revoca Copia" con modal conferma
    -   [ ] Form: motivo revoca (obbligatorio)
    -   [ ] Update status → revoked + timestamp
    -   [ ] Email notifica a destinatario (optional)
-   [ ] **Bulk Copy Management**
    -   [ ] Export lista copie in CSV
    -   [ ] Filtri: status (valide/revocate), periodo, destinatario
    -   [ ] Statistiche: copie rilasciate per atto, top destinatari

**Priorità:** 🟡 MEDIA  
**Effort:** 7 giorni  
**Dependencies:** Public verification system, PDF generation service

---

## ⚡ CATEGORIA B: EFFICIENZA OPERATIVA

### 1. Ottimizzare il Tempo Operativo ✅ PARZIALE

**Descrizione:** Automazione completa: upload → AI parsing → blockchain → QR in 10-15 sec

**Implementato:**

-   [x] Upload automatico
-   [x] Blockchain anchoring automatico (10-15 sec)
-   [x] QR Code generazione

**TODO:**

-   [ ] **AI Document Parsing** (CORE N.A.T.A.N.)

    -   [ ] Installare Smalot/PdfParser: `composer require smalot/pdfparser`
    -   [ ] Service `DocumentParserService`:

        -   [ ] `extractText(filePath): string` - Estrae testo da PDF
        -   [ ] `extractP7M(filePath): string` - Gestisce PDF firmati P7M
        -   [ ] `cleanText(text): string` - Normalizza testo estratto

    -   [ ] Service `AIAnalyzerService` (Anthropic Claude API):

        -   [ ] Setup API client: `composer require guzzlehttp/guzzle` (se non già presente)
        -   [ ] Configurazione `.env`: `ANTHROPIC_API_KEY`, `ANTHROPIC_MODEL=claude-3-5-sonnet-20241022`
        -   [ ] `analyzeDocument(text): array` - Chiama API Claude
        -   [ ] System prompt per atti PA italiani (vedi guida implementazione)
        -   [ ] JSON schema validation output
        -   [ ] Token usage tracking per cost control
        -   [ ] Error handling: rate limits, API failures, retry logic

    -   [ ] Service `MetadataExtractorService` (Orchestrator):

        -   [ ] `extract(filePath): array` - Coordina Parser → AI → Validation
        -   [ ] Validazione campi obbligatori (tipo_atto, data_atto, oggetto)
        -   [ ] Enrichment metadata: document_id, timestamp, file_info
        -   [ ] Normalizzazione date (ISO 8601)

    -   [ ] **Integration con Upload Flow:**

        -   [ ] Checkbox "Abilita Analisi AI N.A.T.A.N." nel form upload
        -   [ ] Se checked: dispatch `AnalyzePaActJob` PRIMA di `TokenizePaActJob`
        -   [ ] `AnalyzePaActJob` popola metadata AI-extracted
        -   [ ] Auto-fill campi form con metadata estratti (UX migliore)

    -   [ ] **Accuracy Validation:**
        -   [ ] Test su 50 atti reali Comune Firenze
        -   [ ] Target: >95% accuracy su campi chiave
        -   [ ] Manual review workflow per atti con confidence <90%
        -   [ ] Dashboard accuracy metrics per monitoraggio

**Priorità:** 🔥 CRITICA (Core N.A.T.A.N.)  
**Effort:** 7-10 giorni  
**Dependencies:** Anthropic API key, test documents Firenze

---

### 2. Partire Subito con Poche Risorse ✅ FATTO

**Descrizione:** Setup 48h, costi pilot €200-400, zero hardware

**Implementato:**

-   [x] Setup account PA rapido
-   [x] Zero hardware necessario (cloud-based)
-   [x] Costi minimi (sandbox free, API AI pay-per-use)

**Già operativo - Nessuna azione richiesta.**

---

### 3. Classificare e Organizzare Automaticamente ⚠️ TODO

**Descrizione:** Archivio semantico con categorie tematiche estratte da AI

**TODO:**

-   [ ] **Semantic Categorization**
    -   [ ] AI extraction categoria tematica: urbanistica, bilancio, personale, appalti, etc.
    -   [ ] Tabella `pa_act_categories`: `id`, `name`, `slug`, `description`, `color`
    -   [ ] Tabella pivot `pa_act_category`: `egi_id`, `category_id` (many-to-many)
    -   [ ] Auto-tagging durante AI parsing
-   [ ] **Advanced Search**
    -   [ ] Filtro per categoria tematica (multi-select)
    -   [ ] Query: "tutte determine dirigenziali su appalti verdi ultimi 3 anni"
    -   [ ] Suggerimenti ricerca intelligenti (autocomplete semantico)
    -   [ ] Related acts: "atti simili" basati su categoria + oggetto
-   [ ] **Auto-Organization**
    -   [ ] Creazione automatica "Fascicoli" (Collections) per tema
    -   [ ] Suggerimento fascicolo durante upload basato su categoria AI
    -   [ ] Bulk move acts tra fascicoli
    -   [ ] Fascicoli smart: query dinamiche salvate (es: "Tutti atti urbanistica 2025")

**Priorità:** 🟡 MEDIA-ALTA  
**Effort:** 5 giorni  
**Dependencies:** AI Parsing implementato

---

### 4. Rispondere Rapidamente ad Accessi FOIA ⚠️ TODO

**Descrizione:** Export massivo atti in PDF zip per risposte FOIA veloci

**TODO:**

-   [ ] **FOIA Export System**
    -   [ ] Checkbox multi-select in acts index
    -   [ ] Pulsante "Export Selezionati" → modal scelta formato
    -   [ ] Formati supportati:
        -   [ ] PDF Zip (tutti gli atti originali)
        -   [ ] CSV metadata (protocol, tipo, oggetto, responsabile, importo)
        -   [ ] JSON completo (per trasferimenti dati)
        -   [ ] Excel con formattazione (per report dirigenza)
-   [ ] **FOIA-Ready Reports**
    -   [ ] Template report ANAC (con CIG/CUP se presenti)
    -   [ ] Template report Corte dei Conti (tempi pubblicazione)
    -   [ ] Template report Prefettura (atti per categoria)
    -   [ ] Generazione PDF report con grafici e statistiche
-   [ ] **Bulk Actions**
    -   [ ] "Seleziona tutti in pagina"
    -   [ ] "Seleziona tutti matching filtri" (anche oltre pagina corrente)
    -   [ ] Preview count prima di export massivo
    -   [ ] Progress bar per export >100 atti

**Priorità:** 🟡 MEDIA  
**Effort:** 4 giorni  
**Dependencies:** Nessuna (usa dati esistenti)

---

### 5. Onboarding Rapido Nuovo Personale ⚠️ TODO

**Descrizione:** Memoria istituzionale digitale con ricerca semantica storica

**TODO:**

-   [ ] **Historical Knowledge Base**
    -   [ ] Vista "Timeline Storica" atti per anno/mese
    -   [ ] Grafici trend: atti per tipo, per ufficio, per categoria nel tempo
    -   [ ] Dashboard "Panoramica Area": nuovo dirigente seleziona area → vede tutti atti ultimi N anni
-   [ ] **Semantic Search Enhancement**
    -   [ ] Query natural language: "come abbiamo gestito emergenze neve passate?"
    -   [ ] AI-powered search suggestions
    -   [ ] Context-aware results: privilegia atti recenti o atti simili a quello visualizzato
-   [ ] **Knowledge Aggregation**
    -   [ ] Widget "Atti Correlati" in ogni act detail
    -   [ ] Tag cloud interattivo (keyword estratte da AI)
    -   [ ] "Frequently Accessed Acts" dashboard per nuovo personale

**Priorità:** 🟢 BASSA  
**Effort:** 5 giorni  
**Dependencies:** AI Parsing, Semantic categorization

---

## ✅ CATEGORIA C: COMPLIANCE E GOVERNANCE

### 1. Lavorare in Piena Conformità ✅ PARZIALE

**Descrizione:** GDPR-by-design, audit trail completo, dashboard DPO

**Implementato:**

-   [x] GDPR-safe: solo hash su blockchain (no PII)
-   [x] Audit trail base (ULM logging)
-   [x] Data minimization

**TODO:**

-   [ ] **DPO Dashboard**
    -   [ ] Rotta `/pa/compliance/gdpr-dashboard` (role: dpo o admin)
    -   [ ] Visualizzazione tutti log accessi atti PA
    -   [ ] Export audit trail filtrabile (chi, quando, cosa)
    -   [ ] Statistiche compliance:
        -   [ ] % atti con firma valida
        -   [ ] % atti tokenizzati (immutabilità garantita)
        -   [ ] Tempi medi processing (GDPR requires minimization)
-   [ ] **GDPR Documentation Auto-Generation**
    -   [ ] Template DPIA (Data Protection Impact Assessment) per N.A.T.A.N.
    -   [ ] Registro trattamenti automatico (Art. 30 GDPR)
    -   [ ] Export documentazione compliance per Garante Privacy
-   [ ] **Consent Management**
    -   [ ] Informativa privacy PA operators (processing documenti)
    -   [ ] Consenso blockchain operations (users che caricano)
    -   [ ] Log consensi con timestamp (audit trail)

**Priorità:** 🟡 MEDIA  
**Effort:** 4 giorni  
**Dependencies:** Spatie Permission per ruolo DPO

---

### 2. Evitare il "Lock-in" Tecnologico ✅ FATTO

**Descrizione:** Export completo metadata, blockchain pubblica indipendente

**Implementato:**

-   [x] Blockchain pubblica Algorand (indipendente da FlorenceEGI)
-   [x] QR Code validi per sempre
-   [x] Metadata in JSON esportabile

**TODO:**

-   [ ] **Data Portability**
    -   [ ] Export completo DB in formato standard (CSV + JSON)
    -   [ ] Export include: PDF originali + metadata + blockchain TXIDs
    -   [ ] Documentazione formato export per import in altri sistemi
    -   [ ] API pubblica per export programmatico (OAuth 2.0)

**Priorità:** 🟢 BASSA  
**Effort:** 2 giorni  
**Dependencies:** Nessuna

---

### 3. Misurare Efficienza e Risultati ⚠️ TODO

**Descrizione:** Analytics real-time, KPI dashboard, export Excel per report dirigenziali

**TODO:**

-   [ ] **Analytics Dashboard** (`/pa/analytics`)

    -   [ ] KPI Cards:

        -   [ ] Atti pubblicati per periodo (oggi, settimana, mese, anno)
        -   [ ] Tempo medio upload → certificazione (target <30 sec)
        -   [ ] Distribuzione per categoria (pie chart)
        -   [ ] Top responsabili per volume atti (bar chart)
        -   [ ] Trend mensile pubblicazioni (line chart)

    -   [ ] **Charts Interactive (Chart.js o ApexCharts)**

        -   [ ] Atti per tipo (bar chart)
        -   [ ] Atti per direzione/ufficio (donut chart)
        -   [ ] Timeline pubblicazioni (area chart)
        -   [ ] Importi totali per categoria (stacked bar)

    -   [ ] **Export Reports**

        -   [ ] Excel formattato con grafici embedded
        -   [ ] PDF report executive (design PA brand)
        -   [ ] PowerPoint slides auto-generated (per presentazioni Giunta)

    -   [ ] **Comparative Analytics**
        -   [ ] Confronto anno corrente vs anno precedente
        -   [ ] Benchmark inter-ufficio (performance relativa)
        -   [ ] Trend efficiency: tempo medio processing nel tempo

-   [ ] **Real-Time Stats**
    -   [ ] Widget "Atti oggi" in PA dashboard
    -   [ ] Auto-refresh ogni 30 secondi (AJAX)
    -   [ ] Notifiche push per milestone (es: "100° atto tokenizzato!")

**Priorità:** 🔥 ALTA  
**Effort:** 6 giorni  
**Dependencies:** AI Parsing per categorie complete

---

### 4. Dimostrare Compliance a Enti Sovraordinati ⚠️ TODO

**Descrizione:** Report compliance-ready per ANAC, Corte Conti, Prefettura

**TODO:**

-   [ ] **Compliance Report Templates**
    -   [ ] Template ANAC: tutti atti con CIG/CUP, importi, tempi pubblicazione
    -   [ ] Template Corte Conti: cronologia atti finanziari, responsabili, verifiche
    -   [ ] Template Prefettura: overview atti deliberativi, ordinanze, emergenze
-   [ ] **One-Click Report Generation**
    -   [ ] Dashboard `/pa/compliance/reports`
    -   [ ] Select template → Select period → Generate
    -   [ ] Output: PDF formattato + CSV allegato + blockchain proofs
-   [ ] **Audit Trail Export**
    -   [ ] Log completo attività: chi ha caricato cosa, quando
    -   [ ] Filtri: utente, periodo, tipo atto
    -   [ ] Export firmato digitalmente (per validità legale)
-   [ ] **Blockchain Proof Package**
    -   [ ] Zip file con: PDFs + Metadata JSON + Blockchain TXIDs list
    -   [ ] Script verifica Python/Bash per enti terzi
    -   [ ] Documentazione processo verifica indipendente

**Priorità:** 🟡 MEDIA  
**Effort:** 5 giorni  
**Dependencies:** Analytics dashboard, Export system

---

## 📈 CATEGORIA D: INNOVAZIONE ORGANIZZATIVA

### 1. Prevenire Duplicazioni e Conflitti Normativi ❌ TODO

**Descrizione:** Alert automatici per atti simili/contraddittori pre-pubblicazione

**TODO:**

-   [ ] **Duplicate Detection System**
    -   [ ] Durante upload: AI cerca atti esistenti con oggetto simile
    -   [ ] Similarity threshold configurable (default 80%)
    -   [ ] Alert modal: "Attenzione! Trovati 3 atti simili: [lista]"
    -   [ ] Operatore può: procedere / visualizzare atti simili / annullare
-   [ ] **Conflict Detection** (Advanced)
    -   [ ] AI analizza se nuovo atto contraddice atti precedenti su stesso tema
    -   [ ] Esempio: "Delibera chiusura strada X" vs "Delibera riapertura strada X" (stesso anno)
    -   [ ] Warning: "Possibile contraddizione con Delibera GC 123/2025"
    -   [ ] Link diretto a atto potenzialmente conflittuale
-   [ ] **Pre-Publication Review Queue**
    -   [ ] Queue "Atti in revisione" per atti flagged da AI
    -   [ ] Responsabile può: approva / rigetta / richiedi chiarimenti
    -   [ ] Workflow approval multi-step configurabile

**Priorità:** 🟢 BASSA (Feature avanzata)  
**Effort:** 8 giorni  
**Dependencies:** AI Parsing, Semantic search

---

### 2. Facilitare Collaborazione Inter-Ufficio ❌ TODO

**Descrizione:** Dashboard condivisa cross-ufficio con permessi granulari e notifiche

**TODO:**

-   [ ] **Multi-User PA System**
    -   [ ] Tabella `pa_offices`: `id`, `ente_id`, `name`, `slug`, `description`
    -   [ ] User field: `pa_office_id` (FK a pa_offices)
    -   [ ] Ruoli: `pa_admin`, `pa_director`, `pa_operator` (Spatie permissions)
-   [ ] **Cross-Office Visibility**
    -   [ ] Impostazione visibilità atto: `private` (solo ufficio), `shared` (tutto ente), `public`
    -   [ ] Filtro dashboard: "Atti di altri uffici" con permission check
    -   [ ] Tag atti con ufficio emittente
-   [ ] **Custom Filters per Ruolo**
    -   [ ] Ragioneria: auto-filter importo >10k€
    -   [ ] Urbanistica: auto-filter categoria "urbanistica" + "edilizia"
    -   [ ] Configurazione filtri salvati per ruolo/ufficio
-   [ ] **Notification System**
    -   [ ] Preferenze notifiche per utente:
        -   [ ] "Avvisami quando [Ufficio X] pubblica atto su [Categoria Y]"
        -   [ ] "Avvisami per atti con importo > [Soglia]"
    -   [ ] Email digest giornaliero/settimanale
    -   [ ] In-app notifications panel
    -   [ ] Push notifications (optional, via Laravel Echo)

**Priorità:** 🟡 MEDIA  
**Effort:** 7 giorni  
**Dependencies:** Multi-tenancy setup, Notification system

---

### 3. Supportare Decisioni Data-Driven ⚠️ TODO

**Descrizione:** Intelligence operativa con KPI e trend multi-anno

**TODO:**

-   [ ] **Executive Dashboard** (`/pa/executive`)

    -   [ ] KPI Performance:

        -   [ ] Atti per ufficio (ranking produttività)
        -   [ ] Categorie spesa in crescita/decrescita
        -   [ ] Tempi medi delibera per tipo
        -   [ ] Distribuzione responsabili (chi firma più atti)

    -   [ ] **Trend Analysis**

        -   [ ] Grafici multi-anno (2020-2025)
        -   [ ] Seasonality detection (picchi fine anno, agosto vuoto)
        -   [ ] Forecasting: "stima atti Q4 2025 basata su trend"

    -   [ ] **Comparative Metrics**

        -   [ ] Benchmark inter-ufficio: efficienza relativa
        -   [ ] Confronto con periodo precedente (+/- %)
        -   [ ] Goal tracking: "target 500 atti/anno" → progress bar

    -   [ ] **Impact Analysis**
        -   [ ] Mostra impatto riforme organizzative su KPI
        -   [ ] Before/After comparison con data intervention
        -   [ ] ROI calculation: tempo risparmiato × costo orario personale

-   [ ] **Data Export for Decision Makers**
    -   [ ] PowerPoint auto-generated con slide executive summary
    -   [ ] Excel pivot tables pre-configurate
    -   [ ] PDF report "Relazione Attività Amministrativa 2025"

**Priorità:** 🟡 MEDIA  
**Effort:** 8 giorni  
**Dependencies:** Analytics dashboard, AI categorization

---

## 🛠️ FUNZIONALITÀ AGGIUNTIVE (DA ARCHETYPE)

### Upload via Email Dedicata ❌ TODO

**Descrizione:** Email-to-N.A.T.A.N. per workflow email-based

**TODO:**

-   [ ] **Email Ingestion Service**
    -   [ ] Setup email forwarding: `atti@comune.fi.it` → N.A.T.A.N. processor
    -   [ ] Laravel Mail parser (IMAP monitoring)
    -   [ ] Extract PDF attachment + metadata da subject/body
    -   [ ] Auto-upload via PaActService
    -   [ ] Reply email con public verification link

**Priorità:** 🟢 BASSA  
**Effort:** 4 giorni  
**Dependencies:** Email server configuration

---

### Integrazione API con Gestionale ❌ TODO

**Descrizione:** API REST per integrazione con gestionali documentali esistenti

**TODO:**

-   [ ] **Public API Endpoints** (`/api/v1/natan/...`)
    -   [ ] `POST /acts/upload` - Upload atto da gestionale esterno
    -   [ ] `GET /acts/{id}` - Retrieve atto metadata
    -   [ ] `GET /acts` - List atti con filters
    -   [ ] `POST /acts/{id}/certify` - Trigger tokenizzazione manuale
-   [ ] **API Authentication**
    -   [ ] OAuth 2.0 client credentials grant
    -   [ ] API keys per enti PA (tabella `pa_api_keys`)
    -   [ ] Rate limiting per ente (100 req/min)
    -   [ ] IP whitelisting per security
-   [ ] **API Documentation**
    -   [ ] OpenAPI 3.0 spec completa
    -   [ ] Postman collection
    -   [ ] Code examples: PHP, JavaScript, Python
    -   [ ] Sandbox API per testing

**Priorità:** 🟢 BASSA (Feature enterprise)  
**Effort:** 6 giorni  
**Dependencies:** Laravel Passport/Sanctum

---

### Batch Upload Massivo ⚠️ TODO

**Descrizione:** Carica fino a 50 atti contemporaneamente

**TODO:**

-   [ ] **Batch Upload UI**
    -   [ ] Drag & drop multiplo (fino a 50 file)
    -   [ ] Preview lista files con validation (size, type)
    -   [ ] Bulk metadata form:
        -   [ ] Same metadata per tutti (ente, direzione)
        -   [ ] Individual metadata per file (protocol number, tipo, oggetto)
    -   [ ] Progress bar upload + processing
-   [ ] **Batch Processing**
    -   [ ] Job `BatchProcessPaActsJob` con chunking
    -   [ ] Process 10 atti alla volta (evita memory issues)
    -   [ ] Update progress in real-time (websocket o polling)
    -   [ ] Email notification al completamento
-   [ ] **Error Handling**
    -   [ ] Lista atti failed con motivo errore
    -   [ ] Retry singolo atto failed
    -   [ ] Export CSV atti failed per correzione offline

**Priorità:** 🟡 MEDIA  
**Effort:** 5 giorni  
**Dependencies:** Queue optimization, Frontend multi-upload

---

## 🎨 UI/UX ENHANCEMENTS

### Dashboard Widgets ⚠️ TODO

**TODO:**

-   [ ] **PA Dashboard Overhaul** (`/pa/dashboard`)
    -   [ ] Widget "Ultimi Atti Caricati" (5 recenti con preview)
    -   [ ] Widget "Statistiche Settimanali" (atti, tokenizzazioni, tempo medio)
    -   [ ] Widget "Atti in Attesa Tokenizzazione" con count
    -   [ ] Widget "Top Categorie Mese Corrente"
    -   [ ] Quick actions: "Carica Atto", "Cerca Atto", "Export FOIA"

**Priorità:** 🟡 MEDIA  
**Effort:** 3 giorni  
**Dependencies:** Analytics service

---

### Notification System ❌ TODO

**TODO:**

-   [ ] **In-App Notifications**
    -   [ ] Tabella `notifications` (Laravel standard)
    -   [ ] Notification types:
        -   [ ] `ActTokenized`: "Atto X tokenizzato con successo"
        -   [ ] `TokenizationFailed`: "Errore tokenizzazione atto Y"
        -   [ ] `SimilarActFound`: "Atto simile trovato: [link]"
        -   [ ] `MonthlyReport`: "Report mensile disponibile"
    -   [ ] Bell icon header con count unread
    -   [ ] Dropdown panel notifiche
    -   [ ] Mark as read / Mark all as read

**Priorità:** 🟢 BASSA  
**Effort:** 3 giorni  
**Dependencies:** Laravel Notifications

---

## 🧪 TESTING & QUALITY ASSURANCE

### Unit Tests ⚠️ TODO

**TODO:**

-   [ ] **DocumentParserService Tests**
    -   [ ] `it('extracts text from valid PDF')`
    -   [ ] `it('handles P7M signed PDFs')`
    -   [ ] `it('throws exception on corrupt file')`
-   [ ] **AIAnalyzerService Tests**
    -   [ ] `it('analyzes document and returns valid JSON')`
    -   [ ] `it('validates required fields')`
    -   [ ] `it('handles API failures gracefully')`
-   [ ] **TokenizePaActJob Tests**
    -   [ ] `it('tokenizes act successfully')`
    -   [ ] `it('updates pa_anchored columns')`
    -   [ ] `it('retries on failure')`

**Priorità:** 🟡 MEDIA  
**Effort:** 4 giorni  
**Dependencies:** AI Services implemented

---

### Feature Tests ⚠️ TODO

**TODO:**

-   [ ] **Upload Flow Tests**
    -   [ ] `it('uploads PA act with signature validation')`
    -   [ ] `it('dispatches tokenization job if checkbox checked')`
    -   [ ] `it('does not tokenize if checkbox unchecked')`
-   [ ] **Acts Index Tests**
    -   [ ] `it('shows only acts of authenticated PA entity')`
    -   [ ] `it('filters by doc_type correctly')`
    -   [ ] `it('paginates results')`

**Priorità:** 🟡 MEDIA  
**Effort:** 3 giorni  
**Dependencies:** Test database seeder

---

### Accuracy Validation ❌ TODO

**TODO:**

-   [ ] **AI Accuracy Tests**
    -   [ ] Collect 50 real acts from Comune Firenze
    -   [ ] Manual annotation: expected metadata for each
    -   [ ] Run AI extraction on all 50
    -   [ ] Calculate accuracy per field (tipo_atto, oggetto, importo, etc.)
    -   [ ] Target: >95% overall accuracy
    -   [ ] Identify failure patterns → improve prompts
-   [ ] **Continuous Monitoring**
    -   [ ] Dashboard accuracy metrics (updated weekly)
    -   [ ] Flag low-confidence extractions for manual review
    -   [ ] Feedback loop: manual corrections → AI fine-tuning

**Priorità:** 🔥 CRITICA (per pilot Firenze)  
**Effort:** 3 giorni + ongoing  
**Dependencies:** AI Parsing implemented, Real documents Firenze

---

## 📦 DEPLOYMENT & OPERATIONS

### Production Readiness ⚠️ TODO

**TODO:**

-   [ ] **Environment Setup**
    -   [ ] Production .env configuration
    -   [ ] Anthropic API key production
    -   [ ] Algorand mainnet configuration (vs testnet)
    -   [ ] Redis cache configuration
    -   [ ] Queue worker Supervisor config
-   [ ] **Monitoring**
    -   [ ] Laravel Horizon per queue monitoring
    -   [ ] Laravel Telescope (dev only, disable in prod)
    -   [ ] Sentry per error tracking
    -   [ ] Uptime monitoring (UptimeRobot)
    -   [ ] Custom alerts:
        -   [ ] Tokenization failure rate >5%
        -   [ ] AI API budget >80% monthly limit
        -   [ ] Queue backlog >100 jobs
-   [ ] **Performance Optimization**
    -   [ ] Cache stats queries (10 min TTL)
    -   [ ] Cache individual acts (1 hour TTL)
    -   [ ] Database indexes optimization
    -   [ ] Lazy loading images/PDFs
    -   [ ] CDN for static assets
-   [ ] **Backup Strategy**
    -   [ ] Daily DB backup to S3/DigitalOcean Spaces
    -   [ ] Weekly PDF files backup
    -   [ ] Disaster recovery plan documentation
    -   [ ] Restore procedure tested

**Priorità:** 🔥 CRITICA (pre-production)  
**Effort:** 5 giorni  
**Dependencies:** Production server access

---

### Documentation ⚠️ TODO

**TODO:**

-   [ ] **User Manual PA Operators** (PDF)
    -   [ ] Come caricare atto
    -   [ ] Come cercare atto
    -   [ ] Come gestire rettifiche
    -   [ ] Come esportare per FOIA
    -   [ ] Troubleshooting common issues
-   [ ] **Admin Guide** (per Fabio)
    -   [ ] Setup nuova PA entity
    -   [ ] Configurazione permissions
    -   [ ] Monitoring queue/jobs
    -   [ ] Gestione API budget
    -   [ ] Procedure manutenzione
-   [ ] **API Documentation** (OpenAPI)
    -   [ ] Endpoint reference completa
    -   [ ] Authentication guide
    -   [ ] Code examples
    -   [ ] Error codes reference
-   [ ] **GDPR Documentation**
    -   [ ] DPIA (Data Protection Impact Assessment)
    -   [ ] Registro trattamenti Art. 30
    -   [ ] Informativa privacy operatori
    -   [ ] Informativa privacy cittadini (verifica pubblica)

**Priorità:** 🟡 MEDIA  
**Effort:** 4 giorni  
**Dependencies:** Tutte features completate

---

## 📊 RIEPILOGO PRIORITÀ

### 🔥 PRIORITÀ CRITICA (Blockers per Pilot Firenze)

1. **AI Document Parsing** (7-10 giorni) - CORE N.A.T.A.N.
2. **Pagina Verifica Pubblica** (3 giorni) - Trasparenza visibile
3. **Accuracy Validation** (3 giorni) - Quality gate
4. **Production Deployment** (5 giorni) - Go-live

**Totale effort critico:** ~18-21 giorni

---

### 🟡 PRIORITÀ ALTA (Important per successo pilot)

1. **Analytics Dashboard** (6 giorni) - KPI misurabili
2. **Advanced Search** (5 giorni) - Efficienza ricerca
3. **Public Act Card** (4 giorni) - UX cittadini
4. **FOIA Export** (4 giorni) - Risposta rapida accessi

**Totale effort alto:** ~19 giorni

---

### 🟢 PRIORITÀ MEDIA-BASSA (Post-pilot enhancements)

1. Version Control System (6 giorni)
2. Certified Copies (7 giorni)
3. Compliance Reports (5 giorni)
4. Batch Upload (5 giorni)
5. Multi-User System (7 giorni)
6. DPO Dashboard (4 giorni)
7. Documentation (4 giorni)

**Totale effort medio:** ~38 giorni

---

### 🔵 PRIORITÀ BASSA (Nice-to-have, post-MVP)

1. Duplicate Detection (8 giorni)
2. Email Ingestion (4 giorni)
3. Public API (6 giorni)
4. Notification System (3 giorni)
5. Data Portability (2 giorni)
6. Onboarding Knowledge Base (5 giorni)

**Totale effort basso:** ~28 giorni

---

## 🎯 ROADMAP SUGGERITA

### FASE 1: MVP PILOT-READY (3-4 settimane)

**Obiettivo:** Sistema funzionante per pilot 8 settimane Comune Firenze

**Week 1-2:**

-   [ ] AI Document Parsing completo
-   [ ] Accuracy validation >95%
-   [ ] Pagina verifica pubblica

**Week 3:**

-   [ ] Analytics dashboard base
-   [ ] Advanced search
-   [ ] QR Code enhancement

**Week 4:**

-   [ ] Production deployment
-   [ ] User manual
-   [ ] Training operatori Firenze (2 ore)
-   [ ] Go-live pilot! 🚀

---

### FASE 2: PILOT OPTIMIZATION (Settimane 1-4 pilot)

**Obiettivo:** Miglioramenti basati su feedback operatori

**Durante pilot:**

-   [ ] FOIA export system
-   [ ] Public act card UI/UX polish
-   [ ] Compliance reports base
-   [ ] Bug fixing prioritario

---

### FASE 3: ENTERPRISE FEATURES (Post-pilot, se confermato)

**Obiettivo:** Scale a più comuni, features avanzate

**Mesi 3-6:**

-   [ ] Version control + rettifiche
-   [ ] Certified copies system
-   [ ] Multi-user + cross-office
-   [ ] Batch upload
-   [ ] API pubblica
-   [ ] Duplicate detection
-   [ ] Email ingestion

---

## 💰 STIME COSTI IMPLEMENTAZIONE

### FASE 1 (MVP Pilot-Ready)

-   **Development:** ~20 giorni
-   **AI API (Anthropic):** ~€50-100 per 200 atti pilot
-   **Infra (DO):** €25/mese server
-   **Blockchain Algorand:** €0 (testnet free, mainnet ~€0.001/tx)

**Totale Fase 1:** ~€75-125 + dev time

### FASE 3 (Enterprise)

-   **Development:** ~40 giorni
-   **AI API:** €100-200/mese (production scale)
-   **Infra:** €50-100/mese (scale-up server + CDN)

**Totale Fase 3:** ~€150-300/mese ongoing

---

## ✅ ACCEPTANCE CRITERIA PILOT

### Must-Have (Go/No-Go)

-   [ ] Upload + tokenizzazione funzionante per 200 atti
-   [ ] Accuracy AI >95% su metadata chiave
-   [ ] Tempo processing <30 secondi per atto
-   [ ] Pagina verifica pubblica operativa
-   [ ] Zero data breaches / compliance issues
-   [ ] 2 operatori Firenze formati e autonomi

### Success Metrics (Pilot 8 settimane)

-   [ ] 200+ atti processati
-   [ ] Feedback operatori >7/10
-   [ ] Zero downtime >4 ore
-   [ ] Response time FOIA test <2 ore (vs giorni baseline)
-   [ ] Sparavigna approva estensione 🎯

---

## 📞 NEXT STEPS

### Immediate (Questa settimana)

1. **Fabio decide priorità:** AI Parsing subito o completare UX base?
2. **Setup Anthropic API key** (se non già fatto)
3. **Collect 50 atti test** da Comune Firenze per accuracy validation
4. **Review questo documento** con Fabio → adjust priorities

### Short-term (Prossime 2 settimane)

1. Implementare features 🔥 CRITICAL
2. Testing continuo su sandbox
3. Preparare demo per Sparavigna

### Medium-term (Mese 1-2)

1. Pilot Firenze go-live
2. Iterazioni basate su feedback
3. Preparazione scaling altri comuni

---

**DOCUMENTO VIVO:** Aggiornare questo file man mano che features vengono implementate.  
**Metodo:** Spuntare checkbox `[ ]` → `[x]` quando completato.  
**Review:** Weekly con Fabio per adjust roadmap.

---

**Fine documento** - N.A.T.A.N. TODO Implementation v1.0
