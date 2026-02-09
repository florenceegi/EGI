# Sistema Pagamenti FlorenceEGI

## Introduzione

FlorenceEGI implementa un sistema di pagamento multi-PSP (Payment Service Provider) conforme MiCA che consente ai creators di ricevere pagamenti direttamente sui propri account merchant, senza custodia da parte della piattaforma.

## Provider Supportati

### 1. Stripe Connect
- **Tipo**: Pagamenti con carta di credito/debito
- **Integrazione**: Stripe Connect con account merchant del creator
- **Campo**: `stripe_account_id` (formato: `acct_XXXXX`)
- **Validazione**: API Stripe verifica `charges_enabled` per l'account
- **Architettura**: WalletDestination (PaymentTypeEnum::STRIPE) + legacy User model field
- **Stato**: ✅ Completamente operativo

### 2. Egili (Crediti Interni)
- **Tipo**: Crediti interni della piattaforma
- **Uso**: Pagamenti con saldo Egili dell'utente
- **Richiede configurazione PSP**: No
- **Stato**: ✅ Operativo

### 3. Bonifico Bancario (Bank Transfer)
- **Tipo**: Bonifico SEPA
- **Campi richiesti**:
  - IBAN (15-34 caratteri, formato: `XX00XXXX...`)
  - BIC/SWIFT (opzionale, 8-11 caratteri)
  - Intestatario (holder)
- **Validazione**: Regex IBAN/BIC standard europei
- **Crittografia**: IBAN automaticamente crittografato via mutator
- **Architettura**: WalletDestination (PaymentTypeEnum::BANK_TRANSFER)
- **Stato**: ✅ Operativo

### 4. PayPal
- **Tipo**: PayPal payments
- **Integrazione**: PayPal merchant accounts
- **Campo**: `paypal_merchant_id`
- **Architettura**: WalletDestination (PaymentTypeEnum::PAYPAL)
- **Stato**: ⚠️ Implementazione parziale (validation non completata)

## Architettura Dual-Layer

### Nuova Architettura: WalletDestination (OS3.0)
- **Modello**: `App\Models\WalletDestination`
- **Campi**:
  - `wallet_id`: Riferimento al wallet
  - `payment_type`: Enum (STRIPE|PAYPAL|BANK_TRANSFER|EGILI)
  - `destination_value`: Account ID (crittografato per BANK_TRANSFER)
  - `is_verified`: Stato verifica (bool)
  - `is_primary`: Destinazione primaria (bool)
  - `metadata`: JSON metadata (es. BIC, holder per bank transfer)
- **Vantaggi**: Multi-wallet support, crittografia nativa, estensibilità

### Legacy Architecture
- **User model fields**: `stripe_account_id` (mantenuto per backward compatibility)
- **Fallback**: Se WalletDestination non esiste, sistema usa campi legacy
- **Deprecation path**: Campi User model saranno rimossi dopo migrazione completa

## Controller: PaymentSettingsController

**File**: `app/Http/Controllers/PaymentSettingsController.php` (592 righe)

### Endpoint Principali

#### 1. GET `/settings/payments`
Visualizza pagina impostazioni pagamento utente.

**Accesso**: Solo utenti `isSeller()` (creators + collectors abilitati per rebind)

**Metodo**: `index()`

**Ritorna**:
- Lista metodi disponibili (`AVAILABLE_METHODS`)
- Metodi configurati utente (`$user->paymentMethods()`)
- Stato connessione Stripe (`stripe_account_id`)

#### 2. POST `/settings/payments/{method}/toggle`
Abilita/disabilita metodo di pagamento.

**Metodo**: `toggle(Request $request, string $method)`

**Validazione**:
- Utente deve essere `isSeller()`
- Metodo deve esistere in `AVAILABLE_METHODS`

**Comportamento**:
- Crea record `UserPaymentMethod` se non esiste
- Inverte stato `is_enabled`
- Log GDPR audit (`payment_method_toggled`)

**Response JSON**:
```json
{
  "success": true,
  "is_enabled": true,
  "message": "Stripe abilitato"
}
```

#### 3. POST `/settings/payments/{method}/set-default`
Imposta metodo come predefinito.

**Metodo**: `setDefault(Request $request, string $method)`

**Validazione**:
- Metodo deve essere già abilitato (`is_enabled = true`)

**Comportamento**:
- Rimuove `is_default` da altri metodi
- Imposta `is_default = true` per il metodo selezionato
- Log GDPR audit

#### 4. POST `/settings/payments/bank-transfer/config`
Configura bonifico bancario.

**Metodo**: `updateBankConfig(Request $request)`

