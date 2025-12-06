<?php

declare(strict_types=1);

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

/**
 * Helper class for cross-database compatibility between MySQL/MariaDB and PostgreSQL.
 *
 * This class provides static methods to generate SQL fragments that work correctly
 * on both MySQL/MariaDB and PostgreSQL databases.
 *
 * @package App\Helpers
 */
class DatabaseHelper {
    /**
     * Get the current database driver name.
     */
    public static function getDriver(): string {
        return DB::getDriverName();
    }

    /**
     * Check if current connection is PostgreSQL.
     */
    public static function isPostgres(): bool {
        return self::getDriver() === 'pgsql';
    }

    /**
     * Check if current connection is MySQL or MariaDB.
     */
    public static function isMysql(): bool {
        return in_array(self::getDriver(), ['mysql', 'mariadb']);
    }

    /**
     * Get the SQL representation of a boolean value for raw queries.
     * 
     * MySQL/MariaDB uses 1/0 for boolean values.
     * PostgreSQL uses TRUE/FALSE literals.
     *
     * @param bool $value The boolean value
     * @return string SQL fragment ('TRUE'/'FALSE' for PostgreSQL, '1'/'0' for MySQL)
     */
    public static function booleanValue(bool $value): string {
        if (self::isPostgres()) {
            return $value ? 'TRUE' : 'FALSE';
        }

        return $value ? '1' : '0';
    }

    /**
     * Convert DATE_FORMAT (MySQL) to TO_CHAR (PostgreSQL).
     *
     * @param string $column The column name
     * @param string $format The MySQL format string (e.g., '%Y-%m-%d')
     * @return string SQL fragment
     *
     * Format mapping:
     * - %Y → YYYY (4-digit year)
     * - %y → YY (2-digit year)
     * - %m → MM (month 01-12)
     * - %d → DD (day 01-31)
     * - %H → HH24 (hour 00-23)
     * - %i → MI (minutes 00-59)
     * - %s → SS (seconds 00-59)
     * - %M → Month (full month name)
     * - %W → Day (full weekday name)
     * - %u → IW (ISO week number)
     */
    public static function dateFormat(string $column, string $format): string {
        if (self::isPostgres()) {
            // Convert MySQL format to PostgreSQL format
            $pgFormat = str_replace(
                ['%Y', '%y', '%m', '%d', '%H', '%i', '%s', '%M', '%W', '%a', '%b', '%u'],
                ['YYYY', 'YY', 'MM', 'DD', 'HH24', 'MI', 'SS', 'Month', 'Day', 'Dy', 'Mon', 'IW'],
                $format
            );
            return "TO_CHAR({$column}, '{$pgFormat}')";
        }

        return "DATE_FORMAT({$column}, '{$format}')";
    }

    /**
     * Convert GROUP_CONCAT (MySQL) to STRING_AGG (PostgreSQL).
     *
     * @param string $column The column to aggregate
     * @param string $separator The separator between values (default: ',')
     * @param bool $distinct Whether to use DISTINCT
     * @param string|null $orderBy Optional ORDER BY clause for the aggregation
     * @return string SQL fragment
     */
    public static function groupConcat(
        string $column,
        string $separator = ',',
        bool $distinct = false,
        ?string $orderBy = null
    ): string {
        $distinctSql = $distinct ? 'DISTINCT ' : '';

        if (self::isPostgres()) {
            $orderBySql = $orderBy ? " ORDER BY {$orderBy}" : '';
            return "STRING_AGG({$distinctSql}{$column}::TEXT, '{$separator}'{$orderBySql})";
        }

        // MySQL
        $orderBySql = $orderBy ? " ORDER BY {$orderBy}" : '';
        return "GROUP_CONCAT({$distinctSql}{$column}{$orderBySql} SEPARATOR '{$separator}')";
    }

    /**
     * IFNULL (MySQL) / COALESCE (standard SQL).
     * Uses COALESCE which works in both databases.
     *
     * @param string $column The column to check
     * @param string $default The default value if null
     * @return string SQL fragment
     */
    public static function ifNull(string $column, string $default): string {
        // COALESCE works in both MySQL and PostgreSQL
        return "COALESCE({$column}, {$default})";
    }

    /**
     * Get current timestamp function.
     * NOW() works in both databases.
     *
     * @return string SQL fragment
     */
    public static function now(): string {
        return 'NOW()';
    }

    /**
     * Get current date function.
     *
     * @return string SQL fragment
     */
    public static function currentDate(): string {
        if (self::isPostgres()) {
            return 'CURRENT_DATE';
        }

        return 'CURDATE()';
    }

