# Debiti Tecnici e Note di Sviluppo

Questo documento raccoglie note su debiti tecnici, inconsistenze e aree di miglioramento individuate durante lo sviluppo.

## 1. Localizzazione e Traduzioni

### Inconsistenza Files di Traduzione (`resources/lang`)

- **Problema**: Il file `resources/lang/it/profile.php` è significativamente più grande rispetto alle versioni nelle altre lingue (EN, DE, ES, FR, PT).
- **Dettaglio**: Molte stringhe e chiavi presenti in Italiano non sono state tradotte o riportate negli altri file di lingua, causando potenziali fallback non corretti o chiavi mancanti per gli utenti internazionali.
- **Azione Richiesta**: Allineare tutti i file di traduzione assicurandosi che ogni chiave presente in `it/profile.php` abbia un corrispettivo (anche se in inglese/fallback) negli altri file.

---

## 2. Notification System (Push Notifications)

### 🔴 Push Notifications (Non-Critical)

**Status**: Not Implemented  
**Priority**: Low (deferred)  
**Created**: 2026-02-04

### Description

Le notifiche push (browser/mobile) non sono attualmente implementate nel sistema V3. Il sistema attuale supporta solo notifiche in-app (database) visualizzate tramite il Notification Center.

### Impact

- Gli utenti devono accedere manualmente al Notification Center per vedere le nuove notifiche
- Nessun alert real-time quando l'utente non è attivamente sulla piattaforma
- Possibile ritardo nella risposta a notifiche urgenti (es. vendite, spedizioni)

### Technical Context

Il sistema V3 è già predisposto per l'integrazione di canali aggiuntivi:

- `CustomDatabaseChannel` è modulare e separato dalla logica di business
- Le classi Notification supportano il metodo `via()` per specificare canali multipli
- Laravel supporta nativamente broadcast notifications via Pusher/Echo

### Proposed Solution (Future)

1. Implementare `BroadcastChannel` per notifiche real-time via WebSocket
2. Integrare servizio push (Firebase Cloud Messaging per mobile, Web Push API per browser)
3. Aggiungere preferenze utente per gestire canali di notifica
4. Implementare queue worker dedicato per push notifications

### Workaround Attuale

Gli utenti ricevono notifiche via:

- Email (per eventi critici come vendite/spedizioni)
- In-app notification center (richiede refresh manuale o polling)
- Badge counter nel menu principale (aggiornato via Livewire)

### Notes

Questo debito tecnico è stato documentato su richiesta esplicita dell'utente per dare priorità allo sviluppo di nuove feature. Non è considerato bloccante per il rilascio in produzione.

---

## 3. Ultra Translation Manager - Dynamic Parameter Caching Issue

### 🔴 UTM Dynamic Parameter Caching

**Status**: Active Issue (Workaround Implemented)  
**Priority**: Medium (affects user experience)  
**Created**: 2026-02-05  
**Component**: Ultra Translation Manager (UTM)

#### Description

Ultra Translation Manager caches translated strings aggressively to improve performance. However, when translations use dynamic parameters (`__('key', ['param' => $value])`), UTM may cache the translation with the **first user's data** and serve the same cached string to ALL subsequent users.

#### Real-World Example

```php
// Translation file:
'greeting' => 'Ciao :name! Sono qui per aiutarti.'

// Blade view:
{{ __('ai_sidebar.greeting', ['name' => $user->name]) }}

// BUG: First user "Mario" logs in → cached as "Ciao Mario! Sono qui..."
// Then user "Luigi" logs in → sees "Ciao Mario! Sono qui..." (WRONG!)
```

#### Impact

- **Data Leakage**: Users see other users' names/data in translated strings
- **Personalization Broken**: User-specific greetings show wrong names
- **Cache Invalidation Required**: Need frequent cache clears to fix
- **Affects All Dynamic Translations**: Not limited to specific components

#### Root Cause

UTM's caching strategy doesn't account for dynamic parameter variations. The cache key is based on:

- Translation key (`ai_sidebar.greeting`)
- Locale (`it`, `en`, etc.)

But NOT on parameter values (`name => Mario` vs `name => Luigi`).

#### Current Workaround: Atomic Translations

Split translations into static parts + Blade variables:

```php
// Translation file (ATOMIC):
'greeting' => 'Ciao',
'greeting_suffix' => '! Sono qui per aiutarti.',

// Blade view:
{{ __('ai_sidebar.greeting') }} {{ $user->name }}{{ __('ai_sidebar.greeting_suffix') }}
// Result: "Ciao" + MARIO + "! Sono qui..." (always fresh)
```

**Pros**:

- ✅ Works reliably with UTM caching
- ✅ No cache invalidation needed
- ✅ Performance maintained

**Cons**:

- ❌ More verbose translation keys
- ❌ Harder to maintain (split sentences)
- ❌ Less natural for translators

#### Proper Solution (TODO)

Investigate and implement ONE of these approaches:

**Option 1: UTM Cache Key Enhancement**
Modify UTM to include parameter values in cache key:

```php
// Current cache key:
$cacheKey = "utm.{$locale}.{$key}";

// Proposed cache key:
$cacheKey = "utm.{$locale}.{$key}." . md5(json_encode($parameters));
```

