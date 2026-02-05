# Technical Debt - Platform Issues

## 🔴 Ultra Translation Manager - Dynamic Parameter Caching Issue

**Status**: Active Issue (Workaround Implemented)  
**Priority**: Medium (affects user experience)  
**Created**: 2026-02-05  
**Component**: Ultra Translation Manager (UTM)

### Description
Ultra Translation Manager caches translated strings aggressively to improve performance. However, when translations use dynamic parameters (`__('key', ['param' => $value])`), UTM may cache the translation with the **first user's data** and serve the same cached string to ALL subsequent users.

### Real-World Example
```php
// Translation file:
'greeting' => 'Ciao :name! Sono qui per aiutarti.'

// Blade view:
{{ __('ai_sidebar.greeting', ['name' => $user->name]) }}

// BUG: First user "Mario" logs in → cached as "Ciao Mario! Sono qui..."
// Then user "Luigi" logs in → sees "Ciao Mario! Sono qui..." (WRONG!)
```

### Impact
- **Data Leakage**: Users see other users' names/data in translated strings
- **Personalization Broken**: User-specific greetings show wrong names
- **Cache Invalidation Required**: Need frequent cache clears to fix
- **Affects All Dynamic Translations**: Not limited to specific components

### Root Cause
UTM's caching strategy doesn't account for dynamic parameter variations. The cache key is based on:
- Translation key (`ai_sidebar.greeting`)
- Locale (`it`, `en`, etc.)

But NOT on parameter values (`name => Mario` vs `name => Luigi`).

### Current Workaround: Atomic Translations
Split translations into static parts + Blade variables:

```php
// Translation file (ATOMIC):
'greeting' => 'Ciao',
'greeting_suffix' => '! Sono qui per aiutarti.',

// Blade view:
{{ __('ai_sidebar.greeting') }} {{ $user->name }}{{ __('ai_sidebar.greeting_suffix') }}
// Result: "Ciao" + MARIO + "! Sono qui..." (always fresh)
```

**Pros**:
- ✅ Works reliably with UTM caching
- ✅ No cache invalidation needed
- ✅ Performance maintained

**Cons**:
- ❌ More verbose translation keys
- ❌ Harder to maintain (split sentences)
- ❌ Less natural for translators

### Proper Solution (TODO)
Investigate and implement ONE of these approaches:

#### Option 1: UTM Cache Key Enhancement
Modify UTM to include parameter values in cache key:
```php
// Current cache key:
$cacheKey = "utm.{$locale}.{$key}";

// Proposed cache key:
$cacheKey = "utm.{$locale}.{$key}." . md5(json_encode($parameters));
```

**Pros**: Minimal code changes, preserves current translation syntax  
**Cons**: May increase cache size, requires UTM core modification

#### Option 2: Disable Caching for Dynamic Translations
Add flag to bypass cache for specific translations:
```php
__('ai_sidebar.greeting', ['name' => $user->name], false); // No cache
```

**Pros**: Granular control, backward compatible  
**Cons**: Performance impact on frequently used translations

#### Option 3: Per-User Translation Cache
Use Laravel's tagged cache with user ID:
```php
Cache::tags(["translations", "user:{$userId}"])->remember(...)
```

**Pros**: Correct caching per user, fast lookups  
**Cons**: Significant cache memory usage in multi-tenant environment

### Investigation Tasks
1. [ ] Review UTM source code to understand caching implementation
2. [ ] Benchmark performance impact of proposed solutions
3. [ ] Test cache invalidation strategies
4. [ ] Discuss with UTM maintainers (if external package)
5. [ ] Create POC for preferred solution

### Affected Areas
- AI Sidebar (fixed with atomic translations)
- User greetings/personalization across platform
- Dynamic notification messages
- Any user-specific translated content

### Notes
This issue was discovered during AI Sidebar implementation (2026-02-05) when user "Fabio" saw greeting for previous user "CamiciaSmartì". Atomic translation workaround successfully deployed across all 6 languages.

**CRITICAL**: Always use atomic translations for user-specific data until proper UTM fix is implemented.

---

## 🔴 Push Notifications (Non-Critical)

**Status**: Not Implemented  
**Priority**: Low (deferred)  
**Created**: 2026-02-04

### Description
Le notifiche push (browser/mobile) non sono attualmente implementate nel sistema V3. Il sistema attuale supporta solo notifiche in-app (database) visualizzate tramite il Notification Center.

### Impact
- Gli utenti devono accedere manualmente al Notification Center per vedere le nuove notifiche
- Nessun alert real-time quando l'utente non è attivamente sulla piattaforma
- Possibile ritardo nella risposta a notifiche urgenti (es. vendite, spedizioni)

### Technical Context
Il sistema V3 è già predisposto per l'integrazione di canali aggiuntivi:
- `CustomDatabaseChannel` è modulare e separato dalla logica di business
- Le classi Notification supportano il metodo `via()` per specificare canali multipli
- Laravel supporta nativamente broadcast notifications via Pusher/Echo

### Proposed Solution (Future)
1. Implementare `BroadcastChannel` per notifiche real-time via WebSocket
2. Integrare servizio push (Firebase Cloud Messaging per mobile, Web Push API per browser)
3. Aggiungere preferenze utente per gestire canali di notifica
4. Implementare queue worker dedicato per push notifications

### Workaround Attuale
Gli utenti ricevono notifiche via:
- Email (per eventi critici come vendite/spedizioni)
- In-app notification center (richiede refresh manuale o polling)
- Badge counter nel menu principale (aggiornato via Livewire)

### Notes
Questo debito tecnico è stato documentato su richiesta esplicita dell'utente per dare priorità allo sviluppo di nuove feature. Non è considerato bloccante per il rilascio in produzione.

---

*Documentazione creata da Antigravity (AI Partner OS3.0) per il progetto FlorenceEGI*  
*Versione 1.0 - Creata il 04 Febbraio 2026*
