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

_Aggiungere qui ulteriori voci man mano che vengono individuate._