**Validazione**:
```php
[
    'iban' => ['required', 'string', 'min:15', 'max:34',
               'regex:/^[A-Z]{2}[0-9]{2}[A-Z0-9]{4,30}$/'],
    'bic' => ['nullable', 'string', 'max:11',
              'regex:/^[A-Z]{6}[A-Z0-9]{2}([A-Z0-9]{3})?$/'],
    'holder' => ['required', 'string', 'max:100'],
]
```

**Comportamento**:
- Recupera `primaryWallet` utente
- Crea/aggiorna `WalletDestination` per `BANK_TRANSFER`
- IBAN normalizzato (uppercase, spazi rimossi) e crittografato
- `is_verified = false` (verifica manuale richiesta)
- GDPR audit log: categoria `WALLET_MANAGEMENT`

**Response**:
```json
{
  "success": true,
  "message": "Dettagli bancari salvati"
}
```

#### 5. POST `/settings/payments/stripe/config`
Configura Stripe Connect manualmente.

**Metodo**: `updateStripeConfig(Request $request)`

**Validazione**:
```php
[
    'stripe_account_id' => ['required', 'string', 'starts_with:acct_', 'max:255']
]
```

**Comportamento**:
- Crea/aggiorna `WalletDestination` per `STRIPE`
- Aggiorna anche `User->stripe_account_id` (legacy, deprecated)
- `is_verified = true` (trusted manual entry)
- GDPR audit log: `payment_stripe_connected_manually`

#### 6. GET `/api/payments/available`
API endpoint per recuperare metodi disponibili.

**Metodo**: `getAvailable()`

**Autenticazione**: Richiesta (Auth::user())

**Response**:
```json
{
  "success": true,
  "methods": {
    "stripe": {
      "name": "Stripe",
      "description": "Accept card payments via Stripe Connect",
      "is_enabled": true,
      "is_default": false,
      "has_config": true
    },
    "egili": {...},
    "bank_transfer": {...}
  }
}
```

#### 7. GET `/settings/payments/modal`
Contenuto modale impostazioni pagamento.

**Metodo**: `modal()`

**Ritorna**: View `settings.payments.modal-content` con:
- User payment methods (legacy)
- WalletDestinations (nuovo)
- Stripe account ID risolto
- Bank details decriptati (IBAN, BIC, holder)

### Metodi per Collection

#### 8. GET `/collections/{collection}/payments`
Impostazioni pagamento specifiche per collection.

**Metodo**: `indexCollection(Collection $collection)`

**Authorization**: Policy `update` sulla collection

#### 9. POST `/collections/{collection}/payments/{method}/toggle`
Toggle metodo per collection.

**Metodo**: `toggleCollection(Request $request, Collection $collection, string $method)`

#### 10. POST `/collections/{collection}/payments/{method}/set-default`
Set default per collection.

**Metodo**: `setDefaultCollection(Request $request, Collection $collection, string $method)`

#### 11. POST `/collections/{collection}/payments/bank-transfer/config`
Configura bank transfer per collection.

**Metodo**: `updateBankConfigCollection(Request $request, Collection $collection)`

**Differenza da user config**: Usa `CollectionPaymentMethod` invece di WalletDestination

## Service: MerchantAccountResolver

**File**: `app/Services/Payment/MerchantAccountResolver.php` (539 righe)

### Scopo
Risolvere configurazioni PSP account merchant per creators nel rispetto di MiCA (no platform custody).

### Metodi Principali

#### 1. `resolveForEgiAndProvider(Egi $egi, string $provider): array`

Risolve merchant account per un EGI specifico e provider.

**Input**:
- `$egi`: Modello EGI (NFT/Environmental Good)
- `$provider`: 'stripe' | 'paypal'

**Output**:
```php
[
    'provider' => 'stripe',
    'collection_id' => 123,
    'wallet_id' => 456,
    'stripe_account_id' => 'acct_XXXXX', // se Stripe
    'paypal_merchant_id' => 'MERCHANT_ID' // se PayPal
]
```

**Logica**:
1. Recupera collection dell'EGI
2. Raccoglie wallets candidati (collection + owner wallets)
3. Per Stripe: cerca wallet con:
   - `WalletDestination` STRIPE con `destination_value` popolato, oppure
   - Legacy: `wallet->user->stripe_account_id` o `wallet->stripe_account_id`
4. Per PayPal: cerca wallet con:
   - `WalletDestination` PAYPAL, oppure
   - Legacy: `wallet->paypal_merchant_id`
5. Se nessun wallet trovato: `throw MerchantAccountNotConfiguredException`

**Priorità**: WalletDestination > User model > Wallet model (fallback legacy)

#### 2. `resolveForUserAndProvider(User $user, string $provider): array`

Risolve merchant account per un utente specifico (es. Rebind).

