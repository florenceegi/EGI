{{-- Mint Statistics Panel (Phase 2) --}}
<div class="mb-8">
    <h2 class="mb-6 flex items-center text-2xl font-bold text-white">
        <svg class="mr-3 h-8 w-8 text-oro-fiorentino" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        {{ __('statistics.mint_statistics') }}
    </h2>

    {{-- Summary KPI Cards --}}
    <div class="mb-8 grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
        {{-- Total Mints --}}
        <div class="rounded-xl border border-gray-700 bg-gray-800 bg-opacity-50 p-6 backdrop-blur-sm transition-all hover:border-oro-fiorentino">
            <div class="mb-2 flex items-center justify-between">
                <span class="text-sm text-gray-400">{{ __('statistics.total_mints') }}</span>
                <svg class="h-5 w-5 text-oro-fiorentino" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                </svg>
            </div>
            <div class="text-3xl font-bold text-white" id="mint-total-mints">0</div>
            <div class="mt-1 text-xs text-gray-500">{{ __('statistics.mints_count') }}</div>
        </div>

        {{-- Total Revenue --}}
        <div class="rounded-xl border border-gray-700 bg-gray-800 bg-opacity-50 p-6 backdrop-blur-sm transition-all hover:border-verde-rinascita">
            <div class="mb-2 flex items-center justify-between">
                <span class="text-sm text-gray-400">{{ __('statistics.total_revenue') }}</span>
                <svg class="h-5 w-5 text-verde-rinascita" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                </svg>
            </div>
            <div class="text-3xl font-bold text-white" id="mint-total-revenue">€0.00</div>
            <div class="mt-1 text-xs text-gray-500">{{ __('statistics.total_revenue_eur') }}</div>
        </div>

        {{-- Average Mint Price --}}
        <div class="rounded-xl border border-gray-700 bg-gray-800 bg-opacity-50 p-6 backdrop-blur-sm transition-all hover:border-blu-algoritmo">
            <div class="mb-2 flex items-center justify-between">
                <span class="text-sm text-gray-400">{{ __('statistics.avg_mint_price') }}</span>
                <svg class="h-5 w-5 text-blu-algoritmo" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                </svg>
            </div>
            <div class="text-3xl font-bold text-white" id="mint-avg-price">€0.00</div>
            <div class="mt-1 text-xs text-gray-500">{{ __('statistics.avg_mint_price') }}</div>
        </div>

        {{-- Collections with Mints --}}
        <div class="rounded-xl border border-gray-700 bg-gray-800 bg-opacity-50 p-6 backdrop-blur-sm transition-all hover:border-viola-innovazione">
            <div class="mb-2 flex items-center justify-between">
                <span class="text-sm text-gray-400">{{ __('statistics.collections') }}</span>
                <svg class="h-5 w-5 text-viola-innovazione" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
            </div>
            <div class="text-3xl font-bold text-white" id="mint-collections-count">0</div>
            <div class="mt-1 text-xs text-gray-500">{{ __('statistics.collections') }}</div>
        </div>
    </div>

    {{-- Revenue by Collection --}}
    <div class="mb-8">
        <div class="rounded-xl border border-gray-700 bg-gray-800 bg-opacity-50 p-6 backdrop-blur-sm">
            <h3 class="mb-4 text-lg font-semibold text-white">{{ __('statistics.mint_revenue_by_collection') }}</h3>
            
            <div id="mint-by-collection-container" class="space-y-3">
                <div class="text-center text-gray-400">{{ __('statistics.no_mint_data') }}</div>
            </div>
        </div>
    </div>

    {{-- Revenue by User Type --}}
    <div class="mb-8">
        <div class="rounded-xl border border-gray-700 bg-gray-800 bg-opacity-50 p-6 backdrop-blur-sm">
            <h3 class="mb-4 text-lg font-semibold text-white">{{ __('statistics.mint_revenue_by_user_type') }}</h3>
            
            <div id="mint-by-user-type-container" class="space-y-3">
                <div class="text-center text-gray-400">{{ __('statistics.no_mint_data') }}</div>
            </div>
        </div>
    </div>
</div>
