# 🏢 EGI Platform - Multi-Tenant SaaS Implementation

> **Trasformazione da monolite a SaaS multi-tenant**  
> **Version:** 1.0 - Implementation Guide  
> **Data:** 23 Ottobre 2025

---

## 🎯 OBIETTIVO

Implementare **architettura multi-tenant** per isolare:

✅ **Tenant pubblico "ART"** → Marketplace NFT, landing pubblica  
✅ **Tenant PA (Comuni, Regioni)** → Atti amministrativi propri, N.A.T.A.N. isolato  
✅ **Isolamento dati totale** → Nessun leak fra tenant  
✅ **Scalabilità** → Pronto per centinaia di enti PA

---

## 📊 ARCHITETTURA SCELTA

### **Opzione Selezionata: Single Database + Tenant Scoping**

```
┌─────────────────────────────────────────┐
│        MariaDB (Single Database)        │
├─────────────────────────────────────────┤
│ pa_entities (tenants)                   │
│   ├─ id: 1 → "ART" (pubblico)           │
│   ├─ id: 2 → "Comune Firenze"           │
│   └─ id: 3 → "Comune Roma"              │
│                                         │
│ users (tenant_id FK)                    │
│ egis (tenant_id FK)                     │
│ natan_chat_messages (tenant_id FK)      │
│ pa_act_embeddings (via egi_id)          │
│ user_conversations (tenant_id FK)       │
└─────────────────────────────────────────┘

Middleware → Detect Tenant → Global Scope → WHERE tenant_id = X
```

**PRO:**
- ✅ Implementazione rapida (2-3 settimane)
- ✅ Un solo database da gestire
- ✅ Buon isolamento (99% sicuro se ben fatto)
- ✅ Cross-tenant queries possibili (per super-admin)

**CONTRO:**
- ⚠️ Un bug può esporre dati (serve testing rigoroso)

---

## 🚀 ROADMAP IMPLEMENTAZIONE

### **Week 1: Database & Models**

#### **Task 1.1: Installa Package Tenancy**

```bash
composer require stancl/tenancy
php artisan tenancy:install
php artisan migrate
```

#### **Task 1.2: Configura Tenant Model**

```php
// config/tenancy.php
return [
    'tenant_model' => \App\Models\Tenant::class,
    'database' => [
        'based_on_tenants' => false, // Single DB strategy
        'central_connection' => 'mysql',
    ],
];
```

```php
// app/Models/Tenant.php
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

class Tenant extends BaseTenant
{
    protected $table = 'pa_entities'; // Usa tabella esistente
    
    public static function getCustomColumns(): array
    {
        return ['id', 'name', 'slug', 'is_public'];
    }
}
```

#### **Task 1.3: Migration - Aggiungi tenant_id**

```php
// database/migrations/2025_10_24_add_tenant_id_to_tables.php

public function up()
{
    // Users
    Schema::table('users', function (Blueprint $table) {
        $table->foreignId('tenant_id')
            ->nullable()
            ->after('id')
            ->constrained('pa_entities')
            ->cascadeOnDelete();
        $table->index('tenant_id');
    });
    
    // Egis (rename existing pa_entity_id → tenant_id)
    Schema::table('egis', function (Blueprint $table) {
        $table->renameColumn('pa_entity_id', 'tenant_id');
        $table->index('tenant_id');
    });
    
    // N.A.T.A.N. Chat Messages
    Schema::table('natan_chat_messages', function (Blueprint $table) {
        $table->foreignId('tenant_id')
            ->nullable()
            ->after('id')
            ->constrained('pa_entities')
            ->cascadeOnDelete();
        $table->index('tenant_id');
    });
    
    // User Conversations (per chat system)
    Schema::table('user_conversations', function (Blueprint $table) {
        $table->foreignId('tenant_id')
            ->nullable()
            ->after('id')
            ->constrained('pa_entities')
            ->cascadeOnDelete();
        $table->index('tenant_id');
    });
}
```

#### **Task 1.4: Aggiungi Trait ai Models**

```php
// app/Models/Egi.php
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Egi extends Model
{
    use BelongsToTenant;
    
    // Il trait aggiunge automaticamente:
    // - tenant_id a $fillable
    // - Global scope: WHERE tenant_id = current_tenant
    // - Auto-assign tenant_id su create
}
```

**Applica a tutti i modelli:**
- `User`
- `Egi`
- `NatanChatMessage`
- `UserConversation`
- `UserChatMessage`

#### **Task 1.5: Seeder - Migra Dati Esistenti**

