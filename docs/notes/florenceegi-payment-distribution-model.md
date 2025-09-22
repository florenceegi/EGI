# Modello di Allocazione Percentuale dei Proventi per FlorenceEGI

## Architettura del Sistema di Distribuzione

Il sistema di distribuzione dei proventi in FlorenceEGI implementa un meccanismo trasparente e tracciabile per la ripartizione automatica dei ricavi derivanti dalle transazioni di prenotazione e acquisto degli EGI. L'architettura è progettata per garantire massima trasparenza, auditabilità e conformità normativa.

## Meccanismo di Distribuzione

### Flusso Operativo

Quando si verifica una transazione (prenotazione o acquisto di un EGI), il sistema attiva automaticamente il processo di distribuzione percentuale. Per ogni transazione vengono generati multipli record di distribuzione, uno per ciascun beneficiario configurato nella Collection di appartenenza dell'EGI.

#### Compliance GDPR - User Activities Tracking

**Registrazione Obbligatoria**: Per ogni transazione, il sistema genera automaticamente un record nella tabella `user_activities` per garantire la compliance GDPR. Questo record documenta:

-   **Tipo di attività**: "payment_distribution" o "transaction_completed"
-   **Timestamp preciso**: Data e ora dell'operazione per audit trail
-   **User ID**: Identificativo dell'utente coinvolto nella transazione
-   **Metadati transazione**: Riferimenti a reservation_id, collection_id e importi
-   **Consensi privacy**: Stato dei consensi al momento della transazione
-   **IP e User-Agent**: Informazioni tecniche per sicurezza e tracciabilità

Questa implementazione assicura che ogni movimento finanziario sia completamente tracciabile secondo le normative europee sulla protezione dei dati personali.

### Struttura delle Relazioni

La Collection rappresenta l'entità centrale che gestisce le regole di distribuzione. Ogni Collection mantiene una relazione con i wallet dei beneficiari, dove ciascun wallet specifica:

-   L'indirizzo blockchain del beneficiario
-   La percentuale di proventi spettante
-   L'associazione con l'utente proprietario del wallet

## Sistema di Tracciabilità

### Payment Distribution Model

Il cuore della tracciabilità risiede nella tabella `payments_distributions`, progettata per garantire:

**Tracciabilità Completa**: Ogni record documenta una specifica allocazione di proventi, collegando la prenotazione originale con il beneficiario finale attraverso chiavi esterne indicizzate (`reservation_id`, `collection_id`, `user_id`).

**Performance Ottimizzata**: L'indicizzazione strategica dei campi chiave permette interrogazioni rapide per la generazione di report e statistiche in tempo reale.

**Fonte Unica di Verità**: Il valore in EUR rappresenta il riferimento autoritativo per il valore dell'EGI al momento della transazione, includendo il tasso di cambio EUR/ALGO utilizzato.

### Gestione EPP (Environment Protection Project)

Un aspetto distintivo del sistema è la gestione dedicata delle donazioni ambientali. Ogni distribuzione destinata a progetti EPP viene marcata attraverso un campo booleano dedicato, permettendo:

-   Aggregazione rapida delle donazioni ambientali
-   Reporting separato per trasparenza verso la community
-   Certificazione dell'impatto ambientale generato

## Informazioni Contestuali

### Dati della Reservation

La tabella `reservations` fornisce il contesto transazionale completo:

#### Tasso di cambio EUR/ALGO

Fotografa il valore di conversione al momento della transazione

#### Tipologia utente

Sistema multi-tipo che distingue tra:

-   `weak` - Utenti con autenticazione solo wallet
-   `creator` - Artisti e creatori di contenuti
-   `collector` - Collezionisti privati
-   `commissioner` - Collezionisti pubblici con visibilità
-   `company` - Entità aziendali
-   `epp` - Progetti di protezione ambientale
-   `trader-pro` - Operatori professionali del mercato
-   `vip` - Utenti con status privilegiato

#### Status gerarchico

Traccia se la prenotazione è quella vincente (`sub_status`) e la posizione nel ranking (`rank_position`)

### Vantaggi dell'Architettura Multi-Tipo

La classificazione granulare degli utenti permette:

-   **Analytics Segmentate**: Analisi comportamentali per categoria di utente
-   **Strategie Differenziate**: Politiche commerciali personalizzate per tipologia
-   **Reporting Avanzato**: Dashboard specifiche con metriche rilevanti per ogni segmento
-   **Compliance Migliorata**: Gestione differenziata dei requisiti normativi (KYC per trader-pro, verifiche semplificate per collector, etc.)

## Vantaggi del Modello

### Auditabilità Completa

Ogni movimento di valore è documentato e verificabile, dalla transazione originale fino alla distribuzione finale.

### Scalabilità

La struttura supporta l'evoluzione del sistema, permettendo future integrazioni con smart contract per automazione completa on-chain.

### Compliance Ready

Il design rispetta i requisiti di trasparenza fiscale e normativa, facilitando la generazione di documentazione per autorità e stakeholder.