**Pros**: Minimal code changes, preserves current translation syntax  
**Cons**: May increase cache size, requires UTM core modification

**Option 2: Disable Caching for Dynamic Translations**
Add flag to bypass cache for specific translations:

```php
__('ai_sidebar.greeting', ['name' => $user->name], false); // No cache
```

**Pros**: Granular control, backward compatible  
**Cons**: Performance impact on frequently used translations

**Option 3: Per-User Translation Cache**
Use Laravel's tagged cache with user ID:

```php
Cache::tags(["translations", "user:{$userId}"])->remember(...)
```

**Pros**: Correct caching per user, fast lookups  
**Cons**: Significant cache memory usage in multi-tenant environment

#### Investigation Tasks

- [ ] Review UTM source code to understand caching implementation
- [ ] Benchmark performance impact of proposed solutions
- [ ] Test cache invalidation strategies
- [ ] Discuss with UTM maintainers (if external package)
- [ ] Create POC for preferred solution

#### Affected Areas

- AI Sidebar (fixed with atomic translations)
- User greetings/personalization across platform
- Dynamic notification messages
- Any user-specific translated content

#### Notes

This issue was discovered during AI Sidebar implementation (2026-02-05) when user "Fabio" saw greeting for previous user "CamiciaSmart". Atomic translation workaround successfully deployed across all 6 languages.

**CRITICAL**: Always use atomic translations for user-specific data until proper UTM fix is implemented.

---

## 4. Sistema Gestione Reclami e Segnalazioni (DSA Compliance)

### 🟡 Notice-and-Action e Complaint Management — IMPLEMENTAZIONE TECNICA PENDENTE

**Status**: Partially Implemented (Legal/ToS completato, Technical pending)
**Priority**: HIGH (implementazione tecnica obbligatoria)
**Created**: 2026-02-24
**Updated**: 2026-02-23
**Component**: Compliance DSA (Reg. UE 2022/2065)

#### Parte Legale/ToS — COMPLETATA (2026-02-23)

Le clausole DSA sono state inserite in tutti i 6 ToS v3.0.0:

- **creator.php** Art. 12.8 — Segnalazione Contenuti e Reclami (DSA)
- **collector.php** Art. 10.8 — Segnalazione Contenuti e Reclami (DSA)
- **patron.php** Art. 7.6 — Segnalazione Contenuti e Reclami (DSA)
- **trader_pro.php** Art. 7.6 — Segnalazione Contenuti e Reclami (DSA)
- **epp.php** Art. 9.4 — Controversie (include mediazione D.Lgs. 28/2010)
- **company.php** — Non applicabile direttamente (B2B con clausole bilaterali)

Canale di segnalazione provvisorio: `legal@florenceegi.com` + funzionalità sulla Piattaforma (da implementare).

#### Parte Tecnica — DA IMPLEMENTARE

#### Contesto Normativo

Il Digital Services Act (Reg. UE 2022/2065), pienamente applicabile dal 17 febbraio 2024, richiede alle piattaforme online di implementare:

- **Art. 16 — Meccanismo di segnalazione e azione (Notice-and-Action)**: Gli utenti devono poter segnalare contenuti ritenuti illeciti tramite un meccanismo facilmente accessibile e di facile utilizzo.
- **Art. 17 — Motivazione delle decisioni**: La piattaforma deve motivare ogni decisione di rimozione, sospensione o limitazione dei contenuti.
- **Art. 20 — Meccanismo interno di gestione dei reclami**: Gli utenti devono poter presentare reclami contro le decisioni di moderazione. Il meccanismo deve essere gratuito, facilmente accessibile e gestito da personale qualificato.
- **Art. 21 — Risoluzione extragiudiziale**: Gli utenti devono poter ricorrere a organismi certificati di risoluzione extragiudiziale.

#### Stato Attuale

La piattaforma FlorenceEGI **non dispone** attualmente di:

1. Un form/pagina per segnalare contenuti illeciti (opere contraffatte, violazioni IP, contenuti offensivi)
2. Un sistema di tracking delle segnalazioni con stati e tempistiche
3. Un meccanismo di notifica al segnalante e al segnalato
4. Un processo di contraddittorio per il Creator (diritto di replica)
5. Un sistema di reclamo interno contro le decisioni di moderazione
6. Documentazione delle decisioni con motivazioni

#### Componenti da Implementare

##### A) Modello Dati

```
complaints (tabella)
├── id
├── type: enum ['content_report', 'ip_violation', 'fraud', 'moderation_appeal', 'general']
├── status: enum ['received', 'under_review', 'action_taken', 'dismissed', 'appealed', 'resolved']
├── reporter_user_id (FK users, nullable per segnalazioni anonime)
├── reported_content_type: enum ['egi', 'collection', 'user_profile', 'comment']
├── reported_content_id
├── reported_user_id (FK users)
├── description (text)
├── evidence_urls (JSON, opzionale)
├── decision (text, motivazione della decisione)
├── decision_by (FK users, moderatore)
├── decided_at (timestamp)
├── appeal_text (text, se reclamo contro decisione)
├── appeal_decided_at (timestamp)
├── created_at, updated_at
```

##### B) Backend

