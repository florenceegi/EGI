---
title: "Collection Commerce: Abilita la Vendita"
category: collections
description: "Guida completa al Commerce Wizard: delivery policy, impact mode, payment methods"
author: "Padmin D. Curtis (AI Partner OS3.0)"
version: "1.0.0"
date: 2026-02-09
language: it
---

# Collection Commerce: Abilita la Vendita

## Introduzione: Il Commerce Wizard

Il **Commerce Wizard** è lo strumento guidato per configurare e abilitare la vendita dei tuoi EGI all'interno di una Collection. Prima di poter vendere, devi completare 3 step fondamentali:

1. **Delivery Policy**: Come verranno consegnati gli EGI (digitale, fisico, ibrido)
2. **Impact Mode**: Come contribuirai all'impatto sociale (EPP o Subscription)
3. **Payment Methods**: Quali metodi di pagamento accetti

Una volta configurato tutto, potrai **abilitare la modalità commerciale** e iniziare a vendere.

---

## Quando Usare il Commerce Wizard

### Scenari Comuni

| Scenario | Azione |
|----------|--------|
| **Nuova Collection** | Configura prima del lancio |
| **Collection Esistente** | Abilita vendita dopo creazione |
| **Cambio Configurazione** | Modifica delivery policy o impact mode |
| **Riabilitazione** | Riattiva vendita dopo disabilitazione |

### Chi Può Usarlo

- ✅ **Creator**: Configurazione obbligatoria per vendere
- ✅ **Company**: Configurazione obbligatoria per vendere
- ❌ **Collector**: Non può creare Collections
- ❌ **PA Entity**: Non vende EGI
- ❌ **EPP**: Non crea Collections

---

## Commercial Status: I 3 Stati

Ogni Collection ha uno **Commercial Status** che indica il livello di preparazione per la vendita:

### 1. DRAFT (Bozza)

**Cosa Significa**:
- Collection appena creata
- Commerce NON configurato
- Non puoi ancora vendere

**Cosa Devi Fare**:
- Accedi al Commerce Wizard
- Completa i 3 step obbligatori
- Passa a CONFIGURED

### 2. CONFIGURED (Configurato)

**Cosa Significa**:
- Commerce configurato correttamente
- Tutte le impostazioni sono valide
- Pronto per l'abilitazione
- **MA: Vendita ancora NON attiva**

**Cosa Puoi Fare**:
- Cliccare su **"Abilita Vendita"**
- Modificare configurazione
- Testare il setup

### 3. COMMERCIAL_ENABLED (Abilitato)

**Cosa Significa**:
- ✅ Vendita **ATTIVA**
- Gli EGI sono acquistabili sul marketplace
- I pagamenti vengono elaborati
- Le royalty vengono distribuite automaticamente

**Cosa Puoi Fare**:
- Vendere EGI
- Monitorare vendite in real-time
- Disabilitare temporaneamente se necessario

---

## Step 1: Delivery Policy (Politica di Consegna)

### Cos'è la Delivery Policy?

La **Delivery Policy** definisce se i tuoi EGI sono:
- Solo digitali (NFT puro)
- Solo fisici (prodotto tangibile con certificato blockchain)
- Ibridi (entrambi)

### Le 3 Opzioni

#### 🌐 DIGITAL_ONLY (Solo Digitale)

**Quando Usarla**:
- Arte digitale (illustrazioni, foto, video)
- Musica digitale (brani, album)
- Collezionabili NFT puri
- Licenze software
- Certificati virtuali

**Caratteristiche**:
- ✅ Nessuna spedizione fisica
- ✅ Consegna istantanea (dopo pagamento)
- ✅ Costi logistici zero
- ✅ Marketplace globale senza restrizioni geografiche
- ❌ Non puoi vendere prodotti fisici

**Cosa Ottiene l'Acquirente**:
- NFT (ASA) nel wallet blockchain
- File digitali HD downloadabili
- Certificate of Authenticity (CoA) digitale
- Accesso tramite piattaforma FlorenceEGI

