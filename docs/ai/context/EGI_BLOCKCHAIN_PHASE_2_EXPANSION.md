# 🚀 EGI BLOCKCHAIN INTEGRATION - PHASE 2 EXPANSION PLAN

**Versione:** 2.0.0  
**Data:** 9 Ottobre 2025  
**Stato:** � IN PROGRESS - 29% completato (29/100 tasks) ✅  
**Fase Precedente:** FASE 6 completata al 94% - Real Blockchain Mint Operativo  
**Documento Base:** `EGI_BLOCKCHAIN_INTEGRATION_MASTER.md`

**📊 Progress per Area:**

-   ✅ Area 1 (Mint/Prenotazioni): 82% (14/17 tasks) 🟢 FUNZIONALE
-   ✅ Area 2 (Payment Distributions): 64% (7/11 tasks) 🟡 PARZIALE
-   ❌ Area 3 (IBAN User): 0% (0/16 tasks) 🔴 NOT STARTED
-   ❌ Area 4 (IBAN Wallets): 0% (0/13 tasks) 🔴 NOT STARTED
-   🟡 Area 5 (Metadata): 39% (7/18 tasks) 🟡 IN PROGRESS
-   ❌ Area 6 (IPFS): 0% (0/25 tasks) 🔴 NOT STARTED

---

## 🎯 **OVERVIEW PHASE 2**

### **OBIETTIVO**

Evoluzione del sistema blockchain EGI per supportare:

1. **Marketplace duale**: Coesistenza Mint diretto + Prenotazioni/Aste
2. **Payment tracking completo**: DB + Blockchain dual tracking
3. **Financial infrastructure**: IBAN support per revenue distribution
4. **Metadata rich**: Traits, CoA, display names permanenti
5. **IPFS Integration**: Decentralized storage per immagini e metadata
6. **Preparazione Mainnet**: Infrastructure production-ready

### **ARCHITETTURA TARGET**

```
┌─────────────────────────────────────────────────┐
│  FRONTEND - Dual Action Buttons                 │
│  ├─ "Prenota" (Reservation/Auction path)       │
│  └─ "Minta Ora" (Direct mint path)             │
├─────────────────────────────────────────────────┤
│  BUSINESS LOGIC LAYER                           │
│  ├─ Reservation System (existing + enhanced)   │
│  ├─ Direct Mint System (new path)              │
│  ├─ Payment Distribution (dual tracking)       │
│  └─ Metadata Builder (traits + CoA + IPFS)     │
├─────────────────────────────────────────────────┤
│  DATA LAYER                                     │
│  ├─ egi_blockchain (+ metadata JSON field)     │
│  ├─ payment_distributions (+ mint tracking)    │
│  ├─ user_personal_data (+ IBAN fields)         │
│  └─ wallets (+ IBAN support)                   │
├─────────────────────────────────────────────────┤
│  EXTERNAL SERVICES                              │
│  ├─ IPFS Provider (Pinata/NFT.Storage)         │
│  ├─ Algorand Microservice (existing)           │
│  └─ PSP Providers (Stripe/PayPal)              │
└─────────────────────────────────────────────────┘
```

### **COMPLIANCE & CONSTRAINTS**

-   🏛️ **MiCA-SAFE**: Tutti i nuovi flussi devono rimanere compliant
-   🔒 **GDPR**: IBAN = dato sensibile finanziario (encryption mandatory)
-   ⚖️ **PA/Enterprise**: Qualità enterprise-grade mantenuta
-   📊 **Audit Trail**: Ogni operazione loggata completamente

---

## 📋 **TODOLIST PHASE 2**

### **🔄 AREA 1: SEPARAZIONE MINT/PRENOTAZIONI**

**Obiettivo:** EGI non mintato può essere prenotato O mintato direttamente. Preparazione per sistema aste.

#### **1.1 Business Logic - State Management**

-   [x] **1.1.1** - Estendere model `Egi.php` con nuovi metodi ✅

    -   [x] Method `canBeMinted(): bool` - Verifica se EGI può essere mintato ✅
    -   [x] Method `canBeReserved(): bool` - Verifica se EGI può essere prenotato ✅
    -   [x] Method `hasPendingReservation(): bool` - Check prenotazioni attive ✅
    -   [x] Method `isReservedByUser(User $user): bool` - Check ownership prenotazione ✅
    -   [x] Scope `availableForMint()` - Query builder helper ✅
    -   [x] Scope `availableForReservation()` - Query builder helper ✅

-   [x] **1.1.2** - Creare service `EgiAvailabilityService.php` ✅

    -   [x] Method `checkAvailability(Egi $egi, User $user): array` ✅
        -   Return: `['can_mint' => bool, 'can_reserve' => bool, 'reason' => string]`
    -   [x] Method `getAvailableActions(Egi $egi, User $user): array` ✅
        -   Return: lista azioni disponibili per UI
    -   [x] Business rules: ✅
        -   EGI mintato → nessuna azione disponibile
        -   EGI prenotato da me → solo mint
        -   EGI prenotato da altri → nessuna azione
        -   EGI libero → mint + prenotazione

-   [x] **1.1.3** - Validation rules per dual path ✅
    -   [x] `MintRequest` validation: verifica can_mint + permissions ✅
    -   [x] `ReservationRequest` validation: verifica can_reserve + no conflicts ✅
    -   [x] Error messages specifici per ogni scenario ✅
    -   [x] Rate limiting separato per mint vs reservation ✅

#### **1.2 Frontend Components**

-   [x] **1.2.1** - Modificare `egi-card.blade.php` ✅

    -   [x] Due bottoni contemporanei layout (flex/grid) ✅
    -   [x] Bottone "Prenota" condizionale ✅
        -   Visible se: `$egi->canBeReserved() && !Auth::user()->hasReservation($egi)`
        -   Style: Secondary button (outline)
        -   Icon: Calendar/Clock
    -   [x] Bottone "Minta Ora" condizionale ✅
        -   Visible se: `$egi->canBeMinted()`
        -   Style: Primary button (solid)
        -   Icon: Zap/Lightning
        -   Badge "Direct Mint" premium indicator
    -   [x] Stato "Prenotato da te" special styling ✅
        -   Solo bottone "Completa Mint" visible
        -   Countdown timer se prenotazione ha scadenza
    -   [x] Translations: 6 lingue (IT, EN, ES, FR, DE, PT) ✅

