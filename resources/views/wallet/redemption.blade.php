<x-app-layout>
    <x-slot name="pageTitle">{{ __('wallet.redemption.title') }}</x-slot>

    <div class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-emerald-900 py-12">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">

            {{-- Header --}}
            <div class="mb-8 text-center">
                <div class="mb-4 inline-flex h-16 w-16 items-center justify-center rounded-full bg-emerald-500/20">
                    <svg class="h-8 w-8 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <h1 class="mb-2 text-3xl font-bold text-white">{{ __('wallet.redemption.title') }}</h1>
                <p class="text-gray-400">{{ __('wallet.redemption.subtitle') }}</p>
            </div>

            {{-- Wallet Info Card --}}
            <div class="mb-8 rounded-2xl border border-slate-700/50 bg-slate-800/50 p-6 backdrop-blur-sm">
                <div class="flex items-center gap-4">
                    <div class="flex-shrink-0">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-500/20">
                            <svg class="h-6 w-6 text-emerald-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm text-gray-400">{{ __('wallet.redemption.your_wallet') }}</p>
                        <p class="break-all font-mono text-lg text-white">{{ $walletAddress }}</p>
                        <p class="mt-1 text-sm text-emerald-400">{{ $shortWallet }}</p>
                    </div>
                </div>
            </div>

            @if ($isRedeemed)
                {{-- Already Redeemed State --}}
                <div class="rounded-2xl border border-amber-500/30 bg-amber-500/10 p-6 text-center">
                    <svg class="mx-auto mb-4 h-12 w-12 text-amber-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <h3 class="mb-2 text-xl font-semibold text-amber-400">
                        {{ __('wallet.redemption.already_redeemed_title') }}</h3>
                    <p class="text-gray-400">{{ __('wallet.redemption.already_redeemed_message') }}</p>
                    <a href="{{ route('dashboard') }}"
                        class="mt-6 inline-flex items-center gap-2 rounded-xl bg-slate-700 px-6 py-3 text-white transition-colors hover:bg-slate-600">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        {{ __('wallet.redemption.back_to_dashboard') }}
                    </a>
                </div>
            @else
                {{-- Redemption Steps --}}
                <div id="redemption-flow" class="space-y-6">

                    {{-- Step 1: Warning --}}
                    <div id="step-warning"
                        class="overflow-hidden rounded-2xl border border-slate-700/50 bg-slate-800/50 backdrop-blur-sm">
                        <div class="border-b border-red-500/30 bg-red-500/10 px-6 py-4">
                            <div class="flex items-center gap-3">
                                <svg class="h-6 w-6 text-red-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <h3 class="text-lg font-semibold text-red-400">
                                    {{ __('wallet.redemption.warning_title') }}</h3>
                            </div>
                        </div>
                        <div class="p-6">
                            <p class="mb-4 text-gray-300">{{ __('wallet.redemption.warning_intro') }}</p>
                            <ul class="mb-6 space-y-3">
                                <li class="flex items-start gap-3">
                                    <span
                                        class="mt-0.5 flex h-5 w-5 flex-shrink-0 items-center justify-center rounded-full bg-red-500/20">
                                        <span class="h-2 w-2 rounded-full bg-red-400"></span>
                                    </span>
                                    <span class="text-gray-400">{{ __('wallet.redemption.warning_1') }}</span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <span
                                        class="mt-0.5 flex h-5 w-5 flex-shrink-0 items-center justify-center rounded-full bg-red-500/20">
                                        <span class="h-2 w-2 rounded-full bg-red-400"></span>
                                    </span>
                                    <span class="text-gray-400">{{ __('wallet.redemption.warning_2') }}</span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <span
                                        class="mt-0.5 flex h-5 w-5 flex-shrink-0 items-center justify-center rounded-full bg-red-500/20">
                                        <span class="h-2 w-2 rounded-full bg-red-400"></span>
                                    </span>
                                    <span class="text-gray-400">{{ __('wallet.redemption.warning_3') }}</span>
                                </li>
                            </ul>

                            <div class="mb-6 rounded-xl bg-slate-900/50 p-4">
                                <label for="confirmation-input" class="mb-2 block text-sm font-medium text-gray-300">
                                    {{ __('wallet.redemption.type_confirmation') }}
                                </label>
                                <input type="text" id="confirmation-input"
                                    class="w-full rounded-lg border border-slate-600 bg-slate-800 px-4 py-3 text-white placeholder-gray-500 focus:border-transparent focus:ring-2 focus:ring-emerald-500"
                                    placeholder="CONFERMO RISCATTO" autocomplete="off">
                                <p class="mt-2 text-xs text-gray-500">{{ __('wallet.redemption.type_exactly') }}: <code
                                        class="text-emerald-400">CONFERMO RISCATTO</code></p>
                            </div>

                            <button id="btn-confirm" type="button" disabled
                                class="w-full rounded-xl bg-red-600 px-6 py-3 font-semibold text-white transition-colors hover:bg-red-700 disabled:cursor-not-allowed disabled:bg-slate-700">
                                {{ __('wallet.redemption.btn_confirm') }}
                            </button>
                        </div>
                    </div>

                    {{-- Step 2: Download (hidden by default) --}}
                    <div id="step-download"
                        class="hidden overflow-hidden rounded-2xl border border-emerald-500/30 bg-slate-800/50 backdrop-blur-sm">
                        <div class="border-b border-emerald-500/30 bg-emerald-500/10 px-6 py-4">
                            <div class="flex items-center gap-3">
                                <svg class="h-6 w-6 text-emerald-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                <h3 class="text-lg font-semibold text-emerald-400">
                                    {{ __('wallet.redemption.download_title') }}</h3>
                            </div>
                        </div>
                        <div class="p-6">
                            <p class="mb-6 text-gray-300">{{ __('wallet.redemption.download_intro') }}</p>

                            <a id="download-link" href="#"
                                class="block w-full rounded-xl bg-emerald-600 px-6 py-4 text-center font-semibold text-white transition-colors hover:bg-emerald-700">
                                <span class="flex items-center justify-center gap-2">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                    {{ __('wallet.redemption.btn_download') }}
                                </span>
                            </a>

                            <div class="mt-6 rounded-xl border border-amber-500/30 bg-amber-500/10 p-4">
                                <p class="text-sm text-amber-400">
                                    <strong>{{ __('wallet.redemption.important') }}:</strong>
                                    {{ __('wallet.redemption.download_warning') }}
                                </p>
                            </div>

                            <div class="mt-6 border-t border-slate-700 pt-6">
                                <label class="flex cursor-pointer items-start gap-3">
                                    <input type="checkbox" id="confirm-downloaded"
                                        class="mt-1 h-5 w-5 rounded border-slate-600 bg-slate-800 text-emerald-500 focus:ring-emerald-500">
                                    <span
                                        class="text-sm text-gray-300">{{ __('wallet.redemption.confirm_downloaded') }}</span>
                                </label>

                                <button id="btn-finalize" type="button" disabled
                                    class="mt-4 w-full rounded-xl bg-slate-700 px-6 py-3 font-semibold text-white transition-colors hover:bg-slate-600 disabled:cursor-not-allowed disabled:bg-slate-800">
                                    {{ __('wallet.redemption.btn_finalize') }}
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Step 3: Success (hidden by default) --}}
                    <div id="step-success"
                        class="hidden rounded-2xl border border-emerald-500/30 bg-emerald-500/10 p-8 text-center">
                        <div
                            class="mb-4 inline-flex h-16 w-16 items-center justify-center rounded-full bg-emerald-500/20">
                            <svg class="h-8 w-8 text-emerald-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <h3 class="mb-2 text-2xl font-bold text-emerald-400">
                            {{ __('wallet.redemption.success_title') }}</h3>
                        <p class="mb-6 text-gray-400">{{ __('wallet.redemption.success_message') }}</p>
                        <a href="{{ route('dashboard') }}"
                            class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-6 py-3 text-white transition-colors hover:bg-emerald-700">
                            {{ __('wallet.redemption.back_to_dashboard') }}
                        </a>
                    </div>

                </div>
            @endif

        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const confirmInput = document.getElementById('confirmation-input');
                const btnConfirm = document.getElementById('btn-confirm');
                const btnDownload = document.getElementById('download-link');
                const confirmDownloaded = document.getElementById('confirm-downloaded');
                const btnFinalize = document.getElementById('btn-finalize');

                const stepWarning = document.getElementById('step-warning');
                const stepDownload = document.getElementById('step-download');
                const stepSuccess = document.getElementById('step-success');

                // Enable confirm button when correct text is entered
                if (confirmInput) {
                    confirmInput.addEventListener('input', function() {
                        btnConfirm.disabled = this.value.toUpperCase().trim() !== 'CONFERMO RISCATTO';
                    });
                }

                // Step 1: Confirm redemption
                if (btnConfirm) {
                    btnConfirm.addEventListener('click', async function() {
                        this.disabled = true;
                        this.textContent = '{{ __('wallet.redemption.processing') }}...';

                        try {
                            const response = await fetch('{{ route('wallet.redemption.confirm') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json',
                                },
                                body: JSON.stringify({
                                    confirmation_text: confirmInput.value
                                })
                            });

                            const data = await response.json();

                            if (data.success) {
                                // Update download link with token
                                btnDownload.href = '{{ route('wallet.redemption.download') }}?token=' +
                                    data.token;

                                // Show step 2
                                stepWarning.classList.add('hidden');
                                stepDownload.classList.remove('hidden');
                            } else {
                                alert(data.message || '{{ __('wallet.redemption.error_generic') }}');
                                this.disabled = false;
                                this.textContent = '{{ __('wallet.redemption.btn_confirm') }}';
                            }
                        } catch (error) {
                            console.error('Error:', error);
                            alert('{{ __('wallet.redemption.error_generic') }}');
                            this.disabled = false;
                            this.textContent = '{{ __('wallet.redemption.btn_confirm') }}';
                        }
                    });
                }

                // Enable finalize button when checkbox is checked
                if (confirmDownloaded) {
                    confirmDownloaded.addEventListener('change', function() {
                        btnFinalize.disabled = !this.checked;
                    });
                }

                // Step 3: Finalize redemption
                if (btnFinalize) {
                    btnFinalize.addEventListener('click', async function() {
                        if (!confirm('{{ __('wallet.redemption.finalize_confirm') }}')) {
                            return;
                        }

                        this.disabled = true;
                        this.textContent = '{{ __('wallet.redemption.processing') }}...';

                        try {
                            const response = await fetch('{{ route('wallet.redemption.finalize') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json',
                                },
                                body: JSON.stringify({
                                    confirm_deletion: true
                                })
                            });

                            const data = await response.json();

                            if (data.success) {
                                // Show success step
                                stepDownload.classList.add('hidden');
                                stepSuccess.classList.remove('hidden');
                            } else {
                                alert(data.message || '{{ __('wallet.redemption.error_generic') }}');
                                this.disabled = false;
                                this.textContent = '{{ __('wallet.redemption.btn_finalize') }}';
                            }
                        } catch (error) {
                            console.error('Error:', error);
                            alert('{{ __('wallet.redemption.error_generic') }}');
                            this.disabled = false;
                            this.textContent = '{{ __('wallet.redemption.btn_finalize') }}';
                        }
                    });
                }
            });
        </script>
    @endpush
</x-app-layout>
