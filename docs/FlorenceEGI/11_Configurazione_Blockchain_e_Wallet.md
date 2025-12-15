# FlorenceEGI – Configurazione Blockchain e Wallet Management

## 1. Strategia di Gestione Wallet

FlorenceEGI adotta una strategia differenziata per la gestione dei wallet di Tesoreria in base all'ambiente di esecuzione. Questa distinzione è fondamentale per garantire sia la flessibilità in fase di sviluppo che la sicurezza in fase di staging/produzione.

### 1.1 Sandbox (Sviluppo Locale)
In ambiente locale (AlgoKit Sandbox), la piattaforma utilizza i wallet pre-generati dal protocollo (KMD - Key Management Daemon).

- **Treasury Wallet**: `Y2IGWQ5ZL2LBSNBQCKFC3QDRJFGXSJGYB5IWSXPDTHTF6UTBJTMEU5LPYE`
- **Ruolo**: Custodial tecnico (Creator degli ASA).
- **Funding**: Lo script `fund-treasury.js` preleva fondi dal "Dispenser" della Sandbox e li invia a questo wallet.
- **Microservizio**: Se non trova configurazioni specifiche, il microservizio fall-backa automaticamente su questo wallet e sulla sua Mnemonic di default (`misery earn...`).

### 1.2 TestNet / Staging (Bootstrap)
In ambiente di Staging (e per il bootstrap su TestNet), la piattaforma adotta una strategia "Dual Role" per semplificare la gestione della liquidità iniziale.

- **Treasury Wallet**: Corrisponde al **Wallet di Natan** (`TF67P6...`).
- **Doppio Ruolo**:
  1.  **Revenue Wallet**: Riceve le fee della piattaforma (10% delle vendite).
  2.  **Custodial Treasury**: Firma le transazioni di Minting degli ASA e paga le network fee.
- **Vantaggio**: I fondi incassati dalle vendite auto-finanziano le operazioni di minting, eliminando la necessità di funding manuale costante della Tesoreria.
- **Configurazione**: Richiede esplicitamente la variabile `TREASURY_MNEMONIC` nel file `.env` del microservizio.

---

## 2. Configurazione Microservizio (Algokit)

Il microservizio Node.js (`algokit-microservice`) è il cuore pulsante delle operazioni blockchain. La sua configurazione differisce da quella di Laravel.

### 2.1 File .env Dedicato
Il microservizio **NON legge** il file `.env` principale di Laravel (root del progetto).
Richiede un proprio file `.env` posizionato in:
`/home/forge/[project-path]/current/algokit-microservice/.env`

**Variabili Richieste (TestNet):**
```bash
PORT=3001
ALGORAND_NETWORK=testnet
ALGORAND_API_URL=https://testnet-api.algonode.cloud
ALGORAND_INDEXER_URL=https://testnet-idx.algonode.cloud
# Mnemonic del wallet Natan (che agisce da Treasury)
TREASURY_MNEMONIC="... inserire mnemonic wallet natan (25 parole) ..."
```

### 2.2 Derivazione dell'Address
Il parametro `ALGORAND_TREASURY_ADDRESS` **non è necessario** nel `.env` del microservizio.
Il sistema calcola matematicamente l'address pubblico a partire dalla `TREASURY_MNEMONIC` ad ogni avvio:
```javascript
// Esempio logica server.js
const account = algosdk.mnemonicToSecretKey(process.env.TREASURY_MNEMONIC);
const address = account.addr; // TF67P6...
// TF67P6...
```
Questo garantisce che l'address usato per firmare sia matematicamente corrispondente alla chiave privata fornita.

### 2.3 Autenticazione API (Sicurezza)
Per proteggere le rotte sensibili (es. minting, transfer), il microservizio richiede un **Bearer Token**.

1.  **Configurazione**: Aggiungere `ALGOKIT_API_TOKEN` nel `.env` del microservizio.
2.  **Configurazione Laravel**: Aggiungere lo stesso `ALGOKIT_API_TOKEN` nel `.env` principale.
3.  **Utilizzo**: Tutte le chiamate HTTP devono includere l'header `Authorization: Bearer <TOKEN>`.

---

## 3. Script di Utilità

### 3.1 fund-treasury.js
Script utilizzato per finanziare la Tesoreria.
- **Comportamento**: Legge `TREASURY_MNEMONIC` dall'.env e invia fondi al wallet derivato.
- **Nota**: In ambiente Sandbox usa il Dispenser. In TestNet richiede un wallet finanziatore (attualmente non attivo per security, si usa funding esterno/manuale o auto-funding da revenue).

---

## 4. Sicurezza e Best Practices

1.  **Protezione Mnemonic**: La Mnemonic della Tesoreria garantisce accesso totale al minting e ai fondi. In Produzione, questa variabile non deve risiedere in un file `.env` in chiaro, ma essere iniettata tramite Secrets Manager (AWS/Vault) o Variabili d'Ambiente CI/CD protette.
2.  **Rotazione**: In caso di compromissione, è necessario:
    -   Generare un nuovo Wallet Algorand.
    -   Effettuare il *Rekey* del vecchio address verso il nuovo (per mantenere l'identità on-chain ma cambiare la chiave di firma).
    -   Aggiornare la `TREASURY_MNEMONIC` sui server.
3.  **Monitoraggio**: Verificare regolarmente il saldo del Treasury Wallet per evitare errori `OVERSPEND` durante i picchi di minting.
