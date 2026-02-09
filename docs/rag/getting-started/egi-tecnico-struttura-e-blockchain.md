---
title: "EGI Tecnico: Struttura e Blockchain"
category: getting-started
author: "Padmin D. Curtis (AI Partner OS3.0)"
version: "1.0.0"
date: 2026-02-09
language: it
---

# EGI Tecnico: Struttura e Blockchain

> Documento che spiega la struttura tecnica degli EGI, i componenti essenziali, le funzioni fondamentali e i vantaggi tecnologici della blockchain Algorand con standard ARC-72.

---

## 1. STRUTTURA TECNICA

Un EGI è uno **Smart Contract NFT ARC-72 su blockchain Algorand**, contenente:
- Un'opera dell'ingegno umano (Invent)
- Una Good (bene/servizio) con valore tangibile
- Il collegamento a un EPP

### Tipologie di Elemento Digitale
Un EGI può essere rappresentato da:
- **Immagine**: opera d'arte digitale o rappresentazione di un bene fisico
- **Brano musicale inedito**: non coperto da SIAE
- **Ebook**: testo in formato digitale
- **Video**: contenuto audiovisivo originale

### Tipologie di Flusso
- **EGI fisico**: beni fisici certificati su blockchain con verifica QR/NFC
- **EGI pt (Pure Trading)**: asset digitali nativi per il trading con liquidità rapida
- **EGI Documento**: notarizzazione di documenti e contratti con hash crittografico immutabile + timestamp

### Metadata
Ogni EGI è corredato dai seguenti metadata:
- Nome del file
- Autore
- Nome attribuito all'EGI
- Data
- Descrizione
- Prezzo
- Proprietà, tag o **traits** (tratti distintivi)

### Cover e Secondo Elemento Digitale
Se l'EGI è un libro, un brano musicale o un video, include una **cover** sotto forma di immagine, che funge da secondo elemento digitale per rappresentare visivamente il contenuto.

---

## 2. I TRE COMPONENTI ESSENZIALI

Un EGI è tale **solo se** include tutti e tre i seguenti elementi:

| Componente | Descrizione |
|---|---|
| **EPP** (Environment Protection Program) | Progetto ambientale certificato collegato all'EGI |
| **Elemento Digitale** (Good) | Immagine, libro, brano musicale, video o documento |
| **Utility** | Vantaggio o funzione associata all'EGI |

**Eccezione**: se l'EGI è la rappresentazione digitale di un bene fisico, l'utility non è obbligatoria. In tutti gli altri casi, l'EGI deve includere un'utility per garantire una consistenza fisica oltre che digitale, distinguendolo dai tradizionali NFT.

---

## 3. LE QUATTRO FUNZIONI FONDAMENTALI

1. **Certificazione**: Registra l'identità unica dell'asset sulla blockchain Algorand
2. **Tokenizzazione**: Trasforma il bene in un asset liquido scambiabile globalmente
3. **Automazione**: Distribuisce istantaneamente royalties e pagamenti via Smart Contracts
4. **Rigenerazione**: Destina automaticamente una quota (20%) all'ambiente

---

## 4. VANTAGGI TECNOLOGICI (ARC-72 su Algorand)

### Perché ARC-72?
- Smart contract personalizzabili e flessibili
- Gestione completa dei metadati e diritti
- Maggiore sicurezza e interoperabilità rispetto ad ASA standard
- Finalità delle transazioni in **4.5 secondi**
- Costi accessibili, molto inferiori al copyright tradizionale

### Perché Algorand?
Algorand è stata scelta per la blockchain degli EGI per motivi tecnici, economici ed ecologici:

**Velocità**:
- Conferma delle transazioni in 4.5 secondi
- Nessun ritardo, nessuna incertezza
- Esperienza utente fluida e immediata

**Costi**:
- Fee microscopiche (frazioni di centesimo)
- Molto più economico del copyright tradizionale
- Accessibile anche per piccoli artisti e creators

**Sostenibilità**:
- Blockchain carbon-negative
- Consuma meno energia di una ricerca Google
- Perfettamente allineata con i valori ambientali degli EGI (pilastro "E")

**Sicurezza**:
- Consensus mecanism Pure Proof of Stake
- Finalità immediata (nessun fork)
- Massima sicurezza per asset di valore

**Partnership Istituzionali**:
- SIAE (Società Italiana Autori ed Editori) utilizza Algorand per la gestione del copyright
- Riconoscimento istituzionale della blockchain
- Validità legale rafforzata

---

## 5. MINTING E ARCHIVIAZIONE

Durante il minting, tutti gli elementi vengono processati:
- Solo l'URL dell'elemento digitale principale viene registrato come token su blockchain
- Tutti gli altri componenti vengono salvati su server IPFS come metadata (inclusa l'URL della cover e delle immagini associate all'utility)