**Esempio**:
```
Collection: "Urban Photography 2026"
- 50 fotografie digitali in edizione limitata
- Ogni EGI include JPG ad alta risoluzione + CoA
- Delivery: DIGITAL_ONLY
- Consegna: Immediata dopo pagamento
```

#### 📦 PHYSICAL_REQUIRED (Solo Fisico)

**Quando Usarla**:
- Prodotti fisici certificati blockchain
- Opere d'arte tangibili (quadri, sculture)
- Oggetti da collezione (orologi, gioielli)
- Prodotti aziendali premium
- Vini pregiati, spirits, luxury goods

**Caratteristiche**:
- ✅ Prodotto fisico obbligatorio
- ✅ Certificato blockchain come garanzia autenticità
- ✅ Tracking spedizione
- ⚠️ Gestione logistica necessaria
- ⚠️ Costi spedizione da considerare
- ⚠️ Restrizioni geografiche possibili

**Cosa Ottiene l'Acquirente**:
- Prodotto fisico spedito a domicilio
- NFT (ASA) come certificato di proprietà blockchain
- Certificate of Authenticity (CoA) PDF/cartaceo
- Tracking spedizione

**Esempio**:
```
Collection: "Limited Edition Watches"
- 100 orologi numerati
- Ogni EGI rappresenta un orologio fisico
- Delivery: PHYSICAL_REQUIRED
- Spedizione: 5-7 giorni lavorativi, tracking incluso
```

#### 🔄 PHYSICAL_ALLOWED (Ibrido - Digitale + Fisico Opzionale)

**Quando Usarla**:
- Arte che esiste in entrambe le forme (stampe + digitale)
- Edizioni speciali con bonus fisico
- Collections con varianti (standard digitale, premium fisica)
- Prodotti con upgrade fisico disponibile

**Caratteristiche**:
- ✅ Flessibilità massima
- ✅ Acquirente sceglie se vuole fisico o solo digitale
- ✅ Prezzo può variare (digitale €X, fisico €X+Y)
- ✅ Upgrade possibile anche dopo acquisto
- ⚠️ Gestione logistica per ordini fisici

**Cosa Ottiene l'Acquirente**:
- Sempre: NFT digitale + file HD
- Opzionale: Prodotto fisico (se richiesto e pagato)
- CoA disponibile in entrambe le forme

**Esempio**:
```
Collection: "Abstract Series"
- 30 opere digitali
- Opzione stampa fine art su tela (+ €150)
- Delivery: PHYSICAL_ALLOWED
- Scelta: Acquirente decide al checkout
```

### Come Scegliere la Delivery Policy

```
Domanda: I miei EGI hanno una controparte fisica?
  └─ No → DIGITAL_ONLY
  └─ Sì, obbligatoria → PHYSICAL_REQUIRED
  └─ Sì, opzionale → PHYSICAL_ALLOWED
```

### Cambiare Delivery Policy

⚠️ **ATTENZIONE**:
- Puoi cambiare la policy **PRIMA** di abilitare la vendita
- **DOPO aver venduto** EGI, modificare la policy è **SCONSIGLIATO**
- Rischi: Confusione per gli acquirenti, obblighi contrattuali diversi

**Best Practice**:
- Decidi la policy PRIMA del lancio
- Se devi cambiare, disabilita la vendita temporaneamente
- Comunica chiaramente agli acquirenti esistenti

---

## Step 2: Impact Mode (Modalità Impatto)

### Cos'è l'Impact Mode?

L'**Impact Mode** definisce come la tua Collection contribuisce all'impatto sociale e ambientale di FlorenceEGI. Esistono 2 modalità:

1. **EPP**: Doni una percentuale a progetti ambientali
2. **SUBSCRIPTION**: Offri abbonamenti con impatto ricorrente

### Modalità 1: EPP (Environmental Protection Project)

#### Per Creator (Obbligatorio)

I **Creator** DEVONO scegliere un progetto EPP e donare **20% di ogni vendita**.

**Setup EPP per Creator**:

1. **Apri Commerce Wizard**
2. **Seleziona Impact Mode: EPP**
3. **Scegli Progetto EPP**:
   - Lista progetti attivi certificati
   - Vedi descrizione, goal, impatto
   - Seleziona quello che preferisci
4. **Conferma**: Il 20% andrà automaticamente al progetto scelto

**Distribuzione Pagamento (Creator con EPP)**:

```
Esempio: EGI venduto a €100

Mint Primario:
- Creator: €68 (68%)
- EPP Scelto: €20 (20%) ← Impatto sociale!
- Natan: €10 (10%)
- Frangette: €2 (2%)

Rebind (Mercato Secondario a €150):
- Venditore: €140.85 (93.9%)
- Creator: €6.75 (4.5%)
- EPP: €1.20 (0.8%) ← Impatto continuato!
- Natan: €1.05 (0.7%)
- Frangette: €0.15 (0.1%)
```

**Cambiare Progetto EPP**:
- ✅ Puoi cambiare EPP per **nuove Collections**
- ❌ **NON puoi** cambiare EPP per Collections già lanciate
- Le vendite passate rimangono col vecchio EPP

#### Per Company (Opzionale)

Le **Company** possono scegliere EPP **volontariamente** per responsabilità sociale d'impresa (CSR).

**Setup EPP Volontario (Company)**:

1. **Apri Commerce Wizard**
2. **Seleziona Impact Mode: EPP** (opzionale)
3. **Scegli Percentuale**: 1% - 20% (tu decidi quanto)
4. **Scegli Progetto EPP**
5. **Conferma**

**Vantaggi EPP Volontario per Company**:
- 🌍 **Immagine Green**: Posizionati come azienda sostenibile
- 💚 **Marketing**: Comunica il tuo impatto sociale
- 📊 **Trasparenza**: Report impatto tracciato su blockchain
- 🎯 **Differenziazione**: Distinguiti dai competitor

**Distribuzione Pagamento (Company con EPP 10%)**:

```
Esempio: Prodotto venduto a €200

Mint Primario:
- Company: €180 (90% - 10% EPP volontario)
- EPP Scelto: €20 (10%) ← Donazione volontaria!
- Natan: €0 (Natan prende dalla base 90%)

Distribuzione Base Company: 90% + 10% Natan = 100%
Con EPP 10%, Company passa a 80% + EPP 10% + Natan 10%
```

### Modalità 2: SUBSCRIPTION (Abbonamento)

**Quando Usarla**:
- Collections con contenuti ricorrenti
- Abbonamenti mensili/annuali
- Accesso premium a contenuti
- Membership NFT con benefici continuativi

**Esempio Subscription**:

```
Collection: "Monthly Art Drop Club"
- 12 EGI all'anno (1 al mese)
- Prezzo: €120/anno (€10/mese)
- Impact Mode: SUBSCRIPTION
- Benefici: Accesso esclusivo, community privata, eventi
```

**Setup Subscription**:

1. **Crea Subscription Plan** (vedi "Subscription Management")
2. **Apri Commerce Wizard**
3. **Seleziona Impact Mode: SUBSCRIPTION**
4. **Scegli Subscription Plan**
5. **Conferma**

⚠️ **Nota**: Al momento, SUBSCRIPTION non è disponibile per tutti gli utenti. Contatta support per abilitarla.

### Confronto EPP vs SUBSCRIPTION

| Caratteristica | EPP | SUBSCRIPTION |
|----------------|-----|--------------|
| **Tipo Impatto** | Ambientale/sociale | Contenuto continuativo |
| **Pagamento** | Una tantum (per EGI) | Ricorrente (mensile/annuale) |
| **Obbligatorio per Creator** | Sì (20%) | No |
| **Opzionale per Company** | Sì | Sì |
| **Percentuale** | Fissa (Creator 20%) o variabile (Company) | Dipende dal piano |

---

## Step 3: Payment Methods (Metodi di Pagamento)

### Metodi Disponibili

