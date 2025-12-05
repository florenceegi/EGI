<?php

namespace App\Http\Controllers;

use App\Models\TeamWallet;
use App\Models\Wallet;
use App\Services\Wallet\WalletProvisioningService;
use App\Services\Wallet\WalletRedemptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * WalletController
 *
 * Gestisce le operazioni wallet dell'utente, incluso il riscatto del wallet
 * che permette al Creator di ottenere la seed phrase e assumere il controllo
 * completo del proprio wallet Algorand.
 *
 * @Oracode v3.0
 * @privacy-critical Gestisce seed phrase cifrate - massima sicurezza richiesta
 *
 * REDEMPTION FLOW v2.0:
 * 1. User views redemption page with cost calculation
 * 2. User confirms with typed text + accepts terms
 * 3. System: deducts EGILI, funds wallet, opt-in ASAs, transfer ASAs
 * 4. User downloads seed phrase
 * 5. User confirms, system deletes mnemonic from DB (irreversible)
 */
class WalletController extends Controller {
    protected WalletProvisioningService $walletService;
    protected WalletRedemptionService $redemptionService;

    public function __construct(
        WalletProvisioningService $walletService,
        WalletRedemptionService $redemptionService
    ) {
        $this->walletService = $walletService;
        $this->redemptionService = $redemptionService;
    }
    /**
     * Mostra la pagina di riscatto wallet
     *
     * Displays:
     * - Wallet address
     * - Number of EGIs owned (ASAs)
     * - Redemption cost (in ALGO and EGILI)
     * - Current EGILI balance
     * - Whether redemption is possible
     *
     * @return \Illuminate\View\View
     */
    public function redemption() {
        $user = Auth::user();

        // Get redemption status and validation
        $status = $this->redemptionService->getRedemptionStatus($user);

        // If no wallet, redirect
        if (!$status['has_wallet']) {
            Log::warning('No wallet found for redemption', [
                'user_id' => $user->id,
            ]);

            return redirect()
                ->route('dashboard')
                ->with('error', __('wallet.redemption.no_wallet'));
        }

        // Get wallet record
        $walletRecord = Wallet::where('user_id', $user->id)->first();

        // Debug log
        Log::info('Wallet redemption page accessed', [
            'user_id' => $user->id,
            'wallet_address' => $status['wallet_address'],
            'is_redeemed' => $status['redeemed'],
        ]);

        // If already redeemed, show redeemed state
        if ($status['redeemed']) {
            return view('wallet.redemption', [
                'user' => $user,
                'walletAddress' => $status['wallet_address'],
                'shortWallet' => substr($status['wallet_address'], 0, 6) . '...' . substr($status['wallet_address'], -4),
                'isRedeemed' => true,
                'redeemedAt' => $status['redeemed_at'],
                'wallet' => $walletRecord,
                'cost' => null,
                'validation' => null,
                'egis' => collect(),
            ]);
        }

        // Calculate cost and get validation
        $validation = $this->redemptionService->validateRedemption($user);

        // Get user's EGIs for display
        $egis = $this->redemptionService->getUserEgis($user);

        return view('wallet.redemption', [
            'user' => $user,
            'walletAddress' => $status['wallet_address'],
            'shortWallet' => substr($status['wallet_address'], 0, 6) . '...' . substr($status['wallet_address'], -4),
            'isRedeemed' => false,
            'wallet' => $walletRecord,
            'cost' => $validation['cost'],
            'validation' => $validation,
            'egis' => $egis,
            'canRedeem' => $validation['valid'],
            'egiliBalance' => $validation['egili_balance'] ?? 0,
        ]);
    }

