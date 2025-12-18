# 10. Logica di Rebind (Mercato Secondario)

## Introduzione
Il **Rebind** è il processo di vendita sul mercato secondario di un EGI (Ecological Goods Invent). A differenza del "Mint" (creazione e prima vendita), il Rebind trasferisce la proprietà da un Collezionista a un altro, attivando una catena di distribuzione del valore che premia non solo il venditore, ma anche il Creatore originale e la Piattaforma.

## 1. Architettura delle Royalties
In una transazione di Rebind, il prezzo di vendita pagato dall'acquirente viene suddiviso tra diversi beneficiari.

### Logica di Distribuzione:
L'importo totale (Prezzo di Vendita) viene allocato secondo il seguente ordine di priorità:

> [!IMPORTANT]
> **Eccezione Commodity (Gold Bars)**:
> Le Commodity seguono una logica diversa (Capitale vs Opera d'Ingegno).
> - **Fee Fissa**: Il venditore paga **50 Egili** fissi per il servizio.
> - **Royalties 0%**: Non si applicano le percentuali riportate sotto (Creator, EPP, Natan = 0%).
> - **Netto Venditore**: 100% Prezzo Mercato - 50 Egili.

1.  **Creator (Autore)**
    - **Percentuale**: Predefinita da `config/egi.php` (default 4.5%) o sovrascritta da `Wallet::royalty_rebind`.
    - **Fonte**: Wallet del creatore associato alla Collection.
2.  **EPP (Environmental Protection Program)**
    - **Percentuale**: Definita in `.env` (`EPP_ROYALTY_REBIND` -> default 0.8%).
    - **Destinatario**: Utente di sistema EPP (ID default: 2).
3.  **Natan (Piattaforma)**
    - **Percentuale**: Definita in `.env` (`NATAN_ROYALTY_REBIND` -> default 0.7%).
    - **Destinatario**: Utente di sistema Natan (ID default: 1).
4.  **Frangette (Associazione/Partner)**
    - **Percentuale**: Definita in `.env` (`FRANGETTE_ROYALTY_REBIND` -> default 0.1%).
    - **Destinatario**: Utente di sistema Natan/Frangette.
5.  **Seller (Venditore)**
    - **Percentuale**: Il rimanente (~93.9%).
    - **Calcolo**: `100% - (Creator% + EPP% + Natan% + Frangette%)`

> [!NOTE]
> Le percentuali sono calcolate sul **Prezzo di Vendita in Euro**. Se il pagamento avviene in Egili, viene utilizzato il controvalore in Euro al momento della transazione.

## 2. Flusso Tecnico (`RebindController@process`)

1.  **Validazione**: Verifica disponibilità EGI, fondi acquirente (Stripe/Egili) e stato proprietario.
2.  **Pagamento**: Esecuzione addebito (EgiliService o Stripe).
3.  **Creazione Distribuzione Venditore**:
    - Viene creato un record `PaymentDistribution` iniziale per l'intero importo assegnato al venditore.
4.  **Calcolo & Deduzione Royalties**:
    - Il sistema itera sui beneficiari (Creator, EPP, Natan, Frangette).
    - Per ogni beneficiario, calcola l'importo (`amount_eur`) basato sulla % configurata.
    - Sottrae l'importo totale delle royalties dalla distribuzione del venditore.
    - Aggiorna il record del venditore con il nuovo netto.
5.  **Creazione Distribuzioni Royalties**:
    - Vengono creati record `PaymentDistribution` separati per ogni beneficiario, con `source_type = 'rebind'` e `user_type` specifico (es. 'creator', 'epp', 'frangette').
6.  **Trasferimento EGI**:
    - L'EGI viene assegnato al nuovo proprietario.
    - Lo stato dell'EGI torna a `sold`.
    - Viene registrata la transazione nella storia dell'EGI.

## 3. Configurazione (.env)
Le percentuali globali sono controllate dalle seguenti variabili d'ambiente:

```dotenv
# Royalties per Rebind (Mercato Secondario)
CREATOR_ROYALTY_REBIND=4.5
NATAN_ROYALTY_REBIND=0.7
FRANGETTE_ROYALTY_REBIND=0.1
EPP_ROYALTY_REBIND=0.8
```

## 4. Database Impact
- **Tabella**: `payment_distributions`
- **Nuovi Valori Enum**:
    - `source_type`: 'rebind'
    - `user_type`: 'frangette', 'natan' (oltre ai preesistenti 'creator', 'epp')

---
*Documento aggiornato al 11 Dicembre 2025 - Implementazione Multi-Party Royalty.*
