<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            {{ __('invoices.title') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            
            {{-- Page Header --}}
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-white">
                    {{ __('invoices.my_invoices') }}
                </h1>
                <p class="mt-2 text-gray-300">
                    {{ __('invoices.subtitle') }}
                </p>
            </div>

            {{-- Success/Error Messages --}}
            @if (session('success'))
                <div class="mb-6 rounded-lg bg-green-900/30 border border-green-500/50 p-4 text-green-200">
                    <p class="font-medium">✓ {{ session('success') }}</p>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 rounded-lg bg-red-900/30 border border-red-500/50 p-4 text-red-200">
                    @foreach ($errors->all() as $error)
                        <p class="font-medium">✗ {{ $error }}</p>
                    @endforeach
                </div>
            @endif

            {{-- Tabs Navigation --}}
            <div class="mb-6 border-b border-gray-700">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <button
                        data-tab="sales"
                        class="tab-button {{ $activeTab === 'sales' ? 'active border-purple-500 text-purple-400' : 'border-transparent text-gray-400' }} whitespace-nowrap border-b-2 px-1 py-4 text-sm font-medium hover:border-purple-400 hover:text-purple-300 transition-colors"
                        onclick="switchTab('sales')"
                    >
                        📤 {{ __('invoices.tabs.sales') }}
                    </button>
                    
                    <button
                        data-tab="purchases"
                        class="tab-button {{ $activeTab === 'purchases' ? 'active border-purple-500 text-purple-400' : 'border-transparent text-gray-400' }} whitespace-nowrap border-b-2 px-1 py-4 text-sm font-medium hover:border-purple-400 hover:text-purple-300 transition-colors"
                        onclick="switchTab('purchases')"
                    >
                        📥 {{ __('invoices.tabs.purchases') }}
                    </button>
                    
                    <button
                        data-tab="aggregations"
                        class="tab-button {{ $activeTab === 'aggregations' ? 'active border-purple-500 text-purple-400' : 'border-transparent text-gray-400' }} whitespace-nowrap border-b-2 px-1 py-4 text-sm font-medium hover:border-purple-400 hover:text-purple-300 transition-colors"
                        onclick="switchTab('aggregations')"
                    >
                        📊 {{ __('invoices.tabs.aggregations') }}
                    </button>
                    
                    <button
                        data-tab="settings"
                        class="tab-button {{ $activeTab === 'settings' ? 'active border-purple-500 text-purple-400' : 'border-transparent text-gray-400' }} whitespace-nowrap border-b-2 px-1 py-4 text-sm font-medium hover:border-purple-400 hover:text-purple-300 transition-colors"
                        onclick="switchTab('settings')"
                    >
                        ⚙️ {{ __('invoices.tabs.settings') }}
                    </button>
                </nav>
            </div>

            {{-- Tab Content --}}
            <div id="tab-content">
                
                {{-- Sales Tab --}}
                <div id="tab-sales" class="tab-pane {{ $activeTab === 'sales' ? 'active' : 'hidden' }}">
                    @include('account.invoices.partials.sales-tab', [
                        'invoices' => $salesInvoices,
                        'filters' => $filters,
                    ])
                </div>

                {{-- Purchases Tab --}}
                <div id="tab-purchases" class="tab-pane {{ $activeTab === 'purchases' ? 'active' : 'hidden' }}">
                    @include('account.invoices.partials.purchases-tab', [
                        'invoices' => $purchaseInvoices,
                        'filters' => $filters,
                    ])
                </div>

                {{-- Aggregations Tab --}}
                <div id="tab-aggregations" class="tab-pane {{ $activeTab === 'aggregations' ? 'active' : 'hidden' }}" data-loaded="false">
                    {{-- Loading spinner - shown by default --}}
                    <div id="aggregations-loading" class="flex items-center justify-center py-20">
                        <div class="text-center">
                            <div class="mb-4 inline-block h-12 w-12 animate-spin rounded-full border-4 border-purple-200 border-t-purple-600"></div>
                            <p class="text-gray-400">{{ __('invoices.loading') }}</p>
                        </div>
                    </div>
                    
                    {{-- Content container - will be populated via AJAX --}}
                    <div id="aggregations-content" class="hidden"></div>
                </div>

                {{-- Settings Tab --}}
                <div id="tab-settings" class="tab-pane {{ $activeTab === 'settings' ? 'active' : 'hidden' }}">
                    @include('account.invoices.partials.settings-tab', [
                        'preferences' => $preferences,
                    ])
                </div>

            </div>

        </div>
    </div>

    @push('scripts')
    <script>
        function switchTab(tabName) {
            // Update URL
            const url = new URL(window.location);
            url.searchParams.set('tab', tabName);
            window.history.pushState({}, '', url);
            
            // Remove active class from all tabs
            document.querySelectorAll('.tab-button').forEach(btn => {
                btn.classList.remove('active', 'border-purple-500', 'text-purple-400');
                btn.classList.add('border-transparent', 'text-gray-400');
            });
            
            // Hide all tab panes
            document.querySelectorAll('.tab-pane').forEach(pane => {
                pane.classList.add('hidden');
                pane.classList.remove('active');
            });
            
            // Activate selected tab
            const activeButton = document.querySelector(`[data-tab="${tabName}"]`);
            if (activeButton) {
                activeButton.classList.add('active', 'border-purple-500', 'text-purple-400');
                activeButton.classList.remove('border-transparent', 'text-gray-400');
                
                // Show selected pane
                const activePane = document.getElementById(`tab-${tabName}`);
                if (activePane) {
                    activePane.classList.remove('hidden');
                    activePane.classList.add('active');
                    
                    // Load aggregations if not loaded yet
                    if (tabName === 'aggregations' && activePane.dataset.loaded === 'false') {
                        loadAggregations();
                    }
                }
            }
        }
        
        async function loadAggregations() {
            const contentDiv = document.getElementById('aggregations-content');
            const loadingDiv = document.getElementById('aggregations-loading');
            const tabPane = document.getElementById('tab-aggregations');
            
            try {
                const response = await fetch('{{ route("account.invoices") }}?tab=aggregations&partial=1', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html'
                    }
                });
                
                if (!response.ok) {
                    throw new Error('Failed to load aggregations');
                }
                
                const html = await response.text();
                
                // Hide loading, show content
                loadingDiv.classList.add('hidden');
                contentDiv.classList.remove('hidden');
                contentDiv.innerHTML = html;
                
                // Mark as loaded
                tabPane.dataset.loaded = 'true';
                
            } catch (error) {
                console.error('Error loading aggregations:', error);
                loadingDiv.innerHTML = `
                    <div class="text-center py-20">
                        <div class="rounded-lg bg-red-50 p-4 text-red-600 dark:bg-red-900/20 dark:text-red-400 inline-block">
                            Errore nel caricamento delle aggregazioni
                        </div>
                    </div>
                `;
            }
        }
        
        // Load aggregations on page load if tab is active
        document.addEventListener('DOMContentLoaded', function() {
            const aggregationsTab = document.getElementById('tab-aggregations');
            if (aggregationsTab && !aggregationsTab.classList.contains('hidden')) {
                loadAggregations();
            }
        });
        
        // =====================================================
        // AGGREGATION DETAILS LOADING (Items & Buyers)
        // =====================================================
        
        // Cache for loaded details
        const loadedDetails = {};

        async function loadAndToggleDetails(elementId, aggregationId, type) {
            const element = document.getElementById(elementId);
            const icon = document.getElementById('icon-' + elementId);
            const cacheKey = `${aggregationId}-${type}`;
            
            // If already open, just close it
            if (!element.classList.contains('hidden')) {
                element.classList.add('hidden');
                icon.textContent = '▼';
                return;
            }
            
            // Open the element
            element.classList.remove('hidden');
            icon.textContent = '▲';
            
            // If already loaded, skip loading
            if (loadedDetails[cacheKey]) {
                return;
            }
            
            // Load data via AJAX
            try {
                const response = await fetch(`/account/invoices/aggregation/${aggregationId}/details/${type}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });
                
                if (!response.ok) {
                    throw new Error('Failed to load details');
                }
                
                const data = await response.json();
                
                // Render the data
                if (type === 'items') {
                    element.innerHTML = renderItems(data.items);
                } else if (type === 'buyers') {
                    element.innerHTML = renderBuyers(data.buyers);
                }
                
                // Mark as loaded
                loadedDetails[cacheKey] = true;
                
            } catch (error) {
                console.error('Error loading details:', error);
                element.innerHTML = `
                    <div class="rounded-lg bg-red-50 p-3 text-center text-sm text-red-600 dark:bg-red-900/20 dark:text-red-400">
                        ${error.message || 'Errore nel caricamento dei dati'}
                    </div>
                `;
            }
        }

        function renderItems(items) {
            if (!items || items.length === 0) {
                return '<div class="rounded-lg bg-white p-3 text-center text-sm text-gray-500 dark:bg-gray-800 dark:text-gray-400">Nessun articolo trovato</div>';
            }
            
            return items.map(item => `
                <a href="/mint/${item.egi_id}" 
                   class="flex items-center justify-between rounded-lg bg-white p-2 hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-700">
                    <div class="flex items-center space-x-3">
                        ${item.thumbnail_url ? 
                            `<img src="${item.thumbnail_url}" alt="${item.title}" class="h-10 w-10 rounded object-cover">` :
                            `<div class="flex h-10 w-10 items-center justify-center rounded bg-purple-100 text-purple-600 dark:bg-purple-900 dark:text-purple-300">🎨</div>`
                        }
                        <div>
                            <div class="text-sm font-medium text-gray-900 dark:text-white">${item.title}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">#${item.egi_id_padded}</div>
                        </div>
                    </div>
                    <div class="text-sm font-semibold text-purple-600 dark:text-purple-400">€ ${item.amount_formatted}</div>
                </a>
            `).join('');
        }

        function renderBuyers(buyers) {
            if (!buyers || buyers.length === 0) {
                return '<div class="rounded-lg bg-white p-3 text-center text-sm text-gray-500 dark:bg-gray-800 dark:text-gray-400">{{ __("invoices.aggregations.no_buyers_data") }}</div>';
            }
            
            return buyers.map(buyer => `
                <a href="${buyer.profile_url}" 
                   class="group flex items-center justify-between rounded-lg bg-white p-2 hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-700 transition-colors">
                    <div class="flex items-center space-x-3">
                        <div class="flex h-10 w-10 items-center justify-center flex-shrink-0 rounded-full bg-gradient-to-r from-blue-500 to-cyan-500">
                            <img src="${buyer.avatar_url}" 
                                 alt="${buyer.name}"
                                 class="h-full w-full object-cover rounded-full transition-transform duration-300 group-hover:scale-105"
                                 loading="lazy" 
                                 decoding="async">
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors">
                                ${buyer.name}
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                ${buyer.count} ${buyer.count === 1 ? 'acquisto' : 'acquisti'}
                            </div>
                        </div>
                    </div>
                    <div class="text-sm font-semibold text-purple-600 dark:text-purple-400">€ ${buyer.total_formatted}</div>
                </a>
            `).join('');
        }
    </script>
    @endpush

</x-app-layout>

