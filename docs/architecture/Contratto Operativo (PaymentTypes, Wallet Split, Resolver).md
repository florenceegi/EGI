docs/architecture# CONTRATTO OPERATIVO — Payment Types + Wallet Split (v1)
Scopo: rendere deterministico quali pagamenti sono disponibili per una collection, e come si distribuiscono (mint/rebind) in base ai wallet. Eccezione: bonifico commodities.

---

## 0) Definizioni

### PaymentType (binari)
- STRIPE_CARD
- EGILI
- ALGO
- CRYPTO_EXCHANGE
- BANK_TRANSFER_COMMODITY   (speciale: solo commodities, flusso diverso)

### EgiKind
- STANDARD
- COMMODITY

### TxKind (azione economica)
- MINT_PRIMARY
- REBIND_SECONDARY

---

## 1) Regole di disponibilità dei pagamenti (Merchant → Collection)

### 1.1 Abilitazioni Merchant (globale)
Il merchant nel profilo abilita una lista di PaymentType “generali” che è disposto ad accettare.
Esempio: STRIPE_CARD, EGILI, ALGO, CRYPTO_EXCHANGE.

Regola:
- BANK_TRANSFER_COMMODITY NON è selezionabile qui (non è un “metodo normale”).

### 1.2 Offerta Collection (subset)
Ogni collection può “esporre” al buyer un subset dei metodi abilitati dal merchant.

Regola:
- collection_offered_payment_types ⊆ merchant_enabled_payment_types
- se la collection non specifica nulla, fallback = tutti i metodi abilitati dal merchant (eccetto BANK_TRANSFER_COMMODITY).

---

## 2) Eccezione Bonifico (solo commodities, obbligatorio)

### 2.1 Forzatura
Se EgiKind = COMMODITY:
- allowed_payment_types = [BANK_TRANSFER_COMMODITY] (solo quello)
- gli altri payment types non vengono mostrati al buyer (per ora).

Motivo: il flusso commodity è diverso (ordine → bonifico esterno → finalizzazione merchant).

### 2.2 Implicazioni sullo split
Nel binario BANK_TRANSFER_COMMODITY:
- NON si fa split in denaro verso più destinatari.
- Il bonifico va buyer → merchant (100% al merchant).
- La piattaforma addebita al merchant una fee di servizio in EGILI, calcolata così:
  - fee_eur = platform_fee_percent (fissa 10%) * merchant_margin_eur
  - fee_egili = fee_eur * EGILI_PER_EUR (es. 100 Egili = 1€ oppure come definito nel sistema)
- La fee viene addebitata nel momento della “finalizzazione” (quando il merchant conferma ricezione e trasferisce proprietà).

Nota: percentuali wallet mint/rebind NON si applicano a BANK_TRANSFER_COMMODITY.

---

## 3) Wallet Split: cosa rappresenta davvero
Un “wallet” collegato a una collection rappresenta:
1) “chi partecipa alla spartizione”
2) “con quale percentuale”
3) “dove riceve i soldi”, in base al PaymentType

### 3.1 Split Rules per TxKind
La collection deve poter avere percentuali diverse per:
- MINT_PRIMARY
- REBIND_SECONDARY

Regola:
- Per ogni TxKind, la somma delle percentuali deve essere 100.00
- La piattaforma può essere un destinatario (ruolo PLATFORM / NATAN) con percentuale propria.
  - Se PLATFORM è destinatario, la sua quota resta “trattenuta” (nessun trasferimento esterno).

### 3.2 Destinazioni (come riceve)
### 3.2 Destinazioni (come riceve)
Ogni wallet può avere destinazioni diverse per binario.
**Fonte di Verità:** Tabella `wallet_destinations` (1:N su Wallet).

Mapping per PaymentType:
- per STRIPE_CARD: `destination_value` = `stripe_account_id` (connected account)
- per ALGO: `destination_value` = `algo_address` (se diverso dal wallet address principale)
- per CRYPTO_EXCHANGE: `destination_value` = `external_reference`
- per EGILI: non serve destinazione esterna (è interno)

