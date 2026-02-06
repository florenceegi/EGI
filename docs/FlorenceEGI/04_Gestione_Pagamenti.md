# FlorenceEGI – Gestione Pagamenti (4 Livelli)

## Filosofia: Inclusione Progressiva

Il sistema è progettato per **tutti**: dal neofita che usa solo euro al crypto-nativo che accetta stablecoin, fino agli utenti attivi dell'ecosistema che utilizzano il token interno Egili.

**Nessuno è escluso, nessuna complessità è imposta.**

---

## Tokenomics e Distribuzione Royalty: I Profili Collection

Il sistema di pagamenti non distingue più rigidamente tra Ruoli Utente, ma permette di scegliere il **Profilo della Collection** più adatto al proprio modello.

> [!NOTE]
> **Commodity (Assets Finanziari/Materiali)**:
> Per le Commodity (es. Gold Bars, **sia Master che Clone**), la **Fee di Piattaforma (10%)** si applica solo sul **Margine Aziendale** (Markup), non sull'intero prezzo dell'asset. Il costo vivo della materia prima è esente da fee. Questo vale per ogni singola copia mintata.

### Chi può scegliere cosa?
*   **Creator (Utenti Base):** Operano nativamente nel profilo **Contributor**.
*   **Company (Aziende):** Possono **scegliere** tra profilo **Contributor** (per benefici etici) o profilo **Normal** (business puro).

### 1. Profilo CONTRIBUTOR (Modello Etico)

✅ **IMPLEMENTATO** in `app/Enums/Fees/FeeStructureEnum.php` (CONTRIBUTOR_MINT, CONTRIBUTOR_REBIND)

*Accessibile a Creator e Company. Nessun costo fisso di abbonamento.*

**Struttura Mint:**
| Destinatario | Percentuale | Note |
|--------------|-------------|------|
| Owner | 68% | Royalty netta venditore |
| **EPP** | **20%** | **Obbligatorio** (Sostegno ecosistema) |
| Natan (Piattaforma) | 10% | Infrastruttura tecnologica |
| **Frangette** | **2%** | **Obbligatorio** (Sviluppo sociale) |
| **Abbonamento** | **NESSUNO** | Profilo gratuito |

### 2. Profilo NORMAL (Modello Business)

✅ **IMPLEMENTATO** in `app/Enums/Fees/FeeStructureEnum.php` (NORMAL_MINT, NORMAL_REBIND)

*Esclusivo per Company. Nessun beneficio etico aggiuntivo.*

**Struttura Mint:**
| Destinatario | Percentuale | Note |
|--------------|-------------|------|
| Company | 90% | Esente da EPP e Frangette |
| EPP | 0% | Esente |
| Natan (Piattaforma) | 10% | Infrastruttura tecnologica |
| Frangette | 0% | Esente |
| **Abbonamento** | **OBBLIGATORIO** | Richiesto per mantenere la collection |

---

### Rebind (Secondary Market)

Le fee di rivendita seguono il profilo della Collection originale.

| Destinatario | Profilo CONTRIBUTOR | Profilo NORMAL |
| :--- | :--- | :--- |
| **Creator Royalty** | 4.5% | 6.0% |
| **Natan (Piattaforma)** | 0.8% | 1.0% |
| **Frangette** | 0.2% | 0% |
| **EPP** | 1.5% | 0% |
| **TOTALE FEES** | **7.0%** | **7.0%** |
| **COMMODITY (GOLD)** | **Fee Fissa 50 Egili** | **Fee Fissa 50 Egili** |

*(Il venditore incassa il residuo: 93.0% nel sia Contributor che Normal)*

### Riepilogo Differenze

✅ **Implementazione verificata vs** `FeeStructureEnum.php`

