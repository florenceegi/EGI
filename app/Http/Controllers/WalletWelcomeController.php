<?php

namespace App\Http\Controllers;

use App\Exceptions\Wallet\DuplicateIbanException;
use App\Models\Wallet;
use App\Rules\ValidIban;
use App\Services\Auth\AuthRedirectService;
use App\Services\Payment\StripeConnectService;
use App\Services\Wallet\WalletProvisioningService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Ultra\UltraLogManager\UltraLogManager;

class WalletWelcomeController extends Controller {
    protected WalletProvisioningService $walletService;
    protected ErrorManagerInterface $errorManager;
    protected UltraLogManager $logger;
    protected ?StripeConnectService $stripeConnectService;
    protected AuthRedirectService $authRedirectService;

    public function __construct(
        WalletProvisioningService $walletService,
        ErrorManagerInterface $errorManager,
        UltraLogManager $logger,
        AuthRedirectService $authRedirectService
    ) {
        $this->walletService = $walletService;
        $this->errorManager = $errorManager;
        $this->logger = $logger;
        $this->authRedirectService = $authRedirectService;

        // StripeConnectService is optional - only inject if Stripe is configured
        try {
            $this->stripeConnectService = app(StripeConnectService::class);
        } catch (\Exception $e) {
            $this->logger->warning('StripeConnectService not available - Stripe may not be configured', [
                'error' => $e->getMessage(),
            ]);
            $this->stripeConnectService = null;
        }
    }

    /**
     * Get wallet welcome data for authenticated user
     */
    public function getData(): JsonResponse {
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

            // Check both session and flash data for show_wallet_welcome flag
            $shouldShow = session()->has('show_wallet_welcome') ||
                session()->get('show_wallet_welcome') === true ||
                old('show_wallet_welcome') === true;

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
                    'should_show' => $shouldShow,
                    'debug' => [
                        'session_has' => session()->has('show_wallet_welcome'),
                        'session_get' => session()->get('show_wallet_welcome'),
                        'all_session' => session()->all(),
                    ]
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
    public function addIban(Request $request): JsonResponse {
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
            $wallet->refresh();

            $accountArray = [];
            $onboardingUrl = null;
            $dashboardUrl = null;
            $redirectUrl = route($this->resolvePostIbanRedirectRoute($user));

            if (($user->usertype ?? null) === 'creator' && $this->stripeConnectService !== null) {
                try {
                    $stripeAccountData = $this->stripeConnectService->ensureExpressAccount($wallet, $user);
                    $accountArray = $stripeAccountData['account'] ?? [];
                    $accountId = $accountArray['id'] ?? null;

                    if ($accountId) {
                        $needsOnboarding = !($accountArray['details_submitted'] ?? false)
                            || !($accountArray['charges_enabled'] ?? false);

                        if ($needsOnboarding) {
                            $onboardingUrl = $this->stripeConnectService->createAccountLink(
                                $accountId,
                                route('creator.onboarding.summary'),
                                route('creator.onboarding.summary')
                            );
                        }

                        $dashboardUrl = $this->stripeConnectService->createExpressDashboardLoginLink($accountId);
                    }

                    $redirectUrl = route('creator.onboarding.summary');
                } catch (\Exception $e) {
                    $this->logger->error('Stripe Connect account creation failed during IBAN setup', [
                        'user_id' => $user->id,
                        'wallet_id' => $wallet->id,
                        'error' => $e->getMessage(),
                    ]);

                    // Continue without Stripe - user can set it up later
                    $this->errorManager->handle('STRIPE_CONNECT_ACCOUNT_FAILED', [
                        'user_id' => $user->id,
                        'wallet_id' => $wallet->id,
                        'error' => $e->getMessage(),
                    ], $e);
                }
            }

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
                'redirect_url' => $redirectUrl,
                'data' => [
                    'masked_iban' => $wallet->getMaskedIbanAttribute(),
                    'stripe_account' => [
                        'id' => $accountArray['id'] ?? null,
                        'charges_enabled' => $accountArray['charges_enabled'] ?? false,
                        'payouts_enabled' => $accountArray['payouts_enabled'] ?? false,
                        'details_submitted' => $accountArray['details_submitted'] ?? false,
                    ],
                    'onboarding_url' => $onboardingUrl,
                    'dashboard_url' => $dashboardUrl,
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'error' => __('register.invalid_iban'),
                'errors' => $e->errors()
            ], 422);
        } catch (DuplicateIbanException $e) {
            $this->logger->warning('Duplicate IBAN detected via welcome modal', [
                'user_id' => Auth::id(),
                'wallet_id' => $wallet->id,
                'iban_last4' => $e->ibanLast4,
            ]);

            return response()->json([
                'success' => false,
                'error' => __('register.wallet_iban_duplicate'),
                'errors' => [
                    'iban' => [__('register.wallet_iban_duplicate')],
                ],
            ], 409);
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
    public function skipIban(Request $request): JsonResponse {
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

    /**
     * Determine redirect route after IBAN flow for non-creator users.
     */
    protected function resolvePostIbanRedirectRoute($user): string {
        return $this->authRedirectService->getRedirectRoute($user);
    }
}