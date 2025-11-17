<?php

namespace App\Exceptions\Wallet;

use Exception;

/**
 * @package App\Exceptions\Wallet
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Wallet Module)
 * @date 2025-11-13
 * @purpose Segnala che un IBAN è già associato a un wallet differente
 */
class DuplicateIbanException extends Exception
{
    public function __construct(
        public readonly int $walletId,
        public readonly string $ibanLast4
    ) {
        parent::__construct('IBAN already registered to another wallet.');
    }
}