    /**
     * Processa la richiesta di riscatto wallet (Step 1: Conferma)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function confirmRedemption(Request $request) {
        $request->validate([
            'confirmation_text' => 'required|string',
        ]);

        $user = Auth::user();
        $expectedText = 'CONFERMO RISCATTO';

        if (strtoupper(trim($request->confirmation_text)) !== $expectedText) {
            return response()->json([
                'success' => false,
                'message' => __('wallet.redemption.invalid_confirmation'),
            ], 422);
        }

        // Genera un token temporaneo per il download
        $redemptionToken = bin2hex(random_bytes(32));

        // Salva il token in sessione (valido 15 minuti)
        session([
            'wallet_redemption_token' => $redemptionToken,
            'wallet_redemption_expires' => now()->addMinutes(15),
            'wallet_redemption_user_id' => $user->id,
        ]);

        Log::channel('security')->info('Wallet redemption confirmed', [
            'user_id' => $user->id,
            'wallet' => substr($user->wallet, 0, 6) . '...',
        ]);

        return response()->json([
            'success' => true,
            'token' => $redemptionToken,
            'message' => __('wallet.redemption.confirmation_accepted'),
        ]);
    }

    /**
     * Execute full wallet redemption (NEW v2.0 Flow)
     *
     * This is the main redemption endpoint that:
     * 1. Validates user can redeem
     * 2. Deducts EGILI cost from wallet
     * 3. Funds wallet with ALGO
     * 4. Performs batch opt-in for all user's ASAs
     * 5. Transfers all ASAs from Treasury to user
     * 6. Returns mnemonic to user
     * 7. Deletes mnemonic from database (irreversible!)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function executeRedemption(Request $request)
    {
        $request->validate([
            'confirmation_text' => 'required|string',
            'accept_terms' => 'required|accepted',
        ]);

        $user = Auth::user();
        $expectedText = 'CONFERMO RISCATTO';

        // 1. Verify confirmation text
        if (strtoupper(trim($request->confirmation_text)) !== $expectedText) {
            return response()->json([
                'success' => false,
                'message' => __('wallet.redemption.invalid_confirmation'),
            ], 422);
        }

        // 2. Validate redemption is possible
        $validation = $this->redemptionService->validateRedemption($user);
        if (!$validation['valid']) {
            Log::channel('security')->warning('Wallet redemption validation failed', [
                'user_id' => $user->id,
                'errors' => $validation['errors'],
            ]);

            return response()->json([
                'success' => false,
                'message' => implode(' ', $validation['errors']),
                'errors' => $validation['errors'],
            ], 400);
        }

        // 3. Execute redemption (this is irreversible after mnemonic deletion!)
        Log::channel('security')->warning('Starting wallet redemption execution', [
            'user_id' => $user->id,
            'wallet_address' => $validation['wallet_address'],
            'cost_egili' => $validation['cost']['egili'],
            'asa_count' => $validation['cost']['breakdown']['asa_count'] ?? 0,
        ]);

        $result = $this->redemptionService->executeRedemption($user);

        if (!$result['success']) {
            Log::channel('security')->error('Wallet redemption failed', [
                'user_id' => $user->id,
                'error' => $result['error'],
            ]);

            return response()->json([
                'success' => false,
                'message' => $result['error'],
                'details' => $result['details'] ?? [],
            ], 500);
        }

        // 4. Generate downloadable document with mnemonic
        $wallet = Wallet::where('user_id', $user->id)->first();
        $document = $this->generateSeedPhraseDocument($user, $wallet, $result['mnemonic']);

        // 5. Log successful redemption
        Log::channel('security')->critical('Wallet redemption completed successfully', [
            'user_id' => $user->id,
            'wallet_address' => $validation['wallet_address'],
            'cost_egili' => $validation['cost']['egili'],
            'asa_count' => $result['details']['asa_count'] ?? 0,
            'ip' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => __('wallet.redemption.completed'),
            'mnemonic' => $result['mnemonic'], // WARNING: Only sent once!
            'document' => base64_encode($document),
            'filename' => 'wallet-seed-phrase-' . date('Y-m-d') . '.txt',
            'details' => [
                'egili_deducted' => $result['details']['egili_deducted'] ?? 0,
                'asa_transferred' => $result['details']['asa_count'] ?? 0,
                'wallet_funded' => $result['details']['funding']['amount_algo'] ?? 0,
            ],
        ]);
    }

    /**
     * API endpoint to get redemption cost calculation
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRedemptionCost()
    {
        $user = Auth::user();

        $validation = $this->redemptionService->validateRedemption($user);
        $status = $this->redemptionService->getRedemptionStatus($user);

        return response()->json([
            'success' => true,
            'can_redeem' => $validation['valid'],
            'is_redeemed' => $status['redeemed'],
            'cost' => $validation['cost'],
            'egili_balance' => $validation['egili_balance'] ?? 0,
            'errors' => $validation['errors'],
        ]);
    }

    /**
     * API endpoint to get user's EGIs (ASAs) for redemption preview
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserEgisForRedemption()
    {
        $user = Auth::user();

        $egis = $this->redemptionService->getUserEgis($user);

        return response()->json([
            'success' => true,
            'count' => $egis->count(),
            'egis' => $egis->map(function ($egi) {
                return [
                    'id' => $egi->id,
                    'title' => $egi->title,
                    'collection_name' => $egi->collection->name ?? 'N/A',
                    'asa_id' => $egi->blockchain->asa_id ?? null,
                    'minted_at' => $egi->blockchain->minted_at ?? null,
                ];
            }),
        ]);
    }

    /**
     * Genera e scarica la seed phrase (Step 2: Download)
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function downloadSeedPhrase(Request $request) {
        $token = $request->query('token');
        $user = Auth::user();

        // Verifica token
        $sessionToken = session('wallet_redemption_token');
        $expires = session('wallet_redemption_expires');
        $userId = session('wallet_redemption_user_id');

        if (!$token || $token !== $sessionToken || $userId !== $user->id) {
            abort(403, __('wallet.redemption.invalid_token'));
        }

        if (now()->gt($expires)) {
            session()->forget(['wallet_redemption_token', 'wallet_redemption_expires', 'wallet_redemption_user_id']);
            abort(403, __('wallet.redemption.token_expired'));
        }

        // Trova il wallet con la seed phrase
        $wallet = Wallet::where('user_id', $user->id)
            ->whereNotNull('secret_ciphertext')
            ->first();

        if (!$wallet) {
            abort(404, __('wallet.redemption.wallet_not_found'));
        }

        // Decifra la seed phrase
        try {
            $seedPhrase = $this->decryptSeedPhrase($wallet);
        } catch (\Exception $e) {
            Log::channel('security')->error('Failed to decrypt seed phrase', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            abort(500, __('wallet.redemption.decryption_failed'));
        }

        // Genera il contenuto del file
        $content = $this->generateSeedPhraseDocument($user, $wallet, $seedPhrase);

        // Log dell'evento
        Log::channel('security')->warning('Seed phrase downloaded - wallet redemption', [
            'user_id' => $user->id,
            'wallet' => substr($user->wallet, 0, 6) . '...',
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Pulisci la sessione
        session()->forget(['wallet_redemption_token', 'wallet_redemption_expires', 'wallet_redemption_user_id']);

        // Restituisci il file
        return response($content)
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', 'attachment; filename="wallet-seed-phrase-' . date('Y-m-d') . '.txt"')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate');
    }

    /**
     * Finalizza il riscatto cancellando la seed phrase dal DB (Step 3)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function finalizeRedemption(Request $request) {
        $request->validate([
            'confirm_deletion' => 'required|accepted',
        ]);

        $user = Auth::user();

        $wallet = Wallet::where('user_id', $user->id)
            ->whereNotNull('secret_ciphertext')
            ->first();

        if (!$wallet) {
            return response()->json([
                'success' => false,
                'message' => __('wallet.redemption.already_redeemed'),
            ], 400);
        }

        // Cancella la seed phrase in modo sicuro
        $wallet->update([
            'secret_ciphertext' => null,
            'secret_nonce' => null,
            'secret_tag' => null,
            'dek_encrypted' => null,
        ]);

        Log::channel('security')->critical('Wallet fully redeemed - seed phrase deleted', [
            'user_id' => $user->id,
            'wallet' => substr($user->wallet, 0, 6) . '...',
            'ip' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => __('wallet.redemption.completed'),
        ]);
    }

    /**
     * Decifra la seed phrase dal wallet
     *
     * @param Wallet $wallet
     * @return string
     * @throws \Exception
     */
    private function decryptSeedPhrase(Wallet $wallet): string {
        if (empty($wallet->secret_ciphertext)) {
            throw new \Exception('No encrypted seed phrase found');
        }

        $user = Auth::user();

        // Usa il WalletProvisioningService per decriptare la mnemonic
        // Il servizio gestisce envelope encryption (DEK + KMS)
        $mnemonic = $this->walletService->retrieveMnemonic($wallet, $user);

        return $mnemonic;
    }

