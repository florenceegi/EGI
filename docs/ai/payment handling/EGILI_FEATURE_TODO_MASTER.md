# 📋 **EGILI SYSTEM - TODO MASTER**

**Versione:** 2.0 - Complete Implementation  
**Data Inizio:** 2025-11-02  
**Autore:** Fabio Cherici + Padmin D. Curtis (AI Partner OS3.0)  
**Documento Architettura:** `EGILI_SYSTEM_ARCHITECTURE_COMPLETE.md`

---

## 🎯 **DECISIONI CONFERMATE**

| # | Decisione | Scelta Finale | Status |
|---|-----------|---------------|--------|
| 1 | **Spend Priority** | Prima Gift (expiring first), poi Lifetime (FIFO) | ✅ CONFERMATO |
| 2 | **Approval Flow** | Sistema prenotazione/scheduling con Egili RESERVED | ✅ CONFERMATO |
| 3 | **Prezzario** | Solo DB dinamico (no config file) | ✅ CONFERMATO |
| 4 | **Conversione Feature→Egili** | 100% valore, nessun limite conversioni | ✅ CONFERMATO |
| 5 | **Hyper Mode** | Admin approval required (futuro: ranking automatico) | ✅ CONFERMATO |

---

## 📊 **PROGRESS OVERVIEW**

```
FASE 1 - Foundation:        [▱▱▱▱▱] 0/5   (0%)
FASE 2 - Pricing & Promo:   [▱▱▱▱] 0/4     (0%)
FASE 3 - Featured/Hyper:    [▱▱▱] 0/3      (0%)
FASE 4 - Admin Panels:      [▱▱▱▱] 0/4     (0%)
FASE 5 - User Experience:   [▱▱▱] 0/3      (0%)

TOTAL: 0/19 tasks completed (0%)
```

---

## 🏗️ **FASE 1 - FOUNDATION (Critical - P0)**

### **Task 1.1: Extend `egili_transactions` Table**
- **Status:** ⚪ NOT STARTED
- **Priority:** P0 (BLOCKING)
- **Effort:** 1h
- **Dependencies:** None

**Subtasks:**
- [ ] Create migration `add_egili_types_to_egili_transactions_table`
- [ ] Add columns:
  - `egili_type` ENUM('lifetime', 'gift') DEFAULT 'lifetime'
  - `expires_at` TIMESTAMP NULL
  - `is_expired` BOOLEAN DEFAULT false
  - `granted_by_admin_id` BIGINT NULL
  - `grant_reason` VARCHAR(255) NULL
  - `priority_order` INT DEFAULT 0
- [ ] Add foreign key: `granted_by_admin_id` → `users(id)`
- [ ] Add indexes:
  - `idx_egili_type` (egili_type)
  - `idx_expires_at` (expires_at, is_expired)
  - `idx_priority` (priority_order)
- [ ] Run migration + verify schema
- [ ] Update `EgiliTransaction` model:
  - Add to `$fillable`
  - Add to `$casts` (expires_at → datetime, is_expired → boolean)
  - Add `grantedByAdmin()` relationship

**Acceptance Criteria:**
- ✅ Migration runs without errors
- ✅ All columns created with correct types
- ✅ Indexes present
- ✅ Model updated with casts and relationships

---

### **Task 1.2: Extend `EgiliService` - Gift & Priority Logic**
- **Status:** ⚪ NOT STARTED
- **Priority:** P0 (BLOCKING)
- **Effort:** 4h
- **Dependencies:** Task 1.1

**Subtasks:**
- [ ] Method: `grantGift(User $user, int $amount, int $expirationDays, string $reason, User $admin)`
  - Create transaction with `egili_type = 'gift'`
  - Set `expires_at = now() + $expirationDays`
  - Set `granted_by_admin_id`
  - Update wallet balance
  - GDPR audit: `GdprActivityCategory::EGILI_GIFT_GRANTED`
  - Return `EgiliTransaction`

