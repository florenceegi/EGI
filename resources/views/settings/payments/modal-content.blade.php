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
                                                class="text-[10px] font-bold uppercase tracking-wider text-green-400 opacity-80">{{ __('payment.label_default') }}</span>
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
                                <span class="sr-only">{{ __('payment.label_toggle') }}</span>
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
                                                    {{ $stripeAccountId }}</p>
                                            </div>
                                        </div>
                                        {{-- Optional: Disconnect button could go here --}}
                                    </div>
                                @else
                                    {{-- 4-step Stripe Onboarding Wizard (popup approach — utente non lascia FlorenceEGI) --}}
                                    <div id="stripe-wizard" class="space-y-3">

                                        {{-- Step 1: Intro --}}
                                        <div id="sw-step-1" class="sw-step">
                                            <div
                                                class="flex items-start gap-3 rounded-xl border border-violet-500/20 bg-violet-900/20 p-4">
                                                <span class="shrink-0 text-2xl">🤖</span>
                                                <div>
                                                    <p class="mb-1 text-sm font-semibold text-violet-200">
                                                        {{ __('payment.wizard.intro_title') }}
                                                    </p>
                                                    <p class="text-xs leading-relaxed text-gray-300">
                                                        {{ __('payment.wizard.intro_text', ['psp_name' => $pspName]) }}
                                                    </p>
                                                    <p class="mt-2 text-[11px] text-amber-400/80">
                                                        💡 {{ __('payment.wizard.intro_note') }}
                                                    </p>
                                                </div>
                                            </div>
                                            <button type="button" onclick="window.stripeWizard.go(2)"
                                                class="mt-3 flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-violet-600 to-violet-500 px-4 py-3 text-sm font-bold text-white shadow-lg shadow-violet-500/20 transition-all hover:scale-[1.02] hover:from-violet-500 hover:to-violet-400">
                                                <span>{{ __('payment.wizard.step1_next') }}</span>
                                                <span>→</span>
                                            </button>
                                        </div>

                                        {{-- Step 2: Cosa ti serve --}}
                                        <div id="sw-step-2" class="sw-step hidden">
                                            <p class="mb-3 text-sm font-semibold text-gray-200">
                                                {{ __('payment.wizard.step2_title') }}
                                            </p>
                                            <ul class="mb-4 space-y-2">
                                                <li class="flex items-center gap-2 text-xs text-gray-300">
                                                    <span class="font-bold text-green-400">✓</span>
                                                    {{ __('payment.wizard.step2_item1') }}
                                                </li>
                                                <li class="flex items-center gap-2 text-xs text-gray-300">
                                                    <span class="font-bold text-green-400">✓</span>
                                                    {{ __('payment.wizard.step2_item2') }}
                                                </li>
                                                <li class="flex items-center gap-2 text-xs text-gray-300">
                                                    <span class="font-bold text-green-400">✓</span>
                                                    {{ __('payment.wizard.step2_item3') }}
                                                </li>
                                            </ul>
                                            <div class="flex gap-2">
                                                <button type="button" onclick="window.stripeWizard.go(1)"
                                                    class="flex-1 rounded-xl border border-white/10 px-3 py-2.5 text-xs text-gray-400 transition-colors hover:border-white/20 hover:text-gray-200">
                                                    ← {{ __('payment.wizard.back') }}
                                                </button>
                                                <button type="button" onclick="window.stripeWizard.go(3)"
                                                    class="flex-[2] rounded-xl bg-gradient-to-r from-violet-600 to-violet-500 px-4 py-2.5 text-sm font-bold text-white transition-all hover:scale-[1.02]">
                                                    {{ __('payment.wizard.step2_next') }} →
                                                </button>
                                            </div>
                                        </div>

                                        {{-- Step 3: Apri finestra Stripe --}}
                                        <div id="sw-step-3" class="sw-step hidden">
                                            <div
                                                class="mb-3 rounded-xl border border-amber-500/20 bg-amber-900/10 p-3">
                                                <p class="text-xs leading-relaxed text-amber-300/90">
                                                    🪟 {{ __('payment.wizard.step3_note') }}
                                                </p>
                                            </div>
                                            <button type="button" id="stripe-onboarding-btn"
                                                onclick="window.stripeWizard.openPopup()"
                                                class="flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-green-600 to-green-500 px-4 py-3 text-sm font-bold text-white shadow-lg shadow-green-500/20 transition-all hover:scale-[1.02] hover:from-green-500 hover:to-green-400 disabled:cursor-not-allowed disabled:opacity-60">
                                                <span>💳</span>
                                                <span
                                                    id="stripe-onboarding-btn-text">{{ __('payment.wizard.step3_cta', ['psp_name' => $pspName]) }}</span>
                                            </button>
                                            <p id="sw-popup-blocked"
                                                class="mt-2 hidden text-center text-[11px] text-red-400">
                                                ⚠️ {{ __('payment.wizard.popup_blocked') }}
                                            </p>
                                            <button type="button" onclick="window.stripeWizard.go(2)"
                                                class="mt-2 w-full rounded-xl border border-white/10 px-4 py-2 text-xs text-gray-400 transition-colors hover:border-white/20 hover:text-gray-200">
                                                ← {{ __('payment.wizard.back') }}
                                            </button>
                                        </div>

                                        {{-- Step 4: Risultato --}}
                                        <div id="sw-step-4" class="sw-step hidden">
                                            {{-- Checking spinner --}}
                                            <div id="sw-result-checking"
                                                class="rounded-xl border border-violet-500/20 bg-violet-900/10 p-5 text-center">
                                                <p class="text-sm text-gray-400">
                                                    {{ __('payment.wizard.step4_checking') }}</p>
                                            </div>
                                            {{-- Complete --}}
                                            <div id="sw-result-complete"
                                                class="hidden rounded-xl border border-green-500/30 bg-green-900/20 p-5 text-center">
                                                <div class="text-3xl">🎉</div>
                                                <p class="mt-2 text-sm font-semibold text-green-400">
                                                    {{ __('payment.wizard.step4_complete') }}</p>
                                                <p class="mt-1 text-xs text-gray-400">
                                                    {{ __('payment.wizard.step4_complete_hint') }}</p>
                                            </div>
                                            {{-- Pending / restricted --}}
                                            <div id="sw-result-pending"
                                                class="hidden rounded-xl border border-amber-500/30 bg-amber-900/20 p-5 text-center">
                                                <div class="text-3xl">⏳</div>
                                                <p class="mt-2 text-sm font-semibold text-amber-400">
                                                    {{ __('payment.wizard.step4_pending') }}</p>
                                                <p class="mt-1 text-xs text-gray-400">
                                                    {{ __('payment.wizard.step4_pending_hint') }}</p>
                                            </div>
                                            {{-- Error --}}
                                            <div id="sw-result-error"
                                                class="hidden rounded-xl border border-red-500/30 bg-red-900/20 p-5 text-center">
                                                <div class="text-3xl">❌</div>
                                                <p class="mt-2 text-sm font-semibold text-red-400">
                                                    {{ __('payment.wizard.step4_error') }}</p>
                                                <button type="button" onclick="window.stripeWizard.go(3)"
                                                    class="mt-3 rounded-xl border border-white/10 px-4 py-2 text-xs text-gray-400 hover:text-gray-200">
                                                    {{ __('payment.wizard.step4_retry') }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Step indicator dots --}}
                                    <div class="mt-3 flex items-center justify-center gap-1.5">
                                        @foreach ([1, 2, 3, 4] as $s)
                                            <span id="sw-dot-{{ $s }}"
                                                class="{{ $s === 1 ? 'w-4 bg-violet-400' : 'w-1.5 bg-white/20' }} h-1.5 rounded-full transition-all duration-300">
                                            </span>
                                        @endforeach
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
                                    {{ __('payment.label_make_default') }}
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
                        {{ __('payment.bank_config_title') }}
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
                                        value="{{ $bankDetails['iban'] ?? $bankMethod?->getIban() }}"
                                        class="block w-full rounded-xl border border-gray-700 bg-gray-900/80 px-4 py-3 font-mono tracking-wider text-white placeholder-gray-600 transition-all focus:border-amber-500 focus:ring-1 focus:ring-amber-500 sm:text-sm"
                                        placeholder="IT60X0542811101000000123456">
                                </div>
                            </div>
                            <div class="group">
                                <label for="bic"
                                    class="mb-2 block text-xs font-bold uppercase tracking-widest text-gray-400 transition-colors group-focus-within:text-amber-400">BIC/SWIFT</label>
                                <div class="relative">
                                    <input type="text" id="bic" name="bic"
                                        value="{{ $bankDetails['bic'] ?? $bankMethod?->getBic() }}"
                                        class="block w-full rounded-xl border border-gray-700 bg-gray-900/80 px-4 py-3 font-mono tracking-wider text-white placeholder-gray-600 transition-all focus:border-amber-500 focus:ring-1 focus:ring-amber-500 sm:text-sm"
                                        placeholder="BPPIITRRXXX">
                                </div>
                            </div>
                        </div>
                        <div class="group">
                            <label for="holder"
                                class="mb-2 block text-xs font-bold uppercase tracking-widest text-gray-400 transition-colors group-focus-within:text-amber-400">{{ __('payment.bank_account_holder') }}</label>
                            <input type="text" id="holder" name="holder"
                                value="{{ $bankDetails['holder'] ?? $bankMethod?->getHolder() }}"
                                class="block w-full rounded-xl border border-gray-700 bg-gray-900/80 px-4 py-3 text-white placeholder-gray-600 transition-all focus:border-amber-500 focus:ring-1 focus:ring-amber-500 sm:text-sm"
                                placeholder="{{ __('payment.bank_holder_placeholder') }}">
                        </div>
                        <div class="flex justify-end pt-4">
                            <button type="button" onclick="window.paymentModal.saveBankConfig()"
                                class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-amber-500 to-amber-600 px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-amber-500/20 transition-all hover:scale-105 hover:from-amber-400 hover:to-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/50">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                {{ __('payment.bank_save_details') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    @endif
</div>
