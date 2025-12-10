# Gold Bar Feature Documentation

## Overview

The Gold Bar feature allows EGI items to represent physical gold bars with real-time indicative pricing based on gold spot prices.

## Components Created

### 1. Database Structure

**Migration**: `2025_12_08_100000_add_gold_bar_trait_category.php`

Adds the following to the traits system:

-   **Category**: Gold Bar (slug: `gold-bar`)
-   **Trait Types**:
    -   `gold-weight`: Numeric weight value
    -   `gold-weight-unit`: Select (Grams, Ounces, Troy Ounces)
    -   `gold-purity`: Select (999, 995, 990, 916, 750)
    -   `gold-margin-percent`: Numeric percentage margin
    -   `gold-margin-fixed`: Numeric fixed amount margin

### 2. GoldPriceService

**File**: `app/Services/GoldPriceService.php`

Features:

-   Real-time gold price fetching from multiple API sources
-   Caching (5 minutes TTL)
-   Fallback mechanism between providers
-   Gold bar value calculation with:
    -   Weight conversion (Grams, Ounces, Troy Ounces)
    -   Purity factor application
    -   Margin calculation (percentage or fixed)

**API Providers**:

1. Gold-API.io (primary)
2. MetalPriceAPI (fallback)

**Configuration** (add to `.env`):

```env
GOLD_API_KEY=your_gold_api_key_here
METAL_PRICE_API_KEY=your_metal_price_api_key_here
```

### 3. Egi Model Accessors

**File**: `app/Models/Egi.php`

New methods:

-   `isGoldBar()`: Check if EGI has gold bar traits
-   `getGoldWeight()`: Get weight value
-   `getGoldWeightUnit()`: Get weight unit
-   `getGoldPurity()`: Get purity code
-   `getGoldMarginPercent()`: Get percentage margin
-   `getGoldMarginFixed()`: Get fixed margin
-   `getGoldBarValue($currency)`: Get full calculation

### 4. Blade Component

**Component Class**: `app/View/Components/GoldBarInfo.php`
**View**: `resources/views/components/gold-bar-info.blade.php`

**Usage**:

```blade
{{-- Full display --}}
<x-gold-bar-info :egi="$egi" currency="EUR" />

{{-- Compact without details --}}
<x-gold-bar-info :egi="$egi" :showDetails="false" size="compact" />

{{-- With custom attributes --}}
<x-gold-bar-info :egi="$egi" class="mt-4" />
```

**Properties**:

-   `egi`: The Egi model (required)
-   `currency`: Currency code (default: 'EUR')
-   `showDetails`: Show breakdown (default: true)
-   `size`: 'compact', 'normal', 'large' (default: 'normal')

### 5. Translations

**Files Created**:

-   `resources/lang/en/gold_bar.php`
-   `resources/lang/it/gold_bar.php`
-   `resources/lang/fr/gold_bar.php`
-   `resources/lang/de/gold_bar.php`
-   `resources/lang/es/gold_bar.php`
-   `resources/lang/pt/gold_bar.php`

**Updated Files**:

-   `resources/lang/*/trait_elements.php` (all 6 languages)

## Usage Examples

### Creating a Gold Bar EGI

When creating traits for an EGI through the trait editor:

1. Select the "Gold Bar" category
2. Add the following traits:
    - Gold Weight: e.g., "100"
    - Gold Weight Unit: "Grams"
    - Gold Purity: "999" (24k)
    - Gold Margin Percent: "5" (optional, 5% margin)
    - OR Gold Margin Fixed: "50" (optional, €50 fixed margin)

### Displaying Gold Bar Info in egi-card

Add to `egi-card.blade.php` where appropriate:

```blade
@if($egi->isGoldBar())
    <x-gold-bar-info :egi="$egi" size="compact" />
@endif
```

### Programmatic Access

```php
$egi = Egi::find($id);

if ($egi->isGoldBar()) {
    // Get calculated value
    $goldValue = $egi->getGoldBarValue('EUR');

    echo "Weight: " . $egi->getGoldWeight() . " " . $egi->getGoldWeightUnit();
    echo "Purity: " . $egi->getGoldPurity();
    echo "Base Value: " . $goldValue['base_value'];
    echo "Final Value: " . $goldValue['final_value'];
}
```

### Direct Service Usage

```php
use App\Services\GoldPriceService;

$goldService = app(GoldPriceService::class);

// Get gold price
$price = $goldService->getGoldPrice('EUR');
// Returns: ['price_per_gram' => 85.50, 'price_per_oz' => 2660.00, ...]

// Calculate value
$value = $goldService->calculateGoldBarValue(
    weight: 100,
    weightUnit: 'Grams',
    purity: '999',
    marginPercent: 5,
    marginFixed: null,
    currency: 'EUR'
);
```

## Deployment Steps

1. Run migration:

    ```bash
    php artisan migrate
    ```

2. Add API keys to `.env`:

    ```
    GOLD_API_KEY=your_key
    METAL_PRICE_API_KEY=your_key
    ```

3. Clear caches:
    ```bash
    php artisan config:clear
    php artisan cache:clear
    php artisan view:clear
    ```

## API Rate Limits

-   **Gold-API.io**: 300 requests/month on free tier
-   **MetalPriceAPI**: Limited free tier

With 5-minute caching, monthly usage is approximately:

-   12 requests/hour × 24 hours × 30 days = ~8,640 potential requests
-   But cached = max 12 requests/hour = ~8,640/month (if constantly accessed)

Recommendation: Get paid API plan for production or implement additional caching strategies.

## Troubleshooting

### Gold price not loading

1. Check API keys in `.env`
2. Verify network connectivity to API endpoints
3. Check Laravel logs for API errors

### Component not rendering

1. Ensure EGI has all required traits (weight, unit, purity)
2. Check `$egi->isGoldBar()` returns true
3. Verify translations are loaded

### Cache issues

Clear gold price cache:

```php
app(GoldPriceService::class)->clearCache();
```
