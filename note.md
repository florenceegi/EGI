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

### � CoA Blockchain Certificate + Post-Mint Payment Breakdown ✅ **COMPLETATO 2025-10-16**

**CONTEXT:** Post-mint UX deve mostrare certificato blockchain + breakdown pagamenti senza reload pagina.

**IMPLEMENTAZIONE COMPLETA:**

-   [x] **Database Schema** (`egi_reservation_certificates` table):
    -   Rinominata colonna `blockchain_algorand_id` → `egi_blockchain_id` (naming convention)
    -   Foreign key constraint a `egi_blockchain` table con cascade delete
    -   Performance indexes su `certificate_type` e composite `(egi_id, certificate_type)`
    -   Migration: `2025_10_16_095951_add_blockchain_support_to_egi_reservation_certificates.php`
-   [x] **Model** (`EgiReservationCertificate`):
    -   Relationship `egiBlockchain()` aggiunta per certificati mint
    -   Scopes: `blockchainType()` e `reservationType()` per filtrare certificate_type
    -   Fillable fields aggiornati: `certificate_type`, `egi_blockchain_id`
-   [x] **Service** (`CertificateGeneratorService`):
    -   Method `generateBlockchainCertificate(Egi, EgiBlockchain)` → Crea certificato PDF + record DB
    -   Dual storage: record in `egi_reservation_certificates` + path in `egi_blockchain`
    -   PDF include: ASA ID, TX hash, wallet buyer, timestamp, QR code verifica
-   [x] **Controller** (`EgiReservationCertificateController`):
    -   Endpoint `generatePostMintCertificate(Request, int $egiId)` → AJAX endpoint
    -   Authorization: solo buyer può generare certificato (check `buyer_user_id`)
    -   Query payment_distributions WHERE `source_type='mint'` AND `amount_eur > 0`
    -   Return JSON: `certificate_url`, `payment_breakdown[]`, `blockchain_data`
-   [x] **Routes** (`web.php`):
    -   Route: `POST /mint/{egiId}/certificate/generate` (auth required)
-   [x] **Frontend** (`mint/checkout.blade.php`):
    -   ❌ Rimosso `window.location.reload()` dopo mint success
    -   ✅ Implementata SPA-style post-mint UI con 3 stati: loading → success → partial success
    -   Function `updateUIToMinted()` → Async call a certificate generation endpoint
    -   Function `showPostMintLoading()` → Spinner durante generazione
    -   Function `showPostMintSuccess(data)` → UI completa con:
        -   Celebration message con icona check verde
        -   Blockchain info card (ASA ID, TX hash, Pera Explorer link)
        -   Payment breakdown table (solo importi > 0 EUR)
        -   Certificate download section con thumbnail
        -   CTA buttons: "Visualizza EGI" + "Visualizza Certificato"
    -   Function `showPostMintPartialSuccess(data)` → Fallback se certificato fallisce
-   [x] **i18n** (`mint.php` + `certificate.php`):
    -   Sezione `post_mint` con 20+ chiavi (congratulations, loading, success, fallback)
    -   Sezione `roles` per payment distributions (creator, co_creator, platform, royalty)
    -   Messaggi errore: unauthorized, generation_failed, not_minted

**COMMITS:**

-   `[FEAT] Backend certificati blockchain post-mint + payment breakdown` (1a3eab7)
-   `[FEAT] Frontend + i18n certificati blockchain post-mint completato` (d3aa281)

---

### �💰 Payment Distributions - Dual Source Statistics ✅ **COMPLETATO 2025-10-16**

**CONTEXT:** `payment_distributions` già popolata da 2 fonti:

-   `source_type = 'reservation'` → prenotazioni (forecast revenue)
-   `source_type = 'mint'` → acquisti reali blockchain-certified

**PROBLEMA:** Statistiche attuali NON distinguono tra forecast (reservation) e revenue reale (mint).

**TASK: Implementare Analytics Dual Source**

-   [x] **FIX CRITICO**: `calculateMintDistributions()` scriveva `distribution_status = PENDING` invece di `CONFIRMED` ✅
    -   Rationale: Mint = pagamento già completato e certificato blockchain
    -   Commit: `[FIX] PaymentDistributionService - Set CONFIRMED status for mint distributions`
-   [x] **StatisticsService - Separation Logic**:
    -   [x] Method: `getMintStatistics()` → Filter `source_type = 'mint'` + `CONFIRMED`
    -   [x] Method: `getDualSourceComparison()` → Compare forecast (reservations) vs reality (mints)
    -   [x] Refactor: `calculateAllStatistics()` integra mint data con reservation data
    -   [x] Refactor: `buildSummaryKPIs()` include mint KPIs nel summary