Per vendere, devi avere **almeno 1 metodo di pagamento** configurato:

| Metodo | Tipo | Tempo Accredito | Fee |
|--------|------|-----------------|-----|
| **Stripe** | Carta credito/debito | 2-5 giorni | 2.9% + €0.25 |
| **PayPal** | PayPal account | 1-3 giorni | 3.4% + €0.35 |
| **Bank Transfer** | Bonifico bancario | 1-3 giorni | 0% (FlorenceEGI) |
| **Egili** | Crediti piattaforma | Istantaneo | 0% |
| **Algorand (ALGO)** | Cryptocurrency | Istantaneo | ~€0.001 blockchain |

### Configurare Payment Methods

#### Metodo A: User-Level (Globale)

Configura metodi validi per **tutte le tue Collections**:

1. Vai su **"Impostazioni"** → **"Pagamenti"**
2. **Aggiungi Metodo**:
   - **Stripe**: Collega account Stripe
   - **PayPal**: Collega email PayPal
   - **Bank Transfer**: Inserisci IBAN
3. **Salva**
4. Tutti i metodi saranno disponibili per le tue Collections

#### Metodo B: Collection-Level (Specifico)

Configura metodi diversi per ogni Collection:

1. Apri la Collection
2. Vai su **"Payment Settings"**
3. **Aggiungi Destination**:
   - Primary destination (metodo principale)
   - Secondary destinations (fallback)
4. **Salva**

⚠️ **Priorità**: Collection-level > User-level

### Validazione Payment Methods

Il Commerce Wizard verifica che:
- ✅ Almeno 1 metodo attivo
- ✅ Metodi validati (email confermata, IBAN verificato)
- ✅ Account in buono stato (no sospensioni)

---

## Workflow Completo: Abilita la Vendita

### Procedura Step-by-Step

#### Fase 1: Accesso al Wizard

1. **Vai alla tua Collection**
   - Dashboard → "Mie Collections" → Seleziona Collection
2. **Clicca su "Commerce Wizard"**
   - Bottone visibile se commercial_status = DRAFT o CONFIGURED
3. **Visualizza Overview**
   - Vedi stato attuale dei 3 step

#### Fase 2: Configurazione (3 Step)

**Step 1 - Delivery Policy**:

```
[ ] DIGITAL_ONLY        Descrizione: Solo digitale, nessuna spedizione
[ ] PHYSICAL_REQUIRED   Descrizione: Solo fisico, spedizione obbligatoria
[ ] PHYSICAL_ALLOWED    Descrizione: Digitale + fisico opzionale

Seleziona: [DIGITAL_ONLY] ✓
```

**Step 2 - Impact Mode**:

```
Per Creator (obbligatorio):
[ ] EPP (20% automatico)

Seleziona Progetto EPP:
[ ] Riforestazione Appennino (Goal: €50k, ARF: 1000 alberi)
[ ] Pulizia Oceani Mediterraneo (Goal: €80k, BPE: 500 persone)
[ ] Educazione Ambientale Scuole (Goal: €30k, BPE: 1000 studenti)

Seleziona: [Riforestazione Appennino] ✓
```

**Step 3 - Payment Methods**:

```
Metodi Configurati (User-Level):
✅ Stripe (account@example.com)
✅ PayPal (paypal@example.com)
✅ Bank Transfer (IT60 X054 2811 1010 0000 0123 456)

Metodi Attivi per Questa Collection:
[x] Stripe
[x] PayPal
[x] Bank Transfer
[ ] Egili (non hai crediti sufficienti)
[ ] Algorand (no wallet collegato)

Almeno 1 attivo: ✅
```

#### Fase 3: Revisione

Il wizard mostra un riepilogo:

```
📋 RIEPILOGO CONFIGURAZIONE

Collection: "Urban Photography 2026"
Creator: Marco Rossi

✅ Delivery Policy: DIGITAL_ONLY
✅ Impact Mode: EPP - Riforestazione Appennino (20%)
✅ Payment Methods: 3 attivi (Stripe, PayPal, Bank Transfer)

Commercial Status: CONFIGURED (pronto per abilitazione)

[Modifica Configurazione] [Abilita Vendita →]
```

