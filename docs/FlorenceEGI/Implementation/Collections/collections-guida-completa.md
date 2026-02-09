# Collections FlorenceEGI - Guida Completa

## Introduzione

Le **Collections** sono il contenitore principale per organizzare EGI (Environmental Goods Inventory) su FlorenceEGI. Ogni collection rappresenta una serie di NFT creati da un artista/creator e può contenere configurazioni commerciali, wallet distribuiti, membri del team e progetti EPP associati.

## Caratteristiche Principali

- **Organizzazione EGI**: Raggruppamento logico di NFT per serie/progetto
- **Commerce Setup**: Configurazione vendita con delivery policy, impact mode, metodi pagamento
- **Multi-Wallet**: Supporto split payment tra creator, platform, royalty recipients
- **Team Management**: Membri collection con ruoli e permessi granulari (Spatie permissions)
- **EPP Integration**: Supporto progetti ambientali con donazioni configurabili
- **Profili Collection**: Contributor (standard, subscriber) vs Normal (company)
- **Soft Delete**: Cancellazione logica per recovery dati
- **Media Management**: Gestione media con Spatie MediaLibrary (banner, card, avatar)

## Modello Database: Collection

**File**: `app/Models/Collection.php`

### Campi Principali

#### Identificazione
- `id`: Bigint PK
- `creator_id`: FK users (creatore originale)
- `owner_id`: FK users (proprietario corrente, può differire da creator)
- `collection_name`: String(150), required
- `description`: Text(10000), nullable

#### Configurazione
- `type`: String(50), nullable (es. "Art", "Music", "Photography")
- `status`: String (draft|published)
- `is_default`: Boolean (collection predefinita utente)
- `is_published`: Boolean (visibilità pubblica)
- `position`: Integer (ordinamento)

#### Commerce (P0)
- `commercial_status`: Enum (`CommercialStatusEnum`)
  - `NOT_CONFIGURED`: Default, commerce non setup
  - `CONFIGURED`: Setup completato ma non abilitato
  - `COMMERCIAL_ENABLED`: Abilitato e operativo
- `delivery_policy`: Enum (`DeliveryPolicyEnum`)
  - `DIGITAL_ONLY`: Solo NFT digitali
  - `PHYSICAL_ONLY`: Solo beni fisici
  - `HYBRID`: NFT + bene fisico opzionale
- `impact_mode`: Enum (`ImpactModeEnum`)
  - `EPP`: Environmental Protection Project donation
  - `SUBSCRIPTION`: Subscription-based royalty exemption
  - `NONE`: Nessun impact mode

#### EPP (Environmental Protection)
- `epp_project_id`: FK epp_projects, nullable
- `epp_donation_percentage`: Decimal(5,2), nullable (0-100%)
- `is_epp_voluntary`: Boolean
  - `true`: EPP volontario (Company users)
  - `false`: EPP obbligatorio (altri user types)

#### Profili Collection (Nuova Architettura)
- `profile_type`: Enum (contributor|normal)
  - `contributor`: Creator/Collector standard (royalty obbligatorie, EPP 20% se standard mode)
  - `normal`: Company users (no royalty obbligatorie, EPP volontario)
- `royalty_mode`: Enum (standard|subscriber)
  - `standard`: Royalty + EPP standard (20%)
  - `subscriber`: EPP esente (0%), subscription-based

#### Media
- `image_banner`: JSON (via `EGIImageCast`)
- `image_card`: JSON
- `image_avatar`: JSON
- `image_egi`: JSON

#### Altri Campi
- `url_collection_site`: String(255), nullable (sito esterno collection)
- `floor_price`: Decimal, nullable (prezzo minimo suggerito)
- `featured_in_guest`: Boolean (featured per utenti non autenticati)
- `featured_position`: Integer
- `EGI_number`: Integer (numero EGI nella collection)
- `EGI_asset_roles`: JSON (ruoli asset Algorand)
- `path_image_to_ipfs`: String (path IPFS per metadata)
- `url_image_ipfs`: String (URL IPFS)
- `EGI_asset_id`: Bigint (Algorand ASA ID)
- `subscription_plan_id`: FK subscription_plans (se impact_mode=SUBSCRIPTION)
- `metadata`: JSON (metadata PA/Enterprise)

### Relationships

