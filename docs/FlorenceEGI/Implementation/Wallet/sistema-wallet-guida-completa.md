# Sistema Wallet FlorenceEGI - Guida Completa

## Introduzione

Il sistema **Wallet** di FlorenceEGI gestisce:
- **Algorand blockchain wallets** (custodial) con crittografia envelope via KMS
- **Payment destinations** multi-PSP (Stripe, PayPal, Bank Transfer, Egili)
- **Royalty distribution** (mint, rebind) con split payment automatico
- **GDPR compliance** con audit logging per ogni operazione sensibile
- **Multi-wallet collections** per split payment tra creator + platform + collaboratori

## Caratteristiche Principali

### Security
- **Envelope Encryption**: Mnemonic crittografata con KMS (Key Management Service)
- **Sodium Memzero**: Cancellazione sicura mnemonic dalla memoria dopo encryption
- **IBAN Encryption**: Laravel encrypted cast per IBAN
- **Hidden Fields**: Sensitive data mai esposta in serializzazione JSON/array
- **KMS Health Check**: Pre-flight check before wallet creation

### Flexibility
- **Nullable user_id**: Supporta wallet per collections senza user associato
- **Multi-PSP**: WalletDestination supporta payment types diversi (Stripe/PayPal/Bank/Algorand/Egili)
- **Platform Roles**: Wallet di sistema (Natan, Frangette) vs wallet utenti
- **Anonymous Wallets**: Flag `is_anonymous` per wallet senza owner pubblico

### Compliance
- **GDPR Audit**: Ogni operazione wallet loggata in `gdpr_activity_logs`
- **Legislative**: Royalty rebind 4.5% conforme Legge 633/1941 Art. 144 (droit de suite)
- **No Custody** (per PSP): Payment destinations puntano a merchant account esterni

## Architettura Database

### Tabella: wallets

#### Campi Principali

##### Relationships
- `id`: Bigint PK
- `user_id`: FK users, **NULLABLE** (supporta wallet collection-only)
- `collection_id`: FK collections, nullable
- `notification_payload_wallets_id`: FK notification_payload_wallets

##### Algorand Wallet
- `wallet`: String(255) - Algorand address (public key)
- **Encryption Fields** (Envelope Encryption):
  - `secret_ciphertext`: Text - Mnemonic crittografata (base64)
  - `secret_nonce`: String - Nonce encryption (base64)
  - `secret_tag`: String - Authentication tag
  - `dek_encrypted`: JSON - Data Encryption Key crittografata con KMS
  - `cipher_algo`: String - Algorithm used (es. "aes-256-gcm")
- `version`: Integer - Wallet schema version

##### Business Logic
- `platform_role`: Enum (Creator|EPP|Natan|Frangette|Company|Buyer)
- `royalty_mint`: Decimal(5,2) - Percentuale royalty mint (0-100%)
- `royalty_rebind`: Decimal(5,2) - Percentuale royalty rebind (0-100%)
- `is_anonymous`: Boolean - Wallet anonimo (no public owner)
- `metadata`: JSON - Metadata aggiuntivi
- `wallet_type`: String - Tipo wallet (es. "primary", "collection")

##### IBAN (Legacy)
- `iban_encrypted`: Text - IBAN crittografato (Laravel encrypted cast)
- `iban_hash`: String(64) - SHA256 hash per uniqueness check
- `iban_last4`: String(4) - Ultime 4 cifre IBAN (per masking display)

**Note**: IBAN legacy sta migrando a WalletDestination. Usare WalletDestination per nuovi wallet.

##### PSP (Legacy)
- `stripe_account_id`: String(255) - Stripe Connect account
- `paypal_merchant_id`: String(255) - PayPal merchant ID

**Note**: PSP fields legacy. Usare WalletDestination per nuova architettura.

##### Egili Balance
- `egili_balance`: Integer - Current balance in Egili tokens
- `egili_lifetime_earned`: Integer - Total Egili earned
- `egili_lifetime_spent`: Integer - Total Egili spent

**IMPORTANT**: Questi campi sono gestiti SOLO da `EgiliService`. Non modificare direttamente.

#### Hidden Fields (Security)
```php
protected $hidden = [
    'secret_ciphertext',
    'secret_nonce',
    'secret_tag',
    'dek_encrypted',
    'iban_encrypted',
    'iban_hash',
    'stripe_account_id',
    'paypal_merchant_id',
];
```

**Rationale**: Sensitive data NEVER exposed in API responses o JSON serialization.

#### Casts
```php
protected $casts = [
    'is_anonymous' => 'boolean',
    'metadata' => 'array',
    'iban_encrypted' => 'encrypted', // Laravel automatic encryption
    'egili_balance' => 'integer',
    'egili_lifetime_earned' => 'integer',
    'egili_lifetime_spent' => 'integer',
];
```

