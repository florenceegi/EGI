# FlorenceArt EGI — Claude Code Master Context (Oracode OS3)

> "L'AI non pensa. Predice. Non deduce logicamente. Completa statisticamente."
> FlorenceArt EGI — Organo centrale di certificazione asset (arte, ambiente, ESG)
> URL: art.florenceegi.com
> Stack: Laravel 11.31 (PHP 8.2) + React 19 + TypeScript + Vite + Python FastAPI + Algorand

---

## OSZ — EGI è l'Organo Centrale dell'Ecosistema FlorenceEGI

EGI non è una piattaforma NFT. È il **cuore economico** dell'organismo FlorenceEGI.
Ogni asset certificato è un **EGI**: `Wrapper<T> + Regole + Audit + Valore`.

Il valore generato da EGI alimenta l'**Equilibrium** → EPP (Environmental Protection Projects)
tramite Associazione Frangette.

EGI-HUB è il superiore gerarchico — configurazioni centralizzate lì.
NATAN_LOC condividerà con EGI il RAG engine (destinazione OSZ).

OSZ è la verità assoluta. Questo file si aggiorna per allinearsi a OSZ, mai il contrario.

### Primitivi OSZ attivi in EGI

| Primitivo   | Come si manifesta in EGI                                          |
|-------------|-------------------------------------------------------------------|
| `EGI`       | `app/Models/Egi.php` — Wrapper<T> + Regole + Audit + Valore      |
| `Interface` | Algorand/ASA (ARC-3) — contratto stabile, porta 3000 (AlgoKit)   |
| `URS`       | Non implementato in EGI — da definire con Fabio se necessario     |
| `Nerve`     | Observers, Policies, Jobs, Middleware, Service orchestrators      |

---

## Strategia Delta

```
[NUOVO CODICE]  → segue TUTTE le regole OS3. Zero eccezioni.
[LEGACY]        → resta dove è. Si migra SOLO quando si tocca per altra ragione.
                  MAI refactoring "di principio" su codice production funzionante.
```

### File Legacy Critici [LEGACY] — NON toccare senza piano approvato da Fabio

| File | LOC | Area |
|------|-----|------|
| `app/Http/Controllers/CoaController.php` | 2,888 | COA management |
| `app/Http/Controllers/GdprController.php` | 2,766 | GDPR compliance |
| `app/Http/Controllers/MintController.php` | 2,555 | Blockchain minting |
| `app/Services/AnthropicService.php` | 2,200 | AI/Claude integration |
| `app/Services/Gdpr/DataExportService.php` | 1,998 | GDPR export |
| `app/Services/StatisticsService.php` | 1,894 | Statistiche (P0-3 risk) |
| `app/Http/Controllers/VerifyController.php` | 1,890 | Verifica asset |
| `app/Http/Controllers/PA/NatanChatController.php` | 1,843 | NATAN chat |
| `app/Models/User.php` | 1,815 | Modello utente |
| `app/Services/Payment/StripePaymentSplitService.php` | 1,709 | Stripe split |
| `app/Models/Egi.php` | 1,589 | Core EGI model |
| `app/Console/Commands/CreateUserDomainSystem.php` | 1,522 | User domain setup |
| `app/Services/Coa/CoaPdfService.php` | 1,498 | COA PDF gen |
| `app/Services/ReservationService.php` | 1,488 | Prenotazioni |
| `app/Livewire/Collections/CollectionUserMember.php` | 1,446 | Collection members |
| `app/Http/Controllers/ReservationController.php` | 1,430 | Reservation ctrl |
| `app/Services/NatanChatService.php` | 1,333 | NATAN chat service |
| `app/Http/Controllers/User/PersonalDataController.php` | 1,241 | Dati personali |
| `app/Http/Controllers/EgiController.php` | 1,164 | EGI controller |

---

## P0 — BLOCKING

### Regole Universali (valgono per OGNI file)

| # | Regola | Enforcement |
|---|--------|-------------|
| P0-1 | **REGOLA ZERO** | MAI dedurre. Info mancante → 🛑 CHIEDI |
| P0-2 | **Translation keys** | `__('key')` + Atomic. MAI hardcoded. MAI `__('key', ['param' => $val])` |
| P0-3 | **Statistics Rule** | Nessun `->take(N)` nascosto. Parametri espliciti sempre |
| P0-4 | **Anti-Method-Invention** | `grep` verifica esistenza metodo PRIMA di usarlo |
| P0-5 | **UEM-First** | Errori → `$errorManager->handle()`, mai solo log |
| P0-6 | **Anti-Service-Method** | `read_file` + `grep` prima di qualsiasi service |
| P0-7 | **Anti-Enum-Constant** | Verifica che le costanti enum esistano prima di usarle |
| P0-8 | **Complete Flow Analysis** | Mappa il flusso COMPLETO (4 fasi) prima di qualsiasi fix |
| P0-9 | **i18n 6 lingue** | `it`, `en`, `de`, `es`, `fr`, `pt` — SEMPRE tutte e sei |
| P0-10 | **Anti-Blockchain-Invention** | Verifica metodo in `AlgorandService` con grep PRIMA di usarlo |
| P0-11 | **DOC-SYNC** | Task NON chiusa senza EGI-DOC aggiornato. Zero eccezioni |

