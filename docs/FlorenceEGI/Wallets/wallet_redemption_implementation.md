# 📜 Wallet Redemption System - Documentazione Implementazione

> **Version**: 1.0.0
> **Date**: 2025-01-20
> **Author**: Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
> **Status**: ✅ Implementation Complete

---

## 🎯 Overview

Il sistema di **Wallet Redemption** permette agli utenti di FlorenceEGI di "riscattare" il proprio wallet Algorand, trasferendo la custodia dalla piattaforma all'utente stesso. Dopo il riscatto, l'utente ottiene:

1. La **seed phrase** (25 parole mnemonic)
2. Tutti gli **EGI (ASA)** trasferiti al proprio wallet
3. Il **saldo ALGO** necessario per gestire il wallet

⚠️ **IMPORTANTE**: Il riscatto è **IRREVERSIBILE**. Dopo il completamento, la piattaforma non può più accedere al wallet dell'utente.

---

## 🏗️ Architettura

```
┌─────────────────────────────────────────────────────────────────────────┐
│                        WALLET REDEMPTION FLOW                           │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                         │
│  ┌──────────┐    ┌──────────────────┐    ┌───────────────────┐        │
│  │  User    │───▶│ WalletController │───▶│ WalletRedemption  │        │
│  │ Browser  │    │    (Laravel)     │    │    Service        │        │
│  └──────────┘    └──────────────────┘    └─────────┬─────────┘        │
│                                                     │                   │
│                                                     ▼                   │
│                                          ┌───────────────────┐          │
│                                          │  AlgorandClient   │          │
│                                          │    (Laravel)      │          │
│                                          └─────────┬─────────┘          │
│                                                     │                   │
│                                                     ▼                   │
│                                          ┌───────────────────┐          │
│                                          │ Microservice      │          │
│                                          │ (Node.js/AlgoKit) │          │
│                                          └─────────┬─────────┘          │
│                                                     │                   │
│                                                     ▼                   │
│                                          ┌───────────────────┐          │
│                                          │ Algorand Network  │          │
│                                          │   (TestNet/MainNet)│          │
│                                          └───────────────────┘          │
│                                                                         │
└─────────────────────────────────────────────────────────────────────────┘
```

---

## 💰 Formula Costi

### Calcolo in Micro-Algos

```
Total Cost = Base Cost + ASA Cost + Transaction Fees

Where:
- Base Cost = 100,000 μALGO (0.1 ALGO) - minimum wallet balance
- ASA Cost  = N × 100,000 μALGO (0.1 ALGO per ASA opt-in)
- TX Fees   = (opt_in_batches + transfer_batches + 2) × 1,000 μALGO
```

### Conversione in EGILI

```
EGILI Cost = Total ALGO × EGILI_PER_ALGO

Default: EGILI_PER_ALGO = 100 (100 EGILI = 1 ALGO)
```

### Esempio Pratico

Per un utente con **5 EGI**:

```
Base Cost    = 0.1 ALGO
ASA Cost     = 5 × 0.1 = 0.5 ALGO
TX Fees      = (1 + 1 + 2) × 0.001 = 0.004 ALGO
─────────────────────────────────────────────
Total ALGO   = 0.604 ALGO
Total EGILI  = ceil(0.604 × 100) = 61 EGILI
```

---

## 🔄 Flusso Operativo Dettagliato

### Step 1: Visualizzazione Pagina Riscatto

```
GET /wallet/redemption
```

Il controller:
1. Verifica che l'utente abbia un wallet
2. Verifica che il wallet non sia già riscattato
3. Calcola il costo basato sul numero di EGI
4. Mostra la lista degli EGI che verranno trasferiti

### Step 2: Esecuzione Riscatto

```
POST /wallet/redemption/execute
```

**Request Body:**
```json
{
    "confirmation_text": "CONFERMO RISCATTO",
    "accept_terms": true
}
```

**Response (Success):**
```json
{
    "success": true,
    "message": "Wallet riscattato con successo",
    "mnemonic": "word1 word2 ... word25",
    "document": "<base64_encoded_txt_file>",
    "filename": "wallet-seed-phrase-2025-01-20.txt",
    "details": {
        "egili_deducted": 61,
        "asa_transferred": 5,
        "wallet_funded": 0.604
    }
}
```

### Step 3: Operazioni Blockchain (Interno)

La sequenza esatta delle operazioni blockchain:

