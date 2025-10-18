{{--
    MINT PAYMENT FORM - PAGINA 1
    🎯 Purpose: Form pagamento FIAT per mint EGI
    📍 Route: GET /mint/payment/{egiId}?reservation_id={id}
    ➡️ Submit: POST /mint/process → Redirect a mint.blade.php
--}}
<x-platform-layout :title="__('mint.page_title', ['title' => $egi->title])">
    <div class="container mx-auto max-w-4xl px-4 py-8">

        {{-- Header --}}
        <div class="mb-8 text-center">
            <h1 class="mb-2 text-3xl font-bold text-gray-900">
                {{ __('mint.header_title') }}
            </h1>
            <p class="text-gray-600">
                {{ __('mint.header_description') }}
            </p>
        </div>

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">

            {{-- COLONNA 1: EGI Preview --}}
            <div class="space-y-6">

                {{-- Immagine EGI --}}
                <div class="overflow-hidden rounded-lg bg-white shadow-lg">
                    @if ($egi->utility && $egi->utility->getFirstMediaUrl('utility'))
                        <img src="{{ $egi->utility->getFirstMediaUrl('utility') }}" alt="{{ $egi->title }}"
                            class="h-64 w-full object-cover">
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

                {{-- Blockchain Info --}}
                <div class="rounded-lg bg-blue-50 p-6">
                    <h3 class="mb-3 text-lg font-semibold text-blue-900">
                        {{ __('mint.blockchain_info.title') }}
                    </h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-blue-700">{{ __('mint.blockchain_info.network') }}</span>
                            <span class="font-medium">{{ __('mint.blockchain_info.network_value') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-blue-700">{{ __('mint.blockchain_info.token_type') }}</span>
                            <span class="font-medium">{{ __('mint.blockchain_info.token_type_value') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-blue-700">{{ __('mint.blockchain_info.supply') }}</span>
                            <span class="font-medium">{{ __('mint.blockchain_info.supply_value') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Prezzo Originale --}}
                <div class="rounded-lg bg-green-50 p-6">
                    <h3 class="mb-2 font-semibold text-green-900">
                        {{ __('mint.payment.price_label') }}
                    </h3>
                    <div class="text-3xl font-bold text-green-600">
                        €{{ number_format($egi->price, 2) }}
                    </div>
                    @if ($reservation && $reservation->amount_eur < $egi->price)
                        <p class="mt-2 text-sm text-green-700">
                            ✅ {{ __('mint.payment.winning_reservation') }}:
                            €{{ number_format($reservation->amount_eur, 2) }}
                        </p>
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
                <form id="mint-payment-form" action="{{ route('mint.process') }}" method="POST"
                    class="rounded-lg bg-white p-6 shadow-lg">
                    @csrf

                    <input type="hidden" name="egi_id" value="{{ $egi->id }}">
                    @if ($reservation)
                        <input type="hidden" name="reservation_id" value="{{ $reservation->id }}">
                    @endif

                    {{-- Payment Method --}}
                    <div class="mb-6">
                        <label class="mb-3 block text-sm font-medium text-gray-700">
                            {{ __('mint.payment.payment_method_label') }}
                        </label>
                        <div class="space-y-3">
                            <label
                                class="flex cursor-pointer items-center rounded-lg border border-gray-300 p-3 transition-colors hover:bg-gray-50">
                                <input type="radio" name="payment_method" value="stripe" checked
                                    class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-3 text-sm font-medium text-gray-900">
                                    💳 {{ __('mint.payment.credit_card') }}
                                </span>
                            </label>
                            <label
                                class="flex cursor-pointer items-center rounded-lg border border-gray-300 p-3 transition-colors hover:bg-gray-50">
                                <input type="radio" name="payment_method" value="paypal"
                                    class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-3 text-sm font-medium text-gray-900">
                                    💙 {{ __('mint.payment.paypal') }}
                                </span>
                            </label>
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

                    {{-- Nickname Co-Creator (pre-filled) --}}
                    <div class="mb-6">
                        <label for="co_creator_display_name" class="mb-2 block text-sm font-medium text-gray-700">
                            {{ __('mint.payment.co_creator_name_label') }}
                            <span class="text-xs text-gray-500">({{ __('mint.payment.optional') }})</span>
                        </label>
                        <input type="text" name="co_creator_display_name" id="co_creator_display_name"
                            value="{{ Auth::user()->name }}" placeholder="{{ Auth::user()->name }}"
                            pattern="^[a-zA-Z0-9\s.\'\-]+$" maxlength="100"
                            class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="mt-1 text-xs text-gray-500">
                            {{ __('mint.payment.co_creator_name_help') }}
                        </p>
                        <div class="mt-2 border-l-4 border-yellow-500 bg-yellow-50 p-2">
                            <p class="text-xs text-yellow-800">
                                ⚠️ {{ __('mint.payment.co_creator_name_warning') }}
                            </p>
                        </div>
                    </div>

                    {{-- Total --}}
                    <div class="mb-6 border-t pt-4">
                        <div class="flex items-center justify-between text-lg font-semibold">
                            <span>{{ __('mint.payment.total_label') }}</span>
                            <span class="text-green-600">
                                €{{ number_format($reservation ? $reservation->amount_eur : $egi->price, 2) }}
                            </span>
                        </div>
                    </div>

                    {{-- Submit Button --}}
                    <button type="submit" id="submit-mint-btn"
                        class="w-full rounded-lg bg-blue-600 px-6 py-3 font-bold text-white transition-all hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        {{ __('mint.payment.submit_button') }}
                    </button>

                </form>

            </div>
        </div>

    </div>

    {{-- JavaScript --}}
    @push('scripts')
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

                        // Form submission con MODALE DI PROGRESS
            document.getElementById('mint-payment-form').addEventListener('submit', function(e) {
                e.preventDefault(); // Previeni submit default
                
                const form = this;
                const btn = document.getElementById('submit-mint-btn');
                
                // Disabilita button e mostra spinner
                btn.disabled = true;
                btn.innerHTML =
                    '<svg class="inline w-5 h-5 mr-2 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> {{ __('mint.payment.processing') }}';

                // Mostra modale di progress
                if (window.Swal) {
                    Swal.fire({
                        title: '⏳ Elaborazione Mint',
                        html: `
                            <div class="space-y-4">
                                <div class="flex items-center justify-center">
                                    <svg class="w-16 h-16 animate-spin text-blue-600" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </div>
                                <p class="text-gray-700">Stiamo elaborando il tuo pagamento e preparando il mint sulla blockchain Algorand.</p>
                                <p class="text-sm text-gray-500">⚠️ Non chiudere questa finestra</p>
                            </div>
                        `,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            // Submit form DOPO aver mostrato la modale
                            form.submit();
                        }
                    });
                } else {
                    // Se SweetAlert non disponibile, submit normale
                    form.submit();
                }
            });
        </script>
    @endpush

</x-platform-layout>
```
        </script>
    @endpush

</x-platform-layout>
