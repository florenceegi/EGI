<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\Blockchain\AlgorandClient;
use App\Services\CollectionService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * @Oracode Controller for REAL Algorand wallet connection
 * 🎯 Purpose: Manages authentication with real Algorand wallet addresses
 * 🧱 Core Logic: Validates wallet on-chain, creates/connects weak auth users
 * 🛡️ GDPR: Minimal data collection, wallet as identifier
 *
 * @package App\Http\Controllers
 * @author Padmin D. Curtis for Fabio Cherici
 * @version 1.0.0 (Real Wallet Integration)
 * @date 2025-11-25
 *
 * @core-features
 * 1. Real Algorand address validation (format + on-chain existence)
 * 2. Three-path flow: existing user / new weak user / redirect to register
 * 3. Session-based weak authentication via FegiGuard
 *
 * @security-model
 * - Address format validation (58 chars, Base32)
 * - On-chain verification via Algorand microservice
 * - Session-based authentication state
 *
 * @signature [RealWalletConnectController::v1.0] florence-egi-real-wallet
 */
class RealWalletConnectController extends Controller {
    private UltraLogManager $logger;
    private ErrorManagerInterface $errorManager;
    private AlgorandClient $algorandClient;
    private CollectionService $collectionService;

    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        AlgorandClient $algorandClient,
        CollectionService $collectionService
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->algorandClient = $algorandClient;
        $this->collectionService = $collectionService;
    }

    /**
     * @Oracode Verify if an Algorand address is valid and check its status
     * 📡 API: POST /wallet/real/verify
     *
     * @param Request $request
     * @return JsonResponse
     *
     * Response scenarios:
     * - wallet_found_user_exists: Wallet belongs to registered user
     * - wallet_found_no_user: Valid on-chain wallet, no user in system
     * - wallet_not_on_chain: Valid format but not found on blockchain
     * - invalid_format: Address format is wrong
     */
    public function verify(Request $request): JsonResponse {
        $this->logger->info('=== REAL WALLET VERIFY START ===', [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'session_id' => $request->session()->getId()
        ]);

        try {
            // 1. Validate input format
            $validated = $request->validate([
                'wallet_address' => ['required', 'string', 'size:58', 'regex:/^[A-Z2-7]+$/i']
            ]);

            $walletAddress = strtoupper(trim($validated['wallet_address']));

            $this->logger->info('Real Wallet: Address format validated', [
                'wallet_prefix' => substr($walletAddress, 0, 8) . '...',
            ]);

            // 2. Check if wallet already exists in our system
            $existingUser = User::where('wallet', $walletAddress)->first();

            if ($existingUser) {
                $this->logger->info('Real Wallet: User found in system', [
                    'user_id' => $existingUser->id,
                    'is_weak_auth' => $existingUser->is_weak_auth
                ]);

                return response()->json([
                    'success' => true,
                    'status' => 'wallet_found_user_exists',
                    'message' => trans('collection.wallet.real_wallet_user_found'),
                    'wallet_address' => $walletAddress,
                    'user_name' => $existingUser->name,
                    'is_weak_auth' => $existingUser->is_weak_auth,
                    'can_connect' => true
                ]);
            }

            // 3. Verify wallet exists on Algorand blockchain
            $onChainExists = $this->verifyWalletOnChain($walletAddress);

            if ($onChainExists) {
                $this->logger->info('Real Wallet: Valid on-chain, no user in system', [
                    'wallet_prefix' => substr($walletAddress, 0, 8) . '...',
                ]);

                // Store wallet in session for subsequent actions
                $request->session()->put('pending_wallet_address', $walletAddress);

                return response()->json([
                    'success' => true,
                    'status' => 'wallet_found_no_user',
                    'message' => trans('collection.wallet.real_wallet_verified_no_user'),
                    'wallet_address' => $walletAddress,
                    'options' => [
                        'register' => trans('collection.wallet.option_register_full'),
                        'continue_guest' => trans('collection.wallet.option_continue_guest')
                    ]
                ]);
            }

            // 4. Wallet not found on chain - might be new/unfunded
            $this->logger->info('Real Wallet: Not found on blockchain', [
                'wallet_prefix' => substr($walletAddress, 0, 8) . '...',
            ]);

            // Still allow connection - user might have just created the wallet
            $request->session()->put('pending_wallet_address', $walletAddress);

            return response()->json([
                'success' => true,
                'status' => 'wallet_not_on_chain',
                'message' => trans('collection.wallet.real_wallet_not_on_chain'),
                'wallet_address' => $walletAddress,
                'warning' => trans('collection.wallet.real_wallet_not_funded_warning'),
                'options' => [
                    'register' => trans('collection.wallet.option_register_full'),
                    'continue_guest' => trans('collection.wallet.option_continue_guest')
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->logger->warning('Real Wallet: Invalid format', [
                'errors' => $e->errors(),
                'ip' => $request->ip()
            ]);

            return response()->json([
                'success' => false,
                'status' => 'invalid_format',
                'message' => trans('collection.wallet.real_wallet_invalid_format'),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            $this->logger->error('Real Wallet: Verification error', [
                'error' => $e->getMessage(),
                'trace' => substr($e->getTraceAsString(), 0, 500)
            ]);

            return $this->errorManager->handle('REAL_WALLET_VERIFY_FAILED', [
                'message' => $e->getMessage()
            ], $e);
        }
    }

    /**
     * @Oracode Connect an existing user by wallet address
     * 📡 API: POST /wallet/real/connect
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function connect(Request $request): JsonResponse {
        $this->logger->info('=== REAL WALLET CONNECT START ===', [
            'ip' => $request->ip(),
            'session_id' => $request->session()->getId()
        ]);

        try {
            $validated = $request->validate([
                'wallet_address' => ['required', 'string', 'size:58', 'regex:/^[A-Z2-7]+$/i']
            ]);

            $walletAddress = strtoupper(trim($validated['wallet_address']));

            // Find existing user
            $user = User::where('wallet', $walletAddress)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'status' => 'user_not_found',
                    'message' => trans('collection.wallet.real_wallet_user_not_found')
                ], 404);
            }

            // Establish session
            $this->establishConnectedSession($request, $user, $walletAddress);

            $this->logger->info('Real Wallet: Connection successful', [
                'user_id' => $user->id,
                'is_weak_auth' => $user->is_weak_auth
            ]);

            return response()->json([
                'success' => true,
                'message' => trans('collection.wallet.real_wallet_connected'),
                'wallet_address' => $walletAddress,
                'user_status' => $this->getUserStatus($user),
                'user_name' => $user->name,
                'redirect' => $user->is_weak_auth ? null : route('dashboard')
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Real Wallet: Connection error', [
                'error' => $e->getMessage()
            ]);

            return $this->errorManager->handle('REAL_WALLET_CONNECT_FAILED', [
                'message' => $e->getMessage()
            ], $e);
        }
    }

    /**
     * @Oracode Create weak auth user with real wallet address
     * 📡 API: POST /wallet/real/create-guest
     *
     * Flow: User has real wallet, wants to continue as guest (weak auth)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function createGuest(Request $request): JsonResponse {
        $this->logger->info('=== REAL WALLET CREATE GUEST START ===', [
            'ip' => $request->ip(),
            'session_id' => $request->session()->getId()
        ]);

        try {
            // Get wallet from session or request
            $walletAddress = $request->input('wallet_address')
                ?? $request->session()->get('pending_wallet_address');

            if (!$walletAddress) {
                return response()->json([
                    'success' => false,
                    'message' => trans('collection.wallet.real_wallet_missing_address')
                ], 400);
            }

            // Validate format
            $walletAddress = strtoupper(trim($walletAddress));
            if (strlen($walletAddress) !== 58 || !preg_match('/^[A-Z2-7]+$/', $walletAddress)) {
                return response()->json([
                    'success' => false,
                    'message' => trans('collection.wallet.real_wallet_invalid_format')
                ], 422);
            }

            // Check if wallet already taken
            if (User::where('wallet', $walletAddress)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => trans('collection.wallet.real_wallet_already_registered')
                ], 409);
            }

            // Create anonymous email
            $uniqueEmail = 'wallet_' . Str::random(12) . '@florenceegi.guest';

            // Create weak auth user with REAL wallet
            $newUser = User::create([
                'name' => 'Wallet-' . substr($walletAddress, 0, 6),
                'email' => $uniqueEmail,
                'password' => Hash::make(Str::random(60)),
                'wallet' => $walletAddress,
                'is_weak_auth' => true,
                'email_verified_at' => null,
            ]);

            $this->logger->info('Real Wallet: Guest user created', [
                'user_id' => $newUser->id,
                'wallet_prefix' => substr($walletAddress, 0, 8) . '...'
            ]);

            // Assign guest role
            $guestRole = \Spatie\Permission\Models\Role::where('name', 'guest')->first();
            if ($guestRole) {
                $newUser->assignRole($guestRole);
            }

            // Create default collection
            $collection = $this->collectionService->createDefaultCollection($newUser);
            if ($collection instanceof JsonResponse) {
                $this->logger->error('Real Wallet: Collection creation failed', [
                    'user_id' => $newUser->id
                ]);
                return $collection;
            }

            // Establish session
            $this->establishConnectedSession($request, $newUser, $walletAddress);

            // Clear pending wallet from session
            $request->session()->forget('pending_wallet_address');

            return response()->json([
                'success' => true,
                'message' => trans('collection.wallet.real_wallet_guest_created'),
                'wallet_address' => $walletAddress,
                'user_status' => 'weak_auth',
                'user_name' => $newUser->name,
                'info' => trans('collection.wallet.real_wallet_upgrade_info')
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Real Wallet: Guest creation error', [
                'error' => $e->getMessage(),
                'trace' => substr($e->getTraceAsString(), 0, 500)
            ]);

            return $this->errorManager->handle('REAL_WALLET_CREATE_GUEST_FAILED', [
                'message' => $e->getMessage()
            ], $e);
        }
    }

    /**
     * @Oracode Prepare redirect to registration with wallet pre-filled
     * 📡 API: POST /wallet/real/prepare-register
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function prepareRegister(Request $request): JsonResponse {
        $walletAddress = $request->input('wallet_address')
            ?? $request->session()->get('pending_wallet_address');

        if (!$walletAddress) {
            return response()->json([
                'success' => false,
                'message' => trans('collection.wallet.real_wallet_missing_address')
            ], 400);
        }

        // Store in session for registration form
        $request->session()->put('prefill_wallet_address', strtoupper(trim($walletAddress)));

        return response()->json([
            'success' => true,
            'redirect' => route('register') . '?wallet_prefill=1'
        ]);
    }

    /**
     * @Oracode Verify wallet existence on Algorand blockchain
     *
     * @param string $walletAddress
     * @return bool
     */
    protected function verifyWalletOnChain(string $walletAddress): bool {
        try {
            $accountInfo = $this->algorandClient->getAccountInfo($walletAddress);

            // Account exists if we get valid response with amount field
            return isset($accountInfo['amount']) || isset($accountInfo['address']);
        } catch (\Exception $e) {
            $this->logger->warning('Real Wallet: On-chain verification failed', [
                'wallet_prefix' => substr($walletAddress, 0, 8) . '...',
                'error' => $e->getMessage()
            ]);

            // Return false but don't block - could be network issue
            return false;
        }
    }

    /**
     * @Oracode Establish connected session for FegiGuard
     *
     * @param Request $request
     * @param User $user
     * @param string $walletAddress
     */
    protected function establishConnectedSession(Request $request, User $user, string $walletAddress): void {
        // Ensure session is started
        if (!$request->session()->isStarted()) {
            $request->session()->start();
        }

        // Set session data compatible with FegiGuard
        $sessionData = [
            'auth_status' => 'connected',
            'connected_wallet' => $walletAddress,
            'connected_user_id' => $user->id,
            'is_weak_auth' => $user->is_weak_auth ?? true,
            'wallet_type' => 'real' // Mark as real wallet connection
        ];

        $request->session()->put($sessionData);
        $request->session()->save();
        $request->session()->regenerate();

        $this->logger->info('Real Wallet: Session established', [
            'user_id' => $user->id,
            'session_id' => $request->session()->getId()
        ]);
    }

    /**
     * @Oracode Get user authentication status
     *
     * @param User $user
     * @return string
     */
    protected function getUserStatus(User $user): string {
        if ($user->is_weak_auth) {
            return 'weak_auth';
        }
        return $user->email_verified_at ? 'verified' : 'registered';
    }
}
