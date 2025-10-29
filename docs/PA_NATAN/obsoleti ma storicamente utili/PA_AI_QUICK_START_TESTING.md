# ⚡ PA + AI SYSTEM - QUICK START TESTING

**Per:** Fabio Cherici  
**Quando:** Subito, adesso!  
**Tempo:** 2 ore per test essenziali

---

## 🚀 START TESTING IN 5 MINUTI

### Step 1: Verifica Environment (2 min)

```bash
# Vai nella directory progetto
cd /home/fabio/EGI

# Check Git status
git status

# Pull ultime modifiche (se necessario)
git pull origin main

# Check services
docker ps | grep algorand        # AlgoKit microservice
systemctl status mysql           # Database
php artisan queue:work --help    # Queue disponibile

# Check .env configuration
grep ANTHROPIC_API_KEY .env
grep ALGORAND_ .env
grep APP_ENV .env
```

**Expected:**

-   ✅ AlgoKit container running
-   ✅ MySQL running
-   ✅ ANTHROPIC_API_KEY presente
-   ✅ APP_ENV=local o staging

---

### Step 2: Prepara Database Test (3 min)

```bash
# Backup database corrente (sicurezza)
php artisan db:backup

# Run migrations se necessario
php artisan migrate --pretend  # Dry run
php artisan migrate            # Apply

# Seed dati PA test (se non già fatto)
php artisan db:seed --class=PAEnterpriseDemoSeeder

# Verifica utente PA esiste
php artisan tinker
```

```php
// In tinker
$paUser = User::where('email', 'pa@test.com')->first();
if (!$paUser) {
    echo "CREARE UTENTE PA TEST!\n";
} else {
    echo "PA User: {$paUser->name} - {$paUser->email}\n";
    echo "Roles: " . $paUser->roles->pluck('name')->implode(', ') . "\n";
}
exit;
```

**Se utente non esiste, crea:**

```bash
php artisan tinker
```

```php
$user = User::create([
    'name' => 'PA Test Entity',
    'email' => 'pa@test.com',
    'password' => bcrypt('password123'),
    'email_verified_at' => now(),
]);
$user->assignRole('pa_entity');
echo "User created: pa@test.com / password123\n";
exit;
```

---

### Step 3: Start Queue Worker (1 min)

```bash
# Terminal dedicato per queue worker
php artisan queue:work --queue=default --tries=3 --timeout=90

# Lascia questo terminale aperto e visibile
# Vedrai i job processarsi in real-time
```

**Oppure usa tmux/screen:**

```bash
# In tmux session
tmux new -s queue
php artisan queue:work --queue=default --verbose

# Detach: Ctrl+B, poi D
# Reattach: tmux attach -t queue
```

---

### Step 4: Apri Browser e Login (1 min)

```bash
# Apri browser
firefox http://localhost &
# Oppure
google-chrome http://localhost &
```

**Login:**

1. Vai su http://localhost/login
2. Email: `pa@test.com`
3. Password: `password123`
4. Click "Login"

**Expected:**

-   ✅ Redirect automatico a `/pa/dashboard`
-   ✅ Dashboard PA visibile (NON dashboard Creator)
-   ✅ Sidebar con voci PA-specific
-   ✅ Colors: blu #1B365D e oro #D4A574

---

## 🧪 TEST ESSENZIALI (2 ORE)

### ✅ TEST 1: Upload Atto + Tokenizzazione (20 min)

```bash
# Prepara file test
ls storage/testing/real_signed_pa_acts/

# Se directory vuota, genera mock
php artisan make:command GenerateMockPaAct
php artisan mock:pa-act
```

**Nel Browser:**

1. **Vai su Upload:**

    - Click menu "Atti Amministrativi" → "Carica Nuovo Atto"
    - Oppure URL diretto: http://localhost/pa/acts/upload

2. **Upload File:**

    - Seleziona PDF da `storage/testing/real_signed_pa_acts/`
    - Attendi validazione firma (2-3 sec)
    - Verifica badge verde "Firma Valida" ✅

3. **Compila Metadati:**

    ```
    Protocollo: TEST-2023-001
    Data: 2023-10-23
    Tipo: Determina Dirigenziale
    Titolo: Test Sistema N.A.T.A.N. Pre-Presentazione
    Descrizione: Atto di test per validazione workflow completo
    ```