Regola:
- La ricerca della destinazione avviene PRIMA sulla tabella `wallet_destinations` cercado per `(wallet_id, payment_type)`.
- Se un wallet è destinatario con percentuale > 0 su un certo PaymentType, deve avere una destinazione valida configurata in questa tabella.
- **Eccezione:** PLATFORM/NATAN può non avere destinazione se i fondi sono "retained" (trattenuti alla fonte).

---

## 4) Risolutore unico: “quali pagamenti posso mostrare al buyer?”

### Funzione richiesta (logica)
Input:
- collection_id
- egi_kind (STANDARD/COMMODITY)

Output:
- lista allowed_payment_types ordinata

Algoritmo:
1) se egi_kind == COMMODITY → return [BANK_TRANSFER_COMMODITY]
2) altrimenti:
   - prende merchant_enabled_payment_types
   - prende collection_offered_payment_types (se vuoto: fallback = merchant_enabled)
   - calcola allowed = intersection(merchant_enabled, collection_offered)
   - filtra via metodi non “validi” per la collection (vedi 5.1)
   - return allowed

---

## 5) Validazioni obbligatorie (blocchi UX prima di vendere)

### 5.1 Validazione “metodo attivo”
Per ogni PaymentType che la collection vuole offrire:
- STRIPE_CARD:
  - tutti i wallet destinatari (escluso PLATFORM) devono avere stripe_account_id valido e “abilitato”
- ALGO:
  - tutti i wallet destinatari devono avere algo_address
- CRYPTO_EXCHANGE:
  - deve esistere configurazione provider + destinazione per wallet (se prevista)
- EGILI:
  - ok (interno), ma serve regola di sufficienza saldo buyer durante checkout

Se una validazione fallisce:
- quel PaymentType non viene mostrato al buyer
- nella dashboard merchant compare errore chiaro: “Completa X per abilitare Y”.

### 5.2 Validazione percentuali
Per ogni TxKind (MINT_PRIMARY / REBIND_SECONDARY):
- somma percentuali = 100.00 (tolleranza max 0.01)
- nessuna percentuale negativa
- se non conforme → blocco pubblicazione collection (o blocco vendita)

---

## 6) Esecuzione pagamenti: regola d’oro (per collegare ai flow)

### 6.1 STANDARD + STRIPE_CARD (schema già scelto)
- 1 pagamento unico sul conto piattaforma
- allo “succeeded” (webhook) la piattaforma esegue trasferimenti interni verso connected accounts
- quota PLATFORM resta trattenuta

### 6.2 COMMODITY + BANK_TRANSFER_COMMODITY
- buyer paga fuori (bonifico diretto al merchant)
- dopo 24h: se non finalizzato → apertura caso automatica
- merchant finalizza:
  - paga fee in EGILI (10% del suo margine)
  - trasferisce proprietà / mint
- se merchant non finalizza → misure progressive (supporto/caso)

---

## 7) Output atteso da Copilot (azioni in codebase)

### 7.1 Wallet management
Adeguare “wallet” per supportare:
- percentuali per TxKind (mint/rebind)
- destinazioni per PaymentType (stripe_account_id / algo_address / ecc.)
- ruolo PLATFORM “retained”

### 7.2 Payment availability
Implementare un resolver unico che applica:
- override commodity → solo bonifico
- altrimenti merchant_enabled ∩ collection_offered
- filtri per validazione destinazioni/percentuali

### 7.3 UI Dashboard (minimo)
- Merchant: abilita metodi globali (no bonifico)
- Collection: seleziona subset (solo se STANDARD)
- Collection commodity: mostra “Bonifico obbligatorio” (non selezionabile)

---

## 8) Nota ToS (solo punti da ricordare)
- Bonifico commodities: pagamento fuori piattaforma, la piattaforma non detiene fondi.
- Caso automatico a 24h se ordine non finalizzato.
- Finalizzazione implica fee in Egili calcolata sul margine merchant.
- Misure operative su merchant inadempiente (limitazioni/sospensione).
