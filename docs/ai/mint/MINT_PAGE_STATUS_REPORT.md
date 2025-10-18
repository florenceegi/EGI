# 🎨 MINT PAGE - STATUS REPORT & IMPLEMENTATION PLAN

**Data:** 2025-10-18  
**Autore:** Padmin D. Curtis (AI Partner OS3.0)  
**Contesto:** Analisi esistente + proposta implementazione pagina mint completa

---

## 📋 COSA ESISTE GIÀ (STATO ATTUALE)

### ✅ **1. CONTROLLER: MintController.php**

**Path:** `app/Http/Controllers/MintController.php`

**Methods esistenti:**

-   ✅ `showCheckout(Request $request)` → Vista checkout mint
-   ✅ `processMint(Request $request): JsonResponse` → Process pagamento + mint
-   ✅ `checkMintStatus($egiId): JsonResponse` → Polling status mint
-   ✅ `mockPaymentProcessing()` → Mock payment FIAT (Stripe/PayPal)

**Funzionalità:**

-   Authorization checks (user = winner reservation)
-   Treasury funds check BEFORE payment
-   EgiBlockchain record creation
-   MintEgiJob dispatch (async minting)
-   Payment distribution tracking
-   Co-creator display name support (AREA 5.5.1)
-   GDPR audit trails (UEM, ULM, AuditLogService)

---

### ✅ **2. VIEW PARZIALE: mint/checkout.blade.php**

**Path:** `resources/views/mint/checkout.blade.php` (MA NON ESISTE FISICAMENTE!)

**⚠️ PROBLEMA RILEVATO:**

```
Error: Non è possibile risolvere il file non esistente
'vscode-remote://wsl+ubuntu/home/fabio/EGI/resources/views/mint/checkout.blade.php'
```

**Tuttavia nel codice ci sono riferimenti a:**

-   Route `mint.checkout` funzionante
-   Logica JS per post-mint success UI
-   Translations complete in 6 lingue
-   Integration con certificate generation endpoint

**Funzionalità presenti nel codice (anche se file mancante):**

-   ✅ Payment method selection (Stripe/PayPal)
-   ✅ Optional buyer wallet input
-   ✅ Co-creator display name field
-   ✅ Worker availability check con progress bar
-   ✅ Async mint status polling
-   ✅ Post-mint success UI con:
    -   Blockchain info (ASA ID, TX ID, Pera Explorer link)
    -   Payment breakdown table
    -   Certificate download section con thumbnail
    -   CTA buttons (View EGI, View Certificate)
-   ✅ Post-mint partial success fallback
-   ✅ Debug panel per troubleshooting

---

### ✅ **3. SERVICES**

#### **EgiMintingService.php**

-   ✅ `mintEgi(Egi $egi, User $user, array $metadata): EgiBlockchain`
-   ✅ `mintEgiWithPayment()` → Mint + automatic payment distribution
-   ✅ GDPR compliance (consent check, audit trail)
-   ✅ Integration con AlgorandService

#### **EgiPurchaseWorkflowService.php**

-   ✅ Complete purchase workflow orchestration
-   ✅ Payment processing → Minting → Certificate generation
-   ✅ Transaction management (DB + blockchain)

#### **AlgorandService.php**

-   ✅ Blockchain interaction via microservice
-   ✅ Treasury funds check
-   ✅ ASA minting
-   ✅ Metadata building (EgiMetadataBuilderService)

#### **PaymentDistributionService.php**

-   ✅ Automatic payment split tra Creator, EPP, Platform
-   ✅ `recordMintDistribution()` method
-   ✅ Integration con payment_distributions table

---

### ✅ **4. MODELS & DATABASE**

#### **EgiBlockchain Model**

```php
// Fields presenti:
'egi_id', 'asa_id', 'blockchain_tx_id', 'platform_wallet',
'payment_method', 'psp_provider', 'payment_reference',
'paid_amount', 'paid_currency', 'buyer_user_id', 'buyer_wallet',
'ownership_type', 'mint_status', 'mint_error_message',
'minted_at', 'certificate_uuid', 'co_creator_display_name'
```

#### **PaymentDistribution Model**

```php
// Fields per payment breakdown:
'source_type' => 'mint',
'recipient_user_id', 'recipient_wallet',
'amount_eur', 'currency', 'role'
```

---

### ✅ **5. ROUTES**

**Path:** `routes/web.php`

