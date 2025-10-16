# 📋 FlorenceEGI - TODO List (Updated: 2025-10-15)

## ✅ COMPLETATI

### 🎨 Visual Feedback EGI Mintati

-   [x] Background dinamico (amber/emerald per mintati, standard per non mintati)
-   [x] Badge prominente "EGI CERTIFICATO SU BLOCKCHAIN" con ASA ID
-   [x] Link verifica su Pera Wallet explorer (corretto da `/tx/` a `/asset/`)
-   [x] Pulse animation su icona shield
-   [x] Tutte le stringhe i18n compliant (zero hardcoded text)

### 🔒 CRUD Lockdown Post-Mint

-   [x] Lock overlay su utility-manager (z-50, backdrop-blur)
-   [x] Traits editing disabled via `$canEdit` check
-   [x] Backend validation in TraitsApiController
-   [x] Backend validation in UtilityController
-   [x] CRUD panel: Title, Description, Creation Date, Published → LOCKED
-   [x] CRUD panel: Price → RIMANE EDITABILE (corretto)
-   [x] Backend validation in EgiController (whitelist `['price']`)
-   [x] UEM error codes: TRAITS_EGI_MINTED, UTILITY_EGI_MINTED, EGI_METADATA_IMMUTABLE
-   [x] Traduzioni italiane complete per messaggi errore

### 📇 EGI Card - Logica Co-creatore

-   [x] **egi-card.blade.php**: Co-creatore mostrato SOLO se mintato (`$isMinted && $egi->blockchain->buyer`)
-   [x] **egi-card-list.blade.php**: Stessa logica applicata
-   [x] Badge distintivi: Viola Innovazione (mintato), Arancio Energia (reserved), Verde Rinascita (da attivare)

### 🖼️ Vista Mint - Completezza Dati

-   [x] **Utility nella vista mint**: Sezione completa con tipo, descrizione, gallery immagini (linee 136-175)
-   [x] **Traits nella vista mint**: Grid responsiva con categoria, icona, rarity bar (linee 177-280)
-   [x] Layout 3 colonne: Preview + Blockchain | CoA + Utility + Traits | Checkout
-   [x] Design gradient: Viola-purple per utility, Blue-indigo per traits
-   [x] Rarity visualization con color-coded bar (mythic → common)

---

## 🔴 PRIORITÀ ALTA ✅ TUTTO COMPLETATO!

### 📇 EGI Card - Logica Co-creatore ✅

-   [x] **Modificare egi-card.blade.php**: Mostrare co-creatore SOLO se `$egi->token_EGI` esiste
    -   Logica VECCHIA: Chi fa prenotazione → Co-creatore ❌
    -   Logica NUOVA: Chi fa MINT → Vero Co-creatore ✅
    -   Chi fa prenotazione → "Offerta attiva" (non co-creatore)
    -   **Implementato**: Riga 627-649 di egi-card.blade.php
        -   Condizione: `@if ($isMinted && $egi->blockchain && $egi->blockchain->buyer)`
        -   Mostra avatar e nome del buyer solo se mintato
        -   Altrimenti mostra prenotazione (green/amber badge)
