<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Validation rule per codici IBAN (International Bank Account Number)
 *
 * Implementa l'algoritmo MOD-97 secondo lo standard ISO 7064
 * con validazione lunghezza per paese e gestione edge cases.
 *
 * @package App\Rules
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Wallet Security)
 * @date 2025-10-20
 * @purpose IBAN validation con compliance europea completa
 */
class ValidIban implements ValidationRule {
    /**
     * Lunghezze IBAN per paese secondo SWIFT registry ISO 13616
     * Updated: December 2024
     */
    private const COUNTRY_LENGTHS = [
        'AD' => 24,
        'AE' => 23,
        'AL' => 28,
        'AT' => 20,
        'AZ' => 28,
        'BA' => 20,
        'BE' => 16,
        'BG' => 22,
        'BH' => 22,
        'BR' => 29,
        'BY' => 28,
        'CH' => 21,
        'CR' => 22,
        'CY' => 28,
        'CZ' => 24,
        'DE' => 22,
        'DK' => 18,
        'DO' => 28,
        'EE' => 20,
        'EG' => 29,
        'ES' => 24,
        'FI' => 18,
        'FO' => 18,
        'FR' => 27,
        'GB' => 22,
        'GE' => 22,
        'GI' => 23,
        'GL' => 18,
        'GR' => 27,
        'GT' => 28,
        'HR' => 21,
        'HU' => 28,
        'IE' => 22,
        'IL' => 23,
        'IS' => 26,
        'IT' => 27,
        'JO' => 30,
        'KW' => 30,
        'KZ' => 20,
        'LB' => 28,
        'LC' => 32,
        'LI' => 21,
        'LT' => 20,
        'LU' => 20,
        'LV' => 21,
        'MC' => 27,
        'MD' => 24,
        'ME' => 22,
        'MK' => 19,
        'MR' => 27,
        'MT' => 31,
        'MU' => 30,
        'NL' => 18,
        'NO' => 15,
        'PK' => 24,
        'PL' => 28,
        'PS' => 29,
        'PT' => 25,
        'QA' => 29,
        'RO' => 24,
        'RS' => 22,
        'RU' => 33,
        'SA' => 24,
        'SE' => 24,
        'SI' => 19,
        'SK' => 24,
        'SM' => 27,
        'TN' => 24,
        'TR' => 26,
        'UA' => 29,
        'VG' => 24,
        'XK' => 20
    ];

    /**
     * Mapping caratteri A-Z → valori numerici (A=10, B=11, ..., Z=35)
     */
    private const CHAR_MAP = [
        'A' => '10',
        'B' => '11',
        'C' => '12',
        'D' => '13',
        'E' => '14',
        'F' => '15',
        'G' => '16',
        'H' => '17',
        'I' => '18',
        'J' => '19',
        'K' => '20',
        'L' => '21',
        'M' => '22',
        'N' => '23',
        'O' => '24',
        'P' => '25',
        'Q' => '26',
        'R' => '27',
        'S' => '28',
        'T' => '29',
        'U' => '30',
        'V' => '31',
        'W' => '32',
        'X' => '33',
        'Y' => '34',
        'Z' => '35'
    ];

