# 🏛️ **EGILI SYSTEM - ARCHITETTURA COMPLETA**

**Versione:** 2.0 - Complete Architecture  
**Data:** 2025-11-02  
**Autore:** Fabio Cherici + Padmin D. Curtis (AI Partner OS3.0)  
**Fonte:** Brainstorming sessione 2025-11-02 + egili_feature_promotion_system.md

---

## 📊 **EXECUTIVE SUMMARY**

FlorenceEGI implementa un'economia interna basata su **Egili**, con due tipologie distinte:
1. **Egili Lifetime** (acquistati dall'utente - EUR/Crypto → Egili)
2. **Egili Gift** (donati dalla piattaforma - con scadenza temporale)

Le **feature** sono **SEMPRE visibili a tutti** (no Spatie permissions blocking), ma:
- Se hai Egili → consumi e usi
- Se NON hai Egili → modale "Buy More Egili"

Alcune feature sono **lifetime** (es: EGI Living), altre **consumabili** (es: Descrizioni AI).

**Admin/SuperAdmin gestiscono tutto**: prezzi, promo, analytics, rimborsi, featured EGI.

---

## 🎯 **PRINCIPI ARCHITETTURALI FONDAMENTALI**

### **1. VISIBILITY-FIRST (NO BLOCKING)**
```
❌ VECCHIA LOGICA (Spatie blocking):
   if (!$user->can('feature-x')) {
       return abort(403, 'Access denied');
   }

✅ NUOVA LOGICA (Egili-based):
   // Feature SEMPRE visibile
   if (click_feature) {
       if ($egiliService->canSpend($user, $featureCost)) {
           consumeAndUse();
       } else {
           openEgiliPurchaseModal(); // Invito all'acquisto
       }
   }
```

**RATIONALE:** Trasforma ogni limite in opportunità d'acquisto, non in blocco.

---

### **2. DUE TIPOLOGIE EGILI**

| Tipo | Caratteristiche | Acquisizione | Scadenza | Priorità Consumo |
|------|-----------------|--------------|----------|------------------|
| **Lifetime** | Comprati dall'user | EUR/Crypto purchase | ♾️ MAI | 🔴 Prima (FIFO) |
| **Gift** | Donati da piattaforma | Admin grant, rewards | ⏰ N giorni | 🟢 Dopo (LIFO) |

**ESEMPIO:**
```
User balance: 5000 Egili
├─ 3000 Lifetime (acquistati 01/10/2025)
└─ 2000 Gift (donati 01/11/2025, scadono 31/12/2025)

User spende 1500 Egili:
→ Prima consuma Lifetime: 3000 - 1500 = 1500 Lifetime
→ Gift rimangono: 2000 Gift
```

**TABELLE:**
```sql
egili_transactions:
├─ transaction_type: 'purchase' | 'gift'
├─ expires_at: NULL (lifetime) | TIMESTAMP (gift)
├─ is_expired: BOOLEAN
└─ priority_order: INT (per FIFO/LIFO logic)
```

---

### **3. DUE TIPOLOGIE FEATURE**

| Tipo | Esempio | Logica | Tracking |
|------|---------|--------|----------|
| **Lifetime** | EGI Living | Compri 1 volta → per sempre | NO consumo, YES purchase record |
| **Consumabile** | Descrizione AI | Ogni uso costa | YES consumo, YES usage tracking |

**STRUTTURA:**
```sql
ai_feature_pricing:
├─ feature_type: 'lifetime' | 'consumable'
├─ cost_per_use: INT (Egili per singolo uso, se consumabile)
├─ lifetime_cost: INT (Egili per acquisto lifetime)
└─ is_reusable: BOOLEAN

user_feature_purchases:
├─ feature_code
├─ quantity_purchased (se consumabile)
├─ quantity_used (se consumabile)
├─ is_lifetime (boolean)
└─ expires_at (NULL se lifetime)
```

**ESEMPI:**
```php
// EGI Living (LIFETIME)
'egi_living_subscription' => [
    'feature_type' => 'lifetime',
    'lifetime_cost' => 500,
    'is_reusable' => false,
]

// Descrizione AI (CONSUMABILE)
'ai_description_generation' => [
    'feature_type' => 'consumable',
    'cost_per_use' => 50,
    'is_reusable' => true,
    'stackable' => true, // Acquisti multipli si sommano
]

// Featured Home (TEMPORANEO CONSUMABILE)
'featured_home_7days' => [
    'feature_type' => 'consumable',
    'cost_per_use' => 1000,
    'duration_hours' => 168, // 7 giorni
    'is_reusable' => true,
    'requires_admin_approval' => true, // Spazi limitati!
]
```

---

### **4. ADMIN/MERCHANT PANEL - GESTIONE COMPLETA**

**REQUISITI FUNZIONALI:**

#### **A) Gestione Prezzario**
```
Route: /enterprise/features/pricing
Access: Admin + SuperAdmin

CRUD Features:
├─ Create new feature
├─ Edit pricing (cost_egili, tier_pricing)
├─ Set discount_percentage
├─ Configure duration/limits
└─ Activate/Deactivate feature

UI: Tabella editabile + form modale
```

#### **B) Gestione Promozioni**
```
Route: /enterprise/features/promotions
Access: Admin + SuperAdmin

CRUD Promotions:
├─ Create promo temporanea (feature-specific o globale)
├─ Set discount% + date range
├─ Preview pricing con sconto
└─ Activate/Deactivate promo

UI: Calendar view + promo cards
```

#### **C) Analytics Dashboard**
```
Route: /enterprise/features/analytics
Access: Admin + SuperAdmin

Metriche:
├─ Revenue per feature (Egili + EUR)
├─ Numero acquisti per feature
├─ Conversion rate (views → purchases)
├─ Trending features (last 30 days)
├─ User segmentation (top spenders)
└─ Export CSV/Excel

UI: Charts + tabelle + filtri date
```

#### **D) Gestione Egili**
```
Route: /enterprise/egili/management
Access: SuperAdmin only

Operations:
├─ Grant Egili (lifetime o gift con scadenza)
├─ Deduct Egili (penalità)
├─ Refund purchase (con note admin)
├─ View all transactions (filtri avanzati)
└─ Manual correction (con audit trail)

UI: Action cards + transaction log
```

#### **E) Featured EGI Management**
```
Route: /enterprise/featured/calendar
Access: Admin + SuperAdmin

Gestione Spazi Limitati:
├─ Calendar view (chi, quando, quanto)
├─ Approve/Reject requests creator
├─ Manual assignment (admin decision)
├─ Revenue tracking
└─ Queue management (se spazi pieni)

LOGIC:
1. Creator clicca "Feature: Visibility 7 giorni"
2. Check saldo Egili
3. Se OK → crea REQUEST (status: pending_approval)
4. Admin vede request + calendar availability
5. Admin approva → featured_until settato, Egili spesi
6. Admin nega → Egili NON spesi, notification user

UI: Drag&drop calendar + request queue
```

---

## 🔄 **FLUSSI OPERATIVI COMPLETI**

### **FLUSSO 1: Acquisto Feature LIFETIME (es: EGI Living)**

```
1. User clicca "EGI Living" su EGI page
   ↓
2. Check: userHasFeature('egi_living_subscription')?
   YES → Feature già attiva, skip purchase
   NO → Continua
   ↓
3. Get pricing da ai_feature_pricing
   cost_egili = 500
   ↓
4. Check: egiliService->canSpend(user, 500)?
   YES → Continua al passo 6
   NO → Passo 5
   ↓
5. Modal "Egili insufficienti"
   - Mostra deficit
   - Link "Buy More Egili"
   - User compra Egili (FIAT/Crypto)
   - Torna al passo 4
   ↓
6. Atomic transaction:
   a) egiliService->spend(user, 500, 'egi_living', 'purchase')
   b) UserFeaturePurchase::create([
        'user_id' => user->id,
        'feature_code' => 'egi_living_subscription',
        'is_lifetime' => true,
        'quantity_purchased' => 1,
        'amount_paid_egili' => 500,
      ])
   c) Update EGI record:
      egi->egi_living_enabled = true
   d) GDPR audit trail
   ↓
7. Success message + feature attivata ✅
```

---

### **FLUSSO 2: Uso Feature CONSUMABILE (es: AI Description)**

```
1. User clicca "Generate Description" su EGI edit
   ↓
2. Get pricing da ai_feature_pricing
   cost_per_use = 50 Egili
   ↓
3. Check: user ha crediti disponibili?
   
   Query: UserFeaturePurchase::where('feature_code', 'ai_description')
          ->where('quantity_used', '<', 'quantity_purchased')
          ->exists()
   
   YES → Usa crediti esistenti (passo 6)
   NO → Deve comprare (passo 4)
   ↓
4. Check: egiliService->canSpend(user, 50)?
   YES → Continua
   NO → Modal "Buy More Egili"
   ↓
5. Purchase new credits:
   a) Spend 50 Egili
   b) UserFeaturePurchase::create([
        'feature_code' => 'ai_description',
        'quantity_purchased' => 1,
        'quantity_used' => 0, // Verrà incrementato al passo 6
        'is_lifetime' => false,
      ])
   ↓
6. Consume credit:
   a) Trova primo purchase con available > 0
   b) Increment quantity_used
   c) Log usage in egili_transactions (se nuovo acquisto)
   d) Call AI service per generare descrizione
   ↓
7. Description generata + credit consumato ✅
```

---

### **FLUSSO 3: Featured EGI (Admin Approval Required)**

```
1. Creator clicca "Promuovi in Home - 7 giorni"
   ↓
2. Get pricing: 1000 Egili (7 giorni visibility)
   ↓
3. Check: egiliService->canSpend(user, 1000)?
   NO → Modal "Buy Egili"
   YES → Continua
   ↓
4. Egili RESERVED (non ancora spesi):
   UserFeaturePurchase::create([
     'feature_code' => 'featured_home_7days',
     'status' => 'pending_approval', // ← KEY!
     'egili_reserved' => 1000,
     'requested_at' => now(),
   ])
   ↓
5. Admin riceve notification
   ↓
6. Admin apre /enterprise/featured/calendar
   - Vede request
   - Check calendar availability (spazi limitati)
   - Decide periodo (dal 10/11 al 17/11)
   ↓
7a. APPROVAL:
   a) Spend reserved Egili
   b) Update purchase: status = 'approved'
   c) Update EGI: featured_until = '2025-11-17 23:59:59'
   d) Notification user: "Featured approved!"
   
7b. REJECTION:
   a) Release reserved Egili (no charge)
   b) Update purchase: status = 'rejected'
   c) Notification user: "Featured rejected (reason)"
   ↓
8. EGI appare in home (se approved) ✅
```

---

## 🗄️ **SCHEMA DATABASE COMPLETO**

### **1. egili_transactions (ESISTENTE - DA ESTENDERE)**

```sql
ALTER TABLE egili_transactions ADD COLUMN:
├─ egili_type ENUM('lifetime', 'gift') DEFAULT 'lifetime'
├─ expires_at TIMESTAMP NULL (solo per gift)
├─ is_expired BOOLEAN DEFAULT false
├─ granted_by_admin_id BIGINT NULL (se gift manuale)
├─ grant_reason VARCHAR(255) NULL
└─ priority_order INT DEFAULT 0 (per FIFO/LIFO logic)
```

**LOGIC:**
- Lifetime: expires_at = NULL, priority_order ASC (FIFO)
- Gift: expires_at = NOW + N days, priority_order DESC (LIFO expiring first)

---

### **2. ai_feature_pricing (ESISTENTE - OK)**

Tabella già completa con tutti i campi necessari:
- ✅ cost_egili
- ✅ tier_pricing
- ✅ discount_percentage
- ✅ duration_hours
- ✅ is_bundle, bundle_features
- ✅ max_uses_per_purchase
- ✅ stackable

**DA AGGIUNGERE:**
```sql
ALTER TABLE ai_feature_pricing ADD COLUMN:
├─ feature_type ENUM('lifetime', 'consumable', 'temporal') DEFAULT 'consumable'
├─ cost_per_use INT NULL (Egili per singolo uso se consumable)
├─ requires_admin_approval BOOLEAN DEFAULT false (per Featured/Hyper)
└─ max_concurrent_users INT NULL (es: max 3 Featured EGI in home)
```

---

### **3. user_feature_purchases (ESISTENTE - DA ESTENDERE)**

```sql
ALTER TABLE user_feature_purchases ADD COLUMN:
├─ is_lifetime BOOLEAN DEFAULT false
├─ egili_reserved INT NULL (per approval flow)
├─ status ENUM('active', 'pending_approval', 'approved', 'rejected', 'expired') DEFAULT 'active'
├─ approved_by_admin_id BIGINT NULL
├─ approved_at TIMESTAMP NULL
├─ rejection_reason TEXT NULL
└─ usage_metadata JSON NULL (tracking uso dettagliato)
```

**NOTA:** `quantity_used` già esiste, perfetto per consumabili!

---

### **4. feature_promotions (NUOVA TABELLA)**

```sql
CREATE TABLE feature_promotions (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    
    -- Promo Identity
    promo_code VARCHAR(50) UNIQUE NOT NULL,
    promo_name VARCHAR(255) NOT NULL,
    promo_description TEXT,
    
    -- Scope
    is_global BOOLEAN DEFAULT false,
    feature_code VARCHAR(100) NULL, -- NULL se globale
    feature_category VARCHAR(100) NULL, -- Applica a categoria
    
    -- Discount
    discount_type ENUM('percentage', 'fixed_amount') DEFAULT 'percentage',
    discount_value DECIMAL(10,2) NOT NULL,
    
    -- Temporal
    start_at TIMESTAMP NOT NULL,
    end_at TIMESTAMP NOT NULL,
    
    -- Limits
    max_uses INT NULL, -- Max utilizzi totali promo
    max_uses_per_user INT NULL, -- Max per singolo user
    current_uses INT DEFAULT 0,
    
    -- Display
    is_active BOOLEAN DEFAULT true,
    is_featured BOOLEAN DEFAULT false,
    badge_text VARCHAR(50) NULL, -- "BLACK FRIDAY -50%"
    
    -- Admin
    created_by_admin_id BIGINT NOT NULL,
    admin_notes TEXT,
    
    -- Stats
    total_egili_saved INT DEFAULT 0,
    total_purchases_with_promo INT DEFAULT 0,
    
    -- Timestamps
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    INDEX idx_active_dates (is_active, start_at, end_at),
    INDEX idx_feature_code (feature_code),
    INDEX idx_global (is_global),
    FOREIGN KEY (created_by_admin_id) REFERENCES users(id)
);
```

---

### **5. egis (ESTENDERE - Featured/Hyper)**

```sql
ALTER TABLE egis ADD COLUMN:
├─ featured_until TIMESTAMP NULL (scadenza visibilità home)
├─ featured_by_admin_id BIGINT NULL (chi ha approvato)
├─ hyper_until TIMESTAMP NULL (scadenza hyper mode) -- GIÀ ESISTE?
├─ hyper_activated_at TIMESTAMP NULL
└─ hyper_by_admin_id BIGINT NULL
```

**VERIFICA ESISTENTE:**
```bash
grep -n "hyper" database/migrations/*egis*.php
```

---

## ⚙️ **SERVICES DA CREARE/ESTENDERE**

### **1. EgiliService (ESISTENTE - DA ESTENDERE)**

**Metodi da aggiungere:**

```php
/**
 * Grant gift Egili with expiration
 */
public function grantGift(
    User $user,
    int $amount,
    int $expirationDays,
    string $reason,
    User $admin
): EgiliTransaction;

/**
 * Spend Egili with priority logic (Lifetime first, then Gift)
 * 
 * LOGIC:
 * 1. Get all available Egili (lifetime + non-expired gift)
 * 2. Sort: Lifetime FIFO, Gift LIFO (expiring first)
 * 3. Consume in order until amount satisfied
 * 4. Create transaction records for each source
 */
public function spendWithPriority(
    User $user,
    int $amount,
    string $reason,
    string $category
): array; // Returns array of transactions

/**
 * Expire gift Egili (cron job)
 */
public function expireGiftEgili(): int; // Returns count expired
```

---

### **2. FeatureCreditService (NUOVO)**

```php
namespace App\Services;

/**
 * Feature Credits Manager
 * Gestisce acquisto, consumo, conversione feature credits
 */
class FeatureCreditService
{
    /**
     * Purchase feature credits
     * 
     * @param User $user
     * @param string $featureCode
     * @param int $quantity How many uses to buy
     * @return UserFeaturePurchase
     */
    public function purchaseCredits(
        User $user,
        string $featureCode,
        int $quantity = 1
    ): UserFeaturePurchase;
    
    /**
     * Consume one credit
     * 
     * @param User $user
     * @param string $featureCode
     * @param array $usageMetadata (es: tokens_consumed, ai_model)
     * @return bool Success
     */
    public function consumeCredit(
        User $user,
        string $featureCode,
        array $usageMetadata = []
    ): bool;
    
    /**
     * Get available credits for feature
     */
    public function getAvailableCredits(User $user, string $featureCode): int;
    
    /**
     * Convert unused credits to Egili (80% value)
     * Max 1 conversion/month per feature
     */
    public function convertToEgili(
        User $user,
        string $featureCode,
        int $quantity
    ): array; // Returns: egili_refunded, credits_converted
}
```

---

### **3. FeaturePricingService (NUOVO)**

```php
namespace App\Services;

/**
 * Dynamic Feature Pricing Calculator
 * Applica promo attive e tier discounts
 */
class FeaturePricingService
{
    /**
     * Calculate final price for feature purchase
     * 
     * @param string $featureCode
     * @param User $user (for tier pricing)
     * @param int $quantity
     * @return array Pricing breakdown
     */
    public function calculatePrice(
        string $featureCode,
        User $user,
        int $quantity = 1
    ): array;
    // Returns:
    // [
    //   'base_cost' => 500,
    //   'tier_discount' => 50,  // 10% tier discount
    //   'promo_discount' => 100, // 20% promo attiva
    //   'final_cost' => 350,
    //   'promo_applied' => 'BLACK_FRIDAY_2025',
    //   'savings' => 150
    // ]
    
    /**
     * Get active promotions for feature
     */
    public function getActivePromotions(string $featureCode): Collection;
    
    /**
     * Apply best promotion (highest discount%)
     */
    private function applyBestPromotion(
        int $basePrice,
        Collection $promotions
    ): array;
}
```

---

### **4. FeaturePromotionService (NUOVO)**

```php
namespace App\Services;

/**
 * Promotion Manager
 * CRUD promozioni + applicazione automatica
 */
class FeaturePromotionService
{
    public function createPromotion(array $data): FeaturePromotion;
    public function updatePromotion(int $id, array $data): FeaturePromotion;
    public function deletePromotion(int $id): bool;
    public function activatePromotion(int $id): void;
    public function deactivatePromotion(int $id): void;
    
    /**
     * Get all active promotions (for pricing calculation)
     */
    public function getActivePromotions(?string $featureCode = null): Collection;
    
    /**
     * Record promotion usage
     */
    public function recordPromoUsage(
        FeaturePromotion $promo,
        User $user,
        int $egiliSaved
    ): void;
    
    /**
     * Check promo usage limits
     */
    public function canUsePromo(FeaturePromotion $promo, User $user): bool;
}
```

---

### **5. FeaturedEgiService (NUOVO)**

```php
namespace App\Services;

/**
 * Featured EGI Management
 * Gestisce spazi limitati home page con approval flow
 */
class FeaturedEgiService
{
    /**
     * Request featured placement
     */
    public function requestFeatured(
        User $creator,
        Egi $egi,
        int $durationDays
    ): UserFeaturePurchase; // Status: pending_approval, Egili reserved
    
    /**
     * Approve featured request (admin)
     */
    public function approveFeatured(
        UserFeaturePurchase $request,
        User $admin,
        Carbon $startDate,
        Carbon $endDate
    ): void; // Spend Egili, set featured_until
    
    /**
     * Reject featured request (admin)
     */
    public function rejectFeatured(
        UserFeaturePurchase $request,
        User $admin,
        string $reason
    ): void; // Release Egili, notification
    
    /**
     * Get calendar availability
     */
    public function getCalendarAvailability(
        Carbon $startDate,
        Carbon $endDate
    ): array; // Returns: slots_available per day
    
    /**
     * Get pending requests
     */
    public function getPendingRequests(): Collection;
    
    /**
     * Auto-expire featured EGI (cron job)
     */
    public function expireFeaturedEgis(): int;
}
```

---

## 🎨 **ADMIN PANEL UI - STRUTTURA**

### **Menu Enterprise Sidebar:**

```
📊 Features Management
├─ 💰 Pricing Manager
├─ 🎁 Promotions
├─ 📈 Analytics Dashboard
├─ 💎 Egili Management
├─ ⭐ Featured EGI Calendar
└─ 🔥 Hyper Mode Queue
```

---

### **Screen 1: Pricing Manager**

```
+----------------------------------------------------------+
| 💰 Feature Pricing Manager                              |
+----------------------------------------------------------+
| [+ Create New Feature]  [Import CSV]  [Export]         |
+----------------------------------------------------------+
| Search: [___________]  Category: [All ▼]  Status: [Active ▼] |
+----------------------------------------------------------+
| Code               | Name            | Cost Egili | Type       | Actions |
|--------------------|-----------------|------------|------------|---------|
| egi_living         | EGI Living      | 500        | Lifetime   | [Edit] [Stats] |
| ai_description     | AI Description  | 50/use     | Consumable | [Edit] [Stats] |
| featured_home_7d   | Featured 7d     | 1000       | Temporal   | [Edit] [Stats] |
| ...                |                 |            |            |         |
+----------------------------------------------------------+
```

**Edit Modal:**
```
Feature: EGI Living Subscription
├─ Feature Type: [Lifetime ▼]
├─ Base Cost: [500] Egili
├─ Tier Pricing:
│  ├─ Free: [500]
│  ├─ Pro: [400] (20% discount)
│  └─ Enterprise: [300] (40% discount)
├─ Duration: N/A (lifetime)
├─ Max Uses: N/A (lifetime)
├─ Requires Approval: [☐]
└─ [Save] [Cancel]
```

---

### **Screen 2: Promotions Manager**

```
+----------------------------------------------------------+
| 🎁 Promotions Manager                                   |
+----------------------------------------------------------+
| [+ Create Promotion]  Active: [3]  Scheduled: [1]       |
+----------------------------------------------------------+
| ACTIVE PROMOTIONS                                        |
+----------------------------------------------------------+
| BLACK_FRIDAY_2025          | -50% Global    | 20/11 - 30/11 | 245 uses | [Edit] [Deactivate] |
| HYPER_MODE_LAUNCH          | -30% Hyper     | 01/11 - 15/11 | 12 uses  | [Edit] [Stats] |
+----------------------------------------------------------+
| SCHEDULED PROMOTIONS                                     |
+----------------------------------------------------------+
| XMAS_BUNDLE                | Bundle -40%    | 15/12 - 31/12 | 0 uses   | [Edit] [Activate] |
+----------------------------------------------------------+
```

**Create Promo Modal:**
```
New Promotion
├─ Promo Code: [BLACK_FRIDAY_2025]
├─ Name: [Black Friday Sale]
├─ Type: [Global ▼] / Feature-Specific / Category
├─ Discount: [50]% or [___] Egili fixed
├─ Start: [2025-11-20] End: [2025-11-30]
├─ Limits:
│  ├─ Max uses total: [1000]
│  └─ Max per user: [3]
├─ Badge: [-50% BLACK FRIDAY]
└─ [Create] [Cancel]
```

---

### **Screen 3: Featured EGI Calendar**

```
+----------------------------------------------------------+
| ⭐ Featured EGI Management                              |
+----------------------------------------------------------+
| Pending Requests: [5]  Calendar View  |  List View      |
+----------------------------------------------------------+
| CALENDAR (November 2025)                                 |
+----------------------------------------------------------+
| Mon | Tue | Wed | Thu | Fri | Sat | Sun |
|-----|-----|-----|-----|-----|-----|-----|
| 27  | 28  | 29  | 30  | 31  | 1   | 2   |
| 🟢  | 🟢  | 🟡  | 🔴  | 🔴  | 🟢  | 🟢  |
| 3/3 | 3/3 | 2/3 | 0/3 | 0/3 | 3/3 | 3/3 |
+----------------------------------------------------------+

LEGEND:
🟢 = Spazi disponibili
🟡 = Parzialmente occupato
🔴 = Tutto occupato

PENDING REQUESTS:
┌────────────────────────────────────────────────────┐
│ EGI: "Sunset in Florence" by @mario_rossi         │
│ Duration: 7 days (1000 Egili reserved)             │
│ Requested: 2025-11-01 14:30                        │
│ Egili Status: ✅ Reserved                          │
│ Slots needed: 1/3                                  │
│ [Approve for dates: [10/11 ▼] to [17/11 ▼]]       │
│ [Reject with reason]                               │
└────────────────────────────────────────────────────┘
```

---

### **Screen 4: Egili Management**

```
+----------------------------------------------------------+
| 💎 Egili Management (SuperAdmin Only)                   |
+----------------------------------------------------------+
| [Grant Lifetime]  [Grant Gift]  [Refund]  [View All Transactions] |
+----------------------------------------------------------+

GRANT GIFT EGILI:
├─ User: [Search user...]
├─ Amount: [5000] Egili
├─ Type: [Gift (Expiring) ▼]
├─ Expiration: [30] days from now
├─ Reason: [Exceptional contribution to platform]
└─ [Grant] [Cancel]

RECENT TRANSACTIONS:
| User      | Type     | Amount   | Reason          | Expires    | Admin    |
|-----------|----------|----------|-----------------|------------|----------|
| mario123  | Gift     | +5000    | Contest winner  | 30/12/2025 | fabio    |
| laura456  | Lifetime | +10000   | Purchase (€100) | Never      | system   |
| paolo789  | Refund   | +2000    | Feature bug     | Never      | fabio    |
```

---

## 🔄 **FLUSSO EGILI PRIORITY LOGIC (Critico!)**

### **Algoritmo Spend con Priority:**

```php
// EgiliService->spendWithPriority(user, 1500, reason, category)

STEP 1: Get all available Egili sources
Query: egili_transactions
  WHERE user_id = X
    AND status = 'completed'
    AND (expires_at IS NULL OR expires_at > NOW())
  ORDER BY:
    CASE WHEN egili_type = 'lifetime' THEN 0 ELSE 1 END ASC, -- Lifetime first
    CASE WHEN egili_type = 'gift' THEN expires_at END ASC    -- Gift: expiring first

STEP 2: Loop and consume
$remaining = 1500;
foreach ($sources as $source) {
    $available = $source->balance_remaining;
    
    if ($available >= $remaining) {
        // This source covers all remaining
        $source->balance_remaining -= $remaining;
        createTransaction($source, -$remaining);
        $remaining = 0;
        break;
    } else {
        // Consume all from this source, continue
        $remaining -= $available;
        $source->balance_remaining = 0;
        createTransaction($source, -$available);
    }
}

if ($remaining > 0) {
    throw new InsufficientEgiliException();
}

STEP 3: GDPR audit trail per ogni fonte consumata
```

---

## 📋 **NUOVE CATEGORIE GDPR**

```php
// app/Enums/Gdpr/GdprActivityCategory.php

AGGIUNGI:
├─ FEATURE_PURCHASED
├─ FEATURE_CONSUMED
├─ FEATURE_PROMO_APPLIED
├─ FEATURE_CREDITS_CONVERTED
├─ EGILI_GIFT_GRANTED
├─ EGILI_GIFT_EXPIRED
├─ FEATURED_EGI_REQUESTED
├─ FEATURED_EGI_APPROVED
└─ FEATURED_EGI_REJECTED
```

---

## 🎯 **PRIORITÀ IMPLEMENTAZIONE**

### **FASE 1 - FOUNDATION (Critical)**
1. ✅ Estendi `egili_transactions` (lifetime/gift, expires_at)
2. ✅ Estendi `EgiliService` (grantGift, spendWithPriority, expire cron)
3. ✅ Crea `FeatureCreditService` (purchase, consume, convert)
4. ✅ Estendi `user_feature_purchases` (is_lifetime, status, approval)
5. ✅ Estendi `ai_feature_pricing` (feature_type, cost_per_use)

### **FASE 2 - PRICING & PROMO**
6. ✅ Crea `feature_promotions` table
7. ✅ Crea `FeaturePricingService` (calculate with discounts)
8. ✅ Crea `FeaturePromotionService` (CRUD promo)
9. ✅ Integra pricing dinamico in purchase flow

### **FASE 3 - FEATURED/HYPER**
10. ✅ Aggiungi campi `egis` (featured_until, etc)
11. ✅ Crea `FeaturedEgiService` (request, approve, reject)
12. ✅ Admin panel Featured Calendar

### **FASE 4 - ADMIN PANELS**
13. ✅ Admin: Pricing Manager
14. ✅ Admin: Promotions Manager
15. ✅ Admin: Egili Management
16. ✅ Admin: Analytics Dashboard

### **FASE 5 - USER EXPERIENCE**
17. ✅ Pagina "Le mie Feature" (credits, usage, history)
18. ✅ Conversione feature → Egili UI
19. ✅ Featured EGI request UI (creator side)

---

## ❓ **DECISIONI ANCORA DA PRENDERE**

Fabio, prima di procedere serve confermare:

1. **Logica spend priority** - Ti piace FIFO Lifetime + LIFO Gift expiring?

2. **Approval flow Featured** - Vuoi che Egili siano **reserved** durante pending o **spesi subito** e rimborsati se rejected?

3. **Config file vs DB** - Prezzario in `config/feature_pricelist.php` (statico) o solo DB (dinamico)?

4. **Conversione feature→Egili** - 80% è ok? Limite 1 conversione/mese per feature?

5. **Hyper Mode** - Stessa logica di Featured (approval required) o automatica (se paghi, si attiva)?

---

**Vuoi che discutiamo questi punti PRIMA di fare il piano operativo dettagliato?** 🎯




