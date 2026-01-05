Bonifici A
# BONIFICO COMMODITIES RAIL — SPEC + TOS (FlorenceEGI)

## 1) Obiettivo
Consentire pagamenti via bonifico per COMMODITIES (solo merchant aziendali).
Il bonifico va direttamente dal buyer al merchant.
La piattaforma non incassa e non movimenta fiat.
La piattaforma monetizza tramite fee pagata dal merchant in Egili (su margine commodity).

## 2) Scope
- Metodo disponibile solo se:
  - EGI.type = commodity
  - merchant = company (KYC/Company verified)
  - collection/commodity ha: merchant_margin_percent OR merchant_margin_amount definito e bloccato
- NO EPP split su bonifico (per scelta commerciale)
- Egili: usati solo per fee piattaforma (e eventuali servizi interni)

## 3) Flusso Utente (semplice)
1. Buyer sceglie "Bonifico (Commodity)" al checkout.
2. Piattaforma mostra:
   - IBAN merchant
   - intestatario merchant
   - importo esatto
   - causale obbligatoria con "ORDER_CODE"
   - scadenza (es. 3 giorni lavorativi)
3. Ordine va in stato: WAITING_BANK_TRANSFER.
4. Buyer fa bonifico dalla sua banca (fuori piattaforma).
5. Merchant, quando vede i soldi, clicca "Conferma incasso".
6. Piattaforma richiede dati minimi:
   - importo ricevuto
   - data valuta
   - reference/identificativo (CRO/TRN se presente)
   - upload ricevuta (opzionale ma consigliato)
7. Alla conferma, la piattaforma calcola fee Egili e la scala dal wallet Egili del merchant.
8. Se Egili sufficienti:
   - stato ordine: BANK_TRANSFER_CONFIRMED + PLATFORM_FEE_PAID
   - trigger mint (job)
9. Se Egili insufficienti:
   - stato ordine: WAITING_EGILI_FEE
   - il mint NON parte finché il merchant non ricarica Egili e paga.

## 4) Calcolo Fee Egili
- commodity_price_eur = prezzo ordine
- merchant_margin_percent (es. 3%) oppure merchant_margin_amount_eur
- merchant_margin_eur = price_eur * margin_percent / 100 (oppure amount fisso)
- platform_fee_percent_on_margin = 10%
- platform_fee_eur = merchant_margin_eur * 10%  (es: 300 * 10% = 30€)
- EGILI_EUR_RATE = 0.01€ (1 Egili = 1 cent)
- platform_fee_egili = platform_fee_eur / EGILI_EUR_RATE  (30 / 0.01 = 3000 Egili)

Nota: se la "fee piattaforma standard" è 10%, dichiarare che:
- 0,5% è trattenuto come "Fondo gestione operativa"
- 9,5% è fee piattaforma effettiva
E che è una riduzione interna della fee, non un costo extra.

## 5) Stati Ordine (minimo)
- WAITING_BANK_TRANSFER
- BANK_TRANSFER_CONFIRMED
- WAITING_EGILI_FEE
- PLATFORM_FEE_PAID
- READY_FOR_MINT
- MINTED
- EXPIRED (se scade)
- CANCELLED

## 6) Anti-abuso (minimo)
- Merchant conferma sotto responsabilità: dichiara di aver ricevuto fondi.
- Audit log + timestamp + user_id.
- Rate limit / controllo duplicati su ORDER_CODE.
- Se merchant conferma e poi contesta: responsabilità merchant.

## 7) Testo ToS (bozza, chiaro e corto)
### 7.1 Bonifico diretto
"Il pagamento tramite bonifico è effettuato direttamente dal Cliente al Merchant. FlorenceEGI non riceve, non detiene e non trasferisce fondi relativi al bonifico."

### 7.2 Rischio e responsabilità
"Il Cliente è responsabile dell’esecuzione corretta del bonifico (IBAN, importo, causale). Il Merchant è responsabile della verifica dell’incasso e dell’eventuale gestione di rimborsi o contestazioni relative al bonifico."

### 7.3 Tempi e scadenza
"Se il bonifico non risulta confermato dal Merchant entro X giorni lavorativi, l’ordine può essere annullato automaticamente."

### 7.4 Condizione di mint
"Il mint/trasferimento del certificato digitale avviene solo dopo: (a) conferma incasso del Merchant; (b) pagamento della fee piattaforma in Egili, quando prevista."

### 7.5 Fee piattaforma in Egili
"Per ordini Commodity pagati via bonifico, la fee di piattaforma è dovuta dal Merchant ed è calcolata come percentuale del margine definito nella scheda prodotto/commodity. La fee è regolata in Egili e viene addebitata al wallet Egili del Merchant."

### 7.6 Nessuno split su bonifico
"Per il metodo bonifico, non è previsto split automatico verso terzi (es. programmi ambientali o altri beneficiari). Eventuali accordi economici tra Merchant e terzi restano esterni a FlorenceEGI."

### 7.7 Supporto UX
"FlorenceEGI fornisce supporto informativo e tracciamento dello stato ordine, senza assumere ruolo di intermediario finanziario del bonifico."



Bonifici B
# BONIFICO COMMODITY — Monitoraggio + Caso Automatico (v1)

## Obiettivo
- Pagamento via bonifico avviene fuori piattaforma (buyer → merchant).
- La piattaforma monitora i tempi e apre un caso automatico dopo 24h se l’ordine non è finalizzato.
- La piattaforma offre supporto operativo e tutela l’utente con danno maggiore quando esistono evidenze coerenti.

---

## Stati Ordine (Bank Transfer)

