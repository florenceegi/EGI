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
                <div id="tab-aggregations" class="tab-pane {{ $activeTab === 'aggregations' ? 'active' : 'hidden' }}">
                    @include('account.invoices.partials.aggregations-tab', [
                        'aggregations' => $aggregations,
                        'filters' => $filters,
                    ])
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
                }
            }
        }
    </script>
    @endpush

</x-app-layout>

