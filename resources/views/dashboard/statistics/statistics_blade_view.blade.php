<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('menu.statistics') }}
        </h2>
    </x-slot>

    {{-- Main Content --}}
    <div class="min-h-screen bg-gradient-to-br from-gray-900 via-purple-900 to-violet-800">
        <div class="container mx-auto px-4 py-8">
            {{-- Header Section --}}
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <h1 class="mb-2 text-4xl font-bold text-white">
                        {{ __('menu.statistics') }}
                    </h1>
                    <p class="text-gray-300">
                        {{ __('statistics.dashboard_subtitle') }}
                    </p>
                </div>

                {{-- Actions --}}
                <div class="flex items-center space-x-4">
                    <button id="refresh-stats"
                        class="flex items-center space-x-2 rounded-lg bg-blue-600 px-4 py-2 text-white transition-colors duration-200 hover:bg-blue-700">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        <span>{{ __('statistics.refresh') }}</span>
                    </button>

                    <div class="text-sm text-gray-400" id="last-updated">
                        {{ __('statistics.loading') }}...
                    </div>
                </div>
            </div>

            {{-- Loading State --}}
            <div id="loading-overlay"
                class="fixed inset-0 z-50 flex hidden items-center justify-center bg-black bg-opacity-50">
                <div class="flex items-center space-x-4 rounded-lg bg-white p-6">
                    <div class="h-8 w-8 animate-spin rounded-full border-b-2 border-blue-600"></div>
                    <span class="text-gray-700">{{ __('statistics.calculating') }}...</span>
                </div>
            </div>

            {{-- Error State --}}
            <div id="error-container" class="mb-6 hidden">
                <div class="rounded-lg border border-red-700 bg-red-900 px-4 py-3 text-red-100">
                    <div class="flex items-center">
                        <svg class="mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd" />
                        </svg>
                        <span id="error-message">{{ __('statistics.error_loading') }}</span>
                    </div>
                </div>
            </div>

            {{-- Time Filter Bar --}}
            <div class="mb-8">
                <div class="rounded-xl border border-gray-700 bg-gray-800 bg-opacity-50 p-6 backdrop-blur-sm">
                    <h3 class="mb-4 text-lg font-semibold text-white">
                        {{ __('statistics.time_period') }}
                    </h3>
                    <div class="flex flex-wrap gap-2">
                        <button
                            class="time-filter-btn active rounded-lg border px-4 py-2 text-sm font-medium transition-all duration-200"
                            data-period="day">
                            {{ __('statistics.period_day') }}
                        </button>
                        <button
                            class="time-filter-btn rounded-lg border px-4 py-2 text-sm font-medium transition-all duration-200"
                            data-period="week">
                            {{ __('statistics.period_week') }}
                        </button>
                        <button
                            class="time-filter-btn rounded-lg border px-4 py-2 text-sm font-medium transition-all duration-200"
                            data-period="month">
                            {{ __('statistics.period_month') }}
                        </button>
                        <button
                            class="time-filter-btn rounded-lg border px-4 py-2 text-sm font-medium transition-all duration-200"
                            data-period="year">
                            {{ __('statistics.period_year') }}
                        </button>
                        <button
                            class="time-filter-btn rounded-lg border px-4 py-2 text-sm font-medium transition-all duration-200"
                            data-period="all">
                            {{ __('statistics.period_all') }}
                        </button>
                    </div>
                </div>
            </div>

            {{-- Statistics Content --}}
            <div id="statistics-content" class="hidden">
                {{-- Phase 2: Dual Source Navigation Tabs with Force Refresh --}}
                <div class="mb-8">
                    <div class="mb-4 flex items-center justify-between">
                        <div class="flex flex-1 space-x-2 rounded-xl bg-gray-800 bg-opacity-50 p-2 backdrop-blur-sm">
                            <button
                                class="stats-tab-btn flex flex-1 items-center justify-center space-x-2 rounded-lg px-4 py-3 text-sm font-medium transition-all duration-200"
                                data-tab="mints" role="tab" aria-selected="true" aria-controls="mints-panel">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>{{ __('statistics.mints_tab') }}</span>
                            </button>
                            <button
                                class="stats-tab-btn flex flex-1 items-center justify-center space-x-2 rounded-lg px-4 py-3 text-sm font-medium transition-all duration-200"
                                data-tab="reservations" role="tab" aria-selected="false"
                                aria-controls="reservations-panel">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                <span>{{ __('statistics.reservations_tab') }}</span>
                            </button>
                            <button
                                class="stats-tab-btn flex flex-1 items-center justify-center space-x-2 rounded-lg px-4 py-3 text-sm font-medium transition-all duration-200"
                                data-tab="comparison" role="tab" aria-selected="false" aria-controls="comparison-panel">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                <span>{{ __('statistics.comparison_tab') }}</span>
                            </button>
                        </div>

                        {{-- Force Refresh Button --}}
                        <button id="force-refresh-btn"
                            class="ml-4 flex items-center space-x-2 rounded-lg border border-oro-fiorentino bg-gray-800 bg-opacity-50 px-4 py-3 text-sm font-medium text-oro-fiorentino backdrop-blur-sm transition-all duration-200 hover:bg-oro-fiorentino hover:text-gray-900"
                            title="{{ __('statistics.force_refresh_tooltip') }}">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            <span>{{ __('statistics.force_refresh') }}</span>
                        </button>
                    </div>
                </div>

                {{-- Mints Tab Panel --}}
                <div id="mints-panel" class="stats-tab-panel" role="tabpanel" aria-labelledby="mints-tab">
                    @include('dashboard.statistics.partials.mints-statistics')
                </div>

                {{-- Reservations Tab Panel --}}
                <div id="reservations-panel" class="stats-tab-panel hidden" role="tabpanel"
                    aria-labelledby="reservations-tab">
                    @include('dashboard.statistics.partials.reservations-statistics')
                </div>

                {{-- Comparison Tab Panel --}}
                <div id="comparison-panel" class="stats-tab-panel hidden" role="tabpanel"
                    aria-labelledby="comparison-tab">
                    @include('dashboard.statistics.partials.comparison-statistics')
                </div>

                {{-- Portfolio Statistics Section - I COMPONENTI ESISTENTI SPOSTATI DAL PORTFOLIO --}}
                <div class="mb-8 mt-12">
                    <h2 class="mb-6 flex items-center text-2xl font-bold text-white">
                        <svg class="text-oro-fiorentino mr-3 h-8 w-8" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        {{ __('statistics.portfolio_statistics') }}
                    </h2>

                    {{-- Row 1: Earnings & Monthly Trends --}}
                    <div class="mb-8 grid grid-cols-1 gap-8 lg:grid-cols-2">
                        <x-stats.earnings-widget :creatorId="auth()->id()" :period="$period ?? 'month'" />
                        <x-stats.monthly-trend-chart :creatorId="auth()->id()" :monthlyTrend="[]" :period="$period ?? 'month'" />
                    </div>

                    {{-- Row 2: Collection Performance & Engagement --}}
                    <div class="mb-8 grid grid-cols-1 gap-8 lg:grid-cols-2">
                        <x-stats.collection-performance-widget :creatorId="auth()->id()" :collectionPerformance="[]" :period="$period ?? 'month'" />
                        <x-stats.engagement-widget :creatorId="auth()->id()" :engagement="[]" :period="$period ?? 'month'" />
                    </div>

                    {{-- Row 3: Role-based Earnings & Holders Summary --}}
                    <div class="mb-8 grid grid-cols-1 gap-8 lg:grid-cols-2">
                        <x-stats.role-earnings-widget :user-id="auth()->id()" :period="$period ?? 'month'" />
                        <x-stats.holders-summary :creatorId="auth()->id()" :period="$period ?? 'month'" />
                    </div>

                    {{-- Row 4: Likes Analytics --}}
                    <div class="mb-8 grid grid-cols-1 gap-8 lg:grid-cols-2">
                        <x-stats.like-received-analytics-widget :userId="auth()->id()" :period="$period ?? 'month'" />
                        <x-stats.likes-given-analytics-widget :userId="auth()->id()" :period="$period ?? 'month'" />
                    </div>
                </div>
            </div>

            {{-- GDPR Placeholder --}}
            <div class="mt-8 rounded-xl border border-yellow-700 bg-yellow-900 bg-opacity-50 p-6">
                <div class="flex items-center space-x-3">
                    <svg class="h-6 w-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.314 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                    <div>
                        <h4 class="font-medium text-yellow-200">{{ __('statistics.gdpr_check') }}</h4>
                        <p class="text-sm text-yellow-300">{{ __('statistics.gdpr_coming_soon') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

{{-- JavaScript for Statistics Loading --}}
<style>
    /* Time Filter Buttons Styling */
    .time-filter-btn {
        background-color: rgba(75, 85, 99, 0.5);
        border-color: rgba(156, 163, 175, 0.3);
        color: rgb(209, 213, 219);
    }

    .time-filter-btn:hover {
        background-color: rgba(99, 102, 241, 0.3);
        border-color: rgba(99, 102, 241, 0.5);
        color: white;
    }

    .time-filter-btn.active {
        background-color: rgb(99, 102, 241);
        border-color: rgb(99, 102, 241);
        color: white;
        box-shadow: 0 4px 14px 0 rgba(99, 102, 241, 0.4);
    }

    .time-filter-btn.active:hover {
        background-color: rgb(79, 70, 229);
        border-color: rgb(79, 70, 229);
    }

    /* Stats Tab Buttons Styling */
    .stats-tab-btn {
        background-color: rgba(75, 85, 99, 0.3);
        border: 1px solid rgba(156, 163, 175, 0.2);
        color: rgb(209, 213, 219);
    }

    .stats-tab-btn:hover {
        background-color: rgba(212, 165, 116, 0.2);
        border-color: rgba(212, 165, 116, 0.3);
        color: white;
    }

    .stats-tab-btn[aria-selected="true"] {
        background: linear-gradient(135deg, #D4A574 0%, #B8936A 100%);
        border-color: #D4A574;
        color: white;
        box-shadow: 0 4px 14px 0 rgba(212, 165, 116, 0.4);
    }

    .stats-tab-btn[aria-selected="true"]:hover {
        background: linear-gradient(135deg, #C09563 0%, #A88259 100%);
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let statisticsData = null;
        // Restore last active tab from localStorage or default to 'mints'
        let currentTab = localStorage.getItem('stats_active_tab') || 'mints';

        // Get period from URL parameter or use default
        const urlParams = new URLSearchParams(window.location.search);
        let currentTimePeriod = urlParams.get('period') || '{{ $period ?? 'day' }}';

        // Elements
        const loadingOverlay = document.getElementById('loading-overlay');
        const errorContainer = document.getElementById('error-container');
        const errorMessage = document.getElementById('error-message');
        const statisticsContent = document.getElementById('statistics-content');
        const refreshButton = document.getElementById('refresh-stats');
        const forceRefreshButton = document.getElementById('force-refresh-btn');
        const lastUpdated = document.getElementById('last-updated');
        const timeFilterButtons = document.querySelectorAll('.time-filter-btn');
        const statsTabButtons = document.querySelectorAll('.stats-tab-btn');

        // Set active button based on current period
        updateActiveTimeFilter();

        // Restore active tab (must be done before loadStatistics)
        switchStatsTab(currentTab);

        // Load statistics on page load
        loadStatistics();

        // Stats tab handlers (Mints/Reservations/Comparison)
        statsTabButtons.forEach(button => {
            button.addEventListener('click', function() {
                const tabName = this.getAttribute('data-tab');
                switchStatsTab(tabName);
            });
        });

        // Time filter button handlers
        timeFilterButtons.forEach(button => {
            button.addEventListener('click', function() {
                const newPeriod = this.getAttribute('data-period');
                if (newPeriod !== currentTimePeriod) {
                    currentTimePeriod = newPeriod;
                    updateActiveTimeFilter();

                    // Reload page with period parameter to update server-side components
                    const url = new URL(window.location);
                    url.searchParams.set('period', newPeriod);
                    window.location.href = url.toString();
                }
            });
        });

        // Refresh button handler
        refreshButton?.addEventListener('click', function() {
            loadStatistics(true);
        });

        // Force refresh button handler
        forceRefreshButton?.addEventListener('click', function() {
            // Visual feedback: disable button + loading state
            forceRefreshButton.disabled = true;
            forceRefreshButton.classList.add('opacity-50', 'cursor-not-allowed');
            
            const originalText = forceRefreshButton.querySelector('span').textContent;
            forceRefreshButton.querySelector('span').textContent = '{{ __("statistics.loading") }}...';

            // Force refresh with cache bypass
            loadStatistics(true).finally(() => {
                forceRefreshButton.disabled = false;
                forceRefreshButton.classList.remove('opacity-50', 'cursor-not-allowed');
                forceRefreshButton.querySelector('span').textContent = originalText;
            });
        });

        /**
         * Switch between stats tabs
         */
        function switchStatsTab(tabName) {
            currentTab = tabName;

            // Save active tab to localStorage for persistence
            localStorage.setItem('stats_active_tab', tabName);

            // Update tab buttons
            statsTabButtons.forEach(btn => {
                const isActive = btn.getAttribute('data-tab') === tabName;
                btn.setAttribute('aria-selected', isActive);
            });

            // Update tab panels
            document.querySelectorAll('.stats-tab-panel').forEach(panel => {
                panel.classList.add('hidden');
            });
            document.getElementById(`${tabName}-panel`)?.classList.remove('hidden');
        }

        /**
         * Update active time filter button
         */
        function updateActiveTimeFilter() {
            timeFilterButtons.forEach(btn => {
                btn.classList.remove('active');
                if (btn.getAttribute('data-period') === currentTimePeriod) {
                    btn.classList.add('active');
                }
            });
        }

        /**
         * Load statistics from API
         */
        async function loadStatistics(forceRefresh = false) {
            showLoading();
            hideError();

            try {
                const params = new URLSearchParams({
                    period: currentTimePeriod
                });

                if (forceRefresh) {
                    params.append('refresh', '1');
                }

                const url = `/dashboard/statistics/data?${params.toString()}`;
                const response = await fetch(url, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                            ?.getAttribute('content') || ''
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }

                const result = await response.json();

                if (result.success) {
                    statisticsData = result.data;
                    renderStatistics(statisticsData);
                    updateLastUpdated(statisticsData.generated_at);
                    showContent();
                } else {
                    throw new Error(result.message || 'Failed to load statistics');
                }

            } catch (error) {
                console.error('Statistics loading error:', error);
                showError(error.message);
            } finally {
                hideLoading();
            }
        }

        /**
         * Render statistics data to UI
         */
        function renderStatistics(data) {
            // Render Mint Statistics
            renderMintStatistics(data.mints);

            // Render Reservation Statistics
            renderReservationStatistics(data.reservations, data.amounts, data.epp_potential);

            // Render Comparison Statistics
            renderComparisonStatistics(data.dual_source_comparison);

            // Portfolio statistics are handled by Laravel components
        }

        /**
         * Render Mint Statistics Tab
         */
        function renderMintStatistics(mints) {
            if (!mints) return;

            // Update KPI cards
            document.getElementById('mint-total-mints').textContent = mints.total_mints || 0;
            document.getElementById('mint-total-revenue').textContent = formatCurrency(mints
                .total_revenue_eur || 0);

            const avgPrice = mints.total_mints > 0 ? mints.total_revenue_eur / mints.total_mints : 0;
            document.getElementById('mint-avg-price').textContent = formatCurrency(avgPrice);
            document.getElementById('mint-collections-count').textContent = mints.by_collection?.length || 0;

            // Render by collection
            const byCollectionContainer = document.getElementById('mint-by-collection-container');
            if (mints.by_collection && mints.by_collection.length > 0) {
                byCollectionContainer.innerHTML = mints.by_collection.map(collection => `
                    <div class="flex items-center justify-between rounded-lg bg-black bg-opacity-20 p-4">
                        <div class="flex-1">
                            <h4 class="font-medium text-white">${escapeHtml(collection.collection_name)}</h4>
                            <div class="mt-1 flex items-center space-x-4 text-sm text-gray-400">
                                <span>${collection.mints_count} mints</span>
                                <span>•</span>
                                <span>Avg: ${formatCurrency(collection.avg_price_eur)}</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-xl font-bold text-verde-rinascita">${formatCurrency(collection.revenue_eur)}</div>
                        </div>
                    </div>
                `).join('');
            } else {
                byCollectionContainer.innerHTML =
                    '<div class="text-center text-gray-400">{{ __('statistics.no_mint_data') }}</div>';
            }

            // Render by user type
            const byUserTypeContainer = document.getElementById('mint-by-user-type-container');
            if (mints.by_user_type && mints.by_user_type.length > 0) {
                byUserTypeContainer.innerHTML = mints.by_user_type.map(userType => {
                    const colorClass = getUserTypeColor(userType.user_type);
                    return `
                        <div class="flex items-center justify-between rounded-lg bg-black bg-opacity-20 p-4">
                            <div class="flex items-center space-x-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full ${colorClass}">
                                    <span class="text-sm font-bold uppercase text-white">${userType.user_type.substring(0, 2)}</span>
                                </div>
                                <div>
                                    <h4 class="font-medium text-white">${escapeHtml(userType.user_type)}</h4>
                                    <div class="text-sm text-gray-400">${userType.distributions_count} distributions</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-xl font-bold text-white">${formatCurrency(userType.amount_eur)}</div>
                                <div class="text-sm text-gray-400">${userType.percentage_of_total}%</div>
                            </div>
                        </div>
                    `;
                }).join('');
            } else {
                byUserTypeContainer.innerHTML =
                    '<div class="text-center text-gray-400">{{ __('statistics.no_mint_data') }}</div>';
            }
        }

        /**
         * Render Reservation Statistics Tab
         */
        function renderReservationStatistics(reservations, amounts, eppPotential) {
            if (!reservations) {
                console.error('No reservations data provided');
                return;
            }

            // Update KPI cards
            const totalElement = document.getElementById('reservation-total');
            const forecastElement = document.getElementById('reservation-forecast');
            const strongElement = document.getElementById('reservation-strong');
            const weakElement = document.getElementById('reservation-weak');

            if (totalElement) totalElement.textContent = reservations.total || 0;
            if (forecastElement) forecastElement.textContent = formatCurrency(amounts?.total_eur || 0);
            if (strongElement) strongElement.textContent = reservations.strong || 0;
            if (weakElement) weakElement.textContent = reservations.weak || 0;

            // Render by collection
            const byCollectionContainer = document.getElementById('reservation-by-collection-container');
            if (reservations.by_collection && reservations.by_collection.length > 0) {
                byCollectionContainer.innerHTML = reservations.by_collection.map(collection => `
                    <div class="flex items-center justify-between rounded-lg bg-black bg-opacity-20 p-4">
                        <div class="flex-1">
                            <h4 class="font-medium text-white">${escapeHtml(collection.collection_name)}</h4>
                            <div class="mt-1 flex items-center space-x-4 text-sm text-gray-400">
                                <span>${collection.total_reservations} reservations</span>
                                <span>•</span>
                                <span>${collection.strong_reservations} strong</span>
                                <span>•</span>
                                <span>${collection.weak_reservations} weak</span>
                            </div>
                        </div>
                    </div>
                `).join('');
            } else {
                byCollectionContainer.innerHTML =
                    '<div class="text-center text-gray-400">{{ __('statistics.no_reservations') }}</div>';
            }

            // Render EPP potential
            const eppContainer = document.getElementById('reservation-epp-container');
            if (eppPotential?.by_collection && eppPotential.by_collection.length > 0) {
                eppContainer.innerHTML = eppPotential.by_collection.map(collection => `
                    <div class="flex items-center justify-between rounded-lg bg-black bg-opacity-20 p-4">
                        <div class="flex-1">
                            <h4 class="font-medium text-white">${escapeHtml(collection.collection_name)}</h4>
                            <div class="mt-1 text-sm text-gray-400">EPP ${collection.epp_percentage}%</div>
                        </div>
                        <div class="text-right">
                            <div class="text-xl font-bold text-verde-rinascita">${formatCurrency(collection.epp_quota)}</div>
                        </div>
                    </div>
                `).join('');
            } else {
                eppContainer.innerHTML =
                    '<div class="text-center text-gray-400">{{ __('statistics.no_epp_data') }}</div>';
            }
        }

        /**
         * Render Comparison Statistics Tab
         */
        function renderComparisonStatistics(comparison) {
            if (!comparison) return;

            // Update KPI cards
            document.getElementById('comparison-conversion-rate').textContent = comparison.conversion_rate +
                '%';
            document.getElementById('comparison-forecast').textContent = formatCurrency(comparison
                .forecast_vs_reality?.forecast_eur || 0);
            document.getElementById('comparison-reality').textContent = formatCurrency(comparison
                .forecast_vs_reality?.reality_eur || 0);

            const deltaEur = comparison.forecast_vs_reality?.delta_eur || 0;
            const deltaPercentage = comparison.forecast_vs_reality?.delta_percentage || 0;
            const deltaElement = document.getElementById('comparison-delta');
            const deltaPercentageElement = document.getElementById('comparison-delta-percentage');

            deltaElement.textContent = formatCurrency(Math.abs(deltaEur));
            deltaPercentageElement.textContent = Math.abs(deltaPercentage) + '%';

            // Color based on delta (positive = green, negative = red)
            if (deltaEur >= 0) {
                deltaElement.classList.remove('text-red-400');
                deltaElement.classList.add('text-verde-rinascita');
            } else {
                deltaElement.classList.remove('text-verde-rinascita');
                deltaElement.classList.add('text-red-400');
            }

            // Render by collection table
            const tableBody = document.querySelector('#comparison-by-collection-table tbody');
            if (comparison.by_collection && comparison.by_collection.length > 0) {
                tableBody.innerHTML = comparison.by_collection.map(collection => {
                    const deltaClass = collection.delta_eur >= 0 ? 'text-verde-rinascita' :
                        'text-red-400';
                    return `
                        <tr class="hover:bg-gray-700 hover:bg-opacity-30">
                            <td class="px-4 py-3 font-medium text-white">${escapeHtml(collection.collection_name)}</td>
                            <td class="px-4 py-3 text-right">${collection.reservations_count}</td>
                            <td class="px-4 py-3 text-right">${collection.mints_count}</td>
                            <td class="px-4 py-3 text-right">${formatCurrency(collection.forecast_eur)}</td>
                            <td class="px-4 py-3 text-right">${formatCurrency(collection.reality_eur)}</td>
                            <td class="px-4 py-3 text-right ${deltaClass}">${formatCurrency(collection.delta_eur)}</td>
                            <td class="px-4 py-3 text-right ${deltaClass}">${collection.delta_percentage}%</td>
                        </tr>
                    `;
                }).join('');
            } else {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-400">
                            {{ __('statistics.no_data') }}
                        </td>
                    </tr>
                `;
            }
        }

        /**
         * Get color class for user type
         */
        function getUserTypeColor(userType) {
            const colors = {
                'creator': 'bg-gradient-to-r from-oro-fiorentino to-orange-600',
                'epp': 'bg-gradient-to-r from-verde-rinascita to-green-700',
                'collector': 'bg-gradient-to-r from-blu-algoritmo to-blue-700',
                'commissioner': 'bg-gradient-to-r from-viola-innovazione to-purple-700',
            };
            return colors[userType.toLowerCase()] || 'bg-gradient-to-r from-gray-500 to-gray-600';
        }

        /**
         * Format currency
         */
        function formatCurrency(value) {
            return '€' + parseFloat(value).toLocaleString('it-IT', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        // Utility functions
        function showLoading() {
            loadingOverlay?.classList.remove('hidden');
        }

        function hideLoading() {
            loadingOverlay?.classList.add('hidden');
        }

        function showError(message) {
            if (errorMessage) errorMessage.textContent = message;
            errorContainer?.classList.remove('hidden');
            statisticsContent?.classList.add('hidden');
        }

        function hideError() {
            errorContainer?.classList.add('hidden');
        }

        function showContent() {
            statisticsContent?.classList.remove('hidden');
        }

        function updateLastUpdated(timestamp) {
            if (lastUpdated) {
                const date = new Date(timestamp);
                lastUpdated.textContent = `{{ __('statistics.last_updated') }}: ${date.toLocaleString()}`;
            }
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    });
</script>
