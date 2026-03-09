<?php

namespace App\Services;

use App\DataTransferObjects\Payment\PaymentRequest;
use App\Enums\Gdpr\GdprActivityCategory;
use App\Models\AiFeaturePricing;
use App\Models\Collection;
use App\Models\CollectionSubscription;
use App\Models\User;
use App\Services\Gdpr\AuditLogService;
use App\Services\Payment\PaymentServiceFactory;
use Illuminate\Support\Facades\DB;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * @package App\Services
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Collection Subscription FIAT)
 * @date 2026-03-03
 * @purpose Gestisce gli abbonamenti FIAT per Collection Company (Profilo NORMAL).
 *          MiCA-safe: pagamento esclusivo in EUR via Stripe/PayPal.
 *          Egili = sconto opzionale (reward points), MAI mezzo di pagamento.
 *
 * Questo service NON modifica CollectionSubscriptionService.
 * Le sue responsabilità sono disgiunte e complementari.
 *
 * Flusso:
 *   1. getActivePlans()       → mostra i piani disponibili
 *   2. calculateDiscount()    → calcola sconto Egili opzionale
 *   3. initiatePayment()      → crea row 'pending' + Stripe Checkout URL
 *   4. confirmPayment()       → attiva la subscription dopo webhook OK
 *   5. hasActiveSubscription()→ verifica abbonamento attivo
 */
class CollectionSubscriptionFiatService {
    /**
     * Categoria filata da ai_feature_pricing per i piani abbonamento.
     */
    private const FEATURE_CATEGORY = 'platform_services';

    /**
     * Prefisso feature_code usato dai piani abbonamento collection.
     */
    private const FEATURE_CODE_PREFIX = 'collection_subscription_';

    /**
     * Durata standard di un abbonamento mensile (in giorni).
     */
    private const SUBSCRIPTION_DAYS = 30;

    public function __construct(
        private UltraLogManager        $logger,
        private ErrorManagerInterface  $errorManager,
        private AuditLogService        $auditService,
        private EgiliService           $egiliService,
        private PaymentServiceFactory  $paymentFactory,
    ) {
    }

    /*
    |--------------------------------------------------------------------------
    | Lettura piani
    |--------------------------------------------------------------------------
    */

    /**
     * Restituisce tutti i piani abbonamento attivi, ordinati per costo EUR crescente.
     *
     * @return \Illuminate\Database\Eloquent\Collection<AiFeaturePricing>
     */
    public function getActivePlans(): \Illuminate\Database\Eloquent\Collection {
        return AiFeaturePricing::query()
            ->where('is_active', true)
            ->where('feature_category', self::FEATURE_CATEGORY)
            ->where('feature_code', 'like', self::FEATURE_CODE_PREFIX . '%')
            ->orderBy('cost_fiat_eur', 'asc')
            ->get();
    }

    /**
     * Restituisce un piano specifico dato il feature_code.
     */
    public function getPlanByCode(string $featureCode): ?AiFeaturePricing {
        return AiFeaturePricing::query()
            ->where('feature_code', $featureCode)
            ->where('is_active', true)
            ->first();
    }

    /*
    |--------------------------------------------------------------------------
    | Calcolo sconto Egili (opzionale, MiCA-safe)
    |--------------------------------------------------------------------------
    */

