<?php

namespace App\Helpers;

/**
 * Early Environment Helper
 *
 * Gestisce il caricamento anticipato delle variabili di ambiente
 * necessarie durante il bootstrap dell'applicazione Laravel 11.
 *
 * Questa classe è necessaria per risolvere il problema di timing
 * dove alcuni middleware richiedono APP_KEY prima che il normale
 * caricamento del .env sia completato.
 */
class EarlyEnvironmentHelper {
    /**
     * Carica le variabili di ambiente critiche dal file .env
     *
     * @param string $basePath Il percorso base dell'applicazione
     * @param array $requiredKeys Le chiavi da caricare (default: ['APP_KEY'])
     * @return void
     */
    public static function loadCriticalEnvironmentVariables(string $basePath, array $requiredKeys = ['APP_KEY']): void {
        $envPath = $basePath . '/.env';

        // Verifica se il file .env esiste
        if (!file_exists($envPath)) {
            return;
        }

        // Controlla se le variabili sono già caricate
        $needsLoading = false;
        foreach ($requiredKeys as $key) {
            if (empty($_ENV[$key]) && empty(getenv($key))) {
                $needsLoading = true;
                break;
            }
        }

        if (!$needsLoading) {
            return;
        }

        // Carica solo le variabili richieste
        self::parseAndSetEnvironmentVariables($envPath, $requiredKeys);
    }

    /**
     * Parsifica il file .env e imposta le variabili specificate
     *
     * @param string $envPath Percorso del file .env
     * @param array $requiredKeys Chiavi da estrarre e impostare
     * @return void
     */
    private static function parseAndSetEnvironmentVariables(string $envPath, array $requiredKeys): void {
        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if ($lines === false) {
            return;
        }

        foreach ($lines as $line) {
            $line = trim($line);

            // Salta commenti e righe vuote
            if (empty($line) || strpos($line, '#') === 0) {
                continue;
            }

            // Parsifica la riga key=value
            if (strpos($line, '=') !== false) {
                [$key, $value] = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);

                // Imposta solo le chiavi richieste
                if (in_array($key, $requiredKeys)) {
                    $_ENV[$key] = $value;
                    putenv("$key=$value");
                }
            }
        }
    }
}
