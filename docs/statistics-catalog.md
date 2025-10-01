# CATALOGO COMPLETO METRICHE STATISTICHE - USER ID 3

**Target User**: ID 3 (Natan - fabiogianni@gmail.com)
**Contenuto esistente**: 2 collections, 28 EGIs, 5 likes, 10 reservations

## 📊 METODI DA TESTARE NEL FACTORY

### 1. StatisticsService Core Methods

#### **getLikesStatistics()**

**Input**: User collections IDs  
**Output**:

```php
[
    'total' => int,                    // Total likes (collection + EGI likes)
    'collections_total' => int,        // Direct likes to collections
    'egis_total' => int,              // Likes to EGIs in collections
    'by_collection' => [              // Per-collection breakdown
        [
            'collection_id' => int,
            'collection_name' => string,
            'collection_likes' => int,
            'egi_likes' => int,
            'total_likes' => int
        ]
    ],
    'top_egis' => [                   // Top 3 most liked EGIs
        [
            'id' => int,
            'title' => string,
            'collection_name' => string,
            'likes_count' => int
        ]
    ]
]
```

**Query dipendenze**:

-   `likes` table WHERE `likeable_type` = 'App\Models\Collection' AND `likeable_id` IN (user_collections)
-   `likes` table WHERE `likeable_type` = 'App\Models\Egi' AND `likeable_id` IN (egis from user_collections)

#### **getReservationsStatistics()**

**Input**: User collections IDs  
**Output**:

```php
[
    'total' => int,                    // Total valid reservations (priority logic)
    'strong' => int,                   // Strong type reservations
    'weak' => int,                     // Weak type reservations
    'by_collection' => [              // Per-collection breakdown
        [
            'collection_id' => int,
            'collection_name' => string,
            'total_reservations' => int,
            'strong_reservations' => int,
            'weak_reservations' => int
        ]
    ],
    'valid_reservations_for_amount' => Collection // For other calculations
]
```

**Query dipendenze**:

-   Complex CTE query with ROW_NUMBER() OVER (PARTITION BY egi_id ORDER BY priority logic)
-   `reservations` table JOIN `egis` JOIN `collections` WHERE creator_id = user_id AND status = 'active' AND is_current = 1

#### **getAmountStatistics()**

**Input**: Valid reservations collection  
**Output**:

```php
[
    'total_eur' => float,              // Sum of all valid reservation amounts
    'by_collection' => [              // Per-collection amounts
        [
            'collection_id' => int,
            'collection_name' => string,
            'total_amount_eur' => float
        ]
    ],
    'by_type' => [                    // By reservation type
        'strong' => float,
        'weak' => float
    ]
]
```

**Query dipendenze**:

-   Aggregation on valid_reservations->sum('offer_amount_fiat')

#### **getEppPotentialStatistics()**

**Input**: Valid reservations collection  
**Output**:

```php
[
    'total_quota_eur' => float,        // Total EPP potential across collections
    'by_collection' => [              // Per-collection EPP breakdown
        [
            'collection_id' => int,
            'collection_name' => string,
            'epp_percentage' => float,
            'total_amount' => float,
            'epp_quota' => float
        ]
    ]
]
```

**Query dipendenze**:

-   `wallets` table WHERE `collection_id` IN (user_collections) AND `platform_role` = 'EPP'
-   Uses `royalty_mint` field as EPP percentage or DEFAULT_EPP_PERCENTAGE (20.0)

#### **getPortfolioStatistics()**

**Input**: User collections IDs  
**Output**:

```php
[
    'total_egis' => int,               // Count of all EGIs in user collections
    'total_collections' => int,        // Count of user collections
    'reserved_egis' => int,           // EGIs with active reservations
    'available_egis' => int,          // EGIs without reservations
    'highest_offer' => float,         // Max offer_amount_fiat from active reservations
    'total_value_eur' => float        // Sum of amount_eur from active reservations
]
```

**Query dipendenze**:

-   `egis` table WHERE `collection_id` IN (user_collections) with reservations relationship

### 2. StatisticsService Extended Methods (for widgets)

#### **getCreatorEarnings(creatorId, period)**

**Input**: Creator ID, period ('day', 'week', 'month', 'year', 'all')  
**Output**:

```php
[
    'total_earnings' => float,
    'total_distributions' => int,
    'total_sales' => int,
    'avg_earnings_per_distribution' => float,
    'avg_earnings_per_sale' => float,
    'min_earnings' => float,
    'max_earnings' => float,
    'collections_with_sales' => int
]
```

