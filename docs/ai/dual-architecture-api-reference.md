# EGI Dual Architecture - API Reference

**Version:** 1.0  
**Date:** 2025-10-19  
**Author:** Padmin D. Curtis (AI Partner OS3.0)

---

## 📋 Service Methods Reference

---

## 🏭 EgiMintingOrchestrator

**Location:** `app/Services/EgiMintingOrchestrator.php`

### `mint(Egi $egi, User $user, array $options = [])`

Minta EGI basandosi sul tipo (ASA o SmartContract). Factory pattern.

**Parameters:**

-   `$egi` - Istanza Egi model
-   `$user` - User che esegue il mint
-   `$options` - Array opzionale con:
    -   `trigger_interval` (int) - Per SmartContract
    -   `metadata_hash` (string) - Hash IPFS custom
    -   `metadata` (array) - Metadati aggiuntivi

**Returns:** `EgiBlockchain|EgiSmartContract`

**Throws:** `\Exception`

**Example:**

```php
$orchestrator = app(EgiMintingOrchestrator::class);
$result = $orchestrator->mint($egi, $user, [
    'trigger_interval' => 86400 // 24h
]);
```

**GDPR:** ✅ Audit trail completo  
**UEM:** `EGI_MINTING_ORCHESTRATOR_FAILED`

---

## 🔧 EgiSmartContractService

**Location:** `app/Services/EgiSmartContractService.php`

### `deploySmartContract(Egi $egi, User $user, array $options = [])`

Deploy SmartContract EGI Vivente su Algorand.

**Parameters:**

-   `$egi` - EGI da rendere "Living"
-   `$user` - Creator/Owner
-   `$options`:
    -   `trigger_interval` (int) - Secondi tra AI triggers
    -   `metadata_hash` (string) - IPFS hash iniziale

**Returns:** `EgiSmartContract`

**Throws:** `\Exception`

**Example:**

```php
$service = app(EgiSmartContractService::class);
$sc = $service->deploySmartContract($egi, $user, [
    'trigger_interval' => 3600 // 1h per premium
]);

echo "Deployed: {$sc->app_id}";
```

**Feature Flag:** `FEATURE_SC_MINT`  
**GDPR:** ✅ `blockchain_activity`  
**UEM:** `SMART_CONTRACT_DEPLOY_FAILED`

---

## 🤖 PreMintEgiService

**Location:** `app/Services/PreMintEgiService.php`

### `createPreMintEgi(array $data, User $user)`

Crea EGI virtuale in modalità Pre-Mint.

**Parameters:**

-   `$data` - Dati EGI (title, description, collection_id, etc.)
-   `$user` - Creator

**Returns:** `Egi` (con `egi_type = 'PreMint'`)

**Example:**

```php
$service = app(PreMintEgiService::class);
$egi = $service->createPreMintEgi([
    'title' => 'My Artwork',
    'description' => 'Amazing piece',
    'collection_id' => 1
], $user);
```

**Feature Flag:** `FEATURE_PRE_MINT`  
**UEM:** `PRE_MINT_CREATE_FAILED`

---

### `promoteToBlockchain(Egi $egi, EgiType $targetType, User $user)`

Promuove Pre-Mint EGI a ASA o SmartContract.

**Parameters:**

-   `$egi` - EGI Pre-Mint
-   `$targetType` - `EgiType::ASA` o `EgiType::SMART_CONTRACT`
-   `$user` - User che esegue promozione

**Returns:** `Egi` (aggiornato)

**Example:**

```php
use App\Enums\EgiType;

$service = app(PreMintEgiService::class);
$egi = $service->promoteToBlockchain($egi, EgiType::SMART_CONTRACT, $user);
```

**UEM:** `PRE_MINT_PROMOTE_FAILED`

---

### `requestAIAnalysis(Egi $egi, string $analysisType)`

Richiede analisi AI N.A.T.A.N per Pre-Mint EGI.

**Parameters:**

-   `$egi` - Pre-Mint EGI
-   `$analysisType` - `'description'` | `'traits'` | `'promotion'`

**Returns:** `array` - Risultato analisi AI

**Example:**

```php
$result = $service->requestAIAnalysis($egi, 'description');
// ['description' => '...', 'keywords' => [...], 'confidence' => 0.85]
```

**Feature Flag:** `FEATURE_AI_CURATOR`

---

## 🔮 EgiOracleService

**Location:** `app/Services/EgiOracleService.php`

### `pollForTriggers()`

Polling automatico SmartContracts pronti per AI trigger.

**Returns:** `array` - Status e risultati

**Example (Console Command):**

```bash
php artisan oracle:poll
```

**Feature Flag:** `FEATURE_ORACLE`  
**UEM:** `ORACLE_POLL_FAILED`

---

### `processTrigger(EgiSmartContract $smartContract)`

Processa singolo trigger AI per SmartContract.

**Returns:** `array`

**Example:**

```php
$oracle = app(EgiOracleService::class);
$result = $oracle->processTrigger($smartContract);
// ['success' => true, 'ai_result' => [...], 'blockchain_update' => [...]]
```

---

## 💰 EgiLivingSubscriptionService

**Location:** `app/Services/EgiLivingSubscriptionService.php`

### `createSubscription(Egi $egi, User $user, string $planType, string $paymentMethod)`

Crea abbonamento EGI Vivente (pending payment).

**Parameters:**

-   `$egi` - EGI da attivare
-   `$user` - Acquirente
-   `$planType` - `'one_time'` | `'monthly'` | `'yearly'` | `'lifetime'`
-   `$paymentMethod` - `'stripe'` | `'paypal'` | `'bank_transfer'`

**Returns:** `EgiLivingSubscription`

**Example:**

