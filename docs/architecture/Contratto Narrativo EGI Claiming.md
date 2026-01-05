# FlorenceEGI — Payment Split + Risk Reserve (0.5%) + ToS Text
Versione: v1.0 (per Copilot) — Data: 2026-01-01  
Obiettivo: implementare e documentare la strategia completa “Incasso su piattaforma → Split su NETTO → Fee piattaforma 10% (9.5 ricavo + 0.5 fondo) → Recupero su refund/dispute senza fare da banca”.

---

## 0) Decisioni già prese (non discutere, implementare)
1) **Incasso iniziale sempre su Stripe account della piattaforma** (merchant of record lato Stripe).
2) **1 PaymentIntent = 1 acquisto** (no multi-payment-intent).
3) **Split SOLO dopo `payment_intent.succeeded`** (webhook-driven, mai nel controller).
4) Split via **Stripe Transfer** verso connected accounts esterni.
5) Split calcolato sul **NETTO reale** dopo fee Stripe (non sul lordo).
6) **Fee piattaforma = 10% del NETTO**.
   - Dentro quel 10%: **9,5% ricavo piattaforma (Natan)** + **0,5% fondo rischio/operativo (cassa)**.
   - Il 0,5% NON è un costo aggiuntivo: è una trattenuta interna dentro il 10%.
7) **Scudo UX e supporto**: FlorenceEGI gestisce il cliente.
   - NON “assicurazione finanziaria” per i merchant: se non si riesce a recuperare, si passa a compensazione/sospensione.

---

## 1) Glossario minimo (interno)
- **NETTO (net_amount)**: importo realmente disponibile nel saldo Stripe della piattaforma dopo fee Stripe.
- **Fee piattaforma (platform_fee_total)**: 10% del NETTO.
- **Ricavo piattaforma (platform_fee_revenue)**: 9,5% del NETTO.
- **Fondo (platform_fee_reserve)**: 0,5% del NETTO.
- **Distribuibile (distributable_to_wallets)**: NETTO - platform_fee_total.
- **Wallet esterni**: Creator/Merchant/Partner/EPP/Frangette ecc. (connected accounts).  
- **Wallet interni**: Natan + Reserve (non trasferiti: “platform_retained”).

---

## 2) Flusso (solo pagamento + split + mint)
### 2.1 Creazione pagamento
- Controller chiama `StripeRealPaymentService::processPayment()`
- Crea **1 PaymentIntent** per l’importo lordo che vede il buyer.
- Nessuno split qui.

### 2.2 Webhook di successo (unico trigger)
Quando arriva `payment_intent.succeeded`:
1) Identifica il record interno (es. `EgiBlockchain`) tramite `payment_intent_id`.
2) Ricava **NETTO reale** dal PaymentIntent → Charge → Balance Transaction (per fee e net).
3) Calcola fee piattaforma (10% netto) + ripartizione wallet esterni.
4) Registra in DB le righe “pending” e poi esegue i Transfer Stripe con idempotency.
5) Se split completato: sblocca il mint (job).
6) Se split fallisce: blocca mint e segna stato “payment_split_failed”.

---

## 3) Calcolo importi (regole e arrotondamenti)
### 3.1 NETTO reale
- Recuperare:
  - PaymentIntent
  - prima Charge del PaymentIntent
  - BalanceTransaction della Charge
- Da BalanceTransaction ottenere:
  - `net_amount_cents`
  - `fee_amount_cents` (solo logging/trasparenza)

**Fonte di verità per lo split: `net_amount_cents`.**

### 3.2 Fee piattaforma e fondo (dentro la fee)
Calcoli in centesimi (sempre int):
- `platform_fee_total_cents = round(net_amount_cents * 0.10)`
- `platform_fee_reserve_cents = round(net_amount_cents * 0.005)`   // 0,5% del netto
- `platform_fee_revenue_cents = platform_fee_total_cents - platform_fee_reserve_cents`

### 3.3 Distribuibile ai wallet esterni
- `distributable_cents = net_amount_cents - platform_fee_total_cents`

### 3.4 Percentuali wallet esterni
Regola consigliata (semplice, stabile):
- Le percentuali dei wallet esterni devono sommare **90,00%** (perché 10% è piattaforma).
- Validazione: `sum_external_percent == 90.00` (tolleranza micro: 89.99–90.01 solo se serve per rounding).

**Distribuzione**:
- Per ogni wallet esterno:  
  `wallet_amount_cents = round(net_amount_cents * (wallet_percent / 100))`
  (cioè calcolato sul NETTO, non sul “distributable”, così le % restano “umane” e leggibili.)

