# 📘 Documentazione Flusso Pagamento Mint con Egili

## 🎯 Overview

Questo documento descrive il flusso completo di pagamento del mint di un EGI utilizzando i token Egili (valuta interna della piattaforma).

---

## 🔑 Concetti Chiave

### Egili Token

-   **Tipo**: Token utility NON criptovaluta (MiCA-safe)
-   **Natura**: Account-bound (non trasferibili tra utenti)
-   **Tasso di conversione**: 1 Egilo = €0.01 (prezzo di acquisto)
-   **Utilizzo**: Riduzione fee, servizi, abbonamenti, pagamento mint EGI

### Egili nel Contesto Mint

-   **Abilitazione**: Il Creator decide per ogni EGI se accettare pagamenti in Egili
-   **Campo DB**: `egis.payment_by_egili` (boolean, default: false)
-   **Configurazione**: Tramite CRUD panel nell'edit dell'EGI
-   **Validazione**: Il buyer deve avere saldo Egili sufficiente

---

## 📊 Architettura del Sistema

### 1. Database Schema

#### Tabella: `egis`

```sql
payment_by_egili BOOLEAN DEFAULT false
    COMMENT 'Indica se questo EGI accetta Egili come metodo di pagamento'
```

#### Tabella: `wallets`

```sql
egili_balance INT DEFAULT 0
    COMMENT 'Saldo Egili corrente dell utente'
egili_lifetime_earned INT DEFAULT 0
    COMMENT 'Totale Egili guadagnati lifetime'
egili_lifetime_spent INT DEFAULT 0
    COMMENT 'Totale Egili spesi lifetime'
```

#### Tabella: `egili_transactions`

```sql
id BIGINT PRIMARY KEY
wallet_id BIGINT -- FK to wallets
user_id BIGINT -- FK to users
transaction_type ENUM('earned', 'spent', 'bonus', 'refund')
operation ENUM('add', 'subtract')
amount INT -- Quantità Egili
balance_before INT
balance_after INT
source_type VARCHAR(255) -- Polymorphic (App\Models\Egi, etc.)
source_id BIGINT -- Polymorphic ID
reason VARCHAR(255) -- Codice macchina (es: 'egi_direct_mint')
category VARCHAR(50) -- Categoria reporting (mint, trading, etc.)
metadata JSON -- Contesto aggiuntivo
status ENUM('pending', 'completed', 'failed', 'refunded')
created_at TIMESTAMP
```

#### Tabella: `egi_blockchain`

```sql
payment_provider VARCHAR(50) -- 'stripe', 'paypal', 'egili_internal'
payment_reference VARCHAR(255) -- Per egili: 'EGL-XXXXXXXXXXXX'
paid_currency VARCHAR(10) -- 'EUR' o 'EGL'
paid_amount_recorded DECIMAL(10,2) -- Per egili: quantità Egili spesi
```

---

## 🔄 Flusso Completo del Pagamento

### FASE 1: Configurazione EGI (Creator)

**Route**: `GET /egis/{id}` (EGI Show Page)  
**View**: `resources/views/egis/show.blade.php`  
**Panel**: `resources/views/egis/partials/sidebar/crud-panel.blade.php`

#### 1.1 Abilitazione Pagamenti Egili

```php
// Nel form di edit EGI (crud-panel.blade.php, line ~318)
<input type="checkbox"
    id="payment_by_egili"
    name="payment_by_egili"
    value="1"
    {{ $egi->payment_by_egili ? 'checked' : '' }}>

// Translation key
__('egi.crud.payment_by_egili') // "Abilita pagamento con Egili"
__('egi.crud.payment_by_egili_hint') // Descrizione funzionalità
```

#### 1.2 Salvataggio Configurazione

**Controller**: `App\Http\Controllers\EgiController@update`

```php
// Validazione (line ~448)
'payment_by_egili' => 'sometimes|boolean',

// Il campo viene salvato automaticamente nel model Egi
```

**Migrazione**: `database/migrations/2025_11_13_120000_add_payment_by_egili_to_egis_table.php`

---

### FASE 2: Visualizzazione Opzione Pagamento (Buyer)

**Route**: `GET /mint/payment/{egiId}?reservation_id={optional}`  
**Controller**: `App\Http\Controllers\MintController@showPaymentForm`  
**View**: `resources/views/mint/payment-form.blade.php`

#### 2.1 Verifica Abilitazione Egili

