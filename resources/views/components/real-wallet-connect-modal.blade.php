{{-- 📜 Oracode Component: Real Algorand Wallet Connection Modal --}}
{{-- 🎯 Purpose: Universal modal for connecting real Algorand wallets --}}
{{-- 🛡️ Security: Validates wallet format, verifies on-chain existence --}}
{{-- 🎨 Design: NFT-themed with glassmorphism, consistent with existing modal --}}
{{-- 📋 Stack: Blade + Vanilla TypeScript (NO Alpine, NO Livewire) --}}

<div id="real-wallet-connect-modal"
    class="fixed inset-0 z-[100] hidden items-start justify-center overflow-y-auto px-4 pb-4 pt-4 sm:items-center sm:px-0 sm:pb-0 sm:pt-0"
    role="dialog" aria-modal="true" aria-labelledby="real-wallet-title" aria-describedby="real-wallet-description"
    aria-hidden="true" tabindex="-1">

    {{-- Background NFT style --}}
    <div class="real-wallet-backdrop absolute inset-0 bg-gradient-to-br from-purple-900/90 via-black/95 to-indigo-900/90 backdrop-blur-sm"
        aria-hidden="true"></div>

    <div class="relative scale-95 transform opacity-0 transition-all duration-300" id="real-wallet-content"
        role="document">
        <div
            class="max-h-[90vh] w-11/12 max-w-[520px] overflow-hidden rounded-2xl border border-white/20 bg-white/10 shadow-2xl backdrop-blur-xl sm:max-h-none">

            {{-- Header --}}
            <div class="relative bg-gradient-to-r from-emerald-600 to-teal-600 p-6">
                <button id="close-real-wallet-modal" type="button"
                    class="absolute right-4 top-4 text-white/80 transition-colors hover:text-white"
                    aria-label="{{ __('collection.wallet.close_modal') }}">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                {{-- Dynamic Header Content --}}
                <div class="text-center">
                    {{-- Icon container --}}
                    <div class="mb-4 flex justify-center" aria-hidden="true">
                        <div id="real-wallet-header-icon"
                            class="flex h-20 w-20 items-center justify-center rounded-full bg-white/20">
                            {{-- Wallet icon --}}
                            <svg id="real-wallet-icon-wallet" class="h-12 w-12 text-white" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                            {{-- Loading spinner --}}
                            <svg id="real-wallet-icon-loading" class="hidden h-12 w-12 animate-spin text-white"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            {{-- Success icon --}}
                            <svg id="real-wallet-icon-success" class="hidden h-12 w-12 text-white" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            {{-- Options icon --}}
                            <svg id="real-wallet-icon-options" class="hidden h-12 w-12 text-white" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>

                    {{-- Dynamic Title --}}
                    <h2 id="real-wallet-title" class="text-center text-2xl font-bold text-white">
                        {{ __('collection.wallet.real_wallet_connect_title') }}
                    </h2>
                    <p id="real-wallet-description" class="mt-2 text-center text-white/80">
                        {{ __('collection.wallet.real_wallet_connect_subtitle') }}
                    </p>
                </div>
            </div>

            {{-- Main Content Area --}}
            <div class="max-h-[50vh] overflow-y-auto p-6 sm:max-h-none">

                {{-- SECTION 1: Wallet Input --}}
                <div id="real-wallet-section-input" class="real-wallet-section">
                    <form id="real-wallet-form">
                        @csrf
                        <div class="mb-6">
                            <label for="real_wallet_address" class="mb-2 block text-sm font-medium text-white/90">
                                {{ __('collection.wallet.real_wallet_address_label') }}
                            </label>
                            <div class="group relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3"
                                    aria-hidden="true">
                                    <svg class="h-5 w-5 text-emerald-400 group-focus-within:text-emerald-300"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                    </svg>
                                </div>
                                <input type="text" name="wallet_address" id="real_wallet_address" required
                                    maxlength="58" autocomplete="off" spellcheck="false"
                                    class="w-full rounded-lg border border-emerald-500/30 bg-white/10 py-3 pl-10 pr-3 font-mono text-sm uppercase text-white placeholder-white/50 transition focus:border-transparent focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                    placeholder="ABCDEFGHIJKLMNOPQRSTUVWXYZ234567..."
                                    aria-describedby="real-wallet-help" aria-required="true">
                            </div>
                            <div class="mt-2 flex justify-between">
                                <p id="real-wallet-help" class="text-xs text-white/60">
                                    {{ __('collection.wallet.real_wallet_address_help') }}
                                </p>
                                <p id="real-wallet-char-count" class="text-xs text-white/60">
                                    <span id="real-wallet-current-chars">0</span>/58
                                </p>
                            </div>
                        </div>

                        <button type="submit" id="real-wallet-verify-btn"
                            class="w-full rounded-lg bg-gradient-to-r from-emerald-600 to-teal-600 px-6 py-3 font-semibold text-white transition-all duration-300 hover:from-emerald-700 hover:to-teal-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 disabled:cursor-not-allowed disabled:opacity-50">
                            <span
                                id="real-wallet-verify-text">{{ __('collection.wallet.real_wallet_verify_btn') }}</span>
                        </button>
                    </form>
                </div>

                {{-- SECTION 2: Loading --}}
                <div id="real-wallet-section-loading" class="real-wallet-section hidden">
                    <div class="py-8 text-center">
                        <div
                            class="mb-6 inline-flex h-16 w-16 items-center justify-center rounded-full bg-emerald-500/20">
                            <svg class="h-8 w-8 animate-spin text-emerald-400" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="mb-2 text-xl font-semibold text-white">
                            {{ __('collection.wallet.real_wallet_verifying') }}</h3>
                        <p class="text-sm text-white/70">{{ __('collection.wallet.real_wallet_verifying_desc') }}</p>
                    </div>
                </div>

                {{-- SECTION 3: Connect Existing User --}}
                <div id="real-wallet-section-connect" class="real-wallet-section hidden">
                    <div class="mb-6 text-center">
                        <div
                            class="mb-4 inline-flex h-16 w-16 items-center justify-center rounded-full bg-emerald-500/20">
                            <svg class="h-8 w-8 text-emerald-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <h3 class="mb-2 text-xl font-semibold text-white">
                            {{ __('collection.wallet.real_wallet_found') }}</h3>
                        <p class="text-sm text-white/70">{{ __('collection.wallet.real_wallet_found_desc') }}</p>
                    </div>

                    {{-- User info display --}}
                    <div class="mb-6 rounded-lg border border-white/20 bg-white/10 p-4 backdrop-blur-sm">
                        <p class="mb-1 text-xs uppercase tracking-wider text-white/60">
                            {{ __('collection.wallet.real_wallet_account') }}</p>
                        <p id="real-wallet-user-name" class="font-semibold text-white"></p>
                        <p id="real-wallet-display-address" class="mt-2 break-all font-mono text-xs text-emerald-300">
                        </p>
                    </div>

                    <button type="button" id="real-wallet-connect-btn"
                        class="w-full rounded-lg bg-gradient-to-r from-emerald-600 to-teal-600 px-6 py-3 font-semibold text-white transition-all duration-300 hover:from-emerald-700 hover:to-teal-700 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        {{ __('collection.wallet.real_wallet_connect_now') }}
                    </button>

                    <button type="button" id="real-wallet-back-from-connect"
                        class="mt-3 w-full rounded-lg border border-white/20 px-4 py-2 text-sm font-medium text-white/70 transition-all hover:bg-white/10 hover:text-white">
                        ← {{ __('collection.wallet.real_wallet_back') }}
                    </button>
                </div>

                {{-- SECTION 4: Options (New Wallet - Register or Guest) --}}
                <div id="real-wallet-section-options" class="real-wallet-section hidden">
                    <div class="mb-6 text-center">
                        <h3 class="mb-2 text-xl font-semibold text-white">
                            {{ __('collection.wallet.real_wallet_new_wallet') }}</h3>
                        <p class="text-sm text-white/70">{{ __('collection.wallet.real_wallet_choose_option') }}</p>
                    </div>

                    {{-- Wallet address display --}}
                    <div class="mb-6 rounded-lg border border-white/20 bg-white/10 p-4 backdrop-blur-sm">
                        <p class="mb-1 text-xs uppercase tracking-wider text-white/60">
                            {{ __('collection.wallet.real_wallet_your_address') }}</p>
                        <p id="real-wallet-options-address" class="break-all font-mono text-sm text-emerald-300"></p>
                    </div>

                    {{-- Warning for not on chain --}}
                    <div id="real-wallet-not-funded-warning"
                        class="mb-6 hidden rounded-lg border border-yellow-400/30 bg-yellow-900/30 p-4">
                        <p class="text-sm text-yellow-300">
                            <strong>⚠️ {{ __('collection.wallet.real_wallet_not_funded_title') }}</strong><br>
                            {{ __('collection.wallet.real_wallet_not_funded_text') }}
                        </p>
                    </div>

                    <div class="space-y-4">
                        {{-- Full Registration Option --}}
                        <button type="button" id="real-wallet-register-btn"
                            class="group w-full rounded-lg border border-emerald-500/30 bg-emerald-900/20 p-4 transition-all hover:bg-emerald-800/30">
                            <div class="flex items-center">
                                <div class="mr-4 flex-shrink-0">
                                    <svg class="h-8 w-8 text-emerald-400 group-hover:text-emerald-300" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                    </svg>
                                </div>
                                <div class="text-left">
                                    <h3 class="font-semibold text-white">
                                        {{ __('collection.wallet.real_wallet_register_full') }}</h3>
                                    <p class="text-sm text-white/70">
                                        {{ __('collection.wallet.real_wallet_register_desc') }}</p>
                                </div>
                            </div>
                        </button>

                        {{-- Continue as Guest Option --}}
                        <button type="button" id="real-wallet-guest-btn"
                            class="group w-full rounded-lg border border-white/20 bg-white/5 p-4 transition-all hover:bg-white/10">
                            <div class="flex items-center">
                                <div class="mr-4 flex-shrink-0">
                                    <svg class="h-8 w-8 text-blue-400 group-hover:text-blue-300" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <div class="text-left">
                                    <h3 class="font-semibold text-white">
                                        {{ __('collection.wallet.real_wallet_continue_guest') }}</h3>
                                    <p class="text-sm text-white/70">
                                        {{ __('collection.wallet.real_wallet_guest_desc') }}</p>
                                </div>
                            </div>
                        </button>
                    </div>

                    <button type="button" id="real-wallet-back-from-options"
                        class="mt-4 w-full rounded-lg border border-white/20 px-4 py-2 text-sm font-medium text-white/70 transition-all hover:bg-white/10 hover:text-white">
                        ← {{ __('collection.wallet.real_wallet_back') }}
                    </button>
                </div>

                {{-- SECTION 5: Success --}}
                <div id="real-wallet-section-success" class="real-wallet-section hidden">
                    <div class="py-8 text-center">
                        <div
                            class="mb-6 inline-flex h-16 w-16 items-center justify-center rounded-full bg-emerald-500/20">
                            <svg class="h-8 w-8 text-emerald-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <h3 class="mb-2 text-xl font-semibold text-white">
                            {{ __('collection.wallet.real_wallet_success') }}</h3>
                        <p id="real-wallet-success-message" class="text-sm text-white/70"></p>

                        {{-- Info for weak auth users --}}
                        <div id="real-wallet-upgrade-info"
                            class="mt-6 hidden rounded-lg border border-blue-400/30 bg-blue-900/30 p-4">
                            <p class="text-sm text-blue-300">
                                {{ __('collection.wallet.real_wallet_upgrade_info') }}
                            </p>
                        </div>
                    </div>

                    <button type="button" id="real-wallet-close-success"
                        class="w-full rounded-lg bg-gradient-to-r from-emerald-600 to-teal-600 px-6 py-3 font-semibold text-white transition-all duration-300 hover:from-emerald-700 hover:to-teal-700 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        {{ __('collection.wallet.real_wallet_continue') }}
                    </button>
                </div>

                {{-- Error container --}}
                <div id="real-wallet-error-container" class="mt-4 hidden" role="alert" aria-live="polite">
                    <div class="rounded-lg border border-red-400/30 bg-red-900/30 p-4 backdrop-blur-sm">
                        <p id="real-wallet-error-message" class="text-sm text-red-300"></p>
                    </div>
                </div>

                {{-- Link to FEGI modal for users without wallet --}}
                <p class="mt-6 text-center text-xs text-white/60">
                    {{ __('collection.wallet.real_wallet_no_wallet') }}
                    <button type="button" id="real-wallet-switch-to-fegi"
                        class="text-purple-400 underline transition-colors hover:text-purple-300">
                        {{ __('collection.wallet.real_wallet_use_fegi') }}
                    </button>
                </p>
            </div>
        </div>
    </div>
</div>

<style>
    /* Transizioni smooth per le sezioni */
    .real-wallet-section {
        transition: opacity 0.3s ease-in-out, transform 0.3s ease-in-out;
    }

    .real-wallet-section.hidden {
        opacity: 0;
        transform: translateY(10px);
        pointer-events: none;
        position: absolute;
    }

    .real-wallet-section:not(.hidden) {
        opacity: 1;
        transform: translateY(0);
        position: relative;
    }

    /* Animazione icone header */
    #real-wallet-header-icon svg {
        transition: all 0.3s ease-in-out;
    }
</style>