- [ ] Method: `spendWithPriority(User $user, int $amount, string $reason, string $category)`
  - Query available Egili (lifetime + non-expired gift)
  - Order by:
    ```sql
    ORDER BY 
      CASE WHEN egili_type = 'gift' THEN 0 ELSE 1 END ASC,  -- Gift first
      CASE WHEN egili_type = 'gift' THEN expires_at END ASC, -- Expiring first
      CASE WHEN egili_type = 'lifetime' THEN created_at END ASC -- FIFO
    ```
  - Loop and consume sources
  - Create transaction records for each source
  - GDPR audit for each consumption
  - Return array of transactions

- [ ] Method: `expireGiftEgili()`
  - Find gift Egili with `expires_at < now()` AND `is_expired = false`
  - Update `is_expired = true`
  - Subtract from wallet balance
  - GDPR audit: `GdprActivityCategory::EGILI_GIFT_EXPIRED`
  - Return count expired

- [ ] Method: `getBalanceBreakdown(User $user)`
  - Return array:
    ```php
    [
      'total' => 5000,
      'lifetime' => 3000,
      'gift' => 2000,
      'gift_expiring_soon' => 500, // next 7 days
    ]
    ```

- [ ] Update existing `spend()` method to use `spendWithPriority()` internally

**Acceptance Criteria:**
- ✅ All methods implemented with OS3 compliance (DI, ULM, UEM, GDPR)
- ✅ Unit tests pass for all scenarios
- ✅ Priority logic verified (Gift first, Lifetime after)
- ✅ Expiration cron tested

---

### **Task 1.3: Create `FeatureCreditService`**
- **Status:** ⚪ NOT STARTED
- **Priority:** P0 (BLOCKING)
- **Effort:** 3h
- **Dependencies:** Task 1.2

**Subtasks:**
- [ ] Create `app/Services/FeatureCreditService.php`
- [ ] Implement methods:

```php
/**
 * Purchase feature credits
 * Spends Egili, creates UserFeaturePurchase record
 */
public function purchaseCredits(
    User $user,
    string $featureCode,
    int $quantity = 1
): UserFeaturePurchase;

/**
 * Consume one credit
 * Finds first available purchase, increments quantity_used
 */
public function consumeCredit(
    User $user,
    string $featureCode,
    array $usageMetadata = []
): bool;

/**
 * Get available credits (purchased - used)
 */
public function getAvailableCredits(
    User $user,
    string $featureCode
): int;

/**
 * Check if user has feature (lifetime or available credits)
 */
public function hasFeature(
    User $user,
    string $featureCode
): bool;

/**
 * Convert unused credits to Egili (100% value)
 * No limits on conversions
 */
public function convertToEgili(
    User $user,
    string $featureCode,
    int $quantity
): array; // ['egili_refunded' => 500, 'credits_converted' => 10]

/**
 * Get user's feature purchases history
 */
public function getUserFeatures(User $user): Collection;
```

- [ ] Add GDPR audit for:
  - `FEATURE_PURCHASED`
  - `FEATURE_CONSUMED`
  - `FEATURE_CREDITS_CONVERTED`

**Acceptance Criteria:**
- ✅ All methods implemented with OS3 compliance
- ✅ Atomic transactions for purchase/consume
- ✅ Conversion logic verified (100% refund)
- ✅ GDPR audit trail complete

---

### **Task 1.4: Extend `user_feature_purchases` Table**
- **Status:** ⚪ NOT STARTED
- **Priority:** P0 (BLOCKING)
- **Effort:** 1h
- **Dependencies:** None

**Subtasks:**
- [ ] Create migration `add_scheduling_fields_to_user_feature_purchases_table`
- [ ] Add columns:
  - `is_lifetime` BOOLEAN DEFAULT false
  - `egili_reserved` INT NULL (per approval flow)
  - `status` ENUM('active', 'pending_approval', 'scheduled', 'approved', 'rejected', 'expired') DEFAULT 'active'
  - `scheduled_slot_start` TIMESTAMP NULL
  - `scheduled_slot_end` TIMESTAMP NULL
  - `approved_by_admin_id` BIGINT NULL
  - `approved_at` TIMESTAMP NULL
  - `rejection_reason` TEXT NULL
  - `usage_metadata` JSON NULL