```php
// MintController.php, line ~154
if ($egi->payment_by_egili && $paymentAmountEur !== null && $paymentAmountEur > 0) {
    /** @var EgiliService $egiliService */
    $egiliService = app(EgiliService::class);

    // Ottieni saldo utente
    $egiliBalance = $egiliService->getBalance(Auth::user());

    // Calcola Egili richiesti (conversione EUR → Egili)
    $requiredEgili = max($egiliService->fromEur($paymentAmountEur), 1);

    // Verifica se utente può pagare
    $showEgiliOption = true;
    $canPayWithEgili = $egiliBalance >= $requiredEgili;
}
```

#### 2.2 Conversione EUR ↔ Egili

**Service**: `App\Services\EgiliService`

```php
// Tasso di conversione (EgiliService.php, line ~46)
const EGILI_TO_EUR_RATE = 0.01; // 1 Egilo = €0.01

// Da EUR a Egili
public function fromEur(float $eurAmount): int {
    return (int) round($eurAmount / self::EGILI_TO_EUR_RATE);
}

// Esempio: €10.00 → 1000 Egili
// Esempio: €25.50 → 2550 Egili

// Da Egili a EUR
public function toEur(int $egiliAmount): float {
    return round($egiliAmount * self::EGILI_TO_EUR_RATE, 2);
}
```

#### 2.3 Rendering Form Pagamento

```blade
{{-- payment-form.blade.php, line ~175 --}}
@if ($showEgiliOption)
    <label class="...">
        <input type="radio"
            name="payment_method"
            value="egili"
            {{ $canPayWithEgili ? '' : 'disabled' }}>

        <div>
            <p>🪙 {{ __('mint.payment.payment_method_egili') }}</p>

            {{-- Saldo attuale --}}
            <p>{{ __('mint.payment.egili_balance_label', [
                'balance' => number_format($egiliBalance)
            ]) }}</p>

            {{-- Egili richiesti --}}
            <p>{{ __('mint.payment.egili_required_label', [
                'required' => number_format($requiredEgili)
            ]) }}</p>

            {{-- Warning se insufficienti --}}
            @unless ($canPayWithEgili)
                <p class="text-red-600">
                    {{ __('mint.payment.egili_insufficient') }}
                </p>
            @endunless
        </div>
    </label>
@endif
```

**Translation Keys**:

-   `mint.payment.payment_method_egili`: "Pagamento con Egili"
-   `mint.payment.egili_balance_label`: "Il tuo saldo: {balance} Egili"
-   `mint.payment.egili_required_label`: "Richiesti: {required} Egili"
-   `mint.payment.egili_insufficient`: "Saldo Egili insufficiente"

---

### FASE 3: Elaborazione Pagamento Egili

**Route**: `POST /mint/process`  
**Controller**: `App\Http\Controllers\MintController@processPayment`

#### 3.1 Validazione Request

```php
// MintController.php, line ~339
$validated = $request->validate([
    'egi_id' => 'required|exists:egis,id',
    'payment_method' => 'required|string|in:stripe,paypal,egili',
    'reservation_id' => 'nullable|exists:reservations,id',
]);
```

#### 3.2 Branch Logico per Egili

```php
// MintController.php, line ~462
if ($paymentMethod === 'egili') {
    try {
        // Chiama metodo privato per gestire pagamento Egili
        $egiliPayment = $this->handleEgiliPayment($egi, $reservation, $paymentAmountEur);

        // Estrai dati dal risultato
        $paymentProvider = 'egili_internal';
        $paymentReference = $egiliPayment['reference']; // 'EGL-XXXXXXXXXXXX'
        $paidCurrency = 'EGL';
        $paidAmountRecorded = $egiliPayment['amount_egili']; // Quantità Egili

        $egiliMetadata = [
            'egili_transaction_id' => $egiliPayment['transaction_id'] ?? null,
        ];

    } catch (RuntimeException $egiliException) {
        // Gestione errori specifici Egili
        $reason = $egiliException->getMessage();

        $errorMessageKey = match ($reason) {
            'insufficient_egili' => 'mint.errors.insufficient_egili',
            'egili_disabled' => 'mint.errors.egili_disabled',
            'unauthenticated' => 'mint.errors.unauthorized',
            default => 'mint.errors.payment_failed',
        };

        return redirect()->back()->withErrors(['error' => __($errorMessageKey)]);
    }
}
```

#### 3.3 Metodo handleEgiliPayment (Core Logic)

