<div itemscope itemtype="https://schema.org/FinancialProduct"
    class="{{ !$canCreateWallet || in_array($wallet->platform_role, ['Natan', 'EPP']) ? 'bg-gradient-to-br from-red-900/50 to-rose-900/30 cursor-not-allowed' : 'bg-gradient-to-br from-gray-800 to-gray-900' }} group relative rounded-2xl border border-gray-700/50 p-6 shadow-xl transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl"
    aria-label="Dettagli wallet di {{ $wallet->user ? $wallet->user->name : __('Unassigned') }}"
    aria-disabled="{{ !$canCreateWallet || in_array($wallet->platform_role, ['Natan', 'EPP']) ? 'true' : 'false' }}">

    <!-- Top wallet banner -->
    <div class="absolute top-0 left-0 right-0 h-2 rounded-t-2xl bg-gradient-to-r from-blue-500 to-purple-600"></div>
   
    <!-- Lock icon for disabled state -->
    @if (!$canCreateWallet || in_array($wallet->platform_role, ['Natan', 'EPP']))
        <div class="absolute text-red-400 right-4 top-4">
            <svg class="w-6 h-6 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
        </div>
    @endif

    <div class="flex items-start gap-4 mb-4">
        <!-- Wallet icon -->
        <div class="p-3 bg-gray-700 rounded-xl">
            <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
        </div>

        <!-- Main content -->
        <div class="flex-1">
            <!-- Address with copy button -->
            <div class="mb-4">
                <div class="flex items-center gap-2">
                    <span itemprop="identifier"
                        class="font-mono text-lg font-bold text-transparent bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text break-all">
                        {{-- Mobile: abbreviated --}}
                        <span class="md:hidden">{{ substr($wallet->wallet, 0, 6) }}...{{ substr($wallet->wallet, -4) }}</span>
                        {{-- Desktop: full address --}}
                        <span class="hidden md:inline">{{ $wallet->wallet }}</span>
                    </span>
                    <button 
                        onclick="copyWalletAddress('{{ $wallet->wallet }}')"
                        class="text-gray-400 transition-colors hover:text-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded"
                        aria-label="{{ __('collection.wallet.copy_address') }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                    </button>
                </div>
                <span class="text-sm text-gray-400">{{ __('collection.wallet.address') }}</span>
            </div>

            <!-- Details grid -->
            <div class="grid grid-cols-2 gap-4">
                <!-- Royalty Mint -->
                <div class="p-3 rounded-lg bg-gray-700/50">
                    <div class="flex items-center gap-1 mb-1 text-sm text-blue-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                        <span itemprop="interestRate">{{ $wallet->royalty_mint }}%</span>
                    </div>
                    <span class="text-xs text-gray-400">{{ __('collection.wallet.royalty_mint') }}</span>
                </div>

                <!-- Royalty Rebind -->
                <div class="p-3 rounded-lg bg-gray-700/50">
                    <div class="flex items-center gap-1 mb-1 text-sm text-purple-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        <span itemprop="interestRate">{{ $wallet->royalty_rebind }}%</span>
                    </div>
                    <span class="text-xs text-gray-400">{{ __('collection.wallet.royalty_rebind') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Owner section -->
    <div class="pt-4 mt-4 border-t border-gray-700/50">
        <div class="flex items-center gap-3">
            @if ($wallet->user)
                <img itemprop="image" class="w-8 h-8 rounded-full" src="{{ $wallet->user->profile_photo_url }}"
                    alt="{{ $wallet->user->name }}">
                <div>
                    <p class="text-sm text-gray-300" itemprop="name">{{ $wallet->user->name }}
                        {{ $wallet->user->last_name }}</p>
                    <p class="flex items-center gap-1 text-xs text-gray-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span itemprop="category">{{ $wallet->platform_role }}</span>
                    </p>
                </div>
            @else
                <div class="flex items-center justify-center w-8 h-8 bg-gray-700 rounded-full">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                    </svg>
                </div>
                <span class="text-sm text-gray-500">{{ __('Unassigned') }}</span>
            @endif
        </div>
    </div>

    @if (
        $canCreateWallet &&
            (!in_array($wallet->platform_role, ['Natan', 'EPP', 'Creator']) || Auth::user()->hasRole('superadmin')))
        @if ($wallet && !$wallet->notificationPayloadWallets?->contains('status', 'pending_update'))
            <button data-wallet-address="{{ $wallet->wallet }}" data-collection-id="{{ $wallet->collection_id }}"
                data-user-id="{{ $wallet->user_id }}" data-royalty-mint="{{ $wallet->royalty_mint }}"
                data-royalty-rebind="{{ $wallet->royalty_rebind }}" data-user="{{ $wallet->user_id }}"
                class="update-wallet-btn mt-4 flex w-full transform items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-purple-600 to-blue-500 px-6 py-3 font-medium text-white transition-all duration-300 hover:scale-[1.02] hover:from-purple-700 hover:to-blue-600"
                aria-label="{{ __('collection.wallet.manage_wallet') }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                {{ __('collection.wallet.manage_wallet') }}
            </button>
        @endif
    @elseif ($canCreateWallet && (in_array($wallet->platform_role, ['EPP']) || Auth::user()->hasRole('superadmin')))
        <button data-wallet-address="{{ $wallet->wallet }}" data-collection-id="{{ $wallet->collection_id }}"
            data-user-id="{{ $wallet->user_id }}" data-royalty-mint="{{ $wallet->royalty_mint }}"
            data-royalty-rebind="{{ $wallet->royalty_rebind }}" data-user="{{ $wallet->user_id }}"
            class="donation-btn mt-4 flex w-full transform items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-green-600 to-emerald-500 px-6 py-3 font-medium text-white transition-all duration-300 hover:scale-[1.02] hover:from-green-700 hover:to-emerald-600"
            aria-label="{{ __('collection.wallet.donation') }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
            </svg>
            {{ __('collection.wallet.donation') }}
        </button>
    @endif
</div>