-   [ ] **1.2.2** - Modificare `egi-card-enhanced.blade.php`

    -   [ ] Layout enhanced per dual action
    -   [ ] Tooltip info per ogni azione
    -   [ ] Preview modal prima di mint diretto
    -   [ ] Conferma user per azioni critiche

-   [ ] **1.2.3** - Modificare `egi-card-list.blade.php`

    -   [ ] Compact layout per due bottoni
    -   [ ] Filters per stato: "Mintabili", "Prenotabili", "Tutti"
    -   [ ] Sorting options: prezzo, data creazione, stato

-   [ ] **1.2.4** - Modificare `egi-card-carousel.blade.php`
    -   [ ] Carousel indicators per stato EGI
    -   [ ] Quick action buttons overlay
    -   [ ] Smooth transitions tra stati

#### **1.3 Routes & Controllers**

-   [x] **1.3.1** - Nuove routes in `web.php` ✅

    ```php
    Route::middleware(['auth'])->group(function () {
        // Direct mint path
        Route::get('/egi/{egi}/mint-direct', [MintController::class, 'showDirectMint'])
            ->name('egi.mint.direct');
        Route::post('/egi/{egi}/mint-direct', [MintController::class, 'processDirectMint'])
            ->name('egi.mint.direct.process');

        // Existing reservation path (unchanged)
        Route::post('/egi/{egi}/reserve', [ReservationController::class, 'store'])
            ->name('egi.reserve');
    });
    ```

-   [x] **1.3.2** - Estendere `MintController.php` ✅
    -   [x] Method `showDirectMint(Egi $egi)` - Form mint diretto ✅
    -   [x] Method `processDirectMint(Request $request, Egi $egi)` - Process mint ✅
    -   [x] Integration con `EgiAvailabilityService` ✅
    -   [x] Authorization checks via Policy ✅
    -   [x] GDPR audit logging ✅
    -   [x] UEM error handling ✅

#### **1.4 Testing**

-   [ ] **1.4.1** - Unit tests `EgiAvailabilityServiceTest.php`

    -   [ ] Test: EGI libero → can_mint = true, can_reserve = true
    -   [ ] Test: EGI mintato → entrambi false
    -   [ ] Test: EGI prenotato da me → solo can_mint true
    -   [ ] Test: EGI prenotato da altri → entrambi false
    -   [ ] Test: Permission checks integration

-   [ ] **1.4.2** - Feature tests `DirectMintFlowTest.php`
    -   [ ] Test: Complete flow mint diretto senza prenotazione
    -   [ ] Test: Concurrent mint attempts (race condition)
    -   [ ] Test: Authorization failures
    -   [ ] Test: GDPR audit trail completeness

---

### **💰 AREA 2: PAYMENT DISTRIBUTIONS PER MINT**

**Obiettivo:** Ogni mint popola `payment_distributions` per dual tracking DB + Blockchain fino a mainnet.

#### **2.1 Database Schema**

-   [x] **2.1.1** - Migration: Estendere `payment_distributions` ✅

    ```sql
    ALTER TABLE payment_distributions ADD COLUMN:
    - source_type ENUM('reservation', 'mint', 'transfer') DEFAULT 'reservation'
    - egi_blockchain_id BIGINT UNSIGNED NULL
    - blockchain_tx_id VARCHAR(255) NULL
    - INDEX idx_source_type (source_type)
    - FOREIGN KEY (egi_blockchain_id) REFERENCES egi_blockchain(id) ON DELETE SET NULL
    ```

    **File:** `2025_10_09_105125_extend_payment_distributions_for_mint_tracking.php` ✅

-   [x] **2.1.2** - Verificare constraints esistenti ✅
    -   [x] Check foreign keys a `reservations` (nullable?) ✅
    -   [x] Verificare business rules existing ✅
    -   [x] Backup schema prima di modifiche ✅
            **Migration:** `2025_10_09_110624_make_reservation_id_nullable_in_payment_distributions.php` ✅

#### **2.2 Service Layer**

-   [x] **2.2.1** - Estendere `PaymentDistributionService.php` ✅

    -   [x] Method `recordMintDistribution(EgiBlockchain $egiBlockchain, array $paymentData): void` ✅
        -   Input: blockchain record + payment details
        -   Logic: Split revenue secondo percentuali collection
        -   GDPR: Audit log completo
        -   UEM: Error handling
    -   [x] Method `getMintDistributions(Egi $egi): Collection` ✅
    -   [x] Method `compareDbVsBlockchain(Egi $egi): array` ✅
        -   Utility per verificare consistenza

-   [x] **2.2.2** - Integration in `EgiMintingService.php` ✅

    -   [x] After successful mint → call `recordMintDistribution()` ✅
    -   [x] Wrap in DB transaction con mint ✅
    -   [x] Rollback strategy se distribution fails ✅
    -   [x] Retry logic se blockchain OK ma DB fails ✅

-   [ ] **2.2.3** - Integration in `EgiPurchaseWorkflowService.php`
    -   [ ] Orchestrate payment → mint → distribution
    -   [ ] Atomic transaction management
    -   [ ] Error recovery mechanisms

#### **2.3 Statistics & Analytics**

-   [ ] **2.3.1** - Nuovo service `DualTrackingAnalyticsService.php`

    -   [ ] Method `getReservationStats(): array` - Stats esistenti
    -   [ ] Method `getMintStats(): array` - Stats nuove mint diretti
    -   [ ] Method `getCombinedStats(): array` - Vista unificata
    -   [ ] Method `getDiscrepancyReport(): array` - DB vs Blockchain diff

-   [ ] **2.3.2** - Dashboard admin views
    -   [ ] Sezione "Mint Statistics" separata da "Reservation Statistics"
    -   [ ] Grafici comparative revenue sources
    -   [ ] Alert per discrepanze DB/Blockchain
    -   [ ] Export CSV per entrambi i dataset

#### **2.4 Testing**

-   [ ] **2.4.1** - Unit tests `PaymentDistributionServiceTest.php`

    -   [ ] Test: recordMintDistribution crea record corretto
    -   [ ] Test: Revenue split secondo percentuali
    -   [ ] Test: GDPR audit trail completo
    -   [ ] Test: Error handling scenari

-   [ ] **2.4.2** - Integration tests
    -   [ ] Test: Mint workflow completo popola payment_distributions
    -   [ ] Test: Transaction rollback su errori
    -   [ ] Test: Consistency check DB vs Blockchain

---

### **🏦 AREA 3: COORDINATE BANCARIE USER**

**Obiettivo:** User può inserire IBAN in `user_personal_data` per revenue distribution. GDPR compliant.