4. **Spunta Checkbox:**

    - ✅ "Tokenizza automaticamente su blockchain Algorand"

5. **Carica:**

    - Click "Carica Atto"
    - ⏱️ Attendi conferma (< 5 sec)

6. **Verifica Success:**
    - Messaggio "Atto caricato con successo"
    - Redirect a lista atti

**Verifica in Terminal Queue:**

```
# Dovresti vedere nel terminal queue worker:
[2023-10-23 10:15:32][1] Processing: App\Jobs\TokenizePaActJob
[2023-10-23 10:15:48][1] Processed:  App\Jobs\TokenizePaActJob
```

**Verifica Database:**

```bash
php artisan tinker
```

```php
$egi = Egi::latest()->first();
echo "Protocollo: {$egi->pa_protocol_number}\n";
echo "Hash: {$egi->pa_document_hash}\n";
echo "TXID: {$egi->pa_txid}\n";
echo "Anchored: {$egi->pa_anchored_at}\n";
echo "Public Code: {$egi->public_code}\n";

// Copia TXID per step successivo
$txid = $egi->pa_txid;
echo "\nTXID copiato: $txid\n";
exit;
```

**Verifica AlgoExplorer:**

1. Copia TXID dall'output sopra
2. Vai su: https://testnet.algoexplorer.io/tx/{TXID}
3. Verifica transazione presente
4. Check "note" field contiene hash

**⚠️ Se TXID è null:**

```bash
# Check log errors
tail -50 storage/logs/laravel.log | grep -i error

# Check failed jobs
php artisan queue:failed

# Retry failed job
php artisan queue:retry {job_id}
```

**✅ Success Criteria:**

-   Upload completo senza errori
-   TXID presente in database
-   Transazione visibile su AlgoExplorer
-   ⏱️ Tempo totale < 30 sec

---

### ✅ TEST 2: Chat N.A.T.A.N. AI (15 min)

**Nel Browser:**

1. **Apri Chat:**

    - Click menu "N.A.T.A.N. Chat AI"
    - Oppure URL: http://localhost/pa/natan/chat

2. **Test Query Base:**

    ```
    Query: Ciao N.A.T.A.N., puoi presentarti?
    ```

    - ⏱️ Attendi risposta (< 5 sec)
    - Verifica risposta coerente

3. **Test RAG - Ricerca Specifica:**

    ```
    Query: Cerca l'atto con protocollo TEST-2023-001
    ```

    - Verifica trova l'atto caricato
    - Verifica metadati corretti

4. **Test RAG - Query Semantica:**

    ```
    Query: Quanti atti ho caricato oggi?
    ```

    - Verifica risposta con numero corretto

5. **Test Riassunto:**
    ```
    Query: Riassumi l'atto TEST-2023-001
    ```
    - Verifica riassunto coerente

**Verifica Log GDPR:**

```bash
# Verifica nessun PII inviato ad AI
grep "DataSanitizerService" storage/logs/laravel.log | tail -20
grep "AnthropicService.*SENT" storage/logs/laravel.log | tail -5
```

**Troubleshooting:**

```bash
# Se chat non risponde, verifica API key
php artisan tinker
```

```php
$service = app(\App\Services\AnthropicService::class);
$available = $service->isAvailable();
echo "Anthropic Available: " . ($available ? 'YES' : 'NO') . "\n";

// Se NO, check .env
echo "API Key presente: " . (config('services.anthropic.api_key') ? 'YES' : 'NO') . "\n";
exit;
```

**✅ Success Criteria:**

-   Chat risponde in < 5 sec
-   RAG trova atti corretti
-   Risposte contestualmente rilevanti
-   Log GDPR pulito (no PII)

---

### ✅ TEST 3: Verifica Pubblica (10 min)

**Ottieni Public Code:**

```bash
php artisan tinker
```

```php
$egi = Egi::latest()->first();
$code = $egi->public_code;
echo "Public Code: $code\n";
echo "URL: http://localhost/verify/$code\n";
exit;
```

**Nel Browser:**

1. **LOGOUT Prima:** (importante!)

    - Click logout
    - Verifica non più autenticato

2. **Vai su Verifica Pubblica:**

    - URL: `http://localhost/verify/{CODE}` (sostituisci {CODE})
    - ⚠️ NO login richiesto

