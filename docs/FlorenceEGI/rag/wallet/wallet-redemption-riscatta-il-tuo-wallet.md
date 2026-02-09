---
title: "Wallet Redemption: Riscatta il Tuo Wallet"
category: wallet
description: "Guida completa al wallet redemption: seed phrase, ASA transfer, costi e sicurezza"
author: "Padmin D. Curtis (AI Partner OS3.0)"
version: "1.0.0"
date: 2026-02-09
language: it
---

# Wallet Redemption: Riscatta il Tuo Wallet FlorenceEGI

## Cos'è il Wallet Redemption?

Il **Wallet Redemption** (riscatto del wallet) è il processo che ti permette di ottenere il **controllo completo** del tuo wallet Algorand custodial, trasformandolo da wallet gestito dalla piattaforma a wallet **self-custody** sotto il tuo pieno controllo.

### In Parole Semplici

Quando ti registri su FlorenceEGI, la piattaforma crea automaticamente per te un wallet Algorand **custodial** (gestito da noi per semplicità). Con il redemption:

- 🔓 **Ottieni la seed phrase** (25 parole) del wallet
- 🚀 **Trasferisci tutti i tuoi EGI** (ASA) dal Treasury al tuo wallet
- 🎯 **Pieno controllo**: Puoi usare il wallet con qualsiasi app Algorand (Pera, Defly, MyAlgo)
- ⚠️ **Responsabilità totale**: Sei l'unico responsabile della sicurezza del wallet

---

## Perché Fare il Redemption?

### Vantaggi

| Vantaggio | Descrizione |
|-----------|-------------|
| **🔐 Sovranità Totale** | Tu possiedi le chiavi private, nessuno può bloccare i tuoi fondi |
| **💼 Portabilità** | Puoi usare il wallet con qualsiasi app/service Algorand |
| **🎨 Libertà** | Puoi trasferire EGI fuori dalla piattaforma quando vuoi |
| **🔒 Privacy** | FlorenceEGI non ha più accesso al tuo wallet dopo redemption |
| **⛓️ Decentralizzazione** | Allineato ai principi blockchain: "Not your keys, not your coins" |

### Svantaggi

| Svantaggio | Descrizione |
|------------|-------------|
| **⚠️ Responsabilità** | Se perdi la seed phrase, perdi TUTTO (nessun recovery possibile) |
| **💸 Costo** | Redemption costa EGILI (calcolato in base al numero di EGI) |
| **🔄 Irreversibile** | Una volta riscattato, non puoi tornare indietro |
| **🛠️ Complessità** | Devi gestire manualmente opt-in ASA, min balance, fees |
| **📱 Self-Support** | FlorenceEGI non può più aiutarti con problemi wallet |

### Chi Dovrebbe Fare il Redemption?

✅ **Dovresti riscattare SE**:
- Sei esperto di criptovalute e blockchain
- Vuoi pieno controllo e autodeterminazione
- Hai bisogno di usare il wallet fuori dalla piattaforma
- Vuoi trasferire EGI a exchange o altri wallet
- Non ti fidi di wallet custodial (filosofia "not your keys")

❌ **NON dovresti riscattare SE**:
- Sei nuovo alle criptovalute
- Non hai esperienza con seed phrase e sicurezza wallet
- Preferisci la comodità di un wallet gestito
- Non hai bisogno di funzionalità avanzate
- Vuoi che FlorenceEGI possa aiutarti in caso di problemi

---

## Come Funziona il Redemption: Flow Completo

### Fase 1: Preparazione (Prima del Redemption)

#### Passo 1: Verifica Eligibilità

Prima di poter riscattare, controlla di avere:

✅ **Requisiti**:
- Account FlorenceEGI attivo
- Almeno 1 EGI nel tuo wallet
- EGILI sufficienti per pagare il costo redemption
- Email verificata

✅ **Wallet Status**: Non già riscattato

#### Passo 2: Calcola il Costo

Il costo del redemption dipende dal numero di EGI che possiedi.

**Formula di Calcolo**:

```
Costo Base:          0.1 ALGO  (minimum balance Algorand)
Costo per EGI:       0.1 ALGO  (opt-in ASA per ogni EGI)
Transaction Fees:    ~0.001 ALGO per transaction

Totale ALGO = 0.1 + (N × 0.1) + fees

Conversione: 1 ALGO = 100 EGILI
```

**Esempi Pratici**:

| N° EGI | Costo ALGO | Costo EGILI | Breakdown |
|--------|------------|-------------|-----------|
| 1 EGI | 0.2 ALGO | 20 EGILI | Base 0.1 + 1×0.1 = 0.2 |
| 5 EGI | 0.6 ALGO | 60 EGILI | Base 0.1 + 5×0.1 = 0.6 |
| 10 EGI | 1.1 ALGO | 110 EGILI | Base 0.1 + 10×0.1 = 1.1 |
| 20 EGI | 2.1 ALGO | 210 EGILI | Base 0.1 + 20×0.1 = 2.1 |
| 50 EGI | 5.1 ALGO | 510 EGILI | Base 0.1 + 50×0.1 = 5.1 |

💡 **Perché questo costo?**
- Ogni ASA su Algorand richiede 0.1 ALGO di "minimum balance" nel wallet
- FlorenceEGI deve finanziare il tuo wallet con ALGO per coprire questi costi
- Il costo viene convertito in EGILI e addebitato a te

#### Passo 3: Ottieni EGILI Sufficienti

Se non hai abbastanza EGILI:

1. **Compra EGILI**: Sezione "Shop EGILI" sulla piattaforma
2. **Guadagna EGILI**: Completa task, invita amici, partecipa a promozioni
3. **Ricevi EGILI**: Da Creator che ti hanno regalato crediti

### Fase 2: Esecuzione Redemption

#### Passo 1: Accedi alla Pagina Redemption

1. Vai su **"Wallet"** → **"Redemption"**
2. Visualizza:
   - Tuo wallet address Algorand
   - Lista EGI (ASA) da trasferire
   - Costo redemption (ALGO + EGILI)
   - Saldo EGILI attuale

#### Passo 2: Rivedi i Dettagli

**Schermata Redemption**:

```
═══════════════════════════════════════════════════════
           WALLET REDEMPTION - RISCATTA WALLET
═══════════════════════════════════════════════════════

📍 Tuo Wallet Algorand:
   ABCD...XYZ123456789

📦 EGI da Trasferire (ASA):
   ✓ EGI #1234 "Sunset on Florence" (ASA ID: 987654321)
   ✓ EGI #5678 "Urban Life" (ASA ID: 123456789)
   ✓ EGI #9012 "Abstract Series #5" (ASA ID: 555666777)

   Totale: 3 EGI

💰 Costo Redemption:
   Base Cost:         0.1 ALGO
   Per-EGI (3 × 0.1): 0.3 ALGO
   Transaction Fees:  ~0.003 ALGO
   ────────────────────────────
   TOTALE:            0.403 ALGO = 41 EGILI

💳 Tuo Saldo EGILI:
   Disponibile:       150 EGILI ✅
   Dopo Redemption:   109 EGILI

⚠️ ATTENZIONE:
   Il redemption è IRREVERSIBILE. Una volta riscattato:
   - Otterrai la seed phrase (25 parole)
   - Tutti i tuoi EGI saranno trasferiti al wallet
   - FlorenceEGI NON potrà più aiutarti con problemi wallet
   - Sei responsabile della sicurezza della seed phrase

[ Annulla ]  [ Continua Redemption → ]
```

#### Passo 3: Conferma Redemption

1. Clicca su **"Continua Redemption"**
2. **Conferma Finale**:
   ```
   ⚠️ ULTIMA CONFERMA

   Digitando "REDEEM" qui sotto confermi che:

   ☑ Ho letto e capito i rischi
   ☑ Sono responsabile della seed phrase
   ☑ Il processo è irreversibile
   ☑ Accetto il costo di 41 EGILI

   Digita REDEEM per confermare:
   [________________]

   [ Annulla ]  [ Conferma Redemption ]
   ```

3. Digita **"REDEEM"**
4. Clicca **"Conferma Redemption"**