### Tabella: wallet_destinations

Nuova architettura (OS3.0) per payment destinations multi-PSP.

#### Campi
- `id`: Bigint PK
- `wallet_id`: FK wallets
- `payment_type`: Enum (STRIPE|PAYPAL|BANK_TRANSFER|ALGORAND|EGILI)
- `destination_value`: Text - Account ID o IBAN (crittografato se BANK_TRANSFER)
- `is_verified`: Boolean - Verificato con API PSP
- `verified_at`: Timestamp, nullable
- `is_primary`: Boolean - Destinazione primaria per questo payment type
- `metadata`: JSON - Metadata aggiuntivi (es. BIC, holder per BANK_TRANSFER)
- `created_at`, `updated_at`: Timestamps

#### Payment Types (PaymentTypeEnum)

##### STRIPE
```php
PaymentTypeEnum::STRIPE->value // 'STRIPE'
```
- **destination_value**: Stripe Connect account ID (formato: `acct_XXXXX`)
- **Encryption**: Plain text (account ID è reference, non sensitive come IBAN)
- **Validation**: Chiama Stripe API `accounts->retrieve()`, verifica `charges_enabled`
- **Metadata**: `{'connected_at' => ISO8601}`

##### PAYPAL
```php
PaymentTypeEnum::PAYPAL->value // 'PAYPAL'
```
- **destination_value**: PayPal Merchant ID
- **Encryption**: Plain text
- **Validation**: ⚠️ Not yet implemented (ritorna validation failed)
- **Metadata**: TBD

##### BANK_TRANSFER
```php
PaymentTypeEnum::BANK_TRANSFER->value // 'BANK_TRANSFER'
```
- **destination_value**: IBAN (CRITTOGRAFATO automaticamente da mutator)
- **Encryption**: `Crypt::encryptString()` in mutator, `Crypt::decryptString()` in accessor
- **Validation**: Regex SEPA format (XX00XXXX..., 15-34 caratteri)
- **Metadata**: `{'bic' => 'XXXXXX00', 'holder' => 'Nome Cognome'}`

##### ALGORAND
```php
PaymentTypeEnum::ALGORAND->value // 'ALGORAND'
```
- **destination_value**: Algorand address (58 caratteri)
- **Encryption**: Plain text (address è public key)
- **Validation**: Checksum validation Algorand address
- **Metadata**: Opzionale

##### EGILI
```php
PaymentTypeEnum::EGILI->value // 'EGILI'
```
- **destination_value**: Wallet ID reference (internal)
- **Encryption**: Plain text
- **Validation**: Check wallet existence
- **Metadata**: TBD

#### Hidden Fields
```php
protected $hidden = [
    'destination_value',  // Può contenere IBAN crittografato
];
```

## Modelli Eloquent

### Wallet Model

**File**: `app/Models/Wallet.php`

#### Relationships

##### `collection()`
Collezione associata.
```php
public function collection() {
    return $this->belongsTo(Collection::class);
}
```

##### `user()`
User associato (NULLABLE).
```php
public function user() {
    return $this->belongsTo(User::class);
}
```

**Note**: `user_id` può essere `null` per wallet collection-only (es. EPP project wallet).

##### `destinations()`
Payment destinations (nuova architettura).
```php
public function destinations() {
    return $this->hasMany(WalletDestination::class);
}
```

**Uso**:
```php
$stripeDestination = $wallet->destinations()
    ->where('payment_type', PaymentTypeEnum::STRIPE->value)
    ->first();
```

##### `egiliTransactions()`
Transazioni Egili token.
```php
public function egiliTransactions() {
    return $this->hasMany(EgiliTransaction::class, 'wallet_id');
}
```

##### `notificationPayloadWallet()`
Notifica wallet proposal associata.
```php
public function notificationPayloadWallet() {
    return $this->belongsTo(NotificationPayloadWallet::class, 'notification_payload_wallets_id');
}
```

#### Accessors

##### `getMaskedIbanAttribute(): ?string`
Ritorna IBAN mascherato per display.

**Output**: `****1234` (solo ultime 4 cifre)

**Esempio**:
```php
echo $wallet->masked_iban; // ****5678
```

**Uso**: Display IBAN in UI senza esporre numero completo.

#### Methods

##### `hasMnemonic(): bool`
Verifica se wallet ha mnemonic crittografata.

**Check**:
```php
return !empty($this->secret_ciphertext) && !empty($this->dek_encrypted);
```

**Uso**: Determinare se wallet è custodial (ha mnemonic) o import-only (no mnemonic).

