# Sistema di Pagamento Servizi e Monetizzazione - FlorenceEGI

## 1. Architettura Generale: "Transactional Truth"

Il sistema di pagamento dei servizi in FlorenceEGI (es. Subscription Collection, AI Description, Minting) si basa su un'architettura **Transaction-Ledger**.

A differenza dei sistemi tradizionali che memorizzano lo stato (es. `is_premium = true`) nelle tabelle delle entità (es. `users`, `collections`), FlorenceEGI deriva lo stato corrente analizzando il registro delle transazioni (`ai_credits_transactions` e `egili_transactions`).

### Principi Chiave
1.  **Immutabilità**: Lo storico dei pagamenti è immutabile.
2.  **Stato Derivato**: Lo stato "Attivo/Scaduto" è calcolato al volo (`Runtime Calculation`) basandosi sull'ultima transazione valida.
3.  **Disaccoppiamento**: Le entità (`Collection`, `Egi`) non "sanno" di essere pagate; è il `Service Layer` che interroga il Ledger per determinarne i diritti.

---

## 2. Componenti del Sistema

### 2.1 Il Ledger: `AiCreditsTransaction`
Tutte le operazioni che consumano crediti o valuta interna (Egili) vengono registrate nella tabella `ai_credits_transactions`.

*   **Source of Truth**: È l'unica fonte di verità per diritti e consumi.
*   **Struttura Chiave**:
    *   `user_id`: Chi ha pagato.
    *   `source_type`: Il servizio acquistato (es. `collection_subscription`, `ai_description`).
    *   `source_id`: L'ID dell'entità collegata (es. ID della Collection).
    *   `amount`: Costo in Egili.
    *   `status`: `completed`, `failed`.
    *   `expires_at`: Data di scadenza del diritto (critica per le subscription).

### 2.2 Il Wallet: `EgiliService`
Gestisce il saldo dell'utente in **Egili** (la valuta interna).
*   Verifica la disponibilità fondi (`canSpend`).
*   Esegue la transazione atomica (`spend`).

### 2.3 I Service Managers
Sono i "cervelli" che interrogano il Ledger.

*   **`CollectionSubscriptionService`**:
    *   *Domanda*: "La Collection #5 ha l'abbonamento attivo?"
    *   *Logica*: Cerca in `ai_credits_transactions` l'ultima transazione `collection_subscription` per la Collection #5 con `expires_at > NOW()`.
    *   *Risultato*: `true` o `false`. Non legge una colonna `status` nella tabella `collections`!

*   **`AiService` (Generico)**:
    *   Gestisce pagamenti "One-Off" (es. generazione descrizione).
    *   Registra il consumo immediato senza scadenza.

---

## 3. Flusso di Pagamento: Subscription

Quando un Creator acquista un abbonamento per sbloccare la vendita (Monetizzazione):

1.  **Frontend**: User clicca "Attiva Abbonamento" nel pannello della Collection.
2.  **Controller**: Chiama `CollectionSubscriptionService::processSubscription($user, $collection)`.
3.  **Service**:
    a.  Controlla saldo Egili (`EgiliService`).
    b.  Deduce 5000 Egili (`EgiliService::spend`).
    c.  **Scrive nel Ledger**: Crea un record in `ai_credits_transactions` con `expires_at = NOW() + 30gg`.
4.  **View (`show.blade.php`)**:
    a.  Non legge `$collection->subscription_status` (che non esiste).
    b.  Chiama l'accessor `$collection->subscription_status` (calcolato) o usa il Service per verificare i diritti.
    c.  Se attivo, nasconde il badge di blocco e abilita la vendita.

---

## 4. Tipi di Monetizzazione

La piattaforma supporta modelli ibridi determinati dinamicamente (Accessors):

| Tipo Utente | Modello Monetizzazione | Requisito |
| :--- | :--- | :--- |
| **Azienda (Company)** | `subscription` | Pagamento mensile in Egili. EPP (Donazione) è volontario. |
| **Creator (Standard)** | `epp` | Obbligo di selezione progetto EPP (Royalties). Subscription non richiesta. |

> **Nota**: Il modello è determinato in `Collection::getMonetizationTypeAttribute()` basandosi sul `usertype` del creator.

---

## 5. Vantaggi di questa architettura

*   **Audit Trail**: Sappiamo esattamente chi ha pagato cosa e quando.
*   **Flessibilità**: Possiamo cambiare le regole (es. durata abbonamento, costi) senza migrare dati nel database, ma solo agendo sulla logica dei Service.
*   **Integrità**: Impossibile avere uno stato "Attivo" senza una transazione corrispondente che lo giustifichi.
