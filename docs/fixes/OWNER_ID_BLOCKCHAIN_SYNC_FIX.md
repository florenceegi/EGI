# 🔧 FIX CRITICO: Sync egis.owner_id con egi_blockchain.buyer_user_id

**Data:** 2025-10-10  
**Commit:** TBD  
**Priorità:** P0 - BLOCKING per Secondary Market

---

## 🚨 PROBLEMA IDENTIFICATO

### **Sintomo:**

Dopo il mint di un EGI, il campo `egis.owner_id` rimaneva uguale a `egis.user_id` (creator) invece di essere aggiornato con il `buyer_user_id` della blockchain.

### **Impatto:**

```php
// Scenario:
1. Creator (user_id=10) crea EGI
   egis.user_id = 10
   egis.owner_id = 10  ✅ OK (creator è anche owner iniziale)

2. Buyer (user_id=25) compra e minta EGI
   egi_blockchain.buyer_user_id = 25  ✅ Blockchain aggiornata
   egis.owner_id = 10  ❌ SBAGLIATO! Ancora il creator!

3. Sistema legge egis.owner_id per permission check
   EgiPolicy::updatePrice($user, $egi) {
       return $user->id === $egi->owner_id;  // FALSE! 25 !== 10
   }
   → Buyer NON può modificare prezzo
   → Secondary market NON funziona
   → UI mostra owner sbagliato
```

---

## 🎯 ROOT CAUSE

### **EgiPurchaseWorkflowService::updateEgiOwnership()**

**BEFORE (BUG):**

```php
private function updateEgiOwnership(Egi $egi, User $user, EgiBlockchain $egiBlockchain): void {
    // Note: In EGI system, ownership is tracked through blockchain record
    // EGI model doesn't have owner_id field updated for blockchain purchases
    // This is intentional - blockchain record is source of truth for ownership

    $this->auditService->logActivity(...);  // Solo audit, NO UPDATE!
}
```

**❌ Il commento diceva "è intenzionale"... MA ERA SBAGLIATO!**

---

## ✅ SOLUZIONE IMPLEMENTATA

### **Fix 1: EgiPurchaseWorkflowService.php**

```php
private function updateEgiOwnership(Egi $egi, User $user, EgiBlockchain $egiBlockchain): void {
    // CRITICAL: Sync owner_id with blockchain buyer_user_id
    // Before: owner_id = creator (user_id)
    // After:  owner_id = buyer (buyer_user_id)
    $egi->update([
        'owner_id' => $egiBlockchain->buyer_user_id
    ]);

    $this->auditService->logActivity(
        $user,
        GdprActivityCategory::OWNERSHIP_TRANSFER,
        'EGI ownership transferred via blockchain purchase',
        [
            'egi_id' => $egi->id,
            'previous_owner_id' => $egi->user_id, // Creator (immutable)
            'new_owner_id' => $egiBlockchain->buyer_user_id, // New commercial owner
            'blockchain_record_id' => $egiBlockchain->id,
            'asa_id' => $egiBlockchain->asa_id,
            'blockchain_tx_id' => $egiBlockchain->blockchain_tx_id
        ]
    );
}
```

### **Fix 2: EgiMintingService::transferEgiOwnership()**

```php
public function transferEgiOwnership(EgiBlockchain $egiBlockchain, string $buyerWallet, ?int $buyerUserId = null): string {
    // ...mint and transfer logic...

    // Aggiorna ownership nel record blockchain
    $egiBlockchain->update([
        'ownership_type' => 'wallet',
        'buyer_wallet' => $buyerWallet,
        'buyer_user_id' => $buyerUserId,
        'blockchain_tx_id' => $transferTxId
    ]);

    // CRITICAL: Sync egis.owner_id with buyer_user_id
    // This ensures Policy checks and secondary market work correctly
    if ($buyerUserId) {
        $egiBlockchain->egi->update([
            'owner_id' => $buyerUserId
        ]);
    }

    // ...
}
```

---

## 📊 ARCHITETTURA OWNERSHIP