```php
// Mint routes authenticated
Route::get('/mint/checkout', [MintController::class, 'showCheckout'])
    ->name('mint.checkout');

Route::post('/mint/process', [MintController::class, 'processMint'])
    ->name('mint.process');

Route::get('/mint/status/{egiId}', [MintController::class, 'checkMintStatus'])
    ->name('mint.status');

Route::post('/mint/{egiId}/certificate/generate',
    [EgiReservationCertificateController::class, 'generatePostMintCertificate'])
    ->name('mint.certificate.generate');
```

---

### ✅ **6. TRANSLATIONS (6 lingue)**

**Files:** `resources/lang/{it,en,es,fr,de,pt}/mint.php`

**Sezioni disponibili:**

-   `page_title`, `meta_description`
-   `header_title`, `header_description`
-   `egi_preview.*` (creator, description)
-   `blockchain_info.*` (network, token_type, supply, royalty)
-   `payment.*` (price, method, wallet, co_creator_name)
-   `buyer_info.*` (wallet_label, wallet_placeholder, wallet_help)
-   `confirmation.*` (agree_terms, final_warning)
-   `success.*` (minted, transaction_id, certificate_ready)
-   `errors.*` (missing_params, invalid_reservation, payment_failed)
-   `status.*` (completed, processing, failed)
-   `post_mint.*` (congratulations, certificate_blockchain, payment_breakdown)
-   `worker.*` (checking, starting, ready, unavailable)
-   `notification.*` (success_title, success_message, asa_label)
-   `coa.*` (certified, certificate_number, issuer, issue_date)
-   `traits.*` (traits_title, rarity, collection)

---

### ✅ **7. COA THUMBNAIL LOGIC (da replicare)**

**Path:** `resources/views/components/coa/sidebar-section.blade.php`

**Function:** `renderCoaPdfThumb(container, coaId)`

**Funzionamento:**

1. Fetch `/coa/{coaId}/pdf/check` per verificare PDF esistente
2. Load **pdf.js** library per rendering client-side
3. Render first page del PDF su canvas HTML
4. Scale a target width (140px default)
5. Click handler per aprire PDF in nuova tab

**Dependencies:**

-   pdf.js CDN: `https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/build/pdf.min.js`
-   Worker: `https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/build/pdf.worker.min.js`

---

### ✅ **8. CERTIFICATE GENERATION POST-MINT**

**Controller:** `EgiReservationCertificateController.php`

**Method:** `generatePostMintCertificate(Request $request, int $egiId)`

**Returns JSON:**

```json
{
    "success": true,
    "data": {
        "certificate_url": "URL al PDF certificato",
        "certificate_uuid": "UUID certificato",
        "public_url": "URL pagina pubblica certificato",
        "payment_breakdown": [
            {
                "recipient_user_id": 123,
                "recipient_name": "Creator Name",
                "recipient_wallet": "ALGO_ADDRESS...",
                "amount_eur": 50.0,
                "currency": "EUR",
                "role": "creator"
            }
        ],
        "blockchain_data": {
            "asa_id": "123456789",
            "tx_id": "BLOCKCHAIN_TX_HASH...",
            "minted_at": "18/10/2025 14:30:00",
            "pera_explorer_url": "https://explorer.perawallet.app/asset/123456789"
        }
    }
}
```

**Authorization:** Solo buyer può generare (check `buyer_user_id`)

---

## ❌ COSA MANCA

### **1. VIEW FILE FISICO NON ESISTE**

```bash
# File non trovato:
resources/views/mint/checkout.blade.php
```

**Problema:** Controller chiama `view('mint.checkout')` ma file non esiste.

**Soluzione necessaria:** Creare file completo con tutto il markup.

---

### **2. PAGINA MINT NON HA SEZIONE FIAT/WALLET CHIARA**

**Richiesto da Fabio:**

> "CI deve essere una vista che consente di effettuare il pagamento in FIAT / O MEDIANTE WALLET"

**Attuale:**

-   Pagamento sempre FIAT (Stripe/PayPal)
-   Wallet input è "opzionale" per destinazione post-mint
-   **Non c'è scelta "Pago in FIAT" vs "Pago con Wallet"**

**⚠️ MiCA-SAFE CHECK:**

-   ❌ **NON possiamo accettare pagamenti CRYPTO** (richiede licenza CASP)
-   ✅ **Possiamo solo FIAT** via PSP (Stripe, PayPal, bank transfer)

**Soluzione proposta:**

