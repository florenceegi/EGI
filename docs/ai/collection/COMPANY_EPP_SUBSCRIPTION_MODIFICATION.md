# Modifica EPP vs Subscription per Utenti Company

**Data:** 2025-12-05  
**Autore:** AI Partner OS3.0  
**Versione:** 1.0.0  
**Status:** Analisi Preliminare

---

## 📋 Richiesta

> Gli utenti con `usertype === "company"` nelle loro collection:
>
> -   **NON devono avere obbligo di EPP** (Environmental Protection Project)
> -   **DEVONO avere obbligo di abbonamento** (subscription in Egili)
> -   **POSSONO comunque fare una donazione EPP** opzionale, scegliendo una percentuale a loro piacimento

---

## 🔍 Analisi del Sistema Attuale

### 1. Flusso Attuale Collection → EPP

```
┌─────────────────────────────────────────────────────────────────────┐
│                     FLUSSO ATTUALE (TUTTI GLI UTENTI)               │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  CREAZIONE COLLECTION                                               │
│  └── CollectionService::createDefaultCollection()                   │
│      └── epp_id = config('app.epp_id') [default: 1]                │
│                                                                     │
│  VALIDAZIONE PUBBLICAZIONE                                          │
│  └── Collection::canBePublished()                                   │
│      └── Controlla wallet proposals pending                         │
│                                                                     │
│  EPP OBBLIGATORIO?                                                  │
│  └── CollectionsController::updateEppProject()                      │
│      └── 'epp_project_id' => 'required|exists:epp_projects,id'     │
│                                                                     │
│  DIRITTI COLLECTION (per vendita)                                   │
│  └── CollectionSubscriptionService::collectionHasRights()           │
│      ├── IF epp_project_id !== null → TRUE (EPP attivo)            │
│      └── ELSE → check hasActiveSubscription() (abbonamento)         │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

### 2. File Chiave Coinvolti

| File                                                                     | Responsabilità                       |
| ------------------------------------------------------------------------ | ------------------------------------ |
| `app/Models/Collection.php`                                              | Model con relazione `eppProject()`   |
| `app/Services/CollectionService.php`                                     | Creazione collection con EPP default |
| `app/Services/CollectionSubscriptionService.php`                         | Logica `collectionHasRights()`       |
| `app/Http/Controllers/CollectionsController.php`                         | `updateEppProject()` con required    |
| `app/Http/Controllers/CollectionCrudController.php`                      | Update con EPP default               |
| `resources/views/components/create-collection-modal.blade.php`           | UI creazione                         |
| `resources/views/egis/partials/sidebar/price-purchase-section.blade.php` | Verifica diritti vendita             |
| `resources/lang/*/collection.php`                                        | Traduzioni (`need_to_associate_epp`) |

### 3. Database Schema Rilevante

```sql
-- collections table
epp_project_id BIGINT UNSIGNED NULL  -- FK a epp_projects

-- epp_projects table
id, name, epp_user_id, project_type, description, ...

-- ai_credits_transactions (per subscription tracking)
source_type = 'collection_subscription'
source_model = 'App\Models\Collection'
```

---

## 🎯 Modifiche Richieste

### Nuovo Flusso per Company

```
┌─────────────────────────────────────────────────────────────────────┐
│                     NUOVO FLUSSO (USER_TYPE = COMPANY)              │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  CREAZIONE COLLECTION                                               │
│  └── IF creator->usertype === 'company'                            │
│      └── epp_project_id = NULL (nessun EPP obbligatorio)           │
│      └── RICHIEDE subscription attiva per pubblicare/vendere        │
│                                                                     │
│  EPP OPZIONALE (Donazione Volontaria)                              │
│  └── Company può scegliere:                                         │
│      ├── Nessun EPP (default)                                       │
│      ├── EPP con percentuale custom (0.1% - 100%)                  │
│      └── UI dedicata "Dona a un progetto ambientale"               │
│                                                                     │
│  DIRITTI COLLECTION (per vendita)                                   │
│  └── IF creator->usertype === 'company'                            │
│      └── SOLO subscription richiesta (EPP irrilevante)             │
│  └── ELSE (altri usertype)                                          │
│      └── EPP obbligatorio OPPURE subscription                       │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

---

## 📝 TODO List Implementazione

### Fase 1: Database & Model

-   [ ] **1.1** Aggiungere campo `epp_donation_percentage` alla tabella `collections`
    -   Tipo: `DECIMAL(5,2) NULL DEFAULT NULL`
    -   Commento: "Percentuale donazione EPP volontaria (solo company)"
-   [ ] **1.2** Aggiungere campo `is_epp_voluntary` alla tabella `collections`
    -   Tipo: `BOOLEAN DEFAULT FALSE`
    -   Commento: "Flag per indicare se EPP è volontario (company) vs obbligatorio"
-   [ ] **1.3** Aggiornare `Collection.php` model
    -   Aggiungere ai `$fillable`: `epp_donation_percentage`, `is_epp_voluntary`
    -   Aggiungere casts: `'epp_donation_percentage' => 'decimal:2'`

### Fase 2: Business Logic

-   [ ] **2.1** Modificare `CollectionService::createDefaultCollection()`
    -   Controllare `$user->usertype`
    -   Se `company`: `epp_project_id = null`, `is_epp_voluntary = true`
-   [ ] **2.2** Modificare `CollectionSubscriptionService::collectionHasRights()`
    -   Aggiungere logica per company:
    ```php
    if ($collection->creator->usertype === 'company') {
        // Company: SOLO subscription required, EPP opzionale
        return $this->hasActiveSubscription($collection);
    }
    // Altri: EPP OR subscription
    return $collection->epp_project_id !== null || $this->hasActiveSubscription($collection);
    ```
-   [ ] **2.3** Modificare `CollectionsController::updateEppProject()`
    -   Rendere `epp_project_id` non obbligatorio per company
    -   Accettare `epp_donation_percentage` opzionale
-   [ ] **2.4** Creare nuovo metodo `updateEppDonation()` per company
    -   Validazione percentuale (0.1% - 100% o null)
    -   Aggiornamento EPP volontario

### Fase 3: UI/UX

-   [ ] **3.1** Modificare `create-collection-modal.blade.php`
    -   Per company: NON mostrare selezione EPP obbligatoria
    -   Aggiungere sezione "Donazione Ambientale (Opzionale)"
-   [ ] **3.2** Creare componente `company-epp-donation-card.blade.php`
    -   Slider/input per percentuale donazione
    -   Selezione progetto EPP (opzionale)
    -   Info su come funziona la donazione
-   [ ] **3.3** Modificare `collections/show.blade.php`
    -   Per company con donazione: mostrare badge "Sostiene [EPP Name]"
    -   Per company senza donazione: nessun riferimento EPP
-   [ ] **3.4** Aggiornare messaggi validazione
    -   Company: "Attiva un abbonamento per pubblicare"
    -   Altri: "Seleziona un progetto EPP oppure attiva un abbonamento"

### Fase 4: Traduzioni

-   [ ] **4.1** Aggiungere chiavi traduzioni (IT, EN, ES, DE, FR, PT):
    ```php
    'company_subscription_required' => 'Abbonamento richiesto per pubblicare',
    'voluntary_epp_donation' => 'Donazione Ambientale (Opzionale)',
    'epp_donation_percentage_label' => 'Percentuale da donare',
    'no_epp_donation' => 'Nessuna donazione',
    'company_supports_epp' => 'Questa azienda sostiene :epp_name',
    ```

### Fase 5: Payment Distribution

-   [ ] **5.1** Modificare `PaymentDistributionService`
    -   Per company con donazione volontaria: usare `epp_donation_percentage`
    -   Per company senza donazione: 0% a EPP
-   [ ] **5.2** Aggiornare calcoli distribuzione
    -   Verificare che la logica esistente supporti percentuali custom

### Fase 6: Testing

-   [ ] **6.1** Unit Test: `CollectionSubscriptionServiceTest`
    -   Test `collectionHasRights()` per company vs altri
-   [ ] **6.2** Unit Test: `CollectionServiceTest`
    -   Test creazione collection per company (no EPP default)
-   [ ] **6.3** Feature Test: Flow completo company
    -   Registrazione → Creazione Collection → Subscription → Publish

### Fase 7: Migration & Deploy

-   [ ] **7.1** Creare migration per nuovi campi
-   [ ] **7.2** Aggiornare collections esistenti di company users
-   [ ] **7.3** Documentare breaking changes per API

---

## ⚠️ Considerazioni Importanti

### Retrocompatibilità

-   Le collection esistenti di company users che hanno già EPP devono continuare a funzionare
-   Il flag `is_epp_voluntary` distingue donazioni volontarie da obblighi storici

### Business Rules

1. **Company SENZA subscription**: non può pubblicare/vendere
2. **Company CON subscription, SENZA EPP**: può pubblicare/vendere (100% va ai partecipanti standard)
3. **Company CON subscription, CON EPP volontario**: può pubblicare/vendere (X% va a EPP per scelta)

### UI Clarity

-   Rendere chiaro che per company EPP è una SCELTA ETICA, non un obbligo
-   Enfatizzare il valore marketing: "La tua azienda sostiene l'ambiente"

---

## 📊 Stima Effort

| Fase                   | Ore Stimate | Priorità |
| ---------------------- | ----------- | -------- |
| Fase 1: Database       | 2h          | Alta     |
| Fase 2: Business Logic | 4h          | Alta     |
| Fase 3: UI/UX          | 6h          | Media    |
| Fase 4: Traduzioni     | 2h          | Media    |
| Fase 5: Payment        | 3h          | Alta     |
| Fase 6: Testing        | 4h          | Alta     |
| Fase 7: Deploy         | 1h          | Alta     |
| **TOTALE**             | **~22h**    | -        |

---

## ✅ Approvazione

-   [ ] Analisi approvata
-   [ ] TODO confermata
-   [ ] Pronto per implementazione

---

_Documento generato automaticamente. Revisione umana richiesta prima dell'implementazione._