- [ ] Add foreign key: `approved_by_admin_id` → `users(id)`
- [ ] Add indexes:
  - `idx_status` (status)
  - `idx_pending` (status, created_at) -- per admin queue
  - `idx_scheduled` (scheduled_slot_start, scheduled_slot_end)
- [ ] Update `UserFeaturePurchase` model:
  - Add to `$fillable`
  - Add to `$casts`
  - Add `approvedByAdmin()` relationship
  - Add `isPending()`, `isScheduled()`, `isActive()` helper methods

**Acceptance Criteria:**
- ✅ Migration runs without errors
- ✅ Model updated with casts and relationships
- ✅ Helper methods functional

---

### **Task 1.5: Extend `ai_feature_pricing` Table**
- **Status:** ⚪ NOT STARTED
- **Priority:** P0 (BLOCKING)
- **Effort:** 1h
- **Dependencies:** None

**Subtasks:**
- [ ] Create migration `add_feature_types_to_ai_feature_pricing_table`
- [ ] Add columns:
  - `feature_type` ENUM('lifetime', 'consumable', 'temporal') DEFAULT 'consumable'
  - `cost_per_use` INT NULL (Egili per singolo uso)
  - `lifetime_cost` INT NULL (Egili per acquisto lifetime)
  - `requires_admin_approval` BOOLEAN DEFAULT false
  - `max_concurrent_slots` INT NULL (es: max 3 Featured in home)
- [ ] Update `AiFeaturePricing` model:
  - Add to `$fillable`
  - Add to `$casts`
  - Add `isLifetime()`, `isConsumable()`, `isTemporal()` helper methods
  - Add `requiresApproval()` helper

**Acceptance Criteria:**
- ✅ Migration runs without errors
- ✅ Model updated with helpers
- ✅ All existing pricing records still functional

---

## 💰 **FASE 2 - PRICING & PROMOTIONS**

### **Task 2.1: Create `feature_promotions` Table**
- **Status:** ⚪ NOT STARTED
- **Priority:** P1
- **Effort:** 2h
- **Dependencies:** Task 1.5

**Subtasks:**
- [ ] Create migration `create_feature_promotions_table`
- [ ] Add columns (see architecture doc for full schema):
  - Identity: promo_code, promo_name, promo_description
  - Scope: is_global, feature_code, feature_category
  - Discount: discount_type, discount_value
  - Temporal: start_at, end_at
  - Limits: max_uses, max_uses_per_user, current_uses
  - Display: is_active, is_featured, badge_text
  - Admin: created_by_admin_id, admin_notes
  - Stats: total_egili_saved, total_purchases_with_promo
- [ ] Add indexes:
  - `idx_active_dates` (is_active, start_at, end_at)
  - `idx_feature_code` (feature_code)
  - `idx_global` (is_global)
- [ ] Create `FeaturePromotion` model with relationships

**Acceptance Criteria:**
- ✅ Migration runs without errors
- ✅ Model created with relationships
- ✅ Indexes present

---

### **Task 2.2: Create `FeaturePricingService`**
- **Status:** ⚪ NOT STARTED
- **Priority:** P1
- **Effort:** 3h
- **Dependencies:** Task 2.1

**Subtasks:**
- [ ] Create `app/Services/FeaturePricingService.php`
- [ ] Implement `calculatePrice(string $featureCode, User $user, int $quantity = 1)`
  - Get base price from `ai_feature_pricing`
  - Apply tier discount (if user has tier)
  - Find active promotions
  - Apply best promotion (highest discount%)
  - Return pricing breakdown:
    ```php
    [
      'base_cost' => 500,
      'tier_discount' => 50,
      'promo_discount' => 100,
      'final_cost' => 350,
      'promo_applied' => 'BLACK_FRIDAY_2025',
      'savings' => 150
    ]
    ```