```php
// database/seeders/MigrateToMultiTenantSeeder.php

public function run()
{
    // 1. Crea tenant pubblico ART
    $artTenant = Tenant::updateOrCreate(['id' => 1], [
        'name' => 'ART - Arte Blockchain',
        'slug' => 'art',
        'is_public' => true,
    ]);
    
    // 2. Assegna atti pubblici ad ART
    Egi::whereNull('tenant_id')
        ->orWhere('is_public', true)
        ->update(['tenant_id' => $artTenant->id]);
    
    // 3. Per ogni PA esistente, assegna tenant_id
    PaEntity::where('id', '!=', 1)->chunk(100, function($entities) {
        foreach ($entities as $entity) {
            // Atti
            Egi::where('tenant_id', $entity->id)->update(['tenant_id' => $entity->id]);
            
            // Utenti
            User::where('pa_entity_id', $entity->id)->update(['tenant_id' => $entity->id]);
            
            // N.A.T.A.N. messages
            NatanChatMessage::whereHas('user', function($q) use ($entity) {
                $q->where('pa_entity_id', $entity->id);
            })->update(['tenant_id' => $entity->id]);
        }
    });
}
```

---

### **Week 2: Middleware & Routing**

#### **Task 2.1: Middleware Tenant Detection**

```php
// app/Http/Middleware/InitializeTenancyMiddleware.php

namespace App\Http\Middleware;

use Closure;
use App\Models\Tenant;
use Stancl\Tenancy\Tenancy;

class InitializeTenancyMiddleware
{
    protected $tenancy;

    public function __construct(Tenancy $tenancy)
    {
        $this->tenancy = $tenancy;
    }

    public function handle($request, Closure $next)
    {
        // STRATEGY 1: Da subdomain (priorità massima)
        if ($tenant = $this->detectFromSubdomain($request)) {
            $this->tenancy->initialize($tenant);
            return $next($request);
        }
        
        // STRATEGY 2: Da autenticazione utente
        if (auth()->check() && auth()->user()->tenant_id) {
            $tenant = Tenant::find(auth()->user()->tenant_id);
            if ($tenant) {
                $this->tenancy->initialize($tenant);
                return $next($request);
            }
        }
        
        // STRATEGY 3: Default tenant pubblico (ART)
        $publicTenant = Tenant::where('is_public', true)->first();
        if ($publicTenant) {
            $this->tenancy->initialize($publicTenant);
        }
        
        return $next($request);
    }
    
    protected function detectFromSubdomain($request)
    {
        $host = $request->getHost();
        $parts = explode('.', $host);
        
        // Skip www, app, api
        if (count($parts) < 2 || in_array($parts[0], ['www', 'app', 'api'])) {
            return null;
        }
        
        $subdomain = $parts[0];
        return Tenant::where('slug', $subdomain)->first();
    }
}
```

#### **Task 2.2: Registra Middleware**

```php
// app/Http/Kernel.php

protected $middlewareGroups = [
    'web' => [
        // ... existing middleware
        \App\Http\Middleware\InitializeTenancyMiddleware::class,
    ],
];
```

#### **Task 2.3: Helper Tenant**

```php
// app/Helpers/TenantHelper.php

namespace App\Helpers;

use App\Models\Tenant;

class TenantHelper
{
    public static function current(): ?Tenant
    {
        return tenancy()->tenant;
    }
    
    public static function id(): ?int
    {
        return self::current()?->id;
    }
    
    public static function isPublic(): bool
    {
        $tenant = self::current();
        return $tenant && $tenant->is_public;
    }
    
    public static function name(): string
    {
        return self::current()?->name ?? 'Guest';
    }
}
```

**Uso nel codice:**

```php
// Nei controller
if (TenantHelper::isPublic()) {
    // Mostra landing pubblica
} else {
    // Mostra dashboard PA
}

// Nei Blade templates
<h1>Benvenuto su {{ TenantHelper::name() }}</h1>
```

---

### **Week 3: Refactoring Services**

#### **Task 3.1: RagService - Tenant Isolation**

```php
// app/Services/RagService.php

public function semanticSearch(string $query, User $user, int $limit = 10): ?Collection
{
    // Generate query embedding
    $queryEmbedding = $this->embeddingService->generateEmbedding($query);
    
    // Fetch acts ONLY for current tenant (global scope automatico!)
    $actsWithEmbeddings = Egi::has('embedding')
        ->with('embedding')
        ->get(); // ← Global scope: WHERE tenant_id = X
    
    // ... cosine similarity logic ...
}
```

**Prima:** Vedeva TUTTI gli atti di TUTTE le PA ❌  
**Dopo:** Vede solo atti del proprio tenant ✅

#### **Task 3.2: NatanChatService - Tenant Context**

```php
// app/Services/NatanChatService.php

public function processQuery(string $query, User $user, ...): array
{
    // Tenant già impostato dal middleware
    
    // RAG search (già isolato da global scope)
    $relevantActs = $this->rag->findRelevantActs($query, $user, 10);
    
    // Save message con tenant_id
    $userMessage = NatanChatMessage::create([
        'user_id' => $user->id,
        'tenant_id' => $user->tenant_id, // ← Esplicito o auto dal trait
        'role' => 'user',
        'content' => $query,
    ]);
    
    // ... rest of logic ...
}
```

