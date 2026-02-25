@props(['featureCode'])

@php
    // Pricing dal catalog (può essere null, lo gestiamo nel JS)
    $pricing = DB::table('ai_feature_pricing')->where('feature_code', $featureCode)->where('is_active', true)->first();

    // User Egili balance
    $user = Auth::user();
    $egiliBalance = $user?->primaryWallet->egili_balance ?? 0;
@endphp

{{-- Feature Purchase Modal --}}
<div id="feature-purchase-modal-{{ $featureCode }}"
    class="fixed inset-0 z-50 flex hidden items-center justify-center bg-black/70 backdrop-blur-sm"
    onclick="if(event.target === this) closeFeaturePurchaseModal('{{ $featureCode }}')">

    <div class="mx-4 max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl bg-white shadow-2xl"
        onclick="event.stopPropagation()">

        {{-- MODALE SEMPRE VISIBILE (anche senza pricing) --}}
        {{-- Header --}}
        <div class="rounded-t-2xl bg-gradient-to-r from-purple-600 to-blue-600 p-6 text-white">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    @if ($pricing && $pricing->icon_name)
                        <div class="mr-4 flex h-12 w-12 items-center justify-center rounded-full bg-white/20">
                            <span class="text-2xl">{{ $pricing->icon_name }}</span>
                        </div>
                    @else
                        <div class="mr-4 flex h-12 w-12 items-center justify-center rounded-full bg-white/20">
                            <span class="text-2xl">🧠</span>
                        </div>
                    @endif
                    <div>
                        <h2 class="text-2xl font-bold">{{ $pricing->feature_name ?? 'EGI Living Subscription' }}</h2>
                        <p class="text-sm text-purple-100">
                            {{ $pricing->feature_description ?? 'Sblocca funzionalità avanzate per i tuoi EGI' }}</p>
                    </div>
                </div>
                <button onclick="closeFeaturePurchaseModal('{{ $featureCode }}')"
                    class="text-white transition-colors hover:text-gray-200">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <div class="p-6">
            {{-- STEP 1: Quanti EGI vuoi caricare? --}}
            <div class="mb-6">
                <h3 class="mb-3 text-lg font-semibold" style="color: #8E44AD;">
                    {{ __('features.how_many_egi') }}
                </h3>
                <div class="flex items-center gap-4">
                    <input type="number" id="egi-quantity-{{ $featureCode }}" min="1" value="1"
                        class="w-32 rounded-lg border-2 border-purple-300 px-4 py-2 text-center text-lg font-bold focus:border-purple-600 focus:outline-none"
                        onchange="updatePriceCalculation('{{ $featureCode }}', {{ $pricing->cost_egili ?? 500 }}, {{ $egiliBalance }})">
                    <span class="text-gray-600">× {{ __('features.egi_smartcontract') }}</span>
                </div>
            </div>

            {{-- Benefits --}}
            @if ($pricing && $pricing->benefits)
                <div class="mb-6">
                    <h3 class="mb-3 text-lg font-semibold" style="color: #8E44AD;">
                        {{ __('features.what_you_get') }}
                    </h3>
                    <ul class="space-y-2">
                        @foreach (json_decode($pricing->benefits, true) as $benefit)
                            <li class="flex items-start">
                                <svg class="mr-2 mt-0.5 h-5 w-5 flex-shrink-0 text-green-600" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-gray-700">{{ $benefit }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @else
                <div class="mb-6">
                    <h3 class="mb-3 text-lg font-semibold" style="color: #8E44AD;">
                        {{ __('features.egi_living_benefits_title') }}
                    </h3>
                    <ul class="space-y-2">
                        <li class="flex items-start">
                            <svg class="mr-2 mt-0.5 h-5 w-5 flex-shrink-0 text-green-600" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-gray-700">{{ __('features.egi_living_benefit_curator') }}</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="mr-2 mt-0.5 h-5 w-5 flex-shrink-0 text-green-600" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-gray-700">{{ __('features.egi_living_benefit_promoter') }}</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="mr-2 mt-0.5 h-5 w-5 flex-shrink-0 text-green-600" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-gray-700">{{ __('features.egi_living_benefit_provenance') }}</span>
                        </li>
                    </ul>
                </div>
            @endif

            {{-- Pricing Section (Dynamic) --}}
            <div class="mb-6 rounded-lg bg-gradient-to-r from-purple-50 to-blue-50 p-6">
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <div class="mb-1 text-sm text-gray-600">{{ __('features.total_cost') }}</div>
                        <div class="text-3xl font-bold" style="color: #8E44AD;">
                            <span
                                id="total-egili-{{ $featureCode }}">{{ number_format($pricing->cost_egili ?? 500) }}</span>
                            Egili
                        </div>
                        <div class="text-sm text-gray-600">
                            ≈ €<span
                                id="total-eur-{{ $featureCode }}">{{ number_format(($pricing->cost_egili ?? 500) * 0.1, 2) }}</span>
                        </div>
                        <div class="mt-1 text-xs text-gray-500">
                            (<span
                                id="price-per-egi-{{ $featureCode }}">{{ number_format($pricing->cost_egili ?? 500) }}</span>
                            {{ __('features.egili_per_egi') }})
                        </div>
                    </div>

                    @if (!$pricing || !$pricing->is_recurring)
                        <div class="rounded-full bg-white px-3 py-1 text-sm font-semibold text-purple-700">
                            ♾️ {{ __('payment.lifetime') }}
                        </div>
                    @endif
                </div>

                {{-- User Balance --}}
                <div class="border-t border-gray-200 pt-4">
                    <div class="mb-2 flex items-center justify-between text-sm">
                        <span class="text-gray-600">{{ __('payment.your_egili_balance') }}:</span>
                        <span id="user-balance-{{ $featureCode }}" class="font-bold text-gray-700">
                            {{ number_format($egiliBalance) }} Egili
                        </span>
                    </div>

                    <div id="insufficient-warning-{{ $featureCode }}" class="hidden">
                        <div class="rounded-lg border border-red-200 bg-red-50 p-3">
                            <div class="flex items-start">
                                <svg class="mr-2 mt-0.5 h-5 w-5 text-red-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-red-800">
                                        {{ __('payment.insufficient_egili_title') }}
                                    </p>
                                    <p class="mt-1 text-xs text-red-700">
                                        {{ __('features.need_more_egili_prefix') }} <span
                                            id="deficit-amount-{{ $featureCode }}">0</span> Egili
                                    </p>
                                </div>
                            </div>

                            {{-- Apre modal Pacchetti AI (FIAT: Stripe/PayPal) — ToS v3.0.0 --}}
                            <button onclick="openEgiliPurchaseModal()" type="button"
                                class="mt-3 block w-full rounded-lg bg-red-600 px-4 py-2 text-center text-sm font-semibold text-white transition-colors hover:bg-red-700">
                                {{ __('egili.buy_more') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Form --}}
            <form id="feature-purchase-form-{{ $featureCode }}" action="{{ route('features.purchase.process') }}"
                method="POST">
                @csrf
                <input type="hidden" name="feature_code" value="{{ $featureCode }}">
                <input type="hidden" name="payment_method" value="egili">
                <input type="hidden" name="quantity" id="quantity-hidden-{{ $featureCode }}" value="1">

                {{-- Action Buttons --}}
                <div class="flex gap-3">
                    <button type="submit" id="submit-btn-{{ $featureCode }}"
                        class="flex-1 rounded-lg bg-gradient-to-r from-purple-600 to-purple-700 px-6 py-3 font-bold text-white transition-all hover:from-purple-700 hover:to-purple-800 disabled:cursor-not-allowed disabled:from-gray-400 disabled:to-gray-500">
                        <span class="flex items-center justify-center">
                            <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            {{ __('features.unlock_with_egili') }}
                        </span>
                    </button>

                    <button type="button" onclick="closeFeaturePurchaseModal('{{ $featureCode }}')"
                        class="flex-1 rounded-lg bg-gray-200 px-6 py-3 font-bold text-gray-700 transition-colors hover:bg-gray-300">
                        {{ __('common.cancel') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Open modal
    window.openFeaturePurchaseModal = function(featureCode) {
        const modal = document.getElementById(`feature-purchase-modal-${featureCode}`);
        if (modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            // Initialize calculation on open
            const pricePerEgi = {{ $pricing->cost_egili ?? 500 }};
            const userBalance = {{ $egiliBalance }};
            updatePriceCalculation(featureCode, pricePerEgi, userBalance);
        }
    };

    // Close modal
    window.closeFeaturePurchaseModal = function(featureCode) {
        const modal = document.getElementById(`feature-purchase-modal-${featureCode}`);
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }
    };

    // Update price calculation
    window.updatePriceCalculation = function(featureCode, pricePerEgi, userBalance) {
        const quantityInput = document.getElementById(`egi-quantity-${featureCode}`);
        const quantity = parseInt(quantityInput.value) || 1;

        // Calculate totals
        const totalEgili = quantity * pricePerEgi;
        const totalEur = (totalEgili * 0.1).toFixed(2);

        // Update display
        document.getElementById(`total-egili-${featureCode}`).textContent = totalEgili.toLocaleString();
        document.getElementById(`total-eur-${featureCode}`).textContent = totalEur;
        document.getElementById(`price-per-egi-${featureCode}`).textContent = pricePerEgi.toLocaleString();
        document.getElementById(`quantity-hidden-${featureCode}`).value = quantity;

        // Check affordability
        const canAfford = userBalance >= totalEgili;
        const submitBtn = document.getElementById(`submit-btn-${featureCode}`);
        const warningDiv = document.getElementById(`insufficient-warning-${featureCode}`);
        const balanceSpan = document.getElementById(`user-balance-${featureCode}`);

        if (canAfford) {
            submitBtn.disabled = false;
            warningDiv.classList.add('hidden');
            balanceSpan.classList.remove('text-red-600');
            balanceSpan.classList.add('text-green-600');
        } else {
            submitBtn.disabled = true;
            warningDiv.classList.remove('hidden');
            const deficit = totalEgili - userBalance;
            document.getElementById(`deficit-amount-${featureCode}`).textContent = deficit.toLocaleString();
            balanceSpan.classList.remove('text-green-600');
            balanceSpan.classList.add('text-red-600');
        }
    };
</script>
