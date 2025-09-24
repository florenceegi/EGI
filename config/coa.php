<?php

return [
    // Abilita generazione automatica PDF dopo emissione CoA (fallback se la richiesta non specifica diversamente)
    'auto_generate_pdf' => env('COA_AUTO_GENERATE_PDF', true),
];
