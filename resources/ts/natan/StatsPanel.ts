/**
 * N.A.T.A.N. Stats Panel
 * 
 * @package resources/ts/natan
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - N.A.T.A.N.)
 * @date 2025-10-09
 * @purpose Statistics panel with charts and KPI cards
 */

import type { Stats } from './types';
import { NatanApiClient } from './ApiClient';

// Declare Chart.js as global (loaded via CDN)
declare const Chart: any;

/**
 * Stats Panel Manager
 * 
 * Manages statistics display with Chart.js visualizations
 */
export class StatsPanel {
    private api: NatanApiClient;
    private charts: Map<string, any> = new Map();

    /**
     * Constructor
     * 
     * @param api NatanApiClient instance
     */
    constructor(api: NatanApiClient) {
        this.api = api;
    }

    /**
     * Load and display statistics
     */
    async load(): Promise<void> {
        try {
            const response = await this.api.getStats();
            const stats = response.data || response as any;

            this.renderKPIs(stats);
            this.renderCharts(stats);

        } catch (error) {
            console.error('Failed to load stats:', error);
            this.showError('Impossibile caricare le statistiche');
        }
    }

    /**
     * Render KPI cards
     * 
     * @param stats Statistics data
     */
    private renderKPIs(stats: Stats): void {
        // Update KPI values if elements exist
        this.updateElement('totalActs', stats.total_acts);
        this.updateElement('actsThisMonth', stats.acts_this_month);
        this.updateElement('totalAiCost', `€ ${stats.total_ai_cost.toFixed(2)}`);
        this.updateElement('avgProcessingTime', `${stats.avg_processing_time || 0}s`);
    }

    /**
     * Render charts
     * 
     * @param stats Statistics data
     */
    private renderCharts(stats: Stats): void {
        // Render tipo atto distribution chart
        if (stats.by_tipo && Object.keys(stats.by_tipo).length > 0) {
            this.renderActTypeChart(stats.by_tipo);
        }

        // Render monthly trend chart
        if (stats.by_month && stats.by_month.length > 0) {
            this.renderMonthlyTrendChart(stats.by_month);
        }
    }

    /**
     * Render act type distribution chart
     * 
     * @param byTipo Acts by tipo data
     */
    private renderActTypeChart(byTipo: Record<string, number>): void {
        const canvas = document.getElementById('actTypeChart') as HTMLCanvasElement;
        if (!canvas) return;

        // Destroy existing chart if any
        const existingChart = this.charts.get('actType');
        if (existingChart) {
            existingChart.destroy();
        }

        const ctx = canvas.getContext('2d');
        if (!ctx) return;

        const chart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(byTipo),
                datasets: [{
                    data: Object.values(byTipo),
                    backgroundColor: [
                        '#8E44AD', // Viola Innovazione
                        '#1B365D', // Blu Algoritmo
                        '#2D5016', // Verde Rinascita
                        '#D4A574', // Oro Fiorentino
                        '#6B6B6B', // Grigio Pietra
                        '#E67E22', // Arancio Energia
                    ],
                    borderWidth: 0,
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            padding: 16,
                            font: {
                                size: 13,
                                family: "'Inter', sans-serif"
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: (context: any) => {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const total = context.dataset.data.reduce((a: number, b: number) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });

        this.charts.set('actType', chart);
    }

    /**
     * Render monthly trend chart
     * 
     * @param byMonth Monthly data
     */
    private renderMonthlyTrendChart(byMonth: Array<{ month: string; count: number }>): void {
        const canvas = document.getElementById('monthlyTrendChart') as HTMLCanvasElement;
        if (!canvas) return;

        // Destroy existing chart if any
        const existingChart = this.charts.get('monthlyTrend');
        if (existingChart) {
            existingChart.destroy();
        }

        const ctx = canvas.getContext('2d');
        if (!ctx) return;

        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: byMonth.map(item => {
                    const [year, month] = item.month.split('-');
                    return new Date(parseInt(year), parseInt(month) - 1).toLocaleDateString('it-IT', {
                        month: 'short',
                        year: 'numeric'
                    });
                }),
                datasets: [{
                    label: 'Atti Processati',
                    data: byMonth.map(item => item.count),
                    borderColor: '#1B365D',
                    backgroundColor: 'rgba(27, 54, 93, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#1B365D',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });

        this.charts.set('monthlyTrend', chart);
    }

    /**
     * Update element text content
     * 
     * @param id Element ID
     * @param value New value
     */
    private updateElement(id: string, value: string | number): void {
        const element = document.getElementById(id);
        if (element) {
            element.textContent = String(value);
        }
    }

    /**
     * Show error message
     * 
     * @param message Error message
     */
    private showError(message: string): void {
        console.error('StatsPanel error:', message);
        // You can add UI error display here if needed
    }

    /**
     * Destroy all charts (cleanup)
     */
    destroy(): void {
        this.charts.forEach(chart => chart.destroy());
        this.charts.clear();
    }
}

/**
 * Initialize stats panel on page load
 * 
 * @param api NatanApiClient instance
 * @returns StatsPanel instance
 */
export function initStatsPanel(api: NatanApiClient): StatsPanel {
    const panel = new StatsPanel(api);
    panel.load();
    return panel;
}