    /**
     * Calcola le informazioni di sconto applicabili per un utente su un piano.
     *
     * @return array{
     *   eligible: bool,
     *   egili_available: int,
     *   egili_required: int,
     *   discount_percent: int,
     *   discount_eur: float,
     *   final_price_eur: float
     * }
     */
    public function calculateDiscount(User $user, string $featureCode): array {
        $plan = $this->getPlanByCode($featureCode);

        if (! $plan) {
            return $this->noDiscount(0.0);
        }

        $priceEur           = (float) $plan->cost_fiat_eur;
        $params             = $plan->feature_parameters ?? [];
        $discountPercent    = (int) ($params['egili_discount_percent'] ?? 0);
        $egiliForDiscount   = (int) ($params['egili_required_for_discount'] ?? 0);

        if ($discountPercent <= 0 || $priceEur <= 0) {
            return $this->noDiscount($priceEur);
        }

        $egiliBalance = $this->egiliService->getBalance($user);
        $eligible     = $egiliForDiscount > 0
            ? $egiliBalance >= $egiliForDiscount
            : true; // sconto automatico senza soglia minima

        if (! $eligible) {
            return [
                'eligible'         => false,
                'egili_available'  => $egiliBalance,
                'egili_required'   => $egiliForDiscount,
                'discount_percent' => $discountPercent,
                'discount_eur'     => 0.0,
                'final_price_eur'  => $priceEur,
            ];
        }

        $discountEur   = round($priceEur * ($discountPercent / 100), 2);
        $finalPriceEur = max(0.01, round($priceEur - $discountEur, 2));

        return [
            'eligible'         => true,
            'egili_available'  => $egiliBalance,
            'egili_required'   => $egiliForDiscount,
            'discount_percent' => $discountPercent,
            'discount_eur'     => $discountEur,
            'final_price_eur'  => $finalPriceEur,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Avvio pagamento
    |--------------------------------------------------------------------------
    */

    /**
     * Avvia il pagamento per un abbonamento.
     * Crea una riga CollectionSubscription in status 'pending' e restituisce
     * l'URL di Stripe Checkout (o PayPal) a cui redirezionare l'utente.
     *
     * @param bool $applyEgiliDiscount  Se true, applica sconto Egili e scala il saldo.
     *
     * @return array{
     *   subscription_id: int,
     *   checkout_url: string,
     *   provider_session_id: string,
     *   amount_eur: float
     * }
     *
     * @throws \Exception In caso di piano non trovato, pagamento non iniziabile, etc.
     */
    public function initiatePayment(
        User       $user,
        Collection $collection,
        string     $featureCode,
        string     $provider,
        bool       $applyEgiliDiscount = false,
        string     $successUrl         = '',
        string     $cancelUrl          = ''
    ): array {
        $this->logger->info('CollectionSubscriptionFiat: initiatePayment start', [
            'user_id'        => $user->id,
            'collection_id'  => $collection->id,
            'feature_code'   => $featureCode,
            'provider'       => $provider,
            'with_discount'  => $applyEgiliDiscount,
        ]);

        // 1. Recupera piano
        $plan = $this->getPlanByCode($featureCode);
        if (! $plan) {
            return $this->errorManager->handle(
                'SUBSCRIPTION_PLAN_NOT_FOUND',
                ['feature_code' => $featureCode],
            );
        }

        // 2. Calcola prezzo finale (con eventuale sconto Egili)
        $basePrice   = (float) $plan->cost_fiat_eur;
        $discountInfo = $applyEgiliDiscount
            ? $this->calculateDiscount($user, $featureCode)
            : $this->noDiscount($basePrice);

        $finalPrice       = (float) $discountInfo['final_price_eur'];
        $discountEur      = (float) $discountInfo['discount_eur'];
        $egiliToSpend     = ($applyEgiliDiscount && $discountInfo['eligible'])
            ? (int) ($plan->feature_parameters['egili_required_for_discount'] ?? 0)
            : 0;

        // 3. Verifica saldo Egili se necessario
        if ($egiliToSpend > 0 && ! $this->egiliService->canSpend($user, $egiliToSpend)) {
            return $this->errorManager->handle(
                'SUBSCRIPTION_INSUFFICIENT_EGILI',
                ['user_id' => $user->id, 'required' => $egiliToSpend],
            );
        }

        // 4. Prepara URL di ritorno
        $successUrl = $successUrl ?: route('collections.show', $collection->id);
        $cancelUrl  = $cancelUrl  ?: route('collections.show', $collection->id);

        // 5. Crea PaymentRequest per Stripe/PayPal
        $paymentRequest = new PaymentRequest(
            amount: $finalPrice,
            currency: 'EUR',
            customerEmail: $user->email,
            userId: $user->id,
            metadata: [
                'feature_code'  => $featureCode,
                'collection_id' => $collection->id,
                'plan_tier'     => $plan->feature_parameters['tier'] ?? $featureCode,
                'discount_eur'  => $discountEur,
                'egili_spent'   => $egiliToSpend,
            ],
            successUrl: $successUrl,
            cancelUrl: $cancelUrl,
        );

        // 6. Transazione DB + chiamata provider
        $subscription = null;
        $result       = null;

        try {
            DB::transaction(function () use (
                $user,
                $collection,
                $plan,
                $featureCode,
                $paymentRequest,
                $provider,
                $finalPrice,
                $discountEur,
                $egiliToSpend,
                &$subscription,
                &$result
            ) {
                // 6a. Crea record pending
                $tier = $plan->feature_parameters['tier'] ?? $featureCode;
                $maxEgis = isset($plan->feature_parameters['max_egis'])
                    ? (int) $plan->feature_parameters['max_egis']
                    : null;

                $subscription = CollectionSubscription::create([
                    'collection_id'          => $collection->id,
                    'user_id'                => $user->id,
                    'feature_code'           => $featureCode,
                    'plan_tier'              => $tier,
                    'max_egis'               => $maxEgis,
                    'payment_provider'       => $provider,
                    'amount_eur'             => $finalPrice,
                    'egili_discount_applied' => $egiliToSpend,
                    'discount_amount_eur'    => $discountEur,
                    'status'                 => 'pending',
                ]);

                // 6b. Scala Egili (se sconto applicato) — effetto immediato, reversibile su refund
                if ($egiliToSpend > 0) {
                    $this->egiliService->spend(
                        user: $user,
                        amount: $egiliToSpend,
                        reason: 'collection_subscription_discount',
                        category: 'subscription',
                        metadata: ['subscription_id' => $subscription->id],
                        source: $subscription,
                    );
                }

                // 6c. Chiama provider pagamento
                $paymentService = $this->paymentFactory->create($provider);
                $result         = $paymentService->processPayment($paymentRequest);

                if (! $result->success && $result->status !== 'pending_checkout') {
                    throw new \RuntimeException(
                        $result->errorMessage ?? __('subscription.payment_failed')
                    );
                }

                // 6d. Aggiorna record con dati provider
                $subscription->update([
                    'provider_session_id' => $result->metadata['checkout_session_id']
                        ?? $result->paymentId,
                ]);
            });
        } catch (\Throwable $e) {
            return $this->errorManager->handle(
                'SUBSCRIPTION_PAYMENT_INITIATION_FAILED',
                [
                    'user_id'       => $user->id,
                    'collection_id' => $collection->id,
                    'feature_code'  => $featureCode,
                    'provider'      => $provider,
                ],
                $e
            );
        }

        // 7. GDPR audit
        $this->auditService->logUserAction(
            $user,
            'subscription_payment_initiated',
            [
                'subscription_id' => $subscription->id,
                'collection_id'   => $collection->id,
                'feature_code'    => $featureCode,
                'amount_eur'      => $finalPrice,
                'provider'        => $provider,
            ],
            GdprActivityCategory::WALLET_MANAGEMENT
        );

        $this->logger->info('CollectionSubscriptionFiat: initiatePayment OK', [
            'subscription_id'    => $subscription->id,
            'provider_session_id' => $subscription->provider_session_id,
            'checkout_url'       => $result->redirectUrl ?? null,
        ]);

        return [
            'subscription_id'    => $subscription->id,
            'checkout_url'       => $result->redirectUrl ?? '',
            'provider_session_id' => $subscription->provider_session_id ?? '',
            'amount_eur'         => $finalPrice,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Conferma pagamento (webhook)
    |--------------------------------------------------------------------------
    */

    /**
     * Attiva un abbonamento dopo conferma del provider (webhook / redirect success).
     *
     * @param string $providerSessionId  ID sessione Stripe (o equivalente PayPal)
     * @return CollectionSubscription    L'abbonamento attivato
     *
     * @throws \Exception Se la subscription non esiste o è già attiva
     */
    public function confirmPayment(string $providerSessionId): CollectionSubscription {
        $this->logger->info('CollectionSubscriptionFiat: confirmPayment', [
            'provider_session_id' => $providerSessionId,
        ]);

        $subscription = CollectionSubscription::where('provider_session_id', $providerSessionId)
            ->where('status', 'pending')
            ->first();

        if (! $subscription) {
            return $this->errorManager->handle(
                'SUBSCRIPTION_NOT_FOUND_OR_ALREADY_CONFIRMED',
                ['provider_session_id' => $providerSessionId],
            );
        }

        try {
            DB::transaction(function () use ($subscription) {
                $now = now();
                $subscription->update([
                    'status'     => 'active',
                    'starts_at'  => $now,
                    'expires_at' => $now->copy()->addDays(self::SUBSCRIPTION_DAYS),
                ]);
            });
        } catch (\Throwable $e) {
            return $this->errorManager->handle(
                'SUBSCRIPTION_CONFIRM_FAILED',
                ['subscription_id' => $subscription->id],
                $e
            );
        }

        /** @var User $user */
        $user = $subscription->user;

        $this->auditService->logUserAction(
            $user,
            'subscription_activated',
            [
                'subscription_id' => $subscription->id,
                'collection_id'   => $subscription->collection_id,
                'feature_code'    => $subscription->feature_code,
                'expires_at'      => $subscription->expires_at?->toIso8601String(),
            ],
            GdprActivityCategory::WALLET_MANAGEMENT
        );

        $this->logger->info('CollectionSubscriptionFiat: subscription activated', [
            'subscription_id' => $subscription->id,
            'expires_at'      => $subscription->expires_at?->toIso8601String(),
        ]);

        return $subscription->refresh();
    }

    /*
    |--------------------------------------------------------------------------
    | Stato abbonamento
    |--------------------------------------------------------------------------
    */

    /**
     * Verifica se una Collection ha un abbonamento attivo nella nuova tabella.
     *
     * Questo metodo sostituisce la logica (errata) di hasActiveSubscription
     * presente in CollectionSubscriptionService.
     */
    public function hasActiveSubscription(Collection $collection): bool {
        return CollectionSubscription::query()
            ->forCollection($collection->id)
            ->active()
            ->exists();
    }

    /**
     * Restituisce l'abbonamento attivo corrente di una Collection, o null.
     */
    public function getActiveSubscription(Collection $collection): ?CollectionSubscription {
        return CollectionSubscription::query()
            ->forCollection($collection->id)
            ->active()
            ->latest('starts_at')
            ->first();
    }

    /**
     * Restituisce lo storico abbonamenti di una Collection.
     *
     * @param int|null $limit Numero massimo (P0-3: esplicito nel signature)
     * @return \Illuminate\Database\Eloquent\Collection<CollectionSubscription>
     */
    public function getSubscriptionHistory(
        Collection $collection,
        ?int $limit = null
    ): \Illuminate\Database\Eloquent\Collection {
        $query = CollectionSubscription::query()
            ->forCollection($collection->id)
            ->orderBy('created_at', 'desc');

        if ($limit !== null) {
            $query->limit($limit);
        }

        return $query->get();
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers privati
    |--------------------------------------------------------------------------
    */

    private function noDiscount(float $priceEur): array {
        return [
            'eligible'         => false,
            'egili_available'  => 0,
            'egili_required'   => 0,
            'discount_percent' => 0,
            'discount_eur'     => 0.0,
            'final_price_eur'  => $priceEur,
        ];
    }
}
