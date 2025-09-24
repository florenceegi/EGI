<?php

namespace App\Services\Coa\Signature;

/**
 * @package App\Services\Coa\Signature
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - QES Sandbox)
 * @date 2025-09-24
 * @purpose Contratto per provider di firma digitale (QES/PAdES) e marca temporale RFC3161
 */
interface SignatureProviderInterface {
    /**
     * Firma un PDF (prima firma) e restituisce il percorso del PDF firmato.
     * Implementazioni reali devono applicare PAdES; i mock possono duplicare il file.
     *
     * @param string $pdfPath Percorso assoluto al PDF da firmare (readable)
     * @param array{role?:string,reason?:string,location?:string,contact?:string,hash_algo?:string,metadata?:array} $options Opzioni firma
     * @return array{success:bool,signed_pdf_path?:string,signature_info?:array,error?:string}
     */
    public function signPdf(string $pdfPath, array $options = []): array;

    /**
     * Aggiunge una co‑firma (countersign) al PDF già firmato, generando una nuova versione.
     *
     * @param string $signedPdfPath Percorso assoluto al PDF già firmato
     * @param array{role?:string,reason?:string,location?:string,contact?:string,hash_algo?:string,metadata?:array} $options Opzioni firma
     * @return array{success:bool,signed_pdf_path?:string,signature_info?:array,error?:string}
     */
    public function addCountersignature(string $signedPdfPath, array $options = []): array;

    /**
     * Applica marca temporale RFC3161 e restituisce nuova versione del PDF.
     *
     * @param string $signedPdfPath Percorso PDF (preferibilmente già firmato)
     * @param array{policy_oid?:string,tsa?:string} $options Opzioni TSA
     * @return array{success:bool,signed_pdf_path?:string,timestamp_info?:array,error?:string}
     */
    public function addTimestamp(string $signedPdfPath, array $options = []): array;

    /**
     * Verifica firme e (se presente) marca temporale del PDF fornito.
     *
     * @param string $pdfPath Percorso PDF da verificare
     * @return array{success:bool,signatures?:array,timestamp?:array,errors?:array}
     */
    public function verifySignatures(string $pdfPath): array;
}