#### 1. `creator()`
Creatore originale della collection.
```php
return $this->belongsTo(User::class, 'creator_id');
```

#### 2. `owner()`
Proprietario corrente (può differire da creator in caso di trasferimento).
```php
return $this->belongsTo(User::class, 'owner_id');
```

#### 3. `egis()`
Tutti gli EGI della collection (inclusi cloni rebind).
```php
return $this->hasMany(Egi::class);
```

#### 4. `originalEgis()`
Solo EGI originali (esclude cloni con `parent_id != null`).
```php
return $this->hasMany(Egi::class)->whereNull('parent_id');
```

**Accessor**: `getOriginalEgisCountAttribute()` - Conta EGI originali (usa `withCount` se disponibile)

#### 5. `users()`
Team members via tabella pivot `collection_user`.
```php
return $this->belongsToMany(User::class, 'collection_user')
    ->withPivot('role', 'is_owner')
    ->withTimestamps();
```

**Pivot fields**:
- `role`: Ruolo Spatie (es. "collection_admin", "collection_editor")
- `is_owner`: Boolean (ownership flag)

#### 6. `wallets()`
Wallets associati per split payment.
```php
return $this->hasMany(Wallet::class);
```

#### 7. `eppProject()`
Progetto ambientale selezionato.
```php
return $this->belongsTo(EppProject::class, 'epp_project_id');
```

#### 8. `paymentMethods()`
Metodi di pagamento specifici collection (via polymorphic).
```php
return $this->morphMany(PaymentMethod::class, 'paymentable');
```

### Metodi Principali

#### `isPublished(): bool`
Verifica se collection è pubblicata.
```php
return $this->status === 'published';
```

#### `canBePublished(): bool`
Verifica se collection può essere pubblicata.

**Logica**: Controlla assenza di proposte wallet pending.
```php
$hasPendingWalletProposals = NotificationPayloadWallet::whereHas('walletModel', ...)
    ->where('status', 'LIKE', '%pending%')
    ->exists();
return !$hasPendingWalletProposals;
```

**Uso**: Chiamato prima di pubblicare per bloccare se setup non completo.

#### `userHasPermission($user, string $permission): bool`
Verifica se un utente ha un permesso specifico nella collection.

**Parametri**:
- `$user`: User model o user ID
- `$permission`: Nome permesso Spatie (es. "edit_egis", "manage_wallets")

**Logica**:
1. Recupera relazione user in collection (usa `relationLoaded` per evitare N+1)
2. Estrae `role` dal pivot
3. Verifica con Spatie: `Role::where('name', $role)->first()->hasPermissionTo($permission)`

**Esempio**:
```php
if ($collection->userHasPermission($user, 'edit_egis')) {
    // User può modificare EGI in questa collection
}
```

#### `isCreatorCompany(): bool`
Verifica se il creator è Company user type.
```php
return $this->creator?->usertype === \App\Enums\User\MerchantUserTypeEnum::COMPANY->value;
```

#### `hasVoluntaryEpp(): bool`
Verifica se EPP è volontario (company users).
```php
return $this->is_epp_voluntary === true;
```

#### `hasEppDonation(): bool`
Verifica se ha donazione EPP configurata.
```php
return $this->epp_project_id !== null && $this->epp_donation_percentage > 0;
```

#### `getEffectiveEppPercentage(): float`
Calcola percentuale EPP effettiva per split payment.

**Logica Nuova Architettura**:
1. **Profilo NORMAL** (Company): Ritorna `epp_donation_percentage` (di solito 0)
2. **Profilo CONTRIBUTOR**:
   - Mode `subscriber`: Ritorna 0% (EPP esente)
   - Mode `standard`: Ritorna 20% (EPP obbligatorio)

**Esempio**:
```php
$eppPercentage = $collection->getEffectiveEppPercentage();
// Contributor standard: 20.0
// Contributor subscriber: 0.0
// Company normal: variabile (es. 5.0)
```

**Uso**: Chiamato durante calcolo fee split in `MintController`.

## Controller: CollectionCrudController

**File**: `app/Http/Controllers/CollectionCrudController.php`

Gestisce operazioni CRUD metadata collection con security e GDPR compliance.

### Endpoint: POST `/collections/{collection}` (Update)

**Metodo**: `update(Request $request, Collection $collection)`

