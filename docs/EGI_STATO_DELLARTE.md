# EGI (FlorenceEGI) - Stato dell'Arte

> **Documento generato**: 28 Novembre 2025  
> **Branch corrente**: `main`  
> **Ultimo commit**: `1248319b` - Menu Gestisci Account: separata sezione Wallet in slide dedicata  
> **Versione Laravel**: 11.31+  
> **Versione PHP**: 8.2+

---

## 1. Panoramica Progetto

**FlorenceEGI** è una piattaforma SaaS multi-tenant per la certificazione blockchain di **Eco Goods Invent (EGI)** - asset digitali unici che combinano valore artistico, traccia blockchain immutabile e contributo ambientale.

### 1.1 Stack Tecnologico Verificato

| Componente       | Tecnologia                  | Versione           |
| ---------------- | --------------------------- | ------------------ |
| **Framework**    | Laravel                     | ^11.31             |
| **PHP**          | PHP                         | ^8.2               |
| **Database**     | MariaDB                     | -                  |
| **Frontend**     | Livewire                    | ^3.0               |
| **Blockchain**   | Algorand (TestNet)          | API algonode.cloud |
| **Auth**         | Laravel Jetstream + Sanctum | ^5.3 / ^4.0        |
| **Roles**        | Spatie Permission           | ^6.10              |
| **Media**        | Spatie MediaLibrary         | ^11.0              |
| **Activity Log** | Spatie ActivityLog          | ^4.10              |
| **Payment**      | Stripe + PayPal             | ^12.6 / ^1.0       |
| **PDF**          | TCPDF + DomPDF              | ^6.9 / ^3.1        |
| **Deploy**       | Laravel Vapor               | ^2.39              |

---

## 2. Struttura Codebase

### 2.1 Dimensioni Progetto (Verificato)

| Componente              | Quantità                                |
| ----------------------- | --------------------------------------- |
| **Models**              | 97 file                                 |
| **Services**            | 85 file/cartelle                        |
| **Controllers**         | 40+ (cartelle Admin, Api, Auth incluse) |
| **Migrations**          | 172 file                                |
| **Tests**               | 85 file PHP                             |
| **Livewire Components** | 16 componenti                           |
| **Routes web.php**      | 1529 righe                              |
| **Routes api.php**      | 369 righe                               |

### 2.2 Models Principali

I model chiave identificati nella codebase:

#### Core Business

-   `Egi.php` (45KB) - Asset digitale principale
-   `Collection.php` - Collezioni di EGI
-   `User.php` (52KB) - Utenti con GDPR compliance
-   `Wallet.php` - Gestione wallet Algorand
-   `Tenant.php` - Multi-tenancy

#### Blockchain & Certificazione

-   `EgiBlockchain.php` - Stato blockchain EGI
-   `EgiSmartContract.php` - Smart contract Algorand
-   `Coa.php` - Certificate of Authenticity
-   `CoaEvent.php`, `CoaSignature.php`, `CoaAnnex.php` - Componenti CoA

#### GDPR & Compliance

-   `GdprAuditLog.php` - Audit trail GDPR
-   `ConsentHistory.php`, `ConsentType.php`, `ConsentVersion.php`
-   `UserConsent.php`, `UserConsentConfirmation.php`
-   `PrivacyPolicy.php`, `PrivacyPolicyAcceptance.php`
-   `DataExport.php`, `DataRetentionPolicy.php`

#### AI & NATAN

-   `NatanChatMessage.php` - Chat AI
-   `NatanUnifiedContext.php` - Contesto unificato
-   `NatanUserMemory.php` - Memoria utente
-   `AiCreditsTransaction.php`, `AiFeaturePricing.php`
-   `AiEgiAnalysis.php`, `AiPricingSuggestion.php`
-   `AiTraitGeneration.php`, `AiTraitProposal.php`

#### EPP (Environmental Protection)

-   `Epp.php` - Enti ambientali
-   `EppProject.php` - Progetti ambientali
-   `EppMilestone.php`, `EppTransaction.php`

#### Sistema Egili (Token Interno)

