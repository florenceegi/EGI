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

            // Stripe Connect setup for users that need payment processing (creator, epp, company)
            $userTypesNeedingStripe = ['creator', 'epp', 'company'];

            if (in_array($user->usertype ?? null, $userTypesNeedingStripe) && $this->stripeConnectService !== null) {
                try {
                    $stripeAccountData = $this->stripeConnectService->ensureExpressAccount($wallet, $user);
                    $accountArray = $stripeAccountData['account'] ?? [];
                    $accountId = $accountArray['id'] ?? null;

                    if ($accountId) {
                        $needsOnboarding = !($accountArray['details_submitted'] ?? false)
                            || !($accountArray['charges_enabled'] ?? false);

                        // Determine redirect route based on user type
                        $onboardingRedirectRoute = match ($user->usertype) {
                            'creator' => route('creator.onboarding.summary'),
                            'company' => route('company.dashboard'),
                            default => route('dashboard'),
                        };

                        if ($needsOnboarding) {
                            $onboardingUrl = $this->stripeConnectService->createAccountLink(
                                $accountId,
                                $onboardingRedirectRoute,
                                $onboardingRedirectRoute
                            );
                        }

                        $dashboardUrl = $this->stripeConnectService->createExpressDashboardLoginLink($accountId);
                    }

                    // Set redirect URL based on user type (use onboarding URL if available)
                    $redirectUrl = $onboardingUrl ?? match ($user->usertype) {
                        'creator' => route('creator.onboarding.summary'),
                        'company' => route('company.dashboard'),
                        default => route('dashboard'),
                    };
                } catch (\Exception $e) {
                    $this->logger->error('Stripe Connect account creation failed during IBAN setup', [
                        'user_id' => $user->id,
                        'user_type' => $user->usertype,
                        'wallet_id' => $wallet->id,
                        'error' => $e->getMessage(),
                    ]);

                    // Continue without Stripe - user can set it up later
                    $this->errorManager->handle('STRIPE_CONNECT_ACCOUNT_FAILED', [
                        'user_id' => $user->id,
                        'user_type' => $user->usertype,
                        'wallet_id' => $wallet->id,
                        'error' => $e->getMessage(),
                    ], $e);
                }
            }

            // Save preference if requested
            if ($request->input('dont_show_again', false)) {
                $privacySettings = $user->privacy_settings ?? [];
                $privacySettings['hide_wallet_welcome'] = true;
                $user->update(['privacy_settings' => $privacySettings]);
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
     * When user clicks "Skip", we always save the preference - they made a conscious decision
     * For creator/epp/company users, still trigger Stripe Connect onboarding
     */
    public function skipIban(Request $request): JsonResponse {
        try {
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'error' => __('register.unauthenticated')
                ], 401);
            }

            $user = Auth::user();
            $wallet = $user->primaryWallet;

            // Always save preference - user made a conscious decision to skip IBAN
            // They can always add it later from the "Conto PSP" menu
            $privacySettings = $user->privacy_settings ?? [];
            $privacySettings['hide_wallet_welcome'] = true;
            $user->update(['privacy_settings' => $privacySettings]);

            // Clear session flag
            session()->forget('show_wallet_welcome');

            $this->logger->info('User skipped IBAN setup in welcome modal', [
                'user_id' => $user->id,
                'user_type' => $user->usertype,
            ]);

            // Stripe Connect setup for users that need payment processing
            $userTypesNeedingStripe = ['creator', 'epp', 'company'];
            $onboardingUrl = null;
            $redirectUrl = route($this->resolvePostIbanRedirectRoute($user));

            if (
                in_array($user->usertype ?? null, $userTypesNeedingStripe)
                && $this->stripeConnectService !== null
                && $wallet
            ) {
                try {
                    $stripeAccountData = $this->stripeConnectService->ensureExpressAccount($wallet, $user);
                    $accountArray = $stripeAccountData['account'] ?? [];
                    $accountId = $accountArray['id'] ?? null;

                    if ($accountId) {
                        $needsOnboarding = !($accountArray['details_submitted'] ?? false)
                            || !($accountArray['charges_enabled'] ?? false);

                        // Determine redirect route based on user type
                        $onboardingRedirectRoute = match ($user->usertype) {
                            'creator' => route('creator.onboarding.summary'),
                            'company' => route('company.dashboard'),
                            default => route('dashboard'),
                        };

                        if ($needsOnboarding) {
                            $onboardingUrl = $this->stripeConnectService->createAccountLink(
                                $accountId,
                                $onboardingRedirectRoute,
                                $onboardingRedirectRoute
                            );
                            // Use Stripe onboarding URL as redirect
                            $redirectUrl = $onboardingUrl;
                        } else {
                            $redirectUrl = $onboardingRedirectRoute;
                        }
                    }

                    $this->logger->info('Stripe Connect setup during skip IBAN', [
                        'user_id' => $user->id,
                        'user_type' => $user->usertype,
                        'has_onboarding_url' => !empty($onboardingUrl),
                    ]);
                } catch (\Exception $e) {
                    $this->logger->error('Stripe Connect account creation failed during skip IBAN', [
                        'user_id' => $user->id,
                        'user_type' => $user->usertype,
                        'error' => $e->getMessage(),
                    ]);
                    // Continue without Stripe - user can set it up later
                }
            }

            return response()->json([
                'success' => true,
                'message' => __('register.wallet_welcome_completed'),
                'redirect_url' => $redirectUrl,
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
