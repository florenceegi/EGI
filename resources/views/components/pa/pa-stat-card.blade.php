{{--
/**
 * PA Stat Card Component
 *
 * @package App\View\Components\PA
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - PA Enterprise MVP)
 * @date 2025-10-02
 * @purpose KPI card component for PA dashboard statistics with brand-compliant design
 *
 * @props
 * - title: string (required) - Card title (e.g., "Patrimonio Totale")
 * - value: string|int (required) - Main stat value (e.g., "127" or "89%")
 * - icon: string (optional) - Material Symbols icon name (default: 'analytics')
 * - trend: string (optional) - Trend indicator: 'up'|'down'|'neutral' (default: null)
 * - trendValue: string (optional) - Trend percentage (e.g., "+12%")
 * - variant: string (optional) - Color variant: 'default'|'success'|'warning'|'danger' (default: 'default')
 * - subtitle: string (optional) - Additional context text
 *
 * @example
 * <x-pa.pa-stat-card
 *     title="Patrimonio Totale"
 *     value="127"
 *     icon="museum"
 *     trend="up"
 *     trendValue="+8%"
 *     subtitle="Beni culturali certificati"
 * />
 */
--}}

@props([
    'title' => 'Statistica',
    'value' => '0',
    'icon' => 'analytics',
    'trend' => null,
    'trendValue' => null,
    'variant' => 'default',
    'subtitle' => null,
])

@php
    // Variant color mapping (PA Brand Guidelines)
    $variantClasses = [
        'default' => 'bg-gradient-to-br from-[#1B365D] to-[#0F2342] border-[#D4A574]',
        'success' => 'bg-gradient-to-br from-[#2D5016] to-[#1F3810] border-[#2D5016]',
        'warning' => 'bg-gradient-to-br from-[#E67E22] to-[#D35400] border-[#E67E22]',
        'danger' => 'bg-gradient-to-br from-[#C13120] to-[#A02718] border-[#C13120]',
    ];

    $bgClass = $variantClasses[$variant] ?? $variantClasses['default'];

    // Trend icon mapping
    $trendIcons = [
        'up' => 'trending_up',
        'down' => 'trending_down',
        'neutral' => 'trending_flat',
    ];

    $trendColors = [
        'up' => 'text-[#2D5016]',
        'down' => 'text-[#C13120]',
        'neutral' => 'text-[#6B6B6B]',
    ];
@endphp

<div {{ $attributes->merge(['class' => "stat-card rounded-xl p-6 shadow-lg border-2 transition-all duration-300 hover:shadow-2xl hover:scale-[1.02] $bgClass"]) }}
    role="article" aria-label="{{ $title }}: {{ $value }}">
    {{-- Header con icona e trend --}}
    <div class="flex items-start justify-between mb-4">
        {{-- Icon --}}
        <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 rounded-lg stat-icon bg-white/10">
            <span class="material-symbols-outlined text-3xl text-[#D4A574]" aria-hidden="true">
                {{ $icon }}
            </span>
        </div>

        {{-- Trend Indicator --}}
        @if ($trend && $trendValue)
            <div
                class="{{ $trendColors[$trend] ?? $trendColors['neutral'] }} flex items-center gap-1 rounded-full bg-white/20 px-2 py-1">
                <span class="text-sm material-symbols-outlined" aria-hidden="true">
                    {{ $trendIcons[$trend] ?? $trendIcons['neutral'] }}
                </span>
                <span class="text-xs font-semibold">{{ $trendValue }}</span>
            </div>
        @endif
    </div>

    {{-- Value --}}
    <div class="mb-2 stat-value">
        <p class="text-4xl font-bold tracking-tight text-white" aria-live="polite">
            {{ $value }}
        </p>
    </div>

    {{-- Title --}}
    <div class="mb-1 stat-title">
        <h3 class="text-sm font-medium uppercase tracking-wide text-[#D4A574]">
            {{ $title }}
        </h3>
    </div>

    {{-- Subtitle (optional) --}}
    @if ($subtitle)
        <div class="stat-subtitle">
            <p class="mt-2 text-xs text-white/70">
                {{ $subtitle }}
            </p>
        </div>
    @endif

    {{-- Optional slot for custom content --}}
    @if ($slot->isNotEmpty())
        <div class="pt-4 mt-4 border-t stat-footer border-white/10">
            {{ $slot }}
        </div>
    @endif
</div>
