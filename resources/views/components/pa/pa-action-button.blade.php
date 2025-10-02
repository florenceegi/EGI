{{--
/**
 * PA Action Button Component
 *
 * @package App\View\Components\PA
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - PA Enterprise MVP)
 * @date 2025-10-02
 * @purpose Institutional CTA button component with PA brand styling and accessibility
 *
 * @props
 * - label: string (required) - Button text label
 * - route: string (optional) - Laravel route name
 * - href: string (optional) - Direct URL (if no route)
 * - icon: string (optional) - Material Symbols icon name
 * - variant: string (optional) - Style variant: 'primary'|'secondary'|'success'|'danger'|'outline' (default: 'primary')
 * - size: string (optional) - Button size: 'sm'|'md'|'lg' (default: 'md')
 * - type: string (optional) - Button type for forms: 'button'|'submit'|'reset' (default: 'button')
 * - disabled: bool (optional) - Disable button (default: false)
 * - fullWidth: bool (optional) - Full width button (default: false)
 * - target: string (optional) - Link target: '_blank'|'_self' (default: '_self')
 *
 * @example
 * <x-pa.pa-action-button
 *     label="Nuovo Certificato"
 *     route="pa.coa.create"
 *     icon="add_circle"
 *     variant="primary"
 * />
 */
--}}

@props([
    'label' => 'Azione',
    'route' => null,
    'href' => null,
    'icon' => null,
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'disabled' => false,
    'fullWidth' => false,
    'target' => '_self',
])

@php
// Variant classes (PA Brand Guidelines)
$variantClasses = [
    'primary' => 'bg-[#D4A574] hover:bg-[#C39463] text-white border-[#D4A574] hover:border-[#C39463] shadow-md hover:shadow-lg',
    'secondary' => 'bg-[#1B365D] hover:bg-[#0F2342] text-white border-[#1B365D] hover:border-[#0F2342] shadow-md hover:shadow-lg',
    'success' => 'bg-[#2D5016] hover:bg-[#1F3810] text-white border-[#2D5016] hover:border-[#1F3810] shadow-md hover:shadow-lg',
    'danger' => 'bg-[#C13120] hover:bg-[#A02718] text-white border-[#C13120] hover:border-[#A02718] shadow-md hover:shadow-lg',
    'outline' => 'bg-white hover:bg-gray-50 text-[#1B365D] border-[#1B365D] hover:border-[#D4A574] hover:text-[#D4A574]',
];

// Size classes
$sizeClasses = [
    'sm' => 'text-sm px-3 py-1.5 gap-1',
    'md' => 'text-base px-5 py-2.5 gap-2',
    'lg' => 'text-lg px-6 py-3 gap-2',
];

$iconSizes = [
    'sm' => 'text-base',
    'md' => 'text-xl',
    'lg' => 'text-2xl',
];

// Build classes
$buttonClasses = $variantClasses[$variant] ?? $variantClasses['primary'];
$sizeClass = $sizeClasses[$size] ?? $sizeClasses['md'];
$iconSize = $iconSizes[$size] ?? $iconSizes['md'];
$widthClass = $fullWidth ? 'w-full' : '';

// Disabled state
$disabledClass = $disabled ? 'opacity-50 cursor-not-allowed pointer-events-none' : '';

// Determine URL
$url = null;
if ($route) {
    $url = route($route);
} elseif ($href) {
    $url = $href;
}

// Base classes
$baseClasses = "inline-flex items-center justify-center font-semibold rounded-lg border-2 transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-[#D4A574]/30 $buttonClasses $sizeClass $widthClass $disabledClass";
@endphp

@if ($url && !$disabled)
    {{-- Link Button --}}
    <a
        href="{{ $url }}"
        target="{{ $target }}"
        {{ $attributes->merge(['class' => $baseClasses]) }}
        @if ($target === '_blank') rel="noopener noreferrer" @endif
        aria-label="{{ $label }}"
    >
        @if ($icon)
            <span class="material-symbols-outlined {{ $iconSize }}" aria-hidden="true">{{ $icon }}</span>
        @endif
        <span>{{ $label }}</span>
        {{ $slot }}
    </a>
@else
    {{-- Regular Button --}}
    <button
        type="{{ $type }}"
        {{ $attributes->merge(['class' => $baseClasses]) }}
        @if ($disabled) disabled aria-disabled="true" @endif
        aria-label="{{ $label }}"
    >
        @if ($icon)
            <span class="material-symbols-outlined {{ $iconSize }}" aria-hidden="true">{{ $icon }}</span>
        @endif
        <span>{{ $label }}</span>
        {{ $slot }}
    </button>
@endif