#### **3.1 Database Schema**

-   [ ] **3.1.1** - Migration: Estendere `user_personal_data`

    ```sql
    ALTER TABLE user_personal_data ADD COLUMN:
    - iban VARCHAR(34) NULL
    - bank_name VARCHAR(255) NULL
    - swift_bic VARCHAR(11) NULL
    - account_holder_name VARCHAR(255) NULL
    - iban_country_code CHAR(2) NULL
    - iban_verified_at TIMESTAMP NULL
    - iban_verification_method ENUM('manual', 'api', 'document') NULL
    - iban_encrypted BOOLEAN DEFAULT true
    - INDEX idx_iban_verified (iban_verified_at)
    ```

-   [ ] **3.1.2** - Encryption setup
    -   [ ] Laravel encryption per campo `iban`
    -   [ ] Key rotation strategy documentata
    -   [ ] Backup encrypted data strategy

#### **3.2 Validation Layer**

-   [ ] **3.2.1** - Creare `IbanValidationService.php`

    -   [ ] Method `validateIban(string $iban): ValidationResult`
        -   Check formato paese (IT, DE, FR, etc.)
        -   Check digits verification
        -   Length validation per paese
        -   Character validation
    -   [ ] Method `getIbanCountry(string $iban): string`
    -   [ ] Method `formatIban(string $iban): string` - Pretty print
    -   [ ] Library: `league/iban` o simile (verificare disponibilità)

-   [ ] **3.2.2** - Validation rules per form
    -   [ ] Custom rule `IbanRule.php`
    -   [ ] Required se user seleziona IBAN payment method
    -   [ ] Unique constraint per IBAN (optional, security concern)

#### **3.3 Model & Relationships**

-   [ ] **3.3.1** - Estendere model `UserPersonalData.php`
    -   [ ] Fillable fields per IBAN
    -   [ ] Casts: `iban` → encrypted cast
    -   [ ] Accessor `getFormattedIbanAttribute()` - XX** \*\*** \***\* **34 format
    -   [ ] Mutator `setIbanAttribute()` - Auto format + validate
    -   [ ] Method `hasValidIban(): bool`
    -   [ ] Method `getIbanForPayment(): ?string` - Decrypt for payment processing

#### **3.4 UI/UX**

-   [ ] **3.4.1** - Form profilo utente `/profile/financial-data`

    -   [ ] Sezione "Coordinate Bancarie" accordion
    -   [ ] Campo IBAN con formatting real-time
    -   [ ] Campo Bank Name autocomplete
    -   [ ] Campo SWIFT/BIC optional
    -   [ ] Checkbox "Verifica che intestatario corrisponda"
    -   [ ] Privacy consent specifico GDPR
        -   [ ] Testo consent: "Autorizzo trattamento dati bancari per pagamenti"
        -   [ ] Link privacy policy specifica
    -   [ ] Visual feedback validazione IBAN
    -   [ ] Translations: 6 lingue

-   [ ] **3.4.2** - Display IBAN nelle varie sezioni
    -   [ ] Payment method selection: IBAN come opzione
    -   [ ] Withdrawal requests: Pre-fill IBAN verificato
    -   [ ] Admin panel: IBAN masked `IT** **** **** **34`

#### **3.5 Security & Compliance**

-   [ ] **3.5.1** - GDPR compliance checklist

    -   [ ] Consent tracking per IBAN storage
    -   [ ] Audit log per accesso IBAN
    -   [ ] Audit log per modifiche IBAN
    -   [ ] Right to erasure: delete IBAN strategy
    -   [ ] Data export: IBAN incluso in user data export

-   [ ] **3.5.2** - Access control

    -   [ ] Permission `view-own-iban` (user stesso)
    -   [ ] Permission `view-any-iban` (admin/finance)
    -   [ ] Permission `edit-own-iban` (user stesso)
    -   [ ] Rate limiting per accesso IBAN (anti-scraping)

-   [ ] **3.5.3** - Monitoring
    -   [ ] Alert su accessi IBAN frequenti
    -   [ ] Log decrypt operations
    -   [ ] Monthly audit report accessi dati finanziari

#### **3.6 Testing**

-   [ ] **3.6.1** - Unit tests `IbanValidationServiceTest.php`

    -   [ ] Test: IBAN italiani validi
    -   [ ] Test: IBAN EU vari paesi (DE, FR, ES)
    -   [ ] Test: IBAN invalidi (checksum, formato)
    -   [ ] Test: Formatting consistency

-   [ ] **3.6.2** - Feature tests `UserFinancialDataTest.php`
    -   [ ] Test: User può salvare IBAN valido
    -   [ ] Test: Encryption/Decryption workflow
    -   [ ] Test: GDPR audit trail
    -   [ ] Test: Access control permissions

---

### **💼 AREA 4: IBAN SUPPORT IN WALLETS TABLE**

**Obiettivo:** `wallets` table supporta IBAN oltre a crypto addresses per revenue distribution.

#### **4.1 Database Verification & Schema**

-   [ ] **4.1.1** - Verificare schema `wallets` esistente

    ```bash
    # Action: Leggere migration wallets e model Wallet.php
    ```

    -   [ ] Check se `type` ENUM include 'iban'
    -   [ ] Check campi esistenti sufficienti
    -   [ ] Check constraints e foreign keys

-   [ ] **4.1.2** - Migration: Estendere `wallets` (se necessario)

    ```sql
    ALTER TABLE wallets MODIFY COLUMN:
    - type ENUM('algorand', 'ethereum', 'iban', 'paypal', 'bank_transfer')

    ADD COLUMN:
    - iban_data JSON NULL -- {bank_name, swift_bic, account_holder}
    - wallet_metadata JSON NULL -- generic metadata field
    - is_verified BOOLEAN DEFAULT false
    - verified_at TIMESTAMP NULL
    - verification_method VARCHAR(50) NULL
    ```

#### **4.2 Model Layer**

-   [ ] **4.2.1** - Estendere model `Wallet.php`

    -   [ ] Cast `iban_data` → array
    -   [ ] Cast `wallet_metadata` → array
    -   [ ] Accessor `getFormattedAddressAttribute()` - Handle crypto + IBAN
    -   [ ] Method `isIban(): bool`
    -   [ ] Method `isCrypto(): bool`
    -   [ ] Method `getDisplayAddress(): string` - Masked per IBAN
    -   [ ] Scope `iban()` - Filter IBAN wallets
    -   [ ] Scope `crypto()` - Filter crypto wallets