#### Authorization
1. **Authentication**: `$request->user()` deve esistere
2. **Permission**: Utente deve avere:
   - `update_collection` (global permission), OPPURE
   - `edit_own_collection` E `creator_id == $user->id`

**Response unauthorized**: 401 (unauthenticated) o 403 (forbidden)

#### Validazione
```php
[
    'collection_name'     => ['required', 'string', 'max:150'],
    'description'         => ['nullable', 'string', 'max:10000'],
    'url_collection_site' => ['nullable', 'url', 'max:255'],
    'type'                => ['nullable', 'string', 'max:50'],
    'floor_price'         => ['nullable', 'numeric', 'min:0'],
    'is_published'        => ['nullable', 'boolean'],
]
```

**Validation error response**: 422 con `errors` array

#### Business Rules

##### 1. Publishing Guard
Se `is_published = true` nel payload:
- Chiama `$collection->canBePublished()`
- Se `false`: blocca con 422 error "Publishing conditions not met"
- Rationale: Non pubblicare se ci sono wallet proposals pending

##### 2. Default EPP Assignment
Se collection non ha `epp_id` E creator non è Company:
- Assegna automaticamente `epp_id = 2` (default platform EPP project)
- Skip per Company users (EPP volontario per loro)

**Codice**:
```php
$isCompanyUser = $collection->is_epp_voluntary ||
                  $collection->creator?->usertype === MerchantUserTypeEnum::COMPANY->value;
if (!$isCompanyUser && is_null($collection->epp_id)) {
    $collection->epp_id = 2;
}
```

#### GDPR Audit Logging
Ogni update viene loggato con:
- **Action**: `collection_metadata_updated`
- **Category**: `GdprActivityCategory::CONTENT_MANAGEMENT`
- **Data**: original_state, updated_fields, epp_id_applied

#### Response Success
```json
{
  "message": "Collection updated successfully",
  "collection": {
    "id": 123,
    "collection_name": "My Art Collection",
    "is_published": true,
    ...
  }
}
```

### Endpoint: DELETE `/collections/{collection}` (Delete)

**Metodo**: `destroy(Request $request, Collection $collection)`

**Tipo**: Soft Delete (usa `SoftDeletes` trait)

#### Authorization
- Utente deve avere `delete_collection` OPPURE
- `delete_own_collection` E essere il creator

#### Comportamento
1. Chiama `$collection->delete()` (soft delete, setta `deleted_at`)
2. Collection diventa invisibile ma recuperabile
3. EGI associati NON sono cancellati (relazione preservata)

#### GDPR Audit
- **Action**: `collection_deleted`
- **Category**: `CONTENT_MANAGEMENT`
- **Data**: collection_id, deleted_at

#### Response
```json
{
  "message": "Collection deleted successfully"
}
```

**Note**: Per hard delete usare `$collection->forceDelete()` (admin only)

## Commerce Wizard

**Controller**: `app/Http/Controllers/CollectionCommerceWizardController.php`
**Service**: `app/Services/Commerce/CollectionCommercialService.php`

### Workflow Commerce Setup

#### Step 1: Apertura Wizard

**Route**: GET `/collections/{collection}/commerce/wizard`

**Metodo**: `show(Collection $collection)`

**Authorization**: Policy `update` sulla collection

**Response**: View `merchant.collection.commerce_wizard` con:
- `$collection`: Modello collection
- `$paymentMethods`: `$collection->getEffectivePaymentMethods()`
- `$eppProjects`: `EppProject::active()->get()`

#### Step 2: Configurazione Settings

**Route**: POST `/collections/{collection}/commerce/wizard`

**Metodo**: `update(Collection $collection, Request $request)`

**Validazione** (in `CollectionCommercialService->updateSettings()`):
```php
[
    'delivery_policy' => ['required', 'string'],
    'impact_mode' => ['required', 'string'],
    'epp_project_id' => ['required_if:impact_mode,EPP', 'nullable', 'integer'],
    'subscription_plan_id' => ['required_if:impact_mode,SUBSCRIPTION', 'nullable', 'integer'],
]
```

**Comportamento**:
1. Valida input
2. Aggiorna collection:
   - `delivery_policy`
   - `impact_mode`
   - `epp_project_id` (se impact_mode=EPP)
   - `subscription_plan_id` (se impact_mode=SUBSCRIPTION)
