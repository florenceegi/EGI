# 🐘 Piano di Migrazione da MySQL/MariaDB a PostgreSQL

## Indice

1. [Analisi dello Stato Attuale](#1-analisi-dello-stato-attuale)
2. [Prerequisiti](#2-prerequisiti)
3. [Differenze Critiche MySQL vs PostgreSQL](#3-differenze-critiche-mysql-vs-postgresql)
4. [Modifiche alle Migration](#4-modifiche-alle-migration)
5. [Modifiche ai Model](#5-modifiche-ai-model)
6. [Modifiche alle Query Raw](#6-modifiche-alle-query-raw)
7. [Gestione degli ENUM](#7-gestione-degli-enum)
8. [Gestione degli Indici](#8-gestione-degli-indici)
9. [Migrazione dei Dati](#9-migrazione-dei-dati)
10. [Test e Validazione](#10-test-e-validazione)
11. [Deploy in Produzione](#11-deploy-in-produzione)
12. [Checklist Completa](#12-checklist-completa)

---

## 1. Analisi dello Stato Attuale

### 📊 Statistiche del Progetto

| Elemento             | Quantità       | Note                                       |
| -------------------- | -------------- | ------------------------------------------ |
| **Migration**        | 184 file       | Molte con sintassi MySQL-specifica         |
| **Colonne ENUM**     | 131 occorrenze | Da convertire in STRING + CHECK constraint |
| **Colonne JSON**     | 178 occorrenze | PostgreSQL supporta JSONB (più potente)    |
| **Query Raw**        | 123 occorrenze | Da verificare sintassi                     |
| **UNSIGNED columns** | 155 occorrenze | PostgreSQL non supporta UNSIGNED           |
| **FULLTEXT indexes** | 3 occorrenze   | Da convertire in GIN/GIST                  |
| **DB::statement**    | 53 occorrenze  | Molte con sintassi MySQL pura              |
| **Model**            | 98 file        | Da verificare cast e relazioni             |
| **ULID/UUID**        | 9 occorrenze   | PostgreSQL supporta nativamente UUID       |

### 🔴 Funzioni MySQL-Specifiche Trovate

```
DATE_FORMAT()     → PostgreSQL: TO_CHAR()
NOW()             → PostgreSQL: NOW() ✅ (compatibile)
CURDATE()         → PostgreSQL: CURRENT_DATE
UNIX_TIMESTAMP()  → PostgreSQL: EXTRACT(EPOCH FROM ...)
IFNULL()          → PostgreSQL: COALESCE()
IF()              → PostgreSQL: CASE WHEN
MATCH...AGAINST   → PostgreSQL: to_tsvector/to_tsquery (Full Text Search)
GROUP_CONCAT()    → PostgreSQL: STRING_AGG()
CONCAT_WS()       → PostgreSQL: CONCAT_WS() ✅ (compatibile)
FIND_IN_SET()     → PostgreSQL: ANY(string_to_array())
```

---

## 2. Prerequisiti

### 2.1 Ambiente di Sviluppo

-   [ ] **PHP Extension pgsql**

    ```bash
    # Ubuntu/Debian
    sudo apt-get install php8.2-pgsql

    # Verifica
    php -m | grep pgsql
    ```

-   [ ] **PostgreSQL Server** (locale per test)

    ```bash
    # Ubuntu/Debian
    sudo apt-get install postgresql postgresql-contrib

    # Verifica versione (consigliata 15+)
    psql --version
    ```

-   [ ] **Doctrine DBAL** (per modifiche colonne)
    ```bash
    composer require doctrine/dbal
    ```

### 2.2 Ambiente di Produzione (Forge)

-   [ ] Creare database PostgreSQL su Forge
-   [ ] Configurare credenziali nel `.env`
-   [ ] Verificare connettività

### 2.3 Configurazione Laravel

File `.env` per PostgreSQL:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=florenceegi
DB_USERNAME=forge
DB_PASSWORD=your_password
DB_CHARSET=utf8
```

File `config/database.php` - già configurato ✅

---

## 3. Differenze Critiche MySQL vs PostgreSQL

### 3.1 Tipi di Dato

| MySQL             | PostgreSQL         | Note                             |
| ----------------- | ------------------ | -------------------------------- |
| `TINYINT(1)`      | `BOOLEAN`          | Laravel gestisce automaticamente |
| `INT UNSIGNED`    | `INTEGER` + CHECK  | PostgreSQL non ha UNSIGNED       |
| `BIGINT UNSIGNED` | `BIGINT` + CHECK   | Usare CHECK (value >= 0)         |
| `ENUM('a','b')`   | `VARCHAR` + CHECK  | O creare tipo ENUM custom        |
| `TEXT`            | `TEXT`             | ✅ Compatibile                   |
| `LONGTEXT`        | `TEXT`             | PostgreSQL non differenzia       |
| `JSON`            | `JSONB`            | JSONB è più performante          |
| `DATETIME`        | `TIMESTAMP`        | Laravel gestisce                 |
| `DOUBLE`          | `DOUBLE PRECISION` | Laravel gestisce                 |

### 3.2 Indici

| MySQL      | PostgreSQL              | Note                      |
| ---------- | ----------------------- | ------------------------- |
| `FULLTEXT` | `GIN` con `to_tsvector` | Richiede colonna tsvector |
| `INDEX`    | `INDEX`                 | ✅ Compatibile            |
| `UNIQUE`   | `UNIQUE`                | ✅ Compatibile            |
| `SPATIAL`  | `GIST`                  | Per dati geografici       |

### 3.3 Autoincrement

| MySQL            | PostgreSQL             |
| ---------------- | ---------------------- |
| `AUTO_INCREMENT` | `SERIAL` / `BIGSERIAL` |

Laravel gestisce automaticamente con `$table->id()`.

### 3.4 Case Sensitivity

-   **MySQL**: Case-insensitive per default
-   **PostgreSQL**: Case-sensitive per default

Query come `WHERE name = 'John'` potrebbero non trovare 'john'.

**Soluzione**: Usare `ILIKE` invece di `LIKE`, o `LOWER()`.

---

## 4. Modifiche alle Migration

### 4.1 Pattern per ENUM → STRING

**Prima (MySQL):**

```php
$table->enum('status', ['pending', 'active', 'completed']);
```

**Dopo (Multi-database):**

```php
// Opzione 1: String con valori in applicazione
$table->string('status', 20)->default('pending');

// Opzione 2: Con CHECK constraint (più sicuro)
$table->string('status', 20)->default('pending');
// Nel metodo up(), dopo la creazione della tabella:
if (DB::getDriverName() === 'pgsql') {
    DB::statement("ALTER TABLE table_name ADD CONSTRAINT check_status
                   CHECK (status IN ('pending', 'active', 'completed'))");
}
```

### 4.2 Pattern per Query Raw MySQL-Specifiche

**Prima:**

```php
DB::statement("ALTER TABLE users MODIFY COLUMN status ENUM('a','b','c')");
```

**Dopo:**

```php
$driver = DB::getDriverName();
if ($driver === 'mysql' || $driver === 'mariadb') {
    DB::statement("ALTER TABLE users MODIFY COLUMN status ENUM('a','b','c')");
} elseif ($driver === 'pgsql') {
    // PostgreSQL approach
    DB::statement("ALTER TABLE users ALTER COLUMN status TYPE VARCHAR(20)");
    DB::statement("ALTER TABLE users ADD CONSTRAINT check_status
                   CHECK (status IN ('a','b','c'))");
}
```

### 4.3 Migration con UNSIGNED

**Prima:**

```php
$table->unsignedBigInteger('amount');
```

**Dopo (già gestito da Laravel):**

```php
$table->unsignedBigInteger('amount'); // Laravel crea CHECK constraint per PostgreSQL
```

### 4.4 FULLTEXT Index

**Prima:**

```php
DB::statement('ALTER TABLE egi_acts ADD FULLTEXT fulltext_oggetto (oggetto)');
```

**Dopo:**

```php
$driver = DB::getDriverName();
if ($driver === 'mysql' || $driver === 'mariadb') {
    DB::statement('ALTER TABLE egi_acts ADD FULLTEXT fulltext_oggetto (oggetto)');
} elseif ($driver === 'pgsql') {
    // Aggiungi colonna tsvector
    DB::statement('ALTER TABLE egi_acts ADD COLUMN oggetto_tsv tsvector');
    DB::statement('CREATE INDEX idx_egi_acts_oggetto ON egi_acts USING GIN(oggetto_tsv)');
    // Crea trigger per aggiornare tsvector
    DB::statement("CREATE OR REPLACE FUNCTION update_oggetto_tsv() RETURNS trigger AS $$
        BEGIN
            NEW.oggetto_tsv := to_tsvector('italian', COALESCE(NEW.oggetto, ''));
            RETURN NEW;
        END;
    $$ LANGUAGE plpgsql");
    DB::statement('CREATE TRIGGER trig_oggetto_tsv BEFORE INSERT OR UPDATE ON egi_acts
                   FOR EACH ROW EXECUTE FUNCTION update_oggetto_tsv()');
}
```

---

## 5. Modifiche ai Model

### 5.1 Cast per ENUM

Se usi PHP Enum backed con valore string, funziona con entrambi i DB.

```php
// app/Enums/StatusEnum.php
enum StatusEnum: string {
    case PENDING = 'pending';
    case ACTIVE = 'active';
}

// Model
protected $casts = [
    'status' => StatusEnum::class,
];
```

### 5.2 Cast per JSON/JSONB

PostgreSQL usa JSONB che è più potente. Laravel gestisce automaticamente.

```php
protected $casts = [
    'metadata' => 'array', // Funziona con entrambi
];
```

---

## 6. Modifiche alle Query Raw

### 6.1 File da Modificare

Basato sull'analisi, questi file contengono query MySQL-specifiche:

| File                                            | Funzione MySQL                          | Azione                  |
| ----------------------------------------------- | --------------------------------------- | ----------------------- |
| `app/Services/DualTrackingAnalyticsService.php` | `DATE_FORMAT`                           | Convertire in `TO_CHAR` |
| `app/Models/PaymentDistribution.php`            | `DATE_FORMAT`, `GROUP_CONCAT`, `NULLIF` | Convertire              |
| `app/Models/EgiAct.php`                         | `MATCH...AGAINST`                       | Usare `to_tsvector`     |
| `app/Console/Commands/*`                        | `NOW()`                                 | ✅ Compatibile          |

### 6.2 Helper per Query Cross-Database

Creare helper per funzioni comuni:

```php
// app/Helpers/DatabaseHelper.php
namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class DatabaseHelper
{
    public static function dateFormat(string $column, string $format): string
    {
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            // Converti formato MySQL in PostgreSQL
            $pgFormat = str_replace(
                ['%Y', '%m', '%d', '%H', '%i', '%s'],
                ['YYYY', 'MM', 'DD', 'HH24', 'MI', 'SS'],
                $format
            );
            return "TO_CHAR({$column}, '{$pgFormat}')";
        }

        return "DATE_FORMAT({$column}, '{$format}')";
    }

    public static function groupConcat(string $column, string $separator = ','): string
    {
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            return "STRING_AGG({$column}::TEXT, '{$separator}')";
        }

        return "GROUP_CONCAT({$column} SEPARATOR '{$separator}')";
    }

    public static function ifNull(string $column, string $default): string
    {
        return "COALESCE({$column}, {$default})";
    }
}
```

---

## 7. Gestione degli ENUM

### 7.1 Strategia Consigliata

**Opzione A: Convertire tutti gli ENUM in STRING** ⭐ Raccomandata

Vantaggi:

-   Più flessibile
-   Meno migration problematiche
-   Validazione in applicazione (con PHP Enum)

**Opzione B: Creare tipi ENUM PostgreSQL**

```sql
CREATE TYPE status_enum AS ENUM ('pending', 'active', 'completed');
```

Svantaggi:

-   Aggiungere valori richiede `ALTER TYPE`
-   Più complesso da gestire

### 7.2 Elenco ENUM da Convertire

Migration con ENUM che richiedono modifica:

1. `2025_06_10_125504_create_gdpr_notification_payloads_table.php` - `payload_status`
2. `2025_11_09_143115_create_tenants_table_if_missing.php` - `entity_type`
3. `2025_08_30_080211_create_trait_types_table.php` - `display_type`
4. `2025_10_09_105125_extend_payment_distributions_for_mint_tracking.php` - `source_type`
5. `2025_10_22_102000_create_ai_feature_pricing_table.php` - `feature_category`, `min_tier_required`, `recurrence_period`
6. `2024_01_15_000003_create_data_retention_policies_table.php` - `retention_trigger`, `deletion_method`, `risk_level`
7. ... (tutti i 131 enum)

---

## 8. Gestione degli Indici

### 8.1 Indici Standard

Gli indici standard (`index`, `unique`, `primary`) sono compatibili.

### 8.2 Indici FULLTEXT → GIN

| Tabella             | Colonna                            | Azione            |
| ------------------- | ---------------------------------- | ----------------- |
| `security_events`   | `description`                      | Convertire in GIN |
| `consent_histories` | `reason_for_action`, `admin_notes` | Convertire in GIN |
| `egi_acts`          | `oggetto`                          | Convertire in GIN |

### 8.3 Indici con Espressioni

PostgreSQL supporta indici su espressioni:

```sql
CREATE INDEX idx_lower_name ON users (LOWER(name));
```

---

## 9. Migrazione dei Dati

### 9.1 Tool Consigliati

1. **pgloader** - Tool automatico MySQL → PostgreSQL

    ```bash
    pgloader mysql://user:pass@localhost/florenceegi postgresql://user:pass@localhost/florenceegi_pg
    ```

2. **Laravel Migration Manuale**
    - Esportare con `mysqldump`
    - Importare con script custom

### 9.2 Procedura Consigliata

1. **Fase 1: Dual-Write**

    - Scrivi su entrambi i database
    - Leggi da MySQL
    - Verifica consistenza

2. **Fase 2: Switch**

    - Leggi da PostgreSQL
    - Scrivi su entrambi

3. **Fase 3: Deprecation**
    - Rimuovi MySQL

### 9.3 Script di Migrazione Dati

```php
// database/seeders/PostgresMigrationSeeder.php
class PostgresMigrationSeeder extends Seeder
{
    public function run()
    {
        // Disabilita foreign key
        DB::statement('SET CONSTRAINTS ALL DEFERRED');

        // Migra tabella per tabella
        $tables = ['users', 'collections', 'egis', ...];

        foreach ($tables as $table) {
            $this->migrateTable($table);
        }

        // Riabilita foreign key
        DB::statement('SET CONSTRAINTS ALL IMMEDIATE');
    }

    private function migrateTable(string $table): void
    {
        // Leggi da MySQL
        $mysqlData = DB::connection('mysql')->table($table)->get();

        // Scrivi su PostgreSQL
        DB::connection('pgsql')->table($table)->insert($mysqlData->toArray());
    }
}
```

---

## 10. Test e Validazione

### 10.1 Test Automatici

-   [ ] Tutti i test esistenti passano con PostgreSQL
-   [ ] Creare test specifici per funzioni convertite

### 10.2 Test Manuali

-   [ ] Login/Registrazione
-   [ ] CRUD su tutte le entità principali
-   [ ] Upload file (Spatie Media)
-   [ ] Ricerca fulltext
-   [ ] Report e analytics

### 10.3 Performance

-   [ ] Verificare query lente con `EXPLAIN ANALYZE`
-   [ ] Ottimizzare indici se necessario

---

## 11. Deploy in Produzione

### 11.1 Pre-Deploy

-   [ ] Backup completo MySQL
-   [ ] Verificare spazio disco per PostgreSQL
-   [ ] Preparare rollback plan

### 11.2 Deploy

1. [ ] Mettere sito in maintenance mode
2. [ ] Esportare dati da MySQL
3. [ ] Importare dati in PostgreSQL
4. [ ] Aggiornare `.env` con credenziali PostgreSQL
5. [ ] Eseguire `php artisan migrate --force`
6. [ ] Verificare applicazione
7. [ ] Uscire da maintenance mode

### 11.3 Post-Deploy

-   [ ] Monitorare errori
-   [ ] Verificare performance
-   [ ] Mantenere MySQL in standby per 1 settimana

---

## 12. Checklist Completa

### 🔧 Preparazione Ambiente

-   [ ] Installare estensione PHP pgsql (sviluppo)
-   [ ] Installare PostgreSQL locale (sviluppo)
-   [ ] Installare `doctrine/dbal` via Composer
-   [ ] Creare database PostgreSQL su Forge
-   [ ] Configurare `.env` per PostgreSQL

### 📝 Modifiche Codice - Migration

-   [ ] Creare nuova migration per convertire tutti ENUM in STRING
-   [ ] Aggiungere gestione multi-driver a migration con `DB::statement`
-   [ ] Convertire indici FULLTEXT in GIN
-   [ ] Verificare foreign key con ULID

### 📝 Modifiche Codice - Application

-   [ ] Creare `DatabaseHelper` per funzioni cross-database
-   [ ] Modificare `DualTrackingAnalyticsService.php` - `DATE_FORMAT`
-   [ ] Modificare `PaymentDistribution.php` - `DATE_FORMAT`, `GROUP_CONCAT`
-   [ ] Modificare `EgiAct.php` - `MATCH...AGAINST`
-   [ ] Verificare tutti i file con query raw (123 occorrenze)

### ✅ Test

-   [ ] Eseguire test suite con SQLite
-   [ ] Eseguire test suite con PostgreSQL locale
-   [ ] Test manuali funzionalità critiche

### 🚀 Migrazione Dati

-   [ ] Esportare dati da MySQL produzione
-   [ ] Testare import su PostgreSQL staging
-   [ ] Verificare integrità dati

### 🌐 Deploy

-   [ ] Pianificare finestra di manutenzione
-   [ ] Eseguire migrazione
-   [ ] Verificare applicazione
-   [ ] Monitorare per 48h

---

## Appendice A: Comandi Utili PostgreSQL

```bash
# Connessione
psql -U username -d database_name

# Lista database
\l

# Lista tabelle
\dt

# Descrivi tabella
\d table_name

# Esegui query da file
psql -U username -d database_name -f script.sql

# Backup
pg_dump -U username database_name > backup.sql

# Restore
psql -U username -d database_name < backup.sql
```

## Appendice B: Mapping Funzioni MySQL → PostgreSQL

| MySQL                        | PostgreSQL                             | Note               |
| ---------------------------- | -------------------------------------- | ------------------ |
| `DATE_FORMAT(col, '%Y-%m')`  | `TO_CHAR(col, 'YYYY-MM')`              | Formato diverso    |
| `NOW()`                      | `NOW()`                                | ✅ Identico        |
| `CURDATE()`                  | `CURRENT_DATE`                         |                    |
| `CURRENT_TIMESTAMP()`        | `CURRENT_TIMESTAMP`                    |                    |
| `UNIX_TIMESTAMP()`           | `EXTRACT(EPOCH FROM NOW())`            |                    |
| `FROM_UNIXTIME(ts)`          | `TO_TIMESTAMP(ts)`                     |                    |
| `IFNULL(a, b)`               | `COALESCE(a, b)`                       |                    |
| `NULLIF(a, b)`               | `NULLIF(a, b)`                         | ✅ Identico        |
| `IF(cond, a, b)`             | `CASE WHEN cond THEN a ELSE b END`     |                    |
| `CONCAT(a, b)`               | `CONCAT(a, b)`                         | ✅ Identico        |
| `CONCAT_WS(',', a, b)`       | `CONCAT_WS(',', a, b)`                 | ✅ Identico        |
| `GROUP_CONCAT(col)`          | `STRING_AGG(col::TEXT, ',')`           |                    |
| `GROUP_CONCAT(DISTINCT col)` | `STRING_AGG(DISTINCT col::TEXT, ',')`  |                    |
| `FIND_IN_SET(val, col)`      | `val = ANY(STRING_TO_ARRAY(col, ','))` |                    |
| `MATCH(col) AGAINST(term)`   | `col @@ to_tsquery(term)`              | Richiede tsvector  |
| `LIMIT n OFFSET m`           | `LIMIT n OFFSET m`                     | ✅ Identico        |
| `AUTO_INCREMENT`             | `SERIAL` / `BIGSERIAL`                 | Laravel gestisce   |
| `TINYINT(1)`                 | `BOOLEAN`                              | Laravel gestisce   |
| `MEDIUMTEXT`                 | `TEXT`                                 |                    |
| `LONGTEXT`                   | `TEXT`                                 |                    |
| `DOUBLE`                     | `DOUBLE PRECISION`                     |                    |
| `UNSIGNED`                   | CHECK constraint                       | `CHECK (col >= 0)` |

---

**Documento creato il**: 6 Dicembre 2025  
**Autore**: FlorenceEGI Team  
**Versione**: 1.0
