	# Certificati di Autenticità (CoA) - Requisiti e Implementazione per FlorenceEGI

## 🎯 Premessa: CoA nel Mondo Reale

Nella pratica, i CoA sono più snelli di quanto spesso si pensi. Questo documento definisce i **requisiti minimi effettivi** basati su prassi consolidate del mercato dell'arte e le specifiche implementative per FlorenceEGI.

---

## 📋 Minimo "Reale" - Quasi Sempre Presente

**La "rosa corta" che vedi davvero nei CoA professionali:**

### Dati Essenziali dell'Opera
- **Nome artista**
- **Titolo dell'opera**
- **Anno di realizzazione**
- **Tecnica / supporto** (es. "olio su tela", "gelatina ai sali d'argento su carta...")
- **Dimensioni** (cm; e/o formato stampa)
- **Edizione** *(solo se multiplo/print)*: numero e tiratura (es. 3/20)

### Certificazione
- **Dichiarazione di autenticità** in chiaro
- **Firma dell'artista o dell'ente** + **data** (a volte timbro)

> *Questa è la "rosa corta" raccomandata da guide operative e marketplace come Jackson's Art, Format e Saatchi Art.*

---

## 🔄 Spesso Presente ma Non Sempre

**Elementi aggiuntivi che migliorano il CoA:**

- **Foto dell'opera** sul certificato (aiuta l'identificazione)
- **Contatti dell'artista/galleria** (email/sito)
- **Consigli base di conservazione/esposizione**
- **Provenienza/mostre/pubblicazioni** in breve, se già note
- **Elementi antifrode** (seriale, QR/URL di verifica, ologramma)

---

## 🇮🇹 Nota Italia - Contesto Legale

**In Italia non esiste un formato legale standard** del CoA: il contenuto è di prassi. 

Tuttavia, l'**art. 64 del Codice dei Beni Culturali (D.Lgs. 42/2004)** impone ai **professionisti** (gallerie, case d'asta, intermediari) di consegnare all'acquirente documentazione che attesti:
- **Autenticità o probabile attribuzione**
- **Provenienza**

Nei CoA italiani spesso compaiono:
- **Immagine dell'opera**
- **Titolo, anno, tecnica**
- **Indicazione della firma**
- **Dichiarazione veritiera** con **sottoscrizione**

---

## 📄 Template Essenziale (Una Pagina)

```
Certificato di Autenticità

• Artista: …
• Titolo: …
• Anno: …
• Tecnica / supporto: …
• Dimensioni: …
• Edizione (se applicabile): …
• Dichiarazione: "Attesto che l'opera sopra descritta è autentica 
  e realizzata da me / dal suddetto artista."
• Luogo e data: …
• Firma (mano o digitale qualificata): …

(Opzionali) 
• Foto dell'opera
• Serial-QR di verifica
• Contatti
```

---

## ✅ Analisi FlorenceEGI: Cosa Hai e Cosa Manca

### Quello che Hai Va Bene

- ✅ **Autore + bio condensata** → ok (bio come allegato/appendice)
- ✅ **Titolo + descrizione** → ok
- ✅ **Data di realizzazione** → ok
- ✅ **Dimensioni** → ok
- ✅ **Immagine master + set foto** → ottimo. Per fisico: 6–12 scatti da angolazioni standard + dettaglio firma/etichette
- ✅ **Tecnica/supporto nei traits** → ok, ma **congela** i traits al momento del CoA
- ✅ **Edizione nei traits** → ok (indica "unico" se non è multiplo)
- ✅ **Dichiarazione di autenticità in chiaro** → ok

### 🚨 Aggiungi il Minimo che Fa la Differenza

#### 1. **Firma** ⚠️ CRITICO

**❌ NO:** Una "dichiarazione in chiaro sul web senza firma" **non basta**.

**✅ Minimo accettato nel mercato:**
- **Firma autografa** su un **PDF stampato** (poi scansione allegata), **OPPURE**
- **Firma digitale qualificata (QES)** sul **PDF** del CoA

**✅ Extra utile per EGI:** 
- **Firma crittografica col wallet** dell'autore sul **digest (SHA-256)** del CoA. Non sostituisce la QES, ma aggiunge prova tecnica forte e coerenza on-chain.

