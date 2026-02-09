---
title: "EGI Collection: Item, Minting e Co-Creazione"
category: collections
author: "Padmin D. Curtis (AI Partner OS3.0)"
version: "1.0.0"
date: 2026-02-09
language: it
---

# EGI Collection: Item, Minting e Co-Creazione

> Documento che spiega il modello di co-creazione degli EGI, la differenza tra Item ed EGI, le Collection, il processo di minting e gli EGI Asset.

---

## 1. IL MODELLO DI CO-CREAZIONE

> "Non basta creare. Occorre che qualcuno accolga."

L'EGI si fonda su un modello a tre ruoli inscindibili:

### Artista/Creatore (La Sorgente Creativa)
- Autore originale dell'opera
- Conserva **sempre** i diritti morali (paternità, integrità)
- Conserva **sempre** i diritti patrimoniali (copyright)
- Riceve royalty automatiche ad ogni rivendita (4.5%)
- Rimane per sempre riconosciuto come creatore

### Co-Creatore (La Causa Efficiente)
- Chi attiva l'opera attraverso il minting
- Diventa parte indelebile dell'identità dell'EGI sulla blockchain
- La sua firma resta visibile per sempre, anche se l'opera viene rivenduta
- Trasforma il bisogno di visibilità in una struttura di guarigione digitale
- **Può essere lo stesso creator** (auto-minting) o un collector

### Collezionista (Il Custode del Valore)
- Custode del valore che tramanda l'opera
- Acquisisce il possesso fisico/digitale dell'opera
- Diritto al godimento privato
- Diritto alla rivendita (con royalty al Creatore)
- **NON** acquisisce copyright né diritti di riproduzione

**Flusso di Co-Creazione:**
```
Creatore → Co-Creatore (Minting) → Collezionista → Memoria Permanente Blockchain
```

> "Il Co-Creatore rimane per sempre legato all'opera. Anche se venduta, la sua firma resta visibile nella blockchain. La visibilità non è più sintomo di bisogno, ma conseguenza naturale della partecipazione autentica."

---

## 2. ITEM, COLLECTION E MINTING

### L'Item

L'**Item** è l'entità che rappresenta l'EGI sulla piattaforma e raggruppa tutti i suoi elementi. È il termine tecnico per identificare l'entità gestita dal software. Gli item possono essere:
- Coinvolti in una **drop**
- Trasferiti internamente tra le collection di un utente
- Inviati ad altri utenti tramite email

### Le Collection

- Il creator raggruppa gli item all'interno delle **collection**
- La creazione di una collection è concepita per essere semplice e veloce
- Ogni collection include un EPP, una utility (se applicabile) e un prezzo base
- **Ereditarietà**: gli item caricati in una collection ereditano automaticamente l'EPP e la utility associati alla collection stessa

### Differenza tra Item ed EGI

- L'**item** rappresenta l'EGI nella collection ed è il termine tecnico per l'entità software
- Durante il **minting**, viene registrato l'**EGI**, mentre l'item funge da involucro organizzativo
- **Item = Container pre-minting** | **EGI = Asset on-chain post-minting**

---

## 3. PROCESSO DI MINTING

Il minting è il processo che trasforma un item in un EGI registrato su blockchain.

### Step 1: Creazione Item
1. Il creator carica l'elemento digitale (immagine, video, audio, ebook, documento)
2. Compila i metadata (titolo, descrizione, traits)
3. L'item viene salvato nella collection

### Step 2: Configurazione Collection
1. Il creator sceglie l'EPP da collegare
2. Definisce l'utility (se applicabile)
3. Imposta il prezzo base
4. Configura delivery policy (DIGITAL_ONLY, PHYSICAL_ALLOWED, PHYSICAL_REQUIRED)

### Step 3: Minting (Registrazione on-chain)
1. Il co-creatore (può essere il creator stesso o un collector) inizia il processo di minting
2. La piattaforma genera lo smart contract ARC-72
3. L'elemento digitale principale viene uploadato su IPFS
4. L'URL IPFS viene registrato nello smart contract
5. Tutti gli altri metadata vengono salvati su IPFS
6. Lo smart contract viene deployato su blockchain Algorand
7. L'EGI riceve un ID univoco

### Step 4: Prima Vendita (Opzionale)
1. Se il co-creatore NON è il creator, avviene la prima vendita
2. La distribuzione dei pagamenti avviene automaticamente (68% creator, 20% EPP, 10% Natan, 2% Frangette)
3. L'EGI passa dal creator al co-creatore/collector
4. Il creator mantiene i diritti e le royalty future

---

## 4. EGI ASSET (Collection Mintata)

L'**EGI Asset** è la versione collezione di un EGI. Una collection può essere mintata come EGI autonomo e venduta come entità unica, trasferendo la proprietà di tutti gli EGI contenuti al nuovo acquirente.

### Vantaggi degli EGI Asset