```php
// MintController.php, line ~786
private function handleEgiliPayment(Egi $egi, ?Reservation $reservation, float $amountEur): array
{
    // 1. Verifica che EGI abbia Egili abilitati
    if (!$egi->payment_by_egili) {
        throw new \RuntimeException('egili_disabled');
    }

    $user = Auth::user();

    if (!$user) {
        throw new \RuntimeException('unauthenticated');
    }

    // 2. Ottieni service e calcola Egili richiesti
    /** @var EgiliService $egiliService */
    $egiliService = app(EgiliService::class);
    $requiredEgili = max($egiliService->fromEur($amountEur), 1);

    // 3. Verifica saldo sufficiente
    if (!$egiliService->canSpend($user, $requiredEgili)) {
        throw new \RuntimeException('insufficient_egili');
    }

    // 4. Genera riferimento unico
    $reference = 'EGL-' . strtoupper(Str::random(12));
    // Esempio: 'EGL-X9K2P7M4Q1W8'

    // 5. Esegui transazione Egili (atomica)
    $transaction = $egiliService->spend(
        $user,
        $requiredEgili,
        'egi_direct_mint', // reason (machine-readable)
        'mint',            // category (reporting)
        [
            'egi_id' => $egi->id,
            'reservation_id' => $reservation?->id,
            'payment_reference' => $reference,
            'amount_eur' => $amountEur,
        ],
        $egi // source (polymorphic relation)
    );

    // 6. Log ULM
    $this->logger->info('EGILI_PAYMENT_PROCESSED', [
        'user_id' => $user->id,
        'egi_id' => $egi->id,
        'reservation_id' => $reservation?->id,
        'required_egili' => $requiredEgili,
        'reference' => $reference,
        'transaction_id' => $transaction->id ?? null,
    ]);

    // 7. Return payment data
    return [
        'success' => true,
        'reference' => $reference,
        'provider' => 'egili_internal',
        'amount_eur' => $amountEur,
        'amount_egili' => $requiredEgili,
        'transaction_id' => $transaction->id ?? null,
    ];
}
```

---

### FASE 4: Transazione Atomica Egili

**Service**: `App\Services\EgiliService@spend`  
**File**: `app/Services/EgiliService.php`, line ~244

#### 4.1 Processo Atomico (DB Transaction)

```php
public function spend(
    User $user,
    int $amount,
    string $reason,
    ?string $category = null,
    ?array $metadata = null,
    $source = null
): EgiliTransaction {

    // 1. Validazioni preliminari
    if ($amount <= 0) {
        throw new \InvalidArgumentException("Amount must be positive");
    }

    if (!$user->wallet) {
        throw new \Exception("User has no wallet");
    }

    if (!$this->canSpend($user, $amount)) {
        $currentBalance = $this->getBalance($user);
        throw new \Exception(
            "Saldo insufficiente. Disponibili: {$currentBalance}, Richiesti: {$amount}"
        );
    }

    // 2. Transazione atomica DB
    return DB::transaction(function () use ($user, $amount, $reason, $category, $metadata, $source) {
        $wallet = $user->wallet;
        $balanceBefore = $wallet->egili_balance;
        $balanceAfter = $balanceBefore - $amount;

        // 2.1 Aggiorna wallet (atomico)
        $wallet->update([
            'egili_balance' => $balanceAfter,
            'egili_lifetime_spent' => $wallet->egili_lifetime_spent + $amount,
        ]);

        // 2.2 Crea record transazione (audit trail)
        $transaction = EgiliTransaction::create([
            'wallet_id' => $wallet->id,
            'user_id' => $user->id,
            'transaction_type' => 'spent',
            'operation' => 'subtract',
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'source_type' => $source ? get_class($source) : null, // 'App\Models\Egi'
            'source_id' => $source?->id,
            'reason' => $reason, // 'egi_direct_mint'
            'category' => $category ?? 'other', // 'mint'
            'metadata' => $metadata, // JSON con egi_id, reference, etc.
            'status' => 'completed',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        // 2.3 GDPR Audit Log
        $this->auditService->logUserAction(
            $user,
            'egili_spent',
            [
                'amount' => $amount,
                'reason' => $reason,
                'category' => $category,
                'balance_after' => $balanceAfter,
                'transaction_id' => $transaction->id,
            ],
            GdprActivityCategory::BLOCKCHAIN_ACTIVITY
        );

        // 2.4 ULM Log
        $this->logger->info('Egili spent successfully', [
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'transaction_id' => $transaction->id,
            'amount' => $amount,
            'balance_after' => $balanceAfter,
            'log_category' => 'EGILI_SPEND_SUCCESS'
        ]);

        return $transaction;
    });
}
```

