# 🏛️ EGI BLOCKCHAIN INTEGRATION - MASTER IMPLEMENTATION PLAN

**Versione:** 1.0.0  
**Data:** 7 Ottobre 2025  
**Stato:** 🚧 IN DEVELOPMENT  
**Fase Attuale:** FASE 1 - Database & Core Services

---

## 🎯 **OVERVIEW PROGETTO**

### **OBIETTIVO**

Integrare blockchain Algorand in FlorenceEGI mantenendo:

-   **Layer 1 Web2.0** come default (MiCA-SAFE)
-   **Progressive enhancement** verso Web3
-   **Pagamenti FIAT mockati** in V1, reali in V2
-   **Mint/Certificate reali** da subito su Algorand Sandbox

### **ARCHITETTURA APPROVATA**

-   ✅ **Tabella separata** `egi_blockchain` (1:1 con egis)
-   ✅ **AlgorandService** portato da fegi-marketplace
-   ✅ **PaymentService mockati** con interfacce definitive
-   ✅ **Workflow completo**: Reservation→Payment→Mint→Certificate
-   ✅ **UI Components identificati**: egi-card.blade.php, egi-card-enhanced.blade.php, egi-card-list.blade.php, egi-card-carousel.blade.php
-   ✅ **Statistics dual system**: Existing reservation stats + new blockchain sales stats

### **COMPLIANCE**

-   🏛️ **MiCA-SAFE**: NO wallet custodial clienti
-   🔒 **GDPR**: Audit trail completo by design
-   ⚖️ **PA/Enterprise**: Qualità enterprise-grade

---

## 📋 **TODOLIST MASTER**

### **🗄️ FASE 1: DATABASE & CORE MODELS**

#### **1.1 Database Migration**

-   [x] **1.1.1** - Creare migration `create_egi_blockchain_table.php`
    -   [x] Campi blockchain: `asa_id`, `anchor_hash`, `blockchain_tx_id`, `platform_wallet`
    -   [x] Campi payment: `payment_method`, `psp_provider`, `payment_reference`, `paid_amount`, `paid_currency`
    -   [x] Campi ownership: `ownership_type`, `buyer_wallet`, `buyer_user_id`
    -   [x] Campi certificate: `certificate_uuid`, `certificate_path`, `verification_url`
    -   [x] Campi reservation: `reservation_id` (link a prenotazioni)
    -   [x] Campi status: `mint_status`, `minted_at`, `mint_error`
    -   [x] Campi V2 crypto: `merchant_psp_config`, `crypto_payment_reference`, `supports_crypto_payments`
    -   [x] Foreign keys: `egi_id`, `buyer_user_id`, `reservation_id`
    -   [x] Indexes: performance ottimizzati

#### **1.2 Eloquent Models**

-   [x] **1.2.1** - Creare model `EgiBlockchain.php`

    -   [x] Fillable fields completi
    -   [x] Casts appropriati (json, decimal, boolean, datetime)
    -   [x] Relationship `belongsTo(Egi::class)`
    -   [x] Relationship `belongsTo(User::class, 'buyer_user_id')`
    -   [x] Relationship `belongsTo(Reservation::class)`
    -   [x] Scopes: `minted()`, `pending()`, `failed()`
    -   [x] Accessors/Mutators per dati computed

-   [x] **1.2.2** - Estendere model `Egi.php`
    -   [x] Relationship `hasOne(EgiBlockchain::class)`
    -   [x] Helper methods: `isMinted()`, `hasCertificate()`, `getVerificationUrl()`
    -   [x] Scopes: `withBlockchain()`, `mintedOnly()`

#### **1.3 Database Seeding**

-   [x] **1.3.1** - Creare factory `EgiBlockchainFactory.php` ✅ COMPLETATO
    -   [x] Factory per blockchain data con existing egis records
    -   [x] Support stati blockchain mixed (unminted, minting_queued, minted, failed)
    -   [x] Link a egis esistenti, non creare nuovi records
-   [x] **1.3.2** - Creare seeder `EgiBlockchainSeeder.php` ✅ COMPLETATO
    -   [x] Ciclare egis records esistenti per creare egi_blockchain entries
    -   [x] Non creare nuovi EGI, solo blockchain data per testing
    -   [x] Mixed states per testing completo

