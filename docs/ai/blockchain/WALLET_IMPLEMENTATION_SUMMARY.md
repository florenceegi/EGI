# Wallet Implementation Summary - FlorenceEGI

## 🎯 Obiettivo Completato

Implementazione completa del sistema di wallet sicuro con:

-   **Wallet Algorand reali** (non più fake addresses)
-   **IBAN cifrato** per pagamenti FIAT
-   **Envelope Encryption** con KMS (AWS/Azure/Vault/GCP)
-   **GDPR Compliance** completo con audit trail

## 📦 Componenti Implementati

### 1. **Microservice Node.js** (`algokit-microservice/server.js`)

-   ✅ Endpoint `/create-account` - Crea account Algorand reali con algosdk
-   ✅ Restituisce: `address`, `mnemonic`, `privateKeyBase64`

### 2. **AlgorandClient** (`app/Services/Blockchain/AlgorandClient.php`)

-   ✅ Comunicazione HTTP con il microservice Node.js
-   ✅ Metodi: `createAccount()`, `getAccountInfo()`, `healthCheck()`
-   ✅ ULM logging + UEM error handling
-   ✅ Validazione formato address (58 chars, Base32)

### 3. **WalletProvisioningService** (`app/Services/Wallet/WalletProvisioningService.php`)

-   ✅ Provisioning completo wallet per user
-   ✅ Envelope encryption della mnemonic con KMS
-   ✅ Cifratura IBAN con Laravel encrypted cast
-   ✅ Hash IBAN con pepper per uniqueness check
-   ✅ GDPR audit logging
-   ✅ Metodo `retrieveMnemonic()` per export sicuro

### 4. **KmsClient** (`app/Services/Security/KmsClient.php`)

-   ✅ Già implementato con supporto multi-provider:
    -   AWS KMS
    -   Azure Key Vault
    -   HashiCorp Vault
    -   Google Cloud KMS
-   ✅ Mock mode per development
-   ✅ `secureEncrypt()` e `secureDecrypt()` con envelope encryption

### 5. **UserWallet Model** (`app/Models/UserWallet.php`)

-   ✅ Campi per Algorand wallet:
    -   `address` - Public address
    -   `secret_ciphertext` - Encrypted mnemonic
    -   `secret_nonce` - Nonce per XChaCha20-Poly1305
    -   `dek_encrypted` - DEK cifrata con KMS (JSON)
    -   `cipher_algo` - Algoritmo usato
-   ✅ Campi per IBAN wallet:
    -   `iban_encrypted` - IBAN cifrato (Laravel cast)
    -   `iban_hash` - SHA-256 hash con pepper
    -   `iban_last4` - Ultimi 4 digit per UI
-   ✅ Hidden attributes per sicurezza
-   ✅ Helper methods: `isAlgorand()`, `isIban()`, `getMaskedIbanAttribute()`

### 6. **ValidIban Rule** (`app/Rules/ValidIban.php`)

-   ✅ Validazione formato IBAN (15-34 chars)
-   ✅ Validazione checksum MOD-97
-   ✅ Validazione lunghezza per country code (60+ paesi)

### 7. **GdprActivityCategory Enum** (aggiornato)

-   ✅ `WALLET_CREATED` - Creazione wallet
-   ✅ `WALLET_SECRET_ACCESSED` - Accesso alla mnemonic
-   ✅ Privacy level: CRITICAL (retention 7 anni)

### 8. **RegisteredUserController** (aggiornato)

-   ✅ Integrazione con `WalletProvisioningService`
-   ✅ Creazione wallet reale durante registrazione
-   ✅ Supporto IBAN opzionale
-   ✅ Supporto wallet_passphrase opzionale

### 9. **RegistrationRequest** (aggiornato)

-   ✅ Validazione campo `iban` (opzionale, con ValidIban rule)
-   ✅ Validazione campo `wallet_passphrase` (opzionale, min 12 chars)
-   ✅ Campo `accept_custody_seed` per consenso custodial wallet

## 🔐 Sicurezza Implementata

### Envelope Encryption Flow

```
1. Generate DEK (32 bytes random)
2. Encrypt mnemonic with DEK using XChaCha20-Poly1305
3. Encrypt DEK with KMS KEK (AWS/Azure/Vault/GCP)
4. Store: encrypted_mnemonic + encrypted_DEK
5. Wipe DEK from memory (sodium_memzero)
```

### IBAN Security

```
1. Normalize IBAN (remove spaces, uppercase)
2. Validate checksum (MOD-97)
3. Encrypt with Laravel encrypted cast (app key)
4. Create hash: SHA-256(IBAN + pepper)
5. Store: encrypted_iban + hash + last4
```

### Audit Trail

-   ✅ Ogni creazione wallet loggata con GDPR_ACTIVITY_CATEGORY::WALLET_CREATED
-   ✅ Ogni accesso alla mnemonic loggato con WALLET_SECRET_ACCESSED
-   ✅ Retention: 7 anni (CRITICAL privacy level)

## 📋 Database Migration

La migration `user_wallets` include:

