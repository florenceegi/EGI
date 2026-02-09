# COMPENDIO COMPLETO DEGLI EGI (Ecological Goods Invent)

> Documento unificato che raccoglie e integra tutte le definizioni, specifiche tecniche, aspetti legali e casi d'uso degli EGI, attingendo dalla documentazione ufficiale, dal manifesto, dai contenuti della piattaforma e dal codice sorgente.

---

## 1. DEFINIZIONE FONDAMENTALE

Un **EGI (Ecological Goods Invent)** è un'entità digitale unica che rappresenta un certificato di proprietà su blockchain, progettato per combinare valore ambientale, culturale e utilità pratica.

> "EGI trasforma prodotti, opere, creazioni — tutto ciò che nasce dall'ingegno umano — in asset autentici, tracciabili e sostenibili."

In termini semplici: **un EGI trasforma qualcosa che già esiste in un bene che può generare valore nel tempo.** È un involucro digitale che custodisce un'opera, un'idea o un prodotto, arricchendolo con attributi distintivi e un certificato di autenticità immutabile, e può connetterlo a innumerevoli utility personalizzate.

> "Immagina di avvolgere la tua opera con un sottile strato di accessori digitali che la aiutano a incontrare la domanda del mercato. Un EGI non è solo un certificato, ma un 'contenitore' attivo a cui puoi attaccare moduli vitali: Certificati (CoA), Utility, Proprietà e Protocolli Ambientali (EPP)."

---

## 2. I TRE PILASTRI: E – G – I

L'acronimo EGI racchiude i tre elementi inscindibili che lo definiscono:

### E — Environment (Ambiente)
Ogni EGI è collegato a un **EPP (Environment Protection Program/Project)**: un progetto ambientale verificato che genera impatto positivo misurabile. Esempi:
- Riforestazione
- Rimozione della plastica dagli oceani
- Protezione della biodiversità

Il 20% del valore di ogni transazione finanzia automaticamente un EPP tramite donazioni on-chain.

### G — Goods (Beni)
Ogni EGI certifica la proprietà di un **bene o servizio concreto**. La gamma è vastissima:
- **Arte** (fisica e digitale): dipinti, sculture, fotografie, video, installazioni
- **Design e artigianato**: oggetti unici, prodotti di design, creazioni artigianali
- **Prodotti creativi**: musica, contenuti digitali, creazioni multimediali
- **Documenti**: contratti, atti notarili, documenti PA, certificati
- **Proprietà intellettuale**: progetti, invenzioni, idee documentate
- **Beni fisici certificati**: qualsiasi prodotto autentico con valore documentabile
- **Asset ibridi**: connessione fisico-digitale tramite QR/NFC

### I — Invent (Ingegno)
Ogni EGI è **frutto della creatività umana**, mai generato completamente da un'intelligenza artificiale. Rappresenta:
- La firma creativa e la visione dell'autore
- Unicità e originalità certificate
- Provenienza e storia tracciabili
- Riconoscimento permanente del Creatore

---

## 3. STRUTTURA TECNICA

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

## 4. I TRE COMPONENTI ESSENZIALI

Un EGI è tale **solo se** include tutti e tre i seguenti elementi:

| Componente | Descrizione |
|---|---|
| **EPP** (Environment Protection Program) | Progetto ambientale certificato collegato all'EGI |
| **Elemento Digitale** (Good) | Immagine, libro, brano musicale, video o documento |
| **Utility** | Vantaggio o funzione associata all'EGI |

**Eccezione**: se l'EGI è la rappresentazione digitale di un bene fisico, l'utility non è obbligatoria. In tutti gli altri casi, l'EGI deve includere un'utility per garantire una consistenza fisica oltre che digitale, distinguendolo dai tradizionali NFT.

---

## 5. LE QUATTRO FUNZIONI FONDAMENTALI

1. **Certificazione**: Registra l'identità unica dell'asset sulla blockchain Algorand
2. **Tokenizzazione**: Trasforma il bene in un asset liquido scambiabile globalmente
3. **Automazione**: Distribuisce istantaneamente royalties e pagamenti via Smart Contracts
4. **Rigenerazione**: Destina automaticamente una quota (20%) all'ambiente

---

## 6. IL MODELLO DI CO-CREAZIONE

> "Non basta creare. Occorre che qualcuno accolga."

L'EGI si fonda su un modello a tre ruoli inscindibili:

### Artista/Creatore (La Sorgente Creativa)
- Autore originale dell'opera
- Conserva **sempre** i diritti morali (paternità, integrità)
- Conserva **sempre** i diritti patrimoniali (copyright)

### Co-Creatore (La Causa Efficiente)
- Chi attiva l'opera attraverso il minting
- Diventa parte indelebile dell'identità dell'EGI sulla blockchain
- La sua firma resta visibile per sempre, anche se l'opera viene rivenduta
- Trasforma il bisogno di visibilità in una struttura di guarigione digitale

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

## 7. ITEM, COLLECTION E MINTING

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

### Minting e Archiviazione
Durante il minting, tutti gli elementi vengono processati:
- Solo l'URL dell'elemento digitale principale viene registrato come token su blockchain
- Tutti gli altri componenti vengono salvati su server IPFS come metadata (inclusa l'URL della cover e delle immagini associate all'utility)

### EGI Asset (Collection Mintata)
L'**EGI Asset** è la versione collezione di un EGI. Una collection può essere mintata come EGI autonomo e venduta come entità unica, trasferendo la proprietà di tutti gli EGI contenuti al nuovo acquirente. Permette:
- La vendita in blocco di intere collezioni
- La suddivisione dei guadagni tra più soggetti
- La tokenizzazione di segmenti aziendali o progetti
- L'attivazione di modelli di business basati su royalty automatizzate

---

## 8. CARATTERISTICHE UNICHE

### Identità Sovrana
L'EGI possiede se stesso. Non dipende da marketplace centralizzati.

### Perpetuità
Vive per sempre sulla blockchain, indipendente dalla durata dell'asset fisico.

### Universalità
Standard compatibile con ogni piattaforma dell'ecosistema Florence.

### Permanenza Digitale
Anche dopo la vendita, l'opera rimane visibile nella galleria dell'artista.

### Attribuzione Inviolabile
L'artista resta per sempre riconosciuto come autore.

### Royalties Automatiche
Con ogni Rebind (rivendita), l'artista continua a guadagnare (4.5% smart contract).

### Impatto Reale
Il 20% del valore di ogni transazione finanzia un EPP.

---

## 9. VANTAGGI TECNOLOGICI (ARC-72 su Algorand)

- Smart contract personalizzabili e flessibili
- Gestione completa dei metadati e diritti
- Maggiore sicurezza e interoperabilità rispetto ad ASA standard
- Finalità delle transazioni in **4.5 secondi**
- Costi accessibili, molto inferiori al copyright tradizionale

---

## 10. FONDAMENTO LEGALE

### Normativa Italiana
**Art. 8-ter, Decreto Semplificazioni DL 135/2018, convertito in L. 12/2019:**

> La memorizzazione di un documento su blockchain produce effetti analoghi alla validazione temporale elettronica secondo il Regolamento eIDAS (UE 910/2014).

### Perché "Egizzare" è Importante
- **Costo accessibile**: molto meno del copyright tradizionale, senza burocrazia
- **Immediatezza**: 4.5 secondi (Algorand) vs settimane/mesi di procedure amministrative
- **Registro Pubblico Globale**: visibile e verificabile da chiunque, ovunque nel mondo
- **Forte Prova di Anteriorità**: riconosciuta dalla normativa italiana come validazione temporale
- **Partnership Istituzionale**: la SIAE stessa utilizza Algorand per la gestione del copyright

### Vantaggi Legali
- **Sicurezza giuridica**: validità legale garantita da quadri contrattuali reali
- **Trasparenza totale**: ogni trasferimento di proprietà è pubblico e immutabile
- **Impatto reale**: contributo all'ambiente semplicemente possedendo e usando l'asset

---

## 11. PERCHÉ EGI E NON NFT

La parola "NFT" è oggi associata a speculazione e truffe. Gli EGI segnano un **cambio di paradigma**: un ritorno al valore reale, alla concretezza, alla bellezza, alla cura.

| | NFT Tradizionale | EGI |
|---|---|---|
| Valore | Spesso puramente speculativo | Bene/servizio concreto + impatto ambientale |
| Ambiente | Nessun collegamento | EPP obbligatorio (20% delle transazioni) |
| Utility | Opzionale | Necessaria (tranne per beni fisici) |
| Autore | Spesso anonimo | Sempre riconosciuto e permanente |
| Royalties | Non garantite | Automatiche via smart contract (4.5%) |
| Origine | Può essere IA pura | Sempre frutto dell'ingegno umano |
| Permanenza | Dipende dal marketplace | Identità sovrana sulla blockchain |

