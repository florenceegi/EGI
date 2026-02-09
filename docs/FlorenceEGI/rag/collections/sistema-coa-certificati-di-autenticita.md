---
title: "Sistema CoA: Certificati di Autenticità"
category: collections
description: "Guida completa al sistema di Certificati di Autenticità FlorenceEGI per utenti"
author: "Padmin D. Curtis (AI Partner OS3.0)"
version: "1.0.0"
date: 2026-02-08
language: it
---

# Sistema CoA: Certificati di Autenticità FlorenceEGI

## Cos'è un Certificato di Autenticità (CoA)?

Il **Certificato di Autenticità (CoA)** è un documento digitale professionale che attesta l'autenticità, la provenienza e lo stato di conservazione del tuo EGI. Ogni CoA è immutabile, tracciabile e verificabile pubblicamente tramite blockchain.

### Perché è importante?

- **Autenticità garantita**: Certifica che il tuo EGI è originale
- **Tracciabilità completa**: Registra tutta la storia dell'opera (chain of custody)
- **Verifica pubblica**: Chiunque può verificare l'autenticità tramite QR code o numero seriale
- **Valore sul mercato**: Aumenta la credibilità e il valore dell'opera sul mercato secondario

---

## Come Funziona il Sistema CoA

### 1. Struttura del Certificato

Ogni CoA è composto da:

#### **Core Certificate** (Immutabile)
Il certificato principale contiene:
- **Numero Seriale Univoco**: Formato `COA-EGI-YYYY-000###` (es. COA-EGI-2025-000123)
- **Dati dell'Opera**: Titolo, artista, data di creazione
- **Hash di Integrità**: Impronta digitale crittografica per verificare che il certificato non sia stato alterato
- **QR Code**: Per verifica rapida tramite smartphone
- **Data di Emissione**: Timestamp immutabile

#### **Annexes** (Allegati Versionati)
Gli allegati sono documenti aggiuntivi che possono essere aggiornati nel tempo:

1. **Annex A - PROVENANCE** (Provenienza)
   - Storia dell'opera: precedenti proprietari, gallerie, collezioni
   - Documenti di vendita passate
   - **CRITICAL**: Modifiche richiedono ri-emissione del CoA

2. **Annex B - CONDITION** (Stato di Conservazione)
   - Report di restauro
   - Valutazioni dello stato fisico
   - Certificati di conservazione
   - **CRITICAL**: Modifiche richiedono ri-emissione del CoA

3. **Annex C - EXHIBITIONS** (Esibizioni)
   - Lista mostre ed eventi dove l'opera è stata esposta
   - Cataloghi e recensioni
   - **ADDENDUM**: Può essere aggiornato senza ri-emissione

4. **Annex D - PHOTOS** (Fotografie)
   - Foto ad alta risoluzione dell'opera
   - Foto di dettagli specifici
   - Documentazione fotografica
   - **ADDENDUM**: Può essere aggiornato senza ri-emissione

---

## Come Emettere un Certificato di Autenticità

### Requisiti
✅ Devi essere il proprietario dell'EGI
✅ L'EGI deve essere mintato e nella tua collezione
✅ L'EGI deve avere metadati completi (titolo, descrizione, immagini)

### Procedura Guidata

#### Passo 1: Accedi alla Sezione CoA
1. Vai al tuo portfolio EGI
2. Seleziona l'EGI per cui vuoi emettere il CoA
3. Clicca su **"Emetti Certificato di Autenticità"**

#### Passo 2: Compila i Dati dell'Emittente
- **Tipo di Emittente**:
  - `author` = Sei l'artista creatore dell'opera
  - `archive` = Sei un archivio o fondazione
  - `platform` = FlorenceEGI (solo per certificati amministrativi)
- **Nome Emittente**: Il tuo nome o nome dell'ente
- **Luogo di Emissione**: Città/paese dove emetti il certificato