3. Imposta `commercial_status`:
   - Se già `COMMERCIAL_ENABLED`: mantiene ENABLED
   - Altrimenti: setta a `CONFIGURED`

**Response Success**: Redirect a wizard con `success` message

#### Step 3: Abilitazione Commerce

**Route**: POST `/collections/{collection}/commerce/enable`

**Metodo**: `enable(Collection $collection)`

**Pre-validazione** (in `CollectionCommercialService->validateSetup()`):

1. **Delivery Policy**: Deve essere configurato
2. **Impact Mode**: Deve essere configurato
3. **EPP Project**: Se `impact_mode = EPP`, deve avere `epp_project_id`
4. **Subscription Plan**: Se `impact_mode = SUBSCRIPTION`, deve avere `subscription_plan_id`
5. **Payment Methods**: Almeno UN metodo pagamento abilitato (`getEffectivePaymentMethods()->isEmpty() === false`)

**Se validazione fallisce**: Lancia `ValidationException` con errori specifici

**Se validazione passa**:
- Aggiorna `commercial_status = COMMERCIAL_ENABLED`
- Log: `COMMERCE_ENABLE_SUCCESS`

**Response Success**: Redirect a `/collections/{collection}` con success message

### Service: CollectionCommercialService

#### `validateSetup(Collection $collection): bool`

Validazione completa setup commerce.

**Checks**:
1. `delivery_policy` configurato
2. `impact_mode` configurato
3. Se EPP mode: `epp_project_id` presente
4. Se SUBSCRIPTION mode: `subscription_plan_id` presente
5. Almeno un payment method abilitato

**Throws**: `ValidationException` se fallisce

#### `enableCommercial(Collection $collection): Collection`

Abilita commercial status dopo validazione.

**Workflow**:
1. Log start
2. Chiama `validateSetup()`
3. Update `commercial_status = COMMERCIAL_ENABLED`
4. Log success
5. Ritorna collection aggiornata

**Error Handling**: Usa `ErrorManager->handle('COMMERCE_ENABLE_FAILED', ...)` e rilancia exception

#### `updateSettings(Collection $collection, array $data): Collection`

Aggiorna settings commerce wizard.

**Input**:
- `delivery_policy`: String (required)
- `impact_mode`: String (required)
- `epp_project_id`: Int (required se impact_mode=EPP)
- `subscription_plan_id`: Int (required se impact_mode=SUBSCRIPTION)

**Logica Status**:
- Se già `COMMERCIAL_ENABLED`: mantiene ENABLED
- Altrimenti: setta `CONFIGURED`

**Rationale**: Permette modifica settings anche dopo enablement senza disabilitare

## Enums Commerce

### CommercialStatusEnum

**File**: `app/Enums/Commerce/CommercialStatusEnum.php`

**Valori**:
- `NOT_CONFIGURED`: Default, wizard non completato
- `CONFIGURED`: Setup completato, in attesa enablement
- `COMMERCIAL_ENABLED`: Abilitato per vendita

**Utilizzo**: Filtrare collections vendibili in marketplace

### DeliveryPolicyEnum

**Valori**:
- `DIGITAL_ONLY`: Solo NFT digitale (no spedizione fisica)
- `PHYSICAL_ONLY`: Solo bene fisico (NFT come certificate ownership)
- `HYBRID`: NFT + bene fisico opzionale (buyer sceglie)

**Impatto**:
- DIGITAL_ONLY: Skip shipping form al checkout
- PHYSICAL_ONLY/HYBRID: Richiede indirizzo spedizione

### ImpactModeEnum

**Valori**:
- `EPP`: Environmental Protection Project (donazione % a progetto ambientale)
- `SUBSCRIPTION`: Subscriber-based (royalty exemption via subscription)
- `NONE`: Nessun impact mode

**Impatto su Fee Split**:
- EPP: Aggiunge split a wallet EPP project (20% per contributors standard)
- SUBSCRIPTION: Subscriber creators ottengono EPP exemption (0% EPP)
- NONE: Solo royalty standard (no EPP)

## Profili Collection e Royalty System

### Profile Type: Contributor vs Normal