-   `EgiliTransaction.php` - Transazioni Egili
-   `EgiliMerchantPurchase.php` - Acquisti merchant

#### Fatturazione

-   `Invoice.php`, `InvoiceItem.php`, `InvoiceAggregation.php`

---

## 3. Servizi Implementati

### 3.1 GDPR Services (Verificato: `/app/Services/Gdpr/`)

| Service                            | Scopo                    |
| ---------------------------------- | ------------------------ |
| `AuditLogService.php`              | Traccia audit trail GDPR |
| `ConsentService.php`               | Gestione consensi        |
| `DataExportService.php`            | Export dati utente       |
| `GdprService.php`                  | Coordinamento GDPR       |
| `GdprNotificationService.php`      | Notifiche GDPR           |
| `LegalContentService.php`          | Contenuti legali         |
| `ProcessingRestrictionService.php` | Limitazioni trattamento  |
| `ActivityLogService.php`           | Log attività             |

### 3.2 CoA Services (Verificato: `/app/Services/Coa/`)

| Service                     | Scopo                    |
| --------------------------- | ------------------------ |
| `CoaIssueService.php`       | Emissione certificati    |
| `CoaPdfService.php`         | Generazione PDF CoA      |
| `CoaAddendumService.php`    | Addendum certificati     |
| `CoaRevocationService.php`  | Revoca certificati       |
| `SignatureService.php`      | Firme digitali           |
| `HashingService.php`        | Hash crittografici       |
| `ChainOfCustodyService.php` | Catena custodia          |
| `VocabularyService.php`     | Vocabolario termini      |
| `TraitsSnapshotService.php` | Snapshot traits          |
| `SerialGenerator.php`       | Generazione seriali      |
| `BundleService.php`         | Bundle documenti         |
| `AnnexService.php`          | Allegati                 |
| `VerifyPageService.php`     | Pagina verifica pubblica |

### 3.3 Blockchain Services

| Service                        | Path                        |
| ------------------------------ | --------------------------- |
| `AlgorandService.php`          | `/app/Services/`            |
| `AlgorandClient.php`           | `/app/Services/Blockchain/` |
| `CertificateAnchorService.php` | `/app/Services/`            |

### 3.4 AI Services

| Service                        | Scopo                          |
| ------------------------------ | ------------------------------ |
| `AnthropicService.php`         | Integrazione Claude AI (98KB!) |
| `AiCreditsService.php`         | Gestione crediti AI            |
| `AiTraitGenerationService.php` | Generazione traits AI          |
| `ArtAdvisorService.php`        | Consulente artistico           |
| `EmbeddingService.php`         | Embedding semantici            |

### 3.5 EGI Core Services

| Service                            | Scopo                   |
| ---------------------------------- | ----------------------- |
| `EgiMintingService.php`            | Minting EGI su Algorand |
| `EgiMintingOrchestrator.php`       | Orchestrazione minting  |
| `EgiPreMintManagementService.php`  | Gestione pre-mint       |
| `EgiPurchaseWorkflowService.php`   | Workflow acquisto       |
| `EgiAvailabilityService.php`       | Disponibilità EGI       |
| `EgiMetadataBuilderService.php`    | Builder metadata        |
| `EgiOracleService.php`             | Oracle prezzi           |
| `EgiSmartContractService.php`      | Smart contract          |
| `EgiLivingSubscriptionService.php` | Sottoscrizioni Living   |

### 3.6 Egili Services

| Service                            | Scopo                   |
| ---------------------------------- | ----------------------- |
| `EgiliService.php`                 | Core Egili (37KB)       |
| `EgiliPurchaseWorkflowService.php` | Workflow acquisto Egili |
| `EgiliTransactionService.php`      | Transazioni             |

---

## 4. Enum System (Verificato: `/app/Enums/`)

### 4.1 GDPR Enums