-   [ ] **4.2.2** - Validation rules per Wallet
    -   [ ] Se type='iban' → validate IBAN format
    -   [ ] Se type='algorand' → validate Algorand address
    -   [ ] Unique per (collection_id, type, address)

#### **4.3 Service Layer**

-   [ ] **4.3.1** - Creare/Estendere `WalletService.php`

    -   [ ] Method `createIbanWallet(Collection $collection, array $ibanData): Wallet`
    -   [ ] Method `getPrimaryWallet(Collection $collection): ?Wallet`
    -   [ ] Method `setPrimaryWallet(Wallet $wallet): void`
    -   [ ] Method `validateWalletForPayment(Wallet $wallet): bool`
    -   [ ] Business rules:
        -   1 primary wallet per collection
        -   Primary può essere crypto O IBAN
        -   Validation specifica per type

-   [ ] **4.3.2** - Revenue distribution logic update
    -   [ ] `RevenueDistributionService`: Handle IBAN wallets
    -   [ ] Payment routing: crypto vs IBAN
    -   [ ] Batch payments per IBAN (SEPA)

#### **4.4 UI/UX**

-   [ ] **4.4.1** - Collection wallet management `/collections/{id}/wallets`

    -   [ ] Lista wallets esistenti (crypto + IBAN)
    -   [ ] Bottone "Aggiungi Wallet IBAN"
    -   [ ] Form wallet IBAN:
        -   [ ] Campo IBAN con validazione
        -   [ ] Campo Bank Name
        -   [ ] Campo SWIFT/BIC (optional)
        -   [ ] Checkbox "Set as Primary"
    -   [ ] Bottone "Aggiungi Wallet Crypto" (existing)
    -   [ ] Toggle primary wallet
    -   [ ] Delete wallet con conferma
    -   [ ] Visual distinction crypto vs IBAN

-   [ ] **4.4.2** - Revenue display
    -   [ ] Mostrare destination wallet per ogni payment
    -   [ ] Icon differentiation: 💳 IBAN vs 🔗 Crypto
    -   [ ] Status: "Pending", "Paid", "Failed"

#### **4.5 Testing**

-   [ ] **4.5.1** - Unit tests `WalletServiceTest.php`

    -   [ ] Test: Create IBAN wallet
    -   [ ] Test: Validation IBAN format
    -   [ ] Test: Primary wallet management
    -   [ ] Test: Type-specific validation

-   [ ] **4.5.2** - Feature tests `WalletManagementTest.php`
    -   [ ] Test: User può aggiungere IBAN wallet
    -   [ ] Test: Primary wallet switch
    -   [ ] Test: Revenue routing to correct wallet type

---

### **📊 AREA 5: METADATA FIELD IN EGI_BLOCKCHAIN**

**Obiettivo:** Metadata JSON completo (traits, CoA, references) + display names frozen per Creator/Co-Creator.

#### **5.1 Database Schema**

-   [x] **5.1.1** - Migration: Estendere `egi_blockchain` ✅

    ```sql
    ALTER TABLE egi_blockchain ADD COLUMN:
    - metadata JSON NULL AFTER anchor_hash
    - creator_display_name VARCHAR(100) NULL AFTER metadata
    - co_creator_display_name VARCHAR(100) NULL AFTER creator_display_name
    - metadata_ipfs_cid VARCHAR(255) NULL
    - metadata_last_updated_at TIMESTAMP NULL

    ADD INDEX:
    - INDEX idx_creator_display (creator_display_name)
    - INDEX idx_co_creator_display (co_creator_display_name)
    ```

    **File:** `2025_10_09_185401_add_metadata_fields_to_egi_blockchain_table.php` ✅

#### **5.2 Metadata Structure Definition**

-   [x] **5.2.1** - Creare `EgiMetadataStructure.php` (DTO/Schema) ✅

    ```php
    class EgiMetadataStructure {
        public array $traits;           // Standard NFT traits
        public array $coa_traits;       // Certificate of Authenticity traits
        public ?string $coa_reference;  // "COA-123-XYZ"
        public Carbon $creation_date;
        public string $edition;         // "1/1", "5/100"
        public array $technical_specs;  // {dimensions, format, etc}
        public ?string $ipfs_image_cid;
        public ?string $ipfs_metadata_cid;
        public ?string $collection_slug;
        public ?int $collection_id;
        public array $properties;       // OpenSea compatible properties
        public array $attributes;       // OpenSea compatible attributes
    }
    ```

-   [x] **5.2.2** - OpenSea metadata standard compliance ✅
    ```json
    {
        "name": "EGI #123",
        "description": "...",
        "image": "ipfs://Qm...",
        "external_url": "https://florenceegi.it/egi/123",
        "attributes": [
            { "trait_type": "Creator", "value": "Artist Name" },
            { "trait_type": "Category", "value": "Digital Art" },
            { "trait_type": "Edition", "value": "1/1" }
        ],
        "properties": {
            "coa_reference": "COA-123-XYZ",
            "creation_date": "2025-10-09",
            "blockchain": "algorand"
        }
    }
    ```

#### **5.3 Service Layer**

-   [x] **5.3.1** - Creare `EgiMetadataBuilderService.php` ✅

    -   [x] Method `buildMetadata(Egi $egi, User $minter): EgiMetadataStructure` ✅
        -   Extract traits from EGI ✅
        -   Extract CoA traits (if CoA exists) ✅
        -   Build OpenSea-compatible structure ✅
        -   Include IPFS references ✅
    -   [x] Method `validateMetadata(array $metadata): ValidationResult` ✅
    -   [x] Method `updateMetadata(EgiBlockchain $egiBlockchain, array $metadata): void` ✅
    -   [x] Method `exportForIpfs(EgiMetadataStructure $metadata): string` - JSON format ✅

    **File:** `app/Services/EgiMetadataBuilderService.php` (638 lines)  
    **Commit:** 8a2d837