-   **Livello 1 (MVP):** Pagamento FIAT + opzione "Ho un wallet" (mint diretto lì)
-   **Livello 2 (Future):** Pagamento FIAT + partner CASP gestisce crypto (come descritto in copilot-instructions)

---

### **3. NICKNAME FIELD NON PROPONE user->name**

**Richiesto da Fabio:**

> "DEVE POTER INSERIRE IL NICKNAME MA DEVE ESSERE PROPOSTO IL VALORE DEL CAMPO castato ussr->name"

**Attuale nel codice:**

```php
// Esiste il campo co_creator_display_name
// MA NON è pre-populated con user->name
```

**Soluzione:** Aggiungere `value="{{ Auth::user()->name }}"` al campo input.

---

### **4. IMMAGINE EGI + PREZZO ORIGINALE**

**Richiesto da Fabio:**

> "DEVE AVERE L'IMMAGINE DEL EGI IL PREZZO ORIGINALE"

**Verifica necessaria:** Nel codice esistente ci sono riferimenti a:

-   EGI preview section
-   Price display
-   Ma serve confermare se mostra prezzo base o prezzo reservation

---

### **5. SUCCESS PAGE POST-MINT**

**Richiesto da Fabio:**

> "DOPO IL MINT DEVE APRIRSI LA PAGINA DLE MINT AVVENUTO CON..."

**Cosa serve mostrare:**

1. ✅ Dati blockchain (TX ID, ASA ID) → **ESISTE nel codice**
2. ✅ TX/ASA linkati a blockchain → **ESISTE Pera Explorer link**
3. ✅ Messaggio successo e complimenti → **ESISTE translations**
4. ✅ Box costi suddivisi tra wallet → **ESISTE payment_breakdown**
5. ✅ Thumbnail PDF certificato → **ESISTE logica renderCoaPdfThumb**
6. ⚠️ **Thumbnail stessa usata per CoA in egis.show** → DA VERIFICARE

**Soluzione:** Success page è **già implementata in checkout.blade.php** (funzioni JS), ma serve:

-   Verificare che thumbnail sia identica a CoA in egis.show
-   Testare che tutto funzioni correttamente

---

## 🎯 PIANO IMPLEMENTAZIONE

### **STEP 1: RICREARE FILE mint/checkout.blade.php**

**Azioni:**

1. Cercare backup o versione precedente del file
2. Se non esiste: ricostruire da codice esistente (route chiama view)
3. Verificare che tutto il markup sia presente:
    - Layout grid 3 colonne
    - EGI preview con immagine
    - Payment form con Stripe/PayPal radio
    - Wallet input opzionale
    - Co-creator name field
    - Post-mint success UI (JS)

---

### **STEP 2: FIX NICKNAME FIELD (user->name default)**

**File da modificare:** `mint/checkout.blade.php`

**Change:**

```php
// PRIMA (attuale):
<input type="text" id="co_creator_display_name" name="co_creator_display_name"
       placeholder="{{ __('mint.payment.co_creator_name_placeholder') }}">

// DOPO (proposto):
<input type="text" id="co_creator_display_name" name="co_creator_display_name"
       value="{{ Auth::user()->name }}"
       placeholder="{{ __('mint.payment.co_creator_name_placeholder') }}">
```

---

### **STEP 3: CHIARIRE PAYMENT METHOD (FIAT vs WALLET)**

**Due approcci:**

#### **Approccio A: UI Semplificata (Raccomandato MVP)**

**Sezione Payment Method:**

```html
<h3>Metodo di Pagamento</h3>
<p class="text-sm text-gray-600">
    Pagamento sicuro in FIAT tramite PSP autorizzati (MiCA-compliant)
</p>

<!-- Radio buttons: Stripe / PayPal / Bank Transfer -->
<label>
    <input type="radio" name="payment_method" value="stripe" checked />
    Carta di Credito (Stripe)
</label>
<label>
    <input type="radio" name="payment_method" value="paypal" />
    PayPal
</label>
```

**Sezione Wallet Destinazione:**

```html
<h3>Destinazione EGI (Opzionale)</h3>
<div class="p-4 border rounded">
    <label class="flex items-center mb-3">
        <input
            type="checkbox"
            id="has_wallet_checkbox"
            onchange="toggleWalletInput()"
        />
        Ho già un wallet Algorand
    </label>

    <div id="wallet_input_container" style="display: none;">
        <label>Indirizzo Wallet Algorand</label>
        <input
            type="text"
            name="buyer_wallet"
            placeholder="ALGO ADDRESS (58 caratteri)"
        />
        <p class="text-xs text-gray-500">
            L'EGI verrà trasferito automaticamente qui dopo il mint. Se non
            inserisci un wallet, sarà custodito nel Treasury FlorenceEGI.
        </p>
    </div>
</div>
```

