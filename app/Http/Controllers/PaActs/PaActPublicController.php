<?php

namespace App\Http\Controllers\PaActs;

use App\Http\Controllers\Controller;
use App\Services\PaActs\PaActService;
use App\Services\PaActs\MerkleTreeService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * PA Act Public Verification Controller
 * 
 * ============================================================================
 * CONTESTO - VERIFICA PUBBLICA ATTI PA
 * ============================================================================
 * 
 * Controller per la verifica pubblica degli atti PA tokenizzati su blockchain.
 * 
 * TARGET USER: Pubblico (cittadini, aziende, altre PA)
 * ACCESS: Pubblico, no autenticazione richiesta
 * 
 * PURPOSE:
 * - Trust-minimized verification: Chiunque può verificare autenticità atto
 * - Transparency: Dati pubblici visibili (protocol, signer, hash, blockchain)
 * - No download PDF: Privacy PA (documento consultabile solo internamente)
 * 
 * ============================================================================
 * ROUTE
 * ============================================================================
 * 
 * GET /verify/{public_code}
 * - Name: verify.act
 * - Middleware: web (no auth)
 * - View: pa/acts/verify.blade.php
 * - Purpose: Pagina verifica pubblica atto PA
 * 
 * EXAMPLE URL:
 * https://florenceegi.it/verify/VER-ABC123XYZ
 * 
 * ============================================================================
 * WORKFLOW VERIFICA
 * ============================================================================
 * 
 * INPUT:
 * - public_code: Codice verifica univoco (es. VER-ABC123XYZ)
 * 
 * STEP 1: RECUPERA ATTO DA DB
 * - Query: Egi::where('metadata->public_code', $publicCode)->first()
 * - 404 se non trovato
 * 
 * STEP 2: ESTRAI DATI PUBBLICI
 * - Protocol number + date
 * - Doc type (delibera, determina, etc.)
 * - Title (no description - privacy)
 * - Signer info (nome, org, ruolo da certificato)
 * - Document hash SHA-256
 * 
 * STEP 3: VERIFICA BLOCKCHAIN
 * - Merkle proof presente?
 * - Verifica Merkle proof con MerkleTreeService
 * - Blockchain TXID + link Algorand Explorer
 * - Anchor timestamp
 * 
 * STEP 4: DISPLAY RESULT
 * - ✅ Documento autentico (se Merkle proof valido)
 * - ⏳ In attesa di ancoraggio (se non ancora ancorato)
 * - ❌ Verifica fallita (se Merkle proof invalido - rare)
 * 
 * ============================================================================
 * ESEMPIO OUTPUT VERIFICA
 * ============================================================================
 * 
 * SCENARIO 1: Documento ancorato e verificato
 * 
 * Display:
 * - ✅ DOCUMENTO VERIFICATO SU BLOCKCHAIN
 * - Protocol: 12345/2025 del 15/09/2025
 * - Tipo: Delibera Giunta Comunale
 * - Titolo: "Approvazione bilancio preventivo 2026"
 * - Ente: Comune di Firenze
 * - Firmatario: Dario Nardella (Sindaco)
 * - Certificato: InfoCert Firma Qualificata CA
 * - Hash documento: a3f7d9e2c1b8f4a65e3b8c9d7f2a1b4c...
 * - Blockchain: Algorand Testnet
 * - Transaction ID: ALGO-TX-20250915143022-A1B2C3D4
 * - Link: [Vedi su Algorand Explorer]
 * - Ancorato il: 15/09/2025 14:35:00
 * 
 * SCENARIO 2: Documento in attesa di ancoraggio
 * 
 * Display:
 * - ⏳ DOCUMENTO IN ATTESA DI TOKENIZZAZIONE
 * - Protocol: 12345/2025 del 15/09/2025
 * - Tipo: Delibera Giunta Comunale
 * - Titolo: "Approvazione bilancio preventivo 2026"
 * - Ente: Comune di Firenze
 * - Firmatario: Dario Nardella (Sindaco)
 * - Hash documento: a3f7d9e2c1b8f4a65e3b8c9d7f2a1b4c...
 * - Status: Il documento sarà ancorato su blockchain nel prossimo batch (entro 24h)
 * 
 * SCENARIO 3: Codice non trovato
 * 
 * Display:
 * - ❌ CODICE VERIFICA NON VALIDO
 * - Il codice di verifica inserito non corrisponde a nessun atto tokenizzato
 * - Verifica di aver copiato correttamente il codice dal QR code o documento
 * 
 * ============================================================================
 * DATI PUBBLICI vs PRIVATI
 * ============================================================================
 * 
 * PUBBLICI (visibili su /verify):
 * ✅ Protocol number + date
 * ✅ Doc type (delibera, determina, etc.)
 * ✅ Title
 * ✅ Entity name (Comune di X)
 * ✅ Signer name, organization, role (da certificato QES)
 * ✅ Signature timestamp
 * ✅ Document hash SHA-256
 * ✅ Blockchain TXID + anchor timestamp
 * ✅ Merkle proof verification result
 * 
 * PRIVATI (non visibili):
 * ❌ Description (contenuto dettagliato)
 * ❌ PDF file (download riservato PA)
 * ❌ Internal notes
 * ❌ Collection details
 * ❌ User emails/PII
 * 
 * ============================================================================
 * SICUREZZA E PRIVACY
 * ============================================================================
 * 
 * RATE LIMITING:
 * - Suggerito: throttle:60,1 (60 verifiche/minuto per IP)
 * - Previene abuse/scraping
 * 
 * NO AUTHENTICATION:
 * - Endpoint pubblico, chiunque può verificare
 * - Dati mostrati sono già pubblici per definizione (atti PA)
 * 
 * NO PII EXPOSURE:
 * - Dati firmatario da certificato pubblico (già nel PDF firmato)
 * - No email PA interne
 * - No user accounts info
 * 
 * GDPR COMPLIANCE:
 * - Base legale: Art. 6.1.e eIDAS (obblighi legali PA)
 * - Dati minimali: Solo necessari per verifica
 * - Retention: Permanente (obblighi PA conservazione)
 * - No cookies di tracking su pagina verifica
 * 
 * ============================================================================
 * INTEGRAZIONE QR CODE
 * ============================================================================
 * 
 * QR code contiene URL verifica:
 * - URL: https://florenceegi.it/verify/VER-ABC123XYZ
 * - Stampabile su documento cartaceo
 * - Scansione smartphone → Browser apre pagina verifica
 * - Citizen-friendly: No app required, solo camera
 * 
 * ============================================================================
 * 
 * @package App\Http\Controllers\PaActs
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - PA Acts Tokenization)
 * @date 2025-10-04
 * @purpose Public verification controller for PA acts authenticity
 * 
 * @architecture Controller Layer (Public views)
 * @dependencies PaActService, MerkleTreeService, UltraLogManager
 * @middleware web (public, no auth)
 * @route GET /verify/{public_code} (name: verify.act)
 * @privacy Public data only, GDPR compliant
 */