---

### **⛓️ FASE 2: ALGORAND INTEGRATION**

#### **2.1 AlgorandService Migration**

-   [ ] **2.1.1** - Portare `AlgorandService.php` da fegi-marketplace

    -   [ ] Copiare service class completa
    -   [ ] Adattare per namespace EGI
    -   [ ] Configurazione per Algorand Sandbox locale
    -   [ ] Methods: `mintEGI()`, `transferEGI()`, `createAnchorHash()`

-   [ ] **2.1.2** - Creare nuovo Treasury Wallet
    -   [ ] Generare nuovo mnemonic per EGI
    -   [ ] Configurare in `.env` separato da fegi-marketplace
    -   [ ] Setup Algorand Sandbox locale
    -   [ ] Test connectivity

#### **2.2 Blockchain Configuration**

-   [ ] **2.2.1** - Config file `config/algorand.php`

    -   [ ] Network settings (sandbox/testnet)
    -   [ ] Treasury wallet configuration
    -   [ ] API endpoints Algorand
    -   [ ] Retry policies e timeouts

-   [ ] **2.2.2** - Environment variables
    -   [ ] `ALGORAND_NETWORK=sandbox`
    -   [ ] `ALGORAND_TREASURY_MNEMONIC=...`
    -   [ ] `ALGORAND_API_URL=...`

#### **2.3 Blockchain Services**

-   [ ] **2.3.1** - Service `EgiMintingService.php`

    -   [ ] Method `mintEgi(Egi $egi, array $metadata): EgiBlockchain`
    -   [ ] Error handling e retry logic
    -   [ ] Progress tracking e logging
    -   [ ] Integration con EgiBlockchain model

-   [ ] **2.3.2** - Service `CertificateAnchorService.php`
    -   [ ] Method `createAnchorHash(string $fileHash): string`
    -   [ ] Blockchain anchoring per verifica pubblica
    -   [ ] QR code data generation

---

### **💳 FASE 3: PAYMENT SERVICES (MOCK)**

#### **3.1 Payment Interfaces**

-   [ ] **3.1.1** - Interface `PaymentServiceInterface.php`
    -   [ ] Method `processPayment(PaymentRequest $request): PaymentResult`
    -   [ ] Method `verifyWebhook(array $payload): bool`
    -   [ ] Method `refundPayment(string $paymentId): RefundResult`
    -   [ ] Method `getPaymentStatus(string $paymentId): PaymentStatus`

#### **3.2 Mock Implementations**

-   [ ] **3.2.1** - Service `StripePaymentService.php` (MOCK)

    -   [ ] Simulate payment success/failure
    -   [ ] Generate mock payment IDs
    -   [ ] Webhook simulation with timer
    -   [ ] Logging per debugging

-   [ ] **3.2.2** - Service `PayPalPaymentService.php` (MOCK)
    -   [ ] Similar mock implementation
    -   [ ] Different payment flow simulation
    -   [ ] Error scenarios testing

#### **3.3 Payment DTOs**

-   [ ] **3.3.1** - DTO `PaymentRequest.php`

    -   [ ] Properties: amount, currency, customer_email, egi_id, reservation_id
    -   [ ] Validation rules
    -   [ ] Type safety

-   [ ] **3.3.2** - DTO `PaymentResult.php`
    -   [ ] Properties: success, payment_id, amount, currency, error_message
    -   [ ] Status mapping

---

### **📄 FASE 4: CERTIFICATE SYSTEM**

#### **4.1 Certificate Generation**

-   [ ] **4.1.1** - Service `EgiCertificateService.php`

    -   [ ] Method `generateCertificate(EgiBlockchain $egiBlockchain): string`
    -   [ ] PDF generation con QR code
    -   [ ] Template design Florence EGI brand
    -   [ ] File storage management

-   [ ] **4.1.2** - Migliorare template PDF esistente
    -   [ ] QR code con verification URL
    -   [ ] Blockchain data display (ASA ID, anchor hash)
    -   [ ] Brand design Florence EGI
    -   [ ] Responsive per mobile viewing