    /**
     * Genera il documento con la seed phrase
     *
     * @param \App\Models\User $user
     * @param Wallet $wallet
     * @param string $seedPhrase
     * @return string
     */
    private function generateSeedPhraseDocument($user, Wallet $wallet, string $seedPhrase): string {
        $date = now()->format('d/m/Y H:i:s');
        $network = config('algorand.network', 'testnet');

        return <<<EOT
╔══════════════════════════════════════════════════════════════════════╗
║                    FLORENCE EGI - WALLET SEED PHRASE                ║
║                         DOCUMENTO RISERVATO                          ║
╚══════════════════════════════════════════════════════════════════════╝

⚠️  ATTENZIONE: QUESTO DOCUMENTO CONTIENE INFORMAZIONI CRITICHE
    Conservalo in un luogo sicuro e non condividerlo con nessuno!

═══════════════════════════════════════════════════════════════════════

📅 Data riscatto: {$date}
🔗 Rete: {$network}
👤 Utente: {$user->email}
💼 Indirizzo Wallet: {$wallet->wallet}

═══════════════════════════════════════════════════════════════════════

🔐 SEED PHRASE (25 parole):

{$seedPhrase}

═══════════════════════════════════════════════════════════════════════

📋 ISTRUZIONI:

1. Scrivi queste 25 parole su carta e conservala in un luogo sicuro
2. NON salvare questo file sul computer o nel cloud
3. NON condividere mai la seed phrase con nessuno
4. Usa queste parole per importare il wallet in Pera Wallet o altri wallet Algorand
5. Dopo aver verificato l'accesso al wallet, elimina questo file

⚠️  AVVERTENZA:
    Chiunque conosca questa seed phrase avrà accesso completo ai tuoi fondi.
    FlorenceEGI NON conserva più una copia di queste parole.
    Se le perdi, non potremo aiutarti a recuperare i fondi.

═══════════════════════════════════════════════════════════════════════

🏛️ FlorenceEGI - Digital Renaissance Platform
   Documento generato automaticamente - Non modificare

EOT;
    }
}