class PaActPublicController extends Controller
{
    protected UltraLogManager $logger;
    protected PaActService $paActService;
    protected MerkleTreeService $merkleService;
    
    /**
     * Constructor - Dependency Injection
     * 
     * @param UltraLogManager $logger
     * @param PaActService $paActService
     * @param MerkleTreeService $merkleService
     */
    public function __construct(
        UltraLogManager $logger,
        PaActService $paActService,
        MerkleTreeService $merkleService
    ) {
        $this->logger = $logger;
        $this->paActService = $paActService;
        $this->merkleService = $merkleService;
        
        // No authentication required - public endpoint
    }
    
    /**
     * Display public verification page
     * 
     * @param string $publicCode Public verification code (VER-XXXXXXXXXX)
     * @return View
     * 
     * WORKFLOW:
     * 1. Find document by public code
     * 2. Extract public metadata
     * 3. Verify Merkle proof (if anchored)
     * 4. Return view with verification result
     * 
     * EXAMPLE URL:
     * /verify/VER-ABC123XYZ
     */
    public function verify(string $publicCode): View
    {
        try {
            $this->logger->info('[PaActPublicController] Public verification request', [
                'public_code' => $publicCode,
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);
            
            // STEP 1: Find document
            $egi = $this->paActService->getDocumentByPublicCode($publicCode);
            
            if (!$egi) {
                $this->logger->warning('[PaActPublicController] Public code not found', [
                    'public_code' => $publicCode
                ]);
                
                return view('pa.acts.verify', [
                    'found' => false,
                    'public_code' => $publicCode,
                    'error' => __('pa_acts.verify.not_found')
                ]);
            }
            
            // STEP 2: Extract public metadata
            $metadata = $this->extractPublicMetadata($egi);
            
            // STEP 3: Verify Merkle proof (if anchored)
            $verificationResult = $this->verifyMerkleProof($egi);
            
            // STEP 4: Log successful verification
            $this->logger->info('[PaActPublicController] Verification completed', [
                'public_code' => $publicCode,
                'egi_id' => $egi->id,
                'anchored' => $metadata['anchored'],
                'verified' => $verificationResult['verified']
            ]);
            
            return view('pa.acts.verify', [
                'found' => true,
                'public_code' => $publicCode,
                'metadata' => $metadata,
                'verification' => $verificationResult,
                'algorand_explorer_url' => $this->getAlgorandExplorerUrl($metadata['anchor_txid'])
            ]);
            
        } catch (\Exception $e) {
            $this->logger->error('[PaActPublicController] Verification error', [
                'public_code' => $publicCode,
                'error' => $e->getMessage()
            ]);
            
            return view('pa.acts.verify', [
                'found' => false,
                'public_code' => $publicCode,
                'error' => __('pa_acts.verify.error')
            ]);
        }
    }
    
    /**
     * Extract public metadata from EGI
     * 
     * @param Egi $egi
     * @return array Public metadata only
     * 
     * PUBLIC DATA:
     * - Protocol number + date
     * - Doc type
     * - Title (no description)
     * - Entity name
     * - Signer info (from certificate)
     * - Hash + blockchain data
     */
    protected function extractPublicMetadata($egi): array
    {
        $metadata = $egi->metadata ?? [];
        $signature = $metadata['signature_validation'] ?? [];
        
        return [
            // Document info
            'protocol_number' => $metadata['protocol_number'] ?? null,
            'protocol_date' => $metadata['protocol_date'] ?? null,
            'doc_type' => $metadata['doc_type'] ?? null,
            'title' => $egi->title ?? null,
            
            // Entity info
            'entity_name' => $signature['signer_organization'] ?? __('pa_acts.verify.unknown_entity'),
            
            // Signer info (from QES certificate - public data)
            'signer_cn' => $signature['signer_cn'] ?? null,
            'signer_organization' => $signature['signer_organization'] ?? null,
            'signer_role' => $signature['signer_role'] ?? null,
            'signature_timestamp' => $signature['signature_timestamp'] ?? null,
            'cert_issuer' => $signature['cert_issuer'] ?? null,
            
            // Document hash
            'doc_hash' => $metadata['doc_hash'] ?? null,
            
            // Blockchain data
            'anchored' => $metadata['anchored'] ?? false,
            'anchor_txid' => $metadata['anchor_txid'] ?? null,
            'anchor_root' => $metadata['anchor_root'] ?? null,
            'anchored_at' => $metadata['anchored_at'] ?? null
        ];
    }
    
    /**
     * Verify Merkle proof
     * 
     * @param Egi $egi
     * @return array Verification result
     * 
     * RETURN:
     * [
     *   'verified' => bool,
     *   'method' => 'merkle_proof' | 'not_anchored',
     *   'message' => 'Verification success message'
     * ]
     */
    protected function verifyMerkleProof($egi): array
    {
        $metadata = $egi->metadata ?? [];
        
        // Not anchored yet
        if (!($metadata['anchored'] ?? false)) {
            return [
                'verified' => null, // Null = pending anchoring
                'method' => 'not_anchored',
                'message' => __('pa_acts.verify.pending_anchoring')
            ];
        }
        
        // Check Merkle proof data
        $docHash = $metadata['doc_hash'] ?? null;
        $merkleProof = $metadata['merkle_proof'] ?? [];
        $merkleRoot = $metadata['anchor_root'] ?? null;
        
        if (!$docHash || empty($merkleProof) || !$merkleRoot) {
            return [
                'verified' => false,
                'method' => 'merkle_proof',
                'message' => __('pa_acts.verify.missing_proof_data')
            ];
        }
        
        // Verify Merkle proof
        try {
            $isValid = $this->merkleService->verifyProof($docHash, $merkleProof, $merkleRoot);
            
            return [
                'verified' => $isValid,
                'method' => 'merkle_proof',
                'message' => $isValid 
                    ? __('pa_acts.verify.verified_success')
                    : __('pa_acts.verify.verification_failed')
            ];
            
        } catch (\Exception $e) {
            $this->logger->error('[PaActPublicController] Merkle verification error', [
                'egi_id' => $egi->id,
                'error' => $e->getMessage()
            ]);
            
            return [
                'verified' => false,
                'method' => 'merkle_proof',
                'message' => __('pa_acts.verify.verification_error')
            ];
        }
    }
    
    /**
     * Get Algorand Explorer URL for transaction
     * 
     * @param string|null $txid Transaction ID
     * @return string|null Explorer URL
     * 
     * EXAMPLE:
     * https://testnet.algoexplorer.io/tx/ALGO-TX-20250915143022-A1B2C3D4
     */
    protected function getAlgorandExplorerUrl(?string $txid): ?string
    {
        if (!$txid) {
            return null;
        }
        
        // Testnet explorer (production uses mainnet)
        $baseUrl = config('services.algorand.explorer_url', 'https://testnet.algoexplorer.io');
        
        return "{$baseUrl}/tx/{$txid}";
    }
}