**Input**:
- `$user`: User model
- `$provider`: 'stripe' | 'paypal'

**Output**:
```php
[
    'provider' => 'stripe',
    'wallet_id' => 456,
    'stripe_account_id' => 'acct_XXXXX'
]
```

**Logica**: Simile a `resolveForEgiAndProvider` ma usa solo user wallets

#### 3. `validateAllCollectionWallets(Egi $egi, string $provider): array`

Valida **TUTTI** i wallets di una collection per split payment.

**Scopo**: Garantire che TUTTI i wallet configurati per un provider possano ricevere pagamenti (necessario per split payment multi-beneficiary).

**Input**:
- `$egi`: EGI model
- `$provider`: 'stripe' | 'paypal'

**Output**:
```php
[
    'provider' => 'stripe',
    'all_valid' => true,              // true SOLO se TUTTI i wallets sono validi
    'can_accept_payments' => true,    // true se almeno UN wallet è valido
    'total_wallets' => 3,
    'valid_wallets' => 3,
    'invalid_wallets' => [],          // Array di wallet non validi con errori
    'provider_enabled' => true,       // Provider abilitato in .env
    'errors' => []                    // Array errori unici
]
```

**Logica**:
1. Verifica provider abilitato in `.env` (`config('algorand.payments.stripe_enabled')`)
2. Raccoglie wallets collection
3. Filtra solo wallets con WalletDestination per provider specificato
4. Per ogni wallet: chiama `validateSingleWallet()`
5. Ritorna `all_valid = true` SOLO se TUTTI i wallet passano validazione

**Use case**: Mint con royalties split tra creator + platform + collaborators

#### 4. `validateUserWallets(User $user, string $provider): array`

Valida TUTTI i wallets di un utente.

**Output**: Stesso formato di `validateAllCollectionWallets`

#### 5. `validateSingleWallet(Wallet $wallet, string $provider): array` (private)

Valida singolo wallet chiamando API del provider.

**Per Stripe**:
1. Recupera `stripe_account_id` da WalletDestination o legacy fields
2. Inizializza `\Stripe\StripeClient` con secret key da config
3. Chiama `$stripeClient->accounts->retrieve($accountId)`
4. Verifica `$account->charges_enabled === true`

**Output**:
```php
[
    'valid' => true,
    'account_id' => 'acct_XXXXX',
    'error' => null // o 'missing_account_id'|'charges_disabled'|'verification_failed'
]
```

**Per PayPal**: ⚠️ Non implementato, ritorna `['valid' => false, 'error' => 'paypal_not_implemented']`

#### 6. `collectCandidateWallets(Collection $collection)` (private)

Raccoglie wallets candidati per una collection.

**Logica**:
- Collection wallets
- Owner wallets
- Merge + deduplicazione per ID
- Sort per `updated_at` DESC

#### 7. `collectUserWallets(User $user)` (private)

Raccoglie tutti i wallets di un utente.

## Conformità MiCA e Regolamenti

### No Platform Custody
Il sistema **NON** custodisce fondi degli utenti. Tutti i pagamenti vanno direttamente ai merchant account dei creators presso i PSP (Stripe, PayPal).

### GDPR Compliance
- **Audit Logging**: Ogni modifica ai metodi di pagamento viene registrata in `gdpr_activity_logs`
- **Categorie GDPR**:
  - `WALLET_MANAGEMENT`: Toggle, set default
  - `FINANCIAL`: Bank details update
- **Crittografia**: IBAN automaticamente crittografato in `WalletDestination`
- **Data Minimization**: Solo dati strettamente necessari per transazioni

### Accesso Limitato
- **isSeller() check**: Solo users con ruoli Creator/Collector possono configurare payment methods
- **Collection Authorization**: Policy `update` richiesta per modifiche a collection payment methods

## Workflow Tipici

### Setup Stripe Connect (Creator)
1. User naviga a `/settings/payments`
2. Controller verifica `isSeller()` → OK per creators
3. User inserisce `stripe_account_id` manualmente o tramite OAuth flow
4. POST `/settings/payments/stripe/config` con `acct_XXXXX`
5. Sistema:
   - Crea `WalletDestination` (payment_type=STRIPE, destination_value=acct_XXXXX)
   - Aggiorna `User->stripe_account_id` (legacy)
   - Log GDPR audit
6. User abilita Stripe: POST `/settings/payments/stripe/toggle`

### Setup Bonifico Bancario
1. User apre modal pagamenti
2. Inserisce IBAN (es. `IT60X0542811101000000123456`), BIC, holder
3. POST `/settings/payments/bank-transfer/config`
4. Sistema:
   - Normalizza IBAN (uppercase, no spazi)
   - Valida con regex SEPA
   - Crea `WalletDestination` con `destination_value` crittografato
   - Salva BIC/holder in `metadata`
   - `is_verified = false`
