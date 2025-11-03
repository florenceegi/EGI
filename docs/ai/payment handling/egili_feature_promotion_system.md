# 🪙 **FlorenceEGI – Sistema di Gestione Pagamenti, Feature e Promozioni**
### Versione OS3.2 — "Egili Economy Engine"
**Autore:** Fabio Cherici  
**Contributo tecnico:** Padmin D. Curtis (AI Partner OS3)  
**Ultima revisione:** Novembre 2025

---

## 🗭 EXECUTIVE SUMMARY

Il sistema di pagamento e consumo in FlorenceEGI è basato su **Egili**, l’unità energetica interna che misura e remunera l’interazione virtuosa con la piattaforma.  
Ogni utente possiede un **saldo Egili**, utilizzabile per:
- acquistare **feature premium** (descrizioni AI, upload EGI Living, visibilità, ecc.);
- attivare **promozioni temporanee** (es. Hyper Mode o visibilità in home);
- ricaricare crediti o convertire feature inutilizzate.

L’intero ciclo è tracciato da **ULM / UEM / AuditTrail**, garantendo compliance GDPR e trasparenza totale.

FlorenceEGI non nasconde le feature — **le mostra sempre** — e trasforma ogni limite in un’opportunità d’acquisto.  
Ogni mancanza di credito genera una **modale di invito all’azione**, non un blocco.  
Questo approccio trasforma la UX in un **ecosistema economico ed esperienziale** coerente con la filosofia del Rinascimento Digitale.

---

## ⚙️ ARCHITETTURA LOGICA

### 1. Unita monetaria: Egili
- Bilancio utente tracciato in `user_wallets` o tabella dedicata.  
- Transazioni registrate in `ultra_logs` (ULM) e `transactions`.  
- Conversione € ↔ Egili controllata da `ExchangeRateService`.  
- 🔸 **Promo Egili:** possibilità di moltiplicatori temporanei (es. +20% bonus).

### 2. Feature
- Ogni funzionalità a pagamento è definita in `config/feature_pricelist.php`.
- Campi standard:
  ```php
  [
      'label' => 'Descrizione AI',
      'unit_cost_egili' => 10,
      'description' => 'Genera descrizione automatica opera',
      'modifications_included' => 10,
      'is_visible' => true,
  ]
  ```
- Tutte le feature sono **visibili a tutti**; la disponibilità dipende dal saldo.

### 3. Credito Feature
- Gestito da tabella `user_feature_credits`:
  ```sql
  id, user_id, feature_code, available_units,
  used_units, total_purchased, last_purchase_at
  ```
- Gestione logica centralizzata in `FeatureCreditService`.

### 4. Promozioni
- Ogni promo è un **modificatore temporale** del prezzo Egili o del comportamento di una feature.  
- Struttura `feature_promotions`:
  ```sql
  feature_code, name, discount_percent,
  start_at, end_at, is_global
  ```
- 🔸 **Possibilità di promo multiple** con priorità per percentuale più alta.  
- 🔸 **Promo globali** applicabili a tutte le feature.

### 5. Pricing dinamico
- `FeaturePricingService` calcola sempre il costo effettivo:
  ```php
  totalCost = units × baseCost × (1 - discount%)
  ```
- Tutti i service (acquisto, ricarica, consumo) usano questo calcolo, assicurando coerenza.

### 6. Feature speciali
- **Visibilità Home Page / Prominence**
  - Attiva flag `featured_until` su EGI.  
  - Durata = tempo acquistato.  
  - Costo definito in feature list.  
- **Hyper Mode temporanea**
  - Campi aggiunti in `egis`: `hyper_until`, `hyper_by`.  
  - Attivabile dal creator per periodo definito.  
  - Modifica visibilità e ranking.

### 7. Flusso UX unificato
1. Utente sceglie feature →  
2. JS calcola costo (via API FeaturePricingService) →  
3. Se saldo Egili ≥ costo → transazione ok;  
 se no → **modale di ricarica Egili** → pagamento → aggiornamento saldo.  
4. ULM log “feature purchased”, “feature consumed”.  
5. AuditTrail registra ogni transazione.

### 8. Conversione inversa (facoltativa)
- Conversione feature → Egili (80% valore).  
- Motivazione: stimolare riuso, ridurre immobilizzazioni.  
- 🔸 Vincolo: max 1 conversione al mese per feature.

### 9. Logging & Compliance
- Tutte le operazioni passano da:
  - **ULM** → logging di successo / errore.  
  - **UEM** → gestione errori unificata.  
  - **AuditTrail** → tracciamento GDPR.  
- 🔸 Nuove categorie:
  - `FEATURE_PURCHASED`, `FEATURE_USED`, `FEATURE_PROMO_APPLIED`.

