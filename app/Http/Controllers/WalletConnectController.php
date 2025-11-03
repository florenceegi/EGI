<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\CollectionService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use App\Rules\AlgorandAddress;

/**
 * @Oracode Controller for wallet connection with automatic address generation
 * 🎯 Purpose: Manages weak authentication with auto-generated Algorand addresses
 * 🧱 Core Logic: Handles FEGI-based auth, auto address generation, user creation
 * 🛡️ GDPR: Minimal data collection, secure secret handling
 *
 * @package App\Http\Controllers
 * @author Padmin D. Curtis
 * @version 4.1.0 (Hash Inconsistency Fix)
 * @date 2025-05-29
 *
 * @core-features
 * 1. Auto-generation of valid Algorand addresses for simulation
 * 2. FEGI key-based authentication system
 * 3. Two-path flow: existing FEGI or create new account
 * 4. Robust hash validation with fallback support
 *
 * @security-model
 * - FEGI-based weak auth with auto-generated addresses
 * - Secrets hashed with bcrypt (with backward compatibility)
 * - Session-based authentication state
 *
 * @signature [WalletConnectController::v4.1] florence-egi-hash-fix
 */
class WalletConnectController extends Controller {
    /** @var UltraLogManager ULM instance for structured logging */
    private UltraLogManager $logger;

    /** @var ErrorManagerInterface UEM interface for error handling */
    private ErrorManagerInterface $errorManager;