- [ ] Method: `getActivePromotions(string $featureCode)`
- [ ] Method: `applyBestPromotion(int $basePrice, Collection $promotions)`
- [ ] Unit tests for all discount combinations

**Acceptance Criteria:**
- ✅ All methods implemented
- ✅ Discount stacking works correctly
- ✅ Unit tests pass

---

### **Task 2.3: Create `FeaturePromotionService`**
- **Status:** ⚪ NOT STARTED
- **Priority:** P1
- **Effort:** 3h
- **Dependencies:** Task 2.1

**Subtasks:**
- [ ] Create `app/Services/FeaturePromotionService.php`
- [ ] CRUD methods:
  - `createPromotion(array $data)`
  - `updatePromotion(int $id, array $data)`
  - `deletePromotion(int $id)`
  - `activatePromotion(int $id)`
  - `deactivatePromotion(int $id)`
- [ ] Logic methods:
  - `getActivePromotions(?string $featureCode = null)`
  - `recordPromoUsage(FeaturePromotion $promo, User $user, int $egiliSaved)`
  - `canUsePromo(FeaturePromotion $promo, User $user)`
- [ ] GDPR audit: `FEATURE_PROMO_APPLIED`

**Acceptance Criteria:**
- ✅ CRUD functional
- ✅ Promo limits enforced
- ✅ GDPR audit trail

---

### **Task 2.4: Integrate Dynamic Pricing in Purchase Flow**
- **Status:** ⚪ NOT STARTED
- **Priority:** P1
- **Effort:** 2h
- **Dependencies:** Task 2.2, Task 1.3

**Subtasks:**
- [ ] Update `FeatureCreditService->purchaseCredits()` to use `FeaturePricingService`
- [ ] Update `feature-purchase-modal.blade.php` to show:
  - Base price
  - Tier discount (if applicable)
  - Active promotion (if applicable)
  - Final price
  - Savings
- [ ] Update translations for pricing breakdown
- [ ] Test purchase with/without promo

**Acceptance Criteria:**
- ✅ Dynamic pricing applied in all flows
- ✅ UI shows pricing breakdown
- ✅ Promo recorded correctly

---

## ⭐ **FASE 3 - FEATURED/HYPER SYSTEM**

### **Task 3.1: Extend `egis` Table for Featured/Hyper**
- **Status:** ⚪ NOT STARTED
- **Priority:** P1
- **Effort:** 1h
- **Dependencies:** None

**Subtasks:**
- [ ] Create migration `add_featured_hyper_to_egis_table`
- [ ] Add columns:
  - `featured_until` TIMESTAMP NULL
  - `featured_by_admin_id` BIGINT NULL
  - `hyper_until` TIMESTAMP NULL (verify if exists!)
  - `hyper_activated_at` TIMESTAMP NULL
  - `hyper_by_admin_id` BIGINT NULL
- [ ] Add foreign keys to `users(id)`
- [ ] Add indexes:
  - `idx_featured_active` (featured_until)
  - `idx_hyper_active` (hyper_until)
- [ ] Update `Egi` model:
  - Add to `$casts`
  - Add relationships: `featuredByAdmin()`, `hyperByAdmin()`
  - Add helpers: `isFeatured()`, `isHyper()`, `getFeaturedDaysRemaining()`

**Acceptance Criteria:**
- ✅ Migration runs without errors
- ✅ Model updated with helpers
- ✅ Existing EGI records still functional

---

### **Task 3.2: Create `FeaturedSchedulingService`**
- **Status:** ⚪ NOT STARTED
- **Priority:** P1
- **Effort:** 5h
- **Dependencies:** Task 3.1, Task 1.4

**Subtasks:**
- [ ] Create `app/Services/FeaturedSchedulingService.php`
- [ ] Method: `requestSlot(User $creator, Egi $egi, string $type, Carbon $slotStart, Carbon $slotEnd)`
  - Check Egili balance
  - Calculate cost based on duration
  - Reserve Egili (not spent yet)
  - Create `UserFeaturePurchase` with status `pending_approval`
  - Notification to admin
  - Return request

