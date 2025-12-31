<div id="payment-settings-modal"
    class="fixed inset-0 z-50 hidden h-full w-full overflow-y-auto bg-black/80 backdrop-blur-xl transition-all duration-500"
    aria-labelledby="payment-settings-title" role="dialog" aria-modal="true">
    <div class="flex min-h-screen items-center justify-center px-4 pb-20 pt-4 text-center sm:block sm:p-0">
        {{-- Overlay close --}}
        <div class="absolute inset-0 transition-opacity" aria-hidden="true"
            onclick="document.getElementById('payment-settings-modal').classList.add('hidden')"></div>

        {{-- Modal Panel --}}
        <div
            class="animate-in zoom-in-95 inline-block w-full max-w-4xl transform overflow-hidden rounded-2xl border border-white/10 bg-gradient-to-b from-gray-900 to-black text-left align-middle shadow-[0_0_50px_rgba(212,165,116,0.15)] transition-all duration-300 sm:my-8">
            <div id="payment-settings-loader" class="py-20 text-center">
                <div
                    class="inline-block h-12 w-12 animate-spin rounded-full border-4 border-amber-500 border-t-transparent shadow-[0_0_20px_rgba(245,158,11,0.5)]">
                </div>
                <p class="mt-6 text-xs font-medium uppercase tracking-widest text-amber-500/80">
                    {{ __('Loading settings...') }}</p>
            </div>
            <div id="payment-settings-content" class="hidden">
                {{-- Content inserted here via AJAX --}}
            </div>
            <div id="payment-settings-error" class="hidden py-12 text-center text-red-400">
                <p class="font-medium tracking-wide">{{ __('Error loading payment settings.') }}</p>
                <button onclick="window.paymentModal.loadContent()"
                    class="mt-4 text-sm text-gray-400 underline transition-colors hover:text-white">
                    {{ __('Try again') }}
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        window.paymentModal = {
            init: function() {
                // Pre-bind if needed
            },

            open: function() {
                const modal = document.getElementById('payment-settings-modal');
                modal.classList.remove('hidden');
                this.loadContent();
            },

            close: function() {
                const modal = document.getElementById('payment-settings-modal');
                modal.classList.add('hidden');
            },

            loadContent: async function() {
                const loader = document.getElementById('payment-settings-loader');
                const content = document.getElementById('payment-settings-content');
                const error = document.getElementById('payment-settings-error');

                loader.classList.remove('hidden');
                content.classList.add('hidden');
                error.classList.add('hidden');

                try {
                    const response = await fetch('{{ route('settings.payments.modal') }}', {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'text/html'
                        }
                    });

                    if (!response.ok) throw new Error('Network response was not ok');

                    const html = await response.text();
                    content.innerHTML = html;

                    loader.classList.add('hidden');
                    content.classList.remove('hidden');
                } catch (e) {
                    console.error('Failed to load payment settings', e);
                    loader.classList.add('hidden');
                    error.classList.remove('hidden');
                }
            },

            // Form Actions
            togglePaymentMethod: async function(method) {
                try {
                    const response = await fetch(`/settings/payments/${method}/toggle`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });
                    const data = await response.json();

                    if (data.success) {
                        this.updateToggleUI(method, data.is_enabled);
                        this.showNotification(data.message, 'success');
                    } else {
                        this.showNotification(data.message, 'error');
                    }
                } catch (error) {
                    this.showNotification('{{ __('payment.generic_error') }}', 'error');
                }
            },

            updateToggleUI: function(method, isEnabled) {
                const toggle = document.querySelector(`[data-toggle="${method}"]`);
                if (!toggle) return;

                const span = toggle.querySelector('span:last-child');
                const icon = span.querySelector('svg'); // The checkmark icon

                if (isEnabled) {
                    toggle.classList.remove('bg-gray-700');
                    toggle.style.backgroundColor = '#F59E0B'; // Amber-500

                    span.classList.remove('translate-x-0');
                    span.classList.add('translate-x-5');

                    // Add checkmark if missing
                    if (!icon) {
                        span.innerHTML =
                            `<svg class="w-3 h-3 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>`;
                    }
                } else {
                    toggle.style.backgroundColor = '';
                    toggle.classList.add('bg-gray-700');

                    span.classList.remove('translate-x-5');
                    span.classList.add('translate-x-0');

                    // Remove checkmark
                    span.innerHTML = '';
                }

                if (method === 'bank_transfer') {
                    const section = document.querySelector('.bank-config-section');
                    if (section) {
                        if (isEnabled) {
                            section.classList.remove('hidden');
                        } else {
                            section.classList.add('hidden');
                        }
                    }
                }
            },

            setDefaultMethod: async function(method) {
                try {
                    const response = await fetch(`/settings/payments/${method}/default`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });
                    const data = await response.json();

                    if (data.success) {
                        this.showNotification(data.message, 'success');
                        this.loadContent(); // Refresh to update UI indicators
                    } else {
                        this.showNotification(data.message, 'error');
                    }
                } catch (error) {
                    this.showNotification('{{ __('payment.generic_error') }}', 'error');
                }
            },

            saveStripeConfig: async function() {
                const input = document.getElementById('stripe_account_id');
                const accountId = input.value;

                if (!accountId.startsWith('acct_')) {
                    this.showNotification('Invalid Stripe Account ID (must start with acct_)', 'error');
                    return;
                }

                try {
                    const response = await fetch('/settings/payments/stripe/config', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            stripe_account_id: accountId
                        })
                    });
                    const data = await response.json();

                    if (data.success) {
                        this.showNotification(data.message, 'success');
                        this.loadContent();
                    } else {
                        const errors = Object.values(data.errors || {}).flat().join(', ');
                        this.showNotification(errors || data.message, 'error');
                    }
                } catch (error) {
                    this.showNotification('{{ __('payment.generic_error') }}', 'error');
                }
            },

            saveBankConfig: async function() {
                const form = document.getElementById('bank-config-form');
                const formData = new FormData(form);

                try {
                    const response = await fetch('/settings/payments/bank-transfer/config', {
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
                        this.showNotification(data.message, 'success');
                    } else {
                        const errors = Object.values(data.errors || {}).flat().join(', ');
                        this.showNotification(errors || data.message, 'error');
                    }
                } catch (error) {
                    this.showNotification('{{ __('payment.generic_error') }}', 'error');
                }
            },

            showNotification: function(message, type) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: type,
                        title: message,
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });
                } else {
                    alert(message);
                }
            }
        };
    </script>
@endpush