    /**
     * Convert a date/timestamp to Unix timestamp.
     *
     * @param string $column The column or expression
     * @return string SQL fragment
     */
    public static function unixTimestamp(string $column): string {
        if (self::isPostgres()) {
            return "EXTRACT(EPOCH FROM {$column})::INTEGER";
        }

        return "UNIX_TIMESTAMP({$column})";
    }

    /**
     * Convert Unix timestamp to date/timestamp.
     *
     * @param string $timestamp The unix timestamp column or value
     * @return string SQL fragment
     */
    public static function fromUnixTimestamp(string $timestamp): string {
        if (self::isPostgres()) {
            return "TO_TIMESTAMP({$timestamp})";
        }

        return "FROM_UNIXTIME({$timestamp})";
    }

    /**
     * Full Text Search - generates WHERE clause for full text search.
     *
     * For PostgreSQL, this requires a tsvector column to exist.
     * The convention is: {column}_tsv for the tsvector column.
     *
     * @param string $column The column to search (MySQL) or base name for tsvector (PostgreSQL)
     * @param string $language PostgreSQL text search language (default: 'italian')
     * @return array{where: string, bindings: array} The WHERE clause and bindings
     */
    public static function fullTextSearch(
        string $column,
        string $language = 'italian'
    ): array {
        if (self::isPostgres()) {
            // Assumes tsvector column exists as {column}_tsv
            $tsvColumn = "{$column}_tsv";
            return [
                'where' => "{$tsvColumn} @@ plainto_tsquery('{$language}', ?)",
                'bindings' => [], // The binding will be added by the caller
            ];
        }

        // MySQL/MariaDB
        return [
            'where' => "MATCH({$column}) AGAINST(? IN BOOLEAN MODE)",
            'bindings' => [],
        ];
    }

    /**
     * Full Text Search with direct value (when you need to search without bindings).
     *
     * @param string $column The column to search
     * @param string $searchTerm The search term (will be quoted)
     * @param string $language PostgreSQL text search language
     * @return string SQL fragment
     */
    public static function fullTextSearchRaw(
        string $column,
        string $searchTerm,
        string $language = 'italian'
    ): string {
        $escapedTerm = addslashes($searchTerm);

        if (self::isPostgres()) {
            $tsvColumn = "{$column}_tsv";
            return "{$tsvColumn} @@ plainto_tsquery('{$language}', '{$escapedTerm}')";
        }

        return "MATCH({$column}) AGAINST('{$escapedTerm}' IN BOOLEAN MODE)";
    }

    /**
     * FIND_IN_SET (MySQL) equivalent for PostgreSQL.
     *
     * @param string $value The value to find
     * @param string $column The column containing comma-separated values
     * @return string SQL fragment
     */
    public static function findInSet(string $value, string $column): string {
        if (self::isPostgres()) {
            return "{$value} = ANY(STRING_TO_ARRAY({$column}, ','))";
        }

        return "FIND_IN_SET({$value}, {$column})";
    }

    /**
     * IF function (MySQL) / CASE WHEN (PostgreSQL).
     *
     * @param string $condition The condition
     * @param string $trueValue Value if condition is true
     * @param string $falseValue Value if condition is false
     * @return string SQL fragment
     */
    public static function ifCondition(string $condition, string $trueValue, string $falseValue): string {
        // CASE WHEN works in both databases, so we use that
        return "CASE WHEN {$condition} THEN {$trueValue} ELSE {$falseValue} END";
    }

    /**
     * Generate a random value.
     *
     * @return string SQL fragment
     */
    public static function random(): string {
        if (self::isPostgres()) {
            return 'RANDOM()';
        }

        return 'RAND()';
    }

    /**
     * String concatenation.
     *
     * @param array $parts Parts to concatenate
     * @return string SQL fragment
     */
    public static function concat(array $parts): string {
        // CONCAT works in both MySQL and PostgreSQL
        return 'CONCAT(' . implode(', ', $parts) . ')';
    }

    /**
     * String concatenation with separator.
     *
     * @param string $separator The separator
     * @param array $parts Parts to concatenate
     * @return string SQL fragment
     */
    public static function concatWs(string $separator, array $parts): string {
        // CONCAT_WS works in both MySQL and PostgreSQL
        return "CONCAT_WS('{$separator}', " . implode(', ', $parts) . ')';
    }