3. **Verifica UI:**

    - Header "Verifica Atto PA"
    - Badge "Certificato Blockchain" verde
    - Metadati visibili:
        - Protocollo
        - Data
        - Tipo
        - Titolo
    - Sezione Blockchain:
        - Hash documento
        - TXID
        - Link AlgoExplorer

4. **Test Copy-to-Clipboard:**

    - Click su hash → verifica copia
    - Click su TXID → verifica copia

5. **Test Link AlgoExplorer:**
    - Click link TXID
    - Verifica nuova tab aperta
    - Verifica transazione visibile

**✅ Success Criteria:**

-   Verifica funziona senza auth
-   UI professionale
-   Link blockchain funzionante
-   Mobile responsive (test con F12 → device toolbar)

---

### ✅ TEST 4: Dashboard KPI (10 min)

**Nel Browser (loggato PA):**

1. **Vai su Dashboard:**

    - URL: http://localhost/pa/dashboard

2. **Verifica KPI Cards:**
    - Totale Atti: \_\_
    - Atti Tokenizzati: \_\_
    - In Attesa: \_\_
    - Success Rate: \_\_%

**Verifica Accuracy KPI:**

```bash
php artisan tinker
```

```php
// Count totale atti PA user corrente
$user = User::where('email', 'pa@test.com')->first();
$total = Egi::whereHas('collection', function($q) use ($user) {
    $q->where('user_id', $user->id);
})->count();
echo "Total Atti: $total\n";

// Count tokenizzati
$anchored = Egi::whereHas('collection', function($q) use ($user) {
    $q->where('user_id', $user->id);
})->whereNotNull('pa_anchored_at')->count();
echo "Tokenizzati: $anchored\n";

// Count in attesa
$pending = $total - $anchored;
echo "In Attesa: $pending\n";

// Success rate
$rate = $total > 0 ? round(($anchored / $total) * 100, 1) : 0;
echo "Success Rate: {$rate}%\n";

exit;
```

**Confronta numeri dashboard con output tinker.**

**✅ Success Criteria:**

-   KPI corretti (match tinker)
-   Dashboard carica < 2 sec
-   UI clean senza errori JS

---

### ✅ TEST 5: Lista Atti & Filtri (15 min)

**Nel Browser:**

1. **Vai su Lista Atti:**

    - Menu "Atti Amministrativi" → "Tutti gli Atti"
    - URL: http://localhost/pa/acts

2. **Verifica Tabella:**

    - Colonne: Protocollo, Tipo, Data, Titolo, Status
    - Badge tokenizzazione colorati
    - Pagination funzionante

3. **Test Filtri:**

    - Filtro Tipo: seleziona "Determina Dirigenziale"
    - Verifica filtra correttamente
    - Filtro Data: range ultimi 7 giorni
    - Verifica filtra correttamente

4. **Test Search:**

    - Search box: "TEST-2023"
    - Verifica trova atti con protocollo matching

5. **Test Sorting:**

    - Click header "Data"
    - Verifica sort ASC/DESC
    - Click header "Protocollo"
    - Verifica sort funziona

6. **Test Detail View:**
    - Click su atto
    - Verifica pagina detail carica
    - Verifica tutte le sezioni presenti

**✅ Success Criteria:**

-   Filtri funzionanti
-   Search precisa
-   Sorting corretto
-   Detail view completa

---

## 🔍 VERIFICA LOGS (15 min)

### Check Laravel Logs

```bash
# Ultimi errori
tail -100 storage/logs/laravel.log | grep -i error

# Ultimi warning
tail -100 storage/logs/laravel.log | grep -i warning

# Activity log PA
tail -50 storage/logs/laravel.log | grep PA_ACT

# Queue jobs
tail -50 storage/logs/laravel.log | grep TokenizePaActJob
```

**Expected:**

-   Zero errori critici
-   Warning solo per cose note (deprecations, etc.)
-   PA activity logged correttamente

---

### Check Ultra Log Manager

```bash
php artisan tinker
```

```php
// Ultimi log ULM
$logs = \Ultra\UltraLogManager\Models\UltraLog::latest()->take(20)->get(['action', 'description', 'created_at']);
foreach ($logs as $log) {
    echo "[{$log->created_at}] {$log->action}: {$log->description}\n";
}
exit;
```

