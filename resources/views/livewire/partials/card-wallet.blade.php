{{-- Wallet Card Design: Credit Card Style with Magnetic Stripe --}}
<div itemscope itemtype="https://schema.org/FinancialProduct"
    class="{{ !$canCreateWallet || in_array($wallet->platform_role, ['Natan', 'EPP'])
        ? 'cursor-not-allowed border-red-500/60 bg-gradient-to-br from-red-900/90 via-red-800/80 to-red-900/90'
        : 'border-slate-300/40 bg-gradient-to-br from-slate-400/80 via-slate-300/70 to-slate-400/80 hover:-translate-y-2 hover:shadow-slate-400/30 hover:shadow-[0_20px_60px_-15px]' }} 
    group relative w-full overflow-hidden rounded-2xl border-2 shadow-2xl backdrop-blur-sm transition-all duration-500 
    md:aspect-[1.586] md:max-w-[340px]"
    aria-label="Wallet di {{ $wallet->user ? $wallet->user->name : __('Unassigned') }}"
    aria-disabled="{{ !$canCreateWallet || in_array($wallet->platform_role, ['Natan', 'EPP']) ? 'true' : 'false' }}">

    {{-- Magnetic stripe (TOP - black band like real credit cards) --}}
    <div
        class="{{ !$canCreateWallet || in_array($wallet->platform_role, ['Natan', 'EPP'])
            ? 'bg-gradient-to-b from-gray-900 via-gray-800 to-gray-900'
            : 'bg-gradient-to-b from-gray-950 via-gray-900 to-gray-950' }} absolute left-0 right-0 top-0 h-12 shadow-inner">
        {{-- Magnetic stripe horizontal lines --}}
        <div class="absolute inset-0 opacity-40"
            style="background: repeating-linear-gradient(
            0deg,
            transparent,
            transparent 2px,
            rgba(255,255,255,0.05) 2px,
            rgba(255,255,255,0.05) 4px
        );">
        </div>
    </div>

    {{-- Content wrapper with proper padding --}}
    <div class="relative flex h-full flex-col px-5 py-4">

        {{-- Holographic shimmer on hover --}}
        <div
            class="absolute inset-0 bg-gradient-to-tr from-transparent via-white/10 to-transparent opacity-0 transition-opacity duration-700 group-hover:opacity-100">
        </div>

        {{-- Blockchain pattern decoration --}}
        <div class="absolute right-0 top-0 h-32 w-32 opacity-10">
            <svg viewBox="0 0 100 100" class="h-full w-full text-blue-400">
                <circle cx="50" cy="50" r="40" stroke="currentColor" stroke-width="1" fill="none" />
                <circle cx="50" cy="50" r="30" stroke="currentColor" stroke-width="1" fill="none" />
                <circle cx="50" cy="50" r="20" stroke="currentColor" stroke-width="1" fill="none" />
            </svg>
        </div>

        {{-- Lock icon for disabled state --}}
        @if (!$canCreateWallet || in_array($wallet->platform_role, ['Natan', 'EPP']))
            <div class="absolute right-4 top-16 z-10 text-red-400">
                <div class="rounded-full bg-red-900/50 p-2 backdrop-blur-sm">
                    <svg class="h-6 w-6 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
            </div>
        @endif

        {{-- Top row: Card icon + Algorand badge + Copy button --}}
        <div class="relative z-10 mb-3 mt-9 flex items-center justify-between">
            {{-- Credit card icon (replacing useless grid) --}}
            <div class="flex items-center gap-2">
                <span class="text-2xl drop-shadow-lg" aria-hidden="true">💳</span>
                @if ($wallet->user)
                    <span
                        class="max-w-[120px] truncate text-sm font-bold text-white drop-shadow-md">{{ $wallet->user->name }}</span>
                @endif
            </div>

            {{-- Algorand badge + copy button --}}
            <div class="flex items-center gap-2">
                <div class="rounded-lg border border-slate-400/30 bg-slate-900/60 px-2.5 py-1 backdrop-blur-md">
                    <span class="text-xs font-bold tracking-wider text-white drop-shadow">ALGORAND</span>
                </div>
                <button onclick="copyWalletAddress('{{ $wallet->wallet }}')"
                    class="rounded-lg border border-slate-400/30 bg-slate-900/50 p-1.5 text-gray-300 backdrop-blur-md transition-all hover:border-blue-400/50 hover:bg-slate-800/60 hover:text-white focus:outline-none focus:ring-2 focus:ring-blue-400/50"
                    aria-label="{{ __('collection.wallet.copy_address') }}">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                </button>
            </div>
        </div>

         {{-- Wallet Address (compact like card number) --}}
         <div class="relative z-10 mb-3">
             <div class="mb-0.5 text-[9px] font-semibold uppercase tracking-wider text-slate-700 drop-shadow-md">Wallet
                 Address</div>
             <span itemprop="identifier"
                 class="block break-all font-mono text-[10px] font-bold leading-tight tracking-wider text-white drop-shadow-[0_2px_8px_rgba(0,0,0,0.8)]">
                {{-- Mobile: abbreviated --}}
                <span class="md:hidden">{{ substr($wallet->wallet, 0, 8) }}...{{ substr($wallet->wallet, -6) }}</span>
                {{-- Desktop: full address, dense spacing --}}
                <span class="hidden md:block">{{ chunk_split($wallet->wallet, 18, ' ') }}</span>
            </span>
        </div>

         {{-- Royalties row (dense, visible, icon-labeled) --}}
         <div class="relative z-10 mb-3 flex items-center gap-3 text-sm">
             {{-- Royalty Mint --}}
             <div
                 class="flex items-center gap-1.5 rounded-lg border border-blue-400/20 bg-slate-900/40 px-2.5 py-1.5 backdrop-blur-md">
                 <svg class="h-4 w-4 flex-shrink-0 text-blue-400 drop-shadow" fill="none" stroke="currentColor"
                     viewBox="0 0 24 24">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                         d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                 </svg>
                 <span class="text-[10px] font-semibold uppercase tracking-wide text-slate-700">Royalty:</span>
                 <span itemprop="interestRate"
                     class="text-base font-bold text-blue-300 drop-shadow-md">{{ $wallet->royalty_mint }}%</span>
             </div>

             {{-- Royalty Rebind --}}
             <div
                 class="flex items-center gap-1.5 rounded-lg border border-purple-400/20 bg-slate-900/40 px-2.5 py-1.5 backdrop-blur-md">
                 <svg class="h-4 w-4 flex-shrink-0 text-purple-400 drop-shadow" fill="none" stroke="currentColor"
                     viewBox="0 0 24 24">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                         d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                 </svg>
                 <span class="text-[10px] font-semibold uppercase tracking-wide text-slate-700">Resale:</span>
                 <span itemprop="interestRate"
                     class="text-base font-bold text-purple-300 drop-shadow-md">{{ $wallet->royalty_rebind }}%</span>
             </div>
         </div>

        {{-- Owner section (cardholder name) --}}
        <div class="relative z-10 mt-auto border-t border-slate-400/20 pt-2.5">
            <div class="mb-1 text-[10px] font-semibold uppercase tracking-wider text-slate-700 drop-shadow-md">Card Holder
            </div>
            <div class="flex items-center gap-2">
                @if ($wallet->user)
                    <img itemprop="image" class="h-8 w-8 rounded-full border-2 border-white/30 shadow-lg"
                        src="{{ $wallet->user->profile_photo_url }}" alt="{{ $wallet->user->name }}">
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-bold text-white drop-shadow-md" itemprop="name">
                            {{ $wallet->user->name }} {{ $wallet->user->last_name }}
                        </p>
                        <p class="flex items-center gap-1 text-[10px] text-slate-600 drop-shadow-md">
                            <span itemprop="category"
                                class="truncate font-medium uppercase tracking-wide">{{ $wallet->platform_role }}</span>
                        </p>
                    </div>
                @else
                    <div
                        class="flex h-8 w-8 items-center justify-center rounded-full border-2 border-dashed border-slate-600 bg-slate-800/60">
                        <svg class="h-4 w-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                        </svg>
                    </div>
                    <span class="text-sm font-semibold text-slate-700 drop-shadow-md">{{ __('Unassigned') }}</span>
                @endif
            </div>
        </div>

    </div>{{-- Close content wrapper --}}

    {{-- EMV Chip stripe (BOTTOM - golden band like real credit cards) --}}
    <div
        class="{{ !$canCreateWallet || in_array($wallet->platform_role, ['Natan', 'EPP'])
            ? 'bg-gradient-to-b from-amber-700/40 via-yellow-600/40 to-amber-700/40'
            : 'bg-gradient-to-b from-amber-600/50 via-yellow-500/50 to-amber-600/50' }} absolute bottom-0 left-0 right-0 h-8 shadow-inner">
        {{-- Chip pattern --}}
        <div class="absolute inset-0 opacity-30"
            style="background: repeating-linear-gradient(
            90deg,
            transparent,
            transparent 8px,
            rgba(255,255,255,0.1) 8px,
            rgba(255,255,255,0.1) 10px
        );">
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
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
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
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
            </svg>
            {{ __('collection.wallet.donation') }}
        </button>
    @endif
</div>