#### 4.2 Record Creato in `egili_transactions`

```json
{
    "id": 12345,
    "wallet_id": 67,
    "user_id": 89,
    "transaction_type": "spent",
    "operation": "subtract",
    "amount": 2500,
    "balance_before": 5000,
    "balance_after": 2500,
    "source_type": "App\\Models\\Egi",
    "source_id": 42,
    "reason": "egi_direct_mint",
    "category": "mint",
    "metadata": {
        "egi_id": 42,
        "reservation_id": null,
        "payment_reference": "EGL-X9K2P7M4Q1W8",
        "amount_eur": 25.0
    },
    "status": "completed",
    "ip_address": "192.168.1.100",
    "user_agent": "Mozilla/5.0...",
    "created_at": "2025-11-23 14:30:00"
}
```

---

### FASE 5: Creazione Record Blockchain

**Controller**: `App\Http\Controllers\MintController@processPayment` (continua)  
**File**: `app/Http/Controllers/MintController.php`, line ~575

```php
// 1. Crea record blockchain
$egiBlockchain = EgiBlockchain::create([
    'egi_id' => $egi->id,
    'buyer_user_id' => Auth::id(),
    'reservation_id' => $reservation?->id,

    // Dati pagamento Egili
    'payment_provider' => 'egili_internal',
    'payment_reference' => $egiliPayment['reference'], // 'EGL-XXXXXXXXXXXX'
    'paid_currency' => 'EGL',
    'paid_amount_recorded' => $egiliPayment['amount_egili'], // 2500

    // Prezzi EUR (per reportistica)
    'sale_price_eur' => $paymentAmountEur, // 25.00

    // Metadata
    'payment_metadata' => [
        'egili_transaction_id' => $egiliPayment['transaction_id'],
        'egili_amount' => $egiliPayment['amount_egili'],
        'conversion_rate' => EgiliService::EGILI_TO_EUR_RATE,
    ],

    // Status iniziale
    'status' => 'pending_mint',
    'mint_status' => 'pending',
]);

// 2. Dispatch job asincrono per mint blockchain
MintEgiJob::dispatch($egiBlockchain->id)
    ->onQueue('blockchain');

// 3. GDPR Audit
$this->auditService->logUserAction(
    Auth::user(),
    'egi_mint_initiated',
    [
        'egi_id' => $egi->id,
        'blockchain_id' => $egiBlockchain->id,
        'payment_method' => 'egili',
        'amount_egili' => $egiliPayment['amount_egili'],
    ],
    GdprActivityCategory::BLOCKCHAIN_ACTIVITY
);

// 4. Redirect a pagina risultato
return redirect()->route('mint.show', $egiBlockchain->id)
    ->with('success', __('mint.notification.processing_message'));
```

---

### FASE 6: Mint Blockchain Asincrono

**Job**: `App\Jobs\MintEgiJob`  
**Queue**: `blockchain`

#### 6.1 Processo Mint (Simplified)

```php
// Il job esegue:
// 1. Verifica record EgiBlockchain
// 2. Genera certificato PDF (CertificateGeneratorService)
// 3. Minta su blockchain Algorand
// 4. Aggiorna status: 'minted', 'completed'
// 5. Notifica utente (email, notification bell)
```

---

## 📝 Record Database Finale

### Wallet (Aggiornato)

```sql
-- wallets table
id: 67
user_id: 89
egili_balance: 2500          -- Era 5000, spesi 2500
egili_lifetime_earned: 10000
egili_lifetime_spent: 7500   -- Era 5000, +2500 ora
```

### Egili Transaction (Creata)

```sql
-- egili_transactions table
id: 12345
wallet_id: 67
user_id: 89
transaction_type: 'spent'
operation: 'subtract'
amount: 2500
balance_before: 5000
balance_after: 2500
source_type: 'App\Models\Egi'
source_id: 42
reason: 'egi_direct_mint'
category: 'mint'
metadata: {"egi_id":42,"payment_reference":"EGL-X9K2P7M4Q1W8",...}
status: 'completed'
```

### EgiBlockchain (Creato)

