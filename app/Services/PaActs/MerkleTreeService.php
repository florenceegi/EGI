<?php

namespace App\Services\PaActs;

use Illuminate\Support\Str;

/**
 * Merkle Tree Service for Batch Document Anchoring
 * 
 * ============================================================================
 * CONTESTO BUSINESS - BATCH ANCHORING OTTIMIZZATO
 * ============================================================================
 * 
 * PROBLEMA DA RISOLVERE:
 * Quando un ente PA carica 50 atti in un giorno, ancorarli singolarmente
 * su blockchain costa:
 * - 50 transazioni separate
 * - 50 × fee blockchain (~0.001 ALGO × 50 = 0.05 ALGO)
 * - 50 × latency network (~3 sec × 50 = 150 secondi)
 * - Inefficiente e costoso
 * 
 * SOLUZIONE: BATCH ANCHORING CON MERKLE TREE
 * Invece di 50 transazioni, una SOLA transazione che ancora tutti i documenti:
 * 1. Costruire Merkle tree con i 50 hash documenti
 * 2. Calcolare Merkle root (hash radice dell'albero)
 * 3. Ancorare SOLO il Merkle root su blockchain (1 TX)
 * 4. Generare Merkle proof per ogni documento
 * 5. Ogni documento può essere verificato indipendentemente con il proprio proof
 * 
 * VANTAGGI:
 * - ✅ 50 documenti → 1 transazione blockchain = 98% risparmio fee
 * - ✅ Verifica indipendente di ogni documento (con Merkle proof)
 * - ✅ Integrità batch garantita (se 1 hash cambia, root cambia)
 * - ✅ Standard industria (Bitcoin, Ethereum, NFT metadata)
 * 
 * ESEMPIO CONCRETO:
 * Comune di Firenze carica 50 delibere il 15/09/2025:
 * - Batch ID: BATCH-20250915-001
 * - 50 hash SHA-256 documenti
 * - Merkle root: 3a7f8e9c2b1d4f6a... (unico hash rappresenta tutti)
 * - Algorand TX: ALGO-TX-20250915143022-A1B2C3D4 (ancora solo il root)
 * - Ogni delibera ha Merkle proof (3-6 hash) per verifica
 * 
 * ============================================================================
 * COS'È UN MERKLE TREE
 * ============================================================================
 * 
 * DEFINIZIONE:
 * Struttura dati ad albero binario dove ogni nodo foglia contiene l'hash
 * di un dato, e ogni nodo interno contiene l'hash combinato dei suoi figli.
 * Il nodo radice (Merkle root) rappresenta crittograficamente l'intero dataset.
 * 
 * STRUTTURA ALBERO (esempio 4 documenti):
 * 
 *                    ROOT (Merkle Root)
 *                   /                  \
 *              H(AB)                    H(CD)
 *             /     \                  /     \
 *          H(A)    H(B)             H(C)    H(D)
 *          ↑        ↑                ↑        ↑
 *        Doc1     Doc2             Doc3     Doc4
 * 
 * Dove:
 * - H(A) = SHA-256(documento1.pdf)
 * - H(B) = SHA-256(documento2.pdf)
 * - H(AB) = SHA-256(H(A) || H(B))  [concatenazione hash figli]
 * - ROOT = SHA-256(H(AB) || H(CD)) [hash finale = Merkle root]
 * 
 * ALGORITMO COSTRUZIONE:
 * 1. Level 0 (foglie): Hash di ogni documento
 * 2. Level 1: Combina coppie di foglie (hash(left + right))
 * 3. Level 2: Combina coppie di Level 1
 * 4. Ripeti fino ad avere 1 solo nodo (root)
 * 5. Se numero dispari nodi: duplica l'ultimo nodo
 * 
 * MERKLE PROOF:
 * Per dimostrare che documento1 fa parte del batch, fornisci:
 * - H(A) = hash documento1
 * - H(B) = hash fratello (serve per calcolare H(AB))
 * - H(CD) = hash zio (serve per calcolare ROOT)
 * 
 * Verifica:
 * 1. Calcola H(AB) = SHA-256(H(A) || H(B))
 * 2. Calcola ROOT = SHA-256(H(AB) || H(CD))
 * 3. Confronta ROOT calcolato con ROOT su blockchain
 * 4. Se match → documento1 è autentico
 * 
 * ESEMPIO REALE:
 * Delibera_2025_123.pdf:
 * - Hash: a3f7d9e2c1b8f4a6...
 * - Merkle proof: [b1c2d3e4..., c5d6e7f8..., d9e0f1a2...]
 * - Merkle root (blockchain): 3a7f8e9c2b1d4f6a...
 * - Verifica: SHA-256(SHA-256(a3f7... || b1c2...) || c5d6...) == 3a7f...
 * - Risultato: ✅ Documento verificato con 3 hash (invece di rivalidare 50 doc)
 * 
 * ============================================================================
 * COSA FA QUESTO SERVICE
 * ============================================================================
 * 
 * COMPITO PRINCIPALE:
 * Costruire Merkle tree da array di hash documenti e generare proof per verifica.
 * 
 * METODI PUBBLICI:
 * 
 * 1. buildTree(array $hashes): void
 *    - Input: Array di hash SHA-256 documenti
 *    - Costruisce albero binario completo
 *    - Salva struttura interna per proof generation
 * 
 * 2. getRoot(): string
 *    - Output: Merkle root (hash radice)
 *    - Questo è l'hash che va ancorato su blockchain
 * 
 * 3. getProof(string $hash): array
 *    - Input: Hash di un documento specifico
 *    - Output: Array di hash necessari per verifica
 *    - Usato per generare proof da salvare in egis.metadata
 * 
 * 4. verifyProof(string $hash, array $proof, string $root): bool
 *    - Input: Hash documento + proof + root da blockchain
 *    - Output: true se documento è autentico
 *    - Usato nella pagina pubblica di verifica
 * 
 * UTILIZZO NEL SISTEMA:
 * 
 * FASE 1 - UPLOAD BATCH (AlgorandService::anchorBatch):
 * ```php
 * $merkleService = new MerkleTreeService();
 * $merkleService->buildTree([
 *     'a3f7d9e2c1b8f4a6...', // Delibera 123
 *     'b1c2d3e4f5a6b7c8...', // Determina 456
 *     'c5d6e7f8a9b0c1d2...'  // Ordinanza 789
 * ]);
 * 
 * $merkleRoot = $merkleService->getRoot(); // 3a7f8e9c2b1d4f6a...
 * 
 * // Ancora solo il root su blockchain
 * $algorandService->anchorDocument($merkleRoot);
 * 
 * // Genera proof per ogni documento
 * foreach ($hashes as $hash) {
 *     $proof = $merkleService->getProof($hash);
 *     // Salva in egis.metadata['merkle_proof']
 * }
 * ```
 * 
 * FASE 2 - VERIFICA PUBBLICA (PaActPublicController::verify):
 * ```php
 * $egi = Egi::where('metadata->public_code', $code)->first();
 * $docHash = $egi->metadata['doc_hash'];
 * $merkleProof = $egi->metadata['merkle_proof'];
 * $merkleRoot = $egi->metadata['anchor_root'];
 * 
 * $merkleService = new MerkleTreeService();
 * $isValid = $merkleService->verifyProof($docHash, $merkleProof, $merkleRoot);
 * 
 * if ($isValid) {
 *     // Mostra: ✅ Documento verificato su blockchain
 * }
 * ```
 * 
 * DATI SALVATI:
 * - egis.metadata['anchor_root'] = Merkle root (da blockchain TX)
 * - egis.metadata['merkle_proof'] = Array hash per verifica
 * - egis.metadata['anchor_txid'] = Transaction ID Algorand
 * 
 * ============================================================================
 * IMPLEMENTAZIONE ALGORITMO
 * ============================================================================
 * 
 * QUESTO È UN ALGORITMO REALE - NO MOCK
 * 
 * Matematica pura: SHA-256 + binary tree traversal.
 * Funziona in development e production identicamente.
 * 
 * COMPLESSITÀ:
 * - Costruzione: O(n log n) dove n = numero documenti
 * - Proof generation: O(log n)
 * - Verifica: O(log n)
 * - Spazio: O(n)
 * 
 * ESEMPIO 8 DOCUMENTI:
 * - Depth tree: 3 livelli
 * - Proof size: 3 hash (log₂ 8 = 3)
 * - Verifica: 3 operazioni SHA-256
 * 
 * ESEMPIO 100 DOCUMENTI:
 * - Depth tree: 7 livelli
 * - Proof size: 7 hash (log₂ 100 ≈ 6.64 → 7)
 * - Verifica: 7 operazioni SHA-256
 * 
 * SCALABILITÀ:
 * - 1000 documenti: proof size 10 hash
 * - 10000 documenti: proof size 14 hash
 * - Estremamente efficiente per batch grandi
 * 
 * ============================================================================
 * INTEGRAZIONE CON ALTRI SERVICES
 * ============================================================================
 * 
 * CHIAMATO DA:
 * - AlgorandService::anchorBatch() - Genera root per blockchain TX
 * - PaActService::tokenizeBatch() - Genera proof per ogni documento
 * 
 * UTILIZZATO DA:
 * - PaActPublicController::verify() - Verifica Merkle proof pubblicamente
 * - PaActController::show() - Display Merkle info in detail page
 * 
 * DATI DIPENDENTI:
 * - Input: Hash SHA-256 documenti (da hash_file('sha256', $pdfPath))
 * - Output: Merkle root + proofs (salvati in egis.metadata)
 * 
 * ============================================================================
 * SICUREZZA E INTEGRITÀ
 * ============================================================================
 * 
 * PROPRIETÀ CRITTOGRAFICHE:
 * - ✅ Collision resistance: SHA-256 rende impossibile generare 2 input con stesso hash
 * - ✅ Preimage resistance: Da Merkle root impossibile risalire ai documenti originali
 * - ✅ Avalanche effect: Cambio 1 bit in 1 documento → Merkle root completamente diverso
 * 
 * ATTACCHI IMPOSSIBILI:
 * - ❌ Sostituire documento: Hash cambia → proof invalido
 * - ❌ Riordinare documenti: Ordine cambia hash intermedi → root diverso
 * - ❌ Aggiungere documento: Albero cambia → root diverso
 * - ❌ Rimuovere documento: Albero cambia → root diverso
 * 
 * VERIFICABILITÀ PUBBLICA:
 * - Chiunque con hash + proof + root può verificare autenticità
 * - Non serve fidarsi di FlorenceEGI o PA
 * - Verifica matematica basata su blockchain immutabile
 * - Trust-minimized verification
 * 
 * ============================================================================
 * 
 * @package App\Services\PaActs
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - PA Acts Tokenization)
 * @date 2025-10-04
 * @purpose Real Merkle tree implementation for batch document anchoring optimization
 * 
 * @architecture Service Layer Pattern (Pure Algorithm - No Dependencies)
 * @dependencies NONE (standalone cryptographic implementation)
 * @algorithm Binary Merkle tree with SHA-256
 * @complexity O(n log n) construction, O(log n) proof generation/verification
 */