---

## 📊 BULLET POINT SINTETICO

- ✅ Un’unica valuta: Egili (+ promo temporanee).  
- ✅ Ogni feature ha costo base e promo dinamiche.  
- ✅ Le feature sono sempre visibili e acquistabili.  
- ✅ Le promo modificano prezzo o durata in tempo reale.  
- ✅ Hyper Mode e Featured EGI gestite come feature temporanee.  
- ✅ Conversione feature ↔ Egili permette flessibilità economica.  
- ✅ ULM/UEM integrati, GDPR compliant.  
- ✅ UX guidata da modali, non da blocchi.  
- ✅ Tutto configurabile via JSON o pannello admin.  
- ✅ Economia interna pronta per metriche AI (Egili usage → profilo utente).

---

## 🔧 ELENCO OPERATIVO – IMPLEMENTAZIONE COMPLETA

### **A. Database & Config**
1. Migration `create_user_feature_credits_table.php`  
2. Migration `create_feature_promotions_table.php`  
3. Aggiunta campi in `egis`: `featured_until`, `hyper_until`, `hyper_by`  
4. File `config/feature_pricelist.php` con tutti i valori base  
5. File `config/feature_promotions.php` (opzionale per MVP senza DB)

### **B. Services**
1. `FeatureCreditService` – gestione acquisto, consumo, conversione.  
2. `FeaturePricingService` – calcolo prezzo effettivo + promo attive.  
3. `EgiliTransactionService` (estensione) – integrazione con ULM / wallet.  
4. 🔸 `FeaturePromotionService` (futuro) – pianificazione e gestione promozioni con notifiche cron.

### **C. Frontend / UX**
1. Componente `<x-feature-card>` → UI per feature visibile a tutti.  
2. Componente `<x-feature-modal>` → modale ricarica Egili / acquisto.  
3. Script JS di calcolo automatico costo + promo.  
4. Pagina “Le mie Feature” → storico crediti e consumi.  
5. Sezione Promozioni nel Creator Dashboard.  
6. Interfaccia Admin per creare / attivare promo.

### **D. Backend / Security**
1. Rotte API:
   - `/api/feature/check-credit`  
   - `/api/feature/purchase`  
   - `/api/egili/recharge`  
2. Controller → `FeatureController`, `EgiliController`.  
3. Middleware ULM/UEM integrati per ogni transazione.  
4. Eventi broadcast (`FeaturePurchased`, `EgiliRecharged`).

### **E. Compliance / Audit**
1. Estendere `GdprActivityCategory.php` con:
   - `FEATURE_PURCHASED`, `FEATURE_USED`, `FEATURE_PROMO_APPLIED`.  
2. Audit automatica in ogni metodo di service.  
3. Verifica end-to-end ULM → AuditTrail → Storage.

### **F. Testing / Validazione**
1. Unit tests per FeatureCreditService e PricingService.  
2. Feature tests → acquisto feature + promo attiva.  
3. UI tests → modale acquisto / ricarica.  
4. Perf tests → calcolo costo < 50 ms.  
5. Security tests → nessuna race condition nelle transazioni.

### **G. Extra Future**
- 🔸 Sistema di **bundles di feature** (es. “Creator Pack”) → sconti aggregati.  
- 🔸 Programma “Egili Rewards” → bonus per uso frequente.  
- 🔸 Metriche AI su uso feature → personalizzazione promo futura.

---

## 🧩 LACUNE COLMATE (OS3)

| Aspetto | Stato | Intervento |
|----------|--------|------------|
| Assenza di promo globali | 🔸 colmata | aggiunto campo `is_global` e service di lookup unificato |
| Mancanza di metriche Egili | 🔸 colmata | introdotto `EgiliTransactionService` per analitica |
| Assenza di conversione feature ↔ Egili | 🔸 colmata | inserito metodo `convertToEgili()` con limiti mensili |
| Hyper Mode non temporizzata | 🔸 colmata | aggiunti `hyper_until`, `hyper_by` su EGI |
| Logging promo | 🔸 colmata | creato Audit category `FEATURE_PROMO_APPLIED` |
| Mancanza pricing dinamico | 🔸 colmata | introdotto `FeaturePricingService` centrale |

---

## 🦦 CONCLUSIONE

Questo sistema trasforma la monetica di FlorenceEGI in una **vera economia etica e dinamica**:

> “Ogni azione ha un valore.  
> Ogni valore è tracciato.  
> Ogni tracciamento costruisce fiducia.”

Egili, feature e promozioni diventano **strumenti di partecipazione**, non di consumo.  
Con questo schema, FlorenceEGI può scalare l’intera economia interna senza perdere **chiarezza, libertà e trasparenza** — i tre principi fondanti del Rinascimento Digitale.

