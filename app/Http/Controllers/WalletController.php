<?php

namespace App\Http\Controllers;

use App\Models\TeamWallet;
use App\Models\Wallet;
use App\Services\Wallet\WalletProvisioningService;
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
 */
class WalletController extends Controller
{
    protected WalletProvisioningService $walletService;
    
    public function __construct(WalletProvisioningService $walletService)
    {
        $this->walletService = $walletService;
    }
    /**
     * Mostra la pagina di riscatto wallet
     * 
     * @return \Illuminate\View\View
     */
    public function redemption()
    {
        $user = Auth::user();
        
        // Trova il wallet dell'utente dalla tabella wallets
        $walletRecord = Wallet::where('user_id', $user->id)->first();
        
        // Debug log
        Log::info('Wallet redemption page accessed', [
            'user_id' => $user->id,
            'wallet_record' => $walletRecord ? $walletRecord->toArray() : null,
        ]);
        
        // Verifica che l'utente abbia un wallet
        if (!$walletRecord || empty($walletRecord->wallet)) {
            Log::warning('No wallet found for redemption', [
                'user_id' => $user->id,
            ]);
            
            return redirect()
                ->route('dashboard')
                ->with('error', __('wallet.redemption.no_wallet'));
        }
        
        $walletAddress = $walletRecord->wallet;
        
        // Verifica che sia un indirizzo Algorand valido (58 caratteri)
        if (!is_string($walletAddress) || strlen($walletAddress) !== 58) {
            Log::warning('Invalid wallet address for redemption', [
                'user_id' => $user->id,
                'wallet_address' => $walletAddress,
            ]);
            
            return redirect()
                ->route('dashboard')
                ->with('error', __('wallet.redemption.no_wallet'));
        }
        
        // Verifica se il wallet è già stato riscattato (seed phrase cancellata)
        $isRedeemed = empty($walletRecord->secret_ciphertext);
        
        return view('wallet.redemption', [
            'user' => $user,
            'walletAddress' => $walletAddress,
            'shortWallet' => substr($walletAddress, 0, 6) . '...' . substr($walletAddress, -4),
            'isRedeemed' => $isRedeemed,
            'wallet' => $walletRecord,
        ]);
    }

    /**
     * Processa la richiesta di riscatto wallet (Step 1: Conferma)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function confirmRedemption(Request $request)
    {
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
     * Genera e scarica la seed phrase (Step 2: Download)
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function downloadSeedPhrase(Request $request)
    {
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
    public function finalizeRedemption(Request $request)
    {
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
    private function decryptSeedPhrase(Wallet $wallet): string
    {
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
    private function generateSeedPhraseDocument($user, Wallet $wallet, string $seedPhrase): string
    {
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
