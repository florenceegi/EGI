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
                                {{ __('gold_bar.creator_margin') }}
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
                @php
                    $refreshCost = \App\Services\GoldPriceService::REFRESH_COST_EGILI;
                    $userBalance = auth()->user()->egili_balance ?? 0;
                    $canAfford = $userBalance >= $refreshCost;
                @endphp
                <div class="mt-4 border-t border-yellow-300/30 pt-3 dark:border-yellow-600/20"
                    id="gold-price-refresh-{{ $egi->id }}" data-currency="{{ $currency }}"
                    data-refresh-cost="{{ $refreshCost }}" data-user-balance="{{ $userBalance }}"
                    data-egi-id="{{ $egi->id }}">
                    <div class="flex items-center justify-between gap-2">
                        <div class="text-xs text-yellow-600 dark:text-yellow-400">
                            <span class="js-next-refresh-text">{{ __('gold_bar.next_refresh', ['time' => '...']) }}</span>
                        </div>
                        <button type="button"
                            class="js-refresh-btn inline-flex items-center gap-1.5 rounded-lg bg-yellow-500 px-3 py-1.5 text-xs font-medium text-white transition-colors duration-200 hover:bg-yellow-600 disabled:cursor-not-allowed disabled:opacity-50">
                            <svg class="js-refresh-icon h-3.5 w-3.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            <svg class="js-spinner-icon hidden h-3.5 w-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <span>{{ __('gold_bar.refresh_button') }}</span>
                            <span class="text-yellow-200">({{ $refreshCost }} Egili)</span>
                        </button>
                    </div>
                </div>

                {{-- Vanilla JS per Gold Price Refresh con Swal --}}
                <script>
                    // Aspetta che Swal sia disponibile
                    function initGoldPriceRefresh{{ $egi->id }}() {
                        if (typeof Swal === 'undefined') {
                            setTimeout(initGoldPriceRefresh{{ $egi->id }}, 100);
                            return;
                        }

                        const container = document.getElementById('gold-price-refresh-{{ $egi->id }}');
                        if (!container) return;

                        // Traduzioni (escape per JS)
                        const LANG = {
                            next_refresh: {!! json_encode(__('gold_bar.next_refresh', ['time' => ''])) !!},
                            insufficient_egili_title: {!! json_encode(__('gold_bar.insufficient_egili_title')) !!},
                            insufficient_egili_message: {!! json_encode(__('gold_bar.insufficient_egili_message')) !!},
                            required: {!! json_encode(__('gold_bar.required')) !!},
                            available: {!! json_encode(__('gold_bar.available')) !!},
                            missing: {!! json_encode(__('gold_bar.missing')) !!},
                            buy_egili_hint: {!! json_encode(__('gold_bar.buy_egili_hint')) !!},
                            buy_egili_button: {!! json_encode(__('gold_bar.buy_egili_button')) !!},
                            refresh_cancel: {!! json_encode(__('gold_bar.refresh_cancel')) !!},
                            refresh_confirm_title: {!! json_encode(__('gold_bar.refresh_confirm_title')) !!},
                            operation_cost: {!! json_encode(__('gold_bar.operation_cost')) !!},
                            your_balance: {!! json_encode(__('gold_bar.your_balance')) !!},
                            after_operation: {!! json_encode(__('gold_bar.after_operation')) !!},
                            egili_charged_on_success: {!! json_encode(__('gold_bar.egili_charged_on_success')) !!},
                            confirm_and_charge: {!! json_encode(__('gold_bar.confirm_and_charge')) !!},
                            refreshing_title: {!! json_encode(__('gold_bar.refreshing_title')) !!},
                            refreshing_message: {!! json_encode(__('gold_bar.refreshing_message')) !!},
                            refresh_success_title: {!! json_encode(__('gold_bar.refresh_success_title')) !!},
                            refresh_success: {!! json_encode(__('gold_bar.refresh_success')) !!},
                            refresh_error_title: {!! json_encode(__('gold_bar.refresh_error_title')) !!},
                            refresh_error: {!! json_encode(__('gold_bar.refresh_error')) !!},
                            refresh_network_error: {!! json_encode(__('gold_bar.refresh_network_error')) !!},
                            throttle_exceeded_title: {!! json_encode(__('gold_bar.throttle_exceeded_title')) !!},
                            throttle_exceeded: {!! json_encode(__('gold_bar.throttle_exceeded')) !!}
                        };

                        const currency = container.dataset.currency;
                        const refreshCost = parseInt(container.dataset.refreshCost);
                        let userBalance = parseInt(container.dataset.userBalance);
                        const egiId = container.dataset.egiId;
                        const refreshBtn = container.querySelector('.js-refresh-btn');
                        const refreshIcon = container.querySelector('.js-refresh-icon');
                        const spinnerIcon = container.querySelector('.js-spinner-icon');
                        const nextRefreshText = container.querySelector('.js-next-refresh-text');

                        let isRefreshing = false;

                        // Update refresh info
                        function updateRefreshInfo() {
                            fetch('/api/gold/refresh-info?currency=' + currency)
                                .then(function(response) {
                                    return response.json();
                                })
                                .then(function(data) {
                                    if (data.success && nextRefreshText) {
                                        nextRefreshText.textContent = LANG.next_refresh + data.data.next_auto_refresh;
                                    }
                                })
                                .catch(function(error) {
                                    console.error('Failed to fetch refresh info:', error);
                                });
                        }

                        // Set loading state
                        function setLoading(loading) {
                            isRefreshing = loading;
                            refreshBtn.disabled = loading;
                            if (loading) {
                                refreshIcon.classList.add('hidden');
                                spinnerIcon.classList.remove('hidden');
                            } else {
                                refreshIcon.classList.remove('hidden');
                                spinnerIcon.classList.add('hidden');
                            }
                        }

                        // Handle refresh click
                        async function handleRefreshClick() {
                            if (isRefreshing) return;

                            // Step 1: Check balance
                            if (userBalance < refreshCost) {
                                // Insufficient balance - show error and offer to buy
                                Swal.fire({
                                    icon: 'error',
                                    title: LANG.insufficient_egili_title,
                                    html: '<p class="mb-3">' + LANG.insufficient_egili_message + '</p>' +
                                        '<div class="p-3 text-left border border-red-200 rounded bg-red-50">' +
                                        '<p class="text-sm"><strong>' + LANG.required + ':</strong> ' + refreshCost +
                                        ' Egili</p>' +
                                        '<p class="text-sm"><strong>' + LANG.available + ':</strong> ' + userBalance +
                                        ' Egili</p>' +
                                        '<p class="text-sm text-red-600"><strong>' + LANG.missing + ':</strong> ' + (
                                            refreshCost - userBalance) + ' Egili</p>' +
                                        '</div>' +
                                        '<p class="mt-3 text-xs text-gray-600">' + LANG.buy_egili_hint + '</p>',
                                    confirmButtonText: LANG.buy_egili_button,
                                    showCancelButton: true,
                                    cancelButtonText: LANG.refresh_cancel,
                                    confirmButtonColor: '#f97316'
                                }).then(function(result) {
                                    if (result.isConfirmed) {
                                        if (typeof window.openEgiliPurchaseModal === 'function') {
                                            window.openEgiliPurchaseModal();
                                        } else {
                                            window.location.href = '/egili/purchase/pricing';
                                        }
                                    }
                                });
                                return;
                            }

                            // Step 2: Show cost confirmation modal
                            const confirmResult = await Swal.fire({
                                title: LANG.refresh_confirm_title,
                                html: '<div class="bg-gradient-to-r from-yellow-50 to-amber-50 border-l-4 border-yellow-400 p-4 mb-4">' +
                                    '<div class="flex items-center mb-2">' +
                                    '<svg class="w-5 h-5 text-yellow-600 mr-2" fill="currentColor" viewBox="0 0 20 20">' +
                                    '<path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>' +
                                    '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>' +
                                    '</svg>' +
                                    '<p class="text-sm font-bold text-gray-800">' + LANG.operation_cost + '</p>' +
                                    '</div>' +
                                    '<p class="text-2xl font-bold text-yellow-600 mb-2">' + refreshCost + ' Egili</p>' +
                                    '<div class="flex items-center justify-between text-xs text-gray-600">' +
                                    '<span>' + LANG.your_balance + ': <strong>' + userBalance +
                                    ' Egili</strong></span>' +
                                    '<span>' + LANG.after_operation + ': <strong>' + (userBalance - refreshCost) +
                                    ' Egili</strong></span>' +
                                    '</div>' +
                                    '</div>' +
                                    '<p class="text-xs text-gray-500">' + LANG.egili_charged_on_success + '</p>',
                                icon: 'question',
                                showCancelButton: true,
                                confirmButtonColor: '#eab308',
                                cancelButtonColor: '#6b7280',
                                confirmButtonText: LANG.confirm_and_charge + ' ' + refreshCost + ' Egili',
                                cancelButtonText: LANG.refresh_cancel
                            });

                            if (!confirmResult.isConfirmed) return;

                            // Step 3: Execute refresh
                            Swal.fire({
                                title: LANG.refreshing_title,
                                html: '<p class="text-sm text-gray-600">' + LANG.refreshing_message + '</p>',
                                allowOutsideClick: false,
                                showConfirmButton: false,
                                didOpen: function() {
                                    Swal.showLoading();
                                }
                            });

                            setLoading(true);

                            const csrfToken = document.querySelector('meta[name="csrf-token"]');

                            fetch('/api/gold/refresh', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': csrfToken ? csrfToken.content : ''
                                    },
                                    body: JSON.stringify({
                                        currency: currency,
                                        egi_id: egiId
                                    })
                                })
                                .then(function(response) {
                                    return response.json().then(function(data) {
                                        return { ok: response.ok, status: response.status, data: data };
                                    });
                                })
                                .then(function(result) {
                                    if (result.ok && result.data.success) {
                                        // Aggiorna il saldo locale
                                        if (result.data.cost && result.data.cost.new_balance !== undefined) {
                                            userBalance = result.data.cost.new_balance;
                                        }
                                        Swal.fire({
                                            icon: 'success',
                                            title: LANG.refresh_success_title,
                                            text: result.data.message || LANG.refresh_success,
                                            confirmButtonColor: '#eab308'
                                        }).then(function() {
                                            window.location.reload();
                                        });
                                    } else {
                                        // Handle specific errors
                                        const errorIcon = result.status === 429 ? 'warning' : 'error';
                                        const errorTitle = result.status === 429 
                                            ? LANG.throttle_exceeded_title 
                                            : LANG.refresh_error_title;
                                        
                                        Swal.fire({
                                            icon: errorIcon,
                                            title: errorTitle,
                                            text: result.data.message || LANG.refresh_error,
                                            confirmButtonColor: result.status === 429 ? '#f97316' : '#ef4444'
                                        });
                                    }
                                })
                                .catch(function(error) {
                                    console.error('Refresh failed:', error);
                                    Swal.fire({
                                        icon: 'error',
                                        title: LANG.refresh_error_title,
                                        text: LANG.refresh_network_error,
                                        confirmButtonColor: '#ef4444'
                                    });
                                })
                                .finally(function() {
                                    setLoading(false);
                                });
                        }

                        // Event listener
                        refreshBtn.addEventListener('click', handleRefreshClick);

                        // Initial update and interval
                        updateRefreshInfo();
                        setInterval(updateRefreshInfo, 60000);
                    }

                    // Avvia inizializzazione
                    initGoldPriceRefresh{{ $egi->id }}();
                </script>
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
