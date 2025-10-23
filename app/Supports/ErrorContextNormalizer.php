<?php

namespace App\Supports;

/**
 * Small utility to normalize context payloads passed to ErrorManager->handle()
 * Ensures there are no nested arrays that can break translation replacements.
 */
class ErrorContextNormalizer {
    /**
     * Normalize payload by converting nested arrays to JSON/string and trimming long values.
     * @param array $payload
     * @return array
     */
    public static function normalize(array $payload): array {
        $normalized = [];

        foreach ($payload as $key => $value) {
            if (is_array($value)) {
                // Flatten associative arrays into key-prefixed strings
                foreach ($value as $subKey => $subVal) {
                    $flatKey = $key . '_' . $subKey;
                    $normalized[$flatKey] = self::toString($subVal);
                }
            } else {
                $normalized[$key] = self::toString($value);
            }
        }

        return $normalized;
    }

    protected static function toString($v): string {
        if (is_string($v)) {
            return $v;
        }
        if (is_numeric($v) || is_bool($v)) {
            return (string) $v;
        }
        // For objects or arrays, JSON-encode with limit
        try {
            $s = is_object($v) ? json_encode($v) : json_encode($v);
        } catch (\Throwable $e) {
            $s = (string) $v;
        }

        // Limit length to avoid huge replacements
        if (is_string($s) && strlen($s) > 1000) {
            return substr($s, 0, 1000) . '...';
        }

        return (string) $s;
    }
}
