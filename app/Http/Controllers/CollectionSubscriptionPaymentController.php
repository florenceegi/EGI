<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use App\Services\CollectionSubscriptionFiatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * @package App\Http\Controllers
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Collection Subscription FIAT)
 * @date 2026-03-03
 * @purpose Controller per pagamenti FIAT abbonamenti Collection Company.
 *          Gestisce: lista piani, avvio checkout, ritorno Stripe, webhook.
 *          MiCA-safe: nessun crypto-custody, solo FIAT EUR.
 *
 * Route attive (da aggiungere in web.php / api.php):
 *   GET  /api/collection-subscription-plans              → getActivePlans()
 *   POST /collections/{id}/fiat-subscription/initiate   → initiatePayment()
 *   GET  /collections/{id}/fiat-subscription/success    → paymentSuccess()
 *   GET  /collections/{id}/fiat-subscription/cancel     → paymentCancel()
 *   POST /api/webhooks/collection-subscription/{provider} → handleWebhook()
 */
class CollectionSubscriptionPaymentController extends Controller {
    public function __construct(
        private CollectionSubscriptionFiatService $subscriptionService,
        private UltraLogManager                   $logger,
        private ErrorManagerInterface             $errorManager,
    ) {
    }

    /*
    |--------------------------------------------------------------------------
    | API: Lista piani (pubblica per utenti autenticati)
    |--------------------------------------------------------------------------
    */