#### **Task 3.3: ChatService - Tenant Isolation**

```php
// app/Services/ChatService.php (per user-to-user chat)

public function startConversation(User $sender, User $recipient): UserConversation
{
    // Verifica stesso tenant
    if ($sender->tenant_id !== $recipient->tenant_id) {
        throw new \Exception('Cannot chat with users from other tenants');
    }
    
    // Crea conversazione con tenant_id
    return UserConversation::create([
        'tenant_id' => $sender->tenant_id,
        'type' => 'one_to_one',
    ]);
}
```

---

### **Week 4: UI/UX & Testing**

#### **Task 4.1: Tenant Switcher (Super Admin)**

```blade
<!-- resources/views/components/tenant-switcher.blade.php -->

@can('switch-tenant')
<div class="tenant-switcher">
    <label>Tenant attuale:</label>
    <select onchange="switchTenant(this.value)">
        @foreach(\App\Models\Tenant::all() as $tenant)
            <option value="{{ $tenant->id }}" 
                    @selected($tenant->id === TenantHelper::id())>
                {{ $tenant->name }}
            </option>
        @endforeach
    </select>
</div>

<script>
function switchTenant(tenantId) {
    fetch('/admin/switch-tenant', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({tenant_id: tenantId})
    }).then(() => location.reload());
}
</script>
@endcan
```

#### **Task 4.2: Testing - Tenant Isolation**

```php
// tests/Feature/TenantIsolationTest.php

class TenantIsolationTest extends TestCase
{
    public function test_user_can_only_see_own_tenant_acts()
    {
        $tenant1 = Tenant::factory()->create();
        $tenant2 = Tenant::factory()->create();
        
        // User tenant1
        $user1 = User::factory()->create(['tenant_id' => $tenant1->id]);
        
        // Crea atti per tenant1
        tenancy()->initialize($tenant1);
        $act1 = Egi::factory()->create();
        
        // Crea atti per tenant2
        tenancy()->initialize($tenant2);
        $act2 = Egi::factory()->create();
        
        // Switch a tenant1
        tenancy()->initialize($tenant1);
        $this->actingAs($user1);
        
        // Verifica isolamento
        $acts = Egi::all();
        $this->assertCount(1, $acts);
        $this->assertEquals($act1->id, $acts->first()->id);
        $this->assertNotContains($act2->id, $acts->pluck('id'));
    }
    
    public function test_natan_only_searches_tenant_acts()
    {
        // ... similar logic per N.A.T.A.N. RAG ...
    }
    
    public function test_users_cannot_chat_across_tenants()
    {
        $user1 = User::factory()->create(['tenant_id' => 1]);
        $user2 = User::factory()->create(['tenant_id' => 2]);
        
        $this->actingAs($user1);
        
        $response = $this->postJson('/api/user-chat/conversations', [
            'recipient_id' => $user2->id
        ]);
        
        $response->assertStatus(403); // Forbidden
    }
}
```

#### **Task 4.3: Performance Testing**

```bash
# Installa Apache Bench
sudo apt-get install apache2-utils

# Test 100 concurrent users
ab -n 1000 -c 100 -H "Authorization: Bearer TOKEN" \
   https://egi.test/api/pa/natan/chat

# Test RAG query performance
ab -n 100 -c 10 -p query.json -T application/json \
   https://egi.test/api/pa/natan/chat
```

**Target Performance:**
- RAG query: <3s (p95)
- User chat message: <500ms (p95)
- Page load: <2s (p95)

---

## 🌐 SUBDOMAIN ROUTING

### **DNS Configuration**

```
# DNS Records (Cloudflare/AWS Route53)

A    egi.it              → 123.45.67.89 (server IP)
A    *.egi.it            → 123.45.67.89 (wildcard)

CNAME www.egi.it         → egi.it
CNAME firenze.egi.it     → egi.it
CNAME roma.egi.it        → egi.it
```

### **Nginx Configuration**

```nginx
# /etc/nginx/sites-available/egi

server {
    listen 80;
    server_name egi.it *.egi.it;
    
    root /var/www/egi/public;
    index index.php;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
    }
}
```

### **SSL Certificate (Let's Encrypt)**

```bash
# Installa certbot
sudo apt-get install certbot python3-certbot-nginx

# Genera certificati wildcard
sudo certbot --nginx -d egi.it -d "*.egi.it"
```

### **Tenant Slug Registration**

```php
// Quando PA si registra:
$paEntity = PaEntity::create([
    'name' => 'Comune di Firenze',
    'slug' => 'firenze', // ← Usato per subdomain
    'is_public' => false,
]);

// Accessibile su: https://firenze.egi.it
```