```sql
- id (PK)
- user_id (FK → users)
- type (enum: 'algorand', 'iban')
- address (nullable, Algorand public address)
- secret_ciphertext (binary, encrypted mnemonic)
- secret_nonce (binary, nonce for XChaCha20)
- secret_tag (binary, optional for AES-GCM)
- dek_encrypted (binary, encrypted DEK from KMS)
- iban_encrypted (text, Laravel encrypted)
- iban_hash (string, SHA-256 with pepper)
- iban_last4 (string, for UI display)
- meta (json, additional metadata)
- cipher_algo (string, algorithm used)
- version (int, schema version)
- timestamps + soft deletes
```

## ⚙️ Configurazioni Necessarie

### 1. `.env` - AWS KMS (Production)

```env
# KMS Configuration
KMS_PROVIDER=aws
AWS_DEFAULT_REGION=eu-west-1
AWS_ACCESS_KEY_ID=your-access-key-id
AWS_SECRET_ACCESS_KEY=your-secret-access-key
AWS_KMS_KEK_ARN=arn:aws:kms:eu-west-1:123456789:key/your-key-id

# Algorand Microservice
ALGORAND_MICROSERVICE_URL=http://localhost:3000
ALGORAND_NETWORK=sandbox  # sandbox|testnet|mainnet
ALGORAND_TIMEOUT=10

# IBAN Security
APP_IBAN_PEPPER=your-random-pepper-string-here
```

### 2. Development Mode (Mock KMS)

```env
KMS_PROVIDER=aws
APP_ENV=local  # Usa mock KMS automaticamente
```

## 🧪 Testing

### Test Manuale - Registrazione

1. Avvia il microservice Algorand:

    ```bash
    cd algokit-microservice
    npm install
    node server.js
    ```

2. Verifica health check:

    ```bash
    curl http://localhost:3000/health
    ```

3. Registra un nuovo utente con IBAN (usertype che richiede payout):

    - Creator
    - Patron (Mecenate)
    - EPP
    - Company

4. Verifica nel database:
    ```sql
    SELECT * FROM user_wallets WHERE user_id = X;
    -- Dovresti vedere 2 record:
    -- 1. type='algorand' con address, secret_ciphertext, dek_encrypted
    -- 2. type='iban' con iban_encrypted, iban_hash, iban_last4
    ```

### Test KMS Mock

```bash
php test-kms-mock.php
```

Output atteso:

```
=== KMS Provider Test ===

Environment: local
KMS Provider: aws
KEK ID: egi-wallet-master-key

Original text: test wallet mnemonic phrase for development

🔐 Testing encryption...
✅ Encrypted successfully
   Provider: mock
   KEK ID: egi-wallet-master-key

🔓 Testing decryption...
✅ Decrypted successfully
   Decrypted text: test wallet mnemonic phrase for development

✅ ✅ ✅ KMS TEST PASSED! ✅ ✅ ✅
Envelope encryption working correctly.
```

## 🚀 Deployment Checklist

-   [ ] Configurare AWS KMS e creare KEK
-   [ ] Aggiungere credenziali AWS in `.env` production
-   [ ] Aggiornare `APP_IBAN_PEPPER` con valore sicuro random
-   [ ] Deployare microservice Node.js (Docker/PM2)
-   [ ] Verificare connettività backend → microservice
-   [ ] Eseguire migration `user_wallets`
-   [ ] Testare registrazione completa su staging
-   [ ] Verificare audit logs in produzione
-   [ ] Configurare monitoring per KMS errors
-   [ ] Backup encrypted wallets (disaster recovery)

## 📖 Documentazione Aggiuntiva

### Export Mnemonic (Futuro)

Per permettere agli utenti di esportare la propria mnemonic:

```php
// Controller method (con 2FA + step-up auth)
public function exportMnemonic(Request $request, UserWallet $wallet)
{
    // 1. Verify 2FA
    // 2. Verify step-up authentication
    // 3. Get mnemonic
    $mnemonic = $this->walletProvisioningService->retrieveMnemonic(
        $wallet,
        auth()->user()
    );

    // 4. Return encrypted file or display once
    return response()->json([
        'mnemonic' => $mnemonic,
        'warning' => 'Save this in a secure place. It will not be shown again.'
    ]);
}
```

### Key Rotation (Futuro)

Per ruotare le DEK quando necessario:

1. Decrypt mnemonic con vecchia DEK
2. Generate nuova DEK
3. Encrypt mnemonic con nuova DEK
4. Encrypt nuova DEK con KMS
5. Update record con nuovi valori + increment version

## 🎓 Risorse

-   [Algorand SDK Documentation](https://developer.algorand.org/)
-   [AWS KMS Best Practices](https://docs.aws.amazon.com/kms/latest/developerguide/best-practices.html)
-   [IBAN Validation Standard](https://www.swift.com/standards/iban)
-   [XChaCha20-Poly1305](https://doc.libsodium.org/secret-key_cryptography/aead/chacha20-poly1305)

---

**Status**: ✅ IMPLEMENTATION COMPLETE  
**Date**: 2025-10-22  
**Version**: 1.0.0  
**Author**: Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