```sql
-- egi_blockchain table
id: 9876
egi_id: 42
buyer_user_id: 89
payment_provider: 'egili_internal'
payment_reference: 'EGL-X9K2P7M4Q1W8'
paid_currency: 'EGL'
paid_amount_recorded: 2500.00
sale_price_eur: 25.00
status: 'pending_mint' → 'minted' → 'completed'
mint_status: 'pending' → 'minting' → 'completed'
```

---

## 🔍 Punti Chiave del Sistema

### 1. **Configurazione Opt-In**

-   Il Creator **deve abilitare esplicitamente** `payment_by_egili` per ogni EGI
-   Default: `false` (solo EUR/stripe/paypal)
-   Checkbox nel CRUD panel EGI

### 2. **Validazione Multi-Livello**

```
1. Frontend: Disabilita radio se saldo insufficiente
2. Backend (handleEgiliPayment): Check payment_by_egili flag
3. Service (EgiliService::spend): Check canSpend()
4. DB Transaction: Atomicità garantita
```

### 3. **Conversione EUR ↔ Egili**

```
Tasso fisso: 1 Egilo = €0.01

Esempi:
- €10.00 → 1000 Egili
- €25.00 → 2500 Egili
- €99.99 → 9999 Egili (arrotondato)

Funzioni:
- fromEur(10.00) → 1000
- toEur(2500) → 25.00
```

### 4. **Riferimento Pagamento Egili**

```
Formato: 'EGL-' + 12 caratteri random uppercase
Esempio: 'EGL-X9K2P7M4Q1W8'

Generato in: MintController::handleEgiliPayment()
Salvato in: egi_blockchain.payment_reference
```

### 5. **Audit Trail Completo**

```
Ogni spesa Egili genera:
1. EgiliTransaction record
2. GDPR audit log (AuditLogService)
3. ULM log entry (UltraLogManager)
4. Wallet update (egili_balance, egili_lifetime_spent)
```

### 6. **Atomicità Transazioni**

```php
DB::transaction(function() {
    // 1. Update wallet balance
    // 2. Create egili_transaction
    // 3. GDPR log
    // 4. ULM log
});

// Se fallisce QUALSIASI step → rollback completo
```

---

## 🎬 Esempio Pratico End-to-End

### Scenario

-   **EGI ID**: 42
-   **Titolo**: "Sunset Over Florence"
-   **Prezzo**: €25.00
-   **Creator**: User ID 10 (ha abilitato `payment_by_egili = true`)
-   **Buyer**: User ID 89 (ha 5000 Egili in wallet)

### Step by Step

1. **Buyer visita** `/mint/payment/42`

    - Controller calcola: `fromEur(25.00) = 2500 Egili`
    - Verifica: `5000 >= 2500` → `canPayWithEgili = true`
    - Mostra opzione radio Egili abilitata

2. **Buyer seleziona** radio `egili` e clicca "Procedi al Mint"

    - Form POST a `/mint/process`
    - `payment_method = 'egili'`

3. **Controller esegue** `handleEgiliPayment()`

    - Verifica: `$egi->payment_by_egili = true` ✓
    - Verifica: saldo 5000 >= 2500 ✓
    - Genera: `reference = 'EGL-A7F3K9M2P1Q8'`
    - Chiama: `egiliService->spend(user, 2500, 'egi_direct_mint', ...)`

4. **EgiliService atomicamente**:

    - Wallet: `5000 - 2500 = 2500`
    - Crea: `EgiliTransaction` (id 12345)
    - GDPR log: `egili_spent`
    - ULM log: `EGILI_SPEND_SUCCESS`

5. **Controller crea** `EgiBlockchain`:

    ```
    payment_provider: 'egili_internal'
    payment_reference: 'EGL-A7F3K9M2P1Q8'
    paid_currency: 'EGL'
    paid_amount_recorded: 2500
    ```

6. **Job asincrono** `MintEgiJob`:

    - Genera certificato PDF
    - Minta su Algorand
    - Status: `completed`

7. **Buyer vede** pagina `/mint/{blockchainId}`:
    - "Congratulazioni! Il tuo EGI è stato mintato"
    - Dettagli blockchain
    - Link download certificato

---

## 📂 File Coinvolti

### Controllers

-   `app/Http/Controllers/MintController.php` - Gestione mint e pagamenti
-   `app/Http/Controllers/EgiController.php` - CRUD EGI (config payment_by_egili)

### Services

-   `app/Services/EgiliService.php` - Logica core Egili (earn/spend/balance)

### Models