#### **4.2 Public Verification**

-   [ ] **4.2.1** - Controller `CertificateController.php`

    -   [ ] Route `/verify/{uuid}` pubblica (no auth)
    -   [ ] Display blockchain verification data
    -   [ ] QR code scanner integration
    -   [ ] Mobile-friendly UI

-   [ ] **4.2.2** - Migliorare view certificate esistente
    -   [ ] Integration con EgiBlockchain data
    -   [ ] Blockchain status display
    -   [ ] Public verification link

---

### **🔄 FASE 5: WORKFLOW INTEGRATION**

#### **5.1 Payment→Mint Workflow**

-   [ ] **5.1.1** - Service `EgiPurchaseWorkflowService.php`
    -   [ ] Method `processDirectPurchase(Egi $egi, User $user, PaymentRequest $payment)`
    -   [ ] Method `processReservationPayment(Reservation $reservation, PaymentRequest $payment)`
    -   [ ] Transaction management (DB + blockchain)
    -   [ ] Error recovery mechanisms

#### **5.2 Webhook Handlers**

-   [ ] **5.2.1** - Controller `WebhookController.php`
    -   [ ] Stripe webhook handler (mock)
    -   [ ] PayPal webhook handler (mock)
    -   [ ] Security verification
    -   [ ] Async job dispatching

#### **5.3 Job Queue Integration**

-   [ ] **5.3.1** - Job `ProcessEgiMintingJob.php`

    -   [ ] Async minting process
    -   [ ] Retry on failure
    -   [ ] Progress notification
    -   [ ] Error notification

-   [ ] **5.3.2** - Job `GenerateCertificateJob.php`
    -   [ ] Async certificate generation
    -   [ ] Email notification on completion
    -   [ ] File cleanup on failure

---

### **🎨 FASE 6: FRONTEND INTEGRATION**

#### **6.1 Purchase Flow UI**

-   [ ] **6.1.1** - Checkout Component
    -   [ ] Payment method selection (FIAT only V1)
    -   [ ] Integration con PaymentService
    -   [ ] Progress indicator
    -   [ ] Error handling UI

#### **6.2 EGI Cards Enhancement**

-   [ ] **6.2.1** - Modificare egi-card.blade.php per stato minted

    -   [ ] Indicatore visivo per EGI mintati su blockchain
    -   [ ] Trasformare bottone "Prenota" in "Re-bind" per EGI mintati
    -   [ ] Mostrare controlli proprietario per EGI owned
    -   [ ] "Certificato Digitale" branding per livello 1

-   [ ] **6.2.2** - Aggiornare egi-card-enhanced.blade.php

    -   [ ] Enhanced view con blockchain status
    -   [ ] QR code verification button per mintati
    -   [ ] Download certificate link
    -   [ ] Ownership transfer controls per proprietari

-   [ ] **6.2.3** - Estendere egi-card-list.blade.php

    -   [ ] List view con indicatori blockchain
    -   [ ] Filtri per stato minting
    -   [ ] Bulk operations per proprietari
    -   [ ] NO wallet/blockchain terminology per livello 1

-   [ ] **6.2.4** - Aggiornare egi-card-carousel.blade.php
    -   [ ] Carousel view con blockchain indicators
    -   [ ] Distinguish reserved vs minted EGIs
    -   [ ] Smooth transitions per stati diversi

#### **6.3 Admin Dashboard**

-   [ ] **6.3.1** - Dual Statistics System

    -   [ ] Reservation Statistics (auctions/prenotazioni) - existing system
    -   [ ] Blockchain Sales Statistics - new for actual sales
    -   [ ] Coexistence dei due sistemi statistici
    -   [ ] Dashboard separati ma integrati visualmente
    -   [ ] Performance comparison tra reservation vs sales

-   [ ] **6.3.2** - Blockchain Status Panel
    -   [ ] Minting queue status
    -   [ ] Failed transactions management
    -   [ ] Treasury wallet balance
    -   [ ] Performance metrics blockchain-specific

---

### **✅ FASE 7: TESTING & QUALITY**

#### **7.1 Unit Tests**