-   [x] **5.3.2** - Creare `DisplayNameService.php` ✅

    -   [x] Method `freezeCreatorName(Egi $egi): string` ✅
        -   Snapshot `User->name` al momento creazione EGI ✅
        -   Store in `egi_blockchain.creator_display_name` ✅
        -   Immutable dopo mint ✅
    -   [x] Method `freezeCoCreatorName(User $minter, ?string $customName): string` ✅
        -   Snapshot `User->name` al momento mint ✅
        -   Store in `egi_blockchain.co_creator_display_name` ✅
        -   Immutable dopo mint ✅
    -   [x] Method `proposeCoCreatorName(User $user): string` ✅
        -   Default = `User->name` ✅
        -   User può modificare PRE-mint ✅
        -   Max 100 chars, alphanumeric + spaces ✅
    -   [x] Method `validateDisplayName(string $name): bool` ✅
    -   [x] Method `storeFrozenNames(EgiBlockchain, creator, coCreator): void` ✅
    -   [x] Method `areNamesFrozen(EgiBlockchain): bool` ✅

    **File:** `app/Services/DisplayNameService.php` (527 lines)  
    **Commit:** dd020cf

#### **5.4 Model Layer**

-   [x] **5.4.1** - Estendere model `EgiBlockchain.php` ✅

    -   [x] Cast `metadata` → array ✅
    -   [x] Accessor `getMetadataStructure(): EgiMetadataStructure` ✅
    -   [x] Accessor `getCreatorDisplayName()` - Frozen value ✅
    -   [x] Accessor `getCoCreatorDisplayName()` - Frozen value ✅
    -   [x] Method `hasMetadata(): bool` ✅
    -   [x] Method `hasCoaReference(): bool` ✅
    -   [x] Method `getTraits(): array` ✅
    -   [x] Method `getCoaTraits(): array` ✅
    -   [x] Method `getOpenSeaAttributes(): array` ✅
    -   [x] Method `getMetadataIpfsUrl(): ?string` ✅
    -   [x] Method `areDisplayNamesFrozen(): bool` ✅

    **File:** `app/Models/EgiBlockchain.php` (380 lines) - Already implemented ✅

#### **5.5 UI/UX**

