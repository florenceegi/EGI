<x-platform-layout>
    <div class="container mx-auto max-w-4xl px-4 py-8">
        <div class="mb-8">
            <h1 class="mb-2 text-3xl font-bold text-white">{{ __('Collection Payment Settings') }}</h1>
            <p class="text-primary-400 text-xl">{{ $collection->collection_name }}</p>
            <p class="mt-2 text-gray-400">{{ __('Override default payment methods for this specific collection') }}</p>
        </div>

        <div class="space-y-6">
            @foreach ($availableMethods as $methodKey => $methodInfo)
                <div class="rounded-xl border border-gray-700/50 bg-gray-800/50 p-6" data-method="{{ $methodKey }}">
                    <div class="mb-4 flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="bg-primary-500/20 flex h-12 w-12 items-center justify-center rounded-full">
                                @if ($methodInfo['icon'] === 'credit-card')
                                    <svg class="text-primary-400 h-6 w-6" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                                        </path>
                                    </svg>
                                @elseif($methodInfo['icon'] === 'coins')
                                    <svg class="text-primary-400 h-6 w-6" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                        </path>
                                    </svg>
                                @else
                                    <svg class="text-primary-400 h-6 w-6" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                        </path>
                                    </svg>
                                @endif
                            </div>
                            <div>
                                <h3 class="text-xl font-semibold text-white">{{ $methodInfo['name'] }}</h3>
                                <p class="text-sm text-gray-400">{{ $methodInfo['description'] }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            @php
                                $collectionMethod = $collectionMethods[$methodKey] ?? null;
                                $isEnabled = $collectionMethod?->is_enabled ?? false;
                                $isDefault = $collectionMethod?->is_default ?? false;
                            @endphp

                            @if ($isDefault)
                                <span
                                    class="rounded-full bg-green-500/20 px-3 py-1 text-xs text-green-400">{{ __('Default') }}</span>
                            @endif

                            <button type="button" onclick="togglePaymentMethod('{{ $methodKey }}')"
                                class="{{ $isEnabled ? 'bg-primary-500' : 'bg-gray-600' }} relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                                data-toggle="{{ $methodKey }}">
                                <span class="sr-only">{{ __('Toggle') }}</span>
                                <span
                                    class="{{ $isEnabled ? 'translate-x-5' : 'translate-x-0' }} pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                            </button>
                        </div>
                    </div>

                    {{-- Method-specific options --}}
                    @if ($methodKey === 'stripe' && isset($methodInfo['requires_connect']))
                        <div class="mt-4 border-t border-gray-700/50 pt-4">
                            @if ($stripeConnected)
                                <div class="flex items-center gap-2 text-green-400">
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    <span>{{ __('Stripe account connected (Creator Level)') }}</span>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">
                                    {{ __('Collection inherits your main Stripe Connect account.') }}</p>
                            @else
                                <div class="text-sm text-yellow-500">
                                    {{ __('Please connect your Stripe account in main settings first.') }}
                                </div>
                            @endif
                        </div>
                    @endif

                    @if ($methodKey === 'bank_transfer' && isset($methodInfo['requires_iban']))
                        <div
                            class="bank-config-section {{ $isEnabled ? '' : 'hidden' }} mt-4 border-t border-gray-700/50 pt-4">
                            <form id="bank-config-form" class="space-y-4">
                                @csrf
                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                    <div>
                                        <label for="iban" class="mb-1 block text-sm font-medium text-gray-300">IBAN
                                            *</label>
                                        <input type="text" id="iban" name="iban"
                                            value="{{ $collectionMethod?->getIban() }}"
                                            class="focus:border-primary-500 focus:ring-primary-500 w-full rounded-lg border border-gray-600 bg-gray-700/50 px-4 py-2 text-white placeholder-gray-400 focus:ring-1"
                                            placeholder="IT60X0542811101000000123456">
                                    </div>
                                    <div>
                                        <label for="bic"
                                            class="mb-1 block text-sm font-medium text-gray-300">BIC/SWIFT</label>
                                        <input type="text" id="bic" name="bic"
                                            value="{{ $collectionMethod?->getBic() }}"
                                            class="focus:border-primary-500 focus:ring-primary-500 w-full rounded-lg border border-gray-600 bg-gray-700/50 px-4 py-2 text-white placeholder-gray-400 focus:ring-1"
                                            placeholder="BPPIITRRXXX">
                                    </div>
                                </div>
                                <div>
                                    <label for="holder"
                                        class="mb-1 block text-sm font-medium text-gray-300">{{ __('Account Holder') }}
                                        *</label>
                                    <input type="text" id="holder" name="holder"
                                        value="{{ $collectionMethod?->getHolder() }}"
                                        class="focus:border-primary-500 focus:ring-primary-500 w-full rounded-lg border border-gray-600 bg-gray-700/50 px-4 py-2 text-white placeholder-gray-400 focus:ring-1"
                                        placeholder="{{ __('Full name as on bank account') }}">
                                </div>
                                <button type="button" onclick="saveBankConfig()"
                                    class="bg-primary-500 hover:bg-primary-600 rounded-lg px-4 py-2 text-white transition">
                                    {{ __('payment.collection_bank_details_saved') }}
                                </button>
                            </form>
                        </div>
                    @endif

                    {{-- Set as default button --}}
                    @if ($isEnabled && !$isDefault)
                        <div class="mt-4 border-t border-gray-700/50 pt-4">
                            <button type="button" onclick="setDefaultMethod('{{ $methodKey }}')"
                                class="text-primary-400 hover:text-primary-300 text-sm transition">
                                {{ __('payment.collection_default_set', ['method' => $methodInfo['name']]) }}
                            </button>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    @push('scripts')
        <script>
            const COLLECTION_ID = {{ $collection->id }};

            async function togglePaymentMethod(method) {
                try {
                    const response = await fetch(`/collections/${COLLECTION_ID}/settings/payments/${method}/toggle`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });
                    const data = await response.json();

                    if (data.success) {
                        // Toggle visual state
                        const toggle = document.querySelector(`[data-toggle="${method}"]`);
                        const span = toggle.querySelector('span:last-child');

                        if (data.is_enabled) {
                            toggle.classList.remove('bg-gray-600');
                            toggle.classList.add('bg-primary-500');
                            span.classList.remove('translate-x-0');
                            span.classList.add('translate-x-5');
                        } else {
                            toggle.classList.remove('bg-primary-500');
                            toggle.classList.add('bg-gray-600');
                            span.classList.remove('translate-x-5');
                            span.classList.add('translate-x-0');
                        }

                        // Show/hide bank config section
                        if (method === 'bank_transfer') {
                            const section = document.querySelector('.bank-config-section');
                            section?.classList.toggle('hidden', !data.is_enabled);
                        }

                        showNotification(data.message, 'success');
                    } else {
                        showNotification(data.message, 'error');
                    }
                } catch (error) {
                    showNotification('{{ __('payment.generic_error') }}', 'error');
                }
            }

            async function setDefaultMethod(method) {
                try {
                    const response = await fetch(`/collections/${COLLECTION_ID}/settings/payments/${method}/default`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });
                    const data = await response.json();

                    if (data.success) {
                        showNotification(data.message, 'success');
                        location.reload();
                    } else {
                        showNotification(data.message, 'error');
                    }
                } catch (error) {
                    showNotification('{{ __('payment.generic_error') }}', 'error');
                }
            }

            async function saveBankConfig() {
                const form = document.getElementById('bank-config-form');
                const formData = new FormData(form);

                try {
                    const response = await fetch(`/collections/${COLLECTION_ID}/settings/payments/bank-transfer/config`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            iban: formData.get('iban'),
                            bic: formData.get('bic'),
                            holder: formData.get('holder')
                        })
                    });
                    const data = await response.json();

                    if (data.success) {
                        showNotification(data.message, 'success');
                    } else {
                        const errors = Object.values(data.errors || {}).flat().join(', ');
                        showNotification(errors || data.message, 'error');
                    }
                } catch (error) {
                    showNotification('{{ __('payment.generic_error') }}', 'error');
                }
            }

            function showNotification(message, type) {
                // Use existing notification system or fallback
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: type,
                        title: message,
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });
                } else if (typeof window.showToast === 'function') {
                    window.showToast(message, type);
                } else {
                    alert(message);
                }
            }
        </script>
    @endpush
</x-platform-layout>