**Query dipendenze**:

-   `payment_distributions` JOIN `reservations` JOIN `egis` JOIN `collections` WHERE creator_id AND user_type='creator' AND is_highest=true
-   Temporal filtering with period dates

#### **getUserTotalEarnings(userId, period)**

**Input**: User ID, period  
**Output**:

```php
[
    'total_earnings' => float,
    'total_distributions' => int,
    'avg_earning_per_distribution' => float,
    'collections_involved' => int,
    'reservations_involved' => int
]
```

**Query dipendenze**:

-   `payment_distributions` JOIN `reservations` WHERE user_id AND is_highest=true
-   Temporal filtering

#### **getUserNonCreatorEarnings(userId, period)**

**Input**: User ID, period  
**Output**:

```php
[
    'total_earnings' => float,
    'total_distributions' => int,
    'avg_earning_per_distribution' => float,
    'collections_involved' => int,
    'reservations_involved' => int
]
```

**Query dipendenze**:

-   `payment_distributions` JOIN `reservations` JOIN `egis` JOIN `collections` WHERE user_id AND creator_id != user_id

#### **getCreatorEngagementStats(creatorId, period)**

**Input**: Creator ID, period  
**Output**:

```php
[
    'collectors_reached' => int,       // Unique users who made reservations
    'epp_impact_generated' => float,  // EPP distributions from creator's works
    'total_volume_generated' => float,// All distributions from creator's works
    'avg_impact_per_collector' => float,
    'epp_percentage' => float
]
```

**Query dipendenze**:

-   Complex queries on `payment_distributions` with user counting and EPP filtering

#### **getCreatorHoldersStats(creatorId, period)**

**Input**: Creator ID, period  
**Output**:

```php
[
    'holders' => [],                  // Per-collection holder data
    'aggregated' => [],               // Aggregated holder stats
    'summary' => [
        'total_collectors' => int,
        'total_items_held' => int,
        'total_revenue' => float,
        'avg_per_collector' => float
    ]
]
```

**Query dipendenze**:

-   Complex aggregation of `payment_distributions` to identify holders

#### **getLikesReceivedStatsByPeriod(userId, period)**

#### **getLikesGivenStatsByPeriod(userId, period)**

**Similar structure for likes analytics with temporal filtering**

### 3. PaymentDistribution Model Methods

#### **PaymentDistribution::getCreatorEarnings(creatorId)**

**Similar to StatisticsService but without temporal filtering**

#### **PaymentDistribution::getUserTotalEarnings(userId)**

**Similar to StatisticsService but without temporal filtering**

#### **PaymentDistribution::getUserNonCreatorEarnings(userId)**

**Similar to StatisticsService but without temporal filtering**

## 🗄️ TABELLE COINVOLTE

### Core Tables:

1. **users** - Base user data
2. **collections** - creator_id relationship
3. **egis** - collection_id relationship
4. **reservations** - egi_id, user_id, type, status, is_current, offer_amount_fiat, amount_eur
5. **likes** - likeable_type, likeable_id, user_id
6. **payment_distributions** - user_id, reservation_id, collection_id, user_type, amount_eur, distribution_status
7. **wallets** - collection_id, platform_role, royalty_mint (EPP percentage)

### Support Tables:

8. **collection_user** - For non-creator earnings detection

## 🎯 FACTORY REQUIREMENTS

### Data Volume for User 3:

-   **Collections**: 2 (existing)
-   **EGIs**: 28 (existing)
-   **Users**: 5-10 (for creating reservations/likes from different users)
-   **Reservations**: 20-30 (different types, amounts, dates)
-   **Likes**: 15-25 (on collections and EGIs)
-   **PaymentDistributions**: 15-20 (tied to reservations)
-   **Wallets**: 2 (one per collection with EPP settings)

### Period Distribution:

-   **2022**: 20% of data
-   **2023**: 30% of data
-   **2024**: 50% of data
-   **Daily spread**: Ensure data across different days/weeks/months for temporal testing

### Expected Results Pre-calculation:

For each method above, calculate EXACT expected values based on seeded data.

## 🔍 NEXT STEPS

1. Create StatisticsTestFactory with seedDeterministicData()
2. Implement data clearing for User 3
3. Create deterministic data with known values
4. Pre-calculate all expected results
5. Implement validation command
