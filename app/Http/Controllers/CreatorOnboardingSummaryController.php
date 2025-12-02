<?php

namespace App\Http\Controllers;

use App\Services\Payment\StripeConnectService;
use Illuminate\Support\Facades\Auth;
use Ultra\UltraLogManager\UltraLogManager;
use App\Enums\User\MerchantUserTypeEnum;

class CreatorOnboardingSummaryController extends Controller {
    public function __construct(
        UltraLogManager $logger
    ) {
        $this->middleware('auth');
        $this->logger = $logger;

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

    private ?StripeConnectService $stripeConnectService;
    private UltraLogManager $logger;

    public function show() {
        $user = Auth::user();

        // Merchant user types (sellers) can access Stripe onboarding
        if (!MerchantUserTypeEnum::isMerchant($user->usertype ?? null)) {
            abort(403);
        }

        $wallet = $user->wallets()
            ->whereNotNull('secret_ciphertext')
            ->first();

        if (!$wallet) {
            abort(404, 'Wallet not found');
        }

        $stripeAccount = null;
        $onboardingUrl = null;
        $dashboardUrl = null;

        if ($this->stripeConnectService !== null) {
            if ($wallet->stripe_account_id) {
                try {
                    $stripeAccount = $this->stripeConnectService->retrieveAccount($wallet->stripe_account_id);

                    if (!$stripeAccount) {
                        $accountData = $this->stripeConnectService->ensureExpressAccount($wallet, $user);
                        $stripeAccount = $accountData['account'] ?? null;
                        $wallet->refresh();
                    }
                } catch (\Exception $e) {
                    $this->logger->error('Failed to retrieve Stripe account', [
                        'user_id' => $user->id,
                        'wallet_id' => $wallet->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            } else {
                try {
                    $accountData = $this->stripeConnectService->ensureExpressAccount($wallet, $user);
                    $stripeAccount = $accountData['account'] ?? null;
                    $wallet->refresh();
                } catch (\Exception $e) {
                    $this->logger->error('Failed to create Stripe account', [
                        'user_id' => $user->id,
                        'wallet_id' => $wallet->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            if ($stripeAccount && $wallet->stripe_account_id) {
                $requiresOnboarding = !($stripeAccount['details_submitted'] ?? false)
                    || !($stripeAccount['charges_enabled'] ?? false);

                if ($requiresOnboarding) {
                    try {
                        $onboardingUrl = $this->stripeConnectService->createAccountLink(
                            $wallet->stripe_account_id,
                            route('creator.onboarding.summary'),
                            route('creator.onboarding.summary')
                        );
                    } catch (\Exception $e) {
                        $this->logger->error('Failed to create Stripe onboarding link', [
                            'user_id' => $user->id,
                            'wallet_id' => $wallet->id,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }

                try {
                    $dashboardUrl = $this->stripeConnectService->createExpressDashboardLoginLink(
                        $wallet->stripe_account_id
                    );
                } catch (\Exception $e) {
                    $this->logger->error('Failed to create Stripe dashboard link', [
                        'user_id' => $user->id,
                        'wallet_id' => $wallet->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        $this->logger->info('Creator onboarding summary viewed', [
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'stripe_account_id' => $wallet->stripe_account_id,
        ]);

        return view('creator.onboarding-summary', [
            'user' => $user,
            'wallet' => $wallet,
            'stripeAccount' => $stripeAccount,
            'onboardingUrl' => $onboardingUrl,
            'dashboardUrl' => $dashboardUrl,
        ]);
    }
}