class MerkleTreeService {
    /**
     * Tree structure: array of levels, each level is array of hashes
     * Level 0 = leaves (document hashes)
     * Level N = root (single hash)
     * @var array
     */
    protected array $tree = [];

    /**
     * Original document hashes (leaves)
     * @var array
     */
    protected array $leaves = [];

    /**
     * Merkle root (top of tree)
     * @var string|null
     */
    protected ?string $root = null;

    /**
     * Build Merkle tree from array of document hashes
     * 
     * @param array $hashes Array of SHA-256 hashes (hex strings)
     * @return void
     * 
     * ALGORITMO:
     * 1. Salva hashes come foglie (level 0)
     * 2. Se numero dispari: duplica ultimo hash
     * 3. Combina coppie di hash con SHA-256(hash1 || hash2)
     * 4. Ripeti per ogni livello fino ad avere 1 hash (root)
     * 
     * ESEMPIO:
     * Input: ['abc123', 'def456', 'ghi789']
     * Level 0: ['abc123', 'def456', 'ghi789', 'ghi789'] (duplicato)
     * Level 1: [hash('abc123'+'def456'), hash('ghi789'+'ghi789')]
     * Level 2: [hash(Level1[0]+Level1[1])] = ROOT
     */
    public function buildTree(array $hashes): void {
        if (empty($hashes)) {
            throw new \InvalidArgumentException('Cannot build Merkle tree from empty hash array');
        }

        // Reset state
        $this->tree = [];
        $this->leaves = $hashes;
        $this->root = null;

        // Level 0: original hashes (leaves)
        $currentLevel = $hashes;
        $this->tree[] = $currentLevel;

        // Build tree bottom-up until we have single root
        while (count($currentLevel) > 1) {
            $currentLevel = $this->buildLevel($currentLevel);
            $this->tree[] = $currentLevel;
        }

        // Root is the single hash at top level
        $this->root = $currentLevel[0];
    }

