# FegiAuth - Sistema di Autenticazione Unificata

## 📋 Indice

1. [Panoramica del Sistema](#panoramica-del-sistema)
2. [Architettura e Componenti](#architettura-e-componenti)
3. [Tipi di Autenticazione](#tipi-di-autenticazione)
4. [API e Metodi Principali](#api-e-metodi-principali)
5. [Flussi di Autenticazione](#flussi-di-autenticazione)
6. [Sicurezza e Permessi](#sicurezza-e-permessi)
7. [Gestione degli Errori](#gestione-degli-errori)
8. [Esempi Pratici](#esempi-pratici)
9. [Troubleshooting](#troubleshooting)

---

## 🎯 Panoramica del Sistema

`FegiAuth` è un sistema di autenticazione unificata per la piattaforma FlorenceEGI che gestisce **due tipi di autenticazione**:

### **Strong Authentication (Autenticazione Forte)**

-   **Login tradizionale**: Email + Password
-   **Guard Laravel**: `web`
-   **Sessione completa**: Utente completamente autenticato
-   **Accesso**: Tutte le funzionalità della piattaforma

### **Weak Authentication (Autenticazione Debole)**

-   **FEGI Connect**: Connessione tramite wallet crypto
-   **Sessione limitata**: Dati utente in sessione PHP
-   **Accesso limitato**: Solo alcune funzionalità, upgrade disponibile

---

## 🏗️ Architettura e Componenti

### **Struttura del Sistema**

```
FegiAuth (Helper Class)
├── User Resolution (Risoluzione Utente)
├── Authentication Type Detection (Rilevamento Tipo Auth)
├── Permission System Integration (Integrazione Permessi)
├── Session Management (Gestione Sessione)
└── Security Utilities (Utilità di Sicurezza)
```

### **Componenti Chiave**

#### **1. Risoluzione Utente**

```php
public static function user(): ?User
```

-   **Priorità 1**: Controllo Strong Auth (`Auth::guard('web')->user()`)
-   **Priorità 2**: Controllo Weak Auth (sessione + database)
-   **Fallback**: `null` se nessuna autenticazione trovata

#### **2. Gestione Sessione Weak Auth**

```php
// Variabili di sessione per Weak Auth
session('auth_status')        // 'connected' | null
session('connected_user_id')  // ID utente | null
session('connected_wallet')   // Indirizzo wallet | null
```

#### **3. Integrazione Spatie Permissions**

-   Usa **Spatie Permission** per gestione ruoli e permessi
-   **Single Source of Truth** per controllo accessi
-   **Ruoli automatici** per utenti weak auth

---

## 🔐 Tipi di Autenticazione

### **Strong Authentication**

#### **Caratteristiche**

-   **Guard**: `web` (Laravel standard)
-   **Metodo**: Login con credenziali
-   **Sessione**: Completa e persistente
-   **Sicurezza**: Massima
-   **Scadenza**: Configurabile (remember me)

#### **Identificazione**

```php
FegiAuth::isStrongAuth()  // true se strong auth
FegiAuth::getAuthType()   // 'strong'
```

#### **Vantaggi**

-   Accesso completo a tutte le funzionalità
-   Nessuna limitazione di permessi
-   Gestione avanzata di profilo e dati
-   Operazioni sensibili (transazioni, modifiche importanti)

### **Weak Authentication**

#### **Caratteristiche**

-   **Meccanismo**: FEGI Connect (wallet)
-   **Sessione**: Limitata e temporanea
-   **Sicurezza**: Media
-   **Scopo**: Accesso rapido e semplificato

#### **Identificazione**

```php
FegiAuth::isWeakAuth()    // true se weak auth
FegiAuth::getAuthType()   // 'weak'
```

#### **Limitazioni**

-   Accesso limitato a certe funzionalità
-   Richiede upgrade per operazioni sensibili
-   Permessi ridotti
-   Sessione non persistente

#### **Vantaggi**

-   **Onboarding veloce**: Nessuna registrazione
-   **User Experience**: Accesso immediato con wallet
-   **Web3 Integration**: Nativamente compatibile con crypto

---

## 🛠️ API e Metodi Principali

### **Metodi di Base**

#### **Ottenere Utente Corrente**

```php
// Ottiene l'utente autenticato (forte o debole)
$user = FegiAuth::user();

// Ottiene l'ID utente
$userId = FegiAuth::id();

// Verifica se c'è un utente autenticato
$isAuthenticated = FegiAuth::check();
```

#### **Verifiche di Tipo Autenticazione**

```php
// Verifica tipo di autenticazione
$authType = FegiAuth::getAuthType(); // 'strong' | 'weak' | 'guest'

// Verifiche specifiche
$isStrong = FegiAuth::isStrongAuth();
$isWeak = FegiAuth::isWeakAuth();
$isGuest = FegiAuth::guest();
```

#### **Informazioni Utente**

```php
// Nome utente formattato
$userName = FegiAuth::getUserName();

// Messaggio di benvenuto personalizzato
$welcome = FegiAuth::getWelcomeMessage();

// Tipo di utente FEGI
$userType = FegiAuth::getFegiUserType(); // 'creator' | 'collector' | etc.

// Indirizzo wallet
$wallet = FegiAuth::getWallet();
```

### **Sistema di Permessi**

#### **Controllo Permessi**

```php
// Verifica se l'utente può eseguire un'azione
$canEdit = FegiAuth::can('edit_profile');

// Verifica permesso per utenti weak auth
$canWeakAuth = FegiAuth::canWeakAuth('view_collections');
```

#### **Gestione Ruoli**

```php
// Assegna ruolo a utente
$success = FegiAuth::assignRole($userId, 'creator');

// Verifica se utente ha ruolo
$hasRole = FegiAuth::hasRole('premium_user');
```

### **Gestione Sessione**

#### **Logout**

```php
// Logout completo (strong + weak)
FegiAuth::logout();

// Flush stato interno (development)
FegiAuth::flushState();
```

#### **Debug e Sviluppo**

```php
// Informazioni complete di debug
$debugInfo = FegiAuth::debugUserResolution();

// Route di debug (solo development)
// GET /test/fegiauth-debug
```

---

## 🔄 Flussi di Autenticazione

### **Flusso Strong Authentication**

```mermaid
graph TD
    A[Utente visita sito] --> B{Già loggato?}
    B -->|SI| C[FegiAuth::user() → Strong User]
    B -->|NO| D[Redirect a Login]
    D --> E[Login con Email/Password]
    E --> F[Auth::guard('web')->attempt()]
    F -->|SUCCESS| G[Sessione Laravel Attiva]
    G --> H[FegiAuth::isStrongAuth() = true]
    H --> I[Accesso Completo]
```

### **Flusso Weak Authentication**

```mermaid
graph TD
    A[Utente clicca FEGI Connect] --> B[Connessione Wallet]
    B --> C[Verifica Firma]
    C -->|SUCCESS| D[Trova/Crea User]
    D --> E[Set Session Variables]
    E --> F[session: auth_status = 'connected']
    F --> G[session: connected_user_id = $userId]
    G --> H[session: connected_wallet = $address]
    H --> I[FegiAuth::isWeakAuth() = true]
    I --> J[Accesso Limitato]
    J --> K{Vuole più funzionalità?}
    K -->|SI| L[Upgrade a Strong Auth]
    K -->|NO| M[Continua con Weak Auth]
```

### **Flusso di Upgrade**

```mermaid
graph TD
    A[Utente Weak Auth] --> B[Tenta accesso funzione avanzata]
    B --> C[FegiAuth::checkWeakAuthAccess()]
    C --> D{Ha permesso?}
    D -->|NO| E[redirectToUpgrade()]
    E --> F[Route: user.domains.upgrade]
    F --> G[Pagina Upgrade]
    G --> H[Completa Registrazione]
    H --> I[Strong Authentication]
    D -->|SI| J[Accesso Consentito]
```

---

## 🛡️ Sicurezza e Permessi

### **Livelli di Sicurezza**

#### **Guest (Nessuna Autenticazione)**

-   **Accesso**: Solo contenuti pubblici
-   **Operazioni**: Visualizzazione, navigazione
-   **Redirect**: Login per azioni che richiedono autenticazione

#### **Weak Authentication**

-   **Accesso**: Funzionalità base, profilo limitato
-   **Operazioni**: Visualizzazione collezioni, interazioni base
-   **Limitazioni**: No transazioni, no modifiche sensibili
-   **Upgrade**: Disponibile per accesso completo

#### **Strong Authentication**

-   **Accesso**: Tutte le funzionalità
-   **Operazioni**: Qualsiasi azione sulla piattaforma
-   **Sicurezza**: Verification aggiuntiva per operazioni critiche

### **Sistema di Permessi**

#### **Permessi Weak Auth**

```php
// Permessi tipici per utenti weak
$weakAuthPermissions = [
    'view_collections',
    'view_artworks',
    'basic_profile_access',
    'view_public_content'
];
```

#### **Permessi Strong Auth**

```php
// Tutti i permessi + operazioni avanzate
$strongAuthPermissions = [
    'edit_profile',
    'manage_collections',
    'financial_operations',
    'admin_functions', // se ruolo admin
    'data_export',
    'account_deletion'
];
```

### **Controlli di Accesso**

#### **BaseUserDomainController**

```php
protected function checkWeakAuthAccess(): bool|RedirectResponse {
    // 1. Controllo autenticazione base
    if (!FegiAuth::check()) {
        return $this->respondAuthRequired();
    }

    // 2. Strong auth → accesso immediato
    if (FegiAuth::isStrongAuth()) {
        return true;
    }

    // 3. Weak auth → controllo permessi specifici
    $permission = $this->getRequiredDomainPermission();
    if ($permission && !FegiAuth::can($permission)) {
        return $this->redirectToUpgrade();
    }

    return true;
}
```

---

## ⚠️ Gestione degli Errori

### **Errori Comuni**

#### **User ID Null**

-   **Causa**: Problemi di risoluzione utente
-   **Sintomo**: `FegiAuth::id()` ritorna `null`
-   **Soluzione**: Debug con `FegiAuth::debugUserResolution()`

#### **Route Not Found**

-   **Causa**: Route di upgrade non corrette
-   **Sintomo**: `Route [account.upgrade] not defined`
-   **Soluzione**: Usare `user.domains.upgrade`

#### **Permission Denied**

-   **Causa**: Utente weak senza permessi sufficienti
-   **Sintomo**: Redirect loop o accesso negato
-   **Soluzione**: Verificare `getRequiredDomainPermission()`

### **Debug e Logging**

#### **Route di Debug**

```php
// Solo in development
GET /test/fegiauth-debug

// Ritorna informazioni complete su:
// - Strong auth status
// - Weak auth session data
// - User resolution results
// - Error details
```

#### **Log Entries**

```php
// Esempi di log per debugging
[FEGI_AUTH_DEBUG] Starting user resolution
[FEGI_AUTH_DEBUG] Strong auth check {user_id: 11}
[FEGI_AUTH_DEBUG] Weak auth user resolved {user_id: 11}
[GDPR AUDIT] Data domain access {action: personal_data_view}
```

---

## 💡 Esempi Pratici

### **Esempio 1: Controllo Autenticazione Base**

```php
// In un controller
public function index() {
    if (!FegiAuth::check()) {
        return redirect()->route('login');
    }

    $user = FegiAuth::user();
    $authType = FegiAuth::getAuthType();

    return view('dashboard', compact('user', 'authType'));
}
```

### **Esempio 2: Accesso Differenziato per Tipo Auth**

```php
// In una vista Blade
@if(FegiAuth::isStrongAuth())
    <a href="{{ route('profile.edit') }}">Modifica Profilo Completo</a>
@elseif(FegiAuth::isWeakAuth())
    <a href="{{ route('user.domains.upgrade') }}">Upgrade per Accesso Completo</a>
    <a href="{{ route('profile.basic') }}">Profilo Base</a>
@else
    <a href="{{ route('login') }}">Accedi</a>
@endif
```

### **Esempio 3: Controllo Permessi Avanzato**

```php
// Controller con controllo permessi
public function sensitiveAction() {
    if (!FegiAuth::can('sensitive_operation')) {
        if (FegiAuth::isWeakAuth()) {
            return redirect()->route('user.domains.upgrade')
                ->with('message', 'Upgrade richiesto per questa operazione');
        }
        return abort(403);
    }

    // Operazione sensibile
    return $this->performSensitiveOperation();
}
```

### **Esempio 4: Middleware Personalizzato**

```php
// Middleware per weak auth
class RequireWeakAuth {
    public function handle($request, Closure $next) {
        if (!FegiAuth::check()) {
            return redirect()->route('fegi.connect');
        }

        return $next($request);
    }
}
```

---

## 🔧 Troubleshooting

### **Problema: FegiAuth::id() ritorna null**

#### **Diagnosi**

```php
// Route di debug
GET /test/fegiauth-debug

// Controlla:
// 1. strong_auth.guard_check
// 2. weak_auth.session_auth_status
// 3. resolved_user
```

#### **Soluzioni**

1. **Strong Auth**: Verifica `Auth::guard('web')->check()`
2. **Weak Auth**: Controlla sessione PHP
3. **Database**: Verifica esistenza utente con ID sessione

### **Problema: Redirect Loop**

#### **Causa Comune**

Route di upgrade che punta a se stessa

#### **Soluzione**

```php
// SBAGLIATO
return redirect()->route('user.domains.personal-data');

// CORRETTO
return redirect()->route('user.domains.upgrade');
```

### **Problema: Permission Denied Unexpectedly**

#### **Verifica**

```php
// Controlla permessi utente
$user = FegiAuth::user();
$permissions = $user->getAllPermissions();
dd($permissions);

// Controlla ruoli
$roles = $user->getRoleNames();
dd($roles);
```

### **Problema: Sessione Weak Auth Persa**

#### **Cause**

1. Sessione PHP scaduta
2. Clear cache/session
3. Cambio di dominio/subdomain

#### **Soluzioni**

1. Estendere timeout sessione
2. Implementare remember token per weak auth
3. Debug con `session()->all()`

---

## 📚 Risorse Aggiuntive

### **File Chiave**

-   `app/Helpers/FegiAuth.php` - Classe principale
-   `app/Http/Controllers/User/BaseUserDomainController.php` - Controller base
-   `routes/user-domains.php` - Route di autenticazione
-   `routes/test.php` - Route di debug

### **Configurazione**

-   `config/auth.php` - Guards Laravel
-   `config/session.php` - Configurazione sessione
-   `.env` - Variabili ambiente per debug

### **Testing**

```bash
# Test autenticazione
php artisan test --filter AuthTest

# Debug route
curl http://localhost/test/fegiauth-debug

# Clear cache
php artisan optimize:clear
```

---

**Documento aggiornato**: 23 Settembre 2025  
**Versione FegiAuth**: 2.1.0  
**Autore**: AI Partner (Copilot) per Fabio Cherici  
**Progetto**: FlorenceEGI MVP