- [ ] Method: `scheduleSlot(UserFeaturePurchase $request, User $admin, ?string $notes = null)`
  - Check slot availability
  - Update request: status = `scheduled`
  - Set `scheduled_slot_start`, `scheduled_slot_end`
  - Set `approved_by_admin_id`, `approved_at`
  - Notification to creator
  - GDPR audit: `FEATURED_EGI_APPROVED`

- [ ] Method: `rejectRequest(UserFeaturePurchase $request, User $admin, string $reason)`
  - Release reserved Egili
  - Update request: status = `rejected`, `rejection_reason`
  - Notification to creator
  - GDPR audit: `FEATURED_EGI_REJECTED`

- [ ] Method: `activateScheduledSlots()` (cron job)
  - Find scheduled requests with `scheduled_slot_start <= now()`
  - Spend reserved Egili
  - Update EGI: `featured_until = scheduled_slot_end`
  - Update request: status = `active`
  - Return count activated

- [ ] Method: `deactivateExpiredSlots()` (cron job)
  - Find EGIs with `featured_until <= now()`
  - Update EGI: `featured_until = NULL`
  - Return count expired

- [ ] Method: `checkSlotAvailability(string $type, Carbon $slotStart, Carbon $slotEnd)`
  - Count active/scheduled slots for date range
  - Compare with `max_concurrent_slots` from pricing
  - Return availability array

- [ ] Method: `getPendingRequests()`
  - Return Collection of pending requests with EGI eager-loaded

**Acceptance Criteria:**
- ✅ All methods implemented with OS3 compliance
- ✅ Egili reserved/spent logic verified
- ✅ Cron jobs tested
- ✅ Slot availability logic works

---

### **Task 3.3: Create Cron Jobs for Featured/Hyper**
- **Status:** ⚪ NOT STARTED
- **Priority:** P1
- **Effort:** 1h
- **Dependencies:** Task 3.2

**Subtasks:**
- [ ] Update `app/Console/Kernel.php`:
  ```php
  // Activate scheduled slots daily at 00:01
  $schedule->call(function () {
      app(FeaturedSchedulingService::class)->activateScheduledSlots();
  })->dailyAt('00:01');
  
  // Expire featured slots daily at 23:59
  $schedule->call(function () {
      app(FeaturedSchedulingService::class)->deactivateExpiredSlots();
  })->dailyAt('23:59');
  
  // Expire gift Egili daily at 00:05
  $schedule->call(function () {
      app(EgiliService::class)->expireGiftEgili();
  })->dailyAt('00:05');
  ```
- [ ] Test cron execution locally

**Acceptance Criteria:**
- ✅ Cron jobs registered
- ✅ Tested locally with `php artisan schedule:run`

---

## 🎨 **FASE 4 - ADMIN PANELS**

### **Task 4.1: Admin Panel - Pricing Manager**
- **Status:** ⚪ NOT STARTED
- **Priority:** P2
- **Effort:** 6h
- **Dependencies:** Task 1.5

**Subtasks:**
- [ ] Create route: `/enterprise/features/pricing`
- [ ] Create controller: `EnterprisePricingController`
- [ ] Create views:
  - `enterprise/pricing/index.blade.php` (lista features)
  - `enterprise/pricing/edit-modal.blade.php` (form modale)
- [ ] Implement CRUD:
  - List all features (filtri: category, type, status)
  - Edit feature pricing (inline modal)
  - Create new feature
  - Deactivate feature
- [ ] Middleware: `auth`, `role:admin|superadmin`
- [ ] GDPR audit: `FEATURE_PRICING_UPDATED`
- [ ] Add to enterprise sidebar menu

**Acceptance Criteria:**
- ✅ CRUD completo funzionale
- ✅ UI pulita e professionale (PA-grade)
- ✅ Permessi verificati
- ✅ GDPR audit trail

---

### **Task 4.2: Admin Panel - Promotions Manager**
- **Status:** ⚪ NOT STARTED
- **Priority:** P2
- **Effort:** 6h
- **Dependencies:** Task 2.3