    /**
     * Build next level of tree by combining pairs of hashes
     * 
     * @param array $level Current level hashes
     * @return array Next level hashes (half size)
     * 
     * ALGORITMO:
     * - Itera coppie di hash (i=0,2,4,...)
     * - Per ogni coppia: combina con SHA-256(left || right)
     * - Se numero dispari: duplica ultimo hash
     */
    protected function buildLevel(array $level): array {
        $nextLevel = [];
        $count = count($level);

        // Se numero dispari, duplica ultimo hash
        if ($count % 2 !== 0) {
            $level[] = $level[$count - 1];
            $count++;
        }

        // Combina coppie di hash
        for ($i = 0; $i < $count; $i += 2) {
            $left = $level[$i];
            $right = $level[$i + 1];

            // Combina hash: SHA-256(left || right)
            $combined = $this->combineHashes($left, $right);
            $nextLevel[] = $combined;
        }

        return $nextLevel;
    }

    /**
     * Combine two hashes into one
     * 
     * @param string $left Left hash (hex)
     * @param string $right Right hash (hex)
     * @return string Combined hash (hex)
     * 
     * ALGORITMO:
     * 1. Concatena left + right
     * 2. Calcola SHA-256 della concatenazione
     * 3. Return hex string
     */
    protected function combineHashes(string $left, string $right): string {
        // Concatena e calcola hash
        return hash('sha256', $left . $right);
    }

