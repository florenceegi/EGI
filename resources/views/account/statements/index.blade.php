<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            {{ __('statements.page_title') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            
            {{-- Page Header --}}
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                    {{ __('statements.page_title') }}
                </h1>
                <p class="mt-2 text-gray-600 dark:text-gray-400">
                    {{ __('statements.page_subtitle') }}
                </p>
            </div>

            {{-- Tabs Navigation --}}
            <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <button
                        data-tab="egili"
                        class="tab-button active whitespace-nowrap border-b-2 border-purple-500 px-1 py-4 text-sm font-medium text-purple-600 dark:text-purple-400"
                        onclick="switchTab('egili')"
                    >
                        💎 {{ __('statements.tabs.egili') }}
                    </button>
                    
                    {{-- Future tabs --}}
                    <button
                        data-tab="invoices"
                        class="tab-button whitespace-nowrap border-b-2 border-transparent px-1 py-4 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 opacity-50 cursor-not-allowed"
                        disabled
                        title="{{ __('statements.tabs.invoices') }} - Coming soon"
                    >
                        📄 {{ __('statements.tabs.invoices') }} <span class="text-xs">(Coming soon)</span>
                    </button>
                    
                    <button
                        data-tab="receipts"
                        class="tab-button whitespace-nowrap border-b-2 border-transparent px-1 py-4 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 opacity-50 cursor-not-allowed"
                        disabled
                        title="{{ __('statements.tabs.receipts') }} - Coming soon"
                    >
                        🧾 {{ __('statements.tabs.receipts') }} <span class="text-xs">(Coming soon)</span>
                    </button>
                </nav>
            </div>

            {{-- Tab Content --}}
            <div id="tab-content">
                
                {{-- EGILI Tab (Active by default) --}}
                <div id="tab-egili" class="tab-pane active">
                    @include('account.statements.partials.egili-statement', [
                        'user' => $user,
                        'filter' => $filter,
                        'dateFrom' => $dateFrom,
                        'dateTo' => $dateTo,
                        'startDate' => $startDate,
                        'endDate' => $endDate,
                        'egiliTransactions' => $egiliTransactions,
                        'egiliSummary' => $egiliSummary,
                    ])
                </div>

                {{-- Future tabs content --}}
                <div id="tab-invoices" class="tab-pane hidden">
                    <div class="rounded-lg bg-gray-50 p-8 text-center dark:bg-gray-800">
                        <p class="text-gray-600 dark:text-gray-400">
                            📄 {{ __('statements.tabs.invoices') }} - Coming soon
                        </p>
                    </div>
                </div>

                <div id="tab-receipts" class="tab-pane hidden">
                    <div class="rounded-lg bg-gray-50 p-8 text-center dark:bg-gray-800">
                        <p class="text-gray-600 dark:text-gray-400">
                            🧾 {{ __('statements.tabs.receipts') }} - Coming soon
                        </p>
                    </div>
                </div>

            </div>

        </div>
    </div>

    @push('scripts')
    <script>
        function switchTab(tabName) {
            // Remove active class from all tabs
            document.querySelectorAll('.tab-button').forEach(btn => {
                btn.classList.remove('active', 'border-purple-500', 'text-purple-600', 'dark:text-purple-400');
                btn.classList.add('border-transparent', 'text-gray-500', 'dark:text-gray-400');
            });
            
            // Hide all tab panes
            document.querySelectorAll('.tab-pane').forEach(pane => {
                pane.classList.add('hidden');
                pane.classList.remove('active');
            });
            
            // Activate selected tab
            const activeButton = document.querySelector(`[data-tab="${tabName}"]`);
            if (activeButton && !activeButton.disabled) {
                activeButton.classList.add('active', 'border-purple-500', 'text-purple-600', 'dark:text-purple-400');
                activeButton.classList.remove('border-transparent', 'text-gray-500', 'dark:text-gray-400');
                
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

