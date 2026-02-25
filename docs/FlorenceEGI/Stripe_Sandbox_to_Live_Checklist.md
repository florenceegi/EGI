# Checklist: Stripe Sandbox → Live

**Data**: 2026-02-23

## Stato attuale

- **SDK**: Stripe PHP SDK diretto (`Stripe\StripeClient`), NO Laravel Cashier
- **Modalità**: Sandbox (`STRIPE_MODE=sandbox`)
- **Config**: `config/algorand.php` → sezione `payments.stripe`
- **Account piattaforma**: `STRYPE_ACCOUNT_ID_NATAN` = `acct_1SUkWtP4QQSK0FDv`

## Pre-requisiti

- [ ] Conto corrente aziendale aperto
- [ ] Account Stripe verificato con dati aziendali (IBAN, documenti)
- [ ] Stripe Dashboard → verifica identità completata

---

## 1. Ottenere le chiavi Live da Stripe Dashboard

1. Vai su [dashboard.stripe.com](https://dashboard.stripe.com)
2. Toggle "Test mode" → **OFF** (passa a Live)
3. Developers → API keys:
   - Copia **Publishable key**: `pk_live_...`
   - Copia **Secret key**: `sk_live_...`

## 2. Configurare webhook Live

1. Stripe Dashboard → Developers → Webhooks → **Add endpoint**
2. URL: `https://art.florenceegi.com/api/webhooks/stripe`
3. Eventi da selezionare (minimo):
   - `payment_intent.succeeded`
4. Copia **Signing secret**: `whsec_...`

> **Nota**: esistono anche route fallback (`/stripe/webhook`) ma la primaria è `/api/webhooks/stripe`

## 3. Modificare `.env` di produzione

Cambiare **solo 4 variabili** (le altre restano invariate):

```env
# --- STRIPE: LIVE ---
STRIPE_MODE=live
STRIPE_PUBLISHABLE_KEY=pk_live_XXXXXXXXXXXXXXXX
STRIPE_SECRET_KEY=sk_live_XXXXXXXXXXXXXXXX
STRIPE_WEBHOOK_SECRET=whsec_XXXXXXXXXXXXXXXX
```

**NON cambia**:
- `STRYPE_ACCOUNT_ID_NATAN` — stesso account in sandbox e live
- `STRIPE_ENABLED=true` — già impostato
- `EGI_MOCK_PAYMENTS=false` — già impostato
- `STRIPE_AUTO_CONFIRM=false` — già impostato

## 4. Applicare la configurazione sul server

```bash
sudo -u forge bash -c "cd /home/forge/art.florenceegi.com && php artisan config:cache && php artisan cache:clear"
```

## 5. Re-onboarding utenti Stripe Connect

Gli account Express creati in sandbox **non esistono** in Live. Eseguire:

```sql
-- Reset stripe_account_id per tutti gli utenti (sandbox accounts non validi in live)
UPDATE users SET stripe_account_id = NULL WHERE stripe_account_id LIKE 'acct_%' AND usertype IN ('creator', 'collector');

-- Reset anche nelle wallets (legacy)
UPDATE wallets SET stripe_account_id = NULL WHERE stripe_account_id LIKE 'acct_%';

-- Reset destinazioni wallet
UPDATE wallet_destinations SET destination_value = NULL WHERE payment_type = 'stripe';
```

Al prossimo accesso, l'onboarding checklist mostrerà "Configura Stripe" come task prioritario per creator e collector.

## 6. Verificare il funzionamento

- [ ] Webhook riceve eventi → Stripe Dashboard → Webhooks → verifica delivery
- [ ] Polling command attivo come safety net: `stripe:process-events` (schedulato ogni minuto)
- [ ] Test con pagamento reale a importo minimo (mint di test)
- [ ] Verificare split payment → controllare record `PaymentDistribution`

---

## Switch back a Sandbox (per Staging)

Quando verrà creato l'ambiente staging, nel `.env` dello staging rimettere:

```env
# --- STRIPE: SANDBOX ---
STRIPE_MODE=sandbox
STRIPE_PUBLISHABLE_KEY=pk_test_51ST4iEP4QQlp6ZtMLlI3XvBfzTjOSa9mlZmrfPL96xRdQqeIJGLEdtYvCXrmUXOdRWztqY7O79pksnhWGSMbcEp000fo8IU4Aw
STRIPE_SECRET_KEY=sk_test_51ST4iEP4QQlp6ZtMRW0hKRkFmFuaRYe56LLEqsMiYYzywqp82ue9Q37BFlKgMRUVJERJybYtsajJMNLpG05URhh700NWOzKfLC
STRIPE_WEBHOOK_SECRET=whsec_JWGUUFwBq1WT19scp0u5oQxmhswSzRM8
STRIPE_AUTO_CONFIRM=false
STRIPE_SANDBOX_PAYMENT_METHOD=pm_card_visa
```

---

## Architettura pagamenti (riferimento)

| Componente | File |
|---|---|
| Config Stripe | `config/algorand.php` (sezione `payments.stripe`) |
| Config Services | `config/services.php` (sezione `stripe`) |
| Payment Service | `app/Services/Payment/StripeRealPaymentService.php` |
| Split Service | `app/Services/Payment/StripePaymentSplitService.php` |
| Connect Service | `app/Services/Payment/StripeConnectService.php` |
| Webhook Controller | `app/Http/Controllers/Payment/PspWebhookController.php` |
| Event Polling | `app/Console/Commands/ProcessStripeEventsCommand.php` |
| Factory PSP | `app/Services/Payment/PaymentServiceFactory.php` |
| Mappa completa | `docs/FlorenceEGI/Payment_System_Map.md` |

## Note sicurezza

- Le chiavi Live **non devono mai** essere committate nel repository
- Il `.env` è già nel `.gitignore`
- Il codice PHP non richiede modifiche — tutto è config-driven via `.env`
