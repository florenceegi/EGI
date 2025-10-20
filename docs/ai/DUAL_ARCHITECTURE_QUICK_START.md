# 🚀 EGI Dual Architecture - Quick Start Guide

> **Guida rapida per attivare e configurare la Dual Architecture EGI**  
> Versione: 1.0.0 | Data: 2025-10-19

---

## 📋 Indice

1. [Prerequisiti](#prerequisiti)
2. [Configurazione Feature Flags](#configurazione-feature-flags)
3. [Variabili d'Ambiente](#variabili-dambiente)
4. [Migrazione Database](#migrazione-database)
5. [Scheduler Oracle](#scheduler-oracle)
6. [Routes API Disponibili](#routes-api-disponibili)
7. [Componenti UI](#componenti-ui)
8. [Testing](#testing)
9. [Rollout Graduale](#rollout-graduale)

---

## 1️⃣ Prerequisiti

### Software Richiesto
- PHP >= 8.2
- Laravel >= 10.x
- MariaDB/MySQL >= 10.5
- Redis (per cache e queue)
- Node.js >= 18.x (per Vite build)

### Microservizi Esterni
- **AlgoKit Microservice** (per deploy SmartContract)
- **N.A.T.A.N AI Core** (per analisi EGI Living)

---

## 2️⃣ Configurazione Feature Flags

### File: `config/egi_living.php`

I feature flags controllano l'attivazione graduale delle funzionalità:

```php
'feature_flags' => [
    'pre_mint_enabled'           => env('FEATURE_PRE_MINT', false),
    'auto_mint_enabled'          => env('FEATURE_AUTO_MINT', false),
    'smart_contract_enabled'     => env('FEATURE_SMART_CONTRACT', false),
    'oracle_polling_enabled'     => env('FEATURE_ORACLE_POLLING', false),
    'living_subscriptions_enabled' => env('FEATURE_LIVING_SUBSCRIPTIONS', false),
],
```

### Attivazione Graduale (5 Fasi)

**FASE 1 - Pre-Mint System** (Settimana 1)
```bash
FEATURE_PRE_MINT=true
```

**FASE 2 - Auto-Mint Creators** (Settimana 2)
```bash
FEATURE_PRE_MINT=true
FEATURE_AUTO_MINT=true
```

**FASE 3 - SmartContract Living** (Settimana 3)
```bash
FEATURE_PRE_MINT=true
FEATURE_AUTO_MINT=true
FEATURE_SMART_CONTRACT=true
```

**FASE 4 - Oracle Polling** (Settimana 4)
```bash
FEATURE_PRE_MINT=true
FEATURE_AUTO_MINT=true
FEATURE_SMART_CONTRACT=true
FEATURE_ORACLE_POLLING=true
```

**FASE 5 - Subscriptions Premium** (Settimana 5)
```bash
FEATURE_PRE_MINT=true
FEATURE_AUTO_MINT=true
FEATURE_SMART_CONTRACT=true
FEATURE_ORACLE_POLLING=true
FEATURE_LIVING_SUBSCRIPTIONS=true
```

---

## 3️⃣ Variabili d'Ambiente

### File: `.env` (aggiungi queste variabili)

```bash
# ============================================================
# 🚀 EGI DUAL ARCHITECTURE - FEATURE FLAGS
# ============================================================

# Feature Flags (vedi sezione precedente)
FEATURE_PRE_MINT=false
FEATURE_AUTO_MINT=false
FEATURE_SMART_CONTRACT=false
FEATURE_ORACLE_POLLING=false
FEATURE_LIVING_SUBSCRIPTIONS=false

# Microservizio Algorand per SmartContract deploy
ALGORAND_SMARTCONTRACT_URL=http://localhost:3001

# Algorand API per interazioni SmartContract
ALGORAND_API_URL=https://testnet-algorand.api.purestake.io/ps2
ALGORAND_API_KEY=your_purestake_api_key

# Wallet per deploy SmartContract (Testnet/Mainnet)
# ⚠️ SECURITY: Non committare mai questa variabile
ALGORAND_DEPLOYER_MNEMONIC="word1 word2 ... word25"

# Intervallo polling Oracle (minuti) - default: 5
EGI_ORACLE_POLL_INTERVAL=5

# AI Core API per analisi EGI Living
NATAN_AI_CORE_URL=http://localhost:5000
NATAN_AI_CORE_API_KEY=your_ai_core_api_key
```

### ⚠️ Security Note

**NON committare mai:**
- `ALGORAND_DEPLOYER_MNEMONIC`
- `ALGORAND_API_KEY`
- `NATAN_AI_CORE_API_KEY`

Usare `.env.example` per template senza valori sensibili.

---

## 4️⃣ Migrazione Database

### Esegui Migrazioni

```bash
php artisan migrate
```

### Migrazioni Eseguite

1. `2025_10_19_200000_create_egi_living_subscriptions_table.php`
2. `2025_10_19_200001_create_egi_smart_contracts_table.php`
3. `2025_10_19_200002_add_smart_contract_support_to_egi_blockchain_table.php`
4. `2025_10_19_200003_add_dual_architecture_to_egis_table.php`

### Campi Aggiunti a `egis` Table

- `egi_type` (enum: 'ASA', 'SmartContract', 'PreMint')
- `pre_mint_mode` (boolean)
- `auto_mint_enabled` (boolean)
- `mint_availability` (string: 'ASA' o 'SmartContract')
- `egi_living_enabled` (boolean)
- `egi_living_activated_at` (timestamp)
- `egi_living_subscription_id` (FK)
- `smart_contract_app_id` (bigint)

---

## 5️⃣ Scheduler Oracle

### Registrazione Scheduler

Lo scheduler è già configurato in `routes/console.php`:

```php
Schedule::command('oracle:poll')
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->onOneServer()
    ->when(function () {
        return config('egi_living.feature_flags.oracle_polling_enabled', false);
    })
    ->appendOutputTo(storage_path('logs/oracle.log'));
```

### Avviare Scheduler

**Development:**
```bash
php artisan schedule:work
```

**Production (Cron):**
```bash
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

### Esecuzione Manuale

```bash
php artisan oracle:poll
```

### Monitoraggio Log

```bash
tail -f storage/logs/oracle.log
```

---

## 6️⃣ Routes API Disponibili

### Auto-Mint & Pre-Mint Actions

Tutte le route richiedono autenticazione e verifica creator:

```php
// Abilita Auto-Mint
POST /egi/{egi}/dual-arch/auto-mint/enable
Body: { "mint_type": "ASA" | "SmartContract" }

// Disabilita Auto-Mint
POST /egi/{egi}/dual-arch/auto-mint/disable

// Richiedi analisi AI (Pre-Mint)
POST /egi/{egi}/dual-arch/pre-mint/request-analysis

// Promuovi a On-Chain
POST /egi/{egi}/dual-arch/pre-mint/promote
Body: { "target_type": "ASA" | "SmartContract" }
```

### Route Names (Laravel)

```php
route('egi.dual-arch.auto-mint.enable', $egi)
route('egi.dual-arch.auto-mint.disable', $egi)
route('egi.dual-arch.pre-mint.request-analysis', $egi)
route('egi.dual-arch.pre-mint.promote', $egi)
```

---

## 7️⃣ Componenti UI

### Blade Components Disponibili

```blade
{{-- Badge tipo EGI --}}
<x-egi-type-badge :egi="$egi" />

{{-- Panel Auto-Mint (Creators) --}}
<x-egi-auto-mint-panel :egi="$egi" />

{{-- Panel Pre-Mint (Testing/Promozione) --}}
<x-egi-pre-mint-panel :egi="$egi" />

{{-- Panel EGI Living (SmartContract Dashboard) --}}
<x-egi-living-panel :egi="$egi" />
```

### Integrazione in `egis.show`

I componenti sono già integrati in `resources/views/egis/partials/sidebar/crud-panel.blade.php`:

```blade
@if (config('egi_living.feature_flags.pre_mint_enabled', true) && $egiType === 'PreMint' && $isCreatorCheck)
    <x-egi-auto-mint-panel :egi="$egi" class="mb-4" />
@endif

@if ($egiType === 'SmartContract' && $egi->smartContract)
    <x-egi-living-panel :egi="$egi" :smart-contract="$egi->smartContract" class="mb-4" />
@endif

@if ($egiType === 'PreMint')
    <x-egi-pre-mint-panel :egi="$egi" class="mb-4" />
@endif
```

---

## 8️⃣ Testing

### 1. Crea un EGI Pre-Mint

```bash
php artisan tinker
```

```php
$egi = App\Models\Egi::find(71);
$egi->update(['egi_type' => 'PreMint']);
```

### 2. Abilita Feature Flags (Dev)

```bash
# .env
FEATURE_PRE_MINT=true
FEATURE_AUTO_MINT=true
APP_DEBUG=true
```

```bash
php artisan config:clear
php artisan cache:clear
```

### 3. Verifica UI

1. Naviga su `/egis/{id}` come creator
2. Verifica visibilità pannelli:
   - **Auto-Mint Panel**: per abilitare/disabilitare auto-mint
   - **Pre-Mint Panel**: per richiedere analisi AI e promuovere
3. Controlla debug panel (solo se `APP_DEBUG=true`)

### 4. Test API

```bash
# Abilita Auto-Mint
curl -X POST http://localhost:8010/egi/71/dual-arch/auto-mint/enable \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: {token}" \
  -d '{"mint_type": "ASA"}'
```

---

## 9️⃣ Rollout Graduale

### Piano di Rollout (5 Settimane)

#### **Settimana 1: Pre-Mint System** 🧪
- ✅ Attiva `FEATURE_PRE_MINT=true`
- 📊 Obiettivo: 100% nuovi EGI in stato Pre-Mint
- 🔍 Monitoraggio: DB stats, error logs
- 🚫 Rischio: Basso (solo DB, no blockchain)

#### **Settimana 2: Auto-Mint Creators** 🎯
- ✅ Attiva `FEATURE_AUTO_MINT=true`
- 📊 Obiettivo: 50% creators usano auto-mint
- 🔍 Monitoraggio: Creator adoption, UI feedback
- 🚫 Rischio: Medio (introduce minting workflow)

#### **Settimana 3: SmartContract Living** 🔗
- ✅ Attiva `FEATURE_SMART_CONTRACT=true`
- 📊 Obiettivo: 10-20 EGI Living pilota
- 🔍 Monitoraggio: Deploy success rate, gas costs
- 🚫 Rischio: Alto (blockchain deploy, testnet first)

#### **Settimana 4: Oracle Polling** 🤖
- ✅ Attiva `FEATURE_ORACLE_POLLING=true`
- 📊 Obiettivo: 5 cicli AI analysis completi
- 🔍 Monitoraggio: Oracle logs, AI API latency
- 🚫 Rischio: Medio (AI integration, scheduler)

#### **Settimana 5: Premium Subscriptions** 💎
- ✅ Attiva `FEATURE_LIVING_SUBSCRIPTIONS=true`
- 📊 Obiettivo: 5-10 subscriptions attive
- 🔍 Monitoraggio: Payment flow, churn rate
- 🚫 Rischio: Alto (monetization, GDPR compliance)

### Rollback Strategy

Per ogni fase, in caso di problemi critici:

```bash
# Disabilita feature flag problematico
FEATURE_XXX=false

# Clear cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Rollback migration (solo se necessario)
php artisan migrate:rollback --step=1
```

---

## 📚 Documentazione Completa

- **Overview Architettura**: `docs/ai/mint/egi_dual_architecture_overview.md`
- **API Reference**: `docs/ai/dual-architecture-api-reference.md`
- **Deployment Guide**: `docs/ai/dual-architecture-deployment-guide.md`
- **UI Integration**: `docs/ai/dual-architecture-ui-integration.md`
- **Implementation Summary**: `docs/ai/DUAL_ARCHITECTURE_IMPLEMENTATION_COMPLETE.md`

---

## 🆘 Troubleshooting

### ❌ Pannelli UI non visibili

**Causa**: Feature flags disabilitati o tipo EGI errato

**Soluzione**:
```bash
# Verifica .env
grep FEATURE_ .env

# Verifica tipo EGI
php artisan tinker
>>> App\Models\Egi::find(71)->egi_type
```

### ❌ Oracle non parte

**Causa**: Scheduler non attivo o feature flag disabilitato

**Soluzione**:
```bash
# Verifica feature flag
php artisan tinker
>>> config('egi_living.feature_flags.oracle_polling_enabled')

# Avvia scheduler
php artisan schedule:work
```

### ❌ SmartContract deploy fallisce

**Causa**: Credenziali Algorand mancanti o microservizio offline

**Soluzione**:
```bash
# Verifica microservizio
curl http://localhost:3001/health

# Verifica .env
echo $ALGORAND_API_KEY
echo $ALGORAND_DEPLOYER_MNEMONIC
```

---

## ✅ Checklist Pre-Production

- [ ] Tutte le migrazioni eseguite senza errori
- [ ] Feature flags configurati correttamente
- [ ] Variabili d'ambiente sensibili in vault (non `.env`)
- [ ] Scheduler cron configurato su server
- [ ] Microservizi Algorand + AI operativi
- [ ] Log monitoring configurato (Oracle, SmartContract)
- [ ] Backup database pre-rollout
- [ ] Piano di rollback documentato
- [ ] Team notificato delle nuove funzionalità
- [ ] Testing su staging completato

---

**Versione**: 1.0.0  
**Ultimo Aggiornamento**: 2025-10-19  
**Maintainer**: DevTeam FlorenceEGI