```php
$service = app(EgiLivingSubscriptionService::class);
$subscription = $service->createSubscription($egi, $user, 'one_time', 'stripe');
// Status: pending_payment
```

**UEM:** `LIVING_SUBSCRIPTION_CREATE_FAILED`

---

### `completePayment(EgiLivingSubscription $subscription, string $paymentReference)`

Completa pagamento e attiva subscription (chiamato da PSP webhook).

**Parameters:**

-   `$subscription` - Subscription pending
-   `$paymentReference` - TX ID da PSP

**Returns:** `EgiLivingSubscription` (attivato)

**Example:**

```php
$subscription = $service->completePayment($subscription, 'stripe_tx_abc123');
// Status: active
// EGI->egi_living_enabled: true
```

**UEM:** `LIVING_SUBSCRIPTION_PAYMENT_FAILED`

---

### `cancelSubscription(EgiLivingSubscription $subscription, ?string $reason, User $user)`

Cancella abbonamento attivo.

**Example:**

```php
$service->cancelSubscription($subscription, 'Non serve più', $user);
// Status: cancelled
// EGI->egi_living_enabled: false
```

**UEM:** `LIVING_SUBSCRIPTION_CANCEL_FAILED`

---

### `expireSubscriptions()`

Job automatico per expirare abbonamenti scaduti.

**Returns:** `int` - Numero subscription expirate

**Example (Scheduled):**

```php
// In Kernel.php
$schedule->call(function() {
    app(EgiLivingSubscriptionService::class)->expireSubscriptions();
})->daily();
```

---

## 📊 Model Methods

### Egi Model

**New Relations:**

```php
$egi->smartContract()       // HasOne EgiSmartContract
$egi->livingSubscription()  // HasOne EgiLivingSubscription (active)
$egi->livingSubscriptions() // HasMany EgiLivingSubscription (all)
```

**New Properties:**

```php
$egi->egi_type              // 'ASA' | 'SmartContract' | 'PreMint'
$egi->pre_mint_mode         // bool
$egi->egi_living_enabled    // bool
$egi->smart_contract_app_id // string (Algorand App ID)
```

---

### EgiSmartContract Model

**Scopes:**

```php
EgiSmartContract::readyForTrigger()->get()  // SC pronti per AI
EgiSmartContract::active()->get()           // SC attivi
```

**Methods:**

```php
$sc->isActive()                 // bool
$sc->isTriggerReady()           // bool
$sc->getExplorerUrl()           // string (Algorand Explorer)
$sc->getAISuccessRate()         // float (0-100)
```

---

### EgiLivingSubscription Model

**Scopes:**

```php
EgiLivingSubscription::active()->get()              // Subscription attive
EgiLivingSubscription::expired()->get()             // Scadute
EgiLivingSubscription::expiringSoon(7)->get()       // In scadenza entro 7 giorni
```

**Methods:**

```php
$sub->isActive()            // bool
$sub->isExpired()           // bool
$sub->isLifetime()          // bool
$sub->hasFeature('curator') // bool
$sub->getDaysRemaining()    // int|null
$sub->activate()            // void
$sub->cancel(?string $reason) // void
```

---

## 🎨 Enum Reference

### EgiType

```php
use App\Enums\EgiType;

EgiType::ASA->value              // 'ASA'
EgiType::SMART_CONTRACT->value   // 'SmartContract'
EgiType::PRE_MINT->value         // 'PreMint'

$type->label()           // 'EGI Classico (ASA)'
$type->description()     // Descrizione completa
$type->requiresBlockchain() // bool
$type->supportsAI()      // bool
$type->isPremium()       // bool
$type->badgeClass()      // Tailwind classes
```

### EgiLivingStatus

```php
use App\Enums\EgiLivingStatus;

EgiLivingStatus::PENDING_PAYMENT
EgiLivingStatus::ACTIVE
EgiLivingStatus::SUSPENDED
EgiLivingStatus::CANCELLED
EgiLivingStatus::EXPIRED

$status->aiEnabled()    // bool
$status->canRenew()     // bool
$status->canCancel()    // bool
```

### SmartContractStatus

```php
use App\Enums\SmartContractStatus;

SmartContractStatus::DEPLOYING
SmartContractStatus::ACTIVE
SmartContractStatus::PAUSED
SmartContractStatus::TERMINATED

$status->allowsAITriggers()  // bool
$status->canPause()          // bool
$status->canResume()         // bool
```

---

## 🔐 Security & GDPR

Tutti i metodi implementano:

-   ✅ **UltraLogManager** per logging
-   ✅ **ErrorManager** per error handling
-   ✅ **AuditLogService** per GDPR trail
-   ✅ **DB Transactions** per atomicità
-   ✅ **ConsentService** check dove necessario

---

## 📞 Error Codes Reference

| Code                               | Type     | Blocking | Notify      |
| ---------------------------------- | -------- | -------- | ----------- |
| SMART_CONTRACT_DEPLOY_FAILED       | error    | semi     | email+slack |
| EGI_MINTING_ORCHESTRATOR_FAILED    | error    | blocking | email+slack |
| PRE_MINT_CREATE_FAILED             | error    | semi     | none        |
| PRE_MINT_PROMOTE_FAILED            | error    | semi     | none        |
| ORACLE_POLL_FAILED                 | critical | not      | email+slack |
| LIVING_SUBSCRIPTION_CREATE_FAILED  | error    | semi     | none        |
| LIVING_SUBSCRIPTION_PAYMENT_FAILED | error    | semi     | email+slack |
| LIVING_SUBSCRIPTION_CANCEL_FAILED  | error    | not      | none        |

Tutti mappati in `config/error-manager.php` con traduzioni IT.

---

**FlorenceEGI - Dove l'arte diventa valore virtuoso**