```
1. EGILI Deduction (Database)
   wallet.egili_balance -= cost
   wallet.egili_lifetime_spent += cost

2. Fund Wallet (Algorand)
   Treasury → User Wallet (0.604 ALGO)
   TX: fund-wallet

3. Batch Opt-In (Algorand)
   For each batch of max 16 ASAs:
     User signs opt-in transactions
     Atomic group submission
   TX: batch-opt-in-asa

4. Batch Transfer (Algorand)
   For each batch of max 16 ASAs:
     Treasury signs transfer transactions
     Atomic group submission
   TX: batch-transfer-asa

5. Mnemonic Delivery
   Decrypt mnemonic from DB
   Return to user

6. Mnemonic Deletion (IRREVERSIBLE)
   wallet.secret_ciphertext = NULL
   wallet.secret_nonce = NULL
   wallet.dek_encrypted = NULL
```

---

## 📁 File Modificati/Creati

### Microservice (Node.js)

| File | Modifiche |
|------|-----------|
| `algokit-microservice/server.js` | +5 nuovi endpoint: `/opt-in-asa`, `/batch-opt-in-asa`, `/transfer-asa`, `/batch-transfer-asa` |

### Laravel Backend

| File | Modifiche |
|------|-----------|
| `app/Services/Wallet/WalletRedemptionService.php` | **NUOVO** - Servizio orchestratore completo |
| `app/Services/Blockchain/AlgorandClient.php` | +4 metodi: `optInToAsa()`, `batchOptInToAsas()`, `transferAsa()`, `batchTransferAsas()` |
| `app/Http/Controllers/WalletController.php` | +3 metodi: `executeRedemption()`, `getRedemptionCost()`, `getUserEgisForRedemption()` |
| `app/Enums/Gdpr/GdprActivityCategory.php` | +2 casi: `WALLET_REDEEMED`, `WALLET_MNEMONIC_DELETED` |
| `routes/web.php` | +3 rotte: `/redemption/execute`, `/redemption/cost`, `/redemption/egis` |

---

## 🔑 Costanti di Configurazione

```php
// WalletRedemptionService.php

// Cost per ASA (0.1 ALGO)
const COST_PER_ASA_MICRO_ALGO = 100000;

// Base wallet cost (0.1 ALGO minimum balance)
const BASE_WALLET_COST_MICRO_ALGO = 100000;

// Transaction fee estimate (0.001 ALGO)
const TX_FEE_MICRO_ALGO = 1000;

// Max ASAs per batch (Algorand protocol limit)
const MAX_BATCH_SIZE = 16;

// EGILI to ALGO rate
const EGILI_PER_ALGO = 100;
```

---

## 🛡️ Considerazioni di Sicurezza

### GDPR Compliance

Tutti gli accessi sensibili sono loggati:
- `WALLET_SECRET_ACCESSED` - Accesso alla mnemonic
- `WALLET_REDEEMED` - Riscatto completato
- `WALLET_MNEMONIC_DELETED` - Cancellazione mnemonic

### Memory Security

```php
// Secure memory cleanup after mnemonic use
if (function_exists('sodium_memzero')) {
    sodium_memzero($mnemonic);
}
```

### No Logging of Secrets

La mnemonic **MAI** viene loggata nei log:

```php
$this->logger->info('WalletRedemption: Opt-in to ASA', [
    'asa_id' => $asaId,
    // NO user_mnemonic here!
    'log_category' => 'ALGORAND_OPT_IN_ASA'
]);
```

---

## 🔧 Recovery Procedures

### Scenario 1: Errore durante Opt-In

**Problema**: Le transazioni opt-in falliscono dopo che gli EGILI sono stati detratti.

**Soluzione**:
1. La transazione è in un blocco `DB::transaction()`
2. Se opt-in fallisce, l'intera operazione viene rollback
3. Gli EGILI vengono ripristinati automaticamente

### Scenario 2: Errore durante Transfer

**Problema**: Il transfer ASA fallisce dopo opt-in riuscito.

**Soluzione**:
1. Il sistema usa batch atomici - se un transfer fallisce, tutto il batch fallisce
2. Il rollback ripristina lo stato pre-redemption
3. L'utente può riprovare

### Scenario 3: Utente perde la Mnemonic dopo Redemption

**Problema**: L'utente non ha salvato la mnemonic e il wallet è stato riscattato.

**Soluzione**:
- ⚠️ **NESSUN RECOVERY POSSIBILE**
- La mnemonic è stata cancellata dal database
- L'utente perde l'accesso al wallet e a tutti gli ASA

