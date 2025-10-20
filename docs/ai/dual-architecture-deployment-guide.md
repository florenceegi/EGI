# EGI Dual Architecture - Deployment Guide

**Version:** 1.0  
**Date:** 2025-10-19  
**Author:** Padmin D. Curtis (AI Partner OS3.0)

---

## 📋 Indice

1. [Prerequisiti](#prerequisiti)
2. [Deployment Steps](#deployment-steps)
3. [Configurazione Feature Flags](#configurazione-feature-flags)
4. [Testing Checklist](#testing-checklist)
5. [Rollout Plan](#rollout-plan)
6. [Troubleshooting](#troubleshooting)

---

## 🔧 Prerequisiti

### Database

```bash
# Run migrations
php artisan migrate

# Verifica tabelle create
php artisan tinker
>>> \DB::table('egis')->count()
>>> \DB::table('egi_smart_contracts')->exists()
>>> \DB::table('egi_living_subscriptions')->exists()
```

### Python Environment (SmartContracts)

```bash
cd algorand-smartcontracts
python3 -m venv venv
source venv/bin/activate
pip install -r requirements.txt

# Test compilation
python egi_living_v1.py
```

### Environment Variables

Aggiungere a `.env`:

```env
# Feature Flags (disabilita per default)
FEATURE_SC_MINT=false
FEATURE_PRE_MINT=false
FEATURE_AI_CURATOR=false
FEATURE_ORACLE=false

# Oracle Configuration
ORACLE_WALLET_ADDRESS=your_oracle_wallet_address
ORACLE_PRIVATE_KEY=your_oracle_private_key

# Algorand Network
ALGORAND_NETWORK=testnet
```

---

## 🚀 Deployment Steps

### Step 1: Database Migration

```bash
# Backup database first!
php artisan backup:db

# Run migrations
php artisan migrate

# Verify
php artisan migrate:status
```

### Step 2: Clear Caches

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

### Step 3: Update Existing EGIs

```bash
php artisan tinker

# Set all existing EGIs to ASA type (default)
>>> \App\Models\Egi::whereNull('egi_type')->update(['egi_type' => 'ASA']);
```

### Step 4: Register Console Commands

Verificare che `OraclePollCommand` sia in `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    // Oracle polling (se abilitato)
    if (config('egi_living.feature_flags.oracle_enabled')) {
        $schedule->command('oracle:poll')
            ->everyMinute()
            ->withoutOverlapping();
    }
}
```

### Step 5: Test SmartContract Deployment (Testnet)

```bash
cd algorand-smartcontracts
python deploy_helper.py

# Test deploy con wallet di test
# Segui istruzioni output
```

---

## 🎛️ Configurazione Feature Flags

### Fase 1: Solo ASA (Production Safe)

```env
FEATURE_SC_MINT=false
FEATURE_PRE_MINT=false
FEATURE_AI_CURATOR=false
FEATURE_ORACLE=false
```

**Risultato:** Sistema funziona come prima, zero impatto utenti.

---

### Fase 2: Abilita Pre-Mint (Testing Interno)

```env
FEATURE_PRE_MINT=true  # ← ABILITATO
FEATURE_SC_MINT=false
FEATURE_AI_CURATOR=false
FEATURE_ORACLE=false
```

**Test:**

-   Crea EGI → verifica sia PreMint di default
-   Richiedi analisi AI → verifica mock funziona
-   Promuovi a ASA → verifica mint corretto

---

### Fase 3: SmartContract Beta (Beta Testers)

```env
FEATURE_PRE_MINT=true
FEATURE_SC_MINT=true  # ← ABILITATO
FEATURE_AI_CURATOR=false
FEATURE_ORACLE=false
```

**Test:**

-   Deploy SmartContract su testnet
-   Verifica creazione record `egi_smart_contracts`
-   Check Algorand Explorer per App ID

---

### Fase 4: Full AI + Oracle (Production)

```env
FEATURE_PRE_MINT=true
FEATURE_SC_MINT=true
FEATURE_AI_CURATOR=true  # ← ABILITATO
FEATURE_ORACLE=true      # ← ABILITATO
```

**Setup Oracle:**

```bash
# Registra comando scheduler
php artisan schedule:work

# Verifica polling
php artisan oracle:poll --force

# Monitor logs
tail -f storage/logs/laravel.log | grep ORACLE
```

---

## ✅ Testing Checklist

### Database

-   [ ] Tutte le migrations eseguite senza errori
-   [ ] Tabelle `egi_smart_contracts` e `egi_living_subscriptions` create
-   [ ] Foreign keys funzionanti
-   [ ] Rollback migrations OK

### Backend Services

-   [ ] `EgiMintingOrchestrator` routing corretto ASA/SmartContract
-   [ ] `PreMintEgiService` creazione EGI PreMint
-   [ ] `EgiSmartContractService` deploy (con mock OK)
-   [ ] `EgiOracleService` polling funzionante
-   [ ] `EgiLivingSubscriptionService` subscription flow

### Models

-   [ ] `Egi` relations `smartContract()` e `livingSubscription()` OK
-   [ ] `EgiSmartContract` model accessibile
-   [ ] `EgiLivingSubscription` model accessibile
-   [ ] Enum `EgiType`, `EgiLivingStatus`, `SmartContractStatus` OK

### UI Components

-   [ ] `x-egi-type-badge` rendering corretto
-   [ ] `x-egi-living-panel` solo per SmartContract
-   [ ] `x-egi-pre-mint-panel` solo per PreMint
-   [ ] `x-egi-auto-mint-panel` solo per creator
-   [ ] Brand Guidelines rispettate (colori, font, spacing)

### Error Handling (UEM)

-   [ ] Tutti gli 8 codici UEM mappati in `config/error-manager.php`
-   [ ] Traduzioni IT complete in `errors_2.php`
-   [ ] Test trigger errore → verifica messaggio user corretto
-   [ ] Notifiche Slack/Email per errori critici

---

## 📅 Rollout Plan

### Week 1: Internal Testing

-   **Goal:** Validare backend senza impatto utenti
-   **Actions:**
    -   Deploy con tutti i flags OFF
    -   Test manuale funzioni ASA esistenti
    -   Zero regressioni
-   **Success:** Sistema stabile, nessun bug ASA

### Week 2: Pre-Mint Testing

-   **Goal:** Testare creazione EGI virtuali
-   **Actions:**
    -   Abilita `FEATURE_PRE_MINT=true`
    -   Team interno crea EGI Pre-Mint
    -   Test promozione → ASA
-   **Success:** Pre-Mint → ASA funziona, UI OK

### Week 3: SmartContract Beta

-   **Goal:** Deploy SC su testnet con beta users
-   **Actions:**
    -   Abilita `FEATURE_SC_MINT=true`
    -   5-10 beta tester selezionati
    -   Deploy SC reali su Algorand testnet
    -   Monitor Algorand Explorer
-   **Success:** SC deployati, visibili on-chain, UI corretta

### Week 4: Oracle + AI Integration

-   **Goal:** Attivare analisi AI automatiche
-   **Actions:**
    -   Integra N.A.T.A.N API reale
    -   Abilita `FEATURE_ORACLE=true`
    -   Monitor trigger automatici
    -   Verifica update_state on-chain
-   **Success:** AI triggers funzionano, state sync OK

### Week 5: Production Launch

-   **Goal:** Lancio pubblico EGI Viventi
-   **Actions:**
    -   Comunicazione marketing
    -   Pricing plans attivi
    -   PSP integration (Stripe/PayPal)
    -   Monitor subscriptions
-   **Success:** Primi utenti paganti, zero downtime

---

## 🔥 Troubleshooting

### Migration Fails

```bash
# Rollback
php artisan migrate:rollback --step=1

# Fix issue
# Re-run
php artisan migrate
```

### SmartContract Deploy Error

```bash
# Check Python environment
python --version  # >= 3.8
pip list | grep pyteal

# Check Algorand node
curl https://testnet-api.algonode.cloud/v2/status

# Check Oracle wallet funded
# Testnet dispenser: https://testnet.algoexplorer.io/dispenser
```

### Oracle Not Triggering

```bash
# Check scheduler running
php artisan schedule:list

# Manual trigger
php artisan oracle:poll --force -v

# Check logs
tail -f storage/logs/laravel.log | grep ORACLE
```

### UI Components Not Showing

```bash
# Clear views
php artisan view:clear

# Check Blade syntax
php artisan view:cache

# Verify component paths
ls -la resources/views/components/egi-*.blade.php
```

### UEM Errors Not Showing

```bash
# Check config cached
php artisan config:cache

# Verify translations
php artisan tinker
>>> __('error-manager::errors_2.user.smart_contract_deploy_failed')

# Test error manually
>>> app(ErrorManagerInterface::class)->handle('SMART_CONTRACT_DEPLOY_FAILED', [], new \Exception('test'));
```

---

## 📞 Support

Per problemi contattare:

-   **Backend:** Fabio Cherici / Padmin D. Curtis
-   **Blockchain:** AlgoKit Team
-   **AI/N.A.T.A.N:** AI Integration Team

---

## 📚 Riferimenti

-   [EGI Dual Architecture Overview](./mint/egi_dual_architecture_overview.md)
-   [UI Integration Guide](./dual-architecture-ui-integration.md)
-   [Brand Guidelines](./marketing/FlorenceEGI%20Brand%20Guidelines.md)
-   [Algorand Docs](https://developer.algorand.org/)
-   [PyTeal Docs](https://pyteal.readthedocs.io/)

---

**FlorenceEGI - Dove l'arte diventa valore virtuoso**