- **Controller**: `ComplaintController` con endpoints per:
    - `store()` — creazione segnalazione (autenticato o anonimo)
    - `index()` — lista segnalazioni utente
    - `show()` — dettaglio segnalazione con stato
    - `appeal()` — reclamo contro decisione di moderazione
- **Admin Controller**: `Admin\ComplaintManagementController` per:
    - Lista tutte le segnalazioni con filtri per tipo/stato
    - Presa in carico
    - Decisione con motivazione obbligatoria
    - Statistiche e report

##### C) Frontend

- **Form segnalazione**: Accessibile da ogni pagina EGI/Collection/Profilo (icona "flag")
- **Dashboard reclami utente**: Sezione in area personale per seguire lo stato
- **Pagina informativa**: Spiegazione della procedura di segnalazione e dei diritti dell'utente
- **Admin panel**: Gestione centralizzata delle segnalazioni

##### D) Notifiche

- Email al segnalante: conferma ricezione, aggiornamenti stato, decisione finale
- Email al segnalato: notifica della segnalazione, diritto di replica, decisione finale
- Tempistiche: risposta entro 72h (best practice), decisione entro 15 giorni lavorativi

##### E) Reporting DSA

- Report annuale di trasparenza sulle segnalazioni gestite (Art. 15 DSA)
- Statistiche aggregate: numero segnalazioni, tipologie, tempi medi, esiti

#### Impatto Legale

Senza questo sistema, FlorenceEGI non è conforme al DSA. Il rischio include:

