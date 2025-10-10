{{-- resources/views/mint/checkout.blade.php --}}
<x-platform-layout :title="__('mint.page_title', ['title' => $egi->title])">
    <div class="container px-4 py-8 mx-auto">
        <div class="max-w-4xl mx-auto">
            {{-- Header --}}
            <div class="mb-8">
                <h1 class="mb-2 text-3xl font-bold text-gray-900">
                    {{ __('mint.header_title') }}
                </h1>
                <p class="text-gray-600">
                    {{ __('mint.header_description') }}
                </p>
            </div>

            <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
                {{-- EGI Preview --}}
                <div class="space-y-6">
                    <div class="p-6 bg-white rounded-lg shadow-lg">
                        <h2 class="mb-4 text-xl font-semibold">{{ __('mint.egi_preview.title') }}</h2>

                        {{-- EGI Card Preview --}}
                        <div class="mb-4 overflow-hidden rounded-lg aspect-square">
                            <img src="{{ $egi->main_image_url }}" alt="{{ $egi->title }}"
                                class="object-cover w-full h-full">
                        </div>

                        <h3 class="text-lg font-semibold text-gray-900">{{ $egi->title }}</h3>
                        <p class="mb-2 text-sm text-gray-600">
                            {{ __('mint.egi_preview.creator_by', ['name' => $egi->user->name]) }}</p>

                        @if ($egi->description)
                            <p class="text-sm text-gray-700">{{ Str::limit($egi->description, 150) }}</p>
                        @endif
                    </div>

                    {{-- Blockchain Info --}}
                    <div class="p-6 rounded-lg bg-blue-50">
                        <h3 class="mb-3 text-lg font-semibold text-blue-900">{{ __('mint.blockchain_info.title') }}</h3>
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

                    {{-- Certificate of Authenticity (CoA) --}}
                    @if($egi->coa && $egi->coa->status === 'valid')
                        <div class="p-6 rounded-lg bg-amber-50">
                            <div class="flex items-center mb-3">
                                <svg class="w-6 h-6 mr-2 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                                <h3 class="text-lg font-semibold text-amber-900">{{ __('mint.coa.title') }}</h3>
                            </div>
                            <div class="space-y-3">
                                {{-- CoA Status Badge --}}
                                <div class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-800">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    {{ __('mint.coa.certified') }}
                                </div>

                                {{-- CoA Details --}}
                                <div class="space-y-2 text-sm">
                                    @if($egi->coa->serial)
                                        <div class="flex justify-between">
                                            <span class="text-amber-700">{{ __('mint.coa.certificate_number') }}</span>
                                            <span class="font-mono font-medium text-amber-900">{{ $egi->coa->serial }}</span>
                                        </div>
                                    @endif
                                    @if($egi->coa->issuer_name)
                                        <div class="flex justify-between">
                                            <span class="text-amber-700">{{ __('mint.coa.issuer') }}</span>
                                            <span class="font-medium text-amber-900">{{ Str::limit($egi->coa->issuer_name, 30) }}</span>
                                        </div>
                                    @endif
                                    @if($egi->coa->issued_at)
                                        <div class="flex justify-between">
                                            <span class="text-amber-700">{{ __('mint.coa.issue_date') }}</span>
                                            <span class="font-medium text-amber-900">{{ $egi->coa->issued_at->format('d/m/Y') }}</span>
                                        </div>
                                    @endif
                                    {{-- Note: authenticity_level does NOT exist in Coa model --}}
                                </div>

                                {{-- CoA Info Note --}}
                                <div class="p-3 mt-3 border rounded-md border-amber-200 bg-amber-100">
                                    <p class="text-xs text-amber-800">
                                        <svg class="inline w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                        </svg>
                                        {{ __('mint.coa.info_note') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Checkout Form --}}
                <div class="space-y-6">
                    <div class="p-6 bg-white rounded-lg shadow-lg">
                        <h2 class="mb-4 text-xl font-semibold">{{ __('mint.payment.title') }}</h2>

                        {{-- Reservation Summary (if minting after reservation) --}}
                        @if ($reservation)
                            <div class="p-4 mb-6 rounded-lg bg-green-50">
                                <h3 class="mb-2 font-semibold text-green-900">
                                    {{ __('mint.payment.winning_reservation') }}
                                </h3>
                                <div class="space-y-1 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-green-700">{{ __('mint.payment.your_offer') }}</span>
                                        <span
                                            class="font-bold text-green-900">€{{ number_format($reservation->amount_eur ?? 0, 2) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-green-700">{{ __('mint.payment.reservation_date') }}</span>
                                        <span
                                            class="text-green-900">{{ $reservation->created_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                </div>
                            </div>
                        @else
                            {{-- Direct Mint Price --}}
                            <div class="p-4 mb-6 rounded-lg bg-blue-50">
                                <h3 class="mb-2 font-semibold text-blue-900">
                                    {{ __('mint.payment.direct_mint_price') }}
                                </h3>
                                <div class="space-y-1 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-blue-700">{{ __('mint.payment.base_price') }}</span>
                                        <span
                                            class="font-bold text-blue-900">€{{ number_format($egi->price ?? 0, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Payment Form --}}
                        <form id="mint-form"
                            action="{{ $reservation ? route('mint.process') : route('egi.mint-direct.process', $egi->id) }}"
                            method="POST" class="space-y-4">
                            @csrf
                            <input type="hidden" name="egi_id" value="{{ $egi->id }}">
                            @if ($reservation)
                                <input type="hidden" name="reservation_id" value="{{ $reservation->id }}">
                            @endif

                            {{-- Payment Method --}}
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-700">
                                    {{ __('mint.payment.payment_method_label') }}
                                </label>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input type="radio" name="payment_method" value="stripe" checked
                                            class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                        <span
                                            class="ml-2 text-sm text-gray-900">{{ __('mint.payment.credit_card') }}</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" name="payment_method" value="paypal"
                                            class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                        <span class="ml-2 text-sm text-gray-900">{{ __('mint.payment.paypal') }}</span>
                                    </label>
                                </div>
                            </div>

                            {{-- Optional Wallet Address --}}
                            <div>
                                <label for="buyer_wallet" class="block mb-2 text-sm font-medium text-gray-700">
                                    {{ __('mint.payment.wallet_label') }}
                                </label>
                                <input type="text" id="buyer_wallet" name="buyer_wallet"
                                    placeholder="{{ __('mint.payment.wallet_placeholder') }}"
                                    class="w-full px-3 py-2 font-mono text-sm border border-gray-300 rounded-md focus:border-transparent focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <p class="mt-1 text-xs text-gray-500">
                                    {{ __('mint.payment.wallet_help') }}
                                </p>
                            </div>

                            {{-- AREA 5.5.1: Co-Creator Display Name (IMMUTABLE AFTER MINT) --}}
                            <div>
                                <label for="co_creator_display_name"
                                    class="block mb-2 text-sm font-medium text-gray-700">
                                    {{ __('mint.payment.co_creator_name_label') }}
                                    <span class="text-xs text-gray-500">({{ __('mint.payment.optional') }})</span>
                                </label>
                                <input type="text" id="co_creator_display_name" name="co_creator_display_name"
                                    value="{{ old('co_creator_display_name', Auth::user()->name) }}"
                                    placeholder="{{ Auth::user()->name }}" maxlength="100"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:border-transparent focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    pattern="[a-zA-Z0-9\s.\'\-]+"
                                    title="{{ __('mint.payment.co_creator_name_pattern') }}">
                                <div class="flex items-start justify-between mt-1">
                                    <p class="text-xs text-gray-500">
                                        {{ __('mint.payment.co_creator_name_help') }}
                                    </p>
                                    <span id="char-counter" class="text-xs text-gray-400">
                                        <span id="char-count">{{ strlen(Auth::user()->name) }}</span>/100
                                    </span>
                                </div>
                                <div class="p-3 mt-2 border rounded-md border-amber-200 bg-amber-50">
                                    <div class="flex">
                                        <svg class="flex-shrink-0 w-5 h-5 text-amber-500" fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <div class="ml-3">
                                            <p class="text-xs font-medium text-amber-800">
                                                {{ __('mint.payment.co_creator_name_warning') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                @error('co_creator_display_name')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Total --}}
                            <div class="pt-4 border-t">
                                <div class="flex items-center justify-between text-lg font-semibold">
                                    <span>{{ __('mint.payment.total_label') }}</span>
                                    <span class="text-green-600">
                                        €{{ number_format($reservation ? $reservation->amount_eur : $egi->price, 2) }}
                                    </span>
                                </div>
                            </div>

                            {{-- Submit Button --}}
                            <button type="submit"
                                class="w-full transform rounded-lg bg-gradient-to-r from-green-500 to-emerald-600 px-4 py-3 font-medium text-white transition-all duration-200 hover:scale-[1.02] hover:from-green-600 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                <span class="flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ __('mint.payment.submit_button') }}
                                </span>
                            </button>
                        </form>
                    </div>

                    {{-- Info MiCA Compliance --}}
                    <div class="p-4 rounded-lg bg-yellow-50">
                        <h3 class="mb-2 font-semibold text-yellow-900">{{ __('mint.compliance.mica_title') }}</h3>
                        <p class="text-sm text-yellow-800">
                            {{ __('mint.compliance.mica_description') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Loading Modal --}}
    {{-- Loading Modal --}}
    <div id="loading-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 transition-opacity bg-black bg-opacity-50"></div>
            <div class="relative w-full max-w-md p-6 transition-all transform bg-white rounded-lg shadow-xl">
                <div class="text-center">
                    <div class="flex items-center justify-center w-12 h-12 mx-auto bg-blue-100 rounded-full">
                        <svg class="w-6 h-6 text-blue-600 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0a12 12 0 00-12 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                    </div>
                    <div class="mt-3">
                        <h3 class="text-lg font-medium text-gray-900">{{ __('mint.modal.processing_title') }}</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">{{ __('mint.modal.processing_message') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            const form = document.getElementById('mint-form');

            // AREA 5.5.1: Character counter for Co-Creator Display Name
            const coCreatorNameInput = document.getElementById('co_creator_display_name');
            const charCountSpan = document.getElementById('char-count');

            if (coCreatorNameInput && charCountSpan) {
                coCreatorNameInput.addEventListener('input', function() {
                    const length = this.value.length;
                    charCountSpan.textContent = length;

                    // Visual feedback quando si avvicina al limite
                    const charCounter = document.getElementById('char-counter');
                    if (length > 90) {
                        charCounter.classList.add('text-red-600', 'font-semibold');
                        charCounter.classList.remove('text-gray-400');
                    } else if (length > 75) {
                        charCounter.classList.add('text-amber-600', 'font-medium');
                        charCounter.classList.remove('text-gray-400', 'text-red-600');
                    } else {
                        charCounter.classList.remove('text-red-600', 'text-amber-600', 'font-semibold', 'font-medium');
                        charCounter.classList.add('text-gray-400');
                    }
                });

                // Validazione pattern in tempo reale
                coCreatorNameInput.addEventListener('blur', function() {
                    const pattern = /^[a-zA-Z0-9\s.\'\-]+$/;
                    if (this.value && !pattern.test(this.value)) {
                        this.classList.add('border-red-500');
                        this.setCustomValidity('{{ __('mint.payment.co_creator_name_invalid') }}');
                    } else {
                        this.classList.remove('border-red-500');
                        this.setCustomValidity('');
                    }
                });
            }

            form.addEventListener('submit', async (e) => {
                e.preventDefault();

                // Show loading modal
                document.getElementById('loading-modal').classList.remove('hidden');

                try {
                    const formData = new FormData(form);
                    const response = await fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content'),
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) {
                        const errorText = await response.text();
                        console.error('Server response:', errorText);
                        throw new Error(`HTTP ${response.status}: ${errorText.substring(0, 100)}`);
                    }

                    const result = await response.json();

                    if (result.success) {
                        // Redirect to success page or reload
                        window.location.href = result.redirect || window.location.href + '?success=1';
                    } else {
                        throw new Error(result.message || result.error || '{{ __('mint.js.default_error') }}');
                    }
                } catch (error) {
                    console.error('Mint error:', error);
                    document.getElementById('loading-modal').classList.add('hidden');
                    alert('{{ __('mint.js.error_prefix') }}' + error.message);
                }
            });
        </script>
    @endpush
</x-platform-layout>