    /**
     * Get Merkle root (top of tree)
     * 
     * @return string Merkle root hash (hex)
     * @throws \RuntimeException If tree not built
     * 
     * QUESTO È L'HASH DA ANCORARE SU BLOCKCHAIN
     */
    public function getRoot(): string {
        if ($this->root === null) {
            throw new \RuntimeException('Tree not built. Call buildTree() first.');
        }

        return $this->root;
    }

    /**
     * Generate Merkle proof for specific document hash
     * 
     * @param string $hash Document hash to prove
     * @return array Merkle proof (array of hashes needed for verification)
     * @throws \RuntimeException If tree not built or hash not found
     * 
     * MERKLE PROOF:
     * Array di hash "fratelli" necessari per ricostruire il path fino al root.
     * 
     * ESEMPIO (8 documenti, provare doc #2):
     *                  ROOT
     *               /        \
     *           H(0-3)       H(4-7)    → Proof[2] = H(4-7)
     *          /      \
     *      H(0-1)    H(2-3)            → Proof[1] = H(2-3)
     *     /    \
     *   H(0)  H(1)                     → Proof[0] = H(1)
     *          ↑
     *       DOC #2
     * 
     * Proof for doc #2: [H(1), H(2-3), H(4-7)]
     * 
     * Verifica:
     * Step 1: hash(H(0) + H(1)) = H(0-1)
     * Step 2: hash(H(0-1) + H(2-3)) = H(0-3)
     * Step 3: hash(H(0-3) + H(4-7)) = ROOT ✅
     */
    public function getProof(string $hash): array {
        if (empty($this->tree)) {
            throw new \RuntimeException('Tree not built. Call buildTree() first.');
        }

        // Trova indice dell'hash nelle foglie (level 0)
        $index = array_search($hash, $this->leaves);

        if ($index === false) {
            throw new \RuntimeException("Hash not found in tree: {$hash}");
        }

        $proof = [];
        $currentIndex = $index;

        // Risale l'albero dal basso verso l'alto
        foreach ($this->tree as $levelIndex => $level) {
            // Skip root level (ultimo livello)
            if (count($level) === 1) {
                break;
            }

            // Determina se il nodo è a sinistra o destra
            $isLeft = ($currentIndex % 2 === 0);

            // Aggiungi hash del fratello (sibling) al proof
            $siblingIndex = $isLeft ? $currentIndex + 1 : $currentIndex - 1;

            // Verifica che sibling esiste (potrebbe non esistere nell'ultimo nodo dispari)
            if (isset($level[$siblingIndex])) {
                $proof[] = [
                    'hash' => $level[$siblingIndex],
                    'position' => $isLeft ? 'right' : 'left' // Posizione del sibling rispetto al nodo corrente
                ];
            }

            // Sali al livello successivo (parent node)
            $currentIndex = intdiv($currentIndex, 2);
        }

        return $proof;
    }

