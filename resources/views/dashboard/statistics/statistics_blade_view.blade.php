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
                {{-- Portfolio Statistics Section - I COMPONENTI ESISTENTI SPOSTATI DAL PORTFOLIO --}}
                <div class="mb-8">
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
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let statisticsData = null;

        // Get period from URL parameter or use default
        const urlParams = new URLSearchParams(window.location.search);
        let currentTimePeriod = urlParams.get('period') || '{{ $period ?? 'day' }}';

        // Elements
        const loadingOverlay = document.getElementById('loading-overlay');
        const errorContainer = document.getElementById('error-container');
        const errorMessage = document.getElementById('error-message');
        const statisticsContent = document.getElementById('statistics-content');
        const refreshButton = document.getElementById('refresh-stats');
        const lastUpdated = document.getElementById('last-updated');
        const timeFilterButtons = document.querySelectorAll('.time-filter-btn');

        // Set active button based on current period
        updateActiveTimeFilter();

        // Load statistics on page load
        loadStatistics();

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
            // Portfolio statistics are handled by Laravel components
            // No client-side rendering needed
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
