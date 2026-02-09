# RAG System - TODO List

**Data creazione**: 2026-02-08
**Obiettivo**: Completare documentazione RAG user-facing per AI Sidebar FlorenceEGI

---

## 📊 STATO ATTUALE

### Documenti RAG Esistenti (User-Facing)
- [x] ID 1: FlorenceEGI – Fondamenti e Visione
- [x] ID 2: FlorenceEGI – Architettura Tecnica
- [x] ID 3: FlorenceEGI – Compliance e Governance
- [x] ID 4: FlorenceEGI – Gestione Pagamenti (4 Livelli)
- [x] ID 5: FlorenceEGI – Diritti d'Autore e Royalty
- [x] ID 6: FlorenceEGI – Gestione Fiscale e Rendicontazione
- [x] ID 7: NATAN – Neural Assistant
- [x] ID 8: Oracode System
- [x] ID 9: Glossario Completo
- [x] ID 10: Sistema di Auto-Rinnovo e Pagamenti Ricorsivi
- [x] ID 11: Logica di Rebind (Mercato Secondario)
- [x] ID 12: Configurazione Blockchain e Wallet Management
- [x] ID 13: Mappa Sistema Pagamenti
- [x] ID 14: Debiti Tecnici e Note di Sviluppo
- [x] ID 19: Assistenza e Supporto - Come Ottenere Aiuto

- [x] ID 23: Sistema CoA: Certificati di Autenticità (43 chunks)
- [x] ID 24: Prenotazioni Pre-Launch: Come Funziona (52 chunks)
- [x] ID 25: FlorenceEGI per Tutti: Guida Archetipi Utente (48 chunks)
- [x] ID 26: Collection Commerce: Abilita la Vendita (39 chunks)
- [x] ID 27: Wallet Redemption: Riscatta il Tuo Wallet (39 chunks)

**Totale**: 20 documenti user-facing nel database RAG

### Documentazione Tecnica (per Dev Team)
- [x] docs/FlorenceEGI/Implementation/Payments/sistema-pagamenti-guida-completa.md
- [x] docs/FlorenceEGI/Implementation/Collections/collections-guida-completa.md
- [x] docs/FlorenceEGI/Implementation/Wallet/sistema-wallet-guida-completa.md

---

## 🎯 GAP ANALYSIS - 12 Funzionalità Implementate Non Documentate

### PRIORITÀ ALTA (5 documenti)

- [x] **1. Sistema CoA: Certificati di Autenticità** ✅ COMPLETATO (ID 23)
  - **Categoria RAG**: `coa`
  - **Funzionalità implementate**:
    - Emissione CoA (Core + Annexes)
    - Verifica pubblica (QR code, serial, hash)
    - Signature workflow (author + inspector)
    - Chain of custody
    - PDF generation
    - Bundle support
    - Vocabulary traits
  - **Controller**: CoaController (35+ endpoints)
  - **Contenuto doc user-facing**:
    - Come emettere un certificato di autenticità
    - Come verificare un CoA con QR code
    - Cosa sono gli Annexes e come usarli
    - Come firmare un CoA come autore/inspector
    - Chain of custody: cos'è e come funziona

- [x] **2. Prenotazioni Pre-Launch: Come Funziona** ✅ COMPLETATO (ID 24)
  - **Categoria RAG**: `collections` o `getting-started`
  - **Funzionalità implementate**:
    - Pre-launch reservation con ranking pubblico
    - Multiple reservations per EGI
    - Mint window management
    - Weak auth support (guest users)
    - Supersede logic
  - **Controller**: ReservationController
  - **Contenuto doc user-facing**:
    - Come prenotare un EGI prima del lancio
    - Come funziona il ranking pubblico
    - Differenza tra reservation e mint
    - Cosa succede se non completo il mint dopo reservation
    - Mint window: cos'è e quando scade

