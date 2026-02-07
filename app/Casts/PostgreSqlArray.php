<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * PostgreSQL Array Cast
 *
 * Properly handles PostgreSQL TEXT[] arrays in Laravel models.
 * Converts between PHP arrays and PostgreSQL array format.
 */
class PostgreSqlArray implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): ?array
    {
        if (is_null($value)) {
            return null;
        }

        // If already an array (from Eloquent), return it
        if (is_array($value)) {
            return $value;
        }

        // Parse PostgreSQL array format: {value1,value2,value3}
        if (is_string($value)) {
            $value = trim($value, '{}');

            if (empty($value)) {
                return [];
            }

            // Handle quoted values and commas
            return array_map(function ($item) {
                return trim($item, '"');
            }, str_getcsv($value));
        }

        return [];
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): string
    {
        if (is_null($value)) {
            return '{}';
        }

        if (!is_array($value)) {
            $value = [$value];
        }

        // Convert PHP array to PostgreSQL array format
        // Escape quotes and wrap in quotes
        $escaped = array_map(function ($item) {
            $item = str_replace('"', '""', $item); // Escape quotes
            return '"' . $item . '"';
        }, $value);

        return '{' . implode(',', $escaped) . '}';
    }
}