### 3.5 Rounding / “cent leftover”
Dopo i round, può rimanere:
- `delta = net_amount_cents - (platform_fee_total_cents + sum(wallet_amount_cents esterni))`

Regola:
- Se `delta != 0`, assegnare `delta` al **platform_fee_reserve_cents** (o, in alternativa, al revenue).  
Scelta proposta: **al fondo** (così il fondo assorbe gli errori di centesimi e cresce/decresce in modo coerente).

---

## 4) Idempotenza e atomicità (punto 3 del tuo riassunto)
### 4.1 Webhook event dedupe
- Salvare evento webhook in tabella (o cache) con `stripe_event_id`.
- Se evento già visto: return 200 “already processed”.

### 4.2 PaymentDistribution come ledger
Prima di chiamare Stripe Transfer:
- creare righe `PaymentDistribution` in stato `pending` (una per wallet esterno + 2 interne: Natan+Reserve).
Dopo Transfer ok:
- aggiornare righe a `completed` con `transfer_id`.
Se fallisce:
- `failed` + motivo.

### 4.3 Unique + idempotency_key per Transfer
- Unique DB: `UNIQUE(payment_intent_id, wallet_id)`
- `idempotency_key = "{payment_intent_id}_{wallet_id}"` nella chiamata Stripe Transfer
- Se record esiste già con transfer_id → skip.

---

## 5) Refund & Reversal (e “non facciamo gli assicuratori”)
### 5.1 Refund richiesto (admin o sistema)
Quando si crea un refund sul PaymentIntent:
1) esegui refund su Stripe (API)
2) poi fai **reverse** dei transfer registrati su `payment_distributions` (quelli completed, con transfer_id)
3) aggiorna righe:
   - `reversed` con `reversal_id` e `reversed_at`
   - se reversal fallisce → `reversal_failed` + motivo

### 5.2 Se reversal fallisce (soldi già prelevati dal merchant)
Qui entra la strategia “non banca ma recupero”:
- generare **debito** a carico del wallet/merchant (ledger interno).
- recuperare automaticamente su vendite future (compensazione).
- se debito supera soglia → sospendere trasferimenti o vendite per quel wallet.

> Nota: la piattaforma non promette copertura totale: questa è tutela operativa, non assicurazione.

---

## 6) Dispute / Chargeback (GAP)
### Stato attuale
- Documento deve dire la verità:
  - Se handler dispute non è implementato, segnare come **TODO**.

### Implementazione richiesta (da fare)
- Gestire webhook `charge.dispute.created` e `charge.dispute.closed`
- Collegare a PaymentIntent (via charge)
- Avviare flusso:
  - log + stato interno
  - tentativo reversal transfers
  - se non recuperabile → debito/compensazione

---

## 7) DB: modifiche minime richieste (proposte)
### 7.1 payment_distributions
Richiesti campi per fondo e fee:
- `net_amount_cents` (sul “gruppo” o replicato sulle righe)
- `stripe_fee_cents` (facoltativo, utile)
- `is_internal_retained` boolean (o `destination_type`)
- ruoli interni:
  - `platform_role = NATAN_REVENUE`
  - `platform_role = PLATFORM_RESERVE`

### 7.2 tabella webhook_events (consigliata)
- `provider`
- `event_id` (unique)
- `type`
- `payment_intent_id`
- `processed_at`
- `payload_hash` (opzionale)

### 7.3 tabella wallet_debts (o ledger equivalente)
Minimo indispensabile:
- `wallet_id`
- `amount_cents` (positivo = debito)
- `reason` (refund/dispute/reversal_failed)
- `related_payment_intent_id`
- `status` (open/settled)
- `created_at`

---

## 8) Regole di compensazione (recupero su vendite future)
Quando un wallet ha debito aperto:
- prima di trasferire soldi su una nuova vendita:
  - calcola il payout
  - trattieni fino a coprire debito (anche 100% del payout)
  - aggiorna debito
- se debito si azzera: status settled

Soglie configurabili (env/config):
- `MAX_DEBT_BEFORE_SUSPEND_CENTS`
- `SUSPEND_ON_DEBT=true`
- `MAX_RETRY_REVERSAL=3`

---

## 9) Aggiornamenti UI/UX (minimi)
- Merchant vede:
  - pagamenti ricevuti
  - eventuale debito e piano di recupero (semplice)
  - stato account Stripe