**Prevenzione**:
1. UI richiede conferma esplicita ("CONFERMO RISCATTO")
2. Documento scaricabile con mnemonic
3. Warning multipli prima della cancellazione

### Scenario 4: Microservice Non Disponibile

**Problema**: Il microservice Algorand non risponde.

**Soluzione**:
1. `AlgorandClient::ensureMicroserviceRunning()` verifica la disponibilità
2. Se non disponibile, l'operazione fallisce prima di qualsiasi modifica
3. UEM logga l'errore per monitoraggio

### Scenario 5: Treasury Insufficiente

**Problema**: La Treasury non ha abbastanza ALGO per il funding.

**Soluzione**:
1. Monitorare il saldo Treasury regolarmente
2. Alert quando sotto soglia minima
3. L'errore viene propagato all'utente con messaggio chiaro

---

## 🧪 Testing

### Test Unitari Necessari

```php
// tests/Feature/WalletRedemptionServiceTest.php

public function test_calculate_redemption_cost_with_no_egis()
public function test_calculate_redemption_cost_with_multiple_egis()
public function test_validation_fails_without_wallet()
public function test_validation_fails_when_already_redeemed()
public function test_validation_fails_with_insufficient_egili()
public function test_execute_redemption_success()
public function test_execute_redemption_rollback_on_failure()
public function test_mnemonic_deleted_after_successful_redemption()
```

### Test E2E Necessari

```bash
# 1. Create user with wallet and EGIs
# 2. Fund wallet with EGILI
# 3. Call /wallet/redemption/execute
# 4. Verify mnemonic returned
# 5. Verify EGILI deducted
# 6. Verify ASAs transferred on-chain
# 7. Verify mnemonic deleted from DB
```

---

## 📊 API Reference

### GET /wallet/redemption/cost

Calcola il costo del riscatto senza eseguirlo.

**Response:**
```json
{
    "success": true,
    "can_redeem": true,
    "is_redeemed": false,
    "cost": {
        "micro_algos": 604000,
        "algo": 0.604,
        "egili": 61,
        "breakdown": {
            "asa_count": 5,
            "base_cost_algo": 0.1,
            "asa_cost_algo": 0.5,
            "estimated_fees_algo": 0.004,
            "egili_rate": "100 EGILI = 1 ALGO"
        }
    },
    "egili_balance": 1000,
    "errors": []
}
```

### GET /wallet/redemption/egis

Lista degli EGI dell'utente che verranno trasferiti.

**Response:**
```json
{
    "success": true,
    "count": 5,
    "egis": [
        {
            "id": 123,
            "title": "My First EGI",
            "collection_name": "Nature Collection",
            "asa_id": 12345678,
            "minted_at": "2025-01-15T10:30:00Z"
        }
    ]
}
```

### POST /wallet/redemption/execute

Esegue il riscatto completo. **IRREVERSIBILE**.

**Request:**
```json
{
    "confirmation_text": "CONFERMO RISCATTO",
    "accept_terms": true
}
```

**Response (Success):**
```json
{
    "success": true,
    "message": "Wallet riscattato con successo",
    "mnemonic": "abandon ability able about above absent absorb abstract absurd abuse access accident account accuse achieve acid acoustic acquire across act action actor actress actual adapt",
    "document": "base64...",
    "filename": "wallet-seed-phrase-2025-01-20.txt",
    "details": {
        "egili_deducted": 61,
        "asa_transferred": 5,
        "wallet_funded": 0.604
    }
}
```

---

## 📝 Note per Sviluppatori Futuri

1. **EGILI Rate Dinamico**: Attualmente `EGILI_PER_ALGO` è hardcoded. In futuro, potrebbe essere fetch da un oracle o configurabile.

2. **Batch Size**: Il limite di 16 è imposto dal protocollo Algorand per atomic transaction groups.

3. **MainNet**: Prima del deploy su MainNet, verificare:
   - Treasury wallet configurato correttamente
   - Mnemonic Treasury sicura (non in codice)
   - Rate limiting sugli endpoint

4. **Monitoring**: Implementare dashboard per monitorare:
   - Numero di redemption giornalieri
   - Saldo Treasury
   - Errori di blockchain

---

## 📞 Supporto

Per problemi con il sistema di wallet redemption:

1. Verificare i log: `storage/logs/laravel.log` (cercare `WALLET_REDEMPTION`)
2. Verificare lo stato del microservice: `GET /health`
3. Verificare il saldo Treasury su Algorand explorer

---

*Documento generato automaticamente - FlorenceEGI Platform*
