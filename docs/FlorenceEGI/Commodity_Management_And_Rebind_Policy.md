# Gestione Commodity e Policy Rebind – Architettura Tecnica
**Versione:** 1.0
**Stato:** DRAFT (In attesa di approvazione)
**Lingua:** Italiano

---

## 1. Visione Architetturale

L'obiettivo è disaccoppiare la logica delle Commodity dal sistema generico dei "Traits", che risulta troppo rigido per gestire la complessità di asset fisici come oro o metalli preziosi.

### I Principi del Nuovo Sistema:
1.  **Disaccoppiamento**: La categoria "Traits" non gestirà più i dati vitali (peso, purezza). Esisterà una categoria `Commodity` che definisce solo il *TIPO* (es. 'GoldBar'), mentre i dati risiederanno altrove.
2.  **Metadata JSON**: Un nuovo campo `commodity_metadata` nella tabella `egis` funge da *Single Source of Truth* per le specifiche tecniche.
3.  **Pattern Strategy**: Ogni tipo di commodity avrà una sua classe dedicata (es. `GoldCommodity`, `SilverCommodity`) che incapsula la logica di business, validazione e presentazione.
4.  **UI Dinamica**: La vista `show.blade.php` non dovrà conoscere i dettagli, ma inietterà pannelli "ad-hoc" forniti dalla classe della commodity specifica.

---

## 2. Modello Dati

### 2.1 Tabella `egis`
Verrà aggiunto/utilizzato un campo JSON per ospitare i dati strutturati.

| Campo | Tipo | Descrizione |
|-------|------|-------------|
| `commodity_type` | string (nullable) | Es. 'GoldBar', 'SilverBar'. Se null, non è una commodity. |
| `commodity_metadata` | json (nullable) | Contiene peso, unità, purezza, seriale manifattura, fixing price, ecc. |

### 2.2 Ruolo dei Traits
I Traits rimangono solo per caratteristiche accessorie o puramente descrittive (es. "Produttore: Argor-Heraeus"), ma non guidano più la logica di business (prezzo, peso).

---

## 3. Logica Applicativa (Backend)

### 3.1 Interfaccia `CommodityInterface`
Ogni commodity deve implementare un contratto standard:

```php
interface CommodityInterface {
    public function calculatePrice(): float; // Calcola prezzo basato su fixing * peso
    public function validateMetadata(array $data): bool;
    public function renderPanel(Egi $egi): string; // Restituisce HTML/View per il pannello gestione
    public function getSyncData(): array; // Restituisce dati da sincronizzare su EGI (price, commission)
}
```

### 3.2 Sincronizzazione Dati
Il sistema seguirà una logica di **Auto-Sync**:
1.  L'admin aggiorna i dati nel pannello Commodity (JSON).
2.  Al salvataggio, la classe specifica (es. `GoldCommodity`) calcola il nuovo prezzo.
3.  Il sistema sovrascrive automaticamente i campi `price` e `company_commission` della tabella `egis`.
    *   *Regola Aure*: Il JSON è la fonte di verità. Il campo `price` su DB è una cache per query veloci.

---

## 4. UI/UX: Pannelli Ad-Hoc

In `resources/views/egis/show.blade.php`, la visualizzazione sarà agnostica:

```blade
@if($egi->isCommodity())
    <div class="commodity-panel">
        {{ $egi->commodity()->renderPanel() }}
    </div>
@endif
```

La classe `GoldCommodity` renderizzerà, ad esempio, un pannello con:
*   Grafico andamento oro (opzionale).
*   Input per Peso e Purezza (se in edit).
*   Calcolatore live del valore.

---

## 5. REBIND POLICY – COMMODITY ASSETS

Come da direttive, il Rebind delle Commodity segue regole speciali.

### 5.1 Il Concetto
Il Rebind di una commodity è un'operazione tecnica di trasferimento proprietà, non artistica.

### 5.2 Regole Economiche
1.  **Fee Fissa**: Il costo del servizio è **50 Egili** fissi (≈ 0,50€).
2.  **Zero Commissioni**: Nessuna % sul prezzo di vendita.
3.  **Zero Royalty**: Nessuna royalty a Master Owner o Creator originali.

### 5.3 Flusso di Pagamento (Payment Split)
Dato che il Rebind ha un prezzo (es. 50.000€ per il lingotto) + una Fee (50 Egili):
1.  **Addebito Fee**: Il sistema scala 50 Egili dal Wallet Utente (condizione bloccante).
2.  **Pagamento Oggetto**: L'utente paga l'importo in Euro tramite i canali standard (Stripe/Bonifico).
3.  **Settlement**:
    *   Il Venditore riceve il 100% dell'importo Euro.
    *   La Piattaforma incassa/brucia i 50 Egili di fee.

---


---

## 6. MINT POLICY – COMMODITY ASSETS (Primary Market)

A differenza degli EGI standard dove la fee piattaforma (es. 10%) si applica sul prezzo totale, per le Commodity vige una regola diversa basata sul **Margine**.

### 6.1 Calcolo del Prezzo e della Fee
Il prezzo finale al pubblico è composto da:
1.  **Costo Materia Prima**: (es. Fixing Oro * Peso).
2.  **Margine Company**: Il guadagno lordo definito dall'azienda (markup).
3.  **Prezzo Totale** = Costo Materia + Margine.

### 6.2 Regola della Commissione
La **Platform Fee (10%)** si calcola **SOLO sul Margine Company**, non sul prezzo totale.

**Esempio:**
*   Valore Oro: 1.000€
*   Margine Company: 100€
*   **Prezzo Vendita**: 1.100€
*   **Fee Piattaforma**: 10% di 100€ = **10€** (non 110€).

### 6.3 Implementazione Tecnica
La classe Commodity (es. `GoldCommodity`) è responsabile di questo calcolo nel metodo `getSyncData()`.
*   Il campo `price` su DB sarà 1.100€.
*   Il campo `company_commission` su DB sarà 10€.

### 6.4 Compatibilità Master & Clone
Questa logica si applica identicamente sia ai **Master** che ai **Clones** (Direct Mint).
*   **Master**: Definisce il prezzo e il costo base.
*   **Clone**: Eredita il `commodity_base_value` (Costo) dal Master al momento del mint.
    *   Il calcolo della fee avviene *sempre* sul Margine (Prezzo Clone - Costo Base Clone).
    *   Questo garantisce che la fee piattaforme non eroda mai il capitale necessario a coprire il costo della materia prima (es. acquisto oro fisico), anche per vendite massive di copie.

---

## 7. Piano di Migrazione

1.  Creazione Migration: `commodity_type`, `commodity_metadata` su `egis`.
2.  Refactoring: Creazione classi `App\Commodities\GoldCommodity`.
3.  Migrazione Dati: Script per spostare i dati dagli attuali Traits al nuovo JSON per i GoldBar esistenti.
4.  UI Update: Implementazione pannelli dinamici.
5.  Logic Update: Implementazione calcolo Fee su Margine (Mint) e Fee 50 Egili (Rebind).

---
