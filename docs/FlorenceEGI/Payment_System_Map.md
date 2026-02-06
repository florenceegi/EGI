# Mappa Sistema Pagamenti — FlorenceEGI (Documentazione + Codebase)

**Data**: 2026-02-06

## 1) Riferimento Funzionale (Documento Ufficiale)

**Fonte**: [docs/FlorenceEGI/04_Gestione_Pagamenti.md](docs/FlorenceEGI/04_Gestione_Pagamenti.md)

- **Livello 1 (FIAT, no wallet)**: PSP (Stripe/Adyen/PayPal), split off‑chain, EGI in profilo utente.
- **Livello 2 (FIAT, wallet utente)**: PSP FIAT, mint + transfer su wallet esterno.
- **Livello 3 (Crypto via PSP CASP)**: pagamento crypto su partner (es. Coinbase). FlorenceEGI riceve notifica, non custodisce fondi.
- **Livello 4 (Egili)**: pagamento interno non‑crypto, burn Egili, mint asincrono standard.

## 2) Architettura Reale in Codebase

### 2.1 Mint EGI (Primario) — FIAT / Egili

**Controller**

- [app/Http/Controllers/MintController.php](app/Http/Controllers/MintController.php)
    - `showPaymentForm()` (checkout)
    - `processMint()` (esecuzione pagamento + creazione record blockchain)

**Provider PSP**

- [app/Services/Payment/PaymentServiceFactory.php](app/Services/Payment/PaymentServiceFactory.php)
    - `stripe` → StripeRealPaymentService
    - `paypal` → PayPalRealPaymentService

**Stripe**

- [app/Services/Payment/StripeRealPaymentService.php](app/Services/Payment/StripeRealPaymentService.php)
    - `processPayment()` crea `PaymentIntent` o `Checkout Session`
    - `processPaymentWebhook()` gestisce split su webhook

**PayPal**

- [app/Services/Payment/PayPalRealPaymentService.php](app/Services/Payment/PayPalRealPaymentService.php)

**Split (multi‑wallet)**

- [app/Services/Payment/StripePaymentSplitService.php](app/Services/Payment/StripePaymentSplitService.php)
    - `splitPaymentToWallets()`

**Webhook PSP**

- [app/Http/Controllers/Payment/PspWebhookController.php](app/Http/Controllers/Payment/PspWebhookController.php)
- [routes/api.php](routes/api.php)

**Record Mint**

- `EgiBlockchain` creato/aggiornato in `processMint()`

### 2.2 Rebind (Secondario) — FIAT / Egili

**Controller**

- [app/Http/Controllers/RebindController.php](app/Http/Controllers/RebindController.php)
    - `show()` (checkout)
    - `process()` (pagamento + trasferimento proprietà)

**PSP Seller (collector)**

- Risoluzione PSP sul **venditore** via `MerchantAccountResolver::resolveForUserAndProvider()`.
- Validazione PSP del venditore in `show()`/`process()` via `validateUserWallets()`.

**Distribuzioni rebind**

- `PaymentDistribution` con percentuali da wallet collection (campo `royalty_rebind`)

### 2.3 Egili (Token interno)

**Service**

- [app/Services/EgiliService.php](app/Services/EgiliService.php)
    - `getBalance()`, `fromEur()`, `spend()`

**Mint/Rebind**

- Pagamento Egili gestito in `MintController` e `RebindController`

### 2.4 Acquisto Egili (FIAT / Crypto)

**Controller**

- [app/Http/Controllers/EgiliPurchaseController.php](app/Http/Controllers/EgiliPurchaseController.php)

**Nota normativa**

- Egili **non devono essere acquistabili** (da rimuovere o disabilitare).

### 2.5 Crypto PSP (EGI) — Stato Attuale

**Gateway disponibile**

- [app/Services/Payment/CryptoPaymentGateway.php](app/Services/Payment/CryptoPaymentGateway.php)

**Nota**

- Non integrato in `MintController` / `RebindController`.

### 2.6 Payment Settings (UI/Accesso) — Stato Attuale

**UI**
- [resources/views/settings/payments/index.blade.php](resources/views/settings/payments/index.blade.php)
    - Blocco UI per utenti non seller: `@if (!$user->isSeller())`

**Route note**
- [routes/web.php](routes/web.php) — commento: “Sellers only - Collectors excluded”

**Model Gate**
- [app/Models/User.php](app/Models/User.php)
    - `isSeller()` ritorna `false` per `collector`

## 3) Fee Structure (Contributor / Normal)

**Enum**

- [app/Enums/Fees/FeeStructureEnum.php](app/Enums/Fees/FeeStructureEnum.php)
    - `CONTRIBUTOR_MINT`, `CONTRIBUTOR_REBIND`, `NORMAL_MINT`, `NORMAL_REBIND`