##### `hasIban(): bool`
Verifica se wallet ha IBAN configurato (legacy).

```php
return !empty($this->iban_encrypted);
```

**Note**: Check legacy. Per nuova architettura usare:
```php
$wallet->destinations()->where('payment_type', PaymentTypeEnum::BANK_TRANSFER->value)->exists();
```

##### `isPlatformWallet(): bool`
Verifica se è wallet di sistema (EPP, Natan, Frangette).

```php
return in_array($this->platform_role, ['EPP', 'Natan', 'Frangette']);
```

**Uso**: Distinguere wallet platform da wallet utenti per business logic.

##### `isCreatorWallet(): bool`
Verifica se è wallet creator.

```php
return $this->platform_role === 'Creator';
```

### WalletDestination Model

**File**: `app/Models/WalletDestination.php`

#### Relationships

##### `wallet(): BelongsTo`
Wallet proprietario.
```php
public function wallet(): BelongsTo {
    return $this->belongsTo(Wallet::class);
}
```

#### Accessors

##### `getPaymentTypeEnumAttribute(): PaymentTypeEnum`
Ritorna enum PaymentTypeEnum.

```php
public function getPaymentTypeEnumAttribute(): PaymentTypeEnum {
    return PaymentTypeEnum::from($this->payment_type);
}
```

**Uso**:
```php
$enum = $destination->paymentTypeEnum;
if ($enum === PaymentTypeEnum::STRIPE) {
    // Logic for Stripe
}
```

##### `getDecryptedValueAttribute(): ?string`
Decripta `destination_value` se BANK_TRANSFER.

**Logica**:
- Se `payment_type === BANK_TRANSFER`: decripta con `Crypt::decryptString()`
- Altrimenti: ritorna plain text

**Uso**:
```php
$iban = $bankDestination->decrypted_value; // IT60X0542811101000000123456
```

##### `getMaskedDestinationAttribute(): string`
Ritorna destination mascherata per display.

**Format**:
- **STRIPE**: `acct_***XXXX` (ultime 4)
- **PAYPAL**: `XXX***XXX` (prime 3 + ultime 3)
- **BANK_TRANSFER**: `XXXX****XXXX` (prime 4 + ultime 4)
- **ALGORAND**: `XXXXXX...XXXXXX` (prime 6 + ultime 6)
- **Default**: `***`

**Esempio**:
```php
echo $stripeDestination->masked_destination; // acct_***5678
echo $bankDestination->masked_destination; // IT60****3456
```

#### Mutators

##### `setDestinationValueAttribute(string $value): void`
Cripta automaticamente se BANK_TRANSFER.

**Logica**:
```php
if ($this->payment_type === PaymentTypeEnum::BANK_TRANSFER->value) {
    $this->attributes['destination_value'] = Crypt::encryptString($value);
} else {
    $this->attributes['destination_value'] = $value;
}
```

**Uso automatico**:
```php
$destination->destination_value = 'IT60X0542811101000000123456'; // Auto-encrypted se BANK_TRANSFER
```

#### Methods

##### `isValidForPayment(): bool`
Valida destination per pagamenti.

**Delega a**: `PaymentTypeEnum->validateDestination()`

**Esempio**:
```php
if ($destination->isValidForPayment()) {
    // Procedi con payment
}
```

##### `supportsAutomaticSplit(): bool`
Verifica se payment type supporta split automatico.

**Delega a**: `PaymentTypeEnum->supportsAutomaticSplit()`

**Ritorna**:
- `STRIPE`: `true` (Stripe Connect Transfers)
- `BANK_TRANSFER`: `false` (split manuale richiesto)
- `ALGORAND`: `true` (atomic transactions)
- `EGILI`: `true` (internal ledger)

##### `markAsVerified(): bool`
Marca destination come verificata.

```php
public function markAsVerified(): bool {
    return $this->update([
        'is_verified' => true,
        'verified_at' => now(),
    ]);
}
```

**Uso**:
```php
if ($stripeClient->accounts->retrieve($accountId)->charges_enabled) {
    $destination->markAsVerified();
}
```

##### `setAsPrimary(): bool`
Imposta come destination primaria per questo payment type.

**Logica**:
1. Rimuove `is_primary = true` da altre destinations dello stesso wallet/payment_type
2. Setta `is_primary = true` su questa

```php
public function setAsPrimary(): bool {
    self::where('wallet_id', $this->wallet_id)
        ->where('payment_type', $this->payment_type)
        ->where('id', '!=', $this->id)
        ->update(['is_primary' => false]);

    return $this->update(['is_primary' => true]);
}
```

