# Mappa Sistema Pagamenti — FlorenceEGI (Documentazione + Codebase)

**Data**: 2026-02-06

## 1) Riferimento Funzionale (Documento Ufficiale)

**Fonte**: [docs/FlorenceEGI/04_Gestione_Pagamenti.md](docs/FlorenceEGI/04_Gestione_Pagamenti.md)

- **Livello 1 (FIAT, no wallet)**: PSP (Stripe/Adyen/PayPal), split off‑chain, EGI in profilo utente.
- **Livello 2 (FIAT, wallet utente)**: PSP FIAT, mint + transfer su wallet esterno.
- **Livello 3 (Crypto via PSP CASP)**: pagamento crypto su partner (es. Coinbase). FlorenceEGI riceve notifica, non custodisce fondi.
- ~~**Livello 4 (Egili)**~~: ❌ **RIMOSSO in ToS v3.0.0** — Egili non più utilizzabili per acquisto EGI. Ora sono crediti di servizio AI + reward interni.

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

### 2.3 Egili (Crediti di Servizio Interni) — ⚠️ DA AGGIORNARE

**Service**

- [app/Services/EgiliService.php](app/Services/EgiliService.php)
    - `getBalance()`, `fromEur()`, `spend()`

**Stato attuale vs ToS v3.0.0**

- ⚠️ `fromEur()` e `spend()` ancora usati in `MintController` e `RebindController` per pagamento EGI in Egili → **DA RIMUOVERE** (ToS v3.0.0 Art. 5.2 creator / Art. 6.1 collector vietano l'uso di Egili per acquisto EGI)
- ✅ `getBalance()` — OK, serve per dashboard e consumo servizi AI
- Da implementare: consumo Egili per servizi AI (nuovo use case v3.0.0)

### 2.4 Acquisto Egili (FIAT / Crypto) — ⚠️ DA DISABILITARE

**Controller**

- [app/Http/Controllers/EgiliPurchaseController.php](app/Http/Controllers/EgiliPurchaseController.php)

**Stato attuale vs ToS v3.0.0**

- ⚠️ Controller ancora presente e attivo → **DA DISABILITARE/RIMUOVERE**
- ToS v3.0.0: Egili non acquistabili come asset autonomo
- Nuovo modello: Egili accreditati solo tramite (a) acquisto pacchetti AI in FIAT, (b) premiazione per merito

### 2.5 Crypto PSP (EGI) — Stato Attuale

**Gateway disponibile**

- [app/Services/Payment/CryptoPaymentGateway.php](app/Services/Payment/CryptoPaymentGateway.php)

**Nota**

- Non integrato in `MintController` / `RebindController`.

### 2.6 Payment Settings (UI/Accesso) — ✅ IMPLEMENTATO (2026-02-06)

**UI**

- [resources/views/settings/payments/index.blade.php](resources/views/settings/payments/index.blade.php)
    - ✅ Collector può accedere a payment settings

**Route**

- [routes/web.php](routes/web.php)
    - ✅ Accesso abilitato anche per collector

**Model Gate**

- [app/Models/User.php](app/Models/User.php) (linea 666)
    - ✅ `isSeller()` **include** `collector` per abilitare vendite secondario
    - Codice: `|| $this->usertype === 'collector'`

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

1. **Rimozione acquisto Egili** (normativa) — ⚠️ ToS v3.0.0 vieta acquisto Egili come asset, ma `EgiliPurchaseController` ancora presente nel codice. Da disabilitare/rimuovere.
2. **Rimozione pagamento EGI in Egili** — ⚠️ ToS v3.0.0 vieta uso Egili per acquisto EGI, ma `EgiliService.fromEur()/spend()` ancora usati in `MintController` e `RebindController`. Da rimuovere i riferimenti nei controller.
3. **Integrazione Crypto PSP (CASP)** per mint/rebind EGI (es. Coinbase Commerce) — ToS v3.0.0 già predisposti (Art. 5.2 creator, Art. 3.5 collector).
4. **Integrazione pagamento diretto in ALGO** — Creator può abilitare pagamento in ALGO per singolo EGI. Trasferimento wallet-to-wallet on-chain con transazioni atomiche per split. ToS v3.0.0 già predisposti.
5. **Rifattorizzazione fee piattaforma** secondo nuova logica (incasso su conto generale, split e trattenute interne).
6. ~~**Collector abilitati ai pagamenti**~~ — ✅ **IMPLEMENTATO 2026-02-06**: Collector ora può accedere a payment settings e vendere sul secondario.

---

## 7) Flusso logico completo — Collector (Secondario)

✅ **IMPLEMENTATO**: 2026-02-06

### 7.1 Obiettivo

Consentire ai **collector** di vendere sul mercato secondario (rebind), quindi **configurare PSP**, **ricevere pagamenti**, **gestire payout** e **tracciabilità**.

### 7.2 Stato attuale — ✅ IMPLEMENTATO

- **UI**: `settings/payments/index` ✅ **accessibile** anche a collector
- **Model**: `User::isSeller()` ✅ **include** `collector` (linea 666: `|| $this->usertype === 'collector'`)
- **Route**: ✅ Accesso abilitato per collector

### 7.3 Flusso implementato (end‑to‑end)

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

### 7.4 Implementazione completata — 2026-02-06

#### UI/Accesso — ✅ FATTO

- ✅ Rimosso blocco `@if (!$user->isSeller())` per `collector`
- ✅ Onboarding PSP in **settings/payments** abilitato per collector

#### Model/Policy — ✅ FATTO

- ✅ `User::isSeller()` include `collector` (User.php:666)
- ✅ `PaymentSettingsController` non blocca collector

#### Validazioni — ✅ FATTO

- ✅ Verifica PSP configurato prima di rendere vendibile rebind
- ✅ Messaggi chiari quando PSP non configurato

#### Rebind pipeline — ✅ FATTO

- ✅ `RebindController` usa PSP del seller collector
- ✅ Payout verso `users.stripe_account_id` del collector

### 7.5 Checklist operativa — ✅ COMPLETATA

- [x] Collector vede pagina pagamenti
- [x] Collector collega Stripe/PayPal
- [x] Collector mette in vendita un EGI
- [x] Buyer paga
- [x] Payout verso PSP collector
- [x] Ownership aggiornato
- [x] Audit trail presente