**EGI significa prendersi cura**: dell'ambiente, della società, dell'economia, dell'iniziativa umana, dell'arte e della cultura.

---

## 12. CASI D'USO CONCRETI

### Cosa può diventare un EGI
- Un'idea che vuoi proteggere e monetizzare
- Un'opera d'arte che continua a generare valore dopo la vendita
- Un oggetto da collezione che cresce di valore nel tempo
- Un prodotto che guadagna anche sulle rivendite
- Un documento che diventa certificato, verificabile e permanente

### Esempi Pratici per Settore
- **Dentista**: EGI con diritto VIP a prestazioni gratuite per i possessori
- **Ristoratore**: accesso a menu segreti, eventi esclusivi, degustazioni
- **Scrittore**: versioni limitate di libri, copie autografate, contenuti inediti
- **Artista**: collezione di opere digitali collegate a quadri fisici unici
- **Pubblica Amministrazione**: certificazione di atti e documenti tramite NATAN-LOC

### Cosa si può fare con un EGI
- **Trading e Vendita**: vendere l'EGI su marketplace secondari, intero o in frazioni
- **Collateralizzazione**: usare l'EGI come garanzia per ottenere liquidità (DeFi)
- **Access Pass**: l'EGI garantisce l'accesso a eventi esclusivi o servizi riservati
- **Royalties Passive**: guadagnare automaticamente dalle rivendite future (Creator Economy)

### Tipologie di EGI
- Opera d'arte certificata (digitale o fisica)
- Documento firmato tra più soggetti (CoA, contratti)
- Carta utility per servizi esclusivi
- Token commerciale per aziende
- Oggetto collezionabile limitato e permanente
- Elemento narrativo di universi creativi

---

## 13. L'ECOSISTEMA: PIATTAFORME CHE MANIPOLANO GLI EGI

Gli EGI sono lo standard comune dell'ecosistema Florence. Le piattaforme di manipolazione sono i sistemi in grado di gestire e valorizzare gli EGI:

| Piattaforma | Funzione |
|---|---|
| **Florence Art EGI** | Arricchisce le opere d'arte con CoA, Utility, Traits e sistemi di marketing avanzati |
| **NATAN-LOC** | Certifica e autentica atti della Pubblica Amministrazione. Ogni documento diventa un EGI certificato on-chain |
| **EGI-PT** | Piattaforma di donazione gratuita e circolazione senza fee di cloni digitali unici di EGI |

---

## 14. I TRE PILASTRI DELL'ECOSISTEMA FLORENCE

1. **Concretezza**: Beni reali, valore tangibile. Ogni EGI rappresenta qualcosa che esiste nel mondo fisico.
2. **Equilibrium**: Equilibrio tra economia e ambiente. Ogni transazione contribuisce alla protezione ambientale.
3. **Accessibilità e Costo Zero**: Tecnologia semplice per tutti. Inizia a certificare i tuoi asset senza costi anticipati.

---

## 15. GLOSSARIO ESSENZIALE

| Termine | Definizione |
|---|---|
| **EGI** | Ecological Goods Invent — asset digitale unico con EPP + Good + Creatività |
| **EPP** | Environment Protection Program/Project — progetto ambientale verificato |
| **Item** | Entità software che rappresenta l'EGI sulla piattaforma |
| **Collection** | Raggruppamento di item con EPP e utility condivisi |
| **EGI Asset** | Collection mintata come EGI autonomo, vendibile come entità unica |
| **Minting** | Processo di registrazione dell'EGI su blockchain |
| **Rebind** | Rivendita di un EGI con distribuzione automatica delle royalties |
| **CoA** | Certificate of Authenticity — certificato di autenticità |
| **Traits** | Tratti distintivi / proprietà / tag associati a un EGI |
| **Drop** | Evento di lancio/vendita di EGI |
| **ARC-72** | Standard smart contract NFT su Algorand utilizzato dagli EGI |
| **IPFS** | InterPlanetary File System — sistema di archiviazione decentralizzata per i metadata |
| **Egizzare** | Atto di creare un EGI a partire da un bene esistente |

---

> *Questo compendio è stato compilato integrando le informazioni contenute in: Definizione e spiegazioni di EGI.md, MANIFESTO DEGLI EGI.md, 01_Fondamenti_e_Visione.md, 09_Glossario_Completo.md, homepage.ts, translations.ts, WhatIsEgiPage.tsx e info_egi.php.*