### **Due Campi, Due Responsabilità:**

```
egis table:
├─ user_id → CREATOR (IMMUTABLE)
│             Proprietà intellettuale
│             Diritti d'autore (Legge 633/1941 Art. 20)
│             Gestione CoA (competenza artistica)
│
└─ owner_id → OWNER COMMERCIALE (MUTABLE)
              Proprietà commerciale
              Gestione prezzo
              Diritto di vendita secondary market
              SYNC con egi_blockchain.buyer_user_id!
```

### **Flusso Timeline:**

```
CREATION:
egis.user_id = 10 (creator)
egis.owner_id = 10 (creator è anche owner iniziale)
egi_blockchain: NON ESISTE ancora

MINT (Primary Market):
egi_blockchain.buyer_user_id = 25 (buyer)
egis.owner_id = 25  ← ✅ SYNC! (FIX APPLICATO)
egis.user_id = 10  ← IMMUTABLE

TRANSFER (Secondary Market):
egi_blockchain.buyer_user_id = 30 (new buyer)
egis.owner_id = 30  ← ✅ SYNC! (FIX APPLICATO)
egis.user_id = 10  ← IMMUTABLE (sempre il creator originale)
```

---

## 🎯 IMPATTI POSITIVI DEL FIX

### **1. Policy Checks Funzionanti:**

```php
// EgiPolicy::updatePrice()
public function updatePrice(User $user, Egi $egi): bool
{
    if (!$egi->isMinted()) {
        return $user->id === $egi->user_id; // Creator before mint
    }

    // After mint: check blockchain first, fallback DB
    if ($egi->blockchain) {
        return $user->id === $egi->blockchain->buyer_user_id;
    }
    return $user->id === $egi->owner_id;  // ✅ ORA FUNZIONA!
}
```

### **2. Secondary Market Abilitato:**

```php
// Reservation logic
if ($egi->owner_id !== $egi->user_id) {
    // EGI è stato mintato e rivenduto
    $marketType = 'secondary';
    $royaltyPercentages = [
        'creator' => 4.5%,
        'epp' => 0.8%,
        'natan' => 0.7%,
        'frangette' => 0.1%,
        'owner' => 94.0%  // ✅ Owner corretto riceve 94%!
    ];
}
```

### **3. UI Corretta:**

```blade
{{-- egi-card.blade.php --}}
@if($egi->owner_id !== $egi->user_id)
    <span class="badge bg-blue">IN VENDITA (Secondary Market)</span>
    <p>Owner: {{ $egi->owner->name }}</p>  {{-- ✅ Mostra buyer corretto --}}
    <p>Creator: {{ $egi->creator->name }}</p>  {{-- Immutable --}}
@endif
```

---

## 🧪 TEST VALIDAZIONE

### **Test Manuale:**

```bash
php artisan tinker --execute="
use App\Models\Egi;
use App\Models\EgiBlockchain;

\$egi = Egi::with('blockchain')->find(1);

echo 'EGI ID: ' . \$egi->id . PHP_EOL;
echo 'Creator (user_id): ' . \$egi->user_id . PHP_EOL;
echo 'Owner (owner_id): ' . \$egi->owner_id . PHP_EOL;

if (\$egi->blockchain) {
    echo 'Blockchain buyer_user_id: ' . \$egi->blockchain->buyer_user_id . PHP_EOL;
    echo 'SYNC STATUS: ' . (\$egi->owner_id === \$egi->blockchain->buyer_user_id ? '✅ OK' : '❌ FAIL') . PHP_EOL;
} else {
    echo 'Blockchain: NOT MINTED' . PHP_EOL;
}
"
```

### **Expected Output (DOPO FIX):**

```
EGI ID: 1
Creator (user_id): 10
Owner (owner_id): 25
Blockchain buyer_user_id: 25
SYNC STATUS: ✅ OK
```

### **Test Unitario (TODO):**