#### CONTRIBUTOR (Default)
- **Users**: Creator, Collector, PA Entity
- **Royalty**: Obbligatorie (% configurata)
- **EPP**:
  - Mode `standard`: 20% obbligatorio
  - Mode `subscriber`: 0% (esente se subscribed)

#### NORMAL
- **Users**: Company
- **Royalty**: Opzionali (0% default)
- **EPP**: Volontario (`epp_donation_percentage` configurabile, default 0%)

### Royalty Mode: Standard vs Subscriber

#### STANDARD
- Contributor creators pagano:
  - Royalty platform: % configurata
  - EPP: 20% fisso

#### SUBSCRIBER
- Contributor creators con subscription attiva ottengono:
  - EPP exemption: 0% (vs 20%)
  - Royalty platform: rimangono invariate
- **Requisito**: `subscription_plan_id` deve essere configurato

**Check subscription**:
```php
if ($collection->royalty_mode === 'subscriber' && $collection->subscription_plan_id) {
    $eppPercentage = 0; // EPP esente
}
```

## Team Management e Permessi

### Collection Users (Pivot)

**Tabella**: `collection_user`

**Campi**:
- `collection_id`: FK collections
- `user_id`: FK users
- `role`: String (nome ruolo Spatie, es. "collection_admin")
- `is_owner`: Boolean
- `created_at`, `updated_at`

### Ruoli Predefiniti

#### 1. Collection Owner
- **Permessi**: Tutti (full control)
- **Automatico**: Creator diventa owner alla creazione

#### 2. Collection Admin
- **Permessi**: Gestione EGI, wallet, payment methods, team members (eccetto delete collection)

#### 3. Collection Editor
- **Permessi**: Modifica EGI metadata, upload media (no wallet management)

#### 4. Collection Viewer
- **Permessi**: Solo visualizzazione (read-only)

### Assegnazione Permessi

**Via Spatie Permission System**:
```php
$role = Role::findByName('collection_admin');
$role->givePermissionTo(['edit_egis', 'manage_wallets', 'invite_members']);
```

**Check Permission nella Collection**:
```php
if ($collection->userHasPermission($user, 'edit_egis')) {
    // Autorizzato
}
```

## Workflow Tipici

### Creazione Collection

1. User (Creator) clicca "Crea Collection"
2. POST `/collections` con `collection_name`, `description`
3. Sistema:
   - Crea record Collection con `creator_id` e `owner_id` = user ID
   - `status = 'draft'`, `is_published = false`
   - `commercial_status = NOT_CONFIGURED`
   - Assegna `epp_id = 2` se non Company user
4. Redirect a `/collections/{collection}/edit`

### Setup Commerce

1. Creator naviga a `/collections/{collection}/commerce/wizard`
2. **Delivery Policy**: Seleziona DIGITAL_ONLY / PHYSICAL_ONLY / HYBRID
3. **Impact Mode**: Seleziona EPP / SUBSCRIPTION / NONE
4. Se EPP: Seleziona EppProject da dropdown
5. Se SUBSCRIPTION: Seleziona SubscriptionPlan
6. Configura Payment Methods (via `/settings/payments`)
7. POST `/collections/{collection}/commerce/wizard` → `commercial_status = CONFIGURED`
8. Click "Abilita Commerce"
9. POST `/collections/{collection}/commerce/enable`
10. Sistema valida setup completo → `commercial_status = COMMERCIAL_ENABLED`

### Pubblicazione Collection

1. Creator completa setup commerce
2. Carica almeno 1 EGI
3. Naviga a `/collections/{collection}/edit`
4. Toggle `is_published = true`
5. Sistema:
   - Chiama `canBePublished()` (verifica no wallet proposals pending)
   - Se OK: aggiorna `is_published = true`, `status = 'published'`
   - Se KO: Blocca con errore "Publishing conditions not met"
6. Collection diventa visibile in marketplace

### Aggiunta Team Member

1. Owner naviga a `/collections/{collection}/team`
2. Clicca "Invita Membro"
3. Inserisce email + seleziona ruolo (Admin/Editor/Viewer)
4. POST `/collections/{collection}/team/invite`
5. Sistema:
   - Crea record in `collection_user` pivot
   - Invia notifica email all'invitee
   - Assigned role con Spatie permissions
6. Invitee accetta invite → diventa collection team member

## EPP Integration

### EPP Project Selection

