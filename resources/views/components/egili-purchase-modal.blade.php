{{-- AI Package Purchase Modal --}}
{{-- [REFACTOR] ToS v3.1.0: prodotto = Pacchetti Servizi AI in FIAT. Egili = contatore interno accreditato automaticamente. --}}
@php
    $packages = config('ai-credits.ai_service_packages', []);
    $egiliRatio = config('ai-credits.egili_credit_ratio', 0.8);
    $stripeEnabled = config('egili.ai_package_payment_providers.fiat.stripe.enabled', true);
    $paypalEnabled = config('egili.ai_package_payment_providers.fiat.paypal.enabled', true);
    // Pre-build JS-ready array (avoids inline PHP loops in JS)
    $packagesForJs = [];
    foreach ($packages as $key => $pkg) {
        $packagesForJs[] = [
            'key' => $key,
            'label' => $pkg['label'],
            'price_eur' => $pkg['price_eur'],
            'egili_credited' => (int) round(($pkg['credits'] ?? 0) * $egiliRatio),
        ];
    }
@endphp

<div id="egili-purchase-modal"
    class="fixed inset-0 z-[9999] flex hidden items-center justify-center bg-black/80 backdrop-blur-sm"
    onclick="if(event.target === this) closeEgiliPurchaseModal()">

    <div class="mx-4 max-h-[90vh] w-full max-w-3xl overflow-y-auto rounded-2xl bg-white shadow-2xl"
        onclick="event.stopPropagation()">

        {{-- Header --}}
        <div class="rounded-t-2xl bg-gradient-to-r from-purple-600 to-blue-600 p-6 text-white">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="mr-4 flex h-16 w-16 items-center justify-center rounded-full bg-white/20"
                        aria-hidden="true">
                        <span class="text-4xl">✨</span>
                    </div>
                    <div>
                        <h2 class="text-3xl font-bold">{{ __('egili.purchase.title') }}</h2>
                        <p class="text-sm text-purple-100">{{ __('egili.purchase.subtitle') }}</p>
                    </div>
                </div>
                <button onclick="closeEgiliPurchaseModal()" class="text-white transition-colors hover:text-gray-200">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <form id="egili-purchase-form" class="p-8">
            @csrf

            {{-- Nota Legale --}}
            <div class="mb-5 rounded-xl border border-blue-200 bg-blue-50 p-3 text-xs text-blue-700" role="note">
                💡 {{ __('egili.purchase.legal_note') }}
            </div>

            {{-- Step 1: Package Selection --}}
            <div class="mb-6">
                <label class="mb-3 block text-sm font-semibold text-gray-700">
                    🤖 {{ __('egili.purchase.select_package') }}
                </label>
                <div class="grid grid-cols-2 gap-3">
                    @foreach ($packages as $key => $pkg)
                        @php $egiliCredited = (int) round(($pkg['credits'] ?? 0) * $egiliRatio); @endphp
                        <label class="relative cursor-pointer">
                            <input type="radio" name="package_key" value="{{ $key }}"
                                {{ $loop->first ? 'checked' : '' }} class="peer sr-only"
                                onchange="updatePackageSummary()">
                            <div
                                class="focus-within:ring-oro-fiorentino rounded-xl border-2 border-gray-200 p-4 transition-all focus-within:ring-2 hover:border-purple-400 peer-checked:border-purple-600 peer-checked:bg-purple-50">
                                <p class="text-sm font-bold text-gray-800">{{ $pkg['label'] }}</p>
                                <p class="mt-1 text-2xl font-extrabold text-purple-700">
                                    €{{ number_format($pkg['price_eur'], 2, ',', '.') }}
                                </p>
                                <p class="mt-1 text-xs text-gray-500">
                                    💎 {{ number_format($egiliCredited) }} {{ __('egili.purchase.egili_credited') }}
                                </p>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Package Summary --}}
            <div id="package-summary"
                class="mb-6 rounded-xl border-2 border-purple-200 bg-gradient-to-r from-purple-50 to-blue-50 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="mb-1 text-xs text-gray-500">{{ __('egili.purchase.you_get') }}</p>
                        <p id="summary-egili" class="text-2xl font-bold text-purple-700" aria-live="polite">— Egili</p>
                    </div>
                    <div class="text-right">
                        <p class="mb-1 text-xs text-gray-500">{{ __('egili.purchase.total_cost') }}</p>
                        <p id="summary-price" class="text-3xl font-bold text-purple-700" aria-live="polite">€—</p>
                    </div>
                </div>
                <p class="mt-3 text-xs text-gray-400">{{ __('egili.purchase.egili_model_note') }}</p>
            </div>

            {{-- Step 2: Metodo di Pagamento (solo FIAT — no crypto per ToS v3.1.0) --}}
            <div class="mb-6">
                <label class="mb-3 block text-sm font-semibold text-gray-700">
                    💳 {{ __('egili.purchase.select_provider') }}
                </label>
                <div class="space-y-2">
                    @if ($stripeEnabled)
                        <label class="relative block cursor-pointer">
                            <input type="radio" name="fiat_provider" value="stripe" checked class="peer sr-only">
                            <div
                                class="focus-within:ring-oro-fiorentino flex items-center rounded-lg border-2 border-gray-300 p-3 transition-all focus-within:ring-2 hover:border-purple-400 peer-checked:border-purple-600 peer-checked:bg-purple-50">
                                <span class="mr-3 text-2xl" aria-hidden="true">💳</span>
                                <span class="font-medium">{{ __('egili.purchase.fiat_provider_stripe') }}</span>
                            </div>
                        </label>
                    @endif
                    @if ($paypalEnabled)
                        <label class="relative block cursor-pointer">
                            <input type="radio" name="fiat_provider" value="paypal" class="peer sr-only">
                            <div
                                class="focus-within:ring-oro-fiorentino flex items-center rounded-lg border-2 border-gray-300 p-3 transition-all focus-within:ring-2 hover:border-purple-400 peer-checked:border-purple-600 peer-checked:bg-purple-50">
                                <span class="mr-3 text-2xl" aria-hidden="true">🅿️</span>
                                <span class="font-medium">{{ __('egili.purchase.fiat_provider_paypal') }}</span>
                            </div>
                        </label>
                    @endif
                </div>
            </div>

            {{-- Error Message --}}
            <div id="purchase-error" class="mb-4 hidden rounded border-l-4 border-red-500 bg-red-50 p-4" role="alert"
                aria-live="assertive">
                <p class="text-sm font-semibold text-red-700"></p>
            </div>

            {{-- Submit Button --}}
            <div class="flex items-center justify-between">
                <button type="button" onclick="closeEgiliPurchaseModal()"
                    class="font-semibold text-gray-600 transition-colors hover:text-gray-800">
                    ← {{ __('common.back') }}
                </button>

                <button type="submit" id="purchase-submit-btn"
                    class="transform rounded-lg bg-gradient-to-r from-purple-600 to-blue-600 px-8 py-3 font-bold text-white shadow-lg transition-all hover:scale-105 hover:from-purple-700 hover:to-blue-700 disabled:cursor-not-allowed disabled:opacity-50">
                    <span id="submit-btn-text">✨ {{ __('egili.purchase.purchase_now') }}</span>
                    <span id="submit-btn-loading" class="hidden">
                        <svg class="inline h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        {{ __('egili.purchase.processing') }}
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Packages injected from backend PHP — no hardcoding in JS (OS3 Intenzionalità)
    window.AI_PACKAGES = window.AI_PACKAGES || {!! json_encode($packagesForJs) !!};
    window.EGILI_MODAL_CONFIG = window.EGILI_MODAL_CONFIG || {
        csrf: document.querySelector('meta[name="csrf-token"]')?.content || '',
    };

    // Open modal (keep same function name — 10 trigger points in codebase)
    window.openEgiliPurchaseModal = function() {
        if (typeof window.closeMobileMenu === 'function') {
            window.closeMobileMenu();
        }
        const modal = document.getElementById('egili-purchase-modal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.style.display = '';
            setTimeout(() => updatePackageSummary(), 50);
        } else {
            console.error('❌ Modal #egili-purchase-modal NOT FOUND in DOM');
        }
    };

    // Close modal
    window.closeEgiliPurchaseModal = function() {
        const modal = document.getElementById('egili-purchase-modal');
        if (modal) {
            modal.classList.add('hidden');
            document.getElementById('egili-purchase-form').reset();
            hideError();
            updatePackageSummary();
        }
    };

    // Update summary card when package radio changes
    window.updatePackageSummary = function() {
        const selected = document.querySelector('input[name="package_key"]:checked');
        if (!selected) return;
        const pkg = (window.AI_PACKAGES || []).find(p => p.key === selected.value);
        if (pkg) {
            document.getElementById('summary-egili').textContent =
                pkg.egili_credited.toLocaleString('it-IT') + ' Egili';
            document.getElementById('summary-price').textContent =
                '€' + pkg.price_eur.toFixed(2).replace('.', ',');
        }
    };

    function showError(msg) {
        const el = document.getElementById('purchase-error');
        el.querySelector('p').textContent = msg;
        el.classList.remove('hidden');
    }

    function hideError() {
        document.getElementById('purchase-error').classList.add('hidden');
    }

    // Form submit
    document.getElementById('egili-purchase-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        hideError();

        const submitBtn = document.getElementById('purchase-submit-btn');
        const btnText = document.getElementById('submit-btn-text');
        const btnLoad = document.getElementById('submit-btn-loading');

        submitBtn.disabled = true;
        btnText.classList.add('hidden');
        btnLoad.classList.remove('hidden');

        try {
            const formData = new FormData(this);
            const packageKey = formData.get('package_key');
            const provider = formData.get('fiat_provider');
            const pkg = (window.AI_PACKAGES || []).find(p => p.key === packageKey);

            const response = await fetch('{{ route('egili.purchase.process') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.EGILI_MODAL_CONFIG.csrf,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    package_key: packageKey,
                    egili_amount: pkg ? pkg.egili_credited :
                    0, // backward-compat controller
                    payment_method: 'fiat',
                    fiat_provider: provider,
                    crypto_provider: null,
                }),
            });

            const result = await response.json();

            if (result.success && result.redirect_url) {
                window.location.href = result.redirect_url;
            } else {
                showError(result.message || '{{ __('egili.purchase.process_error') }}');
                submitBtn.disabled = false;
                btnText.classList.remove('hidden');
                btnLoad.classList.add('hidden');
            }
        } catch (error) {
            console.error('AI Package purchase error:', error);
            showError('{{ __('egili.purchase.process_error') }}');
            submitBtn.disabled = false;
            btnText.classList.remove('hidden');
            btnLoad.classList.add('hidden');
        }
    });

    // Initialize summary
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', updatePackageSummary);
    } else {
        updatePackageSummary();
    }
</script>
