# 🔧 File con Query Raw MySQL da Modificare

Questo documento elenca tutti i file che contengono query SQL MySQL-specifiche che devono essere convertite per la compatibilità con PostgreSQL.

---

## 📍 File Critici da Modificare

### 1. `app/Services/DualTrackingAnalyticsService.php`

**Problema**: `DATE_FORMAT()` (riga 314)

```php
// ATTUALE (MySQL)
->selectRaw("DATE_FORMAT(created_at, ?) as period", [$dateFormat])

// CONVERTIRE IN (Cross-database)
use App\Helpers\DatabaseHelper;

// Nel metodo
$dateFormatSql = DatabaseHelper::dateFormat('created_at', $dateFormat);
->selectRaw("{$dateFormatSql} as period")
```

**Mapping formati DATE_FORMAT MySQL → TO_CHAR PostgreSQL:**

-   `%Y` → `YYYY` (anno 4 cifre)
-   `%m` → `MM` (mese 2 cifre)
-   `%d` → `DD` (giorno 2 cifre)
-   `%H` → `HH24` (ora 24h)
-   `%i` → `MI` (minuti)
-   `%s` → `SS` (secondi)

---

### 2. `app/Models/PaymentDistribution.php`

**Problema**: `GROUP_CONCAT()` (riga 938)

```php
// ATTUALE (MySQL)
GROUP_CONCAT(DISTINCT collection_user.role) as roles_held

// CONVERTIRE IN (Cross-database)
use Illuminate\Support\Facades\DB;

$groupConcatSql = DB::getDriverName() === 'pgsql'
    ? "STRING_AGG(DISTINCT collection_user.role, ',')"
    : "GROUP_CONCAT(DISTINCT collection_user.role)";

// Usare $groupConcatSql nella query
```

**Nota aggiuntiva**: Verificare se ci sono altri `GROUP_CONCAT` nel file.

---

### 3. `app/Models/EgiAct.php`

**Problema**: `MATCH...AGAINST` Full Text Search (riga 222)

```php
// ATTUALE (MySQL)
'MATCH(oggetto) AGAINST(? IN BOOLEAN MODE)'

// CONVERTIRE IN (Cross-database)
use Illuminate\Support\Facades\DB;

$driver = DB::getDriverName();
if ($driver === 'pgsql') {
    // PostgreSQL Full Text Search
    $whereClause = "oggetto_tsv @@ plainto_tsquery('italian', ?)";
    // NOTA: Richiede colonna tsvector 'oggetto_tsv' nel database
} else {
    // MySQL/MariaDB
    $whereClause = 'MATCH(oggetto) AGAINST(? IN BOOLEAN MODE)';
}
```

**Prerequisito PostgreSQL**: Creare colonna `tsvector` e trigger:

```sql
ALTER TABLE egi_acts ADD COLUMN oggetto_tsv tsvector;
CREATE INDEX idx_egi_acts_fts ON egi_acts USING GIN(oggetto_tsv);

CREATE FUNCTION update_oggetto_tsv() RETURNS trigger AS $$
BEGIN
    NEW.oggetto_tsv := to_tsvector('italian', COALESCE(NEW.oggetto, ''));
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trig_egi_acts_tsv
BEFORE INSERT OR UPDATE ON egi_acts
FOR EACH ROW EXECUTE FUNCTION update_oggetto_tsv();
```

---

## 📋 Checklist File Critici

-   [ ] `app/Services/DualTrackingAnalyticsService.php`

    -   [ ] Convertire `DATE_FORMAT` → `TO_CHAR` (o helper)
    -   [ ] Verificare altri usi di funzioni MySQL

-   [ ] `app/Models/PaymentDistribution.php`

    -   [ ] Convertire `GROUP_CONCAT` → `STRING_AGG`
    -   [ ] Verificare altri usi di funzioni MySQL

-   [ ] `app/Models/EgiAct.php`
    -   [ ] Implementare Full Text Search cross-database
    -   [ ] Creare migration per colonna `tsvector` e trigger

---

## 🛠️ Helper Consigliato

Creare file `app/Helpers/DatabaseHelper.php`:

```php
<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class DatabaseHelper
{
    /**
     * Convert DATE_FORMAT (MySQL) to TO_CHAR (PostgreSQL)
     */
    public static function dateFormat(string $column, string $format): string
    {
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            // Convert MySQL format to PostgreSQL format
            $pgFormat = str_replace(
                ['%Y', '%m', '%d', '%H', '%i', '%s', '%y', '%M', '%D'],
                ['YYYY', 'MM', 'DD', 'HH24', 'MI', 'SS', 'YY', 'Month', 'DDth'],
                $format
            );
            return "TO_CHAR({$column}, '{$pgFormat}')";
        }

        return "DATE_FORMAT({$column}, '{$format}')";
    }

    /**
     * Convert GROUP_CONCAT (MySQL) to STRING_AGG (PostgreSQL)
     */
    public static function groupConcat(
        string $column,
        string $separator = ',',
        bool $distinct = false
    ): string {
        $driver = DB::getDriverName();
        $distinctSql = $distinct ? 'DISTINCT ' : '';

        if ($driver === 'pgsql') {
            return "STRING_AGG({$distinctSql}{$column}::TEXT, '{$separator}')";
        }

        return "GROUP_CONCAT({$distinctSql}{$column} SEPARATOR '{$separator}')";
    }

    /**
     * Convert IFNULL (MySQL) to COALESCE (standard SQL)
     */
    public static function ifNull(string $column, string $default): string
    {
        // COALESCE works in both MySQL and PostgreSQL
        return "COALESCE({$column}, {$default})";
    }

    /**
     * Full Text Search cross-database
     */
    public static function fullTextSearch(
        string $column,
        string $searchTerm,
        string $language = 'italian'
    ): array {
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            // Assumes tsvector column exists as {column}_tsv
            $tsvColumn = "{$column}_tsv";
            return [
                'where' => "{$tsvColumn} @@ plainto_tsquery('{$language}', ?)",
                'bindings' => [$searchTerm]
            ];
        }

        // MySQL/MariaDB
        return [
            'where' => "MATCH({$column}) AGAINST(? IN BOOLEAN MODE)",
            'bindings' => [$searchTerm]
        ];
    }

    /**
     * Get current timestamp
     */
    public static function now(): string
    {
        // NOW() works in both
        return 'NOW()';
    }

    /**
     * Get current date
     */
    public static function currentDate(): string
    {
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            return 'CURRENT_DATE';
        }

        return 'CURDATE()';
    }

    /**
     * Unix timestamp from date
     */
    public static function unixTimestamp(string $column): string
    {
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            return "EXTRACT(EPOCH FROM {$column})::INTEGER";
        }

        return "UNIX_TIMESTAMP({$column})";
    }

    /**
     * From Unix timestamp to date
     */
    public static function fromUnixTimestamp(string $timestamp): string
    {
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            return "TO_TIMESTAMP({$timestamp})";
        }

        return "FROM_UNIXTIME({$timestamp})";
    }
}
```

---

## 📝 Note Aggiuntive

### Pattern da Cercare nel Codebase

Usare questi comandi per trovare altri usi potenzialmente problematici:

```bash
# Cercare tutte le query raw
grep -rn "whereRaw\|selectRaw\|havingRaw\|orderByRaw" app/

# Cercare DB::statement
grep -rn "DB::statement\|DB::unprepared" app/ database/

# Cercare funzioni MySQL specifiche
grep -rn "DATE_FORMAT\|GROUP_CONCAT\|MATCH.*AGAINST\|UNIX_TIMESTAMP\|IFNULL\|FIND_IN_SET" app/
```

### Test Consigliati

1. Creare un database PostgreSQL di test
2. Eseguire le migration
3. Eseguire la test suite completa
4. Verificare manualmente le funzionalità di:
    - Analytics (DATE_FORMAT)
    - Payment distributions (GROUP_CONCAT)
    - Ricerca atti PA (MATCH AGAINST)

---

## ⚠️ Indici FULLTEXT da Convertire

| Tabella             | Colonna                            | Migration |
| ------------------- | ---------------------------------- | --------- |
| `security_events`   | `description`                      | Da creare |
| `consent_histories` | `reason_for_action`, `admin_notes` | Da creare |
| `egi_acts`          | `oggetto`                          | Da creare |

Per ogni indice FULLTEXT, creare:

1. Colonna `tsvector`
2. Indice GIN
3. Trigger per aggiornamento automatico
4. Popolamento iniziale dati esistenti
