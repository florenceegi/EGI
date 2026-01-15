# Commodity Management – Technical Reference

## 1. Overview
Il modulo **Commodity Management** estende la piattaforma FlorenceEGI per gestire asset con valore estrinseco e quotazione di mercato in tempo reale (es. Lingotti d'Oro).
A differenza degli EGI artistici standard, il cui valore è soggettivo/collezionistico, le Commodity hanno un **valore oggettivo** legato al mercato delle materie prime (`XAU` per l'oro).

Data "Stato dell'Arte": 17 dicembre 2025
Versione: 1.0.0

---

## 2. Architettura Tecnica

### 2.1 Pattern: Strategy & Factory
Il sistema utilizza un **Strategy Pattern** per gestire diversi tipi di commodity senza sporcare il modello `Egi` principale.

- **Contract**: `App\Egi\Commodity\CommodityContract` define l'interfaccia standard (`validate`, `fields`, `calculateMetrics`).
- **Factory**: `App\Egi\Commodity\CommodityFactory` istanzia la classe corretta in base a `commodity_type`.
- **Implementazione**: `App\Egi\Commodity\GoldBarCommodity` gestisce la logica specifica per l'oro (campi `weight`, `purity`, `margin`).

### 2.2 Database Schema (`egis`)
L'integrazione è "NoSQL-like" all'interno della tabella relazionale `egis`:

- **`commodity_type`** (`string|nullable`): Identificatore del tipo (es. `goldbar`). Indicizza la strategia da caricare.
- **`commodity_metadata`** (`json|nullable`): Payload flessibile per i dati tecnici (peso, purezza, margine).
  ```json
  {
    "weight": 100,
    "unit": "Grams",
    "purity": "999",
    "markup": 5.0,
    "margin_fixed": 0
  }
  ```
- **Observer**: `App\Observers\EgiCommodityObserver` sincronizza i tratti (`EgiTrait`) basandosi sul JSON, mantenendo i dati ricercabili via trait system ma masterizzati nel JSON.

---

## 3. Pricing Engine (GoldPriceService)

Il prezzo delle Commodity non è statico ma dinamico.

### 3.1 GoldPriceService
Servizio dedicato (`App\Services\GoldPriceService`) che:
1.  **Fetch Quotazioni**: Interroga API esterne (`Gold-API.io`, `MetalPriceAPI`) per ottenere il prezzo spot XAU/EUR.
2.  **Caching Intelligente**: Cache Redis (TTL 6 ore) per minimizzare chiamate API e costi.
3.  **Calcolo Valore**:
    ```
    Valore = (Peso * Purezza * PrezzoSpot) + Margine
    ```
    Il margine può essere percentuale (`margin_percent`) o fisso (`margin_fixed`), definito nel `commodity_metadata`.

### 3.2 Aggiornamento Prezzo (Refresh)
L'aggiornamento del prezzo avviene in due modalità tramite `GoldPriceController`:

1.  **Minting (Gratuito)**:
    - Endpoint: `/api/gold-price/refresh-for-mint/{egiId}`
    - Triggerato dal Creator/Merchant prima del minting.
    - Aggiorna il prezzo in cache con validità breve (10 min) per bloccare il prezzo di vendita.
    - Sincronizza il campo `egis.price` con il valore calcolato.

2.  **Consultazione (a Pagamento - Egili)**:
    - Endpoint: `/api/gold-price/force-refresh`
    - Triggerato dall'utente per avere quotazione real-time fuori cache standard.
    - **Costo**: 1 Egili (dedotto via `EgiliService`).
    - **Throttling**: Max 3 refresh ogni 6 ore per utente.

---

## 4. Minting & Fee Distribution Policy

La logica di distribuzione dei pagamenti per le Commodity differisce dallo standard EGI per riflettere la natura dell'asset (valore intrinseco vs valore artistico).

### 4.1 Fee su Margine (Phase 2 Logic)
Durante il Minting (Primary Market), la **Fee di Piattaforma** non viene calcolata sul prezzo totale dell'asset, ma esclusivamente sul **Margine (Markup)** applicato dal Venditore.

**Formula:**
1.  `GoldBaseValue` = Prezzo Spot x Peso x Purezza
2.  `Margin` = Prezzo Totale - GoldBaseValue
3.  `PlatformFee` = `Margin` * %Fee (es. 10%)

**Implementazione Tecnica:**
-   **Service**: `App\Services\PaymentDistributionService`
-   **Metodo**: `recordMintDistribution` (Phase 2)
-   **Logica**:
    1.  Il `GoldBaseValue` viene estratto dai metadati della transazione (`egi_blockchain.metadata`).
    2.  Viene creato un record di distribuzione "Rimborso Costi" (`gold_cost_reimbursement`) pari al `GoldBaseValue`, assegnato al Creator/Seller (esente da fee).
    3.  Il valore rimanente (`Margin`) viene utilizzato come base imponibile per il calcolo delle Royalty standard e della Fee Piattaforma.

### 4.2 Fallback e Resilienza
Per garantire il completamento delle transazioni critiche (Minting), sono state introdotte logiche di fallback:

#### A. Stripe Account Resolution
In scenari in cui il Wallet del venditore non è associato direttamente a un Utente (es. wallet tecnici o disallineati):
-   Il `MerchantAccountResolver` risale al **Proprietario della Collezione**.
-   Utilizza lo `stripe_account_id` del Proprietario come fallback certificato.

#### B. Hot Refresh Prezzo (Just-In-Time)
Se la cache del prezzo oro scade (10 min) validità) durante il flow di pagamento:
-   Il sistema **non blocca** la transazione.
-   Esegue un **Hot Refresh** immediato (ricalcolo live) recuperando il prezzo spot aggiornato.
-   Permette il completamento del minting con il dato più fresco disponibile.

---

## 5. Rebind Policy (Mercato Secondario)

Le Commodity seguono una logica di ridistribuzione valore specifica definita nella Policy, distintiva rispetto agli EGI Standard.

### 5.1 Logica Definita (Policy Target)
Da specifiche funzionali, la gestione Rebind Commodity prevede:
- **Fee Fissa**: 50 Egili (pagata dal venditore/scalata dal prezzo).
- **No Royalties Percentuali**: Il Creatore e la Piattaforma non prendono % sul valore intrinseco dell'oro.
- **Split**: Il Seller incassa il 100% del valore mercato - 50 Egili fissi.

### 4.2 Stato Implementazione Corrente
L'attuale `RebindController` implementa la logica **Standard EGI** (Royalty percentuali configurabili via `config/egi.php`).

> **Nota Tecnica**: Per attivare la Policy Commodity Rebind (Fee Fissa 50 Egili), è necessario implementare un intercettore nel `RebindController` o una strategia dedicata in `PaymentDistribution` che:
> 1. Verifichi `$egi->isGoldBar()`.
> 2. Bypass il calcolo percentuale standard.
> 3. Applichi la detrazione fissa di 50 Egili.
>
> *Allo stato attuale (Codebase v3.0), le Commodity utilizzano il flusso Rebind standard se non diversamente configurato tramite override dei wallet a 0% e fee piattaforma ad-hoc.*

---

## 6. Front-End & UX

- **Badge Distintivi**: Gestiti da `Egi::getCategoryBadgeClassesAttribute` (Palette Oro/Ambra per `goldbar`).
- **Nomenclatura Localizzata**: Override in `Egi::getCategoryNameAttribute` per mostrare "Lingotto d'Oro" (i18n).
- **Visual**: Componenti Blade specifici `x-gold-bar-info` per mostrare peso/purezza/valore live.

## 7. Conclusioni
La gestione Commodity è pienamente operativa per:
- **Creazione/Gestione** (CRUD + JSON Metadata).
- **Quotazione** (Live Pricing + Caching + API Esterne).
- **Monetizzazione Servizio** (Refresh a pagamento in Egili).

*Prossimi Passi (Roadmap tecnica):* Implementazione strict della logica "Fixed Fee Rebind" nel controller di pagamento.