    /**
     * Valida il codice IBAN secondo algoritmo MOD-97 ISO 7064
     *
     * @param string $attribute Nome del campo
     * @param mixed $value Valore IBAN da validare
     * @param Closure $fail Callback per failure
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        // Sanitize input
        $iban = $this->sanitizeIban($value);

        // Basic format validation
        if (!$this->isValidFormat($iban)) {
            $fail('Il codice IBAN deve contenere solo lettere maiuscole e numeri.');
            return;
        }

        // Country validation
        $countryCode = substr($iban, 0, 2);
        if (!$this->isValidCountry($countryCode)) {
            $fail("Paese '$countryCode' non supportato per codici IBAN.");
            return;
        }

        // Length validation
        if (!$this->isValidLength($iban, $countryCode)) {
            $expectedLength = self::COUNTRY_LENGTHS[$countryCode];
            $actualLength = strlen($iban);
            $fail("Lunghezza IBAN non valida per $countryCode. Attesa: $expectedLength, ricevuta: $actualLength.");
            return;
        }

        // MOD-97 checksum validation
        if (!$this->isValidChecksum($iban)) {
            $fail('Il codice IBAN non supera la validazione del checksum.');
            return;
        }
    }

    /**
     * Sanitizza input IBAN rimuovendo spazi e convertendo a maiuscolo
     */
    private function sanitizeIban(mixed $value): string {
        if (!is_string($value)) {
            return '';
        }

        return strtoupper(str_replace(' ', '', trim($value)));
    }

    /**
     * Verifica formato base IBAN (solo caratteri alfanumerici)
     */
    private function isValidFormat(string $iban): bool {
        return preg_match('/^[A-Z]{2}[0-9]{2}[A-Z0-9]+$/', $iban) === 1;
    }

    /**
     * Verifica se il paese è supportato
     */
    private function isValidCountry(string $countryCode): bool {
        return isset(self::COUNTRY_LENGTHS[$countryCode]);
    }

    /**
     * Verifica lunghezza IBAN per paese specifico
     */
    private function isValidLength(string $iban, string $countryCode): bool {
        $expectedLength = self::COUNTRY_LENGTHS[$countryCode];
        return strlen($iban) === $expectedLength;
    }

    /**
     * Verifica checksum MOD-97 secondo ISO 7064
     *
     * Algoritmo:
     * 1. Sposta primi 4 caratteri alla fine
     * 2. Converte lettere in numeri (A=10, B=11, ..., Z=35)
     * 3. Calcola resto divisione per 97
     * 4. IBAN valido se resto = 1
     */
    private function isValidChecksum(string $iban): bool {
        // Step 1: Riorganizza IBAN (sposta primi 4 caratteri alla fine)
        $rearranged = substr($iban, 4) . substr($iban, 0, 4);

        // Step 2: Converte caratteri in numeri
        $numericString = '';
        for ($i = 0; $i < strlen($rearranged); $i++) {
            $char = $rearranged[$i];
            if (is_numeric($char)) {
                $numericString .= $char;
            } else {
                $numericString .= self::CHAR_MAP[$char] ?? '';
            }
        }

        // Handle edge case: string vuota dopo conversione
        if (empty($numericString)) {
            return false;
        }

        // Step 3: Calcola MOD-97 con gestione large integers
        return $this->calculateMod97($numericString) === 1;
    }

    /**
     * Calcola MOD-97 con gestione large integers
     *
     * Usa bcmod() se disponibile, altrimenti piece-wise calculation
     * per evitare problemi con integer overflow su stringhe lunghe
     */
    private function calculateMod97(string $numericString): int {
        // Remove leading zeros per evitare errori bcmod
        $numericString = ltrim($numericString, '0');

        if (empty($numericString)) {
            return 0;
        }

        // Usa bcmod se disponibile (raccomandato)
        if (function_exists('bcmod')) {
            return (int) bcmod($numericString, '97');
        }

        // Fallback: piece-wise calculation per compatibility
        return $this->pieceWiseMod97($numericString);
    }

    /**
     * Calcolo MOD-97 piece-wise per sistemi senza bcmath
     *
     * Processa la stringa in chunks per evitare integer overflow
     */
    private function pieceWiseMod97(string $numericString): int {
        $remainder = 0;
        $chunkSize = 9; // Safe per 32-bit integers

        for ($i = 0; $i < strlen($numericString); $i += $chunkSize) {
            $chunk = substr($numericString, $i, $chunkSize);
            $number = $remainder . $chunk;
            $remainder = (int) $number % 97;
        }

        return $remainder;
    }
}