| Aspetto | Profilo CONTRIBUTOR | Profilo NORMAL |
| :--- | :--- | :--- |
| **Target Utente** | Creator & Company (Etiche) | Solo Company |
| **Frangette** | ✅ **Sì (2%)** | ❌ **No (0%)** |
| **EPP Royalty** | ✅ **Sì (20%)** | ❌ **No (0%)** |
| **Abbonamento** | ❌ **No (Gratis)** | ✅ **Obbligatorio** |
| **Royalty Netta Base (Mint)** | 68% | 90% |
| **Creator Royalty (Rebind)** | 4.5% | 6.0% |

---

## Livello 1 — Nessun Wallet (100% FIAT Tradizionale)

### Per il Cliente

**Esperienza identica a un e-commerce tradizionale**:

1. Sceglie EGI da acquistare
2. Paga in **euro** su pagina sicura PSP (Stripe, Adyen, etc.)
3. Riceve conferma acquisto via email
4. **EGI appare automaticamente** nel suo profilo FlorenceEGI
5. Può verificare autenticità tramite **QR code** (scan con smartphone)

**Zero competenze blockchain richieste. Zero crypto. Zero wallet da gestire.**

### Per il Merchant (Creator/Artista)

1. Imposta prezzo EGI in euro
2. Cliente paga → PSP incassa
3. **PSP esegue split payment** (vedi sezione Tokenomics sopra):
    - **Creator**: 68% (se Creator) o 88% (se Company)
    - **EPP**: 20% (Opzione A) o 0% (Opzione B/Company/Livello 4)
    - **Piattaforma (Natan)**: 10%
    - **Associazione (Frangette)**: 2% (solo Creator, Company esente)
4. **Payout FIAT** sul conto bancario merchant (timing: T+2/T+7 secondo PSP)
5. Merchant riceve report dettagliato transazione

**Royalty e ripartizioni gestite off-chain dal PSP.**### Wallet Auto-Generato (Custodia Tecnica Limitata)

Per utenti che pagano in FIAT e non hanno wallet, FlorenceEGI **genera automaticamente** un wallet Algorand.

#### Caratteristiche

-   **Creazione**: Al momento registrazione o primo acquisto
-   **Contenuto**: SOLO NFT unici (EGI), **nessuna criptovaluta**
-   **Sicurezza**: Chiavi private cifrate AES-256 server-side
-   **Storage**: Database conforme GDPR
-   **Invisibile**: Utente non vede il wallet (gestione trasparente)

#### Diritti Utente

L'utente può **in qualsiasi momento**:

1. Accedere alla propria area personale
2. **Scaricare la frase segreta** (seed phrase)
3. Importare in Pera Wallet / Defly / altro client compatibile
4. **Richiedere cancellazione definitiva** chiavi da server FlorenceEGI

**Finché non effettua export, il wallet rimane gestito dalla piattaforma.**

#### Limitazioni

Il wallet **NON può essere utilizzato** per:

-   Detenere ALGO (criptovaluta nativa)
-   Ricevere/inviare stablecoin (USDCa, EURC)
-   Trading crypto
-   Operazioni finanziarie

**Solo NFT unici (EGI) con valore artistico/culturale.**

#### Conformità MiCA-safe

Questa gestione costituisce **custodia tecnica limitata di asset digitali non finanziari** e **non configura attività CASP** (Crypto-Asset Service Provider) ai sensi del Regolamento MiCA.

**Motivazioni**:

-   Nessun cambio valuta
-   Nessuna custodia fondi monetari
-   Nessuna intermediazione finanziaria
-   Solo certificati digitali di autenticità

**FlorenceEGI opera fuori dal perimetro MiCA**, soggetta esclusivamente a:

-   Sicurezza informatica (GDPR Art. 32)
-   Protezione dati personali (GDPR)

---

## Livello 2 — Ho un Wallet, Pago in FIAT

### Per il Cliente

**Usa il proprio wallet (Pera, Defly, etc.) per ricevere l'EGI, ma paga in euro.**

1. Sceglie EGI da acquistare
2. Paga in **euro** tramite PSP
3. **Durante checkout**: inserisce indirizzo wallet Algorand
4. Riceve **EGI direttamente nel proprio wallet** (transfer on-chain)
5. Piena proprietà e controllo dell'asset