**Subtasks:**
- [ ] Create route: `/enterprise/features/promotions`
- [ ] Create controller: `EnterprisePromotionController`
- [ ] Create views:
  - `enterprise/promotions/index.blade.php` (lista promo)
  - `enterprise/promotions/create-modal.blade.php`
  - `enterprise/promotions/edit-modal.blade.php`
- [ ] Implement CRUD per promozioni
- [ ] Preview pricing con sconto applicato
- [ ] Calendar view per promo temporanee
- [ ] Stats per promo: uses, egili saved, conversion rate
- [ ] Middleware: `auth`, `role:admin|superadmin`
- [ ] Add to enterprise sidebar menu

**Acceptance Criteria:**
- ✅ CRUD promo completo
- ✅ Preview funzionale
- ✅ Stats accurate
- ✅ UI professionale

---

### **Task 4.3: Admin Panel - Egili Management**
- **Status:** ⚪ NOT STARTED
- **Priority:** P2
- **Effort:** 5h
- **Dependencies:** Task 1.2

**Subtasks:**
- [ ] Create route: `/enterprise/egili/management`
- [ ] Create controller: `EnterpriseEgiliController`
- [ ] Create views:
  - `enterprise/egili/index.blade.php`
  - `enterprise/egili/grant-modal.blade.php`
  - `enterprise/egili/refund-modal.blade.php`
- [ ] Implement operations:
  - Grant Lifetime Egili
  - Grant Gift Egili (con scadenza)
  - Refund purchase (con note)
  - View all transactions (filtri avanzati)
  - Manual correction (audit trail)
- [ ] Search users by email/username
- [ ] Transaction log view con filtri
- [ ] Middleware: `auth`, `role:superadmin` (solo SuperAdmin!)
- [ ] Add to enterprise sidebar menu

**Acceptance Criteria:**
- ✅ Tutte le operations funzionali
- ✅ Solo SuperAdmin può accedere
- ✅ GDPR audit completo
- ✅ UI professionale

---

### **Task 4.4: Admin Panel - Featured EGI Calendar**
- **Status:** ⚪ NOT STARTED
- **Priority:** P2
- **Effort:** 8h
- **Dependencies:** Task 3.2

**Subtasks:**
- [ ] Create route: `/enterprise/featured/calendar`
- [ ] Create controller: `EnterpriseFeaturedController`
- [ ] Create views:
  - `enterprise/featured/calendar.blade.php` (calendar view)
  - `enterprise/featured/pending-requests.blade.php`
  - `enterprise/featured/approve-modal.blade.php`
  - `enterprise/featured/reject-modal.blade.php`
- [ ] Implement calendar view:
  - Monthly view con slot availability
  - Visual indicators: 🟢 disponibile, 🟡 parziale, 🔴 pieno
  - Click su giorno → dettagli EGI attivi
- [ ] Implement pending requests queue:
  - Lista richieste pending
  - User info, EGI preview, Egili reserved
  - Approve form: select date range, notes
  - Reject form: reason required
- [ ] Stats: revenue per feature, top creators
- [ ] Middleware: `auth`, `role:admin|superadmin`
- [ ] Add to enterprise sidebar menu

**Acceptance Criteria:**
- ✅ Calendar view funzionale
- ✅ Approval/rejection flow completo
- ✅ Notifications a creator
- ✅ Stats accurate

---

## 🎯 **FASE 5 - USER EXPERIENCE**

### **Task 5.1: Pagina "Le mie Feature"**
- **Status:** ⚪ NOT STARTED
- **Priority:** P2
- **Effort:** 5h
- **Dependencies:** Task 1.3

**Subtasks:**
- [ ] Create route: `/account/features`
- [ ] Create controller: `UserFeatureController`
- [ ] Create view: `account/features.blade.php`
- [ ] Sections:
  - **Active Features:** Lifetime + credits disponibili
  - **Usage History:** Log consumo per feature
  - **Purchases History:** Tutte le transazioni
  - **Pending Requests:** Featured/Hyper in pending/scheduled
