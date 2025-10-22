<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * @Oracode ValidIban: IBAN validation rule
 * 🎯 Purpose: Validate IBAN format and checksum (MOD-97)
 * 🛡️ Security: Prevents invalid IBAN storage
 *
 * @package App\Rules
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Wallet Security Module)
 * @date 2025-10-22
 * @purpose IBAN validation with MOD-97 checksum
 */
class ValidIban implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value)) {
            $fail('The :attribute must be a string.');
            return;
        }

        // Normalize IBAN (remove spaces, uppercase)
        $iban = strtoupper(preg_replace('/\s+/', '', $value));

        // 1. Length validation (15-34 characters)
        if (strlen($iban) < 15 || strlen($iban) > 34) {
            $fail('The :attribute must be between 15 and 34 characters.');
            return;
        }

        // 2. Format validation: 2 letters (country) + 2 digits (check) + alphanumeric
        if (!preg_match('/^[A-Z]{2}[0-9]{2}[A-Z0-9]+$/', $iban)) {
            $fail('The :attribute has an invalid format. Must start with 2 letters and 2 digits.');
            return;
        }

        // 3. MOD-97 checksum validation
        if (!$this->validateChecksum($iban)) {
            $fail('The :attribute has an invalid checksum.');
            return;
        }

        // 4. Country-specific length validation (optional but recommended)
        if (!$this->validateCountryLength($iban)) {
            $fail('The :attribute has an invalid length for country ' . substr($iban, 0, 2) . '.');
            return;
        }
    }

    /**
     * Validate IBAN checksum using MOD-97 algorithm
     *
     * @param string $iban Normalized IBAN
     * @return bool
     */
    protected function validateChecksum(string $iban): bool
    {
        // Rearrange: move first 4 chars to end
        $rearranged = substr($iban, 4) . substr($iban, 0, 4);

        // Convert to numeric string (A=10, B=11, ..., Z=35)
        $numeric = '';
        for ($i = 0; $i < strlen($rearranged); $i++) {
            $char = $rearranged[$i];
            if (ctype_alpha($char)) {
                $numeric .= (ord($char) - ord('A') + 10);
            } else {
                $numeric .= $char;
            }
        }

        // Check if MOD-97 = 1
        return bcmod($numeric, '97') === '1';
    }

    /**
     * Validate IBAN length for specific country codes
     *
     * @param string $iban Normalized IBAN
     * @return bool
     */
    protected function validateCountryLength(string $iban): bool
    {
        // Country-specific IBAN lengths
        $lengths = [
            'AD' => 24, // Andorra
            'AE' => 23, // United Arab Emirates
            'AL' => 28, // Albania
            'AT' => 20, // Austria
            'AZ' => 28, // Azerbaijan
            'BA' => 20, // Bosnia
            'BE' => 16, // Belgium
            'BG' => 22, // Bulgaria
            'BH' => 22, // Bahrain
            'BR' => 29, // Brazil
            'BY' => 28, // Belarus
            'CH' => 21, // Switzerland
            'CR' => 22, // Costa Rica
            'CY' => 28, // Cyprus
            'CZ' => 24, // Czech Republic
            'DE' => 22, // Germany
            'DK' => 18, // Denmark
            'DO' => 28, // Dominican Republic
            'EE' => 20, // Estonia
            'EG' => 29, // Egypt
            'ES' => 24, // Spain
            'FI' => 18, // Finland
            'FO' => 18, // Faroe Islands
            'FR' => 27, // France
            'GB' => 22, // United Kingdom
            'GE' => 22, // Georgia
            'GI' => 23, // Gibraltar
            'GL' => 18, // Greenland
            'GR' => 27, // Greece
            'GT' => 28, // Guatemala
            'HR' => 21, // Croatia
            'HU' => 28, // Hungary
            'IE' => 22, // Ireland
            'IL' => 23, // Israel
            'IQ' => 23, // Iraq
            'IS' => 26, // Iceland
            'IT' => 27, // Italy
            'JO' => 30, // Jordan
            'KW' => 30, // Kuwait
            'KZ' => 20, // Kazakhstan
            'LB' => 28, // Lebanon
            'LC' => 32, // Saint Lucia
            'LI' => 21, // Liechtenstein
            'LT' => 20, // Lithuania
            'LU' => 20, // Luxembourg
            'LV' => 21, // Latvia
            'MC' => 27, // Monaco
            'MD' => 24, // Moldova
            'ME' => 22, // Montenegro
            'MK' => 19, // North Macedonia
            'MR' => 27, // Mauritania
            'MT' => 31, // Malta
            'MU' => 30, // Mauritius
            'NL' => 18, // Netherlands
            'NO' => 15, // Norway
            'PK' => 24, // Pakistan
            'PL' => 28, // Poland
            'PS' => 29, // Palestine
            'PT' => 25, // Portugal
            'QA' => 29, // Qatar
            'RO' => 24, // Romania
            'RS' => 22, // Serbia
            'SA' => 24, // Saudi Arabia
            'SE' => 24, // Sweden
            'SI' => 19, // Slovenia
            'SK' => 24, // Slovakia
            'SM' => 27, // San Marino
            'TN' => 24, // Tunisia
            'TR' => 26, // Turkey
            'UA' => 29, // Ukraine
            'VA' => 22, // Vatican
            'VG' => 24, // British Virgin Islands
            'XK' => 20, // Kosovo
        ];

        $countryCode = substr($iban, 0, 2);

        // If country not in list, skip validation (allow it)
        if (!isset($lengths[$countryCode])) {
            return true;
        }

        return strlen($iban) === $lengths[$countryCode];
    }
}