#### Passo 4: Esecuzione Automatica

Il sistema esegue automaticamente:

```
🔄 REDEMPTION IN CORSO...

Step 1/6: Validazione requisiti...              ✅ OK
Step 2/6: Deduzione EGILI (41)...               ✅ OK
Step 3/6: Funding wallet con ALGO...            ✅ OK (0.403 ALGO inviati)
Step 4/6: Opt-in ASA (3 EGI)...                 ✅ OK
Step 5/6: Trasferimento ASA da Treasury...      ✅ OK (3 EGI trasferiti)
Step 6/6: Generazione documento seed phrase...  ✅ OK

✅ REDEMPTION COMPLETATO!

📥 Download seed phrase disponibile per 15 minuti.

[ Download Seed Phrase ]
```

⏱️ **Tempo Stimato**: 2-5 minuti (dipende dalla congestione blockchain)

### Fase 3: Download Seed Phrase

#### Passo 1: Download Documento

1. Clicca su **"Download Seed Phrase"**
2. Viene scaricato file `wallet-seed-phrase-2026-02-08.txt`
3. ⏰ **IMPORTANTE**: Il link scade dopo 15 minuti!

#### Passo 2: Contenuto Documento

Il file contiene:

```
═══════════════════════════════════════════════════════════════════════
║               FLORENCE EGI - WALLET SEED PHRASE                    ║
═══════════════════════════════════════════════════════════════════════

🔐 SEED PHRASE (25 parole):

abandon ability able about above absent absorb abstract absurd abuse
access accident account accuse achieve acid acoustic acquire across act
action actor actress actual adapt add

📍 WALLET ADDRESS:
   ABCD...XYZ123456789

📅 DATA REDEMPTION:
   2026-02-08 14:35:22 UTC

⚠️  ISTRUZIONI CRITICHE DI SICUREZZA:

1. Conserva questa seed phrase in un luogo SICURO e OFFLINE
2. NON salvare su cloud, email, o dispositivi connessi a internet
3. NON condividere mai la seed phrase con nessuno
4. Chiunque conosca questa seed phrase avrà accesso completo ai tuoi fondi

🛡️  BEST PRACTICES:

- Scrivi la seed phrase su carta (no digitale!)
- Conserva in cassaforte o safe deposit box
- Considera backup in luoghi fisici diversi
- Testa il recovery su wallet di test prima di cancellare

═══════════════════════════════════════════════════════════════════════
```

#### Passo 3: Conservazione Sicura

🔒 **CRITICAMENTE IMPORTANTE**:

✅ **DEVI**:
- Scrivere la seed phrase su carta (no digitale!)
- Conservare in luogo sicuro e offline (cassaforte, safe deposit box)
- Fare backup in 2-3 luoghi fisici diversi
- Testare il recovery su wallet Algorand di test

❌ **MAI**:
- Salvare su computer, telefono, cloud
- Inviare via email, WhatsApp, Telegram
- Fotografare la seed phrase
- Condividere con nessuno (neanche support FlorenceEGI!)

### Fase 4: Finalizzazione (Opzionale ma Consigliata)

Dopo aver salvato la seed phrase:

1. **Testa il Recovery**:
   - Usa un wallet Algorand (Pera, Defly, MyAlgo)
   - Importa la seed phrase
   - Verifica che vedi i tuoi EGI (ASA)
   - **IMPORTANTE**: Fai solo test, non trasferire nulla ancora!

2. **Finalizza Redemption**:
   - Torna su FlorenceEGI
   - Clicca **"Finalizza Redemption"**
   - Questo cancella la seed phrase dal database FlorenceEGI (irreversibile)

⚠️ **Nota**: Se non finalizzi, la seed phrase rimane nel database cifrata per 30 giorni, poi viene auto-cancellata.

---

## Cosa Succede Dopo il Redemption?

### Cambiamenti Immediati

#### ✅ Cosa Puoi Fare

1. **Usare Wallet Esterno**:
   - Importa seed phrase in Pera Wallet, Defly, MyAlgo
   - Gestisci EGI da app mobile