- [ ] Actions:
  - Convert credits to Egili (modal)
  - Request refund (form)
  - Re-purchase feature
- [ ] UI cards per ogni feature con:
  - Nome, icon, descrizione
  - Status (active/expired)
  - Credits remaining (se consumabile)
  - Expiration date (se temporal)
  - Actions disponibili
- [ ] Add to user menu

**Acceptance Criteria:**
- ✅ Pagina completa e intuitiva
- ✅ Tutte le info accurate
- ✅ Actions funzionali
- ✅ Mobile responsive

---

### **Task 5.2: Feature Conversion UI (Credits → Egili)**
- **Status:** ⚪ NOT STARTED
- **Priority:** P2
- **Effort:** 3h
- **Dependencies:** Task 5.1, Task 1.3

**Subtasks:**
- [ ] Create modal: `convert-credits-modal.blade.php`
- [ ] Form:
  - Select feature to convert
  - Input quantity credits
  - Show Egili equivalente (100%)
  - Confirm button
- [ ] Controller action: `UserFeatureController@convertToEgili`
- [ ] Validations:
  - User ha credits disponibili
  - Quantity > 0 and <= available
- [ ] Success message + updated balance
- [ ] GDPR audit: `FEATURE_CREDITS_CONVERTED`

**Acceptance Criteria:**
- ✅ Conversione funziona
- ✅ 100% refund verificato
- ✅ UI chiara e intuitiva

---

### **Task 5.3: Featured EGI Request UI (Creator Side)**
- **Status:** ⚪ NOT STARTED
- **Priority:** P2
- **Effort:** 4h
- **Dependencies:** Task 3.2

**Subtasks:**
- [ ] Add button on EGI page: "🌟 Richiedi Featured in Home"
- [ ] Create modal: `request-featured-modal.blade.php`
- [ ] Form:
  - Select duration (7/14/30 giorni)
  - Show cost in Egili
  - Date range picker (preferred dates)
  - Note per admin (optional)
  - Check balance, show "Buy Egili" if insufficient
  - Confirm button
- [ ] Controller: `FeaturedRequestController@store`
- [ ] Success message: "Richiesta inviata, aspetta approvazione admin"
- [ ] Notification to admin

**Acceptance Criteria:**
- ✅ Request flow completo
- ✅ Egili reserved correttamente
- ✅ Notification a admin
- ✅ UI professionale

---

## 📋 **GDPR EXTENSIONS REQUIRED**

**File:** `app/Enums/Gdpr/GdprActivityCategory.php`

**Add new cases:**
```php
case FEATURE_PURCHASED = 'feature_purchased';
case FEATURE_CONSUMED = 'feature_consumed';
case FEATURE_PROMO_APPLIED = 'feature_promo_applied';
case FEATURE_CREDITS_CONVERTED = 'feature_credits_converted';
case EGILI_GIFT_GRANTED = 'egili_gift_granted';
case EGILI_GIFT_EXPIRED = 'egili_gift_expired';
case FEATURED_EGI_REQUESTED = 'featured_egi_requested';
case FEATURED_EGI_APPROVED = 'featured_egi_approved';
case FEATURED_EGI_REJECTED = 'featured_egi_rejected';
```

---

## 📊 **METRICS & ANALYTICS (Future Task)**

**Non blocking, ma importante per PA:**

- Revenue per feature (Egili + EUR equivalent)
- Conversion rate (views → purchases)
- Top features trending
- Top spenders
- Promo effectiveness
- Featured EGI ROI

**Sarà Task 4.5 in futuro.**

---

## 🎯 **NEXT STEPS**

1. ✅ FASE 1 completamente (Foundation)
2. ✅ FASE 2 (Pricing & Promo)
3. ✅ FASE 3 (Featured/Hyper)
4. ⚠️ FASE 4 (Admin Panels) - can parallelize
5. ⚠️ FASE 5 (User Experience) - can parallelize

**Estimated Total Effort:** ~60 hours  
**Estimated Timeline:** 2-3 settimane (part-time)

---

**🚀 INIZIAMO DA TASK 1.1!**