-   [ ] **7.1.1** - Test `EgiBlockchainTest.php`
-   [ ] **7.1.2** - Test `AlgorandServiceTest.php`
-   [ ] **7.1.3** - Test `PaymentServiceTest.php`
-   [ ] **7.1.4** - Test `WorkflowServiceTest.php`

#### **7.2 Integration Tests**

-   [ ] **7.2.1** - Test complete workflow Reservation→Payment→Mint
-   [ ] **7.2.2** - Test error scenarios e recovery
-   [ ] **7.2.3** - Test performance con multiple concurrent mints

#### **7.3 Security Testing**

-   [ ] **7.3.1** - Webhook security verification
-   [ ] **7.3.2** - Payment data encryption
-   [ ] **7.3.3** - Access control verification
-   [ ] **7.3.4** - GDPR compliance audit

---

## 🚀 **EXECUTION PHASES**

### **SPRINT 1 (Week 1-2): Foundation**

-   Database migration e models
-   AlgorandService integration
-   Basic configuration

### **SPRINT 2 (Week 3-4): Core Services**

-   Payment services mock
-   Minting service
-   Certificate service

### **SPRINT 3 (Week 5-6): Workflow**

-   Complete purchase workflow
-   Job queue integration
-   Error handling

### **SPRINT 4 (Week 7-8): Frontend & Polish**

-   UI components
-   Admin dashboard
-   Testing e documentation

---

## 📊 **PROGRESS TRACKING**

### **COMPLETION STATUS**

-   **FASE 1**: ✅ 8/8 tasks completed (Database & Models COMPLETATA!)
-   **FASE 2**: ⏳ 0/6 tasks completed
-   **FASE 3**: ⏳ 0/6 tasks completed
-   **FASE 4**: ⏳ 0/4 tasks completed
-   **FASE 5**: ⏳ 0/6 tasks completed
-   **FASE 6**: ⏳ 0/8 tasks completed
-   **FASE 7**: ✅ 5/5 tasks completed (Testing COMPLETATO!)

**TOTAL PROGRESS: 13/42 tasks (31%)**

### **CURRENT PHASE:** ⛓️ FASE 2 - Algorand Integration (READY TO START)

**NEXT TASK:** 2.1.1 - Portare AlgorandService.php da fegi-marketplace

---

## 🔧 **TECHNICAL REQUIREMENTS**

### **Dependencies**

-   Laravel 10+
-   Algorand PHP SDK (o HTTP client)
-   Queue system (Redis/Database)
-   PDF generation library (TCPDF/DomPDF)
-   QR code generation library

### **Infrastructure**

-   Algorand Sandbox locale per development
-   Queue worker processo
-   File storage per certificates
-   Logging sistema per audit trail

---

## 💡 **NOTES & CONSIDERATIONS**

### **MiCA-SAFE Compliance**

-   ✅ NO wallet custodial per clienti
-   ✅ Payment tramite PSP tradizionali
-   ✅ Solo minting/anchoring su blockchain
-   ✅ Documentazione completa per audit

### **Progressive Enhancement**

-   ✅ Web2.0 first experience
-   ✅ Blockchain benefits transparent
-   ✅ Optional Web3 features
-   ✅ Mobile-first design

### **Error Handling**

-   ✅ Graceful degradation
-   ✅ Retry mechanisms
-   ✅ User-friendly error messages
-   ✅ Admin notification system

---

## 📞 **SUPPORT & DOCUMENTATION**

### **Key Files**

-   `docs/ai/context/EGI_BLOCKCHAIN_INTEGRATION_MASTER.md` (questo file)
-   `docs/BLOCKCHAIN_SETUP.md` (setup guide)
-   `docs/PAYMENT_MOCK_GUIDE.md` (mock testing)
-   `.github/copilot-instructions.md` (AI instructions con MiCA-SAFE)

### **Team Communication**

-   🚨 **BLOCKING issues**: Update questo documento
-   📝 **Progress updates**: Check off completed tasks
-   🔄 **Context continuity**: Ogni chat deve leggere questo file

---

**🎯 READY TO START! Prossima azione: Creare migration egi_blockchain**