### Stati principali
- WAITING_BANK_TRANSFER
- CASE_OPENED_24H
- WAITING_BUYER_PROOF
- WAITING_MERCHANT_CHECK
- READY_FOR_FINALIZATION   (merchant può finalizzare)
- FINALIZATION_IN_PROGRESS
- COMPLETED
- CANCELLED / EXPIRED

### Regola: 24h
- Dopo 24h dall’ordine, se non COMPLETED → apertura caso automatica:
  - stato: CASE_OPENED_24H
  - creazione case_id
  - notifica buyer + merchant

---

## Trigger & Automazioni

### T0: ordine creato
- Stato: WAITING_BANK_TRANSFER
- Salva: iban, intestatario, causale suggerita, importo
- Avvia timer: case_open_at = created_at + 24h

### T+24h: apertura caso automatica
Condizione: stato != COMPLETED && stato != CANCELLED
Azione:
- crea record Case (case_id)
- aggiorna stato ordine: CASE_OPENED_24H
- invia notifiche:
  - Buyer: “Se hai già fatto il bonifico, carica la prova”
  - Merchant: “Ordine non finalizzato: verifica incassi e causale”

### Buyer carica prova
- Stato: WAITING_MERCHANT_CHECK
- Salva: proof_file, trn/cro se presente, data/ora, importo, note
- Marca case: buyer_proof_received_at

### Merchant risponde
Possibili azioni merchant:
1) “Non risulta ancora” → resta WAITING_BANK_TRANSFER con case aperto (supporto continua)
2) “Ricevuto” → richiede finalizzazione (vedi sotto)
3) “Bonifico errato” (IBAN/importo/causale) → richiede correzione al buyer

---

## Finalizzazione (da Merchant) + Fee in Egili

### Bottone Merchant
“Conferma ricezione bonifico e trasferisci proprietà”

Al click:
1) apri modale esistente “Scarico Egili”
2) se saldo insufficiente → modale esistente “Acquista Egili”
3) se pagamento Egili OK → esegui finalizzazione

### Finalizzazione
- Stato: FINALIZATION_IN_PROGRESS
- Azione: trasferimento proprietà / mint finale / certificato
- Se OK → COMPLETED
- Se FAIL → stato: FINALIZATION_FAILED (con retry)

---

## UX: Testi (semplici)

### Buyer (ordine)
- Stato WAITING_BANK_TRANSFER:
  “Hai scelto bonifico. Invia il pagamento al merchant e conserva la ricevuta.”
- Dopo 24h (CASE_OPENED_24H):
  “Non vediamo ancora l’ordine completato. Se hai già fatto il bonifico, carica la ricevuta: ti aiutiamo a chiudere il ciclo.”

### Merchant (ordine)
- Stato WAITING_BANK_TRANSFER:
  “In attesa del bonifico. Quando lo ricevi, completa il trasferimento di proprietà.”
- Dopo 24h:
  “Ordine aperto da 24h. Verifica l’incasso (importo e causale). Se ricevuto, completa il trasferimento.”

---

## Regola “tutela danno maggiore” (senza fare banca)

### Se buyer invia prova coerente
(coerenza = importo corretto + IBAN corretto + causale ragionevole + data compatibile)
- case_priority = BUYER_PROTECTED
- richiedi al merchant:
  - conferma ricezione OPPURE motivazione “non risulta” con check interno
- se merchant non collabora → limitazioni progressive (sotto)

### Se prova non coerente / assente
- case_priority = NEUTRAL_SUPPORT
- supporto tecnico: correggere IBAN/causale/importo, attendere tempi bancari

---

## Misure Progressive (solo operative, non finanziarie)

- Dopo 48h con buyer_proof coerente e merchant inattivo:
  - lock: nuove vendite commodity disabilitate (soft)
- Dopo 72h con buyer_proof coerente e merchant ancora inattivo:
  - sospensione merchant (hard)
  - case_flag = FRAUD_SUSPECTED
- Sempre: log completo + conservazione evidenze

---

## ToS — testo pronto

### Natura del bonifico
“Il pagamento via bonifico avviene direttamente tra Cliente e Merchant, fuori dalla piattaforma. La piattaforma non detiene i fondi né opera come banca o servizio di incasso.”

### Monitoraggio e caso automatico
“Se un ordine pagato via bonifico non viene finalizzato entro 24 ore, la piattaforma può aprire automaticamente un caso di assistenza per facilitare la chiusura dell’ordine.”

### Evidenze
“Nell’ambito del caso di assistenza, la piattaforma può richiedere al Cliente evidenze dell’avvenuto bonifico (es. ricevuta, riferimento TRN/CRO) e al Merchant verifiche interne. Tali informazioni sono utilizzate esclusivamente per la gestione della controversia e per finalità di sicurezza e prevenzione abusi.”

### Misure verso merchant inadempienti
“In caso di ritardi ingiustificati, mancata collaborazione o condotte sospette, la piattaforma può applicare limitazioni operative, sospensione dell’account e rimozione delle inserzioni.”

### Trasferimento proprietà
“Per le Commodities pagate via bonifico, il trasferimento di proprietà e/o l’aggiornamento del certificato digitale avviene solo dopo conferma di ricezione del bonifico da parte del Merchant e completamento delle procedure di finalizzazione previste dalla piattaforma.”

---

## Note Implementative (minime)
- Scheduler/Job: `OpenBankTransferCasesJob` (ogni ora) → apre i case a 24h
- Tabelle suggerite:
  - `bank_transfer_orders` (o fields su orders)
  - `cases` (case_id, order_id, status, priority, timestamps)
  - `case_messages` / `case_events` (audit)
- Reuse modali esistenti Egili:
  - action_code = `COMMODITY_FINALIZATION_FEE`