- [x] **3. Guida per Archetipi Utente** ✅ COMPLETATO (ID 25)
  - **Categoria RAG**: `getting-started`
  - **Funzionalità implementate**:
    - 5 archetipi completi: Creator, Collector, Company, PA_Entity, EPP
    - Routes dedicate per ogni archetipo
    - Features specifiche per archetipo
  - **Routes**: /creator/*, /collector/*, /company/*, /pa/*, /epp/*
  - **Contenuto doc user-facing**:
    - FlorenceEGI per Creators: guida completa
    - FlorenceEGI per Collectors: cosa puoi fare
    - FlorenceEGI per Company: profilo business
    - FlorenceEGI per Enti Pubblici (PA)
    - FlorenceEGI per EPP: gestione progetti ambientali
    - Tabella comparativa funzionalità per archetipo

- [x] **4. Collection Commerce: Abilita la Vendita** ✅ COMPLETATO (ID 26)
  - **Categoria RAG**: `collections`
  - **Funzionalità implementate**:
    - Commerce Wizard guided setup
    - Delivery policy (DIGITAL_ONLY, PHYSICAL_ONLY, HYBRID)
    - Impact mode (EPP, SUBSCRIPTION, NONE)
    - EPP project selection
    - Commercial status progression
  - **Controller**: CollectionCommerceWizardController
  - **Contenuto doc user-facing**:
    - Come abilitare la vendita per la tua collection
    - Commerce Wizard: guida step-by-step
    - Delivery Policy: quale scegliere?
    - Impact Mode: EPP vs Subscription
    - Requisiti per abilitazione commerciale

- [x] **5. Wallet Redemption: Riscatta il Tuo Wallet** ✅ COMPLETATO (ID 27)
  - **Categoria RAG**: `wallet`
  - **Funzionalità implementate**:
    - Redemption flow completo
    - ASA transfer automatico
    - Download seed phrase
    - Cost calculation
    - Lista EGI riscattabili
  - **Controller**: WalletController
  - **Contenuto doc user-facing**:
    - Cos'è il wallet redemption e perché farlo
    - Come riscattare il tuo wallet custodial step-by-step
    - Transfer automatico ASA durante redemption
    - Costi redemption wallet
    - Cosa succede dopo redemption

### PRIORITÀ MEDIA (5 documenti)

- [ ] **6. Dashboard EPP: Gestione Progetti**
  - **Categoria RAG**: `epp`
  - **Funzionalità implementate**:
    - Dashboard gestione progetti ambientali
    - CRUD progetti (create, update, delete)
    - Milestone tracking
    - Impact metrics (ARF, APR, BPE)
    - Financial goals tracking
    - Collection linking
  - **Controller**: EPPController, EppProjectController
  - **Contenuto doc user-facing**:
    - Come creare un progetto EPP
    - Come tracciare milestone e impatto
    - Come linkare collections al mio progetto
    - Dashboard EPP: guida completa
    - Impact metrics: cosa significano

- [ ] **7. PA Acts: Tokenizzazione Atti Pubblici**
  - **Categoria RAG**: `pa-entity` o `blockchain`
  - **Funzionalità implementate**:
    - Upload PDF firmati QES/PAdES
    - Validazione firma digitale
    - Ancoraggio blockchain Algorand (Merkle tree batch)
    - Verifica pubblica atti
    - QR code verifica
  - **Routes**: /pa/heritage, /pa/dashboard
  - **Contenuto doc user-facing**:
    - Come tokenizzare un atto della Pubblica Amministrazione
    - Requisiti firma digitale (QES/PAdES)
    - Come verificare un atto tokenizzato
    - Dashboard PA: guida per enti pubblici
    - Ancoraggio blockchain: cos'è e come funziona

- [ ] **8. Egili Gift vs Lifetime**
  - **Categoria RAG**: `payments` o `egili`
  - **Funzionalità implementate**:
    - Gift Egili (con scadenza)
    - Lifetime Egili (senza scadenza)
    - Expiration management
    - Audit trail completo
  - **Service**: EgiliService
  - **Contenuto doc user-facing**:
    - Egili Gift vs Egili Lifetime: differenze
    - Quanto durano gli Egili Gift?
    - Come ottenere Egili Lifetime
    - Sistema scadenza Egili
    - Tracking balance Egili

- [ ] **9. Notifiche: Tipi e Gestione**
  - **Categoria RAG**: `getting-started` o `notifications`
  - **Funzionalità implementate**:
    - 5 tipi notifiche: Wallet, Invitations, Reservations, Commerce, GDPR
    - Response handlers dedicati per tipo
    - Archive system
  - **Controller**: Multiple notification controllers
  - **Contenuto doc user-facing**:
    - Sistema notifiche FlorenceEGI: come funziona
    - Tipi di notifiche e come rispondere
    - Notifiche commerce: tracking ordini
    - Gestione notifiche team collection
    - Archive notifiche

- [ ] **10. EGI Fisici: Spedizione e Fulfillment**
  - **Categoria RAG**: `commerce`
  - **Funzionalità implementate**:
    - Multiple shipping addresses
    - Physical EGI detection (is_physical flag)
    - Shipping profile (weight, dimensions)
    - Delivery policy enforcement
    - Shipping notifications
  - **Models**: UserShippingAddress
  - **Contenuto doc user-facing**:
    - EGI Fisici: come funziona la spedizione
    - Come aggiungere indirizzi di spedizione
    - Tracking spedizione ordini
    - Delivery Policy delle Collections
    - EGI ibridi (digitale + fisico)

### PRIORITÀ BASSA (2 documenti)

- [ ] **11. GDPR Avanzato: Tutti i Tuoi Diritti**
  - **Categoria RAG**: `privacy-gdpr`
  - **Funzionalità implementate**:
    - 10 diritti GDPR completi
    - Consent management (versioning, revoca)
    - Data export/portability
    - Activity log
    - Breach reporting
    - DPO contact
    - Cookie consent
  - **Routes**: /gdpr/* (15+ endpoints)
  - **Contenuto doc user-facing**:
    - Come richiedere l'export dei tuoi dati
    - Activity Log: visualizza tutte le tue attività
    - Come segnalare un breach di sicurezza
    - Contatta il DPO (Data Protection Officer)
    - Cookie Consent: gestione preferenze
    - Come eliminare il tuo account

- [ ] **12. Payment Methods per Collection**
  - **Categoria RAG**: `payments`
  - **Funzionalità implementate**:
    - WalletDestination architecture
    - Multi-PSP per collection
    - Primary vs secondary destinations
  - **Controller**: PaymentSettingsController
  - **Contenuto doc user-facing**:
    - Come configurare metodi di pagamento per collection
    - Differenza tra user payment methods e collection payment methods
    - Primary vs secondary payment destinations

---

## 📋 TASK OPERATIVI

### Task Completati
- [x] Creare migration per 30 categorie RAG
- [x] Creare traduzioni 6 lingue per categorie (IT/EN/DE/ES/FR/PT)
- [x] Analizzare codebase per identificare funzionalità implementate
- [x] Confrontare codebase vs documentazione esistente
- [x] Identificare 12 GAP nella documentazione user-facing
- [x] Salvare documentazione tecnica in docs/FlorenceEGI/Implementation/

### Task Da Fare
- [ ] Ri-categorizzare documenti esistenti (ID 1-14) nelle categorie specifiche
- [ ] Creare 12 documenti user-facing per colmare GAP (vedi sopra)
- [ ] Testare AI Sidebar con nuova documentazione RAG
- [ ] Validare copertura query utenti comuni

---

## 📝 NOTE

### Differenza Documentazione Tecnica vs User-Facing

**Documentazione TECNICA** (docs/FlorenceEGI/Implementation/):
- Audience: Dev team
- Contenuto: Controller, service, model, endpoints, codice
- Esempio: "PaymentSettingsController::updateBankConfig() accetta IBAN regex..."

**Documentazione USER-FACING** (Database RAG):
- Audience: Utenti finali (creators, collectors, companies, PA, EPP)
- Contenuto: Guide pratiche, workflow, "come fare X"
- Esempio: "Come configurare il bonifico bancario per ricevere pagamenti"

### Workflow Creazione Documento User-Facing

1. Identificare funzionalità implementata (da GAP analysis)
2. Analizzare controller/service per capire workflow utente
3. Scrivere documento in linguaggio USER-FRIENDLY (no codice)
4. Salvare in /tmp/rag_[nome].md
5. Indicizzare con `php artisan rag:create-document [categoria] "[titolo]" /tmp/rag_[nome].md --save-md`
6. Spuntare task in questo file

### Regole P0 da Seguire

- **P0-1**: MAI dedurre. Se non sai → 🛑 CHIEDI
- **P0-2**: Translation keys only (no hardcoded strings)
- **P0-9**: Traduzioni in TUTTE le 6 lingue (it, en, de, es, fr, pt)

---

**Ultimo aggiornamento**: 2026-02-08 23:15

---

## 🎉 PROGRESS UPDATE - 5 DOCUMENTI HIGH PRIORITY COMPLETATI!

**Data completamento**: 2026-02-08

### Documenti Creati (Sessione Corrente)

| ID | Titolo | Categoria | Chunks | Status |
|----|--------|-----------|--------|--------|
| 23 | Sistema CoA: Certificati di Autenticità | collections | 43 | ✅ Indexed |
| 24 | Prenotazioni Pre-Launch: Come Funziona | getting-started | 52 | ✅ Indexed |
| 25 | FlorenceEGI per Tutti: Guida Archetipi Utente | getting-started | 48 | ✅ Indexed |
| 26 | Collection Commerce: Abilita la Vendita | collections | 39 | ✅ Indexed |
| 27 | Wallet Redemption: Riscatta il Tuo Wallet | wallet | 39 | ✅ Indexed |

**Totale chunks aggiunti**: 221 chunks

### File Backup Creati

Tutti i documenti sono stati salvati anche come .md in:
- /home/fabio/EGI/docs/rag/collections/sistema-coa-certificati-di-autenticita.md
- /home/fabio/EGI/docs/rag/getting-started/prenotazioni-pre-launch-come-funziona.md
- /home/fabio/EGI/docs/rag/getting-started/florenceegi-per-tutti-guida-archetipi-utente.md
- /home/fabio/EGI/docs/rag/collections/collection-commerce-abilita-la-vendita.md
- /home/fabio/EGI/docs/rag/wallet/wallet-redemption-riscatta-il-tuo-wallet.md

**Ultimo aggiornamento**: 2026-02-08 23:20
