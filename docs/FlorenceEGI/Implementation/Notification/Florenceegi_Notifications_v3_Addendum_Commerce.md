# Addendum v3.1: Ottimizzazione Flusso Commerce & Auto-Resolution Pattern

**(Data: 4 Febbraio 2026)**
*Autore: Antigravity (AI Partner OS3.0) per Fabio Cherici*

---

## **1. Introduzione**
Questo addendum estende la guida "FlorenceEGI Notifications v3.0" documentando l'implementazione specifica del modulo **Commerce**.
Mentre l'architettura base V3 rimane invariata, il modulo Commerce introduce un pattern ottimizzato di **Auto-Risoluzione** (Auto-Resolution Loop) gestito direttamente dal livello Infrastrutturale (`CustomDatabaseChannel`).

---

## **2. Il Pattern "Auto-Resolution Loop"**

### **2.1 Il Problema**
Nel flusso standard V3 (Request/Response), la chiusura del ciclo (es. marcare una notifica come "letta" o "gestita" dopo l'azione dell'utente) richiedeva spesso logica manuale nei Controller.
Nel caso del Commerce (Vendita -> Spedizione), il flusso è:
1.  Merchant riceve notifica "Oggetto Venduto" (`EgiSoldNotification`).
2.  Merchant spedisce l'oggetto.
3.  La notifica "Oggetto Venduto" deve sparire/essere archiviata.
4.  Il Buyer deve ricevere "Oggetto Spedito".

### **2.2 La Soluzione: Channel-Driven Resolution**
Abbiamo spostato la logica di "chiusura" dentro il canale stesso.
Quando il sistema invia la notifica di risposta (`EgiShippedNotification`), il canale `CustomDatabaseChannel` rileva questo evento e chiude automaticamente la notifica genitore.

**Schema Logico:**
```php
// CustomDatabaseChannel.php

// Mappatura Evento -> Azione
$actionResponseMap = [
    // Se invio una notifica di "Spedizione Avvenuta"...
    'App\Notifications\Commerce\EgiShippedNotification' => NotificationStatus::SHIPPED,
];

// Logica di Auto-Risoluzione
if ($action === NotificationStatus::SHIPPED) {
    // ...il canale cerca la notifica precedente (Sold) e la chiude (Outcome: SHIPPED)
    $this->updatePreviousNotification($notification_prevId, $action);
}
```

**Vantaggi:**
*   **Zero Boilerplate**: I controller non devono preoccuparsi di pulire le notifiche vecchie. Inviare la risposta *è* l'atto di pulizia.
*   **Consistenza**: Impossibile dimenticarsi di chiudere un task. Se spedisci, il task di vendita si chiude.

---

## **3. Compliance, Enums e Visibilità**

### **3.1 Rigore sui Tipi (Enums)**
Per garantire la conformità alla filosofia OS3 "No Magic Strings", è stato introdotto il case `SHIPPED` nell'Enum ufficiale.

**File:** `App\Enums\NotificationStatus`
```php
case SHIPPED = 'shipped'; // Stato ufficiale per il flusso commerce
```

Questo elimina le stringhe hardcoded e centralizza la definizione degli stati validi.

### **3.2 Visibilità Lato Buyer (Outcome Logic)**
È stata corretta una criticità architetturale riguardante la visibilità delle notifiche passive.
*   **Problema**: Impostare `outcome => 'done'` su una nuova notifica la rendeva invisibile (già processata).
*   **Soluzione**: La notifica inviata al Buyer (`EgiShippedNotification`) nasce ora con stato `PENDING`.
    ```php
    // EgiShippedNotification.php
    'outcome' => \App\Enums\NotificationStatus::PENDING->value
    ```
    Questo garantisce che appaia immediatamente nella lista "Da Leggere" del Buyer.

---

## **4. Evoluzione Architetturale Frontend**

### **4.1 Modalità "Pure Vanilla" (Rule P0-0)**
In accordo con le nuove direttive architetturali (Febbraio 2026), l'interfaccia di risposta (Modale di Spedizione) è stata implementata ripudiando i framework reattivi (Alpine/Livewire) a favore di standard web puri per garantire stabilità a lungo termine.

*   **Implementazione**: HTML5 + CSS (Tailwind) + Vanilla JS.
*   **Interazione**: Gestione eventi diretta (`onclick`, manipolazione classi `hidden`/`flex`).
*   **Vantaggio**: Indipendenza totale da librerie esterne per componenti critici di business.

---

## **5. Riepilogo Integrazione**
Con queste modifiche, il modulo Commerce si integra perfettamente nella V3, rispettandone i principi di Polimorfismo e Payload Strutturati, ma elevando il livello di automazione grazie al *Channel-Driven Resolution Pattern*.
