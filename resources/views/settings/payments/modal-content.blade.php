<div class="relative p-8">
    <!-- Decorative Header Elements -->
    <div class="pointer-events-none absolute right-0 top-0 -mr-20 -mt-20 h-64 w-64 rounded-full bg-amber-500/5 blur-3xl">
    </div>
    <div
        class="pointer-events-none absolute bottom-0 left-0 -mb-20 -ml-20 h-64 w-64 rounded-full bg-blue-500/5 blur-3xl">
    </div>

    <div class="relative mb-10 flex items-start justify-between">
        <div>
            <h2
                class="bg-gradient-to-r from-amber-200 via-amber-400 to-amber-600 bg-clip-text text-3xl font-black uppercase tracking-wider text-transparent drop-shadow-sm">
                {{ __('payment.settings_title') }}
            </h2>
            <p class="mt-1 text-sm font-medium tracking-wide text-gray-400">{{ __('payment.settings_description') }}</p>
        </div>
        <button onclick="document.getElementById('payment-settings-modal').classList.add('hidden')"
            class="group rounded-full bg-white/5 p-2 text-gray-400 transition-all hover:bg-white/10 hover:text-white">
            <svg class="h-6 w-6 transition-transform duration-300 group-hover:rotate-90" fill="none"
                viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    @if (!$user->isSeller())
        <div
            class="relative mb-8 overflow-hidden rounded-xl border border-amber-500/20 bg-gradient-to-br from-amber-900/20 to-black p-6">
            <div
                class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMSIgY3k9IjEiIHI9IjEiIGZpbGw9InJnYmEoMjQ1LCAxNTgsIDExLCAwLjEpIi8+PC9zdmc+')] opacity-20">
            </div>
            <div class="relative z-10 flex items-center gap-4">
                <div
                    class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-amber-500/10 text-amber-500">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <p class="font-medium text-amber-100">{{ __('payment.settings_restricted_to_sellers') }}</p>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            @foreach ($availableMethods as $methodKey => $methodInfo)
                @php
                    $userMethod = $userMethods[$methodKey] ?? null;
                    $isEnabled = $userMethod?->is_enabled ?? false;
                    $isDefault = $userMethod?->is_default ?? false;
                @endphp

                <div class="group relative overflow-hidden rounded-3xl border border-white/5 bg-gray-900/80 p-6 transition-all duration-300 hover:-translate-y-1 hover:border-amber-500/30 hover:shadow-[0_0_40px_rgba(245,158,11,0.1)]"
                    data-method="{{ $methodKey }}">
                    <!-- Card Gradient Background -->
                    <div
                        class="absolute inset-0 bg-gradient-to-br from-white/5 to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100">
                    </div>

                    <!-- Watermark Icon -->
                    <div
                        class="absolute -bottom-6 -right-6 text-white/5 transition-transform duration-500 group-hover:scale-110 group-hover:text-white/10">
                        @if ($methodInfo['icon'] === 'credit-card')
                            <svg class="h-32 w-32" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                                </path>
                            </svg>
                        @elseif($methodInfo['icon'] === 'coins')
                            <svg class="h-32 w-32" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                        @else
                            <svg class="h-32 w-32" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                </path>
                            </svg>
                        @endif
                    </div>

                    <div class="relative z-10 flex h-full flex-col justify-between">
                        <!-- Top Section -->
                        <div class="mb-4 flex items-start justify-between">
                            <div class="flex items-center gap-4">
                                <div
                                    class="flex h-12 w-12 items-center justify-center rounded-2xl border border-white/10 bg-gradient-to-br from-gray-800 to-black shadow-inner">
                                    @if ($methodInfo['icon'] === 'credit-card')
                                        <svg class="h-6 w-6 text-amber-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                                            </path>
                                        </svg>
                                    @elseif($methodInfo['icon'] === 'coins')
                                        <svg class="h-6 w-6 text-amber-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                            </path>
                                        </svg>
                                    @else
                                        <svg class="h-6 w-6 text-amber-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                            </path>
                                        </svg>
                                    @endif
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold tracking-wide text-white">{{ $methodInfo['name'] }}
                                    </h3>
                                    @if ($isDefault)
                                        <div class="mt-1 flex items-center gap-1.5">
                                            <div
                                                class="h-1.5 w-1.5 rounded-full bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.6)]">
                                            </div>
                                            <span
                                                class="text-[10px] font-bold uppercase tracking-wider text-green-400 opacity-80">{{ __('Default') }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Premium Toggle -->
                            <button type="button"
                                onclick="window.paymentModal.togglePaymentMethod('{{ $methodKey }}')"
                                class="relative inline-flex h-7 w-12 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent bg-gray-700 transition-colors duration-200 ease-in-out focus:outline-none"
                                data-toggle="{{ $methodKey }}"
                                style="{{ $isEnabled ? 'background-color: #F59E0B;' : '' }}">
                                <!-- Amber-500 fallback -->
                                <span class="sr-only">{{ __('Toggle') }}</span>
                                <span
                                    class="{{ $isEnabled ? 'translate-x-5' : 'translate-x-0' }} pointer-events-none inline-block flex h-6 w-6 transform items-center justify-center rounded-full bg-white shadow-lg ring-0 transition duration-200 ease-in-out">
                                    @if ($isEnabled)
                                        <svg class="h-3 w-3 text-amber-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    @endif
                                </span>
                            </button>
                        </div>

                        <p class="mb-4 min-h-[40px] text-xs leading-relaxed text-gray-400">
                            {{ $methodInfo['description'] }}</p>

                        {{-- Stripe Specific Config --}}
                        @if ($methodKey === 'stripe')
                            <div class="mt-4 space-y-4 border-t border-white/5 pt-4">
                                @if ($stripeConnected)
                                    <div
                                        class="flex items-center justify-between rounded-lg bg-green-500/10 p-3 ring-1 ring-green-500/30">
                                        <div class="flex items-center space-x-3">
                                            <div
                                                class="flex h-8 w-8 items-center justify-center rounded-full bg-green-500/20 text-green-400">
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-green-400">Connected</p>
                                                <p class="font-mono text-xs text-green-500/60">
                                                    {{ $user->stripe_account_id }}</p>
                                            </div>
                                        </div>
                                        {{-- Optional: Disconnect button could go here --}}
                                    </div>
                                @else
                                    <div class="space-y-3">

                                        <a href="https://connect.stripe.com/express/oauth/authorize" target="_blank"
                                            class="flex w-full items-center justify-center space-x-2 rounded-lg bg-[#635BFF] px-4 py-2 text-sm font-medium text-white shadow-lg shadow-indigo-500/20 transition-transform hover:scale-[1.02] hover:bg-[#5851E3]">
                                            <span>Connect with Stripe</span>
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                            </svg>
                                        </a>

                                        <div class="relative">
                                            <div class="absolute inset-0 flex items-center" aria-hidden="true">
                                                <div class="w-full border-t border-gray-700"></div>
                                            </div>
                                            <div class="relative flex justify-center">
                                                <span
                                                    class="bg-[#111] px-2 text-xs uppercase tracking-wider text-gray-500">OR
                                                    ENTER ID MANUALLY</span>
                                            </div>
                                        </div>

                                        <div class="flex space-x-2">
                                            <input type="text" id="stripe_account_id"
                                                class="block w-full rounded-lg border border-gray-700 bg-gray-900/50 px-3 py-2 text-sm text-white placeholder-gray-600 focus:border-[#635BFF] focus:ring-1 focus:ring-[#635BFF]"
                                                placeholder="acct_...">
                                            <button type="button" onclick="window.paymentModal.saveStripeConfig()"
                                                class="rounded-lg bg-gray-800 px-3 py-2 text-sm font-medium text-gray-300 ring-1 ring-white/10 hover:bg-gray-700">
                                                Save
                                            </button>
                                        </div>
                                        <p class="text-[10px] text-gray-500">
                                            If you already have a Stripe Connect account, enter the ID here (starts with
                                            acct_).
                                        </p>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- Default Button -->
                        @if ($isEnabled && !$isDefault)
                            <div class="mt-auto flex justify-end border-t border-white/5 pt-4">
                                <button type="button"
                                    onclick="window.paymentModal.setDefaultMethod('{{ $methodKey }}')"
                                    class="text-xs font-bold uppercase tracking-wider text-gray-500 transition-colors hover:text-amber-400">
                                    {{ __('Make Default') }}
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Bank Transfer Detailed Config (Moved outside grid or handled elegantly) -->
        @if (isset($availableMethods['bank_transfer']) && isset($availableMethods['bank_transfer']['requires_iban']))
            @php
                $bankMethod = $userMethods['bank_transfer'] ?? null;
                $bankEnabled = $bankMethod?->is_enabled ?? false;
            @endphp
            <div
                class="bank-config-section {{ $bankEnabled ? '' : 'hidden' }} animate-in slide-in-from-top-4 mt-8 duration-500">
                <div
                    class="relative overflow-hidden rounded-2xl border border-white/10 bg-black/40 p-8 shadow-2xl backdrop-blur-md">
                    <div class="absolute right-0 top-0 p-4 opacity-10">
                        <svg class="h-32 w-32 text-amber-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                            </path>
                        </svg>
                    </div>

                    <h3 class="mb-6 flex items-center gap-2 text-xl font-bold text-white">
                        <span class="h-8 w-1 rounded-full bg-amber-500"></span>
                        {{ __('Bank Account Configuration') }}
                    </h3>

                    <form id="bank-config-form" class="relative z-10 space-y-6">
                        @csrf
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div class="group">
                                <label for="iban"
                                    class="mb-2 block text-xs font-bold uppercase tracking-widest text-gray-400 transition-colors group-focus-within:text-amber-400">IBAN
                                    Code</label>
                                <div class="relative">
                                    <input type="text" id="iban" name="iban"
                                        value="{{ $bankMethod?->getIban() }}"
                                        class="block w-full rounded-xl border border-gray-700 bg-gray-900/80 px-4 py-3 font-mono tracking-wider text-white placeholder-gray-600 transition-all focus:border-amber-500 focus:ring-1 focus:ring-amber-500 sm:text-sm"
                                        placeholder="IT60X0542811101000000123456">
                                </div>
                            </div>
                            <div class="group">
                                <label for="bic"
                                    class="mb-2 block text-xs font-bold uppercase tracking-widest text-gray-400 transition-colors group-focus-within:text-amber-400">BIC/SWIFT</label>
                                <div class="relative">
                                    <input type="text" id="bic" name="bic"
                                        value="{{ $bankMethod?->getBic() }}"
                                        class="block w-full rounded-xl border border-gray-700 bg-gray-900/80 px-4 py-3 font-mono tracking-wider text-white placeholder-gray-600 transition-all focus:border-amber-500 focus:ring-1 focus:ring-amber-500 sm:text-sm"
                                        placeholder="BPPIITRRXXX">
                                </div>
                            </div>
                        </div>
                        <div class="group">
                            <label for="holder"
                                class="mb-2 block text-xs font-bold uppercase tracking-widest text-gray-400 transition-colors group-focus-within:text-amber-400">{{ __('Account Holder') }}</label>
                            <input type="text" id="holder" name="holder"
                                value="{{ $bankMethod?->getHolder() }}"
                                class="block w-full rounded-xl border border-gray-700 bg-gray-900/80 px-4 py-3 text-white placeholder-gray-600 transition-all focus:border-amber-500 focus:ring-1 focus:ring-amber-500 sm:text-sm"
                                placeholder="{{ __('Full name as on bank account') }}">
                        </div>
                        <div class="flex justify-end pt-4">
                            <button type="button" onclick="window.paymentModal.saveBankConfig()"
                                class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-amber-500 to-amber-600 px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-amber-500/20 transition-all hover:scale-105 hover:from-amber-400 hover:to-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/50">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                {{ __('Save Bank Details') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    @endif
</div>