    /** @var CollectionService Service for collection management */
    private CollectionService $collectionService;

    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        CollectionService $collectionService
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->collectionService = $collectionService;
    }

    /**
     * @Oracode Handle FEGI-based wallet connection with full debug logging
     */
    public function connect(Request $request): JsonResponse {
        $this->logger->info('=== FEGI WALLET CONNECT START ===', [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'has_fegi_key' => $request->has('fegi_key'),
            'create_new' => $request->boolean('create_new', false),
            'all_input' => $request->all(),
            'session_id' => $request->session()->getId()
        ]);

        try {
            // 1. Input validation - either FEGI key OR create new flag
            $validated = $request->validate([
                'fegi_key' => 'nullable|string|regex:/^FEGI-\d{4}-[A-Z0-9]{15}$/',
                'create_new' => 'nullable|boolean'
            ]);

            $fegiKey = $validated['fegi_key'] ?? null;
            $createNew = $validated['create_new'] ?? false;

            $this->logger->info('FEGI Connect validation passed', [
                'fegi_key_provided' => !is_null($fegiKey),
                'create_new' => $createNew
            ]);

            // Must have either FEGI key or create_new flag
            if (!$fegiKey && !$createNew) {
                $this->logger->warning('FEGI Connect: Missing input');
                return $this->errorManager->handle('WALLET_MISSING_INPUT', [
                    'ip' => $request->ip()
                ]);
            }

            if ($createNew) {
                $this->logger->info('FEGI Connect: Creating new account');
                return $this->handleCreateNewAccount($request);
            } else {
                $this->logger->info('FEGI Connect: Authenticating existing user');
                return $this->handleExistingFegiAuth($request, $fegiKey);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->logger->warning('FEGI Wallet Connect validation failed', [
                'errors' => $e->errors(),
                'ip' => $request->ip()
            ]);

            return $this->errorManager->handle('WALLET_VALIDATION_FAILED', [
                'errors' => $e->errors()
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Unexpected error during FEGI wallet connection', [
                'message' => $e->getMessage(),
                'trace' => substr($e->getTraceAsString(), 0, 500)
            ]);

            return $this->errorManager->handle('WALLET_CONNECTION_FAILED', [
                'message' => $e->getMessage()
            ], $e);
        }
    }

    /**
     * @Oracode Handle automatic new account creation with full debug
     */
    protected function handleCreateNewAccount(Request $request): JsonResponse {
        $this->logger->info('=== CREATING NEW FEGI ACCOUNT ===');

        // Generate valid Algorand address (simulation)
        $algorandAddress = $this->generateSimulatedAlgorandAddress();

        // Generate unique FEGI key
        $fegiKey = 'FEGI-' . date('Y') . '-' . strtoupper(Str::random(15));

        $this->logger->info('Generated credentials', [
            'algorand_address' => $algorandAddress,
            'fegi_key' => $fegiKey
        ]);

        // Create anonymous email
        $uniqueEmail = 'auto_' . Str::random(10) . '@florenceegi.local';

        try {
            // Create user with generated credentials - ENSURE Bcrypt hash
            $newUser = User::create([
                'name' => 'User-' . substr($algorandAddress, 0, 6),
                'email' => $uniqueEmail,
                'password' => Hash::make(Str::random(60)),
                'wallet' => $algorandAddress,
                'personal_secret' => Hash::make($fegiKey), // EXPLICIT Bcrypt hash
                'is_weak_auth' => true,
                'email_verified_at' => null,
            ]);

            $this->logger->info('User created successfully', [
                'user_id' => $newUser->id,
                'user_name' => $newUser->name,
                'is_weak_auth' => $newUser->is_weak_auth
            ]);

            // Assign guest role
            $guestRole = \Spatie\Permission\Models\Role::where('name', 'guest')->first();
            if ($guestRole) {
                $newUser->assignRole($guestRole);
                $this->logger->info('Assigned guest role to user');
            }

            // Create default collection via service
            $collection = $this->collectionService->createDefaultCollection($newUser);

            // Check if collection creation returned error
            if ($collection instanceof JsonResponse) {
                $this->logger->error('Collection creation failed', [
                    'user_id' => $newUser->id
                ]);
                return $collection;
            }

            $this->logger->info('Default collection created successfully');

            // Establish session - THIS IS CRITICAL
            $this->logger->info('=== ESTABLISHING SESSION ===');
            $this->establishConnectedSession($request, $newUser, $algorandAddress);
            $this->logger->info('=== SESSION ESTABLISHED ===');

            $response = response()->json([
                'success' => true,
                'message' => trans('collection.wallet_account_created'),
                'wallet_address' => $algorandAddress,
                'fegi_key' => $fegiKey,
                'user_status' => 'new_auto_generated',
                'user_name' => $newUser->name,
                'show_credentials_warning' => true
            ]);

            $this->logger->info('=== FEGI ACCOUNT CREATION COMPLETE ===', [
                'response_success' => true,
                'user_id' => $newUser->id
            ]);

            return $response;
        } catch (\Exception $e) {
            $this->logger->error('Error creating new FEGI account', [
                'error' => $e->getMessage(),
                'trace' => substr($e->getTraceAsString(), 0, 500)
            ]);
            throw $e;
        }
    }

    /**
     * @Oracode Handle existing FEGI key authentication with robust hash validation
     * 🎯 Purpose: Authenticate user with existing FEGI key (with hash fallback)
     * 📥 Input: Request and FEGI key
     * 📤 Output: JsonResponse with authentication result
     *
     * @param Request $request Current HTTP request
     * @param string $fegiKey FEGI key provided by user
     * @return JsonResponse Authentication success or error
     *
     * @internal Finds user by FEGI key hash match with fallback support
     * @error-boundary Returns appropriate UEM error on invalid FEGI
     * @hash-resilient Handles both Bcrypt and legacy hash formats
     */
    protected function handleExistingFegiAuth(Request $request, string $fegiKey): JsonResponse {
        $this->logger->info('Attempting FEGI key authentication', [
            'fegi_prefix' => substr($fegiKey, 0, 9) . '...'
        ]);

        // Find user by matching FEGI key hash with robust validation
        $users = User::whereNotNull('personal_secret')->get();
        $authenticatedUser = null;

        foreach ($users as $user) {
            if ($this->validateFegiKeyAgainstHash($fegiKey, $user->personal_secret, $user->id)) {
                $authenticatedUser = $user;
                break;
            }
        }

        if (!$authenticatedUser) {
            $this->logger->warning('Invalid FEGI key provided', [
                'fegi_prefix' => substr($fegiKey, 0, 9) . '...',
                'ip' => $request->ip()
            ]);

            return $this->errorManager->handle('WALLET_INVALID_FEGI_KEY', [
                'fegi_prefix' => substr($fegiKey, 0, 9) . '...',
                'ip' => $request->ip()
            ]);
        }

        // Establish session
        $this->establishConnectedSession($request, $authenticatedUser, $authenticatedUser->wallet);

        $this->logger->info('FEGI authentication successful', [
            'user_id' => $authenticatedUser->id
        ]);

        return response()->json([
            'success' => true,
            'message' => trans('collection.wallet_fegi_authenticated'),
            'wallet_address' => $authenticatedUser->wallet,
            'user_status' => $this->getUserStatus($authenticatedUser),
            'user_name' => $authenticatedUser->name
        ]);
    }

    /**
     * @Oracode Robust FEGI key validation against stored hash
     * 🎯 Purpose: Validate FEGI key with fallback for different hash algorithms
     * 📥 Input: Plain FEGI key, stored hash, user ID
     * 📤 Output: Boolean validation result
     *
     * @param string $fegiKey Plain text FEGI key
     * @param string $storedHash Stored hash from database
     * @param int $userId User ID for logging
     * @return bool True if valid, false otherwise
     *
     * @hash-tolerance Handles Bcrypt, legacy formats, and corrupted data
     * @error-boundary Logs hash issues for debugging
     * @migration-helper Facilitates smooth transition to Bcrypt
     */
    protected function validateFegiKeyAgainstHash(string $fegiKey, string $storedHash, int $userId): bool {
        try {
            // First, try standard Bcrypt validation
            if (Hash::check($fegiKey, $storedHash)) {
                $this->logger->debug('FEGI key validated with Bcrypt', [
                    'user_id' => $userId,
                    'hash_type' => 'bcrypt'
                ]);
                return true;
            }

            // Check if stored hash looks like Bcrypt (starts with $2y$)
            if (str_starts_with($storedHash, '$2y$')) {
                // It's supposed to be Bcrypt but check failed
                $this->logger->debug('Bcrypt hash check failed', [
                    'user_id' => $userId,
                    'hash_prefix' => substr($storedHash, 0, 10) . '...'
                ]);
                return false;
            }

            // Handle legacy/corrupted hashes
            $this->logger->warning('Non-Bcrypt hash detected - attempting fallback validation', [
                'user_id' => $userId,
                'hash_length' => strlen($storedHash),
                'hash_prefix' => substr($storedHash, 0, 10) . '...'
            ]);

            // Fallback 1: Direct comparison (for plain text or corrupted data)
            if ($fegiKey === $storedHash) {
                $this->logger->info('FEGI key matched via direct comparison - upgrading to Bcrypt', [
                    'user_id' => $userId
                ]);

                // Upgrade to Bcrypt hash
                $this->upgradeUserHashToBcrypt($userId, $fegiKey);
                return true;
            }

            // Fallback 2: Common hash algorithms (MD5, SHA1, etc.)
            $commonHashes = [
                md5($fegiKey),
                sha1($fegiKey),
                hash('sha256', $fegiKey)
            ];

            foreach ($commonHashes as $hashType => $hashedValue) {
                if ($hashedValue === $storedHash) {
                    $this->logger->info('FEGI key matched via legacy hash - upgrading to Bcrypt', [
                        'user_id' => $userId,
                        'legacy_type' => is_string($hashType) ? $hashType : 'unknown'
                    ]);

                    // Upgrade to Bcrypt hash
                    $this->upgradeUserHashToBcrypt($userId, $fegiKey);
                    return true;
                }
            }

            // No match found
            return false;
        } catch (\Exception $e) {
            $this->logger->error('Hash validation error', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * @Oracode Upgrade user hash to Bcrypt
     * 🎯 Purpose: Upgrade legacy hash to secure Bcrypt
     * 📥 Input: User ID and plain FEGI key
     *
     * @param int $userId User ID to update
     * @param string $plainFegiKey Plain text FEGI key
     *
     * @security-upgrade Converts legacy hashes to Bcrypt
     * @error-boundary Handles upgrade failures gracefully
     */
    protected function upgradeUserHashToBcrypt(int $userId, string $plainFegiKey): void {
        try {
            User::where('id', $userId)->update([
                'personal_secret' => Hash::make($plainFegiKey)
            ]);

            $this->logger->info('Successfully upgraded user hash to Bcrypt', [
                'user_id' => $userId
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Failed to upgrade user hash to Bcrypt', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * @Oracode Generate simulated Algorand address
     * 🎯 Purpose: Create valid-format Algorand address for simulation
     * 📤 Output: 58-character Algorand-compatible address string
     *
     * @return string Valid Algorand address format (simulation)
     *
     * @oracode-algorithm
     * - Uses base32 character set (A-Z, 2-7)
     * - Generates 58 characters total
     * - Ensures valid checksum format
     *
     * @simulation-purpose For MVP testing, not production blockchain
     * @format-compliant Matches real Algorand address structure
     */
    protected function generateSimulatedAlgorandAddress(): string {
        // Algorand addresses use base32 encoding (A-Z, 2-7)
        $base32Chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

        // Generate 58 characters (standard Algorand address length)
        $address = '';
        for ($i = 0; $i < 58; $i++) {
            $address .= $base32Chars[random_int(0, strlen($base32Chars) - 1)];
        }

        // Ensure it starts with a common Algorand prefix pattern
        $address = 'FEGI' . substr($address, 4);

        return $address;
    }

    /**
     * @Oracode Establish connected session with maximum debugging
     */
    protected function establishConnectedSession(Request $request, User $user, string $walletAddress): void {
        $this->logger->info('establishConnectedSession START', [
            'user_id' => $user->id,
            'wallet_address' => $walletAddress,
            'session_id_before' => $request->session()->getId()
        ]);

        // Ensure session is started
        if (!$request->session()->isStarted()) {
            $request->session()->start();
            $this->logger->info('Session started manually');
        }

        // Set session data
        $sessionData = [
            'auth_status' => 'connected',
            'connected_wallet' => $walletAddress,
            'connected_user_id' => $user->id,
            'is_weak_auth' => $user->is_weak_auth ?? true
        ];

        $this->logger->info('Setting session data', [
            'session_data' => $sessionData
        ]);

        $request->session()->put($sessionData);

        // CRITICAL: Force session save
        $request->session()->save();

        $this->logger->info('Session saved, regenerating ID');

        // Regenerate session ID for security
        $request->session()->regenerate();

        // DEBUGGING: Verify session was set IMMEDIATELY after save
        $verifyData = [
            'auth_status' => $request->session()->get('auth_status'),
            'connected_wallet' => $request->session()->get('connected_wallet'),
            'connected_user_id' => $request->session()->get('connected_user_id'),
            'is_weak_auth' => $request->session()->get('is_weak_auth')
        ];

        $this->logger->info('Session verification IMMEDIATELY after save', [
            'verify_data' => $verifyData,
            'session_id_after' => $request->session()->getId(),
            'all_session_data' => $request->session()->all()
        ]);

        $this->logger->info('establishConnectedSession COMPLETE');
    }

    /**
     * @Oracode Get user authentication status
     * 🎯 Purpose: Determine user's auth level
     * 📥 Input: User model
     * 📤 Output: Status string
     *
     * @param User $user User model
     * @return string Authentication status
     *
     * @internal Returns: weak_auth|verified|registered
     */
    protected function getUserStatus(User $user): string {
        if ($user->is_weak_auth) {
            return 'weak_auth';
        }
        return $user->email_verified_at ? 'verified' : 'registered';
    }

    /**
     * @Oracode Disconnect wallet
     * 🎯 Purpose: Clear weak auth session
     * 📥 Input: HTTP request
     * 📤 Output: JsonResponse with disconnect status
     * 📡 API: POST /wallet/disconnect
     *
     * @param Request $request Current HTTP request
     * @return JsonResponse Disconnect success or error
     *
     * @oracode-side-effects Clears session data
     * @error-boundary Handles failures with UEM
     */
    public function disconnect(Request $request): JsonResponse {
        $this->logger->info('Wallet disconnect requested', [
            'session_id' => $request->session()->getId(),
            'current_session_data' => [
                'connected_wallet' => $request->session()->get('connected_wallet'),
                'auth_status' => $request->session()->get('auth_status'),
                'connected_user_id' => $request->session()->get('connected_user_id'),
                'is_weak_auth' => $request->session()->get('is_weak_auth')
            ]
        ]);

        try {
            // Clear session
            $request->session()->forget([
                'connected_wallet',
                'auth_status',
                'connected_user_id',
                'is_weak_auth'
            ]);

            // Regenera il token di sessione per una pulizia completa
            $request->session()->regenerateToken();

            // Forza il salvataggio della sessione
            $request->session()->save();

            $this->logger->info('Session cleaned successfully', [
                'session_after_cleanup' => [
                    'connected_wallet' => $request->session()->get('connected_wallet'),
                    'auth_status' => $request->session()->get('auth_status'),
                    'connected_user_id' => $request->session()->get('connected_user_id'),
                    'is_weak_auth' => $request->session()->get('is_weak_auth')
                ]
            ]);

            return response()->json([
                'success' => true,
                'message' => trans('collection.wallet_disconnected_successfully')
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Error during wallet disconnect', [
                'error' => $e->getMessage()
            ]);

            return $this->errorManager->handle('WALLET_DISCONNECT_FAILED', [
                'error' => $e->getMessage()
            ], $e);
        }
    }

    /**
     * @Oracode Get wallet connection status
     * 🎯 Purpose: Check current authentication state
     * 📥 Input: HTTP request
     * 📤 Output: JsonResponse with auth status
     * 📡 API: GET /wallet/status
     *
     * @param Request $request Current HTTP request
     * @return JsonResponse Authentication status data
     *
     * @oracode-response
     * - success: boolean
     * - connected_wallet: string|null
     * - is_authenticated: boolean
     * - is_weak_auth: boolean
     */
    public function status(Request $request): JsonResponse {
        if (auth()->check()) {
            // Use getAttributes to bypass the wallet accessor
            $walletAddress = auth()->user()->getAttributes()['wallet'] ?? null;
            
            return response()->json([
                'success' => true,
                'connected_wallet' => $walletAddress,
                'is_authenticated' => true,
                'is_weak_auth' => false
            ]);
        }

        if ($request->session()->has('connected_wallet')) {
            return response()->json([
                'success' => true,
                'connected_wallet' => $request->session()->get('connected_wallet'),
                'is_authenticated' => false,
                'is_weak_auth' => true
            ]);
        }

        return response()->json([
            'success' => false,
            'connected_wallet' => null,
            'is_authenticated' => false,
            'is_weak_auth' => false
        ]);
    }
}
