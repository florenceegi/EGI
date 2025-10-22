<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use App\Rules\ValidIban;
use App\Services\Wallet\WalletProvisioningService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Ultra\UltraLogManager\UltraLogManager;

class WalletWelcomeController extends Controller
{
    protected WalletProvisioningService $walletService;
    protected ErrorManagerInterface $errorManager;
    protected UltraLogManager $logger;

    public function __construct(
        WalletProvisioningService $walletService,
        ErrorManagerInterface $errorManager,
        UltraLogManager $logger
    ) {
        $this->walletService = $walletService;
        $this->errorManager = $errorManager;
        $this->logger = $logger;
    }

    /**
     * Get wallet welcome data for authenticated user
     */
    public function getData(): JsonResponse
    {
        try {
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Unauthenticated'
                ], 401);
            }

            $user = Auth::user();
            
            // Get user's wallet
            $wallet = Wallet::where('user_id', $user->id)
                ->whereNotNull('secret_ciphertext')
                ->first();

            if (!$wallet) {
                return response()->json([
                    'success' => false,
                    'error' => 'Wallet not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                    ],
                    'wallet' => [
                        'id' => $wallet->id,
                        'address' => $wallet->wallet,
                        'has_iban' => $wallet->hasIban(),
                        'masked_iban' => $wallet->getMaskedIbanAttribute(),
                    ],
                    'should_show' => session()->has('show_wallet_welcome'),
                ]
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Failed to load wallet welcome data', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to load data'
            ], 500);
        }
    }

    /**
     * Add IBAN to user's wallet
     */
    public function addIban(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'iban' => ['required', 'string', 'max:34', new ValidIban()],
                'dont_show_again' => ['sometimes', 'boolean'],
            ]);

            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'error' => __('register.unauthenticated')
                ], 401);
            }

            $user = Auth::user();
            
            // Get user's wallet
            $wallet = Wallet::where('user_id', $user->id)
                ->whereNotNull('secret_ciphertext')
                ->first();

            if (!$wallet) {
                return response()->json([
                    'success' => false,
                    'error' => __('register.wallet_not_found')
                ], 404);
            }

            // Add IBAN to wallet
            $this->walletService->addIbanToWallet($wallet->id, $request->input('iban'));

            // Save preference if requested
            if ($request->input('dont_show_again', false)) {
                $user->update(['preferences->hide_wallet_welcome' => true]);
            }

            // Clear session flag
            session()->forget('show_wallet_welcome');

            $this->logger->info('IBAN added to wallet via welcome modal', [
                'user_id' => $user->id,
                'wallet_id' => $wallet->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => __('register.wallet_iban_added_success'),
                'data' => [
                    'masked_iban' => $wallet->fresh()->getMaskedIbanAttribute(),
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'error' => __('register.invalid_iban'),
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            $this->logger->error('Failed to add IBAN to wallet', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => __('register.wallet_iban_add_failed')
            ], 500);
        }
    }

    /**
     * Skip IBAN and close modal
     */
    public function skipIban(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'dont_show_again' => ['sometimes', 'boolean'],
            ]);

            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'error' => __('register.unauthenticated')
                ], 401);
            }

            $user = Auth::user();

            // Save preference if requested
            if ($request->input('dont_show_again', false)) {
                $user->update(['preferences->hide_wallet_welcome' => true]);
            }

            // Clear session flag
            session()->forget('show_wallet_welcome');

            $this->logger->info('User skipped IBAN setup in welcome modal', [
                'user_id' => $user->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => __('register.wallet_welcome_completed')
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Failed to skip IBAN setup', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to complete setup'
            ], 500);
        }
    }
}