**Uso**:
```php
$destination->setAsPrimary(); // Solo questa sarà primary per STRIPE su questo wallet
```

#### Query Scopes

##### `scopeOfType($query, PaymentTypeEnum $type)`
Filtra per payment type.

```php
$stripeDestinations = WalletDestination::ofType(PaymentTypeEnum::STRIPE)->get();
```

##### `scopeVerified($query)`
Solo destinations verificate.

```php
$verified = $wallet->destinations()->verified()->get();
```

##### `scopePrimary($query)`
Solo destinations primarie.

```php
$primary = $wallet->destinations()->primary()->get();
```

## Enums

### WalletRoleEnum

**File**: `app/Enums/Wallet/WalletRoleEnum.php`

Definisce ruoli wallet e royalty tokenomics.

#### Roles

```php
enum WalletRoleEnum: string {
    case CREATOR = 'Creator';
    case EPP = 'EPP';
    case NATAN = 'Natan';
    case FRANGETTE = 'Frangette';
    case COMPANY = 'Company';
    case BUYER = 'Buyer';
}
```

#### Mint Royalties (Primary Market)

**Metodo**: `getMintRoyalty(): float`

**Distribution**:
- **CREATOR**: 68% (+ EPP 20% + Natan 10% + Frangette 2% = 100%)
- **COMPANY**: 90% (no Frangette, EPP optional)
- **EPP**: 20%
- **NATAN**: 10%
- **FRANGETTE**: 2%
- **BUYER**: 0%

**Esempio**:
```php
WalletRoleEnum::CREATOR->getMintRoyalty(); // 68.0
WalletRoleEnum::EPP->getMintRoyalty(); // 20.0
```

**Use Case - Mint Payment Split (€100 EGI)**:
```php
$price = 100.00;
Creator: €68.00
EPP: €20.00
Natan: €10.00
Frangette: €2.00
Total: €100.00
```

**Company Mint (€100 EGI)**:
```php
Company: €90.00
Natan: €10.00
(No Frangette, EPP voluntary)
Total: €100.00
```

#### Rebind Royalties (Secondary Market)

**Metodo**: `getRebindRoyalty(): float`