**Vantaggi**:

-   Controllo totale (non-custodial)
-   Può trasferire EGI liberamente
-   Può importare in altri marketplace compatibili Algorand
-   Sicurezza: solo tu hai le chiavi

### Per il Merchant

1. Imposta prezzo in euro
2. Cliente paga FIAT → PSP incassa
3. FlorenceEGI **esegue mint + transfer EGI** a wallet cliente
4. PSP esegue payout FIAT al merchant
5. **Tracciabilità on-chain** completa

**Differenza rispetto Livello 1**: Transfer on-chain verso wallet esterno (invece di wallet auto-generato).

### Gestione Wallet Utenti – Modello Non-Custodial FIAT

Quando l'utente decide di usare il proprio wallet:

#### Export da Wallet Auto-Generato

1. Utente accede a "Impostazioni Wallet"
2. Scarica **seed phrase** (12-25 parole)
3. Importa in Pera Wallet / Defly
4. **Richiede cancellazione definitiva** da database FlorenceEGI
5. Da quel momento: **FlorenceEGI non detiene più chiavi private**

#### Nuovo Acquisto con Wallet Proprio

Durante acquisto:

1. Utente inserisce **indirizzo wallet Algorand**
2. Paga in FIAT tramite PSP
3. **Mint ASA (EGI)** eseguito con `sender = wallet utente`
4. Nessun transfer successivo (minting diretto)
5. FlorenceEGI paga micro-fee di rete come **fee-payer tecnico**

**Flusso**:

```
Cliente paga €100 → PSP → Split payment off-chain
Simultaneamente:
FlorenceEGI mint EGI con sender=wallet_cliente → Blockchain Algorand
```

**Nessun fondo crypto transita tra le parti. Pagamento resta 100% FIAT.**

#### Conformità MiCA-safe

-   **Nessuna custodia**: Wallet sotto controllo esclusivo utente
-   **Nessuna intermediazione**: Minting diretto a wallet utente
-   **Nessun scambio**: Pagamento FIAT ↛ crypto (sono processi separati)

**Fuori perimetro MiCA**: Semplice emissione NFT unico verso wallet proprietà utente, con pagamento valuta tradizionale.

---

## Livello 3 — Accetto Pagamenti Crypto (Opzionale)

### Per il Merchant

**Richiede partnership con CASP/EMI autorizzato.**

#### Setup

1. Merchant si registra presso **Partner autorizzato** (es. Coinbase Commerce, MoonPay)
2. Completa KYC/AML presso Partner
3. Configura wallet ricezione fondi crypto
4. **Integra checkout Partner** in FlorenceEGI (via API)

#### Flusso Vendita

1. Cliente sceglie EGI
2. Seleziona "Paga in Crypto"
3. **Redirect a checkout Partner** (es. Coinbase Commerce)
4. Cliente paga in crypto (BTC, ETH, USDC, etc.) a Partner
5. **Partner notifica FlorenceEGI**: pagamento completato
6. FlorenceEGI esegue **mint + transfer EGI** a wallet cliente
7. **Partner esegue settlement** al merchant (crypto o FIAT, secondo accordi)

**FlorenceEGI riceve solo notifica, non tocca mai i fondi crypto.**

#### Responsabilità

-   **KYC/AML**: Partner autorizzato
-   **Conversione crypto/FIAT**: Partner (se richiesto)
-   **Tax reporting**: Partner + Merchant
-   **Compliance MiCA**: Partner (licenza CASP)

### Per il Cliente

1. Sceglie EGI
2. Paga in crypto preferita sul checkout Partner
3. Riceve EGI nel proprio wallet
4. **Conferma transazione blockchain** (link explorer)

**Esperienza simile a pagamento FIAT, ma con stablecoin/crypto.**

### Pagamenti Stablecoin via PSP Partner – Wallet-to-Wallet Direct

**Modalità avanzata per utenti esperti.**

