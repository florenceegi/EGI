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
                </div>

                {{-- Checkout Form --}}
                <div class="space-y-6">
                    <div class="p-6 bg-white rounded-lg shadow-lg">
                        <h2 class="mb-4 text-xl font-semibold">{{ __('mint.payment.title') }}</h2>

                        {{-- Reservation Summary --}}
                        <div class="p-4 mb-6 rounded-lg bg-green-50">
                            <h3 class="mb-2 font-semibold text-green-900">{{ __('mint.payment.winning_reservation') }}
                            </h3>
                            <div class="space-y-1 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-green-700">{{ __('mint.payment.your_offer') }}</span>
                                    <span
                                        class="font-bold text-green-900">€{{ number_format($reservation->offer_amount_fiat ?? $reservation->amount_eur, 2) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-green-700">{{ __('mint.payment.reservation_date') }}</span>
                                    <span
                                        class="text-green-900">{{ $reservation->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Payment Form --}}
                        <form id="mint-form" class="space-y-4">
                            @csrf
                            <input type="hidden" name="egi_id" value="{{ $egi->id }}">
                            <input type="hidden" name="reservation_id" value="{{ $reservation->id }}">

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

                            {{-- Total --}}
                            <div class="pt-4 border-t">
                                <div class="flex items-center justify-between text-lg font-semibold">
                                    <span>{{ __('mint.payment.total_label') }}</span>
                                    <span
                                        class="text-green-600">€{{ number_format($reservation->offer_amount_fiat ?? $reservation->amount_eur, 2) }}</span>
                                </div>
                            </div>

                            {{-- Submit Button --}}
                            <button type="submit"
                                class="w-full transform rounded-lg bg-gradient-to-r from-green-500 to-emerald-600 px-4 py-3 font-medium text-white transition-all duration-200 hover:scale-[1.02] hover:from-green-600 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                <span class="flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                        <h3 class="mb-2 font-semibold text-yellow-900">⚖️ MiCA Compliance</h3>
                        <p class="text-sm text-yellow-800">
                            Questo processo è completamente MiCA-SAFE. Paghiamo in FIAT tramite PSP autorizzati,
                            mintiamo l'NFT per tuo conto, e gestiamo solo la custodia temporanea se necessario.
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
                                'content')
                        }
                    });

                    const result = await response.json();

                    if (result.success) {
                        // Redirect to success page or reload
                        window.location.href = result.redirect || window.location.href + '?success=1';
                    } else {
                        throw new Error(result.message || '{{ __('mint.js.default_error') }}');
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