#### Fase 4: Abilitazione

1. **Clicca "Abilita Vendita"**
2. **Conferma**:
   ```
   ⚠️ Conferma Abilitazione Vendita

   Stai per abilitare la vendita per la Collection "Urban Photography 2026".

   Una volta abilitata:
   - Gli EGI saranno acquistabili sul marketplace
   - I pagamenti verranno elaborati automaticamente
   - Le royalty saranno distribuite secondo il tuo setup

   Confermi?
   [Annulla] [Sì, Abilita Vendita]
   ```

3. **✅ Vendita Abilitata!**
   ```
   🎉 Congratulazioni!

   La vendita per "Urban Photography 2026" è ora ATTIVA.

   - Commercial Status: COMMERCIAL_ENABLED
   - Marketplace: Visibile e acquistabile
   - Pagamenti: Attivi

   [Vai al Marketplace] [Torna alla Collection]
   ```

---

## Monitoraggio e Gestione Post-Abilitazione

### Dashboard Vendite

Dopo l'abilitazione, accedi alla **Dashboard Vendite**:

1. **Overview**:
   - Vendite totali (numero + €)
   - Guadagni netti (dopo royalty)
   - Contributo EPP (totale donato)
   - Conversion rate

2. **Transazioni Recenti**:
   - EGI venduto
   - Acquirente
   - Prezzo
   - Data
   - Metodo pagamento
   - Status (pending, completed, refunded)

3. **Performance Metrics**:
   - Views vs Purchases
   - Best-selling EGI
   - Revenue per EGI
   - Payment method breakdown

### Modificare Configurazione

Se devi modificare la configurazione **dopo l'abilitazione**:

1. **Disabilita Vendita Temporaneamente**:
   - Collection → "Disabilita Vendita"
   - Commercial Status passa a CONFIGURED

2. **Modifica Setup**:
   - Accedi al Commerce Wizard
   - Cambia delivery policy o impact mode
   - Salva modifiche

3. **Ri-abilita Vendita**:
   - "Abilita Vendita" di nuovo
   - Commercial Status torna a COMMERCIAL_ENABLED

⚠️ **ATTENZIONE**:
- Le vendite passate mantengono la vecchia configurazione
- Solo le nuove vendite useranno il nuovo setup
- Comunica i cambiamenti agli acquirenti potenziali

### Disabilitare Vendita

Puoi disabilitare la vendita in qualsiasi momento:

**Motivi Comuni**:
- Esaurite le edizioni
- Problemi logistici (spedizioni)
- Manutenzione piattaforma
- Ristrutturazione Collection

**Come Disabilitare**:
1. Collection → "Impostazioni"
2. Clicca "Disabilita Vendita"
3. Conferma
4. Commercial Status → CONFIGURED (vendita fermata)

**Cosa Succede**:
- ❌ Nuovi acquisti bloccati
- ✅ Acquisti già pagati vengono completati
- ✅ Rebind sul mercato secondario continua (non puoi bloccarlo)

---

## Troubleshooting: Errori Comuni

### Errore: "Delivery Policy Required"

**Problema**: Non hai selezionato una delivery policy.

**Soluzione**:
1. Torna al Commerce Wizard
2. Step 1: Seleziona DIGITAL_ONLY, PHYSICAL_REQUIRED, o PHYSICAL_ALLOWED
3. Salva

### Errore: "EPP Project Required"

**Problema**: Hai selezionato Impact Mode EPP ma non hai scelto un progetto.

**Soluzione**:
1. Commerce Wizard → Step 2
2. Seleziona un progetto EPP dalla lista
3. Salva

### Errore: "No Payment Methods Enabled"

**Problema**: Nessun metodo di pagamento configurato o attivo.

**Soluzione**:
1. Vai su "Impostazioni" → "Pagamenti"
2. Aggiungi almeno 1 metodo (Stripe, PayPal, Bank Transfer)
3. Valida il metodo (conferma email, IBAN)
4. Torna al Commerce Wizard

