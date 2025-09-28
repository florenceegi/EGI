# 🚀 Script di Deployment Laravel Forge - Configurazione

## 📋 Panoramica

Questo documento descrive la configurazione e il funzionamento dello script di deployment automatico per l'ambiente Laravel Forge del progetto Florence EGI.

## 🎯 Obiettivo

Lo script automatizza il processo di deployment su server Forge, garantendo:

-   Aggiornamento sicuro del codice
-   Configurazione corretta delle dipendenze
-   Gestione intelligente delle migrazioni
-   Ottimizzazione delle performance

## 📁 Struttura Script

### 🔧 Variabili di Ambiente Forge

Lo script utilizza le seguenti variabili Forge predefinite:

```bash
FORGE_SITE_BRANCH     # Branch da deployare (es. main, staging)
FORGE_COMPOSER        # Comando Composer personalizzato (opzionale)
FORGE_PHP            # Versione PHP da utilizzare (opzionale)
```

### 📍 Directory di Lavoro

```bash
SITE_DIR="/home/forge/default"
```

**Nota**: Per progetti multi-sito, questa variabile può essere modificata per puntare alla directory specifica.

## 🔄 Processo di Deployment

### 1. **Verifica Ambiente** ✅

```bash
# Controllo esistenza file .env
if [ ! -f ".env" ]; then
  echo "ERRORE: manca .env (Forge → Environment)."
  exit 1
fi
```

### 2. **Aggiornamento Codice** 🔄

```bash
# Fetch e reset hard del branch specificato
git fetch origin "$FORGE_SITE_BRANCH" --prune
git reset --hard "origin/$FORGE_SITE_BRANCH"
```

### 3. **Installazione Dipendenze** 📦

```bash
# Composer install ottimizzato per produzione
$COMPOSER_CMD install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts
```

**Parametri Composer**:

-   `--no-dev`: Esclude dipendenze di sviluppo
-   `--no-interaction`: Modalità non interattiva
-   `--prefer-dist`: Preferisce pacchetti distribuiti
-   `--optimize-autoloader`: Ottimizza autoloader PSR-4
-   `--no-scripts`: Disabilita script post-installazione

### 4. **Configurazione Permessi** 🔐

```bash
# Creazione directory necessarie
mkdir -p bootstrap/cache storage/logs

# Impostazione proprietario e permessi
chown -R forge:forge storage bootstrap/cache
chmod -R ug+rw storage bootstrap/cache
```

### 5. **Gestione APP_KEY** 🗝️

```bash
# Generazione automatica se mancante
if ! grep -q "^APP_KEY=base64:" .env; then
  $PHP_CMD artisan key:generate --force
fi
```

### 6. **Storage Link** 🔗

```bash
# Creazione link simbolico per storage pubblico
$PHP_CMD artisan storage:link || true
```

### 7. **Migrazioni Intelligenti** 🗄️

Lo script implementa un sistema di **migrazioni on-demand** basato sulla configurazione:

#### **Sessions Database**

```bash
if [ "${ENV_SESSION_DRIVER:-file}" = "database" ]; then
  if [ "$(have_table sessions || echo 0)" != "1" ]; then
    echo "   - Genero migration sessions"
    $PHP_CMD artisan session:table || true
  fi
fi
```

#### **Queue Database**

```bash
if [ "${ENV_QUEUE_CONNECTION:-sync}" = "database" ]; then
  if [ "$(have_table jobs || echo 0)" != "1" ]; then
    echo "   - Genero migration jobs"
    $PHP_CMD artisan queue:table || true
  fi
fi
```

**Funzione `have_table`**:

```bash
have_table () {
  $PHP_CMD -r '
  use Illuminate\Support\Facades\Schema;
  require "vendor/autoload.php";
  $app = require "bootstrap/app.php";
  $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
  echo Schema::hasTable($argv[1]) ? "1" : "0";
  ' "$1"
}
```

### 8. **Esecuzione Migrazioni** 🚀

```bash
# Migrazione forzata con gestione errori
$PHP_CMD artisan migrate --force || {
  echo "ATTENZIONE: migrate ha dato errore. Controlla storage/logs/laravel.log"
}
```