2. **Trasferire EGI**:
   - Invia EGI ad altri wallet Algorand
   - Trasferisci a exchange (se supportano gli ASA)
   - Fai trading OTC con altri collezionisti

3. **Partecipare ad AlgoFi DeFi**:
   - Usa i tuoi EGI in protocolli DeFi Algorand
   - Collateralizza, stake, lend (se supportato)

#### ❌ Cosa NON Puoi Più Fare

1. **Recovery Automatico**:
   - FlorenceEGI non può più recuperare il tuo wallet
   - Se perdi seed phrase, perdi tutto

2. **Gestione Semplificata**:
   - Devi gestire manualmente opt-in per nuovi ASA
   - Devi mantenere min balance ALGO nel wallet
   - Devi pagare transaction fees manualmente

3. **Support Custodial**:
   - Il support FlorenceEGI non può aiutarti con problemi wallet
   - Sei completamente autonomo

### Integrazione con FlorenceEGI

Anche dopo redemption:

✅ **Funzionalità Attive**:
- Puoi ancora comprare EGI sul marketplace
- Puoi vendere (rebind) i tuoi EGI
- Ricevi royalty come Creator
- Accedi a tutte le funzionalità piattaforma

⚠️ **Comportamento Cambiato**:
- Quando compri nuovi EGI:
  - Devi fare **opt-in manuale** dell'ASA sul tuo wallet
  - Devi avere **ALGO sufficiente** per min balance
  - FlorenceEGI ti invierà l'ASA dopo il pagamento
- Quando vendi (rebind):
  - Devi **autorizzare manualmente** il trasferimento ASA
  - Usi il tuo wallet esterno per firmare transaction

---

## Usare il Wallet Riscattato: Guida Pratica

### Importare in Pera Wallet (Mobile)

#### iOS / Android

1. **Download Pera Wallet**:
   - iOS: App Store → "Pera Algorand Wallet"
   - Android: Google Play → "Pera Algorand Wallet"

2. **Importa Wallet**:
   - Apri Pera Wallet
   - Tap "Add Existing Account"
   - Seleziona "Import with Passphrase"
   - Inserisci le 25 parole della seed phrase (in ordine!)
   - Tap "Import"

3. **Verifica EGI**:
   - Vai su "Assets"
   - Vedrai tutti i tuoi EGI come ASA
   - Ogni EGI mostra: Nome, ID ASA, Quantity (1)

4. **Gestisci Fondi**:
   - Invia ALGO al wallet per fees
   - Trasferisci EGI ad altri indirizzi
   - Opt-in nuovi ASA quando acquisti su FlorenceEGI

### Importare in Defly Wallet

Simile a Pera:

1. Download Defly Wallet
2. "Add Account" → "Import Account"
3. Inserisci seed phrase (25 parole)
4. Conferma

### Importare in MyAlgo (Web)

⚠️ **ATTENZIONE**: MyAlgo è deprecato. Usa Pera o Defly.

---

## Gestione Post-Redemption: Responsabilità

### Minimum Balance ALGO

Ogni wallet Algorand richiede un **minimum balance** per rimanere attivo:

- **Base**: 0.1 ALGO (wallet vuoto)
- **Per ogni ASA**: +0.1 ALGO

**Esempio**:
```
Hai 5 EGI nel wallet riscattato:
- Base wallet: 0.1 ALGO
- 5 ASA × 0.1: 0.5 ALGO
─────────────────────────
Min Balance:   0.6 ALGO

Se hai meno di 0.6 ALGO nel wallet, non puoi inviare transazioni!
```

💡 **Best Practice**: Mantieni sempre 0.5-1 ALGO extra nel wallet per fees e sicurezza.

### Opt-In ASA Manuali

Quando acquisti nuovi EGI su FlorenceEGI dopo redemption:

1. **Ricevi Notifica**:
   ```
   🔔 Nuovo EGI Acquistato!

   Hai acquistato "Sunset #10" (EGI #6789)
   ASA ID: 999888777

   ⚠️ ACTION REQUIRED:
   Devi fare opt-in dell'ASA manualmente sul tuo wallet.

   [ Guida Opt-In ]  [ Fatto ✓ ]
   ```