5. User abilita bank transfer: POST `/settings/payments/bank_transfer/toggle`

### Processo Mint con Split Payment
1. Buyer acquista EGI
2. `MintController` chiama `MerchantAccountResolver->validateAllCollectionWallets($egi, 'stripe')`
3. Resolver:
   - Raccoglie tutti wallets collection (creator + platform + royalty recipients)
   - Valida OGNI wallet con API Stripe
   - Verifica `charges_enabled` per ognuno
   - Ritorna `all_valid = true` se TUTTI passano
4. Se `all_valid = true`: procede con Stripe PaymentIntent e split transfer
5. Se `all_valid = false`: blocca pagamento, ritorna errore con dettagli wallet invalidi

### Rebind Payment (Collector)
1. Collector vende NFT su mercato secondario
2. `RebindController` chiama `MerchantAccountResolver->resolveForUserAndProvider($collector, 'stripe')`
3. Resolver:
   - Recupera collector wallets
   - Trova primo wallet con Stripe configurato
   - Ritorna `stripe_account_id`
4. Sistema crea Stripe transfer al collector account

## Modelli Database

### UserPaymentMethod
- `user_id`: FK users
- `method`: enum (stripe|egili|bank_transfer|paypal)
- `is_enabled`: bool
- `is_default`: bool
- `config`: JSON (es. IBAN, BIC per bank_transfer)

### CollectionPaymentMethod
- `collection_id`: FK collections
- `method`: enum
- `is_enabled`: bool
- `is_default`: bool
- `config`: JSON

### WalletDestination (Nuovo OS3.0)
- `id`: bigint PK
- `wallet_id`: FK wallets
- `payment_type`: enum (STRIPE|PAYPAL|BANK_TRANSFER|EGILI)
- `destination_value`: encrypted string (account ID, IBAN)
- `is_verified`: bool
- `is_primary`: bool
- `metadata`: JSON
- `created_at`, `updated_at`

## Configurazione (.env)

```bash
# Stripe
STRIPE_KEY=pk_live_XXXXX
STRIPE_SECRET=sk_live_XXXXX
ALGORAND_PAYMENTS_STRIPE_ENABLED=true

# PayPal
PAYPAL_CLIENT_ID=XXXXX
PAYPAL_CLIENT_SECRET=XXXXX
ALGORAND_PAYMENTS_PAYPAL_ENABLED=false
```

## Errori Comuni

### `MerchantAccountNotConfiguredException`
**Causa**: Wallet non ha account PSP configurato

**Soluzione**: User deve configurare Stripe/PayPal/Bank Transfer in `/settings/payments`

### `charges_disabled` (Stripe validation)
**Causa**: Account Stripe non ha completato onboarding o è stato sospeso

**Soluzione**: User deve completare Stripe Connect onboarding

### `payment.settings_restricted_to_sellers`
**Causa**: User non ha ruolo Creator o Collector (rebind-enabled)

**Soluzione**: Solo sellers possono accedere alle impostazioni pagamenti

### Validation Error: IBAN format
**Causa**: IBAN non rispetta regex SEPA standard

**Soluzione**: Verificare formato IBAN (XX00XXXX..., 15-34 caratteri)

## Best Practices

1. **Dual Architecture**: Sempre popolare WalletDestination + legacy fields durante transizione
2. **Validation**: Sempre chiamare `validateAllCollectionWallets()` prima di split payment
3. **GDPR**: Usare `AuditLogService` per ogni modifica finanziaria
4. **Error Handling**: Catch `MerchantAccountNotConfiguredException` e guidare user a setup
5. **Security**: IBAN sempre crittografato, `stripe_account_id` in `$hidden`
6. **Backward Compatibility**: Fallback a legacy fields se WalletDestination mancante

## Riferimenti Codice

- **PaymentSettingsController**: `app/Http/Controllers/PaymentSettingsController.php:24`
- **MerchantAccountResolver**: `app/Services/Payment/MerchantAccountResolver.php:18`
- **WalletDestination Model**: `app/Models/WalletDestination.php`
- **User Payment Methods**: `app/Models/UserPaymentMethod.php`
- **Payment Enums**: `app/Enums/Payment/PaymentTypeEnum.php`
- **Routes**: `routes/web.php` (payment settings routes)

## Prossimi Sviluppi

- [ ] Completare validazione PayPal API
- [ ] Implementare OAuth flow Stripe Connect (attualmente manuale)
- [ ] Migrare tutti record da legacy User fields a WalletDestination
- [ ] Aggiungere supporto per bank transfer verification API
- [ ] Dashboard merchant con analytics pagamenti ricevuti