-   [x] **PaymentDistribution Model - Scopes**:
    -   [x] Scope: `scopeReservationSource()` → `where('source_type', 'reservation')`
    -   [x] Scope: `scopeMintSource()` → `where('source_type', 'mint')`
    -   [x] Scope: `scopeConfirmed()` → `where('distribution_status', CONFIRMED)`
-   [x] **Dashboard User - Dual Source Views** (`/dashboard/statistics`):
    -   [x] Tab navigation con 3 tabs:
        -   Tab 1: "Mint (Revenue Reale)" - mints completati, revenue EUR, by_collection, by_user_type
        -   Tab 2: "Prenotazioni (Forecast)" - reservations attive, forecast EUR, strong/weak breakdown
        -   Tab 3: "Confronto" - conversion rate, forecast vs reality, delta EUR e %, comparison by_collection
    -   [x] Force Refresh button per bypassare cache (30min TTL)
    -   [x] Tab persistence con localStorage
    -   [x] Brand styling Oro Fiorentino (#D4A574)
-   [x] **Testing**:
    -   [x] Backend testato: User 1 (5 reservations, 4 mints, 80% conversion)
    -   [x] User scoping verified: User 3 (2 reservations, 3 mints, 150% conversion)
    -   [x] Cache bypass: Force refresh funzionante
    -   [x] Frontend: Tutti e 3 i tabs renderizzano correttamente
-   [x] **i18n Compliance**:
    -   [x] 30 chiavi tradotte (IT + EN): mints_tab, reservations_tab, comparison_tab, conversion_rate, delta_eur, etc.
    -   [x] Zero hardcoded text (RULE 1 satisfied)

**FILES MODIFIED:**

-   `app/Models/PaymentDistribution.php` (+3 query scopes)
-   `app/Services/StatisticsService.php` (+2 methods getMintStatistics, getDualSourceComparison, ~175 lines)
-   `resources/views/dashboard/statistics/statistics_blade_view.blade.php` (tabs UI, force refresh, ~350 lines JS)
-   `resources/views/dashboard/statistics/partials/mints-statistics.blade.php` (new)
-   `resources/views/dashboard/statistics/partials/reservations-statistics.blade.php` (refactored)
-   `resources/views/dashboard/statistics/partials/comparison-statistics.blade.php` (new)
-   `resources/lang/{it,en}/statistics.php` (+30 keys)

**COMMITS:**

-   `f370150` - Initial mint statistics implementation (~650 lines)
-   `7506f01` - Force refresh button & cache fix
-   `4d44439` - Debug cleanup & verification

**RISULTATO:** Dashboard dual-source 100% funzionante, user-scoped, GDPR compliant, enterprise-grade! 🎉

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

**Completati**: 43 task ✅  
**Priorità Alta**: 0 task - **FASE COMPLETATA** 🎉
**Priorità Media**: 19 task (20 completati ✅, 0 da fare) - **DUAL SOURCE STATISTICS COMPLETATO** 🎉
**Priorità Bassa**: 7 task  
**Blockchain Avanzato**: 12 task

**Totale**: 43/59 task completati (72.9%)

---

## 🎯 NEXT IMMEDIATE STEPS

1. ✅ ~~Fix EGI Card co-creatore logic~~ **COMPLETATO**
2. ✅ ~~Aggiungere utility/traits a vista mint~~ **COMPLETATO**
3. ✅ ~~Dual Source Statistics Dashboard~~ **COMPLETATO 2025-10-16**
4. ✅ ~~CoA minting + vista breakdown pagamenti in checkout mint~~ **COMPLETATO 2025-10-16**
5. ⏳ **PROSSIMO**: Sistema Asta (rinominare Prenotazione → Asta, aggiungere campi obbligatori)
6. ⏳ Royalty Monitor Dashboard (vista breakdown pagamenti per Creator/Owner)
7. ⏳ Controllare permessi PA (non deve creare EGI)
8. ⏳ Weak → Strong authentication per messa in vendita
9. ⏳ Implementare auto-mint EGI
10. ⏳ Controllare funzione ricerca

**Estimated time to MVP completion**: ~50 ore sviluppo + 15 ore testing (ridotto dopo completamento statistics)

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

debug dual mode smart contract / ASA in egis.show

implementare modifiche da prenotaizone ad asta nel processo di "offerta", si carica la voce 1 prenotaiozne al posto di un'offerta, si apre l aodal edelle prenotazioni a,drebbe rivsta come Offerta Asta, insomma rivedere tutta qusta parte

test cn smart contract, a copilot far fare unit test 