### P0 Specifici EGI

| # | Regola | Enforcement |
|---|--------|-------------|
| P0-EGI-1 | **MiCA-SAFE** | NO wallet custody. Crypto = pagamento, non investimento |
| P0-EGI-2 | **GDPR-First** | `GdprActivityCategory` su OGNI operazione dati personali |
| P0-EGI-3 | **TenantScope** | Ogni query tenant-specific → `auth()->user()->tenant_id` |
| P0-EGI-4 | **Multi-tenancy** | `stancl/tenancy v3` attivo — rispetta isolamento tenant |
| P0-EGI-5 | **ARC-3 Standard** | Metadata NFT/ASA sempre in formato ARC-3 |

---

## Architettura EGI (Stato Attuale — 2026-03-13)

### Stack

```
Frontend   → React 19 + TypeScript + Vite 5.4 + Tailwind + DaisyUI + Three.js
             Livewire 3 (componenti server-rendered)
             Vanilla TS in /resources/js/ (NATAN chat integration)
Backend    → Laravel 11.31 (PHP 8.2) — 869 file PHP, 213k LOC
             Sanctum (API auth) + Jetstream (dashboard, 2FA)
             Spatie Permission (RBAC, 11 ruoli) + stancl/tenancy v3
Python     → FastAPI (porta 8001) — RAG, AI layer
Blockchain → Algorand Testnet (⚠️ migrazione Mainnet prevista settimana 2026-03-13)
             AlgoKit microservice (porta 3000)
             AlgorandService.php + EgiSmartContractService.php + CertificateAnchorService.php
Database   → PostgreSQL RDS eu-north-1 (AWS)
             DB_SEARCH_PATH: natan,core,public
             Schema core:  users, tenants, roles, permissions, egis, collections,
                           egi_blockchain, egi_blockchain_smart_contracts, gdpr_*
             Schema natan: rag_documents, rag_chunks, rag_user_memories
Storage    → AWS S3 + CloudFront CDN
Pagamenti  → Stripe + PayPal (FIAT-first, MiCA-SAFE)
LLM        → Claude 3.5 Sonnet (AnthropicService.php) + Ollama llama3.1:8b (locale)
```

### Ruoli Piattaforma (PlatformRole enum — 11 valori)

```
NATAN | EPP | CREATOR | COLLECTOR | COMPANY | TRADER_PRO | VIP | WEAK | PA_ENTITY | INSPECTOR | COMMISSIONER
```

### Flusso EGI Asset (Wrapper<T>)

```
Creator → Upload Asset → Egi::create() → COA (Certificate of Authenticity)
       → AlgorandService → ASA Mint (ARC-3) → EgiBlockchain record
       → GDPR audit → Equilibrium → EPP distribution
```

### Deploy EC2

```
Instance: i-0940cdb7b955d1632 (eu-north-1)
Accesso:  AWS SSM (NO SSH diretto)
Path:     /home/forge/art.florenceegi.com/
Branch:   develop (branch attivo — mai origin/main direttamente)
Deploy:   git pull origin develop && php artisan cache:clear && php artisan config:cache && php artisan view:clear
```

---

## Valori Immutabili

```
Al momento nessun valore immutabile definito per EGI.

Quando saranno introdotte soglie/configurazioni critiche
(es. fee distribution %, thresholds COA, parametri Algorand mainnet),
DOVRANNO essere gestite su EGI-HUB — mai hardcoded in EGI.
```

---

## File Critici

```
app/Models/Egi.php                              — Core EGI Wrapper<T>
app/Models/EgiBlockchain.php                    — Record blockchain ASA
app/Services/AlgorandService.php                — Bridge Algorand testnet
app/Services/EgiSmartContractService.php        — Smart contract PyAlgo
app/Services/CertificateAnchorService.php       — SHA256 anchoring
app/Enums/PlatformRole.php                      — 11 ruoli piattaforma
config/algorand.php                             — Config Algorand/AlgoKit
routes/api.php                                  — API pubbliche + protette
routes/web.php                                  — Route web con middleware
algorand-smartcontracts/egi_living_v1.py        — Smart contract PyAlgo [LEGACY]
```

---

## Firma OS3 (P1 — obbligatoria su ogni file nuovo)

```php
<?php

declare(strict_types=1);

/**
 * @package App\Http\[Area]
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - EGI)
 * @date YYYY-MM-DD
 * @purpose [Scopo chiaro e specifico in una riga]
 */
```

---

## Trigger Matrix DOC-SYNC

Prima di chiudere ogni task, classifica la modifica:

| Tipo | Definizione | DOC-SYNC |
|------|-------------|----------|
| 1 — Locale | Fix puntuale, output invariato | NO |
| 2 — Comportamentale | Cambia output visibile, API o comportamento | SÌ → `EGI-DOC/docs/egi/` |
| 3 — Architetturale | Nuovo service/table/model/layer/dipendenza | SÌ → EGI-DOC + CLAUDE.md |
| 4 — Contrattuale | Tocca GDPR/MiCA/ToS/compliance | SÌ + **approvazione Fabio PRIMA** |
| 5 — Naming dominio | Rinomina entità/concetto del dominio | SÌ → grep tutti i file impattati |
| 6 — Cross-project | Impatta più organi dell'ecosistema | SÌ + **approvazione Fabio** |

> Dubbio tra Tipo 1 e 2? → Tratta come Tipo 2.
> Dettaglio completo: `EGI-DOC/docs/oracode/audit/02_TRIGGER_MATRIX.md`

---

## Checklist Pre-Risposta

```
1. Ho TUTTE le info necessarie?               NO  → 🛑 CHIEDI (P0-1)
2. Metodi verificati con grep?                NO  → 🛑 grep prima (P0-4, P0-6)
3. Esiste pattern simile nel codebase?        ?   → 🛑 CERCA prima
4. Sto assumendo qualcosa?                    SÌ  → 🛑 DICHIARA e CHIEDI
5. Aggiungendo limiti impliciti/take(N)?      SÌ  → 🛑 RENDI ESPLICITO (P0-3)
6. Sto toccando file [LEGACY]?                SÌ  → 🛑 DICHIARA + piano Fabio
7. Enum constants verificate?                 NO  → 🛑 grep su app/Enums/ (P0-7)
8. i18n in 6 lingue?                          NO  → 🛑 NON PROCEDERE (P0-9)
9. GDPR audit incluso (dati personali)?       NO  → 🛑 GdprActivityCategory (P0-EGI-2)
10. TenantScope su query tenant-specific?     NO  → 🛑 aggiungi (P0-EGI-3)
11. Tipo modifica → [1-6]?                    ?   → classifica con Trigger Matrix sopra
12. DOC-SYNC eseguito (se Tipo 2+)?           NO  → 🛑 NON CHIUDERE (P0-11)
```

---

## Comandi Verifica Rapida

```bash
# Verifica metodo in un service
grep -n "function methodName" app/Services/**/*.php

# Verifica enum constants
grep -n "CONSTANT_NAME" app/Enums/**/*.php

# Verifica Algorand methods
grep -n "public function" app/Services/AlgorandService.php

# Controlla translations esistenti (6 lingue)
ls lang/

# File più grandi (anti-bloat)
find app/ -name "*.php" -exec wc -l {} + | sort -rn | head -20

# Cerca TenantScope nei servizi
grep -rn "tenant_id\|TenantScope" app/Services/ | head -20

# Verifica UEM usage
grep -rn "errorManager\|UEM" app/ --include="*.php" | head -20

# Verifica GDPR audit
grep -rn "GdprActivityCategory\|GdprAuditLog" app/ --include="*.php" | head -20
```

---

## Modello Operativo

| Ruolo | Persona | Responsabilità |
|-------|---------|----------------|
| CEO & OS3 Architect | Fabio Cherici | Visione, standard, approvazione arch, valori immutabili |
| CTO & Technical Lead | Padmin D. Curtis (AI) | Esecuzione, enforcement OS3, delivery |

Decisioni su Interface, valori immutabili, Strategia Delta, refactoring LEGACY
→ **sempre approvate da Fabio prima dell'esecuzione**.

---

## Agenti Specializzati

| Agente | Quando usarlo |
|--------|---------------|
| `laravel-specialist` | Controllers, Services, Models, Migrations, Routes, Lang |
| `frontend-ts-specialist` | React/TSX, Vanilla TS, Vite, Tailwind, componenti Livewire view |
| `blockchain-specialist` | Algorand, ASA minting, smart contracts, AlgoKit |
| `python-rag-specialist` | FastAPI, RAG, AI layer, NatanChatService integration |
| `doc-sync-guardian` | DOC-SYNC post ogni task (P0-11) |

---

## 🔍 Sistema Audit Oracode

| Riferimento | Path |
|-------------|------|
| Target ID | T-001 (vedi TARGET_MATRIX) |
| Runbook audit | `EGI-DOC/docs/oracode/audit/07_RUNBOOK.md` |
| Enforcement Claude | `EGI-DOC/docs/oracode/audit/06_CLAUDE_CODE_ENFORCEMENT.md` |
| Trigger Matrix completa | `EGI-DOC/docs/oracode/audit/02_TRIGGER_MATRIX.md` |
| Report audit | `EGI-DOC/docs/oracode/audit/reports/` |
| **AWS Infrastructure** | `EGI-DOC/docs/egi-hub/AWS_INFRASTRUCTURE.md` |

---

*EGI v1.0.0 — Oracode OS3.0 — FlorenceEGI Organismo Software*
*Padmin D. Curtis (CTO) for Fabio Cherici (CEO) — "Less talk, more code. Ship it."*
