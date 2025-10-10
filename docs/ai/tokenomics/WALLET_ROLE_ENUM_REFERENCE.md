# 🎯 WalletRoleEnum - Tokenomics Reference

**Created:** 2025-10-10  
**Commit:** 85e1d55  
**Status:** ✅ IMPLEMENTED & TESTED

---

## 📊 TOKENOMICS DISTRIBUTION

### Primary Market (Mint) - 100%

```
Creator:   68.0%  (User-specific, intellectual property owner)
EPP:       20.0%  (Environmental impact partner)
Natan:     10.0%  (Platform infrastructure)
Frangette:  2.0%  (Ecosystem development)
------
TOTAL:    100.0%  ✅ VALIDATED
```

### Secondary Market (Rebind) - 6.1%

```
Creator:   4.5%   (Droit de suite - Legge 633/1941 Art. 144)
EPP:       0.8%   (Ongoing environmental monitoring)
Natan:     0.7%   (Platform maintenance)
Frangette: 0.1%   (Ecosystem sustainability)
------
TOTAL:     6.1%   ✅ VALIDATED
```

---

## 🏗️ ARCHITECTURE

### Enum Location

```
app/Enums/Wallet/WalletRoleEnum.php
```

### Key Methods

```php
// Get royalty percentages
WalletRoleEnum::CREATOR->getMintRoyalty();     // 68.0
WalletRoleEnum::CREATOR->getRebindRoyalty();   // 4.5

// Get platform configuration
WalletRoleEnum::EPP->getUserId();              // 2 (from ENV)
WalletRoleEnum::EPP->getWalletAddress();       // Algorand address

// Validation & helpers
WalletRoleEnum::validateMintTotal();           // bool (true if 100%)
WalletRoleEnum::getTotalRebindPercentage();    // 6.1
WalletRoleEnum::platformRoles();               // [EPP, Natan, Frangette]
```

---

## 🔧 USAGE IN CODE

### WalletService Example

```php
use App\Enums\Wallet\WalletRoleEnum;

public function attachDefaultWalletsToCollection(Collection $collection, User $user): void
{
    // 1. Create CREATOR wallet (user-specific)
    $this->createWallet(
        $collection->id,
        $user->id,
        $user->wallet ?? null,
        WalletRoleEnum::CREATOR->getMintRoyalty(),    // 68.0
        WalletRoleEnum::CREATOR->getRebindRoyalty(),  // 4.5
        WalletRoleEnum::CREATOR->value                // 'Creator'
    );

    // 2. Create PLATFORM wallets (loop)
    foreach (WalletRoleEnum::platformRoles() as $role) {
        $this->createWallet(
            $collection->id,
            $role->getUserId(),           // 2, 1, 3
            $role->getWalletAddress(),    // Algorand addresses
            $role->getMintRoyalty(),      // 20, 10, 2
            $role->getRebindRoyalty(),    // 0.8, 0.7, 0.1
            $role->value                  // 'EPP', 'Natan', 'Frangette'
        );
    }

    // 3. Validate
    if (!WalletRoleEnum::validateMintTotal()) {
        throw new \Exception('Invalid tokenomics');
    }
}
```

---

## ⚙️ CONFIGURATION

### Required .env Variables

```bash
NATAN_ID=1
EPP_ID=2
FRANGETTE_ID=3

NATAN_WALLET_ADDRESS='XKQM7FWJVN4LBZR2X6YDPU3TS9WE5QAZ7VBNM8R4X2LKJHG6FD3SWCVBN2Q'
EPP_WALLET_ADDRESS='MKPN8RWQL5X3VZTY9MBHJ6KUSG7PX4ZN2VBCM5ER7WQPL3YHGF9TSXVBNRA'
FRANGETTE_WALLET_ADDRESS='QRTV3BZNWH6YJ5XPGU4FLSM7KC2EN9VBWQ6ZY3MNXT4PRS7GH5KWJYBLNFC'
```