```php
// tests/Unit/Services/EgiPurchaseWorkflowServiceTest.php

public function test_owner_id_syncs_with_buyer_user_id_after_mint()
{
    $creator = User::factory()->create(); // ID 10
    $buyer = User::factory()->create();   // ID 25

    $egi = Egi::factory()->create([
        'user_id' => $creator->id,
        'owner_id' => $creator->id  // Initially creator
    ]);

    // Simulate mint purchase
    $egiBlockchain = EgiBlockchain::factory()->create([
        'egi_id' => $egi->id,
        'buyer_user_id' => $buyer->id,
        'mint_status' => 'minted'
    ]);

    // Call the fixed method
    $service->updateEgiOwnership($egi, $buyer, $egiBlockchain);

    // Assert sync
    $egi->refresh();
    $this->assertEquals($buyer->id, $egi->owner_id);
    $this->assertEquals($creator->id, $egi->user_id); // Immutable
    $this->assertEquals($egi->owner_id, $egiBlockchain->buyer_user_id); // SYNC!
}
```

---

## 📝 FILES MODIFICATI

1. **app/Services/EgiPurchaseWorkflowService.php**

    - Metodo: `updateEgiOwnership()`
    - Aggiunto: `$egi->update(['owner_id' => $egiBlockchain->buyer_user_id])`
    - Linee: 590-620

2. **app/Services/EgiMintingService.php**
    - Metodo: `transferEgiOwnership()`
    - Aggiunto: `$egiBlockchain->egi->update(['owner_id' => $buyerUserId])`
    - Linee: 230-280

---

## ⚠️ BACKWARD COMPATIBILITY

### **EGI Esistenti Già Mintati:**

**PROBLEMA:** EGI mintati prima di questo fix hanno `owner_id` sbagliato.

**SOLUZIONE:** Migration di pulizia (opzionale):

```php
// database/migrations/YYYY_MM_DD_sync_owner_id_with_blockchain.php

public function up()
{
    // Sync owner_id per tutti gli EGI già mintati
    DB::statement("
        UPDATE egis e
        INNER JOIN egi_blockchain eb ON e.id = eb.egi_id
        SET e.owner_id = eb.buyer_user_id
        WHERE eb.mint_status = 'minted'
          AND e.owner_id != eb.buyer_user_id
    ");
}
```

**Query di verifica:**

```sql
SELECT
    e.id,
    e.title,
    e.user_id AS creator_id,
    e.owner_id AS current_owner_id,
    eb.buyer_user_id AS blockchain_buyer_id,
    CASE
        WHEN e.owner_id = eb.buyer_user_id THEN '✅ OK'
        ELSE '❌ DESYNC'
    END AS sync_status
FROM egis e
INNER JOIN egi_blockchain eb ON e.id = eb.egi_id
WHERE eb.mint_status = 'minted';
```

---

## 🚀 DEPLOYMENT CHECKLIST

-   [x] Fix applicato in EgiPurchaseWorkflowService
-   [x] Fix applicato in EgiMintingService
-   [x] Documentazione creata
-   [ ] Test unitario scritto
-   [ ] Migration dati per EGI esistenti (se necessario)
-   [ ] Test manuale su staging
-   [ ] Deploy in produzione
-   [ ] Verifica query sync_status

---

## 🎯 RATIONALE FINALE

**Perché due campi separati?**

```
user_id (IMMUTABLE):
- Diritti d'autore inalienabili
- Art. 20 Legge 633/1941: diritto all'integrità
- Creator SEMPRE mantiene controllo CoA
- Legislative compliance

owner_id (MUTABLE):
- Proprietà commerciale trasferibile
- Diritto di vendita secondary market
- Gestione prezzo
- Policy checks per permission system
```

**Fonte di verità:**

```
PRIMA DEL MINT:  egis.owner_id = egis.user_id
DOPO IL MINT:    egis.owner_id = egi_blockchain.buyer_user_id
```

**Blockchain record è SOURCE OF TRUTH per ownership blockchain, MA `egis.owner_id` deve essere SYNC per performance e policy checks senza sempre joinare egi_blockchain.**

---

**Status:** ✅ FIX IMPLEMENTATO  
**Next:** Test + Migration dati + Deploy