### Errore: "Cannot Enable - Collection Empty"

**Problema**: La collection non ha EGI da vendere.

**Soluzione**:
1. Aggiungi almeno 1 EGI alla collection
2. Upload immagini e metadati completi
3. Torna al Commerce Wizard

### Errore: "Commercial Status Already Enabled"

**Problema**: Stai cercando di abilitare una collection già abilitata.

**Soluzione**:
- Niente da fare, sei già live!
- Se vuoi modificare, disabilita prima, poi riaibilita

---

## Best Practices

### Prima del Lancio

1. **Testa il Flusso**:
   - Crea un EGI di test a €1
   - Abilita vendita
   - Acquistalo tu stesso (o un amico) per testare
   - Verifica pagamento ricevuto
   - Verifica CoA generato

2. **Documenta la Policy**:
   - Scrivi chiaramente nella descrizione Collection:
     - Cosa include l'EGI (digitale, fisico, entrambi)
     - Tempi di consegna
     - Costi spedizione (se fisico)
     - Policy rimborsi

3. **Comunica l'Impatto EPP**:
   - Menziona quale progetto EPP supporti
   - Spiega perché lo hai scelto
   - Condividi report impatto con la community

### Durante le Vendite

1. **Monitora Dashboard Quotidianamente**:
   - Controlla nuove vendite
   - Gestisci eventuali problemi pagamento
   - Rispondi a domande acquirenti

2. **Fulfillment Fisico** (se PHYSICAL_*):
   - Processa ordini entro 24-48h
   - Comunica tracking spedizione
   - Usa imballaggi sicuri e professionali

3. **Engagement Community**:
   - Ringrazia gli acquirenti pubblicamente (con permesso)
   - Condividi milestone (es. "Venduti 10/50 EGI!")
   - Mostra impatto EPP (es. "Grazie a voi, 50 alberi piantati!")

### Post-Vendita

1. **Follow-Up**:
   - Invia messaggio di ringraziamento
   - Chiedi feedback
   - Offri supporto per redemption wallet (se vogliono)

2. **Report Impatto**:
   - Pubblica report trimestrale contributo EPP
   - Condividi foto/video progetto EPP
   - Trasparenza totale su come vengono usati i fondi

---

## FAQ

**Q: Devo abilitare il commerce per ogni Collection?**
A: Sì. Ogni Collection ha il suo setup commerce indipendente. Questo permette flessibilità (es. Collection A digitale, Collection B fisica).

**Q: Posso cambiare EPP dopo aver venduto?**
A: No. Le vendite passate rimangono col vecchio EPP. Solo nuove Collections possono avere EPP diverso.

**Q: Le Company devono scegliere un EPP?**
A: No. Per le Company, EPP è **opzionale**. Possono scegliere di NON supportare nessun progetto.

**Q: Cosa succede se disabilito la vendita?**
A: Nuovi acquisti vengono bloccati. Gli acquisti già pagati vengono completati. Il mercato secondario (rebind) continua.

**Q: Posso avere prezzi diversi per digitale e fisico?**
A: Sì, se scegli PHYSICAL_ALLOWED. L'acquirente paga il prezzo base per il digitale, e può pagare un extra per la versione fisica.

**Q: Quanto tempo ci vuole per abilitare la vendita?**
A: Se hai già i metodi di pagamento configurati, 5-10 minuti. Altrimenti, aggiungi 1-2 giorni per validare Stripe/PayPal/IBAN.

---

## Supporto e Assistenza

Problemi con il Commerce Wizard?

📧 **Email**: commerce@florenceegi.com
💬 **Live Chat**: Disponibile Mon-Fri 9:00-18:00 CET
📚 **Centro Assistenza**: support.florenceegi.com/commerce
🎥 **Video Tutorial**: youtube.com/florenceegi/commerce-wizard

---

**Ultimo aggiornamento**: 2026-02-08
**Versione documentazione**: 1.0.0