-   [x] **5.5.1** - Pre-mint form: Co-Creator nickname ✅

    -   [x] In `/mint/checkout` form ✅
    -   [x] Campo "Display Name" (optional) ✅
    -   [x] Placeholder: Current user name ✅
    -   [x] Info text: "Questo nome sarà permanente nell'EGI" ✅
    -   [x] Character counter (max 100) con color feedback ✅
    -   [x] Real-time validation (pattern + length) ✅
    -   [x] Amber warning box about immutability ✅

    **File:** `resources/views/mint/checkout.blade.php` (+53 lines)  
    **Translations:** `resources/lang/it/mint.php` (+9 keys)  
    **Backend:** `MintController.php`, `MintDirectRequest.php`, `MintEgiJob.php`  
    **Commit:** d207b34

    **Implementation Details:**

    -   Input field with `maxlength="100"` and pattern validation
    -   Default value: `Auth::user()->name`
    -   Real-time character counter: gray (0-74) → amber (75-89) → red (90-100)
    -   Pattern regex: `/^[a-zA-Z0-9\s.\'\-]+$/` (alphanumeric + spaces + . ' -)
    -   Validation on blur with visual feedback (red border on error)
    -   Amber alert box: "ATTENZIONE: Questo nome diventerà permanente..."
    -   Stored in `egi_blockchain.co_creator_display_name` on record creation
    -   Passed to `MintEgiJob` → `EgiMintingService` → `DisplayNameService.freezeCoCreatorName()`
    -   Full backend validation in controller + FormRequest

-   [ ] **5.5.2** - Display metadata in EGI views

    -   [ ] EGI detail page: Tab "Metadata"
    -   [ ] Show traits in structured format
    -   [ ] Show CoA reference se esiste
    -   [ ] Show Creator + Co-Creator display names
    -   [ ] Link to IPFS metadata (se disponibile)
    -   [ ] JSON raw view (collapsible)

-   [ ] **5.5.3** - Admin metadata editor
    -   [ ] Admin può editare metadata PRE-mint
    -   [ ] POST-mint: metadata frozen (blockchain anchored)
    -   [ ] Validation warnings pre-publish

#### **5.6 Integration**

-   [x] **5.6.1** - Integration in mint workflow ✅

    -   [x] Before mint: Build metadata completo ✅
    -   [x] Freeze display names al momento mint ✅
    -   [ ] Upload metadata to IPFS (Area 6 dependency) ⏳ BLOCKED BY AREA 6
    -   [ ] Store IPFS CID in `metadata_ipfs_cid` ⏳ BLOCKED BY AREA 6
    -   [ ] Update Algorand ASA with metadata URL ⏳ BLOCKED BY AREA 6

    **File:** `app/Services/EgiMintingService.php` (modified - +77 lines, -13 lines)  
    **Commit:** 1eef50f

    **Integration Details:**

    -   Added `EgiMetadataBuilderService` + `DisplayNameService` to constructor
    -   Modified `mintEgi()` method with complete metadata workflow:
        1. Build `EgiMetadataStructure` with OpenSea-compatible attributes
        2. Freeze creator display name (from EGI creator)
        3. Freeze co-creator display name (from minter User)
        4. Validate metadata structure before blockchain mint
        5. Store metadata JSON + display names in `egi_blockchain` table
    -   Custom co-creator name supported via `$metadata['co_creator_display_name']`
    -   Audit trail includes metadata info (traits count, CoA, display names)
    -   IPFS integration prepared but blocked by Area 6 implementation
    -   Full backward compatibility maintained

#### **5.7 Testing**

-   [ ] **5.7.1** - Unit tests `EgiMetadataBuilderServiceTest.php`

    -   [ ] Test: Build metadata completo
    -   [ ] Test: OpenSea standard compliance
    -   [ ] Test: Traits extraction corretta
    -   [ ] Test: CoA integration

-   [ ] **5.7.2** - Unit tests `DisplayNameServiceTest.php`

    -   [ ] Test: Freeze creator name
    -   [ ] Test: Freeze co-creator name
    -   [ ] Test: Display name validation
    -   [ ] Test: Immutability dopo mint

-   [ ] **5.7.3** - Integration tests
    -   [ ] Test: Complete mint workflow con metadata
    -   [ ] Test: Display names frozen correttamente
    -   [ ] Test: Metadata IPFS upload (Area 6)

---

### **☁️ AREA 6: IPFS INTEGRATION**

**Obiettivo:** Upload automatico immagini + metadata su IPFS. IPFS URLs in metadata blockchain.

#### **6.1 IPFS Provider Setup**

-   [ ] **6.1.1** - Scegliere IPFS provider

    -   **Opzioni:**
        -   [ ] **Pinata** (raccomandato)
            -   Free tier: 1GB storage, 100K API calls/mese
            -   Pricing: ~$20/mese per 100GB
            -   Dashboard: https://pinata.cloud
            -   Pros: Semplice, reliable, buon supporto
        -   [ ] **NFT.Storage**
            -   Free (sponsorizzato Protocol Labs)
            -   Unlimited storage per NFTs
            -   Pros: Gratuito, NFT-focused
            -   Cons: Meno controllo, future incerto
        -   [ ] **Infura IPFS**
            -   Free tier: 5GB storage
            -   Pricing: $50/mese per 50GB
            -   Pros: Parte Infura suite (Ethereum integration)
        -   [ ] **Self-hosted IPFS node**
            -   Pros: Controllo totale, no costs
            -   Cons: Maintenance, uptime responsibility
    -   **Decisione:** [DA DEFINIRE CON FABIO]

-   [ ] **6.1.2** - Account creation & API keys

    -   [ ] Creare account provider scelto
    -   [ ] Generate API keys
    -   [ ] Configure payment method (se necessario)
    -   [ ] Setup alerts per usage limits

-   [ ] **6.1.3** - Environment configuration
    ```env
    IPFS_PROVIDER=pinata  # pinata|nft_storage|infura|custom
    IPFS_API_KEY=your_api_key_here
    IPFS_API_SECRET=your_api_secret_here
    IPFS_GATEWAY_URL=https://gateway.pinata.cloud
    IPFS_PIN_ON_UPLOAD=true
    IPFS_MAX_FILE_SIZE=10485760  # 10MB
    IPFS_TIMEOUT=60  # seconds
    ```

#### **6.2 Configuration Layer**

-   [ ] **6.2.1** - Config file `config/ipfs.php`
    ```php
    return [
        'provider' => env('IPFS_PROVIDER', 'pinata'),
        'api_key' => env('IPFS_API_KEY'),
        'api_secret' => env('IPFS_API_SECRET'),
        'gateway_url' => env('IPFS_GATEWAY_URL', 'https://gateway.pinata.cloud'),
        'pin_on_upload' => env('IPFS_PIN_ON_UPLOAD', true),
        'max_file_size' => env('IPFS_MAX_FILE_SIZE', 10485760),
        'timeout' => env('IPFS_TIMEOUT', 60),
        'retry_attempts' => 3,
        'retry_delay' => 1000, // ms
    ];
    ```

#### **6.3 Service Layer**

-   [ ] **6.3.1** - Creare `IpfsService.php` (Main service)

    -   [ ] Method `uploadImage(File $image, array $metadata = []): IpfsUploadResult`
        -   Input: File object da storage
        -   Output: `{cid: string, url: string, size: int}`
        -   Logic: Upload to provider, get CID, pin content
    -   [ ] Method `uploadMetadata(array $metadata, string $name): IpfsUploadResult`
        -   Input: Metadata array (OpenSea compatible)
        -   Output: `{cid: string, url: string}`
        -   Logic: JSON encode, upload as file, pin
    -   [ ] Method `uploadJson(string $json, string $filename): IpfsUploadResult`
    -   [ ] Method `pinContent(string $cid): bool`
    -   [ ] Method `unpinContent(string $cid): bool`
    -   [ ] Method `getGatewayUrl(string $cid): string`
        -   Convert `ipfs://Qm...` to `https://gateway.../ipfs/Qm...`
    -   [ ] Method `getContentInfo(string $cid): array`
    -   [ ] Method `testConnection(): bool` - Health check

-   [ ] **6.3.2** - Creare provider interface `IpfsProviderInterface.php`

    ```php
    interface IpfsProviderInterface {
        public function uploadFile(string $filePath, array $options = []): array;
        public function uploadJson(string $json, string $filename, array $options = []): array;
        public function pin(string $cid): bool;
        public function unpin(string $cid): bool;
        public function getInfo(string $cid): array;
    }
    ```

-   [ ] **6.3.3** - Implementare `PinataProvider.php`

    -   [ ] Implementa `IpfsProviderInterface`
    -   [ ] HTTP client per Pinata API
    -   [ ] Endpoints:
        -   `POST /pinning/pinFileToIPFS` - Upload file
        -   `POST /pinning/pinJSONToIPFS` - Upload JSON
        -   `POST /pinning/unpin/{cid}` - Unpin
        -   `GET /data/pinList` - List pins
    -   [ ] Error handling Pinata-specific
    -   [ ] Rate limiting handling

-   [ ] **6.3.4** - Implementare `NftStorageProvider.php` (optional)

    -   [ ] Implementa `IpfsProviderInterface`
    -   [ ] NFT.Storage API client
    -   [ ] CAR file support (se necessario)

-   [ ] **6.3.5** - Factory pattern `IpfsProviderFactory.php`
    -   [ ] Method `make(string $provider): IpfsProviderInterface`
    -   [ ] Instantiate provider based on config
    -   [ ] Dependency injection per providers

#### **6.4 Integration in Mint Workflow**

-   [ ] **6.4.1** - Modificare `EgiMintingService.php`

    -   [ ] Step 1: Upload image to IPFS
        ```php
        $imageResult = $this->ipfsService->uploadImage(
            $egi->getImageFile(),
            ['name' => $egi->title, 'description' => $egi->description]
        );
        $ipfsImageUrl = "ipfs://{$imageResult->cid}";
        ```
    -   [ ] Step 2: Build metadata con IPFS image URL
        ```php
        $metadata = $this->metadataBuilder->buildMetadata($egi, $user);
        $metadata->ipfs_image_cid = $imageResult->cid;
        ```
    -   [ ] Step 3: Upload metadata to IPFS
        ```php
        $metadataResult = $this->ipfsService->uploadMetadata(
            $metadata->toArray(),
            "egi-{$egi->id}-metadata.json"
        );
        $ipfsMetadataUrl = "ipfs://{$metadataResult->cid}";
        ```
    -   [ ] Step 4: Call AlgorandService con IPFS URLs
        ```php
        $asaData = $this->algorandService->mintEgi($egi->id, [
            'image_url' => $ipfsImageUrl,
            'metadata_url' => $ipfsMetadataUrl,
            ...
        ]);
        ```
    -   [ ] Step 5: Store IPFS refs in egi_blockchain
        ```php
        $egiBlockchain->update([
            'metadata' => $metadata->toArray(),
            'metadata_ipfs_cid' => $metadataResult->cid,
            ...
        ]);
        ```

-   [ ] **6.4.2** - Modificare `AlgorandService.php`

    -   [ ] Update ASA creation con IPFS metadata URL
    -   [ ] ASA `url` field = `ipfs://Qm...` (metadata)
    -   [ ] ASA `metadata_hash` = SHA-256 del metadata JSON
    -   [ ] Note field con anchor reference

-   [ ] **6.4.3** - Modificare `algokit-microservice/server.js`
    -   [ ] Accept `metadata_url` in mint request
    -   [ ] Set ASA `url` parameter
    -   [ ] Calculate metadata hash se fornito
    -   [ ] Validation IPFS URL format

#### **6.5 Job Queue Integration**

-   [ ] **6.5.1** - Creare `UploadToIpfsJob.php`

    -   [ ] Async upload immagine + metadata
    -   [ ] Retry logic: 3 attempts, exponential backoff
    -   [ ] Timeout: 5 minuti
    -   [ ] Queue: 'ipfs' (separata da 'blockchain')
    -   [ ] Job chaining: IPFS upload → Mint job
    -   [ ] Error notification su failure

-   [ ] **6.5.2** - Workflow orchestration
    ```
    User submits mint
      ↓
    Dispatch UploadToIpfsJob
      ↓ (on success)
    Dispatch MintEgiJob (con IPFS URLs)
      ↓ (on success)
    Certificate generation
    ```

#### **6.6 Admin Tools**

-   [ ] **6.6.1** - IPFS management dashboard `/admin/ipfs`

    -   [ ] Lista tutti i content pinned
    -   [ ] Search per CID
    -   [ ] Filter per tipo (image, metadata)
    -   [ ] Unpin content (con conferma)
    -   [ ] Test connection button
    -   [ ] Usage statistics (storage used, API calls)
    -   [ ] Health check status

-   [ ] **6.6.2** - Artisan commands
    -   [ ] `php artisan ipfs:test-connection` - Test provider
    -   [ ] `php artisan ipfs:upload-file {path}` - Manual upload
    -   [ ] `php artisan ipfs:pin {cid}` - Pin existing content
    -   [ ] `php artisan ipfs:unpin {cid}` - Unpin content
    -   [ ] `php artisan ipfs:cleanup-unused` - Cleanup unpinned content
    -   [ ] `php artisan ipfs:sync-database` - Sync DB con IPFS pins

#### **6.7 Error Handling & Recovery**

-   [ ] **6.7.1** - Fallback strategies

    -   [ ] Se IPFS upload fails → retry 3 volte
    -   [ ] Se tutti retry fail → queue retry dopo 1 ora
    -   [ ] Se IPFS down → use temporary S3/local storage
    -   [ ] Admin notification su failures persistenti
    -   [ ] Manual retry mechanism

-   [ ] **6.7.2** - Monitoring
    -   [ ] Log ogni upload IPFS (ULM)
    -   [ ] Track upload durations
    -   [ ] Alert su upload >30 secondi
    -   [ ] Alert su failure rate >5%
    -   [ ] Daily report IPFS operations

#### **6.8 Testing**

-   [ ] **6.8.1** - Unit tests `IpfsServiceTest.php`

    -   [ ] Test: Upload image (mocked)
    -   [ ] Test: Upload metadata (mocked)
    -   [ ] Test: Gateway URL generation
    -   [ ] Test: Error handling

-   [ ] **6.8.2** - Unit tests `PinataProviderTest.php`

    -   [ ] Test: API calls corrette
    -   [ ] Test: Response parsing
    -   [ ] Test: Error scenarios
    -   [ ] Test: Rate limiting handling

-   [ ] **6.8.3** - Integration tests `IpfsIntegrationTest.php`

    -   [ ] Test: Upload real file to IPFS (testnet)
    -   [ ] Test: Retrieve content via gateway
    -   [ ] Test: Pin/Unpin workflow
    -   [ ] Test: Complete mint workflow con IPFS

-   [ ] **6.8.4** - Feature tests `IpfsMintFlowTest.php`
    -   [ ] Test: Complete flow: Image upload → Metadata upload → Mint
    -   [ ] Test: Job chaining IPFS → Mint
    -   [ ] Test: Error recovery mechanisms
    -   [ ] Test: IPFS URLs in blockchain record

#### **6.9 Documentation**

-   [ ] **6.9.1** - Setup guide `docs/IPFS_SETUP.md`

    -   [ ] Provider comparison table
    -   [ ] Account creation steps
    -   [ ] Configuration guide
    -   [ ] Troubleshooting common issues

-   [ ] **6.9.2** - Developer guide `docs/IPFS_INTEGRATION.md`
    -   [ ] Architecture overview
    -   [ ] Service API documentation
    -   [ ] Code examples
    -   [ ] Testing guide

---

## 📊 **PROGRESS TRACKING**

### **COMPLETION STATUS**

-   **AREA 1**: ⚪ 0/17 tasks completed (Separazione Mint/Prenotazioni)
-   **AREA 2**: ⚪ 0/11 tasks completed (Payment Distributions)
-   **AREA 3**: ⚪ 0/16 tasks completed (IBAN User Personal Data)
-   **AREA 4**: ⚪ 0/13 tasks completed (IBAN Wallets)
-   **AREA 5**: ⚪ 0/18 tasks completed (Metadata + Display Names)
-   **AREA 6**: ⚪ 0/25 tasks completed (IPFS Integration)

**TOTAL PROGRESS: 0/100 tasks (0%)**

### **CURRENT PHASE:** 📋 PLANNING COMPLETE - Ready for Implementation

---

## 🎯 **EXECUTION ROADMAP**

### **SPRINT 1 (Week 1): P0 Foundations**

**Goal:** Separazione Mint/Prenotazioni + Metadata structure

-   **Days 1-3:** Area 1 - Business logic + UI dual buttons
-   **Days 4-5:** Area 5 - Metadata field + Display names

**Deliverables:**

-   ✅ EGI può essere mintato O prenotato
-   ✅ Metadata structure definita
-   ✅ Display names frozen functionality

---

### **SPRINT 2 (Week 2): IPFS Integration**

**Goal:** IPFS provider setup + upload workflow

-   **Days 1-2:** Area 6 - Provider setup + configuration
-   **Days 3-4:** Area 6 - Service layer implementation
-   **Day 5:** Area 6 - Integration in mint workflow

**Deliverables:**

-   ✅ IPFS account attivo
-   ✅ Upload automatico immagini + metadata
-   ✅ IPFS URLs in blockchain records

---

### **SPRINT 3 (Week 3): Financial Infrastructure**

**Goal:** IBAN support + Payment tracking

-   **Days 1-2:** Area 3 - IBAN in user_personal_data
-   **Day 3:** Area 4 - IBAN in wallets table
-   **Days 4-5:** Area 2 - Payment distributions per mint

**Deliverables:**

-   ✅ User può inserire IBAN
-   ✅ Wallets supporta IBAN
-   ✅ Dual tracking DB + Blockchain operativo

---

### **SPRINT 4 (Week 4): Testing & Polish**

**Goal:** Integration testing + bug fixing + documentation

-   **Days 1-2:** Integration tests tutte le aree
-   **Days 3-4:** Bug fixing + performance optimization
-   **Day 5:** Documentation + deployment preparation

**Deliverables:**

-   ✅ Test coverage >80%
-   ✅ Performance benchmarks OK
-   ✅ Documentation completa
-   ✅ Ready for production deployment

---

## 🔍 **DEPENDENCIES MATRIX**

| Area   | Depends On | Blocks            |
| ------ | ---------- | ----------------- |
| Area 1 | Nessuna    | Area 2            |
| Area 2 | Area 1     | -                 |
| Area 3 | Nessuna    | Area 4            |
| Area 4 | Area 3     | -                 |
| Area 5 | Nessuna    | Area 6            |
| Area 6 | Area 5     | Production deploy |

**Critical Path:** Area 1 → Area 5 → Area 6 (per MVP)

---

## ⚠️ **RISK ASSESSMENT**

### **🔴 HIGH RISK**

1. **IPFS Provider Reliability**

    - Risk: Provider downtime durante mint
    - Mitigation: Fallback to local storage, retry mechanism, monitoring
    - Impact: 🔴 HIGH - Blocca mint workflow

2. **IBAN Validation Complexity**

    - Risk: Validazione insufficiente → pagamenti falliti
    - Mitigation: Library robusta, test su IBAN EU, manual verification option
    - Impact: 🟡 MEDIUM - Revenue distribution delays

3. **Race Conditions Mint/Reservation**
    - Risk: Concurrent actions su stesso EGI
    - Mitigation: DB locks, transaction isolation, idempotency keys
    - Impact: 🟡 MEDIUM - User experience issues

### **🟡 MEDIUM RISK**

4. **GDPR IBAN Encryption**

    - Risk: Key rotation issues, decrypt errors
    - Mitigation: Laravel encryption standard, backup keys, audit logs
    - Impact: 🟡 MEDIUM - Data access issues

5. **Metadata Structure Evolution**
    - Risk: Future changes break existing metadata
    - Mitigation: Versioning strategy, migration scripts, backward compatibility
    - Impact: 🟢 LOW - Manageable con versioning

### **🟢 LOW RISK**

6. **Display Names Immutability**
    - Risk: User vuole modificare post-mint
    - Mitigation: Clear UI warning, support policy documentata
    - Impact: 🟢 LOW - Support tickets only

---

## 💡 **TECHNICAL DECISIONS LOG**

### **DECISION 1: IPFS Provider**

-   **Status:** ⏳ PENDING
-   **Options:** Pinata vs NFT.Storage vs Self-hosted
-   **Recommendation:** Pinata (reliability + support)
-   **Decision Date:** [TBD]

### **DECISION 2: IBAN Validation Library**

-   **Status:** ⏳ PENDING
-   **Options:** `league/iban` vs custom implementation
-   **Recommendation:** `league/iban` (battle-tested)
-   **Decision Date:** [TBD]

### **DECISION 3: Metadata Versioning Strategy**

-   **Status:** ⏳ PENDING
-   **Options:** Schema versioning vs backward compatibility
-   **Recommendation:** Versioning con migration scripts
-   **Decision Date:** [TBD]

### **DECISION 4: IPFS Upload Timing**

-   **Status:** ⏳ PENDING
-   **Options:** Sync during mint vs Async job
-   **Recommendation:** Async job con chaining
-   **Decision Date:** [TBD]

---

## 📞 **SUPPORT & REFERENCES**

### **Key Documents**

-   `EGI_BLOCKCHAIN_INTEGRATION_MASTER.md` - Phase 1 reference
-   `PA_ENTERPRISE_TODO_MASTER.md` - Enterprise requirements
-   `PA_ENTERPRISE_IMPLEMENTATION_GUIDE.md` - Code patterns
-   `.github/copilot-instructions.md` - AI guidelines + MiCA-SAFE rules

### **External Resources**

-   **IPFS:**
    -   Pinata Docs: https://docs.pinata.cloud
    -   NFT.Storage Docs: https://nft.storage/docs
    -   IPFS Docs: https://docs.ipfs.tech
-   **IBAN Validation:**
    -   ISO 13616 Standard: https://www.iso.org/standard/81090.html
    -   IBAN Registry: https://www.swift.com/resource/iban-registry-pdf
-   **OpenSea Metadata:**
    -   Metadata Standards: https://docs.opensea.io/docs/metadata-standards

### **Team Communication**

-   🚨 **BLOCKING issues**: Update questo documento + notify team
-   📝 **Progress updates**: Check off completed tasks daily
-   🔄 **Context continuity**: Ogni chat deve leggere questo file + master

---

## 🎉 **SUCCESS CRITERIA**

### **Phase 2 Complete When:**

-   [x] User può scegliere: Prenota O Minta EGI non mintato
-   [x] Payment distributions popola per ogni mint
-   [x] User ha IBAN validated in profilo
-   [x] Collection wallet supporta IBAN + crypto
-   [x] Metadata JSON completo con traits + CoA
-   [x] Creator + Co-Creator display names frozen
-   [x] Immagini uploadate su IPFS automaticamente
-   [x] Metadata uploadati su IPFS automaticamente
-   [x] IPFS URLs in Algorand ASA
-   [x] Dual tracking DB vs Blockchain funzionante
-   [x] Test coverage >80% su nuovo codice
-   [x] Documentation completa tutte le aree
-   [x] MiCA-SAFE compliance mantenuta
-   [x] GDPR compliance verification passata
-   [x] Performance benchmarks OK (mint <5 secondi)
-   [x] Ready for Mainnet deployment

---

**🚀 PHASE 2 EXPANSION PLAN COMPLETE - READY FOR IMPLEMENTATION**

**Next Steps:**

1. Verify database schemas (wallets, payment_distributions)
2. Choose IPFS provider (Pinata recommended)
3. Create detailed TODO list breakdown (100+ subtasks)
4. Start implementation Sprint 1

**Ship it! 🚢**
