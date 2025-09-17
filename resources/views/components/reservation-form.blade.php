<div class="p-6 bg-gray-800 rounded-lg shadow-md">
    <h3 class="mb-4 text-lg font-medium text-gray-900">{{ __('reservation.form.title') }}</h3>

    <form method="POST" action="{{ route('egis.reserve', $egi->id) }}" id="reservation-form">
        @csrf

        <div class="space-y-6">
            {{-- Offer Amount --}}
            <div>
                <label for="offer_amount_fiat" class="block text-sm font-medium text-gray-700">
                    {{ __('reservation.form.offer_amount_label') }}
                </label>
                <div class="relative mt-1 rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <span class="text-gray-500 sm:text-sm">€</span>
                    </div>
                    <input type="text" name="offer_amount_fiat" id="offer_amount_fiat"
                        class="block w-full pr-12 border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 pl-12 sm:text-sm"
                        placeholder="{{ __('reservation.form.offer_amount_placeholder') }}"
                        pattern="[0-9]+(\.[0-9]{1,2})?" inputmode="decimal" required
                        value="{{ old('offer_amount_fiat', '') }}">
                </div>
                <p class="mt-1 text-sm text-gray-500" id="algo-equivalent-text">
                    {{ __('reservation.form.algo_equivalent', ['amount' => '0.00']) }}
                </p>
                @error('offer_amount_fiat')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Terms Acceptance --}}
            <div class="flex items-start">
                <div class="flex items-center h-5">
                    <input id="terms_accepted" name="terms_accepted" type="checkbox" required
                        class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                </div>
                <div class="ml-3 text-sm">
                    <label for="terms_accepted" class="font-medium text-gray-700">
                        {{ __('reservation.form.terms_accepted') }}
                    </label>
                </div>
            </div>
            @error('terms_accepted')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror

            {{-- Contact Data (Optional) --}}
            <div class="pt-4 border-t border-gray-200">
                <h4 class="mb-2 text-sm font-medium text-gray-500">{{ __('reservation.form.contact_info') }}</h4>
                <div class="space-y-4">
                    <div>
                        <label for="contact_data[name]" class="block text-sm font-medium text-gray-700">
                            {{ __('Name') }}
                        </label>
                        <input type="text" name="contact_data[name]" id="contact_data[name]"
                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>

                    <div>
                        <label for="contact_data[email]" class="block text-sm font-medium text-gray-700">
                            {{ __('Email') }}
                        </label>
                        <input type="email" name="contact_data[email]" id="contact_data[email]"
                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>

                    <div>
                        <label for="contact_data[message]" class="block text-sm font-medium text-gray-700">
                            {{ __('Message') }}
                        </label>
                        <textarea name="contact_data[message]" id="contact_data[message]" rows="3"
                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                    </div>
                </div>
            </div>

            {{-- Submit Button --}}
            <div>
                <button type="submit"
                    class="inline-flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    {{ __('reservation.form.submit_button') }}
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const offerInput = document.getElementById('offer_amount_fiat');
        const algoText = document.getElementById('algo-equivalent-text');

        // Initial ALGO rate (will be updated via API)
        let algoRate = 0.2; // Default fallback rate: 1 EUR = 5 ALGO

        // Try to fetch current ALGO rate
        fetch('/api/currency/algo-exchange-rate')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Handle new API format with data.rate_to_algo or legacy format with rate
                    const rate = data.data?.rate_to_algo || data.rate;
                    if (rate) {
                        algoRate = rate;
                        updateAlgoEquivalent();
                    }
                }
            })
            .catch(err => console.error('Failed to fetch ALGO rate:', err));

        // Update ALGO equivalent when offer amount changes
        offerInput.addEventListener('input', updateAlgoEquivalent);

        function updateAlgoEquivalent() {
            const eurAmount = parseFloat(offerInput.value) || 0;
            const algoAmount = (eurAmount / algoRate).toFixed(8);
            algoText.textContent = "{{ __('reservation.form.algo_equivalent', ['amount' => '']) }}" + algoAmount;
        }
    });
</script>
@endpush
