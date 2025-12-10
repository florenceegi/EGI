{{--
    Gold Bar Info Component

    Autonomous component for displaying gold bar EGI information
    with real-time price quotation.

    Usage:
    <x-gold-bar-info :egi="$egi" currency="EUR" />
    <x-gold-bar-info :egi="$egi" :showDetails="false" size="compact" />

    @author Fabio Cherici
    @version 1.0.0
    @date 2025-12-08
--}}

@if ($egi && $isGoldBar)
    <div
        {{ $attributes->merge([
            'class' =>
                'gold-bar-info rounded-lg border border-yellow-400/30 bg-gradient-to-br from-yellow-50 to-amber-50 dark:from-yellow-900/20 dark:to-amber-900/20 ' .
                $getSizeClasses(),
        ]) }}>
        {{-- Header with Gold Icon --}}
        <div class="mb-4 flex items-center gap-3">
            <div
                class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-yellow-400 to-amber-500 shadow-lg">
                <span class="text-xl">🥇</span>
            </div>
            <div>
                <h3 class="font-bold text-yellow-800 dark:text-yellow-200">
                    {{ __('gold_bar.title') }}
                </h3>
                <p class="text-xs text-yellow-600 dark:text-yellow-400">
                    {{ __('gold_bar.subtitle') }}
                </p>
            </div>
        </div>

        @if ($error)
            {{-- Error State --}}
            <div
                class="flex items-center gap-2 rounded-lg bg-red-100 p-3 text-red-700 dark:bg-red-900/30 dark:text-red-300">
                <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-sm">{{ $error }}</span>
            </div>
        @elseif($goldValue)
            {{-- Gold Properties --}}
            @if ($showDetails)
                <div class="mb-4 grid grid-cols-2 gap-3 text-sm">
                    {{-- Weight --}}
                    <div class="flex flex-col">
                        <span class="text-xs uppercase tracking-wider text-yellow-600 dark:text-yellow-400">
                            {{ __('gold_bar.weight') }}
                        </span>
                        <span class="font-semibold text-yellow-800 dark:text-yellow-200">
                            {{ number_format($egi->getGoldWeight(), 2) }} {{ $getWeightUnitLabel() }}
                        </span>
                    </div>

                    {{-- Purity --}}
                    <div class="flex flex-col">
                        <span class="text-xs uppercase tracking-wider text-yellow-600 dark:text-yellow-400">
                            {{ __('gold_bar.purity') }}
                        </span>
                        <span class="font-semibold text-yellow-800 dark:text-yellow-200">
                            {{ $getPurityDescription() }}
                        </span>
                    </div>

                    {{-- Pure Gold Content --}}
                    <div class="col-span-2 flex flex-col">
                        <span class="text-xs uppercase tracking-wider text-yellow-600 dark:text-yellow-400">
                            {{ __('gold_bar.pure_gold') }}
                        </span>
                        <span class="font-semibold text-yellow-800 dark:text-yellow-200">
                            {{ number_format($goldValue['pure_gold_grams'], 2) }} {{ __('gold_bar.unit_grams') }}
                        </span>
                    </div>
                </div>

                {{-- Divider --}}
                <div class="my-4 border-t border-yellow-300/50 dark:border-yellow-600/30"></div>

                {{-- Price Breakdown --}}
                <div class="mb-4 space-y-2 text-sm">
                    {{-- Gold Spot Price --}}
                    <div class="flex items-center justify-between">
                        <span class="text-yellow-600 dark:text-yellow-400">
                            {{ __('gold_bar.gold_price') }} ({{ __('gold_bar.per_gram') }})
                        </span>
                        <span class="font-medium text-yellow-800 dark:text-yellow-200">
                            {{ $formatCurrency($goldValue['gold_price_per_gram']) }}
                        </span>
                    </div>

                    {{-- Base Value --}}
                    <div class="flex items-center justify-between">
                        <span class="text-yellow-600 dark:text-yellow-400">
                            {{ __('gold_bar.base_value') }}
                        </span>
                        <span class="font-medium text-yellow-800 dark:text-yellow-200">
                            {{ $formatCurrency($goldValue['base_value']) }}
                        </span>
                    </div>

                    {{-- Margin (if any) --}}
                    @if ($goldValue['margin_applied'] > 0)
                        <div class="flex items-center justify-between">
                            <span class="text-yellow-600 dark:text-yellow-400">
                                {{ __('gold_bar.margin') }}
                            </span>
                            <span class="font-medium text-yellow-800 dark:text-yellow-200">
                                +{{ $formatCurrency($goldValue['margin_applied']) }}
                            </span>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Indicative Value (Always Shown) --}}
            <div
                class="flex items-center justify-between rounded-lg bg-gradient-to-r from-yellow-200/50 to-amber-200/50 p-3 dark:from-yellow-800/30 dark:to-amber-800/30">
                <span class="font-semibold text-yellow-700 dark:text-yellow-300">
                    {{ __('gold_bar.indicative_value') }}
                </span>
                <span class="text-xl font-bold text-yellow-900 dark:text-yellow-100">
                    {{ $formatCurrency($goldValue['final_value']) }}
                </span>
            </div>

            {{-- Disclaimer --}}
            <div class="mt-4 text-xs leading-relaxed text-yellow-600/80 dark:text-yellow-400/60">
                <p>{{ __('gold_bar.disclaimer') }}</p>
                @if ($goldValue['price_timestamp'])
                    <p class="mt-1 flex items-center gap-1">
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ __('gold_bar.price_updated_at') }}:
                        {{ $goldValue['price_timestamp']->format('H:i') }}
                        @if ($goldValue['price_source'] ?? null)
                            <span class="text-yellow-500">
                                ({{ __('gold_bar.price_source') }}: {{ $goldValue['price_source'] }})
                            </span>
                        @endif
                    </p>
                @endif
            </div>

            {{-- Refresh Button (only for authenticated users / creators) --}}
            @auth
                <div class="mt-4 pt-3 border-t border-yellow-300/30 dark:border-yellow-600/20"
                     x-data="goldPriceRefresh({ 
                         currency: '{{ $currency }}',
                         refreshCost: {{ \App\Services\GoldPriceService::REFRESH_COST_EGILI }},
                         egiId: {{ $egi->id }}
                     })">
                    <div class="flex items-center justify-between gap-2">
                        <div class="text-xs text-yellow-600 dark:text-yellow-400">
                            <span x-text="nextRefreshText">{{ __('gold_bar.next_refresh', ['time' => '...']) }}</span>
                        </div>
                        <button 
                            @click="confirmRefresh()"
                            :disabled="isRefreshing"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg
                                   bg-yellow-500 hover:bg-yellow-600 text-white
                                   disabled:opacity-50 disabled:cursor-not-allowed
                                   transition-colors duration-200"
                        >
                            <svg x-show="!isRefreshing" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            <svg x-show="isRefreshing" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>{{ __('gold_bar.refresh_button') }}</span>
                            <span class="text-yellow-200">({{ \App\Services\GoldPriceService::REFRESH_COST_EGILI }} Egili)</span>
                        </button>
                    </div>
                    
                    {{-- Confirmation Modal --}}
                    <div x-show="showConfirmModal" 
                         x-cloak
                         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
                         @click.self="showConfirmModal = false">
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl p-6 max-w-sm mx-4"
                             @click.stop>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">
                                {{ __('gold_bar.refresh_confirm_title') }}
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                {{ __('gold_bar.refresh_confirm_message', ['cost' => \App\Services\GoldPriceService::REFRESH_COST_EGILI]) }}
                            </p>
                            <div class="flex gap-3">
                                <button @click="showConfirmModal = false"
                                        class="flex-1 px-4 py-2 text-sm font-medium rounded-lg
                                               bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600
                                               text-gray-800 dark:text-gray-200 transition-colors">
                                    {{ __('gold_bar.refresh_cancel') }}
                                </button>
                                <button @click="executeRefresh()"
                                        :disabled="isRefreshing"
                                        class="flex-1 px-4 py-2 text-sm font-medium rounded-lg
                                               bg-yellow-500 hover:bg-yellow-600 text-white
                                               disabled:opacity-50 transition-colors">
                                    {{ __('gold_bar.refresh_confirm_button', ['cost' => \App\Services\GoldPriceService::REFRESH_COST_EGILI]) }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endauth
        @else
            {{-- Loading State --}}
            <div class="flex animate-pulse items-center gap-2 p-3">
                <div class="h-4 w-4 animate-bounce rounded-full bg-yellow-400"></div>
                <span class="text-yellow-600 dark:text-yellow-400">{{ __('gold_bar.loading') }}</span>
            </div>
        @endif
    </div>
@elseif($egi && !$isGoldBar)
    {{-- Not a Gold Bar (optional: show nothing or a message) --}}
    {{-- Uncomment below to show a message when component is used on non-gold bar EGI --}}
    {{--
    <div class="text-sm text-gray-500 dark:text-gray-400">
        {{ __('gold_bar.not_gold_bar') }}
    </div>
    --}}
@endif