**Distribution** (conforme Legge 633/1941 Art. 144):
- **CREATOR**: 4.5% (droit de suite)
- **COMPANY**: 4.6% (gets Frangette's 0.1%)
- **EPP**: 0.8%
- **NATAN**: 0.7%
- **FRANGETTE**: 0.1%
- **BUYER**: 0%

**Total Rebind**: 6.1% (creator path) o 5.3% (company path)

**Esempio**:
```php
WalletRoleEnum::CREATOR->getRebindRoyalty(); // 4.5
WalletRoleEnum::TOTAL->getTotalRebindPercentage(); // 6.1
```

**Use Case - Rebind Sale (€1000)**:
```php
Seller (Collector): €939.00 (93.9%)
Creator: €45.00 (4.5%)
EPP: €8.00 (0.8%)
Natan: €7.00 (0.7%)
Frangette: €1.00 (0.1%)
Total Fees: €61.00 (6.1%)
```

#### Platform Roles

**Metodo**: `isPlatformRole(): bool`

**Platform roles** (fixed system accounts):
- **NATAN**: Platform technology & infrastructure
- **FRANGETTE**: Ecosystem development

**Dynamic roles** (user/collection-specific):
- **CREATOR**: Artist/IP owner
- **COMPANY**: Business entity
- **EPP**: Environmental project (per-collection selection)
- **BUYER**: EGI purchaser

**Esempio**:
```php
WalletRoleEnum::NATAN->isPlatformRole(); // true
WalletRoleEnum::CREATOR->isPlatformRole(); // false (dynamic)
```

#### Configuration Integration

##### `getWalletAddress(): ?string`
Recupera Algorand address da config per platform roles.

```php
WalletRoleEnum::NATAN->getWalletAddress(); // config('app.natan_wallet_address')
WalletRoleEnum::FRANGETTE->getWalletAddress(); // config('app.frangette_wallet_address')
WalletRoleEnum::CREATOR->getWalletAddress(); // null (dynamic, user-specific)
```

##### `getUserId(): ?int`
Recupera user ID da config per platform roles.

```php
WalletRoleEnum::NATAN->getUserId(); // config('app.natan_id', 1)
WalletRoleEnum::FRANGETTE->getUserId(); // config('app.frangette_id', 3)
WalletRoleEnum::EPP->getUserId(); // null (dynamic per-collection)
```

**Note**: EPP non è più fixed platform account. EPP wallet creato per ogni EppProject selezionato.

#### Validation

##### `validateMintTotal(): bool`
Valida che mint percentages sommano a 100%.

**Logic**: Exclude COMPANY e BUYER (mutually exclusive).

```php
WalletRoleEnum::validateMintTotal(); // true (68+20+10+2 = 100)
```

##### `getTotalRebindPercentage(): float`
Ritorna total rebind percentage.

```php
WalletRoleEnum::getTotalRebindPercentage(); // 6.1
```

#### Helper Methods

##### `getDescription(): string`
Descrizione human-readable.

```php
WalletRoleEnum::CREATOR->getDescription();
// "Creator/Artist - Intellectual property owner"
```

##### `getTokenomicsSummary(): string`
Summary tokenomics formattato.

```php
WalletRoleEnum::CREATOR->getTokenomicsSummary();
// "Creator: Mint 68.0%, Rebind 4.5%"
```

## Service: WalletProvisioningService

**File**: `app/Services/Wallet/WalletProvisioningService.php`

Gestisce creazione sicura wallet con envelope encryption.

### Dependencies

```php
protected UltraLogManager $logger;
protected ErrorManagerInterface $errorManager;
protected AuditLogService $auditService;
protected AlgorandClient $algorandClient;
protected KmsClient $kms;
protected KmsHealthCheck $kmsHealth;
```

### Metodi Principali

#### `provisionUserWallet(User $user, array $data = []): Wallet`

Crea wallet completo per utente con Algorand + optional IBAN.

**Parametri**:
- `$user`: User model
- `$data`: Array opzionale con:
  - `iban`: String, nullable (IBAN bancario)
  - `wallet_passphrase`: String, nullable (future use)
  - `collection_id`: Int, nullable (associazione collection)

**Workflow**:
1. **KMS Health Check**: `$this->kmsHealth->ensureHealthy()`
   - Verifica KMS disponibile prima di generare mnemonic
   - Previene generazione wallet non recuperabili
2. **ULM Log**: Start wallet creation
3. **DB Transaction**: Atomicità operazioni
4. **Create Algorand Wallet**:
   - Genera account Algorand (address + mnemonic)
   - Envelope encryption mnemonic con KMS
   - Sodium memzero per cancellare mnemonic dalla RAM
   - Salva wallet record con encryption fields
5. **Add IBAN** (optional): Se `$data['iban']` presente
6. **GDPR Audit**: Log `wallet_provisioned` action
7. **ULM Log**: Success

**Return**: `Wallet` model con Algorand wallet creato

**Exceptions**:
- `\Exception`: Se KMS unhealthy, Algorand client fails, DB error
- Logged con ULM + UEM

**Esempio**:
```php
$wallet = $walletProvisioningService->provisionUserWallet($user, [
    'iban' => 'IT60X0542811101000000123456',
    'collection_id' => 123
]);

echo $wallet->wallet; // Algorand address
echo $wallet->hasMnemonic(); // true
echo $wallet->hasIban(); // true
```

#### `provisionWallet(?int $userId, ?int $collectionId, array $data = []): Wallet`

Provisioning flessibile con support wallet collection-only (no user).

**Use Cases**:
1. **User wallet**: `provisionWallet(userId: 123, collectionId: null)`
2. **Collection wallet WITH user**: `provisionWallet(userId: 123, collectionId: 456)`
3. **Collection wallet WITHOUT user**: `provisionWallet(userId: null, collectionId: 456)`

**Parametri**:
- `$userId`: Int, nullable (può essere null per collection-only wallets)
- `$collectionId`: Int, nullable
- `$data`: Array (iban, etc.)

**Workflow**: Simile a `provisionUserWallet`, ma con `user_id` nullable.

**Esempio - EPP Project Wallet**:
```php
// EPP project wallet (no user association)
$eppWallet = $walletProvisioningService->provisionWallet(
    userId: null,
    collectionId: $collection->id,
    data: []
);

$eppWallet->update([
    'platform_role' => WalletRoleEnum::EPP->value
]);
```

### Metodi Interni

#### `createAlgorandWalletFlexible(?int $userId, ?int $collectionId): Wallet` (protected)

Crea Algorand wallet con parametri flessibili.

**Workflow Dettagliato**:

1. **Generate Algorand Account**:
```php
$accountData = $this->algorandClient->createAccount();
// Returns: ['address' => 'ALGO...', 'mnemonic' => '25-word phrase']
```

2. **Envelope Encryption**:
```php
$encrypted = $this->kms->secureEncrypt($mnemonic);
// Returns: [
//     'ciphertext' => 'base64...',
//     'nonce' => 'base64...',
//     'encrypted_dek' => ['ciphertext' => '...', 'kms_key_id' => '...'],
//     'algorithm' => 'aes-256-gcm'
// ]
```

**KMS Envelope Encryption Explained**:
- **DEK (Data Encryption Key)**: Random AES-256 key generato per ogni wallet
- **Encrypt Mnemonic**: Mnemonic crittografata con DEK (AES-256-GCM)
- **Encrypt DEK**: DEK crittografata con KMS Master Key
- **Store**: Solo ciphertext + encrypted DEK salvati in DB

**Vantaggi**:
- KMS Master Key MAI lascia KMS (HSM-protected)
- Ogni wallet ha DEK unica (no single point of failure)
- Decrypt richiede KMS access (audit logged)

3. **Memory Wipe**:
```php
if (function_exists('sodium_memzero')) {
    sodium_memzero($mnemonic); // Overwrite memory
}
```

**Rationale**: Previene memory dump attacks dopo encryption.

4. **Create Wallet Record**:
```php
$wallet = Wallet::create([
    'collection_id' => $collectionId,
    'user_id' => $userId, // NULL per collection-only
    'wallet' => $address,
    'secret_ciphertext' => $encrypted['ciphertext'],
    'secret_nonce' => $encrypted['nonce'],
    'dek_encrypted' => json_encode($encrypted['encrypted_dek']),
    'cipher_algo' => $encrypted['algorithm'],
    'is_anonymous' => false,
]);
```

#### `addIbanToWalletInternal(Wallet $wallet, string $iban): void` (protected)

Aggiunge IBAN a wallet esistente (legacy method).

**Logica**:
1. Normalizza IBAN (uppercase, no spaces)
2. Genera SHA256 hash per uniqueness check
3. Verifica duplicati: `Wallet::where('iban_hash', $hash)->exists()`
4. Se duplicato: lancia `DuplicateIbanException`
5. Salva IBAN crittografato (Laravel encrypted cast)
6. Salva ultime 4 cifre per masking

**Note**: Preferire WalletDestination per nuovi wallet.

## Workflow Tipici

### Creazione Wallet Utente (Onboarding)

**Scenario**: Nuovo user registrato, necessita wallet custodial.

**Route**: POST `/wallet/provision`

**Workflow**:
1. User completa registrazione
2. Sistema chiama `WalletProvisioningService->provisionUserWallet($user)`
3. **Algorand Account**:
   - AlgorandClient genera keypair
   - Address salvato in `wallets.wallet`
   - Mnemonic crittografata con KMS
4. **IBAN** (optional): Se user fornisce IBAN per ricezione pagamenti
5. **WalletDestination**: Crea record BANK_TRANSFER con IBAN crittografato
6. **GDPR Audit**: Log `wallet_provisioned`
7. **Welcome Modal**: Mostra address Algorand e backup instructions (no mnemonic exposure)

**Codice Esempio**:
```php
// Controller
public function provisionWallet(Request $request) {
    $user = Auth::user();

    $wallet = app(WalletProvisioningService::class)
        ->provisionUserWallet($user, [
            'iban' => $request->input('iban'), // optional
        ]);

    return response()->json([
        'success' => true,
        'wallet' => [
            'address' => $wallet->wallet,
            'has_mnemonic' => $wallet->hasMnemonic(),
        ]
    ]);
}
```

### Setup Payment Destination (Stripe)

**Scenario**: Creator vuole ricevere pagamenti via Stripe Connect.

**Route**: POST `/wallets/{wallet}/destinations`

**Workflow**:
1. Creator completa Stripe Connect OAuth flow (o inserisce `acct_XXXXX` manualmente)
2. POST con `payment_type = STRIPE`, `destination_value = acct_XXXXX`
3. Sistema crea `WalletDestination`:
```php
$destination = WalletDestination::create([
    'wallet_id' => $wallet->id,
    'payment_type' => PaymentTypeEnum::STRIPE->value,
    'destination_value' => $request->stripe_account_id,
    'is_verified' => false,
    'is_primary' => true,
    'metadata' => ['connected_at' => now()->toIso8601String()]
]);
```
4. **Validation**: Background job valida con Stripe API
```php
$stripeClient = new \Stripe\StripeClient(config('algorand.payments.stripe.secret_key'));
$account = $stripeClient->accounts->retrieve($destination->destination_value);

if ($account->charges_enabled) {
    $destination->markAsVerified();
}
```
5. **GDPR Audit**: Log `payment_destination_added`

### Mint Payment Split (Primary Market)

**Scenario**: Buyer acquista EGI €100, royalty split automatico.

**Workflow**:
1. **Resolve Wallets** (via `MintController`):
```php
$collection = $egi->collection;
$creatorWallet = $collection->wallets()->where('platform_role', 'Creator')->first();
$eppWallet = $collection->eppProject->wallet;
$natanWallet = Wallet::where('platform_role', 'Natan')->first();
$frangetteWallet = Wallet::where('platform_role', 'Frangette')->first();
```

2. **Calculate Split** (via `WalletRoleEnum`):
```php
$price = 100.00;
$splits = [
    'creator' => $price * (WalletRoleEnum::CREATOR->getMintRoyalty() / 100), // €68
    'epp' => $price * (WalletRoleEnum::EPP->getMintRoyalty() / 100), // €20
    'natan' => $price * (WalletRoleEnum::NATAN->getMintRoyalty() / 100), // €10
    'frangette' => $price * (WalletRoleEnum::FRANGETTE->getMintRoyalty() / 100), // €2
];
```

3. **Validate Destinations** (via `MerchantAccountResolver`):
```php
$validation = $merchantAccountResolver->validateAllCollectionWallets($egi, 'stripe');
if (!$validation['all_valid']) {
    throw new \Exception('Not all wallets have valid payment destinations');
}
```

4. **Create Stripe PaymentIntent con Transfers**:
```php
$paymentIntent = $stripeClient->paymentIntents->create([
    'amount' => 10000, // €100 in cents
    'currency' => 'eur',
    'transfer_group' => "MINT_{$egi->id}",
]);

// Dopo payment success, crea transfers
foreach ($splits as $role => $amount) {
    $wallet = ${$role . 'Wallet'};
    $destination = $wallet->destinations()
        ->where('payment_type', PaymentTypeEnum::STRIPE->value)
        ->first();

    $stripeClient->transfers->create([
        'amount' => (int)($amount * 100),
        'currency' => 'eur',
        'destination' => $destination->destination_value,
        'transfer_group' => "MINT_{$egi->id}",
        'metadata' => [
            'role' => $role,
            'egi_id' => $egi->id,
            'mint_tx' => 'true'
        ]
    ]);
}
```

**Risultato**: €100 distribuiti automaticamente tra 4 wallets secondo tokenomics.

### Rebind Payment Split (Secondary Market)

**Scenario**: Collector rivende EGI a €1000, royalty rebind automatiche.

**Workflow**:
1. **Calculate Rebind Split**:
```php
$salePrice = 1000.00;
$rebindSplits = [
    'seller' => $salePrice * (1 - 0.061), // €939 (93.9%)
    'creator' => $salePrice * (WalletRoleEnum::CREATOR->getRebindRoyalty() / 100), // €45
    'epp' => $salePrice * (WalletRoleEnum::EPP->getRebindRoyalty() / 100), // €8
    'natan' => $salePrice * (WalletRoleEnum::NATAN->getRebindRoyalty() / 100), // €7
    'frangette' => $salePrice * (WalletRoleEnum::FRANGETTE->getRebindRoyalty() / 100), // €1
];
```

2. **Validate Seller Wallet** (Collector deve avere payment destination configurato):
```php
$sellerWallet = $seller->primaryWallet;
$sellerDestination = $sellerWallet->destinations()
    ->where('payment_type', PaymentTypeEnum::STRIPE->value)
    ->verified()
    ->first();

if (!$sellerDestination) {
    throw new \Exception('Seller must configure Stripe payment destination');
}
```

3. **Create Stripe Charge + Transfers**:
```php
// Buyer paga €1000
$charge = $stripeClient->charges->create([
    'amount' => 100000,
    'currency' => 'eur',
    'source' => $buyerCardToken,
]);

// Split a seller (€939)
$stripeClient->transfers->create([
    'amount' => 93900,
    'currency' => 'eur',
    'destination' => $sellerDestination->destination_value,
    'metadata' => ['type' => 'rebind_seller_payment']
]);

// Split royalties (€61 totali divisi tra creator/EPP/Natan/Frangette)
// ... (loop come mint split)
```

**Risultato**: Seller riceve €939, royalties distribuite automaticamente.

### Collection Wallet Setup (Multi-Wallet)

**Scenario**: Collection collaborativa con 3 creators che dividono royalty.

**Workflow**:
1. **Creator A** (owner) crea collection
2. **Wallet A** (Creator): Auto-created con `provisionWallet(userId: A, collectionId: X)`
3. **Invite Collaborators**:
   - Creator A invita Creator B e C
   - Sistema crea wallet B e C: `provisionWallet(userId: B/C, collectionId: X)`
4. **Configure Split Percentages**:
```php
// collection_wallets pivot table
CollectionWallet::create([
    'collection_id' => $collection->id,
    'wallet_id' => $walletB->id,
    'split_percentage' => 30.0, // Creator B gets 30% of creator share
]);
```
5. **Mint Split Calculation**:
```php
// Base creator share: 68%
$creatorShare = 68.00;
$walletA_share = $creatorShare * 0.40; // 40% → 27.2%
$walletB_share = $creatorShare * 0.30; // 30% → 20.4%
$walletC_share = $creatorShare * 0.30; // 30% → 20.4%
// Total: 68%
```

6. **Payment Split**: Stripe transfer a TUTTI i wallets con split % corretto.

## Security Best Practices

### 1. Mnemonic Handling
- **NEVER** log mnemonic in plain text
- **ALWAYS** use envelope encryption con KMS
- **ALWAYS** call `sodium_memzero()` dopo encryption
- **NEVER** ritornare mnemonic in API responses (use `$hidden` attribute)

### 2. IBAN Encryption
- **ALWAYS** use WalletDestination con automatic encryption per nuovi wallet
- **LEGACY**: `iban_encrypted` usa Laravel encrypted cast
- **Display**: Solo `iban_last4` o `masked_iban` in UI

### 3. Payment Destination Validation
- **ALWAYS** validate con PSP API prima di `markAsVerified()`
- **Stripe**: Check `charges_enabled = true`
- **IBAN**: Validate regex SEPA format
- **NEVER** trust user input senza validation

### 4. KMS Health Check
- **ALWAYS** call `kmsHealth->ensureHealthy()` prima di wallet creation
- **Prevent**: Generazione wallets non recuperabili se KMS down
- **Fallback**: Se KMS unhealthy, ritorna error e riprova dopo

### 5. GDPR Audit
- **EVERY** wallet operation → `auditService->logUserAction()`
- **Categories**: `WALLET_CREATED`, `WALLET_MANAGEMENT`, `FINANCIAL`
- **Data**: Include wallet_id, address (public), operation, ma MAI mnemonic/IBAN plain

## Troubleshooting

### Errore: "KMS unhealthy"
**Causa**: KmsHealthCheck fallisce

**Check**:
```bash
php artisan kms:health-check
```

**Fix**: Verificare KMS service running, network connectivity, credentials.

### Errore: "Duplicate IBAN"
**Causa**: `DuplicateIbanException` - IBAN già usato da altro wallet

**Check**:
```php
$hash = hash('sha256', strtoupper(str_replace(' ', '', $iban)));
$existing = Wallet::where('iban_hash', $hash)->first();
```

**Fix**: IBAN deve essere unico per wallet. User deve usare IBAN diverso.

### Wallet non riceve pagamenti
**Check**:
1. **WalletDestination exists**?
```php
$destination = $wallet->destinations()
    ->where('payment_type', PaymentTypeEnum::STRIPE->value)
    ->first();
```
2. **is_verified = true**?
```php
if (!$destination->is_verified) {
    // Run validation
}
```
3. **Stripe account charges_enabled**?
```bash
stripe accounts retrieve $account_id
```

**Fix**: Completare Stripe onboarding, verificare destination.

### Mnemonic decrypt fails
**Causa**: KMS key rotated/deleted o corrupted ciphertext

**Check**:
```php
try {
    $kms->secureDecrypt([
        'ciphertext' => $wallet->secret_ciphertext,
        'nonce' => $wallet->secret_nonce,
        'encrypted_dek' => json_decode($wallet->dek_encrypted, true),
        'algorithm' => $wallet->cipher_algo
    ]);
} catch (\Exception $e) {
    // Decrypt failed
}
```

**Fix**: Se KMS key deleted → wallet IRRECUPERABILE (backup critical!). Se corrupted DB → restore da backup.

## Riferimenti Codice

- **Wallet Model**: `app/Models/Wallet.php:17`
- **WalletDestination Model**: `app/Models/WalletDestination.php:32`
- **WalletRoleEnum**: `app/Enums/Wallet/WalletRoleEnum.php:23`
- **WalletProvisioningService**: `app/Services/Wallet/WalletProvisioningService.php:30`
- **MerchantAccountResolver**: `app/Services/Payment/MerchantAccountResolver.php:18`
- **Payment Settings Controller**: `app/Http/Controllers/PaymentSettingsController.php:24`
- **KMS Client**: `app/Services/Security/KmsClient.php`
- **Algorand Client**: `app/Services/Blockchain/AlgorandClient.php`

## Prossimi Sviluppi

- [ ] Wallet recovery flow (mnemonic reveal con MFA)
- [ ] Multi-signature wallets per collections enterprise
- [ ] Automated wallet backup to encrypted cold storage
- [ ] Wallet balance real-time sync con Algorand indexer
- [ ] Support per hardware wallets (Ledger integration)
- [ ] PayPal destination validation implementation
- [ ] Complete migration da legacy IBAN fields a WalletDestination