    /**
     * Restituisce i piani abbonamento attivi da ai_feature_pricing.
     *
     * GET /api/collection-subscription-plans
     */
    public function getActivePlans(): JsonResponse {
        try {
            $plans = $this->subscriptionService->getActivePlans();

            return response()->json([
                'success' => true,
                'plans'   => $plans->map(fn($plan) => [
                    'feature_code'       => $plan->feature_code,
                    'name'               => $plan->feature_name,
                    'description'        => $plan->feature_description ?? '',
                    'cost_fiat_eur'      => (float) $plan->cost_fiat_eur,
                    'is_recurring'       => (bool) $plan->is_recurring,
                    'recurrence_period'  => $plan->recurrence_period,
                    'max_egis'           => $plan->feature_parameters['max_egis'] ?? null,
                    'egili_discount_pct' => $plan->feature_parameters['egili_discount_percent'] ?? 0,
                    'benefits'           => $plan->benefits ?? [],
                ]),
            ]);
        } catch (\Throwable $e) {
            return $this->errorManager->handle(
                'SUBSCRIPTION_PLANS_FETCH_FAILED',
                [],
                $e
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Avvio pagamento
    |--------------------------------------------------------------------------
    */

    /**
     * Avvia il checkout Stripe per un piano abbonamento.
     *
     * POST /collections/{id}/fiat-subscription/initiate
     * Body: feature_code, provider (optional, default 'stripe'), apply_egili_discount (bool)
     */
    public function initiatePayment(Request $request, int $id): JsonResponse {
        $validated = $request->validate([
            'feature_code'          => ['required', 'string', 'max:100'],
            'provider'              => ['sometimes', 'string', 'in:stripe,paypal'],
            'apply_egili_discount'  => ['sometimes', 'boolean'],
        ]);

        /** @var \App\Models\User $user */
        $user = $request->user();

        $collection = Collection::find($id);
        if (! $collection) {
            return response()->json([
                'success' => false,
                'message' => __('subscription.collection_not_found'),
            ], 404);
        }

        // Verifica ownership: solo il creator può abbonarsi per la sua collection
        if ((int) $collection->creator_id !== (int) $user->id) {
            return response()->json([
                'success' => false,
                'message' => __('subscription.unauthorized'),
            ], 403);
        }

        $provider             = $validated['provider'] ?? 'stripe';
        $applyEgiliDiscount   = (bool) ($validated['apply_egili_discount'] ?? false);

        // URL di ritorno da Stripe Checkout
        $successUrl = route('home.collections.fiat-subscription.success', ['id' => $id]);
        $cancelUrl  = route('home.collections.fiat-subscription.cancel',  ['id' => $id]);

        try {
            $result = $this->subscriptionService->initiatePayment(
                user: $user,
                collection: $collection,
                featureCode: $validated['feature_code'],
                provider: $provider,
                applyEgiliDiscount: $applyEgiliDiscount,
                successUrl: $successUrl,
                cancelUrl: $cancelUrl,
            );

            return response()->json([
                'success'             => true,
                'checkout_url'        => $result['checkout_url'],
                'provider_session_id' => $result['provider_session_id'],
                'amount_eur'          => $result['amount_eur'],
                'subscription_id'     => $result['subscription_id'],
            ]);
        } catch (\Throwable $e) {
            return $this->errorManager->handle(
                'SUBSCRIPTION_INITIATE_CONTROLLER_ERROR',
                [
                    'user_id'       => $user->id,
                    'collection_id' => $id,
                    'feature_code'  => $validated['feature_code'],
                ],
                $e
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Rientro da Stripe Checkout
    |--------------------------------------------------------------------------
    */

    /**
     * Pagamento completato — Stripe reindirizza qui con ?session_id=...
     *
     * GET /collections/{id}/fiat-subscription/success?session_id=xxx
     */
    public function paymentSuccess(Request $request, int $id): RedirectResponse {
        $sessionId = $request->query('session_id', '');

        $this->logger->info('CollectionSubscriptionPayment: success redirect', [
            'collection_id' => $id,
            'session_id'    => $sessionId,
        ]);

        if ($sessionId) {
            try {
                $this->subscriptionService->confirmPayment($sessionId);
            } catch (\Throwable $e) {
                // Loggato dal service — il webhook arriverà comunque
                $this->logger->warning('CollectionSubscriptionPayment: confirmPayment via redirect failed (webhook fallback)', [
                    'session_id' => $sessionId,
                    'error'      => $e->getMessage(),
                ]);
            }
        }

        return redirect()
            ->route('home.collections.show', $id)
            ->with('success', __('subscription.payment_success'));
    }

    /**
     * Pagamento annullato — Stripe reindirizza qui.
     *
     * GET /collections/{id}/fiat-subscription/cancel
     */
    public function paymentCancel(Request $request, int $id): RedirectResponse {
        $this->logger->info('CollectionSubscriptionPayment: payment cancelled', [
            'collection_id' => $id,
            'user_id'       => $request->user()?->id,
        ]);

        return redirect()
            ->route('home.collections.show', $id)
            ->with('warning', __('subscription.payment_cancelled'));
    }

    /*
    |--------------------------------------------------------------------------
    | Webhook provider (Stripe / PayPal)
    |--------------------------------------------------------------------------
    */

    /**
     * Gestisce i webhook in arrivo dal provider di pagamento.
     * Stripe invia eventi: checkout.session.completed, payment_intent.payment_failed, etc.
     *
     * POST /api/webhooks/collection-subscription/{provider}
     * Header: Stripe-Signature
     */
    public function handleWebhook(Request $request, string $provider): JsonResponse {
        $this->logger->info("CollectionSubscriptionPayment: webhook received [{$provider}]");

        try {
            $paymentService = app(\App\Services\Payment\PaymentServiceFactory::class)
                ->create($provider);

            $payload = $request->all();
            $headers = $request->headers->all();

            $result = $paymentService->processPaymentWebhook($payload, $headers);

            $event   = $result['event'] ?? '';
            $session = $result['session_id'] ?? $result['metadata']['checkout_session_id'] ?? '';

            if ($event === 'checkout.session.completed' && $session) {
                $this->subscriptionService->confirmPayment($session);
            }

            return response()->json(['received' => true]);
        } catch (\Throwable $e) {
            return $this->errorManager->handle(
                'SUBSCRIPTION_WEBHOOK_FAILED',
                ['provider' => $provider],
                $e
            );
        }
    }
}