#### 2. **Seriale + QR di Verifica**
- Numero univoco del CoA (es. `COA-<creator>-<aaaa>-<seq>`)
- QR che punta a una **pagina di verifica** con: dati essenziali, foto, hash, stato (valido/revocato), eventuale asset ID Algorand

#### 3. **Hash**
- Calcola **SHA-256** del PDF del CoA e dell'immagine master
- Mostralo in chiaro nella pagina di verifica e nel PDF

#### 4. **Provenienza "Light"** *(facoltativa ma utile)*
- Una riga: "Da: Autore → A: Collezionista X, data". Se non c'è, lascia "N/D"

#### 5. **Condition Report Breve**
- "Ottimo / Buono / Interventi noti: …"

#### 6. **Contatti del Certificante**
- Nome/ente che emette il CoA (può essere l'autore stesso), email/sito

---

## 🔄 Flusso Consigliato per FlorenceEGI

### Processo Operativo (Pratico e Corto)

1. **Compilazione**: L'autore compila i campi **rosa corta** (quelli che già hai)

2. **Generazione**: Il sistema genera **PDF CoA + JSON snapshot** dei traits (bloccati)

3. **Calcolo Hash**: Calcoli gli **hash** del PDF e immagini

4. **Firma dell'Autore**:
   - **🎯 Preferito**: QES sul PDF
   - **➕ In aggiunta**: firma wallet Algorand del digest (salvi la firma + pubkey)
   - **🔄 Se niente QES per ora**: firma a penna su stampa del CoA, carica la scansione
     *(È prassi accettata, ma punta a QES appena possibile)*

5. **Pubblicazione**: 
   - Pagina **/verify/<seriale>** con dati essenziali, foto, hash, stato
   - **QR** sul PDF e sull'etichetta fisica (se l'opera è fisica)

6. **On-chain** *(quando vuoi)*: 
   - Registra hash/URI nei metadati dell'asset (ARC-3/19/69 a tua scelta) o in una transazione di nota
   - Linki l'**asset ID** nella pagina di verifica

---

## ❓ FAQ Critica: Firma sul Web

**Q: "La firma, sul Web… se è presente nella Dichiarazione di autenticità in chiaro, va bene?"**

**❌ RISPOSTA: NO.** 

Serve una **firma che identifichi l'autore**:

- **✅ QES su PDF** (soluzione solida, riconosciuta)
- **🔄 In alternativa transitoria**: **firma autografa** su carta del CoA (scansione allegata)
- **➕ Firma col wallet** dell'autore sul digest → ottima **prova tecnica**, ma **non sostituisce** la QES nel diritto civile. Usala come secondo strato.

---

## 📝 Mini-Checklist Operativa

**Per non appesantire il processo:**

- ✅ Dati opera (titolo, anno, tecnica, dimensioni)
- ✅ Dichiarazione di autenticità
- ✅ Foto fronte/retro + dettaglio firma
- 🔧 **Seriale + QR + hash**
- 🔧 **Firma** (QES o autografa)
- ◻️ Condition report breve (facoltativo)
- ◻️ Pagina di verifica con stato/asset ID

---

## 🚀 Implementazione Tecnica per FlorenceEGI

### Componenti da Sviluppare

1. **Blade Template**: PDF 1-pagina + QR + seriale
2. **Modulo TS**: Calcolo hash e gestione firma wallet dell'autore
3. **Controller**: Generazione CoA e gestione firma
4. **Pagina Verifica**: `/verify/<seriale>` con tutti i dati
5. **Integration**: Link con asset Algorand

### Pattern di Implementazione

- **Un file per volta** secondo metodologia OS3.0
- **GDPR compliance** integrato
- **OOP puro** con design patterns
- **Documentazione completa** OS2.0 standard
- **AI-readable code** per future sessions

---

## 🎯 Obiettivo Finale

**CoA che funziona nel mondo reale dell'arte + innovazione blockchain di FlorenceEGI.**

Equilibrio tra:
- **Semplicità** operativa (non appesantire gli artisti)
- **Solidità** legale (riconoscimento nel mercato)
- **Innovazione** tecnica (blockchain, hash, verificabilità)
- **Scalabilità** (automatizzazione dei processi)

---

*Documento preparato per FlorenceEGI Seconda Fase - Implementazione CoA Standards*