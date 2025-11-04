# AI Feature Orchestration - OS3 Architecture

**Version**: 1.0.0  
**Date**: 2025-11-04  
**Author**: Padmin D. Curtis (AI Partner OS3.0)  
**Pattern**: Strategy + Factory  

---

## 🎯 OVERVIEW

Sistema unificato per gestire tutte le feature AI di FlorenceEGI con:
- ✅ **Pricing automatico** da DB (`ai_feature_pricing`)
- ✅ **Credits check** (TODO: integrazione EgiliService)
- ✅ **Validation** per ogni feature
- ✅ **GDPR audit trail** automatico
- ✅ **UEM/ULM** error handling centralizzato
- ✅ **Type-safe** con Interface + DTO

---

## 📁 STRUTTURA

```
app/Services/AI/Features/
├── Contracts/
│   └── AiFeatureInterface.php          # Interface comune
├── DTOs/
│   └── AiFeatureResult.php             # Response DTO
├── Handlers/
│   ├── TraitGenerationHandler.php      # AI Traits
│   ├── DescriptionGenerationHandler.php # AI Description
│   └── CollectionStrategyHandler.php   # AI Collection (placeholder)
├── AiFeatureFactory.php                # Factory Pattern
└── AiFeatureOrchestrator.php          # Orchestrator (Entry Point)

app/Http/Controllers/AI/
└── AiFeatureController.php             # REST Controller
```

---

## 🔄 WORKFLOW

```
┌────────────────────────────────────────────────────────┐
│  FRONTEND                                              │
│  POST /api/ai/features/execute                         │
│  {feature_code, egi_id, params}                        │
└─────────────────────┬──────────────────────────────────┘
                      │
                      ▼
┌────────────────────────────────────────────────────────┐
│  AiFeatureController                                   │
│  - Validate request                                    │
│  - Extract user_id                                     │
└─────────────────────┬──────────────────────────────────┘
                      │
                      ▼
┌────────────────────────────────────────────────────────┐
│  AiFeatureOrchestrator (CORE)                          │
│  1. Check ai_feature_pricing (exists? active?)         │
│  2. Check user credits (TODO: EgiliService)            │
│  3. Load EGI + User                                    │
│  4. Factory → create handler                           │
│  5. Handler → validate()                               │
│  6. Handler → execute()                                │
│  7. GDPR audit trail                                   │
└─────────────────────┬──────────────────────────────────┘
                      │
                      ▼
┌────────────────────────────────────────────────────────┐
│  AiFeatureFactory                                      │
│  Map: feature_code → Handler Class                     │
│  - ai_trait_generation → TraitGenerationHandler        │
│  - ai_description_generation → DescriptionHandler      │
│  - ai_collection_strategy → CollectionHandler          │
└─────────────────────┬──────────────────────────────────┘
                      │
                      ▼
┌────────────────────────────────────────────────────────┐
│  Handler (implements AiFeatureInterface)               │
│  - validate(egi, user, params): bool                   │
│  - execute(egi, user, params): AiFeatureResult         │
│  - getValidationError(): ?string                       │
└─────────────────────┬──────────────────────────────────┘
                      │
                      ▼
┌────────────────────────────────────────────────────────┐
│  Existing Service (wrapping)                           │
│  - AiTraitGenerationService                            │
│  - EgiPreMintManagementService                         │
│  - AnthropicService (future)                           │
└────────────────────────────────────────────────────────┘
```

---

## 🚀 USAGE

### Frontend Call

```javascript
// Vanilla JS
async function callAIFeature(featureCode, egiId, params = {}) {
    const response = await fetch('/api/ai/features/execute', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            feature_code: featureCode,
            egi_id: egiId,
            params: params
        })
    });

    return await response.json();
}

// Example: Generate traits
const result = await callAIFeature('ai_trait_generation', 123, {
    requested_count: 5
});

// Example: Generate description
const result = await callAIFeature('ai_description_generation', 123, {
    guidelines: 'Enfatizza i colori vivaci'
});
```

### Response Format

```json
{
    "success": true,
    "message": "AI trait generation completed. 5 traits proposed.",
    "data": {
        "generation_id": 42,
        "proposed_count": 5,
        "status": "analyzed"
    },
    "feature_code": "ai_trait_generation",
    "egi_id": 123,
    "user_id": 456,
    "metadata": {
        "requested_count": 5,
        "actual_proposed": 5
    }
}
```

---

## 🛠️ COME AGGIUNGERE UNA NUOVA FEATURE

### 1. Aggiungi in `ai_feature_pricing` seeder

```php
// database/seeders/AiFeaturePricingSeederV2Real.php
[
    'feature_code' => 'ai_my_new_feature',
    'feature_name' => 'My New AI Feature',
    'feature_description' => '...',
    'feature_category' => 'ai_services',
    'cost_egili' => 100,
    'feature_type' => 'consumable',
    'cost_per_use' => 100,
    'is_active' => true,
    // ...
],
```

