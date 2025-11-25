# 🎓 Natan Tutor - Assistente Operativo di Piattaforma

**Versione:** 1.0.0  
**Data:** 2025-11-25  
**Autore:** Padmin D. Curtis (AI Partner OS3.0)  
**Status:** DESIGN PHASE

---

## 📋 Indice

1. [Visione e Obiettivi](#1-visione-e-obiettivi)
2. [Architettura](#2-architettura)
3. [Modalità Operative](#3-modalità-operative)
4. [Azioni Eseguibili](#4-azioni-eseguibili)
5. [Sistema Costi Egili](#5-sistema-costi-egili)
6. [Gift Iniziale Nuovi Utenti](#6-gift-iniziale-nuovi-utenti)
7. [UI/UX Flow](#7-uiux-flow)
8. [Implementazione Tecnica](#8-implementazione-tecnica)

---

## 1. Visione e Obiettivi

### 🎯 Mission

**Natan Tutor** è l'evoluzione di Natan Assistant: da assistente informativo a **assistente operativo** che può **eseguire azioni concrete** per conto dell'utente.

### 🎭 Due Anime, Un Assistente

| Aspetto      | Natan Informativo (esistente) | Natan Tutor (nuovo)              |
| ------------ | ----------------------------- | -------------------------------- |
| **Funzione** | Spiega, guida, informa        | Esegue azioni, opera             |
| **Costo**    | Gratuito                      | Consuma Egili                    |
| **Target**   | Tutti gli utenti              | Utenti che preferiscono delegare |
| **Esempio**  | "Come funziona il mint?"      | "Minta questa opera per me"      |

### 💡 Principio Fondamentale

> **"Natan può FARE, non solo DIRE"**

L'utente esperto usa Natan come **shortcut** (risparmia tempo).  
L'utente nuovo usa Natan come **tutor** (impara mentre Natan fa).

---

## 2. Architettura

```
┌─────────────────────────────────────────────────────────────┐
│                      NATAN TUTOR                             │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  ┌──────────────┐     ┌──────────────┐     ┌──────────────┐ │
│  │   CHAT UI    │────▶│  INTENT      │────▶│   ACTION     │ │
│  │  (esistente) │     │  PARSER      │     │   EXECUTOR   │ │
│  └──────────────┘     └──────────────┘     └──────────────┘ │
│         │                    │                    │          │
│         │                    ▼                    ▼          │
│         │           ┌──────────────┐     ┌──────────────┐   │
│         │           │   EGILI      │     │   SERVICE    │   │
│         │           │   CHECKER    │     │   LAYER      │   │
│         │           └──────────────┘     └──────────────┘   │
│         │                    │                    │          │
│         ▼                    ▼                    ▼          │
│  ┌──────────────────────────────────────────────────────┐   │
│  │              CONFIRMATION DIALOG                      │   │
│  │  "Vuoi che esegua [AZIONE] per [COSTO] Egili?"       │   │
│  │  [✅ Conferma] [⚙️ Modifica] [❌ Annulla]             │   │
│  └──────────────────────────────────────────────────────┘   │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

---

## 3. Modalità Operative

### 3.1 Modalità "Fai Tu" (Delegazione)

```
Utente: "Minta la mia opera Tramonto.jpg"

Natan: "Ho trovato 'Tramonto.jpg' nella tua collection 'Paesaggi'.

        Riepilogo operazione:
        • Opera: Tramonto.jpg
        • Collection: Paesaggi
        • Costo mint: 50 Egili (gas blockchain)
        • Costo servizio Natan: 10 Egili
        • Totale: 60 Egili

        Il tuo saldo: 150 Egili

        [✅ Procedi] [❌ Annulla]"

Utente: [✅ Procedi]

Natan: "✅ Mint completato!

        Il tuo EGI #4521 è ora on-chain.
        Transaction: ALGO-TX-ABC123...

        [👁️ Visualizza] [📤 Condividi] [🔄 Minta un'altra]"
```

### 3.2 Modalità "Guidami" (Tutorial Interattivo)

```
Utente: "Voglio imparare a mintare"

Natan: "Perfetto! Ti guido passo passo.

        **STEP 1/4: Scegli l'opera**

        Clicca sull'opera che vuoi mintare.
        Ti aspetto... 👀

        💡 Suggerimento: Devi prima aver caricato un'opera nella tua collection."

[Utente clicca su un'opera]

Natan: "Ottimo! Hai selezionato 'Tramonto.jpg'.

        **STEP 2/4: Verifica i dettagli**

        Prima del mint, verifica:
        ☑️ Titolo corretto
        ☑️ Descrizione presente
        ☑️ Prezzo impostato

        Tutto ok? [Sì, continua] [No, devo modificare]"
```

### 3.3 Modalità "Faccio da Solo" (Minima Interferenza)

Natan appare solo:

-   Su errori critici
-   Su richiesta esplicita (icona Natan sempre visibile)
-   Per conferme operazioni irreversibili

---

## 4. Azioni Eseguibili

### 4.1 Azioni Creator

| Azione                    | Comando Esempio                           | Costo Egili |
| ------------------------- | ----------------------------------------- | ----------- |
| **Mint Opera**            | "Minta questa opera"                      | 10          |
| **Crea Collection**       | "Crea una collection chiamata X"          | 5           |
| **Imposta Prezzo**        | "Imposta prezzo a 100€"                   | 2           |
| **Pubblica Opera**        | "Pubblica questa opera"                   | 2           |
| **Genera Descrizione AI** | "Scrivi una descrizione per questa opera" | 15          |
| **Genera Tags AI**        | "Suggerisci tag per questa opera"         | 5           |

### 4.2 Azioni Collector

| Azione                   | Comando Esempio               | Costo Egili           |
| ------------------------ | ----------------------------- | --------------------- |
| **Prenota EGI**          | "Prenota questa opera"        | 5                     |
| **Acquista Egili**       | "Compra 1000 Egili"           | 0 (redirect checkout) |
| **Annulla Prenotazione** | "Annulla la mia prenotazione" | 0                     |

### 4.3 Azioni Navigazione

| Azione               | Comando Esempio           | Costo Egili |
| -------------------- | ------------------------- | ----------- |
| **Vai a...**         | "Portami alle mie opere"  | 0           |
| **Cerca**            | "Cerca opere di Monet"    | 0           |
| **Mostra Dashboard** | "Mostra la mia dashboard" | 0           |

### 4.4 Azioni Informatiche (Gratuite)

| Azione     | Comando Esempio                 | Costo Egili |
| ---------- | ------------------------------- | ----------- |
| **Spiega** | "Cos'è il mint?"                | 0           |
| **Guida**  | "Come funzionano le royalties?" | 0           |
| **FAQ**    | "Quali wallet sono supportati?" | 0           |

---

## 5. Sistema Costi Egili

### 5.1 Listino Natan Tutor

```php
// config/natan-tutor.php

return [
    'pricing' => [
        // === AZIONI CREATOR ===
        'action_mint' => [
            'code' => 'natan_action_mint',
            'name' => 'Mint Opera via Natan',
            'cost_egili' => 10,
            'category' => 'creator_actions',
        ],
        'action_create_collection' => [
            'code' => 'natan_action_create_collection',
            'name' => 'Crea Collection via Natan',
            'cost_egili' => 5,
            'category' => 'creator_actions',
        ],
        'action_set_price' => [
            'code' => 'natan_action_set_price',
            'name' => 'Imposta Prezzo via Natan',
            'cost_egili' => 2,
            'category' => 'creator_actions',
        ],
        'action_publish' => [
            'code' => 'natan_action_publish',
            'name' => 'Pubblica Opera via Natan',
            'cost_egili' => 2,
            'category' => 'creator_actions',
        ],
        'action_ai_description' => [
            'code' => 'natan_action_ai_description',
            'name' => 'Genera Descrizione AI',
            'cost_egili' => 15,
            'category' => 'ai_services',
        ],
        'action_ai_tags' => [
            'code' => 'natan_action_ai_tags',
            'name' => 'Genera Tags AI',
            'cost_egili' => 5,
            'category' => 'ai_services',
        ],

        // === AZIONI COLLECTOR ===
        'action_reserve' => [
            'code' => 'natan_action_reserve',
            'name' => 'Prenota EGI via Natan',
            'cost_egili' => 5,
            'category' => 'collector_actions',
        ],

        // === TUTORING ===
        'guided_tutorial' => [
            'code' => 'natan_guided_tutorial',
            'name' => 'Tutorial Guidato Interattivo',
            'cost_egili' => 20,
            'category' => 'tutoring',
            'description' => 'Sessione completa step-by-step con Natan',
        ],

        // === GRATUITI ===
        'navigation' => [
            'code' => 'natan_navigation',
            'name' => 'Navigazione Assistita',
            'cost_egili' => 0,
            'category' => 'free',
        ],
        'info' => [
            'code' => 'natan_info',
            'name' => 'Informazioni e FAQ',
            'cost_egili' => 0,
            'category' => 'free',
        ],
    ],
];
```

### 5.2 Logica di Addebito

```php
// Workflow addebito Egili per azione Natan

class NatanActionService
{
    public function executeAction(User $user, string $actionCode, array $params): ActionResult
    {
        // 1. Verifica costo
        $cost = $this->getActionCost($actionCode);

        // 2. Verifica saldo
        if (!$this->egiliService->canSpend($user, $cost)) {
            return ActionResult::insufficientBalance($cost, $user->egili_balance);
        }

        // 3. Richiedi conferma UI
        // (gestito lato frontend)

        // 4. Esegui azione
        $result = $this->executeActionInternal($actionCode, $params);

        // 5. Addebita Egili SOLO se successo
        if ($result->isSuccess()) {
            $this->egiliService->spend(
                user: $user,
                amount: $cost,
                description: "Natan Tutor: {$actionCode}",
                metadata: [
                    'action' => $actionCode,
                    'params' => $params,
                    'result' => $result->toArray(),
                ]
            );
        }

        return $result;
    }
}
```

---

## 6. Gift Iniziale Nuovi Utenti

### 6.1 Pacchetto Welcome

```php
// Alla registrazione utente

'welcome_gift' => [
    'egili_amount' => 100,           // 100 Egili gratis
    'type' => 'gift',                 // Tipo Gift (scade)
    'expires_days' => 90,             // Scadenza 90 giorni
    'reason' => 'Welcome to FlorenceEGI!',
],
```

### 6.2 Cosa Può Fare con 100 Egili

| Azione                    | Costo | Quante Volte |
| ------------------------- | ----- | ------------ |
| Tutorial Guidato Completo | 20    | 5x           |
| Mint via Natan            | 10    | 10x          |
| Crea Collection           | 5     | 20x          |
| Prenota EGI               | 5     | 20x          |
| Imposta Prezzo            | 2     | 50x          |
| Genera Descrizione AI     | 15    | 6x           |

### 6.3 Messaggio di Benvenuto

```
🎉 Benvenuto su FlorenceEGI!

Ti abbiamo regalato 100 Egili per iniziare!

Con questi Egili puoi:
• Farti guidare da Natan in un tutorial completo
• Mintare le tue prime opere
• Esplorare tutte le funzionalità

I tuoi Egili Gift scadono tra 90 giorni.
Usali per scoprire la piattaforma! 🚀

[🎓 Inizia Tutorial con Natan] [🚀 Esplora da solo]
```

---

## 7. UI/UX Flow

### 7.1 Chat Natan con Conferma Azione

```
┌─────────────────────────────────────────────────┐
│  🎩 Natan Tutor                            ─ ✕ │
├─────────────────────────────────────────────────┤
│                                                 │
│  Tu: Minta la mia opera Tramonto               │
│                                                 │
│  ┌─────────────────────────────────────────┐   │
│  │ 🎩 Ho trovato la tua opera!             │   │
│  │                                          │   │
│  │ 📄 Tramonto.jpg                         │   │
│  │ 📁 Collection: Paesaggi                 │   │
│  │                                          │   │
│  │ ─────────────────────────────           │   │
│  │ 💰 Costo operazione:                    │   │
│  │    • Servizio Natan: 10 Egili           │   │
│  │    • Gas blockchain: incluso            │   │
│  │                                          │   │
│  │ 💳 Il tuo saldo: 150 Egili              │   │
│  │ ─────────────────────────────           │   │
│  │                                          │   │
│  │ [✅ Conferma Mint] [❌ Annulla]          │   │
│  └─────────────────────────────────────────┘   │
│                                                 │
├─────────────────────────────────────────────────┤
│  💬 Scrivi un messaggio...              [Invia] │
└─────────────────────────────────────────────────┘
```

### 7.2 Feedback Successo

```
┌─────────────────────────────────────────────────┐
│  ┌─────────────────────────────────────────┐   │
│  │ ✅ Mint Completato!                     │   │
│  │                                          │   │
│  │ 🎨 EGI #4521 è ora on-chain!           │   │
│  │                                          │   │
│  │ 📋 Transaction: ALGO-TX-ABC123...       │   │
│  │                                          │   │
│  │ 💰 Addebitati: 10 Egili                 │   │
│  │ 💳 Nuovo saldo: 140 Egili               │   │
│  │                                          │   │
│  │ [👁️ Vedi EGI] [📤 Condividi] [🔄 Altro] │   │
│  └─────────────────────────────────────────┘   │
└─────────────────────────────────────────────────┘
```

---

## 8. Implementazione Tecnica

### 8.1 Nuovi File da Creare

```
app/
├── Services/
│   └── NatanTutor/
│       ├── NatanTutorService.php        # Orchestratore principale
│       ├── IntentParser.php             # Parsing intenti utente
│       ├── ActionExecutor.php           # Esecuzione azioni
│       └── TutorialEngine.php           # Motore tutorial guidati
├── Http/
│   └── Controllers/
│       └── NatanTutorController.php     # API endpoints
└── Events/
    └── NatanActionCompleted.php         # Event per tracking

config/
└── natan-tutor.php                      # Configurazione e listino

resources/
├── ts/
│   └── components/
│       └── natan-tutor/
│           ├── natan-tutor.ts           # Componente principale
│           ├── action-confirmation.ts   # Dialog conferma
│           └── tutorial-overlay.ts      # Overlay tutorial
└── views/
    └── components/
        └── natan-tutor/
            ├── chat-panel.blade.php
            └── action-card.blade.php

database/
└── migrations/
    └── xxxx_create_natan_tutor_actions_table.php
```

### 8.2 Database Schema

```sql
-- Tracking azioni Natan Tutor
CREATE TABLE natan_tutor_actions (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    action_code VARCHAR(100) NOT NULL,
    action_params JSON,
    egili_cost INT NOT NULL DEFAULT 0,
    status ENUM('pending', 'confirmed', 'executing', 'completed', 'failed', 'cancelled'),
    result JSON,
    error_message TEXT,
    created_at TIMESTAMP,
    executed_at TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id),
    INDEX idx_user_actions (user_id, created_at),
    INDEX idx_action_code (action_code)
);
```

### 8.3 API Endpoints

```php
// routes/api.php

Route::prefix('natan-tutor')->middleware(['auth:sanctum'])->group(function () {
    // Intent parsing
    POST   /parse-intent          → parseUserIntent

    // Action execution
    POST   /actions/preview       → previewAction (mostra costo, richiede conferma)
    POST   /actions/execute       → executeAction (dopo conferma)
    POST   /actions/cancel        → cancelAction

    // Tutorial
    POST   /tutorial/start        → startGuidedTutorial
    POST   /tutorial/step         → nextTutorialStep
    POST   /tutorial/skip         → skipTutorial

    // History
    GET    /history               → getActionHistory
    GET    /stats                 → getUsageStats
});
```

---

## 📋 Next Steps

1. **Fase 1**: Creare `config/natan-tutor.php` con listino prezzi
2. **Fase 2**: Implementare `NatanTutorService` base
3. **Fase 3**: Aggiungere gift Egili al flusso registrazione
4. **Fase 4**: UI componente chat con conferma azione
5. **Fase 5**: Integrare con sistema Egili esistente
6. **Fase 6**: Tutorial engine per modalità guidata

---

_Documento generato per FlorenceEGI - OS3.0 Compliant_