2. **Opt-In su Pera Wallet**:
   - Apri Pera Wallet
   - Tap "+" (Add Asset)
   - Inserisci ASA ID: 999888777
   - Tap "Opt-In" (costa 0.001 ALGO fee)
   - ✅ Ora puoi ricevere l'ASA

3. **Notifica FlorenceEGI**:
   - Torna su FlorenceEGI
   - Clicca "Fatto ✓"
   - Il sistema verifica l'opt-in
   - Invia l'ASA al tuo wallet

### Transaction Fees

Ogni transaction su Algorand costa **0.001 ALGO**:

| Tipo Transaction | Fee |
|------------------|-----|
| Opt-in ASA | 0.001 ALGO |
| Trasferimento ASA | 0.001 ALGO |
| Trasferimento ALGO | 0.001 ALGO |

💡 **Stima Mensile**: Se fai 10 transazioni/mese = 0.01 ALGO = ~€0.03 (trascurabile)

---

## Troubleshooting e FAQ

### Errori Comuni

#### Errore: "Insufficient EGILI Balance"

**Problema**: Non hai abbastanza EGILI per pagare il redemption.

**Soluzione**:
1. Calcola costo: vai su "Wallet" → "Redemption" → vedi costo
2. Compra EGILI: "Shop EGILI"
3. Riprova redemption

#### Errore: "Wallet Already Redeemed"

**Problema**: Hai già riscattato il wallet in passato.

**Soluzione**:
- Recupera la seed phrase dal documento salvato
- Importa il wallet in Pera/Defly
- Non puoi fare redemption due volte!

#### Errore: "Redemption Token Expired"

**Problema**: Hai aspettato più di 15 minuti tra conferma e download seed phrase.

**Soluzione**:
1. Torna alla pagina redemption
2. Ri-conferma il redemption
3. Scarica subito la seed phrase (entro 15 minuti!)

#### Problema: "Non Vedo i Miei EGI su Pera Wallet"

**Causa Possibile**:
- Gli ASA non sono visibili di default, devi aggiungerli manualmente

**Soluzione**:
1. Su Pera Wallet, tap "+" (Add Asset)
2. Cerca per ASA ID (lo trovi su FlorenceEGI)
3. Aggiungi l'ASA
4. Ora dovresti vedere l'EGI

#### Problema: "Wallet Balance Troppo Basso per Transazioni"

**Causa**: Minimum balance non rispettato (hai meno ALGO del richiesto).

**Soluzione**:
1. Calcola min balance: 0.1 ALGO + (N × 0.1) dove N = numero ASA
2. Invia ALGO al wallet da exchange o altro wallet
3. Mantieni sempre 0.5-1 ALGO extra per fees

### FAQ

**Q: Posso annullare il redemption dopo averlo fatto?**
A: **No**. Il redemption è irreversibile. Una volta trasferiti gli ASA e scaricata la seed phrase, non si torna indietro.

**Q: Cosa succede se perdo la seed phrase?**
A: Perdi l'accesso al wallet per sempre. Nessuno (neanche FlorenceEGI) può recuperarla. I tuoi EGI sono persi permanentemente.

**Q: FlorenceEGI conserva una copia della seed phrase dopo redemption?**
A: Dopo che clicchi "Finalizza Redemption", la seed phrase viene cancellata dal database (irreversibile). Se non finalizzi, rimane cifrata per 30 giorni poi si auto-cancella.

**Q: Posso fare redemption parziale (solo alcuni EGI)?**
A: No. Il redemption trasferisce TUTTI i tuoi EGI in una sola operazione. Non puoi scegliere quali EGI riscattare.

**Q: Il costo redemption è rimborsabile?**
A: No. Gli EGILI spesi per il redemption non sono rimborsabili perché coprono costi reali blockchain (funding wallet, opt-in, transfer).

**Q: Dopo redemption, posso ancora comprare EGI su FlorenceEGI?**
A: Sì! Puoi comprare normalmente. Ma dovrai fare opt-in manuali degli ASA dal tuo wallet.

