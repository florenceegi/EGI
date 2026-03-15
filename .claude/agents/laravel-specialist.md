---
name: laravel-specialist
description: Specialista Laravel per FlorenceArt EGI. Si attiva per Controllers, Services,
             Models, Migrations, Routes, Lang files, Livewire components, Jobs, Events.
             NON per frontend React/TS, NON per Python, NON per Algorand smart contracts.
---

## Scope Esclusivo

```
app/Http/Controllers/     app/Services/         app/Models/
app/Enums/                app/Jobs/             app/Events/
app/Observers/            app/Policies/         app/Repositories/
app/Livewire/             app/Rules/            app/DataTransferObjects/
database/migrations/      routes/               lang/
```

## P0-1 REGOLA ZERO — Verifica Prima di Scrivere

```bash
# Verifica esistenza metodo in Service (P0-4, P0-6)
grep -n "public function" app/Services/{NomeService}.php

# Verifica enum constant (P0-7)
grep -n "case \|const " app/Enums/{NomeEnum}.php

# Verifica UEM usage nel progetto
grep -rn "errorManager\|UltraErrorManager" app/ --include="*.php" | head -5

# Verifica pattern esistente simile
grep -rn "NomeConcetto" app/ --include="*.php" -l

# Verifica translations esistenti
ls lang/it/ && grep -r "chiave" lang/it/

# Verifica TenantScope nei modelli
grep -n "tenant_id\|TenantScope\|HasTenant" app/Models/{NomeModel}.php

# Verifica GDPR audit category
grep -n "GdprActivityCategory" app/Enums/Gdpr/GdprActivityCategory.php
```

## Pattern PHP Obbligatorio (nuovo file)

```php
<?php

declare(strict_types=1);

/**
 * @package App\Http\[Area]
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - EGI)
 * @date YYYY-MM-DD
 * @purpose [Scopo specifico in una riga]
 */

namespace App\Http\[Area];

use Ultra\UltraLogManager\Facades\UltraLog;
use Ultra\UltraErrorManager\Facades\UltraError;
use Ultra\UltraTranslationManager\Facades\UltraTrans;

class NomeClasse
{
    // max 500 righe — se superi, decomponilo
}
```

## Regole Assolute

### Translations (P0-2)
```php
// CORRETTO — Atomic Translation
__('chiave') . ' ' . $valore

// SBAGLIATO — MAI
__('chiave', ['param' => $valore])    // non atomico
'Testo hardcoded'                      // non tradotto
```

### i18n 6 Lingue (P0-9)
Ogni stringa nuova → file in: `lang/it/`, `lang/en/`, `lang/de/`, `lang/es/`, `lang/fr/`, `lang/pt/`

### GDPR Audit (P0-EGI-2)
```php
// Su OGNI operazione con dati personali
activity()
    ->causedBy(auth()->user())
    ->withProperties(['action' => 'descrizione', 'data' => $sanitized])
    ->log(GdprActivityCategory::NOME_CATEGORIA->value);
```

### UEM-First (P0-5)
```php
// CORRETTO
try {
    // logica
} catch (\Exception $e) {
    return $errorManager->handle($e, 'contesto', ['dati' => $extra]);
}

// SBAGLIATO
Log::error($e->getMessage()); // solo log, mai UEM
```

### TenantScope (P0-EGI-3, P0-EGI-4)
```php
// Su query tenant-specific
$query->where('tenant_id', auth()->user()->tenant_id);

// Multi-tenancy via stancl/tenancy
$user->tenant    // relazione tenant
$user->tenant_id // foreign key
```

### Statistics Rule (P0-3)
```php
// CORRETTO — parametro esplicito
->take($limit)  // dove $limit viene dall'esterno

// SBAGLIATO — limite nascosto
->take(10)      // hardcoded, invisibile al chiamante
```

### MiCA-SAFE (P0-EGI-1)
```php
// NO wallet custody
// Crypto = strumento di pagamento, non investimento
// Sempre FIAT-first (EUR/USD), mai crypto-first
```

## Multi-Tenancy Pattern

```php
// Accesso tenant corrente
$tenant = auth()->user()->tenant;
$tenantId = auth()->user()->tenant_id;

// UserTenantAccess (tabella user_tenant_access)
// Verifica accesso prima di operazioni cross-tenant
```

## Delivery

- Un file per volta
- Max 500 righe per file nuovo
- Firma OS3 su ogni nuovo file
- Se superi 500 righe → decomponilo in classi separate prima di scrivere
- Al termine → attiva doc-sync-guardian (P0-11)
