<?php

// Test file per Rule Engine - contiene violazioni intenzionali

namespace App\Services\Test;

use Ultra\UltraLogManager\UltraLogManager;

class TestViolationsService {
    protected UltraLogManager $logger;

    public function __construct(UltraLogManager $logger) {
        $this->logger = $logger;
    }

    /**
     * VIOLAZIONE REGOLA_ZERO: hasConsentFor() è un metodo inventato
     */
    public function testRegolaZero() {
        $user = auth()->user();

        // ❌ VIOLAZIONE: hasConsentFor() non esiste (metodo inventato)
        if ($this->consentService->hasConsentFor('data-processing')) {
            return true;
        }

        return false;
    }

    /**
     * VIOLAZIONE UEM_FIRST: logger->error() senza errorManager->handle()
     */
    public function testUemFirst() {
        try {
            // Some risky operation
            $result = $this->riskyOperation();
        } catch (\Exception $e) {
            // ❌ VIOLAZIONE: solo logger->error() senza errorManager->handle()
            $this->logger->error('Operation failed', ['error' => $e->getMessage()]);
            return false;
        }

        return true;
    }
}