**Expected:**

-   Upload logged
-   Tokenization logged
-   AI requests logged
-   No PII in logs

---

### Check Queue Failed Jobs

```bash
# Lista failed jobs
php artisan queue:failed

# Se ci sono failures, inspect
php artisan queue:failed --id={id}

# Retry se possibile
php artisan queue:retry {id}

# Clear old failures (se > 24h)
php artisan queue:flush
```

---

## 🎯 STRESS TEST RAPIDO (15 min)

### Test Caricamento Multiplo

```bash
# Terminal 1: Queue worker
php artisan queue:work --verbose

# Terminal 2: Genera 5 atti test rapidi
php artisan tinker
```

```php
// Script rapido 5 atti
use App\Models\Egi;
use App\Models\Collection;
use App\Models\User;
use App\Jobs\TokenizePaActJob;

$user = User::where('email', 'pa@test.com')->first();
$collection = $user->collections()->where('type', 'pa_documents')->first();

if (!$collection) {
    $collection = Collection::create([
        'user_id' => $user->id,
        'name' => 'Atti Amministrativi Test',
        'type' => 'pa_documents',
        'status' => 'active',
    ]);
}

for ($i = 1; $i <= 5; $i++) {
    $egi = Egi::create([
        'collection_id' => $collection->id,
        'pa_protocol_number' => "STRESS-TEST-{$i}",
        'pa_protocol_date' => now()->subDays(rand(1,30)),
        'pa_doc_type' => 'Determina Dirigenziale',
        'pa_title' => "Atto Stress Test Numero {$i}",
        'pa_description' => "Generato automaticamente per stress test",
        'pa_document_hash' => hash('sha256', "test-content-{$i}-" . time()),
        'public_code' => 'VER-' . strtoupper(substr(md5(time() . $i), 0, 10)),
        'status' => 'active',
    ]);

    // Dispatch tokenization
    TokenizePaActJob::dispatch($egi->id);
    echo "Atto {$i}/5 creato e dispatched\n";
}

echo "\n5 atti creati. Check queue worker terminal per processing.\n";
exit;
```

**Monitor Queue Worker:**

-   Dovrebbe processare i 5 job in sequenza
-   ⏱️ Tempo medio per job: 15-25 sec
-   Zero errori

**Verifica Risultato:**

```bash
php artisan tinker
```

```php
$recentEgis = Egi::where('pa_protocol_number', 'LIKE', 'STRESS-TEST-%')->get();
echo "Atti creati: " . $recentEgis->count() . "\n";
$tokenized = $recentEgis->whereNotNull('pa_anchored_at')->count();
echo "Tokenizzati: $tokenized\n";
echo "Success rate: " . ($tokenized / $recentEgis->count() * 100) . "%\n";
exit;
```

**✅ Success Criteria:**

-   5/5 atti tokenizzati
-   Success rate 100%
-   No memory leaks
-   Queue worker stabile

---

## 🐛 TROUBLESHOOTING RAPIDO

### Problema: "Queue job failing"

```bash
# Check log specifico job
tail -100 storage/logs/laravel.log | grep TokenizePaActJob

# Possibili cause:
# 1. AlgoKit non running
docker ps | grep algokit

# 2. .env ALGORAND_* missing
grep ALGORAND .env

# 3. Rete blockchain down (raro)
curl http://localhost:4001/health  # AlgoKit health check
```

---

### Problema: "AI chat non risponde"

```bash
# Check Anthropic API key
php artisan tinker
```

```php
config('services.anthropic.api_key');  // NON vuoto
exit;
```

```bash
# Test connectivity
curl -X POST https://api.anthropic.com/v1/messages \
  -H "x-api-key: YOUR_KEY" \
  -H "anthropic-version: 2023-06-01" \
  -H "content-type: application/json" \
  -d '{"model":"claude-3-5-sonnet-20241022","max_tokens":10,"messages":[{"role":"user","content":"test"}]}'

# Se fallisce: problemi rete o API key invalida
```

---

### Problema: "Dashboard KPI errati"

```bash
# Rebuild cache
php artisan cache:clear
php artisan config:clear

# Verifica database direttamente
mysql -u root -p
```