    /**
     * Verify Merkle proof for document hash
     * 
     * @param string $hash Document hash to verify
     * @param array $proof Merkle proof (from getProof())
     * @param string $expectedRoot Expected Merkle root (from blockchain)
     * @return bool True if proof is valid
     * 
     * ALGORITMO VERIFICA:
     * 1. Inizia con hash documento
     * 2. Per ogni elemento proof:
     *    - Se sibling a destra: hash(current || sibling)
     *    - Se sibling a sinistra: hash(sibling || current)
     * 3. Risultato finale deve matchare expectedRoot
     * 
     * ESEMPIO:
     * Hash doc: a3f7d9e2...
     * Proof: [
     *   ['hash' => 'b1c2d3e4...', 'position' => 'right'],
     *   ['hash' => 'c5d6e7f8...', 'position' => 'left']
     * ]
     * Root: 3a7f8e9c...
     * 
     * Step 1: hash(a3f7... || b1c2...) = temp1
     * Step 2: hash(c5d6... || temp1) = calculatedRoot
     * Step 3: calculatedRoot === 3a7f... ? TRUE ✅
     */
    public function verifyProof(string $hash, array $proof, string $expectedRoot): bool {
        $currentHash = $hash;

        // Risali l'albero usando proof
        foreach ($proof as $proofElement) {
            $siblingHash = $proofElement['hash'];
            $position = $proofElement['position'];

            // Combina current con sibling nell'ordine corretto
            if ($position === 'right') {
                // Sibling a destra: hash(current || sibling)
                $currentHash = $this->combineHashes($currentHash, $siblingHash);
            } else {
                // Sibling a sinistra: hash(sibling || current)
                $currentHash = $this->combineHashes($siblingHash, $currentHash);
            }
        }

        // Verifica che hash calcolato corrisponda al root atteso
        return $currentHash === $expectedRoot;
    }

    /**
     * Get tree structure (for debugging/visualization)
     * 
     * @return array Complete tree structure
     */
    public function getTree(): array {
        return $this->tree;
    }

    /**
     * Get tree depth (number of levels)
     * 
     * @return int Tree depth
     */
    public function getDepth(): int {
        return count($this->tree);
    }

    /**
     * Get number of leaves (documents)
     * 
     * @return int Number of documents in tree
     */
    public function getLeafCount(): int {
        return count($this->leaves);
    }
}
