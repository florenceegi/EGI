{{-- Wallet Card Design: Credit Card Style with Magnetic Stripe --}}
<div itemscope itemtype="https://schema.org/FinancialProduct"
    class="group relative overflow-hidden rounded-2xl border-2 shadow-2xl backdrop-blur-sm transition-all duration-500 
    {{ !$canCreateWallet || in_array($wallet->platform_role, ['Natan', 'EPP']) 
        ? 'cursor-not-allowed border-red-400/40' 
        : 'border-slate-400/30 hover:-translate-y-2 hover:shadow-slate-400/20 hover:shadow-[0_20px_60px_-15px]' }}
    {{-- Desktop: Credit card aspect ratio + max width --}}
    md:max-w-[340px] md:aspect-[1.586]
    {{-- Mobile: Full width, auto height --}}
    w-full"
    style="background: {{ !$canCreateWallet || in_array($wallet->platform_role, ['Natan', 'EPP']) 
        ? 'linear-gradient(135deg, rgba(220, 38, 38, 0.35), rgba(185, 28, 28, 0.25), rgba(220, 38, 38, 0.35))' 
        : 'linear-gradient(135deg, rgba(203, 213, 225, 0.18), rgba(148, 163, 184, 0.12), rgba(226, 232, 240, 0.18))' }},
    url(\'data:image/svg+xml,%3Csvg width=&quot;200&quot; height=&quot;200&quot; xmlns=&quot;http://www.w3.org/2000/svg&quot;%3E%3Cdefs%3E%3Cpattern id=&quot;brushed&quot; width=&quot;4&quot; height=&quot;4&quot; patternUnits=&quot;userSpaceOnUse&quot; patternTransform=&quot;rotate(45)&quot;%3E%3Cline x1=&quot;0&quot; y1=&quot;0&quot; x2=&quot;0&quot; y2=&quot;4&quot; stroke=&quot;rgba(255,255,255,0.04)&quot; stroke-width=&quot;2&quot;/%3E%3C/pattern%3E%3C/defs%3E%3Crect width=&quot;200&quot; height=&quot;200&quot; fill=&quot;url(%23brushed)&quot; /%3E%3C/svg%3E\'),
    radial-gradient(circle at 20% 50%, rgba(255,255,255,0.08) 0%, transparent 50%),
    radial-gradient(circle at 80% 50%, rgba(255,255,255,0.08) 0%, transparent 50%);"
    aria-label="Wallet di {{ $wallet->user ? $wallet->user->name : __('Unassigned') }}"
    aria-disabled="{{ !$canCreateWallet || in_array($wallet->platform_role, ['Natan', 'EPP']) ? 'true' : 'false' }}">

    {{-- Magnetic stripe (TOP - black band like real credit cards) --}}
    <div class="absolute left-0 right-0 top-0 h-12 {{ !$canCreateWallet || in_array($wallet->platform_role, ['Natan', 'EPP']) 
        ? 'bg-gradient-to-b from-gray-900 via-gray-800 to-gray-900' 
        : 'bg-gradient-to-b from-gray-950 via-gray-900 to-gray-950' }} shadow-inner">
        {{-- Magnetic stripe horizontal lines --}}
        <div class="absolute inset-0 opacity-40" style="background: repeating-linear-gradient(
            0deg,
            transparent,
            transparent 2px,
            rgba(255,255,255,0.05) 2px,
            rgba(255,255,255,0.05) 4px
        );"></div>
    </div>

    {{-- Content wrapper with proper padding --}}
    <div class="relative h-full px-5 py-4 flex flex-col">

    {{-- Holographic shimmer on hover --}}
    <div class="absolute inset-0 bg-gradient-to-tr from-transparent via-white/10 to-transparent opacity-0 transition-opacity duration-700 group-hover:opacity-100"></div>

    {{-- Blockchain pattern decoration --}}
    <div class="absolute right-0 top-0 h-32 w-32 opacity-10">
        <svg viewBox="0 0 100 100" class="h-full w-full text-blue-400">
            <circle cx="50" cy="50" r="40" stroke="currentColor" stroke-width="1" fill="none"/>
            <circle cx="50" cy="50" r="30" stroke="currentColor" stroke-width="1" fill="none"/>
            <circle cx="50" cy="50" r="20" stroke="currentColor" stroke-width="1" fill="none"/>
        </svg>
    </div>

    {{-- Lock icon for disabled state --}}
    @if (!$canCreateWallet || in_array($wallet->platform_role, ['Natan', 'EPP']))
        <div class="absolute right-4 top-16 z-10 text-red-400">
            <div class="rounded-full bg-red-900/50 p-2 backdrop-blur-sm">
                <svg class="h-6 w-6 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
        </div>
    @endif

    {{-- Top row: Card icon + Algorand badge + Copy button --}}
    <div class="relative z-10 mt-9 mb-3 flex items-center justify-between">
        {{-- Credit card icon (replacing useless grid) --}}
        <div class="flex items-center gap-2">
            <span class="text-2xl drop-shadow-lg" aria-hidden="true">💳</span>
            @if ($wallet->user)
                <span class="text-sm font-bold text-white drop-shadow-md truncate max-w-[120px]">{{ $wallet->user->name }}</span>
            @endif
        </div>

        {{-- Algorand badge + copy button --}}
        <div class="flex items-center gap-2">
            <div class="rounded-lg bg-slate-900/60 px-2.5 py-1 backdrop-blur-md border border-slate-400/30">
                <span class="text-xs font-bold tracking-wider text-white drop-shadow">ALGORAND</span>
            </div>
            <button onclick="copyWalletAddress('{{ $wallet->wallet }}')"
                class="rounded-lg bg-slate-900/50 p-1.5 text-gray-300 backdrop-blur-md border border-slate-400/30 transition-all hover:bg-slate-800/60 hover:text-white hover:border-blue-400/50 focus:outline-none focus:ring-2 focus:ring-blue-400/50"
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
        <div class="mb-1 text-[10px] font-semibold uppercase tracking-wider text-gray-400 drop-shadow">Wallet Address</div>
        <span itemprop="identifier"
            class="block break-all font-mono text-xs font-bold tracking-wider text-white drop-shadow-[0_2px_8px_rgba(0,0,0,0.8)] leading-tight">
            {{-- Mobile: abbreviated --}}
            <span class="md:hidden">{{ substr($wallet->wallet, 0, 8) }}...{{ substr($wallet->wallet, -6) }}</span>
            {{-- Desktop: full address, dense spacing --}}
            <span class="hidden md:block">{{ chunk_split($wallet->wallet, 16, ' ') }}</span>
        </span>
    </div>

    {{-- Royalties row (dense, visible, icon-labeled) --}}
    <div class="relative z-10 mb-3 flex items-center gap-3 text-sm">
        {{-- Royalty Mint --}}
        <div class="flex items-center gap-1.5 rounded-lg bg-slate-900/40 px-2.5 py-1.5 backdrop-blur-md border border-blue-400/20">
            <svg class="h-4 w-4 text-blue-400 drop-shadow flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
            </svg>
            <span class="text-[10px] font-semibold uppercase tracking-wide text-gray-300">Royalty:</span>
            <span itemprop="interestRate" class="text-base font-bold text-blue-300 drop-shadow-md">{{ $wallet->royalty_mint }}%</span>
        </div>

        {{-- Royalty Rebind --}}
        <div class="flex items-center gap-1.5 rounded-lg bg-slate-900/40 px-2.5 py-1.5 backdrop-blur-md border border-purple-400/20">
            <svg class="h-4 w-4 text-purple-400 drop-shadow flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            <span class="text-[10px] font-semibold uppercase tracking-wide text-gray-300">Resale:</span>
            <span itemprop="interestRate" class="text-base font-bold text-purple-300 drop-shadow-md">{{ $wallet->royalty_rebind }}%</span>
        </div>
    </div>

    {{-- Owner section (cardholder name) --}}
    <div class="relative z-10 border-t border-slate-400/20 pt-2.5 mt-auto">
        <div class="mb-1 text-[10px] font-semibold uppercase tracking-wider text-gray-400 drop-shadow">Card Holder</div>
        <div class="flex items-center gap-2">
            @if ($wallet->user)
                <img itemprop="image" class="h-8 w-8 rounded-full border-2 border-white/30 shadow-lg" 
                    src="{{ $wallet->user->profile_photo_url }}"
                    alt="{{ $wallet->user->name }}">
                <div class="flex-1 min-w-0">
                    <p class="font-bold text-white drop-shadow-md text-sm truncate" itemprop="name">
                        {{ $wallet->user->name }} {{ $wallet->user->last_name }}
                    </p>
                    <p class="flex items-center gap-1 text-[10px] text-gray-400 drop-shadow-sm">
                        <span itemprop="category" class="uppercase tracking-wide font-medium truncate">{{ $wallet->platform_role }}</span>
                    </p>
                </div>
            @else
                <div class="flex h-8 w-8 items-center justify-center rounded-full border-2 border-dashed border-gray-500 bg-slate-800/60">
                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                    </svg>
                </div>
                <span class="text-sm font-semibold text-gray-400 drop-shadow">{{ __('Unassigned') }}</span>
            @endif
        </div>
    </div>

    </div>{{-- Close content wrapper --}}

    {{-- EMV Chip stripe (BOTTOM - golden band like real credit cards) --}}
    <div class="absolute left-0 right-0 bottom-0 h-8 {{ !$canCreateWallet || in_array($wallet->platform_role, ['Natan', 'EPP']) 
        ? 'bg-gradient-to-b from-amber-700/40 via-yellow-600/40 to-amber-700/40' 
        : 'bg-gradient-to-b from-amber-600/50 via-yellow-500/50 to-amber-600/50' }} shadow-inner">
        {{-- Chip pattern --}}
        <div class="absolute inset-0 opacity-30" style="background: repeating-linear-gradient(
            90deg,
            transparent,
            transparent 8px,
            rgba(255,255,255,0.1) 8px,
            rgba(255,255,255,0.1) 10px
        );"></div>
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