#### Setup

1. Utente ha wallet Algorand (Pera, Defly)
2. Wallet contiene stablecoin (USDCa, EURC) o ALGO
3. Merchant ha configurato PSP partner per crypto

#### Flusso

1. Utente sceglie EGI, seleziona "Paga con Stablecoin"
2. Inserisce **indirizzo wallet** e **seleziona stablecoin**
3. **FlorenceEGI genera richiesta pagamento** (QR code / deep link)
4. Utente approva transazione nel proprio wallet
5. **Trasferimento wallet-to-wallet diretto** → PSP Partner
6. PSP notifica FlorenceEGI: pagamento ricevuto
7. FlorenceEGI **mint EGI** con `sender = wallet utente`
8. PSP esegue settlement al merchant (crypto o FIAT)

**FlorenceEGI non gestisce conversioni, non detiene fondi, non partecipa alla transazione stablecoin.**

#### Stablecoin Accettate

**Devono essere emesse da soggetti conformi MiCA** e riconosciuti:

-   **USDCa** (USD Coin Algorand) - Circle
-   **EURC** (Euro Coin) - Circle
-   **USDT** (Tether, se conforme)

#### Conformità MiCA-safe

FlorenceEGI opera **unicamente come infrastruttura di registrazione blockchain**, senza ruolo finanziario.

**Responsabilità**:

-   **Pagamento crypto**: PSP Partner (licenza CASP/EMI)
-   **Conversione**: PSP (se applicabile)
-   **Custody**: Utente (wallet proprio) e PSP (wallet merchant)
-   **Minting NFT**: FlorenceEGI (fuori perimetro MiCA)

**Rapporto contrattuale diretto**: Utente ↔ PSP Partner

---

## Livello 4 — Pagamento con Egili (Token Interno)

### Cos'è Egili?

**Egili** è il **token utility interno** di FlorenceEGI con caratteristiche specifiche:

-   ✅ **Non trasferibile**: Non può essere scambiato tra utenti
-   ✅ **Non quotato**: Nessuna quotazione su exchange esterni
-   ✅ **Account-bound**: Legato all'account utente (no wallet crypto)
-   ✅ **Merit-based**: Si guadagna attraverso attività meritevoli, non si compra
-   ✅ **MiCA-safe**: Punti fedeltà fuori perimetro crypto-asset

**Tasso di conversione**: **1 Egilo = €0.01** (valore percepito interno)

### Come si Guadagnano Egili?

Gli utenti accumulano Egili automaticamente attraverso:

1. **Volume vendite**: Creator guadagna Egili proporzionali alle vendite generate
2. **Referral verificati**: Bonus per nuovi utenti portati che completano KYC
3. **Donazioni EPP volontarie**: Oltre il 20% standard (bonus aggiuntivo)
4. **Partecipazione community**: Eventi, feedback costruttivi, contributi
5. **Fondo distribuzione**: 1% delle fee piattaforma → Pool Egili distribuito secondo merito

### Come Funziona il Pagamento?

#### Per il Creator (Configurazione)

1. Nel **CRUD panel EGI**, il Creator può abilitare opzione "Accetta pagamento Egili"
2. Checkbox: `payment_by_egili` (default: disabilitata)
3. Una volta abilitata, gli utenti con saldo Egili sufficiente vedranno l'opzione

#### Per il Buyer (Acquisto)

**Step 1: Selezione Metodo Pagamento**

1. Buyer sceglie EGI da acquistare (es: €25.00)
2. Nella pagina checkout vede opzioni:
    - Carta di credito (Stripe/PayPal)
    - Bonifico bancario
    - Crypto via PSP Partner
    - **🪙 Pagamento con Egili** (se abilitato dal Creator)

**Step 2: Verifica Saldo**

```
Prezzo EGI: €25.00
Egili richiesti: 2,500 Egili (25.00 / 0.01)
Saldo utente: 5,000 Egili

✅ Opzione abilitata (saldo sufficiente)
```

Se saldo insufficiente:

```
Saldo utente: 1,500 Egili
❌ Opzione disabilitata (mostra: "Saldo insufficiente")
```

**Step 3: Conferma e Pagamento**

1. Buyer seleziona "Paga con Egili"
2. Sistema mostra riepilogo:
    ```
    ┌─────────────────────────────────────────┐
    │ EGI: "Sunset Over Florence"            │
    │ Prezzo: €25.00                          │
    │ Egili richiesti: 2,500                  │
    │ Tuo saldo: 5,000 → 2,500 (dopo acquisto)│
    └─────────────────────────────────────────┘
    ```
3. Conferma → **2,500 Egili vengono "bruciati"** (consumo irreversibile)
4. Wallet: `5,000 - 2,500 = 2,500 Egili rimanenti`

**Step 4: Mint Blockchain**

1. Sistema genera riferimento pagamento: `EGL-X9K2P7M4Q1W8`
2. Crea record blockchain con:
    - `payment_provider: 'egili_internal'`
    - `paid_currency: 'EGL'`
    - `paid_amount_recorded: 2500` (Egili spesi)
3. **Mint asincrono** su Algorand (identico agli altri livelli)
4. Buyer riceve EGI nel wallet (auto-generato o proprio)

### Tracciabilità e Audit

Ogni pagamento Egili genera:

1. **EgiliTransaction** record:

    ```json
    {
        "transaction_type": "spent",
        "amount": 2500,
        "reason": "egi_direct_mint",
        "category": "mint",
        "balance_before": 5000,
        "balance_after": 2500,
        "metadata": {
            "egi_id": 42,
            "payment_reference": "EGL-X9K2P7M4Q1W8"
        }
    }
    ```

2. **GDPR Audit Log**: Categoria `BLOCKCHAIN_ACTIVITY`
3. **ULM Log**: `EGILI_SPEND_SUCCESS`
4. **Wallet update**: Saldi aggiornati atomicamente

### Conformità MiCA-safe

**Egili NON sono crypto-asset** perché:

-   ✅ Non trasferibili (no exchange)
-   ✅ Non convertibili in denaro
-   ✅ Utilizzo esclusivo interno piattaforma
-   ✅ Classificazione: **Punti fedeltà** (come programmi loyalty tradizionali)

**Fuori perimetro MiCA**: Nessun obbligo licenza CASP/EMI.

### Vantaggi Sistema Egili

**Per Creator**:

-   Incentiva fidelizzazione utenti attivi
-   Riduce barriera economica acquisto
-   Premia community engagement

**Per Buyer**:

-   Utilizza ricompense guadagnate
-   Zero costi bancari (no PSP fee)
-   Esperienza gamificata

**Per Piattaforma**:

-   Circolarità economica interna
-   Engagement aumentato
-   Compliance semplificata (no crypto)

### Limitazioni

-   ❌ **Solo fee di mint**: Egili coprono il costo transazione, non royalty Creator/EPP
-   ❌ **Opt-in Creator**: Creator deve abilitare esplicitamente per ogni EGI
-   ❌ **Irreversibile**: Egili spesi sono bruciati definitivamente (no refund)
-   ❌ **Non cumulabile**: Non si può pagare "metà Egili + metà FIAT" (tutto o niente)

---

## Confronto Livelli

| Aspetto             | Livello 1 (No Wallet)         | Livello 2 (Wallet FIAT) | Livello 3 (Wallet Crypto) | Livello 4 (Egili)        |
| ------------------- | ----------------------------- | ----------------------- | ------------------------- | ------------------------ |
| **Pagamento**       | Euro (PSP)                    | Euro (PSP)              | Crypto (PSP Partner)      | Egili (token interno)    |
| **Wallet Cliente**  | Auto-generato                 | Proprio (non-custodial) | Proprio (non-custodial)   | Auto-generato o proprio  |
| **Complessità**     | Zero                          | Bassa                   | Media                     | Zero                     |
| **Controllo Asset** | Limitato (export disponibile) | Totale                  | Totale                    | Limitato/Totale          |
| **Target**          | Neofiti, tradizionalisti      | Crypto-curious          | Crypto-native             | Utenti attivi ecosistema |
| **MiCA-safe**       | ✅ Sì                         | ✅ Sì                   | ✅ Sì (tramite Partner)   | ✅ Sì (punti fedeltà)    |
| **Costi**           | Fee PSP (1-3%)                | Fee PSP (1-3%)          | Fee blockchain (~€0.001)  | Zero                     |