### 2. Crea Handler

```php
// app/Services/AI/Features/Handlers/MyNewFeatureHandler.php
<?php

namespace App\Services\AI\Features\Handlers;

use App\Models\Egi;
use App\Models\User;
use App\Services\AI\Features\Contracts\AiFeatureInterface;
use App\Services\AI\Features\DTOs\AiFeatureResult;

class MyNewFeatureHandler implements AiFeatureInterface
{
    private ?string $validationError = null;

    public function __construct(
        private MyService $myService
    ) {}

    public function getFeatureCode(): string
    {
        return 'ai_my_new_feature';
    }

    public function validate(Egi $egi, User $user, array $params): bool
    {
        // Validation logic
        if ($egi->user_id !== $user->id) {
            $this->validationError = 'Not authorized';
            return false;
        }

        return true;
    }

    public function execute(Egi $egi, User $user, array $params): AiFeatureResult
    {
        try {
            // Call your service
            $result = $this->myService->doSomething($egi, $params);

            return AiFeatureResult::success(
                message: 'Feature executed successfully',
                data: $result,
                featureCode: $this->getFeatureCode(),
                egiId: $egi->id,
                userId: $user->id
            );
        } catch (\Exception $e) {
            return AiFeatureResult::failure(
                message: 'Execution failed: ' . $e->getMessage(),
                featureCode: $this->getFeatureCode(),
                egiId: $egi->id,
                userId: $user->id
            );
        }
    }

    public function getValidationError(): ?string
    {
        return $this->validationError;
    }
}
```

### 3. Registra in Factory

```php
// app/Services/AI/Features/AiFeatureFactory.php
private const FEATURE_HANDLERS = [
    'ai_trait_generation' => TraitGenerationHandler::class,
    'ai_description_generation' => DescriptionGenerationHandler::class,
    'ai_my_new_feature' => MyNewFeatureHandler::class, // ← ADD HERE
];
```

### 4. DONE! ✅

Il sistema è pronto. Il tuo handler sarà automaticamente:
- ✅ Controllato per pricing
- ✅ Controllato per credits
- ✅ Validato
- ✅ Loggato con ULM
- ✅ Audit GDPR
- ✅ Error handling UEM

---

## 🔧 FEATURE CODES DISPONIBILI

| Feature Code | Handler | Status | Service |
|-------------|---------|--------|---------|
| `ai_trait_generation` | TraitGenerationHandler | ✅ READY | AiTraitGenerationService |
| `ai_description_generation` | DescriptionGenerationHandler | ✅ READY | EgiPreMintManagementService |
| `ai_collection_strategy` | CollectionStrategyHandler | ⏳ PLACEHOLDER | - |

---

## 📝 TODO

1. ✅ Interface + DTO
2. ✅ Factory + Orchestrator
3. ✅ TraitGenerationHandler
4. ✅ DescriptionGenerationHandler
5. ✅ Controller + Route
6. ⏳ Traduzioni error code (file bloccato)
7. ⏳ Integrazione EgiliService per credits check
8. ⏳ Aggiungere `ai_description_generation` al seeder pricing
9. ⏳ Update frontend per usare route unificata
10. ⏳ CollectionStrategyHandler implementation

---

## 🎓 PATTERN APPLICATI

### Strategy Pattern
Ogni handler implementa la stessa interface ma con logica diversa.

### Factory Pattern
Factory crea il giusto handler basandosi sul `feature_code`.

### Dependency Injection
Laravel DI risolve automaticamente le dipendenze dei costruttori.

### DTO (Data Transfer Object)
`AiFeatureResult` è un DTO typed per response consistenti.

### Single Responsibility
Ogni handler fa UNA cosa sola.

### Open/Closed Principle
Aggiungi feature senza modificare orchestrator.

---

## 🔒 SECURITY & GDPR

- ✅ **Authentication**: Middleware `auth` su route
- ✅ **Authorization**: Validation in ogni handler (es: owner check)
- ✅ **Audit Trail**: Automatico via `AuditLogService`
- ✅ **GDPR Category**: `GdprActivityCategory::AI_INTERACTION`
- ✅ **Error Handling**: UEM con notifiche automatiche
- ✅ **Logging**: ULM per tutti gli step

---

## 📞 SUPPORT

Per domande sull'architettura:
- 📖 Leggi questo documento
- 🔍 Studia i 3 handler esistenti come esempi
- 💻 Segui il pattern per nuove feature
- ✅ Testa sempre con handler mock prima di production

**Remember**: Questo è un sistema **OS3-compliant**, segui i pattern! 🚀

