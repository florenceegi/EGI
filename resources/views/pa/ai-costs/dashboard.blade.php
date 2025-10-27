<x-pa-layout pageTitle="Monitor Costi AI">
    <div class="space-y-6">
        {{-- Header con periodo selezione --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-[#1B365D]">Monitor Costi AI</h1>
                <p class="mt-1 text-sm text-gray-600">Monitora spesa per Anthropic Claude, OpenAI e Perplexity</p>
            </div>

            <select id="periodSelector"
                class="rounded-lg border-gray-300 px-4 py-2 text-sm shadow-sm focus:border-[#1B365D] focus:ring-[#1B365D]">
                <option value="current_month">Mese Corrente</option>
                <option value="last_30_days">Ultimi 30 Giorni</option>
            </select>
        </div>

        {{-- Alert Banner (se budget superato) --}}
        <div id="alertBanner" class="hidden rounded-lg border-l-4 p-4 shadow-sm">
            <div class="flex items-start gap-3">
                <span class="material-icons text-2xl" id="alertIcon"></span>
                <div class="flex-1">
                    <h3 class="font-semibold" id="alertTitle"></h3>
                    <p class="mt-1 text-sm" id="alertMessage"></p>
                </div>
                <button onclick="document.getElementById('alertBanner').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600">
                    <span class="material-icons">close</span>
                </button>
            </div>
        </div>

        {{-- Stats Cards Row --}}
        <div class="grid gap-6 md:grid-cols-3">
            {{-- Total Spending Card --}}
            <div class="rounded-xl bg-gradient-to-br from-[#1B365D] to-[#2D5016] p-6 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium opacity-90">Spesa Totale</p>
                        <p class="mt-2 text-3xl font-bold">$<span id="totalCost">0.00</span></p>
                        <p class="mt-1 text-xs opacity-75"><span id="totalMessages">0</span> richieste AI</p>
                    </div>
                    <div class="rounded-full bg-white/20 p-3">
                        <span class="material-icons text-3xl">payments</span>
                    </div>
                </div>
            </div>

            {{-- Budget Progress Card --}}
            <div class="rounded-xl bg-white p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-600">Budget Mensile</p>
                        <p class="mt-2 text-3xl font-bold text-[#1B365D]">$<span id="budgetTotal">0</span></p>
                        <div class="mt-3">
                            <div class="h-2 w-full overflow-hidden rounded-full bg-gray-200">
                                <div id="budgetProgress"
                                    class="h-full rounded-full bg-[#2D5016] transition-all duration-500"
                                    style="width: 0%"></div>
                            </div>
                            <p class="mt-1 text-xs text-gray-500"><span id="budgetPercentage">0</span>% utilizzato</p>
                        </div>
                    </div>
                    <div class="rounded-full bg-[#D4A574]/20 p-3">
                        <span class="material-icons text-3xl text-[#D4A574]">account_balance_wallet</span>
                    </div>
                </div>
            </div>

            {{-- Avg Cost per Request --}}
            <div class="rounded-xl bg-white p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Costo Medio/Richiesta</p>
                        <p class="mt-2 text-3xl font-bold text-[#1B365D]">$<span id="avgCost">0.00</span></p>
                        <p class="mt-1 text-xs text-gray-500"><span id="totalTokens">0</span> token totali</p>
                    </div>
                    <div class="rounded-full bg-[#8E44AD]/20 p-3">
                        <span class="material-icons text-3xl text-[#8E44AD]">analytics</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Charts Row --}}
        <div class="grid gap-6 lg:grid-cols-2">
            {{-- Daily Spending Trend Chart --}}
            <div class="rounded-xl bg-white p-6 shadow-lg">
                <h3 class="mb-4 flex items-center gap-2 text-lg font-semibold text-[#1B365D]">
                    <span class="material-icons">trending_up</span>
                    Trend Spesa Giornaliera
                </h3>
                <canvas id="trendChart" height="250"></canvas>
            </div>

            {{-- Provider Breakdown Chart --}}
            <div class="rounded-xl bg-white p-6 shadow-lg">
                <h3 class="mb-4 flex items-center gap-2 text-lg font-semibold text-[#1B365D]">
                    <span class="material-icons">pie_chart</span>
                    Spesa per Provider
                </h3>
                <canvas id="providerChart" height="250"></canvas>
            </div>
        </div>

        {{-- Provider Details Table --}}
        <div class="rounded-xl bg-white p-6 shadow-lg">
            <h3 class="mb-4 flex items-center gap-2 text-lg font-semibold text-[#1B365D]">
                <span class="material-icons">list_alt</span>
                Dettaglio per Provider
            </h3>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="border-b-2 border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Provider</th>
                            <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Spesa</th>
                            <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Budget</th>
                            <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Richieste</th>
                            <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Token</th>
                            <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">Status</th>
                            <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">Azioni</th>
                        </tr>
                    </thead>
                    <tbody id="providerTableBody">
                        {{-- Populated by JavaScript --}}
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Model Breakdown Table --}}
        <div class="rounded-xl bg-white p-6 shadow-lg">
            <h3 class="mb-4 flex items-center gap-2 text-lg font-semibold text-[#1B365D]">
                <span class="material-icons">memory</span>
                Dettaglio per Modello AI
            </h3>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="border-b-2 border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Modello</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Provider</th>
                            <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Spesa</th>
                            <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Richieste</th>
                            <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Token</th>
                            <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Costo/Richiesta</th>
                        </tr>
                    </thead>
                    <tbody id="modelTableBody">
                        {{-- Populated by JavaScript --}}
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Budget Edit Modal --}}
    <dialog id="budgetModal" class="rounded-xl p-0 shadow-2xl backdrop:bg-black/50">
        <div class="w-full max-w-md">
            <div class="bg-gradient-to-r from-[#1B365D] to-[#2D5016] px-6 py-4">
                <h3 class="text-lg font-bold text-white">Configura Budget</h3>
            </div>

            <form id="budgetForm" class="space-y-4 p-6">
                <input type="hidden" id="budgetProvider" name="provider">

                <div>
                    <label class="block text-sm font-medium text-gray-700">Provider</label>
                    <input type="text" id="budgetProviderName" readonly
                        class="mt-1 w-full rounded-lg border-gray-300 bg-gray-100 px-4 py-2">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Budget Mensile (USD)</label>
                    <input type="number" id="budgetAmount" name="monthly_budget" step="0.01" min="0"
                        required
                        class="mt-1 w-full rounded-lg border-gray-300 px-4 py-2 focus:border-[#1B365D] focus:ring-[#1B365D]">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Soglia Alert (%)</label>
                    <input type="number" id="budgetThreshold" name="alert_threshold" step="1" min="0"
                        max="100" value="75" required
                        class="mt-1 w-full rounded-lg border-gray-300 px-4 py-2 focus:border-[#1B365D] focus:ring-[#1B365D]">
                </div>

                <div>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" id="budgetAlertsEnabled" name="alerts_enabled" checked
                            class="rounded border-gray-300 text-[#1B365D] focus:ring-[#1B365D]">
                        <span class="text-sm font-medium text-gray-700">Abilita notifiche alert</span>
                    </label>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Email Alert (opzionale)</label>
                    <input type="email" id="budgetEmail" name="alert_email"
                        class="mt-1 w-full rounded-lg border-gray-300 px-4 py-2 focus:border-[#1B365D] focus:ring-[#1B365D]">
                </div>

                <div class="flex gap-3 pt-4">
                    <button type="submit"
                        class="flex-1 rounded-lg bg-[#2D5016] px-4 py-2 font-medium text-white hover:bg-[#1F3810]">
                        Salva Budget
                    </button>
                    <button type="button" onclick="document.getElementById('budgetModal').close()"
                        class="rounded-lg border border-gray-300 px-4 py-2 font-medium text-gray-700 hover:bg-gray-50">
                        Annulla
                    </button>
                </div>
            </form>
        </div>
    </dialog>

    {{-- Chart.js CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <script>
        // AI Costs Dashboard JavaScript
        (function() {
            'use strict';

            const Dashboard = {
                charts: {
                    trend: null,
                    provider: null
                },
                stats: null,

                async init() {
                    console.log('[AiCostsDashboard] Initializing...');

                    // Bind events
                    document.getElementById('periodSelector').addEventListener('change', (e) => {
                        this.loadStats(e.target.value);
                    });

                    document.getElementById('budgetForm').addEventListener('submit', (e) => {
                        e.preventDefault();
                        this.saveBudget();
                    });

                    // Initial load
                    await this.loadStats('current_month');
                    await this.loadTrend();
                },

                async loadStats(period = 'current_month') {
                    try {
                        const response = await fetch(`/pa/ai-costs/api/stats?period=${period}`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        const data = await response.json();

                        if (data.success) {
                            this.stats = data.stats;
                            this.renderStats();
                            this.renderAlerts(data.alerts);
                        }
                    } catch (error) {
                        console.error('[Dashboard] Load stats failed:', error);
                    }
                },

                async loadTrend() {
                    try {
                        const response = await fetch('/pa/ai-costs/api/trend', {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        const data = await response.json();

                        if (data.success) {
                            this.renderTrendChart(data.trend);
                        }
                    } catch (error) {
                        console.error('[Dashboard] Load trend failed:', error);
                    }
                },

                renderStats() {
                    const {
                        totals,
                        by_provider,
                        by_model
                    } = this.stats;

                    // Update summary cards
                    document.getElementById('totalCost').textContent = totals.cost.toFixed(2);
                    document.getElementById('totalMessages').textContent = totals.messages.toLocaleString();
                    document.getElementById('avgCost').textContent = totals.avg_cost_per_message.toFixed(4);
                    document.getElementById('totalTokens').textContent = totals.tokens.toLocaleString();

                    // Calculate total budget
                    const totalBudget = by_provider.reduce((sum, p) => sum + (p.budget?.budget || 0), 0);
                    const budgetPercentage = totalBudget > 0 ? (totals.cost / totalBudget * 100) : 0;

                    document.getElementById('budgetTotal').textContent = totalBudget.toFixed(0);
                    document.getElementById('budgetPercentage').textContent = budgetPercentage.toFixed(1);
                    document.getElementById('budgetProgress').style.width = `${Math.min(budgetPercentage, 100)}%`;

                    // Color based on percentage
                    const progressBar = document.getElementById('budgetProgress');
                    if (budgetPercentage >= 100) {
                        progressBar.className = 'h-full rounded-full bg-red-600 transition-all duration-500';
                    } else if (budgetPercentage >= 75) {
                        progressBar.className = 'h-full rounded-full bg-yellow-500 transition-all duration-500';
                    } else {
                        progressBar.className = 'h-full rounded-full bg-[#2D5016] transition-all duration-500';
                    }

                    // Render provider chart
                    this.renderProviderChart(by_provider);

                    // Render tables
                    this.renderProviderTable(by_provider);
                    this.renderModelTable(by_model);
                },

                renderAlerts(alerts) {
                    if (!alerts || alerts.length === 0) {
                        document.getElementById('alertBanner').classList.add('hidden');
                        return;
                    }

                    const banner = document.getElementById('alertBanner');
                    const icon = document.getElementById('alertIcon');
                    const title = document.getElementById('alertTitle');
                    const message = document.getElementById('alertMessage');

                    const highestAlert = alerts[0];

                    // Set alert styling
                    if (highestAlert.level === 'critical') {
                        banner.className = 'rounded-lg border-l-4 border-red-600 bg-red-50 p-4 shadow-sm';
                        icon.className = 'material-icons text-2xl text-red-600';
                        icon.textContent = 'error';
                        title.className = 'font-semibold text-red-900';
                        message.className = 'mt-1 text-sm text-red-700';
                    } else if (highestAlert.level === 'danger') {
                        banner.className = 'rounded-lg border-l-4 border-orange-500 bg-orange-50 p-4 shadow-sm';
                        icon.className = 'material-icons text-2xl text-orange-500';
                        icon.textContent = 'warning';
                        title.className = 'font-semibold text-orange-900';
                        message.className = 'mt-1 text-sm text-orange-700';
                    } else {
                        banner.className = 'rounded-lg border-l-4 border-yellow-500 bg-yellow-50 p-4 shadow-sm';
                        icon.className = 'material-icons text-2xl text-yellow-600';
                        icon.textContent = 'warning_amber';
                        title.className = 'font-semibold text-yellow-900';
                        message.className = 'mt-1 text-sm text-yellow-700';
                    }

                    title.textContent = 'Alert Budget';
                    message.textContent = alerts.map(a => a.message).join(' • ');

                    banner.classList.remove('hidden');
                },

                renderProviderChart(providers) {
                    const ctx = document.getElementById('providerChart');

                    if (this.charts.provider) {
                        this.charts.provider.destroy();
                    }

                    this.charts.provider = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: providers.map(p => p.provider),
                            datasets: [{
                                data: providers.map(p => p.cost),
                                backgroundColor: ['#1B365D', '#2D5016', '#D4A574', '#8E44AD'],
                                borderWidth: 2,
                                borderColor: '#fff'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom'
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return `${context.label}: $${context.parsed.toFixed(2)}`;
                                        }
                                    }
                                }
                            }
                        }
                    });
                },

                renderTrendChart(trend) {
                    const ctx = document.getElementById('trendChart');

                    if (this.charts.trend) {
                        this.charts.trend.destroy();
                    }

                    this.charts.trend = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: trend.map(t => new Date(t.date).toLocaleDateString('it-IT', {
                                month: 'short',
                                day: 'numeric'
                            })),
                            datasets: [{
                                label: 'Spesa Giornaliera ($)',
                                data: trend.map(t => t.cost),
                                borderColor: '#1B365D',
                                backgroundColor: 'rgba(27, 54, 93, 0.1)',
                                fill: true,
                                tension: 0.4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function(value) {
                                            return '$' + value.toFixed(2);
                                        }
                                    }
                                }
                            }
                        }
                    });
                },

                renderProviderTable(providers) {
                    const tbody = document.getElementById('providerTableBody');
                    tbody.innerHTML = '';

                    providers.forEach(provider => {
                        const budget = provider.budget;
                        const percentage = budget ? budget.percentage : 0;

                        let statusClass, statusText;
                        if (!budget) {
                            statusClass = 'bg-gray-100 text-gray-600';
                            statusText = 'No Budget';
                        } else if (budget.alert_level === 'critical') {
                            statusClass = 'bg-red-100 text-red-700';
                            statusText = 'Superato';
                        } else if (budget.alert_level === 'danger') {
                            statusClass = 'bg-orange-100 text-orange-700';
                            statusText = 'Critico';
                        } else if (budget.alert_level === 'warning') {
                            statusClass = 'bg-yellow-100 text-yellow-700';
                            statusText = 'Alert';
                        } else {
                            statusClass = 'bg-green-100 text-green-700';
                            statusText = 'OK';
                        }

                        const row = document.createElement('tr');
                        row.className = 'border-b border-gray-100 hover:bg-gray-50';
                        row.innerHTML = `
                            <td class="px-4 py-3 font-medium text-gray-900">${provider.provider}</td>
                            <td class="px-4 py-3 text-right font-semibold text-[#1B365D]">$${provider.cost.toFixed(2)}</td>
                            <td class="px-4 py-3 text-right text-gray-600">${budget ? '$' + budget.budget.toFixed(0) : '-'}</td>
                            <td class="px-4 py-3 text-right text-gray-600">${provider.messages.toLocaleString()}</td>
                            <td class="px-4 py-3 text-right text-gray-600">${provider.tokens.toLocaleString()}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold ${statusClass}">
                                    ${statusText}${budget ? ` (${percentage.toFixed(0)}%)` : ''}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button onclick="Dashboard.editBudget('${provider.provider.toLowerCase()}', '${provider.provider}')" 
                                    class="rounded-lg bg-[#D4A574] px-3 py-1 text-xs font-medium text-white hover:bg-[#c19563]">
                                    Configura
                                </button>
                            </td>
                        `;
                        tbody.appendChild(row);
                    });
                },

                renderModelTable(models) {
                    const tbody = document.getElementById('modelTableBody');
                    tbody.innerHTML = '';

                    models.forEach(model => {
                        const avgCost = model.messages > 0 ? model.cost / model.messages : 0;

                        const row = document.createElement('tr');
                        row.className = 'border-b border-gray-100 hover:bg-gray-50';
                        row.innerHTML = `
                            <td class="px-4 py-3 font-medium text-gray-900">${model.model_name || model.model}</td>
                            <td class="px-4 py-3 text-gray-600">${model.provider}</td>
                            <td class="px-4 py-3 text-right font-semibold text-[#1B365D]">$${model.cost.toFixed(2)}</td>
                            <td class="px-4 py-3 text-right text-gray-600">${model.messages.toLocaleString()}</td>
                            <td class="px-4 py-3 text-right text-gray-600">${model.tokens.toLocaleString()}</td>
                            <td class="px-4 py-3 text-right text-gray-600">$${avgCost.toFixed(4)}</td>
                        `;
                        tbody.appendChild(row);
                    });
                },

                editBudget(provider, providerName) {
                    document.getElementById('budgetProvider').value = provider;
                    document.getElementById('budgetProviderName').value = providerName;

                    // Load existing budget if any
                    const providerData = this.stats.by_provider.find(p => p.provider.toLowerCase() === provider);
                    if (providerData && providerData.budget) {
                        document.getElementById('budgetAmount').value = providerData.budget.budget;
                        // Threshold and alerts would come from DB, for now use defaults
                    } else {
                        document.getElementById('budgetAmount').value = '';
                    }

                    document.getElementById('budgetModal').showModal();
                },

                async saveBudget() {
                    const formData = new FormData(document.getElementById('budgetForm'));
                    const data = Object.fromEntries(formData);
                    data.alerts_enabled = document.getElementById('budgetAlertsEnabled').checked;

                    try {
                        const response = await fetch('/pa/ai-costs/api/budget', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    ?.content || ''
                            },
                            body: JSON.stringify(data)
                        });

                        const result = await response.json();

                        if (result.success) {
                            document.getElementById('budgetModal').close();
                            // Reload stats
                            await this.loadStats(document.getElementById('periodSelector').value);
                            alert('Budget aggiornato con successo!');
                        } else {
                            alert('Errore durante l\'aggiornamento del budget');
                        }
                    } catch (error) {
                        console.error('[Dashboard] Save budget failed:', error);
                        alert('Errore durante l\'aggiornamento del budget');
                    }
                }
            };

            // Expose globally
            window.Dashboard = Dashboard;

            // Initialize on DOM ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', () => Dashboard.init());
            } else {
                Dashboard.init();
            }
        })();
    </script>
</x-pa-layout>