**Tabella**: `epp_projects`

**Campi**:
- `id`: Bigint PK
- `name`: String (nome progetto, es. "Amazon Rainforest Protection")
- `description`: Text
- `image`: String (URL immagine progetto)
- `is_active`: Boolean
- `wallet_id`: FK wallets (wallet destinazione donazioni)
- `created_at`, `updated_at`

**Scope**: `active()`
```php
EppProject::active()->get() // Solo progetti attivi
```

### Configurazione EPP su Collection

#### Contributor Standard (Mandatory)
```php
$collection->profile_type = 'contributor';
$collection->royalty_mode = 'standard';
$collection->impact_mode = ImpactModeEnum::EPP;
$collection->epp_project_id = 3; // Selezione progetto
// EPP automatico: 20% fisso
```

#### Contributor Subscriber (Exempt)
```php
$collection->profile_type = 'contributor';
$collection->royalty_mode = 'subscriber';
$collection->subscription_plan_id = 5;
$collection->impact_mode = ImpactModeEnum::SUBSCRIPTION;
// EPP esente: 0%
```

#### Company (Voluntary)
```php
$collection->profile_type = 'normal';
$collection->is_epp_voluntary = true;
$collection->impact_mode = ImpactModeEnum::EPP;
$collection->epp_project_id = 3;
$collection->epp_donation_percentage = 5.0; // Volontario, configurabile
// EPP: 5% (o 0% se non configurato)
```

### EPP Payment Distribution

Nel `MintController` al momento del pagamento:
```php
$eppPercentage = $collection->getEffectiveEppPercentage();
if ($eppPercentage > 0 && $collection->epp_project_id) {
    $eppProject = $collection->eppProject;
    $eppWallet = $eppProject->wallet;
    $eppAmount = $totalPrice * ($eppPercentage / 100);

    // Split payment a EPP wallet
    $transfers[] = [
        'destination' => $eppWallet->stripe_account_id,
        'amount' => (int)($eppAmount * 100), // cents
        'metadata' => ['type' => 'epp_donation', 'project_id' => $eppProject->id]
    ];
}
```

## Soft Delete e Recovery

### Soft Delete
```php
$collection->delete(); // Setta deleted_at
```

**Effetti**:
- Collection esclusa da query default (global scope)
- EGI associati rimangono (non cancellati)
- Wallets associati rimangono
- Team members rimangono in pivot

**Visibilità**: Solo admin con `withTrashed()` può vedere

### Recovery
```php
Collection::withTrashed()->find($id)->restore();
```

**Effetti**:
- Setta `deleted_at = null`
- Collection riappare in query normali
- Tutti i dati associati intatti

### Hard Delete (Force Delete)
```php
$collection->forceDelete();
```

**ATTENZIONE**: Cancellazione permanente. Usare solo per cleanup admin.

**Effetti**:
- Record eliminato fisicamente da DB
- Relazioni orfane (EGI rimangono ma `collection_id` = deleted ID)

## Policy: CollectionPolicy

**File**: `app/Policies/CollectionPolicy.php`

### Metodi Policy

#### `view(User $user, Collection $collection): bool`
- Tutti possono vedere published collections
- Creator/Owner possono vedere proprie draft collections
- Admin possono vedere tutte

#### `update(User $user, Collection $collection): bool`
- Creator può aggiornare se `edit_own_collection` permission
- Admin con `update_collection` possono aggiornare tutte
- Team members con `edit_collections` permission nella collection

#### `delete(User $user, Collection $collection): bool`
- Solo creator o admin con `delete_collection`

#### `publish(User $user, Collection $collection): bool`
- Creator può pubblicare proprie collections
- Admin possono pubblicare tutte
- Check aggiuntivo: `canBePublished()` deve ritornare `true`

## Media Collections (Spatie)

### Registrazione Media Collections

**Nel Model**:
```php
public function registerMediaCollections(): void {
    $this->addMediaCollection('banner')
        ->singleFile()
        ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);

    $this->addMediaCollection('card')
        ->singleFile();

    $this->addMediaCollection('avatar')
        ->singleFile();
}
```

### Upload Media

**Controller**: `CollectionBannerController`

```php
$collection->addMediaFromRequest('banner')
    ->toMediaCollection('banner');
```