**Regole slug:**
- Lowercase, no spazi
- No caratteri speciali (solo a-z, 0-9, -)
- Univoco (DB unique constraint)
- Validazione: `/^[a-z0-9-]+$/`

---

## 🔐 SECURITY CHECKLIST

### **Code Level**

- [ ] **Global Scopes attivi** su tutti i modelli tenant-scoped
- [ ] **Policies** verificano tenant ownership
- [ ] **No raw queries** senza tenant_id WHERE clause
- [ ] **Input validation** su tenant_id (prevent injection)
- [ ] **CSRF protection** attivo
- [ ] **XSS sanitization** su tutti gli output

### **Database Level**

- [ ] **Foreign keys** con `ON DELETE CASCADE`
- [ ] **Indexes** su tutti i tenant_id columns
- [ ] **Unique constraints** su slug
- [ ] **Backup strategy** (daily, retention 30 days)

### **Infrastructure Level**

- [ ] **SSL/TLS** obbligatorio (HSTS header)
- [ ] **Firewall** (UFW/iptables, solo 80/443 pubblici)
- [ ] **Rate limiting** (Laravel throttle middleware)
- [ ] **DDoS protection** (Cloudflare)
- [ ] **Log monitoring** (Sentry, Laravel Log Viewer)

---

## 📊 MONITORING & ALERTING

### **Metrics da Tracciare**

```php
// app/Services/TenantMetricsService.php

public function getTenantStats(int $tenantId): array
{
    return [
        'users_count' => User::where('tenant_id', $tenantId)->count(),
        'acts_count' => Egi::where('tenant_id', $tenantId)->count(),
        'embeddings_count' => PaActEmbedding::whereHas('egi', function($q) use ($tenantId) {
            $q->where('tenant_id', $tenantId);
        })->count(),
        'natan_queries_today' => NatanChatMessage::where('tenant_id', $tenantId)
            ->where('created_at', '>=', now()->startOfDay())
            ->where('role', 'user')
            ->count(),
        'storage_mb' => $this->calculateStorageUsage($tenantId),
    ];
}
```

### **Alerting Rules**

```
- Tenant > 10k atti → Notify admin (potential scaling issue)
- Nessun query N.A.T.A.N. per 7 giorni → Notify tenant (onboarding issue?)
- Embeddings generation failed > 10% → Alert tecnico
- API errors > 5% → Critical alert
```

---

## 💰 PRICING MODEL (Futuro)

### **Tier Proposti**

| Tier | Atti | Utenti | N.A.T.A.N. Query/mese | Prezzo/mese |
|------|------|--------|----------------------|-------------|
| **Free** | 100 | 3 | 50 | €0 |
| **Basic** | 1,000 | 10 | 500 | €99 |
| **Professional** | 10,000 | 50 | 2,000 | €299 |
| **Enterprise** | Unlimited | Unlimited | Unlimited | Custom |

**Add-ons:**
- User Chat: +€29/mese
- Export PDF: +€19/mese
- API Access: +€49/mese

---

## 🚀 GO-LIVE CHECKLIST

### **Pre-Launch**

- [ ] Tutti i test passano (unit, feature, browser)
- [ ] Seeders eseguiti (tenant ART + demo data)
- [ ] DNS configurato + SSL attivo
- [ ] Backup automatici attivi
- [ ] Monitoring configurato (Sentry, uptime)
- [ ] Documentazione aggiornata

### **Launch Day**

- [ ] Deploy su production
- [ ] Migrazione dati esistenti
- [ ] Smoke test (login, N.A.T.A.N. query, chat)
- [ ] Notifica ai beta testers

### **Post-Launch (Prima settimana)**

- [ ] Monitor errori (target: <1%)
- [ ] Raccogliere feedback utenti
- [ ] Fix critical bugs entro 24h
- [ ] Ottimizzazioni performance se necessario

---

## 📚 DOCUMENTAZIONE UTENTE

### **Per PA Admin**

1. **Onboarding:**
   - Registrazione ente
   - Configurazione subdomain
   - Invito utenti

2. **Gestione Atti:**
   - Upload manuale
   - Scraping automatico
   - Generazione embeddings

3. **N.A.T.A.N.:**
   - Come fare domande strategiche
   - Interpretare le risposte
   - Condividere analisi

4. **User Chat:**
   - Iniziare conversazioni
   - Condividere analisi N.A.T.A.N.
   - Best practices

### **Per Sviluppatori**

- API Reference (Swagger/Postman)
- Webhook documentation
- SDK (PHP, JavaScript)

---

**Documentato da:** AI Agent (Claude Sonnet 3.5)  
**Ultima revisione:** 23 Ottobre 2025  
**Status:** Implementation Guide (ready to start)

