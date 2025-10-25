{{--
    Component: EGI Type Badge
    Display badge for EGI type with FlorenceEGI brand styling

    @package Components
    @author Padmin D. Curtis (AI Partner OS3.0)
    @version 1.0.0 (FlorenceEGI - Dual Architecture)
    @date 2025-10-19

    Props:
    - type: EgiType enum value (ASA|SmartContract|PreMint)
    - size: sm|md|lg (default: md)
--}}

@props(['type', 'size' => 'md'])

@php
    use App\Enums\EgiType;

    $egiType = is_string($type) ? EgiType::from($type) : $type;

    // Brand-compliant styling per tipo
    $styles = [
        EgiType::ASA->value => [
            'bg' => 'bg-gradient-to-r from-blue-900 to-blue-800', // Blu Algoritmo
            'text' => 'text-white',
            'icon' => 'fa-shield-check',
            'label' => 'EGI Classico',
            'border' => 'border-blue-700',
        ],
        EgiType::SMART_CONTRACT->value => [
            'bg' => 'bg-gradient-to-r from-purple-700 to-purple-600', // Viola Innovazione
            'text' => 'text-white',
            'icon' => 'fa-brain',
            'label' => 'EGI Vivente',
            'border' => 'border-purple-500',
            'glow' => 'shadow-purple-500/50',
        ],
        EgiType::PRE_MINT->value => [
            'bg' => 'bg-gradient-to-r from-amber-600 to-amber-500', // Oro Fiorentino
            'text' => 'text-white',
            'icon' => 'fa-seedling',
            'label' => 'Pre-Mint',
            'border' => 'border-amber-400',
        ],
    ];

    $sizeClasses = [
        'sm' => 'text-[9px] sm:text-[10px] md:text-xs px-1 sm:px-1.5 md:px-2 py-0.5 sm:py-0.5 md:py-1',
        'md' => 'text-[10px] sm:text-xs md:text-xs lg:text-sm px-1.5 sm:px-2 md:px-2.5 py-0.5 sm:py-1 md:py-1',
        'lg' => 'text-xs sm:text-sm md:text-sm lg:text-base px-2 sm:px-3 md:px-3 py-1 sm:py-1.5 md:py-1.5',
    ];

    $style = $styles[$egiType->value];
    $sizeClass = $sizeClasses[$size];
@endphp

<span
    class="{{ $style['bg'] }} {{ $style['text'] }} {{ $sizeClass }} {{ $style['border'] }} {{ $style['glow'] ?? '' }} inline-flex items-center gap-0.5 rounded-sm border font-medium transition-all duration-200 hover:scale-105 sm:gap-1 sm:rounded-md md:gap-1 lg:rounded-md"
    role="status" aria-label="Tipo EGI: {{ $style['label'] }}">
    <i class="fas {{ $style['icon'] }} text-[9px] sm:text-[10px] md:text-xs lg:text-sm"></i>
    <span class="font-semibold">{{ $style['label'] }}</span>
</span>
