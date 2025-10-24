# 📦 Export/Import Scrapers su Staging

> Guida per trasferire le configurazioni degli scrapers da locale a staging

---

## 🎯 OBIETTIVO

Trasferire i **record di `pa_web_scrapers`** (configurazioni scraping) dal tuo ambiente locale al server staging, così puoi eseguire gli scraping direttamente su staging per testare N.A.T.A.N. con dati reali.

---

## ⚡ PROCEDURA RAPIDA (5 minuti)

### **STEP 1: Export da Locale** ✅ (GIÀ FATTO!)

```bash
# Sul tuo computer (locale)
cd /home/fabio/EGI
php artisan pa:export-scrapers
```

**Output:**

```
✅ Scrapers exported to: storage/app/scrapers_export.json
```

**File esportato:** `/home/fabio/EGI/storage/app/scrapers_export.json`

---

### **STEP 2: Copia File su Staging**

```bash
# Dal tuo computer locale
scp storage/app/scrapers_export.json forge@app.13.48.57.194.sslip.io:/home/forge/app.13.48.57.194.sslip.io/storage/app/
```

**Sostituisci:**

-   `forge` → username SSH del server
-   `app.13.48.57.194.sslip.io` → percorso corretto su staging

**Alternative se SCP non funziona:**

**Opzione A - Via contenuto file:**

```bash
# 1. Copia il contenuto del file
cat storage/app/scrapers_export.json

# 2. SSH su staging
ssh forge@app.13.48.57.194.sslip.io

# 3. Crea il file su staging
cd /home/forge/app.13.48.57.194.sslip.io
nano storage/app/scrapers_export.json
# Incolla il contenuto
# Ctrl+X → Y → Enter
```

**Opzione B - Via SFTP (FileZilla, WinSCP, etc.):**

1. Connettiti via SFTP al server staging
2. Naviga su `/home/forge/app.13.48.57.194.sslip.io/storage/app/`
3. Upload `scrapers_export.json`

---

### **STEP 3: Import su Staging**

```bash
# SSH su staging
ssh forge@app.13.48.57.194.sslip.io
cd /home/forge/app.13.48.57.194.sslip.io

# Import scrapers
php artisan pa:import-scrapers
```

**Output atteso:**

```
📥 Reading scrapers from: scrapers_export.json
📦 Found 3 scrapers to import
  ✅ Imported: Delibere Comune di Firenze
  ✅ Imported: Albo Pretorio Comune di Firenze
  ✅ Imported: Delibere Comune di Firenze

✅ Import completed! Imported 3/3 scrapers
```

---

## 🧪 VERIFICA IMPORT

```bash
# Su staging
php artisan tinker
```

```php
// Conta scrapers importati
\App\Models\PaWebScraper::count()
// Expected: 3

// Mostra scrapers
\App\Models\PaWebScraper::all(['id', 'name', 'base_url', 'type'])
```

---

## 🚀 ESEGUI SCRAPING SU STAGING

Ora puoi eseguire gli scraping direttamente su staging:

### **Opzione A: Da Command Line**

```bash
# Su staging
php artisan pa:scrape-all
```

### **Opzione B: Da Interfaccia Web**

1. Login su staging: `https://app.13.48.57.194.sslip.io/pa/login`
2. Vai su: `/pa/scrapers`
3. Click "Run Scraper" su uno degli scrapers

---

## 🔄 SE VUOI PULIRE E REIMPORTARE

```bash
# Su staging - Delete tutti gli scrapers esistenti e importa da zero
php artisan pa:import-scrapers --clean
```

**⚠️ WARNING:** Questo cancella TUTTI gli scrapers esistenti prima di importare!

---

## 📊 COSA CONTIENE IL FILE ESPORTATO

Il file `scrapers_export.json` contiene:

```json
[
    {
        "name": "Delibere Comune di Firenze",
        "base_url": "https://accessoconcertificato.comune.fi.it",
        "type": "api",
        "config": null,
        "pa_entity_id": null,
        "is_active": true,
        "schedule": null,
        "last_scrape_at": null,
        "last_error": null
    }
    // ... altri scrapers
]
```

**Campi esportati:**

-   `name`: Nome scraper
-   `base_url`: URL base da scrappare
-   `type`: Tipo scraper (api/html)
-   `config`: Configurazioni JSON (selettori, filtri, etc.)
-   `pa_entity_id`: ID ente PA (null se pubblico)
-   `is_active`: Scraper attivo/disattivo
-   `schedule`: Scheduling automatico (cron)
-   `last_scrape_at`: Ultimo scraping eseguito
-   `last_error`: Ultimo errore (se presente)

---

## 🛠️ OPZIONI AVANZATE

### **Export con nome custom:**

```bash
php artisan pa:export-scrapers --output=firenze_scrapers.json
```

### **Import da file custom:**

```bash
php artisan pa:import-scrapers --input=firenze_scrapers.json
```

---

## 🐛 TROUBLESHOOTING

### **Problema: "File not found"**

```bash
# Verifica che il file esista
ls -la storage/app/scrapers_export.json

# Se manca, ri-esegui export:
php artisan pa:export-scrapers
```

### **Problema: "Permission denied" (SCP)**

```bash
# Verifica connessione SSH:
ssh forge@app.13.48.57.194.sslip.io "pwd"

# Se SSH funziona, verifica permessi:
ssh forge@app.13.48.57.194.sslip.io "ls -ld /home/forge/app.13.48.57.194.sslip.io/storage/app/"
```

### **Problema: "Class 'PaWebScraper' not found"**

```bash
# Clear cache autoloader:
composer dump-autoload
php artisan config:clear
```

---

## ✅ CHECKLIST COMPLETA

**Sul computer locale:**

-   [ ] `php artisan pa:export-scrapers` eseguito
-   [ ] File `storage/app/scrapers_export.json` generato
-   [ ] Comandi committati e pushati su git

**Sul server staging:**

-   [ ] Codice aggiornato (`git pull origin main`)
-   [ ] File `scrapers_export.json` copiato su staging
-   [ ] `php artisan pa:import-scrapers` eseguito
-   [ ] Scrapers verificati (`tinker` o UI)
-   [ ] Scraping test eseguito con successo

---

## 🎯 PROSSIMI STEP

Dopo aver importato gli scrapers su staging:

1. **Esegui scraping:** `php artisan pa:scrape-all`
2. **Genera embeddings:** `php artisan pa:generate-embeddings --limit=100`
3. **Testa N.A.T.A.N.:** `/pa/natan/chat`

---

**Fatto! 🎉** Gli scrapers sono ora configurati su staging e puoi testare il sistema completo!