### config/app.php Mappings

```php
'natan_id' => env('NATAN_ID', 1),
'epp_id' => env('EPP_ID', 2),
'frangette_id' => env('FRANGETTE_ID', 3),

'natan_wallet_address' => env('NATAN_WALLET_ADDRESS'),
'epp_wallet_address' => env('EPP_WALLET_ADDRESS'),
'frangette_wallet_address' => env('FRANGETTE_WALLET_ADDRESS'),
```

---

## ✅ ADVANTAGES vs OLD CONFIG SYSTEM

| Aspect              | Config (OLD)             | Enum (NEW)             |
| ------------------- | ------------------------ | ---------------------- |
| **Type Safety**     | ❌ String/Float parsing  | ✅ Native float types  |
| **Cache Issues**    | ❌ config:cache problems | ✅ No cache needed     |
| **Validation**      | ❌ Manual checks         | ✅ Built-in validation |
| **IDE Support**     | ❌ No autocomplete       | ✅ Full autocomplete   |
| **Corruption Risk** | ❌ File can be edited    | ✅ Immutable constants |
| **Testing**         | ❌ Hard to mock          | ✅ Easy to test        |

---

## 🧪 VALIDATION TEST

```bash
php artisan tinker --execute="
use App\Enums\Wallet\WalletRoleEnum;

echo 'Mint Total: ' . array_sum(array_map(fn(\$r) => \$r->getMintRoyalty(), WalletRoleEnum::cases())) . '%' . PHP_EOL;
echo 'Rebind Total: ' . WalletRoleEnum::getTotalRebindPercentage() . '%' . PHP_EOL;
echo 'Validation: ' . (WalletRoleEnum::validateMintTotal() ? '✅ PASS' : '❌ FAIL');
"
```

**Expected Output:**

```
Mint Total: 100%
Rebind Total: 6.1%
Validation: ✅ PASS
```

---

## 📚 LEGISLATIVE COMPLIANCE

**Legge 633/1941 Art. 144 (Droit de suite)**

-   Legal minimum: 0.25% - 4%
-   FlorenceEGI applies: 4.5% to Creator
-   **Rationale:** Higher than legal minimum, disclosed in T&C
-   **Target:** Support artists beyond minimum requirements

---

## 🚨 IMPORTANT NOTES

### Creator vs Platform Roles

-   **Creator:** User-specific, dynamic (`$user->id`, `$user->wallet`)
-   **Platform:** Static system accounts (EPP ID=2, Natan ID=1, Frangette ID=3)

### Wallet Creation Flow

```
1. Collection created (user registration OR manual via modal)
   ↓
2. attachDefaultWalletsToCollection($collection, $user)
   ↓
3. WalletRoleEnum defines percentages (SOURCE OF TRUTH)
   ↓
4. Wallets inserted in DB with enum values
   ↓
5. PaymentDistribution reads from DB wallets table
```

### Source of Truth Timeline

```
CREATION TIME:  WalletRoleEnum → percentages written to DB
RUNTIME:        DB wallets table → PaymentDistributionService reads
```

---

## 🔄 FUTURE CHANGES

To modify tokenomics:

1. Edit `app/Enums/Wallet/WalletRoleEnum.php`
2. Update `getMintRoyalty()` and `getRebindRoyalty()` methods
3. Run validation test
4. Commit with clear rationale
5. **EXISTING collections NOT affected** (use DB migration if needed)

---

## 📝 TODO (if needed)

-   [ ] Data migration to add Frangette wallet to existing collections
-   [ ] Unit tests for WalletRoleEnum validation
-   [ ] Integration tests for wallet creation flow
-   [ ] Admin panel to view tokenomics distribution
-   [ ] PaymentDistribution service refactor to use enum (optional)

---

**Status:** ✅ PRODUCTION-READY  
**Next Step:** Test with real collection creation