-   `app/Models/Egi.php` - Campo `payment_by_egili`
-   `app/Models/Wallet.php` - Campi `egili_balance`, `egili_lifetime_*`
-   `app/Models/EgiliTransaction.php` - Audit trail transazioni
-   `app/Models/EgiBlockchain.php` - Record mint con payment info

### Views

-   `resources/views/mint/payment-form.blade.php` - Form selezione metodo pagamento
-   `resources/views/egis/partials/sidebar/crud-panel.blade.php` - Checkbox payment_by_egili

### Migrations

-   `2025_11_13_120000_add_payment_by_egili_to_egis_table.php`
-   (Altre migration per wallets, egili_transactions, egi_blockchain già esistenti)

### Routes

-   `routes/web.php`:
    -   `GET /mint/payment/{egiId}` → `MintController@showPaymentForm`
    -   `POST /mint/process` → `MintController@processPayment`

---

## ⚠️ Note Importanti

### MiCA Compliance

-   Egili **NON sono criptovaluta**
-   Egili **NON sono trasferibili** tra utenti
-   Egili sono **utility token** account-bound
-   Nessuna custodia crypto → MiCA-safe

### GDPR Compliance

-   Ogni transazione Egili è logged in:
    1. `egili_transactions` table (audit trail)
    2. `gdpr_audit_logs` (via AuditLogService)
    3. ULM logs (via UltraLogManager)
-   Categoria GDPR: `BLOCKCHAIN_ACTIVITY`

### Atomicità

-   Tutte le operazioni Egili usano `DB::transaction()`
-   Rollback automatico in caso di errore
-   Nessuna possibilità di inconsistenza dati

### Error Handling

```php
try {
    $egiliPayment = $this->handleEgiliPayment(...);
} catch (RuntimeException $e) {
    match ($e->getMessage()) {
        'insufficient_egili' => __('mint.errors.insufficient_egili'),
        'egili_disabled' => __('mint.errors.egili_disabled'),
        'unauthenticated' => __('mint.errors.unauthorized'),
        default => __('mint.errors.payment_failed'),
    };
}
```

---

## 🔄 Possibili Estensioni Future

1. **Refund Egili**: Se mint fallisce, rimborsare automaticamente
2. **Egili Cashback**: Dare % Egili al buyer dopo mint riuscito
3. **Dynamic Conversion Rate**: Permettere al Creator di scegliere tasso conversione
4. **Egili Packages**: Vendita pacchetti Egili con discount
5. **Egili Expiry**: Gift Egili con scadenza temporale

---

## 📞 Contatti e Supporto

**Documentato da**: Padmin D. Curtis (AI Partner OS3.0)  
**Data**: 2025-11-23  
**Versione**: 1.0.0  
**Ultima Revisione**: 2025-11-23

Per domande tecniche sul sistema Egili:

-   **ULM Logs**: Cercare `log_category` = `EGILI_*`
-   **GDPR Audit**: Categoria `BLOCKCHAIN_ACTIVITY`
-   **Error Manager**: Codici `EGILI_*` o `MINT_PAYMENT_*`

---

## 📊 Diagramma Flusso Semplificato

```
[Creator]
   ↓
[Abilita payment_by_egili nel CRUD EGI]
   ↓
[Salva: egis.payment_by_egili = true]
   ↓
[Buyer visita /mint/payment/{egiId}]
   ↓
[Controller verifica: payment_by_egili && saldo >= required]
   ↓
[Mostra opzione radio Egili (abilitata/disabilitata)]
   ↓
[Buyer seleziona "Egili" e invia form]
   ↓
[POST /mint/process con payment_method=egili]
   ↓
[handleEgiliPayment():
  - Verifica payment_by_egili
  - Calcola Egili richiesti
  - Genera reference 'EGL-XXX'
  - Chiama egiliService->spend()]
   ↓
[DB Transaction:
  - wallet.egili_balance -= amount
  - CREATE egili_transaction
  - GDPR audit log
  - ULM log]
   ↓
[CREATE egi_blockchain con:
  - payment_provider = 'egili_internal'
  - payment_reference = 'EGL-XXX'
  - paid_currency = 'EGL'
  - paid_amount_recorded = Egili spesi]
   ↓
[Dispatch MintEgiJob (asincrono)]
   ↓
[Job minta su Algorand]
   ↓
[Status: completed]
   ↓
[Redirect a /mint/{blockchainId}]
   ↓
[Buyer vede: "Mint completato!"]
```

---

**Fine Documentazione** 🎉