**Conversions**:
- `banner`: 1920x600px
- `card`: 400x300px
- `avatar`: 200x200px (circle crop)

### Retrieval

```php
$bannerUrl = $collection->getFirstMediaUrl('banner');
$cardUrl = $collection->getFirstMediaUrl('card', 'thumb');
```

## Best Practices

### 1. Commerce Setup
- **Sempre validare** setup completo prima di `enableCommercial()`
- **Configurare EPP** prima di publishing (mandatory per contributors standard)
- **Testare payment methods** con Stripe test mode prima di production

### 2. Permissions
- **Usare Policy** per authorization, non controller logic
- **Assegnare ruoli** con least privilege principle
- **Team member invites**: sempre con email confirmation

### 3. EPP Configuration
- **Contributor standard**: EPP automatico 20%, non modificabile
- **Contributor subscriber**: Verificare `subscription_plan_id` valido e attivo
- **Company**: EPP volontario, ma se configurato deve essere >= 0% e <= 100%

### 4. Publishing
- **Check canBePublished()** prima di pubblicare
- **Wallet setup completo**: Tutti wallets split payment devono essere verified
- **Media**: Caricare banner e card PRIMA di publishing (UX)

### 5. Soft Delete
- **Preferire soft delete** a hard delete (data recovery)
- **Hard delete**: Solo per GDPR right-to-erasure requests
- **Before delete**: Notificare team members se collection è collaborativa

## Troubleshooting

### Errore: "Publishing conditions not met"
**Causa**: `canBePublished()` ritorna `false`

**Check**:
1. Wallet proposals pending: Query `notification_payload_wallets` per status pending
2. Commerce setup incompleto: `commercial_status != COMMERCIAL_ENABLED`

**Fix**: Completare setup wallet/commerce prima di publishing

### Errore: "At least one payment method must be enabled"
**Causa**: `getEffectivePaymentMethods()->isEmpty()` ritorna `true`

**Fix**: Andare su `/settings/payments` e abilitare almeno un metodo (Stripe/Bank Transfer/Egili)

### Errore: "EPP Project is required for EPP impact mode"
**Causa**: `impact_mode = EPP` ma `epp_project_id = null`

**Fix**: Selezionare un EPP project attivo nel wizard commerce

### Collection non appare in marketplace
**Check**:
1. `is_published = true`?
2. `status = 'published'`?
3. `commercial_status = COMMERCIAL_ENABLED`?
4. Ha almeno 1 EGI originale (`originalEgis()->count() > 0`)?

**Fix**: Completare tutti i requisiti sopra

### EPP percentage errata
**Debug**:
```php
dd([
    'profile_type' => $collection->profile_type,
    'royalty_mode' => $collection->royalty_mode,
    'is_epp_voluntary' => $collection->is_epp_voluntary,
    'epp_donation_percentage' => $collection->epp_donation_percentage,
    'effective_epp' => $collection->getEffectiveEppPercentage()
]);
```

**Expected**:
- Contributor standard: 20.0
- Contributor subscriber: 0.0
- Company: variabile (0-100)

## Riferimenti Codice

- **Collection Model**: `app/Models/Collection.php:18`
- **CollectionCrudController**: `app/Http/Controllers/CollectionCrudController.php:27`
- **CollectionCommerceWizardController**: `app/Http/Controllers/CollectionCommerceWizardController.php:11`
- **CollectionCommercialService**: `app/Services/Commerce/CollectionCommercialService.php:15`
- **CollectionPolicy**: `app/Policies/CollectionPolicy.php`
- **CommercialStatusEnum**: `app/Enums/Commerce/CommercialStatusEnum.php`
- **DeliveryPolicyEnum**: `app/Enums/Commerce/DeliveryPolicyEnum.php`
- **ImpactModeEnum**: `app/Enums/Commerce/ImpactModeEnum.php`
- **Routes**: `routes/web.php` (collection routes group)

## Prossimi Sviluppi

- [ ] Collection analytics dashboard (views, sales, revenue)
- [ ] Batch operations per EGI (mass update metadata)
- [ ] Collection transfer ownership flow (con notifica team members)
- [ ] Advanced royalty splits (multi-wallet percentages configurabili)
- [ ] Collection templates (preset per art/music/photography)
- [ ] API endpoints per collection CRUD (attualmente solo web)