---

#### **Approccio B: UI Espansa (FIAT vs CRYPTO - FUTURE)**

⚠️ **BLOCCO MiCA-SAFE:** Possiamo implementare UI, ma backend DEVE passare tramite partner CASP.

```html
<h3>Metodo di Pagamento</h3>
<div class="payment-method-selector">
    <button type="button" class="payment-tab active" data-method="fiat">
        💶 Pagamento FIAT
    </button>
    <button type="button" class="payment-tab" data-method="crypto">
        🪙 Pagamento Crypto (Prossimamente)
    </button>
</div>

<div id="fiat-payment-panel">
    <!-- Stripe/PayPal radio buttons -->
</div>

<div id="crypto-payment-panel" style="display: none;">
    <div class="alert alert-info">
        <p>Pagamento crypto tramite partner CASP autorizzato.</p>
        <p>Nessuna custodia crypto da parte di FlorenceEGI.</p>
        <p><strong>Status:</strong> In sviluppo - disponibile Q1 2026</p>
    </div>
</div>
```

**Raccomandazione:** **Approccio A** per MVP, poi Approccio B quando partner CASP è integrato.

---

### **STEP 4: VERIFICARE THUMBNAIL PDF (CoA style)**

**Task:**

1. Verificare che `renderCoaPdfThumb()` funzioni correttamente in checkout success
2. Confrontare con thumbnail in `egis.show` sidebar CoA section
3. Assicurarsi che usino **stessa logica** e **stesso styling**

**Codice da replicare:**

```javascript
// From: components/coa/sidebar-section.blade.php
async function renderCoaPdfThumb(container, coaId) {
    // 1. Check PDF exists
    const res = await fetch(`/coa/${coaId}/pdf/check`);
    const info = await res.json();

    // 2. Load pdf.js
    await ensurePdfJsLoaded();

    // 3. Render first page
    const pdf = await window["pdfjsLib"].getDocument(info.download_url).promise;
    const page = await pdf.getPage(1);
    const canvas = renderPageToCanvas(page, targetWidth);
    container.appendChild(canvas);
}
```

**Adattamento per mint checkout:**

-   Container: `<div id="certificate-thumbnail" data-thumb-width="200"></div>`
-   Call dopo certificate generation success
-   Same click handler per aprire PDF

---

### **STEP 5: VERIFICARE PREZZO ORIGINALE MOSTRATO**

**Task:**

1. Controllare se `mint/checkout.blade.php` mostra `$egi->price` (base price)
2. O se mostra `$reservation->amount_eur` (offer price)
3. Fabio vuole "prezzo originale" = prezzo base EGI

**Code snippet da verificare:**

```php
// Nel template checkout:
@if ($reservation)
    <!-- Mostra prezzo reservation (offerta vincente) -->
    <span>€{{ number_format($reservation->amount_eur, 2) }}</span>
@else
    <!-- Mostra prezzo base EGI -->
    <span>€{{ number_format($egi->price, 2) }}</span>
@endif

// Fabio vuole SEMPRE mostrare $egi->price come "prezzo originale"?
// + eventualmente mostrare "Sconto da prenotazione" se $reservation->amount_eur < $egi->price
```

---

### **STEP 6: TESTARE WORKFLOW COMPLETO**

**Flow da testare:**

1. **Accedi** come user con reservation vincente
2. **Vai** su `GET /mint/checkout?egi_id=123&reservation_id=456`
3. **Verifica UI:**
    - ✅ Immagine EGI visibile
    - ✅ Prezzo base mostrato
    - ✅ Payment method radio (Stripe/PayPal)
    - ✅ Wallet input opzionale (inizialmente nascosto)
    - ✅ Nickname field pre-filled con `user->name`
    - ✅ Co-creator name warning presente
    - ✅ Worker check eseguito prima submit
4. **Submit form** → `POST /mint/process`
5. **Polling** mint status → `GET /mint/status/{egiId}`
6. **Quando minted:**
    - ✅ Call `POST /mint/{egiId}/certificate/generate`
    - ✅ Show post-mint success UI:
        - ✅ Blockchain info card (ASA ID, TX ID linkati)
        - ✅ Payment breakdown table (solo importi > 0)
        - ✅ Certificate thumbnail (pdf.js rendering)
        - ✅ Download certificate button
        - ✅ View EGI button
        - ✅ View certificate button

