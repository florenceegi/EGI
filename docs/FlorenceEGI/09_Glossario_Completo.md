# Glossario Completo – FlorenceEGI

Questo glossario raccoglie **tutti i termini tecnici, legali, fiscali e di governance** utilizzati nell'ecosistema FlorenceEGI, unificando le definizioni dai documenti di riferimento.

---

## A

### AES-256

**Advanced Encryption Standard a 256 bit**. Algoritmo crittografico simmetrico usato per cifrare le chiavi private dei wallet auto-generati nel Livello 1 (No Wallet FIAT).

### Algorand

Blockchain **layer-1** con consenso **Pure Proof-of-Stake (PPoS)**. Utilizzata da FlorenceEGI per:

- Emissione **ASA** (EGI come NFT)
- Smart contract **TEAL** (AVM)
- Anchor hash immutabili
- Finalità transazioni <5 secondi
- Costo ~€0.001 per transazione

### AMMk

**Asset Market Maker**. Piattaforma che abilita la creazione e distribuzione di Asset Digitali (EGI).
**Caratteristiche distintive**:
- **Oggetto**: Asset (EGI) con valore reale, non semplici prodotti.
- **Marketplace Personale**: Consente a un utente di generare il proprio Marketplace per la distribuzione degli EGI.
- **Marketing AI**: Strumenti nativi per promozione e posizionamento.
- **GDPR by design**: Privacy integrata dall'architettura.
- **Native AI**: Intelligenza Artificiale (NATAN) integrata nel core.

### Anchor Hash

**Hash crittografico SHA-256** registrato on-chain (Algorand) che certifica:

- Autenticità metadata EGI
- Timestamp immutabile
- Integrità dati (tamper-proof)

Verifica: Ricalcola hash da dati → confronta con hash on-chain.

### APS

**Associazione di Promozione Sociale**. Entità giuridica nella **Governance Duale** che:

- Custodisce **valori fondanti** FlorenceEGI
- Ha **diritto di veto** su decisioni SRL contrarie a missione
- Garantisce sostenibilità sociale progetto

### ASA

**Algorand Standard Asset**. Token nativo su blockchain Algorand. Gli **EGI sono ASA** con metadata personalizzati (artwork, provenance, royalty).

### AuditLogService

Servizio **append-only** che registra ogni azione critica nel sistema:

- Accesso dati personali (GDPR Art. 15-20)
- Modifica consensi (ConsentService)
- Transazioni EGI (mint, transfer, royalty)
- Decisioni NATAN (suggest, activate)

**Caratteristiche**:

- Immutabile (firma digitale SHA-256)
- Conservazione 10 anni
- Ricostruibile (event sourcing)

---

## B

### Blockchain

**Registro distribuito immutabile** basato su crittografia. FlorenceEGI usa **Algorand** per:

- Certificare proprietà EGI (ASA)
- Garantire trasparenza transazioni
- Prevenire double-spending
- Tracciare provenance opere

---

## C

### CASP

**Crypto-Asset Service Provider**. Soggetto autorizzato a fornire servizi su cripto-attività secondo **Regolamento MiCA** (UE 2023/1114).

FlorenceEGI **NON è CASP** perché:

- Non custodisce fondi utenti
- Non offre exchange crypto/FIAT
- Non gestisce wallet per conto terzi
- Delega a **PSP esterni** (Stripe, CASP partner)

### Co-Creatore

Ruolo centrale nell'ecosistema, rappresenta la **Causa Efficiente** dell'opera.
Mentre l'artista è la sorgente creativa, il Co-Creatore è colui che **porta l'opera all'esistenza reale** tramite il prima atto di Mint (acquisto/creazione).

- **Firma Perpetua**: Il suo nome rimane legato all'EGI per sempre (on-chain).
- **Partecipazione**: Non è un semplice acquirente, ma parte attiva del processo generativo.
- **Status**: Riceve riconoscimento pubblico come co-autore del valore dell'asset.