- NON vede “fondo” come fee extra.
- Natan nel menu resta “10% piattaforma”, ma nel dettaglio spieghiamo:
  - 9,5 ricavo
  - 0,5 accantonamento interno

---

## 10) Testi ToS (da incollare)
### 10.1 Pagamenti e split (chiaro)
FlorenceEGI gestisce l’incasso tramite provider terzi (es. Stripe).  
Il pagamento del cliente viene processato dal provider e accreditato sul saldo di FlorenceEGI.  
Successivamente, FlorenceEGI dispone la ripartizione degli importi ai destinatari associati alla Collection/EGI.

### 10.2 Fee piattaforma 10% con fondo interno 0,5% (non extra)
FlorenceEGI trattiene una commissione di piattaforma pari al **10% dell’importo netto** della transazione (ossia al netto delle commissioni applicate dal provider di pagamento).  
All’interno di tale commissione, **il 9,5%** rappresenta il ricavo della piattaforma e **lo 0,5%** viene accantonato in un fondo operativo interno per gestione costi e attività di tutela (es. rimborsi, contestazioni, costi tecnici e amministrativi).  
Tale accantonamento **non costituisce un costo aggiuntivo** per l’utente o per i Merchant, ma una trattenuta interna alla commissione di piattaforma.

### 10.3 Rimborsi e contestazioni (scudo UX, non assicurazione)
In caso di rimborso, contestazione o chargeback, FlorenceEGI gestisce il processo verso il cliente.  
Qualora una transazione venga rimborsata o contestata dopo che gli importi siano già stati ripartiti, il Merchant autorizza FlorenceEGI a recuperare le somme dovute, in ordine di priorità:
1) tramite storno/revoca dei trasferimenti già eseguiti, se tecnicamente possibile;  
2) tramite compensazione automatica su incassi futuri fino al rientro del saldo;  
3) ove necessario, tramite sospensione temporanea dei trasferimenti e/o della possibilità di vendere fino alla regolarizzazione.

FlorenceEGI **non fornisce copertura assicurativa** e non garantisce l’assenza di rimborsi o contestazioni.

### 10.4 Obbligo di collaborazione
Il Merchant si impegna a fornire tempestivamente informazioni e prove richieste (es. consegna, autenticità, policy) utili alla gestione di rimborsi/contestazioni.

---

## 11) Checklist per Copilot (implementazione)
### P0 — Calcolo su NETTO
- [ ] In webhook `payment_intent.succeeded`, recuperare net da balance_transaction
- [ ] Salvare net/fee nei log e/o DB

### P0 — Fee piattaforma 10% con fondo 0,5% interno
- [ ] Calcolare: total 10%, reserve 0.5%, revenue = total - reserve
- [ ] Creare 2 righe PaymentDistribution interne “platform_retained”

### P0 — Split wallet esterni
- [ ] Validare somma percentuali wallet esterni = 90%
- [ ] Creare righe pending + Transfer con idempotency_key
- [ ] Gestire delta centesimi → al reserve

### P0 — Mint condizionale
- [ ] Mint solo se split completato con successo

### P1 — Refund con reversal + debito
- [ ] Refund API
- [ ] Reversal Transfer dove possibile
- [ ] Se reversal fallisce: creare wallet_debt e compensazione su incassi futuri

### P1 — Webhook event dedupe (tabella consigliata)
- [ ] Registrare `stripe_event_id` processed (unique)
- [ ] Skip su duplicati

### P2 — Dispute handler (se non esiste davvero)
- [ ] Implementare `charge.dispute.created/closed`
- [ ] Collegare a charge → payment_intent
- [ ] Applicare reversal / debito / sospensione

---

## 12) Esempio numerico (da usare nel documento e nei ToS FAQ)
- Buyer paga: 100,00
- Fee Stripe: 3,30  (esempio)
- NETTO: 96,70

Fee piattaforma (10% del netto): 9,67  
- Ricavo piattaforma (9,5%): 9,19  
- Fondo interno (0,5%): 0,48

Restante per wallet esterni (90%): 87,03  
Distribuito secondo percentuali wallet esterni.

---

## 13) Nota finale per Copilot
- Non introdurre nuove tecnologie o librerie.
- Aggiornare anche `PAYMENT_SYSTEM_ARCHITECTURE_v2_4_2.md` con:
  - calcolo su netto
  - fee 10% = 9.5 + 0.5 interno
  - compensazione debiti (policy)
  - dispute handler: vero stato (TODO se non fatto)