- **Vendita in blocco**: Vendere un'intera collezione come singolo asset
- **Suddivisione guadagni**: Distribuire i profitti tra più soggetti
- **Tokenizzazione aziendale**: Tokenizzare segmenti aziendali o progetti
- **Royalty automatizzate**: Attivare modelli di business basati su royalty

### Caso d'Uso

Immagina un creator che ha creato 50 EGI in una collection "Summer Vibes 2025":
- Può mintare l'intera collection come EGI Asset
- Venderla a un collector come entità unica
- Il collector acquisisce la proprietà di tutti i 50 EGI in un solo acquisto
- Può poi decidere di:
  - Tenere la collection unita
  - Vendere singoli EGI separatamente
  - Rivendere l'intera collection a un altro buyer

---

## 5. PRE-LAUNCH E RESERVATION

Prima del minting ufficiale, gli EGI possono essere prenotati tramite **pre-launch reservation**.

### Come Funziona

1. **Annuncio Drop**: Il creator annuncia il lancio di una collection
2. **Finestra Reservation**: Gli utenti possono prenotare EGI specifici
3. **Ranking Pubblico**: Le prenotazioni sono visibili pubblicamente
4. **Mint Window**: Una volta aperto il minting, chi ha prenotato ha 48h per completare l'acquisto
5. **Supersede Logic**: Se chi ha prenotato non completa, il secondo in fila può subentrare

### Vantaggi Reservation

- **Per il Creator**: Misura l'interesse prima del drop
- **Per il Collector**: Garantisce l'accesso a EGI desiderati
- **Weak Auth Support**: Anche utenti guest possono prenotare (con email)

---

## 6. DIRITTI E PROPRIETÀ

### Cosa Acquista il Collector

Quando acquisti un EGI, ottieni:
- ✅ **Proprietà dell'asset digitale**: L'EGI è tuo
- ✅ **Diritto di godimento privato**: Puoi usarlo, mostrarlo, custodirlo
- ✅ **Diritto di rivendita**: Puoi venderlo sul mercato secondario
- ✅ **Accesso alle utility**: Benefici pratici collegati all'EGI
- ✅ **Possesso fisico** (se EGI fisico): Ricevi l'oggetto fisico certificato

### Cosa NON Acquista il Collector

Quando acquisti un EGI, NON ottieni:
- ❌ **Copyright**: I diritti d'autore restano al creator
- ❌ **Diritto di riproduzione**: Non puoi duplicare o riprodurre commercialmente l'opera
- ❌ **Diritto di modifica**: Non puoi modificare l'opera originale
- ❌ **Diritto di merchandising**: Non puoi creare prodotti derivati
- ❌ **Licenza commerciale**: Non puoi usare l'opera per scopi commerciali senza permesso

---

## 7. TRASFERIMENTI E GIFT

### Trasferimento Interno

Gli EGI possono essere trasferiti tra collection dello stesso utente:
- Gratuito (nessuna fee)
- Immediato
- Non genera royalty
- Non cambia owner on-chain

### Gift (Invio a Terzi)

Gli EGI possono essere inviati come gift a altri utenti:
- Tramite email o indirizzo Algorand
- Genera royalty come una vendita (se configurato)
- Cambio di owner on-chain
- Notification al destinatario

---

## 8. BURN (Distruzione)

Il creator o l'owner può decidere di "bruciare" (distruggere) un EGI:

- **Irreversibile**: Una volta bruciato, l'EGI non può essere recuperato
- **On-chain**: La distruzione è registrata sulla blockchain
- **Scarsità**: Riduce il supply totale, potenzialmente aumentando il valore degli altri EGI della collection
- **Motivazioni comuni**:
  - Errore nella creazione
  - Aumento della scarsità
  - Ritiro volontario dal mercato

---

## 9. BEST PRACTICES PER CREATORS

### Organizzazione Collection

1. **Tematica chiara**: Raggruppa EGI con concept coerente
2. **Numero limitato**: Non sovraccaricare una collection
3. **Prezzo base realistico**: Considera il mercato e il tuo posizionamento
4. **EPP rilevante**: Scegli un progetto ambientale in linea con la tua opera
5. **Utility interessante**: Offri vantaggi concreti ai collector

### Prima del Minting

1. **Metadata completi**: Titolo, descrizione, traits accurati
2. **Immagini di qualità**: Cover e preview professionali
3. **Testing**: Verifica che tutto funzioni prima del drop pubblico
4. **Comunicazione**: Annuncia il drop sui tuoi canali
5. **Pre-launch reservation**: Considera di usarla per generare hype

### Dopo il Minting

1. **Community engagement**: Interagisci con i collector
2. **Delivery delle utility**: Mantieni le promesse fatte
3. **Trasparenza**: Comunica qualsiasi cambiamento
4. **Royalty tracking**: Monitora le rivendite e i guadagni
5. **Espansione**: Continua a creare valore per la collection

---

> *Il modello di co-creazione trasforma il minting in un atto collaborativo che valorizza creators, co-creators e collectors in modo equo e trasparente.*