**Q: Posso vendere (rebind) EGI dopo redemption?**
A: Sì. Ma dovrai autorizzare manualmente i trasferimenti ASA usando il tuo wallet esterno (Pera/Defly).

**Q: Quanto tempo impiega il redemption?**
A: 2-5 minuti in totale (dipende dalla congestione blockchain Algorand).

**Q: Il redemption è tracciato su blockchain?**
A: Sì! Tutti i trasferimenti ASA sono pubblici e verificabili su Algorand Explorer:
- Funding ALGO al wallet
- Opt-in ASA transactions
- Transfer ASA da Treasury al tuo wallet

**Q: Posso fare redemption se ho debiti con FlorenceEGI?**
A: Dipende. Se hai debiti attivi (es. pagamenti pending), potresti non essere eligible. Contatta support.

---

## Sicurezza: Best Practices

### Seed Phrase Storage

| ✅ DEVI | ❌ MAI |
|---------|--------|
| Scrivere su carta (no digitale) | Salvare su computer/telefono |
| Conservare in cassaforte fisica | Fotografare |
| Backup in 2-3 luoghi diversi | Inviare via email/chat |
| Testare recovery prima di cancellare | Condividere con nessuno |
| Usare steel seed phrase plates (optional) | Conservare vicino a computer/wifi |

### Metodi di Conservazione Avanzati

1. **Shamir Secret Sharing** (Avanzato):
   - Dividi seed phrase in 3-5 parti
   - Serve N parti per ricostruire (es. 3 su 5)
   - Conserva parti in luoghi diversi
   - Tool: `slip39` (Shamir's Secret Sharing)

2. **Steel Backup**:
   - Incidi seed phrase su piastre di acciaio
   - Resistente a fuoco, acqua, corrosione
   - Prodotti: Cryptosteel, Billfodl

3. **Multisig Wallet** (Pro):
   - Crea wallet multisig 2-of-3
   - Richiede 2 firme per transazioni
   - Maggiore sicurezza ma complessità elevata

### Phishing e Scam

⚠️ **Attenzione**:
- FlorenceEGI **MAI** ti chiederà la seed phrase
- Support **MAI** ti chiederà di condividere recovery phrase
- **NESSUNO** legittimo ha bisogno della tua seed phrase

🚨 **Scam Comuni**:
1. Email fake: "Verifica il tuo wallet, inserisci seed phrase"
2. Siti clone: Fake FlorenceEGI che rubano seed phrase
3. Support fake: Persone che si spacciano per support e chiedono seed

💡 **Regola d'Oro**: Se qualcuno ti chiede la seed phrase, è una TRUFFA al 100%.

---

## Alternative al Redemption

Se non sei sicuro di voler fare redemption, considera queste alternative:

### 1. Wallet Custodial (Default)

**Pro**:
- Gestito da FlorenceEGI (niente responsabilità)
- Nessun costo aggiuntivo
- Support disponibile
- Semplice e user-friendly

**Contro**:
- Non possiedi le chiavi private
- Limitato a FlorenceEGI

### 2. Wallet Connect (Coming Soon)

**Pro**:
- Colleghi wallet esterno senza redemption
- Mantieni custodial per backup
- Flessibilità

**Contro**:
- Non ancora disponibile

### 3. Hybrid Approach

**Pro**:
- Trasferisci solo alcuni EGI a wallet esterno (via rebind)
- Mantieni wallet custodial attivo
- Bilanciamento flessibilità/sicurezza

**Contro**:
- Devi comprare EGI su rebind (costi maggiori)
- Non hai controllo seed phrase

---

## Supporto e Assistenza

Hai domande sul wallet redemption?

📧 **Email**: wallet@florenceegi.com
💬 **Live Chat**: Disponibile Mon-Fri 9:00-18:00 CET
📚 **Centro Assistenza**: support.florenceegi.com/wallet-redemption
🎥 **Video Tutorial**: youtube.com/florenceegi/wallet-redemption

⚠️ **IMPORTANTE**: Support **MAI** ti chiederà la seed phrase! Se qualcuno lo fa, è una truffa.

---

**Ultimo aggiornamento**: 2026-02-08
**Versione documentazione**: 1.0.0