```sql
USE florenceegi;

SELECT COUNT(*) FROM egis WHERE pa_protocol_number IS NOT NULL;
SELECT COUNT(*) FROM egis WHERE pa_anchored_at IS NOT NULL;

EXIT;
```

---

### Problema: "Pagina verifica 404"

```bash
# Check route exists
php artisan route:list | grep verify

# Check public code formato
php artisan tinker
```

```php
$egi = Egi::latest()->first();
echo "Public Code: {$egi->public_code}\n";
echo "Expected format: VER-XXXXXXXXXX\n";

// Test route
$url = route('verify.act', ['public_code' => $egi->public_code]);
echo "Full URL: $url\n";
exit;
```

---

## ✅ CHECKLIST FINALE (5 min)

Prima della presentazione, verifica:

### Funzionalità Core

-   [ ] Upload atto funzionante ✅
-   [ ] Tokenizzazione blockchain OK ✅
-   [ ] Chat AI risponde correttamente ✅
-   [ ] Verifica pubblica accessibile ✅
-   [ ] Dashboard KPI accurati ✅

### Performance

-   [ ] Upload < 5 sec ✅
-   [ ] AI chat < 5 sec ✅
-   [ ] Tokenizzazione < 30 sec ✅
-   [ ] Dashboard load < 2 sec ✅

### UI/UX

-   [ ] Colors PA corretti (blu + oro) ✅
-   [ ] Badge e status chiari ✅
-   [ ] Mobile responsive ✅
-   [ ] No broken images ✅

### Data Quality

-   [ ] 20+ atti caricati ✅
-   [ ] 15+ tokenizzati ✅
-   [ ] Tipologie varie (determine, delibere) ✅
-   [ ] Chat history con query interessanti ✅

### Logs Clean

-   [ ] Zero errori critici ✅
-   [ ] Zero failed jobs ✅
-   [ ] GDPR audit trail OK ✅

---

## 📝 REPORT RAPIDO

Dopo testing, compila:

```
╔══════════════════════════════════════════╗
║   PA + AI SYSTEM - TEST REPORT          ║
╠══════════════════════════════════════════╣
║ Data: ___/___/2025                      ║
║ Tester: Fabio Cherici                   ║
║ Durata: ___ ore                         ║
╠══════════════════════════════════════════╣
║ RISULTATI:                              ║
║                                          ║
║ ✅ Upload + Tokenizzazione: PASS        ║
║ ✅ Chat N.A.T.A.N. AI: PASS             ║
║ ✅ Verifica Pubblica: PASS              ║
║ ✅ Dashboard KPI: PASS                  ║
║ ✅ Performance: PASS                    ║
║                                          ║
╠══════════════════════════════════════════╣
║ BUG IDENTIFICATI:                       ║
║                                          ║
║ 🐛 #1: ___________________________     ║
║ 🐛 #2: ___________________________     ║
║ 🐛 #3: ___________________________     ║
║                                          ║
╠══════════════════════════════════════════╣
║ METRICHE:                               ║
║                                          ║
║ Upload speed: ___ sec                   ║
║ AI chat response: ___ sec               ║
║ Tokenizzazione: ___ sec                 ║
║ AI accuracy: ___%                       ║
║ Success rate: ___%                      ║
║                                          ║
╠══════════════════════════════════════════╣
║ STATUS PRESENTAZIONE:                   ║
║                                          ║
║ [ ] READY - Nessun blocker              ║
║ [ ] MOSTLY READY - Fix minori needed    ║
║ [ ] NOT READY - Bug critici da fixare   ║
║                                          ║
╠══════════════════════════════════════════╣
║ NOTE:                                   ║
║ _____________________________________   ║
║ _____________________________________   ║
║ _____________________________________   ║
╚══════════════════════════════════════════╝
```

---

**Salva report in:** `docs/testing/TEST_REPORT_$(date +%Y%m%d).md`

---

## 🎯 READY TO GO!

**Hai tutto quello che ti serve.**

**Ora:**

1. Apri il terminal
2. Run i comandi sopra
3. Testa sistematicamente
4. Annota problemi
5. Fixa bug critici
6. Re-test
7. Repeat until READY

**Fra 4-5 giorni, presenterai qualcosa di:**

-   ✅ Funzionante
-   ✅ Professionale
-   ✅ Impressionante

**Let's go! 🚀**