| Enum                              | Scopo                                                                            |
| --------------------------------- | -------------------------------------------------------------------------------- |
| `GdprActivityCategory.php`        | 25+ categorie attività (AUTHENTICATION, GDPR_ACTIONS, BLOCKCHAIN_ACTIVITY, etc.) |
| `PrivacyLevel.php`                | Livelli privacy                                                                  |
| `ConsentStatus.php`               | Stati consenso                                                                   |
| `CookieConsentCategory.php`       | Categorie cookie                                                                 |
| `DataExportStatus.php`            | Stati export                                                                     |
| `GdprRequestStatus.php`           | Stati richieste                                                                  |
| `GdprRequestType.php`             | Tipi richieste                                                                   |
| `GdprNotificationStatus.php`      | Stati notifiche                                                                  |
| `ProcessingRestrictionReason.php` | Motivi restrizione                                                               |
| `ProcessingRestrictionType.php`   | Tipi restrizione                                                                 |

### 4.2 Business Enums

| Enum                          | Scopo                |
| ----------------------------- | -------------------- |
| `EgiType.php`                 | Tipi EGI             |
| `EgiLivingStatus.php`         | Stati EGI Living     |
| `BusinessType.php`            | Tipi business        |
| `SmartContractStatus.php`     | Stati smart contract |
| `PlatformRole.php`            | Ruoli piattaforma    |
| `NotificationHandlerType.php` | Handler notifiche    |
| `NotificationStatus.php`      | Stati notifiche      |
| `UserRoleForInvite.php`       | Ruoli per inviti     |

---

## 5. Jobs & Events

### 5.1 Jobs (Verificato: `/app/Jobs/`)

| Job                          | Scopo                  |
| ---------------------------- | ---------------------- |
| `MintEgiJob.php`             | Minting asincrono EGI  |
| `ProcessEgiMintingJob.php`   | Processing minting     |
| `TokenizePaActJob.php`       | Tokenizzazione atti PA |
| `ProcessDocumentJob.php`     | Elaborazione documenti |
| `ProcessDataExport.php`      | Export dati GDPR       |
| `ProcessChunkedAnalysis.php` | Analisi chunked        |
| `ExecuteScraperJob.php`      | Web scraping           |

### 5.2 Events (Verificato: `/app/Events/`)

| Event                    | Scopo                     |
| ------------------------ | ------------------------- |
| `PriceUpdated.php`       | Aggiornamento prezzi      |
| `StatsUpdated.php`       | Aggiornamento statistiche |
| `UserWelcomeUpdated.php` | Welcome utente            |

---

## 6. Integrazioni Esterne

### 6.1 Ultra Ecosystem (Pacchetti Interni)

Configurati in `composer.json`:

| Package                           | Repository                                  |
| --------------------------------- | ------------------------------------------- |
| `ultra/egi-module`                | path: `packages/ultra/egi-module`           |
| `ultra/ultra-error-manager`       | GitHub: AutobookNft/UltraErrorManager       |
| `ultra/ultra-log-manager`         | GitHub: AutobookNft/UltraLogManager         |
| `ultra/ultra-translation-manager` | GitHub: AutobookNft/UltraTranslationManager |
| `ultra/ultra-config-manager`      | GitHub: AutobookNft/UltraConfigManager      |
| `ultra/ultra-upload-manager`      | GitHub: AutobookNft/UltraUploadManager      |

### 6.2 EGI-HUB (Nuovo - Nov 2025)

| Package           | Path                                  |
| ----------------- | ------------------------------------- |
| `florenceegi/hub` | path: `/home/fabio/EGI-HUB` (symlink) |

**Scopo**: Layer di coordinamento centrale per ecosistema FlorenceEGI. Fornisce:

-   `Aggregation` model per aggregazioni P2P tra tenant
-   `AggregationMember` model per membership
-   `HasAggregations` trait usato da `Tenant.php`

### 6.3 Algorand Blockchain

```env
ALGORAND_NETWORK=testnet
ALGORAND_API_URL=https://testnet-api.algonode.cloud
ALGORAND_INDEXER_URL=https://testnet-idx.algonode.cloud
ALGORAND_TREASURY_ADDRESS="TF67P6XRLQJWBJSFIZFMTNW574VP5XWZMTH3ONP5JQLKHRWDG5IIZXLG7A"
```

---

## 7. Multi-Tenancy