### Analytics Real-Time

L'architettura ottimizzata consente dashboard e analytics in tempo reale su:

-   Andamento delle vendite
-   Distribuzione dei proventi
-   Impatto ambientale
-   Comportamento per tipologia di utente

### Segmentazione Strategica

La tipizzazione degli utenti abilita strategie di marketing e prodotto mirate, ottimizzando conversioni e retention per ogni segmento.

## Considerazioni di Implementazione

### Integrazione Blockchain

Il modello è stato progettato per integrarsi nativamente con l'ecosistema Algorand, preparando il terreno per una futura migrazione verso smart contract che automatizzino completamente il processo di distribuzione, mantenendo però la flessibilità necessaria per operare inizialmente in modalità ibrida (off-chain tracking con settlement on-chain).

### Evoluzione del Sistema

La granularità della tipologia utente garantisce che il sistema possa evolvere per supportare nuovi modelli di business e requisiti normativi senza necessità di refactoring strutturali.

### Tracciabilità e Trasparenza

Ogni transazione genera un audit trail completo che permette:

-   Ricostruzione storica delle distribuzioni
-   Verifica delle percentuali applicate
-   Certificazione dei flussi verso EPP
-   Reportistica per autorità fiscali

## Schema Dati Principale

### Tabella: payments_distributions

| Campo          | Tipo                  | Descrizione                                       |
| -------------- | --------------------- | ------------------------------------------------- |
| reservation_id | Foreign Key           | Collegamento alla prenotazione originale          |
| collection_id  | Foreign Key (indexed) | Collection di appartenenza per query rapide       |
| user_id        | Foreign Key (indexed) | Beneficiario della distribuzione                  |
| user_type      | Enum                  | Tipologia utente (weak, creator, collector, etc.) |
| percentage     | Decimal               | Percentuale di distribuzione                      |
| amount_eur     | Decimal               | Valore in EUR (fonte di verità)                   |
| exchange_rate  | Decimal               | Tasso EUR/ALGO al momento della transazione       |
| is_epp         | Boolean               | Flag per donazioni ambientali                     |
| created_at     | Timestamp             | Data/ora della distribuzione                      |

### Tabella: user_activities (GDPR Compliance)

| Campo            | Tipo                  | Descrizione                                                 |
| ---------------- | --------------------- | ----------------------------------------------------------- |
| user_id          | Foreign Key (indexed) | Utente protagonista dell'attività                           |
| activity_type    | String                | Tipo attività (payment_distribution, transaction_completed) |
| description      | Text                  | Descrizione dettagliata dell'operazione                     |
| metadata         | JSON                  | Dati strutturati (reservation_id, collection_id, amounts)   |
| ip_address       | String                | Indirizzo IP per audit e sicurezza                          |
| user_agent       | Text                  | Browser/app utilizzata                                      |
| privacy_consents | JSON                  | Stato consensi privacy al momento della transazione         |
| created_at       | Timestamp             | Timestamp preciso per audit trail                           |
| updated_at       | Timestamp             | Ultimo aggiornamento del record                             |

### Relazioni Chiave

```
Collection → hasMany → Wallets
Wallet → belongsTo → User
Reservation → hasMany → PaymentDistributions
PaymentDistribution → belongsTo → User
PaymentDistribution → belongsTo → Collection
User → hasMany → UserActivities (GDPR tracking)
UserActivity → belongsTo → User
```

### Compliance GDPR Integrata

Il sistema implementa una **doppia tracciabilità**:

1. **Tracciabilità Finanziaria**: Via `payments_distributions` per audit economici
2. **Tracciabilità GDPR**: Via `user_activities` per compliance privacy

**Vantaggi dell'approccio integrato**:

-   **Audit Trail Completo**: Ogni azione utente è documentata secondo normative EU
-   **Right to Portability**: Esportazione dati user completa e strutturata
-   **Right to Erasure**: Identificazione rapida di tutti i dati da anonimizzare
-   **Consent Management**: Storico consensi per ogni transazione
-   **Security Monitoring**: IP tracking per identificazione attività sospette

## Metriche e KPI

### Per Tipologia Utente

-   Volume transazioni per tipo
-   Valore medio per categoria
-   Tasso di conversione per segmento
-   Retention rate differenziato

### Performance Sistema

-   Tempo medio di distribuzione
-   Accuratezza delle percentuali
-   Volume donazioni EPP
-   Crescita per categoria utente

### Compliance e Audit

-   Completezza record di distribuzione
-   Tracciabilità end-to-end
-   Report fiscali automatizzati
-   Certificazioni ambientali generate
-   **Compliance GDPR**: Copertura attività utente, consensi aggiornati
-   **Audit Privacy**: Tempo risposta richieste dati, anonimizzazioni eseguite
-   **Security Metrics**: Attività sospette rilevate, accessi anomali

---

_Documento tecnico del sistema di distribuzione proventi FlorenceEGI_  
_Versione 1.0 - Seconda Fase_  
_Data: 2025_