- Sanzioni fino al 6% del fatturato annuo globale (Art. 52 DSA)
- Ordini di conformità dalle autorità nazionali (AGCOM per l'Italia)
- Responsabilità per contenuti illeciti non rimossi tempestivamente

#### Riferimenti Normativi

- Regolamento (UE) 2022/2065 — Digital Services Act, Artt. 14-21
- Delibera AGCOM 353/23/CONS — Linee guida attuative DSA per l'Italia
- Codice del Consumo D.Lgs. 206/2005 — Tutela dei consumatori

#### Priorità di Implementazione

1. **Fase 1 (URGENTE)**: Modello dati + form segnalazione base + admin review
2. **Fase 2**: Notifiche automatiche + tracking stato + dashboard utente
3. **Fase 3**: Report trasparenza + integrazione con sistema moderazione automatica

---

## 5. Verifica Data Residency API AI

### 🟡 Endpoint AI fuori UE

**Status**: Da verificare/mitigare
**Priority**: Medium
**Created**: 2026-02-24

#### Situazione

- **Infrastruttura FlorenceEGI**: Tutta in EU (AWS eu-north-1 Stoccolma)
    - RDS PostgreSQL: eu-north-1
    - EC2: eu-north-1
    - S3 + CloudFront: eu-south-1 (Milano) / eu-north-1
    - RAG data: nel DB PostgreSQL (eu-north-1)

- **API AI esterne**: Endpoint US
    - Anthropic Claude: `https://api.anthropic.com` (US)
    - OpenAI Embeddings: `https://api.openai.com/v1` (US)

#### Mitigazioni Attuali

- Il commento in `.env.example` specifica: "Processes ONLY public metadata"
- NATAN analizza **opere d'arte** (titoli, descrizioni, traits, categorie) — non profila utenti
- I dati inviati sono metadati pubblici delle opere, non dati personali
- `config/services.php` conferma: "DPA: Anthropic has Data Processing Agreement with EU customers"
- Le API non ricevono: email, indirizzi, dati bancari, documenti d'identità

#### Azioni Raccomandate

1. **Documentare formalmente** nel registro dei trattamenti (Art. 30 GDPR) che le API AI ricevono solo metadati pubblici
2. **Verificare DPA** con Anthropic e OpenAI — confermare SCCs (Standard Contractual Clauses) attive
3. **Valutare** migrazione a Azure OpenAI (EU region disponibile) per embedding se necessario
4. **Aggiornare Privacy Policy** con disclosure dei sub-processori extra-UE e base giuridica del trasferimento

---

## 6. ~~Allegato A e Allegato B dei ToS — Non Ancora Creati~~ COMPLETATO

### ✅ Allegati ToS — COMPLETATO

**Status**: COMPLETATO
**Priority**: ~~HIGH~~ Risolto
**Created**: 2026-02-24
**Completed**: 2026-02-23

#### Descrizione

I due allegati mancanti sono stati creati:

- **Allegato A — Tabella delle Fee Dinamiche** (`resources/legal/terms/versions/current/it/allegato_a_fee.php`)
    - 7 sezioni: Profili Collection, Fee Mint (Contributor/Normal), Fee Rebind (Contributor/Normal), Eccezione Commodity, Fee Dinamiche e Sconti Volume, Riepilogo Comparativo, Note Importanti
    - Dati verificati con: `FeeStructureEnum.php`, `04_Gestione_Pagamenti.md`, `10_Rebind_Logic_Reference.md`

- **Allegato B — Guida al Sistema Egili** (`resources/legal/terms/versions/current/it/allegato_b_egili.php`)
    - 7 sezioni: Definizione e Natura Giuridica, Come Ottenere Egili, Come Utilizzare gli Egili, Condizioni per Tipo di Utente, Regole e Limitazioni, Modifiche al Sistema, Assistenza e Saldo
    - Dati verificati con: `config/ai-credits.php`, `config/egili.php`, ToS Art. 6

Referenziati correttamente in: creator.php Art. 11, collector.php Art. 6, patron.php Art. 3.3, trader_pro.php Art. 3.4.

---

## 7. Compliance ToS v3.0.0 — Gap P0/P1 (COMPLETATO)

### ✅ Correzione 22 Gap di Compliance — COMPLETATO

**Status**: COMPLETATO
**Priority**: ~~P0/P1~~ Risolto
**Created**: 2026-02-24
**Completed**: 2026-02-23
**Commit**: pushato su `develop`, deployato su `art.florenceegi.com`

#### Lavoro Svolto

Analisi incrociata di 12 documenti in `docs/FlorenceEGI/` con i 6 ToS ha identificato 22 gap critici di compliance, tutti risolti:

##### NATAN/AI (P0)

- Acronimo unificato: "Neural Adaptive Technology for Art Navigation" (prima inconsistente tra creator e collector)
- Descrizione corretta: analizza **opere** (titoli, descrizioni, traits) per posizionamento mercato, **NON profila utenti**, **NON prende decisioni autonome**
- EU AI Act disclosure (Reg. 2024/1689): creator.php Art. 9.4, collector.php Art. 7.4

##### Clausole Universali (P0/P1)

Aggiunte a tutti i 6 ToS:

- **Separabilità**: creator 12.5, collector 10.5, patron 7.4, trader_pro 7.4, epp 9.5, company 10.5
- **Forza Maggiore** (Art. 1218 CC): creator 12.6, collector 10.6, patron 7.5, trader_pro 7.5, epp 9.6, company 10.6
- **DSA Segnalazioni**: creator 12.8, collector 10.8, patron 7.6, trader_pro 7.6
- **ODR/ADR**: creator 12.9, collector 10.4, patron 7.7, trader_pro 7.7
- **Limitazione Responsabilita**: creator 12.10, collector 10.9, patron 7.8
- **Cessione Contratto**: creator 12.7, collector 10.7
- **Comunicazioni**: creator 12.11, collector 10.10, patron 7.9, trader_pro 7.8, epp 9.7, company 10.7

##### Altre Correzioni (P1)

- **creator.php** Art. 4.3: Restrizioni IP acquirente (no merchandise, no derivati, no uso commerciale)
- **creator.php** Art. 7.6: Note fiscali (non sostituto d'imposta, soglia P.IVA, SIAE, DAC7)
- **collector.php** Art. 4.4: Restrizioni d'uso dettagliate (cosa puoi/non puoi fare con l'EGI)
- **collector.php** Art. 4.3: Diritti morali aggiornati (L. 633/1941 Art. 20)

##### Allegati

- Allegato A (Fee Dinamiche) e Allegato B (Guida Egili) — vedi item #6

##### Metadata

- `metadata.php` aggiornato: `eu_ai_act => true`, `files_updated`, `review_notes`, `ai_act_review => Completato`

#### Verifica Eseguita

- PHP syntax validation: tutti i 9 file OK (`php -l`)
- Coerenza acronimo NATAN: nessun vecchio acronimo residuo
- Numerazione articoli: nessun salto o duplicazione

---

## 8. Allineamento Codebase alla Riforma Egili (ToS v3.0.0)

### � Codebase Egili — Allineamento ai ToS v3.0.0 (Parzialmente Completato)

**Status**: 🟠 IN CORSO — Block F (UI) + Block D (Legale) COMPLETATI | Block A/B/C/E pending  
**Priority**: HIGH (rilevanza legale — i ToS sono la fonte di verità)  
**Created**: 2026-02-25  
**Last Update**: 2026-02-25 (Blocco F completato — F5-F14 done)  
**Component**: Sistema Egili — config, allegati, frontend

#### Progresso (2026-02-25)

| Block                    | Descrizione                                                                     | Stato       |
| ------------------------ | ------------------------------------------------------------------------------- | ----------- |
| **F** (UI/Frontend)      | F1 modal ✅, F2/F3/F4 menu ✅, F5-F9 ✅, F10/F11 ✅, F12 ✅, F13 N/A ✅, F14 ✅ | ✅ Completo |
| **D** (Legale+Lang)      | Allegato B v3.1.0 ✅, 6 lang files ✅                                           | ✅ Completo |
| **A** (P0 violazioni DB) | A1/A2/A3/A4 pending                                                             | 🔴 Pending  |
| **B** (P1 naming)        | B1-B10 pending                                                                  | 🟠 Pending  |
| **C** (P2 commenti)      | C1-C4 pending                                                                   | 🟡 Pending  |
| **E** (Test)             | pending                                                                         | 🟡 Pending  |

#### Contesto

I ToS v3.0.0 (tutti e 4: `creator.php`, `collector.php`, `patron.php`, `trader_pro.php`) hanno formalizzato la **Riforma Egili**:

> _"Riforma sistema Egili — da utility token a crediti servizio AI prepagati e sistema di premiazione interna. Eliminazione acquisto Egili come asset. Eliminazione pagamento EGI in Egili. Introduzione pacchetti AI in valuta FIAT."_

#### Definizione Giuridica Vigente (fonte: ToS v3.0.0)

Gli **Egili** sono:

- Crediti di servizio interni + unità di premiazione della Piattaforma
- **Non** token, **non** asset acquistabile autonomamente
- **Non** trasferibili, **non** rimborsabili, privi di valore monetario autonomo

**Come si ottengono** (soli 2 modi validi):

1. **Acquisto di pacchetti di servizi AI** in valuta FIAT (EUR) → l'oggetto dell'acquisto è il **pacchetto AI**, non gli Egili come asset
2. **Premiazione per merito** (gratuita) — azioni meritevoli sulla Piattaforma

**Come si usano** (usi consentiti):

- Consumo servizi AI (NATAN)
- Riduzione/azzeramento commissioni di servizio FlorenceEGI
- Sconti e vantaggi nell'ecosistema
- ❌ Vietato come mezzo di pagamento per acquisto/vendita di EGI

#### File da Aggiornare

##### 1. `config/egili.php` — PRIORITÀ ALTA

- La sezione `purchase` e `payment_providers` usa il paradigma "acquisto Egili come asset"
- Va rinominata e riformulata come "pacchetti servizi AI che accreditano Egili"
- La chiave `'purchase'` deve diventare `'ai_packages'` o simile
- Il commento `"Egili Purchase Configuration"` va aggiornato a `"AI Service Packages Configuration"`

##### 2. `config/ai-credits.php` — PRIORITÀ ALTA

- La sezione `purchase_packages` usa ancora la nomenclatura vecchia
- I key `premium_tier_monthly_credits`, `premium_tier_discount_percentage` usano il termine "credits" anziché "Egili" — da uniformare
- `refund_processing_fee_percentage` e `auto_refund_on_job_failure` vanno verificati contro la politica ToS (gli Egili non sono rimborsabili salvo malfunzionamento tecnico)

##### 3. `resources/legal/terms/versions/current/it/allegato_b_egili.php` — PRIORITÀ MEDIA

- Sezione 2.1: La tabella dei pacchetti è concettualmente corretta ma usa il titolo "Acquisto di Pacchetti Servizi AI (FIAT)" — da verificare che il linguaggio non lasci intendere l'acquisto di Egili come asset
- Sezione 1 (Natura Giuridica): Già corretta
- Da aggiornare per allineamento formale con la definizione ToS Creator Art. 6

##### 4. Frontend / Controllers — DA CENSIRE

- Qualsiasi view, component React o controller che usa il termine "acquista Egili" o "compra Egili" va aggiornato in "acquista pacchetto AI"
- Ricerca necessaria: `grep -r "acquista.*egili\|compra.*egili\|buy.*egili" resources/ app/` (case-insensitive)

#### Impatto Legale

⚠️ **CRITICO**: La codebase che mostra "compra Egili" come asset autonomo è in **contrasto diretto con i ToS v3.0.0** e con la classificazione MiCA (Reg. UE 2023/1114). Qualsiasi comunicazione all'utente che presenti gli Egili come asset acquistabile potrebbe configurare una violazione normativa.

#### Fonte di Verità

I **ToS v3.0.0** sono la fonte di verità. Qualsiasi modifica alla codebase deve allinearsi ai ToS, non viceversa.

#### Censimento Completo Codebase (effettuato 2026-02-25)

##### File impattati per categoria:

**CONFIG**: `config/egili.php`, `config/ai-credits.php`, `config/egi_living.php`, `config/icons.php`, `config/error-manager.php`, `config/natan-tutor.php`

**SERVICES**: `app/Services/EgiliService.php`, `app/Services/EgiliPurchaseWorkflowService.php`, `app/Services/EgiliTransactionService.php`, `app/Services/NatanTutorService.php`, `app/Services/Payment/StripeRealPaymentService.php`, `app/Services/Wallet/WalletRedemptionService.php`

**CONTROLLERS**: `app/Http/Controllers/EgiliPurchaseController.php`, `app/Http/Controllers/Admin/EgiliManagementController.php`, `app/Http/Controllers/Superadmin/SuperadminEgiliController.php`

**MODELS**: `app/Models/EgiliTransaction.php`, `app/Models/EgiliMerchantPurchase.php`

**NOTIFICATIONS**: `app/Notifications/EgiliPurchaseConfirmation.php`

**ROUTES**: `routes/web.php` (riga 183: purchase routes, riga 938: admin routes), `routes/console.php` (scheduled jobs), `routes/api.php`

**MIGRATIONS**: `add_egili_to_wallets_table`, `create_egili_transactions_table`, `create_egili_merchant_purchases_table`, `add_payment_by_egili_to_egis_table`, `update_payment_method_enum_on_egi_blockchain_table`

---

#### Piano di Modifiche — Checklist

> **Legenda**: 🔴 P0-CRITICO (violazione ToS) | 🟠 P1-MUST (naming errato) | 🟡 P2-SHOULD (commenti/terminologia)

---

##### BLOCCO F — Interfacce Utente (PRIORITÀ ASSOLUTA) 🔴 P0

> Queste sono le prime da modificare — è ciò che l'utente vede e usa. Finché queste esistono, la violazione è attiva e visibile.

---

**NUOVO CONCETTO MODALE (da implementare)**:
La modale non vende più "Egili come asset" ma vende **Pacchetti Token AI in FIAT**.
Al momento dell'acquisto, gli Egili vengono **accreditati automaticamente** sul saldo con il seguente meccanismo:

> **Rapporto di conversione**: 1.000 Token AI acquistati → 800 Egili accreditati
> **Margine FlorenceEGI**: 20% (trattenuto come margine sulla vendita di token)
> **Formula**: `Egili_accreditati = Token_AI_acquistati × 0.80`

**Logica del sistema**:

1. L'utente paga in FIAT (Stripe/PayPal) per un quantitativo di **Token AI**
2. La piattaforma applica il margine del 20% e accredita gli **Egili** risultanti (80% dei token acquistati)
3. Gli **Egili fungono da contatore interno**: ogni utilizzo di un servizio AI scala il saldo Egili in proporzione ai token AI effettivamente consumati
4. Quando il saldo Egili si esaurisce, i servizi AI non sono più accessibili fino a nuovo acquisto

L'utente vede sempre: "Acquisti X Token AI → ricevi Y Egili da usare".
Il nuovo nome della modale deve essere: **"Acquista Pacchetto AI"** → accredito Egili.

---

**TRIGGER della modale `openEgiliPurchaseModal()` — da aggiornare tutti:**

| File                                                | Riga | Contesto                                        |
| --------------------------------------------------- | ---- | ----------------------------------------------- |
| `layouts/app.blade.php`                             | 208  | Inclusione globale `<x-egili-purchase-modal />` |
| `layouts/platform.blade.php`                        | 218  | Inclusione globale `<x-egili-purchase-modal />` |
| `layouts/collection.blade.php`                      | 52   | Inclusione globale `<x-egili-purchase-modal />` |
| `layouts/guest.blade.php`                           | 223  | Inclusione globale `<x-egili-purchase-modal />` |
| `components/navigation/egili-wallet-card.blade.php` | 30   | Bottone CTA nel wallet card                     |
| `components/feature-purchase-modal.blade.php`       | 174  | Bottone "Acquista Egili" nella modale feature   |
| `components/gold-bar-info.blade.php`                | 296  | Link/bottone nel componente gold-bar            |
| `components/egi/traits-viewer.blade.php`            | 611  | Trigger dal traits viewer                       |
| `collections/show.blade.php`                        | 1050 | Trigger dalla pagina collection                 |
| `collections/show-epp.blade.php`                    | 977  | Trigger dalla collection EPP                    |

---

- [x] **F1** ✅ 2026-02-25 — `egili-purchase-modal.blade.php` **CONVERTITA**: rimossa sezione crypto, rimosso input "quanti Egili", aggiunta selezione pacchetti AI da `config('ai-credits.purchase_packages')`, riepilogo mostra Egili accreditati (ratio 80%), pagamento solo Stripe/PayPal, aggiunta nota legale, script riscritto con `AI_PACKAGES` JSON. Route `egili.purchase.process` mantenuta per backward-compat (rinomina in Block B). Componente mantenuto stesso filename (evita cascata 10 trigger points — decisione OS3 Semplicità).
- [x] **F2** ✅ 2026-02-25 — Menu desktop: aggiornato via lang key `egili.wallet.buy_more` (it: "Pacchetti AI", en: "AI Packages", +4 lingue). Il menu include `<x-navigation.egili-wallet-card />` che usa la chiave.
- [x] **F3** ✅ 2026-02-25 — Menu mobile: stessa risoluzione di F2.
- [x] **F4** ✅ 2026-02-25 — `egili-wallet-card.blade.php`: bottone CTA aggiornato via lang key `egili.wallet.buy_more`.
- [x] **F5** ✅ 2026-02-25 — `purchase-confirmation.blade.php`: aggiunto banner "Pacchetto AI" con nota `egili.purchase.egili_model_note`; aggiornate lang keys in IT/EN: `confirmation.egili_purchased` → 'Egili Accreditati', `confirmation.title` → 'Pacchetto AI Acquistato!', `transaction_types.purchase` → 'Accreditati (Pacchetto AI)', `email.purchase_confirmation_subject` e `email.purchase_success` aggiornati in IT/EN. `transaction_types.purchase` aggiornato anche in DE/ES/FR/PT. Sezione crypto marcata come legacy.
- [x] **F6** ✅ 2026-02-25 — `feature-purchase-modal.blade.php` (riga 174): bottone usa `__('egili.buy_more')` già aggiornato a 'Pacchetti Servizi AI' in D2. Nessuna modifica ulteriore necessaria.
- [x] **F7** ✅ 2026-02-25 — `gold-bar-info.blade.php`: usa `__('gold_bar.buy_egili_hint')` e `__('gold_bar.buy_egili_button')`. Aggiornati IT (`'Acquista Egili'` → `'Acquista Pacchetto AI'`, hint aggiornato) ed EN. Aggiunte chiavi mancanti in DE/ES/FR/PT (P0-9).
- [x] **F8** ✅ 2026-02-25 — `traits-viewer.blade.php`: testi JS aggiornati: `'Acquista Egili per continuare.'` → `'Acquista un Pacchetto AI per ricaricare i tuoi Egili.'`, confirmButtonText `'Acquista Egili'` → `'Acquista Pacchetto AI'`. Contesto: il traits viewer apre la modale quando l'utente non ha Egili sufficienti per usare una Living Feature AI.
- [x] **F9** ✅ 2026-02-25 — `collections/show.blade.php` + `show-epp.blade.php`: aggiornati testi JS: `'Acquista Egili per continuare.'` → `'Acquista un Pacchetto AI per ricaricare i tuoi Egili.'`; confirmButtonText `'Acquista Egili'` → `'Acquista Pacchetto AI'`.
- [x] **F10** ✅ 2026-02-25 — `mint/payment-form.blade.php`: `$showEgiliOption` forzato a `false` (era `?? false`) con commento ToS v3.0.0. Opzione "Paga con Egili" non più visibile. Ref: debiti F10 + A1/A2.
- [x] **F11** ✅ 2026-02-25 — `rebind/checkout.blade.php`: stessa soluzione di F10. `$showEgiliOption` forzato a `false` con commento ToS v3.0.0.
- [x] **F12** ✅ 2026-02-25 — `crud-panel.blade.php`: rimosso toggle UI `payment_by_egili` (era il form di creazione/modifica EGI). Aggiunto `input[type=hidden value=0]` + commento ToS v3.0.0. Rimosso anche l'indicatore di stato `payment_by_egili_status` nella view panel. Il DB rimane invariato (rimozione colonna in Block A, item A1).
- [x] **F13** ✅ 2026-02-25 — Layouts: **N/A** — componente mantenuto stesso filename `egili-purchase-modal.blade.php` per semplicità (OS3). Il contenuto è stato completamente riscritto in-place. Non serve rinominare.
- [x] **F14** ✅ 2026-02-25 — `livello-4-pagamento-egili.blade.php` + `livello-4-flusso-egili.blade.php`: riscritte completamente. Rimosso vecchio paradigma (pay-with-egili per EGI, burn/gift mechanism). Nuovo contenuto: definizione Egili v3.0.0, 2 soli modi di ottenimento, usi consentiti, tipi Gift/Lifetime, conformità MiCA aggiornata.

---

- [x] **A1** ✅ 2026-02-25 **MIGRATA** — `2026_02_25_100000_remove_payment_by_egili_from_egis_table.php` eseguita (695ms). Colonna `payment_by_egili` rimossa dalla tabella `egis`.
- [x] **A2** ✅ 2026-02-25 **MIGRATA** — `2026_02_25_100001_remove_egili_from_payment_method_on_egi_blockchain.php` eseguita (849ms). CHECK constraint PostgreSQL aggiornato: `payment_method` non accetta più `'egili'`.
- [x] **A3** ✅ 2026-02-25 — **DECISIONE FABIO: MANTENERE** la funzionalità ALGO→Egili in `WalletRedemptionService`. Commento aggiornato da WARNING a NOTA. Il servizio resta attivo.
- [x] **A4** ✅ 2026-02-25 — `database/factories/EgiFactory.php`: `'payment_by_egili' => false` commentato con nota ToS v3.0.0 e riferimento rimozione colonna (A1).

---

##### BLOCCO B — Naming errato (vecchio paradigma "acquisto Egili come asset") 🟠 P1

- [x] **B1** ✅ 2026-02-25 — `config/egili.php`: header → `AI Service Packages — Egili credit system`, `'purchase'` → `'ai_packages'`, `payment_providers` → `'ai_package_payment_providers'`, tutti i commenti "acquisto Egili come asset" rimossi. Crypto providers disabilitati con nota ToS v3.0.0.
- [x] **B2** ✅ 2026-02-25 — `config/ai-credits.php`: `purchase_packages` → `ai_service_packages`, aggiunto `egili_credit_ratio => 0.80` (fonte di verità ratio), commenti aggiornati.
- [x] **B3** ✅ 2026-02-25 — `config/egi_living.php` riga 97: rimosso commento errato `1 Egilo = €0.10`. Aggiunto commento che spiega che 500 è il prezzo-egili intentional (non derivato dal tasso). TODO per conferma con Fabio.
- [x] **B4** ✅ 2026-02-25 — `config/icons.php` + 6 caller files: `superadmin-egili-token` → `superadmin-egili-credits` in `ContextMenus.php`, `SuperadminFeaturePricingMenu.php`, `SuperadminEgiliManagementMenu.php`, `AdminPromotionsMenu.php`, `AiFeaturePricingSeeder.php`, `AiFeaturePricingSeederV2Real.php`.
- [x] **B5** ✅ 2026-02-25 — `NatanTutorService.php`: metodo `assistEgiliPurchase()` → `assistAiPackagePurchase()` con docblock aggiornato.
- [x] **B6** ✅ 2026-02-25 — `NatanTutorService.php`: `route('egili.purchase', ...)` → `route('egili.purchase.pricing')` (route corretta del flusso selezione pacchetti AI).
- [x] **B7** ✅ 2026-02-25 — `StripeRealPaymentService.php`: `'Acquisto %s Egili'` → `sprintf('Acquisto Pacchetto AI (%s Egili accreditati)', ...)`.
- [x] **B8** ✅ 2026-02-25 — `EgiliMerchantPurchase.php`: docblock aggiornato — chiarisce che traccia "acquisti di pacchetti AI che accreditano Egili" (non acquisto diretto Egili). Versione → 1.1.0. Rinomina modello/migration rinviata (alta cascata, decisione Fabio).
- [x] **B9** ✅ 2026-02-25 — `EgiliPurchaseConfirmation.php`: docblock aggiornato `"AI Package Purchase Confirmation Email"`, versione → 1.1.0.
- [x] **B10** ✅ 2026-02-25 — `EgiliPurchaseController.php`: verificato — usa esclusivamente `__()` lang keys (`egili.purchase.*`) già aggiornate in Blocco F/D. Nessun testo hardcoded.

---

##### BLOCCO C — Commenti e terminologia nelle migration/factory 🟡 P2

- [x] **C1** ✅ 2026-02-25 — `add_egili_to_wallets_table.php`: commento RATIONALE aggiornato — `"platform utility tokens"` rimosso, sostituito con `"internal AI service credits"`. Versione → 1.1.0.
- [x] **C2** ✅ 2026-02-25 — `create_egili_transactions_table.php`: commento enum `'purchase'` → `"Acquisto pacchetto AI (accredita Egili — ToS v3.0.0)"`. Versione → 1.1.0.
- [x] **C3** ✅ 2026-02-25 — `add_egili_types_to_egili_transactions_table.php`: comment `egili_type` → `"lifetime (obtained via AI package purchase — ToS v3.0.0) or gift"`.
- [x] **C4** ✅ 2026-02-25 — `EgiliTransactionFactory.php`: header `"Egili Token System"` → `"Egili Credit System"`. Versione → 1.1.0.

---

##### BLOCCO D — Allegato B e Lang files 🟡 P2

- [x] **D1** ✅ 2026-02-25 — `allegato_b_egili.php` **RISCRITTA completamente** → v3.1.0 MiCA-compliant. Sezione 1: Natura Giuridica con `🚫 Gli Egili NON sono un prodotto acquistabile`. Sezione 2: "Pacchetti Servizi AI — Il Prodotto Acquistabile" (FIAT only, no crypto, ratio non hardcoded → punta alla dashboard). Sezione 3-7: utilizzo, condizioni, limitazioni, modifiche, assistenza. Rimosso: tabella EUR hardcoded, tasso `1 EUR = 100 Egili`, sezione crypto.
- [x] **D2** ✅ 2026-02-25 — Lang files aggiornati/creati per tutte e 6 le lingue (P0-9):
    - `it/egili.php` + `en/egili.php`: aggiornate chiavi `buy_more`, `wallet.buy_more`, `purchase.title/subtitle/purchase_now`, aggiunte nuove chiavi `legal_note`, `select_package`, `egili_credited`, `you_get`, `egili_model_note`.
    - **CREATI** ex-novo: `de/egili.php`, `es/egili.php`, `fr/egili.php`, `pt/egili.php` — struttura completa con tutte le sezioni.

---

##### BLOCCO E — Verifica e test finale

- [x] **E1** ✅ 2026-02-25 — `tests/Feature/EgiliTosV3ComplianceTest.php`: E1a verifica schema (`payment_by_egili` assente da `egis`), E1b verifica nessun record `egi_blockchain` con `payment_method='egili'`, E1c verifica che `EgiFactory` crea senza errori dopo la rimozione colonna.
- [x] **E2** ✅ 2026-02-25 — E2a verifica tutti i crypto providers `enabled=false` in config. E2b verifica almeno un FIAT provider abilitato. E2c verifica che POST `/egili/purchase` con `payment_method=crypto` viene rifiutato (non-200).
- [x] **E3** ✅ 2026-02-25 — E3 verifica `EgiliService::grantGiftFromSystem()` (merit reward): crea `EgiliTransaction` con `transaction_type=admin_grant`, `category=reward`, `egili_type=gift`, aggiorna `wallet.egili_balance` correttamente.
- [x] **E4** ✅ 2026-02-25 — E4a verifica che `$egi->payment_by_egili` è `null` (colonna rimossa) → logica MintController `showEgiliOption` non può mai essere `true`. E4b verifica che RebindController non espone l'opzione Egili. Suite: **9 test, 23 assertions — tutti PASSED**.

---

#### Ordine di Implementazione

> **Principio**: mai toccare il DB prima di aver rimosso le interfacce e il backend che ci scrivono.

1. **Fase 1 — Interfacce/UI** 🔴 (Blocco F): Menu, modali, form di pagamento — l'utente non deve più poter innescare le azioni vietate. Iniziare da F1 (modale acquisto Egili), F2/F3 (menu), F7/F8/F9/F10 (pagamento Egili su EGI).
2. **Fase 2 — Routes** 🔴: Disabilitare/rimuovere le route che esponevano le funzionalità vietate (purchase routes).
3. **Fase 3 — Backend/Services** 🔴 (Blocco A + B): Disabilitare la logica nei service/controller. Aggiornare naming e testi.
4. **Fase 4 — Commenti e Allegati** 🟡 (Blocco C + D): Migration comments, Allegato B, lang files 6 lingue.
5. **Fase 5 — DB** 🟡: Solo dopo che routes, controllers e services non scrivono più → rimozione colonne (`payment_by_egili`, enum value `egili` su `payment_method`) con migration dedicata.
6. **Fase 6 — Test** (Blocco E): Verifica e test end-to-end.

---

_Aggiungere qui ulteriori voci man mano che vengono individuate._
