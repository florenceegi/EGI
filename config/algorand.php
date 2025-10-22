<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Algorand Microservice Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the Node.js Algorand microservice that handles
    | account creation and blockchain interactions.
    |
    */

    /**
     * Microservice URL
     */
    'microservice_url' => env('ALGORAND_MICROSERVICE_URL', 'http://localhost:3000'),

    /**
     * Request timeout in seconds
     */
    'timeout' => env('ALGORAND_TIMEOUT', 10),

    /**
     * Network mode (sandbox, testnet, mainnet)
     */
    'network' => env('ALGORAND_NETWORK', 'sandbox'),
];