### IPFS (InterPlanetary File System)
- Sistema di archiviazione decentralizzata
- I file sono distribuiti su una rete globale
- Content addressing: ogni file ha un hash unico
- Immutabilità: il contenuto non può essere modificato
- Resilienza: i file rimangono disponibili anche se alcuni nodi si spengono

---

## 6. SMART CONTRACT ARC-72

### Cosa Contiene lo Smart Contract

Ogni EGI è uno smart contract che contiene:

1. **ID univoco**: Identificatore unico sulla blockchain
2. **Owner address**: Indirizzo Algorand del proprietario attuale
3. **Creator address**: Indirizzo Algorand del creatore originale (immutabile)
4. **Metadata URL**: Link IPFS ai metadata completi
5. **Royalty percentage**: Percentuale royalty per rebind (4.5%)
6. **EPP address**: Indirizzo del progetto ambientale collegato

### Funzioni dello Smart Contract

- **Transfer**: Trasferisce la proprietà da un indirizzo a un altro
- **Burn**: Distrugge l'EGI (opzionale, irreversibile)
- **Update metadata**: Permette l'aggiornamento di alcuni metadata (solo dal creator)
- **Royalty distribution**: Distribuisce automaticamente le royalty al rebind

---

## 7. IMMUTABILITÀ E MODIFICABILITÀ

### Cosa È Immutabile

Una volta mintato, questi elementi **NON** possono essere modificati:
- ID dell'EGI
- Creator address
- Timestamp di creazione
- Hash del file principale (se registrato on-chain)
- Collegamento EPP

### Cosa Può Essere Modificato

Il creator può aggiornare (prima della prima vendita):
- Metadata descrittivi (title, description)
- Prezzo
- Traits e proprietà
- Immagine di cover (per libri, musica, video)

Dopo la prima vendita, solo il nuovo owner può decidere il prezzo di rivendita.

---

## 8. VERIFICA E AUTENTICITÀ

### Come Verificare un EGI

1. **Verifica on-chain**: Controlla l'esistenza sulla blockchain Algorand
2. **Verifica IPFS**: Verifica l'integrità dei file su IPFS
3. **Verifica CoA**: Controlla il Certificate of Authenticity (se emesso)
4. **Verifica QR Code**: Scansiona il QR per verificare l'autenticità (EGI fisici)

### Chain of Custody

Ogni EGI ha una chain of custody completa:
- Cronologia di tutti i trasferimenti
- Timestamp di ogni operazione
- Indirizzi di tutti i proprietari passati
- Eventi di rebind con royalty distribuite

---

## 9. TOKENOMICS E DISTRIBUZIONE

### Al Minting (Prima Vendita)

Quando un EGI viene mintato e venduto per la prima volta:

**Per Creator (68% del prezzo)**:
- 68% va direttamente al creator
- 20% va all'EPP collegato
- 10% va a Natan (piattaforma)
- 2% va a Frangette (marketplace)

**Per Company (90% del prezzo)**:
- 90% va direttamente alla company
- 10% va a Natan (piattaforma)

### Al Rebind (Rivendita)

Quando un EGI viene rivenduto sul mercato secondario:

**Per Creator**:
- 4.5% del prezzo di rivendita va automaticamente al creator originale
- 0.8% va all'EPP
- 0.7% va a Natan
- 0.1% va a Frangette
- Il resto (93.9%) va al venditore

**Per Company**:
- 4.6% del prezzo di rivendita va automaticamente alla company originale
- 0.8% va all'EPP
- 0.7% va a Natan
- 0.1% va a Frangette
- Il resto (93.8%) va al venditore

Questa distribuzione è gestita **automaticamente** dallo smart contract, senza possibilità di evasione.

---

## 10. INTEROPERABILITÀ

Gli EGI sono progettati per essere interoperabili:

- **Wallet Algorand**: Qualsiasi wallet Algorand può custodire EGI
- **Marketplace**: Possono essere listati su marketplace Algorand compatibili
- **Piattaforme Florence**: Riconosciuti in tutto l'ecosistema Florence
- **Standard ARC-72**: Compatibili con tutti i tool che supportano ARC-72

---

## 11. SICUREZZA

### Protezione della Proprietà

- **Blockchain immutabile**: La proprietà è registrata in modo permanente
- **Crittografia**: Tutte le transazioni sono crittograficamente sicure
- **Consensus decentralizzato**: Nessuna singola entità può modificare i record
- **Backup automatico**: La blockchain è replicata su migliaia di nodi

### Protezione dei File

- **IPFS distribuito**: I file sono replicati su più nodi
- **Content addressing**: L'hash garantisce l'integrità del contenuto
- **Backup regolare**: Copie di sicurezza su server dedicati
- **CDN**: Distribuzione geografica per alta disponibilità

---

> *La tecnologia degli EGI è progettata per essere sicura, veloce, economica ed ecologica, garantendo la massima protezione per creators e collectors.*