---

## 🔍 DOMANDE PER FABIO

### **Q1: Payment Method - Cosa intendiamo esattamente?**

**Scenario A (Attuale):**

-   Pagamento SEMPRE in FIAT (Stripe/PayPal)
-   Wallet input è solo "destinazione post-mint"
-   **NON c'è scelta di pagare con crypto**

**Scenario B (Richiesto?):**

-   Due tab: "Pago in FIAT" | "Pago con Wallet"
-   Tab "Pago con Wallet" = ⚠️ **RICHIEDE partner CASP** (non MiCA-safe da soli)

**Domanda:** Quale scenario vuoi implementare ora?

---

### **Q2: Prezzo Originale - Quale mostrare?**

**Opzione A:**

-   Mostra sempre `$egi->price` (prezzo base)
-   Se c'è reservation con prezzo diverso → mostra "Sconto applicato"

**Opzione B:**

-   Mostra `$reservation->amount_eur` (prezzo offerta vincente)
-   Nascondi prezzo base

**Domanda:** Vuoi sempre mostrare prezzo base, anche se reservation ha prezzo diverso?

---

### **Q3: Thumbnail PDF - Stesso style di CoA in egis.show?**

**Attuale:** CoA sidebar section ha thumbnail con:

-   Width: 140px
-   Click → apre PDF in nuova tab
-   Render via pdf.js client-side

**Domanda:** Mint success page deve avere IDENTICA thumbnail (stesse dimensioni, stesso stile)?

---

### **Q4: File mint/checkout.blade.php mancante - Cosa è successo?**

**Controller chiama:**

```php
return view('mint.checkout', compact(...));
```

**Ma file non esiste in:**

```
resources/views/mint/checkout.blade.php
```

**Domanda:**

1. È stato cancellato per sbaglio?
2. Esiste backup?
3. Serve ricrearlo da zero?

---

## 🚀 PROSSIMI PASSI PROPOSTI

### **TASK 1: Recover/Create checkout.blade.php** ⏱️ 30min

-   Cercare file in git history
-   Se non esiste: ricreare da route/controller logic
-   Commit: `[FIX] Restore mint/checkout.blade.php`

### **TASK 2: Fix Nickname Field Default** ⏱️ 5min

-   Aggiungere `value="{{ Auth::user()->name }}"`
-   Test che campo sia pre-populated
-   Commit: `[FEAT] Pre-fill co-creator name with user->name`

### **TASK 3: Clarify Payment UI** ⏱️ 20min

-   Implementare Approccio A (FIAT + wallet opzionale)
-   Add MiCA compliance notice
-   Commit: `[UX] Clarify payment method section (FIAT only MVP)`

### **TASK 4: Verify Price Display** ⏱️ 10min

-   Check se mostra `$egi->price` o `$reservation->amount_eur`
-   Adjust secondo risposta Q2
-   Commit: `[FIX] Display original EGI price in checkout`

### **TASK 5: Test Thumbnail Rendering** ⏱️ 15min

-   Test `renderCoaPdfThumb()` in post-mint success
-   Verify same style as egis.show sidebar
-   Commit: `[TEST] Verify certificate thumbnail rendering`

### **TASK 6: E2E Test Workflow** ⏱️ 30min

-   Test completo: reservation → mint → success → certificate
-   Verify all UI elements present
-   Commit: `[TEST] E2E mint workflow verification`

---

## 📊 EFFORT ESTIMATE

| Task                          | Effort     | Priority |
| ----------------------------- | ---------- | -------- |
| TASK 1: Recover checkout view | 30min      | P0 🔴    |
| TASK 2: Fix nickname default  | 5min       | P1 🟡    |
| TASK 3: Payment UI clarity    | 20min      | P1 🟡    |
| TASK 4: Price display         | 10min      | P1 🟡    |
| TASK 5: Thumbnail test        | 15min      | P2 🟢    |
| TASK 6: E2E test              | 30min      | P2 🟢    |
| **TOTAL**                     | **110min** | **~2h**  |

---

## 🎯 READY TO PROCEED?

**Aspetto tue risposte a Q1-Q4 per procedere con implementazione ottimale.**

**Posso anche iniziare subito con TASK 1 (recover file) se vuoi, mentre discutiamo le altre questioni.**

**Fammi sapere! 🚀**