    /**
     * Cast a column to a specific type.
     *
     * @param string $column The column
     * @param string $type Target type (integer, text, date, etc.)
     * @return string SQL fragment
     */
    public static function cast(string $column, string $type): string {
        $typeMap = [
            'integer' => self::isPostgres() ? 'INTEGER' : 'SIGNED',
            'unsigned' => self::isPostgres() ? 'INTEGER' : 'UNSIGNED',
            'text' => self::isPostgres() ? 'TEXT' : 'CHAR',
            'date' => 'DATE',
            'datetime' => self::isPostgres() ? 'TIMESTAMP' : 'DATETIME',
            'decimal' => 'DECIMAL',
            'float' => self::isPostgres() ? 'DOUBLE PRECISION' : 'FLOAT',
        ];

        $sqlType = $typeMap[strtolower($type)] ?? strtoupper($type);

        if (self::isPostgres()) {
            return "{$column}::{$sqlType}";
        }

        return "CAST({$column} AS {$sqlType})";
    }

    /**
     * Get year from a date column.
     *
     * @param string $column The date column
     * @return string SQL fragment
     */
    public static function year(string $column): string {
        if (self::isPostgres()) {
            return "EXTRACT(YEAR FROM {$column})::INTEGER";
        }

        return "YEAR({$column})";
    }

    /**
     * Get month from a date column.
     *
     * @param string $column The date column
     * @return string SQL fragment
     */
    public static function month(string $column): string {
        if (self::isPostgres()) {
            return "EXTRACT(MONTH FROM {$column})::INTEGER";
        }

        return "MONTH({$column})";
    }

    /**
     * Get day from a date column.
     *
     * @param string $column The date column
     * @return string SQL fragment
     */
    public static function day(string $column): string {
        if (self::isPostgres()) {
            return "EXTRACT(DAY FROM {$column})::INTEGER";
        }

        return "DAY({$column})";
    }

    /**
     * Get date part only from a timestamp column (removes time).
     *
     * @param string $column The timestamp/datetime column
     * @return string SQL fragment
     */
    public static function dateOnly(string $column): string {
        if (self::isPostgres()) {
            return "{$column}::DATE";
        }

        return "DATE({$column})";
    }

    /**
     * JSON extract value (for JSON columns).
     *
     * @param string $column The JSON column
     * @param string $path The JSON path (e.g., '$.name' for MySQL, 'name' for PostgreSQL)
     * @return string SQL fragment
     */
    public static function jsonExtract(string $column, string $path): string {
        if (self::isPostgres()) {
            // PostgreSQL uses -> for JSON object access, ->> for text
            // Convert MySQL path $.key.subkey to PostgreSQL format
            $pgPath = preg_replace('/^\$\./', '', $path);
            $parts = explode('.', $pgPath);

            if (count($parts) === 1) {
                return "{$column}->>" . "'" . $parts[0] . "'";
            }

            $accessChain = '';
            foreach ($parts as $i => $part) {
                $operator = ($i === count($parts) - 1) ? '->>' : '->';
                $accessChain .= "{$operator}'{$part}'";
            }
            return "{$column}{$accessChain}";
        }

        // MySQL
        return "JSON_EXTRACT({$column}, '{$path}')";
    }

    /**
     * Boolean value for SQL.
     *
     * @param bool $value The boolean value
     * @return string SQL fragment
     */
    public static function boolean(bool $value): string {
        if (self::isPostgres()) {
            return $value ? 'TRUE' : 'FALSE';
        }

        return $value ? '1' : '0';
    }

    /**
     * LIMIT and OFFSET clause.
     * Both databases support the same syntax, but this is here for clarity.
     *
     * @param int $limit The limit
     * @param int $offset The offset (default: 0)
     * @return string SQL fragment
     */
    public static function limitOffset(int $limit, int $offset = 0): string {
        // Both MySQL and PostgreSQL support this syntax
        return "LIMIT {$limit} OFFSET {$offset}";
    }

    /**
     * Check if a value is in a list (for subqueries or value lists).
     *
     * @param string $column The column
     * @param array $values The values
     * @return string SQL fragment with placeholders
     */
    public static function inList(string $column, array $values): string {
        $placeholders = implode(', ', array_fill(0, count($values), '?'));
        return "{$column} IN ({$placeholders})";
    }

    /**
     * REGEXP / ~ operator for regex matching.
     *
     * @param string $column The column
     * @param string $pattern The regex pattern
     * @return string SQL fragment
     */
    public static function regexp(string $column, string $pattern): string {
        if (self::isPostgres()) {
            return "{$column} ~ '{$pattern}'";
        }

        return "{$column} REGEXP '{$pattern}'";
    }

    /**
     * Case-insensitive LIKE.
     *
     * @param string $column The column
     * @return string SQL fragment (use with bindings)
     */
    public static function ilike(string $column): string {
        if (self::isPostgres()) {
            return "{$column} ILIKE ?";
        }

        // MySQL LIKE is case-insensitive by default with most collations
        return "{$column} LIKE ?";
    }
}