### 9. **Ottimizzazione Cache** ⚡

#### **Pulizia Cache**

```bash
rm -f bootstrap/cache/*.php || true
$PHP_CMD artisan config:clear
$PHP_CMD artisan cache:clear
$PHP_CMD artisan route:clear
$PHP_CMD artisan view:clear
```

#### **Ricostruzione Cache Ottimizzata**

```bash
$PHP_CMD artisan config:cache
$PHP_CMD artisan route:cache
$PHP_CMD artisan view:cache || true
```

## 🔧 Configurazione Personalizzata

### **Composer Personalizzato**

```bash
# Esempio per PHP 8.3
FORGE_COMPOSER="php8.3 /usr/local/bin/composer"
```

### **PHP Personalizzato**

```bash
# Esempio per versione specifica
FORGE_PHP="/usr/bin/php8.3"
```

## 📊 Variabili d'Ambiente Critiche

Basandosi sul file `.env_forge`, lo script gestisce:

### **Database**

```env
DB_CONNECTION=mariadb
DB_HOST=localhost
DB_DATABASE=forge
DB_USERNAME=forge
```

### **Sessions**

```env
SESSION_DRIVER=database  # Trigger per migration sessions
```

### **Queue**

```env
QUEUE_CONNECTION=database  # Trigger per migration jobs
```

### **Cache & Storage**

```env
CACHE_DRIVER=redis
FILESYSTEM_DISK=public
```

## 🚨 Gestione Errori

### **Errori Critici**

-   **Manca .env**: Script termina con exit 1
-   **Git fetch fallisce**: Reset hard può fallire
-   **Composer install**: Dipendenze mancanti o conflitti

### **Errori Non Critici**

-   **Storage link esistente**: Continuazione normale
-   **Migration errors**: Log in `storage/logs/laravel.log`
-   **View cache**: Fallback silenzioso

## 📈 Ottimizzazioni Performance

### **Composer**

-   Autoloader ottimizzato per PSR-4
-   Pacchetti distribuiti invece di source
-   Esclusione dipendenze sviluppo

### **Laravel Cache**

-   Config cache per accesso rapido
-   Route cache per routing ottimizzato
-   View cache per template compilati

### **Database**

-   Migrazioni solo se necessarie
-   Controllo esistenza tabelle
-   Generazione on-demand

## 🔍 Monitoraggio

### **Log Files**

-   `storage/logs/laravel.log`: Errori applicazione
-   Log Forge: Output script deployment
-   Git log: Storico deployment

### **Health Checks**

-   Verifica esistenza `.env`
-   Controllo permessi directory
-   Validazione cache Laravel

## 🛠️ Personalizzazioni Avanzate

### **Multi-Environment**

```bash
# Adattamento per diversi ambienti
case "$FORGE_SITE_BRANCH" in
  "main")
    SITE_DIR="/home/forge/production"
    ;;
  "staging")
    SITE_DIR="/home/forge/staging"
    ;;
esac
```

### **Backup Pre-Deploy**

```bash
# Aggiunta backup prima del reset
tar -czf "/tmp/backup-$(date +%Y%m%d-%H%M%S).tar.gz" storage/
```

### **Rollback Automatico**

```bash
# Rollback in caso di errore critico
if [ $? -ne 0 ]; then
  git reset --hard HEAD~1
  echo "Rollback eseguito"
fi
```

## 📋 Checklist Pre-Deploy

-   [ ] File `.env` configurato correttamente
-   [ ] Variabili Forge impostate
-   [ ] Branch da deployare aggiornato
-   [ ] Migrazioni testate in locale
-   [ ] Cache pulita in locale
-   [ ] Backup database eseguito

## 🎯 Best Practices

1. **Test in Staging**: Sempre testare in ambiente staging prima di production
2. **Backup Regolari**: Backup automatici prima di deployment critici
3. **Monitoring**: Monitoraggio post-deploy per 15-30 minuti
4. **Rollback Plan**: Piano di rollback sempre pronto
5. **Log Analysis**: Analisi regolare dei log per ottimizzazioni

---

**Documento creato**: $(date)
**Versione**: 1.0
**Progetto**: Florence EGI
**Ambiente**: Laravel Forge Production
