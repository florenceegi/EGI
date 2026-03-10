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

            startOnboarding: function() {
                // Delegato a stripeWizard.openPopup() — popup approach, utente non lascia FlorenceEGI
                if (window.stripeWizard) {
                    window.stripeWizard.openPopup();
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

    /**
     * Stripe Onboarding Wizard — popup approach.
     * Utente non lascia mai FlorenceEGI. La finestra Stripe si apre in popup.
     * Quando chiude → check /stripe/status → mostra step 4 risultato.
     *
     * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
     */
    window.stripeWizard = {
        _pollTimer: null,
        _popup: null,

        go: function (step) {
            [1, 2, 3, 4].forEach(function (s) {
                var el  = document.getElementById('sw-step-' + s);
                var dot = document.getElementById('sw-dot-' + s);
                if (el)  { s === step ? el.classList.remove('hidden') : el.classList.add('hidden'); }
                if (dot) {
                    dot.className = s === step
                        ? 'h-1.5 rounded-full transition-all duration-300 w-4 bg-violet-400'
                        : 'h-1.5 rounded-full transition-all duration-300 w-1.5 bg-white/20';
                }
            });
        },

        openPopup: async function () {
            var btn            = document.getElementById('stripe-onboarding-btn');
            var btnText        = document.getElementById('stripe-onboarding-btn-text');
            var popupBlockedEl = document.getElementById('sw-popup-blocked');
            var processing     = '{{ __('payment.wizard.processing') }}';
            var ctaLabel       = '{{ __('payment.wizard.step3_cta', ['psp_name' => $pspName]) }}';

            if (btn)            btn.disabled = true;
            if (btnText)        btnText.textContent = processing;
            if (popupBlockedEl) popupBlockedEl.classList.add('hidden');

            try {
                var response = await fetch('{{ route('settings.payments.stripe.start-onboarding') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                var data = await response.json();

                if (!data.success || !data.url) {
                    if (btn)     btn.disabled = false;
                    if (btnText) btnText.textContent = ctaLabel;
                    if (window.paymentModal) {
                        window.paymentModal.showNotification(
                            data.message || '{{ __('payment.wizard.link_failed') }}', 'error'
                        );
                    }
                    return;
                }

                // Apri Stripe in popup — FlorenceEGI resta aperta
                var left   = Math.round((screen.width  - 840) / 2);
                var top    = Math.round((screen.height - 720) / 2);
                var popup  = window.open(
                    data.url,
                    'stripe-onboarding',
                    'width=840,height=720,scrollbars=yes,resizable=yes,left=' + left + ',top=' + top
                );

                if (!popup || popup.closed || typeof popup.closed === 'undefined') {
                    // Popup bloccato dal browser
                    if (btn)            btn.disabled = false;
                    if (btnText)        btnText.textContent = ctaLabel;
                    if (popupBlockedEl) popupBlockedEl.classList.remove('hidden');
                    return;
                }

                this._popup = popup;

                // Callback da stripe-popup-return.blade.php
                var self = this;
                window.stripeOnboardingComplete = function () { self._onPopupClose(true); };

                // Polling fallback: se l'utente chiude manualmente
                this._pollTimer = setInterval(function () {
                    if (popup.closed) {
                        clearInterval(self._pollTimer);
                        self._onPopupClose(false);
                    }
                }, 2000);

                if (btn)     btn.disabled = false;
                if (btnText) btnText.textContent = ctaLabel;

            } catch (e) {
                if (btn)     btn.disabled = false;
                if (btnText) btnText.textContent = ctaLabel;
                if (window.paymentModal) {
                    window.paymentModal.showNotification('{{ __('payment.wizard.link_failed') }}', 'error');
                }
            }
        },

        _onPopupClose: function (completed) {
            clearInterval(this._pollTimer);
            this._pollTimer = null;
            this.go(4);
            this.checkStatus();
        },

        checkStatus: async function () {
            var elChecking = document.getElementById('sw-result-checking');
            var elComplete = document.getElementById('sw-result-complete');
            var elPending  = document.getElementById('sw-result-pending');
            var elError    = document.getElementById('sw-result-error');

            [elChecking, elComplete, elPending, elError].forEach(function (el) {
                if (el) el.classList.add('hidden');
            });
            if (elChecking) elChecking.classList.remove('hidden');

            try {
                var response = await fetch('{{ route('settings.payments.stripe.status') }}', {
                    headers: { 'Accept': 'application/json' }
                });
                var data = await response.json();

                if (elChecking) elChecking.classList.add('hidden');

                if (data.connected && data.status === 'complete') {
                    if (elComplete) elComplete.classList.remove('hidden');
                    // Ricarica il modal dopo 2 secondi per mostrare stato "Connected"
                    setTimeout(function () {
                        if (window.paymentModal && typeof window.paymentModal.loadContent === 'function') {
                            window.paymentModal.loadContent();
                        }
                    }, 2500);
                } else if (data.connected && (data.status === 'pending' || data.status === 'restricted')) {
                    if (elPending) elPending.classList.remove('hidden');
                } else {
                    if (elError) elError.classList.remove('hidden');
                }
            } catch (e) {
                if (elChecking) elChecking.classList.add('hidden');
                if (elError)    elError.classList.remove('hidden');
            }
        }
    };
    </script>
@endpush