---

## Cosa Fa (e NON Fa) la Piattaforma

### ✅ Cosa FlorenceEGI FA

-   Incassa **FIAT** tramite PSP per propria **fee** (10% default)
-   Gestisce **Egili** (token interno non-crypto, punti fedeltà)
-   Emette e trasferisce **EGI** (NFT unici)
-   Scrive **anchor hash** su blockchain (notarizzazione)
-   Gestisce **QR code** e verifica pubblica autenticità
-   Calcola **royalty** per istruire PSP su split payment
-   Fornisce **dashboard** e reportistica

### ❌ Cosa FlorenceEGI NON FA

-   **NON custodisce** criptovalute per terzi (solo NFT unici in wallet auto-generati)
-   **NON fa da exchange** crypto/fiat o Egili/fiat (Egili non convertibili)
-   **NON processa** pagamenti crypto direttamente (tramite PSP Partner autorizzati)
-   **NON detiene** fondi utenti (né FIAT né crypto)
-   **NON svolge** attività CASP (MiCA-safe by design)
-   **NON permette** trasferimento Egili tra utenti (account-bound)

---

## Gestione Spedizioni (Shipping Workflow)

Per gli Asset Fisici (Commodity, Phygital) che richiedono consegna fisica, il sistema integra un workflow logistico parallelo al flusso finanziario.

### 1. Snapshot e Privacy (GDPR)
Al momento del checkout (qualsiasi Livello di pagamento), se l'EGI richiede spedizione:
*   Il sistema cattura un **Snapshot Statico** dell'indirizzo di spedizione.
*   Questo dato viene salvato in `egi_blockchain` e `notification_payload_shippings`.
*   **Privacy**: L'indirizzo è legato alla singola transazione (storico immutabile) e visibile solo alle controparti.

### 2. Flusso di Notifica "Actionable"
A differenza delle piattaforme tradizionali, la gestione logistica è guidata dalle Notifiche Interattive:

1.  **Merchant**: Riceve notifica **"EGI Sold - Spedizione Richiesta"**.
    *   La notifica contiene un'azione diretta (Form integrato): **"Inserisci Tracking"**.
    *   Non serve navigare in pannelli complessi; l'operazione si gestisce dalla notifica.
2.  **Input Dati**: Il Merchant inserisce *Carrier* (Corriere) e *Tracking Code*.
3.  **Aggiornamento**: Il sistema aggiorna il **Payload di Spedizione** (`notification_payload_shippings`) e lo stato `ugi_blockchain`.
4.  **Buyer**: Riceve automaticamente notifica **"Il tuo EGI è in viaggio"** con link tracciamento.

### 3. Integrazione Dati
*   **Finanza**: Gestita da `payment_distributions` (Chi prende i soldi).
*   **Logistica**: Gestita da `notification_payload_shippings` (Chi sposta la merce).
*   **Asset**: Gestito da `egi_blockchain` (Chi possiede il titolo).

Questa separazione garantisce che un problema sulla spedizione non blocchi la contabilità (e viceversa), mantenendo però tutto collegato nello stesso Ordine Logico.

---

## Principio Guida

**Inclusione progressiva senza esclusione**: ogni utente può partecipare al livello di complessità che preferisce, senza imporre barriere tecnologiche.

**Dal nonno che paga con carta di credito, al crypto-nativo che usa stablecoin, fino all'utente attivo che utilizza i propri Egili guadagnati: tutti possono collezionare EGI.**