-   [x] **Adeguare egi-card-list.blade.php** di conseguenza (stessa logica)
-   [x] Badge distintivo: Viola (#8E44AD) per mintato, Arancio (#E67E22) per reserved, Verde (#2D5016) per da attivare

### 🖼️ Vista Mint - Completezza Dati ✅

-   [x] **Utility nella vista mint** (`mint/checkout.blade.php` linee 136-175)
    -   Sezione con gradient viola-purple
    -   Mostra tipo, descrizione completa
    -   Gallery immagini scrollabile (thumb + click per large)
    -   Design coerente con brand guidelines
-   [x] **Traits nella vista mint** (`mint/checkout.blade.php` linee 177-280)
    -   Grid responsiva 2-3 colonne
    -   Badge categoria con icona e colore
    -   Rarity percentage con barra colorata (mythic gold → common gray)
    -   Indica se trait ha immagine allegata (📷 badge)
    -   Mostra unità di misura se presente

---

## 🟡 PRIORITÀ MEDIA

### 💰 Payment Distributions - Dual Source Statistics

**CONTEXT:** `payment_distributions` già popolata da 2 fonti:

-   `source_type = 'reservation'` → prenotazioni (forecast revenue)
-   `source_type = 'mint'` → acquisti reali blockchain-certified

**PROBLEMA:** Statistiche attuali NON distinguono tra forecast (reservation) e revenue reale (mint).

**TASK: Implementare Analytics Dual Source**

-   [x] **FIX CRITICO**: `calculateMintDistributions()` scriveva `distribution_status = PENDING` invece di `CONFIRMED` ✅
    -   Rationale: Mint = pagamento già completato e certificato blockchain
    -   Commit: `[FIX] PaymentDistributionService - Set CONFIRMED status for mint distributions`
-   [ ] **StatisticsService - Separation Logic**:
    -   [ ] Method: `getReservationDistributionStats()` → Filter `source_type = 'reservation'`
    -   [ ] Method: `getMintDistributionStats()` → Filter `source_type = 'mint'`
    -   [ ] Method: `getCombinedDistributionStats()` → Aggregate entrambe le fonti
    -   [ ] Refactor metodi esistenti che usano `PaymentDistribution`:
        -   Aggiungere parametro `$sourceType = null` (default: tutti)
        -   Rispettare filtro quando specificato
-   [ ] **PaymentDistribution Model - Scopes**:
    -   [ ] Scope: `scopeReservationSource()` → `where('source_type', 'reservation')`
    -   [ ] Scope: `scopeMintSource()` → `where('source_type', 'mint')`
    -   [ ] Update static statistics methods per usare scopes
-   [ ] **Dashboard Admin - Dual Source Views**:
    -   [ ] Sezione "Revenue Analytics" con 3 tabs:
        -   Tab 1: "Reservation Revenue" (forecast pre-mint)
        -   Tab 2: "Mint Revenue" (blockchain certified)
        -   Tab 3: "Combined Overview" (aggregato totale)
    -   [ ] Grafici comparativi:
        -   Line chart: Reservation vs Mint revenue over time
        -   Conversion rate: Reservations → Mint completion %
        -   Average mint price vs average reservation price
-   [ ] **Testing**:
    -   [ ] Unit test: Dual source statistics methods
    -   [ ] Integration test: Dashboard rendering con dati mixed
    -   [ ] Verify: Existing reservation stats unchanged
-   [ ] **Documentation**:
    -   [ ] Update `docs/statistics-catalog.md` con dual source queries
    -   [ ] Comment inline PERCHÉ separare reservation vs mint stats

### 🏷️ Sistema Asta (ex Prenotazione)

**Rinominare "Prenotazione" → "Asta" in tutto il sistema:**

-   [ ] Rinominare model `Reservation` → `Auction` (o aggiungere type 'auction')
-   [ ] Aggiungere campi obbligatori asta:
    -   `minimum_price` (decimal)
    -   `start_date` (datetime)
    -   `end_date` (datetime)
-   [ ] Migration per aggiungere campi mancanti
-   [ ] Aggiornare controller e service con logica asta
-   [ ] UI per Creator/Owner: form configurazione asta

### 📊 Royalty Monitor Dashboard

**Vista Creator/Owner per monitorare pagamenti:**

-   [ ] Creare `RoyaltyMonitorController`
-   [ ] Vista dashboard con breakdown pagamenti:
    -   Chi paga (buyer)
    -   A chi (wallet beneficiari da model `Wallet`)
    -   Quanto (percentuali royalty)
    -   Quando (date transazioni)
-   [ ] Integrare con `wallets` collegati alla collection
-   [ ] Calcolo automatico split royalty basato su `collection->wallets`

---

## 🟢 PRIORITÀ BASSA (Future Enhancement)

### ⚖️ Sezione Legale - Diritto di Seguito

**Creare sezione completa riferimenti legali:**

-   [ ] Pagina dedicata o sezione in egis.show
-   [ ] Contenuti da includere:
    -   Legge diritto di seguito (Art. 144-148 Legge sul Diritto d'Autore)
    -   Proprietà intellettuale creator post-vendita
    -   Cosa acquista realmente l'acquirente (diritti trasferiti vs riservati)
    -   Cosa può/non può fare il buyer (riproduzione, commercializzazione, etc.)
    -   Procedure alterazione opera (CoA update, notifica creator, etc.)
    -   Chi fa cosa in caso di alterazione/restauro
-   [ ] Link a normative di riferimento
-   [ ] FAQ interattive
-   [ ] i18n completo (IT/EN minimo)

### 💬 Chat Push Creator ↔ Owner

**Sistema comunicazione diretta:**

-   [ ] Valutare tecnologia: Laravel Echo + Pusher vs Socket.io
-   [ ] Model `Message` con relazioni User-to-User
-   [ ] UI chat component (stile WhatsApp/Telegram)
-   [ ] Notifiche push browser
-   [ ] Notifiche email optional
-   [ ] Privacy: chat visibili solo a Creator e Owner dell'EGI specifico
-   [ ] Archiviazione messaggi e policy retention
-   [ ] **NOTA**: Lavoro significativo, da ultimo

---

## 🔵 BLOCKCHAIN AVANZATO (Critical per Merchants)

### 🔐 Auto-Wallet Creation per Merchant

**Workflow registrazione Merchant:**

-   [ ] Trigger auto-creazione wallet Algorand su registrazione user type='Merchant'
-   [ ] Generazione keypair tramite AlgoSDK
-   [ ] Storage sicuro seed phrase:
    -   Encryption forte (AES-256-GCM)
    -   Key derivation (Argon2id)
    -   Vault separato dal DB principale
    -   Access logging completo
-   [ ] UI onboarding: Mostra seed phrase UNA SOLA VOLTA
-   [ ] Conferma utente: "Ho salvato seed phrase" (checkbox + conferma email)

### 💼 Wallet Redemption Flow

**Procedura riscatto wallet da parte Merchant:**

-   [ ] Vista dedicata: `resources/views/merchant/wallet-redemption.blade.php`
-   [ ] Link da menu: `vanilla-desktop-menu.blade.php` → Account Management Card
-   [ ] Flusso multi-step sicuro:
    1. **Verifica identità** (2FA obbligatorio)
    2. **Warning screen** (conseguenze, no recovery possibile)
    3. **Download seed phrase** (PDF crittografato + QR code)
    4. **Conferma manuale** (typing challenge: "CONFERMO RISCATTO")
    5. **Verifica email** (link conferma con scadenza 15min)
    6. **Trasferimento ownership** (wallet passa sotto controllo merchant)
    7. **Cancellazione chiavi** (seed phrase eliminata permanentemente)
-   [ ] Rollback possibile FINO a step 6 (non dopo cancellazione)
-   [ ] Audit trail completo (GDPR compliance)
-   [ ] UEM integration per errori critici
-   [ ] Test E2E del flusso completo

---

## 📊 PROGRESS SUMMARY

**Completati**: 24 task ✅  
**Priorità Alta**: 0 task - **FASE COMPLETATA** 🎉
**Priorità Media**: 19 task (1 fix critico ✅, 18 da fare)
**Priorità Bassa**: 7 task  
**Blockchain Avanzato**: 12 task

**Totale**: 24/59 task completati (40.7%)

---

## 🎯 NEXT IMMEDIATE STEPS

1. ✅ ~~Fix EGI Card co-creatore logic~~ **COMPLETATO**
2. ✅ ~~Aggiungere utility/traits a vista mint~~ **COMPLETATO**
3. ⏳ **PROSSIMO**: Testare CRUD price management Creator/Owner (30 min)
4. ⏳ Design sistema Asta (planning session - 2-3 ore)
5. ⏳ Implementare Royalty Monitor Dashboard (4-6 ore)

**Estimated time to MVP completion**: ~70 ore sviluppo + 20 ore testing

---

## 🏆 MILESTONE RAGGIUNTO: PRIORITÀ ALTA 100% COMPLETATA!

Tutte le feature critiche per il lancio MVP sono implementate:

-   ✅ Visual feedback EGI mintati (blockchain certification UX)
-   ✅ CRUD lockdown completo post-mint (data integrity)
-   ✅ Co-creatore logic corretta (business logic)
-   ✅ Vista mint completa con utility/traits (buyer transparency)

**Prossima fase**: Priorità Media - Price management + Auction system + Royalty monitor

controllare permessi pa. non deve creare egi.
weak per mettere in vendita deve autenticarsi strong.
migrazione da weak a strong.

si deve poter automintare un egi.

controllare funzione di ricerca

il CoA lo devo mintare? 
sulla vista del mint occorre visualizzare certificato di proprietà. denaro speso. epp a cui si è contribuito e in che misura. denaro inviato a piattaforma,creator,feangette.