### CoA

**Certificate of Authenticity**. Certificato digitale immutabile emesso **dal Creator** per ogni EGI che attesta:

- **Autore originale** (issuer_type: author/archive/platform)
- **Data creazione** e timestamp emissione
- **Snapshot immutabile dei traits** dell'opera (titolo, tecnica, materiali, dimensioni, etc.)
- **Hash on-chain** (proof immutabile)
- **Verification hash** (SHA-256) e **integrity hash**
- **Firme digitali** (autore, ispettore)
- **QR code** per verifica pubblica

**Emesso da**: `CoaIssueService` → crea `Coa` + `CoaSnapshot` immutabile.

> ⚠️ **Nota**: Il CoA attesta l'**AUTENTICITÀ** dell'opera, NON la proprietà.

### CoO

**Certificate of Ownership**. Certificato digitale emesso al **settlement di una transazione** che attesta il **trasferimento di proprietà** di un EGI. Contiene:

- **Proprietario corrente** dell'EGI
- **Importo pagato** (EUR + valuta buyer)
- **Tasso di cambio** utilizzato (`fx_rate_used`)
- **Transaction ID on-chain** per verifica (se applicabile)
- **Merkle proof** per ancoraggio blockchain
- **Data/ora** del trasferimento

**Emesso al**: completamento del settlement (`escrow_locked → completed`).

> **Distinzione CoA vs CoO:**
> - **CoA** → emesso dal **Creator** per attestare l'**autenticità** dell'opera
> - **CoO** → emesso al **settlement** per attestare il **trasferimento di proprietà**

### Collector

Utente che **acquista e possiede EGI**. Può:

- Collezionare opere digitali/fisiche
- Rivendere su mercato secondario (MiCA-safe)
- Esporre in gallerie virtuali/fisiche
- Donare a musei/enti culturali

**Non acquisisce copyright**, solo proprietà fisica/NFT.

### Compliance

Conformità a **normative** e **standard etici**:

- **GDPR**: Protezione dati personali
- **MiCA**: Regolamentazione cripto-attività
- **LDA**: Diritti autore (633/1941)
- **D.Lgs. 118/2006**: Diritto di Seguito
- **Oracode OS3.0**: Trasparenza e tracciabilità

### ConsentService

Servizio GDPR-compliant per gestione **consenso utente**:

- **Granulare**: Consenso per singola funzionalità (analytics, NATAN, marketing)
- **Versionato**: Storico modifiche consenso
- **Revocabile**: Utente può ritirare in qualsiasi momento
- **Tracciato**: Ogni modifica registrata in AuditLog

---

## D

### Diritto di Seguito

**Royalty legale** (4%-0.25%) su rivendite **opere fisiche** oltre €3,000 (D.Lgs. 118/2006, recepimento UE 2001/84/CE).

**Caratteristiche**:

- Solo opere fisiche (non digitali)
- Solo rivendite professionali (gallerie, case d'asta)
- Solo UE
- Durata: Vita autore + 70 anni
- Gestione: Professionisti autorizzati (SIAE)
- Scadenza: 3 mesi dalla vendita

**CUMULABILE** con royalty contrattuale piattaforma (4.5%).

### Diritti Morali

Diritti **inalienabili** dell'autore (LDA 633/1941 Art. 20):

- **Paternità**: Riconoscimento autore sempre
- **Integrità**: Divieto modifiche senza consenso
- **Attribuzione**: Divieto rimozione firma

**Durata**: Vita + 70 anni (eredi).

**Non si trasferiscono** con vendita EGI.

### Diritti Patrimoniali

Diritti **economici** sull'opera (LDA Art. 12-19):

- **Riproduzione**: Copie, stampe
- **Comunicazione**: Pubblicità, TV, online
- **Distribuzione**: Vendita copie

**Durata**: Vita + 70 anni.

**IMPORTANTE**: Comprare NFT ≠ Comprare copyright.

### Drops

**Evento di pubblicazione** collezione EGI. Può essere:

- **Timed**: Slot temporale limitato (es: 48h)
- **Limited**: Quantità limitata (es: 100 pezzi)
- **Open**: Senza limiti

NATAN può suggerire **timing ottimale** basato su dati mercato.

---

## E

### EGI

**Environment Goods Invent**. Asset digitale unico composto da:

- **EPP**: Environmental Ptection Projects (impatto sociale)
- **GOODS**: Opera/servizio/creatività (valore artistico)
- **Creativity**: Origine umana (mai IA generativa pura)

**Caratteristiche**:

- Certificato on-chain (Algorand ASA)
- Dual flow: Valore economico + valore sociale
- Royalty automatiche (4.5% smart contract)
- Tracciabilità completa (provenance)

### Egili

**Moneta interna** dell'ecosistema FlorenceEGI.
Utilizzata in sostituzione della valuta FIAT per:
- **Acquistare servizi** interni alla piattaforma (es. boost visibilità, tools avanzati).
- **Reward**: Elargiti come premi per contributi alla community, promozioni o azioni virtuose.

L'utente acquista Egili presso la piattaforma (valore stabile). Non sono token speculativi esterni.

### EMI

**Electronic Money Institution**. Ente autorizzato a emettere moneta elettronica (es: stablecoin). FlorenceEGI può integrare PSP partner EMI per Livello 3 (Crypto).

### EPP

**Environmet Protection Projects**. Organizzazione no-profit che riceve **donazione automatica** da ogni transazione EGI:

- Default: **20%** vendita primaria, **1%** secondaria
- Split trustless (smart contract)
- Progetti verificati (Gold Standard/VCS)
- Dashboard pubblica KPI impatto

**Esempi**: WWF, FAI, Croce Rossa, progetti locali (riforestazione, restauro).

### Equilibrium

Rappresenta concretamente i **Fondi** destinati al ripristino dell'ecosistema.
Metaforicamente è la "Molecola" (Amore + Estetica) che permea il sistema.
Il nome deriva dalla sua funzione: generare un **economia equilibrata** dove la produzione di ricchezza finanzia direttamente il ripristino ambientale (20% delle transazioni destinato agli EPP).

### ERP

**Enterprise Resource Planning**. Software gestionale aziendale (SAP, Oracle) per grandi enti EPP. FlorenceEGI integra via export CSV/XML o webhook.

### ETS

**Ente del Terzo Settore** (riforma D.Lgs. 117/2017). Categoria EPP piccoli (ONLUS, APS, ODV).

### Event Bus

Architettura **pub/sub** per comunicazione asincrona tra moduli:

- **Publisher**: Emette eventi (es: `AssetCreated`)
- **Subscriber**: Reagisce a eventi (es: NATAN riceve `AssetCreated` → tweet automatico)

**Trigger**:

- **On-chain**: Eventi blockchain (mint, transfer)
- **Off-chain**: Eventi applicativi (user login, payment received)

---

## F

### FatturaPA

Standard XML per **fatturazione elettronica** italiana (versione 1.6.1). Obbligatorio per:

- Transazioni B2B con PA
- Merchant con P.IVA

FlorenceEGI integra provider SDI per generazione/invio automatico.

### Fee

**Commissione** piattaforma su transazioni:

- **Default**: 10% (ridotta con Fee Dinamiche)
- **Minima**: 5% (community 10k+ utenti)
- **Creator/EPP**: 85-90% ricavi

**Fee Dinamiche**:

```
Fee = Fee_Base × (1 - min(Users/10k × 0.1, 0.5))
```

---

## G

### GDPR

**General Data Protection Regulation** (UE 2016/679). Regolamento protezione dati personali. FlorenceEGI compliance:

- **ULM**: Log strutturati, conservazione 90gg
- **AuditLogService**: Traccia accessi, immutabile, 10 anni
- **ConsentService**: Consenso granulare revocabile
- **Diritti utente**: Access, Portability, Erasure, Rectification

### Gold Standard

Certificazione progetti EPP che rispettano:

- **Addizionalità**: Impatto oltre business-as-usual
- **Permanenza**: Durata >20 anni
- **Nessuna doppia contabilizzazione**: Crediti unici

Standard riconosciuti: **VCS** (Verified Carbon Standard), **GS** (Gold Standard).

### Governance Duale

Modello di governo FlorenceEGI:

- **SRL**: Operatività, innovazione, mercato
- **APS**: Custode valori, veto su decisioni anti-missione

**Esempio pratico**:

- SRL propone partnership con azienda fast-fashion
- APS valuta compatibilità missione sostenibilità
- Se incompatibile: **veto** (blocca decisione)

---

## H

### Hash

Funzione crittografica **one-way** che genera **impronta digitale** univoca da dati:

- **SHA-256**: Standard usato da FlorenceEGI e Bitcoin
- **Immutabile**: Cambio minimo input → hash completamente diverso
- **Collisioni**: Probabilità trascurabili (2^256 combinazioni)

**Uso**: Anchor hash on-chain per certificare metadata EGI.

---

## I

### IVA

**Imposta sul Valore Aggiunto**. Gestione FlorenceEGI:

- **Italia**: 22% ordinaria (esenzione opere originali Art. 10 DPR 633/72)
- **UE B2C**: Regime OSS (One Stop Shop)
- **UE B2B**: Reverse charge (cliente autoliquida)
- **Extra-UE**: Esente

---

## L

### LDA

**Legge sul Diritto d'Autore** (633/1941). Normativa italiana che tutela:

- **Diritti morali** (Art. 20)
- **Diritti patrimoniali** (Art. 12-19)
- **Diritto di Seguito** (Art. 19bis, D.Lgs. 118/2006)

---

## M

### Mecenate

Ruolo nell'ecosistema FlorenceEGI. Utente che:

- **Sostiene Creator** (donazioni, acquisti opere emergenti)
- **Supporta EPP** (donazioni dirette progetti)
- **Profilo verificato**: Storico contributi, ranking impatto

**Riconoscimento**: Badge on-chain, visibilità community.

### Merchant

**Venditore** EGI. Può essere:

- **Creator**: Vende opere proprie
- **Collector**: Rivende su mercato secondario
- **Galleria**: Rivendita professionale

**Obblighi**:

- Registrazione autenticazione forte (SPID/CIE)
- Dati fiscali validi (P.IVA o CF)
- Fatturazione elettronica (se P.IVA)

### MiCA

**Markets in Crypto-Assets Regulation** (UE 2023/1114). Regolamento cripto-attività. Entrata in vigore: 30 dicembre 2024.

**Definisce**:

- **CASP**: Fornitori servizi cripto (exchange, wallet custodial)
- **Stablecoin**: Asset-referenced token (ART), e-money token (EMT)
- **Obblighi**: KYC, AML, trasparenza, white paper

**FlorenceEGI è MiCA-safe** perché:

- Non custodisce fondi
- Non offre exchange
- Non gestisce pagamenti crypto diretti
- Delega a PSP/CASP autorizzati

### MiCA-safe

**Termine FlorenceEGI** per indicare conformità **de facto** a MiCA **pur non essendo CASP**.

**Cosa fa FlorenceEGI** (MiCA-safe):

- Incassa fee FIAT via PSP
- Emette EGI (ASA) su Algorand
- Registra anchor hash on-chain
- Calcola royalty automatiche
- Genera QR code verifica

**Cosa NON fa** (esclusione MiCA):

- Custodisce fondi/crypto utenti
- Offre exchange crypto/FIAT
- Gestisce pagamenti crypto diretti
- Detiene chiavi private wallet clienti
- Intermediazione finanziaria

---

## N

### NATAN

**Neural Assistant for Technical Art Navigation**. IA FlorenceEGI che:

- **Analizza** collezioni (traits, rarità, sentiment)
- **Suggerisce** strategie marketing Creator
- **Apprende** gusto personale Collector
- **Attiva** campagne automatiche (trigger on/off-chain)
- **Spiega** ogni decisione (trasparenza etica)

**Principio**: "Non sostituisce, ma espande" capacità umane.

### NFT

**Non-Fungible Token**. Asset digitale unico su blockchain. Gli **EGI sono NFT** (ASA su Algorand) con:

- Metadata artwork/servizio
- Royalty automatiche (smart contract)
- Provenance tracciabile

---

## O

### Off-chain

Dati/eventi **fuori dalla blockchain**:

- Metadata JSON (IPFS/server centralizzato)
- Pagamenti FIAT (PSP)
- Interazioni app (like, commenti)

**Collegamento blockchain**: Anchor hash on-chain verifica integrità dati off-chain.

### On-chain

Dati/eventi **registrati su blockchain**:

- Transazioni ASA (mint, transfer)
- Smart contract (royalty split)
- Anchor hash (proof immutabilità)

**Vantaggio**: Immutabilità, trasparenza, decentralizzazione.

### ONLUS

**Organizzazione Non Lucrativa di Utilità Sociale**. Categoria EPP (ora ETS post-riforma). Riceve donazioni con ricevuta fiscale.

### Oracode System

**Paradigma filosofico-tecnico** che fonde ingegneria e etica. Principi:

1. **Documentazione Totale**: Ogni decisione spiegata
2. **Regola Zero**: Mai dedurre senza dati
3. **Trasparenza Etica**: Ogni algoritmo interrogabile
4. **Funzionalità Verificabile**: Ogni processo tracciato

**Versione corrente**: **OS3.0** (Testing as Oracles, ADR, Architettura Cognitiva).

### Opt-in

Operazione **obbligatoria** su Algorand per ricevere ASA:

- Costo: ~€0.001
- Aumenta MBR (Minimum Balance Requirement) wallet di 0.1 ALGO
- Necessario prima di mint/transfer EGI

### OSS

**One Stop Shop**. Regime semplificato IVA UE per vendite B2C cross-border:

- Venditore dichiara IVA paese consumatore
- Versamento tramite portale OSS paese sede
- Nessun obbligo registrazione in ogni paese UE

FlorenceEGI usa OSS per gestire IVA clienti UE privati.

---

## P

### Padmin D. Curtis

**Identità operativa** (persona di sistema) che rappresenta l'approccio **Oracode System**. Incarna principi:

- Documentazione radicale
- Testing as Oracles
- Trasparenza etica
- Funzionalità verificabile

**Ruolo**: Guida filosofica-tecnica FlorenceEGI, custode paradigma Oracode.

### Partita IVA

Codice identificativo fiscale **attività commerciale/professionale**. Obbligatoria per Creator se:

- Vendite abituali (continuità)
- Organizzazione mezzi (studio, galleria)
- Scopo lucro prevalente
- Ricavi >€5,000/anno (soglia prestazione occasionale)

### Payout

**Accredito fondi** da piattaforma a merchant/creator. FlorenceEGI:

- **NON trattiene** fondi (MiCA-safe)
- Accredito diretto wallet utente
- Oppure: Settlement PSP → conto bancario merchant

### PPoS

**Pure Proof-of-Stake**. Meccanismo consenso Algorand:

- Validatori scelti casualmente proporzionalmente a stake (ALGO posseduti)
- Nessun mining energivoro
- Finalità istantanea (<5s)
- Green blockchain (~0.000008 kWh/transazione vs 700 kWh Bitcoin)

### PSP

**Payment Service Provider**. Fornitore servizi pagamento (Stripe, Adyen, PayPal). FlorenceEGI delega a PSP:

- Incasso FIAT (Livello 1 e 2)
- Conversione crypto (Livello 3, tramite CASP/EMI partner)

**Responsabilità PSP**: KYC, AML, PCI-DSS compliance.

---

## Q

### QR Code

**Quick Response Code**. Codice bidimensionale per verifica EGI:

- Scansione → URL verifica
- Mostra: Metadata, anchor hash, storia transazioni
- Confronta: Hash calcolato vs hash on-chain

**Uso**: Tag fisici su opere, certificati stampati.

---

## R

### RAG

**Retrieval-Augmented Generation**. Tecnica AI che combina:

- **Ricerca semantica**: Trova documenti rilevanti
- **Generazione testo**: Risposta contestuale

**Uso NATAN**: Assistenza documentale Tenant (notarizzazione, ricerca archivi PA).

### RBAC

**Role-Based Access Control**. Sistema permessi FlorenceEGI:

- **Ruoli globali**: Admin, Creator, Collector, Mecenate
- **Ruoli locali**: Curatore (tenant FlorenceArtEGI), Analista (tenant NATAN)
- **Enforcement**: Middleware Laravel + policy

**Principio**: Least privilege (minimo necessario).

### Reverse Charge

Meccanismo IVA **B2B intra-UE**:

- Fornitore emette fattura **senza IVA**
- Cliente **autoliquida** IVA nel proprio paese

**Esempio**:

- FlorenceEGI (IT) vende a azienda DE
- Fattura: €100 + €0 IVA (reverse charge)
- Azienda DE autoliquida IVA 19% in Germania

### Ricevuta Occasionale

Documento fiscale per **prestazioni occasionali** (sotto €5,000/anno):

- Non serve P.IVA
- Reddito dichiarato come "reddito diverso"

**Oltre soglia**: Obbligo apertura P.IVA.

### ROD

**Registry Of Deeds**. Registro digitale decentralizzato:

- Ogni EGI registrato con provenance
- Storico transazioni immutabile (blockchain)
- Verifica proprietà real-time

**Analogia**: Catasto digitale opere d'arte.

### Royalty

**Compenso automatico** Creator su rivendite EGI. FlorenceEGI ha sistema **dual-layer**:

#### Layer 1: Royalty Piattaforma (Contrattuale)

- **Aliquota**: 4.5% fisso
- **Soglia**: €0+ (sempre)
- **Esecuzione**: Smart contract trustless istantaneo
- **Durata**: Perpetuo

#### Layer 2: Diritto di Seguito (Legale)

- **Aliquota**: 4%-0.25% decrescente
- **Soglia**: €3,000+
- **Esecuzione**: Manuale via professionisti SIAE
- **Durata**: Vita + 70 anni
- **Scadenza**: 3 mesi dalla vendita

**CUMULABILI**: Vendita €50k galleria → 4.5% + 4% = 8.5% totale.

---

## S

### SDI

**Sistema di Interscambio**. Piattaforma Agenzia delle Entrate per trasmissione **fatture elettroniche**. FlorenceEGI integra via provider accreditato.

### Settlement

**Regolamento transazione**. Processo finale trasferimento fondi:

- PSP riceve pagamento cliente
- PSP trattiene fee (1-3%)
- PSP accredita merchant (settlement)

FlorenceEGI **NON partecipa** a settlement (MiCA-safe).

### SIAE

**Società Italiana Autori Editori**. Ente gestione collettiva diritti autore. Gestisce:

- Diritto di Seguito (D.Lgs. 118/2006)
- Royalty SIAE (musica, teatro, etc.)

**Responsabilità Creator**: Comunicare vendite >€3k a professionisti SIAE entro 3 mesi.

### Smart Contract

**Programma autoeseguente** su blockchain:

- Logica if/then codificata (es: "se transfer EGI → paga 4.5% royalty")
- Esecuzione trustless (nessun intermediario)
- Immutabile (non modificabile post-deploy)

**FlorenceEGI usa**: TEAL (Algorand) per royalty split automatico.

### SRL

**Società a Responsabilità Limitata**. Entità giuridica nella **Governance Duale** che:

- Gestisce operatività FlorenceEGI
- Innovazione prodotto
- Rapporti commerciali

**Vincolo**: Decisioni sottoposte a valutazione APS (veto se anti-missione).

### Stablecoin

Criptovaluta **ancorata** a valuta fiat (1:1):

- **USDCa**: USD Coin su Algorand
- **EURC**: Euro Coin su Algorand

**Uso FlorenceEGI**: Livello 3 (Crypto) tramite PSP/CASP partner.

---

## T

### TEAL

**Transaction Execution Approval Language**. Linguaggio smart contract Algorand:

- Turing-incomplete (sicurezza)
- Verificabile formalmente
- Costo gas deterministico

**Uso FlorenceEGI**: Royalty split, escrow, attestazioni.

### Tenant

**Istanza specializzata** FlorenceEGI:

- **NATAN**: AI + notarizzazione + RAG per PA
- **FlorenceArtEGI**: Marketplace arte + Collection Workspace

**Architettura**: SaaS multi-tenant (Core condiviso, tenant isolati).

### Token Ecosystem

Sistema token FlorenceEGI:

- **EGI**: Asset principale (NFT arte/servizi)
- **Equilibrium**: Governance non-speculativa
- **Egili**: Micro-token engagement

**Principio**: Nessun token speculativo puro.

### TPS

**Transactions Per Second**. Throughput blockchain:

- **Algorand**: ~1,000 TPS
- **Ethereum**: ~15 TPS
- **Bitcoin**: ~7 TPS

---

## U

### UEM

**Unified Event Model**. Standard eventi Event Bus:

- Struttura JSON consistente
- Campi obbligatori: `event_type`, `timestamp`, `user_id`, `payload`
- Versioning eventi (backward compatibility)

### ULM

**Unified Logging Model**. Standard log FlorenceEGI:

- Struttura JSON semantica
- Campi: `timestamp`, `level`, `context`, `message`, `user_id`
- Conservazione: 90 giorni (hot storage) + 10 anni (cold storage per audit)
- GDPR-compliant: Pseudonimizzazione automatica

---

## V

### VCS

**Verified Carbon Standard**. Standard certificazione progetti compensazione carbonio (EPP). Garantisce:

- Addizionalità
- Permanenza
- Nessuna doppia contabilizzazione

---

## W

### Wallet

**Portafoglio digitale** per gestire cripto-attività:

#### Custodial (Custodia terzi)

- Piattaforma detiene chiavi private
- Utente ha solo credenziali accesso
- **Rischio**: Trust piattaforma (MiCA regola questo)

#### Non-Custodial (Auto-custodia)

- Utente controlla chiavi private
- Piattaforma NON può accedere fondi
- **Responsabilità**: Utente (backup seed phrase)

**FlorenceEGI** adotta un approccio ibrido **Non-Custodial by default**:

- **Wallet Utente (Non-Custodial)**: L'utente mantiene il controllo delle proprie chiavi o utilizza sistemi di firma remota sicura dove la piattaforma non ha accesso ai fondi.
- **Integrazione PSP**: I fondi FIAT sono gestiti da PSP autorizzati (Custodial regolamentati), non dalla piattaforma direttamente.

Questo garantisce la massima sicurezza e l'assenza di rischio di controparte per i fondi degli utenti (MiCA-safe).

---

## Altri Termini

### Blockchain Anchor

Sinonimo **Anchor Hash**.

### Creator

Utente che crea opere originali (digitali/fisiche) e le certifica come EGI.

### Curatore

Ruolo professionale verificato che:

- Seleziona opere qualità
- Organizza mostre virtuali/fisiche
- Ranking pubblico basato impatto

**Verifica**: Portfolio mostre, referenze, contributi community.

### Dashboard

Area amministrativa merchant/creator con:

- Report vendite (primarie/secondarie)
- Royalty ricevute
- Export CSV/XML
- Alert fiscali
- Gestione consent GDPR

### Export

Download dati transazioni per contabilità:

- Formato: CSV, XML, JSON
- Periodo: Mensile, trimestrale, annuale
- Compatibilità: Software fiscali (Teamsystem, Zucchetti)

### Fatturazione Batch

**Fattura cumulativa** per alto volume transazioni:

- Una fattura mensile (es: €250 fee totali)
- Allegato CSV/XML con dettaglio 500+ operazioni
- Conforme SDI

**Uso**: Trader alto flusso.

### IVA Internazionale

Gestione IVA cross-border:

- **Italia**: 22% ordinaria (esenzione opere originali)
- **UE B2C**: OSS (IVA paese consumatore)
- **UE B2B**: Reverse charge
- **Extra-UE**: Esente

### KYC/AML

**Know Your Customer / Anti-Money Laundering**. Procedure obbligatorie PSP/CASP per:

- Identificazione cliente (documento identità)
- Verifica fonte fondi
- Monitoraggio transazioni sospette

FlorenceEGI **NON gestisce** KYC/AML (delegato a PSP).

### Merchant di Record

**Responsabile vendita**. In FlorenceEGI:

- **Merchant**: Creator/Collector che vende
- **NON piattaforma**: FlorenceEGI non è merchant of record (MiCA-safe)

### Mint

**Creazione** asset digitale su blockchain (conio NFT). FlorenceEGI:

- Crea ASA su Algorand
- Associa metadata (artwork, CoA)
- Registra anchor hash on-chain
- Emette royalty smart contract

### NFC

**Near Field Communication**. Tag wireless per verifica EGI:

- Avvicina smartphone → legge metadata
- Alternativa QR code per opere fisiche

### Plusvalenza

**Capital gain** (profitto rivendita asset). Fiscalità Italia:

- Aliquota: 26% (imposta sostitutiva)
- Regime dichiarativo: Quadro RT
- Calcolo: `Prezzo Vendita - Prezzo Acquisto - Fee - Costi`

**Responsabilità**: Utente (FlorenceEGI fornisce report, non calcola imposte).

### Provenance

**Storia proprietà** opera:

- Owner precedenti
- Transazioni (date, prezzi)
- Esposizioni/mostre
- Restauri

**FlorenceEGI**: Provenance on-chain immutabile (ROD).

### Sostituto d'Imposta

Soggetto che **trattiene e versa** imposte per conto contribuente. **FlorenceEGI NON è sostituto d'imposta**:

- Non trattiene fondi
- Non calcola imposte utente
- Non versa per conto terzi

**Responsabilità fiscale**: Sempre utente.

### Transfer

**Trasferimento** proprietà EGI (ASA) tra wallet. Algorand:

- Recipient deve aver fatto **opt-in**
- Costo: ~€0.001
- Trigger automatico: Royalty smart contract (4.5% a Creator)

### Trustless

**Senza fiducia** (non servono intermediari fidati). Smart contract:

- Codice verificabile pubblicamente
- Esecuzione automatica (blockchain)
- Immutabile (nessuna manipolazione post-deploy)

**Esempio**: Royalty FlorenceEGI pagata trustless (nessuno può bloccarla/modificarla).

---

## Note Finali

Questo glossario è **vivo**: Si aggiorna con evoluzioni normative (MiCA, GDPR) e tecniche (Algorand upgrade, nuovi tenant).

**Per approfondimenti**:

- **Normative**: Consulta avvocato specializzato IP/blockchain
- **Tecnici**: Vedi documentazione Algorand, Oracode OS3.0
- **Fiscali**: Consulta commercialista esperto crypto-attività
