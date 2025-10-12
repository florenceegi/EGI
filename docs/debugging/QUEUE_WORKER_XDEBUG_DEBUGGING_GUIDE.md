# 🐛 Queue Worker XDebug Debugging Guide - FlorenceEGI

**Documento:** Guida completa al debugging di Laravel Queue Workers con XDebug  
**Creato:** 2025-10-11  
**Autore:** Padmin D. Curtis (AI Partner OS3.0)  
**Versione:** 1.0.0

---

## 📋 **INDICE**

1. [Problema Rilevato](#problema-rilevato)
2. [Causa Root](#causa-root)
3. [Soluzione Applicata](#soluzione-applicata)
4. [Setup XDebug per Queue Workers](#setup-xdebug-per-queue-workers)
5. [Checklist Pre-Debug](#checklist-pre-debug)
6. [Troubleshooting](#troubleshooting)
7. [Best Practices](#best-practices)

---

## 🚨 **PROBLEMA RILEVATO**

### **Sintomi:**

```
❌ Breakpoints impostati in MintEgiJob::handle() mai raggiunta
❌ dd() statements nel Job completamente ignorati
❌ Mint completato correttamente (ASA ID, TX ID in DB)
❌ owner_id non sincronizzato con buyer_user_id dopo mint
✅ Job dispatch() eseguito correttamente (visible nei log)
```

### **Contesto:**

-   **File coinvolto:** `app/Jobs/MintEgiJob.php`
-   **Queue:** `blockchain` (dedicated queue con `onQueue('blockchain')`)
-   **Sistema:** Laravel 10.x con Redis queue backend
-   **IDE:** VS Code con PHP Debug (XDebug)

### **Cosa sembrava:**

Il Job non veniva mai eseguito, sembrava che `MintEgiJob::handle()` fosse bypassato completamente dal sistema.

---

## 🔍 **CAUSA ROOT**

### **Worker senza XDebug:**

Il worker `php artisan queue:work redis --queue=blockchain` era stato avviato **PRIMA** che XDebug fosse configurato/collegato:

```bash
# Worker PID 30283 (avviato Oct 10, 15:30)
fabio 30283 php artisan queue:work redis --queue=blockchain --tries=3 --timeout=300
```

**Comportamento osservato:**

1. ✅ **Constructor eseguito:** `MintEgiJob::__construct()` viene chiamato al `dispatch()`
2. ✅ **Job serializzato:** Job inserito in Redis queue 'blockchain'
3. ✅ **Worker prende il Job:** Worker processa il Job dalla queue
4. ❌ **handle() eseguito MA senza debug:** Codice esegue correttamente ma:
    - Breakpoints ignorati (no XDebug attached)
    - dd() non stoppa l'esecuzione
    - Log potrebbero finire in stream diverso (/dev/null)

### **Perché owner_id non si sincronizzava:**

Il codice di sincronizzazione era presente in `EgiMintingService::mintEgi()` e `MintEgiJob::handle()`, MA:

```php
// In EgiMintingService::mintEgi() - QUESTO VENIVA ESEGUITO
$egi->update(['owner_id' => $freshBlockchain->buyer_user_id]);
```

**PROBLEMA:** Cache di Eloquent! Anche con `fresh()`, in alcuni casi la relazione `$egiBlockchain->buyer` poteva essere stale.

---

## ✅ **SOLUZIONE APPLICATA**

### **1. Riavvio Worker con XDebug**

```bash
# STEP 1: Terminare vecchio worker
ps aux | grep "queue:work.*blockchain" | grep -v grep
kill 30283  # PID del vecchio worker

# STEP 2: Configurare XDebug
export XDEBUG_MODE=debug
export XDEBUG_CONFIG="client_host=localhost client_port=9003"

# STEP 3: Avviare nuovo worker CON XDebug
php artisan queue:work redis --queue=blockchain --tries=3 --timeout=300 --sleep=3 --max-jobs=100
```

**Verifica XDebug attivo:**

```bash
# Nel Job, aggiungi log:
$logger->info('XDebug status', [
    'xdebug_loaded' => extension_loaded('xdebug') ? 'YES' : 'NO',
    'pid' => getmypid()
]);
```

### **2. Owner ID Sync - Triple Safety**

Implementato sync in **3 punti** per garantire consistenza:

#### **A. Controller (immediato):**

```php
// app/Http/Controllers/MintController.php (linee 347-350)
dispatch(new MintEgiJob($blockchainRecord->id));

// CRITICAL FIX: Sync IMMEDIATAMENTE dopo dispatch
\App\Models\Egi::where('id', $egi->id)->update([
    'owner_id' => Auth::id()
]);
```

**Rationale:** Garantisce owner_id corretto ANCHE se Job fallisce o queue worker non funziona.

#### **B. EgiMintingService (dopo blockchain mint):**

```php
// app/Services/EgiMintingService.php (linee 211-229)
$freshBlockchain = $egiBlockchain->fresh();

$this->logger->info('🔍 DEBUG OWNER SYNC - PRIMA', [
    'egi_id' => $egi->id,
    'egi_owner_id_before' => $egi->owner_id,
    'blockchain_buyer_user_id' => $freshBlockchain->buyer_user_id,
    'will_set_owner_to' => $freshBlockchain->buyer_user_id
]);

$egi->update([
    'owner_id' => $freshBlockchain->buyer_user_id
]);

$egiAfter = Egi::find($egi->id);
$this->logger->info('🔍 DEBUG OWNER SYNC - DOPO', [
    'egi_id' => $egi->id,
    'egi_owner_id_after' => $egiAfter->owner_id,
    'update_worked' => $egiAfter->owner_id == $freshBlockchain->buyer_user_id ? 'YES' : 'NO'
]);
```

**Rationale:** Sincronizzazione "canonica" quando mint completa correttamente. Log dettagliati per debug.

#### **C. MintEgiJob (dopo service call):**

```php
// app/Jobs/MintEgiJob.php (linee 101-107)
$egi = \App\Models\Egi::find($egiBlockchain->egi_id);

$egi->update([
    'owner_id' => $egiBlockchain->buyer_user_id
]);

$logger->info('REAL blockchain minting completed successfully', [
    'asa_id' => $result->asa_id,
    'owner_id_synced' => $egiBlockchain->buyer_user_id,
    'egi_owner_updated' => $egi->owner_id
]);
```

**Rationale:** Ridondante ma safe - garantisce sync anche se Service non lo fa per qualche motivo.

### **3. Log di Debug Aggiunti**

Per tracciare il flusso completo:

```php
// MintEgiJob::handle() - linea 61
$logger->emergency('🚨🚨🚨 MINT JOB HANDLE STARTED 🚨🚨🚨', [
    'egi_blockchain_id' => $this->egiBlockchainId,
    'pid' => getmypid(),
    'xdebug_loaded' => extension_loaded('xdebug') ? 'YES ✅' : 'NO ❌',
    'timestamp' => now()->format('H:i:s.u'),
    'log_category' => 'MINT_JOB_DEBUG'
]);

// EgiMintingService::mintEgi() - linea 83
$this->logger->emergency('🔥🔥🔥 MINTING SERVICE CALLED 🔥🔥🔥', [
    'egi_id' => $egi->id,
    'user_id' => $user->id,
    'pid' => getmypid(),
    'xdebug_enabled' => extension_loaded('xdebug') ? 'YES' : 'NO',
    'timestamp' => now()->format('H:i:s.u'),
    'log_category' => 'MINTING_SERVICE_DEBUG'
]);

// AlgorandService::mintEgi() - linea 100
$this->logger->emergency('🌊🌊🌊 ALGORAND SERVICE CALLED 🌊🌊🌊', [
    'egi_id' => $egiId,
    'user_id' => $user->id,
    'pid' => getmypid(),
    'xdebug_enabled' => extension_loaded('xdebug') ? 'YES' : 'NO',
    'timestamp' => now()->format('H:i:s.u'),
    'log_category' => 'ALGORAND_MINT_DEBUG'
]);
```

**Rationale:** `emergency()` ha priorità massima - garantito che appaia nei log anche con filtering aggressivo.

---

## 🛠️ **SETUP XDEBUG PER QUEUE WORKERS**

### **Prerequisiti:**

1. **XDebug installato:**

    ```bash
    php -m | grep xdebug
    # Output: xdebug
    ```

2. **php.ini configurato:**

    ```ini
    [XDebug]
    zend_extension=xdebug.so
    xdebug.mode=debug
    xdebug.start_with_request=yes
    xdebug.client_host=localhost
    xdebug.client_port=9003
    xdebug.log=/tmp/xdebug.log
    ```

3. **VS Code launch.json:**
    ```json
    {
        "name": "Listen for XDebug",
        "type": "php",
        "request": "launch",
        "port": 9003,
        "pathMappings": {
            "/home/fabio/EGI": "${workspaceFolder}"
        }
    }
    ```

### **Procedura Completa:**

#### **STEP 1: Verifica XDebug**

```bash
php -i | grep xdebug
php -r "var_dump(extension_loaded('xdebug'));"  # bool(true)
```

#### **STEP 2: Stop Worker Vecchio**

```bash
# Trova PID worker
ps aux | grep "queue:work" | grep blockchain

# Termina worker
kill <PID>

# Verifica terminato
ps aux | grep <PID>
```

#### **STEP 3: Configure Environment**

```bash
export XDEBUG_MODE=debug
export XDEBUG_CONFIG="client_host=localhost client_port=9003"

# Verifica export
echo $XDEBUG_MODE
echo $XDEBUG_CONFIG
```

#### **STEP 4: Start Worker con XDebug**

```bash
# Full command with all options
php artisan queue:work redis \
    --queue=blockchain \
    --tries=3 \
    --timeout=300 \
    --sleep=3 \
    --max-jobs=100 \
    --verbose

# Verifica worker attivo
ps aux | grep "queue:work.*blockchain"
```

#### **STEP 5: Test Breakpoint**

```php
// In MintEgiJob::handle(), aggiungi:
public function handle(...) {
    \Log::info('🚨 BREAKPOINT TEST', ['pid' => getmypid()]);
    // ← METTI BREAKPOINT QUI

    $egiBlockchain = EgiBlockchain::with(['egi', 'buyer'])
        ->findOrFail($this->egiBlockchainId);
}
```

#### **STEP 6: Trigger Job**

```bash
# Dispatch test job via tinker
php artisan tinker
>>> dispatch(new \App\Jobs\MintEgiJob(1));
```

#### **STEP 7: Verifica Debug**

```
✅ VS Code si ferma al breakpoint
✅ Puoi ispezionare variabili ($egiBlockchain, $logger, etc.)
✅ Step-through funziona correttamente
```

---

## ✅ **CHECKLIST PRE-DEBUG**

**Prima di ogni sessione di debug Queue Jobs:**

### **1. Worker Status**

```bash
# [ ] Worker attivo?
ps aux | grep "queue:work"

# [ ] Worker su queue corretta?
ps aux | grep "queue:work.*blockchain"

# [ ] Worker PID recente? (non vecchio di giorni)
ps aux | grep "queue:work" | awk '{print $2}' | xargs ps -o lstart= -p
```

### **2. XDebug Status**

```bash
# [ ] XDebug caricato in PHP CLI?
php -m | grep xdebug

# [ ] XDebug configurato correttamente?
php -i | grep xdebug.mode
php -i | grep xdebug.client_host

# [ ] Environment variables settate?
echo $XDEBUG_MODE
echo $XDEBUG_CONFIG
```

### **3. VS Code Debugger**

```
[ ] Launch configuration "Listen for XDebug" attiva?
[ ] Breakpoints impostati nei file corretti?
[ ] Path mappings corretti in launch.json?
[ ] Firewall non blocca porta 9003?
```

### **4. Queue Configuration**

```bash
# [ ] Redis queue funzionante?
php artisan queue:monitor blockchain

# [ ] Job nella queue?
php artisan queue:monitor blockchain
# Output: [redis] blockchain [X] OK

# [ ] Failed jobs?
php artisan queue:failed
```

---

## 🐛 **TROUBLESHOOTING**

### **Problema: Breakpoint non si ferma MAI**

#### **Causa 1: Worker senza XDebug**

```bash
# Verifica PID worker
ps aux | grep "queue:work.*blockchain" | awk '{print $2}'

# Verifica XDebug su quel processo
cat /proc/<PID>/environ | tr '\0' '\n' | grep XDEBUG

# Se vuoto → Worker senza XDebug!
# Soluzione: Riavvia worker con XDEBUG_MODE=debug
```

#### **Causa 2: Path Mappings errati**

```json
// launch.json - VERIFICA PATH
{
    "pathMappings": {
        "/home/fabio/EGI": "${workspaceFolder}" // ← Deve corrispondere!
    }
}
```

#### **Causa 3: Porta sbagliata**

```bash
# XDebug trying to connect to port?
tail -f /tmp/xdebug.log

# Se vedi "Connection refused" → VS Code non in ascolto
# Se vedi "Connected" → Path mappings errati
```

### **Problema: owner_id non si sincronizza**

#### **Verifica 1: Cache Eloquent**

```php
// ❌ SBAGLIATO
$egi->owner_id = $buyer_id;
$egi->save();

// ✅ CORRETTO - Bypassa cache
\App\Models\Egi::where('id', $egi->id)->update(['owner_id' => $buyer_id]);

// ✅ ANCORA MEGLIO - Fresh + explicit find
$egi = \App\Models\Egi::find($egi->id);
$egi->update(['owner_id' => $buyer_id]);
```

#### **Verifica 2: Transazioni DB**

```php
// Se in DB transaction, update potrebbe non essere committed
DB::commit(); // Force commit
$egi = \App\Models\Egi::find($egi->id); // Re-query dopo commit
```

#### **Verifica 3: Observer/Events**

```bash
# Cerca Observer che potrebbero sovrascrivere owner_id
grep -r "owner_id" app/Observers/
grep -r "saving\|saved" app/Observers/EgiObserver.php
```

### **Problema: Job esegue ma nessun log**

#### **Causa: Log channel diverso**

```php
// Verifica UltraLogManager channel
$this->logger->info('Test'); // Dove finisce?

// Check config
php artisan tinker
>>> config('ultra_log_manager.log_channel')
=> "upload"

// Log finisce in storage/logs/upload.log, non laravel.log!
```

#### **Soluzione: Multi-channel logging**

```php
// Log sia su upload.log CHE console
$this->logger->info('Message');
\Log::channel('single')->info('Message'); // Backup su laravel.log
```

---

## 📋 **BEST PRACTICES**

### **1. Worker Management**

#### **Development:**

```bash
# Usa --verbose per vedere Job processing
php artisan queue:work --verbose

# Usa --once per debug singolo Job
php artisan queue:work --once

# Restart worker dopo code changes
php artisan queue:restart
```

#### **Production:**

```bash
# Usa Supervisor per persistenza
[program:laravel-worker-blockchain]
command=php /path/to/artisan queue:work redis --queue=blockchain --tries=3 --timeout=300
autostart=true
autorestart=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/path/to/worker.log
```

### **2. Debug Logging**

```php
// Emergency per debug critici (sempre visibili)
$this->logger->emergency('🚨 CRITICAL DEBUG POINT', [...]);

// Info per flusso normale
$this->logger->info('Operation completed', [...]);

// Aggiungi SEMPRE:
// - PID processo
// - Timestamp microsecondi
// - XDebug status
// - Context completo
```

### **3. Owner ID Sync Pattern**

```php
// ALWAYS use questo pattern per sync owner_id:
public function syncOwnership(Egi $egi, int $newOwnerId): void
{
    // 1. Log BEFORE
    $this->logger->info('Syncing owner_id', [
        'egi_id' => $egi->id,
        'old_owner_id' => $egi->owner_id,
        'new_owner_id' => $newOwnerId
    ]);

    // 2. Direct update (bypass cache)
    \App\Models\Egi::where('id', $egi->id)->update([
        'owner_id' => $newOwnerId
    ]);

    // 3. Verify AFTER
    $egiAfter = \App\Models\Egi::find($egi->id);

    if ($egiAfter->owner_id !== $newOwnerId) {
        $this->logger->error('Owner sync FAILED!', [
            'expected' => $newOwnerId,
            'actual' => $egiAfter->owner_id
        ]);
        throw new \Exception('Owner ID sync failed');
    }

    // 4. Log SUCCESS
    $this->logger->info('Owner sync SUCCESS', [
        'egi_id' => $egi->id,
        'owner_id' => $egiAfter->owner_id
    ]);
}
```

### **4. Queue Monitoring**

```bash
# Crea monitoring script
#!/bin/bash
# File: scripts/monitor-queue.sh

echo "=== QUEUE STATUS ==="
php artisan queue:monitor blockchain

echo -e "\n=== WORKER STATUS ==="
ps aux | grep "queue:work.*blockchain" | grep -v grep

echo -e "\n=== XDEBUG STATUS ==="
ps aux | grep "queue:work.*blockchain" | grep -v grep | awk '{print $2}' | while read pid; do
    echo "PID $pid:"
    cat /proc/$pid/environ 2>/dev/null | tr '\0' '\n' | grep XDEBUG || echo "  ❌ No XDebug"
done

echo -e "\n=== FAILED JOBS ==="
php artisan queue:failed --limit=5
```

---

## 🎯 **RISULTATO FINALE**

### **PRIMA (Broken):**

```
❌ Breakpoints ignorati
❌ owner_id non sincronizzato
❌ Debug impossibile
❌ "Il Job non esegue mai handle()!"
```

### **DOPO (Fixed):**

```
✅ Worker con XDebug attivo (PID 63532)
✅ Breakpoints funzionanti
✅ owner_id sincronizzato correttamente (3 punti)
✅ Log completi e tracciabili
✅ Debug completo del flusso
```

### **Verifica Success:**

```bash
# Query last mint
php artisan tinker --execute="
\$mint = \App\Models\EgiBlockchain::orderBy('id', 'desc')->first();
\$egi = \$mint->egi;
echo 'Buyer ID: ' . \$mint->buyer_user_id . PHP_EOL;
echo 'Owner ID: ' . \$egi->owner_id . PHP_EOL;
echo 'Match: ' . (\$egi->owner_id == \$mint->buyer_user_id ? 'YES ✅' : 'NO ❌') . PHP_EOL;
"

# Output:
# Buyer ID: 3
# Owner ID: 3
# Match: YES ✅
```

---

## 📚 **RIFERIMENTI**

-   **Laravel Queues:** https://laravel.com/docs/10.x/queues
-   **XDebug Documentation:** https://xdebug.org/docs/
-   **VS Code PHP Debug:** https://marketplace.visualstudio.com/items?itemName=xdebug.php-debug
-   **UltraLogManager:** `vendor/ultra/ultra-log-manager/README.md`
-   **Eloquent Caching:** https://laravel.com/docs/10.x/eloquent#refreshing-models

---

## 🔄 **CHANGELOG**

-   **2025-10-11:** v1.0.0 - Documento iniziale creato dopo risoluzione owner_id sync bug

---

**Fine documento - Archiviato per riferimento futuro** ✅