#### Passo 3: Aggiungi Annexes (Opzionale)
Puoi allegare subito documenti aggiuntivi:
- Upload PDF per Provenance (storia dell'opera)
- Upload PDF per Condition (stato di conservazione)
- Upload PDF per Exhibitions (esibizioni passate)
- Upload immagini per Photos (fotografie alta risoluzione)

#### Passo 4: Conferma ed Emetti
- Rivedi i dati inseriti
- Clicca su **"Emetti CoA"**
- Il sistema genera automaticamente:
  - Numero seriale univoco
  - Hash di integrità crittografico
  - QR Code per verifica pubblica
  - PDF del certificato

✅ **Certificato Emesso!** Ora puoi firmarlo come autore e richiedere controfirma da un inspector.

---

## Firme Digitali: Author + Inspector

### Firma dell'Autore (Author)
L'artista creatore firma digitalmente il CoA per attestare l'autenticità dell'opera.

**Come firmare come Autore:**
1. Vai al CoA emesso
2. Clicca su **"Firma come Autore"**
3. Conferma la tua identità (autenticazione)
4. La firma viene registrata con timestamp immutabile

### Controfirma dell'Inspector
Un esperto indipendente (critico d'arte, gallerista, archivio) può controfirmare il CoA per aggiungere ulteriore credibilità.

**Come richiedere controfirma:**
1. Vai al CoA firmato da te
2. Clicca su **"Richiedi Controfirma Inspector"**
3. Inserisci email dell'inspector
4. L'inspector riceverà notifica e potrà controfirmare

**Come controfirmare come Inspector:**
1. Ricevi notifica via email
2. Accedi al CoA tramite link
3. Rivedi il certificato e i dati
4. Clicca su **"Controfirma come Inspector"**
5. La tua firma viene registrata con timestamp

🔒 **Sicurezza**: Le firme sono immutabili e tracciate nella chain of custody.

---

## Come Verificare un Certificato di Autenticità

### Verifica Pubblica (Senza Login)
Chiunque può verificare l'autenticità di un CoA senza bisogno di account FlorenceEGI.

#### Metodo 1: Scansiona QR Code
1. Usa la fotocamera del tuo smartphone
2. Scansiona il QR Code sul certificato PDF
3. Verrai reindirizzato alla pagina di verifica pubblica
4. Visualizza:
   - Dati completi del certificato
   - Stato (valido / revocato)
   - Firme digitali (author + inspector)
   - Chain of custody

#### Metodo 2: Inserisci Numero Seriale
1. Vai su `florenceegi.com/coa/verify`
2. Inserisci il numero seriale (es. `COA-EGI-2025-000123`)
3. Clicca su **"Verifica"**
4. Visualizza tutti i dati del certificato

#### Metodo 3: Verifica Hash
Per verificare che il PDF non sia stato alterato:
1. Vai su `florenceegi.com/coa/verify/hash`
2. Upload il PDF del certificato
3. Il sistema calcola l'hash e lo confronta con quello registrato
4. Risultato: ✅ Certificato Integro oppure ⚠️ Certificato Modificato

### Cosa puoi Verificare
- ✅ Autenticità del certificato
- ✅ Stato (valido o revocato)
- ✅ Data di emissione
- ✅ Firme digitali (chi ha firmato e quando)
- ✅ Chain of custody (tutti i passaggi di proprietà)
- ✅ Integrità del PDF (hash matching)
- ✅ Annexes allegati (se pubblici)

---

## Chain of Custody: Traccia la Storia dell'Opera

La **Chain of Custody** è la cronologia completa di tutti gli eventi che riguardano il CoA e l'opera certificata.

### Eventi Tracciati
Ogni azione viene registrata con:
- Timestamp immutabile
- Utente che ha eseguito l'azione
- Tipo di evento
- Dati aggiuntivi

### Tipi di Eventi

| Evento | Descrizione |
|--------|-------------|
| `issued` | CoA emesso |
| `author_signed` | Firmato dall'autore |
| `inspector_signed` | Controfirmato dall'inspector |
| `signature_removed` | Firma rimossa (richiede motivazione) |
| `annex_added` | Aggiunto nuovo annex |
| `annex_updated` | Annex esistente aggiornato (nuova versione) |
| `location_updated` | Aggiornata ubicazione fisica dell'opera |
| `pdf_generated` | PDF generato |
| `pdf_downloaded` | PDF scaricato |
| `verified` | Certificato verificato pubblicamente |
| `reissued` | CoA ri-emesso (dopo modifica critical annex) |
| `revoked` | CoA revocato |

### Visualizza Chain of Custody
1. Apri il CoA
2. Clicca su **"Chain of Custody"**
3. Visualizza timeline completa con:
   - Data e ora di ogni evento
   - Chi ha eseguito l'azione
   - Dettagli tecnici (hash, versioni)

---

## Gestione Annexes: Aggiorna i Documenti

### Aggiungi un Nuovo Annex
1. Apri il CoA
2. Clicca su **"Gestisci Annexes"**
3. Seleziona il tipo di annex da aggiungere (A, B, C o D)
4. Upload del file PDF o immagine
5. Conferma

### Aggiorna un Annex Esistente
Puoi aggiungere nuove versioni degli annexes:
1. Apri il CoA
2. Vai su **"Gestisci Annexes"**
3. Seleziona l'annex da aggiornare
4. Upload nuova versione del file
5. La vecchia versione viene conservata (versionamento automatico)

⚠️ **IMPORTANTE**:
- **Annexes CRITICAL** (A_PROVENANCE, B_CONDITION): Modifiche richiedono **ri-emissione del CoA** con nuovo numero seriale
- **Annexes ADDENDUM** (C_EXHIBITIONS, D_PHOTOS): Modifiche NON richiedono ri-emissione, vengono semplicemente versionate

### Visualizza Storico Versioni
1. Apri l'annex
2. Clicca su **"Storico Versioni"**
3. Visualizza tutte le versioni precedenti con:
   - Data upload
   - Utente che ha caricato
   - Hash SHA-256
   - Link per download

---

## Bundles: Certifica Collezioni Multiple

I **Bundles** permettono di raggruppare più CoA in un unico certificato collettivo.

### Quando usare i Bundles
- Opere parte di una serie (es. trittico)
- Collection tematica
- Mostra con opere multiple
- Portfolio venduto insieme

### Come Creare un Bundle
1. Emetti CoA individuali per ogni EGI
2. Seleziona i CoA da raggruppare
3. Clicca su **"Crea Bundle"**
4. Assegna un nome al bundle (es. "Trittico delle Stagioni")
5. Il sistema genera un CoA Bundle con:
   - Lista di tutti i CoA inclusi
   - Numero seriale del bundle
   - QR Code per verifica bundle

### Caratteristiche dei Bundles
- ✅ Verifica pubblica del bundle completo
- ✅ Chain of custody condivisa
- ✅ PDF unico che include tutti i CoA
- ✅ Se un CoA viene revocato, il bundle viene invalidato

---

## Scarica il PDF del Certificato

### Genera PDF
1. Apri il CoA
2. Clicca su **"Genera PDF"**
3. Il sistema crea un PDF professionale con:
   - Tutti i dati del certificato
   - QR Code per verifica
   - Firme digitali
   - Logo FlorenceEGI
   - Watermark di sicurezza

### Download PDF
1. Clicca su **"Scarica PDF"**
2. Il PDF viene salvato sul tuo dispositivo
3. Puoi stamparlo o inviarlo via email

⚠️ **IMPORTANTE**: Il PDF contiene un hash crittografico. Se modifichi anche solo un byte del PDF, il hash non corrisponderà più e la verifica fallirà.

### Rigenera PDF
Se aggiorni il CoA (es. aggiungi firme o annexes), rigenera il PDF:
1. Clicca su **"Rigenera PDF"**
2. Il sistema crea una nuova versione aggiornata
3. Tutte le versioni precedenti vengono archiviate

---

## Revoca e Ri-Emissione

### Quando Revocare un CoA
Puoi revocare un CoA in questi casi:
- L'opera è stata distrutta
- Scoperta di falsificazione
- Errore grave nei dati del certificato
- Modifica di CRITICAL Annexes (Provenance, Condition)

### Come Revocare un CoA
1. Apri il CoA
2. Clicca su **"Revoca Certificato"**
3. Inserisci la motivazione della revoca
4. Conferma
5. Il CoA viene marcato come `revoked` con timestamp
6. La revoca è **irreversibile**

### Ri-Emissione dopo Revoca
Se hai revocato un CoA per errore o per modificare critical annexes:
1. Vai al CoA revocato
2. Clicca su **"Ri-Emetti Certificato"**
3. Modifica i dati necessari
4. Il sistema emette un NUOVO CoA con:
   - Nuovo numero seriale
   - Riferimento al CoA revocato (chain of custody)
   - Nuovi hash di integrità

---

## FAQ: Domande Frequenti

### Quanto costa emettere un CoA?
L'emissione del CoA è **gratuita** per tutti gli utenti FlorenceEGI. Fa parte dei servizi inclusi nella piattaforma.

### Posso emettere un CoA per EGI non miei?
No. Puoi emettere CoA solo per EGI che possiedi. Il sistema verifica automaticamente la proprietà.

### Posso modificare un CoA dopo l'emissione?
Il Core Certificate è **immutabile**. Puoi però:
- Aggiungere/aggiornare Annexes
- Aggiungere firme digitali
- Aggiornare ubicazione fisica
- Revocare e ri-emettere (genera nuovo numero seriale)

### Le firme digitali sono legalmente valide?
Le firme digitali FlorenceEGI sono **timestamp immutabili** registrati in blockchain. Non sono firme digitali qualificate (QES) secondo eIDAS, ma hanno valore probatorio come attestazione di autenticità.

### Cosa succede se perdo il PDF del CoA?
Nessun problema! Il CoA è salvato in blockchain e nella piattaforma FlorenceEGI. Puoi:
1. Accedere al CoA dal tuo portfolio
2. Rigenerare il PDF in qualsiasi momento
3. Scaricarlo nuovamente

### Posso verificare un CoA offline?
No. La verifica richiede connessione internet per interrogare il database FlorenceEGI e la blockchain Algorand.

### Gli Annexes sono pubblici?
Dipende dalla tua scelta durante l'upload. Puoi scegliere:
- **Pubblico**: Chiunque può visualizzare l'annex durante la verifica
- **Privato**: Solo tu e chi ha permessi specifici può visualizzarlo

### Posso eliminare un CoA?
No. I CoA non possono essere eliminati per garantire tracciabilità. Puoi però **revocarli** se necessario.

---

## Supporto e Assistenza

Hai bisogno di aiuto con il sistema CoA?

📧 **Contatta il supporto**: support@florenceegi.com
📚 **Documentazione tecnica**: docs.florenceegi.com/coa
💬 **Community**: forum.florenceegi.com

---

**Ultimo aggiornamento**: 2026-02-08
**Versione documentazione**: 1.0.0
