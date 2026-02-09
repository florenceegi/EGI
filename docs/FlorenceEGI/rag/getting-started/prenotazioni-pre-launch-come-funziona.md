---
title: "Prenotazioni Pre-Launch: Come Funziona"
category: getting-started
description: "Guida completa al sistema di prenotazioni pre-launch con ranking pubblico"
author: "Padmin D. Curtis (AI Partner OS3.0)"
version: "1.0.0"
date: 2026-02-08
language: it
---

# Prenotazioni Pre-Launch: Come Funziona

## Cos'è il Sistema di Prenotazione Pre-Launch?

Il **Sistema di Prenotazione Pre-Launch** permette agli utenti di prenotare un EGI prima del suo lancio ufficiale, partecipando a un **ranking pubblico** basato sull'importo della prenotazione.

### Caratteristiche Principali

- **Nessun Pagamento Immediato**: La prenotazione non richiede pagamento, solo l'indicazione di un importo
- **Ranking Pubblico**: La classifica è visibile a tutti in tempo reale
- **Prenotazioni Multiple**: Più utenti possono prenotare lo stesso EGI contemporaneamente
- **Mint Window**: Il vincitore ha un periodo di tempo limitato per completare il mint
- **Accesso Aperto**: Anche gli utenti non registrati (guest) possono prenotare

---

## Come Funziona: Il Processo Completo

### Fase 1: Pre-Launch (Raccolta Prenotazioni)

Durante la fase pre-launch, gli EGI non sono ancora disponibili per il mint. Gli utenti possono:

1. **Visualizzare l'EGI**: Vedere dettagli, immagini, prezzo base
2. **Creare Prenotazione**: Indicare quanto sono disposti a offrire (in EUR)
3. **Vedere il Ranking**: Visualizzare la propria posizione nella classifica pubblica
4. **Aggiornare la Prenotazione**: Modificare l'importo per migliorare la posizione

#### Esempio di Ranking Pubblico

```
🏆 Ranking Prenotazioni - EGI #1234 "Sunset on Florence"
Prezzo base: €120

#1  👑 Marco R.      €350    (sei tu!)
#2     Laura T.      €280
#3     Francesco G.  €250
#4     Anonymous     €200
#5     Silvia M.     €150
...

📊 Totale prenotazioni: 12
💶 Importo medio: €220
```

### Fase 2: Lancio Ufficiale (Apertura Mint Window)

Quando l'artista/creator lancia ufficialmente l'EGI:

1. **Il Ranking si Blocca**: Non si accettano più nuove prenotazioni
2. **Vincitore Determinato**: L'utente con l'importo più alto (#1) vince
3. **Mint Window Aperto**: Il vincitore ha **48 ore** per completare il mint
4. **Notifica Inviata**: Il vincitore riceve email e notifica in-app

#### Mint Window: Le 48 Ore Critiche

Il **Mint Window** è il periodo di tempo entro cui il vincitore DEVE completare il mint:

- ⏰ **Durata**: 48 ore (2 giorni) dall'apertura
- 📧 **Notifiche**: Email + push notification + in-app
- 💳 **Pagamento**: Il vincitore paga l'importo che aveva indicato
- ⏳ **Scadenza Visibile**: Timer countdown in tempo reale

### Fase 3: Cosa Succede Dopo il Mint Window

#### Scenario A: Il Vincitore Completa il Mint ✅

1. Il vincitore paga l'importo prenotato
2. L'EGI viene mintato e trasferito al suo wallet
3. Le altre prenotazioni vengono **cancellate automaticamente**
4. Il processo si chiude con successo

#### Scenario B: Il Vincitore NON Completa il Mint ⏰

Se il vincitore non minta entro le 48 ore:

1. La sua prenotazione viene marcata come **EXPIRED**
2. Il sistema passa automaticamente al **2° in classifica**
3. Si apre un nuovo Mint Window di 48 ore per il 2°
4. Il processo continua fino a che qualcuno minta o la lista si esaurisce

#### Scenario C: Tutti i Prenotanti Scadono 🚫

Se nessuno completa il mint:

- L'EGI torna disponibile sul mercato primario
- Prezzo standard (prezzo base impostato dall'artista)
- Chiunque può mintare (first-come-first-served)

---

## Come Creare una Prenotazione

### Requisiti

✅ Nessun requisito di autenticazione (puoi prenotare come guest)
✅ L'EGI deve essere in stato "pre-launch"
✅ Importo minimo: €1
✅ Importo massimo: €1,000,000

### Procedura Passo-Passo

#### Passo 1: Trova l'EGI Pre-Launch

1. Naviga nella sezione **"EGI in Arrivo"** o **"Pre-Launch"**
2. Filtra per artista, categoria, data di lancio
3. Clicca sull'EGI che ti interessa

#### Passo 2: Visualizza Dettagli e Ranking

Sulla pagina dell'EGI vedrai:

- **Dettagli Opera**: Titolo, artista, descrizione, immagini
- **Prezzo Base**: Prezzo minimo impostato dall'artista (es. €120)
- **Data Lancio**: Quando verrà aperto il mint window
- **Ranking Attuale**: Classifica pubblicamente delle prenotazioni

#### Passo 3: Crea la Tua Prenotazione

1. Clicca su **"Prenota Ora"**
2. Inserisci l'importo che sei disposto a offrire (in EUR)
3. (Opzionale) Aggiungi una nota personale
4. Clicca su **"Conferma Prenotazione"**

💡 **Consiglio**: Per avere più probabilità di vincere, offri un importo superiore al prezzo base e superiore alle altre prenotazioni.

#### Passo 4: Controlla la Tua Posizione

Dopo aver creato la prenotazione:

- Vedi la tua posizione nel ranking in tempo reale
- Ricevi notifiche se qualcuno ti supera
- Puoi aggiornare l'importo in qualsiasi momento

---

## Come Aggiornare una Prenotazione

### Aumenta l'Importo per Scalare il Ranking

Se vedi che qualcuno ti ha superato nel ranking:

1. Vai alla tua prenotazione
2. Clicca su **"Aggiorna Importo"**
3. Inserisci un nuovo importo (deve essere **maggiore** del precedente)
4. Conferma

⚠️ **IMPORTANTE**:
- Puoi solo **aumentare** l'importo, non diminuirlo
- La tua vecchia prenotazione viene sostituita dalla nuova (sistema **supersede**)
- Puoi aggiornare illimitatamente fino al lancio ufficiale

### Esempio Supersede Logic

```
Timeline delle tue prenotazioni:

10:00 - Prenotazione #1: €200  → Posizione #3
11:30 - Prenotazione #2: €280  → Posizione #2  (supersede #1)
14:00 - Prenotazione #3: €350  → Posizione #1  (supersede #2)

✅ Solo la prenotazione #3 è valida e attiva
❌ Le prenotazioni #1 e #2 sono state superate
```

---

## Ranking Pubblico: Come Funziona

### Criteri di Ordinamento

Il ranking è determinato da:

1. **Importo EUR** (criterio principale): Chi offre di più è primo
2. **Timestamp** (criterio secondario): A parità di importo, chi ha prenotato prima è avvantaggiato

### Visibilità del Ranking

Il ranking è **pubblico** e visibile a tutti:

- ✅ **Tutti vedono**: Posizioni, importi, timestamp
- ✅ **Utenti autenticati**: Nome utente visibile
- ✅ **Utenti guest**: Mostrati come "Anonymous"
- ❌ **Privacy**: Le email e dati sensibili NON sono mai mostrati

### Aggiornamenti in Tempo Reale

Il ranking si aggiorna automaticamente ogni volta che:

- Un nuovo utente crea una prenotazione
- Un utente aggiorna l'importo della sua prenotazione
- Un utente ritira la sua prenotazione
- Un utente sale o scende di posizione

📊 **Dashboard Live**: Puoi tenere aperta la pagina del ranking e vedere gli aggiornamenti in tempo reale (via WebSocket).

---

## Ritira una Prenotazione

### Quando Ritirare

Puoi ritirare la tua prenotazione in qualsiasi momento prima del lancio:

- Hai cambiato idea
- Non puoi più permetterti l'importo indicato
- Hai trovato un EGI migliore

### Come Ritirare

1. Vai alle **"Mie Prenotazioni"**
2. Trova la prenotazione attiva
3. Clicca su **"Ritira Prenotazione"**
4. Conferma l'azione

⚠️ **ATTENZIONE**:
- Il ritiro è **irreversibile**
- La tua posizione nel ranking viene persa
- Puoi comunque creare una nuova prenotazione dopo

---

## Mint Window: Completa il Mint da Vincitore

### Notifica di Vincita

Quando vinci (sei #1 e il lancio è ufficiale), ricevi:

- 📧 **Email** con link diretto al mint
- 📱 **Notifica Push** (se attivata)
- 🔔 **Notifica in-app** con countdown

### Come Completare il Mint

#### Passo 1: Accedi al Mint Window

1. Clicca sul link nella email/notifica
2. Oppure vai su **"Mie Prenotazioni"** → **"Completa Mint"**
3. Vedrai il timer di scadenza (es. "Restano 47h 23m")

#### Passo 2: Scegli Metodo di Pagamento

Puoi pagare l'importo prenotato con:

- 💳 **Stripe** (carta di credito/debito)
- 💰 **PayPal**
- 🏦 **Bonifico Bancario**
- 🪙 **Egili** (crediti piattaforma)
- ⛓️ **Algorand** (ALGO cryptocurrency)

#### Passo 3: Conferma e Paga

1. Rivedi i dettagli:
   - EGI da mintare
   - Importo da pagare (quello che hai prenotato)
   - Commissioni (se applicabili)
2. Clicca su **"Conferma e Paga"**
3. Completa il pagamento tramite il PSP scelto

#### Passo 4: Mint Completato! 🎉

Una volta pagato:

- ✅ L'EGI viene mintato sulla blockchain Algorand
- ✅ L'ASA (Algorand Standard Asset) viene trasferito al tuo wallet custodial
- ✅ Ricevi conferma via email
- ✅ L'EGI appare nel tuo portfolio

---

## Cosa Succede Se Perdi il Mint Window

### Se Non Completi Entro 48 Ore

La tua prenotazione viene marcata come **EXPIRED**:

- ❌ Perdi la priorità di acquisto
- ⬇️ Il 2° in classifica diventa il nuovo vincitore
- 📧 Ricevi email di notifica "Mint Window Scaduto"
- 🔄 Puoi comunque mintare l'EGI se nessuno lo prende (prezzo standard)

### Estensioni del Mint Window

In casi eccezionali, il creator può:

- **Estendere il window** (es. da 48h a 72h)
- **Riattivare una prenotazione scaduta**
- **Modificare il processo di assegnazione**

📧 Riceverai notifica se il creator estende il tuo mint window.

---

## Prenotazioni Guest (Weak Auth)

### Cosa Sono le Weak Reservations

Gli utenti **non registrati** (guest) possono creare prenotazioni:

- ✅ Nessun account richiesto
- ✅ Solo email richiesta
- ✅ Partecipano al ranking pubblico
- ⚠️ Per mintare, devono registrarsi

### Come Funziona per i Guest

#### Fase 1: Prenotazione (Senza Account)

1. Navighi nell'EGI pre-launch
2. Clicchi su **"Prenota Ora"**
3. Inserisci:
   - Email (per notifiche)
   - Importo offerta
   - (Opzionale) Nome da mostrare nel ranking
4. Ricevi email di conferma con link di verifica

#### Fase 2: Mint (Richiede Account)

Se vinci il ranking e ricevi il mint window:

1. Ricevi email con link al mint
2. Ti viene chiesto di **registrarti o accedere**
3. Una volta autenticato, puoi completare il mint
4. La tua prenotazione guest viene collegata al tuo account

### Vantaggi Weak Auth

- 🚀 **Onboarding Veloce**: Prenoti in 30 secondi senza registrarti
- 🎯 **Basso Commitment**: Testi la piattaforma prima di registrarti
- 📈 **Conversione Graduale**: Ti registri solo se vinci

---

## Statistiche e Trasparenza

### Statistiche EGI Pre-Launch

Per ogni EGI in pre-launch puoi vedere:

| Metrica | Descrizione |
|---------|-------------|
| **Totale Prenotazioni** | Numero di utenti che hanno prenotato |
| **Importo Medio** | Media delle offerte |
| **Offerta Massima** | L'importo più alto prenotato (#1) |
| **Offerta Minima** | L'importo più basso tra i prenotanti |
| **Data Lancio** | Quando si apre il mint window |
| **Tempo Rimanente** | Countdown al lancio |

### Trasparenza del Ranking

- ✅ **Pubblico**: Chiunque può vedere il ranking completo
- ✅ **Real-Time**: Aggiornamenti in tempo reale
- ✅ **Storico**: Puoi vedere come è cambiato il ranking nel tempo
- ✅ **Audit Trail**: Ogni modifica è tracciata (chi, quando, quanto)

---

## Gestione Prenotazioni: Dashboard Utente

### Visualizza Tue Prenotazioni

Accedi a **"Mie Prenotazioni"** per vedere:

- **Prenotazioni Attive**: In attesa del lancio
  - Posizione nel ranking
  - Importo offerto
  - Tempo al lancio
- **Mint Windows Aperti**: Prenotazioni che hai vinto
  - Timer scadenza
  - Pulsante "Completa Mint"
- **Storico**: Prenotazioni passate
  - Completate (minted)
  - Scadute (expired)
  - Ritirate (withdrawn)
  - Superate (superseded)

### Filtri e Ricerca

Filtra le tue prenotazioni per:

- **Status**: Active, Expired, Completed, Withdrawn
- **Data Creazione**: Ultime 7 giorni, 30 giorni, sempre
- **Artista**: Filtra per creator specifico
- **Importo**: Ordina per importo offerto

---

## Notifiche: Rimani Aggiornato

### Tipi di Notifiche Reservation

Ricevi notifiche automatiche per:

| Evento | Notifica |
|--------|----------|
| **Prenotazione Creata** | "Prenotazione #123 confermata per EGI #456" |
| **Posizione Cambiata** | "Sei salito in posizione #2!" / "Sei sceso in posizione #4" |
| **Superato da Altri** | "Qualcuno ti ha superato nel ranking" |
| **Lancio Imminente** | "L'EGI verrà lanciato tra 24 ore" |
| **Hai Vinto!** | "Congratulazioni! Hai vinto la prenotazione" |
| **Mint Window Aperto** | "Hai 48 ore per completare il mint" |
| **Scadenza Imminente** | "Restano 6 ore per mintare!" |
| **Mint Window Scaduto** | "Il tuo mint window è scaduto" |
| **2° Posto Promosso** | "Sei stato promosso a #1! Nuovo mint window aperto" |

### Canali di Notifica

- 📧 **Email**: Sempre attiva (necessaria per guest)
- 📱 **Push Notification**: Attivabile da app mobile
- 🔔 **In-App**: Badge e notifiche sulla dashboard
- 💬 **SMS**: (Opzionale, per mint window critici)

### Personalizza Notifiche

Puoi configurare:

1. Vai su **"Impostazioni"** → **"Notifiche"**
2. Sezione **"Prenotazioni"**
3. Attiva/disattiva per tipo:
   - Ranking changes (On/Off)
   - Mint window (Sempre On, non disattivabile)
   - Launch reminders (On/Off)

---

## Domande Frequenti (FAQ)

### Pagamenti e Rimborsi

**Q: Devo pagare subito quando prenoto?**
A: **No**. La prenotazione non richiede pagamento. Paghi solo SE vinci e decidi di completare il mint.

**Q: Cosa succede se prenoto €500 ma l'EGI costa €100?**
A: Se vinci, paghi l'importo che hai prenotato (€500), NON il prezzo base. Stai offrendo di più per avere priorità.

**Q: Posso essere rimborsato se cambio idea?**
A: Durante la fase pre-launch (prima del mint window) puoi ritirare gratuitamente. Una volta aperto il mint window, se non completi il mint perdi semplicemente la priorità (nessun rimborso necessario perché non hai ancora pagato).

### Ranking e Priorità

**Q: Se offro lo stesso importo di un altro utente, chi vince?**
A: Chi ha prenotato **prima** (timestamp più vecchio) ha priorità.

**Q: Posso diminuire l'importo della mia prenotazione?**
A: **No**. Puoi solo aumentarlo. Se vuoi offrire meno, devi ritirare la prenotazione e crearne una nuova (perderai il timestamp originale).

**Q: Quante prenotazioni posso avere contemporaneamente?**
A: **Illimitate**, ma solo **1 prenotazione attiva per EGI**. Puoi prenotare 10 EGI diversi contemporaneamente.

### Mint Window e Scadenze

**Q: Cosa succede se pago dopo 48 ore?**
A: Il mint window è scaduto. L'EGI passa al 2° in classifica. Non puoi più mintare con priorità, ma puoi provare a comprarlo al prezzo standard (se disponibile).

**Q: Posso chiedere un'estensione del mint window?**
A: Contatta il creator dell'EGI. Solo loro possono estendere il window. Non garantito.

**Q: Ricevo reminder prima della scadenza?**
A: Sì. Ricevi notifiche a:
- 24 ore prima della scadenza
- 6 ore prima
- 1 ora prima
- 15 minuti prima (se attivi SMS/push)

### Guest e Autenticazione

**Q: Devo registrarmi per prenotare?**
A: **No**, puoi prenotare come guest fornendo solo l'email. MA devi registrarti per mintare se vinci.

**Q: Cosa succede se prenoto come guest e poi mi registro?**
A: Le tue prenotazioni guest vengono automaticamente collegate al tuo account (via email matching).

**Q: Posso prenotare con email temporanee/usa-e-getta?**
A: Tecnicamente sì, ma NON riceverai le notifiche di vincita e mint window. Altamente sconsigliato.

### Artisti e Creator

**Q: Come artista, posso vedere chi ha prenotato?**
A: Sì, dalla tua dashboard creator vedi:
- Lista completa prenotazioni
- Dati utenti (se autenticati)
- Statistiche (media, max, min)

**Q: Posso bloccare le prenotazioni?**
A: Sì, puoi chiudere le prenotazioni in anticipo e lanciare quando vuoi. Oppure puoi aspettare fino alla data prevista.

**Q: E se nessuno prenota il mio EGI?**
A: Nessun problema. Lanci normalmente l'EGI al prezzo base, disponibile per chiunque.

---

## Esempi Pratici

### Esempio 1: Prenotazione Semplice con Vittoria

**Scenario**:
- EGI: "Tuscan Sunset" di Laura M.
- Prezzo base: €150
- Data lancio: 15 febbraio 2026

**Timeline**:

**5 febbraio** - Marco crea prenotazione
```
Marco offre €200 → Posizione #1 (unico prenotante)
```

**8 febbraio** - Silvia entra nel ranking
```
Silvia offre €180 → Posizione #2
Marco rimane #1 (€200 > €180)
```

**10 febbraio** - Silvia aumenta
```
Silvia aggiorna a €250 → Posizione #1
Marco scende a #2
→ Marco riceve notifica "Sei stato superato"
```

**12 febbraio** - Marco risponde
```
Marco aggiorna a €300 → Posizione #1
Silvia scende a #2
```

**15 febbraio** - Lancio!
```
Marco vince! 🏆
→ Mint window aperto (scadenza: 17 febbraio ore 10:00)
→ Marco paga €300 e minta l'EGI
→ Prenotazione di Silvia viene cancellata
```

### Esempio 2: Vincitore Non Minta → Passaggio al 2°

**Timeline**:

**20 febbraio** - Lancio
```
#1 Francesco (€400) → Vince, mint window aperto
#2 Laura (€350)
#3 Paolo (€280)
```

**22 febbraio** - Scadenza Window di Francesco
```
Francesco NON completa il mint (48h scadute)
→ Laura promossa a #1! Nuovo mint window aperto
→ Laura ha 48h per mintare (scadenza: 24 febbraio)
```

**23 febbraio** - Laura minta
```
Laura paga €350 e completa il mint ✅
→ Prenotazione di Paolo cancellata
```

### Esempio 3: Guest Reservation

**Timeline**:

**1 marzo** - Anna (guest, non registrata) prenota
```
Email: anna@example.com
Importo: €200
→ Prenotazione creata (tipo: WEAK)
→ Posizione #1
```

**15 marzo** - Lancio, Anna vince
```
Anna riceve email: "Hai vinto! Registrati per mintare"
→ Anna clicca link e si registra
→ Account creato, prenotazione collegata
→ Anna completa il mint
```

---

## Best Practices per Vincere

### Strategia Timing

1. **Prenota Presto**: Il timestamp conta in caso di parità
2. **Monitora il Ranking**: Controlla ogni giorno se qualcuno ti ha superato
3. **Aggiorna Strategicamente**: Non rivelare subito il tuo massimo budget

### Strategia Budget

1. **Analizza la Concorrenza**: Vedi quanto offrono gli altri
2. **Valuta l'Opera**: Quanto vale per te? Quanto sei disposto a pagare?
3. **Offri con Margine**: Se vuoi davvero l'EGI, offri 20-30% sopra gli altri

### Strategia Notifiche

1. **Attiva Tutte le Notifiche**: Specialmente mint window
2. **Aggiungi Reminder Personali**: Calendar, allarmi telefono
3. **Prepara Metodo Pagamento**: Carta salvata, Egili caricati

### Strategia Mint Window

1. **Non Aspettare**: Minta appena aperto il window
2. **Verifica Fondi**: Assicurati di avere liquidità disponibile
3. **Testa il Flusso**: Fai un mint di prova su un EGI economico

---

## Supporto e Assistenza

Problemi con le prenotazioni?

📧 **Email**: reservations@florenceegi.com
💬 **Live Chat**: Disponibile Mon-Fri 9:00-18:00 CET
📚 **Centro Assistenza**: support.florenceegi.com/reservations
🎫 **Ticket System**: Per problemi tecnici urgenti

---

**Ultimo aggiornamento**: 2026-02-08
**Versione documentazione**: 1.0.0
