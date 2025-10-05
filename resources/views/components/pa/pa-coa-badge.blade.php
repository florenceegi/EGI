{{--
/**
 * PA CoA Badge Component
 *
 * @package App\View\Components\PA
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - PA Enterprise MVP)
 * @date 2025-10-02
 * @purpose CoA status badge component for heritage items with accessible design
 *
 * @props
 * - status: string (required) - CoA status: 'valid'|'revoked'|'pending'|'none'|'expired'
 * - size: string (optional) - Badge size: 'sm'|'md'|'lg' (default: 'md')
 * - showIcon: bool (optional) - Show status icon (default: true)
 * - showLabel: bool (optional) - Show status label (default: true)
 *
 * @example
 * <x-pa.pa-coa-badge status="valid" size="md" />
 * <x-pa.pa-coa-badge status="pending" :showIcon="false" />
 */
--}}

@props([
    'status' => 'none',
    'size' => 'md',
    'showIcon' => true,
    'showLabel' => true,
])

@php
    // Status configuration (PA Brand Guidelines compliant)
    $statusConfig = [
        'valid' => [
            'label' => __('pa_heritage.coa_badge_valid'),
            'color' => 'bg-[#2D5016] text-white border-[#2D5016]',
            'icon' => 'verified',
            'ariaLabel' => __('pa_heritage.coa_badge_valid_aria'),
        ],
        'revoked' => [
            'label' => __('pa_heritage.coa_badge_revoked'),
            'color' => 'bg-[#C13120] text-white border-[#C13120]',
            'icon' => 'cancel',
            'ariaLabel' => __('pa_heritage.coa_badge_revoked_aria'),
        ],
        'pending' => [
            'label' => __('pa_heritage.coa_badge_pending'),
            'color' => 'bg-[#E67E22] text-white border-[#E67E22]',
            'icon' => 'pending',
            'ariaLabel' => __('pa_heritage.coa_badge_pending_aria'),
        ],
        'expired' => [
            'label' => __('pa_heritage.coa_badge_expired'),
            'color' => 'bg-[#6B6B6B] text-white border-[#6B6B6B]',
            'icon' => 'event_busy',
            'ariaLabel' => __('pa_heritage.coa_badge_expired_aria'),
        ],
        'none' => [
            'label' => __('pa_heritage.coa_badge_none'),
            'color' => 'bg-gray-200 text-gray-600 border-gray-300',
            'icon' => 'description_off',
            'ariaLabel' => __('pa_heritage.coa_badge_none_aria'),
        ],
    ];

    // Size classes
    $sizeClasses = [
        'sm' => 'text-xs px-2 py-1 gap-1',
        'md' => 'text-sm px-3 py-1.5 gap-1.5',
        'lg' => 'text-base px-4 py-2 gap-2',
    ];

    $iconSizes = [
        'sm' => 'text-sm',
        'md' => 'text-base',
        'lg' => 'text-lg',
    ];

    $config = $statusConfig[$status] ?? $statusConfig['none'];
    $sizeClass = $sizeClasses[$size] ?? $sizeClasses['md'];
    $iconSize = $iconSizes[$size] ?? $iconSizes['md'];
@endphp

<span
    {{ $attributes->merge(['class' => "coa-badge inline-flex items-center font-semibold rounded-full border-2 transition-all duration-200 $config[color] $sizeClass"]) }}
    role="status" aria-label="{{ $config['ariaLabel'] }}">
    {{-- Icon --}}
    @if ($showIcon)
        <span class="material-symbols-outlined {{ $iconSize }}" aria-hidden="true">
            {{ $config['icon'] }}
        </span>
    @endif

    {{-- Label --}}
    @if ($showLabel)
        <span class="coa-label">{{ $config['label'] }}</span>
    @endif

    {{-- Optional slot for custom content --}}
    {{ $slot }}
</span>
