{{--
    MINT PAYMENT FORM - PAGINA 1
    🎯 Purpose: Form pagamento FIAT per mint EGI
    📍 Route: GET /mint/payment/{egiId}?reservation_id={id}
    ➡️ Submit: POST /mint/process → Redirect a mint.blade.php
--}}
<x-platform-layout :title="__('mint.page_title', ['title' => $egi->title])">
    <div class="container mx-auto max-w-4xl px-4 py-8">

        {{-- Header --}}
        <div class="mb-8 rounded-xl bg-gradient-to-br from-[#1B365D] to-[#2D5016] p-6 text-center shadow-2xl">
            <h1 class="mb-2 text-3xl font-bold text-white drop-shadow-lg">
                {{ __('mint.header_title') }}
            </h1>
            <p class="text-lg font-semibold text-[#D4A574]">
                {{ __('mint.header_description') }}
            </p>
        </div>

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">

            {{-- COLONNA 1: EGI Preview --}}
            <div class="space-y-6">

                {{-- Immagine EGI --}}
                <div class="overflow-hidden rounded-lg bg-white shadow-lg">
                    @if ($egi->main_image_url)
                        <img src="{{ $egi->main_image_url }}" alt="{{ $egi->title }}" class="h-64 w-full object-cover">
                    @else
                        <div class="flex h-64 w-full items-center justify-center bg-gray-200">
                            <span class="text-4xl text-gray-400">🎨</span>
                        </div>
                    @endif

                    <div class="p-6">
                        <h2 class="mb-2 text-xl font-bold text-gray-900">{{ $egi->title }}</h2>
                        <p class="mb-4 text-sm text-gray-600">
                            {{ __('mint.egi_preview.creator_by', ['name' => $egi->user->name]) }}
                        </p>

                        @if ($egi->description)
                            <p class="text-sm text-gray-700">{{ Str::limit($egi->description, 150) }}</p>
                        @endif
                    </div>
                </div>

                {{-- Blockchain Info - Contrasto migliorato --}}
                <div class="rounded-lg bg-blue-50 p-6">
                    <h3 class="mb-3 text-lg font-semibold text-blue-900">
                        {{ __('mint.blockchain_info.title') }}
                    </h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-blue-700">{{ __('mint.blockchain_info.network') }}</span>
                            <span
                                class="font-semibold text-blue-900">{{ __('mint.blockchain_info.network_value') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-blue-700">{{ __('mint.blockchain_info.token_type') }}</span>
                            <span
                                class="font-semibold text-blue-900">{{ __('mint.blockchain_info.token_type_value') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-blue-700">{{ __('mint.blockchain_info.supply') }}</span>
                            <span
                                class="font-semibold text-blue-900">{{ __('mint.blockchain_info.supply_value') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Prezzo Originale --}}
                <div class="{{ $isGoldBar ?? false ? 'bg-amber-50' : 'bg-green-50' }} rounded-lg p-6">
                    <h3 class="{{ $isGoldBar ?? false ? 'text-amber-900' : 'text-green-900' }} mb-2 font-semibold">
                        {{ $isGoldBar ?? false ? __('gold_bar.indicative_value') : __('mint.payment.price_label') }}
                    </h3>
                    <div class="{{ $isGoldBar ?? false ? 'text-amber-600' : 'text-green-600' }} text-3xl font-bold">
                        €{{ number_format($paymentAmountEur ?? 0, 2) }}
                    </div>

                    {{-- Gold Bar specific info --}}
                    @if (($isGoldBar ?? false) && !empty($goldBarData) && is_array($goldBarData))
                        <div class="mt-3 space-y-2 border-l-4 border-amber-400 pl-3 text-sm text-amber-700">
                            <div class="flex items-center gap-2 font-semibold">
                                <img src="{{ asset('images/icons/goldbar.png') }}" alt="Gold Bar" class="h-5 w-5">
                                <span>{{ __('gold_bar.indicative_value') }}</span>
                            </div>

                            {{-- Gold Bar Details --}}
                            <div class="grid grid-cols-2 gap-2 text-xs">
                                <div>
                                    <span class="text-gray-500">{{ __('gold_bar.weight') }}:</span>
                                    <span
                                        class="font-medium">{{ number_format($goldBarData['weight_grams'] ?? 0, 2) }}g</span>
                                </div>
                                <div>
                                    <span class="text-gray-500">{{ __('gold_bar.pure_gold') }}:</span>
                                    <span
                                        class="font-medium">{{ number_format($goldBarData['pure_gold_grams'] ?? 0, 2) }}g</span>
                                </div>
                                <div>
                                    <span class="text-gray-500">{{ __('gold_bar.gold_price') }}:</span>
                                    <span
                                        class="font-medium">€{{ number_format($goldBarData['gold_price_per_gram'] ?? 0, 2) }}/g</span>
                                </div>
                                <div>
                                    <span class="text-gray-500">{{ __('gold_bar.base_value') }}:</span>
                                    <span
                                        class="font-medium">€{{ number_format($goldBarData['base_value'] ?? 0, 2) }}</span>
                                </div>
                                @if (($goldBarData['margin_applied'] ?? 0) > 0)
                                    <div class="col-span-2">
                                        <span class="text-gray-500">{{ __('gold_bar.creator_margin') }}:</span>
                                        <span
                                            class="font-medium">+€{{ number_format($goldBarData['margin_applied'] ?? 0, 2) }}</span>
                                    </div>
                                @endif
                            </div>

                            <div class="border-t border-amber-300 pt-2">
                                <span class="text-gray-600">{{ __('gold_bar.final_value') }}:</span>
                                <span
                                    class="text-lg font-bold text-amber-900">€{{ number_format($goldBarData['final_value'] ?? 0, 2) }}</span>
                            </div>

                            {{-- FEE BREAKDOWN (User Request) --}}
                            <div class="mt-2 border-t border-dashed border-amber-300 pt-2 text-xs">
                                <div class="flex justify-between text-amber-800/80">
                                    <span>Platform (10% Margin):</span>
                                    <span>€{{ number_format($goldBarData['platform_fee'] ?? 0, 2) }}</span>
                                </div>
                                <div class="flex justify-between text-amber-800/80">
                                    <span>Company (Cost + 90%):</span>
                                    <span>€{{ number_format($goldBarData['company_share'] ?? 0, 2) }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- 10-minute timer --}}
                        <div id="gold-bar-timer" class="mt-4 rounded-lg border border-amber-400 bg-amber-100 p-3"
                            data-valid-until="{{ $goldPriceValidUntil ?? '' }}">
                            <div class="flex items-center gap-2 text-sm font-medium text-amber-800">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>{{ __('gold_bar.price_updated_at') }}: <span
                                        id="gold-timer-countdown">10:00</span></span>
                            </div>
                            <p class="mt-1 text-xs text-amber-700">{{ __('gold_bar.mint_price_warning') }}</p>
                        </div>

                        {{-- Timer expired modal placeholder --}}
                        <div id="gold-bar-expired-modal"
                            class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">
                            <div class="mx-4 max-w-md rounded-lg bg-white p-6 shadow-xl">
                                <h3 class="mb-2 text-lg font-bold text-amber-900">⏱️
                                    {{ __('gold_bar.mint_price_warning') }}</h3>
                                <p class="mb-4 text-sm text-gray-700">{{ __('gold_bar.mint_price_expired') }}</p>
                                <button onclick="window.location.reload()"
                                    class="w-full rounded-lg bg-amber-500 px-4 py-2 font-semibold text-white hover:bg-amber-600">
                                    {{ __('gold_bar.refresh_button') }}
                                </button>
                            </div>
                        </div>
                    @else
                        @if ($reservation && $reservation->amount_eur < $egi->price)
                            <p class="mt-2 text-sm text-green-700">
                                ✅ {{ __('mint.payment.winning_reservation') }}:
                                €{{ number_format($reservation->amount_eur, 2) }}
                            </p>
                        @endif
                    @endif
                </div>

            </div>

            {{-- COLONNA 2: Payment Form --}}
            <div class="space-y-6">

                {{-- MiCA Compliance Notice --}}
                <div class="border-l-4 border-blue-500 bg-blue-50 p-4">
                    <h4 class="mb-1 text-sm font-semibold text-blue-900">
                        {{ __('mint.compliance.mica_title') }}
                    </h4>
                    <p class="text-xs text-blue-700">
                        {{ __('mint.compliance.mica_description') }}
                    </p>
                </div>

                {{-- Form Pagamento --}}
                <form id="mint-payment-form"
                    action="{{ $reservation ? route('mint.process') : route('egi.mint-direct.process', $egi->id) }}"
                    method="POST" class="rounded-lg bg-white p-6 shadow-lg">
                    @csrf

                    @php
                        $showEgiliOption = $showEgiliOption ?? false;
                        $canPayWithEgili = $canPayWithEgili ?? false;
                        $egiliBalance = $egiliBalance ?? 0;
                        $requiredEgili = $requiredEgili ?? 0;
                        $paymentAmountEur = $paymentAmountEur ?? ($reservation?->amount_eur ?? ($egi->price ?? 0));
                        $selectedPaymentMethod = old(
                            'payment_method',
                            $showEgiliOption && $canPayWithEgili ? 'egili' : 'stripe',
                        );
                    @endphp

                    <input type="hidden" name="egi_id" value="{{ $egi->id }}">
                    @if ($reservation)
                        <input type="hidden" name="reservation_id" value="{{ $reservation->id }}">
                    @endif



                    {{-- SHIPPING ADDRESS SECTION (New) --}}
                    {{-- FORCE RENDER FOR DEBUGGING --}}
                    {{-- @if ($shippingRequired ?? false) --}}
                    <div class="mb-8 rounded-xl border border-gray-700 bg-gray-800 p-5">
                        <div class="mb-4 flex items-center border-b border-gray-700 pb-2">
                            <span class="mr-2 rounded-lg bg-indigo-900 p-2 text-xl">🚚</span>
                            <h3 class="text-lg font-bold text-gray-100">
                                Dati di Spedizione
                            </h3>
                        </div>

                        @if ($shippingAddresses->count() > 0)
                            <div class="space-y-3">
                                <p class="mb-2 text-sm text-indigo-300">
                                    Seleziona un indirizzo per la consegna del bene fisico:
                                </p>
                                @foreach ($shippingAddresses as $address)
                                    <label
                                        class="flex cursor-pointer items-start rounded-lg border border-gray-600 bg-gray-700 p-3 shadow-sm transition-all hover:border-indigo-400 hover:shadow-md">
                                        <div class="flex h-5 items-center">
                                            <input type="radio" name="shipping_address_id"
                                                value="{{ $address->id }}" {{ $loop->first ? 'checked' : '' }}
                                                class="h-4 w-4 border-gray-500 bg-gray-600 text-indigo-500 focus:ring-indigo-400">
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <div class="font-bold text-gray-100">
                                                {{ $address->full_name }}
                                                @if ($address->is_default)
                                                    <span
                                                        class="ml-2 inline-flex items-center rounded-full bg-indigo-900 px-2.5 py-0.5 text-xs font-medium text-indigo-200">
                                                        Default
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="text-gray-300">{{ $address->address_line_1 }}</div>
                                            <div class="text-gray-300">
                                                {{ $address->city }}, {{ $address->postal_code }}
                                                ({{ $address->country }})
                                            </div>
                                            @if ($address->phone)
                                                <div class="mt-1 text-xs text-gray-400">📞 {{ $address->phone }}
                                                </div>
                                            @endif
                                        </div>
                                    </label>
                                @endforeach
                                <div class="mt-4 border-t border-gray-700 pt-4">
                                    <button type="button" data-action="open-shipping-modal"
                                        onclick="document.getElementById('shipping-address-modal').classList.remove('hidden')"
                                        data-url="{{ route('user.domains.personal-data.shipping-address.store') }}"
                                        data-method="POST"
                                        class="inline-flex items-center text-sm font-semibold text-indigo-400 hover:text-indigo-300">
                                        ➕
                                        ➕ Aggiungi un altro indirizzo
                                    </button>
                                </div>
                            </div>
                        @else
                            <div class="rounded-lg border border-yellow-700 bg-yellow-900/30 p-4 text-center">
                                <p class="mb-3 text-sm text-yellow-200">
                                    ⚠️
                                    ⚠️ Non hai ancora salvato un indirizzo di spedizione.
                                </p>
                                <button type="button" data-action="open-shipping-modal"
                                    onclick="document.getElementById('shipping-address-modal').classList.remove('hidden')"
                                    data-url="{{ route('user.domains.personal-data.shipping-address.store') }}"
                                    data-method="POST"
                                    class="inline-flex items-center rounded-lg bg-indigo-500 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-600">
                                    ➕ Aggiungi Indirizzo
                                </button>
                                <p class="mt-2 text-xs text-gray-400">
                                    Dopo aver aggiunto l'indirizzo, ricarica questa pagina.
                                </p>
                            </div>
                        @endif

                        <input type="hidden" name="shipping_required" value="1">
                    </div>
                    {{-- @endif --}}

                    @push('scripts')
                        {{-- Load Personal Data Logic for Shipping Modal --}}
                        @vite(['resources/css/personal-data.css', 'resources/ts/domain/personal-data.ts'])
                        <script>
                            window.personalDataConfig = {
                                csrfToken: '{{ csrf_token() }}',
                                translations: {
                                    shipping_add_new: "{{ __('user_personal_data.shipping.add_new') }}",
                                    shipping_edit_address: "{{ __('user_personal_data.shipping.edit_address') }}"
                                }
                            };
                            };

                            // MODAL GLOBAL HANDLERS - Nuclear Option
                            window.closeShippingModal = function() {
                                const modal = document.getElementById('shipping-address-modal');
                                if (modal) modal.classList.add('hidden');
                            };

                            window.saveShippingAddress = function(e) {
                                console.log('🔘 Save Address Clicked');
                                if (e) e.preventDefault();

                                const form = document.getElementById('shipping-address-form');
                                if (!form) return;

                                const btn = e.target;
                                const originalText = btn.innerHTML;
                                btn.disabled = true;
                                btn.innerHTML = 'Salvataggio...';

                                const formData = new FormData(form);
                                const url = btn.getAttribute('data-url') || form.getAttribute('action') ||
                                    '{{ route('user.domains.personal-data.shipping-address.store') }}';

                                fetch(url, {
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                            'Accept': 'application/json'
                                        },
                                        body: formData
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.success || data.id) {
                                            alert('Indirizzo salvato con successo!');
                                            window.location.reload();
                                        } else {
                                            alert('Errore: ' + (data.message || 'Errore sconosciuto'));
                                            btn.disabled = false;
                                            btn.innerHTML = originalText;
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error:', error);
                                        alert('Errore di comunicazione col server.');
                                        btn.disabled = false;
                                        btn.innerHTML = originalText;
                                    });
                            };
                        </script>
                    @endpush
                    <x-personal-data.shipping-address-modal :countries="$availableCountries ?? []" />

                    {{-- Payment Method --}}
                    <div class="mb-6">
                        <label class="mb-3 block text-sm font-medium text-gray-700">
                            {{ __('mint.payment.payment_method_label') }}
                        </label>
                        <div class="space-y-3">
                            @php
                                $stripeMerchantAvailable = $stripeMerchantAvailable ?? false;
                                $stripeMerchantError =
                                    $stripeMerchantError ?? __('payment.errors.merchant_account_incomplete');
                            @endphp
                            <label
                                class="{{ $stripeMerchantAvailable ? 'cursor-pointer border-gray-300 hover:bg-gray-50' : 'cursor-not-allowed border-red-300 bg-red-50 opacity-60' }} flex items-center rounded-lg border p-3 transition-colors">
                                <input type="radio" name="payment_method" value="stripe"
                                    {{ $selectedPaymentMethod === 'stripe' && $stripeMerchantAvailable ? 'checked' : '' }}
                                    {{ !$stripeMerchantAvailable ? 'disabled' : '' }}
                                    class="{{ !$stripeMerchantAvailable ? 'cursor-not-allowed opacity-50' : '' }} h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500">
                                <div class="ml-3 flex-1">
                                    <span
                                        class="{{ $stripeMerchantAvailable ? 'text-gray-900' : 'text-red-700' }} text-sm font-medium">
                                        💳 {{ __('mint.payment.credit_card') }}
                                    </span>
                                    @if (!$stripeMerchantAvailable)
                                        <p class="mt-1 text-xs text-red-600">
                                            ⚠️ {{ $stripeMerchantError }}
                                        </p>
                                    @endif
                                </div>
                            </label>
                            @php
                                $paypalAvailable = $paypalAvailable ?? false;
                                $paypalError = $paypalError ?? __('payment.errors.paypal_not_implemented');
                            @endphp
                            <label
                                class="{{ $paypalAvailable ? 'cursor-pointer border-gray-300 hover:bg-gray-50' : 'cursor-not-allowed border-red-300 bg-red-50 opacity-60' }} flex items-center rounded-lg border p-3 transition-colors">
                                <input type="radio" name="payment_method" value="paypal"
                                    {{ $selectedPaymentMethod === 'paypal' && $paypalAvailable ? 'checked' : '' }}
                                    {{ !$paypalAvailable ? 'disabled' : '' }}
                                    class="{{ !$paypalAvailable ? 'cursor-not-allowed opacity-50' : '' }} h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500">
                                <div class="ml-3 flex-1">
                                    <span
                                        class="{{ $paypalAvailable ? 'text-gray-900' : 'text-red-700' }} text-sm font-medium">
                                        💙 {{ __('mint.payment.paypal') }}
                                    </span>
                                    @if (!$paypalAvailable)
                                        <p class="mt-1 text-xs text-red-600">
                                            ⚠️ {{ $paypalError }}
                                        </p>
                                    @endif
                                </div>
                            </label>
                            @if ($showEgiliOption)
                                <label
                                    class="{{ $canPayWithEgili ? 'cursor-pointer hover:bg-emerald-50' : 'cursor-not-allowed bg-emerald-50/60' }} flex items-start rounded-lg border border-emerald-400/60 p-3 transition-colors">
                                    <div class="pt-1">
                                        <input type="radio" name="payment_method" value="egili"
                                            {{ $selectedPaymentMethod === 'egili' && $canPayWithEgili ? 'checked' : '' }}
                                            {{ $canPayWithEgili ? '' : 'disabled' }}
                                            class="h-4 w-4 border-emerald-400 text-emerald-600 focus:ring-emerald-600">
                                    </div>
                                    <div class="ml-3 space-y-1 text-sm">
                                        <p class="font-semibold text-emerald-900">
                                            🪙 {{ __('mint.payment.payment_method_egili') }}
                                        </p>
                                        <p class="text-xs text-emerald-700">
                                            {{ __('mint.payment.egili_balance_label', ['balance' => number_format($egiliBalance)]) }}
                                        </p>
                                        <p class="text-xs text-emerald-700">
                                            {{ __('mint.payment.egili_required_label', ['required' => number_format($requiredEgili)]) }}
                                        </p>
                                        @unless ($canPayWithEgili)
                                            <p class="text-xs font-semibold text-red-600">
                                                {{ __('mint.payment.egili_insufficient') }}
                                            </p>
                                        @endunless
                                    </div>
                                </label>
                            @endif
                        </div>
                    </div>

                    {{-- Wallet Destinazione (Opzionale) --}}
                    <div class="mb-6">
                        <div class="mb-3 flex items-center">
                            <input type="checkbox" id="has_wallet_toggle"
                                class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <label for="has_wallet_toggle" class="ml-2 text-sm font-medium text-gray-700">
                                {{ __('mint.buyer_info.has_wallet') }}
                            </label>
                        </div>

                        <div id="wallet_input_container" class="hidden">
                            <label class="mb-2 block text-sm font-medium text-gray-700">
                                {{ __('mint.buyer_info.wallet_label') }}
                            </label>
                            <input type="text" name="buyer_wallet" id="buyer_wallet"
                                placeholder="{{ __('mint.buyer_info.wallet_placeholder') }}"
                                class="w-full rounded-md border border-gray-300 px-3 py-2 font-mono text-sm focus:border-transparent focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p class="mt-1 text-xs text-gray-500">
                                {{ __('mint.buyer_info.wallet_help') }}
                            </p>
                        </div>
                    </div>

                    {{-- Nickname Co-Creator (pre-filled) - Contrasto migliorato --}}
                    <div class="mb-6">
                        <label for="co_creator_display_name" class="mb-2 block text-sm font-medium text-gray-700">
                            {{ __('mint.payment.co_creator_name_label') }}
                            <span class="text-xs text-gray-500">({{ __('mint.payment.optional') }})</span>
                        </label>
                        <input type="text" name="co_creator_display_name" id="co_creator_display_name"
                            value="{{ Auth::user()->name }}" placeholder="{{ Auth::user()->name }}"
                            pattern="^[a-zA-Z0-9\s.''\-]+$" maxlength="100"
                            class="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 placeholder-gray-600 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="mt-1 text-xs text-gray-500">
                            {{ __('mint.payment.co_creator_name_help') }}
                        </p>
                        <div class="mt-2 border-l-4 border-yellow-500 bg-yellow-50 p-2">
                            <p class="text-xs text-yellow-800">
                                ⚠️ {{ __('mint.payment.co_creator_name_warning') }}
                            </p>
                        </div>
                    </div>

                    {{-- Total - Contrasto migliorato --}}
                    <div class="mb-6 border-t pt-4">
                        <div class="flex items-center justify-between text-lg font-semibold">
                            <span class="text-gray-900">{{ __('mint.payment.total_label') }}</span>
                            <span class="text-2xl font-bold text-green-700">
                                €{{ number_format($paymentAmountEur, 2) }}
                            </span>
                        </div>
                        @if ($showEgiliOption)
                            <div class="mt-3 rounded-lg bg-emerald-50 p-3 text-xs text-emerald-800">
                                <p class="font-semibold">
                                    {{ __('mint.payment.egili_summary_title') }}
                                </p>
                                <p>
                                    {{ __('mint.payment.egili_summary', ['required' => number_format($requiredEgili)]) }}
                                </p>
                                <p>
                                    {{ __('mint.payment.egili_balance_label', ['balance' => number_format($egiliBalance)]) }}
                                </p>
                                @unless ($canPayWithEgili)
                                    <p class="font-semibold text-red-600">
                                        {{ __('mint.payment.egili_insufficient') }}
                                    </p>
                                @endunless
                            </div>
                        @endif
                    </div>

                    {{-- Submit Button --}}
                    <button type="button" id="submit-mint-btn" onclick="window.submitMintForm(event)"
                        class="w-full rounded-lg bg-blue-600 px-6 py-3 font-bold text-white transition-all hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        {{ __('mint.payment.submit_button') }}
                    </button>

                </form>

            </div>
        </div>

    </div>

    {{-- JavaScript --}}
    {{-- JavaScript --}}

    <script>
        // Toggle wallet input
        document.getElementById('has_wallet_toggle').addEventListener('change', function(e) {
            const container = document.getElementById('wallet_input_container');
            const input = document.getElementById('buyer_wallet');

            if (e.target.checked) {
                container.classList.remove('hidden');
                input.required = true;
            } else {
                container.classList.add('hidden');
                input.required = false;
                input.value = '';
            }
        });

        // Co-creator name validation
        const coCreatorInput = document.getElementById('co_creator_display_name');
        coCreatorInput.addEventListener('input', function(e) {
            const pattern = /^[a-zA-Z0-9\s.\'\-]+$/;
            if (this.value && !pattern.test(this.value)) {
                this.classList.add('border-red-500');
                this.setCustomValidity('{{ __('mint.payment.co_creator_name_invalid') }}');
            } else {
                this.classList.remove('border-red-500');
                this.setCustomValidity('');
            }
        });

        // Check if page was reloaded after error (flash messages present)
        document.addEventListener('DOMContentLoaded', function() {
            // Se ci sono errori flash, chiudi modale SweetAlert
            const hasErrors = document.querySelector('.alert-danger') ||
                document.querySelector('[role="alert"]') ||
                @json($errors->any());

            if (hasErrors && window.Swal) {
                Swal.close();
            }
        });

        // Form submission con MODALE DI PROGRESS
        // Polling helper to ensure elements exist before binding
        function ensureElement(selector, callback, maxAttempts = 20) {
            let attempts = 0;
            const interval = setInterval(() => {
                const element = document.querySelector(selector);
                if (element) {
                    clearInterval(interval);
                    console.log(`✅ Element found: ${selector}`);
                    callback(element);
                } else {
                    attempts++;
                    if (attempts >= maxAttempts) {
                        clearInterval(interval);
                        console.warn(`❌ Element not found after ${maxAttempts} attempts: ${selector}`);
                    }
                }
            }, 250);
        }

        // Form submission logic
            // DIRECT GLOBAL HANDLER - Nuclear Option for reliability
            window.submitMintForm = function(e) {
                console.log('🔘 Submit Button Clicked (Direct Handler)');
                if (e) e.preventDefault();

                const form = document.getElementById('mint-payment-form');
                const btn = document.getElementById('submit-mint-btn');

                if (!form) {
                    console.error('❌ Form mint-payment-form not found');
                    alert('Errore tecnico: Modulo non trovato. Ricarica la pagina.');
                    return;
                }

                try {
                     // Check Gold Bar Timer
                    const timerElement = document.getElementById('gold-bar-timer');
                    if (timerElement) {
                        const validUntil = new Date(timerElement.dataset.validUntil);
                        if (new Date() > validUntil) {
                            console.warn('Timer expired during submit');
                            const expiredModal = document.getElementById('gold-bar-expired-modal');
                            if(expiredModal) {
                                expiredModal.classList.remove('hidden');
                                expiredModal.classList.add('flex');
                            }
                            return;
                        }
                    }

                    // UI Feedback
                    if (btn) {
                        btn.disabled = true;
                        btn.innerHTML = '<svg class="inline w-5 h-5 mr-2 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Elaborazione...';
                    }

                    // SweetAlert Logic
                    if (window.Swal) {
                         Swal.fire({
                            title: '⏳ Elaborazione Mint',
                            html: '<div class="space-y-4"><p class="text-lg">Caricamento...</p><p class="text-sm text-gray-500">Non chiudere la finestra.</p></div>',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            didOpen: () => {
                                console.log('🚀 Submitting form via Swal...');
                                form.submit();
                            }
                        });
                    } else {
                        console.log('🚀 Submitting form directly...');
                        form.submit();
                    }
                } catch (err) {
                    console.error('Critical Error in Submit Handler:', err);
                    alert('Errore durante l\'invio. Riprovare.');
                    if(btn) btn.disabled = false;
                }
            };

        // Gold Bar Timer Logic
        ensureElement('#gold-bar-timer', (goldBarTimer) => {
            const validUntil = new Date(goldBarTimer.dataset.validUntil);
            const countdownElement = document.getElementById('gold-timer-countdown');

            function updateGoldBarTimer() {
                const now = new Date();
                const diff = validUntil - now;

                if (diff <= 0) {
                    if (countdownElement) countdownElement.textContent = '00:00';
                    goldBarTimer.classList.add('border-red-400', 'bg-red-100');

                    const expiredModal = document.getElementById('gold-bar-expired-modal');
                    if (expiredModal) {
                        expiredModal.classList.remove('hidden');
                        expiredModal.classList.add('flex');
                    }

                    const btn = document.getElementById('submit-mint-btn');
                    if (btn) btn.disabled = true;
                    return;
                }

                const minutes = Math.floor(diff / 60000);
                const seconds = Math.floor((diff % 60000) / 1000);
                if (countdownElement) {
                    countdownElement.textContent =
                        `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
                    if (diff < 120000) countdownElement.classList.add('text-red-600', 'font-bold');
                }
                setTimeout(updateGoldBarTimer, 1000);
            }
            updateGoldBarTimer();
        });
    </script>

</x-platform-layout>
