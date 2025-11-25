# EGI External Collection Integration & Secondary Market Architecture

## 1. Visione

Documento unico che definisce: - Importazione di intere collection
esterne (Algorand ASA) - Gestione come "Collection Esterne" - Flusso di
Egizzazione (conversione in EGI) - Listing da parte dei creator/owner -
Attivazione mercato secondario FlorenceEGI

------------------------------------------------------------------------

## 2. Importazione Collection Esterne

### 2.1 Fonti dati

-   Algorand Indexer (ASA, metadata, owner)
-   Marketplace esterni (Rand, Algox, Shufl) tramite API pubbliche
-   IPFS metadata

### 2.2 Processo

1.  Recupero asset_id della collection
2.  Fetch on-chain:
    -   nome
    -   unit-name
    -   metadata-hash
    -   creator
    -   url (IPFS)
3.  Fetch off-chain (se disponibile):
    -   prezzo marketplace esterno
    -   owner listing esterno
4.  Creazione "Collection Esterna" nel DB:
    -   `id`
    -   `nome_collection`
    -   `source_marketplace`
    -   `descrizione`
5.  Creazione schede EGI-show pre-listing
6.  Nessuna listing attiva → solo visualizzazione

------------------------------------------------------------------------

## 3. Proprietario esterno: identificazione & accesso

### 3.1 Registrazione utente FlorenceEGI

-   Account email/password
-   Onboarding Stripe PSP
-   Logging ULM/UEM

### 3.2 Collegamento Wallet

-   Connect wallet (Pera/Defly)
-   Firma messaggio (verifica proprietà)
-   Salvataggio:
    -   `user_id`
    -   `wallet_address`
    -   `verified_signature`

### 3.3 Mappatura ASA → Owner

Tramite Indexer: - `amount = 1` → owner - Validazione automatica: solo
il wallet che possiede l'ASA può listarlo

------------------------------------------------------------------------

## 4. EGI-zzazione (Conversione NFT → EGI)

### 4.1 Cos'è la Egizzazione

Processo con cui un NFT esterno: - diventa un EGI ufficiale - entra nel
Marketplace FlorenceEGI - eredita: - EPP - CoA esteso - Curatela -
Meccanismi social - Notifiche NATAN - Dual-flow fisico/digitale (se
creato)

### 4.2 Flusso tecnico

1.  Utente apre scheda ASA importato
2.  Clicca **"Converti in EGI"**
3.  La piattaforma:
    -   Verifica ownership on-chain
    -   Genera record `egi`
    -   Collega metadata estesi
    -   Attiva gestione EPP
4.  L'asset è ora un "EGI Esterno"

------------------------------------------------------------------------

## 5. Listing su FlorenceEGI

### 5.1 Requisiti

-   Utente registrato
-   Wallet collegato e verificato
-   Stripe PSP completato
-   ASA effettivamente posseduto on-chain

### 5.2 Passi

1.  Utente apre `egi.show`
2.  Clicca **"Metti in vendita"**
3.  Piattaforma mostra form:
    -   prezzo
    -   valuta (€)
    -   condizioni (fee, EPP, royalty)
4.  Backend:
    -   Verifica ownership via Indexer
    -   Scrive record in `listings`
    -   Log ULM/UEM
5.  Listing visibile pubblicamente

### 5.3 Record listing

    listings:
    - id
    - egi_id
    - seller_user_id
    - seller_wallet
    - price
    - currency
    - status ("active")
    - created_at
    - updated_at

------------------------------------------------------------------------

## 6. Mercato Secondario FlorenceEGI

### 6.1 Acquisto

-   Pagamento FIAT tramite Stripe
-   Split:
    -   Creator Royalty
    -   Co-Creator Split
    -   EPP %
    -   Marketplace fee
-   Algorand Atomic Transfer eseguito dalla piattaforma

### 6.2 Sincronizzazione on-chain

Se l'owner vende fuori: - Indexer cattura transazione - FlorenceEGI
disattiva listing interno - Aggiorna owner - NATAN notifica
artisti/collezionisti

### 6.3 Multi-listing logico

-   Un ASA può essere:
    -   "Elencato altrove"
    -   "EGI ufficiale"
    -   "Listing attivo FlorenceEGI"
    -   "Listing esterno attivo" (visibile tramite API)

------------------------------------------------------------------------

## 7. Benefici specifici FlorenceEGI

-   Marketplace FIAT (barriera zero)
-   EPP sempre integrato
-   CoA esteso e verificabile
-   Curatela artistica
-   Community Mecenati
-   Dual Flow (fisico/digitale)
-   Egili / loyalty
-   NATAN per attivazione culturale
-   Importazione NFT → promozione artistica

------------------------------------------------------------------------

## 8. Diagramma sintetico del flusso

    [ Algorand ASA ] --> [ Import Collection ] --> [ EGI-show esterno ]
                                     |                    |
                                     |                    --> [ Egizzazione ]
                                     |                             |
                                     |                             --> [ Listing EGI ]
                                     |                                       |
                                     -------------------------------> [ Marketplace FIAT ]

------------------------------------------------------------------------

## 9. Conclusione Finale

Con questo modello: - importi intere collection esterne - gestisci NFT
esterni come beni culturali (EGI) - abiliti listing controllati e
sicuri - attivi un mercato secondario reale - garantisci che solo il
vero owner possa listare e vendere - mantieni FIAT, EPP, royalty e CoA
come elementi centrali

Questo è il design giusto per FlorenceEGI.
