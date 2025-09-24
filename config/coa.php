<?php

return [
    // Abilita generazione automatica PDF dopo emissione CoA (fallback se la richiesta non specifica diversamente)
    'auto_generate_pdf' => env('COA_AUTO_GENERATE_PDF', true),

    // Firma digitale (QES/PAdES) e TSA — abilitati di default in mock (feature flags)
    'signature' => [
        'enabled' => env('COA_SIGNATURE_ENABLED', true),
        // Provider selezionato: 'namirial' | 'infocert' | 'mock'
        'provider' => env('COA_SIGNATURE_PROVIDER', 'mock'),
        // Ambiente di esecuzione firma: 'sandbox' | 'production'
        'environment' => env('COA_SIGNATURE_ENV', 'sandbox'),
        // Algoritmo hash per digest del documento
        'hash_algo' => env('COA_SIGNATURE_HASH', 'sha256'),

        // Abilita co‑firma ispettore (perito) — opzionale
        'inspector' => [
            'enabled' => env('COA_SIGNATURE_INSPECTOR_ENABLED', true),
        ],

        // Marca temporale RFC3161 (TSA)
        'tsa' => [
            'enabled'   => env('COA_TSA_ENABLED', true),
            'policy_oid' => env('COA_TSA_POLICY_OID', null),
            // Config per TSA Namirial (placeholder env)
            'namirial' => [
                'sandbox' => [
                    'url'      => env('NAMIRIAL_TSA_SANDBOX_URL'),
                    'username' => env('NAMIRIAL_TSA_SANDBOX_USERNAME'),
                    'password' => env('NAMIRIAL_TSA_SANDBOX_PASSWORD'),
                ],
                'production' => [
                    'url'      => env('NAMIRIAL_TSA_URL'),
                    'username' => env('NAMIRIAL_TSA_USERNAME'),
                    'password' => env('NAMIRIAL_TSA_PASSWORD'),
                ],
            ],
            // Config per TSA InfoCert (placeholder env)
            'infocert' => [
                'sandbox' => [
                    'url'      => env('INFOCERT_TSA_SANDBOX_URL'),
                    'username' => env('INFOCERT_TSA_SANDBOX_USERNAME'),
                    'password' => env('INFOCERT_TSA_SANDBOX_PASSWORD'),
                ],
                'production' => [
                    'url'      => env('INFOCERT_TSA_URL'),
                    'username' => env('INFOCERT_TSA_USERNAME'),
                    'password' => env('INFOCERT_TSA_PASSWORD'),
                ],
            ],
        ],

        // Config provider firma remota (placeholder env, non operativi senza credenziali)
        'providers' => [
            'namirial' => [
                'sandbox' => [
                    'base_url' => env('NAMIRIAL_SANDBOX_BASE_URL'),
                    'api_key'  => env('NAMIRIAL_SANDBOX_API_KEY'),
                ],
                'production' => [
                    'base_url' => env('NAMIRIAL_BASE_URL'),
                    'api_key'  => env('NAMIRIAL_API_KEY'),
                ],
            ],
            'infocert' => [
                'sandbox' => [
                    'base_url' => env('INFOCERT_SANDBOX_BASE_URL'),
                    'api_key'  => env('INFOCERT_SANDBOX_API_KEY'),
                ],
                'production' => [
                    'base_url' => env('INFOCERT_BASE_URL'),
                    'api_key'  => env('INFOCERT_API_KEY'),
                ],
            ],
        ],
    ],

    // Impostazioni integrità/verifica
    'integrity' => [
        // Elenco di trait critici per coerenza DB↔snapshot (CSV); opzionale
        'critical_traits' => env('COA_INTEGRITY_CRITICAL_TRAITS', ''),
    ],

    // Politiche di validità
    'validity' => [
        // Nessuna scadenza per default, conforme alla policy "no expiry"
        'no_expiry' => env('COA_VALIDITY_NO_EXPIRY', true),
    ],
];
