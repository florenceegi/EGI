# Direct Minting & Master Cloning - Technical Guide
**Version:** 1.0  
**Date:** 2025-12-15  
**Status:** IMPLEMENTED

This document details the technical implementation of the **Direct Mint** flow ("Paga Ora"), specifically focusing on the Logic for Master EGI Cloning and Microservice Security.

---

## 1. Core Logic: Direct Mint Workflow

The `processDirectMint` method in `MintController.php` handles the "Buy Now" flow.

### 1.1 Master Cloning Interception
When a user attempts to mint an EGI that is marked as a **Master Template** (`is_template = true`):

1.  **Detection**: The controller detects `is_template`.
2.  **Cloning**: Instead of minting the Master ID directly (which would consume the template), the system calls `CloneEgiFromMasterAction`.
3.  **Swap**: The `$egi` variable in the controller is swapped with the **newly created Child Clone**.
4.  **Flow Continuation**: Payment and Minting proceed using the **Child EGI's ID**.

```php
// MintController.php Logic
if ($egi->is_template) {
    // 1. Clone Master to Child (Buyer becomes Owner)
    $clone = $cloneAction->execute($egi, Auth::user(), false);
    
    // 2. Swap logic to Child
    $egi = $clone;
    $wasCloned = true;
}
```

### 1.2 Availability Check & Race Condition
Standard EGIs perform an availability check (`EgiAvailabilityService`).
**Issue**: A user cannot mint an EGI they already own.
**Conflict**: When a Master is cloned, the Child is *immediately* assigned to the Buyer.
**Fix**: If `$wasCloned` is true, the availability check is **SKIPPED** (logic: we just created it for you, it is by definition available).


### 1.3 Secondary Market Enablement (Rebind)
By default, Master Templates might have `rebind = false` (to prevent selling the Template itself).
When a **Clone** is created, it represents a user-owned asset.
**System Action**: The `CloneEgiFromMasterAction` explicitly sets `$child->rebind = true`.
**Result**: The Buyer can immediately list the EGI on the Secondary Market (Rebind) if desired, without admin intervention.

---

## 2. Response Handling (Browser vs API)

To support both standard Web Forms and AJAX/API calls, the controller implements strict **Content Negotiation**:

-   **Browser Request** (`Form Submit`): Returns `RedirectResponse` (redirects to `mint.show` or `back()` with errors).
-   **AJAX/API Request** (`wantsJson()` or `Accept: application/json`): Returns `JsonResponse` (JSON data).

This prevents "Raw JSON" being displayed in the browser window during standard usage.

---

## 3. Microservice Authentication

The Algorand Microservice (`algokit-microservice`) is protected by a Bearer Token.

### 3.1 Security Requirement
All requests to the microservice (except `/health`) must include the header:
`Authorization: Bearer <ALGOKIT_API_TOKEN>`

### 3.2 Configuration
1.  **Laravel (.env)**:
    `ALGOKIT_API_TOKEN=your-secure-token-here`
2.  **Microservice (.env)**:
    `ALGOKIT_API_TOKEN=your-secure-token-here`

### 3.3 Implementation
-   **Laravel**: `AlgorandService.php` injects the token into outgoing HTTP requests.
-   **Node.js**: `server.js` middleware validates the token before processing any mint/transfer logic.

---

## 4. Troubleshooting

### Master Minted by Mistake?
If a Master EGI appears as "Minted" (`mint=true`):
1.  **Cause**: Run of old code (pre-v2) or manual force minting.
2.  **Fix**: Run restoration script to:
    -   Set `mint = false`
    -   Set `owner_id = user_id` (Creator)
    -   Delete orphan `egi_blockchain` records.

### "Unexpected Error" / Crash
Check `storage/logs/error_manager.log` for **UEM** codes:
-   `MINT_BLOCKED_WORKER_UNAVAILABLE`: Start `php artisan queue:work --queue=blockchain`.
-   `DIRECT_MINT_PROCESS_ERROR`: General catch-all (often Microservice connection or Payment fail).

---
