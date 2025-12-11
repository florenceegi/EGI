# 09. Sistema di Auto-Rinnovo e Pagamenti Ricorsivi

> **Versione:** 1.0.0
> **Data:** 11/12/2025
> **Autore:** Padmin D. Curtis (AI Partner OS3.0)

## Introduzione
Il sistema di **Auto-Rinnovo** (Auto-Addebito) di FlorenceEGI è un modulo architetturale generico progettato per gestire pagamenti ricorrenti per qualsiasi tipo di servizio (Abbonamenti Collection, Strumenti SaaS, Funzionalità Premium).

A differenza dei pagamenti una tantum, il sistema mantiene uno **stato di intento** ("l'utente vuole rinnovare") separato dal **ledger transazionale** ("il pagamento è avvenuto").

## Architettura

### 1. Database: `recurring_subscriptions`
Questa tabella funge da "state manager" per i rinnovi. È polimorfica, quindi può agganciarsi a qualsiasi entità.

| Colonna | Descrizione |
|---|---|
| `user_id` | L'utente pagante. |
| `subscribable_type` | Il modello del servizio (es. `App\Models\Collection`). |
| `subscribable_id` | L'ID del servizio specifico. |
| `service_type` | Identificativo logico (es. `collection_subscription`). |
| `status` | `active`, `cancelled`, `payment_failed`. |
| `next_renewal_at` | Data prevista per il prossimo addebito. |
| `metadata` | Snapshot dei costi/condizioni al momento della sottoscrizione. |

### 2. Service Layer: `RecurringPaymentService`
Questo servizio orchestra la logica di rinnovo:
- **`registerSubscription`**: Attiva o riattiva un rinnovo automatico.
- **`processDueRenewals`**: Metodo batch chiamato dal Cron Job. Verifica i fondi e processa il pagamento.
- **`cancelSubscription`**: Disattiva il rinnovo (ma non rimborsa il periodo corrente).

### 3. Integrazione con Egili (Transactional Truth)
Quando un rinnovo viene processato con successo:
1. Viene invocato `EgiliService::spend()` per dedurre i fondi e creare la `EgiliTransaction`.
2. Viene aggiornata la data `next_renewal_at` in `recurring_subscriptions`.
3. (Opzionale) Viene chiamato un metodo specifico del servizio (es. `CollectionSubscriptionService::recordRenewalTransaction`) per generare record specifici di dominio (es. `AiCreditsTransaction`).

## Flusso Operativo

1. **Attivazione**: Quando un utente paga un abbonamento (o tramite toggle nella dashboard), viene creato un record `active` in `recurring_subscriptions`.
2. **Cron Job**: Ogni ora/giorno, il comando `egi:process-recurring-payments` cerca i record con `status=active` e `next_renewal_at <= NOW()`.
3. **Esecuzione**:
    - **Successo**: Saldo scalato, scadenza prorogata (+30gg), notificata inviata.
    - **Fail (No Fondi)**: Contatore `failed_attempts` incrementato. Dopo N tentativi, lo stato passa a `payment_failed`.

## Comandi Artisan

```bash
# Esegue il controllo e processa i rinnovi scaduti
php artisan egi:process-recurring-payments
```

## Integrazione Frontend
Lo stato del rinnovo è esposto tramite l'accessor `$model->is_auto_renew_active`. Nella dashboard, un toggle permette all'utente di chiamare API per attivare/disattivare il servizio (logica implementata nel Controller, che richiama `RecurringPaymentService`).

## Policy di Gestione Scadenza e Downgrade (v1.1)

### 1. Scadenza Abbonamento (Expiration Policy)
Quando un abbonamento scade (e non viene rinnovato per mancanza fondi o cancellazione):
*   **Visibilità**: La Collection e i suoi EGI rimangono **pienamente visibili**. Nessun contenuto viene nascosto.
*   **Interazione Social**: Gli utenti possono continuare a mettere "Like" e interagire.
*   **Vendita (Minting)**: La possibilità di vendere (mintare) nuovi EGI viene **DISABILITATA**.
    *   Gli EGI già venduti rimangono nella collezione come "sold/minted".
    *   Gli EGI invenduti non possono essere acquistati finché non viene riattivato un piano valido o un EPP.

### 2. Policy di Downgrade e Sottoscrizione
Un Creator **NON PUÒ** sottoscrivere un piano che offre un numero di slot EGI inferiore al numero di EGI già presenti nella collezione.
*   *Esempio*: Se una collezione ha 50 EGI, il Creator non può passare a un "Basic Plan" da 10 EGI.
*   **UI Enforcement**: I piani non sufficienti a coprire il numero attuale di EGI devono essere disabilitati o nascosti nell'interfaccia di acquisto.