### 7.1 Model Tenant

Il model `Tenant.php` è integrato con EGI-HUB:

```php
class Tenant extends Model
{
    use HasFactory, SoftDeletes, HasAggregations;

    protected $table = 'tenants';

    protected $fillable = [
        'name', 'slug', 'code', 'entity_type',
        'email', 'phone', 'address', 'vat_number',
        'settings', 'is_active', 'trial_ends_at',
        'subscription_ends_at', 'notes',
    ];
}
```

### 7.2 Aggregazioni P2P

Tramite `HasAggregations` trait, i tenant possono:

-   Creare aggregazioni consensuali
-   Proporre/accettare membership
-   Condividere dati tra tenant affiliati

---

## 8. Testing

### 8.1 Struttura Test (Verificato: `/tests/`)

| Cartella      | Contenuto       |
| ------------- | --------------- |
| `Feature/`    | Test funzionali |
| `Unit/`       | Test unitari    |
| `debug/`      | Script debug    |
| `php_legacy/` | Test legacy     |

**Totale file test**: 85

### 8.2 Framework

-   **PHPUnit** ^11.0.1
-   **Mockery** ^1.6
-   **Laravel Pail** ^1.1 (log viewer)

---

## 9. Branches Attivi

```
* main (branch corrente)
  dev
  feature/auction-module-debug
  feature/dual-architecture
  feature/smart-contract-debug
  feature/wallet-security-module
  backup-before-split-20251015-131203
```

---

## 10. Ultimi Sviluppi (Novembre 2025)

### Ultime Migration (dal 13 Nov 2025)

| Data       | Migration                                                        |
| ---------- | ---------------------------------------------------------------- |
| 2025-11-28 | `add_co_creator_id_to_egis_table`                                |
| 2025-11-25 | `create_natan_tutor_actions_table`                               |
| 2025-11-21 | `create_invoices_table`, `invoice_items`, `invoice_aggregations` |
| 2025-11-21 | `add_payment_processing_consent_type`                            |
| 2025-11-21 | `add_collection_subscription_to_ai_credits_transactions`         |
| 2025-11-20 | `correct_epp_project_associations`                               |
| 2025-11-19 | `fix_frangette_collection_visibility_and_link`                   |
| 2025-11-17 | `add_iban_to_user_data_tables`                                   |
| 2025-11-13 | `add_payment_by_egili_to_egis_table`                             |
| 2025-11-13 | `create_permission_tables`                                       |
| 2025-11-09 | `create_tenants_table_if_missing`                                |

### Ultimi Commit

1. `1248319b` - Menu Gestisci Account: separata sezione Wallet
2. `78427bd5` - Rimossi user type EPP e Commissioner dal form registrazione
3. `a5e7be49` - WheelMenu: aggiunta rotta e aumentate dimensioni desktop
4. `7d93948f` - Test unitari WalletRedemptionTest
5. `935bb682` - Wallet redemption page fixes

---

## 11. Prossimi Passi

### In Progress

-   [ ] Integrazione completa EGI-HUB con aggregazioni P2P
-   [ ] Sistema di fatturazione (Invoice models creati)
-   [ ] NATAN Tutor Actions

### Da Verificare

-   [ ] Migration aggregations (da EGI-HUB)
-   [ ] Sincronizzazione tenant NATAN_LOC ↔ EGI

---

## 12. Note Tecniche

### 12.1 Database Condiviso

EGI e NATAN_LOC condividono lo stesso database MariaDB:

-   **Database**: `EGI`
-   **Tabelle condivise**: `tenants`, `users`, `aggregations`, `aggregation_members`

### 12.2 Package Condiviso

EGI-HUB è montato come symlink in entrambi i progetti:

-   `/home/fabio/EGI-HUB` → `florenceegi/hub`

---

**Documento Verificato**: Ogni informazione è stata estratta direttamente dalla codebase tramite analisi file system e lettura codice sorgente.

**Autore**: Padmin D. Curtis (OS3.0 Compliant)  
**Data**: 28 Novembre 2025  
**Versione Documento**: 1.0.0