## 4) EPP (Definizione ufficiale)

**Termini legali**

- [resources/legal/terms/versions/current/it/epp.php](resources/legal/terms/versions/current/it/epp.php)

**Compliance**

- [docs/FlorenceEGI/03_Compliance_e_Governance.md](docs/FlorenceEGI/03_Compliance_e_Governance.md)

**Concetti base**

- [docs/CONCETTI_FONDAMENTALI_FLORENCEEGI_NUOVO.md](docs/CONCETTI_FONDAMENTALI_FLORENCEEGI_NUOVO.md)

**Glossario**

- [docs/FlorenceEGI/09_Glossario_Completo.md](docs/FlorenceEGI/09_Glossario_Completo.md)

## 5) Note Operative Chiave

- **Ogni user** ha `stripe_account_id` (tabella `users`).
- **Natan (user_id = 1)** rappresenta il conto della piattaforma.
- **STRYPE_ACCOUNT_ID_NATAN** in `.env` è il conto generale per incasso iniziale e split.

## 6) Gap Principali (da pianificare)

1. **Rimozione acquisto Egili** (normativa).
2. **Integrazione Crypto PSP (CASP)** per mint/rebind EGI (es. Coinbase Commerce).
3. **Rifattorizzazione fee piattaforma** secondo nuova logica (incasso su conto generale, split e trattenute interne).
4. **Collector abilitati ai pagamenti**: rimuovere il blocco `isSeller()` per `collector` e prevedere UI/flow di configurazione PSP per vendita sul secondario.

---

## 7) Flusso logico completo — Collector (Secondario)

### 7.1 Obiettivo
Consentire ai **collector** di vendere sul mercato secondario (rebind), quindi **configurare PSP**, **ricevere pagamenti**, **gestire payout** e **tracciabilità**.

### 7.2 Stato attuale (blocchi)
- **UI**: `settings/payments/index` mostra banner “solo seller”.
- **Model**: `User::isSeller()` restituisce `false` per `collector`.
- **Route comment**: “Sellers only - Collectors excluded”.

### 7.3 Flusso target (end‑to‑end)

#### A) Accesso impostazioni pagamenti (Collector)
1. Collector apre **Impostazioni Pagamenti**.
2. Sistema **non blocca** in base a `isSeller()`.
3. Collector vede metodi disponibili (Stripe/PayPal/IBAN).
4. Configura metodo PSP (es. Stripe Connect).

**Output atteso**:
- Record `user_payment_methods` con `is_enabled`, `is_default`.
- `users.stripe_account_id` valorizzato (se Stripe).

#### B) Messa in vendita EGI sul secondario
1. Collector possiede EGI (owner_id).
2. Imposta prezzo e abilita vendita rebind.
3. Sistema verifica che il collector abbia **PSP configurato**.
4. EGI diventa acquistabile sul secondario.

**Output atteso**:
- EGI visibile con prezzo.
- Stato “vendibile” coerente con PSP configurato.

#### C) Checkout rebind (Buyer)
1. Buyer seleziona EGI in rebind.
2. `RebindController::show()` prepara checkout.
3. Vengono validati i metodi PSP del **seller (collector)**.
4. Se PSP OK → mostra opzioni pagamento.

#### D) Pagamento rebind (PSP)
1. Buyer paga con Stripe/PayPal.
2. PSP conferma pagamento (webhook).
3. `RebindController::process()` risolve PSP sul seller e crea `PaymentDistribution`.

**Output atteso**:
- Distribuzione venditore (collector) + royalties rebind.
- `Egi.owner_id` aggiornato al buyer.

#### E) Payout al collector
1. Stripe/PayPal invia payout sul conto PSP del collector.
2. Piattaforma registra metadata PSP e stato distribuzione.

### 7.4 Punti di implementazione necessari

#### UI/Accesso
- Rimuovere blocco `@if (!$user->isSeller())` per `collector`.
- Permettere onboarding PSP in **settings/payments** anche ai collector.

#### Model/Policy
- Aggiornare `User::isSeller()` per includere `collector` come seller **per rebind**.
- Verificare permessi su `PaymentSettingsController` (non bloccare collector).

#### Validazioni
- Verifica PSP configurato **prima** di rendere vendibile un rebind.
- Messaggi chiari quando PSP non configurato.

#### Rebind pipeline
- Assicurare che `RebindController` usi il PSP del **seller collector**.
- Payout verso `users.stripe_account_id` del collector.

### 7.5 Checklist operativa
- [ ] Collector vede pagina pagamenti
- [ ] Collector collega Stripe/PayPal
- [ ] Collector mette in vendita un EGI
- [ ] Buyer paga
- [ ] Payout verso PSP collector
- [ ] Ownership aggiornato
- [ ] Audit trail presente
